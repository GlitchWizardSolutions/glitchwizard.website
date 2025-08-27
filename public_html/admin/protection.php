<?php
/* 
 * Admin Access Protection
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: protection.php
 * LOCATION: /public_html/admin/
 * PURPOSE: Central security check for admin panel access
 * DETAILED DESCRIPTION:
 * This file serves as a security gateway for the admin panel, ensuring that
 * only authorized users with appropriate permissions can access administrative
 * functions. It provides centralized access control and security validation
 * for all admin pages.
 * REQUIRED FILES: 
 * - /private/access-control.php
 * - /private/role-definitions.php
 * - /private/role-functions.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Admin access verification
 * - Role-based access control
 * - Security validation
 * - Session protection
 * - Access logging
 */

require_once __DIR__ . '/../../private/access-control.php';

// Check admin access before allowing any admin page to load
check_admin_access();
?>
<?php
// Provide global spinner overlay only for full page (non-AJAX, non-JSON) requests
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])==='xmlhttprequest';
$accept = $_SERVER['HTTP_ACCEPT'] ?? '';
$wantsJson = stripos($accept,'application/json') !== false;
if (!defined('SUPPRESS_BRAND_SPINNER') && !$isAjax && !$wantsJson) {
	if (!function_exists('echoBrandSpinnerOverlay')) { require_once PROJECT_ROOT . '/private/gws-universal-functions.php'; }
	if (!defined('BRAND_SPINNER_OVERLAY_EMITTED')) {
		define('BRAND_SPINNER_OVERLAY_EMITTED', true);
		echoBrandSpinnerOverlay(['id'=>'global-spinner-overlay','class'=>'brand-spinner-size-sm','label'=>'Loading']);
		$spinner_inline_sm = getBrandSpinnerHTML(null, ['size'=>'sm','label'=>'Loading','class'=>'align-text-bottom me-1']);
		echo '<script>window.BRAND_SPINNER_STYLE=' . json_encode(getBrandSpinnerStyle()) . ';window.BRAND_SPINNER_INLINE_SM=' . json_encode($spinner_inline_sm) . ';</script>' . "\n";
		echo '<script src="' . htmlspecialchars(WEB_ROOT_URL . '/admin/assets/js/brand-spinner.js', ENT_QUOTES) . '"></script>' . "\n";
	}
}
?>
