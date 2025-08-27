<!DOCTYPE html>
<html>
<head>
    <title>Simple CSS Test</title>
    <?php
    include_once 'admin/assets/includes/main.php';
    include_once 'assets/includes/theme-loader.php';
    
    echo "<!-- Starting theme loader -->\n";
    loadActiveThemeCSS('admin');
    echo "<!-- Theme loader complete -->\n";
    ?>
</head>
<body>
    <h1>CSS Loading Test</h1>
    <p>If you see a red banner, the CSS is working.</p>
    <p>If not, check the page source to see what CSS links are included.</p>
    
    <h2>Debug Info:</h2>
    <?php
    echo "<p>Active theme: " . getActiveTheme('admin') . "</p>";
    
    $css_file = "/gws-universal-hybrid-app/public_html/assets/css/themes/admin-branding-" . getActiveTheme('admin') . ".css";
    echo "<p>CSS path: {$css_file}</p>";
    
    $absolute_path = "D:\\XAMPP\\htdocs{$css_file}";
    echo "<p>File exists: " . (file_exists($absolute_path) ? "YES" : "NO") . "</p>";
    ?>
</body>
</html>
