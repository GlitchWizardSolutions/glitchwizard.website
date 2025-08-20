<?php
/* 
 * SEO Settings Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: seo_settings.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Manage search engine optimization settings
 * DETAILED DESCRIPTION:
 * This file provides a comprehensive interface for managing all SEO-related
 * settings including meta tags, sitemap configuration, robots.txt settings,
 * and other search engine optimization parameters. It helps maintain and
 * improve the website's search engine visibility and ranking.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/assets/includes/settings/seo_config.php
 * - /public_html/assets/includes/settings/meta_config.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Meta tag management
 * - Sitemap configuration
 * - Robots.txt settings
 * - Schema markup tools
 * - SEO analytics integration
 */
 
include_once '../assets/includes/main.php';


// Load persistent SEO settings from config file
$settings_file = PROJECT_ROOT . '/public_html/assets/includes/settings/seo_settings.php';
if (file_exists($settings_file))
{
    include $settings_file;
} else
{
    $seo_settings = [];
}

?>
<?= template_admin_header('Seo Settings', 'settings', 'seo') ?>

<div class="content-title">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path
                    d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z" />
            </svg>
        </div>
        <div class="txt">
            <h2>Accounts</h2>
            <p>View, edit, and create accounts.</p>
        </div>
    </div>
</div>
<br>
<form action="" method="post" id="seoSettingsForm" novalidate>
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center"
            style="padding: 1.25rem 1.5rem;">
            <h5 class="mb-0">SEO Settings</h5>
            <button type="submit" class="btn btn-success px-4">
                <i class="fas fa-save me-2"></i>Save Settings
            </button>
        </div>
        <div class="card-body" style="padding: 2rem 1.5rem;">
            <?php
            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST')
            {
                $new_seo_settings = $seo_settings;
                foreach ($_POST['seo'] as $page => $fields)
                {
                    $new_seo_settings[$page]['title'] = trim($fields['title'] ?? '');
                    $new_seo_settings[$page]['description'] = trim($fields['description'] ?? '');
                    $new_seo_settings[$page]['keywords'] = trim($fields['keywords'] ?? '');
                }
                // Save to file
                $config_content = "<?php\n$" . "seo_settings = " . var_export($new_seo_settings, true) . ";\n";
                if (file_put_contents($settings_file, $config_content))
                {
                    $seo_settings = $new_seo_settings;
                    echo '<div class="alert alert-success">SEO settings updated successfully.</div>';
                } else
                {
                    echo '<div class="alert alert-danger">Failed to save SEO settings.</div>';
                }
            }
            ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Page</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Keywords</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($seo_settings as $page => $fields): ?>
                            <tr>
                                <td><?= htmlspecialchars($page) ?></td>
                                <td><input type="text" name="seo[<?= htmlspecialchars($page) ?>][title]"
                                        class="form-control" value="<?= htmlspecialchars($fields['title'] ?? '') ?>" /></td>
                                <td><input type="text" name="seo[<?= htmlspecialchars($page) ?>][description]"
                                        class="form-control"
                                        value="<?= htmlspecialchars($fields['description'] ?? '') ?>" /></td>
                                <td><input type="text" name="seo[<?= htmlspecialchars($page) ?>][keywords]"
                                        class="form-control" value="<?= htmlspecialchars($fields['keywords'] ?? '') ?>" />
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?= template_admin_footer() ?>