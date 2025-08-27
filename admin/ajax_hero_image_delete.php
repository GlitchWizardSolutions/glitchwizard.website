<?php
// AJAX: Delete a hero image file (and clear DB reference if currently used)
header('Content-Type: application/json');
require_once __DIR__ . '/assets/includes/ajax_bootstrap.php';
if(session_status()!==PHP_SESSION_ACTIVE){session_start();}
if($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit; }

$rel = trim($_POST['file'] ?? '');
if($rel==='') { http_response_code(400); echo json_encode(['ok'=>false,'error'=>'No file specified']); exit; }

// Must begin with assets/img/hero-uploads/
if(strpos($rel,'assets/img/hero-uploads/') !== 0){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Invalid path']); exit; }

$baseDir = realpath(PROJECT_ROOT . '/public_html/assets/img/hero-uploads');
$full = realpath(PROJECT_ROOT . '/public_html/' . $rel);
if(!$full || !$baseDir || strpos($full,$baseDir)!==0){ http_response_code(400); echo json_encode(['ok'=>false,'error'=>'Path traversal blocked']); exit; }

$existed = file_exists($full);
$deleted = false; $err = null;
if($existed){
  if(is_writable($full)){
    $deleted = @unlink($full);
    if(!$deleted){ $err='unlink failed'; }
  } else { $err='not writable'; }
} else { $err='not found'; }

// Clear DB reference if matches current hero image regardless of physical deletion success (so UI consistent)
if(isset($pdo)){
  try {
    $stmt = $pdo->prepare("SELECT hero_background_image FROM setting_content_homepage LIMIT 1");
    $row = $stmt->execute() ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
    if($row && $row['hero_background_image'] === $rel){
      $pdo->exec("UPDATE setting_content_homepage SET hero_background_image='' LIMIT 1");
    }
  } catch(Exception $e){ /* ignore */ }
}

if(!$deleted){
  http_response_code($existed?500:404);
  echo json_encode(['ok'=>false,'error'=>$err?:'delete failed']);
  exit;
}

echo json_encode(['ok'=>true,'file'=>$rel]);
?>
