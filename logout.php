<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOCATION: public_html/logout.php
 * LOG: User logout handler and session cleanup
 * PRODUCTION: [To be updated on deployment]
 */

require '../private/gws-universal-config.php';

// Clear remember token from database if user is logged in
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare('UPDATE accounts SET remember_token = NULL, remember_expires = NULL WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
    } catch (PDOException $e) {
        // Log error but continue with logout
        error_log("Logout error clearing remember token: " . $e->getMessage());
    }
}

// Clear all session data
session_start();
session_destroy();

// Clear remember cookie if it exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
}

// Legacy remember cookie cleanup
if (isset($_COOKIE['rememberme'])) {
    setcookie('rememberme', '', time() - 3600, '/', '', false, true);
}

// Determine redirect based on referrer or default to main site
$redirect_url = 'index.php';
$referer = $_SERVER['HTTP_REFERER'] ?? '';

if (strpos($referer, 'shop') !== false || strpos($referer, 'product') !== false) {
    $redirect_url = 'shop.php';
} elseif (strpos($referer, 'blog') !== false || strpos($referer, 'post') !== false) {
    $redirect_url = 'blog.php';
} elseif (strpos($referer, 'client_portal') !== false) {
    $redirect_url = 'auth.php';
}

// Redirect with logout message
header('Location: ' . $redirect_url . '?logout=1');
exit;
?>