<?php
// Quick test to check if the doctype include works
echo "Testing doctype include from review_system...<br>";

try {
    include_once "../assets/includes/doctype.php";
    echo "<span style='color: green'>✓ doctype.php included successfully!</span><br>";
    echo "Current working directory: " . getcwd() . "<br>";
    echo "__DIR__: " . __DIR__ . "<br>";
    
    if (defined('db_host')) {
        echo "<span style='color: green'>✓ Database constants are available</span><br>";
    } else {
        echo "<span style='color: red'>✗ Database constants not available</span><br>";
    }
    
    if (isset($business_name)) {
        echo "<span style='color: green'>✓ Business settings loaded: $business_name</span><br>";
    } else {
        echo "<span style='color: orange'>⚠ Business settings not loaded (this might be normal)</span><br>";
    }
    
} catch (Exception $e) {
    echo "<span style='color: red'>✗ Error: " . $e->getMessage() . "</span><br>";
}

echo "<br><a href='index.php'>← Back to Reviews</a>";
?>
