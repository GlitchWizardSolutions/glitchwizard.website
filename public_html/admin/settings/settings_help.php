<?php
/**
 * Settings Help Guide
 * Comprehensive documentation for the business configuration system
 */

// Include admin main file which handles authentication
include_once '../assets/includes/main.php';
?>

<?php echo template_admin_header('Settings Help Guide', 'settings', 'help'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
<?php echo template_admin_header('Settings Help Guide', 'settings', 'help'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            <!-- Header Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">
                        <span class="header-icon">
                            <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>
                            </svg>
                        </span>
                        Settings Help Guide &amp; Documentation
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Complete Documentation for Business Configuration</h5>
                            <p class="text-muted mb-3">This comprehensive guide will help you set up and manage your business website through our intuitive configuration system. Everything from contact information to branding and advanced features is covered.</p>
                            
                            <div class="d-flex gap-2 mb-3">
                                <a href="settings_dash.php" class="btn btn-primary">
                                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                        <path d="M495.9 166.6c3.2 8.7 .5 18.4-6.4 24.6l-43.3 39.4c1.1 8.3 1.7 16.8 1.7 25.4s-.6 17.1-1.7 25.4l43.3 39.4c6.9 6.2 9.6 15.9 6.4 24.6c-4.4 11.9-9.7 23.3-15.8 34.3l-4.7 8.1c-6.6 11-14 21.4-22.1 31.2c-5.9 7.2-15.7 9.6-24.5 6.8l-55.7-17.7c-13.4 10.3-28.2 18.9-44 25.4l-12.5 57.1c-2 9.1-9 16.3-18.2 17.8c-13.8 2.3-28 3.5-42.5 3.5s-28.7-1.2-42.5-3.5c-9.2-1.5-16.2-8.7-18.2-17.8l-12.5-57.1c-15.8-6.5-30.6-15.1-44-25.4L83.1 425.9c-8.8 2.8-18.6 .3-24.5-6.8c-8.1-9.8-15.5-20.2-22.1-31.2l-4.7-8.1c-6.1-11-11.4-22.4-15.8-34.3c-3.2-8.7-.5-18.4 6.4-24.6l43.3-39.4C64.6 273.1 64 264.6 64 256s.6-17.1 1.7-25.4L22.4 191.2c-6.9-6.2-9.6-15.9-6.4-24.6c4.4-11.9 9.7-23.3 15.8-34.3l4.7-8.1c6.6-11 14-21.4 22.1-31.2c5.9-7.2 15.7-9.6 24.5-6.8l55.7 17.7c13.4-10.3 28.2-18.9 44-25.4l12.5-57.1c2-9.1 9-16.3 18.2-17.8C227.3 1.2 241.5 0 256 0s28.7 1.2 42.5 3.5c9.2 1.5 16.2 8.7 18.2 17.8l12.5 57.1c15.8 6.5 30.6 15.1 44 25.4l55.7-17.7c8.8-2.8 18.6-.3 24.5 6.8c8.1 9.8 15.5 20.2 22.1 31.2l4.7 8.1c6.1 11 11.4 22.4 15.8 34.3zM256 336a80 80 0 1 0 0-160 80 80 0 1 0 0 160z"/>
                                    </svg>
                                    &nbsp;&nbsp;Settings Dashboard
                                </a>
                                <a href="new_installation.php" class="btn btn-success">
                                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                        <path d="M156.6 384.9L125.7 354c-8.5-8.5-11.5-20.8-7.7-32.2c3-8.9 7-20.5 11.8-33.8L24 288c-8.6 0-16.6-4.6-20.9-12.1s-4.2-16.7 .2-24.1l52.5-87.5c4.3-7.4 12.2-11.9 20.7-11.9s16.4 4.6 20.7 11.9L149.6 256c2.5-2.3 5-4.5 7.4-6.8c2.8-2.6 5.7-5.1 8.6-7.5c-4.9-24.2-1.9-49.7 9.4-71.5L122.6 117c-7.4-4.3-11.9-12.2-11.9-20.7s4.6-16.4 11.9-20.7L210.1 23.1c7.4-4.3 16.7-4.2 24.1 .2S246.3 35.6 246.3 44.2L246.3 136c15.1-8.9 32.2-13.4 49.6-13.4c5.4 0 10.7 .4 15.9 1.2L362.6 42.1c4.6-8.6 13.6-13.9 23.7-13.9c10.1 0 19.1 5.3 23.7 13.9l52.5 87.5c4.3 7.4 4.6 16.7 .2 24.1S450.4 167.9 441.8 167.9L349.1 167.9c9.5 20.9 13.3 45.3 8.5 69.8c24.2 4.9 49.7 1.9 71.5-9.4L482.3 281c4.3 7.4 5.3 16.7 .2 24.1S470.4 318.2 461.8 318.2L369.1 318.2c-8.9 15.1-13.4 32.2-13.4 49.6c0 5.4 .4 10.7 1.2 15.9L469.9 434.4c8.6 4.6 13.9 13.6 13.9 23.7c0 10.1-5.3 19.1-13.9 23.7l-87.5 52.5c-7.4 4.3-16.7 4.6-24.1 .2S344.1 522.2 344.1 513.6L344.1 420.9c-20.9-9.5-45.3-13.3-69.8-8.5c-4.9 24.2-1.9 49.7 9.4 71.5L336.4 537.1c7.4 4.3 11.9 12.2 11.9 20.7s-4.6 16.4-11.9 20.7L248.9 630.9c-7.4 4.3-16.7 4.2-24.1-.2S212.7 618.4 212.7 609.8L212.7 517.1c-15.1 8.9-32.2 13.4-49.6 13.4c-5.4 0-10.7-.4-15.9-1.2L95.4 611C90.8 619.6 81.8 624.9 71.7 624.9c-10.1 0-19.1-5.3-23.7-13.9L-.5 523.5c-4.3-7.4-4.6-16.7-.2-24.1S11.6 486.1 20.2 486.1L112.9 486.1c-9.5-20.9-13.3-45.3-8.5-69.8c-24.2-4.9-49.7-1.9-71.5 9.4L-20.3 372.4c-4.3-7.4-5.3-16.7-.2-24.1S-8.4 335.2 .2 335.2L92.9 335.2c8.9-15.1 13.4-32.2 13.4-49.6c0-5.4-.4-10.7-1.2-15.9L-7.9 217c-8.6-4.6-13.9-13.6-13.9-23.7c0-10.1 5.3-19.1 13.9-23.7l87.5-52.5c7.4-4.3 16.7-4.6 24.1-.2S116.9 129.2 116.9 137.8L116.9 230.5c20.9 9.5 45.3 13.3 69.8 8.5c4.9-24.2 1.9-49.7-9.4-71.5L230.6 114.3c-7.4-4.3-11.9-12.2-11.9-20.7s4.6-16.4 11.9-20.7L318.1 20.5c7.4-4.3 16.7-4.2 24.1 .2S354.3 33 354.3 41.6L354.3 134.3c15.1-8.9 32.2-13.4 49.6-13.4c5.4 0 10.7 .4 15.9 1.2L505.1 204.8c8.6 4.6 13.9 13.6 13.9 23.7c0 10.1-5.3 19.1-13.9 23.7l-87.5 52.5c-7.4 4.3-16.7 4.6-24.1 .2S380.1 292.6 380.1 284L380.1 191.3c-20.9-9.5-45.3-13.3-69.8-8.5z"/>
                                    </svg>
                                    &nbsp;&nbsp;Installation Wizard
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-4 rounded">
                                <h6 class="text-primary mb-3">
                                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                        <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>
                                    </svg>
                                    &nbsp;&nbsp;Quick Facts
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#28a745">
                                            <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                        </svg>
                                        &nbsp;&nbsp;5-minute setup
                                    </li>
                                    <li class="mb-2">
                                        <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#28a745">
                                            <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                        </svg>
                                        &nbsp;&nbsp;No coding required
                                    </li>
                                    <li class="mb-2">
                                        <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#28a745">
                                            <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                        </svg>
                                        &nbsp;&nbsp;Instant updates
                                    </li>
                                    <li class="mb-2">
                                        <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#28a745">
                                            <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                        </svg>
                                        &nbsp;&nbsp;Mobile-friendly
                                    </li>
                                    <li class="mb-2">
                                        <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#28a745">
                                            <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                        </svg>
                                        &nbsp;&nbsp;SEO optimized
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Navigation -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">
                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M464 256A208 208 0 1 0 48 256a208 208 0 1 0 416 0zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm306.7 69.1L162.4 380.6c-19.4 14.7-44.6 14.7-64 0s-19.4-38.1 0-52.8l115.2-87.4c-3.5-23.4-3.5-47.5 0-70.9L98.4 82.1c-19.4-14.7-19.4-38.1 0-52.8s44.6-14.7 64 0L306.7 74.9c23.4-3.5 47.5-3.5 70.9 0L522.3 29.3c19.4-14.7 44.6-14.7 64 0s19.4 38.1 0 52.8L471.1 169.5c3.5 23.4 3.5 47.5 0 70.9l115.2 87.4c19.4 14.7 19.4 38.1 0 52.8s-44.6 14.7-64 0L377.6 325.1c-23.4 3.5-47.5 3.5-70.9 0z"/>
                        </svg>
                        &nbsp;&nbsp;Quick Navigation
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="nav-section">
                                <h6 class="text-primary mb-3">
                                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                        <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>
                                    </svg>
                                    &nbsp;&nbsp;Getting Started
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><a href="#overview" class="text-decoration-none">System Overview</a></li>
                                    <li class="mb-2"><a href="#first-time" class="text-decoration-none">First Time Setup</a></li>
                                    <li class="mb-2"><a href="#quick-start" class="text-decoration-none">Quick Start Guide</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="nav-section">
                                <h6 class="text-success mb-3">
                                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                        <path d="M256 0c4.6 0 9.2 1 13.4 2.9L457.7 82.8c22 9.3 38.4 31 38.3 57.2c-.5 99.2-41.3 280.7-213.6 363.2c-16.7 8-36.1 8-52.8 0C57.3 420.7 16.5 239.2 16 140c-.1-26.2 16.3-47.9 38.3-57.2L242.7 2.9C246.8 1 251.4 0 256 0zm0 66.8V444.8C394 378 431.1 230.1 432 141.4L256 66.8l0 0z"/>
                                    </svg>
                                    &nbsp;&nbsp;Essential Settings
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><a href="#site-settings" class="text-decoration-none">Site Information</a></li>
                                    <li class="mb-2"><a href="#contact-settings" class="text-decoration-none">Contact Details</a></li>
                                    <li class="mb-2"><a href="#branding" class="text-decoration-none">Branding & Logo</a></li>
                                    <li class="mb-2"><a href="#email-config" class="text-decoration-none">Email Configuration</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="nav-section">
                                <h6 class="text-warning mb-3">
                                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                        <path d="M184 48H328c4.4 0 8 3.6 8 8V96H176V56c0-4.4 3.6-8 8-8zm-56 8V96H64C28.7 96 0 124.7 0 160v96H192 320 512V160c0-35.3-28.7-64-64-64H384V56c0-30.9-25.1-56-56-56H184c-30.9 0-56 25.1-56 56zM512 288H320v32c0 17.7-14.3 32-32 32H224c-17.7 0-32-14.3-32-32V288H0V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V288z"/>
                                    </svg>
                                    &nbsp;&nbsp;Business Features
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><a href="#user-management" class="text-decoration-none">User Management</a></li>
                                    <li class="mb-2"><a href="#seo-settings" class="text-decoration-none">SEO & Marketing</a></li>
                                    <li class="mb-2"><a href="#ecommerce" class="text-decoration-none">E-commerce Setup</a></li>
                                    <li class="mb-2"><a href="#blog-content" class="text-decoration-none">Blog & Content</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="nav-section">
                                <h6 class="text-info mb-3">
                                    <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                        <path d="M78.6 5C69.1-2.4 55.6-1.5 47 7L7 47c-8.5 8.5-9.4 22-2.1 31.6l80 104c4.5 5.9 11.6 9.4 19 9.4h54.1l109 109c-14.7 29-10 65.4 14.3 89.6l112 112c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3l-112-112c-24.2-24.2-60.6-29-89.6-14.3l-109-109V104c0-7.5-3.5-14.5-9.4-19L78.6 5zM19.9 396.1C7.2 408.8 0 426.1 0 444.1C0 481.6 30.4 512 67.9 512c18 0 35.3-7.2 48-19.9L233.7 374.3c-7.8-20.9-9-43.6-3.6-65.1l-61.7-61.7L19.9 396.1z"/>
                                    </svg>
                                    &nbsp;&nbsp;Advanced Topics
                                </h6>
                                <ul class="list-unstyled">
                                    <li class="mb-2"><a href="#backup-restore" class="text-decoration-none">Backup & Restore</a></li>
                                    <li class="mb-2"><a href="#troubleshooting" class="text-decoration-none">Troubleshooting</a></li>
                                    <li class="mb-2"><a href="#best-practices" class="text-decoration-none">Best Practices</a></li>
                                    <li class="mb-2"><a href="#developer-guide" class="text-decoration-none">Developer Guide</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Help Topics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">
                        <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                        </svg>
                        &nbsp;&nbsp;Search Help Topics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                    <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                                </svg>
                            </span>
                        </div>
                        <input type="text" class="form-control form-control-lg" id="helpSearch" 
                               placeholder="Search help topics... (e.g., 'email setup', 'logo upload', 'SEO configuration')">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" onclick="clearHelpSearch()">
                                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" fill="currentColor">
                                    <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/>
                                </svg>
                                &nbsp;&nbsp;Clear
                            </button>
                        </div>
                    </div>
                    <small class="text-muted">Use keywords to quickly find the information you need</small>
                </div>
            </div>

            <!-- Help Content -->
            <div class="help-content">

                <!-- System Overview -->
                <div class="help-section card shadow mb-4" id="overview" data-keywords="overview introduction system how it works concept">
                    <div class="card-header py-3">
                        <h5 class="m-0 font-weight-bold text-primary">
                            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>
                            </svg>
                            &nbsp;&nbsp;System Overview
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="section-content">
                                    <h5 class="mb-3">What is the Business Configuration System?</h5>
                                    <p class="lead">This system allows you to manage all your website content and settings from a central dashboard, without needing to edit code files directly. Everything from your business name to your contact information, colors, and content can be easily updated through user-friendly forms.</p>
                                    
                                    <h6 class="mt-4 mb-3">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#28a745">
                                            <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                        </svg>
                                        &nbsp;&nbsp;Key Benefits:
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#28a745">
                                                        <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                                    </svg>
                                                    &nbsp;&nbsp;<strong>No Code Required</strong> - Update your website through simple forms
                                                </li>
                                                <li class="mb-2">
                                                    <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#28a745">
                                                        <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                                    </svg>
                                                    &nbsp;&nbsp;<strong>Consistent Branding</strong> - Changes automatically apply site-wide
                                                </li>
                                                <li class="mb-2">
                                                    <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#28a745">
                                                        <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                                    </svg>
                                                    &nbsp;&nbsp;<strong>Quick Setup</strong> - Get online in minutes, not hours
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#28a745">
                                                        <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                                    </svg>
                                                    &nbsp;&nbsp;<strong>Professional Results</strong> - Ensures consistent, professional appearance
                                                </li>
                                                <li class="mb-2">
                                                    <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#28a745">
                                                        <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                                                    </svg>
                                                    &nbsp;&nbsp;<strong>Client-Ready</strong> - Easy for clients to manage their own content
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <h6 class="mt-4 mb-3">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="#007bff">
                                            <path d="M40 48C26.7 48 16 58.7 16 72v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V72c0-13.3-10.7-24-24-24H40zM192 64c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zM16 232v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V232c0-13.3-10.7-24-24-24H40c-13.3 0-24 10.7-24 24zM40 368c-13.3 0-24 10.7-24 24v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V392c0-13.3-10.7-24-24-24H40z"/>
                                        </svg>
                                        &nbsp;&nbsp;How It Works:
                                    </h6>
                                    <ol class="process-list">
                                        <li class="mb-2"><strong>Settings Files</strong> - Store your business information</li>
                                        <li class="mb-2"><strong>Template System</strong> - Automatically displays your content</li>
                                        <li class="mb-2"><strong>Dashboard</strong> - Easy interface to manage everything</li>
                                        <li class="mb-2"><strong>Live Updates</strong> - Changes appear immediately on your site</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-4 rounded h-100">
                                    <h6 class="text-primary mb-3">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                                            <path d="M159.3 5.4c7.8-7.3 19.9-7.2 27.7 .1c27.6 25.9 53.5 53.8 77.7 83.7c11.2 13.8 21.7 28.3 31.1 43.5c21.7 35.3 36.4 75.2 43.8 116.6c7.4 41.4 6.6 83.8-2.4 124.8c-4.5 20.5-10.9 40.5-19.2 59.6c-8.3 19.1-18.6 37.3-30.7 54.3c-12.1 17-25.9 32.9-41.4 47.4c-15.5 14.5-32.6 27.6-51.1 39.1c-9.2 5.7-18.8 11-28.8 15.8c-10 4.8-20.4 9.2-31.2 13.1c-21.6 7.8-44.6 13.5-68.2 17c-23.6 3.5-47.9 5-72.2 4.4c-12.1-.3-24.3-.9-36.4-1.8c-6-.4-12.1-.9-18.1-1.5c-3-.3-6-.6-9-1c-1.5-.2-3-.4-4.5-.6c-.7-.1-1.5-.2-2.2-.3c-.4-.1-.7-.1-1.1-.2c-.2 0-.4-.1-.6-.1c-.1 0-.3 0-.4-.1c-.1 0-.2 0-.3 0c-.1 0-.1 0-.2 0c-.1 0-.1 0-.1 0c-.1 0-.1 0-.1 0c0 0 0 0 0 0c-7.8-1.1-13.9-7.2-15-15c-1.1-7.8 2.8-15.4 9.8-18.8c3.5-1.7 7.2-3.1 10.9-4.2c18.6-5.5 37.7-8.5 57.1-8.9c19.4-.4 38.9 1.4 57.9 5.4c9.5 2 18.8 4.6 27.9 7.8c4.5 1.6 9 3.4 13.4 5.3c2.2 1 4.4 2 6.5 3c1.1 .5 2.1 1 3.2 1.5c.5 .3 1 .5 1.5 .8c.3 .1 .5 .3 .8 .4c.1 .1 .3 .1 .4 .2c.1 0 .2 .1 .3 .1c.1 0 .2 .1 .2 .1c.1 0 .1 0 .2 .1c0 0 .1 0 .1 0c0 0 .1 0 .1 0c0 0 0 0 .1 0c0 0 0 0 0 0c7.8 3.4 11.7 11 9.8 18.8s-7.2 13.9-15 15c-15.6 2.2-31.5 2.9-47.4 2.1c-31.8-1.6-63.2-7.3-93.1-16.7c-14.9-4.7-29.5-10.4-43.6-17.1c-7.1-3.4-14-7.1-20.7-11.1c-3.4-2-6.7-4.1-9.9-6.3c-1.6-1.1-3.2-2.2-4.7-3.4c-.8-.6-1.5-1.2-2.3-1.8c-.4-.3-.7-.6-1.1-.9c-.2-.2-.4-.3-.6-.5c-.1-.1-.2-.2-.3-.3c-.1-.1-.1-.1-.2-.2c0-.1-.1-.1-.1-.1c0 0 0-.1-.1-.1c0 0 0 0 0-.1c0 0 0 0 0 0c-7.8-6.5-8.9-18.1-2.4-25.9s18.1-8.9 25.9-2.4c.1 .1 .2 .1 .4 .3c.3 .2 .6 .5 1 .8c.7 .6 1.6 1.3 2.6 2.1c2 1.6 4.3 3.4 6.8 5.4c5 4 10.5 8.2 16.4 12.6c11.8 8.8 25.1 17.7 39.4 26.6c28.6 17.8 60.9 34.7 95.8 50.2c17.5 7.8 35.5 15.2 54 22.1c9.2 3.4 18.6 6.7 28.1 9.8c4.8 1.5 9.6 3 14.4 4.4c2.4 .7 4.8 1.4 7.3 2.1c1.2 .3 2.4 .7 3.6 1c.6 .2 1.2 .3 1.8 .5c.3 .1 .6 .2 .9 .3c.2 .1 .3 .1 .5 .2c.1 0 .2 .1 .3 .1c.1 0 .1 0 .2 .1c0 0 .1 0 .1 0c0 0 .1 0 .1 0c0 0 0 0 .1 0c0 0 0 0 0 0c7.8 2.2 12.7 9.8 10.5 17.6s-9.8 12.7-17.6 10.5c-39.1-11-76.9-25.3-112.7-42.8c-17.9-8.8-35.3-18.4-52.1-28.8c-8.4-5.2-16.6-10.6-24.6-16.3c-4-2.9-7.9-5.8-11.7-8.8c-1.9-1.5-3.8-3-5.6-4.6c-.9-.8-1.8-1.6-2.7-2.4c-.4-.4-.9-.8-1.3-1.2c-.2-.2-.4-.4-.6-.6c-.1-.1-.2-.2-.3-.3c-.1-.1-.1-.1-.2-.2c0-.1-.1-.1-.1-.1c0 0 0-.1-.1-.1c0 0 0 0 0-.1c0 0 0 0 0 0c-7.8-6.5-8.9-18.1-2.4-25.9s18.1-8.9 25.9-2.4z"/>
                                        </svg>
                                        &nbsp;&nbsp;System Statistics
                                    </h6>
                                    <div class="stats-grid">
                                        <div class="stat-item mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Setup Time:</span>
                                                <span class="font-weight-bold text-success">5 minutes</span>
                                            </div>
                                        </div>
                                        <div class="stat-item mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Settings Available:</span>
                                                <span class="font-weight-bold text-primary">50+</span>
                                            </div>
                                        </div>
                                        <div class="stat-item mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">File Management:</span>
                                                <span class="font-weight-bold text-info">Automated</span>
                                            </div>
                                        </div>
                                        <div class="stat-item mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Updates:</span>
                                                <span class="font-weight-bold text-warning">Real-time</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <a href="new_installation.php" class="btn btn-success btn-block">
                                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                                <path d="M156.6 384.9L125.7 354c-8.5-8.5-11.5-20.8-7.7-32.2c3-8.9 7-20.5 11.8-33.8L24 288c-8.6 0-16.6-4.6-20.9-12.1s-4.2-16.7 .2-24.1l52.5-87.5c4.3-7.4 12.2-11.9 20.7-11.9s16.4 4.6 20.7 11.9L149.6 256c2.5-2.3 5-4.5 7.4-6.8c2.8-2.6 5.7-5.1 8.6-7.5c-4.9-24.2-1.9-49.7 9.4-71.5L122.6 117c-7.4-4.3-11.9-12.2-11.9-20.7s4.6-16.4 11.9-20.7L210.1 23.1c7.4-4.3 16.7-4.2 24.1 .2S246.3 35.6 246.3 44.2L246.3 136c15.1-8.9 32.2-13.4 49.6-13.4c5.4 0 10.7 .4 15.9 1.2L362.6 42.1c4.6-8.6 13.6-13.9 23.7-13.9c10.1 0 19.1 5.3 23.7 13.9l52.5 87.5c4.3 7.4 4.6 16.7 .2 24.1S450.4 167.9 441.8 167.9L349.1 167.9c9.5 20.9 13.3 45.3 8.5 69.8c24.2 4.9 49.7 1.9 71.5-9.4L482.3 281c4.3 7.4 5.3 16.7 .2 24.1S470.4 318.2 461.8 318.2L369.1 318.2c-8.9 15.1-13.4 32.2-13.4 49.6c0 5.4 .4 10.7 1.2 15.9L469.9 434.4c8.6 4.6 13.9 13.6 13.9 23.7c0 10.1-5.3 19.1-13.9 23.7l-87.5 52.5c-7.4 4.3-16.7 4.6-24.1 .2S344.1 522.2 344.1 513.6L344.1 420.9c-20.9-9.5-45.3-13.3-69.8-8.5c-4.9 24.2-1.9 49.7 9.4 71.5L336.4 537.1c7.4 4.3 11.9 12.2 11.9 20.7s-4.6 16.4-11.9 20.7L248.9 630.9c-7.4 4.3-16.7 4.2-24.1-.2S212.7 618.4 212.7 609.8L212.7 517.1c-15.1 8.9-32.2 13.4-49.6 13.4c-5.4 0-10.7-.4-15.9-1.2L95.4 611C90.8 619.6 81.8 624.9 71.7 624.9c-10.1 0-19.1-5.3-23.7-13.9L-.5 523.5c-4.3-7.4-4.6-16.7-.2-24.1S11.6 486.1 20.2 486.1L112.9 486.1c-9.5-20.9-13.3-45.3-8.5-69.8c-24.2-4.9-49.7-1.9-71.5 9.4L-20.3 372.4c-4.3-7.4-5.3-16.7-.2-24.1S-8.4 335.2 .2 335.2L92.9 335.2c8.9-15.1 13.4-32.2 13.4-49.6c0-5.4-.4-10.7-1.2-15.9L-7.9 217c-8.6-4.6-13.9-13.6-13.9-23.7c0-10.1 5.3-19.1 13.9-23.7l87.5-52.5c7.4-4.3 16.7-4.6 24.1-.2S116.9 129.2 116.9 137.8L116.9 230.5c20.9 9.5 45.3 13.3 69.8 8.5c4.9-24.2 1.9-49.7-9.4-71.5L230.6 114.3c-7.4-4.3-11.9-12.2-11.9-20.7s4.6-16.4 11.9-20.7L318.1 20.5c7.4-4.3 16.7-4.2 24.1 .2S354.3 33 354.3 41.6L354.3 134.3c15.1-8.9 32.2-13.4 49.6-13.4c5.4 0 10.7 .4 15.9 1.2L505.1 204.8c8.6 4.6 13.9 13.6 13.9 23.7c0 10.1-5.3 19.1-13.9 23.7l-87.5 52.5c-7.4 4.3-16.7 4.6-24.1 .2S380.1 292.6 380.1 284L380.1 191.3c-20.9-9.5-45.3-13.3-69.8-8.5z"/>
                                            </svg>
                                            &nbsp;&nbsp;Start Setup Wizard
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- First Time Setup -->
                <div class="help-section card shadow mb-4" id="first-time" data-keywords="first time setup installation new getting started begin">
                    <div class="card-header">
                        <h4 class="mb-0 text-success">
                            <i class="bi bi-play-circle" aria-hidden="true"></i>&nbsp;&nbsp;First Time Setup
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="bi bi-lightbulb" aria-hidden="true"></i> New to the system?</h6>
                            <p class="mb-0">If this is your first time using the system, we recommend using the <a href="new_installation.php">New Installation Wizard</a> which will guide you through each step.</p>
                        </div>

                        <h5>Method 1: Installation Wizard (Recommended)</h5>
                        <p>The easiest way to get started:</p>
                        <ol>
                            <li>Click the <strong>"New Installation Wizard"</strong> button</li>
                            <li>Choose <strong>"Quick Setup"</strong> to create basic settings instantly</li>
                            <li>Or follow the step-by-step guide to configure each section</li>
                            <li>Preview your website when complete</li>
                        </ol>

                        <h5 class="mt-4">Method 2: Manual Configuration</h5>
                        <p>For those who prefer to configure settings individually:</p>
                        <ol>
                            <li>Go to the <a href="settings_dash.php">Settings Dashboard</a></li>
                            <li>Start with the <strong>"Essential Setup"</strong> tab</li>
                            <li>Configure each setting by clicking the buttons</li>
                            <li>Move to <strong>"Business Settings"</strong> when ready</li>
                            <li>Add optional features as needed</li>
                        </ol>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6 class="text-success">✅ Essential Settings (Required)</h6>
                                    <ul class="mb-0">
                                        <li>Site Information</li>
                                        <li>Contact Details</li>
                                        <li>Brand & Logo</li>
                                        <li>Email Configuration</li>
                                        <li>SEO & Meta Tags</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <h6 class="text-info">⭐ Recommended Next Steps</h6>
                                    <ul class="mb-0">
                                        <li>User Management</li>
                                        <li>Blog Settings (if needed)</li>
                                        <li>Shop Settings (if selling)</li>
                                        <li>Gallery Settings (if showcasing)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Start Guide -->
                <div class="help-section card shadow mb-4" id="quick-start" data-keywords="quick start guide 15 minutes fast setup">
                    <div class="card-header">
                        <h4 class="mb-0 text-warning">
                            <i class="bi bi-rocket" aria-hidden="true"></i>&nbsp;&nbsp;15-Minute Quick Start Guide
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-clock-history" aria-hidden="true"></i> Get Online in 15 Minutes</h6>
                            <p class="mb-0">Follow this guide to have a professional business website running in just 15 minutes.</p>
                        </div>

                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker">1</div>
                                <div class="timeline-content">
                                    <h6>Site Information (3 minutes)</h6>
                                    <p>Set your business name, tagline, and description. This appears in your page titles and throughout your site.</p>
                                    <a href="site_settings.php" class="btn btn-sm btn-primary">Configure Now</a>
                                </div>
                            </div>

                            <div class="timeline-item">
                                <div class="timeline-marker">2</div>
                                <div class="timeline-content">
                                    <h6>Contact Information (3 minutes)</h6>
                                    <p>Add your business address, phone number, email, and hours. This creates your contact page and footer information.</p>
                                    <a href="contact_settings.php" class="btn btn-sm btn-primary">Configure Now</a>
                                </div>
                            </div>

                            <div class="timeline-item">
                                <div class="timeline-marker">3</div>
                                <div class="timeline-content">
                                    <h6>Upload Logo & Colors (4 minutes)</h6>
                                    <p>Upload your logo and set your brand colors. This gives your site a professional, branded appearance.</p>
                                    <a href="branding_settings.php" class="btn btn-sm btn-primary">Configure Now</a>
                                </div>
                            </div>

                            <div class="timeline-item">
                                <div class="timeline-marker">4</div>
                                <div class="timeline-content">
                                    <h6>Email Setup (3 minutes)</h6>
                                    <p>Configure email settings so your contact forms work and you can send notifications.</p>
                                    <a href="email_settings.php" class="btn btn-sm btn-primary">Configure Now</a>
                                </div>
                            </div>

                            <div class="timeline-item">
                                <div class="timeline-marker">5</div>
                                <div class="timeline-content">
                                    <h6>Preview & Launch (2 minutes)</h6>
                                    <p>Check your website and make any final adjustments. Your professional site is ready!</p>
                                    <a href="../../index.php" target="_blank" class="btn btn-sm btn-success">Preview Website</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Site Settings -->
                <div class="help-section card shadow mb-4" id="site-settings" data-keywords="site settings business name tagline description title">
                    <div class="card-header">
                        <h4 class="mb-0 text-primary">
                            <i class="bi bi-globe" aria-hidden="true"></i>&nbsp;&nbsp;Site Information Settings
                        </h4>
                    </div>
                    <div class="card-body">
                        <h5>What This Controls</h5>
                        <p>Site settings control the basic information about your business that appears throughout your website.</p>

                        <div class="row">
                            <div class="col-md-6">
                                <h6>Key Fields:</h6>
                                <ul>
                                    <li><strong>Site Name:</strong> Your business name (appears in browser tab, header)</li>
                                    <li><strong>Tagline:</strong> Brief description of what you do</li>
                                    <li><strong>Site Description:</strong> Longer description for about sections</li>
                                    <li><strong>Copyright Text:</strong> Footer copyright notice</li>
                                    <li><strong>Established Year:</strong> When your business was founded</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Where This Appears:</h6>
                                <ul>
                                    <li>Page titles and browser tabs</li>
                                    <li>Website header and navigation</li>
                                    <li>About us sections</li>
                                    <li>Footer copyright area</li>
                                    <li>Social media sharing</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <h6><i class="bi bi-lightbulb" aria-hidden="true"></i> Pro Tips:</h6>
                            <ul class="mb-0">
                                <li>Keep your tagline under 10 words for best impact</li>
                                <li>Use your exact business name for consistency</li>
                                <li>Include keywords in your description for SEO</li>
                                <li>Make sure copyright year is current</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Contact Settings -->
                <div class="help-section card shadow mb-4" id="contact-settings" data-keywords="contact information address phone email hours location">
                    <div class="card-header">
                        <h4 class="mb-0 text-primary">
                            <i class="bi bi-person-badge" aria-hidden="true"></i>&nbsp;&nbsp;Contact Information Settings
                        </h4>
                    </div>
                    <div class="card-body">
                        <h5>What This Controls</h5>
                        <p>Contact settings manage all your business location and communication information.</p>

                        <div class="row">
                            <div class="col-md-6">
                                <h6>Required Information:</h6>
                                <ul>
                                    <li><strong>Business Name:</strong> Official business name</li>
                                    <li><strong>Address:</strong> Physical business location</li>
                                    <li><strong>Phone Number:</strong> Main business phone</li>
                                    <li><strong>Email Address:</strong> Primary contact email</li>
                                    <li><strong>Business Hours:</strong> When you're open</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Optional Information:</h6>
                                <ul>
                                    <li>Secondary phone numbers</li>
                                    <li>Fax number</li>
                                    <li>Social media links</li>
                                    <li>Emergency contact info</li>
                                    <li>Special instructions</li>
                                </ul>
                            </div>
                        </div>

                        <h6 class="mt-4">Where This Information Appears:</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <ul>
                                    <li>Contact page</li>
                                    <li>Website footer</li>
                                    <li>Google Maps integration</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul>
                                    <li>Email signatures</li>
                                    <li>Invoice templates</li>
                                    <li>Business cards (if printed)</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <ul>
                                    <li>Local SEO listings</li>
                                    <li>Social media profiles</li>
                                    <li>Contact forms</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3">
                            <h6><i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i> Important Notes:</h6>
                            <ul class="mb-0">
                                <li>Double-check phone numbers and email addresses</li>
                                <li>Use consistent formatting for professional appearance</li>
                                <li>Keep business hours current and accurate</li>
                                <li>Consider time zones if you serve multiple regions</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Branding Settings -->
                <div class="help-section card shadow mb-4" id="branding" data-keywords="branding logo colors fonts brand identity design">
                    <div class="card-header">
                        <h4 class="mb-0 text-primary">
                            <i class="bi bi-palette" aria-hidden="true"></i>&nbsp;&nbsp;Branding & Design Settings
                        </h4>
                    </div>
                    <div class="card-body">
                        <h5>What This Controls</h5>
                        <p>Branding settings control the visual appearance of your website including logos, colors, and fonts.</p>

                        <div class="row">
                            <div class="col-md-6">
                                <h6>Logo Settings:</h6>
                                <ul>
                                    <li><strong>Main Logo:</strong> Appears in header (PNG recommended)</li>
                                    <li><strong>Favicon:</strong> Small icon in browser tab (ICO format)</li>
                                    <li><strong>Logo Size:</strong> Optimal dimensions for display</li>
                                    <li><strong>Logo Alt Text:</strong> Accessibility description</li>
                                </ul>

                                <h6 class="mt-3">File Requirements:</h6>
                                <ul>
                                    <li>Logo: PNG format, transparent background</li>
                                    <li>Size: 300x100 pixels recommended</li>
                                    <li>Favicon: 32x32 pixels, ICO format</li>
                                    <li>File size: Under 500KB for fast loading</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Color Settings:</h6>
                                <ul>
                                    <li><strong>Primary Color:</strong> Main brand color (buttons, links)</li>
                                    <li><strong>Secondary Color:</strong> Supporting color</li>
                                    <li><strong>Accent Color:</strong> Highlight color for special elements</li>
                                    <li><strong>Text Colors:</strong> Header and body text colors</li>
                                </ul>

                                <h6 class="mt-3">Font Settings:</h6>
                                <ul>
                                    <li>Header font (for titles and headings)</li>
                                    <li>Body font (for content text)</li>
                                    <li>Font sizes and weights</li>
                                    <li>Line spacing and formatting</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-success mt-3">
                            <h6><i class="bi bi-lightbulb" aria-hidden="true"></i> Design Best Practices:</h6>
                            <ul class="mb-0">
                                <li>Use high-contrast colors for readability</li>
                                <li>Keep your color palette to 3-4 colors maximum</li>
                                <li>Ensure your logo is legible at small sizes</li>
                                <li>Test colors on different devices and screens</li>
                                <li>Choose fonts that reflect your brand personality</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Email Configuration -->
                <div class="help-section card shadow mb-4" id="email-config" data-keywords="email configuration SMTP settings mail server contact forms">
                    <div class="card-header">
                        <h4 class="mb-0 text-primary">
                            <i class="bi bi-envelope" aria-hidden="true"></i>&nbsp;&nbsp;Email Configuration
                        </h4>
                    </div>
                    <div class="card-body">
                        <h5>What This Controls</h5>
                        <p>Email settings configure how your website sends emails, including contact form submissions and notifications.</p>

                        <div class="row">
                            <div class="col-md-6">
                                <h6>SMTP Settings:</h6>
                                <ul>
                                    <li><strong>SMTP Host:</strong> Your email server address</li>
                                    <li><strong>SMTP Port:</strong> Usually 587 or 465</li>
                                    <li><strong>Username:</strong> Email account username</li>
                                    <li><strong>Password:</strong> Email account password</li>
                                    <li><strong>Encryption:</strong> TLS or SSL security</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Email Templates:</h6>
                                <ul>
                                    <li><strong>From Name:</strong> Who emails appear to be from</li>
                                    <li><strong>From Email:</strong> Reply-to email address</li>
                                    <li><strong>Subject Lines:</strong> Default email subjects</li>
                                    <li><strong>Email Signatures:</strong> Professional closing</li>
                                </ul>
                            </div>
                        </div>

                        <h6 class="mt-4">Common Email Providers:</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3">
                                    <h6>Gmail/Google Workspace</h6>
                                    <ul class="small mb-0">
                                        <li>Host: smtp.gmail.com</li>
                                        <li>Port: 587</li>
                                        <li>Encryption: TLS</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3">
                                    <h6>Outlook/Office 365</h6>
                                    <ul class="small mb-0">
                                        <li>Host: smtp.office365.com</li>
                                        <li>Port: 587</li>
                                        <li>Encryption: TLS</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 mb-3">
                                    <h6>cPanel/Hosting</h6>
                                    <ul class="small mb-0">
                                        <li>Host: mail.yourdomain.com</li>
                                        <li>Port: 587 or 465</li>
                                        <li>Contact your host for details</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <h6><i class="bi bi-info-circle" aria-hidden="true"></i> Troubleshooting Email Issues:</h6>
                            <ul class="mb-0">
                                <li>Test your settings with the "Send Test Email" button</li>
                                <li>Check spam folders if emails aren't arriving</li>
                                <li>Verify your email credentials are correct</li>
                                <li>Some hosts require app-specific passwords</li>
                                <li>Contact your email provider if you need SMTP details</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Troubleshooting -->
                <div class="help-section card shadow mb-4" id="troubleshooting" data-keywords="troubleshooting problems issues errors help fix bugs">
                    <div class="card-header">
                        <h4 class="mb-0 text-danger">
                            <i class="bi bi-wrench-adjustable-circle" aria-hidden="true"></i>&nbsp;&nbsp;Troubleshooting Guide
                        </h4>
                    </div>
                    <div class="card-body">
                        <h5>Common Issues and Solutions</h5>

                        <div class="accordion" id="troubleshootingAccordion">
                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link text-left" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                            Settings Not Saving / "Permission Denied" Errors
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapseOne" class="collapse" data-bs-parent="#troubleshootingAccordion">
                                    <div class="card-body">
                                        <p><strong>Symptoms:</strong> Changes don't save, error messages about permissions</p>
                                        <p><strong>Causes:</strong> File permission issues or server configuration</p>
                                        <p><strong>Solutions:</strong></p>
                                        <ul>
                                            <li>Check that the settings folder has write permissions (755 or 775)</li>
                                            <li>Ensure your web server can write to the settings directory</li>
                                            <li>Contact your hosting provider about PHP file permissions</li>
                                            <li>Try using the FTP client to set folder permissions manually</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="headingTwo">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link text-left" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                            Changes Don't Appear on Website
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapseTwo" class="collapse" data-bs-parent="#troubleshootingAccordion">
                                    <div class="card-body">
                                        <p><strong>Symptoms:</strong> Settings save successfully but changes don't show on site</p>
                                        <p><strong>Solutions:</strong></p>
                                        <ul>
                                            <li>Clear your browser cache (Ctrl+F5 or Cmd+Shift+R)</li>
                                            <li>Check if caching is enabled on your server</li>
                                            <li>Try viewing the site in an incognito/private browser window</li>
                                            <li>Wait a few minutes for server-side caching to expire</li>
                                            <li>Contact your hosting provider about clearing server cache</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="headingThree">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link text-left" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                            Email Forms Not Working
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapseThree" class="collapse" data-bs-parent="#troubleshootingAccordion">
                                    <div class="card-body">
                                        <p><strong>Symptoms:</strong> Contact forms submit but emails don't arrive</p>
                                        <p><strong>Solutions:</strong></p>
                                        <ul>
                                            <li>Check your email settings are correct</li>
                                            <li>Use the "Send Test Email" function</li>
                                            <li>Check spam/junk folders</li>
                                            <li>Verify SMTP credentials with your email provider</li>
                                            <li>Some hosts block external SMTP - contact support</li>
                                            <li>Try using your hosting provider's SMTP settings instead</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="headingFour">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link text-left" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                            Images/Logo Not Displaying
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapseFour" class="collapse" data-bs-parent="#troubleshootingAccordion">
                                    <div class="card-body">
                                        <p><strong>Symptoms:</strong> Uploaded images don't show on the website</p>
                                        <p><strong>Solutions:</strong></p>
                                        <ul>
                                            <li>Check that image files are in the correct format (JPG, PNG, GIF)</li>
                                            <li>Ensure file names don't contain spaces or special characters</li>
                                            <li>Verify images are uploaded to the correct directory</li>
                                            <li>Check file size - very large images may not display properly</li>
                                            <li>Try uploading images again</li>
                                            <li>Use the image preview feature to test uploads</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header" id="headingFive">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link text-left" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive">
                                            Dashboard Access Issues
                                        </button>
                                    </h6>
                                </div>
                                <div id="collapseFive" class="collapse" data-bs-parent="#troubleshootingAccordion">
                                    <div class="card-body">
                                        <p><strong>Symptoms:</strong> Can't access admin dashboard or settings</p>
                                        <p><strong>Solutions:</strong></p>
                                        <ul>
                                            <li>Clear browser cookies and cache</li>
                                            <li>Check your admin username and password</li>
                                            <li>Try logging out and logging back in</li>
                                            <li>Check if your session has expired</li>
                                            <li>Contact your system administrator</li>
                                            <li>Reset your password if necessary</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <h6><i class="bi bi-life-preserver" aria-hidden="true"></i> Still Need Help?</h6>
                            <p>If you're still experiencing issues:</p>
                            <ul class="mb-0">
                                <li>Check the browser console for error messages (F12)</li>
                                <li>Take a screenshot of any error messages</li>
                                <li>Note what you were doing when the problem occurred</li>
                                <li>Contact support with specific details about the issue</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Best Practices -->
                <div class="help-section card shadow mb-4" id="best-practices" data-keywords="best practices tips recommendations optimization security">
                    <div class="card-header">
                        <h4 class="mb-0 text-success">
                            <i class="bi bi-stars" aria-hidden="true"></i>&nbsp;&nbsp;Best Practices & Tips
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Security Best Practices</h5>
                                <ul>
                                    <li><strong>Regular Backups:</strong> Export your settings monthly</li>
                                    <li><strong>Strong Passwords:</strong> Use complex admin passwords</li>
                                    <li><strong>Access Control:</strong> Limit who has admin access</li>
                                    <li><strong>Updates:</strong> Keep the system updated</li>
                                    <li><strong>HTTPS:</strong> Use SSL certificates for security</li>
                                </ul>

                                <h5 class="mt-4">Performance Tips</h5>
                                <ul>
                                    <li><strong>Image Optimization:</strong> Compress images before uploading</li>
                                    <li><strong>Cache Management:</strong> Clear cache after major changes</li>
                                    <li><strong>File Organization:</strong> Keep uploaded files organized</li>
                                    <li><strong>Regular Cleanup:</strong> Remove unused images and files</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Content Management</h5>
                                <ul>
                                    <li><strong>Consistent Updates:</strong> Keep content fresh and current</li>
                                    <li><strong>Quality Images:</strong> Use professional, high-quality photos</li>
                                    <li><strong>Clear Writing:</strong> Write for your target audience</li>
                                    <li><strong>SEO Focus:</strong> Include relevant keywords naturally</li>
                                    <li><strong>Mobile Testing:</strong> Check how content looks on phones</li>
                                </ul>

                                <h5 class="mt-4">Workflow Efficiency</h5>
                                <ul>
                                    <li><strong>Batch Updates:</strong> Make several changes at once</li>
                                    <li><strong>Preview Changes:</strong> Always preview before going live</li>
                                    <li><strong>Document Changes:</strong> Keep notes of what you've modified</li>
                                    <li><strong>Team Communication:</strong> Coordinate with team members</li>
                                </ul>
                            </div>
                        </div>

                        <div class="alert alert-success mt-4">
                            <h6><i class="bi bi-trophy" aria-hidden="true"></i> Pro Tips for Success</h6>
                            <ul class="mb-0">
                                <li><strong>Start Simple:</strong> Configure essential settings first, add complexity later</li>
                                <li><strong>Test Everything:</strong> Check contact forms, links, and functionality regularly</li>
                                <li><strong>Monitor Analytics:</strong> Track how visitors use your website</li>
                                <li><strong>Get Feedback:</strong> Ask customers for input on your website</li>
                                <li><strong>Stay Updated:</strong> Follow web design and SEO best practices</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<style>
/* Help guide specific styles */
.help-section {
    transition: all 0.3s ease;
}

.help-section.highlight {
    box-shadow: 0 0 20px rgba(0, 123, 255, 0.3);
    border-left: 4px solid #007bff;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 25px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #007bff;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -37px;
    top: 5px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2em;
    z-index: 2;
}

.timeline-content {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-left: 1rem;
}

.timeline-content h6 {
    color: #007bff;
    margin-bottom: 0.5rem;
}

/* Search highlighting */
.search-highlight {
    background-color: #fff3cd;
    padding: 0.2em 0.4em;
    border-radius: 0.25rem;
    font-weight: bold;
}

/* Responsive timeline */
@media (max-width: 768px) {
    .timeline {
        padding-left: 0;
    }
    
    .timeline::before {
        display: none;
    }
    
    .timeline-marker {
        position: relative;
        left: 0;
        margin: 0 auto 1rem auto;
    }
    
    .timeline-content {
        margin-left: 0;
    }
}
</style>

<script>
// Help search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('helpSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const sections = document.querySelectorAll('.help-section');
            
            sections.forEach(function(section) {
                const keywords = section.getAttribute('data-keywords').toLowerCase();
                const content = section.textContent.toLowerCase();
                
                if (searchTerm === '' || keywords.includes(searchTerm) || content.includes(searchTerm)) {
                    section.style.display = 'block';
                    section.classList.remove('highlight');
                    
                    // Highlight matching text
                    if (searchTerm !== '') {
                        highlightSearchText(section, searchTerm);
                        section.classList.add('highlight');
                    } else {
                        removeSearchHighlights(section);
                    }
                } else {
                    section.style.display = 'none';
                }
            });
            
            // Show "no results" message if needed
            showNoResultsMessage(searchTerm, sections);
        });
    }
    
    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Highlight the target section briefly
                target.classList.add('highlight');
                setTimeout(() => {
                    target.classList.remove('highlight');
                }, 3000);
            }
        });
    });
});

function clearHelpSearch() {
    const searchInput = document.getElementById('helpSearch');
    searchInput.value = '';
    searchInput.dispatchEvent(new Event('input'));
}

function highlightSearchText(element, searchTerm) {
    // Remove existing highlights first
    removeSearchHighlights(element);
    
    // Get all text nodes
    const walker = document.createTreeWalker(
        element,
        NodeFilter.SHOW_TEXT,
        {
            acceptNode: function(node) {
                // Skip script and style elements
                if (node.parentElement.tagName === 'SCRIPT' || 
                    node.parentElement.tagName === 'STYLE') {
                    return NodeFilter.FILTER_REJECT;
                }
                return NodeFilter.FILTER_ACCEPT;
            }
        },
        false
    );
    
    const textNodes = [];
    let node;
    while (node = walker.nextNode()) {
        textNodes.push(node);
    }
    
    // Highlight matching text
    textNodes.forEach(function(textNode) {
        const text = textNode.textContent.toLowerCase();
        const index = text.indexOf(searchTerm);
        
        if (index !== -1) {
            const parent = textNode.parentNode;
            const beforeText = textNode.textContent.substring(0, index);
            const matchText = textNode.textContent.substring(index, index + searchTerm.length);
            const afterText = textNode.textContent.substring(index + searchTerm.length);
            
            const span = document.createElement('span');
            span.className = 'search-highlight';
            span.textContent = matchText;
            
            parent.insertBefore(document.createTextNode(beforeText), textNode);
            parent.insertBefore(span, textNode);
            parent.insertBefore(document.createTextNode(afterText), textNode);
            parent.removeChild(textNode);
        }
    });
}

function removeSearchHighlights(element) {
    const highlights = element.querySelectorAll('.search-highlight');
    highlights.forEach(function(highlight) {
        const parent = highlight.parentNode;
        parent.insertBefore(document.createTextNode(highlight.textContent), highlight);
        parent.removeChild(highlight);
        parent.normalize();
    });
}

function showNoResultsMessage(searchTerm, sections) {
    const visibleSections = Array.from(sections).filter(section => 
        section.style.display !== 'none'
    );
    
    let noResultsMsg = document.getElementById('noResultsMessage');
    
    if (searchTerm !== '' && visibleSections.length === 0) {
        if (!noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.id = 'noResultsMessage';
            noResultsMsg.className = 'alert alert-info text-center';
            noResultsMsg.innerHTML = `
                <i class="bi bi-search" aria-hidden="true"></i> 
                <h6>No help topics found matching "${searchTerm}"</h6>
                <p class="mb-0">Try different keywords like "email setup", "logo upload", or "contact information"</p>
            `;
            document.querySelector('.help-content').appendChild(noResultsMsg);
        }
        noResultsMsg.style.display = 'block';
    } else if (noResultsMsg) {
        noResultsMsg.style.display = 'none';
    }
}

/* Enhanced styling for better formatting like branding_settings.php */
.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.nav-section {
    padding: 1rem;
    border-left: 3px solid #e3e6f0;
    margin-bottom: 1rem;
    background-color: #fafbfc;
    border-radius: 0.35rem;
}

.nav-section h6 {
    margin-bottom: 1rem;
    font-weight: 600;
}

.nav-section ul li {
    padding: 0.25rem 0;
}

.nav-section ul li a {
    color: #5a5c69;
    transition: color 0.15s ease-in-out;
}

.nav-section ul li a:hover {
    color: #3a3b45;
    text-decoration: none;
}

.section-content {
    padding: 1rem 0;
}

.section-content .lead {
    font-size: 1.1rem;
    color: #5a5c69;
    margin-bottom: 1.5rem;
}

.stats-grid {
    border-top: 1px solid #e3e6f0;
    padding-top: 1rem;
}

.stat-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f6;
}

.process-list {
    padding-left: 1.5rem;
}

.process-list li {
    margin-bottom: 0.75rem;
    color: #5a5c69;
}

.header-icon {
    margin-right: 0.5rem;
    color: #5a5c69;
}

.form-control-lg {
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
}

.btn-block {
    font-weight: 600;
    padding: 0.75rem 1rem;
}

.bg-light {
    background-color: #f8f9fc !important;
    border: 1px solid #e3e6f0;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .nav-section {
        margin-bottom: 0.5rem;
    }
    
    .section-content .lead {
        font-size: 1rem;
    }
    
    .stats-grid {
        margin-top: 1rem;
    }
}
</style>
</script>

<?php echo template_admin_footer(); ?>
