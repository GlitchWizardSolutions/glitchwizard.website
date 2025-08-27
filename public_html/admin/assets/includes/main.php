<?php
// Admin Main File - Separate from client-facing unified template system
// This includes core database config and admin-specific functions only

// Always include the config from the project root using PROJECT_ROOT for reliability
// Repaired conditional logic (previous manual edit introduced syntax error)
if (!defined('PROJECT_ROOT')) {
    $root_guess = __DIR__ . '/../../../../private/gws-universal-config.php';
    if (file_exists($root_guess)) {
        include_once $root_guess; // defines PROJECT_ROOT
    }
}

if (defined('PROJECT_ROOT')) {
    $primary_config = PROJECT_ROOT . '/private/gws-universal-config.php';
    if (file_exists($primary_config)) {
        include_once $primary_config;
    } else {
        die('Configuration file missing at expected path: ' . htmlspecialchars($primary_config));
    }
} else {
    die('PROJECT_ROOT could not be determined. Check directory structure.');
}
// Feature flags (module visibility control)
$FEATURE_FLAGS = [];
if (file_exists(PROJECT_ROOT . '/private/feature_flags.php')) {
    $FEATURE_FLAGS = require PROJECT_ROOT . '/private/feature_flags.php';
}
if (!function_exists('featureEnabled')) {
    function featureEnabled($key, $flags) { return !isset($flags[$key]) || $flags[$key] === true; }
}

// Include admin protection
require __DIR__ . '/../../protection.php';

// Admin and public paths for use in admin templates (environment-aware)
$admin_path = WEB_ROOT_URL . '/admin';
$public_path = WEB_ROOT_URL;

// Include shared universal functions
include_once PROJECT_ROOT . '/private/gws-universal-functions.php';

// Connect to the MySQL database using the PDO interface
try
{
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception)
{
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to database: ' . $exception->getMessage());
}

check_loggedin_full($pdo, '../auth.php?tab=login');
//Check if the user is logged-in as Admin.
$stmt = $pdo->prepare('SELECT COUNT(*) FROM accounts WHERE id = ? AND (role = "Admin" OR role = "Editor" OR role = "Developer")');
// Get the account info using the logged-in session ID
$stmt->execute([$_SESSION['id']]);
// If the account exists with the specified ID and is an admin...
if ($stmt->fetchColumn() == 0)
{
    exit('You do not have permission to access this page!');
} else
{
    //grab user data for all allowed roles
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ? AND (role = "Admin" OR role = "Editor" OR role = "Developer")');
    $stmt->execute([$_SESSION['id']]);
    $account_loggedin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Store the user's role in session for easy access
    if ($account_loggedin)
    {
        $_SESSION['admin_role'] = $account_loggedin['role'];
    }
}
// Add/remove roles from the list
$roles_list = ['Admin', 'Member', 'Developer', 'Guest', 'Subscriber', 'Editor', 'Blog_User'];
// Icons for the table headers
$table_icons = [
    // Using Bootstrap Icons chevrons for table sorting
    'asc' => '<i class="bi bi-chevron-up" aria-hidden="true"></i>',
    'desc' => '<i class="bi bi-chevron-down" aria-hidden="true"></i>'
];
// Update last seen
$d = date('Y-m-d H:i:s');
$stmt = $pdo->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
$stmt->execute([$d, $_SESSION['id']]);
// Get total number of accounts
// Generic safe count helper (reduces repeated try/catch blocks)
if (!function_exists('getTableCountSafe')) {
    /**
     * Returns row count for a table, 0 if table doesn't exist or on error.
     * Optional feature flag key prevents unnecessary queries when disabled.
     */
    function getTableCountSafe(PDO $pdo, string $table, ?string $featureKey = null, array $flags = [], string $whereClause = ''): int {
        if ($featureKey && function_exists('featureEnabled') && !featureEnabled($featureKey, $flags)) {
            return 0;
        }
        $sql = 'SELECT COUNT(*) FROM `' . str_replace('`','', $table) . '`';
        if ($whereClause) {
            $sql .= ' ' . $whereClause; // whereClause should be a trusted literal
        }
        try {
            return (int)$pdo->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
}

$accounts_total = getTableCountSafe($pdo, 'accounts');
// Get total number of tickets (tickets are core; no flag presently)
$tickets_total  = getTableCountSafe($pdo, 'tickets');
// Invoice clients (behind invoice_system flag)
$clients_total  = getTableCountSafe($pdo, 'invoice_clients', 'invoice_system', $FEATURE_FLAGS);
// Get online users (active within last 5 minutes, all roles, limited for dropdown display)
function getOnlineUsers($pdo, $current_user_id, $limit = 10)
{
    $stmt = $pdo->prepare('
        SELECT id, username, role, avatar, last_seen 
        FROM accounts 
        WHERE last_seen > DATE_SUB(NOW(), INTERVAL 5 MINUTE) 
        ORDER BY last_seen DESC
        LIMIT :limit
    ');
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// Base invoice count (may be zero if disabled or table absent)
$invoices_total = getTableCountSafe($pdo, 'invoices', 'invoice_system', $FEATURE_FLAGS);
// Get total count of online users (for footer display)
function getOnlineUsersCount($pdo)
{
    $stmt = $pdo->prepare('
        SELECT COUNT(*) 
        FROM accounts 
        WHERE last_seen > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
    ');
    $stmt->execute();
    return $stmt->fetchColumn();
}

$online_users = getOnlineUsers($pdo, $_SESSION['id'], 8); // Limit to 8 users in dropdown
$total_online_users = getOnlineUsersCount($pdo);
// Polling system (always lightweight; could add flag in future)
$polls_total        = getTableCountSafe($pdo, 'polls');
$categories_total   = getTableCountSafe($pdo, 'polls_categories');
// Reviews (behind review_system flag)
$reviews_total      = getTableCountSafe($pdo, 'reviews', 'review_system', $FEATURE_FLAGS);
// Gallery
$media_total        = getTableCountSafe($pdo, 'gallery_media');
$collections_total  = getTableCountSafe($pdo, 'gallery_collections');
// Comment system tables (no feature flag yet)
$comments_total         = getTableCountSafe($pdo, 'comments');
$comments_pages_total   = getTableCountSafe($pdo, 'comment_page_details');
$reports_total          = getTableCountSafe($pdo, 'comment_reports');
$filters_total          = getTableCountSafe($pdo, 'comment_filters');
// Shop system (feature gated)
$orders_total       = getTableCountSafe($pdo, 'shop_transactions', 'shop_system', $FEATURE_FLAGS);
$products_total     = getTableCountSafe($pdo, 'shop_products', 'shop_system', $FEATURE_FLAGS);
$shop_categories_total = getTableCountSafe($pdo, 'shop_categories', 'shop_system', $FEATURE_FLAGS);
// Chat sessions (example with WHERE clause retained)
$chat_sessions_total = getTableCountSafe($pdo, 'chat_sessions', null, [], 'WHERE DATE(created_at) >= CURDATE()');

// Template admin header
function template_admin_header($title, $selected = 'dashboard', $selected_child = '')
{
    // Start with a clean output buffer
    if (ob_get_level()) ob_end_clean();
    ob_start();
    
    // Declare global variables
    global $accounts_total;
    global $reviews_total;
    global $polls_total;
    global $invoices_total;
    global $tickets_total;
    global $media_total;
    global $collections_total;
    global $admin_path;
    global $public_path;
    global $account_loggedin;
    global $online_users;
    global $total_online_users;
    global $clients_total;
    global $comments_total;
    global $comments_pages_total;
    global $reports_total;
    global $filters_total;
    global $orders_total;
    global $products_total;
    global $shop_categories_total;
    global $chat_sessions_total;
    global $FEATURE_FLAGS; // feature flag array for conditional nav counts
    // Admin HTML links
    $admin_links = '
        <a href="' . $admin_path . '/index.php"' . ($selected == 'dashboard' ? ' class="selected"' : '') . '>
            <span class="icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
            <span class="txt">Dashboard</span>
        </a>
        <a href="' . $admin_path . '/accounts/account_dash.php"' . ($selected == 'accounts' ? ' class="selected"' : '') . '>
            <span class="icon"><i class="bi bi-people" aria-hidden="true"></i></span>
            <span class="txt">Accounts</span>
            <span class="note">' . number_format($accounts_total) . '</span>
        </a>
        <div class="sub">
            <a href="' . $admin_path . '/accounts/accounts.php"' . ($selected == 'accounts' && $selected_child == 'view' ? ' class="selected"' : '') . '><span class="square"></span>   View Accounts       </a>
            <a href="' . $admin_path . '/accounts/account.php"' . ($selected == 'accounts' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span class="square"></span>   Create/Edit Account      </a>
            <a href="' . $admin_path . '/accounts/accounts_table_transfer.php"' . ($selected == 'accounts' && $selected_child == 'transfer' ? ' class="selected"' : '') . '><span class="square"></span>   Import/Export Accounts     </a>
            <a href="' . $admin_path . '/accounts/roles.php"' . ($selected == 'accounts' && $selected_child == 'roles' ? ' class="selected"' : '') . '><span class="square"></span>   Account Roles       </a>
            <a href="' . $admin_path . '/accounts/email_templates.php"' . ($selected == 'accounts' && $selected_child == 'templates' ? ' class="selected"' : '') . '><span class="square"></span>   Email Templates     </a>
        </div>
        <a href="' . $admin_path . '/ticket_system/ticket_dash.php"' . ($selected == 'tickets' ? ' class="selected"' : '') . '>
            <span class="icon"><i class="bi bi-ticket-detailed" aria-hidden="true"></i></span>
            <span class="txt">Tickets</span>
            <span class="note">' . number_format($tickets_total) . '</span>
        </a>
        <div class="sub">
            <a href="' . $admin_path . '/ticket_system/tickets.php"' . ($selected == 'tickets' && $selected_child == 'view' ? ' class="selected"' : '') . '><span class="square"></span>   View Tickets       </a>
            <a href="' . $admin_path . '/ticket_system/comments.php"' . ($selected == 'tickets' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span class="square"></span>   View Comments      </a>
            <a href="' . $admin_path . '/ticket_system/tickets_table_transfer.php"' . ($selected == 'tickets' && $selected_child == 'transfer' ? ' class="selected"' : '') . '><span class="square"></span>   Import/Export Tickets    </a>
            <a href="' . $admin_path . '/ticket_system/categories.php"' . ($selected == 'tickets' && $selected_child == 'category' ? ' class="selected"' : '') . '><span class="square"></span>   Ticket Categories       
            </a>
        </div>
 
        <a href="' . $admin_path . '/polling_system/poll_dash.php"' . ($selected == 'polls' ? ' class="selected"' : '') . ' title="Polls">
            <span class="icon"><i class="bi bi-bar-chart-line" aria-hidden="true"></i></span>
            <span class="txt">Polls</span>
            <span class="note">' . ($polls_total ? number_format($polls_total) : 0) . '</span>
        </a>
        <div class="sub">
            <a href="' . $admin_path . '/polling_system/polls.php"'                . ($selected == 'polls' && $selected_child == 'view'       ? ' class="selected"' : '') . '><span class="square"></span>View Polls</a>
            <a href="' . $admin_path . '/polling_system/poll.php"'                 . ($selected == 'polls' && $selected_child == 'manage'     ? ' class="selected"' : '') . '><span class="square"></span>Create Poll</a>
            <a href="' . $admin_path . '/polling_system/poll_categories.php"'      . ($selected == 'polls' && $selected_child == 'categories' ? ' class="selected"' : '') . '><span class="square"></span>Categories</a>
            <a href="' . $admin_path . '/polling_system/polls_table_transfer.php"' . ($selected == 'polls' && $selected_child == 'bulk'       ? ' class="selected"' : '') . '><span class="square"></span>Bulk Import/Export</a>
        </div>
    <a href="' . $admin_path . '/gallery_system/gallery_dash.php"' . ($selected == 'gallery' ? ' class="selected"' : '') . ' title="Gallery">
            <span class="icon"><i class="bi bi-images" aria-hidden="true"></i></span>
            <span class="txt">&nbsp;Gallery</span>
            <span class="note">' . ($media_total ? number_format($media_total) : 0) . '</span>
        </a>
        <div class="sub">
            <a href="' . $admin_path . '/gallery_system/allmedia.php"'               . ($selected == 'gallery' && $selected_child == 'media_view' ? ' class="selected"' : '') . '><span class="square"></span>View Media</a>
            <a href="' . $admin_path . '/gallery_system/media.php"'                  . ($selected == 'gallery' && $selected_child == 'media_manage' ? ' class="selected"' : '') . '><span class="square"></span>Create Media</a>
            <a href="' . $admin_path . '/gallery_system/collections.php"'            . ($selected == 'gallery' && $selected_child == 'collections_view' ? ' class="selected"' : '') . '><span class="square"></span>View Collections</a>
            <a href="' . $admin_path . '/gallery_system/collection.php"'             . ($selected == 'gallery' && $selected_child == 'collections_manage' ? ' class="selected"' : '') . '><span class="square"></span>Create Collection</a>
            <a href="' . $admin_path . '/gallery_system/likes.php"'                  . ($selected == 'gallery' && $selected_child == 'likes' ? ' class="selected"' : '') . '><span class="square"></span>View Likes</a>
            <a href="' . $admin_path . '/gallery_system/gallery_table_transfer.php"' . ($selected == 'gallery' && $selected_child == 'media_export' ? ' class="selected"' : '') . '><span class="square"></span>Bulk Import/Export</a>
            <a href="' . $admin_path . '/gallery_system/settings.php"'               . ($selected == 'gallery' && $selected_child == 'settings' ? ' class="selected"' : '') . '><span class="square"></span>Gallery Settings</a>
        </div>
    ' . (featureEnabled('review_system', $FEATURE_FLAGS) ? ' <a href="' . $admin_path . '/review_system/review_dash.php"'                . ($selected == 'reviews' ? ' class="selected"' : '') . ' title="Reviews">' : '') . '
                    <span class="icon"><i class="bi bi-chat-square-quote" aria-hidden="true"></i></span>
                        <span class="txt">&nbsp;Reviews</span>
                        <span class="note">' . ($reviews_total ? number_format($reviews_total) : 0) . '</span>
                </a>
        
        ' . (featureEnabled('review_system', $FEATURE_FLAGS) ? '<div class="sub">
            <a href="' . $admin_path . '/review_system/reviews.php"'                . ($selected == 'reviews' && $selected_child == 'view'     ? ' class="selected"' : '') . '><span class="square"></span>View Reviews</a>
            <a href="' . $admin_path . '/review_system/review.php"'                 . ($selected == 'reviews' && $selected_child == 'manage'   ? ' class="selected"' : '') . '><span class="square"></span>Create Review</a>
            <a href="' . $admin_path . '/review_system/reviews_table_transfer.php"' . ($selected == 'reviews' && $selected_child == 'transfer' ? ' class="selected"' : '') . '><span class="square"></span>Bulk Import/Export</a>
            <a href="' . $admin_path . '/review_system/review_filters.php"'         . ($selected == 'reviews' && $selected_child == 'filters'  ? ' class="selected"' : '') . '><span class="square"></span>View Filters</a>
            <a href="' . $admin_path . '/review_system/review_filter.php"'          . ($selected == 'reviews' && $selected_child == 'filter'   ? ' class="selected"' : '') . '><span class="square"></span>Create Filter</a>
            <a href="' . $admin_path . '/review_system/review_pages.php"'           . ($selected == 'reviews' && $selected_child == 'pages'    ? ' class="selected"' : '') . '><span class="square"></span>Pages</a>
            <a href="' . $admin_path . '/review_system/settings.php"'               . ($selected == 'reviews' && $selected_child == 'settings' ? ' class="selected"' : '') . '><span class="square"></span>Review Settings</a>
        </div>' : '') . '
        
            ' . (featureEnabled('invoice_system', $FEATURE_FLAGS) ? '<a href="' . $admin_path . '/invoice_system/invoice_dash.php"' . ($selected == 'invoices' ? ' class="selected"' : '') . ' title="Invoices">' : '') . '
                    <span class="icon"><i class="bi bi-receipt" aria-hidden="true"></i></span>
                        <span class="txt">&nbsp;Invoices</span>
                        <span class="note">' . ($invoices_total ? number_format($invoices_total) : 0) . '</span>
                </a>
        
    ' . (featureEnabled('invoice_system', $FEATURE_FLAGS) ? '<div class="sub">' : '') . '
            <a href="' . $admin_path . '/invoice_system/invoices.php"'                  . ($selected == 'invoices' && $selected_child == 'view'            ? ' class="selected"' : '') . '><span class="square"></span>View Invoices</a>
            <a href="' . $admin_path . '/invoice_system/invoice.php"'                   . ($selected == 'invoices' && $selected_child == 'manage'          ? ' class="selected"' : '') . '><span class="square"></span>Create Invoice</a>
            <a href="' . $admin_path . '/invoice_system/invoice_table_transfer.php"'    . ($selected == 'invoices' && $selected_child == 'transfer'        ? ' class="selected"' : '') . '><span class="square"></span>Bulk Import/Export</a>
            <a href="' . $admin_path . '/invoice_system/invoice_templates.php"'         . ($selected == 'invoices' && $selected_child == 'templates'       ? ' class="selected"' : '') . '><span class="square"></span>Templates</a>
            <a href="' . $admin_path . '/invoice_system/email_templates.php"'           . ($selected == 'invoices' && $selected_child == 'email_templates' ? ' class="selected"' : '') . '><span class="square"></span>Email Templates</a>
            <a href="' . $admin_path . '/invoice_system/clients.php"'                   . ($selected == 'invoices' && $selected_child == 'clients_view'    ? ' class="selected"' : '') . '><span class="square"></span>View Clients</a>
            <a href="' . $admin_path . '/invoice_system/client.php"'                    . ($selected == 'invoices' && $selected_child == 'clients_manage'  ? ' class="selected"' : '') . '><span class="square"></span>Create Client</a>
            <a href="' . $admin_path . '/invoice_system/settings.php"'                  . ($selected == 'invoices' && $selected_child == 'settings'        ? ' class="selected"' : '') . '><span class="square"></span>Invoice Settings</a>
    ' . (featureEnabled('invoice_system', $FEATURE_FLAGS) ? '</div>' : '') . '
 
  
        <a href="' . $admin_path . '/blog/blog_dash.php"' . ($selected == 'blog' ? ' class="selected"' : '') . '>
         <span class="icon"><i class="bi bi-journal-text" aria-hidden="true"></i></span>
            <span class="txt">&nbsp;Blogs</span>
        </a>
        <div class="sub">
            <a href="' . $admin_path . '/blog/posts.php"' . ($selected == 'blog' && $selected_child == 'posts' ? ' class="selected"' : '') . '><span class="square"></span>   Blog Posts           </a>
            <a href="' . $admin_path . '/blog/blog-messages.php"' . ($selected == 'blog' && $selected_child == 'messages' ? ' class="selected"' : '') . '><span class="square"></span>   Blog Messages        </a>
            <a href="' . $admin_path . '/blog/files.php"' . ($selected == 'blog' && $selected_child == 'files' ? ' class="selected"' : '') . '><span class="square"></span>   Blog Files           </a>
            <a href="' . $admin_path . '/blog/comments.php"' . ($selected == 'blog' && $selected_child == 'comments' ? ' class="selected"' : '') . '><span class="square"></span>   Blog Comments        </a>
            <a href="' . $admin_path . '/blog/categories.php"' . ($selected == 'blog' && $selected_child == 'categories' ? ' class="selected"' : '') . '><span class="square"></span>   Blog Categories      </a>
            <a href="' . $admin_path . '/blog/albums.php"' . ($selected == 'blog' && $selected_child == 'albums' ? ' class="selected"' : '') . '><span class="square"></span>   Blog Albums          </a>
            <a href="' . $admin_path . '/blog/gallery.php"' . ($selected == 'blog' && $selected_child == 'gallery' ? ' class="selected"' : '') . '><span class="square"></span>   Blog Gallery         </a>
            <a href="' . $admin_path . '/blog/widgets.php"' . ($selected == 'blog' && $selected_child == 'widgets' ? ' class="selected"' : '') . '><span class="square"></span>   Blog Widgets         </a>
            <a href="' . $admin_path . '/blog/pages.php"' . ($selected == 'blog' && $selected_child == 'pages' ? ' class="selected"' : '') . '><span class="square"></span>   Blog Pages           </a>
            <a href="' . $admin_path . '/blog/newsletter.php"' . ($selected == 'blog' && $selected_child == 'newsletter' ? ' class="selected"' : '') . '><span class="square"></span>   Blog Newsletter      </a>
         </div>
    ' . (featureEnabled('shop_system', $FEATURE_FLAGS) ? '<a href="' . $admin_path . '/shop_system/shop_dash.php"' . ($selected == 'shop' ? ' class="selected"' : '') . '>' : '') . '
            <span class="icon"><i class="bi bi-bag" aria-hidden="true"></i></span>
            <span class="txt">&nbsp;Shop</span>
            <span class="note">' . number_format($orders_total) . '</span>
        </a>
    ' . (featureEnabled('shop_system', $FEATURE_FLAGS) ? '<div class="sub">' : '') . '
            <a href="' . $admin_path . '/shop_system/shop_dash.php"' . ($selected == 'shop' && $selected_child == 'dashboard' ? ' class="selected"' : '') . '><span class="square"></span>   Dashboard           </a>
            <a href="' . $admin_path . '/shop_system/orders.php"' . ($selected == 'shop' && $selected_child == 'orders' ? ' class="selected"' : '') . '><span class="square"></span>   View Orders         </a>
            <a href="' . $admin_path . '/shop_system/order.php"' . ($selected == 'shop' && $selected_child == 'order_manage' ? ' class="selected"' : '') . '><span class="square"></span>   Create Order        </a>
            <a href="' . $admin_path . '/shop_system/products.php"' . ($selected == 'shop' && $selected_child == 'products' ? ' class="selected"' : '') . '><span class="square"></span>   View Products       </a>
            <a href="' . $admin_path . '/shop_system/product.php"' . ($selected == 'shop' && $selected_child == 'product_manage' ? ' class="selected"' : '') . '><span class="square"></span>   Create Product      </a>
            <a href="' . $admin_path . '/shop_system/categories.php"' . ($selected == 'shop' && $selected_child == 'categories' ? ' class="selected"' : '') . '><span class="square"></span>   View Categories     </a>
            <a href="' . $admin_path . '/shop_system/category.php"' . ($selected == 'shop' && $selected_child == 'category_manage' ? ' class="selected"' : '') . '><span class="square"></span>   Create Category     </a>
            <a href="' . $admin_path . '/settings/shop_settings.php"' . ($selected == 'shop' && $selected_child == 'settings' ? ' class="selected"' : '') . '><span class="square"></span>   Shop Settings       </a>
    ' . (featureEnabled('shop_system', $FEATURE_FLAGS) ? '</div>' : '') . '


        <a href="' . $public_path . '/client_portal/index.php"' . ($selected == 'portal' ? ' class="selected"' : '') . '>
            <span class="icon"><i class="bi bi-columns-gap" aria-hidden="true"></i></span>
            <span class="txt">&nbsp;Client Portal</span>
        </a>
        <a href="' . $admin_path . '/help/help_admin.php"' . ($selected == 'help' ? ' class="selected"' : '') . '>
            <span class="icon"><i class="bi bi-question-circle" aria-hidden="true"></i></span>
            <span class="txt">&nbsp;Help</span>
        </a>
        <div class="sub">
            <a href="' . $admin_path . '/help/help_admin.php"' . ($selected == 'help' && $selected_child == 'admin' ? ' class="selected"' : '') . '><span class="square"></span>   Admin Guide</a>
            <a href="' . $admin_path . '/help/help_editor.php"' . ($selected == 'help' && $selected_child == 'editor' ? ' class="selected"' : '') . '><span class="square"></span>   Editor Guide</a>
            ' . (($_SESSION['admin_role'] ?? '') === 'Developer' ? '<a href="' . $admin_path . '/help/help_developer.php"' . ($selected == 'help' && $selected_child == 'developer' ? ' class="selected"' : '') . '><span class="square"></span>   Developer SOP</a>' : '') . '
        </div>
        <a href="' . $admin_path . '/settings/settings_dash.php"' . ($selected == 'settings' ? ' class="selected"' : '') . '>
            <span class="icon"><i class="bi bi-gear" aria-hidden="true"></i></span>
            <span class="txt">&nbsp;Settings</span>
        </a>
        <div class="sub">
            <a href="' . $admin_path . '/settings/database_settings.php"' . ($selected == 'settings' && $selected_child == 'database' ? ' class="selected"' : '') . '><span class="square"></span>   Database Settings   </a>
            <a href="' . $admin_path . '/settings/settings_migration.php"' . ($selected == 'settings' && $selected_child == 'migration' ? ' class="selected"' : '') . '><span class="square"></span>   Settings Migration   </a>
            <a href="' . $admin_path . '/settings/settings_dash.php"' . ($selected == 'settings' && $selected_child == 'dashboard' ? ' class="selected"' : '') . '><span class="square"></span>   Settings Dashboard   </a>
            <a href="' . $admin_path . '/settings/branding_settings.php"' . ($selected == 'settings' && $selected_child == 'branding' ? ' class="selected"' : '') . '><span class="square"></span>   Branding Settings   </a>
            <a href="' . $admin_path . '/settings/system_settings.php"' . ($selected == 'settings' && $selected_child == 'system' ? ' class="selected"' : '') . '><span class="square"></span>   System Settings   </a>
            <a href="' . $admin_path . '/settings/blog_settings.php"' . ($selected == 'settings' && $selected_child == 'blog' ? ' class="selected"' : '') . '><span class="square"></span>   Blog Settings   </a>
            <a href="' . $admin_path . '/settings/content_settings.php"' . ($selected == 'settings' && $selected_child == 'content' ? ' class="selected"' : '') . '><span class="square"></span>   Content Settings   </a>
            <a href="' . $admin_path . '/settings/seo_settings.php"' . ($selected == 'settings' && $selected_child == 'seo' ? ' class="selected"' : '') . '><span class="square"></span>   SEO Settings   </a>
            <a href="' . $admin_path . '/settings/account_settings.php"' . ($selected == 'settings' && $selected_child == 'accounts' ? ' class="selected"' : '') . '><span class="square"></span>   Account Settings   </a>
            <a href="' . $admin_path . '/settings/dev_settings.php"' . ($selected == 'settings' && $selected_child == 'developer' ? ' class="selected"' : '') . '><span class="square"></span>   Developer Settings   </a>
        </div>

    '; // End of admin navigation links

    // Profile image using proper avatar system
    $avatar_url = '';
    if ($account_loggedin)
    {
        $avatar_url = getUserAvatar($account_loggedin);
    } else
    {
        // Fallback if account data is missing
        $fallback_account = array('avatar' => '', 'role' => (isset($_SESSION['admin_role']) ? $_SESSION['admin_role'] : 'Member'));
        $avatar_url = getUserAvatar($fallback_account);
    }

    $profile_img = '<div class="profile-img">'
        . '<img src="' . $avatar_url . '" alt="' . htmlspecialchars($_SESSION['name'], ENT_QUOTES) . ' avatar" />'
        . '</div>';

    // Online users indicator (only show if there are other online users)
    $online_users_indicator = '';
    if (!empty($online_users))
    {
        $displayed_count = count($online_users);
        $online_users_html = '';

        foreach ($online_users as $user)
        {
            $user_avatar = getUserAvatar($user);
            $time_ago = time_elapsed_string($user['last_seen'], false);
            $is_current_user = ($user['id'] == $_SESSION['id']);
            $current_user_class = $is_current_user ? ' current-user' : '';

            $online_users_html .= '
                <div class="online-user-item' . $current_user_class . '" role="listitem" tabindex="0">
                    <span class="online-user-name">' . htmlspecialchars($user['username'], ENT_QUOTES) . '</span>
                    <span class="online-user-role">' . htmlspecialchars($user['role'], ENT_QUOTES) . '</span>
                    <span class="online-user-time">' . $time_ago . '</span>
                </div>';
        }

        // Footer with "View Online" button (show if there are more users than displayed)
        $footer_html = '';
        if ($total_online_users > $displayed_count)
        {
            $footer_html = '
                <div class="online-users-footer">
                    <a href="' . $admin_path . '/accounts/accounts.php?status=Online" class="view-online-btn">
                        <i class="bi bi-eye" aria-hidden="true"></i>
                        View All Online Users (' . $total_online_users . ')
                    </a>
                </div>';
        }

        $online_users_indicator = '
        <div class="dropdown online-users-dropdown">
            <div class="online-users-indicator" 
                 title="' . $total_online_users . ' user' . ($total_online_users > 1 ? 's' : '') . ' online"
                 role="button" 
                 aria-label="View ' . $total_online_users . ' online users"
                 aria-haspopup="true"
                 aria-expanded="false"
                 tabindex="0">
                <i class="bi bi-bell" aria-hidden="true"></i>
                <span class="online-count" aria-label="' . $total_online_users . ' users">' . $total_online_users . '</span>
                <i class="online-pulse" aria-hidden="true"></i>
            </div>
            <div class="list online-users-list" role="list" aria-label="Online users list">
                <div class="online-users-header">
                    <div class="header-title">Online Users (' . $displayed_count . ($total_online_users > $displayed_count ? ' of ' . $total_online_users : '') . ')</div>
                </div>
                ' . $online_users_html . '
                ' . $footer_html . '
            </div>
        </div>';
    }

    // Indenting the below code may cause an error
    echo '<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,minimum-scale=1">
        <title>' . $title . '</title>
        <meta name="author" content="" />
        <link rel="icon" href="' . getBrandingAsset(FAVICON, $admin_path . '/assets/img/favicon.png') . '" type="image/x-icon" />
        <link rel="shortcut icon" href="' . getBrandingAsset(FAVICON, $admin_path . '/assets/img/favicon.png') . '" type="image/x-icon" />

        <!-- Google Fonts for improved accessibility and readability -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

        <!-- Bootstrap 5-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Font Awesome removed: all icons migrated to Bootstrap Icons or inline SVG -->
        
       
        <!--DataTables-->
        <link href="https://cdn.datatables.net/v/bs5/dt-2.1.8/r-3.0.3/datatables.min.css" rel="stylesheet">
 
        <!-- jQuery --> 
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
        <!-- SummerNote -->
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote-bs5.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote-bs5.min.js"></script>
 
        <link href="' . $admin_path . '/assets/css/admin.css?v=' . time() . '" rel="stylesheet" type="text/css">
        
        <!-- Table Styles - Must load AFTER admin.css to override -->
        <link href="' . $admin_path . '/assets/css/table-styles.css?v=' . time() . '" rel="stylesheet" type="text/css">
        
        <!-- Dashboard Specific Styles -->
        <link href="' . $admin_path . '/assets/css/dashboard.css?v=' . time() . '" rel="stylesheet" type="text/css">
        
        <!-- Admin Brand Enhancement -->
        <link href="' . $admin_path . '/assets/css/admin-branding.css?v=' . time() . '" rel="stylesheet" type="text/css">
        
        <!-- Brand Customization -->
        <style>' . getBrandingCSS() . '</style>
        </head>
    <body class="admin">
        <aside>
            <h1>
                <span class="icon admin-logo">
                    <img src="' . getBrandingAsset(ADMIN_LOGO) . '" alt="' . BUSINESS_NAME . ' Logo" 
                         onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';" />
                    <span class="fallback-logo" style="display: none; background: var(--brand-gradient, ' . BRAND_GRADIENT . '); color: white; width: 40px; height: 40px; border-radius: 8px; align-items: center; justify-content: center; font-weight: bold; font-size: 18px;">' . strtoupper(substr(BUSINESS_SHORT_NAME, 0, 1)) . '</span>
                </span>
                <span class="title">' . getAdminDisplayName() . '</span>
            </h1>
            ' . $admin_links . '
            <div class="footer">
                <a href="https://GlitchwizardSolutions.com">GlitchWizard Solutions Admin Center</a>
                Version 3.0.1
            </div>
        </aside>
        <main class="responsive-width-100">
            <header>
                <button class="responsive-toggle" type="button" aria-label="Toggle sidebar navigation"></button>
                <div class="space-between"></div>
                ' . $online_users_indicator . '
                <div class="dropdown right">
                    ' . $profile_img . '
                    <div class="list">
                        <a href="' . $admin_path . '/accounts/account.php?id=' . $_SESSION['id'] . '">Edit Profile</a>
                        <a href="' . $public_path . '/logout.php">Logout</a>
                    </div>
                </div>
            </header>';
}
// Template admin footer
function template_admin_footer($js_script = '')
{
    // DO NOT INDENT THE BELOW CODE
    global $admin_path;
    
    // Check if we're in specific systems that need additional resources
    $is_invoice_system = strpos($_SERVER['REQUEST_URI'], '/invoice_system/') !== false;
    $is_comment_system = strpos($_SERVER['REQUEST_URI'], '/comment_system/') !== false;
    $is_shop_system = strpos($_SERVER['REQUEST_URI'], '/shop_system/') !== false;
    
    echo '  </main>
        <script src="' . $admin_path . '/accounts/admin.js"></script>';
    
    // Include system-specific JavaScript and CSS
    if ($is_invoice_system) {
        echo '
        <link href="' . $admin_path . '/invoice_system/invoice-specific.css?v=' . time() . '" rel="stylesheet" type="text/css">
        <script src="' . $admin_path . '/invoice_system/invoice-specific.js"></script>';
    }
    
    if ($is_comment_system) {
        echo '
        <link href="' . $admin_path . '/comment_system/comment-specific.css?v=' . time() . '" rel="stylesheet" type="text/css">
        <script src="' . $admin_path . '/comment_system/comment-specific.js"></script>';
    }
    
    if ($is_shop_system) {
        echo '
        <link href="' . $admin_path . '/shop_system/shop-admin-specific.css?v=' . time() . '" rel="stylesheet" type="text/css">
        <script src="' . $admin_path . '/shop_system/admin.js"></script>';
    }
    
    echo '
        <script src="https://cdn.datatables.net/v/bs5/dt-2.1.8/r-3.0.3/datatables.min.js"></script>
        <script src="' . $admin_path . '/assets/js/accessibility-fix.js"></script>
        <script src="' . $admin_path . '/assets/js/table-actions.js"></script>
        <script src="' . $admin_path . '/assets/js/admin-shared.js"></script>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const triggerTabList = [].slice.call(document.querySelectorAll(\'[data-bs-toggle="tab"]\'));
            triggerTabList.forEach(function(triggerEl) {
                new bootstrap.Tab(triggerEl);
            });
            // Show initial active tab if one exists
            const activeTab = document.querySelector(\'.nav-link.active[data-bs-toggle="tab"]\');
            if (activeTab) {
                new bootstrap.Tab(activeTab).show();
            }';
    
    // Include countries data if we're in the invoice system
    if ($is_invoice_system) {
        echo '
            // Countries data for invoice system
            const countries = ' . json_encode(get_countries()) . ';';
    }
    
    echo '
        });
        </script>
        ' . ($js_script ? '<script>' . $js_script . '</script>' : '') . '
    </body>
</html>';
}
// The following function will be used to assign a unique icon color to our users
function color_from_string($string)
{
    // The list of hex colors
    $colors = ['#34568B', '#FF6F61', '#6B5B95', '#88B04B', '#F7CAC9', '#92A8D1', '#955251', '#B565A7', '#009B77', '#DD4124', '#D65076', '#45B8AC', '#EFC050', '#5B5EA6', '#9B2335', '#DFCFBE', '#BC243C', '#C3447A', '#363945', '#939597', '#E0B589', '#926AA6', '#0072B5', '#E9897E', '#B55A30', '#4B5335', '#798EA4', '#00758F', '#FA7A35', '#6B5876', '#B89B72', '#282D3C', '#C48A69', '#A2242F', '#006B54', '#6A2E2A', '#6C244C', '#755139', '#615550', '#5A3E36', '#264E36', '#577284', '#6B5B95', '#944743', '#00A591', '#6C4F3D', '#BD3D3A', '#7F4145', '#485167', '#5A7247', '#D2691E', '#F7786B', '#91A8D0', '#4C6A92', '#838487', '#AD5D5D', '#006E51', '#9E4624'];
    // Find color based on the string
    $colorIndex = hexdec(substr(sha1($string), 0, 10)) % count($colors);
    // Return the hex color
    return $colors[$colorIndex];
}
// Convert date to elapsed string function
function time_elapsed_string($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $w = floor($diff->d / 7);
    $diff->d -= $w * 7;
    $string = ['y' => 'year', 'm' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'i' => 'minute', 's' => 'second'];
    foreach ($string as $k => &$v)
    {
        if ($k == 'w' && $w)
        {
            $v = $w . ' week' . ($w > 1 ? 's' : '');
        } else if (isset($diff->$k) && $diff->$k)
        {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else
        {
            unset($string[$k]);
        }
    }
    if (!$full)
        $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
// Remove param from URL function
function remove_url_param($url, $param)
{
    $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*$/', '', $url);
    $url = preg_replace('/(&|\?)' . preg_quote($param) . '=[^&]*&/', '$1', $url);
    return $url;
}
// Get country list function (used by invoice system)
function get_countries() {
    return ["Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe"];
}
?>