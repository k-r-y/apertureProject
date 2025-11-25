<?php
require_once '../includes/functions/config.php';
require_once '../includes/functions/auth.php';
require_once '../includes/functions/function.php';
require_once '../includes/functions/csrf.php';
require_once '../includes/functions/session.php';

if (isset($_SESSION["userId"]) and isset($_SESSION["role"]) and $_SESSION["role"] === "User" and isset($_SESSION["isVerified"]) and  $_SESSION["isVerified"]) {
    header("Location: ../booking.php");
    exit;
}

if (!isset($_SESSION['userId']) or !isset($_SESSION['isVerified']) or $_SESSION['isVerified'] === 0) {
    header("Location: ../logIn.php");
    exit;
} else {
    $isProfileCompleted = isProfileCompleted($_SESSION['userId']);
    if (!$isProfileCompleted) {
        header("Location: ../completeProfile.php");
        exit;
    }
}

if (isset($_GET['action']) and $_GET['action'] === 'logout') {
    require_once '../includes/functions/auth.php';
    logout();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - Aperture Admin</title>

    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../luxuryDesignSystem.css">
    <link rel="stylesheet" href="../css/modal.css">
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <style>
        .fc-event { cursor: pointer; }
        .fc-toolbar-title { color: #D4AF37 !important; font-family: 'Playfair Display', serif; }
        .fc-button-primary { background-color: #D4AF37 !important; border-color: #D4AF37 !important; color: #000 !important; }
        .fc-daygrid-day-number { color: #fff; text-decoration: none; }
        .fc-col-header-cell-cushion { color: #fff; text-decoration: none; }
    </style>
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid p-0">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">Booking Calendar</h1>
                    <a href="bookings.php" class="btn btn-gold">Manage Bookings</a>
                </div>

                <div class="glass-panel p-4">
                    <div class="calendar-luxury" id="calendar"></div>
                </div>

            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                themeSystem: 'bootstrap5',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: 'get_bookings.php',
                eventClick: function(info) {
                    const event = info.event;
                    const props = event.extendedProps;
                    
                    Swal.fire({
                        title: event.title,
                        html: `
                            <div class="text-start">
                                <p><strong>Date:</strong> ${event.start.toLocaleString()}</p>
                                <p><strong>Status:</strong> <span class="badge bg-secondary">${props.status}</span></p>
                                <p><strong>Amount:</strong> ₱${Number(props.amount).toLocaleString()}</p>
                            </div>
                        `,
                        icon: 'info',
                        background: '#1a1a1a',
                        color: '#fff',
                        confirmButtonColor: '#D4AF37',
                        confirmButtonText: 'View Booking Details',
                        showCancelButton: true,
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect to bookings page or open modal (simplified for now)
                            window.location.href = `bookings.php?search=${event.id}`;
                        }
                    });
                }
            });
            calendar.render();
        });
    </script>
</body>

</html>
