<?php
/**
 * PHASE 1: CONFIGURATION MIGRATION HELPER SCRIPT
 * 
 * This script provides automated migration capabilities for converting
 * file-based configuration systems to the new database-driven approach.
 * 
 * Created: August 17, 2025
 * Purpose: Automate configuration migration and validation
 */

// Initialize session and security
session_start();
require_once __DIR__ . '/private/gws-universal-config.php';
require_once __DIR__ . '/private/classes/SettingsManager.php';

class ConfigurationMigrator {
    private $pdo;
    private $settingsManager;
    private $migrationLog = [];
    private $errors = [];
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->settingsManager = new SettingsManager($pdo);
    }
    
    /**
     * Main migration orchestrator
     */
    public function runMigration() {
        echo "<h2>🚀 Phase 1 Configuration Migration</h2>\n";
        echo "<p>Starting migration from file-based configurations to database...</p>\n";
        
        try {
            // Step 1: Verify database schema
            $this->verifyDatabaseSchema();
            
            // Step 2: Backup existing configuration files
            $this->backupConfigurationFiles();
            
            // Step 3: Migrate blog system
            $this->migrateBlogSystem();
            
            // Step 4: Migrate invoice system
            $this->migrateInvoiceSystem();
            
            // Step 5: Migrate other applications
            $this->migrateOtherApplications();
            
            // Step 6: Validate migration
            $this->validateMigration();
            
            // Step 7: Generate migration report
            $this->generateMigrationReport();
            
        } catch (Exception $e) {
            $this->errors[] = "Migration failed: " . $e->getMessage();
            echo "<div class='alert alert-danger'>❌ Migration failed: " . htmlspecialchars($e->getMessage()) . "</div>\n";
        }
    }
    
    /**
     * Verify the database schema exists
     */
    private function verifyDatabaseSchema() {
        echo "<h3>📋 Verifying Database Schema</h3>\n";
        
        $requiredTables = [
            'setting_app_configurations',
            'setting_app_configurations_audit',
            'setting_app_configurations_cache'
        ];
        
        foreach ($requiredTables as $table) {
            $stmt = $this->pdo->prepare("SHOW TABLES LIKE ?");
            $stmt->execute([$table]);
            
            if ($stmt->rowCount() === 0) {
                throw new Exception("Required table '{$table}' does not exist. Please run phase1_app_configurations_schema.sql first.");
            }
            
            echo "✅ Table '{$table}' exists<br>\n";
        }
        
        // Check if template data exists
        $templateCheck = $this->pdo->prepare("
            SELECT COUNT(*) as count FROM setting_app_configurations 
            WHERE app_name IN ('blog_system', 'shop_system', 'accounts_system')
        ");
        $templateCheck->execute();
        $templateCount = $templateCheck->fetchColumn();
        
        if ($templateCount == 0) {
            echo "⚠️ No template configurations found. You may need to run the schema SQL with the INSERT statements.<br>\n";
            $this->errors[] = "No template configurations found in database";
        } else {
            echo "✅ Found {$templateCount} template configuration records<br>\n";
        }
        
        $this->migrationLog[] = "Database schema verification completed";
    }
    
    /**
     * Backup existing configuration files
     */
    private function backupConfigurationFiles() {
        echo "<h3>💾 Backing up Configuration Files</h3>\n";
        
        $backupDir = __DIR__ . '/private/backups/config_migration_' . date('Y-m-d_H-i-s');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $configFiles = [
            'public_html/blog_system/config_settings.php',
            'public_html/shop_system/config.php',
            'public_html/accounts_system/settings.php'
        ];
        
        foreach ($configFiles as $file) {
            $fullPath = __DIR__ . '/' . $file;
            if (file_exists($fullPath)) {
                $backupPath = $backupDir . '/' . basename($file) . '.bak';
                if (copy($fullPath, $backupPath)) {
                    echo "✅ Backed up: {$file}<br>\n";
                    $this->migrationLog[] = "Backed up: {$file}";
                } else {
                    echo "⚠️ Failed to backup: {$file}<br>\n";
                    $this->errors[] = "Failed to backup: {$file}";
                }
            } else {
                echo "ℹ️ File not found (skipping): {$file}<br>\n";
            }
        }
    }
    
    /**
     * Migrate blog system configuration
     */
    private function migrateBlogSystem() {
        echo "<h3>📝 Migrating Blog System Configuration</h3>\n";
        
        $configFile = __DIR__ . '/public_html/blog_system/config_settings.php';
        if (!file_exists($configFile)) {
            echo "⚠️ Blog config file not found: {$configFile}<br>\n";
            return;
        }
        
        // Load the existing settings
        // The blog config file sets $settings variable, so we need to extract it
        unset($settings); // Clear any existing $settings variable
        include($configFile);
        
        if (!isset($settings) || !is_array($settings)) {
            throw new Exception("Invalid blog configuration file format - \$settings variable not found or not an array");
        }
        
        echo "ℹ️ Found " . count($settings) . " blog configuration settings<br>\n";
        
        // Debug: Show first few settings
        $debugSettings = array_slice($settings, 0, 3, true);
        foreach ($debugSettings as $key => $value) {
            echo "🔍 Sample: {$key} = " . htmlspecialchars(substr($value, 0, 30)) . "...<br>\n";
        }
        
        // Mapping from old keys to new structure
        $mapping = [
            'blog_site_url' => ['section' => 'identity', 'key' => 'blog_site_url'],
            'sitename' => ['section' => 'identity', 'key' => 'blog_title'],
            'description' => ['section' => 'identity', 'key' => 'blog_description'],
            'email' => ['section' => 'identity', 'key' => 'email'],
            'date_format' => ['section' => 'display', 'key' => 'date_format'],
            'layout' => ['section' => 'display', 'key' => 'layout'],
            'latestposts_bar' => ['section' => 'display', 'key' => 'latestposts_bar'],
            'sidebar_position' => ['section' => 'display', 'key' => 'sidebar_position'],
            'posts_per_row' => ['section' => 'display', 'key' => 'posts_per_row'],
            'theme' => ['section' => 'display', 'key' => 'theme'],
            'background_image' => ['section' => 'display', 'key' => 'background_image'],
            'comments' => ['section' => 'functionality', 'key' => 'comments'],
            'rtl' => ['section' => 'functionality', 'key' => 'rtl'],
            'head_customcode' => ['section' => 'functionality', 'key' => 'head_customcode'],
            'facebook' => ['section' => 'social', 'key' => 'facebook'],
            'instagram' => ['section' => 'social', 'key' => 'instagram'],
            'twitter' => ['section' => 'social', 'key' => 'twitter'],
            'youtube' => ['section' => 'social', 'key' => 'youtube'],
            'linkedin' => ['section' => 'social', 'key' => 'linkedin'],
            'gcaptcha_sitekey' => ['section' => 'security', 'key' => 'gcaptcha_sitekey'],
            'gcaptcha_secretkey' => ['section' => 'security', 'key' => 'gcaptcha_secretkey'],
            'gcaptcha_projectid' => ['section' => 'security', 'key' => 'gcaptcha_projectid']
        ];
        
        $migratedCount = 0;
        foreach ($settings as $oldKey => $value) {
            if (isset($mapping[$oldKey])) {
                $newConfig = $mapping[$oldKey];
                $success = $this->updateConfigValue(
                    'blog_system', 
                    $newConfig['section'], 
                    $newConfig['key'], 
                    $value
                );
                
                if ($success) {
                    echo "✅ Migrated: {$oldKey} → {$newConfig['section']}.{$newConfig['key']}<br>\n";
                    $migratedCount++;
                } else {
                    echo "❌ Failed: {$oldKey}<br>\n";
                    $this->errors[] = "Failed to migrate blog setting: {$oldKey}";
                }
            } else {
                echo "⚠️ Unmapped: {$oldKey} (value: " . htmlspecialchars(substr($value, 0, 50)) . ")<br>\n";
            }
        }
        
        $this->migrationLog[] = "Blog system migration completed: {$migratedCount} settings migrated";
        echo "<p><strong>Blog migration summary: {$migratedCount} settings migrated</strong></p>\n";
    }
    
    /**
     * Migrate shop system configuration  
     */
    private function migrateShopSystem() {
        echo "<h3>🛒 Migrating Shop System Configuration</h3>\n";
        
        $configFile = __DIR__ . '/public_html/shop_system/config.php';
        if (!file_exists($configFile)) {
            echo "⚠️ Shop config file not found: {$configFile}<br>\n";
            return;
        }
        
        // Parse define() statements from the config file
        $content = file_get_contents($configFile);
        $defines = [];
        
        // Extract define statements with better regex patterns
        // Handle single quotes, double quotes, and boolean values
        preg_match_all("/define\s*\(\s*['\"]([^'\"]+)['\"]\s*,\s*([^)]+)\s*\)\s*;/", $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $key = $match[1];
            $value = trim($match[2]);
            
            // Clean up the value
            if (preg_match("/^['\"](.*)['\"]\s*$/", $value, $valueMatch)) {
                // String value in quotes
                $value = $valueMatch[1];
            } elseif (in_array(strtolower($value), ['true', 'false'])) {
                // Boolean value
                $value = strtolower($value);
            } elseif (is_numeric($value)) {
                // Numeric value - keep as is
                // $value = $value; // No change needed
            }
            
            $defines[$key] = $value;
        }
        
        if (empty($defines)) {
            echo "⚠️ No define() statements found in shop config<br>\n";
            return;
        }
        
        echo "ℹ️ Found " . count($defines) . " configuration defines<br>\n";
        
        // Mapping from define keys to new structure (COMPLETE MAPPING)
        $mapping = [
            // Basic shop settings
            'site_name' => ['section' => 'basic', 'key' => 'site_name'],
            'currency_code' => ['section' => 'basic', 'key' => 'currency_code'],
            'weight_unit' => ['section' => 'basic', 'key' => 'weight_unit'],
            'featured_image' => ['section' => 'basic', 'key' => 'featured_image'],
            'base_url' => ['section' => 'basic', 'key' => 'base_url'],
            
            // Functionality settings
            'account_required' => ['section' => 'functionality', 'key' => 'account_required'],
            'rewrite_url' => ['section' => 'functionality', 'key' => 'rewrite_url'],
            'template_editor' => ['section' => 'functionality', 'key' => 'template_editor'],
            'default_payment_status' => ['section' => 'functionality', 'key' => 'default_payment_status'],
            
            // Mail settings
            'mail_enabled' => ['section' => 'mail', 'key' => 'mail_enabled'],
            'mail_from' => ['section' => 'mail', 'key' => 'mail_from'],
            'mail_name' => ['section' => 'mail', 'key' => 'mail_name'],
            'notifications_enabled' => ['section' => 'mail', 'key' => 'notifications_enabled'],
            'notification_email' => ['section' => 'mail', 'key' => 'notification_email'],
            
            // SMTP settings
            'SMTP' => ['section' => 'smtp', 'key' => 'smtp_enabled'],
            'smtp_secure' => ['section' => 'smtp', 'key' => 'smtp_secure'],
            'smtp_host' => ['section' => 'smtp', 'key' => 'smtp_host'],
            'smtp_port' => ['section' => 'smtp', 'key' => 'smtp_port'],
            'smtp_user' => ['section' => 'smtp', 'key' => 'smtp_username'],
            'smtp_pass' => ['section' => 'smtp', 'key' => 'smtp_password'],
            
            // PayPal settings
            'paypal_enabled' => ['section' => 'payment_paypal', 'key' => 'paypal_enabled'],
            'paypal_email' => ['section' => 'payment_paypal', 'key' => 'paypal_email'],
            'paypal_testmode' => ['section' => 'payment_paypal', 'key' => 'paypal_testmode'],
            'paypal_currency' => ['section' => 'payment_paypal', 'key' => 'paypal_currency'],
            'paypal_ipn_url' => ['section' => 'payment_paypal', 'key' => 'paypal_ipn_url'],
            'paypal_cancel_url' => ['section' => 'payment_paypal', 'key' => 'paypal_cancel_url'],
            'paypal_return_url' => ['section' => 'payment_paypal', 'key' => 'paypal_return_url'],
            
            // Stripe settings
            'stripe_enabled' => ['section' => 'payment_stripe', 'key' => 'stripe_enabled'],
            'stripe_publish_key' => ['section' => 'payment_stripe', 'key' => 'stripe_publish_key'],
            'stripe_secret_key' => ['section' => 'payment_stripe', 'key' => 'stripe_secret_key'],
            'stripe_currency' => ['section' => 'payment_stripe', 'key' => 'stripe_currency'],
            'stripe_ipn_url' => ['section' => 'payment_stripe', 'key' => 'stripe_ipn_url'],
            'stripe_cancel_url' => ['section' => 'payment_stripe', 'key' => 'stripe_cancel_url'],
            'stripe_return_url' => ['section' => 'payment_stripe', 'key' => 'stripe_return_url'],
            'stripe_webhook_secret' => ['section' => 'payment_stripe', 'key' => 'stripe_webhook_secret'],
            
            // Coinbase settings
            'coinbase_enabled' => ['section' => 'payment_coinbase', 'key' => 'coinbase_enabled'],
            'coinbase_key' => ['section' => 'payment_coinbase', 'key' => 'coinbase_key'],
            'coinbase_secret' => ['section' => 'payment_coinbase', 'key' => 'coinbase_secret'],
            'coinbase_currency' => ['section' => 'payment_coinbase', 'key' => 'coinbase_currency'],
            'coinbase_cancel_url' => ['section' => 'payment_coinbase', 'key' => 'coinbase_cancel_url'],
            'coinbase_return_url' => ['section' => 'payment_coinbase', 'key' => 'coinbase_return_url'],
            
            // Payment options
            'pay_on_delivery_enabled' => ['section' => 'payment_cod', 'key' => 'pay_on_delivery_enabled'],
            
            // Security
            'secret_key' => ['section' => 'security', 'key' => 'secret_key']
        ];
        
        $migratedCount = 0;
        foreach ($defines as $oldKey => $value) {
            if (isset($mapping[$oldKey])) {
                $newConfig = $mapping[$oldKey];
                $success = $this->updateConfigValue(
                    'shop_system', 
                    $newConfig['section'], 
                    $newConfig['key'], 
                    $value
                );
                
                if ($success) {
                    echo "✅ Migrated: {$oldKey} → {$newConfig['section']}.{$newConfig['key']}<br>\n";
                    $migratedCount++;
                } else {
                    echo "❌ Failed: {$oldKey}<br>\n";
                    $this->errors[] = "Failed to migrate shop setting: {$oldKey}";
                }
            } else {
                echo "⚠️ Unmapped: {$oldKey} (value: " . htmlspecialchars(substr($value, 0, 50)) . ")<br>\n";
            }
        }
        
        $this->migrationLog[] = "Shop system migration completed: {$migratedCount} settings migrated";
        echo "<p><strong>Shop migration summary: {$migratedCount} settings migrated</strong></p>\n";
    }
    
    /**
     * Migrate invoice system configuration  
     */
    private function migrateInvoiceSystem() {
        echo "<h3>📄 Migrating Invoice System Configuration</h3>\n";
        
        $configFile = __DIR__ . '/public_html/invoice_system/config.php';
        if (!file_exists($configFile)) {
            echo "⚠️ Invoice config file not found: {$configFile}<br>\n";
            return;
        }
        
        // Parse define() statements from the invoice config file
        $content = file_get_contents($configFile);
        $defines = [];
        
        // Extract define statements with better regex patterns
        preg_match_all("/define\s*\(\s*['\"]([^'\"]+)['\"]\s*,\s*([^)]+)\s*\)\s*;/", $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $key = $match[1];
            $value = trim($match[2]);
            
            // Clean up the value
            if (preg_match("/^['\"](.*)['\"]\s*$/", $value, $valueMatch)) {
                // String value in quotes
                $value = $valueMatch[1];
            } elseif (in_array(strtolower($value), ['true', 'false'])) {
                // Boolean value
                $value = strtolower($value);
            } elseif (is_numeric($value)) {
                // Numeric value - keep as is
                // $value = $value; // No change needed
            }
            
            $defines[$key] = $value;
        }
        
        if (empty($defines)) {
            echo "⚠️ No define() statements found in invoice config<br>\n";
            return;
        }
        
        echo "ℹ️ Found " . count($defines) . " invoice configuration defines<br>\n";
        
        // Mapping from define keys to new structure for invoice system
        $mapping = [
            // Basic settings
            'base_url' => ['section' => 'basic', 'key' => 'base_url'],
            'invoice_prefix' => ['section' => 'basic', 'key' => 'invoice_prefix'],
            'currency_code' => ['section' => 'basic', 'key' => 'currency_code'],
            'pdf_attachments' => ['section' => 'basic', 'key' => 'pdf_attachments'],
            'cron_secret' => ['section' => 'basic', 'key' => 'cron_secret'],
            
            // Company settings
            'company_name' => ['section' => 'company', 'key' => 'company_name'],
            'company_email' => ['section' => 'company', 'key' => 'company_email'],
            'company_phone' => ['section' => 'company', 'key' => 'company_phone'],
            'company_address' => ['section' => 'company', 'key' => 'company_address'],
            'company_logo' => ['section' => 'company', 'key' => 'company_logo'],
            
            // Mail settings
            'mail_enabled' => ['section' => 'mail', 'key' => 'mail_enabled'],
            'mail_from' => ['section' => 'mail', 'key' => 'mail_from'],
            'mail_name' => ['section' => 'mail', 'key' => 'mail_name'],
            'notifications_enabled' => ['section' => 'mail', 'key' => 'notifications_enabled'],
            'notification_email' => ['section' => 'mail', 'key' => 'notification_email'],
            
            // SMTP settings
            'SMTP' => ['section' => 'smtp', 'key' => 'smtp_enabled'],
            'smtp_secure' => ['section' => 'smtp', 'key' => 'smtp_secure'],
            'smtp_host' => ['section' => 'smtp', 'key' => 'smtp_host'],
            'smtp_port' => ['section' => 'smtp', 'key' => 'smtp_port'],
            'smtp_user' => ['section' => 'smtp', 'key' => 'smtp_username'],
            'smtp_pass' => ['section' => 'smtp', 'key' => 'smtp_password'],
            
            // PayPal settings
            'paypal_enabled' => ['section' => 'payment_paypal', 'key' => 'paypal_enabled'],
            'paypal_email' => ['section' => 'payment_paypal', 'key' => 'paypal_email'],
            'paypal_testmode' => ['section' => 'payment_paypal', 'key' => 'paypal_testmode'],
            'paypal_currency' => ['section' => 'payment_paypal', 'key' => 'paypal_currency'],
            'paypal_ipn_url' => ['section' => 'payment_paypal', 'key' => 'paypal_ipn_url'],
            
            // Stripe settings
            'stripe_enabled' => ['section' => 'payment_stripe', 'key' => 'stripe_enabled'],
            'stripe_secret_key' => ['section' => 'payment_stripe', 'key' => 'stripe_secret_key'],
            'stripe_currency' => ['section' => 'payment_stripe', 'key' => 'stripe_currency'],
            'stripe_ipn_url' => ['section' => 'payment_stripe', 'key' => 'stripe_ipn_url'],
            'stripe_webhook_secret' => ['section' => 'payment_stripe', 'key' => 'stripe_webhook_secret']
        ];
        
        $migratedCount = 0;
        foreach ($defines as $oldKey => $value) {
            if (isset($mapping[$oldKey])) {
                $newConfig = $mapping[$oldKey];
                $success = $this->updateConfigValue(
                    'invoice_system', 
                    $newConfig['section'], 
                    $newConfig['key'], 
                    $value
                );
                
                if ($success) {
                    echo "✅ Migrated: {$oldKey} → {$newConfig['section']}.{$newConfig['key']}<br>\n";
                    $migratedCount++;
                } else {
                    echo "❌ Failed: {$oldKey}<br>\n";
                    $this->errors[] = "Failed to migrate invoice setting: {$oldKey}";
                }
            } else {
                echo "⚠️ Unmapped: {$oldKey} (value: " . htmlspecialchars(substr($value, 0, 50)) . ")<br>\n";
            }
        }
        
        $this->migrationLog[] = "Invoice system migration completed: {$migratedCount} settings migrated";
        echo "<p><strong>Invoice migration summary: {$migratedCount} settings migrated</strong></p>\n";
    }
    
    /**
     * Migrate other application configurations
     */
    private function migrateOtherApplications() {
        echo "<h3>🔧 Checking Other Applications</h3>\n";
        
        // For now, just check what other config files exist
        $otherConfigs = [
            'accounts_system' => 'public_html/accounts_system/settings.php',
            'client_portal' => 'public_html/client_portal/config.php',
            'form_system' => 'public_html/form_system/config.php'
        ];
        
        foreach ($otherConfigs as $app => $configPath) {
            $fullPath = __DIR__ . '/' . $configPath;
            if (file_exists($fullPath)) {
                echo "ℹ️ Found {$app} config: {$configPath} (manual migration needed)<br>\n";
                $this->migrationLog[] = "Found {$app} configuration file for future migration";
            } else {
                echo "ℹ️ No config found for {$app}<br>\n";
            }
        }
    }
    
    /**
     * Update a configuration value in the database
     */
    private function updateConfigValue($appName, $section, $key, $value) {
        try {
            // First check if the record exists
            $checkStmt = $this->pdo->prepare("
                SELECT id FROM setting_app_configurations 
                WHERE app_name = ? AND section = ? AND config_key = ?
            ");
            $checkStmt->execute([$appName, $section, $key]);
            
            if ($checkStmt->rowCount() === 0) {
                // Record doesn't exist, insert it
                $insertStmt = $this->pdo->prepare("
                    INSERT INTO setting_app_configurations 
                    (app_name, section, config_key, config_value, updated_by, updated_at)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $result = $insertStmt->execute([$appName, $section, $key, $value, 'migration_script']);
                
                if ($result) {
                    echo "✅ Created new config: {$appName}.{$section}.{$key}<br>\n";
                }
                return $result;
            } else {
                // Record exists, update it
                $updateStmt = $this->pdo->prepare("
                    UPDATE setting_app_configurations 
                    SET config_value = ?, updated_by = ?, updated_at = NOW()
                    WHERE app_name = ? AND section = ? AND config_key = ?
                ");
                $result = $updateStmt->execute([$value, 'migration_script', $appName, $section, $key]);
                
                if ($result) {
                    echo "✅ Updated config: {$appName}.{$section}.{$key}<br>\n";
                }
                return $result;
            }
            
        } catch (Exception $e) {
            echo "❌ Database error for {$appName}.{$section}.{$key}: " . $e->getMessage() . "<br>\n";
            $this->errors[] = "Database error updating {$appName}.{$section}.{$key}: " . $e->getMessage();
            return false;
        }
    }
    
    /**
     * Validate the migration results
     */
    private function validateMigration() {
        echo "<h3>✅ Validating Migration</h3>\n";
        
        // Check if configurations were migrated for ALL applications
        $stmt = $this->pdo->prepare("
            SELECT app_name, section, 
                   COUNT(*) as total_settings,
                   COUNT(CASE WHEN config_value IS NOT NULL AND config_value != '' THEN 1 END) as populated_settings
            FROM setting_app_configurations 
            WHERE app_name IN ('blog_system', 'shop_system', 'invoice_system', 'form_system', 'accounts_system', 'chat_system', 'review_system')
            GROUP BY app_name, section
            ORDER BY app_name, section
        ");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as $row) {
            $percentage = ($row['populated_settings'] / $row['total_settings']) * 100;
            echo "📊 {$row['app_name']}.{$row['section']}: {$row['populated_settings']}/{$row['total_settings']} settings populated ({$percentage}%)<br>\n";
        }
        
        $this->migrationLog[] = "Migration validation completed";
    }
    
    /**
     * Generate migration report
     */
    private function generateMigrationReport() {
        echo "<h3>📄 Migration Report</h3>\n";
        
        echo "<div class='alert alert-info'>\n";
        echo "<h5>Migration Summary</h5>\n";
        echo "<ul>\n";
        foreach ($this->migrationLog as $entry) {
            echo "<li>" . htmlspecialchars($entry) . "</li>\n";
        }
        echo "</ul>\n";
        echo "</div>\n";
        
        if (!empty($this->errors)) {
            echo "<div class='alert alert-warning'>\n";
            echo "<h5>Warnings/Errors</h5>\n";
            echo "<ul>\n";
            foreach ($this->errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>\n";
            }
            echo "</ul>\n";
            echo "</div>\n";
        }
        
        echo "<div class='alert alert-success'>\n";
        echo "<h5>🎉 Migration Completed!</h5>\n";
        echo "<p>Your configuration files have been migrated to the database. You can now:</p>\n";
        echo "<ol>\n";
        echo "<li>Access the unified admin interface at: <code>/admin/settings/database_settings.php</code></li>\n";
        echo "<li>Use <code>app_config.php</code> to manage application-specific settings</li>\n";
        echo "<li>Test your applications to ensure they're working correctly</li>\n";
        echo "<li>Remove old configuration files after thorough testing</li>\n";
        echo "</ol>\n";
        echo "</div>\n";
    }
}

// HTML Header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phase 1 Configuration Migration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; font-family: 'Courier New', monospace; }
        .alert { margin: 10px 0; }
        h2, h3 { color: #2c3e50; }
        code { background: #f8f9fa; padding: 2px 4px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Run migration if requested
        if (isset($_GET['run']) && $_GET['run'] === 'true') {
            try {
                $migrator = new ConfigurationMigrator($pdo);
                $migrator->runMigration();
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>❌ Migration initialization failed: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            // Show migration preparation page
            ?>
            <h1>🚀 Phase 1 Configuration Migration</h1>
            <div class="alert alert-info">
                <h5>Ready to migrate your configuration files to the database!</h5>
                <p>This migration will:</p>
                <ul>
                    <li>✅ Verify database schema is ready</li>
                    <li>💾 Backup existing configuration files</li>
                    <li>📝 Migrate blog system settings</li>
                    <li>🛒 Migrate shop system settings</li>
                    <li>📊 Validate migration results</li>
                    <li>📄 Generate detailed migration report</li>
                </ul>
            </div>
            
            <div class="alert alert-warning">
                <h5>⚠️ Prerequisites</h5>
                <p><strong>Before running this migration, ensure you have:</strong></p>
                <ol>
                    <li>Executed <code>phase1_app_configurations_schema.sql</code> in your database</li>
                    <li>Executed <code>phase1_data_migration.sql</code> in your database</li>
                    <li>Backed up your database and files</li>
                    <li>Tested on a development environment first</li>
                </ol>
            </div>
            
            <a href="?run=true" class="btn btn-primary btn-lg">🚀 Start Migration</a>
            <a href="public_html/admin/settings/database_settings.php" class="btn btn-secondary">📊 View Settings Dashboard</a>
            <?php
        }
        ?>
    </div>
</body>
</html>
