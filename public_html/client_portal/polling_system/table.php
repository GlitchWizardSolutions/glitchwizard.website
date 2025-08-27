<?php
include 'main.php';
// Get the current date
$today = date('Y-m-d H:i:s');
// MySQL query that retrieves all the polls and poll answers
$stmt = $pdo->prepare('SELECT p.*, GROUP_CONCAT(pa.title ORDER BY pa.id) AS answers, GROUP_CONCAT(pa.votes ORDER BY pa.id) AS answers_votes, GROUP_CONCAT(pa.img ORDER BY pa.id) AS answers_imgs, (SELECT GROUP_CONCAT(c.title) FROM polls_categories c JOIN poll_categories pc ON pc.poll_id = p.id AND pc.category_id = c.id) AS categories FROM polls p LEFT JOIN poll_answers pa ON pa.poll_id = p.id WHERE p.start_date < ? GROUP BY p.id');
$stmt->execute([ $today ]);
$polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?=template_header('Polls - Table View')?>

<div class="content table">

    <div class="page-title">
        <div class="icon">
            <svg width="30" height="30" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 17H7V10H9V17M13 17H11V7H13V17M17 17H15V13H17V17M19 19H5V5H19V19.1M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3Z" /></svg>
        </div>	
        <div class="wrap">
            <h2>Polls (<?=number_format(count($polls))?>)</h2>
            <p>All available polls are listed below.</p>
        </div>
		<div class="actions">
			<a href="index.php" title="List View">
				<svg width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10,4V8H14V4H10M16,4V8H20V4H16M16,10V14H20V10H16M16,16V20H20V16H16M14,20V16H10V20H14M8,20V16H4V20H8M8,14V10H4V14H8M8,8V4H4V8H8M10,14H14V10H10V14M4,2H20A2,2 0 0,1 22,4V20A2,2 0 0,1 20,22H4C2.92,22 2,21.1 2,20V4A2,2 0 0,1 4,2Z" /></svg>
			</a>
			<a href="table.php" class="selected" title="Table View">
				<svg width="26" height="26" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7,5H21V7H7V5M7,13V11H21V13H7M4,4.5A1.5,1.5 0 0,1 5.5,6A1.5,1.5 0 0,1 4,7.5A1.5,1.5 0 0,1 2.5,6A1.5,1.5 0 0,1 4,4.5M4,10.5A1.5,1.5 0 0,1 5.5,12A1.5,1.5 0 0,1 4,13.5A1.5,1.5 0 0,1 2.5,12A1.5,1.5 0 0,1 4,10.5M7,19V17H21V19H7M4,16.5A1.5,1.5 0 0,1 5.5,18A1.5,1.5 0 0,1 4,19.5A1.5,1.5 0 0,1 2.5,18A1.5,1.5 0 0,1 4,16.5Z" /></svg>			
			</a>			
		</div>
    </div>

	<div class="poll-list-table block">
		<table>
			<thead>
				<tr>
					<th>Title</th>
					<th>Answer Options</th>
					<th>End Date</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($polls as $poll): ?>
				<?php
				$answers = explode(',', $poll['answers']);
				$answers_votes = explode(',', $poll['answers_votes']);
				$answers_imgs = explode(',', $poll['answers_imgs']);
				$categories = explode(',', $poll['categories']);
				$has_expired = $poll['end_date'] && strtotime($poll['end_date']) < strtotime($today);
				?>
				<tr>
					<td>
						<div class="title"><?=htmlspecialchars($poll['title'], ENT_QUOTES)?></div>
						<?php if (array_filter($categories)): ?>
						<div class="poll-categories pad-top-2">
							<?php foreach ($categories as $category): ?>
							<div class="poll-category"><?=$category?></div>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
					</td>
					<td>
						<div class="poll-categories">
							<?php for($i = 0; $i < count($answers); $i++): ?>
							<div class="poll-category alt"><?=$answers[$i]?></div>
							<?php endfor; ?>
						</div>
					</td>
					<td class="alt"><?=$poll['end_date'] ? date('jS F Y', strtotime($poll['end_date'])) : 'Never'?></td>
					<td class="actions">
						<?php if (edit_polls == 'everyone' || (edit_polls == 'user' && isset($_SESSION['account_loggedin'])) || (edit_polls == 'admin' && isset($_SESSION['account_loggedin']) && $_SESSION['account_role'] == 'Admin')): ?>
						<a href="update.php?id=<?=$poll['id']?>" class="update" title="Update Poll">
							<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" /></svg>
						</a>
						<?php endif; ?>
						<a href="result.php?id=<?=$poll['id']?>" class="view" title="View Poll">
							<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z" /></svg>
						</a>
						<a href="vote.php?id=<?=$poll['id']?>" class="vote" title="Vote Poll">
							<svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M18,13H17.32L15.32,15H17.23L19,17H5L6.78,15H8.83L6.83,13H6L3,16V20A2,2 0 0,0 5,22H19A2,2 0 0,0 21,20V16L18,13M17,7.95L12.05,12.9L8.5,9.36L13.46,4.41L17,7.95M12.76,2.29L6.39,8.66C6,9.05 6,9.68 6.39,10.07L11.34,15C11.73,15.41 12.36,15.41 12.75,15L19.11,8.66C19.5,8.27 19.5,7.64 19.11,7.25L14.16,2.3C13.78,1.9 13.15,1.9 12.76,2.29Z" /></svg>
						</a>						
					</td>
				</tr>
				<?php endforeach; ?>
				<?php if (empty($polls)): ?>
				<tr>
					<td colspan="10" class="no-results">There are no polls available.</td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

</div>

<?=template_footer()?>