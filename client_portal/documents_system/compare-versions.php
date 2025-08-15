<?php
/**
 * File: compare-versions.php
 * Description: Compares two PDF preview versions side-by-side in iframes.
 * Functions:
 *   - Validates input filenames
 *   - Embeds each PDF side-by-side using iframe
 * Expected Outputs:
 *   - Visual comparison of PDF versions in browser
 * Related Files:
 *   - uploads/previews/
 *   - dashboard.php
 */

require_once '../../private/gws-universal-config.php';
headerBlock();

$file1 = isset($_GET['file1']) ? basename($_GET['file1']) : null;
$file2 = isset($_GET['file2']) ? basename($_GET['file2']) : null;

if (!$file1 || !$file2) {
    echo "<p class='text-danger'>Missing file parameters for comparison.</p>";
    footerBlock();
    exit;
}

$path1 = pdf_system_path . "uploads/previews/" . $file1;
$path2 = pdf_system_path . "uploads/previews/" . $file2;

if (!file_exists($path1) || !file_exists($path2)) {
    echo "<p class='text-danger'>One or both files could not be found for comparison.</p>";
    footerBlock();
    exit;
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  iframe {
    width: 100%;
    height: 700px;
    border: 1px solid #ccc;
    margin-bottom: 1rem;
  }
</style>

<div class="container py-4">
  <h2 class="mb-4">Compare Document Versions</h2>
  <div class="row">
    <div class="col-md-6">
      <h5>Version A: <?= htmlspecialchars($file1) ?></h5>
      <iframe src="<?= "uploads/previews/" . urlencode($file1) ?>"></iframe>
    </div>
    <div class="col-md-6">
      <h5>Version B: <?= htmlspecialchars($file2) ?></h5>
      <iframe src="<?= "uploads/previews/" . urlencode($file2) ?>"></iframe>
    </div>
  </div>
</div>

<?php footerBlock(); ?>