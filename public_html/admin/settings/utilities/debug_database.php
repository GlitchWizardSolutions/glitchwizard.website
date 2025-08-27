<?php
/**
 * Debug Database Settings
 * Quick test to check database connectivity and table existence
 */

session_start();
require_once __DIR__ . '/../../../private/gws-universal-config.php';
require_once __DIR__ . '/../../../private/classes/SettingsManager.php';

// Test database connection
try {
    echo "<h2>Database Connection Test</h2>";
    echo "PDO Connection: " . ($pdo ? "✅ Connected" : "❌ Failed") . "<br>";
    
    // Test table existence
    $tables = [
        'setting_business_identity',
        'setting_branding_colors', 
        'setting_contact_info',
        'setting_seo',
        'setting_system'
    ];
    
    echo "<h3>Table Existence Check</h3>";
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
            $count = $stmt->fetchColumn();
            echo "{$table}: ✅ Exists ({$count} rows)<br>";
        } catch (Exception $e) {
            echo "{$table}: ❌ Missing or error - " . $e->getMessage() . "<br>";
        }
    }
    
    // Test SettingsManager
    echo "<h3>SettingsManager Test</h3>";
    $settingsManager = new SettingsManager($pdo);
    echo "SettingsManager initialized: ✅<br>";
    
    // Test reading current business identity
    echo "<h3>Current Business Identity Data</h3>";
    try {
        $stmt = $pdo->query("SELECT * FROM setting_business_identity LIMIT 1");
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            echo "<pre>" . print_r($data, true) . "</pre>";
        } else {
            echo "No data found in setting_business_identity table<br>";
        }
    } catch (Exception $e) {
        echo "Error reading business identity: " . $e->getMessage() . "<br>";
    }
    
    // Test a simple update
    echo "<h3>Test Update</h3>";
    try {
        $test_data = ['author' => 'Test Author ' . date('H:i:s')];
        $result = $settingsManager->updateBusinessIdentity($test_data, 'debug_test');
        echo "Update test: " . ($result ? "✅ Success" : "❌ Failed") . "<br>";
    } catch (Exception $e) {
        echo "Update test error: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage();
}
?>
