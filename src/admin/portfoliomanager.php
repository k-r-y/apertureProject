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
    <title>Portfolio Manager - Aperture Admin</title>

    <!-- Local Bootstrap CSS -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="../../bootstrap-5.3.8-dist/font/bootstrap-icons.css">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="../style.css">
    <!-- Favicon -->
    <link rel="icon" href="../assets/camera.png" type="image/x-icon">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Old+Standard+TT:wght@400;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* Paste adminDashboard.php styles here */
        :root {
            --gold: #D4AF37;
            --admin-bg: #101010;
            --card-bg: #1A1A1A;
            --text-primary: #e9ecef;
            --text-secondary: #888888;
            --border-color: rgba(255, 255, 255, 0.05);
            --gold-soft: rgba(212, 175, 55, 0.1);
            --gold-glow: 0 0 25px rgba(212, 175, 55, 0.15);
            --sidebar-width: 240px;
        }

        body.admin-dashboard {
            font-family: "Inter", sans-serif;
            background-color: var(--admin-bg);
            color: var(--text-primary);
            font-size: 0.875rem;
            overflow-x: hidden;
        }

        .card-solid {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            height: 100%;
            transition: all 0.3s ease;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background-color: #000;
            border-right: 1px solid var(--border-color);
            padding: 1rem;
            transition: width 0.3s ease, left 0.3s ease;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            font-family: "Poppins", sans-serif;
            font-weight: 600;
            font-size: 1.5rem;
            color: var(--gold);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            white-space: nowrap;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-brand .brand-icon {
            height: 30px;
            transition: margin 0.3s ease;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
        }

        .nav-section-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            padding: 1rem 0.75rem 0.5rem;
            white-space: nowrap;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            color: var(--text-primary);
            font-family: "Poppins", sans-serif;
            font-weight: 400;
            text-decoration: none;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .sidebar-nav-link .nav-icon {
            font-size: 1.2rem;
            margin-right: 1.25rem;
            width: 20px;
            transition: margin 0.3s ease;
        }

        .sidebar-nav-link:hover {
            background-color: var(--gold-soft);
            color: var(--gold);
        }

        .sidebar-nav-link.active {
            background-color: transparent;
            color: var(--gold);
            border-left: 3px solid var(--gold);
            font-weight: 500;
        }

        .sidebar-nav-link.active .nav-icon {
            color: var(--gold);
        }

        .sidebar-footer {
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .page-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
        }

        .header {
            position: sticky;
            top: 0;
            z-index: 90;
            background-color: rgba(20, 20, 20, 0.65);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .header-toggle {
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-primary);
        }

        .main-content {
            padding: 2rem;
        }

        .portfolio-item {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
        }

        .portfolio-item img {
            transition: transform 0.4s ease;
        }

        .portfolio-item:hover img {
            transform: scale(1.05);
        }

        .portfolio-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            opacity: 0;
            transition: opacity 0.4s ease;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 1.5rem;
        }

        .portfolio-item:hover .portfolio-overlay {
            opacity: 1;
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
                    <h1 class="header-title m-0">Portfolio Manager</h1>
                    <a href="#" class="btn btn-gold"><i class="bi bi-upload me-2"></i>Upload Media</a>
                </div>

                <div class="row g-4">
                    <?php for ($i = 1; $i <= 9; $i++) : ?>
                        <div class="col-xl-4 col-lg-6">
                            <div class="portfolio-item card-solid">
                                <img src="https://picsum.photos/600/400?random=<?= $i ?>" class="img-fluid w-100 h-100 object-fit-cover" alt="Portfolio Image">
                                <div class="portfolio-overlay">
                                    <h5 class="text-light">Wedding in Tagaytay</h5>
                                    <p class="text-secondary small">Category: Weddings</p>
                                    <div>
                                        <button class="btn btn-sm btn-outline-light">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

            </div>
        </main>
    </div>

    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin.js"></script>
</body>

</html>