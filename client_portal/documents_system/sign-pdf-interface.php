<?php
/*
🔁 sign-pdf-interface.php
Purpose: Likely experimental or intermediate page for signing a specific PDF.

Status: 🔁 Replaced by the unified signature flow using sign-documents.php + submit-signature-handler.php.

Action: Obsolete unless it has unique UI you'd like to preserve.
*/
require_once '../../private/gws-universal-config.php';
require_once 'includes/document-functions.php';

$clientId = $_SESSION['client_id'] ?? null; // Must be set via login
$documents = [];
if ($clientId) {
    $documents = getUnsignedDocuments($pdo, $clientId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Your Documents</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    canvas#signature-pad {
      border: 2px solid #000;
      width: 100%;
      height: 200px;
      touch-action: none;
    }
  </style>
</head>
<body>
<div class="container py-5">
  <h1 class="mb-4">Unsigned Documents</h1>

  <?php if (!$clientId): ?>
    <div class="alert alert-danger">Client not logged in.</div>
  <?php elseif (empty($documents)): ?>
    <div class="alert alert-info">You have no unsigned documents at this time.</div>
  <?php else: ?>
    <form action="save-signed-pdf.php" method="POST">
      <div class="mb-3">
        <label for="document_id" class="form-label">Select Document to Sign</label>
        <select name="document_id" id="document_id" class="form-select" required onchange="loadPreview(this)">
          <option value="" selected disabled>Choose a document</option>
          <?php foreach ($documents as $doc): ?>
            <option value="<?= $doc['id'] ?>" data-path="<?= htmlspecialchars($doc['output_path']) ?>">
              <?= htmlspecialchars($doc['document_title']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-4" id="preview-container" style="display:none;">
        <label class="form-label">Document Preview</label>
        <iframe id="preview-frame" src="" class="w-100" style="height:500px; border:1px solid #ccc;"></iframe>
      </div>

      <div class="mb-3">
        <label for="signature" class="form-label">Sign Below</label>
        <canvas id="signature-pad"></canvas>
        <input type="hidden" name="signature_data" id="signature_data">
        <button type="button" class="btn btn-outline-secondary mt-2" onclick="clearSignature()">Clear Signature</button>
      </div>

      <button type="submit" class="btn btn-primary">Submit Signed Document</button>
    </form>
  <?php endif; ?>
</div>

<script>
function loadPreview(select) {
  const iframe = document.getElementById('preview-frame');
  const preview = select.options[select.selectedIndex].getAttribute('data-path');
  if (preview) {
    iframe.src = preview;
    document.getElementById('preview-container').style.display = 'block';
  }
}

const canvas = document.getElementById('signature-pad');
const ctx = canvas.getContext('2d');
let drawing = false;

canvas.addEventListener('mousedown', e => {
  drawing = true;
  ctx.beginPath();
  ctx.moveTo(e.offsetX, e.offsetY);
});

canvas.addEventListener('mousemove', e => {
  if (drawing) {
    ctx.lineTo(e.offsetX, e.offsetY);
    ctx.stroke();
  }
});

canvas.addEventListener('mouseup', () => drawing = false);
canvas.addEventListener('mouseleave', () => drawing = false);

canvas.addEventListener('touchstart', e => {
  const rect = canvas.getBoundingClientRect();
  const touch = e.touches[0];
  ctx.beginPath();
  ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
  drawing = true;
});

canvas.addEventListener('touchmove', e => {
  if (!drawing) return;
  const rect = canvas.getBoundingClientRect();
  const touch = e.touches[0];
  ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
  ctx.stroke();
});

canvas.addEventListener('touchend', () => drawing = false);

function clearSignature() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
}

// On submit, save canvas to input
const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
  const dataURL = canvas.toDataURL();
  document.getElementById('signature_data').value = dataURL;
});
</script>
</body>
</html>
