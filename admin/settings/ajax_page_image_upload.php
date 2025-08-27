<?php
// ajax_page_image_upload.php - handles About page (and future pages) image upload
include_once '../assets/includes/main.php';
require_once dirname(__DIR__,2) . '/assets/includes/content_repository.php';
header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']); exit; }
if(($_SESSION['admin_role'] ?? '') === 'Guest'){ http_response_code(403); echo json_encode(['ok'=>false,'error'=>'Insufficient role']); exit; }
$slug = isset($_POST['slug']) ? preg_replace('/[^a-z0-9\-]/','', strtolower($_POST['slug'])) : '';
if(!$slug){ echo json_encode(['ok'=>false,'error'=>'Missing slug']); exit; }
if($slug==='accessibility'){ echo json_encode(['ok'=>false,'error'=>'Restricted slug']); exit; }
if(empty($_FILES['image'])){ echo json_encode(['ok'=>false,'error'=>'No file']); exit; }
$img = $_FILES['image'];
if($img['error']!==UPLOAD_ERR_OK){ echo json_encode(['ok'=>false,'error'=>'Upload error']); exit; }
if($img['size'] > 2*1024*1024){ echo json_encode(['ok'=>false,'error'=>'File too large']); exit; }
$ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
if(!preg_match('/^(jpg|jpeg|png|webp)$/',$ext)){ echo json_encode(['ok'=>false,'error'=>'Invalid format']); exit; }
// Determine subdirectory based on slug
if($slug === 'about'){
    $subDir = 'about';
} elseif(preg_match('/^service-[1-6]$/', $slug)){
    $subDir = 'services/' . $slug;
} else {
    $subDir = 'page-uploads';
}
$destDir = PROJECT_ROOT . '/public_html/assets/img/' . $subDir . '/';
if(!is_dir($destDir)) @mkdir($destDir,0755,true);
if(!is_dir($destDir)) @mkdir($destDir,0755,true);
if(!is_dir($destDir) || !is_writable($destDir)){ echo json_encode(['ok'=>false,'error'=>'Destination not writable']); exit; }
$safeBase = preg_replace('/[^a-zA-Z0-9_-]/','', pathinfo($img['name'], PATHINFO_FILENAME));
$finalName = $slug . '_' . date('Ymd_His') . '_' . $safeBase . '.' . $ext;
$destPath = $destDir . $finalName;
if(!move_uploaded_file($img['tmp_name'],$destPath)){ echo json_encode(['ok'=>false,'error'=>'Move failed']); exit; }
$relPath = 'assets/img/' . $subDir . '/' . $finalName;
// Optional previous path deletion
$prev = $_POST['prev_path'] ?? '';
if($prev && strpos($prev,'assets/img/'.$subDir.'/')===0){
    $prevFull = PROJECT_ROOT . '/public_html/' . $prev;
    if(is_file($prevFull)) @unlink($prevFull);
}
// Resize / ratio enforce (16:9 target, max 1600x900)
function page_img_load($path){ $info = getimagesize($path); if(!$info) return [null,null]; switch($info[2]){ case IMAGETYPE_JPEG: return [imagecreatefromjpeg($path), $info]; case IMAGETYPE_PNG: return [imagecreatefrompng($path), $info]; case IMAGETYPE_WEBP: if(function_exists('imagecreatefromwebp')) return [@imagecreatefromwebp($path), $info]; } return [null,null]; }
list($src,$info) = page_img_load($destPath);
if($src && $info){
    $w=$info[0]; $h=$info[1];
    $targetRatio = 16/9; $curRatio = $w/$h;
    // Crop to 16:9 if markedly different (>5%)
    if(abs($curRatio - $targetRatio) > 0.05){
        if($curRatio > $targetRatio){ // too wide
            $newW = (int)round($h * $targetRatio); $x = (int)(($w - $newW)/2); $y=0; $cropW=$newW; $cropH=$h;
        } else { // too tall
            $newH = (int)round($w / $targetRatio); $y = (int)(($h - $newH)/2); $x=0; $cropW=$w; $cropH=$newH;
        }
        $tmp = imagecreatetruecolor($cropW,$cropH); imagecopy($tmp,$src,0,0,$x,$y,$cropW,$cropH); imagedestroy($src); $src=$tmp; $w=$cropW; $h=$cropH;
    }
    // Scale down if larger than limits
    $maxW=1600; $maxH=900;
    if($w>$maxW || $h>$maxH){
        $scale = min($maxW/$w, $maxH/$h);
        $nw = (int)round($w*$scale); $nh=(int)round($h*$scale);
        $res = imagecreatetruecolor($nw,$nh);
        imagecopyresampled($res,$src,0,0,0,0,$nw,$nh,$w,$h);
        imagedestroy($src); $src=$res; $w=$nw; $h=$nh;
    }
    // Re-encode (try webp if available else keep extension)
    $outPath = $destPath;
    if(function_exists('imagewebp')){
        $webp = preg_replace('/\.(jpe?g|png|webp)$/i','.webp',$destPath);
        if(imagewebp($src,$webp,82)){
            @unlink($destPath);
            $destPath = $webp;
            $relPath = 'assets/img/' . $subDir . '/' . basename($webp);
        }
    } else {
        if($ext==='jpg' || $ext==='jpeg'){ imagejpeg($src,$destPath,82); }
        elseif($ext==='png'){ imagepng($src,$destPath,7); }
    }
    imagedestroy($src);
}
// Update JSON payload (image key) retaining existing structure
try {
    $existing = pages_get_payload($pdo,$slug);
    $existing['image'] = $relPath;
    pages_upsert_payload($pdo,$slug,$existing);
    echo json_encode(['ok'=>true,'path'=>$relPath]);
} catch(Exception $e){
    echo json_encode(['ok'=>false,'error'=>'DB error']);
}
