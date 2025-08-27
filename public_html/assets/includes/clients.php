<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the Clients section of the website.

// Clients data should be loaded from database_settings.php via doctype.php
// Fallback for missing clients data
if (!isset($clients) || !is_array($clients)) {
  $clients = [];
}
?>
   <!-- Clients Section -->
    <section id="clients" class="clients section light-background">

      <div class="container" data-aos="fade-up">

        <div class="row gy-4">

          <?php if (!empty($clients)): ?>
            <?php foreach ($clients as $client): ?>
              <div class="col-xl-2 col-md-3 col-6 client-logo">
                <?php if (!empty($client['website']) && $client['website'] !== '#'): ?>
                  <a href="<?php echo htmlspecialchars($client['website']); ?>" target="_blank" rel="noopener">
                    <img src="<?php echo htmlspecialchars($client['logo']); ?>" 
                         class="img-fluid" 
                         alt="<?php echo htmlspecialchars($client['alt_text']); ?>"
                         title="<?php echo htmlspecialchars($client['name']); ?>">
                  </a>
                <?php else: ?>
                  <img src="<?php echo htmlspecialchars($client['logo']); ?>" 
                       class="img-fluid" 
                       alt="<?php echo htmlspecialchars($client['alt_text']); ?>"
                       title="<?php echo htmlspecialchars($client['name']); ?>">
                <?php endif; ?>
              </div><!-- End Client Item -->
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Fallback when no clients are loaded -->
            <div class="col-12 text-center">
              <p class="text-muted">Client logos will appear here when added to the database.</p>
            </div>
          <?php endif; ?>

        </div>

      </div>

    </section><!-- /Clients Section -->


