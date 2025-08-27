<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOCATION: public_html/admin/blog/add_post.php
 * LOG: Add new blog posts with image upload and email notifications
 * PRODUCTION: [To be updated on deployment]
 */
 include "header.php";

// Require blog settings (defines $settings, $site_url, $sitename)
$settings_file = __DIR__ . '/../../assets/includes/settings/blog_settings.php';
if (file_exists($settings_file)) {
    include $settings_file;
    // Set site_url from settings
    if (isset($settings['blog_site_url'])) {
        $site_url = $settings['blog_site_url'];
    } else {
        $site_url = isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] : '';
    }
    // Set sitename from settings
    if (isset($settings['sitename'])) {
        $sitename = $settings['sitename'];
    } else {
        $sitename = 'Website';
    }
    // Set from email for newsletter
    if (isset($settings['email'])) {
        $from = $settings['email'];
    } else {
        $from = 'no-reply@' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
    }
} else {
    echo '<div class="alert alert-danger">Critical error: Blog settings file not found. Please contact the administrator.</div>';
    exit;
}

// =====================
// Tag Handling Function
// =====================
if (!function_exists('handleTags')) {
    function handleTags($pdo, $tags, $post_id) {
        if (empty($tags)) return;
        $tagArr = array_map('trim', explode(',', $tags));
        foreach ($tagArr as $tag) {
            if ($tag === '') continue;
            // Insert tag if not exists
            $stmt = $pdo->prepare("SELECT id FROM blog_tags WHERE tag = ?");
            $stmt->execute([$tag]);
            $tag_row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($tag_row) {
                $tag_id = $tag_row['id'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO blog_tags (tag) VALUES (?)");
                $stmt->execute([$tag]);
                $tag_id = $pdo->lastInsertId();
            }
            // Link tag to post
            $stmt = $pdo->prepare("INSERT IGNORE INTO blog_post_tags (post_id, tag_id) VALUES (?, ?)");
            $stmt->execute([$post_id, $tag_id]);
        }
    }
}
// =====================
// End Tag Handling Function

// =====================
// Image Upload Function
// =====================
 
if (!function_exists('handleImageUpload')) {
    function handleImageUpload($file, $rename_image = '') {
        $target_dir = "blog_post_images/";
        $image_result = ['image' => '', 'error' => ''];
        $valid_exts = ['jpg','jpeg','png','gif','webp'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $image_result['error'] = 'Image upload failed. Please try again.';
            return $image_result;
        }
        $original_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $ext = $original_ext;
        if (!in_array($ext, $valid_exts)) {
            $ext = 'jpg';
        }
        if ($rename_image !== '') {
            $base_filename = preg_replace('/[^A-Za-z0-9_.-]/', '', pathinfo($rename_image, PATHINFO_FILENAME));
        } else {
            $base_filename = preg_replace('/[^A-Za-z0-9_.-]/', '', pathinfo($file['name'], PATHINFO_FILENAME));
        }
        $new_filename = $base_filename . '.' . $ext;
        $target_file = $target_dir . $new_filename;
        if (!is_dir(__DIR__ . '/' . $target_dir)) {
            mkdir(__DIR__ . '/' . $target_dir, 0777, true);
        }
        $counter = 1;
        while (file_exists(__DIR__ . '/' . $target_file)) {
            $new_filename = $base_filename . "(" . $counter . ")." . $ext;
            $target_file = $target_dir . $new_filename;
            $counter++;
        }
        if (move_uploaded_file($file['tmp_name'], __DIR__ . '/' . $target_file)) {
            // Resize and pad image to 1200x630px (no crop)
            $target_w = 1200;
            $target_h = 630;
            list($width, $height) = @getimagesize(__DIR__ . '/' . $target_file);
            $ratio = min($target_w / $width, $target_h / $height);
            $new_w = (int)($width * $ratio);
            $new_h = (int)($height * $ratio);
            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                    $src_img = @imagecreatefromjpeg(__DIR__ . '/' . $target_file);
                    break;
                case 'png':
                    $src_img = @imagecreatefrompng(__DIR__ . '/' . $target_file);
                    break;
                case 'gif':
                    $src_img = @imagecreatefromgif(__DIR__ . '/' . $target_file);
                    break;
                case 'webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $src_img = @imagecreatefromwebp(__DIR__ . '/' . $target_file);
                    } else {
                        $src_img = false;
                    }
                    break;
                default:
                    $src_img = false;
            }
            if ($src_img) {
                $dst_img = imagecreatetruecolor($target_w, $target_h);
                // Fill background
                if ($ext === 'png' || $ext === 'gif' || $ext === 'webp') {
                    imagealphablending($dst_img, false);
                    imagesavealpha($dst_img, true);
                    $transparent = imagecolorallocatealpha($dst_img, 0, 0, 0, 127);
                    imagefill($dst_img, 0, 0, $transparent);
                } else {
                    $white = imagecolorallocate($dst_img, 255, 255, 255);
                    imagefill($dst_img, 0, 0, $white);
                }
                // Center the resized image
                $dst_x = (int)(($target_w - $new_w) / 2);
                $dst_y = (int)(($target_h - $new_h) / 2);
                imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, 0, 0, $new_w, $new_h, $width, $height);
                switch ($ext) {
                    case 'jpg':
                    case 'jpeg':
                        imagejpeg($dst_img, __DIR__ . '/' . $target_file, 85);
                        break;
                    case 'png':
                        imagepng($dst_img, __DIR__ . '/' . $target_file, 6);
                        break;
                    case 'gif':
                        imagegif($dst_img, __DIR__ . '/' . $target_file);
                        break;
                    case 'webp':
                        if (function_exists('imagewebp')) {
                            imagewebp($dst_img, __DIR__ . '/' . $target_file, 80);
                        }
                        break;
                }
                imagedestroy($src_img);
                imagedestroy($dst_img);
            }
            $image_result['image'] = $new_filename; // Only file name
        } else {
            $image_result['error'] = 'Failed to move uploaded image.';
        }
        return $image_result;
    }
}
// =====================
// End Image Upload Function

// =====================
// CSRF Protection Setup
// =====================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// =====================
// End CSRF Protection Setup
// =====================

// =====================
// CSRF Protection Setup
// =====================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// =====================
// End CSRF Protection Setup
// =====================

// =====================
// POST Handler Section
// =====================
if (isset($_POST['add'])) {
    // CSRF token validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo '<div class="alert alert-danger">Invalid CSRF token. Please reload the page and try again.</div>';
        exit;
    }
    $errors = [];
    // Title
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    if ($title === '' || mb_strlen($title) > 100) {
        $errors[] = 'Title is required and must be under 100 characters.';
    }
    $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    // Slug
    $slug = generateSeoURL($title);
    // Active
    $active = isset($_POST['active']) && $_POST['active'] === 'Yes' ? 'Yes' : 'No';
    // Featured
    $featured = isset($_POST['featured']) && $_POST['featured'] === 'Yes' ? 'Yes' : 'No';
    // Category
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    if ($category_id <= 0) {
        $errors[] = 'Please select a valid category.';
    }
    // Content
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    if ($content === '') {
        $errors[] = 'Content is required.';
    }
    $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    // Author
    $author_id = isset($_POST['author_id']) ? intval($_POST['author_id']) : 0;
    if ($author_id === 0) {
        $errors[] = 'No author selected or invalid author. Please select a valid Admin author.';
    }
    // Tags
    $tags = isset($_POST['tags']) ? trim($_POST['tags']) : '';
    $tags = htmlspecialchars($tags, ENT_QUOTES, 'UTF-8');
    // Date and time
    $date = date($settings['date_format']);
    $time = date('H:i');
    // If there are errors, display them above the form and prevent further processing
    if (!empty($errors)) {
        $form_error_block = '<div class="alert alert-danger"><ul class="mb-0">';
        foreach ($errors as $err) {
            $form_error_block .= '<li>' . htmlspecialchars($err) . '</li>';
        }
        $form_error_block .= '</ul></div>';
        // Prevent further processing, but do NOT exit; let the form render below
        return;
    }
    // =====================
    // Image Upload Logic (auto-rename to avoid duplicates)
    // =====================
    $image_result = ['image' => '', 'error' => ''];
    $image_processed = false;
    if (isset($_FILES['image']) && $_FILES['image']['name'] !== '') {
        // Always use rename field if provided
        $rename_image = isset($_POST['rename_image']) ? trim($_POST['rename_image']) : '';
        $image_result = handleImageUpload($_FILES['image'], $rename_image);
        if ($image_result['error']) {
            $form_error = $image_result['error'];
        } else {
            $image_processed = true;
        }
    }
    // =====================
    // Database Operations (PDO only)
    // =====================
    // Only save image if processed successfully
    $image_to_save = ($image_processed && isset($image_result['image'])) ? $image_result['image'] : '';
    try {
        $stmt = $pdo->prepare("INSERT INTO blog_posts (title, slug, content, author_id, category_id, active, featured, image, date, time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $title,
            $slug,
            $content,
            $author_id,
            $category_id,
            $active,
            $featured,
            $image_to_save, // Only file name is saved
            $date,
            $time
        ]);
        $post_id = $pdo->lastInsertId();
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error saving post: ' . htmlspecialchars($e->getMessage()) . '</div>';
        exit;
    }
    // Tag Handling
    handleTags($pdo, $tags, $post_id);
    // Newsletter Notification
    $emailSuccess = true;
    try {
        $run2 = $pdo->prepare("SELECT * FROM blog_newsletter");
        $run2->execute();
        $newsletter_rows = $run2->fetchAll(PDO::FETCH_ASSOC);
        foreach ($newsletter_rows as $row) {
            $to = $row['email'];
            $subject = $title;
            $message = '<html><body>' .
                '<b><h1>' . $sitename . '</h1><b/>' .
                '<h2>New post: <b><a href="' . $site_url . '/post.php?id=' . $post_id . '" title="Read more">' . $title . '</a></b></h2><br />' .
                html_entity_decode($content) .
                '<hr /><i>If you do not want to receive more notifications, you can <a href="' . $site_url . '/unsubscribe?email=' . $to . '">Unsubscribe</a></i></body></html>';
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .= 'From: ' . $from . '';
            @mail($to, $subject, $message, $headers);
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-warning">Newsletter email error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    // Show success message
    if ($emailSuccess) {
        echo '<div class="alert alert-success">Blog post created and notifications sent successfully.</div>';
        // Redirect to posts.php after successful creation
        header('Location: posts.php');
        exit;
    } else {
        echo '<div class="alert alert-warning">Blog post created, but some notifications may not have been sent.</div>';
        // Redirect to posts.php even if some notifications failed
        header('Location: posts.php');
        exit;
    }
}
?>
 
<?= template_admin_header('Add Blog Post', 'blog', 'posts') ?>

<div class="content-title" id="main-blog-post-form" role="banner" aria-label="Create Blog Post Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path d="M96 96c0-35.3 28.7-64 64-64H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H80c-44.2 0-80-35.8-80-80V128c0-17.7 14.3-32 32-32s32 14.3 32 32V400c0 8.8 7.2 16 16 16s16-7.2 16-16V96zm64 24v80c0 13.3 10.7 24 24 24H296c13.3 0 24-10.7 24-24V120c0-13.3-10.7-24-24-24H184c-13.3 0-24 10.7-24 24zm208-8c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zM160 304c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16z"/>
            </svg>
        </div>
        <div class="txt">
            <h2>Create Blog Post</h2>
            <p>Create a new blog post for your website.</p>
        </div>
    </div>
</div>
<br>
<div class="d-flex gap-2 pb-3 border-bottom mb-3">
    <a href="posts.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Cancel
    </a>
</div>
     
  <form method="post" enctype="multipart/form-data" action="">
       <!-- CSRF token field -->
       <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <p>
                <label for="title">Title <span class="text-danger">*</span> <span class="text-danger">required</span></label>
                <input class="form-control" name="title" id="title" value="" type="text" oninput="countText()" required
                    aria-required="true" aria-label="Post Title">
                <i>For best SEO keep title under 50 characters.</i>
                <label for="characters">Characters: </label>
                <span id="characters">0</span><br>
            </p>
            <p>
                <label for="summernote">Blog Post Content <span class="text-danger">*</span> <span class="text-danger">required</span></label>
                <textarea class="form-control" id="summernote" rows="8" name="content" required aria-required="true"
                    aria-label="Post Content"></textarea>
            </p>

            <?php if (isset($form_error_block)) echo $form_error_block; ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tags">Tags</label>
                    <input class="form-control" name="tags" id="tags" value="" type="text"
                        placeholder="Comma-separated tags (e.g. news, update, tips)" aria-label="Tags">
                    <i>Enter tags separated by commas. New tags will be created automatically.</i>
                </div>
                <div class="col-md-6">
                    <label for="image">Image</label>
                    <input type="file" name="image" id="image" class="form-control" aria-label="Upload post image"
                        aria-describedby="image-desc" <?php if (isset($retain_temp_image) && $retain_temp_image != '') echo 'disabled'; ?> />
                    <div id="rename-image-group" style="display:none;">
                        <label for="rename_image" class="mt-2">Rename Image File <span class="text-danger">*</span> <span class="text-danger">required</span></label>
                        <?php $rename_val = isset($duplicate_image_name) ? $duplicate_image_name : ''; ?>
                        <input type="text" name="rename_image" id="rename_image" class="form-control" value="<?php echo htmlspecialchars($rename_val); ?>" placeholder="Enter a new file name (e.g. myphoto.jpg)" />
                    </div>
                    <?php
                    if (isset($form_error) && $form_error) {
                        echo '<div class="alert alert-danger">' . htmlspecialchars($form_error) . '</div>';
                    }
                    // Hidden field to retain temp image if present
                    if (isset($retain_temp_image) && $retain_temp_image != '') {
                        echo '<input type="hidden" name="temp_image" value="' . htmlspecialchars($retain_temp_image) . '" />';
                    }
                    ?>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-2">
                    <label for="active">Active</label>
                    <select name="active" id="active" class="form-select" required aria-required="true"
                        aria-label="Active Status">
                        <option value="Yes" selected>Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="featured">Featured</label>
                    <select name="featured" id="featured" class="form-select" required aria-required="true"
                        aria-label="Featured Status">
                        <option value="Yes">Yes</option>
                        <option value="No" selected>No</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id" class="form-select" required aria-required="true"
                        aria-label="Category">
<?php
$crun = $pdo->prepare("SELECT * FROM `blog_categories`");
$crun->execute();
$categories = $crun->fetchAll(PDO::FETCH_ASSOC);
if (count($categories) === 0)
{
    echo '<option value="">No categories found</option>';
} else {
    foreach ($categories as $rw)
    {
        echo '<option value="' . htmlspecialchars($rw['id']) . '">' . htmlspecialchars($rw['category']) . '</option>';
    }
}
?>
                    </select>
                </div>
 

       <div class="col-md-3">
                    <label for="author_id">Author (Admin)</label>
                    <select name="author_id" id="author_id" class="form-select" required aria-required="true" aria-label="Author">
<?php
                    $arun = $pdo->prepare("SELECT id, username FROM accounts WHERE role = 'Admin'");
$arun->execute();
$admins = $arun->fetchAll(PDO::FETCH_ASSOC);
if (count($admins) === 0) {
    echo '<option value="">No admin accounts found</option>';
} else {
    foreach ($admins as $admin) {
        echo '<option value="' . htmlspecialchars($admin['id']) . '">' . htmlspecialchars($admin['username']) . '</option>';
    }
}
?>
                    </select>
                </div>               
            </div>
            <div class="d-flex gap-2 pt-3 border-top mt-4">
                <a href="posts.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Cancel
                </a>
                <button type="submit" name="add" class="btn btn-success">
                    <i class="fas fa-save me-1"></i>Save Post
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#summernote').summernote({ height: 350 });

        // Show/hide rename image field based on image selection
        $('#image').on('change', function () {
            if (this.files && this.files.length > 0) {
                $('#rename-image-group').show();
                $('#rename_image').attr('required', true);
            } else {
                $('#rename-image-group').hide();
                $('#rename_image').removeAttr('required');
            }
        });

        // On page load, check if image is already selected (for edit/retain)
        if ($('#image').val()) {
            $('#rename-image-group').show();
            $('#rename_image').attr('required', true);
        }

        var noteBar = $('.note-toolbar');
        noteBar.find('[data-toggle]').each(function () {
            $(this).attr('data-bs-toggle', $(this).attr('data-toggle')).removeAttr('data-toggle');
        });
    });
</script>
<?= template_admin_footer(); ?>