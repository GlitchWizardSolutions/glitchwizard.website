<?php
require_once '../private/gws-universal-config.php';

echo "=== CHECKING CUSTOM FONTS TABLE ===\n";

try {
    $stmt = $pdo->query('DESCRIBE custom_fonts');
    echo "✅ custom_fonts table exists!\n\n";
    
    echo "Table structure:\n";
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- {$column['Field']} ({$column['Type']})\n";
    }
    
    echo "\nCurrent font entries:\n";
    $stmt = $pdo->query('SELECT * FROM custom_fonts');
    $fonts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($fonts)) {
        echo "No fonts currently in database.\n";
    } else {
        foreach ($fonts as $font) {
            echo "- {$font['font_name']} ({$font['font_family']}) - {$font['font_file_path']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Table does not exist: " . $e->getMessage() . "\n";
    echo "You need to run the SQL file first!\n";
}
?>
