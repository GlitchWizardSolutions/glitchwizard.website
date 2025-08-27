<?php
// Shop Authentication Page: Login, Register with shop system integration

// Robust config and main include logic (copied from auth.php)
$main_paths = [
    __DIR__ . '/accounts_system/main.php',
    __DIR__ . '/assets/includes/main.php',
    __DIR__ . '/main.php'
];
$main_found = false;
foreach ($main_paths as $main_path)
{
    if (file_exists($main_path))
    {
        include $main_path;
        $main_found = true;
        break;
    }
}
if (!$main_found)
{
    die('Critical error: Could not locate main.php');
}

// Load public settings for site branding
// Note: Settings now loaded from database via database_settings.php system
require_once __DIR__ . '/assets/includes/settings/database_settings.php';

// Set page variables for template
$page_title = 'Login / Register - ' . $business_name;
$current_page = 'shop-auth.php';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (!empty($username) && !empty($password)) {
        try {
            // Check accounts table for login
            $stmt = $pdo->prepare('SELECT id, username, email, password, role, is_active FROM accounts WHERE (username = ? OR email = ?) AND is_active = 1 LIMIT 1');
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['is_logged_in'] = true;
                
                // Handle remember me
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    $expires = time() + (30 * 24 * 60 * 60); // 30 days
                    
                    // Store remember token in database
                    $stmt = $pdo->prepare('UPDATE accounts SET remember_token = ?, remember_expires = ? WHERE id = ?');
                    $stmt->execute([$token, date('Y-m-d H:i:s', $expires), $user['id']]);
                    
                    // Set remember cookie
                    setcookie('remember_token', $token, $expires, '/', '', false, true);
                }
                
                // Role-based redirect
                $redirect_url = 'shop.php'; // Default to shop
                if ($user['role'] === 'Blog_User') {
                    $redirect_url = 'blog.php';
                } elseif ($user['role'] === 'Admin' || $user['role'] === 'Manager') {
                    $redirect_url = 'client_portal/';
                }
                
                // Check for return URL
                if (isset($_GET['return']) && !empty($_GET['return'])) {
                    $return_url = urldecode($_GET['return']);
                    // Security check - only allow local URLs
                    if (strpos($return_url, 'http') !== 0 && strpos($return_url, '//') !== 0) {
                        $redirect_url = $return_url;
                    }
                }
                
                header('Location: ' . $redirect_url);
                exit;
            } else {
                $login_error = 'Invalid username/email or password.';
            }
        } catch (PDOException $e) {
            $login_error = 'Login failed. Please try again.';
        }
    } else {
        $login_error = 'Please enter both username/email and password.';
    }
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['reg_username'] ?? '');
    $email = trim($_POST['reg_email'] ?? '');
    $password = $_POST['reg_password'] ?? '';
    $confirm_password = $_POST['reg_confirm_password'] ?? '';
    
    $errors = [];
    
    // Validation
    if (empty($username)) {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters long.';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    
    // Check if username or email already exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('SELECT id FROM accounts WHERE username = ? OR email = ? LIMIT 1');
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $errors[] = 'Username or email already exists.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
    
    // Create account if no errors
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Default role for shop registration is Customer
            $default_role = 'Customer';
            
            $stmt = $pdo->prepare('INSERT INTO accounts (username, email, password, role, is_active, created_at) VALUES (?, ?, ?, ?, 1, NOW())');
            $stmt->execute([$username, $email, $hashed_password, $default_role]);
            
            $user_id = $pdo->lastInsertId();
            
            // Auto-login after registration
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $default_role;
            $_SESSION['is_logged_in'] = true;
            
            // Redirect to shop after successful registration
            header('Location: shop.php?welcome=1');
            exit;
            
        } catch (PDOException $e) {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
    
    $register_errors = $errors;
}

// Include header template files
include_once "assets/includes/doctype.php";
include 'assets/includes/header.php';

// Inject dynamic brand color variable before main.css for CSS variable support
?>
<style id="dynamic-brand-colors">
    :root {
        --brand-primary: <?php echo isset($brand_primary_color) ? $brand_primary_color : '#6c2eb6'; ?>;
    }
</style>

<?php
if (empty($_SESSION['csrf_token']))
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh; background: #f7f9fc;">
    <div class="card gws-auth-card"
        style="width: 100%; max-width: 420px; border-radius:0.5rem; border:2px solid var(--brand-primary); box-shadow:0 2px 8px #0001;">
        <ul class="nav nav-pills nav-justified gws-auth-tabs" id="authTabs" role="tablist"
            style="background:var(--brand-primary); border-radius:0.5rem 0.5rem 0 0; border-bottom:0;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-panel" type="button" role="tab" aria-controls="login-panel" aria-selected="true">
                    Login
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-panel" type="button" role="tab" aria-controls="register-panel" aria-selected="false">
                    Register
                </button>
            </li>
        </ul>
        <div class="tab-content gws-auth-tab-content" id="authTabContent"
            style="border-radius:0 0 0.5rem 0.5rem; border:none; background:#fff;">
            
            <!-- Login Panel -->
            <div class="tab-pane fade show active" id="login-panel" role="tabpanel" aria-labelledby="login-tab">
                <div class="gws-form-title">Shop Login</div>
                <div class="gws-form-subtitle">Access your shop account securely</div>
                            <?php if (isset($login_error)): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($login_error); ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="" autocomplete="on"
                                aria-label="Shop Login Form" role="form" style="width:100%; padding:2rem 1.5rem; margin:0 auto; max-width:400px; background:#fff; border-radius:0.5rem; box-shadow:0 2px 8px rgba(0,0,0,0.04);" class="login-form">
                                <input type="hidden" name="login" value="1">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                
                                <label for="username" class="gws-form-label">Username or Email</label>
                                <input type="text" class="gws-form-control" id="username" name="username" 
                                       placeholder="Enter your username or email"
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                       required autofocus aria-required="true" autocomplete="username">
                                
                                <label for="password" class="gws-form-label">Password</label>
                                <input type="password" class="gws-form-control" id="password" name="password" 
                                       placeholder="Enter your password"
                                       required aria-required="true" autocomplete="current-password">
                                
                                <div class="d-flex justify-content-between align-items-center" aria-label="Login options">
                                    <div class="form-check gws-rememberme">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1"
                                            aria-checked="false" aria-label="Remember me">
                                        <label class="form-check-label" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="msg login-msg" style="min-height:2em;" aria-live="polite"></div>
                                <button type="submit" class="gws-form-btn">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </button>
                            </form>
                            
                            <div class="text-center">
                                <p class="mb-2">Don't have an account? <a href="#" onclick="document.getElementById('register-tab').click();">Register here</a></p>
                                <p><a href="blog.php" class="text-muted">Visit Blog</a> | <a href="shop.php" class="text-muted">Continue Shopping</a></p>
                            </div>
                        </div>
                        
                        <!-- Register Panel -->
                        <div class="tab-pane fade" id="register-panel" role="tabpanel" aria-labelledby="register-tab">
                            <div class="gws-form-title">Create Shop Account</div>
                            <div class="gws-form-subtitle">By registering, you'll be able to track orders, save favorites, and access exclusive shop features.</div>
                            
                            <?php if (isset($register_errors) && !empty($register_errors)): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <ul class="mb-0">
                                        <?php foreach ($register_errors as $error): ?>
                                            <li><?php echo htmlspecialchars($error); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="" autocomplete="on"
                                aria-label="Shop Register Form" role="form" style="width:100%; padding:2rem 1.5rem; margin:0 auto; max-width:400px; background:#fff; border-radius:0.5rem; box-shadow:0 2px 8px rgba(0,0,0,0.04);" class="register-form">
                                <input type="hidden" name="register" value="1">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                
                                <label for="reg_username" class="gws-form-label">Username</label>
                                <input type="text" class="gws-form-control" id="reg_username" name="reg_username" 
                                       placeholder="Choose a username"
                                       value="<?php echo isset($_POST['reg_username']) ? htmlspecialchars($_POST['reg_username']) : ''; ?>" 
                                       required aria-required="true" autocomplete="username">
                                <span class="gws-form-help">3+ characters, letters, numbers, and underscores only</span>
                                
                                <label for="reg_email" class="gws-form-label">Email Address</label>
                                <input type="email" class="gws-form-control" id="reg_email" name="reg_email" 
                                       placeholder="Enter your email address"
                                       value="<?php echo isset($_POST['reg_email']) ? htmlspecialchars($_POST['reg_email']) : ''; ?>" 
                                       required aria-required="true" autocomplete="email">
                                
                                <label for="reg_password" class="gws-form-label">Password</label>
                                <input type="password" class="gws-form-control" id="reg_password" name="reg_password" 
                                       placeholder="Create a secure password"
                                       required aria-required="true" autocomplete="new-password">
                                <span class="gws-form-help">Minimum 6 characters</span>
                                
                                <label for="reg_confirm_password" class="gws-form-label">Confirm Password</label>
                                <input type="password" class="gws-form-control" id="reg_confirm_password" name="reg_confirm_password" 
                                       placeholder="Confirm your password"
                                       required aria-required="true" autocomplete="new-password">
                                
                                <div class="msg register-msg" style="min-height:2em;" aria-live="polite"></div>
                                <button type="submit" class="gws-form-btn">
                                    <i class="fas fa-user-plus"></i> Create Account
                                </button>
                            </form>
                            
                            <div class="text-center" style="padding: 1rem;">
                                <p class="mb-2">Already have an account? <a href="#" onclick="document.getElementById('login-tab').click();" class="gws-form-link">Login here</a></p>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
/* Auth Tab Styling */
.gws-auth-tabs .nav-link {
    border: none !important;
    background: transparent !important;
    color: rgba(255,255,255,0.8) !important;
    transition: all 0.3s ease;
}
.gws-auth-tabs .nav-link.active {
    background: #fff !important;
    color: var(--brand-primary) !important;
    font-weight: 600;
}
.nav-pills .gws-auth-tabs .nav-link:hover:not(.active),
.gws-auth-tabs .nav-link:hover:not(.active) {
    background: transparent !important;
    color: rgba(255,255,255,0.8) !important;
    text-decoration: none !important;
    border: none !important;
    box-shadow: none !important;
}

/* Form Title Styling */
.gws-form-title {
    font-size: 1.5rem;
    font-weight: 600;
    text-align: center;
    margin: 1.5rem 0 0.5rem 0;
    color: var(--brand-primary);
}
.gws-form-subtitle {
    font-size: 0.95rem;
    text-align: center;
    color: #6c757d;
    margin-bottom: 0.75rem;
}

/* Form Control Styling */
.gws-form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
    display: block;
}
.gws-form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e9ecef;
    border-radius: 0.5rem;
    font-size: 1rem;
    margin-bottom: 1rem;
    transition: border-color 0.3s ease;
}
.gws-form-control:focus {
    outline: none;
    border-color: var(--brand-primary);
    box-shadow: 0 0 0 0.2rem rgba(108, 46, 182, 0.25);
}
.gws-form-btn {
    width: 100%;
    padding: 0.75rem;
    background: var(--brand-primary);
    color: white;
    border: none;
    border-radius: 0.5rem;
    font-size: 1rem;
    font-weight: 600;
    margin-top: 0.5rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.gws-form-btn:hover {
    background: #5a2595;
}
.gws-form-help {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: -0.5rem;
    margin-bottom: 1rem;
    display: block;
}
.gws-form-link {
    color: var(--brand-primary);
    text-decoration: none;
    transition: opacity 0.2s ease;
}
.gws-form-link:hover {
    opacity: 0.8;
}
.gws-rememberme {
    margin: 0.5rem 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab persistence
    var triggerTabList = [].slice.call(document.querySelectorAll('#authTabs button'));
    triggerTabList.forEach(function(triggerEl) {
        triggerEl.addEventListener('shown.bs.tab', function(event) {
            localStorage.setItem('shopAuthTab', event.target.id);
        });
    });
    
    // Restore last active tab
    var lastTab = localStorage.getItem('shopAuthTab');
    if (lastTab) {
        var tab = document.getElementById(lastTab);
        if (tab && typeof bootstrap !== 'undefined') {
            new bootstrap.Tab(tab).show();
        }
    }
    
    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const passwordField = form.querySelector('input[name="reg_password"]');
            const confirmField = form.querySelector('input[name="reg_confirm_password"]');
            
            if (passwordField && confirmField) {
                if (passwordField.value !== confirmField.value) {
                    e.preventDefault();
                    alert('Passwords do not match!');
                    return false;
                }
                if (passwordField.value.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters long!');
                    return false;
                }
            }
        });
    });
});
</script>

<?php include 'assets/includes/footer.php'; ?>
