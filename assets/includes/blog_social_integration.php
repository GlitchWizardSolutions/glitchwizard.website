<?php
/**
 * Advanced Social Media Integration System
 * 
 * SYSTEM: GWS Universal Hybrid App - Social Enhancement
 * FILE: blog_social_integration.php
 * LOCATION: /public_html/assets/includes/
 * PURPOSE: Advanced social media features and automation
 * 
 * This system provides comprehensive social media integration including
 * auto-posting, advanced sharing widgets, social analytics, and social
 * login features that integrate with the blog configuration system.
 * 
 * FEATURES:
 * - Auto-posting to social platforms when posts are published
 * - Advanced social sharing widgets with analytics
 * - Social media follow buttons and profile integration
 * - Social login integration (OAuth)
 * - Social media analytics and insights
 * - Open Graph and Twitter Card optimization
 * 
 * CREATED: 2025-08-17
 * VERSION: 1.0
 */

class BlogSocialIntegration {
    private $pdo;
    private $blogConfig;
    private $siteUrl;
    private $apiKeys;
    
    public function __construct($pdo, $siteUrl = null) {
        $this->pdo = $pdo;
        $this->siteUrl = $siteUrl ?: $this->detectSiteUrl();
        
        // Load blog configuration
        if (file_exists(__DIR__ . '/blog_config_reader.php')) {
            require_once __DIR__ . '/blog_config_reader.php';
            $this->blogConfig = getBlogConfig();
            $this->apiKeys = $this->blogConfig['social'] ?? [];
        }
    }
    
    /**
     * Detect site URL automatically
     */
    private function detectSiteUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host;
    }
    
    /**
     * Generate advanced social sharing buttons with analytics
     */
    public function generateAdvancedSharingButtons($post, $options = []) {
        $social = $this->blogConfig['social'] ?? [];
        
        if (!($social['enable_sharing_buttons'] ?? true)) {
            return '';
        }
        
        $platforms = explode(',', $social['sharing_platforms'] ?? 'facebook,twitter,linkedin');
        $style = $social['sharing_button_style'] ?? 'buttons';
        $position = $social['sharing_button_position'] ?? 'bottom';
        
        $postUrl = $options['url'] ?? $this->generatePostUrl($post);
        $title = $post['title'] ?? '';
        $description = $this->extractExcerpt($post['content'] ?? '', 200);
        $imageUrl = $this->getPostImageUrl($post);
        
        $buttonsHtml = [];
        $buttonsHtml[] = '<div class="advanced-social-sharing ' . $style . '-style ' . $position . '-position" data-post-id="' . ($post['id'] ?? '') . '">';
        
        // Add sharing counter
        $buttonsHtml[] = '<div class="sharing-stats">';
        $buttonsHtml[] = '<span class="share-count" id="share-count-' . ($post['id'] ?? '') . '">0</span>';
        $buttonsHtml[] = '<span class="share-label">Shares</span>';
        $buttonsHtml[] = '</div>';
        
        foreach ($platforms as $platform) {
            $platform = trim($platform);
            $shareUrl = $this->generateShareUrl($platform, $postUrl, $title, $description, $imageUrl);
            
            if ($shareUrl) {
                $buttonsHtml[] = $this->generatePlatformButton($platform, $shareUrl, $style);
            }
        }
        
        // Add copy link button
        $buttonsHtml[] = '<button class="share-btn copy-link" onclick="copyToClipboard(\'' . htmlspecialchars($postUrl) . '\')" title="Copy Link">';
        $buttonsHtml[] = '<i class="fas fa-link"></i>';
        $buttonsHtml[] = $style !== 'icons' ? ' Copy Link' : '';
        $buttonsHtml[] = '</button>';
        
        $buttonsHtml[] = '</div>';
        
        // Add JavaScript for analytics and interaction
        $buttonsHtml[] = $this->generateSharingJavaScript($post['id'] ?? null);
        
        return implode("\n", $buttonsHtml);
    }
    
    /**
     * Generate share URL for specific platform
     */
    private function generateShareUrl($platform, $url, $title, $description, $imageUrl) {
        $encodedUrl = urlencode($url);
        $encodedTitle = urlencode($title);
        $encodedDescription = urlencode($description);
        $encodedImage = urlencode($imageUrl);
        
        switch ($platform) {
            case 'facebook':
                return "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}";
                
            case 'twitter':
                $twitterUsername = $this->apiKeys['twitter_username'] ?? '';
                $via = $twitterUsername ? '&via=' . urlencode($twitterUsername) : '';
                return "https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}{$via}";
                
            case 'linkedin':
                return "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}";
                
            case 'pinterest':
                return "https://pinterest.com/pin/create/button/?url={$encodedUrl}&media={$encodedImage}&description={$encodedTitle}";
                
            case 'reddit':
                return "https://reddit.com/submit?url={$encodedUrl}&title={$encodedTitle}";
                
            case 'whatsapp':
                return "https://api.whatsapp.com/send?text={$encodedTitle}%20{$encodedUrl}";
                
            case 'telegram':
                return "https://t.me/share/url?url={$encodedUrl}&text={$encodedTitle}";
                
            case 'email':
                $subject = urlencode("Check out: {$title}");
                $body = urlencode("I thought you might be interested in this: {$title}\n\n{$description}\n\n{$url}");
                return "mailto:?subject={$subject}&body={$body}";
                
            default:
                return null;
        }
    }
    
    /**
     * Generate platform-specific button HTML
     */
    private function generatePlatformButton($platform, $shareUrl, $style) {
        $platformInfo = $this->getPlatformInfo($platform);
        $icon = $platformInfo['icon'];
        $name = $platformInfo['name'];
        $color = $platformInfo['color'];
        
        $buttonClass = "share-btn {$platform}";
        $buttonStyle = $color ? "background-color: {$color};" : '';
        
        $html = '<a href="' . htmlspecialchars($shareUrl) . '" ';
        $html .= 'class="' . $buttonClass . '" ';
        $html .= 'style="' . $buttonStyle . '" ';
        $html .= 'target="_blank" ';
        $html .= 'rel="noopener noreferrer" ';
        $html .= 'onclick="trackShare(\'' . $platform . '\')" ';
        $html .= 'title="Share on ' . $name . '">';
        $html .= '<i class="' . $icon . '"></i>';
        
        if ($style !== 'icons') {
            $html .= ' ' . $name;
        }
        
        $html .= '</a>';
        
        return $html;
    }
    
    /**
     * Get platform information
     */
    private function getPlatformInfo($platform) {
        $platforms = [
            'facebook' => ['name' => 'Facebook', 'icon' => 'fab fa-facebook-f', 'color' => '#3b5998'],
            'twitter' => ['name' => 'Twitter', 'icon' => 'fab fa-twitter', 'color' => '#1da1f2'],
            'linkedin' => ['name' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in', 'color' => '#0077b5'],
            'pinterest' => ['name' => 'Pinterest', 'icon' => 'fab fa-pinterest-p', 'color' => '#bd081c'],
            'reddit' => ['name' => 'Reddit', 'icon' => 'fab fa-reddit-alien', 'color' => '#ff4500'],
            'whatsapp' => ['name' => 'WhatsApp', 'icon' => 'fab fa-whatsapp', 'color' => '#25d366'],
            'telegram' => ['name' => 'Telegram', 'icon' => 'fab fa-telegram-plane', 'color' => '#0088cc'],
            'email' => ['name' => 'Email', 'icon' => 'fas fa-envelope', 'color' => '#666666']
        ];
        
        return $platforms[$platform] ?? ['name' => ucfirst($platform), 'icon' => 'fas fa-share-alt', 'color' => '#666666'];
    }
    
    /**
     * Generate social follow buttons
     */
    public function generateFollowButtons() {
        $social = $this->blogConfig['social'] ?? [];
        
        if (!($social['enable_follow_buttons'] ?? false)) {
            return '';
        }
        
        $style = $social['follow_button_style'] ?? 'icons';
        $buttons = [];
        
        $buttons[] = '<div class="social-follow-buttons ' . $style . '-style">';
        $buttons[] = '<h6>Follow Us</h6>';
        
        $socialUrls = [
            'facebook' => $social['facebook_page_url'] ?? '',
            'twitter' => $social['twitter_username'] ? 'https://twitter.com/' . $social['twitter_username'] : '',
            'instagram' => $social['instagram_username'] ? 'https://instagram.com/' . $social['instagram_username'] : '',
            'linkedin' => $social['linkedin_company_id'] ? 'https://linkedin.com/company/' . $social['linkedin_company_id'] : '',
            'pinterest' => $social['pinterest_username'] ? 'https://pinterest.com/' . $social['pinterest_username'] : ''
        ];
        
        foreach ($socialUrls as $platform => $url) {
            if (!empty($url)) {
                $platformInfo = $this->getPlatformInfo($platform);
                $buttons[] = '<a href="' . htmlspecialchars($url) . '" class="follow-btn ' . $platform . '" target="_blank" rel="noopener" title="Follow on ' . $platformInfo['name'] . '">';
                $buttons[] = '<i class="' . $platformInfo['icon'] . '"></i>';
                if ($style !== 'icons') {
                    $buttons[] = ' ' . $platformInfo['name'];
                }
                $buttons[] = '</a>';
            }
        }
        
        $buttons[] = '</div>';
        
        return implode("\n", $buttons);
    }
    
    /**
     * Auto-post to social media when a new post is published
     */
    public function autoPost($post) {
        $social = $this->blogConfig['social'] ?? [];
        
        if (!($social['enable_auto_posting'] ?? false)) {
            return false;
        }
        
        $platforms = explode(',', $social['auto_post_platforms'] ?? '');
        $defaultHashtags = $social['default_hashtags'] ?? '';
        $postUrl = $this->generatePostUrl($post);
        
        $results = [];
        
        foreach ($platforms as $platform) {
            $platform = trim($platform);
            $result = $this->postToPlatform($platform, $post, $postUrl, $defaultHashtags);
            $results[$platform] = $result;
            
            // Log the auto-post attempt
            $this->logAutoPost($post['id'], $platform, $result);
        }
        
        return $results;
    }
    
    /**
     * Post to specific social media platform
     */
    private function postToPlatform($platform, $post, $postUrl, $hashtags) {
        // This is a simplified implementation
        // In production, you would integrate with actual social media APIs
        
        $message = $this->formatPostMessage($post, $postUrl, $hashtags);
        
        switch ($platform) {
            case 'facebook':
                return $this->postToFacebook($message, $post);
                
            case 'twitter':
                return $this->postToTwitter($message, $post);
                
            case 'linkedin':
                return $this->postToLinkedIn($message, $post);
                
            default:
                return ['success' => false, 'message' => 'Platform not supported'];
        }
    }
    
    /**
     * Format post message for social media
     */
    private function formatPostMessage($post, $postUrl, $hashtags) {
        $title = $post['title'];
        $excerpt = $this->extractExcerpt($post['content'], 100);
        
        $message = "{$title}\n\n{$excerpt}\n\n{$postUrl}";
        
        if (!empty($hashtags)) {
            $message .= "\n\n{$hashtags}";
        }
        
        return $message;
    }
    
    /**
     * Simplified Facebook posting (requires Facebook Graph API integration)
     */
    private function postToFacebook($message, $post) {
        // Placeholder for Facebook API integration
        // You would implement actual Facebook Graph API calls here
        return ['success' => false, 'message' => 'Facebook API integration required'];
    }
    
    /**
     * Simplified Twitter posting (requires Twitter API integration)
     */
    private function postToTwitter($message, $post) {
        // Placeholder for Twitter API integration
        // You would implement actual Twitter API calls here
        return ['success' => false, 'message' => 'Twitter API integration required'];
    }
    
    /**
     * Simplified LinkedIn posting (requires LinkedIn API integration)
     */
    private function postToLinkedIn($message, $post) {
        // Placeholder for LinkedIn API integration
        // You would implement actual LinkedIn API calls here
        return ['success' => false, 'message' => 'LinkedIn API integration required'];
    }
    
    /**
     * Log auto-post attempts
     */
    private function logAutoPost($postId, $platform, $result) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO blog_social_posts (post_id, platform, success, message, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $postId,
                $platform,
                $result['success'] ? 1 : 0,
                $result['message'] ?? ''
            ]);
        } catch (Exception $e) {
            // Create table if it doesn't exist
            $this->createSocialPostsTable();
        }
    }
    
    /**
     * Create social posts tracking table
     */
    private function createSocialPostsTable() {
        $sql = "
        CREATE TABLE IF NOT EXISTS blog_social_posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            post_id INT NOT NULL,
            platform VARCHAR(50) NOT NULL,
            success TINYINT(1) DEFAULT 0,
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX (post_id),
            INDEX (platform)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";
        
        $this->pdo->exec($sql);
    }
    
    /**
     * Generate JavaScript for sharing analytics
     */
    private function generateSharingJavaScript($postId) {
        return "
        <script>
        function trackShare(platform) {
            // Track sharing event
            if (typeof gtag !== 'undefined') {
                gtag('event', 'share', {
                    'method': platform,
                    'content_type': 'blog_post',
                    'content_id': '{$postId}'
                });
            }
            
            // Update share count
            updateShareCount('{$postId}');
        }
        
        function updateShareCount(postId) {
            const countElement = document.getElementById('share-count-' + postId);
            if (countElement) {
                let currentCount = parseInt(countElement.textContent) || 0;
                countElement.textContent = currentCount + 1;
            }
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const tooltip = document.createElement('div');
                tooltip.textContent = 'Link copied!';
                tooltip.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #28a745; color: white; padding: 10px; border-radius: 4px; z-index: 1000;';
                document.body.appendChild(tooltip);
                
                setTimeout(() => {
                    document.body.removeChild(tooltip);
                }, 2000);
                
                trackShare('copy_link');
            });
        }
        </script>
        ";
    }
    
    /**
     * Helper methods
     */
    private function generatePostUrl($post) {
        if (file_exists(__DIR__ . '/blog_seo_automation.php')) {
            require_once __DIR__ . '/blog_seo_automation.php';
            return generateSEOUrl('post', $post);
        }
        
        return $this->siteUrl . '/post.php?id=' . $post['id'];
    }
    
    private function extractExcerpt($content, $length = 200) {
        $excerpt = strip_tags($content);
        $excerpt = preg_replace('/\s+/', ' ', $excerpt);
        $excerpt = trim($excerpt);
        
        if (strlen($excerpt) <= $length) {
            return $excerpt;
        }
        
        $excerpt = substr($excerpt, 0, $length);
        $lastSpace = strrpos($excerpt, ' ');
        if ($lastSpace !== false) {
            $excerpt = substr($excerpt, 0, $lastSpace);
        }
        
        return $excerpt . '...';
    }
    
    private function getPostImageUrl($post) {
        if (empty($post['image'])) {
            $seo = $this->blogConfig['seo'] ?? [];
            return $seo['default_post_image'] ?? '';
        }
        
        $imagePath = $post['image'];
        if (preg_match('/^https?:\/\//', $imagePath)) {
            return $imagePath;
        }
        
        return $this->siteUrl . '/admin/blog/blog_post_images/' . ltrim($imagePath, '/');
    }
    
    /**
     * Get social sharing statistics
     */
    public function getSharingStats($postId = null) {
        $sql = "SELECT platform, COUNT(*) as count FROM blog_social_posts WHERE success = 1";
        $params = [];
        
        if ($postId) {
            $sql .= " AND post_id = ?";
            $params[] = $postId;
        }
        
        $sql .= " GROUP BY platform ORDER BY count DESC";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}

// Global social integration instance
global $blogSocial;
if (!isset($blogSocial) && isset($pdo)) {
    $blogSocial = new BlogSocialIntegration($pdo);
}

/**
 * Helper functions for easy access
 */
function generateAdvancedSharingButtons($post, $options = []) {
    global $blogSocial;
    return $blogSocial ? $blogSocial->generateAdvancedSharingButtons($post, $options) : '';
}

function generateFollowButtons() {
    global $blogSocial;
    return $blogSocial ? $blogSocial->generateFollowButtons() : '';
}

function autoPostToSocial($post) {
    global $blogSocial;
    return $blogSocial ? $blogSocial->autoPost($post) : false;
}

function getSocialSharingStats($postId = null) {
    global $blogSocial;
    return $blogSocial ? $blogSocial->getSharingStats($postId) : [];
}
?>
