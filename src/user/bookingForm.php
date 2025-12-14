<?php
// bookingForm.php - Luxury Dark Theme Booking Form

require_once '../includes/functions/config.php';
require_once '../includes/functions/session.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/booking_logic.php';
require_once '../includes/functions/csrf.php';

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
$query = ("SELECT * FROM packages ORDER BY Price ASC");
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

$minBookingDate = date('Y-m-d', strtotime('+3 days'));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking - Aperture</title>
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/sidebar.css">
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
            <div class="container-fluid px-3 px-lg-5 ">

                <!-- Page Header -->
                <div class="text-center mb-5">
                    <h1 class="mb-2">Create Your Booking</h1>
                    <p class="text-muted">Experience premium photography services</p>
                </div>

                <form action="processBooking.php" method="POST" enctype="multipart/form-data" id="bookingForm" class="needs-validation-luxury" novalidate>
                    <?php csrfField(); ?>
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
                                        $minBookingDate = date('Y-m-d', strtotime('+3 days'));
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

                                            <optgroup label="Weddings & Romance">
                                                <option value="Wedding Ceremony & Reception" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Wedding Ceremony & Reception') ? 'selected' : '' ?>>Wedding Ceremony & Reception</option>
                                                <option value="Engagement / Pre-Nup Session" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Engagement / Pre-Nup Session') ? 'selected' : '' ?>>Engagement / Pre-Nup Session</option>
                                                <option value="Proposal Coverage" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Proposal Coverage') ? 'selected' : '' ?>>Proposal Coverage</option>
                                                <option value="Anniversary Celebration" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Anniversary Celebration') ? 'selected' : '' ?>>Anniversary Celebration</option>
                                                <option value="Bridal Boudoir" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Bridal Boudoir') ? 'selected' : '' ?>>Bridal Boudoir</option>
                                            </optgroup>

                                            <optgroup label="Milestones & Family">
                                                <option value="Debut / 18th Birthday" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Debut / 18th Birthday') ? 'selected' : '' ?>>Debut / 18th Birthday</option>
                                                <option value="Maternity / Pregnancy Shoot" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Maternity / Pregnancy Shoot') ? 'selected' : '' ?>>Maternity / Pregnancy Shoot</option>
                                                <option value="Newborn & Baby Milestones" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Newborn & Baby Milestones') ? 'selected' : '' ?>>Newborn & Baby Milestones</option>
                                                <option value="Family Reunion / Generational Portrait" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Family Reunion / Generational Portrait') ? 'selected' : '' ?>>Family Reunion / Generational Portrait</option>
                                                <option value="Baptism / Christening" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Baptism / Christening') ? 'selected' : '' ?>>Baptism / Christening</option>
                                                <option value="Graduation / Toga Portrait" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Graduation / Toga Portrait') ? 'selected' : '' ?>>Graduation / Toga Portrait</option>
                                            </optgroup>

                                            <optgroup label="Corporate & Professional">
                                                <option value="Corporate Headshots & Personal Branding" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Corporate Headshots & Personal Branding') ? 'selected' : '' ?>>Corporate Headshots & Personal Branding</option>
                                                <option value="Gala Dinner / Awards Night" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Gala Dinner / Awards Night') ? 'selected' : '' ?>>Gala Dinner / Awards Night</option>
                                                <option value="Conference / Seminar Coverage" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Conference / Seminar Coverage') ? 'selected' : '' ?>>Conference / Seminar Coverage</option>
                                                <option value="Product Photography" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Product Photography') ? 'selected' : '' ?>>Product Photography</option>
                                                <option value="Real Estate / Interior Photography" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Real Estate / Interior Photography') ? 'selected' : '' ?>>Real Estate / Interior Photography</option>
                                                <option value="Food & Menu Photography" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Food & Menu Photography') ? 'selected' : '' ?>>Food & Menu Photography</option>
                                            </optgroup>

                                            <optgroup label="Lifestyle & Creative">
                                                <option value="Fashion / Editorial Shoot" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Fashion / Editorial Shoot') ? 'selected' : '' ?>>Fashion / Editorial Shoot</option>
                                                <option value="Pet Photography" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Pet Photography') ? 'selected' : '' ?>>Pet Photography</option>
                                                <option value="Music Concert / Live Performance" <?= (isset($savedData['eventType']) && $savedData['eventType'] === 'Music Concert / Live Performance') ? 'selected' : '' ?>>Music Concert / Live Performance</option>
                                            </optgroup>

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
                                        <select name="startTime" id="startTime" class="neo-input" required>
                                            <option value="" disabled selected>Select start time</option>
                                            <?php
                                            $start = strtotime('07:00');
                                            $end = strtotime('22:00');
                                            $savedStart = $savedData['startTime'] ?? '';
                                            for ($i = $start; $i <= $end; $i += 1800) {
                                                $timeValue = date('H:i', $i);
                                                $timeLabel = date('g:i A', $i);
                                                $selected = ($savedStart == $timeValue) ? 'selected' : '';
                                                echo "<option value=\"$timeValue\" $selected>$timeLabel</option>";
                                            }
                                            ?>
                                        </select>
                                        <div id="start-time-feedback" class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">End Time <span class="text-gold">*</span></label>
                                        <select name="endTime" id="endTime" class="neo-input" required>
                                            <option value="" disabled selected>Select end time</option>
                                            <?php
                                            $savedEnd = $savedData['endTime'] ?? '';
                                            for ($i = $start; $i <= $end; $i += 1800) {
                                                $timeValue = date('H:i', $i);
                                                $timeLabel = date('g:i A', $i);
                                                $selected = ($savedEnd == $timeValue) ? 'selected' : '';
                                                echo "<option value=\"$timeValue\" $selected>$timeLabel</option>";
                                            }
                                            ?>
                                        </select>
                                        <div id="end-time-feedback" class="invalid-feedback"></div>
                                        <div id="time-range-feedback" class="invalid-feedback"></div>
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
                                    foreach ($methods as $name => $icon):
                                        $id = 'pm-' . strtolower(str_replace(' ', '-', $name));
                                    ?>
                                        <div class="col-md-6 col-lg-3">
                                            <input type="radio" name="paymentMethod" id="<?= $id ?>" value="<?= $name ?>" class="btn-check" required>
                                            <label for="<?= $id ?>" class="neo-card w-100 h-100 text-center py-2 px-1 cursor-pointer payment-label" style="cursor: pointer;">
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

                                <!-- Downpayment Amount -->
                                <div class="mt-4">
                                    <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Downpayment Amount (₱) <span class="text-gold">*</span></label>
                                    <input type="number" name="downpayment" id="downpaymentInput" class="neo-input" required step="0.01" min="0">
                                    <div class="form-text text-muted small mt-1" id="downpaymentHint">Minimum required: 25% of total</div>
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

                            <!-- Consultation Schedule -->
                            <div class="neo-card mb-4">
                                <div class="mb-4 pb-2 border-bottom border-secondary">
                                    <h4 class="m-0"><i class="bi bi-camera-video me-2 text-gold"></i>Consultation Schedule</h4>
                                </div>
                                <p class="text-muted small mb-3">Schedule a Google Meet / Zoom call with our team to discuss your event details. <span class="text-gold">Maximum duration: 1.5 hours</span></p>

                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Preferred Date <span class="text-gold">*</span></label>
                                        <input type="date" name="consultationDate" id="consultationDate" class="neo-input" min="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">Start Time <span class="text-gold">*</span></label>
                                        <select name="consultationStartTime" id="consultationStartTime" class="neo-input" required>
                                            <option value="" disabled selected>Select start time</option>
                                            <?php
                                            $cStart = strtotime('09:00');
                                            $cEnd = strtotime('18:00');
                                            for ($i = $cStart; $i < $cEnd; $i += 1800) { // 30-minute intervals
                                                $timeValue = date('H:i', $i);
                                                $timeLabel = date('g:i A', $i);
                                                echo "<option value=\"$timeValue\">$timeLabel</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small mb-2 text-uppercase letter-spacing-1">End Time <span class="text-gold">*</span></label>
                                        <select name="consultationEndTime" id="consultationEndTime" class="neo-input" required disabled>
                                            <option value="" disabled selected>Select start time first</option>
                                        </select>
                                        <small class="text-muted d-block mt-1" id="duractionInfo"></small>
                                    </div>
                                    <div class="col-12">
                                        <div id="conflictWarning" class="alert alert-warning d-none" style="background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); color: #ffc107;">
                                            <i class="bi bi-exclamation-triangle me-2"></i><span id="conflictMessage"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Right Column: Real-time Summary -->
                        <div class="col-lg-4">
                            <div class="sticky-luxury-summary">
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
                                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="text-gold text-decoration-none">Terms</a>, <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" class="text-gold text-decoration-none">Privacy</a>, and <a href="#" data-bs-toggle="modal" data-bs-target="#refundPolicyModal" class="text-gold text-decoration-none">Refund Policy</a>
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
    <script src="../js/feedback.js"></script>
    <script src="../js/validation.js"></script>

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
                            savedPackage.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                        }
                    <?php endif; ?>

                    <?php if (isset($savedData['paymentMethod'])): ?>
                        const paymentMethod = document.querySelector('input[name="paymentMethod"][value="<?= htmlspecialchars($savedData['paymentMethod']) ?>"]');
                        if (paymentMethod) {
                            paymentMethod.checked = true;
                            paymentMethod.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                        }
                    <?php endif; ?>

                    <?php if (isset($savedData['addons']) && is_array($savedData['addons'])): ?>
                        setTimeout(function() {
                            <?php foreach ($savedData['addons'] as $addonId): ?>
                                const addon_<?= $addonId ?> = document.querySelector('input[name="addons[]"][value="<?= htmlspecialchars($addonId) ?>"]');
                                if (addon_<?= $addonId ?>) {
                                    addon_<?= $addonId ?>.checked = true;
                                    addon_<?= $addonId ?>.dispatchEvent(new Event('change', {
                                        bubbles: true
                                    }));
                                }
                            <?php endforeach; ?>
                        }, 1500);
                    <?php endif; ?>

                    <?php if (isset($savedData['eventDate'])): ?>
                        const eventDateInput = document.getElementById('eventDate');
                        if (eventDateInput && eventDateInput.value) {
                            eventDateInput.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                        }
                    <?php endif; ?>

                    <?php if (isset($savedData['startTime'])): ?>
                        const startTimeInput = document.querySelector('input[name="startTime"]');
                        if (startTimeInput && startTimeInput.value) {
                            startTimeInput.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                        }
                    <?php endif; ?>

                    <?php if (isset($savedData['endTime'])): ?>
                        const endTimeInput = document.querySelector('input[name="endTime"]');
                        if (endTimeInput && endTimeInput.value) {
                            endTimeInput.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));
                        }
                    <?php endif; ?>

                }, 1000);
            });
        </script>
    <?php endif; ?>

    <!-- Pre-select package from sessionStorage (from services.php) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectedPackageId = sessionStorage.getItem('selectedPackageId');

            if (selectedPackageId) {
                // Wait a bit for other scripts to initialize
                setTimeout(() => {
                    const packageRadio = document.querySelector(`input[name="packageID"][value="${selectedPackageId}"]`);
                    if (packageRadio) {
                        packageRadio.checked = true;
                        packageRadio.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));

                        // Scroll to package section
                        packageRadio.closest('.neo-card').scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        // Clear storage so it doesn't persist forever
                        sessionStorage.removeItem('selectedPackageId');
                        sessionStorage.removeItem('selectedPackageName');
                        sessionStorage.removeItem('selectedPrice');
                    }
                }, 500);
            }
        });
    </script>

    <!-- Consultation Time Validation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const consultationDate = document.getElementById('consultationDate');
            const startTimeSelect = document.getElementById('consultationStartTime');
            const endTimeSelect = document.getElementById('consultationEndTime');
            const conflictWarning = document.getElementById('conflictWarning');
            const conflictMessage = document.getElementById('conflictMessage');
            const durationInfo = document.getElementById('duractionInfo');

            // Enable end time when start time is selected
            startTimeSelect.addEventListener('change', function() {
                const startTime = this.value;
                if (!startTime) return;

                // Clear and enable end time
                endTimeSelect.innerHTML = '<option value="" disabled selected>Select end time</option>';
                endTimeSelect.disabled = false;

                // Generate end time options (start + 30min to start + 90min)
                const startMinutes = timeToMinutes(startTime);
                const minEnd = startMinutes + 30; // Min 30 minutes
                const maxEnd = startMinutes + 90; // Max 1.5 hours

                // Generate 30-minute intervals
                for (let minutes = minEnd; minutes <= maxEnd && minutes <= 1080; minutes += 30) { // 1080 = 18:00
                    const timeValue = minutesToTime(minutes);
                    const timeLabel = formatTime(timeValue);
                    const option = document.createElement('option');
                    option.value = timeValue;
                    option.textContent = timeLabel;
                    endTimeSelect.appendChild(option);
                }
            });

            // Check for conflicts when end time is selected
            endTimeSelect.addEventListener('change', function() {
                checkConsultationConflict();
            });

            // Also check when date changes
            consultationDate.addEventListener('change', function() {
                if (startTimeSelect.value && endTimeSelect.value) {
                    checkConsultationConflict();
                }
                validateDateTime();
            });

            startTimeSelect.addEventListener('change', function() {
                validateDateTime();
            });

            function validateDateTime() {
                const selectedDate = consultationDate.value;
                const selectedTime = startTimeSelect.value;

                if (!selectedDate || !selectedTime) return;

                const now = new Date();
                const selected = new Date(selectedDate + 'T' + selectedTime);

                if (selected <= now) {
                    conflictWarning.classList.remove('d-none');
                    conflictMessage.textContent = 'Cannot schedule consultation in the past. Please select a future date and time.';
                    conflictWarning.style.background = 'rgba(220, 53, 69, 0.1)';
                    conflictWarning.style.borderColor = 'rgba(220, 53, 69, 0.3)';
                    conflictWarning.style.color = '#dc3545';
                    startTimeSelect.setCustomValidity('Invalid time');
                    return false;
                }

                // Check if consultation is before event date
                const eventDateInput = document.getElementById('eventDate');
                if (eventDateInput && eventDateInput.value) {
                    const eventDateVal = eventDateInput.value;
                    const checkConsultationDate = new Date(selectedDate);
                    checkConsultationDate.setHours(0, 0, 0, 0);

                    const checkEventDate = new Date(eventDateVal);
                    checkEventDate.setHours(0, 0, 0, 0);

                    if (checkConsultationDate >= checkEventDate) {
                        conflictWarning.classList.remove('d-none');
                        conflictMessage.textContent = 'Consultation must be scheduled BEFORE the event date.';
                        conflictWarning.style.background = 'rgba(220, 53, 69, 0.1)';
                        conflictWarning.style.borderColor = 'rgba(220, 53, 69, 0.3)';
                        conflictWarning.style.color = '#dc3545';
                        consultationDate.setCustomValidity('Invalid date');
                        return false;
                    } else {
                        consultationDate.setCustomValidity('');
                    }
                } else {
                    startTimeSelect.setCustomValidity('');
                    if (conflictMessage.textContent.includes('past')) {
                        conflictWarning.classList.add('d-none');
                    }
                    return true;
                }
            }

            function checkConsultationConflict() {
                const date = consultationDate.value;
                const startTime = startTimeSelect.value;
                const endTime = endTimeSelect.value;

                if (!date || !startTime || !endTime) return;

                // Validate date/time first
                if (!validateDateTime()) return;

                // Calculate and show duration
                const duration = (timeToMinutes(endTime) - timeToMinutes(startTime));
                if (duration > 0) {
                    const hours = Math.floor(duration / 60);
                    const mins = duration % 60;
                    let durationText = '';
                    if (hours > 0) durationText += hours + (hours === 1 ? ' hour' : ' hours');
                    if (mins > 0) durationText += (hours > 0 ? ' ' : '') + mins + (mins === 1 ? ' minute' : ' minutes');
                    durationInfo.textContent = 'Duration: ' + durationText;
                }

                // Check for conflicts
                fetch('api/check_consultation_conflict.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            consultationDate: date,
                            startTime: startTime + ':00',
                            endTime: endTime + ':00'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            conflictWarning.classList.remove('d-none');
                            conflictWarning.style.background = 'rgba(220, 53, 69, 0.1)';
                            conflictWarning.style.borderColor = 'rgba(220, 53, 69, 0.3)';
                            conflictWarning.style.color = '#dc3545';

                            if (data.conflicts && data.conflicts.length > 0) {
                                const conflictTimes = data.conflicts.map(c => `${c.startTime} - ${c.endTime}`).join(', ');
                                conflictMessage.innerHTML = `${data.message}:<br><strong>${conflictTimes}</strong><br>Please choose a different time.`;
                            } else {
                                conflictMessage.textContent = data.message;
                            }
                            endTimeSelect.setCustomValidity('Time conflict');
                        } else {
                            conflictWarning.classList.add('d-none');
                            endTimeSelect.setCustomValidity('');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking conflicts:', error);
                    });
            }

            // Helper functions
            function timeToMinutes(time) {
                const [hours, minutes] = time.split(':').map(Number);
                return hours * 60 + minutes;
            }

            function minutesToTime(minutes) {
                const hours = Math.floor(minutes / 60);
                const mins = minutes % 60;
                return String(hours).padStart(2, '0') + ':' + String(mins).padStart(2, '0');
            }

            function formatTime(time) {
                const [hours, minutes] = time.split(':');
                const hour = parseInt(hours);
                const ampm = hour >= 12 ? 'PM' : 'AM';
                const displayHour = hour % 12 || 12;
                return displayHour + ':' + minutes + ' ' + ampm;
            }
        });
    </script>

    <script src="booking.js"></script>
    <script src="user.js"></script>
    <script src="js/user_notifications.js"></script>

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
        .btn-check:checked+.neo-card {
            border-color: var(--gold-main);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.1);
        }

        .btn-check:checked+.neo-card .check-icon {
            opacity: 1 !important;
        }

        .btn-check:checked+.neo-card .text-gold {
            text-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
        }
    </style>
</body>

</html>