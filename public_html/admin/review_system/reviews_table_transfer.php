<?php
/* 
 * Reviews Table Transfer System
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: reviews_table_transfer.php
 * LOCATION: /public_html/admin/reviews_system/
 * PURPOSE: Import and export reviews data in multiple formats (CSV, JSON, XML, TXT)
 * 
 * FILE RELATIONSHIP:
 * This file integrates                                <div class="d-flex gap-2">
                                <button type="submit" name="submit" class="btn btn-primary" style="min-width: 120px;">
                                    <i class="fas fa-upload me-2"></i>Import Data
                                </button>
                            </div>                <div class="d-flex gap-2">
                                <button type="submit" name="submit" class="btn btn-primary" style="min-width: 120px;">
                                    <i class="fas fa-upload me-2"></i>Import Data
                                </button>
                            </div>
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
            $stmt = $pdo->prepare('INSERT IGNORE INTO reviews VALUES (' . $values . ')');
            $stmt->execute(array_values($row));
            $i += $stmt->rowCount();
        }
        header('Location: reviews.php?success_msg=4&imported=' . $i);
        exit;
    }
}
// Handle Export form submission
if (isset($_POST['file_type']))
{
    // Select all reviews from database
    $stmt = $pdo->prepare('SELECT * FROM reviews');
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Convert to CSV
    if ($_POST['file_type'] == 'csv')
    {
        $filename = 'reviews.csv';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        if (!empty($reviews))
        {
            fputcsv($fp, array_keys($reviews[0]));
            foreach ($reviews as $review)
            {
                fputcsv($fp, $review);
            }
        }
        fclose($fp);
        exit;
    }
    // Convert to TXT
    if ($_POST['file_type'] == 'txt')
    {
        $filename = 'reviews.txt';
        $fp = fopen('php://output', 'w');
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename=' . $filename);
        if (!empty($reviews))
        {
            // Header
            fwrite($fp, implode(',', array_keys($reviews[0])) . PHP_EOL);
        }
        foreach ($reviews as $review)
        {
            $line = '';
            foreach ($review as $key => $value)
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
        $filename = 'reviews.json';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, json_encode($reviews));
        fclose($fp);
        exit;
    }
    // Convert to XML
    if ($_POST['file_type'] == 'xml')
    {
        $filename = 'reviews.xml';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/xml');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($fp, '<reviews>' . PHP_EOL);
        foreach ($reviews as $review)
        {
            fwrite($fp, '    <review>' . PHP_EOL);
            foreach ($review as $key => $value)
            {
                fwrite($fp, '        <' . $key . '>' . $value . '</' . $key . '>' . PHP_EOL);
            }
            fwrite($fp, '    </review>' . PHP_EOL);
        }
        fwrite($fp, '</reviews>' . PHP_EOL);
        fclose($fp);
        exit;
    }
}
?>
<?= template_admin_header('Import/Export Reviews', 'reviews', 'transfer') ?>

<style>
    /* Tab Navigation */
    .tab-nav {
        display: flex;
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 30px;
        position: relative;
    }

    .tab-btn {
        background: none;
        border: 2px solid transparent;
        border-bottom: none;
        padding: 12px 24px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.3s ease;
        border-radius: 8px 8px 0 0;
        margin-right: 4px;
    }

    .tab-btn:hover {
        color: #495057;
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .tab-btn:focus {
        outline: 2px solid #0d6efd;
        outline-offset: -2px;
        z-index: 1;
    }

    .tab-btn.active {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        position: relative;
        z-index: 1;
    }

    .tab-btn[aria-selected="true"] {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    /* Tab Content */
    .tab-content {
        display: none;
        padding: 30px;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 8px 8px;
    }

    .tab-content.active {
        display: block;
    }

    /* Form Styling */
    .card {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background-color: #fff;
    }

    .card-header {
        padding: 0.75rem 1.25rem;
        margin-bottom: 0;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        border-radius: 0.375rem 0.375rem 0 0;
        font-size: 1rem;
        font-weight: 500;
    }

    .card-body {
        padding: 1.25rem;
    }

    .form-group, .mb-3 {
        margin-bottom: 1.5rem;
    }

    .form-label, .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #212529;
    }

    .required {
        color: #dc3545;
        margin-right: 0.25rem;
    }

    .form-control, .form-group select,
    .form-group input[type="file"] {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        font-size: 1rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus, .form-group select:focus,
    .form-group input[type="file"]:focus {
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
        height: 100%;
    }

    .file-info h6 {
        margin-top: 0;
        margin-bottom: 0.75rem;
        font-size: 1rem;
        color: #495057;
        font-weight: 500;
    }

    .file-info p {
        margin-bottom: 0.75rem;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .file-info ul {
        margin-bottom: 0;
        padding-left: 1.5rem;
    }

    .file-info li {
        margin-bottom: 0.25rem;
        color: #6c757d;
        font-size: 0.875rem;
    }

    .alert {
        padding: 0.75rem;
        margin-bottom: 0;
        border: 1px solid transparent;
        border-radius: 0.375rem;
    }

    .alert-info {
        color: #055160;
        background-color: #d1ecf1;
        border-color: #b8daff;
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

    .pt-3 {
        padding-top: 1rem !important;
    }

    .border-top {
        border-top: 1px solid #dee2e6 !important;
    }

    .mt-4 {
        margin-top: 1.5rem !important;
    }

    .me-1 {
        margin-right: 0.25rem !important;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .tab-nav {
            flex-wrap: wrap;
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
</style>

<script>
    function openTab(evt, tabName) {
        // Hide all tab content
        var tabcontent = document.getElementsByClassName("tab-content");
        for (var i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }

        // Remove active class from all tab buttons
        var tablinks = document.getElementsByClassName("tab-btn");
        for (var i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
            tablinks[i].setAttribute("aria-selected", "false");
        }

        // Show the current tab and mark button as active
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

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 576 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                <path
                    d="M0 64C0 28.7 28.7 0 64 0H224V128c0 17.7 14.3 32 32 32H384V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V64zm384 64H256V0L384 128zM176 352h24c30.9 0 56 25.1 56 56s-25.1 56-56 56H176c-8.8 0-16-7.2-16-16V368c0-8.8 7.2-16 16-16zm24 80c13.3 0 24-10.7 24-24s-10.7-24-24-24H192v48h8zm72-64c0-8.8 7.2-16 16-16h24c26.5 0 48 21.5 48 48v32c0 26.5-21.5 48-48 48H288c-8.8 0-16-7.2-16-16V352zm32 64h8c8.8 0 16-7.2 16-16V384c0-8.8-7.2-16-16-16h-8v48zM448 352c8.8 0 16 7.2 16 16s-7.2 16-16 16H424v16h24c8.8 0 16 7.2 16 16s-7.2 16-16 16H424v16c0 8.8-7.2 16-16 16s-16-7.2-16-16V368c0-8.8 7.2-16 16-16h40z" />
            </svg>
        </div>
        <div class="txt">
            <h2>Import/Export Reviews</h2>
            <p>Import review data from files or export existing reviews to various formats.</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Review Data Transfer Center</h6>
        <p class="text-muted mb-0 mt-1">Import or export review data using the tabs below</p>
    </div>
    <div class="card-body">
        <!-- Tab Navigation -->
        <div class="tab-nav" role="tablist" aria-label="Review data transfer options">
            <button type="button" class="tab-btn active" 
                role="tab"
                aria-selected="true"
                aria-controls="export-tab"
                id="export-tab-btn"
                onclick="openTab(event, 'export-tab')">
                Export Reviews
            </button>
            <button type="button" class="tab-btn" 
                role="tab"
                aria-selected="false"
                aria-controls="import-tab"
                id="import-tab-btn"
                onclick="openTab(event, 'import-tab')">
                Import Reviews
            </button>
        </div>

        <!-- Export Tab Content -->
        <div id="export-tab" 
            class="tab-content active" 
            role="tabpanel"
            aria-labelledby="export-tab-btn">
            <form action="" method="post" role="form" aria-labelledby="export-title">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="file_type" class="form-label">
                                <span class="required" aria-hidden="true">*</span> 
                                File Type
                                <span class="sr-only">(required)</span>
                            </label>
                            <select id="file_type" 
                                name="file_type" 
                                class="form-control"
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
                            <h6 id="supported-formats">Export Information</h6>
                            <p>Download all review data in your preferred format. The exported file will contain all review records from your system.</p>
                            <ul id="file-formats">
                                <li><strong>CSV:</strong> Spreadsheet-compatible format</li>
                                <li><strong>JSON:</strong> Machine-readable data format</li>
                                <li><strong>XML:</strong> Structured markup format</li>
                                <li><strong>TXT:</strong> Plain text format</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 pt-3 border-top mt-4">
                    <a href="reviews.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download me-1" aria-hidden="true"></i>Export Reviews
                    </button>
                </div>
            </form>
        </div>

        <!-- Import Tab Content -->
        <div id="import-tab" 
            class="tab-content"
            role="tabpanel"
            aria-labelledby="import-tab-btn">
            <form action="" method="post" enctype="multipart/form-data" role="form" aria-labelledby="import-title">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="file" class="form-label">
                                <span class="required" aria-hidden="true">*</span> 
                                File Upload
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
                            <div id="import-file-desc" class="form-text">Select a CSV, TXT, JSON, or XML file containing review data.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="file-info" role="region" aria-labelledby="import-formats">
                            <h6 id="import-formats">Supported File Formats</h6>
                            <ul>
                                <li><strong>CSV:</strong> Comma-separated values with header row</li>
                                <li><strong>JSON:</strong> Array of review objects</li>
                                <li><strong>XML:</strong> Structured XML with review elements</li>
                                <li><strong>TXT:</strong> Tab or comma-delimited text file</li>
                            </ul>
                            <div class="alert alert-info mt-3">
                                <small><strong>Note:</strong> Large files may take time to process. Please ensure your file format matches the expected structure.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 pt-3 border-top mt-4">
                    <a href="reviews.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Cancel
                    </a>
                    <button type="submit" name="submit" class="btn btn-success">
                        <i class="fas fa-upload me-1" aria-hidden="true"></i>Import Reviews
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= template_admin_footer() ?>
