<?php
// Simulate the header.php environment
$_SERVER['DOCUMENT_ROOT'] = 'c:/xampp/htdocs/gws-universal-hybrid-app/public_html';
$_SERVER['SCRIPT_NAME'] = '/index.php';

require_once 'shared/branding-functions-enhanced.php';

// Simulate header variables
$current_area = 'public';
$active_template = getActiveBrandingTemplate($current_area);
$active_css_file = getAreaCSSFileName($current_area, $active_template);

echo "Debug Header CSS Path Generation:\n";
echo "=================================\n";
echo "Current area: $current_area\n";
echo "Active template: $active_template\n";
echo "Active CSS file: $active_css_file\n";

// Determine the correct CSS path for the current area
$css_path = match($current_area) {
    'admin' => '/admin/assets/css/' . $active_css_file,
    'client_portal' => '/client_portal/assets/css/' . $active_css_file,
    default => '/assets/css/' . $active_css_file
};

echo "Generated CSS path: $css_path\n";

// Build the full filesystem path to check if file exists
$document_root = $_SERVER['DOCUMENT_ROOT'];
$script_dir = dirname($_SERVER['SCRIPT_NAME']);
$full_css_path = $document_root . $script_dir . $css_path;

echo "Document root: $document_root\n";
echo "Script dir: $script_dir\n";
echo "Full CSS path: $full_css_path\n";
echo "File exists at full path: " . (file_exists($full_css_path) ? 'YES' : 'NO') . "\n";

// Check the actual file location
$actual_path = $document_root . $css_path;
echo "Actual path (without script dir): $actual_path\n";
echo "File exists at actual path: " . (file_exists($actual_path) ? 'YES' : 'NO') . "\n";
?>
