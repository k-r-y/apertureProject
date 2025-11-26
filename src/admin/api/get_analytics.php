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

$action = $_GET['action'] ?? 'all';
$timeframe = $_GET['timeframe'] ?? 'month'; // today, week, month, year, all

try {
    $response = [];

    // Date filter logic
    $dateCondition = "";
    $params = [];
    $types = "";

    switch ($timeframe) {
        case 'today':
            $dateCondition = "AND DATE(event_date) = CURDATE()";
            break;
        case 'week':
            $dateCondition = "AND YEARWEEK(event_date, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'month':
            $dateCondition = "AND MONTH(event_date) = MONTH(CURDATE()) AND YEAR(event_date) = YEAR(CURDATE())";
            break;
        case 'year':
            $dateCondition = "AND YEAR(event_date) = YEAR(CURDATE())";
            break;
        case 'all':
        default:
            $dateCondition = "";
            break;
    }

    // 1. Key Metrics
    if ($action === 'all' || $action === 'metrics') {
        // Total Revenue (Confirmed/Completed bookings)
        $revSql = "SELECT SUM(total_amount) as total FROM bookings WHERE booking_status IN ('confirmed', 'completed') $dateCondition";
        $revResult = $conn->query($revSql);
        $response['revenue'] = $revResult->fetch_assoc()['total'] ?? 0;

        // Total Bookings
        $bookSql = "SELECT COUNT(*) as total FROM bookings WHERE 1=1 $dateCondition";
        $bookResult = $conn->query($bookSql);
        $response['bookings'] = $bookResult->fetch_assoc()['total'] ?? 0;

        // Upcoming Events (Next 7 days)
        $upSql = "SELECT COUNT(*) as total FROM bookings WHERE booking_status IN ('confirmed', 'pending') AND event_date >= CURDATE() AND event_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
        $upResult = $conn->query($upSql);
        $response['upcoming'] = $upResult->fetch_assoc()['total'] ?? 0;

        // Pending Requests (Actionable)
        $pendingSql = "SELECT COUNT(*) as total FROM bookings WHERE booking_status = 'pending'";
        $pendingResult = $conn->query($pendingSql);
        $response['pending_count'] = $pendingResult->fetch_assoc()['total'] ?? 0;

        // Average Booking Value
        $avgSql = "SELECT AVG(total_amount) as avg_val FROM bookings WHERE booking_status IN ('confirmed', 'completed')";
        $avgResult = $conn->query($avgSql);
        $response['avg_value'] = $avgResult->fetch_assoc()['avg_val'] ?? 0;

        // New Clients (Last 30 days)
        $clientSql = "SELECT COUNT(*) as total FROM users WHERE Role = 'User' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $clientResult = $conn->query($clientSql);
        $response['new_clients'] = $clientResult->fetch_assoc()['total'] ?? 0;
    }

    // 2. Revenue & Booking Trend (Last 12 Months)
    if ($action === 'all' || $action === 'revenue_trend') {
        $trendSql = "
            SELECT 
                DATE_FORMAT(event_date, '%Y-%m') as month,
                SUM(total_amount) as total,
                COUNT(*) as count
            FROM bookings 
            WHERE booking_status IN ('confirmed', 'completed')
            AND event_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY month
            ORDER BY month ASC
        ";
        $trendResult = $conn->query($trendSql);
        $trendData = [];
        while ($row = $trendResult->fetch_assoc()) {
            $trendData[] = $row;
        }
        $response['revenue_trend'] = $trendData;
    }

    // 3. Booking Status Distribution
    if ($action === 'all' || $action === 'booking_status') {
        $statusSql = "SELECT booking_status, COUNT(*) as count FROM bookings WHERE 1=1 $dateCondition GROUP BY booking_status";
        $statusResult = $conn->query($statusSql);
        $statusData = [];
        while ($row = $statusResult->fetch_assoc()) {
            $statusData[$row['booking_status']] = $row['count'];
        }
        $response['booking_status'] = $statusData;
    }

    // 4. Recent Activity (Pending Bookings for Action Center)
    if ($action === 'all' || $action === 'recent_activity') {
        $activitySql = "
            SELECT 
                b.bookingID, 
                b.event_date, 
                b.booking_status, 
                b.total_amount,
                u.FirstName, 
                u.LastName 
            FROM bookings b
            JOIN users u ON b.userID = u.userID
            WHERE b.booking_status = 'pending'
            ORDER BY b.created_at DESC 
            LIMIT 10
        ";
        $activityResult = $conn->query($activitySql);
        $activityData = [];
        while ($row = $activityResult->fetch_assoc()) {
            $activityData[] = $row;
        }
        $response['recent_activity'] = $activityData;
    }

    // 5. Upcoming Events List (Next 7 Days)
    if ($action === 'all' || $action === 'upcoming_events') {
        $upcomingSql = "
            SELECT 
                b.bookingID, 
                b.event_date, 
                b.event_time_start,
                b.event_type,
                u.FirstName, 
                u.LastName 
            FROM bookings b
            JOIN users u ON b.userID = u.userID
            WHERE b.booking_status IN ('confirmed', 'completed') 
            AND b.event_date >= CURDATE() 
            AND b.event_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
            ORDER BY b.event_date ASC
        ";
        $upcomingResult = $conn->query($upcomingSql);
        $upcomingData = [];
        while ($row = $upcomingResult->fetch_assoc()) {
            $upcomingData[] = $row;
        }
        $response['upcoming_events'] = $upcomingData;
    }

    // 6. Package Performance
    if ($action === 'all' || $action === 'package_performance') {
        $pkgSql = "
            SELECT 
                p.packageName, 
                COUNT(b.bookingID) as count 
            FROM bookings b
            JOIN packages p ON b.packageID = p.packageID
            WHERE b.booking_status IN ('confirmed', 'completed')
            GROUP BY p.packageName
            ORDER BY count DESC
            LIMIT 5
        ";
        $pkgResult = $conn->query($pkgSql);
        $pkgData = [];
        while ($row = $pkgResult->fetch_assoc()) {
            $pkgData[] = $row;
        }
        $response['package_performance'] = $pkgData;
    }

    // 7. Event Type Distribution (Polar Area)
    if ($action === 'all' || $action === 'event_type_distribution') {
        $typeSql = "
            SELECT 
                event_type, 
                COUNT(*) as count 
            FROM bookings 
            WHERE booking_status IN ('confirmed', 'completed')
            GROUP BY event_type
            ORDER BY count DESC
        ";
        $typeResult = $conn->query($typeSql);
        $typeData = [];
        while ($row = $typeResult->fetch_assoc()) {
            $typeData[] = $row;
        }
        $response['event_type_distribution'] = $typeData;
    }

    echo json_encode(['success' => true, 'data' => $response]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
