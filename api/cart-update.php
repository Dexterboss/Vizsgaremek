<?php
header("Content-Type: application/json; charset=UTF-8");
session_start();
require_once '../config/db.php';

// Bejelentkezés ellenőrzése
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Nincs bejelentkezve"]);
    exit;
}

// POST paraméterek
$cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($cart_id > 0 && in_array($action, ['increase', 'decrease'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT quantity, product_id FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $quantity = (int)$row['quantity'];
        $product_id = $row['product_id'];

        if ($action === 'increase') {
            $quantity++;
        } elseif ($action === 'decrease') {
            $quantity = max(1, $quantity - 1);
        }

        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
        $stmt->execute();
        
        echo json_encode(["success" => true, "message" => "Mennyiség frissítve", "quantity" => $quantity]);
    } else {
        echo json_encode(["error" => "Kosár elem nem található"]);
    }
} else {
    echo json_encode(["error" => "Hiányzó vagy érvénytelen paraméterek"]);
}
?>
