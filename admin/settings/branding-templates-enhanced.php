<?php
/**
 * Enhanced Branding Templates Management - Simplified Version
 * Admin interface for managing branding templates using existing database structure
 */

include_once '../assets/includes/main.php';

// Security check for admin access (aligned with main admin system)
if (!isset($_SESSION['admin_role']) || !in_array($_SESSION['admin_role'], ['Admin', 'Developer'])) {
    header('Location: ../index.php');
    exit();
}

$message = '';
$messageType = '';

// Simple functions to work with existing database structure
function getAvailableTemplates() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM setting_branding_templates ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function setActiveTemplate($templateId) {
    global $pdo;
    try {
        // Set all templates to inactive
        $pdo->query("UPDATE setting_branding_templates SET is_active = 0");
        
        // Set the selected template to active
        $stmt = $pdo->prepare("UPDATE setting_branding_templates SET is_active = 1 WHERE id = ?");
        return $stmt->execute([$templateId]);
    } catch (Exception $e) {
        return false;
    }
}

function getActiveTemplate() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM setting_branding_templates WHERE is_active = 1 LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null;
    } catch (Exception $e) {
        return null;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'set_template':
                $templateId = (int)$_POST['template_id'];
                
                if (setActiveTemplate($templateId)) {
                    $message = "Template updated successfully!";
                    $messageType = 'success';
                } else {
                    $message = "Failed to update template";
                    $messageType = 'danger';
                }
                break;
        }
    }
}

// Get available templates and current active template
$availableTemplates = getAvailableTemplates();
$activeTemplate = getActiveTemplate();

// Call the admin header template
echo template_admin_header('Enhanced Branding Templates', 'settings', 'branding');
?>

<style>
.template-preview {
    height: 120px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 10px;
    position: relative;
    overflow: hidden;
}
.template-preview.active {
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}
.template-preview .preview-content {
    padding: 10px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.template-card {
    transition: all 0.3s ease;
}
.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Enhanced Branding Templates</h3>
                </div>
                
                <div class="card-body">

                    <?php if ($message): ?>
                    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                        <i class="bi <?= $messageType === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?>"></i> 
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php endif; ?>

                    <!-- Navigation -->
                    <div class="mb-4">
                        <a href="branding_settings.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Branding Settings
                        </a>
                        <a href="settings_dash.php" class="btn btn-outline-secondary ml-2">
                            <i class="bi bi-gear"></i> Settings Dashboard
                        </a>
                    </div>

                    <!-- Current Active Template -->
                    <div class="alert alert-info mb-4">
                        <h5><i class="bi bi-info-circle"></i> Current Active Template</h5>
                        <?php if ($activeTemplate): ?>
                            <strong><?= htmlspecialchars($activeTemplate['template_name']) ?></strong> - 
                            <?= htmlspecialchars($activeTemplate['template_description']) ?>
                        <?php else: ?>
                            <em>No active template selected</em>
                        <?php endif; ?>
                    </div>

                    <!-- Template Selection -->
                    <div class="row">
                        <?php foreach ($availableTemplates as $template): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card template-card <?= $activeTemplate && $activeTemplate['id'] === $template['id'] ? 'border-primary' : '' ?>">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?= htmlspecialchars($template['template_name']) ?>
                                        <?php if ($activeTemplate && $activeTemplate['id'] === $template['id']): ?>
                                            <span class="badge badge-primary ml-2">Active</span>
                                        <?php endif; ?>
                                    </h5>
                                    <p class="card-text"><?= htmlspecialchars($template['template_description']) ?></p>
                                    
                                    <?php if (!$activeTemplate || $activeTemplate['id'] !== $template['id']): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="set_template">
                                        <input type="hidden" name="template_id" value="<?= $template['id'] ?>">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check"></i> Activate Template
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <button class="btn btn-success" disabled>
                                        <i class="bi bi-check-circle-fill"></i> Currently Active
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (empty($availableTemplates)): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill"></i> 
                        <strong>No templates found!</strong> Please check your database configuration or contact your system administrator.
                    </div>
                    <?php endif; ?>
                
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo template_admin_footer(); ?>
