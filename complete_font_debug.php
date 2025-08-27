<?php
// Comprehensive Font System Debug
require_once '../private/gws-universal-config.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Complete Font System Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug { background: #f5f5f5; padding: 10px; margin: 10px 0; border-left: 4px solid #007cba; }
        .error { background: #ffe6e6; border-left-color: #d63638; }
        .success { background: #e6ffe6; border-left-color: #00a32a; }
        .warning { background: #fff3cd; border-left-color: #ffc107; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .code { font-family: monospace; background: #f1f3f4; padding: 2px 4px; border-radius: 3px; }
    </style>
</head>
<body>";

echo "<h1>Complete Font System Debug Analysis</h1>";
echo "<p><strong>Debug Time:</strong> " . date('Y-m-d H:i:s') . "</p>";

// Step 1: Database Connection
echo "<div class='debug'><h3>Step 1: Database Connection</h3>";
try {
    $test_query = $pdo->query("SELECT 1");
    echo "<p class='success'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}
echo "</div>";

// Step 2: Database Structure Analysis
echo "<div class='debug'><h3>Step 2: Database Structure Analysis</h3>";
try {
    // Check table exists
    $table_check = $pdo->query("SHOW TABLES LIKE 'setting_business_identity'");
    if ($table_check->rowCount() > 0) {
        echo "<p class='success'>✓ Table 'setting_business_identity' exists</p>";
        
        // Check all columns
        $columns_check = $pdo->query("SHOW COLUMNS FROM setting_business_identity");
        $all_columns = $columns_check->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p><strong>All table columns:</strong></p><ul>";
        foreach ($all_columns as $col) {
            echo "<li>{$col['Field']} ({$col['Type']})</li>";
        }
        echo "</ul>";
        
        // Check font columns specifically
        $font_columns = array_filter($all_columns, function($col) {
            return strpos($col['Field'], 'font_upload_') === 0;
        });
        
        if (count($font_columns) >= 5) {
            echo "<p class='success'>✓ Font upload columns exist (" . count($font_columns) . " found)</p>";
        } else {
            echo "<p class='error'>✗ Missing font upload columns. Found: " . count($font_columns) . "</p>";
        }
    } else {
        echo "<p class='error'>✗ Table 'setting_business_identity' does not exist</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error checking database structure: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Step 3: Database Content Analysis
echo "<div class='debug'><h3>Step 3: Database Content Analysis</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM setting_business_identity WHERE id = 1");
    $business_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($business_data) {
        echo "<p class='success'>✓ Business identity record found (ID: 1)</p>";
        
        // Check font-related fields
        $font_fields = [
            'font_upload_1', 'font_upload_2', 'font_upload_3', 'font_upload_4', 'font_upload_5',
            'primary_font', 'heading_font', 'body_font'
        ];
        
        echo "<p><strong>Font-related database fields:</strong></p><ul>";
        foreach ($font_fields as $field) {
            $value = $business_data[$field] ?? 'NOT FOUND';
            $status = !empty($value) && $value !== 'NOT FOUND' ? "HAS VALUE" : "EMPTY/MISSING";
            
            if (strpos($field, 'font_upload_') === 0 && !empty($value)) {
                $file_exists = file_exists($value) ? " (FILE EXISTS)" : " (FILE NOT FOUND)";
                echo "<li><strong>$field:</strong> $status - $value$file_exists</li>";
            } else {
                echo "<li><strong>$field:</strong> $status" . ($value !== 'NOT FOUND' && !empty($value) ? " - $value" : "") . "</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p class='error'>✗ No business identity record found with id=1</p>";
        
        // Check if any records exist
        $count_stmt = $pdo->query("SELECT COUNT(*) as count FROM setting_business_identity");
        $count = $count_stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>Total records in table: $count</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error reading database: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Step 4: File System Analysis
echo "<div class='debug'><h3>Step 4: File System Analysis</h3>";
$upload_dir = '../assets/fonts/custom/';
$full_upload_path = realpath($upload_dir);

echo "<p><strong>Upload Directory Analysis:</strong></p>";
echo "<p>Relative path: <span class='code'>$upload_dir</span></p>";
echo "<p>Full path: <span class='code'>$full_upload_path</span></p>";

if (is_dir($upload_dir)) {
    echo "<p class='success'>✓ Upload directory exists</p>";
    
    // Check permissions
    if (is_writable($upload_dir)) {
        echo "<p class='success'>✓ Directory is writable</p>";
    } else {
        echo "<p class='error'>✗ Directory is not writable</p>";
    }
    
    // List all files
    $files = scandir($upload_dir);
    $all_files = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']);
    });
    
    $font_files = array_filter($all_files, function($file) {
        return preg_match('/\.(woff2|woff|ttf|otf)$/i', $file);
    });
    
    echo "<p><strong>All files in directory:</strong> " . count($all_files) . "</p>";
    if (!empty($all_files)) {
        echo "<ul>";
        foreach ($all_files as $file) {
            $is_font = preg_match('/\.(woff2|woff|ttf|otf)$/i', $file);
            $file_size = filesize($upload_dir . $file);
            echo "<li>$file (" . number_format($file_size) . " bytes)" . ($is_font ? " <strong>[FONT FILE]</strong>" : "") . "</li>";
        }
        echo "</ul>";
    }
    
    echo "<p><strong>Font files found:</strong> " . count($font_files) . "</p>";
    if (!empty($font_files)) {
        echo "<ul>";
        foreach ($font_files as $file) {
            echo "<li class='success'>$file</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p class='error'>✗ Upload directory does not exist: $upload_dir</p>";
}
echo "</div>";

// Step 5: Include Path Analysis
echo "<div class='debug'><h3>Step 5: Include Path Analysis</h3>";
echo "<p><strong>Current working directory:</strong> " . getcwd() . "</p>";
echo "<p><strong>Script location:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Config file location:</strong> " . realpath('../private/gws-universal-config.php') . "</p>";

// Test different path resolutions
$test_paths = [
    '../assets/fonts/custom/',
    './assets/fonts/custom/',
    'assets/fonts/custom/',
    realpath('../assets/fonts/custom/') . '/'
];

echo "<p><strong>Path resolution tests:</strong></p><ul>";
foreach ($test_paths as $test_path) {
    $resolved = realpath($test_path);
    $exists = is_dir($test_path);
    echo "<li>Path: <span class='code'>$test_path</span><br>";
    echo "Resolved: " . ($resolved ?: 'FAILED') . "<br>";
    echo "Exists: " . ($exists ? 'YES' : 'NO') . "</li>";
}
echo "</ul>";
echo "</div>";

// Step 6: Function Definition Check
echo "<div class='debug'><h3>Step 6: Function Definition Check</h3>";

// Check if getAvailableFonts function is defined
if (function_exists('getAvailableFonts')) {
    echo "<p class='success'>✓ getAvailableFonts() function is defined</p>";
} else {
    echo "<p class='error'>✗ getAvailableFonts() function is NOT defined</p>";
    echo "<p>Need to check if it's included properly...</p>";
}

// Let's check what functions are available
$defined_functions = get_defined_functions()['user'];
$font_related_functions = array_filter($defined_functions, function($func) {
    return strpos(strtolower($func), 'font') !== false;
});

echo "<p><strong>Font-related functions found:</strong></p>";
if (!empty($font_related_functions)) {
    echo "<ul>";
    foreach ($font_related_functions as $func) {
        echo "<li>$func()</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No font-related functions found</p>";
}
echo "</div>";

// Step 7: Manual Function Test
echo "<div class='debug'><h3>Step 7: Manual Function Implementation Test</h3>";

// Define the function manually to test
function testGetAvailableFonts() {
    global $pdo;
    
    echo "<p><strong>Testing getAvailableFonts() manually...</strong></p>";
    
    // Start with system fonts
    $fonts = [
        ['family' => 'Arial', 'category' => 'sans-serif', 'weight' => '400'],
        ['family' => 'Helvetica', 'category' => 'sans-serif', 'weight' => '400'],
        ['family' => 'Times New Roman', 'category' => 'serif', 'weight' => '400'],
        ['family' => 'Georgia', 'category' => 'serif', 'weight' => '400'],
        ['family' => 'Courier New', 'category' => 'monospace', 'weight' => '400'],
        ['family' => 'Verdana', 'category' => 'sans-serif', 'weight' => '400'],
        ['family' => 'Trebuchet MS', 'category' => 'sans-serif', 'weight' => '400'],
        ['family' => 'Palatino', 'category' => 'serif', 'weight' => '400'],
        ['family' => 'Lucida Sans', 'category' => 'sans-serif', 'weight' => '400'],
        ['family' => 'Impact', 'category' => 'sans-serif', 'weight' => '700']
    ];
    
    echo "<p>System fonts loaded: " . count($fonts) . "</p>";
    
    // Add uploaded custom fonts
    try {
        echo "<p>Querying database for custom fonts...</p>";
        $stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1");
        $custom_fonts = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($custom_fonts) {
            echo "<p class='success'>✓ Custom fonts data retrieved from database</p>";
            
            $font_purposes = [
                'font_upload_1' => 'Regular Body Text',
                'font_upload_2' => 'Headings & Titles', 
                'font_upload_3' => 'Formal/Professional',
                'font_upload_4' => 'Decorative/Fancy',
                'font_upload_5' => 'Italic/Emphasis'
            ];
            
            $custom_count = 0;
            foreach ($custom_fonts as $slot => $font_path) {
                echo "<p>Processing $slot: <span class='code'>$font_path</span></p>";
                
                if (!empty($font_path)) {
                    echo "<p>- Path is not empty</p>";
                    
                    if (file_exists($font_path)) {
                        echo "<p class='success'>- File exists at path!</p>";
                        
                        $filename = basename($font_path);
                        $font_name_without_ext = pathinfo($filename, PATHINFO_FILENAME);
                        $display_name = $font_purposes[$slot] . ' (Custom)';
                        
                        $fonts[] = [
                            'family' => $display_name,
                            'category' => 'custom',
                            'weight' => '400',
                            'file_path' => $font_path,
                            'purpose' => $font_purposes[$slot]
                        ];
                        
                        $custom_count++;
                        echo "<p class='success'>- Added custom font: <strong>$display_name</strong></p>";
                    } else {
                        echo "<p class='error'>- File does NOT exist at path: $font_path</p>";
                        
                        // Try alternative paths
                        $alternative_paths = [
                            './assets/fonts/custom/' . basename($font_path),
                            '../assets/fonts/custom/' . basename($font_path),
                            'assets/fonts/custom/' . basename($font_path)
                        ];
                        
                        echo "<p>Trying alternative paths:</p><ul>";
                        foreach ($alternative_paths as $alt_path) {
                            $alt_exists = file_exists($alt_path);
                            echo "<li>$alt_path: " . ($alt_exists ? "EXISTS" : "NOT FOUND") . "</li>";
                        }
                        echo "</ul>";
                    }
                } else {
                    echo "<p>- Path is empty, skipping</p>";
                }
            }
            
            echo "<p><strong>Custom fonts successfully added: $custom_count</strong></p>";
        } else {
            echo "<p class='error'>✗ No custom fonts data found in database</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error loading custom fonts: " . $e->getMessage() . "</p>";
        echo "<p>Stack trace:</p><pre>" . $e->getTraceAsString() . "</pre>";
    }
    
    echo "<p><strong>Total fonts available: " . count($fonts) . "</strong></p>";
    return $fonts;
}

$test_fonts = testGetAvailableFonts();

echo "<h4>Final Font List:</h4>";
if (!empty($test_fonts)) {
    echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'><th>Font Family</th><th>Category</th><th>Weight</th><th>Type</th><th>Notes</th></tr>";
    
    foreach ($test_fonts as $font) {
        $is_custom = $font['category'] === 'custom';
        $row_class = $is_custom ? "style='background: #e6ffe6;'" : "";
        $type = $is_custom ? "CUSTOM" : "SYSTEM";
        $notes = $is_custom ? "File: " . ($font['file_path'] ?? 'N/A') : "";
        
        echo "<tr $row_class>";
        echo "<td><strong>{$font['family']}</strong></td>";
        echo "<td>{$font['category']}</td>";
        echo "<td>{$font['weight']}</td>";
        echo "<td>$type</td>";
        echo "<td>$notes</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>No fonts found!</p>";
}
echo "</div>";

// Step 8: Check the actual branding file for function inclusion
echo "<div class='debug'><h3>Step 8: Branding File Function Check</h3>";
$branding_file = '../admin/settings/branding_settings_tabbed.php';
$branding_full_path = realpath($branding_file);

echo "<p><strong>Branding file location:</strong> $branding_full_path</p>";

if (file_exists($branding_file)) {
    echo "<p class='success'>✓ Branding settings file exists</p>";
    
    // Read and analyze the file
    $branding_content = file_get_contents($branding_file);
    
    // Check for function definition
    if (strpos($branding_content, 'function getAvailableFonts') !== false) {
        echo "<p class='success'>✓ getAvailableFonts() function is defined in branding file</p>";
    } else {
        echo "<p class='error'>✗ getAvailableFonts() function NOT found in branding file</p>";
    }
    
    // Check for function calls
    $function_calls = preg_match_all('/getAvailableFonts\s*\(\s*\)/', $branding_content, $matches);
    echo "<p>Function calls found: $function_calls</p>";
    
    // Check for dropdown generation
    if (strpos($branding_content, 'primary_font') !== false) {
        echo "<p class='success'>✓ Primary font dropdown code found</p>";
    }
    if (strpos($branding_content, 'heading_font') !== false) {
        echo "<p class='success'>✓ Heading font dropdown code found</p>";
    }
    if (strpos($branding_content, 'body_font') !== false) {
        echo "<p class='success'>✓ Body font dropdown code found</p>";
    }
    
} else {
    echo "<p class='error'>✗ Branding settings file does NOT exist</p>";
}
echo "</div>";

// Step 9: CSS Generation Check
echo "<div class='debug'><h3>Step 9: CSS Generation Check</h3>";
$css_file = '../assets/css/custom-fonts.css';
$css_full_path = realpath($css_file);

echo "<p><strong>Custom fonts CSS file:</strong> $css_file</p>";
echo "<p><strong>Full path:</strong> $css_full_path</p>";

if (file_exists($css_file)) {
    echo "<p class='success'>✓ Custom fonts CSS file exists</p>";
    
    $css_content = file_get_contents($css_file);
    if (!empty($css_content)) {
        echo "<p class='success'>✓ CSS file has content (" . strlen($css_content) . " bytes)</p>";
        echo "<p><strong>CSS Content Preview:</strong></p>";
        echo "<pre>" . htmlspecialchars(substr($css_content, 0, 1000)) . (strlen($css_content) > 1000 ? "\n... (truncated)" : "") . "</pre>";
    } else {
        echo "<p class='warning'>⚠ CSS file exists but is empty</p>";
    }
} else {
    echo "<p class='error'>✗ Custom fonts CSS file does NOT exist</p>";
}
echo "</div>";

// Step 10: Generate Recommendations
echo "<div class='debug'><h3>Step 10: Issue Analysis & Recommendations</h3>";

$issues_found = [];
$recommendations = [];

// Analyze issues
if (!function_exists('getAvailableFonts')) {
    $issues_found[] = "getAvailableFonts() function is not available";
    $recommendations[] = "Check if the function is properly defined in the branding settings file";
}

if (empty($test_fonts) || count(array_filter($test_fonts, function($f) { return $f['category'] === 'custom'; })) === 0) {
    $issues_found[] = "No custom fonts are being loaded";
    $recommendations[] = "Check database font_upload_* columns for correct file paths";
    $recommendations[] = "Verify that uploaded font files exist at the specified paths";
}

if (!file_exists($css_file)) {
    $issues_found[] = "Custom fonts CSS file is missing";
    $recommendations[] = "Ensure generateCustomFontsCSS() function is working";
}

echo "<p><strong>Issues Found:</strong></p>";
if (!empty($issues_found)) {
    echo "<ul>";
    foreach ($issues_found as $issue) {
        echo "<li class='error'>$issue</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='success'>No major issues detected!</p>";
}

echo "<p><strong>Recommendations:</strong></p>";
if (!empty($recommendations)) {
    echo "<ol>";
    foreach ($recommendations as $rec) {
        echo "<li>$rec</li>";
    }
    echo "</ol>";
} else {
    echo "<p class='success'>System appears to be functioning correctly!</p>";
}

echo "</div>";

echo "<hr>";
echo "<p><strong>Debug completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><a href='branding_settings_tabbed.php'>Return to Branding Settings</a></p>";

echo "</body></html>";
?>
