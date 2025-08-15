<?php
include 'main.php';
// Default page values
$page = [
    'title' => '',
    'description' => '',
    'url' => ''
];
if (isset($_GET['id'])) {
    // Retrieve the page from the database
    $stmt = $pdo->prepare('SELECT * FROM event_page_details WHERE page_id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($results) {
        $page = $results;
        if (isset($_POST['submit'])) {
            // Update the event_page_details table
            $stmt = $pdo->prepare('UPDATE event_page_details SET title = ?, description = ?, url = ? WHERE page_id = ?');
            $stmt->execute([ $_POST['title'], $_POST['description'], $_POST['url'], $_GET['id'] ]);
            header('Location: event_pages.php?success_msg=1');
            exit;
        }
    } else {
        if (isset($_POST['submit'])) {
            // Insert into the event_page_details table
            $stmt = $pdo->prepare('INSERT INTO event_page_details (page_id, title, description, url) VALUES (?,?,?,?)');
            $stmt->execute([ $_GET['id'], $_POST['title'], $_POST['description'], $_POST['url'] ]);
            header('Location: event_pages.php?success_msg=1');
            exit;
        }
    }
} else {
    exit('No ID specified.');
}
?>
<?=template_admin_header('Edit Page Details', 'pages', 'manage')?>

<form action="" method="post">

    <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
        <h2 class="responsive-width-100">Edit Page Details</h2>
        <a href="event_pages.php" class="btn alt mar-right-2">Cancel</a>
        <input type="submit" name="submit" value="Save" class="btn">
    </div>

    <div class="content-block">

        <div class="form responsive-width-100">

            <label for="title">Title</label>
            <input id="title" type="text" name="title" placeholder="Title" value="<?=htmlspecialchars($page['title'], ENT_QUOTES)?>">

            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Description"><?=htmlspecialchars($page['description'], ENT_QUOTES)?></textarea>

            <label for="url">URL</label>
            <p class="comment">The page URL where the calendar is located.</p>
            <input id="url" type="text" name="url" placeholder="URL" value="<?=htmlspecialchars($page['url'], ENT_QUOTES)?>">

        </div>

    </div>

</form>

<?=template_admin_footer()?>