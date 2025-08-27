<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the Contact section of the website.

// Contact data should be loaded from database_settings.php via doctype.php
// Fallback for missing contact data
if (!isset($contact_title)) {
  $contact_title = 'Contact Us';
}
if (!isset($contact_paragraph)) {
  $contact_paragraph = 'Get in touch with us today.';
}

// Generate CSRF token for form security
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!-- Contact Section -->
<section id="contact" class="contact section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2><?php echo htmlspecialchars($contact_title ?? 'Contact'); ?></h2>
    <div class="section-header-box">
      <p><?php echo $contact_paragraph ?? 'We look forward to serving you!'; ?></p>
    </div>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="mb-4" data-aos="fade-up" data-aos-delay="200">
      <?php if (!empty($contact_map_embed)): ?>
        <div class="contact-map-embed">
          <?php echo $contact_map_embed; ?>
        </div>
      <?php endif; ?>
    </div><!-- End Google Maps -->

    <div class="row gy-4">

      <div class="col-lg-4">
        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
          <i class="bi bi-geo-alt flex-shrink-0"></i>
          <div>
            <h3>Address</h3>
            <p><?php echo htmlspecialchars($contact_address ?? ''); ?><br>
            <?php echo htmlspecialchars($contact_city ?? ''); ?> <?php echo htmlspecialchars($contact_state ?? ''); ?> <?php echo htmlspecialchars($contact_zipcode ?? ''); ?></p>
          </div>
        </div><!-- End Info Item -->

        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
          <i class="bi bi-telephone flex-shrink-0"></i>
          <div>
            <h3>Call Us</h3>
            <p><?php echo htmlspecialchars($contact_phone); ?></p>
          </div>
        </div><!-- End Info Item -->

        <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="500">
          <i class="bi bi-envelope flex-shrink-0"></i>
          <div>
            <h3>Email Us</h3>
            <p><?php echo htmlspecialchars($contact_email); ?></p>
          </div>
        </div><!-- End Info Item -->

      </div>

      <div class="col-lg-8">
        <form action="forms/contact-secure.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
          <!-- CSRF Protection -->
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
          
          <!-- Honeypot Fields (hidden from users, visible to bots) -->
          <div style="position: absolute; left: -9999px; visibility: hidden;" aria-hidden="true">
            <label for="website">Website (leave blank):</label>
            <input type="text" id="website" name="website" value="" autocomplete="off">
            <label for="phone_number">Phone (leave blank):</label>
            <input type="text" id="phone_number" name="phone_number" value="" autocomplete="off">
          </div>
          
          <div class="row gy-4">
            <div class="col-md-6">
              <label for="contact_name" class="form-label"><?php echo htmlspecialchars($contact_form_labels['name'] ?? 'Your Name'); ?> <span aria-hidden="true"
                  style="color:red;">*</span></label>
              <input type="text" id="contact_name" name="name" class="form-control" autocomplete="name"
                placeholder="<?php echo htmlspecialchars($contact_form_labels['name'] ?? 'Your Name'); ?>" required aria-required="true" style="border:2px solid #555;" maxlength="100">
            </div>

            <div class="col-md-6">
              <label for="contact_email" class="form-label"><?php echo htmlspecialchars($contact_form_labels['email'] ?? 'Your Email'); ?> <span aria-hidden="true"
                  style="color:red;">*</span></label>
              <input type="email" id="contact_email" class="form-control" name="email" autocomplete="email"
                placeholder="<?php echo htmlspecialchars($contact_form_labels['email'] ?? 'Your Email'); ?>" required aria-required="true" style="border:2px solid #555;" maxlength="254">
            </div>

            <div class="col-md-12">
              <label for="contact_subject" class="form-label"><?php echo htmlspecialchars($contact_form_labels['subject'] ?? 'Subject'); ?> <span aria-hidden="true"
                  style="color:red;">*</span></label>
              <input type="text" id="contact_subject" class="form-control" name="subject" autocomplete="off"
                placeholder="<?php echo htmlspecialchars($contact_form_labels['subject'] ?? 'Subject'); ?>" required aria-required="true" style="border:2px solid #555;" maxlength="200">
            </div>

            <div class="col-md-12">
              <label for="contact_message" class="form-label"><?php echo htmlspecialchars($contact_form_labels['message'] ?? 'Message'); ?> <span aria-hidden="true"
                  style="color:red;">*</span></label>
              <textarea id="contact_message" class="form-control" name="message" rows="6" autocomplete="off"
                placeholder="<?php echo htmlspecialchars($contact_form_labels['message'] ?? 'Message'); ?>" required aria-required="true" style="border:2px solid #555;" maxlength="2000"></textarea>
            </div>

            <div class="col-md-12 text-center">
              <div class="loading" role="status" aria-live="polite"><?php echo htmlspecialchars($contact_form_labels['loading'] ?? 'Loading'); ?></div>
              <div class="error-message" role="alert" aria-live="assertive"></div>
              <div class="sent-message" role="status" aria-live="polite"><?php echo htmlspecialchars($contact_form_labels['success'] ?? 'Your message has been sent. Thank you!'); ?></div>
              <button type="submit" class="btn btn-primary" aria-label="Send Message"><?php echo htmlspecialchars($contact_form_labels['send_button'] ?? 'Send Message'); ?></button>
            </div>
          </div>
        </form>
      </div><!-- End Contact Form -->
    </div>

  </div>

</section><!-- /Contact Section -->