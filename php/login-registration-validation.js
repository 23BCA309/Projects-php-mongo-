function submitLogin(event) {
    // Get form values
    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;

    // Clear previous error messages
    clearErrorMessages();

    // Validation flags
    let isValid = true;

    // Email validation
    if (!email) {
        showError('login-email', 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError('login-email', 'Please enter a valid email address');
        isValid = false;
    }

    // Password validation
    if (!password) {
        showError('login-password', 'Password is required');
        isValid = false;
    }

    if (!isValid) {
        event.preventDefault();
        return false;
    }

    // If validation passes, allow form submission (will trigger OTP flow)
    return true;
}

// popup message functions
function showPopup(msg,content) {
    console.log('=== SHOW POPUP CALLED ===');
    console.log('Message:', msg);
    console.log('Content:', content);

    const popup = document.getElementById("popup");
    if (popup) {
        popup.querySelector("h2").textContent = msg;
        popup.querySelector("p").textContent = content;
        popup.style.display = "flex";
        console.log('Popup displayed successfully');
    } else {
        console.error('Popup element not found');
    }
}

function closePopup() {
    document.getElementById("popup").style.display = "none";
    var otpModal = document.getElementById("otpModal");
    if (otpModal) {
        // Determine context by checking page state 
        if (typeof window.isLoginOtpContext !== 'undefined' && window.isLoginOtpContext) {
            // Login OTP context - set the context and open modal
            console.log('closePopup: Login context detected');
            if (typeof window.otpContext !== 'undefined') {
                window.otpContext = 'login';
            }
            if (typeof openLoginOtp === 'function') {
                openLoginOtp();
            } else {
                console.log('openLoginOtp function not available, showing modal directly');
                otpModal.style.display = "flex";
            }
        } else {
            // Registration context
            console.log('closePopup: Registration context');
            if (typeof window.otpContext !== 'undefined') {
                window.otpContext = 'registration';
            }
            if (typeof openRegistrationOtp === 'function') {
                openRegistrationOtp();
            } else {
                console.log('openRegistrationOtp function not available, showing modal directly');
                otpModal.style.display = "flex";
            }
        }
    }
}
function closeOtp(){
     var otpModal = document.getElementById("otpModal");
    if (otpModal) otpModal.style.display = "none";
}

// Helper function to validate email format
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Helper function to show error messages
function showError(fieldId, message) { 
    const field = document.getElementById(fieldId);
    const errorElement = document.getElementById(`${fieldId}-error`) || createErrorElement(fieldId);
    
    // Add error class to input field
    field.classList.add('error');

    // Set error message
    errorElement.textContent = message;
    errorElement.style.display = 'block';
}

// Helper function to create error element if it doesn't exist
function createErrorElement(fieldId) {
    const field = document.getElementById(fieldId);
    const errorElement = document.createElement('div');
    errorElement.id = `${fieldId}-error`;
    errorElement.className = 'error-message';

    // Insert error message in the appropriate container
    const inputGroup = field.closest('.input-group');
    const termsContainer = field.closest('.terms');
    
    if (inputGroup) {
        inputGroup.appendChild(errorElement);
    } else if (termsContainer) {
        termsContainer.appendChild(errorElement);
    } else {
        field.parentNode.appendChild(errorElement);
    }

    return errorElement;
}

// Helper function to clear all error messages
function clearErrorMessages() {
    // Remove error classes from inputs
    document.querySelectorAll('.error').forEach(element => {
        element.classList.remove('error');
    });

    // Hide error messages
    document.querySelectorAll('.error-message').forEach(element => {
        element.style.display = 'none';
    });
}

// Simulate login API call (replace with actual API call)
function simulateLogin(email, password) {
    // Show loading state
    const loginBtn = document.getElementById('btn-login');
    const originalText = loginBtn.textContent;
    loginBtn.textContent = 'Logging in...';
    loginBtn.disabled = true;

    // Simulate API delay
    setTimeout(() => {
        // Here you would handle the actual API response
        window.location.href = "home.php";
        // Reset button state (in real app, you'd redirect on success)
        loginBtn.textContent = originalText;
        loginBtn.disabled = false;

    }, 1500);
}

// Password toggle functionality
function togglePassword(fieldId, toggleElement) {
    const passwordField = document.getElementById(fieldId);
    const icon = toggleElement.querySelector('i');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Forgot password modal functions
function openForgot() {
    document.getElementById('forgotModal').style.display = 'flex';
}

function closeForgot() {
    document.getElementById('forgotModal').style.display = 'none';
}

function submitForgot() {
    const emailInput = document.getElementById('forgot-email');
    const email = emailInput.value.trim();
    
    if (!email) {
        alert('Please enter your email address.');
        return;
    }
    
    if (!isValidEmail(email)) {
        alert('Please enter a valid email address.');
        return;
    }
    
    // Here you would typically send a password reset request
    alert('Password reset link has been sent to ' + email);
    closeForgot();
}

// BASIC DEBUGGING - Check if JavaScript is loading
console.log('=== JavaScript file loaded successfully ===');
console.log('Current URL:', window.location.href);
console.log('Current time:', new Date().toLocaleString());

function submitRegister(event) {
    console.log('=== SUBMIT REGISTER CALLED ===');

    // Get form values
    const name = document.getElementById('register-name').value.trim();
    const email = document.getElementById('register-email').value.trim();
    const password = document.getElementById('register-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const termsChecked = document.getElementById('terms').checked;

    console.log('Form values:', { name, email, password, confirmPassword, termsChecked });

    clearErrorMessages();

    let isValid = true;

    // Name validation
    if (!name) {
        showError('register-name', 'Name is required');
        isValid = false;
    }

    // Email validation
    if (!email) {
        showError('register-email', 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError('register-email', 'Please enter a valid email address');
        isValid = false;
    }


    // Password validation
    let regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!password) {
        showError('register-password', 'Password is required');
        isValid = false;
    } else if (!regex.test(password)) {
        showError('register-password', 'Password must be at least 8 chars, include upper, lower, number & special char.');
        isValid = false;
    }

    // Confirm password validation
    if (!confirmPassword) {
        showError('confirm-password', 'Please confirm your password');
        isValid = false;
    } else if (password !== confirmPassword) {
        showError('confirm-password', 'Passwords do not match');
        isValid = false;
    }

    // Terms validation
    if (!termsChecked) {
        showError('terms', 'You must agree to the Terms & Conditions');
        isValid = false;
    }

    // If validation fails, prevent form submission
    if(!isValid){
        event.preventDefault();
        return false;
    }

    // If validation passes, allow form submission to PHP
    return true;
}


// Show login OTP modal function
window.submitLoginOtp = function() {
    console.log('=== SUBMIT LOGIN OTP CALLED ===');
    const otpInput = document.getElementById('otp-input');
    const otpError = document.getElementById('otp-error');
    
    console.log('OTP Input element:', otpInput);
    console.log('OTP Error element:', otpError);
    
    if (!otpInput || !otpError) {
        console.error('Required elements not found');
        return;
    }

    const otpValue = otpInput.value.trim();
    console.log('OTP Value entered:', otpValue);

    // Validate 6-digit numeric OTP
    if (!/^\d{6}$/.test(otpValue)) {
        console.log('Invalid OTP format');
        otpError.textContent = 'Please enter a valid 6-digit OTP.';
        otpError.style.display = 'block';
        return false;
    }

    otpError.style.display = 'none';
    console.log('OTP format valid, creating form');

    // Create a hidden form to submit login OTP
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '';
    
    // Add login OTP field
    const otpField = document.createElement('input');
    otpField.type = 'hidden';
    otpField.name = 'login_otp';
    otpField.value = otpValue;
    form.appendChild(otpField);
    console.log('Added login_otp field:', otpValue);
    
    // Add verification button field
    const verifyField = document.createElement('input');
    verifyField.type = 'hidden';
    verifyField.name = 'verify_login_otp';
    verifyField.value = '1';
    form.appendChild(verifyField);
    console.log('Added verify_login_otp field');
    
    // Log form data before submission
    console.log('Form elements before submission:');
    const formData = new FormData(form);
    for (let [key, value] of formData.entries()) {
        console.log(key + ':', value);
    }
    
    // Append to document and submit
    document.body.appendChild(form);
    console.log('Submitting form...');
    form.submit();
    
    return true;
}

// Show OTP modal function
function submitOtp() {
    const otpInput = document.getElementById('otp-input');
    const otpError = document.getElementById('otp-error');
    const hiddenOtp = document.getElementById('hidden-otp');
    if (!otpInput || !otpError || !hiddenOtp) return;

    const otpValue = otpInput.value.trim();

    // Validate 6-digit numeric OTP
    if (!/^\d{6}$/.test(otpValue)) {
        otpError.textContent = 'Please enter a valid 6-digit OTP.';
        otpError.style.display = 'block';
        return false;
    }

    otpError.style.display = 'none';

    // Set OTP in hidden field and submit the registration form
    hiddenOtp.value = otpValue;
    const registerForm = document.querySelector('#registerContainer form');
    if (registerForm) {
        // Ensure createbtn is sent
        let btn = registerForm.querySelector('input[name="createbtn"]');
        if (!btn) {
            btn = document.createElement('input');
            btn.type = 'hidden';
            btn.name = 'createbtn';
            btn.value = '1';
            registerForm.appendChild(btn);
        }

        // Ensure all form fields are populated before submission
        const nameField = document.getElementById('register-name');
        const emailField = document.getElementById('register-email');
        const passwordField = document.getElementById('register-password');
        const confirmPasswordField = document.getElementById('confirm-password');
        const termsField = document.getElementById('terms');

        // Debug: Log current field values
        console.log('=== OTP Submit Debug Info ===');
        console.log('Name field value:', nameField ? nameField.value : 'NOT FOUND');
        console.log('Email field value:', emailField ? emailField.value : 'NOT FOUND');
        console.log('Password field value:', passwordField ? passwordField.value : 'NOT FOUND');
        console.log('Confirm password field value:', confirmPasswordField ? confirmPasswordField.value : 'NOT FOUND');
        console.log('Terms checked:', termsField ? termsField.checked : 'NOT FOUND');
        console.log('OTP value:', otpValue);

        // Populate all form fields
        if (nameField) {
            registerForm.querySelector('input[name="new_name"]').value = nameField.value;
            console.log('Set new_name to:', nameField.value);
        }
        if (emailField) {
            registerForm.querySelector('input[name="new_email"]').value = emailField.value;
            console.log('Set new_email to:', emailField.value);
        }
        if (passwordField) {
            registerForm.querySelector('input[name="new_password"]').value = passwordField.value;
            console.log('Set new_password to:', passwordField.value);
        }
        if (confirmPasswordField) {
            registerForm.querySelector('input[name="new_cnfpassword"]').value = confirmPasswordField.value;
            console.log('Set new_cnfpassword to:', confirmPasswordField.value);
        }
        if (termsField && termsField.checked) {
            registerForm.querySelector('input[name="terms"]').value = 'on';
            console.log('Set terms to: on');
        }

        // Debug: Log final form data
        console.log('=== Final Form Data Before Submission ===');
        const formData = new FormData(registerForm);
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }

        registerForm.submit();
    }

    return true;
}

