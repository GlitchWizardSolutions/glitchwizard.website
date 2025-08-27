<?php
// Quick timezone test for client portal
require_once dirname(__DIR__, 2) . '/private/gws-universal-config.php';

echo "<h2>Timezone Test - Client Portal</h2>";
echo "<p><strong>Server timezone:</strong> " . date_default_timezone_get() . "</p>";
echo "<p><strong>Current time:</strong> " . date('Y-m-d H:i:s T') . "</p>";
echo "<p><strong>Current time (formatted):</strong> " . date('F j, Y g:i A T') . "</p>";

// Test with different formats
echo "<h3>Different time formats:</h3>";
echo "<ul>";
echo "<li>ISO 8601: " . date('c') . "</li>";
echo "<li>Unix timestamp: " . time() . "</li>";
echo "<li>Human readable: " . date('l, F jS, Y \a\t g:i A T') . "</li>";
echo "</ul>";

echo "<hr>";
echo "<p><em>Expected: Eastern Time (EST/EDT) - Should match your local time of 3:29 PM</em></p>";
?>
