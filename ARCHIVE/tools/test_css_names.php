<?php
require_once 'shared/branding-functions-enhanced.php';

echo "CSS Filename Testing:\n";
echo "Default public CSS: " . getAreaCSSFileName('public', 'default') . "\n";
echo "Bold public CSS: " . getAreaCSSFileName('public', 'bold') . "\n";
echo "High-contrast public CSS: " . getAreaCSSFileName('public', 'high-contrast') . "\n";

echo "\nFile exists checks:\n";
$default_file = 'assets/css/' . getAreaCSSFileName('public', 'default');
echo "Default file ($default_file): " . (file_exists($default_file) ? 'EXISTS' : 'NOT FOUND') . "\n";

$bold_file = 'assets/css/' . getAreaCSSFileName('public', 'bold');
echo "Bold file ($bold_file): " . (file_exists($bold_file) ? 'EXISTS' : 'NOT FOUND') . "\n";

echo "\nActual files in assets/css/:\n";
$files = scandir('assets/css/');
foreach ($files as $file) {
    if (strpos($file, 'public-branding') === 0) {
        echo "- $file\n";
    }
}
?>
