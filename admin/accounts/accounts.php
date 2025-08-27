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

<div class="container-fluid">
    <?php if (isset($success_msg)): ?>
        <div class="mb-4" role="region" aria-label="Success Message">
            <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite">
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
                <?= $success_msg ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Top page actions -->
    <div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
        <a href="account.php" class="btn btn-outline-secondary">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>
            Add Account
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-4 px-4 branding-settings-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <span class="header-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                    Account Management
                </h6>
                <span class="text-white" style="font-size: 0.875rem;"><?= $total_accounts ?> total accounts</span>
            </div>
        </div>
        <div class="card-body account-table-body p-0">
            <div class="table-filters-wrapper p-3">
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
                                <i class="bi bi-funnel me-1" aria-hidden="true"></i>
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
            </div>

        <div class="table-responsive account-table-wrapper">
            <table class="table table-hover account-table" role="grid" aria-label="User Accounts">
                <thead class="account-table-thead" role="rowgroup">
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
                                <svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                    <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                </svg>
                                <div class="table-dropdown-items" role="menu" aria-label="Account Actions">
                                    <div role="menuitem">
                                        <a href="documents.php?account_id=<?= $account['id'] ?>" 
                                           class="blue" 
                                           tabindex="-1" 
                                           aria-label="View documents for <?= htmlspecialchars($account['username'], ENT_QUOTES) ?>">
                                            <span class="icon" aria-hidden="true">
                                                <i class="bi bi-file-text" style="font-size: 12px;"></i>
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
                                                <i class="bi bi-pencil" style="font-size: 12px;"></i>
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
                                                    <i class="bi bi-check-circle" style="font-size: 12px;"></i>
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
                                                    <i class="bi bi-x-circle" style="font-size: 12px;"></i>
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
                                                <i class="bi bi-trash" style="font-size: 12px;"></i>
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
    <div class="card-footer account-table-footer bg-light">
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

<style>
/* Branding Card Header Styling */
.branding-settings-header {
  background: var(--brand-primary-color, #3b89b0) !important;
  color: white !important;
  border-bottom: 1px solid var(--brand-primary-color, #3b89b0) !important;
}

.branding-settings-header h6 {
  color: white !important;
  margin: 0 !important;
  font-weight: 600 !important;
  font-size: 1rem !important;
}

.header-icon {
  margin-right: 8px;
  opacity: 0.9;
}

.header-icon i {
  font-size: 1.1rem;
}

/* Accessible button contrast */
.btn-success:focus,
.btn-success:hover {
  background-color: #198754 !important;
  border-color: #198754 !important;
}

/* Table accessibility improvements */
.table th a {
  color: #495057 !important;
  text-decoration: none !important;
}

.table th a:hover {
  color: var(--brand-primary-color, #3b89b0) !important;
  text-decoration: underline !important;
}

/* Badge contrast improvements */
.badge.bg-secondary {
  background-color: #6c757d !important;
  color: white !important;
}

.badge.bg-secondary a {
  color: white !important;
  text-decoration: none !important;
}

.badge.bg-secondary a:hover {
  color: #f8f9fa !important;
}
</style>

<?= template_admin_footer() ?>