<?php
/*
FILE NAME  : shop_settings.php
LOCATION   : admin/settings/shop_settings.php
DESCRIPTION: Shop system configuration interface
FUNCTION   : Centralized configuration management for shop settings
CREATED    : August 2025 - Shop Integration Step 3
*/

// Include admin authentication and dependencies
include '../assets/includes/main.php';

// Handle form submissions
if ($_POST) {
    $success_message = '';
    $error_message = '';
    
    try {
        // General Shop Settings
        if (isset($_POST['shop_general_settings'])) {
            $settings = [
                'shop_site_name' => $_POST['shop_site_name'] ?? '',
                'shop_currency_code' => $_POST['shop_currency_code'] ?? '&dollar;',
                'shop_weight_unit' => $_POST['shop_weight_unit'] ?? 'lbs',
                'shop_account_required' => isset($_POST['shop_account_required']) ? '1' : '0',
                'shop_rewrite_url' => isset($_POST['shop_rewrite_url']) ? '1' : '0',
                'shop_featured_image' => $_POST['shop_featured_image'] ?? 'uploads/featured-image.jpg',
                'shop_template_editor' => $_POST['shop_template_editor'] ?? 'tinymce',
                'shop_default_payment_status' => $_POST['shop_default_payment_status'] ?? 'Completed'
            ];
            
            foreach ($settings as $key => $value) {
                updateShopSetting($key, $value);
            }
            $success_message = 'General shop settings updated successfully!';
        }
        
        // PayPal Settings
        if (isset($_POST['paypal_settings'])) {
            $settings = [
                'paypal_enabled' => isset($_POST['paypal_enabled']) ? '1' : '0',
                'paypal_email' => $_POST['paypal_email'] ?? '',
                'paypal_testmode' => isset($_POST['paypal_testmode']) ? '1' : '0',
                'paypal_currency' => $_POST['paypal_currency'] ?? 'USD',
                'paypal_ipn_url' => $_POST['paypal_ipn_url'] ?? '',
                'paypal_cancel_url' => $_POST['paypal_cancel_url'] ?? '',
                'paypal_return_url' => $_POST['paypal_return_url'] ?? ''
            ];
            
            foreach ($settings as $key => $value) {
                updateShopSetting($key, $value);
            }
            $success_message = 'PayPal settings updated successfully!';
        }
        
        // Stripe Settings
        if (isset($_POST['stripe_settings'])) {
            $settings = [
                'stripe_enabled' => isset($_POST['stripe_enabled']) ? '1' : '0',
                'stripe_publish_key' => $_POST['stripe_publish_key'] ?? '',
                'stripe_secret_key' => $_POST['stripe_secret_key'] ?? '',
                'stripe_webhook_secret' => $_POST['stripe_webhook_secret'] ?? ''
            ];
            
            foreach ($settings as $key => $value) {
                updateShopSetting($key, $value);
            }
            $success_message = 'Stripe settings updated successfully!';
        }
        
        // Coinbase Settings
        if (isset($_POST['coinbase_settings'])) {
            $settings = [
                'coinbase_enabled' => isset($_POST['coinbase_enabled']) ? '1' : '0',
                'coinbase_api_key' => $_POST['coinbase_api_key'] ?? '',
                'coinbase_currency' => $_POST['coinbase_currency'] ?? 'USD',
                'coinbase_return_url' => $_POST['coinbase_return_url'] ?? '',
                'coinbase_cancel_url' => $_POST['coinbase_cancel_url'] ?? ''
            ];
            
            foreach ($settings as $key => $value) {
                updateShopSetting($key, $value);
            }
            $success_message = 'Coinbase settings updated successfully!';
        }
        
    } catch (Exception $e) {
        $error_message = 'Error updating settings: ' . $e->getMessage();
    }
}

// Function to update shop settings
function updateShopSetting($key, $value) {
    global $pdo;
    
    // Check if setting exists
    $stmt = $pdo->prepare('SELECT id FROM shop_settings WHERE setting_key = ?');
    $stmt->execute([$key]);
    
    if ($stmt->fetch()) {
        // Update existing setting
        $stmt = $pdo->prepare('UPDATE shop_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?');
        $stmt->execute([$value, $key]);
    } else {
        // Insert new setting
        $stmt = $pdo->prepare('INSERT INTO shop_settings (setting_key, setting_value, created_at, updated_at) VALUES (?, ?, NOW(), NOW())');
        $stmt->execute([$key, $value]);
    }
}

// Function to get shop setting
function getShopSetting($key, $default = '') {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare('SELECT setting_value FROM shop_settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

// Load current settings
$current_settings = [
    // General Settings
    'shop_site_name' => getShopSetting('shop_site_name', 'Shopping Cart'),
    'shop_currency_code' => getShopSetting('shop_currency_code', '&dollar;'),
    'shop_weight_unit' => getShopSetting('shop_weight_unit', 'lbs'),
    'shop_account_required' => getShopSetting('shop_account_required', '0'),
    'shop_rewrite_url' => getShopSetting('shop_rewrite_url', '0'),
    'shop_featured_image' => getShopSetting('shop_featured_image', 'uploads/featured-image.jpg'),
    'shop_template_editor' => getShopSetting('shop_template_editor', 'tinymce'),
    'shop_default_payment_status' => getShopSetting('shop_default_payment_status', 'Completed'),
    
    // PayPal Settings
    'paypal_enabled' => getShopSetting('paypal_enabled', '1'),
    'paypal_email' => getShopSetting('paypal_email', 'payments@example.com'),
    'paypal_testmode' => getShopSetting('paypal_testmode', '1'),
    'paypal_currency' => getShopSetting('paypal_currency', 'USD'),
    'paypal_ipn_url' => getShopSetting('paypal_ipn_url', ''),
    'paypal_cancel_url' => getShopSetting('paypal_cancel_url', ''),
    'paypal_return_url' => getShopSetting('paypal_return_url', ''),
    
    // Stripe Settings
    'stripe_enabled' => getShopSetting('stripe_enabled', '1'),
    'stripe_publish_key' => getShopSetting('stripe_publish_key', ''),
    'stripe_secret_key' => getShopSetting('stripe_secret_key', ''),
    'stripe_webhook_secret' => getShopSetting('stripe_webhook_secret', ''),
    
    // Coinbase Settings
    'coinbase_enabled' => getShopSetting('coinbase_enabled', '0'),
    'coinbase_api_key' => getShopSetting('coinbase_api_key', ''),
    'coinbase_currency' => getShopSetting('coinbase_currency', 'USD'),
    'coinbase_return_url' => getShopSetting('coinbase_return_url', ''),
    'coinbase_cancel_url' => getShopSetting('coinbase_cancel_url', '')
];

// Create database table if it doesn't exist
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS shop_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(255) NOT NULL UNIQUE,
        setting_value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
} catch (Exception $e) {
    // Table might already exist
}

echo template_admin_header('Shop Settings', 'shop', 'settings');
?>

<div class="content-title" id="main-shop-settings" role="banner" aria-label="Shop Settings Header">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" viewBox="0 0 576 512" fill="currentColor">
                <path d="M547.6 103.8L490.3 13.1C485.2 5 476.1 0 466.4 0H109.6C99.9 0 90.8 5 85.7 13.1L28.3 103.8c-29.6 46.8-3.4 111.9 51.9 119.4c4 .5 8.1 .8 12.1 .8c26.1 0 49.3-11.4 65.2-29c15.9 17.6 39.1 29 65.2 29c26.1 0 49.3-11.4 65.2-29c15.9 17.6 39.1 29 65.2 29c26.2 0 49.3-11.4 65.2-29c16 17.6 39.1 29 65.2 29c4 0 8.1-.3 12.1-.8c55.5-7.4 81.8-72.5 52.1-119.4zM499.7 254.9l-.1 0c-5.3 .7-10.7 1.1-16.2 1.1c-12.4 0-24.3-1.9-35.4-5.3V384H128V250.6c-11.2 3.5-23.2 5.4-35.6 5.4c-5.5 0-11-.4-16.3-1.1l-.1 0c-4.1-.6-8.1-1.3-12-2.3V384v64c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V384 252.6c-4 1-8 1.8-12.3 2.3z"/>
            </svg>
        </div>
        <div class="txt">
            <h2>Shop Settings</h2>
            <p>Configure shop system settings, payment gateways, and store preferences.</p>
        </div>
    </div>
</div>
<br>

<?php if (isset($success_message)): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success_message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error_message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="shopSettingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                            <i class="bi bi-gear me-1"></i>General
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="paypal-tab" data-bs-toggle="tab" data-bs-target="#paypal" type="button" role="tab">
                            <i class="bi bi-paypal me-1"></i>PayPal
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="stripe-tab" data-bs-toggle="tab" data-bs-target="#stripe" type="button" role="tab">
                            <i class="bi bi-credit-card-2-front me-1"></i>Stripe
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="coinbase-tab" data-bs-toggle="tab" data-bs-target="#coinbase" type="button" role="tab">
                            <i class="bi bi-currency-bitcoin me-1"></i>Coinbase
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="shopSettingsContent">
                    
                    <!-- General Settings Tab -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <form method="post" action="">
                            <div class="d-flex gap-2 pb-3 border-bottom mb-3">
                                <button type="submit" name="shop_general_settings" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i>Save General Settings
                                </button>
                                <a href="../shop_system/shop_dash.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Shop Dashboard
                                </a>
                            </div>
                            
                            <fieldset class="mb-4">
                                <legend class="h6 text-primary border-bottom pb-1">Store Information</legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="shop_site_name" class="form-label">Store Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="shop_site_name" name="shop_site_name" 
                                                   value="<?= htmlspecialchars($current_settings['shop_site_name']) ?>" required>
                                            <div class="form-text">The name of your online store</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="shop_currency_code" class="form-label">Currency Symbol</label>
                                            <select class="form-select" id="shop_currency_code" name="shop_currency_code">
                                                <option value="&dollar;" <?= $current_settings['shop_currency_code'] === '&dollar;' ? 'selected' : '' ?>>$ (Dollar)</option>
                                                <option value="&euro;" <?= $current_settings['shop_currency_code'] === '&euro;' ? 'selected' : '' ?>>€ (Euro)</option>
                                                <option value="&pound;" <?= $current_settings['shop_currency_code'] === '&pound;' ? 'selected' : '' ?>>£ (Pound)</option>
                                                <option value="&yen;" <?= $current_settings['shop_currency_code'] === '&yen;' ? 'selected' : '' ?>>¥ (Yen)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="shop_weight_unit" class="form-label">Weight Unit</label>
                                            <select class="form-select" id="shop_weight_unit" name="shop_weight_unit">
                                                <option value="lbs" <?= $current_settings['shop_weight_unit'] === 'lbs' ? 'selected' : '' ?>>Pounds (lbs)</option>
                                                <option value="kg" <?= $current_settings['shop_weight_unit'] === 'kg' ? 'selected' : '' ?>>Kilograms (kg)</option>
                                                <option value="oz" <?= $current_settings['shop_weight_unit'] === 'oz' ? 'selected' : '' ?>>Ounces (oz)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="shop_default_payment_status" class="form-label">Default Payment Status</label>
                                            <select class="form-select" id="shop_default_payment_status" name="shop_default_payment_status">
                                                <option value="Completed" <?= $current_settings['shop_default_payment_status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                <option value="Pending" <?= $current_settings['shop_default_payment_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="Processing" <?= $current_settings['shop_default_payment_status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="mb-4">
                                <legend class="h6 text-primary border-bottom pb-1">Store Options</legend>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="shop_account_required" name="shop_account_required"
                                                       <?= $current_settings['shop_account_required'] === '1' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="shop_account_required">
                                                    Account Required for Checkout
                                                </label>
                                            </div>
                                            <div class="form-text">Require customers to create accounts before purchasing</div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="shop_rewrite_url" name="shop_rewrite_url"
                                                       <?= $current_settings['shop_rewrite_url'] === '1' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="shop_rewrite_url">
                                                    Enable URL Rewriting
                                                </label>
                                            </div>
                                            <div class="form-text">Use friendly URLs (requires .htaccess configuration)</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="shop_template_editor" class="form-label">Template Editor</label>
                                            <select class="form-select" id="shop_template_editor" name="shop_template_editor">
                                                <option value="tinymce" <?= $current_settings['shop_template_editor'] === 'tinymce' ? 'selected' : '' ?>>TinyMCE</option>
                                                <option value="textarea" <?= $current_settings['shop_template_editor'] === 'textarea' ? 'selected' : '' ?>>Plain Textarea</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="shop_featured_image" class="form-label">Featured Image Path</label>
                                            <input type="text" class="form-control" id="shop_featured_image" name="shop_featured_image"
                                                   value="<?= htmlspecialchars($current_settings['shop_featured_image']) ?>">
                                            <div class="form-text">Path to default featured image for homepage</div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <div class="d-flex gap-2 pt-3 border-top mt-4">
                                <button type="submit" name="shop_general_settings" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i>Save General Settings
                                </button>
                                <a href="../shop_system/shop_dash.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Shop Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- PayPal Settings Tab -->
                    <div class="tab-pane fade" id="paypal" role="tabpanel">
                        <form method="post" action="">
                            <div class="d-flex gap-2 pb-3 border-bottom mb-3">
                                <button type="submit" name="paypal_settings" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i>Save PayPal Settings
                                </button>
                                <a href="https://developer.paypal.com" target="_blank" class="btn btn-outline-info">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>PayPal Developer
                                </a>
                            </div>
                            
                            <fieldset class="mb-4">
                                <legend class="h6 text-primary border-bottom pb-1">PayPal Configuration</legend>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="paypal_enabled" name="paypal_enabled"
                                                       <?= $current_settings['paypal_enabled'] === '1' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="paypal_enabled">
                                                    Enable PayPal Payments
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="paypal_email" class="form-label">PayPal Business Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" id="paypal_email" name="paypal_email"
                                                   value="<?= htmlspecialchars($current_settings['paypal_email']) ?>" required>
                                            <div class="form-text">Your PayPal business account email</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="paypal_currency" class="form-label">PayPal Currency</label>
                                            <select class="form-select" id="paypal_currency" name="paypal_currency">
                                                <option value="USD" <?= $current_settings['paypal_currency'] === 'USD' ? 'selected' : '' ?>>USD</option>
                                                <option value="EUR" <?= $current_settings['paypal_currency'] === 'EUR' ? 'selected' : '' ?>>EUR</option>
                                                <option value="GBP" <?= $current_settings['paypal_currency'] === 'GBP' ? 'selected' : '' ?>>GBP</option>
                                                <option value="CAD" <?= $current_settings['paypal_currency'] === 'CAD' ? 'selected' : '' ?>>CAD</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="paypal_testmode" name="paypal_testmode"
                                                       <?= $current_settings['paypal_testmode'] === '1' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="paypal_testmode">
                                                    Enable Test Mode (Sandbox)
                                                </label>
                                            </div>
                                            <div class="form-text">Use PayPal sandbox for testing. Disable for production.</div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <fieldset class="mb-4">
                                <legend class="h6 text-primary border-bottom pb-1">PayPal URLs</legend>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="paypal_ipn_url" class="form-label">IPN URL</label>
                                            <input type="url" class="form-control" id="paypal_ipn_url" name="paypal_ipn_url"
                                                   value="<?= htmlspecialchars($current_settings['paypal_ipn_url']) ?>">
                                            <div class="form-text">URL for PayPal Instant Payment Notifications</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="paypal_return_url" class="form-label">Return URL</label>
                                            <input type="url" class="form-control" id="paypal_return_url" name="paypal_return_url"
                                                   value="<?= htmlspecialchars($current_settings['paypal_return_url']) ?>">
                                            <div class="form-text">URL customers return to after payment</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="paypal_cancel_url" class="form-label">Cancel URL</label>
                                            <input type="url" class="form-control" id="paypal_cancel_url" name="paypal_cancel_url"
                                                   value="<?= htmlspecialchars($current_settings['paypal_cancel_url']) ?>">
                                            <div class="form-text">URL customers return to if they cancel payment</div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <div class="d-flex gap-2 pt-3 border-top mt-4">
                                <button type="submit" name="paypal_settings" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i>Save PayPal Settings
                                </button>
                                <a href="https://developer.paypal.com" target="_blank" class="btn btn-outline-info">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>PayPal Developer
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Stripe Settings Tab -->
                    <div class="tab-pane fade" id="stripe" role="tabpanel">
                        <form method="post" action="">
                            <div class="d-flex gap-2 pb-3 border-bottom mb-3">
                                <button type="submit" name="stripe_settings" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i>Save Stripe Settings
                                </button>
                                <a href="https://dashboard.stripe.com" target="_blank" class="btn btn-outline-info">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Stripe Dashboard
                                </a>
                            </div>
                            
                            <fieldset class="mb-4">
                                <legend class="h6 text-primary border-bottom pb-1">Stripe Configuration</legend>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="stripe_enabled" name="stripe_enabled"
                                                       <?= $current_settings['stripe_enabled'] === '1' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="stripe_enabled">
                                                    Enable Stripe Payments
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stripe_publish_key" class="form-label">Publishable Key</label>
                                            <input type="text" class="form-control" id="stripe_publish_key" name="stripe_publish_key"
                                                   value="<?= htmlspecialchars($current_settings['stripe_publish_key']) ?>">
                                            <div class="form-text">Your Stripe publishable key (starts with pk_)</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stripe_secret_key" class="form-label">Secret Key</label>
                                            <input type="password" class="form-control" id="stripe_secret_key" name="stripe_secret_key"
                                                   value="<?= htmlspecialchars($current_settings['stripe_secret_key']) ?>">
                                            <div class="form-text">Your Stripe secret key (starts with sk_)</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="stripe_webhook_secret" class="form-label">Webhook Secret</label>
                                            <input type="password" class="form-control" id="stripe_webhook_secret" name="stripe_webhook_secret"
                                                   value="<?= htmlspecialchars($current_settings['stripe_webhook_secret']) ?>">
                                            <div class="form-text">Your Stripe webhook endpoint secret (starts with whsec_)</div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <div class="d-flex gap-2 pt-3 border-top mt-4">
                                <button type="submit" name="stripe_settings" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i>Save Stripe Settings
                                </button>
                                <a href="https://dashboard.stripe.com" target="_blank" class="btn btn-outline-info">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Stripe Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Coinbase Settings Tab -->
                    <div class="tab-pane fade" id="coinbase" role="tabpanel">
                        <form method="post" action="">
                            <div class="d-flex gap-2 pb-3 border-bottom mb-3">
                                <button type="submit" name="coinbase_settings" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i>Save Coinbase Settings
                                </button>
                                <a href="https://commerce.coinbase.com" target="_blank" class="btn btn-outline-info">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Coinbase Commerce
                                </a>
                            </div>
                            
                            <fieldset class="mb-4">
                                <legend class="h6 text-primary border-bottom pb-1">Coinbase Configuration</legend>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="coinbase_enabled" name="coinbase_enabled"
                                                       <?= $current_settings['coinbase_enabled'] === '1' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="coinbase_enabled">
                                                    Enable Coinbase Payments
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="coinbase_api_key" class="form-label">API Key</label>
                                            <input type="password" class="form-control" id="coinbase_api_key" name="coinbase_api_key"
                                                   value="<?= htmlspecialchars($current_settings['coinbase_api_key']) ?>">
                                            <div class="form-text">Your Coinbase Commerce API key</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="coinbase_currency" class="form-label">Currency</label>
                                            <select class="form-select" id="coinbase_currency" name="coinbase_currency">
                                                <option value="USD" <?= $current_settings['coinbase_currency'] === 'USD' ? 'selected' : '' ?>>USD</option>
                                                <option value="EUR" <?= $current_settings['coinbase_currency'] === 'EUR' ? 'selected' : '' ?>>EUR</option>
                                                <option value="BTC" <?= $current_settings['coinbase_currency'] === 'BTC' ? 'selected' : '' ?>>BTC</option>
                                                <option value="ETH" <?= $current_settings['coinbase_currency'] === 'ETH' ? 'selected' : '' ?>>ETH</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="coinbase_return_url" class="form-label">Return URL</label>
                                            <input type="url" class="form-control" id="coinbase_return_url" name="coinbase_return_url"
                                                   value="<?= htmlspecialchars($current_settings['coinbase_return_url']) ?>">
                                            <div class="form-text">URL customers return to after payment</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="coinbase_cancel_url" class="form-label">Cancel URL</label>
                                            <input type="url" class="form-control" id="coinbase_cancel_url" name="coinbase_cancel_url"
                                                   value="<?= htmlspecialchars($current_settings['coinbase_cancel_url']) ?>">
                                            <div class="form-text">URL customers return to if they cancel payment</div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            
                            <div class="d-flex gap-2 pt-3 border-top mt-4">
                                <button type="submit" name="coinbase_settings" class="btn btn-success">
                                    <i class="bi bi-save me-1"></i>Save Coinbase Settings
                                </button>
                                <a href="https://commerce.coinbase.com" target="_blank" class="btn btn-outline-info">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Coinbase Commerce
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo template_admin_footer(); ?>
