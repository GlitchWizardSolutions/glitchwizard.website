<?php
include '../assets/includes/main.php';
// disable the code execution time limit
set_time_limit(0);
// Disable the default upload file size limits
ini_set('post_max_size', '0');
ini_set('upload_max_filesize', '0');
// Default values
$media = [
    'title' => '',
    'description_text' => '',
    'uploaded_date' => date('Y-m-d H:i'),
    'media_type' => '',
    'thumbnail' => '',
    'filepath' => '',
    'is_approved' => 1,
    'is_public' => 1,
    'account_id' => $_SESSION['account_id']
];

// Initialize page variable based on whether we're editing or creating
$page = isset($_GET['id']) ? 'Edit' : 'Create';

// Retrieve accounts from the database
$stmt = $pdo->prepare('SELECT * FROM accounts');
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Retrieve media
if (isset($_GET['id'])) {
    // Retrieve the media from the database
    $stmt = $pdo->prepare('SELECT * FROM gallery_media WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);
}
// Handle media upload
$media_id = md5(uniqid());
if (isset($_FILES['media']) && !empty($_FILES['media']['tmp_name'])) {
    // Get mime type of the uploaded file
    $mime_type = mime_content_type($_FILES['media']['tmp_name']);
    // Media file type (image/audio/video)
    $media['media_type'] = '';
    $media['media_type'] = strpos($mime_type, 'image/') === 0 ? 'image' : $media['media_type'];
    $media['media_type'] = strpos($mime_type, 'audio/') === 0 ? 'audio' : $media['media_type'];
    $media['media_type'] = strpos($mime_type, 'video/') === 0 ? 'video' : $media['media_type'];
    // Check to make sure the media file is valid
    if (empty($media['media_type'])) {
        $error_msg = 'Unsupported media format! Please upload a valid image, audio, or video file.';
    } else {
        $media_parts = explode('.', $_FILES['media']['name']);
        $ext = end($media_parts);
        $media['filepath'] = 'media/' . $media['media_type'] . 's/' . $media_id . '.' . $ext;
        if (!move_uploaded_file($_FILES['media']['tmp_name'], '../' . $media['filepath'])) {
            $error_msg = 'Failed to upload media file! Please check file permissions and media path.';
        }
    }
}
// Handle thumbnail upload
if (!isset($error_msg) && isset($_FILES['thumbnail']) && !empty($_FILES['thumbnail']['tmp_name']) && getimagesize($_FILES['thumbnail']['tmp_name'])) {
    $media['thumbnail'] = 'media/thumbnails/' . $media_id . '.' . end(explode('.', $_FILES['thumbnail']['name']));
    if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], '../' . $media['thumbnail'])) {
        $error_msg = 'Failed to upload thumbnail! Please check file permissions and thumbnail path.';
    }
}
if (!isset($error_msg)) {
    if (isset($_GET['id'])) {
        // ID param exists, edit an existing media
        if (isset($_POST['submit'])) {
            $approved = isset($_POST['approved']) ? 1 : 0;
            $public = isset($_POST['public']) ? 1 : 0;
            // Update the media
            $stmt = $pdo->prepare('UPDATE gallery_media SET title = ?, description_text = ?, filepath = ?, uploaded_date = ?, media_type = ?, thumbnail = ?, is_approved = ?, is_public = ?, account_id = ? WHERE id = ?');
            $stmt->execute([ $_POST['title'], $_POST['description'], $media['filepath'], date('Y-m-d H:i:s', strtotime($_POST['uploaded_date'])), $media['media_type'], $media['thumbnail'], $approved, $public, $_POST['account_id'], $_GET['id'] ]);
            header('Location: allmedia.php?success_msg=2');
            exit;
        }
        if (isset($_POST['delete'])) {
            // Redirect and delete the media
            header('Location: allmedia.php?delete=' . $_GET['id']);
            exit;
        }
    } else {
        // Create a new media
        if (isset($_POST['submit'])) {
            // convert svg to png
            if (convert_svg_to_png && strtolower($ext) == 'svg') {
                $media['filepath'] = convert_svg_to_png('../' . $media['filepath']);
                $media['filepath'] = str_replace('../', '', $media['filepath']);
            }
            // Compress image
            if (intval($_POST['image_quality']) < 100 && $media['type'] == 'image') {
                compress_image('../' . $media['filepath'], $_POST['image_quality']);
            }
            // Fix image orientation
            if (isset($_POST['correct_image_orientation']) && intval($_POST['correct_image_orientation']) && $media['type'] == 'image') {
                correct_image_orientation('../' . $media['filepath']);
            }
            // Resize image
            if (intval($_POST['image_max_width']) != -1 || intval($_POST['image_max_height']) != -1) {
                resize_image('../' . $media['filepath'], intval($_POST['image_max_width']), intval($_POST['image_max_height']));
            }
            // Strip EXIF data
            if (isset($_POST['strip_exif']) && intval($_POST['strip_exif']) && $media['type'] == 'image') {
                strip_exif('../' . $media['filepath']);
            }
            $approved = isset($_POST['approved']) ? 1 : 0;
            $public = isset($_POST['public']) ? 1 : 0;
            // Insert media into database
            $stmt = $pdo->prepare('INSERT INTO gallery_media (title,description_text,filepath,uploaded_date,media_type,thumbnail,is_approved,is_public,account_id) VALUES (?,?,?,?,?,?,?,?,?)');
            $stmt->execute([ $_POST['title'], $_POST['description'], $media['filepath'], date('Y-m-d H:i:s', strtotime($_POST['uploaded_date'])), $media['media_type'], $media['thumbnail'], $approved, $public, $_POST['account_id'] ]);
            // Redirect
            header('Location: allmedia.php?success_msg=1');
            exit;
        }
    }
}
?>
<?=template_admin_header($page . ' Media', 'gallery', 'media_manage')?>

<div class="content-title mb-4" id="main-gallery-media-form" role="banner" aria-label="Gallery Media Form Header">
    <div class="title">
    <div class="icon"><i class="bi bi-images" aria-hidden="true"></i></div>
        <div class="txt">
            <h2><?=$page?> Media</h2>
            <p><?=$page == 'Edit' ? 'Modify media settings and properties.' : 'Upload and configure new media files.'?></p>
        </div>
    </div>
</div>

<div class="mb-4">
</div>

<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="allmedia.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
        Cancel
    </a>
    <button type="submit" form="media-form" name="submit" class="btn btn-success">
        <i class="bi bi-save me-1" aria-hidden="true"></i>
        Save Media
    </button>
    <?php if ($page == 'Edit'): ?>
    <button type="submit" form="media-form" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this media?')">
        <i class="bi bi-trash me-1" aria-hidden="true"></i>
        Delete
    </button>
    <?php endif; ?>
</div>

<form method="post" enctype="multipart/form-data" class="card" id="media-form">
    <h6 class="card-header"><?=$page?> Media File</h6>
    <div class="card-body">
        <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?=$error_msg?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Media Upload Section -->
        <div class="mb-4">
            <label for="media" class="form-label fw-bold">Media File</label>
            <div id="media-upload-drop-zone" class="border border-2 border-dashed border-secondary rounded p-4 text-center mb-3" style="min-height: 150px; background-color: #f8f9fa;">
                <i class="bi bi-cloud-upload mb-3" style="font-size: 3rem; color: #6c757d;"></i>
                <p class="drop-zone-txt h5 text-muted">Select or drop your media files here!</p>
                <p class="drop-zone-filesize small text-muted">Maximum file size unrestricted for admin</p>
            </div>
            <input type="file" name="media" accept="audio/*,video/*,image/*" id="media" class="form-control">
        </div>

            <div class="media-wrapper"<?=($page == 'Edit' && file_exists('../' . $media['filepath']) ? ' style="display:flex"' : '')?>>
                <?php if ($page == 'Edit' && file_exists('../' . $media['filepath'])): ?>
                <div class="media-preview">
                    <?php if ($media['media_type'] == 'image'): ?>
                    <img src="../<?=$media['filepath']?>" alt="">
                    <?php elseif ($media['media_type'] == 'video'): ?>
                    <video src="../<?=$media['filepath']?>" width="250" height="250" controls autoplay></video>
                    <?php elseif ($media['media_type'] == 'audio'): ?>
                    <audio src="../<?=$media['filepath']?>" controls autoplay></audio>
                    <?php endif; ?>
                </div>
                <div class="media-info">
                    <h3 class="media-name"><?=htmlspecialchars($media['filepath'], ENT_QUOTES)?></h3>
                    <p class="media-size"><?=convert_filesize(filesize('../' . $media['filepath']))?></p>
                </div>
                <?php endif; ?>
            </div>

            <div class="thumbnail-wrapper"<?=($page == 'Edit' && $media['media_type'] != 'image' ? ' style="display:block"' : '')?>>
                <label for="thumbnail" class="thumbnail">Thumbnail</label>
				<input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="thumbnail" style="margin-bottom:10px">
                <a href="#" class="form-link select-video-frame"<?=($page == 'Edit' && $media['media_type'] == 'video' ? ' style="display:inline-block"' : '')?>>Or select frame from video</a>
                <div class="thumbnail-preview">
                    <?php if ($media['thumbnail'] && file_exists('../' . $media['thumbnail'])): ?>
                    <img src="../<?=$media['thumbnail']?>" alt="<?=htmlspecialchars($media['thumbnail'], ENT_QUOTES)?>">
                    <div style="font-size:14px">(<?=convert_filesize(filesize('../' . $media['thumbnail']))?>)</div>
                    <?php endif; ?>
                </div>
            </div>

        <!-- Title Field -->
        <div class="mb-3">
            <label for="title" class="form-label">
                <span class="text-danger">*</span> Title
            </label>
            <input id="title" type="text" name="title" class="form-control" placeholder="Enter media title" value="<?=htmlspecialchars($media['title'], ENT_QUOTES)?>" required>
        </div>

        <!-- Description Field -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter media description"><?=htmlspecialchars($media['description_text'], ENT_QUOTES)?></textarea>
        </div>

        <!-- Account Selection -->
        <div class="mb-3">
            <label for="account_id" class="form-label">Account</label>
            <select id="account_id" name="account_id" class="form-select" required>
                <option value="">(Select Account)</option>
                <?php foreach ($accounts as $account): ?>
                <option value="<?=$account['id']?>"<?=$account['id']==$media['account_id']?' selected':''?>><?=$account['id']?> - <?=$account['email']?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Status Checkboxes -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="checkbox" name="approved" id="approved" class="form-check-input" value="1"<?=($media['is_approved'] ? ' checked' : '')?>>
                    <label for="approved" class="form-check-label">Approved</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input type="checkbox" name="public" id="public" class="form-check-input" value="1"<?=($media['is_public'] ? ' checked' : '')?>>
                    <label for="public" class="form-check-label">Public</label>
                </div>
            </div>
        </div>

        <?php if ($page != 'Edit'): ?>
        <!-- Image Processing Options -->
        <fieldset class="border rounded p-3 mb-3">
            <legend class="fw-bold h6">Image Processing Options</legend>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="correct_image_orientation" id="correct_image_orientation" class="form-check-input">
                        <label for="correct_image_orientation" class="form-check-label">Correct Image Orientation</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch">
                        <input type="checkbox" name="strip_exif" id="strip_exif" class="form-check-input">
                        <label for="strip_exif" class="form-check-label">Strip EXIF Data</label>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="image_quality" class="form-label">Image Quality (%)</label>
                <input id="image_quality" type="number" name="image_quality" class="form-control" placeholder="100" value="100" max="100" min="0" required>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <label for="image_max_width" class="form-label">Max Width (px)</label>
                    <input id="image_max_width" type="number" name="image_max_width" class="form-control" placeholder="Auto" value="-1" required>
                </div>
                <div class="col-md-6">
                    <label for="image_max_height" class="form-label">Max Height (px)</label>
                    <input id="image_max_height" type="number" name="image_max_height" class="form-control" placeholder="Auto" value="-1" required>
                </div>
            </div>
        </fieldset>
        <?php endif; ?>

        <!-- Upload Date -->
        <div class="mb-3">
            <label for="uploaded_date" class="form-label">Uploaded Date</label>
            <input id="uploaded_date" type="datetime-local" name="uploaded_date" class="form-control" value="<?=date('Y-m-d\TH:i', strtotime($media['uploaded_date']))?>" required>
        </div>

        <!-- Action Buttons at Bottom (CORRECTED ORDER: Cancel → Save → Delete) -->
        <div class="d-flex gap-2 pt-3 border-top mt-4">
            <a href="allmedia.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
                Cancel
            </a>
            <button type="submit" name="submit" class="btn btn-success">
                <i class="bi bi-save me-1" aria-hidden="true"></i>
                Save Media
            </button>
            <?php if ($page == 'Edit'): ?>
            <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this media?')">
                <i class="bi bi-trash me-1" aria-hidden="true"></i>
                Delete
            </button>
            <?php endif; ?>
        </div>
    </div>
</form>

<?=template_admin_footer('<script>initManageMedia()</script>')?>