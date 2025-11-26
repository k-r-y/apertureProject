/**
 * Admin Settings Handler
 */

document.addEventListener('DOMContentLoaded', function () {
    loadSettings();
    setupForms();
});

// Load Settings
function loadSettings() {
    fetch('api/settings_api.php?action=get_settings')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const settings = data.settings;

                // Populate inputs
                for (const [key, value] of Object.entries(settings)) {
                    const input = document.getElementById(key);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = value == '1';
                        } else {
                            input.value = value;
                        }
                    }
                }
            }
        })
        .catch(error => console.error('Error loading settings:', error));
}

// Setup Forms
function setupForms() {
    const forms = [
        'generalSettingsForm'
    ];

    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                saveSettings(new FormData(this));
            });
        }
    });

    // Security Form (Password Change) - Keep existing logic or adapt
    const securityForm = document.getElementById('securitySettingsForm');
    if (securityForm) {
        securityForm.addEventListener('submit', handlePasswordChange);
    }
}

// Save Settings
function saveSettings(formData) {
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });

    // Handle checkboxes (unchecked ones don't appear in FormData)
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb => {
        if (cb.name && !data.hasOwnProperty(cb.name)) {
            data[cb.name] = cb.checked ? '1' : '0';
        } else if (cb.name) {
            data[cb.name] = cb.checked ? '1' : '0';
        }
    });

    fetch('api/settings_api.php?action=update_settings', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Settings Saved',
                    text: 'Your changes have been saved successfully.',
                    confirmButtonColor: '#d4af37'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to save settings.',
                    confirmButtonColor: '#d4af37'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An unexpected error occurred.',
                confirmButtonColor: '#d4af37'
            });
        });
}

// Password Change Handler (Adapted from original)
async function handlePasswordChange(e) {
    e.preventDefault();

    const adminPasswordInput = document.getElementById('adminPassword');
    const confirmAdminPasswordInput = document.getElementById('confirmAdminPassword');

    Swal.fire({
        title: 'Confirm Identity',
        html: `<input type="password" id="swal-current-password" class="swal2-input" placeholder="Enter current password">`,
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

            // Validation
            if (!newPassword) return Swal.fire('Error', 'New password is required', 'error');
            if (newPassword !== confirmPassword) return Swal.fire('Error', 'Passwords do not match', 'error');
            if (newPassword.length < 8) return Swal.fire('Error', 'Password must be at least 8 characters', 'error');

            // API Call
            try {
                const response = await fetch('api/change_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ currentPassword, newPassword, confirmPassword })
                });
                const data = await response.json();

                if (data.success) {
                    Swal.fire('Success', 'Password updated successfully', 'success');
                    e.target.reset();
                } else {
                    Swal.fire('Error', data.message || 'Failed to update password', 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'An error occurred', 'error');
            }
        }
    });
}
