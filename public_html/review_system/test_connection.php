<?php
/*
PAGE NAME  : test_connection.php
LOCATION   : public_html/review_system/test_connection.php
DESCRIPTION: Test database connection and verify system integration.
FUNCTION   : Verify that the review system can connect to the database.
CHANGE LOG : 2025-08-12 - Created for testing integration
*/

// Include the review system config file
include 'config.php';

echo "<h2>Review System Integration Test</h2>";

// Test database connection
try {
    echo "<h3>Database Connection Test</h3>";
    echo "Host: " . db_host . "<br>";
    echo "Database: " . reviews_db_name . "<br>";
    echo "User: " . db_user . "<br>";
    echo "Charset: " . db_charset . "<br><br>";
    
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . reviews_db_name . ';charset=' . db_charset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<span style='color: green'>✓ Database connection successful!</span><br><br>";
    
    // Test if tables exist
    echo "<h3>Table Structure Test</h3>";
    $tables = ['reviews', 'review_filters', 'review_images', 'review_page_details'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("DESCRIBE $table");
            echo "<span style='color: green'>✓ Table '$table' exists</span><br>";
        } catch (Exception $e) {
            echo "<span style='color: red'>✗ Table '$table' missing: " . $e->getMessage() . "</span><br>";
        }
    }
    
} catch (PDOException $exception) {
    echo "<span style='color: red'>✗ Database connection failed: " . $exception->getMessage() . "</span><br>";
    echo "<br><strong>Note:</strong> You may need to create the 'phpreviews' database and import the phpreviews.sql file.";
}

echo "<br><br><h3>Configuration Test</h3>";
echo "Authentication required: " . (authentication_required ? 'Yes' : 'No') . "<br>";
echo "Reviews directory URL: " . reviews_directory_url . "<br>";
echo "Max review characters: " . max_review_chars . "<br>";
echo "Max stars: " . max_stars . "<br>";
echo "Upload images allowed: " . (upload_images_allowed ? 'Yes' : 'No') . "<br>";
echo "Reviews approval required: " . (reviews_approval_required ? 'Yes' : 'No') . "<br>";

echo "<br><h3>File System Test</h3>";
$required_files = ['reviews.php', 'reviews.js', 'reviews.css', 'config.php'];
foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "<span style='color: green'>✓ $file exists</span><br>";
    } else {
        echo "<span style='color: red'>✗ $file missing</span><br>";
    }
}

if (upload_images_allowed) {
    $upload_dir = images_directory;
    if (is_dir($upload_dir) && is_writable($upload_dir)) {
        echo "<span style='color: green'>✓ Upload directory '$upload_dir' is writable</span><br>";
    } else {
        echo "<span style='color: red'>✗ Upload directory '$upload_dir' is not writable or doesn't exist</span><br>";
    }
}

echo "<br><a href='index.php'>← Back to Reviews</a>";
?>
