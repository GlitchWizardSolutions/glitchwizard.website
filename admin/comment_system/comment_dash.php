<?php
include '../assets/includes/main.php';

// Use simple triangle icons for sort direction, matching accounts.php canonical pattern
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>', // ▲
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>' // ▼
];

// Get sorting parameters
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : '';
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';

// Current date in MySQL DATETIME format
$date = date('Y-m-d H:i:s');

// Basic counts
$comments_total = $pdo->query('SELECT COUNT(*) AS total FROM comments')->fetchColumn();
$awaiting_approval = $pdo->query('SELECT COUNT(*) AS total FROM comments WHERE approved = 0')->fetchColumn();
$comments_page_total = $pdo->query('SELECT COUNT(*) AS total FROM comment_page_details')->fetchColumn();

// Today's comments
$comments_today = $pdo->query('SELECT COUNT(*) AS total FROM comments WHERE cast(submit_date as DATE) = cast("' . $date . '" as DATE)')->fetchColumn();

// This week's comments
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d', strtotime('sunday this week'));
$comments_this_week = $pdo->query("SELECT COUNT(*) AS total FROM comments WHERE DATE(submit_date) BETWEEN '$week_start' AND '$week_end'")->fetchColumn();

// This month's comments  
$month_start = date('Y-m-01');
$month_end = date('Y-m-t');
$comments_this_month = $pdo->query("SELECT COUNT(*) AS total FROM comments WHERE DATE(submit_date) BETWEEN '$month_start' AND '$month_end'")->fetchColumn();

// Approved comments count
$approved_comments = $pdo->query('SELECT COUNT(*) AS total FROM comments WHERE approved = 1')->fetchColumn();

// Featured comments count
$featured_comments = $pdo->query('SELECT COUNT(*) AS total FROM comments WHERE featured = 1')->fetchColumn();

// Comments with votes
$voted_comments = $pdo->query('SELECT COUNT(*) AS total FROM comments WHERE votes > 0')->fetchColumn();

// Top voted comment
$top_voted_comment = $pdo->query('SELECT c.*, a.username AS account_display_name, p.url FROM comments c LEFT JOIN accounts a ON a.id = c.account_id LEFT JOIN comment_page_details p ON p.page_id = c.page_id ORDER BY c.votes DESC LIMIT 1')->fetch(PDO::FETCH_ASSOC);

// Recent comments for display (last 3)
$recent_comments = $pdo->query('SELECT c.*, a.email, p.url, a.avatar, a.username AS account_display_name, a.banned FROM comments c LEFT JOIN accounts a ON a.id = c.account_id LEFT JOIN comment_page_details p ON p.page_id = c.page_id ORDER BY c.submit_date DESC LIMIT 3')->fetchAll(PDO::FETCH_ASSOC);

// Comments awaiting approval (for action items)
$comments_awaiting_approval = $pdo->query('SELECT c.*, a.email, p.url, a.avatar, a.username AS account_display_name, a.banned FROM comments c LEFT JOIN accounts a ON a.id = c.account_id LEFT JOIN comment_page_details p ON p.page_id = c.page_id WHERE c.approved = 0 ORDER BY c.submit_date DESC LIMIT 3')->fetchAll(PDO::FETCH_ASSOC);

// Most voted comments (for display)
$comments_most_votes = $pdo->query('SELECT c.*, a.email, p.url, a.avatar, a.username AS account_display_name FROM comments c LEFT JOIN accounts a ON a.id = c.account_id LEFT JOIN comment_page_details p ON p.page_id = c.page_id WHERE c.votes > 0 ORDER BY c.votes DESC LIMIT 3')->fetchAll(PDO::FETCH_ASSOC);

// Calculate action items total
$total_action_items = $awaiting_approval;

// Analytics data (simplified)
if (isset($_GET['date_start'], $_GET['date_end'])) {
    $start = new DateTime($_GET['date_start']);
    $end = new DateTime($_GET['date_end']);
    if ($end <= $start) {
        $end = new DateTime($_GET['date_start']);
    }
} else {
    $start = new DateTime();         
    $start->modify('-6 days'); 
    $end = new DateTime();
}
$analytics_period_comments = $pdo->query('SELECT COUNT(*) AS total FROM comments WHERE submit_date BETWEEN "' . $start->format('Y-m-d 00:00:00') . '" AND "' . $end->format('Y-m-d 23:59:59') . '"')->fetchColumn();
?>
<?=template_admin_header('Comment System Dashboard', 'comments')?>

<div class="content-title">
    <div class="title">
        <div class="icon">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,4A8,8 0 0,1 20,12C20,14.4 19,16.5 17.3,18C15.9,16.7 14,16 12,16C10,16 8.2,16.7 6.7,18C5,16.5 4,14.4 4,12A8,8 0 0,1 12,4M14,5.89C13.62,5.9 13.26,6.15 13.1,6.54L11.81,9.77L11.71,10C11,10.13 10.41,10.6 10.14,11.26C9.73,12.29 10.23,13.45 11.26,13.86C12.29,14.27 13.45,13.77 13.86,12.74C14.12,12.08 14,11.32 13.57,10.76L13.67,10.5L14.96,7.29L14.97,7.26C15.17,6.75 14.92,6.17 14.41,5.96C14.28,5.91 14.15,5.89 14,5.89M10,6A1,1 0 0,0 9,7A1,1 0 0,0 10,8A1,1 0 0,0 11,7A1,1 0 0,0 10,6M7,9A1,1 0 0,0 6,10A1,1 0 0,0 7,11A1,1 0 0,0 8,10A1,1 0 0,0 7,9M17,9A1,1 0 0,0 16,10A1,1 0 0,0 17,11A1,1 0 0,0 18,10A1,1 0 0,0 17,9Z" /></svg>
        </div>
        <div class="txt">
            <h2>Comment System Dashboard</h2>
            <p>Monitor comments, engagement, and moderation activities.</p>
        </div>
    </div>
</div>



<!-- Comment Dashboard Cards Grid -->
<div class="dashboard-apps">
    <!-- Comment Quick Actions Card -->
    <div class="app-card" role="region" aria-labelledby="comment-quick-actions-title">
        <div class="app-header events-header" role="banner" aria-labelledby="comment-quick-actions-title">
            <h3 id="comment-quick-actions-title">Quick Actions</h3>
            <i class="bi bi-lightning-charge-fill header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Comment management actions">Manage</span>
        </div>
        <div class="app-body">
            <div class="quick-actions">
                <a href="comments.php" class="quick-action primary">
                    <div class="action-icon">
                        <i class="bi bi-chat-text-fill" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Manage Comments</h4>
                        <small class="text-muted">View and moderate comments</small>
                    </div>
                </a>
                <a href="comment_table_transfer.php" class="quick-action secondary">
                    <div class="action-icon">
                        <i class="bi bi-arrow-left-right" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Import/Export</h4>
                        <small class="text-muted">Data transfer operations</small>
                    </div>
                </a>
                <a href="reports.php" class="quick-action warning">
                    <div class="action-icon">
                        <i class="bi bi-flag-fill" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Reports</h4>
                        <small class="text-muted">View comment reports</small>
                    </div>
                </a>
                <a href="comments.php?approved=0" class="quick-action info">
                    <div class="action-icon">
                        <i class="bi bi-clock-fill" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Pending Comments</h4>
                        <small class="text-muted">Review awaiting approval</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="actions-title">
            <h3 id="actions-title">Action Items</h3>
            <i class="bi bi-exclamation-triangle-fill header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $total_action_items ?> items requiring attention"><?= $total_action_items ?> items</span>
        </div>
        <div class="app-body">
            <?php if ($total_action_items > 0): ?>
                <div class="action-items">
                    <?php if ($awaiting_approval > 0): ?>
                        <a href="comments.php?approved=0" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-clock-history" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Pending Comments</h4>
                                <small class="text-muted">Need moderation</small>
                            </div>
                            <div class="action-count"><?= $awaiting_approval ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <p>All caught up! No pending comments.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Content Overview Card -->
    <div class="app-card" role="region" aria-labelledby="content-title">
        <div class="app-header accounts-header" role="banner" aria-labelledby="content-title">
            <h3 id="content-title">Comment Overview</h3>
            <i class="bi bi-chat-dots-fill header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $comments_total ?> total comments"><?= number_format($comments_total) ?> total</span>
        </div>
        <div class="app-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($approved_comments) ?></div>
                    <div class="stat-label">Approved Comments</div>
                    <div class="stat-progress">
                        <div class="progress-bar success" style="width: <?= $comments_total > 0 ? round(($approved_comments / $comments_total) * 100) : 0 ?>%"></div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($comments_page_total) ?></div>
                    <div class="stat-label">Pages with Comments</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($featured_comments) ?></div>
                    <div class="stat-label">Featured Comments</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($comments_this_month) ?></div>
                    <div class="stat-label">Comments This Month</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Engagement Metrics Card -->
    <div class="app-card" role="region" aria-labelledby="engagement-title">
        <div class="app-header documents-header" role="banner" aria-labelledby="engagement-title">
            <h3 id="engagement-title">Engagement</h3>
            <i class="bi bi-graph-up-arrow header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $voted_comments ?> voted comments"><?= $voted_comments ?> voted</span>
        </div>
        <div class="app-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($comments_today) ?></div>
                    <div class="stat-label">Comments Today</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($comments_this_week) ?></div>
                    <div class="stat-label">Comments This Week</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($voted_comments) ?></div>
                    <div class="stat-label">Comments with Votes</div>
                </div>
                <?php if ($top_voted_comment): ?>
                <div class="stat-item full-width">
                    <div class="stat-value"><?= $top_voted_comment['votes'] ?> votes</div>
                    <div class="stat-label">Top Comment: <?= htmlspecialchars(substr(strip_tags($top_voted_comment['content']), 0, 30)) ?><?= strlen(strip_tags($top_voted_comment['content'])) > 30 ? '...' : '' ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Analytics Card -->
    <div class="app-card" role="region" aria-labelledby="analytics-title">
        <div class="app-header client-header" role="banner" aria-labelledby="analytics-title">
            <h3 id="analytics-title">Comments Analytics</h3>
            <i class="bi bi-bar-chart-fill header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Period analytics"><?= date('M j', $start->getTimestamp()) ?> - <?= date('M j', $end->getTimestamp()) ?></span>
        </div>
        <div class="app-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($analytics_period_comments) ?></div>
                    <div class="stat-label">Comments in Period</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $analytics_period_comments > 0 ? number_format($analytics_period_comments / max(1, $end->diff($start)->days + 1), 1) : 0 ?></div>
                    <div class="stat-label">Avg. Comments/Day</div>
                </div>
                <div class="stat-item full-width">
                    <form action="comment_dash.php" method="get" class="date-range" style="display: flex; gap: 8px; align-items: center; justify-content: center;">
                        <input type="date" name="date_start" value="<?=$start->format('Y-m-d')?>" onchange="this.form.submit()" style="padding: 4px; font-size: 12px;">
                        <span style="font-size: 12px;">to</span>
                        <input type="date" name="date_end" value="<?=$end->format('Y-m-d')?>" onchange="this.form.submit()" style="padding: 4px; font-size: 12px;">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-title" style="margin-top:40px">
    <div class="title">
        <div class="icon alt">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M11,6H13V9H16V11H13V14H11V11H8V9H11V6Z" /></svg>        
        </div>
        <div class="txt">
            <h2>New Comments</h2>
            <p>Comments posted in the last &lt;1 day.</p>
        </div>
    </div>
</div>
<br>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Recent Comments</h5>
        <div class="d-flex gap-2 align-items-center ms-auto">
            <small class="text-muted"><?= count($recent_comments) ?> comments</small>
        </div>
    </div>
    <div class="card-body">
        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th colspan="2" style="text-align:left;">
                            <?php $q = $_GET; $q['order_by'] = 'username'; $q['order'] = ($order_by == 'username' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Comment<?= $order_by == 'username' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="responsive-hidden">
                            <?php $q = $_GET; $q['order_by'] = 'votes'; $q['order'] = ($order_by == 'votes' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Votes<?= $order_by == 'votes' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="responsive-hidden">
                            <?php $q = $_GET; $q['order_by'] = 'page_id'; $q['order'] = ($order_by == 'page_id' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Page ID<?= $order_by == 'page_id' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="responsive-hidden">
                            <?php $q = $_GET; $q['order_by'] = 'featured'; $q['order'] = ($order_by == 'featured' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Featured<?= $order_by == 'featured' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th>
                            <?php $q = $_GET; $q['order_by'] = 'approved'; $q['order'] = ($order_by == 'approved' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Status<?= $order_by == 'approved' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="responsive-hidden">
                            <?php $q = $_GET; $q['order_by'] = 'submit_date'; $q['order'] = ($order_by == 'submit_date' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Date<?= $order_by == 'submit_date' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
            <tbody>
                <?php if (empty($recent_comments)): ?>
                <tr>
                    <td colspan="20" class="no-results">There are no recent comments.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($recent_comments as $c): ?>
                <tr>
                    <td class="img"> 
                        <div class="profile-img">
                            <?php if (!empty($c['avatar']) && file_exists('../' . $c['avatar'])): ?>
                            <img src="../<?=$c['avatar']?>" alt="<?=htmlspecialchars($c['account_display_name'], ENT_QUOTES)?>" width="40" height="40">
                            <?php else: ?>
                            <span style="background-color:<?=color_from_string($c['account_display_name'] ? $c['account_display_name'] : $c['username'])?>"><?=strtoupper(substr($c['account_display_name'] ? $c['account_display_name'] : $c['username'], 0, 1))?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="title-caption">
                        <a href="comment.php?id=<?=$c['id']?>" title="Manage Comment" class="<?=$c['banned'] ? 'banned' : ''?>">
                            <?=htmlspecialchars($c['account_display_name'] ? $c['account_display_name'] : $c['username'], ENT_QUOTES)?>
                            <?php if ($c['banned']): ?>
                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>Banned User</title><path d="M12,4A4,4 0 0,1 16,8C16,9.95 14.6,11.58 12.75,11.93L8.07,7.25C8.42,5.4 10.05,4 12,4M12.28,14L18.28,20L20,21.72L18.73,23L15.73,20H4V18C4,16.16 6.5,14.61 9.87,14.14L2.78,7.05L4.05,5.78L12.28,14M20,18V19.18L15.14,14.32C18,14.93 20,16.35 20,18Z" /></svg>
                            <?php endif; ?>
                        </a>
                        <div class="truncated-txt">
                            <div class="short"><?=mb_strimwidth(strip_tags(str_replace('<br>', ' ', $c['content'])), 0, 50, "...")?></div>
                            <div class="full"><?=$c['content']?></div>
                            <a href="#" class="read-more">View</a>
                        </div>
                    </td>
                    <td class="responsive-hidden"><span class="grey small"><?=number_format($c['votes'])?></span></td>
                    <td class="responsive-hidden alt"><?=$c['url'] ? '<a href="' . htmlspecialchars($c['url'], ENT_QUOTES) . '" title="' . $c['url'] . '" target="_blank" class="link1">' . $c['page_id'] . '</a>' : $c['page_id']?></td>
                    <td class="responsive-hidden">
                        <?php if ($c['featured']): ?>
                        <svg class="yes" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>Not Featured</title><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>
                        <?php else: ?>
                        <svg class="no" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>Featured</title><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($c['approved']): ?>
                        <span class="green small">Approved</span>
                        <?php else: ?>
                        <span class="orange small">Pending Approval</span>
                        <?php endif; ?>
                    </td>
                    <td class="responsive-hidden alt"><?=date('F j, Y H:ia', strtotime($c['submit_date']))?></td>
                    <td class="actions">
                        <div class="table-dropdown">
                            <button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for comment">
                                <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                    <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                </svg>
                            </button>
                            <div class="table-dropdown-items">
                                <a href="comment.php?id=<?=$c['id']?>">
                                    <span class="icon">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </span>
                                    Edit
                                </a>
                                <a href="comment.php?reply=<?=$c['id']?>">
                                    <span class="icon">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10,9V5L3,12L10,19V14.9C15,14.9 18.5,16.5 21,20C20,15 17,10 10,9Z" /></svg>                                    
                                    </span>
                                    Reply
                                </a>
                                <?php if (!$c['featured']): ?>
                                <a class="green" href="comments.php?feature=<?=$c['id']?>" onclick="return confirm('Are you sure you want to feature this comment?')">
                                    <span class="icon">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>
                                    </span>    
                                    Feature
                                </a>
                                <?php else: ?>
                                <a class="red" href="comments.php?unfeature=<?=$c['id']?>" onclick="return confirm('Are you sure you want to unfeature this comment?')">
                                    <span class="icon">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                                    </span>    
                                    Unfeature
                                </a> 
                                <?php endif; ?>
                                <?php if (!$c['approved']): ?>
                                <a class="green" href="comments.php?approve=<?=$c['id']?>" onclick="return confirm('Are you sure you want to approve this comment?')">
                                    <span class="icon">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>
                                    </span>    
                                    Approve
                                </a>
                                <?php endif; ?>
                                <?php if ($c['account_id'] > 0 && !$c['banned']): ?>
                                <a class="green" href="comments.php?ban=<?=$c['account_id']?>" onclick="return confirm('Are you sure you want to ban this user?')">
                                    <span class="icon">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>
                                    </span>    
                                    Ban User
                                </a>
                                <?php elseif ($c['account_id'] > 0 && $c['banned']): ?>
                                <a class="red" href="comments.php?unban=<?=$c['account_id']?>" onclick="return confirm('Are you sure you want to unban this user?')">
                                    <span class="icon">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                                    </span>    
                                    Unban User
                                </a> 
                                <?php endif; ?>
                                <a class="red" href="comments.php?delete=<?=$c['id']?>" onclick="return confirm('Are you sure you want to delete this comment?')">
                                    <span class="icon">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                    </span>    
                                    Delete
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
</div></div> 
<div class="content-title" style="margin-top:40px">
    <div class="title">
        <div class="icon alt">
            <i class="bi bi-hourglass-split" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Comments Awaiting Approval</h2>
            <p>Comments awaiting admin approval.</p>
        </div>
    </div>
</div>
<br>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Pending Approval</h5>
        <div class="d-flex gap-2 align-items-center ms-auto">
            <small class="text-muted"><?= count($comments_awaiting_approval) ?> comments</small>
        </div>
    </div>
    <div class="card-body">
        <div class="table">
            <table>
                <thead>
                    <tr>
                        <th colspan="2" style="text-align:left;">
                            <?php $q = $_GET; $q['order_by'] = 'username'; $q['order'] = ($order_by == 'username' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Comment<?= $order_by == 'username' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="responsive-hidden">
                            <?php $q = $_GET; $q['order_by'] = 'votes'; $q['order'] = ($order_by == 'votes' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Votes<?= $order_by == 'votes' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="responsive-hidden">
                            <?php $q = $_GET; $q['order_by'] = 'page_id'; $q['order'] = ($order_by == 'page_id' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Page ID<?= $order_by == 'page_id' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="responsive-hidden">
                            <?php $q = $_GET; $q['order_by'] = 'featured'; $q['order'] = ($order_by == 'featured' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Featured<?= $order_by == 'featured' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th>
                            <?php $q = $_GET; $q['order_by'] = 'approved'; $q['order'] = ($order_by == 'approved' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Status<?= $order_by == 'approved' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th class="responsive-hidden">
                            <?php $q = $_GET; $q['order_by'] = 'submit_date'; $q['order'] = ($order_by == 'submit_date' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="sort-header">Date<?= $order_by == 'submit_date' ? $table_icons[strtolower($order)] : '' ?></a>
                        </th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
            <tbody>
                <?php if (empty($comments_awaiting_approval)): ?>
                <tr>
                    <td colspan="20" class="no-results">There are no comments awaiting approval.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($comments_awaiting_approval as $c): ?>
                <tr>
                    <td class="img"> 
                        <div class="profile-img">
                            <?php if (!empty($c['avatar']) && file_exists('../' . $c['avatar'])): ?>
                            <img src="../<?=$c['avatar']?>" alt="<?=htmlspecialchars($c['account_display_name'], ENT_QUOTES)?>" width="40" height="40">
                            <?php else: ?>
                            <span style="background-color:<?=color_from_string($c['account_display_name'] ? $c['account_display_name'] : $c['username'])?>"><?=strtoupper(substr($c['account_display_name'] ? $c['account_display_name'] : $c['username'], 0, 1))?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="title-caption">
                        <a href="comment.php?id=<?=$c['id']?>" title="Manage Comment" class="<?=$c['banned'] ? 'banned' : ''?>">
                            <?=htmlspecialchars($c['account_display_name'] ? $c['account_display_name'] : $c['username'], ENT_QUOTES)?>
                            <?php if ($c['banned']): ?>
                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>Banned User</title><path d="M12,4A4,4 0 0,1 16,8C16,9.95 14.6,11.58 12.75,11.93L8.07,7.25C8.42,5.4 10.05,4 12,4M12.28,14L18.28,20L20,21.72L18.73,23L15.73,20H4V18C4,16.16 6.5,14.61 9.87,14.14L2.78,7.05L4.05,5.78L12.28,14M20,18V19.18L15.14,14.32C18,14.93 20,16.35 20,18Z" /></svg>
                            <?php endif; ?>
                        </a>
                        <div class="truncated-txt">
                            <div class="short"><?=mb_strimwidth(strip_tags(str_replace('<br>', ' ', $c['content'])), 0, 50, "...")?></div>
                            <div class="full"><?=$c['content']?></div>
                            <a href="#" class="read-more">View</a>
                        </div>
                    </td>
                    <td class="responsive-hidden"><span class="grey small"><?=number_format($c['votes'])?></span></td>
                    <td class="responsive-hidden alt"><?=$c['url'] ? '<a href="' . htmlspecialchars($c['url'], ENT_QUOTES) . '" title="' . $c['url'] . '" target="_blank" class="link1">' . $c['page_id'] . '</a>' : $c['page_id']?></td>
                    <td class="responsive-hidden">
                        <?php if ($c['featured']): ?>
                        <svg class="yes" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>Not Featured</title><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>
                        <?php else: ?>
                        <svg class="no" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><title>Featured</title><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($c['approved']): ?>
                        <span class="green small">Approved</span>
                        <?php else: ?>
                        <span class="orange small">Pending Approval</span>
                        <?php endif; ?>
                    </td>
                    <td class="responsive-hidden alt"><?=date('F j, Y H:ia', strtotime($c['submit_date']))?></td>
                    <td class="actions">
                        <div class="table-dropdown">
                            <button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for comment">
                                <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                    <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                </svg>
                            </button>
                            <div class="table-dropdown-items">
                                <a href="comment.php?id=<?=$c['id']?>">
                                    <span class="icon">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </span>
                                    Edit
                                </a>
                                <a href="comment.php?reply=<?=$c['id']?>">
                                    <span class="icon">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10,9V5L3,12L10,19V14.9C15,14.9 18.5,16.5 21,20C20,15 17,10 10,9Z" /></svg>                                    
                                    </span>
                                    Reply
                                </a>
                                <?php if (!$c['featured']): ?>
                                <a class="green" href="comments.php?feature=<?=$c['id']?>" onclick="return confirm('Are you sure you want to feature this comment?')">
                                    <span class="icon">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>
                                    </span>    
                                    Feature
                                </a>
                                <?php else: ?>
                                <a class="red" href="comments.php?unfeature=<?=$c['id']?>" onclick="return confirm('Are you sure you want to unfeature this comment?')">
                                    <span class="icon">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                                    </span>    
                                    Unfeature
                                </a> 
                                <?php endif; ?>
                                <?php if (!$c['approved']): ?>
                                <a class="green" href="comments.php?approve=<?=$c['id']?>" onclick="return confirm('Are you sure you want to approve this comment?')">
                                    <span class="icon">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>
                                    </span>    
                                    Approve
                                </a>
                                <?php endif; ?>
                                <?php if ($c['account_id'] > 0 && !$c['banned']): ?>
                                <a class="green" href="comments.php?ban=<?=$c['account_id']?>" onclick="return confirm('Are you sure you want to ban this user?')">
                                    <span class="icon">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>
                                    </span>    
                                    Ban User
                                </a>
                                <?php elseif ($c['account_id'] > 0 && $c['banned']): ?>
                                <a class="red" href="comments.php?unban=<?=$c['account_id']?>" onclick="return confirm('Are you sure you want to unban this user?')">
                                    <span class="icon">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                                    </span>    
                                    Unban User
                                </a> 
                                <?php endif; ?>
                                <a class="red" href="comments.php?delete=<?=$c['id']?>" onclick="return confirm('Are you sure you want to delete this comment?')">
                                    <span class="icon">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                    </span>    
                                    Delete
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

<?=template_admin_footer()?>
