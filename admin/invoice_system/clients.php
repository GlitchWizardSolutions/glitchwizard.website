<?php
include 'main.php';
// Retrieve the GET request parameters (if specified)
$pagination_page = isset($_GET['pagination_page']) ? $_GET['pagination_page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','first_name','last_name','email','phone','address_street','address_city','address_state','address_zip','address_country','created','total_invoices'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination pagination_page
$results_per_pagination_page = 20;
// clients array
$clients = [];
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_pagination_page;
$param2 = $results_per_pagination_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (CONCAT(c.first_name, " ", c.last_name) LIKE :search OR c.email LIKE :search) ' : '';
// Retrieve the total number of clients
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM invoice_clients c ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
$total_clients = $stmt->fetchColumn();
// Prepare clients query
$stmt = $pdo->prepare('SELECT c.*, (SELECT COUNT(*) FROM invoices i WHERE i.client_id = c.id) AS total_invoices FROM invoice_clients c ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete client
if (isset($_GET['delete'])) {
    // Delete the client
    $stmt = $pdo->prepare('DELETE FROM invoice_clients WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: clients.php?success_msg=3');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Client created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Client updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Client deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Client(s) imported successfully! ' . $_GET['imported'] . ' client(s) were imported.';
    }
}
// Create URL
$url = 'clients.php?search_query=' . $search;

// CANONICAL TABLE SORTING: Use triangle icons to match accounts.php standard
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>', // ▲
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>' // ▼
];
?>
<?=template_admin_header('Clients', 'invoices', 'clients_view')?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <i class="bi bi-people-fill" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Clients</h2>
            <p>View, edit, and create clients.</p>
        </div>
    </div>
</div>

<div style="height: 20px;"></div>

<div class="d-flex gap-2 mb-4">
    <a href="client.php" class="btn btn-outline-secondary">
        <i class="bi bi-plus me-1"></i>Create Client
    </a>
</div>

<?php if (isset($success_msg)): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>
    <?=$success_msg?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h6 class="card-title mb-0">Client Management</h6>
        <small class="text-muted">Search, filter, and manage client accounts</small>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form method="get" action="" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="search_query" class="form-label">Search Clients</label>
                    <input type="text" id="search_query" name="search_query" 
                           value="<?=htmlspecialchars($search, ENT_QUOTES)?>" 
                           class="form-control" 
                           placeholder="Search by name or email...">
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
                        <?php if (!empty($search)): ?>
                        <a href="clients.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>Clear
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>

        <?php if (!empty($search)): ?>
        <div class="mb-3">
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-secondary">
                    Search: <?= htmlspecialchars($search, ENT_QUOTES) ?>
                    <a href="<?= remove_url_param($url, 'search_query') ?>" class="text-white ms-1" aria-label="Remove search filter">×</a>
                </span>
            </div>
        </div>
        <?php endif; ?>
</div>

        <div class="table-responsive">
            <table class="table table-hover" role="grid" aria-label="Clients List">
                <thead class="table-light" role="rowgroup">
                    <tr role="row">
                        <th class="text-center" role="columnheader" scope="col" style="width: 60px;">Avatar</th>
                        <th class="text-start" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'first_name'; $q['order'] = ($order_by == 'first_name' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Name <?= $order_by == 'first_name' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-start d-none d-md-table-cell" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'email'; $q['order'] = ($order_by == 'email' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Email <?= $order_by == 'email' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center d-none d-lg-table-cell" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'phone'; $q['order'] = ($order_by == 'phone' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Phone <?= $order_by == 'phone' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center d-none d-lg-table-cell" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'total_invoices'; $q['order'] = ($order_by == 'total_invoices' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Invoices <?= $order_by == 'total_invoices' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center d-none d-lg-table-cell" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'created'; $q['order'] = ($order_by == 'created' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Created <?= $order_by == 'created' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col" style="width: 80px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$clients): ?>
                    <tr role="row">
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-people-fill fs-2 mb-2 d-block" aria-hidden="true"></i>
                            No clients found. <a href="client.php">Create your first client</a>.
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($clients as $client): ?>
                    <?php
                    $address = [
                        $client['address_street'],
                        $client['address_city'],
                        $client['address_state'],
                        $client['address_zip'],
                        $client['address_country']
                    ];
                    $address = implode(', ', array_filter($address));
                    ?>
                    <tr role="row">
                        <td class="text-center">
                            <div class="profile-img">
                                <?php 
                                // Use getUserAvatar function with proper account data like invoices.php
                                $account_data = array(
                                    'avatar' => $client['avatar'] ?? '',
                                    'role' => 'Client'
                                );
                                $avatar_url = getUserAvatar($account_data);
                                ?>
                                <img src="<?= $avatar_url ?>" 
                                     alt="<?= htmlspecialchars($client['first_name'], ENT_QUOTES) ?> avatar" 
                                     class="avatar-img"
                                     style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;" />
                            </div>
                        </td>
                        <td class="text-start">
                            <strong><?=htmlspecialchars($client['first_name'] . ' ' . $client['last_name'], ENT_QUOTES)?></strong>
                        </td>
                        <td class="text-start d-none d-md-table-cell">
                            <a href="mailto:<?=htmlspecialchars($client['email'], ENT_QUOTES)?>" class="text-decoration-none">
                                <?=htmlspecialchars($client['email'], ENT_QUOTES)?>
                            </a>
                        </td>
                        <td class="text-center d-none d-lg-table-cell">
                            <?php if ($client['phone']): ?>
                                <a href="tel:<?=htmlspecialchars($client['phone'], ENT_QUOTES)?>" class="text-decoration-none">
                                    <?=htmlspecialchars($client['phone'], ENT_QUOTES)?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center d-none d-lg-table-cell">
                            <?php if ($client['total_invoices'] > 0): ?>
                                <a href="invoices.php?client_id=<?=$client['id']?>" class="btn btn-sm btn-outline-primary">
                                    <?=$client['total_invoices']?> <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">0</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center d-none d-lg-table-cell">
                            <small class="text-muted" title="<?=date('F j, Y \a\t g:i A', strtotime($client['created']))?>">
                                <?=date('n/j/Y', strtotime($client['created']))?>
                            </small>
                        </td>
                        <td class="actions text-center">
                            <div class="table-dropdown">
                                <button class="actions-btn" aria-haspopup="true" aria-expanded="false"
                                    aria-label="Actions for <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name'], ENT_QUOTES) ?>">
                                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                        <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                    </svg>
                                </button>
                                <div class="table-dropdown-items" role="menu" aria-label="Client Actions">
                                    <div role="menuitem">
                                        <a href="client.php?id=<?=$client['id']?>" 
                                           class="green" 
                                           tabindex="-1"
                                           aria-label="Edit client <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name'], ENT_QUOTES) ?>">
                                            <span class="icon" aria-hidden="true">
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                                </svg>
                                            </span>
                                            <span>Edit</span>
                                        </a>
                                    </div>
                                    <div role="menuitem">
                                        <a href="invoices.php?client_id=<?=$client['id']?>" 
                                           class="blue" 
                                           tabindex="-1"
                                           aria-label="View invoices for <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name'], ENT_QUOTES) ?>">
                                            <span class="icon" aria-hidden="true">
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                                    <path d="M64 464c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16H224v80c0 17.7 14.3 32 32 32h80V448c0 8.8-7.2 16-16 16H64zM64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V154.5c0-17-6.7-33.3-18.7-45.3L274.7 18.7C262.7 6.7 246.5 0 229.5 0H64zm56 256c-13.3 0-24 10.7-24 24s10.7 24 24 24H264c13.3 0 24-10.7 24-24s-10.7-24-24-24H120zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H264c13.3 0 24-10.7 24-24s-10.7-24-24-24H120z" />
                                                </svg>
                                            </span>
                                            <span>View Invoices</span>
                                        </a>
                                    </div>
                                    <div role="menuitem">
                                        <a href="clients.php?delete=<?=$client['id']?>" 
                                           class="red" 
                                           tabindex="-1"
                                           onclick="return confirm('Are you sure you want to delete this client?')"
                                           aria-label="Delete client <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name'], ENT_QUOTES) ?>">
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
                </tbody>
            </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_clients > $results_per_pagination_page): ?>
        <nav aria-label="Clients pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php 
                $total_pages = ceil($total_clients / $results_per_pagination_page);
                
                // Previous page
                if ($pagination_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, array('pagination_page' => $pagination_page - 1))) ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">&laquo;</span>
                    </li>
                <?php endif; ?>
                
                <?php
                // Calculate page range
                $start = max(1, $pagination_page - 2);
                $end = min($total_pages, $pagination_page + 2);
                
                // First page if not in range
                if ($start > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, array('pagination_page' => 1))) ?>">1</a>
                    </li>
                    <?php if ($start > 2): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <?php if ($i == $pagination_page): ?>
                        <li class="page-item active" aria-current="page">
                            <span class="page-link"><?= $i ?></span>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, array('pagination_page' => $i))) ?>"><?= $i ?></a>
                        </li>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php
                // Last page if not in range
                if ($end < $total_pages): ?>
                    <?php if ($end < $total_pages - 1): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, array('pagination_page' => $total_pages))) ?>"><?= $total_pages ?></a>
                    </li>
                <?php endif; ?>
                
                <!-- Next page -->
                <?php if ($pagination_page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, array('pagination_page' => $pagination_page + 1))) ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">&raquo;</span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>