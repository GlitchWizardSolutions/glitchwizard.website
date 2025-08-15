<?php
// Your MySQL database hostname.
define('db_host','localhost');
// Your MySQL database username.
define('db_user','root');
// Your MySQL database password.
define('db_pass','');
// Your MySQL database name.
define('db_name','phpcontact');
// Your MySQL database charset.
define('db_charset','utf8');
// Admin credentials
// Admin username
define('admin_user','admin');
// Admin password
define('admin_pass','admin');
// General settings
// Review images directory
define('file_upload_directory', 'uploads/');
// Maximum allowed upload file size (500KB)
define('max_allowed_upload_file_size', 512000);
/* Mail */
// This is the email address that will be used to send emails.
define('mail_from','noreply@example.com');
// Where should we send the contact form mail?
define('support_email', 'support@yourwebsite.com');
// If enabled, the mail will be sent using SMTP.
define('SMTP',false);
// Your SMTP hostname.
define('smtp_host','smtp.example.com');
// Your SMTP port number.
define('smtp_port',465);
// Your SMTP username.
define('smtp_user','user@example.com');
// Your SMTP Password.
define('smtp_pass','secret');
?>