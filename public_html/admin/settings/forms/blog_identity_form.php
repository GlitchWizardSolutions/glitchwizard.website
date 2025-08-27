<?php
/**
 * Blog Identity Settings (Refactored Mini-Form)
 * PURPOSE: Secure (CSRF + centralized validation) management of blog identity fields.
 * NOTES: Replaces legacy standalone page markup with unified admin template styling.
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
$message='';$message_type='';$errors=[];
$csrf_token = SecurityHelper::getCsrfToken('blog_identity');

if ($_SERVER['REQUEST_METHOD']==='POST') {
        if (!SecurityHelper::validateCsrf('blog_identity', $_POST['csrf_token'] ?? '')) {
                $message='Security token invalid. Please retry.'; $message_type='error';
        } else {
                $spec = [
                    'blog_title' => ['type'=>'string','required'=>true,'max'=>150],
                    'blog_description' => ['type'=>'string','max'=>255],
                    'blog_tagline' => ['type'=>'string','max'=>150],
                    'author_name' => ['type'=>'string','max'=>150],
                    'author_bio' => ['type'=>'string','max'=>1000],
                    'default_author_id' => ['type'=>'int','min'=>1,'max'=>999999],
                    'meta_description' => ['type'=>'string','max'=>255],
                    'meta_keywords' => ['type'=>'string','max'=>255],
                    'blog_email' => ['type'=>'email','max'=>255],
                    'blog_url' => ['type'=>'url','max'=>255],
                    'copyright_text' => ['type'=>'string','max'=>255]
                ];
                $validated = SecurityHelper::validatePayload($spec, $_POST, $errors);
                if ($errors) {
                        $message='Validation errors detected.'; $message_type='error';
                } else {
                        $ok = $settingsManager->updateBlogIdentity($validated, $account_loggedin['username'] ?? 'admin');
                        if ($ok) { $message='Blog identity updated.'; $message_type='success'; }
                        else { $message='Update failed (see logs).'; $message_type='error'; }
                }
        }
        $csrf_token = SecurityHelper::getCsrfToken('blog_identity');
}

$current = $settingsManager->getBlogIdentity();
$page_title='Blog Identity Settings';
echo template_admin_header($page_title,'settings','blog');
?>
<div class="content-title">
    <div class="title">
        <div class="icon"><i class="bi bi-journal-text" style="font-size:18px"></i></div>
        <div class="txt">
            <h2>Blog Identity</h2>
            <p>Manage core identity & author defaults for the blog system.</p>
        </div>
    </div>
    <div class="btn-group">
        <a href="../settings_dash.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Dashboard</a>
        <a href="blog_display_form.php" class="btn btn-outline-primary"><i class="bi bi-layout-text-window-reverse"></i> Display</a>
    </div>
</div>
<?php if ($message): ?>
    <div class="alert alert-<?= $message_type==='success'?'success':'danger' ?>" role="alert">
        <i class="bi bi-<?= $message_type==='success'?'check-circle':'exclamation-triangle' ?>"></i>
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>
<form method="post" class="settings-form" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-info-circle"></i> Basic Blog Info</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="blog_title">Blog Title *</label>
                        <input type="text" class="form-control" id="blog_title" name="blog_title" value="<?= htmlspecialchars($current['blog_title'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="blog_tagline">Tagline</label>
                        <input type="text" class="form-control" id="blog_tagline" name="blog_tagline" value="<?= htmlspecialchars($current['blog_tagline'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="blog_description">Short Description</label>
                        <textarea class="form-control" id="blog_description" name="blog_description" rows="3"><?= htmlspecialchars($current['blog_description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="blog_email">Blog Contact Email</label>
                        <input type="email" class="form-control" id="blog_email" name="blog_email" value="<?= htmlspecialchars($current['blog_email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="blog_url">Canonical Blog URL</label>
                        <input type="url" class="form-control" id="blog_url" name="blog_url" placeholder="https://example.com/blog" value="<?= htmlspecialchars($current['blog_url'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-person-lines-fill"></i> Default Author</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="author_name">Author Name</label>
                        <input type="text" class="form-control" id="author_name" name="author_name" value="<?= htmlspecialchars($current['author_name'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="author_bio">Author Bio</label>
                        <textarea class="form-control" id="author_bio" name="author_bio" rows="4"><?= htmlspecialchars($current['author_bio'] ?? '') ?></textarea>
                        <div class="form-text">Plain text only (rich editor later).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="default_author_id">Default Author ID</label>
                        <input type="number" min="1" class="form-control" id="default_author_id" name="default_author_id" value="<?= htmlspecialchars($current['default_author_id'] ?? 1) ?>">
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-search"></i> Meta Basics</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="meta_description">Meta Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" rows="2"><?= htmlspecialchars($current['meta_description'] ?? '') ?></textarea>
                    </div>
                        <div class="mb-3">
                            <label class="form-label" for="meta_keywords">Meta Keywords (comma separated)</label>
                            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="<?= htmlspecialchars($current['meta_keywords'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="copyright_text">Copyright Footer Text</label>
                            <input type="text" class="form-control" id="copyright_text" name="copyright_text" value="<?= htmlspecialchars($current['copyright_text'] ?? '') ?>">
                        </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Blog Identity</button>
        <a href="../settings_dash.php" class="btn btn-secondary"><i class="bi bi-x-lg me-1"></i>Cancel</a>
    </div>
</form>
<style>
.card{border:1px solid rgba(0,0,0,.125);border-radius:.375rem;box-shadow:0 .125rem .25rem rgba(0,0,0,.075)}
.card-header{background:rgba(0,0,0,.03);border-bottom:1px solid rgba(0,0,0,.125);padding:1rem 1.25rem}
.card-title{font-size:1.05rem;font-weight:600;margin:0}
.settings-form textarea{resize:vertical}
</style>
<?= template_admin_footer(); ?>
