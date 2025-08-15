<?php
/**
 * File: client-signature-list.php
 * Description: Displays saved signatures for the logged-in client.
 * Functions:
 *   - headerBlock()
 *   - footerBlock()
 * Expected Outputs:
 *   - Thumbnail previews of signature files
 * Related Files:
 *   - update-signature.php
 *   - dashboard.php
 *   - gws-universal-config.php
 */

require_once '../../private/gws-universal-config.php';
headerBlock();

$clientId = $_SESSION['client_id'] ?? null;
if (!$clientId) {
    echo "<div class='container py-4'><div class='alert alert-danger'>Client not logged in.</div></div>";
    footerBlock();
    exit;
}

$signatureDir = "signatures/client_$clientId/";
$signatures = is_dir($signatureDir) ? glob($signatureDir . "*.png") : [];
?>

<div class="container py-4">
    <h2>Your Saved Signatures</h2>

    <?php if (empty($signatures)): ?>
        <div class="alert alert-warning">You don't have any saved signatures.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($signatures as $file): ?>
                <div class="col-md-4 mb-3">
                    <img src="<?= htmlspecialchars($file) ?>" class="img-fluid border p-2" alt="Signature">
                    <p class="text-muted"><?= basename($file) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a href="update-signature.php" class="btn btn-primary">Update Signature</a>
    <a href="dashboard.php" class="btn btn-secondary ms-2">Back to Dashboard</a>
</div>

<?php footerBlock(); ?>