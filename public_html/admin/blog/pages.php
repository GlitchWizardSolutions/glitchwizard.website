<?php
/* 
 * Blog Pages Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: pages.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Manage static blog pages and custom content
 * DETAILED DESCRIPTION:
 * This file provides an interface for managing static blog pages and
 * custom content. It supports page creation, editing, organization,
 * and template management for non-blog content within the blog system.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/assets/includes/settings/blog_config.php
 * - /public_html/assets/includes/settings/pages_config.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Page creation and editing
 * - Template management
 * - Custom content types
 * - Page organization
 * - SEO optimization
 */
 


include_once "header.php";

if (isset($_GET['delete']))
{
	$id = (int) $_GET["delete"];

	// Use PDO and blog_ prefix
	$stmtValid = $pdo->prepare("SELECT * FROM blog_pages WHERE id = ? LIMIT 1");
	$stmtValid->execute([$id]);
	if ($stmtValid->rowCount() > 0)
	{
		$rowvalidator = $stmtValid->fetch(PDO::FETCH_ASSOC);
		$slug = $rowvalidator['slug'];

		$stmtMenu = $pdo->prepare("DELETE FROM blog_menu WHERE path = ?");
		$stmtMenu->execute(["page?name=$slug"]);
		$stmtPage = $pdo->prepare("DELETE FROM blog_pages WHERE id = ?");
		$stmtPage->execute([$id]);
	}
} ?>

<?= template_admin_header('Blog Pages', 'blog', 'pages') ?>

<!-- Page Header Section -->
<div class="content-title mb-4" id="main-blog-pages" role="banner" aria-label="Blog Pages Management Header">
	<div class="title">
		<div class="icon">
			<svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
				<path d="M96 96c0-35.3 28.7-64 64-64H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H80c-44.2 0-80-35.8-80-80V128c0-17.7 14.3-32 32-32s32 14.3 32 32V400c0 8.8 7.2 16 16 16s16-7.2 16-16V96zm64 24v80c0 13.3 10.7 24 24 24H296c13.3 0 24-10.7 24-24V120c0-13.3-10.7-24-24-24H184c-13.3 0-24 10.7-24 24zm208-8c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zM160 304c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16z"/>
			</svg>
		</div>
		<div class="txt">
			<h2>Pages Management</h2>
			<p>Create and manage static pages for your blog with custom content and navigation.</p>
		</div>
	</div>
</div>

 
<!-- Action Buttons Section -->
<div class="mb-4">
	<?php if (isset($_GET['edit'])): ?>
		<!-- Edit Mode Buttons -->
		<div class="d-flex gap-2 flex-wrap">
			<a href="pages.php" class="btn btn-outline-secondary"
				aria-label="Cancel and return to pages list">
				<i class="fa fa-arrow-left me-1" aria-hidden="true"></i>
				Cancel
			</a>
			<button type="submit" form="edit-page-form" class="btn btn-success" name="submit">
				<i class="fas fa-save me-1"></i>
				Save Changes
			</button>
			
		</div>
	<?php else: ?>
		<!-- Default Mode Buttons -->
		<a href="add_page.php" class="btn btn-outline-secondary">
			<i class="fas fa-plus me-1"></i>
			Add Page
		</a>
	<?php endif; ?>
</div>
<?php if (isset($_GET['edit'])): ?>
	<?php
	$id = (int) $_GET["edit"];
	$stmt = $pdo->prepare("SELECT * FROM blog_pages WHERE id = ?");
	$stmt->execute([$id]);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$slug_old = $row['slug'];

	if (empty($id) || !$row)
	{
		header("Location: pages.php");
		exit;
	}

	if (isset($_POST['submit']))
	{
		$title = $_POST['title'];
		$slug = generateSeoURL($title, 0);
		$content = htmlspecialchars($_POST['content']);

		$stmtValid = $pdo->prepare("SELECT * FROM blog_pages WHERE title = ? AND id != ? LIMIT 1");
		$stmtValid->execute([$title, $id]);
		if ($stmtValid->rowCount() > 0)
		{
			echo '
			<div class="alert alert-warning">
				<i class="fas fa-info-circle"></i> Page with this name has already been added.
			</div>';
		} else
		{
			$stmtUpdate = $pdo->prepare("UPDATE blog_pages SET title = ?, slug = ?, content = ? WHERE id = ?");
			$stmtUpdate->execute([$title, $slug, $content, $id]);
			$stmtMenu = $pdo->prepare("UPDATE blog_menu SET page = ?, path = ? WHERE path = ?");
			$stmtMenu->execute([$title, "page?name=$slug", "page?name=$slug_old"]);

			header("Location: pages.php");
			exit;
		}
	}
	?>
	<form id="edit-page-form" action="" method="post" class="needs-validation" novalidate>
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">
					<i class="fas fa-edit me-2"></i>
					Edit Page
				</h6>
			</div>
			<div class="card-body">
				<div class="mb-3">
					<label for="title" class="form-label">Page Title</label>
					<input name="title" id="title" type="text" class="form-control"
						value="<?= htmlspecialchars($row['title']) ?>" required>
					<div class="form-text">
						<i class="fas fa-info-circle me-1"></i>
						Page title will be used for navigation and SEO
					</div>
				</div>
				<div class="mb-0">
					<label for="summernote" class="form-label">Page Content</label>
					<textarea name="content" id="summernote" required><?= html_entity_decode($row['content']) ?></textarea>
				</div>
			</div>
			<div class="card-footer bg-light">
				<div class="d-flex gap-2 flex-wrap">
					<a href="pages.php" class="btn btn-outline-secondary"
						aria-label="Cancel and return to pages list">
						<i class="fa fa-arrow-left me-1" aria-hidden="true"></i>
						Cancel
					</a>
					<button type="submit" class="btn btn-success" name="submit">
						<i class="fas fa-save me-1"></i>
						Save Changes
					</button>
					<a href="add_page.php" class="btn btn-outline-secondary">
						<i class="fas fa-plus me-1"></i>
						New Page
					</a>
				</div>
			</div>
		</div>
	</form>
<?php else: ?>

<!-- Content Management View -->
<div class="card">
	<div class="card-header">
		<h6 class="mb-0">
			<i class="fas fa-file-alt me-2"></i>
			Blog Pages
		</h6>
	</div>
	<div class="card-body p-0">
		<div class="table-responsive" role="table" aria-label="Blog Pages">
			<table class="table table-hover align-middle mb-0" role="grid">
				<thead class="table-light" role="rowgroup">
					<tr role="row">
						<th class="text-start" role="columnheader" scope="col">
							Page Title
						</th>
						<th class="text-start" role="columnheader" scope="col">
							Slug
						</th>
						<th class="text-center" role="columnheader" scope="col">
							Actions
						</th>
					</tr>
				</thead>
				<tbody role="rowgroup">
						<?php
					$stmt = $pdo->query("SELECT * FROM blog_pages ORDER BY title ASC");
					$pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if (!empty($pages)):
						foreach ($pages as $row):
					?>
						<tr role="row">
							<td class="text-start" role="gridcell">
								<?= htmlspecialchars($row['title']) ?>
							</td>
							<td class="text-start" role="gridcell">
								<code class="text-muted"><?= htmlspecialchars($row['slug']) ?></code>
							</td>
							<td class="actions text-center" role="gridcell">
								<div class="table-dropdown">
									<button class="actions-btn" aria-haspopup="true" aria-expanded="false" 
										aria-label="Actions for page <?= htmlspecialchars($row['title']) ?>">
										<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
											<path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
										</svg>
									</button>
									<div class="table-dropdown-items" role="menu" aria-label="Page Actions">
										<div role="menuitem">
											<a href="?edit=<?= $row['id'] ?>" class="green" tabindex="-1" 
												aria-label="Edit page <?= htmlspecialchars($row['title']) ?>">
												<i class="fas fa-edit" aria-hidden="true"></i>
												<span>Edit</span>
											</a>
										</div>
										<div role="menuitem">
											<a href="?delete=<?= $row['id'] ?>" class="red" tabindex="-1" 
												onclick="return confirm('Are you sure you want to delete this page?')" 
												aria-label="Delete page <?= htmlspecialchars($row['title']) ?>">
												<i class="fas fa-trash" aria-hidden="true"></i>
												<span>Delete</span>
											</a>
										</div>
									</div>
								</div>
							</td>
						</tr>
					<?php 
						endforeach;
					else:
					?>
						<tr role="row">
							<td colspan="3" class="text-center text-muted py-4">
								<i class="fas fa-file-alt fa-2x mb-2 d-block"></i>
								No pages created yet. <a href="add_page.php">Create your first page</a>.
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="card-footer bg-light">
		<div class="small">
			<?= count($pages) ?> page<?= count($pages) !== 1 ? 's' : '' ?> total
		</div>
	</div>
</div>
<?php endif; ?>
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