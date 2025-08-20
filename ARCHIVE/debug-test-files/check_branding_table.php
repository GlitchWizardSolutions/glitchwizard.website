<?php
require_once 'private/gws-universal-config.php';

try {
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== BRANDING-RELATED TABLES ===\n";
    $stmt = $pdo->query('SHOW TABLES LIKE "%branding%"');
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "Table: " . $row[0] . "\n";
    }
    
    echo "\n=== SETTINGS TABLES ===\n";
    $stmt = $pdo->query('SHOW TABLES LIKE "%setting%"');
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "Table: " . $row[0] . "\n";
    }
    
    echo "\n=== setting_branding_templates STRUCTURE ===\n";
    $stmt = $pdo->query('DESCRIBE setting_branding_templates');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "Column: " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    echo "\n=== setting_branding_templates DATA ===\n";
    $stmt = $pdo->query('SELECT * FROM setting_branding_templates');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']}, Key: {$row['template_key']}, Name: {$row['template_name']}, Active: {$row['is_active']}\n";
    }
    
    // Check if there are any settings that might be related to sync mode
    echo "\n=== CHECKING FOR SYNC MODE SETTINGS ===\n";
    try {
        $stmt = $pdo->query("SELECT * FROM settings WHERE setting_name LIKE '%sync%' OR setting_name LIKE '%branding%'");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
    } catch (Exception $e2) {
        echo "No 'settings' table or no sync-related settings found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
