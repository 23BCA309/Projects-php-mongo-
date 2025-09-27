<?php
// Include database connection and session check but don't require login for home page
include 'connect.php';
require_once 'session_check.php';
$isLoggedIn = checkSession(); // Optional check, no redirect
$user = getUserInfo(); // Get user info or guest info
$username = $user['username'];
$userRole = $user['role'];

// Fetch courses from database for display on home page
$featured_courses = [];
$courses_query = "SELECT id, title, description, level, thumbnail, created_at FROM courses ORDER BY created_at DESC LIMIT 6";
$courses_result = mysqli_query($con, $courses_query);

if ($courses_result) {
    while ($course = mysqli_fetch_assoc($courses_result)) {
        // Get video count for each course
        $course_id = $course['id'];
        $video_count_query = "SELECT COUNT(*) as count FROM course_videos WHERE course_id = '$course_id'";
        $video_count_result = mysqli_query($con, $video_count_query);
        $course['video_count'] = $video_count_result ? mysqli_fetch_assoc($video_count_result)['count'] : 0;
        
        $featured_courses[] = $course;
    }
}

// Function to get level color
function getLevelColor($level) {
    switch(strtolower($level)) {
        case 'beginner': return '#28a745';
        case 'intermediate': return '#ffc107';  
        case 'advanced': return '#dc3545';
        default: return '#6c757d';
    }
}

// Function to get level emoji
function getLevelEmoji($level) {
    switch(strtolower($level)) {
        case 'beginner': return '🌱';
        case 'intermediate': return '🌿'; 
        case 'advanced': return '🌳';
        default: return '📚';
    }
}

// Debug: Add this temporarily to check session status
$debug_session = false; // Set to true to see debug info
if ($debug_session) {
    echo "<div style='background: #f0f0f0; padding: 10px; border: 1px solid #ccc; margin: 10px;'>";
    echo "<strong>DEBUG SESSION INFO:</strong><br>";
    echo "isLoggedIn: " . ($isLoggedIn ? 'YES' : 'NO') . "<br>";
    echo "Username: " . $username . "<br>";
    echo "Role: " . $userRole . "<br>";
    echo "Session ID: " . session_id() . "<br>";
    echo "Raw Session: " . print_r($_SESSION, true) . "<br>";
    echo "</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sunrise Yoga</title>
  <meta name="description" content="A bright, colorful yoga site: programs, teachers, and pricing. Pure HTML + CSS." />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css" />
  <style>
    /* Login Required Popup Styles */
    .login-popup {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }

    .login-popup-content {
      background: white;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      text-align: center;
      max-width: 400px;
      margin: 20px;
    }

    .login-popup h3 {
      color: #2d5a3d;
      margin-bottom: 1rem;
    }

    .login-popup p {
      color: #666;
      margin-bottom: 1.5rem;
    }

    .login-popup-buttons {
      display: flex;
      gap: 1rem;
      justify-content: center;
    }

    .btn-login {
      background: #4a7c59;
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 25px;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      transition: background 0.3s;
    }

    .btn-login:hover {
      background: #3d6b4a;
    }

    .btn-cancel {
      background: #f0f0f0;
      color: #666;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 25px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .btn-cancel:hover {
      background: #e0e0e0;
    }

    /* Disabled content styles */
    .content-disabled {
      opacity: 0.6;
      pointer-events: none;
      position: relative;
    }

    .content-disabled::after {
      content: "🔒 Login required to access this content";
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(255, 255, 255, 0.95);
      padding: 1rem 2rem;
      border-radius: 25px;
      border: 2px solid #4a7c59;
      color: #2d5a3d;
      font-weight: 600;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      z-index: 10;
    }

    /* Program click cursor */
    .program {
      cursor: pointer;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .program:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    /* Welcome message for logged in users */
    .welcome-message {
      background: linear-gradient(135deg, #4a7c59, #6ba777);
      color: white;
      padding: 1rem 2rem;
      border-radius: 15px;
      margin-bottom: 2rem;
      text-align: center;
    }

    .welcome-message h3 {
      margin: 0 0 0.5rem 0;
    }

    .welcome-message p {
      margin: 0;
      opacity: 0.9;
    }

    /* Course Meta Styles */
    .course-meta {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.5rem;
    }
    
    .course-level-badge {
      display: inline-block;
      padding: 0.25rem 0.5rem;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: 500;
      color: white;
    }
    
    .video-count {
      color: #666;
      font-size: 0.85rem;
      background: #f0f0f0;
      padding: 0.2rem 0.4rem;
      border-radius: 8px;
    }
    
    /* Horizontal Videos Scroll */
    .videos-horizontal-scroll {
      display: flex;
      gap: 1.5rem;
      overflow-x: auto;
      padding: 1rem 0;
      scroll-behavior: smooth;
      -webkit-overflow-scrolling: touch;
    }
    
    .videos-horizontal-scroll::-webkit-scrollbar {
      height: 8px;
    }
    
    .videos-horizontal-scroll::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    
    .videos-horizontal-scroll::-webkit-scrollbar-thumb {
      background: #4a7c59;
      border-radius: 10px;
    }
    
    .videos-horizontal-scroll::-webkit-scrollbar-thumb:hover {
      background: #3d6b4a;
    }
    
    /* Video Protection Styles */
    .video-container {
      position: relative;
      background: #f8f9fa;
      border-radius: 15px;
      overflow: hidden;
      cursor: pointer;
      transition: transform 0.3s, box-shadow 0.3s;
      min-width: 280px;
      flex-shrink: 0;
    }

    .video-container:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .video-thumbnail {
      width: 100%;
      height: 200px;
      background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      color: #666;
      position: relative;
    }

    .play-button {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 60px;
      height: 60px;
      background: rgba(74, 124, 89, 0.9);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 24px;
      transition: all 0.3s;
    }

    .play-button:hover {
      background: rgba(74, 124, 89, 1);
      transform: translate(-50%, -50%) scale(1.1);
    }

    .video-info {
      padding: 1rem;
    }

    .video-info h4 {
      margin: 0 0 0.5rem 0;
      color: #2d5a3d;
    }

    .video-info p {
      margin: 0;
      color: #666;
      font-size: 14px;
    }

    .video-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 0.5rem;
      font-size: 12px;
      color: #999;
    }

    .video-duration {
      background: rgba(74, 124, 89, 0.1);
      padding: 2px 8px;
      border-radius: 12px;
      color: #4a7c59;
    }

    /* Video Grid */
    .videos-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    /* Course/Program Image Styling */
    .course-image {
      width: 100%;
      height: 180px;
      background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
      border-radius: 12px;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      position: relative;
      overflow: hidden;
    }

    .course-image::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, rgba(74, 124, 89, 0.1), rgba(107, 167, 119, 0.1));
      z-index: 1;
    }

    .course-image .emoji {
      position: relative;
      z-index: 2;
    }

    .program:hover .course-image {
      transform: scale(1.05);
      transition: transform 0.3s ease;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <?php include 'header.php';?>

  <main>
    <!-- Welcome Message for Logged In Users -->
    <?php if ($isLoggedIn): ?>
    <div class="container" style="margin-top: 2rem;">
      <div class="welcome-message">
        <h3>Welcome back, <?php echo htmlspecialchars($username); ?>! 🧘‍♀️</h3>
        <p>Ready to continue your yoga journey? Browse our courses below.</p>
      </div>
    </div>
    <?php endif; ?>

    <!-- Hero -->
    <section id="home" class="hero">
      <div class="container hero__inner">
        <div class="hero__text">
          <h1>SunRise yoga for bright days ☀️</h1>
          <p>Quick morning energizers, cozy stretch breaks, and calming breath—designed to make you smile.</p>
          <div class="cta-row">
            <a class="btn btn--primary" href="about_us.php">Start 7-day free trial</a>
            <a class="btn btn--ghost" href="courses.php">Browse Courses</a>
          </div>
          <div class="badges">
            <span class="badge">Beginner-friendly</span>
            <span class="badge">No props needed</span>
            <span class="badge">10-40 min</span>
          </div>
        </div>
        <div class="hero__art" aria-hidden="true">
          <div class="blob b1"></div>
          <div class="blob b2"></div>
          <div class="blob b3"></div>
        </div>
      </div>
    </section>

    <!-- Featured Videos -->
    <section class="section" id="featured-videos">
      <div class="container">
        <div class="section__head">
          <h2>Featured Yoga Videos 🎥</h2>
          <p class="muted">Watch our most popular yoga sessions and tutorials</p>
        </div>
        <div class="videos-horizontal-scroll">
          <div class="video-container" onclick="handleVideoClick('Morning Sunrise Flow', 'morning_flow.mp4')">
            <div class="video-thumbnail">
              🌅
              <div class="play-button">▶</div>
            </div>
            <div class="video-info">
              <h4>Morning Sunrise Flow</h4>
              <p>Start your day with this energizing 20-minute flow</p>
              <div class="video-meta">
                <span>Beginner Friendly</span>
                <span class="video-duration">20:15</span>
              </div>
            </div>
          </div>
          
          <div class="video-container" onclick="handleVideoClick('Evening Relaxation', 'evening_relax.mp4')">
            <div class="video-thumbnail">
              🌙
              <div class="play-button">▶</div>
            </div>
            <div class="video-info">
              <h4>Evening Relaxation</h4>
              <p>Wind down with gentle stretches and breathing</p>
              <div class="video-meta">
                <span>All Levels</span>
                <span class="video-duration">15:30</span>
              </div>
            </div>
          </div>
          
          <div class="video-container" onclick="handleVideoClick('Core Strengthening', 'core_strength.mp4')">
            <div class="video-thumbnail">
              💪
              <div class="play-button">▶</div>
            </div>
            <div class="video-info">
              <h4>Core Strengthening Yoga</h4>
              <p>Build core strength with targeted poses and flows</p>
              <div class="video-meta">
                <span>Intermediate</span>
                <span class="video-duration">25:45</span>
              </div>
            </div>
          </div>
          
          <div class="video-container" onclick="handleVideoClick('Flexibility & Balance', 'flexibility.mp4')">
            <div class="video-thumbnail">
              🧘‍♀️
              <div class="play-button">▶</div>
            </div>
            <div class="video-info">
              <h4>Flexibility & Balance</h4>
              <p>Improve flexibility and find your balance</p>
              <div class="video-meta">
                <span>All Levels</span>
                <span class="video-duration">18:20</span>
              </div>
            </div>
          </div>
          
          <div class="video-container" onclick="handleVideoClick('Deep Stretching', 'deep_stretch.mp4')">
            <div class="video-thumbnail">
              🧘
              <div class="play-button">▶</div>
            </div>
            <div class="video-info">
              <h4>Deep Stretching</h4>
              <p>Release tension with deep stretching poses</p>
              <div class="video-meta">
                <span>All Levels</span>
                <span class="video-duration">22:10</span>
              </div>
            </div>
          </div>
          
          <div class="video-container" onclick="handleVideoClick('Power Yoga', 'power_yoga.mp4')">
            <div class="video-thumbnail">
              🔥
              <div class="play-button">▶</div>
            </div>
            <div class="video-info">
              <h4>Power Yoga Flow</h4>
              <p>High intensity yoga flow for strength building</p>
              <div class="video-meta">
                <span>Advanced</span>
                <span class="video-duration">35:45</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Free Courses -->
    <section class="section section--alt" id="free-courses">
    <div class="container">
      <div class="section__head">
        <h2>Free Courses <span><a href="#"><img src="plus.png" width="25px" height="25px" alt=""></a></span></h2>
        <p class="muted">Try these beginner-friendly yoga courses at no cost</p>
      </div>
      <div class="grid grid--programs">
        <div class="program" onclick="handleProgramClick('14 Days Hormone Control', 'free')">
          <div class="course-image">
            <span class="emoji">🌸</span>
          </div>
          <h3>14 Days Hormone Control</h3>
          <p>Balance your hormones naturally with guided yoga.</p>
          <?php if ($isLoggedIn): ?>
          <div class="program-status">
            <small style="color: #4a7c59; font-weight: 600;">✓ Available</small>
          </div>
          <?php endif; ?>
        </div>
        <div class="program" onclick="handleProgramClick('15 Days Free Style Yoga', 'free')">
          <div class="course-image">
            <span class="emoji">🧘</span>
          </div>
          <h3>15 Days Free Style Yoga</h3>
          <p>Improve flexibility and mindfulness with freestyle yoga.</p>
          <?php if ($isLoggedIn): ?>
          <div class="program-status">
            <small style="color: #4a7c59; font-weight: 600;">✓ Available</small>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>


   <section class="section" id="premium-courses">
    <div class="container">
      <div class="section__head">
        <h2>Premium Courses <span><a href="#"><img src="plus.png" width="25px" height="25px" alt=""></a></span></h2>
        <p class="muted">Transform your body and mind with our premium courses</p>
      </div>
      <div class="grid grid--programs">
        <div class="program" onclick="handleProgramClick('30 Days Belly Fat Reduction', 'paid')">
          <div class="course-image">
            <span class="emoji">💪</span>
          </div>
          <h3>30 Days Belly Fat Reduction</h3>
          <p>Intense yoga program designed to reduce belly fat and strengthen your core.</p>
          <?php if ($isLoggedIn): ?>
          <div class="program-status">
            <small style="color: #e67e22; font-weight: 600;">💰 Premium</small>
          </div>
          <?php endif; ?>
        </div>
        <div class="program" onclick="handleProgramClick('Advanced Flexibility Yoga', 'paid')">
          <div class="course-image">
            <span class="emoji">🤸‍♀️</span>
          </div>
          <h3>Advanced Flexibility Yoga</h3>
          <p>A premium program to take your flexibility to the next level.</p>
          <?php if ($isLoggedIn): ?>
          <div class="program-status">
            <small style="color: #e67e22; font-weight: 600;">💰 Premium</small>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

    <!-- Dynamic Database Courses -->
    <section id="courses" class="section section--alt">
      <div class="container">
        <header class="section__head">
          <h2>Our Latest Courses <span><a href="courses.php"><img src="plus.png" width="25px" height="25px" alt="View All"></a></span></h2>
          <p class="muted">Discover our expertly crafted yoga courses from beginner to advanced levels.</p>
        </header>
        
        <?php if (empty($featured_courses)): ?>
          <div class="no-courses-message" style="text-align: center; padding: 3rem; color: #666;">
            <h3>📚 No Courses Available Yet</h3>
            <p>We're working on adding amazing yoga courses for you. Please check back soon!</p>
          </div>
        <?php else: ?>
          <div class="grid grid--programs">
            <?php foreach ($featured_courses as $course): ?>
              <div class="program" onclick="handleDatabaseCourseClick(<?php echo $course['id']; ?>, '<?php echo addslashes($course['title']); ?>')">
                <div class="course-image">
                  <?php if ($course['thumbnail'] && file_exists($course['thumbnail'])): ?>
                    <img src="<?php echo htmlspecialchars($course['thumbnail']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                  <?php else: ?>
                    <span class="emoji"><?php echo getLevelEmoji($course['level']); ?></span>
                  <?php endif; ?>
                </div>
                
                <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                
                <div class="course-meta" style="margin-bottom: 0.5rem;">
                  <span class="course-level-badge" style="background-color: <?php echo getLevelColor($course['level']); ?>; color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem; margin-right: 0.5rem;">
                    <?php echo ucfirst($course['level']); ?>
                  </span>
                  <span class="video-count" style="color: #666; font-size: 0.85rem;">
                    <?php echo $course['video_count']; ?> videos
                  </span>
                </div>
                
                <p style="font-size: 0.9rem; color: #666; line-height: 1.4;">
                  <?php echo htmlspecialchars(substr($course['description'], 0, 80) . (strlen($course['description']) > 80 ? '...' : '')); ?>
                </p>
                
                <?php if ($isLoggedIn): ?>
                <div class="program-status">
                  <small style="color: #4a7c59; font-weight: 600;">✓ Available</small>
                </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
          
          <div style="text-align: center; margin-top: 2rem;">
            <a href="courses.php" class="btn btn--primary">View All Courses</a>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Pricing -->
   
  </main>

  <!-- Footer -->
  <?php include 'footer.php';?>

  <!-- Login Required Popup -->
  <div id="loginPopup" class="login-popup">
    <div class="login-popup-content">
      <h3>🔒 Login Required</h3>
      <p>You need to login to access our yoga courses and programs.</p>
      <div class="login-popup-buttons">
        <a href="login-registration.php" class="btn-login">Login / Register</a>
        <button onclick="closeLoginPopup()" class="btn-cancel">Cancel</button>
      </div>
    </div>
  </div>

  <script>
    // Pass PHP variables to JavaScript
    const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
    const username = <?php echo json_encode($username); ?>;

    // Handle video clicks - MAIN PROTECTION FEATURE
    function handleVideoClick(videoTitle, videoFile) {
      console.log('Video clicked:', videoTitle, 'File:', videoFile, 'Logged in:', isLoggedIn);
      
      // Always require login for video playback
      if (!isLoggedIn) {
        showVideoLoginPopup(videoTitle);
        return false;
      }
      
      // If user is logged in, play the video
      alert('Playing video: ' + videoTitle + '\n\nFile: ' + videoFile + '\n\nNote: This would normally open the video player.');
      // Here you would implement actual video playback:
      // window.location.href = 'video-player.php?video=' + encodeURIComponent(videoFile);
      // OR open video in modal player, etc.
    }

    // Handle program/course clicks
    function handleProgramClick(programName, programType) {
      console.log('Program clicked:', programName, 'Type:', programType, 'Logged in:', isLoggedIn);
      
      // Require login for course access
      if (!isLoggedIn) {
        showCourseLoginPopup(programName);
        return false;
      }
      
      // If user is logged in, handle the program access
      switch(programType) {
        case 'free':
          alert('Opening free program: ' + programName + '\n\nNote: This would normally redirect to the course page.');
          // window.location.href = 'course.php?id=' + encodeURIComponent(programName);
          break;
        case 'paid':
          alert('Opening premium program: ' + programName + '\n\nNote: This would check subscription status first.');
          // window.location.href = 'premium-course.php?id=' + encodeURIComponent(programName);
          break;
        case 'regular':
          alert('Opening program: ' + programName + '\n\nNote: This would normally redirect to the course page.');
          // window.location.href = 'course.php?id=' + encodeURIComponent(programName);
          break;
      }
    }

    // Handle database course clicks - NEW FUNCTION
    function handleDatabaseCourseClick(courseId, courseName) {
      console.log('Database course clicked:', courseId, courseName, 'Logged in:', isLoggedIn);
      
      // Require login for course access
      if (!isLoggedIn) {
        showCourseLoginPopup(courseName);
        return false;
      }
      
      // If user is logged in, redirect to courses page with course expanded
      window.location.href = 'courses.php?course=' + courseId;
    }

    // Show video login popup - specific for video content
    function showVideoLoginPopup(videoTitle) {
      const popup = document.getElementById('loginPopup');
      const popupContent = popup.querySelector('.login-popup-content');
      
      // Update popup content for video access
      popupContent.querySelector('h3').textContent = '🎥 Video Access Restricted';
      popupContent.querySelector('p').textContent = `Please login to watch "${videoTitle}". Join our yoga community to unlock all premium video content!`;
      
      popup.style.display = 'flex';
      
      // Add event listener to close popup when clicking outside
      popup.addEventListener('click', function(e) {
        if (e.target === popup) {
          closeLoginPopup();
        }
      });
    }

    // Show course login popup - specific for course content  
    function showCourseLoginPopup(courseName) {
      const popup = document.getElementById('loginPopup');
      const popupContent = popup.querySelector('.login-popup-content');
      
      // Update popup content for course access
      popupContent.querySelector('h3').textContent = '📚 Course Access Required';
      popupContent.querySelector('p').textContent = `Please login to access "${courseName}". Join our community to unlock all yoga courses and programs!`;
      
      popup.style.display = 'flex';
      
      // Add event listener to close popup when clicking outside
      popup.addEventListener('click', function(e) {
        if (e.target === popup) {
          closeLoginPopup();
        }
      });
    }

    // Generic login popup (for backward compatibility)
    function showLoginPopup(itemName) {
      const popup = document.getElementById('loginPopup');
      const popupContent = popup.querySelector('.login-popup-content');
      
      // Update popup content
      popupContent.querySelector('h3').textContent = '🔒 Login Required';
      popupContent.querySelector('p').textContent = `You need to login to access "${itemName}". Join our yoga community today!`;
      
      popup.style.display = 'flex';
      
      // Add event listener to close popup when clicking outside
      popup.addEventListener('click', function(e) {
        if (e.target === popup) {
          closeLoginPopup();
        }
      });
    }

    // Close login popup
    function closeLoginPopup() {
      document.getElementById('loginPopup').style.display = 'none';
    }

    // Handle escape key to close popup
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeLoginPopup();
      }
    });

    // Initialize page - users can browse freely but need login for videos/courses
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Page loaded. User logged in:', isLoggedIn);
      
      // No navigation restrictions - users can browse all pages freely
      // Protection is only applied to video playback and course access
    });
  </script>
  
</body>
</html>
