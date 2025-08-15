<?php
/**
 * File: pick-signature.php
 * Description: Lets the client select from saved signatures for document reuse.
 * Functions:
 *   - None (UI logic and JavaScript)
 * Expected Outputs:
 *   - Client selects a signature which is stored in browser localStorage
 * Related Files:
 *   - signatures/client_[id]/ (source of signature images)
 *   - Any page where the user is redirected to (returnTo)
 *   - gws-universal-config.php
 */

require_once '../../private/gws-universal-config.php';

$clientId = $_SESSION['client_id'] ?? null;
$returnTo = $_GET['return_to'] ?? 'dashboard.php';

$signatureDir = __DIR__ . "/signatures/client_$clientId/";
$signatureFiles = [];

if ($clientId && is_dir($signatureDir)) {
    foreach (glob($signatureDir . "*.png") as $file) {
        $signatureFiles[] = basename($file);
    }
}

headerBlock();
?>
<head>
  <meta charset="UTF-8">
  <title>Select Saved Signature</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .signature-thumb {
      border: 1px solid #ccc;
      padding: 10px;
      margin: 10px;
      max-width: 300px;
      cursor: pointer;
    }
  </style>
</head>
<body class="container py-4">
  <h2 class="mb-4">Select Your Saved Signature</h2>

  <?php if (empty($signatureFiles)): ?>
    <div class="alert alert-warning">No saved signatures found. Please draw and save a signature first.</div>
  <?php else: ?>
    <div class="row">
      <?php foreach ($signatureFiles as $file): ?>
        <div class="col-md-4">
          <img 
            src="<?= "signatures/client_$clientId/$file" ?>" 
            class="signature-thumb"
            onclick="selectSignature('<?= htmlspecialchars("signatures/client_$clientId/$file") ?>')"
          >
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <a href="<?= htmlspecialchars($returnTo) ?>" class="btn btn-secondary mt-4">Cancel and Return</a>

  <script>
    function selectSignature(imagePath) {
      const img = new Image();
      img.crossOrigin = "Anonymous";
      img.onload = () => {
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0);
        const dataURL = canvas.toDataURL();
        localStorage.setItem('selectedSignature', dataURL);
        window.location.href = "<?= htmlspecialchars($returnTo) ?>";
      };
      img.src = imagePath;
    }
  </script>
<?php footerBlock(); ?>