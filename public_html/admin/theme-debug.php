<!DOCTYPE html>
<html>
<head>
    <title>Theme System Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .warning { color: orange; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .section { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Theme System Debug & Test</h1>
    
    <div class="section">
        <h2>Theme Affects These Areas:</h2>
        <ul>
            <li><strong>Public Website:</strong> Uses public-branding-*.css files</li>
            <li><strong>Admin Panel:</strong> Uses admin-branding-*.css files</li>
            <li><strong>Client Portal:</strong> Uses client-branding-*.css files</li>
        </ul>
        <p class="info">When you select a theme, it changes the styling across all three areas simultaneously.</p>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<div class='section'>";
        echo "<h2>Activation Test Result</h2>";
        
        if (isset($_POST['select_theme']) && isset($_POST['template_key'])) {
            $template_key = isset($_POST['template_key']) ? trim(strip_tags($_POST['template_key'])) : '';
            $allowed_keys = ['default', 'subtle', 'bold', 'casual', 'high_contrast', 'template_1', 'template_2', 'template_3'];
            
            echo "<p class='info'><strong>Attempting to activate:</strong> '$template_key'</p>";
            
            if (in_array($template_key, $allowed_keys)) {
                try {
                    require_once '../../private/gws-universal-config.php';
                    require_once '../assets/includes/branding-functions.php';
                    
                    // First check if template exists
                    $stmt = $pdo->prepare('SELECT * FROM setting_branding_templates WHERE template_key = ?');
                    $stmt->execute([$template_key]);
                    $template_exists = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($template_exists) {
                        echo "<p class='success'>✅ Template '$template_key' exists in database</p>";
                        
                        // Now try to activate
                        if (setActiveBrandingTemplate($template_key)) {
                            echo "<p class='success'>✅ Theme '$template_key' activated successfully!</p>";
                            echo "<p class='info'>The page will refresh in 2 seconds to show the new admin theme...</p>";
                            echo "<script>setTimeout(function(){ window.location.reload(); }, 2000);</script>";
                        } else {
                            echo "<p class='error'>❌ Failed to activate theme '$template_key'</p>";
                            
                            // Check database state after failed attempt
                            $stmt = $pdo->query('SELECT template_key, is_active FROM setting_branding_templates');
                            $all_templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            echo "<p class='warning'>Database state after failed attempt:</p><ul>";
                            foreach ($all_templates as $t) {
                                $status = $t['is_active'] ? 'ACTIVE' : 'inactive';
                                echo "<li>{$t['template_key']}: $status</li>";
                            }
                            echo "</ul>";
                        }
                    } else {
                        echo "<p class='error'>❌ Template '$template_key' does not exist in database</p>";
                    }
                    
                } catch (Exception $e) {
                    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p class='error'>❌ Invalid template key: '$template_key'</p>";
            }
        }
        echo "</div>";
    }
    ?>
    
    <div class="section">
        <h2>Current Database State</h2>
        <?php
        try {
            require_once '../../private/gws-universal-config.php';
            
            echo "<table>";
            echo "<tr><th>Template Key</th><th>Template Name</th><th>Description</th><th>Active</th><th>CSS File Config</th></tr>";
            
            $stmt = $pdo->query('SELECT template_key, template_name, template_description, is_active, template_config FROM setting_branding_templates ORDER BY template_key');
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $active_indicator = $row['is_active'] ? '<span class="success">✅ YES</span>' : 'No';
                $config = $row['template_config'] ? json_decode($row['template_config'], true) : [];
                $css_file = isset($config['css_file']) ? $config['css_file'] : 'Not specified';
                
                echo "<tr>";
                echo "<td><strong>{$row['template_key']}</strong></td>";
                echo "<td>{$row['template_name']}</td>";
                echo "<td>{$row['template_description']}</td>";
                echo "<td>{$active_indicator}</td>";
                echo "<td>{$css_file}</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            
        } catch (Exception $e) {
            echo "<p class='error'>Database error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
    
    <div class="section">
        <h2>Test Theme Activation</h2>
        <p>Click any button below to activate that theme across all three areas (Public, Admin, Client Portal):</p>
        
        <form method="POST" style="display: inline-block; margin: 5px;">
            <input type="hidden" name="template_key" value="default">
            <button type="submit" name="select_theme" style="padding: 10px 15px; background: #007bff; color: white; border: none; border-radius: 3px;">Activate Default</button>
        </form>
        
        <form method="POST" style="display: inline-block; margin: 5px;">
            <input type="hidden" name="template_key" value="subtle">
            <button type="submit" name="select_theme" style="padding: 10px 15px; background: #6c757d; color: white; border: none; border-radius: 3px;">Activate Subtle</button>
        </form>
        
        <form method="POST" style="display: inline-block; margin: 5px;">
            <input type="hidden" name="template_key" value="bold">
            <button type="submit" name="select_theme" style="padding: 10px 15px; background: #dc3545; color: white; border: none; border-radius: 3px;">Activate Bold</button>
        </form>
        
        <form method="POST" style="display: inline-block; margin: 5px;">
            <input type="hidden" name="template_key" value="casual">
            <button type="submit" name="select_theme" style="padding: 10px 15px; background: #28a745; color: white; border: none; border-radius: 3px;">Activate Casual</button>
        </form>
        
        <form method="POST" style="display: inline-block; margin: 5px;">
            <input type="hidden" name="template_key" value="high_contrast">
            <button type="submit" name="select_theme" style="padding: 10px 15px; background: #000; color: white; border: none; border-radius: 3px;">Activate High Contrast</button>
        </form>
    </div>
</body>
</html>
