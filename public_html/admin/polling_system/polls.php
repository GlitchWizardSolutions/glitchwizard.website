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
            <i class="bi bi-bar-chart-line" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Polls</h2>
            <p>View, edit, and create polls.</p>
        </div>
    </div>
</div>
<?php if (isset($success_msg)): ?>
<div class="alert alert-success d-flex align-items-center" role="alert">
    <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>
    <div><?=$success_msg?></div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="poll.php" class="btn btn-outline-secondary">
        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
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
                        <i class="bi bi-search me-1" aria-hidden="true"></i>
                        Apply Filters
                    </button>
                    <?php if ($search || $status || $datestart || $dateend): ?>
                    <a href="polls.php" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
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
                                <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                            </button>
                            <div class="table-dropdown-items" role="menu" aria-label="Poll Actions">
                                <?php if (!$poll['approved']): ?>
                                <div role="menuitem">
                                    <a class="green" href="polls.php?approve=<?=$poll['id']?>" onclick="return confirm('Are you sure you want to approve this poll?')" tabindex="-1" aria-label="Approve poll <?=htmlspecialchars($poll['title'])?>">
                                        <span class="icon" aria-hidden="true"><i class="bi bi-check-circle"></i></span>
                                        <span>Approve</span>
                                    </a>
                                </div>
                                <?php endif; ?>
                                <div role="menuitem">
                                    <a href="../../client_portal/polling_system/result.php?id=<?=$poll['id']?>" target="_blank" class="blue" tabindex="-1" aria-label="View results for poll <?=htmlspecialchars($poll['title'])?>">
                                        <span class="icon" aria-hidden="true"><i class="bi bi-eye"></i></span>
                                        <span>View Results</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a href="poll.php?id=<?=$poll['id']?>" class="green" tabindex="-1" aria-label="Edit poll <?=htmlspecialchars($poll['title'])?>">
                                        <span class="icon" aria-hidden="true"><i class="bi bi-pencil-square"></i></span>
                                        <span>Edit</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a class="black" href="polls.php?delete=<?=$poll['id']?>" onclick="return confirm('Are you sure you want to delete this poll?')" tabindex="-1" aria-label="Delete poll <?=htmlspecialchars($poll['title'])?>">
                                        <span class="icon" aria-hidden="true"><i class="bi bi-trash"></i></span>
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
                            <i class="bi bi-chevron-left" aria-hidden="true"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link"><i class="bi bi-chevron-left" aria-hidden="true"></i></span>
                    </li>
                    <?php endif; ?>
                    
                    <li class="page-item active">
                        <span class="page-link"><?=$pagination_page?></span>
                    </li>
                    
                    <?php if ($pagination_page * $results_per_pagination_page < $total_polls): ?>
                    <li class="page-item">
                        <a class="page-link" href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>" aria-label="Next page">
                            <i class="bi bi-chevron-right" aria-hidden="true"></i>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link"><i class="bi bi-chevron-right" aria-hidden="true"></i></span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</div>

<?=template_admin_footer()?>