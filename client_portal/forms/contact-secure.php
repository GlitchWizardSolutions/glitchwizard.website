<?php
/**
 * Secure Contact Form Handler with Multiple Spam Protection Layers
 * 
 * Protection Features:
 * - CSRF Token Validation
 * - Honeypot Fields Detection
 * - Rate Limiting
 * - Input Validation & Sanitization
 * - Suspicious Content Detection
 * - IP-based Blocking
 */

// Start session for CSRF and rate limiting
if (!isset($_SESSION)) {
    session_start();
}

// Configuration
$config = [
    'receiving_email' => 'contact@example.com', // Change this to your email
    'max_submissions_per_hour' => 3,
    'min_time_between_submissions' => 10, // seconds
    'blocked_words' => ['viagra', 'casino', 'lottery', 'winner', 'congratulations', 'urgent', 'bitcoin', 'crypto'],
    'max_links_allowed' => 2,
    'enable_logging' => true,
];

// Response function
function sendResponse($success, $message) {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// Logging function
function logAttempt($type, $ip, $message) {
    global $config;
    if (!$config['enable_logging']) return;
    
    $log_file = __DIR__ . '/contact_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] $type - IP: $ip - $message" . PHP_EOL;
    file_put_contents($log_file, $entry, FILE_APPEND | LOCK_EX);
}

// Get client IP
function getClientIP() {
    $ip_keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

$client_ip = getClientIP();

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logAttempt('INVALID_METHOD', $client_ip, 'Non-POST request');
    sendResponse(false, 'Invalid request method.');
}

// 1. CSRF Token Validation
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    logAttempt('CSRF_FAIL', $client_ip, 'Invalid CSRF token');
    sendResponse(false, 'Security token mismatch. Please refresh the page and try again.');
}

// 2. Honeypot Detection
if (!empty($_POST['website']) || !empty($_POST['phone_number'])) {
    logAttempt('HONEYPOT', $client_ip, 'Bot detected via honeypot fields');
    // Return success to not alert bots
    sendResponse(true, 'Message sent successfully!');
}

// 3. Rate Limiting
$rate_limit_key = 'contact_submissions_' . md5($client_ip);
$current_time = time();

if (!isset($_SESSION[$rate_limit_key])) {
    $_SESSION[$rate_limit_key] = [];
}

// Clean old submissions (older than 1 hour)
$_SESSION[$rate_limit_key] = array_filter($_SESSION[$rate_limit_key], function($timestamp) use ($current_time) {
    return ($current_time - $timestamp) < 3600;
});

// Check submission frequency
if (count($_SESSION[$rate_limit_key]) >= $config['max_submissions_per_hour']) {
    logAttempt('RATE_LIMIT', $client_ip, 'Too many submissions per hour');
    sendResponse(false, 'Too many submissions. Please wait before sending another message.');
}

// Check minimum time between submissions
if (!empty($_SESSION[$rate_limit_key])) {
    $last_submission = max($_SESSION[$rate_limit_key]);
    if (($current_time - $last_submission) < $config['min_time_between_submissions']) {
        logAttempt('RATE_LIMIT', $client_ip, 'Submissions too frequent');
        sendResponse(false, 'Please wait a moment before sending another message.');
    }
}

// 4. Input Validation and Sanitization
$required_fields = ['name', 'email', 'subject', 'message'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        logAttempt('VALIDATION', $client_ip, "Missing required field: $field");
        sendResponse(false, "Please fill in all required fields.");
    }
}

// Sanitize inputs
$name = trim(filter_var($_POST['name'], FILTER_SANITIZE_STRING));
$email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
$subject = trim(filter_var($_POST['subject'], FILTER_SANITIZE_STRING));
$message = trim($_POST['message']);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    logAttempt('VALIDATION', $client_ip, 'Invalid email format');
    sendResponse(false, 'Please enter a valid email address.');
}

// Length validation
if (strlen($name) > 100 || strlen($subject) > 200 || strlen($message) > 2000) {
    logAttempt('VALIDATION', $client_ip, 'Input too long');
    sendResponse(false, 'One or more fields exceed the maximum length.');
}

if (strlen($name) < 2 || strlen($subject) < 3 || strlen($message) < 10) {
    logAttempt('VALIDATION', $client_ip, 'Input too short');
    sendResponse(false, 'Please provide more detailed information.');
}

// 5. Content Analysis for Spam
$full_text = strtolower($name . ' ' . $subject . ' ' . $message);

// Check for blocked words
foreach ($config['blocked_words'] as $word) {
    if (strpos($full_text, strtolower($word)) !== false) {
        logAttempt('SPAM_CONTENT', $client_ip, "Blocked word detected: $word");
        sendResponse(false, 'Your message contains prohibited content.');
    }
}

// Check for excessive links
$link_count = preg_match_all('/https?:\/\//', $message);
if ($link_count > $config['max_links_allowed']) {
    logAttempt('SPAM_CONTENT', $client_ip, "Too many links: $link_count");
    sendResponse(false, 'Please limit the number of links in your message.');
}

// Check for repeated characters (common in spam)
if (preg_match('/(.)\1{10,}/', $message)) {
    logAttempt('SPAM_CONTENT', $client_ip, 'Excessive repeated characters');
    sendResponse(false, 'Your message contains unusual formatting.');
}

// Check for suspicious patterns
$suspicious_patterns = [
    '/\b(click here|act now|limited time|don\'t delay)\b/i',
    '/\$\d+.*\b(million|thousand|prize|winner)\b/i',
    '/\b(urgent|asap|immediate|expire)\b.*\b(action|response)\b/i',
];

foreach ($suspicious_patterns as $pattern) {
    if (preg_match($pattern, $full_text)) {
        logAttempt('SPAM_PATTERN', $client_ip, 'Suspicious pattern detected');
        sendResponse(false, 'Your message appears to be spam.');
    }
}

// Record successful submission attempt
$_SESSION[$rate_limit_key][] = $current_time;

// 6. Send Email (simplified version - adapt to your email system)
$to = $config['receiving_email'];
$email_subject = '[Contact Form] ' . $subject;
$email_body = "
New contact form submission:

Name: $name
Email: $email
Subject: $subject

Message:
$message

---
Submitted: " . date('Y-m-d H:i:s') . "
IP Address: $client_ip
User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "
";

$headers = [
    'From: noreply@' . $_SERVER['SERVER_NAME'],
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'Content-Type: text/plain; charset=UTF-8',
];

// Send email
$mail_sent = mail($to, $email_subject, $email_body, implode("\r\n", $headers));

if ($mail_sent) {
    logAttempt('SUCCESS', $client_ip, "Message sent from $email");
    
    // Optional: Save to database
    // saveToDatabase($name, $email, $subject, $message, $client_ip);
    
    sendResponse(true, 'Thank you! Your message has been sent successfully.');
} else {
    logAttempt('EMAIL_FAIL', $client_ip, 'Failed to send email');
    sendResponse(false, 'Sorry, there was an error sending your message. Please try again.');
}

// Optional: Database storage function
function saveToDatabase($name, $email, $subject, $message, $ip) {
    // Implement database storage if needed
    // This could integrate with your existing blog_messages table
}
?>
