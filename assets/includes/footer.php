<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the footer of the website.
include_once __DIR__ . '/settings/public_settings.php';
?>
</main>
 
<?php // Inject brand colors as CSS custom properties for dynamic branding ?>
<style>
  :root {
    --accent-color:
      <?php echo isset($brand_primary_color) ? $brand_primary_color : '#6c2eb6'; ?>
    ;
    --heading-color:
      <?php echo isset($brand_secondary_color) ? $brand_secondary_color : '#28a745'; ?>
    ;
    /* You can add more custom properties here as you add more color controls */
  }

  .accent-background {
    --background-color: var(--accent-color);
    --default-color: #ffffff;
    --heading-color: #ffffff;
    --surface-color: #469fdf;
    --contrast-color: #ffffff;
  }
</style>
<footer id="footer" class="footer light-background">

  <div class="container footer-top">
    <div class="row gy-4">
      <div class="col-lg-5 col-md-12 footer-about d-flex flex-row align-items-start px-4" style="gap:32px;">
        <div class="d-flex flex-column justify-content-start align-items-start w-100">
          <a href="index.php" class="logo mb-1" style="text-decoration:none;">
            <span class="sitename" style="font-size:1.15rem; font-weight:600; color:var(--accent-color); letter-spacing:1px;">
              <?php echo htmlspecialchars($business_name); ?>
            </span>
          </a>
          <br>
          <div class="social-links d-flex gap-3 mb-2" style="margin-top:0;">
            <a href="<?php echo htmlspecialchars($social_links['twitter'] ?? '#'); ?>" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
            <a href="<?php echo htmlspecialchars($social_links['facebook'] ?? '#'); ?>" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="<?php echo htmlspecialchars($social_links['instagram'] ?? '#'); ?>" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="<?php echo htmlspecialchars($social_links['linkedin'] ?? '#'); ?>" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
          </div>
          <br>
          <p class="mb-0 text-start" style="max-width:420px; color:#444; font-size:1.08rem; line-height:1.6; font-weight:400;">
            <?php echo htmlspecialchars($footer_about_text ?? 'Welcome to our site!'); ?>
          </p>
        </div>
      </div>

      <div class="col-lg-2 col-6 footer-links d-flex flex-column align-items-center px-2">
        <h4 class="mb-3 text-center" style="font-size:1.15rem; font-weight:600; color:var(--accent-color);">Useful Links</h4>
        <div class="useful-links-card w-100">
          <div class="links-card p-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 8px; border-left: 4px solid var(--accent-color); transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" 
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" 
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">
            <?php 
            $link_icons = [
              'Reviews' => 'fas fa-star',
              'FAQs' => 'fas fa-question-circle',
              'Terms of Service' => 'fas fa-file-contract',
              'Privacy Policy' => 'fas fa-shield-alt'
            ];
            $link_index = 0;
            foreach ($footer_links as $label => $url): 
              $icon = $link_icons[$label] ?? 'fas fa-link';
            ?>
              <div class="link-list-item d-flex align-items-center <?php echo $link_index < count($footer_links) - 1 ? 'mb-2' : ''; ?>" style="padding: 4px 0;">
                <i class="<?php echo $icon; ?>" style="color: var(--accent-color); font-size: 1rem; margin-right: 8px; width: 16px;"></i>
                <a href="<?php echo htmlspecialchars($url); ?>" style="text-decoration:none; color:#333; font-weight:500; font-size:0.85rem; transition: color 0.3s ease;">
                  <?php echo htmlspecialchars($label); ?>
                </a>
              </div>
            <?php 
              $link_index++;
            endforeach; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-2 col-6 footer-links d-flex flex-column align-items-center px-2">
        <h4 class="mb-3 text-center" style="font-size:1.15rem; font-weight:600; color:var(--accent-color);">Our Services</h4>
        <div class="services-card w-100">
          <div class="service-card p-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 8px; border-left: 4px solid var(--accent-color); transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" 
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" 
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">
            <?php 
            $service_icons = [
              'Web Development' => 'fas fa-code',
              'UI/UX Design' => 'fas fa-paint-brush', 
              'SEO & Marketing' => 'fas fa-chart-line',
              'Hosting & Security' => 'fas fa-shield-alt'
            ];
            foreach ($services as $index => $service): 
              $icon = $service_icons[$service['title']] ?? 'fas fa-cog';
            ?>
              <div class="service-list-item d-flex align-items-center <?php echo $index < count($services) - 1 ? 'mb-2' : ''; ?>" style="padding: 4px 0;">
                <i class="<?php echo $icon; ?>" style="color: var(--accent-color); font-size: 1rem; margin-right: 8px; width: 16px;"></i>
                <?php if (!empty($service['url'])): ?>
                  <a href="<?php echo htmlspecialchars($service['url']); ?>" style="text-decoration:none; color:#333; font-weight:500; font-size:0.85rem; transition: color 0.3s ease;">
                    <?php echo htmlspecialchars($service['title']); ?>
                  </a>
                <?php else: ?>
                  <span style="color:#333; font-weight:500; font-size:0.85rem;"><?php echo htmlspecialchars($service['title']); ?></span>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-3 col-md-12 footer-contact d-flex flex-column align-items-center px-2">
        <h4 class="mb-3 text-center" style="font-size:1.15rem; font-weight:600; color:var(--accent-color);"><?php echo htmlspecialchars($footer_contact_title ?? 'Contact Us'); ?></h4>
        <div class="contact-us-card w-100">
          <div class="contact-card p-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 8px; border-left: 4px solid var(--accent-color); transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" 
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" 
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)'">
            <div class="contact-item d-flex align-items-center mb-2" style="padding: 4px 0;">
              <i class="fas fa-map-marker-alt" style="color: var(--accent-color); font-size: 1rem; margin-right: 8px; width: 16px;"></i>
              <span style="color:#333; font-weight:500; font-size:0.85rem; line-height: 1.2;">
                <?php echo htmlspecialchars($contact_address ?? ''); ?><br>
                <?php echo htmlspecialchars($contact_city ?? ''); ?> <?php echo htmlspecialchars($contact_state ?? ''); ?> <?php echo htmlspecialchars($contact_zipcode ?? ''); ?>
              </span>
            </div>
            <div class="contact-item d-flex align-items-center mb-2" style="padding: 4px 0;">
              <i class="fas fa-phone" style="color: var(--accent-color); font-size: 1rem; margin-right: 8px; width: 16px;"></i>
              <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $contact_phone ?? ''); ?>" style="text-decoration:none; color:#333; font-weight:500; font-size:0.85rem; transition: color 0.3s ease;">
                <?php echo htmlspecialchars($contact_phone ?? ''); ?>
              </a>
            </div>
            <div class="contact-item d-flex align-items-center" style="padding: 4px 0;">
              <i class="fas fa-envelope" style="color: var(--accent-color); font-size: 1rem; margin-right: 8px; width: 16px;"></i>
              <a href="mailto:<?php echo htmlspecialchars($contact_email ?? ''); ?>" style="text-decoration:none; color:#333; font-weight:500; font-size:0.85rem; transition: color 0.3s ease; word-break: break-all;">
                <?php echo htmlspecialchars($contact_email ?? ''); ?>
              </a>
            </div>
            <div class="social-media-section mt-3 pt-2" style="border-top: 1px solid #dee2e6;">
              <div class="d-flex justify-content-center align-items-center gap-2">
                <a href="<?php echo htmlspecialchars($social_links['twitter'] ?? '#'); ?>" aria-label="Twitter" style="color: var(--accent-color); font-size: 1.3rem; transition: color 0.3s ease, transform 0.2s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                  <i class="bi bi-twitter-x"></i>
                </a>
                <a href="<?php echo htmlspecialchars($social_links['facebook'] ?? '#'); ?>" aria-label="Facebook" style="color: var(--accent-color); font-size: 1.3rem; transition: color 0.3s ease, transform 0.2s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                  <i class="bi bi-facebook"></i>
                </a>
                <a href="<?php echo htmlspecialchars($social_links['instagram'] ?? '#'); ?>" aria-label="Instagram" style="color: var(--accent-color); font-size: 1.3rem; transition: color 0.3s ease, transform 0.2s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                  <i class="bi bi-instagram"></i>
                </a>
                <a href="<?php echo htmlspecialchars($social_links['linkedin'] ?? '#'); ?>" aria-label="LinkedIn" style="color: var(--accent-color); font-size: 1.3rem; transition: color 0.3s ease, transform 0.2s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                  <i class="bi bi-linkedin"></i>
                </a>
              </div>
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

  <div class="w-100 py-3" style="background: var(--accent-color); color: #fff;">
    <div class="container text-center">
      <p class="mb-1">
        &copy; Copyright <?php echo htmlspecialchars($footer_copyright_year ?? '2025'); ?> - <?php echo date('Y'); ?>
        <?php echo htmlspecialchars($footer_copyright_site ?? $business_name); ?> All Rights Reserved
      </p>
      <p class="mb-0">
        <style>
          .footer-design-link {
            color: #fff;
            text-decoration: none;
            font-weight: inherit;
          }
          .footer-design-link:hover,
          .footer-design-link:focus {
            text-decoration: underline;
          }
        </style>
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
<style>
  /* Enhanced Footer Services Styling - Single Card */
  .service-card {
    position: relative;
    overflow: hidden;
  }
  
  .service-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 100%;
    background: linear-gradient(135deg, var(--accent-color, #6c2eb6) 0%, var(--brand-secondary, #bf5512) 100%);
    opacity: 0.05;
    transition: width 0.3s ease;
    border-radius: 8px;
  }
  
  .service-card:hover::before {
    width: 100%;
  }
  
  .service-card:hover {
    border-left-color: var(--brand-secondary, #bf5512) !important;
  }
  
  .service-list-item span,
  .service-list-item a {
    transition: color 0.3s ease;
  }
  
  .service-card:hover .service-list-item a {
    color: var(--accent-color) !important;
  }
  
  .service-card:hover .service-list-item span {
    color: var(--accent-color) !important;
  }
  
  .service-card:hover .service-list-item i {
    color: var(--brand-secondary, #bf5512) !important;
  }
  
  /* Mobile responsiveness for services */
  @media (max-width: 768px) {
    .service-card {
      padding: 0.75rem !important;
    }
    
    .service-list-item span,
    .service-list-item a {
      font-size: 0.8rem !important;
    }
    
    .service-list-item i {
      font-size: 0.9rem !important;
    }
  }

  #scroll-top {
    position: fixed;
    right: 32px;
    bottom: 32px;
    width: 48px;
    height: 48px;
    background: var(--heading-color);
    color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    text-decoration: none;
    transition: background 0.2s;
  }
  #scroll-top:hover {
    background: #6c2eb6;
    color: #fff;
  }
</style>
<a href="#" id="scroll-top" class="scroll-top"><i class="bi bi-arrow-up-short"></i></a>

<!-- Preloader -->
<div id="preloader"></div>

<!-- Vendor JS Files -->
<script src="assets/vendor/php-email-form/validate.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
<script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<!-- Main JS File -->
<script src="assets/js/main.js"></script>

<?php
// End output buffering and display highlighted content (if in development mode)
if (isset($dev_mode) && $dev_mode && ob_get_level() > 0) {
    ob_end_flush();
}
?>

</body>

</html>