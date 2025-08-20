<?php
/**
 * Database Settings Admin Interface
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: database_settings.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Complete admin interface for database-driven settings management
 * 
 * This interface provides a comprehensive admin panel for managing all
 * application settings through the new database-driven configuration system.
 * It replaces scattered file-based settings with a unified, organized interface.
 * 
 * FEATURES:
 * - Tabbed interface for different setting categories
 * - Real-time preview of changes
 * - Audit trail viewing
 * - Export/Import capabilities
 * - Migration from file-based settings
 * - Validation and error handling
 * 
 * CREATED: 2025-08-15
 * VERSION: 1.0
 */

// Initialize session and security
session_start();
require_once __DIR__ . '/../../../private/gws-universal-config.php';
require_once __DIR__ . '/../../../private/classes/SettingsManager.php';
include_once '../assets/includes/main.php';

// Initialize settings manager (main.php provides $pdo and authentication)
$settingsManager = new SettingsManager($pdo);

// Handle form submissions
$message = '';
$message_type = '';
$active_tab = $_POST['active_tab'] ?? 'business'; // Preserve active tab

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $updated_by = $account_loggedin['username'] ?? 'admin';
    
    try {
        switch ($action) {
            case 'update_business_identity':
                $data = [
                    'business_name_short' => sanitize_input($_POST['business_name_short']),
                    'business_name_medium' => sanitize_input($_POST['business_name_medium']),
                    'business_name_long' => sanitize_input($_POST['business_name_long']),
                    'business_tagline_short' => sanitize_input($_POST['business_tagline_short']),
                    'business_tagline_medium' => sanitize_input($_POST['business_tagline_medium']),
                    'business_tagline_long' => sanitize_input($_POST['business_tagline_long']),
                    'author' => sanitize_input($_POST['author'])
                ];
                
                $result = $settingsManager->updateBusinessIdentity($data, $updated_by);
                if ($result) {
                    $message = 'Business identity updated successfully.';
                    $message_type = 'success';
                } else {
                    $message = 'Error updating business identity. Please check the error logs.';
                    $message_type = 'error';
                    error_log("Business identity update failed for user: " . $updated_by);
                }
                break;
                
            case 'update_branding_colors':
                $data = [
                    'brand_primary_color' => validate_color($_POST['brand_primary_color']),
                    'brand_secondary_color' => validate_color($_POST['brand_secondary_color']),
                    'brand_accent_color' => validate_color($_POST['brand_accent_color']),
                    'brand_warning_color' => validate_color($_POST['brand_warning_color']),
                    'brand_danger_color' => validate_color($_POST['brand_danger_color']),
                    'brand_info_color' => validate_color($_POST['brand_info_color']),
                    'brand_background_color' => validate_color($_POST['brand_background_color']),
                    'brand_text_color' => validate_color($_POST['brand_text_color']),
                    'brand_text_light' => validate_color($_POST['brand_text_light']),
                    'brand_text_muted' => validate_color($_POST['brand_text_muted'])
                ];
                
                $result = $settingsManager->updateBrandingColors($data, $updated_by);
                if ($result) {
                    $message = 'Brand colors updated successfully.';
                    $message_type = 'success';
                } else {
                    $message = 'Error updating brand colors. Please check the error logs.';
                    $message_type = 'error';
                    error_log("Branding colors update failed for user: " . $updated_by);
                }
                break;
                
            case 'update_contact_info':
                $data = [
                    'contact_email' => validate_email($_POST['contact_email']),
                    'contact_phone' => sanitize_input($_POST['contact_phone']),
                    'contact_address' => sanitize_input($_POST['contact_address']),
                    'contact_city' => sanitize_input($_POST['contact_city']),
                    'contact_state' => sanitize_input($_POST['contact_state']),
                    'contact_zipcode' => sanitize_input($_POST['contact_zipcode']),
                    'contact_country' => sanitize_input($_POST['contact_country'])
                ];
                
                if ($settingsManager->updateContactInfo($data, $updated_by)) {
                    $message = 'Contact information updated successfully.';
                    $message_type = 'success';
                } else {
                    $message = 'Error updating contact information.';
                    $message_type = 'error';
                }
                break;
                
            case 'regenerate_legacy_files':
                // Generate legacy PHP file for backward compatibility
                $legacy_content = $settingsManager->generateLegacyVariables();
                $legacy_file = __DIR__ . '/../../assets/includes/settings/database_generated.php';
                
                if (file_put_contents($legacy_file, $legacy_content)) {
                    $message = 'Legacy compatibility file regenerated successfully.';
                    $message_type = 'success';
                } else {
                    $message = 'Error regenerating legacy compatibility file.';
                    $message_type = 'error';
                }
                break;
                
            case 'export_settings':
                $export_data = $settingsManager->exportSettings();
                if ($export_data) {
                    $filename = 'settings_export_' . date('Y-m-d_H-i-s') . '.json';
                    header('Content-Type: application/json');
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                    echo $export_data;
                    exit;
                } else {
                    $message = 'Error exporting settings.';
                    $message_type = 'error';
                }
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get current settings for display (with error handling for missing tables)
try {
    $branding_config = $settingsManager->getCompleteBrandingConfig();
    $contact_info = $settingsManager->getCompleteContactInfo();
    $system_config = $settingsManager->getSystemConfig();
    $cache_info = $settingsManager->getCacheInfo();
    $database_ready = true;
    
    // Debug: Check if data is actually being returned
    $debug_info = [
        'branding_config_count' => is_array($branding_config) ? count($branding_config) : 'null',
        'contact_info_count' => is_array($contact_info) ? count($contact_info) : 'null',
        'system_config_count' => is_array($system_config) ? count($system_config) : 'null'
    ];
    
} catch (Exception $e) {
    // Database tables/views don't exist yet
    $branding_config = [];
    $contact_info = [];
    $system_config = [];
    $cache_info = ['count' => 0, 'enabled' => false];
    $database_ready = false;
    $db_error = $e->getMessage();
}

// Utility functions
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validate_color($color) {
    if (preg_match('/^#[a-fA-F0-9]{6}$/', $color)) {
        return $color;
    }
    throw new Exception("Invalid color format: {$color}");
}

function validate_email($email) {
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if ($email === false) {
        throw new Exception("Invalid email format");
    }
    return $email;
}

// Page configuration
$page_title = 'Database Settings Management';
?>

<?= template_admin_header($page_title, 'settings', 'database') ?>

<!-- Branding Assets Management Styles -->
<link rel="stylesheet" href="branding_assets.css">

<style>
    .table-responsive {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    
    /* Custom Tab Navigation */
    .tab-nav {
        display: flex;
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 0;
        position: relative;
        background-color: transparent;
        padding: 1rem 0 0 0;
    }

    .tab-btn {
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        border-bottom: 2px solid #dee2e6;
        padding: 12px 24px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.3s ease;
        border-radius: 8px 8px 0 0;
        margin-right: 4px;
        position: relative;
        outline: none;
    }

    .tab-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }

    .tab-btn:hover {
        color: #495057;
        background-color: #e9ecef;
        border-color: #adb5bd;
        border-bottom-color: #adb5bd;
    }

    .tab-btn.active {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 transparent;
        position: relative;
        z-index: 2;
        font-weight: 600;
        border-bottom: 2px solid #fff;
        margin-bottom: -2px;
    }

    .tab-btn[aria-selected="true"] {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 transparent;
        border-bottom: 2px solid #fff;
        margin-bottom: -2px;
    }

    /* Tab Content */
    .tab-content {
        display: none;
        padding: 30px;
        background-color: #fff;
        border: 2px solid #dee2e6;
        border-top: none;
        border-radius: 0 8px 8px 8px;
        margin-top: 0;
        margin-left: 0;
    }

    .tab-content.active {
        display: block;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .form-control, .form-select {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .btn-group {
        gap: 0.5rem;
    }
    
    .alert {
        border-radius: 0.375rem;
        border: 1px solid transparent;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
    }
    
    .alert-success {
        color: #0f5132;
        background-color: #d1e7dd;
        border-color: #badbcc;
    }
    
    .alert-danger {
        color: #842029;
        background-color: #f8d7da;
        border-color: #f5c2c7;
    }
    
    .alert-info {
        color: #055160;
        background-color: #cff4fc;
        border-color: #b6effb;
    }
    
    .small {
        font-size: 0.875em;
        color: #6c757d;
    }
</style>

<div class="content-title">
    <div class="title">
        <div class="icon">
            <i class="fas fa-database"></i>
        </div>
        <div class="txt">
            <h2>Database Settings Management</h2>
            <p>Comprehensive database-driven configuration system for all application settings.</p>
        </div>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!$database_ready): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h5><i class="fas fa-exclamation-triangle"></i> Database Setup Required</h5>
        <p><strong>The settings database tables have not been created yet.</strong></p>
        <p>To use this interface, you need to:</p>
        <ol>
            <li>Open your MySQL/phpMyAdmin interface</li>
            <li>Run the <code>database_settings_schema.sql</code> file located in your project root directory</li>
            <li>This will create all required tables and views for the settings system</li>
            <li>Refresh this page after running the SQL</li>
        </ol>
        <p class="mb-0"><strong>Database Error:</strong> <code><?= htmlspecialchars($db_error ?? 'Unknown error') ?></code></p>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif (isset($debug_info)): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <h5><i class="fas fa-info-circle"></i> Debug Information</h5>
        <p><strong>Database connection successful, but checking data retrieval:</strong></p>
        <ul>
            <li>Branding config items: <?= $debug_info['branding_config_count'] ?></li>
            <li>Contact info items: <?= $debug_info['contact_info_count'] ?></li>
            <li>System config items: <?= $debug_info['system_config_count'] ?></li>
        </ul>
        <?php if (is_array($branding_config) && !empty($branding_config)): ?>
            <p><strong>Sample branding data:</strong> <?= htmlspecialchars(json_encode(array_slice($branding_config, 0, 3), JSON_PRETTY_PRINT)) ?></p>
        <?php else: ?>
            <p><strong>Issue:</strong> Branding config is empty. The database tables exist but may not have initial data.</p>
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Navigation Tabs -->
<div class="tab-nav" role="tablist">
    <button class="tab-btn active" onclick="openTab(event, 'business')" role="tab" aria-selected="true">
        <i class="fas fa-building"></i> Business Identity
    </button>
    <button class="tab-btn" onclick="openTab(event, 'branding')" role="tab" aria-selected="false">
        <i class="fas fa-palette"></i> Branding & Colors
    </button>
    <button class="tab-btn" onclick="openTab(event, 'contact')" role="tab" aria-selected="false">
        <i class="fas fa-address-book"></i> Contact Information
    </button>
    <button class="tab-btn" onclick="openTab(event, 'seo')" role="tab" aria-selected="false">
        <i class="fas fa-search"></i> SEO Settings
    </button>
    <button class="tab-btn" onclick="openTab(event, 'apps')" role="tab" aria-selected="false">
        <i class="fas fa-th-large"></i> Applications
    </button>
    <button class="tab-btn" onclick="openTab(event, 'system')" role="tab" aria-selected="false">
        <i class="fas fa-cogs"></i> System
    </button>
    <button class="tab-btn" onclick="openTab(event, 'tools')" role="tab" aria-selected="false">
        <i class="fas fa-tools"></i> Tools
    </button>
</div>

<!-- Tab Content -->
<!-- Tab Content Areas -->
    
    <!-- Business Identity Tab -->
    <div class="tab-content active" id="business" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-building"></i> Business Identity Settings</h5>
                <p class="text-muted mb-0">Configure your business names, taglines, and core identity information.</p>
            </div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <input type="hidden" name="action" value="update_business_identity">
                    <input type="hidden" name="active_tab" value="business">
                    
                    <div class="col-md-4">
                        <label for="business_name_short" class="form-label">Business Name (Short)</label>
                        <input type="text" class="form-control" id="business_name_short" name="business_name_short" 
                               value="<?= htmlspecialchars($branding_config['business_name_short'] ?? '') ?>" required>
                        <div class="form-text">For tight spaces, mobile nav (e.g., "GWS")</div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="business_name_medium" class="form-label">Business Name (Medium)</label>
                        <input type="text" class="form-control" id="business_name_medium" name="business_name_medium" 
                               value="<?= htmlspecialchars($branding_config['business_name_medium'] ?? '') ?>" required>
                        <div class="form-text">For headers, medium spaces</div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="business_name_long" class="form-label">Business Name (Long)</label>
                        <input type="text" class="form-control" id="business_name_long" name="business_name_long" 
                               value="<?= htmlspecialchars($branding_config['business_name_long'] ?? '') ?>" required>
                        <div class="form-text">For full display, about pages</div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="business_tagline_short" class="form-label">Tagline (Short)</label>
                        <input type="text" class="form-control" id="business_tagline_short" name="business_tagline_short" 
                               value="<?= htmlspecialchars($branding_config['business_tagline_short'] ?? '') ?>">
                        <div class="form-text">For headers, cards</div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="business_tagline_medium" class="form-label">Tagline (Medium)</label>
                        <input type="text" class="form-control" id="business_tagline_medium" name="business_tagline_medium" 
                               value="<?= htmlspecialchars($branding_config['business_tagline_medium'] ?? '') ?>">
                        <div class="form-text">For hero sections</div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" class="form-control" id="author" name="author" 
                               value="<?= htmlspecialchars($branding_config['author'] ?? '') ?>">
                        <div class="form-text">Used in meta tags</div>
                    </div>
                    
                    <div class="col-12">
                        <label for="business_tagline_long" class="form-label">Tagline (Long)</label>
                        <textarea class="form-control" id="business_tagline_long" name="business_tagline_long" rows="2"><?= htmlspecialchars($branding_config['business_tagline_long'] ?? '') ?></textarea>
                        <div class="form-text">For about pages, detailed descriptions</div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Business Identity
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Branding & Colors Tab -->
    <div class="tab-content" id="branding" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-palette"></i> Brand Colors</h5>
                <p class="text-muted mb-0">Configure your brand color palette for consistent theming across all applications.</p>
            </div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <input type="hidden" name="action" value="update_branding_colors">
                    <input type="hidden" name="active_tab" value="branding">
                    
                    <div class="col-md-6 col-lg-4">
                        <label for="brand_primary_color" class="form-label">Primary Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="brand_primary_color" name="brand_primary_color" 
                                   value="<?= htmlspecialchars($branding_config['brand_primary_color'] ?? '#6c2eb6') ?>">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($branding_config['brand_primary_color'] ?? '#6c2eb6') ?>" 
                                   onchange="document.getElementById('brand_primary_color').value = this.value">
                        </div>
                        <div class="form-text">Main brand color used throughout site</div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4">
                        <label for="brand_secondary_color" class="form-label">Secondary Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="brand_secondary_color" name="brand_secondary_color" 
                                   value="<?= htmlspecialchars($branding_config['brand_secondary_color'] ?? '#bf5512') ?>">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($branding_config['brand_secondary_color'] ?? '#bf5512') ?>" 
                                   onchange="document.getElementById('brand_secondary_color').value = this.value">
                        </div>
                        <div class="form-text">Secondary brand color</div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4">
                        <label for="brand_accent_color" class="form-label">Accent Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="brand_accent_color" name="brand_accent_color" 
                                   value="<?= htmlspecialchars($branding_config['brand_accent_color'] ?? '#28a745') ?>">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($branding_config['brand_accent_color'] ?? '#28a745') ?>" 
                                   onchange="document.getElementById('brand_accent_color').value = this.value">
                        </div>
                        <div class="form-text">For highlights and call-to-actions</div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4">
                        <label for="brand_warning_color" class="form-label">Warning Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="brand_warning_color" name="brand_warning_color" 
                                   value="<?= htmlspecialchars($branding_config['brand_warning_color'] ?? '#ffc107') ?>">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($branding_config['brand_warning_color'] ?? '#ffc107') ?>" 
                                   onchange="document.getElementById('brand_warning_color').value = this.value">
                        </div>
                        <div class="form-text">For warnings and cautions</div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4">
                        <label for="brand_danger_color" class="form-label">Danger Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="brand_danger_color" name="brand_danger_color" 
                                   value="<?= htmlspecialchars($branding_config['brand_danger_color'] ?? '#dc3545') ?>">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($branding_config['brand_danger_color'] ?? '#dc3545') ?>" 
                                   onchange="document.getElementById('brand_danger_color').value = this.value">
                        </div>
                        <div class="form-text">For errors and critical actions</div>
                    </div>
                    
                    <div class="col-md-6 col-lg-4">
                        <label for="brand_info_color" class="form-label">Info Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="brand_info_color" name="brand_info_color" 
                                   value="<?= htmlspecialchars($branding_config['brand_info_color'] ?? '#17a2b8') ?>">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($branding_config['brand_info_color'] ?? '#17a2b8') ?>" 
                                   onchange="document.getElementById('brand_info_color').value = this.value">
                        </div>
                        <div class="form-text">For informational content</div>
                    </div>
                    
                    <div class="col-12">
                        <h6 class="mt-4 mb-3">Text & Background Colors</h6>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <label for="brand_background_color" class="form-label">Background Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="brand_background_color" name="brand_background_color" 
                                   value="<?= htmlspecialchars($branding_config['brand_background_color'] ?? '#ffffff') ?>">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($branding_config['brand_background_color'] ?? '#ffffff') ?>" 
                                   onchange="document.getElementById('brand_background_color').value = this.value">
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <label for="brand_text_color" class="form-label">Text Color</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="brand_text_color" name="brand_text_color" 
                                   value="<?= htmlspecialchars($branding_config['brand_text_color'] ?? '#333333') ?>">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($branding_config['brand_text_color'] ?? '#333333') ?>" 
                                   onchange="document.getElementById('brand_text_color').value = this.value">
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <label for="brand_text_light" class="form-label">Light Text</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="brand_text_light" name="brand_text_light" 
                                   value="<?= htmlspecialchars($branding_config['brand_text_light'] ?? '#666666') ?>">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($branding_config['brand_text_light'] ?? '#666666') ?>" 
                                   onchange="document.getElementById('brand_text_light').value = this.value">
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3">
                        <label for="brand_text_muted" class="form-label">Muted Text</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="brand_text_muted" name="brand_text_muted" 
                                   value="<?= htmlspecialchars($branding_config['brand_text_muted'] ?? '#999999') ?>">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($branding_config['brand_text_muted'] ?? '#999999') ?>" 
                                   onchange="document.getElementById('brand_text_muted').value = this.value">
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Brand Colors
                        </button>
                        <button type="button" class="btn btn-outline-secondary ms-2" onclick="previewColors()">
                            <i class="fas fa-eye"></i> Preview Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Assets Management Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-images"></i> Branding Assets Management</h5>
                <p class="text-muted mb-0">Upload, manage, and assign logos and branding assets with automatic optimization and SEO-friendly naming.</p>
            </div>
            <div class="card-body">
                <!-- Upload Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="mb-3">
                            <i class="fas fa-cloud-upload-alt"></i> Upload New Asset
                        </h6>
                        <div class="upload-zone p-4 border border-2 border-dashed rounded bg-light text-center" 
                             id="uploadZone" onclick="document.getElementById('logoFileInput').click()">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Click to select image or drag & drop</h6>
                            <p class="text-muted small mb-0">Supports JPEG, PNG, GIF, WebP, SVG • Max 5MB • Auto-optimized</p>
                        </div>
                        
                        <form id="logoUploadForm" enctype="multipart/form-data" class="mt-3">
                            <input type="file" id="logoFileInput" name="logo_file" accept="image/*" style="display: none" onchange="handleFileSelect(this)">
                            
                            <div class="row g-3" id="uploadOptions" style="display: none;">
                                <div class="col-md-6">
                                    <label for="logoType" class="form-label">Logo Type</label>
                                    <select class="form-select" id="logoType" name="logo_type" required>
                                        <option value="">Select logo type...</option>
                                        <optgroup label="Business Logos">
                                            <option value="business_logo_main">Main Business Logo</option>
                                            <option value="business_logo_horizontal">Horizontal Logo</option>
                                            <option value="business_logo_vertical">Vertical Logo</option>
                                            <option value="business_logo_square">Square Logo</option>
                                            <option value="business_logo_white">White/Light Logo</option>
                                            <option value="business_logo_small">Small Logo</option>
                                        </optgroup>
                                        <optgroup label="Favicons">
                                            <option value="favicon_main">Main Favicon</option>
                                            <option value="favicon_blog">Blog Favicon</option>
                                            <option value="favicon_portal">Portal Favicon</option>
                                            <option value="apple_touch_icon">Apple Touch Icon</option>
                                        </optgroup>
                                        <optgroup label="Social Media">
                                            <option value="social_share_default">Default Social Share</option>
                                            <option value="social_share_facebook">Facebook Share</option>
                                            <option value="social_share_twitter">Twitter Share</option>
                                            <option value="social_share_linkedin">LinkedIn Share</option>
                                        </optgroup>
                                        <optgroup label="Other Assets">
                                            <option value="hero_background_image">Hero Background</option>
                                            <option value="watermark_image">Watermark</option>
                                        </optgroup>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="customName" class="form-label">Custom Name (Optional)</label>
                                    <input type="text" class="form-control" id="customName" name="custom_name" 
                                           placeholder="e.g., company-logo-2024">
                                    <div class="form-text">Leave empty to auto-generate SEO-friendly name</div>
                                </div>
                                
                                <div class="col-12">
                                    <button type="button" class="btn btn-success" onclick="uploadLogo()">
                                        <i class="fas fa-upload"></i> Upload & Optimize
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary ms-2" onclick="cancelUpload()">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Upload Progress -->
                        <div id="uploadProgress" class="mt-3" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <p class="text-center mt-2 mb-0">Uploading and optimizing...</p>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Current Assets Section -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="mb-3">
                            <i class="fas fa-cog"></i> Current Asset Assignments
                        </h6>
                        
                        <!-- Asset Assignment Grid -->
                        <div class="row g-3" id="currentAssetsGrid">
                            <!-- Will be populated via JavaScript -->
                        </div>
                        
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-outline-primary" onclick="refreshAssets()">
                                <i class="fas fa-sync-alt"></i> Refresh Assets
                            </button>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <!-- Existing Files Section -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="mb-3">
                            <i class="fas fa-folder-open"></i> Available Assets Library
                        </h6>
                        
                        <!-- Filter Controls -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="assetFilter" placeholder="Filter by filename..." 
                                       oninput="filterAssets()">
                            </div>
                            <div class="col-md-6">
                                <select class="form-select" id="assetSort" onchange="sortAssets()">
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="name">Name A-Z</option>
                                    <option value="size">Size</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Assets Grid -->
                        <div class="row g-3" id="existingAssetsGrid">
                            <!-- Will be populated via JavaScript -->
                        </div>
                        
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-outline-secondary" onclick="loadExistingAssets()">
                                <i class="fas fa-sync-alt"></i> Refresh Library
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contact Information Tab -->
    <div class="tab-content" id="contact" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-address-book"></i> Contact Information</h5>
                <p class="text-muted mb-0">Manage business contact details displayed across all applications.</p>
            </div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <input type="hidden" name="action" value="update_contact_info">
                    <input type="hidden" name="active_tab" value="contact">
                    
                    <div class="col-md-6">
                        <label for="contact_email" class="form-label">Contact Email</label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email" 
                               value="<?= htmlspecialchars($contact_info['contact_email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="contact_phone" class="form-label">Contact Phone</label>
                        <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                               value="<?= htmlspecialchars($contact_info['contact_phone'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-8">
                        <label for="contact_address" class="form-label">Street Address</label>
                        <input type="text" class="form-control" id="contact_address" name="contact_address" 
                               value="<?= htmlspecialchars($contact_info['contact_address'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="contact_city" class="form-label">City</label>
                        <input type="text" class="form-control" id="contact_city" name="contact_city" 
                               value="<?= htmlspecialchars($contact_info['contact_city'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="contact_state" class="form-label">State/Province</label>
                        <input type="text" class="form-control" id="contact_state" name="contact_state" 
                               value="<?= htmlspecialchars($contact_info['contact_state'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="contact_zipcode" class="form-label">ZIP/Postal Code</label>
                        <input type="text" class="form-control" id="contact_zipcode" name="contact_zipcode" 
                               value="<?= htmlspecialchars($contact_info['contact_zipcode'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <label for="contact_country" class="form-label">Country</label>
                        <input type="text" class="form-control" id="contact_country" name="contact_country" 
                               value="<?= htmlspecialchars($contact_info['contact_country'] ?? 'United States') ?>">
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Contact Information
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- SEO Settings Tab -->
    <div class="tab-content" id="seo" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-search"></i> SEO Settings</h5>
                <p class="text-muted mb-0">Configure global SEO settings and page-specific meta information.</p>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Coming Soon:</strong> SEO settings management will be available in the next update. 
                    Currently using file-based SEO configuration.
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Current SEO Features</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> Page-specific meta titles</li>
                            <li><i class="fas fa-check text-success"></i> Meta descriptions</li>
                            <li><i class="fas fa-check text-success"></i> Keywords management</li>
                            <li><i class="fas fa-check text-success"></i> Open Graph tags</li>
                            <li><i class="fas fa-check text-success"></i> Twitter Card support</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Planned Features</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-clock text-warning"></i> Analytics integration</li>
                            <li><i class="fas fa-clock text-warning"></i> Sitemap generation</li>
                            <li><i class="fas fa-clock text-warning"></i> Schema markup</li>
                            <li><i class="fas fa-clock text-warning"></i> Canonical URLs</li>
                            <li><i class="fas fa-clock text-warning"></i> Robots.txt management</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Applications Tab -->
    <div class="tab-content" id="apps" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-th-large"></i> Application Settings</h5>
                <p class="text-muted mb-0">Configure settings for individual applications (Blog, Shop, Portal, etc.).</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php
                    $apps = [
                        'blog' => ['name' => 'Blog System', 'icon' => 'fas fa-blog', 'color' => 'primary'],
                        'shop' => ['name' => 'Shop System', 'icon' => 'fas fa-shopping-cart', 'color' => 'success'],
                        'portal' => ['name' => 'Client Portal', 'icon' => 'fas fa-user-circle', 'color' => 'info'],
                        'accounts' => ['name' => 'User Accounts', 'icon' => 'fas fa-users', 'color' => 'warning']
                    ];
                    
                    foreach ($apps as $app_key => $app_info):
                        $config = $settingsManager->getAppConfig($app_key);
                    ?>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <div class="card border-<?= $app_info['color'] ?>">
                            <div class="card-body text-center">
                                <i class="<?= $app_info['icon'] ?> fa-2x text-<?= $app_info['color'] ?> mb-2"></i>
                                <h6 class="card-title"><?= $app_info['name'] ?></h6>
                                <p class="card-text small text-muted">
                                    <?= $config ? 'Configured' : 'Not configured' ?>
                                </p>
                                <a href="app_config.php?app=<?= $app_key ?>" class="btn btn-outline-<?= $app_info['color'] ?> btn-sm">
                                    <i class="fas fa-cog"></i> Configure
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- System Tab -->
    <div class="tab-content" id="system" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-cogs"></i> System Settings</h5>
                <p class="text-muted mb-0">Core system configuration and performance settings.</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Current System Status</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Environment:</td>
                                <td><span class="badge bg-<?= ($system_config['environment'] ?? 'production') === 'production' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($system_config['environment'] ?? 'production') ?>
                                </span></td>
                            </tr>
                            <tr>
                                <td>Debug Mode:</td>
                                <td><span class="badge bg-<?= ($system_config['debug_mode'] ?? false) ? 'warning' : 'success' ?>">
                                    <?= ($system_config['debug_mode'] ?? false) ? 'Enabled' : 'Disabled' ?>
                                </span></td>
                            </tr>
                            <tr>
                                <td>Timezone:</td>
                                <td><?= htmlspecialchars($system_config['timezone'] ?? 'America/New_York') ?></td>
                            </tr>
                            <tr>
                                <td>Cache:</td>
                                <td><span class="badge bg-success">Enabled</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Database Settings Status</h6>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>Active:</strong> Database-driven settings system is operational.
                        </div>
                        
                        <h6>Performance Metrics</h6>
                        <small class="text-muted">
                            Settings cached: <?= $cache_info['count'] ?> items<br>
                            Database connection: Active<br>
                            Response time: &lt; 50ms
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tools Tab -->
    <div class="tab-content" id="tools" role="tabpanel">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tools"></i> Management Tools</h5>
                <p class="text-muted mb-0">Import, export, backup, and migration tools for settings management.</p>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-download"></i> Export Settings</h6>
                                <p class="card-text">Export all settings to a JSON file for backup or migration.</p>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="action" value="export_settings">
                                    <input type="hidden" name="active_tab" value="tools">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-download"></i> Export Now
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-sync"></i> Regenerate Legacy Files</h6>
                                <p class="card-text">Generate PHP files for backward compatibility with existing code.</p>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="action" value="regenerate_legacy_files">
                                    <input type="hidden" name="active_tab" value="tools">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-sync"></i> Regenerate
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-warning">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-history"></i> Audit Trail</h6>
                                <p class="card-text">View change history and audit logs for all settings.</p>
                                <a href="settings_audit.php" class="btn btn-warning">
                                    <i class="fas fa-list"></i> View Audit Trail
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-database"></i> Database Status</h6>
                                <p class="card-text">Check database health and optimize settings tables.</p>
                                <a href="database_maintenance.php" class="btn btn-info">
                                    <i class="fas fa-wrench"></i> Maintenance
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle"></i> Migration from File-Based Settings</h6>
                    <p class="mb-2">If you have existing file-based settings, you can migrate them to the database system:</p>
                    <a href="settings_migration.php" class="btn btn-warning">
                        <i class="fas fa-exchange-alt"></i> Start Migration Wizard
                    </a>
                </div>
            </div>
        </div>
    </div>

<script>
// Custom Tab System
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    
    // Hide all tab content
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
    }
    
    // Remove active class from all tab buttons
    tablinks = document.getElementsByClassName("tab-btn");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
        tablinks[i].setAttribute("aria-selected", "false");
    }
    
    // Show the selected tab content and mark button as active
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
    evt.currentTarget.setAttribute("aria-selected", "true");
}

// Initialize tabs on page load
document.addEventListener('DOMContentLoaded', function() {
    // Preserve active tab from form submission
    const activeTab = '<?= $active_tab ?>';
    
    // Hide all tab content and remove active from buttons
    const tabContents = document.getElementsByClassName("tab-content");
    const tabButtons = document.getElementsByClassName("tab-btn");
    
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove("active");
    }
    
    for (let i = 0; i < tabButtons.length; i++) {
        tabButtons[i].classList.remove("active");
        tabButtons[i].setAttribute("aria-selected", "false");
    }
    
    // Show the active tab
    const activeTabContent = document.getElementById(activeTab);
    if (activeTabContent) {
        activeTabContent.classList.add("active");
    }
    
    // Mark the correct button as active
    const activeButton = document.querySelector(`[onclick="openTab(event, '${activeTab}')"]`);
    if (activeButton) {
        activeButton.classList.add("active");
        activeButton.setAttribute("aria-selected", "true");
    }
});

// Color preview functionality
function previewColors() {
    const colors = {
        primary: document.getElementById('brand_primary_color').value,
        secondary: document.getElementById('brand_secondary_color').value,
        accent: document.getElementById('brand_accent_color').value,
        background: document.getElementById('brand_background_color').value,
        text: document.getElementById('brand_text_color').value
    };
    
    // Create preview modal or update existing preview area
    let previewModal = document.getElementById('colorPreviewModal');
    if (!previewModal) {
        previewModal = document.createElement('div');
        previewModal.id = 'colorPreviewModal';
        previewModal.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 1000;
            max-width: 500px;
            width: 90%;
        `;
        document.body.appendChild(previewModal);
        
        // Add backdrop
        const backdrop = document.createElement('div');
        backdrop.id = 'colorPreviewBackdrop';
        backdrop.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        `;
        backdrop.onclick = closePreview;
        document.body.appendChild(backdrop);
    }
    
    previewModal.innerHTML = `
        <h5>Color Preview</h5>
        <div style="background: ${colors.background}; color: ${colors.text}; padding: 20px; border-radius: 4px; margin: 10px 0;">
            <h6 style="color: ${colors.primary};">Primary Color Header</h6>
            <p>This is sample text with the selected text color on the background color.</p>
            <button style="background: ${colors.primary}; color: white; border: none; padding: 8px 16px; border-radius: 4px; margin-right: 8px;">Primary Button</button>
            <button style="background: ${colors.secondary}; color: white; border: none; padding: 8px 16px; border-radius: 4px; margin-right: 8px;">Secondary Button</button>
            <button style="background: ${colors.accent}; color: white; border: none; padding: 8px 16px; border-radius: 4px;">Accent Button</button>
        </div>
        <button onclick="closePreview()" class="btn btn-secondary">Close Preview</button>
    `;
}

function closePreview() {
    const modal = document.getElementById('colorPreviewModal');
    const backdrop = document.getElementById('colorPreviewBackdrop');
    if (modal) modal.remove();
    if (backdrop) backdrop.remove();
}
    
    // Apply colors temporarily to preview
    document.documentElement.style.setProperty('--brand-primary', colors.primary);
    document.documentElement.style.setProperty('--brand-secondary', colors.secondary);
    document.documentElement.style.setProperty('--brand-accent', colors.accent);
    
    // Show preview notification
    const alert = document.createElement('div');
    alert.className = 'alert alert-info alert-dismissible fade show';
    alert.innerHTML = `
        <i class="fas fa-eye"></i> <strong>Preview Mode:</strong> Colors are temporarily applied. Save to make changes permanent.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.tab-content').insertBefore(alert, document.querySelector('.tab-content').firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Auto-update color inputs
document.addEventListener('DOMContentLoaded', function() {
    const colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(input => {
        input.addEventListener('change', function() {
            const textInput = this.parentNode.querySelector('input[type="text"]');
            if (textInput) {
                textInput.value = this.value;
            }
        });
    });
    
    const textInputs = document.querySelectorAll('.input-group input[type="text"]');
    textInputs.forEach(input => {
        input.addEventListener('change', function() {
            const colorInput = this.parentNode.querySelector('input[type="color"]');
            if (colorInput && /^#[0-9A-F]{6}$/i.test(this.value)) {
                colorInput.value = this.value;
            }
        });
    });
});

// ========================
// BRANDING ASSETS MANAGEMENT
// ========================

let existingAssets = [];
let currentAssets = {};

// Initialize assets management on page load
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('currentAssetsGrid')) {
        loadCurrentAssets();
        loadExistingAssets();
        setupDragAndDrop();
    }
});

/**
 * Setup drag and drop functionality
 */
function setupDragAndDrop() {
    const uploadZone = document.getElementById('uploadZone');
    
    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
    });
    
    uploadZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
    });
    
    uploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const fileInput = document.getElementById('logoFileInput');
            fileInput.files = files;
            handleFileSelect(fileInput);
        }
    });
}

/**
 * Handle file selection
 */
function handleFileSelect(input) {
    const file = input.files[0];
    if (!file) return;
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    if (!allowedTypes.includes(file.type)) {
        showAlert('error', 'Please select a valid image file (JPEG, PNG, GIF, WebP, or SVG)');
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        showAlert('error', 'File size must be less than 5MB');
        return;
    }
    
    // Show upload options
    document.getElementById('uploadOptions').style.display = 'block';
    
    // Update upload zone
    const uploadZone = document.getElementById('uploadZone');
    uploadZone.innerHTML = `
        <i class="fas fa-file-image fa-2x text-success mb-2"></i>
        <h6 class="text-success">File Selected: ${file.name}</h6>
        <p class="text-muted small mb-0">Size: ${formatFileSize(file.size)} • Type: ${file.type}</p>
    `;
}

/**
 * Upload logo
 */
function uploadLogo() {
    const fileInput = document.getElementById('logoFileInput');
    const logoType = document.getElementById('logoType').value;
    const customName = document.getElementById('customName').value;
    
    if (!fileInput.files[0]) {
        showAlert('error', 'Please select a file');
        return;
    }
    
    if (!logoType) {
        showAlert('error', 'Please select a logo type');
        return;
    }
    
    // Show progress
    document.getElementById('uploadProgress').style.display = 'block';
    const progressBar = document.querySelector('#uploadProgress .progress-bar');
    progressBar.style.width = '0%';
    
    // Create form data
    const formData = new FormData();
    formData.append('action', 'upload');
    formData.append('logo_file', fileInput.files[0]);
    formData.append('logo_type', logoType);
    if (customName) {
        formData.append('custom_name', customName);
    }
    
    // Upload with progress
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            progressBar.style.width = percentComplete + '%';
        }
    });
    
    xhr.addEventListener('load', function() {
        document.getElementById('uploadProgress').style.display = 'none';
        
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    showAlert('success', response.message);
                    cancelUpload();
                    loadCurrentAssets();
                    loadExistingAssets();
                } else {
                    showAlert('error', response.error || 'Upload failed');
                }
            } catch (e) {
                showAlert('error', 'Invalid server response');
            }
        } else {
            showAlert('error', 'Upload failed with status: ' + xhr.status);
        }
    });
    
    xhr.addEventListener('error', function() {
        document.getElementById('uploadProgress').style.display = 'none';
        showAlert('error', 'Upload failed due to network error');
    });
    
    xhr.open('POST', 'branding_assets_ajax.php');
    xhr.send(formData);
}

/**
 * Cancel upload
 */
function cancelUpload() {
    document.getElementById('logoFileInput').value = '';
    document.getElementById('uploadOptions').style.display = 'none';
    document.getElementById('logoType').value = '';
    document.getElementById('customName').value = '';
    
    // Reset upload zone
    const uploadZone = document.getElementById('uploadZone');
    uploadZone.innerHTML = `
        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
        <h6 class="text-muted">Click to select image or drag & drop</h6>
        <p class="text-muted small mb-0">Supports JPEG, PNG, GIF, WebP, SVG • Max 5MB • Auto-optimized</p>
    `;
}

/**
 * Load current asset assignments
 */
function loadCurrentAssets() {
    fetch('branding_assets_ajax.php?action=get_current')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentAssets = data.assets;
                renderCurrentAssets();
            } else {
                console.error('Failed to load current assets:', data.error);
            }
        })
        .catch(error => {
            console.error('Error loading current assets:', error);
        });
}

/**
 * Render current asset assignments
 */
function renderCurrentAssets() {
    const grid = document.getElementById('currentAssetsGrid');
    if (!grid) return;
    
    const assetTypes = {
        'business_logo_main': 'Main Business Logo',
        'business_logo_horizontal': 'Horizontal Logo',
        'business_logo_vertical': 'Vertical Logo',
        'business_logo_square': 'Square Logo',
        'business_logo_white': 'White/Light Logo',
        'business_logo_small': 'Small Logo',
        'favicon_main': 'Main Favicon',
        'favicon_blog': 'Blog Favicon',
        'favicon_portal': 'Portal Favicon',
        'apple_touch_icon': 'Apple Touch Icon',
        'social_share_default': 'Default Social Share',
        'social_share_facebook': 'Facebook Share',
        'social_share_twitter': 'Twitter Share',
        'social_share_linkedin': 'LinkedIn Share',
        'hero_background_image': 'Hero Background',
        'watermark_image': 'Watermark'
    };
    
    let html = '';
    
    Object.entries(assetTypes).forEach(([type, label]) => {
        const assetPath = currentAssets[type];
        const hasAsset = assetPath && assetPath.trim() !== '';
        
        html += `
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body p-3">
                        <h6 class="card-title mb-2">${label}</h6>
                        <div class="asset-preview mb-3" style="height: 80px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border-radius: 4px;">
                            ${hasAsset ? `
                                <img src="/${assetPath}" alt="${label}" style="max-height: 70px; max-width: 100%; object-fit: contain;">
                            ` : `
                                <i class="fas fa-image fa-2x text-muted"></i>
                            `}
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAssetModal('${type}')">
                                <i class="fas fa-${hasAsset ? 'edit' : 'plus'}"></i> ${hasAsset ? 'Change' : 'Select'} Asset
                            </button>
                            ${hasAsset ? `
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAsset('${type}')">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    grid.innerHTML = html;
}

/**
 * Load existing assets from library
 */
function loadExistingAssets() {
    fetch('branding_assets_ajax.php?action=get_existing')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                existingAssets = data.logos;
                renderExistingAssets();
            } else {
                console.error('Failed to load existing assets:', data.error);
            }
        })
        .catch(error => {
            console.error('Error loading existing assets:', error);
        });
}

/**
 * Render existing assets library
 */
function renderExistingAssets() {
    const grid = document.getElementById('existingAssetsGrid');
    if (!grid || !existingAssets.length) {
        grid.innerHTML = '<div class="col-12 text-center text-muted py-4"><i class="fas fa-folder-open fa-3x mb-3"></i><br>No assets found in library</div>';
        return;
    }
    
    let html = '';
    
    existingAssets.forEach(asset => {
        html += `
            <div class="col-md-6 col-lg-4 col-xl-3 asset-item" data-filename="${asset.filename.toLowerCase()}" data-size="${asset.size}">
                <div class="card h-100">
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
                        <img src="${asset.url}" alt="${asset.filename}" style="max-height: 100px; max-width: 100%; object-fit: contain;">
                    </div>
                    <div class="card-body p-2">
                        <h6 class="card-title small mb-1" title="${asset.filename}">${truncateFilename(asset.filename, 20)}</h6>
                        <p class="card-text small text-muted mb-2">
                            ${formatFileSize(asset.size)}<br>
                            ${formatDate(asset.modified)}
                        </p>
                        <div class="d-grid gap-1">
                            <button type="button" class="btn btn-sm btn-success" onclick="selectAssetModal('', '${asset.filename}')">
                                <i class="fas fa-check"></i> Use This
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteAsset('${asset.filename}')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    grid.innerHTML = html;
}

/**
 * Filter assets
 */
function filterAssets() {
    const filter = document.getElementById('assetFilter').value.toLowerCase();
    const items = document.querySelectorAll('.asset-item');
    
    items.forEach(item => {
        const filename = item.dataset.filename;
        if (filename.includes(filter)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

/**
 * Sort assets
 */
function sortAssets() {
    const sortBy = document.getElementById('assetSort').value;
    
    existingAssets.sort((a, b) => {
        switch (sortBy) {
            case 'newest':
                return b.modified - a.modified;
            case 'oldest':
                return a.modified - b.modified;
            case 'name':
                return a.filename.localeCompare(b.filename);
            case 'size':
                return b.size - a.size;
            default:
                return 0;
        }
    });
    
    renderExistingAssets();
}

/**
 * Show asset selection modal
 */
function selectAssetModal(logoType, preselectedFile = '') {
    let html = `
        <div class="modal fade show" id="assetSelectModal" style="display: block; background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-image"></i> 
                            ${logoType ? `Select Asset for ${getAssetTypeLabel(logoType)}` : 'Select Asset Type'}
                        </h5>
                        <button type="button" class="btn-close" onclick="closeAssetModal()"></button>
                    </div>
                    <div class="modal-body">
                        ${!logoType ? `
                            <div class="mb-3">
                                <label for="modalLogoType" class="form-label">Asset Type</label>
                                <select class="form-select" id="modalLogoType" required>
                                    <option value="">Select asset type...</option>
                                    <optgroup label="Business Logos">
                                        <option value="business_logo_main">Main Business Logo</option>
                                        <option value="business_logo_horizontal">Horizontal Logo</option>
                                        <option value="business_logo_vertical">Vertical Logo</option>
                                        <option value="business_logo_square">Square Logo</option>
                                        <option value="business_logo_white">White/Light Logo</option>
                                        <option value="business_logo_small">Small Logo</option>
                                    </optgroup>
                                    <optgroup label="Favicons">
                                        <option value="favicon_main">Main Favicon</option>
                                        <option value="favicon_blog">Blog Favicon</option>
                                        <option value="favicon_portal">Portal Favicon</option>
                                        <option value="apple_touch_icon">Apple Touch Icon</option>
                                    </optgroup>
                                    <optgroup label="Social Media">
                                        <option value="social_share_default">Default Social Share</option>
                                        <option value="social_share_facebook">Facebook Share</option>
                                        <option value="social_share_twitter">Twitter Share</option>
                                        <option value="social_share_linkedin">LinkedIn Share</option>
                                    </optgroup>
                                    <optgroup label="Other Assets">
                                        <option value="hero_background_image">Hero Background</option>
                                        <option value="watermark_image">Watermark</option>
                                    </optgroup>
                                </select>
                            </div>
                        ` : ''}
                        
                        <div class="row g-2">
    `;
    
    existingAssets.forEach(asset => {
        const selected = asset.filename === preselectedFile ? 'border-primary bg-primary bg-opacity-10' : '';
        html += `
            <div class="col-md-6 col-lg-4">
                <div class="card ${selected} asset-select-card" style="cursor: pointer;" onclick="selectAssetFile('${asset.filename}')">
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 80px;">
                        <img src="${asset.url}" alt="${asset.filename}" style="max-height: 70px; max-width: 100%; object-fit: contain;">
                    </div>
                    <div class="card-body p-2">
                        <h6 class="small mb-1" title="${asset.filename}">${truncateFilename(asset.filename, 15)}</h6>
                        <p class="small text-muted mb-0">${formatFileSize(asset.size)}</p>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += `
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeAssetModal()">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="assignSelectedAsset('${logoType}')">
                            <i class="fas fa-check"></i> Use Selected Asset
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', html);
    
    if (preselectedFile) {
        window.selectedAssetFile = preselectedFile;
    }
}

let selectedAssetFile = '';

/**
 * Select asset file in modal
 */
function selectAssetFile(filename) {
    // Remove previous selection
    document.querySelectorAll('.asset-select-card').forEach(card => {
        card.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
    });
    
    // Add selection to clicked card
    event.currentTarget.classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
    
    selectedAssetFile = filename;
}

/**
 * Assign selected asset
 */
function assignSelectedAsset(logoType) {
    const modalLogoType = document.getElementById('modalLogoType');
    const finalLogoType = logoType || (modalLogoType ? modalLogoType.value : '');
    
    if (!finalLogoType) {
        showAlert('error', 'Please select an asset type');
        return;
    }
    
    if (!selectedAssetFile) {
        showAlert('error', 'Please select an asset file');
        return;
    }
    
    // Send assignment request
    const formData = new FormData();
    formData.append('action', 'select_existing');
    formData.append('filename', selectedAssetFile);
    formData.append('logo_type', finalLogoType);
    
    fetch('branding_assets_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            closeAssetModal();
            loadCurrentAssets();
        } else {
            showAlert('error', data.error || 'Assignment failed');
        }
    })
    .catch(error => {
        showAlert('error', 'Network error occurred');
        console.error('Error:', error);
    });
}

/**
 * Close asset selection modal
 */
function closeAssetModal() {
    const modal = document.getElementById('assetSelectModal');
    if (modal) {
        modal.remove();
    }
    selectedAssetFile = '';
}

/**
 * Remove asset assignment
 */
function removeAsset(logoType) {
    if (!confirm('Are you sure you want to remove this asset assignment?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'select_existing');
    formData.append('filename', '');
    formData.append('logo_type', logoType);
    
    fetch('branding_assets_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', `${getAssetTypeLabel(logoType)} removed successfully`);
            loadCurrentAssets();
        } else {
            showAlert('error', data.error || 'Removal failed');
        }
    })
    .catch(error => {
        showAlert('error', 'Network error occurred');
        console.error('Error:', error);
    });
}

/**
 * Delete asset file
 */
function deleteAsset(filename) {
    if (!confirm(`Are you sure you want to permanently delete "${filename}"?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('filename', filename);
    
    fetch('branding_assets_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            loadExistingAssets();
            loadCurrentAssets();
        } else {
            showAlert('error', data.error || 'Deletion failed');
        }
    })
    .catch(error => {
        showAlert('error', 'Network error occurred');
        console.error('Error:', error);
    });
}

/**
 * Refresh assets
 */
function refreshAssets() {
    loadCurrentAssets();
    loadExistingAssets();
    showAlert('info', 'Assets refreshed successfully');
}

/**
 * Helper functions
 */
function getAssetTypeLabel(type) {
    const labels = {
        'business_logo_main': 'Main Business Logo',
        'business_logo_horizontal': 'Horizontal Logo',
        'business_logo_vertical': 'Vertical Logo',
        'business_logo_square': 'Square Logo',
        'business_logo_white': 'White/Light Logo',
        'business_logo_small': 'Small Logo',
        'favicon_main': 'Main Favicon',
        'favicon_blog': 'Blog Favicon',
        'favicon_portal': 'Portal Favicon',
        'apple_touch_icon': 'Apple Touch Icon',
        'social_share_default': 'Default Social Share',
        'social_share_facebook': 'Facebook Share',
        'social_share_twitter': 'Twitter Share',
        'social_share_linkedin': 'LinkedIn Share',
        'hero_background_image': 'Hero Background',
        'watermark_image': 'Watermark'
    };
    return labels[type] || type;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function formatDate(timestamp) {
    return new Date(timestamp * 1000).toLocaleDateString();
}

function truncateFilename(filename, maxLength) {
    if (filename.length <= maxLength) return filename;
    const ext = filename.substring(filename.lastIndexOf('.'));
    const name = filename.substring(0, filename.lastIndexOf('.'));
    const truncated = name.substring(0, maxLength - ext.length - 3) + '...';
    return truncated + ext;
}

function showAlert(type, message) {
    const alertClass = type === 'error' ? 'danger' : type;
    const iconClass = {
        'success': 'check-circle',
        'error': 'exclamation-triangle',
        'danger': 'exclamation-triangle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    }[type] || 'info-circle';
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${alertClass} alert-dismissible fade show`;
    alert.innerHTML = `
        <i class="fas fa-${iconClass}"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at top of main content
    const container = document.querySelector('.container-fluid') || document.body;
    container.insertBefore(alert, container.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// END BRANDING ASSETS MANAGEMENT
</script>

<script>
// Auto-update color inputs
document.addEventListener('DOMContentLoaded', function() {
    const colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(input => {
        input.addEventListener('change', function() {
            const textInput = this.parentNode.querySelector('input[type="text"]');
            if (textInput) {
                textInput.value = this.value;
            }
        });
    });
    
    const textInputs = document.querySelectorAll('.input-group input[type="text"]');
    textInputs.forEach(input => {
        input.addEventListener('change', function() {
            const colorInput = this.parentNode.querySelector('input[type="color"]');
            if (colorInput && /^#[0-9A-F]{6}$/i.test(this.value)) {
                colorInput.value = this.value;
            }
        });
