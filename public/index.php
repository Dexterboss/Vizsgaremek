<?php
session_start();
require_once '../config/db.php';

$loggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$username = $loggedIn ? $_SESSION['username'] : '';
?>

<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Molnár Barber Shop - Professzionális Borbély Szolgáltatások</title>
  <link rel="icon" href="../images/favicon.png" type="image/x-icon">
  <meta name="description" content="Molnár Barber Shop - Modern és klasszikus hajvágás, szakáll ápolás, arckezelés Budapest szívében. Foglalj időpontot online!">

  <!-- CSS link -->
  <link rel="stylesheet" href="../css/indexstyle.css">
  <!-- BOXICONS Library link (összes icont ez kezeli) -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <!-- AOS link (Animate On Scroll Library) -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <!-- GLightBox Library link (Galéria) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/css/glightbox.min.css">

</head>

<body>
  <div class="progress"></div>

  <header>
    <nav>
      <div class="logo-div">
        <a href="#home" class="logo">
          <img src="../images/landing-page-logo.png" alt="Molnár Barber Logo" loading="lazy">
          <span>Molnár Barber <br>Shop</span>
        </a>
      </div>

      <button class="menu-btn">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <div class="menu">
        <a href="#home" class="active"><i class='bx bx-home'></i><span>Főoldal</span></a>
        <a href="#about"><i class='bx bx-info-circle'></i><span>Rólunk</span></a>
        <a href="#services"><i class='bx bx-cut'></i><span>Szolgáltatások</span></a>
        <a href="webshop.php"><i class='bx bx-cart'></i><span>Webshop</span></a>
        <a href="#team"><i class='bx bx-group'></i><span>Csapat</span></a>
        <a href="#gallery"><i class='bx bx-image'></i><span>Galéria</span></a>
        <a href="#reviews"><i class='bx bx-star'></i><span>Vélemények</span></a>
        <a href="#faq"><i class='bx bx-help-circle'></i><span>GYIK</span></a>
        <a href="#contact"><i class='bx bx-phone'></i><span>Kapcsolat</span></a>

        <div class="nav-footer">
          <?php if ($loggedIn && $isAdmin): ?>
          <a href="../admin/admin.php" class="admin-btn"><i class='bx bx-shield'></i><span>Admin Felület</span></a>
          <a href="appointments.php" class="cta-btn"><i class='bx bx-calendar'></i><span>Időpontfoglalás</span></a>
          <a href="logout.php" class="logout-btn"><i class='bx bx-log-out'></i><span>Kijelentkezés</span></a>
          <?php elseif ($loggedIn): ?>
          <a href="appointments.php" class="cta-btn"><i class='bx bx-calendar'></i><span>Időpontfoglalás</span></a>
          <a href="logout.php" class="logout-btn"><i class='bx bx-log-out'></i><span>Kijelentkezés</span></a>
          <?php else: ?>
          <a href="login.php" class="login-btn"><i class='bx bx-log-in'></i><span>Bejelentkezés</span></a>
          <?php endif; ?>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <section id="home">
      <div class="hero-overlay"></div>
      <div class="hero-content" data-aos="fade-up">
        <h1>Ahol a stílus születik - <br><span>Molnár Barber Shop</span></h1>
        <p>Fedezd fel, hogyan találkozik a klasszikus borbély mesterség a modern trendekkel. Minden hajvágás és
          szakállápolás nálunk prémium szintű törődést és feltöltődést jelent.</p>
        <a href="appointments.php" class="btn-primary">Foglalj időpontot most<i class='bx bx-right-arrow-alt'></i></a>
      </div>
      <div class="scroll-down">
        <i class='bx bx-chevron-down'></i>
      </div>
    </section>

    <section id="about">
      <div class="container">
        <div class="about-content">
          <img src="../images/about-img.png" alt="Molnár Barber Shop Interior" data-aos="fade-right" loading="lazy">
          <div class="text" data-aos="fade-left">
            <h2 data-aos="fade-up" class="black">Rólunk</h2>
            <p>Nálunk a vendég az első. Modern technikák és klasszikus hagyományok ötvözése, prémium élmény és maximális
              higiénia.</p>
            <p>A Molnár Barber Shopban mindenki megtalálja a stílusához illő szolgáltatást, miközben egy családias
              közösség tagjává válik.</p>
            <p><strong>2015 óta</strong> szolgáljuk ki ügyfeleinket Budapest szívében, több mint <strong>10.000
                elégedett vendéggel</strong>.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="services">
      <div class="container">
        <h2 data-aos="fade-up" class="white">Szolgáltatásaink</h2>
        <div class="services-grid">
          <article data-aos="fade-up" data-aos-delay="100">
            <i class='bx bx-cut'></i>
            <h3>Hajvágás</h3>
            <p>Modern és klasszikus fazonok minden korosztálynak</p>
            <button class="btn-details" data-service="haircut">Részletek</button>
          </article>
          <article data-aos="fade-up" data-aos-delay="200">
            <i class='bx bxs-user-badge'></i>
            <h3>Szakáll Ápolás</h3>
            <p>Szakáll, arcápolás, borotválás klasszikus stílusban</p>
            <button class="btn-details" data-service="beard">Részletek</button>
          </article>
          <article data-aos="fade-up" data-aos-delay="300">
            <i class='bx bx-spa'></i>
            <h3>Arckezelés</h3>
            <p>Frissítő arckezelések, masszázsok</p>
            <button class="btn-details" data-service="facial">Részletek</button>
          </article>
          <article data-aos="fade-up" data-aos-delay="400">
            <i class='bx bx-brush'></i>
            <h3>Hajstyling</h3>
            <p>Professzionális styling, mindennapi hajápolás</p>
            <button class="btn-details" data-service="styling">Részletek</button>
          </article>
          <article data-aos="fade-up" data-aos-delay="500">
            <i class='bx bx-palette'></i>
            <h3>Festés & Melír</h3>
            <p>Prémium minőségű festés, szőkítés, melír</p>
            <button class="btn-details" data-service="coloring">Részletek</button>
          </article>
        </div>
      </div>
    </section>

    <section id="team">
      <div class="container">
        <h2 class="black">Csapatunk</h2>
        <div class="team-carousel">
          <button class="carousel-arrow left"><i class='bx bx-left-arrow-alt'></i></button>
          <div class="carousel-track"></div>
          <button class="carousel-arrow right"><i class='bx bx-right-arrow-alt'></i></button>
        </div>
      </div>
    </section>

    <section id="gallery">
      <div class="container">
        <h2 data-aos="fade-up" class="white">Galéria</h2>
        <div class="gallery-scroll">
          <a href="../images/haircuts/haircut1.jpg" class="glightbox" data-aos="fade-left" data-aos-delay="0"><img
              src="../images/haircuts/haircut1.jpg" alt="Munkák 1" loading="lazy"><i class='bx bx-search-alt'></i></a>
          <a href="../images/haircuts/haircut2.jpg" class="glightbox" data-aos="fade-left" data-aos-delay="200"><img
              src="../images/haircuts/haircut2.jpg" alt="Munkák 2" loading="lazy"><i class='bx bx-search-alt'></i></a>
          <a href="../images/haircuts/haircut3.jpg" class="glightbox" data-aos="fade-left" data-aos-delay="400"><img
              src="../images/haircuts/haircut3.jpg" alt="Munkák 3" loading="lazy"><i class='bx bx-search-alt'></i></a>
          <a href="../images/haircuts/haircut4.jpg" class="glightbox" data-aos="fade-left" data-aos-delay="600"><img
              src="../images/haircuts/haircut4.jpg" alt="Munkák 4" loading="lazy"><i class='bx bx-search-alt'></i></a>
          <a href="../images/haircuts/haircut5.jpg" class="glightbox" data-aos="fade-left" data-aos-delay="800"><img
              src="../images/haircuts/haircut5.jpg" alt="Munkák 5" loading="lazy"><i class='bx bx-search-alt'></i></a>
          <a href="../images/haircuts/haircut6.jpg" class="glightbox" data-aos="fade-left" data-aos-delay="1000"><img
              src="../images/haircuts/haircut6.jpg" alt="Munkák 6" loading="lazy"><i class='bx bx-search-alt'></i></a>
          <a href="../images/haircuts/haircut7.jpg" class="glightbox" data-aos="fade-left" data-aos-delay="1200"><img
              src="../images/haircuts/haircut7.jpg" alt="Munkák 7" loading="lazy"><i class='bx bx-search-alt'></i></a>
          <a href="../images/haircuts/haircut8.jpg" class="glightbox" data-aos="fade-left" data-aos-delay="1400"><img
              src="../images/haircuts/haircut8.jpg" alt="Munkák 8" loading="lazy"><i class='bx bx-search-alt'></i></a>
          <a href="../images/haircuts/haircut9.jpg" class="glightbox" data-aos="fade-left" data-aos-delay="1600"><img
              src="../images/haircuts/haircut9.jpg" alt="Munkák 9" loading="lazy"><i class='bx bx-search-alt'></i></a>
          <a href="../images/haircuts/haircut10.jpg" class="glightbox" data-aos="fade-left" data-aos-delay="1800"><img
              src="../images/haircuts/haircut10.jpg" alt="Munkák 10" loading="lazy"><i class='bx bx-search-alt'></i></a>
        </div>
      </div>
    </section>

    <section id="reviews">
      <div class="container">
        <h2 data-aos="fade-up" class="black">Vélemények</h2>
        <div class="reviews-grid">
          <article data-aos="fade-up" data-aos-delay="100">
            <div class="stars">★★★★★</div>
            <p>"A Molnár Barber Shop a legjobb hely a városban! Professzionális és barátságos csapat."</p>
            <strong>- Kovács Péter</strong>
          </article>
          <article data-aos="fade-up" data-aos-delay="200">
            <div class="stars">★★★★★</div>
            <p>"Mindig elégedetten távozom, a stílusom igazi műalkotás itt."</p>
            <strong>- Nagy János</strong>
          </article>
          <article data-aos="fade-up" data-aos-delay="300">
            <div class="stars">★★★★★</div>
            <p>"Kedves, segítőkész kiszolgálás és gyors időpontfoglalás."</p>
            <strong>- Tóth Gábor</strong>
          </article>
        </div>
      </div>
    </section>

    <section id="faq">
      <div class="faq-bg"></div>
      <div class="container">
        <h2 class="white" data-aos="fade-up">Gyakran Ismételt Kérdések</h2>
        <div class="faq-list" data-aos="fade-up">
          <article class="faq-card">
            <button class="faq-btn">
              <span>Hogyan tudok időpontot foglalni?</span>
              <i class="bx bx-chevron-down"></i>
            </button>
            <div class="faq-answer">
              <p>
                <i class="bx bx-calendar-check"></i> Lépj az Időpontfoglalás menüpontra, válassz dátumot, időpontot és
                fodrászt, majd erősítsd meg a
                foglalásodat egy kattintással.
              </p>
              <div class="faq-important">
                <span class="important-label"><i class="bx bx-error-circle"></i> Fontos!</span>
                Felhasználói fiók kell hozzá.
              </div>
              <div class="faq-action">
                <a class="action-btn" href="appointments.php">
                  <i class="bx bx-calendar"></i> Kattints ide!
                </a>
              </div>
            </div>
          </article>

          <article class="faq-card">
            <button class="faq-btn">
              <span>Kötelező előre foglalni?</span>
              <i class="bx bx-chevron-down"></i>
            </button>
            <div class="faq-answer">
              <p>
                <i class="bx bx-alarm-exclamation"></i> Érdemes előre foglalni, de ha van szabad borbély,
                bejelentkezés nélkül is fogadunk!
              </p>
            </div>
          </article>

          <article class="faq-card">
            <button class="faq-btn">
              <span>Milyen fizetési módokat fogadtok el?</span>
              <i class="bx bx-chevron-down"></i>
            </button>
            <div class="faq-answer">
              <p>
                <i class="bx bx-credit-card"></i> Fizethetsz <b>készpénzzel</b> és <b>bankkártyával</b>. Webshopban
                kizárólag a <b>kártyás fizetés</b>
                működik.
              </p>
            </div>
          </article>

          <article class="faq-card">
            <button class="faq-btn">
              <span>Van parkolási lehetőség?</span>
              <i class="bx bx-chevron-down"></i>
            </button>
            <div class="faq-answer">
              <p>
                <i class='bx  bx-car'></i> Ingyenes utcai parkolás közvetlenül az üzlet előtt, illetve 2 perc
                sétára <b>fizetős parkolóház</b> is
                elérhető.
              </p>
            </div>
          </article>
        </div>
      </div>
    </section>

    <section id="contact">
      <div class="container">
        <h2 data-aos="fade-up" class="black">Kapcsolat</h2>
        <div class="contact-grid">
          <div class="contact-info" data-aos="fade-right">
            <article>
              <i class='bx bx-map'></i>
              <div>
                <h3>Cím</h3>
                <p><a href="https://www.google.com/maps" target="_blank">Budapest, Példa utca 123.</a></p>
              </div>
            </article>
            <article>
              <i class='bx bx-phone'></i>
              <div>
                <h3>Telefon</h3>
                <p><a href="tel:+36301234567" target="_blank">+36 30 123 4567</a></p>
              </div>
            </article>
            <article>
              <i class='bx bx-envelope'></i>
              <div>
                <h3>Email</h3>
                <p><a href="mailto:info@molnarbarbershop.hu" target="_blank">info@molnarbarbershop.hu</a></p>
              </div>
            </article>
          </div>
          <div class="hours" data-aos="fade-left">
            <h3>Nyitvatartás</h3>
            <ul>
              <li>Hétfő - Vasárnap: 9:00 - 18:00</li>
              <li>Ünnepnapokon: eltérhet</li>
            </ul>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 Molnár Barber Shop. Minden jog fenntartva.</p>
    <div class="social">
      <a href="https://www.facebook.com/"><i class='bx bxl-facebook'></i></a>
      <a href="https://www.instagram.com/molnar_barbershop/#" target="_blank"><i class='bx bxl-instagram'></i></a>
      <a href="https://www.tiktok.com/explore"><i class='bx bxl-tiktok'></i></a>
    </div>
  </footer>

  <div class="modal">
    <div class="modal-content">
      <button class="modal-close">&times;</button>
      <div class="modal-body"></div>
    </div>
  </div>

  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
  <script src="../js/script.js"></script>
</body>

</html>