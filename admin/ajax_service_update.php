<?php
// AJAX endpoint to update a single service content_item
// Expects POST: id, title, icon, body
header('Content-Type: application/json');
require_once __DIR__ . '/assets/includes/ajax_bootstrap.php';
if(session_status()!==PHP_SESSION_ACTIVE){session_start();}

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok'=>false,'error'=>'Method not allowed']);
    exit;
}

$id = isset($_POST['id'])? (int)$_POST['id'] : 0;
if($id <= 0){
    http_response_code(400);
    echo json_encode(['ok'=>false,'error'=>'Invalid id']);
    exit;
}

$title = trim($_POST['title'] ?? '');
$icon  = trim($_POST['icon'] ?? '');
$body  = trim($_POST['body'] ?? '');

try {
    $stmt = $pdo->prepare("UPDATE content_items SET title=:t, icon=:i, body=:b, updated_at=NOW() WHERE id=:id AND area='service'");
    $stmt->execute([':t'=>$title, ':i'=>$icon, ':b'=>$body, ':id'=>$id]);
    echo json_encode(['ok'=>true,'id'=>$id,'updated_at'=>date('c')]);
} catch(Exception $e){
    http_response_code(500);
    echo json_encode(['ok'=>false,'error'=>'DB error','detail'=>$e->getMessage()]);
}
