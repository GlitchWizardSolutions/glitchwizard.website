<?php
// Simple debug test for database connection
echo "<h2>Reviews Database Connection Debug</h2>";

try {
    // Test including config
    echo "<p>Testing config include...</p>";
    include 'review_system/config.php';
    echo "<p>✓ Config included successfully</p>";
    
    echo "<p>Database settings:</p>";
    echo "Host: " . db_host . "<br>";
    echo "User: " . db_user . "<br>";
    echo "Database: " . reviews_db_name . "<br>";
    echo "Charset: " . db_charset . "<br>";
    
    // Test database connection
    echo "<p>Testing database connection...</p>";
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . reviews_db_name . ';charset=' . db_charset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✓ Database connection successful!</p>";
    
    // Test a simple query
    echo "<p>Testing simple query...</p>";
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "<p>✓ Query test successful: " . $result['test'] . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p style='color: red;'>File: " . $e->getFile() . "</p>";
    echo "<p style='color: red;'>Line: " . $e->getLine() . "</p>";
}

echo "<br><a href='public_reviews.php'>← Back to Reviews</a>";
?>
