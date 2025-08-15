<?php
/* 
 * User Accounts Management Interface
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: accounts.php
 * LOCATION: /public_html/admin/accounts/
 * PURPOSE: Provides a comprehensive interface for managing all user accounts with filtering,
 *         sorting, and pagination capabilities.
 * 
 * FILE RELATIONSHIP:
 * This file integrates with:
 * - ../assets/includes/main.php: Core functionality and database connection
 * - User authentication system
 * - Role management system
 * - Account settings configuration
 * 
 * HOW IT WORKS:
 * 1. Loads account data with configurable filters (status, role, last seen)
 * 2. Implements pagination for large datasets
 * 3. Provides sorting capabilities for all columns
 * 4. Supports search functionality across username and email
 * 5. Displays account information in an accessible, sortable table
 * 
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: YES
 * 
 * FEATURES:
 * - Advanced filtering system (status, role, last seen)
 * - Sortable columns with directional indicators
 * - Pagination support
 * - Search functionality
 * - Role-based display
 * - Last seen tracking
 * - Accessible table layout
 */

include_once '../assets/includes/main.php';

// Use simple triangle icons for sort direction, matching posts.php
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>', // ▲
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>' // ▼
];
// Retrieve the GET request parameters (if specified)
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
// Filters parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$role = isset($_GET['role']) ? $_GET['role'] : '';
$last_seen = isset($_GET['last_seen']) ? $_GET['last_seen'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id', 'username', 'email', 'activation_code', 'role', 'registered', 'last_seen'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 20;
// Accounts array
$accounts = [];
// Declare query param variables
$param1 = ($page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (username LIKE :search OR email LIKE :search) ' : '';
// Add filters
// Role filter
if ($role)
{
    $where .= ($where ? 'AND ' : 'WHERE ') . 'role = :role ';
}
// Last seen filter
if ($last_seen == 'today')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . 'last_seen > date_sub("' . date('Y-m-d H:i:s') . '", interval 1 day) ';
} else if ($last_seen == 'yesterday')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . 'last_seen > date_sub("' . date('Y-m-d H:i:s') . '", interval 2 day) AND last_seen < date_sub("' . date('Y-m-d H:i:s') . '", interval 1 day) ';
} else if ($last_seen == 'week')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . 'last_seen > date_sub("' . date('Y-m-d H:i:s') . '", interval 1 week) ';
} else if ($last_seen == 'month')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . 'last_seen > date_sub("' . date('Y-m-d H:i:s') . '", interval 1 month) ';
} else if ($last_seen == 'year')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . 'last_seen > date_sub("' . date('Y-m-d H:i:s') . '", interval 1 year) ';
} else if ($last_seen == 'inactive')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . 'last_seen < date_sub("' . date('Y-m-d H:i:s') . '", interval 1 month) ';
}
// Status filter
if ($status == 'Activated')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . 'activation_code = "activated" ';
} else if ($status == 'Deactivated')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . 'activation_code = "deactivated" ';
} else if ($status == 'Pending Activation')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . 'activation_code != "activated" AND activation_code != "deactivated" ';
} else if ($status == 'Approved')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . 'approved = 1 ';
} else if ($status == 'Pending Approval')
{
    $where .= ($where ? 'AND ' : 'WHERE ') . 'approved = 0 ';
}
// Retrieve the total number of accounts
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM accounts ' . $where);
if ($search)
    $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($role)
    $stmt->bindParam('role', $role, PDO::PARAM_STR);
$stmt->execute();
$total_accounts = $stmt->fetchColumn();
// Prepare accounts query
$stmt = $pdo->prepare('SELECT * FROM accounts ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search)
    $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($role)
    $stmt->bindParam('role', $role, PDO::PARAM_STR);
$stmt->execute();
// Retrieve query results
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete account
if (isset($_GET['delete']))
{
    // Delete the account
    $stmt = $pdo->prepare('DELETE FROM accounts WHERE id = ?');
    $stmt->execute([$_GET['delete']]);
    header('Location: accounts.php?success_msg=3');
    exit;
}
// deactivate (also remove remember me code)
if (isset($_GET['deactivate']))
{
    // Update the account
    $stmt = $pdo->prepare('UPDATE accounts SET activation_code = "deactivated", remember_me_code = "" WHERE id = ?');
    $stmt->execute([$_GET['deactivate']]);
    header('Location: accounts.php?success_msg=2');
    exit;
}
// activate
if (isset($_GET['activate']))
{
    // Update the account
    $stmt = $pdo->prepare('UPDATE accounts SET activation_code = "activated" WHERE id = ?');
    $stmt->execute([$_GET['activate']]);
    header('Location: accounts.php?success_msg=2');
    exit;
}
// approve
if (isset($_GET['approve']))
{
    // Update the account
    $stmt = $pdo->prepare('UPDATE accounts SET approved = 1 WHERE id = ?');
    $stmt->execute([$_GET['approve']]);
    header('Location: accounts.php?success_msg=2');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg']))
{
    if ($_GET['success_msg'] == 1)
    {
        $success_msg = 'Account created successfully!';
    }
    if ($_GET['success_msg'] == 2)
    {
        $success_msg = 'Account updated successfully!';
    }
    if ($_GET['success_msg'] == 3)
    {
        $success_msg = 'Account deleted successfully!';
    }
    if ($_GET['success_msg'] == 4)
    {
        $success_msg = 'Accounts imported successfully! ' . $_GET['imported'] . ' accounts were imported.';
    }
}
// Create URL
$url = 'accounts.php?search_query=' . $search . '&status=' . $status . '&role=' . $role . '&last_seen=' . $last_seen;
?>
<?= template_admin_header('Accounts', 'accounts', 'view') ?>

<div class="content-title mb-4" id="main-accounts-list" role="banner" aria-label="Accounts List Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path
                    d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z" />
            </svg>
        </div>
        <div class="txt">
            <h2>Accounts</h2>
            <p>View, edit, and create accounts.</p>
        </div>
    </div>
</div>

<?php if (isset($success_msg)): ?>
    <div class="mb-4" role="region" aria-label="Success Message">
        <div class="msg success" role="alert" aria-live="polite">
            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path
                    d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
            </svg>
            <p><?= $success_msg ?></p>
            <button type="button" class="close-success" aria-label="Dismiss success message" onclick="this.parentElement.parentElement.style.display='none'">
                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path
                        d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                </svg>
            </button>
        </div>
    </div>
<?php endif; ?>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="account.php" class="btn btn-outline-secondary">
        <i class="fas fa-plus me-1" aria-hidden="true"></i>
        Add Account
    </a>
</div>
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Account Management</h6>
        <small class="text-muted"><?= $total_accounts ?> total accounts</small>
    </div>
    <div class="card-body">
        <form action="" method="get" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search_query" class="form-label">Search</label>
                    <input id="search_query" type="text" name="search_query" class="form-control"
                        placeholder="Search accounts..." 
                        value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
                </div>
                <div class="col-md-2">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select">
                        <option value="" <?= $role == '' ? ' selected' : '' ?>>All</option>
                        <option value="Admin" <?= $role == 'Admin' ? ' selected' : '' ?>>Admin</option>
                        <option value="Member" <?= $role == 'Member' ? ' selected' : '' ?>>Member</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="last_seen" class="form-label">Last Seen</label>
                    <select name="last_seen" id="last_seen" class="form-select">
                        <option value="" <?= $last_seen == '' ? ' selected' : '' ?>>All</option>
                        <option value="today" <?= $last_seen == 'today' ? ' selected' : '' ?>>Today</option>
                        <option value="yesterday" <?= $last_seen == 'yesterday' ? ' selected' : '' ?>>Yesterday</option>
                        <option value="week" <?= $last_seen == 'week' ? ' selected' : '' ?>>This Week</option>
                        <option value="month" <?= $last_seen == 'month' ? ' selected' : '' ?>>This Month</option>
                        <option value="year" <?= $last_seen == 'year' ? ' selected' : '' ?>>This Year</option>
                        <option value="inactive" <?= $last_seen == 'inactive' ? ' selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="" <?= $status == '' ? ' selected' : '' ?>>All</option>
                        <option value="Activated" <?= $status == 'Activated' ? ' selected' : '' ?>>Activated</option>
                        <option value="Deactivated" <?= $status == 'Deactivated' ? ' selected' : '' ?>>Deactivated</option>
                        <option value="Pending Activation" <?= $status == 'Pending Activation' ? ' selected' : '' ?>>Pending Activation</option>
                        <option value="Approved" <?= $status == 'Approved' ? ' selected' : '' ?>>Approved</option>
                        <option value="Pending Approval" <?= $status == 'Pending Approval' ? ' selected' : '' ?>>Pending Approval</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-filter me-1" aria-hidden="true"></i>
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>

        <!-- Active Filters -->
        <?php if ($role || $last_seen || $status || $search): ?>
            <div class="mb-3">
                <h6 class="mb-2">Active Filters:</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($role): ?>
                        <span class="badge bg-secondary">
                            Role: <?= htmlspecialchars($role, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'role') ?>" class="text-white ms-1" aria-label="Remove role filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($last_seen): ?>
                        <span class="badge bg-secondary">
                            Last Seen: <?= htmlspecialchars($last_seen, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'last_seen') ?>" class="text-white ms-1" aria-label="Remove last seen filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($status): ?>
                        <span class="badge bg-secondary">
                            Status: <?= htmlspecialchars($status, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'status') ?>" class="text-white ms-1" aria-label="Remove status filter">×</a>
                        </span>
                    <?php endif; ?>
                    <?php if ($search): ?>
                        <span class="badge bg-secondary">
                            Search: <?= htmlspecialchars($search, ENT_QUOTES) ?>
                            <a href="<?= remove_url_param($url, 'search_query') ?>" class="text-white ms-1" aria-label="Remove search filter">×</a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover" role="grid" aria-label="User Accounts">
                <thead class="table-light" role="rowgroup">
                    <tr role="row">
                        <th class="text-center" role="columnheader" scope="col" style="width: 60px;">Avatar</th>
                        <th class="text-start" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'username'; $q['order'] = ($order_by == 'username' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Username <?= $order_by == 'username' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-start d-none d-md-table-cell" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'email'; $q['order'] = ($order_by == 'email' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Email <?= $order_by == 'email' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center d-none d-lg-table-cell" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'activation_code'; $q['order'] = ($order_by == 'activation_code' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Status <?= $order_by == 'activation_code' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center d-none d-lg-table-cell" role="columnheader" scope="col">
                            <?php $q = $_GET; $q['order_by'] = 'last_seen'; $q['order'] = ($order_by == 'last_seen' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                            <a href="?<?= http_build_query($q) ?>" class="text-decoration-none">
                                Last Seen <?= $order_by == 'last_seen' ? $table_icons[strtolower($order)] : '' ?>
                            </a>
                        </th>
                        <th class="text-center" role="columnheader" scope="col" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
            <tbody>
                <?php if (!$accounts): ?>
                    <tr>
                        <td colspan="6" class="no-results">There are no accounts.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($accounts as $account): ?>
                    <tr>
                        <td style="text-align: center;">
                            <div class="profile-img">
                                <img src="<?= getUserAvatar($account) ?>"
                                    alt="<?= htmlspecialchars($account['username'], ENT_QUOTES) ?> avatar"
                                    class="avatar-img"
                                    style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;" />
                                <?php if ($account['last_seen'] > date('Y-m-d H:i:s', strtotime('-15 minutes'))): ?>
                                    <i class="online" title="Online"></i>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-left"><?= htmlspecialchars($account['username'], ENT_QUOTES) ?></td>
                        <td class="text-left responsive-hidden"><?= htmlspecialchars($account['email'], ENT_QUOTES) ?></td>
                        <td class="responsive-hidden" style="text-align: center;">
                            <?php if (!$account['approved']): ?>
                                <span class="orange">Pending Approval</span>
                            <?php elseif ($account['activation_code'] == 'activated'): ?>
                                <span class="green">Activated</span>
                            <?php elseif ($account['activation_code'] == 'deactivated'): ?>
                                <span class="red">Deactivated</span>
                            <?php else: ?>
                                <span class="grey" title="<?= $account['activation_code'] ?>">Pending Activation</span>
                            <?php endif; ?>
                        </td>
                        <td class="responsive-hidden" style="text-align: center;" title="<?= $account['last_seen'] ?>">
                            <?= time_elapsed_string($account['last_seen']) ?>
                        </td>
                        <td class="actions" style="text-align: center;">
                            <div class="table-dropdown">
                                <button class="actions-btn" aria-haspopup="true" aria-expanded="false"
                                    aria-label="Actions for <?= htmlspecialchars($account['username'], ENT_QUOTES) ?>">
                                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                        <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                    </svg>
                                </button>
                                <div class="table-dropdown-items" role="menu" aria-label="Account Actions">
                                    <div role="menuitem">
                                        <a href="documents.php?account_id=<?= $account['id'] ?>" 
                                           class="blue" 
                                           tabindex="-1" 
                                           aria-label="View documents for <?= htmlspecialchars($account['username'], ENT_QUOTES) ?>">
                                            <span class="icon" aria-hidden="true">
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                                    <path d="M64 464c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16H224v80c0 17.7 14.3 32 32 32h80V448c0 8.8-7.2 16-16 16H64zM64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V154.5c0-17-6.7-33.3-18.7-45.3L274.7 18.7C262.7 6.7 246.5 0 229.5 0H64zm56 256c-13.3 0-24 10.7-24 24s10.7 24 24 24H264c13.3 0 24-10.7 24-24s-10.7-24-24-24H120zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H264c13.3 0 24-10.7 24-24s-10.7-24-24-24H120z" />
                                                </svg>
                                            </span>
                                            <span>Documents</span>
                                        </a>
                                    </div>
                                    <div role="menuitem">
                                        <a href="account.php?id=<?= $account['id'] ?>" 
                                           class="green" 
                                           tabindex="-1"
                                           aria-label="Edit account for <?= htmlspecialchars($account['username'], ENT_QUOTES) ?>">
                                            <span class="icon" aria-hidden="true">
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                                </svg>
                                            </span>
                                            <span>Edit</span>
                                        </a>
                                    </div>
                                    <?php if ($account['activation_code'] != 'activated'): ?>
                                        <div role="menuitem">
                                            <a class="green" 
                                               href="accounts.php?activate=<?= $account['id'] ?>"
                                               onclick="return confirm('Are you sure you want to activate this account?')"
                                               tabindex="-1"
                                               aria-label="Activate account for <?= htmlspecialchars($account['username'], ENT_QUOTES) ?>">
                                                <span class="icon" aria-hidden="true">
                                                    <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                                        <path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM625 177L497 305c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L591 143c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
                                                    </svg>
                                                </span>
                                                <span>Activate</span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($account['activation_code'] != 'deactivated'): ?>
                                        <div role="menuitem">
                                            <a class="black" 
                                               href="accounts.php?deactivate=<?= $account['id'] ?>"
                                               onclick="return confirm('Are you sure you want to deactivate this account? They will no longer be able to log in.')"
                                               tabindex="-1"
                                               aria-label="Deactivate account for <?= htmlspecialchars($account['username'], ENT_QUOTES) ?>">
                                                <span class="icon" aria-hidden="true">
                                                    <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                                        <path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L353.3 251.6C407.9 237 448 187.2 448 128C448 57.3 390.7 0 320 0C250.2 0 193.5 55.8 192 125.2L38.8 5.1zM264.3 304.3C170.5 309.4 96 387.2 96 482.3c0 16.4 13.3 29.7 29.7 29.7H514.3c3.9 0 7.6-.7 11-2.1l-261-205.6z" />
                                                    </svg>
                                                </span>
                                                <span>Deactivate</span>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div role="menuitem">
                                        <a class="red" 
                                           href="accounts.php?delete=<?= $account['id'] ?>"
                                           onclick="return confirm('Are you sure you want to delete this account?')"
                                           tabindex="-1"
                                           aria-label="Delete account for <?= htmlspecialchars($account['username'], ENT_QUOTES) ?>">
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
    </div>
    <div class="card-footer bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?= count($accounts) ?> of <?= $total_accounts ?> accounts
            </small>
            <nav aria-label="Accounts pagination">
                <div class="d-flex gap-2">
                    <?php if ($page > 1): ?>
                        <a href="<?= $url ?>&page=<?= $page - 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>" 
                           class="btn btn-sm btn-outline-secondary">Previous</a>
                    <?php endif; ?>
                    <span class="btn btn-sm btn-secondary disabled">
                        Page <?= $page ?> of <?= ceil($total_accounts / $results_per_page) == 0 ? 1 : ceil($total_accounts / $results_per_page) ?>
                    </span>
                    <?php if ($page * $results_per_page < $total_accounts): ?>
                        <a href="<?= $url ?>&page=<?= $page + 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>" 
                           class="btn btn-sm btn-outline-secondary">Next</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>
</div>

<?= template_admin_footer() ?>