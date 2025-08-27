<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Theme Activation Diagnostic ===\n";

try {
    // Step 1: Test database connection
    echo "1. Testing database connection...\n";
    require_once '../../private/gws-universal-config.php';
    
    if (isset($pdo)) {
        echo "   ✅ PDO connection successful\n";
    } else {
        echo "   ❌ No PDO connection\n";
        exit;
    }
    
    // Step 2: Check if table exists
    echo "2. Checking if setting_branding_templates table exists...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'setting_branding_templates'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ Table exists\n";
    } else {
        echo "   ❌ Table does not exist\n";
        exit;
    }
    
    // Step 3: List all templates
    echo "3. Listing all templates:\n";
    $stmt = $pdo->query("SELECT template_key, template_name, is_active FROM setting_branding_templates ORDER BY template_key");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $active = $row['is_active'] ? ' (ACTIVE)' : '';
        echo "   - {$row['template_key']}: {$row['template_name']}{$active}\n";
    }
    
    // Step 4: Check if 'default' template exists
    echo "4. Checking if 'default' template exists...\n";
    $stmt = $pdo->prepare("SELECT * FROM setting_branding_templates WHERE template_key = ?");
    $stmt->execute(['default']);
    $default_template = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($default_template) {
        echo "   ✅ Default template found\n";
        echo "   - Name: {$default_template['template_name']}\n";
        echo "   - Active: " . ($default_template['is_active'] ? 'Yes' : 'No') . "\n";
    } else {
        echo "   ❌ Default template not found\n";
        exit;
    }
    
    // Step 5: Test activation function
    echo "5. Testing activation function...\n";
    require_once '../assets/includes/branding-functions.php';
    
    // First check if function exists
    if (function_exists('setActiveBrandingTemplate')) {
        echo "   ✅ setActiveBrandingTemplate function exists\n";
        
        // Try to activate
        $result = setActiveBrandingTemplate('default');
        
        if ($result) {
            echo "   ✅ Theme activation successful!\n";
        } else {
            echo "   ❌ Theme activation failed\n";
        }
        
        // Verify result
        $stmt = $pdo->prepare("SELECT template_key, template_name FROM setting_branding_templates WHERE is_active = 1");
        $stmt->execute();
        $active = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($active) {
            echo "   Currently active: {$active['template_key']} - {$active['template_name']}\n";
        } else {
            echo "   No active template found\n";
        }
        
    } else {
        echo "   ❌ setActiveBrandingTemplate function not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== Diagnostic Complete ===\n";
?>
