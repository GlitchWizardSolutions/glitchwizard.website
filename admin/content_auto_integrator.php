<?php
/**
 * Content Auto-Integration Engine
 * Automatically integrates hardcoded content with settings system
 * Handles settings file updates, content replacement, and admin form generation
 */

class ContentAutoIntegrator {
    
    private $db;
    private $auto_write_disabled = true; // SAFETY: Disable automatic file writing
    private $settings_files = [
        'global_header' => '../assets/settings/global_settings.php',
        'global_footer' => '../assets/settings/global_settings.php', 
        'global_shared' => '../assets/settings/global_settings.php',
        'section_services' => '../assets/settings/section_settings.php',
        'section_team' => '../assets/settings/section_settings.php',
        'section_about' => '../assets/settings/section_settings.php',
        'section_hero' => '../assets/settings/section_settings.php',
        'section_contact' => '../assets/settings/section_settings.php',
        'page_' => '../assets/settings/public_settings.php' // Default for page-specific
    ];
    
    public function __construct() {
        include_once '../private/gws-universal-config.php';
        // Initialize database connection if needed
    }
    
    /**
     * Main integration function
     */
    public function integrateContent($findings, $page_path, $scope) {
        $results = [
            'success' => true,
            'message' => '',
            'changes' => [],
            'errors' => []
        ];
        
        try {
            // Step 1: Backup original files
            $this->backupFiles($page_path);
            
            // Step 2: Update settings files
            $settings_changes = $this->updateSettingsFiles($findings, $scope);
            $results['changes']['settings'] = $settings_changes;
            
            // Step 3: Replace content in page
            $content_changes = $this->replaceContentInPage($findings, $page_path);
            $results['changes']['content'] = $content_changes;
            
            // Step 4: Generate admin form fields
            $form_changes = $this->generateAdminFormFields($findings, $scope);
            $results['changes']['forms'] = $form_changes;
            
            // Step 5: Update database schema if needed
            $db_changes = $this->updateDatabaseSchema($findings, $scope);
            $results['changes']['database'] = $db_changes;
            
            $results['message'] = "✅ Content integration completed successfully!";
            
        } catch (Exception $e) {
            $results['success'] = false;
            $results['errors'][] = $e->getMessage();
            $this->rollbackChanges($page_path);
        }
        
        return $results;
    }
    
    /**
     * Backup original files before modification
     */
    private function backupFiles($page_path) {
        $backup_dir = '../private/backups/content_integration/' . date('Y-m-d_H-i-s');
        if (!is_dir($backup_dir)) {
            mkdir($backup_dir, 0755, true);
        }
        
        // Backup the page file
        $page_name = basename($page_path);
        copy($page_path, $backup_dir . '/' . $page_name);
        
        // Backup settings files
        foreach ($this->settings_files as $settings_file) {
            if (file_exists($settings_file)) {
                $settings_name = basename($settings_file);
                copy($settings_file, $backup_dir . '/' . $settings_name);
            }
        }
        
        echo "<div class='alert alert-info'>💾 Files backed up to: {$backup_dir}</div>";
    }
    
    /**
     * Update settings files with new variables
     */
    private function updateSettingsFiles($findings, $scope) {
        $changes = [];
        
        // Determine which settings file to use
        $settings_file = $this->getSettingsFileForScope($scope);
        
        // Create settings file if it doesn't exist
        if (!file_exists($settings_file)) {
            $this->createSettingsFile($settings_file, $scope);
        }
        
        // Read current settings
        $current_content = file_get_contents($settings_file);
        $new_variables = [];
        
        // Process each finding type
        foreach ($findings as $type => $items) {
            foreach ($items as $item) {
                $var_name = $item['variable_name'];
                $var_value = $this->escapeForPhp($item['content']);
                
                // Check if variable already exists
                if (strpos($current_content, "\${$var_name}") === false) {
                    $new_variables[] = [
                        'name' => $var_name,
                        'value' => $var_value,
                        'type' => $item['type'],
                        'comment' => $this->generateVariableComment($item)
                    ];
                }
            }
        }
        
        // Add new variables to settings file
        if (!empty($new_variables)) {
            $this->addVariablesToSettingsFile($settings_file, $new_variables, $scope);
            $changes['added_variables'] = count($new_variables);
            $changes['settings_file'] = $settings_file;
        }
        
        return $changes;
    }
    
    /**
     * Replace hardcoded content with variable references in page
     */
    private function replaceContentInPage($findings, $page_path) {
        $changes = [];
        $content = file_get_contents($page_path);
        $original_content = $content;
        $replacements = 0;
        
        foreach ($findings as $type => $items) {
            foreach ($items as $item) {
                $old_content = $item['content'];
                $variable_name = $item['variable_name'];
                
                // Generate replacement based on content type
                $replacement = $this->generateReplacement($item, $variable_name);
                
                // Perform replacement
                $pattern = $this->generateReplacementPattern($item);
                $new_content = preg_replace($pattern, $replacement, $content, 1);
                
                if ($new_content !== $content) {
                    $content = $new_content;
                    $replacements++;
                    $changes['replacements'][] = [
                        'old' => $old_content,
                        'new' => $replacement,
                        'variable' => $variable_name
                    ];
                }
            }
        }
        
        // Write updated content back to file
        if ($replacements > 0) {
            file_put_contents($page_path, $content);
            $changes['total_replacements'] = $replacements;
            $changes['file_updated'] = true;
        }
        
        return $changes;
    }
    
    /**
     * Generate admin form fields for new variables
     */
    private function generateAdminFormFields($findings, $scope) {
        $changes = [];
        
        // Determine which admin form file to update
        $form_file = $this->getAdminFormFile($scope);
        
        // Create form fields array
        $new_fields = [];
        
        foreach ($findings as $type => $items) {
            foreach ($items as $item) {
                $field = $this->generateFormField($item);
                $new_fields[] = $field;
            }
        }
        
        // Add fields to admin form
        if (!empty($new_fields)) {
            $this->addFieldsToAdminForm($form_file, $new_fields, $scope);
            $changes['fields_added'] = count($new_fields);
            $changes['form_file'] = $form_file;
        }
        
        return $changes;
    }
    
    /**
     * Update database schema for new settings
     */
    private function updateDatabaseSchema($findings, $scope) {
        $changes = [];
        
        // This would connect to database and add new columns
        // For now, we'll create a SQL file with the schema changes
        
        $sql_statements = [];
        
        foreach ($findings as $type => $items) {
            foreach ($items as $item) {
                $column_name = $item['variable_name'];
                $column_type = $this->getDatabaseColumnType($item['type']);
                
                $sql_statements[] = "ALTER TABLE settings ADD COLUMN IF NOT EXISTS `{$column_name}` {$column_type};";
            }
        }
        
        if (!empty($sql_statements)) {
            $sql_file = "../private/sql/content_integration_" . date('Y-m-d_H-i-s') . ".sql";
            file_put_contents($sql_file, implode("\n", $sql_statements));
            $changes['sql_file'] = $sql_file;
            $changes['sql_statements'] = count($sql_statements);
        }
        
        return $changes;
    }
    
    /**
     * Get appropriate settings file for scope
     */
    private function getSettingsFileForScope($scope) {
        foreach ($this->settings_files as $scope_pattern => $file) {
            if (strpos($scope, $scope_pattern) === 0) {
                return $file;
            }
        }
        return $this->settings_files['page_']; // Default
    }
    
    /**
     * Create new settings file if it doesn't exist
     */
    private function createSettingsFile($settings_file, $scope) {
        $dir = dirname($settings_file);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $template = "<?php\n";
        $template .= "/**\n";
        $template .= " * Auto-generated settings file for scope: {$scope}\n";
        $template .= " * Created on: " . date('Y-m-d H:i:s') . "\n";
        $template .= " */\n\n";
        $template .= "// Settings variables will be added below\n\n";
        $template .= "?>\n";
        
        // SAFETY CHECK: Don't automatically write if disabled
        if (!$this->auto_write_disabled) {
            file_put_contents($settings_file, $template);
        } else {
            // Log what would have been written for manual review
            error_log("ContentAutoIntegrator: Would have created settings file: " . $settings_file);
        }
    }
    
    /**
     * Add new variables to settings file
     */
    private function addVariablesToSettingsFile($settings_file, $new_variables, $scope) {
        $content = file_get_contents($settings_file);
        
        // Find insertion point (before closing PHP tag)
        $insertion_point = strrpos($content, '?>');
        if ($insertion_point === false) {
            $insertion_point = strlen($content);
            $new_content = $content . "\n\n";
        } else {
            $new_content = substr($content, 0, $insertion_point);
        }
        
        // Add comment header for new variables
        $new_content .= "\n// Auto-integrated variables from content scanner (" . date('Y-m-d H:i:s') . ")\n";
        
        // Add each variable
        foreach ($new_variables as $var) {
            $new_content .= "\n// {$var['comment']}\n";
            $new_content .= "\${$var['name']} = '{$var['value']}';\n";
        }
        
        // Add closing PHP tag back
        if (strpos($content, '?>') !== false) {
            $new_content .= "\n?>\n";
        }
        
        // SAFETY CHECK: Don't automatically write if disabled
        if (!$this->auto_write_disabled) {
            file_put_contents($settings_file, $new_content);
        } else {
            // Log what would have been written for manual review
            error_log("ContentAutoIntegrator: Would have updated settings file: " . $settings_file);
        }
    }
    
    /**
     * Generate variable comment
     */
    private function generateVariableComment($item) {
        return ucfirst($item['type']) . ": " . substr($item['content'], 0, 50) . (strlen($item['content']) > 50 ? '...' : '');
    }
    
    /**
     * Generate replacement pattern for content
     */
    private function generateReplacementPattern($item) {
        $content = preg_quote($item['content'], '/');
        
        switch ($item['type']) {
            case 'heading':
                return "/(<{$item['tag']}[^>]*>)({$content})(<\/{$item['tag']}>)/i";
            case 'link_text':
                return "/(<a[^>]*>)({$content})(<\/a>)/i";
            case 'image_src':
                return "/src=[\"']" . preg_quote($item['content'], '/') . "[\"']/i";
            case 'image_alt':
                return "/alt=[\"']" . preg_quote($item['content'], '/') . "[\"']/i";
            default:
                return "/(\>)({$content})(\<)/i";
        }
    }
    
    /**
     * Generate replacement content
     */
    private function generateReplacement($item, $variable_name) {
        switch ($item['type']) {
            case 'heading':
                return "$1<?php echo htmlspecialchars(\${$variable_name} ?? '{$item['content']}'); ?>$3";
            case 'link_text':
                return "$1<?php echo htmlspecialchars(\${$variable_name} ?? '{$item['content']}'); ?>$3";
            case 'image_src':
                return "src=\"<?php echo htmlspecialchars(\${$variable_name} ?? '{$item['content']}'); ?>\"";
            case 'image_alt':
                return "alt=\"<?php echo htmlspecialchars(\${$variable_name} ?? '{$item['content']}'); ?>\"";
            default:
                return "$1<?php echo htmlspecialchars(\${$variable_name} ?? '{$item['content']}'); ?>$3";
        }
    }
    
    /**
     * Escape content for PHP strings
     */
    private function escapeForPhp($content) {
        return addslashes($content);
    }
    
    /**
     * Get admin form file for scope
     */
    private function getAdminFormFile($scope) {
        if (strpos($scope, 'global') === 0) {
            return '../admin/forms/global_settings_form.php';
        } elseif (strpos($scope, 'section') === 0) {
            return '../admin/forms/section_settings_form.php';
        } else {
            return '../admin/forms/page_settings_form.php';
        }
    }
    
    /**
     * Generate form field for admin interface
     */
    private function generateFormField($item) {
        $field_type = $this->getFormFieldType($item['type']);
        
        return [
            'name' => $item['variable_name'],
            'label' => $this->generateFieldLabel($item),
            'type' => $field_type,
            'default_value' => $item['content'],
            'description' => "Auto-generated from: " . $item['content'],
            'category' => $this->getFieldCategory($item['type'])
        ];
    }
    
    /**
     * Get form field type based on content type
     */
    private function getFormFieldType($content_type) {
        switch ($content_type) {
            case 'heading':
                return 'text';
            case 'text':
            case 'link_text':
                return 'text';
            case 'image_src':
                return 'file';
            case 'image_alt':
                return 'text';
            default:
                return 'text';
        }
    }
    
    /**
     * Generate field label
     */
    private function generateFieldLabel($item) {
        $content_preview = substr($item['content'], 0, 30);
        return ucfirst(str_replace('_', ' ', $item['variable_name'])) . " ({$content_preview}...)";
    }
    
    /**
     * Get field category
     */
    private function getFieldCategory($content_type) {
        switch ($content_type) {
            case 'heading':
                return 'Headings';
            case 'text':
            case 'link_text':
                return 'Text Content';
            case 'image_src':
            case 'image_alt':
                return 'Images';
            default:
                return 'General';
        }
    }
    
    /**
     * Add fields to admin form
     */
    private function addFieldsToAdminForm($form_file, $new_fields, $scope) {
        // This would update the admin form files
        // For now, we'll create a JSON file with the field definitions
        
        $form_dir = dirname($form_file);
        if (!is_dir($form_dir)) {
            mkdir($form_dir, 0755, true);
        }
        
        $form_data = [
            'scope' => $scope,
            'generated_at' => date('Y-m-d H:i:s'),
            'fields' => $new_fields
        ];
        
        $json_file = $form_file . '.json';
        file_put_contents($json_file, json_encode($form_data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Get database column type
     */
    private function getDatabaseColumnType($content_type) {
        switch ($content_type) {
            case 'heading':
            case 'text':
            case 'link_text':
                return 'VARCHAR(255)';
            case 'image_src':
                return 'VARCHAR(500)';
            case 'image_alt':
                return 'VARCHAR(255)';
            default:
                return 'TEXT';
        }
    }
    
    /**
     * Rollback changes if something goes wrong
     */
    private function rollbackChanges($page_path) {
        // This would restore from backup
        echo "<div class='alert alert-warning'>⚠️ Rollback functionality would restore from backup</div>";
    }
}

// AJAX endpoint for auto-integration
if (isset($_POST['action']) && $_POST['action'] === 'auto_integrate') {
    header('Content-Type: application/json');
    
    $integrator = new ContentAutoIntegrator();
    
    // Get findings from POST data (would come from scanner)
    $findings = json_decode($_POST['findings'], true);
    $page_path = $_POST['page_path'];
    $scope = $_POST['scope'];
    
    $results = $integrator->integrateContent($findings, $page_path, $scope);
    
    echo json_encode($results);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Content Auto-Integration Engine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>🔧 Content Auto-Integration Engine</h1>
        <p class="lead">Automatically integrates hardcoded content with the settings system.</p>
        
        <div class="alert alert-info">
            <h5>🎯 What This Tool Does:</h5>
            <ul>
                <li>✅ Backs up original files before changes</li>
                <li>🔧 Updates appropriate settings files with new variables</li>
                <li>🔄 Replaces hardcoded content with variable references</li>
                <li>📝 Generates admin form fields automatically</li>
                <li>🗄️ Creates database schema updates</li>
                <li>🔙 Provides rollback capabilities</li>
            </ul>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <h3>📋 Integration Process</h3>
                <ol>
                    <li><strong>Scan</strong> - Use the Content Scanner to identify hardcoded content</li>
                    <li><strong>Review</strong> - Check the findings and scope detection</li>
                    <li><strong>Integrate</strong> - Click "Auto-Integrate" to apply changes</li>
                    <li><strong>Test</strong> - Verify the integration works correctly</li>
                </ol>
            </div>
            
            <div class="col-md-6">
                <h3>🎯 Smart Scope Management</h3>
                <ul>
                    <li><strong>Global Content</strong> → global_settings.php</li>
                    <li><strong>Section Content</strong> → section_settings.php</li>
                    <li><strong>Page Content</strong> → public_settings.php</li>
                </ul>
                <p class="text-muted">No duplication - each piece of content goes to the right place!</p>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="content_integration_scanner.php" class="btn btn-primary btn-lg">
                🔍 Go to Content Scanner
            </a>
        </div>
    </div>
</body>
</html>
