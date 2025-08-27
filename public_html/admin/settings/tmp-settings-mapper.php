<?php
/* 
 * Temporary Settings Mapper
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: tmp-settings-mapper.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Temporary tool for mapping settings during migration
 * DETAILED DESCRIPTION:
 * This file serves as a temporary utility for mapping settings during the
 * migration process. It analyzes existing configuration files and maps them
 * to the new template structure, providing a transitional tool for the
 * settings migration process.
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
 * - Configuration analysis
 * - Template structure mapping
 * - Migration assistance
 * - Setting verification
 * - Backup creation
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

class SettingsMapper {
    private $appName;
    private $existingSettings = [];
    private $templateSettings = [];
    private $mappingPlan = [];
    private $backupDir;
    
    public function __construct($appName) {
        $this->appName = $appName;
        $this->backupDir = PROJECT_ROOT . '/private/backups/settings_' . date('Y-m-d_His');
    }
    
    /**
     * Main mapping process
     */
    public function analyze() {
        // Create backup directory
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }

        // Load existing settings
        $this->loadExistingSettings();
        
        // Load template structure
        $this->loadTemplateStructure();
        
        // Create mapping plan
        $this->createMappingPlan();
        
        // Generate report
        $this->generateReport();
    }
    
    /**
     * Load all existing settings files for the app
     */
    private function loadExistingSettings() {
        $existingFiles = [];
        
        switch ($this->appName) {
            case 'blog':
                $existingFiles = [
                    'config' => '/public_html/blog_system/config_settings.php',
                    'settings' => '/public_html/blog_system/assets/settings/blog_settings.php',
                    'admin' => '/public_html/admin/settings/blog_settings.php'
                ];
                break;
                
            case 'accounts':
                $existingFiles = [
                    'settings' => '/public_html/accounts_system/settings.php',
                    'admin' => '/public_html/admin/settings/account_settings.php',
                    'features' => '/public_html/admin/settings/account_feature_settings.php'
                ];
                break;
                
            case 'client':
                $existingFiles = [
                    'config' => '/public_html/client_portal/assets/includes/user-config.php',
                    'admin' => '/public_html/admin/settings/client_settings.php'
                ];
                break;
                
            case 'documents':
                $existingFiles = [
                    'config' => '/public_html/documents_system/pdf-driver/config.php',
                    'fonts' => '/public_html/documents_system/pdf-driver/config_fonts.php',
                    'admin' => '/public_html/admin/settings/documents_settings.php'
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
     * Create mapping plan between existing and new settings
     */
    private function createMappingPlan() {
        foreach ($this->templateSettings as $key => $setting) {
            if (isset($setting['value'])) {
                $defaultValue = $setting['value'];
            } else {
                $defaultValue = null;
                echo "Warning: Invalid template setting format for key {$key}\n";
                continue;
            }

            // Try to find matching setting in existing files
            $found = false;
            foreach ($this->existingSettings as $type => $existingSection) {
                $matchingKey = $this->findMatchingKey($key, $existingSection);
                if ($matchingKey !== null) {
                    $this->mappingPlan[$key] = [
                        'source_file' => $type,
                        'source_key' => $matchingKey,
                        'current_value' => $existingSection[$matchingKey],
                        'default_value' => $defaultValue,
                        'description' => $setting['description'] ?? '',
                        'status' => 'found'
                    ];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $this->mappingPlan[$key] = [
                    'source_file' => null,
                    'source_key' => null,
                    'current_value' => null,
                    'default_value' => $defaultValue,
                    'description' => $setting['description'] ?? '',
                    'status' => 'missing'
                ];
            }
        }
        
        // Find orphaned settings (existing settings not in template)
        foreach ($this->existingSettings as $type => $existingSection) {
            foreach ($existingSection as $key => $value) {
                $found = false;
                foreach ($this->mappingPlan as $templateKey => $mapping) {
                    if ($mapping['source_key'] === $key) {
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $this->mappingPlan['orphaned_' . $key] = [
                        'source_file' => $type,
                        'source_key' => $key,
                        'current_value' => $value,
                        'status' => 'orphaned'
                    ];
                }
            }
        }
    }
    
    /**
     * Find matching key in existing settings using fuzzy matching
     */
    private function findMatchingKey($templateKey, $existingSection) {
        // Direct match
        if (isset($existingSection[$templateKey])) {
            return $templateKey;
        }
        
        // Convert template key to different formats and check
        $variations = [
            $templateKey,
            str_replace('_', '', $templateKey),
            strtolower($templateKey),
            strtoupper($templateKey)
        ];
        
        // Also try converting between different naming styles
        if (strpos($templateKey, '_') !== false) {
            // Convert from snake_case to camelCase
            $variations[] = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $templateKey))));
        } else {
            // Convert from camelCase to snake_case
            $variations[] = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $templateKey));
        }
        
        foreach ($existingSection as $existingKey => $value) {
            foreach ($variations as $variant) {
                if (strcasecmp($existingKey, $variant) === 0) {
                    return $existingKey;
                }
            }
            
            // Try semantic matching if no exact match found
            similar_text($existingKey, $templateKey, $similarity);
            if ($similarity > 80) {
                return $existingKey;
            }
        }
        
        return null;
    }
    
    /**
     * Generate mapping report
     */
    public function generateReport() {
        $report = "============================================================\n";
        $report .= "SETTINGS MAPPING REPORT FOR {$this->appName}\n";
        $report .= "============================================================\n\n";
        
        $report .= "Backup Location: {$this->backupDir}\n\n";
        
        $report .= "SETTINGS MAPPING\n";
        $report .= "----------------\n\n";
        
        foreach ($this->mappingPlan as $key => $mapping) {
            $report .= "Setting: {$key}\n";
            $report .= "Status: {$mapping['status']}\n";
            
            if ($mapping['source_file']) {
                $report .= "Source: {$mapping['source_file']} => {$mapping['source_key']}\n";
                $report .= "Current Value: " . var_export($mapping['current_value'], true) . "\n";
            }
            
            if (isset($mapping['default_value'])) {
                $report .= "Default Value: " . var_export($mapping['default_value'], true) . "\n";
            }
            
            if (!empty($mapping['description'])) {
                $report .= "Description: {$mapping['description']}\n";
            }
            
            $report .= "\n";
        }
        
        // Save report
        $reportFile = $this->backupDir . "/{$this->appName}_mapping_report.txt";
        file_put_contents($reportFile, $report);
        
        echo "Mapping analysis complete. Report saved to: {$reportFile}\n";
        
        // Print statistics
        $stats = $this->getStatistics();
        echo "\nStatistics:\n";
        echo "- Total settings in template: {$stats['total']}\n";
        echo "- Settings found in existing files: {$stats['found']}\n";
        echo "- Missing settings: {$stats['missing']}\n";
        echo "- Orphaned settings: {$stats['orphaned']}\n";
    }
    
    /**
     * Get mapping statistics
     */
    private function getStatistics() {
        $stats = [
            'total' => 0,
            'found' => 0,
            'missing' => 0,
            'orphaned' => 0
        ];
        
        foreach ($this->mappingPlan as $mapping) {
            switch ($mapping['status']) {
                case 'found':
                    $stats['found']++;
                    $stats['total']++;
                    break;
                case 'missing':
                    $stats['missing']++;
                    $stats['total']++;
                    break;
                case 'orphaned':
                    $stats['orphaned']++;
                    break;
            }
        }
        
        return $stats;
    }
}

// Check for app name argument
if ($argc != 2) {
    die("Usage: php settings-mapper.php [app_name]\n");
}

$appName = $argv[1];

// Validate app name
$validApps = ['blog', 'accounts', 'client', 'documents'];
if (!in_array($appName, $validApps)) {
    die("Error: Invalid app name. Valid options are: " . implode(', ', $validApps) . "\n");
}

// Run the mapper
$mapper = new SettingsMapper($appName);
$mapper->analyze();
