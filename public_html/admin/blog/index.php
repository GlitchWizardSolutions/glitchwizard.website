<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOCATION: public_html/admin/blog/index.php
 * LOG: Blog admin area authentication and redirect handler
 * PRODUCTION: [To be updated on deployment]
 */

include "header.php";

// Redirect authenticated users to dashboard
echo '<meta http-equiv="refresh" content="0; url=dashboard.php" />';
exit;
?>