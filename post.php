<?php
/*
PAGE NAME  : post.php
LOCATION   : public_html/post.php
DESCRIPTION: This page displays a single blog post along with its comments and related posts.
FUNCTION   : Users can view, comment on, and share individual blog posts. Admins can manage posts and comments.
CHANGE LOG : Initial creation of post.php to display a single blog post.
2025-08-24 : Added social sharing buttons for blog posts.
2025-08-25 : Improved comment system with user avatars.
2025-08-26 : Enhanced SEO features for blog posts.
*/
// Include necessary files
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
include_once "assets/includes/blog_load.php";
 
// ...existing code...
$secret = $settings['gcaptcha_secretkey'] ?? '';
$date = date($settings['date_format'] ?? 'Y-m-d');
$time = date('H:i');
$remoteIp = $_SERVER['REMOTE_ADDR'];
$gcaptcha_projectid = $settings["gcaptcha_projectid"] ?? '';
?>
<div class="container mt-3 mb-5" role="main" aria-label="Blog post content">
  <div class="row">
    <?php if (isset($settings['sidebar_position']) && $settings['sidebar_position'] === 'Left') { ?>
      <div class="col-md-4" id="sidebar-left">
        <?php sidebar(); ?>
      </div>
    <?php } ?>
    <div id='1' class="col-md-8 mb-3" role="region" aria-labelledby="post-title">
    <?php
    $slug = $_GET['name'];
    $return_error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
    if (empty($slug))
    {
        error_log('slug get name is empty');
        echo '<meta http-equiv="refresh" content="0; url=blog">';
        exit;
    }//end checking for the get name slug.
    $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE active="Yes" AND slug = ?');
    $stmt->execute([$slug]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row)
    {
        echo '<meta http-equiv="refresh" content="0; url=blog">';
        exit;
    }//end selecting the correct post to display.
    $stmt = $pdo->prepare('UPDATE blog_posts SET views = views + 1 WHERE active="Yes" AND slug = ?');
    $stmt->execute([$slug]);
    $post_id = $row['id'];
    $post_slug = $row['slug'];
    ?>
    <div id='2' class="card shadow-sm bg-light" role="article" aria-label="Blog post">
        <div id='3' class="col-md-12">

            <?php
            if ($row['image'] != '')
            {
                $img_path = $row['image'];
                // If it's a full URL, use as is. Otherwise, prepend the correct directory.
                if (preg_match('/^(https?:\/\/)/', $img_path)) {
                    // Full URL, use as is
                } else {
                    $img_path = 'admin/blog/blog_post_images/' . ltrim($img_path, '/');
                }
                echo '<div style="text-align:center">';
                echo '<img src="' . htmlspecialchars($img_path) . '" class="center" width="50%" height="auto" alt="' . htmlspecialchars($row["title"]) . '" aria-label="' . htmlspecialchars($row["title"]) . '" /></div>';
            } else {
                // Show a box with same aspect ratio as image (e.g. 16:9 or square)
                echo '<div class="rounded-start d-flex align-items-center justify-content-center" style="width:50%;height:180px;min-height:120px;background:#e9ecef;color:#888;text-align:center;font-size:1.2em;margin:auto;" role="img" aria-label="No Image">No Image</div>';
            }
            ?>

            <div id='4' class="card-body">
                <div id='5' class="mb-1">
                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                    <a
                        href="category.php?name=<?= post_categoryslug($row['category_id']) ?>"><?= post_category($row['category_id']) ?></a>
                </div><?php /* end div id 5 */ ?>
                <h5 class="card-title fw-bold" id="post-title"><?= $row['title'] ?></h5>
                <div id='posted-by-row' class="d-flex justify-content-between align-items-center" aria-label="Post meta information">
                    <small>
                        <?php
                        $author_username = '-';
                        $author_avatar_path = 'accounts_system/assets/uploads/avatars/default-guest.svg';
                        if (!empty($row['author_id'])) {
                            $stmt_author = $pdo->prepare('SELECT username, avatar FROM accounts WHERE id = ? LIMIT 1');
                            $stmt_author->execute([$row['author_id']]);
                            $author = $stmt_author->fetch(PDO::FETCH_ASSOC);
                            if ($author && !empty($author['username'])) {
                                $author_username = htmlspecialchars($author['username']);
                                $avatar_filename = !empty($author['avatar']) ? basename($author['avatar']) : '';
                                if ($avatar_filename && file_exists('accounts_system/assets/uploads/avatars/' . $avatar_filename)) {
                                    $author_avatar_path = 'accounts_system/assets/uploads/avatars/' . $avatar_filename;
                                }
                            }
                        }
                        ?>
                        Posted by <img src="<?= htmlspecialchars($author_avatar_path) ?>" alt="<?= $author_username ?>" class="rounded-circle me-1" width="24" height="24" style="object-fit:cover;vertical-align:middle;" aria-label="Author avatar"> <b><?= $author_username ?></b>
                        on <b><?= date($settings['date_format'], strtotime($row['date'])) ?>, <?= $row['time'] ?></b>
                    </small>
                    <small>
                        <i class="bi bi-eye" aria-hidden="true"></i> Views <?= $row['views'] ?>
                    </small>
                    <small class="float-end">
                        <i class="bi bi-chat-dots" aria-hidden="true"></i> Comments <a
                            href="#comments"><b><?= post_commentscount($row['id']) ?></b></a>
                    </small>
                </div><?php /* end posted-by-row' */ ?>
                <hr />

                <?= html_entity_decode($row['content']) ?>
                <hr />

                <h5><i class="bi bi-share" aria-hidden="true"></i> <span aria-label="Share this post">Share</span></h5>
                <div id="share" style="font-size: 16px;" aria-label="Social sharing options"></div><?php /* end div id share */ ?>
                <script>
                if (typeof $ !== 'undefined' && typeof $.fn.jsSocials === 'function') {
                    $(function() {
                        $("#share").jsSocials({
                            showCount: false,
                            showLabel: true,
                            shares: [
                                { share: "facebook", logo: "bi bi-facebook", label: "Share" },
                                { share: "twitter", logo: "bi bi-twitter", label: "Tweet" },
                                { share: "linkedin", logo: "bi bi-linkedin", label: "Share" },
                                { share: "email", logo: "bi bi-envelope", label: "E-Mail" }
                            ]
                        });
                    });
                }
                </script>
                <hr />

                <h5 class="mt-2" id="comments" aria-label="Comments section">
                    <i class="bi bi-chat-dots" aria-hidden="true"></i> Comments (<?= post_commentscount($row['id']) ?>)
                </h5>

                <?php
                // Fetch approved comments for this post
                $stmt = $pdo->prepare('SELECT * FROM blog_comments WHERE post_id = ? AND approved="Yes" ORDER BY id DESC');
                $stmt->execute([$row['id']]);
                $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$comments) {
                    echo "<div class='text-muted mb-3'>There are no comments</div>";
                } else {
                    foreach ($comments as $comment_row) {
                        // Get user info and avatar
                        $user_info = get_user_avatar_info($comment_row['username'], $comment_row['account_id'], $comment_row['guest']);
                        $avatar_filename = !empty($user_info['avatar']) ? basename($user_info['avatar']) : '';
                        $avatar_path = '';
                        if ($avatar_filename && file_exists('accounts_system/assets/uploads/avatars/' . $avatar_filename)) {
                            $avatar_path = 'accounts_system/assets/uploads/avatars/' . $avatar_filename;
                        } else {
                            $avatar_path = 'accounts_system/assets/uploads/avatars/default-guest.svg';
                        }
                        $author = isset($comment_row['username']) ? htmlspecialchars($comment_row['username']) : '-';
                        $badge = isset($user_info['badge']) ? $user_info['badge'] : '';
                        $date = $comment_row['date'];
                        $time = $comment_row['time'];
                        $comment_text = $comment_row['comment'];
                        echo '<div class="row d-flex justify-content-center bg-white rounded border mt-3 mb-3 ms-1 me-1" role="article" aria-label="Comment by ' . $author . '">';
                        echo '  <div class="mb-2 d-flex flex-start align-items-center">';
                        echo '    <img class="rounded-circle shadow-1-strong mt-1 me-3" src="' . htmlspecialchars($avatar_path) . '" alt="' . $author . '" width="50" height="50" aria-label="Commenter avatar" />';
                        echo '    <div class="mt-1 mb-1">';
                        echo '      <h6 class="fw-bold mt-1 mb-1">' . $author . ' ' . $badge . '</h6>';
                        echo '      <p class="small mb-0">' . date($settings['date_format'], strtotime($date)) . ', ' . $time . '</p>';
                        echo '    </div>';
                        echo '  </div>';
                        echo '  <hr class="my-0" />';
                        echo '  <p class="mt-1 mb-1 pb-1">' . emoticons($comment_text) . '</p>';
                        echo '</div>';
                    }
                }
                ?>
                <h5 class="mt-4">Leave A Comment</h5>

                <?php
                // Optional error display if redirected back
                if (isset($_GET['error']))
                {
                    echo "<p style='color:red'>{$_GET['error']}</p>";
                }
                ?>
                <?php
                // Optional error display if redirected back
                if (isset($_GET['success']))
                {
                    echo "<p style='color:green'>{$_GET['success']}</p>";
                }
                ?>

                <?php
                // Comment logic: Only use accounts table and $_SESSION['loggedin']
                $cancomment = 'No';
                $approved = 'No';
                $logged_in = false;
                $author_name = '';
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
                    $logged_in = true;
                    $author_name = $_SESSION['name'];
                    $cancomment = 'Yes';
                    $approved = 'Yes';
                } else if ($settings['comments'] === 'guests') {
                    $cancomment = 'Yes';
                    $approved = 'No';
                }

                if ($cancomment === 'Yes')
                {// Display the form, if comments are allowd for the user.
                    ?>

                    <?php
                    // Prefill the form with the logged in username, or leave blank for guests.
                    $author_name = $logged_in ? htmlspecialchars($_SESSION['name']) : "";
                    ?>
                    <form id="comment_form" name="comment_form" action="post.php?name=<?= $post_slug; ?>" method="post" aria-label="Leave a comment">
                        <label for="name">
                            <?php
                            if ($logged_in) {
                                // Get avatar for logged-in user
                                $avatar_src = 'accounts_system/assets/uploads/avatars/default-guest.svg';
                                if (isset($_SESSION['id'])) {
                                    $stmt_avatar = $pdo->prepare('SELECT avatar FROM accounts WHERE id = ? LIMIT 1');
                                    $stmt_avatar->execute([$_SESSION['id']]);
                                    $user_avatar = $stmt_avatar->fetch(PDO::FETCH_ASSOC);
                                    if ($user_avatar && !empty($user_avatar['avatar']) && file_exists('accounts_system/assets/uploads/avatars/' . $user_avatar['avatar'])) {
                                        $avatar_src = 'accounts_system/assets/uploads/avatars/' . $user_avatar['avatar'];
                                    }
                                }
                                echo '<img src="' . htmlspecialchars($avatar_src) . '" alt="Avatar" class="rounded-circle me-1" width="22" height="22" style="object-fit:cover;vertical-align:middle;" />';
                            } else {
                                echo '<img src="accounts_system/assets/uploads/avatars/default-guest.svg" alt="Guest Avatar" class="rounded-circle me-1" width="22" height="22" style="object-fit:cover;vertical-align:middle;" />';
                            }
                            ?> Author:
                        </label><br>
                        <?php if (!$logged_in): ?>
                            <input type="text" name="name" id="name" placeholder="Your Name" minlength="5" required aria-required="true" aria-label="Your Name"><br>
                        <?php else: ?>
                            <input type="text" name="name" id="name" value="<?= htmlspecialchars($author_name); ?>" readonly aria-label="Your Name"><br>
                        <?php endif; ?>
                        <label for="comment"><i class="bi bi-chat-left-text" aria-hidden="true"></i> Comment:</label><br>
                        <textarea name="comment" title='Leave a Comment' placeholder='Leave a Comment.' id="comment"
                            rows="5" class="form-control" maxlength="1000" oninput="countText()" minlength="5"
                            required aria-required="true" aria-label="Comment"></textarea><br>
                        <label for="characters" aria-label="Characters left"><i>Characters left: </i></label>
                        <span id="characters" aria-live="polite">1000</span><br><br>

                        <!-- Hidden reCAPTCHA token field -->
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                        <input type="hidden" name="cancomment" id="cancomment" value="<?= $cancomment ?>" />
                        <!-- Submit "link" styled as a button -->
                        <a href="#" id="submit-btn" class="btn btn-primary" aria-label="Submit Comment">Submit Comment</a>
                    </form>
                    <?php
                } else {
                    echo '<div class="alert alert-warning">You must be logged in to comment. <a href="auth.php"><i class="bi bi-box-arrow-in-right" aria-hidden="true"></i> Sign In</a></div>';
                }
                // End of first if ($cancomment === 'Yes') block

                // Now handle comment submission
                if ($cancomment === 'Yes' && isset($_POST['comment'])) {
                    // For comment submission, use username for logged-in user from accounts table, or guest input
                    if ($logged_in) {
                        $account_id = $_SESSION['id'];
                        $stmt_user = $pdo->prepare('SELECT username FROM accounts WHERE id = ? LIMIT 1');
                        $stmt_user->execute([$account_id]);
                        $user_row = $stmt_user->fetch(PDO::FETCH_ASSOC);
                        $author = $user_row && !empty($user_row['username']) ? $user_row['username'] : $_SESSION['username'];
                    } else {
                        $author = trim($_POST['name']);
                    }
                    $comment = trim($_POST['comment']);
                    $recaptcha_response = $_POST['g-recaptcha-response'];

                    // ==== Validate basic fields ====
                    if (strlen($author) < 5 || strlen($comment) < 5)
                    {
                        // Safely encode the query parameters
                        $slug = urlencode($row['slug']);
                        $error = 'Please use at least 5 letters.';
                        $encoded_error = urlencode($error);
                        //redirect
                        header("Location: post.php?name=$slug&error=$encoded_error#comments");
               
                    }
 
                    error_log('BEGIN CAPTCHA VERIFY WITH GOOGLE on blog/post.php');
                    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';

                    $data = [
                        'secret' => $secret,
                        'response' => $recaptcha_response,
                        'remoteip' => $_SERVER['REMOTE_ADDR']
                    ];

                    $options = [
                        'http' => [
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                   
                        ]
                    ];

                    $context = stream_context_create($options);
                    $result = file_get_contents($verify_url, false, $context);
                    $verification = json_decode($result, true);
                       
                    // ==== Check score and success ====
                    if ($verification['success'] && $verification['score'] ?? 0 >= 0.5)
                    {
                        // ✅ Good score, accept comment
                        if ($cancomment === 'Yes')
                        {
                            // Safely encode the query parameters
                            $slug = urlencode($row['slug']);
                            $success = 'Your comment has been successfully posted.';
                            $encoded_success = urlencode($success);
                            $rowid = $row['id'];
                            $comment = trim($_POST['comment']);
                             // echo "✅ Comment accepted! Author: " . htmlspecialchars($author) . "<br>Comment: " . htmlspecialchars($comment);
                            $author = $user_row && !empty($user_row['username']) ? $user_row['username'] : trim($_POST['name']); // Changed to use guest input
                            // Define $guest for comment submission
                            $guest = $logged_in ? 'No' : 'Yes';
                            $stmt = $pdo->prepare("INSERT INTO blog_comments (post_id, comment, approved, user_id, date, time, guest, ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->execute([$rowid, $comment, $approved, $author, $date, $time, $guest, $remoteIp]);
                            header("Location: post.php?name=$slug&success=$encoded_success#comments");
                            exit;
                        } else
                        {
                            // Safely encode the query parameters
                            $slug = urlencode($row['slug']);
                            $error = 'You must be logged in to comment.';
                            $encoded_error = urlencode($error);
                            //redirect
                            header("Location: post.php?name=$slug&error=$encoded_error#comments");
                            exit;
                        }
                       
                    } else {
                        // ❌ Low score or invalid
                        $slug = urlencode($row['slug']);
                        $error = 'Google cannot verify you are a human.';
                        $encoded_error = urlencode($error);
                        error_log('Low Score or Invalid Google Verification: ' . $encoded_error);
                        //redirect
                        header("Location: post.php?name=$slug&error=$encoded_error#comments");
                        exit;
                    }
                }
                // closes the processing of the comment form.
                ?>
            </div> <?php /* end div id 5 */ ?>
        </div><?php /* end div id 3. */ ?>
    </div><?php /* end div id 2. */ ?>
    </div><?php /* end div id 1 */ ?>
    <?php if (isset($settings['sidebar_position']) && $settings['sidebar_position'] === 'Right') { ?>
    <div class="col-md-4" id="sidebar-right" role="complementary" aria-label="Sidebar">
        <?php sidebar(); ?>
      </div>
    <?php } ?>
  </div>
</div>
<script>
    function countText() {
        var commentForm = document.getElementById('comment_form');
        var commentBox = document.getElementById('comment');
        var charactersSpan = document.getElementById('characters');
        if (commentForm && commentBox && charactersSpan) {
            let text = commentBox.value;
            charactersSpan.innerText = 1000 - text.length;
            charactersSpan.setAttribute('aria-live', 'polite');
        }
    }
</script>
<script>
    var submitBtn = document.getElementById('submit-btn');
    var commentForm = document.getElementById('comment_form');
    var authorInput = document.getElementById('name');
    var commentBox = document.getElementById('comment');
    if (submitBtn && commentForm && authorInput && commentBox) {
        submitBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (authorInput.value.length < 5) {
                alert("Author must be at least 5 characters.");
                submitBtn.setAttribute('aria-invalid', 'true');
                return;
            }
            if (commentBox.value.length < 5) {
                alert("Comment must be at least 5 characters.");
                submitBtn.setAttribute('aria-invalid', 'true');
                return;
            }
            submitBtn.setAttribute('aria-invalid', 'false');
            if (typeof grecaptcha !== 'undefined') {
                grecaptcha.ready(function () {
                    grecaptcha.execute('6LdmAmgrAAAAAIdsJeCLDjkPhYeVZIH6wSGqkxIH', { action: 'submit' }).then(function (token) {
                        document.getElementById('g-recaptcha-response').value = token;
                        commentForm.submit();
                    });
                });
            } else {
                commentForm.submit();
            }
        });
    }
</script>

<script>
    if (typeof $ !== 'undefined' && typeof $.fn.jsSocials === 'function') {
        $("#share").jsSocials({
            showCount: false,
            showLabel: true,
            shares: [
                { share: "facebook", logo: "bi bi-facebook", label: "Share", ariaLabel: "Share on Facebook" },
                { share: "twitter", logo: "bi bi-twitter", label: "Tweet", ariaLabel: "Share on Twitter" },
                { share: "linkedin", logo: "bi bi-linkedin", label: "Share", ariaLabel: "Share on LinkedIn" },
                { share: "email", logo: "bi bi-envelope", label: "E-Mail", ariaLabel: "Share via Email" }
            ]
        });
    }
</script>
<?php
// Removed duplicate sidebar() call at the bottom of the page
include_once __DIR__ . '/assets/includes/footer.php'; // Render the footer
?>