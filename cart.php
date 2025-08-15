<?php
/*
PAGE NAME  : cart.php
LOCATION   : public_html/cart.php
DESCRIPTION: Shopping cart page for viewing and managing cart items
FUNCTION   : Users can view cart contents, update quantities, remove items, and proceed to checkout
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

// Cart processing logic
// Remove product from cart, check for the URL param "remove", this is the product id, make sure it's a number and check if it's in the cart
if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['cart']) && isset($_SESSION['cart'][$_GET['remove']])) {
    // Remove the product from the shopping cart
    array_splice($_SESSION['cart'], $_GET['remove'], 1);
    header('Location: cart.php');
    exit;
}

// Empty the cart
if (isset($_POST['emptycart']) && isset($_SESSION['cart'])) {
    // Remove all products from the shopping cart
    unset($_SESSION['cart']);
    header('Location: cart.php');
    exit;
}

// Update product quantities in cart if the user clicks the "Update" button on the shopping cart page
if ((isset($_POST['update']) || isset($_POST['checkout'])) && isset($_SESSION['cart'])) {
    // Iterate the post data and update quantities for every product in cart
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'quantity') !== false && is_numeric($v)) {
            $id = str_replace('quantity-', '', $k);
            // abs() function will prevent minus quantity and (int) will ensure the value is an integer (number)
            $quantity = abs((int)$v);
            // Always do checks and validation
            if (is_numeric($id) && isset($_SESSION['cart'][$id]) && $quantity > 0) {
                // Can update the quantity?
                $canUpdate = true;
                // Check if product has options
                if ($_SESSION['cart'][$id]['options']) {
                    $options = explode(',', $_SESSION['cart'][$id]['options']);
                    foreach ($options as $opt) {
                        $option_name = explode('-', $opt)[0];
                        $option_value = explode('-', $opt)[1];
                        $stmt = $pdo->prepare('SELECT * FROM shop_product_options WHERE option_name = ? AND (option_value = ? OR option_value = "") AND product_id = ?');   
                        $stmt->execute([ $option_name, $option_value, $_SESSION['cart'][$id]['id'] ]);
                        $option = $stmt->fetch(PDO::FETCH_ASSOC);   
                        // Get cart option quantity
                        $cart_option_quantity = get_cart_option_quantity($_SESSION['cart'][$id]['id'], $opt);
                        // Check if the option exists and the quantity is available
                        if (!$option) {
                            $canUpdate = false;
                        } elseif ($option['quantity'] != -1 && $option['quantity'] < ($cart_option_quantity-$_SESSION['cart'][$id]['quantity']) + $quantity) {
                            $canUpdate = false;
                        }
                    }
                }
                // Check if the product quantity is available
                $cart_product_quantity = get_cart_product_quantity($_SESSION['cart'][$id]['id']);
                // Get product quantity from the database
                $stmt = $pdo->prepare('SELECT quantity FROM shop_products WHERE id = ?');
                $stmt->execute([ $_SESSION['cart'][$id]['id'] ]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                // Check if the product quantity is available
                if ($product['quantity'] != -1 && $product['quantity'] < ($cart_product_quantity-$_SESSION['cart'][$id]['quantity']) + $quantity) {
                    $canUpdate = false;
                }
                // Update the quantity if can update
                if ($canUpdate) {
                    $_SESSION['cart'][$id]['quantity'] = $quantity;
                }
            }
        }
    }
    // Send the user to the place order page if they click the Place Order button, also the cart should not be empty
    if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
        header('Location: checkout.php');
        exit;
    }
    header('Location: cart.php');
    exit;
}

// Check the session variable for products in cart
$products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$subtotal = 0.00;

// If there are products in cart
if ($products_in_cart) {
    // There are products in the cart so we need to select those products from the database
    // Products in cart array to question mark string array, we need the SQL statement to include: IN (?,?,?,...etc)
    $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
    // Prepare SQL statement
    $stmt = $pdo->prepare('SELECT p.*, (SELECT m.full_path FROM shop_product_media_map pm JOIN shop_product_media m ON m.id = pm.media_id WHERE pm.product_id = p.id ORDER BY pm.position ASC LIMIT 1) AS img FROM shop_products p WHERE p.id IN (' . $array_to_question_marks . ')');
    // Leverage the array_column function to retrieve only the id's of the products
    $stmt->execute(array_column($products_in_cart, 'id'));
    // Fetch the products from the database and return the result as an Array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Iterate the products in cart and add the meta data (product name, desc, etc)
    foreach ($products_in_cart as &$cart_product) {
        foreach ($products as $product) {
            if ($cart_product['id'] == $product['id']) {
                $cart_product['meta'] = $product;
                // Calculate the subtotal
                $subtotal += (float)$cart_product['options_price'] * (int)$cart_product['quantity'];
            }
        }
    }
}
?>

<!-- Shopping Cart Content -->
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header accent-background">
                    <i class="fas fa-shopping-cart"></i> Shopping Cart
                </div>
                <div class="card-body">
                    <form action="cart.php" method="post">
                        <?php if (empty($products_in_cart)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h4>Your shopping cart is empty</h4>
                                <p class="text-muted">Add some products to your cart to get started!</p>
                                <a href="products.php" class="btn btn-primary">
                                    <i class="fas fa-shopping-bag"></i> Browse Products
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Options</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products_in_cart as $num => $product): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($product['meta']['img']) && file_exists($product['meta']['img'])): ?>
                                                        <img src="<?=$product['meta']['img']?>" width="60" height="60" class="img-thumbnail me-3" alt="<?=$product['meta']['title']?>">
                                                    <?php endif; ?>
                                                    <div>
                                                        <h6 class="mb-0">
                                                            <a href="product.php?id=<?=$product['id']?>" class="text-decoration-none">
                                                                <?=$product['meta']['title']?>
                                                            </a>
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?=str_replace(',', '<br>', htmlspecialchars($product['options'], ENT_QUOTES))?>
                                                <input type="hidden" name="options" value="<?=htmlspecialchars($product['options'], ENT_QUOTES)?>">
                                            </td>
                                            <td><?=currency_code?><?=num_format($product['options_price'],2)?></td>
                                            <td>
                                                <input type="number" class="form-control" name="quantity-<?=$num?>" value="<?=$product['quantity']?>" min="1" <?php if ($product['meta']['quantity'] != -1): ?>max="<?=$product['meta']['quantity']?>"<?php endif; ?> style="width: 80px;">
                                            </td>
                                            <td class="fw-bold"><?=currency_code?><?=num_format($product['options_price'] * $product['quantity'],2)?></td>
                                            <td>
                                                <a href="cart.php?remove=<?=$num?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove this item from cart?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                            <td class="fw-bold"><?=currency_code?><?=num_format($subtotal,2)?></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Shipping and tax calculated at checkout
                                    </p>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="btn-group" role="group">
                                        <button type="submit" name="update" class="btn btn-outline-primary">
                                            <i class="fas fa-sync"></i> Update Cart
                                        </button>
                                        <button type="submit" name="emptycart" class="btn btn-outline-warning" onclick="return confirm('Are you sure you want to empty your cart?')">
                                            <i class="fas fa-trash"></i> Empty Cart
                                        </button>
                                        <button type="submit" name="checkout" class="btn btn-success">
                                            <i class="fas fa-credit-card"></i> Proceed to Checkout
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <?php if (!empty($products_in_cart)): ?>
            <!-- Continue Shopping Section -->
            <div class="card mt-4">
                <div class="card-body text-center">
                    <h5>Continue Shopping</h5>
                    <p class="text-muted">Browse our complete product catalog</p>
                    <a href="products.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once "assets/includes/footer.php"; ?>
