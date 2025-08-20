<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Branding System - Template Testing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <?php
    require_once '../private/gws-universal-config.php';
    require_once 'public_html/shared/branding-functions-enhanced.php';
    
    // Get current template overview
    $overview = getBrandingTemplateOverview();
    $syncMode = $overview['sync_mode'];
    
    // Test switching templates (only if not in production)
    $testTemplate = $_GET['test_template'] ?? null;
    $testArea = $_GET['test_area'] ?? 'public';
    
    if ($testTemplate && in_array($testTemplate, ['default', 'high-contrast', 'subtle', 'bold', 'casual'])) {
        // Temporarily load CSS for testing without changing database
        $cssFile = match($testArea) {
            'admin' => "public_html/admin/assets/css/admin-branding-{$testTemplate}.css",
            'client_portal' => "public_html/client_portal/assets/css/client-branding-{$testTemplate}.css",
            default => "public_html/assets/css/public-branding-{$testTemplate}.css"
        };
        
        if (file_exists($cssFile)) {
            echo "<link rel='stylesheet' href='/{$cssFile}?v=" . time() . "'>\n";
        }
    }
    
    // Load brand variables
    $brandVariables = generateBrandCSSVariables($testArea);
    ?>
    
    <style>
        :root {
            <?php foreach ($brandVariables as $property => $value): ?>
            <?= $property ?>: <?= $value ?>;
            <?php endforeach; ?>
        }
        
        .template-demo-section {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        
        .area-switcher {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .template-switcher {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .demo-element {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Area Switcher -->
    <div class="area-switcher">
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-layer-group me-2"></i>Current Area: <?= ucfirst($testArea) ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?test_area=public&test_template=<?= $testTemplate ?>">
                    <i class="fas fa-globe me-2"></i>Public Website
                </a></li>
                <li><a class="dropdown-item" href="?test_area=admin&test_template=<?= $testTemplate ?>">
                    <i class="fas fa-cogs me-2"></i>Admin Interface
                </a></li>
                <li><a class="dropdown-item" href="?test_area=client_portal&test_template=<?= $testTemplate ?>">
                    <i class="fas fa-users me-2"></i>Client Portal
                </a></li>
            </ul>
        </div>
    </div>

    <!-- Template Switcher -->
    <div class="template-switcher">
        <div class="dropdown dropup">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-palette me-2"></i>Template: <?= ucfirst($testTemplate ?? 'Current') ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?test_area=<?= $testArea ?>&test_template=default">
                    <i class="fas fa-circle me-2"></i>Default
                </a></li>
                <li><a class="dropdown-item" href="?test_area=<?= $testArea ?>&test_template=high-contrast">
                    <i class="fas fa-adjust me-2"></i>High Contrast
                </a></li>
                <li><a class="dropdown-item" href="?test_area=<?= $testArea ?>&test_template=subtle">
                    <i class="fas fa-minus me-2"></i>Subtle
                </a></li>
                <li><a class="dropdown-item" href="?test_area=<?= $testArea ?>&test_template=bold">
                    <i class="fas fa-plus me-2"></i>Bold
                </a></li>
                <li><a class="dropdown-item" href="?test_area=<?= $testArea ?>&test_template=casual">
                    <i class="fas fa-smile me-2"></i>Casual
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="?test_area=<?= $testArea ?>">
                    <i class="fas fa-undo me-2"></i>Current Active
                </a></li>
            </ul>
        </div>
    </div>

    <div class="container mt-4">
        <h1 class="mb-4">
            <i class="fas fa-vial me-2"></i>Enhanced Branding System - Template Testing
        </h1>
        
        <!-- Current Status -->
        <div class="alert alert-info">
            <h5><i class="fas fa-info-circle me-2"></i>Current Configuration</h5>
            <div class="row">
                <div class="col-md-6">
                    <strong>Sync Mode:</strong> <?= ucfirst($syncMode) ?><br>
                    <strong>Test Area:</strong> <?= ucfirst(str_replace('_', ' ', $testArea)) ?><br>
                    <strong>Test Template:</strong> <?= ucfirst($testTemplate ?? 'Active Template') ?>
                </div>
                <div class="col-md-6">
                    <?php foreach ($overview['areas'] as $area => $data): ?>
                    <strong><?= ucfirst(str_replace('_', ' ', $area)) ?>:</strong> <?= ucfirst($data['active_template']) ?><br>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Navigation Demo -->
        <div class="template-demo-section">
            <h3>Navigation Elements</h3>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">Brand Logo</a>
                    <div class="navbar-nav">
                        <a class="nav-link active" href="#">Home</a>
                        <a class="nav-link" href="#">About</a>
                        <a class="nav-link" href="#">Services</a>
                        <a class="nav-link" href="#">Contact</a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Cards Demo -->
        <div class="template-demo-section">
            <h3>Card Components</h3>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Card Header</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">This is a sample card to demonstrate the template styling.</p>
                            <a href="#" class="btn btn-primary">Primary Button</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Another Card</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Cards adapt to the selected template styling automatically.</p>
                            <a href="#" class="btn btn-secondary">Secondary Button</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Third Card</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Notice how all elements follow the template's design philosophy.</p>
                            <a href="#" class="btn btn-success">Success Button</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forms Demo -->
        <div class="template-demo-section">
            <h3>Form Elements</h3>
            <form>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sampleInput" class="form-label">Sample Input</label>
                            <input type="text" class="form-control" id="sampleInput" placeholder="Enter text here">
                        </div>
                        <div class="mb-3">
                            <label for="sampleSelect" class="form-label">Sample Select</label>
                            <select class="form-select" id="sampleSelect">
                                <option>Choose an option</option>
                                <option>Option 1</option>
                                <option>Option 2</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sampleTextarea" class="form-label">Sample Textarea</label>
                            <textarea class="form-control" id="sampleTextarea" rows="3" placeholder="Enter your message"></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="sampleCheck">
                                <label class="form-check-label" for="sampleCheck">
                                    Sample Checkbox
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit Form</button>
            </form>
        </div>

        <!-- Tables Demo -->
        <div class="template-demo-section">
            <h3>Table Elements</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>John Doe</td>
                        <td>john@example.com</td>
                        <td><span class="badge bg-success">Active</span></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Jane Smith</td>
                        <td>jane@example.com</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Bob Johnson</td>
                        <td>bob@example.com</td>
                        <td><span class="badge bg-danger">Inactive</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Alerts Demo -->
        <div class="template-demo-section">
            <h3>Alert Messages</h3>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle me-2"></i>This is a success alert with template styling!
            </div>
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>This is a warning alert with template styling!
            </div>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-times-circle me-2"></i>This is a danger alert with template styling!
            </div>
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle me-2"></i>This is an info alert with template styling!
            </div>
        </div>

        <!-- Typography Demo -->
        <div class="template-demo-section">
            <h3>Typography Elements</h3>
            <h1>Heading 1 - Large Title</h1>
            <h2>Heading 2 - Section Title</h2>
            <h3>Heading 3 - Subsection Title</h3>
            <h4>Heading 4 - Minor Section</h4>
            <p>This is a regular paragraph demonstrating the template's typography. The text should be readable and follow the template's design philosophy.</p>
            <p class="text-muted">This is muted text that provides secondary information.</p>
            <blockquote class="blockquote">
                <p>"This is a blockquote demonstrating how quoted text appears in the template."</p>
                <footer class="blockquote-footer">Template Designer</footer>
            </blockquote>
        </div>

        <!-- Management Links -->
        <div class="template-demo-section">
            <h3>Management Actions</h3>
            <div class="d-flex gap-2 flex-wrap">
                <a href="public_html/admin/settings/branding-templates-enhanced.php" class="btn btn-primary">
                    <i class="fas fa-cog me-2"></i>Enhanced Template Manager
                </a>
                <a href="setup-enhanced-branding-system.php" class="btn btn-info">
                    <i class="fas fa-tools me-2"></i>Run Setup
                </a>
                <a href="?test_area=<?= $testArea ?>" class="btn btn-secondary">
                    <i class="fas fa-undo me-2"></i>Reset to Active Template
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-refresh template display
        function refreshTemplate(area, template) {
            const url = new URL(window.location);
            url.searchParams.set('test_area', area);
            if (template) {
                url.searchParams.set('test_template', template);
            }
            window.location.href = url.toString();
        }
        
        // Add visual feedback for current selections
        document.addEventListener('DOMContentLoaded', function() {
            const currentArea = '<?= $testArea ?>';
            const currentTemplate = '<?= $testTemplate ?>';
            
            // Highlight current selections in dropdowns
            document.querySelectorAll('.dropdown-item').forEach(item => {
                const href = item.getAttribute('href');
                if (href && href.includes(`test_area=${currentArea}`) && 
                    (!currentTemplate || href.includes(`test_template=${currentTemplate}`))) {
                    item.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
