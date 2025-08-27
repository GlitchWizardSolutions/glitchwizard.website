<?php
include_once '../assets/includes/main.php';
// Default page values
$page = [
    'title' => '',
    'description' => '',
    'url' => ''
];
$page_title = 'Create';
if (isset($_GET['id'])) {
    // Retrieve the page from the database
    $stmt = $pdo->prepare('SELECT * FROM review_page_details WHERE page_id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($results) {
        $page = $results;
        $page_title = 'Edit';
        if (isset($_POST['submit'])) {
            // Update the review_page table
            $stmt = $pdo->prepare('UPDATE review_page_details SET title = ?, description = ?, url = ? WHERE page_id = ?');
            $stmt->execute([ $_POST['title'], $_POST['description'], $_POST['url'], $_GET['id'] ]);
            header('Location: review_pages.php?success_msg=2');
            exit;
        }
    } else {
        if (isset($_POST['submit'])) {
            // Insert into the review_page table
            $stmt = $pdo->prepare('INSERT INTO review_page_details (page_id, title, description, url) VALUES (?,?,?,?)');
            $stmt->execute([ $_GET['id'], $_POST['title'], $_POST['description'], $_POST['url'] ]);
            header('Location: review_pages.php?success_msg=1');
            exit;
        }
    }
} else {
    exit('No ID specified.');
}
?>
<?=template_admin_header($page_title . ' Page Details', 'reviews', 'pages')?>

<div class="content-title">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                <path d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0z"/>
            </svg>
        </div>
        <div class="txt">
            <h2><?=$page_title?> Page Details</h2>
            <p><?= $page_title == 'Edit' ? 'Modify page details and metadata for this review page' : 'Create page details and metadata for this review page' ?></p>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-lg-8">
        <form action="" method="post">
            
            <!-- Action Buttons at Top -->
            <div class="d-flex gap-2 pb-3 border-bottom mb-3">
                <a href="review_pages.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Cancel
                </a>
                <button type="submit" name="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i><?=$page_title == 'Edit' ? 'Save' : 'Create'?> Page
                </button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Page Details</h6>
                </div>
                <div class="card-body">
                    <fieldset>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input id="title" type="text" name="title" class="form-control" placeholder="Enter page title" value="<?=htmlspecialchars($page['title'], ENT_QUOTES)?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter page description"><?=htmlspecialchars($page['description'], ENT_QUOTES)?></textarea>
                                    <div class="form-text">Provide a brief description of what this page is about.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="url" class="form-label">URL</label>
                                    <input id="url" type="url" name="url" class="form-control" placeholder="https://example.com/page" value="<?=htmlspecialchars($page['url'], ENT_QUOTES)?>">
                                    <div class="form-text">The full URL where this page can be accessed.</div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
            
        </form>
    </div>
</div>

<?=template_admin_footer()?>