<?php
/*
PAGE NAME  : doctype.php
LOCATION   : public_html/assets/includes/doctype.php
DESCRIPTION: Universal document type setup.
FUNCTION   : Centralized header, meta, SEO, Open Graph, Twitter, and asset includes.
CHANGE LOG : Initial creation of blog.php to display posts, categories, and pages.
2025-08-24 : Added dynamic meta tags for blog posts, categories, and pages.
2025-08-25 : Unified doctype.php for public and blog system.
2025-08-26 : Added dynamic pagination links for blog posts.
*/
// Dynamically locate and include private/gws-universal-config.php
$config_found = false;
$max_levels = 5;
$config_path = '../../private/gws-universal-config.php';
$dir = __DIR__;
for ($i = 0; $i <= $max_levels; $i++) {
    $try_path = $dir . str_repeat('/..', $i) . '/' . $config_path;
    if (file_exists($try_path)) {
        require_once $try_path;
        $config_found = true;
        break;
    }
}
if (!$config_found) {
    die('Critical error: Could not locate private/gws-universal-config.php');
}

// Dynamically locate and include database_settings.php (enhanced settings system)
$settings_found = false;
$settings_path = 'public_html/assets/includes/settings/database_settings.php';
for ($i = 0; $i <= $max_levels; $i++) {
    $try_settings_path = $dir . str_repeat('/..', $i) . '/' . $settings_path;
    if (file_exists($try_settings_path)) {
        require_once $try_settings_path;
        $settings_found = true;
        break;
    }
}
if (!$settings_found) {
    // No fallback - database_settings.php is required
    die('Critical error: Could not locate database_settings.php. Please ensure the database settings file exists.');
}

// Load dynamic brand colors and fonts from database
$brand_loader_found = false;
$brand_loader_path = 'public_html/assets/includes/brand_loader.php';
for ($i = 0; $i <= $max_levels; $i++) {
    $try_brand_path = $dir . str_repeat('/..', $i) . '/' . $brand_loader_path;
    if (file_exists($try_brand_path)) {
        require_once $try_brand_path;
        $brand_loader_found = true;
        break;
    }
}
if (!$brand_loader_found) {
    // Brand loader is optional - fallback to defaults
    error_log('Warning: Could not locate brand_loader.php. Using CSS defaults.');
}

// Include content highlighter for development mode
// Temporarily disabled - uncomment to re-enable highlighting
/*
if (defined('ENVIRONMENT') && ENVIRONMENT === 'dev') {
    $highlighter_path = 'assets/includes/content_highlighter.php';
    for ($i = 0; $i <= $max_levels; $i++) {
        $try_highlighter_path = $dir . str_repeat('/..', $i) . '/public_html/' . $highlighter_path;
        if (file_exists($try_highlighter_path)) {
            include_once $try_highlighter_path;
            break;
        }
    }
}
*/

// Determine page type and meta data dynamically.
$current_page = basename($_SERVER['SCRIPT_NAME'], '.php');
$is_blog = (strpos($_SERVER['SCRIPT_NAME'], 'blog_system') !== false);
$title = $business_name ?? 'My Website';
$description = $about_content['text'] ?? 'Default description';
$keywords = $settings['keywords'] ?? 'default, keywords';
$author = $settings['author'] ?? 'GlitchWizard Solutions';
$copyright = 'GlitchWizard Solutions, Tallahassee, Florida';
$robots = 'index,follow';
$social_image = $settings['default_image'] ?? $settings['logo'] ?? 'assets/img/social-share/default.jpg';
$canonical = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Blog system: dynamic meta for post, category, page.  This is central
// to this system and is included in every page. 
if ($is_blog) {
    if ($current_page === 'post' && isset($_GET['name'])) {
        $slug = $_GET['name'];
        $stmt = $pdo->prepare("SELECT title, slug, image, content, keywords FROM blog_posts WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        $rowpt = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rowpt) {
            $title = $rowpt['title'] . ' - ' . ($settings['sitename'] ?? 'Blog');
            $description = substr(strip_tags(html_entity_decode($rowpt['content'])), 0, 150);
            $keywords = $rowpt['keywords'] ?? $settings['keywords'];
            $social_image = !empty($rowpt['image']) ? $rowpt['image'] : ($settings['default_image'] ?? $settings['logo']);
            $canonical = $settings['site_url'] . '/post?name=' . urlencode($rowpt['slug']);
        }
    } elseif ($current_page === 'category' && isset($_GET['name'])) {
        $slug = $_GET['name'];
        $stmt_cat = $pdo->prepare("SELECT category, slug FROM blog_categories WHERE slug = ? LIMIT 1");
        $stmt_cat->execute([$slug]);
        $rowct = $stmt_cat->fetch(PDO::FETCH_ASSOC);
        if ($rowct) {
            $title = $rowct['category'] . ' - ' . ($settings['sitename'] ?? 'Blog');
            $description = 'View all blog posts from ' . $rowct['category'] . ' category.';
            $keywords = $settings['keywords'];
            $canonical = $settings['site_url'] . '/category?name=' . urlencode($rowct['slug']);
        }
    } elseif ($current_page === 'page' && isset($_GET['name'])) {
        $slug = $_GET['name'];
        $stmt_page = $pdo->prepare("SELECT title, content FROM blog_pages WHERE slug = ? LIMIT 1");
        $stmt_page->execute([$slug]);
        $rowpp = $stmt_page->fetch(PDO::FETCH_ASSOC);
        if ($rowpp) {
            $title = $rowpp['title'] . ' - ' . ($settings['sitename'] ?? 'Blog');
            $description = substr(strip_tags(html_entity_decode($rowpp['content'])), 0, 150);
            $keywords = $settings['keywords'];
            $canonical = $settings['site_url'] . '/page?name=' . urlencode($slug);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta name="description" content="<?= htmlspecialchars($description) ?>">
  <meta name="keywords" content="<?= htmlspecialchars($keywords) ?>">
  <meta name="author" content="<?= htmlspecialchars($author) ?>">
  <meta name="copyright" content="<?= htmlspecialchars($copyright) ?>">
  <meta name="robots" content="<?= htmlspecialchars($robots) ?>">
  <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>" />

  <!-- Open Graph / Twitter -->
  <meta property="og:title" content="<?= htmlspecialchars($title) ?>">
  <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
  <meta property="og:image" content="<?= htmlspecialchars($social_image) ?>">
  <meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
  <meta property="og:type" content="<?= $is_blog ? 'article' : 'website' ?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= htmlspecialchars($title) ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($description) ?>">
  <meta name="twitter:image" content="<?= htmlspecialchars($social_image) ?>">

  <!-- Favicons -->
  <link href="<?php echo PUBLIC_ASSETS_URL; ?>/img/favicon.png" rel="icon">
  <link href="<?php echo PUBLIC_ASSETS_URL; ?>/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Bootstrap, FontAwesome, Summernote (CDN) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote-bs5.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote-bs5.min.js"></script>

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- DataTables, AOS, Glightbox, Swiper, Custom CSS -->
  <link href="<?php echo PUBLIC_ASSETS_URL; ?>/vendor/aos/aos.css" rel="stylesheet">
  <link href="<?php echo PUBLIC_ASSETS_URL; ?>/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="<?php echo PUBLIC_ASSETS_URL; ?>/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="<?php echo PUBLIC_ASSETS_URL; ?>/css/main.css" rel="stylesheet">
  <link href="<?php echo PUBLIC_ASSETS_URL; ?>/css/brand-dynamic.css" rel="stylesheet">
  <link href="<?php echo PUBLIC_ASSETS_URL; ?>/css/section-borders.css" rel="stylesheet">
  <link href="<?php echo PUBLIC_ASSETS_URL; ?>/css/footer.css" rel="stylesheet">
  <!-- Dynamic Brand Enhancement -->
  <?php 
  // Load branding functions if not already loaded
  if (!function_exists('getActiveBrandingCSSFile')) {
      require_once __DIR__ . '/branding-functions.php';
  }
  
  // Get the active branding CSS file dynamically and add correct path
  $active_css_file = getActiveBrandingCSSFile();
  // Use the PUBLIC_ASSETS_URL constant for proper path resolution
  $css_path = PUBLIC_ASSETS_URL . "/css/" . $active_css_file;
  echo "<link href=\"{$css_path}\" rel=\"stylesheet\">\n";
  ?>
  <!-- Team Section Sleek Styling -->
  <link href="<?php echo PUBLIC_ASSETS_URL; ?>/css/team-sleek.css" rel="stylesheet">
  <script src="<?php echo PUBLIC_ASSETS_URL; ?>/js/main.js"></script>
  <script src="<?php echo PUBLIC_ASSETS_URL; ?>/js/team-interactions.js"></script>
  <link rel="icon" type="image/png" href="<?php echo PUBLIC_ASSETS_URL; ?>/img/logo.png">
  <!-- jsSocials CDN -->
  <script src="https://cdn.jsdelivr.net/npm/jssocials@1.5.0/dist/jssocials.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jssocials@1.5.0/dist/jssocials.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jssocials@1.5.0/dist/jssocials-theme-flat.css">
  
  <?php 
  // Output dynamic brand colors and fonts as CSS custom properties
  if (function_exists('outputBrandCSS')) {
      outputBrandCSS();
  }
  ?>
  
  <?php if (!empty($settings['head_customcode'])) echo base64_decode($settings['head_customcode']); ?>
</head>
<body>
<?php
// Inject global brand spinner overlay + JS globals for public context (mirrors admin/client portal)
if (!function_exists('echoBrandSpinnerOverlay')) { 
    $try_private = __DIR__ . '/../../../private/gws-universal-functions.php';
    if (file_exists($try_private)) { require_once $try_private; }
}
if (function_exists('echoBrandSpinnerOverlay') && !defined('PUBLIC_BRAND_SPINNER_OVERLAY_EMITTED')) {
    define('PUBLIC_BRAND_SPINNER_OVERLAY_EMITTED', true);
    echoBrandSpinnerOverlay(['id'=>'global-spinner-overlay','class'=>'brand-spinner-size-sm','label'=>'Loading']);
    $spinner_inline_sm = getBrandSpinnerHTML(null, ['size'=>'sm','label'=>'Loading','class'=>'align-text-bottom me-1']);
    echo '<script>window.BRAND_SPINNER_STYLE=' . json_encode(getBrandSpinnerStyle()) . ';window.BRAND_SPINNER_INLINE_SM=' . json_encode($spinner_inline_sm) . ';</script>' . "\n";
    echo '<script src="' . PUBLIC_ASSETS_URL . '/js/brand-spinner.js"></script>' . "\n";  
}
?>