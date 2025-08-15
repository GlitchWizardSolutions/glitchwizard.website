<?php
/**
 * File: scope-of-work-template.php
 * Description: Displays scope-of-work items and related tasks, allows client signature input.
 * Functions:
 *   - Displays scopes from database
 *   - Lists related tasks (mine/yours/status/due_date)
 *   - Submits signed agreement
 * Expected Outputs:
 *   - Rendered scope list with task breakdown
 *   - Signature pad for signing
 * Related Files:
 *   - gws-universal-config.php
 *   - submit-signature-handler.php
 *   - generate-pdf-handler.php
 *   - clients/client_[id]/ folders
 */

require_once '../../private/gws-universal-config.php';
headerBlock();

$clientId = $_SESSION['client_id'] ?? null;

$stmt = $pdo->query("SELECT * FROM scope ORDER BY update_date DESC");
$scopes = $stmt->fetchAll();
?>

<head>
  <meta charset="UTF-8">
  <title>Scope of Work Agreement</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    canvas { border: 1px solid #ccc; width: 100%; height: 200px; }
  </style>
</head>
<body class="container py-4">
  <h1 class="mb-4">Scope of Work Agreement</h1>

  <form method="POST" action="submit-signature-handler.php">
    <?php foreach ($scopes as $scope): ?>
      <div class="mb-4 border p-3 rounded bg-light">
        <h4><?= htmlspecialchars($scope['title']) ?></h4>
        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($scope['description'])) ?></p>
        <p><strong>Fee:</strong> <?= htmlspecialchars($scope['fee']) ?> | <strong>Frequency:</strong> <?= htmlspecialchars($scope['frequency']) ?></p>
        <?php if (!empty($scope['document_path'])): ?>
          <p><a href="<?= htmlspecialchars($scope['document_path']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm">View Attached PDF</a></p>
        <?php endif; ?>

        <table class="table table-bordered table-sm mt-3">
          <thead class="table-secondary">
            <tr>
              <th>Your Tasks</th>
              <th>My Tasks</th>
              <th>Status</th>
              <th>Due</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $taskStmt = $pdo->prepare("SELECT * FROM tasks WHERE scope_id = ?");
              $taskStmt->execute([$scope['id']]);
              $tasks = $taskStmt->fetchAll();
              foreach ($tasks as $task):
            ?>
              <tr>
                <td><?= htmlspecialchars($task['yours']) ?></td>
                <td><?= htmlspecialchars($task['mine']) ?></td>
                <td><?= htmlspecialchars($task['status']) ?></td>
                <td><?= htmlspecialchars($task['due_date']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endforeach; ?>

    <input type="hidden" name="client_id" value="<?= htmlspecialchars($clientId) ?>">
    <input type="hidden" name="document_title" value="Scope of Work Agreement">

    <div class="mb-3">
      <label for="signature">Client Signature</label>
      <canvas id="signature" class="mb-2"></canvas>
      <input type="hidden" name="signature" id="signature_input">
    </div>

    <button type="submit" class="btn btn-primary">Submit Signed Agreement</button>
  </form>

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
  </script>

<?php footerBlock(); ?>