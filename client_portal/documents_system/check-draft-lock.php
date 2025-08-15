<?php
/**
 * File: check-draft-lock.php
 * Description: Endpoint to check if a document is currently locked for editing.
 * Functions:
 *   - Queries `draft_locks` table for lock status on given filename
 * Expected Outputs:
 *   - JSON { locked: true, by: "...", since: "..." } or { locked: false }
 * Related Files:
 *   - lock-draft-handler.php
 *   - generate-pdf-handler.php
 *   - dashboard.php
 */

require_once '../../private/gws-universal-config.php';

header('Content-Type: application/json');

$filename = $_GET['filename'] ?? null;

if (!$filename) {
    echo json_encode(['error' => 'Missing filename']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM draft_locks WHERE filename = ? AND expires_at > NOW() LIMIT 1");
$stmt->execute([$filename]);
$lock = $stmt->fetch();

if ($lock) {
    echo json_encode([
        'locked' => true,
        'by'     => $lock['locked_by'] ?? 'Unknown',
        'since'  => $lock['locked_at'],
    ]);
} else {
    echo json_encode(['locked' => false]);
}