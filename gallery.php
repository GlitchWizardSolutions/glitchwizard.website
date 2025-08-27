<?php
/*
PAGE NAME  : gallery.php
LOCATION   : public_html/gallery.php
DESCRIPTION: This page displays a gallery of images, organized by albums.
FUNCTION   : Users can view images grouped by albums using tab navigation. The "All" tab shows all active images, while each album tab displays images belonging to that album. Clicking an image opens a modal with its details and description. Admins can manage albums and images via backend tools. There is currently no search or sort feature for images, and images are only categorized by album.
CHANGE LOG : Initial creation of gallery.php to display images by albums.
2025-08-24 : Added album filtering for images.
2025-08-25 : Improved comment system with user avatars.
2025-08-26 : Enhanced SEO features for gallery images.
2025-08-27 : Added detailed description of gallery page functionality.
*/

// Include necessary files
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
include_once "assets/includes/blog_load.php";
?>
<div class="container<?php echo ($settings['layout'] == 'Wide') ? '-fluid' : ''; ?> mt-3 mb-5">
    <div class="row">
        <div class="col-md-8 order-2 order-md-1 mb-3">
            <div class="card">
                <div class="card-header"><i class="bi bi-images" aria-hidden="true"></i> Gallery
                    <?php
                    // Show filter summary
                    $active_filters = [];
                    if (!empty($_GET['categories'])) {
                        $active_filters[] = 'Category: ' . implode(', ', array_map('htmlspecialchars', (array)$_GET['categories']));
                    }
                    if (!empty($_GET['tags'])) {
                        $active_filters[] = 'Tags: ' . implode(', ', array_map('htmlspecialchars', (array)$_GET['tags']));
                    }
                    if (!empty($_GET['q'])) {
                        $active_filters[] = 'Search: ' . htmlspecialchars($_GET['q']);
                    }
                    if ($active_filters) {
                        echo ' &ndash; <span class="small text-secondary">' . implode(' | ', $active_filters) . '</span>';
                    }
                    ?>
                </div>
                <div class="card-body">
                    <!-- Gallery Filter/Search Panel -->
                    <form method="GET" class="p-3 mb-3 rounded" style="background: var(--brand-secondary, #4a278a); color: #fff;">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-12">
                                <label class="form-label mb-1"></label>
                                <div class="input-group">
                                    <a href="gallery.php" class="btn btn-light btn-sm px-3 d-flex justify-content-center align-items-center" title="Clear Filters" style="margin-right:4px;"><i class="bi bi-x-circle me-2" aria-hidden="true"></i> <span>Clear Filters</span></a>
                                    <input type="search" class="form-control" placeholder="Image Search" name="q" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" style="background:#fff;color:#222;" />
                                    <button class="btn btn-light search-btn" title="Image Search" type="submit" style="display:flex;align-items:center;">
                                        <i class="bi bi-search" aria-hidden="true" style="color: var(--brand-secondary, #4a278a) !important;"></i>
                                        <span class="visually-hidden">Image Search</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <style>
                            .form-check-input:checked {
                                background-color: var(--brand-primary, #593196) !important;
                                border-color: var(--brand-primary, #593196) !important;
                            }
                            .form-check-label {
                                color: #fff !important;
                            }
                            .search-btn:hover, .search-btn:focus {
                                background: var(--brand-primary, #593196) !important;
                            }
                            .search-btn:hover .bi-search, .search-btn:focus .bi-search {
                                color: #fff !important;
                            }
                        </style>
                    </form>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-all" role="tabpanel" aria-labelledby="nav-all-tab" tabindex="0">
                            <br />
                            <div class="row">
                                <?php
                                // Build filter SQL for categories/tags/search
                                $filter_sql = "";
                                $params = [];
                                $category_ids = [];
                                $tag_image_ids = [];
                                // Categories (checkboxes)
                                if (!empty($_GET['categories'])) {
                                    $slugs = array_map('strtolower', (array)$_GET['categories']);
                                    $in = str_repeat('?,', count($slugs) - 1) . '?';
                                    $stmt = $pdo->prepare("SELECT id FROM blog_gallery_categories WHERE LOWER(slug) IN ($in)");
                                    $stmt->execute($slugs);
                                    $category_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                    if ($category_ids) {
                                        $filter_sql .= " AND category_id IN (" . implode(',', array_map('intval', $category_ids)) . ")";
                                    } else {
                                        $filter_sql .= " AND 0";
                                    }
                                }
                                // Tags (checkboxes)
                                if (!empty($_GET['tags'])) {
                                    $slugs = array_map('strtolower', (array)$_GET['tags']);
                                    $in = str_repeat('?,', count($slugs) - 1) . '?';
                                    $stmt = $pdo->prepare("SELECT id FROM blog_gallery_tags WHERE LOWER(slug) IN ($in)");
                                    $stmt->execute($slugs);
                                    $tag_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                    if ($tag_ids) {
                                        $in_tag = str_repeat('?,', count($tag_ids) - 1) . '?';
                                        $stmt_img = $pdo->prepare("SELECT image_id FROM blog_gallery_image_tags WHERE tag_id IN ($in_tag)");
                                        $stmt_img->execute($tag_ids);
                                        $tag_image_ids = $stmt_img->fetchAll(PDO::FETCH_COLUMN);
                                        if ($tag_image_ids) {
                                            $filter_sql .= " AND id IN (" . implode(',', array_map('intval', $tag_image_ids)) . ")";
                                        } else {
                                            $filter_sql .= " AND 0";
                                        }
                                    } else {
                                        $filter_sql .= " AND 0";
                                    }
                                }
                                // Search (image name/category/tag)
                                if (!empty($_GET['q'])) {
                                    $q = '%' . strtolower($_GET['q']) . '%';
                                    $filter_sql .= " AND (LOWER(title) LIKE ? OR LOWER(description) LIKE ? OR id IN (SELECT image_id FROM blog_gallery_image_tags WHERE tag_id IN (SELECT id FROM blog_gallery_tags WHERE LOWER(name) LIKE ?)) OR category_id IN (SELECT id FROM blog_gallery_categories WHERE LOWER(name) LIKE ? OR LOWER(slug) LIKE ?))";
                                    $params[] = $q;
                                    $params[] = $q;
                                    $params[] = $q;
                                    $params[] = $q;
                                    $params[] = $q;
                                }
                                // Only run query if not forcibly empty
                                if (strpos($filter_sql, 'AND 0') !== false) {
                                    $rows = [];
                                } else {
                                    $sql = "SELECT * FROM blog_gallery WHERE active='Yes' $filter_sql ORDER BY title ASC";
                                    $stmt = $pdo->prepare($sql);
                                    $stmt->execute($params);
                                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                }
                                if (!$rows) {
                                    echo '<div class="alert alert-info">No images found for this filter.</div>';
                                } else {
                                    echo '<div class="list-group">';
                                    foreach ($rows as $row) {
                                        echo '<div class="list-group-item d-flex align-items-center">';
                                        echo '  <img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '" style="width:60px;height:60px;object-fit:cover;border-radius:6px;margin-right:12px;">';
                                        echo '  <div class="flex-grow-1">' . htmlspecialchars($row['title']) . '</div>';
                                        echo '  <button type="button" data-bs-toggle="modal" data-bs-target="#p' . $row['id'] . '" class="btn btn-sm btn-outline-secondary ms-2">';
                                        echo '    <i class="bi bi-info-circle" aria-hidden="true"></i> Details';
                                        echo '  </button>';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                        $stmt_alb = $pdo->query("SELECT * FROM blog_albums ORDER BY id DESC");
                        while ($rowalb = $stmt_alb->fetch(PDO::FETCH_ASSOC)) {
                            echo '<div class="tab-pane fade" id="nav-' . $rowalb['id'] . '" role="tabpanel" aria-labelledby="nav-' . $rowalb['id'] . '-tab"><br /><div class="row">';
                            $stmt_gal = $pdo->prepare("SELECT * FROM blog_gallery WHERE active='Yes' AND album_id=? ORDER BY id DESC");
                            $stmt_gal->execute([$rowalb['id']]);
                            $rows = $stmt_gal->fetchAll(PDO::FETCH_ASSOC);
                            if (!$rows) {
                                echo '<div class="alert alert-info">There are no images in this album.</div>';
                            } else {
                                foreach ($rows as $row) {
                                    echo '<div class="col-md-4 mb-3" data-bs-toggle="modal" data-bs-target="#p' . $row['id'] . '">
                                        <div class="card shadow-sm">
                                            <img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '" style="width: 100%; height: 180px;">
                                            <div class="card-body">
                                                <h6 class="card-title">' . htmlspecialchars($row['title']) . '</h6>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <button type="button" data-bs-toggle="modal" data-bs-target="#p' . $row['id'] . '" class="btn btn-sm btn-outline-secondary col-12">
                                                        <i class="bi bi-info-circle" aria-hidden="true"></i> Details
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                }
                            }
                            echo '</div></div>';
                        }
                        $stmt_img = $pdo->query("SELECT * FROM blog_gallery WHERE active='Yes' ORDER BY id DESC");
                        $rows_img = $stmt_img->fetchAll(PDO::FETCH_ASSOC);
                        if ($rows_img) {
                            foreach ($rows_img as $rowimg) {
                                $shareUrl = urlencode($settings['site_url'] . '/gallery.php?p=' . $rowimg['id']);
                                $shareText = urlencode($rowimg['title']);
                                echo '<div class="modal" id="p' . $rowimg['id'] . '">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">' . htmlspecialchars($rowimg['title']) . '</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <img src="' . htmlspecialchars($rowimg['image']) . '" width="100%" height="auto" alt="' . htmlspecialchars($rowimg['title']) . '" /><br /><br />' . html_entity_decode($rowimg['description']) . '
                                                <hr />
                                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                                    <span class="me-2">Share:</span>
                                                    <a href="https://www.facebook.com/sharer/sharer.php?u=' . $shareUrl . '" target="_blank" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" title="Share on Facebook">' . getBrandIconSVG('facebook', ['width'=>18,'height'=>18,'aria-hidden'=>'true','class'=>'me-1']) . ' Facebook</a>
                                                    <a href="https://twitter.com/intent/tweet?url=' . $shareUrl . '&text=' . $shareText . '" target="_blank" class="btn btn-sm btn-outline-info d-flex align-items-center gap-1" title="Share on Twitter">' . getBrandIconSVG('twitter', ['width'=>18,'height'=>18,'aria-hidden'=>'true','class'=>'me-1']) . ' Twitter</a>
                                                    <a href="https://wa.me/?text=' . $shareText . '%20' . $shareUrl . '" target="_blank" class="btn btn-sm btn-outline-success d-flex align-items-center gap-1" title="Share on WhatsApp">' . getBrandIconSVG('whatsapp', ['width'=>18,'height'=>18,'aria-hidden'=>'true','class'=>'me-1']) . ' WhatsApp</a>
                                                    <a href="mailto:?subject=' . $shareText . '&body=' . $shareUrl . '" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1" title="Share by Email"><i class="bi bi-envelope" aria-hidden="true"></i> Email</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 order-1 order-md-2 mb-3">
            <?php
            // Gallery sidebar (categories, tags, subscribe)
            if (function_exists('gallery_sidebar')) {
                gallery_sidebar();
            }
            ?>
        </div>
    </div>
<?php
// Use public footer for unified branding 
include 'assets/includes/footer.php';

