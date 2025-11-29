<?php
// cleanup_expired_photos.php
// Run this script via cron job or scheduled task daily.

require_once '../includes/functions/config.php';

echo "Starting photo cleanup process...\n";

// 1. Find expired photos
// Logic:
// - Join user_photos -> bookings -> packages
// - Check if uploadDate + access_duration_months < NOW()
// - Exclude bookings that have an 'Extended Access' addon

$sql = "
    SELECT 
        up.photoID, 
        up.userID, 
        up.fileName, 
        up.uploadDate,
        p.access_duration_months,
        b.bookingID
    FROM 
        user_photos up
    JOIN 
        bookings b ON up.bookingID = b.bookingID
    JOIN 
        packages p ON b.packageID = p.packageID
    WHERE 
        DATE_ADD(up.uploadDate, INTERVAL p.access_duration_months MONTH) < NOW()
        AND NOT EXISTS (
            SELECT 1 
            FROM booking_addons ba 
            JOIN addons a ON ba.addonID = a.addID 
            WHERE ba.bookingID = b.bookingID 
            AND a.Description LIKE '%Extended Access%'
        )
";

$result = $conn->query($sql);

if (!$result) {
    die("Error executing query: " . $conn->error . "\n");
}

$deletedCount = 0;
$failedCount = 0;

if ($result->num_rows > 0) {
    echo "Found " . $result->num_rows . " expired photos.\n";

    while ($row = $result->fetch_assoc()) {
        $photoID = $row['photoID'];
        $userID = $row['userID'];
        $fileName = $row['fileName'];
        $filePath = "../../uploads/user_photos/" . $userID . "/" . $fileName;

        // Delete from disk
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                // Delete from DB
                $delStmt = $conn->prepare("DELETE FROM user_photos WHERE photoID = ?");
                $delStmt->bind_param("i", $photoID);
                if ($delStmt->execute()) {
                    echo "Deleted photo ID $photoID (User $userID)\n";
                    $deletedCount++;
                } else {
                    echo "Failed to delete photo ID $photoID from DB: " . $conn->error . "\n";
                    $failedCount++;
                }
                $delStmt->close();
            } else {
                echo "Failed to delete file: $filePath\n";
                $failedCount++;
            }
        } else {
            // File doesn't exist, just delete from DB
            echo "File not found: $filePath. Deleting from DB only.\n";
            $delStmt = $conn->prepare("DELETE FROM user_photos WHERE photoID = ?");
            $delStmt->bind_param("i", $photoID);
            if ($delStmt->execute()) {
                $deletedCount++;
            }
            $delStmt->close();
        }
    }
} else {
    echo "No expired photos found.\n";
}

echo "Cleanup complete. Deleted: $deletedCount, Failed: $failedCount\n";
?>
