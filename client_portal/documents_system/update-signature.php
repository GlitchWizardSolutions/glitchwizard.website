<?php
require_once '../../private/gws-universal-config.php';

$clientId = $_SESSION['client_id'] ?? null;
$signaturePath = "uploads/signatures/signature-{$clientId}.png";
$initialsPath = "uploads/signatures/initials-{$clientId}.png";

if (!$clientId) {
    die('Client not logged in.');
}

// Save signature or initials
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $dataUrl = $_POST['image'] ?? '';
    if (in_array($type, ['signature', 'initials']) && strpos($dataUrl, 'data:image/png;base64,') === 0) {
        $data = base64_decode(str_replace('data:image/png;base64,', '', $dataUrl));
        $path = ($type === 'signature') ? $signaturePath : $initialsPath;
        file_put_contents($path, $data);
        $message = ucfirst($type) . " saved successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Signature</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    canvas { border: 1px solid #ccc; }
    .thumb { max-width: 200px; margin-top: 10px; border: 1px solid #ddd; display: block; }
  </style>
</head>
<body>
<div class="container py-5">
  <h1 class="mb-4">Update Signature & Initials</h1>

  <?php if (!empty($message)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <?php if (file_exists($signaturePath)): ?>
    <div class="mb-3">
      <label class="form-label">Current Signature</label>
      <img src="<?= $signaturePath ?>" class="thumb">
    </div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="type" value="signature">
    <label class="form-label">Draw Signature</label><br>
    <canvas id="sig-canvas" width="400" height="150"></canvas><br>
    <input type="hidden" name="image" id="sig-data">
    <button class="btn btn-primary mt-2" onclick="saveCanvas('signature')">Save Signature</button>
  </form>

  <hr>

  <?php if (file_exists($initialsPath)): ?>
    <div class="mb-3">
      <label class="form-label">Current Initials</label>
      <img src="<?= $initialsPath ?>" class="thumb">
    </div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="type" value="initials">
    <label class="form-label">Draw Initials</label><br>
    <canvas id="init-canvas" width="200" height="100"></canvas><br>
    <input type="hidden" name="image" id="init-data">
    <button class="btn btn-secondary mt-2" onclick="saveCanvas('initials')">Save Initials</button>
  </form>
</div>

<script>
function saveCanvas(type) {
  event.preventDefault();
  const canvas = document.getElementById(type === 'signature' ? 'sig-canvas' : 'init-canvas');
  const hiddenInput = document.getElementById(type === 'signature' ? 'sig-data' : 'init-data');
  hiddenInput.value = canvas.toDataURL();
  hiddenInput.form.submit();
}

['sig-canvas', 'init-canvas'].forEach(id => {
  const canvas = document.getElementById(id);
  const ctx = canvas.getContext('2d');
  let drawing = false;

  canvas.addEventListener('mousedown', () => drawing = true);
  canvas.addEventListener('mouseup', () => drawing = false);
  canvas.addEventListener('mouseout', () => drawing = false);
  canvas.addEventListener('mousemove', e => {
    if (!drawing) return;
    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#000';
    ctx.lineTo(x, y);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(x, y);
  });
});
</script>
</body>
</html>
