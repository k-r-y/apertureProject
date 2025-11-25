document.addEventListener('DOMContentLoaded', function () {
    // Initialize Charts
    initDashboard();

    // Refresh Button Listener
    document.getElementById('refreshDashboard')?.addEventListener('click', function () {
        const btn = this;
        const icon = btn.querySelector('i');
        icon.classList.add('spin-animation'); // Add CSS animation class
        initDashboard().then(() => {
            setTimeout(() => icon.classList.remove('spin-animation'), 500);
        });
    });

    // Timeframe Filter Listener
    document.getElementById('timeframeFilter')?.addEventListener('change', function () {
        initDashboard(this.value);
    });
});

async function initDashboard(timeframe = 'month') {
    try {
        const response = await fetch(`api/get_analytics.php?action=all&timeframe=${timeframe}`);
        const result = await response.json();

        if (result.success) {
            updateMetrics(result.data);
            renderRevenueChart(result.data.revenue_trend);
            renderStatusChart(result.data.booking_status);
            renderActivityFeed(result.data.recent_activity);
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

function updateMetrics(data) {
    // Helper to animate numbers
    const animateValue = (id, value, prefix = '') => {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = prefix + Number(value).toLocaleString();
            // Simple scale animation
            el.style.transform = 'scale(1.1)';
            setTimeout(() => el.style.transform = 'scale(1)', 200);
        }
    };

    animateValue('stat-revenue', data.revenue, '₱');
    animateValue('stat-bookings', data.bookings);
    animateValue('stat-upcoming', data.upcoming);
    animateValue('stat-clients', data.new_clients);
}

let revenueChartInstance = null;
function renderRevenueChart(data) {
    const options = {
        series: [{
            name: 'Revenue',
            data: data.map(item => item.total)
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false },
            background: 'transparent'
        },
        colors: ['#D4AF37'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        xaxis: {
            categories: data.map(item => item.month),
            labels: { style: { colors: '#888' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { colors: '#888' },
                formatter: (value) => '₱' + value.toLocaleString()
            }
        },
        grid: {
            borderColor: 'rgba(255,255,255,0.1)',
            strokeDashArray: 4
        },
        theme: { mode: 'dark' }
    };

    const chartEl = document.querySelector("#revenueChart");
    if (chartEl) {
        if (revenueChartInstance) {
            revenueChartInstance.updateOptions(options);
        } else {
            revenueChartInstance = new ApexCharts(chartEl, options);
            revenueChartInstance.render();
        }
    }
}

let statusChartInstance = null;
function renderStatusChart(data) {
    // Ensure we have data for all statuses to avoid undefined errors
    const series = [
        parseInt(data.confirmed || 0),
        parseInt(data.pending || 0),
        parseInt(data.cancelled || 0)
    ];

    const options = {
        series: series,
        chart: {
            type: 'donut',
            height: 350,
            background: 'transparent'
        },
        labels: ['Confirmed', 'Pending', 'Cancelled'],
        colors: ['#4CAF50', '#FFC107', '#F44336'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: { color: '#888' },
                        value: { color: '#fff' },
                        total: {
                            show: true,
                            label: 'Total',
                            color: '#D4AF37'
                        }
                    }
                }
            }
        },
        stroke: { show: false },
        legend: {
            position: 'bottom',
            labels: { colors: '#fff' }
        },
        dataLabels: { enabled: false },
        theme: { mode: 'dark' }
    };

    const chartEl = document.querySelector("#statusChart");
    if (chartEl) {
        if (statusChartInstance) {
            statusChartInstance.updateOptions(options);
        } else {
            statusChartInstance = new ApexCharts(chartEl, options);
            statusChartInstance.render();
        }
    }
}

function renderActivityFeed(activities) {
    const container = document.getElementById('activityFeed');
    if (!container) return;

    if (activities.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">No recent activity</p>';
        return;
    }

    container.innerHTML = activities.map(item => {
        const date = new Date(item.event_date).toLocaleDateString();
        let statusColor = 'text-warning';
        if (item.booking_status === 'confirmed') statusColor = 'text-success';
        if (item.booking_status === 'cancelled') statusColor = 'text-danger';

        return `
            <div class="activity-item d-flex align-items-center mb-3 pb-3 border-bottom border-secondary" style="border-color: rgba(255,255,255,0.1) !important;">
                <div class="me-3">
                    <div class="rounded-circle bg-soft-gold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-calendar-check text-gold"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0 text-light" style="font-size: 0.9rem;">${item.FirstName} ${item.LastName}</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">${date}</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <small class="text-muted">Booking #${String(item.bookingID).padStart(6, '0')}</small>
                        <span class="badge bg-transparent border ${statusColor.replace('text-', 'border-')} ${statusColor}" style="font-size: 0.7rem;">
                            ${item.booking_status}
                        </span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}
