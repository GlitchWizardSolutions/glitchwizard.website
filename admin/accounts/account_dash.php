<?php
/* 
 * Account Dashboard Interface
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: account_dash.php
 * LOCATION: /public_html/admin/accounts/
 * PURPOSE: Primary dashboard for account statistics and recent registrations overview
 * 
 * FILE RELATIONSHIP:
 * This file integrates with:
 * - User authentication system
 * - Account statistics tracker
 * - Avatar management system
 * - Role management system
 * 
 * HOW IT WORKS:
 * 1. Fetches and displays account statistics (new, active, inactive, total)
 * 2. Lists recent account registrations with detailed information
 * 3. Provides quick action capabilities for account management
 * 4. Implements accessible interface elements for all users
 * 
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: YES
 * 
 * FEATURES:
 * - Real-time statistics display
 * - Recent registrations table
 * - Quick action dropdowns
 * - Avatar display integration
 * - ARIA-compliant accessibility
 * - Secure data handling
 * - Responsive stat blocks
 * 
 * Last reviewed: July 22, 2025
 * 
 * CHANGELOG:
 * - July 22, 2025, 10:00 AM: Quality Assurance and Accessibility Testing completed. Page approved for production. (GitHub Copilot)
 * - 2025-07-03: Initial creation with dashboard statistics
 * - 2025-07-04: Updated avatar display and improved UI consistency
 */
include_once '../assets/includes/main.php';

// New accounts registered within the past month with sorting support
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>',
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>'
];
$order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';
$order_by_whitelist = [
    'username' => 'username',
    'email' => 'email',
    'access' => 'access_level',
    'registered' => 'registered'
];
$order_by = isset($_GET['order_by']) && isset($order_by_whitelist[$_GET['order_by']]) ? $_GET['order_by'] : 'registered';
$order_by_sql = $order_by_whitelist[$order_by];

$sql = "SELECT * FROM accounts WHERE registered > date_sub(now(), interval 1 month) ORDER BY $order_by_sql $order";
$accounts = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
// Total accounts
$accounts_total = $pdo->query('SELECT COUNT(*) AS total FROM accounts')->fetchColumn();
// Total accounts that were last active over a month ago
$inactive_accounts = $pdo->query('SELECT COUNT(*) AS total FROM accounts WHERE last_seen < date_sub(now(), interval 1 month)')->fetchColumn();
// Accounts that are active in the last day
$active_accounts = $pdo->query('SELECT * FROM accounts WHERE last_seen > date_sub(now(), interval 1 day) ORDER BY last_seen DESC')->fetchAll(PDO::FETCH_ASSOC);
// Total accounts that are active in the last month
$active_accounts2 = $pdo->query('SELECT COUNT(*) AS total FROM accounts WHERE last_seen > date_sub(now(), interval 1 month)')->fetchColumn();

// Action Items Data - Things requiring attention
$unactivated_accounts = $pdo->query('SELECT COUNT(*) FROM accounts WHERE activation_code = 0')->fetchColumn();
$admin_accounts = $pdo->query('SELECT COUNT(*) FROM accounts WHERE role = "Admin"')->fetchColumn();
$recent_inactive = $pdo->query('SELECT COUNT(*) FROM accounts WHERE last_seen < DATE_SUB(NOW(), INTERVAL 7 DAY) AND last_seen > DATE_SUB(NOW(), INTERVAL 30 DAY)')->fetchColumn();

// Calculate action items total
$total_action_items = $unactivated_accounts + ($admin_accounts > 5 ? 1 : 0) + $recent_inactive;

// Get the directory size
function dirSize($directory)
{
    $size = 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file)
    {
        $size += $file->getSize();
    }
    return $size;
}
?>

<a href="#main-dashboard" class="skip-link"
    style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;background:#fff;color:#007bff;padding:8px 16px;z-index:1000;"
    onfocus="this.style.left='8px';this.style.top='8px';this.style.width='auto';this.style.height='auto';">Skip to main
    dashboard</a>
<?= template_admin_header('Account Dashboard', 'accounts') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-4 px-4 branding-settings-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <span class="header-icon"><i class="bi bi-people-fill" aria-hidden="true"></i></span>
                    Account Dashboard
                </h6>
                <span class="text-white" style="font-size: 0.875rem;">System Overview</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="container-fluid py-3 px-4">

<div class="mb-4">
<!--ACCOUNT SYSTEM OVERVIEW-->
<div class="dashboard-apps">
    <!-- Account Quick Actions Card -->
    <div class="app-card" role="region" aria-labelledby="account-quick-actions-title">
        <div class="app-header events-header" role="banner" aria-labelledby="account-quick-actions-title">
            <h3 id="account-quick-actions-title">Quick Actions</h3>
            <i class="bi bi-lightning" aria-hidden="true"></i>
            <span class="badge" aria-label="Account management actions">Manage</span>
        </div>
        <div class="app-body">
            <div class="quick-actions">
                <a href="account.php" class="quick-action primary">
                    <div class="action-icon">
                        <i class="bi bi-person-plus" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Create Account</h4>
                        <small class="text-muted">Add new user account</small>
                    </div>
                </a>
                <a href="accounts.php" class="quick-action info">
                    <div class="action-icon">
                        <i class="bi bi-list-ul" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>View All Accounts</h4>
                        <small class="text-muted">Manage existing accounts</small>
                    </div>
                </a>
                <a href="roles.php" class="quick-action secondary">
                    <div class="action-icon">
                        <i class="bi bi-person-gear" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Manage Roles</h4>
                        <small class="text-muted">Configure user permissions</small>
                    </div>
                </a>
                <a href="email_templates.php" class="quick-action success">
                    <div class="action-icon">
                        <i class="bi bi-envelope" aria-hidden="true"></i>
                    </div>
                    <div class="action-details">
                        <h4>Email Templates</h4>
                        <small class="text-muted">Manage notification emails</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Account Action Items Card -->
    <div class="app-card" role="region" aria-labelledby="account-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="account-actions-title">
            <h3 id="account-actions-title">Action Items</h3>
            <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= $total_action_items ?> items requiring attention"><?= $total_action_items ?> items</span>
        </div>
        <div class="app-body">
            <?php if ($total_action_items > 0): ?>
                <div class="action-items">
                    <?php if ($unactivated_accounts > 0): ?>
                        <a href="accounts.php?status=unactivated" class="action-item warning">
                            <div class="action-icon">
                                <i class="bi bi-person-exclamation" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Unactivated Accounts</h4>
                                <small class="text-muted">Accounts pending email activation</small>
                            </div>
                            <div class="action-count"><?= $unactivated_accounts ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($admin_accounts > 5): ?>
                        <a href="accounts.php?role=admin" class="action-item info">
                            <div class="action-icon">
                                <i class="bi bi-shield-check" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Review Admin Accounts</h4>
                                <small class="text-muted">Many admin accounts detected</small>
                            </div>
                            <div class="action-count"><?= $admin_accounts ?></div>
                        </a>
                    <?php endif; ?>
                    <?php if ($recent_inactive > 0): ?>
                        <a href="accounts.php?status=recent_inactive" class="action-item danger">
                            <div class="action-icon">
                                <i class="bi bi-person-x" aria-hidden="true"></i>
                            </div>
                            <div class="action-details">
                                <h4>Recently Inactive</h4>
                                <small class="text-muted">Users inactive 7-30 days</small>
                            </div>
                            <div class="action-count"><?= $recent_inactive ?></div>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-action-items">
                    <i class="bi bi-check-circle" aria-hidden="true"></i>
                    <p>All accounts in good standing! No issues detected.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Account Statistics Card -->
    <div class="app-card" role="region" aria-labelledby="account-stats-title">
        <div class="app-header accounts-header" role="banner" aria-labelledby="account-stats-title">
            <h3 id="account-stats-title">Account Statistics</h3>
            <i class="bi bi-people" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= number_format($accounts_total) ?> total accounts"><?= number_format($accounts_total) ?> total</span>
        </div>
        <div class="app-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= number_format(count($accounts)) ?></div>
                    <div class="stat-label">New Accounts</div>
                    <div class="stat-sublabel">Accounts &lt;1 month old</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($active_accounts2) ?></div>
                    <div class="stat-label">Active</div>
                    <div class="stat-sublabel">Total Active &lt;30 days</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($inactive_accounts) ?></div>
                    <div class="stat-label">Inactive</div>
                    <div class="stat-sublabel">Inactive &gt;30 days</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($accounts_total) ?></div>
                    <div class="stat-label">Total Accounts</div>
                    <div class="stat-sublabel">All accounts</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-4">
    <div class="card">
    <h6 class="card-header">New Account Registrations ( &lt; 1 month )</h6>
    <div class="card-body">
        <div class="table">
            <table>
                    <thead>
                        <tr>
                            <th style="text-align: center;">Avatar</th>
                            <th class="text-left">
                                <?php $q = $_GET; $q['order_by'] = 'username'; $q['order'] = ($order_by == 'username' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                <a href="?<?= http_build_query($q) ?>" class="sort-header">Username<?= $order_by == 'username' ? $table_icons[strtolower($order)] : '' ?></a>
                            </th>
                            <th class="text-left">
                                <?php $q = $_GET; $q['order_by'] = 'email'; $q['order'] = ($order_by == 'email' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                <a href="?<?= http_build_query($q) ?>" class="sort-header">Email<?= $order_by == 'email' ? $table_icons[strtolower($order)] : '' ?></a>
                            </th>
                            <th style="text-align: center;">
                                <?php $q = $_GET; $q['order_by'] = 'access'; $q['order'] = ($order_by == 'access' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                <a href="?<?= http_build_query($q) ?>" class="sort-header">Access<?= $order_by == 'access' ? $table_icons[strtolower($order)] : '' ?></a>
                            </th>
                            <th style="text-align: center;">Actions</th>
                        </tr>
                    </thead>
            <tbody role="rowgroup">
                <?php if (!$accounts): ?>
                    <tr role="row">
                        <td colspan="5" class="no-results">There are no newly registered accounts</td>
                    </tr>
                <?php endif; ?>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'exists'): ?>
                    <tr role="row">
                        <td colspan="5" class="error-message-row">Username and/or email exists!</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($accounts as $account): ?>
                    <tr>
                        <td style="text-align: center;">
                            <img src="<?= getUserAvatar($account) ?>"
                                alt="Avatar for <?= htmlspecialchars($account['username'], ENT_QUOTES) ?>"
                                class="avatar-img"
                                style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;" />
                        </td>
                        <td class="text-left"><?= htmlspecialchars($account['username'], ENT_QUOTES) ?></td>
                        <td class="text-left"><?= htmlspecialchars($account['email'], ENT_QUOTES) ?></td>
                        <td style="text-align: center;"><?= htmlspecialchars($account['access_level'] ?? '0', ENT_QUOTES) ?></td>
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
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 384 512" aria-hidden="true">
                                                    <path
                                                        d="M64 464c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16H224v80c0 17.7 14.3 32 32 32h80V448c0 8.8-7.2 16-16 16H64zM64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V154.5c0-17-6.7-33.3-18.7-45.3L274.7 18.7C262.7 6.7 246.5 0 229.5 0H64zm56 256c-13.3 0-24 10.7-24 24s10.7 24 24 24H264c13.3 0 24-10.7 24-24s-10.7-24-24-24H120zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H264c13.3 0 24-10.7 24-24s-10.7-24-24-24H120z" />
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
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 512 512">
                                                    <path
                                                        d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
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
                                                    <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 640 512">
                                                        <path
                                                            d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM625 177L497 305c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L591 143c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
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
                                                    <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 640 512">
                                                        <path
                                                            d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L353.3 251.6C407.9 237 448 187.2 448 128C448 57.3 390.7 0 320 0C250.2 0 193.5 55.8 192 125.2L38.8 5.1zM264.3 304.3C170.5 309.4 96 387.2 96 482.3c0 16.4 13.3 29.7 29.7 29.7H514.3c3.9 0 7.6-.7 11-2.1l-261-205.6z" />
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
                                                <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 448 512">
                                                    <path
                                                        d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z" />
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
        <!-- Recent accounts summary -->
        <div class="small">
            <span>Showing <?= count($accounts) ?> recent account<?= count($accounts) != 1 ? 's' : '' ?></span>
        </div>
    </div>
            </div>
        </div>
    </div>
</div>

<?= template_admin_footer() ?>