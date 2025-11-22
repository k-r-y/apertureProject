<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/csrf.php';
require_once '../includes/functions/session.php';

// Redirect non-users or unverified users
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'User' || !$_SESSION['isVerified']) {
    header("Location: ../logIn.php");
    exit;
}

$query = "SELECT b.*, p.packageName 
          FROM bookings b 
          LEFT JOIN packages p ON b.packageID = p.packageID 
          WHERE b.userID = ? 
          ORDER BY b.event_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['userId']);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Aperture</title>

    <!-- Local Bootstrap CSS -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <!-- Custom User CSS -->

        <main class="main-content">
            <div class="container-fluid p-0">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">My Appointments</h1>
                </div>

                <!-- Filters -->
                <div class="glass-card mb-4">
                    <div class="card-body d-flex flex-wrap gap-3 align-items-center">
                        <div class="flex-grow-1">
                            <input type="text" class="form-control bg-dark border-secondary text-light" placeholder="Search by event type...">
                        </div>
                        <div>
                            <select class="form-select bg-dark border-secondary text-light">
                                <option selected>All Statuses</option>
                                <option value="Pending">Pending</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Completed">Completed</option>
                                <option value="Canceled">Canceled</option>
                            </select>
                        </div>
                        <button class="btn btn-outline-secondary">Filter</button>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="glass-card">
                    <div class="table-responsive">
                        <table class="table-luxury px-3">
                            <thead>
                                <tr>
                                    <th class="ps-3">Event Type</th>
                                    <th>Date</th>
                                    <th>Package</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody >
                                <?php if (empty($bookings)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-light">No appointments found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($bookings as $appt) : 
                                        $statusClass = 'status-pending';
                                        switch($appt['booking_status']) {
                                            case 'Confirmed': $statusClass = 'status-confirmed'; break;
                                            case 'Completed': $statusClass = 'status-completed'; break;
                                            case 'Canceled': $statusClass = 'status-canceled'; break;
                                        }
                                    ?>
                                        <tr >
                                            <td class="ps-3 fw-bold  text-light "><?= htmlspecialchars($appt['event_type']) ?></td>
                                            <td class="text-light"><?= date('M d, Y', strtotime($appt['event_date'])) ?></td>
                                            <td class="text-light"><?= htmlspecialchars($appt['packageName'] ?? 'Custom') ?></td>
                                            <td class="text-light"><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($appt['booking_status']) ?></span></td>
                                            <td class="text-end pe-3">
                                                <a href="#" class="btn btn-sm btn-outline-light">View Details</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center mt-3">
                        <span class="text-secondary small">Showing <?= count($bookings) ?> appointments</span>
                        <!-- Pagination could be added here -->
<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/csrf.php';
require_once '../includes/functions/session.php';

// Redirect non-users or unverified users
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'User' || !$_SESSION['isVerified']) {
    header("Location: ../logIn.php");
    exit;
}

$query = "SELECT b.*, p.packageName 
          FROM bookings b 
          LEFT JOIN packages p ON b.packageID = p.packageID 
          WHERE b.userID = ? 
          ORDER BY b.event_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['userId']);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Aperture</title>

    <!-- Local Bootstrap CSS -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <!-- Custom User CSS -->

        <main class="main-content">
            <div class="container-fluid p-0">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">My Appointments</h1>
                </div>

                <!-- Filters -->
                <div class="glass-card mb-4">
                    <div class="card-body d-flex flex-wrap gap-3 align-items-center">
                        <div class="flex-grow-1">
                            <input type="text" class="form-control bg-dark border-secondary text-light" placeholder="Search by event type...">
                        </div>
                        <div>
                            <select class="form-select bg-dark border-secondary text-light">
                                <option selected>All Statuses</option>
                                <option value="Pending">Pending</option>
                                <option value="Confirmed">Confirmed</option>
                                <option value="Completed">Completed</option>
                                <option value="Canceled">Canceled</option>
                            </select>
                        </div>
                        <button class="btn btn-outline-secondary">Filter</button>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="glass-card">
                    <div class="table-responsive">
                        <table class="table-luxury px-3">
                            <thead>
                                <tr>
                                    <th class="ps-3">Event Type</th>
                                    <th>Date</th>
                                    <th>Package</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody >
                                <?php if (empty($bookings)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-light">No appointments found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($bookings as $appt) : 
                                        $statusClass = 'status-pending';
                                        switch($appt['booking_status']) {
                                            case 'Confirmed': $statusClass = 'status-confirmed'; break;
                                            case 'Completed': $statusClass = 'status-completed'; break;
                                            case 'Canceled': $statusClass = 'status-canceled'; break;
                                        }
                                    ?>
                                        <tr >
                                            <td class="ps-3 fw-bold  text-light "><?= htmlspecialchars($appt['event_type']) ?></td>
                                            <td class="text-light"><?= date('M d, Y', strtotime($appt['event_date'])) ?></td>
                                            <td class="text-light"><?= htmlspecialchars($appt['packageName'] ?? 'Custom') ?></td>
                                            <td class="text-light"><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($appt['booking_status']) ?></span></td>
                                            <td class="text-end pe-3">
                                                <a href="#" class="btn btn-sm btn-outline-light">View Details</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center mt-3">
                        <span class="text-secondary small">Showing <?= count($bookings) ?> appointments</span>
                        <!-- Pagination could be added here -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="user.js"></script>
    
    <!-- Booking Success Notification -->
    <?php if (isset($_SESSION['booking_success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Booking Submitted!',
                text: '<?= addslashes($_SESSION['booking_success']) ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d4af37',
                background: '#1a1a1a',
                color: '#ffffff'
            });
        });
    </script>
    <?php unset($_SESSION['booking_success']); ?>
    <?php endif; ?>
</body>

</html>