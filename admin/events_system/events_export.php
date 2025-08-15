<?php
include 'main.php';
// Remove the time limit and file size limit
set_time_limit(0);
ini_set('post_max_size', '0');
ini_set('upload_max_filesize', '0');
// If form submitted
if (isset($_POST['file_type'])) {
    // Get all events
    $result = $pdo->query('SELECT * FROM events ORDER BY id ASC');
    $events = [];
    $columns = [];
    // Fetch all records into an associative array
    if ($result->rowCount() > 0) {
        // Fetch column names
        for ($i = 0; $i < $result->columnCount(); $i++) {
            $columns[] = $result->getColumnMeta($i)['name'];
        }
        // Fetch associative array
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $events[] = $row;
        }    
    }
    // Convert to CSV
    if ($_POST['file_type'] == 'csv') {
        $filename = 'events.csv';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        fputcsv($fp,  $columns);
        foreach ($events as $event) {
            fputcsv($fp, $event);
        }
        fclose($fp);
        exit;
    }
    // Convert to TXT
    if ($_POST['file_type'] == 'txt') {
        $filename = 'events.txt';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/txt');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, implode(',', $columns) . PHP_EOL);
        foreach ($events as $event) {
            $line = '';
            foreach ($event as $key => $value) {
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
        $filename = 'events.json';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/json');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, json_encode($events));
        fclose($fp);
        exit;
    }
    // Convert to XML
    if ($_POST['file_type'] == 'xml') {
        $filename = 'events.xml';
        $fp = fopen('php://output', 'w');
        header('Content-type: application/xml');
        header('Content-Disposition: attachment; filename=' . $filename);
        fwrite($fp, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fwrite($fp, '<events>' . PHP_EOL);
        foreach ($events as $event) {
            fwrite($fp, '    <event>' . PHP_EOL);
            foreach ($event as $key => $value) {
                fwrite($fp, '        <' . $key . '>' . $value . '</' . $key . '>' . PHP_EOL);
            }
            fwrite($fp, '    </event>' . PHP_EOL);
        }
        fwrite($fp, '</events>' . PHP_EOL);
        fclose($fp);
        exit;
    }
}
?>
<?=template_admin_header('Export Events', 'events', 'export')?>

<form action="" method="post">

    <div class="content-title responsive-flex-wrap responsive-pad-bot-3">
        <h2 class="responsive-width-100">Export Events</h2>
        <a href="events.php" class="btn alt mar-right-2">Cancel</a>
        <input type="submit" name="submit" value="Export" class="btn">
    </div>

    <div class="content-block">

        <div class="form responsive-width-100">

            <label for="file_type"><span class="required">*</span> File Type</label>
            <select id="file_type" name="file_type" required>
                <option value="csv">CSV</option>
                <option value="txt">TXT</option>
                <option value="json">JSON</option>
                <option value="xml">XML</option>
            </select>

        </div>

    </div>

</form>

<?=template_admin_footer()?>