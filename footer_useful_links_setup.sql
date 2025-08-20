-- Footer Useful Links Database Setup
-- This creates the database table for managing useful links in the footer

-- Create footer useful links table
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

-- Show the results
SELECT * FROM setting_footer_useful_links ORDER BY display_order ASC;
