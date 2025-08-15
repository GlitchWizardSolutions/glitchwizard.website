<?php
include 'main.php';
// Default event values
$event = [
    'title' => '',
    'description' => '',
    'color' => '',
    'datestart' => date('Y-m-d H:i'),
    'dateend' => date('Y-m-d H:i'),
    'uid' => '',
    'submit_date' => date('Y-m-d H:i'),
    'recurring' => 'never',
    'photo_url' => '',
    'redirect_url' => ''
];
// Check if the file is not empty
if (isset($_FILES['photo']) && !empty($_FILES['photo']['tmp_name'])) {
    // Check if the file is an image
    if (getimagesize($_FILES['photo']['tmp_name']) !== false) {
        // Generate a unique name for the image
        $photo_url = upload_directory . md5(uniqid()) . '.' . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        // Move the image to the uploads folder
        move_uploaded_file($_FILES['photo']['tmp_name'], '../' . $photo_url);
    }
}
// Check if the ID param exists
if (isset($_GET['id'])) {
    // Retrieve the event from the database
    $stmt = $pdo->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing event
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the event
        if (!isset($error_msg)) {
            $page_id = $_POST['uid'] == '' ? 1 : $_POST['uid'];
            $color = $_POST['color'] == '' ? '#2163BA' : $_POST['color'];
            // Update the event
            $stmt = $pdo->prepare('UPDATE events SET title = ?, description = ?, color = ?, datestart = ?, dateend = ?, uid = ?, submit_date = ?, recurring = ?, photo_url = ?, redirect_url = ? WHERE id = ?');
            $stmt->execute([ $_POST['title'], $_POST['description'], $color, $_POST['datestart'], $_POST['dateend'], $page_id, $_POST['submit_date'], $_POST['recurring'], isset($photo_url) ? $photo_url : $event['photo_url'], $_POST['redirect_url'], $_GET['id'] ]);
            header('Location: events.php?success_msg=2');
            exit;
        } else {
            // Save the submitted values
            $event = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'color' => $_POST['color'],
                'datestart' => $_POST['datestart'],
                'dateend' => $_POST['dateend'],
                'uid' => $_POST['uid'],
                'submit_date' => $_POST['submit_date'],
                'recurring' => $_POST['recurring'],
                'photo_url' => $_POST['photo_url'],
                'redirect_url' => $_POST['redirect_url']
            ];
        }
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete event
        header('Location: events.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new event
    $page = 'Create';
    if (isset($_POST['submit'])) {
        // Insert the event
        $page_id = $_POST['uid'] == '' ? 1 : $_POST['uid'];
        $color = $_POST['color'] == '' ? '#2163BA' : $_POST['color'];
        $stmt = $pdo->prepare('INSERT INTO events (title,description,color,datestart,dateend,uid,submit_date,recurring,photo_url,redirect_url) VALUES (?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([ $_POST['title'], $_POST['description'], $color, $_POST['datestart'], $_POST['dateend'], $page_id, $_POST['submit_date'], $_POST['recurring'], isset($photo_url) ? $photo_url : $event['photo_url'], $_POST['redirect_url'] ]);
        header('Location: events.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Event', 'events', 'manage')?>

<form action="" method="post" enctype="multipart/form-data">

    <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
        <h2 class="responsive-width-100"><?=$page?> Event</h2>
        <a href="events.php" class="btn alt mar-right-2">Cancel</a>
        <?php if ($page == 'Edit'): ?>
        <input type="submit" name="delete" value="Delete" class="btn red mar-right-2" onclick="return confirm('Are you sure you want to delete this event?')">
        <?php endif; ?>
        <input type="submit" name="submit" value="Save" class="btn">
    </div>

    <?php if (isset($error_msg)): ?>
    <div class="mar-top-4">
        <div class="msg error">
            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
            <p><?=$error_msg?></p>
            <svg class="close" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
        </div>
    </div>
    <?php endif; ?>

    <div class="content-block">
        
        <div class="form responsive-width-100">

            <label for="uid">Page ID</label>
            <input type="number" id="uid" name="uid" value="<?=$event['uid']?>" placeholder="Page ID">

            <label for="title"><span class="required">*</span> Title</label>
            <input type="text" id="title" name="title" value="<?=$event['title']?>" placeholder="Title" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Description..."><?=$event['description']?></textarea>

            <label for="datestart"><span class="required">*</span> Start Date</label>
            <input type="datetime-local" id="datestart" name="datestart" value="<?=date('Y-m-d\TH:i', strtotime($event['datestart']))?>" required>

            <label for="dateend"><span class="required">*</span> End Date</label>
            <input type="datetime-local" id="dateend" name="dateend" value="<?=date('Y-m-d\TH:i', strtotime($event['dateend']))?>" required>

            <label for="color">Color</label>
            <input type="text" id="color" name="color" value="<?=$event['color']?>" placeholder="#000000">

            <label for="recurring"><span class="required">*</span> Recurring</label>
            <select id="recurring" name="recurring" style="margin-bottom: 30px;">
                <option value="never"<?=$event['recurring']=='never'?' selected':''?>>Never</option>
                <option value="daily"<?=$event['recurring']=='daily'?' selected':''?>>Daily</option>
                <option value="weekly"<?=$event['recurring']=='weekly'?' selected':''?>>Weekly</option>
                <option value="monthly"<?=$event['recurring']=='monthly'?' selected':''?>>Monthly</option>
                <option value="yearly"<?=$event['recurring']=='yearly'?' selected':''?>>Yearly</option>
            </select>

            <label for="photo">Photo URL</label>
            <?php if (!empty($event['photo_url'])): ?>
            <img src="../<?=$event['photo_url']?>" alt="<?=$event['title']?>" style="max-width:200px;max-height:200px;width:100%;height:auto;margin:15px 0;">
            <?php endif; ?>
            <input id="photo" type="file" name="photo" accept="image/*">

            <label for="redirect_url">Redirect URL</label>
            <input type="text" id="redirect_url" name="redirect_url" value="<?=$event['redirect_url']?>" placeholder="Redirect URL">

            <label for="submit_date"><span class="required">*</span> Submit Date</label>
            <input type="datetime-local" id="submit_date" name="submit_date" value="<?=date('Y-m-d\TH:i', strtotime($event['submit_date']))?>" required>

        </div>
    
    </div>

</form>

<?=template_admin_footer()?>