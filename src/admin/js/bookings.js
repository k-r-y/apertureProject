document.addEventListener('DOMContentLoaded', function () {
    const bookingsTableBody = document.getElementById('bookingsTableBody');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const applyFiltersBtn = document.getElementById('applyFilters');

    // Modal Elements - Handled in booking-modal.js

    // Initial Load
    // 1. Restore state from sessionStorage
    const savedStatus = sessionStorage.getItem('bookingStatus');
    const savedSearch = sessionStorage.getItem('bookingSearch');

    if (savedStatus) statusFilter.value = savedStatus;
    if (savedSearch) searchInput.value = savedSearch;

    fetchBookings();

    // Check for ID param
    const urlParams = new URLSearchParams(window.location.search);
    const bookingIdParam = urlParams.get('id');
    if (bookingIdParam) {
        // viewBooking is now global from booking-modal.js
        if (typeof viewBooking === 'function') {
            viewBooking(bookingIdParam);
        }
        // Clean URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // Event Listeners
    applyFiltersBtn.addEventListener('click', fetchBookings);

    // Fetch Bookings
    // Fetch Bookings
    function fetchBookings() {
        const status = statusFilter.value;
        const search = searchInput.value;

        // Save state
        sessionStorage.setItem('bookingStatus', status);
        sessionStorage.setItem('bookingSearch', search);

        bookingsTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">Loading...</td></tr>';

        fetch(`api/manage_booking.php?action=list&status=${status}&search=${search}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderBookings(data.bookings);
                } else {
                    bookingsTableBody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-danger">Error: ${data.message}</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                bookingsTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-danger">Failed to load bookings</td></tr>';
            });
    }
    // Expose to window for external access (e.g. from booking-modal.js)
    window.fetchBookings = fetchBookings;

    // Render Bookings Table
    function renderBookings(bookings) {
        if (bookings.length === 0) {
            bookingsTableBody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">No bookings found</td></tr>';
            return;
        }

        bookingsTableBody.innerHTML = bookings.map(booking => `
            <tr>
                <td class="text-light">#${booking.bookingID}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="ms-2">
                            <h6 class="mb-0 text-light small">${booking.FirstName} ${booking.LastName}</h6>
                        </div>
                    </div>
                </td>
                <td class="text-light small">${booking.event_type}</td>
                <td class="text-light small">${formatDate(booking.event_date)}</td>
                <td>${getStatusBadge(booking.booking_status)}</td>
                <td class="text-light small">₱${parseFloat(booking.total_amount).toLocaleString()}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-gold" onclick="viewBooking(${booking.bookingID})">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }
});

