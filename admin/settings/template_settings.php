<?php
/* 
 * Template Settings Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: template_settings.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Website template and theme configuration interface
 * DETAILED DESCRIPTION:
 * This file provides a comprehensive interface for managing website templates
 * and theme settings. It allows administrators to configure layout options,
 * color schemes, typography, and other visual aspects of the website through
 * an organized settings interface.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/assets/includes/settings/template_config.php
 * - /public_html/assets/includes/settings/theme_settings.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Template configuration
 * - Theme customization
 * - Layout settings
 * - Typography options
 * - Color scheme management
 */


include_once '../assets/includes/main.php';

// Load persistent blog settings from config files
$settings_files = [
    'content' => PROJECT_ROOT . '/public_html/assets/includes/settings/blog_content_settings.php',
    'comments' => PROJECT_ROOT . '/public_html/assets/includes/settings/blog_comments_settings.php',
    'display' => PROJECT_ROOT . '/public_html/assets/includes/settings/blog_display_settings.php',
    'seo' => PROJECT_ROOT . '/public_html/assets/includes/settings/blog_seo_settings.php'
];

// Create settings directory if it doesn't exist
$settings_dir = PROJECT_ROOT . '/public_html/assets/includes/settings';
if (!file_exists($settings_dir)) {
    mkdir($settings_dir, 0755, true);
}

// Initialize default settings if files don't exist
foreach ($settings_files as $type => $file) {
    if (!file_exists($file)) {
        $default_settings = [];
        $php_code = "<?php\n// Blog Settings - {$type}\n// Last updated: " . date('Y-m-d H:i:s') . "\n\n";
        $php_code .= "\$blog_settings = " . var_export($default_settings, true) . ";\n";
        file_put_contents($file, $php_code);
    }
}

$settings = [];
foreach ($settings_files as $type => $file) {
    if (file_exists($file)) {
        include $file;
        // Each file should populate its own settings array which we merge here
        $settings[$type] = isset($blog_settings) ? $blog_settings : [];
        unset($blog_settings); // Clear for next iteration
    } else {
        $settings[$type] = [];
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messages = [];
    $success = true;
    
    // Store the active tab
    $active_tab = isset($_POST['active_tab']) ? $_POST['active_tab'] : 'content';

    // Process each settings category
    foreach (['content', 'comments', 'display', 'seo'] as $category) {
        if (isset($_POST[$category])) {
            // Get the file path for this category
            $file_path = $settings_files[$category];
            
            // Create settings array
            $settings_array = [];
            foreach ($_POST[$category] as $key => $value) {
                // Handle boolean values from checkboxes
                if (in_array($key, [
                    'enable_posts', 'enable_pages', 'enable_categories',
                    'enable_tags', 'enable_authors', 'enable_archives',
                    'enable_comments', 'moderate_comments', 'notify_new_comments',
                    'enable_feeds', 'enable_search', 'enable_social_sharing',
                    'enable_post_thumbnails', 'enable_featured_images',
                    'generate_sitemap', 'enable_meta_tags', 'enable_schema_markup',
                    'enable_social_meta'
                ])) {
                    $settings_array[$key] = ($value === '1');
                }
                // Handle numeric values
                else if (in_array($key, [
                    'posts_per_page', 'excerpt_length', 'thumbnail_width',
                    'thumbnail_height', 'featured_image_width', 'featured_image_height',
                    'comment_max_length', 'recent_posts_count', 'popular_posts_count',
                    'related_posts_count', 'sitemap_frequency_days'
                ])) {
                    $settings_array[$key] = intval($value);
                }
                // Everything else as string
                else {
                    $settings_array[$key] = trim($value);
                }
            }

            // Generate PHP code
            $php_code = "<?php\n// Blog Settings - {$category}\n// Last updated: " . date('Y-m-d H:i:s') . "\n\n";
            $php_code .= "\$blog_settings = " . var_export($settings_array, true) . ";\n";
            
            // Save to file
            if (file_put_contents($file_path, $php_code)) {
                $messages[] = ucfirst($category) . " settings saved successfully.";
            } else {
                $success = false;
                $messages[] = "Error saving " . $category . " settings.";
            }

            // Update the settings array for the current request
            $settings[$category] = $settings_array;
        }
    }

    // Add alert message
    if ($success) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>Settings saved successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    } else {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-octagon-fill me-2"></i>Error saving some settings. Please check the messages below.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

?>
<?= template_admin_header('Blog Settings', 'settings', 'blog') ?>
<!-- Include tab fixes CSS -->
<link rel="stylesheet" href="../assets/css/tab-fixes.css">

<div class="content-title">
    <div class="title">
        <div class="icon">
            <i class="bi bi-layout-text-window-reverse"></i>
        </div>
        <div class="txt">
            <h2>Blog Settings</h2>
            <p>Manage blog settings and configurations</p>
        </div>
    </div>
</div>
<br>
    <form action="" method="post" id="blogSettingsForm" class="needs-validation" novalidate>
        <input type="hidden" name="active_tab" id="active_tab" value="content">
  <div class="form-actions">
                    <a href="settings_dash.php" class="btn btn-outline-secondary" aria-label="Cancel and return to settings">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success" aria-label="Save meta tags">
                        <i class="bi bi-save me-2"></i>Save Site Configuration Settings
                    </button>
                </div>
                <br>
    <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="content-tab" data-bs-toggle="tab" href="#content" role="tab" aria-controls="content" aria-selected="true">
                            <i class="bi bi-file-earmark-text"></i>
                            Content
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="comments-tab" data-bs-toggle="tab" href="#comments" role="tab" aria-controls="comments" aria-selected="false">
                            <i class="bi bi-chat-dots"></i>
                            Comments
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="display-tab" data-bs-toggle="tab" href="#display" role="tab" aria-controls="display" aria-selected="false">
                            <i class="bi bi-palette"></i>
                            Display
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="seo-tab" data-bs-toggle="tab" href="#seo" role="tab" aria-controls="seo" aria-selected="false">
                            <i class="bi bi-search"></i>
                            SEO
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="card-body p-4">
                <div class="tab-content">
                    <!-- Content Tab -->
                    <div class="tab-pane fade show active" id="content" role="tabpanel" aria-labelledby="content-tab">
                        <div class="row">
                            <?php
                            $content_meta = [
                                'enable_posts' => ['Enable Blog Posts', 'boolean', 'Allow creation and display of blog posts'],
                                'enable_pages' => ['Enable Pages', 'boolean', 'Allow creation and display of static pages'],
                                'enable_categories' => ['Enable Categories', 'boolean', 'Allow post categorization'],
                                'enable_tags' => ['Enable Tags', 'boolean', 'Allow post tagging'],
                                'posts_per_page' => ['Posts Per Page', 'number', 'Number of posts to display per page'],
                                'excerpt_length' => ['Excerpt Length', 'number', 'Number of words in post excerpts'],
                                'default_category' => ['Default Category', 'text', 'Default category for new posts'],
                                'post_slug_format' => ['Post URL Format', 'text', 'URL format for blog posts (e.g., {year}/{month}/{slug})']
                            ];
                            foreach ($content_meta as $key => $meta): 
                                $value = $settings['content'][$key] ?? '';
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="content_<?= $key ?>" class="form-label fw-bold">
                                            <?= htmlspecialchars($meta[0]) ?>
                                        </label>
                                        <?php if (!empty($meta[2])): ?>
                                            <div class="form-text text-muted mb-2">
                                                <?= htmlspecialchars($meta[2]) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($meta[1] === 'boolean'): ?>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" 
                                                       name="content[<?= $key ?>]" 
                                                       id="content_<?= $key ?>" 
                                                       class="form-check-input" 
                                                       value="1" 
                                                       <?= $value ? ' checked' : '' ?>>
                                                <label class="form-check-label" for="content_<?= $key ?>">
                                                   <?= htmlspecialchars($meta[0]) ?>
                                                </label>
                                            </div>
                                        <?php elseif ($meta[1] === 'number'): ?>
                                            <input type="number" 
                                                   name="content[<?= $key ?>]" 
                                                   id="content_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php else: ?>
                                            <input type="text" 
                                                   name="content[<?= $key ?>]" 
                                                   id="content_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Comments Tab -->
                    <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="comments-tab">
                        <div class="row">
                            <?php
                            $comments_meta = [
                                'enable_comments' => ['Enable Comments', 'boolean', 'Allow users to comment on posts'],
                                'moderate_comments' => ['Moderate Comments', 'boolean', 'Require approval before comments are published'],
                                'notify_new_comments' => ['Email Notifications', 'boolean', 'Send email when new comments are posted'],
                                'comment_max_length' => ['Maximum Comment Length', 'number', 'Maximum number of characters in comments'],
                                'comments_order' => ['Comments Display Order', 'select', 'Order to display comments', ['newest', 'oldest', 'best_rated']],
                                'comments_per_page' => ['Comments Per Page', 'number', 'Number of comments to display per page'],
                                'comment_reply_levels' => ['Reply Nesting Levels', 'number', 'Maximum levels of nested comment replies'],
                                'comment_blacklist' => ['Blacklisted Words', 'text', 'Comma-separated list of blocked words in comments']
                            ];
                            foreach ($comments_meta as $key => $meta): 
                                $value = $settings['comments'][$key] ?? '';
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="comments_<?= $key ?>" class="form-label fw-bold">
                                            <?= htmlspecialchars($meta[0]) ?>
                                        </label>
                                        <?php if (!empty($meta[2])): ?>
                                            <div class="form-text text-muted mb-2">
                                                <?= htmlspecialchars($meta[2]) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($meta[1] === 'boolean'): ?>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" 
                                                       name="comments[<?= $key ?>]" 
                                                       id="comments_<?= $key ?>" 
                                                       class="form-check-input" 
                                                       value="1" 
                                                       <?= $value ? ' checked' : '' ?>>
                                                <label class="form-check-label" for="comments_<?= $key ?>">
                                                    Enable <?= htmlspecialchars($meta[0]) ?>
                                                </label>
                                            </div>
                                        <?php elseif ($meta[1] === 'select'): ?>
                                            <select name="comments[<?= $key ?>]" 
                                                    id="comments_<?= $key ?>" 
                                                    class="form-select">
                                                <?php foreach ($meta[3] as $option): ?>
                                                    <option value="<?= htmlspecialchars($option) ?>" 
                                                            <?= $value === $option ? ' selected' : '' ?>>
                                                        <?= ucfirst(str_replace('_', ' ', $option)) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php elseif ($meta[1] === 'number'): ?>
                                            <input type="number" 
                                                   name="comments[<?= $key ?>]" 
                                                   id="comments_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php else: ?>
                                            <input type="text" 
                                                   name="comments[<?= $key ?>]" 
                                                   id="comments_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Display Tab -->
                    <div class="tab-pane fade" id="display" role="tabpanel" aria-labelledby="display-tab">
                        <div class="row">
                            <?php
                            $display_meta = [
                                'enable_post_thumbnails' => ['Post Thumbnails', 'boolean', 'Show thumbnails in post lists'],
                                'enable_featured_images' => ['Featured Images', 'boolean', 'Show featured images on single posts'],
                                'thumbnail_width' => ['Thumbnail Width', 'number', 'Width of post thumbnails in pixels'],
                                'thumbnail_height' => ['Thumbnail Height', 'number', 'Height of post thumbnails in pixels'],
                                'featured_image_width' => ['Featured Image Width', 'number', 'Width of featured images in pixels'],
                                'featured_image_height' => ['Featured Image Height', 'number', 'Height of featured images in pixels'],
                                'post_layout' => ['Post List Layout', 'select', 'Layout style for post listings', ['grid', 'list', 'masonry']],
                                'sidebar_position' => ['Sidebar Position', 'select', 'Position of the blog sidebar', ['right', 'left', 'none']]
                            ];
                            foreach ($display_meta as $key => $meta): 
                                $value = $settings['display'][$key] ?? '';
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="display_<?= $key ?>" class="form-label fw-bold">
                                            <?= htmlspecialchars($meta[0]) ?>
                                        </label>
                                        <?php if (!empty($meta[2])): ?>
                                            <div class="form-text text-muted mb-2">
                                                <?= htmlspecialchars($meta[2]) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($meta[1] === 'boolean'): ?>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" 
                                                       name="display[<?= $key ?>]" 
                                                       id="display_<?= $key ?>" 
                                                       class="form-check-input" 
                                                       value="1" 
                                                       <?= $value ? ' checked' : '' ?>>
                                                <label class="form-check-label" for="display_<?= $key ?>">
                                                    Enable <?= htmlspecialchars($meta[0]) ?>
                                                </label>
                                            </div>
                                        <?php elseif ($meta[1] === 'select'): ?>
                                            <select name="display[<?= $key ?>]" 
                                                    id="display_<?= $key ?>" 
                                                    class="form-select">
                                                <?php foreach ($meta[3] as $option): ?>
                                                    <option value="<?= htmlspecialchars($option) ?>" 
                                                            <?= $value === $option ? ' selected' : '' ?>>
                                                        <?= ucfirst($option) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php elseif ($meta[1] === 'number'): ?>
                                            <input type="number" 
                                                   name="display[<?= $key ?>]" 
                                                   id="display_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php else: ?>
                                            <input type="text" 
                                                   name="display[<?= $key ?>]" 
                                                   id="display_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- SEO Tab -->
                    <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                        <div class="row">
                            <?php
                            $seo_meta = [
                                'generate_sitemap' => ['Generate Sitemap', 'boolean', 'Automatically generate XML sitemap'],
                                'enable_meta_tags' => ['Meta Tags', 'boolean', 'Generate SEO meta tags automatically'],
                                'enable_schema_markup' => ['Schema Markup', 'boolean', 'Add schema.org markup to posts'],
                                'enable_social_meta' => ['Social Meta', 'boolean', 'Add Open Graph and Twitter Card meta tags'],
                                'sitemap_frequency_days' => ['Sitemap Update Frequency', 'number', 'Days between sitemap updates'],
                                'meta_description_length' => ['Meta Description Length', 'number', 'Maximum length for meta descriptions'],
                                'default_meta_title' => ['Default Meta Title', 'text', 'Default title format for blog pages'],
                                'default_meta_description' => ['Default Meta Description', 'text', 'Default description for blog pages']
                            ];
                            foreach ($seo_meta as $key => $meta): 
                                $value = $settings['seo'][$key] ?? '';
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="seo_<?= $key ?>" class="form-label fw-bold">
                                            <?= htmlspecialchars($meta[0]) ?>
                                        </label>
                                        <?php if (!empty($meta[2])): ?>
                                            <div class="form-text text-muted mb-2">
                                                <?= htmlspecialchars($meta[2]) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($meta[1] === 'boolean'): ?>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" 
                                                       name="seo[<?= $key ?>]" 
                                                       id="seo_<?= $key ?>" 
                                                       class="form-check-input" 
                                                       value="1" 
                                                       <?= $value ? ' checked' : '' ?>>
                                                <label class="form-check-label" for="seo_<?= $key ?>">
                                                    Enable <?= htmlspecialchars($meta[0]) ?>
                                                </label>
                                            </div>
                                        <?php elseif ($meta[1] === 'number'): ?>
                                            <input type="number" 
                                                   name="seo[<?= $key ?>]" 
                                                   id="seo_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php else: ?>
                                            <input type="text" 
                                                   name="seo[<?= $key ?>]" 
                                                   id="seo_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="form-actions">
                    <a href="settings_dash.php" class="btn btn-outline-secondary" aria-label="Cancel and return to settings">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success" aria-label="Save meta tags">
                        <i class="bi bi-save me-2"></i>Save Site Configuration Settings
                    </button>
                </div> 
                </div>
            </div>
        </div>
        
    </form>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    });

    // Get the active tab from PHP or URL hash or default to content
    var activeTabId = '<?= $active_tab ?? '' ?>' || (window.location.hash ? window.location.hash.substring(1) : 'content');
    
    // Update hidden input with current active tab
    document.getElementById('active_tab').value = activeTabId;
    
    // Activate the appropriate tab
    var activeTab = document.querySelector('[data-bs-target="#' + activeTabId + '"]');
    if (activeTab) {
        var tab = new bootstrap.Tab(activeTab);
        tab.show();
    }

    // Update URL hash when tab changes and ensure proper display
    var tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabs.forEach(function(tab) {
        tab.addEventListener('shown.bs.tab', function (event) {
            var targetId = event.target.getAttribute('href').substring(1);
            document.getElementById('active_tab').value = targetId;
            history.replaceState(null, null, '#' + targetId);
            
            // Ensure proper display of tab content
            document.querySelector(targetId).classList.add('show', 'active');
        });
    });
});
</script>

<?= template_admin_footer() ?>