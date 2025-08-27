<?php
/*
PAGE NAME  : search.php
LOCATION   : public_html/search.php
DESCRIPTION: This page displays search results for blog posts.
FUNCTION   : Users can search for posts by keyword, view results, and use pagination. Sidebar and layout match blog.php/post.php.
CHANGE LOG : Refactored to use unified includes, layout, and sidebar logic.
2025-08-04 : Refactored to match blog.php/post.php structure and conventions.
*/
// Include necessary files
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
include_once "assets/includes/blog_load.php";

echo '<div class="container-fluid">';
echo '<div class="row">';

if ($settings['sidebar_position'] == 'Left') {
    echo '<div class="col-md-4 order-1" id="sidebar-left">';
    sidebar();
    echo '</div>';
    echo '<div class="col-md-8 order-2 mb-3">';
} else if ($settings['sidebar_position'] == 'Right') {
    echo '<div class="col-md-8 order-1 mb-3">';
} else {
    echo '<div class="col-md-8 mb-3">';
}
echo '<div class="card">';
echo '<div class="card-header"><i class="bi bi-search" aria-hidden="true"></i> Search</div>';
echo '<div class="card-body">';

// Search logic
if (isset($_GET['q'])) {
    $word = $_GET['q'];
    if (strlen($word) < 2) {
        echo '<div class="alert alert-warning">Enter at least 2 characters to search.</div>';
    } else {
        $postsperpage = 8;
        $pageNum = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($pageNum - 1) * $postsperpage;
        $searchWord = '%' . $word . '%';
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE active = 'Yes' AND (title LIKE :word OR content LIKE :word) ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':word', $searchWord, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $postsperpage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = count($posts);
        if ($count == 0) {
            echo '<div class="alert alert-info">No results found.</div>';
        } else {
            echo '<div class="alert alert-success">' . $count . ' results for <b>"' . htmlspecialchars($word) . '"</b></div>';
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
                echo '      <a href="post.php?name=' . htmlspecialchars($row['slug']) . '">' . $image . '</a>';
                echo '    </div>';
                echo '    <div class="col-md-8">';
                echo '      <div class="card-body">';
                echo '        <div class="d-flex justify-content-between align-items-center row">';
                echo '          <div class="col-md-9">';
                echo '            <a href="post.php?name=' . htmlspecialchars($row['slug']) . '"><h5 class="card-title">' . htmlspecialchars($row['title']) . '</h5></a>';
                echo '          </div>';
                echo '          <div class="col-md-3">';
                echo '            <a href="category.php?name=' . htmlspecialchars(post_categoryslug($row['category_id'])) . '"><span class="badge bg-primary float-end">' . htmlspecialchars(post_category($row['category_id'])) . '</span></a>';
                echo '          </div>';
                echo '        </div>';
                echo '        <div class="d-flex justify-content-between align-items-center mb-3">';
                echo '          <small>Posted by <b><i class="bi bi-person" aria-hidden="true"></i> ' . htmlspecialchars(post_author($row['author_id'])) . '</b> on <b><i class="bi bi-calendar-event" aria-hidden="true"></i> ' . date($settings['date_format'], strtotime($row['date'])) . '</b></small>';
                echo '          <small class="float-end"><i class="bi bi-chat-dots" aria-hidden="true"></i> <a href="post.php?name=' . htmlspecialchars($row['slug']) . '#comments" class="blog-comments"><b>' . post_commentscount($row['id']) . '</b></a></small>';
                echo '        </div>';
                echo '        <p class="card-text">' . short_text(strip_tags(html_entity_decode($row['content'])), 200) . '</p>';
                echo '      </div>';
                echo '    </div>';
                echo '  </div>';
                echo '</div>';
            }
            // Pagination
            $stmt_count = $pdo->prepare("SELECT COUNT(id) AS numrows FROM blog_posts WHERE active = 'Yes' AND (title LIKE :word OR content LIKE :word)");
            $stmt_count->bindParam(':word', $searchWord, PDO::PARAM_STR);
            $stmt_count->execute();
            $row_count = $stmt_count->fetch(PDO::FETCH_ASSOC);
            $numrows = $row_count ? (int)$row_count['numrows'] : 0;
            $maxPage = ceil($numrows / $postsperpage);
            $pagenums = '';
            echo '<center>';
            for ($page = 1; $page <= $maxPage; $page++) {
                if ($page == $pageNum) {
                    $pagenums .= "<a href='?q=$word&page=$page' class='btn btn-primary'>$page</a> ";
                } else {
                    $pagenums .= "<a href=\"?q=$word&page=$page\" class='btn btn-default'>$page</a> ";
                }
            }
            if ($pageNum > 1) {
                $page = $pageNum - 1;
                $previous = "<a href=\"?q=$word&page=$page\" class='btn btn-default'><i class='bi bi-arrow-left' aria-hidden='true'></i> Previous</a> ";
                $first = "<a href=\"?q=$word&page=1\" class='btn btn-default'>First</a> ";
            } else {
                $previous = '';
                $first = '';
            }
            if ($pageNum < $maxPage) {
                $page = $pageNum + 1;
                $next = "<a href=\"?q=$word&page=$page\" class='btn btn-default'><i class='bi bi-arrow-right' aria-hidden='true'></i> Next</a> ";
                $last = "<a href=\"?q=$word&page=$maxPage\" class='btn btn-default'>Last</a> ";
            } else {
                $next = '';
                $last = '';
            }
            echo $first . $previous . $pagenums . $next . $last;
            echo '</center>';
        }
    }
} else {
    echo '<div class="alert alert-info">Enter a search term above.</div>';
}

echo '</div>';
echo '</div>';
echo '</div>';

if ($settings['sidebar_position'] == 'Right') {
    echo '<div class="col-md-4 order-2" id="sidebar-right">';
    sidebar();
    echo '</div>';
}
echo '</div>';
echo '</div>';
include_once __DIR__ . '/assets/includes/footer.php';
?>