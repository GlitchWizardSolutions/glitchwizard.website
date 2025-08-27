<?php
// Quick database check for quaternary color
try {
    $pdo = new PDO('mysql:host=localhost;dbname=gws_universal', 'root', '');
    
    echo "<h3>Database Check</h3>";
    
    // Check current database content
    $stmt = $pdo->query("SELECT brand_quaternary_color, brand_primary_color FROM setting_branding_colors WHERE id = 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        echo "<p><strong>Database Values:</strong></p>";
        echo "<p>Quaternary: " . ($row['brand_quaternary_color'] ?? 'NULL') . "</p>";
        echo "<p>Primary: " . ($row['brand_primary_color'] ?? 'NULL') . "</p>";
    } else {
        echo "<p>No data found in database</p>";
    }
    
    // Test the getBrandingColors function
    include_once '../assets/includes/branding-functions.php';
    $colors = getBrandingColors();
    
    echo "<h3>getBrandingColors() Result</h3>";
    echo "<p>Quaternary: " . ($colors['quaternary'] ?? 'NOT SET') . "</p>";
    echo "<p>Primary: " . ($colors['primary'] ?? 'NOT SET') . "</p>";
    
    echo "<h3>All Colors from Function</h3>";
    echo "<pre>" . print_r($colors, true) . "</pre>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
