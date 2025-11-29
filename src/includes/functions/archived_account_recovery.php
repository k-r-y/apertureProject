<?php
/**
 * Archived Account Recovery Handler
 * Include this file in logIn.php and register.php to handle archived account recovery
 */

// Check and store archived account info in session
function handleArchivedAccount($result, $password) {
    if (isset($result['archived']) && $result['archived']) {
        $_SESSION['archived_account_email'] = $result['email'];
        $_SESSION['archived_account_password'] = $password;
        $_SESSION['show_recovery_modal'] = true;
        return true;
    }
    return false;
}

// Generate the recovery modal JavaScript
function displayRecoveryModal() {
    if (!isset($_SESSION['show_recovery_modal']) || !$_SESSION['show_recovery_modal']) {
        return '';
    }
    
    $email = addslashes($_SESSION['archived_account_email'] ?? '');
    $password = addslashes($_SESSION['archived_account_password'] ?? '');
    $csrfToken = generateCSRFToken();
    
    // Clean up session variables after displaying
    unset($_SESSION['show_recovery_modal']);
    unset($_SESSION['archived_account_email']);
    unset($_SESSION['archived_account_password']);
    
    return <<<MODAL
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'info',
                title: 'Account Archived',
                text: 'Your account has been archived. Would you like to recover it and continue logging in?',
                showCancelButton: true,
                confirmButtonText: 'Yes, Recover My Account',
                cancelButtonText: 'No, Cancel',
                confirmButtonColor: '#212529',
                cancelButtonColor: '#6c757d',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // User wants to recover - call the API
                    const formData = new FormData();
                    formData.append('email', '{$email}');
                    formData.append('password', '{$password}');
                    formData.append('csrfToken', '{$csrfToken}');

                    fetch('./includes/api/recover_archived_account.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Account Recovered!',
                                text: 'Your account has been successfully recovered. Please log in again.',
                                confirmButtonColor: '#212529'
                            }).then(() => {
                                window.location.href = 'logIn.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Recovery Failed',
                                text: data.error || 'Failed to recover account',
                                confirmButtonColor: '#212529'
                            }).then(() => {
                                window.location.href = 'logIn.php';
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred during account recovery',
                            confirmButtonColor: '#212529'
                        }).then(() => {
                            window.location.href = 'logIn.php';
                        });
                    });
                } else {
                    // User cancelled - just redirect to login
                    window.location.href = 'logIn.php';
                }
            });
        });
    </script>
MODAL;
}
