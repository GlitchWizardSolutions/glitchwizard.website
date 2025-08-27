<?php
// Safe Content Integration Manager with Preview and Verification
session_start();

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Note: Settings now loaded from database via database_settings.php system
include_once "../assets/includes/settings/database_settings.php";
include_once "../private/gws-universal-config.php";

$page_to_scan = $_GET['page'] ?? '';
$action = $_POST['action'] ?? '';
$integration_id = $_POST['integration_id'] ?? '';

// Safety: Track what's already been processed
$processed_file = __DIR__ . '/integration_history.json';
$integration_history = [];
if (file_exists($processed_file)) {
    $integration_history = json_decode(file_get_contents($processed_file), true) ?? [];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Content Integration Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .preview-box {
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        .danger-zone {
            background: #fff5f5;
            border: 2px solid #feb2b2;
            border-radius: 8px;
            padding: 15px;
        }
        .safe-zone {
            background: #f0fff4;
            border: 2px solid #9ae6b4;
            border-radius: 8px;
            padding: 15px;
        }
        .before-after {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 15px 0;
        }
        .before-content, .after-content {
            padding: 15px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 0.9em;
            white-space: pre-wrap;
        }
        .before-content {
            background: #ffeaa7;
            border: 2px solid #fdcb6e;
        }
        .after-content {
            background: #d1f2eb;
            border: 2px solid #6c757d;
        }
        .variable-preview {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 10px;
            margin: 10px 0;
        }
        .conflict-warning {
            background: #ffebee;
            border-left: 4px solid #f44336;
            padding: 10px;
            margin: 10px 0;
        }
        .btn-verify {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }
        .btn-discard {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        .integration-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin: 15px 0;
            padding: 20px;
        }
        .step-indicator {
            background: #007bff;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <h1><i class="bi bi-shield-check text-primary"></i> Safe Content Integration Manager</h1>
                <p class="text-muted">Preview, verify, and safely integrate hardcoded content with settings system</p>
                
                <!-- Safety Status Panel -->
                <div class="safe-zone mb-4">
                    <h5><i class="bi bi-check-circle-fill text-success"></i> Safety Features Active</h5>
                    <ul class="mb-0">
                        <li><strong>Preview Mode:</strong> All changes shown before execution</li>
                        <li><strong>Conflict Detection:</strong> Checks for existing variables</li>
                        <li><strong>Backup Creation:</strong> Auto-backup before any changes</li>
                        <li><strong>Rollback Available:</strong> Can undo any integration</li>
                        <li><strong>Admin Verification:</strong> Requires manual approval</li>
                    </ul>
                </div>

                <?php if (empty($page_to_scan)): ?>
                    <!-- Page Selection -->
                    <div class="card">
                        <div class="card-header">
                            <h5><span class="step-indicator">1</span>Select Page to Analyze</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="page" class="form-label">Choose Page:</label>
                                        <select name="page" id="page" class="form-select" required>
                                            <option value="">Select a page...</option>
                                            <option value="index.php">Homepage (index.php)</option>
                                            <option value="policy-terms.php">Terms of Service</option>
                                            <option value="policy-privacy.php">Privacy Policy</option>
                                            <option value="policy-accessibility.php">Accessibility Policy</option>
                                            <option value="contact.php">Contact Page</option>
                                            <option value="about.php">About Page</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search"></i> Analyze Page
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Analysis Results -->
                    <div class="card">
                        <div class="card-header">
                            <h5><span class="step-indicator">2</span>Analysis Results for: <?php echo htmlspecialchars($page_to_scan); ?></h5>
                            <a href="?" class="btn btn-secondary btn-sm float-end">
                                <i class="bi bi-arrow-left"></i> Back to Selection
                            </a>
                        </div>
                        <div class="card-body">
                            <?php
                            // Analyze the selected page
                            $page_path = "../" . $page_to_scan;
                            if (!file_exists($page_path)) {
                                echo '<div class="alert alert-danger">Page not found: ' . htmlspecialchars($page_path) . '</div>';
                            } else {
                                $page_content = file_get_contents($page_path);
                                $analysis_results = analyze_page_content($page_content, $page_to_scan);
                                
                                if (empty($analysis_results['hardcoded_content'])) {
                                    echo '<div class="alert alert-success">
                                        <i class="bi bi-check-circle-fill"></i> 
                                        No hardcoded content detected! This page appears to be fully integrated.
                                    </div>';
                                } else {
                                    echo '<div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> 
                                        Found ' . count($analysis_results['hardcoded_content']) . ' items that could be integrated with settings.
                                    </div>';
                                    
                                    // Debug: Show what was actually detected
                                    echo '<div class="alert alert-warning">
                                        <strong>Debug - Detected content:</strong><br>';
                                    foreach ($analysis_results['hardcoded_content'] as $index => $item) {
                                        echo ($index + 1) . '. "' . htmlspecialchars($item['original_content']) . '" (Line: ' . $item['line_number'] . ', Context: ' . $item['context'] . ')<br>';
                                    }
                                    echo '</div>';
                                    
                                    // Display each potential integration
                                    foreach ($analysis_results['hardcoded_content'] as $index => $item) {
                                        display_integration_preview($item, $index, $page_to_scan);
                                    }
                                }
                            }
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Integration History -->
                <?php if (!empty($integration_history)): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5><i class="bi bi-clock-history"></i> Recent Integrations</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Page</th>
                                        <th>Content</th>
                                        <th>Variable</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($integration_history, -10) as $hist): ?>
                                    <tr>
                                        <td><?php echo date('M j, Y H:i', $hist['timestamp']); ?></td>
                                        <td><?php echo htmlspecialchars($hist['page']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($hist['original_content'], 0, 50)) . '...'; ?></td>
                                        <td><code><?php echo htmlspecialchars($hist['variable_name']); ?></code></td>
                                        <td>
                                            <span class="badge bg-<?php echo $hist['status'] === 'success' ? 'success' : 'danger'; ?>">
                                                <?php echo $hist['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-outline-warning btn-sm" onclick="rollbackIntegration('<?php echo $hist['id']; ?>')">
                                                <i class="bi bi-arrow-counterclockwise"></i> Rollback
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function approveIntegration(integrationId) {
            if (!confirm('Are you sure you want to proceed with this integration? This will modify your files.')) {
                return;
            }
            
            // Show loading state
            const button = event.target;
            const originalText = button.innerHTML;
            const spinnerHTML = <?php if (!function_exists('getBrandSpinnerHTML')) { require_once __DIR__ . '/../../private/gws-universal-functions.php'; } echo json_encode(getBrandSpinnerHTML(null, ['size'=>'sm','label'=>'Loading','class'=>'me-1 align-text-bottom'])); ?>;
            button.innerHTML = spinnerHTML + ' Processing...';
            button.disabled = true;
            
            // Get form data
            const form = document.getElementById('form_' + integrationId);
            const formData = new FormData(form);
            formData.set('action', 'process_integration');
            
            // Send to safe processor
            fetch('safe_integration_processor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const item = document.getElementById('item_' + integrationId);
                    item.innerHTML = `
                        <div class="alert alert-success">
                            <h6><i class="bi bi-check-circle-fill"></i> Integration Successful!</h6>
                            <p>${data.message}</p>
                            <small>Changes made: ${data.changes_made.join(', ')}</small>
                        </div>
                    `;
                } else {
                    // Show error message
                    alert('Integration failed: ' + data.message);
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            })
            .catch(error => {
                alert('Error processing integration: ' + error.message);
                button.innerHTML = originalText;
                button.disabled = false;
            });
        }
        
        function discardIntegration(integrationId) {
            if (confirm('Discard this integration suggestion?')) {
                const item = document.getElementById('item_' + integrationId);
                item.style.transition = 'opacity 0.3s';
                item.style.opacity = '0.3';
                setTimeout(() => {
                    item.style.display = 'none';
                }, 300);
            }
        }
        
        function editVariableName(integrationId) {
            const currentName = document.querySelector(`#form_${integrationId} input[name="variable_name"]`).value;
            const newName = prompt('Enter new variable name:', currentName);
            
            if (newName && newName !== currentName) {
                // Validate variable name
                if (!/^[a-zA-Z_][a-zA-Z0-9_]*$/.test(newName)) {
                    alert('Invalid variable name. Use only letters, numbers, and underscores.');
                    return;
                }
                
                // Update the form and preview
                document.querySelector(`#form_${integrationId} input[name="variable_name"]`).value = newName;
                document.querySelector(`#item_${integrationId} .variable-preview code`).textContent = '$' + newName;
                document.querySelector(`#item_${integrationId} .after-content`).innerHTML = 
                    document.querySelector(`#item_${integrationId} .after-content`).innerHTML.replace(/\$[a-zA-Z_][a-zA-Z0-9_]*/, '$' + newName);
            }
        }
        
        function rollbackIntegration(historyId) {
            if (confirm('Rollback this integration? This will restore the original content.')) {
                fetch('safe_integration_processor.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=rollback&history_id=' + historyId
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Rollback completed successfully!');
                        location.reload();
                    } else {
                        alert('Rollback failed: ' + data.message);
                    }
                });
            }
        }
        
        // Auto-save progress
        window.addEventListener('beforeunload', function() {
            // Save any pending changes
        });
    </script>
</body>
</html>

<?php
// Analysis functions
function analyze_page_content($content, $page_name) {
    $results = [
        'hardcoded_content' => [],
        'existing_variables' => [],
        'conflicts' => [],
        'integration_status' => 'unknown'
    ];
    
    // Extract existing settings variables to avoid conflicts
    $existing_vars = extract_existing_variables();
    
    // Read the raw PHP file (before processing) to find truly hardcoded content
    $raw_file_path = "../" . $page_name;
    $raw_content = file_exists($raw_file_path) ? file_get_contents($raw_file_path) : '';
    
    // Quick integration check - if the file has lots of PHP variables, it's likely integrated
    $php_variable_count = preg_match_all('/\$[a-zA-Z_][a-zA-Z0-9_]*/', $raw_content);
    $settings_includes = preg_match('/include.*settings.*public_settings\.php/', $raw_content);
    
    if ($php_variable_count > 5 && $settings_includes) {
        $results['integration_status'] = 'well_integrated';
    }
    
    // Parse the raw PHP content to find hardcoded strings
    $hardcoded_strings = extract_hardcoded_strings($raw_content);
    
    if (empty($hardcoded_strings) && $results['integration_status'] === 'well_integrated') {
        $results['integration_status'] = 'fully_integrated';
        return $results;
    }
    
    foreach ($hardcoded_strings as $string_info) {
        $text = $string_info['content'];
        
        // Very strict filtering to exclude PHP fragments and code
        if (strlen(trim($text)) < 10 || 
            strpos($text, '$') !== false || 
            strpos($text, 'php') !== false ||
            strpos($text, '?>') !== false ||
            strpos($text, '<?') !== false ||
            strpos($text, '];') !== false ||
            strpos($text, '])') !== false ||
            strpos($text, 'htmlspecialchars') !== false ||
            strpos($text, 'echo') !== false ||
            strpos($text, 'isset') !== false ||
            strpos($text, '[') !== false ||
            strpos($text, ']') !== false ||
            strpos($text, ';') !== false ||
            strpos($text, '?') !== false && strpos($text, ' ') === false ||
            preg_match('/^[a-z_\s]*$/', trim($text)) ||
            preg_match('/^[\s\>\<\;\?\[\]\(\)]*$/', trim($text)) ||
            preg_match('/^\s*$/', $text)) {
            continue;
        }
        
        // Skip development mode content
        if (strpos($text, 'Development Mode') !== false || 
            strpos($text, 'Content Integration') !== false ||
            strpos($text, 'Legend') !== false) {
            continue;
        }
        
        // Skip navigation items
        $nav_items = ['Home', 'About', 'Services', 'Contact', 'Login', 'Register', 'Dashboard', 'Logout'];
        if (in_array(trim($text), $nav_items)) {
            continue;
        }
        
        // Skip content that appears to be from existing settings
        if (content_appears_to_be_from_settings($text, $existing_vars)) {
            continue;
        }
        
        $suggested_var = generate_variable_name($text, $page_name);
        
        // Check for conflicts
        $conflict = false;
        if (in_array($suggested_var, $existing_vars)) {
            $conflict = true;
            $suggested_var = $suggested_var . '_' . uniqid();
        }
        
        $results['hardcoded_content'][] = [
            'original_content' => $text,
            'suggested_variable' => $suggested_var,
            'context' => $string_info['context'],
            'element_type' => $string_info['element_type'],
            'conflict' => $conflict,
            'scope' => determine_content_scope($text, $page_name),
            'line_number' => $string_info['line_number'] ?? 0
        ];
    }
    
    // Determine final integration status
    if (empty($results['hardcoded_content'])) {
        $results['integration_status'] = 'fully_integrated';
    } else if (count($results['hardcoded_content']) < 3) {
        $results['integration_status'] = 'mostly_integrated';
    } else {
        $results['integration_status'] = 'needs_integration';
    }
    
    return $results;
}

function extract_hardcoded_strings($php_content) {
    $strings = [];
    $lines = explode("\n", $php_content);
    
    foreach ($lines as $line_num => $line) {
        $line = trim($line);
        
        // Skip empty lines, comments, and pure PHP logic
        if (empty($line) || strpos($line, '//') === 0 || strpos($line, '/*') === 0) {
            continue;
        }
        
        // Method 1: HTML tags with hardcoded content (not PHP variables)
        // Look for complete HTML elements like <h1>Hardcoded Text</h1>
        if (preg_match_all('/<(h[1-6]|p|div|span|li|strong|em|b|i|title|label|button|a)\b[^>]*>([^<]+)<\/\1>/i', $line, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $tag = $match[1];
                $content = trim($match[2]);
                
                // Skip if it contains PHP variables, functions, or code fragments
                if (strlen($content) < 5 || 
                    strpos($content, '$') !== false || 
                    strpos($content, '<?php') !== false || 
                    strpos($content, 'echo') !== false ||
                    strpos($content, 'htmlspecialchars') !== false ||
                    strpos($content, 'isset') !== false ||
                    strpos($content, '])') !== false ||  // PHP array syntax
                    strpos($content, '? ') !== false ||  // Ternary operator
                    strpos($content, '; ?>') !== false || // PHP closing tag
                    preg_match('/\w+\(\s*\$/', $content) || // Function calls with variables
                    in_array(strtolower($content), ['home', 'about', 'services', 'contact', 'login', 'register', 'dashboard'])) {
                    continue;
                }
                
                $strings[] = [
                    'content' => $content,
                    'line_number' => $line_num + 1,
                    'context' => "HTML <$tag> element",
                    'element_type' => $tag,
                    'full_match' => $match[0]
                ];
            }
        }
        
        // Method 2: Self-closing tags with hardcoded attributes
        // Look for <img alt="text"> or <input placeholder="text">
        if (preg_match_all('/<(img|input)\b[^>]*(?:alt|placeholder|title|value)=(["\'])([^"\']{5,})\2[^>]*\/?>/i', $line, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $tag = $match[1];
                $content = trim($match[3]);
                
                // Skip if it contains PHP variables
                if (strpos($content, '$') !== false || strpos($content, '<?php') !== false) {
                    continue;
                }
                
                $strings[] = [
                    'content' => $content,
                    'line_number' => $line_num + 1,
                    'context' => "Attribute in <$tag>",
                    'element_type' => $tag . '_attribute',
                    'full_match' => $match[0]
                ];
            }
        }
        
        // Method 3: PHP echo with hardcoded strings
        // Look for echo "hardcoded text"
        if (preg_match_all('/echo\s+(["\'])([^"\']{8,})\1/i', $line, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $content = trim($match[2]);
                
                // Skip if it looks like HTML tags or contains variables
                if (strpos($content, '<') !== false || strpos($content, '>') !== false || 
                    strpos($content, '$') !== false) {
                    continue;
                }
                
                $strings[] = [
                    'content' => $content,
                    'line_number' => $line_num + 1,
                    'context' => 'PHP echo statement',
                    'element_type' => 'php_echo',
                    'full_match' => $match[0]
                ];
            }
        }
        
        // Method 4: Standalone hardcoded text between tags (most common)
        // Look for >Hardcoded Text< patterns but be very selective
        if (preg_match_all('/>\s*([A-Za-z][A-Za-z0-9\s\.,!?;:\'"()-]{8,}?)\s*</i', $line, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $content = trim($match[1]);
                
                // Skip if it contains PHP, is too short, or looks like code
                if (strlen($content) < 8 || 
                    strpos($content, '$') !== false || 
                    strpos($content, '<?') !== false || 
                    strpos($content, '?>') !== false ||
                    preg_match('/^[\s\d\.\-_]*$/', $content) ||
                    in_array(strtolower($content), ['home', 'about', 'services', 'contact', 'blog', 'shop'])) {
                    continue;
                }
                
                // Only include if it looks like genuine content (has spaces or punctuation)
                if (preg_match('/[A-Za-z]{3,}.*[A-Za-z]{3,}/', $content) || 
                    preg_match('/[A-Za-z\s]{8,}[.!?]/', $content)) {
                    $strings[] = [
                        'content' => $content,
                        'line_number' => $line_num + 1,
                        'context' => 'HTML content',
                        'element_type' => 'text_content',
                        'full_match' => '>' . $content . '<'
                    ];
                }
            }
        }
    }
    
    // Remove duplicates based on content
    $unique_strings = [];
    foreach ($strings as $string) {
        $key = strtolower(trim($string['content']));
        if (!isset($unique_strings[$key]) && strlen($key) > 7) {
            $unique_strings[$key] = $string;
        }
    }
    
    return array_values($unique_strings);
}

function content_appears_to_be_from_settings($text, $existing_vars) {
    // Check if this content looks like it could be from an existing settings variable
    $business_indicators = ['LLC', 'Inc', 'Corp', 'Company', 'Solutions'];
    $contact_indicators = ['@', '.com', '.net', '.org', 'phone', 'email'];
    $address_indicators = ['Street', 'Ave', 'Road', 'Blvd', 'Suite', 'Unit'];
    
    $text_lower = strtolower($text);
    
    // If it contains business indicators and we have business_name variable
    if (in_array('business_name', $existing_vars)) {
        foreach ($business_indicators as $indicator) {
            if (strpos($text, $indicator) !== false) {
                return true;
            }
        }
    }
    
    // If it contains contact indicators and we have contact variables
    if (array_intersect(['contact_email', 'contact_phone'], $existing_vars)) {
        foreach ($contact_indicators as $indicator) {
            if (strpos($text_lower, $indicator) !== false) {
                return true;
            }
        }
    }
    
    // If it contains address indicators and we have address variables
    if (array_intersect(['contact_address', 'contact_city', 'contact_state'], $existing_vars)) {
        foreach ($address_indicators as $indicator) {
            if (strpos($text, $indicator) !== false) {
                return true;
            }
        }
    }
    
    return false;
}

function extract_existing_variables() {
    $vars = [];
    // Note: public_settings.php is deprecated - variables now loaded from database
    $settings_file = "../assets/includes/settings/database_settings.php";
    
    if (file_exists($settings_file)) {
        $content = file_get_contents($settings_file);
        preg_match_all('/\$([a-zA-Z_][a-zA-Z0-9_]*)\s*=/', $content, $matches);
        $vars = $matches[1];
    }
    
    return $vars;
}

function generate_variable_name($text, $page_name) {
    // Create a safe variable name from content and page
    $base_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $text));
    $base_name = substr($base_name, 0, 30); // Limit length
    $base_name = trim($base_name, '_');
    
    $page_prefix = str_replace(['.php', '-'], ['', '_'], $page_name);
    
    return $page_prefix . '_' . $base_name;
}

function get_content_context($node) {
    $parent = $node->parentNode;
    if ($parent) {
        return $parent->nodeName . (isset($parent->attributes['class']) ? '.' . $parent->attributes['class']->value : '');
    }
    return 'unknown';
}

function determine_content_scope($text, $page_name) {
    // Determine if content should be global, section-specific, or page-specific
    $global_indicators = ['copyright', 'all rights reserved', 'contact', 'phone', 'email', 'address'];
    $section_indicators = ['services', 'about', 'team', 'testimonials'];
    
    $text_lower = strtolower($text);
    
    foreach ($global_indicators as $indicator) {
        if (strpos($text_lower, $indicator) !== false) {
            return 'global';
        }
    }
    
    foreach ($section_indicators as $indicator) {
        if (strpos($text_lower, $indicator) !== false) {
            return 'section';
        }
    }
    
    return 'page';
}

function display_integration_preview($item, $index, $page_name) {
    $integration_id = $page_name . '_' . $index;
    ?>
    <div class="integration-item" id="item_<?php echo $integration_id; ?>">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h6><i class="bi bi-code-slash"></i> Integration Preview #<?php echo $index + 1; ?></h6>
            <div>
                <span class="badge bg-<?php echo $item['scope'] === 'global' ? 'primary' : ($item['scope'] === 'section' ? 'warning' : 'info'); ?>">
                    <?php echo ucfirst($item['scope']); ?> Scope
                </span>
                <?php if ($item['conflict']): ?>
                    <span class="badge bg-danger">Conflict Detected</span>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($item['conflict']): ?>
        <div class="conflict-warning">
            <i class="bi bi-exclamation-triangle-fill"></i> 
            <strong>Variable name conflict detected!</strong> 
            Suggested alternative: <code><?php echo htmlspecialchars($item['suggested_variable']); ?></code>
        </div>
        <?php endif; ?>
        
        <div class="variable-preview">
            <strong>Proposed Variable:</strong> 
            <code>$<?php echo htmlspecialchars($item['suggested_variable']); ?></code>
            <br>
            <strong>Context:</strong> <?php echo htmlspecialchars($item['context']); ?>
            <br>
            <strong>Element Type:</strong> <?php echo htmlspecialchars($item['element_type']); ?>
        </div>
        
        <div class="before-after">
            <div>
                <h6>Before (Hardcoded):</h6>
                <div class="before-content">
&lt;<?php echo htmlspecialchars($item['element_type']); ?>&gt;<?php echo htmlspecialchars($item['original_content']); ?>&lt;/<?php echo htmlspecialchars($item['element_type']); ?>&gt;
                </div>
            </div>
            <div>
                <h6>After (Settings Variable):</h6>
                <div class="after-content">
&lt;<?php echo htmlspecialchars($item['element_type']); ?>&gt;&lt;?php echo htmlspecialchars($<?php echo $item['suggested_variable']; ?>); ?&gt;&lt;/<?php echo htmlspecialchars($item['element_type']); ?>&gt;
                </div>
            </div>
        </div>
        
        <form method="POST" id="form_<?php echo $integration_id; ?>">
            <input type="hidden" name="integration_id" value="<?php echo $integration_id; ?>">
            <input type="hidden" name="page" value="<?php echo htmlspecialchars($page_name); ?>">
            <input type="hidden" name="original_content" value="<?php echo htmlspecialchars($item['original_content']); ?>">
            <input type="hidden" name="variable_name" value="<?php echo htmlspecialchars($item['suggested_variable']); ?>">
            <input type="hidden" name="scope" value="<?php echo htmlspecialchars($item['scope']); ?>">
            <input type="hidden" name="action" id="action_<?php echo $integration_id; ?>" value="">
            
            <div class="d-flex gap-2 mt-3">
                <button type="button" class="btn btn-verify" onclick="approveIntegration('<?php echo $integration_id; ?>')">
                    <i class="bi bi-check-lg"></i> Approve Integration
                </button>
                <button type="button" class="btn btn-discard" onclick="discardIntegration('<?php echo $integration_id; ?>')">
                    <i class="bi bi-x-lg"></i> Discard
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="editVariableName('<?php echo $integration_id; ?>')">
                    <i class="bi bi-pencil-square"></i> Edit Variable Name
                </button>
            </div>
        </form>
    </div>
    <?php
}

// Handle integration actions
if ($_POST && $action === 'approve') {
    // This would implement the actual integration with full safety checks
    // For now, just log the action
    $integration_history[] = [
        'id' => uniqid(),
        'timestamp' => time(),
        'page' => $_POST['page'],
        'original_content' => $_POST['original_content'],
        'variable_name' => $_POST['variable_name'],
        'scope' => $_POST['scope'],
        'status' => 'pending_review' // Would be 'success' after actual implementation
    ];
    
    file_put_contents($processed_file, json_encode($integration_history, JSON_PRETTY_PRINT));
    
    echo '<div class="alert alert-success">Integration approved and queued for processing!</div>';
}
?>
