<?php
/* 
 * Content Settings Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: content_settings.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Unified content management system with tabbed interface
 * DETAILED DESCRIPTION:
 * This file provides a unified interface for managing content settings across the application.
 * It allows administrators to configure settings for different content areas, including
 * general site settings, homepage content, modular sections, media assets, and individual pages.
 * The tabbed interface enhances usability by organizing settings into distinct categories.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/settings/general_content_settings.php
 * - /public_html/assets/includes/settings/home_content_settings.php
 * - /public_html/assets/includes/settings/sections_content_settings.php
 * - /public_html/assets/includes/settings/media_content_settings.php
 * - /public_html/assets/includes/settings/pages_content_settings.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Unified tabbed interface for all content settings
 * - General tab (site-wide content)
 * - Home Page tab (homepage specific content)
 * - Sections tab (modular content sections)
 * - Media tab (image and video management)
 * - Pages tab (page-specific content)
 */

include_once '../assets/includes/main.php';

// Load persistent content settings from config files
$settings_files = [
    'general' => PROJECT_ROOT . '/public_html/assets/includes/settings/general_content_settings.php',
    'home' => PROJECT_ROOT . '/public_html/assets/includes/settings/home_content_settings.php',
    'sections' => PROJECT_ROOT . '/public_html/assets/includes/settings/sections_content_settings.php',
    'media' => PROJECT_ROOT . '/public_html/assets/includes/settings/media_content_settings.php',
    'pages' => PROJECT_ROOT . '/public_html/assets/includes/settings/pages_content_settings.php'
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
        $php_code = "<?php\n// Content Settings - {$type}\n// Last updated: " . date('Y-m-d H:i:s') . "\n\n";
        $php_code .= "\$content_settings = " . var_export($default_settings, true) . ";\n";
        file_put_contents($file, $php_code);
    }
}

$settings = [];
foreach ($settings_files as $type => $file) {
    if (file_exists($file)) {
        include $file;
        // Each file should populate its own settings array which we merge here
        $settings[$type] = isset($content_settings) ? $content_settings : [];
        unset($content_settings); // Clear for next iteration
    } else {
        $settings[$type] = [];
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messages = [];
    $success = true;

    // Handle file uploads first
    if (!empty($_FILES)) {
        foreach ($_FILES as $file_key => $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $temp_name = $file['tmp_name'];
                $target_path = PROJECT_ROOT . '/public_html/assets/img/' . basename($file['name']);
                
                // Handle image optimization and resizing here
                if (move_uploaded_file($temp_name, $target_path)) {
                    $messages[] = "File " . basename($file['name']) . " uploaded successfully.";
                    // Update the corresponding setting
                    $section = explode('_', $file_key)[0];
                    $settings[$section][$file_key] = 'assets/img/' . basename($file['name']);
                } else {
                    $success = false;
                    $messages[] = "Error uploading " . basename($file['name']);
                }
            }
        }
    }

    // Process each settings category
    foreach (['general', 'home', 'sections', 'media', 'pages'] as $category) {
        if (isset($_POST[$category])) {
            // Get the file path for this category
            $file_path = $settings_files[$category];
            
            // Create settings array
            $settings_array = [];
            foreach ($_POST[$category] as $key => $value) {
                // Handle arrays (for repeatable content)
                if (is_array($value)) {
                    $settings_array[$key] = array_values(array_filter($value));
                } else {
                    $settings_array[$key] = trim($value);
                }
            }

            // Generate PHP code
            $php_code = "<?php\n// Content Settings - {$category}\n// Last updated: " . date('Y-m-d H:i:s') . "\n\n";
            $php_code .= "\$content_settings = " . var_export($settings_array, true) . ";\n";
            
            // Save to file
            if (file_put_contents($file_path, $php_code)) {
                $messages[] = ucfirst($category) . " content saved successfully.";
            } else {
                $success = false;
                $messages[] = "Error saving " . $category . " content.";
            }

            // Update the settings array for the current request
            $settings[$category] = $settings_array;
        }
    }

    // Update content-vars.php with the new settings
    $vars_content = "<?php\n// content-vars.php - Auto-generated by content_settings.php\n// Last updated: " . date('Y-m-d H:i:s') . "\n\n";
    foreach ($settings as $category => $category_settings) {
        foreach ($category_settings as $key => $value) {
            if (is_array($value)) {
                $vars_content .= "\${$key} = " . var_export($value, true) . ";\n";
            } else {
                $vars_content .= "\${$key} = " . var_export($value, true) . ";\n";
            }
        }
    }
    file_put_contents(PROJECT_ROOT . '/public_html/assets/includes/content-vars.php', $vars_content);

    // Add alert message
    if ($success) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Content settings saved successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>Error saving some settings. Please check the messages below.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

?>
<?= template_admin_header('Content Settings', 'settings', 'content') ?>
<!-- Include tab fixes CSS -->
<link rel="stylesheet" href="../assets/css/tab-fixes.css">
<?php
// Load image settings configuration
include_once 'public_image_settings_config.php';
?>

<div class="content-title">
    <div class="title">
        <div class="icon">
            <i class="fas fa-edit"></i>
        </div>
        <div class="txt">
            <h2>Content Settings</h2>
            <p>Manage website content and media</p>
        </div>
    </div>
</div>

    <form action="" method="post" id="contentSettingsForm" class="needs-validation" enctype="multipart/form-data" novalidate>
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <div class="content-header form-actions-header mb-2">
                    <div class="form-actions">
                        <a href="settings.php" class="btn btn-outline-secondary" aria-label="Cancel and return to settings">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success" aria-label="Save content changes">
                            <i class="fas fa-save me-2"></i>Save Content
                        </button>
                    </div>
                </div>
                <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">
                            <i class="fas fa-globe"></i>
                            General
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="home-tab" data-bs-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false">
                            <i class="fas fa-home"></i>
                            Home Page
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="sections-tab" data-bs-toggle="tab" href="#sections" role="tab" aria-controls="sections" aria-selected="false">
                            <i class="fas fa-th-large"></i>
                            Sections
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="media-tab" data-bs-toggle="tab" href="#media" role="tab" aria-controls="media" aria-selected="false">
                            <i class="fas fa-images"></i>
                            Media
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="pages-tab" data-bs-toggle="tab" href="#pages" role="tab" aria-controls="pages" aria-selected="false">
                            <i class="fas fa-file-alt"></i>
                            Pages
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="card-body p-4">
                <div class="tab-content">
                    <!-- General Tab -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <div class="row">
                            <?php
                            $general_meta = [
                                'sitename' => ['Site Name', 'text', 'The name of your website'],
                                'site_topic' => ['Site Topic', 'text', 'A brief description of your website'],
                                'tagline' => ['Tagline', 'text', 'Your site\'s tagline or slogan'],
                                'meta_description' => ['Meta Description', 'textarea', 'Site-wide meta description for SEO'],
                                'meta_keywords' => ['Meta Keywords', 'text', 'Comma-separated keywords for SEO'],
                                'footer_text' => ['Footer Text', 'text', 'Text to display in the footer'],
                                'copyright_text' => ['Copyright Text', 'text', 'Copyright notice in the footer'],
                                'contact_email' => ['Contact Email', 'email', 'Primary contact email address'],
                                'contact_phone' => ['Contact Phone', 'text', 'Primary contact phone number'],
                                'contact_address' => ['Contact Address', 'textarea', 'Physical address']
                            ];
                            foreach ($general_meta as $key => $meta): 
                                $value = $settings['general'][$key] ?? '';
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="general_<?= $key ?>" class="form-label fw-bold">
                                            <?= htmlspecialchars($meta[0]) ?>
                                        </label>
                                        <?php if (!empty($meta[2])): ?>
                                            <div class="form-text text-muted mb-2">
                                                <?= htmlspecialchars($meta[2]) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($meta[1] === 'textarea'): ?>
                                            <textarea name="general[<?= $key ?>]" 
                                                      id="general_<?= $key ?>" 
                                                      class="form-control" 
                                                      rows="3"><?= htmlspecialchars($value) ?></textarea>
                                        <?php else: ?>
                                            <input type="<?= $meta[1] ?>" 
                                                   name="general[<?= $key ?>]" 
                                                   id="general_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Home Page Tab -->
                    <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="row">
                            <?php
                            $home_meta = [
                                'hero_title' => ['Hero Title', 'text', 'Main heading in the hero section'],
                                'hero_subtitle' => ['Hero Subtitle', 'text', 'Subheading in the hero section'],
                                'hero_text' => ['Hero Text', 'textarea', 'Main text content in the hero section'],
                                'cta_button_text' => ['CTA Button Text', 'text', 'Text for the call-to-action button'],
                                'cta_button_url' => ['CTA Button URL', 'text', 'URL for the call-to-action button'],
                                'featured_services' => ['Featured Services', 'textarea', 'List of featured services (one per line)'],
                                'about_title' => ['About Section Title', 'text', 'Title for the about section'],
                                'about_text' => ['About Section Content', 'textarea', 'Content for the about section'],
                                'mission_statement' => ['Mission Statement', 'textarea', 'Company mission statement'],
                                'vision_statement' => ['Vision Statement', 'textarea', 'Company vision statement']
                            ];
                            foreach ($home_meta as $key => $meta): 
                                $value = $settings['home'][$key] ?? '';
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="form-group">
                                        <label for="home_<?= $key ?>" class="form-label fw-bold">
                                            <?= htmlspecialchars($meta[0]) ?>
                                        </label>
                                        <?php if (!empty($meta[2])): ?>
                                            <div class="form-text text-muted mb-2">
                                                <?= htmlspecialchars($meta[2]) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($meta[1] === 'textarea'): ?>
                                            <textarea name="home[<?= $key ?>]" 
                                                      id="home_<?= $key ?>" 
                                                      class="form-control <?= ($key === 'hero_text' || $key === 'about_text' || $key === 'mission_statement' || $key === 'vision_statement') ? 'summernote' : '' ?>" 
                                                      rows="3"><?= htmlspecialchars($value) ?></textarea>
                                        <?php else: ?>
                                            <input type="<?= $meta[1] ?>" 
                                                   name="home[<?= $key ?>]" 
                                                   id="home_<?= $key ?>" 
                                                   class="form-control" 
                                                   value="<?= htmlspecialchars($value) ?>">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Sections Tab -->
                    <div class="tab-pane fade" id="sections" role="tabpanel" aria-labelledby="sections-tab">
                        <div class="row">
                            <?php
                            // Services Section
                            echo '<div class="col-12 mb-4"><h3>Services Section</h3></div>';
                            for ($i = 1; $i <= 6; $i++): 
                                $service_title = $settings['sections']['service' . $i . '_title'] ?? '';
                                $service_desc = $settings['sections']['service' . $i . '_description'] ?? '';
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="mb-0">Service <?= $i ?></h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Title</label>
                                                <input type="text" 
                                                       name="sections[service<?= $i ?>_title]" 
                                                       class="form-control" 
                                                       value="<?= htmlspecialchars($service_title) ?>">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Description</label>
                                                <textarea name="sections[service<?= $i ?>_description]" 
                                                          class="form-control summernote" 
                                                          rows="3"><?= htmlspecialchars($service_desc) ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>

                            <!-- Features Section -->
                            <?php 
                            echo '<div class="col-12 mt-4 mb-4"><h3>Features Section</h3></div>';
                            for ($i = 1; $i <= 4; $i++): 
                                $feature_title = $settings['sections']['feature' . $i . '_title'] ?? '';
                                $feature_desc = $settings['sections']['feature' . $i . '_description'] ?? '';
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="mb-0">Feature <?= $i ?></h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Title</label>
                                                <input type="text" 
                                                       name="sections[feature<?= $i ?>_title]" 
                                                       class="form-control" 
                                                       value="<?= htmlspecialchars($feature_title) ?>">
                                            </div>
                                            <div class="form-group">
                                                <label class="form-label">Description</label>
                                                <textarea name="sections[feature<?= $i ?>_description]" 
                                                          class="form-control summernote" 
                                                          rows="3"><?= htmlspecialchars($feature_desc) ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <!-- Media Tab -->
                    <div class="tab-pane fade" id="media" role="tabpanel" aria-labelledby="media-tab">
                        <div class="row">
                            <?php foreach ($images as $key => $image): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h4 class="mb-0"><?= htmlspecialchars($image['label']) ?></h4>
                                        </div>
                                        <div class="card-body">
                                            <?php
                                            $file_exists = file_exists(PROJECT_ROOT . '/public_html/' . $image['path']);
                                            if ($file_exists):
                                                if (!empty($image['is_video'])): ?>
                                                    <video class="img-fluid mb-3" controls>
                                                        <source src="../../<?= htmlspecialchars($image['path']) ?>" type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                <?php else: ?>
                                                    <img src="../../<?= htmlspecialchars($image['path']) ?>" 
                                                         class="img-fluid mb-3" 
                                                         alt="<?= htmlspecialchars($image['label']) ?>">
                                                <?php endif;
                                            endif; ?>
                                            <div class="form-group">
                                                <label class="form-label">Upload New <?= $image['is_video'] ? 'Video' : 'Image' ?></label>
                                                <input type="file" 
                                                       name="media_<?= $key ?>" 
                                                       class="form-control" 
                                                       accept="<?= $image['is_video'] ? 'video/*' : 'image/*' ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Pages Tab -->
                    <div class="tab-pane fade" id="pages" role="tabpanel" aria-labelledby="pages-tab">
                        <div class="row">
                            <?php
                            $pages = [
                                'about' => 'About Us',
                                'services' => 'Services',
                                'portfolio' => 'Portfolio',
                                'contact' => 'Contact',
                                'privacy' => 'Privacy Policy',
                                'terms' => 'Terms of Service'
                            ];
                            
                            foreach ($pages as $page_key => $page_title): 
                                $page_content = $settings['pages'][$page_key . '_content'] ?? '';
                                $page_meta_title = $settings['pages'][$page_key . '_meta_title'] ?? '';
                                $page_meta_desc = $settings['pages'][$page_key . '_meta_description'] ?? '';
                            ?>
                                <div class="col-12 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="mb-0"><?= htmlspecialchars($page_title) ?></h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Meta Title</label>
                                                        <input type="text" 
                                                               name="pages[<?= $page_key ?>_meta_title]" 
                                                               class="form-control" 
                                                               value="<?= htmlspecialchars($page_meta_title) ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">Meta Description</label>
                                                        <input type="text" 
                                                               name="pages[<?= $page_key ?>_meta_description]" 
                                                               class="form-control" 
                                                               value="<?= htmlspecialchars($page_meta_desc) ?>">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">Page Content</label>
                                                        <textarea name="pages[<?= $page_key ?>_content]" 
                                                                  class="form-control summernote" 
                                                                  rows="10"><?= htmlspecialchars($page_content) ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light">
                <div class="content-header form-actions-header">
                    <div class="form-actions">
                        <a href="settings.php" class="btn btn-outline-secondary" aria-label="Cancel and return to settings">
                            <i class="fa fa-arrow-left" aria-hidden="true"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success" aria-label="Save content changes">
                            <i class="fas fa-save me-2"></i>Save Content
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>


<script>
$(document).ready(function() {
    // Initialize Summernote for content editors
    $('.summernote').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview']]
        ],
        callbacks: {
            onImageUpload: function(files) {
                // You can implement image upload here if needed
                console.log('Image upload triggered');
            }
        }
    });

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

    // Get the active tab from URL hash or default to general
    var activeTabId = window.location.hash ? window.location.hash.substring(1) : 'general';
    
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
            var targetId = event.target.getAttribute('data-bs-target');
            history.replaceState(null, null, targetId);
            
            // Ensure proper display of tab content
            document.querySelector(targetId).classList.add('show', 'active');
        });
    });
});
</script>

<?= template_admin_footer() ?>