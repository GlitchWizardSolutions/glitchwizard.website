<?php
include 'main.php';
// Remove the time limit and file size limit
set_time_limit(0);
ini_set('post_max_size', '0');
ini_set('upload_max_filesize', '0');
// If form submitted
if (isset($_FILES['file'], $_POST['table']) && !empty($_FILES['file']['tmp_name'])) {
    // Check if the table is valid
    $table = in_array($_POST['table'], ['invoices', 'invoice_items']) ? $_POST['table'] : 'invoices';
    // check type
    $type = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $data = [];
    if ($type == 'csv') {
        $file = fopen($_FILES['file']['tmp_name'], 'r');
        $header = fgetcsv($file);
        while ($row = fgetcsv($file)) {
            $data[] = array_combine($header, $row);
        }
        fclose($file);
    } elseif ($type == 'json') {
        $data = json_decode(file_get_contents($_FILES['file']['tmp_name']), true);
    } elseif ($type == 'xml') {
        $xml = simplexml_load_file($_FILES['file']['tmp_name']);
        $data = json_decode(json_encode($xml), true)['item'];
    } elseif ($type == 'txt') {
        $file = fopen($_FILES['file']['tmp_name'], 'r');
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
            if (isset($row['due_date'])) {
                $row['due_date'] = date('Y-m-d H:i', strtotime(str_replace('/','-', $row['due_date'])));
            }
            // insert into database
            // tip: if you want to update existing records, use INSERT ... ON DUPLICATE KEY UPDATE instead
            $stmt = $pdo->prepare('INSERT IGNORE INTO ' . $table . ' VALUES (' . $values . ')');
            $stmt->execute(array_values($row));
            $i += $stmt->rowCount();
        }
        header('Location: invoices.php?success_msg=4&imported=' . $i);
        exit;
    }
}
?>
<?=template_admin_header('Import invoices', 'invoices', 'import')?>

<form action="" method="post" enctype="multipart/form-data">
    <div class="content-header">
        <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
            <h2 class="responsive-width-100">Import Invoices</h2>
            <div class="d-flex gap-2">
                <a href="invoices.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Back to Invoices
                </a>
                <button type="submit" name="submit" class="btn btn-success">
                    <i class="bi bi-upload me-1" aria-hidden="true"></i>Import Data
                </button>
            </div>
        </div>
        <p class="text-muted">Import invoice data from CSV, JSON, XML, or TXT files.</p>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="card-title mb-0">Import Configuration</h6>
            <small class="text-muted">Select table and upload data file</small>
        </div>
        <div class="card-body">
            <fieldset>
                <legend class="h6 mb-3">Import Settings</legend>
                
                <div class="mb-3">
                    <label for="table" class="form-label">
                        <span class="text-danger">*</span> Target Table
                    </label>
                    <select name="table" id="table" class="form-select" required>
                        <option value="invoices">Invoices</option>
                        <option value="invoice_items">Invoice Items</option>
                    </select>
                    <div class="form-text">Choose which table to import data into.</div>
                </div>

                <div class="mb-3">
                    <label for="file" class="form-label">
                        <span class="text-danger">*</span> Data File
                    </label>
                    <input type="file" name="file" id="file" class="form-control" accept=".csv,.json,.xml,.txt" required>
                    <div class="form-text">Select a file containing invoice data to import.</div>
                </div>

                <div class="alert alert-info">
                    <h6 class="alert-heading">Supported File Formats</h6>
                    <ul class="mb-0 small">
                        <li><strong>CSV:</strong> Comma-separated values with header row</li>
                        <li><strong>JSON:</strong> Array of invoice objects</li>
                        <li><strong>XML:</strong> Structured XML with invoice elements</li>
                        <li><strong>TXT:</strong> Tab or comma-delimited text file</li>
                    </ul>
                </div>
            </fieldset>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="invoices.php" class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Cancel
            </a>
            <button type="submit" name="submit" class="btn btn-success">
                <i class="bi bi-upload me-1" aria-hidden="true"></i>Import Data
            </button>
        </div>
    </div>
</form>

<?=template_admin_footer()?>