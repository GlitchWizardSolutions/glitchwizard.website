<?php
/*
PAGE NAME  : products.php
LOCATION   : public_html/products.php
DESCRIPTION: Products catalog page for browsing and filtering products
FUNCTION   : Users can browse products, filter by categories, search, and add items to cart
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

// Get all the categories from the database
$stmt = $pdo->query('SELECT * FROM shop_product_categories');
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Execute query to retrieve product options and group by the title
$stmt = $pdo->query('SELECT option_name, option_value FROM shop_product_options WHERE option_type = "select" OR option_type = "radio" OR option_type = "checkbox" GROUP BY option_name, option_value ORDER BY option_name, option_value ASC');
$stmt->execute();
$product_options = $stmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

// Get the current category from the GET request
$category_list = isset($_GET['category']) && $_GET['category'] ? $_GET['category'] : [];
$category_list = is_array($category_list) ? $category_list : [$category_list];
$category_sql = '';
if ($category_list) {
    $category_sql = 'JOIN shop_product_category pc ON FIND_IN_SET(pc.category_id, :category_list) AND pc.product_id = p.id JOIN shop_product_categories c ON c.id = pc.category_id';
}

// Get the options from the GET request
$options_list = isset($_GET['option']) && $_GET['option'] ? $_GET['option'] : [];
$options_list = is_array($options_list) ? $options_list : [$options_list];
$options_sql = '';
if ($options_list) {
    $options_sql = 'JOIN shop_product_options po ON po.product_id = p.id AND FIND_IN_SET(CONCAT(po.option_name, "-", po.option_value), :option_list)';
}

// Availability options
$availability_list = isset($_GET['availability']) && $_GET['availability'] ? $_GET['availability'] : [];
$availability_list = is_array($availability_list) ? $availability_list : [$availability_list];
$availability_sql = '';
if ($availability_list) {
    $availability_sql = 'AND (p.quantity > 0 OR p.quantity = -1)';
    if (in_array('out-of-stock', $availability_list)) {
        $availability_sql = 'AND p.quantity = 0';
    }
}

// Get price filters
$price_min = isset($_GET['price_min']) && is_numeric($_GET['price_min']) ? $_GET['price_min'] : '';
$price_max = isset($_GET['price_max']) && is_numeric($_GET['price_max']) ? $_GET['price_max'] : '';
$price_sql = '';
if ($price_min) {
    $price_sql .= ' AND p.price >= :price_min ';
}
if ($price_max) {
    $price_sql .= ' AND p.price <= :price_max ';
}

// Get the sort option
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$sort_sql = '';
if ($sort == 'newest') {
    $sort_sql = 'p.created DESC';
} elseif ($sort == 'oldest') {
    $sort_sql = 'p.created ASC';
} elseif ($sort == 'highprice') {
    $sort_sql = 'p.price DESC';
} elseif ($sort == 'lowprice') {
    $sort_sql = 'p.price ASC';
} elseif ($sort == 'name') {
    $sort_sql = 'p.title ASC';
}

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_sql = '';
if ($search) {
    $search_sql = 'AND (p.title LIKE :search OR p.short_description LIKE :search OR p.long_description LIKE :search)';
}

// Pagination
$products_per_page = 12;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $products_per_page;

// Build the main query
$sql = "SELECT DISTINCT p.*, (SELECT m.full_path FROM shop_product_media_map pm JOIN shop_product_media m ON m.id = pm.media_id WHERE pm.product_id = p.id ORDER BY pm.position ASC LIMIT 1) AS img 
        FROM shop_products p 
        $category_sql 
        $options_sql 
        WHERE p.product_status = 1 
        $availability_sql 
        $price_sql 
        $search_sql 
        ORDER BY $sort_sql 
        LIMIT :offset, :products_per_page";

$stmt = $pdo->prepare($sql);

// Bind parameters
if ($category_list) {
    $stmt->bindValue(':category_list', implode(',', $category_list));
}
if ($options_list) {
    $stmt->bindValue(':option_list', implode(',', $options_list));
}
if ($price_min) {
    $stmt->bindValue(':price_min', $price_min, PDO::PARAM_INT);
}
if ($price_max) {
    $stmt->bindValue(':price_max', $price_max, PDO::PARAM_INT);
}
if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':products_per_page', $products_per_page, PDO::PARAM_INT);

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total products for pagination
$count_sql = "SELECT COUNT(DISTINCT p.id) as total 
              FROM shop_products p 
              $category_sql 
              $options_sql 
              WHERE p.product_status = 1 
              $availability_sql 
              $price_sql 
              $search_sql";

$count_stmt = $pdo->prepare($count_sql);
if ($category_list) {
    $count_stmt->bindValue(':category_list', implode(',', $category_list));
}
if ($options_list) {
    $count_stmt->bindValue(':option_list', implode(',', $options_list));
}
if ($price_min) {
    $count_stmt->bindValue(':price_min', $price_min, PDO::PARAM_INT);
}
if ($price_max) {
    $count_stmt->bindValue(':price_max', $price_max, PDO::PARAM_INT);
}
if ($search) {
    $count_stmt->bindValue(':search', '%' . $search . '%');
}
$count_stmt->execute();
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $products_per_page);
?>

<!-- Products Catalog -->
<div class="container">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header accent-background">
                    <i class="bi bi-funnel" aria-hidden="true"></i> Filters
                </div>
                <div class="card-body">
                    <form action="products.php" method="get" id="filter-form">
                        <!-- Search -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Search Products</label>
                            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
                        </div>

                        <!-- Categories -->
                        <?php if ($categories): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Categories</label>
                            <?php foreach ($categories as $category): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="category[]" value="<?=$category['id']?>" id="cat<?=$category['id']?>" <?=in_array($category['id'], $category_list) ? 'checked' : ''?>>
                                    <label class="form-check-label" for="cat<?=$category['id']?>">
                                        <?=htmlspecialchars($category['title'] ?? 'Unknown Category', ENT_QUOTES)?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Price Range</label>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="number" name="price_min" class="form-control" placeholder="Min" value="<?=$price_min?>" step="0.01">
                                </div>
                                <div class="col">
                                    <input type="number" name="price_max" class="form-control" placeholder="Max" value="<?=$price_max?>" step="0.01">
                                </div>
                            </div>
                        </div>

                        <!-- Availability -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Availability</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="availability[]" value="in-stock" id="in-stock" <?=in_array('in-stock', $availability_list) ? 'checked' : ''?>>
                                <label class="form-check-label" for="in-stock">In Stock</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="availability[]" value="out-of-stock" id="out-of-stock" <?=in_array('out-of-stock', $availability_list) ? 'checked' : ''?>>
                                <label class="form-check-label" for="out-of-stock">Out of Stock</label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="products.php" class="btn btn-outline-secondary">Clear Filters</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Header with Sort and Results Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="mb-0">
                                <i class="bi bi-bag" aria-hidden="true"></i> Products
                                <?php if ($search): ?>
                                    <small class="text-muted">- Search: "<?=htmlspecialchars($search, ENT_QUOTES)?>"</small>
                                <?php endif; ?>
                            </h4>
                            <p class="text-muted mb-0"><?=$total_products?> products found</p>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="col-auto">
                                    <label class="form-label mb-0">Sort by:</label>
                                </div>
                                <div class="col">
                                    <select name="sort" class="form-select" onchange="updateSort(this.value)">
                                        <option value="newest" <?=$sort == 'newest' ? 'selected' : ''?>>Newest First</option>
                                        <option value="oldest" <?=$sort == 'oldest' ? 'selected' : ''?>>Oldest First</option>
                                        <option value="name" <?=$sort == 'name' ? 'selected' : ''?>>Name A-Z</option>
                                        <option value="lowprice" <?=$sort == 'lowprice' ? 'selected' : ''?>>Price Low to High</option>
                                        <option value="highprice" <?=$sort == 'highprice' ? 'selected' : ''?>>Price High to Low</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <?php if ($products): ?>
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-4 col-sm-6 mb-4">
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
                                    <h5 class="card-title">
                                        <a href="product.php?id=<?=$product['id']?>" class="text-decoration-none">
                                            <?=htmlspecialchars($product['title'], ENT_QUOTES)?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted flex-grow-1">
                                        <?php 
                                        $description = $product['short_description'] ?? $product['description'] ?? '';
                                        // Strip HTML tags for clean truncation in listing
                                        $clean_description = strip_tags($description);
                                        echo htmlspecialchars(substr($clean_description, 0, 100), ENT_QUOTES);
                                        echo strlen($clean_description) > 100 ? '...' : '';
                                        ?>
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <?php if ($product['rrp'] > 0 && $product['rrp'] != $product['price']): ?>
                                                    <small class="text-muted text-decoration-line-through"><?=currency_code?><?=num_format($product['rrp'], 2)?></small>
                                                <?php endif; ?>
                                                <span class="h5 text-primary mb-0"><?=currency_code?><?=num_format($product['price'], 2)?></span>
                                            </div>
                                            <?php if ($product['quantity'] == 0): ?>
                                                <span class="badge bg-danger">Out of Stock</span>
                                            <?php elseif ($product['quantity'] > 0 && $product['quantity'] <= 5): ?>
                                                <span class="badge bg-warning">Low Stock</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">In Stock</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <a href="product.php?id=<?=$product['id']?>" class="btn btn-primary">
                                                <i class="bi bi-eye" aria-hidden="true"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Products pagination">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?=$page-1?><?=http_build_query(array_filter($_GET, function($key) { return $key != 'page'; }), '', '&')?>">&laquo; Previous</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <li class="page-item <?=$i == $page ? 'active' : ''?>">
                                    <a class="page-link" href="?page=<?=$i?><?=http_build_query(array_filter($_GET, function($key) { return $key != 'page'; }), '', '&')?>"><?=$i?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?=$page+1?><?=http_build_query(array_filter($_GET, function($key) { return $key != 'page'; }), '', '&')?>">Next &raquo;</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search fa-3x text-muted mb-3" aria-hidden="true"></i>
                        <h4>No products found</h4>
                        <p class="text-muted">Try adjusting your search criteria or browse all products.</p>
                        <a href="products.php" class="btn btn-primary">View All Products</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function updateSort(value) {
    const url = new URL(window.location);
    url.searchParams.set('sort', value);
    window.location = url;
}

// Auto-submit filter form when checkboxes change
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('#filter-form input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            document.getElementById('filter-form').submit();
        });
    });
});
</script>

<?php include_once "assets/includes/footer.php"; ?>
