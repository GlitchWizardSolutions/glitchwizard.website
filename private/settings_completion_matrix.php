<?php
/**
 * Central Settings Completion Matrix + helper functions.
 * Provides a single source of truth for settings completion scoring and diagnostics.
 */

// Prevent redefinition
if (!isset($SETTINGS_COMPLETION_MATRIX)) {
    $SETTINGS_COMPLETION_MATRIX = [
        'business_info' => [ 'table' => 'setting_branding_colors', 'must_have' => ['brand_primary_color','brand_text_color'] ],
        'site_identity' => [ 'table' => 'setting_seo_global', 'must_have' => ['default_title_suffix','default_meta_description'] ],
        'contact_info' => [ 'table' => 'setting_contact_info', 'must_have' => ['contact_email','contact_address'] ],
        'user_accounts' => [ 'table' => 'setting_accounts_config', 'must_have' => ['registration_enabled','password_min_length'] ],
        'ecommerce_setup' => [ 'table' => 'setting_shop_config', 'must_have' => ['currency'] , 'flag' => 'shop_system'],
        'blog_config' => [ 'table' => 'setting_blog_identity', 'must_have' => ['blog_title','author_name'], 'flag' => 'blog_system' ],
        'blog_display' => [ 'table' => 'setting_blog_display', 'must_have' => ['posts_per_page','layout'], 'flag' => 'blog_system' ],
        'system_config' => [ 'table' => 'setting_system_core', 'must_have' => [], 'optional' => true ],
    ];
}

if (!function_exists('setting_is_complete')) {
    function setting_is_complete(PDO $pdo, string $key, array $def, array $flags): bool {
        if (isset($def['flag']) && function_exists('featureEnabled') && !featureEnabled($def['flag'], $flags)) {
            return false;
        }
        $table = $def['table'] ?? null;
        if (!$table) return false;
        try {
            $stmt = $pdo->query('SELECT * FROM `' . str_replace('`','', $table) . '` LIMIT 1');
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) return false;
            if (!empty($def['must_have'])) {
                foreach ($def['must_have'] as $col) {
                    if (!isset($row[$col]) || trim((string)$row[$col]) === '') {
                        return false;
                    }
                }
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}

if (!function_exists('setting_table_exists')) {
    function setting_table_exists(PDO $pdo, string $table): bool {
        try {
            $pdo->query('SELECT 1 FROM `' . str_replace('`','', $table) . '` LIMIT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}

if (!function_exists('get_table_columns')) {
    function get_table_columns(PDO $pdo, string $table): array {
        try {
            $stmt = $pdo->query('DESCRIBE `' . str_replace('`','', $table) . '`');
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            return [];
        }
    }
}

?>
