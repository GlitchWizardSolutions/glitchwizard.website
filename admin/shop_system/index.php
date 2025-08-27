<?php
define('shoppingcart_admin', true);
// Load admin authentication and database connection
include '../assets/includes/main.php';
// Include shop functions from the main shop_system directory
include '../../shop_system/functions.php';

// Output error variable
$error = '';
// Icons for the table headers (matching admin pattern)
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>', // ▲
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>' // ▼
];

// Get shop system totals
try {
    $orders_total = $pdo->query('SELECT COUNT(*) AS total FROM shop_transactions')->fetchColumn();
} catch (Exception $e) {
    $orders_total = 0;
}

try {
    $products_total = $pdo->query('SELECT COUNT(*) AS total FROM shop_products')->fetchColumn();
} catch (Exception $e) {
    $products_total = 0;
}

try {
    $categories_total = $pdo->query('SELECT COUNT(*) AS total FROM shop_categories')->fetchColumn();
} catch (Exception $e) {
    $categories_total = 0;
}

// Page is set to dashboard by default
$page = isset($_GET['page']) && file_exists($_GET['page'] . '.php') ? $_GET['page'] : 'dashboard';
// Include the requested page
include $page . '.php';
?>