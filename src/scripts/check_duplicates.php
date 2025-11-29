<?php
require_once __DIR__ . '/../includes/functions/config.php';

echo "Checking for duplicates...\n";

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
        echo "User: " . $row['userID'] . ", File: " . $row['originalName'] . ", Count: " . $row['count'] . "\n";
    }
} else {
    echo "No duplicates found.\n";
}
?>
