<?php
include 'functions.php';
// Output messages
$login_msg = '';
$register_msg = '';
// Check whether the user us logged in or not
if (isset($_SESSION['account_loggedin'])) {
	// Retrieve the user's collections
	$stmt = $pdo->prepare('SELECT c.*, (SELECT COUNT(*) FROM media_collections mc JOIN media m ON m.id = mc.media_id AND m.is_approved = 1 WHERE mc.collection_id = c.id) AS total_media FROM collections c WHERE c.acc_id = ? ORDER BY c.title');
	$stmt->execute([ $_SESSION['account_id'] ]);
	$collections = $stmt->fetchAll(PDO::FETCH_ASSOC);
	// Retrieve likes
	$stmt = $pdo->prepare('SELECT COUNT(*) FROM media_likes WHERE acc_id = ?');
	$stmt->execute([ $_SESSION['account_id'] ]);
	$total_media_likes = $stmt->fetchColumn();	
	// Retrieve total images
	$stmt = $pdo->prepare('SELECT COUNT(*) FROM media WHERE media_type = "image" AND is_approved = 1 AND acc_id = ?');
	$stmt->execute([ $_SESSION['account_id'] ]);
	$total_images = $stmt->fetchColumn();	
	// Retrieve total audios
	$stmt = $pdo->prepare('SELECT COUNT(*) FROM media WHERE media_type = "audio" AND is_approved = 1 AND acc_id = ?');
	$stmt->execute([ $_SESSION['account_id'] ]);
	$total_audios = $stmt->fetchColumn();	
	// Retrieve total videos
	$stmt = $pdo->prepare('SELECT COUNT(*) FROM media WHERE media_type = "video" AND is_approved = 1 AND acc_id = ?');
	$stmt->execute([ $_SESSION['account_id'] ]);
	$total_videos = $stmt->fetchColumn();	
} else {
	// Login form: Authenticate the user
	if (isset($_POST['login'], $_POST['email'], $_POST['password'])) {
		// Retrieve the account associated with the captured email
		$stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ?');
		$stmt->execute([ $_POST['email'] ]);
		$account = $stmt->fetch(PDO::FETCH_ASSOC);
		// Validate password
		if ($account && password_verify($_POST['password'], $account['password'])) {
			// Declare session data
			$_SESSION['account_loggedin'] = true;
			$_SESSION['account_id'] = $account['id'];
			$_SESSION['account_role'] = $account['role'];
			$_SESSION['account_name'] = $account['display_name'];
			// Redirect to collections page
			header('Location: collections.php');
			exit;
		} else {
			// Ouput login error
			$login_msg = 'Incorrect email and/or password!';
		}
	}
	// Registration form: Register new user
	if (isset($_POST['register'], $_POST['display_name'], $_POST['email'], $_POST['password'])) {
		// Make sure the submitted registration values are not empty.
		if (empty($_POST['display_name']) || empty($_POST['password']) || empty($_POST['email'])) {
			// One or more values are empty.
			$register_msg = 'Please complete the registration form!';
		} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			$register_msg = 'Please provide a valid email address!';
		} else if (!preg_match('/^[a-zA-Z0-9 ]+$/', $_POST['display_name'])) {
			$register_msg = 'Display name must contain only letters and numbers!';
		} else if (strlen($_POST['display_name']) > 20 || strlen($_POST['display_name']) < 3) {
			$register_msg = 'Display name must be between 3 and 20 characters long!';
		} else if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
			$register_msg = 'Password must be between 5 and 20 characters long!';
		} else {
			// Check if the account with that email already exists
			$stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ?');
			$stmt->execute([ $_POST['email'] ]);
			$account = $stmt->fetch(PDO::FETCH_ASSOC);
			// Store the result, so we can check if the account exists in the database.
			if ($account) {
				// Email already exists
				$register_msg = 'Email already exists!';
			} else {
				// Email doesn't exist, insert new account
				// We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
				$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
				// Prepare query; prevents SQL injection
				$stmt = $pdo->prepare('INSERT INTO accounts (email, password, display_name) VALUES (?, ?, ?)');
				$stmt->execute([ $_POST['email'], $password, $_POST['display_name'] ]);
				// Output response
				$register_msg = 'You have successfully registered!';
			}
		}
	}	
}
?>
<?=template_header(!isset($_SESSION['account_loggedin']) ? 'Login' : 'My Collections')?>

<?php if (!isset($_SESSION['account_loggedin'])): ?>

<div class="page-content">

	<div class="login-register">

		<form action="collections.php" method="post" class="auth-form">

			<div class="page-title">
				<h2>Login</h2>
			</div>

			<label for="email">Email</label>
			<input id="email" name="email" type="email" placeholder="Email" required>

			<label for="password">Password</label>
			<input id="password" name="password" type="password" placeholder="Password" required>

			<div class="btn-wrapper">
				<button type="submit" name="login" class="btn">Login</button>
				<div class="result"><?=$login_msg?></div>
			</div>

		</form>

		<form action="collections.php" method="post" class="auth-form" autocomplete="off">
			
			<div class="page-title">
				<h2>Register</h2>
			</div>

			<label for="display_name">Display Name</label>
			<input id="display_name" name="display_name" type="text" placeholder="Display Name" pattern="[A-Za-z0-9 ]+" required>

			<label for="email2">Email</label>
			<input id="email2" name="email" type="email" placeholder="Email" required>

			<label for="password2">Password</label>
			<input id="password2" name="password" type="password" placeholder="Password" required autocomplete="new-password">

			<div class="btn-wrapper">
				<button type="submit" name="register" class="btn">Register</button>
				<div class="result"><?=$register_msg?></div>
			</div>

		</form>

	</div>

</div>

<?php else: ?>
<div class="page-content collections">
	<div class="page-title">
		<h2>My Collections</h2>
	</div>
	<div class="media-list-filters" style="padding:0">
		<a href="manage-collection.php" class="btn">
			<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/></svg>	
			Create Collection
		</a>
	</div>
	<div class="collection-list">
		<?php foreach ($collections as $collection): ?>
		<a href="collection.php?collection=<?=$collection['id']?>">
			<svg width="60" height="60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V8C22,6.89 21.1,6 20,6H12L10,4Z" /></svg>
			<span class="title"><?=htmlspecialchars($collection['title'], ENT_QUOTES)?></span>
			<span class="num"><?=number_format($collection['total_media'])?> Files</span>
		</a>
		<?php endforeach; ?>
		<a href="collection.php?type=image">
			<svg width="55" height="55" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M448 80c8.8 0 16 7.2 16 16l0 319.8-5-6.5-136-176c-4.5-5.9-11.6-9.3-19-9.3s-14.4 3.4-19 9.3L202 340.7l-30.5-42.7C167 291.7 159.8 288 152 288s-15 3.7-19.5 10.1l-80 112L48 416.3l0-.3L48 96c0-8.8 7.2-16 16-16l384 0zM64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zm80 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/></svg>
			<span class="title">Images</span>
			<span class="num"><?=number_format($total_images)?> Files</span>
		</a>
		<a href="collection.php?type=video">
			<svg width="55" height="55" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM48 368l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 240l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 112l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16L64 96c-8.8 0-16 7.2-16 16zM416 96c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM160 128l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32L192 96c-17.7 0-32 14.3-32 32zm32 160c-17.7 0-32 14.3-32 32l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32l-128 0z"/></svg>
			<span class="title">Videos</span>
			<span class="num"><?=number_format($total_videos)?> Files</span>
		</a>
		<a href="collection.php?type=audio">
			<svg width="55" height="55" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M499.1 6.3c8.1 6 12.9 15.6 12.9 25.7l0 72 0 264c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L448 147 192 223.8 192 432c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L128 200l0-72c0-14.1 9.3-26.6 22.8-30.7l320-96c9.7-2.9 20.2-1.1 28.3 5z"/></svg>
			<span class="title">Audios</span>
			<span class="num"><?=number_format($total_audios)?> Files</span>
		</a>
		<a href="collection.php?view=likes">
			<svg width="60" height="60" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z" /></svg>
			<span class="title">Likes</span>
			<span class="num"><?=number_format($total_media_likes)?> Files</span>
		</a>
	</div>
</div>
<?php endif; ?>

<?=template_footer()?>