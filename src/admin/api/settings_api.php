<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';
require_once '../../includes/functions/rate_limit.php';

// Enforce Rate Limit
enforceRateLimit(60, 60);

// Ensure user is logged in and is an Admin
if (!isset($_SESSION['userId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_settings':
            $result = $conn->query("SELECT setting_key, setting_value FROM settings");
            $settings = [];
            while ($row = $result->fetch_assoc()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            echo json_encode(['success' => true, 'settings' => $settings]);
            break;

        case 'update_settings':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) throw new Exception("Invalid data");

            $conn->begin_transaction();
            
            $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            
            foreach ($data as $key => $value) {
                // Basic validation could go here
                $stmt->bind_param("ss", $key, $value);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update setting: $key");
                }
            }
            
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Settings updated successfully']);
            break;

        default:
            throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    if (isset($conn) && $conn->in_transaction) {
        $conn->rollback();
    }
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
