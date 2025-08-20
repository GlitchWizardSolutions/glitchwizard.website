<?php
/**
 * Blog Identity Settings Form
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel  
 * FILE: blog_identity_form.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Blog identity and basic information management
 * 
 * Manages blog title, description, author information, and meta data
 * through the unified database settings system.
 * 
 * FEATURES:
 * - Blog title and tagline configuration
 * - Author information management
 * - Meta description and keywords
 * - Email and URL settings
 * - Copyright text configuration
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
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['Admin', 'Editor', 'Developer'])) {
    header('Location: ../index.php');
    exit();
}

// Initialize settings manager
$settingsManager = new SettingsManager($pdo);

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_by = $account_loggedin['username'] ?? 'admin';
    
    try {
        $data = [
            'blog_title' => sanitize_input($_POST['blog_title']),
            'blog_description' => sanitize_input($_POST['blog_description']),
            'blog_tagline' => sanitize_input($_POST['blog_tagline']),
            'author_name' => sanitize_input($_POST['author_name']),
            'author_bio' => sanitize_input($_POST['author_bio']),
            'default_author_id' => (int)$_POST['default_author_id'],'blog_email' => sanitize_input($_POST['blog_email']),
            'blog_url' => sanitize_input($_POST['blog_url']),];
        
        // Validate required fields
        if (empty($data['blog_title'])) {
            throw new Exception('Blog title is required');
        }
        
        if (!empty($data['blog_email']) && !filter_var($data['blog_email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        
        if (!empty($data['blog_url']) && !filter_var($data['blog_url'], FILTER_VALIDATE_URL)) {
            throw new Exception('Invalid URL format');
        }
        
        $result = $settingsManager->updateBlogIdentity($data, $updated_by);
        
        if ($result) {
            $message = 'Blog identity settings updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to update blog identity settings.';
            $message_type = 'error';
        }
        
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Load current settings
$current_settings = $settingsManager->getBlogIdentity();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Identity Settings - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .settings-form {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .form-group small {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        
        .submit-btn {
            background: #007cba;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .submit-btn:hover {
            background: #005a87;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .nav-links {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .nav-links a {
            margin-right: 15px;
            color: #007cba;
            text-decoration: none;
        }
        
        .nav-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <button type="submit" class="submit-btn">Update Blog Identity Settings</button>
        </form>
    </div>
</body>
</html>
