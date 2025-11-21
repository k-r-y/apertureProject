<?php
// bookingForm.php - Luxury Dark Theme Booking Form

require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/auth.php';

// Session validation
if (!isset($_SESSION["userId"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "User") {
    header("Location: ../logIn.php");
    exit;
}
if (!isset($_SESSION["isVerified"]) || !$_SESSION["isVerified"]) {
     header("Location: ../logIn.php");
     exit;
}

// Fetch packages
$query = ("SELECT * FROM packages");
$result = $conn->query($query);

$packages = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
}

$minBookingDate = date('Y-m-d', strtotime('+5 days'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking - Aperture</title>
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="admin-dashboard dark-luxury-theme">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content luxury-booking-bg">
            <div class="container-fluid px-3 px-lg-5 py-5">
                
                <!-- Page Header -->
                <div class="text-center mb-5">
                    <h1 class="luxury-title mb-2">Create Your Booking</h1>
                    <p class="text-muted-luxury">Experience premium photography services</p>
                </div>

                <form action="processBooking.php" method="POST" enctype="multipart/form-data" id="bookingForm">
                    <div class="row g-4">
                        
                        <!-- Left Column: Form -->
                        <div class="col-lg-8">
                            
                            <!-- Client Information -->
                            <div class="glass-card mb-4">
                                <div class="glass-card-header">
                                    <i class="bi bi-person-circle me-2"></i>
                                    <span>Client Information</span>
                                </div>
                                <div class="glass-card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="luxury-label">First Name</label>
                                            <input type="text" name="fname" class="luxury-input" value="<?= htmlspecialchars($_SESSION["firstName"] ?? '') ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="luxury-label">Last Name</label>
                                            <input type="text" name="lname" class="luxury-input" value="<?= htmlspecialchars($_SESSION["lastName"] ?? '') ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="luxury-label">Email Address</label>
                                            <input type="email" name="email" class="luxury-input" value="<?= htmlspecialchars($_SESSION["email"] ?? '') ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="luxury-label">Contact Number</label>
                                            <input type="text" name="phone" class="luxury-input" value="<?= htmlspecialchars($_SESSION['contact'] ?? ''); ?>" readonly>
                                        </div>
                                        <div class="col-12">
                                            <div class="luxury-alert">
                                            <i class="bi bi-info-circle me-2 text-light"></i>
                                            <span class="text-light">To edit this, go to profile settings</span>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Event Details -->
                            <div class="glass-card mb-4">
                                <div class="glass-card-header">
                                    <i class="bi bi-calendar-event me-2"></i>
                                    <span>Event Details</span>
                                </div>
                                <div class="glass-card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="luxury-label">Event Date <span class="text-gold">*</span></label>
                                            <input type="date" name="eventDate" id="eventDate" class="luxury-input" min="<?= $minBookingDate ?>" required>
                                            <div id="date-availability-feedback" class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="luxury-label">Event Type <span class="text-gold">*</span></label>
                                            <select name="eventType" class="luxury-input" required>
                                                <option value="" selected>Select event type</option>
                                                <option value="Wedding">Wedding</option>
                                                <option value="Birthday">Birthday</option>
                                                <option value="Corporate">Corporate Event</option>
                                                <option value="Portrait">Portrait Session</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="luxury-label">Start Time <span class="text-gold">*</span></label>
                                            <input type="time" name="startTime" class="luxury-input" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="luxury-label">End Time <span class="text-gold">*</span></label>
                                            <input type="time" name="endTime" class="luxury-input" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="luxury-label">Event Location <span class="text-gold">*</span></label>
                                            <input type="text" name="location" class="luxury-input" placeholder="Full address" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="luxury-label">Landmark (Optional)</label>
                                            <input type="text" name="landmark" class="luxury-input" placeholder="Nearby landmark for easier navigation">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Package Selection -->
                            <div class="glass-card mb-4">
                                <div class="glass-card-header">
                                    <i class="bi bi-box-seam me-2"></i>
                                    <span>Select Your Package</span>
                                </div>
                                <div class="glass-card-body">
                                    <div class="row g-3">
                                        <?php foreach ($packages as $pkg): ?>
                                        <div class="col-12">
                                            <input type="radio" name="packageID" id="luxury-pkg-<?= $pkg['packageID'] ?>" value="<?= $pkg['packageID'] ?>" class="luxury-radio" required>
                                            <label for="luxury-pkg-<?= $pkg['packageID'] ?>" class="luxury-package-card" data-price="<?= $pkg['Price'] ?>" data-name="<?= htmlspecialchars($pkg['packageName']) ?>">
                                                <div class="package-content">
                                                    <div class="package-info">
                                                        <h5 class="package-name text-light"><?= htmlspecialchars($pkg['packageName']) ?></h5>
                                                        <p class="package-desc"><?= htmlspecialchars($pkg['description']) ?></p>
                                                    </div>
                                                    <div class="package-price">
                                                        <span class="price-amount">₱<?= number_format($pkg['Price']) ?></span>
                                                    </div>
                                                </div>
                                                <div class="package-check"><i class="bi bi-check-circle-fill"></i></div>
                                            </label>
                                            <div id="details-<?= $pkg['packageID'] ?>" class="package-details-luxury" style="display: none;"></div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="glass-card mb-4">
                                <div class="glass-card-header">
                                    <i class="bi bi-credit-card me-2"></i>
                                    <span>Payment Method</span>
                                </div>
                                <div class="glass-card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6 col-lg-3">
                                            <input type="radio" name="paymentMethod" id="luxury-pm-gcash" value="GCash" class="luxury-radio" required>
                                            <label for="luxury-pm-gcash" class="luxury-payment-card">
                                                <i class="bi bi-phone payment-icon"></i>
                                                <span class="payment-name">GCash</span>
                                            </label>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <input type="radio" name="paymentMethod" id="luxury-pm-paymaya" value="PayMaya" class="luxury-radio">
                                            <label for="luxury-pm-paymaya" class="luxury-payment-card">
                                                <i class="bi bi-wallet2 payment-icon"></i>
                                                <span class="payment-name">PayMaya</span>
                                            </label>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <input type="radio" name="paymentMethod" id="luxury-pm-bank" value="Bank Transfer" class="luxury-radio">
                                            <label for="luxury-pm-bank" class="luxury-payment-card">
                                                <i class="bi bi-bank payment-icon"></i>
                                                <span class="payment-name">Bank</span>
                                            </label>
                                        </div>
                                        <div class="col-md-6 col-lg-3">
                                            <input type="radio" name="paymentMethod" id="luxury-pm-cash" value="Cash" class="luxury-radio">
                                            <label for="luxury-pm-cash" class="luxury-payment-card">
                                                <i class="bi bi-cash payment-icon"></i>
                                                <span class="payment-name">Cash</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Payment Details -->
                                    <div id="paymentDetails" class="mt-4" style="display: none;">
                                        <div class="payment-info-box">
                                            <div id="paymentInfo"></div>
                                        </div>
                                    </div>

                                    <!-- Proof Upload -->
                                    <div id="proofUploadSection" class="mt-4" style="display: none;">
                                        <label class="luxury-label">Proof of Payment <span class="text-gold">*</span></label>
                                        <div class="file-upload-luxury">
                                            <input type="file" name="paymentProof" id="paymentProof" class="file-input-luxury" accept="image/*,.pdf">
                                            <label for="paymentProof" class="file-label-luxury">
                                                <i class="bi bi-cloud-upload me-2"></i>
                                                <span>Choose file or drag here</span>
                                            </label>
                                        </div>
                                        <small class="text-muted-luxury d-block mt-2">Max 5MB • JPG, PNG, or PDF</small>
                                        <div id="proofPreview" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Special Requests -->
                            <div class="glass-card mb-4">
                                <div class="glass-card-header">
                                    <i class="bi bi-chat-left-text me-2"></i>
                                    <span>Special Requests</span>
                                </div>
                                <div class="glass-card-body">
                                    <label class="luxury-label">Additional Notes (Optional)</label>
                                    <textarea name="specialRequests" class="luxury-textarea" rows="4" placeholder="Any special requirements or requests..."></textarea>
                                </div>
                            </div>

                            <!-- Terms -->
                            <div class="luxury-checkbox-wrapper mb-4">
                                <input type="checkbox" id="termsConfirm" class="luxury-checkbox" required>
                                <label for="termsConfirm" class="luxury-checkbox-label">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="text-gold text-decoration-none">Terms and Conditions</a>
                                </label>
                            </div>

                        </div>

                        <!-- Right Column: Real-time Summary -->
                        <div class="col-lg-4">
                            <div class="sticky-luxury-summary">
                                <div class="glass-card summary-card">
                                    <div class="glass-card-header">
                                        <i class="bi bi-receipt me-2"></i>
                                        <span>Booking Summary</span>
                                    </div>
                                    <div class="glass-card-body">
                                        
                                        <!-- User Information -->
                                        <div class="summary-section">
                                            <div class="summary-label"><i class="bi bi-person me-1"></i> Client Information</div>
                                            <div id="summary-client-name" class="summary-value text-truncate">-</div>
                                            <div id="summary-client-email" class="summary-detail">-</div>
                                            <div id="summary-client-phone" class="summary-detail">-</div>
                                        </div>

                                        <div class="summary-divider"></div>

                                        <!-- Event Details -->
                                        <div class="summary-section">
                                            <div class="summary-label"><i class="bi bi-calendar-event me-1"></i> Event Details</div>
                                            <div id="summary-event-date" class="summary-value">-</div>
                                            <div id="summary-event-time" class="summary-detail">-</div>
                                            <div id="summary-event-venue" class="summary-detail text-truncate">-</div>
                                        </div>

                                        <div class="summary-divider"></div>

                                        <!-- Package Selection -->
                                        <div class="summary-section">
                                            <div class="summary-label"><i class="bi bi-box-seam me-1"></i> Package</div>
                                            <div id="summary-package-name" class="summary-value">Not selected</div>
                                        </div>

                                        <!-- Add-ons -->
                                        <div id="summary-addons-container" class="summary-section" style="display: none;">
                                            <div class="summary-label"><i class="bi bi-plus-circle me-1"></i> Add-ons</div>
                                            <div id="summary-addons-list" class="summary-addons"></div>
                                        </div>

                                        <div class="summary-divider"></div>

                                        <!-- Payment Method -->
                                        <div id="summary-payment-container" class="summary-section" style="display: none;">
                                            <div class="summary-label"><i class="bi bi-credit-card me-1"></i> Payment Method</div>
                                            <div id="summary-payment-method" class="summary-value">-</div>
                                        </div>

                                        <!-- Special Requests -->
                                        <div id="summary-requests-container" class="summary-section" style="display: none;">
                                            <div class="summary-label"><i class="bi bi-chat-left-text me-1"></i> Special Requests</div>
                                            <div id="summary-special-requests" class="summary-detail">-</div>
                                        </div>

                                        <div class="summary-divider"></div>

                                        <!-- Pricing -->
                                        <div class="summary-pricing">
                                            <div class="pricing-row">
                                                <span>Subtotal</span>
                                                <span id="totalPrice" class="price-value">₱0</span>
                                            </div>
                                            <div class="pricing-row downpayment-row">
                                                <span>Downpayment (25%)</span>
                                                <span id="downpaymentAmount" class="price-value">₱0</span>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <button type="submit" class="luxury-submit-btn">
                                            <span>Confirm Booking</span>
                                            <i class="bi bi-arrow-right ms-2"></i>
                                        </button>

                                        <!-- Info Alert -->
                                        <div class="luxury-alert">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <span>25% downpayment required to secure your booking</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </main>
    </div>

    <?php include "../includes/modals/terms.php" ?>
    <?php include "../includes/modals/privacy.php" ?>
    <?php include "../includes/modals/refund.php" ?>
    <script src="user.js"></script>
    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
