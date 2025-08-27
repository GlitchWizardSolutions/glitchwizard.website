<?php
// Shop Secondary Navigation Component
// This creates a consistent navigation bar for all shop pages matching blog navigation style

// Get cart item count
$cart_count = 0;
if (isset($_SESSION['cart']) && $_SESSION['cart']) {
    $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
}

// Get categories for dropdown
$stmt = $pdo->query('SELECT * FROM shop_product_categories ORDER BY title ASC');
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Determine current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Shop Secondary Navigation (Blog Style) -->
<div class="container mt-3 mb-3">
    <nav class="navbar navbar-expand-lg bg-primary shop-navbar" style="background: var(--brand-primary, #593196) !important; border-radius: 8px;">
        <div class="container-fluid">
            <button class="navbar-toggler mx-auto" type="button" data-bs-toggle="collapse"
                data-bs-target="#shopNavbarContent" aria-controls="shopNavbarContent" aria-expanded="false"
                aria-label="Toggle shop navigation">
                <span class="navbar-toggler-icon"></span> Shop Menu
            </button>
        <div class="collapse navbar-collapse" id="shopNavbarContent">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link link-light px-2 <?= $current_page === 'shop.php' ? 'active' : '' ?>" href="shop.php">
                        Shop
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link link-light px-2 <?= $current_page === 'products.php' ? 'active' : '' ?>" href="products.php">
                        All Products
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link link-light dropdown-toggle px-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categories <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="products.php">View All Products</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php foreach ($categories as $category): ?>
                            <li><a class="dropdown-item" href="products.php?category=<?=$category['id']?>"><?=htmlspecialchars($category['title'])?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php if ($logged_in): ?>
                    <li class="nav-item">
                        <a class="nav-link link-light px-2 <?= $current_page === 'myaccount.php' ? 'active' : '' ?>" href="myaccount.php">
                            My Account
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav d-flex">
                <li class="nav-item">
                    <a class="nav-link link-light px-2 <?= $current_page === 'cart.php' ? 'active' : '' ?>" href="cart.php">
                        <i class="bi bi-cart" style="font-size: 1.5rem;" aria-hidden="true"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="badge text-bg-light rounded-pill align-text-bottom"><?=$cart_count?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <!-- Login/User Avatar Dropdown -->
                <?php if ($logged_in && isset($rowusers)): ?>
                    <li class="nav-item dropdown"> 
                        <?php
                        // Get avatar for logged-in user
                        $avatar_src = 'accounts_system/assets/uploads/avatars/default-guest.svg';
                        $avatar_alt = 'Profile';
                        if (isset($rowusers) && !empty($rowusers['id'])) {
                            $stmt_avatar = $pdo->prepare('SELECT avatar FROM accounts WHERE id = ? LIMIT 1');
                            $stmt_avatar->execute([$rowusers['id']]);
                            $user_avatar = $stmt_avatar->fetch(PDO::FETCH_ASSOC);
                            if ($user_avatar && !empty($user_avatar['avatar']) && file_exists('accounts_system/assets/uploads/avatars/' . $user_avatar['avatar'])) {
                                $avatar_src = 'accounts_system/assets/uploads/avatars/' . $user_avatar['avatar'];
                            }
                            $avatar_alt = isset($rowusers['username']) ? htmlspecialchars($rowusers['username']) : 'Profile';
                        }
                        ?>
                        <a href="#" class="nav-link link-light dropdown-toggle d-flex align-items-center px-2" data-bs-toggle="dropdown">
                            <img src="<?php echo htmlspecialchars($avatar_src); ?>" alt="<?php echo $avatar_alt; ?>" class="rounded-circle me-2" width="28" height="28" style="object-fit:cover;vertical-align:middle;" />
                            <?php echo isset($rowusers['username']) ? htmlspecialchars($rowusers['username']) : 'Profile'; ?> <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu"> 
                            <?php
                            // Show My Portal link only if logged in and NOT Customer or Blog_User
                            if (isset($rowusers['role']) && !in_array($rowusers['role'], ['Customer', 'Blog_User'])) {
                            ?>
                            <li>
                                <a class="dropdown-item" href="client_portal/">
                                    <i class="bi bi-speedometer2" aria-hidden="true"></i> My Portal
                                </a>
                            </li>
                            <?php
                            }
                            ?>
                            <li>
                                <a class="dropdown-item <?= $current_page === 'myaccount.php' ? 'active' : '' ?>" href="myaccount.php">
                                    <i class="bi bi-person" aria-hidden="true"></i> My Account
                                </a>
                            </li>
                            <li> 
                                <a class="dropdown-item <?= $current_page === 'my-comments.php' ? 'active' : '' ?>" href="my-comments.php">
                                    <i class="bi bi-chat-dots" aria-hidden="true"></i> My Blog Comments
                                </a>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="bi bi-box-arrow-right" aria-hidden="true"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link link-light px-2" href="shop-auth.php">
                            <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Login/Register
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Shop Search Form (moved to far right) -->
                <li class="nav-item">
                    <form class="d-flex" action="shop-search.php" method="GET" style="min-width:180px;">
                        <div class="input-group">
                            <input type="search" class="form-control" placeholder="Search..." name="q"
                                value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" required />
                            <button class="btn btn-light search-btn" title="search" type="submit" style="display:flex;align-items:center;">
                                <i class="bi bi-search" aria-hidden="true" style="color: var(--brand-secondary, #2487ce) !important;"></i>
                                <span class="visually-hidden">Search</span>
                            </button>
                        </div>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
</div>

<style>
/* Shop Navigation Styles (Blog Style) */
.shop-navbar {
    border-radius: 8px !important;
    margin: 0 !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative; /* Establish positioning context */
}

.shop-navbar .nav-link {
    color: rgba(255, 255, 255, 0.75) !important;
    transition: color 0.15s ease-in-out;
    position: relative;
}

.shop-navbar .nav-link:hover {
    color: rgba(255, 255, 255, 0.95) !important;
    text-decoration: none;
}

.shop-navbar .nav-link.active {
    color: #fff !important;
    font-weight: 600;
}

.shop-navbar .nav-link.active::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 80%;
    height: 2px;
    background-color: var(--bs-light, #f8f9fa);
    border-radius: 1px;
}

.shop-navbar .nav-link:focus {
    color: #fff !important;
    outline: 2px solid rgba(255, 255, 255, 0.5);
    outline-offset: 2px;
}

.shop-navbar .dropdown-menu {
    border: 1px solid #dee2e6;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    z-index: 10050 !important; /* Higher than main navigation */
    position: absolute !important; /* Ensure proper positioning */
}

.shop-navbar .dropdown {
    position: static; /* Allow dropdown to escape container constraints */
}

@media (min-width: 992px) {
    .shop-navbar .dropdown {
        position: relative; /* Normal positioning on desktop */
    }
}

.shop-navbar .dropdown-item {
    color: #212529;
    transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
}

.shop-navbar .dropdown-item:hover,
.shop-navbar .dropdown-item:focus {
    background-color: var(--bs-primary, #0d6efd);
    color: #fff;
}

.shop-navbar .dropdown-item.active {
    background-color: var(--bs-primary, #0d6efd);
    color: #fff;
    font-weight: 600;
}

.shop-navbar .navbar-toggler {
    border-color: rgba(255, 255, 255, 0.3);
    color: white;
}

.shop-navbar .navbar-toggler:focus {
    box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
    outline: none;
}

.shop-navbar .navbar-toggler:hover {
    border-color: rgba(255, 255, 255, 0.5);
}

.badge.text-bg-light {
    background-color: white !important;
    color: var(--brand-primary, #124265) !important;
    font-weight: bold;
}

.search-btn {
    transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
}

.search-btn:hover, .search-btn:focus {
    background: var(--brand-primary, #124265) !important;
}

.search-btn:hover .bi-search, .search-btn:focus .bi-search {
    color: #fff !important;
}

/* Avatar dropdown styling */
.shop-navbar .dropdown-toggle::after {
    margin-left: 0.5rem;
}

.shop-navbar .dropdown-toggle:focus {
    box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
}

/* Login/Register button styling */
.shop-navbar .btn-outline-primary {
    border-color: rgba(255, 255, 255, 0.5);
    color: rgba(255, 255, 255, 0.85);
    transition: all 0.15s ease-in-out;
}

.shop-navbar .btn-outline-primary:hover,
.shop-navbar .btn-outline-primary:focus {
    border-color: #fff;
    background-color: #fff;
    color: var(--brand-primary, #0d6efd);
    box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.25);
}

@media (max-width: 991.98px) {
    .shop-navbar {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    
    .shop-navbar .container {
        max-width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    
    .shop-navbar .navbar-nav .nav-link {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
    
    .shop-navbar .navbar-nav .dropdown-menu {
        margin-left: 1rem;
    }
    
    .shop-navbar .navbar-nav form {
        margin: 0.5rem 1rem;
        min-width: 150px !important;
        max-width: calc(100vw - 3rem) !important;
    }
    
    .shop-navbar .nav-link.active::after {
        display: none;
    }
    
    .shop-navbar .nav-link.active {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 0.375rem;
    }
}</style>
