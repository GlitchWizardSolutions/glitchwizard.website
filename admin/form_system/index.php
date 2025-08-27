<?php
include 'main.php';
// Retrieve all messages
$stmt = $pdo->prepare('SELECT * FROM messages WHERE cast(submit_date as DATE) = cast(now() as DATE) ORDER BY submit_date DESC');
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Retrieve the average messages per day
$stmt = $pdo->prepare('SELECT COUNT(*) / DATEDIFF(NOW(), MIN(submit_date)) AS average FROM messages');
$stmt->execute();
$messages_average_per_day = $stmt->fetchColumn();
// Retrieve the total number of unique emails
$stmt = $pdo->prepare('SELECT COUNT(DISTINCT email) AS total FROM messages');
$stmt->execute();
$total_unique_emails = $stmt->fetchColumn();
// Get the total number of messages
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM messages');
$stmt->execute();
$messages_total = $stmt->fetchColumn();
// Get the total number of unread messages
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM messages WHERE status = "Unread"');
$stmt->execute();
$unread_messages_total = $stmt->fetchColumn();
?>
<?=template_admin_header('Form Messages', 'messages')?>

<div class="content-title">
    <div class="title">
        <i class="fa-solid fa-gauge-high"></i>
        <div class="txt">
            <h2>Dashboard</h2>
            <p>View statistics, new messages, and more.</p>
        </div>
    </div>
</div>

<div class="dashboard">
    <div class="content-block stat">
        <div class="data">
            <h3>Today's Messages</h3>
            <p><?=number_format(count($messages))?></p>
        </div>
    <i class="bi bi-envelope-fill"></i>
        <div class="footer">
            <i class="fa-solid fa-rotate fa-xs"></i>Total messages for today
        </div>
    </div>

    <div class="content-block stat">
        <div class="data">
            <h3>Total Unread Messages</h3>
            <p><?=number_format($unread_messages_total)?></p>
        </div>
    <i class="bi bi-envelope-fill"></i>
        <div class="footer">
            <i class="fa-solid fa-rotate fa-xs"></i>Total unread messages
        </div>
    </div>

    <div class="content-block stat">
        <div class="data">
            <h3>Total Messages</h3>
            <p><?=number_format($messages_total)?></p>
        </div>
    <i class="bi bi-inbox"></i>
        <div class="footer">
            <i class="fa-solid fa-rotate fa-xs"></i>Total messages
        </div>
    </div>

    <div class="content-block stat">
        <div class="data">
            <h3>Average Messages</h3>
            <p><?=number_format($messages_average_per_day, 2)?></p>
        </div>
    <i class="bi bi-clock-history"></i>
        <div class="footer">
            <i class="fa-solid fa-rotate fa-xs"></i>Avg messages per day
        </div>
    </div>

</div>

<div class="content-title">
    <div class="title">
        <i class="fa-solid fa-envelope alt"></i>
        <div class="txt">
            <h2>Today's Messages</h2>
            <p>Messages submitted in the last &lt;1 day.</p>
        </div>
    </div>
</div>

<div class="content-block">
    <div class="table">
        <table>
            <thead>
                <tr>
                    <td>From</td>
                    <td>Subject</td>
                    <td class="responsive-hidden">Message</td>
                    <td class="responsive-hidden">Status</td>
                    <td class="responsive-hidden">Date</td>
                    <td>Actions</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($messages)): ?>
                <tr>
                    <td colspan="10" class="no-results">There are no recent messages</td>
                </tr>
                <?php else: ?>
                <?php foreach ($messages as $message): ?>
                <tr>
                    <td><?=$message['email']?></td>
                    <td><?=mb_strimwidth(nl2br(htmlspecialchars($message['subject'], ENT_QUOTES)), 0, 100, '...')?></td>
                    <td class="responsive-hidden truncated-txt">
                        <div>
                            <span class="short"><?=htmlspecialchars(mb_strimwidth($message['msg'], 0, 50, "..."), ENT_QUOTES)?></span>
                            <span class="full"><?=nl2br(htmlspecialchars($message['msg'], ENT_QUOTES))?></span>
                            <?php if (strlen($message['msg']) > 50): ?>
                            <a href="#" class="read-more">Read More</a>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="responsive-hidden"><span class="<?=str_replace(['Unread','Read','Replied'], ['grey','orange','green'], $message['status'])?>"><?=$message['status']?></span></td>
                    <td class="responsive-hidden"><?=date('F j, Y H:ia', strtotime($message['submit_date']))?></td>
                    <td>
                        <a href="message.php?id=<?=$message['id']?>" class="link1">View</a>
                        <a href="messages.php?delete=<?=$message['id']?>" onclick="return confirm('Are you sure you want to delete this message?');" class="link1">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?=template_admin_footer()?>