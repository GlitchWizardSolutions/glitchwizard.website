<?php
include 'main.php';
// Output message
$success_msg = '';
$error_msg = '';
// Check if the user is allowed to create polls
if (edit_polls != 'everyone') {
    // Check if the user is logged in
    if (!isset($_SESSION['account_loggedin'])) {
        // User is not logged in
        header('Location: index.php');
        exit;
    }
    // Check if the user is an admin
    if (edit_polls == 'admin' && $_SESSION['account_role'] != 'Admin') {
        // User is not an admin
        header('Location: index.php');
        exit;
    }
}
// Check if poll ID exists
if (!isset($_GET['id'])) {
    // Poll ID does not exist
    header('Location: index.php');
    exit;
}
// Check if the poll ID is valid
$stmt = $pdo->prepare('SELECT * FROM polls WHERE id = ?');
$stmt->execute([ $_GET['id'] ]);
$poll = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$poll) {
    // Poll does not exist
    header('Location: index.php');
    exit;
}
// Get poll answers
$stmt = $pdo->prepare('SELECT * FROM poll_answers WHERE poll_id = ?');
$stmt->execute([ $_GET['id'] ]);
$poll['answers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get the categories
$categories = $pdo->query('SELECT * FROM polls_categories')->fetchAll(PDO::FETCH_ASSOC);
// Get poll categories
$stmt = $pdo->prepare('SELECT p.* FROM poll_categories pc JOIN polls_categories p ON p.id = pc.category_id WHERE pc.poll_id = ?');
$stmt->execute([ $_GET['id'] ]);
$poll['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Check if POST data exists
if (isset($_POST['title'])) {
    // Post data exists, insert a new record
    // Check all POST data variables
    $title = $_POST['title'];
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $num_choices = isset($_POST['num_choices']) ? $_POST['num_choices'] : 1;
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-d\TH:i');
    $end_date = isset($_POST['end_date']) && $_POST['end_date'] ? $_POST['end_date'] : NULL;
    $approved = approval_required ? 0 : 1;
    // Insert new record into the "polls" table
    $stmt = $pdo->prepare('UPDATE polls SET title = ?, description = ?, start_date = ?, end_date = ?, approved = ?, num_choices = ? WHERE id = ?');
    $stmt->execute([ $title, $description, $start_date, $end_date, $approved, $num_choices, $_GET['id'] ]);
    // Check if the answers POST data exists and is an array
    if (isset($_POST['answers']) && is_array($_POST['answers'])) {
        // Iterate the post data and add the answers
        foreach($_POST['answers'] as $k => $v) {
            // Define image path variable
            $image_path = isset($poll['answers'][$k]) ? $poll['answers'][$k]['img'] : '';
            // Handle image uploads
            if (images_enabled && isset($_FILES['images'], $_FILES['images']['error'][$k]) && $_FILES['images']['error'][$k] == UPLOAD_ERR_OK) {
                // Check if the image is too large
                if (images_max_size && $_FILES['images']['size'][$k] > images_max_size) {
                    continue;
                }
                // Check if the image is an image
                if (getimagesize($_FILES['images']['tmp_name'][$k])) {
                    // Get the image extension
                    $ext = pathinfo($_FILES['images']['name'][$k], PATHINFO_EXTENSION);
                    // Update image path variable
                    $image_path = 'images/' . md5(uniqid()) . '.' . $ext;
                    // Move the image to the "images" folder
                    move_uploaded_file($_FILES['images']['tmp_name'][$k], $image_path);
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
                $stmt->execute([ $_GET['id'], $v, $image_path ]);
            }
        }
    }
    // Check if the categories POST data exists and is an array
    if (isset($_POST['categories']) && is_array($_POST['categories']) && count($_POST['categories']) > 0) {
        $in  = str_repeat('?,', count($_POST['categories']) - 1) . '?';
        $stmt = $pdo->prepare('DELETE FROM poll_categories WHERE poll_id = ? AND category_id NOT IN (' . $in . ')');
        $stmt->execute(array_merge([ $_GET['id'] ], $_POST['categories']));
        foreach ($_POST['categories'] as $cat) {
            $stmt = $pdo->prepare('INSERT IGNORE INTO poll_categories (poll_id,category_id) VALUES (?,?)');
            $stmt->execute([ $_GET['id'], $cat ]);
        }
    } else {
        $stmt = $pdo->prepare('DELETE FROM poll_categories WHERE poll_id = ?');
        $stmt->execute([ $_GET['id'] ]);       
    }
    // Output success message / approval message
    if (!$approved) {
        $error_msg = 'Your poll is awaiting approval!';
    } else {
        $success_msg = 'Poll updated successfully!';
    }
    // Get new data
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);
    // Get poll answers
    $stmt = $pdo->prepare('SELECT * FROM poll_answers WHERE poll_id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $poll['answers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Get poll categories
    $stmt = $pdo->prepare('SELECT p.* FROM poll_categories pc JOIN polls_categories p ON p.id = pc.category_id WHERE pc.poll_id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $poll['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?=template_header('Update Poll')?>

<div class="content create">

    <div class="page-title">
        <div class="icon">
            <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" /></svg>
        </div>	
        <div class="wrap">
            <h2>Update Poll</h2>
            <p>Update the poll below.</p>
        </div>
    </div>

    <div class="block">

        <form action="" method="post" class="form form-small pad-y-2" enctype="multipart/form-data">

            <label for="title" class="form-label" style="padding-top:0">Title</label>
            <input type="text" name="title" id="title" placeholder="Title" class="form-input" value="<?=htmlspecialchars($poll['title'], ENT_QUOTES)?>" required>

            <label for="description" class="form-label">Description</label>
            <input type="text" name="description" id="description" placeholder="Description" value="<?=htmlspecialchars($poll['description'], ENT_QUOTES)?>" class="form-input">

            <label for="answers" class="form-label">Answer Options</label>
            <div class="answers">
                <div class="answer">
                    <?php if ($poll['answers']): ?>
                    <?php foreach ($poll['answers'] as $k => $answer): ?>
                    <input type="hidden" name="answer_ids[]" value="<?=$answer['id']?>">
                    <input type="text" name="answers[]" placeholder="Option" class="form-input<?=$k > 0 ? ' mar-top-3' : ''?>" value="<?=htmlspecialchars($answer['title'], ENT_QUOTES)?>">
                    <?php if (images_enabled): ?>
                    <label class="file-input mar-top-2">
                        <span class="file-icon"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,19H5V5H19M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M13.96,12.29L11.21,15.83L9.25,13.47L6.5,17H17.5L13.96,12.29Z" /></svg></span>
                        <span class="file-name">Select Image...</span>
                        <input id="image" name="images[]" type="file" placeholder="Image" class="image">
                    </label>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <input type="text" name="answers[]" placeholder="Option 1" class="form-input">
                    <?php if (images_enabled): ?>
                    <label class="file-input mar-top-2">
                        <span class="file-icon"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,19H5V5H19M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M13.96,12.29L11.21,15.83L9.25,13.47L6.5,17H17.5L13.96,12.29Z" /></svg></span>
                        <span class="file-name">Select Image 1...</span>
                        <input id="image" name="images[]" type="file" placeholder="Image" class="image">
                    </label>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <a href="#" class="add_answer form-link mar-top-2"><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" /></svg>Add Option</a>

            <label for="categories" class="form-label">Categories</label>
            <div class="multiselect" data-name="categories[]">
                <?php foreach ($poll['categories'] as $c): ?>
                <span class="item" data-value="<?=$c['id']?>">
                    <i class="remove">&times;</i><?=$c['title']?>
                    <input type="hidden" name="categories[]" value="<?=$c['id']?>">
                </span>
                <?php endforeach; ?>
                <input type="text" class="search" id="category" placeholder="Categories">
                <div class="list">
                    <?php foreach ($categories as $category): ?>
                    <span data-value="<?=$category['id']?>"><?=$category['title']?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <div>
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="datetime-local" id="start_date" name="start_date" value="<?=date('Y-m-d\TH:i', strtotime($poll['start_date']))?>" class="form-input">
                </div>
                <div>
                    <label for="end_date" class="form-label">End Date <span>(optional)</span></label>
                    <input type="datetime-local" id="end_date" name="end_date" value="<?=$poll['end_date'] ? date('Y-m-d\TH:i', strtotime($poll['end_date'])) : ''?>" class="form-input">
                </div>
            </div>

            <label for="num_choices" class="form-label">How many choices can the user select?</label>
            <input type="number" id="num_choices" name="num_choices" value="<?=$poll['num_choices']?>" class="form-input">

            <div class="btns mar-top-4">
                <button type="submit" class="btn blue">Update</button>
            </div>

            <?php if ($success_msg): ?>
            <p class="msg success"><?=$success_msg?></p>
            <?php elseif ($error_msg): ?>
            <p class="msg error"><?=$error_msg?></p>
            <?php endif; ?>

        </form>

    </div>

</div>

<?=template_footer()?>