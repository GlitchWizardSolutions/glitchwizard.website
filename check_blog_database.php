<?php
require_once '../private/gws-universal-config.php';

try {
    echo "=== BLOG DATABASE ANALYSIS ===\n";
    
    // Check if tables exist and get their data
    $tables = ['setting_blog_identity', 'setting_blog_config', 'setting_blog_display', 'setting_blog_features', 'setting_blog_comments', 'setting_blog_seo'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "$table: $count rows\n";
            
            if ($count > 0) {
                $stmt = $pdo->query("SELECT * FROM $table LIMIT 1");
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "  Sample data: " . json_encode(array_slice($data, 0, 5)) . "\n";
            }
        } catch (Exception $e) {
            echo "$table: ERROR - " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Database connection error: " . $e->getMessage() . "\n";
}
?>
