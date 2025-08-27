<?php
/**
 * Meta Tag Settings Management
 * ...existing docblock...
 */
include_once '../assets/includes/main.php';
if (!isset($_SESSION['admin_role']) || !in_array(strtolower($_SESSION['admin_role']), ['admin', 'editor', 'developer']))
{
    header('Location: ../index.php?error=unauthorized');
    exit();
}
$file = PROJECT_ROOT . '/assets/settings/meta-config.php';
if (!file_exists($file))
{
    die('<div class="alert alert-danger">Meta config file not found: ' . htmlspecialchars($file) . '</div>');
}
include $file;
if (!isset($meta_data) || !is_array($meta_data))
{
    $meta_data = [];
}
// Handle form submission and file uploads
$success_msg = $error_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $meta = $_POST['meta'] ?? [];
    $new_meta_data = $meta_data;
    $upload_dir = PROJECT_ROOT . '/assets/img/social-share/';
    if (!is_dir($upload_dir))
    {
        mkdir($upload_dir, 0755, true);
    }
    // Handle updates for each page
    foreach ($meta as $page => $fields)
    {
        if ($page === 'new')
        {
            $new_page = trim($fields['page'] ?? '');
            if ($new_page)
            {
                $new_meta_data[$new_page] = [
                    'description' => $fields['description'] ?? '',
                    'keywords' => $fields['keywords'] ?? '',
                    'image' => '',
                    'social' => []
                ];
            }
            continue;
        }
        // Update description/keywords/image
        $new_meta_data[$page]['description'] = $fields['description'] ?? '';
        $new_meta_data[$page]['keywords'] = $fields['keywords'] ?? '';
        $new_meta_data[$page]['image'] = $fields['image'] ?? '';
        // Handle per-platform social
        foreach (['facebook', 'twitter', 'linkedin'] as $platform)
        {
            if (!isset($new_meta_data[$page]['social'][$platform]))
            {
                $new_meta_data[$page]['social'][$platform] = [];
            }
            $new_meta_data[$page]['social'][$platform]['description'] = $fields['social'][$platform]['description'] ?? '';
            // Sanitize page name for file input key
            $sanitized_page = preg_replace('/[^a-zA-Z0-9_]/', '_', $page);
            $file_key = 'meta_' . $sanitized_page . '_' . $platform . '_image';
            if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK)
            {
                $tmp_name = $_FILES[$file_key]['tmp_name'];
                $orig_name = basename($_FILES[$file_key]['name']);
                $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($ext, $allowed))
                {
                    $safe_name = $sanitized_page . '_' . $platform . '_' . time() . '.' . $ext;
                    $dest = $upload_dir . $safe_name;
                    if (move_uploaded_file($tmp_name, $dest))
                    {
                        $rel_path = 'assets/img/social-share/' . $safe_name;
                        $new_meta_data[$page]['social'][$platform]['image'] = $rel_path;
                    } else
                    {
                        $error_msg .= "Failed to upload $platform image for $page. ";
                    }
                } else
                {
                    $error_msg .= "Invalid file type for $platform image on $page. ";
                }
            } elseif (!empty($fields['social'][$platform]['image']))
            {
                $new_meta_data[$page]['social'][$platform]['image'] = $fields['social'][$platform]['image'];
            }
        }
    }
    // Save updated meta config
    $config_file = $file;
    $config_content = "<?php\n$" . "meta_data = " . var_export($new_meta_data, true) . ";\n";
    if (file_put_contents($config_file, $config_content))
    {
        $success_msg = 'Meta tags updated successfully.';
        $meta_data = $new_meta_data;
    } else
    {
        $error_msg .= 'Failed to save meta config.';
    }
}
function h($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Render header
echo template_admin_header('Meta Tag Settings', 'settings', 'metatags');

?>


<div class="alert alert-info mb-4"><i class="bi bi-info-circle me-2"></i>
    Edit meta description, keywords, and social share image for each public page. Changes are live immediately.
</div>

<?php if ($success_msg): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Success!</strong> <?= h($success_msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if ($error_msg): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-octagon-fill me-2"></i>
        <strong>Error!</strong> <?= h($error_msg) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form action="" method="post" id="metaTagForm" enctype="multipart/form-data" novalidate>
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <div class="content-header form-actions-header mb-2">
                <div class="form-actions">
                    <a href="settings.php" class="btn btn-outline-secondary" aria-label="Cancel and return to settings">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success" aria-label="Save meta tags">
                        <i class="bi bi-save me-2"></i>Save Meta Tags
                    </button>
                </div>
            </div>
            <strong>Edit Meta Tags for Each Page</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Page</th>
                            <th>Description</th>
                            <th>Keywords</th>
                            <th style="width:60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($meta_data as $page => $fields): ?>
                            <tr>
                                <td><?= h($page) ?></td>
                                <td><?= h($fields['description'] ?? '') ?></td>
                                <td><?= h($fields['keywords'] ?? '') ?></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-link p-0 text-dark" type="button"
                                            id="actionDropdown<?= md5($page) ?>" data-bs-toggle="dropdown"
                                            aria-expanded="false" title="Actions" style="font-size:1.5rem;line-height:1;">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="actionDropdown<?= md5($page) ?>">
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#viewMetaModal" data-page="<?= h($page) ?>">View</a>
                                            </li>
                                            <li><a class="dropdown-item text-success" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#editMetaModal" data-page="<?= h($page) ?>">Edit</a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#deleteMetaModal" data-page="<?= h($page) ?>">Delete</a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <!-- Row for adding a new page -->
                        <tr>
                            <td><input type="text" name="meta[new][page]" class="form-control"
                                    placeholder="New page (e.g. contact.php)" /></td>
                            <td><input type="text" name="meta[new][description]" class="form-control"
                                    placeholder="Description" /></td>
                            <td><input type="text" name="meta[new][keywords]" class="form-control"
                                    placeholder="Keywords" /></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- View Modal -->
            <div class="modal fade" id="viewMetaModal" tabindex="-1" aria-labelledby="viewMetaModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewMetaModalLabel">View Meta Tag Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="viewMetaModalBody">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit Modal -->
            <div class="modal fade" id="editMetaModal" tabindex="-1" aria-labelledby="editMetaModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editMetaModalLabel">Edit Meta Tag</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="editMetaModalBody">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Delete Modal -->
            <div class="modal fade" id="deleteMetaModal" tabindex="-1" aria-labelledby="deleteMetaModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteMetaModalLabel">Delete Meta Tag</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="deleteMetaModalBody">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="content-header form-actions-header">
                <div class="form-actions">
                    <a href="settings.php" class="btn btn-outline-secondary" aria-label="Cancel and return to settings">
                        <i class="bi bi-arrow-left" aria-hidden="true"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success" aria-label="Save meta tags">
                        <i class="bi bi-save me-2"></i>Save Meta Tags
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // Action menu modals for View/Edit/Delete
    const metaData = <?php echo json_encode($meta_data); ?>;
    document.addEventListener('DOMContentLoaded', function () {
        // View
        document.querySelectorAll('a[data-bs-target="#viewMetaModal"]').forEach(function (el) {
            el.addEventListener('click', function () {
                const page = this.getAttribute('data-page');
                const data = metaData[page];
                let html = `<strong>Page:</strong> ${page}<br>`;
                html += `<strong>Description:</strong> ${data.description || ''}<br>`;
                html += `<strong>Keywords:</strong> ${data.keywords || ''}<br>`;
                html += `<strong>Default Social Image:</strong> ${data.image || ''}<br>`;
                html += `<hr><strong>Per-Platform Social:</strong><br>`;
                ['facebook', 'twitter', 'linkedin'].forEach(function (platform) {
                    html += `<div class='mb-2'><span class='badge bg-secondary text-capitalize'>${platform}</span> `;
                    if (data.social && data.social[platform] && data.social[platform].image) {
                        html += `<img src='/${data.social[platform].image}' style='height:24px;width:auto;vertical-align:middle;' />`;
                    }
                    html += `<br><strong>Description:</strong> ${(data.social && data.social[platform] && data.social[platform].description) || ''}`;
                    html += `</div>`;
                });
                document.getElementById('viewMetaModalBody').innerHTML = html;
            });
        });
        // Edit
        document.querySelectorAll('a[data-bs-target="#editMetaModal"]').forEach(function (el) {
            el.addEventListener('click', function () {
                const page = this.getAttribute('data-page');
                const data = metaData[page];
                // Sanitize page for file input name
                const sanitizedPage = page.replace(/[^a-zA-Z0-9_]/g, '_');
                let html = `<form id="editMetaForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <input type="text" class="form-control" name="meta[${page}][description]" value="${data.description || ''}" required />
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Keywords</label>
                    <input type="text" class="form-control" name="meta[${page}][keywords]" value="${data.keywords || ''}" />
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Default Social Image</label>
                    <input type="text" class="form-control" name="meta[${page}][image]" value="${data.image || ''}" />
                </div>`;
                ['facebook', 'twitter', 'linkedin'].forEach(function (platform) {
                    html += `<div class='mb-3 border rounded p-2'>`;
                    html += `<span class='badge bg-secondary text-capitalize mb-1'>${platform}</span><br>`;
                    html += `<label class='form-label'>${platform.charAt(0).toUpperCase() + platform.slice(1)} Description</label>`;
                    html += `<input type='text' class='form-control form-control-sm mb-1' name='meta[${page}][social][${platform}][description]' value='${(data.social && data.social[platform] && data.social[platform].description) || ''}' />`;
                    html += `<label class='form-label'>${platform.charAt(0).toUpperCase() + platform.slice(1)} Image</label>`;
                    html += `<input type='file' class='form-control form-control-sm mb-1' name='meta_${sanitizedPage}_${platform}_image' accept='image/*' />`;
                    if (data.social && data.social[platform] && data.social[platform].image) {
                        html += `<img src='/${data.social[platform].image}' style='height:24px;width:auto;vertical-align:middle;' />`;
                    }
                    html += `</div>`;
                });
                html += `<input type="hidden" name="page" value="${page}" />
            </form>`;
                document.getElementById('editMetaModalBody').innerHTML = html;
                // Optionally, handle AJAX save here
            });
        });
        // Delete
        document.querySelectorAll('a[data-bs-target="#deleteMetaModal"]').forEach(function (el) {
            el.addEventListener('click', function () {
                const page = this.getAttribute('data-page');
                document.getElementById('deleteMetaModalBody').innerHTML = `<p>Are you sure you want to delete meta tags for <strong>${page}</strong>?</p>`;
            });
        });
    });
</script>

<?= template_admin_footer() ?>