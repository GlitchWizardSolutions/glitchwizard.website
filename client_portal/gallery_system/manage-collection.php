<?php
include 'functions.php';
// Page title
$title = 'Create';
// User must be authenticated
if (!isset($_SESSION['account_loggedin'])) {
    header('Location: collections.php');
    exit;
}
// Default values
$collection_defaults = [
    'title' => '',
    'description_text' => '',
    'is_public' => 0
];
// Error message
$error = '';
// If GET ID exists, user is editing media
if (isset($_GET['id'])) {
    $title = 'Edit';
    // Get the collection details
    $stmt = $pdo->prepare('SELECT * FROM collections WHERE id = ? AND acc_id = ?');
	$stmt->execute([ $_GET['id'], $_SESSION['account_id'] ]);
	$collection_defaults = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$collection_defaults) {
        exit('Invalid collection!');
    }
    // Get all media in collection
    $stmt = $pdo->prepare('SELECT m.* FROM media_collections mc JOIN media m ON mc.media_id = m.id WHERE mc.collection_id = ? AND m.acc_id = ? AND m.is_approved = 1');
    $stmt->execute([ $_GET['id'], $_SESSION['account_id'] ]);
    $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Delete collection
    if (isset($_POST['delete'])) {
        $stmt = $pdo->prepare('DELETE c, mc FROM collections c LEFT JOIN media_collections mc ON mc.collection_id = c.id WHERE c.id = ? AND c.acc_id = ?');
        $stmt->execute([ $_GET['id'], $_SESSION['account_id'] ]);        
        header('Location: collections.php');
        exit;         
    }
    // Update collection
    if (isset($_POST['title'], $_POST['description'], $_POST['public'])) {
        // Check if collection title already exists
        $stmt = $pdo->prepare('SELECT * FROM collections WHERE title = ? AND acc_id = ? AND id != ?');
        $stmt->execute([ $_POST['title'], $_SESSION['account_id'], $_GET['id'] ]);
        $collection = $stmt->fetch(PDO::FETCH_ASSOC);
        // Validate input
        if ($collection) {
            $error = 'Collection with that title already exists!';
        } else if (empty($_POST['title'])) {
            $error = 'Please enter a title!';
        } else if (strlen($_POST['title']) > 50) {
            $error = 'Title must be less than 50 characters!';
        } else if (!preg_match('/^[A-Za-z0-9 ]+$/', $_POST['title'])) {
            $error = 'Title must contain only letters, numbers and spaces!';
        } else if (strlen($_POST['description']) > 300) {
            $error = 'Description must be less than 300 characters!';
        } else {
            // Update collection
            $stmt = $pdo->prepare('UPDATE collections SET title = ?, description_text = ?, is_public = ? WHERE id = ? AND acc_id = ?');
            $stmt->execute([ $_POST['title'], $_POST['description'], $_POST['public'], $_GET['id'], $_SESSION['account_id'] ]);
            // Check if media is selected
            if (isset($_POST['media']) && count($_POST['media']) > 0) {
                // Delete all media from collection using IN clause
                $stmt = $pdo->prepare('DELETE FROM media_collections WHERE collection_id = ? AND media_id NOT IN (' . implode(',', array_fill(0, count($_POST['media']), '?')) . ')');
                $stmt->execute(array_merge([ $_GET['id'] ], $_POST['media']));
            } else {
                // Delete all media from collection
                $stmt = $pdo->prepare('DELETE FROM media_collections WHERE collection_id = ?');
                $stmt->execute([ $_GET['id'] ]);
            }
            // Redirect to collections page
            header('Location: collections.php');
            exit; 
        }
    }
} else if (isset($_POST['title'], $_POST['description'], $_POST['public'])) {
    // Check if collection title already exists
    $stmt = $pdo->prepare('SELECT * FROM collections WHERE title = ? AND acc_id = ?');
    $stmt->execute([ $_POST['title'], $_SESSION['account_id'] ]);
    $collection = $stmt->fetch(PDO::FETCH_ASSOC);
    // Validate input
    if ($collection) {
        $error = 'Collection with that title already exists!';  
    } else if (empty($_POST['title'])) {
        $error = 'Please enter a title!';
    } else if (strlen($_POST['title']) > 50) {
        $error = 'Title must be less than 50 characters!';
    } else if (!preg_match('/^[A-Za-z0-9 ]+$/', $_POST['title'])) {
        $error = 'Title must contain only letters, numbers and spaces!';
    } else if (strlen($_POST['description']) > 300) {
        $error = 'Description must be less than 300 characters!';
    } else {   
        // Create collection 
        $stmt = $pdo->prepare('INSERT INTO collections (title, description_text, is_public, acc_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([ $_POST['title'], $_POST['description'], $_POST['public'], $_SESSION['account_id'] ]);
        // Redirect to collections page
        header('Location: collections.php');
        exit;  
    }  
}
?>
<?=template_header($title . ' Collection')?>

<div class="page-content">

	<div class="page-title">
		<h2><?=$title?> Collection</h2>
	</div>

	<form method="post" class="gallery-form alt-form">

		<label for="title">Title</label>
        <input id="title" name="title" type="text" placeholder="Title" value="<?=htmlspecialchars($collection_defaults['title'], ENT_QUOTES)?>" pattern="[A-Za-z0-9 ]+" maxlength="50" required>

		<label for="Description">Description</label>
        <textarea id="title" name="description" placeholder="Description" maxlength="300"><?=htmlspecialchars($collection_defaults['description_text'], ENT_QUOTES)?></textarea>

        <label for="public">Who can view your collection?</label>
        <select id="public" name="public" type="text" required>
            <option value="0"<?=$collection_defaults['is_public']?'':' selected'?>>Only Me</option>
            <option value="1"<?=$collection_defaults['is_public']?' selected':''?>>Everyone</option> 
        </select>

        <?php if ($title == 'Edit'): ?>
        <label for="media">Media</label>
        <div class="media-collection">
            <?php if ($media): ?>
            <?php foreach ($media as $m): ?>
            <div class="media-item">
                <input type="checkbox" name="media[]" value="<?=$m['id']?>" checked>
                <?php if (!file_exists($m['filepath'])): ?>
                <div class="media-error" title="Media not found">
                    <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                </div>
                <?php elseif ($m['media_type'] == 'image'): ?>
                <img src="<?=$m['filepath']?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" width="100" height="100">
                <?php elseif ($m['media_type'] == 'video'): ?>
                <video src="<?=$m['filepath']?>" width="100" height="100" controls></video>
                <?php elseif ($m['media_type'] == 'audio'): ?>
                <audio src="<?=$m['filepath']?>" width="100" height="100" controls></audio>
                <?php endif; ?>
                <h3><?=htmlspecialchars($m['title'], ENT_QUOTES)?></h3>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p>No media in this collection.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

		<div class="btn-wrapper">
			<button type="submit" name="submit" class="btn">Save</button>
            <?php if ($title == 'Edit'): ?>
            <button type="submit" name="delete" class="btn alt" onclick="return confirm('Are you sure you want to delete this collection?')">Delete</button>
            <?php endif; ?>
            <a href="collections.php" class="btn alt">Cancel</a>
		</div>

        <?php if ($error): ?>
        <p class="error-msg"><?=$error?></p>
        <?php endif; ?>
		
	</form>

</div>

<?=template_footer()?>