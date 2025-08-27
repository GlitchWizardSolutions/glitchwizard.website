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

// reCAPTCHA v3 configuration (optional - set these in your config)
$recaptcha_site_key = ''; // Your reCAPTCHA v3 site key
$enable_recaptcha = !empty($recaptcha_site_key);
?>
<!-- Contact Section -->
<section id="contact" class="contact section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Contact</h2>
    <p>We look forward to serving you!</p>
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
        <form action="forms/contact-secure.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200" id="contactForm">
          <!-- CSRF Protection -->
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
          
          <!-- reCAPTCHA v3 token (if enabled) -->
          <?php if ($enable_recaptcha): ?>
          <input type="hidden" name="recaptcha_token" id="recaptcha_token">
          <?php endif; ?>
          
          <!-- Honeypot Fields (hidden from users, visible to bots) -->
          <div style="position: absolute; left: -9999px; visibility: hidden;" aria-hidden="true">
            <label for="website">Website (leave blank):</label>
            <input type="text" id="website" name="website" value="" autocomplete="off" tabindex="-1">
            <label for="phone_number">Phone (leave blank):</label>
            <input type="text" id="phone_number" name="phone_number" value="" autocomplete="off" tabindex="-1">
          </div>
          
          <div class="row gy-4">
            <div class="col-md-6">
              <label for="contact_name" class="form-label">Your Name <span aria-hidden="true"
                  style="color:red;">*</span></label>
              <input type="text" id="contact_name" name="name" class="form-control" autocomplete="name"
                placeholder="Your Name" required aria-required="true" style="border:2px solid #555;" maxlength="100">
            </div>

            <div class="col-md-6">
              <label for="contact_email" class="form-label">Your Email <span aria-hidden="true"
                  style="color:red;">*</span></label>
              <input type="email" id="contact_email" class="form-control" name="email" autocomplete="email"
                placeholder="Your Email" required aria-required="true" style="border:2px solid #555;" maxlength="254">
            </div>

            <div class="col-md-12">
              <label for="contact_subject" class="form-label">Subject <span aria-hidden="true"
                  style="color:red;">*</span></label>
              <input type="text" id="contact_subject" class="form-control" name="subject" autocomplete="off"
                placeholder="Subject" required aria-required="true" style="border:2px solid #555;" maxlength="200">
            </div>

            <div class="col-md-12">
              <label for="contact_message" class="form-label">Message <span aria-hidden="true"
                  style="color:red;">*</span></label>
              <textarea id="contact_message" class="form-control" name="message" rows="6" autocomplete="off"
                placeholder="Message" required aria-required="true" style="border:2px solid #555;" maxlength="2000"></textarea>
            </div>

            <div class="col-md-12 text-center">
              <div class="loading" role="status" aria-live="polite">Loading</div>
              <div class="error-message" role="alert" aria-live="assertive"></div>
              <div class="sent-message" role="status" aria-live="polite">Your message has been sent. Thank you!</div>
              <button type="submit" class="btn btn-primary" aria-label="Send Message">Send Message</button>
            </div>
          </div>
        </form>
      </div><!-- End Contact Form -->
    </div>

  </div>

</section><!-- /Contact Section -->

<?php if ($enable_recaptcha): ?>
<!-- reCAPTCHA v3 Script (Invisible) -->
<script src="https://www.google.com/recaptcha/api.js?render=<?php echo htmlspecialchars($recaptcha_site_key); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        grecaptcha.ready(function() {
            grecaptcha.execute('<?php echo htmlspecialchars($recaptcha_site_key); ?>', {action: 'contact_form'}).then(function(token) {
                document.getElementById('recaptcha_token').value = token;
                
                // Now submit the form
                submitForm();
            });
        });
    });
    
    function submitForm() {
        // Your existing form submission logic here
        // This integrates with your current AJAX form handler
        form.submit();
    }
});
</script>
<?php endif; ?>

<!-- Enhanced Client-Side Validation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    let isSubmitting = false;
    
    // Prevent double submission
    form.addEventListener('submit', function(e) {
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        
        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        
        // Re-enable after 5 seconds to prevent permanent lock
        setTimeout(function() {
            isSubmitting = false;
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
        }, 5000);
    });
    
    // Client-side validation enhancements
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(function(input) {
        input.addEventListener('input', function() {
            validateField(this);
        });
    });
    
    function validateField(field) {
        const value = field.value.trim();
        
        // Remove any validation styling first
        field.classList.remove('is-invalid', 'is-valid');
        
        if (field.required && value === '') {
            field.classList.add('is-invalid');
            return false;
        }
        
        // Email validation
        if (field.type === 'email' && value !== '') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                field.classList.add('is-invalid');
                return false;
            }
        }
        
        // Length validation
        if (value.length > 0 && value.length < (field.name === 'message' ? 10 : 2)) {
            field.classList.add('is-invalid');
            return false;
        }
        
        field.classList.add('is-valid');
        return true;
    }
});
</script>

<style>
/* Enhanced form validation styles */
.form-control.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-control.is-valid {
    border-color: #28a745 !important;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

/* Hide honeypot fields more thoroughly */
input[name="website"], 
input[name="phone_number"] {
    position: absolute !important;
    left: -9999px !important;
    width: 0 !important;
    height: 0 !important;
    opacity: 0 !important;
    visibility: hidden !important;
    display: none !important;
}
</style>
