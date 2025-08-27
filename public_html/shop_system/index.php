<?php
define('shoppingcart', true);
// Load main site authentication and database connection
include_once 'shop_load.php';
// Include the shop-specific functions
include 'functions.php';
// Output error variable
$error = '';
// Define all the routes for all pages
$url = routes([
    '/' => 'index.php?page=home',
    '/home' => 'index.php?page=home',
    '/product/{id}' => 'index.php?page=product&id={id}',
    '/products' => 'index.php?page=products',
    '/myaccount' => 'index.php?page=myaccount',
    '/myaccount/{tab}' => 'index.php?page=myaccount&tab={tab}',
    '/forgotpassword' => 'index.php?page=forgotpassword',
    '/download/{id}' => 'index.php?page=download&id={id}',
    '/cart' => 'index.php?page=cart',
    '/checkout' => 'index.php?page=checkout',
    '/subscribe/{method}' => 'index.php?page=subscribe&method={method}',
    '/placeorder' => 'index.php?page=placeorder',
    '/search/{query}' => 'index.php?page=search&query={query}',
    '/logout' => 'index.php?page=logout'
]);
// Check if route exists
if ($url) {
    include $url;
} else {
    // Page is set to home (home.php) by default, so when the visitor visits that will be the page they see.
    $page = isset($_GET['page']) && file_exists($_GET['page'] . '.php') ? $_GET['page'] : 'home';
    // Include the requested page
    include $page . '.php';
}
?>