<?php
/**
 * File: draft-locking-setup.php
 * Description: Handles draft locking to prevent concurrent editing of documents.
 * Functions:
 *   - createDraftLock($pdo, $draftId, $clientId)
 *   - checkDraftLock($pdo, $draftId)
 *   - releaseDraftLock($pdo, $draftId)
 * Expected Outputs:
 *   - Locks or checks for a draft editing session
 * Related Files:
 *   - generate-pdf-handler.php
 *   - submit-version-notes.php
 *   - dashboard.php
 * Called From:
 * You’ll want to call this via fetch() or XMLHttpRequest in:
 * generate-pdf-handler.php → to create a lock before PDF generation.
 * submit-version-notes.php → to check and update the lock during editing.
 * dashboard.php → (optional) to alert the user if a draft is being edited.
 */

require_once '../../private/gws-universal-config.php';

if (!function_exists('createDraftLock')) {
    function createDraftLock(PDO $pdo, string $draftId, int $clientId): bool {
        // Remove any expired locks (older than 10 minutes)
        $pdo->prepare("DELETE FROM draft_locks WHERE last_updated < (NOW() - INTERVAL 10 MINUTE)")->execute();

        // Check if draft is already locked
        $stmt = $pdo->prepare("SELECT * FROM draft_locks WHERE draft_id = ?");
        $stmt->execute([$draftId]);
        $existing = $stmt->fetch();

        if ($existing && $existing['client_id'] !== $clientId) {
            return false; // Locked by another user
        }

        // Insert or update lock for current user
        $stmt = $pdo->prepare("
            INSERT INTO draft_locks (draft_id, client_id, last_updated)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE last_updated = NOW()
        ");
        return $stmt->execute([$draftId, $clientId]);
    }
}

if (!function_exists('checkDraftLock')) {
    function checkDraftLock(PDO $pdo, string $draftId): ?array {
        $stmt = $pdo->prepare("SELECT * FROM draft_locks WHERE draft_id = ?");
        $stmt->execute([$draftId]);
        return $stmt->fetch() ?: null;
    }
}

if (!function_exists('releaseDraftLock')) {
    function releaseDraftLock(PDO $pdo, string $draftId): void {
        $stmt = $pdo->prepare("DELETE FROM draft_locks WHERE draft_id = ?");
        $stmt->execute([$draftId]);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['draft_id'])) {
    header('Content-Type: application/json');
    $action   = $_POST['action'];
    $draftId  = $_POST['draft_id'];
    $clientId = $_SESSION['client_id'] ?? 0;

    if ($action === 'check') {
        $lock = checkDraftLock($pdo, $draftId);
        echo json_encode(['locked' => (bool)$lock, 'client_id' => $lock['client_id'] ?? null]);
    } elseif ($action === 'lock') {
        $success = createDraftLock($pdo, $draftId, $clientId);
        echo json_encode(['locked' => !$success ? true : false, 'success' => $success]);
    } elseif ($action === 'release') {
        releaseDraftLock($pdo, $draftId);
        echo json_encode(['released' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
    }
    exit;
}