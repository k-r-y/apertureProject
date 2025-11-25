/**
 * Admin Settings Password Change Handler
 */

document.addEventListener('DOMContentLoaded', function () {
    // Find the security form
    const securityCard = document.querySelector('.card-solid .card-body:has(#adminPassword)');

    if (securityCard) {
        const securityForm = securityCard.querySelector('form');
        const adminPasswordInput = document.getElementById('adminPassword');
        const confirmAdminPasswordInput = document.getElementById('confirmAdminPassword');

        if (securityForm && adminPasswordInput && confirmAdminPasswordInput) {
            securityForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                // Since admin doesn't have current password field, we need to add one
                // Or use a modal to get it
                Swal.fire({
                    title: 'Confirm Identity',
                    html: `
                        <input type="password" id="swal-current-password" class="swal2-input" placeholder="Enter current password">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Continue',
                    confirmButtonColor: '#d4af37',
                    preConfirm: () => {
                        const currentPassword = document.getElementById('swal-current-password').value;
                        if (!currentPassword) {
                            Swal.showValidationMessage('Current password is required');
                            return false;
                        }
                        return currentPassword;
                    }
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        const currentPassword = result.value;
                        const newPassword = adminPasswordInput.value.trim();
                        const confirmPassword = confirmAdminPasswordInput.value.trim();

                        // Client-side validation
                        const errors = {};

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
                            const response = await fetch('api/change_password.php', {
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
                                    securityForm.reset();
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
                    }
                });
            });
        }
    }
});
