<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the About Alt section of the website 

// About data should be loaded from database_settings.php via doctype.php
// Fallback for missing about data
if (!isset($about_alt_heading)) {
  $about_alt_heading = 'About Our Company';
}
if (!isset($about_alt_italic)) {
  $about_alt_italic = 'Our mission and values';
}
if (!isset($about_alt_paragraph)) {
  $about_alt_paragraph = 'Learn more about our company and what we do.';
}
if (!isset($about_alt_list) || !is_array($about_alt_list)) {
  $about_alt_list = ['Professional service', 'Quality results', 'Customer satisfaction'];
}
?>
<!-- About Alt Section -->
<section id="about-alt" class="about-alt section">

  <div class="container">

    <div class="row gy-4">
      <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="100">
        <img src="assets/img//about/about.jpg" class="img-fluid" alt="">
        <?php
        $video_path = 'assets/img/about-alt-video.mp4';
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/gws-universal-hybrid-app/public_html/' . $video_path))
        {
          ?>
          <video src="<?php echo $video_path; ?>" controls
            style="max-width:100%;height:320px;display:block;margin:20px auto;"></video>
          <?php
        } else
        {
          ?>
          <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8" class="glightbox pulsating-play-btn"></a>
          <?php
        }
        ?>
      </div>
      <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="200">
        <h3><?php echo htmlspecialchars($about_alt_heading); ?></h3>
        <p class="fst-italic">
          <?php echo htmlspecialchars($about_alt_italic); ?>
        </p>
        <ul>
          <?php foreach ($about_alt_list as $item): ?>
            <li><i class="bi bi-check2-all"></i> <span><?php echo htmlspecialchars($item); ?></span></li>
          <?php endforeach; ?>
        </ul>
        <p>
          <?php echo htmlspecialchars($about_alt_paragraph); ?>
        </p>
      </div>
    </div>

  </div>

</section><!-- /About Alt Section -->