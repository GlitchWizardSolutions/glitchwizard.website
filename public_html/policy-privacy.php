<?php
// Unified includes and layout
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
// Note: Settings now loaded via doctype.php from database_settings.php
include_once "assets/includes/settings/image_helper.php";
?>

<!-- Privacy Policy Section -->
<section id="privacy" class="about section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Privacy Policy<br></h2>
    <div class="section-header-box">
      <p>How we collect, use, and protect your personal information</p>
    </div>
  </div><!-- End Section Title -->

  <div class="container">
    <div class="row gy-4">
      <div class="col-lg-12 content" data-aos="fade-up" data-aos-delay="100">
        <div class="section-content-box">
          
          <div class="mb-4 p-4 bg-light rounded">
            <p class="mb-1"><strong>Business:</strong> <?php echo htmlspecialchars($business_name); ?></p>
            <p class="mb-0"><strong>Last Updated:</strong> <?php echo date('F j, Y'); ?></p>
          </div>

          <div class="content">
            <h2>Our Commitment to Privacy</h2>
            <p class="lead">
              <?php echo htmlspecialchars($business_name); ?> is committed to protecting your privacy and personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or use our services.
            </p>

            <h3>1. Information We Collect</h3>
            <div class="row">
              <div class="col-md-6">
                <h5><i class="bi bi-person text-primary" aria-hidden="true"></i> Personal Information</h5>
                <p>When you register for an account or use our services, we may collect:</p>
                <ul>
                  <li>Name and contact information</li>
                  <li>Email address</li>
                  <li>Phone number</li>
                  <li>Billing and shipping addresses</li>
                  <li>Payment information</li>
                </ul>
              </div>
              <div class="col-md-6">
                <h5><i class="bi bi-globe text-primary" aria-hidden="true"></i> Automatically Collected Information</h5>
                <p>We automatically collect certain information when you visit our website:</p>
                <ul>
                  <li>IP address and device information</li>
                  <li>Browser type and version</li>
                  <li>Pages visited and time spent</li>
                  <li>Referring website information</li>
                  <li>Cookies and similar technologies</li>
                </ul>
              </div>
            </div>

            <h3>2. How We Use Your Information</h3>
            <p><?php echo htmlspecialchars($business_name); ?> uses the collected information for the following purposes:</p>
            <div class="row">
              <div class="col-md-4">
                <div class="card mb-3">
                  <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-gear text-success" aria-hidden="true"></i> Service Provision</h6>
                    <p class="card-text small">To provide, operate, and maintain our services and website functionality.</p>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card mb-3">
                  <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-envelope-fill text-info" aria-hidden="true"></i> Communication</h6>
                    <p class="card-text small">To respond to your inquiries, send updates, and provide customer support.</p>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card mb-3">
                  <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-graph-up-arrow text-warning" aria-hidden="true"></i> Improvement</h6>
                    <p class="card-text small">To analyze usage patterns and improve our website and services.</p>
                  </div>
                </div>
              </div>
            </div>

            <h3>3. Information Sharing and Disclosure</h3>
            <p>
              <?php echo htmlspecialchars($business_name); ?> does not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:
            </p>
            <ul>
              <li><strong>Service Providers:</strong> With trusted third-party vendors who assist in operating our website and conducting our business</li>
              <li><strong>Legal Requirements:</strong> When required by law or to protect our rights and safety</li>
              <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of business assets</li>
              <li><strong>With Your Consent:</strong> When you have given explicit permission for specific sharing</li>
            </ul>

            <h3>4. Data Security</h3>
            <p>
              We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet is 100% secure.
            </p>

            <h3>5. Cookies and Tracking Technologies</h3>
            <p>
              Our website uses cookies and similar tracking technologies to enhance your browsing experience. You can control cookie preferences through your browser settings, though disabling cookies may affect website functionality.
            </p>

            <h3>6. Your Privacy Rights</h3>
            <p>Depending on your location, you may have the following rights regarding your personal information:</p>
            <div class="row">
              <div class="col-md-6">
                <ul>
                  <li><strong>Access:</strong> Request access to your personal data</li>
                  <li><strong>Correction:</strong> Request correction of inaccurate data</li>
                  <li><strong>Deletion:</strong> Request deletion of your personal data</li>
                </ul>
              </div>
              <div class="col-md-6">
                <ul>
                  <li><strong>Portability:</strong> Request transfer of your data</li>
                  <li><strong>Objection:</strong> Object to processing of your data</li>
                  <li><strong>Restriction:</strong> Request restriction of processing</li>
                </ul>
              </div>
            </div>

            <h3>7. Data Retention</h3>
            <p>
              We retain your personal information only for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required or permitted by law.
            </p>

            <h3>8. Third-Party Links</h3>
            <p>
              Our website may contain links to third-party websites. We are not responsible for the privacy practices or content of these external sites. We encourage you to review their privacy policies.
            </p>

            <h3>9. Children's Privacy</h3>
            <p>
              Our services are not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13. If you believe we have collected such information, please contact us immediately.
            </p>

            <h3>10. Changes to This Privacy Policy</h3>
            <p>
              <?php echo htmlspecialchars($business_name); ?> may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.
            </p>

            <div class="alert alert-warning mt-5" role="alert">
              <h4 class="alert-heading"><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> Data Deletion Request</h4>
              <p>
                If you would like to request deletion of your personal information, please contact us using the link below. We will process your request in accordance with applicable privacy laws.
              </p>
              <hr>
              <p class="mb-0">
                <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($contact_email); ?>?subject=Data%20Deletion%20Request%20-%20<?php echo urlencode($business_name); ?>" class="alert-link">Request Data Deletion</a><br>
                <small class="text-muted">Please include your name and email address associated with your account for verification purposes.</small>
              </p>
            </div>

            <div class="alert alert-info mt-3" role="alert">
              <h4 class="alert-heading"><i class="bi bi-envelope-fill" aria-hidden="true"></i> Privacy Questions?</h4>
              <p>
                If you have any questions about this Privacy Policy or our data practices, please contact us at:
              </p>
              <hr>
              <p class="mb-0">
                <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($contact_email); ?>?subject=Privacy%20Policy%20Inquiry%20-%20<?php echo urlencode($business_name); ?>" class="alert-link"><?php echo htmlspecialchars($contact_email); ?></a><br>
                <strong>Phone:</strong> <?php echo htmlspecialchars($contact_phone); ?><br>
                <strong>Address:</strong> <?php echo htmlspecialchars($contact_address); ?>, <?php echo htmlspecialchars($contact_city); ?> <?php echo htmlspecialchars($contact_state); ?> <?php echo htmlspecialchars($contact_zipcode); ?>
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
  </div>

</section><!-- /Privacy Policy Section -->

<?php
include_once "assets/includes/footer.php";
?>