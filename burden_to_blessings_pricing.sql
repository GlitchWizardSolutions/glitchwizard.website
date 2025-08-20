-- Burden to Blessings Pricing Setup
-- Add missing columns and update homepage section titles and create foreclosure assistance pricing

-- First, add missing columns if they don't exist
ALTER TABLE setting_content_homepage 
ADD COLUMN IF NOT EXISTS pricing_section_title VARCHAR(255) DEFAULT 'Pricing',
ADD COLUMN IF NOT EXISTS pricing_section_description TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS team_section_title VARCHAR(255) DEFAULT 'Our Team',
ADD COLUMN IF NOT EXISTS team_section_description TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS portfolio_section_title VARCHAR(255) DEFAULT 'Portfolio',
ADD COLUMN IF NOT EXISTS portfolio_section_description TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS contact_section_title VARCHAR(255) DEFAULT 'Contact Us',
ADD COLUMN IF NOT EXISTS contact_section_description TEXT DEFAULT NULL;

-- Create the homepage record if it doesn't exist
INSERT IGNORE INTO setting_content_homepage (id) VALUES (1);

-- Now update the pricing section titles
UPDATE setting_content_homepage 
SET pricing_section_title = 'Our Services',
    pricing_section_description = 'We offer comprehensive foreclosure assistance and real estate solutions tailored to your family''s needs.'
WHERE id = 1;

-- Clear existing pricing data and add Burden to Blessings services
DELETE FROM setting_content_pricing WHERE plan_key IN ('starter', 'professional', 'enterprise', 'basic_service', 'premium_service', 'consultation');

-- Insert Burden to Blessings specific services
INSERT INTO `setting_content_pricing` (`plan_key`, `plan_name`, `plan_description`, `plan_short_desc`, `plan_price`, `plan_price_numeric`, `plan_billing_period`, `plan_currency`, `plan_features`, `plan_benefits`, `plan_limitations`, `plan_button_text`, `plan_button_link`, `plan_icon`, `plan_badge`, `plan_color_scheme`, `plan_category`, `plan_order`, `is_featured`, `is_popular`, `is_active`) VALUES

('consultation', 'Free Consultation', 'Get personalized help understanding your options during foreclosure', 'No-cost assessment of your situation', 'Free', 0.00, 'one-time', 'USD', '["Family situation assessment", "Foreclosure timeline review", "Available options explanation", "Resource connections", "Follow-up support"]', '["No financial commitment", "Expert guidance", "Personalized approach", "Local connections"]', '["Consultation only", "Implementation requires additional services"]', 'Get Help Now', '/contact', 'fas fa-handshake', 'No Cost', 'success', 'consultation', 1, 0, 1, 1),

('foreclosure_assistance', 'Foreclosure Assistance', 'Comprehensive support to help you navigate the foreclosure process', 'Complete foreclosure navigation support', 'Contact Us', NULL, 'custom', 'USD', '["Foreclosure process guidance", "Documentation assistance", "Lender negotiations", "Timeline management", "Legal resource connections", "Alternative solution research"]', '["Expert navigation", "Reduced stress", "Better outcomes", "Professional representation"]', '["Pricing varies by complexity", "Timeline depends on situation"]', 'Learn More', '/contact', 'fas fa-shield-alt', 'Most Popular', 'primary', 'assistance', 2, 1, 1, 1),

('home_solutions', 'Creative Home Solutions', 'Explore alternatives to foreclosure that work for your family', 'Alternative solutions to foreclosure', 'Custom Pricing', NULL, 'custom', 'USD', '["Loan modification assistance", "Short sale coordination", "Deed in lieu options", "Refinancing exploration", "Rental conversion options", "Family financial planning"]', '["Avoid foreclosure", "Preserve credit rating", "Multiple options", "Family-focused approach"]', '["Solutions depend on individual circumstances", "Not all options available for every situation"]', 'Explore Options', '/contact', 'fas fa-home', 'Recommended', 'info', 'solutions', 3, 0, 0, 1);

-- Verify the data was inserted
SELECT plan_name, plan_price, plan_order, is_featured, is_popular FROM setting_content_pricing ORDER BY plan_order;
