<?php
// Include the configuration file
include 'config.php';
// Connect to MySQL using the below function
function pdo_connect_mysql($db_host, $db_name, $db_user, $db_pass, $db_charset = 'utf8') {
	// Connect to the MySQL database using the PDO interface
	try {
		$pdo = new PDO('mysql:host=' . $db_host . ';dbname=' . $db_name . ';charset=' . $db_charset, $db_user, $db_pass);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $exception) {
		// If there is an error with the connection, stop the script and display the error.
		exit('Failed to connect to database: ' . $exception->getMessage());
	}
	return $pdo;
}
// Template header, feel free to customize this
function template_header($title) {
// Do NOT indent the below code, otherwise it will not work properly
echo <<<EOT
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>$title</title>
		<link href="style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<header class="header">

			<div class="wrapper">

				<h1>CRUD</h1>

				<input type="checkbox" id="menu">
				<label for="menu">
					<i class="fa-solid fa-bars"></i>
				</label>
				
				<nav class="menu">
					<a href="index.php"><i class="fas fa-home"></i>Home</a>
					<a href="read.php"><i class="fas fa-address-book"></i>Contacts</a>
					<a href="import.php"><i class="fa-solid fa-file-import"></i>Import</a>
				</nav>

			</div>

		</header>
EOT;
}
// Template footer
function template_footer() {
echo <<<EOT
		<script src="script.js"></script>
    </body>
</html>
EOT;
}
?>