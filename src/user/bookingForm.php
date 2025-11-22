<?php
// bookingForm.php - Luxury Dark Theme Booking Form

require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/booking_logic.php';

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

// Capture booking status messages from SESSION ONLY (no URL redirects)
$bookingStatus = null;
$bookingMessage = null;

// Check session for messages
if (isset($_SESSION['booking_success'])) {
    $bookingStatus = 'success';
    $bookingMessage = $_SESSION['booking_success'];
    unset($_SESSION['booking_success']);
} elseif (isset($_SESSION['booking_error'])) {
    $bookingStatus = 'error';
    $bookingMessage = $_SESSION['booking_error'];
    unset($_SESSION['booking_error']);
}

// Retrieve saved form data if available (for error recovery)
$savedData = isset($_SESSION['booking_form_data']) ? $_SESSION['booking_form_data'] : [];
// Clear it after retrieving so it doesn't persist
if (!empty($savedData)) {
    unset($_SESSION['booking_form_data']);
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
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                                            <input type="date" name="eventDate" id="eventDate" class="luxury-input" min="<?= $minBookingDate ?>" value="<?= htmlspecialchars($savedData['eventDate'] ?? '') ?>" required>
                                            <div id="date-availability-feedback" class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="luxury-label">Event Type <span class="text-gold">*</span></label>
                                            <select name="eventType" class="luxury-input" required>
                                                <option value="">Select event type</option>
                                                <option value="Wedding" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Wedding') ? 'selected' : '' ?>>Wedding</option>
                                                <option value="Birthday" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Birthday') ? 'selected' : '' ?>>Birthday</option>
                                                <option value="Corporate" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Corporate') ? 'selected' : '' ?>>Corporate Event</option>
                                                <option value="Portrait" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Portrait') ? 'selected' : '' ?>>Portrait Session</option>
                                                <option value="Other" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Other') ? 'selected' : '' ?>>Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="luxury-label">Start Time <span class="text-gold">*</span></label>
                                            <input type="time" name="startTime" class="luxury-input" value="<?= htmlspecialchars($savedData['startTime'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="luxury-label">End Time <span class="text-gold">*</span></label>
                                            <input type="time" name="endTime" class="luxury-input" value="<?= htmlspecialchars($savedData['endTime'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="luxury-label">Event Location <span class="text-gold">*</span></label>
                                            <input type="text" name="location" class="luxury-input" placeholder="Full address" value="<?= htmlspecialchars($savedData['location'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="luxury-label">Landmark (Optional)</label>
                                            <input type="text" name="landmark" class="luxury-input" placeholder="Nearby landmark for easier navigation" value="<?= htmlspecialchars($savedData['landmark'] ?? '') ?>">
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
                                            <input type="radio" name="packageID" id="luxury-pkg-<?= $pkg['packageID'] ?>" value="<?= $pkg['packageID'] ?>" class="luxury-radio" <?= (isset($savedData['packageID']) && $savedData['packageID'] === $pkg['packageID']) ? 'checked' : '' ?> required>
                                            <label for="luxury-pkg-<?= $pkg['packageID'] ?>" class="luxury-package-card" 
                                                data-price="<?= $pkg['Price'] ?>" 
                                                data-name="<?= htmlspecialchars($pkg['packageName']) ?>"
                                                data-coverage-hours="<?= isset($pkg['coverage_hours']) ? $pkg['coverage_hours'] : 4 ?>"
                                                data-hourly-rate="<?= isset($pkg['extra_hour_rate']) ? $pkg['extra_hour_rate'] : 1000 ?>">
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
                                    <textarea name="specialRequests" class="luxury-textarea" rows="4" placeholder="Any special requirements or requests..."><?= htmlspecialchars($savedData['specialRequests'] ?? '') ?></textarea>
                                </div>
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
                                            <div id="extra-hours-row" class="pricing-row" style="display: none;">
                                                <span>
                                                    Extra Hours <small class="text-muted" id="extra-hours-detail"></small>
                                                </span>
                                                <span id="extraHoursPrice" class="price-value">₱0</span>
                                            </div>
                                            <div class="pricing-row">
                                                <span>Subtotal</span>
                                                <span id="totalPrice" class="price-value">₱0</span>
                                            </div>
                                            <div class="pricing-row downpayment-row">
                                                <span>Downpayment (25%)</span>
                                                <span id="downpaymentAmount" class="price-value">₱0</span>
                                            </div>
                                        </div>

                                        <!-- Terms & Conditions -->
                                        <div class="form-check mb-4 d-flex align-items-start justify-content-start gap-2">
                                            <input class="form-check-input luxury-checkbox border" type="checkbox" id="termsConfirm" required>
                                            <label class="form-check-label text-muted-luxury" for="termsConfirm" style="font-size: 0.85rem;">
                                                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="text-gold text-decoration-none">Terms of Service</a>, <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" class="text-gold text-decoration-none">Privacy Policy</a>, and <a href="#" data-bs-toggle="modal" data-bs-target="#refundModal" class="text-gold text-decoration-none">Refund Policy</a>
                                            </label>
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
    
    <!-- SweetAlert2 JS -->
    <!-- SweetAlert2 JS (Local) -->
    <script src="../libs/sweetalert2/sweetalert2.all.min.js"></script>
    
    <!-- Restore saved form state -->
    <?php if (!empty($savedData)): ?>
    <script>
        // Wait for booking.js to fully load before restoring state
        window.addEventListener('load', function() {
            // Longer delay to ensure all event listeners are attached
            setTimeout(function() {
                console.log('Restoring saved form state...');
                
                <?php if (isset($savedData['packageID'])): ?>
                // Trigger package selection if saved
                const savedPackageId = '<?= htmlspecialchars($savedData['packageID']) ?>';
                const savedPackage = document.querySelector(`input[name="packageID"][value="${savedPackageId}"]`);
                
                if (savedPackage) {
                    console.log('Found saved package:', savedPackageId);
                    savedPackage.checked = true;
                    
                    // Manually trigger package details fetch
                    const detailsContainer = document.getElementById(`details-${savedPackageId}`);
                    if (detailsContainer && typeof fetchAndDisplayPackageDetails === 'function') {
                        fetchAndDisplayPackageDetails(savedPackageId, detailsContainer);
                    }
                    
                    // Trigger change event to update summary
                    savedPackage.dispatchEvent(new Event('change', { bubbles: true }));
                    
                    console.log('Package selection restored and details loaded');
                } else {
                    console.error('Saved package not found:', savedPackageId);
                }
                <?php endif; ?>
                
                <?php if (isset($savedData['paymentMethod'])): ?>
                // Trigger payment method selection if saved
                const paymentMethod = document.querySelector('input[name="paymentMethod"][value="<?= htmlspecialchars($savedData['paymentMethod']) ?>"]');
                if (paymentMethod && !paymentMethod.checked) {
                    paymentMethod.checked = true;
                    paymentMethod.dispatchEvent(new Event('change', { bubbles: true }));
                    console.log('Payment method restored');
                }
                <?php endif; ?>

                <?php if (isset($savedData['addons']) && is_array($savedData['addons'])): ?>
                // Restore selected addons after package details are loaded
                setTimeout(function() {
                    console.log('Restoring addons...');
                    <?php foreach ($savedData['addons'] as $addonId): ?>
                    const addon_<?= $addonId ?> = document.querySelector('input[name="addons[]"][value="<?= htmlspecialchars($addonId) ?>"]');
                    if (addon_<?= $addonId ?>) {
                        addon_<?= $addonId ?>.checked = true;
                        addon_<?= $addonId ?>.dispatchEvent(new Event('change', { bubbles: true }));
                        console.log('Addon restored:', <?= $addonId ?>);
                    }
                    <?php endforeach; ?>
                }, 1500); // Wait longer for package details to fully load
                <?php endif; ?>

                // Trigger validation for saved date and times
                <?php if (isset($savedData['eventDate'])): ?>
                const eventDateInput = document.getElementById('eventDate');
                if (eventDateInput && eventDateInput.value) {
                    eventDateInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                <?php endif; ?>

                <?php if (isset($savedData['startTime'])): ?>
                const startTimeInput = document.querySelector('input[name="startTime"]');
                if (startTimeInput && startTimeInput.value) {
                    startTimeInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                <?php endif; ?>

                <?php if (isset($savedData['endTime'])): ?>
                const endTimeInput = document.querySelector('input[name="endTime"]');
                if (endTimeInput && endTimeInput.value) {
                    endTimeInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                <?php endif; ?>
                
                console.log('Form state restoration complete');
            }, 1000); // Increased from 300ms to 1000ms
        });
    </script>
    <?php endif; ?>
    
    <script src="booking.js"></script>
    <script src="user.js"></script>
    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Booking Status Notifications -->
    <?php if ($bookingStatus): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($bookingStatus === 'success'): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Booking Submitted!',
                    text: '<?= addslashes($bookingMessage) ?>',
                    confirmButtonText: 'View My Bookings',
                    showCancelButton: true,
                    cancelButtonText: 'Stay Here',
                    confirmButtonColor: '#d4af37',
                    cancelButtonColor: '#6c757d',
                    background: '#1a1a1a',
                    color: '#ffffff',
                    customClass: {
                        popup: 'luxury-swal-popup',
                        confirmButton: 'luxury-swal-confirm',
                        cancelButton: 'luxury-swal-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'appointments.php';
                    } else {
                        // Clear URL parameters
                        const url = new URL(window.location);
                        url.searchParams.delete('booking');
                        window.history.replaceState({}, '', url);
                    }
                });
            <?php elseif ($bookingStatus === 'error'): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Booking Failed',
                    text: '<?= addslashes($bookingMessage) ?>',
                    confirmButtonText: 'Try Again',
                    confirmButtonColor: '#d4af37',
                    background: '#1a1a1a',
                    color: '#ffffff',
                    customClass: {
                        popup: 'luxury-swal-popup',
                        confirmButton: 'luxury-swal-confirm'
                    }
                }).then(() => {
                    // Clear URL parameters
                    const url = new URL(window.location);
                    url.searchParams.delete('error');
                    window.history.replaceState({}, '', url);
                });
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>
    
    <!-- Custom SweetAlert Styles -->
    <style>
        .luxury-swal-popup {
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 12px;
        }
        .luxury-swal-confirm,
        .luxury-swal-cancel {
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .luxury-swal-confirm:hover {
            background-color: #c49b2e !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.4);
        }
        .luxury-swal-cancel:hover {
            background-color: #5a6268 !important;
        }
    </style>
</body>
</html>
