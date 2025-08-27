<?php
// Initialize session
session_start();
// Include configuration file
include_once 'config.php';
// Connect to MySQL database using PDO
try {
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to database: ' . $exception->getMessage());
}
// Template header, feel free to customize this
function template_header($title) {
	// Admin panel link - will only be visible if the user is an admin
	$admin_panel_link = isset($_SESSION['account_role']) && $_SESSION['account_role'] == 'Admin' ? '<a href="admin/index.php" target="_blank">Admin</a>' : '';
	// Get the current file name (eg. home.php, profile.php)
	$current_file_name = basename($_SERVER['PHP_SELF']);
    // Check if the user is logged in and set the logout link accordingly
    $logout_link = isset($_SESSION['account_loggedin']) ? '<a href="logout.php" class="alt">
        <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"/></svg>
        Logout
    </a>' : '';
// Indenting the code below will result in an error
echo '<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>' . $title . '</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
        <header class="header">

            <div class="wrapper">

                <h1>GALLERY <span>SYSTEM</span></h1>

                <!-- If you prefer to use a logo instead of text uncomment the below code and remove the above h1 tag and replace the src attribute with the path to your logo image
                <img src="https://placehold.co/200x45" width="200" height="45" alt="Logo" class="logo">
                -->

                <!-- Responsive menu toggle icon -->
                <input type="checkbox" id="menu">
                <label for="menu"></label>
                
                <nav class="menu">
                    <a href="index.php" class="' . ($current_file_name == 'index.php' ? 'active' : '') . '">Gallery</a>
                    <a href="upload.php" class="' . ($current_file_name == 'upload.php' ? 'active' : '') . '">Upload</a>
                    <a href="collections.php" class="' . ($current_file_name == 'collections.php' || $current_file_name == 'collection.php' ? 'active' : '') . '">My Collections</a>
                    ' . $admin_panel_link . '
                    ' . $logout_link . '
                </nav>

            </div>

        </header>
';
}
// Template footer
function template_footer() {
// Indenting the code below will result in an error
echo '
        <footer>
            <div class="content-wrapper">
                <!-- Feel free to customize the footer content. No attribution required. -->
                <p>&copy; ' . date('Y') . ', <a href="https://codeshack.io/package/php/advanced-gallery-system/" target="_blank">Gallery System</a></p>
            </div>
        </footer>
        <script>
        const websiteUrl = "' . website_url . '";
        </script>
        <script src="script.js"></script>
    </body>
</html>';
}
// Convert filesize to a readable format
function convert_filesize($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB']; 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow]; 
}
// Convert SVG to PNG
function convert_svg_to_png($source) {
    // The ImageMagick PHP extension is required to convert SVG images 
    if (class_exists('Imagick')) {
        $im = new Imagick();
        // Fetch the SVG file
        $svg = file_get_contents($source);
        // Ensure the background is transparent
        $im->setBackgroundColor(new ImagickPixel('transparent'));
        // Read and process the SVG image
        $im->readImageBlob($svg);
        // Set type as PNG
        $im->setImageFormat('png24');
        // Determine the new path
        $new_path = substr_replace($source, 'png', strrpos($source , '.')+1);
        // Write image to file
        $im->writeImage($new_path);
        // Clean up
        $im->clear();
        $im->destroy();
        // Delete the old file
        unlink($source);
        // return the new path
        return $new_path;
    } else {
        exit('The ImageMagick PHP extension is required to convert SVG images to PNG images!');
    }
}
// Create image thumbnails for image media files
function create_image_thumbnail($source, $id) {
    $info = getimagesize($source);
    $image_width = $info[0];
    $image_height = $info[1];
    $mime_type = $info['mime'];
    if ($image_width <= 0 || $image_height <= 0) {
        return false;
    }
    $new_width = $image_width;
    $new_height = $image_height;
    if ($image_width > auto_generate_image_thumbnail_max_width || $image_height > auto_generate_image_thumbnail_max_height) {
        $ratio_w = auto_generate_image_thumbnail_max_width / $image_width;
        $ratio_h = auto_generate_image_thumbnail_max_height / $image_height;
        $ratio = min($ratio_w, $ratio_h);
        $new_width = floor($image_width * $ratio);
        $new_height = floor($image_height * $ratio);
    }
    $thumbnail_parts = explode('.', basename($source));
    $extension = count($thumbnail_parts) > 1 ? strtolower(end($thumbnail_parts)) : '';
    $thumbnail_path = 'media/thumbnails/' . $id . '.' . $extension;
    $img = null;
    $success = false;
    try {
        switch ($mime_type) {
            case 'image/jpeg':
                $img = imagecreatefromjpeg($source);
                if ($img) {
                    $resized_img = imagescale($img, $new_width, $new_height);
                    if ($resized_img) {
                        $success = imagejpeg($resized_img, $thumbnail_path);
                        imagedestroy($resized_img);
                    }
                }
                break;
            case 'image/webp':
                $img = imagecreatefromwebp($source);
                if ($img) {
                    imagealphablending($img, false);
                    imagesavealpha($img, true);
                    $resized_img = imagescale($img, $new_width, $new_height);
                    if ($resized_img) {
                        imagealphablending($resized_img, false);
                        imagesavealpha($resized_img, true);
                        $success = imagewebp($resized_img, $thumbnail_path);
                        imagedestroy($resized_img);
                    }
                }
                break;
            case 'image/png':
                $img = imagecreatefrompng($source);
                if ($img) {
                    imagealphablending($img, false);
                    imagesavealpha($img, true);
                    $resized_img = imagescale($img, $new_width, $new_height);
                    if ($resized_img) {
                        imagealphablending($resized_img, false);
                        imagesavealpha($resized_img, true);
                        $success = imagepng($resized_img, $thumbnail_path);
                        imagedestroy($resized_img);
                    }
                }
                break;
            case 'image/gif':
                $img = imagecreatefromgif($source);
                if ($img) {
                    $transparent_index = imagecolortransparent($img);
                    if ($transparent_index >= 0) {
                        $transparent_color = imagecolorsforindex($img, $transparent_index);
                        $transparent_new = imagecolorallocatealpha($img, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue'], 127);
                        imagefill($img, 0, 0, $transparent_new);
                        imagecolortransparent($img, $transparent_new);
                    }
                    $resized_img = imagescale($img, $new_width, $new_height);
                    if ($resized_img) {
                        if ($transparent_index >= 0) {
                            $transparent_color_resized = imagecolorsforindex($resized_img, $transparent_index);
                            $transparent_new_resized = imagecolorallocatealpha($resized_img, $transparent_color_resized['red'], $transparent_color_resized['green'], $transparent_color_resized['blue'], 127);
                            imagefill($resized_img, 0, 0, $transparent_new_resized);
                            imagecolortransparent($resized_img, $transparent_new_resized);
                        }
                        $success = imagegif($resized_img, $thumbnail_path);
                        imagedestroy($resized_img);
                    }
                }
                break;
            default:
                return false;
        }
    } catch (Exception $e) {
        if (isset($resized_img) && ($resized_img instanceof GdImage || is_resource($resized_img))) {
            imagedestroy($resized_img);
        }
        if (isset($img) && ($img instanceof GdImage || is_resource($img))) {
            imagedestroy($img);
        }
        return false;
    }
    if (isset($img) && ($img instanceof GdImage || is_resource($img))) {
        imagedestroy($img);
    }
    if ($success) {
        return $thumbnail_path;
    }
    return false;
}
// Compress image function
function compress_image($source, $quality) {
    $info = getimagesize($source);
    if ($info['mime'] == 'image/jpeg') {
        imagejpeg(imagecreatefromjpeg($source), $source, $quality);
    } else if ($info['mime'] == 'image/webp') {
        imagewebp(imagecreatefromwebp($source), $source, $quality);
    } else if ($info['mime'] == 'image/png') {
        $png_quality = 9 - floor($quality/10);
        $png_quality = $png_quality < 0 ? 0 : $png_quality;
        $png_quality = $png_quality > 9 ? 9 : $png_quality;
        imagepng(imagecreatefrompng($source), $source, $png_quality);
    }
}
// Correct image orientation function
function correct_image_orientation($source) {
    if (strpos(strtolower($source), '.jpg') == false && strpos(strtolower($source), '.jpeg') == false) return;
    $exif = exif_read_data($source);
    $info = getimagesize($source);
    if ($exif && isset($exif['Orientation'])) {
        if ($exif['Orientation'] && $exif['Orientation'] != 1) {
            if ($info['mime'] == 'image/jpeg') {
                $img = imagecreatefromjpeg($source);
            } else if ($info['mime'] == 'image/webp') {
                $img = imagecreatefromwebp($source);
            } else if ($info['mime'] == 'image/png') {
                $img = imagecreatefrompng($source);
            }
            $deg = 0;
            $deg = $exif['Orientation'] == 3 ? 180 : $deg;
            $deg = $exif['Orientation'] == 6 ? 90 : $deg;
            $deg = $exif['Orientation'] == 8 ? -90 : $deg;
            if ($deg) {
                $img = imagerotate($img, $deg, 0);
                if ($info['mime'] == 'image/jpeg') {
                    imagejpeg($img, $source);
                } else if ($info['mime'] == 'image/webp') {
                    imagewebp($img, $source);
                } else if ($info['mime'] == 'image/png') {
                    imagepng($img, $source);
                }
            }
        }
    }
}
// Resize image function
function resize_image($source, $max_width, $max_height) {
    $info = getimagesize($source);
	$image_width = $info[0];
	$image_height = $info[1];
	$new_width = $image_width;
	$new_height = $image_height;
	if ($image_width > $max_width || $image_height > $max_height) {
		if ($image_width > $image_height) {
	    	$new_height = floor(($image_height/$image_width)*$max_width);
  			$new_width  = $max_width;
		} else {
			$new_width  = floor(($image_width/$image_height)*$max_height);
			$new_height = $max_height;
		}
	}
    if ($info['mime'] == 'image/jpeg') {
        $img = imagescale(imagecreatefromjpeg($source), $new_width, $new_height);
        imagejpeg($img, $source);
    } else if ($info['mime'] == 'image/webp') {
        $img = imagescale(imagecreatefromwebp($source), $new_width, $new_height);
        imagewebp($img, $source);
    } else if ($info['mime'] == 'image/png') {
        $img = imagescale(imagecreatefrompng($source), $new_width, $new_height);
        imagepng($img, $source);
    }
}
// Strip exif data function
function strip_exif($source) {
    if (extension_loaded('imagick') && class_exists('Imagick')) {
        $imagick = null;
        try {
            $realSourcePath = realpath($source);
            $imagick = new Imagick($realSourcePath);
            $format = $imagick->getImageFormat();
            if ($format !== 'JPEG' && $format !== 'TIFF') {
                $imagick->clear();
                $imagick->destroy();
                return true;
            }
            if ($imagick->stripImage()) {
                if ($imagick->writeImage($realSourcePath)) {
                    $imagick->clear();
                    $imagick->destroy();
                    return true;
                }
            }
            $imagick->clear();
            $imagick->destroy();
            return false;
        } catch (Exception $e) {
            return false;
        }
    } else {
        $info = @getimagesize($source);
        if (!$info || $info['mime'] !== 'image/jpeg') {
            return true;
        }
        $img = null;
        $save_success = false;
        try {
            $img = @imagecreatefromjpeg($source);
            $save_success = imagejpeg($img, $source, 100);
            if (!$save_success) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return false;
        } finally {
            if ($img instanceof GdImage || is_resource($img)) {
                imagedestroy($img);
            }
        }
    }
}
// Function to get collection by ID
function get_collection_by_id($collections, $collection_id) {
	foreach ($collections as $collection) {
		if ($collection['id'] == $collection_id) {
			return $collection;
		}
	}
	return null;
}
?>