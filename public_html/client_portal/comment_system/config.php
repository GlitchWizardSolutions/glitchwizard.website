<?php
// Your MySQL database hostname.
define('db_host','localhost');
// Your MySQL database username.
define('db_user','root');
// Your MySQL database password.
define('db_pass','');
// Your MySQL database name.
define('db_name','phpcomments_advanced');
// Your MySQL database charset.
define('db_charset','utf8mb4');
// The directory URL where the comment files are located.
define('comments_url','http://localhost/projects/phpcomments/advanced/');
// The admin template editor to use for editing email templates, comment content, etc.
// List:tinymce=TinyMCE,textarea=Textarea
define('template_editor','tinymce');
/* Comments */
// Comments require approval before they are displayed on the website.
// List:0=No Approval Required,1=Approval Required for Guests,2=Approval Required for All Users
define('comments_approval_level',0);
// Authentication will require the user to login or register before they can write a comment.
define('authentication_required',false);
// If enabled, the user can login.
define('login_enabled',true);
// If enabled, the user can register.
define('register_enabled',true);
// Number of comments per page.
define('comments_per_page',50);
// The maximum number of nested replies.
define('max_nested_replies',2);
// The minimum number of characters the user can enter in the comment.
define('min_comment_chars',3);
// The maximum number of characters the user can enter in the comment.
define('max_comment_chars',1000);
// The maximum number of minutes the user has to edit their comment after posting.
define('max_comment_edit_time',60);
// The time in seconds after which the user can comment again after posting a comment. Set to 0 to disable cooldown.
define('comment_cooldown_time',0);
// If enabled, the user can search for comments.
define('search_enabled',false);
// If enabled, images will be allowed in comments.
define('images_enabled',true);
// If enabled, profile photos will be allowed in comments.
define('profile_photos_enabled',true);
// If enabled, profile websites will be allowed in comments.
define('profile_websites_enabled',true);
/* Mail */
// If enabled, the website will send an email to the client when a new invoice is created.
define('mail_enabled',false);
// Send mail from which address?
define('mail_from','noreply@example.com');
// The name of your website/business.
define('mail_name','Your Website Name');
// The email address to send notification emails to.
define('notification_email','notifications@example.com');
// Is SMTP server?
define('SMTP',false);
// The SMTP Secure connection type (ssl, tls).
define('smtp_secure','ssl');
// This can be found in your email provider's settings.
define('smtp_host','smtp.example.com');
// This can be found in your email provider's settings.
define('smtp_port',465);
// This can be found in your email provider's settings.
define('smtp_user','user@example.com');
// This can be found in your email provider's settings.
define('smtp_pass','secret');
/* OAuth */
// Google OAuth will enable your users to login with Google.
define('google_oauth_enabled',false);
// The OAuth client ID associated with your API console account.
define('google_oauth_client_id','YOUR_CLIENT_ID');
// The OAuth client secret associated with your API console account.
define('google_oauth_client_secret','YOUR_CLIENT_SECRET');
// The URL to the Google OAuth file.
define('google_oauth_redirect_uri','https://example.com/google-oauth.php');
// Facebook OAuth will enable your users to login with Facebook.
define('facebook_oauth_enabled',false);
// The OAuth App ID associated with your Facebook App.
define('facebook_oauth_app_id','YOUR_APP_ID');
// The OAuth App secret associated with your Facebook App.
define('facebook_oauth_app_secret','YOUR_APP_SECRET');
// The URL to the Facebook OAuth file.
define('facebook_oauth_redirect_uri','https://example.com/facebook-oauth.php');
// X OAuth will enable your users to login with X.
define('x_oauth_enabled',false);
// The OAuth client ID associated with your X account.
define('x_oauth_client_id','YOUR_CLIENT_ID');
// The OAuth client secret associated with your X account.
define('x_oauth_client_secret','YOUR_CLIENT_SECRET');
// The URL to the X OAuth file.
define('x_oauth_redirect_uri','https://example.com/x-oauth.php');
?>