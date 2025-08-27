<?php
include 'main.php';
// Output message
$success_msg = '';
$error_msg = '';
// Check if the user is allowed to create polls
if (create_polls != 'everyone') {
    // Check if the user is logged in
    if (!isset($_SESSION['account_loggedin'])) {
        // User is not logged in
        header('Location: index.php');
        exit;
    }
    // Check if the user is an admin
    if (create_polls == 'admin' && $_SESSION['account_role'] != 'Admin') {
        // User is not an admin
        header('Location: index.php');
        exit;
    }
}
// Get the categories
$categories = $pdo->query('SELECT * FROM polls_categories')->fetchAll(PDO::FETCH_ASSOC);
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
    $created = date('Y-m-d H:i:s');
    // Insert new record into the "polls" table
    $stmt = $pdo->prepare('INSERT INTO polls (title, description, created, start_date, end_date, approved, num_choices) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([ $title, $description, $created, $start_date, $end_date, $approved, $num_choices ]);
    // Below will get the last insert ID, which will be the poll id
    $poll_id = $pdo->lastInsertId();
    // Check if the answers POST data exists and is an array
    if (isset($_POST['answers']) && is_array($_POST['answers'])) {
        // Iterate the post data and add the answers
        foreach($_POST['answers'] as $k => $v) {
            // Define image path variable
            $image_path = '';
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
            // If the answer is empty, there is no need to insert
            if (empty($v) && empty($image_path)) continue;
            // Add answer to the "poll_answers" table
            $stmt = $pdo->prepare('INSERT INTO poll_answers (poll_id, title, img) VALUES (?, ?, ?)');
            $stmt->execute([ $poll_id, $v, $image_path ]);
        }
    }
    // Check categories
    if (isset($_POST['categories']) && is_array($_POST['categories'])) {
        // Iterate the post data and add the categories
        foreach($_POST['categories'] as $category_id) {
            // Add category to the "poll_categories" table
            $stmt = $pdo->prepare('INSERT INTO poll_categories (poll_id, category_id) VALUES (?, ?)');
            $stmt->execute([ $poll_id, $category_id ]);
        }
    }
    // Output success message / approval message
    if (!$approved) {
        $error_msg = 'Your poll is awaiting approval!';
    } else {
        $success_msg = 'Poll created successfully!';
    }
}
?>
<?=template_header('Create Poll')?>

<div class="content create">

    <div class="page-title">
        <div class="icon">
            <svg width="30" height="30" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" /></svg>
        </div>	
        <div class="wrap">
            <h2>Create Poll</h2>
            <p>Create a new poll below.</p>
        </div>
    </div>

    <div class="block">

        <form action="" method="post" class="form form-small pad-y-2" enctype="multipart/form-data">

            <label for="title" class="form-label" style="padding-top:0">Title</label>
            <input type="text" name="title" id="title" placeholder="Title" class="form-input" required>

            <label for="description" class="form-label">Description</label>
            <input type="text" name="description" id="description" placeholder="Description" class="form-input">

            <label for="answers" class="form-label">Answer Options</label>
            <div class="answers">
                <div class="answer">
                    <input type="text" name="answers[]" placeholder="Option 1" class="form-input">
                    <?php if (images_enabled): ?>
                    <label class="file-input mar-top-2">
                        <span class="file-icon"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,19H5V5H19M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M13.96,12.29L11.21,15.83L9.25,13.47L6.5,17H17.5L13.96,12.29Z" /></svg></span>
                        <span class="file-name">Select Image 1...</span>
                        <input id="image" name="images[]" type="file" placeholder="Image" class="image">
                    </label>
                    <?php endif; ?>
                </div>
            </div>
            <a href="#" class="add_answer form-link mar-top-2"><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" /></svg>Add Option</a>

            <label for="categories" class="form-label">Categories</label>
            <div class="multiselect" data-name="categories[]">
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
                    <input type="datetime-local" id="start_date" name="start_date" value="<?=date('Y-m-d\TH:i')?>" class="form-input">
                </div>
                <div>
                    <label for="end_date" class="form-label">End Date <span>(optional)</span></label>
                    <input type="datetime-local" id="end_date" name="end_date" min="<?=date('Y-m-d\TH:i')?>" class="form-input">
                </div>
            </div>

            <label for="num_choices" class="form-label">How many choices can the user select?</label>
            <input type="number" id="num_choices" name="num_choices" value="1" class="form-input">

            <div class="btns mar-top-4">
                <button type="submit" class="btn blue">Create</button>
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