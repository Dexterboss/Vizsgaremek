<?php
session_start();
require_once '../config/db.php';

define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_REQUIRE_NUM', true);
$msg = '';

function validate_password($password) {
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return false;
    }
    
    if (PASSWORD_REQUIRE_NUM && !preg_match('/[0-9]/', $password)) {
        return false;
    }
    
    return true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    
    if (empty($username) || empty($email) || empty($password) || empty($password2)) {
        $msg = 'Minden mező kitöltése kötelező.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = 'Érvénytelen email cím.';
    } elseif ($password !== $password2) {
        $msg = 'A jelszavak nem egyeznek.';
    } elseif (!validate_password($password)) {
        $msg = 'Jelszónak legalább ' . PASSWORD_MIN_LENGTH . ' karakterből kell állnia, és tartalmaznia kell számot!';
    } else {
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $msg = 'Ez az email már foglalt.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';
            
            $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $email, $hash, $role);
            
            if ($stmt->execute()) {
                header("Location: login.php?register=success");
                exit;
            } else {
                $msg = 'Ismeretlen hiba történt. Próbáld újra!';
            }
        }
        
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Molnár Barber Shop | Regisztráció</title>
    <link rel="stylesheet" href="../css/logregstyle.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="icon" href="../images/favicon.png" type="image/x-icon">
</head>
<body>
    <a href= "index.php" class="logo">Molnár Barber <br>Shop</a>
    <div class="form-wrapper" role="main" aria-label="Regisztrációs felület">
        <form method="POST" action="" autocomplete="on" class="unified-form">
            <h1 class="form-title">Regisztráció</h1>

            <div class="input-box">
                <label for="username" class="sr-only">Felhasználónév</label>
                <input type="text" id="username" name="username" placeholder="Felhasználónév" required autocomplete="username"
                    value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" />
                <i class='bx bx-user'></i>
            </div>

            <div class="input-box">
                <label for="email" class="sr-only">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" required autocomplete="email"
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" />
                <i class='bx bx-envelope'></i>
            </div>

            <div class="input-box">
                <label for="password" class="sr-only">Jelszó</label>
                <input type="password" id="password" name="password" placeholder="Jelszó" required autocomplete="new-password" />
                <i class='bx bx-lock'></i>
            </div>

            <div class="input-box">
                <label for="password2" class="sr-only">Jelszó megerősítése</label>
                <input type="password" id="password2" name="password2" placeholder="Jelszó megerősítése" required autocomplete="new-password" />
                <i class='bx bx-lock-alt'></i>
            </div>

            <button type="submit" class="form-btn">Regisztráció</button>

            <?php if (!empty($msg)): ?>
                <p class="error-msg" role="alert"><?php echo htmlspecialchars($msg); ?></p>
            <?php endif; ?>

            <div class="form-link">
                <p>Van már fiókod? <a href="login.php">Bejelentkezés</a></p>
            </div>
        </form>
    </div>
    <footer class="auth-footer">
        <a href="adatkezeles.php">Adatkezelési tájékoztató</a>
        <span>|</span>
        <a href="aszf.php">Általános Szerződési Feltételek</a>
        <span>|</span>
        <a href="index.php#contact">Kapcsolat</a>
    </footer>
</body>
</html>