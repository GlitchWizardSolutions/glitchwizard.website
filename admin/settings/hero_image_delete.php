<?php
/*
 * Hero Image Deletion Endpoint (AJAX)
 * Deletes a selected hero image file from /assets/img/hero-uploads/ if not currently in use.
 * Security considerations: ensure user is authenticated via existing admin bootstrap (main.php)
 */
include_once '../assets/includes/main.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid method']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw,true);
if(!$data || empty($data['file'])){
    echo json_encode(['success'=>false,'message'=>'No file specified']);
    exit;
}
$rel = $data['file'];
// Must begin with assets/img/hero-uploads/
if(strpos($rel,'assets/img/hero-uploads/') !== 0){
    echo json_encode(['success'=>false,'message'=>'Invalid path']);
    exit;
}
$fs = PROJECT_ROOT . '/public_html/' . $rel;
if(!file_exists($fs)){
    echo json_encode(['success'=>false,'message'=>'File not found']);
    exit;
}
// Prevent deletion if file currently set as active hero
$active = null;
try {
    $row = $pdo->query("SELECT hero_background_image FROM setting_content_homepage LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if($row){ $active = $row['hero_background_image']; }
} catch(Exception $e) { /* ignore */ }
if($active === $rel){
    echo json_encode(['success'=>false,'message'=>'Cannot delete active hero image. Select another first.']);
    exit;
}
if(@unlink($fs)){
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'message'=>'Delete failed (permissions).']);
}
