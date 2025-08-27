<?php
/**
 * File: confirm-generation.php
 * Description: Final confirmation screen before generating and saving a document as PDF.
 * Functions:
 *   - Displays draft content, signature preview, audit warning, and confirmation options
 *   - Optional saving of document as reusable template
 * Expected Outputs:
 *   - Preview with option to generate or cancel
 * Related Files:
 *   - generate-pdf-handler.php
 *   - dashboard.php
 *   - draft-locking-setup.php
 */

require_once '../../private/gws-universal-config.php';
headerBlock();

$clientId = $_POST['client_id'] ?? 0;
$documentTitle = $_POST['document_title'] ?? '';
$summernoteContent = $_POST['summernote'] ?? '';
$draftId = $_POST['draft_id'] ?? '';

$_SESSION['pending_generation'] = [
    'client_id' => $clientId,
    'document_title' => $documentTitle,
    'content' => $summernoteContent,
    'draft_id' => $draftId
];
?>

<div class="container py-4">
  <h2>Confirm Document Generation</h2>

  <?php if (isset($_GET['locked']) && $_GET['locked'] === 'true'): ?>
    <div class="alert alert-warning">This draft is currently locked by another user. Please try again later.</div>
  <?php endif; ?>

  <div class="mb-3">
    <label class="form-label">Document Title:</label>
    <div class="border p-2 bg-light"><?= htmlspecialchars($documentTitle) ?></div>
  </div>

  <div class="mb-3">
    <label class="form-label">Document Preview:</label>
    <div class="border p-3" style="background:#fefefe;">
      <?= $summernoteContent ?>
    </div>
  </div>

  <?php if (!empty($_SESSION['selected_signature'])): ?>
    <div class="mb-3">
      <label class="form-label">Signature to be used:</label><br>
      <img src="<?= htmlspecialchars($_SESSION['selected_signature']) ?>" style="max-height:100px; border:1px solid #ccc;">
    </div>
  <?php endif; ?>

  <div class="alert alert-info">This action will be logged for auditing, including your IP address, timestamp, and output type.</div>

  <form action="generate-pdf-handler.php" method="POST">
    <input type="hidden" name="confirm" value="1">
    <input type="hidden" name="draft_id" value="<?= htmlspecialchars($draftId) ?>">
    <div class="form-check mb-3">
      <input class="form-check-input" type="checkbox" name="save_template" value="1" id="saveTemplate">
      <label class="form-check-label" for="saveTemplate">Save this content as a reusable template</label>
    </div>
    <button type="submit" class="btn btn-success">Yes, Generate PDF</button>
    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php footerBlock(); ?>