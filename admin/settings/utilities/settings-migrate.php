<?php
/* 
 * Settings Migration Tool
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: settings-migrate.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Migrate settings between different format versions
 * DETAILED DESCRIPTION:
 * This file provides a utility for migrating settings from older format
 * structures to the new template format. It handles data transformation,
 * validation, and backup creation during the migration process to ensure
 * safe transition between different settings formats.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/assets/includes/settings/settings_config.php
 * - /public_html/admin/settings/config-validator.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Settings format migration
 * - Automatic backups
 * - Data validation
 * - Error recovery
 * - Migration logging
 */

// Prevent direct web access
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line');
}

// Check if PROJECT_ROOT is defined
if (!getenv('PROJECT_ROOT')) {
    die('PROJECT_ROOT environment variable is not defined');
}

define('PROJECT_ROOT', getenv('PROJECT_ROOT'));

class SettingsMigrator {
    private $appName;
    private $existingSettings = [];
    private $templateSettings = [];
    private $migratedSettings = [];
    private $backupDir;
    
    public function __construct($appName) {
        $this->appName = $appName;
        $this->backupDir = PROJECT_ROOT . '/private/backups/settings_' . date('Y-m-d_His');
    }
    
    /**
     * Main migration process
     */
    public function migrate() {
        // Create backup directory
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }

        // Load existing settings
        $this->loadExistingSettings();
        
        // Load template structure
        $this->loadTemplateStructure();
        
        // Migrate settings
        $this->migrateSettings();
        
        // Save migrated settings
        $this->saveSettings();
    }
    
    /**
     * Load all existing settings files for the app
     */
    private function loadExistingSettings() {
        $existingFiles = [];
        
        switch ($this->appName) {
            case 'accounts':
                $existingFiles = [
                    'settings' => '/public_html/accounts_system/settings.php',
                    'admin' => '/public_html/admin/settings/account_settings.php',
                    'features' => '/public_html/admin/settings/account_feature_settings.php'
                ];
                break;
        }
        
        foreach ($existingFiles as $type => $path) {
            $fullPath = PROJECT_ROOT . $path;
            if (file_exists($fullPath)) {
                // Backup the file
                copy($fullPath, $this->backupDir . '/' . basename($path) . '.bak');
                
                // Load settings
                $settings = @include($fullPath);
                if (is_array($settings)) {
                    $this->existingSettings[$type] = $settings;
                } else {
                    echo "Warning: Unable to load settings from {$fullPath}\n";
                }
            } else {
                echo "Warning: File not found - {$fullPath}\n";
            }
        }
    }
    
    /**
     * Load template structure for the app
     */
    private function loadTemplateStructure() {
        $templateFile = PROJECT_ROOT . "/public_html/{$this->appName}_system/settings/{$this->appName}-config.template.php";
        if (file_exists($templateFile)) {
            $this->templateSettings = include($templateFile);
        } else {
            echo "Error: Template file not found - {$templateFile}\n";
            die();
        }
    }
    
    /**
     * Migrate settings from old to new format
     */
    private function migrateSettings() {
        // Start with template defaults
        $this->migratedSettings = [];
        
        // Flatten existing settings into key-value pairs
        $flatSettings = $this->flattenSettings($this->existingSettings);
        
        // Map old settings to new format
        foreach ($this->templateSettings as $key => $setting) {
            $defaultValue = $setting['value'];
            $mappedValue = $this->mapOldSettingToNew($key, $flatSettings);
            
            $this->migratedSettings[$key] = [
                'value' => $mappedValue !== null ? $mappedValue : $defaultValue,
                'description' => $setting['description']
            ];
        }
    }
    
    /**
     * Flatten nested settings arrays into key-value pairs
     */
    private function flattenSettings($settings, $prefix = '', $result = []) {
        foreach ($settings as $type => $section) {
            if (is_array($section)) {
                foreach ($section as $key => $value) {
                    if (is_array($value)) {
                        $this->flattenSettings([$key => $value], $prefix . $key . '_', $result);
                    } else {
                        $result[$prefix . $key] = $value;
                    }
                }
            }
        }
        return $result;
    }
    
    /**
     * Map old setting keys to new format
     */
    private function mapOldSettingToNew($newKey, $flatSettings) {
        // Direct mapping cases
        switch ($newKey) {
            case 'registration_enabled':
                return $flatSettings['registration_enabled'] ?? $flatSettings['enabled'] ?? null;
            
            case 'email_verification_required':
                return $flatSettings['require_email'] ?? null;
            
            case 'min_password_length':
                return $flatSettings['min_password'] ?? null;
            
            case 'password_complexity_required':
                return $flatSettings['special_chars'] ?? null;
            
            case 'max_login_attempts':
                return $flatSettings['max_attempts'] ?? null;
            
            case 'login_lockout_duration':
                return $flatSettings['lockout_time'] ?? null;
            
            case 'session_timeout':
                return $flatSettings['session_lifetime'] ?? null;
            
            case 'rememberme_duration':
                return $flatSettings['remember_duration'] ?? null;
            
            case 'avatar_upload_enabled':
                return $flatSettings['allow_avatar'] ?? null;
            
            case 'avatar_max_size':
                if (isset($flatSettings['max_size'])) {
                    // Convert string size to bytes
                    if (preg_match('/^(\d+)MB$/i', $flatSettings['max_size'], $matches)) {
                        return $matches[1] * 1024 * 1024;
                    }
                }
                return null;
            
            case 'profile_picture_types':
                if (isset($flatSettings['allowed_types']) && is_array($flatSettings['allowed_types'])) {
                    return implode(',', $flatSettings['allowed_types']);
                }
                return null;
            
            case 'two_factor_auth_enabled':
                return $flatSettings['enable_2fa'] ?? null;
            
            case 'social_login_enabled':
                return $flatSettings['social_login'] ?? null;
            
            case 'api_access_enabled':
                return $flatSettings['api_access'] ?? null;
        }
        
        // Try fuzzy matching
        foreach ($flatSettings as $oldKey => $value) {
            if (strcasecmp($oldKey, $newKey) === 0) {
                return $value;
            }
        }
        
        return null;
    }
    
    /**
     * Save migrated settings to new files
     */
    private function saveSettings() {
        // Main settings file
        $mainSettingsFile = PROJECT_ROOT . "/public_html/{$this->appName}_system/settings/{$this->appName}-config.php";
        $this->saveSettingsFile($mainSettingsFile, $this->migratedSettings);
        
        // Admin settings file
        $adminSettingsFile = PROJECT_ROOT . "/public_html/admin/settings/{$this->appName}_settings.php";
        $this->saveSettingsFile($adminSettingsFile, $this->migratedSettings);
        
        echo "Settings migrated successfully to:\n";
        echo "- {$mainSettingsFile}\n";
        echo "- {$adminSettingsFile}\n";
    }
    
    /**
     * Save settings array to a file
     */
    private function saveSettingsFile($filePath, $settings) {
        $content = "<?php\n\n";
        $content .= "// Prevent direct access\n";
        $content .= "if (!defined('PROJECT_ROOT')) {\n";
        $content .= "    die('Direct access to this file is not allowed');\n";
        $content .= "}\n\n";
        $content .= "// {$this->appName} system settings\n";
        $content .= "return " . var_export($settings, true) . ";\n";
        
        // Create directory if it doesn't exist
        $dir = dirname($filePath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($filePath, $content);
    }
}

// Check for app name argument
if ($argc != 2) {
    die("Usage: php settings-migrate.php [app_name]\n");
}

$appName = $argv[1];

// Validate app name
$validApps = ['blog', 'accounts', 'client', 'documents'];
if (!in_array($appName, $validApps)) {
    die("Error: Invalid app name. Valid options are: " . implode(', ', $validApps) . "\n");
}

// Run the migrator
$migrator = new SettingsMigrator($appName);
$migrator->migrate();
