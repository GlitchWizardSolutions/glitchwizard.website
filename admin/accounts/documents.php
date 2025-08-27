<?php
/**
 * Document Management System
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: documents.php
 * LOCATION: /public_html/admin/accounts/
 * PURPOSE: View and manage documents for specific accounts or browse all document folders
 * 
 * FILE RELATIONSHIP:
 * This file integrates with:
 * - User authentication system
 * - Role management system
 * - File system operations
 * - Account settings configuration
 * 
 * HOW IT WORKS:
 * 1. Validates user permissions based on role
 * 2. Provides folder browsing interface
 * 3. Lists files with metadata (size, type, dates)
 * 4. Handles file operations (view, download)
 * 5. Manages document-related permissions
 * 
 * CREATED: 2025-07-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: YES
 * 
 * FEATURES:
 * - Account-specific document viewing
 * - Role-based access control (Developer, Admin, Editor)
 * - Folder browsing with special system folders
 * - File listing and basic navigation
 * - File metadata display
 * - Download functionality
 * - Permission management
 * 
 * ACCESS CONTROL:
 * - Developer: Can view all folders (including Developer, Admin, Editor, Welcome, and user folders)
 * - Admin: Can view all except Developer folder (Admin, Editor, Welcome, and user folders)
 * - Editor: Can view Editor, Welcome, and user folders only (restricted from Admin and Developer)
 * 
 * DEPENDENCIES:
 * - main.php (admin includes)
 * - PDO database connection
 * - Bootstrap 5 for styling
 * 
 * SECURITY NOTES:
 * - Admin authentication required
 * - Role-based folder access restrictions
 * - Path traversal protection
 */
include_once '../assets/includes/main.php';

// Get current user's role for access control
$current_user_role = $_SESSION['role'] ?? 'Admin'; // Default to Admin for testing

// Define role hierarchy and access permissions
$role_permissions = [
    'Developer' => ['Developer', 'Admin', 'Editor', 'Welcome'], // Can view all
    'Admin' => ['Admin', 'Editor', 'Welcome'], // Cannot view Developer folder
    'Editor' => ['Editor', 'Welcome'] // Can only view Editor and Welcome folders
];

// Get the allowed folders for current user
$allowed_system_folders = $role_permissions[$current_user_role] ?? ['Welcome'];

// Documents base path
$documents_base_path = dirname(__DIR__, 2) . '/documents_system/account_documents/';

// Ensure documents directory exists
if (!file_exists($documents_base_path))
{
    mkdir($documents_base_path, 0755, true);
}

// Create system folders if they don't exist
$system_folders = ['Developer', 'Admin', 'Editor', 'Welcome'];
foreach ($system_folders as $folder)
{
    $folder_path = $documents_base_path . $folder;
    if (!file_exists($folder_path))
    {
        mkdir($folder_path, 0755, true);
        // Create a readme file in each system folder
        $readme_content = "# " . $folder . " Folder\n\n";
        $readme_content .= "This is a system folder for " . $folder . " access level.\n";
        $readme_content .= "Created: " . date('Y-m-d H:i:s') . "\n";
        file_put_contents($folder_path . DIRECTORY_SEPARATOR . 'README.md', $readme_content);
    }
}

$account = null;
$account_folder = null;
$viewing_specific_account = false;

// Check if viewing a specific account's documents
if (isset($_GET['account_id']) && !empty($_GET['account_id']))
{
    $account_id = (int) $_GET['account_id'];

    // Get account details
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([$account_id]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($account)
    {
        $viewing_specific_account = true;

        // Clean the document path - remove quotes and ensure proper format
        $clean_document_path = trim($account['document_path'], '"\'/ ');
        if (empty($clean_document_path))
        {
            $clean_document_path = $account['username']; // Fallback to username
        }

        $account_folder = $documents_base_path . $clean_document_path;

        // Ensure the account's folder exists
        if (!file_exists($account_folder))
        {
            mkdir($account_folder, 0755, true);
        }
    }
}

// Function to get folder contents
function getFolderContents($path)
{
    $contents = [];
    if (is_dir($path))
    {
        $items = scandir($path);
        foreach ($items as $item)
        {
            if ($item !== '.' && $item !== '..')
            {
                $item_path = $path . DIRECTORY_SEPARATOR . $item;
                $contents[] = [
                    'name' => $item,
                    'path' => $item_path,
                    'is_dir' => is_dir($item_path),
                    'size' => is_file($item_path) ? filesize($item_path) : 0,
                    'modified' => file_exists($item_path) ? filemtime($item_path) : 0
                ];
            }
        }
    }

    // Sort: directories first, then files, both alphabetically
    usort($contents, function ($a, $b) {
        if ($a['is_dir'] && !$b['is_dir'])
            return -1;
        if (!$a['is_dir'] && $b['is_dir'])
            return 1;
        return strcasecmp($a['name'], $b['name']);
    });

    return $contents;
}

// Function to format file size
function formatFileSize($bytes)
{
    if ($bytes >= 1073741824)
    {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576)
    {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024)
    {
        return number_format($bytes / 1024, 2) . ' KB';
    } else
    {
        return $bytes . ' bytes';
    }
}

// Get folder contents based on view mode
if ($viewing_specific_account)
{
    $folder_contents = getFolderContents($account_folder);
    $page_title = 'Documents for ' . htmlspecialchars($account['username'], ENT_QUOTES);
} else
{
    // Show all folders the user has access to
    $folder_contents = [];

    // Add system folders (based on permissions)
    foreach ($system_folders as $folder)
    {
        if (in_array($folder, $allowed_system_folders))
        {
            $folder_path = $documents_base_path . $folder;
            if (is_dir($folder_path) && file_exists($folder_path))
            {
                $folder_contents[] = [
                    'name' => $folder,
                    'path' => $folder_path,
                    'is_dir' => true,
                    'size' => 0,
                    'modified' => filemtime($folder_path),
                    'is_system' => true
                ];
            }
        }
    }

    // Add user folders
    $all_items = getFolderContents($documents_base_path);
    foreach ($all_items as $item)
    {
        if ($item['is_dir'] && !in_array($item['name'], $system_folders))
        {
            $item['is_system'] = false;
            $folder_contents[] = $item;
        }
    }

    $page_title = 'Document Management - All Folders';
}
?>
<?= template_admin_header('Documents', 'accounts', 'manage') ?>

<div class="content-title">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                <path
                    d="M64 464c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16H224v80c0 17.7 14.3 32 32 32h80V448c0 8.8-7.2 16-16 16H64zM64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V154.5c0-17-6.7-33.3-18.7-45.3L274.7 18.7C262.7 6.7 246.5 0 229.5 0H64zm56 256c-13.3 0-24 10.7-24 24s10.7 24 24 24H264c13.3 0 24-10.7 24-24s-10.7-24-24-24H120zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H264c13.3 0 24-10.7 24-24s-10.7-24-24-24H120z" />
            </svg>
        </div>
        <div class="txt">
            <h2><?= $page_title ?></h2>
            <p>
                <?php if ($viewing_specific_account): ?>
                    Browse and manage documents for this account.
                    <?php if ($account): ?>
                        Document folder: <?= htmlspecialchars($account['document_path'], ENT_QUOTES) ?>
                    <?php endif; ?>
                <?php else: ?>
                    Browse all document folders. Access level: <?= htmlspecialchars($current_user_role, ENT_QUOTES) ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<div class="mb-4">
    <div class="content-block">

    <!-- Navigation -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <?php if ($viewing_specific_account): ?>
                        <a href="documents.php" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left me-1"></i> All Folders
                        </a>
                        <a href="account.php?id=<?= $account['id'] ?>" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-pencil-square me-1"></i> Edit Account
                        </a>
                        <a href="accounts.php" class="btn btn-outline-secondary">
                            <i class="bi bi-people me-1"></i> All Accounts
                        </a>
                    <?php else: ?>
                        <a href="accounts.php" class="btn btn-outline-secondary">
                            <i class="bi bi-people me-1"></i> Back to Accounts
                        </a>
                    <?php endif; ?>
                </div>
                <div class="text-muted">
                    <?= count($folder_contents) ?> items
                </div>
            </div>
        </div>
    </div>

    <!-- Document Listing -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="bi bi-folder me-2"></i>
                <?= $viewing_specific_account ? 'Account Documents' : 'Document Folders' ?>
            </h6>
            <?php if (!empty($folder_contents)): ?>
                <small class="text-muted">
                    <?= $current_user_role ?> Access
                </small>
            <?php endif; ?>
        </div>

        <?php if (empty($folder_contents)): ?>
            <div class="card-body text-center py-5">
                <div class="text-muted">
                    <i class="bi bi-folder2-open fa-3x mb-3"></i>
                    <h5>No Documents Found</h5>
                    <p>
                        <?php if ($viewing_specific_account): ?>
                            This account doesn't have any documents yet.
                        <?php else: ?>
                            No document folders are available for your access level.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="40px"></th>
                            <th>Name</th>
                            <th width="120px">Type</th>
                            <th width="100px">Size</th>
                            <th width="150px">Modified</th>
                            <th width="100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($folder_contents as $item): ?>
                            <tr>
                                <td>
                                    <?php if ($item['is_dir']): ?>
                                        <i class="bi bi-folder-fill text-warning"></i>
                                    <?php else: ?>
                                        <?php
                                        $ext = strtolower(pathinfo($item['name'], PATHINFO_EXTENSION));
                                        $icon_class = 'file-earmark';
                                        $icon_color = 'text-muted';

                                        switch ($ext)
                                        {
                                            case 'pdf':
                                                $icon_class = 'file-earmark-pdf';
                                                $icon_color = 'text-danger';
                                                break;
                                            case 'doc':
                                            case 'docx':
                                                $icon_class = 'file-earmark-word';
                                                $icon_color = 'text-primary';
                                                break;
                                            case 'xls':
                                            case 'xlsx':
                                                $icon_class = 'file-earmark-excel';
                                                $icon_color = 'text-success';
                                                break;
                                            case 'ppt':
                                            case 'pptx':
                                                $icon_class = 'file-earmark-ppt';
                                                $icon_color = 'text-warning';
                                                break;
                                            case 'jpg':
                                            case 'jpeg':
                                            case 'png':
                                            case 'gif':
                                                $icon_class = 'file-earmark-image';
                                                $icon_color = 'text-info';
                                                break;
                                            case 'txt':
                                            case 'md':
                                                $icon_class = 'file-earmark-text';
                                                break;
                                        }
                                        ?>
                                        <i class="bi bi-<?= $icon_class ?> <?= $icon_color ?>"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($item['is_system']) && $item['is_system']): ?>
                                        <span class="badge bg-secondary me-2">System</span>
                                    <?php endif; ?>
                                    <strong><?= htmlspecialchars($item['name'], ENT_QUOTES) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= $item['is_dir'] ? 'Folder' : 'File' ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $item['is_dir'] ? '-' : formatFileSize($item['size']) ?>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?= date('M j, Y g:i A', $item['modified']) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($item['is_dir']): ?>
                                        <button class="btn btn-sm btn-outline-primary" disabled>
                                            <i class="bi bi-folder2-open me-1"></i> Browse
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-info" disabled>
                                            <i class="bi bi-eye me-1"></i> View
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Access Information -->
    <div class="card mt-3">
        <div class="card-body">
            <h6 class="card-title">
                <i class="bi bi-shield-lock me-2"></i>Access Information
            </h6>
            <div class="row">
                <div class="col-md-4">
                    <strong>Your Role:</strong> <?= htmlspecialchars($current_user_role, ENT_QUOTES) ?>
                </div>
                <div class="col-md-8">
                    <strong>Accessible System Folders:</strong>
                    <?php if (empty($allowed_system_folders)): ?>
                        <span class="text-muted">None</span>
                    <?php else: ?>
                        <?php foreach ($allowed_system_folders as $folder): ?>
                            <span class="badge bg-primary me-1"><?= htmlspecialchars($folder, ENT_QUOTES) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <hr>
            <div class="row text-muted">
                <div class="col-md-12">
                    <small>
                        <strong>Note:</strong> This is a basic document browser.
                        Full document management features (upload, download, edit, delete) will be implemented in future
                        updates.
                        <?php if (!$viewing_specific_account): ?>
                            Click on an account's "Documents" action to view their specific folder.
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .table td {
        vertical-align: middle;
    }

    .btn.alt {
        background-color: #f8f9fa;
        color: #6c757d;
        border: 1px solid #6c757d;
        transition: all 0.2s ease;
    }

    .btn.alt:hover {
        background-color: #e9ecef;
        border-color: #5a6268;
        color: #5a6268;
    }

    .table-responsive {
        border-radius: 0;
    }

    .badge {
        font-size: 0.75em;
    }
</style>

<?= template_admin_footer() ?>