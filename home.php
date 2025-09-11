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
  <?php include 'header.php';?>

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
    <section class="section section--alt" id="free-programs">
    <div class="container">
      <div class="section__head">
        <h2>Free Programs <span><a href="#"><img src="plus.png" width="25px" height="25px" alt=""></a></span></h2>
        <p class="muted">Try these beginner-friendly yoga programs at no cost</p>
      </div>
      <div class="grid grid--programs">
        <div class="program">
          <h3>14 Days Hormone Control</h3>
          <p>Balance your hormones naturally with guided yoga.</p>
        </div>
        <div class="program">
          <h3>15 Days Free Style Yoga</h3>
          <p>Improve flexibility and mindfulness with freestyle yoga.</p>
        </div>
      </div>
    </div>
  </section>


   <section class="section" id="paid-programs">
    <div class="container">
      <div class="section__head">
        <h2>Paid Programs <span><a href="#"><img src="plus.png" width="25px" height="25px" alt=""></a></span></h2>
        <p class="muted">Transform your body and mind with our premium courses</p>
      </div>
      <div class="grid grid--programs">
        <div class="program">
          <h3>30 Days Belly Fat Reduction</h3>
          <p>Intense yoga program designed to reduce belly fat and strengthen your core.</p>
        </div>
        <div class="program">
          <h3>Advanced Flexibility Yoga</h3>
          <p>A premium program to take your flexibility to the next level.</p>
        </div>
      </div>
    </div>
  </section>

    <!-- Programs -->
    <section id="programs" class="section section--alt">
      <div class="container">
        <header class="section__head">
          <h2>Feel-good programs <span><a href="#"><img src="plus.png" width="25px" height="25px" alt=""></a></span></h2>
          <p class="muted">Short plans to build a habit—no pressure, all sunshine. </p>
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

          
        </div>
      </div>
    </section>

    

    <!-- Teachers -->
    <section id="teachers" class="section">
      <div class="container">
        <header class="section__head">
          <h2>Meet the teachers <span><a href="#"><img src="plus.png" width="25px" height="25px" alt=""></a></span></h2>
          <p class="muted">Friendly humans with bright cues and kind energy.</p>
        </header>
        <div class="grid grid--teachers">k
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
  <?php include 'footer.php';?>
  
</body>
</html>