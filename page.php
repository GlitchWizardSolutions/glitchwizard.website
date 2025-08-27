<?php
/*
PAGE NAME  : page.php
LOCATION   : public_html/page.php
DESCRIPTION: This page displays a static page from the blog system.
FUNCTION   : Users can view, comment on, and share blog posts. Admins can manage posts and comments.
CHANGE LOG : Initial creation of page.php to display static pages.
2025-08-24 : Added pagination for blog posts.
2025-08-25 : Improved comment system with user avatars.
2025-08-26 : Enhanced SEO features for blog posts.
2025-08-05 : Refactored to use PDO and unified layout.
*/

// Include necessary files
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
include_once "assets/includes/blog_load.php";

// Sidebar (left)
if ($settings['sidebar_position'] == 'Left') {
    echo '<div class="row">';
    echo '<div class="col-md-4 order-1">';
    sidebar();
    echo '</div>';
    echo '<div class="col-md-8 order-2">';
} else {
    echo '<div class="row">';
    echo '<div class="col-md-8 order-1">';
}

// Get slug from URL
$slug = isset($_GET['name']) ? trim($_GET['name']) : '';
if (empty($slug)) {
    echo '<meta http-equiv="refresh" content="0; url=' . $settings['site_url'] . '">';
    exit;
}

global $pdo;
$stmt = $pdo->prepare("SELECT * FROM blog_pages WHERE slug = ? LIMIT 1");
$stmt->execute([$slug]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    echo '<meta http-equiv="refresh" content="0; url=' . $settings['site_url'] . '">';
    exit;
}

echo '<div class="card mb-3">';
echo '  <div class="card-header">' . htmlspecialchars($row['title']) . '</div>';
echo '  <div class="card-body">' . html_entity_decode($row['content']) . '</div>';
echo '</div>';

// Sidebar (right)
if ($settings['sidebar_position'] == 'Right') {
    echo '</div><div class="col-md-4 order-2">';
    sidebar();
    echo '</div>';
} else {
    echo '</div>';
}
 
?>
</div>
<?php
// Use public footer for unified branding 
include 'assets/includes/footer.php';
?>