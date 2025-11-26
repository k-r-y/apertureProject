<?php
require_once 'includes/functions/config.php';

// Check if table exists
$tableExists = $conn->query("SHOW TABLES LIKE 'invoices'")->num_rows > 0;

if (!$tableExists) {
    echo "Table 'invoices' does NOT exist.\n";
    // Create it if needed?
} else {
    echo "Table 'invoices' exists.\n";
    
    // Count rows
    $count = $conn->query("SELECT COUNT(*) as count FROM invoices")->fetch_assoc()['count'];
    echo "Total invoices: $count\n";
    
    if ($count > 0) {
        $rows = $conn->query("SELECT * FROM invoices LIMIT 5")->fetch_all(MYSQLI_ASSOC);
        print_r($rows);
    }
}

// Check for eligible bookings for invoice creation
$sql = "
    SELECT b.bookingID, b.booking_status
    FROM bookings b
    LEFT JOIN invoices i ON b.bookingID = i.bookingID
    WHERE i.invoiceID IS NULL AND b.booking_status != 'cancelled'
";
$eligible = $conn->query($sql)->num_rows;
echo "Eligible bookings for new invoice: $eligible\n";
?>
