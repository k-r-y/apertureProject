// CSRF Token Auto-Refresh Handler
// Prevents booking submission errors due to expired tokens

document.addEventListener('DOMContentLoaded', function () {
    let csrfToken = document.querySelector('input[name="csrfToken"]')?.value;
    let pageLoadTime = Date.now();

    // Refresh CSRF token every 20 minutes (session typically lasts 30 min)
    const TOKEN_REFRESH_INTERVAL = 20 * 60 * 1000; // 20 minutes

    // Check if page has been inactive (user left and came back)
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            const timeAway = Date.now() - pageLoadTime;

            // If user was away for more than 20 minutes, refresh token
            if (timeAway > TOKEN_REFRESH_INTERVAL) {
                refreshCSRFToken();
            }
        }
    });

    // Refresh token before form submission
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        const originalSubmit = bookingForm.onsubmit;

        bookingForm.addEventListener('submit', async function (e) {
            const tokenAge = Date.now() - pageLoadTime;

            // If token is older than 20 minutes, refresh it first
            if (tokenAge > TOKEN_REFRESH_INTERVAL) {
                e.preventDefault();

                const success = await refreshCSRFToken();
                if (success) {
                    // Token refreshed, submit the form
                    bookingForm.submit();
                }
            }
        }, true); // Use capture phase to run before other handlers
    }

    async function refreshCSRFToken() {
        try {
            const response = await fetch('../includes/api/refresh_csrf.php', {
                method: 'GET',
                credentials: 'include'
            });

            if (!response.ok) {
                showTokenExpiredMessage();
                return false;
            }

            const data = await response.json();

            if (data.success && data.token) {
                // Update token in form
                const tokenInput = document.querySelector('input[name="csrfToken"]');
                if (tokenInput) {
                    tokenInput.value = data.token;
                    csrfToken = data.token;
                    pageLoadTime = Date.now();
                    console.log('CSRF token refreshed successfully');
                    return true;
                }
            } else {
                showTokenExpiredMessage();
                return false;
            }
        } catch (error) {
            console.error('Failed to refresh CSRF token:', error);
            showTokenExpiredMessage();
            return false;
        }
    }

    function showTokenExpiredMessage() {
        if (typeof LuxuryModal !== 'undefined') {
            LuxuryModal.show({
                title: 'Session Expired',
                message: 'Your session has expired. The page will refresh to restore your session. Your form data will be preserved.',
                icon: 'warning',
                confirmText: 'Refresh Page',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                // Save form data before refresh
                saveFormState();
                window.location.reload();
            });
        } else {
            alert('Your session has expired. Please refresh the page.');
            window.location.reload();
        }
    }

    function saveFormState() {
        const form = document.getElementById('bookingForm');
        if (!form) return;

        const formData = new FormData(form);
        const stateData = {};

        for (let [key, value] of formData.entries()) {
            if (key !== 'csrfToken' && key !== 'paymentProof') {
                stateData[key] = value;
            }
        }

        sessionStorage.setItem('bookingFormData', JSON.stringify(stateData));
    }
});
