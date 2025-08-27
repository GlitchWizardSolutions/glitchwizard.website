<?php
/*
PAGE NAME  : shop-search.php
LOCATION   : public_html/shop-search.php
DESCRIPTION: Product search page with filtering
FUNCTION   : Search and filter products across the entire catalog
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

// Get search parameters
$search_query = $_GET['q'] ?? '';
$category_filter = $_GET['category'] ?? [];
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$sort_by = $_GET['sort'] ?? 'newest';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Clean up category filter
if (!is_array($category_filter)) {
    $category_filter = $category_filter ? [$category_filter] : [];
}
$category_filter = array_filter($category_filter, 'is_numeric');

// Build search query
$where_conditions = ["p.product_status = 1"];
$params = [];

// Search query
if (!empty($search_query)) {
    $where_conditions[] = "(p.title LIKE ? OR p.description LIKE ?)";
    $search_param = '%' . $search_query . '%';
    $params[] = $search_param;
    $params[] = $search_param;
}

// Category filter
if (!empty($category_filter)) {
    $placeholders = str_repeat('?,', count($category_filter) - 1) . '?';
    $where_conditions[] = "pc.category_id IN ($placeholders)";
    $params = array_merge($params, $category_filter);
}

// Price filter
if (!empty($min_price) && is_numeric($min_price)) {
    $where_conditions[] = "p.price >= ?";
    $params[] = floatval($min_price);
}
if (!empty($max_price) && is_numeric($max_price)) {
    $where_conditions[] = "p.price <= ?";
    $params[] = floatval($max_price);
}

// Sort options
$sort_options = [
    'newest' => 'p.created DESC',
    'oldest' => 'p.created ASC',
    'price_low' => 'p.price ASC',
    'price_high' => 'p.price DESC',
    'name_asc' => 'p.title ASC',
    'name_desc' => 'p.title DESC'
];
$order_by = $sort_options[$sort_by] ?? $sort_options['newest'];

// Category join
$category_join = !empty($category_filter) ? "LEFT JOIN shop_product_categories_map pc ON p.id = pc.product_id" : "";

// Get total count
$count_sql = "SELECT COUNT(DISTINCT p.id) FROM shop_products p $category_join WHERE " . implode(' AND ', $where_conditions);
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $per_page);

// Get products
$sql = "SELECT DISTINCT p.*, 
        (SELECT m.full_path FROM shop_product_media_map pm 
         JOIN shop_product_media m ON m.id = pm.media_id 
         WHERE pm.product_id = p.id ORDER BY pm.position ASC LIMIT 1) AS img
        FROM shop_products p 
        $category_join
        WHERE " . implode(' AND ', $where_conditions) . "
        ORDER BY $order_by
        LIMIT $per_page OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all categories for filter
$categories_stmt = $pdo->query('SELECT * FROM shop_product_categories ORDER BY title ASC');
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get cart item count for header display
$cart_count = 0;
if (isset($_SESSION['cart']) && $_SESSION['cart']) {
    $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
}
?>

<!-- Search Results Page -->
<div class="container">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-search" aria-hidden="true"></i> Product Search</h1>
                    <?php if (!empty($search_query)): ?>
                        <p class="text-muted mb-0">
                            Results for: <strong>"<?=htmlspecialchars($search_query, ENT_QUOTES)?>"</strong>
                            (<?=$total_products?> products found)
                        </p>
                    <?php else: ?>
                        <p class="text-muted mb-0">Browse all products (<?=$total_products?> total)</p>
                    <?php endif; ?>
                </div>
                <div>
                    <a href="products.php" class="btn btn-outline-primary">
                        <i class="bi bi-grid" aria-hidden="true"></i> Browse All
                    </a>
                    <?php if ($cart_count > 0): ?>
                        <a href="cart.php" class="btn btn-primary">
                            <i class="bi bi-cart" aria-hidden="true"></i> Cart (<?=$cart_count?>)
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header accent-background text-white">
                    <h5 class="mb-0"><i class="bi bi-funnel" aria-hidden="true"></i> Search & Filters</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="shop-search.php" id="search-form">
                        <!-- Search Input -->
                        <div class="mb-3">
                            <label for="search-query" class="form-label">Search Products</label>
                            <input type="text" class="form-control" id="search-query" name="q" 
                                   value="<?=htmlspecialchars($search_query, ENT_QUOTES)?>" 
                                   placeholder="Enter keywords...">
                        </div>

                        <!-- Categories Filter -->
                        <?php if ($categories): ?>
                            <div class="mb-3">
                                <label class="form-label">Categories</label>
                                <div class="category-filter" style="max-height: 200px; overflow-y: auto;">
                                    <?php foreach ($categories as $category): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="category[]" value="<?=$category['id']?>" 
                                                   id="cat-<?=$category['id']?>"
                                                   <?=in_array($category['id'], $category_filter) ? 'checked' : ''?>>
                                            <label class="form-check-label" for="cat-<?=$category['id']?>">
                                                <?=htmlspecialchars($category['title'], ENT_QUOTES)?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label">Price Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" 
                                           name="min_price" placeholder="Min" step="0.01"
                                           value="<?=htmlspecialchars($min_price, ENT_QUOTES)?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" 
                                           name="max_price" placeholder="Max" step="0.01"
                                           value="<?=htmlspecialchars($max_price, ENT_QUOTES)?>">
                                </div>
                            </div>
                        </div>

                        <!-- Sort By -->
                        <div class="mb-3">
                            <label for="sort-by" class="form-label">Sort By</label>
                            <select class="form-select" id="sort-by" name="sort">
                                <option value="newest" <?=$sort_by === 'newest' ? 'selected' : ''?>>Newest First</option>
                                <option value="oldest" <?=$sort_by === 'oldest' ? 'selected' : ''?>>Oldest First</option>
                                <option value="price_low" <?=$sort_by === 'price_low' ? 'selected' : ''?>>Price: Low to High</option>
                                <option value="price_high" <?=$sort_by === 'price_high' ? 'selected' : ''?>>Price: High to Low</option>
                                <option value="name_asc" <?=$sort_by === 'name_asc' ? 'selected' : ''?>>Name: A to Z</option>
                                <option value="name_desc" <?=$sort_by === 'name_desc' ? 'selected' : ''?>>Name: Z to A</option>
                            </select>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search" aria-hidden="true"></i> Search
                            </button>
                            <a href="shop-search.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x" aria-hidden="true"></i> Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Area -->
        <div class="col-lg-9">
            <?php if ($products): ?>
                <!-- Results Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">
                        Showing <?=($offset + 1)?> - <?=min($offset + $per_page, $total_products)?> of <?=$total_products?> products
                    </span>
                    <span class="text-muted">
                        Page <?=$page?> of <?=$total_pages?>
                    </span>
                </div>

                <!-- Products Grid -->
                <div class="row">
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
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

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Search results pagination">
                        <ul class="pagination justify-content-center">
                            <!-- Previous -->
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?=http_build_query(array_merge($_GET, ['page' => $page - 1]))?>">
                                        <i class="bi bi-chevron-left" aria-hidden="true"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>

                            <!-- Page Numbers -->
                            <?php 
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?=http_build_query(array_merge($_GET, ['page' => 1]))?>">1</a>
                                </li>
                                <?php if ($start_page > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <li class="page-item <?=$i === $page ? 'active' : ''?>">
                                    <a class="page-link" href="?<?=http_build_query(array_merge($_GET, ['page' => $i]))?>"><?=$i?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($end_page < $total_pages): ?>
                                <?php if ($end_page < $total_pages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?=http_build_query(array_merge($_GET, ['page' => $total_pages]))?>"><?=$total_pages?></a>
                                </li>
                            <?php endif; ?>

                            <!-- Next -->
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?=http_build_query(array_merge($_GET, ['page' => $page + 1]))?>">
                                        Next <i class="bi bi-chevron-right" aria-hidden="true"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <!-- No Results -->
                <div class="text-center py-5">
                    <i class="bi bi-search fa-4x text-muted mb-3" aria-hidden="true"></i>
                    <h3>No Products Found</h3>
                    <p class="text-muted mb-4">
                        <?php if (!empty($search_query)): ?>
                            We couldn't find any products matching "<?=htmlspecialchars($search_query, ENT_QUOTES)?>".
                        <?php else: ?>
                            No products match your current filters.
                        <?php endif; ?>
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="shop-search.php" class="btn btn-primary">
                            <i class="bi bi-x" aria-hidden="true"></i> Clear Filters
                        </a>
                        <a href="products.php" class="btn btn-outline-primary">
                            <i class="bi bi-grid" aria-hidden="true"></i> Browse All Products
                        </a>
                    </div>
                </div>
            <?php endif; ?>
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

.category-filter {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.5rem;
    background-color: #f8f9fa;
}

.category-filter .form-check {
    margin-bottom: 0.25rem;
}

.category-filter .form-check:last-child {
    margin-bottom: 0;
}
</style>

<script>
// Auto-submit form when sort changes
document.getElementById('sort-by').addEventListener('change', function() {
    document.getElementById('search-form').submit();
});

// Auto-submit when category checkboxes change (with slight delay)
document.querySelectorAll('input[name="category[]"]').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        setTimeout(function() {
            document.getElementById('search-form').submit();
        }, 300);
    });
});
</script>

<?php include_once "assets/includes/footer.php"; ?>
