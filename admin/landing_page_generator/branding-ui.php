<?php
/* 
 * Landing Page Branding Interface
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: branding-ui.php
 * LOCATION: /public_html/admin/landing_page_generator/
 * PURPOSE: Configure branding settings for landing pages
 * DETAILED DESCRIPTION:
 * This file provides a user interface for managing branding settings of
 * generated landing pages. It allows administrators to configure colors,
 * fonts, logos, and other brand-specific elements that will be applied
 * to the generated landing pages.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/admin/landing_page_generator/branding-upload-handler.php
 * - /public_html/assets/includes/settings/branding_config.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Brand color selection
 * - Typography management
 * - Logo upload
 * - Industry presets
 * - Preview functionality
 */

// default to temp folder if not passed
$folder = $_GET['folder'] ?? 'temp';
$branding_action = "branding-upload-handler.php?folder=" . urlencode($folder);

// Preset industries
$industries = [
  'default' => 'Choose an Industry',
  'real_estate' => 'Real Estate',
  'medical' => 'Medical',
  'law_firm' => 'Law Firm',
  'restaurant' => 'Restaurant',
  'travel' => 'Travel',
  'pet_grooming' => 'Pet Grooming',
  'spa' => 'Spa & Wellness'
];

// Industry branding presets
$presets = [
  'real_estate' => ['#004085', '#6c757d', '#ffffff', '#212529', "'Poppins', sans-serif", "'Roboto', sans-serif"],
  'medical'      => ['#00796b', '#c2185b', '#f5f5f5', '#263238', "'Lato', sans-serif", "'Open Sans', sans-serif"],
  'restaurant'   => ['#d32f2f', '#fbc02d', '#fff3e0', '#5d4037', "'Merriweather', serif", "'Lora', serif"],
  'spa'          => ['#4db6ac', '#a7ffeb', '#e0f2f1', '#004d40', "'Playfair Display', serif", "'Nunito', sans-serif"]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Configure Branding</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      font-family: sans-serif;
      background: #f8f9fa;
      margin: 0;
      padding: 2em;
    }
    .container {
      max-width: 700px;
      background: white;
      padding: 2em;
      margin: auto;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin-top: 1em;
      font-weight: 600;
    }
    input, select {
      width: 100%;
      padding: 0.5em;
      margin-top: 0.3em;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 1em;
    }
    button {
      margin-top: 1.5em;
      padding: 0.8em 1.5em;
      font-size: 1em;
      background: #007BFF;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container" role="form" aria-labelledby="brandingFormHeading">
    <h1 id="brandingFormHeading">Configure Branding for <code><?php echo htmlspecialchars($folder); ?></code></h1>
    
    <div style="background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
      <h3 style="margin-top: 0;">💡 Tip: Use Main Site Branding</h3>
      <p>For consistency, consider copying your branding settings from the main <strong><a href="../settings/branding_settings.php" target="_blank">Comprehensive Branding Settings</a></strong> to ensure your landing pages match your main site.</p>
    </div>

    <form method="post" action="<?php echo $branding_action; ?>" enctype="multipart/form-data">
      <label for="industry">Industry Preset</label>
      <select id="industry" name="industry" onchange="applyPreset(this.value)">
        <?php foreach ($industries as $key => $label): ?>
          <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
        <?php endforeach; ?>
      </select>

      <label for="brand_primary">Primary Color</label>
      <input type="color" id="brand_primary" name="brand_primary" placeholder="#007BFF">

      <label for="brand_secondary">Secondary Color</label>
      <input type="color" id="brand_secondary" name="brand_secondary" placeholder="#6C757D">

      <label for="brand_background">Background Color</label>
      <input type="color" id="brand_background" name="brand_background" placeholder="#FFFFFF">

      <label for="brand_text">Text Color</label>
      <input type="color" id="brand_text" name="brand_text" placeholder="#333333">

      <label for="brand_font_headings">Heading Font</label>
      <input type="text" id="brand_font_headings" name="brand_font_headings" placeholder="'Poppins', sans-serif">

      <label for="brand_font_body">Body Font</label>
      <input type="text" id="brand_font_body" name="brand_font_body" placeholder="'Open Sans', sans-serif">

      <label for="logo">Upload Logo (160x160px preferred)</label>
      <input type="file" id="logo" name="logo" accept=".jpg,.jpeg,.png,.gif" aria-label="Upload logo file">

      <button type="submit" aria-label="Submit branding settings">Save Branding</button>
    </form>
  </div>

  <script>
    function applyPreset(industry) {
      const presets = <?php echo json_encode($presets); ?>;
      if (!presets[industry]) return;
      const [primary, secondary, background, text, fontH, fontB] = presets[industry];
      document.getElementById('brand_primary').value = primary;
      document.getElementById('brand_secondary').value = secondary;
      document.getElementById('brand_background').value = background;
      document.getElementById('brand_text').value = text;
      document.getElementById('brand_font_headings').value = fontH;
      document.getElementById('brand_font_body').value = fontB;
    }
  </script>
</body>
</html>
