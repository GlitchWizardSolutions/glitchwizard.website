<!DOCTYPE html>
<html>
<head>
    <title>Font Upload Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
    </style>
</head>
<body>
    <h1>Font Upload Database Setup</h1>
    
    <?php
    require_once '../private/gws-universal-config.php';
    
    echo "<p class='info'>Starting font upload columns setup...</p>";
    
    try {
        // Check if columns exist and add them if they don't
        $columns_to_add = [
            'font_upload_1' => 'VARCHAR(255) DEFAULT \'\'',
            'font_upload_2' => 'VARCHAR(255) DEFAULT \'\'',
            'font_upload_3' => 'VARCHAR(255) DEFAULT \'\'',
            'font_upload_4' => 'VARCHAR(255) DEFAULT \'\'',
            'font_upload_5' => 'VARCHAR(255) DEFAULT \'\''
        ];
        
        foreach ($columns_to_add as $column_name => $column_definition) {
            // Check if column exists
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE table_name = 'setting_business_identity' 
                AND column_name = ? 
                AND table_schema = DATABASE()
            ");
            $stmt->execute([$column_name]);
            $column_exists = $stmt->fetchColumn() > 0;
            
            if (!$column_exists) {
                $sql = "ALTER TABLE setting_business_identity ADD COLUMN $column_name $column_definition";
                $pdo->exec($sql);
                echo "<p class='success'>Added column: $column_name</p>";
            } else {
                echo "<p class='info'>Column already exists: $column_name</p>";
            }
        }
        
        echo "<p class='success'><strong>Font upload columns setup completed successfully!</strong></p>";
        
    } catch (Exception $e) {
        echo "<p class='error'>Error setting up font upload columns: " . $e->getMessage() . "</p>";
        echo "<p class='error'>Stack trace: " . htmlspecialchars($e->getTraceAsString()) . "</p>";
    }
    ?>
    
</body>
</html>
