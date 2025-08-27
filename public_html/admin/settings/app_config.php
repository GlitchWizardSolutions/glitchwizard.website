<?php
/**
 * Application Configuration Manager
 * Handles configuration settings for individual applications (shop, blog, etc.)
 */

// Include admin main file
include_once '../assets/includes/main.php';

// Security check for admin/developer access (configs are sensitive)
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['Admin', 'Developer']))
{
    header('Location: ../index.php');
    exit();
}

// Get application parameter
$app_name = $_GET['app'] ?? '';

if (empty($app_name)) {
    header('Location: settings_dash.php');
    exit;
}

// Load application configuration mapping
$app_mapping = require_once 'app_config_mapping.php';

// Get application configuration
if (!isset($app_mapping[$app_name])) {
    $_SESSION['error_message'] = "Configuration not found for application: " . htmlspecialchars($app_name);
    header('Location: settings_dash.php');
    exit;
}

$app_config = $app_mapping[$app_name];
$display_name = $app_config['display_name'];

// Function to parse current config file values
function parseConfigFile($file_path) {
    if (!file_exists($file_path)) {
        return [];
    }
    
    $content = file_get_contents($file_path);
    $values = [];
    
    // Parse define() statements
    if (preg_match_all("/define\s*\(\s*['\"]([^'\"]+)['\"]\s*,\s*([^)]+)\s*\)/", $content, $matches)) {
        for ($i = 0; $i < count($matches[1]); $i++) {
            $key = $matches[1][$i];
            $value = $matches[2][$i];
            
            // Clean up the value
            $value = trim($value);
            
            // Remove quotes for strings
            if ((substr($value, 0, 1) === "'" && substr($value, -1) === "'") ||
                (substr($value, 0, 1) === '"' && substr($value, -1) === '"')) {
                $value = substr($value, 1, -1);
            }
            
            // Convert boolean strings
            if ($value === 'true') $value = true;
            if ($value === 'false') $value = false;
            
            $values[$key] = $value;
        }
    }
    
    return $values;
}

// Function to update config file
function updateConfigFile($file_path, $new_values, $app_config) {
    if (!file_exists($file_path)) {
        throw new Exception("Config file not found: $file_path");
    }
    
    // Backup original file
    $backup_path = $app_config['backup_file'];
    if (!copy($file_path, $backup_path)) {
        throw new Exception("Could not create backup file");
    }
    
    $content = file_get_contents($file_path);
    
    // Update each define statement
    foreach ($new_values as $key => $value) {
        // Escape the value properly
        if (is_bool($value)) {
            $escaped_value = $value ? 'true' : 'false';
        } elseif (is_numeric($value)) {
            $escaped_value = $value;
        } else {
            $escaped_value = "'" . str_replace("'", "\\'", $value) . "'";
        }
        
        // Replace the define statement
        $pattern = "/define\s*\(\s*['\"]" . preg_quote($key) . "['\"]\s*,\s*[^)]+\s*\)/";
        $replacement = "define('$key',$escaped_value)";
        
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    if (file_put_contents($file_path, $content) === false) {
        throw new Exception("Could not write to config file");
    }
    
    return true;
}

// Load current configuration values
$current_values = parseConfigFile($app_config['config_file']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_config') {
    try {
        $new_values = [];
        
        // Collect all configuration values from POST
        foreach ($app_config['sections'] as $section_key => $section) {
            foreach ($section['settings'] as $setting_key => $setting) {
                $post_key = 'config_' . $setting_key;
                
                if (isset($_POST[$post_key])) {
                    $value = $_POST[$post_key];
                    
                    // Convert value based on type
                    switch ($setting['type']) {
                        case 'boolean':
                            $value = ($value === '1' || $value === 'on');
                            break;
                        case 'number':
                            $value = (int)$value;
                            break;
                        case 'password':
                            // Only update password if not empty
                            if (empty($value)) {
                                continue 2; // Skip this setting
                            }
                            break;
                        default:
                            $value = trim($value);
                            break;
                    }
                    
                    $new_values[$setting_key] = $value;
                }
            }
        }
        
        if (!empty($new_values)) {
            updateConfigFile($app_config['config_file'], $new_values, $app_config);
            $_SESSION['success_message'] = "Configuration updated successfully!";
        } else {
            $_SESSION['error_message'] = "No configuration values to update.";
        }
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error updating configuration: " . $e->getMessage();
    }
    
    // Redirect to prevent re-submission
    header('Location: app_config.php?app=' . urlencode($app_name));
    exit;
}

?>
<?php echo template_admin_header('App Configuration - ' . $display_name, 'settings', 'app'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-gear" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo htmlspecialchars($display_name); ?> Configuration
                </h1>
                <div class="d-flex gap-2">
                    <a href="settings_dash.php" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Back to Dashboard
                    </a>
                    <?php if (file_exists($app_config['backup_file'])): ?>
                    <a href="?app=<?php echo urlencode($app_name); ?>&action=restore" class="btn btn-warning btn-sm" 
                       onclick="return confirm('Restore from backup? This will overwrite current settings.')">
                        <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>&nbsp;&nbsp;Restore Backup
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $_SESSION['success_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $_SESSION['error_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <!-- Description -->
            <div class="alert alert-info">
                <h6><i class="bi bi-info-circle" aria-hidden="true"></i> About This Configuration</h6>
                <p class="mb-0"><?php echo htmlspecialchars($app_config['description']); ?></p>
                <small class="text-muted">Config file: <?php echo htmlspecialchars($app_config['config_file']); ?></small>
            </div>

            <!-- Configuration Form -->
            <form method="POST" id="configForm">
                <input type="hidden" name="action" value="save_config">
                
                <?php foreach ($app_config['sections'] as $section_key => $section): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="<?php echo $section['icon']; ?>" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo $section['title']; ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($section['settings'] as $setting_key => $setting): ?>
                            <div class="col-lg-6 mb-3">
                                <div class="form-group">
                                    <label for="config_<?php echo $setting_key; ?>" class="form-label">
                                        <?php echo $setting['label']; ?>
                                        <?php if ($setting['type'] === 'password'): ?>
                                        <span class="badge badge-warning">Sensitive</span>
                                        <?php endif; ?>
                                    </label>
                                    
                                    <?php 
                                    $current_value = $current_values[$setting_key] ?? $setting['default'];
                                    $field_id = 'config_' . $setting_key;
                                    ?>
                                    
                                    <?php if ($setting['type'] === 'select'): ?>
                                        <select class="form-control" id="<?php echo $field_id; ?>" name="<?php echo $field_id; ?>">
                                            <?php foreach ($setting['options'] as $option_value => $option_label): ?>
                                            <option value="<?php echo htmlspecialchars($option_value); ?>" 
                                                    <?php echo ($current_value == $option_value) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($option_label); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php elseif ($setting['type'] === 'boolean'): ?>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="<?php echo $field_id; ?>" 
                                                   name="<?php echo $field_id; ?>" value="1" 
                                                   <?php echo $current_value ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo $field_id; ?>">
                                                <?php echo $current_value ? 'Enabled' : 'Disabled'; ?>
                                            </label>
                                        </div>
                                    <?php elseif ($setting['type'] === 'password'): ?>
                                        <input type="password" class="form-control" id="<?php echo $field_id; ?>" 
                                               name="<?php echo $field_id; ?>" 
                                               placeholder="Leave blank to keep current value">
                                    <?php elseif ($setting['type'] === 'number'): ?>
                                        <input type="number" class="form-control" id="<?php echo $field_id; ?>" 
                                               name="<?php echo $field_id; ?>" value="<?php echo htmlspecialchars($current_value); ?>">
                                    <?php elseif ($setting['type'] === 'email'): ?>
                                        <input type="email" class="form-control" id="<?php echo $field_id; ?>" 
                                               name="<?php echo $field_id; ?>" value="<?php echo htmlspecialchars($current_value); ?>">
                                    <?php else: // text ?>
                                        <input type="text" class="form-control" id="<?php echo $field_id; ?>" 
                                               name="<?php echo $field_id; ?>" value="<?php echo htmlspecialchars($current_value); ?>">
                                    <?php endif; ?>
                                    
                                    <small class="form-text text-muted"><?php echo $setting['description']; ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Submit Button -->
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save" aria-hidden="true"></i>&nbsp;&nbsp;Save Configuration
                                </button>
                                <button type="button" class="btn btn-outline-secondary ml-2" onclick="resetForm()">
                                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>&nbsp;&nbsp;Reset
                                </button>
                            </div>
                            <div>
                                <small class="text-muted">
                                    <i class="bi bi-shield-check" aria-hidden="true"></i> A backup will be created automatically
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    if (confirm('Reset all changes? This will restore all fields to their current saved values.')) {
        location.reload();
    }
}

// Toggle boolean field labels
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const label = this.parentNode.querySelector('.form-check-label');
            if (label) {
                label.textContent = this.checked ? 'Enabled' : 'Disabled';
            }
        });
    });
});
</script>

<?php echo template_admin_footer(); ?>
