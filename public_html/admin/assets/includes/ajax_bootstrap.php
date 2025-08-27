<?php
// Lightweight include for pure JSON/ajax endpoints (no spinner/HTML wrappers)
// Loads config, DB, auth, and minimal functions without emitting layout or overlay markup.
if (!defined('PROJECT_ROOT')) {
    $root_guess = __DIR__ . '/../../../../private/gws-universal-config.php';
    if (file_exists($root_guess)) {
        include_once $root_guess; // defines PROJECT_ROOT
    }
}
if (defined('PROJECT_ROOT')) {
    $primary_config = PROJECT_ROOT . '/private/gws-universal-config.php';
    if (file_exists($primary_config)) {
        include_once $primary_config;
    } else {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['ok'=>false,'error'=>'Config missing']);
        exit;
    }
} else {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'Root unresolved']);
    exit;
}
include_once PROJECT_ROOT . '/private/gws-universal-functions.php';
// Suppress spinner/overlay emission for pure JSON endpoints
if(!defined('SUPPRESS_BRAND_SPINNER')) define('SUPPRESS_BRAND_SPINNER', true);
require_once __DIR__ . '/../../protection.php'; // runs access check without emitting overlay when suppressed
if(defined('BRAND_SPINNER_OVERLAY_EMITTED')) {
    // Prevent overlay output during ajax by unsetting the flag before protection tries to echo
    // (If protection already emitted, we cannot retract; better to adjust protection file later.)
}
try {
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'DB connect fail','detail'=>$exception->getMessage()]);
    exit;
}
if(session_status()!==PHP_SESSION_ACTIVE){session_start();}
check_loggedin_full($pdo, '../auth.php?tab=login');
// Basic role check
$stmt = $pdo->prepare('SELECT role FROM accounts WHERE id = ?');
$stmt->execute([$_SESSION['id']]);
$role = $stmt->fetchColumn();
if(!$role || !in_array($role,['Admin','Editor','Developer'])){
    http_response_code(403);
    echo json_encode(['ok'=>false,'error'=>'Forbidden']);
    exit;
}
?>
