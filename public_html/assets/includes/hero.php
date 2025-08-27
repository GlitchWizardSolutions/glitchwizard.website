<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the Hero section of the website.

// Safe defaults for hero_content with debug string detection
if (!isset($hero_content) || !is_array($hero_content) || 
    (isset($hero_content['headline']) && strpos($hero_content['headline'], '[DB:') === 0)) {
  $hero_content = [
    'headline' => 'Welcome to Our Site',
    'subheadline' => 'Your trusted partner for digital solutions.',
    'bg_image' => ($hero_content['bg_image'] ?? '') // preserve any loaded image value
  ];
}

// Only assign bg image if set and non-empty
$hero_bg_image = (!empty($hero_content['bg_image'])) ? $hero_content['bg_image'] : '';
$hero_alt = 'Hero Background';

// Generate CSRF token for form security
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<!-- Hero Section -->
<section id="hero" class="hero section" style="position: relative; height: 100vh; overflow: hidden;">

  <?php if ($hero_bg_image !== ''): ?>
    <img src="<?= htmlspecialchars($hero_bg_image) ?>"
      alt="<?= htmlspecialchars($hero_alt) ?>" data-aos="fade-in" class="hero-bg"
      style="width:100%;height:100vh;object-fit:cover;position:absolute;top:0;left:0;z-index:1;">
  <?php endif; ?>

  <div class="container" style="position: relative; z-index: 2; height: 100vh; display: flex; flex-direction: column; padding-top: 80px;">
    <!-- Hero Text - Moved Higher -->
    <div class="row justify-content-center" data-aos="zoom-out" style="margin-top: 4vh; flex-shrink: 0;">
      <div class="col-xl-7 col-lg-9 text-center">
        <h1><?= htmlspecialchars($hero_content['headline']) ?></h1>
        <p><?= htmlspecialchars($hero_content['subheadline']) ?></p>
      </div>
    </div>
    
    <!-- Spacer to push form down -->
    <div style="flex: 1;"></div>
    
    <!-- Hero Form - Positioned Over Image -->
    <div class="text-center" data-aos="zoom-out" data-aos-delay="100" style="margin-bottom: 4vh;">
      <div class="hero-inline-form" style="background: linear-gradient(135deg, #008B8B 0%, #20B2AA 100%); padding: 0.8rem 1.2rem; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.3); width: 100%; margin-left: 0; margin-right: 0;">

        <!-- Subtitle Text Above Form -->
        <div class="hero-form-subtitle mb-2" style="text-align: center;">
          <p style="color: white; font-size: clamp(0.7rem, 1.4vw, 0.85rem); margin: 0; line-height: 1.2; font-weight: 400; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
            <?= htmlspecialchars($hero_form_top_text ?? 'No obligation. No spam. Just a real offer from your local neighbor.') ?>
          </p>
        </div>

        <form action="forms/hero-offer-form.php" method="post" class="d-flex flex-wrap align-items-center justify-content-center gap-3">
          
          <!-- CSRF Protection -->
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
          
          <!-- Honeypot Fields (hidden) -->
          <div style="position: absolute; left: -9999px; visibility: hidden;" aria-hidden="true">
            <input type="text" name="website" value="" autocomplete="off" tabindex="-1">
            <input type="text" name="email_check" value="" autocomplete="off" tabindex="-1">
          </div>

          <!-- GET STARTED Text on Same Line -->
          <div class="hero-form-header" style="flex-shrink: 0; margin-right: 1rem;">
            <h3 style="color: white; font-weight: 800; font-size: clamp(1rem, 2.2vw, 1.3rem); margin: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); white-space: nowrap;">
              <?= htmlspecialchars($hero_form_side_text ?? 'GET STARTED!') ?>
            </h3>
          </div>

          <!-- Compact Input Fields Row -->
          <div class="hero-form-inputs d-flex flex-wrap gap-2 justify-content-center" style="flex: 1; max-width: 100%;">
            
            <input type="text" 
                   name="name" 
                   class="form-control" 
                   placeholder="Your Name" 
                   required 
                   aria-label="Your Name"
                   autocomplete="name"
                   style="flex: 0 1 160px; min-width: 140px; max-width: 180px; padding: 0.5rem 0.7rem; border: 2px solid white; border-radius: 8px; font-size: 0.9rem; font-weight: 500; color: #2c3e50; height: 38px;" 
                   maxlength="100">
            
            <input type="tel" 
                   name="phone" 
                   class="form-control" 
                   placeholder="Phone Number" 
                   required 
                   aria-label="Phone Number"
                   autocomplete="tel"
                   style="flex: 0 1 160px; min-width: 140px; max-width: 180px; padding: 0.5rem 0.7rem; border: 2px solid white; border-radius: 8px; font-size: 0.9rem; font-weight: 500; color: #2c3e50; height: 38px;" 
                   maxlength="20">
            
            <input type="text" 
                   name="address" 
                   class="form-control" 
                   placeholder="Your Address" 
                   required 
                   aria-label="Your Address"
                   autocomplete="address-line1"
                   style="flex: 1; min-width: 220px; max-width: 350px; padding: 0.5rem 0.7rem; border: 2px solid white; border-radius: 8px; font-size: 0.9rem; font-weight: 500; color: #2c3e50; height: 38px;" 
                   maxlength="200">

            <!-- Compact Submit Button -->
            <button type="submit" 
                    class="btn btn-warning d-flex align-items-center justify-content-center" 
                    style="background: #FFD700; color: #2c3e50; border: 2px solid #FFA500; font-weight: 700; font-size: 0.9rem; padding: 0.4rem 1.2rem; border-radius: 12px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 3px 8px rgba(255,215,0,0.4); transition: all 0.3s ease; min-width: 160px; white-space: nowrap; flex: 0 0 auto; height: 38px;"
                    aria-label="<?= htmlspecialchars($hero_form_button_text ?? 'Get My Offer') ?>"
                    onmouseover="this.style.background='#FFA500'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(255,165,0,0.5)';"
                    onmouseout="this.style.background='#FFD700'; this.style.transform='translateY(0)'; this.style.boxShadow='0 3px 8px rgba(255,215,0,0.4)';">
              <?= htmlspecialchars($hero_form_button_text ?? 'Get My Offer') ?>
            </button>
          </div>

        </form>

        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success']) && $_GET['success'] === 'offer_submitted'): ?>
          <div class="alert alert-success mt-3 mb-0" style="background: rgba(40, 167, 69, 0.9); color: white; border: none; border-radius: 6px; font-size: 0.9rem; padding: 0.5rem 1rem;">
            <i class="bi bi-check-circle"></i> Thank you! We'll contact you soon.
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger mt-3 mb-0" style="background: rgba(220, 53, 69, 0.9); color: white; border: none; border-radius: 6px; font-size: 0.9rem; padding: 0.5rem 1rem;">
            <i class="bi bi-exclamation-triangle"></i> 
            <?php 
            $error = $_GET['error'];
            if ($error === 'email_failed') {
              echo 'Sorry, there was a problem. Please try again.';
            } elseif ($error === 'security') {
              echo 'Please refresh and try again.';
            } else {
              echo htmlspecialchars($error);
            }
            ?>
          </div>
        <?php endif; ?>

      </div>
    </div>

    <!-- You can add dynamic hero icon boxes here using settings if needed -->
  </div>

</section><!-- /Hero Section -->