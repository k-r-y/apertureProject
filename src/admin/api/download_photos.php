<?php
// download_photos.php - Admin tool to download all photos for a booking/user as ZIP

require_once '../../includes/functions/config.php';
require_once '../../includes/functions/auth.php';

// Check admin access
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    die('Unauthorized access');
}

$bookingId = $_GET['booking_id'] ?? 0;

if (!$bookingId) {
    die('Booking ID required');
}

// Get user ID from booking
$stmt = $conn->prepare("SELECT userID, event_type, event_date FROM bookings WHERE bookingID = ?");
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    die('Booking not found');
}

$userId = $booking['userID'];
$eventType = preg_replace('/[^a-zA-Z0-9]/', '_', $booking['event_type']);
$eventDate = $booking['event_date'];

// Fetch all photos for this user
$stmt = $conn->prepare("SELECT fileName, originalName FROM user_photos WHERE userID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$photos = $stmt->get_result();

if ($photos->num_rows === 0) {
    die('No photos found for this user');
}

// Create ZIP
$zip = new ZipArchive();
$zipName = "Aperture_{$eventType}_{$eventDate}_Photos.zip";
$tempZipPath = sys_get_temp_dir() . '/' . $zipName;

if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die('Failed to create ZIP archive');
}

$baseUploadPath = '../../uploads/user_photos/' . $userId . '/';
$addedCount = 0;

while ($photo = $photos->fetch_assoc()) {
    $filePath = $baseUploadPath . $photo['fileName'];
    if (file_exists($filePath)) {
        // Add file to zip with its original name (or unique name if duplicates)
        $zip->addFile($filePath, $photo['fileName']);
        $addedCount++;
    }
}

$zip->close();

if ($addedCount === 0) {
    die('No physical photo files found to zip');
}

// Serve file
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipName . '"');
header('Content-Length: ' . filesize($tempZipPath));
header('Pragma: no-cache');
header('Expires: 0');

readfile($tempZipPath);

// Cleanup
unlink($tempZipPath);
exit;
?>
