<?php
include_once 'public_html/shop_system/shop_load.php';
echo 'Testing database connection...' . PHP_EOL;
try {
    // Check if tables exist
    $stmt = $pdo->query('SHOW TABLES LIKE "shop_%"');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo 'Shop tables found: ' . implode(', ', $tables) . PHP_EOL;
    
    // Check specific product media tables
    $stmt = $pdo->query('SHOW TABLES LIKE "%media%"');
    $media_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo 'Media tables found: ' . implode(', ', $media_tables) . PHP_EOL;
    
    // Check if product ID 2 exists
    $stmt = $pdo->prepare('SELECT id, title FROM shop_products WHERE id = 2');
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        echo 'Product 2 found: ' . $product['title'] . PHP_EOL;
        
        // Check media for product 2
        $stmt = $pdo->prepare('SELECT m.*, pm.position FROM shop_product_media_map pm JOIN shop_product_media m ON m.id = pm.media_id WHERE pm.product_id = ? ORDER BY pm.position ASC');
        $stmt->execute([2]);
        $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo 'Media count for product 2: ' . count($media) . PHP_EOL;
        if ($media) {
            foreach ($media as $m) {
                echo 'Media: ' . $m['full_path'] . ' (exists: ' . (file_exists($m['full_path']) ? 'yes' : 'no') . ')' . PHP_EOL;
            }
        } else {
            echo 'No media found in database for product 2' . PHP_EOL;
        }
    } else {
        echo 'Product 2 not found!' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
