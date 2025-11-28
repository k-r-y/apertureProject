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

        // Edit Details Elements
        const editDetailsBtn = document.getElementById('editDetailsBtn');
        const saveDetailsBtn = document.getElementById('saveDetailsBtn');
        const cancelEditBtn = document.getElementById('cancelEditBtn');
        const viewModeDetails = document.getElementById('viewModeDetails');
        const editModeDetails = document.getElementById('editModeDetails');

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

        // Edit Mode Toggles
        if (editDetailsBtn) {
            editDetailsBtn.addEventListener('click', function () {
                viewModeDetails.style.display = 'none';
                editModeDetails.style.display = 'block';
                editDetailsBtn.style.display = 'none';
            });
        }

        if (cancelEditBtn) {
            cancelEditBtn.addEventListener('click', function () {
                viewModeDetails.style.display = 'block';
                editModeDetails.style.display = 'none';
                editDetailsBtn.style.display = 'block';
            });
        }

        if (saveDetailsBtn) {
            saveDetailsBtn.addEventListener('click', function () {
                if (currentBookingId) {
                    updateBookingDetails(currentBookingId);
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

    // Populate Edit Fields
    const editDate = document.getElementById('editEventDate');
    const editStart = document.getElementById('editEventStartTime');
    const editEnd = document.getElementById('editEventEndTime');
    const editLoc = document.getElementById('editEventLocation');
    const editType = document.getElementById('editEventType');

    if (editDate) editDate.value = booking.event_date;
    if (editStart) editStart.value = booking.event_time_start;
    if (editEnd) editEnd.value = booking.event_time_end;
    if (editLoc) editLoc.value = booking.event_location;
    if (editType) editType.value = booking.event_type;

    // Reset View Mode
    const viewModeDetails = document.getElementById('viewModeDetails');
    const editModeDetails = document.getElementById('editModeDetails');
    const editDetailsBtn = document.getElementById('editDetailsBtn');

    if (viewModeDetails) viewModeDetails.style.display = 'block';
    if (editModeDetails) editModeDetails.style.display = 'none';
    if (editDetailsBtn) editDetailsBtn.style.display = 'block';

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

    // Payment Status Checkboxes
    const dpCheckbox = document.getElementById('confirmDownpayment');
    const fpCheckbox = document.getElementById('confirmFinalPayment');

    if (dpCheckbox) {
        dpCheckbox.checked = booking.downpayment_paid == 1;
        dpCheckbox.disabled = booking.downpayment_paid == 1; // Disable if already paid? Or allow toggle? Usually confirm is one-way. Let's keep it enabled but warn if unchecking (if we implement uncheck).
        // Actually, the API supports confirming. Unconfirming might be tricky if we don't support it.
        // Let's assume one-way for now or just allow clicking.
        // If disabled, user can't click. Let's disable if paid to prevent accidental clicks, or allow re-clicking if we want to toggle (but API needs to support it).
        // The requirement says "admin confirms it".
        dpCheckbox.disabled = booking.downpayment_paid == 1;

        // Remove old listeners to prevent duplicates (cloning is a quick hack, or just setting onclick)
        const newDp = dpCheckbox.cloneNode(true);
        dpCheckbox.parentNode.replaceChild(newDp, dpCheckbox);

        newDp.addEventListener('change', function () {
            if (this.checked) {
                confirmPayment(booking.bookingID, 'downpayment', this);
            }
        });
    }

    if (fpCheckbox) {
        fpCheckbox.checked = booking.final_payment_paid == 1;
        fpCheckbox.disabled = booking.final_payment_paid == 1;

        const newFp = fpCheckbox.cloneNode(true);
        fpCheckbox.parentNode.replaceChild(newFp, fpCheckbox);

        newFp.addEventListener('change', function () {
            if (this.checked) {
                confirmPayment(booking.bookingID, 'final', this);
            }
        });
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
                    <a href="${booking.balance_payment_proof}" target="_blank" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-file-earmark-image me-2"></i>View Receipt
                    </a>
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
function updateMeetingLink(bookingId, meeting_link) {
    const btn = document.getElementById('saveLinkBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    }

    fetch('api/manage_booking.php?action=update_meeting_link', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ bookingId, meeting_link })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Meeting link saved successfully! User will receive email notification.');
                // Refresh modal to show updated link
                viewBooking(currentBookingId);
            } else {
                alert('❌ Error: ' + (data.message || 'Failed to save link'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Failed to save meeting link: ' + error.message);
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Save';
            }
        });
}

// Update Booking Details
function updateBookingDetails(bookingId) {
    const btn = document.getElementById('saveDetailsBtn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    }

    const data = {
        bookingId: bookingId,
        event_date: document.getElementById('editEventDate').value,
        event_time_start: document.getElementById('editEventStartTime').value,
        event_time_end: document.getElementById('editEventEndTime').value,
        event_location: document.getElementById('editEventLocation').value,
        event_type: document.getElementById('editEventType').value
    };

    fetch('api/manage_booking.php?action=update_details', {
        method: 'POST',
        credentials: 'include',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                LuxuryToast.show({ message: 'Booking details updated successfully', type: 'success' });
                viewBooking(bookingId); // Refresh modal
                if (typeof fetchBookings === 'function') {
                    fetchBookings(); // Refresh list
                }
            } else {
                LuxuryToast.show({ message: data.message || 'Failed to update details', type: 'error' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            LuxuryToast.show({ message: 'Failed to update details', type: 'error' });
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Save Changes';
            }
        });
}

// Confirm Payment (Downpayment or Final)
function confirmPayment(bookingId, type, checkbox) {
    if (!confirm(`Are you sure you want to confirm the ${type} payment? This cannot be undone.`)) {
        checkbox.checked = false;
        return;
    }

    // Disable checkbox while processing
    checkbox.disabled = true;

    fetch('api/confirm_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            bookingId: bookingId,
            paymentType: type
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                LuxuryToast.show({ message: `${type.charAt(0).toUpperCase() + type.slice(1)} confirmed successfully`, type: 'success' });
                viewBooking(bookingId); // Refresh modal to show updated status/badges
                if (typeof fetchBookings === 'function') {
                    fetchBookings(); // Refresh list
                }
            } else {
                LuxuryToast.show({ message: data.message || 'Failed to confirm payment', type: 'error' });
                checkbox.checked = false;
                checkbox.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            LuxuryToast.show({ message: 'An error occurred', type: 'error' });
            checkbox.checked = false;
            checkbox.disabled = false;
        });
}

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
