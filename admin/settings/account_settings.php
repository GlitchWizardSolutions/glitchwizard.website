<?php
/**
 * Account Settings Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: account_settings.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Database-driven user account configuration management
 * 
 * FEATURES:
 * - User registration settings
 * - Account permissions
 * - Login/logout configuration
 * - Password policies
 * - Account verification settings
 * 
 * CREATED: 2025-08-18
 * VERSION: 1.0
 */

// Initialize session and security
session_start();
require_once __DIR__ . '/../../../private/gws-universal-config.php';
require_once __DIR__ . '/../../../private/classes/SettingsManager.php';
include_once '../assets/includes/main.php';

// Security check for admin access
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['Admin', 'Developer'])) {
    header('Location: ../index.php');
    exit();
}

// Initialize settings manager
$settingsManager = new SettingsManager($pdo);

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_by = $account_loggedin['username'] ?? 'admin';
    
    try {
        $data = [
            'allow_registration' => isset($_POST['allow_registration']) ? 1 : 0,
            'require_email_verification' => isset($_POST['require_email_verification']) ? 1 : 0,
            'min_password_length' => (int)$_POST['min_password_length'],
            'require_password_uppercase' => isset($_POST['require_password_uppercase']) ? 1 : 0,
            'require_password_numbers' => isset($_POST['require_password_numbers']) ? 1 : 0,
            'require_password_symbols' => isset($_POST['require_password_symbols']) ? 1 : 0,
            'default_user_role' => sanitize_input($_POST['default_user_role']),
            'session_timeout' => (int)$_POST['session_timeout'],
            'max_login_attempts' => (int)$_POST['max_login_attempts'],
            'lockout_duration' => (int)$_POST['lockout_duration']
        ];
        
        // Use SettingsManager to update account settings
        $result = $settingsManager->updateAccountSettings($data, $updated_by);
        
        if ($result) {
            $message = 'Account settings updated successfully.';
            $message_type = 'success';
        } else {
            $message = 'Error updating account settings. Please check the error logs.';
            $message_type = 'error';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get current account settings
try {
    $current_settings = $settingsManager->getAccountSettings();
} catch (Exception $e) {
    $current_settings = [];
    $message = 'Warning: Could not load current account settings. ' . $e->getMessage();
    $message_type = 'warning';
}

// Utility function
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

$page_title = 'Account Settings';
?>

<?= template_admin_header($page_title, 'settings', 'accounts') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0"><i class="bi bi-people"></i> Account Settings</h1>
                    <p class="text-muted">Configure user registration, authentication, and account policies</p>
                </div>
                <a href="settings_dash.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Settings
                </a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $message_type === 'success' ? 'success' : ($message_type === 'warning' ? 'warning' : 'danger') ?> alert-dismissible fade show" role="alert">
                    <i class="bi bi-<?= $message_type === 'success' ? 'check-circle' : ($message_type === 'warning' ? 'exclamation-triangle' : 'x-circle') ?>"></i>
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-person-plus"></i> Registration Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="allow_registration" name="allow_registration" 
                                               <?= !empty($current_settings['allow_registration']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="allow_registration">
                                            Allow new user registration
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="require_email_verification" name="require_email_verification" 
                                               <?= !empty($current_settings['require_email_verification']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="require_email_verification">
                                            Require email verification
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="default_user_role" class="form-label">Default User Role</label>
                                    <select class="form-select" id="default_user_role" name="default_user_role" required>
                                        <option value="User" <?= ($current_settings['default_user_role'] ?? '') === 'User' ? 'selected' : '' ?>>User</option>
                                        <option value="Client" <?= ($current_settings['default_user_role'] ?? '') === 'Client' ? 'selected' : '' ?>>Client</option>
                                        <option value="Editor" <?= ($current_settings['default_user_role'] ?? '') === 'Editor' ? 'selected' : '' ?>>Editor</option>
                                    </select>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-shield-lock"></i> Password Policies
                            </h5>
                        </div>
                        <div class="card-body">
                                <div class="mb-3">
                                    <label for="min_password_length" class="form-label">Minimum Password Length</label>
                                    <input type="number" class="form-control" id="min_password_length" name="min_password_length" 
                                           value="<?= $current_settings['min_password_length'] ?? 8 ?>" min="6" max="20" required>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="require_password_uppercase" name="require_password_uppercase" 
                                               <?= !empty($current_settings['require_password_uppercase']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="require_password_uppercase">
                                            Require uppercase letters
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="require_password_numbers" name="require_password_numbers" 
                                               <?= !empty($current_settings['require_password_numbers']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="require_password_numbers">
                                            Require numbers
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="require_password_symbols" name="require_password_symbols" 
                                               <?= !empty($current_settings['require_password_symbols']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="require_password_symbols">
                                            Require symbols (!@#$%^&*)
                                        </label>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lock"></i> Security Settings
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="session_timeout" class="form-label">Session Timeout (minutes)</label>
                                <input type="number" class="form-control" id="session_timeout" name="session_timeout" 
                                       value="<?= $current_settings['session_timeout'] ?? 60 ?>" min="15" max="480" required>
                                <div class="form-text">How long users stay logged in</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="max_login_attempts" class="form-label">Max Login Attempts</label>
                                <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" 
                                       value="<?= $current_settings['max_login_attempts'] ?? 5 ?>" min="3" max="10" required>
                                <div class="form-text">Before account lockout</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="lockout_duration" class="form-label">Lockout Duration (minutes)</label>
                                <input type="number" class="form-control" id="lockout_duration" name="lockout_duration" 
                                       value="<?= $current_settings['lockout_duration'] ?? 15 ?>" min="5" max="120" required>
                                <div class="form-text">How long accounts are locked</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="settings_dash.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Account Settings
                        </button>
                    </div>
                            </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= template_admin_footer() ?>
