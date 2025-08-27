<?php
/**
 * File: client-sign-documents.php
 * Description: Displays list of unsigned PDF documents for a client to sign.
 * Functions:
 *   - headerBlock()
 *   - footerBlock()
 * Expected Outputs:
 *   - Table of documents with a "Sign Now" action
 * Related Files:
 *   - sign-pdf-interface.php
 *   - gws-universal-config.php
 *   - save-signed-document.php
 */

require_once 'main.php';

// Check if user is logged in  
check_loggedin('../accounts_system/index.php');

echo template_header('Sign Documents');

$clientId = get_current_user_id();
if (!$clientId)
{
    echo "<p class='text-danger'>Client session not found.</p>";
    footerBlock();
    exit;
}

$unsignedDir = "unsigned-documents/client_$clientId/";
$documents = [];

if (is_dir($unsignedDir))
{
    foreach (glob($unsignedDir . "*.pdf") as $filePath)
    {
        $documents[] = basename($filePath);
    }
}
?>

<div class="container py-4">
    <h2 class="mb-4">Documents Awaiting Your Signature</h2>

    <?php if (empty($documents)): ?>
        <div class="alert alert-info">You have no unsigned documents at this time.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Document</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td><?= htmlspecialchars($doc) ?></td>
                        <td><a class="btn btn-sm btn-primary" href="sign-pdf-interface.php?doc=<?= urlencode($doc) ?>">Sign
                                Now</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="dashboard.php" class="btn btn-secondary mt-4">Return to Dashboard</a>
</div>

<?php footerBlock(); ?>