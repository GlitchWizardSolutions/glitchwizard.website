<?php
// Test script to debug quaternary color saving

try {
    $pdo = new PDO('mysql:host=localhost;dbname=gws_universal', 'root', '');
    
    echo "=== DATABASE STRUCTURE TEST ===\n";
    
    // Check table structure
    $stmt = $pdo->query('DESCRIBE setting_branding_colors');
    echo "Table columns:\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- {$row['Field']} ({$row['Type']})\n";
    }
    
    echo "\n=== CURRENT DATA TEST ===\n";
    
    // Check current data
    $stmt = $pdo->query('SELECT * FROM setting_branding_colors WHERE id = 1 LIMIT 1');
    $current = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($current) {
        echo "Current data:\n";
        foreach ($current as $key => $value) {
            echo "- $key = " . ($value ?? 'NULL') . "\n";
        }
    } else {
        echo "No data found (will need to INSERT)\n";
    }
    
    echo "\n=== SAVE TEST ===\n";
    
    // Test saving quaternary color
    $test_quaternary = '#ff6b35'; // Orange test color
    
    $stmt = $pdo->prepare("
        INSERT INTO setting_branding_colors (
            id, brand_primary_color, brand_secondary_color, brand_accent_color, 
            brand_tertiary_color, brand_quaternary_color, brand_success_color, brand_warning_color
        ) VALUES (1, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            brand_quaternary_color = VALUES(brand_quaternary_color)
    ");
    
    $result = $stmt->execute([
        '#6c2eb6', '#bf5512', '#28a745', '#8B4513', 
        $test_quaternary, // quaternary color
        '#28a745', '#ffc107'
    ]);
    
    echo "Save result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    echo "\n=== VERIFICATION TEST ===\n";
    
    // Verify the save
    $stmt = $pdo->query('SELECT brand_quaternary_color FROM setting_branding_colors WHERE id = 1');
    $saved = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($saved) {
        echo "Quaternary color after save: " . $saved['brand_quaternary_color'] . "\n";
        echo "Expected: $test_quaternary\n";
        echo "Match: " . ($saved['brand_quaternary_color'] === $test_quaternary ? 'YES' : 'NO') . "\n";
    } else {
        echo "Could not retrieve saved data\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
