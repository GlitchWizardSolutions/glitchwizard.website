<?php
include_once '../assets/includes/main.php';
// Default filter values
$filter = [
    'word' => '',
    'replacement' => ''
];
if (isset($_GET['id'])) {
    // Retrieve the filter from the database
    $stmt = $pdo->prepare('SELECT * FROM review_filters WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $filter = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing filter
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the filter
        $stmt = $pdo->prepare('UPDATE review_filters SET word = ?, replacement = ? WHERE id = ?');
        $stmt->execute([ $_POST['word'], $_POST['replacement'], $_GET['id'] ]);
        header('Location: review_filters.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Delete the filter
        $stmt = $pdo->prepare('DELETE FROM review_filters WHERE id = ?');
        $stmt->execute([ $_GET['id'] ]);
        header('Location: review_filters.php?success_msg=3');
        exit;
    }
} else {
    // Create a new filter
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO review_filters (word,replacement) VALUES (?,?)');
        $stmt->execute([ $_POST['word'], $_POST['replacement'] ]);
        header('Location: review_filters.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Filter', 'reviews', 'filter')?>

<div class="content-title">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 43.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 98.4c-9.8-13-10.8-29.5-5.2-43.5z"/>
            </svg>
        </div>
        <div class="txt">
            <h2><?=$page?> Review Filter</h2>
            <p><?= $page == 'Edit' ? 'Modify or delete an existing review filter' : 'Create a new review filter to automatically replace words in reviews' ?></p>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-lg-8">
        <form action="" method="post">
            
            <!-- Action Buttons at Top -->
            <div class="d-flex gap-2 pb-3 border-bottom mb-3">
                <a href="review_filters.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Cancel
                </a>
                <?php if ($page == 'Edit'): ?>
                <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this filter?')">
                    <i class="fas fa-trash me-1"></i>Delete Filter
                </button>
                <?php endif; ?>
                <button type="submit" name="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i><?=$page == 'Edit' ? 'Save' : 'Save'?> Filter
                </button>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Filter Details</h6>
                </div>
                <div class="card-body">
                    <!-- Filter Configuration Fieldset -->
                    <fieldset class="mb-4">
                        <legend class="h6 text-primary mb-3">Filter Configuration</legend>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="word" class="form-label">
                                    <span class="text-danger">*</span> Word to Filter
                                </label>
                                <input 
                                    id="word" 
                                    type="text" 
                                    name="word" 
                                    class="form-control" 
                                    placeholder="Enter word or phrase to filter" 
                                    value="<?=htmlspecialchars($filter['word'], ENT_QUOTES)?>" 
                                    required
                                    aria-describedby="wordHelp"
                                >
                                <div id="wordHelp" class="form-text">The word or phrase that will be automatically replaced in reviews</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="replacement" class="form-label">
                                    <span class="text-danger">*</span> Replacement Text
                                </label>
                                <input 
                                    id="replacement" 
                                    type="text" 
                                    name="replacement" 
                                    class="form-control" 
                                    placeholder="Enter replacement text" 
                                    value="<?=htmlspecialchars($filter['replacement'], ENT_QUOTES)?>" 
                                    required
                                    aria-describedby="replacementHelp"
                                >
                                <div id="replacementHelp" class="form-text">The text that will replace the filtered word or phrase</div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </form>
    </div>
    
    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title">Filter Guidelines</h6>
                <ul class="mb-0">
                    <li>Filters are case-insensitive</li>
                    <li>Use exact word matches for best results</li>
                    <li>Replacement text can be empty to remove words</li>
                    <li>Changes apply to new reviews immediately</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?=template_admin_footer()?>