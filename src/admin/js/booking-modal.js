/**
 * Shared Booking Modal Logic
 * Used in bookings.php and calendar.php
 */

let currentBookingId = null;
let bookingDetailsModal = null;

document.addEventListener('DOMContentLoaded', function () {
    // Initialize Modal if element exists
    const modalEl = document.getElementById('bookingDetailsModal');
    if (modalEl) {
        bookingDetailsModal = new bootstrap.Modal(modalEl);

        // Event Listeners for Modal Actions
        const updateStatusBtn = document.getElementById('updateStatusBtn');
        const saveNotesBtn = document.getElementById('saveNotesBtn');
        const saveLinkBtn = document.getElementById('saveLinkBtn');
        const modalStatusSelect = document.getElementById('modalStatusSelect');
        const modalAdminNotes = document.getElementById('modalAdminNotes');
        const modalMeetingLink = document.getElementById('modalMeetingLink');

        if (updateStatusBtn) {
            updateStatusBtn.addEventListener('click', function () {
                if (currentBookingId) {
                    updateBookingStatus(currentBookingId, modalStatusSelect.value);
                }
            });
        }

        if (saveNotesBtn) {
            saveNotesBtn.addEventListener('click', function () {
                if (currentBookingId) {
                    updateBookingNote(currentBookingId, modalAdminNotes.value);
                }
            });
        }

        if (saveLinkBtn) {
            saveLinkBtn.addEventListener('click', function () {
                if (currentBookingId) {
                    updateMeetingLink(currentBookingId, modalMeetingLink.value);
                }
            });
        }
    }
});

// View Booking Details
window.viewBooking = function (bookingId) {
    currentBookingId = bookingId;

    if (!bookingDetailsModal) {
        const modalEl = document.getElementById('bookingDetailsModal');
        if (modalEl) {
            bookingDetailsModal = new bootstrap.Modal(modalEl);
        } else {
            console.error('Booking modal element not found');
            return;
        }
    }

    // Reset Modal
    const logContainer = document.getElementById('modalActivityLog');
    if (logContainer) logContainer.innerHTML = 'Loading logs...';

    fetch(`api/manage_booking.php?action=details&id=${bookingId}`, {
        credentials: 'include'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateModal(data.booking);
                bookingDetailsModal.show();
            } else {
                LuxuryToast.show({ message: data.message || 'Failed to load booking details', type: 'error' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            LuxuryToast.show({ message: 'Failed to load details', type: 'error' });
        });
};

// Populate Modal
function populateModal(booking) {
    setText('modalBookingId', booking.bookingID);
    setText('modalClientName', `${booking.FirstName} ${booking.LastName}`);
    setText('modalClientEmail', booking.Email);
    setText('modalClientPhone', booking.contactNo || 'N/A');

    setText('modalEventDate', formatDate(booking.event_date));
    setText('modalEventTime', `${formatTime(booking.event_time_start)} - ${formatTime(booking.event_time_end)}`);
    setText('modalEventLocation', booking.event_location);
    setText('modalEventType', booking.event_type);

    setText('modalPackageName', booking.packageName);
    setText('modalTotalAmount', `₱${parseFloat(booking.total_amount).toLocaleString()}`);
    setText('modalDownpayment', `₱${parseFloat(booking.downpayment_amount).toLocaleString()}`);

    const balance = parseFloat(booking.total_amount) - parseFloat(booking.downpayment_amount);
    const balanceEl = document.getElementById('modalBalance');
    if (balanceEl) {
        balanceEl.textContent = `₱${balance.toLocaleString()}`;

        // Clear previous buttons/badges
        const existingBtn = balanceEl.querySelector('button');
        const existingBadge = balanceEl.querySelector('.badge');
        if (existingBtn) existingBtn.remove();
        if (existingBadge) existingBadge.remove();

        if (balance > 0 && booking.is_fully_paid != 1) {
            // We need to append, but textContent overwrites. 
            // Let's re-set HTML
            balanceEl.innerHTML = `₱${balance.toLocaleString()} 
                <button class="btn btn-sm btn-success ms-2 py-0" onclick="markAsPaid(${booking.bookingID})">
                    <i class="bi bi-check-circle"></i> Mark Paid
                </button>`;
        } else if (booking.is_fully_paid == 1) {
            balanceEl.innerHTML = `₱${balance.toLocaleString()} <span class="badge bg-success ms-2">PAID</span>`;
        }
    }

    // Status
    const statusBadge = document.getElementById('modalStatusBadge');
    if (statusBadge) {
        statusBadge.className = `badge ${getStatusClass(booking.booking_status)}`;
        statusBadge.textContent = formatStatus(booking.booking_status);
    }

    const statusSelect = document.getElementById('modalStatusSelect');
    if (statusSelect) statusSelect.value = booking.booking_status;

    // Notes
    const adminNotes = document.getElementById('modalAdminNotes');
    if (adminNotes) adminNotes.value = booking.admin_notes || '';

    // Meeting Link
    const meetingLink = document.getElementById('modalMeetingLink');
    if (meetingLink) meetingLink.value = booking.meeting_link || '';

    // Addons
    const addonsContainer = document.getElementById('modalAddons');
    if (addonsContainer) {
        if (booking.addons && booking.addons.length > 0) {
            addonsContainer.innerHTML = booking.addons.map(a => `<div>+ ${a.name} (₱${parseFloat(a.price).toLocaleString()})</div>`).join('');
        } else {
            addonsContainer.innerHTML = 'No addons selected';
        }
    }

    // Proof of Payment
    const paymentContainer = document.getElementById('modalPaymentProof');
    if (paymentContainer) {
        let html = '';

        // Downpayment Proof
        if (booking.proof_payment) {
            html += `
                <div class="mt-3 border-top border-secondary pt-3">
                    <h6 class="text-gold mb-2">Downpayment Proof</h6>
                    <a href="${booking.proof_payment}" target="_blank" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-file-earmark-image me-2"></i>View Receipt
                    </a>
                </div>
            `;
        }

        // Balance Payment Proof
        if (booking.balance_payment_proof) {
            html += `
                <div class="mt-3 border-top border-secondary pt-3">
                    <h6 class="text-gold mb-2">Balance Payment Proof</h6>
                    <div class="d-flex align-items-center gap-2">
                        <a href="${booking.balance_payment_proof}" target="_blank" class="btn btn-sm btn-outline-light">
                            <i class="bi bi-file-earmark-image me-2"></i>View Receipt
                        </a>
                        ${booking.is_fully_paid == 0 ? `
                        <button class="btn btn-sm btn-success" onclick="markAsPaid(${booking.bookingID})">
                            <i class="bi bi-check-circle me-1"></i>Confirm Payment
                        </button>
                        ` : '<span class="badge bg-success">Confirmed</span>'}
                    </div>
                </div>
            `;
        }

        paymentContainer.innerHTML = html;
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

function setText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
}

// Render Logs
function renderLogs(logs) {
    const container = document.getElementById('modalActivityLog');
    if (!container) return;

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
    const btn = document.getElementById('updateStatusBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    }

    fetch('api/manage_booking.php?action=update_status', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ bookingId, status })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                LuxuryToast.show({ message: 'Status updated successfully', type: 'success' });
                viewBooking(bookingId); // Refresh modal
                // If we are on the bookings page, refresh the list
                if (typeof fetchBookings === 'function') {
                    fetchBookings();
                }
                // If we are on calendar page, we might want to refresh events?
                // For now, let's assume calendar auto-refreshes or user reloads if needed.
                // Or we can try to find the calendar instance.
            } else {
                LuxuryToast.show({ message: data.message || 'Failed to update booking status', type: 'error' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            LuxuryToast.show({ message: 'Failed to update status', type: 'error' });
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Update';
            }
        });
}

// Update Note
function updateBookingNote(bookingId, note) {
    const btn = document.getElementById('saveNotesBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    }

    fetch('api/manage_booking.php?action=update_note', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ bookingId, note })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                LuxuryToast.show({ message: 'Note saved successfully', type: 'success' });
                viewBooking(bookingId); // Refresh modal
            } else {
                LuxuryToast.show({ message: data.message || 'Failed to save note', type: 'error' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            LuxuryToast.show({ message: 'Failed to save note', type: 'error' });
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Save Notes';
            }
        });
}

// Update Meeting Link
function updateMeetingLink(bookingId, link) {
    const btn = document.getElementById('saveLinkBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    }

    fetch('api/manage_booking.php?action=update_meeting_link', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ bookingId, link })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                LuxuryToast.show({ message: 'Meeting link saved successfully', type: 'success' });
            } else {
                LuxuryToast.show({ message: data.message || 'Failed to save link', type: 'error' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            LuxuryToast.show({ message: 'Failed to save link', type: 'error' });
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Save';
            }
        });
}

// Mark as Paid
window.markAsPaid = function (bookingId) {
    if (!confirm('Are you sure you want to mark the balance as PAID? This cannot be undone.')) return;

    fetch('api/manage_booking.php?action=mark_paid', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ bookingId: bookingId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                LuxuryToast.show({ message: 'Payment updated & booking confirmed', type: 'success' });
                viewBooking(bookingId); // Refresh modal
                if (typeof fetchBookings === 'function') {
                    fetchBookings();
                }
            } else {
                LuxuryToast.show({ message: data.message || 'Failed to update payment', type: 'error' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            LuxuryToast.show({ message: 'An error occurred', type: 'error' });
        });
};

// Helpers
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatTime(timeString) {
    if (!timeString) return 'N/A';
    return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}

function formatStatus(status) {
    if (!status) return 'Unknown';
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
