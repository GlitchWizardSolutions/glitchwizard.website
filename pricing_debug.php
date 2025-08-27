<?php
// Pricing Database Debug Test
// This will help verify that pricing data is being loaded correctly

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

echo "<h2>Pricing Database Debug</h2>";

try {
    // Check homepage pricing titles
    echo "<h3>Homepage Pricing Section Titles:</h3>";
    $stmt = $pdo->query("SELECT pricing_section_title, pricing_section_description FROM setting_content_homepage WHERE id = 1");
    $homepage_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($homepage_data) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Title</th><th>Description</th></tr>";
        echo "<tr>";
        echo "<td>" . ($homepage_data['pricing_section_title'] ?? 'NULL') . "</td>";
        echo "<td>" . ($homepage_data['pricing_section_description'] ?? 'NULL') . "</td>";
        echo "</tr>";
        echo "</table>";
    } else {
        echo "<p>❌ No homepage data found</p>";
    }
    
    // Check if pricing table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'setting_content_pricing'");
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        echo "<p>✅ Table 'setting_content_pricing' exists</p>";
        
        // Check row count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM setting_content_pricing WHERE is_active = 1");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Active pricing plans: " . $count['count'] . "</p>";
        
        // Show current pricing data
        echo "<h3>Current Pricing Plans:</h3>";
        $stmt = $pdo->query("SELECT * FROM setting_content_pricing WHERE is_active = 1 ORDER BY plan_order ASC");
        $pricing_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($pricing_data)) {
            echo "<p>❌ No active pricing plans found</p>";
        } else {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Order</th><th>Plan Name</th><th>Price</th><th>Description</th><th>Features Count</th><th>Featured</th><th>Popular</th></tr>";
            foreach ($pricing_data as $plan) {
                $features = json_decode($plan['plan_features'] ?? '[]', true);
                $feature_count = is_array($features) ? count($features) : 0;
                
                echo "<tr>";
                echo "<td>" . ($plan['plan_order'] ?? 'NULL') . "</td>";
                echo "<td><strong>" . ($plan['plan_name'] ?? 'NULL') . "</strong></td>";
                echo "<td>" . ($plan['plan_price'] ?? 'NULL') . "</td>";
                echo "<td style='max-width: 300px;'>" . substr($plan['plan_short_desc'] ?? 'NULL', 0, 100) . "...</td>";
                echo "<td>" . $feature_count . "</td>";
                echo "<td>" . ($plan['is_featured'] ? '✅' : '❌') . "</td>";
                echo "<td>" . ($plan['is_popular'] ? '✅' : '❌') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Show detailed features for each plan
            echo "<h3>Plan Features Detail:</h3>";
            foreach ($pricing_data as $plan) {
                echo "<h4>" . ($plan['plan_name'] ?? 'Unknown Plan') . "</h4>";
                $features = json_decode($plan['plan_features'] ?? '[]', true);
                if (is_array($features) && !empty($features)) {
                    echo "<ul>";
                    foreach ($features as $feature) {
                        echo "<li>" . htmlspecialchars($feature) . "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No features defined</p>";
                }
            }
        }
        
    } else {
        echo "<p>❌ Table 'setting_content_pricing' does not exist</p>";
        echo "<p>You need to run the pricing SQL script first</p>";
    }
    
    // Test the database mapping (simulate database_settings.php)
    echo "<h3>Processed Pricing Plans (database_settings.php simulation):</h3>";
    if (!empty($pricing_data)) {
        $pricing_plans = [];
        foreach ($pricing_data as $plan) {
            $pricing_plans[] = [
                'name' => $plan['plan_name'],
                'price' => $plan['plan_price'],
                'description' => $plan['plan_short_desc'],
                'features' => json_decode($plan['plan_features'], true) ?? [],
                'button_text' => $plan['plan_button_text'] ?? 'Get Started',
                'button_link' => $plan['plan_button_link'] ?? '#contact',
                'featured' => $plan['is_featured'] ?? false,
            ];
        }
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Name</th><th>Price</th><th>Features</th><th>Button</th><th>Featured</th></tr>";
        foreach ($pricing_plans as $plan) {
            echo "<tr>";
            echo "<td><strong>" . $plan['name'] . "</strong></td>";
            echo "<td>" . $plan['price'] . "</td>";
            echo "<td>" . count($plan['features']) . " features</td>";
            echo "<td>" . $plan['button_text'] . "</td>";
            echo "<td>" . ($plan['featured'] ? '✅ Featured' : '❌ Standard') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database Error: " . $e->getMessage() . "</p>";
}
?>
