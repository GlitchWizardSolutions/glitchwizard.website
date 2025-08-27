<?php
defined('shoppingcart_admin') or exit;
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
// Filters parameters
$type = isset($_GET['type']) ? $_GET['type'] : '';
$country = isset($_GET['country']) ? $_GET['country'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','title','shipping_type','countries','price_from','price_to','weight_from','weight_to','price'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination pagination_page
$results_per_pagination_page = 15;
// shipping array
$shipping = [];
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_pagination_page;
$param2 = $results_per_pagination_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (s.title LIKE :search OR s.countries LIKE :search OR s.price LIKE :search) ' : '';
// Add filters
// Type filter
if ($type) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 's.shipping_type = :type ';
}
// Country filter
if ($country) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 's.countries LIKE :country OR s.countries = "" ';
}
// Retrieve the total number of shipping
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM shop_shipping s ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($type) $stmt->bindParam('type', $type, PDO::PARAM_STR);
if ($country) $stmt->bindParam('country', $country, PDO::PARAM_STR);
$stmt->execute();
$total_shipping = $stmt->fetchColumn();
// Prepare shipping query
$stmt = $pdo->prepare('SELECT * FROM shop_shipping s ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($type) $stmt->bindParam('type', $type, PDO::PARAM_STR);
if ($country) $stmt->bindParam('country', $country, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$shipping = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete shipping
if (isset($_GET['delete'])) {
    // Delete the shipping
    $stmt = $pdo->prepare('DELETE FROM shop_shipping WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: index.php?page=shipping&success_msg=3');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Shipping created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Shipping updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Shipping deleted successfully!';
    }
}
// Create URL
$url = 'index.php?page=shipping&search_query=' . $search . '&type=' . $type . '&country=' . $country;
?>
<?=template_admin_header('Shipping', 'shipping', 'view')?>

<div class="content-title">
    <div class="title">
    <div class="icon"><i class="bi bi-truck" aria-hidden="true"></i></div>
        <div class="txt">
            <h2>Shipping</h2>
            <p>View, edit, and create shipping methods</p>
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
    <a href="index.php?page=shipping_process" class="btn">
    <i class="bi bi-plus me-1" aria-hidden="true"></i>Create Shipping
    </a>
    <form method="get">
        <input type="hidden" name="page" value="shipping">
        <div class="filters">
            <a href="#"><i class="bi bi-sliders" aria-hidden="true"></i> Filters</a>
            <div class="list">
                <label for="type">Type</label>
                <select name="type" id="type">
                    <option value=""<?=$type==''?' selected':''?>>All</option>
                    <option value="Entire Order"<?=$type=='Entire Order'?' selected':''?>>Entire Order</option>
                    <option value="Single Product"<?=$type=='Single Product'?' selected':''?>>Single Product</option>
                </select>
                <label for="country">Country</label>
                <select name="country" id="country">
                    <option value="">All</option>
                    <?php foreach (get_countries() as $c): ?>
                    <option value="<?=$c?>"<?=$c==$country?' selected':''?>><?=$c?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Apply</button>
            </div>
        </div>
        <div class="search">
            <label for="search_query">
                <input id="search_query" type="text" name="search_query" placeholder="Search shipping..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>" class="responsive-width-100">
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
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=title'?>">Title<?=$order_by=='title' ? $table_icons[strtolower($order)] : ''?></a></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=shipping_type'?>">Type<?=$order_by=='shipping_type' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=countries'?>">Countries<?=$order_by=='countries' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=price_from'?>">Price Range<?=$order_by=='price_from' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=weight_from'?>">Weight Range<?=$order_by=='weight_from' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=price'?>">Total Shipping Price<?=$order_by=='price' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="align-center">Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$shipping): ?>
                <tr>
                    <td colspan="20" class="no-results">There are no shipping methods.</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($shipping as $s): ?>
                <tr>
                    <td class="responsive-hidden alt"><?=$s['id']?></td>
                    <td><?=$s['title']?></td>
                    <td class="responsive-hidden"><span class="<?=$s['shipping_type']=='Entire Order'?'green':'orange'?>"><?=$s['shipping_type']?></span></td>
                    <td class="responsive-hidden" style="max-width:300px">
                    <?php
                    if (empty($s['countries'])) {
                        echo '<span class="grey">All</span>';
                    } else {
                        $countries = explode(',', $s['countries']);
                        foreach ($countries as $c) {
                            echo '<span class="grey mar-right-1 mar-bot-1">' . $c . '</span>';
                        }
                    }
                    ?>
                    </td>
                    <td class="responsive-hidden alt"><?=currency_code?><?=num_format($s['price_from'], 2)?> - <?=currency_code?><?=num_format($s['price_to'], 2)?></td>
                    <td class="responsive-hidden alt"><?=num_format($s['weight_from'], 2)?> <?=weight_unit?> - <?=num_format($s['weight_to'], 2)?> <?=weight_unit?></td>
                    <td class="strong"><?=currency_code?><?=num_format($s['price'], 2)?></td>
                    <td class="actions">
                        <div class="table-dropdown">
                            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                            <div class="table-dropdown-items">
                                <a href="index.php?page=shipping_process&id=<?=$s['id']?>">
                                    <span class="icon">
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </span>
                                    Edit
                                </a>
                                <a class="red" href="index.php?page=shipping&delete=<?=$s['id']?>" onclick="return confirm('Are you sure you want to delete this shipping method?')">
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
    <span>Page <?=$pagination_page?> of <?=ceil($total_shipping / $results_per_pagination_page) == 0 ? 1 : ceil($total_shipping / $results_per_pagination_page)?></span>
    <?php if ($pagination_page * $results_per_pagination_page < $total_shipping): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>