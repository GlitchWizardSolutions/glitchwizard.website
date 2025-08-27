<?php
include_once '../assets/includes/main.php';
echo template_admin_header('Editor Help & Guide','help','editor');
?>
<div class="content-header"><h2>Editor Guide</h2></div>
<div class="card mb-4">
  <div class="card-body">
    <p class="lead mb-3">Focused instructions for users managing blog content.</p>
    <ol class="mb-4">
      <li>Creating a Blog Post</li>
      <li>Uploading & Inserting Media</li>
      <li>Managing Categories & Tags</li>
      <li>Comment Moderation Workflow</li>
      <li>Newsletter Basics</li>
      <li>SEO Meta (Titles & Descriptions)</li>
    </ol>
    <div class="alert alert-info small">Placeholder – detailed step-by-step instructions will be added.</div>
  </div>
</div>
<div class="text-end text-muted small">Version 0.1 Documentation Scaffold</div>
<?= template_admin_footer(); ?>
