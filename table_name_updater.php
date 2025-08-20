<?php

// Table name mapping for shop system updates
$table_mappings = [
    'discounts' => 'shop_discounts',
    'products' => 'shop_products',
    'product_categories' => 'shop_product_categories',
    'product_category' => 'shop_product_category',
    'product_downloads' => 'shop_product_downloads',
    'product_media' => 'shop_product_media',
    'product_media_map' => 'shop_product_media_map',
    'product_options' => 'shop_product_options',
    'shipping' => 'shop_shipping',
    'taxes' => 'shop_taxes',
    'transactions' => 'shop_transactions',
    'transaction_items' => 'shop_transaction_items',
    'wishlist' => 'shop_wishlist'
];

// Directories to process
$directories = [
    'c:/xampp/htdocs/gws-universal-hybrid-app/public_html/admin/shop_system',
    'c:/xampp/htdocs/gws-universal-hybrid-app/public_html/shop_system'
];

function updateTableNamesInFile($filePath, $mappings) {
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    foreach ($mappings as $oldTable => $newTable) {
        // Update SQL statements (FROM, JOIN, UPDATE, INSERT INTO, DELETE FROM)
        $patterns = [
            '/\bFROM\s+' . preg_quote($oldTable, '/') . '\b/i',
            '/\bJOIN\s+' . preg_quote($oldTable, '/') . '\b/i',
            '/\bUPDATE\s+' . preg_quote($oldTable, '/') . '\b/i',
            '/\bINSERT\s+INTO\s+' . preg_quote($oldTable, '/') . '\b/i',
            '/\bDELETE\s+FROM\s+' . preg_quote($oldTable, '/') . '\b/i',
            '/\bINSERT\s+IGNORE\s+INTO\s+' . preg_quote($oldTable, '/') . '\b/i'
        ];
        
        $replacements = [
            'FROM ' . $newTable,
            'JOIN ' . $newTable,
            'UPDATE ' . $newTable,
            'INSERT INTO ' . $newTable,
            'DELETE FROM ' . $newTable,
            'INSERT IGNORE INTO ' . $newTable
        ];
        
        $content = preg_replace($patterns, $replacements, $content);
    }
    
    if ($content !== $originalContent) {
        file_put_contents($filePath, $content);
        return true;
    }
    
    return false;
}

function processDirectory($directory, $mappings) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory)
    );
    
    $updatedFiles = [];
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $filePath = $file->getPathname();
            if (updateTableNamesInFile($filePath, $mappings)) {
                $updatedFiles[] = $filePath;
            }
        }
    }
    
    return $updatedFiles;
}

echo "Starting table name updates...\n";

$allUpdatedFiles = [];

foreach ($directories as $directory) {
    if (is_dir($directory)) {
        echo "Processing directory: $directory\n";
        $updatedFiles = processDirectory($directory, $table_mappings);
        $allUpdatedFiles = array_merge($allUpdatedFiles, $updatedFiles);
        echo "Updated " . count($updatedFiles) . " files in $directory\n";
    } else {
        echo "Directory not found: $directory\n";
    }
}

echo "\nTable name update complete!\n";
echo "Total files updated: " . count($allUpdatedFiles) . "\n";

if (count($allUpdatedFiles) > 0) {
    echo "\nUpdated files:\n";
    foreach ($allUpdatedFiles as $file) {
        echo "- $file\n";
    }
}

?>
