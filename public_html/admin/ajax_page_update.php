<?php
// Wrapper endpoint at /admin/ajax_page_update.php to unify path (calls settings version)
header('Content-Type: application/json');
// Reuse existing implementation in settings directory
require_once __DIR__ . '/assets/includes/ajax_bootstrap.php';
require_once __DIR__ . '/../assets/includes/content_repository.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit; }
if(!isset($pdo)) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'DB unavailable']); exit; }
$slug = isset($_POST['slug']) ? preg_replace('/[^a-z0-9\-]/','', strtolower($_POST['slug'])) : '';
if(!$slug){ echo json_encode(['ok'=>false,'error'=>'Missing slug']); exit; }
if($slug==='accessibility'){ echo json_encode(['ok'=>false,'error'=>'Slug not editable']); exit; }
$meta_title = trim($_POST['meta_title'] ?? '');
$meta_description = trim($_POST['meta_description'] ?? '');
$parts=[]; foreach($_POST as $k=>$v){ if(strpos($k,'part_')===0){ $parts[substr($k,5)]=$v; } }
try { pages_upsert_metadata($pdo,$slug,$meta_title,$meta_description); if($parts){ $existing = pages_get_payload($pdo,$slug); if(!is_array($existing)) $existing=[]; $merged = array_merge($existing,$parts); pages_upsert_payload($pdo,$slug,$merged); }
  echo json_encode(['ok'=>true,'updated_at'=>date('c')]);
} catch(Exception $e){ http_response_code(500); $msg = (isset($_GET['debug']) || (defined('APP_ENV') && APP_ENV!=='prod'))? $e->getMessage() : 'DB error'; echo json_encode(['ok'=>false,'error'=>$msg]); }
