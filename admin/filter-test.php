<!DOCTYPE html>
<html>
<head>
    <title>Filter Input Test</title>
</head>
<body>
    <h1>Filter Input Test</h1>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<h2>Testing filter_input function:</h2>";
        
        echo "<p><strong>Raw POST data:</strong></p>";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        
        // Test the deprecated filter
        $template_key_filtered = filter_input(INPUT_POST, 'template_key', FILTER_SANITIZE_STRING);
        echo "<p><strong>Filtered value (deprecated method):</strong> ";
        var_dump($template_key_filtered);
        echo "</p>";
        
        // Alternative method
        $template_key_direct = isset($_POST['template_key']) ? trim($_POST['template_key']) : null;
        echo "<p><strong>Direct access:</strong> ";
        var_dump($template_key_direct);
        echo "</p>";
        
        // Check if they match
        if ($template_key_filtered === $template_key_direct) {
            echo "<p style='color: green;'>✅ Both methods return the same value</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Methods return different values</p>";
        }
        
        // Test with the problematic value 'default'
        if ($template_key_direct === 'default') {
            echo "<p style='color: green;'>✅ Received 'default' value correctly</p>";
            
            // Now test the actual function
            try {
                require_once '../../private/gws-universal-config.php';
                require_once '../assets/includes/branding-functions.php';
                
                echo "<p>Testing setActiveBrandingTemplate('default')...</p>";
                $result = setActiveBrandingTemplate('default');
                
                if ($result) {
                    echo "<p style='color: green;'>✅ Function returned true - success!</p>";
                } else {
                    echo "<p style='color: red;'>❌ Function returned false - failed!</p>";
                }
                
            } catch (Exception $e) {
                echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
            }
        }
    }
    ?>
    
    <form method="POST">
        <input type="hidden" name="template_key" value="default">
        <button type="submit" name="select_theme">Test with 'default' value</button>
    </form>
    
    <form method="POST">
        <input type="hidden" name="template_key" value="subtle">
        <button type="submit" name="select_theme">Test with 'subtle' value</button>
    </form>
</body>
</html>
