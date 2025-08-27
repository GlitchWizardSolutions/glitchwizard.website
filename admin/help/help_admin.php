<?php
include_once '../assets/includes/main.php';
echo template_admin_header('Admin Help & Guide','help','admin');
?>
<div class="content-header"><h2>Admin Help Center</h2></div>
<div class="card mb-4">
  <div class="card-body">
    <p class="lead mb-3">Central reference for administrators. This page will grow into a searchable knowledge base.</p>
    <ul class="mb-4">
      <li>System Overview</li>
      <li>User & Role Management</li>
      <li>Content Management (Hero, Services, Pages)</li>
      <li>Branding & Appearance</li>
      <li>Blog / Editor Workflow (<a href="help_editor.php">see Editor Guide</a>)</li>
      <li>Upcoming: Clients (Logos & Testimonials)</li>
      <li>Legal & Compliance Notes</li>
    </ul>
    <div class="alert alert-info small">Placeholder content – detailed sections will be added iteratively.</div>
  </div>
</div>
<div class="card mb-4">
  <div class="card-header"><strong>Link: Developer SOP</strong></div>
  <div class="card-body small">
    <p>The <a href="help_developer.php">Developer SOP</a> covers deployment standards, font installation, content schema, and advanced customization. Admins typically won't need those details, but can review for transparency.</p>
  </div>
</div>
<?= template_admin_footer(); ?>
