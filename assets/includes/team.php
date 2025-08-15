<?php
// This file is part of the GWS Universal Hybrid App project.
// It is included in the main index.php file to render the Team section of the website.
include_once "assets/includes/settings/public_settings.php";
// Fallbacks to prevent undefined variable warnings
if (!isset($team_members) || !is_array($team_members))
  $team_members = [];
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

    <div class="row gy-4">

      <?php
      // Array of hardcoded team images
      $team_images = [
        'assets/img/team/team-1.jpg',
        'assets/img/team/team-2.jpg',
        'assets/img/team/team-3.jpg',
        'assets/img/team/team-4.jpg'
      ];
      foreach ($team_members as $i => $member): ?>
        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up"
          data-aos-delay="<?php echo 100 + $i * 100; ?>">
          <div class="team-member">
            <div class="member-img">
              <?php $img = isset($team_images[$i]) ? $team_images[$i] : $team_images[0]; ?>
              <img src="<?php echo $img; ?>" class="img-fluid" alt="">
              <div class="social">
                <a href="<?php echo htmlspecialchars($member['social']['twitter']); ?>"><i
                    class="bi bi-twitter-x"></i></a>
                <a href="<?php echo htmlspecialchars($member['social']['facebook']); ?>"><i
                    class="bi bi-facebook"></i></a>
                <a href="<?php echo htmlspecialchars($member['social']['instagram']); ?>"><i
                    class="bi bi-instagram"></i></a>
                <a href="<?php echo htmlspecialchars($member['social']['linkedin']); ?>"><i
                    class="bi bi-linkedin"></i></a>
              </div>
            </div>
            <div class="member-info">
              <h4><?php echo htmlspecialchars($member['name']); ?></h4>
              <span><?php echo htmlspecialchars($member['role']); ?></span>
            </div>
          </div>
        </div><!-- End Team Member -->
      <?php endforeach; ?>
    </div>

  </div>

</section><!-- /Team Section -->