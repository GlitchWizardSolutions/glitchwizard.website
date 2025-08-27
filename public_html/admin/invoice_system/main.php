<?php
// Invoice System - Main Include File
// Include the admin main file for standard admin functionality
include_once '../assets/includes/main.php';

// Invoice System Configuration Constants
if (!defined('base_path')) {
    define('base_path', __DIR__ . '/');
}
if (!defined('base_url')) {
    define('base_url', $admin_path . '/invoice_system/');
}
if (!defined('currency_code')) {
    define('currency_code', '$');
}
if (!defined('invoice_prefix')) {
    define('invoice_prefix', 'INV');
}
if (!defined('company_name')) {
    define('company_name', 'Your Company Name');
}
if (!defined('company_email')) {
    define('company_email', 'company@example.com');
}
if (!defined('company_phone')) {
    define('company_phone', '(555) 123-4567');
}
if (!defined('company_address')) {
    define('company_address', '123 Business Street\nBusiness City, BC 12345\nUnited States');
}
if (!defined('mail_enabled')) {
    define('mail_enabled', false);
}
if (!defined('notifications_enabled')) {
    define('notifications_enabled', true);
}

// Get the total number of accounts
$stmt = $pdo->query('SELECT COUNT(*) FROM accounts');
$accounts_total = $stmt->fetchColumn();
// Get the total number of events
$stmt = $pdo->query('SELECT COUNT(*) FROM invoices');
$invoices_total = $stmt->fetchColumn();
// Get the total number of clients
$stmt = $pdo->query('SELECT COUNT(*) FROM invoice_clients');
$clients_total = $stmt->fetchColumn();
// Icons for the table headers
$table_icons = [
    // Table sorting direction indicators (Bootstrap Icons)
    'asc' => '<i class="bi bi-arrow-up-short" aria-hidden="true"></i>',
    'desc' => '<i class="bi bi-arrow-down-short" aria-hidden="true"></i>'
];

// Copy directory function (invoice-specific)
function copy_directory($source, $destination) {
    if (is_dir($source)) {
        @mkdir($destination);
        $directory = dir($source);
        while (false !== ($readdirectory = $directory->read())) {
            if ($readdirectory == '.' || $readdirectory == '..') {
                continue;
            }
            $PathDir = $source . '/' . $readdirectory;
            if (is_dir($PathDir)) {
                copy_directory($PathDir, $destination . '/' . $readdirectory);
                continue;
            }
            copy($PathDir, $destination . '/' . $readdirectory);
        }
        $directory->close();
    } else {
        copy($source, $destination);
    }
}
// Add transactions items to the database (invoice-specific)
function addItems($pdo, $invoice_number) {
    if (isset($_POST['item_id']) && is_array($_POST['item_id']) && count($_POST['item_id']) > 0) {
        // Iterate items
        $delete_list = [];
        for ($i = 0; $i < count($_POST['item_id']); $i++) {
            // If the item doesnt exist in the database
            if (!intval($_POST['item_id'][$i])) {
                // Insert new item
                $stmt = $pdo->prepare('INSERT INTO invoice_items (invoice_number, item_name, item_description, item_price, item_quantity) VALUES (?,?,?,?,?)');
                $stmt->execute([ $invoice_number, $_POST['item_name'][$i], $_POST['item_description'][$i], $_POST['item_price'][$i], $_POST['item_quantity'][$i] ]);
                $delete_list[] = $pdo->lastInsertId();
            } else {
                // Update existing item
                $stmt = $pdo->prepare('UPDATE invoice_items SET invoice_number = ?, item_name = ?, item_description = ?, item_price = ?, item_quantity = ? WHERE id = ?');
                $stmt->execute([ $invoice_number, $_POST['item_name'][$i], $_POST['item_description'][$i], $_POST['item_price'][$i], $_POST['item_quantity'][$i], $_POST['item_id'][$i] ]);
                $delete_list[] = $_POST['item_id'][$i];          
            }
        }
        // Delete item
        $in  = str_repeat('?,', count($delete_list) - 1) . '?';
        $stmt = $pdo->prepare('DELETE FROM invoice_items WHERE invoice_number = ? AND id NOT IN (' . $in . ')');
        $stmt->execute(array_merge([ $invoice_number ], $delete_list));
    } else {
        // No item exists, delete all
        $stmt = $pdo->prepare('DELETE FROM invoice_items WHERE invoice_number = ?');
        $stmt->execute([ $invoice_number ]);       
    }
}
?>