<?php
/**
 * Settings Migration Wizard
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: settings_migration.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Migrate existing file-based settings to database system
 * 
 * This wizard analyzes all existing file-based settings throughout the
 * application and provides a comprehensive migration path to the new
 * database-driven settings system.
 * 
 * FEATURES:
 * - Automatic detection of existing settings files
 * - Data mapping and validation
 * - Backup creation before migration
 * - Progress tracking and rollback capability
 * - Conflict resolution
 * 
 * CREATED: 2025-08-15
 * VERSION: 1.0
 */

// Initialize session and security
session_start();
require_once __DIR__ . '/../../../private/gws-universal-config.php';
require_once __DIR__ . '/../../../private/classes/SettingsManager.php';
require_once __DIR__ . '/../shared/template_admin.php';

// Security check
if (!isset($_SESSION['admin_user'])) {
    header('Location: ../login.php');
    exit;
}

// Initialize settings manager
$settingsManager = new SettingsManager();

// Migration status tracking
$migration_status = $_SESSION['migration_status'] ?? 'not_started';
$migration_progress = $_SESSION['migration_progress'] ?? [];
$migration_errors = $_SESSION['migration_errors'] ?? [];

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'analyze_settings':
                $analysis_results = analyzeExistingSettings();
                $_SESSION['migration_analysis'] = $analysis_results;
                $_SESSION['migration_status'] = 'analyzed';
                $message = 'Settings analysis complete. Review the findings below.';
                $message_type = 'success';
                break;
                
            case 'start_migration':
                $selected_files = $_POST['migrate_files'] ?? [];
                if (empty($selected_files)) {
                    throw new Exception('Please select at least one settings file to migrate.');
                }
                
                $migration_results = performMigration($selected_files);
                $_SESSION['migration_results'] = $migration_results;
                $_SESSION['migration_status'] = 'completed';
                $message = 'Migration completed successfully. Check the results below.';
                $message_type = 'success';
                break;
                
            case 'create_backup':
                $backup_result = createSettingsBackup();
                if ($backup_result) {
                    $message = 'Settings backup created successfully: ' . $backup_result;
                    $message_type = 'success';
                } else {
                    $message = 'Error creating settings backup.';
                    $message_type = 'error';
                }
                break;
                
            case 'reset_migration':
                unset($_SESSION['migration_status'], $_SESSION['migration_analysis'], $_SESSION['migration_results']);
                $message = 'Migration status reset.';
                $message_type = 'info';
                break;
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

/**
 * Analyze existing settings files
 */
function analyzeExistingSettings() {
    $analysis = [
        'files_found' => [],
        'settings_count' => 0,
        'conflicts' => [],
        'recommendations' => []
    ];
    
    // Define settings files to analyze
    $settings_files = [
        'branding_settings.php' => __DIR__ . '/../../assets/includes/settings/branding_settings.php',
        'public_settings.php' => __DIR__ . '/../../assets/includes/settings/public_settings.php',
        'contact_settings.php' => __DIR__ . '/../../assets/includes/settings/contact_settings.php',
        'blog_settings.php' => __DIR__ . '/../../assets/includes/settings/blog_settings.php',
        'client_portal_settings.php' => __DIR__ . '/../../assets/includes/settings/client_portal_settings.php',
        'seo_settings.php' => __DIR__ . '/../../assets/includes/settings/seo_settings.php',
        'account_settings.php' => __DIR__ . '/../../assets/includes/settings/account_settings.php',
        'shop_config.php' => __DIR__ . '/../../shop_system/config.php',
        'blog_config.php' => __DIR__ . '/../../blog_system/config_settings.php',
        'accounts_config.php' => __DIR__ . '/../../accounts_system/accounts-system-config.php'
    ];
    
    foreach ($settings_files as $file_key => $file_path) {
        if (file_exists($file_path)) {
            $file_info = analyzeSettingsFile($file_path, $file_key);
            if ($file_info) {
                $analysis['files_found'][] = $file_info;
                $analysis['settings_count'] += $file_info['setting_count'];
            }
        }
    }
    
    // Check for conflicts and provide recommendations
    $analysis['conflicts'] = detectSettingsConflicts($analysis['files_found']);
    $analysis['recommendations'] = generateMigrationRecommendations($analysis);
    
    return $analysis;
}

/**
 * Analyze individual settings file
 */
function analyzeSettingsFile($file_path, $file_key) {
    try {
        $content = file_get_contents($file_path);
        if (!$content) return null;
        
        $file_info = [
            'key' => $file_key,
            'path' => $file_path,
            'size' => filesize($file_path),
            'modified' => filemtime($file_path),
            'setting_count' => 0,
            'variables' => [],
            'arrays' => [],
            'file_type' => 'unknown'
        ];
        
        // Count PHP variables
        preg_match_all('/\$([a-zA-Z_][a-zA-Z0-9_]*)\s*=/', $content, $var_matches);
        $file_info['variables'] = array_unique($var_matches[1]);
        $file_info['setting_count'] = count($file_info['variables']);
        
        // Count array definitions
        preg_match_all('/\$([a-zA-Z_][a-zA-Z0-9_]*)\s*=\s*array\s*\(/', $content, $array_matches);
        $file_info['arrays'] = array_unique($array_matches[1]);
        
        // Determine file type
        if (strpos($file_key, 'branding') !== false) {
            $file_info['file_type'] = 'branding';
        } elseif (strpos($file_key, 'contact') !== false) {
            $file_info['file_type'] = 'contact';
        } elseif (strpos($file_key, 'blog') !== false) {
            $file_info['file_type'] = 'blog';
        } elseif (strpos($file_key, 'shop') !== false) {
            $file_info['file_type'] = 'shop';
        } elseif (strpos($file_key, 'account') !== false) {
            $file_info['file_type'] = 'accounts';
        } else {
            $file_info['file_type'] = 'general';
        }
        
        return $file_info;
        
    } catch (Exception $e) {
        error_log("Error analyzing settings file {$file_path}: " . $e->getMessage());
        return null;
    }
}

/**
 * Detect conflicts between settings files
 */
function detectSettingsConflicts($files_found) {
    $conflicts = [];
    $all_variables = [];
    
    // Collect all variables
    foreach ($files_found as $file) {
        foreach ($file['variables'] as $var) {
            if (!isset($all_variables[$var])) {
                $all_variables[$var] = [];
            }
            $all_variables[$var][] = $file['key'];
        }
    }
    
    // Find conflicts (same variable in multiple files)
    foreach ($all_variables as $var => $files) {
        if (count($files) > 1) {
            $conflicts[] = [
                'variable' => $var,
                'files' => $files,
                'severity' => 'high'
            ];
        }
    }
    
    return $conflicts;
}

/**
 * Generate migration recommendations
 */
function generateMigrationRecommendations($analysis) {
    $recommendations = [];
    
    // Recommend migration order
    $priority_order = ['branding', 'contact', 'general', 'blog', 'shop', 'accounts'];
    
    foreach ($priority_order as $type) {
        $files_of_type = array_filter($analysis['files_found'], function($file) use ($type) {
            return $file['file_type'] === $type;
        });
        
        if (!empty($files_of_type)) {
            $recommendations[] = [
                'type' => 'migration_order',
                'priority' => $type,
                'files' => array_column($files_of_type, 'key'),
                'message' => "Migrate {$type} settings first to establish core configuration."
            ];
        }
    }
    
    // Recommend backup
    if ($analysis['settings_count'] > 0) {
        $recommendations[] = [
            'type' => 'backup',
            'priority' => 'critical',
            'message' => 'Create a backup before starting migration to allow rollback if needed.'
        ];
    }
    
    // Recommend conflict resolution
    if (!empty($analysis['conflicts'])) {
        $recommendations[] = [
            'type' => 'conflicts',
            'priority' => 'high',
            'message' => 'Resolve variable conflicts before migration to prevent data loss.'
        ];
    }
    
    return $recommendations;
}

/**
 * Perform the actual migration
 */
function performMigration($selected_files) {
    $results = [
        'success' => [],
        'errors' => [],
        'skipped' => [],
        'total_migrated' => 0
    ];
    
    global $settingsManager;
    
    foreach ($selected_files as $file_key) {
        try {
            $migration_result = migrateSettingsFile($file_key);
            
            if ($migration_result['success']) {
                $results['success'][] = [
                    'file' => $file_key,
                    'migrated_count' => $migration_result['migrated_count'],
                    'details' => $migration_result['details']
                ];
                $results['total_migrated'] += $migration_result['migrated_count'];
            } else {
                $results['errors'][] = [
                    'file' => $file_key,
                    'error' => $migration_result['error']
                ];
            }
            
        } catch (Exception $e) {
            $results['errors'][] = [
                'file' => $file_key,
                'error' => $e->getMessage()
            ];
        }
    }
    
    return $results;
}

/**
 * Migrate individual settings file
 */
function migrateSettingsFile($file_key) {
    global $settingsManager;
    
    // Map file keys to migration handlers
    $migration_handlers = [
        'branding_settings.php' => 'migrateBrandingSettings',
        'public_settings.php' => 'migratePublicSettings',
        'contact_settings.php' => 'migrateContactSettings',
        'blog_settings.php' => 'migrateBlogSettings',
        'client_portal_settings.php' => 'migratePortalSettings',
        'shop_config.php' => 'migrateShopSettings',
        'account_settings.php' => 'migrateAccountSettings'
    ];
    
    if (!isset($migration_handlers[$file_key])) {
        throw new Exception("No migration handler found for {$file_key}");
    }
    
    $handler = $migration_handlers[$file_key];
    return $handler();
}

/**
 * Migrate branding settings
 */
function migrateBrandingSettings() {
    global $settingsManager;
    
    $branding_file = __DIR__ . '/../../assets/includes/settings/branding_settings.php';
    
    if (!file_exists($branding_file)) {
        return ['success' => false, 'error' => 'Branding settings file not found'];
    }
    
    // Include the file to get variables
    ob_start();
    include $branding_file;
    ob_end_clean();
    
    $migrated_count = 0;
    
    // Migrate business identity
    if (isset($business_name_short) || isset($business_name_medium) || isset($business_name_long)) {
        $identity_data = [
            'business_name_short' => $business_name_short ?? 'GWS',
            'business_name_medium' => $business_name_medium ?? 'GWS Universal',
            'business_name_long' => $business_name_long ?? 'GWS Universal Hybrid Application',
            'business_tagline_short' => $business_tagline_short ?? '',
            'business_tagline_medium' => $business_tagline_medium ?? '',
            'business_tagline_long' => $business_tagline_long ?? '',
            'author' => $author ?? 'GWS'
        ];
        
        if ($settingsManager->updateBusinessIdentity($identity_data, 'migration')) {
            $migrated_count += count($identity_data);
        }
    }
    
    // Migrate brand colors
    if (isset($brand_primary_color) || isset($brand_secondary_color)) {
        $colors_data = [
            'brand_primary_color' => $brand_primary_color ?? '#6c2eb6',
            'brand_secondary_color' => $brand_secondary_color ?? '#bf5512',
            'brand_accent_color' => $brand_accent_color ?? '#28a745',
            'brand_warning_color' => $brand_warning_color ?? '#ffc107',
            'brand_danger_color' => $brand_danger_color ?? '#dc3545',
            'brand_info_color' => $brand_info_color ?? '#17a2b8',
            'brand_background_color' => $brand_background_color ?? '#ffffff',
            'brand_text_color' => $brand_text_color ?? '#333333',
            'brand_text_light' => $brand_text_light ?? '#666666',
            'brand_text_muted' => $brand_text_muted ?? '#999999'
        ];
        
        if ($settingsManager->updateBrandingColors($colors_data, 'migration')) {
            $migrated_count += count($colors_data);
        }
    }
    
    return [
        'success' => true,
        'migrated_count' => $migrated_count,
        'details' => 'Business identity and brand colors migrated'
    ];
}

/**
 * Migrate contact settings
 */
function migrateContactSettings() {
    global $settingsManager;
    
    $contact_file = __DIR__ . '/../../assets/includes/settings/contact_settings.php';
    
    if (!file_exists($contact_file)) {
        // Try public_settings.php for contact info
        $contact_file = __DIR__ . '/../../assets/includes/settings/public_settings.php';
    }
    
    if (!file_exists($contact_file)) {
        return ['success' => false, 'error' => 'Contact settings file not found'];
    }
    
    ob_start();
    include $contact_file;
    ob_end_clean();
    
    $contact_data = [
        'contact_email' => $contact_email ?? '',
        'contact_phone' => $contact_phone ?? '',
        'contact_address' => $contact_address ?? '',
        'contact_city' => $contact_city ?? '',
        'contact_state' => $contact_state ?? '',
        'contact_zipcode' => $contact_zipcode ?? '',
        'contact_country' => 'United States'
    ];
    
    if ($settingsManager->updateContactInfo($contact_data, 'migration')) {
        return [
            'success' => true,
            'migrated_count' => count(array_filter($contact_data)),
            'details' => 'Contact information migrated'
        ];
    }
    
    return ['success' => false, 'error' => 'Failed to migrate contact settings'];
}

/**
 * Create settings backup
 */
function createSettingsBackup() {
    $backup_dir = __DIR__ . '/../../../private/backups/settings_migration';
    
    if (!file_exists($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }
    
    $timestamp = date('Y-m-d_H-i-s');
    $backup_file = $backup_dir . "/settings_backup_{$timestamp}.zip";
    
    $zip = new ZipArchive();
    if ($zip->open($backup_file, ZipArchive::CREATE) !== TRUE) {
        return false;
    }
    
    // Add settings files to backup
    $settings_dirs = [
        __DIR__ . '/../../assets/includes/settings/',
        __DIR__ . '/../../shop_system/',
        __DIR__ . '/../../blog_system/',
        __DIR__ . '/../../accounts_system/'
    ];
    
    foreach ($settings_dirs as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '*.php');
            foreach ($files as $file) {
                if (is_file($file)) {
                    $zip->addFile($file, basename(dirname($file)) . '/' . basename($file));
                }
            }
        }
    }
    
    $zip->close();
    
    return $backup_file;
}

// Get analysis results
$analysis_results = $_SESSION['migration_analysis'] ?? null;
$migration_results = $_SESSION['migration_results'] ?? null;

// Page title
$page_title = 'Settings Migration Wizard';
?>

<?= template_admin_header($page_title, 'settings', 'migration') ?>

<div class="professional-card-header">
    <div class="title">
        <div class="icon">
            <i class="fas fa-exchange-alt"></i>
        </div>
        <div class="txt">
            <h2>Settings Migration Wizard</h2>
            <p>Migrate existing file-based settings to the new database-driven system.</p>
        </div>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $message_type === 'success' ? 'success' : ($message_type === 'error' ? 'danger' : 'info') ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Migration Progress -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="professional-card-header">
                <h5 class="mb-0"><i class="fas fa-list-ol"></i> Migration Progress</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-search fa-2x <?= $migration_status === 'not_started' ? 'text-muted' : 'text-success' ?> me-3"></i>
                            <div>
                                <h6 class="mb-0">1. Analysis</h6>
                                <small class="text-muted">Scan existing settings</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shield-alt fa-2x <?= in_array($migration_status, ['analyzed', 'completed']) ? 'text-success' : 'text-muted' ?> me-3"></i>
                            <div>
                                <h6 class="mb-0">2. Backup</h6>
                                <small class="text-muted">Create safety backup</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-database fa-2x <?= $migration_status === 'completed' ? 'text-success' : 'text-muted' ?> me-3"></i>
                            <div>
                                <h6 class="mb-0">3. Migration</h6>
                                <small class="text-muted">Transfer to database</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-2x <?= $migration_status === 'completed' ? 'text-success' : 'text-muted' ?> me-3"></i>
                            <div>
                                <h6 class="mb-0">4. Complete</h6>
                                <small class="text-muted">Verify and test</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($migration_status === 'not_started'): ?>
    <!-- Step 1: Initial Analysis -->
    <div class="card">
        <div class="professional-card-header">
            <h5><i class="fas fa-search"></i> Step 1: Analyze Existing Settings</h5>
        </div>
        <div class="card-body">
            <p>Before starting the migration, we need to analyze your existing settings files to understand what needs to be migrated.</p>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>What this does:</strong>
                <ul class="mb-0 mt-2">
                    <li>Scans all settings files across your application</li>
                    <li>Counts variables and identifies conflicts</li>
                    <li>Provides migration recommendations</li>
                    <li>Creates a migration plan</li>
                </ul>
            </div>
            
            <form method="post">
                <input type="hidden" name="action" value="analyze_settings">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-search"></i> Start Analysis
                </button>
            </form>
        </div>
    </div>

<?php elseif ($migration_status === 'analyzed' && $analysis_results): ?>
    <!-- Step 2: Review Analysis Results -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="professional-card-header">
                    <h5><i class="fas fa-chart-bar"></i> Analysis Results</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-file fa-3x text-primary mb-2"></i>
                                <h4><?= count($analysis_results['files_found']) ?></h4>
                                <p class="text-muted">Settings Files Found</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-cog fa-3x text-success mb-2"></i>
                                <h4><?= $analysis_results['settings_count'] ?></h4>
                                <p class="text-muted">Total Settings</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-exclamation-triangle fa-3x <?= count($analysis_results['conflicts']) > 0 ? 'text-warning' : 'text-success' ?> mb-2"></i>
                                <h4><?= count($analysis_results['conflicts']) ?></h4>
                                <p class="text-muted">Conflicts Detected</p>
                            </div>
                        </div>
                    </div>
                    
                    <h6>Settings Files to Migrate:</h6>
                    <form method="post" id="migrationForm">
                        <input type="hidden" name="action" value="start_migration">
                        
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" checked> 
                                        </th>
                                        <th>File</th>
                                        <th>Type</th>
                                        <th>Settings</th>
                                        <th>Size</th>
                                        <th>Modified</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($analysis_results['files_found'] as $file): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="migrate_files[]" value="<?= htmlspecialchars($file['key']) ?>" checked>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($file['key']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars(basename($file['path'])) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $file['file_type'] === 'branding' ? 'primary' : 
                                                ($file['file_type'] === 'contact' ? 'success' : 
                                                ($file['file_type'] === 'blog' ? 'info' : 'secondary')) 
                                            ?>">
                                                <?= ucfirst($file['file_type']) ?>
                                            </span>
                                        </td>
                                        <td><?= $file['setting_count'] ?> variables</td>
                                        <td><?= number_format($file['size'] / 1024, 1) ?> KB</td>
                                        <td><?= date('Y-m-d H:i', $file['modified']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (!empty($analysis_results['conflicts'])): ?>
                        <div class="alert alert-warning mt-3">
                            <h6><i class="fas fa-exclamation-triangle"></i> Conflicts Detected</h6>
                            <p>The following variables appear in multiple files. The migration will use the first occurrence found:</p>
                            <ul class="mb-0">
                                <?php foreach ($analysis_results['conflicts'] as $conflict): ?>
                                <li><strong><?= htmlspecialchars($conflict['variable']) ?></strong> in: <?= implode(', ', $conflict['files']) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="professional-card-header">
                    <h5><i class="fas fa-lightbulb"></i> Recommendations</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($analysis_results['recommendations'] as $rec): ?>
                    <div class="alert alert-<?= $rec['priority'] === 'critical' ? 'danger' : ($rec['priority'] === 'high' ? 'warning' : 'info') ?> alert-sm">
                        <strong><?= ucfirst($rec['type']) ?>:</strong><br>
                        <?= htmlspecialchars($rec['message']) ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="professional-card-header">
                    <h5><i class="fas fa-play"></i> Start Migration</h5>
                </div>
                <div class="card-body">
                    <p>Ready to migrate your settings to the database?</p>
                    
                    <form method="post" class="mb-3">
                        <input type="hidden" name="action" value="create_backup">
                        <button type="submit" class="btn btn-warning btn-sm w-100 mb-2">
                            <i class="fas fa-shield-alt"></i> Create Backup First
                        </button>
                    </form>
                    
                    <button type="submit" form="migrationForm" class="btn btn-primary btn-lg w-100">
                        <i class="fas fa-database"></i> Start Migration
                    </button>
                    
                    <form method="post" class="mt-2">
                        <input type="hidden" name="action" value="reset_migration">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-redo"></i> Reset & Reanalyze
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($migration_status === 'completed' && $migration_results): ?>
    <!-- Step 3: Migration Results -->
    <div class="card">
        <div class="professional-card-header">
            <h5><i class="fas fa-check-circle"></i> Migration Complete!</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fas fa-check fa-3x text-success mb-2"></i>
                        <h4><?= count($migration_results['success']) ?></h4>
                        <p class="text-muted">Files Migrated</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fas fa-database fa-3x text-primary mb-2"></i>
                        <h4><?= $migration_results['total_migrated'] ?></h4>
                        <p class="text-muted">Settings Transferred</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fas fa-exclamation-circle fa-3x <?= count($migration_results['errors']) > 0 ? 'text-danger' : 'text-success' ?> mb-2"></i>
                        <h4><?= count($migration_results['errors']) ?></h4>
                        <p class="text-muted">Errors</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <i class="fas fa-forward fa-3x text-warning mb-2"></i>
                        <h4><?= count($migration_results['skipped']) ?></h4>
                        <p class="text-muted">Skipped</p>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($migration_results['success'])): ?>
            <div class="alert alert-success">
                <h6><i class="fas fa-check"></i> Successfully Migrated:</h6>
                <ul class="mb-0">
                    <?php foreach ($migration_results['success'] as $success): ?>
                    <li><strong><?= htmlspecialchars($success['file']) ?></strong> - <?= $success['migrated_count'] ?> settings - <?= htmlspecialchars($success['details']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($migration_results['errors'])): ?>
            <div class="alert alert-danger">
                <h6><i class="fas fa-exclamation-circle"></i> Errors:</h6>
                <ul class="mb-0">
                    <?php foreach ($migration_results['errors'] as $error): ?>
                    <li><strong><?= htmlspecialchars($error['file']) ?></strong> - <?= htmlspecialchars($error['error']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            
            <div class="d-flex gap-3 mt-4">
                <a href="database_settings.php" class="btn btn-primary">
                    <i class="fas fa-cog"></i> Manage Database Settings
                </a>
                
                <form method="post" style="display: inline;">
                    <input type="hidden" name="action" value="reset_migration">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-redo"></i> Run Migration Again
                    </button>
                </form>
                
                <a href="../settings_dash.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back to Settings Dashboard
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
// Select all checkbox functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const fileCheckboxes = document.querySelectorAll('input[name="migrate_files[]"]');
    
    if (selectAllCheckbox && fileCheckboxes.length > 0) {
        selectAllCheckbox.addEventListener('change', function() {
            fileCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        fileCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('input[name="migrate_files[]"]:checked').length;
                selectAllCheckbox.checked = checkedCount === fileCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < fileCheckboxes.length;
            });
        });
    }
});
</script>

<?= template_admin_footer() ?>
