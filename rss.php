<?php
/*
PAGE NAME  : rss.php
LOCATION   : public_html/rss.php
DESCRIPTION: This page generates an RSS feed for blog posts.
FUNCTION   : Users can subscribe to the RSS feed to receive updates on new blog posts.
CHANGE LOG : Initial creation of rss.php for blog post RSS feed.
2025-08-04 : Refactored to match blog.php/post.php structure and conventions.
*/

// Start output buffering to prevent any accidental output
ob_start();

// Get the database configuration and settings
require_once '../private/gws-universal-config.php';
// Note: Settings now loaded from database via database_settings.php system
require_once 'assets/includes/settings/database_settings.php';
// DO NOT include blog_system/functions.php - it outputs HTML

// Clean the buffer and set header
ob_clean();
header('Content-Type: application/rss+xml; charset=UTF-8');

$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';

$stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE active="Yes" ORDER BY id DESC LIMIT 20');
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<?xml version='1.0' encoding='UTF-8'?>\n";
echo "<rss version='2.0'>\n";
echo "  <channel>\n";
echo "    <title>" . htmlspecialchars($business_name) . " | RSS</title>\n";
echo "    <link>{$base_url}blog.php</link>\n";
echo "    <description>RSS Feed</description>\n";
echo "    <language>en-us</language>\n";

foreach ($posts as $row) {
    $title = htmlspecialchars($row['title']);
    $link = $base_url . 'post.php?name=' . urlencode($row['slug']);
    $description = htmlspecialchars(short_text(strip_tags(html_entity_decode($row['content'])), 100));
    $date = date('r', strtotime($row['date'] . ' ' . $row['time']));
    $guid = $row['id'];
    echo "    <item>\n";
    echo "      <title>$title</title>\n";
    echo "      <link>$link</link>\n";
    echo "      <description>$description</description>\n";
    echo "      <pubDate>$date</pubDate>\n";
    echo "      <guid isPermaLink=\"false\">$guid</guid>\n";
    echo "    </item>\n";
}
echo "  </channel>\n";
echo "</rss>\n";
?>