<?php
/**
 * Comprehensive Dashboard Metrics API
 * Provides all analytics data for admin dashboard in one optimized call
 */

require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Check admin authentication
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Access global database connection
global $conn;

// Get timeframe parameter (default: month)
$timeframe = isset($_GET['timeframe']) ? $_GET['timeframe'] : 'month';

// Build date filter based on timeframe
$dateFilter = "";
switch ($timeframe) {
    case 'today':
        $dateFilter = "AND DATE(b.created_at) = CURDATE()";
        break;
    case 'week':
        $dateFilter = "AND YEARWEEK(b.created_at, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'month':
        $dateFilter = "AND MONTH(b.created_at) = MONTH(CURDATE()) AND YEAR(b.created_at) = YEAR(CURDATE())";
        break;
    case 'quarter':
        $dateFilter = "AND QUARTER(b.created_at) = QUARTER(CURDATE()) AND YEAR(b.created_at) = YEAR(CURDATE())";
        break;
    case 'year':
        $dateFilter = "AND YEAR(b.created_at) = YEAR(CURDATE())";
        break;
    case 'all':
    default:
        $dateFilter = "";  // No filter for "all time"
        break;
}

try {
    // 1. REVENUE METRICS
    $revenueMetrics = [];
    
    // Total Revenue (non-cancelled) with timeframe filter
    // Note: For revenue, we use the bookings alias 'b' if we join, but here simple query
    // We need to be consistent with aliases. Let's use 'b' alias in main queries or just standard WHERE
    
    // Fix: Use simple WHERE for single table queries, but ensure alias compatibility if needed
    // Actually, let's just use the table name directly or alias if we define it.
    // For simplicity, I'll modify the $dateFilter to not assume an alias 'b' unless I use it.
    // Wait, I used 'b.created_at' in the switch. Let's make sure queries use alias 'b' or I remove 'b.' from filter.
    // Better: Remove 'b.' from filter and add it where needed or just use column name since most queries are single table.
    // Actually, some queries join. Let's use a variable for the column name.
    
    $colName = "created_at";
    
    // Re-build filter without alias
    $filterSQL = "";
    switch ($timeframe) {
        case 'today':
            $filterSQL = "AND DATE($colName) = CURDATE()";
            break;
        case 'week':
            $filterSQL = "AND YEARWEEK($colName, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'month':
            $filterSQL = "AND MONTH($colName) = MONTH(CURDATE()) AND YEAR($colName) = YEAR(CURDATE())";
            break;
        case 'quarter':
            $filterSQL = "AND QUARTER($colName) = QUARTER(CURDATE()) AND YEAR($colName) = YEAR(CURDATE())";
            break;
        case 'year':
            $filterSQL = "AND YEAR($colName) = YEAR(CURDATE())";
            break;
        case 'all':
        default:
            $filterSQL = "";
            break;
    }

    // Total Revenue
    $revQuery = "SELECT SUM(CASE WHEN is_fully_paid = 1 THEN total_amount ELSE downpayment_amount END) as total FROM bookings WHERE booking_status != 'cancelled' $filterSQL";
    $revResult = $conn->query($revQuery);
    $revenueMetrics['total'] = floatval($revResult->fetch_assoc()['total'] ?? 0);
    
    // Revenue Growth (current vs previous period)
    // This is tricky with variable timeframes. For now, let's keep the monthly growth logic 
    // but maybe adapt it or just show it for monthly view. 
    // Let's stick to the original "Current Month vs Previous Month" logic regardless of filter 
    // OR disable it for non-monthly views. 
    // Let's keep it as "Month over Month" growth for now as a general indicator.
    
    $growthQuery = "SELECT 
        SUM(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) THEN (CASE WHEN is_fully_paid = 1 THEN total_amount ELSE downpayment_amount END) ELSE 0 END) as current_month,
        SUM(CASE WHEN MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) THEN (CASE WHEN is_fully_paid = 1 THEN total_amount ELSE downpayment_amount END) ELSE 0 END) as previous_month
    FROM bookings WHERE booking_status != 'cancelled'";
    $growthResult = $conn->query($growthQuery);
    $growthData = $growthResult->fetch_assoc();
    $currentMonth = floatval($growthData['current_month'] ?? 0);
    $previousMonth = floatval($growthData['previous_month'] ?? 0);
    $revenueMetrics['current_month'] = $currentMonth;
    $revenueMetrics['previous_month'] = $previousMonth;
    $revenueMetrics['growth_percentage'] = $previousMonth > 0 ? (($currentMonth - $previousMonth) / $previousMonth) * 100 : 0;
    
    // 2. BOOKING METRICS
    $bookingMetrics = [];
    
    // Total bookings
    $totalBookingsQuery = "SELECT COUNT(*) as total FROM bookings WHERE 1=1 $filterSQL";
    $totalBookingsResult = $conn->query($totalBookingsQuery);
    $bookingMetrics['total'] = intval($totalBookingsResult->fetch_assoc()['total'] ?? 0);
    
    // Upcoming events (Always future, so filter doesn't apply the same way, but maybe we filter by "created in timeframe"?)
    // Usually "Upcoming" means future events regardless of when booked. Let's keep it as is (future events).
    $upcomingQuery = "SELECT COUNT(*) as count FROM bookings 
                     WHERE booking_status NOT IN ('cancelled', 'completed') 
                     AND event_date >= CURDATE()";
    $upcomingResult = $conn->query($upcomingQuery);
    $bookingMetrics['upcoming'] = intval($upcomingResult->fetch_assoc()['count'] ?? 0);
    
    // Average booking value (filtered)
    $avgValueQuery = "SELECT AVG(total_amount) as avg_value FROM bookings WHERE booking_status != 'cancelled' $filterSQL";
    $avgValueResult = $conn->query($avgValueQuery);
    $bookingMetrics['average_value'] = floatval($avgValueResult->fetch_assoc()['avg_value'] ?? 0);
    
    // 3. CLIENT METRICS
    $clientMetrics = [];
    
    // New clients (filtered by timeframe)
    $newClientsQuery = "SELECT COUNT(DISTINCT userID) as count FROM bookings 
                       WHERE 1=1 $filterSQL";
    $newClientsResult = $conn->query($newClientsQuery);
    $clientMetrics['new_clients'] = intval($newClientsResult->fetch_assoc()['count'] ?? 0);
    
    // Total unique clients (All time)
    $totalClientsQuery = "SELECT COUNT(DISTINCT userID) as count FROM bookings";
    $totalClientsResult = $conn->query($totalClientsQuery);
    $clientMetrics['total_clients'] = intval($totalClientsResult->fetch_assoc()['count'] ?? 0);
    
    // Client retention (repeat clients) - Global metric
    $retentionQuery = "SELECT 
        COUNT(DISTINCT userID) as total_clients,
        SUM(CASE WHEN booking_count > 1 THEN 1 ELSE 0 END) as repeat_clients
    FROM (
        SELECT userID, COUNT(*) as booking_count 
        FROM bookings 
        GROUP BY userID
    ) as client_bookings";
    $retentionResult = $conn->query($retentionQuery);
    $retentionData = $retentionResult->fetch_assoc();
    $totalClients = intval($retentionData['total_clients'] ?? 0);
    $repeatClients = intval($retentionData['repeat_clients'] ?? 0);
    $clientMetrics['repeat_clients'] = $repeatClients;
    $clientMetrics['retention_rate'] = $totalClients > 0 ? ($repeatClients / $totalClients) * 100 : 0;
    
    // 4. CONVERSION METRICS (Filtered)
    $conversionMetrics = [];
    
    $conversionQuery = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN booking_status IN ('confirmed', 'completed') THEN 1 ELSE 0 END) as confirmed,
        SUM(CASE WHEN booking_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM bookings WHERE 1=1 $filterSQL";
    $conversionResult = $conn->query($conversionQuery);
    $conversionData = $conversionResult->fetch_assoc();
    $totalBookings = intval($conversionData['total'] ?? 0);
    $confirmedBookings = intval($conversionData['confirmed'] ?? 0);
    $cancelledBookings = intval($conversionData['cancelled'] ?? 0);
    $conversionMetrics['total'] = $totalBookings;
    $conversionMetrics['confirmed'] = $confirmedBookings;
    $conversionMetrics['cancelled'] = $cancelledBookings;
    $conversionMetrics['conversion_rate'] = $totalBookings > 0 ? ($confirmedBookings / $totalBookings) * 100 : 0;
    $conversionMetrics['cancellation_rate'] = $totalBookings > 0 ? ($cancelledBookings / $totalBookings) * 100 : 0;
    
    // 5. BOOKING STATUS BREAKDOWN (Filtered)
    $statusBreakdown = [];
    $statusQuery = "SELECT booking_status, COUNT(*) as count FROM bookings WHERE 1=1 $filterSQL GROUP BY booking_status";
    $statusResult = $conn->query($statusQuery);
    while ($row = $statusResult->fetch_assoc()) {
        $statusBreakdown[$row['booking_status']] = intval($row['count']);
    }
    
    // 6. MONTHLY REVENUE TREND (12 months) - Always show 12 months trend regardless of filter
    $revenueQuery = "SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        SUM(CASE WHEN is_fully_paid = 1 THEN total_amount ELSE downpayment_amount END) as revenue
    FROM bookings
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    AND booking_status != 'cancelled'
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC";
    $revenueResult = $conn->query($revenueQuery);
    
    $revenueTrend = [];
    while ($row = $revenueResult->fetch_assoc()) {
        $revenueTrend[] = [
            'month' => $row['month'],
            'revenue' => floatval($row['revenue'])
        ];
    }
    
    // 7. MONTHLY BOOKINGS TREND (12 months) - Always show 12 months trend
    $monthlyQuery = "SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as count
    FROM bookings
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC";
    $monthlyResult = $conn->query($monthlyQuery);
    
    $bookingsTrend = [];
    while ($row = $monthlyResult->fetch_assoc()) {
        $bookingsTrend[] = [
            'month' => $row['month'],
            'count' => intval($row['count'])
        ];
    }
    
    // 8. PACKAGE PERFORMANCE (Filtered)
    // Need to use alias 'b' for bookings here because of JOIN
    $filterSQL_b = str_replace("created_at", "b.created_at", $filterSQL);
    
    $packageQuery = "SELECT 
        COALESCE(p.packageName, 'No Package') as package_name,
        COUNT(b.bookingID) as bookings,
        SUM(CASE WHEN b.is_fully_paid = 1 THEN b.total_amount ELSE b.downpayment_amount END) as revenue
    FROM bookings b
    LEFT JOIN packages p ON b.packageID = p.packageID
    WHERE b.booking_status != 'cancelled' $filterSQL_b
    GROUP BY p.packageID, p.packageName
    ORDER BY revenue DESC";
    $packageResult = $conn->query($packageQuery);
    
    $packagePerformance = [];
    while ($row = $packageResult->fetch_assoc()) {
        $packagePerformance[] = [
            'name' => $row['package_name'],
            'bookings' => intval($row['bookings']),
            'revenue' => floatval($row['revenue'])
        ];
    }
    
    // 9. EVENT TYPE DISTRIBUTION (Filtered)
    $eventTypeQuery = "SELECT 
        event_type,
        COUNT(*) as count
    FROM bookings
    WHERE booking_status != 'cancelled' $filterSQL
    GROUP BY event_type
    ORDER BY count DESC
    LIMIT 10";
    $eventTypeResult = $conn->query($eventTypeQuery);
    
    $eventTypes = [];
    while ($row = $eventTypeResult->fetch_assoc()) {
        $eventTypes[] = [
            'type' => $row['event_type'] ?? 'Other',
            'count' => intval($row['count'])
        ];
    }
    
    // 10. RECENT ACTIVITY (last 10) - Always show most recent
    $activityQuery = "SELECT 
        b.bookingID,
        b.event_type,
        b.booking_status,
        b.created_at,
        u.Email as client_email
    FROM bookings b
    LEFT JOIN users u ON b.userID = u.userID
    ORDER BY b.created_at DESC
    LIMIT 10";
    $activityResult = $conn->query($activityQuery);
    
    $recentActivity = [];
    while ($row = $activityResult->fetch_assoc()) {
        $recentActivity[] = [
            'id' => $row['bookingID'],
            'reference' => 'BK-' . str_pad($row['bookingID'], 6, '0', STR_PAD_LEFT),
            'event_type' => $row['event_type'],
            'status' => $row['booking_status'],
            'client' => $row['client_email'],
            'created_at' => $row['created_at']
        ];
    }
    
    // 11. UPCOMING EVENTS (next 7 days) - Always show upcoming
    $upcomingEventsQuery = "SELECT 
        b.bookingID,
        b.event_type,
        b.event_date,
        b.event_time_start,
        u.Email as client_email
    FROM bookings b
    LEFT JOIN users u ON b.userID = u.userID
    WHERE b.event_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    AND b.booking_status IN ('confirmed', 'pending')
    ORDER BY b.event_date ASC, b.event_time_start ASC";
    $upcomingEventsResult = $conn->query($upcomingEventsQuery);
    
    $upcomingEvents = [];
    while ($row = $upcomingEventsResult->fetch_assoc()) {
        $upcomingEvents[] = [
            'id' => $row['bookingID'],
            'reference' => 'BK-' . str_pad($row['bookingID'], 6, '0', STR_PAD_LEFT),
            'event_type' => $row['event_type'],
            'date' => $row['event_date'],
            'time' => $row['event_time_start'],
            'client' => $row['client_email']
        ];
    }
    
    // COMPILE RESPONSE
    $response = [
        'success' => true,
        'timeframe' => $timeframe,
        'revenue' => $revenueMetrics,
        'bookings' => $bookingMetrics,
        'clients' => $clientMetrics,
        'conversion' => $conversionMetrics,
        'status_breakdown' => $statusBreakdown,
        'revenue_trend' => $revenueTrend,
        'bookings_trend' => $bookingsTrend,
        'package_performance' => $packagePerformance,
        'event_types' => $eventTypes,
        'recent_activity' => $recentActivity,
        'upcoming_events' => $upcomingEvents,
        'generated_at' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Dashboard Metrics Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch dashboard metrics',
        'debug' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
