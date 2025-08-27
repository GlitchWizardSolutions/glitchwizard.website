<?php
/**
 * SYSTEM: Blog System
 * LOCATION: public_html/admin/blog/
 * LOG:
 * 2025-07-04 - Original Development
 * PRODUCTION:
 */
include "header.php";

$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM `blog_messages` WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt_update = $pdo->prepare("UPDATE `blog_messages` SET viewed = 'Yes' WHERE id = ?");
$stmt_update->execute([$id]);

if (empty($id))
{
	echo '<meta http-equiv="refresh" content="0; url=blog_messages.php">';
	exit;
}
if (!$row)
{
	echo '<meta http-equiv="refresh" content="0; url=blog_messages.php">';
	exit;
}
?>

<?= template_admin_header('Read Message', 'blog', 'messages') ?>

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
			<h2>Read Message</h2>
			<p>View the details of a specific message.</p>
		</div>
	</div>
</div>
<br>

<!-- Action Buttons Row -->
<div class="row mb-3" style="gap: 10px;">
	<div class="col-auto p-0">
		<a href="messages.php" class="btn btn-primary" aria-label="Cancel and return to messages list"><i
				class="fa fa-arrow-left" aria-hidden="true"></i> Cancel</a>
	</div>
	<div class="col-auto p-0">
		<a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="btn btn-primary" target="_blank"><i
				class="fa fa-reply"></i> Reply</a>
	</div>
	<div class="col-auto p-0">
		<a href="messages.php?id=<?= $row['id'] ?>" class="btn btn-danger"><i class="fa fa-trash"></i> Delete</a>
	</div>
</div>
<div class="card">
	<h6 class="professional-card-header">Message</h6>
	<div class="card-body">
		<div class="row mb-3">
			<div class="col-md-4 mb-2">
				<span class="text-muted"><i class="fa fa-user"></i> Sender:</span><br>
				<span class="fw-bold"><?= htmlspecialchars($row['name']) ?></span>
			</div>
			<div class="col-md-4 mb-2">
				<span class="text-muted"><i class="fa fa-envelope"></i> E-Mail Address:</span><br>
				<span class="fw-bold"><?= htmlspecialchars($row['email']) ?></span>
			</div>
			<div class="col-md-4 mb-2">
				<span class="text-muted"><i class="fa fa-calendar-alt"></i> Date:</span><br>
				<span class="fw-bold"><?= date($settings['date_format'], strtotime($row['date'])) ?>,
					<?= strtolower(date('h:i a', strtotime($row['date']))) ?></span>
			</div>
		</div>
		<div class="mb-2">
			<span class="text-muted"><i class="fa fa-file"></i> Message:</span>
			<div class="border rounded bg-light p-3 mt-1" style="min-height:60px;">
				<span class="fw-bold"><?= nl2br(htmlspecialchars($row['content'])) ?></span>
			</div>
		</div>
	</div>
</div>
<?= template_admin_footer(); ?>