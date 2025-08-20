<?php
/**
 * Session cleanup utility script
 * Run this periodically via cron to clean up expired sessions and tokens
 */

require_once __DIR__ . '/gws-universal-config.php';
require_once __DIR__ . '/remember-me-functions.php';

try {
    // Clean up expired remember-me tokens
    cleanupRememberMeTokens($pdo);
    
    // Clean up expired sessions from the database
    $stmt = $pdo->prepare('UPDATE accounts SET last_seen = NULL WHERE last_seen < DATE_SUB(NOW(), INTERVAL 24 HOUR)');
    $stmt->execute();
    
    echo "Session cleanup completed successfully.\n";
} catch (Exception $e) {
    error_log('Session cleanup failed: ' . $e->getMessage());
    echo "Session cleanup failed. Check error log for details.\n";
}
