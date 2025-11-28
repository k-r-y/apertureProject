<?php

function validateBookingDate($date) {
    $minDate = date('Y-m-d', strtotime('+3 days'));
    $maxDate = date('Y-m-d', strtotime('+3 years'));
    
    if (strtotime($date) < strtotime($minDate)) {
        throw new Exception("Bookings must be made at least 3 days in advance.");
    }
    
    if (strtotime($date) > strtotime($maxDate)) {
        throw new Exception("Bookings cannot be made more than 3 years in advance.");
    }
    
    return true;
}

function validateBookingTime($startTime, $endTime) {
    $start = strtotime($startTime);
    $end = strtotime($endTime);
    
    $minTime = strtotime('07:00:00');
    $maxTime = strtotime('22:00:00');
    
    // Extract time part only for comparison if dates are involved, but input is usually just time
    // Assuming inputs are 'H:i' strings
    // Let's use a fixed date to compare times correctly
    $baseDate = date('Y-m-d');
    $startTs = strtotime("$baseDate $startTime");
    $endTs = strtotime("$baseDate $endTime");
    $minTs = strtotime("$baseDate 07:00:00");
    $maxTs = strtotime("$baseDate 22:00:00");

    if ($startTs < $minTs || $endTs > $maxTs) {
        throw new Exception("Events must be scheduled between 7:00 AM and 10:00 PM.");
    }
    
    if ($startTs >= $endTs) {
        throw new Exception("End time must be after start time.");
    }
    
    return true;
}

function calculateExtraHoursCost($startTime, $endTime, $coverageHours, $rate) {
    $start = new DateTime($startTime);
    $end = new DateTime($endTime);
    $interval = $start->diff($end);
    
    $durationHours = $interval->h + ($interval->i / 60);
    
    $extraHours = max(0, $durationHours - $coverageHours);
    // Round up extra hours? Or charge per fraction? Usually per hour or fraction.
    // Let's assume per hour started (ceil) or exact. User said "excess hours".
    // Let's use exact float for now, or ceil if typical.
    // "excess hours should be paid extra". Let's ceil to nearest hour for simplicity or use float.
    // Common practice: charge per hour.
    $extraHours = ceil($extraHours); 
    
    return $extraHours * $rate;
}

function getPackageData($conn, $packageId) {
    $stmt = $conn->prepare("SELECT Price, packageName, coverage_hours, extra_hour_rate FROM packages WHERE packageID = ?");
    $stmt->bind_param("s", $packageId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Invalid package selected.");
    }
    return $result->fetch_assoc();
}

function getBookingCount($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE userID = ?");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_row()[0];
}


function getUpcomingBookingsCount($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE userID = ? AND booking_status = 'confirmed' AND event_date >= CURRENT_DATE() ORDER BY event_date ASC");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_row()[0];
}   

function getTotalSpent($userId) {
    global $conn;
    // Sum of confirmed downpayments + confirmed final payments - refunds
    $query = "
        SELECT 
            (
                COALESCE(SUM(CASE WHEN downpayment_paid = 1 THEN downpayment_amount ELSE 0 END), 0) +
                COALESCE(SUM(CASE WHEN final_payment_paid = 1 THEN (total_amount - downpayment_amount) ELSE 0 END), 0) -
                COALESCE(SUM(refund_amount), 0)
            ) as total_spent
        FROM bookings 
        WHERE userID = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_row()[0] ?? 0;
}

function getAllBookingsCount() {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_row()[0];
}

function getAllBookings() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM bookings");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getTotalRevenue() {
    global $conn;
    // Sum of confirmed downpayments + confirmed final payments - refunds
    // This represents actual cash flow
    $query = "
        SELECT 
            (
                COALESCE(SUM(CASE WHEN downpayment_paid = 1 THEN downpayment_amount ELSE 0 END), 0) +
                COALESCE(SUM(CASE WHEN final_payment_paid = 1 THEN balance_amount ELSE 0 END), 0) -
                COALESCE(SUM(refund_amount), 0)
            ) as total_revenue
        FROM bookings
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_row()[0] ?? 0;
}

function getAverageBookingDuration() {
    global $conn;
    $stmt = $conn->prepare("SELECT AVG(event_duration) FROM bookings");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_row()[0];
}

function getBookingsByPackageType($userId) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT p.packageName, COUNT(b.bookingID) as count
        FROM bookings b
        INNER JOIN packages p ON b.packageID = p.packageID
        WHERE b.userID = ?
        GROUP BY p.packageName
        ORDER BY count DESC
    ");
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// function 

?>



