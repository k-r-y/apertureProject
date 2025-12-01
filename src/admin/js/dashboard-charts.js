document.addEventListener('DOMContentLoaded', function () {
    // Initialize Charts with saved state or default
    const savedTimeframe = sessionStorage.getItem('dashboardTimeframe') || 'month';

    // Set dropdown value
    const timeframeSelect = document.getElementById('timeframeFilter');
    if (timeframeSelect) {
        timeframeSelect.value = savedTimeframe;
    }

    initDashboard(savedTimeframe);

    // Refresh Button Listener
    document.getElementById('refreshDashboard')?.addEventListener('click', function () {
        const btn = this;
        const icon = btn.querySelector('i');
        icon.classList.add('spin-animation');
        // Use current dropdown value
        const currentTimeframe = document.getElementById('timeframeFilter')?.value || 'month';
        initDashboard(currentTimeframe).then(() => {
            setTimeout(() => icon.classList.remove('spin-animation'), 500);
        });
    });

    // Timeframe Filter Listener
    document.getElementById('timeframeFilter')?.addEventListener('change', function () {
        const selectedTimeframe = this.value;
        sessionStorage.setItem('dashboardTimeframe', selectedTimeframe);
        initDashboard(selectedTimeframe);
    });
});

async function initDashboard(timeframe = 'month') {
    try {
        // Use the new metrics API with cache busting
        const response = await fetch(`api/get_dashboard_metrics.php?timeframe=${timeframe}&_=${new Date().getTime()}`);
        const result = await response.json();

        if (result.success) {
            // Map the new API structure to the UI functions
            updateMetrics({
                revenue: result.revenue.total,
                bookings: result.bookings.total,
                pending_count: result.bookings.pending,
                avg_value: result.bookings.average_value
            });

            renderRevenueChart(result.revenue_trend);
            renderActionCenter(result.recent_activity, result.upcoming_events);
            renderTopPackagesChart(result.package_performance);
            renderBookingStatusChart(result.status_breakdown);
            renderEventTypeChart(result.event_types);
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

function updateMetrics(data) {
    const animateValue = (id, value, prefix = '') => {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = prefix + Number(value).toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 2 });
            el.style.transform = 'scale(1.1)';
            setTimeout(() => el.style.transform = 'scale(1)', 200);
        }
    };

    animateValue('stat-revenue', data.revenue, '₱');
    animateValue('stat-bookings', data.bookings);
    animateValue('stat-pending', data.pending_count);
    animateValue('stat-avg-value', data.avg_value, '₱');
}

let revenueChartInstance = null;
function renderRevenueChart(data) {
    const options = {
        series: [{
            name: 'Revenue',
            data: data.map(item => item.revenue)
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: { show: false },
            background: 'transparent',
            fontFamily: 'Inter, sans-serif',
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
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
                style: { colors: '#D4AF37' },
                formatter: (value) => '₱' + value.toLocaleString()
            }
        },
        grid: {
            borderColor: 'rgba(255,255,255,0.05)',
            strokeDashArray: 4
        },
        theme: { mode: 'dark' },
        tooltip: {
            theme: 'dark',
            y: { formatter: function (val) { return "₱" + val.toLocaleString() } }
        }
    };

    const chartEl = document.querySelector("#revenueBookingChart");
    if (chartEl) {
        if (revenueChartInstance) revenueChartInstance.destroy();
        revenueChartInstance = new ApexCharts(chartEl, options);
        revenueChartInstance.render();
    }
}

function renderActionCenter(pending, upcoming) {
    // Render Pending
    const pendingContainer = document.getElementById('pendingFeed');
    if (pendingContainer) {
        if (!pending || pending.length === 0) {
            pendingContainer.innerHTML = '<p class="text-muted text-center py-4">No pending requests.</p>';
        } else {
            pendingContainer.innerHTML = pending.map(item => `
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary" style="border-color: rgba(255,255,255,0.1) !important;">
                    <div>
                        <h6 class="mb-0 text-light" style="font-size: 0.9rem;">${item.FirstName} ${item.LastName}</h6>
                        <small class="text-muted">₱${Number(item.total_amount).toLocaleString()}</small>
                    </div>
                    <a href="bookings.php?id=${item.bookingID}" class="btn btn-sm btn-outline-gold" style="font-size: 0.75rem;">Review</a>
                </div>
            `).join('');
        }
    }

    // Render Upcoming
    const upcomingContainer = document.getElementById('upcomingFeed');
    if (upcomingContainer) {
        if (!upcoming || upcoming.length === 0) {
            upcomingContainer.innerHTML = '<p class="text-muted text-center py-4">No upcoming events this week.</p>';
        } else {
            upcomingContainer.innerHTML = upcoming.map(item => {
                const date = new Date(item.event_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                return `
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3 text-center" style="min-width: 40px;">
                        <div class="text-gold fw-bold" style="font-size: 0.8rem;">${date}</div>
                        <div class="text-muted" style="font-size: 0.7rem;">${item.event_time_start.substring(0, 5)}</div>
                    </div>
                    <div>
                        <h6 class="mb-0 text-light" style="font-size: 0.9rem;">${item.event_type}</h6>
                        <small class="text-muted">${item.FirstName} ${item.LastName}</small>
                    </div>
                </div>
            `}).join('');
        }
    }
}

let packageChartInstance = null;
function renderTopPackagesChart(data) {
    // Safety check: if no data or empty series, clear chart or show message
    if (!data || !data.series || data.series.length === 0) {
        const chartEl = document.querySelector("#topPackagesChart");
        if (chartEl) chartEl.innerHTML = '<p class="text-muted text-center py-5">No package data available for this period.</p>';
        return;
    }

    const options = {
        series: data.series,
        chart: {
            type: 'bar',
            height: 350,
            stacked: true, // Enable stacking
            toolbar: { show: false },
            background: 'transparent',
            fontFamily: 'Inter, sans-serif'
        },
        plotOptions: {
            bar: {
                horizontal: true,
                barHeight: '60%',
                borderRadius: 2
            }
        },
        colors: ['#D4AF37', '#C5A028', '#B69119', '#A7820A', '#987300', '#896500', '#7A5600'], // Gold Palette
        dataLabels: { enabled: false },
        xaxis: {
            categories: data.categories,
            labels: { style: { colors: '#888' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { style: { colors: '#fff' } }
        },
        grid: {
            borderColor: 'rgba(255,255,255,0.05)',
            xaxis: { lines: { show: true } },
            yaxis: { lines: { show: false } }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left',
            labels: { colors: '#fff' }
        },
        theme: { mode: 'dark' },
        tooltip: {
            theme: 'dark',
            y: { formatter: function (val) { return val + " bookings" } }
        }
    };

    const chartEl = document.querySelector("#topPackagesChart");
    if (chartEl) {
        chartEl.innerHTML = ''; // Clear previous content/message
        if (packageChartInstance) packageChartInstance.destroy();
        packageChartInstance = new ApexCharts(chartEl, options);
        packageChartInstance.render();
    }
}

let statusChartInstance = null;
function renderBookingStatusChart(data) {
    // Safety check
    if (!data) return;

    const series = [
        parseInt(data.confirmed || 0),
        parseInt(data.pending || 0),
        parseInt(data.cancelled || 0)
    ];

    const options = {
        series: series,
        chart: {
            type: 'donut',
            height: 250,
            background: 'transparent',
            fontFamily: 'Inter, sans-serif'
        },
        labels: ['Confirmed', 'Pending', 'Cancelled'],
        colors: ['#198754', '#ffc107', '#dc3545'],
        plotOptions: {
            pie: {
                donut: {
                    size: '75%',
                    labels: {
                        show: true,
                        name: { color: '#888', offsetY: -10 },
                        value: { color: '#fff', fontSize: '24px', offsetY: 5 },
                        total: {
                            show: true,
                            label: 'Total',
                            color: '#D4AF37',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
                            }
                        }
                    }
                }
            }
        },
        stroke: { show: false },
        legend: { position: 'right', labels: { colors: '#fff' } },
        dataLabels: { enabled: false },
        theme: { mode: 'dark' }
    };

    const chartEl = document.querySelector("#bookingStatusChart");
    if (chartEl) {
        if (statusChartInstance) statusChartInstance.destroy();
        statusChartInstance = new ApexCharts(chartEl, options);
        statusChartInstance.render();
    }
}

let eventTypeChartInstance = null;
function renderEventTypeChart(data) {
    if (!data || data.length === 0) {
        const chartEl = document.querySelector("#eventTypeChart");
        if (chartEl) chartEl.innerHTML = '<p class="text-muted text-center py-5">No event data available for this period.</p>';
        return;
    }

    const options = {
        series: data.map(item => parseInt(item.count)),
        chart: {
            type: 'polarArea',
            height: 350,
            background: 'transparent',
            fontFamily: 'Inter, sans-serif'
        },
        labels: data.map(item => item.event_type),
        stroke: { colors: ['#fff'] },
        fill: { opacity: 0.8 },
        colors: ['#D4AF37', '#C5A028', '#B69119', '#A7820A', '#987300'],
        legend: {
            position: 'bottom',
            labels: { colors: '#fff' }
        },
        yaxis: { show: false },
        theme: {
            mode: 'dark',
            monochrome: {
                enabled: true,
                color: '#D4AF37',
                shadeTo: 'dark',
                shadeIntensity: 0.65
            }
        },
        plotOptions: {
            polarArea: {
                rings: { strokeWidth: 0 },
                spokes: { strokeWidth: 0 }
            }
        }
    };

    const chartEl = document.querySelector("#eventTypeChart");
    if (chartEl) {
        chartEl.innerHTML = ''; // Clear previous content/message
        if (eventTypeChartInstance) eventTypeChartInstance.destroy();
        eventTypeChartInstance = new ApexCharts(chartEl, options);
        eventTypeChartInstance.render();
    }
}
