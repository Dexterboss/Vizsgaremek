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

$product_id = 0;
if (isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
}

if ($product_id <= 0) {
    echo json_encode(array("error" => "Hiányzó vagy érvénytelen product_id"));
    exit;
}

// Stock ellenőrzés
$stock_query = "SELECT stock FROM products WHERE id = ?";
$stock_stmt = $conn->prepare($stock_query);
$stock_stmt->bind_param("i", $product_id);
$stock_stmt->execute();
$stock_result = $stock_stmt->get_result();
if ($stock_result->num_rows == 0) {
    echo json_encode(array("error" => "Termék nem található"));
    exit;
}
$stock_row = $stock_result->fetch_assoc();
$current_stock = $stock_row['stock'];

// Ellenőrizzük a kosárban lévő mennyiséget
$cart_query = "SELECT id, quantity FROM cart WHERE product_id = ? AND user_id = ?";
$cart_stmt = $conn->prepare($cart_query);
$cart_stmt->bind_param("ii", $product_id, $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();

if ($cart_result->num_rows > 0) {
    $cart_row = $cart_result->fetch_assoc();
    $new_quantity = $cart_row['quantity'] + 1;
    if ($new_quantity > $current_stock) {
        echo json_encode(array("error" => "Nincs elég készlet"));
        exit;
    }
    // Update
    $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $update_stmt->bind_param("ii", $new_quantity, $cart_row['id']);
    $update_stmt->execute();
    $message = "Mennyiség frissítve";
    $quantity = $new_quantity;
} else {
    if (1 > $current_stock) {
        echo json_encode(array("error" => "Nincs készlet"));
        exit;
    }
    // Insert
    $insert_stmt = $conn->prepare("INSERT INTO cart (product_id, quantity, user_id) VALUES (?, 1, ?)");
    $insert_stmt->bind_param("ii", $product_id, $user_id);
    $insert_stmt->execute();
    $message = "Hozzáadva a kosárhoz";
    $quantity = 1;
}

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
    "message" => $message,
    "quantity" => $quantity,
    "cartCount" => $cart_count
));
?>
