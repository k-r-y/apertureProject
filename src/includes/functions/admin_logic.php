<?php

// ====================================
// DASHBOARD STATISTICS FUNCTIONS
// ====================================

function getTotalRevenue() {
    global $conn;
    $stmt = $conn->prepare("SELECT SUM(total_amount) as revenue FROM bookings WHERE payment_status IN ('Paid', 'Partial')");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['revenue'] ?? 0;
}

function getPendingBookingsCount() {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE booking_status = 'Pending'");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'] ?? 0;
}

function getNewClientsCount($days = 30) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'User' AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL ? DAY)");
    $stmt->bind_param("i", $days);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'] ?? 0;
}

// ====================================
// CHART DATA FUNCTIONS
// ====================================

function getMonthlyBookingsData($year = null) {
    global $conn;
    if (!$year) $year = date('Y');
    
    $stmt = $conn->prepare("
        SELECT MONTH(event_date) as month, COUNT(*) as count 
        FROM bookings 
        WHERE YEAR(event_date) = ? 
        GROUP BY MONTH(event_date) 
        ORDER BY month
    ");
    $stmt->bind_param("i", $year);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Initialize array with 0 for all 12 months
    $data = array_fill(1, 12, 0);
    while ($row = $result->fetch_assoc()) {
        $data[$row['month']] = (int)$row['count'];
    }
    
    return array_values($data);
}

function getServicePopularityData() {
    global $conn;
    $stmt = $conn->prepare("
        SELECT p.packageName, COUNT(b.bookingID) as count 
        FROM bookings b 
        JOIN packages p ON b.packageID = p.packageID 
        GROUP BY b.packageID, p.packageName
        ORDER BY count DESC
        LIMIT 5
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $labels = [];
    $series = [];
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['packageName'];
        $series[] = (int)$row['count'];
    }
    
    return ['labels' => $labels, 'series' => $series];
}

// ====================================
// APPOINTMENTS FUNCTIONS
// ====================================

function getAllBookingsForAdmin($limit = 10, $offset = 0, $filters = []) {
    global $conn;
    
    $query = "
        SELECT b.*, 
               CONCAT(u.fname, ' ', u.lname) as client_name,
               u.email as client_email,
               p.packageName 
        FROM bookings b 
        JOIN users u ON b.userID = u.userID 
        JOIN packages p ON b.packageID = p.packageID 
        WHERE 1=1
    ";
    
    $params = [];
    $types = "";
    
    if (!empty($filters['status'])) {
        $query .= " AND b.booking_status = ?";
        $params[] = $filters['status'];
        $types .= "s";
    }
    
    if (!empty($filters['date'])) {
        $query .= " AND DATE(b.event_date) = ?";
        $params[] = $filters['date'];
        $types .= "s";
    }
    
    if (!empty($filters['search'])) {
        $query .= " AND (u.fname LIKE ? OR u.lname LIKE ? OR u.email LIKE ? OR b.eventType LIKE ?)";
        $searchTerm = "%" . $filters['search'] . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ssss";
    }
    
    $query .= " ORDER BY b.event_date DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getTotalBookingsCountForAdmin($filters = []) {
    global $conn;
    
    $query = "SELECT COUNT(*) as count FROM bookings b JOIN users u ON b.userID = u.userID WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if (!empty($filters['status'])) {
        $query .= " AND b.booking_status = ?";
        $params[] = $filters['status'];
        $types .= "s";
    }
    
    if (!empty($filters['date'])) {
        $query .= " AND DATE(b.event_date) = ?";
        $params[] = $filters['date'];
        $types .= "s";
    }
    
    if (!empty($filters['search'])) {
        $query .= " AND (u.fname LIKE ? OR u.lname LIKE ? OR u.email LIKE ? OR b.eventType LIKE ?)";
        $searchTerm = "%" . $filters['search'] . "%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ssss";
    }
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'] ?? 0;
}

// ====================================
// CALENDAR FUNCTIONS
// ====================================

function getBookingEventsForCalendar() {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT b.bookingID, b.event_date, b.start_time, b.end_time,
               CONCAT(u.fname, ' ', u.lname) as client_name, 
               b.eventType, p.packageName,
               b.booking_status
        FROM bookings b 
        JOIN users u ON b.userID = u.userID 
        JOIN packages p ON b.packageID = p.packageID 
        WHERE b.booking_status != 'Cancelled'
        ORDER BY b.event_date ASC
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $eventTitle = $row['client_name'];
        if (!empty($row['eventType'])) {
            $eventTitle .= ' - ' . $row['eventType'];
        }
        
        // Determine color based on status
        $backgroundColor = '#666'; // Default
        if ($row['booking_status'] === 'Confirmed') {
            $backgroundColor = '#d4af37'; // Gold
        } elseif ($row['booking_status'] === 'Pending') {
            $backgroundColor = '#ffc107'; // Amber
        } elseif ($row['booking_status'] === 'Completed') {
            $backgroundColor = '#28a745'; // Green
        }
        
        $events[] = [
            'id' => $row['bookingID'],
            'title' => $eventTitle,
            'start' => $row['event_date'] . 'T' . $row['start_time'],
            'end' => !empty($row['end_time']) ? $row['event_date'] . 'T' . $row['end_time'] : null,
            'backgroundColor' => $backgroundColor,
            'borderColor' => '#d4af37',
            'extendedProps' => [
                'package' => $row['packageName'],
                'status' => $row['booking_status'],
                'clientName' => $row['client_name']
            ]
        ];
    }
    
    return $events;
}

?>
