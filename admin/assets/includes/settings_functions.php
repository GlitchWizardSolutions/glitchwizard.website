<?php
/**
 * Settings Management Helper Functions
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: settings_functions.php
 * LOCATION: /public_html/admin/assets/includes/
 * PURPOSE: Shared functions for settings management across the admin panel
 * 
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 */

// Configuration Management Functions
function process_config_updates($contents, $post_data, $required_fields) {
    $updated = $contents;
    foreach ($post_data as $key => $value) {
        if (empty(trim($value)) && !in_array($key, $required_fields)) {
            continue;
        }

        $clean_value = trim($value);
        if (in_array(strtolower($clean_value), ['true', 'false'])) {
            $val = strtolower($clean_value);
        } elseif (is_numeric($clean_value)) {
            $val = $clean_value;
        } else {
            $val = "'" . str_replace("'", "\\'", $clean_value) . "'";
        }

        $pattern = '/define\(\'' . preg_quote($key, '/') . '\'\s*,\s*.*?\)/';
        $replacement = "define('" . $key . "', " . $val . ")";

        if (preg_match($pattern, $updated)) {
            $updated = preg_replace($pattern, $replacement, $updated);
        }
    }
    return $updated;
}

function validate_and_save_config($contents, $file_path, $backup_path) {
    // Basic PHP syntax validation
    if (strpos($contents, '<?php') === false) {
        return false;
    }

    // Save to temporary file for syntax check
    $temp_file = $file_path . '.test';
    file_put_contents($temp_file, $contents);
    
    // Check PHP syntax
    $syntax_check = shell_exec('php -l ' . escapeshellarg($temp_file) . ' 2>&1');
    unlink($temp_file);

    if (strpos($syntax_check, 'No syntax errors') === false) {
        copy($backup_path, $file_path); // Restore backup
        return false;
    }

    // Save the validated configuration
    return file_put_contents($file_path, $contents, LOCK_EX) !== false;
}

function cleanup_old_backups($backup_dir, $keep_count) {
    $pattern = $backup_dir . '/gws-universal-config.php.backup.*';
    $backups = glob($pattern);
    if (count($backups) > $keep_count) {
        array_multisort(array_map('filemtime', $backups), SORT_DESC, $backups);
        foreach (array_slice($backups, $keep_count) as $old_backup) {
            unlink($old_backup);
        }
    }
}

// UI Helper Functions
function format_key($key) {
    $replacements = [
        '_' => ' ',
        'url' => 'URL',
        'db ' => 'Database ',
        ' pass' => ' Password',
        ' user' => ' Username',
        ' id' => ' ID',
        ' uri' => ' URI',
        'smtp' => 'SMTP',
        'api' => 'API',
        'ssl' => 'SSL',
        'tls' => 'TLS',
        'http' => 'HTTP',
        'https' => 'HTTPS'
    ];

    $key = str_replace(array_keys($replacements), array_values($replacements), strtolower($key));
    return ucwords($key);
}

function format_var_html($key, $value, $comment, $list = []) {
    $html = '<div class="form-group mb-3">';

    // Determine input type and clean value first
    $cleanValue = trim($value, '\'"');
    $isTextarea = strpos($value, '\n') !== false;
    $isPassword = strpos(strtolower($key), 'pass') !== false || strpos(strtolower($key), 'secret') !== false;
    $isBoolean = in_array(strtolower($cleanValue), ['true', 'false']);
    $isNumeric = is_numeric($cleanValue);
    $label = format_key($key);

    // Format label with tooltip
    $html .= '<label for="' . htmlspecialchars($key) . '" class="form-label fw-bold">' . htmlspecialchars($label);

    // Add info icon for complex fields
    if ($comment && substr($comment, 0, 2) === '//') {
        $commentText = htmlspecialchars(trim(ltrim($comment, '//')));
    $html .= ' <i class="bi bi-info-circle text-muted" aria-hidden="true" data-bs-toggle="tooltip" title="' . $commentText . '"></i>';
    }

    $html .= '</label>';

    // Add comment as help text
    if ($comment && substr($comment, 0, 2) === '//') {
        $html .= '<div class="form-text text-muted mb-2">' . htmlspecialchars(trim(ltrim($comment, '//'))) . '</div>';
    }

    // Generate appropriate input
    if ($list && !empty($list)) {
        // Dropdown/Select
        $html .= '<select name="' . htmlspecialchars($key) . '" id="' . htmlspecialchars($key) . '" class="form-select">';
        foreach ($list as $item) {
            $item = explode('=', trim($item));
            $optionValue = trim($item[0]);
            $optionLabel = isset($item[1]) ? trim($item[1]) : $optionValue;
            $selected = strtolower($optionValue) == strtolower($cleanValue) ? ' selected' : '';
            $html .= '<option value="' . htmlspecialchars($optionValue) . '"' . $selected . '>' . htmlspecialchars($optionLabel) . '</option>';
        }
        $html .= '</select>';
    } elseif ($isBoolean) {
        // Boolean checkbox with hidden field
        $checked = strtolower($cleanValue) == 'true' ? ' checked' : '';
        $html .= '<input type="hidden" name="' . htmlspecialchars($key) . '" value="false">';
        $html .= '<div class="form-check form-switch">';
        $html .= '<input type="checkbox" name="' . htmlspecialchars($key) . '" id="' . htmlspecialchars($key) . '" class="form-check-input" value="true"' . $checked . '>';
        $html .= '<label class="form-check-label" for="' . htmlspecialchars($key) . '">Enable ' . htmlspecialchars($label) . '</label>';
        $html .= '</div>';
    } elseif ($isTextarea) {
        // Textarea for multiline content
        $textareaValue = str_replace('\n', PHP_EOL, $cleanValue);
        $html .= '<textarea name="' . htmlspecialchars($key) . '" id="' . htmlspecialchars($key) . '" class="form-control" rows="4" placeholder="' . htmlspecialchars($label) . '">' . htmlspecialchars($textareaValue) . '</textarea>';
    } else {
        // Regular input field
        $inputType = $isPassword ? 'password' : 'text';
        $inputClass = 'form-control';

        // Add special styling for different types
        if ($isNumeric) {
            $inputClass .= ' text-end'; // Right-align numbers
        }

        $html .= '<input type="' . $inputType . '" name="' . htmlspecialchars($key) . '" id="' . htmlspecialchars($key) . '" class="' . $inputClass . '" value="' . htmlspecialchars($cleanValue) . '" placeholder="' . htmlspecialchars($label) . '">';

        // Add show/hide toggle for passwords
        if ($isPassword) {
            $html .= '<div class="input-group-append mt-1">';
            $html .= '<button type="button" class="btn btn-outline-secondary btn-sm" onclick="togglePassword(\'' . htmlspecialchars($key) . '\')">Show/Hide</button>';
            $html .= '</div>';
        }
    }

    $html .= '</div>';
    return $html;
}

function format_tabs($contents) {
    $rows = explode("\n", $contents);
    $tabs = [];

    // Define standard tabs
    $tabs = [
        ['name' => 'Environment', 'id' => 'tab-env', 'icon' => 'bi bi-hdd-network'],
        ['name' => 'Database', 'id' => 'tab-db', 'icon' => 'bi bi-database'],
        ['name' => 'URLs & Paths', 'id' => 'tab-urls', 'icon' => 'bi bi-link-45deg'],
        ['name' => 'Security', 'id' => 'tab-security', 'icon' => 'bi bi-shield-check']
    ];

    // Generate modern Bootstrap nav tabs
    $html = '<div class="card-header bg-light">';
    $html .= '<ul class="nav nav-tabs card-header-tabs" id="configTabs" role="tablist">';

    foreach ($tabs as $tab) {
        $active = $tab['id'] === 'tab-env' ? ' active' : '';
        $selected = $tab['id'] === 'tab-env' ? 'true' : 'false';

        $html .= '<li class="nav-item" role="presentation">';
        $html .= '<button class="nav-link' . $active . '" id="' . $tab['id'] . '-tab" data-bs-toggle="tab" data-bs-target="#' . $tab['id'] . '" type="button" role="tab" aria-controls="' . $tab['id'] . '" aria-selected="' . $selected . '">';
        $html .= '<i class="' . $tab['icon'] . ' me-2"></i>' . htmlspecialchars($tab['name']);
        $html .= '</button>';
        $html .= '</li>';
    }

    $html .= '</ul>';
    $html .= '</div>';
    return $html;
}

function format_form($contents) {
    $rows = explode("\n", $contents);
    $html = '<div class="card-body">';
    $html .= '<div class="tab-content" id="configTabContent">';

    // Initialize tabs
    $tabs = [
        'env' => ['active' => true, 'settings' => []],
        'db' => ['active' => false, 'settings' => []],
        'urls' => ['active' => false, 'settings' => []],
        'security' => ['active' => false, 'settings' => []]
    ];

    // Categorize settings
    for ($i = 0; $i < count($rows); $i++) {
        if (preg_match('/define\(\'(.*?)\',\s*(.*?)\)/', $rows[$i], $matches)) {
            $key = $matches[1];
            $value = $matches[2];
            $comment = ($i > 0) ? trim($rows[$i-1]) : '';
            
            // Categorize the setting
            if (strpos(strtolower($key), 'environment') !== false || $key === 'ENVIRONMENT') {
                $tabs['env']['settings'][] = ['key' => $key, 'value' => $value, 'comment' => $comment];
            } elseif (strpos(strtolower($key), 'db_') === 0) {
                $tabs['db']['settings'][] = ['key' => $key, 'value' => $value, 'comment' => $comment];
            } elseif (strpos(strtolower($key), '_url') !== false || strpos(strtolower($key), 'path') !== false) {
                $tabs['urls']['settings'][] = ['key' => $key, 'value' => $value, 'comment' => $comment];
            } elseif (strpos(strtolower($key), 'secret') !== false || strpos(strtolower($key), 'key') !== false || strpos(strtolower($key), 'token') !== false) {
                $tabs['security']['settings'][] = ['key' => $key, 'value' => $value, 'comment' => $comment];
            } else {
                $tabs['env']['settings'][] = ['key' => $key, 'value' => $value, 'comment' => $comment];
            }
        }
    }

    // Generate tab content
    foreach ($tabs as $tabKey => $tab) {
        $active = $tab['active'] ? ' show active' : '';
        $html .= '<div class="tab-pane fade' . $active . '" id="tab-' . $tabKey . '" role="tabpanel" aria-labelledby="tab-' . $tabKey . '-tab">';
        $html .= '<div class="row"><div class="col-md-8">';

        // Generate form fields for this tab's settings
        foreach ($tab['settings'] as $setting) {
            // Check for list options in comments
            $list = [];
            if (strpos($setting['comment'], '// List:') === 0) {
                $list = array_map('trim', explode(',', substr($setting['comment'], 8)));
                $setting['comment'] = ''; // Clear the list comment
            }
            
            $html .= format_var_html($setting['key'], $setting['value'], $setting['comment'], $list);
        }

        $html .= '</div></div></div>';
    }

    $html .= '</div>'; // Close tab-content
    $html .= '</div>'; // Close card-body
    return $html;
}
?>
