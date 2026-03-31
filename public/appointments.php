<?php
// ===== IDŐPONTFOGLALÁS oldal =====
session_start();
require_once '../config/db.php';

$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? $_SESSION['username'] : '';

if (!$loggedIn) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/favicon.png" type="image/x-icon">
    <title>Molnár Barber Shop - Időpontfoglalás</title>
    <link rel="stylesheet" href="../css/appointmentsstyle.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <header>
        <nav>
            <div class="logo-div">
                <a href="index.php" class="logo">
                    <img src="../images/landing-page-logo.png" alt="Molnár Barber Logo">
                    <span>Molnár Barber <br>Shop</span>
                </a>
            </div>

            <div class="menu">
                <a href="index.php"><i class='bx bx-home'></i><span>Főoldal</span></a>
                <a href="index.php#about"><i class='bx bx-info-circle'></i><span>Rólunk</span></a>
                <a href="index.php#services"><i class='bx bx-cut'></i><span>Szolgáltatások</span></a>
                <a href="webshop.php"><i class='bx bx-cart'></i><span>Webshop</span></a>
                <a href="appointments.php" class="active"><i class='bx bx-calendar'></i><span>Időpontfoglalás</span></a>
                <a href="index.php#team"><i class='bx bx-group'></i><span>Csapat</span></a>
                <a href="index.php#gallery"><i class='bx bx-image'></i><span>Galéria</span></a>
                <a href="index.php#reviews"><i class='bx bx-star'></i><span>Vélemények</span></a>
                <a href="index.php#faq"><i class='bx bx-help-circle'></i><span>GYIK</span></a>
                <a href="index.php#contact"><i class='bx bx-phone'></i><span>Kapcsolat</span></a>

                <div class="nav-footer">
                    <?php if ($loggedIn): ?>
                        <a href="logout.php" class="logout-btn"><i class='bx bx-log-out'></i><span>Kijelentkezés</span></a>
                    <?php else: ?>
                        <a href="login.php" class="login-btn"><i class='bx bx-log-in'></i><span>Bejelentkezés</span></a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="appointments-container">
            <h1><i class='bx bx-calendar-check'></i> Időpontfoglalás</h1>

            <div class="booking-content">
                <div class="date-selection">
                    <h2>Válassz dátumot</h2>
                    <input type="date" id="appointment-date" class="date-picker">
                </div>

                <div class="hairdressers-section">
                    <h2>Válassz fodrászt és időpontot</h2>
                    <div id="hairdressers-list">
                    </div>
                </div>

                <div class="services-section hidden" id="services-section">
                    <h2>Válassz szolgáltatást</h2>
                    <div id="services-list">
                    </div>
                    <div class="booking-actions">
                        <button class="btn-back" id="btn-back">Vissza</button>
                        <button class="btn-confirm" id="btn-confirm">Foglalás megerősítése</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/appointments.js"></script>
</body>
</html>
