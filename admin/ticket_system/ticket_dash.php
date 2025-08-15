<?php
include 'main.php';

// Current date in MySQL DATETIME format
$date = date('Y-m-d H:i:s');

// Action Items Data - Things requiring attention
$stmt = $pdo->prepare('SELECT t.*, (SELECT COUNT(tc.id) FROM tickets_comments tc WHERE tc.ticket_id = t.id) AS num_comments, (SELECT GROUP_CONCAT(tu.filepath) FROM tickets_uploads tu WHERE tu.ticket_id = t.id) AS imgs, c.title AS category, a.full_name AS p_full_name, a.email AS a_email FROM tickets t LEFT JOIN tickets_categories c ON c.id = t.category_id LEFT JOIN accounts a ON t.account_id = a.id WHERE t.approved = 0');
$stmt->execute();
$awaiting_approval = $stmt->fetchAll(PDO::FETCH_ASSOC);

// High priority open tickets
$stmt = $pdo->prepare('SELECT t.*, c.title AS category FROM tickets t LEFT JOIN tickets_categories c ON c.id = t.category_id WHERE t.ticket_status = "open" AND t.priority = "High"');
$stmt->execute();
$high_priority_tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Old unresolved tickets (older than 7 days)
$stmt = $pdo->prepare('SELECT t.*, c.title AS category FROM tickets t LEFT JOIN tickets_categories c ON c.id = t.category_id WHERE t.ticket_status = "open" AND t.created < DATE_SUB(?, INTERVAL 7 DAY)');
$stmt->execute([$date]);
$old_tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve today's tickets
$stmt = $pdo->prepare('SELECT t.*, (SELECT COUNT(tc.id) FROM tickets_comments tc WHERE tc.ticket_id = t.id) AS num_comments, (SELECT GROUP_CONCAT(tu.filepath) FROM tickets_uploads tu WHERE tu.ticket_id = t.id) AS imgs, c.title AS category, a.full_name AS p_full_name, a.email AS a_email FROM tickets t LEFT JOIN tickets_categories c ON c.id = t.category_id LEFT JOIN accounts a ON t.account_id = a.id WHERE cast(t.created as DATE) = cast(now() as DATE) ORDER BY t.priority DESC, t.created DESC');
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve all open tickets
$stmt = $pdo->prepare('SELECT t.*, (SELECT count(*) FROM tickets_comments tc WHERE t.id = tc.ticket_id) AS msgs, c.title AS category FROM tickets t LEFT JOIN tickets_categories c ON c.id = t.category_id WHERE t.ticket_status = "open" ORDER BY t.priority DESC, t.created DESC');
$stmt->execute();
$open_tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistics Data
// Retrieve the total number of tickets
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM tickets');
$stmt->execute();
$tickets_total = $stmt->fetchColumn();

// Retrieve the total number of open tickets
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM tickets WHERE ticket_status = "open"');
$stmt->execute();
$open_tickets_total = $stmt->fetchColumn();

// Retrieve the total number of resolved tickets
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM tickets WHERE ticket_status = "resolved"');
$stmt->execute();
$resolved_tickets_total = $stmt->fetchColumn();

// Recent tickets (last 7 days)
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM tickets WHERE created >= DATE_SUB(?, INTERVAL 7 DAY)');
$stmt->execute([$date]);
$recent_tickets_total = $stmt->fetchColumn();

// Average resolution time (in days)
$stmt = $pdo->prepare('SELECT AVG(DATEDIFF(last_update, created)) AS avg_days FROM tickets WHERE ticket_status = "resolved"');
$stmt->execute();
$avg_resolution_days = $stmt->fetchColumn();

// Total comments across all tickets
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM tickets_comments');
$stmt->execute();
$total_comments = $stmt->fetchColumn();

// Calculate action items total
$total_action_items = count($awaiting_approval) + count($high_priority_tickets) + count($old_tickets);
?>
<?=template_admin_header('Ticket Dashboard', 'tickets')?>

<div class="content-title" id="main-tickets-dashboard" role="banner" aria-label="Tickets Dashboard Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm320 96c0-26.9-16.5-49.9-40-59.3V88c0-13.3-10.7-24-24-24s-24 10.7-24 24V292.7c-23.5 9.5-40 32.5-40 59.3c0 35.3 28.7 64 64 64s64-28.7 64-64zM144 176a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm-16 80a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm288 32a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM400 144a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/></svg>
        </div>
        <div class="txt">
            <h2>Ticket Dashboard</h2>
            <p>Overview of ticket statistics and recent activity.</p>
        </div>
    </div>
</div>



<!-- Ticket Dashboard Cards Grid -->
<div class="dashboard-apps">
    <!-- Ticket Quick Actions Card -->
    <div class="app-card" role="region" aria-labelledby="ticket-quick-actions-title">
        <div class="app-header events-header" role="banner" aria-labelledby="ticket-quick-actions-title">
            <h3 id="ticket-quick-actions-title">Quick Actions</h3>
            <i class="fas fa-bolt header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="Ticket management actions">Manage</span>
        </div>
        <div class="app-body">
            <div class="quick-actions">
                <a href="ticket.php" class="quick-action primary">
                    <div class="action-icon">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Create Ticket</h4>
                        <small class="text-muted">Add new support ticket</small>
                    </div>
                </a>
                <a href="categories.php" class="quick-action secondary">
                    <div class="action-icon">
                        <i class="fas fa-tags" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Manage Categories</h4>
                        <small class="text-muted">Organize ticket types</small>
                    </div>
                </a>
                <a href="tickets.php" class="quick-action info">
                    <div class="action-icon">
                        <i class="fas fa-list" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>View All Tickets</h4>
                        <small class="text-muted">Manage existing tickets</small>
                    </div>
                </a>
                <a href="comments.php" class="quick-action success">
                    <div class="action-icon">
                        <i class="fas fa-comments" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>View Comments</h4>
                        <small class="text-muted">Review ticket responses</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Ticket Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="ticket-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="ticket-actions-title">
            <h3 id="ticket-actions-title">Action Items</h3>
            <i class="fas fa-exclamation-triangle header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $total_action_items ?> items requiring attention"><?= $total_action_items ?> items</span>
        </div>
        <div class="app-body">
            <?php if ($total_action_items > 0): ?>
                <div class="action-items">
                    <?php if (count($awaiting_approval) > 0): ?>
                        <a href="tickets.php?status=pending" class="action-item warning">
                            <div class="action-icon">
                                <i class="fas fa-clock" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Pending Approval</h4>
                                <small class="text-muted">Tickets need approval</small>
                            </div>
                            <div class="action-count"><?= count($awaiting_approval) ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if (count($high_priority_tickets) > 0): ?>
                        <a href="tickets.php?priority=High&status=open" class="action-item danger">
                            <div class="action-icon">
                                <i class="fas fa-exclamation" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>High Priority</h4>
                                <small class="text-muted">Urgent tickets requiring attention</small>
                            </div>
                            <div class="action-count"><?= count($high_priority_tickets) ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if (count($old_tickets) > 0): ?>
                        <a href="tickets.php?status=open&old=1" class="action-item info">
                            <div class="action-icon">
                                <i class="fas fa-history" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Old Tickets</h4>
                                <small class="text-muted">Open for more than 7 days</small>
                            </div>
                            <div class="action-count"><?= count($old_tickets) ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                    <p>All tickets up to date! No pending actions.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ticket Statistics Card -->
    <div class="app-card" role="region" aria-labelledby="ticket-stats-title">
        <div class="app-header accounts-header" role="banner" aria-labelledby="ticket-stats-title">
            <h3 id="ticket-stats-title">Ticket Statistics</h3>
            <i class="fas fa-chart-pie header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= number_format($tickets_total) ?> total tickets"><?= number_format($tickets_total) ?> total</span>
        </div>
        <div class="app-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format(count($tickets)) ?></div>
                    <div class="stat-label">New Today</div>
                    <div class="stat-progress">
                        <div class="progress-bar" style="width: <?= $tickets_total > 0 ? round((count($tickets) / $tickets_total) * 100) : 0 ?>%"></div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($open_tickets_total) ?></div>
                    <div class="stat-label">Open</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($resolved_tickets_total) ?></div>
                    <div class="stat-label">Resolved</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $avg_resolution_days ? round($avg_resolution_days, 1) : 0 ?> days</div>
                    <div class="stat-label">Avg Resolution</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-title" id="recent-tickets" role="banner" aria-label="Recent Tickets Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M64 64C28.7 64 0 92.7 0 128v64c0 8.8 7.4 15.7 15.7 18.6C34.5 217.1 48 235 48 256s-13.5 38.9-32.3 45.4C7.4 304.3 0 311.2 0 320v64c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V320c0-8.8-7.4-15.7-15.7-18.6C541.5 294.9 528 277 528 256s13.5-38.9 32.3-45.4c8.3-2.9 15.7-9.8 15.7-18.6V128c0-35.3-28.7-64-64-64H64zm64 112l0 160c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16V176c0-8.8-7.2-16-16-16H144c-8.8 0-16 7.2-16 16zM96 160c0-17.7 14.3-32 32-32H448c17.7 0 32 14.3 32 32v192c0 17.7-14.3 32-32 32H128c-17.7 0-32-14.3-32-32V160z"/></svg>
        </div>
        <div class="txt">
            <h2>Recent Tickets</h2>
            <p>Tickets submitted in the last day.</p>
        </div>
    </div>
</div>
<br>

<div class="card">
    <h6 class="card-header">Today's Ticket Activity</h6>
    <div class="card-body">
        <div class="table">
        <table>
            <thead>
                <tr>
                    <td>#</td>
                    <td colspan="2">User</td>
                    <td>Title</td>
                    <td>Status</td>
                    <td class="responsive-hidden">Has Comments</td>
                    <td class="responsive-hidden">Priority</td>
                    <td class="responsive-hidden">Category</td>
                    <td class="responsive-hidden">Private</td>
                    <td>Approved</td>
                    <td class="responsive-hidden">Date</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                <tr>
                    <td colspan="20" style="text-align:center;">There are no recent tickets.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td><?=$ticket['id']?></td>
                    <td class="img">
                        <span style="background-color:<?=color_from_string($ticket['p_full_name'] ?? $ticket['full_name'])?>"><?=strtoupper(substr($ticket['p_full_name'] ?? $ticket['full_name'], 0, 1))?></span>
                    </td>
                    <td class="user">
                        <?=htmlspecialchars($ticket['p_full_name'] ?? $ticket['full_name'], ENT_QUOTES)?>
                        <span><?=$ticket['p_email'] ?? $ticket['email']?></span>
                    </td>
                    <td><?=htmlspecialchars($ticket['title'], ENT_QUOTES)?></td>
                    <td><span class="<?=$ticket['ticket_status']=='resolved'?'green':($ticket['ticket_status']=='closed'?'red':'grey')?>"><?=ucwords($ticket['ticket_status'])?></span></td>
                    <td class="responsive-hidden"><?=$ticket['num_comments'] ? '<span class="mark yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>' : '<span class="mark no"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 15L9 9M9 15L15 9M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>'?></td>
                    <td class="responsive-hidden"><span class="<?=$ticket['priority']=='low'?'green':($ticket['priority']=='high'?'red':'orange')?>"><?=ucwords($ticket['priority'])?></span></td>
                    <td class="responsive-hidden"><span class="grey"><?=$ticket['category']?></span></td>
                    <td class="responsive-hidden"><?=$ticket['private'] ? '<span class="mark yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>' : '<span class="mark no"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 15L9 9M9 15L15 9M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>'?></td>
                    <td><?=$ticket['approved'] ? '<span class="mark yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>' : '<span class="mark no"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 15L9 9M9 15L15 9M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>'?></td>
                    <td class="responsive-hidden"><?=date('F j, Y H:ia', strtotime($ticket['created']))?></td>
                    <td>
                        <a href="../view.php?id=<?=$ticket['id']?>&code=<?=md5($ticket['id'] . $ticket['email'])?>" target="_blank" class="link1">View</a>
                        <a href="ticket.php?id=<?=$ticket['id']?>" class="link1">Edit</a>
                        <a href="tickets.php?delete=<?=$ticket['id']?>" class="link1" onclick="return confirm('Are you sure you want to delete this ticket?')">Delete</a>
                        <?php if ($ticket['approved'] != 1): ?>
                        <a href="tickets.php?approve=<?=$ticket['id']?>" class="link1" onclick="return confirm('Are you sure you want to approve this ticket?')">Approve</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<div class="content-title" style="margin-top:40px">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 6V12L16 14M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="txt">
            <h2>Awaiting Approval</h2>
            <p>Tickets awaiting approval.</p>
        </div>
    </div>
</div>
<br>
<div class="card">
    <h6 class="card-header">Tickets Awaiting Approval</h6>
    <div class="card-body">
        <div class="table">
        <table>
            <thead>
                <tr>
                    <td>#</td>
                    <td colspan="2">User</td>
                    <td>Title</td>
                    <td>Status</td>
                    <td class="responsive-hidden">Has Comments</td>
                    <td class="responsive-hidden">Priority</td>
                    <td class="responsive-hidden">Category</td>
                    <td class="responsive-hidden">Private</td>
                    <td>Approved</td>
                    <td class="responsive-hidden">Date</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($awaiting_approval)): ?>
                <tr>
                    <td colspan="20" style="text-align:center;">There are no tickets awaiting approval.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($awaiting_approval as $ticket): ?>
                <tr>
                    <td><?=$ticket['id']?></td>
                    <td class="img">
                        <span style="background-color:<?=color_from_string($ticket['p_full_name'] ?? $ticket['full_name'])?>"><?=strtoupper(substr($ticket['p_full_name'] ?? $ticket['full_name'], 0, 1))?></span>
                    </td>
                    <td class="user">
                        <?=htmlspecialchars($ticket['p_full_name'] ?? $ticket['full_name'], ENT_QUOTES)?>
                        <span><?=$ticket['p_email'] ?? $ticket['email']?></span>
                    </td>
                    <td><?=htmlspecialchars($ticket['title'], ENT_QUOTES)?></td>
                    <td><span class="<?=$ticket['ticket_status']=='resolved'?'green':($ticket['ticket_status']=='closed'?'red':'grey')?>"><?=ucwords($ticket['ticket_status'])?></span></td>
                    <td class="responsive-hidden"><?=$ticket['num_comments'] ? '<span class="mark yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>' : '<span class="mark no"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 15L9 9M9 15L15 9M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>'?></td>
                    <td class="responsive-hidden"><span class="<?=$ticket['priority']=='low'?'green':($ticket['priority']=='high'?'red':'orange')?>"><?=ucwords($ticket['priority'])?></span></td>
                    <td class="responsive-hidden"><span class="grey"><?=$ticket['category']?></span></td>
                    <td class="responsive-hidden"><?=$ticket['private'] ? '<span class="mark yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>' : '<span class="mark no"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 15L9 9M9 15L15 9M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>'?></td>
                    <td><?=$ticket['approved'] ? '<span class="mark yes"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>' : '<span class="mark no"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 15L9 9M9 15L15 9M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>'?></td>
                    <td class="responsive-hidden"><?=date('F j, Y H:ia', strtotime($ticket['created']))?></td>
                    <td>
                        <a href="../view.php?id=<?=$ticket['id']?>&code=<?=md5($ticket['id'] . $ticket['email'])?>" target="_blank" class="link1">View</a>
                        <a href="ticket.php?id=<?=$ticket['id']?>" class="link1">Edit</a>
                        <a href="tickets.php?delete=<?=$ticket['id']?>" class="link1" onclick="return confirm('Are you sure you want to delete this ticket?')">Delete</a>
                        <?php if ($ticket['approved'] != 1): ?>
                        <a href="tickets.php?approve=<?=$ticket['id']?>" class="link1" onclick="return confirm('Are you sure you want to approve this ticket?')">Approve</a>
                        <?php endif; ?>
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