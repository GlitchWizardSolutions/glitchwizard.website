<?php
// System tab content - Migrated from accounts_system_settings.php
$system_meta = [
    'max_login_attempts' => ['Maximum Login Attempts', 'number', 'Number of failed login attempts before account lockout'],
    'login_lockout_duration' => ['Lockout Duration (seconds)', 'number', 'How long to lock accounts after failed login attempts'],
    'session_timeout' => ['Session Timeout (seconds)', 'number', 'How long user sessions remain active without activity'],
    'rememberme_duration' => ['Remember Me Duration (seconds)', 'number', 'How long "Remember Me" sessions last'],
    'password_reset_token_expiry' => ['Password Reset Token Expiry (seconds)', 'number', 'How long password reset tokens are valid'],
    'auto_cleanup_inactive_days' => ['Auto Cleanup Inactive Days', 'number', 'Days before inactive accounts are cleaned up'],
    'gdpr_compliance_enabled' => ['Enable GDPR Compliance', 'boolean', 'Enable GDPR compliance features'],
    'audit_log_enabled' => ['Enable Audit Log', 'boolean', 'Log account system actions for auditing'],
    'api_access_enabled' => ['Enable API Access', 'boolean', 'Allow API access for accounts']
];

foreach ($system_meta as $key => $meta): 
    $value = $settings['system'][$key] ?? '';
?>
    <div class="col-md-6 mb-4">
        <div class="form-group">
            <label for="system_<?= $key ?>" class="form-label fw-bold">
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
                           name="system[<?= $key ?>]" 
                           id="system_<?= $key ?>" 
                           class="form-check-input" 
                           value="1" 
                           <?= $value ? ' checked' : '' ?>>
                    <label class="form-check-label" for="system_<?= $key ?>">
                        Enable <?= htmlspecialchars($meta[0]) ?>
                    </label>
                </div>
            <?php elseif ($meta[1] === 'number'): ?>
                <input type="number" 
                       name="system[<?= $key ?>]" 
                       id="system_<?= $key ?>" 
                       class="form-control" 
                       value="<?= htmlspecialchars($value) ?>">
            <?php else: ?>
                <input type="text" 
                       name="system[<?= $key ?>]" 
                       id="system_<?= $key ?>" 
                       class="form-control" 
                       value="<?= htmlspecialchars($value) ?>">
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
