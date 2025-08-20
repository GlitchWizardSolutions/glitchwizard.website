<?php
// Database Update Script - Add Tertiary and Quaternary Colors
echo "=== Adding Tertiary and Quaternary Brand Colors ===\n\n";

try {
    require_once 'private/gws-universal-config.php';
    echo "✅ Database connection established\n";
    
    // Add brand_tertiary_color column
    echo "\n1. Adding brand_tertiary_color column...\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM setting_branding_colors LIKE 'brand_tertiary_color'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE setting_branding_colors ADD COLUMN brand_tertiary_color VARCHAR(7) DEFAULT '#8B4513' COMMENT 'Third brand color - Tertiary'");
        echo "   ✅ Added brand_tertiary_color column\n";
    } else {
        echo "   ℹ️  brand_tertiary_color column already exists\n";
    }
    
    // Add brand_quaternary_color column
    echo "\n2. Adding brand_quaternary_color column...\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM setting_branding_colors LIKE 'brand_quaternary_color'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE setting_branding_colors ADD COLUMN brand_quaternary_color VARCHAR(7) DEFAULT '#2E8B57' COMMENT 'Fourth brand color - Quaternary'");
        echo "   ✅ Added brand_quaternary_color column\n";
    } else {
        echo "   ℹ️  brand_quaternary_color column already exists\n";
    }
    
    // Update existing records with default values
    echo "\n3. Updating existing records with default values...\n";
    $stmt = $pdo->prepare("
        UPDATE setting_branding_colors 
        SET 
            brand_tertiary_color = COALESCE(brand_tertiary_color, '#8B4513'),
            brand_quaternary_color = COALESCE(brand_quaternary_color, '#2E8B57')
        WHERE id = 1
    ");
    $stmt->execute();
    echo "   ✅ Updated existing record with defaults\n";
    
    // Show current table structure
    echo "\n4. Current table structure:\n";
    $stmt = $pdo->query("DESCRIBE setting_branding_colors");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $color_indicator = (strpos($row['Field'], 'color') !== false) ? '🎨 ' : '   ';
        echo "   {$color_indicator}{$row['Field']} ({$row['Type']})\n";
    }
    
    // Show current brand colors
    echo "\n5. Current brand colors in database:\n";
    $stmt = $pdo->query("SELECT * FROM setting_branding_colors WHERE id = 1");
    $colors = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($colors) {
        $color_fields = [
            'brand_primary_color' => 'Primary',
            'brand_secondary_color' => 'Secondary', 
            'brand_tertiary_color' => 'Tertiary',
            'brand_quaternary_color' => 'Quaternary',
            'brand_accent_color' => 'Accent',
            'brand_warning_color' => 'Warning',
            'brand_danger_color' => 'Danger',
            'brand_info_color' => 'Info'
        ];
        
        foreach ($color_fields as $field => $label) {
            $value = $colors[$field] ?? 'NOT SET';
            echo "   🎨 {$label}: {$value}\n";
        }
    } else {
        echo "   ⚠️  No color data found - you may need to initialize the table\n";
    }
    
    echo "\n✅ Database update completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Go to admin/settings/branding_settings.php\n";
    echo "2. Test the new Tertiary and Quaternary color inputs\n";
    echo "3. Save some colors to verify database functionality\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting:\n";
    echo "1. Make sure MySQL is running\n";
    echo "2. Check database credentials in private/gws-universal-config.php\n";
    echo "3. Verify the setting_branding_colors table exists\n";
}
?>
