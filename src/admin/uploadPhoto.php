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
$successful = [];
$failed = [];

// Process each uploaded file
foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {
    $originalName = $_FILES['photos']['name'][$index];
    
    if ($_FILES['photos']['error'][$index] !== UPLOAD_ERR_OK) {
        $failed[] = [
            'index' => $index,
            'filename' => $originalName,
            'reason' => 'Upload error occurred'
        ];
        continue;
    }

    // Validate file type
    $fileType = $_FILES['photos']['type'][$index];
    if (!in_array($fileType, $allowedTypes)) {
        $failed[] = [
            'index' => $index,
            'filename' => $originalName,
            'reason' => 'Invalid file type (only JPG, PNG, GIF allowed)'
        ];
        continue;
    }

    // Validate file size
    $fileSize = $_FILES['photos']['size'][$index];
    if ($fileSize > $maxFileSize) {
        $fileSizeMB = round($fileSize / (1024 * 1024), 1);
        $failed[] = [
            'index' => $index,
            'filename' => $originalName,
            'reason' => "File too large ({$fileSizeMB}MB, max 5MB)"
        ];
        continue;
    }

    // Generate unique filename
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
            $photoID = $conn->insert_id;
            $uploadedCount++;
            $successful[] = [
                'index' => $index,
                'filename' => $originalName,
                'photoID' => $photoID
            ];
        } else {
            $failed[] = [
                'index' => $index,
                'filename' => $originalName,
                'reason' => 'Database error'
            ];
            // Delete the uploaded file if database insert fails
            unlink($filePath);
        }
        $stmt->close();
    } else {
        $failed[] = [
            'index' => $index,
            'filename' => $originalName,
            'reason' => 'Failed to save file to server'
        ];
    }
}

$totalFiles = count($_FILES['photos']['name']);

if ($uploadedCount > 0) {
    $message = "Successfully uploaded $uploadedCount of $totalFiles photo(s)";
    echo json_encode([
        'success' => true,
        'uploaded' => $uploadedCount,
        'total' => $totalFiles,
        'message' => $message,
        'successful' => $successful,
        'failed' => $failed
    ]);
} else {
    echo json_encode([
        'success' => false,
        'uploaded' => 0,
        'total' => $totalFiles,
        'message' => 'No photos were uploaded',
        'successful' => [],
        'failed' => $failed
    ]);
}
?>

