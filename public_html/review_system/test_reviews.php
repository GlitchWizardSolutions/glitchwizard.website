<?php
// Minimal test of reviews.php without any other includes
echo "Testing reviews.php directly...<br>";

try {
    // Just include config to see if that works
    include 'config.php';
    echo "<span style='color: green'>✓ config.php included successfully!</span><br>";
    
    if (defined('db_host')) {
        echo "Database host: " . db_host . "<br>";
    }
    
    if (defined('reviews_db_name')) {
        echo "Reviews database: " . reviews_db_name . "<br>";
    }
    
    // Test database connection
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . reviews_db_name . ';charset=' . db_charset, db_user, db_pass);
    echo "<span style='color: green'>✓ Database connection successful!</span><br>";
    
} catch (Exception $e) {
    echo "<span style='color: red'>✗ Error: " . $e->getMessage() . "</span><br>";
}

echo "<br><a href='index.php'>← Back to Reviews</a>";
?>
