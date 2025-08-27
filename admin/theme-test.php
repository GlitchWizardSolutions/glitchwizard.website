<!DOCTYPE html>
<html>
<head>
    <title>Theme Test</title>
</head>
<body>
    <h1>Theme Activation Test</h1>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<h2>POST Data Received:</h2>";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        
        if (isset($_POST['select_theme']) && isset($_POST['template_key'])) {
            $template_key = $_POST['template_key'];
            echo "<p>Attempting to activate theme: '$template_key'</p>";
            
            try {
                require_once '../../private/gws-universal-config.php';
                require_once '../assets/includes/branding-functions.php';
                
                if (setActiveBrandingTemplate($template_key)) {
                    echo "<p style='color: green;'>✅ Theme '$template_key' activated successfully!</p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to activate theme '$template_key'</p>";
                }
                
            } catch (Exception $e) {
                echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
            }
        }
    }
    ?>
    
    <form method="POST">
        <label>
            Theme to activate:
            <select name="template_key">
                <option value="default">Default</option>
                <option value="subtle">Subtle</option>
                <option value="bold">Bold</option>
                <option value="casual">Casual</option>
                <option value="high_contrast">High Contrast</option>
            </select>
        </label>
        <button type="submit" name="select_theme" value="1">Activate Theme</button>
    </form>
</body>
</html>
