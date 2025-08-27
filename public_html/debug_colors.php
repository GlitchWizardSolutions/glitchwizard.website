<?php
// Test file to debug branding colors from database
require_once '../private/gws-universal-config.php';
require_once 'shared/branding-functions-enhanced.php';

echo "=== BRANDING COLORS DEBUG ===\n";

try {
    $colors = getBrandingColors();
    echo "Colors retrieved from database:\n";
    print_r($colors);
    
    echo "\nSpecific color values:\n";
    echo "Primary: " . ($colors['brand_primary_color'] ?? 'NOT SET') . "\n";
    echo "Secondary: " . ($colors['brand_secondary_color'] ?? 'NOT SET') . "\n";
    echo "Tertiary: " . ($colors['brand_tertiary_color'] ?? 'NOT SET') . "\n";
    echo "Quaternary: " . ($colors['brand_quaternary_color'] ?? 'NOT SET') . "\n";
    
    // Test the database connection directly
    global $pdo;
    echo "\nDirect database query:\n";
    $stmt = $pdo->query("DESCRIBE setting_branding_colors");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Available columns in setting_branding_colors table:\n";
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
    echo "\nActual data in table:\n";
    $stmt = $pdo->query("SELECT * FROM setting_branding_colors LIMIT 1");
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($data) {
        foreach ($data as $key => $value) {
            echo "$key: $value\n";
        }
    } else {
        echo "No data found in table!\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
