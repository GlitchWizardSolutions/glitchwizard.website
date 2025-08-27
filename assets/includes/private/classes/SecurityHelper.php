<?php
/**
 * SecurityHelper
 * Centralized CSRF token generation/validation and input validation utilities.
 * Phase 0 introduction (2025-08-20)
 */
class SecurityHelper {
    const CSRF_NAMESPACE = 'csrf_tokens';
    const TOKEN_TTL = 7200; // 2 hours

    public static function ensureSession() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function generateCsrfToken($form_key) {
        self::ensureSession();
        if (!isset($_SESSION[self::CSRF_NAMESPACE])) {
            $_SESSION[self::CSRF_NAMESPACE] = [];
        }
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::CSRF_NAMESPACE][$form_key] = [
            'token' => $token,
            'created' => time()
        ];
        return $token;
    }

    public static function getCsrfToken($form_key) {
        self::ensureSession();
        if (isset($_SESSION[self::CSRF_NAMESPACE][$form_key])) {
            $entry = $_SESSION[self::CSRF_NAMESPACE][$form_key];
            if (time() - $entry['created'] < self::TOKEN_TTL) {
                return $entry['token'];
            }
        }
        return self::generateCsrfToken($form_key);
    }

    public static function validateCsrf($form_key, $submitted_token) {
        self::ensureSession();
        if (!isset($_SESSION[self::CSRF_NAMESPACE][$form_key])) {
            return false;
        }
        $entry = $_SESSION[self::CSRF_NAMESPACE][$form_key];
        $valid = hash_equals($entry['token'], $submitted_token ?? '');
        // Rotate token after successful validation to reduce replay
        if ($valid) {
            unset($_SESSION[self::CSRF_NAMESPACE][$form_key]);
        }
        return $valid;
    }

    /** Basic field validators **/
    public static function sanitizeString($value, $max = 500) {
        $value = trim((string)$value);
        if (strlen($value) > $max) { $value = substr($value, 0, $max); }
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeEmail($value) {
        $value = filter_var(trim($value), FILTER_SANITIZE_EMAIL);
        return filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : '';
    }

    public static function sanitizeUrl($value) {
        $value = filter_var(trim($value), FILTER_SANITIZE_URL);
        return filter_var($value, FILTER_VALIDATE_URL) ? $value : '';
    }

    public static function toBoolInt($value) { return !empty($value) ? 1 : 0; }

    public static function validatePayload(array $spec, array $input, &$errors = []) {
        $out = [];
        foreach ($spec as $field => $rule) {
            $raw = $input[$field] ?? null;
            switch ($rule['type']) {
                case 'email': $val = self::sanitizeEmail($raw); break;
                case 'url': $val = self::sanitizeUrl($raw); break;
                case 'int': $val = (int)$raw; break;
                case 'bool': $val = self::toBoolInt($raw); break;
                case 'string': default: $val = self::sanitizeString($raw, $rule['max'] ?? 500); break;
            }
            if (($rule['required'] ?? false) && ($val === '' || $val === null)) {
                $errors[$field] = 'Required';
            }
            if (isset($rule['min']) && is_int($val) && $val < $rule['min']) {
                $errors[$field] = 'Below minimum';
            }
            if (isset($rule['max']) && is_int($val) && $val > $rule['max']) {
                $errors[$field] = 'Above maximum';
            }
            $out[$field] = $val;
        }
        return $out;
    }
}
