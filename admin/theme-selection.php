<?php
/**
 * Brand Theme Selection and Preview System
 * 
 * Allows administrators to preview and select different branding themes
 * for the public-facing website.
 */

require_once '../assets/includes/private/gws-master-config.php';
require_once '../../private/gws-universal-functions.php';
require_once '../assets/includes/brand_loader.php';
require_once '../assets/includes/branding-functions.php';

// Check if user is admin (you may need to adjust this based on your auth system)
// if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
//     header('Location: ../login.php');
//     exit;
// }

// Handle theme selection
if (isset($_POST['select_theme']) && isset($_POST['template_key'])) {
    // Use a more reliable sanitization method
    $template_key = isset($_POST['template_key']) ? trim(strip_tags($_POST['template_key'])) : '';
    
    // Validate against allowed template keys for security
    $allowed_keys = ['default', 'subtle', 'bold', 'casual', 'high_contrast', 'template_1', 'template_2', 'template_3'];
    
    if (in_array($template_key, $allowed_keys) && setActiveBrandingTemplate($template_key)) {
        $success_message = "Theme '{$template_key}' has been activated successfully!";
    } else {
        $error_message = "Failed to activate theme '{$template_key}'. Please try again.";
    }
}

// Get current active template and all available templates
$active_template = getActiveBrandingTemplate();
$all_templates = getAllBrandingTemplates();

// Map template keys to their CSS files for preview
$template_css_mapping = [
    'default' => 'public-branding.css',
    'subtle' => 'public-branding-subtle.css',
    'bold' => 'public-branding-bold.css',
    'casual' => 'public-branding-casual.css',
    'high_contrast' => 'public-branding-high-contrast.css',
    'template_1' => 'public-branding.css',
    'template_2' => 'public-branding-subtle.css',
    'template_3' => 'public-branding-bold.css'
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Theme Selection - Admin Panel</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Dynamic Brand CSS -->
    <link href="../assets/css/brand-dynamic.css" rel="stylesheet">
    
    <?php 
    // Output dynamic brand CSS variables
    outputBrandCSS(); 
    ?>
    
    <style>
        .theme-preview-card {
            height: 300px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        
        .theme-preview-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .theme-preview-card.active {
            border-color: var(--brand-primary);
            box-shadow: 0 4px 20px rgba(var(--brand-primary-rgb, 108, 46, 182), 0.3);
        }
        
        .theme-preview-frame {
            width: 100%;
            height: 200px;
            border: none;
            border-radius: 8px;
            transform: scale(0.8);
            transform-origin: top left;
            pointer-events: none;
        }
        
        .theme-actions {
            position: absolute;
            bottom: 15px;
            left: 15px;
            right: 15px;
        }
        
        .current-theme-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }
        
        .admin-header {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-secondary) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .preview-container {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        
        .color-palette {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }
        
        .color-swatch {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .theme-description {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0"><i class="fas fa-palette me-3"></i>Brand Theme Selection</h1>
                    <p class="mb-0 mt-2 opacity-75">Choose the perfect branding theme for your website</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="../admin/" class="btn btn-light btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Back to Admin
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Status Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Current Active Theme Info -->
        <?php if ($active_template): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-star me-2"></i>Currently Active Theme</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6><?= htmlspecialchars($active_template['template_name']) ?></h6>
                                    <p class="text-muted mb-0"><?= htmlspecialchars($active_template['template_description']) ?></p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-check me-1"></i>Active
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Theme Selection Grid -->
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">Available Themes</h3>
            </div>
            
            <?php
            // Define theme details for better presentation
            $theme_details = [
                'default' => [
                    'name' => 'Classic Professional',
                    'description' => 'Clean, traditional layout perfect for professional businesses',
                    'colors' => ['var(--brand-primary)', 'var(--brand-secondary)', '#ffffff']
                ],
                'subtle' => [
                    'name' => 'Subtle Elegance',
                    'description' => 'Minimal, understated design for sophisticated brands',
                    'colors' => ['rgba(var(--brand-primary-rgb), 0.7)', '#ffffff', '#f8f9fa']
                ],
                'bold' => [
                    'name' => 'Bold Impact',
                    'description' => 'Strong, vibrant design for maximum visual impact',
                    'colors' => ['var(--brand-primary)', 'var(--brand-secondary)', '#fff']
                ],
                'casual' => [
                    'name' => 'Friendly Casual',
                    'description' => 'Approachable, relaxed design for friendly businesses',
                    'colors' => ['var(--brand-primary)', 'var(--brand-secondary)', '#f0f8ff']
                ],
                'high_contrast' => [
                    'name' => 'High Contrast',
                    'description' => 'Accessible design with strong contrast ratios',
                    'colors' => ['#000000', 'var(--brand-primary)', '#ffffff']
                ]
            ];
            
            // Create theme cards for available CSS files
            $available_themes = ['default', 'subtle', 'bold', 'casual', 'high_contrast'];
            
            foreach ($available_themes as $theme_key):
                $theme_info = $theme_details[$theme_key];
                $css_file = $template_css_mapping[$theme_key] ?? 'public-branding.css';
                $is_active = $active_template && 
                    (($active_template['template_key'] === $theme_key) || 
                     (isset($active_template['config']['css_file']) && $active_template['config']['css_file'] === $css_file));
            ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card theme-preview-card <?= $is_active ? 'active' : '' ?>">
                        <?php if ($is_active): ?>
                            <div class="current-theme-badge">
                                <span class="badge bg-primary">
                                    <i class="fas fa-check me-1"></i>Active
                                </span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="preview-container">
                            <!-- Theme Preview Mockup -->
                            <div style="background: <?= $theme_info['colors'][0] ?>; height: 40px; border-radius: 8px 8px 0 0; position: relative;">
                                <div style="position: absolute; top: 10px; left: 15px; color: white; font-weight: bold; font-size: 12px;">
                                    Your Brand
                                </div>
                                <div style="position: absolute; top: 10px; right: 15px; color: white; font-size: 10px;">
                                    ≡ Menu
                                </div>
                            </div>
                            <div style="background: <?= $theme_info['colors'][2] ?>; height: 80px; padding: 15px; border-radius: 0 0 8px 8px;">
                                <div style="background: <?= $theme_info['colors'][1] ?>; height: 20px; border-radius: 4px; margin-bottom: 8px; opacity: 0.8;"></div>
                                <div style="background: #e9ecef; height: 12px; border-radius: 2px; margin-bottom: 4px; width: 80%;"></div>
                                <div style="background: #e9ecef; height: 12px; border-radius: 2px; width: 60%;"></div>
                            </div>
                            
                            <!-- Color Palette -->
                            <div class="color-palette">
                                <?php foreach ($theme_info['colors'] as $color): ?>
                                    <div class="color-swatch" style="background-color: <?= $color ?>;"></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="card-body pb-5">
                            <h6 class="card-title"><?= htmlspecialchars($theme_info['name']) ?></h6>
                            <p class="theme-description"><?= htmlspecialchars($theme_info['description']) ?></p>
                        </div>
                        
                        <div class="theme-actions">
                            <?php if (!$is_active): ?>
                                <form method="POST" class="d-inline-block w-100">
                                    <input type="hidden" name="template_key" value="<?= htmlspecialchars($theme_key) ?>">
                                    <button type="submit" name="select_theme" class="btn btn-primary w-100">
                                        <i class="fas fa-check me-2"></i>Select Theme
                                    </button>
                                </form>
                            <?php else: ?>
                                <button type="button" class="btn btn-success w-100" disabled>
                                    <i class="fas fa-star me-2"></i>Currently Active
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Preview Instructions -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Theme Preview Instructions</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>Select a Theme:</strong> Click "Select Theme" to activate any theme instantly</li>
                            <li><strong>Preview Changes:</strong> Visit your public website to see the theme in action</li>
                            <li><strong>Professional Appearance:</strong> All themes are designed to be attractive and professional for your clients</li>
                            <li><strong>Dynamic Colors:</strong> Themes automatically use your brand colors from the database settings</li>
                            <li><strong>Responsive Design:</strong> All themes work perfectly on desktop, tablet, and mobile devices</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-dismiss success messages
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-success');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Theme card hover effects
        document.querySelectorAll('.theme-preview-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                if (!this.classList.contains('active')) {
                    this.style.borderColor = 'var(--brand-primary)';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                if (!this.classList.contains('active')) {
                    this.style.borderColor = 'transparent';
                }
            });
        });
    </script>
</body>
</html>
