<?php
/**
 * Settings Diagnostics
 * Provides a matrix-driven health report of settings tables & required columns.
 */
include_once '../assets/includes/main.php';
require_once PROJECT_ROOT . '/private/settings_completion_matrix.php';

echo template_admin_header('Settings Diagnostics', 'settings', 'dashboard');

$flags_local = $FEATURE_FLAGS ?? [];
$rows = [];
foreach ($SETTINGS_COMPLETION_MATRIX as $key => $def) {
    $flag = $def['flag'] ?? null;
    $flagEnabled = !$flag || featureEnabled($flag, $flags_local);
    $table = $def['table'];
    $tableExists = setting_table_exists($pdo, $table);
    $columns = $tableExists ? get_table_columns($pdo, $table) : [];
    $missingCols = [];
    foreach ($def['must_have'] as $col) {
        if ($tableExists && !in_array($col, $columns)) {
            $missingCols[] = $col;
        }
    }
    $complete = $flagEnabled && $tableExists && empty($missingCols) && setting_is_complete($pdo, $key, $def, $flags_local);
    $rows[] = [
        'key' => $key,
        'table' => $table,
        'flag' => $flag ?: '-',
        'flag_enabled' => $flagEnabled,
        'table_exists' => $tableExists,
        'missing_cols' => $missingCols,
        'complete' => $complete,
    ];
}
?>
<div class="content-title"><div class="title"><div class="icon"><i class="bi bi-clipboard-data" aria-hidden="true"></i></div><div class="txt"><h2>Settings Diagnostics</h2><p>Matrix-driven verification of settings persistence.</p></div></div></div>
<div class="app-card" role="region" aria-label="Settings Diagnostics Table">
    <div class="app-header"><h3>Health Report</h3><i class="bi bi-heart-pulse header-icon" aria-hidden="true"></i></div>
    <div class="app-body">
        <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr>
                    <th scope="col">Key</th>
                    <th scope="col">Table</th>
                    <th scope="col">Flag</th>
                    <th scope="col">Flag Enabled</th>
                    <th scope="col">Table Exists</th>
                    <th scope="col">Missing Columns</th>
                    <th scope="col">Completion</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr class="<?php echo $r['complete'] ? 'table-success' : (!$r['flag_enabled'] ? 'table-secondary' : (!$r['table_exists'] || !empty($r['missing_cols']) ? 'table-warning' : 'table-light')); ?>">
                    <td><?php echo htmlspecialchars($r['key']); ?></td>
                    <td><?php echo htmlspecialchars($r['table']); ?></td>
                    <td><?php echo htmlspecialchars($r['flag']); ?></td>
                    <td><?php echo $r['flag_enabled'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'; ?></td>
                    <td><?php echo $r['table_exists'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>'; ?></td>
                    <td><?php echo empty($r['missing_cols']) ? '<span class="text-muted">None</span>' : implode(', ', array_map('htmlspecialchars', $r['missing_cols'])); ?></td>
                    <td><?php echo $r['complete'] ? '<i class="bi bi-check-circle text-success" aria-hidden="true"></i>' : '<i class="bi bi-x-circle text-danger" aria-hidden="true"></i>'; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <p class="mt-3 small text-muted">Completion requires table row present and all must-have columns populated. Flag-disabled modules are excluded from completion metrics but shown for visibility.</p>
    </div>
</div>
<?php echo template_admin_footer(); ?>
