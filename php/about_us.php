<?php
// Include session check but don't require login for about page
require_once 'session_check.php';
$isLoggedIn = checkSession(); // Optional check, no redirect
$user = getUserInfo(); // Get user info or guest info
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Sunrise Yoga</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <!-- Header -->
  <?php include 'header.php';?>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <div class="hero__inner">
        <div class="hero__text">
          <h1>Discover Your Inner Peace at <span class="accent">Sunrise Yoga</span></h1>
          <p>Where ancient wisdom meets modern wellness. Join our community of yogis on a transformative journey toward balance, strength, and mindfulness.</p>
          <div class="cta-row">
            <a href="classes.php" class="btn btn--primary">Explore Classes</a>
            <a href="contact.php" class="btn btn--outline">Get in Touch</a>
          </div>
        </div>
        <div class="hero__art">
          <div class="blob b1"></div>
          <div class="blob b2"></div>
          <div class="blob b3"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <main>
    <section class="section">
      <div class="container about-flex">
        <div class="about-text">
          <div class="section__head">
            <h2>Our Story</h2>
            <p class="muted">Born from a passion for holistic wellness</p>
          </div>
          <p>
            Founded in 2020, <strong>Sunrise Yoga</strong> began as a small community of dedicated practitioners seeking to create a sanctuary for mind-body wellness. What started as intimate group sessions in a local park has evolved into a comprehensive yoga studio offering diverse classes, workshops, and wellness courses.
          </p>
          <p>
            Our founder, Sarah Chen, discovered yoga during a challenging period in her life. The practice not only helped her find physical strength and flexibility but also provided mental clarity and emotional balance. Inspired by this transformation, she set out to create a space where others could experience the same profound benefits.
          </p>
          <p>
            Today, Sunrise Yoga serves hundreds of students weekly, from complete beginners taking their first downward dog to advanced practitioners deepening their meditation practice. We believe that yoga is for every body, every mind, and every spirit.
          </p>
        </div>
        <div class="about-image">
          <img src="img.avif" alt="Yoga Practice at Sunrise Yoga Studio" />
        </div>
      </div>
    </section>

    <!-- Mission & Values -->
    <section class="section section--alt">
      <div class="container">
        <div class="section__head">
          <h2>Our Mission & Values</h2>
          <p class="muted">Guiding principles that shape everything we do</p>
        </div>
        <div class="grid grid--cards">
          <div class="card">
            <div class="thumb t1"></div>
            <div class="card__body">
              <h3>Inclusivity</h3>
              <p>Yoga for every body, regardless of age, size, experience level, or background. We create a welcoming space where everyone feels they belong.</p>
            </div>
          </div>
          <div class="card">
            <div class="thumb t2"></div>
            <div class="card__body">
              <h3>Authenticity</h3>
              <p>We honor the ancient traditions of yoga while making them accessible and relevant to modern life. No pretense, just genuine practice.</p>
            </div>
          </div>
          <div class="card">
            <div class="thumb t3"></div>
            <div class="card__body">
              <h3>Growth</h3>
              <p>Every class is an opportunity to learn and grow. We encourage students to progress at their own pace while supporting their individual journeys.</p>
            </div>
          </div>
          <div class="card">
            <div class="thumb t4"></div>
            <div class="card__body">
              <h3>Community</h3>
              <p>Building connections and fostering relationships. Our studio is more than a place to practice—it's a community of like-minded individuals.</p>
            </div>
          </div>
          <div class="card">
            <div class="thumb t5"></div>
            <div class="card__body">
              <h3>Wellness</h3>
              <p>Promoting holistic health that encompasses physical, mental, and spiritual well-being. Yoga is just the beginning of a healthier lifestyle.</p>
            </div>
          </div>
          <div class="card">
            <div class="thumb t6"></div>
            <div class="card__body">
              <h3>Sustainability</h3>
              <p>Mindful of our environmental impact, we incorporate eco-friendly practices and promote conscious living both on and off the mat.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- What Makes Us Different -->
    <section class="section">
      <div class="container">
        <div class="section__head">
          <h2>What Makes Us Different</h2>
          <p class="muted">More than just yoga classes—we offer a complete wellness experience</p>
        </div>
        <div class="grid grid--programs">
          <div class="program">
            <h3>🏆 Expert Instructors</h3>
            <p>Our certified teachers bring years of experience and diverse specializations, ensuring you receive personalized guidance and safe alignment cues.</p>
          </div>
          <div class="program">
            <h3>🎯 Diverse Course Offerings</h3>
            <p>From gentle Hatha for beginners to dynamic Vinyasa flows and restorative Yin sessions—we have something for every mood and energy level.</p>
          </div>
          <div class="program">
            <h3>🌱 Holistic Approach</h3>
            <p>Beyond physical postures, we incorporate meditation, breathwork, and philosophy to support your complete well-being.</p>
          </div>
          <div class="program">
            <h3>📅 Flexible Scheduling</h3>
            <p>With classes throughout the day and online options, we make it easy to fit yoga into your lifestyle, not the other way around.</p>
          </div>
          <div class="program">
            <h3>🤝 Community Events</h3>
            <p>Regular workshops, retreats, and social gatherings help you connect with fellow yogis and deepen your practice.</p>
          </div>
          <div class="program">
            <h3>💝 Personalized Attention</h3>
            <p>Small class sizes ensure you get the individual attention and modifications you need to practice safely and effectively.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="section">
      <div class="container">
        <div class="section__head">
          <h2>What Our Students Say</h2>
          <p class="muted">Real stories from our yoga community</p>
        </div>
        <div class="grid grid--cards">
          <div class="card">
            <div class="card__body">
              <p class="u-sub">"Sunrise Yoga transformed my relationship with stress. The instructors are incredibly knowledgeable and supportive."</p>
              <p class="meta">— Emma S., 2 years practicing</p>
            </div>
          </div>
          <div class="card">
            <div class="card__body">
              <p class="u-sub">"As a beginner, I was nervous about joining. The welcoming atmosphere and patient teaching made all the difference."</p>
              <p class="meta">— James L., 6 months practicing</p>
            </div>
          </div>
          <div class="card">
            <div class="card__body">
              <p class="u-sub">"The variety of classes keeps things interesting. I can always find something that matches my energy level that day."</p>
              <p class="meta">— Maria G., 3 years practicing</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="section section--alt">
      <div class="container">
        <div style="text-align: center; padding: 36px 0;">
          <h2>Ready to Start Your Journey?</h2>
          <p class="muted" style="margin-bottom: 24px;">Join our community and discover the transformative power of yoga</p>
          <div class="cta-row">
            <a href="classes.php" class="btn btn--primary">View Classes</a>
            <a href="contact.php" class="btn btn--outline">Contact Us</a>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- Footer -->
  <?php include 'footer.php';?>

</body>
</html>