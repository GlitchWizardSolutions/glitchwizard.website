<?php
include '../assets/includes/main.php';
// Default input like values
$like = [
    'media_id' => '',
    'acc_id' => ''
];
// Get all accounts
$stmt = $pdo->prepare('SELECT id, display_name, email FROM accounts ORDER BY id');
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Get all media
$stmt = $pdo->prepare('SELECT id, title FROM media ORDER BY id');
$stmt->execute();
$media = $stmt->fetchAll(PDO::FETCH_ASSOC);
// If editing an like
if (isset($_GET['id'])) {
    // Get the like from the database
    $stmt = $pdo->prepare('SELECT * FROM media_likes WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $like = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing like
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('UPDATE media_likes SET media_id = ?, acc_id = ? WHERE id = ?');
        $stmt->execute([ $_POST['media_id'], $_POST['acc_id'], $_GET['id'] ]);
        header('Location: likes.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete the like
        header('Location: likes.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Add a new like
    $page = 'Add';
    if (isset($_POST['submit'])) {
        // Insert the like
        $stmt = $pdo->prepare('INSERT IGNORE INTO media_likes (media_id, acc_id) VALUES (?, ?)');
        $stmt->execute([ $_POST['media_id'], $_POST['acc_id'] ]);
        header('Location: likes.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Media Like', 'media', 'view_likes')?>

<form method="post">

    <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
        <h2><?=$page?> Media Like</h2>
        <div class="btns">
            <a href="likes.php" class="btn alt mar-right-1">Cancel</a>
            <?php if ($page == 'Edit'): ?>
            <input type="submit" name="delete" value="Delete" class="btn red mar-right-1" onclick="return confirm('Are you sure you want to delete this like?')">
            <?php endif; ?>
            <input type="submit" name="submit" value="Save" class="btn">
        </div>
    </div>

    <div class="content-block">

        <div class="form responsive-width-100">

            <label for="media_id"><span class="required">*</span> Media</label>
            <select id="media_id" name="media_id" required>
                <?php foreach ($media as $item): ?>
                <option value="<?=$item['id']?>"<?=$item['id']==$like['media_id']?' selected':''?>>[<?=$item['id']?>] <?=htmlspecialchars($item['title'], ENT_QUOTES)?></option>
                <?php endforeach; ?>
            </select>

            <label for="acc_id"><span class="required">*</span> Account</label>
            <select id="acc_id" name="acc_id" required>
                <?php foreach ($accounts as $account): ?>
                <option value="<?=$account['id']?>"<?=$account['id']==$like['acc_id']?' selected':''?>>[<?=$account['id']?>] <?=htmlspecialchars($account['display_name'], ENT_QUOTES)?> (<?=htmlspecialchars($account['email'], ENT_QUOTES)?>)</option>
                <?php endforeach; ?>
            </select>

        </div>

    </div>

</form>

<?=template_admin_footer()?>