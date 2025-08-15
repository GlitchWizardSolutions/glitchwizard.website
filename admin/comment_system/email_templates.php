<?php
include '../assets/includes/main.php';

// Define template editor if not already defined
if (!defined('template_editor')) {
    define('template_editor', 'summernote');
}

// Save the email templates
if (isset($_POST['notification_email_template'])) {
    if (file_put_contents('../notification-email-template.html', $_POST['notification_email_template']) === false) {
        header('Location: email_templates.php?error_msg=1');
        exit;
    }
}
if (isset($_POST['submit'])) {
    header('Location: email_templates.php?success_msg=1');
    exit;
}
// Read the notification email template HTML file
if (file_exists('../notification-email-template.html')) {
    $notification_email_template = file_get_contents('../notification-email-template.html');
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Email template updated successfully!';
    }
}
// Handle error messages
if (isset($_GET['error_msg'])) {
    if ($_GET['error_msg'] == 1) {
        $error_msg = 'There was an error updating the email template! Please set the correct permissions!';
    }
}
?>
<?=template_admin_header('Email Templates', 'comments', 'settings')?>

<form method="post" enctype="multipart/form-data">

    <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
        <h2>Email Templates</h2>
        <div class="btns">
            <input type="submit" name="submit" value="Save" class="btn btn-primary">
        </div>
    </div>

    <?php if (isset($success_msg)): ?>
    <div class="mar-top-4">
        <div class="msg success">
            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
            <p><?=$success_msg?></p>
            <svg class="close" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($error_msg)): ?>
    <div class="mar-top-4">
        <div class="msg error">
            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
            <p><?=$error_msg?></p>
            <svg class="close" width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg>
        </div>
    </div>
    <?php endif; ?>

    <div class="tabs">
        <?php if (isset($notification_email_template)): ?>
        <a href="#" class="active">Notification</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Email Templates</h5>
        </div>
        <div class="card-body">
        <div class="form responsive-width-100 size-full">
            <?php if (isset($notification_email_template)): ?>
            <div class="tab-content active">
                <?php if (template_editor == 'summernote'): ?>
                <div style="width:100%">
                    <textarea id="notification_email_template" name="notification_email_template" style="width:100%;height:600px;" wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><?=$notification_email_template?></textarea>
                </div>
                <?php else: ?>
                <textarea name="notification_email_template" id="notification_email_template" class="code-editor"><?=$notification_email_template?></textarea>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

</form>

<?php if (template_editor == 'summernote'): ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
$(document).ready(function() {
    $('#notification_email_template').summernote({
        height: 600,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });
});
</script>
<?php endif; ?>

<?=template_admin_footer()?>