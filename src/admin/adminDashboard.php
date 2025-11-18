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
    <title>Admin Dashboard - Aperture</title>

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

</head>

<body class="admin-dashboard">
    <?php include_once 'components/sidebar.php'; ?>

    <div class="page-wrapper" id="page-wrapper">
        <?php include_once 'components/header.php'; ?>

        <main class="main-content">
            <div class="container-fluid p-0">

                <h1 class="header-title mb-4">Dashboard</h1>

                <!-- KPI Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="admin-card  card-solid kpi-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="kpi-title">Monthly Revenue</div>
                                    <div class="kpi-value">₱42,750</div>
                                </div>
                                <div class="kpi-icon">
                                    <canvas id="kpiChart1" width="80" height="40"></canvas>
                                </div>
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
                                <div class="kpi-icon">
                                    <canvas id="kpiChart2" width="80" height="40"></canvas>
                                </div>
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
                                <div class="kpi-icon">
                                    <canvas id="kpiChart3" width="80" height="40"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="admin-card card-solid kpi-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="kpi-title">Pending Invoices</div>
                                    <div class="kpi-value">₱8,200</div>
                                </div>
                                <div class="kpi-icon">
                                    <canvas id="kpiChart4" width="80" height="40"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="admin-card card-solid">
                            <h5 class="card-header-title">Revenue (YTD)</h5>
                            <div style="height: 350px;">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="admin-card card-solid">
                            <h5 class="card-header-title">Analytics</h5>
                            <div class="d-flex flex-column justify-content-between" style="height: 350px;">
                                <!-- Popular Packages -->
                                <div class="mb-4">
                                    <h6 class="analytics-title">Popular Packages</h6>
                                    <div class="d-flex flex-column gap-3">
                                        <div class="analytics-item">
                                            <div class="d-flex justify-content-between mb-1"><span class="item-name">Elite</span><span class="item-value">45%</span></div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar" style="width: 45%;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <div class="analytics-item">
                                            <div class="d-flex justify-content-between mb-1"><span class="item-name">Premium</span><span class="item-value">30%</span></div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar" style="width: 30%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <div class="analytics-item">
                                            <div class="d-flex justify-content-between mb-1"><span class="item-name">Essential</span><span class="item-value">25%</span></div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Popular Event Types -->
                                <div>
                                    <h6 class="analytics-title">Popular Event Types</h6>
                                    <div class="d-flex flex-column gap-3">
                                        <div class="analytics-item">
                                            <div class="d-flex justify-content-between mb-1"><span class="item-name">Weddings</span><span class="item-value">52%</span></div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar" style="width: 52%;" aria-valuenow="52" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <div class="analytics-item">
                                            <div class="d-flex justify-content-between mb-1"><span class="item-name">Corporate</span><span class="item-value">28%</span></div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar" style="width: 28%;" aria-valuenow="28" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <div class="analytics-item">
                                            <div class="d-flex justify-content-between mb-1"><span class="item-name">Celebrations</span><span class="item-value">20%</span></div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar" style="width: 20%;" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Appointments Table -->
                <div class="row g-4">
                    <div class="col-12">
                        <div class="admin-card card-solid p-0">
                            <h5 class="card-header-title p-3 mb-0">Recent Appointments</h5>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-3">Client</th>
                                            <th>Date</th>
                                            <th>Package</th>
                                            <th>Status</th>
                                            <th class="text-end pe-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="client-name ps-3">Stark Industries</td>
                                            <td>Nov 18, 2025</td>
                                            <td>Premium</td>
                                            <td><span class="status-badge status-confirmed">Confirmed</span></td>
                                            <td class="text-end pe-3"><a href="#" class="btn btn-sm btn-outline-secondary">View</a></td>
                                        </tr>
                                        <tr>
                                            <td class="client-name ps-3">Wayne Enterprises</td>
                                            <td>Nov 22, 2025</td>
                                            <td>Elite</td>
                                            <td><span class="status-badge status-confirmed">Confirmed</span></td>
                                            <td class="text-end pe-3"><a href="#" class="btn btn-sm btn-outline-secondary">View</a></td>
                                        </tr>
                                        <tr>
                                            <td class="client-name ps-3">Prestige Worldwide</td>
                                            <td>Nov 25, 2025</td>
                                            <td>Elite</td>
                                            <td><span class="status-badge status-pending">Pending Deposit</span></td>
                                            <td class="text-end pe-3"><a href="#" class="btn btn-sm btn-outline-secondary">View</a></td>
                                        </tr>
                                        <tr>
                                            <td class="client-name ps-3">Aperture Science</td>
                                            <td>Dec 02, 2025</td>
                                            <td>Essential</td>
                                            <td><span class="status-badge status-completed">Completed</span></td>
                                            <td class="text-end pe-3"><a href="#" class="btn btn-sm btn-outline-secondary">View</a></td>
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

    <!-- Local Bootstrap JS -->
    <script src="../../bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // --- Chart.js Initialization ---
            Chart.defaults.font.family = "Inter";
            Chart.defaults.color = "var(--text-secondary)"; /* Default text color for charts */
            Chart.defaults.plugins.legend.display = false;
            Chart.defaults.responsive = true;
            Chart.defaults.maintainAspectRatio = false;

            // --- Revenue Line Chart ---
            const ctxRevenue = document.getElementById('revenueChart')?.getContext('2d');
            if (ctxRevenue) {
                const revenueGradient = ctxRevenue.createLinearGradient(0, 0, 0, 380);
                revenueGradient.addColorStop(0, 'rgba(212, 175, 55, 0.3)');
                revenueGradient.addColorStop(1, 'rgba(212, 175, 55, 0)');

                new Chart(ctxRevenue, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'],
                        datasets: [{
                            label: 'Revenue (₱k)',
                            data: [15, 22, 18, 25, 30, 42, 38, 35, 45, 50, 42.75],
                            borderColor: 'var(--gold)',
                            backgroundColor: revenueGradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointBackgroundColor: 'var(--gold)',
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBorderColor: 'var(--card-bg)',
                            pointHoverBorderWidth: 2
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    color: 'var(--text-secondary)',
                                    callback: (value) => `₱${value}k`
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: 'var(--text-secondary)'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                backgroundColor: '#111',
                                titleFont: {
                                    weight: 'bold',
                                    family: 'Poppins'
                                },
                                bodyColor: 'var(--text-primary)',
                                padding: 12,
                                cornerRadius: 8,
                                intersect: false,
                                borderColor: 'var(--border-color)',
                                borderWidth: 1,
                                displayColors: false
                            }
                        }
                    }
                });
            }



            // --- KPI Sparkline Charts ---
            const kpiChartOptions = {
                type: 'line',
                options: {
                    scales: {
                        x: {
                            display: false
                        },
                        y: {
                            display: false
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: false
                        }
                    },
                    elements: {
                        line: {
                            borderWidth: 2,
                            tension: 0.4
                        },
                        point: {
                            radius: 0
                        }
                    },
                    maintainAspectRatio: false
                }
            };

            const kpiData = [{
                    id: 'kpiChart1',
                    data: [10, 20, 15, 30, 25, 42],
                    color: 'var(--gold)'
                },
                {
                    id: 'kpiChart2',
                    data: [5, 10, 8, 15, 12, 28],
                    color: 'var(--gold)'
                },
                {
                    id: 'kpiChart3',
                    data: [20, 15, 18, 10, 14, 12],
                    color: 'var(--gold)'
                },
                {
                    id: 'kpiChart4',
                    data: [5, 6, 9, 7, 8, 8.2],
                    color: 'var(--gold)'
                }
            ];

            kpiData.forEach(kpi => {
                const ctx = document.getElementById(kpi.id)?.getContext('2d');
                if (ctx) {
                    const gradient = ctx.createLinearGradient(0, 0, 0, 40);
                    gradient.addColorStop(0, 'rgba(212, 175, 55, 0.2)');
                    gradient.addColorStop(1, 'rgba(212, 175, 55, 0)');

                    new Chart(ctx, {
                        ...kpiChartOptions,
                        data: {
                            labels: Array(kpi.data.length).fill(''),
                            datasets: [{
                                data: kpi.data,
                                borderColor: kpi.color,
                                backgroundColor: gradient,
                                fill: true
                            }]
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>