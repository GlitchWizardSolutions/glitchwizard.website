<?php
include 'main.php';
// Get the current date
$today = date('Y-m-d H:i:s');
// MySQL query that retrieves all the polls and poll answers
$stmt = $pdo->prepare('SELECT p.*, (SELECT COUNT(*) FROM poll_votes pv WHERE pv.poll_id = p.id AND pv.ip_address = ?) AS has_voted, GROUP_CONCAT(pa.title ORDER BY pa.id) AS answers, GROUP_CONCAT(pa.votes ORDER BY pa.id) AS answers_votes, GROUP_CONCAT(pa.img ORDER BY pa.id) AS answers_imgs, (SELECT GROUP_CONCAT(c.title) FROM polls_categories c JOIN poll_categories pc ON pc.poll_id = p.id AND pc.category_id = c.id) AS categories FROM polls p LEFT JOIN poll_answers pa ON pa.poll_id = p.id WHERE p.start_date < ? GROUP BY p.id');
$stmt->execute([ get_ip_address(), $today ]);
$polls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?=template_header('Polls')?>

<div class="content home">

    <div class="page-title">
        <div class="icon">
            <svg width="30" height="30" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 17H7V10H9V17M13 17H11V7H13V17M17 17H15V13H17V17M19 19H5V5H19V19.1M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3Z" /></svg>
        </div>	
        <div class="wrap">
            <h2>Polls (<?=number_format(count($polls))?>)</h2>
            <p>All available polls are listed below.</p>
        </div>
		<div class="actions">
			<a href="index.php" class="selected" title="List View">
				<svg width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10,4V8H14V4H10M16,4V8H20V4H16M16,10V14H20V10H16M16,16V20H20V16H16M14,20V16H10V20H14M8,20V16H4V20H8M8,14V10H4V14H8M8,8V4H4V8H8M10,14H14V10H10V14M4,2H20A2,2 0 0,1 22,4V20A2,2 0 0,1 20,22H4C2.92,22 2,21.1 2,20V4A2,2 0 0,1 4,2Z" /></svg>
			</a>
			<a href="table.php" title="Table View">
				<svg width="26" height="26" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7,5H21V7H7V5M7,13V11H21V13H7M4,4.5A1.5,1.5 0 0,1 5.5,6A1.5,1.5 0 0,1 4,7.5A1.5,1.5 0 0,1 2.5,6A1.5,1.5 0 0,1 4,4.5M4,10.5A1.5,1.5 0 0,1 5.5,12A1.5,1.5 0 0,1 4,13.5A1.5,1.5 0 0,1 2.5,12A1.5,1.5 0 0,1 4,10.5M7,19V17H21V19H7M4,16.5A1.5,1.5 0 0,1 5.5,18A1.5,1.5 0 0,1 4,19.5A1.5,1.5 0 0,1 2.5,18A1.5,1.5 0 0,1 4,16.5Z" /></svg>			
			</a>			
		</div>
    </div>

	<div class="poll-list">
		<?php foreach($polls as $poll): ?>
		<?php
		$answers = explode(',', $poll['answers']);
		$answers_votes = explode(',', $poll['answers_votes']);
		$answers_imgs = explode(',', $poll['answers_imgs']);
		$categories = explode(',', $poll['categories']);
		$has_expired = $poll['end_date'] && strtotime($poll['end_date']) < strtotime($today);
		$has_voted = duplication_checking == 'ip' ? $poll['has_voted'] : isset($_COOKIE['poll' . $poll['id']]);
		?>
		<div class="wrapper">
			<?php if (array_filter($categories)): ?>
			<div class="poll-categories pad-bot-2">
				<?php foreach ($categories as $category): ?>
				<div class="poll-category"><?=$category?></div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
			<h3 class="poll-title"><?=htmlspecialchars($poll['title'], ENT_QUOTES)?></h3>
			<?php for($i = 0; $i < count($answers); $i++): ?>
			<?php if (hide_results_until_voting && !$has_voted) continue; ?>
			<div class="poll-question<?=$has_expired && (int)$answers_votes[$i] != max($answers_votes) ? ' expired' : ''?><?=$has_expired && (int)$answers_votes[$i] == max($answers_votes) ? ' winner' : ''?>">
				<?php if ($answers_imgs[$i]): ?>
                <div class="poll-img">
                    <img src="<?=$answers_imgs[$i]?>" alt="<?=htmlspecialchars($answers[$i], ENT_QUOTES)?>" width="100" height="100">
                </div>
                <?php endif; ?>
				<p class="poll-txt"><?=htmlspecialchars($answers[$i], ENT_QUOTES)?> <span><?=number_format((int)$answers_votes[$i])?> vote<?=(int)$answers_votes[$i] != 1 ? 's' : ''?></span></p>
				<div class="result-bar-container">
					<div class="result-bar<?=(int)$answers_votes[$i] ? '' : ' no-votes'?>" style="width:<?=(int)$answers_votes[$i] ? (((int)$answers_votes[$i]/array_sum($answers_votes))*100) : 0?>%">
						<?=(int)$answers_votes[$i] ? round(((int)$answers_votes[$i]/array_sum($answers_votes))*100) : 0?>%
					</div>
				</div>
			</div>
			<?php endfor; ?>
            <div class="btns pad-top-2">
                <span class="num-votes"><?=number_format(array_sum($answers_votes))?> vote<?=array_sum($answers_votes) != 1 ? 's' : ''?></span>
                <a href="vote.php?id=<?=$poll['id']?>" class="btn<?=$has_expired ? ' disabled' : ''?>">Vote Now</a>
            </div>
            <?php if ($poll['end_date'] && strtotime($poll['end_date']) < strtotime($today . ' +3 days') && strtotime($poll['end_date']) > strtotime($today)): ?>
            <div class="msg error pad-top-3">This poll ends in <?=date_diff(date_create($today), date_create($poll['end_date']))->format('%dd %hh %im')?>.</div>
            <?php elseif ($has_expired): ?>
            <div class="msg error pad-top-3">This poll has ended.</div>
            <?php endif; ?>
		</div>
		<?php endforeach; ?>
	</div>

</div>

<?=template_footer()?>