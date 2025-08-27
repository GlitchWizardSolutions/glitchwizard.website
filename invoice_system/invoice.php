<?php
// Include the main file
include 'main.php';
// Check if invoice ID param exists
if (!isset($_GET['id'])) {
    exit('Invoice ID not specified!');
}
// Retrieve the invoice from the database
$stmt = $pdo->prepare('SELECT * FROM invoices WHERE invoice_number = ?');
$stmt->execute([ $_GET['id'] ]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);
// Check if invoice exists
if (!$invoice) {
    exit('Invoice does not exist!');
}
// Get invoice items
$stmt = $pdo->prepare('SELECT * FROM invoice_items WHERE invoice_number = ?');
$stmt->execute([ $invoice['invoice_number'] ]);
$invoice_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get client details
$stmt = $pdo->prepare('SELECT * FROM invoice_clients WHERE id = ?');
$stmt->execute([ $invoice['client_id'] ]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);
// Client address
$client_address = [
    $client['address_street'],
    $client['address_city'],
    $client['address_state'],
    $client['address_zip'],
    $client['address_country']
];
// remove any empty values
$client_address = array_filter($client_address);
// Get payment methods
$payment_methods = explode(', ', $invoice['payment_methods']);
// Determine correct ip address
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
// Get ip addresses from the accounts table
$stmt = $pdo->prepare('SELECT ip FROM accounts');
$stmt->execute();
$ips = $stmt->fetchAll(PDO::FETCH_COLUMN);
// Update invoice viewed status and sure the IP doesn't match one of the ip address in the accounts table
if ($invoice['viewed'] == 0 && !in_array($ip, $ips)) {
    $stmt = $pdo->prepare('UPDATE invoices SET viewed = 1 WHERE invoice_number = ?');
    $stmt->execute([ $invoice['invoice_number'] ]);
}
// define invoice, which will prevent direct access to the template
define('INVOICE', true);
// Include the template
if (file_exists(base_path . 'templates/' . $invoice['invoice_template'] . '/template.php')) {
    // set template path
    define('template_path', base_url . 'templates/' . $invoice['invoice_template'] . '/');
    // include the template
    require base_path . 'templates/' . $invoice['invoice_template'] . '/template.php';
} else if (file_exists(base_path . 'templates/default/template.php')) {
    // set template path
    define('template_path', base_url . 'templates/default/');
    // include the default template
    require base_path . 'templates/default/template.php';
} else {
    exit('No template could be found!');
}
?>