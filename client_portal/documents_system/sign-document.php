<?php
/**
 * File: sign-document.php
 * Description: Allows client to sign a selected document using a signature pad.
 * Functions:
 *   - Renders selected document inline
 *   - Captures signature and posts to submit-signature-handler.php
 * Expected Outputs:
 *   - Inline PDF preview
 *   - Signature capture via canvas
 * Related Files:
 *   - gws-universal-config.php
 *   - submit-signature-handler.php
 *   - view-signed-documents.php
 *   - pick-signature.php
 */

require_once '../../private/gws-universal-functions.php';
require_once 'main.php';

// Check if user is logged in with remember-me support
check_loggedin_full($pdo, '../auth.php?tab=login');

// Get document ID and validate access
$clientId = get_current_user_id();
$documentId = $_GET['id'] ?? null;

if (!$documentId) {
    header('Location: client-documents.php');
    exit;
}

echo template_header('Sign Document');
$document = $_GET['document'] ?? null;

if (!$document || !$clientId)
{
  echo "<div class='alert alert-danger'>Missing document or client session.</div>";
  footerBlock();
  exit;
}

$documentPath = "clients/client_$clientId/" . basename($document);

if (!file_exists($documentPath))
{
  echo "<div class='alert alert-danger'>Document not found: " . htmlspecialchars($documentPath) . "</div>";
  footerBlock();
  exit;
}
?>

<style>
  canvas {
    border: 1px solid #ccc;
    width: 100%;
    height: 200px;
  }
</style>

<div class="container py-4">
  <h2 class="mb-3">Sign Document</h2>

  <iframe src="<?= htmlspecialchars($documentPath) ?>" width="100%" height="600px" class="mb-4"></iframe>

  <form action="submit-signature-handler.php" method="POST">
    <input type="hidden" name="client_id" value="<?= htmlspecialchars($clientId) ?>">
    <input type="hidden" name="document" value="<?= htmlspecialchars($documentPath) ?>">
    <input type="hidden" name="signature" id="signature_input">

    <label for="signature">Draw Your Signature Below:</label>
    <canvas id="signature" class="mb-3"></canvas>

    <div class="mb-3">
      <a href="pick-signature.php?return_to=<?= urlencode("sign-document.php?document=$document") ?>"
        class="btn btn-outline-secondary btn-sm">Choose Existing Signature</a>
    </div>

    <button type="submit" class="btn btn-primary">Submit Signed Document</button>
  </form>
</div>

<script>
  const canvas = document.getElementById("signature");
  const input = document.getElementById("signature_input");
  const ctx = canvas.getContext("2d");
  let drawing = false;

  canvas.addEventListener("mousedown", () => drawing = true);
  canvas.addEventListener("mouseup", () => {
    drawing = false;
    input.value = canvas.toDataURL();
  });
  canvas.addEventListener("mousemove", (e) => {
    if (!drawing) return;
    const rect = canvas.getBoundingClientRect();
    ctx.lineWidth = 2;
    ctx.lineCap = "round";
    ctx.strokeStyle = "#000";
    ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
  });

  // Restore picked signature from localStorage
  const savedSig = localStorage.getItem("selectedSignature");
  if (savedSig) {
    const img = new Image();
    img.onload = () => {
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
      input.value = canvas.toDataURL();
      localStorage.removeItem("selectedSignature");
    };
    img.src = savedSig;
  }
</script>

<?php footerBlock(); ?>