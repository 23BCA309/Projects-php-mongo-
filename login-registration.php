<?php

  include'connect.php';
  
  if(isset($_POST['createbtn']) && $_SERVER['REQUEST_METHOD']=="POST"){
    $username=trim($_POST['new_name']);
    $email=trim($_POST['new_email']);
    $role=trim($_POST['new_role']);
    $password=trim(password_hash($_POST['new_password'], PASSWORD_DEFAULT));
    $cnfpassword=trim($_POST['new_cnfpassword']);
    
    if(empty($role) || empty($username) || empty($email) || empty($password) || empty($cnfpassword)  ){
      echo"";
      exit();
    }
    
    if($password !== $cnfpassword){
      echo"<script>
            showError(confirm-password,'Password MisMatch');
          </script>";
      exit();
    }

    if ($role !=="learner" && $role !=="teacher"){
       echo"<script>
            showError(dropdown,'invalid role selected');
          </script>";
      exit();
    }

    $query="INSERT INTO users_tbl (username, email, role, password) VALUES(?,?,?,?)";

    $res=$con->prepare($query)->bind_param('ssss',$fields_data)->execute();

    if($res !== true){
      $message="Error in registration";
    }
    else{
      header("location:home.php?msg=Registration+Successful");
      exit();
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

          <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" id="login-email" placeholder="Username or Email">
          </div>

          <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" id="login-password" placeholder="Password">
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

          <button class="btn" id="btn-login" onclick="submitLogin(event)">Sign In</button>

          <div class="form-footer">New to Feel-Good Yoga? <a onclick="showRegister()">Create Account</a></div>
        </div>

        <!-- Registration Form -->
        <div class="form register-container" id="registerContainer">
          <h2 class="form-title">Begin Your Journey</h2>
          <p class="subheading">Create an account to get started</p>
          <form action="" onsubmit="return submitRegister(event)" method="post">

            <div class="input-group">
              <i class="fas fa-user"></i>
              <input type="text" id="register-name" name="new_name" placeholder="Full Name">
            </div>

            <div class="input-group">
              <i class="fas fa-envelope"></i>
              <input type="email" id="register-email" name="new_email" placeholder="Email Address">
            </div>

            <div class="input-group">
                <i class="fas fa-user-tag"></i>
                <div id="register-dropdown">
                    <div id="selected" data-value="">Select Role</div>
                    <div class="options">
                        <div class="option" data-value="Learner" >Learner</div>
                        <div class="line"></div>
                        <div class="option" data-value="teacher" >Teacher</div>
                    </div>
                </div>
                <input type="hidden" name="new_role" id="hidden-role">
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
              <span class="password-toggle" oncick="togglePassword('confirm-password', this)"><i
                  class="fas fa-eye"></i></span>
            </div>

            <div class="terms">
              <input type="checkbox" id="terms"> <label for="terms">I agree to the <a>Terms &amp; Conditions</a></label>
            </div>

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

  <script>
    // dropdown code
    selected=document.getElementById('selected');
    options=document.querySelector('.options');
    options_list=document.querySelectorAll('.option');
    
    // show and hide the dropdownlist
    selected.addEventListener('click',()=>{
        options.style.display = options.style.display==='block'? 'none':'block';
    });

    // set the selected option
    options_list.forEach( option => {
        option.addEventListener('click',()=>{ 
            selected.innerHTML = option.innerHTML;
            selected.setAttribute('data-value',option.getAttribute('data-value'));
            options.style.display ='none';
            document.getElementById('hidden-role').value = option.getAttribute('data-value');
        
        });
    });

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
  
</body>

</html>