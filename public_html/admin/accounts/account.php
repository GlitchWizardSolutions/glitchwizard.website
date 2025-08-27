<?php
/* 
 * Account Creation and Management Interface
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: account.php
 * LOCATION: /public_html/admin/accounts/
 * PURPOSE: Create new accounts and edit existing account details with comprehensive profile management
 * 
 * FILE RELATIONSHIP:
 * This file integrates with:
 * - User authentication system
 * - Avatar management system
 * - Document folder structure
 * - Role management system
 * - Access control system
 * 
 * HOW IT WORKS:
 * 1. Handles both create and edit modes for accounts
 * 2. Manages profile fields and validation
 * 3. Processes avatar assignments based on roles
 * 4. Creates and manages document folders
 * 5. Implements security measures for data handling
 * 
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: YES
 * 
 * FEATURES:
 * - Full profile field management
 * - Role-based avatar assignment
 * - Access level control
 * - Document path handling
 * - Form validation
 * - ARIA compliance
 * - Password security
 * - Input sanitization
 * - Error handling
 */
 
include_once '../assets/includes/main.php';

// Add skip link for accessibility
echo '<a href="#main-form" class="skip-link" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;background:#fff;color:#007bff;padding:8px 16px;z-index:1000;" onfocus="this.style.left=\'8px\';this.style.top=\'8px\';this.style.width=\'auto\';this.style.height=\'auto\';">Skip to account form</a>';

// Default input account values
$account = [
    'username' => '',
    'password' => '',
    'email' => '',
    'activation_code' => 'activated',
    'role' => 'Member',
    'access_level' => 'Member',
    'document_path' => '',
    'approved' => 1,
    'first_name' => '',
    'last_name' => '',
    'full_name' => '',
    'phone' => '',
    'address_street' => '',
    'address_city' => '',
    'address_state' => '',
    'address_zip' => '',
    'address_country' => 'United States',
    'avatar' => ''
];
// If editing an account
if (isset($_GET['id']))
{
    // Get the account from the database
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing account
    $page = 'Edit';
    if (isset($_POST['submit']))
    {
        // Check to see if username already exists
        $stmt = $pdo->prepare('SELECT id FROM accounts WHERE username = ? AND username != ?');
        $stmt->execute([$_POST['username'], $account['username']]);
        if ($stmt->fetch(PDO::FETCH_ASSOC))
        {
            $error_msg = 'Username already exists!';
        }
        // Check to see if email already exists
        $stmt = $pdo->prepare('SELECT id FROM accounts WHERE email = ? AND email != ?');
        $stmt->execute([$_POST['email'], $account['email']]);
        if ($stmt->fetch(PDO::FETCH_ASSOC))
        {
            $error_msg = 'Email already exists!';
        }
        // Update the account
        if (!isset($error_msg))
        {
            $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $account['password'];
            $activation_code = $_POST['activation_status'] == 'activated' || $_POST['activation_status'] == 'deactivated' ? $_POST['activation_status'] : $_POST['activation_code'];

            // If role changed and current avatar is a default avatar, update to new role's default avatar
            $avatar = $account['avatar'];
            $default_avatars = ['default-developer.svg', 'default-admin.svg', 'default-editor.svg', 'default-blog.svg', 'default-member.svg', 'default-guest.svg', 'default-demo.svg', 'default-user.svg'];

            // Only update avatar if:
            // 1. Role has changed AND
            // 2. Current avatar is one of the default avatars (not a custom upload) AND  
            // 3. Current avatar is not empty/null
            if (
                $_POST['role'] !== $account['role'] &&
                !empty($account['avatar']) &&
                in_array($account['avatar'], $default_avatars)
            )
            {
                // Role changed and current avatar is a default, assign new default based on new role
                switch (strtolower($_POST['role']))
                {
                    case 'developer':
                        $avatar = 'default-developer.svg';
                        break;
                    case 'admin':
                        $avatar = 'default-admin.svg';
                        break;
                    case 'editor':
                        $avatar = 'default-editor.svg';
                        break;
                    case 'blog_only':
                    case 'blog user':
                        $avatar = 'default-blog.svg';
                        break;
                    case 'member':
                        $avatar = 'default-member.svg';
                        break;
                    case 'guest':
                        $avatar = 'default-guest.svg';
                        break;
                    case 'demo':
                        $avatar = 'default-demo.svg';
                        break;
                    default:
                        $avatar = 'default-user.svg';
                }
            }
            // If current avatar is empty/null, assign default based on role
            elseif (empty($account['avatar']))
            {
                switch (strtolower($_POST['role']))
                {
                    case 'developer':
                        $avatar = 'default-developer.svg';
                        break;
                    case 'admin':
                        $avatar = 'default-admin.svg';
                        break;
                    case 'editor':
                        $avatar = 'default-editor.svg';
                        break;
                    case 'blog_only':
                    case 'blog user':
                        $avatar = 'default-blog.svg';
                        break;
                    case 'member':
                        $avatar = 'default-member.svg';
                        break;
                    case 'guest':
                        $avatar = 'default-guest.svg';
                        break;
                    case 'demo':
                        $avatar = 'default-demo.svg';
                        break;
                    default:
                        $avatar = 'default-user.svg';
                }
            }

            // Create full_name from first_name and last_name
            $full_name = trim(($_POST['first_name'] ?? '') . ' ' . ($_POST['last_name'] ?? ''));
            $full_name = $full_name ?: 'None Provided';

            $stmt = $pdo->prepare('UPDATE accounts SET username = ?, password = ?, email = ?, activation_code = ?, role = ?, access_level = ?, document_path = ?, approved = ?, first_name = ?, last_name = ?, full_name = ?, phone = ?, address_street = ?, address_city = ?, address_state = ?, address_zip = ?, address_country = ?, avatar = ? WHERE id = ?');
            $stmt->execute([
                $_POST['username'],
                $password,
                $_POST['email'],
                $activation_code,
                $_POST['role'],
                $_POST['access_level'] ?? 'Member',
                $_POST['document_path'] ?? '',
                $_POST['approved'],
                $_POST['first_name'] ?? '',
                $_POST['last_name'] ?? '',
                $full_name,
                $_POST['phone'] ?? '',
                $_POST['address_street'] ?? '',
                $_POST['address_city'] ?? '',
                $_POST['address_state'] ?? '',
                $_POST['address_zip'] ?? '',
                $_POST['address_country'] ?? 'United States',
                $avatar,
                $_GET['id']
            ]);
            header('Location: accounts.php?success_msg=2');
            exit;
        } else
        {
            // Update the account variables
            $account = [
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'email' => $_POST['email'],
                'activation_code' => $_POST['activation_code'],
                'role' => $_POST['role'],
                'access_level' => $_POST['access_level'] ?? 'Member',
                'document_path' => $_POST['document_path'] ?? '',
                'approved' => $_POST['approved'],
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'full_name' => $_POST['full_name'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address_street' => $_POST['address_street'] ?? '',
                'address_city' => $_POST['address_city'] ?? '',
                'address_state' => $_POST['address_state'] ?? '',
                'address_zip' => $_POST['address_zip'] ?? '',
                'address_country' => $_POST['address_country'] ?? 'United States',
                'avatar' => $account['avatar'] // Preserve existing avatar on error
            ];
        }
    }
    if (isset($_POST['delete']))
    {
        // Redirect and delete the account
        header('Location: accounts.php?delete=' . $_GET['id']);
        exit;
    }
} else
{
    // Create a new account
    $page = 'Create';
    if (isset($_POST['submit']))
    {
        // Check to see if username already exists
        $stmt = $pdo->prepare('SELECT id FROM accounts WHERE username = ?');
        $stmt->execute([$_POST['username']]);
        if ($stmt->fetch(PDO::FETCH_ASSOC))
        {
            $error_msg = 'Username already exists!';
        }
        // Check to see if email already exists
        $stmt = $pdo->prepare('SELECT id FROM accounts WHERE email = ?');
        $stmt->execute([$_POST['email']]);
        if ($stmt->fetch(PDO::FETCH_ASSOC))
        {
            $error_msg = 'Email already exists!';
        }
        // Insert the account
        if (!isset($error_msg))
        {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $activation_code = $_POST['activation_status'] == 'activated' || $_POST['activation_status'] == 'deactivated' ? $_POST['activation_status'] : $_POST['activation_code'];

            // Assign default avatar based on role
            $default_avatar = '';
            switch (strtolower($_POST['role']))
            {
                case 'developer':
                    $default_avatar = 'default-developer.svg';
                    break;
                case 'admin':
                    $default_avatar = 'default-admin.svg';
                    break;
                case 'editor':
                    $default_avatar = 'default-editor.svg';
                    break;
                case 'blog_only':
                case 'blog user':
                    $default_avatar = 'default-blog.svg';
                    break;
                case 'member':
                    $default_avatar = 'default-member.svg';
                    break;
                case 'guest':
                    $default_avatar = 'default-guest.svg';
                    break;
                case 'demo':
                    $default_avatar = 'default-demo.svg';
                    break;
                default:
                    $default_avatar = 'default-user.svg';
            }

            // Create full_name from first_name and last_name
            $full_name = trim(($_POST['first_name'] ?? '') . ' ' . ($_POST['last_name'] ?? ''));
            $full_name = $full_name ?: 'None Provided';

            // Generate folder name from first and last name
            $first_name_clean = trim($_POST['first_name'] ?? '');
            $last_name_clean = trim($_POST['last_name'] ?? '');
            $folder_name = '';

            if (!empty($first_name_clean) && !empty($last_name_clean))
            {
                // Create folder name as FirstName_LastName
                $folder_name = $first_name_clean . '_' . $last_name_clean . '/';
            } else
            {
                // Fallback to username if no names provided
                $folder_name = $_POST['username'] . '/';
            }

            $stmt = $pdo->prepare('INSERT IGNORE INTO accounts (username,password,email,activation_code,role,access_level,document_path,approved,first_name,last_name,full_name,phone,address_street,address_city,address_state,address_zip,address_country,avatar,registered,last_seen) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())');
            $stmt->execute([
                $_POST['username'],
                $password,
                $_POST['email'],
                $activation_code,
                $_POST['role'],
                $_POST['access_level'] ?? 'Member',
                $folder_name, // Store just the folder name, not full path
                $_POST['approved'],
                $_POST['first_name'] ?? '',
                $_POST['last_name'] ?? '',
                $full_name,
                $_POST['phone'] ?? '',
                $_POST['address_street'] ?? '',
                $_POST['address_city'] ?? '',
                $_POST['address_state'] ?? '',
                $_POST['address_zip'] ?? '',
                $_POST['address_country'] ?? 'United States',
                $default_avatar
            ]);

            // Create the actual folder structure
            $documents_base_path = dirname(__DIR__, 2) . '/documents_system/account_documents/';
            $user_folder_path = $documents_base_path . rtrim($folder_name, '/');

            if (!file_exists($user_folder_path))
            {
                if (mkdir($user_folder_path, 0755, true))
                {
                    // Successfully created folder
                    error_log("Created user document folder: " . $user_folder_path);
                } else
                {
                    // Log error but don't stop account creation
                    error_log("Failed to create user document folder: " . $user_folder_path);
                }
            }

            header('Location: accounts.php?success_msg=1');
            exit;
        } else
        {
            // Update the account variables  
            $account = [
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'email' => $_POST['email'],
                'activation_code' => $_POST['activation_code'],
                'role' => $_POST['role'],
                'access_level' => $_POST['access_level'] ?? 'Member',
                'document_path' => $_POST['document_path'] ?? '',
                'approved' => $_POST['approved'],
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'full_name' => $_POST['full_name'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'address_street' => $_POST['address_street'] ?? '',
                'address_city' => $_POST['address_city'] ?? '',
                'address_state' => $_POST['address_state'] ?? '',
                'address_zip' => $_POST['address_zip'] ?? '',
                'address_country' => $_POST['address_country'] ?? 'United States',
                'avatar' => '' // No avatar for new account on error
            ];
        }
    }
}
?>
<?= template_admin_header($page . ' Account', 'accounts', 'manage') ?>

<div class="content-title" id="main-account-form" role="banner" aria-label="<?=$page?> Account Header">
    <div class="title  mb-4">
        <div class="icon">
            <?php if ($page == 'Edit'): ?>
                <img src="<?= getUserAvatar($account) ?>" 
                    alt="<?= htmlspecialchars($account['username'], ENT_QUOTES) ?> profile avatar"
                    style="width: 18px; height: 18px; border-radius: 50%; object-fit: cover;" />
            <?php else: ?>
                <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z"/>
                </svg>
            <?php endif; ?>
        </div>
        <div class="txt">
            <h2><?= $page ?> Account</h2>
            <p><?= $page == 'Edit' ? 'Modify account details and permissions.' : 'Create a new user account with appropriate permissions.' ?></p>
        </div>
    </div>
</div>

<form action="" 
    method="post" 
    enctype="multipart/form-data" 
    id="main-form"
    role="form" 
    aria-labelledby="form-title"
    aria-describedby="form-description">

    <!-- Top form actions -->
    <div class="d-flex gap-2 mb-4" role="region" aria-label="Form Actions">
        <a href="accounts.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
            Cancel
        </a>
        <button type="submit" name="submit" class="btn btn-success">
            <i class="fas fa-save me-1" aria-hidden="true"></i>
            Save Account
        </button>
        <?php if ($page == 'Edit'): ?>
            <button type="submit" name="delete" class="btn btn-danger"
                onclick="return confirm('Are you sure you want to delete this account? This action cannot be undone.')"
                aria-label="Delete this account permanently">
                <i class="fas fa-trash me-1" aria-hidden="true"></i>
                Delete Account
            </button>
        <?php endif; ?>
    </div>

    <div class="card mb-3">

    <h6 class="card-header"><?= $page == 'Edit' ? 'Edit Account' : 'Create Account' ?></h6>
    <div class="card-body">

            <?php if (isset($error_msg)): ?>
                <div class="mb-4" role="region" aria-label="Error Message">
                    <div class="msg error" role="alert" aria-live="assertive">
                        <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                            aria-hidden="true" focusable="false">
                            <path
                                d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
                        </svg>
                        <p id="error-message"><?= $error_msg ?></p>
                        <button type="button" class="close-error" aria-label="Dismiss error message" onclick="this.parentElement.style.display='none'">
                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"
                                aria-hidden="true" focusable="false">
                                <path
                                    d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                            </svg>
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="content-block">
                <fieldset role="group" aria-labelledby="account-credentials">
                    <legend id="account-credentials">Account Credentials</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <span class="required" aria-hidden="true">*</span> Username
                                    <span class="sr-only">(required)</span>
                                </label>
                                <input type="text" id="username" name="username" class="form-control"
                                    placeholder="Enter unique username"
                                    value="<?= htmlspecialchars($account['username'], ENT_QUOTES) ?>" 
                                    required
                                    aria-required="true" 
                                    aria-describedby="username-hint"
                                    autocomplete="username">
                                <div id="username-hint" class="form-text">Username must be unique and cannot be changed later.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <?= $page == 'Edit' ? 'New ' : '<span class="required" aria-hidden="true">*</span> ' ?>Password
                                    <?= $page == 'Edit' ? '' : '<span class="sr-only">(required)</span>' ?>
                                </label>
                                <input type="password" id="password" name="password" class="form-control"
                                    placeholder="<?= $page == 'Edit' ? 'Leave blank to keep current' : 'Enter secure password' ?>"
                                    aria-describedby="password-hint"
                                    autocomplete="new-password" 
                                    value="" 
                                    <?= $page == 'Edit' ? '' : ' required aria-required="true"' ?>>
                                <div id="password-hint" class="form-text">
                                    <?= $page == 'Edit' ? 'Leave blank to keep current password' : 'Choose a secure password with at least 8 characters' ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <span class="required" aria-hidden="true">*</span> Email
                                    <span class="sr-only">(required)</span>
                                </label>
                                <input type="email" id="email" name="email" class="form-control" 
                                    placeholder="Enter email address"
                                    value="<?= htmlspecialchars($account['email'], ENT_QUOTES) ?>" 
                                    required
                                    aria-required="true" 
                                    aria-describedby="email-hint"
                                    autocomplete="email">
                                <div id="email-hint" class="form-text">Email must be unique and will be used for account notifications.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" 
                                    placeholder="Enter phone number (optional)"
                                    value="<?= htmlspecialchars($account['phone'], ENT_QUOTES) ?>" 
                                    aria-describedby="phone-hint"
                                    autocomplete="tel">
                                <div id="phone-hint" class="form-text">Optional contact number for account verification.</div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset role="group" aria-labelledby="personal-info">
                    <legend id="personal-info">Personal Information</legend>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" id="first_name" name="first_name" class="form-control" 
                                    placeholder="Enter first name"
                                    value="<?= htmlspecialchars($account['first_name'], ENT_QUOTES) ?>"
                                    aria-describedby="name-hint"
                                    autocomplete="given-name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" id="last_name" name="last_name" class="form-control" 
                                    placeholder="Enter last name"
                                    value="<?= htmlspecialchars($account['last_name'], ENT_QUOTES) ?>"
                                    aria-describedby="name-hint"
                                    autocomplete="family-name">
                            </div>
                        </div>
                    </div>
                    <div id="name-hint" class="form-text">Names will be used to create the account's document folder.</div>
                </fieldset>

                <fieldset role="group" aria-labelledby="address-info">
                    <legend id="address-info">Address Information</legend>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="address_street" class="form-label">Street Address</label>
                                <input type="text" id="address_street" name="address_street" class="form-control"
                                    placeholder="Enter street address"
                                    value="<?= htmlspecialchars($account['address_street'], ENT_QUOTES) ?>"
                                    aria-describedby="address-hint"
                                    autocomplete="street-address">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address_city" class="form-label">City</label>
                                <input type="text" id="address_city" name="address_city" class="form-control" 
                                    placeholder="Enter city"
                                    value="<?= htmlspecialchars($account['address_city'], ENT_QUOTES) ?>"
                                    autocomplete="address-level2">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address_state" class="form-label">State</label>
                                <input type="text" id="address_state" name="address_state" class="form-control"
                                    placeholder="Enter state"
                                    value="<?= htmlspecialchars($account['address_state'], ENT_QUOTES) ?>"
                                    autocomplete="address-level1">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address_zip" class="form-label">Zipcode</label>
                                <input type="text" id="address_zip" name="address_zip" class="form-control" 
                                    placeholder="Enter zipcode"
                                    value="<?= htmlspecialchars($account['address_zip'], ENT_QUOTES) ?>"
                                    pattern="[0-9]{5}(-[0-9]{4})?"
                                    aria-describedby="zip-hint"
                                    autocomplete="postal-code">
                                <div id="zip-hint" class="form-text">Format: 12345 or 12345-6789</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address_country" class="form-label">Country</label>
                                <input type="text" id="address_country" name="address_country" class="form-control"
                                    placeholder="Enter country"
                                    value="<?= htmlspecialchars($account['address_country'], ENT_QUOTES) ?>"
                                    autocomplete="country-name">
                            </div>
                        </div>
                    </div>
                    <div id="address-hint" class="form-text">Provide the account holder's mailing address information.</div>
                </fieldset>

                <fieldset role="group" aria-labelledby="account-settings">
                    <legend id="account-settings">Account Settings</legend>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select id="role" name="role" class="form-select" 
                                    aria-describedby="role-hint">
                                    <?php foreach ($roles_list as $role): ?>
                                        <option value="<?= htmlspecialchars($role, ENT_QUOTES) ?>" 
                                            <?= $role == $account['role'] ? ' selected' : '' ?>>
                                            <?= htmlspecialchars($role, ENT_QUOTES) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="role-hint" class="form-text">Determines user's permissions and default avatar.</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="access_level" class="form-label">Access Level</label>
                                <select id="access_level" name="access_level" class="form-select"
                                    aria-describedby="access-hint">
                                    <option value="Member" <?= $account['access_level'] == 'Member' ? ' selected' : '' ?>>Member</option>
                                    <option value="Blog Only" <?= $account['access_level'] == 'Blog Only' ? ' selected' : '' ?>>Blog Only</option>
                                    <option value="Admin" <?= $account['access_level'] == 'Admin' ? ' selected' : '' ?>>Admin</option>
                                    <option value="Editor" <?= $account['access_level'] == 'Editor' ? ' selected' : '' ?>>Editor</option>
                                    <option value="Guest" <?= $account['access_level'] == 'Guest' ? ' selected' : '' ?>>Guest</option>
                                </select>
                                <div id="access-hint" class="form-text">Controls what features the user can access.</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="activation_status" class="form-label">Activation Status</label>
                                <select id="activation_status" name="activation_status" class="form-select"
                                    aria-describedby="activation-hint">
                                    <option value="activated" <?= $account['activation_code'] == 'activated' ? ' selected' : '' ?>>Activated</option>
                                    <option value="deactivated" <?= $account['activation_code'] == 'deactivated' ? ' selected' : '' ?>>Deactivated</option>
                                <option value="pending" <?= $account['activation_code'] != 'activated' && $account['activation_code'] != 'deactivated' ? ' selected' : '' ?>>Pending</option>
                            </select>
                            <div id="activation-hint" class="form-text">Controls whether the user can access their account.</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="approved" class="form-label">Approval Status</label>
                            <select id="approved" name="approved" class="form-select"
                                aria-describedby="approved-hint">
                                <option value="1" <?= $account['approved'] == 1 ? ' selected' : '' ?>>Approved</option>
                                <option value="0" <?= $account['approved'] == 0 ? ' selected' : '' ?>>Not Approved</option>
                            </select>
                            <div id="approved-hint" class="form-text">Determines if the account is approved for use.</div>
                        </div>
                    </div>
                </div>

                <!-- Activation Code (conditional) -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="activation_code mb-3" <?= $account['activation_code'] == 'activated' || $account['activation_code'] == 'deactivated' ? ' aria-hidden="true" style="display: none;"' : '' ?>>
                            <label for="activation_code" class="form-label">Activation Code</label>
                            <input type="text" id="activation_code" name="activation_code" class="form-control"
                                placeholder="Enter activation code"
                                value="<?= htmlspecialchars($account['activation_code'], ENT_QUOTES) ?>"
                                aria-describedby="code-hint">
                            <div id="code-hint" class="form-text">Only visible when account status is 'Pending'.</div>
                        </div>
                    </div>
                </div>

                <!-- Document Storage Settings -->
                <fieldset role="group" aria-labelledby="document-settings">
                    <legend id="document-settings">Document Storage Settings</legend>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-0">
                                <label for="document_path" class="form-label">Document Path</label>
                                <input type="text" id="document_path" name="document_path" class="form-control"
                                    placeholder="Enter document storage path"
                                    value="<?= htmlspecialchars(trim($account['document_path'], '"\''), ENT_QUOTES) ?>"
                                    aria-describedby="path-hint">
                                <div id="path-hint" class="form-text">
                                    Path where user's documents will be stored. Created automatically from user's name if left blank.
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>

        <script>
        // Handle activation code visibility based on status selection
        document.getElementById('activation_status').addEventListener('change', function() {
            const activationCodeDiv = document.querySelector('.activation_code');
            const activationCodeInput = document.getElementById('activation_code');
            
            if (this.value === 'pending') {
                activationCodeDiv.style.display = 'block';
                activationCodeDiv.removeAttribute('aria-hidden');
                activationCodeInput.setAttribute('aria-required', 'true');
            } else {
                activationCodeDiv.style.display = 'none';
                activationCodeDiv.setAttribute('aria-hidden', 'true');
                activationCodeInput.removeAttribute('aria-required');
            }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            const email = document.getElementById('email');
            const username = document.getElementById('username');
            const zip = document.getElementById('address_zip');
            
            // Validate password on new accounts
            if ('<?= $page ?>' !== 'Edit' && password.value.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                password.focus();
                return;
            }

            // Validate email format
            if (email.value && !email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                e.preventDefault();
                alert('Please enter a valid email address.');
                email.focus();
                return;
            }

            // Validate username (alphanumeric and underscore only)
            if (!username.value.match(/^[a-zA-Z0-9_]+$/)) {
                e.preventDefault();
                alert('Username can only contain letters, numbers, and underscores.');
                username.focus();
                return;
            }

            // Validate zip code if provided
            if (zip.value && !zip.value.match(/^\d{5}(-\d{4})?$/)) {
                e.preventDefault();
                alert('Please enter a valid ZIP code (12345 or 12345-6789).');
                zip.focus();
                return;
            }
        });

        // Accessible delete confirmation
        <?php if ($page == 'Edit'): ?>
        document.querySelector('button[name="delete"]').addEventListener('click', function(e) {
            e.preventDefault();
            const dialog = document.createElement('div');
            dialog.role = 'dialog';
            dialog.setAttribute('aria-labelledby', 'dialog-title');
            dialog.setAttribute('aria-describedby', 'dialog-content');
            dialog.innerHTML = `
                <div class="dialog-overlay">
                    <div class="dialog-content">
                        <h2 id="dialog-title">Confirm Deletion</h2>
                        <p id="dialog-content">Are you sure you want to delete this account? This action cannot be undone.</p>
                        <div class="dialog-buttons">
                            <button class="btn btn-outline-secondary" onclick="this.closest('.dialog-overlay').remove()">
                                <i class="fas fa-times me-1" aria-hidden="true"></i>
                                Cancel
                            </button>
                            <button class="btn btn-danger" onclick="document.getElementById('delete-form').submit()">
                                <i class="fas fa-trash me-1" aria-hidden="true"></i>
                                Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(dialog);
            dialog.querySelector('button').focus();
        });
        <?php endif; ?>
        </script>

        <style>
        .dialog-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .dialog-content {
            background: white;
            padding: 20px;
            border-radius: 4px;
            max-width: 500px;
            width: 90%;
        }

        .dialog-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        /* Accessibility enhancements */
        .form-control:focus,
        .form-select:focus,
        .btn:focus {
            outline: 2px solid #007bff;
            outline-offset: 2px;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .required {
            color: #dc3545;
            font-weight: bold;
        }

        .form-text {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        fieldset {
            margin-bottom: 2rem;
            padding: 0;
            border: none;
        }

        legend {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding: 0;
            border-bottom: 2px solid #e9ecef;
            width: 100%;
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                transition: none !important;
            }
        }
        </style>
         <!-- Bottom form actions -->
        <div class="d-flex gap-2 pt-3 border-top mt-4 mx-3" role="region" aria-label="Form Actions">
            <a href="accounts.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
                Cancel
            </a>
            <button type="submit" name="submit" class="btn btn-success">
                <i class="fas fa-save me-1" aria-hidden="true"></i>
                Save Account
            </button>
            <?php if ($page == 'Edit'): ?>
                <button type="submit" name="delete" class="btn btn-danger"
                    onclick="return confirm('Are you sure you want to delete this account? This action cannot be undone.')"
                    aria-label="Delete this account permanently">
                    <i class="fas fa-trash me-1" aria-hidden="true"></i>
                    Delete Account
                </button>
            <?php endif; ?>
        </div>
</form>

<style>
    /* Accessibility: Screen reader only content */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    /* Enhanced contrast for accessibility */
    .required {
        color: #dc3545;
        font-weight: bold;
    }

    /* Focus styles for better accessibility */
    input:focus,
    select:focus {
        outline: 2px solid #007bff;
        outline-offset: 2px;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }

    .btn.alt {
        background-color: #f8f9fa;
        color: #6c757d;
        border: 1px solid #6c757d;
        transition: all 0.2s ease;
    }

    .btn.alt:hover {
        background-color: #e9ecef;
        border-color: #5a6268;
        color: #5a6268;
    }

    .btn.green {
        background-color: #28a745;
        color: white;
        border: 1px solid #28a745;
    }

    .btn.green:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .btn.red {
        background-color: #dc3545;
        color: white;
        border: 1px solid #dc3545;
    }

    .btn.red:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }
</style>

<script>
    document.getElementById('activation_status').addEventListener('change', function () {
        if (this.value == 'activated' || this.value == 'deactivated') {
            document.querySelector('.activation_code').style.display = 'none';
            document.querySelector('#activation_code').value = this.value;
        } else {
            document.querySelector('.activation_code').style.display = 'block';
            document.querySelector('#activation_code').value = '';
            document.querySelector('#activation_code').focus();
        }
    });

    // Auto-populate full name when first/last name changes (like in profile.php)
    function updateFullName() {
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const fullName = (firstName + ' ' + lastName).trim() || 'None Provided';

        // Store full name for form submission (hidden field will be added if needed)
        if (!document.getElementById('full_name_hidden')) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'full_name';
            hiddenInput.id = 'full_name_hidden';
            document.querySelector('form').appendChild(hiddenInput);
        }
        document.getElementById('full_name_hidden').value = fullName;
    }

    // Add event listeners for name fields
    document.getElementById('first_name').addEventListener('input', updateFullName);
    document.getElementById('last_name').addEventListener('input', updateFullName);

    // Update full name on page load
    document.addEventListener('DOMContentLoaded', updateFullName);
</script>

<?= template_admin_footer() ?>