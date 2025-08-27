/**
 * ACCOUNTS SYSTEM JAVASCRIPT
 * App-specific JavaScript for accounts_system
 * Location: public_html/accounts_system/app.js
 */

// Accounts system specific functionality
const AccountsApp = {
    /**
     * Initialize accounts system specific features
     */
    init: function() {
        console.log('Accounts System JavaScript loaded');
        
        // Custom login form handling (if needed beyond unified)
        this.setupCustomLoginFeatures();
        
        // Profile form enhancements
        this.setupProfileForm();
        
        // Password validation
        this.setupPasswordValidation();
    },

    /**
     * Setup custom login features
     */
    setupCustomLoginFeatures: function() {
        const loginForm = document.querySelector('.login-form');
        if (!loginForm) return;

        // Remember me checkbox enhancement
        const rememberMe = loginForm.querySelector('input[name="remember_me"]');
        if (rememberMe) {
            // Add visual feedback
            rememberMe.addEventListener('change', function() {
                const label = this.closest('label');
                if (this.checked) {
                    label.style.fontWeight = '600';
                } else {
                    label.style.fontWeight = '500';
                }
            });
        }

        // Username field auto-lowercase
        const usernameField = loginForm.querySelector('input[name="username"]');
        if (usernameField) {
            usernameField.addEventListener('blur', function() {
                this.value = this.value.toLowerCase();
            });
        }
    },

    /**
     * Setup profile form enhancements
     */
    setupProfileForm: function() {
        const profileForm = document.querySelector('form[action=""][method="post"]');
        if (!profileForm) return;

        // Password confirmation validation
        const newPassword = profileForm.querySelector('input[name="npassword"]');
        const confirmPassword = profileForm.querySelector('input[name="cpassword"]');
        
        if (newPassword && confirmPassword) {
            confirmPassword.addEventListener('input', function() {
                if (this.value && newPassword.value !== this.value) {
                    this.setCustomValidity('Passwords do not match');
                } else {
                    this.setCustomValidity('');
                }
            });

            newPassword.addEventListener('input', function() {
                if (confirmPassword.value && this.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Passwords do not match');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            });
        }

        // Real-time preview of changes in the current profile section
        this.setupProfilePreview();
    },

    /**
     * Setup real-time profile preview
     */
    setupProfilePreview: function() {
        const formFields = {
            'username': { input: 'input[name="username"]', display: '#currentUsername' },
            'email': { input: 'input[name="email"]', display: '#currentEmail' },
            'first_name': { input: 'input[name="first_name"]', display: '#currentFirstName' },
            'last_name': { input: 'input[name="last_name"]', display: '#currentLastName' },
            'phone': { input: 'input[name="phone"]', display: '#currentPhone' },
            'blog_newsletter': { input: 'input[name="blog_newsletter"]', display: '#currentNewsletter', type: 'checkbox' }
        };
        
        // Store original values
        const originalValues = {};
        Object.keys(formFields).forEach(field => {
            const displayElement = document.querySelector(formFields[field].display);
            if (displayElement) {
                if (field === 'blog_newsletter') {
                    originalValues[field] = displayElement.textContent.trim();
                } else {
                    originalValues[field] = displayElement.textContent.trim();
                }
            }
        });

        // Setup real-time preview for each field
        Object.keys(formFields).forEach(field => {
            const fieldConfig = formFields[field];
            const inputElement = document.querySelector(fieldConfig.input);
            const displayElement = document.querySelector(fieldConfig.display);
            
            if (inputElement && displayElement) {
                const eventType = fieldConfig.type === 'checkbox' ? 'change' : 'input';
                
                inputElement.addEventListener(eventType, function() {
                    let newValue, displayValue, isChanged;
                    
                    if (fieldConfig.type === 'checkbox') {
                        displayValue = this.checked ? 'Subscribed' : 'Not subscribed';
                        isChanged = (this.checked && originalValues[field] === 'Not subscribed') || 
                                   (!this.checked && originalValues[field] === 'Subscribed');
                    } else {
                        newValue = this.value.trim();
                        displayValue = newValue || 'Not provided';
                        isChanged = newValue !== '' && displayValue !== originalValues[field];
                    }
                    
                    displayElement.textContent = displayValue;
                    
                    if (isChanged) {
                        displayElement.style.color = '#0ea5e9';
                        displayElement.style.fontWeight = '600';
                        displayElement.classList.add('profile-preview-changed');
                        displayElement.classList.add('updated');
                    } else {
                        displayElement.style.color = '#212529';
                        displayElement.style.fontWeight = '600';
                        displayElement.classList.remove('profile-preview-changed');
                        displayElement.classList.remove('updated');
                    }
                });
            }
        });

        // Password status tracking
        this.setupPasswordStatusTracking();

        // Password strength indicator
        const newPasswordInput = document.querySelector('input[name="npassword"]');
        if (newPasswordInput) {
            this.setupPasswordStrengthIndicator(newPasswordInput);
        }
    },

    /**
     * Setup password status tracking
     */
    setupPasswordStatusTracking: function() {
        const newPasswordInput = document.querySelector('input[name="npassword"]');
        const confirmPasswordInput = document.querySelector('input[name="cpassword"]');
        const passwordStatusElement = document.querySelector('#currentPasswordStatus');
        
        if (!newPasswordInput || !confirmPasswordInput || !passwordStatusElement) return;
        
        const updatePasswordStatus = () => {
            const newPassword = newPasswordInput.value.trim();
            const confirmPassword = confirmPasswordInput.value.trim();
            
            if (newPassword === '' && confirmPassword === '') {
                passwordStatusElement.textContent = 'Unchanged';
                passwordStatusElement.style.color = '#6c757d';
                passwordStatusElement.classList.remove('profile-preview-changed');
            } else if (newPassword !== '' && confirmPassword !== '' && newPassword === confirmPassword) {
                passwordStatusElement.textContent = 'Will be updated';
                passwordStatusElement.style.color = '#0ea5e9';
                passwordStatusElement.style.fontWeight = '600';
                passwordStatusElement.classList.add('profile-preview-changed');
            } else if (newPassword !== '' || confirmPassword !== '') {
                passwordStatusElement.textContent = 'Incomplete';
                passwordStatusElement.style.color = '#dc3545';
                passwordStatusElement.style.fontWeight = '600';
                passwordStatusElement.classList.remove('profile-preview-changed');
            }
        };
        
        newPasswordInput.addEventListener('input', updatePasswordStatus);
        confirmPasswordInput.addEventListener('input', updatePasswordStatus);
    },

    /**
     * Setup password strength indicator
     */
    setupPasswordStrengthIndicator: function(passwordInput) {
        // Create strength indicator element
        const strengthIndicator = document.createElement('div');
        strengthIndicator.className = 'password-strength-indicator';
        strengthIndicator.innerHTML = `
            <div class="strength-bar">
                <div class="strength-fill"></div>
            </div>
            <div class="strength-text">Password strength: <span class="strength-level">None</span></div>
        `;
        
        // Add styles
        const style = document.createElement('style');
        style.textContent = `
            .password-strength-indicator {
                margin-top: 8px;
                font-size: 12px;
            }
            .strength-bar {
                height: 4px;
                background: #e9ecef;
                border-radius: 2px;
                overflow: hidden;
                margin-bottom: 5px;
            }
            .strength-fill {
                height: 100%;
                width: 0%;
                transition: all 0.3s ease;
                border-radius: 2px;
            }
            .strength-text {
                color: #6c757d;
            }
            .strength-level {
                font-weight: 600;
            }
        `;
        document.head.appendChild(style);
        
        // Insert after password field
        passwordInput.closest('.form-group').insertAdjacentElement('afterend', strengthIndicator);
        
        const strengthFill = strengthIndicator.querySelector('.strength-fill');
        const strengthLevel = strengthIndicator.querySelector('.strength-level');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = this.calculatePasswordStrength(password);
            
            strengthFill.style.width = strength.percentage + '%';
            strengthFill.style.backgroundColor = strength.color;
            strengthLevel.textContent = strength.text;
            strengthLevel.style.color = strength.color;
        }.bind(this));
    },

    /**
     * Calculate password strength
     */
    calculatePasswordStrength: function(password) {
        if (!password) {
            return { percentage: 0, color: '#e9ecef', text: 'None' };
        }
        
        let score = 0;
        const checks = {
            length: password.length >= 8,
            lowercase: /[a-z]/.test(password),
            uppercase: /[A-Z]/.test(password),
            numbers: /\d/.test(password),
            special: /[^a-zA-Z0-9]/.test(password)
        };
        
        score += checks.length ? 2 : (password.length >= 5 ? 1 : 0);
        score += checks.lowercase ? 1 : 0;
        score += checks.uppercase ? 1 : 0;
        score += checks.numbers ? 1 : 0;
        score += checks.special ? 1 : 0;
        
        if (score <= 2) {
            return { percentage: 25, color: '#dc3545', text: 'Weak' };
        } else if (score <= 3) {
            return { percentage: 50, color: '#fd7e14', text: 'Fair' };
        } else if (score <= 4) {
            return { percentage: 75, color: '#ffc107', text: 'Good' };
        } else {
            return { percentage: 100, color: '#198754', text: 'Strong' };
        }
    },
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    AccountsApp.init();
});

// Auto-focus first input on login page
if (document.querySelector('.login')) {
    document.addEventListener('DOMContentLoaded', function() {
        const firstInput = document.querySelector('.login-form input[type="text"], .login-form input[type="email"]');
        if (firstInput) {
            firstInput.focus();
        }
    });
}
