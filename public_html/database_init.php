<?php
require_once '../private/gws-universal-config.php';

echo "<h2>Database Record Initialization</h2>";

// Check if setting_business_identity record exists
$stmt = $pdo->query("SELECT COUNT(*) as count FROM setting_business_identity WHERE id = 1");
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['count'] == 0) {
    echo "<p class='error'>No business identity record found with id=1. Creating initial record...</p>";
    
    // Create initial record
    $create_stmt = $pdo->prepare("
        INSERT INTO setting_business_identity (id, primary_font, heading_font, body_font) 
        VALUES (1, 'Arial', 'Arial', 'Arial')
    ");
    
    if ($create_stmt->execute()) {
        echo "<p class='success'>✓ Initial business identity record created successfully!</p>";
    } else {
        echo "<p class='error'>✗ Failed to create initial record</p>";
    }
} else {
    echo "<p class='success'>✓ Business identity record exists</p>";
}

// Now check the font upload paths and fix them if needed
echo "<h3>Font Path Analysis and Repair</h3>";

$stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1");
$font_data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($font_data) {
    echo "<p>Current database values:</p><ul>";
    foreach ($font_data as $slot => $path) {
        echo "<li><strong>$slot:</strong> " . (empty($path) ? "EMPTY" : $path) . "</li>";
    }
    echo "</ul>";
    
    // Check for font files in the directory
    $upload_dir = '../assets/fonts/custom/';
    if (is_dir($upload_dir)) {
        $files = scandir($upload_dir);
        $font_files = array_filter($files, function($file) {
            return !in_array($file, ['.', '..']) && preg_match('/\.(woff2|woff|ttf|otf)$/i', $file);
        });
        
        echo "<p>Font files found in directory:</p><ul>";
        foreach ($font_files as $file) {
            echo "<li>$file</li>";
        }
        echo "</ul>";
        
        // Map files to slots and update database
        if (!empty($font_files)) {
            echo "<h4>Updating database with correct paths:</h4>";
            
            foreach ($font_files as $file) {
                // Extract slot number from filename
                if (preg_match('/custom_font_(\d+)_/', $file, $matches)) {
                    $slot_num = $matches[1];
                    $slot_column = "font_upload_$slot_num";
                    $db_path = '../assets/fonts/custom/' . $file;
                    
                    try {
                        $update_stmt = $pdo->prepare("UPDATE setting_business_identity SET $slot_column = ? WHERE id = 1");
                        $update_stmt->execute([$db_path]);
                        echo "<p class='success'>✓ Updated $slot_column with: $db_path</p>";
                    } catch (Exception $e) {
                        echo "<p class='error'>✗ Error updating $slot_column: " . $e->getMessage() . "</p>";
                    }
                }
            }
        }
    }
}

echo "<h3>Final Verification</h3>";
$final_stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1");
$final_data = $final_stmt->fetch(PDO::FETCH_ASSOC);

echo "<p>Updated database values:</p><ul>";
foreach ($final_data as $slot => $path) {
    $status = !empty($path) ? "HAS VALUE: $path" : "EMPTY";
    $file_exists = !empty($path) && file_exists($path) ? " (FILE EXISTS)" : (!empty($path) ? " (FILE NOT FOUND)" : "");
    echo "<li><strong>$slot:</strong> $status$file_exists</li>";
}
echo "</ul>";

echo "<p><a href='complete_font_debug.php'>Re-run complete debug</a></p>";
echo "<p><a href='admin/settings/branding_settings_tabbed.php'>Go to Branding Settings</a></p>";
?>
