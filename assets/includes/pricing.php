<?php
// Pricing data should be loaded from database_settings.php via doctype.php
// Fallback for missing pricing data
if (!isset($pricing_title)) {
  $pricing_title = 'Pricing';
}
if (!isset($pricing_paragraph)) {
  $pricing_paragraph = 'Choose the plan that works for you';
}
if (!isset($pricing_plans) || !is_array($pricing_plans)) {
  $pricing_plans = [];
}
?>
<!-- Pricing Section -->
<section id="pricing" class="pricing section">
  <div class="container section-title" data-aos="fade-up">
    <h2><?php echo htmlspecialchars($pricing_title ?? 'Pricing'); ?></h2>
    <div class="section-header-box">
      <p><?php echo $pricing_paragraph ?? 'Choose the plan that fits your needs. All plans are fully customizable.'; ?></p>
    </div>
  </div>
  <div class="container">
    <div class="row g-4 g-lg-0">
      <?php foreach ($pricing_plans as $i => $plan): ?>
        <div class="col-lg-4<?php echo ($i == 1) ? ' featured' : ''; ?>" data-aos="zoom-in"
          data-aos-delay="<?php echo 100 + $i * 100; ?>">
          <div class="pricing-item">
            <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
            <h4><?php echo htmlspecialchars($plan['price']); ?></h4>
            <ul>
              <?php
              $feature_status = isset($plan['feature_status']) ? $plan['feature_status'] : [];
              foreach ($plan['features'] as $f => $feature):
                $status = isset($feature_status[$f]) ? $feature_status[$f] : 'check';
                $icon = ($status == 'x') ? 'bi-x' : 'bi-check';
                $li_class = ($status == 'x') ? 'na' : '';
                $text = ($status == 'x') ? '<span style="text-decoration:line-through;">' . htmlspecialchars($feature) . '</span>' : '<span>' . htmlspecialchars($feature) . '</span>';
                ?>
                <li class="<?php echo $li_class; ?>">
                  <i class="bi <?php echo $icon; ?>"></i> <?php echo $text; ?>
                </li>
              <?php endforeach; ?>
            </ul>
            <div class="text-center">
              <a href="<?php echo isset($plan['button_link']) ? htmlspecialchars($plan['button_link']) : '#'; ?>"
                class="buy-btn">
                <?php echo isset($plan['button_text']) ? htmlspecialchars($plan['button_text']) : 'Buy Now'; ?>
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section><!-- /Pricing Section -->