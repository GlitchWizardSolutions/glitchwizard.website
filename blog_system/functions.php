<?php
/*
PAGE NAME  : functions.php
LOCATION   : public_html/blog_system/functions.php
DESCRIPTION: This file contains various functions used throughout the blog system.
FUNCTION   : Provides utility functions for post management, comment handling, and user interactions.
CHANGE LOG : Initial creation of functions.php to centralize blog-related functions.
2025-08-24 : Added function for retrieving post categories.
2025-08-25 : Improved comment system with user avatars.
2025-08-26 : Enhanced SEO features for blog posts.
*/ 
global $settings, $phpblog_version;
if (!function_exists('short_text')){
 function short_text($text, $length){
    $maxTextLenght = $length;
    $aspace        = " ";
    if (strlen($text) > $maxTextLenght) {
        $text = substr(trim($text), 0, $maxTextLenght);
        $text = substr($text, 0, strlen($text) - strpos(strrev($text), $aspace));
        $text = $text . "...";
    }
    return $text;
 }
}//function exists

if (!function_exists('post_title')) {
    function post_title($post_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT title FROM blog_posts WHERE id = ? LIMIT 1");
        $stmt->execute([$post_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['title'] : '-';
    }
}
if (!function_exists('emoticons')){
function emoticons($text){
   
    $icons = array(
        ':)' => '🙂',
        ':-)' => '🙂',
        ':}' => '🙂',
        ':D' => '😀',
        ':d' => '😁',
        ':-D ' => '😂',
        ';D' => '😂',
        ';d' => '😂',
        ';)' => '😉',
        ';-)' => '😉',
        ':P' => '😛',
        ':-P' => '😛',
        ':-p' => '😛',
        ':p' => '😛',
        ':-b' => '😛',
        ':-Þ' => '😛',
        ':(' => '🙁',
        ';(' => '😓',
        ':\'(' => '😓',
        ':o' => '😮',
        ':O' => '😮',
        ':0' => '😮',
        ':-O' => '😮',
        ':|' => '😐',
        ':-|' => '😐',
        ' :/' => ' 😕',
        ':-/' => '😕',
        ':X' => '😷',
        ':x' => '😷',
        ':-X' => '😷',
        ':-x' => '😷',
        '8)' => '😎',
        '8-)' => '😎',
        'B-)' => '😎',
        ':3' => '😊',
        '^^' => '😊',
        '^_^' => '😊',
        '<3' => '😍',
        ':*' => '😘',
        'O:)' => '😇',
        '3:)' => '😈',
        'o.O' => '😵',
        'O_o' => '😵',
        'O_O' => '😵',
        'o_o' => '😵',
        '0_o' => '😵',
        'T_T' => '😵',
        '-_-' => '😑',
        '>:O' => '😆',
        '><' => '😆',
        '>:(' => '😣',
        ':v' => '🙃',
        '(y)' => '👍',
        ':poop:' => '💩',
        ':|]' => '🤖'
    );
    return strtr($text, $icons);
}
}//function exists



        if (!function_exists('sidebar'))
        {
            function sidebar()
            {
                global $pdo, $settings;
                if (!isset($pdo) || !$pdo) {
                    echo '<div class="alert alert-danger">Database connection not available.</div>';
                    return;
                }
                ?>
                <div id="sidebar">
                    <div class="card">
                        <div class="card-header"><i class="fas fa-list"></i> Categories</div>
                        <div class="card-body">
                            <ul class="list-group">
                                <?php
                                $stmt = $pdo->query("SELECT * FROM blog_categories ORDER BY category ASC");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
                                {
                                    $category_id = $row['id'];
                                    $postc_stmt = $pdo->prepare("SELECT COUNT(id) FROM blog_posts WHERE category_id = ? AND active = 'Yes'");
                                    $postc_stmt->execute([$category_id]);
                                    $posts_count = $postc_stmt->fetchColumn();
                                    echo '<a href="category.php?name=' . htmlspecialchars($row['slug']) . '">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    ' . htmlspecialchars($row['category']) . '
                                    <span class="badge bg-secondary rounded-pill">' . $posts_count . '</span>
                                </li>
                            </a>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs nav-justified">
                                <li class="nav-item active">
                                    <a class="nav-link active" href="#commentss" data-bs-toggle="tab">
                                        Recent Comments
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#latestposts" data-bs-toggle="tab">
                                        Latest Posts
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div id="commentss" class="tab-pane fade show active">
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM blog_comments WHERE approved='Yes' ORDER BY date DESC, id DESC LIMIT 4");
                                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    if (!$comments)
                                    {
                                        echo "There are no comments";
                                    } else
                                    {
                                        foreach ($comments as $row)
                                        {
                                            $badge = '';
                                            if ($row['guest'] == 'Yes' || empty($row['account_id'])) {
                                                $acavatar = 'accounts_system/assets/uploads/avatars/default-guest.svg';
                                                $badge = ' <span class="badge bg-secondary">Guest</span>';
                                                $acuthor = htmlspecialchars($row['username']);
                                            } else {
                                                // Registered user: get from accounts using account_id
                                                $stmt2 = $pdo->prepare('SELECT username, avatar FROM accounts WHERE id = ? LIMIT 1');
                                                $stmt2->execute([$row['account_id']]);
                                                $user = $stmt2->fetch(PDO::FETCH_ASSOC);
                                                if ($user && !empty($user['avatar'])) {
                                                    $acavatar = 'accounts_system/assets/uploads/avatars/' . $user['avatar'];
                                                } else {
                                                    $acavatar = 'accounts_system/assets/uploads/avatars/default-guest.svg';
                                                }
                                                $acuthor = $user && !empty($user['username']) ? htmlspecialchars($user['username']) : 'User';
                                            }
                                            // Get post info
                                            $stmt3 = $pdo->prepare('SELECT slug, title FROM blog_posts WHERE id = ? AND active = "Yes" LIMIT 1');
                                            $stmt3->execute([$row['post_id']]);
                                            while ($row2 = $stmt3->fetch(PDO::FETCH_ASSOC))
                                            {
                                                echo '<div class="mb-2 d-flex flex-start align-items-center bg-light rounded border">
                                            <a href="post.php?name=' . urlencode($row2['slug']) . '#comments" class="ms-2">
                                                <img class="rounded-circle shadow-1-strong me-2" src="' . htmlspecialchars($acavatar) . '" alt="' . $acuthor . '" width="55" height="55" />
                                            </a>
                                            <div class="mt-1 mb-1 ms-1 me-1">
                                                <h6 class="text-primary mb-1"><a href="post.php?name=' . urlencode($row2['slug']) . '#comments">' . $acuthor . '</a></h6>
                                                <p class="text-muted small mb-0">on <a href="post.php?name=' . urlencode($row2['slug']) . '#comments">' . htmlspecialchars($row2['title']) . '</a><br />
                                                    <i class="fas fa-calendar"></i> ' . date($settings['date_format'] . ' g:i a', strtotime($row['date'])) . '
                                        </p>
                                    </div>
                                </div>
';
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                                <div id="latestposts" class="tab-pane fade">
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM blog_posts WHERE active='Yes' ORDER BY id DESC LIMIT 4");
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
                                                if (!preg_match('/^(https?:\/\/|\/)/', $img_path))
                                                {
                                                    $img_path = 'admin/blog/blog_post_images/' . ltrim($img_path, '/');
                                                }
                                                $image = '<img class="rounded shadow-1-strong me-1" src="' . htmlspecialchars($img_path) . '" alt="' . htmlspecialchars($row['title']) . '" width="70" height="70" />';
                                            } else
                                            {
                                                $image = '<svg class="bd-placeholder-img rounded shadow-1-strong me-1" width="70" height="70" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="No Image" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Image</title><rect width="70" height="70" fill="#55595c"/><text x="0%" y="50%" fill="#eceeef" dy=".1em">No Image</text></svg>';
                                            }
                                            // Author avatar and username logic
                                            $author_username = '-';
                                            $author_avatar_path = 'accounts_system/assets/uploads/avatars/default-guest.svg';
                                            if (!empty($row['author_id'])) {
                                                $author_username = htmlspecialchars(post_author($row['author_id']));
                                                $stmt_author = $pdo->prepare('SELECT avatar FROM accounts WHERE id = ? LIMIT 1');
                                                $stmt_author->execute([$row['author_id']]);
                                                $author = $stmt_author->fetch(PDO::FETCH_ASSOC);
                                                $avatar_filename = !empty($author['avatar']) ? basename($author['avatar']) : '';
                                                if ($avatar_filename && file_exists('accounts_system/assets/uploads/avatars/' . $avatar_filename)) {
                                                    $author_avatar_path = 'accounts_system/assets/uploads/avatars/' . $avatar_filename;
                                                }
                                            }
                                            echo '<div class="mb-2 d-flex flex-start align-items-center bg-light rounded">';
                                            echo '    <a href="post.php?name=' . urlencode($row['slug']) . '" class="ms-1">' . $image . '</a>';
                                            echo '    <div class="mt-2 mb-2 ms-1 me-1">';
                                            echo '        <h6 class="text-primary mb-1"><a href="post.php?name=' . urlencode($row['slug']) . '">' . htmlspecialchars($row['title']) . '</a></h6>';
                                            echo '        <p class="text-muted small mb-0">';
                                            echo '            <i class="fas fa-calendar"></i> ' . date($settings['date_format'] . ' g:i a', strtotime($row['date'])) . '<br />';
                                            echo '            <i class="fa fa-comments"></i> Comments: <a href="post.php?name=' . urlencode($row['slug']) . '#comments"><b>' . htmlspecialchars(post_commentscount($row['id'])) . '</b></a><br />';
                                            echo '            <img src="' . htmlspecialchars($author_avatar_path) . '" alt="' . $author_username . '" class="rounded-circle me-1" width="24" height="24" style="object-fit:cover;vertical-align:middle;"> Author: <b>' . $author_username . '</b>';
                                            echo '        </p>';
                                            echo '    </div>';
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3"> 
                        <div class="card-header">
                            <i class="fas fa-star"></i> Popular Posts
                        </div>
                        <div class="card-body">
                            <?php
                            $stmt = $pdo->query("SELECT bp.*, (SELECT COUNT(*) FROM blog_comments bc WHERE bc.post_id = bp.id AND bc.approved = 'Yes') AS comments_count FROM blog_posts bp WHERE bp.active='Yes' HAVING comments_count > 0 ORDER BY comments_count DESC, bp.id DESC LIMIT 5");
                            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (!$posts)
                            {
                                echo '<div class="alert alert-info">There are no popular posts with comments</div>';
                            } else
                            {
                                foreach ($posts as $row)
                                {
                                    $image = "";
                                    if ($row['image'] != "")
                                    {
                                        $img_path = $row['image'];
                                        if (!preg_match('/^(https?:\/\/|\/)/', $img_path))
                                        {
                                            $img_path = 'admin/blog/blog_post_images/' . ltrim($img_path, '/');
                                        }
                                        $image = '<img class="rounded shadow-1-strong me-1" src="' . htmlspecialchars($img_path) . '" alt="' . htmlspecialchars($row['title']) . '" width="70" height="70" />';
                                    } else
                                    {
                                        $image = '<svg class="bd-placeholder-img rounded shadow-1-strong me-1" width="70" height="70" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="No Image" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Image</title><rect width="70" height="70" fill="#55595c"/><text x="0%" y="50%" fill="#eceeef" dy=".1em">No Image</text></svg>';
                                    }
                                    // Author avatar and username logic
                                    $author_username = (isset($row['author_id']) ? htmlspecialchars(post_author($row['author_id'])) : '-');
                                    $author_avatar_path = 'accounts_system/assets/uploads/avatars/default-guest.svg';
                                    if (!empty($row['author_id'])) {
                                        $stmt_author = $pdo->prepare('SELECT avatar FROM accounts WHERE id = ? LIMIT 1');
                                        $stmt_author->execute([$row['author_id']]);
                                        $author = $stmt_author->fetch(PDO::FETCH_ASSOC);
                                        $avatar_filename = !empty($author['avatar']) ? basename($author['avatar']) : '';
                                        if ($avatar_filename && file_exists('accounts_system/assets/uploads/avatars/' . $avatar_filename)) {
                                            $author_avatar_path = 'accounts_system/assets/uploads/avatars/' . $avatar_filename;
                                        }
                                    }
                                    echo '<div class="mb-2 d-flex flex-start align-items-center bg-light rounded">';
                                    echo '    <a href="post.php?name=' . urlencode($row['slug']) . '" class="ms-1">' . $image . '</a>';
                                    echo '    <div class="mt-2 mb-2 ms-1 me-1">';
                                    echo '        <h6 class="text-primary mb-1"><a href="post.php?name=' . urlencode($row['slug']) . '">' . htmlspecialchars($row['title']) . '</a></h6>';
                                    echo '        <p class="text-muted small mb-0">';
                                    echo '            <i class="fas fa-calendar"></i> ' . date($settings['date_format'] . ' g:i a', strtotime($row['date'])) . '<br />';
                                    echo '            <i class="fa fa-comments"></i> Comments: <a href="post.php?name=' . urlencode($row['slug']) . '#comments"><b>' . htmlspecialchars(post_commentscount($row['id'])) . '</b></a><br />';
                                    echo '            <img src="' . htmlspecialchars($author_avatar_path) . '" alt="' . $author_username . '" class="rounded-circle me-1" width="24" height="24" style="object-fit:cover;vertical-align:middle;"> Author: <b>' . $author_username . '</b>';
                                    echo '        </p>';
                                    echo '    </div>';
                                    echo '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <i class="fas fa-tags"></i> Tags
                        </div>
                        <div class="card-body">
                            <?php
                            $stmt = $pdo->query("SELECT * FROM blog_tags ORDER BY tag ASC");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
                            {
                                echo '<a href="search.php?q=' . urlencode($row['tag']) . '" class="badge bg-light text-dark text-decoration-none rounded-pill">' . htmlspecialchars($row['tag']) . '</a> ';
                            }
                            ?>
                        </div>
                    </div>

                    <div class="p-4 mt-3 bg-body-tertiary rounded text-dark">
                        <h6><i class="fas fa-envelope-open-text"></i> Subscribe</h6>
                        <hr />
                        <p class="mb-3">Get the latest news and exclusive offers</p>
                        <form action="" method="POST">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="E-Mail Address" name="email" required />
                                <span class="input-group-btn">
                                <button class="btn" type="submit" name="subscribe" style="background: var(--brand-primary, #593196); color: #fff; border: none;">Subscribe</button>
                                </span>
                            </div>
                        </form>
                        <?php
                        if (isset($_POST['subscribe']))
                        {
                            $email = $_POST['email'];
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                            {
                                echo '<div class="alert alert-danger">The entered E-Mail Address is invalid</div>';
                            } else
                            {
                                $stmt = $pdo->prepare("SELECT COUNT(id) FROM blog_newsletter WHERE email = ?");
                                $stmt->execute([$email]);
                                $validator = $stmt->fetchColumn();
                                if ($validator > 0)
                                {
                                    echo '<div class="alert alert-warning">This E-Mail Address is already subscribed.</div>';
                                } else
                                {
                                    $stmt = $pdo->prepare("INSERT INTO blog_newsletter (email) VALUES (?)");
                                    $stmt->execute([$email]);
                                    echo '<div class="alert alert-success">You have successfully subscribed to our newsletter.</div>';
                                }
                            }
                        }
                        ?>
                    </div>

                    <?php
                    $stmt = $pdo->query("SELECT * FROM blog_widgets WHERE position = 'sidebar' ORDER BY id ASC");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
                    {
                        echo '	
                <div class="card mt-3">
                      <div class="card-header">' . $row['title'] . '</div>
                      <div class="card-body">
                        ' . html_entity_decode($row['content']) . '
                      </div>
                </div>
';
                    }
                    ?>
                </div>

                <?php
            }//end function sidebar...
        } 
        ?>
<?php
if (!function_exists('post_category'))
{
    function post_category($category_id)
    {
        global $pdo;
        if (!isset($pdo) || !$pdo) {
            echo '<div class="alert alert-danger">Database connection not available.</div>';
            return '-';
        }
        $category = '-';
        $stmt = $pdo->prepare("SELECT category FROM blog_categories WHERE id = ? LIMIT 1");
        $stmt->execute([$category_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['category']))
        {
            $category = $row['category'];
        }
        return $category;
    }
}//function exists
if (!function_exists('post_slug'))
{
    function post_slug($post_id)
    {
        global $pdo;
        if (!isset($pdo) || !$pdo) {
            echo '<div class="alert alert-danger">Database connection not available.</div>';
            return '';
        }
        $stmt = $pdo->prepare("SELECT slug FROM blog_posts WHERE id = ? LIMIT 1");
        $stmt->execute([$post_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['slug'] : '';
    }
}//function exists
if (!function_exists('post_commentscount'))
{
    function post_commentscount($post_id)
    {
        global $pdo;
        if (!isset($pdo) || !$pdo) {
            echo '<div class="alert alert-danger">Database connection not available.</div>';
            return 0;
        }
        $comments_count = 0;
        $stmt = $pdo->prepare("SELECT COUNT(id) AS cnt FROM blog_comments WHERE post_id = ?");
        $stmt->execute([$post_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row['cnt']))
        {
            $comments_count = (int) $row['cnt'];
        }
        return $comments_count;
    }
}//function exists
if (!function_exists('post_author'))
{
    function post_author($author_id)
    {
        global $pdo;
        if (!isset($pdo) || !$pdo) {
            echo '<div class="alert alert-danger">Database connection not available.</div>';
            return '-';
        }
        $author = '-';
        $stmt = $pdo->prepare("SELECT username FROM accounts WHERE id = ? LIMIT 1");
        $stmt->execute([$author_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['username']))
        {
            $author = $row['username'];
        }
        return $author;
    }
}//function exists
if (!function_exists('post_categoryslug'))
{
    function post_categoryslug($category_id)
    {
        global $pdo;
        if (!isset($pdo) || !$pdo) {
            echo '<div class="alert alert-danger">Database connection not available.</div>';
            return '';
        }
        $stmt = $pdo->prepare("SELECT slug FROM blog_categories WHERE id = ? LIMIT 1");
        $stmt->execute([$category_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['slug'] : '';
    }
}//function exists

if (!function_exists('get_user_avatar_info'))
{
    function get_user_avatar_info(string $username, string $account_id, string $guest = 'No'): array
    {
        global $pdo;
        // Only use accounts table for registered users; no more users table
        $default_avatar = 'accounts_system/assets/uploads/avatars/avatar.png';
        $result = [
            'avatar' => $default_avatar,
            'author' => $username,
            'badge' => '<span class="badge bg-secondary">Guest</span>',
        ];

        if (strtolower($guest) === 'yes')
        {
            return $result;
        }

        // If account_id is present, fetch from accounts
        if (!empty($account_id) && $account_id != 0)
        {
            $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ? LIMIT 1');
            $stmt->execute([$account_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user)
            {
                $result['avatar'] = !empty($user['avatar']) ? $user['avatar'] : $default_avatar;
                $result['author'] = htmlspecialchars($user['username']);
                $result['badge'] = '<span class="badge bg-primary">' . htmlspecialchars($user['role']) . '</span>';
            }
        }
        return $result;
    }
}//function exists

// Global site settings - only output if settings exist and key is not empty
if (isset($settings) && is_array($settings) && !empty($settings['head_customcode'])) {
    echo base64_decode($settings['head_customcode']);
}

// Gallery-specific sidebar for gallery.php and related pages
if (!function_exists('gallery_sidebar')) {
    // Render gallery sidebar cards for use in gallery.php
    // Place this inside a <div class="col-md-4"> in your gallery.php, beside the main content column.
    function gallery_sidebar() {
        global $pdo;
        echo '<form method="GET" action="gallery.php">';
        // Filters Card (Categories & Tags)
        echo '<div class="card mb-3">';
        echo '<div class="card-header"><i class="fas fa-filter"></i> Filters</div>';
        echo '<div class="card-body">';
        echo '<div class="row">';
        // Categories column
        echo '<div class="col-6">';
        echo '<h6 class="mb-2"><i class="fas fa-images"></i> Categories</h6>';
        $stmt = $pdo->query("SELECT * FROM blog_gallery_categories ORDER BY name ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($categories) {
            foreach ($categories as $cat) {
                $cat_id = $cat['id'];
                $stmt_count = $pdo->prepare("SELECT COUNT(id) FROM blog_gallery WHERE category_id = ? AND active = 'Yes'");
                $stmt_count->execute([$cat_id]);
                $img_count = $stmt_count->fetchColumn();
                echo '<div class="d-flex align-items-center mb-2">';
                // Checkbox
                echo '<input class="form-check-input custom-gallery-checkbox me-2" type="checkbox" name="categories[]" value="' . htmlspecialchars($cat['slug']) . '" id="cat_' . $cat['id'] . '"';
                if (isset($_GET['categories']) && in_array($cat['slug'], (array)$_GET['categories'])) echo ' checked';
                echo ' />';
                // Name
                echo '<label class="form-check-label flex-grow-1 text-dark" for="cat_' . $cat['id'] . '" style="font-weight:500;">' . htmlspecialchars($cat['name']) . '</label>';
                // Removed badge for image count
                echo '</div>';
            }
        } else {
            echo '<span class="text-muted">No categories found.</span>';
        }
        echo '</div>';
        // Tags column
        echo '<div class="col-6">';
        echo '<h6 class="mb-2"><i class="fas fa-tags"></i> Tags</h6>';
        $stmt = $pdo->query("SELECT * FROM blog_gallery_tags ORDER BY name ASC");
        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($tags) {
            foreach ($tags as $tag) {
                echo '<div class="d-flex align-items-center mb-2">';
                // Checkbox
                echo '<input class="form-check-input custom-gallery-checkbox me-2" type="checkbox" name="tags[]" value="' . htmlspecialchars($tag['slug']) . '" id="tag_' . $tag['id'] . '"';
                if (isset($_GET['tags']) && in_array($tag['slug'], (array)$_GET['tags'])) echo ' checked';
                echo ' />';
                // Name
                echo '<label class="form-check-label flex-grow-1 text-dark" for="tag_' . $tag['id'] . '" style="font-weight:500;">' . htmlspecialchars($tag['name']) . '</label>';
                // Badge (optional, for tag usage count)
                // If you want tag usage count, add logic here
                echo '</div>';
            }
        } else {
            echo '<span class="text-muted">No tags found.</span>';
        }
        echo '</div>';
        echo '</div>'; // row
        echo '</div>'; // card-body
        // Apply Filters button
        echo '<div class="card-footer bg-white border-0">';
        echo '<button type="submit" class="btn w-100 py-2" style="background: var(--brand-secondary, #4a278a); color: #fff; border: none;"><i class="fa fa-filter"></i> Apply Filters</button>';
        echo '</div>';
        echo '</div>';
        echo '</form>';
        // Custom CSS for gallery sidebar checkboxes
        echo '<style>
        .custom-gallery-checkbox {
            accent-color: var(--brand-secondary, #4a278a);
            width: 1.3em;
            height: 1.3em;
            min-width: 1.3em;
            min-height: 1.3em;
            border: 2px solid #4a278a !important;
            background: #fff !important;
            box-shadow: 0 0 2px #4a278a33;
        }
        .custom-gallery-checkbox:checked {
            background-color: var(--brand-secondary, #4a278a) !important;
            border-color: var(--brand-secondary, #4a278a) !important;
        }
        .custom-gallery-checkbox:checked {
            background-color: var(--brand-secondary, #4a278a) !important;
            border-color: var(--brand-secondary, #4a278a) !important;
            position: relative;
        }
        .custom-gallery-checkbox:checked::after {
            content: "";
            position: absolute;
            left: 50%;
            top: 50%;
            width: 0.5em;
            height: 0.9em;
            border: solid #fff;
            border-width: 0 0.2em 0.2em 0;
            transform: translate(-50%, -50%) rotate(45deg);
            z-index: 2;
            pointer-events: none;
        }
        .custom-gallery-checkbox:focus {
            outline: 2px solid var(--brand-secondary, #4a278a);
            outline-offset: 2px;
        }
        .form-check-label {
            color: #222 !important;
            font-weight: normal !important;
        }
        .badge.bg-secondary {
            background-color: #4a278a !important;
            color: #fff !important;
            font-size: 1em;
            min-width: 2em;
            text-align: center;
        }
        </style>';
    }
}

// IMPORTANT: This section contains HTML output that should only execute 
// when this file is included in the proper context with all variables available.
// Safety check to prevent execution if required variables are not available

if (!defined('BLOG_FUNCTIONS_SAFE_TO_OUTPUT')) {
    // Only proceed if we're in a safe context and have the required variables
    global $pdo, $settings, $logged_in, $rowusers;
    
    // Check if we have minimum required variables before outputting HTML
    if (!isset($pdo) || !$pdo || !isset($settings) || !is_array($settings)) {
        // Variables not ready yet - this file was included too early
        // Return silently to prevent errors
        return;
    }
}

    // Use $logged_in from header.php/blog_load.php, fallback to $_SESSION if not set
    // Use $logged_in from header.php/blog_load.php, fallback to $_SESSION if not set
    if (!isset($logged)) {
        $logged = (isset($logged_in) && $logged_in) ? 'Yes' : ((isset($_SESSION['loggedin']) && $_SESSION['loggedin']) ? 'Yes' : 'No');
    }
    
    // Only proceed if we have the necessary variables
    if (isset($logged) && isset($rowusers) && $logged == 'Yes' && $rowusers && ($rowusers['role'] == 'Admin' || $rowusers['role'] == 'Editor'))
    {
        ?> 
        <div class="nav-scroller bg-dark shadow-sm"> 
            <nav class="nav" aria-label="Secondary navigation"> 
                <?php
                if ($rowusers['role'] == 'Admin')
                {
                    ?>
                    <a class="nav-link text-white" href="admin/blog/blog_dash.php">ADMIN MENU</a>
                    <?php
                } else
                {
                    ?>
                    <a class="nav-link text-white" href="admin/blog/blog_dash.php">EDITOR MENU</a>
                    <?php
                }
                ?>
                <a class="nav-link text-secondary" href="admin/blog/blog_dash.php">
                    <i class="fas fa-columns"></i> Blog Dashboard
                </a>
                <a class="nav-link text-secondary" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-tasks"></i> Manage
                </a>
                <ul class="dropdown-menu bg-dark">

                    <?php
                    if ($rowusers['role'] == 'Admin')
                    {
                        ?>
                        <li>
                            <a class="dropdown-item text-white" href="admin/blog/settings.php">
                                Site Settings
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-white" href="admin/blog/menu_editor.php">
                                Menu
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-white" href="admin/blog/widgets.php">
                                Widgets
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item text-white" href="admin/blog/newsletter.php">
                                Newsletter
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                    <li>
                        <a class="dropdown-item text-white" href="admin/blog/file.php">
                            Files
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item text-white" href="admin/blog/posts.php">
                            Posts
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item text-white" href="admin/blog/gallery.php">
                            Gallery
                        </a>
                    </li>
                    <?php
                    if ($rowusers['role'] == 'Admin')
                    {
                        ?>
                        <li>
                            <a class="dropdown-item text-white" href="admin/blog/pages.php">
                                Pages
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <?php
                if ($rowusers['role'] == 'Admin')
                {
                    $stmt = $pdo->query("SELECT COUNT(id) FROM blog_messages WHERE viewed = 'No'");
                    $unread_messages = $stmt->fetchColumn();
                    ?>

                    <a class="nav-link text-secondary" href="admin/blog/messages.php">
                        <i class="fas fa-envelope"></i> Messages
                        <span class="badge text-bg-light rounded-pill align-text-bottom">
                            <?php
                            echo $unread_messages;
                            ?>
                        </span>
                    </a>
                    <a class="nav-link text-secondary" href="admin/blog/comments.php">
                        <i class="fas fa-comments"></i> Comments
                    </a>
                    <?php
                }
                ?>
                <a class="nav-link text-secondary" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="far fa-plus-square"></i> New
                </a>
                <ul class="dropdown-menu bg-dark">
                    <li>
                        <a class="dropdown-item text-white" href=admin/blog/add_post.php">
                            Add Post
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item text-white" href="admin/blog/add_image.php">
                            Add Image
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item text-white" href="admin/blog/upload_file.php">
                            Upload File
                        </a>
                    </li>
                    <?php
                    if ($rowusers['role'] == 'Admin')
                    {
                        ?>
                        <li>
                            <a class="dropdown-item text-white" href="admin/blog/add_page.php">
                                Add Page
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </nav>
        </div>
        <?php
    }
    ?>
  
    <!-- Blog Navigation with Shop-Style Rounded Edges -->
    <div class="container mt-3 mb-3">
        <nav class="navbar nav-underline navbar-expand-lg py-2" style="background: var(--brand-primary, #593196) !important; border-radius: 8px;">
            <div class="<?php
            global $settings;
            if (isset($settings) && is_array($settings) && isset($settings['layout']) && $settings['layout'] == 'Wide')
            {
                echo 'container-fluid';
            } else
            {
                echo 'container';
            }
            ?>">
   
            <button class="navbar-toggler mx-auto" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span> Navigation
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto">
                    <?php
                    // Blog menu rendering (update for dropdown submenus)
                    global $pdo;
                    if (!isset($current_page)) {
                        $current_page = basename($_SERVER['PHP_SELF']);
                    }
                    
                    // Check if $pdo exists and blog_menu table exists before querying
                    if (isset($pdo) && $pdo !== null) {
                        try {
                            $stmt_menu = $pdo->query("SELECT * FROM blog_menu");
                            while ($row = $stmt_menu->fetch(PDO::FETCH_ASSOC)) {
                        if ($row['path'] == 'blog') {
                            echo '<li class="nav-item link-body-emphasis dropdown">';
                            echo '<a href="blog.php" class="nav-link link-dark dropdown-toggle px-2' .
                                (($current_page == 'blog.php' || $current_page == 'category.php') ? ' active' : '') . '" data-bs-toggle="dropdown">';
                            echo '<i class="fa ' . $row['fa_icon'] . '"></i> ' . $row['page'] . ' <span class="caret"></span></a>';
                            echo '<ul class="dropdown-menu">';
                            echo '<li><a class="dropdown-item" href="blog.php">View all posts</a></li>';
                            // Categories submenu
                            echo '<li class="dropdown-submenu">';
                            echo '<a class="dropdown-item dropdown-toggle" href="#">Categories <i class="fas fa-chevron-right"></i></a>';
                            echo '<ul class="dropdown-menu">';
                            $stmt_cat = $pdo->query("SELECT * FROM blog_categories ORDER BY category ASC");
                            while ($row2 = $stmt_cat->fetch(PDO::FETCH_ASSOC)) {
                                echo '<li><a class="dropdown-item" href="category.php?name=' . $row2['slug'] . '">' . $row2['category'] . '</a></li>';
                            }
                            echo '</ul></li>';
                            // Tags submenu
                            echo '<li class="dropdown-submenu">';
                            echo '<a class="dropdown-item dropdown-toggle" href="#">Tags <i class="fas fa-chevron-right"></i></a>';
                            echo '<ul class="dropdown-menu">';
                            $stmt_tag = $pdo->query("SELECT * FROM blog_tags ORDER BY tag ASC");
                            while ($row3 = $stmt_tag->fetch(PDO::FETCH_ASSOC)) {
                                echo '<li><a class="dropdown-item" href="search.php?q=' . urlencode($row3['tag']) . '">' . htmlspecialchars($row3['tag']) . '</a></li>';
                            }
                            echo '</ul></li>';
                            echo '</ul></li>';
                        } elseif ($row['path'] == 'gallery') {
                            echo '<li class="nav-item link-body-emphasis dropdown">';
                            echo '<a href="gallery.php" class="nav-link link-dark dropdown-toggle px-2' .
                                (($current_page == 'gallery.php') ? ' active' : '') . '" data-bs-toggle="dropdown">';
                            echo '<i class="fa ' . $row['fa_icon'] . '"></i> ' . $row['page'] . ' <span class="caret"></span></a>';
                            echo '<ul class="dropdown-menu">';
                            echo '<li><a class="dropdown-item" href="gallery.php">View all images</a></li>';
                            // Gallery Categories submenu
                            echo '<li class="dropdown-submenu">';
                            echo '<a class="dropdown-item dropdown-toggle" href="#">Categories <i class="fas fa-chevron-right"></i></a>';
                            echo '<ul class="dropdown-menu">';
                            $stmt_cat = $pdo->query("SELECT * FROM blog_gallery_categories ORDER BY name ASC");
                            while ($row2 = $stmt_cat->fetch(PDO::FETCH_ASSOC)) {
                                // Use categories[] for filter form compatibility, lowercase for case-insensitive filtering
                                echo '<li><a class="dropdown-item" href="gallery.php?categories[]=' . urlencode(strtolower($row2['slug'])) . '">' . htmlspecialchars($row2['name']) . '</a></li>';
                            }
                            echo '</ul></li>';
                            // Gallery Tags submenu
                            echo '<li class="dropdown-submenu">';
                            echo '<a class="dropdown-item dropdown-toggle" href="#">Tags <i class="fas fa-chevron-right"></i></a>';
                            echo '<ul class="dropdown-menu">';
                            $stmt_tag = $pdo->query("SELECT * FROM blog_gallery_tags ORDER BY name ASC");
                            while ($row3 = $stmt_tag->fetch(PDO::FETCH_ASSOC)) {
                                // Use tags[] for filter form compatibility, lowercase for case-insensitive filtering
                                echo '<li><a class="dropdown-item" href="gallery.php?tags[]=' . urlencode(strtolower($row3['slug'])) . '">' . htmlspecialchars($row3['name']) . '</a></li>';
                            }
                            echo '</ul></li>';
                            echo '</ul></li>';
                            // Reminder: In gallery.php, use LOWER() in SQL queries for case-insensitive matching, e.g. WHERE LOWER(category_slug) = LOWER(?)
                        } else {
                            echo '<li class="nav-item link-body-emphasis">';
                            echo '<a href="' . $row['path'] . '.php" class="nav-link link-dark px-2' .
                                (($current_page != 'page.php' && $current_page == $row['path'] . '.php') ? ' active' : '') . '">';
                            echo '<i class="fa ' . $row['fa_icon'] . '"></i> ' . $row['page'] . '</a></li>';
                        }
                    }
                        } catch (PDOException $e) {
                            // If blog_menu table doesn't exist or there's a database error, show basic navigation
                            echo '<li class="nav-item"><a href="blog.php" class="nav-link">Blog</a></li>';
                            echo '<li class="nav-item"><a href="gallery.php" class="nav-link">Gallery</a></li>';
                        }
                    } else {
                        // If no PDO connection, show basic navigation
                        echo '<li class="nav-item"><a href="blog.php" class="nav-link">Blog</a></li>';
                        echo '<li class="nav-item"><a href="gallery.php" class="nav-link">Gallery</a></li>';
                    }
                    ?>
                </ul>
                <ul class="navbar-nav d-flex">
     <?php
                    if ($logged != 'No')
                    {
                        ?>
                        
                        <li class="nav-item dropdown"> 
                            <?php
                            // Get avatar for logged-in user
                            $avatar_src = 'accounts_system/assets/uploads/avatars/default-guest.svg';
                            $avatar_alt = 'Profile';
                            if (isset($rowusers) && !empty($rowusers['id']) && isset($pdo) && $pdo !== null) {
                                try {
                                    $stmt_avatar = $pdo->prepare('SELECT avatar FROM accounts WHERE id = ? LIMIT 1');
                                    $stmt_avatar->execute([$rowusers['id']]);
                                    $user_avatar = $stmt_avatar->fetch(PDO::FETCH_ASSOC);
                                    if ($user_avatar && !empty($user_avatar['avatar']) && file_exists('accounts_system/assets/uploads/avatars/' . $user_avatar['avatar'])) {
                                        $avatar_src = 'accounts_system/assets/uploads/avatars/' . $user_avatar['avatar'];
                                    }
                                } catch (PDOException $e) {
                                    // Use default avatar if database error
                                }
                                $avatar_alt = isset($rowusers['username']) ? htmlspecialchars($rowusers['username']) : 'Profile';
                            }
                            ?>
                            <a href="#" class="nav-link link-dark dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                                <img src="<?php echo htmlspecialchars($avatar_src); ?>" alt="<?php echo $avatar_alt; ?>" class="rounded-circle me-2" width="28" height="28" style="object-fit:cover;vertical-align:middle;" />
                                <?php echo isset($rowusers['username']) ? htmlspecialchars($rowusers['username']) : 'Profile'; ?> <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu"> 
                                <?php
                                // Show My Portal link only if logged in and NOT Blog_User
                                if (isset($rowusers['role']) && $rowusers['role'] !== 'Blog_User') {
                                ?>
                                <li>
                                    <a class="dropdown-item<?php if ($current_page == 'client_portal/index.php') echo ' active'; ?>" href="../client_portal/">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M411.5 208.8C418 214.2 424 220.1 429.5 226.5C438.2 236.7 453.2 240.5 464.6 233.4L518.7 199.6C529.9 192.6 533.4 177.9 525.6 167.4C510.3 146.9 492.1 128.8 471.5 113.6C461.1 106 446.8 109.3 439.7 120L404.6 172.6C396.8 184.2 400.8 199.9 411.6 208.8zM391.8 105.1C400.4 92.2 394.7 74.6 379.6 71C360.5 66.4 340.5 64 320 64C299.5 64 279.6 66.4 260.4 71C245.3 74.6 239.6 92.2 248.2 105.1L288.2 165.1C293.4 172.9 302.7 176.8 312.1 176.3C317.3 176 322.7 176 327.9 176.3C337.3 176.8 346.6 172.9 351.8 165.1L391.8 105.1zM114.5 167.3C106.6 177.8 110.2 192.6 121.4 199.5L175.5 233.3C186.9 240.4 201.8 236.6 210.6 226.4C216.1 220 222.1 214.1 228.6 208.7C239.4 199.8 243.4 184.1 235.6 172.5L200.4 119.9C193.3 109.2 178.9 105.9 168.6 113.5C148 128.6 129.8 146.8 114.5 167.3zM176.5 308.4C177.3 298.5 173.2 288.5 164.7 283.3L105 246C92.1 238 75.1 243.7 71.4 258.5C66.5 278.2 63.9 298.8 63.9 320.1L63.9 344.1C63.9 357.4 74.6 368.1 87.9 368.1L151.9 368.1C165.2 368.1 175.9 357.4 175.9 344.1L175.9 320.1C175.9 316.2 176.1 312.3 176.4 308.5zM463.6 308.4C463.9 312.2 464.1 316.1 464.1 320L464.1 344C464.1 357.3 474.8 368 488.1 368L552.1 368C565.4 368 576.1 357.3 576.1 344L576.1 320C576.1 298.8 573.5 278.2 568.6 258.4C565 243.7 547.9 237.9 535 245.9L475.3 283.2C466.9 288.5 462.7 298.4 463.5 308.3zM152 416L88 416C74.7 416 64 426.7 64 440L64 552C64 565.3 74.7 576 88 576L152 576C165.3 576 176 565.3 176 552L176 440C176 426.7 165.3 416 152 416zM552 416L488 416C474.7 416 464 426.7 464 440L464 552C464 565.3 474.7 576 488 576L552 576C565.3 576 576 565.3 576 552L576 440C576 426.7 565.3 416 552 416zM344 248C344 234.7 333.3 224 320 224C306.7 224 296 234.7 296 248L296 552C296 565.3 306.7 576 320 576C333.3 576 344 565.3 344 552L344 248zM264 280C264 266.7 253.3 256 240 256C226.7 256 216 266.7 216 280L216 552C216 565.3 226.7 576 240 576C253.3 576 264 565.3 264 552L264 280zM424 280C424 266.7 413.3 256 400 256C386.7 256 376 266.7 376 280L376 552C376 565.3 386.7 576 400 576C413.3 576 424 565.3 424 552L424 280z"/></svg> My Portal
                                    </a>
                                </li>
                                <?php
                                }
                                ?>
                                <li> 
                                    <a class="dropdown-item <?php
                                    if ($current_page == 'my-comments.php')
                                    {
                                        echo ' active';
                                    }
                                    ?>" href="my-comments.php">
                                        <i class="fa fa-comments"></i> My Comments
                                    </a>
                                </li>
                                 <?php
                                // Show Settings link only for Blog_User role
                                if (isset($rowusers['role']) && $rowusers['role'] === 'Blog_User') {
                                ?>
                                <li>
                                    <a class="dropdown-item<?php if ($current_page == 'profile.php') echo ' active'; ?>" href="profile.php">
                                        <i class="fas fa-cog"></i> Settings
                                    </a>
                                </li>
                                <?php
                                }
                                ?>
                                
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a class="dropdown-item" href="logout.php">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <?php
                    }
                    ?>
                    
                    <?php
                    // Show Login/Register link only when not logged in
                    if ($logged == 'No') {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link link-light px-2" href="auth.php" style="white-space:nowrap;">
                            <i class="fas fa-sign-in-alt"></i> Login / Register
                        </a>
                    </li>
                    <?php
                    }
                    ?>
                    
                    <?php
                    // Search form moved to far right
                    ?>
                    <li class="nav-item">
                        <form class="d-flex" action="search.php" method="GET" style="min-width:220px;">
                            <div class="input-group">
                                <input type="search" class="form-control" placeholder="Search" name="q"
                                    value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" required />
                                <button class="btn btn-light search-btn" title="search" type="submit" style="display:flex;align-items:center;">
                                    <i class="fa fa-search" aria-hidden="true" style="color: var(--brand-secondary, #4a278a) !important;"></i>
                                    <span class="visually-hidden">Search</span>
                                </button>
                            </div>
                            <style>
                                .search-btn:hover, .search-btn:focus {
                                    background: var(--brand-primary, #593196) !important;
                                }
                                .search-btn:hover .fa-search, .search-btn:focus .fa-search {
                                    color: #fff !important;
                                }
                                
                                /* Blog navigation container and positioning fixes */
                                .navbar {
                                    position: relative; /* Establish positioning context */
                                }
                                
                                /* Blog navigation dropdown z-index fix */
                                .navbar .dropdown-menu {
                                    z-index: 10050 !important; /* Higher than main navigation */
                                    position: absolute !important; /* Ensure proper positioning */
                                }
                                
                                .navbar .dropdown {
                                    position: static; /* Allow dropdown to escape container constraints */
                                }
                                
                                @media (min-width: 992px) {
                                    .navbar .dropdown {
                                        position: relative; /* Normal positioning on desktop */
                                    }
                                }
                            </style>
                        </form>
                    </li>
               
                </ul>
            </div>
        </div>
    </nav>
    </div> <!-- Close container wrapper for rounded navigation -->

    <?php
    global $settings;
    if (isset($settings) && is_array($settings) && isset($settings['latestposts_bar']) && $settings['latestposts_bar'] == 'Enabled')
    {
        ?>
        <div class="pt-2 bg-light">
            <div class="<?php
            if (isset($settings) && is_array($settings) && isset($settings['layout']) && $settings['layout'] == 'Wide')
            {
                echo 'container-fluid';
            } else
            {
                echo 'container';
            }
            ?> d-flex justify-content-center">
                <div class="col-md-2">
                    <h5>
                        <span class="badge bg-danger">
                            <i class="fa fa-info-circle"></i> Latest:
                        </span>
                    </h5>
                </div>
                <div class="col-md-10">
                    <div class="marquee-wrapper" aria-label="Scrolling announcements">
                        <div class="marquee-content">
                            <?php /*    This is scrolling text. It's accessible and standards-compliant! */ ?>
                            <?php
                            global $pdo;
                            if (isset($pdo) && $pdo !== null) {
                                $stmt = $pdo->query("SELECT * FROM blog_posts WHERE active='Yes' ORDER BY date DESC, id DESC LIMIT 4");
                                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if (!$posts)
                                {
                                    echo 'There are no published posts';
                                } else
                                {
                                    foreach ($posts as $row)
                                    {
                                        echo '<a href="post.php?name=' . $row['slug'] . '">' . $row['title'] . '</a>
        &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;';
                                    }
                                }
                            } else {
                                echo 'Database connection not available';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <?php
    }
    ?>

    <div class="<?php
    if (isset($settings) && is_array($settings) && isset($settings['layout']) && $settings['layout'] == 'Wide')
    {
        echo 'container-fluid';
    } else
    {
        echo 'container';
    }
    ?> mt-3">

        <?php
        global $pdo;
        if (!isset($pdo) || !$pdo) {
            global $pdo;
        }
        if (isset($pdo) && $pdo) {
            $stmt = $pdo->query("SELECT * FROM blog_widgets WHERE position = 'header' ORDER BY id ASC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                echo '
            <div class="card mb-3">
                <div class="card-header">' . (isset($row['title']) ? $row['title'] : '') . '</div>
                <div class="card-body">
                    ' . (isset($row['content']) ? html_entity_decode($row['content']) : '') . '
                </div>
            </div>
        ';
            }
        }
        ?>

        <div class="row">
            <?php
            if (!function_exists('display_3_recent_posts')) {
                function display_3_recent_posts() {
                    global $pdo, $settings;
                    if (!isset($pdo) || !$pdo || !isset($settings) || !is_array($settings)) {
                        echo '<div class="alert alert-info">Database or settings not available</div>';
                        return;
                    }
                    $stmt = $pdo->query("SELECT * FROM blog_posts WHERE active='Yes' ORDER BY id DESC LIMIT 3");
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
                                $image = '<img class="rounded shadow-1-strong me-1" src="' . htmlspecialchars($img_path) . '" alt="' . htmlspecialchars($row['title']) . '" width="70" height="70" />';
                            } else {
                                $image = '<svg class="bd-placeholder-img rounded shadow-1-strong me-1" width="70" height="70" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="No Image" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Image</title><rect width="70" height="70" fill="#55595c"/><text x="0%" y="50%" fill="#eceeef" dy=".1em">No Image</text></svg>';
                            }
                            echo '<div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <a href="post.php?name=' . urlencode($row['slug']) . '">' . $image . '</a>
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="post.php?name=' . urlencode($row['slug']) . '" class="text-dark">' . htmlspecialchars($row['title']) . '</a>
                                        </h5>
                                        <p class="card-text text-muted small mb-0">
                                            <i class="fas fa-calendar"></i> ' . date($settings['date_format'] . ' g:i a', strtotime($row['date'])) . '<br />
                                            <i class="fa fa-comments"></i> Comments: <a href="post.php?name=' . urlencode($row['slug']) . '#comments"><b>' . htmlspecialchars(post_commentscount($row['id'])) . '</b></a>
                                            <br />
                                            <i class="fas fa-user"></i> Author: <b>' . (isset($row['author_id']) ? htmlspecialchars(post_author($row['author_id'])) : '-') . '</b>
                                        </p>
                                    </div>
                                </div>
                            </div>';
                        }
                    }
                }
            }
           // display_3_recent_posts();
            ?>
        </div>
    </div>

    <div class="<?php
    if (isset($settings) && is_array($settings) && isset($settings['layout']) && $settings['layout'] == 'Wide')
    {
        echo 'container-fluid';
    } else
    {
        echo 'container';
    }
    ?> mt-3 mb-5">
        <div class="row">
            <div class="col-md-8">
                <?php
                if (!function_exists('post_list'))
                {
                    function post_list($category_id = 0)
                    {
                        
                        global $pdo, $settings;
                        if (!isset($pdo) || !$pdo || !isset($settings) || !is_array($settings)) {
                            echo '<div class="alert alert-danger">Database or settings not available</div>';
                            return;
                        }
                        if ($category_id > 0)
                        {
                            $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE category_id = ? AND active = 'Yes' ORDER BY id DESC LIMIT 5");
                            $stmt->execute([$category_id]);
                        } else
                        {
                            $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE active = 'Yes' ORDER BY id DESC LIMIT 5");
                            $stmt->execute();
                        }
                        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (!$posts)
                        {
                            // Get the ?name= value from the URL if present
                            $category_slug = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '';
                            $category_name = '';
                            if ($category_slug) {
                                // Try to look up the actual category name from the slug
                                $stmt_catname = $pdo->prepare("SELECT category FROM blog_categories WHERE slug = ? LIMIT 1");
                                $stmt_catname->execute([$category_slug]);
                                $row_catname = $stmt_catname->fetch(PDO::FETCH_ASSOC);
                                // Debug output (force always visible)
                                echo '<div class="alert alert-warning small" style="border:2px solid red;">DEBUG: <b>Slug from URL:</b> ' . $category_slug . ' | <b>DB result:</b> ' . ($row_catname ? htmlspecialchars($row_catname['category']) : 'not found') . '</div>';
                                if ($row_catname && !empty($row_catname['category'])) {
                                    $category_name = htmlspecialchars($row_catname['category']);
                                } else {
                                    $category_name = $category_slug;
                                }
                                echo '<div class="alert alert-info">There are no published posts using "' . $category_name . '"</div>';
                            } else {
                                echo '<div class="alert alert-info">There are no published posts with that category</div>';
                            }
                        } else
                        {
                            foreach ($posts as $row)
                            {
                                $image = "";
                                if ($row['image'] != "")
                                {
                                    $img_path = $row['image'];
                                    if (!preg_match('/^(https?:\/\/|\/)/', $img_path))
                                    {
                                        $img_path = 'admin/blog/blog_post_images/' . ltrim($img_path, '/');
                                    }
                                    $image = '<img class="rounded shadow-1-strong me-1" src="' . htmlspecialchars($img_path) . '" alt="' . htmlspecialchars($row['title']) . '" width="70" height="70" />';
                                } else
                                {
                                    $image = '<svg class="bd-placeholder-img rounded shadow-1-strong me-1" width="70" height="70" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="No Image" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Image</title><rect width="70" height="70" fill="#55595c"/><text x="0%" y="50%" fill="#eceeef" dy=".1em">No Image</text></svg>';
                                }
                                // Unified avatar logic for comments in main section
                                $stmt_comments = $pdo->prepare("SELECT * FROM blog_comments WHERE post_id = ? AND approved = 'Yes' ORDER BY date DESC, id DESC LIMIT 5");
                                $stmt_comments->execute([$row['id']]);
                                $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
                                if ($comments) {
                                    foreach ($comments as $comment) {
                                        if ($comment['guest'] == 'Yes' || empty($comment['account_id'])) {
                                            $acavatar = 'accounts_system/assets/uploads/avatars/default-guest.svg';
                                            $badge = ' <span class="badge bg-secondary">Guest</span>';
                                            $acuthor = htmlspecialchars($comment['username']);
                                        } else {
                                            $stmt2 = $pdo->prepare('SELECT username, avatar FROM accounts WHERE id = ? LIMIT 1');
                                            $stmt2->execute([$comment['account_id']]);
                                            $user = $stmt2->fetch(PDO::FETCH_ASSOC);
                                            if ($user && !empty($user['avatar'])) {
                                                // Only use avatar if it looks like a valid image filename (e.g., ends with .jpg, .jpeg, .png, .gif, .svg)
                                                if (preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $user['avatar'])) {
                                                    $acavatar = 'accounts_system/assets/uploads/avatars/' . $user['avatar'];
                                                } else {
                                                    $acavatar = 'accounts_system/assets/uploads/avatars/default-guest.svg';
                                                }
                                            } else {
                                                $acavatar = 'accounts_system/assets/uploads/avatars/default-guest.svg';
                                            }
                                            $acuthor = ($user && !empty($user['username'])) ? htmlspecialchars($user['username']): 'User';
                                            $badge = '';
                                        }
                                        echo '<div class="mb-2 d-flex flex-start align-items-center bg-light rounded border">
                                            <a href="post.php?name=' . urlencode($row['slug']) . '#comments" class="ms-2">
                                                <img class="rounded-circle shadow-1-strong me-2" src="' . htmlspecialchars($acavatar) . '" alt="' . $acuthor . '" width="55" height="55" />
                                            </a>
                                            <div class="mt-1 mb-1 ms-1 me-1">
                                                <h6 class="text-primary mb-1"><a href="post.php?name=' . urlencode($row['slug']) . '#comments">' . $acuthor . $badge . '</a></h6>
                                                <p class="text-muted small mb-0">' . emoticons(htmlspecialchars($comment['comment'])) . '<br />
                                                    <i class="fas fa-calendar"></i> ' . date($settings['date_format'], strtotime($comment['date'])) . ', ' . htmlspecialchars($comment['time']) . '
                                                </p>
                                            </div>
                                        </div>';
                                    }
                                }
                            }
                        }
                    }
                } 
                ?>
                  
            </div>

            <div class="col-md-4">
                <?php
                if (!function_exists('popular_posts'))
                {
                    function popular_posts()
                    {
                        global $pdo, $settings;
                        if (!isset($pdo) || !$pdo || !isset($settings) || !is_array($settings)) {
                            echo '<div class="alert alert-info">Database or settings not available</div>';
                            return;
                        }
                        $stmt = $pdo->query("SELECT * FROM blog_posts WHERE active='Yes' ORDER BY views DESC, id DESC LIMIT 5");
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
                                    if (!preg_match('/^(https?:\/\/|\/)/', $img_path))
                                    {
                                        $img_path = 'admin/blog/blog_post_images/' . ltrim($img_path, '/');
                                    }
                                    $image = '<img class="rounded shadow-1-strong me-1" src="' . htmlspecialchars($img_path) . '" alt="' . htmlspecialchars($row['title']) . '" width="70" height="70" />';
                                } else
                                {
                                    $image = '<svg class="bd-placeholder-img rounded shadow-1-strong me-1" width="70" height="70" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="No Image" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Image</title><rect width="70" height="70" fill="#55595c"/><text x="0%" y="50%" fill="#eceeef" dy=".1em">No Image</text></svg>';
                                }
                                echo '<div class="mb-2 d-flex flex-start align-items-center bg-light rounded">';
                                echo '    <a href="post.php?name=' . urlencode($row['slug']) . '" class="ms-1">' . $image . '</a>';
                                echo '    <div class="mt-2 mb-2 ms-1 me-1">';
                                echo '        <h6 class="text-primary mb-1"><a href="post.php?name=' . urlencode($row['slug']) . '">' . htmlspecialchars($row['title']) . '</a></h6>';
                                echo '        <p class="text-muted small mb-0">';
                                echo '            <i class="fas fa-calendar"></i> ' . date($settings['date_format'], strtotime($row['date'])) . ', ' . htmlspecialchars($row['time']) . '<br />';
                                echo '            <i class="fa fa-comments"></i> Comments: <a href="post.php?name=' . urlencode($row['slug']) . '#comments"><b>' . htmlspecialchars(post_commentscount($row['id'])) . '</b></a><br />';
                                // Author avatar and username logic
                                $author_username = (isset($row['author_id']) ? htmlspecialchars(post_author($row['author_id'])) : '-');
                                $author_avatar_path = 'accounts_system/assets/uploads/avatars/default-guest.svg';
                                if (!empty($row['author_id'])) {
                                    $stmt_author = $pdo->prepare('SELECT avatar FROM accounts WHERE id = ? LIMIT 1');
                                    $stmt_author->execute([$row['author_id']]);
                                    $author = $stmt_author->fetch(PDO::FETCH_ASSOC);
                                    $avatar_filename = !empty($author['avatar']) ? basename($author['avatar']) : '';
                                    if ($avatar_filename && file_exists('accounts_system/assets/uploads/avatars/' . $avatar_filename)) {
                                        $author_avatar_path = 'accounts_system/assets/uploads/avatars/' . $avatar_filename;
                                    }
                                }
                                echo '            <img src="' . htmlspecialchars($author_avatar_path) . '" alt="' . $author_username . '" class="rounded-circle me-1" width="24" height="24" style="object-fit:cover;vertical-align:middle;"> Author: <b>' . $author_username . '</b>';
                                echo '        </p>';
                                echo '    </div>';
                                echo '</div>';
                            }
                        }
                    }
                }
                ?>
    
            </div>
        </div>
    </div>


