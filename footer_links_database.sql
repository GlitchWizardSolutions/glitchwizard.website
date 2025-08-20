-- Footer Links Database Structure
-- This creates a proper database table for managing footer useful links

-- Create footer_links table
CREATE TABLE IF NOT EXISTS setting_footer_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    label VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(50) DEFAULT 'bi-link-45deg',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert useful links for Burden to Blessings
INSERT INTO setting_footer_links (label, url, icon, sort_order, is_active) VALUES
('Reviews', '#reviews', 'bi-star', 1, TRUE),
('FAQs', '#faq', 'bi-question-circle', 2, TRUE),
('Terms of Service', 'policy-terms.php', 'bi-file-text', 3, TRUE),
('Privacy Policy', 'policy-privacy.php', 'bi-shield-check', 4, TRUE),
('Accessibility Policy', 'policy-accessibility.php', 'bi-universal-access', 5, TRUE)
ON DUPLICATE KEY UPDATE
    url = VALUES(url),
    icon = VALUES(icon),
    sort_order = VALUES(sort_order),
    is_active = VALUES(is_active),
    updated_at = CURRENT_TIMESTAMP;
