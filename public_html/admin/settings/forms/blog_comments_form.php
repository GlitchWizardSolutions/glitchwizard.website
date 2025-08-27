<?php
/**
 * Blog Comments Settings Form
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel  
 * FILE: blog_comments_form.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Blog comment system configuration and management
 * 
 * Manages all aspects of the blog comment system including moderation,
 * threading, notifications, and third-party integrations.
 * 
 * FEATURES:
 * - Comment system selection (internal/Disqus/Facebook)
 * - Moderation and approval settings
 * - Threading and depth controls
 * - Notification configuration
 * - Spam protection settings
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
            'comment_system' => sanitize_input($_POST['comment_system']),
            'require_approval' => isset($_POST['require_approval']) ? 1 : 0,
            'allow_guest_comments' => isset($_POST['allow_guest_comments']) ? 1 : 0,
            'require_registration' => isset($_POST['require_registration']) ? 1 : 0,
            'max_comment_length' => (int)$_POST['max_comment_length'],
            'enable_notifications' => isset($_POST['enable_notifications']) ? 1 : 0,
            'notification_email' => sanitize_input($_POST['notification_email']),
            'enable_threading' => isset($_POST['enable_threading']) ? 1 : 0,
            'max_thread_depth' => (int)$_POST['max_thread_depth'],
            'enable_comment_voting' => isset($_POST['enable_comment_voting']) ? 1 : 0,
            'enable_comment_editing' => isset($_POST['enable_comment_editing']) ? 1 : 0,
            'comment_edit_time_limit' => (int)$_POST['comment_edit_time_limit'],
            'enable_comment_deletion' => isset($_POST['enable_comment_deletion']) ? 1 : 0,
            'enable_spam_protection' => isset($_POST['enable_spam_protection']) ? 1 : 0,
            'disqus_shortname' => sanitize_input($_POST['disqus_shortname']),
            'facebook_app_id' => sanitize_input($_POST['facebook_app_id'])
        ];
        
        // Validate email
        if (!empty($data['notification_email']) && !filter_var($data['notification_email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid notification email format');
        }
        
        // Validate numeric fields
        if ($data['max_comment_length'] < 50) $data['max_comment_length'] = 1000;
        if ($data['max_thread_depth'] < 1) $data['max_thread_depth'] = 3;
        if ($data['comment_edit_time_limit'] < 60) $data['comment_edit_time_limit'] = 300;
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM setting_blog_comments LIMIT 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing record
            $sql = "UPDATE setting_blog_comments SET 
                    comment_system = ?, require_approval = ?, allow_guest_comments = ?, require_registration = ?,
                    max_comment_length = ?, enable_notifications = ?, notification_email = ?, enable_threading = ?,
                    max_thread_depth = ?, enable_comment_voting = ?, enable_comment_editing = ?, comment_edit_time_limit = ?,
                    enable_comment_deletion = ?, enable_spam_protection = ?, disqus_shortname = ?, facebook_app_id = ?,
                    updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $data['comment_system'], $data['require_approval'], $data['allow_guest_comments'], $data['require_registration'],
                $data['max_comment_length'], $data['enable_notifications'], $data['notification_email'], $data['enable_threading'],
                $data['max_thread_depth'], $data['enable_comment_voting'], $data['enable_comment_editing'], $data['comment_edit_time_limit'],
                $data['enable_comment_deletion'], $data['enable_spam_protection'], $data['disqus_shortname'], $data['facebook_app_id'],
                $exists['id']
            ]);
        } else {
            // Insert new record
            $sql = "INSERT INTO setting_blog_comments 
                    (comment_system, require_approval, allow_guest_comments, require_registration, max_comment_length,
                     enable_notifications, notification_email, enable_threading, max_thread_depth, enable_comment_voting,
                     enable_comment_editing, comment_edit_time_limit, enable_comment_deletion, enable_spam_protection,
                     disqus_shortname, facebook_app_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $data['comment_system'], $data['require_approval'], $data['allow_guest_comments'], $data['require_registration'],
                $data['max_comment_length'], $data['enable_notifications'], $data['notification_email'], $data['enable_threading'],
                $data['max_thread_depth'], $data['enable_comment_voting'], $data['enable_comment_editing'], $data['comment_edit_time_limit'],
                $data['enable_comment_deletion'], $data['enable_spam_protection'], $data['disqus_shortname'], $data['facebook_app_id']
            ]);
        }
        
        if ($result) {
            $message = 'Blog comments settings updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to update blog comments settings.';
            $message_type = 'error';
        }
        
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Load current settings
$current_settings = $settingsManager->getBlogComments();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Comments Settings - Admin Panel</title>
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
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
            flex: 1;
        }
        
        .form-group.full-width {
            flex: 100%;
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
        
        .form-group small {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        
        .checkbox-item input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
        
        .checkbox-item label {
            margin: 0;
            font-weight: normal;
            cursor: pointer;
            flex: 1;
        }
        
        .section-header {
            background: #007cba;
            color: white;
            padding: 15px;
            margin: 30px 0 20px 0;
            border-radius: 6px;
            font-size: 18px;
            font-weight: bold;
        }
        
        .third-party-section {
            background: #f1f3f4;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #007cba;
        }
        
        .submit-btn {
            background: #007cba;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
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
    <div class="settings-form">
        <div class="nav-links">
            <a href="blog_features_form.php">← Blog Features</a>
            <a href="settings_dash.php">Settings Dashboard</a>
            <a href="blog_seo_form.php">Blog SEO →</a>
        </div>
        
        <h1>Blog Comments Settings</h1>
        <p>Configure how comments work on your blog, including moderation, threading, and third-party integrations.</p>
        
        <?php if ($message): ?>
            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="professional-section-header">Comment System Configuration</div>
            
            <div class="form-group">
                <label for="comment_system">Comment System</label>
                <select id="comment_system" name="comment_system" onchange="toggleCommentSections(this.value)">
                    <option value="internal" <?= ($current_settings['comment_system'] ?? 'internal') === 'internal' ? 'selected' : '' ?>>Internal Comment System</option>
                    <option value="disqus" <?= ($current_settings['comment_system'] ?? '') === 'disqus' ? 'selected' : '' ?>>Disqus Comments</option>
                    <option value="facebook" <?= ($current_settings['comment_system'] ?? '') === 'facebook' ? 'selected' : '' ?>>Facebook Comments</option>
                    <option value="disabled" <?= ($current_settings['comment_system'] ?? '') === 'disabled' ? 'selected' : '' ?>>Disable Comments</option>
                </select>
                <small>Choose which comment system to use for your blog</small>
            </div>
            
            <div id="internal-settings" style="<?= ($current_settings['comment_system'] ?? 'internal') === 'internal' ? '' : 'display: none;' ?>">
                <div class="professional-section-header">Internal Comment System Settings</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="max_comment_length">Maximum Comment Length</label>
                        <input type="number" id="max_comment_length" name="max_comment_length" 
                               value="<?= htmlspecialchars($current_settings['max_comment_length'] ?? '1000') ?>" min="50" max="5000">
                        <small>Maximum number of characters allowed in comments</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="max_thread_depth">Thread Depth</label>
                        <input type="number" id="max_thread_depth" name="max_thread_depth" 
                               value="<?= htmlspecialchars($current_settings['max_thread_depth'] ?? '3') ?>" min="1" max="10">
                        <small>Maximum depth for comment threading/replies</small>
                    </div>
                </div>
                
                <div class="checkbox-item">
                    <input type="checkbox" id="require_approval" name="require_approval" 
                           <?= ($current_settings['require_approval'] ?? 1) ? 'checked' : '' ?>>
                    <label for="require_approval">
                        Require Approval for Comments
                        <small>Comments must be approved by an administrator before appearing</small>
                    </label>
                </div>
                
                <div class="checkbox-item">
                    <input type="checkbox" id="allow_guest_comments" name="allow_guest_comments" 
                           <?= ($current_settings['allow_guest_comments'] ?? 1) ? 'checked' : '' ?>>
                    <label for="allow_guest_comments">
                        Allow Guest Comments
                        <small>Allow visitors to comment without registering an account</small>
                    </label>
                </div>
                
                <div class="checkbox-item">
                    <input type="checkbox" id="require_registration" name="require_registration" 
                           <?= ($current_settings['require_registration'] ?? 0) ? 'checked' : '' ?>>
                    <label for="require_registration">
                        Require Registration
                        <small>Users must be registered and logged in to comment</small>
                    </label>
                </div>
                
                <div class="checkbox-item">
                    <input type="checkbox" id="enable_threading" name="enable_threading" 
                           <?= ($current_settings['enable_threading'] ?? 1) ? 'checked' : '' ?>>
                    <label for="enable_threading">
                        Enable Comment Threading
                        <small>Allow users to reply to specific comments</small>
                    </label>
                </div>
                
                <div class="checkbox-item">
                    <input type="checkbox" id="enable_comment_voting" name="enable_comment_voting" 
                           <?= ($current_settings['enable_comment_voting'] ?? 0) ? 'checked' : '' ?>>
                    <label for="enable_comment_voting">
                        Enable Comment Voting
                        <small>Allow users to upvote/downvote comments</small>
                    </label>
                </div>
                
                <div class="checkbox-item">
                    <input type="checkbox" id="enable_comment_editing" name="enable_comment_editing" 
                           <?= ($current_settings['enable_comment_editing'] ?? 1) ? 'checked' : '' ?>>
                    <label for="enable_comment_editing">
                        Allow Comment Editing
                        <small>Users can edit their own comments within a time limit</small>
                    </label>
                </div>
                
                <div class="form-group">
                    <label for="comment_edit_time_limit">Edit Time Limit (seconds)</label>
                    <input type="number" id="comment_edit_time_limit" name="comment_edit_time_limit" 
                           value="<?= htmlspecialchars($current_settings['comment_edit_time_limit'] ?? '300') ?>" min="60" max="3600">
                    <small>How long users can edit their comments after posting (in seconds)</small>
                </div>
                
                <div class="checkbox-item">
                    <input type="checkbox" id="enable_comment_deletion" name="enable_comment_deletion" 
                           <?= ($current_settings['enable_comment_deletion'] ?? 1) ? 'checked' : '' ?>>
                    <label for="enable_comment_deletion">
                        Allow Comment Deletion
                        <small>Users can delete their own comments</small>
                    </label>
                </div>
                
                <div class="checkbox-item">
                    <input type="checkbox" id="enable_spam_protection" name="enable_spam_protection" 
                           <?= ($current_settings['enable_spam_protection'] ?? 1) ? 'checked' : '' ?>>
                    <label for="enable_spam_protection">
                        Enable Spam Protection
                        <small>Use built-in spam detection and protection measures</small>
                    </label>
                </div>
            </div>
            
            <div class="professional-section-header">Notification Settings</div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="enable_notifications" name="enable_notifications" 
                       <?= ($current_settings['enable_notifications'] ?? 1) ? 'checked' : '' ?>>
                <label for="enable_notifications">
                    Enable Comment Notifications
                    <small>Send email notifications when new comments are posted</small>
                </label>
            </div>
            
            <div class="form-group">
                <label for="notification_email">Notification Email</label>
                <input type="email" id="notification_email" name="notification_email" 
                       value="<?= htmlspecialchars($current_settings['notification_email'] ?? '') ?>">
                <small>Email address to receive comment notifications</small>
            </div>
            
            <div id="disqus-settings" class="third-party-section" style="<?= ($current_settings['comment_system'] ?? '') === 'disqus' ? '' : 'display: none;' ?>">
                <h3>Disqus Integration</h3>
                <div class="form-group">
                    <label for="disqus_shortname">Disqus Shortname</label>
                    <input type="text" id="disqus_shortname" name="disqus_shortname" 
                           value="<?= htmlspecialchars($current_settings['disqus_shortname'] ?? '') ?>">
                    <small>Your Disqus site shortname (found in your Disqus admin panel)</small>
                </div>
            </div>
            
            <div id="facebook-settings" class="third-party-section" style="<?= ($current_settings['comment_system'] ?? '') === 'facebook' ? '' : 'display: none;' ?>">
                <h3>Facebook Comments Integration</h3>
                <div class="form-group">
                    <label for="facebook_app_id">Facebook App ID</label>
                    <input type="text" id="facebook_app_id" name="facebook_app_id" 
                           value="<?= htmlspecialchars($current_settings['facebook_app_id'] ?? '') ?>">
                    <small>Your Facebook App ID for Facebook Comments integration</small>
                </div>
            </div>
            
            <button type="submit" class="submit-btn">Update Blog Comments Settings</button>
        </form>
    </div>
    
    <script>
        function toggleCommentSections(system) {
            const internalSettings = document.getElementById('internal-settings');
            const disqusSettings = document.getElementById('disqus-settings');
            const facebookSettings = document.getElementById('facebook-settings');
            
            // Hide all sections
            internalSettings.style.display = 'none';
            disqusSettings.style.display = 'none';
            facebookSettings.style.display = 'none';
            
            // Show relevant section
            switch(system) {
                case 'internal':
                    internalSettings.style.display = 'block';
                    break;
                case 'disqus':
                    disqusSettings.style.display = 'block';
                    break;
                case 'facebook':
                    facebookSettings.style.display = 'block';
                    break;
            }
        }
    </script>
</body>
</html>
