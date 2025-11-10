<?php

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

function createToken($email)
{
    global $conn;

    $token = bin2hex(random_bytes(32));

    $query = $conn->prepare("UPDATE users SET verificationToken = ?, tokenExpires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE Email = ?");
    $query->bind_param('ss', $token, $email);
    $query->execute();

    $query->close();
    return $token;
}

function registerUser($email, $password)
{
    global $conn;

    // Inserting user data into the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = $conn->prepare("INSERT INTO users(Email, Password) values(?,?)");
    $query->bind_param('ss', $email, $hashedPassword);

    if ($query->execute()) {
        // Creating and sending token for email verification
        $token = createToken($email);
        $emailSent = sendVerificationEmail($email, $token);

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
function verifyEmail($token)
{
    global $conn;

    $query = $conn->prepare("SELECT userID FROM users WHERE verificationToken = ? AND tokenExpires_at > NOW()");
    $query->bind_param('s', $token);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        $stmt = $conn->prepare("UPDATE users SET isVerified = true, tokenExpires_at = NULL, verificationToken = NULL WHERE userID = ?");
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
function logInUser($email, $password){
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

function isVerified($email){
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
function isProfileCompleted($userId){
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

function logout() {
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


function storePasswordResetCode($email, $code){
    
}