<?php
/**
 * Activity Logger
 * Tracks user actions across the system for enquiries/activity tracking
 */

require_once __DIR__ . '/config.php';

/**
 * Log user activity
 * 
 * @param int $userId User ID
 * @param string $activityType Type: contact_form, booking_created, booking_cancelled, payment_made, inquiry_sent, booking_updated
 * @param string $description Human-readable description
 * @param int|null $bookingId Related booking ID (optional)
 * @param array|null $metadata Additional data as associative array (optional)
 * @return bool Success status
 */
function logUserActivity($userId, $activityType, $description, $bookingId = null, $metadata = null) {
    global $conn;
    
    $validTypes = ['contact_form', 'booking_created', 'booking_cancelled', 'payment_made', 'inquiry_sent', 'booking_updated'];
    
    if (!in_array($activityType, $validTypes)) {
        error_log("Invalid activity type: $activityType");
        return false;
    }
    
    try {
        $metadataJson = $metadata ? json_encode($metadata) : null;
        
        $stmt = $conn->prepare("
            INSERT INTO user_activity_log 
            (user_id, activity_type, activity_description, related_booking_id, metadata) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param("issss", $userId, $activityType, $description, $bookingId, $metadataJson);
        
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        
        $stmt->close();
        return false;
        
    } catch (Exception $e) {
        error_log("Activity logging error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get user activities
 * 
 * @param int|null $userId Filter by user ID (null for all users)
 * @param string|null $activityType Filter by activity type
 * @param int $limit Number of records to return
 * @param int $offset Offset for pagination
 * @return array Activity records
 */
function getUserActivities($userId = null, $activityType = null, $limit = 50, $offset = 0) {
    global $conn;
    
    $query = "
        SELECT 
            a.*,
            u.fullName as user_name,
            u.email as user_email,
            b.bookingID as booking_reference,
            b.event_type
        FROM user_activity_log a
        LEFT JOIN users u ON a.user_id = u.userID
        LEFT JOIN bookings b ON a.related_booking_id = b.bookingID
        WHERE 1=1
    ";
    
    $params = [];
    $types = "";
    
    if ($userId !== null) {
        $query .= " AND a.user_id = ?";
        $params[] = $userId;
        $types .= "i";
    }
    
    if ($activityType !== null) {
        $query .= " AND a.activity_type = ?";
        $params[] = $activityType;
        $types .= "s";
    }
    
    $query .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $activities = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $activities;
}

/**
 * Get activity count
 * 
 * @param int|null $userId Filter by user ID
 * @param string|null $activityType Filter by activity type
 * @return int Total count
 */
function getActivityCount($userId = null, $activityType = null) {
    global $conn;
    
    $query = "SELECT COUNT(*) as total FROM user_activity_log WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if ($userId !== null) {
        $query .= " AND user_id = ?";
        $params[] = $userId;
        $types .= "i";
    }
    
    if ($activityType !== null) {
        $query .= " AND activity_type = ?";
        $params[] = $activityType;
        $types .= "s";
    }
    
    $stmt = $conn->prepare($query);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row['total'] ?? 0;
}

