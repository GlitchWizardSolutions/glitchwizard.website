<?php
/**
 * Dynamic Page Settings Update Form
 * Handles settings updates for any page based on mapping configuration
 */

// Include admin main file (includes config, functions, templates)
include_once '../assets/includes/main.php';

// Security check for admin/editor/developer access
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['Admin', 'Editor', 'Developer']))
{
    header('Location: ../index.php');
    exit();
}

// Get page parameter
$page_path = $_GET['page'] ?? '';
$page_name = $_GET['name'] ?? '';

if (empty($page_path)) {
    header('Location: settings_dash.php');
    exit;
}

// Load page settings mapping
$page_mapping = require_once 'page_settings_mapping.php';

// Get page configuration - handle unmapped pages gracefully
$page_config = null;
$display_name = basename($page_path, '.php');
$is_unmapped_page = false;

if (!isset($page_mapping[$page_path])) {
    $is_unmapped_page = true;
    $display_name = ucwords(str_replace(['-', '_'], ' ', basename($page_path, '.php')));
} else {
    $page_config = $page_mapping[$page_path];
    $display_name = $page_config['display_name'] ?? basename($page_path, '.php');
}

// Function to load settings from PHP files
function loadPageSettings($page_config) {
    $settings = [];
    
    foreach ($page_config['settings_files'] as $file_key => $file_config) {
        $file_path = $file_config['path'];
        
        if (file_exists($file_path)) {
            if ($file_config['type'] === 'php_variable') {
                // Load single variable from PHP file
                include $file_path;
                $var_name = $file_config['variable'];
                if (isset($$var_name)) {
                    $settings[$var_name] = $$var_name;
                }
            } elseif ($file_config['type'] === 'php_variables') {
                // Load multiple variables from PHP file
                include $file_path;
                foreach ($file_config['variables'] as $var_name => $structure) {
                    if (isset($$var_name)) {
                        $settings[$var_name] = $$var_name;
                    }
                }
            }
        }
    }
    
    // Load image settings if configured
    if (isset($page_config['image_settings'])) {
        include_once '../../assets/includes/settings/image_helper.php';
        foreach ($page_config['image_settings'] as $image_key => $image_config) {
            $settings[$image_key . '_image'] = get_image_data($image_key) ?? [];
        }
    }
    
    return $settings;
}

// Function to update PHP variable in file
function updatePhpVariable($file_path, $var_name, $new_data) {
    $file_content = file_get_contents($file_path);
    $data_escaped = var_export($new_data, true);
    
    // Replace the variable assignment
    $pattern = '/\$' . preg_quote($var_name) . '\s*=\s*\[.*?\];/s';
    $replacement = '$' . $var_name . ' = ' . $data_escaped . ';';
    
    $updated_content = preg_replace($pattern, $replacement, $file_content);
    
    if ($updated_content === null || $updated_content === $file_content) {
        throw new Exception("Could not update variable $var_name in file");
    }
    
    if (file_put_contents($file_path, $updated_content) === false) {
        throw new Exception("Could not write to file: $file_path");
    }
}

// Function to save settings to PHP files
function savePageSettings($page_config, $post_data) {
    $success = true;
    $errors = [];
    
    foreach ($page_config['settings_files'] as $file_key => $file_config) {
        $file_path = $file_config['path'];
        
        try {
            if ($file_config['type'] === 'php_variable') {
                // Save single variable to PHP file
                $var_name = $file_config['variable'];
                $structure = $file_config['structure'];
                
                $new_data = [];
                foreach ($structure as $field_name => $field_config) {
                    $post_key = 'setting_' . $var_name . '_' . $field_name;
                    if (isset($post_data[$post_key])) {
                        $value = $post_data[$post_key];
                        // Don't stripslashes for HTML content (summernote)
                        if ($field_config['type'] !== 'summernote') {
                            $value = trim(stripslashes($value));
                        } else {
                            $value = trim($value);
                        }
                        $new_data[$field_name] = $value;
                    }
                }
                
                if (!empty($new_data)) {
                    updatePhpVariable($file_path, $var_name, $new_data);
                }
                
            } elseif ($file_config['type'] === 'php_variables') {
                // Save multiple variables to PHP file
                foreach ($file_config['variables'] as $var_name => $structure) {
                    $new_data = [];
                    foreach ($structure as $field_name => $field_config) {
                        $post_key = 'setting_' . $var_name . '_' . $field_name;
                        if (isset($post_data[$post_key])) {
                            $value = $post_data[$post_key];
                            // Don't stripslashes for HTML content (summernote)
                            if ($field_config['type'] !== 'summernote') {
                                $value = trim(stripslashes($value));
                            } else {
                                $value = trim($value);
                            }
                            $new_data[$field_name] = $value;
                        }
                    }
                    
                    if (!empty($new_data)) {
                        updatePhpVariable($file_path, $var_name, $new_data);
                    }
                }
            }
        } catch (Exception $e) {
            $success = false;
            $errors[] = "Error updating {$file_key}: " . $e->getMessage();
        }
    }
    
    // Save image settings if configured
    if (isset($page_config['image_settings'])) {
        include_once '../../assets/includes/settings/image_helper.php';
        foreach ($page_config['image_settings'] as $image_key => $image_config) {
            $image_data = [];
            foreach ($image_config['fields'] as $field_name => $field_config) {
                $post_key = 'setting_' . $image_key . '_image_' . $field_name;
                if (isset($post_data[$post_key])) {
                    $image_data[$field_name] = trim(stripslashes($post_data[$post_key]));
                }
            }
            
            if (!empty($image_data)) {
                try {
                    save_image_data($image_key, $image_data);
                } catch (Exception $e) {
                    $success = false;
                    $errors[] = "Error updating {$image_key} image: " . $e->getMessage();
                }
            }
        }
    }
    
    return ['success' => $success, 'errors' => $errors];
}

// Load current settings
$current_settings = [];
if (!$is_unmapped_page && $page_config) {
    $current_settings = loadPageSettings($page_config);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'save_page_settings' && !$is_unmapped_page) {
        $result = savePageSettings($page_config, $_POST);
        
        if ($result['success']) {
            $_SESSION['success_message'] = "Settings updated successfully!";
        } else {
            $_SESSION['error_message'] = "Error updating settings: " . implode(', ', $result['errors']);
        }
        
        // Redirect to prevent re-submission
        header('Location: page_update.php?page=' . urlencode($page_path) . '&name=' . urlencode($page_name));
        exit;
    } elseif ($_POST['action'] === 'create_page_mapping') {
        // Handle quick creation of page mapping
        try {
            $new_display_name = trim($_POST['display_name']);
            $new_settings_file = trim($_POST['settings_file']);
            $field_configs = json_decode($_POST['field_configs'], true);
            
            if (empty($new_display_name) || empty($new_settings_file)) {
                throw new Exception('Display name and settings file are required');
            }
            
            // Load current mapping
            $mapping_file = __DIR__ . '/page_settings_mapping.php';
            $current_mapping = require $mapping_file;
            
            // Create new page configuration
            $new_config = [
                'settings_files' => [
                    $new_settings_file => [
                        'path' => '../../assets/includes/settings/' . $new_settings_file,
                        'type' => 'php_variable',
                        'variable' => str_replace('.php', '', $new_settings_file) . '_content',
                        'structure' => $field_configs
                    ]
                ],
                'display_name' => $new_display_name
            ];
            
            // Add to mapping
            $current_mapping[$page_path] = $new_config;
            
            // Write back to file
            $mapping_content = "<?php\n/**\n * Page Settings Mapping\n * Maps each page to its corresponding settings files and structure\n */\n\nreturn " . var_export($current_mapping, true) . ";";
            
            if (file_put_contents($mapping_file, $mapping_content)) {
                $_SESSION['success_message'] = "Page mapping created successfully! You can now edit settings for this page.";
                header('Location: page_update.php?page=' . urlencode($page_path) . '&name=' . urlencode($page_name));
                exit;
            } else {
                throw new Exception('Could not write to mapping file');
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error creating page mapping: " . $e->getMessage();
        }
    }
}

// Recursive function to render nested settings
function renderSettings($data, $prefix = '', $level = 0, $field_configs = []) {
    $indent_class = $level > 0 ? 'ml-' . ($level * 3) : '';
    
    foreach ($data as $key => $value) {
        $field_name = $prefix ? $prefix . '_' . $key : $key;
        $field_id = 'setting_' . $field_name;
        $field_display = ucwords(str_replace('_', ' ', $key));
        
        // Get field configuration
        $field_config = $field_configs[$key] ?? ['type' => 'text', 'label' => $field_display];
        $field_display = $field_config['label'] ?? $field_display;
        
        if (is_array($value)) {
            // Nested section
            echo '<div class="' . $indent_class . ' mb-3">';
            echo '<h6 class="text-secondary"><i class="bi bi-chevron-right" aria-hidden="true"></i>&nbsp;&nbsp;' . $field_display . '</h6>';
            renderSettings($value, $field_name, $level + 1, $field_configs);
            echo '</div>';
        } else {
            // Individual setting
            echo '<div class="form-group ' . $indent_class . ' mb-3">';
            echo '<label for="' . $field_id . '" class="form-label">' . $field_display . '</label>';
            
            $field_type = $field_config['type'] ?? 'text';
            
            switch ($field_type) {
                case 'summernote':
                    echo '<textarea class="form-control summernote" id="' . $field_id . '" name="' . $field_id . '" rows="6">' . htmlspecialchars($value) . '</textarea>';
                    break;
                case 'textarea':
                    echo '<textarea class="form-control" id="' . $field_id . '" name="' . $field_id . '" rows="4">' . htmlspecialchars($value) . '</textarea>';
                    break;
                case 'number':
                    echo '<input type="number" class="form-control" id="' . $field_id . '" name="' . $field_id . '" value="' . htmlspecialchars($value) . '">';
                    break;
                case 'email':
                    echo '<input type="email" class="form-control" id="' . $field_id . '" name="' . $field_id . '" value="' . htmlspecialchars($value) . '">';
                    break;
                default: // text
                    echo '<input type="text" class="form-control" id="' . $field_id . '" name="' . $field_id . '" value="' . htmlspecialchars($value) . '">';
                    break;
            }
            echo '</div>';
        }
    }
}

?>
<?php echo template_admin_header('Page Settings - ' . $display_name, 'settings', 'pages'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="bi bi-pencil-square" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo htmlspecialchars($display_name); ?> Settings
                </h1>
                <div class="d-flex gap-2">
                    <a href="settings_dash.php" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Back to Dashboard
                    </a>
                    <a href="../../<?php echo htmlspecialchars($page_path); ?>" target="_blank" class="btn btn-info btn-sm">
                        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>&nbsp;&nbsp;View Page
                    </a>
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

            <!-- Page Settings Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-gear" aria-hidden="true"></i>&nbsp;&nbsp;Configure <?php echo htmlspecialchars($display_name); ?>
                    </h6>
                </div>
                <div class="card-body">
                    
                    <?php if ($is_unmapped_page): ?>
                    <!-- Setup form for unmapped pages -->
                    <div class="alert alert-info">
                        <h5><i class="bi bi-info-circle" aria-hidden="true"></i>&nbsp;&nbsp;Page Settings Setup Required</h5>
                        <p>This page isn't configured for settings management yet. Set it up now to start editing content!</p>
                    </div>
                    
                    <form method="POST" id="setupPageForm">
                        <input type="hidden" name="action" value="create_page_mapping">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="displayName" class="form-label">Display Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="displayName" name="display_name" 
                                           value="<?php echo htmlspecialchars($display_name); ?>" required>
                                    <small class="form-text text-muted">Human-readable name for this page</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="settingsFile" class="form-label">Settings File <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="settingsFile" name="settings_file" 
                                           value="<?php echo str_replace(['-', '.php'], ['_', '_settings.php'], $page_path); ?>" required>
                                    <small class="form-text text-muted">PHP file to store the settings</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Page Fields</label>
                            <div id="pageFields">
                                <div class="field-row border rounded p-3 mb-2">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control field-name" placeholder="title" value="title" required>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control field-type">
                                                <option value="text" selected>Text Input</option>
                                                <option value="textarea">Textarea</option>
                                                <option value="summernote">Rich Text (Summernote)</option>
                                                <option value="email">Email</option>
                                                <option value="number">Number</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control field-label" placeholder="Page Title" value="Page Title" required>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeField(this)" disabled>
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="field-row border rounded p-3 mb-2">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control field-name" placeholder="content" value="content" required>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control field-type">
                                                <option value="text">Text Input</option>
                                                <option value="textarea">Textarea</option>
                                                <option value="summernote" selected>Rich Text (Summernote)</option>
                                                <option value="email">Email</option>
                                                <option value="number">Number</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control field-label" placeholder="Page Content" value="Page Content" required>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeField(this)">
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addField()">
                                <i class="bi bi-plus-lg" aria-hidden="true"></i> Add Field
                            </button>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-lg" aria-hidden="true"></i>&nbsp;&nbsp;Create Page Settings
                            </button>
                            <a href="settings_dash.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Back to Dashboard
                            </a>
                        </div>
                    </form>
                    
                    <?php elseif (empty($current_settings)): ?>
                    <div class="alert alert-warning">
                        <h5><i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>&nbsp;&nbsp;No Settings Available</h5>
                        <p>This page doesn't have any configurable settings yet, or the settings files don't exist.</p>
                        <ul class="mb-0 mt-2">
                            <li>Settings files may need to be created for this page type</li>
                            <li>Check that the page mapping configuration is correct</li>
                            <li>Verify that settings files exist in the expected locations</li>
                        </ul>
                        <div class="mt-3">
                            <a href="settings_dash.php" class="btn btn-primary">
                                <i class="bi bi-gear" aria-hidden="true"></i>&nbsp;&nbsp;Back to Settings Dashboard
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    
                    <form method="POST" id="pageSettingsForm">
                        <input type="hidden" name="action" value="save_page_settings">
                        
                        <!-- Render settings sections -->
                        <?php 
                        foreach ($page_config['settings_files'] as $file_key => $file_config):
                            if ($file_config['type'] === 'php_variable'):
                                $var_name = $file_config['variable'];
                                if (isset($current_settings[$var_name])):
                        ?>
                        <div class="mb-4">
                            <h5 class="text-primary border-bottom pb-2">
                                <i class="bi bi-folder" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo ucwords(str_replace('_', ' ', $var_name)); ?>
                            </h5>
                            <?php 
                            renderSettings($current_settings[$var_name], $var_name, 0, $file_config['structure']);
                            ?>
                        </div>
                        <?php 
                                endif;
                            elseif ($file_config['type'] === 'php_variables'):
                                foreach ($file_config['variables'] as $var_name => $structure):
                                    if (isset($current_settings[$var_name])):
                        ?>
                        <div class="mb-4">
                            <h5 class="text-primary border-bottom pb-2">
                                <i class="bi bi-folder" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo ucwords(str_replace('_', ' ', $var_name)); ?>
                            </h5>
                            <?php 
                            renderSettings($current_settings[$var_name], $var_name, 0, $structure);
                            ?>
                        </div>
                        <?php 
                                    endif;
                                endforeach;
                            endif;
                        endforeach;
                        
                        // Render image settings
                        if (isset($page_config['image_settings'])):
                            foreach ($page_config['image_settings'] as $image_key => $image_config):
                                $image_data = $current_settings[$image_key . '_image'] ?? [];
                                if (!empty($image_data)):
                        ?>
                        <div class="mb-4">
                            <h5 class="text-primary border-bottom pb-2">
                                <i class="bi bi-image" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo ucwords(str_replace('_', ' ', $image_key)); ?> Image
                            </h5>
                            <?php 
                            renderSettings($image_data, $image_key . '_image', 0, $image_config['fields']);
                            ?>
                        </div>
                        <?php 
                                endif;
                            endforeach;
                        endif;
                        ?>
                        
                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save" aria-hidden="true"></i>&nbsp;&nbsp;Save Page Settings
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary ml-2" onclick="resetForm()">
                                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>&nbsp;&nbsp;Reset
                                        </button>
                                    </div>
                                    <div>
                                        <a href="../../<?php echo htmlspecialchars($page_path); ?>" target="_blank" class="btn btn-outline-info">
                                            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>&nbsp;&nbsp;Preview Changes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Summernote CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Summernote for rich text editing
    $('.summernote').summernote({
        height: 200,
        minHeight: null,
        maxHeight: null,
        focus: false,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
});

function resetForm() {
    if (confirm('Reset all changes? This will restore all fields to their saved values.')) {
        $('#pageSettingsForm')[0].reset();
        // Reinitialize Summernote content
        $('.summernote').each(function() {
            $(this).summernote('code', $(this).text());
        });
    }
}

// Functions for setup form (unmapped pages)
function addField() {
    const fieldsContainer = document.getElementById('pageFields');
    
    const fieldRow = document.createElement('div');
    fieldRow.className = 'field-row border rounded p-3 mb-2';
    fieldRow.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control field-name" placeholder="Field name" required>
            </div>
            <div class="col-md-3">
                <select class="form-control field-type">
                    <option value="text">Text Input</option>
                    <option value="textarea">Textarea</option>
                    <option value="summernote">Rich Text (Summernote)</option>
                    <option value="email">Email</option>
                    <option value="number">Number</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control field-label" placeholder="Display label" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeField(this)">
                    <i class="bi bi-trash" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    `;
    
    fieldsContainer.appendChild(fieldRow);
    updateRemoveButtons();
}

function removeField(button) {
    button.closest('.field-row').remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const fieldRows = document.querySelectorAll('.field-row');
    fieldRows.forEach((row, index) => {
        const removeBtn = row.querySelector('.btn-outline-danger');
        if (removeBtn) {
            removeBtn.disabled = fieldRows.length <= 1;
        }
    });
}

// Handle setup form submission
document.addEventListener('DOMContentLoaded', function() {
    const setupForm = document.getElementById('setupPageForm');
    if (setupForm) {
        setupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Collect field configurations
            const fieldConfigs = {};
            const fieldRows = document.querySelectorAll('.field-row');
            
            let isValid = true;
            fieldRows.forEach(row => {
                const nameInput = row.querySelector('.field-name');
                const typeSelect = row.querySelector('.field-type');
                const labelInput = row.querySelector('.field-label');
                
                if (!nameInput.value.trim() || !labelInput.value.trim()) {
                    isValid = false;
                    return;
                }
                
                fieldConfigs[nameInput.value.trim()] = {
                    type: typeSelect.value,
                    label: labelInput.value.trim()
                };
            });
            
            if (!isValid) {
                alert('Please fill in all field names and labels.');
                return;
            }
            
            // Add field configs to form
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'field_configs';
            hiddenInput.value = JSON.stringify(fieldConfigs);
            setupForm.appendChild(hiddenInput);
            
            // Submit the form
            setupForm.submit();
        });
        
        // Initialize remove button states
        updateRemoveButtons();
    }
});
</script>

<?php echo template_admin_footer(); ?>
