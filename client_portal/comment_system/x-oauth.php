<?php
// Initialize sessions
session_start();
// Include your configuration file
include 'config.php';
// Check if X OAuth is enabled in the config file
if (!x_oauth_enabled) {
    exit('X OAuth is not enabled!');
}
// Connect to the MySQL database using the PDO interface
try {
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to database: ' . $exception->getMessage());
}
// This is the main logic block that handles the callback from X (Twitter)
if (isset($_GET['code'])) {
    // Exchange the authorization code for an access token
    $params = [
        'code' => $_GET['code'],
        'grant_type' => 'authorization_code',
        'client_id' => x_oauth_client_id,
        'redirect_uri' => x_oauth_redirect_uri,
        // The crucial PKCE parameter: send the original verifier stored in the session
        'code_verifier' => $_SESSION['x_oauth_code_verifier']
    ];
    // The token endpoint requires HTTP Basic Authentication
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.twitter.com/2/oauth2/token');
    // Set up Basic Auth with your Client ID and Client Secret
    curl_setopt($ch, CURLOPT_USERPWD, x_oauth_client_id . ':' . x_oauth_client_secret);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response_text = curl_exec($ch);
    curl_close($ch);
    $response = json_decode($response_text, true);
    // Ensure we received a valid access token
    if (isset($response['access_token'])) {
        // Use the access token to get the user's profile information
        $ch = curl_init();
        // Request specific fields from the user endpoint
        curl_setopt($ch, CURLOPT_URL, 'https://api.twitter.com/2/users/me?user.fields=profile_image_url,username');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Provide the access token in the Authorization header
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $response['access_token']]);
        $profile_response_text = curl_exec($ch);
        curl_close($ch);
        $profile = json_decode($profile_response_text, true);
        // Ensure we received valid profile data
        if (isset($profile['data'])) {
            $user_data = $profile['data'];
            $oauth_uid = $user_data['id'];
            // NOTE: X API v2 does not provide an email address.
            // We create a unique, placeholder email using the user's X ID.
            $email = $oauth_uid . '@twitter.placeholder';
            // Check if the user already exists in your database using the placeholder email
            $stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ?');
            $stmt->execute([$email]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            $date = date('Y-m-d\TH:i:s');
            if ($account) {
                // User exists! Log them in.
                $id = $account['id'];
                $display_name = $account['display_name'];
                $role = $account['role'];
                // Check if account is banned
                if ($account['banned']) {
                    exit('You cannot login right now!');
                }
            } else {
                // User does not exist, create a new account.
                $display_name = isset($user_data['name']) ? preg_replace('/[^a-zA-Z0-9\s]/s', '', $user_data['name']) : $user_data['username'];
                $role = 'Member';
                $password = password_hash(uniqid() . $date, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO accounts (email, `password`, display_name, `role`, registered) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$email, $password, $display_name, $role, $date]);
                $id = $pdo->lastInsertId();
            }
            // Authenticate the user and set session variables
            $previous_page = isset($_SESSION['previous_page']) ? $_SESSION['previous_page'] : comments_url;
            session_regenerate_id();
            $_SESSION['comment_account_loggedin'] = TRUE;
            $_SESSION['comment_account_id'] = $id;
            $_SESSION['comment_account_display_name'] = $display_name;
            $_SESSION['comment_account_role'] = $role;
            $_SESSION['comment_account_email'] = $email;
            // Redirect to the page the user came from
            header('Location: ' . $previous_page);
            exit;

        } else {
            exit('Could not retrieve profile information! Please try again later.');
        }
    } else {
        exit('Invalid access token! Please try again later.');
    }
} else {
    // Generate PKCE parameters
    $code_verifier = bin2hex(random_bytes(32));
    $code_challenge = rtrim(strtr(base64_encode(hash('sha256', $code_verifier, true)), '+/', '-_'), '=');
    // Store the verifier in the session to check against later
    $_SESSION['x_oauth_code_verifier'] = $code_verifier;
    // Store the page the user came from
    $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'] ?? comments_url;
    // Build the authorization URL and redirect the user
    $params = [
        'response_type' => 'code',
        'client_id' => x_oauth_client_id,
        'redirect_uri' => x_oauth_redirect_uri,
        'scope' => 'users.read tweet.read offline.access', // Scopes required to get user info
        'state' => 'state', // The state parameter is required but we are not validating it per user request
        'code_challenge' => $code_challenge,
        'code_challenge_method' => 'S256'
    ];
    header('Location: https://twitter.com/i/oauth2/authorize?' . http_build_query($params));
    exit;
}
?>