<?php
/**
 * Brand Theme Testing and Documentation Page
 * 
 * This page demonstrates all the branded themes and provides testing
 * information for administrators.
 */

require_once '../private/gws-universal-config.php';
require_once '../private/gws-universal-functions.php';
require_once 'assets/includes/brand_loader.php';
require_once 'assets/includes/branding-functions.php';

// Get current active template
$active_template = getActiveBrandingTemplate();
$active_css_file = getActiveBrandingCSSFile();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Theme Testing - <?= $active_template['template_name'] ?? 'Default' ?> Theme</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Dynamic Brand CSS -->
    <link href="assets/css/brand-dynamic.css" rel="stylesheet">
    <!-- Active Theme CSS -->
    <link href="<?= htmlspecialchars($active_css_file) ?>" rel="stylesheet">
    
    <?php 
    // Output dynamic brand CSS variables
    outputBrandCSS(); 
    ?>
</head>
<body>
    <!-- Theme Test Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#"><strong>Brand Theme Test</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#hero">Hero</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#components">Components</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#forms">Forms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#cards">Cards</a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <a href="admin/theme-selection.php" class="nav-link">
                        <i class="fas fa-palette me-1"></i>Change Theme
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Current Theme Info -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="container">
            <strong><i class="fas fa-info-circle me-2"></i>Current Active Theme:</strong> 
            <?= htmlspecialchars($active_template['template_name'] ?? 'Default') ?>
            <?php if ($active_template): ?>
                - <?= htmlspecialchars($active_template['template_description']) ?>
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>

    <!-- Hero Section -->
    <section id="hero" class="hero">
        <div class="container">
            <div class="row align-items-center min-vh-50">
                <div class="col-lg-6">
                    <h1 class="display-4 mb-4">Professional Brand Theme Testing</h1>
                    <p class="lead mb-4">
                        This page demonstrates how your chosen brand theme looks across different components 
                        and sections. All themes are designed to be professional, attractive, and not 
                        obnoxious - perfect for impressing your clients.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <button class="btn btn-primary btn-lg">
                            <i class="fas fa-play me-2"></i>Primary Action
                        </button>
                        <button class="btn btn-secondary btn-lg">
                            <i class="fas fa-info me-2"></i>Secondary Action
                        </button>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="fas fa-palette text-primary" style="font-size: 8rem; opacity: 0.1;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Components Section -->
    <section id="components" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Component Examples</h2>
            
            <!-- Buttons -->
            <div class="row mb-5">
                <div class="col-12">
                    <h4 class="mb-3">Buttons</h4>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-primary">Primary</button>
                        <button class="btn btn-secondary">Secondary</button>
                        <button class="btn btn-success">Success</button>
                        <button class="btn btn-warning">Warning</button>
                        <button class="btn btn-danger">Danger</button>
                        <button class="btn btn-info">Info</button>
                        <button class="btn btn-outline-primary">Outline</button>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <div class="row mb-5">
                <div class="col-12">
                    <h4 class="mb-3">Alerts</h4>
                    <div class="alert alert-primary" role="alert">
                        <i class="fas fa-info-circle me-2"></i>This is a primary alert with brand colors.
                    </div>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i>This is a success alert - great for confirmations.
                    </div>
                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>This is a warning alert for important notices.
                    </div>
                </div>
            </div>

            <!-- Progress Bars -->
            <div class="row mb-5">
                <div class="col-12">
                    <h4 class="mb-3">Progress Indicators</h4>
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar bg-primary" style="width: 75%">75% Complete</div>
                    </div>
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar bg-secondary" style="width: 50%">50% Complete</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Forms Section -->
    <section id="forms" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Form Elements</h2>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Contact Form Example</h5>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" placeholder="Enter your first name">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" placeholder="Enter your last name">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" placeholder="your@email.com">
                                </div>
                                <div class="mb-3">
                                    <label for="service" class="form-label">Service Interest</label>
                                    <select class="form-select" id="service">
                                        <option selected>Choose a service...</option>
                                        <option value="1">Web Development</option>
                                        <option value="2">Brand Design</option>
                                        <option value="3">Digital Marketing</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea class="form-control" id="message" rows="4" placeholder="Tell us about your project..."></textarea>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="newsletter">
                                    <label class="form-check-label" for="newsletter">
                                        Subscribe to our newsletter for updates
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Send Message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cards Section -->
    <section id="cards" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Card Layouts</h2>
            
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-star text-warning me-2"></i>Featured Service</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">Professional Web Development</h6>
                            <p class="card-text">
                                Custom websites built with modern technologies and responsive design. 
                                Perfect for businesses that want to make a great impression online.
                            </p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Responsive Design</li>
                                <li><i class="fas fa-check text-success me-2"></i>SEO Optimized</li>
                                <li><i class="fas fa-check text-success me-2"></i>Fast Loading</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary w-100">Learn More</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-palette text-primary me-2"></i>Brand Design</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">Complete Brand Identity</h6>
                            <p class="card-text">
                                Professional branding that reflects your business values and appeals 
                                to your target audience. Clean, attractive, never obnoxious.
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Starting at</small>
                                <span class="h5 mb-0 text-primary">$599</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-outline-primary w-100">Get Quote</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-rocket me-2"></i>Popular Choice</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title">Complete Package</h6>
                            <p class="card-text">
                                Website + Branding + Marketing setup. Everything you need to 
                                launch your business with professional, attractive presentation.
                            </p>
                            <div class="text-center">
                                <span class="h4 text-success">$1,299</span>
                                <small class="text-muted d-block">Save $400</small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-success w-100">Choose Package</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5>Brand Theme Testing</h5>
                    <p class="text-muted">
                        This page demonstrates how your brand theme appears across different 
                        components and layouts. All themes maintain professional appearance.
                    </p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#hero" class="text-muted">Hero Section</a></li>
                        <li><a href="#components" class="text-muted">Components</a></li>
                        <li><a href="#forms" class="text-muted">Forms</a></li>
                        <li><a href="#cards" class="text-muted">Cards</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6>Theme Information</h6>
                    <p class="text-muted small">
                        <strong>Active Theme:</strong> <?= htmlspecialchars($active_template['template_name'] ?? 'Default') ?><br>
                        <strong>CSS File:</strong> <?= htmlspecialchars($active_css_file) ?><br>
                        <strong>Last Updated:</strong> <?= date('Y-m-d H:i') ?>
                    </p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0 text-muted">
                    &copy; <?= date('Y') ?> GWS Universal Hybrid App - Professional Brand Theme System
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Update navigation active states
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.navbar-nav a[href^="#"]');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 100;
                if (scrollY >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
