<?php
// This file is part of the GWS Universal Hybrid App project.
// Enhanced Contact Form with Database Integration
// It is included in the main index.php file to render the Contact section of the website.

// Contact data should be loaded from database_settings.php via doctype.php

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
        <form action="forms/contact-database.php" method="post" class="php-email-form enhanced-contact-form" data-aos="fade-up" data-aos-delay="200" id="contactForm">
          <!-- CSRF Protection -->
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
          
          <!-- Honeypot Fields (hidden from users, visible to bots) -->
          <div style="position: absolute; left: -9999px; visibility: hidden;" aria-hidden="true">
            <label for="website">Website (leave blank):</label>
            <input type="text" id="website" name="website" value="" autocomplete="off" tabindex="-1">
            <label for="phone_number">Phone (leave blank):</label>
            <input type="text" id="phone_number" name="phone_number" value="" autocomplete="off" tabindex="-1">
          </div>
          
          <div class="row gy-4">
            <div class="col-md-6">
              <label for="contact_first_name" class="form-label">First Name <span aria-hidden="true" style="color:red;">*</span></label>
              <input type="text" id="contact_first_name" name="first_name" class="form-control" 
                autocomplete="given-name" placeholder="Your First Name" required aria-required="true" 
                style="border:2px solid #555;" maxlength="50" pattern="^[a-zA-Z\s]+$" 
                title="First name must contain only letters and spaces">
            </div>

            <div class="col-md-6">
              <label for="contact_last_name" class="form-label">Last Name <span aria-hidden="true" style="color:red;">*</span></label>
              <input type="text" id="contact_last_name" name="last_name" class="form-control" 
                autocomplete="family-name" placeholder="Your Last Name" required aria-required="true" 
                style="border:2px solid #555;" maxlength="50" pattern="^[a-zA-Z\s]+$" 
                title="Last name must contain only letters and spaces">
            </div>

            <div class="col-md-12">
              <label for="contact_email" class="form-label">Your Email <span aria-hidden="true" style="color:red;">*</span></label>
              <input type="email" id="contact_email" class="form-control" name="email" autocomplete="email"
                placeholder="Your Email" required aria-required="true" style="border:2px solid #555;" maxlength="254">
            </div>

            <div class="col-md-12">
              <label for="contact_category" class="form-label">Category <span aria-hidden="true" style="color:red;">*</span></label>
              <select id="contact_category" name="category" class="form-select" required aria-required="true" 
                style="border:2px solid #555;">
                <option value="" disabled selected>Choose a category</option>
                <option value="general">General Inquiry</option>
                <option value="technical">Technical Support</option>
                <option value="business">Business Inquiry</option>
                <option value="feedback">Feedback</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div class="col-md-12">
              <label for="contact_subject" class="form-label">Subject <span aria-hidden="true" style="color:red;">*</span></label>
              <input type="text" id="contact_subject" class="form-control" name="subject" autocomplete="off"
                placeholder="Subject" required aria-required="true" style="border:2px solid #555;" maxlength="200">
            </div>

            <div class="col-md-12">
              <label for="contact_message" class="form-label">Message <span aria-hidden="true" style="color:red;">*</span></label>
              <textarea id="contact_message" class="form-control" name="message" rows="6" autocomplete="off"
                placeholder="Your message..." required aria-required="true" style="border:2px solid #555;" 
                maxlength="2000" minlength="10"></textarea>
              <small class="form-text text-muted">Minimum 10 characters required</small>
            </div>

            <div class="col-md-12 text-center">
              <div class="loading" role="status" aria-live="polite" style="display: none;">
                <?php
                // Use shared brand spinner (small) plus accessible text
                if (!function_exists('getBrandSpinnerHTML')) { require_once dirname(__DIR__, 3) . '/private/gws-universal-functions.php'; }
                echo getBrandSpinnerHTML(null, ['size'=>'sm','label'=>'Sending','class'=>'me-2 align-text-bottom']);
                ?>
                <span class="loading-text">Sending...</span>
              </div>
              <div class="error-message" role="alert" aria-live="assertive" style="display: none;"></div>
              <div class="sent-message" role="status" aria-live="polite" style="display: none;">
                <i class="bi bi-check-circle-fill text-success" aria-hidden="true"></i> Your message has been sent. Thank you!
              </div>
              <button type="submit" class="btn btn-primary" aria-label="Send Message">
                <span class="default-label"><i class="bi bi-send-fill" aria-hidden="true"></i> Send Message</span>
              </button>
            </div>
          </div>
        </form>
      </div><!-- End Contact Form -->
    </div>

  </div>

</section><!-- /Contact Section -->

<!-- Enhanced Client-Side Validation and AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Lightweight brand spinner JS helper bootstrap (public context)
  if (!window.BrandSpinner && !document.getElementById('brand-spinner-helper-loaded')) {
    // Inject helper script once (reuse admin/client portal script if path standardized later)
    const script = document.createElement('script');
    script.id = 'brand-spinner-helper-loaded';
    script.src = '/client_portal/assets/js/brand-spinner.js'; // falls back if accessible; adjust path if public copy added
    document.head.appendChild(script);
  }
    const form = document.getElementById('contactForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    const loadingDiv = form.querySelector('.loading');
    const errorDiv = form.querySelector('.error-message');
    const successDiv = form.querySelector('.sent-message');
    let isSubmitting = false;
    
    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (isSubmitting) {
            return false;
        }
        
        // Validate form
        if (!validateForm()) {
            return false;
        }
        
        // Prevent double submission
        isSubmitting = true;
        submitBtn.disabled = true;
        
    // Show loading state (replace button label with inline spinner)
    hideAllMessages();
    loadingDiv.style.display = 'block';
    try {
      if (window.BrandSpinner) {
        BrandSpinner.buttonLoading(submitBtn, {label:'Sending', size:'sm'});
      } else {
        submitBtn.dataset.originalHtml = submitBtn.innerHTML;
        submitBtn.innerHTML = '<?php echo addslashes(getBrandSpinnerHTML(null, ['size'=>'sm','label'=>'Sending','class'=>'me-2 align-text-bottom'])); ?> Sending...';
      }
    } catch(e) {}
        
        // Submit form via AJAX
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            loadingDiv.style.display = 'none';
            
            if (data.success) {
                successDiv.style.display = 'block';
                successDiv.innerHTML = '<i class="bi bi-check-circle-fill text-success" aria-hidden="true"></i> ' + data.message;
                form.reset();
                // Remove validation classes
                form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                    el.classList.remove('is-valid', 'is-invalid');
                });
            } else {
                errorDiv.style.display = 'block';
                errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger" aria-hidden="true"></i> ' + data.message;
                
                // Handle field-specific errors
                if (data.field_errors) {
                    Object.keys(data.field_errors).forEach(fieldName => {
                        const field = form.querySelector(`[name="${fieldName}"]`);
                        if (field) {
                            field.classList.add('is-invalid');
                            // Add error message below field
                            const errorMsg = document.createElement('div');
                            errorMsg.className = 'invalid-feedback';
                            errorMsg.textContent = data.field_errors[fieldName];
                            field.parentNode.appendChild(errorMsg);
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            loadingDiv.style.display = 'none';
            errorDiv.style.display = 'block';
            errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill text-danger" aria-hidden="true"></i> An error occurred. Please try again.';
    })
    .finally(() => {
      isSubmitting = false;
      submitBtn.disabled = false;
      try {
        if (window.BrandSpinner && BrandSpinner.revertButton) {
          BrandSpinner.revertButton(submitBtn);
        } else if (submitBtn.dataset.originalHtml) {
          submitBtn.innerHTML = submitBtn.dataset.originalHtml;
        }
      } catch(e) {}
    });
    });
    
    // Real-time validation
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            // Clear previous validation state on input
            this.classList.remove('is-invalid', 'is-valid');
            const feedback = this.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.remove();
            }
        });
    });
    
    function validateForm() {
        let isValid = true;
        
        // Clear previous validation
        form.querySelectorAll('.is-invalid, .is-valid').forEach(el => {
            el.classList.remove('is-invalid', 'is-valid');
        });
        form.querySelectorAll('.invalid-feedback').forEach(el => {
            el.remove();
        });
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    function validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let errorMessage = '';
        
        // Required field validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'This field is required.';
        }
        
        // Pattern validation
        if (isValid && field.hasAttribute('pattern') && value) {
            const pattern = new RegExp(field.getAttribute('pattern'));
            if (!pattern.test(value)) {
                isValid = false;
                errorMessage = field.getAttribute('title') || 'Invalid format.';
            }
        }
        
        // Email validation
        if (isValid && field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid email address.';
            }
        }
        
        // Length validation
        if (isValid && value) {
            const minLength = field.getAttribute('minlength');
            const maxLength = field.getAttribute('maxlength');
            
            if (minLength && value.length < parseInt(minLength)) {
                isValid = false;
                errorMessage = `Minimum ${minLength} characters required.`;
            }
            
            if (maxLength && value.length > parseInt(maxLength)) {
                isValid = false;
                errorMessage = `Maximum ${maxLength} characters allowed.`;
            }
        }
        
        // Apply validation styling
        if (isValid && value) {
            field.classList.add('is-valid');
            field.classList.remove('is-invalid');
        } else if (!isValid) {
            field.classList.add('is-invalid');
            field.classList.remove('is-valid');
            
            // Add error message
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = errorMessage;
            field.parentNode.appendChild(feedback);
        }
        
        return isValid;
    }
    
    function hideAllMessages() {
        loadingDiv.style.display = 'none';
        errorDiv.style.display = 'none';
        successDiv.style.display = 'none';
    }
});
</script>

<style>
/* Enhanced form validation styles */
.enhanced-contact-form .form-control.is-invalid,
.enhanced-contact-form .form-select.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.enhanced-contact-form .form-control.is-valid,
.enhanced-contact-form .form-select.is-valid {
    border-color: #28a745 !important;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.enhanced-contact-form .invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
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

/* Loading, error, and success message styling */
.loading, .error-message, .sent-message {
    margin: 15px 0;
    padding: 10px;
    border-radius: 4px;
    font-weight: 500;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.sent-message {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.loading {
    background-color: #e2e3e5;
    color: #383d41;
    border: 1px solid #d6d8db;
}
</style>
