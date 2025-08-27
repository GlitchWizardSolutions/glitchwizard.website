<!DOCTYPE html>
<html>
<head>
    <title>Font Debug</title>
</head>
<body>
    <h1>Font Debug Information</h1>
    
    <?php
    require_once '../private/gws-universal-config.php';
    
    echo "<h3>Database Font Uploads:</h3>";
    $stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1");
    $fonts_in_db = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($fonts_in_db) {
        echo "<ul>";
        foreach ($fonts_in_db as $slot => $path) {
            echo "<li><strong>$slot:</strong> $path";
            if (!empty($path)) {
                echo " (exists: " . (file_exists($path) ? 'YES' : 'NO') . ")";
            }
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No font uploads found in database.</p>";
    }
    
    echo "<h3>Files in Upload Directory:</h3>";
    $upload_dir = '../assets/fonts/custom/';
    if (is_dir($upload_dir)) {
        $files = scandir($upload_dir);
        echo "<ul>";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "<li>$file</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>Upload directory doesn't exist.</p>";
    }
    
    echo "<h3>Test getAvailableFonts():</h3>";
    include '../admin/settings/branding_settings_tabbed.php';
    $fonts = getAvailableFonts();
    echo "<p>Total fonts: " . count($fonts) . "</p>";
    echo "<ul>";
    foreach ($fonts as $font) {
        echo "<li>" . $font['family'] . " (" . $font['category'] . ")</li>";
    }
    echo "</ul>";
    ?>
    
</body>
</html>
