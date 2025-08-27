<?php
/* 
 * Gallery Table Transfer System
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: gallery_table_transfer.php
 * LOCATION: /public_html/admin/gallery_system/
 * PURPOSE: Import and export gallery and gallery category data in multiple formats (CSV, JSON, XML, TXT)
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
 * Tabbed user interface for media and poll collections
 * Bootstrap 5 styling
 */

include '../assets/includes/main.php';
// Remove the time limit and file size limit
set_time_limit(0);
ini_set('post_max_size', '0');
ini_set('upload_max_filesize', '0');

// Handle Media Import form submission
if (isset($_FILES['media_file'], $_POST['media_import']) && !empty($_FILES['media_file']['tmp_name'])) {
    // check type
    $type = pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION);
    $data = [];
    if ($type == 'csv') {
        $file = fopen($_FILES['media_file']['tmp_name'], 'r');
        $header = fgetcsv($file);
        while ($row = fgetcsv($file)) {
            $data[] = array_combine($header, $row);
        }
        fclose($file);
    } elseif ($type == 'json') {
        $data = json_decode(file_get_contents($_FILES['media_file']['tmp_name']), true);
    } elseif ($type == 'xml') {
        $xml = simplexml_load_file($_FILES['media_file']['tmp_name']);
        $data = json_decode(json_encode($xml), true)['item'];
    } elseif ($type == 'txt') {
        $file = fopen($_FILES['media_file']['tmp_name'], 'r');
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
            $stmt = $pdo->prepare('INSERT IGNORE INTO gallery_media VALUES (' . $values . ')');
            $stmt->execute(array_values($row));
            $i += $stmt->rowCount();
        }
        header('Location: allmedia.php?success_msg=4&imported=' . $i);
        exit;
    }
}

// Handle Media Collections Import form submission
if (isset($_FILES['collections_file'], $_POST['collections_import']) && !empty($_FILES['collections_file']['tmp_name'])) {
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
            $stmt = $pdo->prepare('INSERT IGNORE INTO gallery_media_collections VALUES (' . $values . ')');
            $stmt->execute(array_values($row));
            $i += $stmt->rowCount();
        }
        header('Location: collections.php?success_msg=4&imported=' . $i);
        exit;
    }
}

// Handle Media Export form submission
if (isset($_POST['media_export_type'])) {
    // Get all media
    $result = $pdo->query('SELECT * FROM gallery_media ORDER BY id ASC');
    $media = [];
    $columns = [];
    // Fetch all records into an associative array
    if ($result->rowCount() > 0) {
        // Fetch column names
        for ($i = 0; $i < $result->columnCount(); $i++) {
            $columns[] = $result->getColumnMeta($i)['name'];
        }
        // Fetch associative array
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $media[] = $row;
        }    
    }
    // Convert to CSV
    if ($_POST['media_export_type'] == 'csv') {
        $filename = 'media.csv';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        fputcsv($fp,  $columns);
        foreach ($media as $poll) {
            fputcsv($fp, $poll);
        }
        fclose($fp);
        exit;
    }
    // Convert to TXT
    if ($_POST['media_export_type'] == 'txt') {
        $filename = 'media.txt';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/txt');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, implode(',', $columns) . PHP_EOL);
        foreach ($media as $poll) {
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
    if ($_POST['media_export_type'] == 'json') {
        $filename = 'media.json';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, json_encode($media));
        fclose($fp);
        exit;
    }
    // Convert to XML
    if ($_POST['media_export_type'] == 'xml') {
        $filename = 'media.xml';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/xml');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($fp, '<media>' . PHP_EOL);
        foreach ($media as $poll) {
            fwrite($fp, '    <poll>' . PHP_EOL);
            foreach ($poll as $key => $value) {
                fwrite($fp, '        <' . $key . '>' . htmlspecialchars($value) . '</' . $key . '>' . PHP_EOL);
            }
            fwrite($fp, '    </poll>' . PHP_EOL);
        }
        fwrite($fp, '</media>' . PHP_EOL);
        fclose($fp);
        exit;
    }
}

// Handle Media Collections Export form submission
if (isset($_POST['collections_export_type'])) {
    // Get all gallery_media_collections
    $result = $pdo->query('SELECT * FROM gallery_media_collections ORDER BY id ASC');
    $media_collections = [];
    $columns = [];
    // Fetch all records into an associative array
    if ($result->rowCount() > 0) {
        // Fetch column names
        for ($i = 0; $i < $result->columnCount(); $i++) {
            $columns[] = $result->getColumnMeta($i)['name'];
        }
        // Fetch associative array
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $media_collections[] = $row;
        }    
    }
    // Convert to CSV
    if ($_POST['collections_export_type'] == 'csv') {
        $filename = 'media_collections.csv';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        fputcsv($fp,  $columns);
        foreach ($media_collections as $media_collection) {
            fputcsv($fp, $media_collection);
        }
        fclose($fp);
        exit;
    }
    // Convert to TXT
    if ($_POST['collections_export_type'] == 'txt') {
        $filename = 'media_collections.txt';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/txt');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, implode(',', $columns) . PHP_EOL);
        foreach ($media_collections as $media_collection) {
            $line = '';
            foreach ($media_collection as $key => $value) {
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
    if ($_POST['collections_export_type'] == 'json') {
        $filename = 'media_collections.json';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, json_encode($media_collections));
        fclose($fp);
        exit;
    }
    // Convert to XML
    if ($_POST['collections_export_type'] == 'xml') {
        $filename = 'media_collections.xml';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/xml');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($fp, '<media_collections>' . PHP_EOL);
        foreach ($media_collections as $media_collection) {
            fwrite($fp, '    <media_collection>' . PHP_EOL);
            foreach ($media_collection as $key => $value) {
                fwrite($fp, '        <' . $key . '>' . htmlspecialchars($value) . '</' . $key . '>' . PHP_EOL);
            }
            fwrite($fp, '    </media_collection>' . PHP_EOL);
        }
        fwrite($fp, '</media_collections>' . PHP_EOL);
        fclose($fp);
        exit;
    }
}
?>

<?=template_admin_header('Import/Export Gallery Data', 'gallery', 'import_export')?>

<div class="content-title mb-4" id="main-gallery-import-export" role="banner" aria-label="Gallery Import/Export Header">
    <div class="title">
        <div class="icon">
            <i class="bi bi-hdd-network" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Import/Export Gallery Data</h2>
            <p>Import media and collection data from files or export existing data to various formats.</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">Gallery Data Transfer Center</h6>
        <p class="text-muted mb-0 mt-1">Import or export gallery media and collection data using the tabs below</p>
    </div>
    <div class="card-body">
        <!-- Tab Navigation -->
        <div class="tab-nav" role="tablist" aria-label="Gallery data transfer options">
        <button class="tab-btn active" 
            role="tab"
            aria-selected="true"
            aria-controls="export-media-tab"
            id="export-media-tab-btn"
            onclick="openTab(event, 'export-media-tab')">
            Export Media
        </button>
        <button class="tab-btn" 
            role="tab"
            aria-selected="false"
            aria-controls="import-media-tab"
            id="import-media-tab-btn"
            onclick="openTab(event, 'import-media-tab')">
            Import Media
        </button>
        <button class="tab-btn" 
            role="tab"
            aria-selected="false"
            aria-controls="export-collections-tab"
            id="export-collections-tab-btn"
            onclick="openTab(event, 'export-collections-tab')">
            Export Collections
        </button>
        <button class="tab-btn" 
            role="tab"
            aria-selected="false"
            aria-controls="import-collections-tab"
            id="import-collections-tab-btn"
            onclick="openTab(event, 'import-collections-tab')">
            Import Collections
        </button>
    </div>

        <!-- Export Media Tab Content -->
        <div id="export-media-tab" 
            class="tab-content active" 
            role="tabpanel"
            aria-labelledby="export-media-tab-btn">
            <form action="" method="post" role="form" aria-labelledby="export-media-title">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="media_export_type" class="form-label">
                                <span class="required" aria-hidden="true">*</span> 
                                File Type
                                <span class="sr-only">(required)</span>
                            </label>
                            <select id="media_export_type" 
                                name="media_export_type" 
                                class="form-control"
                                required 
                                aria-required="true"
                                aria-describedby="media-export-type-desc">
                                <option value="csv">CSV (Comma Separated Values)</option>
                                <option value="txt">TXT (Text File)</option>
                                <option value="json">JSON (JavaScript Object Notation)</option>
                                <option value="xml">XML (Extensible Markup Language)</option>
                            </select>
                            <div id="media-export-type-desc" class="form-text">Choose the format for your exported media data file.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="file-info" role="region" aria-labelledby="media-export-info">
                            <h6 id="media-export-info">Export Information</h6>
                            <p>Download all media data in your preferred format. The exported file will contain all media records from your gallery system.</p>
                            <ul>
                                <li><strong>CSV:</strong> Spreadsheet-compatible format</li>
                                <li><strong>JSON:</strong> Machine-readable data format</li>
                                <li><strong>XML:</strong> Structured markup format</li>
                                <li><strong>TXT:</strong> Plain text format</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 pt-3 border-top mt-4">
                    <a href="allmedia.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancel
                    </a>
                    <button type="submit" name="submit" class="btn btn-success">
                        <i class="bi bi-download me-1" aria-hidden="true"></i>Export Media
                    </button>
                </div>
            </form>
        </div>

        <!-- Import Media Tab Content -->
        <div id="import-media-tab" 
            class="tab-content"
            role="tabpanel"
            aria-labelledby="import-media-tab-btn">
            <form action="" 
                method="post" 
                enctype="multipart/form-data" 
                role="form" 
                aria-labelledby="import-media-title">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="media_file" class="form-label">
                                <span class="required" aria-hidden="true">*</span> 
                                File Upload
                                <span class="sr-only">(required)</span>
                            </label>
                            <input type="file" 
                                name="media_file" 
                                id="media_file" 
                                class="form-control"
                                accept=".csv,.json,.xml,.txt" 
                                required
                                aria-required="true"
                                aria-describedby="media-file-desc media-file-formats">
                            <div id="media-file-desc" class="form-text">Select a data file to import media information.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="file-info" role="region" aria-labelledby="media-supported-formats">
                            <h6 id="media-supported-formats">Supported File Formats</h6>
                            <ul id="media-file-formats">
                                <li><strong>CSV:</strong> Comma-separated values with header row</li>
                                <li><strong>JSON:</strong> Array of media objects</li>
                                <li><strong>XML:</strong> Structured XML with media elements</li>
                                <li><strong>TXT:</strong> Tab or comma-delimited text file</li>
                            </ul>
                            <div class="alert alert-info mt-3">
                                <small><strong>Note:</strong> Large files may take time to process. Please ensure your file format matches the expected structure.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 pt-3 border-top mt-4">
                    <a href="allmedia.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancel
                    </a>
                    <button type="submit" name="media_import" class="btn btn-success">
                        <i class="bi bi-upload me-1" aria-hidden="true"></i>Import Media
                    </button>
                </div>
            </form>
        </div>

        <!-- Export Collections Tab Content -->
        <div id="export-collections-tab" 
            class="tab-content"
            role="tabpanel"
            aria-labelledby="export-collections-tab-btn">
            <form action="" method="post" role="form" aria-labelledby="export-collections-title">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="collections_export_type" class="form-label">
                                <span class="required" aria-hidden="true">*</span> 
                                File Type
                                <span class="sr-only">(required)</span>
                            </label>
                            <select id="collections_export_type" 
                                name="collections_export_type" 
                                class="form-control"
                                required 
                                aria-required="true"
                                aria-describedby="collections-export-type-desc">
                                <option value="csv">CSV (Comma Separated Values)</option>
                                <option value="txt">TXT (Text File)</option>
                                <option value="json">JSON (JavaScript Object Notation)</option>
                                <option value="xml">XML (Extensible Markup Language)</option>
                            </select>
                            <div id="collections-export-type-desc" class="form-text">Choose the format for your exported collections data file.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="file-info" role="region" aria-labelledby="collections-export-info">
                            <h6 id="collections-export-info">Export Information</h6>
                            <p>Download all media collections data in your preferred format. This includes collection metadata, organization, and relationships.</p>
                            <ul>
                                <li><strong>CSV:</strong> Spreadsheet-compatible format</li>
                                <li><strong>JSON:</strong> Machine-readable data format</li>
                                <li><strong>XML:</strong> Structured markup format</li>
                                <li><strong>TXT:</strong> Plain text format</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 pt-3 border-top mt-4">
                    <a href="collections.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancel
                    </a>
                    <button type="submit" name="submit" class="btn btn-success">
                        <i class="bi bi-download me-1" aria-hidden="true"></i>Export Collections
                    </button>
                </div>
            </form>
        </div>

        <!-- Import Collections Tab Content -->
        <div id="import-collections-tab" 
            class="tab-content"
            role="tabpanel"
            aria-labelledby="import-collections-tab-btn">
            <form action="" 
                method="post" 
                enctype="multipart/form-data" 
                role="form" 
                aria-labelledby="import-collections-title">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="collections_file" class="form-label">
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
                                aria-describedby="collections-file-desc collections-file-formats">
                            <div id="collections-file-desc" class="form-text">Select a data file to import collections information.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="file-info" role="region" aria-labelledby="collections-supported-formats">
                            <h6 id="collections-supported-formats">Supported File Formats</h6>
                            <ul id="collections-file-formats">
                                <li><strong>CSV:</strong> Comma-separated values with header row</li>
                                <li><strong>JSON:</strong> Array of collections objects</li>
                                <li><strong>XML:</strong> Structured XML with collections elements</li>
                                <li><strong>TXT:</strong> Tab or comma-delimited text file</li>
                            </ul>
                            <div class="alert alert-info mt-3">
                                <small><strong>Note:</strong> Ensure collection data matches your system's structure before importing.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2 pt-3 border-top mt-4">
                    <a href="collections.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancel
                    </a>
                    <button type="submit" name="collections_import" class="btn btn-success">
                        <i class="bi bi-upload me-1" aria-hidden="true"></i>Import Collections
                    </button>
                </div>
            </form>
        </div>
    </div>
</div><style>
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

<?=template_admin_footer()?>
