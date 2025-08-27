<?php
header('Content-Type: application/json');
require_once __DIR__ . '/assets/includes/ajax_bootstrap.php';
if(session_status()!==PHP_SESSION_ACTIVE){session_start();}
if($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit; }
$title = trim($_POST['title'] ?? '');
$paragraph = trim($_POST['paragraph'] ?? '');
try {
    $stmt = $pdo->prepare("INSERT INTO content_items (area, slug, title, body, position, active, created_at, updated_at) VALUES ('section','services',:t,:b,0,1,NOW(),NOW()) ON DUPLICATE KEY UPDATE title=VALUES(title), body=VALUES(body), updated_at=NOW()");
    $stmt->execute([':t'=>$title, ':b'=>$paragraph]);
    echo json_encode(['ok'=>true,'updated_at'=>date('c')]);
} catch(Exception $e){ http_response_code(500); echo json_encode(['ok'=>false,'error'=>'DB error','detail'=>$e->getMessage()]); }
