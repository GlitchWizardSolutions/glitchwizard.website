<?php
/**
 * New Installation Wizard
 * Guides users through the essential setup process step by step
 */

// Include admin main file which handles authentication
include_once '../assets/includes/main.php';

// Get current setup progress
$business_setup_items = [
    'site_settings' => [
        'title' => 'Site Information',
        'description' => 'Basic site name, description, and contact details',
        'file' => 'site_settings.php',
        'icon' => 'globe',
        'category' => 'essential',
        'completed' => file_exists(__DIR__ . '/../../../assets/settings/site_settings.php'),
        'step' => 1
    ],
    'contact_settings' => [
        'title' => 'Contact Information',
        'description' => 'Business address, phone, email, and social media',
        'file' => 'contact_settings.php',
        'icon' => 'address-card',
        'category' => 'essential',
        'completed' => file_exists(__DIR__ . '/../../../assets/settings/contact_settings.php'),
        'step' => 2
    ],
    'branding_settings' => [
        'title' => 'Brand & Logo',
        'description' => 'Upload logo, set colors, and brand identity',
        'file' => 'branding_settings.php',
        'icon' => 'palette',
        'category' => 'essential',
        'completed' => file_exists(__DIR__ . '/../../../assets/settings/branding_settings.php'),
        'step' => 3
    ],
    'email_settings' => [
        'title' => 'Email Configuration',
        'description' => 'SMTP settings for sending emails from your site',
        'file' => 'email_settings.php',
        'icon' => 'envelope',
        'category' => 'essential',
        'completed' => file_exists(__DIR__ . '/../../../assets/settings/email_settings.php'),
        'step' => 4
    ],
    'seo_settings' => [
        'title' => 'SEO & Meta Tags',
        'description' => 'Search engine optimization and meta information',
        'file' => 'seo_settings.php',
        'icon' => 'search',
        'category' => 'essential',
        'completed' => file_exists(__DIR__ . '/../../../assets/settings/seo_settings.php'),
        'step' => 5
    ]
];

// Calculate progress
$total_steps = count($business_setup_items);
$completed_steps = array_sum(array_column($business_setup_items, 'completed'));
$progress_percentage = round(($completed_steps / $total_steps) * 100);

// Determine current step
$current_step = 1;
foreach ($business_setup_items as $item) {
    if (!$item['completed']) {
        $current_step = $item['step'];
        break;
    }
    $current_step = $item['step'] + 1; // If all completed, go to final step
}

// Handle form submissions for quick setup
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'quick_setup':
            // Create basic settings files with default values
            $quick_setup_success = createQuickSetupFiles();
            if ($quick_setup_success) {
                $success_message = "Quick setup completed! Basic settings files have been created. You can now customize them.";
                // Recalculate progress
                foreach ($business_setup_items as $key => $item) {
                    $business_setup_items[$key]['completed'] = file_exists(__DIR__ . '/../../../assets/settings/' . basename($item['file']));
                }
                $completed_steps = array_sum(array_column($business_setup_items, 'completed'));
                $progress_percentage = round(($completed_steps / $total_steps) * 100);
            } else {
                $error_message = "Error during quick setup. Please check file permissions.";
            }
            break;
            
        case 'skip_wizard':
            header('Location: settings_dash.php');
            exit;
    }
}

function createQuickSetupFiles() {
    $settings_dir = __DIR__ . '/../../../assets/settings/';
    
    // Ensure settings directory exists
    if (!is_dir($settings_dir)) {
        mkdir($settings_dir, 0755, true);
    }
    
    $templates = [
        'site_settings.php' => [
            'site_name' => 'Your Business Name',
            'site_tagline' => 'Your business tagline or description',
            'site_description' => 'A brief description of your business and services',
            'copyright_text' => '© ' . date('Y') . ' Your Business Name. All rights reserved.',
            'established_year' => date('Y')
        ],
        'contact_settings.php' => [
            'business_name' => 'Your Business Name',
            'address_line1' => '123 Business Street',
            'address_line2' => 'Suite 100',
            'city' => 'Your City',
            'state' => 'Your State',
            'zip_code' => '12345',
            'phone' => '(555) 123-4567',
            'email' => 'info@yourbusiness.com',
            'business_hours' => 'Monday - Friday: 9:00 AM - 5:00 PM'
        ],
        'branding_settings.php' => [
            'logo_url' => '/assets/img/logo.png',
            'favicon_url' => '/assets/img/favicon.ico',
            'primary_color' => '#007bff',
            'secondary_color' => '#6c757d',
            'accent_color' => '#28a745',
            'brand_font' => 'Arial, sans-serif'
        ],
        'email_settings.php' => [
            'smtp_host' => 'smtp.yourdomain.com',
            'smtp_port' => '587',
            'smtp_username' => 'noreply@yourdomain.com',
            'smtp_password' => '',
            'from_email' => 'noreply@yourdomain.com',
            'from_name' => 'Your Business Name'
        ],
        'seo_settings.php' => [
            'meta_title' => 'Your Business Name - Professional Services',
            'meta_description' => 'Professional business services and solutions for your needs.',
            'meta_keywords' => 'business, services, professional, solutions',
            'og_title' => 'Your Business Name',
            'og_description' => 'Professional business services and solutions for your needs.',
            'og_image' => '/assets/img/og-image.jpg'
        ]
    ];
    
    $success = true;
    foreach ($templates as $filename => $settings) {
        $file_path = $settings_dir . $filename;
        if (!file_exists($file_path)) {
            $content = "<?php\n";
            $content .= "/**\n";
            $content .= " * " . ucfirst(str_replace(['_settings.php', '_'], ['', ' '], $filename)) . " Settings\n";
            $content .= " * Auto-generated by New Installation Wizard\n";
            $content .= " */\n\n";
            
            foreach ($settings as $key => $value) {
                $content .= "\$" . $key . " = " . var_export($value, true) . ";\n";
            }
            
            if (file_put_contents($file_path, $content) === false) {
                $success = false;
            }
        }
    }
    
    return $success;
}

include '../assets/includes/main.php';
?>

<?php echo template_admin_header('New Installation Wizard', 'settings', 'wizard'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="bi bi-rocket-takeoff"></i>&nbsp;&nbsp;New Installation Wizard
                    </h1>
                    <p class="text-muted mb-0">Get your business website up and running in 5 easy steps</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="settings_dash.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i>&nbsp;&nbsp;Back to Dashboard
                    </a>
                    <a href="settings_help.php" class="btn btn-info btn-sm">
                        <i class="bi bi-question-circle"></i>&nbsp;&nbsp;Help
                    </a>
                </div>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success_message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error_message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Progress Overview -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title">Setup Progress: <?= $completed_steps ?>/<?= $total_steps ?> Steps Complete</h4>
                            <div class="progress progress-lg mb-3">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?= $progress_percentage ?>%" 
                                     aria-valuenow="<?= $progress_percentage ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <?= $progress_percentage ?>%
                                </div>
                            </div>
                            <?php if ($progress_percentage < 100): ?>
                                <p class="text-muted">Complete the essential settings to launch your business website.</p>
                            <?php else: ?>
                                <p class="text-success"><i class="bi bi-check-circle-fill"></i> <strong>Congratulations!</strong> Your essential setup is complete. Your website is ready to go live!</p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="progress-circle-xl" data-progress="<?= $progress_percentage ?>">
                                <div class="progress-text">
                                    <span class="percentage h1"><?= $progress_percentage ?>%</span>
                                    <br><span class="label">Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Setup Options -->
            <?php if ($progress_percentage < 50): ?>
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header">
                    <h5 class="mb-0 text-primary">
                        <i class="bi bi-magic"></i>&nbsp;&nbsp;Quick Setup Options
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6>Need to get started quickly?</h6>
                            <p class="text-muted mb-3">
                                We can create basic settings files with placeholder content that you can customize later. 
                                This will get your site functional immediately.
                            </p>
                            <ul class="text-muted">
                                <li>Creates all essential settings files</li>
                                <li>Uses professional placeholder content</li>
                                <li>You can customize everything later</li>
                                <li>Takes less than 30 seconds</li>
                            </ul>
                        </div>
                        <div class="col-md-4 text-center">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="quick_setup">
                                <button type="submit" class="btn btn-primary btn-lg mb-2">
                                    <i class="bi bi-magic"></i><br>
                                    Quick Setup
                                </button>
                            </form>
                            <br>
                            <small class="text-muted">Auto-create basic settings</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Step-by-Step Setup -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ol"></i>&nbsp;&nbsp;Step-by-Step Setup Guide
                    </h5>
                </div>
                <div class="card-body">
                    <div class="setup-steps">
                        <?php foreach ($business_setup_items as $key => $item): ?>
                            <div class="step-item <?= $item['completed'] ? 'completed' : ($item['step'] == $current_step ? 'current' : 'pending') ?>">
                                <div class="step-number">
                                    <?php if ($item['completed']): ?>
                                        <i class="bi bi-check"></i>
                                    <?php else: ?>
                                        <?= $item['step'] ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="step-content">
                                    <div class="step-header">
                                        <h6 class="step-title">
                                            <i class="bi bi-<?= $item['icon'] === 'address-card' ? 'person-vcard' : ($item['icon'] === 'palette' ? 'palette' : ($item['icon'] === 'envelope' ? 'envelope' : ($item['icon'] === 'search' ? 'search' : 'globe'))) ?>"></i>
                                            <?= $item['title'] ?>
                                        </h6>
                                        <div class="step-status">
                                            <?php if ($item['completed']): ?>
                                                <span class="badge badge-success">Complete</span>
                                            <?php elseif ($item['step'] == $current_step): ?>
                                                <span class="badge badge-warning">Current Step</span>
                                            <?php else: ?>
                                                <span class="badge badge-light">Pending</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <p class="step-description"><?= $item['description'] ?></p>
                                    
                                    <div class="step-actions">
                                        <?php if ($item['completed']): ?>
                                            <a href="<?= $item['file'] ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-pencil-square"></i> Edit Settings
                                            </a>
                                            <span class="text-success ml-2">
                                                <i class="bi bi-check-circle-fill"></i> Configuration exists
                                            </span>
                                        <?php else: ?>
                                            <a href="<?= $item['file'] ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-gear"></i> Configure Now
                                            </a>
                                            <?php if ($item['step'] == $current_step): ?>
                                                <span class="text-warning ml-2">
                                                    <i class="bi bi-arrow-left"></i> Start here
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Completion Actions -->
            <?php if ($progress_percentage >= 100): ?>
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-trophy"></i>&nbsp;&nbsp;Setup Complete!
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-success">🎉 Congratulations! Your essential setup is complete.</h6>
                            <p class="mb-3">Your business website is now ready to go live. Here's what you can do next:</p>
                            <ul>
                                <li><strong>Preview your website</strong> - See how it looks to visitors</li>
                                <li><strong>Configure additional features</strong> - Add blog, shop, gallery, etc.</li>
                                <li><strong>Customize design</strong> - Adjust colors, fonts, and layout</li>
                                <li><strong>Add content</strong> - Create pages, posts, and products</li>
                            </ul>
                        </div>
                        <div class="col-md-4 text-center">
                            <a href="../../index.php" target="_blank" class="btn btn-success btn-lg mb-2">
                                <i class="bi bi-eye"></i><br>
                                Preview Website
                            </a>
                            <br>
                            <a href="settings_dash.php" class="btn btn-primary">
                                <i class="bi bi-gear"></i> Advanced Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Help & Resources -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-question-circle"></i>&nbsp;&nbsp;Need Help?
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="bi bi-book-half fs-1 text-info mb-2"></i>
                                <h6>Setup Guide</h6>
                                <p class="text-muted">Detailed instructions for each step</p>
                                <a href="settings_help.php" class="btn btn-info btn-sm">View Guide</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="bi bi-camera-video fs-1 text-success mb-2"></i>
                                <h6>Video Tutorials</h6>
                                <p class="text-muted">Watch step-by-step video guides</p>
                                <a href="#" class="btn btn-success btn-sm">Watch Videos</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <i class="bi bi-headset fs-1 text-warning mb-2"></i>
                                <h6>Support</h6>
                                <p class="text-muted">Get help from our support team</p>
                                <a href="mailto:support@example.com" class="btn btn-warning btn-sm">Contact Support</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Skip Wizard Option -->
            <div class="text-center mb-4">
                <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="skip_wizard">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="bi bi-skip-forward"></i> Skip Wizard & Go to Advanced Dashboard
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<style>
/* Wizard-specific styles */
.progress-lg {
    height: 2rem;
}

.progress-circle-xl {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(#28a745 calc(var(--progress, 0) * 1%), #e9ecef 0);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin: 0 auto;
}

.progress-circle-xl::before {
    content: '';
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: white;
    position: absolute;
}

.progress-text {
    position: relative;
    z-index: 2;
    text-align: center;
}

.setup-steps {
    position: relative;
}

.step-item {
    display: flex;
    margin-bottom: 2rem;
    position: relative;
}

.step-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 25px;
    top: 50px;
    bottom: -30px;
    width: 2px;
    background: #e9ecef;
}

.step-item.completed:not(:last-child)::after {
    background: #28a745;
}

.step-number {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 1rem;
    flex-shrink: 0;
    z-index: 2;
    position: relative;
}

.step-item.completed .step-number {
    background: #28a745;
    color: white;
}

.step-item.current .step-number {
    background: #ffc107;
    color: #212529;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.3);
}

.step-item.pending .step-number {
    background: #e9ecef;
    color: #6c757d;
}

.step-content {
    flex: 1;
}

.step-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.step-title {
    margin: 0;
    flex: 1;
}

.step-description {
    color: #6c757d;
    margin-bottom: 1rem;
}

.step-actions {
    display: flex;
    align-items: center;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .step-item {
        flex-direction: column;
        text-align: center;
    }
    
    .step-item:not(:last-child)::after {
        display: none;
    }
    
    .step-number {
        margin: 0 auto 1rem auto;
    }
    
    .step-header {
        flex-direction: column;
        text-align: center;
    }
    
    .step-status {
        margin-top: 0.5rem;
    }
}
</style>

<script>
// Set progress circle custom property
document.addEventListener('DOMContentLoaded', function() {
    const progressCircle = document.querySelector('.progress-circle-xl');
    if (progressCircle) {
        const progress = <?= $progress_percentage ?>;
        progressCircle.style.setProperty('--progress', progress);
    }
});
</script>

<?php echo template_admin_footer(); ?>
