<?php
/**
 * Blog Social Settings Form
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel  
 * FILE: blog_social_form.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Blog social media integration configuration
 * 
 * Manages all social media integration settings for the blog including
 * sharing buttons, social logins, auto-posting, and platform integrations.
 * 
 * FEATURES:
 * - Social sharing buttons configuration
 * - Auto-posting to social platforms
 * - Social media platform connections
 * - Open Graph and Twitter Card settings
 * - Social login integration
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
            'enable_sharing_buttons' => isset($_POST['enable_sharing_buttons']) ? 1 : 0,
            'sharing_platforms' => is_array($_POST['sharing_platforms']) ? implode(',', $_POST['sharing_platforms']) : '',
            'sharing_button_style' => sanitize_input($_POST['sharing_button_style']),
            'sharing_button_position' => sanitize_input($_POST['sharing_button_position']),
            'enable_social_login' => isset($_POST['enable_social_login']) ? 1 : 0,
            'facebook_app_id' => sanitize_input($_POST['facebook_app_id']),
            'facebook_app_secret' => sanitize_input($_POST['facebook_app_secret']),
            'twitter_api_key' => sanitize_input($_POST['twitter_api_key']),
            'twitter_api_secret' => sanitize_input($_POST['twitter_api_secret']),
            'google_client_id' => sanitize_input($_POST['google_client_id']),
            'google_client_secret' => sanitize_input($_POST['google_client_secret']),
            'enable_auto_posting' => isset($_POST['enable_auto_posting']) ? 1 : 0,
            'auto_post_platforms' => is_array($_POST['auto_post_platforms']) ? implode(',', $_POST['auto_post_platforms']) : '',
            'default_hashtags' => sanitize_input($_POST['default_hashtags']),
            'social_image_size' => sanitize_input($_POST['social_image_size']),
            'enable_social_meta' => isset($_POST['enable_social_meta']) ? 1 : 0,
            'twitter_username' => sanitize_input($_POST['twitter_username']),
            'facebook_page_url' => sanitize_input($_POST['facebook_page_url']),
            'instagram_username' => sanitize_input($_POST['instagram_username']),
            'linkedin_company_id' => sanitize_input($_POST['linkedin_company_id']),
            'pinterest_username' => sanitize_input($_POST['pinterest_username']),
            'enable_follow_buttons' => isset($_POST['enable_follow_buttons']) ? 1 : 0,
            'follow_button_style' => sanitize_input($_POST['follow_button_style'])
        ];
        
        // Validate URLs if provided
        $url_fields = ['facebook_page_url'];
        foreach ($url_fields as $field) {
            if (!empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_URL)) {
                throw new Exception("Invalid URL for {$field}");
            }
        }
        
        // Validate usernames (remove @ if present)
        $username_fields = ['twitter_username', 'instagram_username', 'pinterest_username'];
        foreach ($username_fields as $field) {
            if (!empty($data[$field])) {
                $data[$field] = ltrim($data[$field], '@');
            }
        }
        
        // Check if record exists
        $stmt = $pdo->prepare("SELECT id FROM setting_blog_social LIMIT 1");
        $stmt->execute();
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing record
            $sql = "UPDATE setting_blog_social SET 
                    enable_sharing_buttons = ?, sharing_platforms = ?, sharing_button_style = ?, sharing_button_position = ?,
                    enable_social_login = ?, facebook_app_id = ?, facebook_app_secret = ?, twitter_api_key = ?,
                    twitter_api_secret = ?, google_client_id = ?, google_client_secret = ?, enable_auto_posting = ?,
                    auto_post_platforms = ?, default_hashtags = ?, social_image_size = ?, enable_social_meta = ?,
                    twitter_username = ?, facebook_page_url = ?, instagram_username = ?, linkedin_company_id = ?,
                    pinterest_username = ?, enable_follow_buttons = ?, follow_button_style = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $data['enable_sharing_buttons'], $data['sharing_platforms'], $data['sharing_button_style'], $data['sharing_button_position'],
                $data['enable_social_login'], $data['facebook_app_id'], $data['facebook_app_secret'], $data['twitter_api_key'],
                $data['twitter_api_secret'], $data['google_client_id'], $data['google_client_secret'], $data['enable_auto_posting'],
                $data['auto_post_platforms'], $data['default_hashtags'], $data['social_image_size'], $data['enable_social_meta'],
                $data['twitter_username'], $data['facebook_page_url'], $data['instagram_username'], $data['linkedin_company_id'],
                $data['pinterest_username'], $data['enable_follow_buttons'], $data['follow_button_style'], $exists['id']
            ]);
        } else {
            // Insert new record
            $sql = "INSERT INTO setting_blog_social 
                    (enable_sharing_buttons, sharing_platforms, sharing_button_style, sharing_button_position, enable_social_login,
                     facebook_app_id, facebook_app_secret, twitter_api_key, twitter_api_secret, google_client_id,
                     google_client_secret, enable_auto_posting, auto_post_platforms, default_hashtags, social_image_size,
                     enable_social_meta, twitter_username, facebook_page_url, instagram_username, linkedin_company_id,
                     pinterest_username, enable_follow_buttons, follow_button_style) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $data['enable_sharing_buttons'], $data['sharing_platforms'], $data['sharing_button_style'], $data['sharing_button_position'],
                $data['enable_social_login'], $data['facebook_app_id'], $data['facebook_app_secret'], $data['twitter_api_key'],
                $data['twitter_api_secret'], $data['google_client_id'], $data['google_client_secret'], $data['enable_auto_posting'],
                $data['auto_post_platforms'], $data['default_hashtags'], $data['social_image_size'], $data['enable_social_meta'],
                $data['twitter_username'], $data['facebook_page_url'], $data['instagram_username'], $data['linkedin_company_id'],
                $data['pinterest_username'], $data['enable_follow_buttons'], $data['follow_button_style']
            ]);
        }
        
        if ($result) {
            $message = 'Blog social settings updated successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to update blog social settings.';
            $message_type = 'error';
        }
        
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Load current settings
$current_settings = $settingsManager->getBlogSocial();

// Parse sharing platforms
$sharing_platforms = !empty($current_settings['sharing_platforms']) ? explode(',', $current_settings['sharing_platforms']) : ['facebook', 'twitter', 'linkedin'];
$auto_post_platforms = !empty($current_settings['auto_post_platforms']) ? explode(',', $current_settings['auto_post_platforms']) : [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Social Settings - Admin Panel</title>
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
        
        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .checkbox-grid .checkbox-item {
            margin-bottom: 0;
            padding: 8px 12px;
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
        
        .api-section {
            background: #fff3cd;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        
        .social-platform {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }
        
        .platform-header {
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
            font-size: 16px;
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
            <a href="blog_seo_form.php">← Blog SEO</a>
            <a href="settings_dash.php">Settings Dashboard</a>
            <a href="blog_identity_form.php">Blog Identity →</a>
        </div>
        
        <h1>Blog Social Settings</h1>
        <p>Configure social media integration for your blog including sharing buttons, social logins, and auto-posting features.</p>
        
        <?php if ($message): ?>
            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="professional-section-header">Social Sharing Buttons</div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="enable_sharing_buttons" name="enable_sharing_buttons" 
                       <?= ($current_settings['enable_sharing_buttons'] ?? 1) ? 'checked' : '' ?>
                       onchange="toggleSharingSettings()">
                <label for="enable_sharing_buttons">
                    Enable Social Sharing Buttons
                    <small>Add social media sharing buttons to blog posts</small>
                </label>
            </div>
            
            <div id="sharing-settings" style="<?= ($current_settings['enable_sharing_buttons'] ?? 1) ? '' : 'display: none;' ?>">
                <div class="form-group">
                    <label>Sharing Platforms</label>
                    <div class="checkbox-grid">
                        <div class="checkbox-item">
                            <input type="checkbox" id="platform_facebook" name="sharing_platforms[]" value="facebook" 
                                   <?= in_array('facebook', $sharing_platforms) ? 'checked' : '' ?>>
                            <label for="platform_facebook">Facebook</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="platform_twitter" name="sharing_platforms[]" value="twitter" 
                                   <?= in_array('twitter', $sharing_platforms) ? 'checked' : '' ?>>
                            <label for="platform_twitter">Twitter</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="platform_linkedin" name="sharing_platforms[]" value="linkedin" 
                                   <?= in_array('linkedin', $sharing_platforms) ? 'checked' : '' ?>>
                            <label for="platform_linkedin">LinkedIn</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="platform_pinterest" name="sharing_platforms[]" value="pinterest" 
                                   <?= in_array('pinterest', $sharing_platforms) ? 'checked' : '' ?>>
                            <label for="platform_pinterest">Pinterest</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="platform_reddit" name="sharing_platforms[]" value="reddit" 
                                   <?= in_array('reddit', $sharing_platforms) ? 'checked' : '' ?>>
                            <label for="platform_reddit">Reddit</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="platform_whatsapp" name="sharing_platforms[]" value="whatsapp" 
                                   <?= in_array('whatsapp', $sharing_platforms) ? 'checked' : '' ?>>
                            <label for="platform_whatsapp">WhatsApp</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="sharing_button_style">Button Style</label>
                        <select id="sharing_button_style" name="sharing_button_style">
                            <option value="buttons" <?= ($current_settings['sharing_button_style'] ?? 'buttons') === 'buttons' ? 'selected' : '' ?>>Standard Buttons</option>
                            <option value="icons" <?= ($current_settings['sharing_button_style'] ?? '') === 'icons' ? 'selected' : '' ?>>Icon Only</option>
                            <option value="text" <?= ($current_settings['sharing_button_style'] ?? '') === 'text' ? 'selected' : '' ?>>Text Only</option>
                            <option value="floating" <?= ($current_settings['sharing_button_style'] ?? '') === 'floating' ? 'selected' : '' ?>>Floating Sidebar</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sharing_button_position">Button Position</label>
                        <select id="sharing_button_position" name="sharing_button_position">
                            <option value="top" <?= ($current_settings['sharing_button_position'] ?? '') === 'top' ? 'selected' : '' ?>>Above Content</option>
                            <option value="bottom" <?= ($current_settings['sharing_button_position'] ?? 'bottom') === 'bottom' ? 'selected' : '' ?>>Below Content</option>
                            <option value="both" <?= ($current_settings['sharing_button_position'] ?? '') === 'both' ? 'selected' : '' ?>>Above and Below</option>
                            <option value="left" <?= ($current_settings['sharing_button_position'] ?? '') === 'left' ? 'selected' : '' ?>>Left Sidebar</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="professional-section-header">Social Media Accounts</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="twitter_username">Twitter Username</label>
                    <input type="text" id="twitter_username" name="twitter_username" 
                           value="<?= htmlspecialchars($current_settings['twitter_username'] ?? '') ?>" 
                           placeholder="username (without @)">
                    <small>Your Twitter/X username for Twitter Cards and mentions</small>
                </div>
                
                <div class="form-group">
                    <label for="facebook_page_url">Facebook Page URL</label>
                    <input type="url" id="facebook_page_url" name="facebook_page_url" 
                           value="<?= htmlspecialchars($current_settings['facebook_page_url'] ?? '') ?>" 
                           placeholder="https://facebook.com/yourpage">
                    <small>Full URL to your Facebook business page</small>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="instagram_username">Instagram Username</label>
                    <input type="text" id="instagram_username" name="instagram_username" 
                           value="<?= htmlspecialchars($current_settings['instagram_username'] ?? '') ?>" 
                           placeholder="username">
                    <small>Your Instagram username (without @)</small>
                </div>
                
                <div class="form-group">
                    <label for="pinterest_username">Pinterest Username</label>
                    <input type="text" id="pinterest_username" name="pinterest_username" 
                           value="<?= htmlspecialchars($current_settings['pinterest_username'] ?? '') ?>" 
                           placeholder="username">
                    <small>Your Pinterest username for Rich Pins</small>
                </div>
            </div>
            
            <div class="form-group">
                <label for="linkedin_company_id">LinkedIn Company ID</label>
                <input type="text" id="linkedin_company_id" name="linkedin_company_id" 
                       value="<?= htmlspecialchars($current_settings['linkedin_company_id'] ?? '') ?>" 
                       placeholder="12345678">
                <small>Your LinkedIn company page ID (numbers only)</small>
            </div>
            
            <div class="professional-section-header">Follow Buttons</div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="enable_follow_buttons" name="enable_follow_buttons" 
                       <?= ($current_settings['enable_follow_buttons'] ?? 0) ? 'checked' : '' ?>>
                <label for="enable_follow_buttons">
                    Enable Social Follow Buttons
                    <small>Display buttons linking to your social media profiles</small>
                </label>
            </div>
            
            <div class="form-group">
                <label for="follow_button_style">Follow Button Style</label>
                <select id="follow_button_style" name="follow_button_style">
                    <option value="icons" <?= ($current_settings['follow_button_style'] ?? 'icons') === 'icons' ? 'selected' : '' ?>>Icon Style</option>
                    <option value="buttons" <?= ($current_settings['follow_button_style'] ?? '') === 'buttons' ? 'selected' : '' ?>>Button Style</option>
                    <option value="badges" <?= ($current_settings['follow_button_style'] ?? '') === 'badges' ? 'selected' : '' ?>>Badge Style</option>
                </select>
                <small>Visual style for social media follow buttons</small>
            </div>
            
            <div class="professional-section-header">Social Meta Tags</div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="enable_social_meta" name="enable_social_meta" 
                       <?= ($current_settings['enable_social_meta'] ?? 1) ? 'checked' : '' ?>>
                <label for="enable_social_meta">
                    Enable Social Meta Tags
                    <small>Generate Open Graph and Twitter Card meta tags automatically</small>
                </label>
            </div>
            
            <div class="form-group">
                <label for="social_image_size">Social Sharing Image Size</label>
                <select id="social_image_size" name="social_image_size">
                    <option value="1200x630" <?= ($current_settings['social_image_size'] ?? '1200x630') === '1200x630' ? 'selected' : '' ?>>1200x630 (Facebook recommended)</option>
                    <option value="1024x512" <?= ($current_settings['social_image_size'] ?? '') === '1024x512' ? 'selected' : '' ?>>1024x512 (Twitter large card)</option>
                    <option value="800x418" <?= ($current_settings['social_image_size'] ?? '') === '800x418' ? 'selected' : '' ?>>800x418 (LinkedIn)</option>
                    <option value="735x1102" <?= ($current_settings['social_image_size'] ?? '') === '735x1102' ? 'selected' : '' ?>>735x1102 (Pinterest)</option>
                </select>
                <small>Preferred image dimensions for social media sharing</small>
            </div>
            
            <div class="api-section">
                <h3>⚠️ Social API Configuration</h3>
                <p>The following settings are for advanced users who want to integrate social login or auto-posting features. These require API keys from the respective platforms.</p>
                
                <div class="checkbox-item">
                    <input type="checkbox" id="enable_social_login" name="enable_social_login" 
                           <?= ($current_settings['enable_social_login'] ?? 0) ? 'checked' : '' ?>
                           onchange="toggleSocialLogin()">
                    <label for="enable_social_login">
                        Enable Social Login Integration
                        <small>Allow users to log in using their social media accounts</small>
                    </label>
                </div>
                
                <div id="social-login-settings" style="<?= ($current_settings['enable_social_login'] ?? 0) ? '' : 'display: none;' ?>">
                    <div class="social-platform">
                        <div class="platform-header">Facebook Login</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="facebook_app_id">Facebook App ID</label>
                                <input type="text" id="facebook_app_id" name="facebook_app_id" 
                                       value="<?= htmlspecialchars($current_settings['facebook_app_id'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="facebook_app_secret">Facebook App Secret</label>
                                <input type="password" id="facebook_app_secret" name="facebook_app_secret" 
                                       value="<?= htmlspecialchars($current_settings['facebook_app_secret'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="social-platform">
                        <div class="platform-header">Google Login</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="google_client_id">Google Client ID</label>
                                <input type="text" id="google_client_id" name="google_client_id" 
                                       value="<?= htmlspecialchars($current_settings['google_client_id'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="google_client_secret">Google Client Secret</label>
                                <input type="password" id="google_client_secret" name="google_client_secret" 
                                       value="<?= htmlspecialchars($current_settings['google_client_secret'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="social-platform">
                        <div class="platform-header">Twitter API</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="twitter_api_key">Twitter API Key</label>
                                <input type="text" id="twitter_api_key" name="twitter_api_key" 
                                       value="<?= htmlspecialchars($current_settings['twitter_api_key'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="twitter_api_secret">Twitter API Secret</label>
                                <input type="password" id="twitter_api_secret" name="twitter_api_secret" 
                                       value="<?= htmlspecialchars($current_settings['twitter_api_secret'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="checkbox-item">
                    <input type="checkbox" id="enable_auto_posting" name="enable_auto_posting" 
                           <?= ($current_settings['enable_auto_posting'] ?? 0) ? 'checked' : '' ?>
                           onchange="toggleAutoPosting()">
                    <label for="enable_auto_posting">
                        Enable Auto-Posting to Social Media
                        <small>Automatically post to social media when new blog posts are published</small>
                    </label>
                </div>
                
                <div id="auto-posting-settings" style="<?= ($current_settings['enable_auto_posting'] ?? 0) ? '' : 'display: none;' ?>">
                    <div class="form-group">
                        <label>Auto-Post Platforms</label>
                        <div class="checkbox-grid">
                            <div class="checkbox-item">
                                <input type="checkbox" id="autopost_facebook" name="auto_post_platforms[]" value="facebook" 
                                       <?= in_array('facebook', $auto_post_platforms) ? 'checked' : '' ?>>
                                <label for="autopost_facebook">Facebook</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="autopost_twitter" name="auto_post_platforms[]" value="twitter" 
                                       <?= in_array('twitter', $auto_post_platforms) ? 'checked' : '' ?>>
                                <label for="autopost_twitter">Twitter</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="autopost_linkedin" name="auto_post_platforms[]" value="linkedin" 
                                       <?= in_array('linkedin', $auto_post_platforms) ? 'checked' : '' ?>>
                                <label for="autopost_linkedin">LinkedIn</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="default_hashtags">Default Hashtags</label>
                        <input type="text" id="default_hashtags" name="default_hashtags" 
                               value="<?= htmlspecialchars($current_settings['default_hashtags'] ?? '') ?>" 
                               placeholder="#blog #news #update">
                        <small>Default hashtags to include in auto-posts (space-separated)</small>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="submit-btn">Update Blog Social Settings</button>
        </form>
    </div>
    
    <script>
        function toggleSharingSettings() {
            const enabled = document.getElementById('enable_sharing_buttons').checked;
            const settings = document.getElementById('sharing-settings');
            settings.style.display = enabled ? 'block' : 'none';
        }
        
        function toggleSocialLogin() {
            const enabled = document.getElementById('enable_social_login').checked;
            const settings = document.getElementById('social-login-settings');
            settings.style.display = enabled ? 'block' : 'none';
        }
        
        function toggleAutoPosting() {
            const enabled = document.getElementById('enable_auto_posting').checked;
            const settings = document.getElementById('auto-posting-settings');
            settings.style.display = enabled ? 'block' : 'none';
        }
        
        // Initialize visibility on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleSharingSettings();
            toggleSocialLogin();
            toggleAutoPosting();
        });
    </script>
</body>
</html>
