-- 
-- SQL Script to create and populate setting_content_pricing table
-- Generated: August 16, 2025
-- Purpose: Manage pricing plans and packages for the website
--

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_pricing`
--

CREATE TABLE `setting_content_pricing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_key` varchar(100) NOT NULL,
  `plan_name` varchar(255) NOT NULL,
  `plan_description` text DEFAULT NULL,
  `plan_short_desc` varchar(500) DEFAULT NULL,
  `plan_price` varchar(100) NOT NULL,
  `plan_price_numeric` decimal(10,2) DEFAULT NULL,
  `plan_billing_period` varchar(50) DEFAULT 'monthly',
  `plan_currency` varchar(10) DEFAULT 'USD',
  `plan_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plan_features`)),
  `plan_benefits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plan_benefits`)),
  `plan_limitations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plan_limitations`)),
  `plan_button_text` varchar(100) DEFAULT 'Get Started',
  `plan_button_link` varchar(255) DEFAULT '#',
  `plan_icon` varchar(255) DEFAULT NULL,
  `plan_badge` varchar(100) DEFAULT NULL,
  `plan_color_scheme` varchar(50) DEFAULT 'primary',
  `plan_category` varchar(100) DEFAULT 'standard',
  `plan_order` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_popular` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `plan_key` (`plan_key`),
  KEY `idx_plan_category` (`plan_category`),
  KEY `idx_plan_order` (`plan_order`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Sample data for table `setting_content_pricing`
--

INSERT INTO `setting_content_pricing` (`plan_key`, `plan_name`, `plan_description`, `plan_short_desc`, `plan_price`, `plan_price_numeric`, `plan_billing_period`, `plan_currency`, `plan_features`, `plan_benefits`, `plan_limitations`, `plan_button_text`, `plan_button_link`, `plan_icon`, `plan_badge`, `plan_color_scheme`, `plan_category`, `plan_order`, `is_featured`, `is_popular`, `is_active`) VALUES
('starter', 'Starter Plan', 'Perfect for individuals and small projects getting started with our services', 'Ideal for personal use and small projects', '$99', 99.00, 'monthly', 'USD', '["Up to 5 projects", "5GB storage", "Email support", "Basic templates", "Mobile responsive design"]', '["Quick setup", "Cost-effective", "Perfect for beginners", "No long-term commitment"]', '["Limited storage", "Email support only", "Basic features only"]', 'Get Started', '/contact', 'fas fa-rocket', NULL, 'primary', 'standard', 1, 0, 0, 1),

('professional', 'Professional Plan', 'Our most popular plan for growing businesses and professional services', 'Best value for growing businesses', '$199', 199.00, 'monthly', 'USD', '["Unlimited projects", "50GB storage", "Priority phone & email support", "Premium templates", "Advanced customization", "SEO optimization", "Analytics dashboard", "Social media integration"]', '["Scalable solution", "Priority support", "Advanced features", "Great ROI", "Professional appearance"]', '["Phone support during business hours only"]', 'Choose Professional', '/contact', 'fas fa-star', 'Most Popular', 'success', 'professional', 2, 1, 1, 1),

('enterprise', 'Enterprise Plan', 'Comprehensive solution for large organizations with advanced needs', 'Full-featured solution for enterprises', 'Contact Us', NULL, 'custom', 'USD', '["Unlimited everything", "Unlimited storage", "24/7 dedicated support", "Custom development", "White-label solutions", "API access", "Advanced security", "Custom integrations", "Training included", "SLA guarantee"]', '["Complete customization", "Dedicated support", "Maximum flexibility", "Enterprise security", "Scalable infrastructure"]', '["Custom pricing", "Minimum contract terms may apply"]', 'Contact Sales', '/contact', 'fas fa-building', 'Enterprise', 'warning', 'enterprise', 3, 0, 0, 1),

('basic_service', 'Basic Service Package', 'Essential services to get your project off the ground', 'Basic service package for quick start', '$149', 149.00, 'one-time', 'USD', '["Initial consultation", "Basic setup", "1 revision", "30-day support", "Mobile optimization"]', '["Quick turnaround", "Affordable pricing", "Professional quality"]', '["Limited revisions", "Basic features only"]', 'Order Now', '/contact', 'fas fa-tools', NULL, 'info', 'service', 4, 0, 0, 1),

('premium_service', 'Premium Service Package', 'Comprehensive service package with advanced features and support', 'Complete service solution', '$399', 399.00, 'one-time', 'USD', '["Detailed consultation", "Full setup & configuration", "Unlimited revisions", "90-day support", "SEO optimization", "Performance optimization", "Training session", "Documentation"]', '["Comprehensive solution", "Unlimited revisions", "Extended support", "Training included"]', '["Higher investment required"]', 'Get Premium', '/contact', 'fas fa-crown', 'Recommended', 'primary', 'service', 5, 1, 0, 1),

('consultation', 'Consultation Only', 'Professional consultation to help plan your project', 'Expert consultation and planning', '$75', 75.00, 'hourly', 'USD', '["1-hour consultation", "Project assessment", "Recommendations", "Follow-up email", "Resource list"]', '["Expert guidance", "Clear direction", "Cost-effective planning"]', '["Consultation only", "No implementation included"]', 'Book Consultation', '/contact', 'fas fa-comments', NULL, 'secondary', 'consultation', 6, 0, 0, 1);

-- --------------------------------------------------------

--
-- Additional indexes for optimization
--

ALTER TABLE `setting_content_pricing` ADD INDEX `idx_pricing_featured` (`is_featured`, `plan_order`);
ALTER TABLE `setting_content_pricing` ADD INDEX `idx_pricing_popular` (`is_popular`, `plan_order`);
ALTER TABLE `setting_content_pricing` ADD INDEX `idx_pricing_active_order` (`is_active`, `plan_order`);

COMMIT;
