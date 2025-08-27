<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the Testimonials section of the website.

// Testimonials data should be loaded from database_settings.php via doctype.php
// Fallback for missing testimonials data
if (!isset($testimonials_title)) {
  $testimonials_title = 'Testimonials';
}
if (!isset($testimonials_paragraph)) {
  $testimonials_paragraph = 'What our clients say about us';
}
if (!isset($testimonials) || !is_array($testimonials)) {
  $testimonials = [];
}
?>
<!-- Testimonials Section -->
<section id="testimonials" class="testimonials section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2><?php echo htmlspecialchars($testimonials_title ?? 'Testimonials'); ?></h2>
    <div class="section-header-box">
      <p><?php echo $testimonials_paragraph ?? 'Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit'; ?></p>
    </div>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row justify-content-center">
      <?php
      // Array of hardcoded testimonial images
      $testimonial_images = [
        'assets/img/testimonials/testimonials-1.jpg',
        'assets/img/testimonials/testimonials-2.jpg',
        'assets/img/testimonials/testimonials-3.jpg',
        'assets/img/testimonials/testimonials-4.jpg',
        'assets/img/testimonials/testimonials-5.jpg',
        'assets/img/testimonials/testimonials-1.jpg' // fallback for 6th testimonial
      ];
      $i = 0;
      foreach ($testimonials as $testimonial): ?>
        <!-- Testimonial Card -->
        <div class="col-lg-4 col-md-6 mb-4 d-flex align-items-stretch">
          <div class="card testimonial-item shadow-sm h-100 w-100" style="min-height: 400px; max-width: 350px;">
            <div class="card-body text-center d-flex flex-column justify-content-between h-100">
              <div>
                <?php
                $testimonial_img = isset($testimonial_images[$i]) ? $testimonial_images[$i] : $testimonial_images[0];
                if (file_exists($testimonial_img)): ?>
                  <img src="<?php echo $testimonial_img; ?>" class="testimonial-img rounded-circle mb-3"
                    alt="<?php echo htmlspecialchars($testimonial['name']); ?>">
                <?php else: ?>
                  <div class="testimonial-img rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center"
                    style="width:80px;height:80px;background:#eee;color:#888;font-size:2rem;">
                    <i class="bi bi-person-fill"></i>
                  </div>
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($testimonial['name']); ?></h3>
                <h4 class="text-muted"><?php echo htmlspecialchars($testimonial['role']); ?></h4>
              </div>
              <div class="mt-3 flex-grow-1 d-flex align-items-center justify-content-center">
                <p class="mb-0 w-100">
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span><?php echo htmlspecialchars($testimonial['text']); ?></span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div>
          </div>
        </div>
        <?php $i++; endforeach; ?>
    </div>
  </div>

</section><!-- /Testimonials Section -->