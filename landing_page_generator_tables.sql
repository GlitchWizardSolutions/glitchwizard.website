-- Landing Page Generator Database Tables
-- GWS Universal Hybrid Application - Landing Page Generator Integration
-- This creates a comprehensive landing page builder and management system

-- Create database if it doesn't exist
-- CREATE DATABASE IF NOT EXISTS gws_universal_app;
-- USE gws_universal_app;

-- Main landing pages table
CREATE TABLE IF NOT EXISTS landing_pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    template_id INT DEFAULT NULL,
    page_content TEXT, -- JSON structure containing page sections and content
    seo_settings TEXT, -- JSON for SEO meta data
    design_settings TEXT, -- JSON for colors, fonts, layout settings
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    is_homepage BOOLEAN DEFAULT FALSE,
    custom_css TEXT,
    custom_js TEXT,
    analytics_code TEXT,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL,
    view_count INT DEFAULT 0,
    conversion_count INT DEFAULT 0,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_template_id (template_id),
    INDEX idx_created_by (created_by),
    INDEX idx_is_homepage (is_homepage),
    FOREIGN KEY (created_by) REFERENCES accounts(id) ON DELETE SET NULL
);

-- Landing page templates
CREATE TABLE IF NOT EXISTS landing_page_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100), -- 'business', 'ecommerce', 'portfolio', 'event', etc.
    preview_image VARCHAR(500),
    template_structure TEXT NOT NULL, -- JSON structure defining sections and layout
    default_content TEXT, -- JSON with default content for this template
    css_framework VARCHAR(50) DEFAULT 'bootstrap',
    is_premium BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    usage_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_is_active (is_active),
    INDEX idx_usage_count (usage_count)
);

-- Page sections/blocks
CREATE TABLE IF NOT EXISTS landing_page_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    section_type VARCHAR(100) NOT NULL, -- 'hero', 'features', 'testimonials', 'cta', 'contact', etc.
    section_name VARCHAR(255),
    section_content TEXT NOT NULL, -- JSON content for this section
    section_settings TEXT, -- JSON settings (background, spacing, animations, etc.)
    sort_order INT DEFAULT 0,
    is_visible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_page_id (page_id),
    INDEX idx_section_type (section_type),
    INDEX idx_sort_order (sort_order),
    INDEX idx_is_visible (is_visible),
    FOREIGN KEY (page_id) REFERENCES landing_pages(id) ON DELETE CASCADE
);

-- Section templates/blocks library
CREATE TABLE IF NOT EXISTS section_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    section_type VARCHAR(100) NOT NULL,
    description TEXT,
    preview_image VARCHAR(500),
    template_html TEXT NOT NULL,
    template_css TEXT,
    template_js TEXT,
    default_content TEXT, -- JSON with default content
    settings_schema TEXT, -- JSON schema for configurable options
    category VARCHAR(100),
    is_premium BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    usage_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_section_type (section_type),
    INDEX idx_category (category),
    INDEX idx_is_active (is_active)
);

-- Landing page media/assets
CREATE TABLE IF NOT EXISTS landing_page_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    media_type ENUM('image', 'video', 'audio', 'document') NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    alt_text VARCHAR(255),
    caption TEXT,
    usage_context VARCHAR(100), -- 'hero', 'gallery', 'background', etc.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_page_id (page_id),
    INDEX idx_media_type (media_type),
    INDEX idx_usage_context (usage_context),
    FOREIGN KEY (page_id) REFERENCES landing_pages(id) ON DELETE CASCADE
);

-- Landing page analytics
CREATE TABLE IF NOT EXISTS landing_page_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    date_tracked DATE NOT NULL,
    page_views INT DEFAULT 0,
    unique_visitors INT DEFAULT 0,
    bounce_rate DECIMAL(5,2) DEFAULT 0.00,
    avg_time_on_page INT DEFAULT 0, -- in seconds
    conversions INT DEFAULT 0,
    conversion_rate DECIMAL(5,2) DEFAULT 0.00,
    traffic_sources TEXT, -- JSON with referrer data
    device_breakdown TEXT, -- JSON with device/browser data
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_page_date (page_id, date_tracked),
    INDEX idx_page_id (page_id),
    INDEX idx_date_tracked (date_tracked),
    FOREIGN KEY (page_id) REFERENCES landing_pages(id) ON DELETE CASCADE
);

-- Landing page forms integration
CREATE TABLE IF NOT EXISTS landing_page_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    form_id INT DEFAULT NULL,
    form_type ENUM('contact', 'newsletter', 'lead_capture', 'survey', 'custom') NOT NULL,
    form_settings TEXT, -- JSON configuration
    position VARCHAR(100), -- 'header', 'footer', 'sidebar', 'popup', etc.
    is_active BOOLEAN DEFAULT TRUE,
    submission_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_page_id (page_id),
    INDEX idx_form_id (form_id),
    INDEX idx_form_type (form_type),
    FOREIGN KEY (page_id) REFERENCES landing_pages(id) ON DELETE CASCADE,
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE SET NULL
);

-- A/B Testing for landing pages
CREATE TABLE IF NOT EXISTS landing_page_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    variant_name VARCHAR(255) NOT NULL,
    variant_content TEXT NOT NULL, -- JSON with variant-specific content
    traffic_percentage DECIMAL(5,2) DEFAULT 50.00,
    is_active BOOLEAN DEFAULT TRUE,
    views INT DEFAULT 0,
    conversions INT DEFAULT 0,
    conversion_rate DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_page_id (page_id),
    INDEX idx_is_active (is_active),
    FOREIGN KEY (page_id) REFERENCES landing_pages(id) ON DELETE CASCADE
);

-- Landing page configuration settings
CREATE TABLE IF NOT EXISTS setting_landing_pages_config (
    setting_name VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json', 'array') DEFAULT 'string',
    description TEXT,
    category VARCHAR(50) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category)
);

-- Insert default landing page system settings
INSERT INTO setting_landing_pages_config (setting_name, setting_value, setting_type, description, category) VALUES
('landing_pages_enabled', 'true', 'boolean', 'Enable or disable the landing page system', 'general'),
('default_template', '1', 'number', 'Default template ID for new pages', 'general'),
('max_pages_per_user', '50', 'number', 'Maximum pages per user (0 = unlimited)', 'limits'),
('enable_custom_css', 'true', 'boolean', 'Allow custom CSS in pages', 'customization'),
('enable_custom_js', 'true', 'boolean', 'Allow custom JavaScript in pages', 'customization'),
('enable_analytics', 'true', 'boolean', 'Enable built-in analytics tracking', 'analytics'),
('google_analytics_id', '', 'string', 'Google Analytics tracking ID', 'analytics'),
('facebook_pixel_id', '', 'string', 'Facebook Pixel ID', 'analytics'),
('enable_ab_testing', 'true', 'boolean', 'Enable A/B testing features', 'testing'),
('media_upload_max_size', '10485760', 'number', 'Maximum media upload size in bytes', 'media'),
('allowed_media_types', '["jpg", "jpeg", "png", "gif", "svg", "mp4", "webm", "pdf"]', 'json', 'Allowed media file types', 'media'),
('media_directory', 'uploads/landing-pages/', 'string', 'Directory for landing page media', 'media'),
('auto_save_interval', '30', 'number', 'Auto-save interval in seconds', 'editor'),
('enable_revision_history', 'true', 'boolean', 'Enable page revision history', 'editor'),
('max_revisions', '10', 'number', 'Maximum revisions to keep per page', 'editor'),
('default_seo_title', 'Welcome to Our Website', 'string', 'Default SEO title for new pages', 'seo'),
('default_seo_description', 'Discover amazing products and services on our website.', 'string', 'Default SEO description', 'seo'),
('enable_schema_markup', 'true', 'boolean', 'Enable automatic schema markup', 'seo'),
('cache_enabled', 'true', 'boolean', 'Enable page caching for better performance', 'performance'),
('cache_duration', '3600', 'number', 'Cache duration in seconds', 'performance'),
('minify_html', 'true', 'boolean', 'Minify HTML output', 'performance')
ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    updated_at = CURRENT_TIMESTAMP;

-- Insert default templates
INSERT INTO landing_page_templates (name, description, category, template_structure, default_content, css_framework) VALUES
('Business Landing Page', 'Professional business landing page with hero, features, and contact sections', 'business',
'{"sections": ["hero", "features", "about", "testimonials", "contact"], "layout": "full-width", "navigation": "top"}',
'{"hero": {"title": "Welcome to Our Business", "subtitle": "We provide excellent services for your needs", "cta_text": "Get Started", "background_type": "image"}, "features": {"title": "Our Features", "items": [{"title": "Feature 1", "description": "Description of feature 1"}, {"title": "Feature 2", "description": "Description of feature 2"}, {"title": "Feature 3", "description": "Description of feature 3"}]}}',
'bootstrap'),

('Product Launch', 'Perfect for launching new products with countdown and pre-order functionality', 'ecommerce',
'{"sections": ["hero", "product-showcase", "features", "pricing", "countdown", "pre-order"], "layout": "centered", "navigation": "hidden"}',
'{"hero": {"title": "Revolutionary New Product", "subtitle": "Coming Soon - Be the First to Experience It", "cta_text": "Notify Me", "background_type": "video"}, "product-showcase": {"title": "Product Gallery", "images": []}, "countdown": {"launch_date": "", "title": "Launch Countdown"}}',
'bootstrap'),

('Event Registration', 'Event landing page with registration form and event details', 'event',
'{"sections": ["hero", "event-details", "speakers", "schedule", "registration", "location"], "layout": "full-width", "navigation": "sticky"}',
'{"hero": {"title": "Join Our Amazing Event", "subtitle": "Network, Learn, and Grow with Industry Leaders", "event_date": "", "location": ""}, "event-details": {"description": "", "highlights": []}, "registration": {"form_title": "Register Now", "form_fields": ["name", "email", "company", "title"]}}',
'bootstrap'),

('Portfolio Showcase', 'Creative portfolio layout for showcasing work and projects', 'portfolio',
'{"sections": ["hero", "about", "portfolio", "services", "testimonials", "contact"], "layout": "grid", "navigation": "side"}',
'{"hero": {"title": "Creative Professional", "subtitle": "Bringing Ideas to Life Through Design", "cta_text": "View Portfolio"}, "portfolio": {"title": "My Work", "filter_categories": ["Web Design", "Branding", "Photography"]}, "about": {"title": "About Me", "bio": ""}}',
'bootstrap')
ON DUPLICATE KEY UPDATE name = name;

-- Insert default section templates
INSERT INTO section_templates (name, section_type, description, template_html, default_content, category) VALUES
('Hero Banner with CTA', 'hero', 'Full-width hero section with background image and call-to-action button',
'<div class="hero-section" style="background-image: url({{background_image}});">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h1 class="hero-title">{{title}}</h1>
                <p class="hero-subtitle">{{subtitle}}</p>
                <a href="{{cta_link}}" class="btn btn-primary btn-lg">{{cta_text}}</a>
            </div>
        </div>
    </div>
</div>',
'{"title": "Welcome to Our Website", "subtitle": "Discover amazing products and services", "cta_text": "Get Started", "cta_link": "#contact", "background_image": ""}',
'hero'),

('Feature Grid', 'features', '3-column feature grid with icons and descriptions',
'<div class="features-section py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2>{{section_title}}</h2>
                <p>{{section_subtitle}}</p>
            </div>
        </div>
        <div class="row">
            {{#each features}}
            <div class="col-md-4 mb-4">
                <div class="feature-item text-center">
                    <div class="feature-icon mb-3">
                        <i class="{{icon}}" style="font-size: 3rem; color: {{icon_color}};"></i>
                    </div>
                    <h4>{{title}}</h4>
                    <p>{{description}}</p>
                </div>
            </div>
            {{/each}}
        </div>
    </div>
</div>',
'{"section_title": "Our Features", "section_subtitle": "Why choose us", "features": [{"title": "Feature 1", "description": "Description", "icon": "fas fa-star", "icon_color": "#007bff"}]}',
'features'),

('Contact Form', 'contact', 'Contact form with validation',
'<div class="contact-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="text-center mb-4">{{section_title}}</h2>
                <form id="contact-form" class="contact-form">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="message" rows="5" placeholder="Your Message" required></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">{{submit_text}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>',
'{"section_title": "Get In Touch", "submit_text": "Send Message"}',
'contact')
ON DUPLICATE KEY UPDATE name = name;

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_pages_status_published ON landing_pages(status, published_at);
CREATE INDEX IF NOT EXISTS idx_sections_page_order ON landing_page_sections(page_id, sort_order);
CREATE INDEX IF NOT EXISTS idx_analytics_date_range ON landing_page_analytics(date_tracked, page_id);
CREATE INDEX IF NOT EXISTS idx_templates_category_active ON landing_page_templates(category, is_active);

COMMIT;
