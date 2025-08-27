<?php
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
// Note: Settings now loaded via doctype.php from database_settings.php

// Get the portfolio item ID from URL parameter
$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Portfolio data array
$portfolio_items = [
    1 => [
        'title' => 'Mobile App Design',
        'category' => 'App Development',
        'description' => 'Modern, user-friendly mobile application for business productivity.',
        'full_description' => 'This comprehensive mobile application was designed to enhance business productivity through intuitive user interface design and powerful functionality. The app features a clean, modern design that prioritizes user experience while providing robust tools for task management, team collaboration, and data analysis.',
        'image' => 'assets/img/masonry-portfolio/masonry-portfolio-1.jpg',
        'client' => 'TechCorp Solutions',
        'date' => 'January 2024',
        'technologies' => ['React Native', 'Node.js', 'Firebase', 'UI/UX Design'],
        'url' => '#'
    ],
    2 => [
        'title' => 'Product Launch Campaign',
        'category' => 'Marketing',
        'description' => 'Comprehensive marketing campaign for a new product release.',
        'full_description' => 'A full-scale marketing campaign designed to maximize impact for a new product launch. The campaign included digital marketing strategies, social media engagement, content creation, and brand positioning to ensure successful market penetration.',
        'image' => 'assets/img/masonry-portfolio/masonry-portfolio-2.jpg',
        'client' => 'Innovation Labs',
        'date' => 'February 2024',
        'technologies' => ['Digital Marketing', 'Content Strategy', 'Social Media', 'Analytics'],
        'url' => '#'
    ],
    3 => [
        'title' => 'Brand Identity',
        'category' => 'Branding',
        'description' => 'Complete branding package for a growing business.',
        'full_description' => 'A comprehensive brand identity project that included logo design, color palette development, typography selection, and brand guidelines. The project aimed to create a cohesive visual identity that would support the company\'s growth and market positioning.',
        'image' => 'assets/img/masonry-portfolio/masonry-portfolio-3.jpg',
        'client' => 'GrowthCo',
        'date' => 'March 2024',
        'technologies' => ['Logo Design', 'Brand Strategy', 'Visual Identity', 'Print Design'],
        'url' => '#'
    ],
    4 => [
        'title' => 'Task Management App',
        'category' => 'App Development',
        'description' => 'Efficient task tracking and collaboration tool for teams.',
        'full_description' => 'An advanced task management application designed to streamline team collaboration and project tracking. The app features real-time updates, intuitive project organization, and comprehensive reporting tools to help teams stay organized and productive.',
        'image' => 'assets/img/masonry-portfolio/masonry-portfolio-4.jpg',
        'client' => 'TeamWork Inc.',
        'date' => 'April 2024',
        'technologies' => ['Vue.js', 'Laravel', 'MySQL', 'Real-time Sync'],
        'url' => '#'
    ],
    5 => [
        'title' => 'Product Packaging Design',
        'category' => 'Design',
        'description' => 'Creative packaging for a retail product line.',
        'full_description' => 'Innovative packaging design for a new retail product line that needed to stand out on store shelves while maintaining cost-effectiveness. The design process included market research, competitor analysis, and multiple concept iterations.',
        'image' => 'assets/img/masonry-portfolio/masonry-portfolio-5.jpg',
        'client' => 'Retail Plus',
        'date' => 'May 2024',
        'technologies' => ['Package Design', '3D Modeling', 'Print Production', 'Market Research'],
        'url' => '#'
    ],
    6 => [
        'title' => 'Website Redesign',
        'category' => 'Web Development',
        'description' => 'Modern responsive website for a local business.',
        'full_description' => 'Complete website redesign and development project focusing on improved user experience, mobile responsiveness, and search engine optimization. The new site features modern design principles and enhanced functionality.',
        'image' => 'assets/img/masonry-portfolio/masonry-portfolio-6.jpg',
        'client' => 'Local Business Co.',
        'date' => 'June 2024',
        'technologies' => ['PHP', 'CSS3', 'JavaScript', 'SEO Optimization'],
        'url' => '#'
    ],
    7 => [
        'title' => 'Event App',
        'category' => 'App Development',
        'description' => 'Mobile app for event scheduling and attendee engagement.',
        'full_description' => 'A comprehensive event management mobile application that facilitates event scheduling, attendee networking, and real-time engagement. The app includes features for schedule management, speaker profiles, and interactive sessions.',
        'image' => 'assets/img/masonry-portfolio/masonry-portfolio-7.jpg',
        'client' => 'EventPro',
        'date' => 'July 2024',
        'technologies' => ['Flutter', 'Firebase', 'Push Notifications', 'QR Integration'],
        'url' => '#'
    ],
    8 => [
        'title' => 'Product Demo Video',
        'category' => 'Video Production',
        'description' => 'Engaging video content to showcase product features.',
        'full_description' => 'Professional video production project to create engaging demo content that effectively showcases product features and benefits. The project included scriptwriting, filming, editing, and post-production optimization.',
        'image' => 'assets/img/masonry-portfolio/masonry-portfolio-8.jpg',
        'client' => 'VideoTech',
        'date' => 'August 2024',
        'technologies' => ['Video Production', 'Motion Graphics', 'Sound Design', 'Color Grading'],
        'url' => '#'
    ],
    9 => [
        'title' => 'Logo & Branding',
        'category' => 'Branding',
        'description' => 'Distinctive logo and branding for a startup company.',
        'full_description' => 'Complete logo design and branding package for an emerging startup. The project focused on creating a memorable visual identity that would support the company\'s growth objectives and market differentiation.',
        'image' => 'assets/img/masonry-portfolio/masonry-portfolio-9.jpg',
        'client' => 'StartupCo',
        'date' => 'September 2024',
        'technologies' => ['Logo Design', 'Brand Guidelines', 'Business Cards', 'Digital Assets'],
        'url' => '#'
    ]
];

// Get the current portfolio item or default to first item
$item = isset($portfolio_items[$item_id]) ? $portfolio_items[$item_id] : $portfolio_items[1];
?>

<!-- Portfolio Details Section -->
<section id="portfolio-details" class="portfolio-details section">
  <div class="container" data-aos="fade-up">
    
    <div class="row gy-4">
      
      <div class="col-lg-8">
        <div class="portfolio-details-slider swiper">
          <div class="swiper-wrapper align-items-center">
            <div class="swiper-slide">
              <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="img-fluid">
            </div>
          </div>
          <div class="swiper-pagination"></div>
        </div>
      </div>
      
      <div class="col-lg-4">
        <div class="portfolio-info" data-aos="fade-up" data-aos-delay="200">
          <h3>Project Information</h3>
          <ul>
            <li><strong>Category</strong>: <?php echo htmlspecialchars($item['category']); ?></li>
            <li><strong>Client</strong>: <?php echo htmlspecialchars($item['client']); ?></li>
            <li><strong>Project Date</strong>: <?php echo htmlspecialchars($item['date']); ?></li>
            <?php if($item['url'] !== '#'): ?>
            <li><strong>Project URL</strong>: <a href="<?php echo htmlspecialchars($item['url']); ?>" target="_blank">View Project</a></li>
            <?php endif; ?>
          </ul>
        </div>
        
        <div class="portfolio-description" data-aos="fade-up" data-aos-delay="300">
          <h2><?php echo htmlspecialchars($item['title']); ?></h2>
          <p><?php echo htmlspecialchars($item['full_description']); ?></p>
          
          <div class="technologies">
            <h4>Technologies Used:</h4>
            <div class="tech-tags">
              <?php foreach($item['technologies'] as $tech): ?>
                <span class="tech-tag"><?php echo htmlspecialchars($tech); ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      
    </div>
    
    <div class="row mt-5">
      <div class="col-12">
        <a href="index.php#portfolio" class="btn btn-primary">
          <i class="bi bi-arrow-left"></i> Back to Portfolio
        </a>
      </div>
    </div>
    
  </div>
</section><!-- /Portfolio Details Section -->

<style>
.portfolio-details .portfolio-info ul {
  list-style: none;
  padding: 0;
}

.portfolio-details .portfolio-info ul li {
  padding: 10px 0;
  border-bottom: 1px solid #eee;
}

.portfolio-details .portfolio-info ul li:last-child {
  border-bottom: none;
}

.portfolio-description {
  padding-top: 30px;
}

.tech-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 10px;
}

.tech-tag {
  background: linear-gradient(135deg, #007bff, #28a745);
  color: white;
  padding: 4px 12px;
  border-radius: 15px;
  font-size: 0.875rem;
  font-weight: 500;
}

.portfolio-details .portfolio-info {
  background: #f8f9fa;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
}

.portfolio-details .portfolio-description {
  background: #ffffff;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
}
</style>

<?php
include_once "assets/includes/footer.php";
?>
