<?php
/**
 * Blog Widget Management
 *
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: add_widget.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Add new blog widgets (sidebar, header, footer)
 *
 * CREATED: 2025-07-04
 * UPDATED: 2025-07-24
 * VERSION: 2.0
 * PRODUCTION: [READY FOR PRODUCTION]
 *
 * CHANGE LOG:
 * 2025-07-04 - Original Development
 * 2025-07-24 - UI/UX improvements, ARIA accessibility, Bootstrap layout
 * 2025-07-24 - Passed Quality Assurance and Accessibility Testing
 *
 * FEATURES:
 * - Add new widgets with title, content, and position
 * - Bootstrap 5 layout and styling
 * - ARIA accessibility for form fields
 * - SummerNote rich text editor for content
 * - PDO prepared statements for security
 */
include "header.php";

if (isset($_POST['add']))
{
	$title = addslashes($_POST['title']);
	$content = htmlspecialchars($_POST['content']);
	$position = addslashes($_POST['position']);

	$stmt = $pdo->prepare("INSERT INTO widgets (title, content, position) VALUES (?, ?, ?)");
	$stmt->execute([$title, $content, $position]);
	echo '<meta http-equiv="refresh" content="0; url=widgets.php">';
}
?>
<?= template_admin_header('Add Blog Widget', 'blog', 'widgets') ?>

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
			<h2>Create a New Widget</h2>
			<p>Create a custom widget for your blog's sidebar, header, or footer area.</p>
		</div>
	</div>
</div>
<br <div class="mb-3">
<a href="widgets.php" class="btn btn-primary" aria-label="Cancel and return to widgets list">
	<i class="fas fa-arrow-left me-1"></i>Cancel
</a>
</div>
<br> <br>
<div class="card">
	<h6 class="professional-card-header">Add Widget</h6>
	<div class="card-body">
		<form action="" method="post" aria-label="Add Widget Form" role="form">
			<div class="row mb-3">
				<div class="col-md-9">
					<label for="title" class="form-label">Title</label>
					<input class="form-control" name="title" id="title" value="" type="text" required
						aria-required="true" aria-label="Widget Title">
				</div>
				<div class="col-md-3">
					<label for="position" class="form-label">Position:</label>
					<select class="form-select" name="position" id="position" required aria-required="true"
						aria-label="Widget Position">
						<option value="Sidebar" selected>Sidebar</option>
						<option value="Header">Header</option>
						<option value="Footer">Footer</option>
					</select>
				</div>
			</div>
			<p>
				<label for="summernote">Content</label>
				<textarea class="form-control" id="summernote" name="content" required aria-required="true"
					aria-label="Widget Content"></textarea>
			</p>
			<button type="submit" name="add" class="btn btn-primary"> Save Widget</button>
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