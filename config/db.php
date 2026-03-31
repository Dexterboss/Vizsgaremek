<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "idopontfoglalas";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Adatbázis kapcsolat hiba: " . $conn->connect_error);
}

// Session timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 1800) {
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();

?>
