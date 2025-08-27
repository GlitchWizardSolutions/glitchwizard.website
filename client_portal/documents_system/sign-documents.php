<?php
require_once '../../private/gws-universal-config.php';
require_once 'includes/document-functions.php';

$clientId = $_SESSION['client_id'] ?? null;
$documents = [];
$signaturePath = "uploads/signatures/signature-{$clientId}.png";
$initialsPath = "uploads/signatures/initials-{$clientId}.png";
if ($clientId) {
    $documents = getUnsignedDocuments($pdo, $clientId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Unsigned Documents</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js"></script>
  <style>
    .signature-pad {
      border: 1px solid #ccc;
      border-radius: 4px;
      width: 100%;
      height: 200px;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Client Portal</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="/sign-documents.php">Unsigned Documents</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/view-signed-documents.php">Signed Documents</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/client-dashboard.php">Back to Dashboard</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h1 class="mb-4">Unsigned Documents</h1>

  <?php if (!$clientId): ?>
    <div class="alert alert-danger">Client not logged in.</div>
  <?php elseif (empty($documents)): ?>
    <div class="alert alert-info">You have no unsigned documents.</div>
  <?php else: ?>
    <form action="submit-signature-handler.php" method="POST" enctype="multipart/form-data">
      <div class="mb-4">
        <label for="document" class="form-label">Select a Document</label>
        <select name="document" id="document" class="form-select" required>
          <option value="" disabled selected>Select a document</option>
          <?php foreach ($documents as $doc): ?>
            <option value="<?= htmlspecialchars($doc['output_path']) ?>"><?= htmlspecialchars($doc['document_title']) ?> - <?= htmlspecialchars($doc['created_at']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <?php if (file_exists($signaturePath)): ?>
        <div class="mb-3">
          <label class="form-label">Use Your Saved Signature:</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="signature_option" value="saved" id="savedSig" checked>
            <label class="form-check-label" for="savedSig">
              <img src="<?= $signaturePath ?>" alt="Saved Signature" style="max-height:100px;">
            </label>
          </div>
        </div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label">Or Draw a New Signature</label>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="signature_option" value="new" id="newSig">
          <label class="form-check-label" for="newSig">Draw Below</label>
        </div>
        <canvas id="signaturePad" class="signature-pad"></canvas>
        <input type="hidden" name="new_signature" id="new_signature">
        <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="clearPad()">Clear</button>
      </div>

      <button type="submit" class="btn btn-primary">Submit Signature</button>
    </form>
  <?php endif; ?>
</div>

<script>
const canvas = document.getElementById("signaturePad");
const signaturePad = new SignaturePad(canvas);

document.querySelector("form").addEventListener("submit", function(e) {
  const newSig = document.getElementById("newSig");
  if (newSig.checked && !signaturePad.isEmpty()) {
    document.getElementById("new_signature").value = signaturePad.toDataURL();
  }
});

function clearPad() {
  signaturePad.clear();
}
</script>

</body>
</html>
