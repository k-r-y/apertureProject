<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/rate_limit.php';

// Enforce Rate Limit
enforceRateLimit(20, 60);

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $bookingId = intval($_POST['bookingId'] ?? 0);
    $userId = $_SESSION['userId'];

    if (!$bookingId) {
        throw new Exception("Invalid booking ID");
    }

    // Verify booking ownership and status
    $stmt = $conn->prepare("SELECT bookingID, total_amount, downpayment_amount, is_fully_paid FROM bookings WHERE bookingID = ? AND userID = ?");
    $stmt->bind_param("ii", $bookingId, $userId);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();

    if (!$booking) {
        throw new Exception("Booking not found");
    }

    if ($booking['is_fully_paid']) {
        throw new Exception("Booking is already fully paid");
    }

    $type = $_POST['type'] ?? 'balance'; // 'downpayment' or 'balance'
    
    // Handle File Upload
    if (!isset($_FILES['paymentProof']) || $_FILES['paymentProof']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Please upload a valid proof of payment");
    }

    $uploadDir = '../../uploads/payment_proofs/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileExt = strtolower(pathinfo($_FILES['paymentProof']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    
    if (!in_array($fileExt, $allowed)) {
        throw new Exception("Invalid file type. Only JPG, PNG, and PDF allowed.");
    }

    if ($_FILES['paymentProof']['size'] > 5 * 1024 * 1024) { // 5MB
        throw new Exception("File too large. Max 5MB.");
    }

    $prefix = ($type === 'downpayment') ? 'downpayment_' : 'balance_';
    $fileName = $prefix . $bookingId . '_' . time() . '.' . $fileExt;
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['paymentProof']['tmp_name'], $targetPath)) {
        $proofPath = '../uploads/payment_proofs/' . $fileName;
        
        // Update Database
        $column = ($type === 'downpayment') ? 'proof_payment' : 'balance_payment_proof';
        $updateStmt = $conn->prepare("UPDATE bookings SET $column = ? WHERE bookingID = ?");
        $updateStmt->bind_param("si", $proofPath, $bookingId);
        
        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Payment proof uploaded successfully. Waiting for admin confirmation.']);
        } else {
            throw new Exception("Database error");
        }
    } else {
        throw new Exception("Failed to upload file");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
