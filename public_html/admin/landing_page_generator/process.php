<?php
/* 
 * Landing Page Generation Processor
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: process.php
 * LOCATION: /public_html/admin/landing_page_generator/
 * PURPOSE: Process and generate landing pages from user input
 * DETAILED DESCRIPTION:
 * This file handles the processing and generation of landing pages based on
 * user input. It manages AI content generation, template processing, file
 * creation, and asset management for new landing pages. The processor ensures
 * proper formatting and structure of generated content.
 * REQUIRED FILES: 
 * - /private/openai-key.php
 * - /public_html/assets/includes/main.php
 * - /public_html/admin/landing_page_generator/templates/
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - AI content generation
 * - Template processing
 * - File system management
 * - Asset organization
 * - Error handling
 */

include '../../private/openai-key.php';

// Input sanitization
$domain = preg_replace('/[^a-zA-Z0-9.-]/', '', strtolower(trim($_POST['domain'] ?? '')));
$industry = trim($_POST['industry'] ?? '');

if (!$domain || !$industry) {
    die("Domain and industry are required.");
}

// Directory setup
$templateDir = __DIR__ . '/template';
$targetDir = __DIR__ . '/../landing_pages/' . $domain;

if (file_exists($targetDir)) {
    die("A folder for this domain already exists.");
}
mkdir($targetDir, 0755, true);

// Copy template files
function copyDirectory($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if ($file == '.' || $file == '..') continue;
        $srcPath = "$src/$file";
        $dstPath = "$dst/$file";
        if (is_dir($srcPath)) {
            copyDirectory($srcPath, $dstPath);
        } else {
            copy($srcPath, $dstPath);
        }
    }
    closedir($dir);
}
copyDirectory($templateDir, $targetDir);

// Prompt for OpenAI with OG tags
$prompt = <<<EOT
You are an expert in SEO and social media metadata. For the following business info:

Domain: $domain
Industry: $industry

Generate:
1. A meta description (max 155 characters).
2. A comma-separated list of 8-12 SEO keywords.
3. Open Graph and Twitter meta tags:
   - og:title
   - og:description
   - og:image (use a placeholder image URL like https://example.com/og-image.jpg)
   - og:url
   - og:type (use "website")
   - twitter:card (use "summary_large_image")
   - twitter:title
   - twitter:description
   - twitter:image

Respond in this exact format:
Meta Description: ...
Meta Keywords: ...
OG Tags:
og:title: ...
og:description: ...
og:image: ...
og:url: ...
og:type: ...
twitter:card: ...
twitter:title: ...
twitter:description: ...
twitter:image: ...
EOT;

// Call OpenAI
$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . OPENAI_API_KEY
]);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = [
    'model' => 'gpt-3.5-turbo',
    'messages' => [
        ['role' => 'system', 'content' => 'You are a helpful assistant for SEO and social media meta tags.'],
        ['role' => 'user', 'content' => $prompt],
    ],
    'max_tokens' => 600,
    'temperature' => 0.7,
];
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);
$ai_output = $responseData['choices'][0]['message']['content'] ?? '';

// Extract metadata using regex
preg_match('/Meta Description:(.*)\nMeta Keywords:(.*)\nOG Tags:(.*)/is', $ai_output, $matches);
$meta_description = trim($matches[1] ?? '');
$meta_keywords = trim($matches[2] ?? '');
$og_tags_raw = trim($matches[3] ?? '');

// Parse OG tags
$og_tags = [];
foreach (explode("\n", $og_tags_raw) as $line) {
    if (strpos($line, ':') !== false) {
        [$key, $value] = explode(':', $line, 2);
        $og_tags[trim($key)] = trim($value);
    }
}

// Save meta-vars.php
$metaVarsCode = "<?php
\$meta_description = " . var_export($meta_description, true) . ";
\$meta_keywords = " . var_export($meta_keywords, true) . ";

// Open Graph / Twitter tags
";
foreach ($og_tags as $key => $val) {
    $varName = str_replace(['-', ':'], '_', $key);
    $metaVarsCode .= "\$meta_$varName = " . var_export($val, true) . ";
";
}
$metaVarsCode .= "?>";
file_put_contents("$targetDir/meta-vars.php", $metaVarsCode);

// Write preview.html
$previewHtml = "<!DOCTYPE html>
<html>
<head>
  <meta charset='UTF-8'>
  <title>Preview: $domain</title>
  <style>
    body { font-family: Arial; padding: 2em; background: #f4f4f4; }
    .preview { background: white; padding: 2em; border-radius: 8px; max-width: 700px; margin: auto; }
    .title { font-size: 1.2em; color: #1a0dab; }
    .url { color: #006621; font-size: 0.9em; }
    .desc { color: #545454; margin-top: 0.5em; }
  </style>
</head>
<body>
<div class='preview'>
  <div class='title'>{$og_tags['og:title']}</div>
  <div class='url'>{$og_tags['og:url']}</div>
  <div class='desc'>{$og_tags['og:description']}</div>
</div>
</body>
</html>";
file_put_contents("$targetDir/preview.html", $previewHtml);

// Display success
echo "<h2>Landing Page Folder Created</h2>";
echo "<p><strong>Domain:</strong> $domain</p>";
echo "<p><strong>Industry:</strong> $industry</p>";
echo "<p><strong>Meta Description:</strong><br>$meta_description</p>";
echo "<p><strong>Meta Keywords:</strong><br>$meta_keywords</p>";
echo "<p><a href='../landing_pages/$domain/preview.html' target='_blank'>View Preview</a></p>";
echo "<p>Files created in: <code>$targetDir</code></p>";
echo "<p><a href='generate.php'>&laquo; Create Another</a></p>";
?>
