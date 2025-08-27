<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOCATION: public_html/admin/blog/add_menu.php
 * LOG: Add new menu items to navigation
 * PRODUCTION: [To be updated on deployment]
 */

include "header.php";

if (isset($_POST['add']))
{
	$page = $_POST['page'];
	$path = $_POST['path'];
	$fa_icon = $_POST['fa_icon'];

	$add_stmt = $pdo->prepare("INSERT INTO blog_menu (page, path, fa_icon) VALUES (?, ?, ?)");
	$add_stmt->execute([$page, $path, $fa_icon]);

	echo '<meta http-equiv="refresh" content="0;url=menu_editor.php">';
}
?>

<?= template_admin_header('Add Menu', 'blog', 'menu') ?>
<div class="professional-card-header">
	<div class="title">
		<div class="icon">
			<svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" aria-hidden="true"
				focusable="false">
				<path
					d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z" />
			</svg>
		</div>
		<div class="txt">
			<h2>Add a Blog Menu</h2>
			<p>Add a new menu item for the blog system to use.</p>
		</div>
	</div>
</div>
<br>

<div class="mb-3">
	<a href="menu_editor.php" class="btn btn-primary" aria-label="Cancel and return to menu editor">
		<i class="fa fa-arrow-left" aria-hidden="true"></i> Cancel
	</a>
</div>


<div class="card">
	<h6 class="professional-card-header">Add Menu</h6>
	<div class="card-body">
		<form action="" method="post">
			<p>
				<label>Title</label>
				<input class="form-control" name="page" value="" type="text" required>
			</p>
			<p>
				<label>Path (Link)</label>
				<input class="form-control" name="path" value="" type="text" required>
			</p>
			<p>
				<label>Font Awesome 5 Icon</label>
				<input class="form-control" name="fa_icon" value="" type="text">
			</p>
			<div class="form-actions d-flex justify-content-start">
				<button type="submit" name="add" class="btn btn-primary" aria-label="Save menu item">
					<i class="fas fa-save me-2"></i>Save Menu
				</button>
			</div>
		</form>
	</div>
</div>
<?= template_admin_footer(); ?>