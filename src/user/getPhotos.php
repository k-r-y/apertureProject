<?php
// getPhotos.php - API to fetch user's photos

require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/api_security.php';

// Apply rate limiting
enforceRateLimit('/api/getPhotos', 200, 3600);

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION["userId"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "User") {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userID = $_SESSION['userId'];

// Fetch photos for this user
$stmt = $conn->prepare("
    SELECT p.photoID, p.fileName, p.originalName, p.caption, p.uploadDate,
           u.fullName as uploadedByName
    FROM user_photos p
    LEFT JOIN users u ON p.uploadedBy = u.userID
    WHERE p.userID = ?
    ORDER BY p.uploadDate DESC
");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$photos = [];
while ($row = $result->fetch_assoc()) {
    $photos[] = [
        'photoID' => $row['photoID'],
        'fileName' => $row['fileName'],
        'originalName' => $row['originalName'],
        'caption' => $row['caption'],
        'uploadDate' => $row['uploadDate'],
        'uploadedBy' => $row['uploadedByName'],
        'url' => '../../uploads/user_photos/' . $userID . '/' . $row['fileName']
    ];
}

$stmt->close();

echo json_encode([
    'success' => true,
    'photos' => $photos,
    'count' => count($photos)
]);
?>
