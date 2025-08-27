<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the Call To Action section of the website.

// CTA data should be loaded from database_settings.php via doctype.php
// Fallbacks to prevent undefined variable warnings
if (!isset($cta_heading))
  $cta_heading = 'Ready to get started?';
if (!isset($cta_text))
  $cta_text = 'Contact us today to discuss your project and see how we can help.';
if (!isset($cta_button_text))
  $cta_button_text = 'Contact Us';
if (!isset($cta_button_link))
  $cta_button_link = '#contact';
?>
<!-- Call To Action Section -->
<section id="call-to-action" class="call-to-action section accent-background">

  <div class="container">
    <div class="row justify-content-center" data-aos="zoom-in" data-aos-delay="100">
      <div class="col-xl-10">
        <div class="text-center">
          <h3><?php echo htmlspecialchars($cta_heading); ?></h3>
          <p><?php echo htmlspecialchars($cta_text); ?></p>
          <a class="cta-btn"
            href="<?php echo htmlspecialchars($cta_button_link); ?>"><?php echo htmlspecialchars($cta_button_text); ?></a>
        </div>
      </div>
    </div>
  </div>

</section><!-- /Call To Action Section -->