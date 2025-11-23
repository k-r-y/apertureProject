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
    <link rel="stylesheet" href="../luxuryDesignSystem.css">
    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid px-3 px-lg-5 py-5">
                
                <!-- Page Header -->
                <div class="text-center mb-5">
                    <h1 class="mb-2">Create Your Booking</h1>
                    <p class="text-muted">Experience premium photography services</p>
                </div>

                <form action="processBooking.php" method="POST" enctype="multipart/form-data" id="bookingForm">
                    <div class="row g-4">
                        
                        <!-- Left Column: Form -->
                        <div class="col-lg-8">
                            
                            <!-- Client Information -->
                            <div class="neo-card mb-4">
                                <div class="mb-4 pb-2 border-bottom border-secondary">
                                    <h4 class="m-0"><i class="bi bi-person-circle me-2 text-gold"></i>Client Information</h4>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">First Name</label>
                                        <input type="text" name="fname" class="neo-input" value="<?= htmlspecialchars($_SESSION["firstName"] ?? '') ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Last Name</label>
                                        <input type="text" name="lname" class="neo-input" value="<?= htmlspecialchars($_SESSION["lastName"] ?? '') ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Email Address</label>
                                        <input type="email" name="email" class="neo-input" value="<?= htmlspecialchars($_SESSION["email"] ?? '') ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Contact Number</label>
                                        <input type="text" name="phone" class="neo-input" value="<?= htmlspecialchars($_SESSION['contact'] ?? ''); ?>" readonly>
                                    </div>
                                    <div class="col-12">
                                        <div class="alert-gold">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <span class="text-gold-dark">To edit this, go to <a href="profile.php">profile settings</a></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Event Details -->
                            <div class="neo-card mb-4">
                                <div class="mb-4 pb-2 border-bottom border-secondary">
                                    <h4 class="m-0"><i class="bi bi-calendar-event me-2 text-gold"></i>Event Details</h4>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <?php
                                        $minBookingDate = date('Y-m-d', strtotime('+5 days'));
                                        $maxBookingDate = date('Y-m-d', strtotime('+3 years'));
                                        ?>
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Event Date <span class="text-gold">*</span></label>
                                        <input type="date" name="eventDate" id="eventDate" class="neo-input" min="<?= $minBookingDate ?>" max="<?= $maxBookingDate ?>" value="<?= htmlspecialchars($savedData['eventDate'] ?? '') ?>" required>
                                        <div id="date-availability-feedback" class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Event Type <span class="text-gold">*</span></label>
                                        <select name="eventType" id="eventType" class="neo-input" required>
                                            <option value="" class="text-light" selected disabled>Select event type</option>
                                            <option value="Wedding" class="text-light" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Wedding') ? 'selected' : '' ?>>Wedding</option>
                                            <option value="Birthday" class="text-light" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Birthday') ? 'selected' : '' ?>>Birthday</option>
                                            <option value="Corporate" class="text-light" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Corporate') ? 'selected' : '' ?>>Corporate Event</option>
                                            <option value="Portrait" class="text-light" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Portrait') ? 'selected' : '' ?>>Portrait Session</option>
                                            <option value="Other" class="text-light" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Other') ? 'selected' : '' ?>>Other (Please Specify)</option>
                                        </select>
                                    </div>
                                    <!-- Hidden input for custom event type -->
                                    <div class="col-12" id="customEventTypeContainer" style="display: none;">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Specify Event Type <span class="text-gold">*</span></label>
                                        <input type="text" name="customEventType" id="customEventType" class="neo-input" placeholder="Enter your event type" value="<?= htmlspecialchars($savedData['customEventType'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Start Time <span class="text-gold">*</span></label>
                                        <input type="time" name="startTime" class="neo-input" value="<?= htmlspecialchars($savedData['startTime'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">End Time <span class="text-gold">*</span></label>
                                        <input type="time" name="endTime" class="neo-input" value="<?= htmlspecialchars($savedData['endTime'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Event Location <span class="text-gold">*</span></label>
                                        <input type="text" name="location" class="neo-input" placeholder="Full address" value="<?= htmlspecialchars($savedData['location'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Landmark (Optional)</label>
                                        <input type="text" name="landmark" class="neo-input" placeholder="Nearby landmark for easier navigation" value="<?= htmlspecialchars($savedData['landmark'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Package Selection -->
                            <div class="neo-card mb-4">
                                <div class="mb-4 pb-2 border-bottom border-secondary">
                                    <h4 class="m-0"><i class="bi bi-box-seam me-2 text-gold"></i>Select Your Package</h4>
                                </div>
                                <div class="row g-3">
                                    <?php foreach ($packages as $pkg): ?>
                                    <div class="col-12">
                                        <div class="form-check p-0">
                                            <input type="radio" name="packageID" id="luxury-pkg-<?= $pkg['packageID'] ?>" value="<?= $pkg['packageID'] ?>" class="btn-check luxury-radio" <?= (isset($savedData['packageID']) && $savedData['packageID'] === $pkg['packageID']) ? 'checked' : '' ?> required>
                                            <label for="luxury-pkg-<?= $pkg['packageID'] ?>" class="neo-card d-flex justify-content-between align-items-center w-100 cursor-pointer package-label" 
                                                data-price="<?= $pkg['Price'] ?>" 
                                                data-name="<?= htmlspecialchars($pkg['packageName']) ?>"
                                                data-coverage-hours="<?= isset($pkg['coverage_hours']) ? $pkg['coverage_hours'] : 4 ?>"
                                                data-hourly-rate="<?= isset($pkg['extra_hour_rate']) ? $pkg['extra_hour_rate'] : 1000 ?>"
                                                style="cursor: pointer;">
                                                <div class="d-flex flex-column">
                                                    <h5 class="text-gold mb-1"><?= htmlspecialchars($pkg['packageName']) ?></h5>
                                                    <p class="text-muted small mb-0"><?= htmlspecialchars($pkg['description']) ?></p>
                                                </div>
                                                <div class="text-end">
                                                    <span class="h5 text-light d-block mb-0">₱<?= number_format($pkg['Price']) ?></span>
                                                    <i class="bi bi-check-circle-fill text-gold opacity-0 check-icon transition-all"></i>
                                                </div>
                                            </label>
                                        </div>
                                        <div id="details-<?= $pkg['packageID'] ?>" class="mt-3 ps-4 border-start border-gold" style="display: none;"></div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="neo-card mb-4">
                                <div class="mb-4 pb-2 border-bottom border-secondary">
                                    <h4 class="m-0"><i class="bi bi-credit-card me-2 text-gold"></i>Payment Method</h4>
                                </div>
                                <div class="row g-3">
                                    <?php 
                                    $methods = [
                                        'GCash' => 'phone',
                                        'PayMaya' => 'wallet2',
                                        'Bank Transfer' => 'bank',
                                        'Cash' => 'cash'
                                    ];
                                    foreach($methods as $name => $icon):
                                        $id = 'pm-' . strtolower(str_replace(' ', '-', $name));
                                    ?>
                                    <div class="col-md-6 col-lg-3">
                                        <input type="radio" name="paymentMethod" id="<?= $id ?>" value="<?= $name ?>" class="btn-check" required>
                                        <label for="<?= $id ?>" class="neo-card w-100 text-center p-3 cursor-pointer payment-label" style="cursor: pointer;">
                                            <i class="bi bi-<?= $icon ?> fs-3 text-gold mb-2 d-block"></i>
                                            <span class="text-light"><?= $name ?></span>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Payment Details -->
                                <div id="paymentDetails" class="mt-4 p-3 rounded bg-soft-gold" style="display: none;">
                                    <div id="paymentInfo"></div>
                                </div>

                                <!-- Proof Upload -->
                                <div id="proofUploadSection" class="mt-4" style="display: none;">
                                    <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Proof of Payment <span class="text-gold">*</span></label>
                                    <div class="neo-input p-1 d-flex align-items-center">
                                        <input type="file" name="paymentProof" id="paymentProof" class="form-control bg-transparent border-0 text-light" accept="image/*,.pdf">
                                    </div>
                                    <small class="text-muted d-block mt-2">Max 5MB • JPG, PNG, or PDF</small>
                                    <div id="proofPreview" class="mt-3"></div>
                                </div>
                            </div>

                            <!-- Special Requests -->
                            <div class="neo-card mb-4">
                                <div class="mb-4 pb-2 border-bottom border-secondary">
                                    <h4 class="m-0"><i class="bi bi-chat-left-text me-2 text-gold"></i>Special Requests</h4>
                                </div>
                                <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Additional Notes (Optional)</label>
                                <textarea name="specialRequests" class="neo-input" rows="4" placeholder="Any special requirements or requests..."><?= htmlspecialchars($savedData['specialRequests'] ?? '') ?></textarea>
                            </div>

                        </div>

                        <!-- Right Column: Real-time Summary -->
                        <div class="col-lg-4">
                            <div class="sticky-top" style="top: 20px; z-index: 100;">
                                <div class="neo-card-light shadow-lg shadow-light">
                                    <div class="mb-4 pb-2 border-bottom border-secondary">
                                        <h4 class="m-0"><i class="bi bi-receipt me-2 text-gold"></i>Booking Summary</h4>
                                    </div>
                                    
                                    <!-- User Information -->
                                    <div class="mb-3">
                                        <div class="text-muted small mb-1"><i class="bi bi-person me-1"></i> Client Information</div>
                                        <div id="summary-client-name" class="text-muted-luxury fw-bold text-truncate">-</div>
                                        <div id="summary-client-email" class="text-muted small">-</div>
                                        <div id="summary-client-phone" class="text-muted small">-</div>
                                    </div>

                                    <div class="divider"></div>

                                    <!-- Event Details -->
                                    <div class="mb-3">
                                        <div class="text-muted small mb-1"><i class="bi bi-calendar-event me-1"></i> Event Details</div>
                                        <div id="summary-event-date" class="text-muted-luxury fw-bold">-</div>
                                        <div id="summary-event-time" class="text-muted small">-</div>
                                        <div id="summary-event-venue" class="text-muted small text-truncate">-</div>
                                    </div>

                                    <div class="divider"></div>

                                    <!-- Package Selection -->
                                    <div class="mb-3">
                                        <div class="text-muted small mb-1"><i class="bi bi-box-seam me-1"></i> Package</div>
                                        <div id="summary-package-name" class="text-muted-luxury">Not selected</div>
                                    </div>

                                    <!-- Add-ons -->
                                    <div id="summary-addons-container" class="mb-3" style="display: none;">
                                        <div class="text-muted small mb-1"><i class="bi bi-plus-circle me-1"></i> Add-ons</div>
                                        <div id="summary-addons-list"></div>
                                    </div>

                                    <div class="divider"></div>

                                    <!-- Payment Method -->
                                    <div id="summary-payment-container" class="mb-3" style="display: none;">
                                        <div class="text-muted small mb-1"><i class="bi bi-credit-card me-1"></i> Payment Method</div>
                                        <div id="summary-payment-method" class="text-muted-luxury">-</div>
                                    </div>

                                    <!-- Special Requests -->
                                    <div id="summary-requests-container" class="mb-3" style="display: none;">
                                        <div class="text-muted small mb-1"><i class="bi bi-chat-left-text me-1"></i> Special Requests</div>
                                        <div id="summary-special-requests" class="text-muted small fst-italic">-</div>
                                    </div>

                                    <div class="divider"></div>

                                    <!-- Pricing -->
                                    <div class="mt-4">
                                        <div id="extra-hours-row" class="d-flex justify-content-between mb-2" style="display: none !important;">
                                            <span class="text-muted small text-uppercase letter-spacing-1">Extra Hours <small id="extra-hours-detail"></small></span>
                                            <span id="extraHoursPrice" class="text-muted-luxury font-serif">₱0</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted small text-uppercase letter-spacing-1">Subtotal</span>
                                            <span id="totalPrice" class="text-muted-luxury fw-bold font-serif fs-5">₱0</span>
                                        </div>
                                        <div class="d-flex justify-content-between mt-3 pt-3 border-top border-secondary">
                                            <span class="text-gold fw-bold small text-uppercase letter-spacing-1">Downpayment (25%)</span>
                                            <span id="downpaymentAmount" class="text-gold fw-bold fs-4 font-serif">₱0</span>
                                        </div>
                                    </div>

                                    <!-- Terms & Conditions -->
                                    <div class="form-check mt-4 mb-4">
                                        <input class="form-check-input" type="checkbox" id="termsConfirm" required>
                                        <label class="form-check-label text-muted small" for="termsConfirm">
                                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="text-gold text-decoration-none">Terms</a>, <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" class="text-gold text-decoration-none">Privacy</a>, and <a href="#" data-bs-toggle="modal" data-bs-target="#refundModal" class="text-gold text-decoration-none">Refund Policy</a>
                                        </label>
                                    </div>

                                    <!-- Submit Button -->
                                     <input type="submit" value="Confirm Booking" class="btn btn-gold w-100 luxury-submit-btn">
                                   

                                    <!-- Info Alert -->
                                    <div class="mt-3 p-2 rounded border   bg-opacity-50 text-center">
                                        <small class="text-muted"><i class="bi bi-info-circle me-1"></i> 25% downpayment required</small>
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
    
    <!-- Custom Modal Component -->
    <?php include '../includes/components/modal.php'; ?>
    
    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/modal.js"></script>
    
    <!-- Restore saved form state -->
    <?php if (!empty($savedData)): ?>
    <script>
        // Wait for booking.js to fully load before restoring state
        window.addEventListener('load', function() {
            setTimeout(function() {
                console.log('Restoring saved form state...');
                
                <?php if (isset($savedData['packageID'])): ?>
                const savedPackageId = '<?= htmlspecialchars($savedData['packageID']) ?>';
                const savedPackage = document.querySelector(`input[name="packageID"][value="${savedPackageId}"]`);
                
                if (savedPackage) {
                    savedPackage.checked = true;
                    const detailsContainer = document.getElementById(`details-${savedPackageId}`);
                    if (detailsContainer && typeof fetchAndDisplayPackageDetails === 'function') {
                        fetchAndDisplayPackageDetails(savedPackageId, detailsContainer);
                    }
                    savedPackage.dispatchEvent(new Event('change', { bubbles: true }));
                }
                <?php endif; ?>
                
                <?php if (isset($savedData['paymentMethod'])): ?>
                const paymentMethod = document.querySelector('input[name="paymentMethod"][value="<?= htmlspecialchars($savedData['paymentMethod']) ?>"]');
                if (paymentMethod) {
                    paymentMethod.checked = true;
                    paymentMethod.dispatchEvent(new Event('change', { bubbles: true }));
                }
                <?php endif; ?>

                <?php if (isset($savedData['addons']) && is_array($savedData['addons'])): ?>
                setTimeout(function() {
                    <?php foreach ($savedData['addons'] as $addonId): ?>
                    const addon_<?= $addonId ?> = document.querySelector('input[name="addons[]"][value="<?= htmlspecialchars($addonId) ?>"]');
                    if (addon_<?= $addonId ?>) {
                        addon_<?= $addonId ?>.checked = true;
                        addon_<?= $addonId ?>.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                    <?php endforeach; ?>
                }, 1500);
                <?php endif; ?>

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
                
            }, 1000);
        });
    </script>
    <?php endif; ?>
    
    <script src="booking.js"></script>
    <script src="user.js"></script>
    
    <!-- Booking Status Notifications -->
    <?php if ($bookingStatus): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($bookingStatus === 'success'): ?>
                LuxuryModal.show({
                    title: 'Booking Submitted!',
                    message: '<?= addslashes($bookingMessage) ?>',
                    icon: 'success',
                    confirmText: 'View My Bookings',
                    cancelText: 'Stay Here'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'appointments.php';
                    } else {
                        const url = new URL(window.location);
                        url.searchParams.delete('booking');
                        window.history.replaceState({}, '', url);
                    }
                });
            <?php elseif ($bookingStatus === 'error'): ?>
                LuxuryModal.show({
                    title: 'Booking Failed',
                    message: '<?= addslashes($bookingMessage) ?>',
                    icon: 'error',
                    confirmText: 'Try Again'
                }).then(() => {
                    const url = new URL(window.location);
                    url.searchParams.delete('error');
                    window.history.replaceState({}, '', url);
                });
            <?php endif; ?>
        });
    </script>
    <?php endif; ?>
    
    <style>
        /* Additional styles for radio button active states */
        .btn-check:checked + .neo-card {
            border-color: var(--gold-main);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.1);
        }
        .btn-check:checked + .neo-card .check-icon {
            opacity: 1 !important;
        }
        .btn-check:checked + .neo-card .text-gold {
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
        }
    </style>
</body>
</html>
