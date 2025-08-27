<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOCATION: public_html/admin/blog/add_image.php
 * LOG: Add new images to gallery with album assignment
 * PRODUCTION: [To be updated on deployment]
 */

include "header.php";

// Notification message
$notification = '';
if (isset($_POST['add']))
{
    $title = addslashes($_POST['title']);
    $active = addslashes($_POST['active']);
    $album_id = addslashes($_POST['album_id']);
    $description = htmlspecialchars($_POST['description']);

    $image = '';
    $uploadOk = 1;

    if (@$_FILES['avafile']['name'] != '')
    {
        $imageFileType = strtolower(pathinfo($_FILES["avafile"]["name"], PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["avafile"]["tmp_name"]);
        if ($check === false)
        {
            $notification = '<div class="alert alert-danger">The file is not an image.</div>';
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["avafile"]["size"] > 10000000)
        {
            $notification = '<div class="alert alert-warning">Sorry, your file is too large.</div>';
            $uploadOk = 0;
        }

        if ($uploadOk == 1)
        {
            // Absolute path for upload (relative to this script)
            $uploadDir = realpath(__DIR__ . '/../../blog_system/assets/uploads/img/gallery/');
            if ($uploadDir === false)
            {
                // Try to create the directory if it doesn't exist
                $uploadDir = __DIR__ . '/../../blog_system/assets/uploads/img/gallery/';
                if (!is_dir($uploadDir))
                {
                    mkdir($uploadDir, 0777, true);
                }
                $uploadDir = realpath($uploadDir);
            }
            // Use rename field if provided, else original filename
            $rename = isset($_POST['rename']) ? trim($_POST['rename']) : '';
            if ($rename !== '')
            {
                // Remove extension if user added it
                $rename = preg_replace('/\.[A-Za-z0-9]+$/', '', $rename);
                $rename = preg_replace('/[^A-Za-z0-9._-]/', '_', $rename); // sanitize
                $filename = $rename . '.' . $imageFileType;
            } else
            {
                $originalName = basename($_FILES["avafile"]["name"]);
                $originalName = preg_replace('/[^A-Za-z0-9._-]/', '_', $originalName); // sanitize
                $filename = $originalName;
            }
            $filePath = $uploadDir . DIRECTORY_SEPARATOR . $filename;
            // If file exists, show error and do not upload
            if (file_exists($filePath))
            {
                $notification = '<div class="alert alert-danger">A file with this name already exists. Please change the file name or rename your image before uploading. Overwriting is not allowed for safety.</div>';
                $uploadOk = 0;
            } else
            {
                if (move_uploaded_file($_FILES["avafile"]["tmp_name"], $filePath))
                {
                    // Store relative path for display
                    $image = 'blog_system/assets/uploads/img/gallery/' . $filename;
                } else
                {
                    $notification = '<div class="alert alert-danger">Failed to upload image. Check folder permissions.</div>';
                    $uploadOk = 0;
                }
            }
        }
    } else
    {
        $notification = '<div class="alert alert-warning">Please select an image to upload.</div>';
        $uploadOk = 0;
    }

    if ($uploadOk == 1)
    {
        $add = $pdo->prepare("INSERT INTO `blog_gallery` (album_id, title, image, description, active) VALUES (?, ?, ?, ?, ?)");
        $add->execute([$album_id, $title, $image, $description, $active]);
        echo '<meta http-equiv="refresh" content="0; url=gallery.php">';
        exit;
    }
}
?>
<?= template_admin_header('Add Gallery Images', 'blog', 'gallery') ?>
<div class="professional-card-header">
    <div class="title">
        <div class="icon">
            <i class="fas fa-images"></i>
        </div>
        <div class="txt">
            <h2>Add Gallery Images</h2>
            <p>Maintenance of all of the gallery images.</p>
        </div>
    </div>
</div>
<br>

<div class="card">
    <h6 class="professional-card-header">Add Image</h6>
    <div class="card-body">
        <?php if (!empty($notification))
            echo $notification; ?>
        <form action="" method="post" enctype="multipart/form-data">
            <p>
                <label>Title</label>
                <input class="form-control" name="title" value="" type="text" required>
            </p>
            <p>
                <label>Image</label>
                <input type="file" name="avafile" class="form-control" required />
            </p>
            <p>
                <label>Rename Image As (optional, no extension)</label>
                <input type="text" name="rename" class="form-control" maxlength="100" placeholder="e.g. my_photo" />
                <small class="text-muted">If provided, this will be the filename (extension will be added
                    automatically).</small>
            </p>
            <p>
                <label>Active</label><br />
                <select name="active" class="form-select" required>
                    <option value="Yes" selected>Yes</option>
                    <option value="No">No</option>
                </select>
            </p>
            <p>
                <label>Album</label><br />
                <select name="album_id" class="form-select" required>
                    <?php
                    $crun = $pdo->prepare("SELECT id, title FROM `blog_albums`");
                    $crun->execute();
                    $albums = $crun->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($albums as $rw)
                    {
                        echo '<option value="' . htmlspecialchars($rw['id']) . '">' . htmlspecialchars($rw['title']) . '</option>';
                    }
                    ?>
                </select>
            </p>
            <p>
                <label>Description</label>
                <textarea class="form-control" id="summernote" name="description"></textarea>
            </p>

            <button type="submit" name="add" class="btn btn-primary">Add Gallery Image</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#summernote').summernote({ height: 350 });

        var noteBar = $('.note-toolbar');
        noteBar.find('[data-toggle]').each(function () {
            $(this).attr('data-bs-toggle', $(this).attr('data-toggle')).removeAttr('data-toggle');
        });
    });
</script>
<?= template_admin_footer(); ?>