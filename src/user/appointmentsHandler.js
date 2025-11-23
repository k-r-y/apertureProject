// Appointments Handler - Manages fetching, filtering, and displaying appointments

let allAppointments = [];
let currentFilter = 'all';

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    fetchAppointments();
    setupFilterButtons();
});

// Fetch appointments from API
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
            
            <h5 class="text-gold mb-3">${escapeHtml(appointment.eventType)}</h5>
            
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
                <span class="text-light">${escapeHtml(appointment.eventLocation)}</span>
            </div>
            
            <div class="mb-3">
                <i class="bi bi-box text-gold me-2"></i>
                <span class="text-light">${escapeHtml(appointment.packageName)}</span>
            </div>
            
            <div class="divider"></div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted d-block">Total Amount</small>
                    <strong class="text-gold">${appointment.totalAmountFormatted}</strong>
                </div>
                <button class="btn btn-gold btn-sm">
                    <i class="bi bi-eye me-1"></i>View Details
                </button>
            </div>
        </div>
    `).join('');
}

// Format status text
function formatStatus(status) {
    const statusMap = {
        'pending_consultation': 'Pending',
        'confirmed': 'Confirmed',
        'post_production': 'Post Production',
        'completed': 'Completed',
        'cancelled': 'Cancelled'
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
        
        ${appointment.clientMessage ? `
        <div class="divider"></div>
        <h5 class="text-gold mt-4 mb-3">Special Requests</h5>
        <p class="text-light">${escapeHtml(appointment.clientMessage)}</p>
        ` : ''}
        
        ${appointment.gdriveLink ? `
        <div class="divider"></div>
        <h5 class="text-gold mt-4 mb-3">Photos</h5>
        <a href="${escapeHtml(appointment.gdriveLink)}" target="_blank" class="btn btn-gold">
            <i class="bi bi-cloud-download me-2"></i>Download Photos
        </a>
        ` : ''}
        
        <div class="divider"></div>
        
        <div class="mt-4">
            <small class="text-muted">Booked on ${appointment.createdAtFormatted}</small>
        </div>
    `;

    // Show modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Close modal
function closeModal() {
    const modal = document.getElementById('appointmentModal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('appointmentModal').addEventListener('click', function (e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
