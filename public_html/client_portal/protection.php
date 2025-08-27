<?php
require_once __DIR__ . '/../../private/access-control.php';

// Check client portal access before allowing any page to load
check_client_portal_access();

// Inject shared brand spinner overlay & JS helper (mirrors admin implementation)
if (!function_exists('echoBrandSpinnerOverlay')) { require_once dirname(__DIR__,2) . '/private/gws-universal-functions.php'; }
if (!defined('PORTAL_BRAND_SPINNER_OVERLAY_EMITTED')) {
	define('PORTAL_BRAND_SPINNER_OVERLAY_EMITTED', true);
	echoBrandSpinnerOverlay(['id'=>'global-spinner-overlay','class'=>'brand-spinner-size-sm','label'=>'Loading']);
	$spinner_inline_sm = getBrandSpinnerHTML(null, ['size'=>'sm','label'=>'Loading','class'=>'align-text-bottom me-1']);
	echo '<script>window.BRAND_SPINNER_STYLE=' . json_encode(getBrandSpinnerStyle()) . ';window.BRAND_SPINNER_INLINE_SM=' . json_encode($spinner_inline_sm) . ';</script>' . "\n";
	echo '<script src="' . htmlspecialchars(WEB_ROOT_URL . '/client_portal/assets/js/brand-spinner.js', ENT_QUOTES) . '"></script>' . "\n";
}
?>
