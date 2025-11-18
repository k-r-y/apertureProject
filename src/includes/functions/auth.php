<?php

// check if the email exist
function isEmailExists($email)
{
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM users WHERE Email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $exists;
}

// generate verification code
function createCode($email)
{
    global $conn;

    $code = random_int(100000, 999999);
    $now = date('Y-m-d H:i:s');

    $query = $conn->prepare("UPDATE users SET verificationCode = ?, codeCreated_at = ?, codeExpires_at = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE Email = ?");
    $query->bind_param('sss', $code, $now, $email);
    $query->execute();

    $query->close();
    return $code;
}

function resendCode($email)
{
    global $conn;
    $wait = 60; // Wait 60 seconds before allowing another resend

    $query = $conn->prepare("SELECT * from users WHERE Email = ?");
    $query->bind_param('s', $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $lastSent = strtotime($user['codeCreated_at']);
        $now = time();
        $difference =  $now - $lastSent;

        if ($difference >= $wait) {
            $code = createCode($email);
            $emailSent = sendVerificationEmailWithCode($email, $code);

            if ($emailSent) {
                $query->close();
                return true;
            }
        }
    }
    $query->close();
    return false;
}


// register user
function registerUser($email, $password)
{
    global $conn;

    // Inserting user data into the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = $conn->prepare("INSERT INTO users(Email, Password) values(?,?)");
    $query->bind_param('ss', $email, $hashedPassword);


    if ($query->execute()) {
        $userId = $conn->insert_id;

        // Creating and sending token for email verification
        $code = createCode($email);
        $emailSent = sendVerificationEmailWithCode($email, $code);

        // Create a corresponding entry in the ratelimiting table
        $rateLimitQuery = $conn->prepare("INSERT INTO ratelimiting (userID) VALUES (?)");
        $rateLimitQuery->bind_param('i', $userId);
        $rateLimitQuery->execute();
        $rateLimitQuery->close();

        $query->close();
        if ($emailSent) {
            return ['success' => true, 'userId' => $userId];
        }

        return [
            'success' => false,
            'error' => "Something went wrong"
        ];
    } else {
        error_log("Registration failed: " . $query->error);
        $query->close();
        return [
            'success' => false,
            'error' => 'Registration failed'
        ];
    }
}

// function to verify email
function verifyEmail($email, $code, $type)
{
    global $conn;

    // 1. Check if account is locked
    $lockStatus = checkAndHandleLock(getUserId($email), $type);
    if ($lockStatus) {
        return $lockStatus;
    }

    // 2. Verify the code
    $query = $conn->prepare("SELECT userID FROM users WHERE Email = ? AND verificationCode = ? AND codeExpires_at > NOW()");
    $query->bind_param('ss', $email, $code);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        $stmt = $conn->prepare("UPDATE users SET isVerified = true, codeCreated_at = NULL, codeExpires_at = NULL, verificationCode = NULL WHERE userID = ?");
        $stmt->bind_param('s', $user['userID']);
        $stmt->execute();

        $stmt->close();
        verificationUnlocked($user['userID'], $type); // Reset attempts and unlock on success
        $query->close();
        return [
            'success' => true,
            'userId' => $user['userID']
        ];
    }

    // 3. If verification fails, increment attempt counter
    addFailedAttempt(getUserId($email), $type);
    $query->close();

    return ['success' => false, 'error' => "The verification code is invalid or has expired.. Please try again"];
}

// function to logIn users
function logInUser($email, $password, $type)
{
    global $conn;

    // 1. Find user by email
    $query = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows !== 1) {
        // SECURITY: To prevent timing attacks, we perform a dummy password verification.
        // This makes the execution time for a non-existent user similar to that of an existing user with a wrong password.
        $dummyHash = '$2y$10$N9qo8uLOickGtv.k9OPFXuRRiG7LBiXpSqnJ8Sks5wOkoOdUT2HHe'; // "password"
        password_verify($password, $dummyHash); // This operation takes time.
        return ['success' => false, 'error' => "Invalid email or password"];
    }

    $user = $result->fetch_assoc();
    $query->close();
    $userId = $user['userID'];

    // 2. CHECK IF ACCOUNT IS LOCKED
    $lockStatus = checkAndHandleLock($userId, $type);
    if ($lockStatus) {
        return $lockStatus;
    }

    // 3. IT'S NOT LOCKED → VERIFY PASSWORD
    if (password_verify($password, $user['Password'])) {
        verificationUnlocked($userId, $type); // Reset attempts and unlock on success

        return [
            'success' => true,
            'userId' => $user['userID'],
            'role' => $user['Role']
        ];
    }

    // 4. PASSWORD WRONG → ADD ATTEMPT
    addLoginAttempt($userId);

    return [
        'success' => false,
        'error' => "Invalid email or password"
    ];
}

// checks if the user is verified
function isVerified($email)
{
    global $conn;

    $query = $conn->prepare("SELECT * from users WHERE Email = ? and isVerified = true");
    $query->bind_param('s', $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $query->close();
        return true;
    }

    $query->close();
    return false;
}

/**
 * Gets the creation timestamp for a verification code.
 * @param string $email The user's email.
 * @param string $type The type of verification ('registration', 'login_email_verification', 'forgot_password').
 * @return string|null The timestamp or null if not found.
 */
function getCodeCreationTime($email, $type)
{
    global $conn;
    if (!$email) return null;

    $columnMap = [
        'registration' => 'codeCreated_at',
        'login_email_verification' => 'codeCreated_at',
        'forgot_password' => 'resetCodeCreated_at'
    ];

    if (!isset($columnMap[$type])) return null;

    $timestampColumn = $columnMap[$type];

    $query = $conn->prepare("SELECT $timestampColumn FROM users WHERE Email = ?");
    $query->bind_param('s', $email);
    $query->execute();
    $result = $query->get_result()->fetch_assoc();
    $query->close();

    return $result[$timestampColumn] ?? null;
}



// checks if the user needs to complete their profile info
function isProfileCompleted($userId)
{
    global $conn;

    $query = $conn->prepare("SELECT * FROM users WHERE userID = ? AND profileCompleted = true");
    $query->bind_param('i', $userId);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $query->close();
        return true;
    }

    $query->close();
    return false;
}

// logout function
function logout()
{
    session_start();
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

function setSession($userId)
{
    global $conn;

    $query = $conn->prepare("SELECT * from users WHERE userID = ?");
    $query->bind_param('i', $userId);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        $_SESSION['userId'] = $user['userID'];
        $_SESSION['firstName'] = $user['FirstName'];
        $_SESSION['lastName'] = $user['LastName'];
        $_SESSION['fullName'] = $user['FullName'];
        $_SESSION['email'] = $user['Email'];
        $_SESSION['role'] = $user['Role'];
        $_SESSION['contact'] = $user['contactNo'];
        $_SESSION['isVerified'] = $user['isVerified'];
    }
    $query->close();
}

function createForgotCode($email)
{
    global $conn;

    $code = random_int(100000, 999999);
    $now = date('Y-m-d H:i:s');

    $query = $conn->prepare("UPDATE users SET passwordResetCode = ?, resetCodeCreated_at = ?, resetCodeExpires_at = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE Email = ?");
    $query->bind_param('sss', $code, $now, $email);
    $query->execute();

    $query->close();
    return $code;
}

function verifyCode($email, $code, $type)
{
    global $conn;

    $userId = getUserId($email);
    $lockStatus = checkAndHandleLock($userId, $type);
    if ($lockStatus) {
        return $lockStatus;
    }

    // If user doesn't exist, we still need to handle attempts to prevent enumeration
    if (!$userId) {
        // SECURITY: To prevent timing attacks, we perform a dummy password verification.
        // This makes the execution time for a non-existent user similar to that of an existing user.
        $dummyHash = '$2y$10$N9qo8uLOickGtv.k9OPFXuRRiG7LBiXpSqnJ8Sks5wOkoOdUT2HHe'; // "password"
        password_verify($code, $dummyHash); // This operation takes time.
        return ['success' => false, 'error' => "The verification code is invalid or has expired. Please try again."];
    }

    $query = $conn->prepare("SELECT userID FROM users WHERE Email = ? AND passwordResetCode = ? AND resetCodeExpires_at > NOW()");
    $query->bind_param('ss', $email, $code);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $query->close();
        verificationUnlocked($user['userID'], $type); // Reset on success
        return [
            'success' => true,
            'userId' => $user['userID']
        ];
    }

    // 3. If verification fails, increment attempt counter
    addFailedAttempt($userId, $type);
    $query->close();

    return [
        'success' => false,
        'error' => "The verification code is invalid or has expired. Please try again."
    ];
}

function updatePassword($newPassword, $email)
{


    global $conn;

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $query = $conn->prepare("SELECT * FROM users WHERE Email = ?");
    $query->bind_param('s', $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {

        $query2 = $conn->prepare("UPDATE users SET password = ? WHERE Email = ?");
        $query2->bind_param('ss', $hashedPassword, $email);

        if ($query2->execute()) {
            // On successful password update, reset all lock/attempt counters for this user.
            verificationUnlocked(getUserId($email), 'all');
            $query->close();
            $query2->close();
            return true;
        }

        $query->close();
        $query2->close();
        return false;
    }
    $query->close();
    return false;
}


function addFailedAttempt($userId, $type)
{
    global $conn;
    if (!$userId) return;

    $columnMap = [
        'login' => 'logInAttempt',
        'registration' => 'registrationEmailVerificationAttempt',
        'forgot_password' => 'fogotEmailVerificationAttempt', // Corrected to match DB schema typo
        'login_email_verification' => 'loginEmailVerificationAttempt'
    ];

    if (!isset($columnMap[$type])) return;

    $attemptColumn = $columnMap[$type];

    $query = $conn->prepare("UPDATE ratelimiting SET $attemptColumn = $attemptColumn + 1 WHERE userID = ?");
    $query->bind_param('i', $userId);
    if ($query->execute()) {
        $query->close();

        // Check if lock is needed
        $checkQuery = $conn->prepare("SELECT $attemptColumn FROM ratelimiting WHERE userID = ?");
        $checkQuery->bind_param("i", $userId);
        $checkQuery->execute();
        $res = $checkQuery->get_result()->fetch_assoc();
        $checkQuery->close();

        $maxAttempts = 5;
        if ($res && $res[$attemptColumn] >= $maxAttempts) {
            verificationLocked($userId, $type);
        }
    } else {
        // Log error if the UPDATE query fails
        error_log("Failed to increment attempt for userID: $userId, type: $type. Error: " . $query->error);
        $query->close();
    }
}

function addLoginAttempt($userId)
{
    addFailedAttempt($userId, 'login');
}

function addForgotEmailVerificationAttempt($userId)
{
    addFailedAttempt($userId, 'forgot_password');
}

function addLogInEmailVerificationAttempt($userId)
{
    addFailedAttempt($userId, 'login_email_verification');
}

function addRegistrationEmailVerificationAttempt($userId)
{
    addFailedAttempt($userId, 'registration');
}




function verificationLocked($userId, $type)
{
    global $conn;
    if (!$userId) return;

    $columnMap = [
        'login' => 'loginLocked',
        'registration' => 'registrationEmailVerificationLocked',
        'forgot_password' => 'fogotEmailVerificationLocked', // Corrected to match DB schema typo
        'login_email_verification' => 'loginEmailVerificationLocked'
    ];

    if (!isset($columnMap[$type])) return;

    $lockColumn = $columnMap[$type];

    $query = $conn->prepare("UPDATE ratelimiting SET $lockColumn = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE userID = ?");
    $query->bind_param('i', $userId);
    $query->execute();
    $query->close();
}

function verificationUnlocked($userId, $type)
{
    global $conn;
    if (!$userId) return;

    $columnMap = [
        'login' => ['logInAttempt = 0', 'loginLocked = NULL'],
        'registration' => ['registrationEmailVerificationAttempt = 0', 'registrationEmailVerificationLocked = NULL'],
        'forgot_password' => ['fogotEmailVerificationAttempt = 0', 'fogotEmailVerificationLocked = NULL'], // Corrected to match DB schema typo
        'login_email_verification' => ['loginEmailVerificationAttempt = 0', 'loginEmailVerificationLocked = NULL']
    ];

    if ($type === 'all') {
        $updateString = "logInAttempt = 0, loginLocked = NULL, registrationEmailVerificationAttempt = 0, registrationEmailVerificationLocked = NULL, fogotEmailVerificationAttempt = 0, fogotEmailVerificationLocked = NULL, loginEmailVerificationAttempt = 0, loginEmailVerificationLocked = NULL";
    } else {
        if (!isset($columnMap[$type])) return;
        $updateString = implode(', ', $columnMap[$type]);
    }


    $query = $conn->prepare("UPDATE ratelimiting SET $updateString WHERE userID = ?");
    $query->bind_param('i', $userId);
    $query->execute();
    $query->close();
}

function getUserId($email)
{
    global $conn;

    $query = $conn->prepare("SELECT userID from users WHERE Email = ?");
    $query->bind_param('s', $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $query->close();
        return $user['userID'];
    }
    $query->close();
    return false;
}


/**
 * Reusable function to check if an account is locked.
 * Returns an error array if locked, or false if not locked.
 * Automatically unlocks the account if the timer has expired.
 */
function checkAndHandleLock($userId, $type)
{
    global $conn;
    if (!$userId) return false; // Cannot check lock for non-existent user

    $columnMap = [
        'login' => 'loginLocked',
        'registration' => 'registrationEmailVerificationLocked',
        'forgot_password' => 'fogotEmailVerificationLocked', // Corrected to match DB schema typo
        'login_email_verification' => 'loginEmailVerificationLocked'
    ];

    if (!isset($columnMap[$type])) return false;

    $lockColumn = $columnMap[$type];

    $lockQuery = $conn->prepare("SELECT $lockColumn FROM ratelimiting WHERE userID = ?");
    $lockQuery->bind_param('i', $userId);
    $lockQuery->execute();
    $lockResult = $lockQuery->get_result()->fetch_assoc();
    $lockQuery->close();

    if ($lockResult && $lockResult[$lockColumn] !== null) {
        $now = time();
        $lockedUntil = strtotime($lockResult[$lockColumn]);

        if ($lockedUntil > $now) {
            $remaining = $lockedUntil - $now;
            return [
                'success' => false,
                'error' => "Too many failed attempts. Please try again in {$remaining} seconds."
            ];
        } else {
            // Lock has expired, so unlock it.
            verificationUnlocked($userId, $type);
        }
    }

    return false; // Not locked
}



function resendForgotCode($email)
{
    global $conn;

    $wait = 60; // Wait 60 seconds before allowing another resend

    $query = $conn->prepare("SELECT * FROM users WHERE Email = ?");
    $query->bind_param('s', $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        $lastSent = strtotime($user['resetCodeCreated_at']);
        $now = time();
        $difference =  $now - $lastSent;

        if ($difference >= $wait) {
            $code = createForgotCode($email);
            $emailSent = sendForgotPasswordWithCode($email, $code);

            if ($emailSent) {
                $query->close();
                return true;
            }
        }
    }
    $query->close();
    return false;
}
