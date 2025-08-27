<?php
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
// Note: Settings now loaded via doctype.php from database_settings.php
include_once "assets/includes/settings/image_helper.php";
?>

<!-- About Section -->
<section id="about" class="about section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2><?php echo isset($about_content['title']) ? htmlspecialchars($about_content['title']) : 'About Us'; ?><br></h2>
    <div class="section-header-box">
      <p><?php echo isset($about_content['text']) ? htmlspecialchars($about_content['text']) : ''; ?></p>
    </div>
  </div><!-- End Section Title -->

  <div class="container">

    <div class="row gy-4">

      <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
        <div class="section-content-box">
          <?php echo isset($about_content['side_text']) ? $about_content['side_text'] : ''; ?>
        </div>
      </div>

      <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
        <img src="<?php echo htmlspecialchars(get_image_path('about', 'assets/img/about.jpg')); ?>" 
             alt="<?php echo htmlspecialchars(get_image_alt('about', 'About Image')); ?>" 
             style="max-width:100%;height:auto;">
      </div>

    </div>

  </div>

</section><!-- /About Section -->

<?php

include_once "assets/includes/call-to-action.php";
include_once "assets/includes/team.php";
include_once "assets/includes/testimonials.php";
include_once "assets/includes/contact.php";
include_once "assets/includes/footer.php";
?>
