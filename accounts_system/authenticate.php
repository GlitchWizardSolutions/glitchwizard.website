<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOCATION: public_html/accounts_system/authenticate.php
 * LOG: User authentication handler with role-based redirects
 * PRODUCTION: [To be updated on deployment]
 */

include 'main.php';
 
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if (!isset($_POST['username'], $_POST['password']))
{
	if (is_ajax())
	{
		echo 'Error: Please fill both the username and password fields!';
		exit;
	} else
	{
		header('Location: ../auth.php?tab=login&error=' . urlencode('Please fill both the username and password fields!'));
		exit;
	}
}
// Prepare our SQL query and find the account associated with the login details
// Preparing the SQL statement will prevent SQL injection
$stmt = $pdo->prepare('SELECT * FROM accounts WHERE username = ?');
$stmt->execute([$_POST['username']]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);
// Check if the account exists
if ($account)
{
	// Account exists... Verify the password
	if (password_verify($_POST['password'], $account['password']))
	{
		// Check if the account is activated
		if (!empty($account_settings['account_activation']) && $account['activation_code'] != 'activated')
		{
			if (is_ajax())
			{
				echo 'Error: Please activate your account to login! Click here to resend the activation email.';
				exit;
			} else
			{
				header('Location: ../auth.php?tab=login&error=' . urlencode('Please activate your account to login! Click here to resend the activation email.'));
				exit;
			}
		} else if ($account['activation_code'] == 'deactivated')
		{
			if (is_ajax())
			{
				echo 'Error: Your account has been deactivated!';
				exit;
			} else
			{
				header('Location: ../auth.php?tab=login&error=' . urlencode('Your account has been deactivated!'));
				exit;
			}
		} else if (!empty($account_settings['account_approval']) && !$account['approved'])
		{
			if (is_ajax())
			{
				echo 'Error: Your account has not been approved yet!';
				exit;
			} else
			{
				header('Location: ../auth.php?tab=login&error=' . urlencode('Your account has not been approved yet!'));
				exit;
			}
		} else
		{
			// Verification success! User has loggedin!
			session_regenerate_id();
			$_SESSION['loggedin'] = TRUE;
			$_SESSION['name'] = $account['username'];
			$_SESSION['id'] = $account['id'];
			$_SESSION['role'] = $account['role'];
			// IF the "remember me" checkbox is checked...
			if (isset($_POST['rememberme']))
			{
				$cookie_hash = !empty($account['rememberme']) ? $account['rememberme'] : password_hash($account['id'] . $account['username'] . $account_settings['account_secret_key'], PASSWORD_DEFAULT);
				$days = 30;
				setcookie('rememberme', $cookie_hash, (int) (time() + 60 * 60 * 24 * $days));
				$stmt = $pdo->prepare('UPDATE accounts SET rememberme = ? WHERE id = ?');
				$stmt->execute([$cookie_hash, $account['id']]);
			}
			$date = date('Y-m-d\TH:i:s');
			$stmt = $pdo->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
			$stmt->execute([$date, $account['id']]);
			
			// Check for redirect parameter first
			if (isset($_GET['redirect']) && $_GET['redirect'] === 'review') {
				$redirect_url = '../public_reviews.php';
			} else {
				// Default role-based redirect
				$redirect_url = '';
				switch (strtolower(trim($account['role'])))
				{
					case 'admin':
					case 'developer':
						$redirect_url = '../client_portal/';
						break;
					case 'blog_only':
						$redirect_url = '../blog_system/';
						break;
					default:
						$redirect_url = '../client_portal/';
						break;
				}
			}
			if (is_ajax())
			{
				echo 'redirect';
				exit;
			} else
			{
				header('Location: ' . $redirect_url);
				exit;
			}
		}
	} else
	{
		if (is_ajax())
		{
			echo 'Error: Incorrect username and/or password!';
			exit;
		} else
		{
			header('Location: ../auth.php?tab=login&error=' . urlencode('Incorrect username and/or password!'));
			exit;
		}
	}
} else
{
	if (is_ajax())
	{
		echo 'Error: Incorrect username and/or password!';
		exit;
	} else
	{
		header('Location: ../login.php?error=' . urlencode('Incorrect username and/or password!'));
		exit;
	}
}
?>