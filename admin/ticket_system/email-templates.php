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
    <i class="bi bi-envelope-paper" aria-hidden="true"></i>
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
            <i class="bi bi-save me-1" aria-hidden="true"></i>
            Save Email Templates
        </button>
    </div>

    <?php if (isset($success_msg)): ?>
    <div class="mb-4">
        <div class="msg success">
            <i class="bi bi-check-circle-fill"></i>
            <p><?=$success_msg?></p>
            <i class="bi bi-x-lg"></i>
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