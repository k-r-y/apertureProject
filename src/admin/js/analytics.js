// Fetch and display analytics data
(function () {
    // Fetch analytics data
    fetch('api/get_analytics.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update stat cards
                updateStatCard('totalRevenue', '₱' + data.stats.totalRevenue);
                updateStatCard('totalBookings', data.stats.totalBookings);
                updateStatCard('upcomingEvents', data.stats.upcomingEvents);
                updateStatCard('newClients', data.stats.newClients);

                // Render charts
                renderMonthlyBookingsChart(data.monthlyBookings);
                renderPackagePopularityChart(data.packagePopularity);
            } else {
                console.error('Analytics error:', data.message);
                showError();
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showError();
        });

    function updateStatCard(id, value) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    }

    function showError() {
        ['totalRevenue', 'totalBookings', 'upcomingEvents', 'newClients'].forEach(id => {
            updateStatCard(id, 'Error');
        });
    }

    function renderMonthlyBookingsChart(monthlyData) {
        const chartElement = document.querySelector("#monthlyBookingsChart");
        if (!chartElement) return;

        const monthlyBookingsChart = new ApexCharts(chartElement, {
            series: [{
                name: 'Bookings',
                data: monthlyData
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false },
                foreColor: 'var(--text-secondary)'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 4
                }
            },
            dataLabels: { enabled: false },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: getLastTwelveMonths()
            },
            yaxis: {
                title: { text: 'Number of Bookings' }
            },
            fill: {
                opacity: 1,
                colors: ['#D4AF37']
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function (val) {
                        return val + " bookings";
                    }
                }
            }
        });
        monthlyBookingsChart.render();
    }

    function renderPackagePopularityChart(packageData) {
        const chartElement = document.querySelector("#servicePopularityChart");
        if (!chartElement) return;

        const series = packageData.map(p => p.count);
        const labels = packageData.map(p => p.name);

        const servicePopularityChart = new ApexCharts(chartElement, {
            series: series,
            chart: {
                type: 'donut',
                height: 350,
                foreColor: 'var(--text-secondary)'
            },
            labels: labels,
            colors: ['#D4AF37', '#E0C670', '#F8E4A0', '#C4A035', '#B89030'],
            legend: {
                position: 'bottom'
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: { width: 200 },
                    legend: { position: 'bottom' }
                }
            }]
        });
        servicePopularityChart.render();
    }

    function getLastTwelveMonths() {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const currentMonth = new Date().getMonth();
        const result = [];

        for (let i = 0; i < 12; i++) {
            result.push(months[(currentMonth - 11 + i + 12) % 12]);
        }

        return result;
    }
})();
