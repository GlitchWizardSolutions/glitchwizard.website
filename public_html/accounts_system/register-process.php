<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOCATION: public_html/accounts_system/register-process.php
 * LOG: User registration form processing and validation
 * PRODUCTION: [To be updated on deployment]
 */

include 'main.php';

// Account activation and secret key config
if (!defined('account_activation'))
{
	define('account_activation', false); // Set to true to require email activation
}
if (!defined('secret_key'))
{
	if (!defined('secret_key')) define('secret_key', 'changeme'); // Change to a secure random string in production
}

// Additional config constants for registration logic
if (!defined('account_approval'))
{
	define('account_approval', false); // Set to true to require admin approval
}
if (!defined('notifications_enabled'))
{
	define('notifications_enabled', false); // Set to true to enable notifications
}
if (!defined('auto_login_after_register'))
{
	define('auto_login_after_register', true); // Set to true to auto-login after registration
}

// Now we check if the data was submitted, isset() function will check if the data exists.
if (!isset($_POST['username'], $_POST['password'], $_POST['cpassword'], $_POST['email']))
{
	// Could not get the data that should have been sent.
	exit('Error: Please complete the registration form!');
}
// Make sure the submitted registration values are not empty.
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email']))
{
	// One or more values are empty.
	exit('Error: Please complete the registration form!');
}
// Check to see if the email is valid.
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
{
	exit('Error: Please provide a valid email address!');
}
// Username must contain only characters and numbers.
if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['username']))
{
	exit('Error: Username must contain only letters and numbers!');
}
// Password must be between 5 and 20 characters long.
if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5)
{
	exit('Error: Password must be between 5 and 20 characters long!');
}
// Check if both the password and confirm password fields match
if ($_POST['cpassword'] != $_POST['password'])
{
	exit('Error: Passwords do not match!');
}
// Check if the account with that username already exists
$stmt = $pdo->prepare('SELECT * FROM accounts WHERE username = ? OR email = ?');
$stmt->execute([$_POST['username'], $_POST['email']]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);
// Store the result, so we can check if the account exists in the database.
if ($account)
{
	// Username or email already exists
	echo 'Error: Username and/or email exists!';
	// Stop further processing
	exit;
} else
{
	// Username doesnt exists, insert new account
	// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	// Generate unique activation code
	$activation_code = account_activation ? hash('sha256', uniqid() . $_POST['email'] . secret_key) : 'activated';
	// Approval required?
	$approved = account_approval ? 0 : 1;
	// Default role
	$role = 'Member';
	// Current date
	$date = date('Y-m-d\TH:i:s');
	// Prepare query; prevents SQL injection
	$stmt = $pdo->prepare('INSERT INTO accounts (username, password, email, activation_code, role, registered, last_seen, approved) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
	$stmt->execute([$_POST['username'], $password, $_POST['email'], $activation_code, $role, $date, $date, $approved]);
	// Get last insert ID
	$id = $pdo->lastInsertId();
	// Send notification email
	if (notifications_enabled)
	{
		send_notification_email($id, $_POST['username'], $_POST['email'], $date);
	}
	// If account activation is required, send activation email
	if (account_activation)
	{
		// Account activation required, send the user the activation email with the "send_activation_email" function from the "main.php" file
		send_activation_email($_POST['email'], $activation_code);
		echo 'Success: Please check your email to activate your account!';
	} else
	{
		// Automatically authenticate the user if the option is enabled
		if (auto_login_after_register)
		{
			// Regenerate session ID
			session_regenerate_id();
			// Declare session variables
			$_SESSION['loggedin'] = TRUE;
			$_SESSION['name'] = $_POST['username'];
			$_SESSION['id'] = $id;
			$_SESSION['role'] = $role;
			// Do not change the output message as the AJAX code will use this to detect if the registration was successful and redirect to the home page
			echo 'redirect';
		} else
		{
			echo 'Success: You have successfully registered! You can now login!';
		}
	}
}
?>