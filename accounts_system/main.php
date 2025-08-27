<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOCATION: public_html/accounts_system/main.php
 * LOG: Core functions, database connection, and role-based redirect logic
 * PRODUCTION: [To be updated on deployment]
 */

$admin_path = '/public_html/admin';
$public_path = '/public_html';
//Get the database configuration file 
// Robust config file loading for both accounts_system and public_html includes
$config_candidates = [
	__DIR__ . '/../../private/gws-universal-config.php',
	__DIR__ . '/../../../private/gws-universal-config.php',
	__DIR__ . '/../../gws-universal-config.php',
	__DIR__ . '/../../../gws-universal-config.php',
	dirname(__DIR__, 2) . '/private/gws-universal-config.php',
	dirname(__DIR__, 2) . '/gws-universal-config.php',
];
$config_path = null;
foreach ($config_candidates as $candidate)
{
	if (file_exists($candidate))
	{
		$config_path = $candidate;
		break;
	}
}
if (!$config_path)
{
	die('Unable to locate gws-universal-config.php');
}
require_once $config_path;

// Load accounts system settings
$settingsPath = __DIR__ . '/accounts-system-config.php';
if (file_exists($settingsPath))
{
    $account_settings = include($settingsPath);
    if (!is_array($account_settings))
    {
        die('Invalid settings format in accounts-system-config.php');
    }
} else
{
    die('Unable to locate accounts-system-config.php in accounts system directory');
}

// Namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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
// Role-based redirect function
function redirect_by_role($role)
{
    $role = strtolower(trim($role));
    
    // Default redirect paths for each role
    $rolePaths = [
        'developer' => 'admin/index.php',
        'admin' => 'admin/index.php',
        'editor' => 'admin/blog/index.php',
        'member' => 'client_portal/index.php',
        'blog_user' => 'blog.php',
        'subscriber' => 'index.php',
        'guest' => 'index.php'
    ];
    
    // Get redirect URL with fallback to public index
    $redirect_url = $rolePaths[$role] ?? 'index.php';
    
    header('Location: ' . $redirect_url);
    exit;
}

// Include unified template system

// Template header function - now uses unified template
function template_header($title)
{
	// Minimal header output (customize as needed)
	echo "<header><h1>" . htmlspecialchars($title) . "</h1></header>\n";
}

// Template footer function - now uses unified template
function template_footer()
{
	// Minimal footer output (customize as needed)
	echo "<footer><p>&copy; " . date('Y') . " GWS Universal Hybrid Application</p></footer>\n";
}
 
// Send activation email function
function send_activation_email($email, $code)
{
	global $account_settings;
	if (empty($account_settings['mail_enabled']))
		return;
	// Include PHPMailer library
	include_once 'lib/phpmailer/Exception.php';
	include_once 'lib/phpmailer/PHPMailer.php';
	include_once 'lib/phpmailer/SMTP.php';
	// Create an instance; passing `true` enables exceptions
	$mail = new PHPMailer(true);
	try
	{
		// Server settings
		if (!empty($account_settings['SMTP']))
		{
			$mail->isSMTP();
			$mail->Host = $account_settings['smtp_host'];
			$mail->SMTPAuth = true;
			$mail->Username = $account_settings['smtp_user'];
			$mail->Password = $account_settings['smtp_pass'];
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port = $account_settings['smtp_port'];
		}
		// Recipients
		$mail->setFrom($account_settings['mail_from'], $account_settings['mail_name']);
		$mail->addAddress($email);
		$mail->addReplyTo($account_settings['mail_from'], $account_settings['mail_name']);
		// Content
		$mail->isHTML(true);
		$mail->Subject = 'Account Activation Required';
		// Activation link
		$activate_link = $account_settings['activation_link'] . '?code=' . $code;
		// Read the template contents and replace the "%link" placeholder with the above variable
		$email_template = str_replace('%link%', $activate_link, file_get_contents('activation-email-template.html'));
		// Set email body
		$mail->Body = $email_template;
		$mail->AltBody = strip_tags($email_template);
		// Send mail
		$mail->send();
	} catch (Exception $e)
	{
		// Output error message
		exit('Error: Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
	}
}//end of send_activation_email function

// Send notification email function
function send_notification_email($account_id, $account_username, $account_email, $account_date)
{
	global $account_settings;
	if (empty($account_settings['mail_enabled']))
		return;
	// Include PHPMailer library
	include_once 'lib/phpmailer/Exception.php';
	include_once 'lib/phpmailer/PHPMailer.php';
	include_once 'lib/phpmailer/SMTP.php';
	// Create an instance; passing `true` enables exceptions
	$mail = new PHPMailer(true);
	try
	{
		// Server settings
		if (!empty($account_settings['SMTP']))
		{
			$mail->isSMTP();
			$mail->Host = $account_settings['smtp_host'];
			$mail->SMTPAuth = true;
			$mail->Username = $account_settings['smtp_user'];
			$mail->Password = $account_settings['smtp_pass'];
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port = $account_settings['smtp_port'];
		}
		// Recipients
		$mail->setFrom($account_settings['mail_from'], $account_settings['mail_name']);
		$mail->addAddress($account_settings['notify_admin_email']);
		$mail->addReplyTo($account_settings['mail_from'], $account_settings['mail_name']);
		// Content
		$mail->isHTML(true);
		$mail->Subject = 'A new user has registered!';
		// Read the template contents and replace the "%link" placeholder with the above variable
		$email_template = str_replace(['%id%', '%username%', '%date%', '%email%'], [$account_id, htmlspecialchars($account_username, ENT_QUOTES), $account_date, $account_email], file_get_contents('notification-email-template.html'));
		// Set email body
		$mail->Body = $email_template;
		$mail->AltBody = strip_tags($email_template);
		// Send mail
		$mail->send();
	} catch (Exception $e)
	{
		// Output error message
		exit('Error: Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
	}
}//end of send_notification_email function

// Send password reset email function
function send_password_reset_email($email, $username, $code)
{
	global $account_settings;
	if (empty($account_settings['mail_enabled']))
		return;
	// Include PHPMailer library
	include_once 'lib/phpmailer/Exception.php';
	include_once 'lib/phpmailer/PHPMailer.php';
	include_once 'lib/phpmailer/SMTP.php';
	// Create an instance; passing `true` enables exceptions
	$mail = new PHPMailer(true);
	try
	{
		// Server settings
		if (!empty($account_settings['SMTP']))
		{
			$mail->isSMTP();
			$mail->Host = $account_settings['smtp_host'];
			$mail->SMTPAuth = true;
			$mail->Username = $account_settings['smtp_user'];
			$mail->Password = $account_settings['smtp_pass'];
			$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
			$mail->Port = $account_settings['smtp_port'];
		}
		// Recipients
		$mail->setFrom($account_settings['mail_from'], $account_settings['mail_name']);
		$mail->addAddress($email);
		$mail->addReplyTo($account_settings['mail_from'], $account_settings['mail_name']);
		// Content
		$mail->isHTML(true);
		$mail->Subject = 'Password Reset';
		// Password reset link
		$reset_link = $account_settings['reset_password_url'] . '?code=' . $code;
		// Read the template contents and replace the "%link%" placeholder with the above variable
		$email_template_path = __DIR__ . '/resetpass-email-template.html';
		$email_template = str_replace(['%link%', '%username%'], [$reset_link, htmlspecialchars($username, ENT_QUOTES)], file_get_contents($email_template_path));
		// Set email body
		$mail->Body = $email_template;
		$mail->AltBody = strip_tags($email_template);
		// Send mail
		$mail->send();
	} catch (Exception $e)
	{
		// Output error message
		exit('Error: Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
	}
}//end of send_password_reset_email function
// End of main.php
?>