<?php
// Unified includes and layout
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
// Note: Settings now loaded via doctype.php from database_settings.php
include_once "assets/includes/settings/image_helper.php";
?>

<!-- Terms of Service Section -->
<section id="terms" class="about section">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2>Terms of Service<br></h2>
    <div class="section-header-box">
      <p>Terms and conditions governing your use of our services</p>
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
            <h2>Agreement to Terms</h2>
            <p class="lead">
              By accessing and using this website operated by <strong><?php echo htmlspecialchars($business_name); ?></strong>, you accept and agree to be bound by the terms and provision of this agreement.
            </p>

            <h3>1. Use License</h3>
            <p>
              Permission is granted to temporarily download one copy of the materials on <?php echo htmlspecialchars($business_name); ?>'s website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:
            </p>
            <ul>
              <li>Modify or copy the materials</li>
              <li>Use the materials for any commercial purpose or for any public display</li>
              <li>Attempt to reverse engineer any software contained on the website</li>
              <li>Remove any copyright or other proprietary notations from the materials</li>
            </ul>

            <h3>2. Products and Services</h3>
            <p>
              All products and services offered through this website are subject to availability. <?php echo htmlspecialchars($business_name); ?> reserves the right to discontinue any product or service at any time without notice.
            </p>
            <div class="row">
              <div class="col-md-6">
                <h5><i class="bi bi-cart text-primary" aria-hidden="true"></i> Online Store</h5>
                <p>Prices and availability of products are subject to change without notice. We reserve the right to limit quantities and refuse service.</p>
              </div>
              <div class="col-md-6">
                <h5><i class="bi bi-credit-card text-primary" aria-hidden="true"></i> Payment Terms</h5>
                <p>Payment is due at the time of purchase. We accept major credit cards and other payment methods as indicated at checkout.</p>
              </div>
            </div>

            <h3>3. User Accounts</h3>
            <p>
              When you create an account with us, you must provide information that is accurate, complete, and current at all times. You are responsible for safeguarding the password and for maintaining the confidentiality of your account.
            </p>

            <h3>4. Prohibited Uses</h3>
            <p>You may not use our service:</p>
            <ul>
              <li>For any unlawful purpose or to solicit others to commit unlawful acts</li>
              <li>To violate any international, federal, provincial, or state regulations, rules, laws, or local ordinances</li>
              <li>To infringe upon or violate our intellectual property rights or the intellectual property rights of others</li>
              <li>To harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate</li>
              <li>To submit false or misleading information</li>
            </ul>

            <h3>5. Disclaimer</h3>
            <p>
              The materials on <?php echo htmlspecialchars($business_name); ?>'s website are provided on an 'as is' basis. <?php echo htmlspecialchars($business_name); ?> makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.
            </p>

            <h3>6. Limitations</h3>
            <p>
              In no event shall <?php echo htmlspecialchars($business_name); ?> or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on <?php echo htmlspecialchars($business_name); ?>'s website, even if <?php echo htmlspecialchars($business_name); ?> or its authorized representative has been notified orally or in writing of the possibility of such damage.
            </p>

            <h3>7. Accuracy of Materials</h3>
            <p>
              The materials appearing on <?php echo htmlspecialchars($business_name); ?>'s website could include technical, typographical, or photographic errors. <?php echo htmlspecialchars($business_name); ?> does not warrant that any of the materials on its website are accurate, complete, or current.
            </p>

            <h3>8. Modifications</h3>
            <p>
              <?php echo htmlspecialchars($business_name); ?> may revise these terms of service for its website at any time without notice. By using this website, you are agreeing to be bound by the then current version of these terms of service.
            </p>

            <h3>9. Governing Law</h3>
            <p>
              These terms and conditions are governed by and construed in accordance with the laws of the jurisdiction in which <?php echo htmlspecialchars($business_name); ?> operates, and you irrevocably submit to the exclusive jurisdiction of the courts in that state or location.
            </p>

            <div class="alert alert-info mt-5" role="alert">
              <h4 class="alert-heading"><i class="bi bi-envelope-fill" aria-hidden="true"></i> Questions About Terms of Service?</h4>
              <p>
                If you have any questions about these Terms of Service, please contact us at:
              </p>
              <hr>
              <p class="mb-0">
                <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($contact_email); ?>?subject=Terms%20of%20Service%20Inquiry%20-%20<?php echo urlencode($business_name); ?>" class="alert-link"><?php echo htmlspecialchars($contact_email); ?></a><br>
                <strong>Phone:</strong> <?php echo htmlspecialchars($contact_phone); ?>
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

</section><!-- /Terms of Service Section -->

<?php
include_once "assets/includes/footer.php";
?>