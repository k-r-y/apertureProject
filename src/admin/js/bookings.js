document.addEventListener('DOMContentLoaded', function () {
    const bookingsTableBody = document.getElementById('bookingsTableBody');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const applyFiltersBtn = document.getElementById('applyFilters');

    // Modal Elements
    const bookingDetailsModal = new bootstrap.Modal(document.getElementById('bookingDetailsModal'));
    const modalStatusSelect = document.getElementById('modalStatusSelect');
    const updateStatusBtn = document.getElementById('updateStatusBtn');
    const modalAdminNotes = document.getElementById('modalAdminNotes');
    const saveNotesBtn = document.getElementById('saveNotesBtn');

    let currentBookingId = null;

    // Initial Load
    fetchBookings();

    // Event Listeners
    applyFiltersBtn.addEventListener('click', fetchBookings);

    updateStatusBtn.addEventListener('click', function () {
        if (currentBookingId) {
            updateBookingStatus(currentBookingId, modalStatusSelect.value);
        }
    });

    saveNotesBtn.addEventListener('click', function () {
        if (currentBookingId) {
            updateBookingNote(currentBookingId, modalAdminNotes.value);
        }
    });

    // Fetch Bookings
    function fetchBookings() {
        const status = statusFilter.value;
        const search = searchInput.value;

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

    // View Booking Details
    window.viewBooking = function (bookingId) {
        currentBookingId = bookingId;

        // Reset Modal
        document.getElementById('modalActivityLog').innerHTML = 'Loading logs...';

        fetch(`api/manage_booking.php?action=details&id=${bookingId}`, {
            credentials: 'include'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateModal(data.booking);
                    bookingDetailsModal.show();
                } else {
                    LuxuryToast.show('Error', data.message || 'Failed to load booking details', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                LuxuryToast.show('Error', 'Failed to load details', 'error');
            });
    };

    // Populate Modal
    function populateModal(booking) {
        document.getElementById('modalBookingId').textContent = booking.bookingID;
        document.getElementById('modalClientName').textContent = `${booking.FirstName} ${booking.LastName}`;
        document.getElementById('modalClientEmail').textContent = booking.Email;
        document.getElementById('modalClientPhone').textContent = booking.contactNo || 'N/A';

        document.getElementById('modalEventDate').textContent = formatDate(booking.event_date);
        document.getElementById('modalEventTime').textContent = `${formatTime(booking.event_time_start)} - ${formatTime(booking.event_time_end)}`;
        document.getElementById('modalEventLocation').textContent = booking.event_location;
        document.getElementById('modalEventType').textContent = booking.event_type;

        document.getElementById('modalPackageName').textContent = booking.packageName;
        document.getElementById('modalTotalAmount').textContent = `₱${parseFloat(booking.total_amount).toLocaleString()}`;
        document.getElementById('modalDownpayment').textContent = `₱${parseFloat(booking.downpayment_amount).toLocaleString()}`;

        const balance = parseFloat(booking.total_amount) - parseFloat(booking.downpayment_amount);
        document.getElementById('modalBalance').textContent = `₱${balance.toLocaleString()}`;

        // Status
        document.getElementById('modalStatusBadge').className = `badge ${getStatusClass(booking.booking_status)}`;
        document.getElementById('modalStatusBadge').textContent = formatStatus(booking.booking_status);
        modalStatusSelect.value = booking.booking_status;

        // Notes
        modalAdminNotes.value = booking.admin_notes || '';

        // Addons
        const addonsContainer = document.getElementById('modalAddons');
        if (booking.addons && booking.addons.length > 0) {
            addonsContainer.innerHTML = booking.addons.map(a => `<div>+ ${a.name} (₱${parseFloat(a.price).toLocaleString()})</div>`).join('');
        } else {
            addonsContainer.innerHTML = 'No addons selected';
        }

        // Proof of Payment
        const paymentContainer = document.getElementById('modalPaymentProof');
        if (paymentContainer) {
            if (booking.proof_payment) {
                // Adjust path if needed. Assuming proof_payment is relative to uploads/
                // The DB stores "../uploads/payment_proofs/filename". 
                // We need to make it accessible from admin/bookings.php
                // bookings.php is in src/admin/. Uploads are in src/uploads/
                // So path should be ../uploads/...
                // If DB has full relative path "../uploads/...", it works directly if we are in src/admin/

                const proofUrl = booking.proof_payment;
                paymentContainer.innerHTML = `
                    <div class="mt-3 border-top border-secondary pt-3">
                        <h6 class="text-gold mb-2">Proof of Payment</h6>
                        <a href="${proofUrl}" target="_blank" class="btn btn-sm btn-outline-light">
                            <i class="bi bi-file-earmark-image me-2"></i>View Proof
                        </a>
                    </div>
                `;
            } else {
                paymentContainer.innerHTML = '';
            }
        }

        // Consultation Info
        const consultationContainer = document.getElementById('modalConsultation');
        if (consultationContainer) {
            if (booking.consultation_date && booking.consultation_time) {
                consultationContainer.innerHTML = `
                    <div class="alert alert-dark border-gold mt-3">
                        <h6 class="text-gold mb-1"><i class="bi bi-camera-video me-2"></i>Requested Consultation</h6>
                        <div class="text-light small">
                            ${formatDate(booking.consultation_date)} at ${formatTime(booking.consultation_time)}
                        </div>
                    </div>
                `;
            } else {
                consultationContainer.innerHTML = '';
            }
        }

        // Add Download Invoice Button
        const invoiceBtn = document.getElementById('downloadInvoiceBtn');
        if (invoiceBtn) {
            invoiceBtn.onclick = () => window.open(`../api/generate_invoice.php?id=${booking.bookingID}`, '_blank');
        }

        // Logs
        renderLogs(booking.logs);
    }

    // Render Logs
    function renderLogs(logs) {
        const container = document.getElementById('modalActivityLog');
        if (!logs || logs.length === 0) {
            container.innerHTML = '<div class="text-muted small">No activity recorded</div>';
            return;
        }

        container.innerHTML = logs.map(log => `
            <div class="border-bottom border-secondary mb-2 pb-2">
                <div class="d-flex justify-content-between">
                    <small class="text-gold">${log.action.replace('_', ' ').toUpperCase()}</small>
                    <small class="text-muted" style="font-size: 0.7rem;">${new Date(log.created_at).toLocaleString()}</small>
                </div>
                <div class="text-light small">${log.details}</div>
                <div class="text-muted" style="font-size: 0.7rem;">By: ${log.FirstName ? log.FirstName : 'System'}</div>
            </div>
        `).join('');
    }

    // Update Status
    function updateBookingStatus(bookingId, status) {
        updateStatusBtn.disabled = true;
        updateStatusBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch('api/manage_booking.php?action=update_status', {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ bookingId, status })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    LuxuryToast.show('Success', 'Status updated successfully', 'success');
                    viewBooking(bookingId); // Refresh modal
                    fetchBookings(); // Refresh list
                } else {
                    LuxuryToast.show('Error', data.message || 'Failed to update booking status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                LuxuryToast.show('Error', 'Failed to update status', 'error');
            })
            .finally(() => {
                updateStatusBtn.disabled = false;
                updateStatusBtn.textContent = 'Update';
            });
    }

    // Update Note
    function updateBookingNote(bookingId, note) {
        saveNotesBtn.disabled = true;
        saveNotesBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch('api/manage_booking.php?action=update_note', {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ bookingId, note })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    LuxuryToast.show('Success', 'Note saved successfully', 'success');
                    viewBooking(bookingId); // Refresh modal
                } else {
                    LuxuryToast.show('Error', data.message || 'Failed to save note', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                LuxuryToast.show('Error', 'Failed to save note', 'error');
            })
            .finally(() => {
                saveNotesBtn.disabled = false;
                saveNotesBtn.textContent = 'Save Notes';
            });
    }

    // Helpers
    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function formatTime(timeString) {
        return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    }

    function formatStatus(status) {
        return status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function getStatusBadge(status) {
        const badgeClass = getStatusClass(status);
        return `<span class="badge ${badgeClass}">${formatStatus(status)}</span>`;
    }

    function getStatusClass(status) {
        switch (status) {
            case 'pending_consultation': return 'bg-warning text-dark';
            case 'confirmed': return 'bg-success';
            case 'post_production': return 'bg-info text-dark';
            case 'completed': return 'bg-primary';
            case 'cancelled': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }
});
