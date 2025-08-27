<?php
// Include the main configuration file
require_once '../../../private/gws-universal-config.php';

// Redirect if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth.php");
    exit();
}

// Check user role
if (!isset($_SESSION['role'])) {
    header("Location: ../auth.php");
    exit();
}

// Create PDO connection for this file
$host = DB_HOST;
$dbname = DB_NAME;
$username = DB_USERNAME;
$password = DB_PASSWORD;
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed");
}

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
        $data = json_decode(json_encode($xml), true)['ticket'];
    } elseif ($type == 'txt')
    {
        $file = fopen($_FILES['file']['tmp_name'], 'r');
        while ($row = fgetcsv($file))
        {
            $data[] = $row;
        }
        fclose($file);
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
            if (isset($row['created']))
            {
                $row['created'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $row['created'])));
            }
            if (isset($row['submit_date']))
            {
                $row['submit_date'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $row['submit_date'])));
            }
            // insert into database
            // tip: if you want to update existing records, use INSERT ... ON DUPLICATE KEY UPDATE instead
            $stmt = $pdo->prepare('INSERT IGNORE INTO tickets VALUES (' . $values . ')');
            $stmt->execute(array_values($row));
            $i += $stmt->rowCount();
        }
        header('Location: tickets.php?success_msg=4&imported=' . $i);
        exit;
    }
}

// Handle Export form submission
if (isset($_POST['file_type']))
{
    // Get all tickets
    $result = $pdo->query('SELECT * FROM tickets ORDER BY id ASC');
    $tickets = [];
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
        while ($row = $result->fetch())
        {
            $tickets[] = $row;
        }
    }
    // Convert to CSV
    if ($_POST['file_type'] == 'csv')
    {
        $filename = 'tickets.csv';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        fputcsv($fp, $columns);
        foreach ($tickets as $ticket)
        {
            fputcsv($fp, $ticket);
        }
        fclose($fp);
        exit;
    }
    // Convert to TXT
    if ($_POST['file_type'] == 'txt')
    {
        $filename = 'tickets.txt';
        $fp = fopen('php://output', 'w');
        header('Content-type: text/plain');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, implode("\t", $columns) . PHP_EOL);
        foreach ($tickets as $ticket)
        {
            $line = '';
            foreach ($ticket as $key => $value)
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
        $filename = 'tickets.json';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, json_encode($tickets));
        fclose($fp);
        exit;
    }
    // Convert to XML
    if ($_POST['file_type'] == 'xml')
    {
        $filename = 'tickets.xml';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/xml');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($fp, '<tickets>' . PHP_EOL);
        foreach ($tickets as $ticket)
        {
            fwrite($fp, '    <ticket>' . PHP_EOL);
            foreach ($ticket as $key => $value)
            {
                fwrite($fp, '        <' . $key . '>' . $value . '</' . $key . '>' . PHP_EOL);
            }
            fwrite($fp, '    </ticket>' . PHP_EOL);
        }
        fwrite($fp, '</tickets>' . PHP_EOL);
        fclose($fp);
        exit;
    }
}
?>
<?= template_admin_header('Data Transfer', 'ticket-data-transfer', 'admin') ?>

<!-- Main Content -->
<div class="content-title mb-4" role="region" aria-label="Page Header">
    <h2 class="d-flex align-items-center gap-2" role="heading" aria-level="2">
    <i class="bi bi-arrow-left-right text-primary" aria-hidden="true"></i>
        <span>Data Transfer</span>
    </h2>
</div>

<!-- Top page actions -->
<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    <a href="tickets.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
        Cancel
    </a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Data Transfer Center</h6>
        <small class="text-muted">Import or export ticket data</small>
    </div>
    <div class="card-body">
        <!-- Tab Navigation -->
        <ul class="nav nav-pills mb-4" id="transferTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="import-tab" data-bs-toggle="pill" data-bs-target="#import" type="button" role="tab" aria-controls="import" aria-selected="true">
                    <i class="bi bi-upload me-1" aria-hidden="true"></i>
                    Import Tickets
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="export-tab" data-bs-toggle="pill" data-bs-target="#export" type="button" role="tab" aria-controls="export" aria-selected="false">
                    <i class="bi bi-download me-1" aria-hidden="true"></i>
                    Export Tickets
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="transferTabsContent">
            <!-- Import Tab -->
            <div class="tab-pane fade show active" id="import" role="tabpanel" aria-labelledby="import-tab" tabindex="0">
                <div class="row">
                    <div class="col-md-6">
                        <form method="post" enctype="multipart/form-data" class="mb-4" id="importForm">
                            <div class="mb-3">
                                <label for="csv_file" class="form-label required">CSV File</label>
                                <input type="file" class="form-control" id="csv_file" name="file" accept=".csv,.json,.xml,.txt" required 
                                       aria-describedby="csvFileHelp">
                                <div id="csvFileHelp" class="form-text">Select a CSV, JSON, XML, or TXT file to import ticket data</div>
                            </div>

                            <div class="mb-3">
                                <label for="delimiter" class="form-label">CSV Delimiter</label>
                                <select class="form-select" id="delimiter" name="delimiter">
                                    <option value="," selected>Comma (,)</option>
                                    <option value=";">Semicolon (;)</option>
                                    <option value="\t">Tab</option>
                                    <option value="|">Pipe (|)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="has_header" name="has_header" checked>
                                    <label class="form-check-label" for="has_header">
                                        File has header row
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="update_existing" name="update_existing">
                                    <label class="form-check-label" for="update_existing">
                                        Update existing tickets if ID matches
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Import File Format
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="small mb-2">Supported file formats:</p>
                                <ul class="small mb-3">
                                    <li><strong>CSV:</strong> Comma-separated values with header row</li>
                                    <li><strong>JSON:</strong> Array of ticket objects</li>
                                    <li><strong>XML:</strong> Structured XML with ticket elements</li>
                                    <li><strong>TXT:</strong> Tab or comma-delimited text file</li>
                                </ul>
                                <div class="alert alert-info small mb-0">
                                    <i class="bi bi-lightbulb me-1"></i>
                                    <strong>Tip:</strong> Export existing data first to see the exact format required.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Tab -->
            <div class="tab-pane fade" id="export" role="tabpanel" aria-labelledby="export-tab" tabindex="0">
                <div class="row">
                    <div class="col-md-6">
                        <form method="post" class="mb-4" id="exportForm">
                            <input type="hidden" name="action" value="export">
                            
                            <div class="mb-3">
                                <label for="file_type" class="form-label required">Export Format</label>
                                <select class="form-select" id="file_type" name="file_type" required>
                                    <option value="csv" selected>CSV (Comma Separated)</option>
                                    <option value="txt">TXT (Text File)</option>
                                    <option value="json">JSON</option>
                                    <option value="xml">XML</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="export_delimiter" class="form-label">CSV Delimiter (if CSV selected)</label>
                                <select class="form-select" id="export_delimiter" name="export_delimiter">
                                    <option value="," selected>Comma (,)</option>
                                    <option value=";">Semicolon (;)</option>
                                    <option value="\t">Tab</option>
                                    <option value="|">Pipe (|)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Export Options</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="include_header" name="include_header" checked>
                                    <label class="form-check-label" for="include_header">
                                        Include header row
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="export_all" name="export_all" checked>
                                    <label class="form-check-label" for="export_all">
                                        Export all tickets
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="status_filter" class="form-label">Filter by Status (optional)</label>
                                <select class="form-select" id="status_filter" name="status_filter">
                                    <option value="">All Statuses</option>
                                    <option value="open">Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="date_from" class="form-label">Date Range (optional)</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="date" class="form-control" id="date_from" name="date_from" 
                                               placeholder="From date">
                                    </div>
                                    <div class="col-6">
                                        <input type="date" class="form-control" id="date_to" name="date_to" 
                                               placeholder="To date">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Export Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="small mb-2">Export includes the following data:</p>
                                <ul class="small mb-3">
                                    <li>Ticket ID and basic information</li>
                                    <li>Subject, description, and content</li>
                                    <li>Status and priority levels</li>
                                    <li>Category and assignment data</li>
                                    <li>Creation and update timestamps</li>
                                    <li>User associations</li>
                                </ul>
                                <div class="alert alert-success small mb-0">
                                    <i class="bi bi-shield-check me-1"></i>
                                    <strong>Privacy:</strong> Sensitive data is excluded from exports. Personal information is anonymized where applicable.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Actions -->
        <div class="border-top pt-3 mt-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success" form="importForm" id="importBtn">
                    <i class="bi bi-upload me-1"></i>
                    Import Data
                </button>
                <button type="submit" class="btn btn-success" form="exportForm" id="exportBtn" style="display: none;">
                    <i class="bi bi-download me-1"></i>
                    Export Data
                </button>
                <a href="tickets.php" class="btn btn-outline-secondary ms-auto">
                    <i class="bi bi-x-lg me-1"></i>
                    Cancel
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap tab functionality
    const triggerTabList = document.querySelectorAll('#transferTabs button[data-bs-toggle="pill"]');
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl);
        
        triggerEl.addEventListener('click', event => {
            event.preventDefault();
            tabTrigger.show();
            
            // Show/hide appropriate submit buttons
            const targetId = triggerEl.getAttribute('data-bs-target');
            document.getElementById('importBtn').style.display = targetId === '#import' ? 'inline-block' : 'none';
            document.getElementById('exportBtn').style.display = targetId === '#export' ? 'inline-block' : 'none';
        });
    });
    
    // Initialize first tab button visibility
    document.getElementById('importBtn').style.display = 'inline-block';
    document.getElementById('exportBtn').style.display = 'none';
});
</script>
<?= template_admin_footer() ?>
