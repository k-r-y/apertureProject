<?php
require_once './includes/functions/config.php';
require_once './includes/functions/function.php';
require_once './includes/functions/auth.php';
require_once './includes/functions/session.php';
require_once './includes/functions/csrf.php';


if (!isset($_SESSION['userId']) or !isset($_SESSION['isVerified']) or $_SESSION['isVerified'] === 0) {
    header("Location: logIn.php");
    exit;
} else {
    $isProfileCompleted = isProfileCompleted($_SESSION['userId']);
    if (!$isProfileCompleted) {
        header("Location: completeProfile.php");
        exit;
    }
}


if (isset($_GET['action']) and $_GET['action'] === 'logout') {
    require_once './includes/functions/auth.php';
    logout();
}

$query = ("SELECT * FROM packages");
$result = $conn->query($query);

$package = [];

while ($row = $result->fetch_assoc()) {
    $package[] = $row;
}

if (isset($_GET['packageId'])) {
    getPackageDetails();
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="./assets/camera.png" type="image/x-icon">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <title>Booking</title>
</head>

<body>



    <section class="w-100 min-vh-100 bg-light d-flex justify-content-center align-items-center position-relative p-2 py-5 p-md-5" id="bookingFormSection">

        <div class="container">
            <div class="row">
                <div class="col d-flex justify-content-center align-items-center flex-column p-0">
                    <form action="" name="bookingForm" method="POST" class="py-4 px-2 bg-white  rounded" style="max-width: 800px;" id="bookingForm">

                        <div class="text-center mb-4 bg-light p-3 rounded-4">
                            <h1 class=" display-4 m-0 serif">Book Your Shoot Today</h1>
                            <small class="text-muted">Capture your story with precision, creativity, and style. Fill out the form below to reserve your photography and videography session.
                            </small>
                        </div>

                        <div class="progressContainer py-5">
                            <div id="progress" class="progress" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="height: .5rem;">
                                <div class="progress-bar" id="progress-bar" style="width: 25%;"></div>
                            </div>



                        </div>





                        <!------------------------------------- Client's Information ------------------------------------>

                        <fieldset class="border rounded  p-3 my-4 active">
                            <span class="serif" id="legend">Client Information</span>
                            <!-- First and Last Name  -->

                            <div class="mb-2 d-flex gap-2 flex-column flex-md-row ">
                                <div class="w-100">
                                    <label for="fname" class="form-label">First name<span class="text-danger">*</span></label>
                                    <input type="text" name="fname" id="fname" class="form-control text-muted" value="<?= htmlspecialchars($_SESSION["firstName"] ?? '') ?>" required readonly>
                                </div>

                                <div class="w-100">
                                    <label for="lname" class="form-label">Last name<span class="text-danger">*</span></label>
                                    <input type="text" name="lname" id="lname" class="form-control text-muted" value="<?= htmlspecialchars($_SESSION["lastName"] ?? '') ?>" readonly>
                                </div>
                            </div>

                            <!-- Email  -->

                            <div class="mb-2">
                                <label class="form-label" for="email">Email<span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" class="form-control text-muted" value="<?= htmlspecialchars($_SESSION["email"] ?? '') ?>" readonly>
                            </div>

                            <!-- Contact Number -->

                            <div class="mb-2">
                                <label class="form-label" for="phone">Contact No.<span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control text-muted" value="<?= htmlspecialchars($_SESSION['contact'] ?? ''); ?>" readonly>
                            </div>



                        </fieldset>


                        <!------------------------------------- Event's Information ------------------------------------>

                        <fieldset class="border rounded  p-3 my-4">
                            <span class="serif" id="legend">Event Details</span>
                            <!-- First and Last Name  -->

                            <div class="mb-3">
                                <label for="package" class="form-label">Select Event<span class="text-danger">*</span></label>
                                <select id="package" class="form-select" required>
                                    <option value="" disabled selected>--Choose an event--</option>
                                    <?php foreach ($package as $pkg) : ?>
                                        <option value="<?= ($pkg['packageID']) ?>" name="<?= ($pkg['packageID']) ?>"><?= ($pkg['packageName']) ?> - Php <?= number_format($pkg['Price']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Event Date & Time  -->
                            <div class="mb-2">
                                <label class="form-label" for="eventDate">Event Date & Time<span class="text-danger">*</span></label>
                                <input type="date" name="eventDate" id="eventDate" class="form-control text-muted" required>
                            </div>


                            <div class="mb-2 d-flex justify-content-center align-items-center gap-3 flex-column flex-md-row">
                                <div class="w-100">
                                    <label class="form-label" for="fromTime">From:<span class="text-danger">*</span></label>
                                    <input type="time" name="fromTime" id="fromTime" class="form-control text-muted" required>
                                </div>

                                <div class="w-100">
                                    <label class="form-label" for="toTime">To:<span class="text-danger">*</span></label>
                                    <input type="time" name="toTime" id="toTime" class="form-control text-muted" required>
                                </div>
                            </div>

                            <!-- Contact Number -->

                            <div class="mb-2">
                                <label class="form-label" for="location">Location (Full Address)<span class="text-danger">*</span></label>
                                <input type="text" name="location" id="location" class="form-control" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label" for="landmark">Landmark<span class="text-danger">*</span> </label>
                                <input type="text" name="landmark" id="landmark" class="form-control">
                            </div>
                        </fieldset>


                        <!------------------------------------- Service Selectionn ------------------------------------>
                        <fieldset class="border rounded  p-2 my-4">
                            <span class="serif" id="legend">Package & Service Selection</span>
                            <div class="mt-5 mt-sm-3 d-flex flex-column justify-content-center align-items-start gap-3">
                                <label for="<?= $pkg['packageID'] ?>">Select your package<span class="text-danger">*</span></label>

                                <?php foreach ($package as $pkg) : ?>
                                    <div class="radioContainer w-100 position-relative" id="radioContainer">
                                        <input type="radio" name="package" id="<?= $pkg['packageID'] ?>" value="<?= $pkg['packageID'] ?>" class="radio" required>
                                        <label class="  border rounded w-100 d-block p-3 " for="<?= $pkg['packageID'] ?>">
                                            <h4 class="fw-bold serif p-0 m-0"><?= $pkg['packageName'] ?> - <span>₱<?= number_format($pkg['Price']) ?></span></h4>
                                            <p class="p-0 m-0"><?= $pkg['description'] ?></p>

                                            <ul class="inclusionList mt-3"></ul>
                                        </label>

                                    </div>

                                <?php endforeach; ?>
                            </div>

                            <p class="mt-4 ">Addons: </p>
                            <div class="addons border rounded p-2">
                                <p>Please select a package to view addons...</p>
                            </div>
                        </fieldset>

                        <!------------------------------------- Service Selectionn ------------------------------------>

                        <fieldset class="border rounded  p-3 my-4">
                            <span class="serif" id="legend">Payment & Downpayment Details</span>

                            <div class="mb-2">
                                <label class="form-label" for="location">Location (Full Address)<span class="text-danger">*</span></label>
                                <input type="text" name="location" id="location" class="form-control" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label" for="landmark">Landmark<span class="text-danger">*</span> </label>
                                <input type="text" name="landmark" id="landmark" class="form-control">
                            </div>

                            <input type="submit" value="Confirm Booking" class="btn border w-100 text-light" id="submitForm" disabled>
                        </fieldset>




                        <div class="d-flex justify-content-start align-items-center gap-3">
                            <button class="btn border" id="prevStage" disabled><i class="bi bi-arrow-left"></i> Back</button>
                            <button class="btn border bg-dark text-light " id="nextStage">Next <i class="bi bi-arrow-right"></i></button>
                        </div>


                    </form>

                </div>
            </div>
        </div>


    </section>



    <a href="?action=logout" class="btn btn-danger position-absolute bottom-0">Logout</a>


    <script src="../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="./script.js"></script>
    <script>

    </script>
</body>

</html>
<!-- 
<div class="modal fade" id="dataModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="dataModalLabel">Terms & Privacy Policy</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Aperture Data Privacy Notice</h6>

                <p>At Aperture, we value your privacy and are committed to protecting your personal information. This notice explains how we collect, use, and safeguard data when you book a photography session through our platform. We comply with applicable laws like RA10173 or Data Privacy Act of 2012, collecting only what's necessary for your booking experience.</p>



                <h6>What Information We Collect</h6>

                <ul>
                    <li><strong>Personal Details: </strong>Full name, email address, and phone number (for confirmations and coordination).</li>
                    <li><strong>Booking Information: </strong>Service type, preferred date/time, location (e.g., via cascading dropdowns or autocomplete), number of subjects, and special notes.</li>
                    <li><strong>Payment Data: </strong>Processed securely via third-party providers (e.g., Stripe)—we don't store full card details.</li>
                    <li><strong>Technical Data: </strong>IP address, device type, and usage analytics (anonymized) to improve our site.</li>
                </ul>

                <p>We do not collect sensitive information like photos, IDs, or health data unless voluntarily provided in notes.</p>




                <h6>How We Use Your Information</h6>
                <p>Your data enables a seamless service:</p>
                <ul>
                    <li><strong>Fulfill Bookings:</strong> Schedule sessions, match photographers, and send reminders/receipts.</li>
                    <li><strong>Communications:</strong> Email/SMS confirmations, updates, and support (e.g., via PHPMailer).</li>
                    <li><strong>Payments & Logistics:</strong> Process transactions and calculate fees (e.g., based on location).</li>
                    <li><strong>Improvements:</strong> Anonymized analytics to enhance UX (e.g., form completion rates).</li>
                </ul>
                <p>We never sell your data. It may be shared with trusted partners (e.g., payment processors, email services) under strict agreements.</p>

                <h6>How We Protect and Store Your Data</h6>
                <ul>
                    <li><strong>Security:</strong> All data is encrypted (HTTPS), stored securely in our database (e.g., MySQL with hashed elements), and access-controlled.</li>
                    <li><strong>Retention:</strong> Kept only as needed (e.g., 2 years post-booking for records); deleted upon request or completion.</li>
                    <li><strong>Cookies:</strong> We use essential cookies for sessions; optional analytics (e.g., Google) require consent.</li>
                </ul>
                <h6>Your Rights and Choices</h6>
                <ul>
                    <li><strong>Access/Update/Delete:</strong> Contact us to view, correct, or erase your data (e.g., for one-time bookings).</li>
                    <li><strong>Opt-Out:</strong> Unsubscribe from emails anytime; withdraw consent for non-essential processing.</li>
                    <li><strong>Complaints:</strong> If concerned, reach out to us or your local data authority (e.g., NPC in the Philippines).</li>
                </ul>
                <p>For questions, email aperture.eventbookings@gmail.com or use our contact form. Last updated: September 2025. By using our site, you agree to this notice—review it anytime.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> -->