<?php
// Always include session check for header
if (!function_exists('getCurrentUser')) {
    require_once 'session_check.php';
}

// Get current user info with proper validation
$current_user = getCurrentUser();
$isHeaderLoggedIn = $current_user !== null;
$headerUsername = $isHeaderLoggedIn ? $current_user['username'] : 'Guest';
$headerUserRole = $isHeaderLoggedIn ? $current_user['role'] : 'guest';
?>
<header class="site-header">
    <div class="container nav-bar">
      <a class="brand" href="home.php" aria-label="Sunrise Yoga home">
        <span class="sun"></span>
        <span>Sunrise<span class="accent">Yoga</span></span>
      </a>

       <!-- Middle: Navigation -->
    <nav class="main-nav" aria-label="Primary">
      <a href="home.php">Home</a>
      <?php if ($isHeaderLoggedIn): ?>
        <a href="courses.php">Courses</a>
        <a href="videos.php">Videos</a>
      <?php else: ?>
        <a href="#" onclick="showLoginPrompt('courses')">Courses</a>
        <a href="#" onclick="showLoginPrompt('videos')">Videos</a>
      <?php endif; ?>
      <a href="about_us.php">About us</a>
      <?php if ($isHeaderLoggedIn): ?>
        <a href="contact.php">Contact us</a>
      <?php else: ?>
        <a href="#" onclick="showLoginPrompt('contact')">Contact us</a>
      <?php endif; ?>
    </nav>

    <!-- Right: User Menu -->
    <div class="user-menu">
      <?php if ($isHeaderLoggedIn): ?>
        <!-- Logged in user menu -->
        <span class="welcome-text" style="color: #4a7c59; margin-right: 10px; font-size: 14px;">Hi, <?php echo htmlspecialchars($headerUsername); ?>!</span>
        <div class="profile-icon" onclick="toggleMenu()">
          <div class="avatar-circle">
            <span class="avatar-text"><?php echo strtoupper(substr($headerUsername, 0, 2)); ?></span>
          </div>
        </div>

        <!-- Dropdown -->
        <div id="dropdown" class="dropdown">
          <a href="Profile.php">👤 Profile</a>
          <?php if ($headerUserRole === 'admin'): ?>
            <a href="admin-panel.php">🔧 Admin Panel</a>
          <?php endif; ?>
          <a href="logout.php">🚪 Logout</a>
        </div>
      <?php else: ?>
        <!-- Not logged in - show login button -->
        <a href="login-registration.php" class="login-btn" style="background: #4a7c59; color: white; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 14px; transition: background 0.3s;">Login / Register</a>
      <?php endif; ?>
    </div>
  </div>
</header>


<style>
  /* Profile Icon Styles */
  .profile-icon {
    cursor: pointer;
    position: relative;
  }
  
  .avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4a7c59, #6ba777);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
  }
  
  .avatar-circle:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  }
  
  .dropdown {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 10px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    padding: 0;
    min-width: 180px;
    z-index: 1000;
    margin-top: 10px;
    border: 1px solid #e0e0e0;
  }
  
  .dropdown a {
    display: block;
    padding: 12px 16px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.3s ease;
  }
  
  .dropdown a:hover {
    background-color: #f8f9fa;
  }
  
  .dropdown a:last-child {
    border-bottom: none;
    border-radius: 0 0 10px 10px;
  }
  
  .dropdown a:first-child {
    border-radius: 10px 10px 0 0;
  }
  
  .user-menu {
    position: relative;
    display: flex;
    align-items: center;
  }
  
  .welcome-text {
    margin-right: 10px;
    font-size: 14px;
    color: #4a7c59;
    font-weight: 500;
  }
  
  @media (max-width: 768px) {
    .welcome-text {
      display: none;
    }
    
    .avatar-circle {
      width: 35px;
      height: 35px;
      font-size: 12px;
    }
  }
</style>

<body>
  <script>
  function toggleMenu() {
    const dropdown = document.getElementById("dropdown");
    dropdown.style.display = (dropdown.style.display === "flex") ? "none" : "flex";
  }

  // Close dropdown if clicked outside
  window.addEventListener("click", function(e) {
    const dropdown = document.getElementById("dropdown");
    if (!e.target.closest(".user-menu")) {
      dropdown.style.display = "none";
    }
  });
  
  // Show login prompt for protected pages
  function showLoginPrompt(page) {
    const messages = {
      'courses': 'Please login to access our premium courses and start your yoga journey!',
      'videos': 'Join our community to access our complete yoga video library and start your wellness journey!',
      'contact': 'Please login to contact us and get personalized support for your yoga journey!'
    };
    
    const icons = {
      'courses': '🔒',
      'videos': '📹',
      'contact': '📞'
    };
    
    if (confirm(`${icons[page]} Login Required\n\n${messages[page]}\n\nWould you like to go to the login page?`)) {
      window.location.href = 'login-registration.php';
    }
  }
</script>

</body>