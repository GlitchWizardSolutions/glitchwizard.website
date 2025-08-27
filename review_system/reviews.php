<?php
/*
PAGE NAME  : reviews.php
LOCATION   : public_html/review_system/reviews.php
DESCRIPTION: Review system AJAX endpoint integrated with main system database.
FUNCTION   : Handle review operations and return HTML fragments for AJAX requests.
CHANGE LOG : 2025-08-12 - Integrated with main system database configuration
*/

// Initialize sessions if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the review system config file
include 'config.php';
// Namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Connect to database using the PDO interface and reviews database
try {
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . reviews_db_name . ';charset=' . db_charset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to database! Error: ' . $exception->getMessage() . 
         ' | Host: ' . db_host . ' | Database: ' . reviews_db_name . ' | User: ' . db_user);
}
// The following function will be used to assign a unique icon color to our users
function color_from_string($string) {
    // The list of hex colors
    $colors = ['#34568B','#FF6F61','#6B5B95','#88B04B','#F7CAC9','#92A8D1','#955251','#B565A7','#009B77','#DD4124','#D65076','#45B8AC','#EFC050','#5B5EA6','#9B2335','#DFCFBE','#BC243C','#C3447A','#363945','#939597','#E0B589','#926AA6','#0072B5','#E9897E','#B55A30','#4B5335','#798EA4','#00758F','#FA7A35','#6B5876','#B89B72','#282D3C','#C48A69','#A2242F','#006B54','#6A2E2A','#6C244C','#755139','#615550','#5A3E36','#264E36','#577284','#6B5B95','#944743','#00A591','#6C4F3D','#BD3D3A','#7F4145','#485167','#5A7247','#D2691E','#F7786B','#91A8D0','#4C6A92','#838487','#AD5D5D','#006E51','#9E4624'];
    // Find color based on the string
    $colorIndex = hexdec(substr(sha1($string), 0, 10)) % count($colors);
    // Return the hex color
    return $colors[$colorIndex];
}
// Below function will convert datetime to time elapsed string.
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $w = floor($diff->d / 7);
    $diff->d -= $w * 7;
    $string = ['y' => 'year','m' => 'month','w' => 'week','d' => 'day','h' => 'hour','i' => 'minute','s' => 'second'];
    foreach ($string as $k => &$v) {
        if ($k == 'w' && $w) {
            $v = $w . ' week' . ($w > 1 ? 's' : '');
        } else if (isset($diff->$k) && $diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
// Page ID needs to exist, this is used to determine which reviews are for which page.
if (isset($_GET['page_id'])) {
    // Note: Authentication is handled by the main site's auth.php system
    // Users must be logged in through auth.php before accessing review functionality
    // Get type
    $type = isset($_GET['type']) && $_GET['type'] == 'stars' ? 'stars' : 'full';
    
    // Check for display mode parameters
    $chart_only = isset($_GET['chart_only']);
    $reviews_only = isset($_GET['reviews_only']);
    // IF the user submits the review form
    if ($type == 'full' && isset($_POST['rating'], $_POST['content'])) {
        // Check if user has already posted a review
        if (isset($_COOKIE['review' . $_GET['page_id']]) && one_review_per_reviewer) {
            exit('Error: You have already posted a review!');
        }
        // Ensure content doesn't exceed the limit
        if (strlen($_POST['content']) > max_review_chars) {
            exit('Error: Review content must be no longer than ' . max_review_chars . ' characters long!');
        }
        // Display name must contain only characters and numbers.
        if (isset($_POST['name']) && !preg_match('/^[a-zA-Z\s]+$/', $_POST['name'])) {
            exit('Error: Display name must contain only letters and numbers!');
        }
        // Check if authentication required - integrate with main site auth
        if (authentication_required && !isset($_SESSION['loggedin'])) {
            exit('Error: Please <a href="../auth.php?tab=login">login</a> to post a review!');    
        }
        // Declare review variables - use main site user data
        $acc_id = isset($_SESSION['loggedin']) ? $_SESSION['acc_id'] : -1; 
        $name = isset($_SESSION['loggedin']) ? $_SESSION['fname'] . ' ' . $_SESSION['lname'] : $_POST['name']; 
        $approved = reviews_approval_required ? -1 : 1;
        $rating = abs((int)$_POST['rating']) > max_stars ? max_stars : abs((int)$_POST['rating']);
        // Insert a new review
        $stmt = $pdo->prepare('INSERT INTO reviews (page_id, display_name, content, rating, submit_date, approved, acc_id) VALUES (?,?,?,?,?,?,?)');
        $stmt->execute([ $_GET['page_id'], $name, $_POST['content'], $rating, date('Y-m-d H:i:s'), $approved, $acc_id ]);
        // Retrieve the ID of the review
        $id = $pdo->lastInsertId();
        // Check if the user has uploaded images
        if (isset($_FILES['images']) && upload_images_allowed) {
            // Iterate the uploaded images
            for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
                // Get the image extension (png, jpg, etc)
                $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                // The image name will contain a unique code to prevent multiple images with the same name.
            	$image_path = images_directory . sha1(uniqid() . $id . $i) .  '.' . $ext;
            	// Check to make sure the image is valid
            	if (!empty($_FILES['images']['tmp_name'][$i]) && getimagesize($_FILES['images']['tmp_name'][$i])) {
            		if (!file_exists($image_path) && $_FILES['images']['size'][$i] <= max_allowed_upload_image_size) {
                        // The image size is limited to a maximum of 500kb, you can change the value above, or remove it.
            			// If everything checks out we can move the uploaded image to its final destination...
            			move_uploaded_file($_FILES['images']['tmp_name'][$i], $image_path);
            			// Insert image info into the database (review_id, path)
            			$stmt = $pdo->prepare('INSERT INTO review_images (review_id,file_path) VALUES (?, ?)');
            	        $stmt->execute([ $id, $image_path ]);
            		}
            	}
            }
        }
        // Set cookie to prevent user from writing multiple reviews on the same page
        setcookie('review' . $_GET['page_id'], true, time() + (10 * 365 * 24 * 60 * 60));
        // Send notification email
        if (mail_enabled) {
            // Include PHPMailer library
            require 'lib/phpmailer/Exception.php';
            require 'lib/phpmailer/PHPMailer.php';
            require 'lib/phpmailer/SMTP.php';
            // Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);
            try {
                // SMTP Server settings
                if (SMTP) {
                    $mail->isSMTP();
                    $mail->Host = smtp_host;
                    $mail->SMTPAuth = true;
                    $mail->Username = smtp_user;
                    $mail->Password = smtp_pass;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = smtp_port;
                }
                // Recipients
                $mail->setFrom(mail_from, mail_name);
                $mail->addAddress(notification_email);
                $mail->addReplyTo(mail_from, mail_name);
                // Content
                $mail->isHTML(true);
                $mail->Subject = 'A new review has been posted!';
                // Email
                $email = isset($_SESSION['review_account_loggedin']) ? $_SESSION['review_account_email'] : '--';
                // Read the template contents and replace the "%link" placeholder with the above variable
                $email_template = str_replace(['%name%','%email%','%date%','%page_id%','%review%','%url%'], [$name, $email, date('Y-m-d H:i:s'), $_GET['page_id'], htmlspecialchars($_POST['content'], ENT_QUOTES), reviews_directory_url . 'admin/review.php?id=' . $id], file_get_contents('notification-email-template.html'));
                $mail->Body = $email_template;
                $mail->AltBody = strip_tags($email_template);
                // Send mail
                $mail->send();
            } catch (Exception $e) {
                // Output error message
                exit('Error: Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
            }
        }
        // End the ouput below, no need to execute the code after that.
        if ($approved == -1) {
            exit('Your review has been submitted and is awaiting approval!');
        } else {
            exit('Your review has been submitted!');
        }
    }
    // IF the user clicks the like button
    if ($type == 'full' && isset($_GET['like'])) {
        // Check if the cookie exists for the specified review
        if (!isset($_COOKIE['review_like_' . $_GET['like']])) {
            // Cookie does not exists, update the likes column and increment the value
            $stmt = $pdo->prepare('UPDATE reviews SET likes = likes + 1 WHERE id = ?');
            $stmt->execute([ $_GET['like'] ]);
            // Set like cookie, this will prevent the users from liking multiple times on the same review, cookie expires in 10 years
            setcookie('review_like_' . $_GET['like'], 'true', time() + (10 * 365 * 24 * 60 * 60), '/');
        }
        exit;
    }
    if ($type == 'full') {
        // If the pagination params exist, add the LIMIT clause to the SQL statement
        $limit = isset($_GET['current_pagination_page'], $_GET['reviews_per_pagination_page']) ? 'LIMIT :current_pagination_page,:reviews_per_pagination_page' : '';
        // By default, order by the submit data (newest)
        $sort_by = 'ORDER BY submit_date DESC';
        $where = '';
        if (isset($_GET['sort_by'])) {
            // User has changed the sort by, update the sort by variable
            $sort_by = $_GET['sort_by'] == 'newest' ? 'ORDER BY r.submit_date DESC' : $sort_by;
            $sort_by = $_GET['sort_by'] == 'oldest' ? 'ORDER BY r.submit_date ASC' : $sort_by;
            $sort_by = $_GET['sort_by'] == 'rating_highest' ? 'ORDER BY r.rating DESC, r.submit_date DESC' : $sort_by;
            $sort_by = $_GET['sort_by'] == 'rating_lowest' ? 'ORDER BY r.rating ASC, r.submit_date DESC' : $sort_by;
            $sort_by = $_GET['sort_by'] == 'likes_highest' ? 'ORDER BY r.likes DESC, r.submit_date DESC' : $sort_by;
            $sort_by = $_GET['sort_by'] == 'likes_lowest' ? 'ORDER BY r.likes ASC, r.submit_date DESC' : $sort_by;        
            // Determine the star to sort by
            if (strpos($_GET['sort_by'] , 'star_') !== false) {
                $star = intval(str_replace('star_', '', $_GET['sort_by']));
                $where = 'AND r.rating = :star';
            }
        }
        // Prepare statement that will secure our SQL query
        $stmt = $pdo->prepare('SELECT r.*, GROUP_CONCAT(i.file_path) AS images FROM reviews r LEFT JOIN review_images i ON i.review_id = r.id WHERE r.page_id = :page_id AND r.approved = 1 ' . $where . ' GROUP BY r.id, r.page_id, r.display_name, r.content, r.rating, r.submit_date, r.approved ' . $sort_by . ' ' . $limit);
        if ($limit) {
            // Determine which page the user is on and bind the value in to our SQL statement
            $stmt->bindValue(':current_pagination_page', ((int)$_GET['current_pagination_page']-1)*(int)$_GET['reviews_per_pagination_page'], PDO::PARAM_INT);
            // How many reviews will show on each pagination page
            $stmt->bindValue(':reviews_per_pagination_page', (int)$_GET['reviews_per_pagination_page'], PDO::PARAM_INT);
        }
        if ($where) {
            $stmt->bindValue(':star', $star, PDO::PARAM_INT);
        }
        $stmt->bindValue(':page_id', (int)$_GET['page_id'], PDO::PARAM_INT);
        $stmt->execute();
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Breakdown
        // Get the overall rating and total amount of reviews
        $stmt = $pdo->prepare('SELECT rating, COUNT(*) AS total FROM reviews WHERE page_id = ? GROUP BY rating');
        $stmt->execute([ $_GET['page_id'] ]);
        $reviews_breakdown = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        // Determine the status
        $breakdown_status = isset($_GET['breakdown_status']) ? $_GET['breakdown_status'] : 'open';
        // Retrieve the filters
        $stmt = $pdo->prepare('SELECT * FROM review_filters');
        $stmt->execute();
        $filters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Get the overall rating and total number of reviews
    $stmt = $pdo->prepare('SELECT AVG(rating) AS overall_rating, COUNT(*) AS total_reviews FROM reviews WHERE page_id = ? AND approved = 1');
    $stmt->execute([ $_GET['page_id'] ]);
    $reviews_info = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_reviews = $reviews_info['total_reviews'];
    // Updates the total reviews if the user is sorting by a specific star
    if (isset($where) && $where) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM reviews r WHERE r.page_id = :page_id AND r.approved = 1 ' . $where);
        $stmt->bindValue(':page_id', (int)$_GET['page_id'], PDO::PARAM_INT);
        $stmt->bindValue(':star', $star, PDO::PARAM_INT);
        $stmt->execute();
        $total_reviews = $stmt->fetchColumn();
    }
} else {
    exit('Please provide the page ID.');
}
?>
<?php if (!$reviews_only): ?>
<div class="overall-rating" itemscope itemprop="aggregateRating" itemtype="https://schema.org/AggregateRating">
    <span class="num" itemprop="ratingValue"><?=number_format($reviews_info['overall_rating'], 1)?><span itemprop="bestRating"><?=max_stars?></span></span>
    <span class="stars" title="<?=number_format($reviews_info['overall_rating'], 1)?> star">
    <?=str_repeat('<i class="bi bi-star-fill star" aria-hidden="true"></i>', round($reviews_info['overall_rating']))?>
        <?php if (max_stars-round($reviews_info['overall_rating']) > 0): ?>
    <?=str_repeat('<i class="bi bi-star-fill star-alt" aria-hidden="true"></i>', max_stars-round($reviews_info['overall_rating']))?>
        <?php endif; ?>
    </span>
    <span class="total"><span itemprop="ratingCount"><?=$reviews_info['total_reviews']?></span> reviews</span>
    <?php if ($type == 'full'): ?>
    <a href="#" class="toggle-breakdown-button"><i class="bi bi-<?=$breakdown_status=='open'?'dash':'plus'?>" aria-hidden="true"></i></a>
    <?php endif; ?>
</div>

<?php if ($type == 'full'): ?>

<div class="review-breakdown<?=$breakdown_status=='open'?' open':' closed'?>">
    <?php for ($i = max_stars-1; $i >= 0; $i--): ?>
    <a href="#" data-star="<?=$i+1?>">
        <span class="star"><?=$i+1?> star</span>
        <span class="bar"><span style="width:<?=isset($reviews_breakdown[$i+1]) && $reviews_breakdown[$i+1] > 0 && $reviews_info['total_reviews'] > 0 ? round(($reviews_breakdown[$i+1] / $reviews_info['total_reviews']) * 100) : 0?>%;"></span></span>
        <span class="per"><?=isset($reviews_breakdown[$i+1]) && $reviews_breakdown[$i+1] > 0 && $reviews_info['total_reviews'] > 0 ? round(($reviews_breakdown[$i+1] / $reviews_info['total_reviews']) * 100) : 0?>%</span>
    </a>
    <?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<?php if (!$chart_only): ?>
<?php if ($type == 'full'): ?>
<div class="review-header">
    <div class="review-btns">
        <?php if (isset($_SESSION['loggedin'])): ?>
        <a href="#" class="write-review-btn">Write Review</a>
        <?php endif; ?>
    </div>
    <div class="sort-by">
    <a href="#">Sort by <?=isset($_GET['sort_by']) && empty($where) ? htmlspecialchars(ucwords(str_replace('_', ' ', $_GET['sort_by'])), ENT_QUOTES) : 'Newest'?><i class="bi bi-caret-down-fill fa-sm" aria-hidden="true"></i></a>
        <div class="options">
            <a href="#" data-value="newest">Newest</a>
            <a href="#" data-value="oldest">Oldest</a>
            <a href="#" data-value="rating_highest">Rating - High to Low</a>
            <a href="#" data-value="rating_lowest">Rating - Low to High</a>
            <a href="#" data-value="likes_highest">Likes - High to Low</a>
            <a href="#" data-value="likes_lowest">Likes - Low to High</a>
        </div>
    </div>
</div>

<?php if (authentication_required && (!isset($_SESSION['loggedin']) || (isset($_SESSION['role']) && in_array($_SESSION['role'], ['guest', 'blog_only'])))): ?>
<!-- User must login and have proper role to write reviews -->
<?php else: ?>  
<div class="write-review">
    <form autocomplete="off">
        <?php if (!isset($_SESSION['loggedin'])): ?>
        <input name="name" type="text" placeholder="Your Name" required>
        <?php endif; ?>
        <input name="rating" class="rating" type="number">
        <div class="stars">
            <?php for($i = 1; $i <= max_stars; $i++): ?>
            <i class="bi bi-star star" data-id="<?=$i?>" aria-hidden="true"></i>
            <?php endfor; ?>
        </div>
        <div class="content">
            <textarea name="content" placeholder="Write your review..." maxlength="<?=max_review_chars?>" required></textarea>
            <div class="toolbar">
                <i class="format-btn bi bi-type-bold" data-format="bold" aria-hidden="true"></i>
                <i class="format-btn bi bi-type-italic" data-format="italic" aria-hidden="true"></i>
                <i class="format-btn bi bi-type-underline" data-format="underline" aria-hidden="true"></i>
            </div>
        </div>
        <?php if (upload_images_allowed): ?>
        <label for="images">Upload Images? Choose images using the input box below (&#60;500kb).</label>
		<input type="file" name="images[]" id="images" accept="image/*" multiple>
        <?php endif; ?>
        <div class="msg"></div>
        <button type="submit">Submit</button>
    </form>
</div>
<?php endif; ?>

<div class="write-review-msg"></div>

<?php if (empty($reviews)): ?>
<p class="no-reviews">No reviews have yet been posted.</p>
<?php endif; ?>

<?php foreach ($reviews as $review): ?>
<div class="review" itemscope itemprop="review" itemtype="https://schema.org/Review" id="review-<?=$review['id']?>">
    <div class="icon">
        <span style="background-color:<?=color_from_string($review['display_name'])?>"><?=strtoupper(substr($review['display_name'], 0, 1))?></span>
    </div>
    <div>
        <h3 class="name" itemprop="author" itemscope itemtype="https://schema.org/Person"><span itemprop="name"><?=htmlspecialchars($review['display_name'], ENT_QUOTES)?></span></h3>
        <div class="con">
            <span class="rating" itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating" title="<?=$review['rating']?> star">
                <?=str_repeat('<i class="bi bi-star-fill star" aria-hidden="true"></i>', $review['rating'])?>
                <?php if (max_stars-$review['rating'] > 0): ?>
                <?=str_repeat('<i class="bi bi-star-fill star-alt" aria-hidden="true"></i>', max_stars-$review['rating'])?>
                <?php endif; ?>
                <span class="rating-value" itemprop="ratingValue"><?=$review['rating']?></span>
            </span>
            <span class="date" itemprop="datePublished" content="<?=date('Y-m-d', strtotime($review['submit_date']))?>"><?=time_elapsed_string($review['submit_date'])?></span>
        </div>
        <p class="content" itemprop="name"><?=str_ireplace(array_merge(array_column($filters, 'word'), ['&lt;strong&gt;','&lt;/strong&gt;','&lt;u&gt;','&lt;/u&gt;','&lt;i&gt;','&lt;/i&gt;']), array_merge(array_column($filters, 'replacement'), ['<strong>','</strong>','<u>','</u>','<i>','</i>']), nl2br(htmlspecialchars($review['content'], ENT_QUOTES)))?></p>
        <div class="images">
            <?php foreach(explode(',', $review['images']) as $image): ?>
            <?php if (!empty($image) && file_exists($image)): ?>
            <img src="<?=reviews_directory_url . $image?>" width="70" height="70" alt="">
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="image"></div>
        <?php if ($review['likes'] > 0): ?>
        <p class="like-msg"><span><?=number_format($review['likes'])?></span> <?=$review['likes'] == 1 ? 'person' : 'people'?> liked this review.</p>
        <?php endif; ?>
    <a href="#" class="like-btn" data-id="<?=$review['id']?>"><i class="bi bi-hand-thumbs-up" aria-hidden="true"></i>Like</a>
        <?php if (isset($_SESSION['loggedin']) && isset($_SESSION['role']) && $_SESSION['role'] == 'Admin'): ?>
    <a target="_blank" href="<?=reviews_directory_url?>admin/review.php?id=<?=$review['id']?>" class="edit-btn"><i class="bi bi-pencil fa-sm" aria-hidden="true"></i>Edit</a>
        <?php endif; ?>
        <?php if ($review['response']): ?>
        <div class="response">
            <h3>Response from owner</h3>
            <p><?=nl2br($review['response'])?></p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>

<?php if ($limit): ?>
<div class="pagination">
    <?php if (isset($_GET['current_pagination_page']) && $_GET['current_pagination_page'] > 1): ?>
    <a href="#" data-pagination_page="<?=$_GET['current_pagination_page']-1?>" data-records_per_page="<?=$_GET['reviews_per_pagination_page']?>">Prev</a>
    <?php endif; ?>
    <div>Page <?=$_GET['current_pagination_page']?></div>
    <?php if ($_GET['current_pagination_page'] * $_GET['reviews_per_pagination_page'] < $total_reviews): ?>
    <a href="#" data-pagination_page="<?=$_GET['current_pagination_page']+1?>" data-records_per_page="<?=$_GET['reviews_per_pagination_page']?>">Next</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php endif; ?>

<?php endif; ?>