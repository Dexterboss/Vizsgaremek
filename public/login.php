<?php
session_start();
require_once '../config/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] == 'admin') {
            header("Location: index.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $message = "Hibás email vagy jelszó.";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Molnár Barber Shop | Bejelentkezés</title>
    <link rel="stylesheet" href="../css/logregstyle.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="icon" href="../images/favicon.png" type="image/x-icon">
</head>
<body>
    <a href= "index.php" class="logo">Molnár Barber <br>Shop</a>
    <div class="form-wrapper" role="main" aria-label="Bejelentkezési felület">
        <form method="POST" action="" autocomplete="on" class="unified-form">
            <h1 class="form-title">Bejelentkezés</h1>
            <div class="input-box">
                <label for="email" class="sr-only">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" required autocomplete="username"
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" />
                <i class='bx bx-envelope'></i>
            </div>
            <div class="input-box">
                <label for="password" class="sr-only">Jelszó</label>
                <input type="password" id="password" name="password" placeholder="Jelszó" required autocomplete="current-password" />
                <i class='bx bx-lock'></i>
            </div>
            <div class="remember-row">
                <label class="switch">
                    <input type="checkbox" name="remember" id="remember-me" />
                    <span class="toggle"></span>
                    Jegyezz meg.
                </label>
                <a href="#">Elfelejtetted a jelszavad?</a>
            </div>
            <button type="submit" class="form-btn">Bejelentkezés</button>
            <?php if ($message != ''): ?>
                <p class="error-msg" role="alert"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
            <div class="form-link">
                <p>Nincsen még fiókod? <a href="register.php">Regisztráció</a></p>
                <p>Folytatod fiók nélkül? <a href="index.php">Főoldal</a></p>
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
