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
</head>
<body>
  <!-- Header -->
  <header class="site-header">
    <div class="container nav">
      <a class="brand" href="home.php" aria-label="Sunrise Yoga home">
        <span class="sun"></span>
        <span>Sunrise<span class="accent">Yoga</span></span>
      </a>
      <nav aria-label="Primary">
        <!-- <a href="classes.php">Classes</a> -->
        <a href="program.php">Programs</a>
        <a href="teachers.php">Teachers</a>
        <a href="about_us.php">About us</a>
        <!-- <a href="shop.php">shop</a> -->
        <!-- <a href="#pricing" class="btn btn--pill">Start Free</a> -->
      </nav>
    </div>
  </header>

  <main>
    <!-- Hero -->
    <section id="home" class="hero">
      <div class="container hero__inner">
        <div class="hero__text">
          <h1>Feel-good yoga for bright days ☀️</h1>
          <p>Quick morning energizers, cozy stretch breaks, and calming breath—designed to make you smile.</p>
          <div class="cta-row">
            <a class="btn btn--primary" href="about_us.php">Start 7-day free trial</a>
            <a class="btn btn--ghost" href="program.php">Browse Program</a>
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

    <!-- Classes -->
    

    <!-- Programs -->
    <section id="programs" class="section section--alt">
      <div class="container">
        <header class="section__head">
          <h2>Feel-good programs</h2>
          <p class="muted">Short plans to build a habit—no pressure, all sunshine.</p>
        </header>
        <div class="grid grid--programs">
          <div class="program">
            <h3>7-Day Morning Spark</h3>
            <ul>
              <li>Under 20 minutes</li>
              <li>Gentle energizers</li>
              <li>Stretch + breath</li>
            </ul>
          </div>
          <div class="program">
            <h3>14-Day Joyful Flow</h3>
            <ul>
              <li>Playful vinyasa</li>
              <li>Core + balance</li>
              <li>Rest days included</li>
            </ul>
          </div>
          <div class="program">
            <h3>10-Day Unwind</h3>
            <ul>
              <li>Slow stretches</li>
              <li>Neck & back care</li>
              <li>Better sleep</li>
            </ul>
          </div>

          <header class="section__head">
          <!-- <h2>programs</h2> -->
          <p class="muted">you need more habit program then click hear <a class="link" href="program.php">Program</a></p>
        </header>

        </div>
      </div>
    </section>

    

    <!-- Teachers -->
    <section id="teachers" class="section">
      <div class="container">
        <header class="section__head">
          <h2>Meet the teachers</h2>
          <p class="muted">Friendly humans with bright cues and kind energy.</p>
        </header>
        <div class="grid grid--teachers">
          <figure class="teacher">
            <div class="avatar a1" aria-hidden="true"></div>
            <figcaption>
              <h3>Asha Rao</h3>
              <p class="muted">Grounded, sunny guidance</p>
            </figcaption>
          </figure>
          <figure class="teacher">
            <div class="avatar a2" aria-hidden="true"></div>
            <figcaption>
              <h3>Milan Gupta</h3>
              <p class="muted">Playful strength</p>
            </figcaption>
          </figure>
          <figure class="teacher">
            <div class="avatar a3" aria-hidden="true"></div>
            <figcaption>
              <h3>Sara Menon</h3>
              <p class="muted">Soft, restorative focus</p>
            </figcaption>
          </figure>
          <figure class="teacher">
            <div class="avatar a4" aria-hidden="true"></div>
            <figcaption>
              <h3>Jonas Pillai</h3>
              <p class="muted">Core + balance</p>
            </figcaption>
          </figure>

          <header class="section__head">
          <p class="muted">click hear for more teachers details <a class="link" href="teachers.php">Teachers</a></p>
        </header>

        </div>
      </div>
    </section>

    <!-- Pricing -->
   
  </main>

  <!-- Footer -->
  <footer class="site-footer">
    <div class="container">
      <p>© 2025 Sunrise Yoga. Made with good vibes.</p>
      <nav class="foot-links" aria-label="Footer">
        <a href="home.php">Home</a>
        <!-- <a href="classes.php">Classes</a> -->
        <a href="program.php">Programs</a>
        <a href="teachers.php">Teachers</a>
        <a href="about_us.php">About Us</a>
      </nav>
    </div>
  </footer>
</body>
</html>