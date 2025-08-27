<?php
/*
PAGE NAME  : blog.php
LOCATION   : public_html/blog.php
DESCRIPTION: This page displays the blog posts, categories, and pages from the blog system.
FUNCTION   : Users can view, comment on, and share blog posts. Admins can manage posts and comments.
CHANGE LOG : Initial creation of blog.php to display posts, categories, and pages.
2025-08-24 : Added pagination for blog posts.
2025-08-25 : Improved comment system with user avatars.
2025-08-26 : Enhanced SEO features for blog posts.
*/

// Include necessary files
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
include_once "assets/includes/blog_load.php";
?>
<!-- Blog Posts Section -->
<div class="container">
    <div class="row">
        <div class="col-md-8 mb-3">
            <div class="card">
                <div class="card-header accent-background"><i class="bi bi-file-text" aria-hidden="true"></i> Blog Posts</div>
                <div class="card-body">
                    <?php
                    $postsperpage = 8;
                    $pageNum = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
                    $rows = ($pageNum - 1) * $postsperpage;
                    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE active='Yes' ORDER BY id DESC LIMIT ?, ?");
                    $stmt->bindValue(1, $rows, PDO::PARAM_INT);
                    $stmt->bindValue(2, $postsperpage, PDO::PARAM_INT);
                    $stmt->execute();
                    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (!$posts)
                    {
                        echo '<div class="alert alert-info">There are no published posts</div>';
                    } else
                    {
                        foreach ($posts as $row)
                        {
                            $image = "";
                            if ($row['image'] != "")
                            {
                                $img_path = $row['image'];
                                if (preg_match('/^(https?:\/\/)/', $img_path)) {
                                } else {
                                    $img_path = 'admin/blog/blog_post_images/' . ltrim($img_path, '/');
                                }
                                $image = '<img src="' . htmlspecialchars($img_path) . '" alt="' . htmlspecialchars($row['title']) . '" class="rounded-start" style="width:100%;height:100%;object-fit:cover;">';
                            } else
                            {
                                $image = '<div class="rounded-start d-flex align-items-center justify-content-center" style="width:100%;height:180px;min-height:120px;background:#e9ecef;color:#888;text-align:center;font-size:1.2em;">No Image</div>';
                            }
                            echo '
            <div class="card shadow-sm mb-3">
                <div class="row g-0">
                    <div class="col-md-4">
                        <a href="post.php?name=' . urlencode($row['slug']) . '">
                            ' . $image . '
                        </a>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center row">
                                <div class="col-md-9">
                                    <a href="post.php?name=' . urlencode($row['slug']) . '">
                                        <h5 class="card-title">' . htmlspecialchars($row['title']) . '</h5>
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="category.php?name=' . htmlspecialchars(post_categoryslug($row['category_id'])) . '">
                                        <span class="badge accent-background float-end">' . htmlspecialchars(post_category($row['category_id'])) . '</span>
                                    </a>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small>
                                    ' . (function ($author_id, $pdo, $settings, $row) {
                                $author_username = "-";
                                if (!empty($author_id)) {
                                    $stmt_author = $pdo->prepare("SELECT username FROM accounts WHERE id = ? LIMIT 1");
                                    $stmt_author->execute([$author_id]);
                                    $author = $stmt_author->fetch(PDO::FETCH_ASSOC);
                                    if ($author && !empty($author['username'])) {
                                        $author_username = htmlspecialchars($author['username']);
                                    }
                                }
                                return 'Posted by <b><i class="bi bi-person" aria-hidden="true"></i> ' . $author_username . '</b> on <b><i class="bi bi-calendar-date" aria-hidden="true"></i> ' . date($settings['date_format'], strtotime($row['date'])) . ', ' . htmlspecialchars($row['time']) . '</b>';
                            })($row['author_id'], $pdo, $settings, $row) . '
                                </small>
                                <small class="float-end"><i class="bi bi-chat-dots" aria-hidden="true"></i>
                                    <a href="post.php?name=' . urlencode($row['slug']) . '#comments" class="blog-comments"><b>' . htmlspecialchars(post_commentscount($row['id'])) . '</b></a>
<style>
  a {
    text-decoration: none !important;
  }
</style>
                                </small>
                            </div>
                            <p class="card-text">' . htmlspecialchars(short_text(strip_tags(html_entity_decode($row['content'])), 200)) . '</p>
                        </div>
                    </div>
                </div>
            </div>';
                        }
                        // Pagination
                        $stmt = $pdo->query("SELECT COUNT(id) AS numrows FROM blog_posts WHERE active='Yes'");
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        $numrows = $row ? $row['numrows'] : 0;
                        $maxPage = ceil($numrows / $postsperpage);
                        $pagenums = '';
                        echo '<center>';
                        $brand_primary = isset($settings['brand_primary_color']) ? $settings['brand_primary_color'] : '#593196';
                        for ($page = 1; $page <= $maxPage; $page++)
                        {
                            if ($page == $pageNum)
                            {
                                $pagenums .= "<a href='?page=$page' class='btn' style='background: $brand_primary; color: #fff;'>$page</a> ";
                            } else
                            {
                                $pagenums .= "<a href=\"?page=$page\" class='btn btn-default'>$page</a> ";
                            }
                        }
                        if ($pageNum > 1)
                        {
                            $page = $pageNum - 1;
                            $previous = "<a href=\"?page=$page\" class='btn btn-default'><i class='bi bi-arrow-left' aria-hidden='true'></i> Previous</a> ";
                            $first = "<a href=\"?page=1\" class='btn btn-default'>First</a> ";
                        } else
                        {
                            $previous = '';
                            $first = '';
                        }
                        if ($pageNum < $maxPage)
                        {
                            $page = $pageNum + 1;
                            $next = "<a href=\"?page=$page\" class='btn btn-default'><i class='bi bi-arrow-right' aria-hidden='true'></i> Next</a> ";
                            $last = "<a href=\"?page=$maxPage\" class='btn btn-default'>Last</a> ";
                        } else
                        {
                            $next = '';
                            $last = '';
                        }
                        echo $first . $previous . $pagenums . $next . $last;
                        echo '</center>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <?php sidebar(); // Sidebar function should NOT output its own .col-md-4 div, only its content ?>
        </div>
    </div>
</div>
<?php
include 'shared/chat_widget.php';
include_once "assets/includes/contact.php";
// Use public footer for unified branding 
include 'assets/includes/footer.php';
?>