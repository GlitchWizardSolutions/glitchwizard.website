<?php
/* 
 * Blog File Upload Handler
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: upload_file.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Handle file uploads for blog content
 * DETAILED DESCRIPTION:
 * This file manages file uploads for the blog system, including images,
 * documents, and media files. It handles file validation, processing,
 * storage, and organization while ensuring security and proper file
 * management for blog content.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/assets/includes/settings/upload_config.php
 * - /public_html/assets/includes/settings/blog_config.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Secure file uploads
 * - File type validation
 * - Image processing
 * - File organization
 * - Upload logging
 */

include "header.php";
if (isset($_POST['upload']))
{
    $file = $_FILES['file'];
    $tmp_name = $_FILES['file']['tmp_name'];
    $name = $_FILES['file']['name'];

    $date = date($settings['date_format']);
    $time = date('H:i');

    $format = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allowed = [
        "png",
        "gif",
        "jpeg",
        "jpg",
        "bmp",
        "doc",
        "docx",
        "pdf",
        "txt",
        "rar",
        "html",
        "zip",
        "odt",
        "rtf",
        "csv",
        "ods",
        "xls",
        "xlsx",
        "odp",
        "ppt",
        "pptx",
        "mp3",
        "flac",
        "wav",
        "wma",
        "aac",
        "m4a",
        "htm",
        "mov",
        "avi",
        "mkv",
        "mp4",
        "wmv",
        "webm",
        "ts",
        "webp",
        "svg"
    ];
    if (!in_array($format, $allowed))
    {
        echo '<br /><div class="alert alert-info">The uploaded file is with unallowed extension.<br />';
    } else
    {
        $date_str = date('m-d-Y');
        $baseName = pathinfo($name, PATHINFO_FILENAME);
        $uploadDir = "../../blog_system/assets/downloadable/";
        $newFileName = $baseName . '-' . $date_str . '.' . $format;
        $location = $uploadDir . $newFileName;
        $counter = 1;
        // If file exists, append (1), (2), etc. before the extension
        while (file_exists($location))
        {
            $newFileName = $baseName . '-' . $date_str . "($counter)." . $format;
            $location = $uploadDir . $newFileName;
            $counter++;
        }
        move_uploaded_file($tmp_name, $location);

        $stmt = $pdo->prepare("INSERT INTO `blog_files` (filename, date, time, path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $date, $time, $location]);
        header("Location: files.php");
        exit;
    }
}
?>
<?= template_admin_header('Blog Widgets', 'blog', 'widgets') ?>

<div class="professional-card-header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path
                    d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z" />
            </svg>
        </div>
        <div class="txt">
            <h2>Upload Documents & Files</h2>
            <p>This loads them to the Blog Downloads Library.<br>
                Most file types are supported.<br></p>
        </div>
    </div>
</div>
<br>
<!-- Button Area: Cancel (left) and Go to Files (right next to it) -->
<div class="d-flex gap-2 pb-3 mb-3" style="justify-content: flex-start;">
    <a href="blog_dash.php" class="btn btn-secondary" style="min-width: 120px;">
        <i class="fas fa-arrow-left me-1"></i>Dashboard
    </a>
    <a href="files.php" class="btn btn-primary" style="min-width: 140px;">
        <i class="fas fa-folder-open me-1"></i>Go to Files
    </a>
</div>
<div class="card">
    <h6 class="professional-card-header">Upload Document</h6>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
            <p>
                <label><b>File</b></label>
                <input type="file" name="file" class="form-control" required />
            </p>
            <div class="form-actions">
                <button type="submit" name="upload" class="btn btn-primary">
                    <i class="fa fa-upload me-1"></i>Upload
                </button>
            </div>
        </form>

    </div>
</div>
</div>
<?= template_admin_footer(); ?>