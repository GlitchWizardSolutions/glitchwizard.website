<?php
// Unified includes and layout
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
// Note: Settings now loaded via doctype.php from database_settings.php
include_once "assets/includes/settings/image_helper.php";
?>

<!-- Accessibility Policy Section -->
<section id="accessibility-policy" class="about section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Accessibility Policy<br></h2>
    <div class="section-header-box">
      <p>Our commitment to ensuring digital accessibility for all users</p>
    </div>
  </div><!-- End Section Title -->

  <div class="container">
    <div class="row gy-4">
      <div class="col-lg-12 content" data-aos="fade-up" data-aos-delay="100">
        <div class="section-content-box">
                    
                    <div class="mb-4 p-4 bg-light rounded">
                        <p class="mb-1"><strong>Website Owner:</strong> <?php echo htmlspecialchars($business_name); ?></p>
                        <p class="mb-1"><strong>Website Development & Accessibility:</strong> GlitchWizard Solutions, LLC</p>
                        <p class="mb-0"><strong>Last Updated:</strong> <?php echo date('F j, Y'); ?></p>
                    </div>

                    <div class="content">
                        <h2>Our Commitment to Accessibility</h2>
                        <p class="lead">
                            This website for <strong><?php echo htmlspecialchars($business_name); ?></strong> has been developed and is maintained by <strong>GlitchWizard Solutions, LLC</strong> with a firm commitment to ensuring digital accessibility for all users, including individuals with disabilities. We strive to comply with the Web Content Accessibility Guidelines (WCAG) 2.1 Level AA standards to ensure that everyone can access and use this website effectively.
                        </p>

                        <h3>Accessibility Features Implemented</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="bi bi-image text-primary" aria-hidden="true"></i> Alternative Text</h5>
                                <p>We provide descriptive alternative text for all images, ensuring users with screen readers can understand visual content.</p>
                                
                                <h5><i class="bi bi-keyboard text-primary" aria-hidden="true"></i> Keyboard Navigation</h5>
                                <p>The website is fully navigable using keyboard-only commands, supporting users who cannot use a mouse or pointing device.</p>
                                
                                <h5><i class="bi bi-type-h1 text-primary" aria-hidden="true"></i> Clear Structure</h5>
                                <p>We use semantic HTML and proper heading hierarchy to provide clear organization for assistive technologies.</p>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="bi bi-palette text-primary" aria-hidden="true"></i> Color Contrast</h5>
                                <p>We maintain sufficient color contrast ratios between text and background elements to enhance readability.</p>
                                
                                <h5><i class="bi bi-link-45deg text-primary" aria-hidden="true"></i> Descriptive Links</h5>
                                <p>All hyperlinks use meaningful text that clearly describes their purpose and destination.</p>
                                
                                <h5><i class="bi bi-text-paragraph text-primary" aria-hidden="true"></i> Scalable Text</h5>
                                <p>Text can be resized up to 200% without loss of functionality or content.</p>
                            </div>
                        </div>

                        <h3>Ongoing Accessibility Efforts</h3>
                        <div class="row">
                            <div class="col-12">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="bi bi-search text-success" aria-hidden="true"></i> Regular Audits</h5>
                                        <p class="card-text">GlitchWizard Solutions conducts periodic accessibility audits and assessments to identify and address any accessibility barriers on this website.</p>
                                    </div>
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="bi bi-mortarboard text-info" aria-hidden="true"></i> Training & Awareness</h5>
                                        <p class="card-text">Our development team receives ongoing training in web accessibility best practices and techniques to ensure continued compliance.</p>
                                    </div>
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h5 class="card-title"><i class="bi bi-chat-dots text-warning" aria-hidden="true"></i> User Feedback</h5>
                                        <p class="card-text">We actively welcome feedback from users regarding accessibility concerns or suggestions for improvement.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3>Third-Party Content</h3>
                        <p>
                            While GlitchWizard Solutions ensures this website's core accessibility features, some third-party content, plugins, or embedded tools may be beyond our direct control. We make diligent efforts to work with reputable vendors and service providers that prioritize accessibility standards.
                        </p>

                        <h3>Legal Compliance</h3>
                        <p>
                            GlitchWizard Solutions assumes responsibility for ensuring this website complies with applicable accessibility laws and regulations, including:
                        </p>
                        <ul>
                            <li>Americans with Disabilities Act (ADA)</li>
                            <li>Section 508 of the Rehabilitation Act</li>
                            <li>Web Content Accessibility Guidelines (WCAG) 2.1 Level AA</li>
                            <li>Other relevant local and federal accessibility standards</li>
                        </ul>

                        <div class="alert alert-primary mt-5" role="alert">
                            <h4 class="alert-heading"><i class="bi bi-envelope-fill" aria-hidden="true"></i> Need Accessibility Assistance?</h4>
                            <p>
                                If you encounter any accessibility barriers on this website or need assistance accessing any content, please contact <strong>GlitchWizard Solutions</strong> directly. We are committed to providing equal access to all users.
                            </p>
                            <hr>
                            <p class="mb-0">
                                <strong>Email:</strong> <a href="mailto:webdev@glitchwizardsolutions.com?subject=Accessibility%20Request%20-%20<?php echo urlencode($business_name); ?>%20Website" class="alert-link">webdev@glitchwizardsolutions.com</a><br>
                                <strong>Subject Line:</strong> Accessibility Request - <?php echo htmlspecialchars($business_name); ?> Website
                            </p>
                            <p class="mt-3 mb-0">
                                <small class="text-muted">
                                    <strong>Note:</strong> <?php echo htmlspecialchars($business_name); ?> has partnered with GlitchWizard Solutions, LLC for website development and accessibility compliance. All accessibility-related inquiries should be directed to GlitchWizard Solutions for prompt resolution.
                                </small>
                            </p>
                        </div>

                        <div class="text-center mt-4">
                            <a href="index.php" class="btn" style="background-color: <?php echo $brand_primary_color; ?>; border-color: <?php echo $brand_primary_color; ?>; color: white;">
                                <i class="bi bi-house" aria-hidden="true"></i>&nbsp;&nbsp;Return to Homepage
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

</section><!-- /Accessibility Policy Section -->

<?php
include_once "assets/includes/footer.php";
?>