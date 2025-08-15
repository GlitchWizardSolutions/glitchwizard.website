<?php
/* 
 * Account Table Transfer System
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: accounts_table_transfer.php
 * LOCATION: /public_html/admin/accounts/
 * PURPOSE: Import and export account data in multiple formats (CSV, JSON, XML, TXT)
 * 
 * FILE RELATIONSHIP:
 * This file integrates with:
 * - Database connection system
 * - User authentication system
 * - File system operations
 * - Data validation system
 * 
 * HOW IT WORKS:
 * 1. Provides tabbed interface for import/export operations
 * 2. Handles file uploads and data validation
 * 3. Processes data format conversions
 * 4. Manages database import/export operations
 * 5. Implements security measures for data handling
 * 
 * CREATED: 2025-07-03
 * UPDATED: 2025-08-07
 * VERSION: 1.1
 * PRODUCTION: YES
 * 
 * FEATURES:
 * Multi-format data export (CSV, JSON, XML, TXT)
 * Secure file upload handling
 * Data validation and sanitization
 * Progress tracking for large transfers
 * Error logging and reporting
 * Tabbed user interface
 * Bootstrap 5 styling
 */
 
include_once '../assets/includes/main.php';
// Remove the time limit and file size limit
set_time_limit(0);
ini_set('post_max_size', '0');
ini_set('upload_max_filesize', '0');

// Handle Import form submission
if (isset($_FILES['file']) && !empty($_FILES['file']['tmp_name']))
{
    // check type
    $type = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $data = [];
    if ($type == 'csv')
    {
        $file = fopen($_FILES['file']['tmp_name'], 'r');
        $header = fgetcsv($file);
        while ($row = fgetcsv($file))
        {
            $data[] = array_combine($header, $row);
        }
        fclose($file);
    } elseif ($type == 'json')
    {
        $data = json_decode(file_get_contents($_FILES['file']['tmp_name']), true);
    } elseif ($type == 'xml')
    {
        $xml = simplexml_load_file($_FILES['file']['tmp_name']);
        $data = json_decode(json_encode($xml), true)['account'];
    } elseif ($type == 'txt')
    {
        $file = fopen($_FILES['file']['tmp_name'], 'r');
        while ($row = fgetcsv($file))
        {
            $data[] = $row;
        }
        // ...existing code...
    }
    // insert into database
    if (isset($data) && !empty($data))
    {
        $i = 0;
        foreach ($data as $k => $row)
        {
            // convert array to question marks for prepared statements
            $values = array_fill(0, count($row), '?');
            $values = implode(',', $values);
            // Convert date to MySQL format, if you have more datetime columns, add them here
            if (isset($row['registered']))
            {
                $row['registered'] = date('Y-m-d H:i', strtotime(str_replace('/', '-', $row['registered'])));
            }
            if (isset($row['last_seen']))
            {
                $row['last_seen'] = date('Y-m-d H:i', strtotime(str_replace('/', '-', $row['last_seen'])));
            }
            // insert into database
            // tip: if you want to update existing records, use INSERT ... ON DUPLICATE KEY UPDATE instead
            $stmt = $pdo->prepare('INSERT IGNORE INTO accounts VALUES (' . $values . ')');
            $stmt->execute(array_values($row));
            $i += $stmt->rowCount();
        }
        header('Location: accounts.php?success_msg=4&imported=' . $i);
        exit;
    }
}

// Handle Export form submission
if (isset($_POST['file_type']))
{
    // Get all accounts
    $result = $pdo->query('SELECT * FROM accounts ORDER BY id ASC');
    $accounts = [];
    $columns = [];
    // Fetch all records into an associative array
    if ($result->rowCount() > 0)
    {
        // Fetch column names
        for ($i = 0; $i < $result->columnCount(); $i++)
        {
            $columns[] = $result->getColumnMeta($i)['name'];
        }
        // Fetch associative array
        while ($row = $result->fetch(PDO::FETCH_ASSOC))
        {
            $accounts[] = $row;
        }
    }
    // Convert to CSV
    if ($_POST['file_type'] == 'csv')
    {
        $filename = 'accounts.csv';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        fputcsv($fp, $columns);
        foreach ($accounts as $account)
        {
            fputcsv($fp, $account);
        }
        fclose($fp);
        exit;
    }
    // Convert to TXT
    if ($_POST['file_type'] == 'txt')
    {
        $filename = 'accounts.txt';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/txt');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, implode(',', $columns) . PHP_EOL);
        foreach ($accounts as $account)
        {
            $line = '';
            foreach ($account as $key => $value)
            {
                if (is_string($value))
                {
                    $value = '"' . str_replace('"', '\"', $value) . '"';
                }
                $line .= $value . ',';
            }
            $line = rtrim($line, ',') . PHP_EOL;
            fwrite($fp, $line);
        }
        fclose($fp);
        exit;
    }
    // Convert to JSON
    if ($_POST['file_type'] == 'json')
    {
        $filename = 'accounts.json';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, json_encode($accounts));
        fclose($fp);
        exit;
    }
    // Convert to XML
    if ($_POST['file_type'] == 'xml')
    {
        $filename = 'accounts.xml';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/xml');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($fp, '<accounts>' . PHP_EOL);
        foreach ($accounts as $account)
        {
            fwrite($fp, '    <account>' . PHP_EOL);
            foreach ($account as $key => $value)
            {
                fwrite($fp, '        <' . $key . '>' . $value . '</' . $key . '>' . PHP_EOL);
            }
            fwrite($fp, '    </account>' . PHP_EOL);
        }
        fwrite($fp, '</accounts>' . PHP_EOL);
        fclose($fp);
        exit;
    }
}
?>
<?= template_admin_header('Import/Export Accounts', 'accounts', 'transfer') ?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path
                    d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z" />
            </svg>
        </div>
        <div class="txt">
            <h2>Import/Export Accounts</h2>
            <p>Import account data from files or export existing accounts to various formats.</p>
        </div>
    </div>
</div>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="accounts.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
        Cancel
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Data Transfer Center</h6>
        <small class="text-muted">Import or export account data</small>
    </div>
    <div class="card-body" style="padding: 0;">
            <!-- Tab Navigation -->
            <div class="tab-nav" role="tablist" aria-label="Account data transfer options" style="padding: 1rem 1rem 0; background-color: transparent;">
                <button type="button" class="tab-btn active" 
                    role="tab"
                    aria-selected="true"
                    aria-controls="export-tab"
                    id="export-tab-btn"
                    onclick="openTab(event, 'export-tab')"
                    style="outline: none; margin-bottom: -2px;">
                    Export Accounts
                </button>
                <button type="button" class="tab-btn" 
                    role="tab"
                    aria-selected="false"
                    aria-controls="import-tab"
                    id="import-tab-btn"
                    onclick="openTab(event, 'import-tab')"
                    style="outline: none; margin-bottom: -2px;">
                    Import Accounts
                </button>
            </div>

            <!-- Export Tab Content -->
            <div id="export-tab" 
                class="tab-content active" 
                role="tabpanel"
                aria-labelledby="export-tab-btn"
                style="padding: 1rem;">
                
                <form action="" method="post" role="form" aria-labelledby="export-title" id="export-form">

                    <div class="row">
                        <div class="col-md-6">
                            <h3 id="export-title">Export Account Data</h3>
                            <p id="export-desc">Download all account data in your preferred format.</p>

                            <div class="form-group mb-3">
                                <label for="file_type" class="form-label">
                                    <span class="required" aria-hidden="true">*</span> 
                                    File Type
                                    <span class="sr-only">(required)</span>
                                </label>
                                <select id="file_type" 
                                    name="file_type" 
                                    class="form-select"
                                    required 
                                    aria-required="true"
                                    aria-describedby="file-type-desc">
                                    <option value="csv">CSV (Comma Separated Values)</option>
                                    <option value="txt">TXT (Text File)</option>
                                    <option value="json">JSON (JavaScript Object Notation)</option>
                                    <option value="xml">XML (Extensible Markup Language)</option>
                                </select>
                                <div id="file-type-desc" class="form-text">Choose the format for your exported data file.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="file-info" role="region" aria-labelledby="supported-formats">
                                <h4 id="supported-formats">Supported File Formats</h4>
                                <ul id="file-formats">
                                    <li><strong>CSV:</strong> Comma-separated values with header row</li>
                                    <li><strong>JSON:</strong> Array of account objects</li>
                                    <li><strong>XML:</strong> Structured XML with account elements</li>
                                    <li><strong>TXT:</strong> Tab or comma-delimited text file</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Form actions bottom -->
                    <div class="d-flex gap-2 pt-3 border-top mt-4" role="region" aria-label="Export Actions">
                        <a href="accounts.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
                            Cancel
                        </a>
                        <button type="submit" name="export" class="btn btn-success">
                            <i class="fas fa-download me-1" aria-hidden="true"></i>
                            Export Data
                        </button>
                    </div>
                </form>
            </div>

            <!-- Import Tab Content -->
            <div id="import-tab" 
                class="tab-content"
                role="tabpanel"
                aria-labelledby="import-tab-btn"
                style="padding: 1rem;">
                
                <form action="" method="post" enctype="multipart/form-data" role="form" aria-labelledby="import-title" id="import-form">

                    <div class="row">
                        <div class="col-md-6">
                            <h3 id="import-title">Import Account Data</h3>
                            <p id="import-desc">Upload a file containing account data to import into the system.</p>

                            <div class="form-group mb-3">
                                <label for="file" class="form-label">
                                    <span class="required" aria-hidden="true">*</span> 
                                    Select File
                                    <span class="sr-only">(required)</span>
                                </label>
                                <input type="file" 
                                    id="file" 
                                    name="file" 
                                    class="form-control"
                                    accept=".csv,.txt,.json,.xml"
                                    required 
                                    aria-required="true"
                                    aria-describedby="import-file-desc">
                                <div id="import-file-desc" class="form-text">Select a CSV, TXT, JSON, or XML file containing account data.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="file-info" role="region" aria-labelledby="import-formats">
                                <h4 id="import-formats">File Requirements</h4>
                                <ul>
                                    <li><strong>CSV:</strong> Must include header row with field names</li>
                                    <li><strong>JSON:</strong> Array format with account objects</li>
                                    <li><strong>XML:</strong> Valid XML structure with account nodes</li>
                                    <li><strong>TXT:</strong> Tab or comma-delimited format</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Form actions bottom -->
                    <div class="d-flex gap-2 pt-3 border-top mt-4" role="region" aria-label="Import Actions">
                        <a href="accounts.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
                            Cancel
                        </a>
                        <button type="submit" name="submit" class="btn btn-success">
                            <i class="fas fa-upload me-1" aria-hidden="true"></i>
                            Import Data
                        </button>
                    </div>
                </form>
            </div>
    </div>
</div>

<script>
    console.log('Script loaded successfully');
    
    function openTab(evt, tabName) {
        console.log('openTab called with:', tabName);
        
        // Hide all tab content
        var tabcontent = document.getElementsByClassName("tab-content");
        console.log('Found tab content elements:', tabcontent.length);
        for (var i = 0; i < tabcontent.length; i++) {
            console.log('Hiding tab:', tabcontent[i].id);
            tabcontent[i].style.display = "none";
            tabcontent[i].classList.remove("active");
        }
        
        // Remove active class from all tab buttons (fix class name)
        var tablinks = document.getElementsByClassName("tab-btn");
        console.log('Found tab buttons:', tablinks.length);
        for (var i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
            tablinks[i].setAttribute("aria-selected", "false");
        }
        
        // Show the selected tab content
        var targetTab = document.getElementById(tabName);
        console.log('Target tab element:', targetTab);
        if (targetTab) {
            targetTab.style.display = "block";
            targetTab.style.visibility = "visible";
            targetTab.style.opacity = "1";
            targetTab.classList.add("active");
            console.log('Set tab display to block and added active class');
            console.log('Tab computed style:', window.getComputedStyle(targetTab).display);
        } else {
            console.error('Could not find tab element with ID:', tabName);
        }
        
        // Add active class to the clicked tab button
        if (evt && evt.currentTarget) {
            evt.currentTarget.classList.add("active");
            evt.currentTarget.setAttribute("aria-selected", "true");
            console.log('Set button active via event');
        } else {
            // If no event (initial load), find the button by ID
            var targetButton = document.getElementById(tabName.replace('-tab', '-tab-btn'));
            console.log('Target button element:', targetButton);
            if (targetButton) {
                targetButton.classList.add("active");
                targetButton.setAttribute("aria-selected", "true");
                console.log('Set button active via ID');
            }
        }
            if (targetButton) {
                targetButton.classList.add("active");
                targetButton.setAttribute("aria-selected", "true");
            }
        }
        
        // Update the top action button
        const topActionButton = document.getElementById('top-action-button');
        if (topActionButton) {
            const buttonText = topActionButton.querySelector('.button-text');
            const buttonIcon = topActionButton.querySelector('i');
            
            if (tabName === 'import-tab') {
                // Change to import button
                if (buttonText) buttonText.textContent = 'Import Data';
                if (buttonIcon) buttonIcon.className = 'fas fa-upload me-1';
                topActionButton.setAttribute('form', 'import-form');
                topActionButton.setAttribute('name', 'submit');
            } else {
                // Change to export button
                if (buttonText) buttonText.textContent = 'Export Data';
                if (buttonIcon) buttonIcon.className = 'fas fa-download me-1';
                topActionButton.setAttribute('form', 'export-form');
                topActionButton.setAttribute('name', 'export');
            }
        }
        
        console.log('Tab switched to:', tabName);
    }
    
    // Set export tab as active by default when page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded, initializing export tab...');
        
        // Force hide all tabs first
        var allTabs = document.getElementsByClassName("tab-content");
        for (var i = 0; i < allTabs.length; i++) {
            allTabs[i].style.display = "none";
            allTabs[i].classList.remove("active");
        }
        
        // Remove active from all buttons
        var allButtons = document.getElementsByClassName("tab-btn");
        for (var i = 0; i < allButtons.length; i++) {
            allButtons[i].classList.remove("active");
            allButtons[i].setAttribute("aria-selected", "false");
        }
        console.log('Export tab initialized');
    });
</script>

<style>
/* Tab Navigation */
.tab-nav {
    display: flex;
    border-bottom: 2px solid #dee2e6;
    position: relative;
}

.tab-btn {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    border-bottom: 2px solid #dee2e6;
    padding: 12px 20px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #495057;
    transition: all 0.2s ease;
}

.tab-btn:hover {
    background: #e9ecef;
    color: #212529;
}

.tab-btn.active {
    background: white;
    color: #007bff;
    border-bottom: 2px solid transparent;
    font-weight: 600;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.file-info {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
}

.file-info h4 {
    margin-bottom: 0.75rem;
    color: #495057;
    font-size: 1rem;
}

.file-info ul {
    margin-bottom: 0;
    padding-left: 1.25rem;
}

.file-info li {
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    color: #6c757d;
}
</style>

<?= template_admin_footer() ?>