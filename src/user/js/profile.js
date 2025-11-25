/**
 * User Profile Password Change Handler
 */

document.addEventListener('DOMContentLoaded', function () {
    const passwordForm = document.querySelector('.glass-card form');

    if (passwordForm) {
        // Find the password change form specifically
        const currentPasswordInput = document.getElementById('currentPassword');
        const newPasswordInput = document.getElementById('newPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');

        if (currentPasswordInput && newPasswordInput && confirmPasswordInput) {
            passwordForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const currentPassword = currentPasswordInput.value.trim();
                const newPassword = newPasswordInput.value.trim();
                const confirmPassword = confirmPasswordInput.value.trim();

                // Client-side validation
                const errors = {};

                if (!currentPassword) {
                    errors.currentPassword = 'Current password is required';
                }

                if (!newPassword) {
                    errors.newPassword = 'New password is required';
                } else if (newPassword.length < 8) {
                    errors.newPassword = 'Password must be at least 8 characters';
                } else if (!/[A-Z]/.test(newPassword)) {
                    errors.newPassword = 'Password must contain at least one uppercase letter';
                } else if (!/[a-z]/.test(newPassword)) {
                    errors.newPassword = 'Password must contain at least one lowercase letter';
                } else if (!/[0-9]/.test(newPassword)) {
                    errors.newPassword = 'Password must contain at least one number';
                } else if (!/[^A-Za-z0-9]/.test(newPassword)) {
                    errors.newPassword = 'Password must contain at least one special character';
                }

                if (newPassword !== confirmPassword) {
                    errors.confirmPassword = 'Passwords do not match';
                }

                if (Object.keys(errors).length > 0) {
                    const errorMessage = Object.values(errors).join('\n');
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: errorMessage,
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
                    const response = await fetch('../admin/api/change_password.php', {
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

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Your password has been updated successfully',
                            confirmButtonColor: '#d4af37'
                        }).then(() => {
                            // Clear the form
                            passwordForm.reset();
                        });
                    } else {
                        const errorMsg = data.errors ?
                            Object.values(data.errors).join('\n') :
                            data.error || 'Failed to update password';

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg,
                            confirmButtonColor: '#d4af37'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while updating your password',
                        confirmButtonColor: '#d4af37'
                    });
                }
            });
        }
    }
});
