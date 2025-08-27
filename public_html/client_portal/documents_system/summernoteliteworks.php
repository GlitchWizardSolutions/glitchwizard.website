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
<!--Test Code-->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<div class="container">
  <div class="row g-0">
      <div class="summernote" id="EnglishWYSIWIG">
        <p>Hello Summernote</p>
        <ul>
          <li>Try adding some more content</li>
          <li>Just to see how it all works out!</li>
        </ul>
        </div>
  </div>
  
      <!-- ========== Start For Options with Porto ========== -->
<!-- For English -->
  <div class="summernote" name="descriptionEn" id="EnglishWYSIWIG"
  data-plugin-summernote data-plugin-options='{ "height": 180, "lang": "en-EN", "codemirror": { "theme": "ambiance" }, "toolbar": [ ["style", ["bold", "italic", "underline"]], ["para", ["ul", "ol"]], ["insert", ["ltr"]] ] }'></div>  
 <!-- ========== End For Options with Porto ========== -->
  
<!--  For Understanding Purposes ONLY  -->
  <div class="row text-center">
    <button class="btn btn-primary">Display Content</button>
  </div>
  <div class="row mt-3">
    <h2>Display Area</h2>
  </div>
  <div class="row">
      <div class="summernote-content" id="English">
    </div>
    <div class="summernote-content" style="direction:rtl;" id="Arabic">
    </div>
  </div>
</div>
<script>
   $(document).ready(function() {
        $('.summernote').summernote({
            height: "180",
            disableDragAndDrop:true,
            styleTags: ['p','h1','h2','h3','h4','h5','h6'],
            toolbar: [
            ['para',['style']],
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul','ol']]
            ]
        });
    });

    // Sanitization being done OnPaste
    $(".summernote").on("summernote.paste",function(e,ne) {
        var bufferText = ((ne.originalEvent || ne).clipboardData || window.clipboardData).getData('Text');
        ne.preventDefault();
        document.execCommand('insertText', false, bufferText);
        $(ne.currentTarget).find("*").removeAttributes();
    });
    // Sanitization being done OnChange
    $('.summernote').on('summernote.change', function(we, contents, $editable) {
        $editable.find("*").removeAttributes();
    });

    // Helper Function to Remove Attributes
    jQuery.fn.removeAttributes = function() {
        return this.each(function() {
            var attributes = $.map(this.attributes, function(item) {
            return item.name;
            });
            var obj = $(this);
            $.each(attributes, function(i, item) {
                obj.removeAttr(item);
            });
        });
    }

    sanitizeSummernoteAndGetContent = (summernoteID) =>{
        // Sanitizing of All Attributes
        $("#"+summernoteID+" + .note-editor .note-editable *").removeAttributes();
        // Returning Clean Code with \n replaced with <br/>
        return $("#"+summernoteID).summernote("code");
    }

$(".btn").click(function(){
  $('#English').html(sanitizeSummernoteAndGetContent("EnglishWYSIWIG"));
  $('#Arabic').html(sanitizeSummernoteAndGetContent("ArabicWYSIWIG"));
});
</script>
<!-- /end test code-->
<?php footerBlock(); ?>
