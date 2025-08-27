<?php
/**
 * Enhanced Secure Contact Form Processor with reCAPTCHA v3 Support
 * 
 * Multi-layered spam protection:
 * 1. CSRF Token Validation
 * 2. Honeypot Fields Detection
 * 3. Rate Limiting (IP-based)
 * 4. Content Analysis & Spam Detection
 * 5. reCAPTCHA v3 Verification (optional)
 * 6. Input Validation & Sanitization
 * 
 * All protections are invisible to legitimate users.
 */

// Start session for CSRF protection
if (!isset($_SESSION)) {
    session_start();
}

// Configuration
$config = [
    'receiving_email' => 'your-email@domain.com', // UPDATE THIS
    'rate_limit_max' => 3, // Max submissions per hour per IP
    'rate_limit_window' => 3600, // 1 hour in seconds
    'min_submit_interval' => 10, // Minimum seconds between submissions
    'log_file' => __DIR__ . '/../logs/contact_log.txt',
    'blocked_words' => [
        'viagra', 'cialis', 'loan', 'casino', 'poker', 'bitcoin', 'crypto',
        'make money', 'work from home', 'business opportunity', 'free money',
        'click here', 'limited time', 'act now', 'congratulations',
        'winner', 'prize', 'lottery', 'inheritance', 'nigerian prince'
    ],
    'max_links' => 2, // Maximum links allowed in message
    'recaptcha_secret' => '', // Your reCAPTCHA v3 secret key (optional)
    'recaptcha_min_score' => 0.5 // Minimum score for reCAPTCHA v3 (0.0-1.0)
];

// Response function
function sendResponse($success, $message, $redirect = '') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'redirect' => $redirect
    ]);
    exit;
}

// Logging function
function logAttempt($type, $ip, $details = '') {
    global $config;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $type from $ip: $details" . PHP_EOL;
    
    // Ensure logs directory exists
    $logDir = dirname($config['log_file']);
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($config['log_file'], $logEntry, FILE_APPEND | LOCK_EX);
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
    global $config;
    $rateLimitFile = sys_get_temp_dir() . '/contact_rate_' . md5($ip) . '.json';
    
    $now = time();
    $data = [];
    
    if (file_exists($rateLimitFile)) {
        $content = file_get_contents($rateLimitFile);
        $data = json_decode($content, true) ?: [];
    }
    
    // Clean old entries
    $data = array_filter($data, function($timestamp) use ($now, $config) {
        return ($now - $timestamp) < $config['rate_limit_window'];
    });
    
    // Check submission count
    if (count($data) >= $config['rate_limit_max']) {
        return false;
    }
    
    // Check minimum interval
    if (!empty($data)) {
        $lastSubmission = max($data);
        if (($now - $lastSubmission) < $config['min_submit_interval']) {
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
    global $config;
    
    $fullText = strtolower($subject . ' ' . $message);
    $flags = [];
    
    // Check for blocked words
    foreach ($config['blocked_words'] as $word) {
        if (strpos($fullText, strtolower($word)) !== false) {
            $flags[] = "blocked_word:$word";
        }
    }
    
    // Count links
    $linkCount = preg_match_all('/https?:\/\/|www\./i', $fullText);
    if ($linkCount > $config['max_links']) {
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
            $flags[] = "suspicious_pattern:" . str_replace('/', '', $pattern);
        }
    }
    
    // Check message length ratio (very short or very long messages are suspicious)
    $messageLength = strlen(trim($message));
    if ($messageLength < 10) {
        $flags[] = "message_too_short:$messageLength";
    } elseif ($messageLength > 1500) {
        $flags[] = "message_too_long:$messageLength";
    }
    
    return $flags;
}

// reCAPTCHA v3 verification
function verifyRecaptcha($token) {
    global $config;
    
    if (empty($config['recaptcha_secret']) || empty($token)) {
        return true; // Skip if not configured
    }
    
    $data = [
        'secret' => $config['recaptcha_secret'],
        'response' => $token,
        'remoteip' => getClientIP()
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200 || !$response) {
        logAttempt('RECAPTCHA_ERROR', getClientIP(), 'HTTP ' . $httpCode);
        return false;
    }
    
    $result = json_decode($response, true);
    
    if (!$result['success']) {
        logAttempt('RECAPTCHA_FAILED', getClientIP(), implode(',', $result['error-codes'] ?? []));
        return false;
    }
    
    // Check score for v3
    if (isset($result['score'])) {
        if ($result['score'] < $config['recaptcha_min_score']) {
            logAttempt('RECAPTCHA_LOW_SCORE', getClientIP(), 'Score: ' . $result['score']);
            return false;
        }
    }
    
    return true;
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
    $name = trim(filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING));
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $subject = trim(filter_var($_POST['subject'] ?? '', FILTER_SANITIZE_STRING));
    $message = trim(filter_var($_POST['message'] ?? '', FILTER_SANITIZE_STRING));
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        logAttempt('VALIDATION_FAILED', $clientIP, 'missing_fields');
        sendResponse(false, 'All fields are required');
    }
    
    // Validate field lengths
    if (strlen($name) > 100 || strlen($subject) > 200 || strlen($message) > 2000) {
        logAttempt('VALIDATION_FAILED', $clientIP, 'field_too_long');
        sendResponse(false, 'One or more fields exceed maximum length');
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        logAttempt('VALIDATION_FAILED', $clientIP, 'invalid_email');
        sendResponse(false, 'Invalid email address');
    }
    
    // 5. Content Analysis
    $spamFlags = analyzeContent($subject, $message);
    if (!empty($spamFlags)) {
        logAttempt('SPAM_DETECTED', $clientIP, implode(',', $spamFlags));
        // Fake success response to fool spam bots
        sendResponse(true, 'Thank you for your message!');
    }
    
    // 6. reCAPTCHA v3 Verification (if enabled)
    if (!empty($config['recaptcha_secret'])) {
        $recaptchaToken = $_POST['recaptcha_token'] ?? '';
        if (!verifyRecaptcha($recaptchaToken)) {
            sendResponse(false, 'Please try again');
        }
    }
    
    // All validations passed - send email
    $to = $config['receiving_email'];
    $emailSubject = "Contact Form: " . $subject;
    
    $emailBody = "New contact form submission:\n\n";
    $emailBody .= "Name: $name\n";
    $emailBody .= "Email: $email\n";
    $emailBody .= "Subject: $subject\n\n";
    $emailBody .= "Message:\n$message\n\n";
    $emailBody .= "---\n";
    $emailBody .= "IP Address: $clientIP\n";
    $emailBody .= "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n";
    $emailBody .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
    
    $headers = [
        "From: $email",
        "Reply-To: $email",
        "X-Mailer: PHP/" . phpversion(),
        "Content-Type: text/plain; charset=UTF-8"
    ];
    
    $success = mail($to, $emailSubject, $emailBody, implode("\r\n", $headers));
    
    if ($success) {
        logAttempt('SUCCESS', $clientIP, "from: $email, subject: $subject");
        
        // Regenerate CSRF token for security
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        sendResponse(true, 'Thank you for your message! We will get back to you soon.');
    } else {
        logAttempt('MAIL_FAILED', $clientIP, "from: $email");
        sendResponse(false, 'Sorry, there was an error sending your message. Please try again later.');
    }
    
} catch (Exception $e) {
    logAttempt('ERROR', getClientIP(), $e->getMessage());
    sendResponse(false, 'An unexpected error occurred. Please try again later.');
}
?>
