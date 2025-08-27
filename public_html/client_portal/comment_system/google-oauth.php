<?php
// Initialize sessions
session_start();
// Include the config file
include 'config.php';
// Check if oauth is enabled
if (!google_oauth_enabled) {
    exit('Google OAuth is not enabled!');
}
// Connect to the MySQL database using the PDO interface
try {
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to database: ' . $exception->getMessage());
}
// If the captured code param exists and is valid
if (isset($_GET['code']) && !empty($_GET['code'])) {
    // Execute cURL request to retrieve the access token
    $params = [
        'code' => $_GET['code'],
        'client_id' => google_oauth_client_id,
        'client_secret' => google_oauth_client_secret,
        'redirect_uri' => google_oauth_redirect_uri,
        'grant_type' => 'authorization_code'
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response, true);
    // Make sure access token is valid
    if (isset($response['access_token']) && !empty($response['access_token'])) {
        // Execute cURL request to retrieve the user info associated with the Google account
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v3/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $response['access_token']]);
        $response = curl_exec($ch);
        curl_close($ch);
        $profile = json_decode($response, true);
        // Make sure the profile data exists
        if (isset($profile['email'])) {
            // Check if account exists in database
            $stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ?');
            $stmt->execute([ $profile['email'] ]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            // Get the current date
            $date = date('Y-m-d\TH:i:s');
            // If the account exists...
            if ($account) {
                // Account exists! Bind the SQL data
                $id = $account['id'];
                $display_name = $account['display_name'];
                $role = $account['role'];
                $email = $account['email'];
                // Check if account is banned
                if ($account['banned']) {
                    exit('You cannot login right now!');
                }
            } else {
                // Determine google name and remove all special characters
                $display_name_parts = [];
                if (isset($profile['given_name'])) {
                    $display_name_parts[] = preg_replace('/[^a-zA-Z0-9\s]/s', '', $profile['given_name']);
                }
                if (isset($profile['family_name'])) {
                    $display_name_parts[] = preg_replace('/[^a-zA-Z0-9\s]/s', '', $profile['family_name']);
                }
                // if display_name empty, user first part of email
                $display_name = empty($display_name_parts) ? explode('@', $profile['email'])[0] : implode(' ', $display_name_parts);
                // Default role
                $role = 'Member';
                // Email
                $email = $profile['email'];
                // Generate a random password
                $password = password_hash(uniqid() . $date, PASSWORD_DEFAULT);
                // Account doesn't exist, create it
                $stmt = $pdo->prepare('INSERT INTO accounts (email, `password`, display_name, `role`, registered) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([ $email, $password, $display_name, $role, $date ]);
                // Account ID
                $id = $pdo->lastInsertId();
            }
            // Get previous page
            $previous_page = isset($_SESSION['previous_page']) ? $_SESSION['previous_page'] : comments_url;
            // Authenticate the user
            session_regenerate_id();
            $_SESSION['comment_account_loggedin'] = TRUE;
            $_SESSION['comment_account_id'] = $id;
            $_SESSION['comment_account_display_name'] = $display_name;
            $_SESSION['comment_account_role'] = $role;
            $_SESSION['comment_account_email'] = $email;
            // Redirect to previous page
            header('Location: ' . $previous_page);
            exit;
        } else {
            exit('Could not retrieve profile information! Please try again later!');
        }
    } else {
        exit('Invalid access token! Please try again later!');
    }
} else {
    // Save previous page
    $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
    // Define params and redirect to Google Authentication page
    $params = [
        'response_type' => 'code',
        'client_id' => google_oauth_client_id,
        'redirect_uri' => google_oauth_redirect_uri,
        'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
        'access_type' => 'offline',
        'prompt' => 'consent'
    ];
    header('Location: https://accounts.google.com/o/oauth2/auth?' . http_build_query($params));
    exit;
}
?>