<?php
// ===== WEBSHOP oldal - API ALAPÚ =====
session_start();
require_once '../config/db.php';

$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? $_SESSION['username'] : '';

if (!$loggedIn) {
    header('Location: login.php');
    exit;
}

// Szűrő kezdőértékek (az API-k kezelik az adatbázis lekérdezéseket)
$minPrice = isset($_GET['minPrice']) ? (int)$_GET['minPrice'] : 0;
$maxPrice = isset($_GET['maxPrice']) ? (int)$_GET['maxPrice'] : 100000;
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../images/favicon.png" type="image/x-icon">
    <title>Molnár Barber Shop - Webshop</title>
    <link rel="stylesheet" href="../css/webshopstyle.css">
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
                <a href="webshop.php" class="active"><i class='bx bx-cart'></i><span>Webshop</span></a>
                <a href="index.php#team"><i class='bx bx-group'></i><span>Csapat</span></a>
                <a href="index.php#gallery"><i class='bx bx-image'></i><span>Galéria</span></a>
                <a href="index.php#reviews"><i class='bx bx-star'></i><span>Vélemények</span></a>
                <a href="index.php#faq"><i class='bx bx-help-circle'></i><span>GYIK</span></a>
                <a href="index.php#contact"><i class='bx bx-phone'></i><span>Kapcsolat</span></a>

                <div class="nav-footer">
                    <a href="cart.php" class="cta-btn"><i class='bx bx-cart'></i><span>Kosár</span></a>
                    <a href="logout.php" class="logout-btn"><i class='bx bx-log-out'></i><span>Kijelentkezés</span></a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="webshop-container">
            <!-- Szűrők (bal oldali oszlop) -->
            <aside class="filters">
                <h3>Szűrők</h3>
                <form method="GET" action="webshop.php" id="filterForm">
                    <!-- Keresés -->
                    <div class="filter-group">
                        <label for="search">Kulcsszó:</label>
                        <input type="text" id="search" name="search" value="<?php echo $search; ?>" placeholder="Kulcsszó (név vagy leírás)...">
                    </div>

                    <!-- Ár szűrő -->
                    <div class="filter-group">
                        <label>Ár tartomány:</label>
                        <div class="price-range">
                            <div class="price-inputs">
                                <input type="number" id="minInput" value="<?php echo $minPrice; ?>" min="0" max="100000" step="100" placeholder="Min Ft">
                                <input type="number" id="maxInput" value="<?php echo $maxPrice; ?>" min="0" max="100000" step="100" placeholder="Max Ft">
                            </div>
                            <input type="range" id="minPrice" name="minPrice" value="<?php echo $minPrice; ?>" min="0" max="100000" step="100">
                            <input type="range" id="maxPrice" name="maxPrice" value="<?php echo $maxPrice; ?>" min="0" max="100000" step="100">
                            <div class="price-display">
                                <span><?php echo number_format($minPrice); ?> Ft</span> - <span><?php echo number_format($maxPrice); ?> Ft</span>
                            </div>
                        </div>
                    </div>

                    <!-- Márkák (JS betölti az API-ból) -->
                    <div class="filter-group">
                        <label>Márkák:</label>
                        <div class="checkbox-group" id="brands-container">
                            <p>Töltés...</p>
                        </div>
                    </div>

                    <!-- Kategóriák (JS betölti az API-ból) -->
                    <div class="filter-group">
                        <label>Kategóriák:</label>
                        <div class="checkbox-group" id="categories-container">
                            <p>Töltés...</p>
                        </div>
                    </div>

                    <!-- Készlet -->
                    <div class="filter-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="inStockOnly" value="1">
                            Csak raktáron lévő termékek
                        </label>
                    </div>

                    <!-- Gombok -->
                    <div class="filter-buttons">
                        <button type="submit" class="filter-btn">Szűrés</button>
                        <button type="button" id="clearFiltersBtn" class="clear-btn">Összes törlése</button>
                    </div>
                </form>
            </aside>

            <!-- Termékek (jobb oldali oszlop) -->
            <section class="products">
                <!-- Banner -->
                <div class="webshop-banner">
                    <h1>Molnár Barber Shop - Webshop</h1>
                    <p>Fedezd fel prémium hajápolási termékeinket és kozmetikumainkat!</p>
                    <div class="banner-features">
                        <span><i class='bx bx-check'></i> Minőségi termékek</span>
                        <span><i class='bx bx-check'></i> Gyors szállítás</span>
                        <span><i class='bx bx-check'></i> Garantált elégedettség</span>
                    </div>
                </div>

                <!-- Termékek rácsa (JS tölti az API-ból) -->
                <div class="products-grid">
                    <p>Termékek betöltése...</p>
                </div>
            </section>
        </div>
    </main>

    <!-- Lebegő kosár gomb -->
    <a href="cart.php" class="floating-cart-btn" aria-label="Kosár">
        <i class='bx bx-cart'></i>
        <span class="cart-count">0</span>
    </a>

    <!-- JavaScript -->
    <script src="../js/webshop.js"></script>
</body>
</html>
