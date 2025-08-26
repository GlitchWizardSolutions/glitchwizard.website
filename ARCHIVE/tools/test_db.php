<?php
// Quick test to verify database connection and blog_menu table
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting database test...\n";

try {
    include_once "accounts_system/main.php";
    echo "Main.php included successfully\n";
} catch (Exception $e) {
    echo "Error including main.php: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Testing database connection...\n";
if (isset($pdo) && $pdo !== null) {
    echo "✓ PDO connection established\n";
    
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'blog_menu'");
        $table_exists = $stmt->fetch();
        
        if ($table_exists) {
            echo "✓ blog_menu table exists\n";
            
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM blog_menu");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "✓ blog_menu table has " . $result['count'] . " records\n";
            
            $stmt = $pdo->query("SELECT * FROM blog_menu");
            $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "Menu items:\n";
            foreach ($menus as $menu) {
                echo "  - " . $menu['page'] . " (" . $menu['path'] . ")\n";
            }
        } else {
            echo "✗ blog_menu table does not exist\n";
        }
    } catch (PDOException $e) {
        echo "✗ Database error: " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ PDO connection not available\n";
}
?>
