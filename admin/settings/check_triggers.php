<?php
require_once '../../../private/gws-universal-config.php';

echo "<h2>Database Trigger Analysis & Fix</h2>";

try {
    // Check which database we're using
    $db_stmt = $pdo->query('SELECT DATABASE() as current_db');
    $db_result = $db_stmt->fetch(PDO::FETCH_ASSOC);
    echo "<p><strong>Database:</strong> " . $db_result['current_db'] . "</p>";
    
    // Check for triggers on the setting_branding_templates table
    echo "<h3>Checking for Triggers:</h3>";
    $trigger_stmt = $pdo->query("SHOW TRIGGERS LIKE 'setting_branding_templates'");
    $triggers = $trigger_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($triggers) > 0) {
        echo "<p style='color: orange;'>⚠️ Found " . count($triggers) . " trigger(s):</p>";
        
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Trigger</th><th>Event</th><th>Timing</th><th>Statement</th><th>Action</th></tr>";
        
        foreach ($triggers as $trigger) {
            echo "<tr>";
            echo "<td>" . $trigger['Trigger'] . "</td>";
            echo "<td>" . $trigger['Event'] . "</td>";
            echo "<td>" . $trigger['Timing'] . "</td>";
            echo "<td style='max-width: 300px; word-wrap: break-word;'>" . htmlspecialchars($trigger['Statement']) . "</td>";
            echo "<td>";
            echo "<form method='POST' style='display: inline;'>";
            echo "<input type='hidden' name='drop_trigger' value='" . $trigger['Trigger'] . "'>";
            echo "<button type='submit' onclick='return confirm(\"Drop trigger " . $trigger['Trigger'] . "?\")'>Drop</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "<p>✅ No triggers found on setting_branding_templates table</p>";
    }
    
    // Handle trigger dropping
    if (isset($_POST['drop_trigger'])) {
        $trigger_name = $_POST['drop_trigger'];
        try {
            $pdo->exec("DROP TRIGGER IF EXISTS `{$trigger_name}`");
            echo "<p style='color: green;'>✅ Dropped trigger: {$trigger_name}</p>";
            echo "<script>setTimeout(function(){ window.location.reload(); }, 1000);</script>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Failed to drop trigger {$trigger_name}: " . $e->getMessage() . "</p>";
        }
    }
    
    // Check for any other triggers in the database that might affect this table
    echo "<h3>All Database Triggers:</h3>";
    $all_triggers_stmt = $pdo->query("SHOW TRIGGERS");
    $all_triggers = $all_triggers_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($all_triggers) > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Trigger</th><th>Table</th><th>Event</th><th>Timing</th></tr>";
        
        foreach ($all_triggers as $trigger) {
            $highlight = (strpos($trigger['Statement'], 'setting_branding_templates') !== false) ? 'background-color: #ffeb3b;' : '';
            echo "<tr style='$highlight'>";
            echo "<td>" . $trigger['Trigger'] . "</td>";
            echo "<td>" . $trigger['Table'] . "</td>";
            echo "<td>" . $trigger['Event'] . "</td>";
            echo "<td>" . $trigger['Timing'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><small>Highlighted triggers may reference setting_branding_templates</small></p>";
    } else {
        echo "<p>✅ No triggers found in database</p>";
    }
    
    // Test simple update after trigger check
    if (count($triggers) == 0) {
        echo "<h3>Test Update (No Triggers):</h3>";
        try {
            // Simple non-transactional update
            $test_stmt = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 0 WHERE area = 'admin'");
            $result = $test_stmt->execute();
            echo "<p>Test deactivate result: " . ($result ? 'SUCCESS' : 'FAILED') . "</p>";
            
            $test_stmt2 = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 1 WHERE area = 'admin' AND template_key = 'default'");
            $result2 = $test_stmt2->execute();
            echo "<p>Test activate result: " . ($result2 ? 'SUCCESS' : 'FAILED') . "</p>";
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Test update failed: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='branding_settings_tabbed.php'>← Back to Branding Settings</a></p>";
?>
