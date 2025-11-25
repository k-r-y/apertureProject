<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/api_security.php';

// Apply rate limiting
enforceRateLimit('/api/getPackageDetails', 300, 3600);

header('Content-Type: application/json');

if (!isset($_GET['packageId'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Package ID is required']);
    exit;
}

$packageId = $_GET['packageId'];

try {
    // Fetch Inclusions
    $stmt = $conn->prepare("SELECT Description FROM inclusion WHERE packageID = ?");
    $stmt->bind_param("s", $packageId); // Assuming packageID is string based on create_tables.sql ('basic', 'elite', etc.)
    $stmt->execute();
    $result = $stmt->get_result();
    
    $inclusions = [];
    while ($row = $result->fetch_assoc()) {
        $inclusions[] = $row['Description'];
    }
    $stmt->close();

    // Fetch Add-ons
    $stmt = $conn->prepare("SELECT addID, Description, Price FROM addons WHERE packageID = ?");
    $stmt->bind_param("s", $packageId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $addons = [];
    while ($row = $result->fetch_assoc()) {
        $addons[] = [
            'addonID' => $row['addID'],
            'Description' => $row['Description'],
            'Price' => $row['Price']
        ];
    }
    $stmt->close();

    echo json_encode([
        'inclusions' => $inclusions,
        'addons' => $addons
    ]);

} catch (Exception $e) {
    error_log("Package details error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error fetching details']);
}
?>