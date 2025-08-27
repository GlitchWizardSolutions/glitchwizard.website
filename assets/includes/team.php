<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the Team section of the website.

// Team data should be loaded from database_settings.php via doctype.php
// Fallback for missing team data
if (!isset($team_title)) {
  $team_title = 'Our Team';
}
if (!isset($team_paragraph)) {
  $team_paragraph = 'Meet our professional team';
}
if (!isset($team_members) || !is_array($team_members)) {
  $team_members = [];
}
?>
<!-- Team Section -->
<section id="team" class="team section light-background">

  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <h2><?php echo htmlspecialchars($team_title ?? 'Team'); ?></h2>
    <div class="section-header-box">
      <p><?php echo $team_paragraph ?? 'Meet our talented team of professionals dedicated to your success.'; ?></p>
    </div>
  </div><!-- End Section Title -->

  <div class="container">
    <div class="row gy-5">
      <?php
      if (!empty($team_members)) {
        foreach ($team_members as $i => $member): ?>
          <div class="col-lg-4 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="<?php echo 100 + $i * 100; ?>">
            <div class="team-member-card">
              <div class="member-image-container">
                <div class="member-img-wrapper">
                  <img src="<?php echo htmlspecialchars($member['image'] ?? 'assets/img/team/team-1.jpg'); ?>" 
                       class="member-img" 
                       alt="<?php echo htmlspecialchars($member['name'] ?? 'Team Member'); ?>">
                  <div class="member-overlay">
                    <div class="social-links">
                      <?php if (!empty($member['social']['linkedin']) && $member['social']['linkedin'] !== '#'): ?>
                        <a href="<?php echo htmlspecialchars($member['social']['linkedin']); ?>" class="social-link">
                          <i class="bi bi-linkedin"></i>
                        </a>
                      <?php endif; ?>
                      <?php if (!empty($member['social']['twitter']) && $member['social']['twitter'] !== '#'): ?>
                        <a href="<?php echo htmlspecialchars($member['social']['twitter']); ?>" class="social-link">
                          <i class="bi bi-twitter-x"></i>
                        </a>
                      <?php endif; ?>
                      <?php if (!empty($member['social']['facebook']) && $member['social']['facebook'] !== '#'): ?>
                        <a href="<?php echo htmlspecialchars($member['social']['facebook']); ?>" class="social-link">
                          <i class="bi bi-facebook"></i>
                        </a>
                      <?php endif; ?>
                      <?php if (!empty($member['social']['instagram']) && $member['social']['instagram'] !== '#'): ?>
                        <a href="<?php echo htmlspecialchars($member['social']['instagram']); ?>" class="social-link">
                          <i class="bi bi-instagram"></i>
                        </a>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="member-content">
                <div class="member-header">
                  <h4 class="member-name"><?php echo htmlspecialchars($member['name'] ?? 'Team Member'); ?></h4>
                  <div class="member-role"><?php echo htmlspecialchars($member['title'] ?? 'Position'); ?></div>
                </div>
                
                <?php if (!empty($member['bio'])): ?>
                  <div class="member-bio-container">
                    <p class="member-bio"><?php echo htmlspecialchars($member['bio']); ?></p>
                    <button class="bio-toggle" onclick="toggleBio(this)">
                      <span class="read-more">Read More</span>
                      <span class="read-less">Read Less</span>
                      <i class="bi bi-chevron-down"></i>
                    </button>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div><!-- End Team Member -->
        <?php endforeach;
      } else {
        // Fallback message when no team members are loaded
        echo '<div class="col-12"><div class="no-team-content"><p>No team members available. Please add team content to the database using the provided SQL files.</p></div></div>';
      } ?>
    </div>
  </div>

</section><!-- /Team Section -->