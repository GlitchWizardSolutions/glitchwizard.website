<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOCATION: public_html/admin/blog/gallery.php
 * LOG: Gallery management for image upload, editing, and organization by albums
 * PRODUCTION: [To be updated on deployment]
 */

include "header.php";

if (isset($_GET['delete']))
{
    $id = (int) $_GET["delete"];
    // Get image path before deleting
    $stmt = $pdo->prepare("SELECT image FROM `blog_gallery` WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && !empty($row['image']))
    {
        $imagePath = realpath(__DIR__ . '/../../' . $row['image']);
        if ($imagePath && file_exists($imagePath))
        {
            @unlink($imagePath);
        }
    }
    // Now delete the record
    $stmt = $pdo->prepare("DELETE FROM `blog_gallery` WHERE id = ?");
    $stmt->execute([$id]);
}
?>

<?= template_admin_header('Blog Gallery', 'blog', 'gallery') ?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" aria-hidden="true"
                focusable="false">
                <path
                    d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z" />
            </svg>
        </div>
        <div class="txt">
            <h2>Blog Gallery</h2>
            <p>Manage the blog gallery images and their details.</p>
        </div>
    </div>
</div>

<div style="height: 20px;"></div>

<div class="mb-3">
    <a href="add_image.php" class="btn btn-outline-secondary">
        <i class="fas fa-plus me-1"></i>New Image
    </a>
</div>

<?php
if (isset($_GET['edit']))
{
    $id = (int) $_GET["edit"];
    $stmt = $pdo->prepare("SELECT * FROM `blog_gallery` WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($id))
    {
        echo '<meta http-equiv="refresh" content="0; url=gallery.php">';
        exit;
    }
    if (!$row)
    {
        echo '<meta http-equiv="refresh" content="0; url=gallery.php">';
        exit;
    }

    if (isset($_POST['edit']))
    {
        $title = addslashes($_POST['title']);
        $image = $row['image'];
        $active = addslashes($_POST['active']);
        $album_id = addslashes($_POST['album_id']);
        $description = htmlspecialchars($_POST['description']);

        if (@$_FILES['avafile']['name'] != '')
        {
            $target_dir = "uploads/gallery/";
            $target_file = $target_dir . basename($_FILES["avafile"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $uploadOk = 1;

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["avafile"]["tmp_name"]);
            if ($check !== false)
            {
                $uploadOk = 1;
            } else
            {
                echo '<div class="alert alert-danger">The file is not an image.</div>';
                $uploadOk = 0;
            }

            // Check file size
            if ($_FILES["avafile"]["size"] > 10000000)
            {
                echo '<div class="alert alert-warning">Sorry, your file is too large.</div>';
                $uploadOk = 0;
            }

            if ($uploadOk == 1)
            {
                $string = "0123456789wsderfgtyhjuk";
                $new_string = str_shuffle($string);
                // Absolute path for upload
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/gws-universal-hybrid-app/public_html/blog_system/assets/uploads/img/gallery/';
                if (!is_dir($uploadDir))
                {
                    mkdir($uploadDir, 0777, true);
                }
                $filename = 'image_' . $new_string . '.' . $imageFileType;
                $location = $uploadDir . $filename;
                if (move_uploaded_file($_FILES["avafile"]["tmp_name"], $location))
                {
                    // Store relative path for display
                    $image = 'blog_system/assets/uploads/img/gallery/' . $filename;
                } else
                {
                    echo '<div class="alert alert-danger">Failed to upload image. Check folder permissions.</div>';
                }
            }
        }

        $edit = $pdo->prepare("UPDATE blog_gallery SET album_id = ?, title = ?, image = ?, active = ?, description = ? WHERE id = ?");
        $edit->execute([$album_id, $title, $image, $active, $description, $id]);
        echo '<meta http-equiv="refresh" content="0; url=gallery.php">';
    }
    ?>
    <div class="mb-2 text-start">
         
                <a href="gallery.php" class="btn btn-outline-secondary" aria-label="Cancel and return to gallery"><i
                class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;Cancel</a>
                <button type="submit" name="edit" class="btn btn-success" aria-label="Save image changes">Save
                    Image</button>
    </div>
    <div class="card mb-3">
        <h6 class="card-header" id="editImageHeader">Edit Image</h6>
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data" aria-labelledby="editImageHeader" role="form">

                <p>
                    <label for="editTitle">Title</label>
                    <input id="editTitle" class="form-control" name="title" type="text" value="<?php echo $row['title']; ?>"
                        required aria-required="true">
                </p>
                <p>
                    <label for="editImage">Image</label><br />
                    <img src="<?= BLOG_GALLERY_URL ?>/<?php echo htmlspecialchars(basename($row['image'])); ?>" width="50px"
                        height="50px" alt="Current image preview" /><br />
                    <input type="file" id="editImage" name="avafile" class="form-control" aria-label="Upload new image" />
                </p>
                <p>
                    <label for="editActive">Active</label><br />
                    <select id="editActive" name="active" class="form-select" aria-label="Active status">
                        <option value="Yes" <?php if ($row['active'] == "Yes")
                        {
                            echo 'selected';
                        } ?>>Yes</option>
                        <option value="No" <?php if ($row['active'] == "No")
                        {
                            echo 'selected';
                        } ?>>No</option>
                    </select>
                </p>
                <p>
                    <label for="editAlbum">Album</label><br />
                    <select id="editAlbum" name="album_id" class="form-select" required aria-required="true"
                        aria-label="Select album">
                        <?php
                        $crun = $pdo->prepare("SELECT * FROM `blog_albums`");
                        $crun->execute();
                        $albums = $crun->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($albums as $rw)
                        {
                            $selected = "";
                            if ($row['album_id'] == $rw['id'])
                            {
                                $selected = "selected";
                            }
                            echo '<option value="' . htmlspecialchars($rw['id']) . '" ' . $selected . '>' . htmlspecialchars($rw['title']) . '</option>';
                        }
                        ?>
                    </select>
                </p>
                <p>
                    <label for="editDescription">Description</label>
                    <textarea class="form-control" id="summernote" name="description"
                        aria-label="Image description"><?php echo $row['description']; ?></textarea>
                </p>
        <a href="gallery.php" class="btn btn-outline-secondary" aria-label="Cancel and return to gallery"><i
                class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;Cancel</a>
                <button type="submit" name="edit" class="btn btn-success" aria-label="Save image changes">Save
                    Image</button>

            </form>
        </div>
    </div>
    <?php
} else
{
    ?>

    <!-- Search and Filter Form -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Gallery Images Management</h6>
            <small class="text-muted"><?php
                // Get total count for display
                $countSql = $pdo->prepare("SELECT COUNT(*) FROM blog_gallery");
                $countSql->execute();
                $total_images = $countSql->fetchColumn();
                echo number_format($total_images);
            ?> total images</small>
        </div>
        <div class="card-body">
            <form method="get" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input id="search" type="text" name="search" class="form-control"
                            placeholder="Search images..." 
                            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="active" class="form-label">Status</label>
                        <select name="active" id="active" class="form-select">
                            <option value="">All Status</option>
                            <option value="Yes" <?= (isset($_GET['active']) && $_GET['active'] == 'Yes') ? 'selected' : '' ?>>Active</option>
                            <option value="No" <?= (isset($_GET['active']) && $_GET['active'] == 'No') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="album_id" class="form-label">Album</label>
                        <select name="album_id" id="album_id" class="form-select">
                            <option value="">All Albums</option>
                            <?php
                            $albumSql = $pdo->query('SELECT id, title FROM blog_albums ORDER BY title');
                            while ($album = $albumSql->fetch(PDO::FETCH_ASSOC))
                            {
                                $selected = (isset($_GET['album_id']) && $_GET['album_id'] == $album['id']) ? 'selected' : '';
                                echo '<option value="' . $album['id'] . '" ' . $selected . '>' . htmlspecialchars($album['title']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-search me-1" aria-hidden="true"></i>
                            Apply Filters
                        </button>
                        <?php if (isset($_GET['search']) || isset($_GET['active']) || isset($_GET['album_id'])): ?>
                        <a href="gallery.php" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times me-1" aria-hidden="true"></i>
                            Clear
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="table-responsive" role="table" aria-label="Gallery Images">
                <table class="table table-hover align-middle mb-0" role="grid">
                    <thead role="rowgroup">
                        <tr role="row">
                            <th class="text-center" role="columnheader" scope="col">Image</th>
                            <th class="text-left" role="columnheader" scope="col">Title</th>
                            <th class="text-center" role="columnheader" scope="col">Status</th>
                            <th class="text-center" role="columnheader" scope="col">Album</th>
                            <th class="text-center" role="columnheader" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody role="rowgroup">
                        <?php
                        // Search and filter logic
                        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                        $active = isset($_GET['active']) ? trim($_GET['active']) : '';
                        $album_filter = isset($_GET['album_id']) ? trim($_GET['album_id']) : '';
                        
                        $params = [];
                        $where_conditions = [];
                        
                        if ($search !== '')
                        {
                            $where_conditions[] = "(title LIKE ? OR description LIKE ?)";
                            $params[] = "%$search%";
                            $params[] = "%$search%";
                        }
                        
                        if ($active !== '')
                        {
                            $where_conditions[] = "active = ?";
                            $params[] = $active;
                        }
                        
                        if ($album_filter !== '')
                        {
                            $where_conditions[] = "album_id = ?";
                            $params[] = $album_filter;
                        }
                        
                        $where = '';
                        if (!empty($where_conditions))
                        {
                            $where = "WHERE " . implode(' AND ', $where_conditions);
                        }
                        
                        $sql = $pdo->prepare("SELECT * FROM blog_gallery $where ORDER BY id DESC");
                        $sql->execute($params);
                        $gallery_items = $sql->fetchAll(PDO::FETCH_ASSOC);
                        if (count($gallery_items) === 0)
                        {
                            echo '<tr><td colspan="5" class="text-center text-muted">No images found.</td></tr>';
                        }
                        foreach ($gallery_items as $row)
                        {
                            $album_id = $row['album_id'];
                            $runq2 = $pdo->prepare("SELECT * FROM `blog_albums` WHERE id = ?");
                            $runq2->execute([$album_id]);
                            $cat = $runq2->fetch(PDO::FETCH_ASSOC);

                            echo '<tr role="row">';
                            echo '<td class="text-center" role="gridcell"><img src="' . BLOG_GALLERY_URL . '/' . htmlspecialchars(basename($row['image'])) . '" width="100" height="75" class="img-thumbnail" alt="' . htmlspecialchars($row['title']) . ' image" /></td>';
                            echo '<td class="text-left" role="gridcell">' . htmlspecialchars($row['title']) . '</td>';
                            echo '<td class="text-center" role="gridcell">';
                            if ($row['active'] == 'Yes') {
                                echo '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Active</span>';
                            } else {
                                echo '<span class="badge bg-secondary"><i class="fas fa-times me-1"></i>Inactive</span>';
                            }
                            echo '</td>';
                            echo '<td class="text-center" role="gridcell">' . htmlspecialchars($cat['title'] ?? 'Unknown') . '</td>';
                            echo '<td class="actions text-center" role="gridcell">';
                            echo '<div class="table-dropdown">';
                            echo '<button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for image ' . htmlspecialchars($row['title']) . '">';
                            echo '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">';
                            echo '<path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>';
                            echo '</svg>';
                            echo '</button>';
                            echo '<div class="table-dropdown-items" role="menu" aria-label="Image Actions">';
                            echo '<div role="menuitem"><a href="?edit=' . $row['id'] . '" class="green" tabindex="-1" aria-label="Edit image ' . htmlspecialchars($row['title']) . '"><i class="fas fa-edit" aria-hidden="true"></i><span>&nbsp;Edit</span></a></div>';
                            echo '<div role="menuitem"><a href="?delete=' . $row['id'] . '" class="red" tabindex="-1" onclick="return confirm(\'Are you sure you want to delete this image?\')" aria-label="Delete image ' . htmlspecialchars($row['title']) . '"><i class="fas fa-trash" aria-hidden="true"></i><span>&nbsp;Delete</span></a></div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="small">
                Showing <?= count($gallery_items) ?> of <?= $total_images ?> images
            </div>
        </div>
    </div>
    <?php
}
?>

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