-- Complete Footer Database Setup
-- This creates all database tables for managing footer content
-- Includes both Useful Links and Special Links sections

-- ================================================================
-- 1. USEFUL LINKS TABLE (Left side of footer - "Useful Links")
-- ================================================================
CREATE TABLE IF NOT EXISTS setting_footer_useful_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(50) DEFAULT 'bi-link-45deg',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample useful links for Burden to Blessings
INSERT INTO setting_footer_useful_links (title, url, icon, display_order, is_active) VALUES
('About Us', '/about', 'bi-info-circle', 1, 1),
('Reviews', '/reviews', 'bi-star', 2, 1),
('FAQs', '/faq', 'bi-question-circle', 3, 1),
('Resources', '/resources', 'bi-folder', 4, 1),
('Contact', '/contact', 'bi-envelope', 5, 1);

-- ================================================================
-- 2. SPECIAL LINKS TABLE (Bottom row - RSS, Sitemap, Policies)
-- ================================================================
CREATE TABLE IF NOT EXISTS setting_footer_special_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    link_key VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fas fa-link',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    target VARCHAR(10) DEFAULT '_blank',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert special links (bottom row buttons)
INSERT INTO setting_footer_special_links (link_key, title, url, icon, display_order, is_active, target) VALUES
('rss', 'RSS Feed', 'rss.php', 'fas fa-rss-square', 1, 1, '_blank'),
('sitemap', 'XML Sitemap', 'sitemap.php', 'fas fa-sitemap', 2, 1, '_blank'),
('accessibility_policy', 'Accessibility Policy', 'policy-accessibility.php', 'fas fa-universal-access', 3, 1, '_blank'),
('terms_of_service', 'Terms of Service', 'policy-terms.php', 'fas fa-file-contract', 4, 1, '_blank'),
('privacy_policy', 'Privacy Policy', 'policy-privacy.php', 'fas fa-user-shield', 5, 1, '_blank');

-- ================================================================
-- 3. FOOTER SETTINGS TABLE (Copyright, general footer config)
-- ================================================================
CREATE TABLE IF NOT EXISTS setting_footer_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    copyright_year_start VARCHAR(4) DEFAULT '2025',
    copyright_site_name VARCHAR(100),
    copyright_text VARCHAR(255),
    show_design_credit TINYINT(1) DEFAULT 1,
    design_credit_text VARCHAR(100) DEFAULT 'Designed by Glitch Wizard Solutions',
    design_credit_url VARCHAR(255) DEFAULT 'https://glitchwizardsolutions.com',
    footer_background_color VARCHAR(7) DEFAULT '#f8f9fa',
    footer_text_color VARCHAR(7) DEFAULT '#333333',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default footer configuration
INSERT INTO setting_footer_config (copyright_site_name, copyright_text) VALUES
('Burden to Blessings', 'All Rights Reserved');

-- ================================================================
-- VERIFICATION QUERIES
-- ================================================================

-- Show all useful links
SELECT 'USEFUL LINKS:' as section;
SELECT * FROM setting_footer_useful_links ORDER BY display_order ASC;

-- Show all special links  
SELECT 'SPECIAL LINKS:' as section;
SELECT * FROM setting_footer_special_links ORDER BY display_order ASC;

-- Show footer config
SELECT 'FOOTER CONFIG:' as section;
SELECT * FROM setting_footer_config;

-- Show table structures
SELECT 'TABLE STRUCTURES:' as section;
SHOW CREATE TABLE setting_footer_useful_links;
SHOW CREATE TABLE setting_footer_special_links; 
SHOW CREATE TABLE setting_footer_config;
