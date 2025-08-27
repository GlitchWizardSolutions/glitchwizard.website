<?php
/*
PAGE NAME  : index.php
LOCATION   : public_html/review_system/index.php
DESCRIPTION: Standalone review system page integrated with blog system architecture.
FUNCTION   : Display reviews page using unified public website templates and call reviews.php via AJAX.
CHANGE LOG : 2025-08-12 - Created using PUBLIC_WEBSITE_INTEGRATION_CHECKLIST.php
*/

// Include necessary files - following blog system pattern
include_once "../assets/includes/doctype.php";
include_once "../assets/includes/header.php";
?>

<!-- Reviews-specific CSS (included in head per requirements) -->
<link href="reviews.css" rel="stylesheet" type="text/css">

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
                                <p class="lead">See what our customers are saying about us and share your own experience.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="reviews-section">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <!-- The code below will populate the reviews -->
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
<script src="reviews.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the reviews system
    new Reviews({
        page_id: 1, // Unique page ID
        reviews_per_pagination_page: 5, // Number of reviews to show per page
        current_pagination_page: 1, // Current page of pagination
        breakdown_status: 'open', // open | closed
        container: document.getElementById('reviewsContainer')
    });
});
</script>

<?php
// Include footer using blog system pattern
include_once "../assets/includes/footer.php";
?>
