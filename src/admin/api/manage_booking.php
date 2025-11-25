<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/auth.php';
require_once '../../includes/functions/booking_workflow.php';

header('Content-Type: application/json');

// Check admin access
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    // Fetch bookings with filters
    $status = $_GET['status'] ?? 'all';
    $search = $_GET['search'] ?? '';
    
    $query = "SELECT b.*, u.FirstName, u.LastName, p.packageName 
              FROM bookings b 
              JOIN users u ON b.userID = u.userID 
              JOIN packages p ON b.packageID = p.packageID 
              WHERE 1=1";
    
    $params = [];
    $types = "";

    if ($status !== 'all') {
        $query .= " AND b.booking_status = ?";
        $params[] = $status;
        $types .= "s";
    }

    if (!empty($search)) {
        $query .= " AND (u.FirstName LIKE ? OR u.LastName LIKE ? OR b.bookingID LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "sss";
    }

    $query .= " ORDER BY b.created_at DESC";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    
    echo json_encode(['success' => true, 'bookings' => $bookings]);

} elseif ($action === 'details') {
    $bookingId = $_GET['id'] ?? 0;
    
    // Get booking details
    $stmt = $conn->prepare("
        SELECT b.*, u.FirstName, u.LastName, u.Email, u.contactNo, p.packageName, p.Price as packagePrice
        FROM bookings b 
        JOIN users u ON b.userID = u.userID 
        JOIN packages p ON b.packageID = p.packageID 
        WHERE b.bookingID = ?
    ");
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    
    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit;
    }

    // Get addons (handle if table doesn't exist)
    try {
        $stmt = $conn->prepare("
            SELECT a.name, a.price 
            FROM booking_addons ba 
            JOIN addons a ON ba.addonID = a.addonID 
            WHERE ba.bookingID = ?
        ");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        $addons = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $booking['addons'] = $addons;
    } catch (Exception $e) {
        // Table doesn't exist, return empty array
        $booking['addons'] = [];
    }

    // Get logs
    $booking['logs'] = getBookingLogs($bookingId);

    echo json_encode(['success' => true, 'booking' => $booking]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $bookingId = $input['bookingId'] ?? 0;
    $userId = $_SESSION['userId'];

    if ($action === 'update_status') {
        $newStatus = $input['status'] ?? '';
        $result = updateBookingStatus($bookingId, $newStatus, $userId);
        echo json_encode($result);

    } elseif ($action === 'update_note') {
        $note = $input['note'] ?? '';
        $result = addBookingNote($bookingId, $note, $userId);
        echo json_encode($result);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
