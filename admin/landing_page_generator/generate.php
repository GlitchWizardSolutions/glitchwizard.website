<?php
/* 
 * Landing Page Generator Interface
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: generate.php
 * LOCATION: /public_html/admin/landing_page_generator/
 * PURPOSE: Interface for generating new landing pages
 * DETAILED DESCRIPTION:
 * This file provides a user interface for creating new landing pages using
 * AI-assisted generation. It allows administrators to input page requirements,
 * configure settings, and initiate the landing page generation process with
 * automated content and structure creation.
 * REQUIRED FILES: 
 * - /private/openai-key.php
 * - /public_html/assets/includes/main.php
 * - /public_html/admin/landing_page_generator/process.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - AI-assisted page generation
 * - Template selection
 * - Content configuration
 * - SEO settings
 * - Preview capabilities
 */

include '../../private/openai-key.php';
?>
<!DOCTYPE html>
<html>
<head><title>New Lead Page Generator</title></head>
<body>
<h1>Create New Lead Page</h1>
<form action="process.php" method="post">
  <label>Domain Name (no www):<br>
    <input type="text" name="domain" required>
  </label><br><br>

  <label>Industry Type:<br>
    <input type="text" name="industry" required>
  </label><br><br>

  <button type="submit">Generate Website Folder</button>
</form>
</body>
</html>
