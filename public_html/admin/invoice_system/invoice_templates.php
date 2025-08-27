<?php
include 'main.php';
// Retrieve all the templates from the templates directory and sort alphabetically
$templates = glob('../../invoice_system/templates/*', GLOB_ONLYDIR);
sort($templates);
// Search
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
if ($search != '') {
    $templates = array_filter($templates, function($template) use ($search) {
        return strpos($template, $search) !== false;
    });
}
// Delete template
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $file = '../../invoice_system/templates/' . $_GET['delete'];
    if (is_dir($file)) {
        // Delete the directory
        array_map('unlink', glob($file . '/*.*'));
        rmdir($file);
        header('Location: invoice_templates.php?success_msg=3');
        exit;
    }
}
// Duplicate template
if (isset($_GET['duplicate']) && !empty($_GET['duplicate'])) {
    $source = '../../invoice_system/templates/' . $_GET['duplicate'];
    if (is_dir($source)) {
        // determine destination and append copy number if necessary
        $destination = $source . '_copy';
        $i = 1;
        while (is_dir($destination)) {
            $destination = $source . '_copy' . $i++;
        }
        // copy the directory
        copy_directory($source, $destination);
        header('Location: invoice_templates.php?success_msg=4');
        exit;
    }
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Invoice template created successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Invoice template updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Invoice template deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Invoice template duplicated successfully!';
    }    
}
// Create URL
$url = 'invoice_templates.php?search_query=' . $search;
?>
<?=template_admin_header('Invoice Templates', 'invoices', 'templates')?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
        </div>
        <div class="txt">
           <h2>Invoice Templates</h2>
            <p>View and manage invoice templates.</p>
        </div>
    </div>
</div>
 

<div class="d-flex gap-2 mb-4">
   <a href="invoice_template.php"  class="btn btn-outline-secondary">
        <i class="bi bi-plus me-1" aria-hidden="true"></i>Create Template
    </a>
</div>
 
 
<?php if (isset($success_msg)): ?>
<div class="msg success">
    <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
    <p><?=$success_msg?></p>
    <i class="bi bi-x-lg close" aria-hidden="true"></i>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Invoice Templates</h6>
        <small class="text-muted"><?=count($templates)?> template<?=count($templates) != 1 ? 's' : ''?> found</small>
    </div>
    <div class="card-body">
        <form action="" method="get" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search_query" class="form-label">Search Templates</label>
                    <input type="text" name="search_query" id="search_query" class="form-control" placeholder="Search templates..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-search me-1" aria-hidden="true"></i>Search Templates
                    </button>
                    <?php if ($search != ''): ?>
                    <a href="invoice_templates.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1" aria-hidden="true"></i>Clear
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <!-- Active Filters -->
        <?php if ($search): ?>
        <div class="mb-3">
            <h6 class="mb-2">Active Filters:</h6>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-secondary">
                    Search: <?=htmlspecialchars($search, ENT_QUOTES)?>
                    <a href="invoice_templates.php" class="text-white ms-1" aria-label="Remove search filter">×</a>
                </span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Templates Table -->
        <?php if (empty($templates)): ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-receipt fa-3x mb-3 d-block" aria-hidden="true"></i>
            <h5>No Templates Found</h5>
            <p>Create your first invoice template to get started.</p>
            <a href="invoice_template.php" class="btn btn-success">
                <i class="bi bi-plus me-1" aria-hidden="true"></i>Create Template
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Preview</th>
                        <th class="text-start">Template Name</th>
                        <th>Type</th>
                        <th>Last Modified</th>
                        <th class="text-center" style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($templates as $template): ?>
                <tr>
                    <td>
                        <?php if (file_exists($template . '/preview.png')): ?>
                        <img src="<?='../../invoice_system/templates/' . basename($template) . '/preview.png'?>" alt="<?=basename($template)?>" class="rounded" style="width: 480px; height: 320px; object-fit: cover;">
                        <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width: 480px; height: 320px;">
                            <i class="bi bi-file-earmark-text text-muted fa-3x" aria-hidden="true"></i>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="text-start">
                        <div class="fw-medium"><?=htmlspecialchars(ucwords(str_replace('_', ' ', basename($template))), ENT_QUOTES)?></div>
                        <small class="text-muted"><?=basename($template)?></small>
                    </td>
                    <td>
                        <span class="badge bg-primary">Invoice Template</span>
                    </td>
                    <td>
                        <small class="text-muted">
                            <?php 
                            $last_modified = is_dir($template) ? filemtime($template) : time();
                            echo date('M j, Y g:i A', $last_modified);
                            ?>
                        </small>
                    </td>
                    <td class="actions" style="text-align: center;">
                        <div class="table-dropdown">
                            <button class="actions-btn" aria-haspopup="true" aria-expanded="false"
                                aria-label="Actions for <?=htmlspecialchars(ucwords(str_replace('_', ' ', basename($template))), ENT_QUOTES)?>">
                                <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                    <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                </svg>
                            </button>
                            <div class="table-dropdown-items" role="menu" aria-label="Template Actions">
                                <div role="menuitem">
                                    <a href="invoice_template.php?id=<?=basename($template)?>" 
                                       class="green" 
                                       tabindex="-1" 
                                       aria-label="Edit <?=htmlspecialchars(ucwords(str_replace('_', ' ', basename($template))), ENT_QUOTES)?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z" />
                                            </svg>
                                        </span>
                                        <span>Edit</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a href="invoice_templates.php?duplicate=<?=basename($template)?>" 
                                       class="blue" 
                                       tabindex="-1" 
                                       aria-label="Duplicate <?=htmlspecialchars(ucwords(str_replace('_', ' ', basename($template))), ENT_QUOTES)?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path d="M288 448H64V224h64V160H64c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H288c35.3 0 64-28.7 64-64V384H288v64zm-64-96H448c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64H224c-35.3 0-64 28.7-64 64V288c0 35.3 28.7 64 64 64z" />
                                            </svg>
                                        </span>
                                        <span>Duplicate</span>
                                    </a>
                                </div>
                                <div role="menuitem">
                                    <a href="invoice_templates.php?delete=<?=basename($template)?>" 
                                       class="red" 
                                       tabindex="-1" 
                                       aria-label="Delete <?=htmlspecialchars(ucwords(str_replace('_', ' ', basename($template))), ENT_QUOTES)?>" 
                                       onclick="return confirm('Are you sure you want to delete this template?')">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z" />
                                            </svg>
                                        </span>
                                        <span>Delete</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?=template_admin_footer()?>