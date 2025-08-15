<?php
/**
 * Shared functions for document management system
 */

/**
 * Get list of clients from the database
 */
function getClientList(PDO $pdo): array {
    $stmt = $pdo->query('SELECT id, name FROM clients ORDER BY name');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get list of unsigned documents for a client
 */
function getUnsignedDocuments(PDO $pdo, int $clientId): array {
    $stmt = $pdo->prepare('SELECT * FROM documents WHERE client_id = ? AND status = "unsigned" ORDER BY upload_date DESC');
    $stmt->execute([$clientId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Display friendly error message with back link
 */
function friendlyError($message, $backLink = 'sign-document.php') {
    echo '&lt;div class="alert alert-danger"&gt;' . htmlspecialchars($message) . '&lt;/div&gt;';
    echo '&lt;p&gt;&lt;a href="' . htmlspecialchars($backLink) . '" class="btn btn-primary"&gt;&lt;i class="fas fa-arrow-left"&gt;&lt;/i&gt; Back&lt;/a&gt;&lt;/p&gt;';
    exit;
}

/**
 * Check if a draft document is currently locked for editing
 */
function checkDraftLock(PDO $pdo, int $draftId): bool {
    $stmt = $pdo->prepare('SELECT locked, lock_expiry FROM document_drafts WHERE id = ?');
    $stmt->execute([$draftId]);
    $draft = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$draft) return false;
    
    if ($draft['locked'] && strtotime($draft['lock_expiry']) > time()) {
        return true;
    }
    
    return false;
}
