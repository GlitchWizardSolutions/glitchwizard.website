<?php
// Features tab content - Migrated from account_feature_settings.php
$features_meta = [
    'registration_enabled' => ['Enable User Registration', 'boolean', 'Allow new users to register accounts'],
    'email_verification_required' => ['Require Email Verification', 'boolean', 'Users must verify their email address before accessing their account'],
    'min_password_length' => ['Minimum Password Length', 'number', 'Minimum number of characters required for passwords'],
    'password_complexity_required' => ['Require Complex Passwords', 'boolean', 'Passwords must contain uppercase, lowercase, numbers, and special characters'],
    'two_factor_auth_enabled' => ['Enable Two-Factor Authentication', 'boolean', 'Require two-factor authentication for login'],
    'social_login_enabled' => ['Enable Social Login', 'boolean', 'Allow users to login with social accounts'],
    'avatar_upload_enabled' => ['Enable Avatar Uploads', 'boolean', 'Allow users to upload profile pictures'],
    'avatar_max_size' => ['Maximum Avatar Size (bytes)', 'number', 'Maximum file size for avatar uploads'],
    'profile_fields_required' => ['Required Profile Fields', 'text', 'Comma-separated list of required profile fields']
];

foreach ($features_meta as $key => $meta): 
    $value = $settings['features'][$key] ?? '';
?>
    <div class="col-md-6 mb-4">
        <div class="form-group">
            <label for="features_<?= $key ?>" class="form-label fw-bold">
                <?= htmlspecialchars($meta[0]) ?>
            </label>
            <?php if (!empty($meta[2])): ?>
                <div class="form-text text-muted mb-2">
                    <?= htmlspecialchars($meta[2]) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($meta[1] === 'boolean'): ?>
                <div class="form-check form-switch">
                    <input type="checkbox" 
                           name="features[<?= $key ?>]" 
                           id="features_<?= $key ?>" 
                           class="form-check-input" 
                           value="1" 
                           <?= $value ? ' checked' : '' ?>>
                    <label class="form-check-label" for="features_<?= $key ?>">
                        Enable <?= htmlspecialchars($meta[0]) ?>
                    </label>
                </div>
            <?php elseif ($meta[1] === 'number'): ?>
                <input type="number" 
                       name="features[<?= $key ?>]" 
                       id="features_<?= $key ?>" 
                       class="form-control" 
                       value="<?= htmlspecialchars($value) ?>">
            <?php else: ?>
                <input type="text" 
                       name="features[<?= $key ?>]" 
                       id="features_<?= $key ?>" 
                       class="form-control" 
                       value="<?= htmlspecialchars($value) ?>">
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
