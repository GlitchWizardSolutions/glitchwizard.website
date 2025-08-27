<?php
// Load main site config/session

// Robustly locate and include main.php
$main_found = false;
$main_paths = [
    $_SERVER['DOCUMENT_ROOT'] . '/accounts_system/main.php',
    __DIR__ . '/../../accounts_system/main.php',
    __DIR__ . '/../accounts_system/main.php',
    __DIR__ . '/accounts_system/main.php',
    __DIR__ . '/main.php',
];
foreach ($main_paths as $main_path) {
    if (file_exists($main_path)) {
        include_once $main_path;
        $main_found = true;
        break;
    }
}
if (!$main_found) {
    die('Critical error: Could not locate main.php');
}

// Set timezone to EST for consistent time display
date_default_timezone_set('America/New_York');
// Robustly locate and include blog_settings.php
$blog_settings_found = false;
$blog_settings_paths = [
    __DIR__ . '/settings/blog_settings.php',
    __DIR__ . '/../settings/blog_settings.php',
    $_SERVER['DOCUMENT_ROOT'] . '/settings/blog_settings.php',
];
foreach ($blog_settings_paths as $blog_settings_path) {
    if (file_exists($blog_settings_path)) {
        include_once $blog_settings_path;
        $blog_settings_found = true;
        break;
    }
}
if (!$blog_settings_found) {
    echo 'Could not locate blog_settings.php';
}

// Define $rowusers for role checks in functions.php
$rowusers = null;
if (isset($logged_in) && $logged_in && isset($_SESSION['id'])) {
    $stmt_rowusers = $pdo->prepare('SELECT * FROM accounts WHERE id = ? LIMIT 1');
    $stmt_rowusers->execute([$_SESSION['id']]);
    $rowusers = $stmt_rowusers->fetch(PDO::FETCH_ASSOC);
}

// Robustly locate and include blog_system/functions.php
$functions_found = false;
$functions_paths = [
    __DIR__ . '/../../blog_system/functions.php',
    __DIR__ . '/../blog_system/functions.php',
    $_SERVER['DOCUMENT_ROOT'] . '/blog_system/functions.php',
];
foreach ($functions_paths as $functions_path) {
    if (file_exists($functions_path)) {
        // Define safety constant before including functions.php
        define('BLOG_FUNCTIONS_SAFE_TO_OUTPUT', true);
        include_once $functions_path;
        $functions_found = true;
        break;
    }
}
if (!$functions_found) {
    echo 'Could not locate blog_system/functions.php';
}
echo '<style>a { text-decoration: none !important; }</style>';
?>