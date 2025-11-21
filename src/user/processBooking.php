<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/auth.php';

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION["userId"])) {
    header("Location: ../logIn.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $userId = $_SESSION["userId"];
        
        // 1. Retrieve and Sanitize Inputs
        $eventDate = sanitizeInput($_POST['eventDate']);
        $eventType = sanitizeInput($_POST['eventType']);
        $startTime = sanitizeInput($_POST['startTime']);
        $endTime = sanitizeInput($_POST['endTime']);
        $location = sanitizeInput($_POST['location']);
        $landmark = isset($_POST['landmark']) ? sanitizeInput($_POST['landmark']) : '';
        $packageId = sanitizeInput($_POST['packageID']);
        $paymentMethod = sanitizeInput($_POST['paymentMethod']);
        $specialRequests = isset($_POST['specialRequests']) ? sanitizeInput($_POST['specialRequests']) : '';
        
        // Handle Add-ons (Array)
        $addons = [];
        if (isset($_POST['addons']) && is_array($_POST['addons'])) {
            foreach ($_POST['addons'] as $addon) {
                $addons[] = sanitizeInput($addon);
            }
        }
        $addonsJson = json_encode($addons);

        // 2. Calculate Pricing (Server-side validation)
        $totalPrice = 0;
        
        // Fetch Package Price
        $stmt = $conn->prepare("SELECT Price, packageName FROM packages WHERE packageID = ?");
        $stmt->bind_param("s", $packageId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Invalid package selected.");
        }
        
        $pkg = $result->fetch_assoc();
        $totalPrice += floatval($pkg['Price']);
        $packageName = $pkg['packageName']; // For reference if needed
        $stmt->close();

        // Fetch Add-on Prices
        if (!empty($addons)) {
            // Create a string of placeholders for the IN clause
            $placeholders = implode(',', array_fill(0, count($addons), '?'));
            $types = str_repeat('s', count($addons)); // Assuming addon IDs are strings/ints
            
            $query = "SELECT Price FROM addons WHERE addID IN ($placeholders)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$addons);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $totalPrice += floatval($row['Price']);
            }
            $stmt->close();
        }

        $downpayment = $totalPrice * 0.25;
        $balance = $totalPrice - $downpayment;

        // 3. Handle File Upload (Payment Proof)
        $referenceFilePath = null;
        if ($paymentMethod !== 'Cash' && isset($_FILES['paymentProof']) && $_FILES['paymentProof']['error'] == 0) {
            $uploadDir = '../uploads/payment_proofs/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $file = $_FILES['paymentProof'];
            $fileName = time() . '_' . basename($file['name']);
            $targetPath = $uploadDir . $fileName;
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception("Invalid file type. Only JPG, PNG, and PDF are allowed.");
            }

            if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
                throw new Exception("File size exceeds 5MB limit.");
            }

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $referenceFilePath = $targetPath;
            } else {
                throw new Exception("Failed to upload payment proof.");
            }
        }

        // 4. Insert into Database
        // Note: Using 'venue_address' for location and 'venue_name' for landmark/venue name combination or just location
        // Adjusting based on schema: venue_name, venue_address
        
        $query = "INSERT INTO bookings (
            userID, 
            event_type, 
            event_date, 
            event_time_start, 
            event_time_end, 
            venue_address, 
            venue_name,
            service_type, 
            package_type, 
            add_ons, 
            special_requirements, 
            reference_files, 
            total_price, 
            downpayment, 
            balance, 
            status, 
            payment_status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'Both', ?, ?, ?, ?, ?, ?, ?, 'Pending', 'Unpaid')";

        $stmt = $conn->prepare($query);
        
        // service_type is hardcoded to 'Both' for now as per form context, or could be derived
        // venue_name used for landmark if provided, else same as location or empty
        $venueName = $landmark ? $landmark : $location; 

        $stmt->bind_param(
            "isssssssssssddd", 
            $userId,
            $eventType,
            $eventDate,
            $startTime,
            $endTime,
            $location,
            $venueName,
            $packageId, // Storing packageID in package_type column for now
            $addonsJson,
            $specialRequests,
            $referenceFilePath,
            $totalPrice,
            $downpayment,
            $balance
        );

        if ($stmt->execute()) {
            // Success! Redirect to appointments or confirmation page
            header("Location: appointments.php?booking=success");
            exit;
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }

    } catch (Exception $e) {
        // Handle errors (redirect back with error message)
        $errorMsg = urlencode($e->getMessage());
        header("Location: bookingForm.php?error=" . $errorMsg);
        exit;
    }
} else {
    // Invalid request method
    header("Location: bookingForm.php");
    exit;
}
?>
