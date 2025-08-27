<?php
include '../assets/includes/main.php';
// Default input report values
$report = [
    'comment_id' => '',
    'acc_id' => '',
    'reason' => ''
];
// Get all accounts
$accounts = $pdo->query('SELECT id, username FROM accounts ORDER BY username')->fetchAll(PDO::FETCH_ASSOC);
// Get all comments
$comments = $pdo->query('SELECT id, content FROM comments ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
// If editing an report
if (isset($_GET['id'])) {
    // Get the report from the database
    $stmt = $pdo->prepare('SELECT * FROM comment_reports WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing report
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the report in the database
        $acc_id = !empty($_POST['acc_id']) ? $_POST['acc_id'] : null;
        $stmt = $pdo->prepare('UPDATE comment_reports SET comment_id = ?, acc_id = ?, reason = ? WHERE id = ?');
        $stmt->execute([ $_POST['comment_id'], $acc_id, $_POST['reason'], $_GET['id'] ]);
        header('Location: reports.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete the report
        header('Location: reports.php?delete=' . $_GET['id']);
        exit;
    }
    if (isset($_POST['delete_all'])) {
        // Redirect and delete the report and all its comments
        header('Location: reports.php?delete_all=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new report
    $page = 'Create';
    if (isset($_POST['submit'])) {
        // Insert the report into the database
        $acc_id = !empty($_POST['acc_id']) ? $_POST['acc_id'] : null;
        $stmt = $pdo->prepare('INSERT INTO comment_reports (comment_id, acc_id, reason) VALUES (?, ?, ?)');
        $stmt->execute([ $_POST['comment_id'], $acc_id, $_POST['reason'] ]);
        header('Location: reports.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Report', 'comments', 'reports')?>

<div class="content-title mb-4" id="main-report-create-edit" role="banner" aria-label="<?=$page?> Report Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11,15H13V17H11V15M11,7H13V13H11V7M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20Z" /></svg>
        </div>
        <div class="txt">
            <h2><?=$page?> Report</h2>
            <p><?=$page == 'Edit' ? 'Modify the report details below.' : 'Create a new report by filling out the form below.'?></p>
        </div>
    </div>
</div>

<?php if (isset($error_msg)): ?>
<div class="mb-4" role="region" aria-label="Error Message">
    <div class="msg error" role="alert" aria-live="polite">
        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        <p><?=$error_msg?></p>
        <button type="button" class="close-error" aria-label="Dismiss error message" onclick="this.parentElement.parentElement.style.display='none'">
            <i class="bi bi-x-circle-fill" aria-hidden="true"></i>
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="reports.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
        Cancel
    </a>
    <button type="submit" name="submit" form="main-form" class="btn btn-success">
        <i class="bi bi-save me-1" aria-hidden="true"></i>
        Save Report
    </button>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><?=$page?> Comment Report</h6>
    </div>
    <div class="card-body">
        <form method="post" enctype="multipart/form-data" id="main-form">
            <div class="row g-3">
                <div class="col-md-12">
                    <label for="comment_id" class="form-label">
                        <span class="text-danger">*</span> Comment to Report
                    </label>
                    <select id="comment_id" name="comment_id" class="form-select" required>
                        <option value="" disabled>Select a comment to report</option>
                        <?php foreach ($comments as $comment): ?>
                        <option value="<?= $comment['id'] ?>" <?= $comment['id'] == $report['comment_id'] ? ' selected' : '' ?>>
                            [<?= $comment['id'] ?>] <?= htmlspecialchars(mb_strimwidth(strip_tags($comment['content']), 0, 80, '...'), ENT_QUOTES) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Select the specific comment that is being reported.</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="acc_id" class="form-label">Reporter Account (Optional)</label>
                    <select id="acc_id" name="acc_id" class="form-select">
                        <option value="">Anonymous Report</option>
                        <?php foreach ($accounts as $account): ?>
                        <option value="<?= $account['id'] ?>" <?= $account['id'] == $report['acc_id'] ? ' selected' : '' ?>>
                            [<?= $account['id'] ?>] <?= htmlspecialchars($account['username'], ENT_QUOTES) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Account that reported this comment (can be left blank).</div>
                </div>
                <div class="col-md-6">
                    <label for="reason" class="form-label">
                        <span class="text-danger">*</span> Report Reason
                    </label>
                    <select id="reason_select" class="form-select mb-2" onchange="updateReasonText()">
                        <option value="">Select a reason template...</option>
                        <option value="spam">Spam or promotional content</option>
                        <option value="harassment">Harassment or bullying</option>
                        <option value="inappropriate">Inappropriate content</option>
                        <option value="offensive">Offensive language</option>
                        <option value="misinformation">Misinformation</option>
                        <option value="off-topic">Off-topic or irrelevant</option>
                        <option value="custom">Custom reason</option>
                    </select>
                    <textarea id="reason" name="reason" class="form-control" rows="4" 
                              placeholder="Describe why this comment is being reported..." required><?= htmlspecialchars($report['reason'], ENT_QUOTES) ?></textarea>
                    <div class="form-text">Explain why this comment violates community guidelines.</div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer bg-light">
        <div class="d-flex gap-2">
            <a href="reports.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
                Cancel
            </a>
            <button type="submit" name="submit" form="main-form" class="btn btn-success">
                <i class="bi bi-save me-1" aria-hidden="true"></i>
                Save Report
            </button>
            <?php if ($page == 'Edit'): ?>
            <button type="submit" name="delete" form="main-form" class="btn btn-danger"
                onclick="return confirm('Are you sure you want to delete this report?')"
                aria-label="Delete this report permanently">
                <i class="bi bi-trash me-1" aria-hidden="true"></i>
                Delete
            </button>
            <button type="submit" name="delete_all" form="main-form" class="btn btn-outline-danger"
                onclick="return confirm('Are you sure you want to delete this report AND the reported comment?')"
                aria-label="Delete this report and the reported comment">
                <i class="bi bi-trash me-1" aria-hidden="true"></i>
                Delete + Comment
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function updateReasonText() {
    const select = document.getElementById('reason_select');
    const textarea = document.getElementById('reason');
    const reasons = {
        'spam': 'This comment contains spam or promotional content that is not relevant to the discussion.',
        'harassment': 'This comment contains harassment, bullying, or personal attacks against other users.',
        'inappropriate': 'This comment contains inappropriate content that violates community guidelines.',
        'offensive': 'This comment contains offensive language or hate speech.',
        'misinformation': 'This comment contains false or misleading information.',
        'off-topic': 'This comment is off-topic and not relevant to the current discussion.'
    };
    
    if (select.value && select.value !== 'custom' && reasons[select.value]) {
        textarea.value = reasons[select.value];
    } else if (select.value === 'custom') {
        textarea.value = '';
        textarea.focus();
    }
}
</script>

<?=template_admin_footer()?>