<?php
require_once '../../private/gws-universal-config.php';
session_start();
$clientId = $_SESSION['client_id'] ?? null;
if (!$clientId) {
    die("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$documentPath = $_POST['document'] ?? '';
$signatureOption = $_POST['signature_option'] ?? '';
$newSignatureData = $_POST['new_signature'] ?? '';

if (!$documentPath || !$signatureOption) {
    die("Missing required fields.");
}

// Generate file name for signature
$signatureDir = "uploads/signatures/";
$signatureFileName = "signature-{$clientId}.png";
$signaturePath = $signatureDir . $signatureFileName;

if ($signatureOption === 'new' && !empty($newSignatureData)) {
    // Save new signature
    $data = explode(',', $newSignatureData);
    $decoded = base64_decode($data[1]);
    if (!is_dir($signatureDir)) {
        mkdir($signatureDir, 0755, true);
    }
    file_put_contents($signaturePath, $decoded);
} elseif ($signatureOption === 'saved' && !file_exists($signaturePath)) {
    die("No saved signature found.");
}

// Mark document as signed in audit_log
$stmt = $pdo->prepare("UPDATE audit_log SET signed_path = ?, signed_at = NOW() WHERE output_path = ? AND client_id = ?");
$stmt->execute([$signaturePath, $documentPath, $clientId]);

header("Location: view-signed-documents.php?success=1");
exit();
