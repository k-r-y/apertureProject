<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/auth.php';
require_once '../../includes/functions/session.php';

// Check admin access
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'Admin') {
    die("Unauthorized Access");
}

$bookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($bookingId === 0) {
    die("Invalid Booking ID");
}

// Fetch Booking Details
$stmt = $conn->prepare("
    SELECT b.*, u.FirstName, u.LastName, u.Email, u.contactNo, u.Address, p.packageName, p.Price as packagePrice
    FROM bookings b 
    JOIN users u ON b.userID = u.userID 
    JOIN packages p ON b.packageID = p.packageID 
    WHERE b.bookingID = ?
");
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    die("Booking not found");
}

// Fetch Addons
$addons = [];
try {
    $stmt = $conn->prepare("
        SELECT a.name, a.price 
        FROM booking_addons ba 
        JOIN addons a ON ba.addonID = a.addonID 
        WHERE ba.bookingID = ?
    ");
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $addons[] = $row;
    }
} catch (Exception $e) {
    // Ignore if table doesn't exist
}

// Fetch Invoice Details
$stmt = $conn->prepare("SELECT * FROM invoices WHERE bookingID = ?");
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();

$invoiceNumber = $invoice ? 'INV-' . str_pad($invoice['invoiceID'], 5, '0', STR_PAD_LEFT) : 'PREVIEW';
$issueDate = $invoice ? date('F d, Y', strtotime($invoice['issue_date'])) : date('F d, Y');
$dueDate = $invoice ? date('F d, Y', strtotime($invoice['due_date'])) : 'N/A';
$status = $invoice ? strtoupper($invoice['status']) : 'DRAFT';

// Calculate Totals
$subtotal = $booking['packagePrice'];
foreach ($addons as $addon) {
    $subtotal += $addon['price'];
}
$total = $booking['total_amount']; // Should match subtotal
$downpayment = $booking['downpayment_amount'];

// Calculate balance based on payment status
if ($booking['final_payment_paid'] == 1 || $booking['is_fully_paid'] == 1) {
    $balance = 0; // Final payment confirmed, balance is 0
} else {
    $balance = $total - $downpayment; // Only downpayment paid, show remaining balance
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $bookingId ?> - Aperture</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #D4AF37;
            --dark: #1a1a1a;
            --light: #f8f9fa;
            --text: #333;
        }
        body {
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: #fff;
            margin: 0;
            padding: 40px;
            font-size: 14px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #eee;
            padding: 40px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 50px;
        }
        .brand h1 {
            font-family: 'Playfair Display', serif;
            color: var(--gold);
            margin: 0;
            font-size: 32px;
        }
        .brand p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h2 {
            margin: 0;
            font-size: 24px;
            color: var(--dark);
        }
        .invoice-details p {
            margin: 5px 0 0;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            background: #eee;
            color: #555;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        .info-box h3 {
            font-size: 14px;
            text-transform: uppercase;
            color: #999;
            margin: 0 0 10px;
            letter-spacing: 1px;
        }
        .info-box p {
            margin: 0 0 5px;
            font-weight: 500;
        }
        .table-container {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            padding: 15px;
            background: #f8f9fa;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .text-end { text-align: right; }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .totals-row.final {
            border-bottom: none;
            border-top: 2px solid var(--gold);
            margin-top: 10px;
            padding-top: 15px;
            font-size: 18px;
            font-weight: 700;
            color: var(--gold);
        }
        .footer {
            margin-top: 60px;
            text-align: center;
            color: #999;
            font-size: 12px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--gold);
            color: #000;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .print-btn:hover {
            transform: translateY(-2px);
        }
        @media print {
            body { padding: 0; background: #fff; }
            .invoice-container { border: none; padding: 0; max-width: 100%; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()">Print Invoice</button>

    <div class="invoice-container">
        <div class="header">
            <div class="brand">
                <h1>APERTURE</h1>
                <p>Photography & Visual Arts</p>
                <p>Dasmariñas City, Cavite, Philippines</p>
                <p>contact@aperture.com</p>
            </div>
            <div class="invoice-details">
                <h2>INVOICE</h2>
                <p>#<?= $invoiceNumber ?></p>
                <p>Date: <?= $issueDate ?></p>
                <?php if($dueDate !== 'N/A'): ?>
                <p>Due Date: <?= $dueDate ?></p>
                <?php endif; ?>
                <span class="status-badge"><?= $status ?></span>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h3>Bill To</h3>
                <p><?= $booking['FirstName'] . ' ' . $booking['LastName'] ?></p>
                <p><?= $booking['Email'] ?></p>
                <p><?= $booking['contactNo'] ?></p>
                <?php if(!empty($booking['Address'])): ?>
                <p><?= $booking['Address'] ?></p>
                <?php endif; ?>
            </div>
            <div class="info-box">
                <h3>Event Details</h3>
                <p>Type: <?= $booking['event_type'] ?></p>
                <p>Date: <?= date('F d, Y', strtotime($booking['event_date'])) ?></p>
                <p>Location: <?= $booking['event_location'] ?></p>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong><?= $booking['packageName'] ?> Package</strong>
                            <br><small class="text-muted">Base package price</small>
                        </td>
                        <td class="text-end">₱<?= number_format($booking['packagePrice'], 2) ?></td>
                    </tr>
                    <?php foreach ($addons as $addon): ?>
                    <tr>
                        <td>
                            + <?= $addon['name'] ?>
                        </td>
                        <td class="text-end">₱<?= number_format($addon['price'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="totals">
            <div class="totals-row">
                <span>Subtotal</span>
                <span>₱<?= number_format($subtotal, 2) ?></span>
            </div>
            <div class="totals-row">
                <span>Downpayment <?= $booking['downpayment_paid'] == 1 ? '(Paid)' : '(Pending)' ?></span>
                <span>- ₱<?= number_format($downpayment, 2) ?></span>
            </div>
            <?php if ($booking['final_payment_paid'] == 1 || $booking['is_fully_paid'] == 1): ?>
            <div class="totals-row">
                <span>Final Payment (Paid)</span>
                <span>- ₱<?= number_format($total - $downpayment, 2) ?></span>
            </div>
            <?php endif; ?>
            <div class="totals-row final">
                <span><?= $balance == 0 ? 'Total Paid' : 'Balance Due' ?></span>
                <span>₱<?= number_format($balance, 2) ?></span>
            </div>
        </div>

        <div class="footer">
            <p>Thank you for choosing Aperture. Please settle the balance before the event date.</p>
            <p>&copy; <?= date('Y') ?> Aperture Photography. All rights reserved.</p>
        </div>
    </div>

    <script>
        // Auto-print if requested via query param, otherwise just show
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
