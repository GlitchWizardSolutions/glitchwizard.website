<?php
/**
 * SYSTEM: Document Management System
 * LOCATION: public_html/documents_system/
 * LOG:
 * 2025-07-04 - Original Development
 * PRODUCTION:
 */
/**
 * File: dashboard.php
 * Description: Main interface for generating PDF documents and managing drafts.
 * Functions:
 *   - Displays form for creating or editing a draft
 *   - Integrates Summernote WYSIWYG editor with tabs
 *   - Performs draft lock check
 * Expected Outputs:
 *   - HTML form for title and content
 *   - Submission to generate-pdf-handler.php
 * Related Files:
 *   - generate-pdf-handler.php
 *   - submit-version-notes.php
 *   - gws-universal-config.php
 *   - draft-locking-setup.php
 */

require_once '../../private/gws-universal-functions.php';
require_once 'main.php';

// Check if user is logged in with remember-me support
check_loggedin_full($pdo, '../auth.php?tab=login');

// Check if user has appropriate role
if (!has_role(['admin', 'editor'])) {
    header('Location: ../auth.php?tab=login&error=' . urlencode('You do not have permission to access this page.'));
    exit;
}

echo template_header('Document Generator - Dashboard');

$clientId = get_current_user_id();
$draftId = $_GET['draft_id'] ?? 'new_' . uniqid();
?>

<h1 class="mb-4">Document Generator</h1>

<form id="draftForm" action="generate-pdf-handler.php" method="POST">
  <input type="hidden" name="draft_id" value="<?= htmlspecialchars($draftId) ?>">

  <div class="mb-3">
    <label for="documentTitle" class="form-label">Document Title</label>
    <input type="text" class="form-control" name="document_title" id="documentTitle" required>
  </div>

  <!-- Tabs for Document & Version Notes -->
  <ul class="nav nav-tabs" id="editorTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="doc-tab" data-bs-toggle="tab" data-bs-target="#doc" type="button" role="tab"
        aria-controls="doc" aria-selected="true">Document</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab"
        aria-controls="notes" aria-selected="false">Version Notes</button>
    </li>
  </ul>

  <div class="tab-content mt-3" id="editorTabsContent">
    <div class="tab-pane fade show active" id="doc" role="tabpanel" aria-labelledby="doc-tab"
      data-title="Edit Document Body">
      <textarea name="document_content" id="documentContent"></textarea>
    </div>
    <div class="tab-pane fade" id="notes" role="tabpanel" aria-labelledby="notes-tab" data-title="Edit Version Notes">
      <textarea name="version_notes" id="versionNotes"></textarea>
    </div>
  </div>

  <button type="submit" class="btn btn-primary mt-4">Generate PDF</button>
</form>

<?php footerBlock(); ?>