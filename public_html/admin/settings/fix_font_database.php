<!DOCTYPE html>
<html>
<head>
    <title>Font Upload Database Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .info { color: #0066cc; background: #cce7ff; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .btn { display: inline-block; padding: 8px 16px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; margin: 5px; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #1e7e34; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Font Upload Database Fix</h1>
        
    <?php
    require_once '../../../private/gws-universal-config.php';        // Check if we should run the fix
        $run_fix = isset($_GET['run_fix']) && $_GET['run_fix'] === 'yes';
        
        if (!$run_fix) {
            // Show current table structure first
            echo "<div class='info'><strong>Checking current database structure...</strong></div>";
            
            try {
                $stmt = $pdo->query("DESCRIBE setting_business_identity");
                $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo "<h3>Current Columns in setting_business_identity:</h3>";
                echo "<table class='table'>";
                echo "<tr><th>Column Name</th><th>Type</th><th>Null</th><th>Default</th></tr>";
                
                $font_columns_exist = [];
                foreach ($columns as $column) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
                    echo "</tr>";
                    
                    // Check for font upload columns
                    if (preg_match('/^font_upload_[1-5]$/', $column['Field'])) {
                        $font_columns_exist[] = $column['Field'];
                    }
                }
                echo "</table>";
                
                // Check which font columns are missing
                $required_columns = ['font_upload_1', 'font_upload_2', 'font_upload_3', 'font_upload_4', 'font_upload_5'];
                $missing_columns = array_diff($required_columns, $font_columns_exist);
                
                if (empty($missing_columns)) {
                    echo "<div class='success'><strong>✅ All font upload columns exist!</strong> The database structure is correct.</div>";
                    echo "<div class='info'>If you're still getting errors, there might be a different issue. Check the error logs or try refreshing the branding settings page.</div>";
                } else {
                    echo "<div class='warning'><strong>⚠️ Missing font upload columns:</strong> " . implode(', ', $missing_columns) . "</div>";
                    echo "<div class='info'>Click the button below to add the missing columns:</div>";
                    echo "<a href='?run_fix=yes' class='btn btn-success'>🔧 Fix Database Structure</a>";
                }
                
            } catch (Exception $e) {
                echo "<div class='error'><strong>Error checking database:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            
        } else {
            // Run the fix
            echo "<div class='info'><strong>Running database fix...</strong></div>";
            
            try {
                // Check which columns need to be added
                $stmt = $pdo->query("DESCRIBE setting_business_identity");
                $existing_columns = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $existing_columns[] = $row['Field'];
                }
                
                $columns_to_add = [
                    'font_upload_1' => 'VARCHAR(255) DEFAULT NULL',
                    'font_upload_2' => 'VARCHAR(255) DEFAULT NULL',
                    'font_upload_3' => 'VARCHAR(255) DEFAULT NULL',
                    'font_upload_4' => 'VARCHAR(255) DEFAULT NULL',
                    'font_upload_5' => 'VARCHAR(255) DEFAULT NULL'
                ];
                
                $added_count = 0;
                $already_exists = 0;
                
                foreach ($columns_to_add as $column_name => $column_definition) {
                    if (!in_array($column_name, $existing_columns)) {
                        $sql = "ALTER TABLE setting_business_identity ADD COLUMN `$column_name` $column_definition";
                        $pdo->exec($sql);
                        echo "<div class='success'>✅ Added column: <strong>$column_name</strong></div>";
                        $added_count++;
                    } else {
                        echo "<div class='info'>ℹ️ Column already exists: <strong>$column_name</strong></div>";
                        $already_exists++;
                    }
                }
                
                if ($added_count > 0) {
                    echo "<div class='success'><strong>🎉 Database fix completed!</strong> Added $added_count new columns.</div>";
                } else {
                    echo "<div class='warning'><strong>ℹ️ No changes needed.</strong> All font upload columns already exist.</div>";
                }
                
                // Verify the fix worked
                echo "<div class='info'><strong>Verifying the fix...</strong></div>";
                $verify_stmt = $pdo->query("SELECT font_upload_1, font_upload_2, font_upload_3, font_upload_4, font_upload_5 FROM setting_business_identity WHERE id = 1 LIMIT 1");
                $result = $verify_stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result !== false) {
                    echo "<div class='success'><strong>✅ Verification successful!</strong> Font upload columns are working correctly.</div>";
                    echo "<div class='info'>You can now go back to the <a href='branding_settings_tabbed.php#fonts'>Typography tab</a> and upload your fonts.</div>";
                } else {
                    echo "<div class='warning'><strong>⚠️ Note:</strong> No data found in setting_business_identity table. This might be normal if no business info has been saved yet.</div>";
                }
                
                echo "<br><a href='branding_settings_tabbed.php#fonts' class='btn btn-success'>📝 Go to Typography Tab</a>";
                echo "<a href='?run_fix=no' class='btn'>🔍 Check Database Again</a>";
                
            } catch (Exception $e) {
                echo "<div class='error'><strong>❌ Error during fix:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
                echo "<div class='info'>Please check your database connection and permissions.</div>";
            }
        }
        ?>
        
        <hr style="margin: 30px 0;">
        <div class="info">
            <strong>🔒 Security Note:</strong> This file should be deleted after fixing the database structure.
        </div>
        
    </div>
</body>
</html>
