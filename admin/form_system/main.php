<?php
session_start();
// Include the configuration file
include_once '../config.php';
// Check if admin is logged in
if (!isset($_SESSION['contact_account_loggedin'])) {
    header('Location: login.php');
    exit;
}
try {
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to database!');
}
// Template admin header
function template_admin_header($title, $selected = 'dashboard', $selected_child = 'view') {
    global $pdo;
    // Retrieve the total number of unread messages
    $unread_messages = $pdo->query('SELECT COUNT(*) AS total FROM messages WHERE status = "Unread"')->fetchColumn();
    $read_messages = $pdo->query('SELECT COUNT(*) AS total FROM messages WHERE status = "Read"')->fetchColumn();
    // Retrieve the total number of messages
    // Admin HTML links
    $admin_links = '
    <a href="index.php"' . ($selected == 'dashboard' ? ' class="selected"' : '') . '><i class="bi bi-speedometer2"></i>Dashboard</a>
    <a href="messages.php"' . ($selected == 'messages' ? ' class="selected"' : '') . '><i class="bi bi-inbox"></i>Messages<span class="note">' . $unread_messages . '</span></a>
        <div class="sub">
            <a href="messages.php"' . ($selected == 'messages' && $selected_child == 'all' ? ' class="selected"' : '') . '><span class="square"></span>All Messages</a>
            <a href="messages.php?status=Unread&nav=unread"' . ($selected == 'messages' && $selected_child == 'unread' ? ' class="selected"' : '') . '><span class="square"></span>Unread Messages (' . $unread_messages . ')</a>
            <a href="messages.php?status=Read&nav=read"' . ($selected == 'messages' && $selected_child == 'read' ? ' class="selected"' : '') . '><span class="square"></span>Read Messages (' . $read_messages . ')</a>
        </div>
    <a href="accounts.php"' . ($selected == 'accounts' ? ' class="selected"' : '') . '><i class="bi bi-people"></i>Accounts</a>
        <div class="sub">
            <a href="accounts.php"' . ($selected == 'accounts' && $selected_child == 'view' ? ' class="selected"' : '') . '><span class="square"></span>View Accounts</a>
            <a href="account.php"' . ($selected == 'accounts' && $selected_child == 'manage' ? ' class="selected"' : '') . '><span class="square"></span>Create Account</a>
        </div>
    <a href="settings.php"' . ($selected == 'settings' ? ' class="selected"' : '') . '><i class="bi bi-tools"></i>Settings</a>
    ';
echo <<<EOT
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>$title</title>
        <link rel="icon" type="image/png" href="../favicon.png">
		<link href="admin.css" rel="stylesheet" type="text/css">
	</head>
	<body class="admin">
        <aside class="responsive-width-100 responsive-hidden">
            <h1>Admin</h1>
            $admin_links
            <div class="footer">
                <a href="https://codeshack.io/package/php/advanced-contact-form/" target="_blank">Advanced Contact Form</a>
                Version 2.0.0
            </div>
        </aside>
        <main class="responsive-width-100">
            <header>
                <a class="responsive-toggle" href="#">
                    <i class="bi bi-list"></i>
                </a>
                <div class="space-between"></div>
                <div class="dropdown right">
                    <i class="bi bi-person-circle"></i>
                    <div class="list">
                        <a href="account.php?id={$_SESSION['contact_account_id']}">Edit Profile</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </header>
EOT;
}
// Template admin footer
function template_admin_footer($js_script = '') {
echo <<<EOT
        </main>
        <script src="admin.js"></script>
        {$js_script}
    </body>
</html>
EOT;
}
?>