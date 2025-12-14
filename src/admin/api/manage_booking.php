<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/auth.php';
require_once '../../includes/functions/booking_workflow.php';

// Start output buffering first
ob_start();

// Set JSON header early
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

try {
    if ($action === 'list') {
        // Fetch bookings with filters
        $status = $_GET['status'] ?? 'all';
        $search = $_GET['search'] ?? '';

        // Auto-update past events to post_production
        $updatePastStmt = $conn->prepare("UPDATE bookings SET booking_status = 'post_production' WHERE booking_status = 'confirmed' AND event_date < CURDATE()");
        $updatePastStmt->execute();
        
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
        
        ob_clean();
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
            ob_clean();
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

        ob_clean();
        echo json_encode(['success' => true, 'booking' => $booking]);

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $bookingId = $input['bookingId'] ?? 0;
        $userId = $_SESSION['userId'];

        if ($action === 'update_status') {
            $newStatus = $input['status'] ?? '';
            $result = updateBookingStatus($bookingId, $newStatus, $userId);
            
                // Auto-create invoice if confirmed
            if ($result['success'] && $newStatus === 'confirmed') {
                // Check if invoice exists
                $checkStmt = $conn->prepare("SELECT invoiceID FROM invoices WHERE bookingID = ?");
                $checkStmt->bind_param("i", $bookingId);
                $checkStmt->execute();
                $checkStmt->store_result();
                
                if ($checkStmt->num_rows === 0) {
                    $checkStmt->close(); // Close before next query

                    // Create Invoice
                    $dateStmt = $conn->prepare("SELECT event_date FROM bookings WHERE bookingID = ?");
                    $dateStmt->bind_param("i", $bookingId);
                    $dateStmt->execute();
                    $res = $dateStmt->get_result();
                    $eventDate = $res->fetch_assoc()['event_date'];
                    $dateStmt->close();

                    $issueDate = date('Y-m-d');
                    $dueDate = $eventDate; // Due on event date

                    $insStmt = $conn->prepare("INSERT INTO invoices (bookingID, issue_date, due_date, status) VALUES (?, ?, ?, 'pending')");
                    $insStmt->bind_param("iss", $bookingId, $issueDate, $dueDate);
                    $insStmt->execute();
                    $insStmt->close();
                    
                    // Optional: Log this action
                    logBookingAction($bookingId, $userId, 'invoice_created', 'System auto-created invoice upon confirmation');
                } else {
                    $checkStmt->close();
                }
            }

            ob_clean();
            echo json_encode($result);

        } elseif ($action === 'update_note') {
            $note = $input['note'] ?? '';
            $result = addBookingNote($bookingId, $note, $userId);
            ob_clean();
            echo json_encode($result);

        } elseif ($action === 'update_meeting_link') {
            $meetingLink = $input['meeting_link'] ?? '';
            
            // Enforce protocol
            if (!empty($meetingLink) && !preg_match("~^(?:f|ht)tps?://~i", $meetingLink)) {
                $meetingLink = "https://" . $meetingLink;
            }

            if (empty($meetingLink)) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Meeting link cannot be empty']);
                exit;
            }
            
            // Fetch booking and user details first
            $detailsStmt = $conn->prepare("
                SELECT b.bookingID, b.event_date, u.email, u.firstname, u.lastname, u.userID 
                FROM bookings b 
                JOIN users u ON b.userID = u.userID 
                WHERE b.bookingID = ?
            ");
            $detailsStmt->bind_param("i", $bookingId);
            $detailsStmt->execute();
            $detailsResult = $detailsStmt->get_result();
            $bookingDetails = $detailsResult->fetch_assoc();
            $detailsStmt->close();

            if (!$bookingDetails) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                exit;
            }

            $stmt = $conn->prepare("UPDATE bookings SET meeting_link = ? WHERE bookingID = ?");
            $stmt->bind_param("si", $meetingLink, $bookingId);

            if ($stmt->execute()) {
                logBookingAction($bookingId, $userId, 'meeting_link_updated', "Updated meeting link: $meetingLink");
                
                // Send Email Notification
                require_once '../../includes/functions/notifications.php';
                $notifier = new NotificationSystem($conn);
                $bookingRef = str_pad($bookingId, 6, '0', STR_PAD_LEFT);
                $fullName = $bookingDetails['firstname'] . ' ' . $bookingDetails['lastname'];
                $eventDate = date('F j, Y', strtotime($bookingDetails['event_date']));
                
                $notifier->sendMeetingLinkNotification(
                    $bookingDetails['email'],
                    $fullName,
                    $bookingDetails['userID'], // Added userID
                    $bookingRef,
                    $meetingLink,
                    $eventDate
                );

                ob_clean();
                echo json_encode(['success' => true, 'message' => 'Meeting link updated and user notified']);
            } else {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Failed to update meeting link']);
            }
            $stmt->close();

        } elseif ($action === 'update_details') {
            $eventDate = $input['event_date'] ?? '';
            $startTime = $input['event_time_start'] ?? '';
            $endTime = $input['event_time_end'] ?? '';
            $location = $input['event_location'] ?? '';
            $eventType = $input['event_type'] ?? '';

            // Basic Validation
            if (empty($eventDate) || empty($startTime) || empty($endTime) || empty($location)) {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                exit;
            }

            $stmt = $conn->prepare("UPDATE bookings SET event_date = ?, event_time_start = ?, event_time_end = ?, event_location = ?, event_type = ? WHERE bookingID = ?");
            $stmt->bind_param("sssssi", $eventDate, $startTime, $endTime, $location, $eventType, $bookingId);

            if ($stmt->execute()) {
                logBookingAction($bookingId, $userId, 'details_updated', 'Updated event details (Date/Time/Location)');
                ob_clean();
                echo json_encode(['success' => true, 'message' => 'Details updated']);
            } else {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Failed to update details']);
            }
            $stmt->close();

        } elseif ($action === 'mark_paid') {
            $stmt = $conn->prepare("UPDATE bookings SET is_fully_paid = 1, payment_status = 'Paid', booking_status = 'confirmed' WHERE bookingID = ?");
            $stmt->bind_param("i", $bookingId);
            
            if ($stmt->execute()) {
                $stmt->close(); // Close update stmt
                
                logBookingAction($bookingId, $userId, 'payment_update', 'Marked balance as paid and confirmed booking');
                
                // Auto-create invoice logic
                $checkStmt = $conn->prepare("SELECT invoiceID FROM invoices WHERE bookingID = ?");
                $checkStmt->bind_param("i", $bookingId);
                $checkStmt->execute();
                $checkStmt->store_result();
                
                if ($checkStmt->num_rows === 0) {
                    $checkStmt->close();

                    $dateStmt = $conn->prepare("SELECT event_date FROM bookings WHERE bookingID = ?");
                    $dateStmt->bind_param("i", $bookingId);
                    $dateStmt->execute();
                    $res = $dateStmt->get_result();
                    $eventDate = $res->fetch_assoc()['event_date'];
                    $dateStmt->close();

                    $issueDate = date('Y-m-d');
                    $dueDate = $eventDate;

                    $insStmt = $conn->prepare("INSERT INTO invoices (bookingID, issue_date, due_date, status) VALUES (?, ?, ?, 'pending')");
                    $insStmt->bind_param("iss", $bookingId, $issueDate, $dueDate);
                    $insStmt->execute();
                    $insStmt->close();
                } else {
                    $checkStmt->close();
                }

                ob_clean();
                echo json_encode(['success' => true, 'message' => 'Payment status updated and booking confirmed']);
            } else {
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Failed to update payment']);
            }
        }
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} catch (Throwable $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
}

