<?php
/**
 * Contact Form Settings Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: contact_settings.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Manage contact form configuration settings
 * 
 * CREATED: 2025-08-08
 * UPDATED: 2025-08-08
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Email configuration settings
 * - SMTP settings management
 * - Spam protection configuration
 * - Auto-reply settings
 * - Contact form behavior options
 */

include_once '../assets/includes/main.php';

// Load persistent contact settings from config file
$settings_file = PROJECT_ROOT . '/public_html/assets/includes/settings/contact_settings.php';

// Create settings directory if it doesn't exist
$settings_dir = PROJECT_ROOT . '/public_html/assets/includes/settings';
if (!file_exists($settings_dir)) {
    mkdir($settings_dir, 0755, true);
}

// Initialize default settings if file doesn't exist
if (!file_exists($settings_file)) {
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
    
    $php_code = "<?php\n// Contact Form Settings\n// Last updated: " . date('Y-m-d H:i:s') . "\n\n";
    $php_code .= "\$contact_settings = " . var_export($default_settings, true) . ";\n";
    file_put_contents($settings_file, $php_code);
}

// Load settings
if (file_exists($settings_file)) {
    include $settings_file;
    $settings = isset($contact_settings) ? $contact_settings : [];
} else {
    $settings = [];
}

// Process form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate and sanitize input
        $new_settings = [
            'receiving_email' => filter_var($_POST['receiving_email'] ?? '', FILTER_SANITIZE_EMAIL),
            'smtp_enabled' => isset($_POST['smtp_enabled']),
            'smtp_host' => trim($_POST['smtp_host'] ?? ''),
            'smtp_port' => (int)($_POST['smtp_port'] ?? 587),
            'smtp_username' => trim($_POST['smtp_username'] ?? ''),
            'smtp_password' => trim($_POST['smtp_password'] ?? ''),
            'smtp_encryption' => in_array($_POST['smtp_encryption'] ?? '', ['tls', 'ssl']) ? $_POST['smtp_encryption'] : 'tls',
            'email_from_name' => trim($_POST['email_from_name'] ?? 'Contact Form'),
            'email_subject_prefix' => trim($_POST['email_subject_prefix'] ?? '[Contact Form]'),
            'auto_reply_enabled' => isset($_POST['auto_reply_enabled']),
            'auto_reply_subject' => trim($_POST['auto_reply_subject'] ?? ''),
            'auto_reply_message' => trim($_POST['auto_reply_message'] ?? ''),
            'rate_limit_max' => max(1, (int)($_POST['rate_limit_max'] ?? 3)),
            'rate_limit_window' => max(300, (int)($_POST['rate_limit_window'] ?? 3600)),
            'min_submit_interval' => max(5, (int)($_POST['min_submit_interval'] ?? 10)),
            'blocked_words' => array_filter(array_map('trim', explode("\n", $_POST['blocked_words'] ?? ''))),
            'max_links' => max(0, (int)($_POST['max_links'] ?? 2)),
            'enable_logging' => isset($_POST['enable_logging'])
        ];
        
        // Validate email
        if (!filter_var($new_settings['receiving_email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid receiving email address');
        }
        
        // Save settings
        $php_code = "<?php\n// Contact Form Settings\n// Last updated: " . date('Y-m-d H:i:s') . "\n\n";
        $php_code .= "\$contact_settings = " . var_export($new_settings, true) . ";\n";
        
        if (file_put_contents($settings_file, $php_code)) {
            $settings = $new_settings;
            $message = 'Contact form settings updated successfully!';
            $message_type = 'success';
        } else {
            throw new Exception('Failed to save settings file');
        }
        
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'danger';
    }
}

// Page title
$page_title = 'Contact Form Settings';
?>

<?= template_admin_header($page_title, 'settings', 'contact') ?>

<div class="content-title">
    <div class="title">
        <div class="icon">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="txt">
            <h2>Contact Form Settings</h2>
            <p>Configure contact form behavior, email settings, and spam protection.</p>
        </div>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
    <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
    <?= htmlspecialchars($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="content-block">
    <form method="post" novalidate>
        
        <!-- Email Configuration Tab -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Email Configuration</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="receiving_email" class="form-label">Receiving Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="receiving_email" name="receiving_email" 
                                value="<?= htmlspecialchars($settings['receiving_email'] ?? '') ?>" required>
                            <div class="form-text">Email address where contact form submissions will be sent</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email_from_name" class="form-label">From Name</label>
                            <input type="text" class="form-control" id="email_from_name" name="email_from_name" 
                                value="<?= htmlspecialchars($settings['email_from_name'] ?? 'Contact Form') ?>">
                            <div class="form-text">Name displayed as the sender in email notifications</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email_subject_prefix" class="form-label">Subject Prefix</label>
                    <input type="text" class="form-control" id="email_subject_prefix" name="email_subject_prefix" 
                        value="<?= htmlspecialchars($settings['email_subject_prefix'] ?? '[Contact Form]') ?>">
                    <div class="form-text">Prefix added to email subject lines (e.g., [Contact Form] Subject)</div>
                </div>
            </div>
        </div>

        <!-- SMTP Settings Tab -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-server me-2"></i>SMTP Settings</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="smtp_enabled" name="smtp_enabled" 
                            <?= ($settings['smtp_enabled'] ?? false) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="smtp_enabled">
                            Enable SMTP (recommended for better email delivery)
                        </label>
                    </div>
                </div>
                
                <div id="smtp_settings" style="<?= ($settings['smtp_enabled'] ?? false) ? '' : 'display: none;' ?>">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="smtp_host" class="form-label">SMTP Host</label>
                                <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                    value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>" 
                                    placeholder="smtp.gmail.com">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="smtp_port" class="form-label">Port</label>
                                <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                    value="<?= htmlspecialchars($settings['smtp_port'] ?? '587') ?>" min="1" max="65535">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="smtp_encryption" class="form-label">Encryption</label>
                                <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                    <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                                    <option value="ssl" <?= ($settings['smtp_encryption'] ?? 'tls') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="smtp_username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                                    value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>" 
                                    autocomplete="username">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="smtp_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                    value="<?= htmlspecialchars($settings['smtp_password'] ?? '') ?>" 
                                    autocomplete="current-password">
                                <div class="form-text">Use app-specific passwords for Gmail/Outlook</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto-Reply Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-reply me-2"></i>Auto-Reply Settings</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="auto_reply_enabled" name="auto_reply_enabled" 
                            <?= ($settings['auto_reply_enabled'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="auto_reply_enabled">
                            Send automatic reply to visitors
                        </label>
                    </div>
                </div>
                
                <div id="auto_reply_settings" style="<?= ($settings['auto_reply_enabled'] ?? true) ? '' : 'display: none;' ?>">
                    <div class="mb-3">
                        <label for="auto_reply_subject" class="form-label">Auto-Reply Subject</label>
                        <input type="text" class="form-control" id="auto_reply_subject" name="auto_reply_subject" 
                            value="<?= htmlspecialchars($settings['auto_reply_subject'] ?? 'Thank you for contacting us') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="auto_reply_message" class="form-label">Auto-Reply Message</label>
                        <textarea class="form-control" id="auto_reply_message" name="auto_reply_message" rows="4"><?= htmlspecialchars($settings['auto_reply_message'] ?? 'We have received your message and will respond as soon as possible.') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Spam Protection Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Spam Protection</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="rate_limit_max" class="form-label">Max Submissions Per Hour</label>
                            <input type="number" class="form-control" id="rate_limit_max" name="rate_limit_max" 
                                value="<?= htmlspecialchars($settings['rate_limit_max'] ?? '3') ?>" min="1" max="100">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="min_submit_interval" class="form-label">Min Interval (seconds)</label>
                            <input type="number" class="form-control" id="min_submit_interval" name="min_submit_interval" 
                                value="<?= htmlspecialchars($settings['min_submit_interval'] ?? '10') ?>" min="5" max="300">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="max_links" class="form-label">Max Links Allowed</label>
                            <input type="number" class="form-control" id="max_links" name="max_links" 
                                value="<?= htmlspecialchars($settings['max_links'] ?? '2') ?>" min="0" max="10">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="blocked_words" class="form-label">Blocked Words (one per line)</label>
                    <textarea class="form-control" id="blocked_words" name="blocked_words" rows="6" 
                        placeholder="viagra&#10;casino&#10;free money"><?= htmlspecialchars(implode("\n", $settings['blocked_words'] ?? [])) ?></textarea>
                    <div class="form-text">Messages containing these words will be automatically blocked</div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="enable_logging" name="enable_logging" 
                            <?= ($settings['enable_logging'] ?? true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="enable_logging">
                            Enable logging (recommended for monitoring spam attempts)
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="../settings/" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Settings
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Save Settings
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // SMTP settings toggle
    const smtpEnabled = document.getElementById('smtp_enabled');
    const smtpSettings = document.getElementById('smtp_settings');
    
    smtpEnabled.addEventListener('change', function() {
        smtpSettings.style.display = this.checked ? 'block' : 'none';
    });
    
    // Auto-reply settings toggle
    const autoReplyEnabled = document.getElementById('auto_reply_enabled');
    const autoReplySettings = document.getElementById('auto_reply_settings');
    
    autoReplyEnabled.addEventListener('change', function() {
        autoReplySettings.style.display = this.checked ? 'block' : 'none';
    });
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const email = document.getElementById('receiving_email');
        if (!email.value || !email.checkValidity()) {
            e.preventDefault();
            email.focus();
            alert('Please enter a valid receiving email address.');
            return false;
        }
    });
});
</script>

<?= template_admin_footer() ?>
