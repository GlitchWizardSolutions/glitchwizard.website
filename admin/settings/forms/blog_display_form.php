<?php
/**
 * Blog Display Settings (Refactored Mini-Form)
 * PURPOSE: Secure layout/display configuration with CSRF + centralized validation.
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
$csrf_token = SecurityHelper::getCsrfToken('blog_display');

if ($_SERVER['REQUEST_METHOD']==='POST') {
        if (!SecurityHelper::validateCsrf('blog_display', $_POST['csrf_token'] ?? '')) {
                $message='Security token invalid. Please retry.'; $message_type='error';
        } else {
                $spec = [
                    'posts_per_page'=>['type'=>'int','min'=>1,'max'=>100,'required'=>true],
                    'excerpt_length'=>['type'=>'int','min'=>20,'max'=>1000,'required'=>true],
                    'date_format'=>['type'=>'string','max'=>50],
                    'layout'=>['type'=>'string','max'=>20],
                    'sidebar_position'=>['type'=>'string','max'=>10],
                    'posts_per_row'=>['type'=>'int','min'=>1,'max'=>4],
                    'theme'=>['type'=>'string','max'=>50],
                    'enable_featured_image'=>['type'=>'bool'],
                    'thumbnail_width'=>['type'=>'int','min'=>50,'max'=>2000],
                    'thumbnail_height'=>['type'=>'int','min'=>50,'max'=>2000],
                    'background_image'=>['type'=>'url','max'=>255],
                    'custom_css'=>['type'=>'string','max'=>5000],
                    'show_author'=>['type'=>'bool'],
                    'show_date'=>['type'=>'bool'],
                    'show_categories'=>['type'=>'bool'],
                    'show_tags'=>['type'=>'bool'],
                    'show_excerpt'=>['type'=>'bool']
                ];
                $validated = SecurityHelper::validatePayload($spec, $_POST, $errors);
                if ($errors) { $message='Validation errors detected.'; $message_type='error'; }
                else {
                        $ok = $settingsManager->updateBlogDisplay($validated, $account_loggedin['username'] ?? 'admin');
                        if ($ok) { $message='Blog display settings updated.'; $message_type='success'; }
                        else { $message='Update failed (see logs).'; $message_type='error'; }
                }
        }
        $csrf_token = SecurityHelper::getCsrfToken('blog_display');
}

$current = $settingsManager->getBlogDisplay();
$page_title='Blog Display Settings';
echo template_admin_header($page_title,'settings','blog');
?>
<div class="content-title">
    <div class="title">
        <div class="icon"><i class="bi bi-layout-text-window-reverse" style="font-size:18px"></i></div>
        <div class="txt">
            <h2>Blog Display</h2>
            <p>Control listing layout, excerpts, imagery and visible metadata.</p>
        </div>
    </div>
    <div class="btn-group">
        <a href="../settings_dash.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Dashboard</a>
        <a href="blog_identity_form.php" class="btn btn-outline-primary"><i class="bi bi-person-badge"></i> Identity</a>
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
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-grid"></i> Listing & Layout</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="posts_per_page">Posts Per Page</label>
                            <input type="number" class="form-control" id="posts_per_page" name="posts_per_page" value="<?= htmlspecialchars($current['posts_per_page'] ?? 10) ?>" min="1" max="100" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="posts_per_row">Posts Per Row</label>
                            <select class="form-select" id="posts_per_row" name="posts_per_row">
                                <?php for($i=1;$i<=4;$i++): ?>
                                    <option value="<?= $i ?>" <?= ($current['posts_per_row'] ?? 2)==$i?'selected':'' ?>><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="layout">Layout Style</label>
                            <select class="form-select" id="layout" name="layout">
                                <?php foreach(['Wide','Boxed','Masonry'] as $opt): ?>
                                    <option value="<?= $opt ?>" <?= ($current['layout'] ?? 'Wide')===$opt?'selected':'' ?>><?= $opt ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="sidebar_position">Sidebar Position</label>
                            <select class="form-select" id="sidebar_position" name="sidebar_position">
                                <?php foreach(['None','Left','Right'] as $opt): ?>
                                    <option value="<?= $opt ?>" <?= ($current['sidebar_position'] ?? 'Right')===$opt?'selected':'' ?>><?= $opt ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="excerpt_length">Excerpt Length (chars)</label>
                            <input type="number" class="form-control" id="excerpt_length" name="excerpt_length" value="<?= htmlspecialchars($current['excerpt_length'] ?? 250) ?>" min="20" max="1000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="date_format">Date Format</label>
                            <input type="text" class="form-control" id="date_format" name="date_format" value="<?= htmlspecialchars($current['date_format'] ?? 'F j, Y') ?>">
                            <div class="form-text">PHP date() format</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-image"></i> Images</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="thumbnail_width">Thumbnail Width</label>
                            <input type="number" class="form-control" id="thumbnail_width" name="thumbnail_width" value="<?= htmlspecialchars($current['thumbnail_width'] ?? 300) ?>" min="50" max="2000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="thumbnail_height">Thumbnail Height</label>
                            <input type="number" class="form-control" id="thumbnail_height" name="thumbnail_height" value="<?= htmlspecialchars($current['thumbnail_height'] ?? 200) ?>" min="50" max="2000">
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="enable_featured_image" name="enable_featured_image" <?= !empty($current['enable_featured_image'])?'checked':'' ?>>
                        <label class="form-check-label" for="enable_featured_image">Enable Featured Images</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="background_image">Background Image URL</label>
                        <input type="url" class="form-control" id="background_image" name="background_image" value="<?= htmlspecialchars($current['background_image'] ?? '') ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-eye"></i> Visibility Toggles</h5></div>
                <div class="card-body row">
                    <?php $toggles=['show_author'=>'Show Author','show_date'=>'Show Date','show_categories'=>'Show Categories','show_tags'=>'Show Tags','show_excerpt'=>'Show Excerpt']; foreach($toggles as $field=>$label): ?>
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="<?= $field ?>" name="<?= $field ?>" <?= !empty($current[$field])?'checked':'' ?>>
                                <label class="form-check-label" for="<?= $field ?>"><?= $label ?></label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header"><h5 class="card-title mb-0"><i class="bi bi-sliders"></i> Theme & Custom CSS</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="theme">Theme Variant</label>
                        <select class="form-select" id="theme" name="theme">
                            <?php foreach(['Default','Minimal','Dark','Cards'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= ($current['theme'] ?? 'Default')===$opt?'selected':'' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="custom_css">Custom CSS (inline)</label>
                        <textarea class="form-control font-monospace" id="custom_css" name="custom_css" rows="6" placeholder="/* Optional custom styles */"><?= htmlspecialchars($current['custom_css'] ?? '') ?></textarea>
                        <div class="form-text">Small tweaks only; consider theme assets for large changes.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Display Settings</button>
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
