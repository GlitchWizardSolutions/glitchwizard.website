<?php
// General tab content - Global account settings
$general_meta = [
    'notification_email_welcome' => ['Send Welcome Emails', 'boolean', 'Send welcome email to new users after registration'],
    'notification_email_password_reset' => ['Send Password Reset Emails', 'boolean', 'Send email notifications for password reset requests'],
    'notification_email_login_alerts' => ['Send Login Alert Emails', 'boolean', 'Send email alerts for new login sessions'],
    'smtp_host' => ['SMTP Host', 'text', 'SMTP server hostname'],
    'smtp_port' => ['SMTP Port', 'number', 'SMTP server port'],
    'smtp_user' => ['SMTP Username', 'text', 'SMTP authentication username'],
    'smtp_pass' => ['SMTP Password', 'password', 'SMTP authentication password'],
    'smtp_secure' => ['SMTP Security', 'select', 'SMTP connection security', ['none', 'ssl', 'tls']],
    'mail_from_name' => ['From Name', 'text', 'Name to show in the From field of emails'],
    'mail_from_email' => ['From Email', 'text', 'Email address to send from']
];

foreach ($general_meta as $key => $meta): 
    $value = $settings['general'][$key] ?? '';
?>
    <div class="col-md-6 mb-4">
        <div class="form-group">
            <label for="general_<?= $key ?>" class="form-label fw-bold">
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
                           name="general[<?= $key ?>]" 
                           id="general_<?= $key ?>" 
                           class="form-check-input" 
                           value="1" 
                           <?= $value ? ' checked' : '' ?>>
                    <label class="form-check-label" for="general_<?= $key ?>">
                        Enable <?= htmlspecialchars($meta[0]) ?>
                    </label>
                </div>
            <?php elseif ($meta[1] === 'select'): ?>
                <select name="general[<?= $key ?>]" 
                        id="general_<?= $key ?>" 
                        class="form-select">
                    <?php foreach ($meta[3] as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" 
                                <?= $value === $option ? ' selected' : '' ?>>
                            <?= htmlspecialchars($option) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ($meta[1] === 'password'): ?>
                <input type="password" 
                       name="general[<?= $key ?>]" 
                       id="general_<?= $key ?>" 
                       class="form-control" 
                       value="<?= htmlspecialchars($value) ?>"
                       autocomplete="new-password">
            <?php else: ?>
                <input type="text" 
                       name="general[<?= $key ?>]" 
                       id="general_<?= $key ?>" 
                       class="form-control" 
                       value="<?= htmlspecialchars($value) ?>">
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
