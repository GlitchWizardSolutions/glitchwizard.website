<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOCATION: public_html/accounts_system/profile.php
 * LOG: User profile management and s			// Update the session variables
			$_SESSION['name'] = $_POST['username'];

			// If email has changed and activation is required, send new activation email
			if ($activation_enabled && $email_changed)
			{
				// Send the activation email and log out the user
				send_activation_email($_POST['email'], $activation_code);
 * PRODUCTION: [To be updated on deployment]
 */

include 'main.php';
require_once '../../private/gws-universal-functions.php';

// Check if account activation is enabled
if (!defined('account_activation')) {
    define('account_activation', false);
}

// Check if user is logged in with remember-me support
check_loggedin_full($pdo, '../auth.php?tab=login');

// Error message variable
$error_msg = '';
// Success message variable
$success_msg = '';

// Get current user ID
$userId = get_current_user_id();

// Retrieve additional account info from the database
$stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
$stmt->execute([$userId]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user is subscribed to newsletter
$stmt = $pdo->prepare('SELECT COUNT(*) FROM blog_newsletter WHERE email = ?');
$stmt->execute([$account['email']]);
$is_subscribed_to_newsletter = $stmt->fetchColumn() > 0;

// Handle edit profile post data
if (isset($_POST['username'], $_POST['email']))
{
	// Make sure the required fields are not empty
	if (empty($_POST['username']) || empty($_POST['email']))
	{
		$error_msg = 'Username and email are required!';
	} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
	{
		$error_msg = 'Please provide a valid email address!';
	} else if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['username']))
	{
		$error_msg = 'Username must contain only letters and numbers!';
	} else if (!empty($_POST['npassword']) && (strlen($_POST['npassword']) > 20 || strlen($_POST['npassword']) < 5))
	{
		$error_msg = 'Password must be between 5 and 20 characters long!';
	} else if (!empty($_POST['npassword']) && $_POST['cpassword'] != $_POST['npassword'])
	{
		$error_msg = 'Passwords do not match!';
	} else if (!empty($_POST['phone']) && !preg_match('/^[\d\s\-\+\(\)\.]+$/', $_POST['phone']))
	{
		$error_msg = 'Please provide a valid phone number!';
	}
	// No validation errors... Process update
	if (empty($error_msg))
	{
		// Check if new username or email already exists in the database
		$stmt = $pdo->prepare('SELECT COUNT(*) FROM accounts WHERE (username = ? OR email = ?) AND username != ? AND email != ?');
		$stmt->execute([$_POST['username'], $_POST['email'], $account['username'], $account['email']]);
		// Account exists? Output error...
		if ($stmt->fetchColumn() > 0)
		{
			$error_msg = 'Account already exists with that username and/or email!';
		} else
		{
			// No errors occurred, update the account...
			// Hash the new password if it was posted and is not blank
			$password = !empty($_POST['npassword']) ? password_hash($_POST['npassword'], PASSWORD_DEFAULT) : $account['password'];
			
			// Check if email has changed and account activation is enabled
			$email_changed = $account['email'] != $_POST['email'];
			$activation_enabled = defined('account_activation') && account_activation;
			
			// If email has changed and activation is enabled, generate a new activation code
			$activation_code = ($activation_enabled && $email_changed) ? 
				hash('sha256', uniqid() . $_POST['email'] . secret_key) : 
				$account['activation_code'];

			// Get user IP address for newsletter subscription
			$user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

			// Create full_name from first_name and last_name
			$full_name = trim(($_POST['first_name'] ?? '') . ' ' . ($_POST['last_name'] ?? ''));
			$full_name = $full_name ?: 'None Provided';

			// Update the account with all fields
			$stmt = $pdo->prepare('UPDATE accounts SET 
				username = ?, 
				password = ?, 
				email = ?, 
				activation_code = ?,
				first_name = ?,
				last_name = ?,
				full_name = ?,
				phone = ?,
				address_street = ?,
				address_city = ?,
				address_state = ?,
				address_zip = ?,
				address_country = ?,
				avatar = ?
				WHERE id = ?');
			$stmt->execute([
				$_POST['username'],
				$password,
				$_POST['email'],
				$activation_code,
				$_POST['first_name'] ?? '',
				$_POST['last_name'] ?? '',
				$full_name,
				$_POST['phone'] ?? '',
				$_POST['address_street'] ?? '',
				$_POST['address_city'] ?? '',
				$_POST['address_state'] ?? '',
				$_POST['address_zip'] ?? '',
				$_POST['address_country'] ?? 'United States',
				$_POST['avatar'] ?? $account['avatar'],
				$_SESSION['account_id']
			]);

			// Handle newsletter subscription
			$newsletter_checked = isset($_POST['blog_newsletter']);

			// Check if currently subscribed
			$stmt = $pdo->prepare('SELECT COUNT(*) FROM blog_newsletter WHERE email = ?');
			$stmt->execute([$account['email']]);
			$currently_subscribed = $stmt->fetchColumn() > 0;

			if ($newsletter_checked && !$currently_subscribed)
			{
				// Subscribe to newsletter
				$stmt = $pdo->prepare('INSERT INTO blog_newsletter (email, ip) VALUES (?, ?)');
				$stmt->execute([$_POST['email'], $user_ip]);
			} elseif (!$newsletter_checked && $currently_subscribed)
			{
				// Unsubscribe from newsletter
				$stmt = $pdo->prepare('DELETE FROM blog_newsletter WHERE email = ?');
				$stmt->execute([$account['email']]);
			} elseif ($newsletter_checked && $currently_subscribed && $account['email'] != $_POST['email'])
			{
				// Email changed but still wants newsletter - update email in newsletter table
				$stmt = $pdo->prepare('UPDATE blog_newsletter SET email = ?, ip = ? WHERE email = ?');
				$stmt->execute([$_POST['email'], $user_ip, $account['email']]);
			}

			// Update the session variables
			$_SESSION['name'] = $_POST['username'];

			// If email has changed and activation is enabled, logout the user and send a new activation email
			if (defined('account_activation') && account_activation && $account['email'] != $_POST['email'])
			{
				// Account activation required, send the user the activation email with the "send_activation_email" function from the "main.php" file
				send_activation_email($_POST['email'], $activation_code);
				// Logout the user
				unset($_SESSION['loggedin']);
				// Output success message
				$success_msg = 'You have changed your email address! You need to re-activate your account!';
			} else
			{
				// Profile updated successfully, redirect the user back to the profile page
				header('Location: profile.php');
				exit;
			}
		}
	}
}
?>
<?= template_header('Profile') ?>

<?php if (!isset($_GET['action'])): ?>

	<!-- View Profile Page -->

	<div class="page-title">
		<div class="icon">
			<svg width="20" height="20" xmlns="http://www.w3.org/2000/svg"
				viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
				<path
					d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z" />
			</svg>
		</div>
		<div class="wrap">
			<h2>Profile</h2>
			<p>View and edit your profile details below.</p>
		</div>
	</div>

	<div class="block">

		<!-- Tip: it's good practice to escape user variables using htmlspecialchars() to prevent XSS attacks. -->

		<div class="profile-detail">
			<strong>Username</strong>
			<?= htmlspecialchars($account['username'], ENT_QUOTES) ?>
		</div>

		<div class="profile-detail">
			<strong>Email</strong>
			<?= htmlspecialchars($account['email'], ENT_QUOTES) ?>
		</div>

		<div class="profile-detail">
			<strong>First Name</strong>
			<?= htmlspecialchars($account['first_name'] ?: 'Not provided', ENT_QUOTES) ?>
		</div>

		<div class="profile-detail">
			<strong>Last Name</strong>
			<?= htmlspecialchars($account['last_name'] ?: 'Not provided', ENT_QUOTES) ?>
		</div>

		<div class="profile-detail">
			<strong>Phone</strong>
			<?= htmlspecialchars($account['phone'] ?: 'Not provided', ENT_QUOTES) ?>
		</div>

		<div class="profile-detail">
			<strong>Address</strong>
			<?php
			$address = trim(($account['address_street'] ?: '') . ' ' . ($account['address_city'] ?: '') . ' ' . ($account['address_state'] ?: '') . ' ' . ($account['address_zip'] ?: ''));
			echo htmlspecialchars($address ?: 'Not provided', ENT_QUOTES);
			?>
		</div>

		<div class="profile-detail">
			<strong>Country</strong>
			<?= htmlspecialchars($account['address_country'] ?: 'Not provided', ENT_QUOTES) ?>
		</div>

		<div class="profile-detail">
			<strong>Role</strong>
			<?= $account['role'] ?>
		</div>

		<div class="profile-detail">
			<strong>Newsletter</strong>
			<?= $is_subscribed_to_newsletter ? 'Subscribed' : 'Not subscribed' ?>
		</div>

		<div class="profile-detail">
			<strong>Registered</strong>
			<?= date('F j, Y', strtotime($account['registered'])) ?>
		</div>

		<a class="btn blue mar-top-5 mar-bot-2" href="?action=edit">Edit Details</a>

	</div>

<?php elseif ($_GET['action'] == 'edit'): ?>

	<!-- Edit Profile Page - Side by Side Layout -->

	<div class="page-title">
		<div class="icon">
			<svg width="22" height="22" xmlns="http://www.w3.org/2000/svg"
				viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
				<path
					d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H322.8c-3.1-8.8-3.7-18.4-1.4-27.8l15-60.1c2.8-11.3 8.6-21.5 16.8-29.7l40.3-40.3c-32.1-31-75.7-50.1-123.9-50.1H178.3zm435.5-68.3c-15.6-15.6-40.9-15.6-56.6 0l-29.4 29.4 71 71 29.4-29.4c15.6-15.6 15.6-40.9 0-56.6l-14.4-14.4zM375.9 417c-4.1 4.1-7 9.2-8.4 14.9l-15 60.1c-1.4 5.5 .2 11.2 4.2 15.2s9.7 5.6 15.2 4.2l60.1-15c5.6-1.4 10.8-4.3 14.9-8.4L576.1 358.7l-71-71L375.9 417z" />
			</svg>
		</div>
		<div class="wrap">
			<h2>Edit Profile</h2>
			<p>Update your information and see changes reflected immediately.</p>
		</div>
	</div>

	<!-- Side by Side Layout -->
	<div class="profile-edit-container">

		<!-- Left Side - Current Profile Information -->
		<div class="profile-view-section">
			<div class="block">
				<h3>
					<svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
						style="fill: #6c757d; margin-right: 8px;">
						<path
							d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z" />
					</svg>
					Current Profile
				</h3>

				<div class="current-profile-info" id="currentProfileInfo">
					<div class="profile-detail">
						<strong>Username</strong>
						<span id="currentUsername"><?= htmlspecialchars($account['username'], ENT_QUOTES) ?></span>
					</div>

					<div class="profile-detail">
						<strong>Email</strong>
						<span id="currentEmail"><?= htmlspecialchars($account['email'], ENT_QUOTES) ?></span>
					</div>

					<div class="profile-detail">
						<strong>First Name</strong>
						<span
							id="currentFirstName"><?= htmlspecialchars($account['first_name'] ?: 'Not provided', ENT_QUOTES) ?></span>
					</div>

					<div class="profile-detail">
						<strong>Last Name</strong>
						<span
							id="currentLastName"><?= htmlspecialchars($account['last_name'] ?: 'Not provided', ENT_QUOTES) ?></span>
					</div>

					<div class="profile-detail">
						<strong>Phone</strong>
						<span
							id="currentPhone"><?= htmlspecialchars($account['phone'] ?: 'Not provided', ENT_QUOTES) ?></span>
					</div>

					<div class="profile-detail">
						<strong>Role</strong>
						<span><?= $account['role'] ?></span>
					</div>

					<div class="profile-detail">
						<strong>Newsletter</strong>
						<span
							id="currentNewsletter"><?= $is_subscribed_to_newsletter ? 'Subscribed' : 'Not subscribed' ?></span>
					</div>

					<div class="profile-detail">
						<strong>Registered</strong>
						<span><?= date('F j, Y', strtotime($account['registered'])) ?></span>
					</div>

					<div class="profile-detail">
						<strong>Password</strong>
						<span id="currentPasswordStatus">Unchanged</span>
					</div>
				</div>

				<div class="profile-tips">
					<h4>
						<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
							style="fill: #0ea5e9; margin-right: 8px;">
							<path
								d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z" />
						</svg>
						Tips
					</h4>
					<ul class="tips-list">
						<li>Your changes will be reflected here immediately as you type</li>
						<li>Leave password fields empty to keep current password</li>
						<li>Password status shows if password will be updated</li>
						<li>Username and email must be unique</li>
						<li>All changes require form submission to save</li>
					</ul>
				</div>
			</div>
		</div>

		<!-- Right Side - Edit Form -->
		<div class="profile-edit-section">
			<div class="block">
				<h3>
					<svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
						style="fill: #16a34a; margin-right: 8px;">
						<path
							d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
					</svg>
					Update Information
				</h3>

				<form action="" method="post" class="form form-edit-profile" id="editProfileForm">

					<label class="form-label" for="username">Username</label>
					<div class="form-group">
						<svg class="form-icon-left" width="14" height="14" xmlns="http://www.w3.org/2000/svg"
							viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
							<path
								d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z" />
						</svg>
						<input class="form-input" type="text" name="username" placeholder="Username" id="username"
							value="<?= htmlspecialchars($account['username'], ENT_QUOTES) ?>" required>
					</div>

					<label class="form-label" for="email">Email Address</label>
					<div class="form-group">
						<svg class="form-icon-left" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
							viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
							<path
								d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z" />
						</svg>
						<input class="form-input" type="email" name="email" placeholder="Email" id="email"
							value="<?= htmlspecialchars($account['email'], ENT_QUOTES) ?>" required>
					</div>

					<div class="personal-info-section">
						<h4 style="margin: 20px 0 10px 0; color: #6c757d; font-size: 16px;">Personal Information</h4>

						<label class="form-label" for="first_name">First Name</label>
						<div class="form-group">
							<svg class="form-icon-left" width="14" height="14" xmlns="http://www.w3.org/2000/svg"
								viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
								<path
									d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z" />
							</svg>
							<input class="form-input" type="text" name="first_name" placeholder="First Name" id="first_name"
								value="<?= htmlspecialchars($account['first_name'], ENT_QUOTES) ?>">
						</div>

						<label class="form-label" for="last_name">Last Name</label>
						<div class="form-group">
							<svg class="form-icon-left" width="14" height="14" xmlns="http://www.w3.org/2000/svg"
								viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
								<path
									d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z" />
							</svg>
							<input class="form-input" type="text" name="last_name" placeholder="Last Name" id="last_name"
								value="<?= htmlspecialchars($account['last_name'], ENT_QUOTES) ?>">
						</div>

						<label class="form-label" for="phone">Phone Number</label>
						<div class="form-group">
							<svg class="form-icon-left" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
								viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
								<path
									d="M164.9 24.6c-7.7-18.6-28-28.5-47.4-23.2l-88 24C12.1 30.2 0 46 0 64C0 311.4 200.6 512 448 512c18 0 33.8-12.1 38.6-29.5l24-88c5.3-19.4-4.6-39.7-23.2-47.4l-96-40c-16.3-6.8-35.2-2.1-46.3 11.6L304.7 368C234.3 334.7 177.3 277.7 144 207.3l49.4-40.4c13.7-11.1 18.4-30 11.6-46.3l-40-96z" />
							</svg>
							<input class="form-input" type="tel" name="phone" placeholder="Phone Number" id="phone"
								value="<?= htmlspecialchars($account['phone'], ENT_QUOTES) ?>">
						</div>
					</div>

					<div class="address-section">
						<h4 style="margin: 20px 0 10px 0; color: #6c757d; font-size: 16px;">Address Information</h4>

						<label class="form-label" for="address_street">Street Address</label>
						<div class="form-group">
							<svg class="form-icon-left" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
								viewBox="0 0 576 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
								<path
									d="M575.8 255.5c0 18-15 32.1-32 32.1h-32l.7 160.2c0 2.7-.2 5.4-.5 8.1V472c0 22.1-17.9 40-40 40H456c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1H416 392c-22.1 0-40-17.9-40-40V448 384c0-17.7-14.3-32-32-32H256c-17.7 0-32 14.3-32 32v64 24c0 22.1-17.9 40-40 40H160 128.1c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2H104c-22.1 0-40-17.9-40-40V360c0-.9 0-1.9 .1-2.8V287.6H32c-18 0-32.1-14-32.1-32.1c0-9 3.8-17.4 10.4-23.4l320-320.5c13-13 34-13 47 0l320 320.5c6.6 6 10.4 14.4 10.4 23.4z" />
							</svg>
							<input class="form-input" type="text" name="address_street" placeholder="Street Address"
								id="address_street" value="<?= htmlspecialchars($account['address_street'], ENT_QUOTES) ?>">
						</div>

						<div class="address-row" style="display: flex; gap: 15px;">
							<div style="flex: 2;">
								<label class="form-label" for="address_city">City</label>
								<div class="form-group">
									<input class="form-input" type="text" name="address_city" placeholder="City"
										id="address_city"
										value="<?= htmlspecialchars($account['address_city'], ENT_QUOTES) ?>">
								</div>
							</div>
							<div style="flex: 1;">
								<label class="form-label" for="address_state">State</label>
								<div class="form-group">
									<input class="form-input" type="text" name="address_state" placeholder="State"
										id="address_state"
										value="<?= htmlspecialchars($account['address_state'], ENT_QUOTES) ?>">
								</div>
							</div>
							<div style="flex: 1;">
								<label class="form-label" for="address_zip">ZIP Code</label>
								<div class="form-group">
									<input class="form-input" type="text" name="address_zip" placeholder="ZIP Code"
										id="address_zip"
										value="<?= htmlspecialchars($account['address_zip'], ENT_QUOTES) ?>">
								</div>
							</div>
						</div>

						<label class="form-label" for="address_country">Country</label>
						<div class="form-group">
							<svg class="form-icon-left" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
								viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
								<path
									d="M57.7 193l9.4 16.4c8.3 14.5 21.9 25.2 38 29.8L126 256.1c11.4 3.5 19.8 14.1 19.8 26.6V288c0 17.7-14.3 32-32 32H96c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32c53 0 96-43 96-96v-3.6c7.9 12.5 20.3 22 34.7 26.5c9.8 3.1 18.9 7.7 26.8 13.5c16.3 12.1 32.8 22.7 50.1 31.8c3.4 1.8 6.9 3.5 10.4 5.2c9.2 4.3 19.3 6.6 29.6 6.6c26.5 0 48-21.5 48-48V352c0-17.7-14.3-32-32-32H384c-8.8 0-16-7.2-16-16V272c0-44.2 35.8-80 80-80h16c17.7 0 32-14.3 32-32V128c0-17.7-14.3-32-32-32H336c-26.5 0-48 21.5-48 48v64c0 8.8-7.2 16-16 16H224c-8.8 0-16-7.2-16-16v-16c0-35.3-28.7-64-64-64H112c-8.8 0-16 7.2-16 16v32c0 26.5 21.5 48 48 48h32c8.8 0 16 7.2 16 16z" />
							</svg>
							<input class="form-input" type="text" name="address_country" placeholder="Country"
								id="address_country"
								value="<?= htmlspecialchars($account['address_country'], ENT_QUOTES) ?>">
						</div>
					</div>

					<div class="newsletter-section">
						<h4 style="margin: 20px 0 10px 0; color: #6c757d; font-size: 16px;">Newsletter Subscription</h4>

						<div class="form-group checkbox-group"
							style="flex-direction: row; align-items: center; margin-bottom: 10px;">
							<input type="checkbox" name="blog_newsletter" id="blog_newsletter"
								<?= $is_subscribed_to_newsletter ? 'checked' : '' ?>
								style="width: 18px; height: 18px; margin-right: 10px; accent-color: #2a77eb;">
							<label for="blog_newsletter"
								style="margin: 0; font-weight: 500; color: #474b50; cursor: pointer;">
								Subscribe to blog newsletter
							</label>
						</div>
						<p style="font-size: 16px; color: #6c757d; margin: 0 0 15px 0;">
							Receive updates about new blog posts and announcements via email.
						</p>
					</div>

					<div class="password-section">
						<h4 style="margin: 20px 0 10px 0; color: #6c757d; font-size: 16px;">Change Password (Optional)</h4>

						<label class="form-label" for="npassword">New Password</label>
						<div class="form-group">
							<svg class="form-icon-left" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
								viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
								<path
									d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z" />
							</svg>
							<input class="form-input" type="password" name="npassword"
								placeholder="Leave blank to keep current password" id="npassword"
								autocomplete="new-password">
						</div>

						<label class="form-label" for="cpassword">Confirm New Password</label>
						<div class="form-group">
							<svg class="form-icon-left" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
								viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
								<path
									d="M144 144v48H304V144c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192V144C80 64.5 144.5 0 224 0s144 64.5 144 144v48h16c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V256c0-35.3 28.7-64 64-64H80z" />
							</svg>
							<input class="form-input" type="password" name="cpassword" placeholder="Confirm new password"
								id="cpassword" autocomplete="new-password">
						</div>
					</div>

					<?php if ($error_msg): ?>
						<div class="msg error">
							<?= $error_msg ?>
						</div>
					<?php elseif ($success_msg): ?>
						<div class="msg success">
							<?= $success_msg ?>
						</div>
					<?php endif; ?>

					<div class="form-actions">
						<button class="btn blue" type="submit">
							<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
								style="fill: currentColor; margin-right: 8px;">
								<path
									d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V173.3c0-17-6.7-33.3-18.7-45.3L352 50.7C340 38.7 323.7 32 306.7 32H64zm0 96c0-17.7 14.3-32 32-32H288c17.7 0 32 14.3 32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V128zM224 288a64 64 0 1 1 0 128 64 64 0 1 1 0-128z" />
							</svg>
							Save Changes
						</button>
						<a href="profile.php" class="btn alt">
							<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
								style="fill: currentColor; margin-right: 8px;">
								<path
									d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 288 480 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-370.7 0 73.4-73.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-128 128z" />
							</svg>
							Cancel
						</a>
					</div>

				</form>
			</div>
		</div>
	</div>

<?php endif; ?>

<?= template_footer() ?>