<?php
include 'main.php';
// Delete comment
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM tickets_comments WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: comments.php?success_msg=3');
    exit;
}
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'ASC' ? 'ASC' : 'DESC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['ticket_id','ticket_title','msg','full_name','created'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'created';
// Number of results per pagination page
$results_per_page = 15;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (t.title LIKE :search OR t.full_name LIKE :search OR t.email LIKE :search OR t.id LIKE :search OR a.full_name LIKE :search) ' : '';
if (isset($_GET['acc_id'])) {
    $where .= $where ? ' AND t.account_id = :acc_id ' : ' WHERE t.account_id = :acc_id ';
} 
// Retrieve the total number of comments from the database
$stmt = $pdo->prepare('SELECT COUNT(*) AS total, tc.* FROM tickets_comments tc JOIN tickets t ON t.id = tc.ticket_id LEFT JOIN accounts a ON a.id = tc.account_id ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if (isset($_GET['acc_id'])) $stmt->bindParam('acc_id', $_GET['acc_id'], PDO::PARAM_INT);
$stmt->execute();
$comments_total = $stmt->fetchColumn();
// SQL query to get all comments from the "comments" table
$stmt = $pdo->prepare('SELECT tc.*, t.title AS ticket_title, a.full_name AS full_name, t.email AS ticket_email FROM tickets_comments tc JOIN tickets t ON t.id = tc.ticket_id LEFT JOIN accounts a ON a.id = tc.account_id ' . $where . ' GROUP BY tc.id ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
// Bind params
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if (isset($_GET['acc_id'])) $stmt->bindParam('acc_id', $_GET['acc_id'], PDO::PARAM_INT);
$stmt->execute();
// Retrieve query results
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Comment created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Comment updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Comment deleted successfully!';
    }
}
// Determine the URL
$url = 'comments.php?search=' . $search;
?>
<?=template_admin_header('Comments', 'tickets', 'manage')?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <i class="bi bi-chat-dots" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Comments</h2>
            <p>View, manage, and search ticket comments.</p>
        </div>
    </div>
</div>

<?php if (isset($success_msg)): ?>
<div class="mb-4" role="region" aria-label="Success Message">
    <div class="msg success" role="alert" aria-live="polite">
    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
        <p><?=$success_msg?></p>
        <button type="button" class="close-success" aria-label="Dismiss success message" onclick="this.parentElement.parentElement.style.display='none'">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="comment.php" class="btn btn-outline-secondary">
        <i class="bi bi-plus me-1" aria-hidden="true"></i>
        Add Comment
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Comment Management</h6>
        <small class="text-muted"><?=number_format($comments_total)?> total comments</small>
    </div>
    <div class="card-body">
        <form action="" method="get" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label">Search</label>
                    <input id="search" type="text" name="search" class="form-control"
                        placeholder="Search comments..." 
                        value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-search me-1" aria-hidden="true"></i>
                        Apply Filters
                    </button>
                    <?php if ($search): ?>
                    <a href="comments.php" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                        Clear
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <div class="table">
        <table>
            <thead>
                <tr>
                    <th class="responsive-hidden" style="text-align:left !important;"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=ticket_id'?>" style="text-align:left !important; display:block;">Ticket ID<?php if ($order_by=='ticket_id'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                    <th style="text-align:left !important;"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=ticket_title'?>" style="text-align:left !important; display:block;">Ticket Title<?php if ($order_by=='ticket_title'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                    <th style="text-align:left !important;"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=full_name'?>" style="text-align:left !important; display:block;">User<?php if ($order_by=='full_name'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                    <th style="text-align:left !important;"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=msg'?>" style="text-align:left !important; display:block;">Message<?php if ($order_by=='msg'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                    <th class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=created'?>">Date<?php if ($order_by=='created'): ?><span aria-label="Sorted <?= $order == 'ASC' ? 'ascending' : 'descending' ?>"><?= $order == 'ASC' ? '▲' : '▼' ?></span><?php endif; ?></a></th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($comments)): ?>
                <tr>
                    <td colspan="20" style="text-align:center;">There are no comments.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                <tr>
                    <td class="responsive-hidden"><?=$comment['ticket_id']?></td>
                    <td><?=$comment['ticket_title']?></td>
                    <td><?=htmlspecialchars($comment['full_name'], ENT_QUOTES)?></td>
                    <td style="max-width:200px"><?=nl2br(htmlspecialchars($comment['msg'], ENT_QUOTES))?></td>
                    <td class="responsive-hidden"><?=date('n/j/Y', strtotime($comment['created']))?></td>
                    <td style="text-align:center;">
                        <div class="table-dropdown">
                            <button class="actions-btn" 
                                    aria-haspopup="true" 
                                    aria-expanded="false" 
                                    aria-label="Actions for comment by <?= htmlspecialchars($comment['full_name'], ENT_QUOTES) ?>">
                                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                    <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                </svg>
                            </button>
                            <div class="table-dropdown-items" role="menu">
                                <div role="menuitem">
                                    <a class="blue" 
                                       href="../view.php?id=<?=$comment['ticket_id']?>&code=<?=md5($comment['ticket_id'] . $comment['ticket_email'])?>" 
                                       target="_blank"
                                       tabindex="-1"
                                       aria-label="View ticket #<?= $comment['ticket_id'] ?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                                <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-92.7-69.4z" />
                                            </svg>
                                        </span>
                                        <span>View</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a class="green" 
                                       href="comment.php?id=<?=$comment['id']?>"
                                       tabindex="-1"
                                       aria-label="Edit comment by <?= htmlspecialchars($comment['full_name'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                            </svg>
                                        </span>
                                        <span>Edit</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a class="red" 
                                       href="comments.php?delete=<?=$comment['id']?>"
                                       onclick="return confirm('Are you sure you want to delete this comment?')"
                                       tabindex="-1"
                                       aria-label="Delete comment by <?= htmlspecialchars($comment['full_name'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z" />
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
    </div>
</div>

<div class="pagination">
    <?php if ($pagination_page > 1): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page-1?>&order=<?=$order?>&order_by=<?=$order_by?>">Prev</a>
    <?php endif; ?>
    <span>Page <?=$pagination_page?> of <?=ceil($comments_total / $results_per_page) == 0 ? 1 : ceil($comments_total / $results_per_page)?></span>
    <?php if ($pagination_page * $results_per_page < $comments_total): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>