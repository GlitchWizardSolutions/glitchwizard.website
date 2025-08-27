<?php
require_once '../../private/gws-universal-config.php';

echo "=== High Contrast Theme Debug ===\n";

try {
    // Check if high_contrast template exists
    echo "1. Checking if high_contrast template exists in database...\n";
    $stmt = $pdo->prepare('SELECT * FROM setting_branding_templates WHERE template_key = ?');
    $stmt->execute(['high_contrast']);
    $high_contrast_template = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($high_contrast_template) {
        echo "✅ high_contrast template found\n";
        echo "   - Name: {$high_contrast_template['template_name']}\n";
        echo "   - Description: {$high_contrast_template['template_description']}\n";
        echo "   - Active: " . ($high_contrast_template['is_active'] ? 'Yes' : 'No') . "\n";
        echo "   - Config: {$high_contrast_template['template_config']}\n";
    } else {
        echo "❌ high_contrast template NOT found\n";
        
        // Show what templates do exist
        echo "\nAvailable templates:\n";
        $stmt = $pdo->query('SELECT template_key, template_name, is_active FROM setting_branding_templates ORDER BY template_key');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $active = $row['is_active'] ? ' (ACTIVE)' : '';
            echo "- {$row['template_key']}: {$row['template_name']}{$active}\n";
        }
        exit;
    }
    
    // Test manual activation
    echo "\n2. Testing manual activation...\n";
    
    // Load the branding functions
    require_once '../assets/includes/branding-functions.php';
    
    // Backup current state
    $stmt = $pdo->query('SELECT template_key FROM setting_branding_templates WHERE is_active = 1');
    $current_active = $stmt->fetchColumn();
    echo "   Current active template: " . ($current_active ?: 'None') . "\n";
    
    // Try to activate high_contrast
    echo "   Attempting to activate high_contrast...\n";
    $result = setActiveBrandingTemplate('high_contrast');
    
    if ($result) {
        echo "✅ Activation returned TRUE\n";
    } else {
        echo "❌ Activation returned FALSE\n";
    }
    
    // Check final state
    echo "\n3. Checking final state...\n";
    $stmt = $pdo->query('SELECT template_key, is_active FROM setting_branding_templates ORDER BY template_key');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $status = $row['is_active'] ? 'ACTIVE' : 'inactive';
        echo "   - {$row['template_key']}: $status\n";
    }
    
    // Test the function from the tabbed interface
    echo "\n4. Testing tabbed interface function...\n";
    
    // Define the simple function like in the tabbed interface
    function setActiveBrandingTemplate_Simple($template_key) {
        global $pdo;
        try {
            $pdo->beginTransaction();
            
            echo "   - Starting transaction\n";
            
            // Deactivate all templates
            $result1 = $pdo->exec("UPDATE setting_branding_templates SET is_active = 0");
            echo "   - Deactivated $result1 templates\n";
            
            // Activate the selected template
            $stmt = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 1 WHERE template_key = ?");
            $result2 = $stmt->execute([$template_key]);
            echo "   - Execute result: " . ($result2 ? 'true' : 'false') . "\n";
            echo "   - Rows affected: " . $stmt->rowCount() . "\n";
            
            if ($stmt->rowCount() === 0) {
                $pdo->rollback();
                echo "   - ROLLBACK: No rows affected\n";
                return false;
            }
            
            $pdo->commit();
            echo "   - COMMIT: Transaction completed\n";
            return true;
        } catch (Exception $e) {
            $pdo->rollback();
            echo "   - ROLLBACK: Exception - " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    echo "   Testing setActiveBrandingTemplate_Simple('high_contrast')...\n";
    $simple_result = setActiveBrandingTemplate_Simple('high_contrast');
    echo "   Result: " . ($simple_result ? 'SUCCESS' : 'FAILED') . "\n";
    
    // Final verification
    echo "\n5. Final verification...\n";
    $stmt = $pdo->prepare('SELECT template_key, template_name FROM setting_branding_templates WHERE is_active = 1');
    $stmt->execute();
    $final_active = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($final_active) {
        echo "✅ Currently active: {$final_active['template_key']} - {$final_active['template_name']}\n";
    } else {
        echo "❌ No active template found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Debug Complete ===\n";
?>
