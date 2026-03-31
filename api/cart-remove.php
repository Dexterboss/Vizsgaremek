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

$cart_id = 0;
if (isset($_POST['cart_id'])) {
    $cart_id = (int)$_POST['cart_id'];
}

if ($cart_id <= 0) {
    echo json_encode(array("error" => "Hiányzó vagy érvénytelen cart_id"));
    exit;
}

$delete_stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
$delete_stmt->bind_param("ii", $cart_id, $user_id);
$delete_stmt->execute();

// Cart count
$cart_count_query = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
$cart_count_stmt = $conn->prepare($cart_count_query);
$cart_count_stmt->bind_param("i", $user_id);
$cart_count_stmt->execute();
$cart_count_result = $cart_count_stmt->get_result();
$cart_count_row = $cart_count_result->fetch_assoc();
$cart_count = 0;
if ($cart_count_row['total'] != null) {
    $cart_count = $cart_count_row['total'];
}

echo json_encode(array(
    "success" => true,
    "message" => "Eltávolítva a kosárból",
    "cartCount" => $cart_count
));
?>
