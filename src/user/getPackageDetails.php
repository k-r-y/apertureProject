<?php
// getPackageDetails.php
header('Content-Type: application/json');

// IMPORTANT: Adjust this path if your config file is in a different location.
// If this path is wrong, you will get the "Unexpected token '<'" error.
require_once '../includes/functions/config.php';

// 1. Change validation to accept a string, as packageID is varchar in your DB.
$packageId = filter_input(INPUT_GET, 'packageId', FILTER_SANITIZE_STRING);

if (!$packageId) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid Package ID']);
    exit;
}

$response = [
    'inclusions' => [],
    'addons' => []
];

// --- Fetch Inclusions ---
// 2. Update table name to 'inclusion' and column to 'Description' based on your schema.
$stmt_inclusions = $conn->prepare("SELECT Description FROM inclusion WHERE packageID = ?");
// Bind as a string ("s")
$stmt_inclusions->bind_param("s", $packageId);

if ($stmt_inclusions->execute()) {
    $result_inclusions = $stmt_inclusions->get_result();
    while ($row = $result_inclusions->fetch_assoc()) {
        $response['inclusions'][] = $row['Description'];
    }
} else {
    // Log error or handle appropriately
    error_log("Database error fetching inclusions: " . $stmt_inclusions->error);
}
$stmt_inclusions->close();


// --- Fetch Add-ons ---
// 3. Update query to select directly from 'addons' table based on your schema.
// Using 'addID' as ID and 'Description' as the name.
$stmt_addons = $conn->prepare("
    SELECT addID, Description, Price
    FROM addons
    WHERE packageID = ?
");
// Bind as a string ("s")
$stmt_addons->bind_param("s", $packageId);

if ($stmt_addons->execute()) {
    $result_addons = $stmt_addons->get_result();
    while ($row = $result_addons->fetch_assoc()) {
        $response['addons'][] = [
            'addID' => $row['addID'],       // Changed from addonID
            'Description' => $row['Description'], // Changed from name
            'Price' => $row['Price']        // Using capitalized 'Price' to match DB
        ];
    }
} else {
     // Log error or handle appropriately
     error_log("Database error fetching addons: " . $stmt_addons->error);
}
$stmt_addons->close();

// Final output
echo json_encode($response);

if ($conn) {
    $conn->close();
}
?>