<?php
/**
 * Branding Template Management
 * 
 * Admin interface for selecting and managing branding templates for public pages
 */

include_once '../assets/includes/main.php';
require_once '../../../private/classes/SettingsManager.php';
require_once '../../assets/includes/branding-functions.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'set_active_template' && isset($_POST['template_key'])) {
        if (setActiveBrandingTemplate($_POST['template_key'])) {
            $success_message = "✅ Branding template updated successfully!";
        } else {
            $error_message = "❌ Failed to update branding template.";
        }
    }
}

// Get all templates and active template
$all_templates = getAllBrandingTemplates();
$active_template = getActiveBrandingTemplate();

include_once '../assets/includes/header.php';
?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1>Branding Templates</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="settings.php">Settings</a></li>
                <li class="breadcrumb-item active">Branding Templates</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-palette"></i> Public Page Branding Templates
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Select the branding style for your public-facing pages. Each template provides a different 
                    visual experience while maintaining your brand colors and identity.
                </p>

                <?php if (empty($all_templates)): ?>
                <div class="alert alert-warning">
                    <h6><i class="bi bi-exclamation-triangle"></i> No Templates Found</h6>
                    <p class="mb-2">Branding templates have not been initialized yet.</p>
                    <a href="../../assets/includes/init-branding-templates.php" class="btn btn-primary">
                        <i class="bi bi-gear"></i> Initialize Templates
                    </a>
                </div>
                <?php else: ?>

                <div class="row">
                    <?php foreach ($all_templates as $template): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card template-card <?php echo $template['is_active'] ? 'border-primary' : 'border-light'; ?>" 
                             style="transition: all 0.3s ease;">
                            
                            <!-- Preview Image -->
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                 style="height: 180px; position: relative;">
                                <?php if (!empty($template['preview_image']) && file_exists('../../' . $template['preview_image'])): ?>
                                    <img src="../../<?php echo $template['preview_image']; ?>" 
                                         alt="<?php echo htmlspecialchars($template['template_name']); ?> Preview"
                                         class="img-fluid rounded" style="max-height: 160px;">
                                <?php else: ?>
                                    <div class="text-center">
                                        <i class="bi bi-image display-1 text-muted"></i>
                                        <p class="text-muted small mt-2">Preview Coming Soon</p>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Active Badge -->
                                <?php if ($template['is_active']): ?>
                                <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                    <i class="bi bi-check-circle"></i> Active
                                </span>
                                <?php endif; ?>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title d-flex align-items-center">
                                    <?php echo htmlspecialchars($template['template_name']); ?>
                                    <?php if ($template['template_key'] === 'default'): ?>
                                        <span class="badge bg-info ms-2 small">Default</span>
                                    <?php endif; ?>
                                </h5>
                                
                                <p class="card-text text-muted small">
                                    <?php echo htmlspecialchars($template['template_description']); ?>
                                </p>

                                <!-- Template Features -->
                                <?php if (isset($template['config']['features'])): ?>
                                <div class="mb-3">
                                    <h6 class="small fw-bold text-muted">Features:</h6>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($template['config']['features'] as $feature): ?>
                                        <span class="badge bg-light text-dark small">
                                            <?php echo ucwords(str_replace('_', ' ', $feature)); ?>
                                        </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Template Info -->
                                <div class="mb-3">
                                    <div class="row g-2 small">
                                        <div class="col-6">
                                            <strong>Style:</strong><br>
                                            <span class="text-muted"><?php echo ucfirst($template['config']['style'] ?? 'Standard'); ?></span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Contrast:</strong><br>
                                            <span class="text-muted"><?php echo ucfirst($template['config']['contrast'] ?? 'Medium'); ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <?php if ($template['is_active']): ?>
                                        <button class="btn btn-success" disabled>
                                            <i class="bi bi-check-circle"></i> Currently Active
                                        </button>
                                    <?php else: ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="set_active_template">
                                            <input type="hidden" name="template_key" value="<?php echo $template['template_key']; ?>">
                                            <button type="submit" class="btn btn-primary w-100" 
                                                    onclick="return confirm('Switch to <?php echo htmlspecialchars($template['template_name']); ?> template?')">
                                                <i class="bi bi-palette"></i> Activate Template
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <a href="../../index.php" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-eye"></i> Preview Site
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php endif; ?>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> About Branding Templates
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Template Types:</h6>
                        <ul class="small">
                            <li><strong>Default:</strong> Standard professional styling</li>
                            <li><strong>High Contrast:</strong> Accessibility-focused with enhanced visibility</li>
                            <li><strong>Subtle:</strong> Minimal, understated elegance</li>
                            <li><strong>Bold:</strong> Vibrant, high-impact design</li>
                            <li><strong>Casual:</strong> Friendly, approachable styling</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Template Features:</h6>
                        <ul class="small">
                            <li>All templates use your brand colors and fonts</li>
                            <li>Responsive design for all devices</li>
                            <li>Consistent navigation and layout</li>
                            <li>Easy switching without losing content</li>
                            <li>Professional quality across all variations</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-3 p-3 bg-light rounded">
                    <h6><i class="bi bi-lightbulb"></i> Pro Tip:</h6>
                    <p class="mb-0 small">
                        You can switch between templates anytime without affecting your content or settings. 
                        Try different templates to see which best represents your brand and appeals to your audience.
                    </p>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
.template-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.template-card.border-primary {
    box-shadow: 0 0 0 2px rgba(13,110,253,0.25);
}

.template-card .card-img-top {
    background: linear-gradient(45deg, #f8f9fa 0%, #e9ecef 100%);
}
</style>

<script>
// Auto-refresh page after template change to show new styling
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('updated') === '1') {
        // Remove the parameter from URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>

<?php include_once '../assets/includes/footer.php'; ?>
