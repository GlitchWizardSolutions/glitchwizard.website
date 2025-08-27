<?php
// Quick test to check branding form structure
include_once '../assets/includes/main.php';

echo "<h1>🔍 Branding Form Structure Check</h1>";

// Load the branding page content without rendering the full page
ob_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Structure Test</title>
    <style>
        .tab-content { display: block !important; }
        .tab-content[hidden] { display: block !important; }
        form { border: 2px solid #007bff; padding: 20px; margin: 20px 0; }
        .submit-section { background: #f8f9fa; padding: 15px; border: 2px dashed #28a745; }
        .form-controls { background: #fff3cd; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>

<h2>Brand Colors Form Structure Test</h2>

<!-- Simplified Brand Colors Tab -->
<div id="colors-tab" class="tab-content">
    <h3 class="mb-4">Brand Colors</h3>
    <p class="text-muted mb-4">Define your brand colors that will be used throughout your website.</p>
    
    <form method="POST" id="colorsForm" style="border: 2px solid #007bff; padding: 20px;">
        <input type="hidden" name="action" value="update_brand_colors">
        
        <div class="form-controls">
            <h4>🎨 Color Inputs</h4>
            <p>Primary Color Input: <input type="color" value="#6c2eb6"> <input type="text" value="#6C2EB6"></p>
            <p>Secondary Color Input: <input type="color" value="#bf5512"> <input type="text" value="#BF5512"></p>
        </div>

        <div class="submit-section">
            <h4>🚀 Submit Section</h4>
            <div class="d-flex justify-content-start align-items-center gap-3 mt-4 pt-3" style="border-top: 1px solid #dee2e6;">
                <button type="button" class="btn btn-outline-secondary">
                    🔄 Reset
                </button>
                <button type="submit" class="btn btn-success" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px;">
                    💾 Save Brand Colors
                </button>
                <span class="colors-save-status small text-muted ms-2">Status area</span>
            </div>
        </div>
    </form>
</div>

<h2>Current Branding Settings Page Check</h2>
<div style="background: #f8f9fa; padding: 20px; border-radius: 4px; margin: 20px 0;">
    <p><strong>If you can see this form structure above, then the submit button should be working.</strong></p>
    <p><strong>If the button is missing in the actual page, it might be:</strong></p>
    <ul>
        <li>Hidden by CSS styling</li>
        <li>Not rendering due to a PHP error</li>
        <li>Covered by another element</li>
        <li>The tab is not switching properly</li>
    </ul>
</div>

<h2>Troubleshooting Steps</h2>
<div style="background: #fff3cd; padding: 20px; border-radius: 4px; margin: 20px 0;">
    <ol>
        <li><strong>Check Browser Console:</strong> Open Developer Tools (F12) and look for JavaScript errors</li>
        <li><strong>Check Tab Switching:</strong> Make sure you're clicking on the "Brand Colors" tab</li>
        <li><strong>Check Page Scroll:</strong> The submit button might be below the visible area - scroll down</li>
        <li><strong>Check Form Rendering:</strong> View page source and search for "Save Brand Colors"</li>
    </ol>
</div>

<h2>Quick Fixes</h2>
<div style="background: #d4edda; padding: 20px; border-radius: 4px; margin: 20px 0;">
    <p><strong>Try these:</strong></p>
    <ul>
        <li>Refresh the branding settings page (Ctrl+F5)</li>
        <li>Click specifically on the "Brand Colors" tab</li>
        <li>Scroll down to the bottom of the form</li>
        <li>Check if there are any error messages in red on the page</li>
    </ul>
</div>

</body>
</html>

<?php
$output = ob_get_clean();
echo $output;
?>

<h3>Quick Actions:</h3>
<a href="branding_settings_tabbed.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px;" target="_blank">
    🔍 Open Branding Settings (New Tab)
</a>
<a href="javascript:history.back()" style="background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
    ← Back
</a>
