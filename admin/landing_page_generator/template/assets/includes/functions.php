<?php
/**
 * Extracts the first <h1>, <h2>, and <p> from HTML content.
 */
function extract_headings_and_paragraph($html) {
    $doc = new DOMDocument();
    @$doc->loadHTML($html);

    $h1 = $doc->getElementsByTagName('h1')->length ? $doc->getElementsByTagName('h1')->item(0)->textContent : '';
    $h2 = $doc->getElementsByTagName('h2')->length ? $doc->getElementsByTagName('h2')->item(0)->textContent : '';
    $p  = $doc->getElementsByTagName('p')->length  ? $doc->getElementsByTagName('p')->item(0)->textContent  : '';

    return [$h1, $h2, $p];
}

/**
 * Generates a meta description from headings and paragraph.
 */
function auto_meta_description($html, $fallback = 'A universal hybrid app project by GWS.') {
    list($h1, $h2, $p) = extract_headings_and_paragraph($html);
    $parts = array_filter([$h1, $h2, $p]);
    if (!$parts) return $fallback;
    $desc = implode('. ', $parts);
    return mb_substr($desc, 0, 155); // Google recommends ~155 chars
}

/**
 * Generates meta keywords from headings and paragraph.
 */
function auto_meta_keywords($html) {
    list($h1, $h2, $p) = extract_headings_and_paragraph($html);
    $text = strtolower(implode(' ', [$h1, $h2, $p]));
    $text = preg_replace('/[^\w\s]/', '', $text); // Remove punctuation
    $words = array_unique(array_filter(explode(' ', $text)));
    $common = ['the','and','for','with','that','this','from','are','was','but','have','has','not','you','your','our','about','more','can','all','any','use','will','one','out','get','now','new','app','gws','universal','hybrid','project','by'];
    $keywords = array_diff($words, $common);
    return implode(', ', array_slice($keywords, 0, 10));
}

/**
 * Generates a SEO-friendly description.
 */
function seo_description($keywords, $default = 'A universal hybrid app project by GWS.') {
    if (empty($keywords)) return $default;
    $kw = explode(',', $keywords);
    $kw = array_map('trim', $kw);
    $main = array_slice($kw, 0, 3);
    return 'Find out more about ' . implode(', ', $main) . ' and more with GWS Universal Hybrid App.';
}