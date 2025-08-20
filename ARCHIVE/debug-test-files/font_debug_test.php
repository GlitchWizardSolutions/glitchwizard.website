<?php
// Simple test page to debug font preview functionality
?>
<!DOCTYPE html>
<html>
<head>
    <title>Font Preview Debug Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .test-select { background: #374151; color: white; padding: 8px; border-radius: 4px; border: 2px solid #ccc; }
        .test-preview { background: #374151; color: white; padding: 20px; margin: 10px 0; border-radius: 8px; min-height: 50px; }
        .debug-info { background: #fff; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Font Preview Debug Test</h1>
    
    <div class="debug-info">
        <strong>Test 1: Basic Font Preview</strong><br>
        <select class="test-select font-select" data-font-preview="test">
            <option value="Arial, sans-serif">Arial</option>
            <option value="Georgia, serif">Georgia</option>
            <option value="Times New Roman, serif">Times New Roman</option>
            <option value="Courier New, monospace">Courier New</option>
        </select>
        <div class="test-preview" id="test-preview" style="font-family: Arial, sans-serif;">
            This text should change font when you select from dropdown above.
        </div>
    </div>
    
    <div class="debug-info">
        <strong>JavaScript Console Output:</strong><br>
        <div id="console-output"></div>
    </div>

    <script>
        // Capture console output
        const consoleOutput = document.getElementById('console-output');
        const originalLog = console.log;
        const originalError = console.error;
        
        console.log = function(...args) {
            consoleOutput.innerHTML += '<div style="color: blue;">LOG: ' + args.join(' ') + '</div>';
            originalLog.apply(console, args);
        };
        
        console.error = function(...args) {
            consoleOutput.innerHTML += '<div style="color: red;">ERROR: ' + args.join(' ') + '</div>';
            originalError.apply(console, args);
        };
        
        console.log('Debug script started');
        
        // Test font selection functionality
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');
            
            const fontSelects = document.querySelectorAll('.font-select');
            console.log('Found font selects:', fontSelects.length);
            
            fontSelects.forEach((select, index) => {
                console.log('Setting up listener for select', index, 'with data-font-preview:', select.dataset.fontPreview);
                
                select.addEventListener('change', function() {
                    console.log('Font changed!', 'Preview ID:', this.dataset.fontPreview, 'New value:', this.value);
                    
                    const previewId = this.dataset.fontPreview + '-preview';
                    const preview = document.getElementById(previewId);
                    
                    console.log('Looking for preview element:', previewId, 'Found:', !!preview);
                    
                    if (preview) {
                        const selectedValue = this.value || 'inherit';
                        preview.style.fontFamily = selectedValue;
                        console.log('Updated font family to:', selectedValue);
                    } else {
                        console.error('Preview element not found:', previewId);
                    }
                });
            });
            
            console.log('Font preview setup complete');
        });
    </script>
</body>
</html>
