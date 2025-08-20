<?php
/**
 * Settings Database Manager
 * 
 * SYSTEM: GWS Universal Hybrid App
 * FILE: SettingsManager.php
 * LOCATION: /private/classes/
 * PURPOSE: Complete database-driven settings management system
 * 
 * This class provides a comprehensive interface for managing all application
 * settings through a centralized database system, replacing scattered file-based
 * configuration with a robust, auditable, and maintainable solution.
 * 
 * FEATURES:
 * - Centralized settings management
 * - Automatic audit trails
 * - Caching for performance
 * - Type validation and sanitization
 * - Batch operations
 * - Migration from file-based settings
 * - Export/Import capabilities
 * 
 * CREATED: 2025-08-15
 * VERSION: 1.0
 */

class SettingsManager {
    private $db;
    private $cache = [];
    private $cache_enabled = true;
    private $cache_duration = 3600; // 1 hour
    private $audit_enabled = true;
    
    /**
     * Constructor
     */
    public function __construct($database_connection = null) {
        if ($database_connection) {
            $this->db = $database_connection;
        } else {
            // Use existing database connection from config
            require_once __DIR__ . '/../../gws-universal-config.php';
            $this->db = $this->createDatabaseConnection();
        }
        
        $this->initializeCache();
    }
    
    /**
     * Create database connection
     */
    private function createDatabaseConnection() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            return $pdo;
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Initialize cache system
     */
    private function initializeCache() {
        // Load frequently accessed settings into memory
        if ($this->cache_enabled) {
            $this->loadCoreSettings();
        }
    }
    
    /**
     * Load core settings that are accessed frequently
     */
    private function loadCoreSettings() {
        try {
            // Load complete branding configuration
            $branding = $this->getCompleteBrandingConfig();
            $this->cache['branding'] = $branding;
            
            // Load contact information
            $contact = $this->getCompleteContactInfo();
            $this->cache['contact'] = $contact;
            
            // Load system configuration
            $system = $this->getSystemConfig();
            $this->cache['system'] = $system;
            
        } catch (Exception $e) {
            error_log("Settings cache initialization failed: " . $e->getMessage());
        }
    }
    
    // ====================================================================
    // CORE SETTINGS RETRIEVAL METHODS
    // ====================================================================
    
    /**
     * Get complete branding configuration
     */
    public function getCompleteBrandingConfig() {
        $cache_key = 'complete_branding';
        
        if ($this->cache_enabled && isset($this->cache[$cache_key])) {
            return $this->cache[$cache_key];
        }
        
        try {
            $stmt = $this->db->query("SELECT * FROM view_complete_branding LIMIT 1");
            $branding = $stmt->fetch();
            
            // If no data returned from view, use fallback
            if (!$branding || empty($branding)) {
                $branding = $this->getFallbackBrandingConfig();
            }
            
            if ($this->cache_enabled) {
                $this->cache[$cache_key] = $branding;
            }
            
            return $branding;
        } catch (Exception $e) {
            error_log("Error retrieving branding config: " . $e->getMessage());
            return $this->getFallbackBrandingConfig();
        }
    }
    
    /**
     * Get complete contact information
     */
    public function getCompleteContactInfo() {
        $cache_key = 'complete_contact';
        
        if ($this->cache_enabled && isset($this->cache[$cache_key])) {
            return $this->cache[$cache_key];
        }
        
        try {
            $stmt = $this->db->query("SELECT * FROM view_complete_contact LIMIT 1");
            $contact = $stmt->fetch();
            
            // If no data returned from view, use fallback
            if (!$contact || empty($contact)) {
                $contact = $this->getFallbackContactInfo();
            }
            
            if ($this->cache_enabled) {
                $this->cache[$cache_key] = $contact;
            }
            
            return $contact;
        } catch (Exception $e) {
            error_log("Error retrieving contact info: " . $e->getMessage());
            return $this->getFallbackContactInfo();
        }
    }
    
    /**
     * Get system configuration
     */
    public function getSystemConfig() {
        $cache_key = 'system_config';
        
        if ($this->cache_enabled && isset($this->cache[$cache_key])) {
            return $this->cache[$cache_key];
        }
        
        try {
            $stmt = $this->db->query("SELECT * FROM setting_system_config LIMIT 1");
            $system = $stmt->fetch();
            
            // If no data returned, use fallback
            if (!$system || empty($system)) {
                $system = $this->getFallbackSystemConfig();
            }
            
            if ($this->cache_enabled) {
                $this->cache[$cache_key] = $system;
            }
            
            return $system;
        } catch (Exception $e) {
            error_log("Error retrieving system config: " . $e->getMessage());
            return $this->getFallbackSystemConfig();
        }
    }
    
    /**
     * Get application-specific configuration
     */
    public function getAppConfig($app_name) {
        $cache_key = "app_config_{$app_name}";
        
        if ($this->cache_enabled && isset($this->cache[$cache_key])) {
            return $this->cache[$cache_key];
        }
        
        try {
            $table_map = [
                'blog' => 'setting_blog_config',
                'shop' => 'setting_shop_config', 
                'portal' => 'setting_portal_config',
                'accounts' => 'setting_accounts_config'
            ];
            
            if (!isset($table_map[$app_name])) {
                throw new Exception("Unknown application: {$app_name}");
            }
            
            $stmt = $this->db->query("SELECT * FROM {$table_map[$app_name]} LIMIT 1");
            $config = $stmt->fetch();
            
            if ($this->cache_enabled) {
                $this->cache[$cache_key] = $config;
            }
            
            return $config;
        } catch (Exception $e) {
            error_log("Error retrieving app config for {$app_name}: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get active content (services, features, testimonials)
     */
    public function getActiveContent($type = null, $limit = null) {
        $cache_key = "active_content_{$type}_{$limit}";
        
        if ($this->cache_enabled && isset($this->cache[$cache_key])) {
            return $this->cache[$cache_key];
        }
        
        try {
            $sql = "SELECT * FROM view_active_content";
            $params = [];
            
            if ($type) {
                $sql .= " WHERE content_type = ?";
                $params[] = $type;
            }
            
            $sql .= " ORDER BY display_order";
            
            if ($limit) {
                $sql .= " LIMIT ?";
                $params[] = (int)$limit;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $content = $stmt->fetchAll();
            
            if ($this->cache_enabled) {
                $this->cache[$cache_key] = $content;
            }
            
            return $content;
        } catch (Exception $e) {
            error_log("Error retrieving active content: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get SEO settings for a specific page
     */
    public function getPageSEO($page_slug) {
        $cache_key = "page_seo_{$page_slug}";
        
        if ($this->cache_enabled && isset($this->cache[$cache_key])) {
            return $this->cache[$cache_key];
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM setting_seo_pages WHERE page_slug = ?");
            $stmt->execute([$page_slug]);
            $seo = $stmt->fetch();
            
            // If no specific SEO found, get global defaults
            if (!$seo) {
                $global_stmt = $this->db->query("SELECT * FROM setting_seo_global LIMIT 1");
                $global_seo = $global_stmt->fetch();
                
                $seo = [
                    'page_slug' => $page_slug,
                    'page_title' => ucfirst(str_replace(['-', '_'], ' ', $page_slug)) . ($global_seo['default_title_suffix'] ?? ''),
                    'meta_description' => $global_seo['default_meta_description'] ?? '',
                    'meta_keywords' => $global_seo['default_meta_keywords'] ?? '',
                    'canonical_url' => '',
                    'noindex' => false,
                    'nofollow' => false
                ];
            }
            
            if ($this->cache_enabled) {
                $this->cache[$cache_key] = $seo;
            }
            
            return $seo;
        } catch (Exception $e) {
            error_log("Error retrieving page SEO for {$page_slug}: " . $e->getMessage());
            return $this->getFallbackPageSEO($page_slug);
        }
    }
    
    // ====================================================================
    // SETTINGS UPDATE METHODS
    // ====================================================================
    
    /**
     * Update business identity settings
     */
    public function updateBusinessIdentity($data, $updated_by = 'system') {
        try {
            $this->db->beginTransaction();
            
            // Get current data for audit
            $current_stmt = $this->db->query("SELECT * FROM setting_business_identity LIMIT 1");
            $current_data = $current_stmt->fetch();
            
            if ($current_data) {
                // Update existing record
                $sql = "UPDATE setting_business_identity SET ";
                $fields = [];
                $params = [];
                
                foreach ($data as $key => $value) {
                    if (isset($current_data[$key])) {
                        $fields[] = "{$key} = ?";
                        $params[] = $value;
                        
                        // Create audit trail for changed fields
                        if ($current_data[$key] !== $value) {
                            $this->createAuditRecord("business_identity.{$key}", $current_data[$key], $value, $updated_by, 'Business identity update');
                        }
                    }
                }
                
                if (!empty($fields)) {
                    $sql .= implode(', ', $fields) . " WHERE id = ?";
                    $params[] = $current_data['id'];
                    
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute($params);
                }
            } else {
                // Insert new record
                $fields = array_keys($data);
                $placeholders = str_repeat('?,', count($fields) - 1) . '?';
                $sql = "INSERT INTO setting_business_identity (" . implode(',', $fields) . ") VALUES ({$placeholders})";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute(array_values($data));
            }
            
            $this->db->commit();
            $this->clearCache('branding');
            
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error updating business identity: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update branding colors
     */
    public function updateBrandingColors($data, $updated_by = 'system') {
        try {
            $this->db->beginTransaction();
            
            $current_stmt = $this->db->query("SELECT * FROM setting_branding_colors LIMIT 1");
            $current_data = $current_stmt->fetch();
            
            if ($current_data) {
                $sql = "UPDATE setting_branding_colors SET ";
                $fields = [];
                $params = [];
                
                foreach ($data as $key => $value) {
                    if (isset($current_data[$key])) {
                        $fields[] = "{$key} = ?";
                        $params[] = $value;
                        
                        if ($current_data[$key] !== $value) {
                            $this->createAuditRecord("branding_colors.{$key}", $current_data[$key], $value, $updated_by, 'Brand colors update');
                        }
                    }
                }
                
                if (!empty($fields)) {
                    $sql .= implode(', ', $fields) . " WHERE id = ?";
                    $params[] = $current_data['id'];
                    
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute($params);
                }
            } else {
                $fields = array_keys($data);
                $placeholders = str_repeat('?,', count($fields) - 1) . '?';
                $sql = "INSERT INTO setting_branding_colors (" . implode(',', $fields) . ") VALUES ({$placeholders})";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute(array_values($data));
            }
            
            $this->db->commit();
            $this->clearCache('branding');
            
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error updating branding colors: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update contact information
     */
    public function updateContactInfo($data, $updated_by = 'system') {
        try {
            $this->db->beginTransaction();
            
            $current_stmt = $this->db->query("SELECT * FROM setting_contact_info LIMIT 1");
            $current_data = $current_stmt->fetch();
            
            if ($current_data) {
                $sql = "UPDATE setting_contact_info SET ";
                $fields = [];
                $params = [];
                
                foreach ($data as $key => $value) {
                    if (isset($current_data[$key])) {
                        $fields[] = "{$key} = ?";
                        $params[] = $value;
                        
                        if ($current_data[$key] !== $value) {
                            $this->createAuditRecord("contact_info.{$key}", $current_data[$key], $value, $updated_by, 'Contact information update');
                        }
                    }
                }
                
                if (!empty($fields)) {
                    $sql .= implode(', ', $fields) . " WHERE id = ?";
                    $params[] = $current_data['id'];
                    
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute($params);
                }
            } else {
                $fields = array_keys($data);
                $placeholders = str_repeat('?,', count($fields) - 1) . '?';
                $sql = "INSERT INTO setting_contact_info (" . implode(',', $fields) . ") VALUES ({$placeholders})";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute(array_values($data));
            }
            
            $this->db->commit();
            $this->clearCache('contact');
            
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error updating contact info: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generic setting update with validation
     */
    public function updateSetting($table, $data, $updated_by = 'system', $where_clause = null) {
        try {
            $this->db->beginTransaction();
            
            // Validate table name to prevent SQL injection
            if (!$this->isValidSettingTable($table)) {
                throw new Exception("Invalid settings table: {$table}");
            }
            
            // Get current data
            $current_sql = "SELECT * FROM {$table}";
            if ($where_clause) {
                $current_sql .= " WHERE {$where_clause}";
            }
            $current_sql .= " LIMIT 1";
            
            $current_stmt = $this->db->query($current_sql);
            $current_data = $current_stmt->fetch();
            
            if ($current_data) {
                // Update existing record
                $fields = [];
                $params = [];
                
                foreach ($data as $key => $value) {
                    if (isset($current_data[$key])) {
                        $fields[] = "{$key} = ?";
                        $params[] = $value;
                        
                        if ($current_data[$key] !== $value) {
                            $this->createAuditRecord("{$table}.{$key}", $current_data[$key], $value, $updated_by, 'Setting update');
                        }
                    }
                }
                
                if (!empty($fields)) {
                    $sql = "UPDATE {$table} SET " . implode(', ', $fields);
                    if ($where_clause) {
                        $sql .= " WHERE {$where_clause}";
                    } else {
                        $sql .= " WHERE id = ?";
                        $params[] = $current_data['id'];
                    }
                    
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute($params);
                }
            } else {
                // Insert new record
                $fields = array_keys($data);
                $placeholders = str_repeat('?,', count($fields) - 1) . '?';
                $sql = "INSERT INTO {$table} (" . implode(',', $fields) . ") VALUES ({$placeholders})";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute(array_values($data));
            }
            
            $this->db->commit();
            $this->clearCache();
            
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error updating setting in {$table}: " . $e->getMessage());
            return false;
        }
    }
    
    // ====================================================================
    // UTILITY METHODS
    // ====================================================================
    
    /**
     * Create audit record
     */
    private function createAuditRecord($setting_key, $old_value, $new_value, $changed_by, $reason) {
        if (!$this->audit_enabled) {
            return;
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO setting_system_audit (setting_key, old_value, new_value, changed_by, change_reason)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$setting_key, $old_value, $new_value, $changed_by, $reason]);
        } catch (Exception $e) {
            error_log("Error creating audit record: " . $e->getMessage());
        }
    }
    
    /**
     * Validate settings table name
     */
    private function isValidSettingTable($table) {
        $valid_tables = [
            'setting_business_identity',
            'setting_branding_colors',
            'setting_branding_fonts',
            'setting_branding_assets',
            'setting_branding_templates',
            'setting_contact_info',
            'setting_social_media',
            'setting_content_homepage',
            'setting_content_services',
            'setting_content_features',
            'setting_content_testimonials',
            'setting_seo_global',
            'setting_seo_pages',
            'setting_blog_config',
            'setting_shop_config',
            'setting_portal_config',
            'setting_accounts_config',
            'setting_email_config',
            'setting_contact_config',
            'setting_payment_config',
            'setting_system_config',
            'setting_security_config',
            'setting_performance_config',
            'setting_analytics_config'
        ];
        
        return in_array($table, $valid_tables);
    }
    
    /**
     * Clear cache
     */
    public function clearCache($specific_key = null) {
        if ($specific_key) {
            unset($this->cache[$specific_key]);
            // Clear related cached items
            foreach (array_keys($this->cache) as $key) {
                if (strpos($key, $specific_key) !== false) {
                    unset($this->cache[$key]);
                }
            }
        } else {
            $this->cache = [];
        }
    }
    
    /**
     * Get cache information
     */
    public function getCacheInfo() {
        return [
            'count' => count($this->cache),
            'enabled' => $this->cache_enabled,
            'duration' => $this->cache_duration,
            'keys' => array_keys($this->cache)
        ];
    }
    
    /**
     * Export settings to JSON
     */
    public function exportSettings($categories = null) {
        try {
            $export_data = [];
            
            // Export all major settings categories
            $categories = $categories ?: [
                'business_identity',
                'branding_colors', 
                'branding_fonts',
                'branding_assets',
                'contact_info',
                'social_media',
                'system_config',
                'email_config'
            ];
            
            foreach ($categories as $category) {
                $table = "setting_{$category}";
                if ($this->isValidSettingTable($table)) {
                    $stmt = $this->db->query("SELECT * FROM {$table}");
                    $export_data[$category] = $stmt->fetchAll();
                }
            }
            
            return json_encode($export_data, JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            error_log("Error exporting settings: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get fallback configurations in case database is unavailable
     */
    private function getFallbackBrandingConfig() {
        return [
            'business_name_short' => 'GWS',
            'business_name_medium' => 'GWS Universal',
            'business_name_long' => 'GWS Universal Hybrid Application',
            'brand_primary_color' => '#6c2eb6',
            'brand_secondary_color' => '#bf5512',
            'brand_font_primary' => 'Arial, sans-serif',
            'business_logo_main' => 'assets/img/logo.png',
            'favicon_main' => 'assets/img/favicon.png'
        ];
    }
    
    private function getFallbackContactInfo() {
        return [
            'contact_email' => 'info@example.com',
            'contact_phone' => '',
            'contact_address' => ''
        ];
    }
    
    private function getFallbackSystemConfig() {
        return [
            'environment' => 'production',
            'debug_mode' => false,
            'timezone' => 'America/New_York'
        ];
    }
    
    private function getFallbackPageSEO($page_slug) {
        return [
            'page_slug' => $page_slug,
            'page_title' => ucfirst(str_replace(['-', '_'], ' ', $page_slug)),'noindex' => false,
            'nofollow' => false
        ];
    }
    
    /**
     * Generate branding CSS variables from database
     */
    public function generateBrandingCSS() {
        $branding = $this->getCompleteBrandingConfig();
        
        if (!$branding) {
            return '';
        }
        
        $css = ":root {\n";
        $css .= "    --brand-primary: {$branding['brand_primary_color']};\n";
        $css .= "    --brand-secondary: {$branding['brand_secondary_color']};\n";
        $css .= "    --brand-accent: {$branding['brand_accent_color']};\n";
        $css .= "    --brand-font-primary: {$branding['brand_font_primary']};\n";
        $css .= "    --brand-font-headings: {$branding['brand_font_headings']};\n";
        $css .= "    --brand-font-body: {$branding['brand_font_body']};\n";
        $css .= "}\n";
        
        return $css;
    }
    
    /**
     * Generate backwards-compatible PHP variables
     */
    public function generateLegacyVariables() {
        $branding = $this->getCompleteBrandingConfig();
        $contact = $this->getCompleteContactInfo();
        
        $php = "<?php\n";
        $php .= "// Auto-generated from database settings - " . date('Y-m-d H:i:s') . "\n";
        $php .= "// DO NOT EDIT - Use admin settings interface instead\n\n";
        
        if ($branding) {
            $php .= "// Business Identity\n";
            $php .= "\$business_name_short = '" . addslashes($branding['business_name_short']) . "';\n";
            $php .= "\$business_name_medium = '" . addslashes($branding['business_name_medium']) . "';\n";
            $php .= "\$business_name_long = '" . addslashes($branding['business_name_long']) . "';\n";
            $php .= "\$business_name = \$business_name_long; // Backward compatibility\n\n";
            
            $php .= "// Brand Colors\n";
            $php .= "\$brand_primary_color = '{$branding['brand_primary_color']}';\n";
            $php .= "\$brand_secondary_color = '{$branding['brand_secondary_color']}';\n";
            $php .= "\$brand_accent_color = '{$branding['brand_accent_color']}';\n\n";
            
            $php .= "// Brand Assets\n";
            $php .= "\$business_logo = '{$branding['business_logo_main']}';\n";
            $php .= "\$favicon = '{$branding['favicon_main']}';\n\n";
        }
        
        if ($contact) {
            $php .= "// Contact Information\n";
            $php .= "\$contact_email = '{$contact['contact_email']}';\n";
            $php .= "\$contact_phone = '{$contact['contact_phone']}';\n";
            $php .= "\$contact_address = '{$contact['contact_address']}';\n\n";
        }
        
        $php .= "?>";
        
        return $php;
    }
    
    // ====================================================================
    // BLOG SYSTEM METHODS
    // ====================================================================
    
    /**
     * Get blog identity settings
     */
    public function getBlogIdentity() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM setting_blog_identity ORDER BY id DESC LIMIT 1");
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result ?: [
                'blog_title' => 'My Blog',
                'blog_description' => 'Welcome to my blog',
                'blog_tagline' => 'Sharing thoughts and ideas',
                'author_name' => 'Blog Author',
                'author_bio' => 'About the author',
                'default_author_id' => 1,'meta_keywords' => 'blog, content, articles',
                'blog_email' => '',
                'blog_url' => '',];
        } catch (Exception $e) {
            error_log("Error getting blog identity: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update blog identity settings
     */
    public function updateBlogIdentity($data, $updated_by = 'admin') {
        try {
            // Check if record exists
            $stmt = $this->db->prepare("SELECT id FROM setting_blog_identity LIMIT 1");
            $stmt->execute();
            $exists = $stmt->fetch();
            
            if ($exists) {
                // Update existing record
                $sql = "UPDATE setting_blog_identity SET 
                        blog_title = ?, blog_description = ?, blog_tagline = ?, 
                        author_name = ?, author_bio = ?, default_author_id = ?,
                        meta_description = ?, meta_keywords = ?, blog_email = ?,
                        blog_url = ?, copyright_text = ?, updated_at = CURRENT_TIMESTAMP 
                        WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    $data['blog_title'], $data['blog_description'], $data['blog_tagline'],
                    $data['author_name'], $data['author_bio'], $data['default_author_id'],
                    $data['meta_description'], $data['meta_keywords'], $data['blog_email'],
                    $data['blog_url'], $data['copyright_text'], $exists['id']
                ]);
            } else {
                // Insert new record
                $sql = "INSERT INTO setting_blog_identity 
                        (blog_title, blog_description, blog_tagline, author_name, author_bio, 
                         default_author_id, meta_description, meta_keywords, blog_email, 
                         blog_url, copyright_text) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    $data['blog_title'], $data['blog_description'], $data['blog_tagline'],
                    $data['author_name'], $data['author_bio'], $data['default_author_id'],
                    $data['meta_description'], $data['meta_keywords'], $data['blog_email'],
                    $data['blog_url'], $data['copyright_text']
                ]);
            }
        } catch (Exception $e) {
            error_log("Error updating blog identity: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get blog display settings
     */
    public function getBlogDisplay() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM setting_blog_display ORDER BY id DESC LIMIT 1");
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result ?: [
                'posts_per_page' => 10,
                'excerpt_length' => 250,
                'date_format' => 'F j, Y',
                'layout' => 'Wide',
                'sidebar_position' => 'Right',
                'posts_per_row' => 2,
                'theme' => 'Default',
                'enable_featured_image' => 1,
                'thumbnail_width' => 300,
                'thumbnail_height' => 200,
                'background_image' => '',
                'custom_css' => '',
                'show_author' => 1,
                'show_date' => 1,
                'show_categories' => 1,
                'show_tags' => 1,
                'show_excerpt' => 1
            ];
        } catch (Exception $e) {
            error_log("Error getting blog display: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update blog display settings
     */
    public function updateBlogDisplay($data, $updated_by = 'admin') {
        try {
            // Check if record exists
            $stmt = $this->db->prepare("SELECT id FROM setting_blog_display LIMIT 1");
            $stmt->execute();
            $exists = $stmt->fetch();
            
            if ($exists) {
                // Update existing record
                $sql = "UPDATE setting_blog_display SET 
                        posts_per_page = ?, excerpt_length = ?, date_format = ?, layout = ?,
                        sidebar_position = ?, posts_per_row = ?, theme = ?, enable_featured_image = ?,
                        thumbnail_width = ?, thumbnail_height = ?, background_image = ?, custom_css = ?,
                        show_author = ?, show_date = ?, show_categories = ?, show_tags = ?, show_excerpt = ?,
                        updated_at = CURRENT_TIMESTAMP WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    $data['posts_per_page'], $data['excerpt_length'], $data['date_format'], $data['layout'],
                    $data['sidebar_position'], $data['posts_per_row'], $data['theme'], $data['enable_featured_image'],
                    $data['thumbnail_width'], $data['thumbnail_height'], $data['background_image'], $data['custom_css'],
                    $data['show_author'], $data['show_date'], $data['show_categories'], $data['show_tags'], $data['show_excerpt'],
                    $exists['id']
                ]);
            } else {
                // Insert new record
                $sql = "INSERT INTO setting_blog_display 
                        (posts_per_page, excerpt_length, date_format, layout, sidebar_position, posts_per_row,
                         theme, enable_featured_image, thumbnail_width, thumbnail_height, background_image, custom_css,
                         show_author, show_date, show_categories, show_tags, show_excerpt) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    $data['posts_per_page'], $data['excerpt_length'], $data['date_format'], $data['layout'],
                    $data['sidebar_position'], $data['posts_per_row'], $data['theme'], $data['enable_featured_image'],
                    $data['thumbnail_width'], $data['thumbnail_height'], $data['background_image'], $data['custom_css'],
                    $data['show_author'], $data['show_date'], $data['show_categories'], $data['show_tags'], $data['show_excerpt']
                ]);
            }
        } catch (Exception $e) {
            error_log("Error updating blog display: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all blog settings (consolidated)
     */
    public function getAllBlogSettings() {
        return [
            'identity' => $this->getBlogIdentity(),
            'display' => $this->getBlogDisplay(),
            'features' => $this->getBlogFeatures(),
            'comments' => $this->getBlogComments(),
            'seo' => $this->getBlogSeo(),
            'social' => $this->getBlogSocial()
        ];
    }
    
    /**
     * Get blog features settings
     */
    public function getBlogFeatures() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM setting_blog_features ORDER BY id DESC LIMIT 1");
            $stmt->execute();
            return $stmt->fetch() ?: [];
        } catch (Exception $e) {
            error_log("Error getting blog features: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get blog comments settings
     */
    public function getBlogComments() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM setting_blog_comments ORDER BY id DESC LIMIT 1");
            $stmt->execute();
            return $stmt->fetch() ?: [];
        } catch (Exception $e) {
            error_log("Error getting blog comments: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get blog SEO settings
     */
    public function getBlogSeo() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM setting_blog_seo ORDER BY id DESC LIMIT 1");
            $stmt->execute();
            return $stmt->fetch() ?: [];
        } catch (Exception $e) {
            error_log("Error getting blog SEO: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get blog social settings
     */
    public function getBlogSocial() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM setting_blog_social ORDER BY id DESC LIMIT 1");
            $stmt->execute();
            return $stmt->fetch() ?: [];
        } catch (Exception $e) {
            error_log("Error getting blog social: " . $e->getMessage());
            return [];
        }
    }
}

/**
 * Settings Helper Functions
 * 
 * Global convenience functions for accessing settings
 */

// Global settings manager instance
$GLOBALS['settings_manager'] = null;

function getSettingsManager() {
    if (!isset($GLOBALS['settings_manager']) || $GLOBALS['settings_manager'] === null) {
        $GLOBALS['settings_manager'] = new SettingsManager();
    }
    return $GLOBALS['settings_manager'];
}

function getBusinessName($length = 'long') {
    $manager = getSettingsManager();
    $branding = $manager->getCompleteBrandingConfig();
    
    switch($length) {
        case 'short': return $branding['business_name_short'] ?? 'GWS';
        case 'medium': return $branding['business_name_medium'] ?? 'GWS Universal';
        case 'long': return $branding['business_name_long'] ?? 'GWS Universal Hybrid Application';
        default: return $branding['business_name_long'] ?? 'GWS Universal Hybrid Application';
    }
}

function getBrandColor($type = 'primary') {
    $manager = getSettingsManager();
    $branding = $manager->getCompleteBrandingConfig();
    
    switch($type) {
        case 'primary': return $branding['brand_primary_color'] ?? '#6c2eb6';
        case 'secondary': return $branding['brand_secondary_color'] ?? '#bf5512';
        case 'accent': return $branding['brand_accent_color'] ?? '#28a745';
        default: return $branding['brand_primary_color'] ?? '#6c2eb6';
    }
}

function getContactInfo($field = null) {
    $manager = getSettingsManager();
    $contact = $manager->getCompleteContactInfo();
    
    if ($field) {
        return $contact[$field] ?? '';
    }
    
    return $contact;
}

function getPageSEO($page_slug) {
    $manager = getSettingsManager();
    return $manager->getPageSEO($page_slug);
}

function getAppConfig($app_name) {
    $manager = getSettingsManager();
    return $manager->getAppConfig($app_name);
}

?>
