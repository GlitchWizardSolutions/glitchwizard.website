<?php
defined('shoppingcart_admin') or exit;
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
// Filters parameters
$country = isset($_GET['country']) ? $_GET['country'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','country','rate','rate_type','rules'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination pagination_page
$results_per_pagination_page = 15;
// taxes array
$taxes = [];
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_pagination_page;
$param2 = $results_per_pagination_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (t.country LIKE :search OR t.rate LIKE :search) ' : '';
// Add filters
// Country filter
if ($country) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 't.country = :country ';
}
// Retrieve the total number of taxes
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM shop_taxes t ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($country) $stmt->bindParam('country', $country, PDO::PARAM_STR);
$stmt->execute();
$total_taxes = $stmt->fetchColumn();
// Prepare taxes query
$stmt = $pdo->prepare('SELECT t.* FROM shop_taxes t ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . '  LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($country) $stmt->bindParam('country', $country, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$taxes = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete taxes
if (isset($_GET['delete'])) {
    // Delete the taxes
    $stmt = $pdo->prepare('DELETE FROM shop_taxes WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: index.php?page=taxes&success_msg=3');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Tax created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Tax updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Tax deleted successfully!';
    }
}
// Create URL
$url = 'index.php?page=taxes&search_query=' . $search . '&country=' . $country;
?>
<?=template_admin_header('Taxes', 'taxes', 'view')?>

<div class="content-title">
    <div class="title">
    <div class="icon"><i class="bi bi-receipt" aria-hidden="true"></i></div>
        <div class="txt">
            <h2>Taxes</h2>
            <p>View, edit, and create taxes</p>
        </div>
    </div>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success alert alert-success d-flex align-items-center" role="alert">
    <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>
    <p class="m-0 flex-grow-1"><?=$success_msg?></p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="content-header responsive-flex-column pad-top-5">
    <a href="index.php?page=tax" class="btn">
    <i class="bi bi-plus me-1" aria-hidden="true"></i>Create Tax
    </a>
    <form method="get">
        <input type="hidden" name="page" value="taxes">
        <div class="filters">
            <a href="#"><i class="bi bi-sliders" aria-hidden="true"></i> Filters</a>
            <div class="list">
                <label for="country">Country</label>
                <select name="country" id="country">
                    <option value="">All</option>
                    <?php foreach (get_countries() as $c): ?>
                    <option value="<?=$c?>"<?=$country==$c?' selected':''?>><?=$c?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Apply</button>
            </div>
        </div>
        <div class="search">
            <label for="search_query">
                <input id="search_query" type="text" name="search_query" placeholder="Search taxes..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>" class="responsive-width-100">
                <i class="bi bi-search" aria-hidden="true"></i>
            </label>
        </div>
    </form>
</div>

<div class="filter-list">
    <?php if ($country != ''): ?>
    <div class="filter">
    <a href="<?=remove_url_param($url, 'country')?>"><i class="bi bi-x-circle" aria-hidden="true"></i></a>
        Country : <?=htmlspecialchars($country, ENT_QUOTES)?>
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
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=country'?>">Country<?=$order_by=='country' ? $table_icons[strtolower($order)] : ''?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=rate'?>">Rate<?=$order_by=='rate' ? $table_icons[strtolower($order)] : ''?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=rate_type'?>">Rate Type<?=$order_by=='rate_type' ? $table_icons[strtolower($order)] : ''?></a></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=rules'?>">Rules<?=$order_by=='rules' ? $table_icons[strtolower($order)] : ''?></a></td>
                    <td class="align-center">Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$taxes): ?>
                <tr>
                    <td colspan="20" class="no-results">There are no taxes.</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($taxes as $tax): ?>
                <tr>
                    <td class="responsive-hidden alt"><?=$tax['id']?></td>
                    <td><?=$tax['country']?></td>
                    <td class="strong"><?=$tax['rate']?></td>
                    <td><span class="grey"><?=$tax['rate_type']?></span></td>
                    <td><?=$tax['rules'] ? '<span class="green small">' . count(json_decode($tax['rules'], true)) . ' Rules</span>' : '<span class="grey small">No rules</span>'?></td>
                    <td class="actions">
                        <div class="table-dropdown">
                            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                            <div class="table-dropdown-items">
                                <a href="index.php?page=tax&id=<?=$tax['id']?>">
                                    <span class="icon">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </span>
                                    Edit
                                </a>
                                <a class="red" href="index.php?page=taxes&delete=<?=$tax['id']?>" onclick="return confirm('Are you sure you want to delete this tax?')">
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
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    <?php if ($pagination_page > 1): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page-1?>&order=<?=$order?>&order_by=<?=$order_by?>">Prev</a>
    <?php endif; ?>
    <span>Page <?=$pagination_page?> of <?=ceil($total_taxes / $results_per_pagination_page) == 0 ? 1 : ceil($total_taxes / $results_per_pagination_page)?></span>
    <?php if ($pagination_page * $results_per_pagination_page < $total_taxes): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>