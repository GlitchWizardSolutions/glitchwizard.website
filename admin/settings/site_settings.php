<?php
/* 
 * Site Settings Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: site_settings.php
 * LOCATION: /public_html/admin/settings/
 * PURPOSE: Manage general website settings and configurations
 * DETAILED DESCRIPTION:
 * This file provides an interface for managing general website settings
 * including site metadata, appearance options, contact information,
 * and other site-wide configurations that affect the entire website's
 * functionality and appearance.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/assets/includes/settings/site_config.php
 * - /private/gws-universal-config.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Site metadata management
 * - Contact information settings
 * - Appearance configuration
 * - Social media integration
 * - Analytics settings
 */

if (!function_exists('headerBlock')) {
    function headerBlock($title = "GWDS PDF System") {
        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{$title}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5.3.3 CSS -->
  <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">

  <!-- Summernote Lite CSS -->
  <link rel="stylesheet" href="/assets/summernote/summernote-lite.min.css">

  <!-- jQuery -->
  <script src="/assets/jquery/jquery.min.js"></script>

  <!-- Bootstrap 5.3.3 Bundle JS -->
  <script src="/assets/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Summernote Lite JS -->
  <script src="/assets/summernote/summernote-lite.min.js"></script>

  <style>
    /* Hide duplicate dropdown caret icons in Summernote */
    .note-btn-group .dropdown-toggle::after {
      display: none !important;
    }

    /* Tab styling for accessibility and contrast */
    .nav-tabs .nav-link.active {
      background-color: #f8f9fa; /* eggshell */
      border-color: #dee2e6 #dee2e6 #fff;
      color: #212529;
      font-weight: bold;
    }
    .nav-tabs .nav-link {
      background-color: #e9ecef;
      border: 1px solid transparent;
      border-color: transparent transparent #dee2e6;
      color: #6c757d;
    }
    .tab-content {
      border: 1px solid #dee2e6;
      border-top: none;
      padding: 1rem;
      background-color: #fdfdfd; /* subtle neutral bg */
    }

    /* Dynamic headings for tab panels */
    .tab-pane[data-title]:before {
      content: attr(data-title);
      display: block;
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 0.75rem;
    }
  </style>

</head>
<body>
<div class="container mt-4">
HTML;
    }
}

if (!function_exists('footerBlock')) {
    function footerBlock() {
        echo <<<HTML
</div> <!-- /.container -->
</body>
</html>
HTML;
    }
}

if (!function_exists('getClientNameById')) {
    function getClientNameById(PDO $pdo, int $clientId): string {
        $stmt = $pdo->prepare("SELECT name FROM clients WHERE id = ? LIMIT 1");
        $stmt->execute([$clientId]);
        return $stmt->fetchColumn() ?: 'Unknown';
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string {
        $text = preg_replace('~[\p{P}\p{S}]+~u', '', $text);
        $text = preg_replace('/[^a-zA-Z0-9]+/', '-', $text);
        return strtolower(trim($text, '-'));
    }
}

if (!function_exists('generateTemplateHTML')) {
    function generateTemplateHTML(string $templateType, array $postData, PDO $pdo): string {
        $title = htmlspecialchars($postData['document_title'] ?? 'Untitled');
        $body  = $postData['document_content'] ?? '<p>No content provided.</p>';
        $footer = htmlspecialchars($postData['footer'] ?? '');

        return <<<HTML
<html><head><style>body { font-family: sans-serif; }</style></head>
<body>
  <h2>{$title}</h2>
  {$body}
  <hr>
  <footer><small>{$footer}</small></footer>
</body></html>
HTML;
    }
}

