<?php
include_once '../assets/includes/main.php';
echo template_admin_header('Review System Instructions', 'reviews', 'manage');
?>

<div class="content-title">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>
            </svg>
        </div>
        <div class="txt">
            <h2>Review System - Complete Guide</h2>
            <p>Comprehensive documentation for understanding and implementing the review system</p>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">System Overview</h6>
            </div>
            <div class="card-body">
                <h5 class="text-primary">What is the Review System?</h5>
                <p>The Review System is a comprehensive solution for collecting, managing, and displaying customer reviews and ratings for pages, products, services, or any content on your website. It functions as a standalone review platform that can be embedded anywhere on your site.</p>
                
                <h6 class="mt-4">Core Components:</h6>
                <ul>
                    <li><strong>Admin Panel:</strong> Complete management interface for reviews, pages, filters, and system settings</li>
                    <li><strong>Client Interface:</strong> Public-facing review display and submission forms</li>
                    <li><strong>Database Structure:</strong> Reviews, pages, accounts, filters, and image attachments</li>
                    <li><strong>Security Features:</strong> User authentication, review approval workflow, content filtering</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">How to Create Pages & Add Content</h6>
            </div>
            <div class="card-body">
                <h6 class="text-success">Method 1: Direct Page Creation</h6>
                <ol>
                    <li>Go to <strong>Review Pages</strong> in the admin panel</li>
                    <li>Create a review with any Page ID (e.g., 1, 2, 3)</li>
                    <li>After creating the review, go to <strong>Review Pages</strong></li>
                    <li>Click <strong>"Edit Details"</strong> for your page</li>
                    <li>Add title, description, and URL for the page</li>
                </ol>
                
                <h6 class="text-info mt-3">Method 2: Admin-First Approach</h6>
                <ol>
                    <li>Manually add entries to <code>review_page_details</code> table</li>
                    <li>Create meaningful page structures before reviews</li>
                    <li>Use admin interface to manage content</li>
                </ol>
                
                <div class="alert alert-warning mt-3">
                    <strong>Note:</strong> Currently, pages are created implicitly when reviews are submitted with a page ID. This design assumes reviews drive page creation.
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Database Structure</h6>
            </div>
            <div class="card-body">
                <h6>Core Tables:</h6>
                <ul>
                    <li><strong>reviews:</strong> Main review data (content, rating, dates)</li>
                    <li><strong>review_page_details:</strong> Page metadata (title, description, URL)</li>
                    <li><strong>accounts:</strong> User accounts for authentication</li>
                    <li><strong>review_filters:</strong> Content filtering (word replacement)</li>
                    <li><strong>review_images:</strong> Image attachments for reviews</li>
                </ul>
                
                <h6 class="mt-3">Key Features:</h6>
                <ul>
                    <li>Star ratings (1-5 configurable)</li>
                    <li>Rich text reviews with image uploads</li>
                    <li>User authentication & profiles</li>
                    <li>Review approval workflow</li>
                    <li>Content filtering system</li>
                    <li>Like/reaction system</li>
                    <li>Email notifications</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Deployment Recommendation & Use Cases</h6>
            </div>
            <div class="card-body">
                <h5 class="text-primary">Recommended Deployment: Public Area</h5>
                <p><strong>Location:</strong> <code>/public_html/reviews/</code> (Public access)</p>
                
                <h6 class="text-success">Why Public Deployment Makes Sense:</h6>
                <div class="row">
                    <div class="col-md-6">
                        <ul>
                            <li><strong>SEO Benefits:</strong> Public reviews improve search rankings</li>
                            <li><strong>Social Proof:</strong> Visible testimonials build trust</li>
                            <li><strong>Universal Access:</strong> Anyone can view reviews</li>
                            <li><strong>Marketing Value:</strong> Reviews serve as content marketing</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul>
                            <li><strong>Customer Engagement:</strong> Easy review submission</li>
                            <li><strong>Transparency:</strong> Open feedback system</li>
                            <li><strong>Integration Ready:</strong> Embed anywhere on site</li>
                            <li><strong>Standalone Operation:</strong> Independent of user accounts</li>
                        </ul>
                    </div>
                </div>
                
                <h5 class="text-primary mt-4">Ideal Use Case Scenarios:</h5>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border-primary mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">🛍️ E-Commerce Store</h6>
                            </div>
                            <div class="card-body">
                                <ul class="small">
                                    <li>Product reviews & ratings</li>
                                    <li>Customer testimonials</li>
                                    <li>Purchase validation</li>
                                    <li>Trust building</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-success mb-3">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">🏢 Service Business</h6>
                            </div>
                            <div class="card-body">
                                <ul class="small">
                                    <li>Service quality feedback</li>
                                    <li>Client testimonials</li>
                                    <li>Portfolio validation</li>
                                    <li>Reputation management</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-info mb-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">📝 Content Platform</h6>
                            </div>
                            <div class="card-body">
                                <ul class="small">
                                    <li>Article/blog feedback</li>
                                    <li>Course evaluations</li>
                                    <li>Resource ratings</li>
                                    <li>Community engagement</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <h6 class="alert-heading">💡 Implementation Strategy</h6>
                    <p class="mb-0">Deploy as a public microservice that can be embedded into any page via iframe or direct integration. Each page/product gets a unique page_id, and reviews are collected and displayed contextually.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Admin Workflow</h6>
            </div>
            <div class="card-body">
                <h6>Daily Operations:</h6>
                <ol>
                    <li><strong>Dashboard:</strong> Monitor new reviews and statistics</li>
                    <li><strong>Review Management:</strong> Approve/edit/respond to reviews</li>
                    <li><strong>Page Management:</strong> Update page details and metadata</li>
                    <li><strong>Filter Management:</strong> Maintain content filtering rules</li>
                    <li><strong>User Management:</strong> Handle user accounts and permissions</li>
                </ol>
                
                <h6 class="mt-3">Content Moderation:</h6>
                <ul>
                    <li>Review approval queue</li>
                    <li>Automated content filtering</li>
                    <li>Response management</li>
                    <li>Spam prevention</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Client Experience</h6>
            </div>
            <div class="card-body">
                <h6>Public Interface Features:</h6>
                <ul>
                    <li>Star rating display with breakdown</li>
                    <li>Review submission form</li>
                    <li>Image upload capability</li>
                    <li>User authentication (optional)</li>
                    <li>Like/reaction system</li>
                    <li>Sorting and filtering options</li>
                </ul>
                
                <h6 class="mt-3">Integration Options:</h6>
                <ul>
                    <li><strong>Embed:</strong> <code>&lt;iframe src="reviews.php?page_id=1"&gt;</code></li>
                    <li><strong>Direct Link:</strong> Dedicated review pages</li>
                    <li><strong>AJAX:</strong> Dynamic loading in existing pages</li>
                    <li><strong>API:</strong> Custom integrations</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning">
                <h6 class="card-title mb-0">🚀 Quick Start Guide</h6>
            </div>
            <div class="card-body">
                <h6>To Get Started:</h6>
                <ol>
                    <li><strong>Move Client System:</strong> Copy <code>/client_portal/review_system/</code> to <code>/public_html/reviews/</code></li>
                    <li><strong>Configure Database:</strong> Update config.php with your database settings</li>
                    <li><strong>Create First Page:</strong> Add a review with page_id=1, then edit page details</li>
                    <li><strong>Test Integration:</strong> Embed reviews.php?page_id=1 on a test page</li>
                    <li><strong>Customize Appearance:</strong> Modify reviews.css to match your branding</li>
                    <li><strong>Configure Settings:</strong> Adjust authentication, approval, and notification settings</li>
                </ol>
                
                <div class="alert alert-success mt-3">
                    <strong>Perfect For:</strong> Any website needing customer feedback, testimonials, or rating systems. The flexible page-based approach makes it suitable for blogs, e-commerce, portfolios, or service businesses.
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo template_admin_footer(); ?>
