<?php
require_once '../../private/gws-universal-config.php';

$clientId = $_SESSION['client_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $clientId) {
    $versionNotes = $_POST['version_notes'] ?? '';
    $documentId = $_POST['document_id'] ?? null; // Optional depending on integration

    if (!empty($versionNotes)) {
        $stmt = $pdo->prepare("INSERT INTO version_notes (client_id, document_id, notes, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$clientId, $documentId, $versionNotes]);

        echo '<div style="padding:2rem;font-family:sans-serif;">
                <h2>Notes saved successfully!</h2>
                <a href="dashboard.php">Return to Dashboard</a>
              </div>';
        exit;
    }
}

http_response_code(400);
echo 'Invalid request.';
