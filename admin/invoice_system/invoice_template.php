<?php
/*
 * PDF TEMPLATE INSTRUCTIONS:
 * 
 * The PDF template uses the same structure as the HTML template but with specific considerations:
 * 
 * 1. SUPPORTED STYLING:
 *    - Basic CSS properties (margin, padding, font-size, color, background-color)
 *    - Table styling (borders, cell padding, text-align)
 *    - Font families (web-safe fonts work best)
 *    - Page breaks using: <div style="page-break-before: always;"></div>
 * 
 * 2. UNSUPPORTED FEATURES:
 *    - JavaScript
 *    - External CSS files
 *    - Complex positioning (absolute, fixed)
 *    - Advanced CSS3 features
 * 
 * 3. RECOMMENDED APPROACH:
 *    - Use table-based layouts for best compatibility
 *    - Inline CSS styles rather than classes
 *    - Test PDF generation after each change
 *    - Keep styling simple and clean
 * 
 * 4. PLACEHOLDERS AVAILABLE:
 *    - %invoice_number% - Invoice number
 *    - %client_name% - Client name
 *    - %due_date% - Due date
 *    - %payment_amount% - Total amount
 *    - %created% - Creation date
 *    - %notes% - Invoice notes
 */
include 'main.php';
// Default template values
$template = [
    'name' => '',
    'html' => '',
    'pdf' => '',
    'preview' => ''
];

// Default PDF template with examples
$default_pdf_template = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice %invoice_number%</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            font-size: 12px; 
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
        }
        .invoice-details { 
            margin-bottom: 20px; 
        }
        .invoice-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        .invoice-table th, .invoice-table td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
        }
        .invoice-table th { 
            background-color: #f5f5f5; 
        }
        .total-row { 
            font-weight: bold; 
        }
        .footer { 
            margin-top: 30px; 
            text-align: center; 
            font-size: 10px; 
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>Invoice #%invoice_number%</p>
    </div>
    
    <div class="invoice-details">
        <p><strong>Bill To:</strong> %client_name%</p>
        <p><strong>Date:</strong> %created%</p>
        <p><strong>Due Date:</strong> %due_date%</p>
    </div>
    
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <!-- Invoice items will be inserted here -->
            <tr>
                <td>Sample Service</td>
                <td>1</td>
                <td>$100.00</td>
                <td>$100.00</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3">Total:</td>
                <td>%payment_amount%</td>
            </tr>
        </tfoot>
    </table>
    
    <div class="notes">
        <p><strong>Notes:</strong> %notes%</p>
    </div>
    
    <div class="footer">
        <p>Thank you for your business!</p>
    </div>
</body>
</html>';
// Check if the ID param exists
if (isset($_GET['id'])) {
    // Retrieve the html file
    $template['html'] = file_exists(base_path . 'templates/' . $_GET['id'] . '/template.php') ? file_get_contents(base_path . 'templates/' . $_GET['id'] . '/template.php') : '';
    // Retrieve the pdf file
    $template['pdf'] = file_exists(base_path . 'templates/' . $_GET['id'] . '/template-pdf.php') ? file_get_contents(base_path . 'templates/' . $_GET['id'] . '/template-pdf.php') : '';
    // Get the template name
    $template['name'] = $_GET['id'];
    // Get the preview image
    $template['preview'] = file_exists(base_path . 'templates/' . $_GET['id'] . '/preview.png') ? base_url . 'templates/' . $_GET['id'] . '/preview.png' : '';
    // ID param exists, edit an existing account
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Check if the template name has changed
        if ($_GET['id'] != $_POST['name']) {
            // check if the new name already exists
            if (file_exists(base_path . 'templates/' . $_POST['name'])) {
                $error_msg = 'Template name already exists!';
            } else {
                // Rename the directory
                if (!rename(base_path . 'templates/' . $_GET['id'], base_path . 'templates/' . $_POST['name'])) {
                    $error_msg = 'Error renaming template directory! Please set the correct permissions!';
                } else {
                    // update invoices in the database
                    $stmt = $pdo->prepare('UPDATE invoices SET invoice_template = ? WHERE invoice_template = ?');
                    $stmt->execute([ $_POST['name'], $_GET['id'] ]);
                }
            }
        }
        // Update the html file
        if ($_POST['html'] && !file_put_contents(base_path . 'templates/' . $_POST['name'] . '/template.php', $_POST['html'])) {
            $error_msg = 'Error updating template file! Please set the correct permissions!';
        }
        // Update the pdf file
        if ($_POST['pdf'] && !file_put_contents(base_path . 'templates/' . $_POST['name'] . '/template-pdf.php', $_POST['pdf'])) {
            $error_msg = 'Error updating template file! Please set the correct permissions!';
        }
        // Update the preview image
        if (isset($_FILES['preview']) && $_FILES['preview']['size'] > 0) {
            // Check if the file is an image
            if (exif_imagetype($_FILES['preview']['tmp_name']) == IMAGETYPE_PNG) {
                // Save the image
                move_uploaded_file($_FILES['preview']['tmp_name'], base_path . 'templates/' . $_POST['name'] . '/preview.png');
            } else {
                $error_msg = 'Preview image must be a PNG file!';
            }
        }
        // Redirect if successful
        if (!isset($error_msg)) {
            header('Location: invoice_templates.php?success_msg=2');
            exit;
        } else {
            // Save the submitted values
            $template = [
                'name' => $_POST['name'],
                'html' => $_POST['html'],
                'pdf' => $_POST['pdf']
            ];
        }
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete template
        header('Location: invoice_templates.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new template
    $page = 'Create';
    if (isset($_POST['submit'])) {
        // Check if the template name already exists
        if (file_exists(base_path . 'templates/' . $_POST['name'])) {
            $error_msg = 'Template name already exists!';
        } else {
            // Create the directory
            if (!mkdir(base_path . 'templates/' . $_POST['name'])) {
                $error_msg = 'Error creating template directory! Please set the correct permissions!';
            }
            // Create the html file
            if ($_POST['html'] && !file_put_contents(base_path . 'templates/' . $_POST['name'] . '/template.php', $_POST['html'])) {
                $error_msg = 'Error creating template file! Please set the correct permissions!';
            }
            // Create the pdf file
            if ($_POST['pdf'] && !file_put_contents(base_path . 'templates/' . $_POST['name'] . '/template-pdf.php', $_POST['pdf'])) {
                $error_msg = 'Error creating template file! Please set the correct permissions!';
            }
            // Save the preview image
            if (isset($_FILES['preview']) && $_FILES['preview']['size'] > 0) {
                // Check if the file is an image
                if (exif_imagetype($_FILES['preview']['tmp_name']) == IMAGETYPE_PNG) {
                    // Save the image
                    move_uploaded_file($_FILES['preview']['tmp_name'], base_path . 'templates/' . $_POST['name'] . '/preview.png');
                } else {
                    $error_msg = 'Preview image must be a PNG file!';
                }
            }
        }
        // Redirect if successful
        if (!isset($error_msg)) {
            header('Location: invoice_templates.php?success_msg=1');
            exit;
        } else {
            // Save the submitted values
            $template = [
                'name' => $_POST['name'],
                'html' => $_POST['html'],
                'pdf' => $_POST['pdf']
            ];
        }
    }
}
?>
<?=template_admin_header($page . ' Invoice Template', 'invoices', 'templates')?>

<form action="" method="post" enctype="multipart/form-data">
    <div class="content-header">
        <div class="content-title mb-4">
            <h2 class="responsive-width-100"><?=$page?> Invoice Template</h2>
        </div>
        <div style="height: 20px;"></div>
        <div class="d-flex gap-2 mb-4">
            <a href="invoice_templates.php" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Cancel
            </a>
            <button type="submit" name="submit" class="btn btn-success">
                <i class="bi bi-save me-1" aria-hidden="true"></i>Save Template
            </button>
        </div>
        <p class="text-muted">Create or edit invoice templates for generating professional invoices.</p>
    </div>

    <?php if (isset($error_msg)): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2" aria-hidden="true"></i>
        <?=$error_msg?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">Template Configuration</h6>
            <small class="text-muted">Configure template settings and upload preview</small>
        </div>
        <div class="card-body">
            <fieldset>
                <legend class="h6 mb-3">Template Information</legend>
                
                <div class="mb-3">
                    <label for="name" class="form-label">
                        <span class="text-danger">*</span> Template Name
                    </label>
                    <input type="text" id="name" name="name" class="form-control" value="<?=$template['name']?>" placeholder="Enter template name" required>
                    <div class="form-text">Choose a unique name for this template.</div>
                </div>

                <div class="mb-3">
                    <label for="preview" class="form-label">Preview Image</label>
                    <input type="file" id="preview" name="preview" class="form-control" accept="image/png">
                    <div class="form-text">Upload a PNG preview image (optional).</div>
                    <?php if ($template['preview']): ?>
                    <div class="mt-2">
                        <small class="text-muted d-block mb-2">Current preview:</small>
                        <img src="<?=$template['preview']?>" alt="Template preview" class="img-thumbnail" style="max-width: 200px;">
                    </div>
                    <?php endif; ?>
                </div>
            </fieldset>

            <fieldset>
                <legend class="h6 mb-3">Template Code</legend>
                
                <div class="mb-3">
                    <label for="html" class="form-label">HTML Template</label>
                    <textarea id="html" name="html" class="form-control summernote" rows="15" placeholder="Your HTML template code here..."><?=$template['html']?></textarea>
                    <div class="form-text">HTML code for the web view of the invoice.</div>
                </div>

                <div class="mb-3">
                    <label for="pdf" class="form-label">
                        PDF Template 
                        <button type="button" class="btn btn-link btn-sm p-0 ms-2" data-bs-toggle="modal" data-bs-target="#pdfInstructionsModal" title="View PDF Template Instructions">
                            <i class="bi bi-question-circle text-info" aria-hidden="true"></i>
                        </button>
                    </label>
                    
                    <!-- PDF Template Tabs -->
                    <div class="border rounded">
                        <ul class="nav nav-tabs" id="pdfTemplateTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pdf-code-tab" data-bs-toggle="tab" data-bs-target="#pdf-code" type="button" role="tab" aria-controls="pdf-code" aria-selected="true">
                                    <i class="bi bi-code me-1" aria-hidden="true"></i>Code Editor
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pdf-preview-tab" data-bs-toggle="tab" data-bs-target="#pdf-preview" type="button" role="tab" aria-controls="pdf-preview" aria-selected="false">
                                    <i class="bi bi-eye me-1" aria-hidden="true"></i>Preview
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="pdfTemplateTabContent">
                            <!-- Code Editor Tab -->
                            <div class="tab-pane fade show active" id="pdf-code" role="tabpanel" aria-labelledby="pdf-code-tab">
                                <div class="p-3">
                                    <div id="pdf-editor-container">
                                        <textarea id="pdf" name="pdf" class="form-control font-monospace" rows="15"><?=$template['pdf'] ?: $default_pdf_template?></textarea>
                                    </div>
                                    <div class="mt-2 d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bi bi-keyboard me-1" aria-hidden="true"></i>Press F11 for fullscreen • Ctrl+Space for autocomplete
                                        </small>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="updatePreview()">
                                            <i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>Update Preview
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Preview Tab -->
                            <div class="tab-pane fade" id="pdf-preview" role="tabpanel" aria-labelledby="pdf-preview-tab">
                                <div class="p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">
                                            <i class="bi bi-eye text-primary me-1" aria-hidden="true"></i>Template Preview
                                        </h6>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <input type="radio" class="btn-check" name="previewMode" id="preview-desktop" autocomplete="off" checked>
                                            <label class="btn btn-outline-secondary" for="preview-desktop" title="Desktop View">
                                                <i class="bi bi-pc" aria-hidden="true"></i>
                                            </label>
                                            
                                            <input type="radio" class="btn-check" name="previewMode" id="preview-mobile" autocomplete="off">
                                            <label class="btn btn-outline-secondary" for="preview-mobile" title="Mobile View">
                                                <i class="bi bi-phone" aria-hidden="true"></i>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div id="pdf-preview-container" class="border rounded bg-white position-relative" style="min-height: 400px;">
                                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                                            <i class="bi bi-file-earmark-pdf fa-3x text-muted mb-3" aria-hidden="true"></i>
                                            <p class="text-muted">Click "Update Preview" to see your template rendered</p>
                                            <button type="button" class="btn btn-primary" onclick="updatePreview()">
                                                <i class="bi bi-play me-1" aria-hidden="true"></i>Generate Preview
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                                            Preview shows how your template will look with sample data. 
                                            Actual PDFs may vary based on the PDF generation library used.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-text mt-2">
                        Code for generating PDF versions of the invoice. 
                        <a href="#" data-bs-toggle="modal" data-bs-target="#pdfInstructionsModal" class="text-decoration-none">
                            <i class="bi bi-info-circle" aria-hidden="true"></i> View instructions and examples
                        </a>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="card-footer">
            <div class="d-flex gap-2">
                <a href="invoice_templates.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Cancel
                </a>
                <button type="submit" name="submit" class="btn btn-success">
                    <i class="bi bi-save me-1" aria-hidden="true"></i>Save Template
                </button>
                <?php if ($page == 'Edit'): ?>
                <button type="submit" name="delete" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this template?')">
                    <i class="bi bi-trash me-1" aria-hidden="true"></i>Delete
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<!-- PDF Instructions Modal -->
<div class="modal fade" id="pdfInstructionsModal" tabindex="-1" aria-labelledby="pdfInstructionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfInstructionsModalLabel">
                    <i class="bi bi-file-earmark-pdf text-danger me-2" aria-hidden="true"></i>PDF Template Instructions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">📋 Available Placeholders</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    <tr><td><code>%invoice_number%</code></td><td>Invoice number</td></tr>
                                    <tr><td><code>%client_name%</code></td><td>Client name</td></tr>
                                    <tr><td><code>%due_date%</code></td><td>Due date</td></tr>
                                    <tr><td><code>%payment_amount%</code></td><td>Total amount</td></tr>
                                    <tr><td><code>%created%</code></td><td>Creation date</td></tr>
                                    <tr><td><code>%notes%</code></td><td>Invoice notes</td></tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <h6 class="text-success mt-3">✅ Supported CSS Properties</h6>
                        <ul class="list-unstyled small">
                            <li>• <code>margin, padding</code></li>
                            <li>• <code>font-size, font-family, color</code></li>
                            <li>• <code>background-color</code></li>
                            <li>• <code>border, border-collapse</code></li>
                            <li>• <code>text-align, font-weight</code></li>
                            <li>• <code>width, height</code></li>
                        </ul>
                        
                        <h6 class="text-warning mt-3">⚠️ Best Practices</h6>
                        <ul class="list-unstyled small">
                            <li>• Use table-based layouts</li>
                            <li>• Keep styling simple</li>
                            <li>• Use inline CSS styles</li>
                            <li>• Test after each change</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-danger">❌ Avoid These Features</h6>
                        <ul class="list-unstyled small">
                            <li>• JavaScript</li>
                            <li>• External CSS files</li>
                            <li>• Flexbox, CSS Grid</li>
                            <li>• Complex positioning</li>
                            <li>• CSS3 animations</li>
                        </ul>
                        
                        <h6 class="text-info mt-3">💡 Quick Start Template</h6>
                        <div class="bg-light p-2 rounded">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadDefaultTemplate()">
                                <i class="bi bi-download me-1" aria-hidden="true"></i>Load Sample Template
                            </button>
                        </div>
                        
                        <h6 class="text-secondary mt-3">🔧 Page Breaks</h6>
                        <p class="small">Insert page breaks using:</p>
                        <code class="d-block bg-light p-2 rounded small">
                            &lt;div style="page-break-before: always;"&gt;&lt;/div&gt;
                        </code>
                        
                        <h6 class="text-secondary mt-3">📏 Recommended Fonts</h6>
                        <p class="small">Use web-safe fonts like:</p>
                        <ul class="list-unstyled small">
                            <li>• Arial, sans-serif</li>
                            <li>• Times New Roman, serif</li>
                            <li>• Courier New, monospace</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="loadDefaultTemplate()" data-bs-dismiss="modal">
                    <i class="bi bi-download me-1" aria-hidden="true"></i>Use Sample Template
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Summernote for HTML template editor
    $('.summernote').summernote({
        height: 400,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        placeholder: 'Enter your HTML template code here...',
        tabsize: 2,
        focus: false,
        codemirror: {
            theme: 'monokai',
            lineNumbers: true,
            lineWrapping: true,
            mode: 'text/html'
        }
    });
    
    // Initialize CodeMirror for PDF template
    window.pdfEditor = CodeMirror.fromTextArea(document.getElementById('pdf'), {
        mode: 'htmlmixed',
        theme: 'monokai',
        lineNumbers: true,
        lineWrapping: true,
        indentUnit: 4,
        tabSize: 4,
        autoCloseTags: true,
        autoCloseBrackets: true,
        matchBrackets: true,
        foldGutter: true,
        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
        extraKeys: {
            "Ctrl-Space": "autocomplete",
            "F11": function(cm) {
                cm.setOption("fullScreen", !cm.getOption("fullScreen"));
            },
            "Esc": function(cm) {
                if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
            }
        }
    });
    
    // Set height for better visibility
    window.pdfEditor.setSize(null, 400);
    
    // Handle tab switching to refresh CodeMirror
    $('#pdfTemplateTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (e.target.getAttribute('aria-controls') === 'pdf-code') {
            // Refresh CodeMirror when switching back to code tab
            setTimeout(function() {
                window.pdfEditor.refresh();
            }, 100);
        }
    });
    
    // Handle preview mode changes
    $('input[name="previewMode"]').change(function() {
        updatePreviewLayout();
    });
});

// Function to load default template
function loadDefaultTemplate() {
    if (window.pdfEditor) {
        window.pdfEditor.setValue(<?=json_encode($default_pdf_template)?>);
    }
}

// Function to update preview
function updatePreview() {
    const previewContainer = document.getElementById('pdf-preview-container');
    const code = window.pdfEditor ? window.pdfEditor.getValue() : document.getElementById('pdf').value;
    
    if (!code.trim()) {
        previewContainer.innerHTML = `
            <div class="position-absolute top-50 start-50 translate-middle text-center">
                <i class="bi bi-exclamation-triangle-fill fa-3x text-warning mb-3" aria-hidden="true"></i>
                <p class="text-muted">No template code to preview</p>
                <button type="button" class="btn btn-primary" onclick="loadDefaultTemplate(); updatePreview();">
                    <i class="bi bi-download me-1" aria-hidden="true"></i>Load Sample Template
                </button>
            </div>
        `;
        return;
    }
    
    // Replace placeholders with sample data
    let previewCode = code
        .replace(/%invoice_number%/g, 'INV-2025-001')
        .replace(/%client_name%/g, 'John Doe Company')
        .replace(/%due_date%/g, 'August 30, 2025')
        .replace(/%payment_amount%/g, '$1,250.00')
        .replace(/%created%/g, 'August 13, 2025')
        .replace(/%notes%/g, 'Thank you for your business. Payment is due within 30 days.');
    
    // Create iframe for preview
    const iframe = document.createElement('iframe');
    iframe.style.width = '100%';
    iframe.style.height = '400px';
    iframe.style.border = 'none';
    iframe.style.backgroundColor = 'white';
    
    // Update layout based on preview mode
    updatePreviewLayout(iframe);
    
    previewContainer.innerHTML = '';
    previewContainer.appendChild(iframe);
    
    // Write content to iframe
    iframe.contentDocument.open();
    iframe.contentDocument.write(previewCode);
    iframe.contentDocument.close();
    
    // Add loading indicator
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75';
    loadingOverlay.innerHTML = <?php if (!function_exists('getBrandSpinnerHTML')) { require_once __DIR__ . '/../../../private/gws-universal-functions.php'; } echo json_encode('<div class="text-center">' . getBrandSpinnerHTML(null, ['size'=>'sm','label'=>'Loading','class'=>'mb-2']) . '<p class="text-muted mb-0">Rendering preview...</p></div>'); ?>;
    previewContainer.appendChild(loadingOverlay);
    
    // Remove loading overlay after iframe loads
    iframe.onload = function() {
        setTimeout(() => {
            if (loadingOverlay.parentNode) {
                loadingOverlay.remove();
            }
        }, 500);
    };
}

// Function to update preview layout based on selected mode
function updatePreviewLayout(iframe = null) {
    const container = document.getElementById('pdf-preview-container');
    const isDesktop = document.getElementById('preview-desktop').checked;
    
    if (iframe) {
        if (isDesktop) {
            iframe.style.width = '100%';
            iframe.style.maxWidth = 'none';
        } else {
            iframe.style.width = '375px';
            iframe.style.maxWidth = '100%';
            iframe.style.margin = '0 auto';
            iframe.style.display = 'block';
        }
    }
    
    // Update container styling
    if (isDesktop) {
        container.style.padding = '0';
    } else {
        container.style.padding = '20px';
        container.style.display = 'flex';
        container.style.justifyContent = 'center';
    }
}
</script>

<?=template_admin_footer()?>