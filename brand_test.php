<?php
/*
 * Brand System Test Page
 * 
 * This page demonstrates the dynamic brand color and font system in action.
 * All colors and fonts should be loaded from the database.
 */

// Include the universal config and doctype which loads the brand system
include_once 'assets/includes/doctype.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="text-center mb-5">
                <h1 class="display-4 mb-3">Brand System Test Page</h1>
                <p class="lead text-muted">This page demonstrates the dynamic brand color and font system</p>
            </div>
        </div>
    </div>

    <!-- Color Demonstrations -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">Brand Colors</h2>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center" style="background: var(--brand-primary); color: white;">
                            <h5 class="card-title">Primary</h5>
                            <p class="card-text">var(--brand-primary)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center" style="background: var(--brand-secondary); color: white;">
                            <h5 class="card-title">Secondary</h5>
                            <p class="card-text">var(--brand-secondary)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center" style="background: var(--brand-tertiary); color: white;">
                            <h5 class="card-title">Tertiary</h5>
                            <p class="card-text">var(--brand-tertiary)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center" style="background: var(--brand-quaternary); color: white;">
                            <h5 class="card-title">Quaternary</h5>
                            <p class="card-text">var(--brand-quaternary)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Functional Colors -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">Functional Colors</h2>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="alert alert-success">
                        <strong>Success!</strong> Using var(--brand-success)
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="alert alert-danger">
                        <strong>Danger!</strong> Using var(--brand-danger)
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> Using var(--brand-warning)
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="alert alert-info">
                        <strong>Info!</strong> Using var(--brand-info)
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">Buttons with Brand Colors</h2>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-primary">Primary Button</button>
                <button class="btn btn-secondary">Secondary Button</button>
                <button class="btn btn-success">Success Button</button>
                <button class="btn btn-danger">Danger Button</button>
                <button class="btn btn-warning">Warning Button</button>
                <button class="btn btn-info">Info Button</button>
            </div>
        </div>
    </div>

    <!-- Typography -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">Brand Typography</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Heading Font</h5>
                        </div>
                        <div class="card-body">
                            <h1 style="font-family: var(--brand-font-heading);">Heading 1</h1>
                            <h2 style="font-family: var(--brand-font-heading);">Heading 2</h2>
                            <h3 style="font-family: var(--brand-font-heading);">Heading 3</h3>
                            <p class="text-muted">Using: var(--brand-font-heading)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Body Font</h5>
                        </div>
                        <div class="card-body">
                            <p style="font-family: var(--brand-font-body);">
                                This is body text using the brand body font. Lorem ipsum dolor sit amet, 
                                consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore 
                                et dolore magna aliqua.
                            </p>
                            <p class="text-muted">Using: var(--brand-font-body)</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Primary Font</h5>
                        </div>
                        <div class="card-body">
                            <p style="font-family: var(--brand-font-primary); font-size: 1.2em; font-weight: 500;">
                                This is text using the primary brand font. Perfect for important text 
                                that needs emphasis without being a heading.
                            </p>
                            <p class="text-muted">Using: var(--brand-font-primary)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Monospace Font</h5>
                        </div>
                        <div class="card-body">
                            <pre style="font-family: var(--brand-font-monospace); background: var(--brand-background); border: 1px solid var(--brand-text-muted); padding: 10px; border-radius: 4px;"><code>function updateBrandColors() {
    return "Using brand monospace font";
}</code></pre>
                            <p class="text-muted">Using: var(--brand-font-monospace)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Text Colors -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">Text Colors</h2>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded">
                        <p style="color: var(--brand-text);" class="mb-1">Primary Text Color</p>
                        <small class="text-muted">var(--brand-text)</small>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded">
                        <p style="color: var(--brand-text-light);" class="mb-1">Light Text Color</p>
                        <small class="text-muted">var(--brand-text-light)</small>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="p-3 border rounded">
                        <p style="color: var(--brand-text-muted);" class="mb-1">Muted Text Color</p>
                        <small class="text-muted">var(--brand-text-muted)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Colors -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">Custom Colors</h2>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center" style="background: var(--brand-custom-1); color: var(--brand-text);">
                            <h5 class="card-title">Custom 1</h5>
                            <p class="card-text">var(--brand-custom-1)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center" style="background: var(--brand-custom-2); color: var(--brand-text);">
                            <h5 class="card-title">Custom 2</h5>
                            <p class="card-text">var(--brand-custom-2)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center" style="background: var(--brand-custom-3); color: var(--brand-text);">
                            <h5 class="card-title">Custom 3</h5>
                            <p class="card-text">var(--brand-custom-3)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-primary">
                <h4 class="alert-heading">
                    <i class="bi bi-lightbulb me-2"></i>
                    How it Works
                </h4>
                <p class="mb-0">
                    All colors and fonts on this page are loaded dynamically from the database through the 
                    <strong>setting_branding_colors</strong> table. To change them:
                </p>
                <hr>
                <ol class="mb-0">
                    <li>Visit the <a href="admin/settings/branding_colors_form.php" class="alert-link">Brand Management Interface</a></li>
                    <li>Update the colors and fonts using the form</li>
                    <li>Save the changes</li>
                    <li>Refresh this page to see the updates instantly!</li>
                </ol>
            </div>
        </div>
    </div>
</main>

<?php include_once 'assets/includes/footer.php'; ?>

<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
</body>
</html>
