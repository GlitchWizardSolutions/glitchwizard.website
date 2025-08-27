<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the Stats section of the website.

// Stats data should be loaded from database_settings.php via doctype.php
// Fallback for missing stats data
if (!isset($stats) || !is_array($stats)) {
  $stats = [];
}
?>
<!-- Stats Section -->
<section id="stats" class="stats section light-background">

  <div class="container" data-aos="fade-up" data-aos-delay="100">

    <div class="row gy-4">
      <?php if (isset($stats) && is_array($stats) && count($stats) > 0):
        foreach ($stats as $stat): ?>
          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="<?php echo htmlspecialchars($stat['value']); ?>"
                data-purecounter-duration="1" class="purecounter"></span>
              <p><?php echo htmlspecialchars($stat['label']); ?></p>
            </div>
          </div><!-- End Stats Item -->
        <?php endforeach;
      endif; ?>
    </div>

  </div>

</section><!-- /Stats Section -->