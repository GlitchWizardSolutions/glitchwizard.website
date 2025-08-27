<?php
/* 
 * Landing Page Branding Upload Handler
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: branding-upload-handler.php
 * LOCATION: /public_html/admin/landing_page_generator/
 * PURPOSE: Process branding uploads and settings for landing pages
 * DETAILED DESCRIPTION:
 * This file handles the processing of branding-related uploads and settings
 * for landing pages. It manages file uploads for logos and other brand assets,
 * processes color and typography settings, and ensures proper storage and
 * organization of branding elements.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/assets/includes/settings/upload_config.php
 * - /public_html/assets/includes/settings/branding_config.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Logo upload processing
 * - Image optimization
 * - Color settings management
 * - Typography configuration
 * - File validation
 */
 

// ==== Initialization ====
$folder = $_GET['folder'] ?? 'temp';
$base_path = "../../" . basename($folder);
$img_dir = "$base_path/assets/img/";
$includes_dir = "$base_path/assets/includes/";
$branding_file = "$includes_dir/branding.php";
$logo_file = "$img_dir/logo.png";

// ==== Ensure directories exist ====
if (!is_dir($img_dir))
  mkdir($img_dir, 0775, true);
if (!is_dir($includes_dir))
  mkdir($includes_dir, 0775, true);

// ==== Confirm Overwrite Logic ====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_overwrite']))
{
  $proceed = $_POST['confirm_overwrite'] === 'yes';
  if (!$proceed)
  {
    echo "<h2>Logo upload canceled. Existing logo was kept.</h2>";
    echo "<p><a href='branding-ui.php?folder=" . htmlspecialchars($folder) . "'>Return to Branding UI</a></p>";
    exit;
  }
}

// ==== Save Branding Colors and Fonts ====
$primary = $_POST['brand_primary'] ?? '#007BFF';
$secondary = $_POST['brand_secondary'] ?? '#6C757D';
$background = $_POST['brand_background'] ?? '#FFFFFF';
$text = $_POST['brand_text'] ?? '#333333';
$font_headings = $_POST['brand_font_headings'] ?? "'Segoe UI', sans-serif";
$font_body = $_POST['brand_font_body'] ?? "'Segoe UI', sans-serif";

$branding_code = "<?php
\$brand_primary = '$primary';
\$brand_secondary = '$secondary';
\$brand_background = '$background';
\$brand_text = '$text';
\$brand_font_headings = \"$font_headings\";
\$brand_font_body = \"$font_body\";
?>";

file_put_contents($branding_file, $branding_code);

// ==== Handle Logo Upload ====
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK)
{
  $tmp_name = $_FILES['logo']['tmp_name'];
  $existing = file_exists($logo_file);

  // If logo already exists and overwrite wasn't confirmed, show confirmation form
  if ($existing && !isset($_POST['confirm_overwrite']))
  {
    echo "<h2>A logo already exists for this landing page.</h2>";
    echo "<form method='post' action=''>
                <input type='hidden' name='brand_primary' value='$primary'>
                <input type='hidden' name='brand_secondary' value='$secondary'>
                <input type='hidden' name='brand_background' value='$background'>
                <input type='hidden' name='brand_text' value='$text'>
                <input type='hidden' name='brand_font_headings' value=\"$font_headings\">
                <input type='hidden' name='brand_font_body' value=\"$font_body\">
                <input type='hidden' name='folder' value='$folder'>
                <input type='hidden' name='confirm_overwrite' value='yes'>
                <p>Would you like to replace the existing logo?</p>
                <button type='submit'>Yes, Overwrite Logo</button>
              </form>
              <form method='post' action=''>
                <input type='hidden' name='confirm_overwrite' value='no'>
                <button type='submit'>No, Keep Existing</button>
              </form>";
    exit;
  }

  // Resize and save as 160x160 PNG using GD
  $image_info = getimagesize($tmp_name);
  $width = $image_info[0];
  $height = $image_info[1];
  $src_type = $image_info[2];

  switch ($src_type)
  {
    case IMAGETYPE_JPEG:
      $src = imagecreatefromjpeg($tmp_name);
      break;
    case IMAGETYPE_PNG:
      $src = imagecreatefrompng($tmp_name);
      break;
    case IMAGETYPE_GIF:
      $src = imagecreatefromgif($tmp_name);
      break;
    default:
      die("Unsupported image format.");
  }

  $dst = imagecreatetruecolor(160, 160);
  imagealphablending($dst, false);
  imagesavealpha($dst, true);

  // Resize & center crop logic
  $min_dim = min($width, $height);
  $src_x = ($width - $min_dim) / 2;
  $src_y = ($height - $min_dim) / 2;

  imagecopyresampled($dst, $src, 0, 0, $src_x, $src_y, 160, 160, $min_dim, $min_dim);
  imagepng($dst, $logo_file);
  imagedestroy($src);
  imagedestroy($dst);
}

// ==== Success Message ====
echo "<h2>Branding saved for <code>" . htmlspecialchars($folder) . "</code>.</h2>";
echo "<p><a href='branding-ui.php?folder=" . htmlspecialchars($folder) . "'>Return to Branding UI</a></p>";