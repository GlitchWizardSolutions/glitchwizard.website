<?php
// Hero form handler - sends quick offer requests to business email
session_start();

// Basic security checks
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

// CSRF token validation
if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    header('Location: /?error=security');
    exit;
}

// Include database settings to get contact email
include_once '../assets/includes/settings/database_settings.php';

// Sanitize and validate input
$name = filter_var(trim($_POST['name'] ?? ''), FILTER_SANITIZE_STRING);
$phone = filter_var(trim($_POST['phone'] ?? ''), FILTER_SANITIZE_STRING);
$address = filter_var(trim($_POST['address'] ?? ''), FILTER_SANITIZE_STRING);

// Basic validation
$errors = [];
if (empty($name) || strlen($name) < 2) {
    $errors[] = 'Please enter your name';
}
if (empty($phone) || strlen($phone) < 10) {
    $errors[] = 'Please enter a valid phone number';
}
if (empty($address) || strlen($address) < 5) {
    $errors[] = 'Please enter your address';
}

// Honeypot check (simple bot protection)
if (!empty($_POST['website']) || !empty($_POST['email_check'])) {
    header('Location: /?success=1'); // Fake success for bots
    exit;
}

if (!empty($errors)) {
    header('Location: /?error=' . urlencode(implode(', ', $errors)));
    exit;
}

// Prepare email
$to = $contact_email ?? 'info@example.com';
$subject = 'New Honest Offer Request - ' . ($business_name ?? 'Website');
$message = "New offer request received:\n\n";
$message .= "Name: " . $name . "\n";
$message .= "Phone: " . $phone . "\n";
$message .= "Address: " . $address . "\n";
$message .= "Submitted: " . date('Y-m-d H:i:s') . "\n";
$message .= "IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n";

$headers = [
    'From: ' . ($contact_email ?? 'noreply@example.com'),
    'Reply-To: ' . ($contact_email ?? 'noreply@example.com'),
    'X-Mailer: PHP/' . phpversion(),
    'Content-Type: text/plain; charset=UTF-8'
];

// Send email
if (mail($to, $subject, $message, implode("\r\n", $headers))) {
    header('Location: /?success=offer_submitted');
} else {
    header('Location: /?error=email_failed');
}
exit;
?>
