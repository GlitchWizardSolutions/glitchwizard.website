<?php
// Include SEO settings
include_once __DIR__ . '/../../../assets/includes/settings/seo_settings.php';

// Determine current page name (without .php)
$current_page = basename($_SERVER['SCRIPT_NAME'], '.php');

// Use SEO settings for the current page, or fallback to default
$seo = $seo_settings[$current_page] ?? $seo_settings['default'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <meta name="description" content="<?php echo htmlspecialchars($seo['description']); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($seo['keywords']); ?>">
  <meta name="author" content="Barbara Moore">
  <meta name="copyright" content="GlitchWizard Solutions, Tallahassee, Florida">
  <meta name="robots" content="noindex,nofollow">
  <!-- Optional: Open Graph for social sharing -->
  <!-- <meta property="og:title" content="<?php echo htmlspecialchars($seo['title']); ?>"> -->
  <!-- <meta property="og:description" content="<?php echo htmlspecialchars($seo['description']); ?>"> -->
  <!-- <meta property="og:image" content=""> -->
  <!-- <meta name="theme-color" content="#ffffff"> -->
  <title><?php echo htmlspecialchars($seo['title']); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bad+Script&display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css"
    href="https://fonts.googleapis.com/css?family=Fira+Sans:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i">
  <!-- Google fonts -->
  <!-- #region -->
  <link rel="stylesheet" type="text/css"
    href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i">
  <!-- Google fonts -->
  <link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
    integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
    crossorigin="anonymous" referrerpolicy="no-referrer" Content-Type="font/woff2">
  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  
  <!-- Client Portal Brand Enhancement -->
  <link href="assets/css/client-branding.css" rel="stylesheet">

</head>

<body> 