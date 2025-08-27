<?php
header('Content-Type: application/json');
require_once __DIR__ . '/assets/includes/ajax_bootstrap.php';
if(session_status()!==PHP_SESSION_ACTIVE){session_start();}
if($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit; }
$id = isset($_POST['id'])? (int)$_POST['id'] : 0;
if($id<=0){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Invalid id']); exit; }
try {
    $stmt=$pdo->prepare("UPDATE content_items SET active=0, updated_at=NOW() WHERE id=:id AND area='service'");
    $stmt->execute([':id'=>$id]);
    echo json_encode(['ok'=>true,'id'=>$id]);
} catch(Exception $e){ http_response_code(500); echo json_encode(['ok'=>false,'error'=>'DB error','detail'=>$e->getMessage()]); }
