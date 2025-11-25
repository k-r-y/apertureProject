<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/rate_limit.php';

// Enforce Rate Limit (30 requests per minute for public gallery)
enforceRateLimit(30, 60);

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'verify_pin':
            $data = json_decode(file_get_contents('php://input'), true);
            $token = $data['token'];
            $pin = $data['pin'];

            $stmt = $conn->prepare("SELECT bookingID, gallery_pin, event_type, event_date FROM bookings WHERE gallery_token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            $booking = $result->fetch_assoc();

            if (!$booking) {
                throw new Exception("Invalid gallery link");
            }

            if ($booking['gallery_pin'] !== $pin) {
                throw new Exception("Incorrect PIN");
            }

            // Start session for this gallery
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['gallery_access_' . $token] = true;

            echo json_encode([
                'success' => true, 
                'event_type' => $booking['event_type'],
                'event_date' => $booking['event_date']
            ]);
            break;

        case 'get_photos':
            $token = $_GET['token'];
            
            if (session_status() === PHP_SESSION_NONE) session_start();
            if (!isset($_SESSION['gallery_access_' . $token])) {
                throw new Exception("Unauthorized access");
            }

            $stmt = $conn->prepare("SELECT bookingID FROM bookings WHERE gallery_token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $booking = $stmt->get_result()->fetch_assoc();

            if (!$booking) {
                throw new Exception("Invalid gallery");
            }

            // Fetch photos (assuming photos are stored in a table or directory related to bookingID)
            // For now, let's fetch from user_photos table if it exists, or mock it based on file structure
            // Actually, the current system uses `uploads/user_photos/{userId}`. 
            // We need to link photos to bookings more explicitly or just show user's photos.
            // Let's assume we show all photos for the user associated with this booking.
            
            $stmt = $conn->prepare("SELECT u.userID FROM bookings b JOIN users u ON b.userID = u.userID WHERE b.bookingID = ?");
            $stmt->bind_param("i", $booking['bookingID']);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            
            $userId = $user['userID'];
            $uploadDir = "../../uploads/user_photos/" . $userId . "/";
            
            $photos = [];
            if (is_dir($uploadDir)) {
                $files = scandir($uploadDir);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $photos[] = [
                            'src' => "../uploads/user_photos/$userId/$file", // Path relative to gallery.php (which is in src/)
                            'w' => 1200,
                            'h' => 800
                        ];
                    }
                }
            }
            
            echo json_encode(['success' => true, 'photos' => $photos]);
            break;

        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
