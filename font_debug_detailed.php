<!DOCTYPE html>
<html>
<head>
    <title>Font Loading Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug { background: #f5f5f5; padding: 10px; margin: 10px 0; border-left: 4px solid #007cba; }
        .error { background: #ffe6e6; border-left-color: #d63638; }
        .success { background: #e6ffe6; border-left-color: #00a32a; }
    </style>
</head>
<body>
    <h1>Font Loading Debug</h1>
    
    <?php
    // Include the required files
    require_once '../private/gws-universal-config.php';
    
    echo "<div class='debug'><h3>Step 1: Check Database Connection</h3>";
    try {
        $test_query = $pdo->query("SELECT 1");
        echo "<p class='success'>✓ Database connection successful</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Database connection failed: " . $e->getMessage() . "</p>";
        exit;
    }
    echo "</div>";
    
    echo "<div class='debug'><h3>Step 2: Check Database Structure</h3>";
    try {
        $columns_check = $pdo->query("SHOW COLUMNS FROM setting_business_identity LIKE 'font_upload_%'");
        $font_columns = $columns_check->fetchAll(PDO::FETCH_COLUMN);
        if (count($font_columns) >= 5) {
            echo "<p class='success'>✓ Font upload columns exist: " . implode(', ', $font_columns) . "</p>";
        } else {
            echo "<p class='error'>✗ Missing font upload columns. Found: " . implode(', ', $font_columns) . "</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error checking database structure: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    echo "<div class='debug'><h3>Step 3: Check Database Content</h3>";
    try {
        $stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1");
        $font_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($font_data) {
            echo "<p class='success'>✓ Database record found</p>";
            echo "<ul>";
            foreach ($font_data as $slot => $path) {
                $status = !empty($path) ? "HAS VALUE: $path" : "EMPTY";
                $file_exists = !empty($path) && file_exists($path) ? " (FILE EXISTS)" : (!empty($path) ? " (FILE NOT FOUND)" : "");
                echo "<li><strong>$slot:</strong> $status$file_exists</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='error'>✗ No database record found with id=1</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error reading database: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    echo "<div class='debug'><h3>Step 4: Check File System</h3>";
    $upload_dir = '../assets/fonts/custom/';
    if (is_dir($upload_dir)) {
        echo "<p class='success'>✓ Upload directory exists: $upload_dir</p>";
        $files = scandir($upload_dir);
        $font_files = array_filter($files, function($file) {
            return !in_array($file, ['.', '..']);
        });
        
        if (!empty($font_files)) {
            echo "<p class='success'>✓ Font files found:</p><ul>";
            foreach ($font_files as $file) {
                echo "<li>$file</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='error'>✗ No font files in directory</p>";
        }
    } else {
        echo "<p class='error'>✗ Upload directory does not exist: $upload_dir</p>";
    }
    echo "</div>";
    
    echo "<div class='debug'><h3>Step 5: Test getAvailableFonts() Function</h3>";
    
    // Copy the function definition here to test independently
    function testGetAvailableFonts() {
        global $pdo;
        
        // Start with system fonts
        $fonts = [
            ['family' => 'Arial', 'category' => 'sans-serif', 'weight' => '400'],
            ['family' => 'Helvetica', 'category' => 'sans-serif', 'weight' => '400'],
            ['family' => 'Times New Roman', 'category' => 'serif', 'weight' => '400'],
            ['family' => 'Georgia', 'category' => 'serif', 'weight' => '400'],
        ];
        
        echo "<p>Starting with " . count($fonts) . " system fonts</p>";
        
        // Add uploaded custom fonts
        try {
            $stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1");
            $custom_fonts = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<p>Database query executed successfully</p>";
            
            if ($custom_fonts) {
                echo "<p class='success'>✓ Custom fonts data retrieved from database</p>";
                
                $font_purposes = [
                    'font_upload_1' => 'Regular Body Text',
                    'font_upload_2' => 'Headings & Titles', 
                    'font_upload_3' => 'Formal/Professional',
                    'font_upload_4' => 'Decorative/Fancy',
                    'font_upload_5' => 'Italic/Emphasis'
                ];
                
                foreach ($custom_fonts as $slot => $font_path) {
                    echo "<p>Processing $slot: '$font_path'</p>";
                    
                    if (!empty($font_path)) {
                        echo "<p>- Path is not empty</p>";
                        
                        if (file_exists($font_path)) {
                            echo "<p class='success'>- File exists!</p>";
                            
                            $filename = basename($font_path);
                            $display_name = $font_purposes[$slot] . ' (Custom)';
                            
                            $fonts[] = [
                                'family' => $display_name,
                                'category' => 'custom',
                                'weight' => '400',
                                'file_path' => $font_path,
                                'purpose' => $font_purposes[$slot]
                            ];
                            
                            echo "<p class='success'>- Added font: $display_name</p>";
                        } else {
                            echo "<p class='error'>- File does not exist at path: $font_path</p>";
                        }
                    } else {
                        echo "<p>- Path is empty, skipping</p>";
                    }
                }
            } else {
                echo "<p class='error'>✗ No custom fonts data found in database</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error loading custom fonts: " . $e->getMessage() . "</p>";
        }
        
        echo "<p>Final font count: " . count($fonts) . "</p>";
        return $fonts;
    }
    
    $available_fonts = testGetAvailableFonts();
    
    echo "<h4>Available Fonts:</h4><ul>";
    foreach ($available_fonts as $font) {
        $custom_indicator = $font['category'] === 'custom' ? ' <strong>(CUSTOM)</strong>' : '';
        echo "<li>{$font['family']} ({$font['category']}){$custom_indicator}</li>";
    }
    echo "</ul>";
    echo "</div>";
    
    echo "<div class='debug'><h3>Step 6: Working Directory Check</h3>";
    echo "<p>Current working directory: " . getcwd() . "</p>";
    echo "<p>Script location: " . __FILE__ . "</p>";
    echo "<p>Checking relative path resolution:</p>";
    
    $test_paths = [
        '../assets/fonts/custom/',
        './assets/fonts/custom/',
        'assets/fonts/custom/',
    ];
    
    foreach ($test_paths as $test_path) {
        $resolved = realpath($test_path);
        $exists = is_dir($test_path);
        echo "<p>Path '$test_path' -> Resolved: " . ($resolved ?: 'FAILED') . " | Exists: " . ($exists ? 'YES' : 'NO') . "</p>";
    }
    echo "</div>";
    ?>
    
</body>
</html>
