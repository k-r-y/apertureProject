<?php
/**
 * Account Recovery Script
 * Allows archived users to recover their accounts
 */

require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Email is required';
    } else {
        // Check if account exists and is archived
        $stmt = $conn->prepare("SELECT userID, FirstName, LastName, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user) {
            $error = 'No account found with this email';
        } else if ($user['status'] !== 'archived') {
            $error = 'This account is not archived';
        } else {
            // Recover the account
            $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE userID = ?");
            $stmt->bind_param("i", $user['userID']);
            
            if ($stmt->execute()) {
                $message = 'Account recovered successfully! You can now log in.';
            } else {
                $error = 'Failed to recover account. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Account - Aperture</title>
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-dark text-light">
    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card bg-secondary p-4" style="max-width: 500px; width: 100%;">
            <h2 class="text-center mb-4">Recover Your Account</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <a href="logIn.php" class="btn btn-primary w-100">Go to Login</a>
            <?php elseif ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if (!$message): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                    <small class="text-muted">Enter the email address of your archived account</small>
                </div>
                <button type="submit" class="btn btn-warning w-100">Recover Account</button>
                <a href="logIn.php" class="btn btn-outline-light w-100 mt-2">Back to Login</a>
            </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
