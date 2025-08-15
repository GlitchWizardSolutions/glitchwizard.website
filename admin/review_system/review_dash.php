<?php
// Phase 2 Template Integration - Using unified admin template system
include_once '../assets/includes/main.php';

// Review system configuration constants
if (!defined('max_stars')) {
    define('max_stars', 5);
}

$stmt = $pdo->prepare('SELECT r.*, (SELECT GROUP_CONCAT(i.file_path) FROM review_images i WHERE i.review_id = r.id) AS imgs, rpd.url, a.id AS account_id, a.email FROM reviews r LEFT JOIN accounts a ON r.account_id = a.id LEFT JOIN review_page_details rpd ON rpd.page_id = r.page_id WHERE cast(r.submit_date as DATE) = cast(now() as DATE) ORDER BY r.submit_date DESC');
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get the total number of reviews
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM reviews WHERE approved = -1');
$stmt->execute();
$awaiting_approval = $stmt->fetchColumn();
// Get the total number of reviews
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM reviews');
$stmt->execute();
$local_reviews_total = $stmt->fetchColumn();
// Get the total number of reviews
$stmt = $pdo->prepare('SELECT COUNT(page_id) AS total FROM reviews GROUP BY page_id');
$stmt->execute();
$reviews_page_total = $stmt->fetchAll(PDO::FETCH_ASSOC);
$reviews_page_total = count($reviews_page_total);
// Get the reviews awaiting approval
$stmt = $pdo->prepare('SELECT r.*, (SELECT GROUP_CONCAT(i.file_path) FROM review_images i WHERE i.review_id = r.id) AS imgs, rpd.url, a.id AS account_id, a.email FROM reviews r LEFT JOIN accounts a ON r.account_id = a.id LEFT JOIN review_page_details rpd ON rpd.page_id = r.page_id WHERE r.approved = -1 ORDER BY r.submit_date DESC');
$stmt->execute();
$reviews_awaiting_approval = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Additional statistics for enhanced dashboard
$approved_reviews = $pdo->query('SELECT COUNT(*) FROM reviews WHERE approved = 1')->fetchColumn();
$avg_rating = $pdo->query('SELECT AVG(rating) FROM reviews WHERE approved = 1')->fetchColumn();
$recent_week_reviews = $pdo->query('SELECT COUNT(*) FROM reviews WHERE submit_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)')->fetchColumn();
$high_rated_reviews = $pdo->query('SELECT COUNT(*) FROM reviews WHERE approved = 1 AND rating >= 4')->fetchColumn();

// Action Items Data - Things requiring attention
$low_rated_reviews = $pdo->query('SELECT COUNT(*) FROM reviews WHERE approved = 1 AND rating <= 2')->fetchColumn();
$old_pending_reviews = $pdo->query('SELECT COUNT(*) FROM reviews WHERE approved = -1 AND submit_date < DATE_SUB(NOW(), INTERVAL 3 DAY)')->fetchColumn();
$flagged_reviews = $pdo->query('SELECT COUNT(*) FROM reviews WHERE approved = -1 AND rating <= 1')->fetchColumn();

// Calculate action items total
$total_action_items = $awaiting_approval + $old_pending_reviews + $low_rated_reviews;

// Use unified admin header with reviews navigation
echo template_admin_header('Review System Dashboard', 'reviews', 'dashboard');
?>
<div class="content-title" id="main-gallery-dashboard" role="banner" aria-label="Gallery Dashboard Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M256 0L576 0c35.3 0 64 28.7 64 64l0 224c0 35.3-28.7 64-64 64l-320 0c-35.3 0-64-28.7-64-64l0-224c0-35.3 28.7-64 64-64zM476 106.7C471.5 100 464 96 456 96s-15.5 4-20 10.7l-56 84L362.7 169c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l80 0 48 0 144 0c8.9 0 17-4.9 21.2-12.7s3.7-17.3-1.2-24.6l-96-144zM336 96a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM64 128l96 0 0 256 0 32c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-32 160 0 0 64c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 192c0-35.3 28.7-64 64-64zm8 64c-8.8 0-16 7.2-16 16l0 16c0 8.8 7.2 16 16 16l16 0c8.8 0 16-7.2 16-16l0-16c0-8.8-7.2-16-16-16l-16 0zm0 104c-8.8 0-16 7.2-16 16l0 16c0 8.8 7.2 16 16 16l16 0c8.8 0 16-7.2 16-16l0-16c0-8.8-7.2-16-16-16l-16 0zm0 104c-8.8 0-16 7.2-16 16l0 16c0 8.8 7.2 16 16 16l16 0c8.8 0 16-7.2 16-16l0-16c0-8.8-7.2-16-16-16l-16 0zm336 16l0 16c0 8.8 7.2 16 16 16l16 0c8.8 0 16-7.2 16-16l0-16c0-8.8-7.2-16-16-16l-16 0c-8.8 0-16 7.2-16 16z"/></svg>
        </div>
        <div class="txt">
            <h2>Review Dashboard</h2>
            <p>Manage Reviews</p>
        </div>
    </div>
</div>
<!-- Dashboard Apps Grid -->
<div class="dashboard-apps">
    <!-- Review Quick Actions Card -->
    <div class="app-card" role="region" aria-labelledby="review-quick-actions-title">
        <div class="app-header events-header" role="banner" aria-labelledby="review-quick-actions-title">
            <h3 id="review-quick-actions-title">Quick Actions</h3>
            <i class="fas fa-bolt header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Review management actions">Manage</span>
        </div>
        <div class="app-body">
            <div class="quick-actions">
                <a href="review.php" class="quick-action primary">
                    <div class="action-icon">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Create Review</h4>
                        <small class="text-muted">Add new review manually</small>
                    </div>
                </a>
                <a href="reviews.php" class="quick-action info">
                    <div class="action-icon">
                        <i class="fas fa-list" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>View All Reviews</h4>
                        <small class="text-muted">Manage existing reviews</small>
                    </div>
                </a>
                <a href="review_pages.php" class="quick-action secondary">
                    <div class="action-icon">
                        <i class="fas fa-file-alt" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Review Pages</h4>
                        <small class="text-muted">Manage page settings</small>
                    </div>
                </a>
                <a href="review_filters.php" class="quick-action success">
                    <div class="action-icon">
                        <i class="fas fa-filter" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Review Filters</h4>
                        <small class="text-muted">Content moderation settings</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Review Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="review-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="review-actions-title">
            <h3 id="review-actions-title">Action Items</h3>
            <i class="fas fa-exclamation-triangle header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $total_action_items ?> items requiring attention"><?= $total_action_items ?> items</span>
        </div>
        <div class="app-body">
            <?php if ($total_action_items > 0): ?>
                <div class="action-items">
                    <?php if ($awaiting_approval > 0): ?>
                        <a href="reviews.php?status=pending" class="action-item warning">
                            <div class="action-icon">
                                <i class="fas fa-clock" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Pending Approval</h4>
                                <small class="text-muted">Reviews awaiting moderation</small>
                            </div>
                            <div class="action-count"><?= $awaiting_approval ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($old_pending_reviews > 0): ?>
                        <a href="reviews.php?status=pending&old=1" class="action-item danger">
                            <div class="action-icon">
                                <i class="fas fa-history" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Old Pending Reviews</h4>
                                <small class="text-muted">Pending for 3+ days</small>
                            </div>
                            <div class="action-count"><?= $old_pending_reviews ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($low_rated_reviews > 0): ?>
                        <a href="reviews.php?rating=low" class="action-item info">
                            <div class="action-icon">
                                <i class="fas fa-star-half-alt" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Low Rated Reviews</h4>
                                <small class="text-muted">2 stars or below - needs attention</small>
                            </div>
                            <div class="action-count"><?= $low_rated_reviews ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                    <p>All reviews up to date! No pending actions.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Review Statistics Card -->
    <div class="app-card" role="region" aria-labelledby="review-stats-title">
        <div class="app-header" role="banner" aria-labelledby="review-stats-title">
            <h3 id="review-stats-title">Review Statistics</h3>
            <i class="fas fa-chart-bar header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= number_format($local_reviews_total) ?> total reviews"><?= number_format($local_reviews_total) ?> total</span>
        </div>
        <div class="app-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= count($reviews) ?></div>
                    <div class="stat-label">New Today</div>
                    <div class="stat-sublabel">Reviews &lt;1 day old</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($awaiting_approval) ?></div>
                    <div class="stat-label">Awaiting Approval</div>
                    <div class="stat-sublabel">Pending review</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($approved_reviews) ?></div>
                    <div class="stat-label">Approved</div>
                    <div class="stat-sublabel">Live reviews</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $avg_rating ? number_format($avg_rating, 1) : 0 ?></div>
                    <div class="stat-label">Avg Rating</div>
                    <div class="stat-sublabel">Out of <?= max_stars ?> stars</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Activity Card -->
    <div class="app-card" role="region" aria-labelledby="review-activity-title">
        <div class="app-header" role="banner" aria-labelledby="review-activity-title">
            <h3 id="review-activity-title">Recent Activity</h3>
            <i class="fas fa-activity header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= number_format($recent_week_reviews) ?> this week"><?= number_format($recent_week_reviews) ?> this week</span>
        </div>
        <div class="app-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($reviews_page_total) ?></div>
                    <div class="stat-label">Active Pages</div>
                    <div class="stat-sublabel">Pages with reviews</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($high_rated_reviews) ?></div>
                    <div class="stat-label">High Rated</div>
                    <div class="stat-sublabel">4+ star reviews</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $approved_reviews ? number_format(($high_rated_reviews / $approved_reviews) * 100, 1) : 0 ?>%</div>
                    <div class="stat-label">Satisfaction</div>
                    <div class="stat-sublabel">High rating ratio</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $awaiting_approval ? number_format(($awaiting_approval / ($awaiting_approval + $approved_reviews)) * 100, 1) : 0 ?>%</div>
                    <div class="stat-label">Pending</div>
                    <div class="stat-sublabel">Approval queue</div>
                </div>
            </div>
        </div>
    </div>



<!-- Recent Reviews Card -->
<div class="row mt-4">
    <div class="col-12 mb-4">
        <div class="card h-100" role="region" aria-labelledby="recent-reviews-heading">
            <div class="card-header d-flex align-items-center">
                 
                <h6 id="recent-reviews-heading" class="card-title mb-0">New Reviews</h6>
                <small class="text-muted ms-auto">Submitted today</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" role="table" aria-label="Recent reviews">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col" class="responsive-hidden">Content</th>
                                <th scope="col">Rating</th>
                                <th scope="col" class="responsive-hidden">Likes</th>
                                <th scope="col" class="responsive-hidden">Page ID</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="responsive-hidden">Date</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($reviews)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                There are no recent reviews.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="profile-img me-3">
                                        <span class="badge rounded-circle" style="background-color:<?=color_from_string($review['display_name'])?>"><?=strtoupper(substr($review['display_name'], 0, 1))?></span>
                                    </div>
                                    <div>
                                        <div class="fw-medium"><?=htmlspecialchars($review['display_name'], ENT_QUOTES)?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="responsive-hidden">
                                <div class="truncated-txt">
                                    <?=nl2br(htmlspecialchars($review['content'], ENT_QUOTES))?>
                                    <?php if ($review['imgs']): ?>
                                    <div class="imgs mt-2">
                                        <?php foreach (explode(',', $review['imgs']) as $img): ?>
                                        <a href="../<?=htmlspecialchars($img, ENT_QUOTES)?>" target="_blank" class="me-1">
                                            <img src="../<?=htmlspecialchars($img, ENT_QUOTES)?>" alt="Review Image" width="32" height="32" class="rounded">
                                        </a>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="rating" title="<?=$review['rating']?> star rating">                    
                                    <?=str_repeat('<i class="fas fa-star text-warning"></i>', $review['rating'])?>
                                    <?php if (max_stars-$review['rating'] > 0): ?>
                                    <?=str_repeat('<i class="far fa-star text-muted"></i>', max_stars-$review['rating'])?>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="responsive-hidden">
                                <span class="badge bg-light text-dark"><?=number_format($review['likes'])?></span>
                            </td>
                            <td class="responsive-hidden">
                                <?php if ($review['url']): ?>
                                <a href="<?=htmlspecialchars($review['url'], ENT_QUOTES)?>" target="_blank" class="link">
                                    <?=$review['page_id']?>
                                </a>
                                <?php else: ?>
                                <?=$review['page_id']?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($review['approved'] == 1): ?>
                                <span class="badge bg-success">Approved</span>
                                <?php elseif ($review['approved'] == 0): ?>
                                <span class="badge bg-danger">Disapproved</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="responsive-hidden">
                                <small class="text-muted"><?=date('F j, Y H:ia', strtotime($review['submit_date']))?></small>
                            </td>
                            <td>
                                <div class="table-dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="table-dropdown-items dropdown-menu" role="menu" aria-label="Review Actions">
                                        <a class="dropdown-item" href="review.php?id=<?=$review['id']?>">
                                            <i class="fas fa-eye me-2"></i>View
                                        </a>
                                        <a class="dropdown-item" href="review.php?id=<?=$review['id']?>">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a>
                                        <?php if ($review['approved'] != 1): ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-success" href="review_dash.php?approve=<?=$review['id']?>">
                                            <i class="fas fa-check me-2"></i>Approve
                                        </a>
                                        <?php endif; ?>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger" href="review_dash.php?delete=<?=$review['id']?>" onclick="return confirm('Are you sure you want to delete this review?')">
                                            <i class="fas fa-trash me-2"></i>Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Awaiting Approval Card -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card h-100" role="region" aria-labelledby="awaiting-approval-heading">
            <div class="card-header d-flex align-items-center">
                 
                <h6 id="awaiting-approval-heading" class="card-title mb-0">Awaiting Approval</h6>
                <small class="text-muted ms-auto"><?=number_format($awaiting_approval)?> pending</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" role="table" aria-label="Reviews awaiting approval">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col" class="responsive-hidden">Content</th>
                                <th scope="col">Rating</th>
                                <th scope="col" class="responsive-hidden">Likes</th>
                                <th scope="col" class="responsive-hidden">Page ID</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="responsive-hidden">Date</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reviews_awaiting_approval)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                                    There are no reviews awaiting approval.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($reviews_awaiting_approval as $review): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="profile-img me-3">
                                            <span class="badge rounded-circle" style="background-color:<?=color_from_string($review['display_name'])?>"><?=strtoupper(substr($review['display_name'], 0, 1))?></span>
                                        </div>
                                        <div>
                                            <div class="fw-medium"><?=htmlspecialchars($review['display_name'], ENT_QUOTES)?></div>
                                            <?php if ($review['email']): ?>
                                            <small class="text-muted"><?=htmlspecialchars($review['email'], ENT_QUOTES)?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="responsive-hidden">
                                    <div class="truncated-txt">
                                        <?=nl2br(htmlspecialchars($review['content'], ENT_QUOTES))?>
                                        <?php if ($review['imgs']): ?>
                                        <div class="imgs mt-2">
                                            <?php foreach (explode(',', $review['imgs']) as $img): ?>
                                            <a href="../<?=htmlspecialchars($img, ENT_QUOTES)?>" target="_blank" class="me-1">
                                                <img src="../<?=htmlspecialchars($img, ENT_QUOTES)?>" alt="Review Image" width="32" height="32" class="rounded">
                                            </a>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="rating" title="<?=$review['rating']?> star rating">                    
                                        <?=str_repeat('<i class="fas fa-star text-warning"></i>', $review['rating'])?>
                                        <?php if (max_stars-$review['rating'] > 0): ?>
                                        <?=str_repeat('<i class="far fa-star text-muted"></i>', max_stars-$review['rating'])?>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class="responsive-hidden">
                                    <span class="badge bg-light text-dark"><?=number_format($review['likes'])?></span>
                                </td>
                                <td class="responsive-hidden">
                                    <?php if ($review['url']): ?>
                                    <a href="<?=htmlspecialchars($review['url'], ENT_QUOTES)?>" target="_blank" class="link">
                                        <?=$review['page_id']?>
                                    </a>
                                    <?php else: ?>
                                    <?=$review['page_id']?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($review['approved'] == 1): ?>
                                    <span class="badge bg-success">Approved</span>
                                    <?php elseif ($review['approved'] == 0): ?>
                                    <span class="badge bg-danger">Disapproved</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="responsive-hidden">
                                    <small class="text-muted"><?=date('F j, Y H:ia', strtotime($review['submit_date']))?></small>
                                </td>
                                <td class="text-end">
                                    <div class="table-dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="table-dropdown-items dropdown-menu" role="menu" aria-label="Review Actions">
                                            <a class="dropdown-item" href="review.php?id=<?=$review['id']?>">
                                                <i class="fas fa-eye me-2"></i>View
                                            </a>
                                            <a class="dropdown-item" href="review.php?id=<?=$review['id']?>">
                                                <i class="fas fa-edit me-2"></i>Edit
                                            </a>
                                            <?php if ($review['approved'] != 1): ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-success" href="review_dash.php?approve=<?=$review['id']?>">
                                                <i class="fas fa-check me-2"></i>Approve
                                            </a>
                                            <?php endif; ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item text-danger" href="review_dash.php?delete=<?=$review['id']?>" onclick="return confirm('Are you sure you want to delete this review?')">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo template_admin_footer(); ?>