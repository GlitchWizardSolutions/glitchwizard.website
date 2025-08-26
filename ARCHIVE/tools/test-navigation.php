<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigation Test - GWS Universal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .test-section {
            padding: 40px 0;
            min-height: 200px;
        }
        .test-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<?php include 'assets/includes/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="test-info">
                <h2>Navigation Test Page</h2>
                <p><strong>What to test:</strong></p>
                <ul>
                    <li><strong>Desktop (screen width ≥ 992px):</strong> Navigation menu should be visible horizontally</li>
                    <li><strong>Mobile (screen width < 992px):</strong> Navigation should be hidden, hamburger button visible</li>
                    <li><strong>Hamburger button:</strong> Should be properly sized (40x40px) and bold</li>
                    <li><strong>Mobile menu:</strong> Should open/close when hamburger is clicked</li>
                    <li><strong>Icons:</strong> Should switch between hamburger (☰) and X (✕) when toggled</li>
                </ul>
                
                <div class="mt-3">
                    <p><strong>Current Template:</strong> <span class="badge bg-primary" id="current-template">Loading...</span></p>
                    <p><strong>Current Area:</strong> <span class="badge bg-secondary" id="current-area">Loading...</span></p>
                    <p><strong>Screen Width:</strong> <span class="badge bg-info" id="screen-width">Loading...</span></p>
                    <p><strong>Expected Behavior:</strong> 
                        <span id="expected-behavior" class="badge bg-warning">Checking...</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="test-section bg-light">
                <h3>Test Section 1</h3>
                <p>This section helps test scrolling and navigation behavior. Try resizing your browser window to test responsive navigation.</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="test-section bg-light">
                <h3>Test Section 2</h3>
                <p>Check that the hamburger menu works on mobile devices and small screens.</p>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="test-info">
                <h4>Browser Console Check</h4>
                <p>Open your browser's developer tools (F12) and check the Console tab for any JavaScript errors.</p>
                <button class="btn btn-primary" onclick="testNavigation()">Test Navigation Functions</button>
                <div id="test-results" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Display current template and area
    if (window.EnhancedBranding) {
        document.getElementById('current-template').textContent = window.EnhancedBranding.getActiveTemplate();
        document.getElementById('current-area').textContent = window.EnhancedBranding.getCurrentArea();
    }
    
    // Update screen width and expected behavior
    updateScreenInfo();
    
    // Listen for window resize
    window.addEventListener('resize', updateScreenInfo);
});

function updateScreenInfo() {
    const width = window.innerWidth;
    document.getElementById('screen-width').textContent = width + 'px';
    
    const expected = document.getElementById('expected-behavior');
    if (width >= 992) {
        expected.textContent = 'Desktop: Menu visible, hamburger hidden';
        expected.className = 'badge bg-success';
    } else {
        expected.textContent = 'Mobile: Menu hidden, hamburger visible';
        expected.className = 'badge bg-warning';
    }
}

function testNavigation() {
    const results = document.getElementById('test-results');
    let html = '<div class="alert alert-info"><h5>Navigation Test Results:</h5><ul>';
    
    // Test 1: Check if hamburger button exists
    const hamburger = document.querySelector('.mobile-nav-toggle');
    if (hamburger) {
        html += '<li>✅ Hamburger button found</li>';
        html += `<li>📐 Button size: ${hamburger.offsetWidth}x${hamburger.offsetHeight}px</li>`;
        
        // Check computed styles
        const computedStyle = window.getComputedStyle(hamburger);
        html += `<li>🎨 Display: ${computedStyle.display}</li>`;
        html += `<li>🎨 Visibility: ${computedStyle.visibility}</li>`;
        html += `<li>🎨 Opacity: ${computedStyle.opacity}</li>`;
        html += `<li>🎨 Position: ${computedStyle.position}</li>`;
        html += `<li>🎨 Z-index: ${computedStyle.zIndex}</li>`;
        
        // Check if it's actually visible
        const rect = hamburger.getBoundingClientRect();
        html += `<li>📍 Position: top=${rect.top}, left=${rect.left}</li>`;
        html += `<li>👁️ Is visible: ${rect.width > 0 && rect.height > 0 ? 'YES' : 'NO'}</li>`;
    } else {
        html += '<li>❌ Hamburger button NOT found</li>';
    }
    
    // Test 2: Check if navigation menu exists
    const navmenu = document.getElementById('navmenu');
    if (navmenu) {
        html += '<li>✅ Navigation menu found</li>';
        const menuStyle = window.getComputedStyle(navmenu);
        html += `<li>📋 Menu display: ${menuStyle.display}</li>`;
        html += `<li>📋 Menu visibility: ${menuStyle.visibility}</li>`;
    } else {
        html += '<li>❌ Navigation menu NOT found</li>';
    }
    
    // Test 3: Check menu ul
    const menuUl = navmenu ? navmenu.querySelector('ul') : null;
    if (menuUl) {
        const ulStyle = window.getComputedStyle(menuUl);
        html += `<li>📋 Menu UL display: ${ulStyle.display}</li>`;
        html += `<li>📋 Menu UL visibility: ${ulStyle.visibility}</li>`;
    }
    
    // Test 4: Check Enhanced Branding functions
    if (window.EnhancedBranding) {
        html += '<li>✅ Enhanced Branding utilities loaded</li>';
    } else {
        html += '<li>❌ Enhanced Branding utilities NOT loaded</li>';
    }
    
    // Test 5: Screen size check
    html += `<li>📱 Current screen width: ${window.innerWidth}px</li>`;
    html += `<li>📱 Expected behavior: ${window.innerWidth >= 992 ? 'Desktop (menu visible, hamburger hidden)' : 'Mobile (hamburger visible, menu hidden)'}</li>`;
    
    html += '</ul></div>';
    results.innerHTML = html;
}
</script>

</body>
</html>
