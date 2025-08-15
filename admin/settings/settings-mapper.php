<?php
/**
 * SETTINGS MAPPING SCRIPT
 * Analyzes existing config files and maps them to new template structure
 * 
 * Usage: php settings-mapper.php [app_name]
 * Example: php settings-mapper.php blog
 */

// Prevent direct web access
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line');
}

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
                $settings = include($fullPath);
                if (is_array($settings)) {
                    $this->existingSettings[$type] = $settings;
                }
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
        }
    }
    
    /**
     * Create mapping plan between existing and new settings
     */
    private function createMappingPlan() {
        foreach ($this->templateSettings as $section => $settings) {
            $this->mappingPlan[$section] = [];
            
            foreach ($settings as $key => $defaultValue) {
                // Try to find matching setting in existing files
                $found = false;
                foreach ($this->existingSettings as $type => $existingSection) {
                    $matchingKey = $this->findMatchingKey($key, $existingSection);
                    if ($matchingKey !== null) {
                        $this->mappingPlan[$section][$key] = [
                            'source_file' => $type,
                            'source_key' => $matchingKey,
                            'current_value' => $existingSection[$matchingKey],
                            'default_value' => $defaultValue,
                            'status' => 'found'
                        ];
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $this->mappingPlan[$section][$key] = [
                        'source_file' => null,
                        'source_key' => null,
                        'current_value' => null,
                        'default_value' => $defaultValue,
                        'status' => 'missing'
                    ];
                }
            }
        }
        
        // Find orphaned settings (existing settings not in template)
        foreach ($this->existingSettings as $type => $existingSection) {
            foreach ($existingSection as $key => $value) {
                $found = false;
                foreach ($this->mappingPlan as $section) {
                    foreach ($section as $mapping) {
                        if ($mapping['source_key'] === $key) {
                            $found = true;
                            break 2;
                        }
                    }
                }
                
                if (!$found) {
                    $this->mappingPlan['orphaned'][$key] = [
                        'source_file' => $type,
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
        
        foreach ($existingSection as $existingKey => $value) {
            foreach ($variations as $variant) {
                if (strcasecmp($existingKey, $variant) === 0) {
                    return $existingKey;
                }
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
        
        foreach ($this->mappingPlan as $section => $mappings) {
            $report .= "SECTION: {$section}\n";
            $report .= str_repeat('-', strlen($section) + 9) . "\n\n";
            
            foreach ($mappings as $key => $mapping) {
                $report .= "Setting: {$key}\n";
                $report .= "Status: {$mapping['status']}\n";
                
                if ($mapping['source_file']) {
                    $report .= "Source: {$mapping['source_file']} => {$mapping['source_key']}\n";
                    $report .= "Current Value: " . var_export($mapping['current_value'], true) . "\n";
                }
                
                if (isset($mapping['default_value'])) {
                    $report .= "Default Value: " . var_export($mapping['default_value'], true) . "\n";
                }
                
                $report .= "\n";
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
        
        foreach ($this->mappingPlan as $section => $mappings) {
            if ($section !== 'orphaned') {
                foreach ($mappings as $mapping) {
                    $stats['total']++;
                    if ($mapping['status'] === 'found') {
                        $stats['found']++;
                    } elseif ($mapping['status'] === 'missing') {
                        $stats['missing']++;
                    }
                }
            } else {
                $stats['orphaned'] = count($mappings);
            }
        }
        
        return $stats;
    }
}

// Run the mapper if script is called directly
if (isset($argv[1])) {
    define('PROJECT_ROOT', dirname(dirname(dirname(__DIR__))));
    $mapper = new SettingsMapper($argv[1]);
    $mapper->analyze();
} else {
    echo "Usage: php settings-mapper.php [app_name]\n";
    echo "Available apps: blog, accounts, client, documents\n";
}
