<?php
// Quick test script to validate app configuration system
require_once 'app_config_mapping.php';
require_once 'app_config.php';

echo "<h3>Testing Application Configuration System</h3>\n";

// Test 1: Check if mapping exists
echo "<h4>Test 1: Mapping Validation</h4>\n";
if (isset($app_config_mapping['shop_system'])) {
    echo "✅ Shop system mapping found<br>\n";
    $config = $app_config_mapping['shop_system'];
    echo "📁 Config file: " . $config['config_file'] . "<br>\n";
    echo "📋 Sections: " . implode(', ', array_keys($config['sections'])) . "<br>\n";
} else {
    echo "❌ Shop system mapping not found<br>\n";
}

// Test 2: Check if config file exists
echo "<h4>Test 2: Config File Validation</h4>\n";
$config_file = __DIR__ . '/../../shop_system/config.php';
if (file_exists($config_file)) {
    echo "✅ Config file exists: {$config_file}<br>\n";
    echo "📊 File size: " . filesize($config_file) . " bytes<br>\n";
} else {
    echo "❌ Config file not found: {$config_file}<br>\n";
}

// Test 3: Parse config file
echo "<h4>Test 3: Config Parsing</h4>\n";
try {
    $parsed_config = parseConfigFile($config_file);
    echo "✅ Config file parsed successfully<br>\n";
    echo "🔧 Found " . count($parsed_config) . " configuration settings<br>\n";
    
    // Show first few settings
    $count = 0;
    foreach ($parsed_config as $key => $value) {
        if ($count++ < 5) {
            $display_value = is_bool($value) ? ($value ? 'true' : 'false') : (string)$value;
            echo "&nbsp;&nbsp;• {$key} = {$display_value}<br>\n";
        }
    }
    if (count($parsed_config) > 5) {
        echo "&nbsp;&nbsp;... and " . (count($parsed_config) - 5) . " more<br>\n";
    }
} catch (Exception $e) {
    echo "❌ Error parsing config: " . $e->getMessage() . "<br>\n";
}

// Test 4: Check if sections match
echo "<h4>Test 4: Section Mapping Validation</h4>\n";
if (isset($parsed_config) && isset($app_config_mapping['shop_system'])) {
    $mapping = $app_config_mapping['shop_system'];
    $mapped_fields = [];
    
    foreach ($mapping['sections'] as $section_name => $section) {
        foreach ($section['fields'] as $field_name => $field_info) {
            $mapped_fields[] = $field_name;
        }
    }
    
    $config_keys = array_keys($parsed_config);
    $missing_mappings = array_diff($config_keys, $mapped_fields);
    $extra_mappings = array_diff($mapped_fields, $config_keys);
    
    echo "📊 Config has " . count($config_keys) . " settings<br>\n";
    echo "📊 Mapping covers " . count($mapped_fields) . " settings<br>\n";
    
    if (empty($missing_mappings)) {
        echo "✅ All config settings have mappings<br>\n";
    } else {
        echo "⚠️ Settings without mappings: " . implode(', ', array_slice($missing_mappings, 0, 5));
        if (count($missing_mappings) > 5) echo " (+" . (count($missing_mappings) - 5) . " more)";
        echo "<br>\n";
    }
    
    if (empty($extra_mappings)) {
        echo "✅ No extra mappings found<br>\n";
    } else {
        echo "ℹ️ Mapped but not in config: " . implode(', ', array_slice($extra_mappings, 0, 3));
        if (count($extra_mappings) > 3) echo " (+" . (count($extra_mappings) - 3) . " more)";
        echo "<br>\n";
    }
}

echo "<h4>Summary</h4>\n";
echo "🎯 Application configuration system is " . (
    isset($app_config_mapping['shop_system']) && 
    file_exists($config_file) && 
    isset($parsed_config) ? 
    "<strong>WORKING</strong>" : 
    "<strong>NEEDS ATTENTION</strong>"
) . "<br>\n";

echo "<br><a href='settings_dash.php'>← Back to Settings Dashboard</a>\n";
echo "<br><a href='app_config.php?app=shop_system'>→ Edit Shop Configuration</a>\n";
?>
