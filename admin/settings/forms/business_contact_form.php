<?php
/**
 * Business Contact Settings Form
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: business_contact_form.php
 * LOCATION: /public_html/admin/settings/forms/
 * PURPOSE: Professional contact information management
 * 
 * Manages business contact details, addresses, and communication preferences
 * through the unified database settings system.
 * 
 * FEATURES:
 * - Primary contact information (email, phone, address)
 * - Social media links and profiles
 * - Business hours configuration
 * - Multiple location management
 * - Emergency contact settings
 * - Professional styling matching existing admin interface
 * 
 * CREATED: 2025-08-18
 * VERSION: 1.0
 */

// Initialize session and security
session_start();
require_once __DIR__ . '/../../../../private/gws-universal-config.php';
require_once __DIR__ . '/../../../../private/classes/SettingsManager.php';
require_once __DIR__ . '/../../../../private/classes/SecurityHelper.php';
include_once '../../assets/includes/main.php';

// Security check for admin access
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['Admin', 'Editor', 'Developer'])) {
    header('Location: ../../index.php');
    exit();
}

// Initialize settings manager
$settingsManager = new SettingsManager($pdo);

// Handle form submissions
$message = '';
$message_type = '';
$errors = [];
$csrf_token = SecurityHelper::getCsrfToken('contact_settings');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_by = $account_loggedin['username'] ?? 'admin';

    if (!SecurityHelper::validateCsrf('contact_settings', $_POST['csrf_token'] ?? '')) {
        $message = 'Security error: invalid or expired form token. Please retry.';
        $message_type = 'error';
    } else {
        // Validation specification (UI -> sanitized)
        $spec = [
            'primary_email' => ['type' => 'email', 'required' => true],
            'primary_phone' => ['type' => 'string', 'max' => 50],
            'primary_address' => ['type' => 'string', 'max' => 255],
            'city' => ['type' => 'string', 'max' => 100],
            'state' => ['type' => 'string', 'max' => 100],
            'zipcode' => ['type' => 'string', 'max' => 20],
            'country' => ['type' => 'string', 'max' => 100],
            'website_url' => ['type' => 'url', 'max' => 255],
            'business_hours' => ['type' => 'string', 'max' => 255],
            'timezone' => ['type' => 'string', 'max' => 50],
            'facebook_url' => ['type' => 'url', 'max' => 255],
            'twitter_url' => ['type' => 'url', 'max' => 255],
            'linkedin_url' => ['type' => 'url', 'max' => 255],
            'instagram_url' => ['type' => 'url', 'max' => 255]
        ];

        $validated = SecurityHelper::validatePayload($spec, $_POST, $errors);

        if (!empty($errors['primary_email'])) {
            $message = 'Valid primary email is required';
            $message_type = 'error';
        } elseif ($errors) {
            $message = 'Validation errors: ' . implode(', ', array_keys($errors));
            $message_type = 'error';
        } else {
            // Map UI -> DB columns for setting_contact_info
            $dbData = array(
                'contact_email' => $validated['primary_email'],
                'contact_phone' => $validated['primary_phone'],
                'contact_address' => $validated['primary_address'],
                'contact_city' => $validated['city'],
                'contact_state' => $validated['state'],
                'contact_zipcode' => $validated['zipcode'],
                'contact_country' => $validated['country'],
                'business_hours' => $validated['business_hours'],
                'time_zone' => $validated['timezone']
                // Social handled separately (could be setting_social_media) for now store via generic update(s) if needed
            );

            try {
                $result = $settingsManager->updateContactInfo($dbData, $updated_by);
                // Optionally, update social media table if present & values submitted
                $social = array_filter(array(
                    'facebook_url' => $validated['facebook_url'],
                    'twitter_url' => $validated['twitter_url'],
                    'linkedin_url' => $validated['linkedin_url'],
                    'instagram_url' => $validated['instagram_url']
                ));
                if ($social) {
                    $settingsManager->updateBusinessSocialMedia($social, $updated_by);
                }
                if ($result) {
                    $message = 'Contact information updated successfully.';
                    $message_type = 'success';
                } else {
                    $message = 'Error updating contact information. Please check the error logs.';
                    $message_type = 'error';
                }
            } catch (Exception $e) {
                $message = 'Error: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
    $csrf_token = SecurityHelper::getCsrfToken('contact_settings');
}

// Get current contact settings
try {
    $current_settings = $settingsManager->getCompleteContactInfo();
    // Merge in business social media profiles for display
    $social_current = $settingsManager->getBusinessSocialMedia();
    if (is_array($social_current)) {
        $current_settings = array_merge($current_settings, $social_current);
    }
} catch (Exception $e) {
    $current_settings = [];
    error_log("Error loading contact settings: " . $e->getMessage());
}

// Utility function
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Page configuration
$page_title = 'Business Contact Settings';
?>

<?= template_admin_header($page_title, 'settings', 'contact') ?>

<div class="content-title" role="banner" aria-label="Contact Settings Header">
    <div class="title">
        <div class="icon">
            <i class="bi bi-telephone" style="font-size: 18px;"></i>
        </div>
        <div class="txt">
            <h2>Business Contact Settings</h2>
            <p>Manage company-wide contact information and official business social media profiles. Individual team member bios & social links are managed separately.</p>
        </div>
    </div>
    <div class="btn-group">
        <a href="../settings_dash.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
        <i class="bi bi-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form method="POST" class="settings-form" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <div class="row">
        <!-- Primary Contact Information -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-badge me-2"></i>Primary Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="primary_email" class="form-label">Primary Email Address *</label>
                        <input type="email" class="form-control" id="primary_email" name="primary_email" 
                               value="<?= htmlspecialchars($current_settings['primary_email'] ?? '') ?>" 
                               required>
                        <?php if (!empty($errors['primary_email'])): ?><div class="text-danger small">Please provide a valid email.</div><?php endif; ?>
                        <div class="form-text">Main business email for contact forms and communications</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="primary_phone" class="form-label">Primary Phone Number</label>
                        <input type="tel" class="form-control" id="primary_phone" name="primary_phone" 
                               value="<?= htmlspecialchars($current_settings['primary_phone'] ?? '') ?>" 
                               placeholder="+1 (555) 123-4567">
                        <div class="form-text">Main business phone number</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="website_url" class="form-label">Website URL</label>
                        <input type="url" class="form-control" id="website_url" name="website_url" 
                               value="<?= htmlspecialchars($current_settings['website_url'] ?? '') ?>" 
                               placeholder="https://www.yourwebsite.com">
                        <div class="form-text">Your primary website URL</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="business_hours" class="form-label">Business Hours</label>
                            <input type="text" class="form-control" id="business_hours" name="business_hours" 
                                   value="<?= htmlspecialchars($current_settings['business_hours'] ?? 'Mon-Fri 9AM-5PM') ?>" 
                                   placeholder="Mon-Fri 9AM-5PM">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone" name="timezone">
                                <option value="America/New_York" <?= ($current_settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>Eastern Time (ET)</option>
                                <option value="America/Chicago" <?= ($current_settings['timezone'] ?? '') === 'America/Chicago' ? 'selected' : '' ?>>Central Time (CT)</option>
                                <option value="America/Denver" <?= ($current_settings['timezone'] ?? '') === 'America/Denver' ? 'selected' : '' ?>>Mountain Time (MT)</option>
                                <option value="America/Los_Angeles" <?= ($current_settings['timezone'] ?? '') === 'America/Los_Angeles' ? 'selected' : '' ?>>Pacific Time (PT)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Business Address -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-geo-alt me-2"></i>Business Address
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="primary_address" class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="primary_address" name="primary_address" 
                               value="<?= htmlspecialchars($current_settings['primary_address'] ?? '') ?>" 
                               placeholder="123 Business Street">
                        <div class="form-text">Physical business address</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" 
                                   value="<?= htmlspecialchars($current_settings['city'] ?? '') ?>" 
                                   placeholder="Your City">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">State/Province</label>
                            <input type="text" class="form-control" id="state" name="state" 
                                   value="<?= htmlspecialchars($current_settings['state'] ?? '') ?>" 
                                   placeholder="State">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="zipcode" class="form-label">ZIP/Postal Code</label>
                            <input type="text" class="form-control" id="zipcode" name="zipcode" 
                                   value="<?= htmlspecialchars($current_settings['zipcode'] ?? '') ?>" 
                                   placeholder="12345">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country</label>
                            <select class="form-select" id="country" name="country">
                                <option value="United States" <?= ($current_settings['country'] ?? '') === 'United States' ? 'selected' : '' ?>>United States</option>
                                <option value="Canada" <?= ($current_settings['country'] ?? '') === 'Canada' ? 'selected' : '' ?>>Canada</option>
                                <option value="United Kingdom" <?= ($current_settings['country'] ?? '') === 'United Kingdom' ? 'selected' : '' ?>>United Kingdom</option>
                                <option value="Australia" <?= ($current_settings['country'] ?? '') === 'Australia' ? 'selected' : '' ?>>Australia</option>
                                <option value="Other" <?= ($current_settings['country'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Business Social Media Links -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-share me-2"></i>Business Social Media Profiles
            </h5>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-4">
                These links represent the official company profiles shown in global site areas (footer, contact page, marketing blocks). Do NOT enter individual employee profiles here. Team member social links will be editable on the forthcoming <strong>Team Members</strong> management screen.
            </p>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="facebook_url" class="form-label">
                        <i class="bi bi-facebook me-1"></i>Official Facebook Page
                    </label>
                    <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                           value="<?= htmlspecialchars($current_settings['facebook_url'] ?? '') ?>" 
                           placeholder="https://facebook.com/yourpage">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="twitter_url" class="form-label">
                        <i class="bi bi-twitter me-1"></i>Official X (Twitter) Profile
                    </label>
                    <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                           value="<?= htmlspecialchars($current_settings['twitter_url'] ?? '') ?>" 
                           placeholder="https://twitter.com/yourhandle">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="linkedin_url" class="form-label">
                        <i class="bi bi-linkedin me-1"></i>Official LinkedIn Page
                    </label>
                    <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" 
                           value="<?= htmlspecialchars($current_settings['linkedin_url'] ?? '') ?>" 
                           placeholder="https://linkedin.com/company/yourcompany">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="instagram_url" class="form-label">
                        <i class="bi bi-instagram me-1"></i>Official Instagram Profile
                    </label>
                    <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                           value="<?= htmlspecialchars($current_settings['instagram_url'] ?? '') ?>" 
                           placeholder="https://instagram.com/yourhandle">
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg me-2"></i>Save Contact Settings
        </button>
        <a href="../settings_dash.php" class="btn btn-secondary">
            <i class="bi bi-x-lg me-2"></i>Cancel
        </a>
    </div>
</form>

<style>
.settings-form {
    max-width: none;
}

.card {
    border: 1px solid rgba(0,0,0,.125);
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
}

.card-header {
    background-color: rgba(0,0,0,.03);
    border-bottom: 1px solid rgba(0,0,0,.125);
    padding: 1rem 1.25rem;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-text {
    font-size: 0.875em;
    color: #6c757d;
}

.btn {
    border-radius: 0.375rem;
    font-weight: 500;
    padding: 0.5rem 1rem;
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.content-title .title .icon i {
    color: #0d6efd;
}

.alert {
    border-radius: 0.375rem;
    border: 1px solid transparent;
}
</style>

<?= template_admin_footer() ?>
