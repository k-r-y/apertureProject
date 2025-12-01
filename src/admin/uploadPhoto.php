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

if (!isset($_POST['bookingID']) || empty($_POST['bookingID'])) {
    echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
    exit;
}

$bookingID = intval($_POST['bookingID']);
$gdriveLink = isset($_POST['gdriveLink']) ? trim($_POST['gdriveLink']) : null;
$uploadedBy = $_SESSION['userId'];
$captions = isset($_POST['captions']) ? $_POST['captions'] : [];

// Fetch userID from bookingID
$stmt = $conn->prepare("SELECT userID FROM bookings WHERE bookingID = ?");
$stmt->bind_param("i", $bookingID);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
    exit;
}

$userID = $booking['userID'];

// Update GDrive link if provided
if ($gdriveLink !== null) {
    $updateStmt = $conn->prepare("UPDATE bookings SET gdrive_link = ? WHERE bookingID = ?");
    $updateStmt->bind_param("si", $gdriveLink, $bookingID);
    $updateStmt->execute();
}

// Check if photos are uploaded
if (!isset($_FILES['photos']) || empty($_FILES['photos']['name'][0])) {
    // If only updating link
    echo json_encode(['success' => true, 'message' => 'Booking updated successfully', 'uploaded' => 0, 'failed' => []]);
    exit;
}

// Create user directory if it doesn't exist
$uploadDir = '../../uploads/user_photos/' . $userID . '/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
$maxFileSize = 5 * 1024 * 1024; // 5MB
$uploadedCount = 0;
$successful = [];
$failed = [];

// Get Photo Type
$photoType = isset($_POST['photoType']) && in_array($_POST['photoType'], ['edited', 'raw']) ? $_POST['photoType'] : 'edited';
error_log("Upload Debug: photoType received: " . (isset($_POST['photoType']) ? $_POST['photoType'] : 'NOT SET') . ", using: " . $photoType);

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
        $stmt = $conn->prepare("INSERT INTO user_photos (userID, bookingID, fileName, originalName, uploadedBy, uploadDate, caption, photo_type) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
        $stmt->bind_param("iisssis", $userID, $bookingID, $fileName, $originalName, $uploadedBy, $caption, $photoType);

        if ($stmt->execute()) {
            // Auto-check if booking can be marked as completed
            require_once '../includes/functions/booking_status_automation.php';
            checkAndUpdateToCompleted($bookingID);

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
    // Send Notifications
    require_once '../includes/functions/notifications.php';
    
    // Fetch user details
    $userStmt = $conn->prepare("SELECT Email, FirstName, LastName FROM users WHERE userID = ?");
    $userStmt->bind_param("i", $userID);
    $userStmt->execute();
    $userRes = $userStmt->get_result();
    $userData = $userRes->fetch_assoc();
    
    if ($userData) {
        $notifier = new NotificationSystem($conn);
        $fullName = $userData['FirstName'] . ' ' . $userData['LastName'];
        
        // Send Notification (Email + In-App handled by class)
        $notifier->sendPhotoUploadNotification(
            $userData['Email'], 
            $fullName, 
            $userID, 
            $bookingID, 
            $uploadedCount, 
            $gdriveLink
        );
    }

    $message = "Successfully uploaded $uploadedCount of $totalFiles photo(s)";
    if ($gdriveLink) $message .= " and updated GDrive link";
    
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

