<?php
include 'main.php';
// Check delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM messages WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: messages.php?success_msg=3');
    exit;
}
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','email','subject','msg','submit_date','status'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 20;
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (m.email LIKE :search OR m.subject LIKE :search OR m.msg LIKE :search OR m.submit_date LIKE :search) ' : '';
if ($status != 'all') {
    $where .= $where ? ' AND m.status = :status ' : ' WHERE m.status = :status ';
}
// Retrieve the total number of products
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM messages m ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($status != 'all') $stmt->bindParam('status', $status, PDO::PARAM_STR);
$stmt->execute();
$messages_total = $stmt->fetchColumn();
// SQL query to get all products from the "products" table
$stmt = $pdo->prepare('SELECT m.* FROM messages m ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
// Bind params
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($status != 'all') $stmt->bindParam('status', $status, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Message created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Message updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Message deleted successfully!';
    }
}
// Determine the URL
$url = 'messages.php?search=' . $search . '&status=' . $status;
?>
<?=template_admin_header('Messages', 'messages', isset($_GET['nav']) ? $_GET['nav'] : 'all')?>

<div class="content-title" id="main-contact-messages" role="banner" aria-label="Contact Messages Management Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/>
            </svg>
        </div>
        <div class="txt">
            <h2>Messages</h2>
            <p>View, manage, and search messages.</p>
        </div>
    </div>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="bi bi-check-circle-fill"></i>
    <p><?=$success_msg?></p>
    <i class="bi bi-x-circle-fill"></i>
</div>
<?php endif; ?>

<div class="content-header responsive-flex-column pad-top-5">
    <div class="filter-list">
        <?php if ($status != 'all'): ?>
        <div class="filter"><a href="<?=str_replace('&status=' . $status, '', $url)?>"><i class="fa-solid fa-xmark"></i></a> Status : <?=$status?></div>
        <?php endif; ?>
    </div>
    <form action="" method="get">
        <input type="hidden" name="page" value="messages">
        <div class="filters">
            <a href="#"><i class="bi bi-sliders"></i> Filters</a>
            <div class="list">
                <label>
                    Status
                    <select name="status">
                        <option value="all"<?=$status=='all'?' selected':''?>>All</option>
                        <option value="Unread"<?=$status=='Unread'?' selected':''?>>Unread</option>
                        <option value="Read"<?=$status=='Read'?' selected':''?>>Read</option>
                        <option value="Replied"<?=$status=='Replied'?' selected':''?>>Replied</option>
                    </select>
                </label>
                <button type="submit">Apply</button>
            </div>
        </div>
        <div class="search">
            <label for="search">
                <input id="search" type="text" name="search" placeholder="Search message..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>" class="responsive-width-100">
                <i class="bi bi-search"></i>
            </label>
        </div>
    </form>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=id'?>">#<?php if ($order_by=='id'): ?><i class="bi bi-arrow-<?=strtolower($order)=='asc'?'up':'down'?>"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=email'?>">From<?php if ($order_by=='email'): ?><i class="bi bi-arrow-<?=strtolower($order)=='asc'?'up':'down'?>"></i><?php endif; ?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=subject'?>">Subject<?php if ($order_by=='subject'): ?><i class="bi bi-arrow-<?=strtolower($order)=='asc'?'up':'down'?>"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=msg'?>">Message<?php if ($order_by=='msg'): ?><i class="bi bi-arrow-<?=strtolower($order)=='asc'?'up':'down'?>"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=status'?>">Status<?php if ($order_by=='status'): ?><i class="bi bi-arrow-<?=strtolower($order)=='asc'?'up':'down'?>"></i><?php endif; ?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=submit_date'?>">Date<?php if ($order_by=='submit_date'): ?><i class="bi bi-arrow-<?=strtolower($order)=='asc'?'up':'down'?>"></i><?php endif; ?></a></td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                <tr>
                    <td colspan="10" class="no-results">There are no recent messages</td>
                </tr>
                <?php else: ?>
                <?php foreach ($messages as $message): ?>
                <tr>
                    <td class="responsive-hidden"><?=$message['id']?></td>
                    <td><?=$message['email']?></td>
                    <td><?=mb_strimwidth(nl2br(htmlspecialchars($message['subject'], ENT_QUOTES)), 0, 100, '...')?></td>
                    <td class="responsive-hidden truncated-txt">
                        <div>
                            <span class="short"><?=htmlspecialchars(mb_strimwidth($message['msg'], 0, 50, "..."), ENT_QUOTES)?></span>
                            <span class="full"><?=nl2br(htmlspecialchars($message['msg'], ENT_QUOTES))?></span>
                            <?php if (strlen($message['msg']) > 50): ?>
                            <a href="#" class="read-more">Read More</a>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="responsive-hidden"><span class="<?=str_replace(['Unread','Read','Replied'], ['grey','orange','green'], $message['status'])?>"><?=$message['status']?></span></td>
                    <td class="responsive-hidden"><?=date('F j, Y H:ia', strtotime($message['submit_date']))?></td>
                    <td>
                        <a href="message.php?id=<?=$message['id']?>" class="link1">View</a>
                        <a href="messages.php?delete=<?=$message['id']?>" onclick="return confirm('Are you sure you want to delete this message?');" class="link1">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    <?php if ($pagination_page > 1): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page-1?>&order=<?=$order?>&order_by=<?=$order_by?>">Prev</a>
    <?php endif; ?>
    <span>Page <?=$pagination_page?> of <?=ceil($messages_total / $results_per_page) == 0 ? 1 : ceil($messages_total / $results_per_page)?></span>
    <?php if ($pagination_page * $results_per_page < $messages_total): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>