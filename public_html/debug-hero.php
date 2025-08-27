<?php
// Quick debug to see what's being loaded
include_once "assets/includes/doctype.php";

echo "<h1>Debug Information</h1>";
echo "<h2>Hero Content:</h2>";
echo "<pre>";
print_r($hero_content ?? 'NOT SET');
echo "</pre>";

echo "<h2>Business Info:</h2>";
echo "<pre>";
echo "business_name: " . ($business_name ?? 'NOT SET') . "\n";
echo "contact_email: " . ($contact_email ?? 'NOT SET') . "\n";
echo "</pre>";

echo "<h2>Current Working Directory:</h2>";
echo "<pre>" . getcwd() . "</pre>";

echo "<h2>Hero Image File Check:</h2>";
echo "<pre>";
$hero_img_path = $hero_content['bg_image'] ?? 'NOT SET';
echo "Hero image path: " . $hero_img_path . "\n";
if ($hero_img_path && $hero_img_path !== 'NOT SET') {
    echo "File exists: " . (file_exists($hero_img_path) ? 'YES' : 'NO') . "\n";
    echo "Full path check: " . (file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $hero_img_path) ? 'YES' : 'NO') . "\n";
}
echo "</pre>";

echo "<h2>Available Hero Images:</h2>";
echo "<pre>";
$hero_dir = 'assets/img/hero-uploads/';
if (is_dir($hero_dir)) {
    $files = scandir($hero_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo $hero_dir . $file . "\n";
        }
    }
} else {
    echo "Directory not found: " . $hero_dir . "\n";
}
echo "</pre>";
?>
