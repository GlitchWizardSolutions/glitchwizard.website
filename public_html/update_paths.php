<?php
require_once '../private/gws-universal-config.php';

echo "<h2>Database Path Update</h2>";

// Find font files and update database
$upload_dir = '../assets/fonts/custom/';
if (is_dir($upload_dir)) {
    $files = scandir($upload_dir);
    $font_files = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']) && preg_match('/\.(woff2|woff|ttf|otf)$/i', $file);
    });
    
    echo "<p>Found " . count($font_files) . " font files</p>";
    
    if (!empty($font_files)) {
        // Map files to database slots
        $updates = [];
        foreach ($font_files as $file) {
            if (preg_match('/custom_font_(\d+)_/', $file, $matches)) {
                $slot = $matches[1];
                $updates[$slot] = $file;
            }
        }
        
        echo "<p>Mapping files to slots:</p><ul>";
        foreach ($updates as $slot => $file) {
            echo "<li>Slot $slot: $file</li>";
        }
        echo "</ul>";
        
        // Update database with paths relative to admin/settings/
        foreach ($updates as $slot => $file) {
            $column = "font_upload_$slot";
            $path = "../../assets/fonts/custom/$file";
            
            try {
                $stmt = $pdo->prepare("UPDATE setting_business_identity SET $column = ? WHERE id = 1");
                $stmt->execute([$path]);
                echo "<p>✓ Updated $column: $path</p>";
            } catch (Exception $e) {
                echo "<p>✗ Error updating $column: " . $e->getMessage() . "</p>";
            }
        }
        
        // Verify updates
        echo "<h3>Verification:</h3>";
        $verify_stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1");
        $result = $verify_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            foreach ($result as $column => $path) {
                $exists = !empty($path) && file_exists($path) ? "✓ EXISTS" : "✗ NOT FOUND";
                echo "<p>$column: " . ($path ?: "EMPTY") . " $exists</p>";
            }
        }
    }
}

echo "<p><a href='admin/settings/branding_settings_tabbed.php'>Go to Branding Settings</a></p>";
?>
