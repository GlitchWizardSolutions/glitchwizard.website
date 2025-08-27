<?php
/*
FILE NAME  : paypal.php
LOCATION   : shop_system/ipn/paypal.php
DESCRIPTION: Enhanced PayPal IPN handler with security and logging
FUNCTION   : Process PayPal Instant Payment Notifications securely
UPDATED    : August 2025 - Shop Integration Step 4 Enhancement
*/

// Remove time limit for IPN processing
set_time_limit(0);

// Include the necessary files
include '../config.php';
include '../functions.php';

// Enhanced logging function
function logIPN($message, $data = []) {
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => $message,
        'data' => $data,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ];
    
    $log_file = '../logs/ipn_' . date('Y-m') . '.log';
    $log_dir = dirname($log_file);
    
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    file_put_contents($log_file, json_encode($log_entry) . "\n", FILE_APPEND | LOCK_EX);
}

// Enhanced security check
function validateIPNSource() {
    $valid_ips = [
        '173.0.80.0/20',
        '64.4.240.0/21',
        '66.211.168.0/22',
        '147.75.0.0/16',
        '173.0.84.0/22'
    ];
    
    $client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
    
    foreach ($valid_ips as $ip_range) {
        if (ip_in_range($client_ip, $ip_range)) {
            return true;
        }
    }
    
    return false;
}

function ip_in_range($ip, $range) {
    if (strpos($range, '/') !== false) {
        list($range, $netmask) = explode('/', $range, 2);
        $range_decimal = ip2long($range);
        $ip_decimal = ip2long($ip);
        $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
        $netmask_decimal = ~ $wildcard_decimal;
        return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
    } else {
        return (ip2long($ip) == ip2long($range));
    }
}

// Security validation
if (!validateIPNSource()) {
    logIPN('IPN Security Error: Invalid source IP', ['ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
    http_response_code(403);
    exit('Access denied');
}

// Get raw POST data
$raw_post_data = file_get_contents('php://input');
if (empty($raw_post_data)) {
    logIPN('IPN Error: Empty POST data');
    exit('No data received');
}

logIPN('IPN Received', ['raw_data_length' => strlen($raw_post_data)]);

// Parse POST data
$raw_post_array = explode('&', $raw_post_data);
$post_data = [];

foreach ($raw_post_array as $keyval) {
    $keyval = explode('=', $keyval);
    if (count($keyval) == 2) {
        // Handle special characters in payment_date
        if ($keyval[0] === 'payment_date') {
            if (substr_count($keyval[1], '+') === 1) {
                $keyval[1] = str_replace('+', '%2B', $keyval[1]);
            }
        }
        $post_data[$keyval[0]] = urldecode($keyval[1]);
    }
}

// Log received data (excluding sensitive info)
$log_data = $post_data;
unset($log_data['test_ipn']); // Remove test indicator for cleaner logs
logIPN('IPN Data Parsed', [
    'txn_id' => $post_data['txn_id'] ?? 'unknown',
    'payment_status' => $post_data['payment_status'] ?? 'unknown',
    'payment_amount' => $post_data['mc_gross'] ?? 'unknown'
]);

// Build verification request
$req = 'cmd=_notify-validate';
foreach ($post_data as $key => $value) {
    $value = urlencode($value);
    $req .= "&$key=$value";
}

// Enhanced PayPal verification with better error handling
$paypal_url = paypal_testmode ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
$ch = curl_init($paypal_url);

// Enhanced cURL settings for security
curl_setopt_array($ch, [
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_POST => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_POSTFIELDS => $req,
    CURLOPT_SSL_VERIFYPEER => 1,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_FORBID_REUSE => 1,
    CURLOPT_HTTPHEADER => ['Connection: Close', 'User-Agent: PHP-IPN-Verification-Script'],
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_MAXREDIRS => 0
]);

$res = curl_exec($ch);
$curl_error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Check for cURL errors
if ($curl_error) {
    logIPN('IPN cURL Error', ['error' => $curl_error, 'http_code' => $http_code]);
    exit('Verification failed');
}

if ($http_code !== 200) {
    logIPN('IPN HTTP Error', ['http_code' => $http_code]);
    exit('Verification failed');
}

logIPN('IPN Verification Response', ['response' => $res, 'http_code' => $http_code]);

// Check if the transaction is verified
if (strcmp($res, 'VERIFIED') == 0) {
    logIPN('IPN Verified Successfully', ['txn_id' => $post_data['txn_id'] ?? 'unknown']);
    
    // Enhanced database connection with error handling
    try {
        $pdo = pdo_connect_mysql();
    } catch (Exception $e) {
        logIPN('Database Connection Error', ['error' => $e->getMessage()]);
        exit('Database error');
    }
    
    // Enhanced duplicate transaction check
    if (isset($post_data['txn_id'])) {
        $stmt = $pdo->prepare('SELECT id FROM shop_transactions WHERE txn_id = ?');
        $stmt->execute([$post_data['txn_id']]);
        
        if ($stmt->fetch()) {
            logIPN('Duplicate Transaction Ignored', ['txn_id' => $post_data['txn_id']]);
            exit('Duplicate transaction');
        }
    }
    
    // Handle Refunds with enhanced logging
    if (isset($post_data['txn_type']) && $post_data['txn_type'] == 'refund') {
        $stmt = $pdo->prepare('UPDATE shop_transactions SET payment_status = ? WHERE txn_id = ?');
        $stmt->execute(['Refunded', $post_data['parent_txn_id']]);
        
        logIPN('Refund Processed', [
            'parent_txn_id' => $post_data['parent_txn_id'],
            'refund_amount' => $post_data['mc_gross'] ?? 'unknown'
        ]);
        exit('Refund processed');
    }
    // Check if the transaction type is a cart
    if ($_POST['txn_type'] == 'cart') {
        // Variables
        $products = [];
        $subtotal = 0.00;
        $shipping_total = isset($_POST['mc_shipping1']) ? floatval($_POST['mc_shipping1']) : 0.00;
        $handling_total = isset($_POST['mc_handling1']) ? floatval($_POST['mc_handling1']) : 0.00;
        $payment_status = $_POST['payment_status'] == 'Completed' ? default_payment_status : $_POST['payment_status'];
        // Retrieve custom data (account_id, discount_code)
        $custom = isset($_POST['custom']) ? json_decode($_POST['custom'], true) : [];
        // Assign custom variables
        $account_id = isset($custom['account_id']) ? $custom['account_id'] : null;
        $discount_code = isset($custom['discount_code']) ? $custom['discount_code'] : '';
        $shipping_method = isset($custom['shipping_method']) ? $custom['shipping_method'] : '';
        // Customer variables
        $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
        $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
        $payer_email = isset($_POST['payer_email']) ? $_POST['payer_email'] : '';
        $address_street = isset($_POST['address_street']) ? $_POST['address_street'] : '';
        $address_city = isset($_POST['address_city']) ? $_POST['address_city'] : '';
        $address_state = isset($_POST['address_state']) ? $_POST['address_state'] : '';
        $address_zip = isset($_POST['address_zip']) ? $_POST['address_zip'] : '';
        $address_country = isset($_POST['address_country']) ? $_POST['address_country'] : '';
        // Check if account exists with the account_id
        if ($account_id) {
            $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
            $stmt->execute([ $account_id ]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($account) {
                // Assign account variables
                $payer_email = empty($payer_email) ? $account['email'] : $payer_email;
                $first_name = empty($first_name) ? $account['first_name'] : $first_name;
                $last_name = empty($last_name) ? $account['last_name'] : $last_name;
                $address_street = empty($address_street) ? $account['address_street'] : $address_street;
                $address_city = empty($address_city) ? $account['address_city'] : $address_city;
                $address_state = empty($address_state) ? $account['address_state'] : $address_state;
                $address_zip = empty($address_zip) ? $account['address_zip'] : $address_zip;
                $address_country = empty($address_country) ? $account['address_country'] : $address_country;
            }
        }
        // Tax calculation
        $tax_amount = 0.00;
        // Iterate the cart items and insert the transaction items into the MySQL database
        for ($i = 1; $i < (intval($_POST['num_cart_items'])+1); $i++) {
            // Check if item is tax
            if ($_POST['item_number' . $i] == 'tax') {
                $tax_amount = floatval($_POST['mc_gross_' . $i]);
                continue;
            }
            // Update product quantity in the products table
            $stmt = $pdo->prepare('UPDATE shop_products SET quantity = GREATEST(quantity - ?, 0) WHERE quantity > 0 AND id = ?');
            $stmt->execute([ $_POST['quantity' . $i], $_POST['item_number' . $i] ]);
            // Product related variables
            $option = isset($_POST['option_selection1_' . $i]) ? $_POST['option_selection1_' . $i] : '';
            $option = $option == 'N/A' ? '' : $option;
            // Deduct option quantities
            if ($option) {
                $options = explode(',', $option);
                foreach ($options as $opt) {
                    $option_name = explode('-', $opt)[0];
                    $option_value = explode('-', $opt)[1];
                    $stmt = $pdo->prepare('UPDATE shop_product_options SET quantity = GREATEST(quantity - ?, 0) WHERE quantity > 0 AND option_name = ? AND (option_value = ? OR option_value = "") AND product_id = ?');
                    $stmt->execute([ $_POST['quantity' . $i], $option_name, $option_value, $_POST['item_number' . $i] ]);         
                }
            }
            // For some reason PayPal sends the shipping amount with the first item, so we need to deduct it from the first item's price
            $gross = $i == 1 ? floatval($_POST['mc_gross_' . $i]) - $shipping_total - $handling_total : floatval($_POST['mc_gross_' . $i]);
            // If thats not the case, uncomment the line below
            // $gross = floatval($_POST['mc_gross_' . $i]);
            // Determine the price of the item
            $item_price = $gross / intval($_POST['quantity' . $i]);
            // Insert product into the "transaction_items" table
            $stmt = $pdo->prepare('INSERT INTO shop_transaction_items (txn_id, item_id, item_price, item_quantity, item_options) VALUES (?,?,?,?,?)');
            $stmt->execute([ $_POST['txn_id'], $_POST['item_number' . $i], $item_price, $_POST['quantity' . $i], $option ]);
            // Add product to array
            $products[] = [
                'id' => $_POST['item_number' . $i],
                'quantity' => $_POST['quantity' . $i],
                'options' => $option,
                'final_price' => $item_price,
                'meta' => [
                    'title' => $_POST['item_name' . $i],
                    'price' => $item_price
                ]
            ];
            // Add product price to the subtotal variable
            $subtotal += $item_price * intval($_POST['quantity' . $i]);
        }
        // Calculate total
        $total = $subtotal + $shipping_total + $tax_amount;
        // Insert the transaction into our transactions table, as the payment status changes the query will execute again and update it, make sure the "txn_id" column is unique
        $stmt = $pdo->prepare('INSERT INTO shop_transactions (txn_id, payment_amount, payment_status, created, payer_email, first_name, last_name, address_street, address_city, address_state, address_zip, address_country, account_id, payment_method, shipping_method, shipping_amount, discount_code, tax_amount) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE payment_status = VALUES(payment_status)');
        $stmt->execute([ $_POST['txn_id'], $total, $payment_status, date('Y-m-d H:i:s'), $payer_email, $first_name, $last_name, $address_street, $address_city, $address_state, $address_zip, $address_country, $account_id, 'paypal', $shipping_method, $shipping_total, $discount_code, $tax_amount ]);
        $order_id = $pdo->lastInsertId();
        // Send order details to the customer's email address
        if ($_POST['payment_status'] == 'Completed') {
            $send_to_email = isset($account) && $account ? $account['email'] : $payer_email;
            send_order_details_email($send_to_email, $products, $first_name, $last_name, $address_street, $address_city, $address_state, $address_zip, $address_country, $total, $order_id);
        }
    }
    // Check if the transaction type is a subscription
    if ($_POST['txn_type'] == 'subscr_payment' && isset($_POST['payment_status'])) {
        // Variables
        $account_id = $_POST['custom'];
        $product_id = $_POST['item_number'];
        $product_name = $_POST['item_name'];
        $product_options = isset($_POST['option_selection1']) && !empty($_POST['option_selection1']) ? $_POST['option_selection1'] : '';
        $product_options = $product_options == 'N/A' ? '' : $product_options;
        $subscription_id = $_POST['subscr_id'];
        $subscription_status = $_POST['payment_status'] == 'Completed' ? 'Subscribed' : $_POST['payment_status'];
        $subscription_price = $_POST['mc_gross'];
        $subscription_created = date('Y-m-d H:i:s');
        // Check if account exists with the account_id
        if ($account_id) {
            $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
            $stmt->execute([ $account_id ]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($account) {
                // Update product quantity in the products table
                $stmt = $pdo->prepare('UPDATE shop_products SET quantity = GREATEST(quantity - ?, 0) WHERE quantity > 0 AND id = ?');
                $stmt->execute([ 1, $product_id ]);
                // Deduct option quantities
                if ($product_options) {
                    $options = explode(',', $product_options);
                    foreach ($options as $opt) {
                        $option_name = explode('-', $opt)[0];
                        $option_value = explode('-', $opt)[1];
                        $stmt = $pdo->prepare('UPDATE shop_product_options SET quantity = GREATEST(quantity - ?, 0) WHERE quantity > 0 AND option_name = ? AND (option_value = ? OR option_value = "") AND product_id = ?');
                        $stmt->execute([ 1, $option_name, $option_value, $product_id ]);         
                    }
                }
                // INSERT INTO shop_transactions
                $stmt = $pdo->prepare('INSERT INTO shop_transactions (txn_id, payment_amount, payment_status, created, payer_email, first_name, last_name, address_street, address_city, address_state, address_zip, address_country, account_id, payment_method, shipping_method, shipping_amount, discount_code) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE payment_status = VALUES(payment_status)');
                $stmt->execute([ $subscription_id, $subscription_price, $subscription_status, $subscription_created, isset($_POST['payer_email']) ? $_POST['payer_email'] : $account['email'], $account['first_name'], $account['last_name'], $account['address_street'], $account['address_city'], $account['address_state'], $account['address_zip'], $account['address_country'], $account_id, 'paypal', '', 0.00, '' ]);
                // Get insert id
                $order_id = $pdo->lastInsertId();
                // Insert product into the "transaction_items" table
                $stmt = $pdo->prepare('INSERT INTO shop_transaction_items (txn_id, item_id, item_price, item_quantity, item_options) VALUES (?,?,?,?,?)');
                $stmt->execute([ $subscription_id, $product_id, $subscription_price, 1, $product_options ]);
                // Send subscription details to the customer's email address
                if ($subscription_status == 'Subscribed') {
                    $products = [
                        [
                            'id' => $product_id,
                            'quantity' => 1,
                            'options' => $product_options,
                            'final_price' => $subscription_price,
                            'meta' => [
                                'title' => $product_name,
                                'price' => $subscription_price
                            ]
                        ]
                    ];
                    send_order_details_email($account['email'], $products, $account['first_name'], $account['last_name'], $account['address_street'], $account['address_city'], $account['address_state'], $account['address_zip'], $account['address_country'], $subscription_price, $order_id);
                }
            }
        }
    }
    // Check if the transaction type is a subscription cancellation, end of term, or failed
    if ($_POST['txn_type'] == 'subscr_cancel' || $_POST['txn_type'] == 'subscr_eot' || $_POST['txn_type'] == 'subscr_failed') {
        // Variables
        $stmt = $pdo->prepare('UPDATE shop_transactions SET payment_status = ? WHERE txn_id = ?');
        $stmt->execute([ 'Unsubscribed', $_POST['subscr_id'] ]);
    }
}
?>