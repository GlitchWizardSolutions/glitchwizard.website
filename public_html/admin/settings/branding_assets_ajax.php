<?php
/**
 * Branding Assets AJAX Handler
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: branding_assets_ajax.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Handle AJAX requests for logo upload, selection, and management
 * 
 * CREATED: 2025-08-17
 * VERSION: 1.0
 */

// Start session and include required files
session_start();
require_once '../../../private/gws-universal-config.php';
require_once '../../../private/gws-universal-functions.php';
require_once '../auth/admin-auth.php';
require_once 'branding_assets_manager.php';

// Ensure user is authenticated and has admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

// Set JSON response header
header('Content-Type: application/json');

try {
    // Get database connection
    $pdo = new PDO($dsn, $username, $password, $pdo_options);
    $assets_manager = new BrandingAssetsManager($pdo);
    
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'upload':
            handleUpload($assets_manager);
            break;
            
        case 'get_existing':
            handleGetExisting($assets_manager);
            break;
            
        case 'select_existing':
            handleSelectExisting($assets_manager);
            break;
            
        case 'delete':
            handleDelete($assets_manager);
            break;
            
        case 'get_current':
            handleGetCurrent($assets_manager);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    error_log("Branding Assets AJAX Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

/**
 * Handle file upload
 */
function handleUpload($assets_manager) {
    if (!isset($_FILES['logo_file']) || !isset($_POST['logo_type'])) {
        throw new Exception('Missing required parameters');
    }
    
    $logo_type = $_POST['logo_type'];
    $custom_name = $_POST['custom_name'] ?? null;
    
    $result = $assets_manager->uploadLogo($_FILES['logo_file'], $logo_type, $custom_name);
    echo json_encode($result);
}

/**
 * Get existing logos
 */
function handleGetExisting($assets_manager) {
    $logos = $assets_manager->getExistingLogos();
    echo json_encode(['success' => true, 'logos' => $logos]);
}

/**
 * Select existing logo for a specific type
 */
function handleSelectExisting($assets_manager) {
    if (!isset($_POST['filename']) || !isset($_POST['logo_type'])) {
        throw new Exception('Missing required parameters');
    }
    
    $filename = $_POST['filename'];
    $logo_type = $_POST['logo_type'];
    
    // Update database with selected logo
    $result = $assets_manager->updateDatabaseAsset($logo_type, $filename);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'message' => ucfirst(str_replace('_', ' ', $logo_type)) . ' updated successfully',
            'url' => '/assets/branding/' . $filename
        ]);
    } else {
        echo json_encode($result);
    }
}

/**
 * Delete logo
 */
function handleDelete($assets_manager) {
    if (!isset($_POST['filename'])) {
        throw new Exception('Missing filename parameter');
    }
    
    $filename = $_POST['filename'];
    $logo_type = $_POST['logo_type'] ?? null;
    
    $result = $assets_manager->deleteLogo($filename, $logo_type);
    echo json_encode($result);
}

/**
 * Get current assets
 */
function handleGetCurrent($assets_manager) {
    $assets = $assets_manager->getCurrentAssets();
    echo json_encode(['success' => true, 'assets' => $assets]);
}
?>
