 <?php
/**
 * File: template-manager-ui.php
 * Description: Handles client-side custom template saving, document locking, and PDF generation.
 * Functions:
 *   - generateTemplateHTML
 *   - getClientNameById
 *   - slugify
 * Expected Outputs:
 *   - JSON response for success, failure, or lock status
 *   - Rendered PDF saved to uploads/documents/
 *   - JSON array of saved templates for dashboard dropdown
 * Related Files:
 *   - gws-universal-functions.php
 *   - gws-universal-config.php
 *   - uploads/documents/
 *   - saved_templates (DB table), audit_log (DB table), draft_locks (DB table)
 */

require_once '../../private/gws-universal-config.php';

use Mpdf\Mpdf;

$clientId = $_SESSION['client_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $clientId) {
    $documentTitle = $_POST['document_title'] ?? '';

    // --- Begin draft locking logic ---
    $lockCheckStmt = $pdo->prepare("SELECT * FROM draft_locks WHERE document_title = ? AND client_id != ? AND locked_until > NOW()");
    $lockCheckStmt->execute([$documentTitle, $clientId]);
    $lock = $lockCheckStmt->fetch(PDO::FETCH_ASSOC);

    if ($lock) {
        echo json_encode([
            'status' => 'locked',
            'message' => 'This document is currently being edited by another user.'
        ]);
        exit;
    }

    $insertLockStmt = $pdo->prepare("REPLACE INTO draft_locks (document_title, client_id, locked_until) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE))");
    $insertLockStmt->execute([$documentTitle, $clientId]);
    // --- End draft locking logic ---

    $outputDir = __DIR__ . '/uploads/documents';
    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    $clientName   = getClientNameById($pdo, $clientId);
    $versionNotes = $_POST['version_notes'] ?? '';
    $versionTags  = $_POST['version_tags'] ?? '';
    $templateType = $_POST['template_type'] ?? 'default';

    $fileName     = slugify($documentTitle) . "_" . time() . ".pdf";
    $outputPath   = $outputDir . '/' . $fileName;
    $relativePath = 'uploads/documents/' . $fileName;

    $html = generateTemplateHTML($templateType, $_POST, $pdo);

    $mpdf = new Mpdf();
    $mpdf->WriteHTML($html);
    $mpdf->Output($outputPath, \Mpdf\Output\Destination::FILE);

    $stmt = $pdo->prepare("INSERT INTO audit_log (client_id, client_name, document_title, file_type, file_path, output_path, version_notes, version_tags, ip_address, user_agent)
        VALUES (?, ?, ?, 'pdf', ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $clientId,
        $clientName,
        $documentTitle,
        $relativePath,
        $outputPath,
        $versionNotes,
        $versionTags,
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);

    echo json_encode([
        'status' => 'success',
        'message' => 'PDF generated and saved.',
        'preview' => $relativePath
    ]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_templates']) && $clientId) {
    $stmt = $pdo->prepare("SELECT id, template_name, content FROM saved_templates WHERE client_id = ? ORDER BY updated_at DESC");
    $stmt->execute([$clientId]);
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($templates);
    exit;
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request.'
    ]);
}
