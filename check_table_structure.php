<?php
try {
    include 'private/gws-universal-config.php';
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "DESCRIBE setting_branding_templates:\n";
    $stmt = $pdo->query('DESCRIBE setting_branding_templates');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Key'] . ' - ' . $row['Default'] . "\n";
    }
    
    echo "\nSample data from setting_branding_templates:\n";
    $stmt = $pdo->query('SELECT * FROM setting_branding_templates LIMIT 5');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
