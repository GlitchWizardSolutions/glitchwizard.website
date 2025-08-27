<!DOCTYPE html>
<html>
<head>
    <title>Final Theme Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        form { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>Final Theme Activation Test</h1>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<h2>POST Request Received</h2>";
        
        if (isset($_POST['select_theme']) && isset($_POST['template_key'])) {
            // Use the same sanitization method as the updated files
            $template_key = isset($_POST['template_key']) ? trim(strip_tags($_POST['template_key'])) : '';
            $allowed_keys = ['default', 'subtle', 'bold', 'casual', 'high_contrast', 'template_1', 'template_2', 'template_3'];
            
            echo "<p class='info'>Template key received: '$template_key'</p>";
            echo "<p class='info'>Is allowed: " . (in_array($template_key, $allowed_keys) ? 'Yes' : 'No') . "</p>";
            
            if (in_array($template_key, $allowed_keys)) {
                try {
                    require_once '../../private/gws-universal-config.php';
                    require_once '../assets/includes/branding-functions.php';
                    
                    echo "<p class='info'>Attempting to activate theme '$template_key'...</p>";
                    
                    if (setActiveBrandingTemplate($template_key)) {
                        echo "<p class='success'>✅ Theme '$template_key' has been activated successfully!</p>";
                    } else {
                        echo "<p class='error'>❌ Failed to activate theme '$template_key'. Please try again.</p>";
                    }
                    
                    // Show current active theme
                    $active = getActiveBrandingTemplate();
                    if ($active) {
                        echo "<p class='info'>Currently active theme: {$active['template_key']} - {$active['template_name']}</p>";
                    }
                    
                } catch (Exception $e) {
                    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p class='error'>Invalid template key: '$template_key'</p>";
            }
        }
    }
    ?>
    
    <h2>Test Theme Activation</h2>
    
    <form method="POST">
        <h3>Activate Default Theme</h3>
        <input type="hidden" name="template_key" value="default">
        <button type="submit" name="select_theme">Activate Default Theme</button>
    </form>
    
    <form method="POST">
        <h3>Activate Subtle Theme</h3>
        <input type="hidden" name="template_key" value="subtle">
        <button type="submit" name="select_theme">Activate Subtle Theme</button>
    </form>
    
    <form method="POST">
        <h3>Activate Bold Theme</h3>
        <input type="hidden" name="template_key" value="bold">
        <button type="submit" name="select_theme">Activate Bold Theme</button>
    </form>
    
    <h2>Current Database State</h2>
    <?php
    try {
        require_once '../../private/gws-universal-config.php';
        
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Template Key</th><th>Template Name</th><th>Active</th></tr>";
        
        $stmt = $pdo->query('SELECT template_key, template_name, is_active FROM setting_branding_templates ORDER BY template_key');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $active_indicator = $row['is_active'] ? '✅ YES' : 'No';
            echo "<tr><td>{$row['template_key']}</td><td>{$row['template_name']}</td><td>{$active_indicator}</td></tr>";
        }
        
        echo "</table>";
        
    } catch (Exception $e) {
        echo "<p class='error'>Database error: " . $e->getMessage() . "</p>";
    }
    ?>
</body>
</html>
