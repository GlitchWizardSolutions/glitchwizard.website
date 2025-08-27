<?php
// Configuration tab content - Account-specific settings
$config_meta = [
    'username_min_length' => ['Minimum Username Length', 'number', 'Minimum number of characters for usernames'],
    'username_max_length' => ['Maximum Username Length', 'number', 'Maximum number of characters for usernames'],
    'profile_picture_types' => ['Allowed Profile Picture Types', 'text', 'Comma-separated list of allowed image types'],
    'default_role' => ['Default User Role', 'select', 'Default role assigned to new user accounts', ['Member', 'Admin', 'Guest', 'Subscriber']]
];

foreach ($config_meta as $key => $meta): 
    $value = $settings['config'][$key] ?? '';
?>
    <div class="col-md-6 mb-4">
        <div class="form-group">
            <label for="config_<?= $key ?>" class="form-label fw-bold">
                <?= htmlspecialchars($meta[0]) ?>
            </label>
            <?php if (!empty($meta[2])): ?>
                <div class="form-text text-muted mb-2">
                    <?= htmlspecialchars($meta[2]) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($meta[1] === 'select'): ?>
                <select name="config[<?= $key ?>]" 
                        id="config_<?= $key ?>" 
                        class="form-select">
                    <?php foreach ($meta[3] as $option): ?>
                        <option value="<?= htmlspecialchars($option) ?>" 
                                <?= $value === $option ? ' selected' : '' ?>>
                            <?= htmlspecialchars($option) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php elseif ($meta[1] === 'number'): ?>
                <input type="number" 
                       name="config[<?= $key ?>]" 
                       id="config_<?= $key ?>" 
                       class="form-control" 
                       value="<?= htmlspecialchars($value) ?>">
            <?php else: ?>
                <input type="text" 
                       name="config[<?= $key ?>]" 
                       id="config_<?= $key ?>" 
                       class="form-control" 
                       value="<?= htmlspecialchars($value) ?>">
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
