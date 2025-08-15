<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the Portfolio section of the website.  
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
        <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
          <img src="assets/img/masonry-portfolio/masonry-portfolio-1.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Mobile App Design</h4>
            <p>Modern, user-friendly mobile application for business productivity.</p>
            <a href="assets/img/masonry-portfolio/masonry-portfolio-1.jpg" title="Mobile App Design"
              data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.php?id=1" title="More Details" class="details-link"><i
                class="bi bi-link-45deg"></i></a>
          </div>
        </div><!-- End Portfolio Item -->
        <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
          <img src="assets/img/masonry-portfolio/masonry-portfolio-2.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Product Launch Campaign</h4>
            <p>Comprehensive marketing campaign for a new product release.</p>
            <a href="assets/img/masonry-portfolio/masonry-portfolio-2.jpg" title="Product Launch Campaign"
              data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.php?id=2" title="More Details" class="details-link"><i
                class="bi bi-link-45deg"></i></a>
          </div>
        </div><!-- End Portfolio Item -->
        <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
          <img src="assets/img/masonry-portfolio/masonry-portfolio-3.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Brand Identity</h4>
            <p>Complete branding package for a growing business.</p>
            <a href="assets/img/masonry-portfolio/masonry-portfolio-3.jpg" title="Brand Identity"
              data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.php?id=3" title="More Details" class="details-link"><i
                class="bi bi-link-45deg"></i></a>
          </div>
        </div><!-- End Portfolio Item -->
        <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
          <img src="assets/img/masonry-portfolio/masonry-portfolio-4.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Task Management App</h4>
            <p>Efficient task tracking and collaboration tool for teams.</p>
            <a href="assets/img/masonry-portfolio/masonry-portfolio-4.jpg" title="Task Management App"
              data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.php?id=4" title="More Details" class="details-link"><i
                class="bi bi-link-45deg"></i></a>
          </div>
        </div><!-- End Portfolio Item -->
        <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
          <img src="assets/img/masonry-portfolio/masonry-portfolio-5.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Product Packaging Design</h4>
            <p>Creative packaging for a retail product line.</p>
            <a href="assets/img/masonry-portfolio/masonry-portfolio-5.jpg" title="Product Packaging Design"
              data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.php?id=5" title="More Details" class="details-link"><i
                class="bi bi-link-45deg"></i></a>
          </div>
        </div><!-- End Portfolio Item -->
        <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
          <img src="assets/img/masonry-portfolio/masonry-portfolio-6.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Website Redesign</h4>
            <p>Modern responsive website for a local business.</p>
            <a href="assets/img/masonry-portfolio/masonry-portfolio-6.jpg" title="Website Redesign"
              data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.php?id=6" title="More Details" class="details-link"><i
                class="bi bi-link-45deg"></i></a>
          </div>
        </div><!-- End Portfolio Item -->
        <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
          <img src="assets/img/masonry-portfolio/masonry-portfolio-7.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Event App</h4>
            <p>Mobile app for event scheduling and attendee engagement.</p>
            <a href="assets/img/masonry-portfolio/masonry-portfolio-7.jpg" title="Event App"
              data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.php?id=7" title="More Details" class="details-link"><i
                class="bi bi-link-45deg"></i></a>
          </div>
        </div><!-- End Portfolio Item -->
        <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
          <img src="assets/img/masonry-portfolio/masonry-portfolio-8.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Product Demo Video</h4>
            <p>Engaging video content to showcase product features.</p>
            <a href="assets/img/masonry-portfolio/masonry-portfolio-8.jpg" title="Product Demo Video"
              data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.php?id=8" title="More Details" class="details-link"><i
                class="bi bi-link-45deg"></i></a>
          </div>
        </div><!-- End Portfolio Item -->
        <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-branding">
          <img src="assets/img/masonry-portfolio/masonry-portfolio-9.jpg" class="img-fluid" alt="">
          <div class="portfolio-info">
            <h4>Logo & Branding</h4>
            <p>Distinctive logo and branding for a startup company.</p>
            <a href="assets/img/masonry-portfolio/masonry-portfolio-9.jpg" title="Logo & Branding"
              data-gallery="portfolio-gallery-branding" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
            <a href="portfolio-details.php?id=9" title="More Details" class="details-link"><i
                class="bi bi-link-45deg"></i></a>
          </div>
        </div><!-- End Portfolio Item -->
      </div><!-- End Portfolio Container -->
    </div>
  </div>
</section><!-- /Portfolio Section -->