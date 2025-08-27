<?php
/*
PAGE NAME  : category.php
LOCATION   : public_html/category.php
DESCRIPTION: This page displays posts in a single category.
FUNCTION   : Users can view all posts in a category, with pagination and sidebar widgets.
CHANGE LOG : Initial creation of category.php to display category posts.
2025-08-04 : Refactored to use PDO, unified layout and includes with blog.php/post.php.
*/
// Include necessary files
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
include_once "assets/includes/blog_load.php";

echo '<div class="container-fluid">';
echo '<div class="row">';

if ($settings['sidebar_position'] == 'Left') {
    echo '<div class="col-md-4" id="sidebar-left">';
    sidebar();
    echo '</div>';
}

$slug = $_GET['name'] ?? '';
if (empty($slug)) {
    echo '<meta http-equiv="refresh" content="0; url=blog.php">';
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM blog_categories WHERE slug = ? LIMIT 1");
$stmt->execute([$slug]);
$rw = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$rw) {
    echo '<meta http-equiv="refresh" content="0; url=blog.php">';
    exit();
}
// ...existing code...
$category_id   = $rw['id'];
$category_name = $rw['category'];
echo '<div class="col-md-8 mb-3">';
echo '<div class="card">';
echo '<div class="card-header"><i class="bi bi-file-text" aria-hidden="true"></i> Blog - ' . htmlspecialchars($rw['category']) . '</div>';
echo '<div class="card-body">';
// ...existing code...
// Pagination setup
$postsperpage = 8;
$pageNum = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($pageNum - 1) * $postsperpage;
// Filtering logic for categories[] and tags[]
$filter_sql = " WHERE active = 'Yes'";
$params = [];
// Filter by category slug (main page logic)
if (!empty($slug)) {
    $filter_sql .= " AND category_id = ?";
    $params[] = $category_id;
}
// Filter by categories[]
if (!empty($_GET['categories']) && is_array($_GET['categories'])) {
    $slugs = array_filter((array)$_GET['categories']);
    if (count($slugs) > 0) {
        $in = rtrim(str_repeat('?,', count($slugs)), ',');
        $stmt_cat = $pdo->prepare("SELECT id FROM blog_categories WHERE slug IN ($in)");
        $stmt_cat->execute($slugs);
        $cat_ids = $stmt_cat->fetchAll(PDO::FETCH_COLUMN);
        if ($cat_ids) {
            $placeholders = rtrim(str_repeat('?,', count($cat_ids)), ',');
            $filter_sql .= " AND category_id IN ($placeholders)";
            foreach ($cat_ids as $cid) {
                $params[] = $cid;
            }
        } else {
            $filter_sql .= " AND 0";
        }
    }
}
// Filter by tags[] (assumes blog_post_tags table: post_id, tag_id)
if (!empty($_GET['tags']) && is_array($_GET['tags'])) {
    $slugs = array_filter((array)$_GET['tags']);
    if (count($slugs) > 0) {
        $in = rtrim(str_repeat('?,', count($slugs)), ',');
        $stmt_tag = $pdo->prepare("SELECT id FROM blog_tags WHERE tag IN ($in)");
        $stmt_tag->execute($slugs);
        $tag_ids = $stmt_tag->fetchAll(PDO::FETCH_COLUMN);
        if ($tag_ids) {
            $placeholders = rtrim(str_repeat('?,', count($tag_ids)), ',');
            $filter_sql .= " AND id IN (SELECT post_id FROM blog_post_tags WHERE tag_id IN ($placeholders))";
            foreach ($tag_ids as $tid) {
                $params[] = $tid;
            }
        } else {
            $filter_sql .= " AND 0";
        }
    }
}
$sql = "SELECT * FROM blog_posts" . $filter_sql . " ORDER BY id DESC LIMIT " . (int)$postsperpage . " OFFSET " . (int)$offset;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$posts) {
    echo '<div class="alert alert-info">There are no published posts</div>';
} else {
    foreach ($posts as $row) {
        $image = "";
        if ($row['image'] != "") {
            $img_path = $row['image'];
            if (!preg_match('/^(https?:\/\/|\/)/', $img_path)) {
                $img_path = 'admin/blog/blog_post_images/' . ltrim($img_path, '/');
            }
            $image = '<img src="' . htmlspecialchars($img_path) . '" alt="' . htmlspecialchars($row['title']) . '" class="rounded-start" width="100%" height="100%">';
        } else {
            $image = '<svg class="bd-placeholder-img rounded-start" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>No Image</title><rect width="100%" height="100%" fill="#55595c"/><text x="37%" y="50%" fill="#eceeef" dy=".3em">No Image</text></svg>';
        }
        echo '<div class="card shadow-sm mb-3">';
        echo '  <div class="row g-0">';
        echo '    <div class="col-md-4">';
        echo '      <a href="post.php?name=' . urlencode($row['slug']) . '">' . $image . '</a>';
        echo '    </div>';
        echo '    <div class="col-md-8">';
        echo '      <div class="card-body">';
        echo '        <div class="d-flex justify-content-between align-items-center row">';
        echo '          <div class="col-md-12">';
        echo '            <a href="post.php?name=' . urlencode($row['slug']) . '"><h5 class="card-title">' . htmlspecialchars($row['title']) . '</h5></a>';
        echo '          </div>';
        echo '        </div>';
        // Avatar logic using get_user_avatar_info (same as post.php)
        $author_id = $row['author_id'];
        $author_username = '';
        $author_avatar_path = 'accounts_system/assets/uploads/avatars/default-guest.png';
        $avatar_size = 32;
        $stmt_author = $pdo->prepare('SELECT username, avatar FROM accounts WHERE id = ? LIMIT 1');
        $stmt_author->execute([$author_id]);
        $author = $stmt_author->fetch(PDO::FETCH_ASSOC);
        if ($author) {
            $author_username = $author['username'];
            $avatar_filename = !empty($author['avatar']) ? basename($author['avatar']) : '';
            if ($avatar_filename && file_exists('accounts_system/assets/uploads/avatars/' . $avatar_filename)) {
                $author_avatar_path = 'accounts_system/assets/uploads/avatars/' . $avatar_filename;
            }
        }
        $author_name = htmlspecialchars(post_author($author_id));
        $avatar_img = '<img src="' . htmlspecialchars($author_avatar_path) . '" alt="' . $author_name . '" class="rounded-circle me-1" width="' . $avatar_size . '" height="' . $avatar_size . '" style="object-fit:cover;vertical-align:middle;">';

        echo '        <div class="mb-3">';
        echo '          <span class="me-2">Posted by ' . $avatar_img . '<b>' . $author_name . '</b></span>';
        echo '          <br />';
    echo '          <span class="me-2"><i class="bi bi-calendar-event" aria-hidden="true"></i> ' . date($settings['date_format'], strtotime($row['date'])) . '</span>';
    echo '          <span class="me-2"><i class="bi bi-eye" aria-hidden="true"></i> Views <b>' . htmlspecialchars($row['views']) . '</b></span>';
    echo '          <span class="me-2"><i class="bi bi-chat-dots" aria-hidden="true"></i> Comments <b>' . htmlspecialchars(post_commentscount($row['id'])) . '</b></span>';
        echo '        </div>';
        echo '        <p class="card-text">' . htmlspecialchars(short_text(strip_tags(html_entity_decode($row['content'])), 200)) . '</p>';
        echo '      </div>';
        echo '    </div>';
        echo '  </div>';
        echo '</div>';
    }

    // Pagination
    $stmt_count = $pdo->prepare("SELECT COUNT(id) AS numrows FROM blog_posts WHERE category_id = ? AND active = 'Yes'");
    $stmt_count->execute([$category_id]);
    $row_count = $stmt_count->fetch(PDO::FETCH_ASSOC);
    $numrows = $row_count ? (int)$row_count['numrows'] : 0;
    $maxPage = ceil($numrows / $postsperpage);
    $pagenums = '';
    echo '<center>';
    for ($page = 1; $page <= $maxPage; $page++) {
        if ($page == $pageNum) {
            $pagenums .= "<a href='category.php?name=" . urlencode($slug) . "&page=$page' class='btn btn-primary'>$page</a> ";
        } else {
            $pagenums .= "<a href='category.php?name=" . urlencode($slug) . "&page=$page' class='btn btn-default'>$page</a> ";
        }
    }
    if ($pageNum > 1) {
        $page = $pageNum - 1;
    $previous = "<a href='category.php?name=" . urlencode($slug) . "&page=$page' class='btn btn-default'><i class='bi bi-arrow-left' aria-hidden='true'></i> Previous</a> ";
        $first = "<a href='category.php?name=" . urlencode($slug) . "&page=1' class='btn btn-default'>First</a> ";
    } else {
        $previous = '';
        $first = '';
    }
    if ($pageNum < $maxPage) {
        $page = $pageNum + 1;
    $next = "<a href='category.php?name=" . urlencode($slug) . "&page=$page' class='btn btn-default'><i class='bi bi-arrow-right' aria-hidden='true'></i> Next</a> ";
        $last = "<a href='category.php?name=" . urlencode($slug) . "&page=$maxPage' class='btn btn-default'>Last</a> ";
    } else {
        $next = '';
        $last = '';
    }
    echo $first . $previous . $pagenums . $next . $last;
    echo '</center>';
}
 

echo '                 </div>';
echo '        </div>';
echo '    </div>';
// Remove closing main content column div so sidebar stays as sibling
if ($settings['sidebar_position'] == 'Right') {
    echo '<div class="col-md-4" id="sidebar-right">';
    sidebar();
    echo '</div>';
}
echo '</div>'; // .row
echo '</div>'; // .container-fluid
include_once __DIR__ . '/assets/includes/footer.php';
?>