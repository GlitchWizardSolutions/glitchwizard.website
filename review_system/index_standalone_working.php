<?php
/*
PAGE NAME  : index_standalone_working.php
LOCATION   : public_html/review_system/index_standalone_working.php
DESCRIPTION: Working standalone review system page without main system integration.
FUNCTION   : Display reviews page using basic HTML structure and call reviews.php via AJAX.
CHANGE LOG : 2025-08-12 - Created as working solution while fixing integration issues
*/

// Include simple config
include 'config_simple.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Reviews</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" rel="stylesheet" />
    <!-- Reviews CSS -->
    <link href="reviews.css" rel="stylesheet" type="text/css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">← Back to Site</a>
            <span class="navbar-text">Customer Reviews</span>
        </div>
    </nav>

    <!-- Main content container -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <main class="content">
                    <section class="hero-section text-center mb-5">
                        <h1>Customer Reviews</h1>
                        <p class="lead">See what our customers are saying about us and share your own experience.</p>
                    </section>

                    <section class="reviews-section">
                        <!-- The code below will populate the reviews -->
                        <div class="reviews-container">
                            <div class="reviews" id="reviewsContainer"></div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Reviews JavaScript -->
    <script src="reviews.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the reviews system
        new Reviews({
            page_id: 1, // Unique page ID
            reviews_per_pagination_page: 5, // Number of reviews to show per page
            current_pagination_page: 1, // Current page of pagination
            breakdown_status: 'open' // open | closed
        });
    });
    </script>
</body>
</html>
