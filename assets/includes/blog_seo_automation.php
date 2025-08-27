<?php
/**
 * Advanced SEO Automation System
 * 
 * SYSTEM: GWS Universal Hybrid App - SEO Enhancement
 * FILE: blog_seo_automation.php
 * LOCATION: /public_html/assets/includes/
 * PURPOSE: Automated SEO optimization for blog content
 * 
 * This system provides advanced SEO automation including sitemap generation,
 * meta tag optimization, schema markup, and search engine optimization
 * features that integrate with the blog configuration system.
 * 
 * FEATURES:
 * - Automatic XML sitemap generation
 * - Dynamic schema markup injection
 * - Meta tag optimization for posts
 * - Open Graph and Twitter Card automation
 * - SEO-friendly URL generation
 * - Analytics integration
 * 
 * CREATED: 2025-08-17
 * VERSION: 1.0
 */

class BlogSEOAutomation {
    private $pdo;
    private $blogConfig;
    private $siteUrl;
    
    public function __construct($pdo, $siteUrl = null) {
        $this->pdo = $pdo;
        $this->siteUrl = $siteUrl ?: $this->detectSiteUrl();
        
        // Load blog configuration
        if (file_exists(__DIR__ . '/blog_config_reader.php')) {
            require_once __DIR__ . '/blog_config_reader.php';
            $this->blogConfig = getBlogConfig();
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
     * Generate XML Sitemap for blog posts
     */
    public function generateXMLSitemap() {
        $seo = $this->blogConfig['seo'] ?? [];
        
        if (!($seo['enable_sitemap'] ?? true)) {
            return false;
        }
        
        $frequency = $seo['sitemap_frequency'] ?? 'weekly';
        $priority = $seo['sitemap_priority'] ?? 0.8;
        
        // Get all published posts
        $stmt = $this->pdo->query("
            SELECT id, title, content, date, updated_at 
            FROM blog_posts 
            WHERE active = 'Yes' 
            ORDER BY date DESC
        ");
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get all published pages
        $pageStmt = $this->pdo->query("
            SELECT id, title, content, date, updated_at 
            FROM blog_pages 
            WHERE active = 'Yes' 
            ORDER BY date DESC
        ");
        $pages = $pageStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Generate XML
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        
        $urlset = $xml->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset');
        $xml->appendChild($urlset);
        
        // Add blog home page
        $url = $xml->createElement('url');
        $url->appendChild($xml->createElement('loc', htmlspecialchars($this->siteUrl . '/blog.php')));
        $url->appendChild($xml->createElement('lastmod', date('Y-m-d')));
        $url->appendChild($xml->createElement('changefreq', $frequency));
        $url->appendChild($xml->createElement('priority', '1.0'));
        $urlset->appendChild($url);
        
        // Add blog posts
        foreach ($posts as $post) {
            $url = $xml->createElement('url');
            $postUrl = $this->generateSEOUrl('post', $post);
            $url->appendChild($xml->createElement('loc', htmlspecialchars($postUrl)));
            
            $lastmod = $post['updated_at'] ?: $post['date'];
            $url->appendChild($xml->createElement('lastmod', date('Y-m-d', strtotime($lastmod))));
            $url->appendChild($xml->createElement('changefreq', $frequency));
            $url->appendChild($xml->createElement('priority', (string)$priority));
            $urlset->appendChild($url);
        }
        
        // Add blog pages
        foreach ($pages as $page) {
            $url = $xml->createElement('url');
            $pageUrl = $this->generateSEOUrl('page', $page);
            $url->appendChild($xml->createElement('loc', htmlspecialchars($pageUrl)));
            
            $lastmod = $page['updated_at'] ?: $page['date'];
            $url->appendChild($xml->createElement('lastmod', date('Y-m-d', strtotime($lastmod))));
            $url->appendChild($xml->createElement('changefreq', 'monthly'));
            $url->appendChild($xml->createElement('priority', '0.7'));
            $urlset->appendChild($url);
        }
        
        // Save sitemap
        $sitemapPath = __DIR__ . '/../../sitemap-blog.xml';
        $xml->save($sitemapPath);
        
        return $sitemapPath;
    }
    
    /**
     * Generate SEO-friendly URLs based on configuration
     */
    public function generateSEOUrl($type, $content) {
        $seo = $this->blogConfig['seo'] ?? [];
        
        if (!($seo['enable_seo_urls'] ?? true)) {
            // Fallback to query parameter URLs
            return $this->siteUrl . '/' . $type . '.php?id=' . $content['id'];
        }
        
        $structure = $seo['post_url_structure'] ?? '{year}/{month}/{slug}';
        $date = strtotime($content['date']);
        $slug = $this->generateSlug($content['title']);
        
        // Get category for post if needed
        $category = '';
        if ($type === 'post' && strpos($structure, '{category}') !== false) {
            $catStmt = $this->pdo->prepare("
                SELECT c.name 
                FROM blog_categories c 
                JOIN blog_post_categories pc ON c.id = pc.category_id 
                WHERE pc.post_id = ? 
                LIMIT 1
            ");
            $catStmt->execute([$content['id']]);
            $categoryResult = $catStmt->fetch();
            $category = $categoryResult ? $this->generateSlug($categoryResult['name']) : 'uncategorized';
        }
        
        // Replace URL structure placeholders
        $url = str_replace([
            '{year}',
            '{month}',
            '{category}',
            '{slug}'
        ], [
            date('Y', $date),
            date('m', $date),
            $category,
            $slug
        ], $structure);
        
        return $this->siteUrl . '/blog/' . ltrim($url, '/') . '/';
    }
    
    /**
     * Generate URL slug from title
     */
    private function generateSlug($title) {
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        return trim($slug, '-');
    }
    
    /**
     * Generate comprehensive meta tags for a post
     */
    public function generatePostMetaTags($post) {
        $seo = $this->blogConfig['seo'] ?? [];
        $identity = $this->blogConfig['identity'] ?? [];
        $social = $this->blogConfig['social'] ?? [];
        
        $metaTags = [];
        
        // Basic SEO meta tags
        if ($seo['enable_meta_tags'] ?? true) {
            $description = $this->extractExcerpt($post['content'], 160);
            $keywords = $this->extractKeywords($post['content'], $post['title']);
            
            $metaTags[] = '<meta name="description" content="' . htmlspecialchars($description) . '">';
            $metaTags[] = '<meta name="keywords" content="' . htmlspecialchars($keywords) . '">';
            $metaTags[] = '<meta name="author" content="' . htmlspecialchars($post['author'] ?? $identity['author_name'] ?? '') . '">';
            $metaTags[] = '<meta name="robots" content="index, follow">';
        }
        
        // Canonical URL
        if ($seo['enable_canonical_urls'] ?? true) {
            $canonicalUrl = $this->generateSEOUrl('post', $post);
            $metaTags[] = '<link rel="canonical" href="' . htmlspecialchars($canonicalUrl) . '">';
        }
        
        // Open Graph meta tags
        if ($seo['enable_open_graph'] ?? true) {
            $postUrl = $this->generateSEOUrl('post', $post);
            $description = $this->extractExcerpt($post['content'], 200);
            
            $metaTags[] = '<meta property="og:title" content="' . htmlspecialchars($post['title']) . '">';
            $metaTags[] = '<meta property="og:description" content="' . htmlspecialchars($description) . '">';
            $metaTags[] = '<meta property="og:url" content="' . htmlspecialchars($postUrl) . '">';
            $metaTags[] = '<meta property="og:type" content="article">';
            $metaTags[] = '<meta property="og:site_name" content="' . htmlspecialchars($identity['blog_title'] ?? '') . '">';
            
            // Featured image
            if (!empty($post['image'])) {
                $imageUrl = $this->getFullImageUrl($post['image']);
                $metaTags[] = '<meta property="og:image" content="' . htmlspecialchars($imageUrl) . '">';
                $metaTags[] = '<meta property="og:image:width" content="1200">';
                $metaTags[] = '<meta property="og:image:height" content="630">';
            } elseif (!empty($seo['default_post_image'])) {
                $metaTags[] = '<meta property="og:image" content="' . htmlspecialchars($seo['default_post_image']) . '">';
            }
            
            // Article specific tags
            $metaTags[] = '<meta property="article:published_time" content="' . date('c', strtotime($post['date'])) . '">';
            if (!empty($post['updated_at'])) {
                $metaTags[] = '<meta property="article:modified_time" content="' . date('c', strtotime($post['updated_at'])) . '">';
            }
            $metaTags[] = '<meta property="article:author" content="' . htmlspecialchars($post['author'] ?? $identity['author_name'] ?? '') . '">';
        }
        
        // Twitter Card meta tags
        if ($seo['enable_twitter_cards'] ?? true) {
            $metaTags[] = '<meta name="twitter:card" content="summary_large_image">';
            $metaTags[] = '<meta name="twitter:title" content="' . htmlspecialchars($post['title']) . '">';
            $metaTags[] = '<meta name="twitter:description" content="' . htmlspecialchars($this->extractExcerpt($post['content'], 200)) . '">';
            
            if (!empty($social['twitter_username'])) {
                $metaTags[] = '<meta name="twitter:site" content="@' . htmlspecialchars($social['twitter_username']) . '">';
            }
            
            if (!empty($post['image'])) {
                $imageUrl = $this->getFullImageUrl($post['image']);
                $metaTags[] = '<meta name="twitter:image" content="' . htmlspecialchars($imageUrl) . '">';
            }
        }
        
        return implode("\n", $metaTags);
    }
    
    /**
     * Generate JSON-LD schema markup for blog post
     */
    public function generatePostSchema($post) {
        $seo = $this->blogConfig['seo'] ?? [];
        $identity = $this->blogConfig['identity'] ?? [];
        
        if (!($seo['enable_schema_markup'] ?? true)) {
            return '';
        }
        
        $postUrl = $this->generateSEOUrl('post', $post);
        $authorName = $post['author'] ?? $identity['author_name'] ?? 'Unknown Author';
        $siteName = $identity['blog_title'] ?? 'Blog';
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post['title'],
            'description' => $this->extractExcerpt($post['content'], 200),
            'url' => $postUrl,
            'datePublished' => date('c', strtotime($post['date'])),
            'author' => [
                '@type' => 'Person',
                'name' => $authorName
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName
            ]
        ];
        
        // Add modified date if available
        if (!empty($post['updated_at'])) {
            $schema['dateModified'] = date('c', strtotime($post['updated_at']));
        }
        
        // Add featured image
        if (!empty($post['image'])) {
            $imageUrl = $this->getFullImageUrl($post['image']);
            $schema['image'] = [
                '@type' => 'ImageObject',
                'url' => $imageUrl
            ];
        }
        
        // Add word count
        $wordCount = str_word_count(strip_tags($post['content']));
        $schema['wordCount'] = $wordCount;
        
        return '<script type="application/ld+json">' . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    }
    
    /**
     * Generate breadcrumb schema markup
     */
    public function generateBreadcrumbSchema($post = null, $category = null) {
        $seo = $this->blogConfig['seo'] ?? [];
        $identity = $this->blogConfig['identity'] ?? [];
        
        if (!($seo['enable_breadcrumb_schema'] ?? true)) {
            return '';
        }
        
        $breadcrumbs = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => []
        ];
        
        // Home
        $breadcrumbs['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => $this->siteUrl
        ];
        
        // Blog
        $breadcrumbs['itemListElement'][] = [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => $identity['blog_title'] ?? 'Blog',
            'item' => $this->siteUrl . '/blog.php'
        ];
        
        // Category (if available)
        if ($category) {
            $breadcrumbs['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $category['name'],
                'item' => $this->siteUrl . '/category.php?id=' . $category['id']
            ];
        }
        
        // Post (if on post page)
        if ($post) {
            $position = $category ? 4 : 3;
            $breadcrumbs['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $post['title'],
                'item' => $this->generateSEOUrl('post', $post)
            ];
        }
        
        return '<script type="application/ld+json">' . json_encode($breadcrumbs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
    }
    
    /**
     * Extract excerpt from content
     */
    private function extractExcerpt($content, $maxLength = 160) {
        $excerpt = strip_tags($content);
        $excerpt = preg_replace('/\s+/', ' ', $excerpt);
        $excerpt = trim($excerpt);
        
        if (strlen($excerpt) <= $maxLength) {
            return $excerpt;
        }
        
        $excerpt = substr($excerpt, 0, $maxLength);
        $lastSpace = strrpos($excerpt, ' ');
        if ($lastSpace !== false) {
            $excerpt = substr($excerpt, 0, $lastSpace);
        }
        
        return $excerpt . '...';
    }
    
    /**
     * Extract keywords from content
     */
    private function extractKeywords($content, $title) {
        $text = strip_tags($content . ' ' . $title);
        $words = str_word_count(strtolower($text), 1);
        
        // Remove common stop words
        $stopWords = ['the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'is', 'are', 'was', 'were', 'be', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'must', 'can', 'shall', 'this', 'that', 'these', 'those', 'a', 'an'];
        $words = array_diff($words, $stopWords);
        
        // Count word frequency
        $wordCount = array_count_values($words);
        arsort($wordCount);
        
        // Get top keywords
        $keywords = array_slice(array_keys($wordCount), 0, 10);
        
        return implode(', ', $keywords);
    }
    
    /**
     * Get full URL for image
     */
    private function getFullImageUrl($imagePath) {
        if (preg_match('/^https?:\/\//', $imagePath)) {
            return $imagePath;
        }
        
        // Handle relative paths
        if (strpos($imagePath, '/') === 0) {
            return $this->siteUrl . $imagePath;
        }
        
        return $this->siteUrl . '/admin/blog/blog_post_images/' . $imagePath;
    }
    
    /**
     * Generate analytics code
     */
    public function generateAnalyticsCode() {
        $seo = $this->blogConfig['seo'] ?? [];
        $analyticsId = $seo['google_analytics_id'] ?? '';
        
        if (empty($analyticsId)) {
            return '';
        }
        
        if (strpos($analyticsId, 'G-') === 0) {
            // Google Analytics 4
            return "
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src=\"https://www.googletagmanager.com/gtag/js?id={$analyticsId}\"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
              gtag('config', '{$analyticsId}');
            </script>";
        } else {
            // Universal Analytics
            return "
            <!-- Google Analytics -->
            <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            ga('create', '{$analyticsId}', 'auto');
            ga('send', 'pageview');
            </script>";
        }
    }
    
    /**
     * Generate site verification meta tags
     */
    public function generateVerificationTags() {
        $seo = $this->blogConfig['seo'] ?? [];
        $tags = [];
        
        if (!empty($seo['google_site_verification'])) {
            $tags[] = '<meta name="google-site-verification" content="' . htmlspecialchars($seo['google_site_verification']) . '">';
        }
        
        if (!empty($seo['bing_site_verification'])) {
            $tags[] = '<meta name="msvalidate.01" content="' . htmlspecialchars($seo['bing_site_verification']) . '">';
        }
        
        return implode("\n", $tags);
    }
}

// Global SEO automation instance
global $blogSEO;
if (!isset($blogSEO) && isset($pdo)) {
    $blogSEO = new BlogSEOAutomation($pdo);
}

/**
 * Helper functions for easy access
 */
function generateBlogSitemap() {
    global $blogSEO;
    return $blogSEO ? $blogSEO->generateXMLSitemap() : false;
}

function generatePostMetaTags($post) {
    global $blogSEO;
    return $blogSEO ? $blogSEO->generatePostMetaTags($post) : '';
}

function generatePostSchema($post) {
    global $blogSEO;
    return $blogSEO ? $blogSEO->generatePostSchema($post) : '';
}

function generateBreadcrumbSchema($post = null, $category = null) {
    global $blogSEO;
    return $blogSEO ? $blogSEO->generateBreadcrumbSchema($post, $category) : '';
}

function generateAnalyticsCode() {
    global $blogSEO;
    return $blogSEO ? $blogSEO->generateAnalyticsCode() : '';
}

function generateVerificationTags() {
    global $blogSEO;
    return $blogSEO ? $blogSEO->generateVerificationTags() : '';
}

function generateSEOUrl($type, $content) {
    global $blogSEO;
    return $blogSEO ? $blogSEO->generateSEOUrl($type, $content) : '';
}
?>
