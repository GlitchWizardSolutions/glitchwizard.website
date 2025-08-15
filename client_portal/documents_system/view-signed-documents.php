<?php
require_once '../../private/gws-universal-config.php';

$clientId = $_SESSION['client_id'] ?? null;
$documents = [];
if ($clientId) {
    $stmt = $pdo->prepare("SELECT document_title, signed_path, created_at FROM audit_log WHERE client_id = ? AND signed_path IS NOT NULL ORDER BY created_at DESC");
    $stmt->execute([$clientId]);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signed Documents</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    iframe {
      width: 100%;
      height: 600px;
      border: 1px solid #ccc;
      border-radius: 8px;
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
          <a class="nav-link" href="/sign-documents.php">Unsigned Documents</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="/view-signed-documents.php">Signed Documents</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/client-dashboard.php">Back to Dashboard</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h1 class="mb-4">Signed Documents</h1>

  <?php if (!$clientId): ?>
    <div class="alert alert-danger">Client not logged in.</div>
  <?php elseif (empty($documents)): ?>
    <div class="alert alert-info">No signed documents available.</div>
  <?php else: ?>
    <div class="accordion" id="signedDocs">
      <?php foreach ($documents as $index => $doc): ?>
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading<?= $index ?>">
            <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>">
              <?= htmlspecialchars($doc['document_title']) ?> - <?= htmlspecialchars($doc['created_at']) ?>
            </button>
          </h2>
          <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#signedDocs">
            <div class="accordion-body">
              <iframe src="<?= htmlspecialchars($doc['signed_path']) ?>"></iframe>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
