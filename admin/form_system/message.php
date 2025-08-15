<?php
include 'main.php';
// Retrieve the message based on the ID
$stmt = $pdo->prepare('SELECT * FROM messages WHERE id = ?');
$stmt->execute([ $_GET['id'] ]);
$message = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$message) {
    exit('Invalid ID!');
}
if (isset($_GET['status'])) {
    // Mark as unread
    $stmt = $pdo->prepare('UPDATE messages SET status = ? WHERE id = ?');
    $stmt->execute([ $_GET['status'], $_GET['id'] ]);
    header('Location: messages.php?success_msg=2');
    exit;
}
// Mark as read
$stmt = $pdo->prepare('UPDATE messages SET status = "Read" WHERE id = ?');
$stmt->execute([ $_GET['id'] ]);
// Convert the JSON string to assoc array
$extra = json_decode($message['extra'], true);
?>
<?=template_admin_header(htmlspecialchars($message['subject'], ENT_QUOTES), 'messages')?>

<div class="content-title">
    <div class="title">
        <i class="fa-solid fa-envelope"></i>
        <div class="txt">
            <h2><?=htmlspecialchars($message['subject'], ENT_QUOTES)?></h2>
            <p>By <strong><?=$message['email']?></strong> on <?=date('F j, Y H:ia', strtotime($message['submit_date']))?></p>
        </div>
    </div>
</div>

<div class="content-block">
    <div class="message"><?=nl2br(htmlspecialchars($message['msg'], ENT_QUOTES))?></div>
    <div class="extras">
        <?php foreach($extra as $k => $v): ?>
        <div class="extra">
            <h3><?=htmlspecialchars(ucwords(str_replace('_', ' ', $k)), ENT_QUOTES)?></h3>
            <?php if (preg_match('[.jpg|.png|.webp|.gif|.bmp|.jpeg|.tif]', strtolower($v))): ?>
            <?php foreach(explode(',', rtrim($v, ',')) as $img): ?>
            <a href="../<?=htmlspecialchars($img, ENT_QUOTES)?>" download><img src="../<?=htmlspecialchars($img, ENT_QUOTES)?>" width="42" height="42"></a>
            <?php endforeach; ?>
            <?php else: ?>
            <p><?=htmlspecialchars($v, ENT_QUOTES)?></p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="btns pad-top-5">
    <a href="mailto:<?=$message['email']?>?subject=<?=urlencode($message['subject'])?>" class="btn mar-right-1">Reply</a>
    <a href="message.php?id=<?=$message['id']?>&status=Unread" class="btn mar-right-1">Mark as Unread</a>
    <a href="message.php?id=<?=$message['id']?>&status=Replied" class="btn mar-right-1">Mark as Replied</a>
    <a href="messages.php?delete=<?=$message['id']?>" class="btn alt">Delete</a>
</div>

<?=template_admin_footer()?>