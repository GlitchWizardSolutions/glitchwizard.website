<?php
/**
 * Page Detection and Settings Management
 * Automatically detects which pages exist and manages their settings accordingly
 */

/**
 * Scan the public directory for pages and determine which settings are needed
 */
function detectExistingPages($base_path = '../../') {
    $detected_pages = [];
    $config = require_once 'page_settings_config.php';
    
    foreach ($config as $page_path => $page_config) {
        $full_path = $base_path . $page_path;
        
        if (file_exists($full_path)) {
            $detected_pages[$page_path] = [
                'exists' => true,
                'config' => $page_config,
                'last_modified' => filemtime($full_path),
                'file_size' => filesize($full_path)
            ];
        } else {
            // Check for common alternative locations
            $alternatives = [
                str_replace('.php', '/index.php', $page_path),
                'pages/' . $page_path,
                'public/' . $page_path
            ];
            
            $found = false;
            foreach ($alternatives as $alt_path) {
                if (file_exists($base_path . $alt_path)) {
                    $detected_pages[$alt_path] = [
                        'exists' => true,
                        'config' => $page_config,
                        'original_path' => $page_path,
                        'last_modified' => filemtime($base_path . $alt_path),
                        'file_size' => filesize($base_path . $alt_path)
                    ];
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $detected_pages[$page_path] = [
                    'exists' => false,
                    'config' => $page_config,
                    'priority' => $page_config['priority'] ?? 10
                ];
            }
        }
    }
    
    // Sort by priority (lower numbers = higher priority)
    uasort($detected_pages, function($a, $b) {
        $priority_a = $a['config']['priority'] ?? 10;
        $priority_b = $b['config']['priority'] ?? 10;
        return $priority_a - $priority_b;
    });
    
    return $detected_pages;
}

/**
 * Get recommended pages for this installation type
 */
function getRecommendedPages($detected_pages) {
    $recommendations = [];
    
    foreach ($detected_pages as $page_path => $page_info) {
        if (!$page_info['exists'] && ($page_info['config']['priority'] ?? 10) <= 5) {
            $recommendations[] = [
                'path' => $page_path,
                'title' => $page_info['config']['title'],
                'description' => $page_info['config']['description'],
                'priority' => $page_info['config']['priority'] ?? 10,
                'reason' => getRecommendationReason($page_path)
            ];
        }
    }
    
    return $recommendations;
}

/**
 * Get the reason why a page is recommended
 */
function getRecommendationReason($page_path) {
    $reasons = [
        'contact.php' => 'Essential for user communication and business credibility',
        'about.php' => 'Builds trust and explains your business story',
        'services.php' => 'Showcases what you offer to potential customers',
        'blog.php' => 'Improves SEO and provides valuable content to users',
        'policy-privacy.php' => 'Required for legal compliance and user trust'
    ];
    
    return $reasons[$page_path] ?? 'Commonly used page that enhances user experience';
}

/**
 * Create default settings structure for a page
 */
function createDefaultPageSettings($page_path, $config) {
    $defaults = [];
    
    foreach ($config['sections'] as $section_name => $section_info) {
        $defaults[$section_name] = [];
        
        if (isset($section_info['fields'])) {
            foreach ($section_info['fields'] as $field) {
                $defaults[$section_name][$field] = getDefaultValue($section_name, $field);
            }
        }
    }
    
    return $defaults;
}

/**
 * Get default value for a specific field
 */
function getDefaultValue($section, $field) {
    $defaults = [
        'site' => [
            'name' => 'Your Website Name',
            'tagline' => 'Your Website Tagline',
            'description' => 'A brief description of your website',
            'email' => 'contact@yoursite.com',
            'phone' => '(555) 123-4567'
        ],
        'homepage' => [
            'welcome_message' => 'Welcome to our website!',
            'call_to_action' => 'Get Started Today'
        ],
        'hero' => [
            'title' => 'Your Main Headline',
            'subtitle' => 'Supporting text that explains your value proposition',
            'button_text' => 'Learn More'
        ],
        'contact' => [
            'address' => '123 Main Street, City, State 12345',
            'hours' => 'Monday - Friday: 9:00 AM - 5:00 PM',
            'form_title' => 'Get In Touch'
        ],
        'blog' => [
            'title' => 'Our Blog',
            'description' => 'Latest news and updates',
            'posts_per_page' => '10'
        ]
    ];
    
    return $defaults[$section][$field] ?? '';
}

/**
 * Validate settings structure
 */
function validatePageSettings($settings, $page_config) {
    $errors = [];
    $warnings = [];
    
    foreach ($page_config['sections'] as $section_name => $section_info) {
        if (!isset($settings[$section_name])) {
            $warnings[] = "Missing section: $section_name";
            continue;
        }
        
        if (isset($section_info['fields'])) {
            foreach ($section_info['fields'] as $field) {
                if (!isset($settings[$section_name][$field]) || 
                    empty(trim($settings[$section_name][$field]))) {
                    $warnings[] = "Empty field: $section_name.$field";
                }
            }
        }
    }
    
    return ['errors' => $errors, 'warnings' => $warnings];
}

/**
 * Get completion percentage for a page
 */
function getPageCompletionPercentage($settings, $page_config) {
    $total_fields = 0;
    $completed_fields = 0;
    
    foreach ($page_config['sections'] as $section_name => $section_info) {
        if (isset($section_info['fields'])) {
            foreach ($section_info['fields'] as $field) {
                $total_fields++;
                if (isset($settings[$section_name][$field]) && 
                    !empty(trim($settings[$section_name][$field]))) {
                    $completed_fields++;
                }
            }
        }
    }
    
    return $total_fields > 0 ? round(($completed_fields / $total_fields) * 100) : 0;
}
?>
