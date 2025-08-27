<?php
/**
 * File: save-signed-pdf.php
 * Description: Saves a newly signed PDF file and optionally logs it to the audit table.
 * Functions:
 *   - None declared; uses standard file and session operations
 * Expected Outputs:
 *   - Signed PDF saved to /clients/client_[id]/signed/
 *   - Optionally logs metadata to audit_log
 * Related Files:
 *   - submit-signature-handler.php
 *   - dashboard.php
 *   - gws-universal-config.php
 */

require_once '../../private/gws-universal-config.php';

$clientId = $_SESSION['client_id'] ?? null;
$filename = $_POST['filename'] ?? null;
$pdfData = $_POST['pdf_data'] ?? null;

if (!$clientId || !$filename || !$pdfData) {
    http_response_code(400);
    echo 'Missing required data.';
    exit;
}

$clientFolder = __DIR__ . "/clients/client_{$clientId}/signed/";
if (!is_dir($clientFolder)) {
    mkdir($clientFolder, 0755, true);
}

$decoded = base64_decode(preg_replace('#^data:application/pdf;base64,#', '', $pdfData));
$filePath = $clientFolder . basename($filename);

if (file_put_contents($filePath, $decoded)) {
    echo "<div class='container p-4'>";
    echo "<h2>Signed PDF saved successfully.</h2>";
    echo "<p><a href='" . htmlspecialchars(str_replace(__DIR__, '', $filePath)) . "' target='_blank'>Open PDF</a></p>";
    echo "<p><a href='dashboard.php'>Return to Dashboard</a></p>";
    echo "</div>";
} else {
    http_response_code(500);
    echo 'Failed to save signed PDF.';
}
?>