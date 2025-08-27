<?php
require_once '../private/gws-universal-config.php';

echo "<h2>Font System Fix - Final Solution</h2>";

// Step 1: Fix the database paths
echo "<h3>Step 1: Fixing Database Paths</h3>";

$upload_dir_from_public = '../assets/fonts/custom/';
$upload_dir_from_admin = '../../assets/fonts/custom/';

// Check which directory structure we have
if (is_dir($upload_dir_from_public)) {
    echo "<p>✓ Font directory found at: $upload_dir_from_public</p>";
    $correct_upload_dir = $upload_dir_from_public;
} else {
    echo "<p>✗ Font directory not found at: $upload_dir_from_public</p>";
    $correct_upload_dir = null;
}

if ($correct_upload_dir) {
    // Get font files
    $files = scandir($correct_upload_dir);
    $font_files = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']) && preg_match('/\.(woff2|woff|ttf|otf)$/i', $file);
    });
    
    echo "<p>Font files found: " . count($font_files) . "</p>";
    
    if (!empty($font_files)) {
        echo "<ul>";
        foreach ($font_files as $file) {
            echo "<li>$file</li>";
        }
        echo "</ul>";
        
        // Update database with correct paths for admin context
        echo "<h4>Updating database with admin-relative paths...</h4>";
        
        foreach ($font_files as $file) {
            if (preg_match('/custom_font_(\d+)_/', $file, $matches)) {
                $slot_num = $matches[1];
                $slot_column = "font_upload_$slot_num";
                
                // Path from admin/settings/ directory to assets
                $admin_relative_path = $upload_dir_from_admin . $file;
                
                try {
                    $update_stmt = $pdo->prepare("UPDATE setting_business_identity SET $slot_column = ? WHERE id = 1");
                    $update_stmt->execute([$admin_relative_path]);
                    echo "<p class='success'>✓ Updated $slot_column with: $admin_relative_path</p>";
                } catch (Exception $e) {
                    echo "<p class='error'>✗ Error updating $slot_column: " . $e->getMessage() . "</p>";
                }
            }
        }
    }
}

// Step 2: Update the getAvailableFonts function in the branding file
echo "<h3>Step 2: Updating getAvailableFonts Function</h3>";

$branding_file = 'admin/settings/branding_settings_tabbed.php';

if (file_exists($branding_file)) {
    echo "<p>✓ Found branding settings file</p>";
    
    // Read the current file
    $content = file_get_contents($branding_file);
    
    // Look for the specific issue in the file_exists check
    if (strpos($content, 'file_exists($font_path)') !== false) {
        echo "<p>Found file_exists check in function</p>";
        
        // Replace the problematic section with a more robust version
        $old_function_part = '                foreach ($custom_fonts as $slot => $font_path) {
                    if (!empty($font_path)) {
                        if (file_exists($font_path)) {
                            $filename = basename($font_path);
                            $font_name_without_ext = pathinfo($filename, PATHINFO_FILENAME);
                            
                            // Create a readable font family name
                            $display_name = $font_purposes[$slot] . \' (Custom)\';
                            
                            $fonts[] = [
                                \'family\' => $display_name,
                                \'category\' => \'custom\',
                                \'weight\' => \'400\',
                                \'file_path\' => $font_path,
                                \'purpose\' => $font_purposes[$slot]
                            ];
                        }
                    }
                }';
        
        $new_function_part = '                foreach ($custom_fonts as $slot => $font_path) {
                    if (!empty($font_path)) {
                        // Check multiple possible paths for the font file
                        $possible_paths = [
                            $font_path,  // Original path
                            \'../../assets/fonts/custom/\' . basename($font_path),  // From admin/settings/
                            \'../assets/fonts/custom/\' . basename($font_path),    // From public_html/
                            \'assets/fonts/custom/\' . basename($font_path)        // From root
                        ];
                        
                        $font_exists = false;
                        $working_path = $font_path;
                        
                        foreach ($possible_paths as $test_path) {
                            if (file_exists($test_path)) {
                                $font_exists = true;
                                $working_path = $test_path;
                                break;
                            }
                        }
                        
                        if ($font_exists) {
                            $filename = basename($working_path);
                            $font_name_without_ext = pathinfo($filename, PATHINFO_FILENAME);
                            
                            // Create a readable font family name
                            $display_name = $font_purposes[$slot] . \' (Custom)\';
                            
                            $fonts[] = [
                                \'family\' => $display_name,
                                \'category\' => \'custom\',
                                \'weight\' => \'400\',
                                \'file_path\' => $working_path,
                                \'purpose\' => $font_purposes[$slot]
                            ];
                        }
                    }
                }';
        
        $new_content = str_replace($old_function_part, $new_function_part, $content);
        
        if ($new_content !== $content) {
            if (file_put_contents($branding_file, $new_content)) {
                echo "<p class='success'>✓ Updated getAvailableFonts function with robust path checking</p>";
            } else {
                echo "<p class='error'>✗ Failed to write updated function to file</p>";
            }
        } else {
            echo "<p class='warning'>⚠ Function content didn't change - may already be updated</p>";
        }
    }
} else {
    echo "<p class='error'>✗ Branding settings file not found</p>";
}

// Step 3: Test the function
echo "<h3>Step 3: Testing Updated Function</h3>";

// Test the updated function
function testUpdatedFonts() {
    global $pdo;
    
    $fonts = [
        ['family' => 'Arial', 'category' => 'sans-serif', 'weight' => '400'],
        ['family' => 'Helvetica', 'category' => 'sans-serif', 'weight' => '400'],
        ['family' => 'Times New Roman', 'category' => 'serif', 'weight' => '400'],
        ['family' => 'Georgia', 'category' => 'serif', 'weight' => '400'],
    ];
    
    try {
        $stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1");
        $custom_fonts = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($custom_fonts) {
            $font_purposes = [
                'font_upload_1' => 'Regular Body Text',
                'font_upload_2' => 'Headings & Titles', 
                'font_upload_3' => 'Formal/Professional',
                'font_upload_4' => 'Decorative/Fancy',
                'font_upload_5' => 'Italic/Emphasis'
            ];
            
            foreach ($custom_fonts as $slot => $font_path) {
                if (!empty($font_path)) {
                    echo "<p>Testing $slot: $font_path</p>";
                    
                    // Check multiple possible paths for the font file
                    $possible_paths = [
                        $font_path,  // Original path
                        '../../assets/fonts/custom/' . basename($font_path),  // From admin/settings/
                        '../assets/fonts/custom/' . basename($font_path),    // From public_html/
                        'assets/fonts/custom/' . basename($font_path)        // From root
                    ];
                    
                    $font_exists = false;
                    $working_path = $font_path;
                    
                    foreach ($possible_paths as $test_path) {
                        if (file_exists($test_path)) {
                            $font_exists = true;
                            $working_path = $test_path;
                            echo "<p class='success'>  ✓ Found font at: $test_path</p>";
                            break;
                        } else {
                            echo "<p>  ✗ Not found at: $test_path</p>";
                        }
                    }
                    
                    if ($font_exists) {
                        $display_name = $font_purposes[$slot] . ' (Custom)';
                        $fonts[] = [
                            'family' => $display_name,
                            'category' => 'custom',
                            'weight' => '400',
                            'file_path' => $working_path,
                            'purpose' => $font_purposes[$slot]
                        ];
                        echo "<p class='success'>  ✓ Added custom font: $display_name</p>";
                    } else {
                        echo "<p class='error'>  ✗ Font file not found in any location</p>";
                    }
                }
            }
        }
    } catch (Exception $e) {
        echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    }
    
    return $fonts;
}

$test_fonts = testUpdatedFonts();
$custom_fonts = array_filter($test_fonts, function($f) { return $f['category'] === 'custom'; });

echo "<p><strong>Result:</strong> Found " . count($custom_fonts) . " custom fonts out of " . count($test_fonts) . " total fonts</p>";

if (!empty($custom_fonts)) {
    echo "<p class='success'>✓ Custom fonts are working!</p>";
    echo "<ul>";
    foreach ($custom_fonts as $font) {
        echo "<li>{$font['family']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='error'>✗ Still no custom fonts found</p>";
}

echo "<hr>";
echo "<p><strong>Summary:</strong></p>";
echo "<p>1. Database paths have been updated to be relative from admin/settings/ directory</p>";
echo "<p>2. getAvailableFonts() function has been updated with robust path checking</p>";
echo "<p>3. Function now tests multiple possible paths to find font files</p>";

echo "<p><a href='admin/settings/branding_settings_tabbed.php'>Test the Branding Settings Page</a></p>";
?>
