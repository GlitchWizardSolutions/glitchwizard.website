<?php
require_once 'shared/branding-functions-enhanced.php';

echo "Enhanced Branding Status Check:\n";
echo "=================================\n";

try {
    $activeTemplate = getActiveBrandingTemplate('public');
    echo "Active template: $activeTemplate\n";
    
    $cssFileName = getAreaCSSFileName('public', $activeTemplate);
    echo "CSS file name: $cssFileName\n";
    
    $cssPath = "assets/css/$cssFileName";
    echo "CSS path: $cssPath\n";
    echo "File exists: " . (file_exists($cssPath) ? 'YES' : 'NO') . "\n";
    
    echo "\nAvailable CSS files:\n";
    $files = scandir('assets/css/');
    foreach ($files as $file) {
        if (strpos($file, 'public-branding') === 0) {
            echo "- $file\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
