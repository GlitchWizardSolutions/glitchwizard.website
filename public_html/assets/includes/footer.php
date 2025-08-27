<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the footer of the website.

// Footer data should be loaded from database_settings.php via doctype.php
// Fallback for missing brand data
if (!isset($brand_primary_color)) {
  $brand_primary_color = '#6c2eb6';
}
if (!isset($brand_secondary_color)) {
  $brand_secondary_color = '#28a745';
}
?>
</main>

<footer id="footer" class="footer light-background">


  <div class="container footer-top">
    <!-- Logo, Name, Welcome -->
    <div class="row justify-content-center mb-4">
      <div class="col-lg-8 text-center">
        <?php
        // Determine which business name to use
        $display_business_name = match($footer_business_name_type ?? 'medium') {
          'short' => $business_name_short ?? $business_name ?? 'Business Name',
          'long' => $business_name_long ?? $business_name ?? 'Business Name',
          'medium' => $business_name_medium ?? $business_name ?? 'Business Name',
          default => $business_name_medium ?? $business_name ?? 'Business Name'
        };
        // Logo path construction
        $logo_path = '';
        if ($footer_logo_enabled && !empty($footer_logo_file)) {
          $logo_path = '/assets/branding/' . $footer_logo_file;
          if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/gws-universal-hybrid-app/public_html' . $logo_path)) {
            $logo_path = '';
          }
        }
        ?>
        <?php if ($logo_path): ?>
          <div class="footer-logo mb-2">
            <img src="<?php echo $logo_path; ?>" alt="Logo" style="height:56px;">
          </div>
        <?php endif; ?>
        <div class="sitename">
          <?php echo htmlspecialchars($display_business_name); ?>
        </div>
        <p class="footer-welcome mt-2 mb-0">
          <?php echo htmlspecialchars($footer_about_text ?? 'Welcome to our site!'); ?>
        </p>
      </div>
    </div>
    <!-- Cards Row -->
    <div class="row justify-content-center gy-4">
      <div class="col-md-3">
        <!-- Useful Links card -->
        <h4 class="mb-3 text-center">Useful Links</h4>
        <div class="useful-links-card w-100">
          <div class="links-card">
            <?php 
            $link_icons = [
              'Reviews' => 'bi-star',
              'FAQs' => 'bi-question-circle',
              'Terms of Service' => 'bi-file-text',
              'Privacy Policy' => 'bi-shield-check'
            ];
            $link_index = 0;
            foreach ($footer_links as $label => $url): 
              $icon = $link_icons[$label] ?? 'bi-link-45deg';
            ?>
              <div class="link-list-item <?php echo $link_index < count($footer_links) - 1 ? 'mb-2' : ''; ?>">
                <i class="bi <?php echo $icon; ?>"></i>
                <a href="<?php echo htmlspecialchars($url); ?>">
                  <?php echo htmlspecialchars($label); ?>
                </a>
              </div>
            <?php 
              $link_index++;
            endforeach; ?>
          </div>
        </div>
      </div>
      <div class="col-md-5">
        <!-- Our Services card -->
        <h4 class="mb-3 text-center">Our Services</h4>
        <div class="services-card">
          <div class="service-card">
            <?php 
            $service_icons = [
              'bi-activity',
              'bi-broadcast', 
              'bi-easel',
              'bi-bounding-box-circles',
              'bi-calendar4-week',
              'bi-bounding-box-circles'
            ];
            foreach ($services as $index => $service): 
              $icon = $service_icons[$index % count($service_icons)];
            ?>
              <div class="service-list-item <?php echo $index < count($services) - 1 ? 'mb-2' : ''; ?>">
                <i class="bi <?php echo $icon; ?>"></i>
                <?php if (!empty($service['url'])): ?>
                  <a href="<?php echo htmlspecialchars($service['url']); ?>">
                    <?php echo htmlspecialchars($service['title']); ?>
                  </a>
                <?php else: ?>
                  <span><?php echo htmlspecialchars($service['title']); ?></span>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <!-- Contact Us card -->
        <h4 class="mb-3 text-center"><?php echo htmlspecialchars($footer_contact_title ?? 'Contact Us'); ?></h4>
        <div class="contact-us-card w-100">
          <div class="contact-card">
            <div class="contact-item mb-2">
              <i class="bi bi-geo-alt"></i>
              <span>
                <?php 
                // Use separate address fields for better control
                $address_parts = [];
                
                // Check if we should show full address or just city/state/zip
                $show_full_address = $show_full_address ?? true;
                
                if ($show_full_address && !empty($contact_street_address)) {
                  $address_parts[] = $contact_street_address;
                }
                
                // Always show city, state, zip if available
                $city_state_zip = '';
                if (!empty($contact_city)) {
                  $city_state_zip = $contact_city;
                  if (!empty($contact_state)) {
                    $city_state_zip .= ', ' . $contact_state;
                  }
                  if (!empty($contact_zipcode)) {
                    $city_state_zip .= ' ' . $contact_zipcode;
                  }
                }
                
                if (!empty($city_state_zip)) {
                  $address_parts[] = $city_state_zip;
                }
                
                // Fallback to old combined address if no separate fields
                if (empty($address_parts) && !empty($contact_address)) {
                  $address_parts[] = $contact_address;
                }
                
                if (!empty($address_parts)) {
                  echo implode('<br>', array_map('htmlspecialchars', $address_parts));
                } else {
                  echo 'Address not available';
                }
                ?>
              </span>
            </div>
            <div class="contact-item mb-2">
              <i class="bi bi-telephone"></i>
              <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $contact_phone ?? ''); ?>">
                <?php echo htmlspecialchars($contact_phone ?? ''); ?>
              </a>
            </div>
            <div class="contact-item">
              <i class="bi bi-envelope"></i>
              <a href="mailto:<?php echo htmlspecialchars($contact_email ?? ''); ?>" style="word-break: break-all;">
                <?php echo htmlspecialchars($contact_email ?? ''); ?>
              </a>
            </div>
            <div class="social-media-section">
              <div class="d-flex justify-content-center align-items-center gap-2">
                <a href="<?php echo htmlspecialchars($social_links['twitter'] ?? '#'); ?>" aria-label="Twitter">
                  <i class="bi bi-twitter-x"></i>
                </a>
                <a href="<?php echo htmlspecialchars($social_links['facebook'] ?? '#'); ?>" aria-label="Facebook">
                  <i class="bi bi-facebook"></i>
                </a>
                <a href="<?php echo htmlspecialchars($social_links['instagram'] ?? '#'); ?>" aria-label="Instagram">
                  <i class="bi bi-instagram"></i>
                </a>
                <a href="<?php echo htmlspecialchars($social_links['linkedin'] ?? '#'); ?>" aria-label="LinkedIn">
                  <i class="bi bi-linkedin"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Horizontal links row -->

  <div class="container mb-2" style="margin-top: 2rem;">
    <div class="row">
      <div class="col text-center">
        <div>
          <p class="btn btn-primary" style="background:white"><a href="rss" target="_blank"><i class="fas fa-rss-square"></i> RSS Feed</a></p> 
          <p class="btn btn-primary" style="background:white"><a href="sitemap" target="_blank"><i class="fas fa-sitemap"></i> XML Sitemap</a></p>          
          <p class="btn btn-primary" style="background:white"><a href="<?php echo htmlspecialchars($footer_special_links['accessibility_policy']); ?>" target="_blank"><i class="fas fa-universal-access"></i> Accessibility Policy</a></p>
          <p class="btn btn-primary" style="background:white"><a href="<?php echo htmlspecialchars($footer_special_links['terms_of_service']); ?>" target="_blank"><i class="fas fa-file-contract"></i> Terms of Service</a></p>
          <p class="btn btn-primary" style="background:white"><a href="<?php echo htmlspecialchars($footer_special_links['privacy_policy']); ?>" target="_blank"><i class="fas fa-user-shield"></i> Privacy Policy</a></p><br>
        </div>
      </div>
    </div>
  </div>

  <div class="w-100 footer-copyright">
    <div class="container text-center">
      <p class="mb-1">
        &copy; Copyright <?php echo htmlspecialchars($footer_copyright_year ?? '2025'); ?> - <?php echo date('Y'); ?>
        <?php echo htmlspecialchars($footer_copyright_site ?? $business_name); ?> All Rights Reserved
      </p>
      <p class="mb-0">
        <?php if (!empty($footer_design_by_text) && !empty($footer_design_by_url)): ?>
          <a href="<?php echo htmlspecialchars($footer_design_by_url); ?>" target="_blank" rel="noopener"
            aria-label="<?php echo htmlspecialchars($footer_design_by_text); ?>" class="footer-design-link">
            <?php echo htmlspecialchars($footer_design_by_text); ?>
          </a>
        <?php else: ?>
          <a href="https://gltchwizardsolutions.com" target="_blank" rel="noopener"
            aria-label="Designed by: GlitchWizard Solutions. LLC" class="footer-design-link">Designed by: GlitchWizard
            Solutions. LLC</a>
        <?php endif; ?>
      </p>
    </div>
  </div>

</footer>

<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top"><i class="bi bi-arrow-up-short"></i></a>

<!-- Preloader -->
<div id="preloader"></div>

<!-- Vendor JS Files -->
<script src="<?php echo PUBLIC_ASSETS_URL; ?>/vendor/php-email-form/validate.js"></script>
<script src="<?php echo PUBLIC_ASSETS_URL; ?>/vendor/aos/aos.js"></script>
<script src="<?php echo PUBLIC_ASSETS_URL; ?>/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="<?php echo PUBLIC_ASSETS_URL; ?>/vendor/glightbox/js/glightbox.min.js"></script>
<script src="<?php echo PUBLIC_ASSETS_URL; ?>/vendor/swiper/swiper-bundle.min.js"></script>
<script src="<?php echo PUBLIC_ASSETS_URL; ?>/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
<script src="<?php echo PUBLIC_ASSETS_URL; ?>/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<!-- Main JS File -->
<script src="<?php echo PUBLIC_ASSETS_URL; ?>/js/main.js"></script>

<?php
// End output buffering and display highlighted content (if in development mode)
if (isset($dev_mode) && $dev_mode && ob_get_level() > 0) {
    ob_end_flush();
}
?>

</body>

</html>