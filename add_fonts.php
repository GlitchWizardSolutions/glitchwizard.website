<?php
require_once '../private/gws-universal-config.php';

echo "=== ADDING YOUR CUSTOM FONTS ===\n";

try {
    // Clear any existing sample data
    $stmt = $pdo->prepare("DELETE FROM custom_fonts WHERE font_file_path LIKE '/admin/assets/fonts/custom/inter-%' OR font_file_path LIKE '/admin/assets/fonts/custom/roboto-%'");
    $stmt->execute();
    echo "✅ Cleared sample data\n";
    
    // Add your custom fonts
    $fonts = [
        ['All Formal Regular', 'All Formal', '/admin/assets/fonts/custom/All Formal by Kestrel Montes.otf', 'otf', 59956],
        ['All Formal Italic', 'All Formal', '/admin/assets/fonts/custom/All Formal by Kestrel Montes Italic.otf', 'otf', 59752],
        ['Besotted Love', 'Besotted Love', '/admin/assets/fonts/custom/BesottedLove.otf', 'otf', 112040],
        ['Calgary Regular', 'Calgary', '/admin/assets/fonts/custom/Calgary-Regular.otf', 'otf', 27792]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO custom_fonts (font_name, font_family, font_file_path, font_format, file_size, is_active, uploaded_date) VALUES (?, ?, ?, ?, ?, 1, NOW())");
    
    foreach ($fonts as $font) {
        $stmt->execute($font);
        echo "✅ Added: {$font[0]} ({$font[1]})\n";
    }
    
    echo "\n=== VERIFICATION ===\n";
    $stmt = $pdo->query("SELECT font_name, font_family, font_file_path, is_active FROM custom_fonts WHERE is_active = 1 ORDER BY font_family, font_name");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Fonts now in database:\n";
    foreach ($results as $font) {
        echo "- {$font['font_name']} ({$font['font_family']}) - {$font['font_file_path']}\n";
    }
    
    echo "\n✅ Your custom fonts are now ready to use in the branding settings!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
