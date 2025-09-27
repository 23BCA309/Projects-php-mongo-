<?php
// Include session check for authentication
require_once 'session_check.php';
$isLoggedIn = checkSession();
$user = getUserInfo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Classes - Sunrise Yoga</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css" />
  <style>
    .class-image {
      width: 100%;
      height: 120px;
      background: linear-gradient(135deg, #ffecd2, #fcb69f);
      border-radius: 12px;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      position: relative;
      overflow: hidden;
      transition: transform 0.3s ease;
    }
    .class-image::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(45deg, rgba(255, 193, 7, 0.1), rgba(255, 152, 0, 0.1));
      z-index: 1;
    }
    .class-image .emoji {
      position: relative;
      z-index: 2;
    }
    .card:hover .class-image {
      transform: scale(1.05);
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
      display: none;
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
  </style>
</head>
<body>
  <!-- Header -->
  <?php include 'header.php'; ?>
  
  <?php if (!$isLoggedIn): ?>
  <!-- Login Required Overlay -->
  <div class="login-required-overlay">
    <div class="login-prompt">
      <h3>🏃‍♀️ Login Required</h3>
      <p>Join our community to access yoga classes and start your wellness journey!</p>
      <a href="login-registration.php" class="btn-login">Login / Register</a>
      <a href="home.php" class="btn-home">Back to Home</a>
    </div>
  </div>
  <?php endif; ?>

  <section id="classes" class="section">
    <div class="container">
      <header class="section__head">
        <h2>Popular Classes</h2>
        <p class="muted">Bright, upbeat sessions to match your mood.</p>
      </header>

      <div class="grid grid--cards">
        <article class="card" data-playlist="c1">
          <div class="class-image">
            <span class="emoji">🌅</span>
          </div>
          <div class="card__body">
            <span class="chip chip--pink">Morning</span>
            <h3>Sunrise Flow</h3>
            <p>Wake up gently with juicy stretches and light heat.</p>
            <p class="meta">20 min • All levels</p>
          </div>
        </article>

        <article class="card" data-playlist="c2">
          <div class="class-image">
            <span class="emoji">💪</span>
          </div>
          <div class="card__body">
            <span class="chip chip--orange">Energy</span>
            <h3>Happy Power</h3>
            <p>Playful vinyasa with bright beats and smiles.</p>
            <p class="meta">30 min • Intensity 3/5</p>
          </div>
        </article>

        <article class="card" data-playlist="c3">
          <div class="class-image">
            <span class="emoji">🧘‍♀️</span>
          </div>
          <div class="card__body">
            <span class="chip chip--teal">Calm</span>
            <h3>Cozy Stretch</h3>
            <p>Release hips, hamstrings, and neck tension.</p>
            <p class="meta">25 min • All levels</p>
          </div>
        </article>
      </div>

      <!-- Class 1 -->
      <div id="c1" class="playlist">
        <h4>Sunrise Flow Class – Videos</h4>
        <div class="video-frame"><iframe id="playerC1" allowfullscreen></iframe></div>
        <ul>
          <li data-video="v7AYKMP6rOE"><img src="https://img.youtube.com/vi/v7AYKMP6rOE/mqdefault.jpg"> Morning Flow – 15 min</li>
          <li data-video="ml6cT4AZdqI"><img src="https://img.youtube.com/vi/ml6cT4AZdqI/mqdefault.jpg"> Energizing Flow – 20 min</li>
          <li data-video="x3ZkJ3H6t9Y"><img src="https://img.youtube.com/vi/x3ZkJ3H6t9Y/mqdefault.jpg"> Breath Balance – 18 min</li>
        </ul>
      </div>

      <!-- Class 2 -->
      <div id="c2" class="playlist">
        <h4>Happy Power Class – Videos</h4>
        <div class="video-frame"><iframe id="playerC2" allowfullscreen></iframe></div>
        <ul>
          <li data-video="4pKly2JojMw"><img src="https://img.youtube.com/vi/4pKly2JojMw/mqdefault.jpg"> Power Vinyasa – 25 min</li>
          <li data-video="KIn3_6b9K9o"><img src="https://img.youtube.com/vi/KIn3_6b9K9o/mqdefault.jpg"> Core Blast – 18 min</li>
          <li data-video="i6T6STl-MRg"><img src="https://img.youtube.com/vi/i6T6STl-MRg/mqdefault.jpg"> Balance Boost – 20 min</li>
        </ul>
      </div>

      <!-- Class 3 -->
      <div id="c3" class="playlist">
        <h4>Cozy Stretch Class – Videos</h4>
        <div class="video-frame"><iframe id="playerC3" allowfullscreen></iframe></div>
        <ul>
          <li data-video="L_xrDAtykMI"><img src="https://img.youtube.com/vi/L_xrDAtykMI/mqdefault.jpg"> Gentle Stretch – 22 min</li>
          <li data-video="uW2FkCzw8d4"><img src="https://img.youtube.com/vi/uW2FkCzw8d4/mqdefault.jpg"> Back & Neck Care – 15 min</li>
          <li data-video="8TuRYV71Rgo"><img src="https://img.youtube.com/vi/8TuRYV71Rgo/mqdefault.jpg"> Relaxation Flow – 25 min</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <?php include 'footer.php'; ?>

  <script>
    const classCards = document.querySelectorAll(".card");
    const playlists = document.querySelectorAll(".playlist");

    // Show playlist when class clicked
    classCards.forEach(card => {
      card.addEventListener("click", () => {
        playlists.forEach(pl => pl.style.display = "none");
        const target = document.getElementById(card.dataset.playlist);
        target.style.display = "block";
        window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
      });
    });

    // Play video when clicked
    document.querySelectorAll(".playlist ul li").forEach(item => {
      item.addEventListener("click", () => {
        const videoId = item.getAttribute("data-video");
        const playlist = item.closest(".playlist");
        const frameBox = playlist.querySelector(".video-frame");
        const iframe = frameBox.querySelector("iframe");

        frameBox.style.display = "block";
        iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
        window.scrollTo({ top: frameBox.offsetTop - 100, behavior: 'smooth' });
      });
    });
  </script>
</body>
</html>