<?php
// Start the session
session_start();

// Include necessary files
require_once '../../private/gws-universal-config.php';
require_once '../../private/gws-universal-functions.php';

// Initialize admin template parameters
$selected = 'events';
$selected_child = 'manage';
$title = 'Manage Events';

// Check if user is logged in and has permission
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Check admin role permissions
if (!in_array($_SESSION['role'], ['Admin', 'Super Admin', 'Manager', 'Editor'])) {
    header('Location: ../index.php');
    exit;
}

// Database connection
try {
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([$_SESSION['id']]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$account) {
        header('Location: ../login.php');
        exit;
    }
} catch(PDOException $e) {
    error_log("Events System Error: " . $e->getMessage());
    $error_message = "Database connection error.";
}

// Handle event actions
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'delete':
                    $event_id = (int)$_POST['event_id'];
                    
                    // Delete event registrations first
                    $stmt = $pdo->prepare("DELETE FROM event_registrations WHERE event_id = ?");
                    $stmt->execute([$event_id]);
                    
                    // Delete event comments
                    $stmt = $pdo->prepare("DELETE FROM event_comments WHERE event_id = ?");
                    $stmt->execute([$event_id]);
                    
                    // Delete event files
                    $stmt = $pdo->prepare("DELETE FROM event_files WHERE event_id = ?");
                    $stmt->execute([$event_id]);
                    
                    // Delete the event
                    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
                    $stmt->execute([$event_id]);
                    
                    $success_message = "Event deleted successfully.";
                    break;
                    
                case 'bulk_action':
                    $bulk_action = $_POST['bulk_action'];
                    $event_ids = $_POST['event_ids'] ?? [];
                    
                    if (!empty($event_ids)) {
                        $placeholders = str_repeat('?,', count($event_ids) - 1) . '?';
                        
                        switch ($bulk_action) {
                            case 'publish':
                                $stmt = $pdo->prepare("UPDATE events SET status = 'published' WHERE id IN ($placeholders)");
                                $stmt->execute($event_ids);
                                $success_message = count($event_ids) . " events published successfully.";
                                break;
                                
                            case 'draft':
                                $stmt = $pdo->prepare("UPDATE events SET status = 'draft' WHERE id IN ($placeholders)");
                                $stmt->execute($event_ids);
                                $success_message = count($event_ids) . " events moved to draft.";
                                break;
                                
                            case 'cancel':
                                $stmt = $pdo->prepare("UPDATE events SET status = 'cancelled' WHERE id IN ($placeholders)");
                                $stmt->execute($event_ids);
                                $success_message = count($event_ids) . " events cancelled.";
                                break;
                                
                            case 'delete':
                                // Delete related data first
                                $stmt = $pdo->prepare("DELETE FROM event_registrations WHERE event_id IN ($placeholders)");
                                $stmt->execute($event_ids);
                                
                                $stmt = $pdo->prepare("DELETE FROM event_comments WHERE event_id IN ($placeholders)");
                                $stmt->execute($event_ids);
                                
                                $stmt = $pdo->prepare("DELETE FROM event_files WHERE event_id IN ($placeholders)");
                                $stmt->execute($event_ids);
                                
                                $stmt = $pdo->prepare("DELETE FROM events WHERE id IN ($placeholders)");
                                $stmt->execute($event_ids);
                                
                                $success_message = count($event_ids) . " events deleted successfully.";
                                break;
                        }
                    }
                    break;
            }
        } catch(PDOException $e) {
            error_log("Events Management Error: " . $e->getMessage());
            $error_message = "An error occurred while processing your request.";
        }
    }
}

// Pagination and filtering
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';
$date_filter = $_GET['date'] ?? '';

// Build query
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(e.title LIKE ? OR e.description LIKE ? OR e.location LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

if (!empty($status_filter)) {
    $where_conditions[] = "e.status = ?";
    $params[] = $status_filter;
}

if (!empty($category_filter)) {
    $where_conditions[] = "e.category_id = ?";
    $params[] = $category_filter;
}

if (!empty($date_filter)) {
    switch ($date_filter) {
        case 'today':
            $where_conditions[] = "DATE(e.datestart) = CURDATE()";
            break;
        case 'upcoming':
            $where_conditions[] = "e.datestart > NOW()";
            break;
        case 'past':
            $where_conditions[] = "e.datestart < NOW()";
            break;
        case 'this_week':
            $where_conditions[] = "YEARWEEK(e.datestart) = YEARWEEK(NOW())";
            break;
        case 'this_month':
            $where_conditions[] = "YEAR(e.datestart) = YEAR(NOW()) AND MONTH(e.datestart) = MONTH(NOW())";
            break;
    }
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

try {
    // Get total count
    $count_query = "
        SELECT COUNT(*) 
        FROM events e 
        LEFT JOIN event_categories ec ON e.category_id = ec.id 
        $where_clause
    ";
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_events = $stmt->fetchColumn();
    
    $total_pages = ceil($total_events / $per_page);
    
    // Get events
    $query = "
        SELECT e.*, 
               ec.name as category_name,
               COUNT(er.id) as registration_count,
               COALESCE(SUM(CASE WHEN er.status = 'confirmed' THEN 1 ELSE 0 END), 0) as confirmed_count
        FROM events e 
        LEFT JOIN event_categories ec ON e.category_id = ec.id
        LEFT JOIN event_registrations er ON e.id = er.event_id 
            AND er.status IN ('registered', 'confirmed')
        $where_clause
        GROUP BY e.id 
        ORDER BY e.datestart DESC 
        LIMIT $per_page OFFSET $offset
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get categories for filter
    $stmt = $pdo->prepare("SELECT * FROM event_categories ORDER BY name");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    error_log("Events Query Error: " . $e->getMessage());
    $events = [];
    $categories = [];
    $total_events = 0;
    $total_pages = 0;
}

// Include the admin template
include '../assets/includes/main.php';
$page_id = isset($_GET['page_id']) ? $_GET['page_id'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','title','description','color','datestart','dateend','recurring','photo_url','submit_date','uid'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination pagination_page
$results_per_pagination_page = 20;
// events array
$events = [];
// Declare query param variables
$param1 = ($pagination_page - 1) * $results_per_pagination_page;
$param2 = $results_per_pagination_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (e.title LIKE :search OR e.description LIKE :search) ' : '';
// Add filters
// Recurring filter
if ($recurring) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'e.recurring = :recurring ';
}
// Date start filter
if ($datestart) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'e.datestart >= :datestart ';
}
// Date end filter
if ($dateend) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'e.dateend <= :dateend ';
}
// Status filter
$current_date = date('Y-m-d H:i:s');
if ($status == 'active') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'e.datestart <= :current_date AND e.dateend >= :current_date ';
}
if ($status == 'upcoming') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'e.datestart >= :current_date ';
}
if ($status == 'ended') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'e.dateend < :current_date ';
}
// Page ID filter
if ($page_id) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'e.uid = :page_id ';
}
// Retrieve the total number of events
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM events e ' . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($recurring) $stmt->bindParam('recurring', $recurring, PDO::PARAM_STR);
if ($datestart) $stmt->bindParam('datestart', $datestart, PDO::PARAM_STR);
if ($dateend) $stmt->bindParam('dateend', $dateend, PDO::PARAM_STR);
if ($status) $stmt->bindParam('current_date', $current_date, PDO::PARAM_STR);
if ($page_id) $stmt->bindParam('page_id', $page_id, PDO::PARAM_INT);
$stmt->execute();
$total_events = $stmt->fetchColumn();
// Prepare events query
$stmt = $pdo->prepare('SELECT e.*, epd.url FROM events e LEFT JOIN event_page_details epd ON epd.page_id = e.uid ' . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($recurring) $stmt->bindParam('recurring', $recurring, PDO::PARAM_STR);
if ($datestart) $stmt->bindParam('datestart', $datestart, PDO::PARAM_STR);
if ($dateend) $stmt->bindParam('dateend', $dateend, PDO::PARAM_STR);
if ($status) $stmt->bindParam('current_date', $current_date, PDO::PARAM_STR);
if ($page_id) $stmt->bindParam('page_id', $page_id, PDO::PARAM_INT);
$stmt->execute();
// Retrieve query results
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete event
if (isset($_GET['delete'])) {
    // Delete the event
    $stmt = $pdo->prepare('DELETE FROM events WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: events.php?success_msg=3');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Event created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Event updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Event deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Event(s) imported successfully! ' . $_GET['imported'] . ' event(s) were imported.';
    }
}
// Create URL
$url = 'events.php?search_query=' . $search . '&recurring=' . $recurring . '&datestart=' . $datestart . '&dateend=' . $dateend . '&status=' . $status . '&page_id=' . $page_id;
?>
<?=template_admin_header('Events', 'events', 'view')?>

<div class="content-title">
    <div class="title">
        <div class="icon">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9,10V12H7V10H9M13,10V12H11V10H13M17,10V12H15V10H17M19,3A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5C3.89,21 3,20.1 3,19V5A2,2 0 0,1 5,3H6V1H8V3H16V1H18V3H19M19,19V8H5V19H19M9,14V16H7V14H9M13,14V16H11V14H13M17,14V16H15V14H17Z" /></svg>
        </div>
        <div class="txt">
            <h2>Events</h2>
            <p>View, edit, and create events.</p>
        </div>
    </div>
</div>

<?php if (isset($success_msg)): ?>
<div class="msg success">
    <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
    <p><?=$success_msg?></p>
    <svg class="close" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
</div>
<?php endif; ?>

<div class="content-header responsive-flex-column pad-top-5">
    <a href="event.php" class="btn">
        <i class="fas fa-plus me-1"></i>Create Event
    </a>
    <form action="" method="get">
        <input type="hidden" name="page" value="events">
        <div class="filters">
            <a href="#">
                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M0 416c0 17.7 14.3 32 32 32l54.7 0c12.3 28.3 40.5 48 73.3 48s61-19.7 73.3-48L480 448c17.7 0 32-14.3 32-32s-14.3-32-32-32l-246.7 0c-12.3-28.3-40.5-48-73.3-48s-61 19.7-73.3 48L32 384c-17.7 0-32 14.3-32 32zm128 0a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zM320 256a32 32 0 1 1 64 0 32 32 0 1 1 -64 0zm32-80c-32.8 0-61 19.7-73.3 48L32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l246.7 0c12.3 28.3 40.5 48 73.3 48s61-19.7 73.3-48l54.7 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-54.7 0c-12.3-28.3-40.5-48-73.3-48zM192 128a32 32 0 1 1 0-64 32 32 0 1 1 0 64zm73.3-64C253 35.7 224.8 16 192 16s-61 19.7-73.3 48L32 64C14.3 64 0 78.3 0 96s14.3 32 32 32l86.7 0c12.3 28.3 40.5 48 73.3 48s61-19.7 73.3-48L480 128c17.7 0 32-14.3 32-32s-14.3-32-32-32L265.3 64z"/></svg>
                Filters
            </a>
            <div class="list">
                <label for="datestart">Date Start</label>
                <input type="datetime-local" name="datestart" id="datestart" value="<?=htmlspecialchars($datestart, ENT_QUOTES)?>">
                <label for="dateend">Date End</label>
                <input type="datetime-local" name="dateend" id="dateend" value="<?=htmlspecialchars($dateend, ENT_QUOTES)?>">
                <label for="recurring">Recurring</label>
                <select name="recurring" id="recurring">
                    <option value=""<?=$recurring==''?' selected':''?>>All</option>
                    <option value="never"<?=$recurring=='never'?' selected':''?>>Never</option>
                    <option value="daily"<?=$recurring=='daily'?' selected':''?>>Daily</option>
                    <option value="weekly"<?=$recurring=='weekly'?' selected':''?>>Weekly</option>
                    <option value="monthly"<?=$recurring=='monthly'?' selected':''?>>Monthly</option>
                    <option value="yearly"<?=$recurring=='yearly'?' selected':''?>>Yearly</option>
                </select>
                <label for="status">Status</label>
                <select name="status" id="status">
                    <option value=""<?=$status==''?' selected':''?>>All</option>
                    <option value="active"<?=$status=='active'?' selected':''?>>Active</option>
                    <option value="upcoming"<?=$status=='upcoming'?' selected':''?>>Upcoming</option>
                    <option value="ended"<?=$status=='ended'?' selected':''?>>Ended</option>
                </select>
                <button type="submit">Apply</button>
            </div>
        </div>
        <div class="search">
            <label for="search_query">
                <input id="search_query" type="text" name="search_query" placeholder="Search event..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>" class="responsive-width-100">
                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
            </label>
        </div>
    </form>
</div>

<div class="filter-list">
    <?php if ($recurring != ''): ?>
    <div class="filter">
        <a href="<?=remove_url_param($url, 'recurring')?>"><svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></a>
        Recurring : <?=htmlspecialchars($recurring, ENT_QUOTES)?>
    </div>
    <?php endif; ?>
    <?php if ($datestart != ''): ?>
    <div class="filter">
        <a href="<?=remove_url_param($url, 'datestart')?>"><svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free --><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></a>
        Date Start : <?=htmlspecialchars($datestart, ENT_QUOTES)?>
    </div>
    <?php endif; ?>
    <?php if ($dateend != ''): ?>
    <div class="filter">
        <a href="<?=remove_url_param($url, 'dateend')?>"><svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free --><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></a>
        Date End : <?=htmlspecialchars($dateend, ENT_QUOTES)?>
    </div>
    <?php endif; ?>
    <?php if ($status != ''): ?>
    <div class="filter">
        <a href="<?=remove_url_param($url, 'status')?>"><svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free --><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></a>
        Status : <?=htmlspecialchars($status, ENT_QUOTES)?>
    </div>
    <?php endif; ?>
    <?php if ($page_id != ''): ?>
    <div class="filter">
        <a href="<?=remove_url_param($url, 'page_id')?>"><svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free --><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></a>
        Page ID : <?=htmlspecialchars($page_id, ENT_QUOTES)?>
    </div>
    <?php endif; ?>
    <?php if ($search != ''): ?>
    <div class="filter">
        <a href="<?=remove_url_param($url, 'search_query')?>"><svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free --><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></a>
        Search : <?=htmlspecialchars($search, ENT_QUOTES)?>
    </div>
    <?php endif; ?>   
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=id'?>">#<?=$order_by=='id' ? $table_icons[strtolower($order)] : ''?></td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=title'?>">Title<?=$order_by=='title' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=description'?>">Description<?=$order_by=='description' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=datestart'?>">Start Date<?=$order_by=='datestart' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=dateend'?>">End Date<?=$order_by=='dateend' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=recurring'?>">Recurring<?=$order_by=='recurring' ? $table_icons[strtolower($order)] : ''?></td>
                    <td>Status</td>
                    <td><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=uid'?>">Page ID<?=$order_by=='uid' ? $table_icons[strtolower($order)] : ''?></td>
                    <td class="align-center">Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$events): ?>
                <tr>
                    <td colspan="20" class="no-results">There are no events.</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?=$event['id']?></td>
                    <td class="title"><span class="event-color" style="color:<?=$event['color']?>" title="<?=$event['color']?>">&#9632;</span> <?=htmlspecialchars($event['title'], ENT_QUOTES)?></td>
                    <td class="alt responsive-hidden"><?=nl2br(htmlspecialchars($event['description'], ENT_QUOTES))?></td>
                    <td class="alt responsive-hidden"><?=date('F j, Y H:ia', strtotime($event['datestart']))?></td>
                    <td class="alt responsive-hidden"><?=date('F j, Y H:ia', strtotime($event['dateend']))?></td>
                    <td class="alt responsive-hidden"><?=$event['recurring'] != 'never' ? '<span class="grey">' . ucwords($event['recurring']) . '</span>' : '--'?></td>
                    <td>
                        <?php if (strtotime($event['datestart']) <= time() && strtotime($event['dateend']) >= time()): ?>
                        <span class="green">Active</span>
                        <?php elseif (strtotime($event['dateend']) < time() && $event['recurring'] == 'never'): ?>
                        <span class="red">Ended</span>
                        <?php elseif (strtotime($event['datestart']) > time() && $event['recurring'] == 'never'): ?>
                        <span class="grey">Upcoming</span>
                        <?php elseif ($event['recurring'] != 'never'): ?>
                        <span class="green">Active</span>
                        <?php endif; ?>
                    </td>
                    <td><?=$event['url'] ? '<a href="' . htmlspecialchars($event['url'], ENT_QUOTES) . '" target="_blank" class="link1">' . $event['uid'] . '</a>' : $event['uid']?></td>
                    <td class="actions">
                        <div class="table-dropdown">
                            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/></svg>
                            <div class="table-dropdown-items">
                                <a href="event.php?id=<?=$event['id']?>">
                                    <span class="icon">
                                        <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/></svg>
                                    </span>
                                    Edit
                                </a>
                                <a class="red" href="events.php?delete=<?=$event['id']?>" onclick="return confirm('Are you sure you want to delete this event?')">
                                    <span class="icon">
                                        <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
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
    <span>Page <?=$pagination_page?> of <?=ceil($total_events / $results_per_pagination_page) == 0 ? 1 : ceil($total_events / $results_per_pagination_page)?></span>
    <?php if ($pagination_page * $results_per_pagination_page < $total_events): ?>
    <a href="<?=$url?>&pagination_page=<?=$pagination_page+1?>&order=<?=$order?>&order_by=<?=$order_by?>">Next</a>
    <?php endif; ?>
</div>

<?=template_admin_footer()?>