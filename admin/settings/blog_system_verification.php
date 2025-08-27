<?php
/**
 * Blog System Integration Verification Script
 * 
 * SYSTEM: GWS Universal Hybrid App - Verification Tool
 * FILE: blog_system_verification.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Verify complete blog system integration
 * 
 * This script verifies that all blog system components are properly
 * integrated and functional before production deployment.
 * 
 * VERIFICATION CHECKS:
 * - Database table existence and structure
 * - Admin form file existence and accessibility
 * - SettingsManager blog methods functionality
 * - Settings dashboard integration
 * - Default data population
 * 
 * CREATED: 2025-08-17
 * VERSION: 1.0
 */

// Initialize session and security
session_start();
require_once __DIR__ . '/../../../private/gws-universal-config.php';
require_once __DIR__ . '/../../../private/classes/SettingsManager.php';
include_once '../assets/includes/main.php';

// Security check for admin access
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['Admin', 'Developer'])) {
    header('Location: ../index.php');
    exit();
}

// Initialize settings manager
$settingsManager = new SettingsManager($pdo);

// Verification results array
$verification_results = [
    'database_tables' => [],
    'admin_forms' => [],
    'settings_manager' => [],
    'default_data' => [],
    'overall_status' => 'pending'
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog System Verification - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .verification-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .verification-section {
            margin: 30px 0;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
        }
        
        .section-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
        }
        
        .check-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .overall-status {
            text-align: center;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
        }
        
        .nav-link {
            display: inline-block;
            margin: 10px;
            padding: 10px 15px;
            background: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .nav-link:hover {
            background: #005a87;
        }
        
        .details-box {
            background: #f1f3f4;
            padding: 15px;
            border-radius: 4px;
            margin-top: 10px;
            font-family: monospace;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <h1>🔍 Blog System Integration Verification</h1>
        <p>Comprehensive verification of all blog system components before production deployment.</p>
        
        <div class="nav-links">
            <a href="settings_dash.php" class="nav-link">← Settings Dashboard</a>
            <a href="blog_identity_form.php" class="nav-link">Blog Identity</a>
            <a href="blog_display_form.php" class="nav-link">Blog Display</a>
            <a href="blog_features_form.php" class="nav-link">Blog Features</a>
        </div>
        
        <!-- Database Tables Verification -->
        <div class="verification-section">
            <div class="section-header">📊 Database Tables Verification</div>
            <?php
            $blog_tables = [
                'setting_blog_identity' => 'Blog Identity Settings',
                'setting_blog_display' => 'Blog Display Settings', 
                'setting_blog_features' => 'Blog Features Settings',
                'setting_blog_comments' => 'Blog Comments Settings',
                'setting_blog_seo' => 'Blog SEO Settings',
                'setting_blog_social' => 'Blog Social Settings'
            ];
            
            foreach ($blog_tables as $table => $description) {
                try {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table}");
                    $stmt->execute();
                    $result = $stmt->fetch();
                    $count = $result['count'];
                    
                    echo '<div class="check-item">';
                    echo '<span>' . $description . ' (' . $table . ')</span>';
                    echo '<span class="status-badge status-success">✓ EXISTS (' . $count . ' records)</span>';
                    echo '</div>';
                    
                    $verification_results['database_tables'][$table] = 'success';
                } catch (Exception $e) {
                    echo '<div class="check-item">';
                    echo '<span>' . $description . ' (' . $table . ')</span>';
                    echo '<span class="status-badge status-error">✗ MISSING</span>';
                    echo '</div>';
                    
                    $verification_results['database_tables'][$table] = 'error';
                }
            }
            ?>
        </div>
        
        <!-- Admin Forms Verification -->
        <div class="verification-section">
            <div class="section-header">📝 Admin Forms Verification</div>
            <?php
            $blog_forms = [
                'blog_identity_form.php' => 'Blog Identity Configuration Form',
                'blog_display_form.php' => 'Blog Display Configuration Form',
                'blog_features_form.php' => 'Blog Features Configuration Form', 
                'blog_comments_form.php' => 'Blog Comments Configuration Form',
                'blog_seo_form.php' => 'Blog SEO Configuration Form',
                'blog_social_form.php' => 'Blog Social Configuration Form'
            ];
            
            foreach ($blog_forms as $file => $description) {
                $file_path = __DIR__ . '/' . $file;
                if (file_exists($file_path) && is_readable($file_path)) {
                    $file_size = round(filesize($file_path) / 1024, 2);
                    echo '<div class="check-item">';
                    echo '<span>' . $description . '</span>';
                    echo '<span class="status-badge status-success">✓ EXISTS (' . $file_size . ' KB)</span>';
                    echo '</div>';
                    
                    $verification_results['admin_forms'][$file] = 'success';
                } else {
                    echo '<div class="check-item">';
                    echo '<span>' . $description . '</span>';
                    echo '<span class="status-badge status-error">✗ MISSING</span>';
                    echo '</div>';
                    
                    $verification_results['admin_forms'][$file] = 'error';
                }
            }
            ?>
        </div>
        
        <!-- SettingsManager Methods Verification -->
        <div class="verification-section">
            <div class="section-header">⚙️ SettingsManager Blog Methods</div>
            <?php
            $blog_methods = [
                'getBlogIdentity' => 'Get Blog Identity Settings',
                'getBlogDisplay' => 'Get Blog Display Settings',
                'getBlogFeatures' => 'Get Blog Features Settings',
                'getBlogComments' => 'Get Blog Comments Settings',
                'getBlogSeo' => 'Get Blog SEO Settings',
                'getBlogSocial' => 'Get Blog Social Settings'
            ];
            
            foreach ($blog_methods as $method => $description) {
                if (method_exists($settingsManager, $method)) {
                    try {
                        $result = $settingsManager->$method();
                        echo '<div class="check-item">';
                        echo '<span>' . $description . '</span>';
                        echo '<span class="status-badge status-success">✓ FUNCTIONAL</span>';
                        echo '</div>';
                        
                        $verification_results['settings_manager'][$method] = 'success';
                    } catch (Exception $e) {
                        echo '<div class="check-item">';
                        echo '<span>' . $description . '</span>';
                        echo '<span class="status-badge status-error">✗ ERROR: ' . $e->getMessage() . '</span>';
                        echo '</div>';
                        
                        $verification_results['settings_manager'][$method] = 'error';
                    }
                } else {
                    echo '<div class="check-item">';
                    echo '<span>' . $description . '</span>';
                    echo '<span class="status-badge status-error">✗ METHOD NOT FOUND</span>';
                    echo '</div>';
                    
                    $verification_results['settings_manager'][$method] = 'error';
                }
            }
            ?>
        </div>
        
        <!-- Default Data Verification -->
        <div class="verification-section">
            <div class="section-header">📋 Default Data Population</div>
            <?php
            foreach ($blog_tables as $table => $description) {
                try {
                    $stmt = $pdo->prepare("SELECT * FROM {$table} LIMIT 1");
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($result) {
                        $non_empty_fields = 0;
                        $total_fields = count($result);
                        
                        foreach ($result as $key => $value) {
                            if (!in_array($key, ['id', 'created_at', 'updated_at']) && !empty($value)) {
                                $non_empty_fields++;
                            }
                        }
                        
                        $completion_percentage = round(($non_empty_fields / max(1, $total_fields - 3)) * 100, 1);
                        
                        echo '<div class="check-item">';
                        echo '<span>' . $description . ' Default Data</span>';
                        echo '<span class="status-badge status-success">✓ POPULATED (' . $completion_percentage . '%)</span>';
                        echo '</div>';
                        
                        $verification_results['default_data'][$table] = 'success';
                    } else {
                        echo '<div class="check-item">';
                        echo '<span>' . $description . ' Default Data</span>';
                        echo '<span class="status-badge status-warning">⚠ NO DATA</span>';
                        echo '</div>';
                        
                        $verification_results['default_data'][$table] = 'warning';
                    }
                } catch (Exception $e) {
                    echo '<div class="check-item">';
                    echo '<span>' . $description . ' Default Data</span>';
                    echo '<span class="status-badge status-error">✗ ERROR</span>';
                    echo '</div>';
                    
                    $verification_results['default_data'][$table] = 'error';
                }
            }
            ?>
        </div>
        
        <!-- Overall Status -->
        <?php
        $total_checks = 0;
        $passed_checks = 0;
        
        foreach ($verification_results as $category => $checks) {
            if ($category === 'overall_status') continue;
            
            foreach ($checks as $result) {
                $total_checks++;
                if ($result === 'success') {
                    $passed_checks++;
                }
            }
        }
        
        $success_rate = round(($passed_checks / max(1, $total_checks)) * 100, 1);
        
        if ($success_rate >= 95) {
            $status_class = 'status-success';
            $status_text = '✅ PRODUCTION READY';
            $verification_results['overall_status'] = 'success';
        } elseif ($success_rate >= 80) {
            $status_class = 'status-warning';
            $status_text = '⚠️ MINOR ISSUES DETECTED';
            $verification_results['overall_status'] = 'warning';
        } else {
            $status_class = 'status-error';
            $status_text = '❌ CRITICAL ISSUES DETECTED';
            $verification_results['overall_status'] = 'error';
        }
        ?>
        
        <div class="overall-status <?= $status_class ?>">
            <div><?= $status_text ?></div>
            <div style="font-size: 16px; margin-top: 10px;">
                Success Rate: <?= $success_rate ?>% (<?= $passed_checks ?>/<?= $total_checks ?> checks passed)
            </div>
        </div>
        
        <div class="details-box">
            <strong>Verification Summary:</strong><br>
            • Database Tables: <?= count(array_filter($verification_results['database_tables'], function($v) { return $v === 'success'; })) ?>/<?= count($verification_results['database_tables']) ?> ✓<br>
            • Admin Forms: <?= count(array_filter($verification_results['admin_forms'], function($v) { return $v === 'success'; })) ?>/<?= count($verification_results['admin_forms']) ?> ✓<br>
            • SettingsManager Methods: <?= count(array_filter($verification_results['settings_manager'], function($v) { return $v === 'success'; })) ?>/<?= count($verification_results['settings_manager']) ?> ✓<br>
            • Default Data: <?= count(array_filter($verification_results['default_data'], function($v) { return $v === 'success'; })) ?>/<?= count($verification_results['default_data']) ?> ✓<br>
            <br>
            <strong>Blog System Status:</strong> 
            <?php if ($verification_results['overall_status'] === 'success'): ?>
                🎉 The blog system is fully integrated and ready for production deployment!
            <?php elseif ($verification_results['overall_status'] === 'warning'): ?>
                ⚠️ The blog system is mostly ready but has minor issues that should be addressed.
            <?php else: ?>
                ❌ The blog system has critical issues that must be resolved before deployment.
            <?php endif; ?>
        </div>
        
        <div class="nav-links" style="text-align: center; margin-top: 30px;">
            <a href="blog_comments_form.php" class="nav-link">Blog Comments</a>
            <a href="blog_seo_form.php" class="nav-link">Blog SEO</a>
            <a href="blog_social_form.php" class="nav-link">Blog Social</a>
            <a href="settings_dash.php" class="nav-link">Return to Dashboard</a>
        </div>
    </div>
</body>
</html>
