<?php
require_once '../../includes/functions/config.php';
require_once '../../includes/functions/session.php';

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$bookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verify booking belongs to user or user is admin
$stmt = $conn->prepare("
    SELECT b.*, u.FirstName, u.LastName, u.Email, u.contactNo, p.packageName, p.Description as packageDesc
    FROM bookings b
    JOIN users u ON b.userID = u.userID
    JOIN packages p ON b.packageID = p.packageID
    WHERE b.bookingID = ? AND (b.userID = ? OR ? = 'Admin')
");
$stmt->bind_param("iis", $bookingId, $_SESSION['userId'], $_SESSION['role']);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    exit('Booking not found');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $booking['bookingRef'] ?></title>
    <style>
        @media print {
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .invoice {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            border-bottom: 3px solid #D4AF37;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #D4AF37;
        }
        .company-info {
            text-align: right;
            color: #666;
        }
        .invoice-title {
            font-size: 24px;
            color: #333;
            margin: 20px 0;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 30px 0;
        }
        .detail-section h3 {
            color: #D4AF37;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
        }
        .detail-section p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        th {
            background: #D4AF37;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        .total-row {
            font-weight: bold;
            font-size: 18px;
            background: #f9f9f9;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
        .print-btn {
            background: #D4AF37;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 20px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-confirmed { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-completed { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Print / Save as PDF</button>
    
    <div class="invoice">
        <div class="header">
            <table style="border: none; margin: 0;">
                <tr>
                    <td style="border: none; padding: 0;">
                        <div class="logo">Aperture Photography</div>
                        <p style="color: #666; margin: 5px 0;">Professional Photography Services</p>
                    </td>
                    <td style="border: none; padding: 0; text-align: right;">
                        <div class="company-info">
                            <p><strong>Aperture Studios</strong></p>
                            <p>Email: contact@aperture.com</p>
              <p>Phone: (123) 456-7890</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="invoice-title">
            INVOICE
            <span style="float: right; font-size: 16px; color: #666;">#<?= htmlspecialchars($booking['bookingRef']) ?></span>
        </div>

        <div class="details-grid">
            <div class="detail-section">
                <h3>Billed To</h3>
                <p><strong><?= htmlspecialchars($booking['FirstName'] . ' ' . $booking['LastName']) ?></strong></p>
                <p><?= htmlspecialchars($booking['Email']) ?></p>
                <p><?= htmlspecialchars($booking['contactNo']) ?></p>
            </div>
            <div class="detail-section" style="text-align: right;">
                <p><strong>Invoice Date:</strong> <?= date('M d, Y') ?></p>
                <p><strong>Event Date:</strong> <?= date('M d, Y', strtotime($booking['event_date'])) ?></p>
                <p><strong>Status:</strong> <span class="status-badge status-<?= $booking['booking_status'] ?>"><?= ucfirst(str_replace('_', ' ', $booking['booking_status'])) ?></span></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($booking['packageName']) ?></strong><br>
                        <small style="color: #666;"><?= htmlspecialchars($booking['event_type']) ?> - <?= date('M d, Y', strtotime($booking['event_date'])) ?></small>
                    </td>
                    <td style="text-align: right;">₱<?= number_format($booking['total_amount'], 2) ?></td>
                </tr>
                <tr>
                    <td><strong>Downpayment Paid</strong></td>
                    <td style="text-align: right;">- ₱<?= number_format($booking['downpayment_amount'], 2) ?></td>
                </tr>
                <tr class="total-row">
                    <td>BALANCE DUE</td>
                    <td style="text-align: right;">₱<?= number_format($booking['total_amount'] - $booking['downpayment_amount'], 2) ?></td>
                </tr>
            </tbody>
        </table>

        <?php if ($booking['admin_notes']): ?>
        <div style="background: #f9f9f9; padding: 15px; border-left: 3px solid #D4AF37; margin: 20px 0;">
            <strong>Notes:</strong><br>
            <?= nl2br(htmlspecialchars($booking['admin_notes'])) ?>
        </div>
        <?php endif; ?>

        <div class="footer">
            <p>Thank you for choosing Aperture Photography!</p>
            <p>This is a computer-generated invoice. No signature required.</p>
        </div>
    </div>
</body>
</html>
