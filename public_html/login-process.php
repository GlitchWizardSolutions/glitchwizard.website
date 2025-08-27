<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Registration Process Documentation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f9fc;
            color: #222;
            margin: 0;
            padding: 0;
        }

        .doc-container {
            max-width: 900px;
            margin: 2em auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px #0001;
            padding: 2em;
        }

        h1,
        h2,
        h3 {
            color: #1a237e;
        }

        h1 {
            font-size: 2em;
            margin-bottom: 0.5em;
        }

        h2 {
            font-size: 1.3em;
            margin-top: 2em;
        }

        ul,
        ol {
            margin-left: 2em;
        }

        .todo {
            color: #b71c1c;
            font-weight: bold;
        }

        code {
            background: #f3f3f3;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.97em;
        }

        .section {
            margin-bottom: 2em;
        }
    </style>
</head>

<body>
    <div class="doc-container">
        <h1>Login & Registration Process Documentation</h1>
        <p>This page provides a comprehensive overview of the login, registration, authentication, and password reset
            processes for the main user account system. <strong>Blog system is excluded for now.</strong></p>

        <div class="section">
            <h2>1. Login Process (<code>login.php</code>)</h2>
            <ul>
                <li><code>login.php</code> displays the login form for users to enter their username and password.</li>
                <li>If a user is already logged in (<code>$_SESSION['loggedin']</code>), <code>login.php</code>
                    immediately redirects them using <code>redirect_by_role($_SESSION['role'])</code> to the appropriate
                    portal (usually <code>client_portal/index.php</code>).</li>
                <li>If a valid <code>rememberme</code> cookie is present, <code>login.php</code> attempts to
                    authenticate the user by matching the cookie value to the database, updating session variables, and
                    redirecting as above.</li>
                <li>The login form submits via AJAX to <code>accounts_system/authenticate.php</code>.</li>
                <li>On successful login, session variables are set: <code>$_SESSION['loggedin']</code>,
                    <code>$_SESSION['name']</code>, <code>$_SESSION['id']</code>, <code>$_SESSION['role']</code>.</li>
                <li>If 'Remember me' is checked, a persistent cookie is set and stored in the database.</li>
                <li>On error, an error message is returned and displayed in the form.</li>
            </ul>
            <div class="todo">TODO: Ensure all redirects use the updated <code>redirect_by_role</code> logic and do not
                hardcode <code>/admin/index.php</code> or other legacy paths.</div>
        </div>

        <div class="section">
            <h2>2. Authentication Process (<code>accounts_system/authenticate.php</code>)</h2>
            <ul>
                <li>Receives POSTed username and password from <code>login.php</code> (AJAX or standard form).</li>
                <li>Validates credentials against the <code>accounts</code> table in the database.</li>
                <li>If valid, sets session variables and optionally the <code>rememberme</code> cookie.</li>
                <li>Calls <code>redirect_by_role($role)</code> to send the user to the correct portal.</li>
                <li>On error, returns an error message for AJAX or redirects back to <code>login.php</code> with an
                    error parameter.</li>
            </ul>
        </div>

        <div class="section">
            <h2>3. Registration Process (<code>register.php</code>, <code>accounts_system/register-process.php</code>)
            </h2>
            <ul>
                <li><code>register.php</code> displays the registration form for new users.</li>
                <li>The form submits via AJAX to <code>accounts_system/register-process.php</code>.</li>
                <li><code>register-process.php</code> validates input, creates a new account in the database, and may
                    send an activation email if required.</li>
                <li>On success, the user is either logged in automatically or prompted to activate their account
                    (depending on settings).</li>
                <li>On error, an error message is returned for AJAX or displayed in the form.</li>
            </ul>
            <div class="todo">TODO: Confirm if registration auto-logs in the user or requires activation. Streamline
                messaging for both cases.</div>
        </div>

        <div class="section">
            <h2>4. Password Reset Process (<code>forgot-password.php</code>,
                <code>accounts_system/forgot-password.php</code>)</h2>
            <ul>
                <li><code>forgot-password.php</code> displays a form for users to enter their email address.</li>
                <li>The form submits via AJAX to itself (AJAX handler at the top of <code>forgot-password.php</code>).
                </li>
                <li>If the email exists, a reset code is generated, stored in the database, and a reset email is sent.
                </li>
                <li>The user receives a link to reset their password (handled by <code>reset-password.php</code>).</li>
                <li><code>reset-password.php</code> allows the user to set a new password using the reset code.</li>
                <li>On success, the user can log in with the new password.</li>
            </ul>
            <div class="todo">TODO: Ensure reset code expiration and one-time use. Confirm
                <code>reset-password.php</code> is robust and secure.</div>
        </div>

        <div class="section">
            <h2>5. Administration (<code>accounts_system/main.php</code>, settings, and admin panel)</h2>
            <ul>
                <li>Admins can manage user accounts via the admin panel (not covered in detail here).</li>
                <li>Account settings (mailer, activation, etc.) are loaded from
                    <code>assets/includes/settings/account_settings.php</code>.</li>
                <li>The <code>main.php</code> file provides core functions for authentication, session management, and
                    email sending.</li>
            </ul>
        </div>

        <div class="section">
            <h2>6. Session &amp; Cookie Management</h2>
            <ul>
                <li>Session variables: <code>$_SESSION['loggedin']</code>, <code>$_SESSION['name']</code>,
                    <code>$_SESSION['id']</code>, <code>$_SESSION['role']</code></li>
                <li>Remember me: Persistent cookie stored in both browser and database, checked on
                    <code>login.php</code> and in <code>main.php</code></li>
            </ul>
        </div>

        <div class="section">
            <h2>7. TODOs &amp; Areas for Review</h2>
            <ul>
                <li class="todo">Audit all redirects for legacy paths (e.g., <code>/admin/index.php</code>) and update
                    to use <code>redirect_by_role</code>.</li>
                <li class="todo">Ensure all login, registration, and password reset forms use AJAX and unified error
                    handling.</li>
                <li class="todo">Confirm that all session and cookie logic is robust and secure (e.g., session fixation,
                    cookie security flags).</li>
                <li class="todo">Remove or refactor any duplicate or legacy login/register logic in
                    <code>blog_system</code> or other modules.</li>
                <li class="todo">Review activation and reset code logic for security and expiration.</li>
                <li class="todo">Document any additional admin or user management flows as needed.</li>
            </ul>
        </div>

        <div class="section" style="text-align:center; color:#888; font-size:1em;">
            <strong>End of Documentation</strong>
        </div>
    </div>
</body>

</html>