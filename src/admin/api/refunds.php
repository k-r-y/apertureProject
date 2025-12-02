<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/rate_limit.php';

// Enforce Rate Limit
enforceRateLimit(60, 60);

// Ensure user is logged in and is an Admin
if (!isset($_SESSION['userId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_all':
            $status = $_GET['status'] ?? 'all';
            
            $sql = "
                SELECT r.*, b.bookingID as bookingRef, b.event_type, b.event_date, 
                       u.FirstName, u.LastName, u.Email
                FROM refunds r
                JOIN bookings b ON r.bookingID = b.bookingID
                JOIN users u ON b.userID = u.userID
            ";
            
            if ($status !== 'all') {
                $sql .= " WHERE r.status = ?";
                $stmt = $conn->prepare($sql . " ORDER BY r.requested_at DESC");
                $stmt->bind_param("s", $status);
            } else {
                $stmt = $conn->prepare($sql . " ORDER BY r.requested_at DESC");
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $refunds = [];
            
            while ($row = $result->fetch_assoc()) {
                $refunds[] = $row;
            }
            
            echo json_encode(['success' => true, 'refunds' => $refunds]);
            break;

        case 'update_status':
            // Handle FormData (POST)
            $refundId = intval($_POST['refundId'] ?? 0);
            $status = $_POST['status'] ?? '';
            $notes = $_POST['notes'] ?? null;
            
            if (!$refundId || !$status) {
                throw new Exception("Missing required fields");
            }

            // Validate status
            $validStatuses = ['pending', 'approved', 'processed', 'rejected'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception("Invalid status");
            }
            
            $processedAt = ($status === 'processed') ? date('Y-m-d H:i:s') : null;
            $proofPath = null;

            // Handle File Upload
            if (isset($_FILES['refundProof']) && $_FILES['refundProof']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../../uploads/refund_proofs/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileExt = strtolower(pathinfo($_FILES['refundProof']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
                
                if (!in_array($fileExt, $allowed)) {
                    throw new Exception("Invalid file type. Only JPG, PNG, and PDF allowed.");
                }

                if ($_FILES['refundProof']['size'] > 5 * 1024 * 1024) { // 5MB
                    throw new Exception("File too large. Max 5MB.");
                }

                $fileName = 'refund_' . $refundId . '_' . time() . '.' . $fileExt;
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['refundProof']['tmp_name'], $targetPath)) {
                    // Store relative path for DB
                    $proofPath = '../uploads/refund_proofs/' . $fileName;
                } else {
                    throw new Exception("Failed to upload proof file.");
                }
            }
            
            // Update Query
            $sql = "UPDATE refunds SET status = ?, notes = ?, processed_at = ?, processed_by = ?";
            $params = [$status, $notes, $processedAt, $_SESSION['userId']];
            $types = "sssi";

            if ($proofPath) {
                $sql .= ", proof_image = ?";
                $params[] = $proofPath;
                $types .= "s";
            }

            $sql .= " WHERE refundID = ?";
            $params[] = $refundId;
            $types .= "i";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            // Fetch booking details for notification
            $detailsStmt = $conn->prepare("
                SELECT b.userID, b.bookingID as bookingRef, r.amount 
                FROM refunds r 
                JOIN bookings b ON r.bookingID = b.bookingID 
                WHERE r.refundID = ?
            ");
            $detailsStmt->bind_param("i", $refundId);
            $detailsStmt->execute();
            $details = $detailsStmt->get_result()->fetch_assoc();
            
            if ($stmt->execute()) {
                // Send Notification
                if ($details) {
                    require_once '../../includes/functions/booking_workflow.php';
                    $notifTitle = "Refund " . ucfirst($status);
                    $notifMessage = "Your refund request for booking #{$details['bookingRef']} has been {$status}.";
                    if ($notes) {
                        $notifMessage .= " Note: $notes";
                    }
                    createNotification($details['userID'], 'refund_update', $notifTitle, $notifMessage, 'appointments.php');

                    // Auto-update booking status based on refund status
                    if ($status === 'approved' || $status === 'processed') {
                        // Approve or complete refund - mark booking as cancelled
                        $cancelStmt = $conn->prepare("UPDATE bookings SET booking_status = 'cancelled' WHERE bookingID = ?");
                        $cancelStmt->bind_param("i", $details['bookingRef']);
                        $cancelStmt->execute();
                    } elseif ($status === 'rejected') {
                        // Reject refund - restore booking to confirmed status and reset refund_amount
                        // User wanted to cancel but admin rejected it, so booking continues normally
                        $rejectStmt = $conn->prepare("UPDATE bookings SET booking_status = 'confirmed', refund_amount = 0 WHERE bookingID = ?");
                        $rejectStmt->bind_param("i", $details['bookingRef']);
                        $rejectStmt->execute();
                    }
                }
                
                echo json_encode(['success' => true, 'message' => 'Refund status updated successfully']);
            } else {
                throw new Exception("Failed to update refund status");
            }
            break;

        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
