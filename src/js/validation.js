/**
 * Luxury Validation System
 * Handles real-time form validation and visual feedback
 */

const LuxuryValidator = {
    init() {
        // Attach to all forms with class 'needs-validation-luxury'
        const forms = document.querySelectorAll('.needs-validation-luxury');
        forms.forEach(form => this.attach(form));
    },

    attach(form) {
        // Disable default HTML5 validation
        form.setAttribute('novalidate', true);

        // Validate on submit
        form.addEventListener('submit', (e) => {
            if (!this.validateForm(form)) {
                e.preventDefault();
                e.stopPropagation();

                // Show summary toast
                if (typeof LuxuryToast !== 'undefined') {
                    LuxuryToast.show({
                        message: 'Please correct the errors in the form before proceeding.',
                        type: 'error',
                        duration: 5000
                    });
                }
            }
        });

        // Real-time validation on input/blur
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => {
                // Clear error immediately on input, validate fully on blur
                if (input.classList.contains('is-invalid')) {
                    this.validateField(input);
                }
            });
        });
    },

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input, select, textarea');

        // Validate all fields
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        // Focus first invalid field
        if (!isValid) {
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
            }
        }

        return isValid;
    },

    validateField(input) {
        // Skip if disabled or hidden
        if (input.disabled || input.type === 'hidden' || input.offsetParent === null) {
            return true;
        }

        let isValid = true;
        let errorMessage = '';

        // 1. Required Check
        if (input.hasAttribute('required') && !input.value.trim()) {
            isValid = false;
            errorMessage = 'This field is required';
        }

        // 2. Email Check
        else if (input.type === 'email' && input.value.trim()) {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(input.value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address';
            }
        }

        // 3. Phone Check (PH Mobile)
        else if (input.name === 'phone' && input.value.trim()) {
            const phonePattern = /^(09|\+639)\d{9}$/;
            if (!phonePattern.test(input.value.replace(/[-\s]/g, ''))) {
                isValid = false;
                errorMessage = 'Please enter a valid PH mobile number (e.g., 09171234567)';
            }
        }

        // 4. Pattern Check (Custom Regex)
        else if (input.getAttribute('pattern') && input.value.trim()) {
            const pattern = new RegExp(input.getAttribute('pattern'));
            if (!pattern.test(input.value)) {
                isValid = false;
                errorMessage = input.getAttribute('title') || 'Invalid format';
            }
        }

        // 5. Min/Max Length
        else if (input.minLength > 0 && input.value.length < input.minLength) {
            isValid = false;
            errorMessage = `Minimum ${input.minLength} characters required`;
        }

        // Update UI
        if (!isValid) {
            this.showError(input, errorMessage);
        } else {
            this.clearError(input);
            // Add success state if value is present
            if (input.value.trim()) {
                input.classList.add('is-valid');
            } else {
                input.classList.remove('is-valid');
            }
        }

        return isValid;
    },

    showError(input, message) {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');

        // Find or create feedback element
        let feedback = input.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            // Check if wrapped in input-group
            if (input.parentElement.classList.contains('input-group')) {
                feedback = input.parentElement.nextElementSibling;
            }
        }

        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            if (input.parentElement.classList.contains('input-group')) {
                input.parentElement.after(feedback);
            } else {
                input.after(feedback);
            }
        }

        feedback.textContent = message;
        feedback.style.display = 'block';
    },

    clearError(input) {
        input.classList.remove('is-invalid');

        let feedback = input.nextElementSibling;
        if (input.parentElement.classList.contains('input-group')) {
            feedback = input.parentElement.nextElementSibling;
        }

        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.style.display = 'none';
            feedback.textContent = '';
        }
    }
};

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    LuxuryValidator.init();
});
