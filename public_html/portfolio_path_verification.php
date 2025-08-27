<?php
// Portfolio Database Path Verification Test
// This will help verify that images are being pulled from database, not hardcoded

// Include the database connection
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

echo "<h2>Portfolio Database Path Verification</h2>";

try {
    // Get the raw database data
    echo "<h3>1. Raw Database Data:</h3>";
    $stmt = $pdo->query("SELECT id, project_title, project_category, project_image FROM setting_content_portfolio WHERE is_active = 1 ORDER BY portfolio_order ASC");
    $raw_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Title</th><th>Category</th><th>Database Image Path</th></tr>";
    foreach ($raw_data as $row) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['project_title'] . "</td>";
        echo "<td>" . $row['project_category'] . "</td>";
        echo "<td style='font-family: monospace;'>" . $row['project_image'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Now let's simulate what database_settings.php does
    echo "<h3>2. Processed Portfolio Items (like database_settings.php):</h3>";
    $portfolio_items = [];
    foreach ($raw_data as $portfolio_row) {
        $portfolio_items[] = [
            'title' => $portfolio_row['project_title'] ?? 'Project Title',
            'description' => $portfolio_row['project_description'] ?? 'Project description',
            'category' => $portfolio_row['project_category'] ?? 'all',
            'image' => $portfolio_row['project_image'] ?? 'assets/img/masonry-portfolio/masonry-portfolio-1.jpg',
            'large_image' => $portfolio_row['project_large_image'] ?? $portfolio_row['project_image'],
            'url' => $portfolio_row['project_url'] ?? '#',
            'filter_class' => 'filter-' . ($portfolio_row['project_category'] ?? 'all'),
            'gallery_name' => 'portfolio-gallery-' . ($portfolio_row['project_category'] ?? 'all')
        ];
    }
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Title</th><th>Category</th><th>Processed Image Path</th><th>Filter Class</th></tr>";
    foreach ($portfolio_items as $item) {
        echo "<tr>";
        echo "<td>" . $item['title'] . "</td>";
        echo "<td>" . $item['category'] . "</td>";
        echo "<td style='font-family: monospace; color: blue;'>" . $item['image'] . "</td>";
        echo "<td>" . $item['filter_class'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test if we're using fallback values
    echo "<h3>3. Fallback Detection:</h3>";
    $using_fallbacks = false;
    foreach ($portfolio_items as $item) {
        if ($item['image'] === 'assets/img/masonry-portfolio/masonry-portfolio-1.jpg') {
            $using_fallbacks = true;
            echo "<p style='color: red;'>⚠️ FALLBACK DETECTED: Item '" . $item['title'] . "' is using fallback image path</p>";
        }
    }
    
    if (!$using_fallbacks) {
        echo "<p style='color: green;'>✅ NO FALLBACKS: All images are using database values</p>";
    }
    
    // Check if images actually exist
    echo "<h3>4. Image File Verification:</h3>";
    foreach ($portfolio_items as $item) {
        $image_path = $item['image'];
        $file_exists = file_exists($image_path);
        $color = $file_exists ? 'green' : 'red';
        $status = $file_exists ? '✅ EXISTS' : '❌ MISSING';
        echo "<p style='color: $color;'>$status: $image_path</p>";
    }
    
    // Compare with any hardcoded values in the current codebase
    echo "<h3>5. Hardcoded Value Check:</h3>";
    $hardcoded_found = false;
    
    // Check if portfolio.php has any hardcoded image paths
    $portfolio_php = file_get_contents('assets/includes/portfolio.php');
    if (strpos($portfolio_php, 'masonry-portfolio-') !== false) {
        echo "<p style='color: orange;'>⚠️ Found 'masonry-portfolio-' references in portfolio.php (likely fallback code)</p>";
    } else {
        echo "<p style='color: green;'>✅ No hardcoded masonry-portfolio references in portfolio.php</p>";
    }
    
    echo "<h3>6. Summary:</h3>";
    echo "<ul>";
    echo "<li><strong>Database entries:</strong> " . count($raw_data) . "</li>";
    echo "<li><strong>Processed items:</strong> " . count($portfolio_items) . "</li>";
    echo "<li><strong>Using fallbacks:</strong> " . ($using_fallbacks ? 'YES (check database data)' : 'NO') . "</li>";
    echo "<li><strong>All images exist:</strong> " . (array_reduce($portfolio_items, function($carry, $item) { return $carry && file_exists($item['image']); }, true) ? 'YES' : 'NO') . "</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
