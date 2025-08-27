<?php
/* 
 * Admin Dashboard
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: index.php
 * LOCATION: /public_html/admin/
 * PURPOSE: Main admin dashboard and control center
 * DETAILED DESCRIPTION:
 * This file serves as the main entry point and dashboard for the admin panel.
 * It provides a centralized interface for accessing all administrative functions,
 * displaying system status, recent activity, and quick-access action cards for
 * common administrative tasks.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /private/gws-universal-config.php
 * - /private/role-definitions.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - System status overview
 * - Quick-access action cards
 * - Recent activity feed
 * - System notifications
 * - Admin shortcuts
 */
 

include_once 'assets/includes/main.php';

$accounts_total = $pdo->query('SELECT COUNT(*) AS total FROM accounts')->fetchColumn();
$active_accounts = $pdo->query('SELECT COUNT(*) AS total FROM accounts WHERE last_seen > date_sub(now(), interval 1 month)')->fetchColumn();
$inactive_accounts = $pdo->query('SELECT COUNT(*) AS total FROM accounts WHERE last_seen < date_sub(now(), interval 1 month)')->fetchColumn();
$new_accounts = $pdo->query('SELECT COUNT(*) AS total FROM accounts WHERE registered > date_sub(now(), interval 1 week)')->fetchColumn();

// Additional system metrics
try
{
    $unread_messages_count = $pdo->query('SELECT COUNT(*) AS total FROM messages WHERE `read` = 0')->fetchColumn();
} catch (Exception $e)
{
    $unread_messages_count = 0; // Table might not exist in all installations
}

try
{
    $documents_count = $pdo->query('SELECT COUNT(*) AS total FROM documents')->fetchColumn();
} catch (Exception $e)
{
    $documents_count = 0; // Table might not exist in all installations
}

try
{
    $blog_pages_count = $pdo->query('SELECT COUNT(*) AS total FROM blog_posts WHERE published = 1')->fetchColumn();
} catch (Exception $e)
{
    $blog_pages_count = 0; // Table might not exist in all installations
}

// Current date in MySQL DATETIME format
$date = date('Y-m-d H:i:s');

// Account Summary Metrics for Dashboard
$accounts_total = $pdo->query('SELECT COUNT(*) AS total FROM accounts')->fetchColumn();
$new_accounts = $pdo->query('SELECT COUNT(*) AS total FROM accounts WHERE registered > date_sub(now(), interval 1 month)')->fetchColumn();
$inactive_accounts = $pdo->query('SELECT COUNT(*) AS total FROM accounts WHERE last_seen < date_sub(now(), interval 1 month)')->fetchColumn();
$active_accounts = $pdo->query('SELECT COUNT(*) AS total FROM accounts WHERE last_seen > date_sub(now(), interval 1 month)')->fetchColumn();

// Accounts Metrics - Action Items Only
$pending_activations = $pdo->query('SELECT COUNT(*) AS total FROM accounts WHERE activation_code != "activated" AND activation_code != "deactivated"')->fetchColumn();
$pending_approvals = $pdo->query('SELECT COUNT(*) AS total FROM accounts WHERE approved = 0')->fetchColumn();

// Blog Metrics - Action Items Only
$pending_comments = $pdo->query('SELECT COUNT(*) AS total FROM blog_comments WHERE approved = "No"')->fetchColumn();
$draft_posts = $pdo->query('SELECT COUNT(*) AS total FROM blog_posts WHERE active = "No"')->fetchColumn();

// Invoice System Action Items
$overdue_invoices = $pdo->query('SELECT COUNT(*) AS total FROM invoices WHERE due_date < "' . $date . '" AND payment_status = "Unpaid"')->fetchColumn();
$draft_invoices = $pdo->query('SELECT COUNT(*) AS total FROM invoices WHERE payment_status = "Draft"')->fetchColumn();

// Ticket System Action Items  
$pending_tickets = $pdo->query('SELECT COUNT(*) AS total FROM tickets WHERE approved = 0')->fetchColumn();
$high_priority_tickets = $pdo->query('SELECT COUNT(*) AS total FROM tickets WHERE ticket_status = "open" AND priority = "High"')->fetchColumn();

// Poll System Action Items
$awaiting_approval_polls = $pdo->query('SELECT COUNT(*) AS total FROM polls WHERE approved = 0')->fetchColumn();

// Review System Action Items  
$awaiting_approval_reviews = $pdo->query('SELECT COUNT(*) AS total FROM reviews WHERE approved = -1')->fetchColumn();

// Calculate total action items across all systems
$total_action_items = $pending_activations + $pending_approvals + $pending_comments + $draft_posts + 
                     $overdue_invoices + $draft_invoices + $pending_tickets + $high_priority_tickets + 
                     $awaiting_approval_polls + $awaiting_approval_reviews;

// Documents Metrics - Recent activity (last 7 days)
$recent_uploads = 0; // Placeholder for now

// Client Portal Metrics - Placeholder for future expansion  
$pending_requests = 0; // Placeholder for now
?>
<?= template_admin_header('Dashboard', 'dashboard') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-4 px-4 branding-settings-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <span class="header-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
                    Admin Dashboard
                </h6>
                <span class="text-white" style="font-size: 0.875rem;">System Overview</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="container-fluid py-3 px-4">
<br>

<!-- Admin Dashboard Cards Grid -->
<div class="dashboard-apps">
    <!-- Overall Action Items Summary Card -->
    <div class="app-card" role="region" aria-labelledby="main-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="main-actions-title">
            <h3 id="main-actions-title">System Action Items</h3>
            <i class="bi bi-exclamation-triangle header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $total_action_items ?> total items requiring attention"><?= $total_action_items ?> items</span>
        </div>
        <div class="app-body">
            <?php if ($total_action_items > 0): ?>
                <div class="action-items">
                    <?php if ($pending_activations > 0): ?>
                        <a href="accounts/accounts.php?status=Pending Activation" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-person-time" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Account Activations</h4>
                                <small class="text-muted">Users need account activation</small>
                            </div>
                            <div class="action-count"><?= $pending_activations ?></div>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($overdue_invoices > 0): ?>
                        <a href="invoice_system/invoices.php?payment_status=Unpaid&overdue=1" class="action-item danger">
                            <div class="action-icon">
                                <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Overdue Invoices</h4>
                                <small class="text-muted">Payment past due date</small>
                            </div>
                            <div class="action-count"><?= $overdue_invoices ?></div>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($high_priority_tickets > 0): ?>
                        <a href="ticket_system/tickets.php?priority=High&status=open" class="action-item danger">
                            <div class="action-icon">
                                <i class="bi bi-exclamation-lg" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>High Priority Tickets</h4>
                                <small class="text-muted">Urgent support tickets</small>
                            </div>
                            <div class="action-count"><?= $high_priority_tickets ?></div>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($pending_comments > 0): ?>
                        <a href="blog/comments.php?approved=No" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-chat-dots" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Pending Comments</h4>
                                <small class="text-muted">Blog comments need approval</small>
                            </div>
                            <div class="action-count"><?= $pending_comments ?></div>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($awaiting_approval_polls > 0): ?>
                        <a href="polling_system/polls.php?approved=No" class="action-item info">
                            <div class="action-icon">
                                <i class="bi bi-clock" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Poll Approvals</h4>
                                <small class="text-muted">Polls awaiting moderation</small>
                            </div>
                            <div class="action-count"><?= $awaiting_approval_polls ?></div>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($awaiting_approval_reviews > 0): ?>
                        <a href="review_system/reviews.php?approved=-1" class="action-item info">
                            <div class="action-icon">
                                <i class="bi bi-star-fill" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Review Approvals</h4>
                                <small class="text-muted">Reviews awaiting moderation</small>
                            </div>
                            <div class="action-count"><?= $awaiting_approval_reviews ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <p>All systems up to date! No pending actions.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Accounts Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="accounts-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="accounts-actions-title">
            <h3 id="accounts-actions-title">Accounts Action Items</h3>
            <i class="bi bi-person-time header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Account related actions">Accounts</span>
        </div>
        <div class="app-body">
            <?php 
            // Get detailed account action items
            $unactivated_accounts = $pdo->query('SELECT COUNT(*) FROM accounts WHERE activation_code = 0')->fetchColumn();
            $admin_accounts = $pdo->query('SELECT COUNT(*) FROM accounts WHERE role = "Admin"')->fetchColumn();
            $recent_inactive = $pdo->query('SELECT COUNT(*) FROM accounts WHERE last_seen < DATE_SUB(NOW(), INTERVAL 7 DAY) AND last_seen > DATE_SUB(NOW(), INTERVAL 30 DAY)')->fetchColumn();
            $account_actions_total = $unactivated_accounts + ($admin_accounts > 5 ? 1 : 0) + $recent_inactive;
            ?>
            <?php if ($account_actions_total > 0): ?>
                <div class="action-items">
                    <?php if ($unactivated_accounts > 0): ?>
                        <a href="accounts/accounts.php?status=unactivated" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-person-time" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Unactivated Accounts</h4>
                                <small class="text-muted">Accounts pending email activation</small>
                            </div>
                            <div class="action-count"><?= $unactivated_accounts ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($admin_accounts > 5): ?>
                        <a href="accounts/accounts.php?role=admin" class="action-item info">
                            <div class="action-icon">
                                <i class="bi bi-shield-lock" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Review Admin Accounts</h4>
                                <small class="text-muted">Many admin accounts detected</small>
                            </div>
                            <div class="action-count"><?= $admin_accounts ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($recent_inactive > 0): ?>
                        <a href="accounts/accounts.php?status=recent_inactive" class="action-item danger">
                            <div class="action-icon">
                                <i class="bi bi-person-x" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Recently Inactive</h4>
                                <small class="text-muted">Users inactive 7-30 days</small>
                            </div>
                            <div class="action-count"><?= $recent_inactive ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <p>All accounts in good standing!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ticket System Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="tickets-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="tickets-actions-title">
            <h3 id="tickets-actions-title">Ticket Action Items</h3>
            <i class="bi bi-ticket-perforated header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Ticket related actions">Tickets</span>
        </div>
        <div class="app-body">
            <?php 
            // Get detailed ticket action items
            $awaiting_approval = $pdo->query('SELECT COUNT(*) FROM tickets WHERE approved = 0')->fetchColumn();
            $high_priority_tickets = $pdo->query('SELECT COUNT(*) FROM tickets WHERE ticket_status = "open" AND priority = "High"')->fetchColumn();
            $old_tickets = $pdo->query('SELECT COUNT(*) FROM tickets WHERE ticket_status = "open" AND created < DATE_SUB(NOW(), INTERVAL 7 DAY)')->fetchColumn();
            $ticket_actions_total = $awaiting_approval + $high_priority_tickets + $old_tickets;
            ?>
            <?php if ($ticket_actions_total > 0): ?>
                <div class="action-items">
                    <?php if ($awaiting_approval > 0): ?>
                        <a href="ticket_system/tickets.php?status=pending" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-clock" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Pending Approval</h4>
                                <small class="text-muted">Tickets need approval</small>
                            </div>
                            <div class="action-count"><?= $awaiting_approval ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($high_priority_tickets > 0): ?>
                        <a href="ticket_system/tickets.php?priority=High&status=open" class="action-item danger">
                            <div class="action-icon">
                                <i class="bi bi-exclamation-lg" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>High Priority</h4>
                                <small class="text-muted">Urgent tickets requiring attention</small>
                            </div>
                            <div class="action-count"><?= $high_priority_tickets ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($old_tickets > 0): ?>
                        <a href="ticket_system/tickets.php?status=open&old=1" class="action-item info">
                            <div class="action-icon">
                                <i class="bi bi-clock-history" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Old Tickets</h4>
                                <small class="text-muted">Open for more than 7 days</small>
                            </div>
                            <div class="action-count"><?= $old_tickets ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <p>All tickets up to date!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Invoice System Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="invoices-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="invoices-actions-title">
            <h3 id="invoices-actions-title">Invoice Action Items</h3>
            <i class="bi bi-receipt header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Invoice related actions">Invoices</span>
        </div>
        <div class="app-body">
            <?php 
            // Get detailed invoice action items
            $overdue_invoices = $pdo->query('SELECT COUNT(*) FROM invoices WHERE due_date < "' . $date . '" AND payment_status = "Unpaid"')->fetchColumn();
            $pending_invoices = $pdo->query('SELECT COUNT(*) FROM invoices WHERE payment_status = "Pending"')->fetchColumn();
            $draft_invoices = $pdo->query('SELECT COUNT(*) FROM invoices WHERE payment_status = "Draft"')->fetchColumn();
            $invoice_actions_total = $overdue_invoices + $pending_invoices + $draft_invoices;
            ?>
            <?php if ($invoice_actions_total > 0): ?>
                <div class="action-items">
                    <?php if ($overdue_invoices > 0): ?>
                        <a href="invoice_system/invoices.php?status=overdue" class="action-item danger">
                            <div class="action-icon">
                                <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Overdue Invoices</h4>
                                <small class="text-muted">Past due date, payment required</small>
                            </div>
                            <div class="action-count"><?= $overdue_invoices ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($pending_invoices > 0): ?>
                        <a href="invoice_system/invoices.php?payment_status=Pending" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-clock" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Pending Payment</h4>
                                <small class="text-muted">Awaiting client payment</small>
                            </div>
                            <div class="action-count"><?= $pending_invoices ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($draft_invoices > 0): ?>
                        <a href="invoice_system/invoices.php?payment_status=Draft" class="action-item info">
                            <div class="action-icon">
                                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Draft Invoices</h4>
                                <small class="text-muted">Ready to send to clients</small>
                            </div>
                            <div class="action-count"><?= $draft_invoices ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <p>All invoices up to date!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Blog System Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="blog-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="blog-actions-title">
            <h3 id="blog-actions-title">Blog Action Items</h3>
            <i class="bi bi-journal-text header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Blog related actions">Blog</span>
        </div>
        <div class="app-body">
            <?php 
            // Get detailed blog action items
            $unread_messages_count = $pdo->query("SELECT COUNT(*) FROM blog_messages WHERE viewed = 'No'")->fetchColumn();
            $pending_comments_count = $pdo->query("SELECT COUNT(*) FROM blog_comments WHERE approved = 'No'")->fetchColumn();
            $draft_posts_count = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE active = 'No'")->fetchColumn();
            $unpublished_pages_count = $pdo->query("SELECT COUNT(*) FROM blog_pages WHERE active = 'No'")->fetchColumn();
            $blog_actions_total = $unread_messages_count + $pending_comments_count + $draft_posts_count + $unpublished_pages_count;
            ?>
            <?php if ($blog_actions_total > 0): ?>
                <div class="action-items">
                    <?php if ($pending_comments_count > 0): ?>
                        <a href="blog/comments.php?approved=No" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-chat-dots" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Pending Comments</h4>
                                <small class="text-muted">Need moderation</small>
                            </div>
                            <div class="action-count"><?= $pending_comments_count ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($draft_posts_count > 0): ?>
                        <a href="blog/posts.php?active=No" class="action-item info">
                            <div class="action-icon">
                                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Draft Posts</h4>
                                <small class="text-muted">Ready to publish</small>
                            </div>
                            <div class="action-count"><?= $draft_posts_count ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($unread_messages_count > 0): ?>
                        <a href="blog/blog-messages.php?viewed=No" class="action-item danger">
                            <div class="action-icon">
                                <i class="bi bi-envelope" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Unread Messages</h4>
                                <small class="text-muted">Contact form submissions</small>
                            </div>
                            <div class="action-count"><?= $unread_messages_count ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($unpublished_pages_count > 0): ?>
                        <a href="blog/pages.php?active=No" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Draft Pages</h4>
                                <small class="text-muted">Need publishing</small>
                            </div>
                            <div class="action-count"><?= $unpublished_pages_count ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <p>All blog content up to date!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Review System Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="reviews-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="reviews-actions-title">
            <h3 id="reviews-actions-title">Review Action Items</h3>
            <i class="bi bi-star-fill header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Review related actions">Reviews</span>
        </div>
        <div class="app-body">
            <?php 
            // Get detailed review action items
            $awaiting_approval_reviews = $pdo->query('SELECT COUNT(*) FROM reviews WHERE approved = -1')->fetchColumn();
            $low_rated_reviews = $pdo->query('SELECT COUNT(*) FROM reviews WHERE approved = 1 AND rating <= 2')->fetchColumn();
            $old_pending_reviews = $pdo->query('SELECT COUNT(*) FROM reviews WHERE approved = -1 AND submit_date < DATE_SUB(NOW(), INTERVAL 3 DAY)')->fetchColumn();
            $review_actions_total = $awaiting_approval_reviews + $low_rated_reviews + $old_pending_reviews;
            ?>
            <?php if ($review_actions_total > 0): ?>
                <div class="action-items">
                    <?php if ($awaiting_approval_reviews > 0): ?>
                        <a href="review_system/reviews.php?status=pending" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-clock" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Pending Approval</h4>
                                <small class="text-muted">Reviews awaiting moderation</small>
                            </div>
                            <div class="action-count"><?= $awaiting_approval_reviews ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($old_pending_reviews > 0): ?>
                        <a href="review_system/reviews.php?status=pending&old=1" class="action-item danger">
                            <div class="action-icon">
                                <i class="bi bi-clock-history" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Old Pending Reviews</h4>
                                <small class="text-muted">Pending for 3+ days</small>
                            </div>
                            <div class="action-count"><?= $old_pending_reviews ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($low_rated_reviews > 0): ?>
                        <a href="review_system/reviews.php?rating=low" class="action-item info">
                            <div class="action-icon">
                                <i class="bi bi-star-half" aria-hidden="true"></i>
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
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <p>All reviews up to date!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Polling System Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="polls-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="polls-actions-title">
            <h3 id="polls-actions-title">Poll Action Items</h3>
            <i class="bi bi-graph-up header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Poll related actions">Polls</span>
        </div>
        <div class="app-body">
            <?php 
            // Get detailed poll action items
            try {
                $awaiting_approval_polls = $pdo->query('SELECT COUNT(*) FROM polls WHERE approved = 0')->fetchColumn();
                $inactive_polls = $pdo->query('SELECT COUNT(*) FROM polls WHERE status = "inactive" OR end_date < NOW()')->fetchColumn();
                $polls_no_votes = $pdo->query('SELECT COUNT(*) FROM polls WHERE id NOT IN (SELECT DISTINCT poll_id FROM poll_votes WHERE poll_id IS NOT NULL)')->fetchColumn();
                $poll_actions_total = $awaiting_approval_polls + $inactive_polls + $polls_no_votes;
            } catch (Exception $e) {
                $poll_actions_total = 0;
                $awaiting_approval_polls = 0;
                $inactive_polls = 0;
                $polls_no_votes = 0;
            }
            ?>
            <?php if ($poll_actions_total > 0): ?>
                <div class="action-items">
                    <?php if ($awaiting_approval_polls > 0): ?>
                        <a href="polling_system/polls.php?approved=0" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-clock" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Pending Approval</h4>
                                <small class="text-muted">Polls awaiting moderation</small>
                            </div>
                            <div class="action-count"><?= $awaiting_approval_polls ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($inactive_polls > 0): ?>
                        <a href="polling_system/polls.php?status=inactive" class="action-item info">
                            <div class="action-icon">
                                <i class="bi bi-pause-circle" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Inactive Polls</h4>
                                <small class="text-muted">Polls that have ended or are disabled</small>
                            </div>
                            <div class="action-count"><?= $inactive_polls ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($polls_no_votes > 0): ?>
                        <a href="polling_system/polls.php?votes=none" class="action-item danger">
                            <div class="action-icon">
                                <i class="bi bi-bar-chart" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Polls With No Votes</h4>
                                <small class="text-muted">May need promotion or review</small>
                            </div>
                            <div class="action-count"><?= $polls_no_votes ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <p>All polls running smoothly!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- System Statistics Card -->
    <div class="app-card" role="region" aria-labelledby="system-stats-title">
        <div class="app-header accounts-header" role="banner" aria-labelledby="system-stats-title">
            <h3 id="system-stats-title">System Statistics</h3>
            <i class="bi bi-pie-chart header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $accounts_total ?> total accounts"><?= $accounts_total ?> accounts</span>
        </div>
        <div class="app-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($active_accounts) ?></div>
                    <div class="stat-label">Active Accounts</div>
                    <div class="stat-progress">
                        <div class="progress-bar" style="width: <?= $accounts_total > 0 ? round(($active_accounts / $accounts_total) * 100) : 0 ?>%"></div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($new_accounts) ?></div>
                    <div class="stat-label">New Accounts</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($total_action_items) ?></div>
                    <div class="stat-label">Action Items</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($blog_pages_count) ?></div>
                    <div class="stat-label">Published Posts</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="app-card" role="region" aria-labelledby="admin-quick-actions-title">
        <div class="app-header events-header" role="banner" aria-labelledby="admin-quick-actions-title">
            <h3 id="admin-quick-actions-title">Quick Actions</h3>
            <i class="bi bi-lightning-charge header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Admin management actions">Manage</span>
        </div>
        <div class="app-body">
            <div class="quick-actions">
                <a href="accounts/account.php" class="quick-action primary">
                    <div class="action-icon">
                        <i class="bi bi-person-plus" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Create Account</h4>
                        <small class="text-muted">Add new user account</small>
                    </div>
                </a>
                <a href="blog/add_post.php" class="quick-action secondary">
                    <div class="action-icon">
                        <i class="bi bi-pencil" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Create Post</h4>
                        <small class="text-muted">Write new blog post</small>
                    </div>
                </a>
                <a href="invoice_system/invoice.php" class="quick-action info">
                    <div class="action-icon">
                        <i class="bi bi-receipt" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Create Invoice</h4>
                        <small class="text-muted">Generate new invoice</small>
                    </div>
                </a>
                <a href="settings/" class="quick-action success">
                    <div class="action-icon">
                        <i class="bi bi-gear" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>System Settings</h4>
                        <small class="text-muted">Configure application</small>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

        </div>
    </div>

    <div class="app-card" role="region" aria-labelledby="documents-title">
        <div class="app-header documents-header" role="banner" aria-labelledby="documents-title">
            <h3 id="documents-title">Documents</h3>
            <i class="bi bi-folder2-open header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $recent_uploads ?> recent document items"><?= $recent_uploads ?>
                items</span>
        </div>
        <div class="app-body">
            <?php if ($recent_uploads > 0): ?>
                <div class="action-items">
                    <a href="accounts/documents.php" class="action-item info">
                        <div class="action-icon">
                            <i class="bi bi-upload" aria-hidden="true"></i>
                        </div>
                        <div class="action-details">
                            <h4>Recent Uploads</h4>
                        </div>
                        <div class="action-count"><?= $recent_uploads ?></div>
                    </a>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <p>No recent document activity</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="app-card" role="region" aria-labelledby="client-title">
        <div class="app-header client-header" role="banner" aria-labelledby="client-title">
            <h3 id="client-title">Client Portal</h3>
            <i class="bi bi-person-vcard header-icon" aria-hidden="true"></i>
            <span class="badge"
                aria-label="<?= $pending_requests ?> pending client portal items"><?= $pending_requests ?> items</span>
        </div>
        <div class="app-body">
            <?php if ($pending_requests > 0): ?>
                <div class="action-items">
                    <div class="action-item warning" role="status" aria-label="Client portal feature coming soon">
                        <div class="action-icon">
                            <i class="bi bi-clock" aria-hidden="true"></i>
                        </div>
                        <div class="action-details">
                            <h4>Pending Requests</h4>
                            <small class="text-muted">(Feature coming soon)</small>
                        </div>
                        <div class="action-count"><?= $pending_requests ?></div>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <p>No pending client requests</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
            </div>
        </div>
    </div>
</div>

<?= template_admin_footer() ?>