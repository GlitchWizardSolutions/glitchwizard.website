<?php
/*
PAGE NAME  : public_reviews.php
LOCATION   : public_html/public_reviews.php
DESCRIPTION: Main review system public interface integrated with blog system architecture.
FUNCTION   : Display reviews, ratings, and submission forms using unified public website templates.
CHANGE LOG : 2025-08-12 - Integrated with public website using PUBLIC_WEBSITE_INTEGRATION_CHECKLIST.php
*/

// Include necessary files - following blog system pattern
include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
?>

<!-- Reviews-specific CSS -->
<link href="review_system/reviews.css" rel="stylesheet" type="text/css">
<style>
/* Consistent spacing throughout the page */
.hero-section {
    padding-bottom: 10px !important;
}
.reviews-section {
    padding-top: 10px !important;
}
/* Match the spacing between columns area and reviews area to hero spacing */
.columns-section {
    margin-bottom: 10px !important;
}
/* Brand color styling for CTA section */
.login-cta h4 {
    color: #2fc090 !important; /* Secondary brand color */
}
.login-cta .btn-primary {
    background-color: #3671c9 !important; /* Primary brand color */
    border-color: #3671c9 !important;
}
.login-cta .btn-primary:hover {
    background-color: #3575c3 !important; /* Slightly darker on hover */
    border-color: #3575c3 !important;
}
</style>

<!-- Main content container -->
<div class="container">
    <div class="row">
        <div class="col-12">
            <main id="main" class="content">
                <section class="hero-section d-flex align-items-center">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 text-center">
                                <h1>Customer Reviews</h1>
                                <p class="lead">See what our verified customers are saying about us!</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="reviews-section">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-10 col-12">
                                <!-- Two column layout for chart and login CTA -->
                                <div class="row align-items-center columns-section">
                                    <div class="col-md-7">
                                        <!-- Chart column - ONLY the chart -->
                                        <div class="reviews-chart-container">
                                            <div class="reviews" id="reviewsChart"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <!-- Login CTA column - ONLY the CTA -->
                                        <div class="login-cta text-center" style="background-color: #f8f9fa; padding: 20px; border-radius: 8px;">
                                            <?php if (!isset($_SESSION['loggedin']) || (isset($_SESSION['role']) && in_array($_SESSION['role'], ['guest', 'blog_only']))): ?>
                                            <h4>Login to share your experience with us.</h4>
                                            <a href="auth.php?redirect=review" class="btn btn-primary btn-lg mt-3">Login to Review</a>
                                            <?php else: ?>
                                            <h4>Share your experience with us.</h4>
                                            <button type="button" class="btn btn-primary btn-lg mt-3" onclick="document.querySelector('#reviewsContainer .write-review-btn').click()">Write Review</button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Full width section for comments/reviews - SEPARATE ROW -->
                        <div class="row justify-content-center">
                            <div class="col-lg-10 col-12">
                                <div class="reviews-container">
                                    <div class="reviews" id="reviewsContainer"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</div>

<!-- Reviews JavaScript -->
<script src="review_system/reviews.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let chartInstance, reviewsInstance;
    
    // Initialize the chart (full chart with breakdown in left column)
    chartInstance = new Reviews({
        page_id: 1,
        type: 'full',
        breakdown_status: 'open',
        container: document.getElementById('reviewsChart'),
        php_file_url: 'review_system/reviews.php?chart_only=1'
    });
    
    // Initialize the main reviews container (individual reviews only)
    reviewsInstance = new Reviews({
        page_id: 1,
        reviews_per_pagination_page: 5,
        current_pagination_page: 1,
        type: 'full',
        breakdown_status: 'closed',
        container: document.getElementById('reviewsContainer'),
        php_file_url: 'review_system/reviews.php?reviews_only=1'
    });
    
    // Override the chart's event handlers to sync with reviews container
    setTimeout(() => {
        // Find and override star filter links in the chart
        const chartContainer = document.getElementById('reviewsChart');
        const reviewsContainer = document.getElementById('reviewsContainer');
        
        if (chartContainer) {
            chartContainer.querySelectorAll('.review-breakdown a').forEach(element => {
                element.onclick = event => {
                    event.preventDefault();
                    
                    // Update both instances
                    const starFilter = 'star_' + element.dataset.star;
                    
                    // Update the reviews instance
                    reviewsInstance.currentPaginationPage = 1;
                    reviewsInstance.sortBy = starFilter;
                    reviewsInstance.fetchReviews();
                    
                    // Scroll to reviews section
                    reviewsContainer.scrollIntoView({ behavior: 'smooth' });
                };
            });
        }
    }, 1000); // Wait for the chart to load
});
</script>

<?php
// Include footer using blog system pattern
include_once "assets/includes/footer.php";
?>