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

    <!-- Local Bootstrap CSS -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <link rel="stylesheet" href="admin.css">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="../style.css">
    <!-- Favicon -->
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Old+Standard+TT:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- FullCalendar CSS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>

    <style>
        /* FullCalendar Customizations */
        #calendar {
            height: 80vh;
        }

        .fc {
            color: var(--text-primary);
        }

        .fc .fc-toolbar-title {
            color: var(--gold);
            font-family: 'Old Standard TT', serif;
        }

        .fc .fc-button-primary {
            background-color: var(--card-bg);
            border-color: var(--border-color);
            color: var(--text-primary);
        }

        .fc .fc-button-primary:hover {
            background-color: var(--gold-soft);
            border-color: var(--gold);
        }

        .fc .fc-button-primary:active,
        .fc .fc-button-primary:focus {
            box-shadow: 0 0 0 0.2rem var(--gold-soft);
        }

        .fc-daygrid-day.fc-day-today {
            background-color: var(--gold-soft);
        }

        .fc-event {
            border: 1px solid var(--gold) !important;
            background-color: var(--gold-soft) !important;
            color: var(--gold) !important;
        }
    </style>
</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid p-0">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="header-title m-0">Calendar</h1>
                    <a href="#" class="btn btn-gold">+ New Appointment</a>
                </div>

                <div class="card-solid p-4">
                    <div id='calendar'></div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [{
                        title: 'Stark Industries - Wedding',
                        start: '2025-11-18T14:00:00'
                    },
                    {
                        title: 'Wayne Enterprises - Corporate Event',
                        start: '2025-11-22'
                    },
                    {
                        title: 'Aperture Science - Product Shoot',
                        start: '2025-12-02'
                    }
                ]
            });
            calendar.render();
        });
    </script>
</body>

</html>