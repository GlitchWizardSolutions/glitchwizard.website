<?php
// Include the main file
include 'main.php';
// Get ID
if (!isset($_GET['id'])) {
    exit('Invoice ID not specified!');
}
// Get the invoice
$stmt = $pdo->prepare('SELECT * FROM invoices WHERE invoice_number = ?');
$stmt->execute([ $_GET['id'] ]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);
// Check if invoice exists
if (!$invoice) {
    exit('Invoice does not exist!');
}
// Check if the invoice has been paid
if ($invoice['payment_status'] == 'Paid') {
    exit('Invoice has already been paid!');
}
// Check if not unpaid
if ($invoice['payment_status'] != 'Unpaid') {
    exit('You cannot pay for this invoice!');
}
// Get the client
$stmt = $pdo->prepare('SELECT * FROM invoice_clients WHERE id = ?');
$stmt->execute([ $invoice['client_id'] ]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);
// Validate client
if (!$client) {
    exit('Could not retrieve client details!');
}
// Get payment methods
$payment_methods = explode(', ', $invoice['payment_methods']);
// Process paypal payment
if (isset($_POST['method']) && $_POST['method'] == 'paypal' && paypal_enabled && in_array('PayPal', $payment_methods)) {
    // Process paypal standard checkout
    $data = [
        'cmd' => '_xclick',
        'charset' => 'UTF-8',
        'business' => paypal_email,
        'notify_url' => paypal_ipn_url,
        'currency_code'	=> paypal_currency,
        'item_name' => 'Invoice ' . $invoice['invoice_number'],
        'item_number' => $invoice['invoice_number'],
        'amount' => $invoice['payment_amount']+$invoice['tax_total'],
        'no_shipping' => 1,
        'no_note' => 1,
        'return' => base_url . 'invoice.php?id=' . $invoice['invoice_number'] . '&success=true',
        'cancel_return' => base_url . 'invoice.php?id=' . $invoice['invoice_number'] . '&cancel=true',
        'custom' => $invoice['invoice_number']
    ];
    // Redirect to paypal
    header('Location: ' . (paypal_testmode ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr') . '?' . http_build_query($data));
    exit;
}
// Process stripe payment
if (isset($_POST['method']) && $_POST['method'] == 'stripe' && stripe_enabled && in_array('Stripe', $payment_methods)) {
    // Include the Stripe PHP library
    require_once 'lib/stripe/init.php';
    // Set the API key
    $stripe = new \Stripe\StripeClient(stripe_secret_key);
    // Check the webhook secret
    if (empty(stripe_webhook_secret)) {
        // No webhook secret, attempt to create one
        // Get the config.php file contents
        $contents = file_get_contents('config.php');
        if ($contents) {
            // Attempt to create the webhook and get the secret
            $webhook = $stripe->webhookEndpoints->create([
                'url' => stripe_ipn_url,
                'description' => 'invoicesystem', // Feel free to change this
                'enabled_events' => ['checkout.session.completed']
            ]);
            // Update the "stripe_webhook_secret" constant in the config.php file with the new secret
            $contents = preg_replace('/define\(\'stripe_webhook_secret\'\, ?(.*?)\)/s', 'define(\'stripe_webhook_secret\',\'' . $webhook['secret'] . '\')', $contents);
            if (!file_put_contents('config.php', $contents)) {
                // Could not write to config.php file
                exit('Failed to automatically assign the Stripe webhook secret! Please set it manually in the config.php file.');
            }
        } else {
            // Could not open config.php file
            exit('Failed to automatically assign the Stripe webhook secret! Please set it manually in the config.php file.');
        }
    }
    // Create the session
    $session = $stripe->checkout->sessions->create([
        'payment_method_types' => ['card'],
        'line_items' => [
            [
                'quantity' => 1,
                'price_data' => [
                    'currency' => stripe_currency,
                    'product_data' => [
                        'name' => 'Invoice #' . $invoice['invoice_number'],
                        'description' => 'Payment for invoice #' . $invoice['invoice_number'],
                    ],
                    'unit_amount' => ($invoice['payment_amount']+$invoice['tax_total']) * 100,
                ]
            ]
        ],
        'mode' => 'payment',
        'success_url' => base_url . 'invoice.php?id=' . $invoice['invoice_number'] . '&success=true',
        'cancel_url' => base_url . 'invoice.php?id=' . $invoice['invoice_number'] . '&cancel=true',
        'metadata' => [
            'invoice_id' => $invoice['invoice_number']
        ]
    ]);
    // Redirect to the checkout session
    header('Location:' . $session->url);
	exit;
}
// Process coinbase payment
if (isset($_POST['method']) && $_POST['method'] == 'coinbase' && coinbase_enabled && in_array('Coinbase', $payment_methods)) {
    // Include the coinbase library
    require_once 'lib/vendor/autoload.php';
    $coinbase = CoinbaseCommerce\ApiClient::init(coinbase_key);  
    // Create a charge
    $chargeData = [
        'name' => 'Invoice #' . $invoice['invoice_number'],
        'description' => 'Payment for invoice #' . $invoice['invoice_number'],
        'local_price' => [
            'amount' => $invoice['payment_amount']+$invoice['tax_total'],
            'currency' => coinbase_currency
        ],
        'pricing_type' => 'fixed_price',
        'metadata' => [
            'invoice_id' => $invoice['invoice_number']
        ],
        'redirect_url' => base_url . 'invoice.php?id=' . $invoice['invoice_number'] . '&success=true',
        'cancel_url' => base_url . 'invoice.php?id=' . $invoice['invoice_number'] . '&cancel=true'
    ];
    $charge = CoinbaseCommerce\Resources\Charge::create($chargeData);
    // Redirect to the charge checkout
    header('Location: ' . $charge->hosted_url);
    exit;
}
// Process bank transfer or cash payment
if (isset($_POST['method']) && ($_POST['method'] == 'banktransfer' || $_POST['method'] == 'cash') && (in_array('Bank Transfer', $payment_methods) || in_array('Cash', $payment_methods))) {
    // Paid with
    $paid_with = $_POST['method'] == 'banktransfer' ? 'Bank Transfer' : 'Cash';
    // Generate unique transaction id
    $transaction_id = 'TXN' . time();
    // Update the invoice
    $stmt = $pdo->prepare('UPDATE invoices SET payment_status = ?, paid_with = ?, payment_ref = ? WHERE invoice_number = ?');
    $stmt->execute([ 'Pending', $paid_with, $transaction_id, $_GET['id'] ]);
    // Redirect to the invoice
    header('Location: invoice.php?id=' . $_GET['id']);
    exit;
}
?>