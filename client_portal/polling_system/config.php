<?php
// Your MySQL database hostname.
define('db_host','localhost');
// Your MySQL database username.
define('db_user','root');
// Your MySQL database password.
define('db_pass','');
// Your MySQL database name.
define('db_name','phppoll_advanced');
// Database charset, only change this if utf8 is not supported by your language.
define('db_charset','utf8');
// Prevents the user from voting multiple times in the same poll.
define('one_vote_per_poll',true);
// The method of duplication checking, 'ip' for IP address, 'cookie' for cookie. 
define('duplication_checking','ip');
// If enabled, approval is required for new polls.
define('approval_required',false);
// Who can create polls? 'everyone' for all users, 'user' for logged in users, 'admin' for admins only.
// List:everyone=Everyone,user=User,admin=Admin
define('create_polls','everyone');
// Who can edit polls? 'everyone' for all users, 'user' for logged in users, 'admin' for admins only.
// List:everyone=Everyone,user=User,admin=Admin
define('edit_polls','admin');
// Hide the results until the user has voted.
define('hide_results_until_voting',false);
/* Images */
// Enable images for polls.
define('images_enabled',false);
// The directory where the images will be uploaded.
define('images_directory','images/');
// The maximum file size for images in bytes.
define('images_max_size',1000000);
?>