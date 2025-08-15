<?php
// Admin: Public Image Settings
// Allows uploading and replacing section images (hero, about, etc.) with resizing and optimization

// Include the centralized image configuration
require_once 'public_image_settings_config.php';

// Adjust paths to be relative to the current script location
foreach ($images as $key => $img) {
    $images[$key]['path'] = '../../' . $img['path'];
}

// Function to optimize and replace image
function optimize_and_replace_image($target_path, $tmp_file)
{
    $orig_info = getimagesize($target_path);
    $img_info = getimagesize($tmp_file);
    if (!$img_info)
        return false;
    $width = $orig_info ? $orig_info[0] : $img_info[0];
    $height = $orig_info ? $orig_info[1] : $img_info[1];
    $src_img = null;
    // Support JPEG, PNG, GIF, BMP, WEBP
    switch ($img_info[2])
    {
        case IMAGETYPE_JPEG:
            $src_img = imagecreatefromjpeg($tmp_file);
            break;
        case IMAGETYPE_PNG:
            $src_img = imagecreatefrompng($tmp_file);
            break;
        case IMAGETYPE_GIF:
            $src_img = imagecreatefromgif($tmp_file);
            break;
        case IMAGETYPE_BMP:
            if (function_exists('imagecreatefrombmp'))
                $src_img = imagecreatefrombmp($tmp_file);
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagecreatefromwebp'))
                $src_img = imagecreatefromwebp($tmp_file);
            break;
    }
    // Always save as JPEG for .jpg targets
    $target_ext = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
    if ($src_img)
    {
        $dst_img = imagecreatetruecolor($width, $height);
        imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $width, $height, $img_info[0], $img_info[1]);
        if ($target_ext === 'jpg' || $target_ext === 'jpeg')
        {
            imagejpeg($dst_img, $target_path, 90);
        } elseif ($target_ext === 'png')
        {
            imagepng($dst_img, $target_path, 6);
        } elseif ($target_ext === 'gif')
        {
            imagegif($dst_img, $target_path);
        } else
        {
            // Default to JPEG if unknown
            imagejpeg($dst_img, $target_path, 90);
        }
        imagedestroy($src_img);
        imagedestroy($dst_img);
        return true;
    } else
    {
        return false; // Unsupported format
    }
}

$success = $error = '';

// Handle alt text updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_alt_text']))
{
    $config_file = 'public_image_settings_config.php';
    $config_content = file_get_contents($config_file);
    
    // Update each alt text in the config file
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'alt_') === 0) {
            $image_key = substr($key, 4); // Remove 'alt_' prefix
            if (isset($images[$image_key])) {
                $old_alt = "'" . $images[$image_key]['alt'] . "'";
                $new_alt = "'" . addslashes($value) . "'";
                $pattern = "/('$image_key'\s*=>\s*\[.*?'alt'\s*=>\s*)'[^']*'/s";
                $replacement = "$1$new_alt";
                $config_content = preg_replace($pattern, $replacement, $config_content);
            }
        }
    }
    
    if (file_put_contents($config_file, $config_content)) {
        $success = 'Alt text updated successfully!';
        // Reload the config
        include $config_file;
        foreach ($images as $key => $img) {
            $images[$key]['path'] = '../../' . $img['path'];
        }
    } else {
        $error = 'Failed to update alt text.';
    }
}

// Handle image uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_key']) && isset($_FILES['image_upload']))
{
    $key = $_POST['image_key'];
    if (isset($images[$key]))
    {
        $target_path = realpath(__DIR__ . '/' . $images[$key]['path']);
        $tmp_file = $_FILES['image_upload']['tmp_name'];
        $is_video = isset($images[$key]['is_video']) && $images[$key]['is_video'];
        if ($_FILES['image_upload']['error'] === UPLOAD_ERR_OK && $target_path)
        {
            if ($is_video)
            {
                $ext = strtolower(pathinfo($_FILES['image_upload']['name'], PATHINFO_EXTENSION));
                if ($ext === 'mp4')
                {
                    if (move_uploaded_file($tmp_file, $target_path))
                    {
                        $success = $images[$key]['label'] . ' updated successfully!';
                    } else
                    {
                        $error = 'Video upload failed.';
                    }
                } else
                {
                    $error = 'Only MP4 videos are supported.';
                }
            } else if (optimize_and_replace_image($target_path, $tmp_file))
            {
                $success = $images[$key]['label'] . ' updated successfully!';
            } else
            {
                $error = 'Image optimization failed.';
            }
        } else
        {
            $error = 'Upload error or invalid target path.';
        }
    } else
    {
        $error = 'Invalid image selection.';
    }
}

?>
<h1>Public Image Settings</h1>
<?php if ($success): ?>
    <div style="color:green;"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div style="color:red;"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <label for="image_key">Select Section Image:</label>
    <select name="image_key" id="image_key">
        <?php foreach ($images as $key => $img): ?>
            <option value="<?php echo $key; ?>"><?php echo htmlspecialchars($img['label']); ?></option>
        <?php endforeach; ?>
    </select>
    <br><br>
    <label for="image_upload">Upload New Image:</label>
    <input type="file" name="image_upload" id="image_upload" accept="image/*" required>
    <br><br>
    <button type="submit">Replace Image</button>
</form>

<hr>
<h2>Update Alt Text</h2>
<form method="post">
    <input type="hidden" name="update_alt_text" value="1">
    <?php foreach ($images as $key => $img): ?>
        <div style="margin-bottom: 15px;">
            <label for="alt_<?php echo $key; ?>"><?php echo htmlspecialchars($img['label']); ?> Alt Text:</label><br>
            <input type="text" name="alt_<?php echo $key; ?>" id="alt_<?php echo $key; ?>" 
                   value="<?php echo htmlspecialchars($img['alt'] ?? ''); ?>" 
                   style="width: 100%; max-width: 400px;" 
                   placeholder="Describe this image for accessibility">
        </div>
    <?php endforeach; ?>
    <button type="submit">Update Alt Text</button>
</form>

<hr>
<h2>Current Images</h2>
<ul>
    <?php foreach ($images as $key => $img): ?>
        <li>
            <?php echo htmlspecialchars($img['label']); ?>:<br>
            <?php
            if (isset($img['is_video']) && $img['is_video'])
            {
                $video_path = realpath(__DIR__ . '/' . $img['path']);
                if ($video_path && file_exists($video_path))
                {
                    echo '<video src="' . $img['path'] . '" controls style="max-width:300px;height:180px;"></video>';
                } else
                {
                    echo '<span style="color:red;">Video not found</span>';
                }
            } else
            {
                $ext = strtolower(pathinfo($img['path'], PATHINFO_EXTENSION));
                if ($ext === 'svg')
                {
                    $svg_path = realpath(__DIR__ . '/' . $img['path']);
                    if ($svg_path && file_exists($svg_path))
                    {
                        $svg_size = filesize($svg_path);
                        echo '<object type="image/svg+xml" data="' . $img['path'] . '" style="max-width:80px;height:80px;"></object>';
                        echo '<br><span style="font-size:16px;color:#555;">SVG size: ' . $svg_size . ' bytes</span>';
                    } else
                    {
                        echo '<span style="color:red;">SVG not found</span>';
                    }
                } else
                {
                    echo '<img src="' . $img['path'] . '" alt="' . htmlspecialchars($img['alt'] ?? $img['label']) . '" style="max-width:300px;height:auto;">';
                }
            }
            ?>
            <br><strong>Alt Text:</strong> <?php echo htmlspecialchars($img['alt'] ?? 'Not set'); ?>
        </li>
    <?php endforeach; ?>
</ul>