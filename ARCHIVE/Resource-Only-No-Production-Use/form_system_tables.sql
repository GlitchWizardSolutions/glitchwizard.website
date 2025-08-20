-- Form System Database Tables
-- GWS Universal Hybrid Application - Form System Integration
-- This creates a comprehensive form builder and management system

-- Create database if it doesn't exist
-- CREATE DATABASE IF NOT EXISTS gws_universal_app;
-- USE gws_universal_app;

-- Main forms table
CREATE TABLE IF NOT EXISTS forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    slug VARCHAR(255) UNIQUE NOT NULL,
    settings TEXT, -- JSON for form settings (success message, redirect, email notifications, etc.)
    status ENUM('active', 'inactive', 'draft') DEFAULT 'active',
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    submit_count INT DEFAULT 0,
    last_submission TIMESTAMP NULL,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_created_by (created_by),
    FOREIGN KEY (created_by) REFERENCES accounts(id) ON DELETE SET NULL
);

-- Form fields table
CREATE TABLE IF NOT EXISTS form_fields (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_label VARCHAR(255) NOT NULL,
    field_type ENUM('text', 'email', 'number', 'textarea', 'select', 'radio', 'checkbox', 'file', 'date', 'time', 'url', 'tel', 'password', 'hidden') NOT NULL,
    field_options TEXT, -- JSON for options (select/radio choices, validation rules, etc.)
    field_placeholder VARCHAR(255),
    field_help_text TEXT,
    is_required BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    validation_rules TEXT, -- JSON for custom validation
    conditional_logic TEXT, -- JSON for show/hide conditions
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_form_id (form_id),
    INDEX idx_sort_order (sort_order),
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE
);

-- Form submissions table
CREATE TABLE IF NOT EXISTS form_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    submission_data TEXT NOT NULL, -- JSON containing all field data
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer VARCHAR(500),
    status ENUM('new', 'read', 'archived', 'spam') DEFAULT 'new',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_form_id (form_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES accounts(id) ON DELETE SET NULL
);

-- Form files (for file uploads)
CREATE TABLE IF NOT EXISTS form_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_submission_id (submission_id),
    INDEX idx_field_name (field_name),
    FOREIGN KEY (submission_id) REFERENCES form_submissions(id) ON DELETE CASCADE
);

-- Form email templates
CREATE TABLE IF NOT EXISTS form_email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    template_type ENUM('admin_notification', 'user_confirmation', 'autoresponder') NOT NULL,
    template_name VARCHAR(255) NOT NULL,
    subject_line VARCHAR(255) NOT NULL,
    email_body TEXT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    send_to TEXT, -- JSON array of email addresses or field names
    send_from VARCHAR(255),
    send_from_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_form_id (form_id),
    INDEX idx_template_type (template_type),
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE
);

-- Form analytics/statistics
CREATE TABLE IF NOT EXISTS form_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    date_tracked DATE NOT NULL,
    views INT DEFAULT 0,
    submissions INT DEFAULT 0,
    conversion_rate DECIMAL(5,2) DEFAULT 0.00,
    bounce_rate DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_form_date (form_id, date_tracked),
    INDEX idx_form_id (form_id),
    INDEX idx_date_tracked (date_tracked),
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE
);

-- Form integrations (for third-party services)
CREATE TABLE IF NOT EXISTS form_integrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    integration_type ENUM('mailchimp', 'zapier', 'webhook', 'slack', 'google_sheets', 'custom') NOT NULL,
    integration_name VARCHAR(255) NOT NULL,
    configuration TEXT NOT NULL, -- JSON configuration for the integration
    is_active BOOLEAN DEFAULT TRUE,
    last_sync TIMESTAMP NULL,
    sync_status ENUM('success', 'failed', 'pending') DEFAULT 'pending',
    error_log TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_form_id (form_id),
    INDEX idx_integration_type (integration_type),
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE
);

-- Form configuration settings
CREATE TABLE IF NOT EXISTS setting_forms_config (
    setting_name VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json', 'array') DEFAULT 'string',
    description TEXT,
    category VARCHAR(50) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category)
);

-- Insert default form system settings
INSERT INTO setting_forms_config (setting_name, setting_value, setting_type, description, category) VALUES
('forms_enabled', 'true', 'boolean', 'Enable or disable the forms system', 'general'),
('max_file_size', '10485760', 'number', 'Maximum file upload size in bytes (10MB)', 'uploads'),
('allowed_file_types', '["jpg", "jpeg", "png", "gif", "pdf", "doc", "docx", "txt"]', 'json', 'Allowed file extensions for uploads', 'uploads'),
('upload_directory', 'uploads/forms/', 'string', 'Directory for form file uploads', 'uploads'),
('spam_protection', 'true', 'boolean', 'Enable spam protection features', 'security'),
('honeypot_enabled', 'true', 'boolean', 'Enable honeypot spam protection', 'security'),
('rate_limiting', '5', 'number', 'Maximum submissions per IP per hour', 'security'),
('default_success_message', 'Thank you for your submission!', 'string', 'Default success message for forms', 'messages'),
('default_error_message', 'There was an error processing your submission. Please try again.', 'string', 'Default error message for forms', 'messages'),
('admin_notification_email', '', 'string', 'Default admin email for form notifications', 'notifications'),
('email_from_name', 'Form System', 'string', 'Default from name for email notifications', 'notifications'),
('email_from_address', '', 'string', 'Default from address for email notifications', 'notifications'),
('form_css_framework', 'bootstrap', 'string', 'CSS framework for form rendering', 'appearance'),
('auto_delete_submissions', '0', 'number', 'Auto delete submissions after X days (0 = never)', 'cleanup'),
('enable_form_analytics', 'true', 'boolean', 'Enable form analytics tracking', 'analytics')
ON DUPLICATE KEY UPDATE 
    setting_value = VALUES(setting_value),
    updated_at = CURRENT_TIMESTAMP;

-- Insert sample form for testing
INSERT INTO forms (title, description, slug, settings, status, created_by) VALUES
('Contact Form', 'Basic contact form for website inquiries', 'contact-form', 
'{"success_message": "Thank you for contacting us! We will get back to you soon.", "redirect_url": "", "send_notifications": true, "notification_emails": ["admin@example.com"], "save_submissions": true, "allow_duplicates": false}', 
'active', 1)
ON DUPLICATE KEY UPDATE title = title;

-- Get the form ID for field creation
SET @form_id = (SELECT id FROM forms WHERE slug = 'contact-form' LIMIT 1);

-- Insert sample form fields
INSERT INTO form_fields (form_id, field_name, field_label, field_type, field_placeholder, is_required, sort_order) VALUES
(@form_id, 'name', 'Full Name', 'text', 'Enter your full name', true, 1),
(@form_id, 'email', 'Email Address', 'email', 'Enter your email address', true, 2),
(@form_id, 'subject', 'Subject', 'text', 'Enter the subject', true, 3),
(@form_id, 'message', 'Message', 'textarea', 'Enter your message', true, 4),
(@form_id, 'phone', 'Phone Number', 'tel', 'Enter your phone number (optional)', false, 5)
ON DUPLICATE KEY UPDATE field_name = field_name;

-- Insert sample email template
INSERT INTO form_email_templates (form_id, template_type, template_name, subject_line, email_body, send_to) VALUES
(@form_id, 'admin_notification', 'Contact Form Notification', 'New Contact Form Submission', 
'<h2>New Contact Form Submission</h2>
<p><strong>Name:</strong> {{name}}</p>
<p><strong>Email:</strong> {{email}}</p>
<p><strong>Subject:</strong> {{subject}}</p>
<p><strong>Phone:</strong> {{phone}}</p>
<p><strong>Message:</strong></p>
<p>{{message}}</p>
<p><small>Submitted on {{submission_date}} from IP: {{ip_address}}</small></p>', 
'["admin@example.com"]')
ON DUPLICATE KEY UPDATE template_name = template_name;

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_forms_status_created ON forms(status, created_at);
CREATE INDEX IF NOT EXISTS idx_submissions_form_status ON form_submissions(form_id, status);
CREATE INDEX IF NOT EXISTS idx_fields_form_order ON form_fields(form_id, sort_order);
CREATE INDEX IF NOT EXISTS idx_analytics_date_range ON form_analytics(date_tracked, form_id);

COMMIT;
