<?php
/**
 * Branding Colors Settings (Incremental Section Test)
 * Purpose: Allow isolated update & test of color palette using SettingsManager::updateBrandingColors
 */
session_start();
require_once __DIR__ . '/../../../../private/gws-universal-config.php';
require_once __DIR__ . '/../../../../private/classes/SettingsManager.php';
require_once __DIR__ . '/../../../../private/classes/SecurityHelper.php';
include_once '../../assets/includes/main.php';

if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['Admin','Developer'])) {
    header('Location: ../../index.php');
    exit();
}

$settingsManager = new SettingsManager($pdo);
$colors = $settingsManager->getBrandingColors();
$csrf_token = SecurityHelper::getCsrfToken('branding_colors');
$message = '';
$message_type = '';
$errors = [];

$colorFields = [
    'brand_primary_color',
    'brand_secondary_color',
    'brand_tertiary_color',
    'brand_quaternary_color',
    'brand_accent_color',
    'brand_warning_color',
    'brand_danger_color',
    'brand_info_color',
    'brand_background_color',
    'brand_text_color',
    'brand_text_light',
    'brand_text_muted'
];

function valid_hex($val) { return preg_match('/^#[0-9A-Fa-f]{6}$/',$val); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!SecurityHelper::validateCsrf('branding_colors', $_POST['csrf_token'] ?? '')) {
        $message = 'Security token invalid. Please retry.';
        $message_type = 'error';
    } else {
        $payload = [];
        foreach ($colorFields as $f) {
            if (isset($_POST[$f]) && $_POST[$f] !== '') {
                $val = trim($_POST[$f]);
                if (!valid_hex($val)) {
                    $errors[$f] = 'Invalid hex color';
                } else {
                    $payload[$f] = strtoupper($val);
                }
            }
        }
        if (!$errors) {
            $ok = $settingsManager->updateBrandingColors($payload, $account_loggedin['username'] ?? 'admin');
            if ($ok) {
                $message = 'Branding colors updated.';
                $message_type = 'success';
                $colors = array_merge($colors, $payload);
            } else {
                $message = 'Update failed (see logs).';
                $message_type = 'error';
            }
        } else {
            $message = 'Validation errors detected.';
            $message_type = 'error';
        }
    }
    $csrf_token = SecurityHelper::getCsrfToken('branding_colors');
}

$page_title = 'Branding Colors';
echo template_admin_header($page_title, 'settings', 'branding');
?>
<div class="content-title">
  <div class="title">
    <div class="icon"><i class="bi bi-palette" style="font-size:18px"></i></div>
    <div class="txt">
      <h2>Branding Colors</h2>
      <p>Incremental section: manage only color palette. Other branding components remain unaffected.</p>
    </div>
  </div>
  <div class="btn-group">
    <a href="../settings_dash.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Dashboard</a>
  </div>
</div>
<?php if ($message): ?>
  <div class="alert alert-<?= $message_type==='success'?'success':'danger' ?>" role="alert">
    <i class="bi bi-<?= $message_type==='success'?'check-circle':'exclamation-triangle' ?>"></i>
    <?= htmlspecialchars($message) ?>
  </div>
<?php endif; ?>
<form method="post" class="settings-form" novalidate>
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
  <div class="row">
    <?php
      $labels = [
        'brand_primary_color'=>'Primary','brand_secondary_color'=>'Secondary','brand_tertiary_color'=>'Tertiary','brand_quaternary_color'=>'Quaternary',
        'brand_accent_color'=>'Accent','brand_warning_color'=>'Warning','brand_danger_color'=>'Danger','brand_info_color'=>'Info',
        'brand_background_color'=>'Background','brand_text_color'=>'Text','brand_text_light'=>'Text Light','brand_text_muted'=>'Text Muted'
      ];
      foreach ($labels as $field=>$label):
        $val = htmlspecialchars($colors[$field] ?? '');
    ?>
    <div class="col-md-3 mb-3">
      <label class="form-label" for="<?= $field ?>"><?= $label ?></label>
      <input type="color" id="<?= $field ?>" name="<?= $field ?>" class="form-control form-control-color" value="<?= $val ?: '#000000' ?>">
      <input type="text" name="<?= $field ?>" value="<?= $val ?>" class="form-control mt-1" placeholder="#FFFFFF" pattern="^#[0-9A-Fa-f]{6}$">
      <?php if (!empty($errors[$field])): ?><div class="text-danger small"><?= htmlspecialchars($errors[$field]) ?></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Colors</button>
    <a href="../settings_dash.php" class="btn btn-secondary"><i class="bi bi-x-lg me-1"></i>Cancel</a>
  </div>
</form>
<style>
.settings-form .form-control-color {height: 42px; width:100%; padding:2px;}
</style>
<?= template_admin_footer(); ?>