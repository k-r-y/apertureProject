<?php
require_once __DIR__ . '/../includes/functions/config.php';

echo "Starting duplicate photo cleanup...\n";

// Find duplicates based on userID and originalName
$sql = "
    SELECT userID, originalName, COUNT(*) as count
    FROM user_photos
    GROUP BY userID, originalName
    HAVING count > 1
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Found " . $result->num_rows . " sets of duplicates.\n";
    
    while ($row = $result->fetch_assoc()) {
        $userID = $row['userID'];
        $originalName = $row['originalName'];
        
        // Get all IDs for this duplicate set, ordered by uploadDate DESC (keep newest)
        $dupSql = "SELECT photoID FROM user_photos WHERE userID = ? AND originalName = ? ORDER BY uploadDate DESC";
        $dupStmt = $conn->prepare($dupSql);
        $dupStmt->bind_param("is", $userID, $originalName);
        $dupStmt->execute();
        $dupResult = $dupStmt->get_result();
        
        $ids = [];
        while ($dupRow = $dupResult->fetch_assoc()) {
            $ids[] = $dupRow['photoID'];
        }
        
        // Keep the first one (newest), delete the rest
        $keepID = array_shift($ids);
        
        foreach ($ids as $deleteID) {
            $delStmt = $conn->prepare("DELETE FROM user_photos WHERE photoID = ?");
            $delStmt->bind_param("i", $deleteID);
            if ($delStmt->execute()) {
                echo "Deleted duplicate photo ID: $deleteID (User: $userID, File: $originalName)\n";
            } else {
                echo "Failed to delete photo ID: $deleteID\n";
            }
        }
    }
} else {
    echo "No duplicates found.\n";
}

echo "Cleanup complete.\n";
?>
