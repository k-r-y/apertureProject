/**
 * Client Management JavaScript
 * Handles all AJAX operations for viewing and editing users
 */

// Get CSRF token from meta tag or generate
function getCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    return token || generateCSRFToken();
}

// Generate CSRF token if not available
function generateCSRFToken() {
    // This should match your CSRF token generation
    return document.querySelector('input[name="csrf_token"]')?.value || '';
}

/**
 * View User Details
 * Fetches and displays user information in read-only modal
 */
function viewUserDetails(userID) {
    // Show loading state
    const modal = new bootstrap.Modal(document.getElementById('viewUserModal'));
    modal.show();

    document.getElementById('viewUserContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-gold" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading user details...</p>
        </div>
    `;

    // Fetch user details
    fetch(`api/get-user-details.php?userID=${userID}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayUserDetails(data.user);
            } else {
                showError('viewUserContent', data.error || 'Failed to load user details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('viewUserContent', 'Network error. Please try again.');
        });
}

/**
 * Display User Details in Modal
 */
function displayUserDetails(user) {
    const createdDate = new Date(user.createdAt).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    const statusClass = user.status === 'Active' ? 'status-confirmed' : 'status-overdue';
    const roleClass = user.role === 'Admin' ? 'bg-danger' : 'bg-soft-gold text-gold';

    const html = `
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label text-muted small">User ID</label>
                <p class="fw-bold">#${user.userID}</p>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Email</label>
                <p class="fw-bold">${user.email}</p>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">First Name</label>
                <p class="fw-bold text-gold">${user.firstName || 'Not set'}</p>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Last Name</label>
                <p class="fw-bold text-gold">${user.lastName || 'Not set'}</p>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Contact Number</label>
                <p class="fw-bold">${user.contactNo || 'Not set'}</p>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Role</label>
                <p><span class="badge ${roleClass}">${user.role}</span></p>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Status</label>
                <p><span class="status-badge ${statusClass}">${user.status}</span></p>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Member Since</label>
                <p class="fw-bold">${createdDate}</p>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Profile Completed</label>
                <p>
                    ${user.profileCompleted
            ? '<i class="bi bi-check-circle-fill text-success"></i> Yes'
            : '<i class="bi bi-x-circle-fill text-danger"></i> No'}
                </p>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Email Verified</label>
                <p>
                    ${user.isVerified
            ? '<i class="bi bi-patch-check-fill text-info"></i> Verified'
            : '<i class="bi bi-exclamation-circle text-warning"></i> Not Verified'}
                </p>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Total Bookings</label>
                <p class="fw-bold">${user.totalBookings}</p>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Confirmed Bookings</label>
                <p class="fw-bold text-success">${user.confirmedBookings}</p>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Total Spent</label>
                <p class="fw-bold text-gold">₱${parseFloat(user.totalSpent).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</p>
            </div>
            ${!user.isVerified ? `
            <div class="col-12 mt-4">
                <button type="button" class="btn btn-gold" onclick="resendVerification(${user.userID})">
                    <i class="bi bi-envelope"></i> Resend Verification Email
                </button>
            </div>
            ` : ''}
        </div>
    `;

    document.getElementById('viewUserContent').innerHTML = html;
}

/**
 * Edit User
 * Opens edit modal with pre-filled data
 */
function editUser(userID) {
    // Show loading state
    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
    modal.show();

    document.getElementById('editUserForm').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-gold" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    // Fetch user details
    fetch(`api/get-user-details.php?userID=${userID}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayEditForm(data.user);
            } else {
                showError('editUserForm', data.error || 'Failed to load user details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('editUserForm', 'Network error. Please try again.');
        });
}

/**
 * Display Edit Form
 */
function displayEditForm(user) {
    const html = `
        <input type="hidden" id="editUserID" value="${user.userID}">
        <input type="hidden" name="csrf_token" value="${getCSRFToken()}">
        
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label text-muted small">User ID</label>
                <p class="fw-bold">#${user.userID}</p>
            </div>
            
            <div class="col-12">
                <label class="form-label text-muted small">Email (Cannot be changed)</label>
                <input type="text" class="form-control" value="${user.email}" disabled>
            </div>
            
            <div class="col-md-6">
                <label class="form-label text-muted small">First Name</label>
                <input type="text" class="form-control" value="${user.firstName || ''}" disabled>
                <small class="text-muted">User must update their own profile</small>
            </div>
            
            <div class="col-md-6">
                <label class="form-label text-muted small">Last Name</label>
                <input type="text" class="form-control" value="${user.lastName || ''}" disabled>
                <small class="text-muted">User must update their own profile</small>
            </div>
            
            <div class="col-md-6">
                <label for="editStatus" class="form-label">Status *</label>
                <select class="form-select" id="editStatus" required>
                    <option value="Active" ${user.status === 'Active' ? 'selected' : ''}>Active</option>
                    <option value="Inactive" ${user.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                </select>
            </div>
            
            <div class="col-md-6">
                <label for="editRole" class="form-label">Role *</label>
                <select class="form-select" id="editRole" required>
                    <option value="User" ${user.role === 'User' ? 'selected' : ''}>User</option>
                    <option value="Admin" ${user.role === 'Admin' ? 'selected' : ''}>Admin</option>
                </select>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="editProfileCompleted" 
                           ${user.profileCompleted ? 'checked' : ''}>
                    <label class="form-check-label" for="editProfileCompleted">
                        Profile Completed
                    </label>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="editIsVerified" 
                           ${user.isVerified ? 'checked' : ''}>
                    <label class="form-check-label" for="editIsVerified">
                        Email Verified
                    </label>
                </div>
            </div>
            
            <div class="col-12 mt-4">
                <button type="button" class="btn btn-gold" onclick="saveUserChanges()">
                    <i class="bi bi-save"></i> Save Changes
                </button>
                <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    `;

    document.getElementById('editUserForm').innerHTML = html;
}

/**
 * Save User Changes
 */
function saveUserChanges() {
    const userID = document.getElementById('editUserID').value;
    const status = document.getElementById('editStatus').value;
    const role = document.getElementById('editRole').value;
    const profileCompleted = document.getElementById('editProfileCompleted').checked ? 1 : 0;
    const isVerified = document.getElementById('editIsVerified').checked ? 1 : 0;
    const csrfToken = getCSRFToken();

    // Show loading state
    const saveBtn = event.target;
    const originalText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

    // Prepare form data
    const formData = new FormData();
    formData.append('userID', userID);
    formData.append('status', status);
    formData.append('role', role);
    formData.append('profileCompleted', profileCompleted);
    formData.append('isVerified', isVerified);
    formData.append('csrf_token', csrfToken);

    // Submit update
    fetch('api/update-user.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;

            if (data.success) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message || 'User updated successfully',
                    timer: 2000,
                    showConfirmButton: false
                });

                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();

                // Update table row
                updateTableRow(data.user);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Failed to update user'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Network error. Please try again.'
            });
        });
}

/**
 * Update Table Row
 */
function updateTableRow(user) {
    const row = document.querySelector(`tr[data-user-id="${user.userID}"]`);
    if (!row) {
        location.reload(); // Reload if row not found
        return;
    }

    // Update role
    const roleCell = row.querySelector('.user-role');
    if (roleCell) {
        const roleClass = user.role === 'Admin' ? 'bg-danger' : 'bg-soft-gold text-gold';
        roleCell.innerHTML = `<span class="badge ${roleClass}">${user.role}</span>`;
    }

    // Update status
    const statusCell = row.querySelector('.user-status');
    if (statusCell) {
        const statusClass = user.status === 'Active' ? 'status-confirmed' : 'status-overdue';
        statusCell.innerHTML = `<span class="status-badge ${statusClass}">${user.status}</span>`;
    }

    // Update profile completed
    const profileCell = row.querySelector('.user-profile');
    if (profileCell) {
        profileCell.innerHTML = user.profileCompleted
            ? '<i class="bi bi-check-circle-fill text-success"></i>'
            : '<i class="bi bi-x-circle-fill text-danger"></i>';
    }

    // Update verified
    const verifiedCell = row.querySelector('.user-verified');
    if (verifiedCell) {
        verifiedCell.innerHTML = user.isVerified
            ? '<i class="bi bi-patch-check-fill text-info"></i>'
            : '<i class="bi bi-exclamation-circle text-warning"></i>';
    }
}

/**
 * Toggle User Status
 */
function toggleUserStatus(userID, currentStatus) {
    const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';

    Swal.fire({
        title: 'Confirm Status Change',
        text: `Change user status to ${newStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#D4AF37',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, change it'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('userID', userID);
            formData.append('csrf_token', getCSRFToken());

            fetch('api/toggle-user-status.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update the row
                        const row = document.querySelector(`tr[data-user-id="${userID}"]`);
                        if (row) {
                            const statusCell = row.querySelector('.user-status');
                            const statusClass = data.newStatus === 'Active' ? 'status-confirmed' : 'status-overdue';
                            statusCell.innerHTML = `<span class="status-badge ${statusClass}">${data.newStatus}</span>`;
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error || 'Failed to toggle status'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Network error. Please try again.'
                    });
                });
        }
    });
}

/**
 * Resend Verification Email
 */
function resendVerification(userID) {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

    const formData = new FormData();
    formData.append('userID', userID);
    formData.append('csrf_token', getCSRFToken());

    fetch('api/resend-verification.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Email Sent!',
                    text: data.message,
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Failed to send email'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.disabled = false;
            btn.innerHTML = originalText;

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Network error. Please try again.'
            });
        });
}

/**
 * Show Error Message
 */
function showError(elementId, message) {
    document.getElementById(elementId).innerHTML = `
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            ${message}
        </div>
    `;
}
