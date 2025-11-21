<?php

require_once '../includes/functions/session.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/sweetalert2.min.css">
    <link rel="icon" href="./assets/camera.png" type="image/x-icon">
    <link rel="stylesheet" href="../bootstrap-5.3.8-dist/font/bootstrap-icons.css">

    <title>Elite Visuals - Admin Dashboard (Heritage)</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <script src="https.cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Old+Standard+TT:ital,wght@0,400;0,700;1,400&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <style>
        /* 2. ADAPTED "HERITAGE" DESIGN SYSTEM */
        :root {
            --sidebar-width: 260px;
            --sidebar-mini-width: 80px;
        }

        /* 3. NEW ADMIN STYLES (Using Heritage Palette) */
        body {
            font-family: "Inter", sans-serif;
            background-color: var(--admin-bg);
            color: var(--text-primary);
            font-size: 0.9rem; /* Denser */
            overflow-x: hidden;
        }

        /* Main Serif Header */
        .header-title {
            font-family: "Old Standard TT", serif;
            font-weight: 700;
            color: var(--gold);
            font-size: 2.25rem;
        }

        /* The main solid card class */
        .card-solid {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 6px; /* Sharper radius */
            box-shadow: none;
            height: 100%;
            transition: border-color 0.3s ease;
        }
        .card-solid:hover {
            border-color: var(--gold-hover);
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background-color: var(--card-bg);
            border-right: 1px solid var(--border-color);
            padding: 1rem;
            transition: width 0.3s ease;
            z-index: 100;
            overflow-x: hidden;
        }

        .sidebar-brand {
            font-family: "Poppins", sans-serif;
            font-weight: 600;
            font-size: 1.5rem;
            color: var(--gold); /* Your gold */
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-bottom: 1rem;
            white-space: nowrap;
            border-bottom: 1px solid var(--border-color);
        }
        .sidebar-brand .brand-icon {
            font-size: 1.8rem;
            margin-right: 0.75rem;
            transition: margin 0.3s ease;
        }

        .sidebar-nav { list-style: none; padding: 1rem 0 0 0; }
        .nav-section-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            padding: 0.75rem 0.75rem 0.25rem;
            white-space: nowrap;
        }

        .sidebar-nav-link {
            display: flex;
            align-items: center;
            color: var(--text-primary);
            font-family: "Poppins", sans-serif;
            font-weight: 400;
            text-decoration: none;
            padding: 0.2rem 1rem; /* Tight padding */
            border-radius: 6px;
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
            background-color: var(--gold-hover); /* Gold hover */
            color: var(--gold);
        }
        .sidebar-nav-link.active {
            background-color: var(--gold-active-bg); /* Your gold */
            color: var(--gold-active-text); /* Your dark text */
            font-weight: 600;
        }
        .sidebar-nav-link.active .nav-icon {
            color: var(--gold-active-text);
        }
        
        /* Main Layout */
        .page-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
        }

        /* Solid Header */
        .header {
            position: sticky;
            top: 0;
            z-index: 90;
            background-color: var(--admin-bg); /* Match body bg */
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 1.5rem; /* Tight */
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-toggle {
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-primary);
        }
        .header-profile { display: flex; align-items: center;  }
        .header-profile .profile-img {
            width: 32px; height: 32px;
            border-radius: 50%;
            margin-left: 1rem;
            border: 2px solid var(--border-color);
        }

        .main-content {
            padding: 1.5rem; /* Tight padding */
        }
        
        /* Dense Content Cards */
        .admin-card {
            padding: 1.25rem; /* Tight */
            height: 100%;
        }

        .card-header-title {
            font-family: "Old Standard TT", serif;
            font-weight: 700;
            font-size: 1.4rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.75rem;
        }

        /* KPI Card */
        .kpi-card .kpi-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
        }
        .kpi-card .kpi-value {
            font-family: "Poppins", sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
        }
        .kpi-card .kpi-icon {
            font-size: 1.5rem;
            color: var(--gold);
        }
        
        /* Table */
        .table {
            color: var(--text-primary);
            font-size: 0.85rem;
            border: none;
        }
        .table thead th {
             background-color: transparent;
             color: var(--gold);
             font-family: "Poppins", sans-serif;
             font-size: 0.75rem;
             text-transform: uppercase;
             border-bottom: 2px solid var(--border-color);
        }
        .table tbody td {
            border-color: var(--border-color);
            vertical-align: middle;
        }
        .table .client-name { font-weight: 600; color: var(--text-primary); }
        .table-hover tbody tr:hover { 
            background-color: var(--gold-hover); 
            color: var(--text-primary);
        }
        
        .status-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-confirmed { background-color: rgba(212, 175, 55, 0.2); color: var(--gold);}
        .status-pending { background-color: rgba(255, 184, 0, 0.15); color: #FFB800; }
        .status-completed { background-color: rgba(144, 238, 144, 0.15); color: #90ee90; }

        /* Mini-sidebar */
        body.sidebar-mini .sidebar { width: var(--sidebar-mini-width); }
        body.sidebar-mini .page-wrapper { margin-left: var(--sidebar-mini-width); }
        body.sidebar-mini .sidebar-brand .brand-text,
        body.sidebar-mini .sidebar-nav-link .nav-text,
        body.sidebar-mini .nav-section-title {
            opacity: 0; visibility: hidden; width: 0;
        }
        body.sidebar-mini .sidebar-brand { justify-content: center; }
        body.sidebar-mini .sidebar-brand .brand-icon { margin-right: 0; }
        body.sidebar-mini .sidebar-nav-link { justify-content: center; }
        body.sidebar-mini .sidebar-nav-link .nav-icon { margin-right: 0; }
        
        @media (max-width: 768px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width)); width: var(--sidebar-width); z-index: 1050;
            }
            body.sidebar-mobile-active .sidebar { left: 0; }
            body.sidebar-mobile-active .sidebar-brand .brand-text,
            body.sidebar-mobile-active .sidebar-nav-link .nav-text,
            body.sidebar-mobile-active .nav-section-title {
                display: block; opacity: 1; visibility: visible; width: auto;
            }
            body.sidebar-mobile-active .sidebar-brand { justify-content: center; }
            body.sidebar-mobile-active .sidebar-brand .brand-icon { margin-right: 0.75rem; }
            body.sidebar-mobile-active .sidebar-nav-link { justify-content: flex-start; }
            body.sidebar-mobile-active .sidebar-nav-link .nav-icon { margin-right: 1.25rem; }
            .page-wrapper { margin-left: 0; }
        }

        .divider{
            margin: 0 !important;
            padding: 0 !important;
            padding-bottom: .5rem !important;
        }
    </style>
</head>
<body class=""> <nav class="sidebar" id="sidebar">
        

        <a class="sidebar-brand" href="index.php#home"><img src="./assets/logo.png" alt="" style="height: 30px;"></a>

        <ul class="sidebar-nav">
            <li class="nav-section-title"><span class="nav-text">Main</span></li>
             <hr class="divider">
            <li class="sidebar-nav-item">
                <a href="#" class="sidebar-nav-link active">
                    <i class="bi bi-grid-1x2-fill nav-icon"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#" class="sidebar-nav-link">
                    <i class="bi bi-calendar-month-fill nav-icon"></i>
                    <span class="nav-text">Calendar</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#" class="sidebar-nav-link">
                    <i class="bi bi-journal-album nav-icon"></i>
                    <span class="nav-text">Enquiries</span>
                </a>
            </li>
            <li class="nav-section-title"><span class="nav-text">Management</span></li>
            <hr class="divider">
            <li class="sidebar-nav-item">
                <a href="#" class="sidebar-nav-link">
                    <i class="bi bi-calendar2-check-fill nav-icon"></i>
                    <span class="nav-text">Appointments</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#" class="sidebar-nav-link">
                    <i class="bi bi-people-fill nav-icon"></i>
                    <span class="nav-text">Client Directory</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#" class="sidebar-nav-link">
                    <i class="bi bi-receipt-cutoff nav-icon"></i>
                    <span class="nav-text">Invoicing</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#" class="sidebar-nav-link">
                    <i class="bi bi-camera-fill nav-icon"></i>
                    <span class="nav-text">Team & Gear</span>
                </a>
            </li>
            <li class="nav-section-title"><span class="nav-text">Content</span></li>
             <hr class="divider">
            <li class="sidebar-nav-item">
                <a href="#" class="sidebar-nav-link">
                    <i class="bi bi-box-fill nav-icon"></i>
                    <span class="nav-text">Services & Packages</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#" class="sidebar-nav-link">
                    <i class="bi bi-images nav-icon"></i>
                    <span class="nav-text">Portfolio Manager</span>
                </a>
            </li>
            <li class="nav-section-title"><span class="nav-text">System</span></li>
             <hr class="divider">
            <li class="sidebar-nav-item">
                <a href="#" class="sidebar-nav-link">
                    <i class="bi bi-gear-fill nav-icon"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </li>
            <li class="sidebar-nav-item">
                <a href="#" class="sidebar-nav-link">
                    <i class="bi bi-box-arrow-left nav-icon"></i>
                    <span class="nav-text">Logout</span>
                </a>
            </li>

            <li class="sidebar-nav-item mt-5">
                  <div class="header-profile">
                <span class="ms-3 d-none d-lg-block">
                    <div class="fw-semibold" style="font-size: 0.9rem;">Admin</div>
                    <div class="small" style="font-size: 0.8rem; color: var(--text-secondary);">Prince Andrew Casiano</div>
                </span>
                <img src="https://i.pravatar.cc/150?img=5" alt="Profile" class="profile-img">
            </div>
            </li>
        </ul>
    </nav>

    <div class="page-wrapper" id="page-wrapper">

      

        <main class="main-content">
            <div class="container-fluid p-0">

                <h1 class="header-title mb-3">Dashboard</h1>

                <div class="row g-3 mb-3">
                    <div class="col-xl-3 col-md-6">
                        <div class="admin-card card-solid kpi-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="kpi-title">Monthly Revenue</div>
                                    <div class="kpi-value">$42,750</div>
                                </div>
                                <div class="kpi-icon"><i class="bi bi-cash-stack"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="admin-card card-solid kpi-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="kpi-title">New Enquiries</div>
                                    <div class="kpi-value">28</div>
                                </div>
                                <div class="kpi-icon"><i class="bi bi-journal-album"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="admin-card card-solid kpi-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="kpi-title">Upcoming Events</div>
                                    <div class="kpi-value">12</div>
                                </div>
                                <div class="kpi-icon"><i class="bi bi-calendar-week"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="admin-card card-solid kpi-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="kpi-title">Pending Invoices</div>
                                    <div class="kpi-value">$8,200</div>
                                </div>
                                <div class="kpi-icon"><i class="bi bi-receipt"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-lg-4">
                        <div class="admin-card card-solid">
                            <h5 class="card-header-title">Revenue (YTD)</h5>
                            <div style="height: 350px;">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="admin-card card-solid">
                            <h5 class="card-header-title">Popular Event Types</h5>
                            <div style="height: 350px;">
                                <canvas id="serviceChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="admin-card card-solid">
                            <h5 class="card-header-title">Popular Packages</h5>
                            <div style="height: 350px;">
                                <canvas id="packageChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <div class="admin-card card-solid p-0"> <h5 class="card-header-title p-3 mb-0">Recent Appointments</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Date</th>
                                            <th>Package</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="client-name">Stark Industries</td>
                                            <td>Nov 18, 2025</td>
                                            <td>Premium</td>
                                            <td><span class="status-badge status-confirmed">Confirmed</span></td>
                                        </tr>
                                        <tr>
                                            <td class="client-name">Wayne Enterprises</td>
                                            <td>Nov 22, 2025</td>
                                            <td>Elite</td>
                                            <td><span class="status-badge status-confirmed">Confirmed</span></td>
                                        </tr>
                                        <tr>
                                            <td class="client-name">Prestige Worldwide</td>
                                            <td>Nov 25, 2025</td>
                                            <td>Elite</td>
                                            <td><span class="status-badge status-pending">Pending Deposit</span></td>
                                        </tr>
                                        <tr>
                                            <td class="client-name">Aperture Science</td>
                                            <td>Dec 02, 2025</td>
                                            <td>Essential</td>
                                            <td><span class="status-badge status-completed">Completed</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // --- 1. Sidebar Toggle Logic ---
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const body = document.body;

        sidebarToggle.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                body.classList.toggle('sidebar-mobile-active');
            } else {
                body.classList.toggle('sidebar-mini');
            }
        });

        // --- 2. Chart.js Initialization ---
        
        // Use your design system fonts and colors
        Chart.defaults.font.family = "Inter";
        Chart.defaults.color = "var(--text-secondary)";
        Chart.defaults.plugins.legend.display = false;
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;

        // --- Revenue Line Chart ---
        const ctxRevenue = document.getElementById('revenueChart');
        if (ctxRevenue) {
            const revenueGradient = ctxRevenue.getContext('2d').createLinearGradient(0, 0, 0, 350);
            revenueGradient.addColorStop(0, 'rgba(212, 175, 55, 0.4)'); // Your gold color
            revenueGradient.addColorStop(1, 'rgba(212, 175, 55, 0.0)');

            new Chart(ctxRevenue, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
                    datasets: [{
                        label: 'Revenue ($k)',
                        data: [15, 22, 18, 25, 30, 42, 38, 35, 45, 50, 42],
                        borderColor: 'var(--gold)',
                        backgroundColor: revenueGradient,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: 'var(--gold)',
                        pointRadius: 0,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'var(--border-color)', drawBorder: false },
                            ticks: { color: 'var(--text-secondary)', callback: (value) => `$${value}k` }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: 'var(--text-secondary)' }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'var(--admin-bg)',
                            titleFont: { weight: 'bold', family: 'Poppins' },
                            bodyColor: 'var(--text-primary)',
                            padding: 10,
                            cornerRadius: 4,
                            intersect: false,
                            borderColor: 'var(--border-color)',
                            borderWidth: 1
                        }
                    }
                }
            });
        }

        // --- Popular Event Types (Doughnut) ---
        const ctxService = document.getElementById('serviceChart');
        if (ctxService) {
            new Chart(ctxService, {
                type: 'doughnut',
                data: {
                    labels: ['Weddings', 'Corporate', 'Portraits', 'Brand'],
                    datasets: [{
                        data: [45, 25, 15, 15],
                        backgroundColor: [
                            'var(--gold)', 
                            'rgba(212, 175, 55, 0.6)', // Lighter gold
                            'var(--text-primary)',
                            'var(--text-secondary)'
                        ],
                        borderColor: 'var(--card-bg)',
                        borderWidth: 4,
                        hoverOffset: 8
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: { usePointStyle: true, boxWidth: 10, padding: 15, color: 'var(--text-primary)' }
                        },
                        tooltip: { callbacks: { label: (context) => ` ${context.label}: ${context.parsed}%` } }
                    }
                }
            });
        }

        // --- Popular Packages (Doughnut) ---
        const ctxPackage = document.getElementById('packageChart');
        if (ctxPackage) {
            new Chart(ctxPackage, {
                type: 'doughnut',
                data: {
                    labels: ['Elite', 'Premium', 'Essential'], // Your package names
                    datasets: [{
                        data: [75, 55, 45], // Dummy data
                        backgroundColor: [
                            'var(--gold)', 
                            'var(--text-primary)',
                            'var(--text-secondary)'
                        ],
                        borderColor: 'var(--card-bg)',
                        borderWidth: 4,
                        hoverOffset: 8
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: { usePointStyle: true, boxWidth: 10, padding: 15, color: 'var(--text-primary)' }
                        },
                        tooltip: { 
                            callbacks: { label: (context) => ` ${context.label}: ${context.parsed} bookings` } 
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>