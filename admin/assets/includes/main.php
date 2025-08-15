<?php
// Admin Main File - Separate from client-facing unified template system
// This includes core database config and admin-specific functions only

// Always include the config from the project root using PROJECT_ROOT for reliability
if (!defined('PROJECT_ROOT'))
{
    include_once __DIR__ . '/../../../../private/gws-universal-config.php';
}
include_once PROJECT_ROOT . '/private/gws-universal-config.php';

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
$stmt = $pdo->prepare('SELECT COUNT(*) FROM accounts WHERE id = ? AND (role = "Admin" || role= "Editor" || role="Developer")');
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
    'asc' => '<svg width="10" height="10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M350 177.5c3.8-8.8 2-19-4.6-26l-136-144C204.9 2.7 198.6 0 192 0s-12.9 2.7-17.4 7.5l-136 144c-6.6 7-8.4 17.2-4.6 26s12.5 14.5 22 14.5h88l0 192c0 17.7-14.3 32-32 32H32c-17.7 0-32 14.3-32 32v32c0 17.7 14.3 32 32 32l80 0c70.7 0 128-57.3 128-128l0-192h88c9.6 0 18.2-5.7 22-14.5z"/></svg>',
    'desc' => '<svg width="10" height="10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M350 334.5c3.8 8.8 2 19-4.6 26l-136 144c-4.5 4.8-10.8 7.5-17.4 7.5s-12.9-2.7-17.4-7.5l-136-144c-6.6-7-8.4-17.2-4.6-26s12.5-14.5 22-14.5h88l0-192c0-17.7-14.3-32-32-32H32C14.3 96 0 81.7 0 64V32C0 14.3 14.3 0 32 0l80 0c70.7 0 128 57.3 128 128l0 192h88c9.6 0 18.2 5.7 22 14.5z"/></svg>'
];
// Update last seen
$d = date('Y-m-d H:i:s');
$stmt = $pdo->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
$stmt->execute([$d, $_SESSION['id']]);
// Get total number of accounts
$accounts_total = $pdo->query('SELECT COUNT(*) AS total FROM accounts')->fetchColumn();
// Get total number of tickets
$tickets_total = $pdo->query('SELECT COUNT(*) AS total FROM tickets')->fetchColumn();
$clients_total = $pdo->query('SELECT COUNT(*) AS total FROM invoice_clients')->fetchColumn();
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
$invoices_total = $pdo->query('SELECT COUNT(*) AS total FROM invoices')->fetchColumn();
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
// Get the total number of polls (with error handling)
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM polls');
    $polls_total = $stmt->fetchColumn();
} catch (PDOException $e) {
    $polls_total = 0; // Default to 0 if table doesn't exist
}
// Get categories (with error handling)
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM polls_categories');
    $categories_total = $stmt->fetchColumn();
} catch (PDOException $e) {
    $categories_total = 0; // Default to 0 if table doesn't exist
}
// Get the total number of invoices (with error handling)
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM invoices');
    $invoices_total = $stmt->fetchColumn();
} catch (PDOException $e) {
    $invoices_total = 0; // Default to 0 if table doesn't exist
}
// Get the total number of reviews (with error handling)
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM reviews');
    $reviews_total = $stmt->fetchColumn();
} catch (PDOException $e) {
    $reviews_total = 0; // Default to 0 if table doesn't exist
}
// Get the total number of media (with error handling)
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM gallery_media');
    $media_total = $stmt->fetchColumn();
} catch (PDOException $e) {
    $media_total = 0; // Default to 0 if table doesn't exist
}
// Get the total number of collections (with error handling)
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM gallery_collections');
    $collections_total = $stmt->fetchColumn();
} catch (PDOException $e) {
    $collections_total = 0; // Default to 0 if table doesn't exist
}
// Get comment system totals (with error handling)
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM comments');
    $comments_total = $stmt->fetchColumn();
} catch (PDOException $e) {
    $comments_total = 0; // Default to 0 if table doesn't exist
}
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM comment_page_details');
    $comments_pages_total = $stmt->fetchColumn();
} catch (PDOException $e) {
    $comments_pages_total = 0; // Default to 0 if table doesn't exist
}
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM comment_reports');
    $reports_total = $stmt->fetchColumn();
} catch (PDOException $e) {
    $reports_total = 0; // Default to 0 if table doesn't exist
}
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM comment_filters');
    $filters_total = $stmt->fetchColumn();
} catch (PDOException $e) {
    $filters_total = 0; // Default to 0 if table doesn't exist
}

// Get shop system totals (with error handling)
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM shop_transactions');
    $orders_total = $stmt->fetchColumn();
} catch (PDOException $e) {
    $orders_total = 0; // Default to 0 if table doesn't exist
}
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM shop_products');
    $products_total = $stmt->fetchColumn();
} catch (PDOException $e) {
    $products_total = 0; // Default to 0 if table doesn't exist
}
try {
    $stmt = $pdo->query('SELECT COUNT(*) FROM shop_categories');
    $shop_categories_total = $stmt->fetchColumn();
} catch (PDOException $e) {
    $shop_categories_total = 0; // Default to 0 if table doesn't exist
}

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
    // Admin HTML links
    $admin_links = '
        <a href="' . $admin_path . '/index.php"' . ($selected == 'dashboard' ? ' class="selected"' : '') . '>
            <span class="icon">
                <svg width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm320 96c0-26.9-16.5-49.9-40-59.3V88c0-13.3-10.7-24-24-24s-24 10.7-24 24V292.7c-23.5 9.5-40 32.5-40 59.3c0 35.3 28.7 64 64 64s64-28.7 64-64zM144 176a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm-16 80a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zm288 32a32 32 0 1 0 0-64 32 32 0 1 0 0 64zM400 144a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/></svg>            
            </span>
            <span class="txt">Dashboard</span>
        </a>
        <a href="' . $admin_path . '/accounts/account_dash.php"' . ($selected == 'accounts' ? ' class="selected"' : '') . '>
            <span class="icon">
                <svg width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/></svg>
            </span>
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
            <span class="icon">
                <svg width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/></svg>
            </span>
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
            <span class="icon">
                <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M4 20H16V22H4C2.9 22 2 21.1 2 20V7H4M22 4V16C22 17.1 21.1 18 20 18H8C6.9 18 6 17.1 6 16V4C6 2.9 6.9 2 8 2H20C21.1 2 22 2.9 22 4M12 8H10V14H12M15 6H13V14H15M18 11H16V14H18Z" /></svg>
            </span>
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
            <span class="icon">
                <svg width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M256 0L576 0c35.3 0 64 28.7 64 64l0 224c0 35.3-28.7 64-64 64l-320 0c-35.3 0-64-28.7-64-64l0-224c0-35.3 28.7-64 64-64zM476 106.7C471.5 100 464 96 456 96s-15.5 4-20 10.7l-56 84L362.7 169c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l80 0 48 0 144 0c8.9 0 17-4.9 21.2-12.7s3.7-17.3-1.2-24.6l-96-144zM336 96a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM64 128l96 0 0 256 0 32c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-32 160 0 0 64c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 192c0-35.3 28.7-64 64-64zm8 64c-8.8 0-16 7.2-16 16l0 16c0 8.8 7.2 16 16 16l16 0c8.8 0 16-7.2 16-16l0-16c0-8.8-7.2-16-16-16l-16 0zm0 104c-8.8 0-16 7.2-16 16l0 16c0 8.8 7.2 16 16 16l16 0c8.8 0 16-7.2 16-16l0-16c0-8.8-7.2-16-16-16l-16 0zm0 104c-8.8 0-16 7.2-16 16l0 16c0 8.8 7.2 16 16 16l16 0c8.8 0 16-7.2 16-16l0-16c0-8.8-7.2-16-16-16l-16 0zm336 16l0 16c0 8.8 7.2 16 16 16l16 0c8.8 0 16-7.2 16-16l0-16c0-8.8-7.2-16-16-16l-16 0c-8.8 0-16 7.2-16 16z"/></svg>
            </span>
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
         <a href="' . $admin_path . '/review_system/review_dash.php"'                . ($selected == 'reviews' ? ' class="selected"' : '') . ' title="Reviews">
          <span class="icon">
                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17 2H2V17H4V4H17V2M21 22L18.5 20.32L16 22L13.5 20.32L11 22L8.5 20.32L6 22V6H21V22M10 10V12H17V10H10M15 14H10V16H15V14Z" /></svg>
          </span>
            <span class="txt">&nbsp;Reviews</span>
            <span class="note">' . ($reviews_total ? number_format($reviews_total) : 0) . '</span>
        </a>
        
        <div class="sub">
            <a href="' . $admin_path . '/review_system/reviews.php"'                . ($selected == 'reviews' && $selected_child == 'view'   ? ' class="selected"' : '') . '><span class="square"></span>View Reviews</a>
            <a href="' . $admin_path . '/review_system/review.php"'                 . ($selected == 'reviews' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span class="square"></span>Create Review</a>
            <a href="' . $admin_path . '/review_system/reviews_table_transfer.php"' . ($selected == 'reviews' && $selected_child == 'export' ? ' class="selected"' : '') . '><span class="square"></span>Bulk Import/Export</a>
            <a href="' . $admin_path . '/review_system/review_filters.php"'         . ($selected == 'reviews' && $selected_child == 'import' ? ' class="selected"' : '') . '><span class="square"></span>View Filters</a>
            <a href="' . $admin_path . '/review_system/review_filter.php"'          . ($selected == 'reviews' && $selected_child == 'filter' ? ' class="selected"' : '') . '><span class="square"></span>Create Filter</a>
            <a href="' . $admin_path . '/review_system/review_pages.php"'           . ($selected == 'reviews' && $selected_child == 'pages'  ? ' class="selected"' : '') . '><span class="square"></span>Pages</a>
        </div>
        
         <a href="' . $admin_path . '/invoice_system/invoice_dash.php"' . ($selected == 'invoices' ? ' class="selected"' : '') . ' title="Invoices">
          <span class="icon">
                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M17 2H2V17H4V4H17V2M21 22L18.5 20.32L16 22L13.5 20.32L11 22L8.5 20.32L6 22V6H21V22M10 10V12H17V10H10M15 14H10V16H15V14Z" /></svg>
          </span>
            <span class="txt">&nbsp;Invoices</span>
            <span class="note">' . ($invoices_total ? number_format($invoices_total) : 0) . '</span>
        </a>
        
        <div class="sub">
            <a href="' . $admin_path . '/invoice_system/invoices.php"'                  . ($selected == 'invoices' && $selected_child == 'view'            ? ' class="selected"' : '') . '><span class="square"></span>View Invoices</a>
            <a href="' . $admin_path . '/invoice_system/invoice.php"'                   . ($selected == 'invoices' && $selected_child == 'manage'          ? ' class="selected"' : '') . '><span class="square"></span>Create Invoice</a>
            <a href="' . $admin_path . '/invoice_system/invoice_table_transfer.php"'    . ($selected == 'invoices' && $selected_child == 'import'          ? ' class="selected"' : '') . '><span class="square"></span>Bulk Import/Export</a>
            <a href="' . $admin_path . '/invoice_system/invoice_templates.php"'         . ($selected == 'invoices' && $selected_child == 'templates'       ? ' class="selected"' : '') . '><span class="square"></span>Templates</a>
            <a href="' . $admin_path . '/invoice_system/email_templates.php"'           . ($selected == 'invoices' && $selected_child == 'email_templates' ? ' class="selected"' : '') . '><span class="square"></span>Email Templates</a>
            <a href="' . $admin_path . '/invoice_system/clients.php"'                   . ($selected == 'invoices' && $selected_child == 'clients_view'    ? ' class="selected"' : '') . '><span class="square"></span>View Clients</a>
            <a href="' . $admin_path . '/invoice_system/client.php"'                    . ($selected == 'invoices' && $selected_child == 'clients_manage'  ? ' class="selected"' : '') . '><span class="square"></span>Create Client</a>
        </div>
         
        <a href="' . $admin_path . '/blog/blog_dash.php"' . ($selected == 'blog' ? ' class="selected"' : '') . '>
         <span class="icon">
                <i class="fas fa-blog"></i>
         </span>
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

        <a href="' . $admin_path . '/comment_system/comment_dash.php"' . ($selected == 'comments' ? ' class="selected"' : '') . '>
            <span class="icon">
                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M123.6 391.3c12.9-9.4 29.6-11.8 44.6-6.4c26.5 9.6 56.2 15.1 87.8 15.1c124.7 0 208-80.5 208-160s-83.3-160-208-160S48 160.5 48 240c0 32 12.4 62.8 35.7 89.2c8.6 9.7 12.8 22.5 11.8 35.5c-1.4 18.1-5.7 34.7-11.3 49.4c17-7.9 31.1-16.7 39.4-22.7zM21.2 431.9c1.8-2.7 3.5-5.4 5.1-8.1c10-16.6 19.5-38.4 21.4-62.9C17.7 326.8 0 285.1 0 240C0 125.1 114.6 32 256 32s256 93.1 256 208s-114.6 208-256 208c-37.1 0-72.3-6.4-104.1-17.9c-11.9 8.7-31.3 20.6-54.3 30.6c-15.1 6.6-32.3 12.6-50.1 16.1c-.8 .2-1.6 .3-2.4 .5c-4.4 .8-8.7 1.5-13.2 1.9c-.2 0-.5 .1-.7 .1c-5.1 .5-10.2 .8-15.3 .8c-6.5 0-12.3-3.9-14.8-9.9c-2.5-6-1.1-12.8 3.4-17.4c4.1-4.2 7.8-8.7 11.3-13.5c1.7-2.3 3.3-4.6 4.8-6.9c.1-.2 .2-.3 .3-.5z"/></svg>
            </span>
            <span class="txt">&nbsp;Comments</span>
            <span class="note">' . ($comments_total ? number_format($comments_total) : 0) . '</span>
        </a>
        <div class="sub">
            <a href="' . $admin_path . '/comment_system/comments.php"'               . ($selected == 'comments' && $selected_child == 'view'  ? ' class="selected"' : '') . '><span class="square"></span>   View Comments        </a>
            <a href="' . $admin_path . '/comment_system/filters.php"'                . ($selected == 'comments' && $selected_child == 'filters' ? ' class="selected"' : '') . '><span class="square"></span>   View Filters         </a>
            <a href="' . $admin_path . '/comment_system/pages.php"'                  . ($selected == 'comments' && $selected_child == 'pages' ? ' class="selected"' : '') . '><span class="square"></span>   View Pages           </a>
            <a href="' . $admin_path . '/comment_system/reports.php"'                . ($selected == 'comments' && $selected_child == 'reports' ? ' class="selected"' : '') . '><span class="square"></span>   View Reports         </a>
            <a href="' . $admin_path . '/comment_system/comment_table_transfer.php"' . ($selected == 'comments' && $selected_child == 'bulk' ? ' class="selected"' : '') . '><span class="square"></span>   Bulk Import/Export   </a>
            <a href="' . $admin_path . '/comment_system/settings.php"'               . ($selected == 'comments' && $selected_child == 'settings' ? ' class="selected"' : '') . '><span class="square"></span>   Settings             </a>
        </div>

        <a href="' . $admin_path . '/shop_system/shop_dash.php"' . ($selected == 'shop' ? ' class="selected"' : '') . '>
            <span class="icon">
                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/></svg>
            </span>
            <span class="txt">&nbsp;Shop</span>
            <span class="note">' . number_format($orders_total) . '</span>
        </a>
        <div class="sub">
            <a href="' . $admin_path . '/shop_system/shop_dash.php"' . ($selected == 'shop' && $selected_child == 'dashboard' ? ' class="selected"' : '') . '><span class="square"></span>   Dashboard           </a>
            <a href="' . $admin_path . '/shop_system/orders.php"' . ($selected == 'shop' && $selected_child == 'orders' ? ' class="selected"' : '') . '><span class="square"></span>   View Orders         </a>
            <a href="' . $admin_path . '/shop_system/order.php"' . ($selected == 'shop' && $selected_child == 'order_manage' ? ' class="selected"' : '') . '><span class="square"></span>   Create Order        </a>
            <a href="' . $admin_path . '/shop_system/products.php"' . ($selected == 'shop' && $selected_child == 'products' ? ' class="selected"' : '') . '><span class="square"></span>   View Products       </a>
            <a href="' . $admin_path . '/shop_system/product.php"' . ($selected == 'shop' && $selected_child == 'product_manage' ? ' class="selected"' : '') . '><span class="square"></span>   Create Product      </a>
            <a href="' . $admin_path . '/shop_system/categories.php"' . ($selected == 'shop' && $selected_child == 'categories' ? ' class="selected"' : '') . '><span class="square"></span>   View Categories     </a>
            <a href="' . $admin_path . '/shop_system/category.php"' . ($selected == 'shop' && $selected_child == 'category_manage' ? ' class="selected"' : '') . '><span class="square"></span>   Create Category     </a>
            <a href="' . $admin_path . '/settings/shop_settings.php"' . ($selected == 'shop' && $selected_child == 'settings' ? ' class="selected"' : '') . '><span class="square"></span>   Shop Settings       </a>
        </div>

        <a href="' . $public_path . '/client_portal/index.php"' . ($selected == 'portal' ? ' class="selected"' : '') . '>
            <span class="icon">
                <i class="fas fa-blog"></i>
            </span>
            <span class="txt">&nbsp;Client Portal</span>
        </a>
        <a href="' . $admin_path . '/settings/settings_dash.php"' . ($selected == 'settings' ? ' class="selected"' : '') . '>
            <span class="icon"> 
                <svg width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/></svg>
            </span>
            <span class="txt">&nbsp;Settings</span>
        </a>
'; // Remove submenu - all settings now accessed through dashboard

  
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
                        <i class="fas fa-eye" aria-hidden="true"></i>
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
                <i class="fas fa-users" aria-hidden="true"></i>
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

        <!-- Bootstrap 5-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

        <!-- Font Awesome -->
        <link href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" rel="stylesheet"/>
 
        <!--DataTables-->
        <link href="https://cdn.datatables.net/v/bs5/dt-2.1.8/r-3.0.3/datatables.min.css" rel="stylesheet">
 
        <!-- jQuery --> 
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    
        <!-- SummerNote -->
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote-bs5.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.1/dist/summernote-bs5.min.js"></script>
 
        <link href="' . $admin_path . '/assets/css/admin.css?v=' . time() . '" rel="stylesheet" type="text/css">
        
        <!-- Dashboard Specific Styles -->
        <link href="' . $admin_path . '/assets/css/dashboard.css?v=' . time() . '" rel="stylesheet" type="text/css">
        
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