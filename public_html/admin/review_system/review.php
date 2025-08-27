<?php
// Phase 2 Template Integration - Using unified admin template system
include_once '../assets/includes/main.php';

// Review system configuration constants
if (!defined('max_stars')) {
    define('max_stars', 5);
}

// Default review values
$review = [
    'page_id' => '',
    'display_name' => '',
    'content' => '',
    'rating' => 0,
    'submit_date' => date('Y-m-d H:i:s'),
    'likes' => 0,
    'approved' => 1,
    'account_id' => -1,
    'response' => ''
];
// Retrieve accounts from the database
$stmt = $pdo->prepare('SELECT * FROM accounts');
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve available pages (from both reviews and review_page_details tables)
$stmt = $pdo->prepare('
    SELECT DISTINCT 
        page_id,
        COALESCE(title, CONCAT("Page ", page_id)) as page_title,
        url
    FROM (
        SELECT DISTINCT r.page_id, rpd.title, rpd.url
        FROM reviews r 
        LEFT JOIN review_page_details rpd ON rpd.page_id = r.page_id
        UNION
        SELECT rpd.page_id, rpd.title, rpd.url
        FROM review_page_details rpd
    ) AS all_pages
    ORDER BY page_title ASC
');
$stmt->execute();
$available_pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET['id'])) {
    // Retrieve the review from the database
    $stmt = $pdo->prepare('SELECT * FROM reviews WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing review
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the review
        $stmt = $pdo->prepare('UPDATE reviews SET page_id = ?, display_name = ?, content = ?, rating = ?, submit_date = ?, approved = ?, account_id = ?, likes = ?, response = ? WHERE id = ?');
        $stmt->execute([ $_POST['page_id'], $_POST['display_name'], $_POST['content'], $_POST['rating'], date('Y-m-d H:i:s', strtotime($_POST['submit_date'])), $_POST['approved'], $_POST['account_id'], $_POST['likes'], $_POST['response'], $_GET['id'] ]);
        header('Location: reviews.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete the review
        header('Location: reviews.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new review
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO reviews (page_id,display_name,content,rating,submit_date,approved,account_id,likes,response) VALUES (?,?,?,?,?,?,?,?,?)');
        $stmt->execute([ $_POST['page_id'], $_POST['display_name'], $_POST['content'], $_POST['rating'], date('Y-m-d H:i:s', strtotime($_POST['submit_date'])), $_POST['approved'], $_POST['account_id'], $_POST['likes'], $_POST['response'] ]);
        header('Location: reviews.php?success_msg=1');
        exit;
    }
}

// Use unified admin header with reviews navigation
echo template_admin_header($page . ' Review', 'reviews', 'manage');
?>

<!-- PHASE 2 STANDARDIZATION: Unified Content Title Block -->
<div class="content-title" id="main-review-form" role="banner" aria-label="<?=$page?> Review Form Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M123.6 391.3c12.9-9.4 29.6-11.8 44.6-6.4c26.5 9.6 56.2 15.1 87.8 15.1c124.7 0 208-80.5 208-160s-83.3-160-208-160S48 160.5 48 240c0 24 12.2 46.7 31.4 64.7c5.4 5.1 9.3 11.9 10.8 19.9c2.4 12.6 2.1 25.6-.1 38.1c-2.1 11.7-5.5 23.1-10.2 34.1c6.2-2.3 12.1-5.2 17.6-8.6c26.1-16.4 42.2-43.1 42.2-72.2c0-47.1-38.1-85.2-85.2-85.2s-85.2 38.1-85.2 85.2c0 47.1 38.1 85.2 85.2 85.2c11.9 0 23.4-2.4 33.8-6.8z"/></svg>
        </div>
        <div class="txt">
            <h2><?=$page?> Review</h2>
            <p><?=$page == 'Edit' ? 'Update review information and settings' : 'Create a new review entry'?></p>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-lg-8">
        <form action="" method="post">
            
            <!-- Action Buttons at Top -->
            <div class="d-flex gap-2 pb-3 border-bottom mb-3">
                <a href="reviews.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Cancel
                </a>
                <?php if ($page == 'Edit'): ?>
                <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this review?')">
                    <i class="fas fa-trash me-1"></i>Delete Review
                </button>
                <?php endif; ?>
                <button type="submit" name="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i><?=$page == 'Edit' ? 'Save' : 'Save'?> Review
                </button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Review Details</h6>
                </div>
                <div class="card-body">
                    <!-- Basic Information Fieldset -->
                    <fieldset class="mb-4">
                        <legend class="h6 text-primary mb-3">Basic Information</legend>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="page_id" class="form-label">
                                    <span class="text-danger">*</span> Page
                                </label>
                                <select 
                                    id="page_id" 
                                    name="page_id" 
                                    class="form-control" 
                                    required
                                    aria-describedby="pageIdHelp"
                                >
                                    <option value="">-- Select a Page --</option>
                                    <?php foreach ($available_pages as $page_option): ?>
                                    <option value="<?=htmlspecialchars($page_option['page_id'], ENT_QUOTES)?>" 
                                            <?= $review['page_id'] == $page_option['page_id'] ? 'selected' : '' ?>>
                                        <?=htmlspecialchars($page_option['page_title'], ENT_QUOTES)?>
                                        <?php if ($page_option['url']): ?>
                                            (<?=htmlspecialchars($page_option['url'], ENT_QUOTES)?>)
                                        <?php endif; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="pageIdHelp" class="form-text">Select the page this review is for</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="display_name" class="form-label">
                                    <span class="text-danger">*</span> Display Name
                                </label>
                                <input 
                                    id="display_name" 
                                    type="text" 
                                    name="display_name" 
                                    class="form-control" 
                                    placeholder="Enter reviewer name" 
                                    value="<?=htmlspecialchars($review['display_name'], ENT_QUOTES)?>" 
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">
                                <span class="text-danger">*</span> Review Content
                            </label>
                            <textarea 
                                id="content" 
                                name="content" 
                                class="form-control" 
                                rows="4" 
                                placeholder="Write the review content..." 
                                required
                            ><?=htmlspecialchars($review['content'], ENT_QUOTES)?></textarea>
                        </div>
                    </fieldset>

                    <!-- Rating and Metrics Fieldset -->
                    <fieldset class="mb-4">
                        <legend class="h6 text-primary mb-3">Rating & Metrics</legend>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="rating" class="form-label">
                                    <span class="text-danger">*</span> Rating
                                </label>
                                <input 
                                    id="rating" 
                                    type="number" 
                                    name="rating" 
                                    class="form-control" 
                                    placeholder="Rating" 
                                    value="<?=htmlspecialchars($review['rating'], ENT_QUOTES)?>" 
                                    min="0" 
                                    max="<?=max_stars?>" 
                                    required
                                >
                                <div class="form-text">Scale: 0 to <?=max_stars?> stars</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="likes" class="form-label">
                                    <span class="text-danger">*</span> Likes
                                </label>
                                <input 
                                    id="likes" 
                                    type="number" 
                                    name="likes" 
                                    class="form-control" 
                                    placeholder="Number of likes" 
                                    value="<?=$review['likes']?>" 
                                    min="0" 
                                    required
                                >
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="submit_date" class="form-label">
                                    <span class="text-danger">*</span> Date Submitted
                                </label>
                                <input 
                                    id="submit_date" 
                                    type="datetime-local" 
                                    name="submit_date" 
                                    class="form-control" 
                                    value="<?=date('Y-m-d\TH:i', strtotime($review['submit_date']))?>" 
                                    required
                                >
                            </div>
                        </div>
                    </fieldset>

                    <!-- Administration Fieldset -->
                    <fieldset class="mb-4">
                        <legend class="h6 text-primary mb-3">Administration</legend>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="approved" class="form-label">
                                    <span class="text-danger">*</span> Approval Status
                                </label>
                                <select id="approved" name="approved" class="form-select" required>
                                    <option value="0"<?=$review['approved']==0?' selected':''?>>Not Approved</option>
                                    <option value="1"<?=$review['approved']==1?' selected':''?>>Approved</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="account_id" class="form-label">Account Association</label>
                                <select id="account_id" name="account_id" class="form-select" required>
                                    <option value="-1">(No Account)</option>
                                    <?php foreach ($accounts as $account): ?>
                                    <option value="<?=$account['id']?>"<?=$review['account_id']==$account['id']?' selected':''?>>
                                        <?=$account['id']?> - <?=$account['email']?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="response" class="form-label">Webmaster Response</label>
                            <textarea 
                                id="response" 
                                name="response" 
                                class="form-control" 
                                rows="3" 
                                placeholder="Optional response to the review..."
                            ><?=htmlspecialchars($review['response'], ENT_QUOTES)?></textarea>
                            <div class="form-text">Optional public response that will be displayed with the review</div>
                        </div>
                    </fieldset>
                    
                    <!-- Bottom Buttons -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <a href="reviews.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Cancel
                                </a>
                                <?php if ($review): ?>
                                <button type="submit" name="save" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i>Save Changes
                                </button>
                                <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this review?')">
                                    <i class="fas fa-trash me-1"></i>Delete Review
                                </button>
                                <?php else: ?>
                                <button type="submit" name="save" class="btn btn-success">
                                    <i class="fas fa-plus me-1"></i>Create Review
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Right sidebar with help information -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Help & Guidelines</h6>
            </div>
            <div class="card-body">
                <h6>Review Guidelines</h6>
                <ul class="small">
                    <li>Page ID should match the reviewed content</li>
                    <li>Display names should be appropriate and authentic</li>
                    <li>Review content should be constructive and helpful</li>
                    <li>Ratings range from 0 to <?=max_stars?> stars</li>
                </ul>
                
                <h6 class="mt-3">Approval Process</h6>
                <p class="small">Reviews can be approved or pending. Approved reviews will be visible to the public, while pending reviews require moderation.</p>
            </div>
        </div>
    </div>
</div>

<?php echo template_admin_footer(); ?>