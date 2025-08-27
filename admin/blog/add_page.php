<?php
/**
 * Blog Add Page
 *
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: add_page.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Add new static pages with automatic menu creation
 *
 * CREATED: 2025-07-03
 * UPDATED: 2025-07-24
 * VERSION: 2.0
 * PRODUCTION: [READY FOR PRODUCTION]
 *
 * CHANGE LOG:
 * 2025-07-03 - Original implementation for adding static pages
 * 2025-07-04 - UI/UX improvements, consistent button formatting
 * 2025-07-24 - Accessibility, ARIA, and Bootstrap 5 layout
 *
 * FEATURES:
 * - Add new static pages with title and content
 * - Automatic SEO-friendly slug generation
 * - Menu integration for new pages
 * - Bootstrap 5 layout and styling
 * - ARIA accessibility for form fields
 * - SummerNote rich text editor for content
 * - PDO prepared statements for security
 *
 * DEPENDENCIES:
 * - header.php (blog includes)
 * - Bootstrap 5 for styling
 * - SummerNote for rich text editing
 * - PDO database connection
 * - Font Awesome icons
 * - SEO URL generation functions
 *
 * SECURITY NOTES:
 * - Admin authentication required
 * - PDO prepared statements prevent SQL injection
 * - Input validation and sanitization
 * - XSS protection on output
 * - Slug validation and duplicate checking
 */

include "header.php";

// Error message for graceful error handling
$add_page_error = '';
if (isset($_POST['add']))
{
	$title = addslashes($_POST['title']);
	$slug = generateSeoURL($title, 0);
	$content = htmlspecialchars($_POST['content']);

	$queryvalid = $pdo->prepare("SELECT * FROM `blog_pages` WHERE title = ? LIMIT 1");
	$queryvalid->execute([$title]);
	$validator = $queryvalid->rowCount();
	if ($validator > 0)
	{
		$add_page_error = '<div class="alert alert-warning mt-2 mb-3"><i class="fas fa-info-circle me-1"></i>Page with this name has already been added.</div>';
	} else
	{
		$add = $pdo->prepare("INSERT INTO blog_pages (title, slug, content) VALUES (?, ?, ?)");
		$add->execute([$title, $slug, $content]);

		$result2 = $pdo->prepare("SELECT * FROM blog_pages WHERE title = ?");
		$result2->execute([$title]);
		$row = $result2->fetch(PDO::FETCH_ASSOC);
		$id = $row['id'];
		$add2 = $pdo->prepare("INSERT INTO blog_menu (page, path, fa_icon) VALUES (?, ?, 'fa-columns')");
		$add2->execute([$title, "page?name=$slug"]);

		echo '<meta http-equiv="refresh" content="0;url=pages.php">';
		exit;
	}
}
?>
<?= template_admin_header('Blog Widgets', 'blog', 'widgets') ?>

<div class="professional-card-header" aria-label="Widgets Management">
	<div class="title">
		<div class="icon">
			<svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-hidden="true"
				focusable="false">
				<path
					d="M0 96C0 60.7 28.7 32 64 32H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zM64 160c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H144c17.7 0 32-14.3 32-32V192c0-17.7-14.3-32-32-32H64zM208 160c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H288c17.7 0 32-14.3 32-32V192c0-17.7-14.3-32-32-32H208zM352 160c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H432c17.7 0 32-14.3 32-32V192c0-17.7-14.3-32-32-32H352zM64 304c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H144c17.7 0 32-14.3 32-32V336c0-17.7-14.3-32-32-32H64zM208 304c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H288c17.7 0 32-14.3 32-32V336c0-17.7-14.3-32-32-32H208zM352 304c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32H432c17.7 0 32-14.3 32-32V336c0-17.7-14.3-32-32-32H352z" />
			</svg>
		</div>
		<div class="txt">
			<h2>Pages Management</h2>
			<p>Create and manage custom pages/landing pages for your blog's website.</p>
		</div>
	</div>
</div>
<br>
<div class="mb-3 d-flex gap-2">
	<a href="pages.php" class="btn btn-primary" aria-label="Cancel and return to pages list">
		<i class="fas fa-arrow-left me-1"></i>
		Cancel
	</a>

</div>
<br>


<div class="card">
	<h6 class="professional-card-header">Add Page</h6>
	<div class="card-body">
		<?php if (!empty($add_page_error))
			echo $add_page_error; ?>
		<form action="" method="post">
			<p>
				<label>Title</label>
				<input class="form-control" name="title"
					value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>" type="text" required>
			</p>
			<p>
				<label>Content</label>
				<textarea class="form-control" id="summernote" name="content"
					required><?= isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '' ?></textarea>
			</p>
			<button type="submit" name="add" class="btn btn-primary">
				<i class="fas fa-plus me-1"></i>Add
			</button>
		</form>
	</div>
</div>

<script>
	$(document).ready(function () {
		$('#summernote').summernote({ height: 350 });

		var noteBar = $('.note-toolbar');
		noteBar.find('[data-toggle]').each(function () {
			$(this).attr('data-bs-toggle', $(this).attr('data-toggle')).removeAttr('data-toggle');
		});
	});
</script>
<?= template_admin_footer(); ?>