<?php
include 'main.php';
// If the GET request "id" exists (poll id)...
if (isset($_GET['id'])) {
    // MySQL query that selects the poll records by the GET request "id"
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE approved = 1 AND id = ?');
    $stmt->execute([$_GET['id']]);
    // Fetch the record
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the poll record exists with the id specified
    if ($poll) {
        // MySQL Query that will get all the answers from the "poll_answers" table
        $stmt = $pdo->prepare('SELECT * FROM poll_answers WHERE poll_id = ? ORDER BY votes DESC');
        $stmt->execute([ $_GET['id'] ]);
        $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Fetch poll categories
        $stmt = $pdo->prepare('SELECT c.* FROM polls_categories c JOIN poll_categories pc ON pc.category_id = c.id AND pc.poll_id = ?');
        $stmt->execute([ $_GET['id'] ]);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Total number of votes, will be used to calculate the percentage
        $total_votes = 0;
        foreach ($answers as &$poll_answer) {
            // Every poll answers votes will be added to total votes
            $total_votes += $poll_answer['votes'];
            // Check if answer has the highest number of votes
            $poll_answer['winner'] = $poll_answer['votes'] == max(array_column($answers, 'votes'));
        }
        // Check if poll has expired
        $has_expired = $poll['end_date'] && date('Y-m-d H:i:s') >= $poll['end_date'];
        // Check if user voted
        if (hide_results_until_voting) {
            if (duplication_checking == 'ip') {
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM poll_votes WHERE poll_id = ? AND ip_address = ?');
                $stmt->execute([ $_GET['id'], get_ip_address() ]);
                $has_voted = $stmt->fetchColumn();
            } else {
                $has_voted = isset($_COOKIE['poll' . $_GET['id']]);
            }
            if (!$has_voted) {
                exit('You must vote in order to see the results!');
            }
        }
    } else {
        exit('Poll with that ID does not exist!');
    }
} else {
    exit('No poll ID specified!');
}
?>
<?=template_header('Results: ' . htmlspecialchars($poll['title'], ENT_QUOTES))?>

<div class="content poll-result">

    <div class="poll-categories pad-top-5">
        <?php foreach ($categories as $category): ?>
        <div class="poll-category"><?=$category['title']?></div>
        <?php endforeach; ?>
    </div>

	<h1><?=htmlspecialchars($poll['title'], ENT_QUOTES)?></h1>

    <p class="desc"><?=nl2br(htmlspecialchars($poll['description'], ENT_QUOTES))?></p>

    <div class="wrapper">
        <?php foreach ($answers as $answer): ?>
        <div class="poll-question<?=$has_expired && !$answer['winner'] ? ' expired' : ''?><?=$has_expired && $answer['winner'] ? ' winner' : ''?>">
            <?php if ($answer['img']): ?>
            <div class="poll-img">
                <img src="<?=$answer['img']?>" alt="<?=htmlspecialchars($answer['title'], ENT_QUOTES)?>" width="100" height="100">
            </div>
            <?php endif; ?>
            <p class="poll-txt"><?=htmlspecialchars($answer['title'], ENT_QUOTES)?> <span><?=number_format((int)$answer['votes'])?> vote<?=(int)$answer['votes'] != 1 ? 's' : ''?></span></p>
            <div class="result-bar-container">
                <div class="result-bar<?=(int)$answer['votes'] ? '' : ' no-votes'?>" style="width:<?=(int)$answer['votes'] ? (((int)$answer['votes']/$total_votes)*100) : 0?>%">
                    <?=(int)$answer['votes'] ? round(((int)$answer['votes']/$total_votes)*100) : 0?>%
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<?=template_footer()?>