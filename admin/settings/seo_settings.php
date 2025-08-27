<?php
/**
 * SEO Settings Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: seo_settings.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Database-driven SEO configuration management
 * 
 * FEATURES:
 * - Meta tags management
 * - Site title and description
 * - Keywords configuration
 * - Analytics integration
 * - Social media meta tags
 * - Robots.txt configuration
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
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['Admin', 'Editor', 'Developer'])) {
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
            'site_title' => sanitize_input($_POST['site_title']),
            'site_description' => sanitize_input($_POST['site_description']),
            'site_keywords' => sanitize_input($_POST['site_keywords']),
            'google_analytics_id' => sanitize_input($_POST['google_analytics_id']),
            'google_tag_manager_id' => sanitize_input($_POST['google_tag_manager_id']),
            'facebook_app_id' => sanitize_input($_POST['facebook_app_id']),
            'twitter_site' => sanitize_input($_POST['twitter_site']),
            'robots_txt_content' => sanitize_input($_POST['robots_txt_content'])
        ];
        
        // Use SettingsManager to update SEO settings
        $result = $settingsManager->updateSeoSettings($data, $updated_by);
        
        if ($result) {
            $message = 'SEO settings updated successfully.';
            $message_type = 'success';
        } else {
            $message = 'Error updating SEO settings. Please check the error logs.';
            $message_type = 'error';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get current SEO settings
try {
    $current_settings = $settingsManager->getSeoSettings();
} catch (Exception $e) {
    $current_settings = [];
    $message = 'Warning: Could not load current SEO settings. ' . $e->getMessage();
    $message_type = 'warning';
}

// Utility function
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

$page_title = 'SEO Settings';
?>

<?= template_admin_header($page_title, 'settings', 'seo') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0"><i class="bi bi-globe"></i> SEO Settings</h1>
                    <p class="text-muted">Configure search engine optimization and meta tags</p>
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

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-search"></i> SEO Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_title" class="form-label">Site Title</label>
                                    <input type="text" class="form-control" id="site_title" name="site_title" 
                                           value="<?= htmlspecialchars($current_settings['site_title'] ?? '') ?>" 
                                           required maxlength="60">
                                    <div class="form-text">Main title for search engines (max 60 characters)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="site_keywords" class="form-label">Keywords</label>
                                    <input type="text" class="form-control" id="site_keywords" name="site_keywords" 
                                           value="<?= htmlspecialchars($current_settings['site_keywords'] ?? '') ?>"
                                           placeholder="keyword1, keyword2, keyword3">
                                    <div class="form-text">Comma-separated keywords for your site</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="site_description" class="form-label">Site Description</label>
                            <textarea class="form-control" id="site_description" name="site_description" 
                                      rows="3" maxlength="160" required><?= htmlspecialchars($current_settings['site_description'] ?? '') ?></textarea>
                            <div class="form-text">Meta description for search engines (max 160 characters)</div>
                        </div>

                        <hr>
                        <h6><i class="bi bi-analytics"></i> Analytics & Tracking</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="google_analytics_id" class="form-label">Google Analytics ID</label>
                                    <input type="text" class="form-control" id="google_analytics_id" name="google_analytics_id" 
                                           value="<?= htmlspecialchars($current_settings['google_analytics_id'] ?? '') ?>"
                                           placeholder="GA-XXXXXXXXX-X">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="google_tag_manager_id" class="form-label">Google Tag Manager ID</label>
                                    <input type="text" class="form-control" id="google_tag_manager_id" name="google_tag_manager_id" 
                                           value="<?= htmlspecialchars($current_settings['google_tag_manager_id'] ?? '') ?>"
                                           placeholder="GTM-XXXXXXX">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6><i class="bi bi-share"></i> Social Media</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="facebook_app_id" class="form-label">Facebook App ID</label>
                                    <input type="text" class="form-control" id="facebook_app_id" name="facebook_app_id" 
                                           value="<?= htmlspecialchars($current_settings['facebook_app_id'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="twitter_site" class="form-label">Twitter Site Handle</label>
                                    <input type="text" class="form-control" id="twitter_site" name="twitter_site" 
                                           value="<?= htmlspecialchars($current_settings['twitter_site'] ?? '') ?>"
                                           placeholder="@yoursite">
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h6><i class="bi bi-robot"></i> Robots.txt</h6>

                        <div class="mb-3">
                            <label for="robots_txt_content" class="form-label">Robots.txt Content</label>
                            <textarea class="form-control font-monospace" id="robots_txt_content" name="robots_txt_content" 
                                      rows="8"><?= htmlspecialchars($current_settings['robots_txt_content'] ?? "User-agent: *\nDisallow: /admin/\nDisallow: /private/\nSitemap: /sitemap.xml") ?></textarea>
                            <div class="form-text">Content for your robots.txt file</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="settings_dash.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save SEO Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= template_admin_footer() ?>
