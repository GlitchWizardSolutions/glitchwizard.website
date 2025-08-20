<?php
/**
 * REMEMBER ME FUNCTIONALITY
 * Functions for handling persistent login tokens
 */

/**
 * Create a new remember-me token
 * @param PDO $pdo Database connection
 * @param int $account_id User's account ID
 * @return array Token data [selector, validator]
 * @throws InvalidArgumentException If account_id is invalid
 */
function createRememberMeToken($pdo, $account_id) {
    try {
        // Validate account_id
        if (!is_numeric($account_id) || $account_id <= 0) {
            throw new InvalidArgumentException('Invalid account ID');
        }

        // Check if account exists
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM accounts WHERE id = ?');
        $stmt->execute([$account_id]);
        if ($stmt->fetchColumn() == 0) {
            throw new InvalidArgumentException('Account does not exist');
        }

        // Rate limiting: Check for recent token creations
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM auth_tokens WHERE account_id = ? AND created > DATE_SUB(NOW(), INTERVAL 1 HOUR)');
        $stmt->execute([$account_id]);
        if ($stmt->fetchColumn() >= 5) { // Maximum 5 tokens per hour
            throw new Exception('Too many token creation attempts');
        }

        // Limit total tokens per user
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM auth_tokens WHERE account_id = ?');
        $stmt->execute([$account_id]);
        if ($stmt->fetchColumn() >= 5) { // Maximum 5 active tokens per user
            // Remove oldest token
            $stmt = $pdo->prepare('DELETE FROM auth_tokens WHERE account_id = ? ORDER BY created ASC LIMIT 1');
            $stmt->execute([$account_id]);
        }

        // Generate random tokens
        $selector = bin2hex(random_bytes(6));
        $validator = bin2hex(random_bytes(32));
        
        // Hash the validator for storage
        $hashedValidator = password_hash($validator, PASSWORD_DEFAULT);
        
        // Set expiry to 30 days from now
        $expires = new DateTime();
        $expires->modify('+30 days');
        
        // Store token in database
        $stmt = $pdo->prepare('INSERT INTO auth_tokens (account_id, selector, token, expires) VALUES (?, ?, ?, ?)');
        $stmt->execute([$account_id, $selector, $hashedValidator, $expires->format('Y-m-d H:i:s')]);
        
        // Clean up any expired tokens for this user
        $stmt = $pdo->prepare('DELETE FROM auth_tokens WHERE account_id = ? AND expires < NOW()');
        $stmt->execute([$account_id]);
        
        return [
            'selector' => $selector,
            'validator' => $validator
        ];
    } catch (Exception $e) {
        error_log('Failed to create remember-me token: ' . $e->getMessage());
        return [
            'selector' => '',
            'validator' => ''
        ];
    }
}

/**
 * Set the remember-me cookie
 * @param array $token Token data from createRememberMeToken
 * @return bool Success
 */
function setRememberMeCookie($token) {
    if (empty($token['selector']) || empty($token['validator'])) {
        return false;
    }
    
    // Create cookie value: selector:validator
    $cookie = $token['selector'] . ':' . $token['validator'];
    
    // Set cookie for 30 days
    return setcookie(
        'rememberme',
        $cookie,
        [
            'expires' => time() + (30 * 24 * 60 * 60),
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]
    );
}

/**
 * Verify a remember-me token
 * @param PDO $pdo Database connection
 * @param string $cookie Cookie value
 * @return array|bool Account data if valid, false if not
 */
function verifyRememberMeToken($pdo, $cookie) {
    if (empty($cookie) || !is_string($cookie)) {
        return false;
    }
    
    // Validate cookie format
    if (!preg_match('/^[a-f0-9]{12}:[a-f0-9]{64}$/', $cookie)) {
        return false;
    }
    
    // Split cookie into selector and validator
    $parts = explode(':', $cookie);
    if (count($parts) !== 2) {
        return false;
    }
    
    list($selector, $validator) = $parts;
    
    // Rate limiting: Check for failed attempts
    $attempts_key = 'remember_me_attempts_' . hash('sha256', $_SERVER['REMOTE_ADDR']);
    if (isset($_SESSION[$attempts_key]) && $_SESSION[$attempts_key]['attempts'] >= 5 && 
        time() - $_SESSION[$attempts_key]['first_attempt'] < 3600) {
        return false;
    }
    
    try {
        // Get token from database
        $stmt = $pdo->prepare('SELECT t.*, a.* FROM auth_tokens t 
                              JOIN accounts a ON t.account_id = a.id 
                              WHERE t.selector = ? AND t.expires > NOW()');
        $stmt->execute([$selector]);
        $token = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$token) {
            return false;
        }
        
        // Track verification attempts
        if (!isset($_SESSION[$attempts_key])) {
            $_SESSION[$attempts_key] = [
                'attempts' => 0,
                'first_attempt' => time()
            ];
        }
        
        // Verify the validator
        if (!password_verify($validator, $token['token'])) {
            $_SESSION[$attempts_key]['attempts']++;
            if (time() - $_SESSION[$attempts_key]['first_attempt'] >= 3600) {
                $_SESSION[$attempts_key] = [
                    'attempts' => 1,
                    'first_attempt' => time()
                ];
            }
            return false;
        }
        
        // Reset attempts on successful verification
        unset($_SESSION[$attempts_key]);
        
        // Token is valid, return account data
        return [
            'id' => $token['account_id'],
            'username' => $token['username'],
            'role' => $token['role']
        ];
    } catch (Exception $e) {
        error_log('Failed to verify remember-me token: ' . $e->getMessage());
        return false;
    }
}

/**
 * Delete all remember-me tokens for a user
 * @param PDO $pdo Database connection
 * @param int $account_id User's account ID
 * @return bool Success
 */
function deleteRememberMeTokens($pdo, $account_id) {
    try {
        $stmt = $pdo->prepare('DELETE FROM auth_tokens WHERE account_id = ?');
        return $stmt->execute([$account_id]);
    } catch (Exception $e) {
        error_log('Failed to delete remember-me tokens: ' . $e->getMessage());
        return false;
    }
}

/**
 * Clean up expired remember-me tokens
 * @param PDO $pdo Database connection
 */
function cleanupRememberMeTokens($pdo) {
    try {
        $stmt = $pdo->prepare('DELETE FROM auth_tokens WHERE expires < NOW()');
        $stmt->execute();
    } catch (Exception $e) {
        error_log('Failed to cleanup remember-me tokens: ' . $e->getMessage());
    }
}
