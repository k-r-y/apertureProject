/**
 * User Profile Handler
 */

document.addEventListener('DOMContentLoaded', function () {
    // ==========================================
    // Personal Information Form Handler
    // ==========================================
    const infoForm = document.getElementById('personalInfoForm');

    if (infoForm) {
        infoForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const phone = document.getElementById('contactPhone').value.trim();

            if (!firstName || !lastName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'First Name and Last Name are required',
                    confirmButtonColor: '#d4af37'
                });
                return;
            }

            // Validate Philippine phone number
            if (!phone) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Contact number is required',
                    confirmButtonColor: '#d4af37'
                });
                return;
            } else if (!/^09[0-9]{9}$/.test(phone)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Contact number must be 11 digits and start with 09 (e.g., 09171234567)',
                    confirmButtonColor: '#d4af37'
                });
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Updating Profile...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch('api/update_profile.php?action=update_info', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        firstName: firstName,
                        lastName: lastName,
                        phone: phone
                    })
                });

                const data = await response.json();
                console.log('Profile update response:', data);

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#d4af37'
                    });
                } else {
                    throw new Error(data.message || 'Failed to update profile');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message,
                    confirmButtonColor: '#d4af37'
                });
            }
        });
    }

    // ==========================================
    // Password Change Form Handler
    // ==========================================
    const passwordForm = document.getElementById('passwordForm');

    if (passwordForm) {
        passwordForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const currentPassword = document.getElementById('currentPassword').value.trim();
            const newPassword = document.getElementById('newPassword').value.trim();
            const confirmPassword = document.getElementById('confirmPassword').value.trim();

            // Client-side validation
            const errors = [];

            if (!currentPassword) errors.push('Current password is required');
            if (!newPassword) errors.push('New password is required');
            if (newPassword.length < 8) errors.push('Password must be at least 8 characters');
            if (newPassword !== confirmPassword) errors.push('Passwords do not match');

            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: errors.join('<br>'),
                    confirmButtonColor: '#d4af37'
                });
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Updating Password...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch('api/update_profile.php?action=change_password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        currentPassword: currentPassword,
                        newPassword: newPassword,
                        confirmPassword: confirmPassword
                    })
                });

                const data = await response.json();
                console.log('Password change response:', data);

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#d4af37'
                    }).then(() => {
                        passwordForm.reset();
                    });
                } else {
                    throw new Error(data.message || 'Failed to update password');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message,
                    confirmButtonColor: '#d4af37'
                });
            }
        });
    }

    // ==========================================
    // Account Deletion Handler
    // ==========================================
    const deleteAccountBtn = document.querySelector('button[class*="btn-outline-danger"]');

    if (deleteAccountBtn) {
        deleteAccountBtn.addEventListener('click', async function () {
            const result = await Swal.fire({
                icon: 'warning',
                title: 'Archive Account?',
                html: 'Your account will be archived (not permanently deleted).<br>You can recover it later by contacting support.<br><br><strong>Are you sure?</strong>',
                showCancelButton: true,
                confirmButtonText: 'Yes, Archive My Account',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch('api/archive_account.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' }
                    });

                    const data = await response.json();
                    console.log('Archive response:', data);

                    if (data.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Account Archived',
                            text: 'Your account has been archived. You will be logged out.',
                            confirmButtonColor: '#d4af37'
                        });
                        window.location.href = '../logIn.php';
                    } else {
                        throw new Error(data.message || 'Failed to archive account');
                    }
                } catch (error) {
                    console.error('Archive error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Could not archive account',
                        confirmButtonColor: '#d4af37'
                    });
                }
            }
        });
    }
});
