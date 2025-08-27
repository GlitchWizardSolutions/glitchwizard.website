<?php
// Start the session
session_start();
// Include the configuration file
include_once 'config.php';
// Connect to the MySQL database using the PDO interface
try {
	$pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to database: ' . $exception->getMessage());
}
// Function that retrieves the client IP address
function get_ip_address() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}
// Template header function
function template_header($title) {
	// Get the current file name (eg. home.php, profile.php)
	$current_file_name = basename($_SERVER['PHP_SELF']);
	// Admin panel link - will only be visible if the user is an admin
	$admin_panel_link = isset($_SESSION['account_role']) && $_SESSION['account_role'] == 'Admin' ? '<a href="admin/index.php" target="_blank">Admin</a>' : '';
	// Create link
	$create_link = (create_polls == 'everyone' || (create_polls == 'user' && isset($_SESSION['account_loggedin'])) || (create_polls == 'admin' && isset($_SESSION['account_loggedin']) && $_SESSION['account_role'] == 'Admin')) ? '<a href="create.php" class="' . ($current_file_name == 'create.php' ? 'active' : '') . '">Create Poll</a>' : '';
	// Indenting the below code may cause HTML validation errors
echo '<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<title>' . $title . '</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>

		<header class="header">

			<div class="wrapper">

				<h1>Poll &amp; Voting System</h1>

				<!-- If you prefer to use a logo instead of text uncomment the below code and remove the above h1 tag and replace the src attribute with the path to your logo image
				<img src="https://via.placeholder.com/200x45" width="200" height="45" alt="Logo" class="logo">
				-->

				<!-- Responsive menu toggle icon -->
				<input type="checkbox" id="menu">
				<label for="menu"></label>
				
				<nav class="menu">
                    <a href="index.php" class="' . ($current_file_name == 'index.php' ? 'active' : '') . '">Polls List</a>
					' . $create_link . '
					' . $admin_panel_link . '
				</nav>

			</div>

		</header>

		<div class="content">
';
}
// Template footer function
function template_footer() {
	// Output the footer HTML
	echo '</div>
		<script>
		const images_enabled = ' . (images_enabled ? 'true' : 'false') . ';
		</script>
		<script src="script.js"></script>
	</body>
</html>';
}
?>