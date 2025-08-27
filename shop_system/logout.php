<?php
// Shop system logout - redirect to main site logout
defined('shoppingcart') or exit;

// Clear any shop-specific session data
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

// Redirect to main site logout
header('Location: ../logout.php');
exit;
?>
