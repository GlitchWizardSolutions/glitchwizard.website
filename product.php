<?php
/*
PAGE NAME  : product.php
LOCATION   : public_html/product.php
DESCRIPTION: Individual product detail page
FUNCTION   : Display product details, images, options, and allow adding to cart/wishlist
CHANGE LOG : Integrated from shop_system to main site structure
*/

// Load configuration and functions first (no HTML output)
require_once "../private/gws-universal-config.php";
require_once "../private/gws-universal-functions.php";

// Load shop system functionality (no HTML output)
include_once "shop_system/shop_load.php";
include_once "shop_system/functions.php";

// Validation error variable
$validation_error = '';

// Check to make sure the id parameter is specified in the URL
if (isset($_GET['id'])) {
    // Prepare statement and execute, prevents SQL injection
    $stmt = $pdo->prepare('SELECT * FROM shop_products WHERE product_status = 1 AND (BINARY id = ? OR url_slug = ?)');
    $stmt->execute([ $_GET['id'], $_GET['id'] ]);
    // Fetch the product from the database and return the result as an Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the product exists (array is not empty)
    if (!$product) {
        // Output simple error if the id for the product doesn't exists (array is empty)
        http_response_code(404);
        include_once "assets/includes/footer.php";
        exit('Product does not exist!');
    }
    
    // Select the product images (if any)
    $stmt = $pdo->prepare('SELECT m.*, pm.position FROM shop_product_media_map pm JOIN shop_product_media m ON m.id = pm.media_id WHERE pm.product_id = ? ORDER BY pm.position ASC');
    $stmt->execute([ $product['id'] ]);
    $product_media = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Select the product options (if any)
    $stmt = $pdo->prepare('SELECT CONCAT(option_name, "::", option_type, "::", required) AS k, option_value, quantity, price, price_modifier, weight, weight_modifier, option_type, required FROM shop_product_options WHERE product_id = ? ORDER BY position ASC, id');
    $stmt->execute([ $product['id'] ]);
    $product_options = $stmt->fetchAll(PDO::FETCH_GROUP);
    
    // Check if product is on wishlist
    $on_wishlist = false;
    global $logged_in, $rowusers;
    if ($logged_in && $rowusers) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM shop_wishlist WHERE product_id = ? AND account_id = ?');
        $stmt->execute([ $product['id'], $rowusers['id'] ]);
        $on_wishlist = $stmt->fetchColumn() > 0 ? true : false;
    }

    // Handle wishlist actions
    if (isset($_POST['add_to_wishlist'])) {
        if ($logged_in && $rowusers) {
            $stmt = $pdo->prepare('SELECT * FROM shop_wishlist WHERE product_id = ? AND account_id = ?');
            $stmt->execute([ $product['id'], $rowusers['id'] ]);
            $wishlist_item = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($wishlist_item) {
                $validation_error = 'Product is already in your wishlist!';
            } else {
                $stmt = $pdo->prepare('INSERT INTO shop_wishlist (product_id, account_id, created) VALUES (?, ?, ?)');
                $stmt->execute([ $product['id'], $rowusers['id'], date('Y-m-d H:i:s') ]);
                header('Location: product.php?id=' . ($_GET['id']));
                exit;
            }
        } else {
            header('Location: myaccount.php');
            exit;
        }
    } else if (isset($_POST['remove_from_wishlist'])) {
        if ($logged_in && $rowusers) {
            $stmt = $pdo->prepare('DELETE FROM shop_wishlist WHERE product_id = ? AND account_id = ?');
            $stmt->execute([ $product['id'], $rowusers['id'] ]);
            header('Location: product.php?id=' . ($_GET['id']));
            exit;
        } else {
            header('Location: myaccount.php');
            exit;
        }
    }

    // Handle add to cart
    if (isset($_POST['quantity'], $_POST['product_id']) || (isset($_POST['add_to_cart']) && $_POST)) {
        $quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) ? abs((int)$_POST['quantity']) : 1;
        $product_id = $product['id'];
        $options = '';
        $options_price = (float)$product['price'];
        $options_weight = (float)$product['weight'];
        $error = '';

        // Process product options
        foreach ($_POST as $k => $v) {
            // Validate options
            if (strpos($k, 'option-') !== false) {
                if (is_array($v)) {
                    // Option is checkbox or radio element
                    foreach ($v as $vv) {
                        if (empty($vv)) continue;
                        // Replace underscores with spaces and remove option- prefix
                        $options .= str_replace(['_', 'option-'], [' ', ''], $k) . '-' . $vv . ',';
                        // Get the option from the database
                        $stmt = $pdo->prepare('SELECT * FROM shop_product_options WHERE option_name = ? AND option_value = ? AND product_id = ?');
                        $stmt->execute([ str_replace(['_', 'option-'], [' ', ''], $k), $vv, $product['id'] ]);
                        $option = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        // Check if the option exists and is available
                        if ($option && ($option['quantity'] == -1 || $option['quantity'] >= $quantity)) {
                            $options_price = $option['price_modifier'] == 'add' ? $options_price + $option['price'] : $options_price - $option['price'];
                            $options_weight = $option['weight_modifier'] == 'add' ? $options_weight + $option['weight'] : $options_weight - $option['weight'];
                        } else {
                            $error = 'The ' . htmlspecialchars(str_replace(['_', 'option-'], [' ', ''], $k) . '-' . $vv, ENT_QUOTES) . ' option is no longer available!';
                        }
                    }
                } else {
                    if (empty($v)) continue;
                    // Replace underscores with spaces and remove option- prefix
                    $options .= str_replace(['_', 'option-'], [' ', ''], $k) . '-' . $v . ',';
                    // Get the option from the database
                    $stmt = $pdo->prepare('SELECT * FROM shop_product_options WHERE option_name = ? AND option_value = ? AND product_id = ?');
                    $stmt->execute([ str_replace(['_', 'option-'], [' ', ''], $k), $v, $product['id'] ]);
                    $option = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$option) {
                        // Option is text or datetime element
                        $stmt = $pdo->prepare('SELECT * FROM shop_product_options WHERE option_name = ? AND product_id = ?');
                        $stmt->execute([ str_replace(['_', 'option-'], [' ', ''], $k), $product['id'] ]);
                        $option = $stmt->fetch(PDO::FETCH_ASSOC);                              
                    }
                    if ($option && ($option['quantity'] == -1 || $option['quantity'] >= $quantity)) {
                        $options_price = $option['price_modifier'] == 'add' ? $options_price + $option['price'] : $options_price - $option['price'];
                        $options_weight = $option['weight_modifier'] == 'add' ? $options_weight + $option['weight'] : $options_weight - $option['weight'];
                    } else {
                        $error = 'The ' . htmlspecialchars(str_replace(['_', 'option-'], [' ', ''], $k) . '-' . $v, ENT_QUOTES) . ' option is no longer available!';
                    }
                }
            }
        }
        
        // Check product quantity
        if ($product['quantity'] != -1 && $product['quantity'] < $quantity) {
            $error = 'The product is out of stock or you have reached the maximum quantity!';
        }

        if (!$error) {
            $cart_product = [
                'id' => $product_id,
                'quantity' => $quantity,
                'options' => $options,
                'options_price' => $options_price
            ];
            
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                array_push($_SESSION['cart'], $cart_product);
            } else {
                $_SESSION['cart'] = [$cart_product];
            }
            
            header('Location: cart.php');
            exit;
        } else {
            $validation_error = $error;
        }
    }
    
} else {
    // No product ID provided
    http_response_code(404);
    // Don't include footer here yet - will include at the end
    exit('Product ID not specified!');
}

// Now include HTML output files (after all form processing is complete)
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";

// Include shop navigation
include_once "shop_system/shop_nav.php";
?>

<!-- Product Detail -->
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                    <li class="breadcrumb-item active"><?=htmlspecialchars($product['title'], ENT_QUOTES)?></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <?php if ($product_media && count($product_media) > 0): ?>
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-inner">
                        <?php 
                        $valid_images = 0;
                        foreach ($product_media as $index => $media): 
                            // Construct the correct file path for the integrated shop
                            $image_path = $media['full_path'];
                            // If path doesn't start with shop_system, prepend it
                            if (!str_starts_with($image_path, 'shop_system/')) {
                                $image_path = 'shop_system/' . $image_path;
                            }
                            
                            if (file_exists($image_path)): 
                                $valid_images++;
                        ?>
                                <div class="carousel-item <?=$valid_images == 1 ? 'active' : ''?>" data-slide-index="<?=$valid_images - 1?>">
                                    <img src="<?=$image_path?>" class="d-block w-100 rounded" alt="<?=htmlspecialchars($product['title'], ENT_QUOTES)?>" style="height: 400px; object-fit: cover;">
                                </div>
                        <?php 
                            endif; 
                        endforeach; 
                        ?>
                    </div>
                    <?php if ($valid_images > 1): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    <?php endif; ?>
                </div>
                
                <!-- Thumbnail Navigation -->
                <?php if ($valid_images > 1): ?>
                    <div class="row mt-3" id="productThumbnails">
                        <?php 
                        $thumb_index = 0;
                        foreach ($product_media as $index => $media): ?>
                            <?php 
                            // Construct the correct file path for thumbnails
                            $thumb_path = $media['full_path'];
                            if (!str_starts_with($thumb_path, 'shop_system/')) {
                                $thumb_path = 'shop_system/' . $thumb_path;
                            }
                            
                            if (file_exists($thumb_path)): ?>
                                <div class="col-3">
                                    <img src="<?=$thumb_path?>" 
                                         class="img-thumbnail product-thumb <?=$thumb_index == 0 ? 'active' : ''?>" 
                                         style="cursor: pointer; height: 80px; object-fit: cover; transition: all 0.3s ease;" 
                                         data-slide-to="<?=$thumb_index?>"
                                         onclick="goToSlide(<?=$thumb_index?>)">
                                </div>
                                <?php $thumb_index++; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <!-- No Image Placeholder -->
                <div class="card">
                    <div class="card-body bg-light rounded d-flex flex-column align-items-center justify-content-center" style="height: 400px;">
                        <i class="bi bi-image fa-4x text-muted mb-3" aria-hidden="true"></i>
                        <h5 class="text-muted mb-2">No Image Available</h5>
                        <p class="text-muted text-center mb-0">
                            Product images will be displayed here once uploaded
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Product Information -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title h3"><?=htmlspecialchars($product['title'], ENT_QUOTES)?></h1>
                    
                    <div class="mb-3">
                        <?php if ($product['rrp'] > 0 && $product['rrp'] != $product['price']): ?>
                            <span class="text-muted text-decoration-line-through h5"><?=currency_code?><?=num_format($product['rrp'], 2)?></span>
                        <?php endif; ?>
                        <span class="h4 text-primary"><?=currency_code?><?=num_format($product['price'], 2)?></span>
                    </div>

                    <div class="mb-3">
                        <?php if ($product['quantity'] == 0): ?>
                            <span class="badge bg-danger fs-6">Out of Stock</span>
                        <?php elseif ($product['quantity'] > 0 && $product['quantity'] <= 5): ?>
                            <span class="badge bg-warning fs-6">Only <?=$product['quantity']?> left!</span>
                        <?php else: ?>
                            <span class="badge bg-success fs-6">In Stock</span>
                        <?php endif; ?>
                    </div>

                    <?php 
                    $description = $product['short_description'] ?? $product['description'] ?? '';
                    if ($description): ?>
                        <div class="lead"><?=$description?></div>
                    <?php endif; ?>

                    <?php if ($validation_error): ?>
                        <div class="alert alert-danger"><?=$validation_error?></div>
                    <?php endif; ?>

                    <!-- Add to Cart Form -->
                    <form action="product.php?id=<?=$_GET['id']?>" method="post" class="mb-4">
                        <input type="hidden" name="product_id" value="<?=$product['id']?>">
                        
                        <!-- Product Options would go here -->
                        <?php if ($product_options): ?>
                            <div class="mb-3">
                                <h5>Options</h5>
                                <!-- Option rendering logic would be implemented here -->
                            </div>
                        <?php endif; ?>

                        <div class="row g-3 mb-3">
                            <div class="col-auto">
                                <label class="form-label">Quantity:</label>
                                <input type="number" name="quantity" class="form-control" value="1" min="1" 
                                       <?php if ($product['quantity'] != -1): ?>max="<?=$product['quantity']?>"<?php endif; ?> 
                                       style="width: 100px;" <?=$product['quantity'] == 0 ? 'disabled' : ''?>>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-block">
                            <?php if ($product['quantity'] != 0): ?>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-cart" aria-hidden="true"></i> Add to Cart
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($logged_in && $rowusers): ?>
                                <?php if ($on_wishlist): ?>
                                    <button type="submit" name="remove_from_wishlist" class="btn btn-outline-danger">
                                        <i class="bi bi-heart-fill" aria-hidden="true"></i> Remove from Wishlist
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="add_to_wishlist" class="btn btn-outline-primary">
                                        <i class="bi bi-heart" aria-hidden="true"></i> Add to Wishlist
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </form>

                    <!-- Additional Product Info -->
                    <div class="border-top pt-3">
                        <small class="text-muted">
                            <strong>SKU:</strong> <?=$product['id']?><br>
                            <?php if ($product['weight'] > 0): ?>
                                <strong>Weight:</strong> <?=$product['weight']?> <?=weight_unit?><br>
                            <?php endif; ?>
                            <strong>Date Added:</strong> <?=date('M j, Y', strtotime($product['created']))?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description -->
    <?php 
    $full_description = $product['long_description'] ?? $product['description'] ?? '';
    if ($full_description): ?>
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-info-circle" aria-hidden="true"></i> Product Description</h4>
                    </div>
                    <div class="card-body">
                        <?=$full_description?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Related Products or Back to Products -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="products.php" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to Products
            </a>
        </div>
    </div>
</div>

<style>
/* Product Thumbnail Styles */
.product-thumb {
    border: 2px solid transparent;
    opacity: 0.7;
}

.product-thumb.active {
    border-color: var(--bs-primary, #0d6efd);
    opacity: 1;
    box-shadow: 0 0 10px rgba(13, 110, 253, 0.3);
}

.product-thumb:hover {
    opacity: 1;
    border-color: var(--bs-primary, #0d6efd);
    transform: scale(1.05);
}

/* Pause indicator for carousel */
.carousel.paused::after {
    content: '⏸️';
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 14px;
    z-index: 10;
}
</style>

<script>
// Product Image Carousel Functionality
document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('productCarousel');
    const thumbnails = document.querySelectorAll('.product-thumb');
    
    if (carousel && thumbnails.length > 0) {
        const bsCarousel = new bootstrap.Carousel(carousel, {
            interval: 5000,
            wrap: true,
            keyboard: true
        });
        
        let isCarouselPaused = false;
        
        // Function to go to specific slide
        window.goToSlide = function(slideIndex) {
            // Pause the carousel
            bsCarousel.pause();
            isCarouselPaused = true;
            carousel.classList.add('paused');
            
            // Go to the specific slide
            bsCarousel.to(slideIndex);
            
            // Update active thumbnail
            updateActiveThumbnail(slideIndex);
            
            // Resume carousel after 10 seconds of inactivity
            clearTimeout(window.carouselResumeTimer);
            window.carouselResumeTimer = setTimeout(function() {
                if (isCarouselPaused) {
                    bsCarousel.cycle();
                    isCarouselPaused = false;
                    carousel.classList.remove('paused');
                }
            }, 10000);
        };
        
        // Function to update active thumbnail
        function updateActiveThumbnail(activeIndex) {
            thumbnails.forEach(function(thumb, index) {
                if (index === activeIndex) {
                    thumb.classList.add('active');
                } else {
                    thumb.classList.remove('active');
                }
            });
        }
        
        // Listen for carousel slide events to update thumbnails
        carousel.addEventListener('slid.bs.carousel', function(event) {
            const activeIndex = Array.from(event.target.querySelectorAll('.carousel-item')).indexOf(event.relatedTarget);
            updateActiveThumbnail(activeIndex);
        });
        
        // Add keyboard navigation
        document.addEventListener('keydown', function(event) {
            if (event.target.closest('.container')) {
                switch(event.key) {
                    case 'ArrowLeft':
                        event.preventDefault();
                        bsCarousel.prev();
                        break;
                    case 'ArrowRight':
                        event.preventDefault();
                        bsCarousel.next();
                        break;
                    case ' ': // Spacebar
                        event.preventDefault();
                        if (isCarouselPaused) {
                            bsCarousel.cycle();
                            isCarouselPaused = false;
                            carousel.classList.remove('paused');
                        } else {
                            bsCarousel.pause();
                            isCarouselPaused = true;
                            carousel.classList.add('paused');
                        }
                        break;
                }
            }
        });
        
        // Pause on hover
        carousel.addEventListener('mouseenter', function() {
            if (!isCarouselPaused) {
                bsCarousel.pause();
            }
        });
        
        // Resume on mouse leave (only if not manually paused)
        carousel.addEventListener('mouseleave', function() {
            if (!isCarouselPaused) {
                bsCarousel.cycle();
            }
        });
    }
});
</script>

<?php include_once "assets/includes/footer.php"; ?>
