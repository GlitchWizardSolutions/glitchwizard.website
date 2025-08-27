<?php
include '../assets/includes/main.php';
// Define polling system configuration constants
if (!defined('images_enabled')) {
    define('images_enabled', false);
}
if (!defined('images_directory')) {
    define('images_directory', 'images/');
}
if (!defined('images_max_size')) {
    define('images_max_size', 1000000);
}
// Default poll values
$poll = [
    'title' => '',
    'description' => '',
    'approved' => 1,
    'num_choices' => 1,
    'start_date' => date('Y-m-d\TH:i'),
    'end_date' => date('Y-m-d\TH:i', strtotime('+1 week')),
    'created' => date('Y-m-d\TH:i'),
    'categories' => []
];
// Get categories
$categories = $pdo->query('SELECT * FROM polls_categories')->fetchAll(PDO::FETCH_ASSOC);
// Add answer options function
function addAnswerOptions($pdo, $poll_id, $poll) {
    // Check if the answers POST data exists and is an array
    if (isset($_POST['answers']) && is_array($_POST['answers'])) {
        // Iterate the post data and add the answers
        foreach($_POST['answers'] as $k => $v) {
            // Define image path variable
            $image_path = isset($poll['answers']) && isset($poll['answers'][$k]) ? $poll['answers'][$k]['img'] : '';
            // Handle image uploads
            if (images_enabled && isset($_FILES['images'], $_FILES['images']['error'][$k]) && $_FILES['images']['error'][$k] == UPLOAD_ERR_OK) {
                // Check if the image is an image
                if (getimagesize($_FILES['images']['tmp_name'][$k])) {
                    // Get the image extension
                    $ext = pathinfo($_FILES['images']['name'][$k], PATHINFO_EXTENSION);
                    // Update image path variable
                    $image_path = 'images/' . md5(uniqid()) . '.' . $ext;
                    // Move the image to the client portal polling system images folder
                    move_uploaded_file($_FILES['images']['tmp_name'][$k], '../../client_portal/polling_system/' . $image_path);
                }
            }
            // Delete the answer if it is empty
            if (empty($v) && isset($_POST['answer_ids']) && isset($_POST['answer_ids'][$k])) {
                $stmt = $pdo->prepare('DELETE FROM poll_answers WHERE id = ?');
                $stmt->execute([ $_POST['answer_ids'][$k] ]);
            }
            // If the answer is empty, there is no need to insert
            if (empty($v) && empty($image_path)) continue;
            // Check if the answer ID exists
            if (isset($_POST['answer_ids']) && isset($_POST['answer_ids'][$k])) {
                // Answer ID exists, update the answer
                $stmt = $pdo->prepare('UPDATE poll_answers SET title = ?, img = ? WHERE id = ?');
                $stmt->execute([ $v, $image_path, $_POST['answer_ids'][$k] ]);
            } else {
                // Add answer to the "poll_answers" table
                $stmt = $pdo->prepare('INSERT INTO poll_answers (poll_id, title, img) VALUES (?, ?, ?)');
                $stmt->execute([ $poll_id, $v, $image_path ]);
            }
        }
    }
}
// Function to add the categories
function addCategories($pdo, $poll_id) {
    if (isset($_POST['categories']) && is_array($_POST['categories']) && count($_POST['categories']) > 0) {
        $in  = str_repeat('?,', count($_POST['categories']) - 1) . '?';
        $stmt = $pdo->prepare('DELETE FROM poll_categories WHERE poll_id = ? AND category_id NOT IN (' . $in . ')');
        $stmt->execute(array_merge([ $poll_id ], $_POST['categories']));
        foreach ($_POST['categories'] as $cat) {
            $stmt = $pdo->prepare('INSERT IGNORE INTO poll_categories (poll_id,category_id) VALUES (?,?)');
            $stmt->execute([ $poll_id, $cat ]);
        }
    } else {
        $stmt = $pdo->prepare('DELETE FROM poll_categories WHERE poll_id = ?');
        $stmt->execute([ $poll_id ]);       
    }
}
// Check if the ID param exists
if (isset($_GET['id'])) {
    // Retrieve the poll from the database
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);
    // Get items
    $stmt = $pdo->prepare('SELECT * FROM poll_answers WHERE poll_id = ?');
    $stmt->execute([ $poll['id'] ]);
    $poll['answers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Get categories
    $stmt = $pdo->prepare('SELECT c.* FROM polls_categories c JOIN poll_categories pc ON pc.category_id = c.id WHERE pc.poll_id = ?');
    $stmt->execute([ $poll['id'] ]);
    $poll['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing poll
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Get the start and end dates
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d\TH:i');
        $end_date = isset($_POST['end_date']) && $_POST['end_date'] ? $_POST['end_date'] : NULL;
        // Update the poll
        $stmt = $pdo->prepare('UPDATE polls SET title = ?, description = ?, approved = ?, num_choices = ?, start_date = ?, end_date = ?, created = ? WHERE id = ?');
        $stmt->execute([ $_POST['title'], $_POST['description'], $_POST['approved'], $_POST['num_choices'], $start_date, $end_date, $_POST['created'], $_GET['id'] ]);
        // Add the answer options
        addAnswerOptions($pdo, $_GET['id'], $poll);
        // Add the categories
        addCategories($pdo, $_GET['id']);
        // Redirect to the polls page
        header('Location: polls.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete poll
        header('Location: polls.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new poll
    $page = 'Create';
    if (isset($_POST['submit'])) {
        // Get the start and end dates
        $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d\TH:i');
        $end_date = isset($_POST['end_date']) && $_POST['end_date'] ? $_POST['end_date'] : NULL;
        // Insert the poll
        $stmt = $pdo->prepare('INSERT INTO polls (title, description, approved, num_choices, start_date, end_date, created) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([ $_POST['title'], $_POST['description'], $_POST['approved'], $_POST['num_choices'], $start_date, $end_date, $_POST['created'] ]);
        // Get the poll ID
        $poll_id = $pdo->lastInsertId();
        // Add the answer options
        addAnswerOptions($pdo, $poll_id, $poll);
        // Add the categories
        addCategories($pdo, $poll_id);
        // Redirect to the polls page
        header('Location: polls.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Poll', 'polls', 'manage')?>
<link rel="stylesheet" href="polling-specific.css">

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <i class="bi bi-bar-chart-steps" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2><?=$page?> Poll</h2>
            <p><?=$page == 'Edit' ? 'Modify poll settings and answer options.' : 'Create a new poll with custom options and settings.'?></p>
        </div>
    </div>
</div>

<form action="" method="post" enctype="multipart/form-data" id="main-form" role="form" aria-labelledby="form-title" aria-describedby="form-description">

    <div class="d-flex gap-2 mb-4" role="region" aria-label="Form Actions">
        <a href="polls.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Cancel
        </a>
        <button type="submit" name="submit" class="btn btn-success">
            <i class="bi bi-save me-1" aria-hidden="true"></i>
            Save Poll
        </button>
        <?php if ($page == 'Edit'): ?>
        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this poll?')">
            <i class="bi bi-trash me-1" aria-hidden="true"></i>
            Delete Poll
        </button>
        <?php endif; ?>
    </div>

    <div class="card mb-3">
        <h6 class="card-header"><?= $page == 'Edit' ? 'Edit Poll' : 'Create Poll' ?></h6>
        <div class="card-body">
            
            <div class="content-block">
                <fieldset role="group" aria-labelledby="poll-basic-info">
                    <legend id="poll-basic-info">Basic Poll Information</legend>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    <span class="required" aria-hidden="true">*</span> Poll Title
                                    <span class="sr-only">(required)</span>
                                </label>
                                <input type="text" id="title" name="title" class="form-control" 
                                    placeholder="Enter poll title" 
                                    value="<?=htmlspecialchars($poll['title'], ENT_QUOTES)?>" 
                                    required 
                                    aria-required="true" 
                                    aria-describedby="title-hint">
                                <div id="title-hint" class="form-text">This will be displayed as the main poll question.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description" class="form-label">Poll Description</label>
                                <input type="text" id="description" name="description" class="form-control" 
                                    placeholder="Enter poll description (optional)" 
                                    value="<?=htmlspecialchars($poll['description'], ENT_QUOTES)?>" 
                                    aria-describedby="description-hint">
                                <div id="description-hint" class="form-text">Optional additional details about the poll.</div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset role="group" aria-labelledby="poll-options">
                    <legend id="poll-options">Answer Options</legend>
                    
                    <div class="mb-3">
                        <label class="form-label">Poll Options</label>
                        <div class="answers border rounded p-3 bg-light">
                            <div class="answer">
                                <?php if ($page == 'Edit'): ?>
                                <?php foreach ($poll['answers'] as $k => $answer): ?>
                                <input type="hidden" name="answer_ids[]" value="<?=$answer['id']?>">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">Option <?=$k + 1?></span>
                                        <input type="text" name="answers[]" class="form-control" 
                                            placeholder="Enter option text" 
                                            value="<?=htmlspecialchars($answer['title'], ENT_QUOTES)?>">
                                    </div>
                                    <?php if (images_enabled): ?>
                                    <div class="mt-2">
                                        <label class="form-label">Option Image</label>
                                        <input type="file" name="images[]" class="form-control" accept="image/*">
                                        <div class="form-text">Optional image for this option.</div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                                <?php else: ?>
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text">Option 1</span>
                                        <input type="text" name="answers[]" class="form-control" 
                                            placeholder="Enter first option">
                                    </div>
                                    <?php if (images_enabled): ?>
                                    <div class="mt-2">
                                        <label class="form-label">Option Image</label>
                                        <input type="file" name="images[]" class="form-control" accept="image/*">
                                        <div class="form-text">Optional image for this option.</div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2 add_answer">
                            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Add Option
                        </button>
                        <div class="form-text">Click "Add Option" to create additional poll choices.</div>
                    </div>
                </fieldset>

                <fieldset role="group" aria-labelledby="poll-settings">
                    <legend id="poll-settings">Poll Settings</legend>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categories" class="form-label">Categories</label>
                                <div class="multiselect border rounded p-2" data-name="categories[]">
                                    <?php foreach ($poll['categories'] as $c): ?>
                                    <span class="badge bg-secondary me-1 mb-1" data-value="<?=$c['id']?>">
                                        <button type="button" class="btn-close btn-close-white btn-sm me-1 remove" aria-label="Remove category"></button>
                                        <?=$c['title']?>
                                        <input type="hidden" name="categories[]" value="<?=$c['id']?>">
                                    </span>
                                    <?php endforeach; ?>
                                    <input type="text" class="form-control search mt-1" id="category" placeholder="Search categories...">
                                    <div class="list border rounded mt-1 bg-white" style="display: none;">
                                        <?php foreach ($categories as $category): ?>
                                        <div class="p-2 border-bottom list-item" data-value="<?=$category['id']?>" style="cursor: pointer;">
                                            <?=$category['title']?>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="form-text">Select one or more categories for this poll.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="approved" class="form-label">
                                    <span class="required" aria-hidden="true">*</span> Approval Status
                                    <span class="sr-only">(required)</span>
                                </label>
                                <select name="approved" id="approved" class="form-select" required aria-required="true">
                                    <option value="1"<?=$poll['approved'] == 1 ? ' selected' : ''?>>Approved</option>
                                    <option value="0"<?=$poll['approved'] == 0 ? ' selected' : ''?>>Pending Approval</option>
                                </select>
                                <div class="form-text">Approved polls are visible to users.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="num_choices" class="form-label">
                                    <span class="required" aria-hidden="true">*</span> Number of Choices Allowed
                                    <span class="sr-only">(required)</span>
                                </label>
                                <input type="number" id="num_choices" name="num_choices" class="form-control" 
                                    placeholder="1" 
                                    value="<?=$poll['num_choices']?>" 
                                    min="1" 
                                    required 
                                    aria-required="true" 
                                    aria-describedby="choices-hint">
                                <div id="choices-hint" class="form-text">How many options can each user select? (1 = single choice, 2+ = multiple choice)</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">
                                    <span class="required" aria-hidden="true">*</span> Start Date & Time
                                    <span class="sr-only">(required)</span>
                                </label>
                                <input type="datetime-local" id="start_date" name="start_date" class="form-control" 
                                    value="<?=date('Y-m-d\TH:i', strtotime($poll['start_date']))?>" 
                                    required 
                                    aria-required="true" 
                                    aria-describedby="start-hint">
                                <div id="start-hint" class="form-text">When the poll becomes available for voting.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date & Time</label>
                                <input type="datetime-local" id="end_date" name="end_date" class="form-control" 
                                    value="<?=$poll['end_date'] ? date('Y-m-d\TH:i', strtotime($poll['end_date'])) : ''?>" 
                                    aria-describedby="end-hint">
                                <div id="end-hint" class="form-text">When the poll closes. Leave blank for no end date.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="created" class="form-label">
                                    <span class="required" aria-hidden="true">*</span> Created Date & Time
                                    <span class="sr-only">(required)</span>
                                </label>
                                <input type="datetime-local" id="created" name="created" class="form-control" 
                                    value="<?=date('Y-m-d\TH:i', strtotime($poll['created']))?>" 
                                    required 
                                    aria-required="true" 
                                    aria-describedby="created-hint">
                                <div id="created-hint" class="form-text">Original creation timestamp for this poll.</div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        
        <!-- Bottom action buttons for longer forms -->
        <div class="pt-3 border-top mt-4" role="region" aria-label="Form Actions">
            <div class="d-flex gap-2">
                <a href="polls.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
                    Cancel
                </a>
                <button type="submit" name="submit" class="btn btn-success">
                    <i class="bi bi-save me-1" aria-hidden="true"></i>
                    Save Poll
                </button>
                <?php if ($page == 'Edit'): ?>
                <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this poll?')">
                    <i class="bi bi-trash me-1" aria-hidden="true"></i>
                    Delete Poll
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

</form>

<script src="polling-specific.js"></script>
<?=template_admin_footer()?>