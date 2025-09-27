<?php
  session_start();
  include 'otp_functions.php';
  include 'email_sender.php';
  include 'connect.php';

  $errorScripts = '';
  
  // Check if database connection is successful
  if (!$con) {
    $errorScripts .= '<script>
          document.getElementById("registerContainer").scrollIntoView(true);
          document.getElementById("registerContainer").insertAdjacentHTML("beforeend", "<p class=\"error\" style=\"font-size:24px; color: red;\">Database connection failed. Please try again later.</p>");
        </script>';
  }

  if(isset($_POST['createbtn']) && $_SERVER['REQUEST_METHOD']=="POST"){
    
    // Store form data in session for persistence during OTP verification
    if (!isset($_POST['otp']) || empty($_POST['otp'])) {
      $_SESSION['reg_data'] = [
        'username' => trim($_POST['new_name'] ?? ''),
        'email' => trim($_POST['new_email'] ?? ''),
        'role' => 'learner', // Default role for all users
        'rawPassword' => trim($_POST['new_password'] ?? ''),
        'cnfpassword' => trim($_POST['new_cnfpassword'] ?? ''),
        'terms' => $_POST['terms'] ?? ''
      ];
    }
    
    // Retrieve data from session if OTP is being verified
    if (isset($_POST['otp']) && !empty($_POST['otp']) && isset($_SESSION['reg_data'])) {
      $username = $_SESSION['reg_data']['username'];
      $email = $_SESSION['reg_data']['email'];
      $role = $_SESSION['reg_data']['role'];
      $rawPassword = $_SESSION['reg_data']['rawPassword'];
      $cnfpassword = $_SESSION['reg_data']['cnfpassword'];
      $terms = $_SESSION['reg_data']['terms'];
    } else {
      // Get data from POST for initial submission
      $username = trim($_POST['new_name'] ?? '');
      $email = trim($_POST['new_email'] ?? '');
      $role = 'learner'; // Default role for all users
      $rawPassword = trim($_POST['new_password'] ?? '');
      $cnfpassword = trim($_POST['new_cnfpassword'] ?? '');
      $terms = $_POST['terms'] ?? '';
    }
    
    $password = password_hash($rawPassword, PASSWORD_DEFAULT);
    $canInsert = true;

    // Validation
    if(empty($username) || empty($email) || empty($rawPassword) || empty($cnfpassword)){
      $errorScripts .= '<script>
            document.getElementById("registerContainer").scrollIntoView(true);
            document.getElementById("registerContainer").insertAdjacentHTML("beforeend", "<p class=\"error\" style=\"font-size:24px;\">All Fields Are Required!</p>");
          </script>';
      $canInsert = false;
    }
    else if($rawPassword !== $cnfpassword){
      $errorScripts .= "<script>
            showError('confirm-password','Password MisMatch');
          </script>";
      $canInsert = false;
    }
    else if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{8,}$/', $rawPassword)) {
      $errorScripts .= "<script>
            showError('register-password','Password must be at least 8 chars, include upper, lower, number & special char.');
          </script>";
      $canInsert = false;
    }
    
    // Validate terms checkbox
    if ($terms !== 'on') {
      $errorScripts .= "<script>
            showError('terms','You must agree to the Terms & Conditions');
          </script>";
      $canInsert = false;
    }

    // Check if email already exists
    if($con && $canInsert) {
      $check_query = "SELECT email FROM users_tbl WHERE email=?";
      $check_stmt = $con->prepare($check_query);
      if ($check_stmt) {
        $check_stmt->bind_param('s', $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        if($check_result && mysqli_num_rows($check_result) > 0){
          $errorScripts .= "<script>
                showError('register-email','Email already registered');
              </script>";
          $canInsert = false;
        }
        $check_stmt->close();
      }
    }
    
    // Process registration
    if($canInsert && $con){
      // Check if OTP is provided
      if(isset($_POST['otp']) && !empty($_POST['otp'])) {
        $user_otp = trim($_POST['otp']);
        
        // Verify OTP
        if(otp_checker($user_otp)) {
          // OTP is valid, proceed with registration
          $query = "INSERT INTO users_tbl (username, email, role, password) VALUES(?,?,?,?)";
          $stmt = $con->prepare($query);
          if (!$stmt) {
            $errorScripts .= '<script>
                  document.getElementById("registerContainer").scrollIntoView(true);
                  document.getElementById("registerContainer").insertAdjacentHTML("beforeend", "<p class=\"error\" style=\"font-size:18px; color: red;\">Database preparation error. Please try again.</p>");
                </script>';
          } else {
            $stmt->bind_param('ssss', $username, $email, $role, $password);
            $res = $stmt->execute();
            if ($res) {
              // Clear session data after successful registration
              unset($_SESSION['reg_data']);
              unset($_SESSION['otp']);
              unset($_SESSION['otp_hash']);
              
              header("Location: home.php?msg=Registration+Successful");
              exit();
            } else {
              $errorScripts .= '<script>
                    document.getElementById("registerContainer").scrollIntoView(true);
                    document.getElementById("registerContainer").insertAdjacentHTML("beforeend", "<p class=\"error\" style=\"font-size:18px; color: red;\">Registration failed: ' . $stmt->error . '</p>");
                  </script>';
            }
            $stmt->close();
          }
        } else {
          $errorScripts .= '<script>
                document.getElementById("registerContainer").scrollIntoView(true);
                document.getElementById("registerContainer").insertAdjacentHTML("beforeend", "<p class=\"error\" style=\"font-size:18px; color: red;\">Invalid OTP. Please try again.</p>");
                showPopup("Invalid OTP", "The OTP you entered is incorrect. Please check your email and try again.");
              </script>';
        }
      } else {
        // First submission - Send OTP
        list($otp, $hash_otp) = otp_Generator();
        $email_sent = Email_sender($otp, $email);
        
        if ($email_sent) {
          $errorScripts .= '<script>
            document.addEventListener("DOMContentLoaded", function() {
              showPopup("OTP Sent", "An OTP has been sent to ' . $email . '. Please check your inbox (and spam folder) and enter the OTP to complete your registration.");
              // Keep form data visible for user reference
              document.getElementById("register-name").value = "' . htmlspecialchars($username, ENT_QUOTES) . '";
              document.getElementById("register-email").value = "' . htmlspecialchars($email, ENT_QUOTES) . '";
              document.getElementById("register-password").value = "' . htmlspecialchars($rawPassword, ENT_QUOTES) . '";
              document.getElementById("confirm-password").value = "' . htmlspecialchars($cnfpassword, ENT_QUOTES) . '";
              if ("' . $terms . '" === "on") {
                document.getElementById("terms").checked = true;
              }
            });
          </script>';
        } else {
          $errorScripts .= '<script>
            document.getElementById("registerContainer").scrollIntoView(true);
            document.getElementById("registerContainer").insertAdjacentHTML("beforeend", "<p class=\"error\" style=\"font-size:18px; color: red;\">Failed to send OTP. Please check your email address and try again.</p>");
          </script>';
        }
      }
    }
  }

  // Handle login form submission
  if(isset($_POST['loginbtn']) && $_SERVER['REQUEST_METHOD']=="POST"){
    $login_email = trim($_POST['login_email']);
    $login_password = $_POST['login_password'];
    $loginError = '';

    if(empty($login_email)){
      $loginError = 'Email is required';
    } else if (!filter_var($login_email, FILTER_VALIDATE_EMAIL)) {
      $loginError = 'Invalid email format';
    } else if(empty($login_password)){
      $loginError = 'Password is required';
    }

    if(empty($loginError) && $con){
      // Check user credentials using prepared statement
      $login_query = "SELECT * FROM users_tbl WHERE email=?";
      $login_stmt = $con->prepare($login_query);
      if ($login_stmt) {
        $login_stmt->bind_param('s', $login_email);
        $login_stmt->execute();
        $result = $login_stmt->get_result();
        if($result && mysqli_num_rows($result) == 1){
          $user = mysqli_fetch_assoc($result);
          if(password_verify($login_password, $user['password'])){
            // Generate and send OTP for login
            list($login_otp, $hash_otp) = otp_Generator();
            $_SESSION['login_otp_hash'] = $hash_otp;
            $_SESSION['login_email'] = $login_email;
            $_SESSION['login_user_data'] = $user; // Store user data for after OTP verification
            $_SESSION['login_otp_time'] = time();
            
            // Send OTP email
            $otp_sent = Email_sender($login_otp, $login_email, "Login OTP - Sunrise Yoga");
            
            if($otp_sent){
              $errorScripts .= '<script>
                document.addEventListener("DOMContentLoaded", function() {
                  window.isLoginOtpContext = true;
                  showPopup("OTP Sent", "Please check your email for the OTP to complete login.");
                  // Keep form data visible
                  document.getElementById("login-email").value = "' . htmlspecialchars($login_email, ENT_QUOTES) . '";
                  document.getElementById("login-password").value = "' . htmlspecialchars($login_password, ENT_QUOTES) . '";
                });
              </script>';
            } else {
              $loginError = 'Failed to send OTP. Please try again.';
            }
          } else {
            $loginError = 'Incorrect password';
          }
        } else {
          $loginError = 'User not found';
        }
        $login_stmt->close();
      } else {
        $loginError = 'Database query error. Please try again.';
      }
    } else if (empty($loginError) && !$con) {
      $loginError = 'Database connection error. Please try again later.';
    }
  }
  
  // Handle login OTP verification
  if(isset($_POST['verify_login_otp']) && $_SERVER['REQUEST_METHOD']=="POST"){
    $loginError = '';
    
    if(isset($_SESSION['login_otp_hash']) && isset($_SESSION['login_user_data'])){
      $submitted_otp = trim($_POST['login_otp']);
      $otp_time = $_SESSION['login_otp_time'];
      
      // Check if OTP is expired (5 minutes)
      if(time() - $otp_time > 300){
        unset($_SESSION['login_otp_hash']);
        unset($_SESSION['login_email']);
        unset($_SESSION['login_user_data']);
        unset($_SESSION['login_otp_time']);
        $loginError = 'OTP has expired. Please try logging in again.';
      } else {
        // Verify OTP using proper hash comparison
        $secret = getenv('SECRET_KEY') ?: 'default_secret_key';
        $user_otp_hash = hash_hmac('sha256', $submitted_otp, $secret);
        $is_otp_valid = hash_equals($_SESSION['login_otp_hash'], $user_otp_hash);
        
        // Debug logging
        error_log("OTP Verification Debug:");
        error_log("Submitted OTP: " . $submitted_otp);
        error_log("Expected Hash: " . $_SESSION['login_otp_hash']);
        error_log("Generated Hash: " . $user_otp_hash);
        error_log("Is Valid: " . ($is_otp_valid ? 'YES' : 'NO'));
        
        if($is_otp_valid){
          // OTP verified, complete login with enhanced session
          $user = $_SESSION['login_user_data'];
        
        // Include session_check.php for enhanced session management
        require_once 'session_check.php';
        
        // Debug: Log user data before session init
        error_log("Login OTP verified for user: " . print_r($user, true));
        
        // Map database fields to expected session format
        $user_session_data = [
            'id' => $user['id'] ?? $user['user_id'] ?? null,
            'username' => $user['username'] ?? $user['name'] ?? $user['full_name'] ?? '',
            'email' => $user['email'] ?? '',
            'role' => $user['role'] ?? 'learner'
        ];
        
        // Debug: Log mapped user data
        error_log("Mapped user session data: " . print_r($user_session_data, true));
        
        // Clear login OTP session data FIRST
        unset($_SESSION['login_otp_hash']);
        unset($_SESSION['login_email']);
        unset($_SESSION['login_user_data']);
        unset($_SESSION['login_otp_time']);
        
        // Now initialize secure session with mapped user data
        $session_init_result = initUserSession($user_session_data);
        
        if (!$session_init_result) {
            error_log("ERROR: Failed to initialize session!");
            $loginError = 'Login failed. Please try again.';
        } else {
            // Debug: Verify session was created
            error_log("Session after initUserSession: " . print_r($_SESSION, true));
            
            // Regenerate session ID for security
            regenerateSessionId();
            
            // Set success message
            setMessage('Welcome back! You have been successfully logged in.', 'success');
            
            header("Location: home.php");
            exit();
        }
        } else {
          $loginError = 'Invalid OTP. Please try again.';
        }
      }
    } else {
      $loginError = 'Session expired. Please try logging in again.';
    }
  }
  
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sunrise Yoga| Login</title>

  <!-- Fonts & Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Open+Sans:wght@300;400;500&display=swap"
    rel="stylesheet">

  <script src="login-registration-validation.js"></script>
  <link rel="stylesheet" href="login-registration.css" />
  <script>

    // Restore last active form on page load
    window.addEventListener('DOMContentLoaded', function () {
      const lastForm = localStorage.getItem('activeForm');
      if (lastForm === 'register') {
        showRegister();
      } else {
        showLogin();
      }
    });

    // Update localStorage when switching forms
    function showRegister() {
      document.getElementById('appContainer').classList.add('register-active');
      localStorage.setItem('activeForm', 'register');
    }
    function showLogin() {
      document.getElementById('appContainer').classList.remove('register-active');
      localStorage.setItem('activeForm', 'login');
    }

    // Attach event listeners to forms to prevent default submission and call validation functions
    document.addEventListener('DOMContentLoaded', function() {
      const loginForm = document.querySelector('#loginContainer form');
      if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
          if (!submitLogin(event)) {
            event.preventDefault();
          }
        });
      }
      const registerForm = document.querySelector('#registerContainer form');
      if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
          if (!submitRegister(event)) {
            event.preventDefault();
          }
        });
      }
    });

  </script>
</head>

<body>

  <div class="container" id="appContainer">
    <div class="brand-side">
      <div class="logo">Sunrise Yoga</div>
      <div class="yoga-icon">🧘</div>
      <p class="brand-text">Quick morning energizers, cozy stretch breaks, and calming breath—designed to make you
        smile.</p>

      <div class="trial-info">
        <h3>Start 7-day free trial</h3>
        <p>Browse classes</p>
        <div class="features">
          <div class="feature">Beginner-friendly</div>
          <div class="feature">No props needed</div>
        </div>
        <p style="margin-top:10px; color:#566e5f">10–40 min</p>
      </div>

      <div class="leaf leaf-1">🍃</div>
      <div class="leaf leaf-2">🍃</div>
    </div>

    <div class="forms-side">
      <div class="forms-container">

        <!-- Login Form -->
        <div class="form login-container" id="loginContainer">
          <h2 class="form-title">Welcome Back</h2>
          <p class="subheading">Sign in to continue your journey</p>
          <?php if(!empty($loginError)){ echo '<div class="error-message error" style="margin-bottom:10px;">'.$loginError.'</div>'; } ?>
          <form action="" method="post" onsubmit="return submitLogin(event)">

            <div class="input-group">
              <i class="fas fa-user"></i>
              <input type="text" id="login-email" name="login_email" placeholder="Email">
            </div>

            <div class="input-group">
              <i class="fas fa-lock"></i>
              <input type="password" id="login-password" name="login_password" placeholder="Password">
              <span class="password-toggle" onclick="togglePassword('login-password', this)">
                <i class="fas fa-eye"></i>
              </span>
            </div>

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
              <label style="display:flex;align-items:center;gap:8px;color:#5b7164;font-size:14px">
                <input type="checkbox" id="login-remember"> Remember
              </label>
              <div class="forgot-link">
                <a onclick="openForgot()">Forgot your password?</a>
              </div>
            </div>

            <button type="submit" class="btn" name="loginbtn">Sign In</button>
          </form>
          <div class="form-footer">New to Sunrise Yoga? <a onclick="showRegister()">Create Account</a></div>
        </div>

        <!-- Registration Form -->
        <div class="form register-container" id="registerContainer">
          <h2 class="form-title">Begin Your Journey</h2>
          <p class="subheading">Create an account to get started</p>
          <form  onsubmit="return submitRegister(event) " method="post">

            <div class="input-group">
              <i class="fas fa-user"></i>
              <input type="text" id="register-name" name="new_name" placeholder="Full Name">
            </div>

            <div class="input-group">
              <i class="fas fa-envelope"></i>
              <input type="email" id="register-email" name="new_email" placeholder="Email Address">
            </div>


            <div class="input-group">
              <i class="fas fa-lock"></i>
              <input type="password" id="register-password" name="new_password" placeholder="Password">
              <span class="password-toggle" onclick="togglePassword('register-password', this)"><i
                  class="fas fa-eye"></i></span>
            </div>

            <div class="input-group">
              <i class="fas fa-lock"></i>
              <input type="password" id="confirm-password" name="new_cnfpassword" placeholder="Confirm Password">
              <span class="password-toggle" onclick="togglePassword('confirm-password', this)"><i
                  class="fas fa-eye"></i></span>
            </div>

            <div class="terms">
              <input type="checkbox" id="terms" name="terms"> <label for="terms">I agree to the <a>Terms &amp; Conditions</a></label>
            </div>

            <input type="hidden" name="otp" id="hidden-otp">

            <button type="submit" class="btn" name="createbtn" >Create Account</button>
          </form>
          <div class="form-footer">Already have an account? <a onclick="showLogin()">Sign In</a></div>
          <?php if(isset($message)){
            echo '<div class="error-message error">'.$message.'</div>';
          } ?>
        </div>

      </div>
    </div>
  </div>

  <!-- Forgot Password Modal (hidden by default) -->
  <div class="modal-backdrop" id="forgotModal">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="forgotTitle">
      <h3 id="forgotTitle">Reset your password</h3>
      <p>Enter the email associated with your account and we'll send a reset link.</p>

      <div style="margin:12px 0 18px">
        <input id="forgot-email" type="email" placeholder="Your email address"
          style="width:100%; padding:12px 14px; border-radius:10px; border:1px solid #e6efe7; background:#fbfdfb">
      </div>

      <div style="display:flex;gap:10px">
        <button class="btn" style="flex:1" onclick="submitForgot()">Send Reset Link</button>
        <button onclick="closeForgot()"
          style="flex:0 0 100px; border-radius:10px; border:none; background:#f2f4f2; color:#3d4b42; cursor:pointer">Cancel</button>
      </div>
    </div>
  </div>

  <!-- OTP Modal (hidden by default) -->
  <div class="modal-backdrop" id="otpModal" style="display:none;">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="otpTitle">
      <h3 id="otpTitle">Enter OTP</h3>
      <p>Please enter the One-Time Password sent to your email.</p>

      <div style="margin:12px 0 18px">
        <input id="otp-input" type="text" maxlength="6" placeholder="Enter OTP"
          style="width:100%; padding:12px 14px; border-radius:10px; border:1px solid #e6efe7; background:#fbfdfb; font-size:18px; letter-spacing:8px; text-align:center;">
      </div>

      <div style="display:flex;gap:10px">
        <button class="btn" style="flex:1" name="otpbtn" id="otp-verify-btn" onclick="handleOtpSubmit()">Verify OTP</button>
        <button onclick="closeOtp()"
          style="flex:0 0 100px; border-radius:10px; border:none; background:#f2f4f2; color:#3d4b42; cursor:pointer">Cancel</button>
      </div>
      <div id="otp-error" class="error-message" style="display:none; margin-top:10px;"></div>
    </div>
  </div>

    <!-- Popup -->
  <div id="popup" class="popup-overlay">
    <div class="popup-box">
      <h2></h2>
      <p></p>
      <button onclick="closePopup()">OK</button>
    </div>
  </div>

  <script>
    // Global variable to track OTP context
    window.otpContext = 'registration'; // 'registration' or 'login'
    
    // Handle OTP submit - route to correct function based on context
    function handleOtpSubmit() {
      console.log('handleOtpSubmit called, context:', window.otpContext);
      console.log('isLoginOtpContext:', typeof window.isLoginOtpContext !== 'undefined' ? window.isLoginOtpContext : 'undefined');
      
      // Check both our context variable and the global flag
      if (window.otpContext === 'login' || (typeof window.isLoginOtpContext !== 'undefined' && window.isLoginOtpContext)) {
        console.log('Calling submitLoginOtp');
        
        // Check if submitLoginOtp function exists
        if (typeof window.submitLoginOtp === 'function') {
          return window.submitLoginOtp();
        } else if (typeof submitLoginOtp === 'function') {
          return submitLoginOtp();
        } else {
          console.error('submitLoginOtp function not found, implementing inline');
          return handleLoginOtpSubmit();
        }
      } else {
        console.log('Calling submitOtp (registration)');
        if (typeof submitOtp === 'function') {
          return submitOtp();
        } else {
          console.error('submitOtp function not found');
          return false;
        }
      }
    }
    
    // Fallback login OTP submission function
    function handleLoginOtpSubmit() {
      console.log('=== INLINE LOGIN OTP SUBMIT ===');
      const otpInput = document.getElementById('otp-input');
      const otpError = document.getElementById('otp-error');
      
      if (!otpInput || !otpError) {
        console.error('Required elements not found');
        return false;
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
      
      // Append to document and submit
      document.body.appendChild(form);
      console.log('Submitting form...');
      form.submit();
      
      return true;
    }
    
    // Open OTP modal for login
    function openLoginOtp() {
      console.log('openLoginOtp called');
      window.otpContext = 'login';
      const otpBtn = document.getElementById('otp-verify-btn');
      if (otpBtn) {
        otpBtn.textContent = 'Verify Login OTP';
      }
      document.getElementById('otpModal').style.display = 'flex';
      // Clear previous input and errors
      document.getElementById('otp-input').value = '';
      document.getElementById('otp-error').style.display = 'none';
    }
    
    // Open OTP modal for registration
    function openRegistrationOtp() {
      console.log('openRegistrationOtp called');
      window.otpContext = 'registration';
      const otpBtn = document.getElementById('otp-verify-btn');
      if (otpBtn) {
        otpBtn.textContent = 'Verify OTP';
      }
      document.getElementById('otpModal').style.display = 'flex';
      // Clear previous input and errors
      document.getElementById('otp-input').value = '';
      document.getElementById('otp-error').style.display = 'none';
    }
    

    // Forgot modal controls
    function openForgot() {
      document.getElementById('forgotModal').style.display = 'flex';
    }
    function closeForgot() {
      document.getElementById('forgotModal').style.display = 'none';
    }
    function submitForgot() {
      const email = document.getElementById('forgot-email').value.trim();
      if (!email) { alert('Please enter your email.'); return; }
      // simulate sending link
      alert('If an account exists for ' + email + ', a password reset link has been sent.');
      closeForgot();
    }

    // Toggle password visibility with icon swap
    function togglePassword(id, el) {
      const input = document.getElementById(id);
      if (!input) return;
      const icon = el.querySelector('i');
      if (input.type === 'password') { input.type = 'text'; if (icon) { icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); } }
      else { input.type = 'password'; if (icon) { icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); } }
    }



    // small focus animation (optional)
    document.querySelectorAll('.input-group input, .input-group select').forEach(inp => {
      inp.addEventListener('focus', () => inp.parentElement.style.transform = 'scale(1.02)');
      inp.addEventListener('blur', () => inp.parentElement.style.transform = 'scale(1)');
    });

    // close modal on backdrop click
    document.getElementById('forgotModal').addEventListener('click', (e) => {
      if (e.target === e.currentTarget) closeForgot();
    });
  </script>
  <?php echo $errorScripts; ?>

</body>

</html>
