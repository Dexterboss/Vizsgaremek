<?php
header("Content-Type: application/json; charset=UTF-8");
session_start();
require_once '../config/db.php';

// Ellenőrizzük, hogy be van-e jelentkezve a felhasználó
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array("error" => "Nincs bejelentkezve"));
    exit;
}

$user_id = $_SESSION['user_id'];

// Beolvassuk a paramétereket
$min_price = 0;
if (isset($_GET['minPrice'])) {
    $min_price = (int)$_GET['minPrice'];
}

$max_price = 100000;
if (isset($_GET['maxPrice'])) {
    $max_price = (int)$_GET['maxPrice'];
}

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

$in_stock_only = false;
if (isset($_GET['inStockOnly']) && $_GET['inStockOnly'] == '1') {
    $in_stock_only = true;
}

$selected_brands = array();
if (isset($_GET['selectedBrands'])) {
    $selected_brands = $_GET['selectedBrands'];
}

$selected_categories = array();
if (isset($_GET['selectedCategories'])) {
    $selected_categories = $_GET['selectedCategories'];
}

// Építjük a WHERE feltételt
$where = array();
$params = array();
$types = '';

if ($min_price > 0) {
    $where[] = "p.price >= ?";
    $params[] = $min_price;
    $types .= 'i';
}

if ($max_price < 100000) {
    $where[] = "p.price <= ?";
    $params[] = $max_price;
    $types .= 'i';
}

if ($search) {
    $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if ($in_stock_only) {
    $where[] = "p.stock > 0";
}

if (!empty($selected_brands)) {
    $placeholders = str_repeat('?,', count($selected_brands) - 1) . '?';
    $where[] = "p.brand_id IN ($placeholders)";
    foreach ($selected_brands as $brand) {
        $params[] = (int)$brand;
        $types .= 'i';
    }
}

if (!empty($selected_categories)) {
    $placeholders = str_repeat('?,', count($selected_categories) - 1) . '?';
    $where[] = "p.category_id IN ($placeholders)";
    foreach ($selected_categories as $category) {
        $params[] = (int)$category;
        $types .= 'i';
    }
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Termékek lekérése
$query = "SELECT p.id, p.name, p.description, p.price, p.stock, p.image, p.rating,
                 b.name as brand_name, c.name as category_name
          FROM products p
          LEFT JOIN brands b ON p.brand_id = b.id
          LEFT JOIN categories c ON p.category_id = c.id
          $where_clause
          ORDER BY p.name";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Márkák lekérése
$brandsQuery = "SELECT id, name FROM brands ORDER BY name";
$brands = $conn->query($brandsQuery)->fetch_all(MYSQLI_ASSOC);

// Kategóriák lekérése
$categoriesQuery = "SELECT id, name FROM categories ORDER BY name";
$categories = $conn->query($categoriesQuery)->fetch_all(MYSQLI_ASSOC);

// Kosár számláló
$cartCountQuery = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
$cartStmt = $conn->prepare($cartCountQuery);
$cartStmt->bind_param("i", $user_id);
$cartStmt->execute();
$cartResult = $cartStmt->get_result()->fetch_assoc();
$cartCount = $cartResult['total'] ?? 0;

echo json_encode([
    "products" => $products,
    "brands" => $brands,
    "categories" => $categories,
    "cartCount" => (int)$cartCount
], JSON_PRETTY_PRINT);
?>