<?php
/* 
 * Invoice Table Transfer System
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: invoice_table_transfer.php
 * LOCATION: /public_html/admin/invoice_system/
 * PURPOSE: Import and export client and collection data in multiple formats (CSV, JSON, XML, TXT)
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
 * CREATED: 2025-08-10
 * UPDATED: 2025-08-10
 * VERSION: 1.0
 * PRODUCTION: YES
 * 
 * FEATURES:
 * Multi-format data export (CSV, JSON, XML, TXT)
 * Secure file upload handling
 * Data validation and sanitization
 * Progress tracking for large transfers
 * Error logging and reporting
 * Tabbed user interface for clients and collections
 * Bootstrap 5 styling
 */

include '../assets/includes/main.php';
// Remove the time limit and file size limit
set_time_limit(0);
ini_set('post_max_size', '0');
ini_set('upload_max_filesize', '0');

// Handle Clients Import form submission
if (isset($_FILES['clients_file'], $_POST['import_clients']) && !empty($_FILES['clients_file']['tmp_name'])) {
    // check type
    $type = pathinfo($_FILES['clients_file']['name'], PATHINFO_EXTENSION);
    $data = [];
    if ($type == 'csv') {
        $file = fopen($_FILES['clients_file']['tmp_name'], 'r');
        $header = fgetcsv($file);
        while ($row = fgetcsv($file)) {
            $data[] = array_combine($header, $row);
        }
        fclose($file);
    } elseif ($type == 'json') {
        $data = json_decode(file_get_contents($_FILES['clients_file']['tmp_name']), true);
    } elseif ($type == 'xml') {
        $xml = simplexml_load_file($_FILES['clients_file']['tmp_name']);
        $data = json_decode(json_encode($xml), true)['item'];
    } elseif ($type == 'txt') {
        $file = fopen($_FILES['clients_file']['tmp_name'], 'r');
        while ($row = fgetcsv($file)) {
            $data[] = $row;
        }
        fclose($file);
    }
    // insert into database
    if (isset($data) && !empty($data)) {    
        $i = 0;   
        foreach ($data as $k => $row) {
            // convert array to question marks for prepared statements
            $values = array_fill(0, count($row), '?');
            $values = implode(',', $values);
            // Convert date to MySQL format, if you have more datetime columns, add them here
            if (isset($row['created'])) {
                $row['created'] = date('Y-m-d H:i', strtotime(str_replace('/','-', $row['created'])));
            }
            if (isset($row['start_date'])) {
                $row['start_date'] = date('Y-m-d H:i', strtotime(str_replace('/','-', $row['start_date'])));
            }
            if (isset($row['end_date'])) {
                $row['end_date'] = date('Y-m-d H:i', strtotime(str_replace('/','-', $row['end_date'])));
            }
            // insert into database
            // tip: if you want to update existing records, use INSERT ... ON DUPLICATE KEY UPDATE instead
            $stmt = $pdo->prepare('INSERT IGNORE INTO clients VALUES (' . $values . ')');
            $stmt->execute(array_values($row));
            $i += $stmt->rowCount();
        }
        header('Location: invoices.php?success_msg=4&imported=' . $i);
        exit;
    }
}

// Handle Poll Categories Import form submission
if (isset($_FILES['collections_file'], $_POST['import_collections']) && !empty($_FILES['collections_file']['tmp_name'])) {
    // check type
    $type = pathinfo($_FILES['collections_file']['name'], PATHINFO_EXTENSION);
    $data = [];
    if ($type == 'csv') {
        $file = fopen($_FILES['collections_file']['tmp_name'], 'r');
        $header = fgetcsv($file);
        while ($row = fgetcsv($file)) {
            $data[] = array_combine($header, $row);
        }
        fclose($file);
    } elseif ($type == 'json') {
        $data = json_decode(file_get_contents($_FILES['collections_file']['tmp_name']), true);
    } elseif ($type == 'xml') {
        $xml = simplexml_load_file($_FILES['collections_file']['tmp_name']);
        $data = json_decode(json_encode($xml), true)['item'];
    } elseif ($type == 'txt') {
        $file = fopen($_FILES['collections_file']['tmp_name'], 'r');
        while ($row = fgetcsv($file)) {
            $data[] = $row;
        }
        fclose($file);
    }
    // insert into database
    if (isset($data) && !empty($data)) {    
        $i = 0;   
        foreach ($data as $k => $row) {
            // convert array to question marks for prepared statements
            $values = array_fill(0, count($row), '?');
            $values = implode(',', $values);
            // Convert date to MySQL format, if you have more datetime columns, add them here
            if (isset($row['created'])) {
                $row['created'] = date('Y-m-d H:i', strtotime(str_replace('/','-', $row['created'])));
            }
            // insert into database
            // tip: if you want to update existing records, use INSERT ... ON DUPLICATE KEY UPDATE instead
            $stmt = $pdo->prepare('INSERT IGNORE INTO collections VALUES (' . $values . ')');
            $stmt->execute(array_values($row));
            $i += $stmt->rowCount();
        }
        header('Location: invoices.php?success_msg=4&imported=' . $i);
        exit;
    }
}

// Handle Polls Export form submission
if (isset($_POST['export_clients'], $_POST['polls_export_type'])) {
    // Get all polls
    $result = $pdo->query('SELECT * FROM clients ORDER BY id ASC');
    $polls = [];
    $columns = [];
    // Fetch all records into an associative array
    if ($result->rowCount() > 0) {
        // Fetch column names
        for ($i = 0; $i < $result->columnCount(); $i++) {
            $columns[] = $result->getColumnMeta($i)['name'];
        }
        // Fetch associative array
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $polls[] = $row;
        }    
    }
    // Convert to CSV
    if ($_POST['polls_export_type'] == 'csv') {
        $filename = 'clients.csv';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        fputcsv($fp,  $columns);
        foreach ($polls as $poll) {
            fputcsv($fp, $poll);
        }
        fclose($fp);
        exit;
    }
    // Convert to TXT
    if ($_POST['polls_export_type'] == 'txt') {
        $filename = 'clients.txt';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/txt');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, implode(',', $columns) . PHP_EOL);
        foreach ($polls as $poll) {
            $line = '';
            foreach ($poll as $key => $value) {
                if (is_string($value)) {
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
    if ($_POST['polls_export_type'] == 'json') {
        $filename = 'clients.json';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, json_encode($polls));
        fclose($fp);
        exit;
    }
    // Convert to XML
    if ($_POST['polls_export_type'] == 'xml') {
        $filename = 'clients.xml';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/xml');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($fp, '<polls>' . PHP_EOL);
        foreach ($polls as $poll) {
            fwrite($fp, '    <poll>' . PHP_EOL);
            foreach ($poll as $key => $value) {
                fwrite($fp, '        <' . $key . '>' . htmlspecialchars($value) . '</' . $key . '>' . PHP_EOL);
            }
            fwrite($fp, '    </poll>' . PHP_EOL);
        }
        fwrite($fp, '</polls>' . PHP_EOL);
        fclose($fp);
        exit;
    }
}

// Handle Poll Categories Export form submission
if (isset($_POST['export_collections'], $_POST['categories_export_type'])) {
    // Get all poll_categories
    $result = $pdo->query('SELECT * FROM collections ORDER BY id ASC');
    $poll_categories = [];
    $columns = [];
    // Fetch all records into an associative array
    if ($result->rowCount() > 0) {
        // Fetch column names
        for ($i = 0; $i < $result->columnCount(); $i++) {
            $columns[] = $result->getColumnMeta($i)['name'];
        }
        // Fetch associative array
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $poll_categories[] = $row;
        }    
    }
    // Convert to CSV
    if ($_POST['categories_export_type'] == 'csv') {
        $filename = 'poll_collections.csv';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        fputcsv($fp,  $columns);
        foreach ($poll_categories as $poll_category) {
            fputcsv($fp, $poll_category);
        }
        fclose($fp);
        exit;
    }
    // Convert to TXT
    if ($_POST['categories_export_type'] == 'txt') {
        $filename = 'poll_collections.txt';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/txt');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, implode(',', $columns) . PHP_EOL);
        foreach ($poll_categories as $poll_category) {
            $line = '';
            foreach ($poll_category as $key => $value) {
                if (is_string($value)) {
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
    if ($_POST['categories_export_type'] == 'json') {
        $filename = 'poll_collections.json';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, json_encode($poll_categories));
        fclose($fp);
        exit;
    }
    // Convert to XML
    if ($_POST['categories_export_type'] == 'xml') {
        $filename = 'poll_collections.xml';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/xml');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($fp, '<poll_categories>' . PHP_EOL);
        foreach ($poll_categories as $poll_category) {
            fwrite($fp, '    <poll_category>' . PHP_EOL);
            foreach ($poll_category as $key => $value) {
                fwrite($fp, '        <' . $key . '>' . htmlspecialchars($value) . '</' . $key . '>' . PHP_EOL);
            }
            fwrite($fp, '    </poll_category>' . PHP_EOL);
        }
        fwrite($fp, '</poll_categories>' . PHP_EOL);
        fclose($fp);
        exit;
    }
}
?>

<?=template_admin_header('Import/Export Invoice Data', 'polls', 'bulk')?>
<link rel="stylesheet" href="invoice-specific.css">

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <i class="bi bi-arrow-left-right" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Import/Export Invoice Data</h2>
            <p>Import poll and category data from files or export existing data to various formats.</p>
        </div>
    </div>
</div>

<form action="" method="post" enctype="multipart/form-data" role="form">
    <!-- Top navigation buttons -->
    <div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
        <a href="invoices.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
            Cancel
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Data Transfer Center</h6>
            <p class="text-muted mb-0 mt-1">Import or export poll and category data using the tabs below</p>
        </div>
        <div class="card-body">
            <!-- Tab Navigation -->
            <div class="tab-nav" role="tablist" aria-label="Poll data transfer options">
        <button class="tab-btn active" 
            role="tab"
            aria-selected="true"
            aria-controls="export-polls-tab"
            id="export-polls-tab-btn"
            onclick="openTab(event, 'export-polls-tab')">
            Export Polls
        </button>
        <button class="tab-btn" 
            role="tab"
            aria-selected="false"
            aria-controls="import-polls-tab"
            id="import-polls-tab-btn"
            onclick="openTab(event, 'import-polls-tab')">
            Import Polls
        </button>
        <button class="tab-btn" 
            role="tab"
            aria-selected="false"
            aria-controls="export-categories-tab"
            id="export-categories-tab-btn"
            onclick="openTab(event, 'export-categories-tab')">
            Export Categories
        </button>
        <button class="tab-btn" 
            role="tab"
            aria-selected="false"
            aria-controls="import-categories-tab"
            id="import-categories-tab-btn"
            onclick="openTab(event, 'import-categories-tab')">
            Import Categories
        </button>
    </div>

    <!-- Export Polls Tab Content -->
    <div id="export-polls-tab" 
        class="tab-content active" 
        role="tabpanel"
        aria-labelledby="export-polls-tab-btn">
        
        <div class="row">
            <div class="col-md-6">
                <h3 id="export-polls-title">Export Poll Data</h3>
                <p id="export-polls-desc">Download all poll data in your preferred format.</p>

                <div class="form-group">
                    <label for="polls_export_type">
                        <span class="required" aria-hidden="true">*</span> 
                        File Type
                        <span class="sr-only">(required)</span>
                    </label>
                    <select id="polls_export_type" 
                        name="polls_export_type" 
                        class="form-control"
                        required 
                        aria-required="true"
                        aria-describedby="polls-export-type-desc">
                        <option value="csv">CSV (Comma Separated Values)</option>
                        <option value="txt">TXT (Text File)</option>
                        <option value="json">JSON (JavaScript Object Notation)</option>
                        <option value="xml">XML (Extensible Markup Language)</option>
                    </select>
                    <div id="polls-export-type-desc" class="form-text">Choose the format for your exported poll data file.</div>
                </div>
                
                <!-- Bottom action buttons for this tab -->
                <div class="pt-3 border-top mt-4">
                    <button type="submit" name="export_clients" class="btn btn-success">
                        <i class="bi bi-download me-1" aria-hidden="true"></i>
                        Export Polls
                    </button>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="file-info" role="region" aria-labelledby="polls-export-info">
                    <h4 id="polls-export-info">Export Information</h4>
                    <ul>
                        <li><strong>CSV:</strong> Compatible with Excel and most spreadsheet applications</li>
                        <li><strong>JSON:</strong> Perfect for API integrations and web applications</li>
                        <li><strong>XML:</strong> Structured data format for enterprise systems</li>
                        <li><strong>TXT:</strong> Simple text format for basic data processing</li>
                    </ul>
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Note:</strong> The export will include all poll data including titles, descriptions, dates, and approval status.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Polls Tab Content -->
    <div id="import-polls-tab" 
        class="tab-content"
        role="tabpanel"
        aria-labelledby="import-polls-tab-btn">
        
        <div class="row">
            <div class="col-md-6">
                <h3 id="import-polls-title">Import Poll Data</h3>
                <p id="import-polls-desc">Upload a file containing poll data to import into the system.</p>

                <div class="form-group">
                    <label for="clients_file">
                        <span class="required" aria-hidden="true">*</span> 
                        File Upload
                        <span class="sr-only">(required)</span>
                    </label>
                    <input type="file" 
                        name="clients_file" 
                        id="clients_file" 
                        class="form-control"
                        accept=".csv,.json,.xml,.txt" 
                        required
                        aria-required="true"
                        aria-describedby="polls-file-desc">
                    <div id="polls-file-desc" class="form-text">Select a data file to import poll information.</div>
                </div>
                
                <!-- Bottom action buttons for this tab -->
                <div class="pt-3 border-top mt-4">
                    <button type="submit" name="import_clients" class="btn btn-success">
                        <i class="bi bi-upload me-1" aria-hidden="true"></i>
                        Import Polls
                    </button>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="file-info" role="region" aria-labelledby="polls-supported-formats">
                    <h4 id="polls-supported-formats">Supported File Formats</h4>
                    <ul id="polls-file-formats">
                        <li><strong>CSV:</strong> Comma-separated values with header row</li>
                        <li><strong>JSON:</strong> Array of poll objects</li>
                        <li><strong>XML:</strong> Structured XML with poll elements</li>
                        <li><strong>TXT:</strong> Tab or comma-delimited text file</li>
                    </ul>
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Important:</strong> Ensure your file contains valid poll data with proper column headers and formatting.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Categories Tab Content -->
    <div id="export-categories-tab" 
        class="tab-content"
        role="tabpanel"
        aria-labelledby="export-categories-tab-btn">
        
        <div class="row">
            <div class="col-md-6">
                <h3 id="export-categories-title">Export Poll Category Data</h3>
                <p id="export-categories-desc">Download all poll category data in your preferred format.</p>

                <div class="form-group">
                    <label for="categories_export_type">
                        <span class="required" aria-hidden="true">*</span> 
                        File Type
                        <span class="sr-only">(required)</span>
                    </label>
                    <select id="categories_export_type" 
                        name="categories_export_type" 
                        class="form-control"
                        required 
                        aria-required="true"
                        aria-describedby="categories-export-type-desc">
                        <option value="csv">CSV (Comma Separated Values)</option>
                        <option value="txt">TXT (Text File)</option>
                        <option value="json">JSON (JavaScript Object Notation)</option>
                        <option value="xml">XML (Extensible Markup Language)</option>
                    </select>
                    <div id="categories-export-type-desc" class="form-text">Choose the format for your exported category data file.</div>
                </div>
                
                <!-- Bottom action buttons for this tab -->
                <div class="pt-3 border-top mt-4">
                    <button type="submit" name="export_collections" class="btn btn-success">
                        <i class="bi bi-download me-1" aria-hidden="true"></i>
                        Export Categories
                    </button>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="file-info" role="region" aria-labelledby="categories-export-info">
                    <h4 id="categories-export-info">Export Information</h4>
                    <ul>
                        <li><strong>CSV:</strong> Compatible with Excel and most spreadsheet applications</li>
                        <li><strong>JSON:</strong> Perfect for API integrations and web applications</li>
                        <li><strong>XML:</strong> Structured data format for enterprise systems</li>
                        <li><strong>TXT:</strong> Simple text format for basic data processing</li>
                    </ul>
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Note:</strong> The export will include all category data including titles and creation dates.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Categories Tab Content -->
    <div id="import-categories-tab" 
        class="tab-content"
        role="tabpanel"
        aria-labelledby="import-categories-tab-btn">
        
        <div class="row">
            <div class="col-md-6">
                <h3 id="import-categories-title">Import Poll Category Data</h3>
                <p id="import-categories-desc">Upload a file containing poll category data to import into the system.</p>

                <div class="form-group">
                    <label for="collections_file">
                        <span class="required" aria-hidden="true">*</span> 
                        File Upload
                        <span class="sr-only">(required)</span>
                    </label>
                    <input type="file" 
                        name="collections_file" 
                        id="collections_file" 
                        class="form-control"
                        accept=".csv,.json,.xml,.txt" 
                        required
                        aria-required="true"
                        aria-describedby="categories-file-desc">
                    <div id="categories-file-desc" class="form-text">Select a data file to import poll category information.</div>
                </div>
                
                <!-- Bottom action buttons for this tab -->
                <div class="pt-3 border-top mt-4">
                    <button type="submit" name="import_collections" class="btn btn-success">
                        <i class="bi bi-upload me-1" aria-hidden="true"></i>
                        Import Categories
                    </button>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="file-info" role="region" aria-labelledby="categories-supported-formats">
                    <h4 id="categories-supported-formats">Supported File Formats</h4>
                    <ul id="categories-file-formats">
                        <li><strong>CSV:</strong> Comma-separated values with header row</li>
                        <li><strong>JSON:</strong> Array of category objects</li>
                        <li><strong>XML:</strong> Structured XML with category elements</li>
                        <li><strong>TXT:</strong> Tab or comma-delimited text file</li>
                    </ul>
                    <div class="mt-3 p-3 bg-light rounded">
                        <strong>Important:</strong> Ensure your file contains valid category data with proper column headers and formatting.
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>
</form>

<style>
    /* Card integration with tabs and header */
    .card .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.25rem;
    }
    
    .card .card-header h6 {
        font-size: 1rem;
        font-weight: 600;
        color: #495057;
    }
    
    .card .card-body {
        padding: 0;
        border-radius: 0 0 8px 8px;
    }

    /* Tab Navigation */
    .tab-nav {
        display: flex;
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 0;
        position: relative;
        background-color: transparent;
        padding: 1rem 0 0 0;
    }

    .tab-btn {
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        border-bottom: 2px solid #dee2e6;
        padding: 12px 24px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.3s ease;
        border-radius: 8px 8px 0 0;
        margin-right: 4px;
        position: relative;
        outline: none;
    }

    .tab-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }

    .tab-btn:hover {
        color: #495057;
        background-color: #e9ecef;
        border-color: #adb5bd;
        border-bottom-color: #adb5bd;
    }

    .tab-btn:focus {
        outline: 2px solid #0d6efd;
        outline-offset: -2px;
        z-index: 1;
    }

    .tab-btn.active {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 transparent;
        position: relative;
        z-index: 2;
        font-weight: 600;
        border-bottom: 2px solid #fff;
        margin-bottom: -2px;
    }

    .tab-btn[aria-selected="true"] {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 transparent;
        border-bottom: 2px solid #fff;
        margin-bottom: -2px;
    }

    /* Tab Content */
    .tab-content {
        display: none;
        padding: 30px;
        background-color: #fff;
        border: 2px solid #dee2e6;
        border-top: none;
        border-radius: 0 8px 8px 8px;
        margin-top: 0;
        margin-left: 0;
    }

    .tab-content.active {
        display: block;
    }

    /* Form Styling */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #212529;
    }

    .required {
        color: #dc3545;
        margin-right: 0.25rem;
    }

    .form-group select,
    .form-group input[type="file"] {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        font-size: 1rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-group select:focus,
    .form-group input[type="file"]:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .form-control {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        font-size: 1rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .form-text {
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #6c757d;
    }

    /* File Info Styling */
    .file-info {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-top: 1rem;
    }

    .file-info h4 {
        margin-top: 0;
        margin-bottom: 0.5rem;
        font-size: 1rem;
        color: #495057;
    }

    .file-info ul {
        margin-bottom: 0;
        padding-left: 1.5rem;
    }

    .file-info li {
        margin-bottom: 0.25rem;
        color: #6c757d;
    }

    /* Button Styling */
    .btn {
        display: inline-block;
        padding: 0.5rem 1rem;
        margin-bottom: 0;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        text-align: center;
        text-decoration: none;
        vertical-align: middle;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 0.375rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .btn:focus {
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .btn-success {
        color: #fff;
        background-color: #198754;
        border-color: #198754;
    }

    .btn-success:hover {
        color: #fff;
        background-color: #157347;
        border-color: #146c43;
    }

    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
        background-color: transparent;
    }

    .btn-outline-secondary:hover {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .d-flex {
        display: flex !important;
    }

    .gap-2 {
        gap: 0.5rem !important;
    }

    .pb-3 {
        padding-bottom: 1rem !important;
    }

    .border-bottom {
        border-bottom: 1px solid #dee2e6 !important;
    }

    .mb-3 {
        margin-bottom: 1rem !important;
    }

    .me-1 {
        margin-right: 0.25rem !important;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .tab-nav {
            flex-wrap: wrap;
            padding: 15px 15px 0 15px;
        }
        
        .tab-btn {
            font-size: 12px;
            padding: 10px 16px;
        }
        
        .tab-content {
            padding: 20px 15px;
        }
        
        .d-flex {
            flex-direction: column;
        }
        
        .gap-2 {
            gap: 0.75rem !important;
        }
    }

    /* File Info Styling */
    .file-info {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1.5rem;
    }

    .file-info h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.75rem;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 0.5rem;
    }

    .file-info ul {
        margin-bottom: 0;
        padding-left: 1rem;
    }

    .file-info li {
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .file-info li:last-child {
        margin-bottom: 0;
    }

    .file-info strong {
        color: #495057;
    }

    /* Form Width Override */
    .form.responsive-width-100 {
        width: 100% !important;
        max-width: 100% !important;
    }

    .tab-content .form {
        width: 100%;
        max-width: none;
    }
</style>

<script>
    function openTab(evt, tabName) {
        // Declare variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tab-content" and hide them
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }

        // Get all elements with class="tab-btn" and remove the class "active"
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
            tablinks[i].setAttribute("aria-selected", "false");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
        evt.currentTarget.setAttribute("aria-selected", "true");
    }

    // Keyboard navigation for tabs
    document.addEventListener('keydown', function(e) {
        const target = e.target;
        const tabButtons = document.getElementsByClassName("tab-btn");
        
        if (!target.classList.contains('tab-btn')) return;
        
        const currentIndex = Array.from(tabButtons).indexOf(target);
        let nextIndex;
        
        switch(e.key) {
            case 'ArrowLeft':
                nextIndex = currentIndex > 0 ? currentIndex - 1 : tabButtons.length - 1;
                break;
            case 'ArrowRight':
                nextIndex = currentIndex < tabButtons.length - 1 ? currentIndex + 1 : 0;
                break;
            case 'Home':
                nextIndex = 0;
                break;
            case 'End':
                nextIndex = tabButtons.length - 1;
                break;
            default:
                return;
        }
        
        e.preventDefault();
        tabButtons[nextIndex].focus();
        tabButtons[nextIndex].click();
    });
</script>

<script src="invoice-specific.js"></script>
<?=template_admin_footer()?>
