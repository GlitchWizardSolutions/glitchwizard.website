<?php
/**
 * Team Member Social Profiles (Stub)
 * PURPOSE: Manage individual team member entries with their own social links.
 * This separates personal/individual profiles from business-wide social media.
 */
session_start();
require_once __DIR__ . '/../../../../private/gws-universal-config.php';
require_once __DIR__ . '/../../../../private/classes/SettingsManager.php';
require_once __DIR__ . '/../../../../private/classes/SecurityHelper.php';
include_once '../../assets/includes/main.php';

if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['Admin','Editor','Developer'])) {
    header('Location: ../../index.php');
    exit();
}

$settingsManager = new SettingsManager($pdo);
$page_title = 'Team Members & Social Profiles';
$message = '';
$message_type = '';
$errors = [];
$csrf_token = SecurityHelper::getCsrfToken('team_members');

// NOTE: This is a stub – full CRUD (add/edit/delete/reorder members) to be implemented.
// Proposed table: setting_content_team (already referenced) with optional columns:
// member_name, member_role, member_bio, member_image, member_order, member_facebook, member_x, member_linkedin, member_instagram, member_other, updated_by, updated_at

?>
<?= template_admin_header($page_title, 'settings', 'team') ?>
<div class="content-title">
  <div class="title">
    <div class="icon"><i class="bi bi-people" style="font-size:18px"></i></div>
    <div class="txt">
      <h2>Team Members & Individual Social Links</h2>
      <p>Manage individual team member listings, bios, photos, and their personal professional social media profiles. These are distinct from the business-wide profiles configured in Business Contact Settings.</p>
    </div>
  </div>
  <div class="btn-group">
    <a href="../settings_dash.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
  </div>
</div>
<div class="alert alert-info small">
  <strong>Status:</strong> This management screen is a placeholder. Full functionality (CRUD + ordering + validation + CSRF) will be implemented in Phase 1 after core settings hardening.
</div>
<section class="mb-4">
  <h5>Planned Features</h5>
  <ul class="small">
    <li>Add new team member (name, role/title, short bio, image upload/select)</li>
    <li>Per-member social links: LinkedIn, X (Twitter), Facebook, Instagram, Other</li>
    <li>Drag-and-drop ordering (member_order)</li>
    <li>Status toggle (active/inactive)</li>
    <li>Audit trail (updated_by / updated_at)</li>
    <li>Server-side validation + CSRF tokens via SecurityHelper</li>
  </ul>
</section>
<section>
  <h5>Next Steps (Implementation Roadmap)</h5>
  <ol class="small">
    <li>Create/confirm schema for "setting_content_team" ensuring social columns present.</li>
    <li>Extend SettingsManager with CRUD helpers (getTeamMembers, addTeamMember, updateTeamMember, deleteTeamMember, reorderTeamMembers).</li>
    <li>Implement list view with inline edit modal or dedicated edit page.</li>
    <li>Integrate image handling (reuse existing upload pipeline if available).</li>
    <li>Wire into public/team section renderer.</li>
  </ol>
</section>
<?= template_admin_footer() ?>
