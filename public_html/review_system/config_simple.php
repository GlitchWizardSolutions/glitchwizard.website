<?php
/*
PAGE NAME  : config_simple.php
LOCATION   : public_html/review_system/config_simple.php
DESCRIPTION: Simplified review system configuration for testing.
FUNCTION   : Basic configuration without main system integration.
CHANGE LOG : 2025-08-12 - Created for troubleshooting
*/

// Basic database configuration - you may need to adjust these
if (!defined('db_host')) {
    define('db_host', 'localhost');
}
if (!defined('db_user')) {
    define('db_user', 'root');
}
if (!defined('db_pass')) {
    define('db_pass', '');
}
if (!defined('db_charset')) {
    define('db_charset', 'utf8');
}

// Reviews database name
if (!defined('reviews_db_name')) {
    define('reviews_db_name', 'phpreviews');
}

/* Reviews */
// Authentication will require the user to login or register before they can write a review.
define('authentication_required',true);
// If enabled, the user will be able to write only one review per page.
define('one_review_per_reviewer',false);
// If enabled, the user will be able to attach images to reviews.
define('upload_images_allowed',true);
// The directory where the uploaded images will be saved.
define('images_directory','uploads/');
// The maximum size of the uploaded image in bytes.
define('max_allowed_upload_image_size','500000');
// If enabled, the reviews will require approval before they are displayed.
define('reviews_approval_required',true);
// The maximum number of characters the user can enter in the review.
define('max_review_chars','600');
// The maximum number of stars the user can select.
define('max_stars','5');
// The reviews directory URL (e.g. http://example.com/reviews/).
define('reviews_directory_url','/review_system/');
/* Mail */
// Send mail to the customers, etc?
define('mail_enabled',false);
// This is the email address that will be used to send emails.
define('mail_from','noreply@example.com');
// This is the email address that will receive the notifications.
define('notification_email','notifications@example.com');
// The name of your business.
define('mail_name','Your Business Name');
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
