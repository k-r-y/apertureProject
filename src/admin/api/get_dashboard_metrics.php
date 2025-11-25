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

try {
    // 1. REVENUE METRICS
    $revenueMetrics = [];
    
    // Total Revenue (non-cancelled)
    $revQuery = "SELECT SUM(total_amount) as total FROM bookings WHERE booking_status != 'cancelled'";
    $revResult = $conn->query($revQuery);
    $revenueMetrics['total'] = floatval($revResult->fetch_assoc()['total'] ?? 0);
    
    // Revenue Growth (current month vs previous month)
    $growthQuery = "SELECT 
        SUM(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) THEN total_amount ELSE 0 END) as current_month,
        SUM(CASE WHEN MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) THEN total_amount ELSE 0 END) as previous_month
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
    $totalBookingsQuery = "SELECT COUNT(*) as total FROM bookings";
    $totalBookingsResult = $conn->query($totalBookingsQuery);
    $bookingMetrics['total'] = intval($totalBookingsResult->fetch_assoc()['total'] ?? 0);
    
    // Upcoming events
    $upcomingQuery = "SELECT COUNT(*) as count FROM bookings 
                     WHERE booking_status NOT IN ('cancelled', 'completed') 
                     AND event_date >= CURDATE()";
    $upcomingResult = $conn->query($upcomingQuery);
    $bookingMetrics['upcoming'] = intval($upcomingResult->fetch_assoc()['count'] ?? 0);
    
    // Average booking value
    $avgValueQuery = "SELECT AVG(total_amount) as avg_value FROM bookings WHERE booking_status != 'cancelled'";
    $avgValueResult = $conn->query($avgValueQuery);
    $bookingMetrics['average_value'] = floatval($avgValueResult->fetch_assoc()['avg_value'] ?? 0);
    
    // 3. CLIENT METRICS
    $clientMetrics = [];
    
    // New clients (last 30 days)
    $newClientsQuery = "SELECT COUNT(DISTINCT userID) as count FROM bookings 
                       WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $newClientsResult = $conn->query($newClientsQuery);
    $clientMetrics['new_clients'] = intval($newClientsResult->fetch_assoc()['count'] ?? 0);
    
    // Total unique clients
    $totalClientsQuery = "SELECT COUNT(DISTINCT userID) as count FROM bookings";
    $totalClientsResult = $conn->query($totalClientsQuery);
    $clientMetrics['total_clients'] = intval($totalClientsResult->fetch_assoc()['count'] ?? 0);
    
    // Client retention (repeat clients)
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
    
    // 4. CONVERSION METRICS
    $conversionMetrics = [];
    
    $conversionQuery = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN booking_status IN ('confirmed', 'completed') THEN 1 ELSE 0 END) as confirmed,
        SUM(CASE WHEN booking_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM bookings";
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
    
    // 5. BOOKING STATUS BREAKDOWN
    $statusBreakdown = [];
    $statusQuery = "SELECT booking_status, COUNT(*) as count FROM bookings GROUP BY booking_status";
    $statusResult = $conn->query($statusQuery);
    while ($row = $statusResult->fetch_assoc()) {
        $statusBreakdown[$row['booking_status']] = intval($row['count']);
    }
    
    // 6. MONTHLY REVENUE TREND (12 months)
    $revenueQuery = "SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        SUM(total_amount) as revenue
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
    
    // 7. MONTHLY BOOKINGS TREND (12 months)
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
    
    // 8. PACKAGE PERFORMANCE
    $packageQuery = "SELECT 
        COALESCE(p.packageName, 'No Package') as package_name,
        COUNT(b.bookingID) as bookings,
        SUM(b.total_amount) as revenue
    FROM bookings b
    LEFT JOIN packages p ON b.packageID = p.packageID
    WHERE b.booking_status != 'cancelled'
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
    
    // 9. EVENT TYPE DISTRIBUTION
    $eventTypeQuery = "SELECT 
        event_type,
        COUNT(*) as count
    FROM bookings
    WHERE booking_status != 'cancelled'
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
    
    // 10. RECENT ACTIVITY (last 10)
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
            'reference' => 'BK-' . str_pad($row['bookingID'], 6, '0', STR_PAD_LEFT),  // Generate reference from ID
            'event_type' => $row['event_type'],
            'status' => $row['booking_status'],
            'client' => $row['client_email'],
            'created_at' => $row['created_at']
        ];
    }
    
    // 11. UPCOMING EVENTS (next 7 days)
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
            'reference' => 'BK-' . str_pad($row['bookingID'], 6, '0', STR_PAD_LEFT),  // Generate reference from ID
            'event_type' => $row['event_type'],
            'date' => $row['event_date'],
            'time' => $row['event_time_start'],
            'client' => $row['client_email']
        ];
    }
    
    // COMPILE RESPONSE
    $response = [
        'success' => true,
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
        'debug' => $e->getMessage(),  // Show actual error for debugging
        'trace' => $e->getTraceAsString()
    ]);
}
?>
