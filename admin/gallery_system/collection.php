<?php
include '../assets/includes/main.php';
// Default input collection values
$collection = [
    'title' => '',
    'description_text' => '',
    'account_id' => '',
    'is_public' => 1
];
// Get all accounts
$stmt = $pdo->prepare('SELECT id, username, email FROM accounts ORDER BY id');
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// If editing an collection
if (isset($_GET['id'])) {
    // Get the collection from the database
    $stmt = $pdo->prepare('SELECT * FROM gallery_collections WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $collection = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing collection
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Check to see if title already exists
        $stmt = $pdo->prepare('SELECT id FROM gallery_collections WHERE title = ? AND title != ? AND account_id = ?');
        $stmt->execute([ $_POST['title'], $collection['title'], $_POST['account_id'] ]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error_msg = 'Collection already exists with that title!';
        }
        // Update the collection
        if (!isset($error_msg)) {
            $stmt = $pdo->prepare('UPDATE gallery_collections SET title = ?, description_text = ?, account_id = ?, is_public = ? WHERE id = ?');
            $stmt->execute([ $_POST['title'], $_POST['description_text'], $_POST['account_id'], $_POST['is_public'], $_GET['id'] ]);
            header('Location: collections.php?success_msg=2');
            exit;
        } else {
            // Update the collection variables
            $collection = [
                'title' => $_POST['title'],
                'description_text' => $_POST['description_text'],
                'account_id' => $_POST['account_id'],
                'is_public' => $_POST['is_public']
            ];
        }
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete the collection
        header('Location: collections.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new collection
    $page = 'Create';
    if (isset($_POST['submit'])) {
        // Check to see if title already exists
        $stmt = $pdo->prepare('SELECT id FROM gallery_collections WHERE title = ? AND account_id = ?');
        $stmt->execute([ $_POST['title'], $_POST['account_id'] ]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $error_msg = 'Collection already exists with that title!';
        }
        // Insert the collection
        if (!isset($error_msg)) {
            $stmt = $pdo->prepare('INSERT INTO gallery_collections (title, description_text, account_id, is_public) VALUES (?, ?, ?, ?)');
            $stmt->execute([ $_POST['title'], $_POST['description_text'], $_POST['account_id'], $_POST['is_public'] ]);
            header('Location: collections.php?success_msg=1');
            exit;
        } else {
            // Update the collection variables
            $collection = [
                'title' => $_POST['title'],
                'description_text' => $_POST['description_text'],
                'account_id' => $_POST['account_id'],
                'is_public' => $_POST['is_public']
            ];
        }
    }
}
?>
<?=template_admin_header($page . ' Collection', 'gallery', 'collections_manage')?>

<div class="content-title mb-4" id="main-gallery-collection-form" role="banner" aria-label="Gallery Collection Form Header">
    <div class="title">
        <div class="icon">
            <i class="bi bi-collection" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2><?=$page?> Collection</h2>
            <p><?=$page == 'Edit' ? 'Modify collection settings and media selection.' : 'Create a new collection to organize media files.'?></p>
        </div>
    </div>
</div>

<div class="mb-4">
    <div class="d-flex gap-2">
        <a href="collections.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Cancel
        </a>
        <button type="submit" name="submit" form="collection-form" class="btn btn-success">
            <i class="bi bi-save me-1"></i>Save Collection
        </button>
        <?php if ($page == 'Edit'): ?>
        <button type="submit" name="delete" form="collection-form" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this collection?')">
            <i class="bi bi-trash me-1"></i>Delete
        </button>
        <?php endif; ?>
    </div>
</div>

<form method="post" id="collection-form" class="card">
    <div class="card-header">
        <h6 class="mb-0"><?=$page?> Collection</h6>
    </div>
    <div class="card-body">
        <?php if (isset($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?=$error_msg?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Collection Title -->
        <div class="mb-3">
            <label for="title" class="form-label">
                <span class="text-danger">*</span> Title
            </label>
            <input type="text" id="title" name="title" class="form-control" placeholder="Enter collection title" value="<?=htmlspecialchars($collection['title'], ENT_QUOTES)?>" required>
        </div>

        <!-- Collection Description -->
        <div class="mb-3">
            <label for="description_text" class="form-label">Description</label>
            <textarea id="description_text" name="description_text" class="form-control" rows="4" placeholder="Enter collection description"><?=htmlspecialchars($collection['description_text'], ENT_QUOTES)?></textarea>
        </div>

        <!-- Account Selection -->
        <div class="mb-3">
            <label for="account_id" class="form-label">
                <span class="text-danger">*</span> Account
            </label>
            <select id="account_id" name="account_id" class="form-select" required>
                <?php foreach ($accounts as $account): ?>
                <option value="<?=$account['id']?>"<?=$account['id']==$collection['account_id']?' selected':''?>>[<?=$account['id']?>] <?=htmlspecialchars($account['username'], ENT_QUOTES)?> (<?=htmlspecialchars($account['email'], ENT_QUOTES)?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Public Status -->
        <div class="mb-3">
            <label for="is_public" class="form-label">Visibility</label>
            <select id="is_public" name="is_public" class="form-select">
                <option value="1"<?=$collection['is_public']?' selected':''?>>Public - Visible to everyone</option>
                <option value="0"<?=$collection['is_public']?'':' selected'?>>Private - Only visible to account owner</option>
            </select>
        </div>
    </div>
</form>

<?=template_admin_footer()?>