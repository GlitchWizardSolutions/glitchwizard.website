<?php
include 'main.php';
// Output message
$msg = '';
// If the GET request "id" exists (poll id)...
if (isset($_GET['id'])) {
    // MySQL query that selects the poll records by the GET request "id"
    $stmt = $pdo->prepare('SELECT * FROM polls WHERE approved = 1 AND id = ?');
    $stmt->execute([ $_GET['id'] ]);
    // Fetch the record
    $poll = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the poll record exists with the id specified
    if ($poll) {
        // MySQL query that selects all the poll answers
        $stmt = $pdo->prepare('SELECT * FROM poll_answers WHERE poll_id = ?');
        $stmt->execute([ $_GET['id'] ]);
        $poll_answers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Fetch poll categories
        $stmt = $pdo->prepare('SELECT c.* FROM polls_categories c JOIN poll_categories pc ON pc.category_id = c.id AND pc.poll_id = ?');
        $stmt->execute([ $_GET['id'] ]);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Check if the user has already voted
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM poll_votes WHERE poll_id = ? AND ip_address = ?');
        $stmt->execute([ $_GET['id'], get_ip_address() ]);
        $voted = $stmt->fetchColumn();
        // If the user clicked the "Vote" button...
        if (isset($_POST['submit'])) {
            // Check if the user selected an answer
            if (!isset($_POST['poll_answer'])) {
                // User has not selected an answer
                $msg = 'You must select an option!';
            } else if ($poll['end_date'] && date('Y-m-d H:i:s') >= date('Y-m-d H:i:s', strtotime($poll['end_date']))) {
                // The poll expire datetime is less than the current server datetime
                $msg = 'This poll has expired! You can no longer vote!';
            } else if (isset($_COOKIE['poll' . $_GET['id']]) && one_vote_per_poll && duplication_checking == 'cookie') {
                // User has already voted...
                $msg = 'You have already voted!';
            } else if ($voted && one_vote_per_poll && duplication_checking == 'ip') {
                // User has already voted...
                $msg = 'You have already voted!';
            } else if (date('Y-m-d H:i:s') < $poll['start_date']) {
                // Poll has not yet started
                $msg = 'This poll has not yet started!';
            } else if (is_array($_POST['poll_answer']) && count($_POST['poll_answer']) > $poll['num_choices']) {
                // User has selected too many answers
                $msg = 'You can only select ' . $poll['num_choices'] . ' options!';
            } else {
                // Update and increase the vote for the answer the user voted for
                if (is_array($_POST['poll_answer'])) {
                    foreach($_POST['poll_answer'] as $poll_answer) {
                        $stmt = $pdo->prepare('UPDATE poll_answers SET votes = votes + 1 WHERE id = ?');
                        $stmt->execute([ $poll_answer ]);
                    }
                } else {
                    $stmt = $pdo->prepare('UPDATE poll_answers SET votes = votes + 1 WHERE id = ?');
                    $stmt->execute([ $_POST['poll_answer'] ]);
                }
                // If the user has not voted yet, insert the vote into the database or set a cookie
                if (one_vote_per_poll && duplication_checking == 'ip') {
                    // Insert the vote into the database
                    $stmt = $pdo->prepare('INSERT INTO poll_votes (poll_id, ip_address, created) VALUES (?, ?, ?)');
                    $stmt->execute([ $_GET['id'], get_ip_address(), date('Y-m-d H:i:s') ]);
                } else if (one_vote_per_poll && duplication_checking == 'cookie') {
                    // Set cookie to prevent user from voting multiple times on te same poll
                    setcookie('poll' . $_GET['id'], true, time() + (10 * 365 * 24 * 60 * 60));
                }
                // Redirect user to the result page
                header('Location: result.php?id=' . $_GET['id']);
                exit;
            }
        }
    } else {
        exit('Poll with that ID does not exist!');
    }
} else {
    exit('No poll ID specified!');
}
?>
<?=template_header(htmlspecialchars($poll['title'], ENT_QUOTES))?>

<div class="content poll-vote pad-top-3">

    <div class="block">

        <?php if ($categories): ?>
        <div class="poll-categories">
            <?php foreach ($categories as $category): ?>
            <div class="poll-category"><?=$category['title']?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <h1><?=htmlspecialchars($poll['title'], ENT_QUOTES)?></h1>

        <p class="desc"><?=nl2br(htmlspecialchars($poll['description'], ENT_QUOTES))?></p>

        <?php if ($poll['num_choices'] > 1): ?>
        <p class="desc">You can select up to <?=$poll['num_choices']?> options.</p>
        <?php endif; ?>

        <form action="" method="post">
            <?php for ($i = 0; $i < count($poll_answers); $i++): ?>
            <label>
                <input type="<?=$poll['num_choices'] > 1 ? 'checkbox' : 'radio'?>" name="poll_answer<?=$poll['num_choices'] > 1 ? '[]' : ''?>" value="<?=$poll_answers[$i]['id']?>">
                <?php if ($poll_answers[$i]['img']): ?>
                <div class="poll-img">
                    <img src="<?=$poll_answers[$i]['img']?>" alt="<?=htmlspecialchars($poll_answers[$i]['title'], ENT_QUOTES)?>" width="100" height="100">
                </div>
                <?php endif; ?>
                <?=htmlspecialchars($poll_answers[$i]['title'], ENT_QUOTES)?>
            </label>
            <?php endfor; ?>
            <div class="btns pad-top-3 pad-bot-2">
                <button type="submit" name="submit" class="btn">Vote</button>
                <a href="result.php?id=<?=$poll['id']?>" class="btn alt mar-left-1">View Result</a>
            </div>
        </form>

        <?php if ($msg): ?>
        <p class="msg error"><?=$msg?></p>
        <?php endif; ?>

    </div>

</div>

<?php if ($poll['num_choices'] > 1): ?>
<script>
document.querySelectorAll('[name="poll_answer[]"]').forEach(function(element) {
    element.onchange = function(event) {
        if (document.querySelectorAll('[name="poll_answer[]"]:checked').length+1 > <?=$poll['num_choices']?>) {
            document.querySelectorAll('[name="poll_answer[]"]:not(:checked)').forEach(function(element) {
                element.disabled = true;
            });
        } else {
            document.querySelectorAll('[name="poll_answer[]"]').forEach(function(element) {
                element.disabled = false;
            });
        }
    };
});
</script>
<?php endif; ?>

<?=template_footer()?>