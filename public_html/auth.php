<?php
// Unified Authentication Page: Login, Register, Forgot Password, Reset Password Tabs
// All backend logic, settings checks, and AJAX endpoints remain unchanged.

// Robust config and main include logic (copied from register.php/login.php)
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

// AJAX handler for forgot password (must be before any HTML output)
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' && isset($_POST['email']) && !empty($_POST['email']))
{
    $stmt = $pdo->prepare('SELECT username, email FROM accounts WHERE email = ?');
    $stmt->execute([$_POST['email']]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($account)
    {
        $unique_reset_code = hash('sha256', uniqid() . $account['email'] . secret_key);
        $stmt = $pdo->prepare('UPDATE accounts SET reset_code = ? WHERE email = ?');
        $stmt->execute([$unique_reset_code, $account['email']]);
        send_password_reset_email($account['email'], $account['username'], $unique_reset_code);
        echo 'Success: Reset password link has been sent to your email!';
    } else
    {
        echo 'Error: We do not have an account with that email!';
    }
    exit;
}

// AJAX handler for reset password (must be before any HTML output)
if (
    isset($_GET['reset_code']) && !empty($_GET['reset_code']) &&
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' &&
    isset($_POST['npassword'], $_POST['cpassword'])
)
{
    header('Content-Type: text/plain');
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE reset_code = ?');
    $stmt->execute([$_GET['reset_code']]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($account)
    {
        $npassword = $_POST['npassword'];
        $cpassword = $_POST['cpassword'];
        if (strlen($npassword) > 20 || strlen($npassword) < 5)
        {
            exit('Error: Password must be between 5 and 20 characters long!');
        } elseif ($npassword !== $cpassword)
        {
            exit('Error: Passwords must match!');
        } else
        {
            $password = password_hash($npassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE accounts SET password = ?, reset_code = "" WHERE reset_code = ?');
            $stmt->execute([$password, $_GET['reset_code']]);
            exit('Success: Password has been reset! You can now <a href="auth.php?tab=login" class="gws-form-link">login</a>.');
        }
    } else
    {
        exit('Error: Incorrect or expired reset code.');
    }
}

// Redirect already logged-in users
if (isset($_SESSION['loggedin']))
{
    // Check for redirect parameter
    if (isset($_GET['redirect']) && $_GET['redirect'] === 'review') {
        header('Location: public_reviews.php');
        exit;
    }
    redirect_by_role($_SESSION['role']);
    exit;
}
// Also check if they are "remembered"
if (isset($_COOKIE['rememberme']) && !empty($_COOKIE['rememberme']))
{
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE rememberme = ?');
    $stmt->execute([$_COOKIE['rememberme']]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($account)
    {
        session_regenerate_id();
        $_SESSION['loggedin'] = TRUE;
        $_SESSION['name'] = $account['username'];
        $_SESSION['id'] = $account['id'];
        $_SESSION['role'] = $account['role'];
        $date = date('Y-m-d\TH:i:s');
        $stmt = $pdo->prepare('UPDATE accounts SET last_seen = ? WHERE id = ?');
        $stmt->execute([$date, $account['id']]);
        // Check for redirect parameter
        if (isset($_GET['redirect']) && $_GET['redirect'] === 'review') {
            header('Location: public_reviews.php');
            exit;
        }
        redirect_by_role($account['role']);
        exit;
    }
}

include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
// Inject dynamic brand color variable before main.css for CSS variable support
?>
<style id="dynamic-brand-colors">
    :root {
        --brand-primary:
            <?php echo isset($brand_primary_color) ? $brand_primary_color : '#6c2eb6'; ?>
        ;
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
                <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login"
                    type="button" role="tab" aria-controls="login" aria-selected="true">Login</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button"
                    role="tab" aria-controls="register" aria-selected="false">Register</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="forgot-tab" data-bs-toggle="tab" data-bs-target="#forgot" type="button"
                    role="tab" aria-controls="forgot" aria-selected="false">Password</button>
            </li>
        </ul>
        <div class="tab-content gws-auth-tab-content" id="authTabsContent"
            style="border-radius:0 0 0.5rem 0.5rem; border:none; background:#fff;">
            <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                <div class="gws-form-title">Member Login</div>
                <div class="gws-form-subtitle">Access your account securely</div>
                <form action="accounts_system/authenticate.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="post" autocomplete="on"
                    aria-label="Member Login Form" role="form" style="width:100%; padding:2rem 1.5rem; margin:0 auto; max-width:400px; background:#fff; border-radius:0.5rem; box-shadow:0 2px 8px rgba(0,0,0,0.04);" class="login-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <label for="login-username" class="gws-form-label">Username</label>
                    <input type="text" class="gws-form-control" id="login-username" name="username"
                        placeholder="Enter your username" required autofocus aria-required="true"
                        aria-describedby="login-username-desc" autocomplete="username">
                    <span id="login-username-desc" class="visually-hidden">Enter your username to log in</span>
                    <label for="login-password" class="gws-form-label">Password</label>
                    <input type="password" class="gws-form-control" id="login-password" name="password"
                        placeholder="Enter your password" required aria-required="true"
                        aria-describedby="login-password-desc" autocomplete="current-password">
                    <div class="d-flex justify-content-between align-items-center" aria-label="Login options">
                        <div class="form-check gws-rememberme">
                            <input class="form-check-input" type="checkbox" id="rememberme" name="rememberme" value="1"
                                aria-checked="false" aria-label="Remember me">
                            <label class="form-check-label" for="rememberme">
                                Remember me
                            </label>
                        </div>
                        <a href="#" class="gws-form-link" aria-label="Forgot your password?" id="show-forgot">Forgot
                            password?</a>
                    </div>
                    <div class="msg login-msg" style="min-height:2em;" aria-live="polite"></div>
                    <button type="submit" class="gws-form-btn">Login</button>
                </form>
            </div>
            <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                <div class="gws-form-title">Member Register</div>
                <div class="gws-form-subtitle">Create your account</div>
                <form action="accounts_system/register-process.php" method="post" autocomplete="on"
                    aria-label="Member Register Form" role="form" style="width:100%; padding:2rem 1.5rem; margin:0 auto; max-width:400px; background:#fff; border-radius:0.5rem; box-shadow:0 2px 8px rgba(0,0,0,0.04);" class="register-form">
                    <label for="reg-username" class="gws-form-label">Username</label>
                    <input type="text" class="gws-form-control" id="reg-username" name="username"
                        placeholder="Choose a username" required aria-required="true"
                        aria-describedby="reg-username-desc" autocomplete="username">
                    <span id="reg-username-desc" class="visually-hidden">Choose a username for your account</span>
                    <label for="reg-email" class="gws-form-label">Email</label>
                    <input type="email" class="gws-form-control" id="reg-email" name="email"
                        placeholder="Enter your email" required aria-required="true" aria-describedby="reg-email-desc"
                        autocomplete="email">
                    <span id="reg-email-desc" class="visually-hidden">Enter your email address</span>
                    <label for="reg-password" class="gws-form-label">Password</label>
                    <input type="password" class="gws-form-control" id="reg-password" name="password"
                        placeholder="Create a password" required aria-required="true"
                        aria-describedby="reg-password-desc" autocomplete="new-password">
                    <span id="reg-password-desc" class="visually-hidden">Create a password for your account</span>
                    <label for="reg-cpassword" class="gws-form-label">Confirm Password</label>
                    <input type="password" class="gws-form-control" id="reg-cpassword" name="cpassword"
                        placeholder="Confirm your password" required aria-required="true"
                        aria-describedby="reg-cpassword-desc" autocomplete="new-password">
                    <span id="reg-cpassword-desc" class="visually-hidden">Re-enter your password to confirm</span>
                    <div class="msg register-msg" style="min-height:2em;" aria-live="polite"></div>
                    <button type="submit" class="gws-form-btn">Register</button>
                </form>
            </div>
            <div class="tab-pane fade" id="forgot" role="tabpanel" aria-labelledby="forgot-tab">
                <?php if (isset($_GET['reset_code']) && !empty($_GET['reset_code'])): ?>
                    <div class="gws-form-title">Reset Password</div>
                    <div class="gws-form-subtitle">Enter your new password below</div>
                    <form action="auth.php?reset_code=<?= htmlspecialchars($_GET['reset_code']) ?>" method="post"
                        class="reset-form" autocomplete="on" aria-label="Reset Password Form" role="form"
                        style="width:100%; padding:2rem 1.5rem; margin:0 auto; max-width:400px; background:#fff; border-radius:0.5rem; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                        <label for="npassword" class="gws-form-label">New Password</label>
                        <input class="gws-form-control" type="password" name="npassword" id="npassword"
                            placeholder="New Password" required autocomplete="new-password" aria-required="true"
                            aria-describedby="npassword-desc" autocomplete="new-password">
                        <span id="npassword-desc" class="visually-hidden">Enter your new password</span>
                        <label for="cpassword" class="gws-form-label">Confirm Password</label>
                        <input class="gws-form-control" type="password" name="cpassword" id="cpassword"
                            placeholder="Confirm Password" required autocomplete="new-password" aria-required="true"
                            aria-describedby="cpassword-desc" autocomplete="new-password">
                        <span id="cpassword-desc" class="visually-hidden">Re-enter your new password to confirm</span>
                        <div class="msg reset-msg" style="display:none;" aria-live="polite"></div>
                        <button class="gws-form-btn" type="submit">Submit</button>
                    </form>
                    <div class="gws-form-footer">Remembered your password? <a href="auth.php?tab=login"
                            class="gws-form-link">Log in</a></div>
                <?php else: ?>
                    <div class="gws-form-title">Forgot Password</div>
                    <div class="gws-form-subtitle">Reset your account password</div>
                    <form action="auth.php" method="post" autocomplete="on" aria-label="Forgot Password Form" role="form"
                        style="width:100%; padding:2rem 1.5rem; margin:0 auto; max-width:400px; background:#fff; border-radius:0.5rem; box-shadow:0 2px 8px rgba(0,0,0,0.04);" class="forgot-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <label for="forgot-email" class="gws-form-label">Email Address</label>
                        <input type="email" class="gws-form-control" id="forgot-email" name="email"
                            placeholder="Enter your email address" required autofocus aria-required="true"
                            aria-describedby="forgot-email-desc" autocomplete="email">
                        <span id="forgot-email-desc" class="visually-hidden">Enter your email address to reset your
                            password</span>
                        <div class="msg forgot-msg" aria-live="polite"></div>
                        <button type="submit" class="gws-form-btn">Send Reset Link</button>
                        <div class="gws-form-footer">Remembered your password? <a href="#" class="gws-form-link"
                                id="show-login">Log In</a></div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <!-- Auth form and tab styles moved to main.css for reuse -->
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

    /* Form Link Styling */
    .gws-form-link {
        color: var(--brand-primary);
        text-decoration: none;
        transition: opacity 0.2s ease;
    }
    .gws-form-link:hover {
        opacity: 0.8;
    }

    /* Custom Remember Me Checkbox Styling */
    .gws-rememberme .form-check-input {
        width: 1.25em;
        height: 1.25em;
        border: 2.5px solid var(--brand-primary, #6c2eb6);
        background-color: var(--brand-primary, #6c2eb6);
        box-shadow: none;
        transition: background 0.15s, border 0.15s;
    }

    .gws-rememberme .form-check-input:checked {
        background-color: #fff;
        border-color: var(--brand-primary, #6c2eb6);
    }

    .gws-rememberme .form-check-input:checked:after {
        content: '';
        display: block;
        width: 0.65em;
        height: 0.65em;
        margin: 0.15em auto;
        border-radius: 2px;
        background: var(--brand-primary, #6c2eb6);
    }

    .gws-rememberme .form-check-label {
        color: var(--brand-primary, #6c2eb6);
        font-weight: 600;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Tab persistence and ?tab= support, and support for ?reset_code=
        function getTabFromQuery() {
            const params = new URLSearchParams(window.location.search);
            if (params.has('reset_code')) return 'forgot';
            const tab = params.get('tab');
            if (tab === 'login' || tab === 'register' || tab === 'forgot') return tab;
            return null;
        }
        var triggerTabList = [].slice.call(document.querySelectorAll('#authTabs button'));
        triggerTabList.forEach(function (triggerEl) {
            triggerEl.addEventListener('shown.bs.tab', function (event) {
                localStorage.setItem('authTab', event.target.id);
            });
        });
        var tabFromQuery = getTabFromQuery();
        if (tabFromQuery) {
            var tabBtn = document.getElementById(tabFromQuery + '-tab');
            if (tabBtn) new bootstrap.Tab(tabBtn).show();
        } else {
            var lastTab = localStorage.getItem('authTab');
            if (lastTab) {
                var tab = document.getElementById(lastTab);
                if (tab) new bootstrap.Tab(tab).show();
            }
        }
        // Tab switching for links
        var showForgot = document.getElementById('show-forgot');
        if (showForgot) {
            showForgot.addEventListener('click', function (e) {
                e.preventDefault();
                new bootstrap.Tab(document.getElementById('forgot-tab')).show();
            });
        }
        var showLogin = document.getElementById('show-login');
        if (showLogin) {
            showLogin.addEventListener('click', function (e) {
                e.preventDefault();
                new bootstrap.Tab(document.getElementById('login-tab')).show();
            });
        }
        // AJAX for login
        const loginForm = document.querySelector('.login-form');
        loginForm.onsubmit = function (event) {
            event.preventDefault();
            fetch(loginForm.action, {
                method: 'POST',
                body: new FormData(loginForm),
                cache: 'no-store',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(response => response.text()).then(result => {
                const msgBox = loginForm.querySelector('.login-msg');
                if (result.toLowerCase().includes('success:')) {
                    msgBox.classList.remove('error', 'success');
                    msgBox.classList.add('success');
                    msgBox.innerHTML = result.replace('Success: ', '');
                    setTimeout(() => { window.location.reload(); }, 1000);
                } else if (result.toLowerCase().includes('redirect')) {
                    window.location.reload();
                } else {
                    msgBox.classList.remove('error', 'success');
                    msgBox.classList.add('error');
                    msgBox.innerHTML = result.replace('Error: ', '');
                    msgBox.style.display = 'block';
                    msgBox.setAttribute('role', 'alert');
                }
            });
        };
        // AJAX for register
        const registerForm = document.querySelector('.register-form');
        registerForm.onsubmit = function (event) {
            event.preventDefault();
            fetch(registerForm.action, {
                method: 'POST',
                body: new FormData(registerForm),
                cache: 'no-store',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(response => response.text()).then(result => {
                const msgBox = registerForm.querySelector('.register-msg');
                if (result.toLowerCase().includes('success:')) {
                    msgBox.classList.remove('error', 'success');
                    msgBox.classList.add('success');
                    msgBox.innerHTML = result.replace('Success: ', '');
                } else if (result.toLowerCase().includes('redirect')) {
                    window.location.reload();
                } else {
                    msgBox.classList.remove('error', 'success');
                    msgBox.classList.add('error');
                    msgBox.innerHTML = result.replace('Error: ', '');
                    msgBox.style.display = 'block';
                    msgBox.setAttribute('role', 'alert');
                }
            });
        };
        // AJAX for forgot password
        const forgotForm = document.querySelector('.forgot-form');
        if (forgotForm) {
            forgotForm.onsubmit = function (event) {
                event.preventDefault();
                fetch(forgotForm.action, {
                    method: 'POST',
                    body: new FormData(forgotForm),
                    cache: 'no-store',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(response => response.text()).then(result => {
                    const msgBox = forgotForm.querySelector('.forgot-msg');
                    if (result.toLowerCase().includes('success:')) {
                        msgBox.classList.remove('error', 'success');
                        msgBox.classList.add('success');
                        msgBox.innerHTML = result.replace('Success: ', '');
                    } else {
                        msgBox.classList.remove('error', 'success');
                        msgBox.classList.add('error');
                        msgBox.innerHTML = result.replace('Error: ', '');
                        msgBox.style.display = 'block';
                        msgBox.setAttribute('role', 'alert');
                    }
                });
            };
        }
        // AJAX for reset password
        const resetForm = document.querySelector('.reset-form');
        if (resetForm) {
            resetForm.onsubmit = function (event) {
                event.preventDefault();
                fetch(resetForm.action, {
                    method: 'POST',
                    body: new FormData(resetForm),
                    cache: 'no-store',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.text())
                    .then(result => {
                        const msgBox = resetForm.querySelector('.reset-msg');
                        msgBox.style.display = 'block';
                        if (result.toLowerCase().includes('success:')) {
                            msgBox.classList.remove('error', 'success');
                            msgBox.classList.add('success');
                            msgBox.innerHTML = result.replace('Success: ', '');
                            msgBox.setAttribute('role', 'status');
                            resetForm.reset();
                        } else {
                            msgBox.classList.remove('error', 'success');
                            msgBox.classList.add('error');
                            msgBox.innerHTML = result.replace('Error: ', '');
                            msgBox.setAttribute('role', 'alert');
                        }
                    });
            };
        }
    });
</script>
<?php
include_once "assets/includes/contact.php";
// Use public footer for unified branding
include __DIR__ . '/assets/includes/footer.php';
?>