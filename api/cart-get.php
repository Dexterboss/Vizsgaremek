<?php
header("Content-Type: application/json; charset=UTF-8");
session_start();
require_once '../config/db.php';

// Ellenőrizzük, hogy be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array("error" => "Nincs bejelentkezve"));
    exit;
}

$user_id = $_SESSION['user_id'];

// Kosár elemek lekérése
$query = "SELECT c.id as cart_id, c.quantity, p.id, p.name, p.price, p.image, p.stock
          FROM cart c
          JOIN products p ON c.product_id = p.id
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = array();
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

// Összes ár számítása
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

echo json_encode(array(
    "items" => $cart_items,
    "total" => $total,
    "count" => count($cart_items),
    "username" => $_SESSION['username']
));
?>
