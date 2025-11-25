/**
 * Comprehensive Admin Dashboard JavaScript
 * Handles data fetching, chart rendering, and timeframe filtering
 */

let dashboardData = null;
let refreshInterval = null;

// Initialize dashboard on page load
document.addEventListener('DOMContentLoaded', function () {
    console.log('Dashboard loading...');

    // Setup filter listener
    const filter = document.getElementById('timeframeFilter');
    if (filter) {
        filter.addEventListener('change', function () {
            loadDashboardData(this.value);
        });
    }

    // Setup refresh button
    const refreshBtn = document.getElementById('refreshDashboard');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function () {
            const currentFilter = filter ? filter.value : 'month';
            loadDashboardData(currentFilter);
        });
    }

    testDatabaseConnection();
});

/**
 * Test database connection first
 */
function testDatabaseConnection() {
    console.log('Testing database connection...');

    fetch('api/test_db.php')
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Test response:', data);

            if (data.success) {
                console.log('✅ Database OK! Loading dashboard...');
                // Load with default filter (month)
                loadDashboardData('month');
            } else {
                console.error('❌ Database test failed:', data.error);
                showError('Database connection failed: ' + data.error);
            }
        })
        .catch(error => {
            console.error('❌ Test fetch error:', error);
            showError('Network error: ' + error.message);
        });
}

/**
 * Load all dashboard data
 */
function loadDashboardData(timeframe = 'month') {
    console.log('Fetching dashboard metrics for:', timeframe);

    fetch(`api/get_dashboard_metrics.php?timeframe=${timeframe}`)
        .then(response => {
            console.log('Metrics response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response body:', text);
                    throw new Error('HTTP ' + response.status + ': ' + text);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Dashboard data received:', data);

            if (data.success) {
                dashboardData = data;
                updateStatCards();
                renderCharts();
                updateActivityFeed();
                updateUpcomingEvents();
                setupAutoRefresh();
            } else {
                console.error('Dashboard error:', data);
                showError('Failed to load dashboard: ' + (data.debug || data.error));
            }
        })
        .catch(error => {
            console.error('Error loading dashboard:', error);
            showError('Network error: ' + error.message);
        });
}

/**
 * Update stat cards with real data
 */
function updateStatCards() {
    console.log('Updating stat cards...');

    // Total Revenue
    const totalRevenue = dashboardData.revenue.total;
    document.getElementById('stat-revenue').textContent = '₱' + numberFormat(totalRevenue);

    // Revenue Growth
    const growthPercent = dashboardData.revenue.growth_percentage;
    const growthElem = document.getElementById('revenue-growth');
    if (growthElem) {
        growthElem.textContent = (growthPercent >= 0 ? '+' : '') + growthPercent.toFixed(1) + '%';
        growthElem.className = 'stat-trend ' + (growthPercent >= 0 ? 'trend-up' : 'trend-down');
    }

    // Total Bookings
    document.getElementById('stat-bookings').textContent = dashboardData.bookings.total;

    // Upcoming Events
    document.getElementById('stat-upcoming').textContent = dashboardData.bookings.upcoming;

    // New Clients
    document.getElementById('stat-clients').textContent = dashboardData.clients.new_clients;

    // Average Booking Value
    const avgValue = dashboardData.bookings.average_value;
    document.getElementById('stat-avg-value').textContent = '₱' + numberFormat(avgValue);

    // Conversion Rate
    const conversionRate = dashboardData.conversion.conversion_rate;
    document.getElementById('stat-conversion').textContent = conversionRate.toFixed(1) + '%';

    // Retention Rate
    const retentionRate = dashboardData.clients.retention_rate;
    document.getElementById('stat-retention').textContent = retentionRate.toFixed(1) + '%';

    console.log('✅ Stat cards updated');
}

// Store chart instances
let charts = {
    revenue: null,
    bookings: null,
    status: null,
    package: null,
    eventType: null
};

/**
 * Render all charts
 */
function renderCharts() {
    console.log('Rendering charts...');
    renderRevenueChart();
    renderBookingsChart();
    renderStatusChart();
    renderPackageChart();
    renderEventTypeChart();
    console.log('✅ Charts rendered');
}

/**
 * Revenue Trend Chart (Line)
 */
function renderRevenueChart() {
    // Destroy existing chart if it exists
    if (charts.revenue) {
        charts.revenue.destroy();
    }

    const months = dashboardData.revenue_trend.map(item => item.month);
    const revenue = dashboardData.revenue_trend.map(item => item.revenue);

    const options = {
        series: [{
            name: 'Revenue',
            data: revenue
        }],
        chart: {
            type: 'area',
            height: 350,
            background: 'transparent',
            toolbar: {
                show: false
            },
            animations: {
                enabled: true
            }
        },
        colors: ['#D4AF37'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.2,
            }
        },
        xaxis: {
            categories: months,
            labels: {
                style: {
                    colors: '#999'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#999'
                },
                formatter: function (val) {
                    return '₱' + numberFormat(val);
                }
            }
        },
        grid: {
            borderColor: 'rgba(255,255,255,0.1)'
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function (val) {
                    return '₱' + numberFormat(val);
                }
            }
        }
    };

    charts.revenue = new ApexCharts(document.querySelector("#revenueChart"), options);
    charts.revenue.render();
}

/**
 * Monthly Bookings Chart (Bar)
 */
function renderBookingsChart() {
    if (charts.bookings) {
        charts.bookings.destroy();
    }

    const months = dashboardData.bookings_trend.map(item => item.month);
    const bookings = dashboardData.bookings_trend.map(item => item.count);

    const options = {
        series: [{
            name: 'Bookings',
            data: bookings
        }],
        chart: {
            type: 'bar',
            height: 350,
            background: 'transparent',
            toolbar: {
                show: false
            }
        },
        colors: ['#D4AF37'],
        plotOptions: {
            bar: {
                borderRadius: 8,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: true,
            style: {
                colors: ['#999']
            },
            offsetY: -20
        },
        xaxis: {
            categories: months,
            labels: {
                style: {
                    colors: '#999'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#999'
                }
            }
        },
        grid: {
            borderColor: 'rgba(255,255,255,0.1)'
        },
        tooltip: {
            theme: 'dark'
        }
    };

    charts.bookings = new ApexCharts(document.querySelector("#bookingsChart"), options);
    charts.bookings.render();
}

/**
 * Booking Status Chart (Donut)
 */
function renderStatusChart() {
    if (charts.status) {
        charts.status.destroy();
    }

    const statuses = Object.keys(dashboardData.status_breakdown);
    const counts = Object.values(dashboardData.status_breakdown);

    const options = {
        series: counts,
        chart: {
            type: 'donut',
            height: 350,
            background: 'transparent'
        },
        labels: statuses,
        colors: ['#4CAF50', '#D4AF37', '#2196F3', '#FF9800', '#F44336'],
        legend: {
            position: 'bottom',
            labels: {
                colors: '#999'
            }
        },
        dataLabels: {
            style: {
                colors: ['#fff']
            }
        },
        tooltip: {
            theme: 'dark'
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            color: '#999',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        }
    };

    charts.status = new ApexCharts(document.querySelector("#statusChart"), options);
    charts.status.render();
}

/**
 * Package Performance Chart (Bar)
 */
function renderPackageChart() {
    if (charts.package) {
        charts.package.destroy();
    }

    const packages = dashboardData.package_performance.map(item => item.name);
    const revenue = dashboardData.package_performance.map(item => item.revenue);

    const options = {
        series: [{
            name: 'Revenue',
            data: revenue
        }],
        chart: {
            type: 'bar',
            height: 350,
            background: 'transparent',
            toolbar: {
                show: false
            }
        },
        colors: ['#D4AF37'],
        plotOptions: {
            bar: {
                horizontal: true,
                borderRadius: 8
            }
        },
        xaxis: {
            categories: packages,
            labels: {
                style: {
                    colors: '#999'
                },
                formatter: function (val) {
                    return '₱' + numberFormat(val);
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#999'
                }
            }
        },
        grid: {
            borderColor: 'rgba(255,255,255,0.1)'
        },
        tooltip: {
            theme: 'dark',
            y: {
                formatter: function (val) {
                    return '₱' + numberFormat(val);
                }
            }
        }
    };

    charts.package = new ApexCharts(document.querySelector("#packageChart"), options);
    charts.package.render();
}

/**
 * Event Type Distribution Chart (Pie)
 */
function renderEventTypeChart() {
    if (charts.eventType) {
        charts.eventType.destroy();
    }

    const types = dashboardData.event_types.map(item => item.type);
    const counts = dashboardData.event_types.map(item => item.count);

    const options = {
        series: counts,
        chart: {
            type: 'pie',
            height: 350,
            background: 'transparent'
        },
        labels: types,
        colors: ['#D4AF37', '#4CAF50', '#2196F3', '#FF9800', '#F44336', '#9C27B0', '#00BCD4', '#CDDC39'],
        legend: {
            position: 'bottom',
            labels: {
                colors: '#999'
            }
        },
        dataLabels: {
            style: {
                colors: ['#fff']
            }
        },
        tooltip: {
            theme: 'dark'
        }
    };

    charts.eventType = new ApexCharts(document.querySelector("#eventTypeChart"), options);
    charts.eventType.render();
}

/**
 * Update recent activity feed
 */
function updateActivityFeed() {
    const container = document.getElementById('activityFeed');
    if (!container) return;

    let html = '';
    dashboardData.recent_activity.forEach(activity => {
        const statusClass = getStatusClass(activity.status);
        const timeAgo = getTimeAgo(activity.created_at);

        html += `
            <div class="activity-item">
                <div class="activity-icon">${getStatusIcon(activity.status)}</div>
                <div class="activity-content">
                    <div class="activity-title">${activity.event_type}</div>
                    <div class="activity-meta">
                        <span class="status-badge ${statusClass}">${activity.status}</span>
                        <span class="text-muted">• ${timeAgo}</span>
                    </div>
                    <div class="activity-client text-muted small">${activity.client}</div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html || '<p class="text-muted text-center">No recent activity</p>';
}

/**
 * Update upcoming events
 */
function updateUpcomingEvents() {
    const container = document.getElementById('upcomingEvents');
    if (!container) return;

    let html = '';
    dashboardData.upcoming_events.forEach(event => {
        const dateFormatted = formatDate(event.date);
        const timeFormatted = formatTime(event.time);

        html += `
            <div class="upcoming-event-item">
                <div class="event-date">
                    <div class="event-day">${new Date(event.date).getDate()}</div>
                    <div class="event-month">${new Date(event.date).toLocaleString('default', { month: 'short' })}</div>
                </div>
                <div class="event-details">
                    <div class="event-title">${event.event_type}</div>
                    <div class="event-meta text-muted small">
                        <i class="bi bi-clock"></i> ${timeFormatted}
                        <span class="mx-2">•</span>
                        <i class="bi bi-person"></i> ${event.client}
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html || '<p class="text-muted text-center py-4">No upcoming events</p>';
}

/**
 * Setup auto-refresh every 5 minutes
 */
function setupAutoRefresh() {
    if (refreshInterval) clearInterval(refreshInterval);

    refreshInterval = setInterval(() => {
        console.log('Auto-refreshing dashboard...');
        const filter = document.getElementById('timeframeFilter');
        const currentFilter = filter ? filter.value : 'month';
        loadDashboardData(currentFilter);
    }, 5 * 60 * 1000); // 5 minutes
}

/**
 * Helper: Format number with commas
 */
function numberFormat(num) {
    return parseFloat(num).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Helper: Get status class
 */
function getStatusClass(status) {
    const classes = {
        'pending': 'status-pending',
        'confirmed': 'status-confirmed',
        'completed': 'status-completed',
        'cancelled': 'status-cancelled',
        'post_production': 'status-production'
    };
    return classes[status] || 'status-pending';
}

/**
 * Helper: Get status icon
 */
function getStatusIcon(status) {
    const icons = {
        'pending': '<i class="bi bi-clock"></i>',
        'confirmed': '<i class="bi bi-check-circle"></i>',
        'completed': '<i class="bi bi-check-all"></i>',
        'cancelled': '<i class="bi bi-x-circle"></i>',
        'post_production': '<i class="bi bi-camera-video"></i>'
    };
    return icons[status] || '<i class="bi bi-circle"></i>';
}

/**
 * Helper: Get time ago
 */
function getTimeAgo(datetime) {
    const now = new Date();
    const past = new Date(datetime);
    const diffMs = now - past;
    const diffMins = Math.floor(diffMs / 60000);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;

    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `${diffHours}h ago`;

    const diffDays = Math.floor(diffHours / 24);
    return `${diffDays}d ago`;
}

/**
 * Helper: Format date
 */
function formatDate(date) {
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
}

/**
 * Helper: Format time
 */
function formatTime(time) {
    if (!time) return '';
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
}

/**
 * Helper: Show error message
 */
function showError(message) {
    console.error("Error:", message);
    // alert('Dashboard Error: ' + message); // Disabled alert to be less annoying
}
