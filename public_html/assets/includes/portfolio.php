<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the Portfolio section of the website.

// Portfolio data should be loaded from database_settings.php via doctype.php
// Fallback for missing portfolio data
if (!isset($portfolio_title)) {
  $portfolio_title = 'Portfolio';
}
if (!isset($portfolio_paragraph)) {
  $portfolio_paragraph = 'Explore some of our recent projects and creative work. Each item showcases our commitment to quality and innovation.';
}
if (!isset($portfolio_items) || !is_array($portfolio_items)) {
  $portfolio_items = [];
}
if (!isset($portfolio_filters) || !is_array($portfolio_filters)) {
  $portfolio_filters = ['All', 'Apps', 'Products', 'Websites'];
}
?>
<!-- Portfolio Section -->
<section id="portfolio" class="portfolio section">
  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2><?php echo htmlspecialchars($portfolio_title ?? 'Portfolio'); ?></h2>
    <div class="section-header-box">
      <p><?php echo $portfolio_paragraph ?? 'Explore some of our recent projects and creative work. Each item showcases our commitment to quality and innovation.'; ?></p>
    </div>
  </div><!-- End Section Title -->
  <div class="container">
    <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">
      <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
        <li data-filter="*" class="filter-active"><?php echo htmlspecialchars($portfolio_filters[0] ?? 'All'); ?></li>
        <li data-filter=".filter-app"><?php echo htmlspecialchars($portfolio_filters[1] ?? 'Apps'); ?></li>
        <li data-filter=".filter-product"><?php echo htmlspecialchars($portfolio_filters[2] ?? 'Products'); ?></li>
        <li data-filter=".filter-branding"><?php echo htmlspecialchars($portfolio_filters[3] ?? 'Websites'); ?></li>
      </ul><!-- End Portfolio Filters -->
      <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">
        <?php 
        if (!empty($portfolio_items)) {
          foreach ($portfolio_items as $index => $item): ?>
            <div class="col-lg-4 col-md-6 portfolio-item isotope-item <?php echo htmlspecialchars($item['filter_class'] ?? 'filter-all'); ?>">
              <img src="<?php echo htmlspecialchars($item['image'] ?? 'assets/img/masonry-portfolio/masonry-portfolio-1.jpg'); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($item['title'] ?? 'Portfolio Item'); ?>">
              <div class="portfolio-info">
                <h4><?php echo htmlspecialchars($item['title'] ?? 'Portfolio Item'); ?></h4>
                <p><?php echo htmlspecialchars($item['description'] ?? 'Portfolio description'); ?></p>
                <a href="<?php echo htmlspecialchars($item['large_image'] ?? $item['image'] ?? 'assets/img/masonry-portfolio/masonry-portfolio-1.jpg'); ?>" 
                   title="<?php echo htmlspecialchars($item['title'] ?? 'Portfolio Item'); ?>"
                   data-gallery="<?php echo htmlspecialchars($item['gallery_name'] ?? 'portfolio-gallery'); ?>" 
                   class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                <a href="<?php echo htmlspecialchars($item['url'] ?? '#'); ?>" 
                   title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
              </div>
            </div><!-- End Portfolio Item -->
          <?php endforeach;
        } else {
          // Fallback message when no portfolio items are loaded
          echo '<div class="col-12"><p class="text-center text-muted">No portfolio items available. Please create the portfolio table and add content to the database using the provided SQL files.</p></div>';
        } ?>
      </div><!-- End Portfolio Container -->
    </div>
  </div>
</section><!-- /Portfolio Section -->