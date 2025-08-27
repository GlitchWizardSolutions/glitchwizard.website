<?php
/*
PAGE NAME  : unsubscribe.php
LOCATION   : public_html/unsubscribe.php
DESCRIPTION: This page handles the unsubscription process for the newsletter.
FUNCTION   : Users can unsubscribe from the blog newsletter.
INPUT      : Email address to unsubscribe. http://localhost/gws-universal-hybrid-app/unsubscribe.php?email=user@example.com
HOW TO USE : Access this page with the email parameter to unsubscribe.
CHANGE LOG : Initial creation of unsubscribe.php to handle unsubscription.
2025-08-24 : Added confirmation message for successful unsubscription.  
CHANGE LOG : Initial creation of unsubscribe.php to handle unsubscription.
2025-08-24 : Added confirmation message for successful unsubscription.
CHANGE LOG : Initial creation of unsubscribe.php to handle unsubscription.
2025-08-24 : Added confirmation message for successful unsubscription.
2025-08-25 : Improved comment system with user avatars.
2025-08-26 : Enhanced SEO features for blog posts.
2025-08-05 : Refactored to use PDO and unified layout.
*/

// Include necessary files
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
include_once "assets/includes/blog_load.php";
if ($settings['sidebar_position'] == 'Left') {
	sidebar();
}
?>
    <div class="col-md-8 mb-3">
        <div class="card">
            <div class="card-header"><i class="bi bi-envelope-fill" aria-hidden="true"></i> Unsubscribe</div>
                <div class="card-body">
<?php
if (!isset($_GET['email'])) {
    echo '<meta http-equiv="refresh" content="0; url=' . $settings['site_url'] . '">';
    exit;
} else {
  	    $email = $_GET['email'];
        $stmt = $blog_pdo->prepare('SELECT * FROM blog_newsletter WHERE email = ? LIMIT 1');
	    $stmt->execute([$email]);
	    $newletter_email = $stmt->fetch(PDO::FETCH_ASSOC);
     
    if (!$newletter_email) {
        echo '<meta http-equiv="refresh" content="0; url=' . $settings['site_url'] . '">';
        exit;
        
    } else {
        $stmt = $blog_pdo->prepare('DELETE FROM blog_newsletter WHERE email = ?');
        $stmt->execute([$email]);;
        echo '<div class="alert alert-primary">You were unsubscribed successfully.</div>';
    }
}//end if there is an email or not to unsubscribe.
?>
                </div>
        </div>
    </div>
<?php
if ($settings['sidebar_position'] == 'Right') {
	sidebar();
}
?>
<?php
// Use public footer for unified branding 
include 'assets/includes/footer.php';
?>