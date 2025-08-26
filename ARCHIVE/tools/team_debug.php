<?php
// Team Database Debug Test
// This will help verify that team data is being loaded correctly

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

echo "<h2>Team Database Debug</h2>";

try {
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'setting_content_team'");
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        echo "<p>✅ Table 'setting_content_team' exists</p>";
        
        // Check table structure
        echo "<h3>Table Structure:</h3>";
        $stmt = $pdo->query("DESCRIBE setting_content_team");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        foreach ($columns as $column) {
            echo $column['Field'] . " (" . $column['Type'] . ")\n";
        }
        echo "</pre>";
        
        // Check row count
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM setting_content_team");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Row count: " . $count['count'] . "</p>";
        
        // Show actual data
        echo "<h3>Current Team Data:</h3>";
        $stmt = $pdo->query("SELECT * FROM setting_content_team ORDER BY team_order ASC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($data)) {
            echo "<p>❌ No data in table</p>";
        } else {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Title</th><th>Bio</th><th>Image</th><th>Order</th><th>Active</th></tr>";
            foreach ($data as $row) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . ($row['member_name'] ?? 'NULL') . "</td>";
                echo "<td>" . ($row['member_role'] ?? 'NULL') . "</td>";
                echo "<td style='max-width: 300px;'>" . substr($row['member_bio'] ?? 'NULL', 0, 100) . "...</td>";
                echo "<td style='font-family: monospace;'>" . ($row['member_image'] ?? 'NULL') . "</td>";
                echo "<td>" . ($row['display_order'] ?? 'NULL') . "</td>";
                echo "<td>" . ($row['is_active'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Check if images exist
        echo "<h3>Image File Verification:</h3>";
        foreach ($data as $row) {
            $image_path = $row['member_image'];
            $file_exists = file_exists($image_path);
            $color = $file_exists ? 'green' : 'red';
            $status = $file_exists ? '✅ EXISTS' : '❌ MISSING';
            echo "<p style='color: $color;'>$status: $image_path</p>";
        }
        
        // Test the database mapping (simulate database_settings.php)
        echo "<h3>Processed Team Members (database_settings.php simulation):</h3>";
        $team_members = [];
        foreach ($data as $team_row) {
            $team_members[] = [
                'name' => $team_row['member_name'] ?? 'Team Member',
                'title' => $team_row['member_role'] ?? 'Position',
                'bio' => $team_row['member_bio'] ?? 'Team member biography',
                'image' => $team_row['member_image'] ?? 'assets/img/team/team-1.jpg'
            ];
        }
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Name</th><th>Title</th><th>Bio Preview</th><th>Image</th></tr>";
        foreach ($team_members as $member) {
            echo "<tr>";
            echo "<td>" . $member['name'] . "</td>";
            echo "<td>" . $member['title'] . "</td>";
            echo "<td style='max-width: 200px;'>" . substr($member['bio'], 0, 80) . "...</td>";
            echo "<td style='font-family: monospace; color: blue;'>" . $member['image'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p>❌ Table 'setting_content_team' does not exist</p>";
        echo "<p>You need to run the create_team_table.sql script first</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database Error: " . $e->getMessage() . "</p>";
}
?>
