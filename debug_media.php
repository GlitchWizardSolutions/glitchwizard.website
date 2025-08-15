<?php
include_once 'shop_system/shop_load.php';
echo 'Testing database...<br>';

// Check if product ID 2 exists  
$stmt = $pdo->prepare('SELECT id, title FROM shop_products WHERE id = 2');
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if ($product) {
    echo 'Product 2: ' . $product['title'] . '<br>';
    
    // Check media for product 2
    $stmt = $pdo->prepare('SELECT m.*, pm.position FROM shop_product_media_map pm JOIN shop_product_media m ON m.id = pm.media_id WHERE pm.product_id = ? ORDER BY pm.position ASC');
    $stmt->execute([2]);
    $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo 'Media count: ' . count($media) . '<br>';
    
    if ($media) {
        foreach ($media as $m) {
            echo 'Media path: ' . $m['full_path'] . '<br>';
        }
    } else {
        echo 'No media records found for product 2<br>';
        
        // Check if tables exist
        $stmt = $pdo->query('SHOW TABLES LIKE "shop_product_media%"');
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo 'Media tables: ' . implode(', ', $tables) . '<br>';
    }
} else {
    echo 'Product 2 not found<br>';
}
?>
