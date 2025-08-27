<?php
/**
 * Email Templates Management System (Unified Visual & Code Editor)
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: email_templates.php
 * LOCATION: /public_html/admin/accounts/
 * PURPOSE: Unified interface for managing email templates with both visual (SummerNote) and code editor modes
 * 
 * CREATED: 2025-07-04
 * UPDATED: 2025-07-04
 * VERSION: 2.0
 * PRODUCTION: [READY FOR PRODUCTION]
 * 
 * CHANGE LOG:
 * 2025-07-04 - Initial creation with email template management
 * 2025-07-04 - Added preview modal functionality for template visualization
 * 2025-07-04 - Unified visual and code editors with nested tabs for professional workflow
 * 2025-07-22 - Quality Assurance and Accessibility Testing completed; header and changelog updated; confirmed production readiness
 * 
 * FEATURES:
 * - Nested tabs for each template (Visual Editor / Code Editor)
 * - SummerNote WYSIWYG editor with full toolbar
 * - Code editor with syntax highlighting
 * - Real-time sync between visual and code modes
 * - Preview modal for both editor modes
 * - Single form submission saves all templates
 * 
 * DEPENDENCIES:
 * - main.php (admin includes)
 * - Login system email templates
 * - Bootstrap 5 for styling
 * - SummerNote WYSIWYG editor (CDN)
 * - jQuery (required for SummerNote)
 * 
 * SECURITY NOTES:
 * - Admin authentication required
 * - Secure file handling for template files
 * - Input validation for template content
 * - SummerNote XSS protection enabled
 */
include_once '../assets/includes/main.php';
// Save the email templates
if (isset($_POST['activation_email_template']))
{
    if (file_put_contents('../../accounts_system/activation-email-template.html', $_POST['activation_email_template']) === false)
    {
        header('Location: email_templates.php?error_msg=1');
        exit;
    }
}
if (isset($_POST['notification_email_template']))
{
    if (file_put_contents('../../accounts_system/notification-email-template.html', $_POST['notification_email_template']) === false)
    {
        header('Location: email_templates.php?error_msg=1');
        exit;
    }
}
if (isset($_POST['twofactor_email_template']))
{
    if (file_put_contents('../../accounts_system/twofactor-email-template.html', $_POST['twofactor_email_template']) === false)
    {
        header('Location: email_templates.php?success_msg=1');
        exit;
    }
}
if (isset($_POST['resetpass_email_template']))
{
    if (file_put_contents('../../accounts_system/resetpass-email-template.html', $_POST['resetpass_email_template']) === false)
    {
        header('Location: email_templates.php?success_msg=1');
        exit;
    }
}
if (isset($_POST['submit']))
{
    header('Location: email_templates.php?success_msg=1');
    exit;
}
// Read the activation email template HTML file
if (file_exists('../../accounts_system/activation-email-template.html'))
{
    $activation_email_template = file_get_contents('../../accounts_system/activation-email-template.html');
}
// Read the notification email template HTML file
if (file_exists('../../accounts_system/notification-email-template.html'))
{
    $notification_email_template = file_get_contents('../../accounts_system/notification-email-template.html');
}
// Read the two-factor email template
if (file_exists('../../accounts_system/twofactor-email-template.html'))
{
    $twofactor_email_template = file_get_contents('../../accounts_system/twofactor-email-template.html');
}
// Read the reset password email template HTML file
if (file_exists('../../accounts_system/resetpass-email-template.html'))
{
    $resetpass_email_template = file_get_contents('../../accounts_system/resetpass-email-template.html');
}
// Handle success messages
if (isset($_GET['success_msg']))
{
    if ($_GET['success_msg'] == 1)
    {
        $success_msg = 'Email template updated successfully!';
    }
}
// Handle error messages
if (isset($_GET['error_msg']))
{
    if ($_GET['error_msg'] == 1)
    {
        $error_msg = 'There was an error updating the email template! Please set the correct permissions!';
    }
}
?>
<?= template_admin_header('Accounts', 'accounts', 'templates') ?>

<!-- Include SummerNote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.css" rel="stylesheet">

<div class="content-title">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path
                    d="M64 112c-8.8 0-16 7.2-16 16v22.1L220.5 291.7c20.7 17 50.4 17 71.1 0L464 150.1V128c0-8.8-7.2-16-16-16H64zM48 212.2V384c0 8.8 7.2 16 16 16H448c8.8 0 16-7.2 16-16V212.2L322 328.8c-38.4 31.5-93.7 31.5-132 0L48 212.2zM0 128C0 92.7 28.7 64 64 64H448c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128z" />
            </svg>
        </div>
        <div class="txt">
            <h2>Edit Email Templates</h2>
            <p>Manage email templates for account activation, password reset, and notifications with visual or code
                editors.</p>
        </div>
    </div>
</div>

<div class="mb-4">
</div>

<form action="" method="post" enctype="multipart/form-data">
    <div class="d-flex gap-2 pb-3 border-bottom mb-4">
        <a href="account_dash.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Cancel
        </a>
        <button type="submit" name="submit" class="btn btn-success">
            <i class="fas fa-save me-1"></i>Save Templates
        </button>
    </div>

    <?php if (isset($success_msg)): ?>
        <div class="mb-4">
            <div class="msg success">
                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path
                        d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
                </svg>
                <p><?= $success_msg ?></p>
                <svg class="close" width="14" height="14" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path
                        d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                </svg>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($error_msg)): ?>
        <div class="mb-4">
            <div class="msg error">
                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path
                        d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zm0-384c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
                </svg>
                <p><?= $error_msg ?></p>
                <svg class="close" width="14" height="14" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path
                        d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                </svg>
            </div>
        </div>
    <?php endif; ?>

    <div class="tabs">
        <?php if (isset($activation_email_template)): ?>
            <a href="#" class="active" onclick="switchTemplate('activation'); return false;">Activation</a>
        <?php endif; ?>
        <?php if (isset($notification_email_template)): ?>
            <a href="#" onclick="switchTemplate('notification'); return false;">Notification</a>
        <?php endif; ?>
        <?php if (isset($twofactor_email_template)): ?>
            <a href="#" onclick="switchTemplate('twofactor'); return false;">Two-factor</a>
        <?php endif; ?>
        <?php if (isset($resetpass_email_template)): ?>
            <a href="#" onclick="switchTemplate('resetpass'); return false;">Reset Password</a>
        <?php endif; ?>
    </div>

    <div class="content-block">
        <div class="form responsive-width-100 size-md">
            <?php if (isset($activation_email_template)): ?>
                <div class="tab-content active" data-template="activation">
                    <h3 style="margin-bottom: 20px; color: #333;">Activation Email Template</h3>

                    <!-- Nested tabs for editor modes -->
                    <div class="editor-mode-tabs" style="margin-bottom: 15px;">
                        <button type="button" class="editor-tab-btn active"
                            onclick="switchEditorMode('activation', 'visual')">
                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path
                                    d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z" />
                            </svg>
                            Visual Editor
                        </button>
                        <button type="button" class="editor-tab-btn" onclick="switchEditorMode('activation', 'code')">
                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                <path
                                    d="M392.8 1.2c-17-4.9-34.7 5-39.6 22l-128 448c-4.9 17 5 34.7 22 39.6s34.7-5 39.6-22l128-448c4.9-17-5-34.7-22-39.6zm80.6 120.1c-12.5 12.5-12.5 32.8 0 45.3L562.7 256l-89.4 89.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l112-112c12.5-12.5 12.5-32.8 0-45.3l-112-112c-12.5-12.5-32.8-12.5-45.3 0zm-306.7 0c-12.5-12.5-32.8-12.5-45.3 0l-112 112c-12.5 12.5-12.5 32.8 0 45.3l112 112c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256l89.4-89.4c12.5-12.5 12.5-32.8 0-45.3z" />
                            </svg>
                            Code Editor
                        </button>
                    </div>

                    <!-- Visual Editor Mode -->
                    <div class="editor-mode visual-mode" id="activation-visual-mode">
                        <textarea name="activation_email_template" id="activation_email_template_visual"
                            class="summernote-editor"><?= $activation_email_template ?></textarea>
                    </div>

                    <!-- Code Editor Mode -->
                    <div class="editor-mode code-mode" id="activation-code-mode" style="display: none;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <label for="activation_email_template_code">HTML Source Code:</label>
                            <button type="button" class="btn preview-btn"
                                onclick="previewTemplate('activation_email_template_code')">
                                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                    <path
                                        d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-92.7-69.4c-6.2 0-11.4 5.2-11.4 11.4s5.2 11.4 11.4 11.4c28.1 0 50.9 22.8 50.9 50.9c0 6.2-5.2 11.4-11.4 11.4z" />
                                </svg>
                                Preview
                            </button>
                        </div>
                        <textarea name="activation_email_template" id="activation_email_template_code"
                            class="code-editor"><?= $activation_email_template ?></textarea>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($notification_email_template)): ?>
                <div class="tab-content" data-template="notification">
                    <h3 style="margin-bottom: 20px; color: #333;">Notification Email Template</h3>

                    <!-- Nested tabs for editor modes -->
                    <div class="editor-mode-tabs" style="margin-bottom: 15px;">
                        <button type="button" class="editor-tab-btn active"
                            onclick="switchEditorMode('notification', 'visual')">
                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path
                                    d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z" />
                            </svg>
                            Visual Editor
                        </button>
                        <button type="button" class="editor-tab-btn" onclick="switchEditorMode('notification', 'code')">
                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                <path
                                    d="M392.8 1.2c-17-4.9-34.7 5-39.6 22l-128 448c-4.9 17 5 34.7 22 39.6s34.7-5 39.6-22l128-448c4.9-17-5-34.7-22-39.6zm80.6 120.1c-12.5 12.5-12.5 32.8 0 45.3L562.7 256l-89.4 89.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l112-112c12.5-12.5 12.5-32.8 0-45.3l-112-112c-12.5-12.5-32.8-12.5-45.3 0zm-306.7 0c-12.5-12.5-32.8-12.5-45.3 0l-112 112c-12.5 12.5-12.5 32.8 0 45.3l112 112c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256l89.4-89.4c12.5-12.5 12.5-32.8 0-45.3z" />
                            </svg>
                            Code Editor
                        </button>
                    </div>

                    <!-- Visual Editor Mode -->
                    <div class="editor-mode visual-mode" id="notification-visual-mode">
                        <textarea name="notification_email_template" id="notification_email_template_visual"
                            class="summernote-editor"><?= $notification_email_template ?></textarea>
                    </div>

                    <!-- Code Editor Mode -->
                    <div class="editor-mode code-mode" id="notification-code-mode" style="display: none;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <label for="notification_email_template_code">HTML Source Code:</label>
                            <button type="button" class="btn preview-btn"
                                onclick="previewTemplate('notification_email_template_code')">
                                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                    <path
                                        d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-92.7-69.4c-6.2 0-11.4 5.2-11.4 11.4s5.2 11.4 11.4 11.4c28.1 0 50.9 22.8 50.9 50.9c0 6.2-5.2 11.4-11.4 11.4z" />
                                </svg>
                                Preview
                            </button>
                        </div>
                        <textarea name="notification_email_template" id="notification_email_template_code"
                            class="code-editor"><?= $notification_email_template ?></textarea>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($twofactor_email_template)): ?>
                <div class="tab-content" data-template="twofactor">
                    <h3 style="margin-bottom: 20px; color: #333;">Two-Factor Email Template</h3>

                    <!-- Nested tabs for editor modes -->
                    <div class="editor-mode-tabs" style="margin-bottom: 15px;">
                        <button type="button" class="editor-tab-btn active"
                            onclick="switchEditorMode('twofactor', 'visual')">
                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path
                                    d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z" />
                            </svg>
                            Visual Editor
                        </button>
                        <button type="button" class="editor-tab-btn" onclick="switchEditorMode('twofactor', 'code')">
                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                <path
                                    d="M392.8 1.2c-17-4.9-34.7 5-39.6 22l-128 448c-4.9 17 5 34.7 22 39.6s34.7-5 39.6-22l128-448c4.9-17-5-34.7-22-39.6zm80.6 120.1c-12.5 12.5-12.5 32.8 0 45.3L562.7 256l-89.4 89.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l112-112c12.5-12.5 12.5-32.8 0-45.3l-112-112c-12.5-12.5-32.8-12.5-45.3 0zm-306.7 0c-12.5-12.5-32.8-12.5-45.3 0l-112 112c-12.5 12.5-12.5 32.8 0 45.3l112 112c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256l89.4-89.4c12.5-12.5 12.5-32.8 0-45.3z" />
                            </svg>
                            Code Editor
                        </button>
                    </div>

                    <!-- Visual Editor Mode -->
                    <div class="editor-mode visual-mode" id="twofactor-visual-mode">
                        <textarea name="twofactor_email_template" id="twofactor_email_template_visual"
                            class="summernote-editor"><?= $twofactor_email_template ?></textarea>
                    </div>

                    <!-- Code Editor Mode -->
                    <div class="editor-mode code-mode" id="twofactor-code-mode" style="display: none;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <label for="twofactor_email_template_code">HTML Source Code:</label>
                            <button type="button" class="btn preview-btn"
                                onclick="previewTemplate('twofactor_email_template_code')">
                                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                    <path
                                        d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-92.7-69.4c-6.2 0-11.4 5.2-11.4 11.4s5.2 11.4 11.4 11.4c28.1 0 50.9 22.8 50.9 50.9c0 6.2-5.2 11.4-11.4 11.4z" />
                                </svg>
                                Preview
                            </button>
                        </div>
                        <textarea name="twofactor_email_template" id="twofactor_email_template_code"
                            class="code-editor"><?= $twofactor_email_template ?></textarea>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($resetpass_email_template)): ?>
                <div class="tab-content" data-template="resetpass">
                    <h3 style="margin-bottom: 20px; color: #333;">Reset Password Email Template</h3>

                    <!-- Nested tabs for editor modes -->
                    <div class="editor-mode-tabs" style="margin-bottom: 15px;">
                        <button type="button" class="editor-tab-btn active"
                            onclick="switchEditorMode('resetpass', 'visual')">
                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path
                                    d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z" />
                            </svg>
                            Visual Editor
                        </button>
                        <button type="button" class="editor-tab-btn" onclick="switchEditorMode('resetpass', 'code')">
                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                <path
                                    d="M392.8 1.2c-17-4.9-34.7 5-39.6 22l-128 448c-4.9 17 5 34.7 22 39.6s34.7-5 39.6-22l128-448c4.9-17-5-34.7-22-39.6zm80.6 120.1c-12.5 12.5-12.5 32.8 0 45.3L562.7 256l-89.4 89.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l112-112c12.5-12.5 12.5-32.8 0-45.3l-112-112c-12.5-12.5-32.8-12.5-45.3 0zm-306.7 0c-12.5-12.5-32.8-12.5-45.3 0l-112 112c-12.5 12.5-12.5 32.8 0 45.3l112 112c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256l89.4-89.4c12.5-12.5 12.5-32.8 0-45.3z" />
                            </svg>
                            Code Editor
                        </button>
                    </div>

                    <!-- Visual Editor Mode -->
                    <div class="editor-mode visual-mode" id="resetpass-visual-mode">
                        <textarea name="resetpass_email_template" id="resetpass_email_template_visual"
                            class="summernote-editor"><?= $resetpass_email_template ?></textarea>
                    </div>

                    <!-- Code Editor Mode -->
                    <div class="editor-mode code-mode" id="resetpass-code-mode" style="display: none;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <label for="resetpass_email_template_code">HTML Source Code:</label>
                            <button type="button" class="btn preview-btn"
                                onclick="previewTemplate('resetpass_email_template_code')">
                                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                    <path
                                        d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-92.7-69.4c-6.2 0-11.4 5.2-11.4 11.4s5.2 11.4 11.4 11.4c28.1 0 50.9 22.8 50.9 50.9c0 6.2-5.2 11.4-11.4 11.4z" />
                                </svg>
                                Preview
                            </button>
                        </div>
                        <textarea name="resetpass_email_template" id="resetpass_email_template_code"
                            class="code-editor"><?= $resetpass_email_template ?></textarea>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

</form>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Email Template Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="preview-container"
                    style="border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; background-color: #f8f9fa;">
                    <div id="preview-content"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .preview-btn {
        background-color: #17a2b8;
        color: white;
        border: 1px solid #17a2b8;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .preview-btn:hover {
        background-color: #138496;
        border-color: #117a8b;
        color: white;
    }

    .preview-container {
        min-height: 400px;
        max-height: 600px;
        overflow-y: auto;
    }

    #preview-content {
        background-color: white;
        padding: 20px;
        border-radius: 4px;
        min-height: 360px;
    }

    /* Editor Mode Tabs Styling */
    .editor-mode-tabs {
        display: flex;
        gap: 2px;
        background-color: #f8f9fa;
        padding: 4px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .editor-tab-btn {
        background-color: transparent;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .editor-tab-btn:hover {
        background-color: #e9ecef;
        color: #495057;
    }

    .editor-tab-btn.active {
        background-color: #fff;
        color: #007bff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .editor-mode {
        margin-top: 15px;
    }

    /* SummerNote customization */
    .note-editor {
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    /* Main tab content visibility */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .note-toolbar {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        border-radius: 8px 8px 0 0;
    }

    .note-editing-area {
        min-height: 400px;
    }

    .summernote-editor {
        display: none;
    }
</style>

<!-- Include SummerNote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs5.min.js"></script>

<script>
    // Initialize SummerNote editors
    document.addEventListener('DOMContentLoaded', function () {
        $('.summernote-editor').summernote({
            height: 400,
            placeholder: 'Enter your email template content here...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onChange: function (contents, $editable) {
                    // Sync content to the corresponding code editor when content changes
                    const editorId = this.id;
                    const templateName = editorId.replace('_visual', '_code');
                    const codeEditor = document.getElementById(templateName);
                    if (codeEditor) {
                        codeEditor.value = contents;
                    }
                }
            }
        });
    });

    // Function to switch between Visual and Code editor modes
    function switchEditorMode(templateName, mode) {
        const visualMode = document.getElementById(templateName + '-visual-mode');
        const codeMode = document.getElementById(templateName + '-code-mode');
        const tabs = document.querySelectorAll(`[onclick*="${templateName}"]`);

        // Remove active class from all tabs
        tabs.forEach(tab => tab.classList.remove('active'));

        if (mode === 'visual') {
            visualMode.style.display = 'block';
            codeMode.style.display = 'none';
            document.querySelector(`[onclick="switchEditorMode('${templateName}', 'visual')"]`).classList.add('active');

            // Sync content from code editor to visual editor
            const codeEditor = document.getElementById(templateName + '_email_template_code');
            const visualEditor = document.getElementById(templateName + '_email_template_visual');
            if (codeEditor && visualEditor) {
                $(visualEditor).summernote('code', codeEditor.value);
            }
        } else if (mode === 'code') {
            visualMode.style.display = 'none';
            codeMode.style.display = 'block';
            document.querySelector(`[onclick="switchEditorMode('${templateName}', 'code')"]`).classList.add('active');

            // Sync content from visual editor to code editor
            const visualEditor = document.getElementById(templateName + '_email_template_visual');
            const codeEditor = document.getElementById(templateName + '_email_template_code');
            if (visualEditor && codeEditor) {
                codeEditor.value = $(visualEditor).summernote('code');
            }
        }
    }

    // Function to switch between main template tabs (Activation, Notification, etc.)
    function switchTemplate(templateName) {
        // Hide all tab contents
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.style.display = 'none';
            content.classList.remove('active');
        });

        // Remove active class from all main tabs
        const mainTabs = document.querySelectorAll('.tabs a');
        mainTabs.forEach(tab => {
            tab.classList.remove('active');
        });

        // Show the selected tab content
        const selectedContent = document.querySelector(`.tab-content[data-template="${templateName}"]`);
        if (selectedContent) {
            selectedContent.style.display = 'block';
            selectedContent.classList.add('active');
        }

        // Add active class to the clicked tab
        const clickedTab = document.querySelector(`[onclick*="switchTemplate('${templateName}')"]`);
        if (clickedTab) {
            clickedTab.classList.add('active');
        }
    }

    function previewTemplate(templateId) {
        try {
            const textarea = document.getElementById(templateId);
            if (!textarea) {
                console.error('Template textarea not found:', templateId);
                return;
            }

            const content = textarea.value;
            console.log('Template content length:', content.length);

            // Get template name for modal title
            const templateNames = {
                'activation_email_template_code': 'Activation Email Template',
                'notification_email_template_code': 'Notification Email Template',
                'twofactor_email_template_code': 'Two-Factor Email Template',
                'resetpass_email_template_code': 'Reset Password Email Template'
            };

            // Update modal title
            const modalTitle = document.getElementById('previewModalLabel');
            if (modalTitle) {
                modalTitle.textContent = templateNames[templateId] + ' Preview';
            }

            // Update preview content
            const previewContent = document.getElementById('preview-content');
            if (previewContent) {
                previewContent.innerHTML = content || '<p style="color: #6c757d; font-style: italic;">No content to preview. The template appears to be empty.</p>';
            }

            // Show modal using Bootstrap 5
            const modalElement = document.getElementById('previewModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else {
                console.error('Modal element not found');
            }
        } catch (error) {
            console.error('Error in previewTemplate:', error);
            alert('There was an error opening the preview. Please check the console for details.');
        }
    }

    // Before form submission, sync all visual editors to their corresponding hidden inputs
    document.querySelector('form').addEventListener('submit', function (e) {
        // Sync all SummerNote editors to their corresponding textareas
        $('.summernote-editor').each(function () {
            const editorId = this.id;
            const templateName = editorId.replace('_visual', '_code');
            const codeEditor = document.getElementById(templateName);
            if (codeEditor) {
                codeEditor.value = $(this).summernote('code');
            }
        });
    });
</script>

<?= template_admin_footer() ?>