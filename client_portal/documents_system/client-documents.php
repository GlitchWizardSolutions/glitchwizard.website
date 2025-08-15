<?php
//Location: public_html/pdf-system/client-documents.php
/**
 * File: client-documents.php
 * Description: Client-facing page to view unsigned/signed documents.
 * Location: public_html/pdf-system/client-documents.php
 * Requirements:
 * - Only shows documents that belong to the logged-in client
 * - Allows download/view of approved/signed documents
 * - Allows payment if required
 */

require_once '../../private/gws-universal-functions.php';
require_once 'main.php';

// Check if user is logged in
check_loggedin_full($pdo, '../auth.php?tab=login');

$clientId = get_current_user_id();

// Fetch documents for this client
$stmt = $pdo->prepare("SELECT id, document_title, saved_pdf_filename, is_signed, payment_required, payment_status, created_at
                       FROM documents
                       WHERE client_id = ?
                       ORDER BY created_at DESC");
$stmt->execute([$clientId]);
$documents = $stmt->fetchAll();

echo template_header('Your Documents');
?>

<h1 class="mb-4">Your Documents</h1>

<?php if (empty($documents)): ?>
  <div class="alert alert-info">You have no documents yet.</div>
<?php else: ?>
  <table class="table table-bordered table-hover">
    <thead class="table-light">
      <tr>
        <th>Title</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($documents as $doc): ?>
        <tr>
          <td><?= htmlspecialchars($doc['document_title']) ?></td>
          <td><?= date('F j, Y', strtotime($doc['created_at'])) ?></td>
          <td>
            <?php if (!$doc['is_signed']): ?>
              <span class="badge bg-warning">Unsigned</span>
            <?php else: ?>
              <span class="badge bg-success">Signed</span>
            <?php endif; ?>

            <?php if ($doc['payment_required'] && $doc['payment_status'] !== 'paid'): ?>
              <span class="badge bg-danger">Payment Due</span>
            <?php elseif ($doc['payment_required']): ?>
              <span class="badge bg-info">Paid</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if (!empty($doc['saved_pdf_filename'])): ?>
              <a href="/pdf-system/generated/<?= urlencode($doc['saved_pdf_filename']) ?>" target="_blank"
                class="btn btn-sm btn-outline-primary">View PDF</a>
            <?php endif; ?>

            <?php if (!$doc['is_signed']): ?>
              <a href="sign-document.php?doc_id=<?= $doc['id'] ?>" class="btn btn-sm btn-outline-secondary">Sign</a>
            <?php endif; ?>

            <?php if ($doc['payment_required'] && $doc['payment_status'] !== 'paid'): ?>
              <a href="pay-document.php?doc_id=<?= $doc['id'] ?>" class="btn btn-sm btn-outline-success">Pay</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php footerBlock(); ?>