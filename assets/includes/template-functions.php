<?php
/**
 * Shared template functions for consistent UI rendering
 */

/**
 * Render a standard page header with title
 * @param string $title Page title
 * @param string $subtitle Optional subtitle
 * @param array $breadcrumbs Optional breadcrumb array [label => url]
 */
function renderPageHeader($title, $subtitle = '', array $breadcrumbs = []) {
    echo '&lt;div class="container mt-3"&gt;';
    if (!empty($breadcrumbs)) {
        echo '&lt;nav aria-label="breadcrumb"&gt;';
        echo '&lt;ol class="breadcrumb"&gt;';
        foreach ($breadcrumbs as $label => $url) {
            if ($url) {
                echo '&lt;li class="breadcrumb-item"&gt;&lt;a href="' . htmlspecialchars($url) . '"&gt;' . htmlspecialchars($label) . '&lt;/a&gt;&lt;/li&gt;';
            } else {
                echo '&lt;li class="breadcrumb-item active" aria-current="page"&gt;' . htmlspecialchars($label) . '&lt;/li&gt;';
            }
        }
        echo '&lt;/ol&gt;';
        echo '&lt;/nav&gt;';
    }
    echo '&lt;h1 class="mb-3"&gt;' . htmlspecialchars($title) . '&lt;/h1&gt;';
    if ($subtitle) {
        echo '&lt;p class="lead"&gt;' . htmlspecialchars($subtitle) . '&lt;/p&gt;';
    }
    echo '&lt;/div&gt;';
}

/**
 * Render a card container with header
 * @param string $title Card title
 * @param string $icon Optional FontAwesome icon class
 * @param array $headerButtons Optional array of header buttons [label => [url, class]]
 */
function renderCard($title, $icon = '', array $headerButtons = []) {
    echo '&lt;div class="card"&gt;';
    echo '&lt;div class="card-header d-flex justify-content-between align-items-center"&gt;';
    echo '&lt;div&gt;';
    if ($icon) {
        echo '&lt;i class="' . htmlspecialchars($icon) . '"&gt;&lt;/i&gt; ';
    }
    echo htmlspecialchars($title);
    echo '&lt;/div&gt;';
    if (!empty($headerButtons)) {
        echo '&lt;div&gt;';
        foreach ($headerButtons as $label => $button) {
            echo '&lt;a href="' . htmlspecialchars($button['url']) . '" class="btn ' . htmlspecialchars($button['class']) . '"&gt;' . htmlspecialchars($label) . '&lt;/a&gt; ';
        }
        echo '&lt;/div&gt;';
    }
    echo '&lt;/div&gt;';
    echo '&lt;div class="card-body"&gt;';
}

/**
 * Close a card container
 */
function closeCard() {
    echo '&lt;/div&gt;&lt;/div&gt;';
}

/**
 * Render a standard page footer
 * @param array $scripts Optional array of additional script URLs to include
 */
function renderPageFooter(array $scripts = []) {
    foreach ($scripts as $script) {
        echo '&lt;script src="' . htmlspecialchars($script) . '"&gt;&lt;/script&gt;';
    }
}
