<?php
/*
PAGE NAME  : shop.php
LOCATION   : public_html/shop.php
DESCRIPTION: Main shop homepage/landing page
FUNCTION   : Display featured products, categories, and shop overview
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

// Get featured products
$stmt = $pdo->prepare('SELECT p.*, (SELECT m.full_path FROM shop_product_media_map pm JOIN shop_product_media m ON m.id = pm.media_id WHERE pm.product_id = p.id ORDER BY pm.position ASC LIMIT 1) AS img FROM shop_products p WHERE p.product_status = 1 ORDER BY p.created DESC LIMIT 8');
$stmt->execute();
$featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$stmt = $pdo->query('SELECT * FROM shop_product_categories ORDER BY title ASC');
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get cart item count
$cart_count = 0;
if (isset($_SESSION['cart']) && $_SESSION['cart']) {
    $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
}
?>

<!-- Shop Landing Page -->
<div class="container">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <h1 class="display-4 mb-3"><i class="bi bi-shop-window" aria-hidden="true"></i> Welcome to Our Shop</h1>
                    <p class="lead mb-4" style="color: #000;">Discover amazing products at great prices. Shop with confidence and enjoy fast, secure checkout.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="products.php" class="btn btn-light btn-lg">
                            <i class="bi bi-bag" aria-hidden="true"></i> Browse All Products
                        </a>
                        <?php if ($cart_count > 0): ?>
                            <a href="cart.php" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-cart" aria-hidden="true"></i> Cart (<?=$cart_count?>)
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-5">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-truck fa-2x text-primary mb-2" aria-hidden="true"></i>
                    <h5>Fast Shipping</h5>
                    <p class="text-muted mb-0">Quick delivery to your door</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-shield-check fa-2x text-success mb-2" aria-hidden="true"></i>
                    <h5>Secure Payment</h5>
                    <p class="text-muted mb-0">Safe and secure transactions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-arrow-counterclockwise fa-2x text-warning mb-2" aria-hidden="true"></i>
                    <h5>Easy Returns</h5>
                    <p class="text-muted mb-0">Hassle-free return policy</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-headset fa-2x text-info mb-2" aria-hidden="true"></i>
                    <h5>24/7 Support</h5>
                    <p class="text-muted mb-0">Always here to help you</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <?php if ($categories): ?>
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center mb-4"><i class="bi bi-tags" aria-hidden="true"></i> Shop by Category</h2>
                <div class="row">
                    <?php foreach (array_slice($categories, 0, 6) as $category): ?>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card h-100 category-card">
                                <div class="card-body text-center">
                                    <i class="bi bi-folder2-open fa-3x text-primary mb-3" aria-hidden="true"></i>
                                    <h5 class="card-title"><?=htmlspecialchars($category['title'], ENT_QUOTES)?></h5>
                                    <a href="products.php?category[]=<?=$category['id']?>" class="btn btn-outline-primary">
                                        Browse Category
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($categories) > 6): ?>
                    <div class="text-center mt-3">
                        <a href="products.php" class="btn btn-primary">View All Categories</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Featured Products -->
    <?php if ($featured_products): ?>
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-star-fill" aria-hidden="true"></i> Featured Products</h2>
                    <a href="products.php" class="btn btn-outline-primary">View All Products</a>
                </div>
                
                <div class="row">
                    <?php foreach ($featured_products as $product): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card h-100 product-card">
                                <?php 
                                $img_path = $product['img'];
                                // Fix path for integrated shop
                                if (!empty($img_path) && !str_starts_with($img_path, 'shop_system/')) {
                                    $img_path = 'shop_system/' . $img_path;
                                }
                                
                                if (!empty($product['img']) && file_exists($img_path)): ?>
                                    <img src="<?=$img_path?>" class="card-img-top" alt="<?=htmlspecialchars($product['title'], ENT_QUOTES)?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                        <i class="bi bi-image fa-3x text-muted" aria-hidden="true"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">
                                        <a href="product.php?id=<?=$product['id']?>" class="text-decoration-none">
                                            <?=htmlspecialchars($product['title'], ENT_QUOTES)?>
                                        </a>
                                    </h6>
                                    <p class="card-text text-muted flex-grow-1">
                                        <?php 
                                        $description = $product['short_description'] ?? $product['description'] ?? '';
                                        // Strip HTML tags for clean truncation in listing
                                        $clean_description = strip_tags($description);
                                        echo htmlspecialchars(substr($clean_description, 0, 80), ENT_QUOTES);
                                        echo strlen($clean_description) > 80 ? '...' : '';
                                        ?>
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <?php if ($product['rrp'] > 0 && $product['rrp'] != $product['price']): ?>
                                                    <small class="text-muted text-decoration-line-through"><?=currency_code?><?=num_format($product['rrp'], 2)?></small>
                                                <?php endif; ?>
                                                <span class="h6 text-primary mb-0"><?=currency_code?><?=num_format($product['price'], 2)?></span>
                                            </div>
                                            <?php if ($product['quantity'] == 0): ?>
                                                <span class="badge bg-danger">Out of Stock</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">In Stock</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <a href="product.php?id=<?=$product['id']?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye" aria-hidden="true"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Call to Action -->
    <div class="row">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body text-center py-5">
                    <h3><i class="bi bi-gift" aria-hidden="true"></i> Start Shopping Today!</h3>
                    <p class="lead mb-4">Join thousands of satisfied customers who trust our products and service.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="products.php" class="btn btn-primary btn-lg">
                            <i class="bi bi-bag" aria-hidden="true"></i> Browse Products
                        </a>
                        <?php if (!$logged_in): ?>
                            <a href="auth.php" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-person-plus" aria-hidden="true"></i> Create Account
                            </a>
                        <?php else: ?>
                            <a href="myaccount.php" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-person" aria-hidden="true"></i> My Account
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.product-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.category-card {
    transition: transform 0.2s ease-in-out;
}

.category-card:hover {
    transform: translateY(-3px);
}
</style>

<?php include_once "assets/includes/footer.php"; ?>
