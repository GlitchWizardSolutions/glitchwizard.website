<?php
// This file is part of the GWS Universal Hybrid App project. 
// It is included in the main index.php file to render the Doctype and HTML structure of the website.
include_once("vars.php");
include_once 'meta-vars.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
   <title><?php echo $title; ?></title>
   <meta name="author" content="<?php echo $author; ?>">
   <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($meta_keywords); ?>">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  
        <!-- Bootstrap 5-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

	    <!-- Font Awesome -->
	    <link href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" rel="stylesheet"/>
 
	    <!--DataTables-->
        <link href="https://cdn.datatables.net/v/bs5/dt-2.1.8/r-3.0.3/datatables.min.css" rel="stylesheet">
 
	    <!-- jQuery --> 
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	
	    <!-- SummerNote -->
	    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote-bs5.min.css" rel="stylesheet">
	    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote-bs5.min.js"></script>
 
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: OnePage 
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  ======================================================== -->
</head>
<body>