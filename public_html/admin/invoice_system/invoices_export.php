<?php
include 'main.php';
// Remove the time limit and file size limit
set_time_limit(0);
ini_set('post_max_size', '0');
ini_set('upload_max_filesize', '0');
// If form submitted
if (isset($_POST['file_type'], $_POST['table'])) {
    // Check if the table is valid
    $table = in_array($_POST['table'], ['invoices', 'invoice_items']) ? $_POST['table'] : 'invoices';
    // Get all invoices
    $result = $pdo->query('SELECT * FROM ' . $table . ' ORDER BY id ASC');
    $invoices = [];
    $columns = [];
    // Fetch all records into an associative array
    if ($result->rowCount() > 0) {
        // Fetch column names
        for ($i = 0; $i < $result->columnCount(); $i++) {
            $columns[] = $result->getColumnMeta($i)['name'];
        }
        // Fetch associative array
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $invoices[] = $row;
        }    
    }
    // Convert to CSV
    if ($_POST['file_type'] == 'csv') {
        $filename = $table . '.csv';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        fputcsv($fp,  $columns);
        foreach ($invoices as $invoice) {
            fputcsv($fp, $invoice);
        }
        fclose($fp);
        exit;
    }
    // Convert to TXT
    if ($_POST['file_type'] == 'txt') {
        $filename = $table . '.txt';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/txt');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, implode(',', $columns) . PHP_EOL);
        foreach ($invoices as $invoice) {
            $line = '';
            foreach ($invoice as $key => $value) {
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
    if ($_POST['file_type'] == 'json') {
        $filename = $table . '.json';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, json_encode($invoices));
        fclose($fp);
        exit;
    }
    // Convert to XML
    if ($_POST['file_type'] == 'xml') {
        $filename = $table . '.xml';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/xml');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($fp, '<' . $table . '>' . PHP_EOL);
        foreach ($invoices as $invoice) {
            fwrite($fp, '    <item>' . PHP_EOL);
            foreach ($invoice as $key => $value) {
                fwrite($fp, '        <' . $key . '>' . $value . '</' . $key . '>' . PHP_EOL);
            }
            fwrite($fp, '    </item>' . PHP_EOL);
        }
        fwrite($fp, '</' . $table . '>' . PHP_EOL);
        fclose($fp);
        exit;
    }
}
?>
<?=template_admin_header('Export Invoices', 'invoices', 'export')?>

<div class="content-header">
    <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
        <h2 class="responsive-width-100">Export Invoices</h2>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="card-title mb-0">Export Invoices</h6>
        <small class="text-muted">Download invoice data in various formats</small>
    </div>
    <div class="card-body">
        <form action="" method="post">
            <!-- Form buttons at top -->
            <div class="d-flex gap-2 pb-3 border-bottom mb-3">
                <button type="submit" name="submit" class="btn btn-success">
                    <i class="bi bi-download me-1" aria-hidden="true"></i>Export Data
                </button>
                <a href="invoices.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancel
                </a>
            </div>

            <!-- Export Options -->
            <fieldset class="mb-4">
                <legend class="h6 text-primary border-bottom pb-1">Export Options</legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="table" class="form-label">Data Table <span class="text-danger">*</span></label>
                            <select class="form-select" id="table" name="table" required>
                                <option value="">Choose data to export...</option>
                                <option value="invoices">Invoices</option>
                                <option value="invoice_items">Invoice Items</option>
                            </select>
                            <div class="form-text">Select which data table to export</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="file_type" class="form-label">File Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="file_type" name="file_type" required>
                                <option value="">Choose format...</option>
                                <option value="csv">CSV (Comma Separated Values)</option>
                                <option value="txt">TXT (Tab Delimited)</option>
                                <option value="json">JSON (JavaScript Object Notation)</option>
                                <option value="xml">XML (Extensible Markup Language)</option>
                            </select>
                            <div class="form-text">Select the export format for your data</div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- Repeat form buttons at bottom -->
            <div class="d-flex gap-2 pt-3 border-top mt-4">
                <button type="submit" name="submit" class="btn btn-success">
                    <i class="bi bi-download me-1" aria-hidden="true"></i>Export Data
                </button>
                <a href="invoices.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?=template_admin_footer()?>