<?php
include_once("assets/includes/branding.php");
$brandingFile = "assets/includes/branding.php";
if (file_exists($brandingFile))
{
  include $brandingFile;
}

include "meta-vars.php";

// Load custom content
$varsFile = "assets/includes/vars.php";
if (file_exists($varsFile))
{
  include $varsFile;
} else
{
  $business_name = "Sample Business";
  $headline = "Welcome to Our Services";
  $subheadline = "Quality and trust you can count on.";
  $about_text = "We are a dedicated company in our industry, committed to excellence.";
  $contact_email = "info@example.com";
  $logo_path = "assets/img/default/logo.png";
  $hero_img = "assets/img/default/hero.jpg";
}

// Fallback image loader
function image_or_fallback($name)
{
  $primary = "assets/img/$name.jpg";
  $default = "assets/img/default/$name.jpg";
  return file_exists($primary) ? $primary : $default;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?php echo $meta_og_title ?? $business_name; ?></title>
  <meta name="description" content="<?php echo $meta_description ?? ''; ?>">
  <meta name="keywords" content="<?php echo $meta_keywords ?? ''; ?>">

  <!-- Open Graph / Twitter -->
  <meta property="og:title" content="<?php echo $meta_og_title ?? ''; ?>">
  <meta property="og:description" content="<?php echo $meta_og_description ?? ''; ?>">
  <meta property="og:image" content="<?php echo $meta_og_image ?? image_or_fallback('hero'); ?>">
  <meta property="og:url" content="<?php echo $meta_og_url ?? ''; ?>">
  <meta property="og:type" content="<?php echo $meta_og_type ?? 'website'; ?>">
  <meta name="twitter:card" content="<?php echo $meta_twitter_card ?? 'summary_large_image'; ?>">
  <meta name="twitter:title" content="<?php echo $meta_twitter_title ?? ''; ?>">
  <meta name="twitter:description" content="<?php echo $meta_twitter_description ?? ''; ?>">
  <meta name="twitter:image" content="<?php echo $meta_twitter_image ?? image_or_fallback('hero'); ?>">

  <link rel="stylesheet" href="assets/css/main.css">
  <style>
    :root {
      --primary:
        <?php echo $brand_primary ?? '#007BFF'; ?>
      ;
      --secondary:
        <?php echo $brand_secondary ?? '#6C757D'; ?>
      ;
      --background:
        <?php echo $brand_background ?? '#FFFFFF'; ?>
      ;
      --text:
        <?php echo $brand_text ?? '#333333'; ?>
      ;
      --font-headings:
        <?php echo $brand_font_headings ?? "'Segoe UI', sans-serif"; ?>
      ;
      --font-body:
        <?php echo $brand_font_body ?? "'Segoe UI', sans-serif"; ?>
      ;
    }

    body {
      background: var(--background);
      color: var(--text);
      font-family: var(--font-body);
    }

    h1,
    h2,
    h3 {
      font-family: var(--font-headings);
      color: var(--primary);
    }

    header.hero {
      border-bottom: 5px solid var(--primary);
    }

    footer {
      background: var(--secondary);
    }
  </style>
</head>

<body>
  <header class="hero" style="background-image: url('<?php echo image_or_fallback('hero'); ?>');">
    <div class="overlay">
      <img src="<?php echo $logo_path ?? image_or_fallback('logo'); ?>" alt="Logo" class="logo">
      <h1><?php echo $headline; ?></h1>
      <h2><?php echo $subheadline; ?></h2>
    </div>
  </header>

  <section class="about">
    <h2>About Us</h2>
    <div class="about-content">
      <img src="<?php echo image_or_fallback('about-us'); ?>" alt="About Image">
      <p><?php echo $about_text; ?></p>
    </div>
  </section>

  <footer>
    <p>Contact us: <a href="mailto:<?php echo $contact_email; ?>"><?php echo $contact_email; ?></a></p>
  </footer>
</body>

</html>