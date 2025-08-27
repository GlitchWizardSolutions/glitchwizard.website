<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the Hero section of the website.

// Safe defaults for hero_content
if (!isset($hero_content) || !is_array($hero_content)) {
  $hero_content = [
    'headline' => 'Welcome to Our Site',
    'subheadline' => 'Your trusted partner for digital solutions.',
    'bg_image' => '/assets/img/hero-bg.jpg' // Use the correct image path
  ];
}

// Simple image defaults without file-based helper
$hero_bg_image = $hero_content['bg_image'] ?? 'assets/img/hero-bg.jpg';
$hero_alt = 'Hero Background';
?>
<!-- Hero Section -->
<section id="hero" class="hero section">

  <img src="<?= htmlspecialchars($hero_bg_image) ?>"
    alt="<?= htmlspecialchars($hero_alt) ?>" data-aos="fade-in" class="hero-bg"
    style="width:100%;height:auto;max-height:70vh;object-fit:cover;">

  <div class="container">
    <div class="row justify-content-center" data-aos="zoom-out">
      <div class="col-xl-7 col-lg-9 text-center">
        <h1><?= htmlspecialchars($hero_content['headline']) ?></h1>
        <p><?= htmlspecialchars($hero_content['subheadline']) ?></p>
      </div>
    </div>
    <div class="text-center" data-aos="zoom-out" data-aos-delay="100">
      <a href="<?php echo htmlspecialchars($hero_button_link ?? 'about.php'); ?>" class="btn-get-started"><?php echo htmlspecialchars($hero_button_text ?? 'Get Started'); ?></a>
    </div>

    <!-- You can add dynamic hero icon boxes here using settings if needed -->
  </div>

</section><!-- /Hero Section -->