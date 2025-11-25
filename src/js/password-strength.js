/**
 * Password Strength Meter
 * 
 * Provides real-time password strength feedback and validation
 * 
 * Usage:
 * <input type="password" id="password" data-password-strength>
 * <div id="password-strength-meter"></div>
 * 
 * Then call: initPasswordStrengthMeter('password', 'password-strength-meter');
 */

/**
 * Calculate password strength score
 * 
 * @param {string} password - Password to evaluate
 * @returns {object} - {score: 0-4, feedback: array, strength: string}
 */
function calculatePasswordStrength(password) {
    let score = 0;
    const feedback = [];

    if (!password || password.length === 0) {
        return {
            score: 0,
            feedback: ['Enter a password'],
            strength: 'none',
            color: '#6c757d'
        };
    }

    // Length check
    if (password.length < 8) {
        feedback.push('At least 8 characters required');
    } else {
        score++;
        if (password.length >= 12) score++;
    }

    // Uppercase check
    if (!/[A-Z]/.test(password)) {
        feedback.push('Add uppercase letters (A-Z)');
    } else {
        score++;
    }

    // Lowercase check
    if (!/[a-z]/.test(password)) {
        feedback.push('Add lowercase letters (a-z)');
    } else {
        score++;
    }

    // Number check
    if (!/[0-9]/.test(password)) {
        feedback.push('Add numbers (0-9)');
    } else {
        score++;
    }

    // Special character check
    if (!/[^A-Za-z0-9]/.test(password)) {
        feedback.push('Add special characters (!@#$%^&*)');
    } else {
        score++;
    }

    // Check for common weak passwords
    const commonPasswords = [
        'password', '12345678', 'qwerty', 'abc123', 'password123',
        '11111111', 'welcome', 'monkey', '1234567890', 'letmein'
    ];

    if (commonPasswords.includes(password.toLowerCase())) {
        score = Math.max(0, score - 2);
        feedback.push('This password is too common');
    }

    // Check for sequential characters
    if (/(.)\1{2,}/.test(password)) {
        score = Math.max(0, score - 1);
        feedback.push('Avoid repeating characters');
    }

    // Determine strength level
    let strength, color, percentage;

    if (score <= 1) {
        strength = 'Weak';
        color = '#dc3545'; // Red
        percentage = 25;
    } else if (score <= 3) {
        strength = 'Fair';
        color = '#ffc107'; // Yellow
        percentage = 50;
    } else if (score <= 4) {
        strength = 'Good';
        color = '#28a745'; // Green
        percentage = 75;
    } else {
        strength = 'Strong';
        color = '#0d6efd'; // Blue
        percentage = 100;
    }

    return {
        score,
        feedback,
        strength,
        color,
        percentage
    };
}

/**
 * Initialize password strength meter
 * 
 * @param {string} passwordInputId - ID of password input field
 * @param {string} meterContainerId - ID of meter container div
 */
function initPasswordStrengthMeter(passwordInputId, meterContainerId) {
    const passwordInput = document.getElementById(passwordInputId);
    const meterContainer = document.getElementById(meterContainerId);

    if (!passwordInput || !meterContainer) {
        console.error('Password strength meter: Input or container not found');
        return;
    }

    // Create meter HTML
    meterContainer.innerHTML = `
        <div class="password-strength-meter">
            <div class="strength-bar-container">
                <div class="strength-bar" id="${meterContainerId}-bar"></div>
            </div>
            <div class="strength-info">
                <span class="strength-text" id="${meterContainerId}-text">Enter password</span>
                <span class="strength-score" id="${meterContainerId}-score"></span>
            </div>
            <ul class="strength-feedback" id="${meterContainerId}-feedback"></ul>
        </div>
    `;

    const strengthBar = document.getElementById(`${meterContainerId}-bar`);
    const strengthText = document.getElementById(`${meterContainerId}-text`);
    const strengthScore = document.getElementById(`${meterContainerId}-score`);
    const feedbackList = document.getElementById(`${meterContainerId}-feedback`);

    // Update meter on input
    passwordInput.addEventListener('input', function () {
        const result = calculatePasswordStrength(this.value);

        // Update bar
        strengthBar.style.width = result.percentage + '%';
        strengthBar.style.backgroundColor = result.color;

        // Update text
        strengthText.textContent = result.strength;
        strengthText.style.color = result.color;

        // Update score
        if (result.score > 0) {
            strengthScore.textContent = `${result.score}/5`;
            strengthScore.style.display = 'inline';
        } else {
            strengthScore.style.display = 'none';
        }

        // Update feedback
        feedbackList.innerHTML = '';
        if (result.feedback.length > 0) {
            result.feedback.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item;
                feedbackList.appendChild(li);
            });
            feedbackList.style.display = 'block';
        } else {
            feedbackList.style.display = 'none';
        }

        // Add validation state to input
        if (this.value.length > 0) {
            if (result.score >= 3) {
                passwordInput.classList.remove('is-invalid');
                passwordInput.classList.add('is-valid');
            } else {
                passwordInput.classList.remove('is-valid');
                passwordInput.classList.add('is-invalid');
            }
        } else {
            passwordInput.classList.remove('is-valid', 'is-invalid');
        }
    });

    // Add CSS if not already present
    if (!document.getElementById('password-strength-meter-styles')) {
        const style = document.createElement('style');
        style.id = 'password-strength-meter-styles';
        style.textContent = `
            .password-strength-meter {
                margin-top: 0.5rem;
            }
            
            .strength-bar-container {
                width: 100%;
                height: 6px;
                background-color: #e9ecef;
                border-radius: 3px;
                overflow: hidden;
                margin-bottom: 0.5rem;
            }
            
            .strength-bar {
                height: 100%;
                width: 0%;
                transition: all 0.3s ease;
                border-radius: 3px;
            }
            
            .strength-info {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 0.875rem;
                margin-bottom: 0.25rem;
            }
            
            .strength-text {
                font-weight: 600;
            }
            
            .strength-score {
                color: #6c757d;
                font-size: 0.75rem;
            }
            
            .strength-feedback {
                list-style: none;
                padding: 0;
                margin: 0.5rem 0 0 0;
                font-size: 0.75rem;
                color: #6c757d;
                display: none;
            }
            
            .strength-feedback li {
                padding: 0.25rem 0;
            }
            
            .strength-feedback li:before {
                content: "• ";
                color: #dc3545;
                font-weight: bold;
                margin-right: 0.25rem;
            }
        `;
        document.head.appendChild(style);
    }
}

/**
 * Validate password meets minimum requirements
 * 
 * @param {string} password - Password to validate
 * @returns {object} - {valid: boolean, errors: array}
 */
function validatePasswordRequirements(password) {
    const result = calculatePasswordStrength(password);

    return {
        valid: result.score >= 3, // Require at least "Good" strength
        errors: result.feedback,
        strength: result.strength
    };
}

/**
 * Add password visibility toggle
 * 
 * @param {string} passwordInputId - ID of password input
 * @param {string} toggleButtonId - ID of toggle button (optional, will create if not exists)
 */
function addPasswordToggle(passwordInputId, toggleButtonId = null) {
    const passwordInput = document.getElementById(passwordInputId);

    if (!passwordInput) {
        console.error('Password toggle: Input not found');
        return;
    }

    let toggleButton;

    if (toggleButtonId) {
        toggleButton = document.getElementById(toggleButtonId);
    } else {
        // Create toggle button
        toggleButton = document.createElement('button');
        toggleButton.type = 'button';
        toggleButton.className = 'btn btn-outline-secondary password-toggle';
        toggleButton.innerHTML = '<i class="bi bi-eye"></i>';

        // Wrap input in input group if not already
        if (!passwordInput.parentElement.classList.contains('input-group')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'input-group';
            passwordInput.parentNode.insertBefore(wrapper, passwordInput);
            wrapper.appendChild(passwordInput);
            wrapper.appendChild(toggleButton);
        } else {
            passwordInput.parentElement.appendChild(toggleButton);
        }
    }

    toggleButton.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Toggle icon
        const icon = this.querySelector('i');
        if (type === 'text') {
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
}
