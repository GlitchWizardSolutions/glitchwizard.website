<?php
/*
PAGE NAME  : my-comments.php
LOCATION   : public_html/my-comments.php
DESCRIPTION: This page displays a user's comments on blog posts.
FUNCTION   : Authenticated users can view and manage their comments on blog posts.
            Users can see their comment history, delete comments, and check approval status.
            Links to the original posts are provided for easy navigation.
CHANGE LOG : Initial creation of my-comments.php for comment management.
2025-08-25 : Added comment approval status indicators
2025-08-26 : Enhanced delete confirmation and security
*/
// Include necessary files
include_once "../private/gws-universal-functions.php";
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
include_once "assets/includes/blog_load.php";
if ($logged == 'No') {
	header('Location: auth.php?tab=login');
	exit;
} 
?>
<div class="container<?php echo ($settings['layout'] == 'Wide') ? '-fluid' : ''; ?> mt-3 mb-5">
	<div class="row">
		<?php if ($settings['sidebar_position'] == 'Left') { echo '<div class="col-md-4 order-1 order-md-1 mb-3">'; sidebar(); echo '</div>'; } ?>
		<div class="col-md-8 order-2 order-md-2 mb-3">
			<?php
			renderCard('My Comments', 'bi bi-chat-dots');
			?>
					<?php
					if ($logged == 'No') {
						echo '<div class="alert alert-danger">You must be logged in to view your comments. <a href="auth.php?tab=login">Login</a></div>';
					} else {
					$account_id = (isset($rowusers) && is_array($rowusers) && isset($rowusers['id'])) ? $rowusers['id'] : null;
					// Handle delete
					if (isset($_GET['delete-comment'])) {
						$id = (int) $_GET['delete-comment'];
						$stmt_del = $pdo->prepare("DELETE FROM blog_comments WHERE account_id = ? AND id = ?");
						$stmt_del->execute([$account_id, $id]);
					}
					// Get comments
					$stmt = $pdo->prepare("SELECT * FROM blog_comments WHERE account_id = ? ORDER BY id DESC");
					$stmt->execute([$account_id]);
						$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
						if (!$comments) {
							echo '<div class="alert alert-info">You have not written any comments yet.</div>';
						} else {
							foreach ($comments as $comment) {
								echo '<div class="card mb-3">
									<div class="row">
										<div class="col-md-12">
											<div class="card-body">
												<h6 class="card-title">
													<div class="row">
														<div class="col-md-10">
															<i class="bi bi-newspaper" aria-hidden="true"></i> On post: <a href="post.php?name=' . htmlspecialchars(post_slug($comment['post_id'])) . '#comments">' . htmlspecialchars(post_title($comment['post_id']))  . '</a>
														</div>
														<div class="col-md-2 d-flex justify-content-end">
															<a href="?delete-comment=' . $comment['id']  . '" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm(\'Are you sure you want to delete this comment?\');">
																<i class="bi bi-trash" aria-hidden="true"></i>
															</a>
														</div>
													</div>
												</h6>
												<p class="card-text">' . htmlspecialchars($comment['comment'])  . '</p>
												<p class="card-text">
													<div class="row">
														<div class="col-md-10">
															<small class="text-muted">' . date($settings['date_format'], strtotime($comment['date'])) . ', ' . htmlspecialchars($comment['time']) . '</small>
														</div>
														<div class="col-md-2 d-flex justify-content-end">';
															if ($comment['approved'] == 'Yes') {
																echo '<span class="badge bg-success"><i class="bi bi-check-lg" aria-hidden="true"></i> Approved</span>';
															} else {
																echo '<span class="badge bg-secondary"><i class="bi bi-clock" aria-hidden="true"></i> Pending</span>';
															}
															echo '</div>
													</div>
												</p>
											</div>
										</div>
									</div>
								</div>';
							}
						}
					}
					?>
			<?php closeCard(); ?>
		</div>
		<?php if ($settings['sidebar_position'] == 'Right') { echo '<div class="col-md-4 order-3 order-md-3 mb-3">'; sidebar(); echo '</div>'; } ?>
	</div>
</div>
<?php 
renderPageFooter();
include_once 'assets/includes/footer.php'; 
?>