<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/csrf.php';

// Check if user is logged in
if (!isset($_SESSION["userId"])) {
    header("Location: ../logIn.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!validateCSRFToken($_POST['csrfToken'] ?? '')) {
        handleCSRFFailure('bookingForm.php');
    }
    try {
        $userId = $_SESSION["userId"];
        
        require_once '../includes/functions/booking_logic.php';

        // Store form data in session for error recovery
        $_SESSION['booking_form_data'] = $_POST;

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
        
        // Validate Date (3-5 days in advance)
        validateBookingDate($eventDate);
        
        // Validate Time (7am - 10pm)
        validateBookingTime($startTime, $endTime);

        // Server-side Availability Check
        // Check if date is fully booked (Limit: 1 booking per day)
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE event_date = ? AND booking_status IN ('confirmed', 'pending')");
        $checkStmt->bind_param("s", $eventDate);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $checkRow = $checkResult->fetch_assoc();
        $checkStmt->close();
        
        if ($checkRow['count'] >= 1) {
            throw new Exception("Selected date is fully booked. Please choose another date.");
        }

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
        $items = []; // For email
        
        // Fetch Package Price & Details
        $pkg = getPackageData($conn, $packageId);
        
        $totalPrice += floatval($pkg['Price']);
        $packageName = $pkg['packageName'];
        $items[] = ['name' => $packageName, 'price' => '₱' . number_format($pkg['Price'], 2)];
        
        // Calculate Extra Hours Cost
        $extraCost = calculateExtraHoursCost($startTime, $endTime, $pkg['coverage_hours'], $pkg['extra_hour_rate']);
        if ($extraCost > 0) {
            $totalPrice += $extraCost;
            $items[] = ['name' => 'Extra Hours Charge', 'price' => '₱' . number_format($extraCost, 2)];
        }

        // Fetch Add-on Prices
        if (!empty($addons)) {
            $placeholders = implode(',', array_fill(0, count($addons), '?'));
            $types = str_repeat('s', count($addons));
            
            $query = "SELECT Description, Price FROM addons WHERE addID IN ($placeholders)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$addons);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $totalPrice += floatval($row['Price']);
                $items[] = ['name' => $row['Description'], 'price' => '₱' . number_format($row['Price'], 2)];
            }
            $stmt->close();
        }

        $downpayment = $totalPrice * 0.25;
        $balance = $totalPrice - $downpayment;

        // 3. Handle File Upload (Payment Proof) - ENHANCED SECURITY
        $referenceFilePath = null;
        if ($paymentMethod !== 'Cash' && isset($_FILES['paymentProof']) && $_FILES['paymentProof']['error'] !== UPLOAD_ERR_NO_FILE) {
            require_once '../includes/functions/validation.php';
            
            // Validate file upload
            $fileValidation = validateFileUpload(
                $_FILES['paymentProof'],
                ['image/jpeg', 'image/png', 'application/pdf'],
                5242880 // 5MB in bytes
            );
            
            if (!$fileValidation['valid']) {
                throw new Exception($fileValidation['error']);
            }
            
            // Create upload directory with proper permissions
            $uploadDir = '../uploads/payment_proofs/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true); // More secure than 0777
            }
            
            // Generate safe filename
            $safeFileName = generateSafeFilename($_FILES['paymentProof']['name']);
            $targetPath = $uploadDir . $safeFileName;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['paymentProof']['tmp_name'], $targetPath)) {
                $referenceFilePath = $targetPath;
                
                // Set proper file permissions
                chmod($targetPath, 0644);
            } else {
                throw new Exception("Failed to upload payment proof. Please try again.");
            }
        }

        // 4. Insert into Database - UPDATED TO MATCH ACTUAL SCHEMA
        $query = "INSERT INTO bookings (
            userID, 
            packageID,
            event_type, 
            event_date, 
            event_time_start, 
            event_time_end, 
            event_location,
            client_message, 
            proof_payment, 
            total_amount, 
            downpayment_amount, 
            booking_status, 
            is_fully_paid
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 0)";

        $stmt = $conn->prepare($query);
        
        $eventLocation = $location . ($landmark ? " (Near: $landmark)" : "");
        $clientMessage = $specialRequests;

        $stmt->bind_param(
            "issssssssdd", 
            $userId,
            $packageId,
            $eventType,
            $eventDate,
            $startTime,
            $endTime,
            $eventLocation,
            $clientMessage,
            $referenceFilePath,
            $totalPrice,
            $downpayment
        );

        if ($stmt->execute()) {
            $bookingId = $stmt->insert_id;
            
            // Send Confirmation Email
            $userEmail = $_SESSION['email'] ?? ''; // Assuming email is in session, otherwise fetch from DB
            if ($userEmail) {
                $bookingDetails = [
                    'name' => $_SESSION['firstName'] . ' ' . $_SESSION['lastName'],
                    'date' => date('F j, Y', strtotime($eventDate)),
                    'time' => date('g:i A', strtotime($startTime)) . ' - ' . date('g:i A', strtotime($endTime)),
                    'location' => $location,
                    'ref' => str_pad($bookingId, 6, '0', STR_PAD_LEFT),
                    'items' => $items,
                    'total' => '₱' . number_format($totalPrice, 2),
                    'downpayment' => '₱' . number_format($downpayment, 2)
                ];
                sendBookingConfirmationEmail($userEmail, $bookingDetails);
            }

            // Clear saved form data on success
            unset($_SESSION['booking_form_data']);
            
            // Return JSON response for AJAX
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Your booking has been submitted successfully!',
                'bookingRef' => str_pad($bookingId, 6, '0', STR_PAD_LEFT)
            ]);
            exit;
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }

    } catch (Exception $e) {
        // Log error for debugging
        error_log("Booking Error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        // Return JSON error response
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
} else {
    // Invalid request method
    header("Location: bookingForm.php");
    exit;
}
?>
