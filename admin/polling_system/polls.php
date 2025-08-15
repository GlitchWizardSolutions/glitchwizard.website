<?php
include '../assets/includes/main.php';
// Get current date
$current_date = date('Y-m-d H:i:s');
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
// Filters parameters
$datestart = isset($_GET['datestart']) ? $_GET['datestart'] : '';
$dateend = isset($_GET['dateend']) ? $_GET['dateend'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id', 'title', 'answers', 'total_votes', 'created'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination pagination_page
$results_per_pagination_page = 20;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_pagination_page;
$param2 = $results_per_pagination_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (p.title LIKE :search) ' : '';
// Add filters
// Date start filter
if ($datestart) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'p.start_date >= :datestart ';
}
// Date end filter
if ($dateend) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'p.end_date <= :dateend ';
}
// Status filter
if ($status) {
    if ($status == 'approved') {
        $where .= ($where ? 'AND ' : 'WHERE ') . 'p.approved = 1 ';
    } elseif ($status == 'pending') {
        $where .= ($where ? 'AND ' : 'WHERE ') . 'p.approved = 0 ';
    } elseif ($status == 'upcoming') {
        $where .= ($where ? 'AND ' : 'WHERE ') . 'p.start_date > :current_date ';
    } else if ($status == 'expired') {
        $where .= ($where ? 'AND ' : 'WHERE ') . 'p.end_date < :current_date ';
    } else if ($status == 'active') {
        $where .= ($where ? 'AND ' : 'WHERE ') . '(p.start_date <= :current_date OR p.start_date IS NULL) AND (p.end_date >= :current_date OR p.end_date IS NULL) ';
    }
}
// Category filter
if ($category) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'EXISTS (SELECT * FROM poll_categories pc WHERE pc.poll_id = p.id AND pc.category_id = :category) ';
}
// Retrieve the total number of polls
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM polls p ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($datestart) $stmt->bindParam('datestart', $datestart, PDO::PARAM_STR);
if ($dateend) $stmt->bindParam('dateend', $dateend, PDO::PARAM_STR);
if ($status && ($status == 'upcoming' || $status == 'expired' || $status == 'active')) $stmt->bindParam('current_date', $current_date, PDO::PARAM_STR);
if ($category) $stmt->bindParam('category', $category, PDO::PARAM_INT);
$stmt->execute();
$total_polls = $stmt->fetchColumn();
// Prepare polls query
$stmt = $pdo->prepare('SELECT p.*, GROUP_CONCAT(pa.title ORDER BY pa.votes DESC) AS answers, GROUP_CONCAT(pa.votes ORDER BY pa.votes DESC) AS answers_votes, GROUP_CONCAT(pa.img ORDER BY pa.id) AS answers_imgs, (SELECT GROUP_CONCAT(c.title) FROM polls_categories c JOIN poll_categories pc ON pc.poll_id = p.id AND pc.category_id = c.id) AS categories FROM polls p LEFT JOIN poll_answers pa ON pa.poll_id = p.id ' . $where . 'GROUP BY p.id ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results, :num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($datestart) $stmt->bindParam('datestart', $datestart, PDO::PARAM_STR);
if ($dateend) $stmt->bindParam('dateend', $dateend, PDO::PARAM_STR);
if ($status && ($status == 'upcoming' || $status == 'expired' || $status == 'active')) $stmt->bindParam('current_date', $current_date, PDO::PARAM_STR);
if ($category) $stmt->bindParam('category', $category, PDO::PARAM_INT);
$stmt->execute();
// Retrieve query results
$polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete poll
if (isset($_GET['delete'])) {
    // Delete the poll
    $stmt = $pdo->prepare('DELETE p, pa, pc FROM polls p LEFT JOIN poll_answers pa ON pa.poll_id = p.id LEFT JOIN poll_categories pc ON pc.poll_id = p.id WHERE p.id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    // Output message
    header('Location: polls.php?success_msg=3');
    exit;
}
// Approve poll
if (isset($_GET['approve'])) {
    // Approve the poll
    $stmt = $pdo->prepare('UPDATE polls SET approved = 1 WHERE id = ?');
    $stmt->execute([ $_GET['approve'] ]);
    // Output message
    header('Location: polls.php?success_msg=2');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Poll created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Poll updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Poll deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Poll(s) imported successfully! ' . $_GET['imported'] . ' poll(s) were imported.';
    }
}
// Create URL
$url = 'polls.php?search_query=' . $search . '&datestart=' . $datestart . '&dateend=' . $dateend . '&status=' . $status . '&category=' . $category;
?>
<?=template_admin_header('Polls', 'polls', 'view')?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path d="M160 80c0-26.5 21.5-48 48-48h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H208c-26.5 0-48-21.5-48-48V80zM0 272c0-26.5 21.5-48 48-48H80c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V272zM368 96h32c26.5 0 48 21.5 48 48V432c0 26.5-21.5 48-48 48H368c-26.5 0-48-21.5-48-48V144c0-26.5 21.5-48 48-48z"/>
            </svg>
        </div>
        <div class="txt">
            <h2>Polls</h2>
            <p>View, edit, and create polls.</p>
        </div>
    </div>
</div>
<?php if (isset($success_msg)): ?>
<div class="msg success">
    <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
    <p><?=$success_msg?></p>
    <svg class="close" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
</div>
<?php endif; ?>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="poll.php" class="btn btn-outline-secondary">
        <i class="fas fa-plus me-1" aria-hidden="true"></i>
        Add Poll
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Poll Management</h6>
        <small class="text-muted"><?=number_format($total_polls)?> total polls</small>
    </div>
    <div class="card-body">
        <form action="" method="get" class="mb-4">
            <input type="hidden" name="page" value="polls">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search_query" class="form-label">Search</label>
                    <input id="search_query" type="text" name="search_query" class="form-control"
                        placeholder="Search polls..." 
                        value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="" <?=$status==''?' selected':''?>>All</option>
                        <option value="approved" <?=$status=='approved'?' selected':''?>>Approved</option>
                        <option value="pending" <?=$status=='pending'?' selected':''?>>Pending Approval</option>
                        <option value="upcoming" <?=$status=='upcoming'?' selected':''?>>Upcoming</option>
                        <option value="expired" <?=$status=='expired'?' selected':''?>>Ended</option>
                        <option value="active" <?=$status=='active'?' selected':''?>>Active</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="datestart" class="form-label">Start Date</label>
                    <input type="datetime-local" name="datestart" id="datestart" class="form-control"
                        value="<?=htmlspecialchars($datestart, ENT_QUOTES)?>">
                </div>
                <div class="col-md-2">
                    <label for="dateend" class="form-label">End Date</label>
                    <input type="datetime-local" name="dateend" id="dateend" class="form-control"
                        value="<?=htmlspecialchars($dateend, ENT_QUOTES)?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-search me-1" aria-hidden="true"></i>
                        Apply Filters
                    </button>
                    <?php if ($search || $status || $datestart || $dateend): ?>
                    <a href="polls.php" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times me-1" aria-hidden="true"></i>
                        Clear
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover mb-0" role="grid" aria-label="Polls">
                <thead class="table-light" role="rowgroup">
                    <tr role="row">
                        <th class="text-start" role="columnheader" scope="col">
                            <a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=title'?>" class="text-decoration-none">
                                Title <?=$order_by=='title' ? ($order=='ASC' ? '▲' : '▼') : ''?>
                            </a>
                        </th>
                        <th class="text-start d-none d-md-table-cell" role="columnheader" scope="col">Categories</th>
                        <th class="text-start d-none d-lg-table-cell" role="columnheader" scope="col" style="width: 200px;">
                            <a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=answers'?>" class="text-decoration-none">
                                Answer Options <?=$order_by=='answers' ? ($order=='ASC' ? '▲' : '▼') : ''?>
                            </a>
                        </th>
                        <th class="text-center d-none d-md-table-cell" role="columnheader" scope="col" style="width: 100px;">Total Votes</th>
                        <th class="text-center d-none d-lg-table-cell" role="columnheader" scope="col" style="width: 120px; padding-right: 20px;">Status</th>
                        <th class="text-center d-none d-lg-table-cell" role="columnheader" scope="col" style="width: 100px;">
                            <a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=created'?>" class="text-decoration-none">
                                Created <?=$order_by=='created' ? ($order=='ASC' ? '▲' : '▼') : ''?>
                            </a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody role="rowgroup">
                <?php if (empty($polls)): ?>
                <tr role="row">
                    <td colspan="7" class="text-center py-4">There are no polls.</td>
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
                <tr role="row">
                    <td role="gridcell" class="fw-medium"><?=htmlspecialchars($poll['title'], ENT_QUOTES)?></td>
                    <td role="gridcell" class="d-none d-md-table-cell">
                        <?php if ($poll['categories']): ?>
                        <?php foreach (explode(',', $poll['categories']) as $category): ?>
                        <span class="badge bg-primary me-1"><?=htmlspecialchars(trim($category), ENT_QUOTES)?></span>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <td role="gridcell" class="d-none d-lg-table-cell">
                        <div class="trigger-answers-modal" data-total-votes="<?=$total_votes?>" data-json='<?=str_replace("'", "\'", json_encode($answers_obj))?>'>
                            <?php if ($poll['answers']): ?>
                            <?php foreach ($answers as $k => $answer): ?>
                            <div class="text-dark mb-1 <?=$total_votes && $k==0?' fw-bold':''?>" 
                                 title="<?=isset($answers_votes[$k]) && $answers_votes[$k] ? number_format($answers_votes[$k]) : 0?> votes">
                                <span class="badge bg-light text-dark border me-1"><?=($k+1)?></span>
                                <?=htmlspecialchars($answer, ENT_QUOTES)?>
                                <small class="text-muted">(<?=isset($answers_votes[$k]) && $answers_votes[$k] ? number_format($answers_votes[$k]) : 0?> votes)</small>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td role="gridcell" class="text-center d-none d-md-table-cell">
                        <span class="fw-bold text-dark"><?=$total_votes ? number_format($total_votes) : 0?></span>
                    </td>
                    <td role="gridcell" class="text-center d-none d-lg-table-cell">
                        <?php if (!$poll['approved']): ?>
                        <span class="orange">Awaiting Approval</span>
                        <?php elseif ($poll['end_date'] && strtotime($poll['end_date']) < strtotime($current_date)): ?>
                        <span class="red">Ended</span>
                        <?php elseif ($poll['start_date'] && strtotime($poll['start_date']) > strtotime($current_date)): ?>
                        <span class="grey" title="Starts on <?=date('jS F Y', strtotime($poll['start_date']))?>">Upcoming</span>
                        <?php else: ?>
                        <span class="green">Active</span>
                        <?php endif; ?>
                    </td>
                    <td role="gridcell" class="text-center d-none d-lg-table-cell"><?=date('m/d/Y', strtotime($poll['created']))?></td>
                    <td role="gridcell" class="text-center">
                        <div class="table-dropdown">
                            <button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for poll <?=htmlspecialchars($poll['title'])?>">
                                <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                    <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                </svg>
                            </button>
                            <div class="table-dropdown-items" role="menu" aria-label="Poll Actions">
                                <?php if (!$poll['approved']): ?>
                                <div role="menuitem">
                                    <a class="green" href="polls.php?approve=<?=$poll['id']?>" onclick="return confirm('Are you sure you want to approve this poll?')" tabindex="-1" aria-label="Approve poll <?=htmlspecialchars($poll['title'])?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z" />
                                            </svg>
                                        </span>
                                        <span>Approve</span>
                                    </a>
                                </div>
                                <?php endif; ?>
                                <div role="menuitem">
                                    <a href="../../client_portal/polling_system/result.php?id=<?=$poll['id']?>" target="_blank" class="blue" tabindex="-1" aria-label="View results for poll <?=htmlspecialchars($poll['title'])?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                                <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z" />
                                            </svg>
                                        </span>
                                        <span>View Results</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a href="poll.php?id=<?=$poll['id']?>" class="green" tabindex="-1" aria-label="Edit poll <?=htmlspecialchars($poll['title'])?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                            </svg>
                                        </span>
                                        <span>Edit</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a class="black" href="polls.php?delete=<?=$poll['id']?>" onclick="return confirm('Are you sure you want to delete this poll?')" tabindex="-1" aria-label="Delete poll <?=htmlspecialchars($poll['title'])?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0H284.2c12.1 0 23.2 6.8 28.6 17.7L320 32h96c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32h96l7.2-14.3zM32 128H416V448c0 35.3-28.7 64-64 64H96c-35.3 0-64-28.7-64-64V128zm96 64c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16z" />
                                            </svg>
                                        </span>
                                        <span>Delete</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-light">
        <!-- Bootstrap Pagination -->
        <nav aria-label="Polls pagination">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing page <?=$pagination_page?> of <?=ceil($total_polls / $results_per_pagination_page) == 0 ? 1 : ceil($total_polls / $results_per_pagination_page)?> 
                    (<?=$total_polls?> total polls)
                </div>
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($pagination_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?=$url?>&pagination_page=<?=$pagination_page-1?>&order=<?=$order?>&order_by=<?=$order_by?>" aria-label="Previous page">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                    </li>
                    <?php endif; ?>
                    
                    <li class="page-item active">
                        <span class="page-link"><?=$pagination_page?></span>
                    </li>
                    
                    <?php if ($pagination_page * $results_per_pagination_page < $total_polls): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>" aria-label="Next page">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</div>

<?=template_admin_footer()?>