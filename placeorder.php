<?php
/*
PAGE NAME  : placeorder.php
LOCATION   : public_html/placeorder.php
DESCRIPTION: Final order processing and payment integration
FUNCTION   : Process the final order and integrate with PayPal payment
CHANGE LOG : Integrated from shop_system to main site structure
*/

// Include necessary files
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";

// Load shop system functionality
include_once "shop_system/shop_load.php";
include_once "shop_system/functions.php";

// Include shop navigation
include_once "shop_system/shop_nav.php";

// Redirect if no cart
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Check if user is logged in
global $logged_in, $rowusers;
if (!$logged_in || !$rowusers) {
    header('Location: auth.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$account = $rowusers;
$products_in_cart = $_SESSION['cart'];
$subtotal = 0.00;
$shipping_total = 9.99;
$tax_total = 0.00;
$total = 0.00;

// Calculate totals
if ($products_in_cart) {
    $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
    $stmt = $pdo->prepare('SELECT * FROM shop_products WHERE id IN (' . $array_to_question_marks . ')');
    $stmt->execute(array_column($products_in_cart, 'id'));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products_in_cart as &$cart_product) {
        foreach ($products as $product) {
            if ($cart_product['id'] == $product['id']) {
                $cart_product['meta'] = $product;
                $subtotal += (float)$cart_product['options_price'] * (int)$cart_product['quantity'];
            }
        }
    }
}

$tax_total = ($subtotal + $shipping_total) * 0.08; // 8% tax
$total = $subtotal + $shipping_total + $tax_total;

// Generate unique transaction ID
$transaction_id = 'TXN_' . time() . '_' . rand(1000, 9999);
?>

<!-- Place Order -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header accent-background text-center">
                    <h4 class="mb-0"><i class="bi bi-credit-card-2-front" aria-hidden="true"></i> Complete Your Order</h4>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5><i class="bi bi-person-circle" aria-hidden="true"></i> Billing Information</h5>
                            <address>
                                <strong><?=htmlspecialchars($account['first_name'] . ' ' . $account['last_name'], ENT_QUOTES)?></strong><br>
                                <?=htmlspecialchars($account['address_street'] ?? '', ENT_QUOTES)?><br>
                                <?=htmlspecialchars($account['address_city'] ?? '', ENT_QUOTES)?>, 
                                <?=htmlspecialchars($account['address_state'] ?? '', ENT_QUOTES)?> 
                                <?=htmlspecialchars($account['address_zip'] ?? '', ENT_QUOTES)?><br>
                                <?=htmlspecialchars($account['address_country'] ?? '', ENT_QUOTES)?>
                            </address>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="bi bi-receipt" aria-hidden="true"></i> Order Total</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td>Subtotal:</td>
                                    <td class="text-end"><?=currency_code?><?=num_format($subtotal, 2)?></td>
                                </tr>
                                <tr>
                                    <td>Shipping:</td>
                                    <td class="text-end"><?=currency_code?><?=num_format($shipping_total, 2)?></td>
                                </tr>
                                <tr>
                                    <td>Tax:</td>
                                    <td class="text-end"><?=currency_code?><?=num_format($tax_total, 2)?></td>
                                </tr>
                                <tr class="table-active">
                                    <td><strong>Total:</strong></td>
                                    <td class="text-end"><strong><?=currency_code?><?=num_format($total, 2)?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <h5><i class="bi bi-bag-check" aria-hidden="true"></i> Order Items</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products_in_cart as $product): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($product['meta']['img']) && file_exists($product['meta']['img'])): ?>
                                                    <img src="<?=$product['meta']['img']?>" width="50" height="50" class="img-thumbnail me-3">
                                                <?php endif; ?>
                                                <div>
                                                    <?=htmlspecialchars($product['meta']['title'], ENT_QUOTES)?>
                                                    <?php if ($product['options']): ?>
                                                        <br><small class="text-muted"><?=str_replace(',', ', ', htmlspecialchars($product['options'], ENT_QUOTES))?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?=$product['quantity']?></td>
                                        <td><?=currency_code?><?=num_format($product['options_price'], 2)?></td>
                                        <td><?=currency_code?><?=num_format($product['options_price'] * $product['quantity'], 2)?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment Options -->
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="text-center mb-4">
                                <h5><i class="bi bi-shield-lock" aria-hidden="true"></i> Secure Payment</h5>
                                <p class="text-muted">Choose your payment method to complete your order.</p>
                            </div>

                            <!-- PayPal Payment Form -->
                            <div class="card mb-3">
                                <div class="card-body text-center">
                                    <h6><span class="align-middle me-1" aria-hidden="true" style="display:inline-block;width:20px;height:20px;vertical-align:middle;"><?php include __DIR__ . '/assets/icons/paypal.svg'; ?></span> Pay with PayPal</h6>
                                    <p class="text-muted mb-3">Pay securely using your PayPal account or credit card.</p>
                                    
                                    <?php if (defined('paypal_enabled') && paypal_enabled): ?>
                                        <!-- PayPal Form -->
                                        <?php 
                                        $paypal_url = paypal_sandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
                                        ?>
                                        <form action="<?=$paypal_url?>" method="post" id="paypal-form">
                                            <input type="hidden" name="business" value="<?=paypal_business?>">
                                            <input type="hidden" name="item_name" value="Order #<?=$transaction_id?>">
                                            <input type="hidden" name="item_number" value="<?=$transaction_id?>">
                                            <input type="hidden" name="amount" value="<?=$total?>">
                                            <input type="hidden" name="currency_code" value="USD">
                                            <input type="hidden" name="return" value="<?=$_SERVER['HTTP_HOST']?>/myaccount.php?success=1">
                                            <input type="hidden" name="cancel_return" value="<?=$_SERVER['HTTP_HOST']?>/checkout.php">
                                            <input type="hidden" name="notify_url" value="<?=$_SERVER['HTTP_HOST']?>/shop_system/ipn/paypal.php">
                                            <input type="hidden" name="custom" value="<?=$account['id']?>">
                                            <input type="hidden" name="cmd" value="_xclick">
                                            
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <span class="align-middle me-1" aria-hidden="true" style="display:inline-block;width:20px;height:20px;vertical-align:middle;"><?php include __DIR__ . '/assets/icons/paypal.svg'; ?></span> Pay with PayPal - <?=currency_code?><?=num_format($total, 2)?><
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            PayPal payment is currently unavailable. Please contact support to complete your order.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Alternative Payment Methods -->
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6><i class="bi bi-credit-card" aria-hidden="true"></i> Other Payment Options</h6>
                                    <p class="text-muted">Additional payment methods will be available soon.</p>
                                    <button class="btn btn-outline-secondary d-inline-flex align-items-center" disabled>
                                        <span class="me-1" aria-hidden="true" style="display:inline-block;width:20px;height:20px;vertical-align:middle;"><?php include __DIR__ . '/assets/icons/stripe.svg'; ?></span> Stripe (Coming Soon)
                                    </button>
                                </div>
                            </div>

                            <!-- Security Notice -->
                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    <i class="bi bi-shield-check" aria-hidden="true"></i> 
                                    Your payment information is processed securely. We do not store credit card details.
                                </small>
                            </div>

                            <!-- Back to Checkout -->
                            <div class="text-center mt-3">
                                <a href="checkout.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to Checkout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Processing JavaScript -->
<script>
// Save transaction details to session storage for tracking
sessionStorage.setItem('pending_order', JSON.stringify({
    transaction_id: '<?=$transaction_id?>',
    total: <?=$total?>,
    timestamp: <?=time()?>
}));

// Optional: Add analytics tracking for order initiation
if (typeof gtag !== 'undefined') {
    gtag('event', 'begin_checkout', {
        currency: 'USD',
        value: <?=$total?>,
        transaction_id: '<?=$transaction_id?>'
    });
}
</script>

<?php include_once "assets/includes/footer.php"; ?>
