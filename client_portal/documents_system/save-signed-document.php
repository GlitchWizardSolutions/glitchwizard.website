<?php
/**
 * File: save-signed-document.php
 * Description: Finalizes and saves the signed PDF to the client’s document folder.
 * Functions:
 *   - None directly, uses filesystem operations
 * Expected Outputs:
 *   - Signed document saved to /clients/client_[id]/signed/
 * Related Files:
 *   - submit-signature-handler.php
 *   - gws-universal-config.php
 */

require_once '../../private/gws-universal-config.php';

$clientId = $_SESSION['client_id'] ?? null;

if (!$clientId || empty($_POST['signed_pdf_path']) || !file_exists($_POST['signed_pdf_path'])) {
    http_response_code(400);
    echo 'Invalid request or missing signed document.';
    exit;
}

$originalPath = $_POST['signed_pdf_path'];
$filename = basename($originalPath);
$clientFolder = __DIR__ . "/clients/client_{$clientId}/signed/";

if (!is_dir($clientFolder)) {
    mkdir($clientFolder, 0755, true);
}

$finalPath = $clientFolder . $filename;

if (rename($originalPath, $finalPath)) {
    echo "<div class='container p-4'>";
    echo "<h2>Signed document saved successfully.</h2>";
    echo "<p><a href='" . htmlspecialchars(str_replace(__DIR__, '', $finalPath)) . "' target='_blank'>View Signed Document</a></p>";
    echo "<p><a href='dashboard.php'>Return to Dashboard</a></p>";
    echo "</div>";
} else {
    http_response_code(500);
    echo 'Failed to move signed document.';
}
?>