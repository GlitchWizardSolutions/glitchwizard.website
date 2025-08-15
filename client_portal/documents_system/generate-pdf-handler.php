 <?php
/**
 * File: generate-pdf-handler.php
 * Description: Handles submission of a new or edited document.
 * Functions:
 *   - Validates form input
 *   - Saves to database (or file system)
 *   - Logs submission event
 *   - Redirects to confirmation
 * Related Files:
 *   - dashboard.php
 *   - confirm-generation.php
 */

require_once '../../private/gws-universal-config.php';

// Validate session/client
$clientId = $_SESSION['client_id'] ?? 0;
if (!$clientId) {
    die('Client session not found.');
}

// Validate POST
$title   = trim($_POST['document_title'] ?? '');
$content = $_POST['document_content'] ?? '';
$notes   = $_POST['version_notes'] ?? '';
$draftId = $_POST['draft_id'] ?? ('new_' . uniqid());

if ($title === '' || $content === '') {
    die('Title and content are required.');
}

// Optional: sanitize input further
$titleSafe = htmlspecialchars($title);
$notesSafe = htmlspecialchars($notes);

// Store draft to `drafts` table
$stmt = $pdo->prepare("
    INSERT INTO drafts (draft_id, client_id, title, content, version_notes, created_at, updated_at)
    VALUES (:draft_id, :client_id, :title, :content, :version_notes, NOW(), NOW())
    ON DUPLICATE KEY UPDATE 
        title = VALUES(title),
        content = VALUES(content),
        version_notes = VALUES(version_notes),
        updated_at = NOW()
");

$stmt->execute([
    ':draft_id' => $draftId,
    ':client_id' => $clientId,
    ':title' => $title,
    ':content' => $content,
    ':version_notes' => $notes
]);

// Optional: Audit log
$pdo->prepare("
    INSERT INTO audit_log (client_id, action, details, created_at)
    VALUES (?, 'draft_saved', ?, NOW())
")->execute([
    $clientId,
    "Draft ID {$draftId} saved"
]);

// Redirect to confirmation
header("Location: confirm-generation.php?draft_id=" . urlencode($draftId));
exit;
