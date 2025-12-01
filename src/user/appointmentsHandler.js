let allAppointments = [];
let currentFilter = 'all';

async function fetchAppointments(status = 'all') {
    const loadingState = document.getElementById('loadingState');
    const appointmentsGrid = document.getElementById('appointmentsGrid');
    const emptyState = document.getElementById('emptyState');

    try {
        loadingState.style.display = 'block';
        appointmentsGrid.style.display = 'none';
        emptyState.style.display = 'none';

        const url = status === 'all'
            ? 'getAppointments.php'
            : `getAppointments.php?status=${encodeURIComponent(status)}`;

        const response = await fetch(url);
        const data = await response.json();

        if (data.success) {
            allAppointments = data.appointments;
            renderAppointments(data.appointments);

            // Handle deep linking
            if (window.pendingBookingId) {
                openAppointmentModal(window.pendingBookingId);
                window.pendingBookingId = null; // Clear it
            }
        } else {
            throw new Error(data.message || 'Failed to fetch appointments');
        }
    } catch (error) {
        console.error('Error fetching appointments:', error);
        loadingState.style.display = 'none';
        emptyState.style.display = 'block';
        document.querySelector('#emptyState h3').textContent = 'Error Loading Appointments';
        document.querySelector('#emptyState p').textContent = 'There was an error loading your appointments. Please try again.';
    }
}

// Render appointments to the grid
function renderAppointments(appointments) {
    const loadingState = document.getElementById('loadingState');
    const appointmentsGrid = document.getElementById('appointmentsGrid');
    const emptyState = document.getElementById('emptyState');

    loadingState.style.display = 'none';

    if (appointments.length === 0) {
        appointmentsGrid.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }

    appointmentsGrid.style.display = 'grid';
    emptyState.style.display = 'none';

    appointmentsGrid.innerHTML = appointments.map(appointment => `
        <div class="appointment-card" onclick="openAppointmentModal(${appointment.bookingID})">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="status-badge status-${appointment.bookingStatus}">
                    ${formatStatus(appointment.bookingStatus)}
                </span>
                <small class="text-muted">Ref: #${appointment.bookingRef}</small>
            </div>
            
            <h5 class="text-gold mb-3">${appointment.eventType}</h5>
            
            <div class="mb-2">
                <i class="bi bi-calendar3 text-gold me-2"></i>
                <span class="text-light">${appointment.eventDateFormatted}</span>
            </div>
            
            <div class="mb-2">
                <i class="bi bi-clock text-gold me-2"></i>
                <span class="text-light">${appointment.eventTimeFormatted}</span>
            </div>
            
            <div class="mb-2">
                <i class="bi bi-geo-alt text-gold me-2"></i>
                <span class="text-light">${appointment.eventLocation}</span>
            </div>
            
            <div class="mb-3">
                <i class="bi bi-box text-gold me-2"></i>
                <span class="text-light">${appointment.packageName}</span>
            </div>
            
            <div class="divider"></div>
            
            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <div>
                    <small class="text-muted d-block">Total Amount</small>
                    <strong class="text-gold">${appointment.totalAmountFormatted}</strong>
                </div>
                <div class="d-flex flex-wrap">
                    <button class="btn btn-gold btn-sm">
                        <i class="bi bi-eye me-1"></i>View Details
                    </button>
                    ${appointment.bookingStatus === 'completed' ? `
                    <button class="btn btn-outline-gold btn-sm ms-2" onclick="openReviewModal(${appointment.bookingID}, event)">
                        <i class="bi bi-star me-1"></i>Review
                    </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `).join('');
}

// Format status text
function formatStatus(status) {
    const statusMap = {
        'pending': 'Pending',
        'confirmed': 'Confirmed',
        'post_production': 'Post Production',
        'completed': 'Completed',
        'cancelled': 'Cancelled',
        'cancellation_pending': 'Cancellation Pending'
    };
    return statusMap[status] || status;
}

// Setup filter button event listeners
function setupFilterButtons() {
    const filterButtons = document.querySelectorAll('.filter-btn');

    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));

            // Add active class to clicked button
            this.classList.add('active');

            // Get status and fetch appointments
            const status = this.getAttribute('data-status');
            currentFilter = status;
            fetchAppointments(status);
        });
    });
}

// Open appointment modal with details
function openAppointmentModal(bookingID) {
    const appointment = allAppointments.find(app => app.bookingID === bookingID);

    if (!appointment) {
        console.error('Appointment not found');
        return;
    }

    const modalBody = document.getElementById('modalBody');
    const modal = document.getElementById('appointmentModal');

    // Build modal content
    modalBody.innerHTML = `
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-gold mb-0">Booking Reference</h5>
                <span class="status-badge status-${appointment.bookingStatus}">
                    ${formatStatus(appointment.bookingStatus)}
                </span>
            </div>
            <p class="text-light font-monospace">#${appointment.bookingRef}</p>
        </div>
        
        <div class="divider"></div>
        
        <h5 class="text-gold mt-4 mb-3">Event Information</h5>
        <div class="detail-row">
            <span class="detail-label">Event Type</span>
            <span class="detail-value">${escapeHtml(appointment.eventType)}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Date</span>
            <span class="detail-value">${appointment.eventDateFormatted}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Time</span>
            <span class="detail-value">${appointment.eventTimeFormatted}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Location</span>
            <span class="detail-value">${escapeHtml(appointment.eventLocation)}</span>
        </div>
        ${appointment.eventTheme ? `
        <div class="detail-row">
            <span class="detail-label">Theme</span>
            <span class="detail-value">${escapeHtml(appointment.eventTheme)}</span>
        </div>
        ` : ''}

        ${appointment.meetingLink ? `
        <div class="detail-row mt-3">
            <span class="detail-label">Meeting Link</span>
            <span class="detail-value">
                <a href="${escapeHtml(appointment.meetingLink)}" target="_blank" class="text-gold text-decoration-none">
                    <i class="bi bi-camera-video me-1"></i> Join Meeting
                </a>
            </span>
        </div>
        ` : ''}
        
        <div class="divider"></div>
        
        <h5 class="text-gold mt-4 mb-3">Package Details</h5>
        <div class="detail-row">
            <span class="detail-label">Package</span>
            <span class="detail-value">${escapeHtml(appointment.packageName)}</span>
        </div>
        ${appointment.packageDescription ? `
        <p class="text-muted mt-2 small">${escapeHtml(appointment.packageDescription)}</p>
        ` : ''}
        
        <div class="divider"></div>
        
        <h5 class="text-gold mt-4 mb-3">Payment Information</h5>
        <div class="detail-row">
            <span class="detail-label">Total Amount</span>
            <span class="detail-value text-gold">${appointment.totalAmountFormatted}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Downpayment</span>
            <span class="detail-value">${appointment.downpaymentFormatted}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Balance</span>
            <span class="detail-value">${appointment.balanceFormatted}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Payment Status</span>
            <span class="detail-value ${appointment.isFullyPaid ? 'text-success' : 'text-warning'}">
                ${appointment.isFullyPaid ? 'Fully Paid' : 'Pending Balance'}
            </span>
        </div>
        
        ${!appointment.isFullyPaid && appointment.bookingStatus !== 'cancelled' ? `
        <div class="mt-3">
            ${(!appointment.proofPayment || appointment.proofPayment === '') ? `
                <button class="btn btn-gold w-100 mb-2" onclick="openPayBalanceModal(${appointment.bookingID}, '${appointment.downpaymentFormatted}', 'downpayment')">
                    <i class="bi bi-upload me-2"></i>Upload Downpayment Proof
                </button>
            ` : ''}
            
            ${appointment.balancePaymentProof ? `
                <div class="alert alert-info border-0 d-flex align-items-center">
                    <i class="bi bi-hourglass-split me-2"></i>
                    <div>
                        <strong>Payment Pending:</strong> Your balance payment proof is under review.
                    </div>
                </div>
            ` : `
                <button class="btn btn-gold w-100" onclick="openPayBalanceModal(${appointment.bookingID}, '${appointment.balanceFormatted}', 'balance')">
                    <i class="bi bi-credit-card me-2"></i>Pay Balance
                </button>
            `}
        </div>
        ` : ''}
        
        ${appointment.clientMessage ? `
        <div class="divider"></div>
        <h5 class="text-gold mt-4 mb-3">Special Requests</h5>
        <p class="text-light">${appointment.clientMessage}</p>
        ` : ''}
        
        ${appointment.gdriveLink ? `
        <div class="divider"></div>
        <h5 class="text-gold mt-4 mb-3">Photos</h5>
        <a href="${escapeHtml(appointment.gdriveLink)}" target="_blank" class="btn btn-gold">
            <i class="bi bi-cloud-download me-2"></i>Download Photos
        </a>
        ` : ''}
        
        <div class="divider"></div>
        
        ${appointment.refund_status ? `
        <div class="alert alert-${appointment.refund_status === 'approved' ? 'success' : (appointment.refund_status === 'rejected' ? 'danger' : 'warning')} border-0 d-flex align-items-center mb-3">
            <i class="bi bi-${appointment.refund_status === 'approved' ? 'check-circle' : (appointment.refund_status === 'rejected' ? 'x-circle' : 'hourglass-split')} me-2"></i>
            <div>
                <strong>Refund Status:</strong> ${appointment.refund_status.charAt(0).toUpperCase() + appointment.refund_status.slice(1)}
            </div>
        </div>
        ` : ''}

        <div class="mt-4 d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted">Booked on ${appointment.createdAtFormatted}</small>
                <button class="btn btn-gold btn-sm ms-3" onclick="window.open('../api/generate_invoice.php?id=${appointment.bookingID}', '_blank')">
                    <i class="bi bi-file-pdf me-2"></i>Download Invoice
                </button>
            </div>
            <div class="d-flex gap-2 w-100 justify-content-end">
                ${appointment.bookingStatus === 'cancellation_pending' ? `
                <div class="alert alert-warning mb-0 py-1 px-3 d-flex align-items-center">
                    <i class="bi bi-hourglass-split me-2"></i>
                    <small>Cancellation Pending Approval</small>
                </div>
                ` : ''}

                ${(appointment.bookingStatus === 'pending' || appointment.bookingStatus === 'confirmed') ? `
                <button class="btn btn-outline-gold btn-sm" onclick="openEditModal(${appointment.bookingID})">
                    <i class="bi bi-pencil me-2"></i>Edit
                </button>
                ` : ''}
                
                ${(appointment.bookingStatus === 'pending_consultation' || appointment.bookingStatus === 'confirmed' || appointment.bookingStatus === 'pending') ? `
                <button class="btn btn-outline-danger btn-sm" onclick="cancelBooking(${appointment.bookingID})">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </button>
                ` : ''}
            </div>
        </div>
    `;

    // Show modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Close appointment modal
function closeModal() {
    const modal = document.getElementById('appointmentModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Pay Balance / Upload Proof Modal Logic
function openPayBalanceModal(bookingID, amount, type = 'balance') {
    document.getElementById('payBalanceBookingId').value = bookingID;
    document.getElementById('payBalanceAmount').textContent = amount;
    document.getElementById('paymentType').value = type;

    // Update modal title and labels based on type
    const modalTitle = document.querySelector('#payBalanceModal .modal-header h3');
    const amountLabel = document.querySelector('#payBalanceModal .text-center p');
    const submitBtn = document.querySelector('#payBalanceForm button[type="submit"]');

    if (type === 'downpayment') {
        modalTitle.textContent = 'Upload Downpayment Proof';
        amountLabel.textContent = 'Downpayment Amount';
        submitBtn.textContent = 'Submit Downpayment Proof';
    } else {
        modalTitle.textContent = 'Pay Balance';
        amountLabel.textContent = 'Remaining Balance';
        submitBtn.textContent = 'Submit Payment Proof';
    }

    document.getElementById('payBalanceModal').classList.add('active');
}

function closePayBalanceModal() {
    document.getElementById('payBalanceModal').classList.remove('active');
}

// Submit Balance Payment
document.getElementById('payBalanceForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const bookingId = document.getElementById('payBalanceBookingId').value;
    const paymentType = document.getElementById('paymentType').value;
    const fileInput = document.getElementById('balanceProof');
    const submitBtn = this.querySelector('button[type="submit"]');

    if (fileInput.files.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing File',
            text: 'Please upload a proof of payment',
            confirmButtonColor: '#d4af37',
            background: '#1a1a1a',
            color: '#fff'
        });
        return;
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Uploading...';

    const formData = new FormData();
    formData.append('bookingId', bookingId);
    formData.append('type', paymentType);
    formData.append('paymentProof', fileInput.files[0]);

    fetch('api/payment_api.php', {
        method: 'POST',
        body: formData
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    confirmButtonColor: '#d4af37',
                    background: '#1a1a1a',
                    color: '#fff'
                }).then(() => {
                    closePayBalanceModal();
                    closeModal(); // Close appointment modal too
                    fetchAppointments(currentFilter); // Refresh list
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    confirmButtonColor: '#d4af37',
                    background: '#1a1a1a',
                    color: '#fff'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while uploading payment proof',
                confirmButtonColor: '#d4af37',
                background: '#1a1a1a',
                color: '#fff'
            });
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Payment Proof';
        });
});

// Cancel Booking
function cancelBooking(bookingID) {
    // Get booking details to show refund amount
    const appointment = allAppointments.find(app => app.bookingID === bookingID);

    // Close the booking modal first to fix z-index issue
    closeModal();

    // Small delay to ensure modal closes before SweetAlert appears
    setTimeout(() => {
        Swal.fire({
            title: 'Request Cancellation?',
            html: `
        <p>Are you sure you want to request cancellation for this booking?</p>
        ${appointment && appointment.isFullyPaid ?
                    '<p class="text-warning"><strong>Refund Policy:</strong> You will receive 40% of your total payment.</p>' :
                    '<p class="text-warning"><strong>Refund Policy:</strong> You will receive 40% of your downpayment.</p>'
                }
        <p class="text-muted small">Your cancellation request will be reviewed by admin before processing the refund.</p>
    `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d4af37',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Request Cancellation',
            cancelButtonText: 'Keep Booking',
            background: '#1a1a1a',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    text: 'Submitting cancellation request',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: '#1a1a1a',
                    color: '#fff'
                });

                fetch('cancelBooking.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ bookingId: bookingID })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Cancellation Requested',
                                html: data.message,
                                icon: 'success',
                                confirmButtonColor: '#d4af37',
                                background: '#1a1a1a',
                                color: '#fff'
                            }).then(() => {
                                fetchAppointments(currentFilter); // Refresh list
                            });
                        } else {
                            Swal.fire({
                                title: 'Request Failed',
                                text: data.message,
                                icon: 'error',
                                confirmButtonColor: '#d4af37',
                                background: '#1a1a1a',
                                color: '#fff'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'An unexpected error occurred.',
                            icon: 'error',
                            confirmButtonColor: '#d4af37',
                            background: '#1a1a1a',
                            color: '#fff'
                        });
                    });
            }
        });
    }, 300);
}

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    setupFilterButtons();
    fetchAppointments();

    // Check for pending booking ID from URL (deep linking)
    const urlParams = new URLSearchParams(window.location.search);
    const pendingBookingId = urlParams.get('booking_id');
    if (pendingBookingId) {
        // Store it to open after fetch completes
        window.pendingBookingId = parseInt(pendingBookingId);

        // Clean URL
        const newUrl = window.location.pathname;
        window.history.replaceState({}, '', newUrl);
    }
    // Close modals when clicking outside
    window.addEventListener('click', function (event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});
