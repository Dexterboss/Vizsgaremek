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

$action = '';
if (isset($_POST['action'])) {
    $action = $_POST['action'];
}

if ($action != 'confirm_order') {
    echo json_encode(array("error" => "Ismeretlen action"));
    exit;
}

$shipping_name = '';
if (isset($_POST['shipping_name'])) {
    $shipping_name = trim($_POST['shipping_name']);
}

$shipping_email = '';
if (isset($_POST['shipping_email'])) {
    $shipping_email = trim($_POST['shipping_email']);
}

$shipping_phone = '';
if (isset($_POST['shipping_phone'])) {
    $shipping_phone = trim($_POST['shipping_phone']);
}

$shipping_address = '';
if (isset($_POST['shipping_address'])) {
    $shipping_address = trim($_POST['shipping_address']);
}

if ($shipping_name == '' || $shipping_email == '' || $shipping_phone == '' || $shipping_address == '') {
    echo json_encode(array("error" => "Minden mezőt ki kell tölteni!"));
    exit;
}

if (!filter_var($shipping_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(array("error" => "Érvénytelen email cím"));
    exit;
}

// Kosár elemek
$query = "SELECT c.id as cart_id, c.quantity, p.id, p.price, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = array();
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

if (empty($cart_items)) {
    echo json_encode(array("error" => "A kosár üres"));
    exit;
}

// Összes ár és stock check
$total = 0;
$can_order = true;
foreach ($cart_items as $item) {
    $total = $total + ($item['price'] * $item['quantity']);
    if ($item['quantity'] > $item['stock']) {
        $can_order = false;
    }
}

if (!$can_order) {
    echo json_encode(array("error" => "Nincs elég készlet"));
    exit;
}

// Rendelés létrehozása
$insert_order = $conn->prepare("INSERT INTO orders (user_id, total_price, shipping_name, shipping_email, shipping_phone, shipping_address) VALUES (?, ?, ?, ?, ?, ?)");
$insert_order->bind_param("iissss", $user_id, $total, $shipping_name, $shipping_email, $shipping_phone, $shipping_address);
$insert_order->execute();
$order_id = $conn->insert_id;

// Order items
foreach ($cart_items as $item) {
    $insert_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $insert_item->bind_param("iiii", $order_id, $item['id'], $item['quantity'], $item['price']);
    $insert_item->execute();
    // Készlet csökkentése
    $update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    $update_stock->bind_param("ii", $item['quantity'], $item['id']);
    $update_stock->execute();
}

// Kosár ürítése
$delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
$delete_cart->bind_param("i", $user_id);
$delete_cart->execute();

// Cart count - most 0
$cart_count = 0;

echo json_encode(array(
    "success" => true,
    "message" => "Rendelés sikeresen leadva",
    "order_id" => $order_id,
    "cartCount" => $cart_count
));
?>
