<?php
include 'main.php';
// Save the email templates
if (isset($_POST['ticket_email_template'])) {
    file_put_contents('../ticket-email-template.php', $_POST['ticket_email_template']);
    header('Location: email-templates.php?success_msg=1');
    exit;
}
// Read the ticket email template HTML file
if (file_exists('../ticket-email-template.php')) {
    $ticket_email_template = file_get_contents('../ticket-email-template.php');
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Email template updated successfully!';
    }
}
?>
<?=template_admin_header('Email Templates', 'tickets')?>

<div class="content-title" id="main-email-templates" role="banner" aria-label="Email Templates Header">
    <div class="icon">
        <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
    </div>
    <div class="txt">
        <h2>Email Templates</h2>
        <p>Manage ticket system email templates and notifications.</p>
    </div>
</div>

<div class="mb-4">
</div>

<form action="" method="post" enctype="multipart/form-data">

    <!-- Top form actions -->
    <div class="d-flex gap-2 pb-3 border-bottom mb-4" role="region" aria-label="Form Actions">
        <button type="submit" name="submit" class="btn btn-success">
            <i class="fas fa-save me-1" aria-hidden="true"></i>
            Save Email Templates
        </button>
    </div>

    <?php if (isset($success_msg)): ?>
    <div class="mb-4">
        <div class="msg success">
            <i class="fas fa-check-circle"></i>
            <p><?=$success_msg?></p>
            <i class="fas fa-times"></i>
        </div>
    </div>
    <?php endif; ?>

    <div class="content-block">

        <div class="form responsive-width-100">

            <?php if (isset($ticket_email_template)): ?>
            <label for="ticket_email_template">Ticket Email Template</label>
            <textarea id="ticket_email_template" name="ticket_email_template"><?=$ticket_email_template?></textarea>
            <?php endif; ?>

        </div>

    </div>

</form>

<?=template_admin_footer()?>