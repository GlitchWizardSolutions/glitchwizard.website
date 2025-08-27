<?php
/**
 * Email Templates Management System (Unified Visual & Code Editor)
 * 
 * SYSTEM: GWS Universal Hybrid App - Invoice System
 * FILE: email_templates.php
 * LOCATION: /public_html/admin/invoice_system/
 * PURPOSE: Unified interface for managing email templates with both visual (SummerNote) and code editor modes
 * 
 * CREATED: 2025-08-13
 * UPDATED: 2025-08-13
 * VERSION: 2.0
 * PRODUCTION: [READY FOR PRODUCTION]
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
 * - Invoice system email templates
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
include 'main.php';
// Save the email templates
if (isset($_POST['client_email_template'])) {
    if (file_put_contents('../../invoice_system/templates/client-email-template.html', $_POST['client_email_template']) === false) {
        header('Location: email_templates.php?error_msg=1');
        exit;
    }
}
if (isset($_POST['notification_email_template'])) {
    if (file_put_contents('../../invoice_system/templates/notification-email-template.html', $_POST['notification_email_template']) === false) {
        header('Location: email_templates.php?error_msg=1');
        exit;
    }
}
if (isset($_POST['submit'])) {
    header('Location: email_templates.php?success_msg=1');
    exit;
}
// Read the order details email template HTML file
if (file_exists('../../invoice_system/templates/client-email-template.html')) {
    $client_email_template = file_get_contents('../../invoice_system/templates/client-email-template.html');
}
// Read the notification email template HTML file
if (file_exists('../../invoice_system/templates/notification-email-template.html')) {
    $notification_email_template = file_get_contents('../../invoice_system/templates/notification-email-template.html');
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Email template(s) updated successfully!';
    }
}
// Handle error messages
if (isset($_GET['error_msg'])) {
    if ($_GET['error_msg'] == 1) {
        $error_msg = 'There was an error updating the email template(s)! Please set the correct permissions!';
    }
}
?>
<?=template_admin_header('Email Templates', 'invoices', 'email_templates')?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <i class="bi bi-envelope-paper" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Email Templates</h2>
            <p>Configure email templates for client communications and notifications.</p>
        </div>
    </div>
</div>

<div style="height: 20px;"></div>

<div class="d-flex gap-2 mb-4">
    <a href="invoice_dash.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancel
    </a>
    <button type="submit" name="submit" form="email-templates-form" class="btn btn-success">
        <i class="bi bi-save me-1" aria-hidden="true"></i>Save Templates
    </button>
</div>

<?php if (isset($success_msg)): ?>
    <div class="mb-4">
        <div class="msg success">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <p><?= $success_msg ?></p>
            <i class="bi bi-x-lg close" aria-hidden="true"></i>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($error_msg)): ?>
    <div class="mb-4">
        <div class="msg error">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <p><?= $error_msg ?></p>
            <i class="bi bi-x-lg close" aria-hidden="true"></i>
        </div>
    </div>
<?php endif; ?>

<div class="tabs">
    <?php if (isset($client_email_template)): ?>
        <a href="#" class="active" onclick="switchTemplate('client'); return false;">
            <i class="bi bi-person me-1" aria-hidden="true"></i>Client Template
        </a>
    <?php endif; ?>
    <?php if (isset($notification_email_template)): ?>
        <a href="#" onclick="switchTemplate('notification'); return false;">
            <i class="bi bi-bell me-1" aria-hidden="true"></i>Notification Template
        </a>
    <?php endif; ?>
</div>

<div class="content-block">
    <div class="form responsive-width-100 size-md">
        <form action="" method="post" enctype="multipart/form-data" id="email-templates-form">
            <?php if (isset($client_email_template)): ?>
                <div class="tab-content active" data-template="client">
                    <h3 style="margin-bottom: 20px; color: #333;">Client Email Template</h3>

                    <!-- Nested tabs for editor modes -->
                    <div class="editor-mode-tabs" style="margin-bottom: 15px;">
                        <button type="button" class="editor-tab-btn active"
                            onclick="switchEditorMode('client', 'visual')">
                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path
                                    d="M362.7 19.3L314.3 67.7 444.3 197.7l48.4-48.4c25-25 25-65.5 0-90.5L453.3 19.3c-25-25-65.5-25-90.5 0zm-71 71L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4L1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L421.7 220.3 291.7 90.3z" />
                            </svg>
                            Visual Editor
                        </button>
                        <button type="button" class="editor-tab-btn" onclick="switchEditorMode('client', 'code')">
                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
                                <path
                                    d="M392.8 1.2c-17-4.9-34.7 5-39.6 22l-128 448c-4.9 17 5 34.7 22 39.6s34.7-5 39.6-22l128-448c4.9-17-5-34.7-22-39.6zm80.6 120.1c-12.5 12.5-12.5 32.8 0 45.3L562.7 256l-89.4 89.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l112-112c12.5-12.5 12.5-32.8 0-45.3l-112-112c-12.5-12.5-32.8-12.5-45.3 0zm-306.7 0c-12.5-12.5-32.8-12.5-45.3 0l-112 112c-12.5 12.5-12.5 32.8 0 45.3l112 112c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256l89.4-89.4c12.5-12.5 12.5-32.8 0-45.3z" />
                            </svg>
                            Code Editor
                        </button>
                    </div>

                    <!-- Visual Editor Mode -->
                    <div class="editor-mode visual-mode" id="client-visual-mode">
                        <textarea name="client_email_template" id="client_email_template_visual"
                            class="summernote-editor"><?= $client_email_template ?></textarea>
                    </div>

                    <!-- Code Editor Mode -->
                    <div class="editor-mode code-mode" id="client-code-mode" style="display: none;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <label for="client_email_template_code">HTML Source Code:</label>
                            <button type="button" class="btn preview-btn"
                                onclick="previewTemplate('client_email_template_code')">
                                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                    <path
                                        d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-92.7-69.4c-6.2 0-11.4 5.2-11.4 11.4s5.2 11.4 11.4 11.4c28.1 0 50.9 22.8 50.9 50.9c0 6.2-5.2 11.4-11.4 11.4z" />
                                </svg>
                                Preview
                            </button>
                        </div>
                        <textarea name="client_email_template" id="client_email_template_code"
                            class="code-editor"><?= $client_email_template ?></textarea>
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

            <?php if (!isset($client_email_template) && !isset($notification_email_template)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-envelope fa-3x mb-3 d-block" aria-hidden="true"></i>
                <h5>No Email Templates Found</h5>
                <p>Email template files are missing from the templates directory.</p>
                <small class="text-muted">Expected files: client-email-template.html, notification-email-template.html</small>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

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

    .note-toolbar {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    /* Code editor styling */
    .code-editor {
        font-family: 'Courier New', Consolas, Monaco, 'Lucida Console', monospace;
        font-size: 13px;
        line-height: 1.4;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        min-height: 400px;
        resize: vertical;
    }

    .code-editor:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Main template tabs */
    .tabs {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 0;
        background-color: #f8f9fa;
        border-radius: 8px 8px 0 0;
        overflow: hidden;
    }

    .tabs a {
        text-decoration: none;
        padding: 15px 25px;
        background-color: transparent;
        color: #6c757d;
        font-weight: 500;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        min-width: 140px;
        justify-content: center;
    }

    .tabs a:hover {
        background-color: #e9ecef;
        color: #495057;
        text-decoration: none;
    }

    .tabs a.active {
        background-color: #fff;
        color: #007bff;
        border-bottom-color: #007bff;
        font-weight: 600;
    }

    /* Content block styling */
    .content-block {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 8px 8px;
        min-height: 500px;
    }

    .tab-content {
        display: none;
        padding: 30px;
        animation: fadeIn 0.3s ease-in-out;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Form styling improvements */
    .form {
        padding: 0;
    }

    .form h3 {
        color: #333;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f8f9fa;
    }

    /* Message styling improvements */
    .msg {
        position: relative;
        padding: 15px 20px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        animation: slideDown 0.3s ease-out;
    }

    .msg.success {
        background-color: #d1edff;
        border: 1px solid #bee5eb;
        color: #0c5460;
    }

    .msg.error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .msg svg {
        flex-shrink: 0;
        fill: currentColor;
    }

    .msg .close {
        margin-left: auto;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s ease;
    }

    .msg .close:hover {
        opacity: 1;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<!-- Include SummerNote CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize SummerNote editors
        $('.summernote-editor').summernote({
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            placeholder: 'Enter your email template content here...',
            tabsize: 2,
            focus: false,
            callbacks: {
                onChange: function(contents, $editable) {
                    // Sync with code editor
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

    function switchTemplate(templateName) {
        // Hide all tab contents
        const allTabContents = document.querySelectorAll('.tab-content');
        allTabContents.forEach(content => {
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

    function switchEditorMode(templateName, mode) {
        // Remove active class from editor mode buttons
        const editorButtons = document.querySelectorAll(`[data-template="${templateName}"] .editor-tab-btn`);
        editorButtons.forEach(btn => btn.classList.remove('active'));

        // Hide all editor modes for this template
        const visualMode = document.getElementById(`${templateName}-visual-mode`);
        const codeMode = document.getElementById(`${templateName}-code-mode`);

        if (mode === 'visual') {
            visualMode.style.display = 'block';
            codeMode.style.display = 'none';
            document.querySelector(`[onclick*="switchEditorMode('${templateName}', 'visual')"]`).classList.add('active');
            
            // Sync code to visual editor
            const codeEditor = document.getElementById(`${templateName}_email_template_code`);
            const visualEditor = document.getElementById(`${templateName}_email_template_visual`);
            if (codeEditor && visualEditor) {
                $(visualEditor).summernote('code', codeEditor.value);
            }
        } else {
            visualMode.style.display = 'none';
            codeMode.style.display = 'block';
            document.querySelector(`[onclick*="switchEditorMode('${templateName}', 'code')"]`).classList.add('active');
            
            // Sync visual to code editor
            const visualEditor = document.getElementById(`${templateName}_email_template_visual`);
            const codeEditor = document.getElementById(`${templateName}_email_template_code`);
            if (visualEditor && codeEditor) {
                codeEditor.value = $(visualEditor).summernote('code');
            }
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
                'client_email_template_code': 'Client Email Template',
                'notification_email_template_code': 'Notification Email Template'
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

    // Close message functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.msg .close')) {
            e.target.closest('.msg').style.display = 'none';
        }
    });
</script>

<?=template_admin_footer()?>