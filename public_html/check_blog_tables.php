<?php
require_once '../private/gws-universal-config.php';
try {
    echo "=== BLOG TABLE STRUCTURES ===\n\n";
    
    $tables = ['setting_blog_config', 'setting_blog_identity', 'setting_blog_display'];
    
    foreach ($tables as $table) {
        echo "Table: $table\n";
        $stmt = $pdo->query("DESCRIBE $table");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "- {$column['Field']} ({$column['Type']})\n";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
