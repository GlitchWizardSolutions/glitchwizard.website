<?php
// Your MySQL database hostname.
define('db_host','localhost');
// Your MySQL database username.
define('db_user','root');
// Your MySQL database password.
define('db_pass','');
// Your MySQL database name.
define('db_name','phpcalendar');
// Your MySQL database charset.
define('db_charset','utf8');
// If checked, photo uploads are disabled.
define('disable_photo_uploads',false);
// The directory where the uploaded photos will be saved.
define('upload_directory','uploads/');
// If checked, the ability to manage events is disabled on the calendar. You can still manage events in the admin panel.
define('disable_event_management',false);
// Display timestamps in the calendar.
define('display_timestamps',true);
// Selectable colors for the events (comma separated).
define('event_colors','#5373ae,#2d7d9a,#8a6d3b,#c0994d,#c74f4f,#7a8a8a,#5c9a5e,#4d7c4d,#4d7c7d,#4d4d7d,#7d4d7d,#7d4d4d');

// Uncomment the below to output all errors
// ini_set('log_errors', true);
// ini_set('error_log', 'error.log');
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Uncomment the below to set the default timezone
// date_default_timezone_set('Europe/London');
?>