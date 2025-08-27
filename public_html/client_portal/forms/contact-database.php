<?php
/**
 * Enhanced Contact Form Processor with Database Integration
 * 
 * SYSTEM: GWS Universal Hybrid App
 * FILE: contact-database.php
 * LOCATION: /public_html/forms/
 * PURPOSE: Process contact form submissions with database storage and email notification
 * 
 * Multi-layered protection:
 * 1. CSRF Token Validation
 * 2. Honeypot Fields Detection
 * 3. Rate Limiting (IP-based)
 * 4. Content Analysis & Spam Detection
 * 5. Input Validation & Sanitization
 * 6. Database Storage
 * 7. Email Notification
 * 
 * All protections are invisible to legitimate users.
 */

// Include the universal configuration
require_once __DIR__ . '/../../private/gws-universal-config.php';

// Start session for CSRF protection
if (!isset($_SESSION)) {
    session_start();
}

// Load contact settings (will create default if doesn't exist)
$contact_settings_file = PROJECT_ROOT . '/public_html/assets/includes/settings/contact_settings.php';
if (!file_exists($contact_settings_file)) {
    // Create default contact settings
    $default_settings = [
        'receiving_email' => 'admin@yoursite.com',
        'smtp_enabled' => false,
        'smtp_host' => '',
        'smtp_port' => 587,
        'smtp_username' => '',
        'smtp_password' => '',
        'smtp_encryption' => 'tls',
        'email_from_name' => 'Contact Form',
        'email_subject_prefix' => '[Contact Form]',
        'auto_reply_enabled' => true,
        'auto_reply_subject' => 'Thank you for contacting us',
        'auto_reply_message' => 'We have received your message and will respond as soon as possible.',
        'rate_limit_max' => 3,
        'rate_limit_window' => 3600,
        'min_submit_interval' => 10,
        'blocked_words' => [
            'viagra', 'cialis', 'loan', 'casino', 'poker', 'bitcoin', 'crypto',
            'make money', 'work from home', 'business opportunity', 'free money',
            'click here', 'limited time', 'act now', 'congratulations'
        ],
        'max_links' => 2,
        'enable_logging' => true
    ];
    
    // Ensure settings directory exists
    $settings_dir = dirname($contact_settings_file);
    if (!file_exists($settings_dir)) {
        mkdir($settings_dir, 0755, true);
    }
    
    $php_code = "<?php\n// Contact Form Settings\n// Last updated: " . date('Y-m-d H:i:s') . "\n\n";
    $php_code .= "\$contact_settings = " . var_export($default_settings, true) . ";\n";
    file_put_contents($contact_settings_file, $php_code);
}

// Load settings
include $contact_settings_file;

// Response function
function sendResponse($success, $message, $field_errors = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'field_errors' => $field_errors
    ]);
    exit;
}

// Logging function
function logAttempt($type, $ip, $details = '') {
    global $contact_settings;
    
    if (!$contact_settings['enable_logging']) {
        return;
    }
    
    $log_file = PROJECT_ROOT . '/private/logs/contact_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $type from $ip: $details" . PHP_EOL;
    
    // Ensure logs directory exists
    $logDir = dirname($log_file);
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($log_file, $logEntry, FILE_APPEND | LOCK_EX);
}

// Get client IP
function getClientIP() {
    $headers = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ips = explode(',', $_SERVER[$header]);
            return trim($ips[0]);
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

// Rate limiting check
function checkRateLimit($ip) {
    global $contact_settings;
    $rateLimitFile = sys_get_temp_dir() . '/contact_rate_' . md5($ip) . '.json';
    
    $now = time();
    $data = [];
    
    if (file_exists($rateLimitFile)) {
        $content = file_get_contents($rateLimitFile);
        $data = json_decode($content, true) ?: [];
    }
    
    // Clean old entries
    $data = array_filter($data, function($timestamp) use ($now, $contact_settings) {
        return ($now - $timestamp) < $contact_settings['rate_limit_window'];
    });
    
    // Check submission count
    if (count($data) >= $contact_settings['rate_limit_max']) {
        return false;
    }
    
    // Check minimum interval
    if (!empty($data)) {
        $lastSubmission = max($data);
        if (($now - $lastSubmission) < $contact_settings['min_submit_interval']) {
            return false;
        }
    }
    
    // Record this attempt
    $data[] = $now;
    file_put_contents($rateLimitFile, json_encode($data), LOCK_EX);
    
    return true;
}

// Content analysis for spam detection
function analyzeContent($subject, $message) {
    global $contact_settings;
    
    $fullText = strtolower($subject . ' ' . $message);
    $flags = [];
    
    // Check for blocked words
    foreach ($contact_settings['blocked_words'] as $word) {
        if (strpos($fullText, strtolower($word)) !== false) {
            $flags[] = "blocked_word:$word";
        }
    }
    
    // Count links
    $linkCount = preg_match_all('/https?:\/\/|www\./i', $fullText);
    if ($linkCount > $contact_settings['max_links']) {
        $flags[] = "too_many_links:$linkCount";
    }
    
    // Check for suspicious patterns
    $suspiciousPatterns = [
        '/\b[A-Z]{10,}\b/', // Excessive caps
        '/(.)\1{4,}/', // Repeated characters
        '/\b\d{4,}\b.*\b\d{4,}\b/', // Multiple long numbers
        '/[^\w\s]{5,}/', // Excessive special characters
    ];
    
    foreach ($suspiciousPatterns as $pattern) {
        if (preg_match($pattern, $fullText)) {
            $flags[] = "suspicious_pattern";
        }
    }
    
    return $flags;
}

// Send email notification
function sendEmailNotification($contactData) {
    global $contact_settings, $pdo;
    
    try {
        // Use PHPMailer if available, otherwise use basic mail()
        $subject = $contact_settings['email_subject_prefix'] . ' ' . $contactData['subject'];
        
        $body = "New contact form submission:\n\n";
        $body .= "Name: {$contactData['first_name']} {$contactData['last_name']}\n";
        $body .= "Email: {$contactData['email']}\n";
        $body .= "Category: {$contactData['category']}\n";
        $body .= "Subject: {$contactData['subject']}\n\n";
        $body .= "Message:\n{$contactData['message']}\n\n";
        $body .= "---\n";
        $body .= "IP Address: " . getClientIP() . "\n";
        $body .= "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n";
        $body .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
        
        $headers = [
            "From: {$contact_settings['email_from_name']} <{$contactData['email']}>",
            "Reply-To: {$contactData['email']}",
            "X-Mailer: PHP/" . phpversion(),
            "Content-Type: text/plain; charset=UTF-8"
        ];
        
        $success = mail($contact_settings['receiving_email'], $subject, $body, implode("\r\n", $headers));
        
        // Send auto-reply if enabled
        if ($success && $contact_settings['auto_reply_enabled']) {
            $autoReplyBody = $contact_settings['auto_reply_message'];
            $autoReplyHeaders = [
                "From: {$contact_settings['email_from_name']} <{$contact_settings['receiving_email']}>",
                "Content-Type: text/plain; charset=UTF-8"
            ];
            
            mail($contactData['email'], $contact_settings['auto_reply_subject'], $autoReplyBody, implode("\r\n", $autoReplyHeaders));
        }
        
        return $success;
        
    } catch (Exception $e) {
        logAttempt('MAIL_ERROR', getClientIP(), $e->getMessage());
        return false;
    }
}

// Main processing
try {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        sendResponse(false, 'Method not allowed');
    }
    
    $clientIP = getClientIP();
    
    // 1. CSRF Token Validation
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])) {
        logAttempt('CSRF_MISSING', $clientIP);
        sendResponse(false, 'Security token missing');
    }
    
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        logAttempt('CSRF_INVALID', $clientIP);
        sendResponse(false, 'Invalid security token');
    }
    
    // 2. Honeypot Detection
    if (!empty($_POST['website']) || !empty($_POST['phone_number'])) {
        logAttempt('HONEYPOT_TRIGGERED', $clientIP, 'website: ' . ($_POST['website'] ?? '') . ', phone: ' . ($_POST['phone_number'] ?? ''));
        // Fake success response to fool bots
        sendResponse(true, 'Thank you for your message!');
    }
    
    // 3. Rate Limiting
    if (!checkRateLimit($clientIP)) {
        logAttempt('RATE_LIMITED', $clientIP);
        sendResponse(false, 'Too many requests. Please wait before submitting again.');
    }
    
    // 4. Input Validation & Sanitization
    $first_name = trim(filter_var($_POST['first_name'] ?? '', FILTER_SANITIZE_STRING));
    $last_name = trim(filter_var($_POST['last_name'] ?? '', FILTER_SANITIZE_STRING));
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $category = trim(filter_var($_POST['category'] ?? '', FILTER_SANITIZE_STRING));
    $subject = trim(filter_var($_POST['subject'] ?? '', FILTER_SANITIZE_STRING));
    $message = trim(filter_var($_POST['message'] ?? '', FILTER_SANITIZE_STRING));
    
    $field_errors = [];
    
    // Validate required fields
    if (empty($first_name)) $field_errors['first_name'] = 'First name is required';
    if (empty($last_name)) $field_errors['last_name'] = 'Last name is required';
    if (empty($email)) $field_errors['email'] = 'Email is required';
    if (empty($category)) $field_errors['category'] = 'Category is required';
    if (empty($subject)) $field_errors['subject'] = 'Subject is required';
    if (empty($message)) $field_errors['message'] = 'Message is required';
    
    // Validate field lengths and formats
    if (!empty($first_name) && (strlen($first_name) > 50 || !preg_match('/^[a-zA-Z\s]+$/', $first_name))) {
        $field_errors['first_name'] = 'First name must contain only letters and be under 50 characters';
    }
    
    if (!empty($last_name) && (strlen($last_name) > 50 || !preg_match('/^[a-zA-Z\s]+$/', $last_name))) {
        $field_errors['last_name'] = 'Last name must contain only letters and be under 50 characters';
    }
    
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $field_errors['email'] = 'Invalid email address';
    }
    
    if (!empty($subject) && strlen($subject) > 200) {
        $field_errors['subject'] = 'Subject must be under 200 characters';
    }
    
    if (!empty($message) && (strlen($message) < 10 || strlen($message) > 2000)) {
        $field_errors['message'] = 'Message must be between 10 and 2000 characters';
    }
    
    // Valid categories
    $valid_categories = ['general', 'technical', 'business', 'feedback', 'other'];
    if (!empty($category) && !in_array($category, $valid_categories)) {
        $field_errors['category'] = 'Invalid category selected';
    }
    
    if (!empty($field_errors)) {
        logAttempt('VALIDATION_FAILED', $clientIP, implode(',', array_keys($field_errors)));
        sendResponse(false, 'Please correct the errors below', $field_errors);
    }
    
    // 5. Content Analysis
    $spamFlags = analyzeContent($subject, $message);
    if (!empty($spamFlags)) {
        logAttempt('SPAM_DETECTED', $clientIP, implode(',', $spamFlags));
        // Fake success response to fool spam bots
        sendResponse(true, 'Thank you for your message!');
    }
    
    // 6. Database Storage
    try {
        // Prepare extra data as JSON
        $extra = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'category' => $category,
            'ip_address' => $clientIP,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'full_name' => $first_name . ' ' . $last_name
        ];
        
        // Insert into database
        $stmt = $pdo->prepare('INSERT INTO contact_form_messages (email, subject, msg, extra, submit_date, status) VALUES (?, ?, ?, ?, NOW(), ?)');
        $stmt->execute([
            $email,
            $subject,
            $message,
            json_encode($extra),
            'Unread'
        ]);
        
        $message_id = $pdo->lastInsertId();
        
    } catch (PDOException $e) {
        logAttempt('DATABASE_ERROR', $clientIP, $e->getMessage());
        sendResponse(false, 'Database error occurred. Please try again later.');
    }
    
    // 7. Send Email Notification
    $contactData = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'category' => $category,
        'subject' => $subject,
        'message' => $message
    ];
    
    $emailSent = sendEmailNotification($contactData);
    
    if ($emailSent) {
        logAttempt('SUCCESS', $clientIP, "Message ID: $message_id, Email: $email, Category: $category");
        
        // Regenerate CSRF token for security
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        sendResponse(true, 'Thank you for your message! We will get back to you soon.');
    } else {
        logAttempt('EMAIL_FAILED', $clientIP, "Message ID: $message_id, stored in database but email failed");
        sendResponse(true, 'Your message has been received and will be reviewed shortly.');
    }
    
} catch (Exception $e) {
    logAttempt('ERROR', getClientIP(), $e->getMessage());
    sendResponse(false, 'An unexpected error occurred. Please try again later.');
}
?>
