<?php
/*
 * Brand Colors and Fonts Management Interface
 * 
 * This comprehensive admin interface allows management of all brand colors and fonts
 * stored in the setting_branding_colors table. Changes are immediately reflected
 * across the entire website through the dynamic CSS system.
 */

// Include the universal config and authentication
require_once '../../../private/gws-universal-config.php';

// Basic authentication check - modify as needed for your admin system
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
//     header('Location: login.php');
//     exit;
// }

// Process form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Prepare update statement for all brand colors and fonts
        $sql = "UPDATE setting_branding_colors SET 
                    brand_primary_color = :primary,
                    brand_secondary_color = :secondary,
                    brand_tertiary_color = :tertiary,
                    brand_quaternary_color = :quaternary,
                    brand_accent_color = :accent,
                    brand_warning_color = :warning,
                    brand_danger_color = :danger,
                    brand_info_color = :info,
                    brand_success_color = :success,
                    brand_error_color = :error,
                    brand_background_color = :background,
                    brand_text_color = :text,
                    brand_text_light = :text_light,
                    brand_text_muted = :text_muted,
                    custom_color_1 = :custom_1,
                    custom_color_2 = :custom_2,
                    custom_color_3 = :custom_3,
                    brand_font_primary = :font_primary,
                    brand_font_secondary = :font_secondary,
                    brand_font_heading = :font_heading,
                    brand_font_body = :font_body,
                    brand_font_monospace = :font_monospace,
                    last_updated = NOW()
                WHERE id = 1";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            ':primary' => $_POST['brand_primary_color'],
            ':secondary' => $_POST['brand_secondary_color'],
            ':tertiary' => $_POST['brand_tertiary_color'],
            ':quaternary' => $_POST['brand_quaternary_color'],
            ':accent' => $_POST['brand_accent_color'],
            ':warning' => $_POST['brand_warning_color'],
            ':danger' => $_POST['brand_danger_color'],
            ':info' => $_POST['brand_info_color'],
            ':success' => $_POST['brand_success_color'],
            ':error' => $_POST['brand_error_color'],
            ':background' => $_POST['brand_background_color'],
            ':text' => $_POST['brand_text_color'],
            ':text_light' => $_POST['brand_text_light'],
            ':text_muted' => $_POST['brand_text_muted'],
            ':custom_1' => $_POST['custom_color_1'],
            ':custom_2' => $_POST['custom_color_2'],
            ':custom_3' => $_POST['custom_color_3'],
            ':font_primary' => $_POST['brand_font_primary'],
            ':font_secondary' => $_POST['brand_font_secondary'],
            ':font_heading' => $_POST['brand_font_heading'],
            ':font_body' => $_POST['brand_font_body'],
            ':font_monospace' => $_POST['brand_font_monospace']
        ]);
        
        if ($result) {
            $message = 'Brand colors and fonts updated successfully! Changes are now live across the website.';
            $message_type = 'success';
        } else {
            $message = 'Error updating brand settings.';
            $message_type = 'danger';
        }
    } catch (PDOException $e) {
        $message = 'Database error: ' . $e->getMessage();
        $message_type = 'danger';
    }
}

// Load current brand settings
try {
    $stmt = $pdo->query("SELECT * FROM setting_branding_colors WHERE id = 1 LIMIT 1");
    $brand_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$brand_data) {
        // Insert default values if no record exists
        $default_sql = "INSERT INTO setting_branding_colors (
            id, brand_primary_color, brand_secondary_color, brand_tertiary_color, 
            brand_quaternary_color, brand_accent_color, brand_warning_color, 
            brand_danger_color, brand_info_color, brand_success_color, brand_error_color,
            brand_background_color, brand_text_color, brand_text_light, brand_text_muted,
            custom_color_1, custom_color_2, custom_color_3,
            brand_font_primary, brand_font_secondary, brand_font_heading, 
            brand_font_body, brand_font_monospace, last_updated
        ) VALUES (
            1, '#6c2eb6', '#bf5512', '#8B4513', '#2E8B57', '#28a745', 
            '#ffc107', '#dc3545', '#17a2b8', '#28a745', '#dc3545',
            '#ffffff', '#333333', '#666666', '#999999',
            '#cccccc', '#dddddd', '#eeeeee',
            'Inter, system-ui, sans-serif', 'Roboto, Arial, sans-serif', 
            'Inter, system-ui, sans-serif', 'Roboto, Arial, sans-serif', 
            'SF Mono, Monaco, Consolas, monospace', NOW()
        )";
        $pdo->exec($default_sql);
        
        // Reload data
        $stmt = $pdo->query("SELECT * FROM setting_branding_colors WHERE id = 1 LIMIT 1");
        $brand_data = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}

// Include the brand loader to get the CSS variables for preview
require_once '../../assets/includes/brand_loader.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Colors & Fonts Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .color-preview {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            border: 2px solid #ddd;
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
        }
        .font-preview {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 5px;
            background-color: #f8f9fa;
        }
        .brand-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .color-group {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .live-preview {
            position: sticky;
            top: 20px;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .preview-element {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">
                        <i class="bi bi-palette-fill me-2"></i>
                        Brand Colors & Fonts Management
                    </h1>
                    <div class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Changes apply immediately across the website
                    </div>
                </div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
                        <i class="bi bi-<?= $message_type === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" class="needs-validation" novalidate>
            <div class="row">
                <!-- Left Column: Form Controls -->
                <div class="col-lg-8">
                    <!-- Primary Brand Colors -->
                    <div class="brand-section">
                        <h4 class="mb-3">
                            <i class="bi bi-star-fill me-2 text-primary"></i>
                            Primary Brand Colors
                        </h4>
                        
                        <div class="color-group">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand_primary_color" class="form-label">Primary Color</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['brand_primary_color']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="brand_primary_color" name="brand_primary_color" 
                                               value="<?= htmlspecialchars($brand_data['brand_primary_color']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['brand_primary_color']) ?>" 
                                               placeholder="#6c2eb6">
                                    </div>
                                    <div class="form-text">Main brand color used for buttons, links, and primary elements</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="brand_secondary_color" class="form-label">Secondary Color</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['brand_secondary_color']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="brand_secondary_color" name="brand_secondary_color" 
                                               value="<?= htmlspecialchars($brand_data['brand_secondary_color']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['brand_secondary_color']) ?>" 
                                               placeholder="#bf5512">
                                    </div>
                                    <div class="form-text">Secondary brand color for headings and accents</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand_tertiary_color" class="form-label">Tertiary Color</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['brand_tertiary_color']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="brand_tertiary_color" name="brand_tertiary_color" 
                                               value="<?= htmlspecialchars($brand_data['brand_tertiary_color']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['brand_tertiary_color']) ?>" 
                                               placeholder="#8B4513">
                                    </div>
                                    <div class="form-text">Third brand color for additional variety</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="brand_quaternary_color" class="form-label">Quaternary Color</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['brand_quaternary_color']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="brand_quaternary_color" name="brand_quaternary_color" 
                                               value="<?= htmlspecialchars($brand_data['brand_quaternary_color']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['brand_quaternary_color']) ?>" 
                                               placeholder="#2E8B57">
                                    </div>
                                    <div class="form-text">Fourth brand color for extended palette</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Functional Colors -->
                    <div class="brand-section">
                        <h4 class="mb-3">
                            <i class="bi bi-gear-fill me-2 text-info"></i>
                            Functional Colors
                        </h4>
                        
                        <div class="color-group">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand_success_color" class="form-label">Success Color</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['brand_success_color']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="brand_success_color" name="brand_success_color" 
                                               value="<?= htmlspecialchars($brand_data['brand_success_color']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['brand_success_color']) ?>" 
                                               placeholder="#28a745">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="brand_danger_color" class="form-label">Danger Color</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['brand_danger_color']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="brand_danger_color" name="brand_danger_color" 
                                               value="<?= htmlspecialchars($brand_data['brand_danger_color']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['brand_danger_color']) ?>" 
                                               placeholder="#dc3545">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand_warning_color" class="form-label">Warning Color</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['brand_warning_color']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="brand_warning_color" name="brand_warning_color" 
                                               value="<?= htmlspecialchars($brand_data['brand_warning_color']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['brand_warning_color']) ?>" 
                                               placeholder="#ffc107">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="brand_info_color" class="form-label">Info Color</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['brand_info_color']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="brand_info_color" name="brand_info_color" 
                                               value="<?= htmlspecialchars($brand_data['brand_info_color']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['brand_info_color']) ?>" 
                                               placeholder="#17a2b8">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Text & Background Colors -->
                    <div class="brand-section">
                        <h4 class="mb-3">
                            <i class="bi bi-fonts me-2 text-secondary"></i>
                            Text & Background Colors
                        </h4>
                        
                        <div class="color-group">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand_background_color" class="form-label">Background Color</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['brand_background_color']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="brand_background_color" name="brand_background_color" 
                                               value="<?= htmlspecialchars($brand_data['brand_background_color']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['brand_background_color']) ?>" 
                                               placeholder="#ffffff">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="brand_text_color" class="form-label">Primary Text Color</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['brand_text_color']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="brand_text_color" name="brand_text_color" 
                                               value="<?= htmlspecialchars($brand_data['brand_text_color']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['brand_text_color']) ?>" 
                                               placeholder="#333333">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand_text_light" class="form-label">Light Text Color</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['brand_text_light']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="brand_text_light" name="brand_text_light" 
                                               value="<?= htmlspecialchars($brand_data['brand_text_light']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['brand_text_light']) ?>" 
                                               placeholder="#666666">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="brand_text_muted" class="form-label">Muted Text Color</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['brand_text_muted']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="brand_text_muted" name="brand_text_muted" 
                                               value="<?= htmlspecialchars($brand_data['brand_text_muted']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['brand_text_muted']) ?>" 
                                               placeholder="#999999">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Brand Fonts -->
                    <div class="brand-section">
                        <h4 class="mb-3">
                            <i class="bi bi-type me-2 text-warning"></i>
                            Brand Fonts
                        </h4>
                        
                        <div class="color-group">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand_font_primary" class="form-label">Primary Font</label>
                                    <input type="text" class="form-control" id="brand_font_primary" 
                                           name="brand_font_primary" 
                                           value="<?= htmlspecialchars($brand_data['brand_font_primary']) ?>" 
                                           placeholder="Inter, system-ui, sans-serif" required>
                                    <div class="font-preview" style="font-family: <?= htmlspecialchars($brand_data['brand_font_primary']) ?>">
                                        The quick brown fox jumps over the lazy dog. 123456789
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="brand_font_secondary" class="form-label">Secondary Font</label>
                                    <input type="text" class="form-control" id="brand_font_secondary" 
                                           name="brand_font_secondary" 
                                           value="<?= htmlspecialchars($brand_data['brand_font_secondary']) ?>" 
                                           placeholder="Roboto, Arial, sans-serif" required>
                                    <div class="font-preview" style="font-family: <?= htmlspecialchars($brand_data['brand_font_secondary']) ?>">
                                        The quick brown fox jumps over the lazy dog. 123456789
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand_font_heading" class="form-label">Heading Font</label>
                                    <input type="text" class="form-control" id="brand_font_heading" 
                                           name="brand_font_heading" 
                                           value="<?= htmlspecialchars($brand_data['brand_font_heading']) ?>" 
                                           placeholder="Inter, system-ui, sans-serif" required>
                                    <div class="font-preview" style="font-family: <?= htmlspecialchars($brand_data['brand_font_heading']) ?>; font-weight: bold; font-size: 1.2em;">
                                        Heading Text Example
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="brand_font_body" class="form-label">Body Font</label>
                                    <input type="text" class="form-control" id="brand_font_body" 
                                           name="brand_font_body" 
                                           value="<?= htmlspecialchars($brand_data['brand_font_body']) ?>" 
                                           placeholder="Roboto, Arial, sans-serif" required>
                                    <div class="font-preview" style="font-family: <?= htmlspecialchars($brand_data['brand_font_body']) ?>">
                                        This is body text that will be used throughout the website for paragraphs and content.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand_font_monospace" class="form-label">Monospace Font</label>
                                    <input type="text" class="form-control" id="brand_font_monospace" 
                                           name="brand_font_monospace" 
                                           value="<?= htmlspecialchars($brand_data['brand_font_monospace']) ?>" 
                                           placeholder="SF Mono, Monaco, Consolas, monospace" required>
                                    <div class="font-preview" style="font-family: <?= htmlspecialchars($brand_data['brand_font_monospace']) ?>">
                                        Code example: function updateBrandColors() { return true; }
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Custom Colors -->
                    <div class="brand-section">
                        <h4 class="mb-3">
                            <i class="bi bi-palette me-2 text-success"></i>
                            Custom Colors
                        </h4>
                        
                        <div class="color-group">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="custom_color_1" class="form-label">Custom Color 1</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['custom_color_1']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="custom_color_1" name="custom_color_1" 
                                               value="<?= htmlspecialchars($brand_data['custom_color_1']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['custom_color_1']) ?>" 
                                               placeholder="#cccccc">
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="custom_color_2" class="form-label">Custom Color 2</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['custom_color_2']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="custom_color_2" name="custom_color_2" 
                                               value="<?= htmlspecialchars($brand_data['custom_color_2']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['custom_color_2']) ?>" 
                                               placeholder="#dddddd">
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="custom_color_3" class="form-label">Custom Color 3</label>
                                    <div class="input-group">
                                        <span class="input-group-text p-1">
                                            <div class="color-preview" style="background-color: <?= htmlspecialchars($brand_data['custom_color_3']) ?>"></div>
                                        </span>
                                        <input type="color" class="form-control form-control-color" 
                                               id="custom_color_3" name="custom_color_3" 
                                               value="<?= htmlspecialchars($brand_data['custom_color_3']) ?>" required>
                                        <input type="text" class="form-control" 
                                               value="<?= htmlspecialchars($brand_data['custom_color_3']) ?>" 
                                               placeholder="#eeeeee">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save me-2"></i>
                            Save Brand Settings
                        </button>
                    </div>
                </div>

                <!-- Right Column: Live Preview -->
                <div class="col-lg-4">
                    <div class="live-preview">
                        <h5 class="mb-3">
                            <i class="bi bi-eye me-2"></i>
                            Live Preview
                        </h5>
                        
                        <div class="preview-element" style="background-color: var(--brand-primary, <?= $brand_data['brand_primary_color'] ?>); color: white; font-family: var(--brand-font-heading, <?= $brand_data['brand_font_heading'] ?>);">
                            Primary Color & Heading Font
                        </div>
                        
                        <div class="preview-element" style="background-color: var(--brand-secondary, <?= $brand_data['brand_secondary_color'] ?>); color: white; font-family: var(--brand-font-secondary, <?= $brand_data['brand_font_secondary'] ?>);">
                            Secondary Color & Secondary Font
                        </div>
                        
                        <div class="preview-element" style="background-color: var(--brand-background, <?= $brand_data['brand_background_color'] ?>); color: var(--brand-text, <?= $brand_data['brand_text_color'] ?>); border: 1px solid var(--brand-text-muted, <?= $brand_data['brand_text_muted'] ?>); font-family: var(--brand-font-body, <?= $brand_data['brand_font_body'] ?>);">
                            Body Text on Background
                        </div>
                        
                        <div class="row">
                            <div class="col-6">
                                <div class="btn btn-success w-100 mb-2" style="background-color: var(--brand-success, <?= $brand_data['brand_success_color'] ?>); border-color: var(--brand-success, <?= $brand_data['brand_success_color'] ?>);">Success</div>
                                <div class="btn btn-warning w-100" style="background-color: var(--brand-warning, <?= $brand_data['brand_warning_color'] ?>); border-color: var(--brand-warning, <?= $brand_data['brand_warning_color'] ?>);">Warning</div>
                            </div>
                            <div class="col-6">
                                <div class="btn btn-danger w-100 mb-2" style="background-color: var(--brand-danger, <?= $brand_data['brand_danger_color'] ?>); border-color: var(--brand-danger, <?= $brand_data['brand_danger_color'] ?>);">Danger</div>
                                <div class="btn btn-info w-100" style="background-color: var(--brand-info, <?= $brand_data['brand_info_color'] ?>); border-color: var(--brand-info, <?= $brand_data['brand_info_color'] ?>);">Info</div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-lightbulb me-1"></i>
                                Preview updates automatically when you change values above.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sync color inputs with text inputs
        document.querySelectorAll('input[type="color"]').forEach(colorInput => {
            const textInput = colorInput.nextElementSibling;
            
            colorInput.addEventListener('input', function() {
                textInput.value = this.value;
                updatePreview();
            });
            
            textInput.addEventListener('input', function() {
                if (this.value.match(/^#[0-9A-F]{6}$/i)) {
                    colorInput.value = this.value;
                    updatePreview();
                }
            });
        });

        // Update font previews when fonts change
        document.querySelectorAll('input[name*="font"]').forEach(fontInput => {
            fontInput.addEventListener('input', function() {
                const preview = this.parentElement.querySelector('.font-preview');
                if (preview) {
                    preview.style.fontFamily = this.value;
                }
                updatePreview();
            });
        });

        function updatePreview() {
            // Update CSS custom properties for live preview
            const root = document.documentElement;
            
            // Update color properties
            document.querySelectorAll('input[type="color"]').forEach(input => {
                const varName = '--' + input.name.replace(/_/g, '-');
                root.style.setProperty(varName, input.value);
            });
            
            // Update font properties
            document.querySelectorAll('input[name*="font"]').forEach(input => {
                const varName = '--' + input.name.replace(/_/g, '-');
                root.style.setProperty(varName, `'${input.value}'`);
            });
        }

        // Initialize preview on page load
        updatePreview();

        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                const forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>
