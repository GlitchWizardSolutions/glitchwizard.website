<!-- header.php - Enhanced Branding System -->
<?php
// Enhanced branding system for GWS Universal Hybrid App
// Clean, modern implementation with multi-area template support

// Include universal configuration
require_once dirname(__DIR__, 3) . '/private/gws-universal-config.php';

// Load enhanced branding system
require_once __DIR__ . '/../../shared/branding-functions-enhanced.php';

// Session management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication check
$logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Determine current area based on request path
$current_area = 'public'; // Default to public
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($request_uri, '/admin/') !== false) {
    $current_area = 'admin';
} elseif (strpos($request_uri, '/client_portal/') !== false) {
    $current_area = 'client_portal';
}

// Get template configuration for current area
$active_template = getActiveBrandingTemplate($current_area);
$active_css_file = getActiveBrandingCSSFile($current_area);
$brand_variables = generateBrandCSSVariables($current_area);

// Get business configuration using proper branding system
$business_identity = getBusinessIdentity();
$business_name = $business_identity['business_name_medium'] ?? '[DB: setting_business_identity.business_name_medium → $business_name]';

// Get logo from branding assets table separately since it's not in business_identity
try {
    $stmt = $pdo->query("SELECT business_logo_main FROM setting_branding_assets WHERE id = 1 LIMIT 1");
    $logo_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $business_logo = $logo_result['business_logo_main'] ?? '[DB: setting_branding_assets.business_logo_main → $business_logo]';
} catch (Exception $e) {
    $business_logo = '[DB ERROR: setting_branding_assets.business_logo_main → ' . $e->getMessage() . ']';
}

?>

<!-- Accessibility -->
<a href="#main" class="skip-link">Skip to main content</a>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- Enhanced Branding System CSS Variables -->
<style id="enhanced-brand-variables">
:root {
<?php foreach ($brand_variables as $property => $value): ?>
    <?= $property ?>: <?= $value ?>;
<?php endforeach; ?>
}
</style>

<!-- Area-Specific Template CSS -->
<?php
// Determine the correct CSS path for the current area
$css_path = match($current_area) {
    'admin' => '/admin/assets/css/' . $active_css_file,
    'client_portal' => '/client_portal/assets/css/' . $active_css_file,
    default => '/assets/css/' . $active_css_file
};

// Build the full filesystem path to check if file exists
$document_root = $_SERVER['DOCUMENT_ROOT'];
$full_css_path = $document_root . $css_path;

// Load template CSS with cache busting
if (file_exists($full_css_path)) {
    $css_version = filemtime($full_css_path);
    echo "<link rel=\"stylesheet\" href=\"{$css_path}?v={$css_version}\">\n";
} else {
    // Fallback to default template
    $fallback_file = match($current_area) {
        'admin' => '/admin/assets/css/admin-branding.css',
        'client_portal' => '/client_portal/assets/css/client-branding.css', 
        default => '/assets/css/public-branding.css'
    };
    
    $fallback_path = $document_root . $fallback_file;
    if (file_exists($fallback_path)) {
        $css_version = filemtime($fallback_path);
        echo "<link rel=\"stylesheet\" href=\"{$fallback_file}?v={$css_version}\">\n";
    } else {
        echo "<!-- Enhanced Branding: No CSS file found for area '{$current_area}' -->\n";
        error_log("Enhanced Branding: CSS file not found - {$full_css_path} and fallback {$fallback_path}");
    }
}
?>


<!-- Enhanced Branding System Utility Classes -->
<style>
.accent-background {
    background: var(--brand-primary) !important;
    color: #fff !important;
}

.accent-color {
    color: var(--brand-primary) !important;
}

.secondary-color {
    color: var(--brand-secondary) !important;
}

.brand-font {
    font-family: var(--brand-font-family), Arial, sans-serif !important;
}

/* Navigation styling with brand colors */
.navmenu {
    padding: 0;
    z-index: 9997;
}

.navmenu ul {
    list-style: none;
    margin: 0;
    padding: 0;
    align-items: center;
}

.navmenu li {
    position: relative;
    margin: 0 15px;
}

.navmenu a,
.navmenu a:focus {
    color: #333;
    padding: 10px 0;
    font-size: 16px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: space-between;
    white-space: nowrap;
    transition: 0.3s;
    text-decoration: none;
}

.navmenu ul li a:hover,
.navmenu ul li a:focus {
    color: var(--brand-primary) !important;
    font-weight: bold !important;
    text-decoration: none !important;
}

.navmenu ul li a.active {
    color: var(--brand-secondary) !important;
    font-weight: bold !important;
    text-decoration: none !important;
}

.mobile-nav-toggle {
    cursor: pointer;
    color: var(--brand-secondary) !important;
    background: #fff !important;
    border: 2px solid var(--brand-secondary) !important;
    width: 40px !important;
    height: 40px !important;
    font-size: 20px !important;
    font-weight: 900 !important;
    border-radius: 4px !important;
    transition: all 0.3s ease;
    align-items: center;
    justify-content: center;
    display: none;
}

.mobile-nav-toggle:hover {
    background: var(--brand-secondary) !important;
    color: #fff !important;
    transform: scale(1.05);
}

.mobile-nav-toggle:focus {
    outline: 2px solid var(--brand-primary);
    outline-offset: 2px;
}

/* Desktop Navigation - Show menu, hide hamburger */
@media (min-width: 992px) {
    .mobile-nav-toggle {
        display: none !important;
    }
    
    .navmenu ul {
        display: flex;
        flex-direction: row;
        background: transparent;
        position: relative;
        width: auto;
        max-width: none;
        top: auto;
        right: auto;
        left: auto;
        margin: 0;
        border-radius: 0;
        box-shadow: none;
        max-height: none;
        overflow: visible;
        z-index: auto;
        padding: 0;
        align-items: center;
        visibility: visible;
        opacity: 1;
    }
    
    .navmenu ul li {
        width: auto;
        margin: 0 15px;
        padding: 0;
        text-align: center;
    }
    
    .navmenu ul li a {
        width: auto;
        padding: 10px 0;
        margin: 0;
        text-align: center;
        display: flex;
        color: #333;
        border-bottom: none;
        font-size: 16px;
        font-weight: 500;
        background: transparent;
    }
}

/* Mobile Navigation Styles */
@media (max-width: 991.98px) {
    .mobile-nav-toggle {
        display: flex !important;
    }
    
    .navmenu {
        position: fixed;
        inset: 0;
        padding: 10px 0;
        margin: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        transition: 0.3s;
        visibility: hidden;
        opacity: 0;
        z-index: 9999;
    }
    
    .navmenu.menu-open,
    body.mobile-nav-active .navmenu {
        visibility: visible;
        opacity: 1;
    }
    
    /* Hide menu by default on mobile */
    .navmenu ul {
        display: none;
        width: 280px;
        max-width: 70vw;
        position: absolute;
        top: 80px;
        right: 20px;
        left: auto;
        margin: 0;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        max-height: 75vh;
        overflow-y: auto;
        z-index: 10000;
        background: #fff;
        padding: 20px 0;
        align-items: flex-start;
        list-style: none;
        flex-direction: column;
    }

    /* Show menu when opened */
    .navmenu.menu-open ul,
    body.mobile-nav-active .navmenu ul {
        display: flex;
    }

    .navmenu ul li {
        width: 100%;
        margin: 0;
        padding: 0;
        text-align: left;
    }

    .navmenu ul li a {
        width: 100%;
        padding: 15px 30px;
        margin: 0;
        text-align: left;
        display: block;
        color: #333;
        border-bottom: 1px solid #f0f0f0;
        font-size: 16px;
        font-weight: 500;
        background: transparent;
    }
    
    .navmenu ul li a:hover,
    .navmenu ul li a.active {
        color: var(--brand-primary) !important;
        font-weight: bold !important;
        background: #f8f9fa !important;
    }
    
    .navmenu ul li:last-child a {
        border-bottom: none;
    }
}

.skip-link {
    position: absolute;
    left: -999px;
    top: 10px;
    background: var(--brand-primary);
    color: #fff;
    padding: 8px 16px;
    z-index: 10000;
    border-radius: 4px;
    font-weight: bold;
    text-decoration: none;
    transition: left 0.2s;
}

.skip-link:focus {
    left: 10px;
    outline: 2px solid var(--brand-secondary);
}

/* Logo styling */
.logo img {
    max-height: 96px;
    margin-right: 15px;
    transition: transform 0.3s ease;
}

.logo img:hover {
    transform: scale(1.05);
}
</style>

<body class="enhanced-branding-system <?= $current_area ?>-area template-<?= $active_template ?>">

<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">
        
        <!-- Logo and Business Name -->
        <a href="index.php" class="logo d-flex align-items-center me-auto">
            <?php if (!empty($business_logo)): ?>
                <img src="<?= htmlspecialchars($business_logo) ?>"
                     alt="<?= htmlspecialchars($business_name) ?> Logo">
            <?php endif; ?>
            <h1 class="sitename"><?= htmlspecialchars($business_name) ?></h1>
        </a>

        <!-- Navigation Menu -->
        <nav id="navmenu" class="navmenu" role="navigation" aria-label="Main navigation">
            <ul role="menubar">
                <li role="none"><a role="menuitem" href="index.php" class="active">Home</a></li>
                <li role="none"><a role="menuitem" href="about.php">About</a></li>
                <li role="none"><a role="menuitem" href="index.php#services">Services</a></li>
                <li role="none"><a role="menuitem" href="blog.php">Blog</a></li>
                <li role="none"><a role="menuitem" href="public_reviews.php">Reviews</a></li>
                <li role="none"><a role="menuitem" href="#contact">Contact</a></li>
                <?php if ($logged_in): ?>
                    <li role="none"><a role="menuitem" href="profile.php">Profile</a></li>
                    <li role="none"><a role="menuitem" href="logout.php">Log Out</a></li>
                <?php else: ?>
                    <li role="none"><a role="menuitem" href="auth.php?tab=login">Login / Register</a></li>
                <?php endif; ?>
            </ul>
            
            <!-- Mobile Menu Toggle -->
            <button class="mobile-nav-toggle bi bi-list" 
                    aria-label="Open main menu" 
                    aria-controls="navmenu"
                    aria-expanded="false" 
                    style="color: var(--brand-secondary); background: #fff; border: 2px solid var(--brand-secondary); width:40px; height:40px; font-size:24px; font-weight:900; display:flex; align-items:center; justify-content:center; border-radius:4px;">
            </button>
        </nav>
    </div>
</header>

<main id="main" class="main">

<script>
// Enhanced Navigation Menu Controller
document.addEventListener('DOMContentLoaded', function() {
    const navmenu = document.getElementById('navmenu');
    const toggleBtn = document.querySelector('.mobile-nav-toggle');
    
    // Add null checks for safety
    if (!navmenu || !toggleBtn) {
        console.warn('Enhanced Branding: Navigation elements not found');
        return;
    }
    
    function toggleMenu(e) {
        e.stopPropagation();
        const expanded = toggleBtn.getAttribute('aria-expanded') === 'true';
        
        // Toggle aria state
        toggleBtn.setAttribute('aria-expanded', !expanded);
        
        // Toggle classes
        navmenu.classList.toggle('menu-open');
        document.body.classList.toggle('mobile-nav-active');
        
        // Toggle hamburger/X icon
        if (!expanded) {
            toggleBtn.classList.remove('bi-list');
            toggleBtn.classList.add('bi-x');
        } else {
            toggleBtn.classList.remove('bi-x');
            toggleBtn.classList.add('bi-list');
        }
    }
    
    // Toggle menu on button click
    toggleBtn.addEventListener('click', toggleMenu);
    
    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!navmenu.contains(e.target) && !toggleBtn.contains(e.target) && navmenu.classList.contains('menu-open')) {
            navmenu.classList.remove('menu-open');
            document.body.classList.remove('mobile-nav-active');
            toggleBtn.setAttribute('aria-expanded', false);
            toggleBtn.classList.remove('bi-x');
            toggleBtn.classList.add('bi-list');
        }
    });
    
    // Set active navigation item based on current page
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    const navLinks = document.querySelectorAll('.navmenu a[role="menuitem"]');
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        const linkPage = link.getAttribute('href').split('/').pop();
        if (linkPage === currentPage) {
            link.classList.add('active');
        }
    });
    
    // Enhanced branding utility functions
    window.EnhancedBranding = {
        getCurrentArea: function() {
            return '<?php echo $current_area; ?>';
        },
        getActiveTemplate: function() {
            return '<?php echo $active_template; ?>';
        }
    };
});
</script>
   