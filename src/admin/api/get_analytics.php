   <?php
    $revenueResult = $conn->query($revenueQuery);
    $totalRevenue = $revenueResult->fetch_assoc()['total_revenue'] ?? 0;

    // Total Bookings
    $bookingsQuery = "SELECT COUNT(*) as total_bookings FROM bookings";
    $bookingsResult = $conn->query($bookingsQuery);
    $totalBookings = $bookingsResult->fetch_assoc()['total_bookings'] ?? 0;

    // Upcoming Events (not cancelled, not completed, event_date in future)
    $upcomingQuery = "SELECT COUNT(*) as upcoming_events 
                      FROM bookings 
                      WHERE booking_status NOT IN ('cancelled', 'completed') 
                      AND event_date >= CURDATE()";
    $upcomingResult = $conn->query($upcomingQuery);
    $upcomingEvents = $upcomingResult->fetch_assoc()['upcoming_events'] ?? 0;

    // New Clients (last 30 days)
    $newClientsQuery = "SELECT COUNT(DISTINCT userID) as new_clients 
                        FROM bookings 
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $newClientsResult = $conn->query($newClientsQuery);
    $newClients = $newClientsResult->fetch_assoc()['new_clients'] ?? 0;

    // Monthly Bookings (last 12 months)
    $monthlyQuery = "SELECT 
                        MONTH(created_at) as month,
                        YEAR(created_at) as year,
                        COUNT(*) as count
                     FROM bookings
                     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                     GROUP BY YEAR(created_at), MONTH(created_at)
                     ORDER BY year, month";
    $monthlyResult = $conn->query($monthlyQuery);
    
    $monthlyBookings = array_fill(0, 12, 0);
    $currentMonth = (int)date('n');
    
    while ($row = $monthlyResult->fetch_assoc()) {
        $monthIndex = ((int)$row['month'] - $currentMonth + 11) % 12;
        $monthlyBookings[11 - $monthIndex] = (int)$row['count'];
    }

    // Package Popularity
    $packageQuery = "SELECT 
                        p.packageName,
                        COUNT(b.bookingID) as count
                     FROM bookings b
                     JOIN packages p ON b.packageID = p.packageID
                     WHERE b.booking_status != 'cancelled'
                     GROUP BY p.packageID, p.packageName
                     ORDER BY count DESC
                     LIMIT 5";
    $packageResult = $conn->query($packageQuery);
    
    $packageData = [];
    while ($row = $packageResult->fetch_assoc()) {
        $packageData[] = [
            'name' => $row['name'],
            'count' => (int)$row['count']
        ];
    }

    // Response
    echo json_encode([
        'success' => true,
        'stats' => []
    ]);
    $revenueResult = $conn->query($revenueQuery);
    $totalRevenue = $revenueResult->fetch_assoc()['total_revenue'] ?? 0;

    // Total Bookings
    $bookingsQuery = "SELECT COUNT(*) as total_bookings FROM bookings";
    $bookingsResult = $conn->query($bookingsQuery);
    $totalBookings = $bookingsResult->fetch_assoc()['total_bookings'] ?? 0;

    // Upcoming Events (not cancelled, not completed, event_date in future)
    $upcomingQuery = "SELECT COUNT(*) as upcoming_events 
                      FROM bookings 
                      WHERE booking_status NOT IN ('cancelled', 'completed') 
                      AND event_date >= CURDATE()";
    $upcomingResult = $conn->query($upcomingQuery);
    $upcomingEvents = $upcomingResult->fetch_assoc()['upcoming_events'] ?? 0;

    // New Clients (last 30 days)
    $newClientsQuery = "SELECT COUNT(DISTINCT userID) as new_clients 
                        FROM bookings 
                        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $newClientsResult = $conn->query($newClientsQuery);
    $newClients = $newClientsResult->fetch_assoc()['new_clients'] ?? 0;

    // Monthly Bookings (last 12 months)
    $monthlyQuery = "SELECT 
                        MONTH(created_at) as month,
                        YEAR(created_at) as year,
                        COUNT(*) as count
                     FROM bookings
                     WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                     GROUP BY YEAR(created_at), MONTH(created_at)
                     ORDER BY year, month";
    $monthlyResult = $conn->query($monthlyQuery);
    
    $monthlyBookings = array_fill(0, 12, 0);
    $currentMonth = (int)date('n');
    
    while ($row = $monthlyResult->fetch_assoc()) {
        $monthIndex = ((int)$row['month'] - $currentMonth + 11) % 12;
        $monthlyBookings[11 - $monthIndex] = (int)$row['count'];
    }

    // Package Popularity
    $packageQuery = "SELECT 
                        p.packageName,
                        COUNT(b.bookingID) as count
                     FROM bookings b
                     JOIN packages p ON b.packageID = p.packageID
                     WHERE b.booking_status != 'cancelled'
                     GROUP BY p.packageID, p.packageName
                     ORDER BY count DESC
                     LIMIT 5";
    $packageResult = $conn->query($packageQuery);
    
    $packageData = [];
    while ($row = $packageResult->fetch_assoc()) {
        $packageData[] = [
            'name' => $row['packageName'],
            'count' => (int)$row['count']
        ];
    }

    // Response
    $response = [
        'success' => true,
        'stats' => [
            'totalRevenue' => number_format($totalRevenue, 2),
            'totalBookings' => $totalBookings,
            'upcomingEvents' => $upcomingEvents,
            'newClients' => $newClients
        ],
        'monthlyBookings' => $monthlyBookings,
        'package_popularity' => $packageData
    ];

    // Store in cache for 15 minutes (900 seconds)
    Cache::set($cacheKey, $response, 900);

    echo json_encode($response);



?>
