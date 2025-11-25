<?php
/**
 * Booking Archival Script
 * Moves old bookings (>2 years) to archive tables for better performance
 * 
 * Usage:
 *   php archive_old_bookings.php           # Normal run
 *   php archive_old_bookings.php --dry-run # Preview without archiving
 */

require_once __DIR__ . '/../includes/functions/config.php';
require_once __DIR__ . '/../includes/functions/logger.php';

// Parse command line arguments
$dryRun = in_array('--dry-run', $argv ?? []);

// Archive threshold: 2 years ago
$archiveDate = date('Y-m-d', strtotime('-2 years'));

echo "=== Booking Archival Script ===\n";
echo "Archive Date: $archiveDate (bookings older than 2 years)\n";
echo "Mode: " . ($dryRun ? "DRY RUN (no changes)" : "LIVE") . "\n\n";

try {
    // Check if archive tables exist
    $checkTable = $conn->query("SHOW TABLES LIKE 'bookings_archive'");
    if ($checkTable->num_rows === 0) {
        echo "ERROR: Archive tables not found. Please run archive_schema.sql first.\n";
        exit(1);
    }
    
    // Find bookings to archive
    // Only archive completed or cancelled bookings older than 2 years
    $findQuery = "
        SELECT 
            b.bookingID,
            b.BookingReference,
            b.EventDate,
            b.BookingStatus,
            COUNT(bl.logID) as log_count
        FROM bookings b
        LEFT JOIN booking_logs bl ON b.bookingID = bl.bookingID
        WHERE b.EventDate < ? 
        AND b.BookingStatus IN ('Completed', 'Cancelled')
        GROUP BY b.bookingID
        ORDER BY b.EventDate ASC
    ";
    
    $stmt = $conn->prepare($findQuery);
    $stmt->bind_param("s", $archiveDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookingsToArchive = [];
    while ($row = $result->fetch_assoc()) {
        $bookingsToArchive[] = $row;
    }
    
    $totalBookings = count($bookingsToArchive);
    
    if ($totalBookings === 0) {
        echo "No bookings found to archive.\n";
        Logger::info('Archival completed', ['bookings_archived' => 0]);
        exit(0);
    }
    
    echo "Found $totalBookings booking(s) to archive:\n\n";
    
    $totalLogs = 0;
    foreach ($bookingsToArchive as $booking) {
        echo sprintf(
            "  - Booking #%s (%s) - %s - %d logs\n",
            $booking['bookingID'],
            $booking['BookingReference'],
            $booking['EventDate'],
            $booking['log_count']
        );
        $totalLogs += $booking['log_count'];
    }
    
    echo "\nTotal: $totalBookings bookings, $totalLogs logs\n\n";
    
    if ($dryRun) {
        echo "DRY RUN MODE: No changes made.\n";
        exit(0);
    }
    
    // Confirm before proceeding
    if (php_sapi_name() === 'cli') {
        echo "Proceed with archival? (yes/no): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        if (trim(strtolower($line)) !== 'yes') {
            echo "Archival cancelled.\n";
            exit(0);
        }
    }
    
    echo "\nStarting archival...\n";
    
    $conn->begin_transaction();
    
    $archivedBookings = 0;
    $archivedLogs = 0;
    
    foreach ($bookingsToArchive as $booking) {
        $bookingID = $booking['bookingID'];
        
        // Copy booking to archive
        $archiveBookingQuery = "
            INSERT INTO bookings_archive 
            SELECT *, NOW() as archivedAt 
            FROM bookings 
            WHERE bookingID = ?
        ";
        $stmt = $conn->prepare($archiveBookingQuery);
        $stmt->bind_param("i", $bookingID);
        $stmt->execute();
        
        // Copy logs to archive
        $archiveLogsQuery = "
            INSERT INTO booking_logs_archive 
            SELECT *, NOW() as archivedAt 
            FROM booking_logs 
            WHERE bookingID = ?
        ";
        $stmt = $conn->prepare($archiveLogsQuery);
        $stmt->bind_param("i", $bookingID);
        $stmt->execute();
        $logsArchived = $stmt->affected_rows;
        
        // Delete logs from main table
        $deleteLogsQuery = "DELETE FROM booking_logs WHERE bookingID = ?";
        $stmt = $conn->prepare($deleteLogsQuery);
        $stmt->bind_param("i", $bookingID);
        $stmt->execute();
        
        // Delete booking from main table
        $deleteBookingQuery = "DELETE FROM bookings WHERE bookingID = ?";
        $stmt = $conn->prepare($deleteBookingQuery);
        $stmt->bind_param("i", $bookingID);
        $stmt->execute();
        
        $archivedBookings++;
        $archivedLogs += $logsArchived;
        
        echo ".";
        if ($archivedBookings % 10 === 0) {
            echo " $archivedBookings\n";
        }
    }
    
    $conn->commit();
    
    echo "\n\n=== Archival Complete ===\n";
    echo "Bookings archived: $archivedBookings\n";
    echo "Logs archived: $archivedLogs\n";
    
    Logger::info('Booking archival completed', [
        'bookings_archived' => $archivedBookings,
        'logs_archived' => $archivedLogs,
        'archive_date_threshold' => $archiveDate
    ]);
    
} catch (Exception $e) {
    if (isset($conn) && $conn->connect_errno === 0) {
        $conn->rollback();
    }
    
    echo "\nERROR: " . $e->getMessage() . "\n";
    Logger::error('Archival failed', ['error' => $e->getMessage()]);
    exit(1);
}
?>
