<?php
/*
Footer Useful Links Debug Script
Tests database loading of footer useful links section
Separate from special links (RSS, Sitemap, etc.)
*/

// Include database configuration
include_once 'private/gws-universal-config.php';
include_once 'public_html/assets/includes/settings/database_settings.php';

echo "<h1>Footer Useful Links Debug</h1>";

// Test database connection
echo "<h2>Database Connection</h2>";
if (isset($pdo)) {
    echo "<p style='color: green;'>✅ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

// Check if table exists
echo "<h2>Table Check</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'setting_footer_useful_links'");
    $table_exists = $stmt->rowCount() > 0;
    
    if ($table_exists) {
        echo "<p style='color: green;'>✅ Table 'setting_footer_useful_links' exists</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Table 'setting_footer_useful_links' does not exist</p>";
        echo "<p><strong>Run this SQL:</strong> <code>source footer_useful_links_setup.sql</code></p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error checking table: " . $e->getMessage() . "</p>";
}

// Test raw database query
echo "<h2>Raw Database Data</h2>";
try {
    $stmt = $pdo->query("SELECT * FROM setting_footer_useful_links ORDER BY display_order ASC");
    $raw_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($raw_data) {
        echo "<p style='color: green;'>✅ Found " . count($raw_data) . " useful links in database</p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Title</th><th>URL</th><th>Icon</th><th>Order</th><th>Active</th></tr>";
        foreach ($raw_data as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['url']) . "</td>";
            echo "<td>" . htmlspecialchars($row['icon']) . "</td>";
            echo "<td>" . htmlspecialchars($row['display_order']) . "</td>";
            echo "<td>" . ($row['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No useful links found in database</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error querying database: " . $e->getMessage() . "</p>";
}

// Test processed footer_links variable
echo "<h2>Processed Footer Links Variable</h2>";
if (isset($footer_links) && !empty($footer_links)) {
    echo "<p style='color: green;'>✅ \$footer_links variable loaded successfully</p>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Label</th><th>URL</th></tr>";
    foreach ($footer_links as $label => $url) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($label) . "</td>";
        echo "<td>" . htmlspecialchars($url) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ \$footer_links variable is empty or not set</p>";
}

// Footer Preview
echo "<h2>Footer Useful Links Preview</h2>";
echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0; background: #f9f9f9;'>";
echo "<h4>Useful Links</h4>";

if (isset($footer_links) && !empty($footer_links)) {
    $link_icons = [
        'About Us' => 'bi-info-circle',
        'Reviews' => 'bi-star',
        'FAQs' => 'bi-question-circle',
        'Resources' => 'bi-folder',
        'Contact' => 'bi-envelope'
    ];
    
    foreach ($footer_links as $label => $url) {
        $icon = $link_icons[$label] ?? 'bi-link-45deg';
        echo "<div style='margin: 5px 0;'>";
        echo "<i class='{$icon}'></i> ";
        echo "<a href='" . htmlspecialchars($url) . "'>" . htmlspecialchars($label) . "</a>";
        echo "</div>";
    }
} else {
    echo "<p>No useful links available</p>";
}
echo "</div>";

echo "<h2>Note</h2>";
echo "<p><strong>Useful Links</strong> (database-driven, shown above) are different from <strong>Special Links</strong> (hardcoded: RSS, Sitemap, Terms, Privacy, Accessibility)</p>";
echo "<p>Special Links remain at the bottom of the footer and are managed separately.</p>";
?>
