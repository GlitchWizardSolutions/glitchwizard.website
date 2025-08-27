<?php
require_once '../private/gws-universal-config.php';

echo "<h2>Font Database Repair Script</h2>";

// Get existing font files
$upload_dir = '../assets/fonts/custom/';
$files = scandir($upload_dir);
$font_files = array_filter($files, function($file) {
    return !in_array($file, ['.', '..']) && preg_match('/\.(woff2|woff|ttf|otf)$/i', $file);
});

echo "<h3>Found font files:</h3><ul>";
foreach ($font_files as $file) {
    echo "<li>$file</li>";
}
echo "</ul>";

// Map files to slots based on naming pattern
$slot_mapping = [];
foreach ($font_files as $file) {
    if (preg_match('/custom_font_(\d+)_/', $file, $matches)) {
        $slot = $matches[1];
        $slot_mapping[$slot] = $file;
    }
}

echo "<h3>Slot mapping:</h3><ul>";
foreach ($slot_mapping as $slot => $file) {
    echo "<li>Slot $slot: $file</li>";
}
echo "</ul>";

// Update database
echo "<h3>Updating database:</h3>";
foreach ($slot_mapping as $slot => $file) {
    $db_path = '../assets/fonts/custom/' . $file;
    $column = "font_upload_$slot";
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO setting_business_identity (id, $column) 
            VALUES (1, ?)
            ON DUPLICATE KEY UPDATE $column = ?
        ");
        $stmt->execute([$db_path, $db_path]);
        echo "<p>✓ Updated slot $slot with path: $db_path</p>";
    } catch (Exception $e) {
        echo "<p>✗ Error updating slot $slot: " . $e->getMessage() . "</p>";
    }
}

// Verify the update
echo "<h3>Verification:</h3>";
try {
    $stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "<ul>";
        foreach ($result as $column => $path) {
            $status = !empty($path) ? "HAS VALUE: $path" : "EMPTY";
            $file_exists = !empty($path) && file_exists($path) ? " (FILE EXISTS)" : (!empty($path) ? " (FILE NOT FOUND)" : "");
            echo "<li><strong>$column:</strong> $status$file_exists</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No database record found!</p>";
    }
} catch (Exception $e) {
    echo "<p>Error reading database: " . $e->getMessage() . "</p>";
}

echo "<p><a href='font_debug_detailed.php'>Re-run font debug test</a></p>";
?>
