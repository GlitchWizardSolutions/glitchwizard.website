<?php
// Quick Portfolio Database Test
// Save this as portfolio_debug.php in your public_html folder and run it

// Include the database connection - use dynamic path finding like doctype.php
$config_found = false;
$max_levels = 5;
$dir = __DIR__;
for ($i = 0; $i <= $max_levels; $i++) {
    $try_path = $dir . str_repeat('/..', $i) . '/private/gws-universal-config.php';
    if (file_exists($try_path)) {
        require_once $try_path;
        $config_found = true;
        break;
    }
}
if (!$config_found) {
    die('Critical error: Could not locate private/gws-universal-config.php');
}

echo "<h2>Portfolio Database Debug</h2>";

try {
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'setting_content_portfolio'");
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        echo "<p>✅ Table 'setting_content_portfolio' exists</p>";
        
        // Check table structure
        echo "<h3>Table Structure:</h3>";
        $stmt = $pdo->query("DESCRIBE setting_content_portfolio");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        foreach ($columns as $column) {
            echo $column['Field'] . " (" . $column['Type'] . ")\n";
        }
        echo "</pre>";
        
        // Check row count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM setting_content_portfolio");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Row count: " . $count['count'] . "</p>";
        
        // Show actual data
        echo "<h3>Current Data:</h3>";
        $stmt = $pdo->query("SELECT * FROM setting_content_portfolio ORDER BY portfolio_order ASC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($data)) {
            echo "<p>❌ No data in table</p>";
        } else {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Title</th><th>Category</th><th>Current Image</th><th>Should Be</th><th>Active</th></tr>";
            foreach ($data as $row) {
                // Determine what the image should be based on category
                $should_be = '';
                switch($row['project_category']) {
                    case 'app': $should_be = 'assets/img/portfolio/app-1.jpg'; break;
                    case 'product': $should_be = 'assets/img/portfolio/product-1.jpg'; break;
                    case 'branding': $should_be = 'assets/img/portfolio/branding-1.jpg'; break;
                    default: $should_be = 'assets/img/portfolio/app-1.jpg';
                }
                
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . ($row['project_title'] ?? 'NULL') . "</td>";
                echo "<td>" . ($row['project_category'] ?? 'NULL') . "</td>";
                echo "<td style='color: " . ($row['project_image'] == $should_be ? 'green' : 'red') . "'>" . ($row['project_image'] ?? 'NULL') . "</td>";
                echo "<td style='color: green'>" . $should_be . "</td>";
                echo "<td>" . ($row['is_active'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            echo "<h3>Available Portfolio Images:</h3>";
            $portfolio_dir = 'assets/img/portfolio/';
            $images = ['app-1.jpg', 'books-1.jpg', 'branding-1.jpg', 'product-1.jpg'];
            echo "<ul>";
            foreach ($images as $img) {
                $full_path = $portfolio_dir . $img;
                $file_exists = file_exists($full_path);
                echo "<li style='color: " . ($file_exists ? 'green' : 'red') . "'>" . $full_path . " " . ($file_exists ? '✅' : '❌') . "</li>";
            }
            echo "</ul>";
        }
        
    } else {
        echo "<p>❌ Table 'setting_content_portfolio' does not exist</p>";
        echo "<p>You need to run the create_portfolio_table.sql script first</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database Error: " . $e->getMessage() . "</p>";
}
?>
