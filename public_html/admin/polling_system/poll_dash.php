<?php
include '../assets/includes/main.php';
// Current date in MySQL DATETIME format
$date = date('Y-m-d H:i:s');
// SQL query that will get all polls created today
$stmt = $pdo->prepare('SELECT p.*, GROUP_CONCAT(pa.title ORDER BY pa.votes DESC) AS answers, GROUP_CONCAT(pa.votes ORDER BY pa.votes DESC) AS answers_votes, GROUP_CONCAT(pa.img ORDER BY pa.id) AS answers_imgs, (SELECT GROUP_CONCAT(c.title) FROM polls_categories c JOIN poll_categories pc ON pc.poll_id = p.id AND pc.category_id = c.id) AS categories FROM polls p LEFT JOIN poll_answers pa ON pa.poll_id = p.id WHERE CAST(p.created AS DATE) = ? GROUP BY p.id');
$stmt->execute([ date('Y-m-d') ]);
$polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
// SQL query that will get all polls awaiting approval
$stmt = $pdo->prepare('SELECT p.*, GROUP_CONCAT(pa.title ORDER BY pa.id) AS answers, GROUP_CONCAT(pa.votes ORDER BY pa.id) AS answers_votes, GROUP_CONCAT(pa.img ORDER BY pa.id) AS answers_imgs, (SELECT GROUP_CONCAT(c.title) FROM polls_categories c JOIN poll_categories pc ON pc.poll_id = p.id AND pc.category_id = c.id) AS categories FROM polls p LEFT JOIN poll_answers pa ON pa.poll_id = p.id WHERE p.approved = 0 GROUP BY p.id');
$stmt->execute();
$awaiting_approval_polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
// SQL query that will get all active polls
$stmt = $pdo->prepare('SELECT p.*, GROUP_CONCAT(pa.title ORDER BY pa.id) AS answers, GROUP_CONCAT(pa.votes ORDER BY pa.id) AS answers_votes, GROUP_CONCAT(pa.img ORDER BY pa.id) AS answers_imgs, (SELECT GROUP_CONCAT(c.title) FROM polls_categories c JOIN poll_categories pc ON pc.poll_id = p.id AND pc.category_id = c.id) AS categories FROM polls p LEFT JOIN poll_answers pa ON pa.poll_id = p.id WHERE (p.start_date < ? OR p.start_date IS NULL) AND (p.end_date > ? OR p.end_date IS NULL) GROUP BY p.id');
$stmt->execute([ $date, $date ]);
$active_polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get the total number of polls
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM polls');
$stmt->execute();
$polls_total = $stmt->fetchColumn();
?>
<?=template_admin_header('Poll System', 'polls')?>
<link rel="stylesheet" href="polling-specific.css">

<div class="content-title">
    <div class="title">
        <div class="icon">
            <i class="bi bi-bar-chart-line" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Polling Dashboard</h2>
            <p>View statistics, new polls, and more.</p>
        </div>
    </div>
</div>
 

<!-- Poll Dashboard Cards Grid -->
<div class="dashboard-apps">
    <!-- Poll Engagement Card -->
    <div class="app-card" role="region" aria-labelledby="poll-engagement-title">
        <div class="app-header events-header" role="banner" aria-labelledby="poll-engagement-title">
            <h3 id="poll-engagement-title">Quick Actions</h3>
            <i class="bi bi-lightning" aria-hidden="true"></i>
            <span class="badge" aria-label="Poll management actions">Manage</span>
        </div>
        <div class="app-body">
            <div class="quick-actions">
                <a href="poll.php" class="quick-action primary">
                    <div class="action-icon">
                        <i class="bi bi-plus-circle" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Create Poll</h4>
                        <small class="text-muted">Add new poll question</small>
                    </div>
                </a>
                <a href="polls.php" class="quick-action info">
                    <div class="action-icon">
                        <i class="bi bi-list-ul" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Manage Polls</h4>
                        <small class="text-muted">View all polls</small>
                    </div>
                </a>
                <a href="poll_table_transfer.php" class="quick-action secondary">
                    <div class="action-icon">
                        <i class="bi bi-arrow-left-right" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Import/Export</h4>
                        <small class="text-muted">Data transfer operations</small>
                    </div>
                </a>
        <a href="polls.php?approved=No" class="quick-action warning">
                    <div class="action-icon">
            <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Pending Approval</h4>
                        <small class="text-muted">Review pending polls</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Poll Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="poll-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="poll-actions-title">
            <h3 id="poll-actions-title">Action Items</h3>
            <i class="bi bi-exclamation-triangle-fill header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= count($awaiting_approval_polls) ?> polls requiring attention"><?= count($awaiting_approval_polls) ?> polls</span>
        </div>
        <div class="app-body">
            <?php if (count($awaiting_approval_polls) > 0): ?>
                <div class="action-items">
                    <a href="polls.php?approved=No" class="action-item warning">
                        <div class="action-icon">
                            <i class="bi bi-clock-history" aria-hidden="true"></i>
                        </div>
                        <div class="action-details">
                            <h4>Awaiting Approval</h4>
                            <small class="text-muted">Polls need moderation</small>
                        </div>
                        <div class="action-count"><?= count($awaiting_approval_polls) ?></div>
                    </a>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                    <p>All polls approved! No pending actions.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Poll Content Overview Card -->
    <div class="app-card" role="region" aria-labelledby="poll-content-title">
        <div class="app-header accounts-header" role="banner" aria-labelledby="poll-content-title">
            <h3 id="poll-content-title">Poll Statistics</h3>
            <i class="bi bi-pie-chart-fill header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $polls_total ?> total polls"><?= $polls_total ?> polls</span>
        </div>
        <div class="app-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format(count($active_polls)) ?></div>
                    <div class="stat-label">Active Polls</div>
                    <div class="stat-progress">
                        <div class="progress-bar" style="width: <?= $polls_total > 0 ? round((count($active_polls) / $polls_total) * 100) : 0 ?>%"></div>
                    </div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format(count($polls)) ?></div>
                    <div class="stat-label">New Today</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($polls_total) ?></div>
                    <div class="stat-label">Total Polls</div>
                </div>
                <div class="stat-item">
                    <?php 
                    $total_votes = 0;
                    foreach ($active_polls as $poll) {
                        if ($poll['answers_votes']) {
                            $votes = explode(',', $poll['answers_votes']);
                            $total_votes += array_sum($votes);
                        }
                    }
                    ?>
                    <div class="stat-value"><?= number_format($total_votes) ?></div>
                    <div class="stat-label">Total Votes</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-title">
    <div class="title">
        <div class="icon alt">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" /></svg>        
        </div>
        <div class="txt">
            <h2>New polls</h2>
            <p>List of polls created today.</p>
        </div>
    </div>
</div>

<div class="card">
    <h6 class="card-header">New Polls</h6>
    <div class="card-body">
        <div class="table" role="table" aria-label="New Polls Today">
            <table role="grid">
                <thead role="rowgroup">
                    <tr role="row">
                        <th style="text-align:left;" role="columnheader" scope="col">Title</th>
                        <th class="responsive-hidden" style="text-align:left;" role="columnheader" scope="col">Categories</th>
                        <th style="text-align:center;" role="columnheader" scope="col">Answer Options</th>
                        <th class="responsive-hidden" style="text-align:center;" role="columnheader" scope="col">Votes</th>
                        <th class="responsive-hidden" style="text-align:center;" role="columnheader" scope="col">Status</th>
                        <th class="responsive-hidden" style="text-align:center;" role="columnheader" scope="col">Created</th>
                        <th style="text-align:center;" role="columnheader" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($polls)): ?>
                <tr>
                    <td colspan="20" class="no-results">There are no new polls.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($polls as $poll): ?>
                <?php
                $answers = explode(',', $poll['answers']);
                $answers_votes = explode(',', $poll['answers_votes']);
                $total_votes = array_sum($answers_votes);
                $answers_obj = [];
                for ($i = 0; $i < count($answers); $i++) {
                    $answers_obj[] = ['title' => htmlspecialchars($answers[$i], ENT_QUOTES), 'votes' => isset($answers_votes[$i]) ? $answers_votes[$i] : 0];
                }
                ?>
                <tr>
                    <td style="text-align:left;"><?=htmlspecialchars($poll['title'], ENT_QUOTES)?></td>
                    <td class="responsive-hidden" style="text-align:left;">
                        <?php if ($poll['categories']): ?>
                        <?php foreach (explode(',', $poll['categories']) as $category): ?>
                        <span class="blue"><?=$category?></span>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <td style="text-align:center;">
                        <div class="trigger-answers-modal" data-total-votes="<?=$total_votes?>" data-json='<?=str_replace("'", "\'", json_encode($answers_obj))?>'>
                            <?php if ($poll['answers']): ?>
                            <?php foreach ($answers as $k => $answer): ?>
                            <span class="grey<?=$total_votes && $k==0?' most':''?>" title="<?=isset($answers_votes[$k]) && $answers_votes[$k] ? number_format($answers_votes[$k]) : 0?> votes"><?=$answer?></span>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="responsive-hidden" style="text-align:center;">
                        <span class="grey small"><?=$total_votes ? number_format($total_votes) : 0?></span>
                    </td>
                    <td class="responsive-hidden" style="text-align:center;">
                        <?php if (!$poll['approved']): ?>
                        <span class="red">Awaiting Approval</span>
                        <?php elseif ($poll['end_date'] && strtotime($poll['end_date']) < strtotime($date)): ?>
                        <span class="red">Ended</span>
                        <?php elseif ($poll['start_date'] && strtotime($poll['start_date']) > strtotime($date)): ?>
                        <span class="orange" title="Starts on <?=date('n/j/Y', strtotime($poll['start_date']))?>">Upcoming</span>
                        <?php else: ?>
                        <span class="green">Active</span>
                        <?php endif; ?>
                    </td>
                    <td class="responsive-hidden" style="text-align:center;"><?=date('n/j/Y', strtotime($poll['created']))?></td>
                    <td style="text-align:center;">
                        <div class="table-dropdown">
                            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                            <div class="table-dropdown-items">
                                <?php if (!$poll['approved']): ?>
                                <a class="green" href="polls.php?approve=<?=$poll['id']?>" onclick="return confirm('Are you sure you want to approve this poll?')">
                                    <span class="icon"><i class="bi bi-check-circle" aria-hidden="true"></i></span>    
                                    Approve
                                </a>
                                <?php endif; ?>
                                <a href="../../client_portal/polling_system/result.php?id=<?=$poll['id']?>" target="_blank">
                                    <span class="icon"><i class="bi bi-eye" aria-hidden="true"></i></span>
                                    View Results
                                </a>
                                <a href="poll.php?id=<?=$poll['id']?>">
                                    <span class="icon"><i class="bi bi-pencil-square" aria-hidden="true"></i></span>
                                    Edit
                                </a>
                                <a class="red" href="polls.php?delete=<?=$poll['id']?>" onclick="return confirm('Are you sure you want to delete this poll?')">
                                    <span class="icon"><i class="bi bi-trash" aria-hidden="true"></i></span>    
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

<div class="content-title mt-5">
    <div class="title">
        <div class="icon alt">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 17H7V10H9V17M13 17H11V7H13V17M17 17H15V13H17V17M19 19H5V5H19V19.1M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3Z" /></svg>
        </div>
        <div class="txt">
            <h2>Active Polls</h2>
            <p>List of active polls.</p>
        </div>
    </div>
</div>

<div class="card">
    <h6 class="card-header">Active Polls</h6>
    <div class="card-body">
        <div class="table" role="table" aria-label="Active Polls">
            <table role="grid">
                <thead role="rowgroup">
                    <tr role="row">
                        <th style="text-align:left;" role="columnheader" scope="col">Title</th>
                        <th class="responsive-hidden" style="text-align:left;" role="columnheader" scope="col">Categories</th>
                        <th style="text-align:center;" role="columnheader" scope="col">Answer Options</th>
                        <th class="responsive-hidden" style="text-align:center;" role="columnheader" scope="col">Votes</th>
                        <th class="responsive-hidden" style="text-align:center;" role="columnheader" scope="col">Status</th>
                        <th class="responsive-hidden" style="text-align:center;" role="columnheader" scope="col">Created</th>
                        <th style="text-align:center;" role="columnheader" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($active_polls)): ?>
                <tr>
                    <td colspan="20" class="no-results">There are no active polls.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($active_polls as $poll): ?>
                <?php
                $answers = explode(',', $poll['answers']);
                $answers_votes = explode(',', $poll['answers_votes']);
                $total_votes = array_sum($answers_votes);
                $answers_obj = [];
                for ($i = 0; $i < count($answers); $i++) {
                    $answers_obj[] = ['title' => htmlspecialchars($answers[$i], ENT_QUOTES), 'votes' => isset($answers_votes[$i]) ? $answers_votes[$i] : 0];
                }
                ?>
                <tr>
                    <td class="title"><?=htmlspecialchars($poll['title'], ENT_QUOTES)?></td>
                    <td class="responsive-hidden">
                        <?php if ($poll['categories']): ?>
                        <?php foreach (explode(',', $poll['categories']) as $category): ?>
                        <span class="blue"><?=$category?></span>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <div class="trigger-answers-modal" data-total-votes="<?=$total_votes?>" data-json='<?=str_replace("'", "\'", json_encode($answers_obj))?>'>
                            <?php if ($poll['answers']): ?>
                            <?php foreach ($answers as $k => $answer): ?>
                            <span class="grey<?=$total_votes && $k==0?' most':''?>" title="<?=isset($answers_votes[$k]) && $answers_votes[$k] ? number_format($answers_votes[$k]) : 0?> votes"><?=$answer?></span>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="alt responsive-hidden text-center">
                        <span class="grey small"><?=$total_votes ? number_format($total_votes) : 0?></span>
                    </td>
                    <td class="alt responsive-hidden text-center">
                        <?php if (!$poll['approved']): ?>
                        <span class="red">Awaiting Approval</span>
                        <?php elseif ($poll['end_date'] && strtotime($poll['end_date']) < strtotime($date)): ?>
                        <span class="red">Ended</span>
                        <?php elseif ($poll['start_date'] && strtotime($poll['start_date']) > strtotime($date)): ?>
                        <span class="orange" title="Starts on <?=date('jS F Y', strtotime($poll['start_date']))?>">Upcoming</span>
                        <?php else: ?>
                        <span class="green">Active</span>
                        <?php endif; ?>
                    </td>
                    <td class="alt responsive-hidden text-center"><?=date('n/j/Y', strtotime($poll['created']))?></td>
                    <td class="actions">
                        <div class="table-dropdown">
                            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                            <div class="table-dropdown-items">
                                <?php if (!$poll['approved']): ?>
                                <a class="green" href="polls.php?approve=<?=$poll['id']?>" onclick="return confirm('Are you sure you want to approve this poll?')">
                                    <span class="icon"><i class="bi bi-check-circle" aria-hidden="true"></i></span>    
                                    Approve
                                </a>
                                <?php endif; ?>
                                <a href="../../client_portal/polling_system/result.php?id=<?=$poll['id']?>" target="_blank">
                                    <span class="icon"><i class="bi bi-eye" aria-hidden="true"></i></span>
                                    View Results
                                </a>
                                <a href="poll.php?id=<?=$poll['id']?>">
                                    <span class="icon"><i class="bi bi-pencil-square" aria-hidden="true"></i></span>
                                    Edit
                                </a>
                                <a class="red" href="polls.php?delete=<?=$poll['id']?>" onclick="return confirm('Are you sure you want to delete this poll?')">
                                    <span class="icon"><i class="bi bi-trash" aria-hidden="true"></i></span>    
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

<div class="content-title mt-5">
    <div class="title">
        <div class="icon alt">
            <svg width="20" height="20"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z" /></svg>
        </div>
        <div class="txt">
            <h2>Awaiting Approval</h2>
            <p>List of polls that are awaiting approval.</p>
        </div>
    </div>
</div>

<div class="card">
    <h6 class="card-header">Awaiting Approval</h6>
    <div class="card-body">
        <div class="table" role="table" aria-label="Polls Awaiting Approval">
            <table role="grid">
                <thead role="rowgroup">
                    <tr role="row">
                        <th style="text-align:left;" role="columnheader" scope="col">Title</th>
                        <th class="responsive-hidden" style="text-align:left;" role="columnheader" scope="col">Categories</th>
                        <th style="text-align:left;" role="columnheader" scope="col">Answer Options</th>
                        <th class="responsive-hidden" style="text-align:center;" role="columnheader" scope="col">Votes</th>
                        <th class="responsive-hidden" style="text-align:center;" role="columnheader" scope="col">Status</th>
                        <th class="responsive-hidden" style="text-align:center;" role="columnheader" scope="col">Created</th>
                        <th style="text-align:center;" role="columnheader" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($awaiting_approval_polls)): ?>
                <tr>
                    <td colspan="20" class="no-results">There are no polls awaiting approval.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($awaiting_approval_polls as $poll): ?>
                <?php
                $answers = explode(',', $poll['answers']);
                $answers_votes = explode(',', $poll['answers_votes']);
                $total_votes = array_sum($answers_votes);
                $answers_obj = [];
                for ($i = 0; $i < count($answers); $i++) {
                    $answers_obj[] = ['title' => htmlspecialchars($answers[$i], ENT_QUOTES), 'votes' => isset($answers_votes[$i]) ? $answers_votes[$i] : 0];
                }
                ?>
                <tr>
                    <td class="title"><?=htmlspecialchars($poll['title'], ENT_QUOTES)?></td>
                    <td class="responsive-hidden">
                        <?php if ($poll['categories']): ?>
                        <?php foreach (explode(',', $poll['categories']) as $category): ?>
                        <span class="blue"><?=$category?></span>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <div class="trigger-answers-modal" data-total-votes="<?=$total_votes?>" data-json='<?=str_replace("'", "\'", json_encode($answers_obj))?>'>
                            <?php if ($poll['answers']): ?>
                            <?php foreach ($answers as $k => $answer): ?>
                            <span class="grey<?=$total_votes && $k==0?' most':''?>" title="<?=isset($answers_votes[$k]) && $answers_votes[$k] ? number_format($answers_votes[$k]) : 0?> votes"><?=$answer?></span>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="alt responsive-hidden text-center">
                        <span class="grey small"><?=$total_votes ? number_format($total_votes) : 0?></span>
                    </td>
                    <td class="alt responsive-hidden text-center">
                        <?php if (!$poll['approved']): ?>
                        <span class="red">Awaiting Approval</span>
                        <?php elseif ($poll['end_date'] && strtotime($poll['end_date']) < strtotime($date)): ?>
                        <span class="red">Ended</span>
                        <?php elseif ($poll['start_date'] && strtotime($poll['start_date']) > strtotime($date)): ?>
                        <span class="orange" title="Starts on <?=date('jS F Y', strtotime($poll['start_date']))?>">Upcoming</span>
                        <?php else: ?>
                        <span class="green">Active</span>
                        <?php endif; ?>
                    </td>
                    <td class="alt responsive-hidden text-center"><?=date('n/j/Y', strtotime($poll['created']))?></td>
                    <td class="actions">
                        <div class="table-dropdown">
                            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                            <div class="table-dropdown-items">
                                <?php if (!$poll['approved']): ?>
                                <a class="green" href="polls.php?approve=<?=$poll['id']?>" onclick="return confirm('Are you sure you want to approve this poll?')">
                                    <span class="icon"><i class="bi bi-check-circle" aria-hidden="true"></i></span>    
                                    Approve
                                </a>
                                <?php endif; ?>
                                <a href="../../client_portal/polling_system/result.php?id=<?=$poll['id']?>" target="_blank">
                                    <span class="icon"><i class="bi bi-eye" aria-hidden="true"></i></span>
                                    View Results
                                </a>
                                <a href="poll.php?id=<?=$poll['id']?>">
                                    <span class="icon"><i class="bi bi-pencil-square" aria-hidden="true"></i></span>
                                    Edit
                                </a>
                                <a class="red" href="polls.php?delete=<?=$poll['id']?>" onclick="return confirm('Are you sure you want to delete this poll?')">
                                    <span class="icon"><i class="bi bi-trash" aria-hidden="true"></i></span>    
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

<script src="polling-specific.js"></script>
<?=template_admin_footer()?>
