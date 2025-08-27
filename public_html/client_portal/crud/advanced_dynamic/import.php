<?php
include 'functions.php';
// Connect to MySQL database
$pdo = pdo_connect_mysql($db_host, $db_name, $db_user, $db_pass);
// Error message
$error_msg = '';
// Success message
$success_msg = '';
// Check if POST data exists (user submitted the form)
if (isset($_POST['submit'])) {
    // Validate the CSV file
    if (empty($_FILES['csv']['tmp_name'])) {
        $error_msg = 'Please select a CSV file to import!';
    } else if (strtolower(pathinfo($_FILES['csv']['name'], PATHINFO_EXTENSION)) != 'csv') {
        $error_msg = 'File must be a CSV file type!';
    } else {
        // Parse the CSV file
        $csv = array_map('str_getcsv', file($_FILES['csv']['tmp_name']));
        // Validate the header
        $header = array_shift($csv);
        $columns = array_keys($columns);
        if ($header != $columns) {
            $error_msg = 'The CSV header must match the table columns!';
        } else {
            // Import the CSV data
            foreach ($csv as $row) {
                $stmt = $pdo->prepare('INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', array_map(function ($column) {
                    return ':' . $column;
                }, $columns)) . ')');
                foreach ($row as $key => $value) {
                    // check if datetime
                    if (in_array($columns[$key], ['created', 'updated'])) {
                        $value = date('Y-m-d H:i:s', strtotime($value));
                    }
                    $stmt->bindValue(':' . $columns[$key], $value);
                }
                $stmt->execute();
            }
            // Output message with number of records imported
            $success_msg = 'Imported ' . count($csv) . ' record(s) from CSV file!';
        }
    }
}
?>
<?=template_header('Import')?>

<div class="content update">

    <div class="page-title">
        <i class="fa-solid fa-file-import fa-lg"></i>
        <div class="wrap">
            <h2>Import Contacts</h2>
            <p>Import contacts from a CSV file, fill in the form below and submit.</p>
        </div>
    </div>

    <form action="" method="post" enctype="multipart/form-data" class="crud-form">

        <div class="cols">
            <div class="form-control">
                <label for="csv">CSV File</label>
                <input type="file" name="csv" id="csv" accept=".csv" required>
            </div>
        </div>

        <?php if ($error_msg): ?>
        <p class="msg-error"><?=$error_msg?></p>
        <?php endif; ?>

        <?php if ($success_msg): ?>
        <p class="msg-success"><?=$success_msg?></p>
        <?php endif; ?>

        <button type="submit" name="submit" class="btn">Import</button>

    </form>

</div>

<?=template_footer()?>