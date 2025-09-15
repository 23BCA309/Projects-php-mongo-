function submitLogin(event) {
    // Prevent form from submitting (if called from form submit)
    event.preventDefault();

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
    let regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    if (!password) {
        showError('login-password', 'Password is required');
        isValid = false;
    } else if (!regex.test(password)) {
        showError('login-password', 'Password must be at least 8 chars, include upper, lower, number & special char.');
        isValid = false;
    }

    // If validation passes, proceed with login
    if (isValid) {
        // Here you would typically make an API call
        console.log('Login credentials:', { email, password });

        // Example: Simulate API call
        simulateLogin(email, password);

        // Or redirect (if not using AJAX)
        // window.location.href = '/dashboard';
    }

    return isValid;
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

function submitRegister(event) {
    console.log('submitRegister called');

    // Get form values
    const name = document.getElementById('register-name').value.trim();
    const email = document.getElementById('register-email').value.trim();
    const role = document.getElementById('selected').getAttribute('data-value');// Added role field
    const password = document.getElementById('register-password').value;
    const confirmPassword = document.getElementById('register-confirm-password').value;
    const termsChecked = document.getElementById('terms').checked;

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

    // Role validation
    if (!role) {
        showError('register-dropdown', 'Please select a role');
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

   if(isValid){
        return isValid;
    }
    else{
        alert("Please fix the errors before submitting.");
        event.preventDefault();
        return false;
    }
}