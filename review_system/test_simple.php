<?php
// Test script using simple config to bypass the main system integration
echo "Testing with simplified configuration...<br>";

try {
    include 'config_simple.php';
    echo "<span style='color: green'>✓ config_simple.php included successfully!</span><br>";
    
    echo "Database host: " . db_host . "<br>";
    echo "Reviews database: " . reviews_db_name . "<br>";
    
    // Test database connection
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . reviews_db_name . ';charset=' . db_charset, db_user, db_pass);
    echo "<span style='color: green'>✓ Database connection successful!</span><br>";
    
    // Test if a simple call to reviews.php works
    echo "<br>Testing reviews.php endpoint:<br>";
    echo '<iframe src="reviews.php?page_id=1&type=full" width="100%" height="400" style="border: 1px solid #ccc;"></iframe>';
    
} catch (Exception $e) {
    echo "<span style='color: red'>✗ Error: " . $e->getMessage() . "</span><br>";
}

echo "<br><a href='index.php'>← Back to Reviews</a>";
?>
