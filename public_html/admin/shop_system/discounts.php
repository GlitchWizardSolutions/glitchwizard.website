<?php
defined('shoppingcart_admin') or exit;
// Get the current date
$current_date = strtotime((new DateTime())->format('Y-m-d H:i:s'));
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
// Filters parameters
$type = isset($_GET['type']) ? $_GET['type'] : '';
$active = isset($_GET['active']) ? $_GET['active'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','category_names','product_names','discount_code','discount_type','discount_value','start_date','end_date'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination pagination_page
$results_per_pagination_page = 15;
// discounts array
$discounts = [];
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_pagination_page;
$param2 = $results_per_pagination_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (d.discount_code LIKE :search OR d.discount_value LIKE :search OR d.discount_type LIKE :search) ' : '';
// Add filters
// Type filter
if ($type) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'd.discount_type = :type ';
}
// Active filter
if ($active) {
    // check if active based on the start and end date
    if ($active == 'yes') {
        $where .= ($where ? 'AND ' : 'WHERE ') . '(d.start_date <= :current_date AND d.end_date >= :current_date) ';
    } else {
        $where .= ($where ? 'AND ' : 'WHERE ') . '(d.start_date > :current_date OR d.end_date < :current_date) ';
    }
    $active_date = date('Y-m-d H:i:s');
}
// Retrieve the total number of discounts
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM shop_discounts d ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($type) $stmt->bindParam('type', $type, PDO::PARAM_STR);
if ($active) $stmt->bindParam('current_date', $active_date, PDO::PARAM_STR);
$stmt->execute();
$total_discounts = $stmt->fetchColumn();
// Prepare discounts query
$stmt = $pdo->prepare('SELECT d.*, GROUP_CONCAT(DISTINCT p.title) product_names, GROUP_CONCAT(DISTINCT c.title) category_names FROM shop_discounts d LEFT JOIN shop_products p ON FIND_IN_SET(p.id, d.product_ids) LEFT JOIN shop_product_categories c ON FIND_IN_SET(c.id, d.category_ids) ' . $where . ' GROUP BY d.id, d.category_ids, d.product_ids, d.discount_code, d.discount_type, d.discount_type, d.discount_value, d.start_date, d.end_date ORDER BY ' . $order_by . ' ' . $order . '  LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($type) $stmt->bindParam('type', $type, PDO::PARAM_STR);
if ($active) $stmt->bindParam('current_date', $active_date, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$discounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete discounts
if (isset($_GET['delete'])) {
    // Delete the discounts
    $stmt = $pdo->prepare('DELETE FROM shop_discounts WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    // Remove session discount code
    if (isset($_SESSION['discount'])) {
        unset($_SESSION['discount']);
    }
    header('Location: index.php?page=discounts&success_msg=3');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Discount created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Discount updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Discount deleted successfully!';
    }
}
// Create URL
$url = 'index.php?page=discounts&search_query=' . $search . '&type=' . $type . '&active=' . $active;
?>
<?=template_admin_header('Discounts', 'discounts', 'view')?>

<div class="content-title">
    <div class="title">
        <div class="icon">
            <i class="bi bi-ticket-perforated" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Discounts</h2>
            <p>View, edit, and create discounts</p>
        </div>
    </div>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
    <p><?=$success_msg?></p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="content-header responsive-flex-column pad-top-5">
    <a href="index.php?page=discount" class="btn">
    <i class="bi bi-plus me-1" aria-hidden="true"></i>Create Discount
    </a>
    <form method="get">
        <input type="hidden" name="page" value="discounts">
        <div class="filters">
            <a href="#">
                <i class="bi bi-sliders" aria-hidden="true"></i>
                Filters
            </a>
            <div class="list">
                <label for="type">Type</label>
                <select name="type" id="type">
                    <option value=""<?=$type==''?' selected':''?>>All</option>
                    <option value="Percentage"<?=$type=='Percentage'?' selected':''?>>Percentage</option>
                    <option value="Fixed"<?=$type=='Fixed'?' selected':''?>>Fixed</option>
                </select>
                <label for="active">Active</label>
                <select name="active" id="active">
                    <option value=""<?=$active==''?' selected':''?>>All</option>
                    <option value="yes"<?=$active=='yes'?' selected':''?>>Yes</option>
                    <option value="no"<?=$active=='no'?' selected':''?>>No</option>
                </select>
                <button type="submit">Apply</button>
            </div>
        </div>
        <div class="search">
            <label for="search_query">
                <input id="search_query" type="text" name="search_query" placeholder="Search discounts..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>" class="responsive-width-100">
                <i class="bi bi-search" aria-hidden="true"></i>
            </label>
        </div>
    </form>
</div>

<div class="filter-list">
    <?php if ($type != ''): ?>
    <div class="filter">
    <a href="<?=remove_url_param($url, 'type')?>"><i class="bi bi-x-circle" aria-hidden="true"></i></a>
        Type : <?=htmlspecialchars($type, ENT_QUOTES)?>
    </div>
    <?php endif; ?>
    <?php if ($active != ''): ?>
    <div class="filter">
    <a href="<?=remove_url_param($url, 'active')?>"><i class="bi bi-x-circle" aria-hidden="true"></i></a>
        Active : <?=$active == 1 ? 'Yes' : 'No'?>
    </div>
    <?php endif; ?>
    <?php if ($search != ''): ?>
    <div class="filter">
    <a href="<?=remove_url_param($url, 'search_query')?>"><i class="bi bi-x-circle" aria-hidden="true"></i></a>
        Search : <?=htmlspecialchars($search, ENT_QUOTES)?>
    </div>
    <?php endif; ?>   
</div>

<div class="content-block no-pad">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=id'?>">#<?=$order_by=='id' ? $table_icons[strtolower($order)] : ''?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=discount_code'?>">Code<?=$order_by=='discount_code' ? $table_icons[strtolower($order)] : ''?></a></td>
                    <td>Active</td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=category_names'?>">Categories<?=$order_by=='category_names' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=product_names'?>">Products<?=$order_by=='product_names' ? $table_icons[strtolower($order)] : ''?></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=discount_type'?>">Type<?=$order_by=='discount_type' ? $table_icons[strtolower($order)] : ''?></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=discount_value'?>">Value<?=$order_by=='discount_value' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=start_date'?>">Start Date<?=$order_by=='start_date' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=end_date'?>">End Date<?=$order_by=='end_date' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="align-center">Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$discounts): ?>
                <tr>
                    <td colspan="20" class="no-results">There are no discounts.</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($discounts as $discount): ?>
                <tr>
                    <td class="responsive-hidden alt"><?=$discount['id']?></td>
                    <td><?=$discount['discount_code']?></td>
                    <td>
                        <?php if ($current_date >= strtotime($discount['start_date']) && $current_date <= strtotime($discount['end_date'])): ?>
                        <svg stroke="#34aa6b" fill="#34aa6b" width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>
                        <?php else: ?>
                        <svg stroke="#b64343" fill="#b64343" width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                        <?php endif; ?>
                    </td>
                    <td class="responsive-hidden" style="max-width:300px">
                    <?php
                    if (empty($discount['category_names'])) {
                        echo '<span class="grey">All</span>';
                    } else {
                        $categories = explode(',', $discount['category_names']);
                        foreach ($categories as $c) {
                            echo '<span class="grey mar-right-1 mar-bot-1">' . $c . '</span>';
                        }
                    }
                    ?>
                    </td>
                    <td class="responsive-hidden" style="max-width:300px">
                    <?php
                    if (empty($discount['product_names'])) {
                        echo '<span class="grey">All</span>';
                    } else {
                        $products = explode(',', $discount['product_names']);
                        foreach ($products as $p) {
                            echo '<span class="grey mar-right-1 mar-bot-1">' . $p . '</span>';
                        }
                    }
                    ?>
                    </td>
                    <td><span class="blue"><?=$discount['discount_type']?></span></td>
                    <td class="strong"><?=$discount['discount_value']?></td>
                    <td class="responsive-hidden alt"><?=date('Y-m-d h:ia', strtotime($discount['start_date']))?></td>
                    <td class="responsive-hidden alt"><?=date('Y-m-d h:ia', strtotime($discount['end_date']))?></td>
                    <td class="actions">
                        <div class="table-dropdown">
                            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                            <div class="table-dropdown-items">
                                <a href="index.php?page=discount&id=<?=$discount['id']?>">
                                    <span class="icon"><i class="bi bi-pencil" aria-hidden="true"></i></span>
                                    Edit
                                </a>
                                <a class="red" href="index.php?page=discounts&delete=<?=$discount['id']?>" onclick="return confirm('Are you sure you want to delete this discount?')">
                                    <span class="icon"><i class="bi bi-trash" aria-hidden="true"></i></span>    
                                    Delete
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    <?php if ($pagination_page > 1): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page-1?>&order=<?=$order?>&order_by=<?=$order_by?>">Prev</a>
    <?php endif; ?>
    <span>Page <?=$pagination_page?> of <?=ceil($total_discounts / $results_per_pagination_page) == 0 ? 1 : ceil($total_discounts / $results_per_pagination_page)?></span>
    <?php if ($pagination_page * $results_per_pagination_page < $total_discounts): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>