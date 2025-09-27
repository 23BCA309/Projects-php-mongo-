<?php
// Include session check for authentication
require_once 'session_check.php';
$isLoggedIn = checkSession();
$user = getUserInfo();

// Check if user is trying to access protected content
if (!$isLoggedIn && isset($_GET['access'])) {
    header("Location: login-registration.php?redirect=courses.php&msg=Please+login+to+access+courses");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Courses - Sunrise Yoga</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css" />
  <style>
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
      transition: transform 0.3s ease;
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
    }
    .playlist {
      display: none;
      margin-top: 2rem;
      background: #fff;
      padding: 1rem;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .video-frame {
      width: 100%;
      aspect-ratio: 16/9;
      margin-bottom: 1rem;
      display: none; /* hidden until video clicked */
    }
    .video-frame iframe {
      width: 100%;
      height: 100%;
      border: none;
      border-radius: 10px;
    }
    .playlist ul {
      list-style: none;
      padding: 0;
      margin: 0;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1rem;
    }
    .playlist li {
      cursor: pointer;
      background: #f9f9f9;
      border-radius: 8px;
      padding: .5rem;
      transition: 0.2s;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .playlist li:hover {
      background: #e8f6f5;
    }
    .playlist img {
      width: 100%;
      border-radius: 6px;
      margin-bottom: .5rem;
    }
    
    /* Login Required Overlay */
    .login-required-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
    }
    
    .login-prompt {
      background: white;
      padding: 2rem;
      border-radius: 15px;
      text-align: center;
      max-width: 400px;
      margin: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .login-prompt h3 {
      color: #2d5a3d;
      margin-bottom: 1rem;
    }
    
    .login-prompt p {
      color: #666;
      margin-bottom: 1.5rem;
    }
    
    .btn-login {
      background: #4a7c59;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 25px;
      text-decoration: none;
      display: inline-block;
      margin-right: 1rem;
      transition: background 0.3s;
    }
    
    .btn-login:hover {
      background: #3d6b4a;
    }
    
    .btn-home {
      background: #f0f0f0;
      color: #666;
      padding: 0.75rem 1.5rem;
      border-radius: 25px;
      text-decoration: none;
      display: inline-block;
      transition: background 0.3s;
    }
    
    .btn-home:hover {
      background: #e0e0e0;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <?php include 'header.php'; ?>
  
  <?php if (!$isLoggedIn): ?>
  <!-- Login Required Overlay -->
  <div class="login-required-overlay">
    <div class="login-prompt">
      <h3>🔒 Login Required</h3>
      <p>Please log in to access our premium courses and start your yoga journey!</p>
      <a href="login-registration.php" class="btn-login">Login / Register</a>
      <a href="home.php" class="btn-home">Back to Home</a>
    </div>
  </div>
  <?php endif; ?>

  <section id="courses" class="section section--alt">
    <div class="container">
      <header class="section__head">
        <h2>Feel-good Courses</h2>
        <p class="muted">Transform your wellness journey with our expertly designed courses.</p>
      </header>
      <div class="grid grid--programs">
        <div class="program" data-playlist="playlist1">
          <div class="course-image">
            <span class="emoji">🌅</span>
          </div>
          <h3>7-Day Morning Spark</h3>
          <ul>
            <li>Under 20 minutes</li>
            <li>Gentle energizers</li>
            <li>Stretch + breath</li>
          </ul>
        </div>
        <div class="program" data-playlist="playlist2">
          <div class="course-image">
            <span class="emoji">💃</span>
          </div>
          <h3>14-Day Joyful Flow</h3>
          <ul>
            <li>Playful vinyasa</li>
            <li>Core + balance</li>
            <li>Rest days included</li>
          </ul>
        </div>
        <div class="program" data-playlist="playlist3">
          <div class="course-image">
            <span class="emoji">🌙</span>
          </div>
          <h3>10-Day Unwind</h3>
          <ul>
            <li>Slow stretches</li>
            <li>Neck & back care</li>
            <li>Better sleep</li>
          </ul>
        </div>
      </div>

      <!-- Course 1 -->
      <div id="playlist1" class="playlist">
        <h4>7-Day Morning Spark Course – Videos</h4>
        <div class="video-frame"><iframe id="player1" allowfullscreen></iframe></div>
        <ul>
          <li data-video="v7AYKMP6rOE"><img src="https://img.youtube.com/vi/v7AYKMP6rOE/mqdefault.jpg"> Morning Stretch – 15 min</li>
          <li data-video="ml6cT4AZdqI"><img src="https://img.youtube.com/vi/ml6cT4AZdqI/mqdefault.jpg"> Energizing Flow – 20 min</li>
          <li data-video="x3ZkJ3H6t9Y"><img src="https://img.youtube.com/vi/x3ZkJ3H6t9Y/mqdefault.jpg"> Breath & Balance – 18 min</li>
        </ul>
      </div>

      <!-- Course 2 -->
      <div id="playlist2" class="playlist">
        <h4>14-Day Joyful Flow Course – Videos</h4>
        <div class="video-frame"><iframe id="player2" allowfullscreen></iframe></div>
        <ul>
          <li data-video="4pKly2JojMw"><img src="https://img.youtube.com/vi/4pKly2JojMw/mqdefault.jpg"> Joyful Flow – Day 1</li>
          <li data-video="KIn3_6b9K9o"><img src="https://img.youtube.com/vi/KIn3_6b9K9o/mqdefault.jpg"> Core Balance – Day 2</li>
          <li data-video="i6T6STl-MRg"><img src="https://img.youtube.com/vi/i6T6STl-MRg/mqdefault.jpg"> Playful Strength – Day 3</li>
        </ul>
      </div>

      <!-- Course 3 -->
      <div id="playlist3" class="playlist">
        <h4>10-Day Unwind Course – Videos</h4>
        <div class="video-frame"><iframe id="player3" allowfullscreen></iframe></div>
        <ul>
          <li data-video="L_xrDAtykMI"><img src="https://img.youtube.com/vi/L_xrDAtykMI/mqdefault.jpg"> Relaxing Evening – 20 min</li>
          <li data-video="uW2FkCzw8d4"><img src="https://img.youtube.com/vi/uW2FkCzw8d4/mqdefault.jpg"> Neck & Back Care – 15 min</li>
          <li data-video="8TuRYV71Rgo"><img src="https://img.youtube.com/vi/8TuRYV71Rgo/mqdefault.jpg"> Sleep Prep Stretch – 25 min</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'footer.php'; ?>

  <script>
    const courses = document.querySelectorAll(".program");
    const playlists = document.querySelectorAll(".playlist");

    // show course videos only
    courses.forEach(course => {
      course.addEventListener("click", () => {
        playlists.forEach(pl => pl.style.display = "none"); 
        const target = document.getElementById(course.dataset.playlist);
        target.style.display = "block"; 
        window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
      });
    });

    // load video only when user clicks a thumbnail
    document.querySelectorAll(".playlist ul li").forEach(item => {
      item.addEventListener("click", () => {
        const videoId = item.getAttribute("data-video");
        const playlist = item.closest(".playlist");
        const frameBox = playlist.querySelector(".video-frame");
        const iframe = frameBox.querySelector("iframe");

        frameBox.style.display = "block"; // show video player
        iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
        window.scrollTo({ top: frameBox.offsetTop - 100, behavior: 'smooth' });
      });
    });
  </script>
</body>
</html>