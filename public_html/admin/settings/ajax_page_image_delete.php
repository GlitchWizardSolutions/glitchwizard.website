<?php
// ajax_page_image_delete.php - deletes an existing page image and clears JSON payload image key
include_once '../assets/includes/main.php';
require_once dirname(__DIR__,2) . '/assets/includes/content_repository.php';
header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit; }
if(($_SESSION['admin_role'] ?? '') === 'Guest'){ http_response_code(403); echo json_encode(['ok'=>false,'error'=>'Insufficient role']); exit; }
$slug = isset($_POST['slug']) ? preg_replace('/[^a-z0-9\-]/','', strtolower($_POST['slug'])) : '';
$path = trim($_POST['path'] ?? '');
if(!$slug || !$path){ echo json_encode(['ok'=>false,'error'=>'Missing data']); exit; }
if($slug==='accessibility'){ echo json_encode(['ok'=>false,'error'=>'Restricted slug']); exit; }
// Security: only allow deletions within allowed dirs
$allowedPaths = ['assets/img/page-uploads/', 'assets/img/about/', 'assets/img/services/service-1/', 'assets/img/services/service-2/', 'assets/img/services/service-3/', 'assets/img/services/service-4/', 'assets/img/services/service-5/', 'assets/img/services/service-6/'];
$pathAllowed = false;
foreach($allowedPaths as $allowedPath){
    if(strpos($path, $allowedPath) === 0){ $pathAllowed = true; break; }
}
if(!$pathAllowed){ echo json_encode(['ok'=>false,'error'=>'Invalid path']); exit; }
$full = PROJECT_ROOT . '/public_html/' . $path;
if(is_file($full)) @unlink($full);
// If about image: remove any sibling about.* leftovers to keep directory clean
if($slug==='about'){
    $dir = dirname($full);
    foreach(['jpg','jpeg','png','webp'] as $e){ $candidate=$dir.'/about.'.$e; if(is_file($candidate)) @unlink($candidate); }
}
$full = PROJECT_ROOT . '/public_html/' . $path;
if(is_file($full)){
    @unlink($full);
}
try {
    $existing = pages_get_payload($pdo,$slug);
    if(isset($existing['image']) && $existing['image']===$path){
        unset($existing['image']);
        pages_upsert_payload($pdo,$slug,$existing);
    }
    echo json_encode(['ok'=>true]);
} catch(Exception $e){
    echo json_encode(['ok'=>false,'error'=>'DB error']);
}
