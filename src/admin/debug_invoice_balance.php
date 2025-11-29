<?php
/**
 * Debug Invoice Balance
 * Checks payment-related fields for a booking to debug balance calculation issues
 */

require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';

// Check admin access
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    die("Unauthorized Access");
}

$bookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($bookingId === 0) {
    die("Please provide a booking ID: ?id=123");
}

// Fetch booking payment details
$stmt = $conn->prepare("
    SELECT 
        bookingID,
        total_amount,
        downpayment_amount,
        downpayment_paid,
        downpayment_paid_date,
        final_payment_paid,
        final_payment_paid_date,
        is_fully_paid,
        booking_status,
        created_at
    FROM bookings 
    WHERE bookingID = ?
");
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    die("Booking not found");
}

// Calculate expected values
$balance = $booking['total_amount'] - $booking['downpayment_amount'];
$expectedBalance = ($booking['final_payment_paid'] == 1 || $booking['is_fully_paid'] == 1) ? 0 : $balance;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Invoice Balance - Booking #<?= $bookingId ?></title>
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <style>
        body { 
            background: #1a1a1a; 
            color: #f8f9fa; 
            padding: 40px; 
            font-family: 'Courier New', monospace;
        }
        .debug-card {
            background: #2a2a2a;
            border: 1px solid #D4AF37;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 20px;
        }
        .field-name {
            color: #D4AF37;
            font-weight: bold;
            display: inline-block;
            width: 250px;
        }
        .field-value {
            color: #fff;
        }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
        h1 { color: #D4AF37; margin-bottom: 30px; }
        h3 { color: #D4AF37; margin-top: 30px; margin-bottom: 15px; }
        .alert { background: #333; border-color: #D4AF37; color: #fff; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Invoice Balance Debug - Booking #<?= $bookingId ?></h1>
        
        <div class="debug-card">
            <h3>Payment Status Flags</h3>
            <div class="mb-2">
                <span class="field-name">downpayment_paid:</span>
                <span class="field-value <?= $booking['downpayment_paid'] == 1 ? 'status-ok' : 'status-error' ?>">
                    <?= $booking['downpayment_paid'] == 1 ? '✅ YES (1)' : '❌ NO (0)' ?>
                </span>
            </div>
            <div class="mb-2">
                <span class="field-name">downpayment_paid_date:</span>
                <span class="field-value"><?= $booking['downpayment_paid_date'] ?? 'NULL' ?></span>
            </div>
            <div class="mb-2">
                <span class="field-name">final_payment_paid:</span>
                <span class="field-value <?= $booking['final_payment_paid'] == 1 ? 'status-ok' : 'status-error' ?>">
                    <?= $booking['final_payment_paid'] == 1 ? '✅ YES (1)' : '❌ NO (0)' ?>
                </span>
            </div>
            <div class="mb-2">
                <span class="field-name">final_payment_paid_date:</span>
                <span class="field-value"><?= $booking['final_payment_paid_date'] ?? 'NULL' ?></span>
            </div>
            <div class="mb-2">
                <span class="field-name">is_fully_paid:</span>
                <span class="field-value <?= $booking['is_fully_paid'] == 1 ? 'status-ok' : 'status-error' ?>">
                    <?= $booking['is_fully_paid'] == 1 ? '✅ YES (1)' : '❌ NO (0)' ?>
                </span>
            </div>
        </div>

        <div class="debug-card">
            <h3>Financial Details</h3>
            <div class="mb-2">
                <span class="field-name">total_amount:</span>
                <span class="field-value">₱<?= number_format($booking['total_amount'], 2) ?></span>
            </div>
            <div class="mb-2">
                <span class="field-name">downpayment_amount:</span>
                <span class="field-value">₱<?= number_format($booking['downpayment_amount'], 2) ?></span>
            </div>
            <div class="mb-2">
                <span class="field-name">Remaining Balance:</span>
                <span class="field-value">₱<?= number_format($balance, 2) ?></span>
            </div>
        </div>

        <div class="debug-card">
            <h3>Invoice Balance Calculation</h3>
            <div class="mb-2">
                <span class="field-name">Expected Invoice Balance:</span>
                <span class="field-value">₱<?= number_format($expectedBalance, 2) ?></span>
            </div>
            <div class="mb-2">
                <span class="field-name">Logic Used:</span>
                <span class="field-value">
                    <?php if ($booking['final_payment_paid'] == 1 || $booking['is_fully_paid'] == 1): ?>
                        Balance = 0 (fully paid)
                    <?php else: ?>
                        Balance = Total - Downpayment (<?= number_format($booking['total_amount'], 2) ?> - <?= number_format($booking['downpayment_amount'], 2) ?>)
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <?php
        // Diagnostic
        $issues = [];
        
        if ($booking['is_fully_paid'] == 1 && $booking['downpayment_paid'] == 0) {
            $issues[] = "⚠️ is_fully_paid=1 but downpayment_paid=0 (inconsistent state)";
        }
        
        if ($booking['is_fully_paid'] == 1 && $booking['final_payment_paid'] == 0) {
            $issues[] = "⚠️ is_fully_paid=1 but final_payment_paid=0 (inconsistent state)";
        }
        
        if ($booking['final_payment_paid'] == 1 && $booking['downpayment_paid'] == 0) {
            $issues[] = "⚠️ final_payment_paid=1 but downpayment_paid=0 (final payment without downpayment)";
        }
        
        if (count($issues) > 0):
        ?>
        <div class="alert alert-warning">
            <h4>⚠️ Issues Detected</h4>
            <?php foreach ($issues as $issue): ?>
                <div><?= $issue ?></div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-success">
            <h4>✅ No Issues Detected</h4>
            <p>All payment flags are consistent.</p>
        </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="bookings.php" class="btn btn-outline-light">← Back to Bookings</a>
            <a href="api/generate_invoice.php?id=<?= $bookingId ?>" class="btn btn-warning ms-2" target="_blank">View Invoice</a>
        </div>
    </div>
</body>
</html>
