<?php
include 'functions.php';
// User must be authenticated
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: collections.php');
    exit;
}
// Make sure GET ID param exists
if (isset($_GET['id'])) {
    // Retrieve media
    $stmt = $pdo->prepare('SELECT * FROM media WHERE id = ? AND acc_id = ?');
	$stmt->execute([ $_GET['id'], $_SESSION['account_id'] ]);
	$media = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$media) {
        exit('Invalid ID!');
    }
    // Delete media if delete button triggered
    if (isset($_POST['delete'])) {
        // Delete file
        if (file_exists($media['filepath'])) unlink($media['filepath']);  
        // Delete thumbnail
        if ($media['thumbnail'] && file_exists($media['thumbnail'])) unlink($media['thumbnail']); 
        // Delete all records associated with the media in all tables
        $stmt = $pdo->prepare('DELETE m, mc, ml FROM media m LEFT JOIN media_collections mc ON mc.media_id = m.id LEFT JOIN media_likes ml ON ml.media_id = m.id WHERE m.id = ? AND m.acc_id = ?');
        $stmt->execute([ $_GET['id'], $_SESSION['account_id'] ]);    
        // Redirect to collections page
        header('Location: collections.php');
        exit;         
    }
    // Update media
    if (isset($_POST['title'], $_POST['description'], $_POST['public'])) {
        // Input validation
        if (strlen($_POST['title']) < 3 || strlen($_POST['title']) > 100) {
			$error = 'Title must be between 3 and 100 characters!';
		} else if (strlen($_POST['description']) > 300) {
			$error = 'Description must be less than 300 characters!';
		} else {
            $stmt = $pdo->prepare('UPDATE media SET title = ?, description_text = ?, is_public = ? WHERE id = ? AND acc_id = ?');
            $stmt->execute([ $_POST['title'], $_POST['description'], $_POST['public'], $_GET['id'], $_SESSION['account_id'] ]);
            header('Location: collections.php');
            exit; 
        }
    }
} else {
    exit('Invalid ID!');
}
?>
<?=template_header('Edit Media')?>

<div class="page-content">

    <div class="page-title">
		<h2>Edit Media</h2>
	</div>

	<form method="post" class="gallery-form alt-form">

		<label for="title">Title</label>
        <input id="title" name="title" type="text" placeholder="Title..." value="<?=htmlspecialchars($media['title'], ENT_QUOTES)?>" required>

		<label for="Description">Description</label>
        <textarea id="title" name="description" placeholder="Description..."><?=htmlspecialchars($media['description_text'], ENT_QUOTES)?></textarea>

        <label for="public">Who can view your media?</label>
        <select id="public" name="public" type="text" required>
            <option value="1"<?=$media['is_public']?' selected':''?>>Everyone</option> 
            <option value="0"<?=!$media['is_public']?' selected':''?>>Only Me</option>
        </select>

		<div class="btn-wrapper">
			<button type="submit" name="submit" class="btn">Save</button>
            <button type="submit" name="delete" class="btn alt" onclick="return confirm('Are you sure you want to delete this media?')">Delete</button>
            <a href="collections.php" class="btn alt">Cancel</a>
		</div>

        <?php if (isset($error) && $error): ?>
        <p class="error-msg"><?=$error?></p>
        <?php endif; ?>
		
	</form>

</div>

<?=template_footer()?>