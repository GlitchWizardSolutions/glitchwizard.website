<?php
// Business Info Database Debug Test
// This will help verify that business information is being loaded correctly

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

echo "<h2>Business Info Database Debug (Normalized Structure)</h2>";

try {
    // Check if both tables exist
    $stmt = $pdo->query("SHOW TABLES LIKE 'setting_business_identity'");
    $identity_exists = $stmt->fetch();
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'setting_business_contact'");
    $contact_exists = $stmt->fetch();
    
    if ($identity_exists && $contact_exists) {
        echo "<p>✅ Both tables exist: setting_business_identity & setting_business_contact</p>";
        
        // Show joined business data
        echo "<h3>Current Business Information (Joined):</h3>";
        $stmt = $pdo->query("
            SELECT 
                bi.business_name_short,
                bi.business_name_medium,
                bi.business_name_long,
                bi.business_tagline_medium,
                bi.legal_business_name,
                bc.primary_email,
                bc.primary_phone,
                bc.primary_address,
                bc.city,
                bc.state,
                bc.zipcode,
                bc.website_url,
                bc.social_facebook,
                bc.social_instagram
            FROM setting_business_identity bi 
            LEFT JOIN setting_business_contact bc ON bi.id = bc.business_identity_id 
            WHERE bi.id = 1
        ");
        $business_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($business_data) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th colspan='2' style='background: #f0f0f0;'>Business Identity</th></tr>";
            
            $identity_fields = [
                'business_name_short' => 'Short Name',
                'business_name_medium' => 'Medium Name',
                'business_name_long' => 'Long Name',
                'business_tagline_medium' => 'Tagline',
                'legal_business_name' => 'Legal Name'
            ];
            
            foreach ($identity_fields as $field => $label) {
                echo "<tr>";
                echo "<td><strong>" . $label . "</strong></td>";
                echo "<td>" . ($business_data[$field] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            
            echo "<tr><th colspan='2' style='background: #e0e0e0;'>Contact Information</th></tr>";
            
            $contact_fields = [
                'primary_email' => 'Email',
                'primary_phone' => 'Phone',
                'primary_address' => 'Address',
                'city' => 'City',
                'state' => 'State',
                'zipcode' => 'ZIP Code',
                'website_url' => 'Website',
                'social_facebook' => 'Facebook',
                'social_instagram' => 'Instagram'
            ];
            
            foreach ($contact_fields as $field => $label) {
                echo "<tr>";
                echo "<td><strong>" . $label . "</strong></td>";
                echo "<td>" . ($business_data[$field] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Test the database mapping (simulate database_settings.php)
            echo "<h3>Processed Business Variables (database_settings.php simulation):</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Variable</th><th>Value</th><th>Source Table</th></tr>";
            
            $mapped_variables = [
                '$business_name' => [$business_data['business_name_medium'] ?? '[MISSING]', 'setting_business_identity'],
                '$business_name_short' => [$business_data['business_name_short'] ?? '[MISSING]', 'setting_business_identity'],
                '$business_name_long' => [$business_data['business_name_long'] ?? '[MISSING]', 'setting_business_identity'],
                '$contact_email' => [$business_data['primary_email'] ?? '[MISSING]', 'setting_business_contact'],
                '$contact_phone' => [$business_data['primary_phone'] ?? '[MISSING]', 'setting_business_contact'],
                '$contact_address' => [$business_data['primary_address'] ?? '[MISSING]', 'setting_business_contact'],
                '$contact_city' => [$business_data['city'] ?? '[MISSING]', 'setting_business_contact'],
                '$contact_state' => [$business_data['state'] ?? '[MISSING]', 'setting_business_contact'],
                '$contact_zipcode' => [$business_data['zipcode'] ?? '[MISSING]', 'setting_business_contact']
            ];
            
            foreach ($mapped_variables as $var => $data) {
                echo "<tr>";
                echo "<td><code>" . $var . "</code></td>";
                echo "<td>" . $data[0] . "</td>";
                echo "<td><em>" . $data[1] . "</em></td>";
                echo "</tr>";
            }
            echo "</table>";
            
        } else {
            echo "<p>❌ No business data found with joined query</p>";
        }
        
    } else {
        if (!$identity_exists) {
            echo "<p>❌ Table 'setting_business_identity' does not exist</p>";
        }
        if (!$contact_exists) {
            echo "<p>❌ Table 'setting_business_contact' does not exist</p>";
        }
        echo "<p>You need to run the burden_to_blessings_business_info.sql script first</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database Error: " . $e->getMessage() . "</p>";
}
?>
