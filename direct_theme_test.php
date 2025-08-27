<?php
require_once 'private/gws-universal-config.php';

echo "Direct database test...\n";

try {
    // Check if default template exists
    $stmt = $pdo->prepare('SELECT * FROM setting_branding_templates WHERE template_key = ?');
    $stmt->execute(['default']);
    $default_template = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($default_template) {
        echo "✅ Default template exists in database\n";
        
        // Try to activate it directly
        $pdo->beginTransaction();
        
        // Deactivate all templates
        $pdo->exec("UPDATE setting_branding_templates SET is_active = 0");
        echo "- Deactivated all templates\n";
        
        // Activate the default template
        $stmt = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 1 WHERE template_key = ?");
        $result = $stmt->execute(['default']);
        echo "- Update statement executed: " . ($result ? 'success' : 'failed') . "\n";
        echo "- Rows affected: " . $stmt->rowCount() . "\n";
        
        if ($stmt->rowCount() > 0) {
            $pdo->commit();
            echo "✅ Successfully activated default theme!\n";
        } else {
            $pdo->rollback();
            echo "❌ No rows were updated - template key might not exist\n";
        }
        
    } else {
        echo "❌ Default template does not exist in database\n";
    }
    
    // Show final state
    echo "\nFinal active template:\n";
    $stmt = $pdo->query('SELECT template_key, template_name, is_active FROM setting_branding_templates WHERE is_active = 1');
    $active = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($active) {
        echo "Active: {$active['template_key']} - {$active['template_name']}\n";
    } else {
        echo "No active template found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
}
?>
