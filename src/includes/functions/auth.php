<?php

// check if the email exist
function isEmailExists($email)
{
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM users WHERE Email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        return [
            'success' => false,
            'error' => 'Email already exist'
        ];
    } else {
        $stmt->close();
        return ['success' => true];
    }
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

    $code = createCode($email);
    $emailSent = sendVerificationEmailWithCode($email, $code);

    if ($emailSent) {
        return true;
    }

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
        // Creating and sending token for email verification
        $code = createCode($email);
        $emailSent = sendVerificationEmailWithCode($email, $code);

        $query->close();
        if ($emailSent) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'error' => "Something went wrong"
        ];
    } else {
        $query->close();
        return [
            'success' => false,
            'error' => 'Registration failed'
        ];
    }
}

// function to verify email
function verifyEmail($code, $email)
{
    global $conn;

    $query = $conn->prepare("SELECT userID FROM users WHERE EMAIL = ? AND verificationCode = ? AND codeExpires_at > NOW()");
    $query->bind_param('ss', $email, $code);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        $stmt = $conn->prepare("UPDATE users SET isVerified = true, codeExpires_at = NULL, verificationCode = NULL WHERE userID = ?");
        $stmt->bind_param('s', $user['userID']);
        $stmt->execute();

        $stmt->close();
        $query->close();
        return [
            'success' => true,
            'userId' => $user['userID']
        ];
    }
    $query->close();

    return ['success' => false];
}

// function to logIn users
function logInUser($email, $password)
{
    global $conn;

    $query = $conn->prepare("SELECT userID, Email, Password, Role from users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['Password'])) {
            $query->close();
            return [
                'success' => true,
                'userId' => $user['userID'],
                'role' => $user['Role']

            ];
        }


        $query->close();
        return [
            'success' => false,
            'error' => "Invalid email or password"
        ];
    } else {
        $query->close();
        return [
            'success' => false,
            'error' => "Invalid email or password"
        ];
    }
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



// checks if the user needs to complete their profile info
function isProfileCompleted($userId)
{
    global $conn;

    $query = $conn->prepare("SELECT * FROM users WHERE userID = ? AND profileCompleted = true");
    $query->bind_param('s', $userId);
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
    $query->bind_param('s', $userId);
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


