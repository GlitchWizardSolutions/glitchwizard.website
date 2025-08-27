<!DOCTYPE html>
<html>
<head>
    <title>Fix Font Paths</title>
</head>
<body>
    <h1>Fix Font Database Paths</h1>
    
    <?php
    require_once '../private/gws-universal-config.php';
    
    // First, let's see the current state
    echo "<h3>Current Database Paths:</h3>";
    $stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1");
    $current_fonts = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($current_fonts) {
        echo "<ul>";
        foreach ($current_fonts as $slot => $path) {
            echo "<li><strong>$slot:</strong> $path</li>";
        }
        echo "</ul>";
    }
    
    // Now let's fix the paths
    echo "<h3>Fixing Paths:</h3>";
    
    $font_files = [];
    $upload_dir = '../assets/fonts/custom/';
    
    if (is_dir($upload_dir)) {
        $files = scandir($upload_dir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && preg_match('/^custom_font_(\d)_/', $file, $matches)) {
                $slot_num = $matches[1];
                $font_files["font_upload_$slot_num"] = $upload_dir . $file;
            }
        }
    }
    
    if (!empty($font_files)) {
        echo "<p>Found font files to update:</p><ul>";
        foreach ($font_files as $slot => $path) {
            echo "<li>$slot: $path</li>";
        }
        echo "</ul>";
        
        // Update the database
        foreach ($font_files as $slot => $path) {
            $stmt = $pdo->prepare("UPDATE setting_business_identity SET $slot = ? WHERE id = 1");
            $stmt->execute([$path]);
            echo "<p>Updated $slot with $path</p>";
        }
        
        echo "<p><strong>Database updated!</strong></p>";
        
    } else {
        echo "<p>No font files found to update.</p>";
    }
    
    // Show final state
    echo "<h3>Updated Database Paths:</h3>";
    $stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1");
    $updated_fonts = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($updated_fonts) {
        echo "<ul>";
        foreach ($updated_fonts as $slot => $path) {
            echo "<li><strong>$slot:</strong> $path";
            if (!empty($path)) {
                echo " (exists: " . (file_exists($path) ? 'YES' : 'NO') . ")";
            }
            echo "</li>";
        }
        echo "</ul>";
    }
    ?>
    
    <p><a href="admin/settings/branding_settings_tabbed.php#fonts">Go back to Typography settings</a></p>
    
</body>
</html>
