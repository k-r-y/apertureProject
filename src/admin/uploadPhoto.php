<?php
// uploadPhoto.php - Backend handler for photo uploads

require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/auth.php';

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION["userId"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "Admin") {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_POST['userID']) || empty($_POST['userID'])) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

if (!isset($_FILES['photos']) || empty($_FILES['photos']['name'][0])) {
    echo json_encode(['success' => false, 'message' => 'No photos uploaded']);
    exit;
}

$userID = intval($_POST['userID']);
$uploadedBy = $_SESSION['userId'];
$captions = isset($_POST['captions']) ? $_POST['captions'] : [];

// Create user directory if it doesn't exist
$uploadDir = '../../uploads/user_photos/' . $userID . '/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
$maxFileSize = 5 * 1024 * 1024; // 5MB
$uploadedCount = 0;
$errors = [];

// Process each uploaded file
foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {
    if ($_FILES['photos']['error'][$index] !== UPLOAD_ERR_OK) {
        $errors[] = "Error uploading " . $_FILES['photos']['name'][$index];
        continue;
    }

    // Validate file type
    $fileType = $_FILES['photos']['type'][$index];
    if (!in_array($fileType, $allowedTypes)) {
        $errors[] = $_FILES['photos']['name'][$index] . " is not a valid image type";
        continue;
    }

    // Validate file size
    if ($_FILES['photos']['size'][$index] > $maxFileSize) {
        $errors[] = $_FILES['photos']['name'][$index] . " exceeds 5MB limit";
        continue;
    }

    // Generate unique filename
    $originalName = $_FILES['photos']['name'][$index];
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $fileName = uniqid('photo_' . time() . '_') . '.' . $extension;
    $filePath = $uploadDir . $fileName;

    // Move uploaded file
    if (move_uploaded_file($tmpName, $filePath)) {
        // Get caption for this photo
        $caption = isset($captions[$index]) ? trim($captions[$index]) : '';

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO user_photos (userID, fileName, originalName, uploadedBy, caption) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issis", $userID, $fileName, $originalName, $uploadedBy, $caption);

        if ($stmt->execute()) {
            $uploadedCount++;
        } else {
            $errors[] = "Database error for " . $originalName;
            // Delete the uploaded file if database insert fails
            unlink($filePath);
        }
        $stmt->close();
    } else {
        $errors[] = "Failed to save " . $originalName;
    }
}

if ($uploadedCount > 0) {
    $message = "Successfully uploaded $uploadedCount photo(s)";
    if (!empty($errors)) {
        $message .= ". Some files failed: " . implode(', ', $errors);
    }
    echo json_encode([
        'success' => true,
        'uploaded' => $uploadedCount,
        'message' => $message,
        'errors' => $errors
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No photos were uploaded. ' . implode(', ', $errors)
    ]);
}
?>
