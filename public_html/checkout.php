<?php
/*
PAGE NAME  : checkout.php
LOCATION   : public_html/checkout.php
DESCRIPTION: Checkout page for processing orders
FUNCTION   : Collect billing/shipping info, calculate totals, process payment
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

// Default values for the input form elements
$account = [
    'first_name' => '',
    'last_name' => '',
    'address_street' => '',
    'address_city' => '',
    'address_state' => '',
    'address_zip' => '',
    'address_country' => 'United States',
    'role' => 'Member'
];

// Error array - output errors on the form
$errors = [];

// Redirect the user if the shopping cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Check if user is logged in using main site authentication
global $logged_in, $rowusers;
if ($logged_in && $rowusers) {
    // Use account data from main site authentication
    $account = $rowusers;
}

// Update discount code
if (isset($_POST['discount_code']) && !empty($_POST['discount_code'])) {
    $_SESSION['discount'] = $_POST['discount_code'];
} else if (isset($_POST['discount_code'], $_SESSION['discount']) && empty($_POST['discount_code'])) {
    unset($_SESSION['discount']);
}

// Variables
$products_in_cart = $_SESSION['cart'];
$subtotal = 0.00;
$shipping_total = 0.00;
$discount = null;
$discount_total = 0.00;
$tax_total = 0.00;
$tax_rate = 0.00;
$weight_total = 0;
$selected_country = isset($_POST['address_country']) ? $_POST['address_country'] : $account['address_country'];
$selected_shipping_method = isset($_POST['shipping_method']) ? $_POST['shipping_method'] : null;
$selected_shipping_method_name = '';
$shipping_methods_available = [];

// Get products from database and calculate totals
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
                $weight_total += (int)$product['weight'] * (int)$cart_product['quantity'];
            }
        }
    }
}

// Calculate shipping (simplified for now)
$shipping_total = 9.99; // Basic shipping rate

// Calculate tax (simplified for now)
$tax_rate = 0.08; // 8% tax rate
$tax_total = ($subtotal + $shipping_total) * $tax_rate;

// Calculate total
$total = $subtotal + $shipping_total + $tax_total - $discount_total;

// Process checkout form submission
if (isset($_POST['placeorder'])) {
    // Validation
    $required_fields = ['first_name', 'last_name', 'address_street', 'address_city', 'address_state', 'address_zip', 'address_country'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucwords(str_replace('_', ' ', $field)) . ' is required.';
        }
    }
    
    if (empty($errors)) {
        // Process the order - redirect to payment processing
        header('Location: placeorder.php');
        exit;
    }
}
?>

<!-- Checkout -->
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header accent-background">
                    <i class="fas fa-credit-card"></i> Checkout
                </div>
                <div class="card-body">
                    <?php if ($errors): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?=$error?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="checkout.php" method="post">
                        <div class="row">
                            <!-- Billing Information -->
                            <div class="col-lg-8">
                                <h4><i class="fas fa-user"></i> Billing Information</h4>
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">First Name *</label>
                                        <input type="text" name="first_name" class="form-control" value="<?=htmlspecialchars($account['first_name'] ?? '', ENT_QUOTES)?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Last Name *</label>
                                        <input type="text" name="last_name" class="form-control" value="<?=htmlspecialchars($account['last_name'] ?? '', ENT_QUOTES)?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Street Address *</label>
                                    <input type="text" name="address_street" class="form-control" value="<?=htmlspecialchars($account['address_street'] ?? '', ENT_QUOTES)?>" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">City *</label>
                                        <input type="text" name="address_city" class="form-control" value="<?=htmlspecialchars($account['address_city'] ?? '', ENT_QUOTES)?>" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">State *</label>
                                        <input type="text" name="address_state" class="form-control" value="<?=htmlspecialchars($account['address_state'] ?? '', ENT_QUOTES)?>" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">ZIP Code *</label>
                                        <input type="text" name="address_zip" class="form-control" value="<?=htmlspecialchars($account['address_zip'] ?? '', ENT_QUOTES)?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Country *</label>
                                    <select name="address_country" class="form-select" required>
                                        <option value="United States" <?=$selected_country == 'United States' ? 'selected' : ''?>>United States</option>
                                        <option value="Canada" <?=$selected_country == 'Canada' ? 'selected' : ''?>>Canada</option>
                                        <option value="United Kingdom" <?=$selected_country == 'United Kingdom' ? 'selected' : ''?>>United Kingdom</option>
                                        <!-- Add more countries as needed -->
                                    </select>
                                </div>

                                <h4 class="mt-4"><i class="fas fa-truck"></i> Shipping Method</h4>
                                <hr>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="standard" value="standard" checked>
                                    <label class="form-check-label" for="standard">
                                        <strong>Standard Shipping</strong> - <?=currency_code?><?=num_format($shipping_total, 2)?> 
                                        <br><small class="text-muted">5-7 business days</small>
                                    </label>
                                </div>

                                <h4 class="mt-4"><i class="fas fa-credit-card"></i> Payment Method</h4>
                                <hr>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal" checked>
                                    <label class="form-check-label" for="paypal">
                                        <i class="fab fa-paypal"></i> PayPal
                                    </label>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-receipt"></i> Order Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Cart Items -->
                                        <div class="mb-3">
                                            <?php foreach ($products_in_cart as $product): ?>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <div>
                                                        <small class="fw-bold"><?=htmlspecialchars($product['meta']['title'], ENT_QUOTES)?></small>
                                                        <br><small class="text-muted">Qty: <?=$product['quantity']?></small>
                                                    </div>
                                                    <small><?=currency_code?><?=num_format($product['options_price'] * $product['quantity'], 2)?></small>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <hr>

                                        <!-- Totals -->
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span><?=currency_code?><?=num_format($subtotal, 2)?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Shipping:</span>
                                            <span><?=currency_code?><?=num_format($shipping_total, 2)?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Tax:</span>
                                            <span><?=currency_code?><?=num_format($tax_total, 2)?></span>
                                        </div>
                                        
                                        <hr>
                                        
                                        <div class="d-flex justify-content-between mb-3">
                                            <strong>Total:</strong>
                                            <strong class="text-primary"><?=currency_code?><?=num_format($total, 2)?></strong>
                                        </div>

                                        <!-- Discount Code -->
                                        <div class="mb-3">
                                            <label class="form-label">Discount Code</label>
                                            <div class="input-group">
                                                <input type="text" name="discount_code" class="form-control" placeholder="Enter code" value="<?=$_SESSION['discount'] ?? ''?>">
                                                <button type="submit" class="btn btn-outline-secondary">Apply</button>
                                            </div>
                                        </div>

                                        <div class="d-grid">
                                            <button type="submit" name="placeorder" class="btn btn-success btn-lg">
                                                <i class="fas fa-lock"></i> Place Order
                                            </button>
                                        </div>

                                        <div class="text-center mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-shield-alt"></i> Secure checkout powered by PayPal
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Continue Shopping -->
                                <div class="text-center mt-3">
                                    <a href="products.php" class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left"></i> Continue Shopping
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once "assets/includes/footer.php"; ?>
