<?php
// Simple test
echo "Starting test...\n";

try {
    require_once 'private/gws-universal-config.php';
    echo "Config loaded\n";
    
    if (isset($pdo)) {
        echo "PDO connection exists\n";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM setting_branding_templates");
        $count = $stmt->fetchColumn();
        echo "Templates in database: $count\n";
        
        $stmt = $pdo->query("SELECT template_key FROM setting_branding_templates WHERE template_key = 'default'");
        $exists = $stmt->fetchColumn();
        echo "Default template exists: " . ($exists ? 'Yes' : 'No') . "\n";
        
    } else {
        echo "No PDO connection\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Test complete\n";
?>
