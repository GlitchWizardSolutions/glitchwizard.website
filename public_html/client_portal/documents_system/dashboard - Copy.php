<?php
/**
 * File: dashboard.php
 * Description: Main interface for generating PDF documents and managing drafts.
 * Functions:
 *   - Displays form for creating or editing a draft
 *   - Integrates Summernote WYSIWYG editor
 *   - Performs draft lock check
 * Expected Outputs:
 *   - HTML form for title and body content input
 *   - Submission to generate-pdf-handler.php
 * Related Files:
 *   - generate-pdf-handler.php
 *   - submit-version-notes.php
 *   - gws-universal-config.php
 *   - draft-locking-setup.php
 */

require_once '../../private/gws-universal-config.php';
headerBlock("Dashboard - PDF System");

$clientId = $_SESSION['client_id'] ?? 0;
$draftId = $_GET['draft_id'] ?? 'new_' . uniqid();
?>

<div class="container py-4">
  <h1 class="mb-4">Document Generator</h1>

  <form id="draftForm" action="generate-pdf-handler.php" method="POST">
    <input type="hidden" name="draft_id" value="<?= htmlspecialchars($draftId) ?>">

    <div class="mb-3">
      <label for="documentTitle" id="documentTitle" class="form-label">Document Title</label>
      <input type="text" class="form-control" name="document_title" id="documentTitle" required>
    </div>

    <div class="mb-3">
      <label for="documentContent" class="form-label">Document Body</label>
      <textarea class="form-control" name="document_content" id="documentContent" rows="10"></textarea>
    </div>

    <div class="mb-3">
      <label for="versionNotes" class="form-label">Version Notes</label>
      <textarea class="form-control" name="version_notes" id="versionNotes" rows="4"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Generate PDF</button>
  </form>
</div>

<script>
  const currentClientId = <?= json_encode($clientId) ?>;
  const draftId = <?= json_encode($draftId) ?>;

  function checkIfDraftLocked(draftId) {
    fetch('draft-locking-setup.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `action=check&draft_id=${encodeURIComponent(draftId)}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.locked && data.client_id !== currentClientId) {
        alert('This draft is currently being edited by someone else.');
      } else {
        console.log('Draft is available.');
      }
    })
    .catch(() => alert('An error occurred while checking the draft lock'));
  }

  document.addEventListener('DOMContentLoaded', () => {
    if (draftId) {
      checkIfDraftLocked(draftId);
    }
  });
</script>

<?php footerBlock(); ?>
