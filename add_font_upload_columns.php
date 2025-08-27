<?php
/**
 * Add font upload columns to setting_business_identity table
 * Run this script once to add the necessary database columns for font uploads
 */

require_once __DIR__ . '/private/gws-universal-config.php';

echo "Starting font upload columns setup...\n";

try {
    // Check if columns exist and add them if they don't
    $columns_to_add = [
        'font_upload_1' => 'VARCHAR(255) DEFAULT \'\'',
        'font_upload_2' => 'VARCHAR(255) DEFAULT \'\'',
        'font_upload_3' => 'VARCHAR(255) DEFAULT \'\'',
        'font_upload_4' => 'VARCHAR(255) DEFAULT \'\'',
        'font_upload_5' => 'VARCHAR(255) DEFAULT \'\''
    ];
    
    foreach ($columns_to_add as $column_name => $column_definition) {
        // Check if column exists
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE table_name = 'setting_business_identity' 
            AND column_name = ? 
            AND table_schema = DATABASE()
        ");
        $stmt->execute([$column_name]);
        $column_exists = $stmt->fetchColumn() > 0;
        
        if (!$column_exists) {
            $sql = "ALTER TABLE setting_business_identity ADD COLUMN $column_name $column_definition";
            $pdo->exec($sql);
            echo "Added column: $column_name\n";
        } else {
            echo "Column already exists: $column_name\n";
        }
    }
    
    echo "\nFont upload columns setup completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error setting up font upload columns: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
