<?php
// Initialize sessions
session_start();
// Include the config file
include 'config.php';
// PHPMailer Namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Page ID needs to exist as it is used to determine which comments are for which page.
if (!isset($_GET['page_id'])) {
    exit('Error: Page ID is missing!');
}
// Connect to the MySQL database using the PDO interface
try {
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name . ';charset=' . db_charset, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    // If there is an error with the connection, stop the script and display the error.
    exit('Failed to connect to database: ' . $exception->getMessage());
}
// The following function will be used to assign a unique icon color to our users
function color_from_string($string) {
    // The list of hex colors
    $colors = ['#34568B','#FF6F61','#6B5B95','#88B04B','#F7CAC9','#92A8D1','#955251','#B565A7','#009B77','#DD4124','#D65076','#45B8AC','#EFC050','#5B5EA6','#9B2335','#DFCFBE','#BC243C','#C3447A','#363945','#939597','#E0B589','#926AA6','#0072B5','#E9897E','#B55A30','#4B5335','#798EA4','#00758F','#FA7A35','#6B5876','#B89B72','#282D3C','#C48A69','#A2242F','#006B54','#6A2E2A','#6C244C','#755139','#615550','#5A3E36','#264E36','#577284','#6B5B95','#944743','#00A591','#6C4F3D','#BD3D3A','#7F4145','#485167','#5A7247','#D2691E','#F7786B','#91A8D0','#4C6A92','#838487','#AD5D5D','#006E51','#9E4624'];
    // Find color based on the string
    $colorIndex = hexdec(substr(sha1($string), 0, 10)) % count($colors);
    // Return the hex color
    return $colors[$colorIndex];
}
// Below function will convert datetime to time elapsed string.
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    $w = floor($diff->d / 7);
    $diff->d -= $w * 7;
    $string = ['y' => 'year','m' => 'month','w' => 'week','d' => 'day','h' => 'hour','i' => 'minute','s' => 'second'];
    foreach ($string as $k => &$v) {
        if ($k == 'w' && $w) {
            $v = $w . ' week' . ($w > 1 ? 's' : '');
        } else if (isset($diff->$k) && $diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
// Sanitize comment content function
function sanitize_comment_html($html, $editor) {
    if (empty(trim($html))) {
        return '';
    }
    // Whitelist of allowed tags and their specific allowed attributes.
    $allowed_elements = [
        'p'          => [], 'strong'     => [], 'b'  => [],
        'em'         => [], 'i'          => [], 'u'  => [], 's' => [],
        'blockquote' => [], 'pre'        => [], 'code' => [], 'br' => [],
        'div'        => ['class'], // Only for Quill's code blocks
        'a'          => ['href', 'target', 'rel']
    ];
    // Enable images
    if (images_enabled) {
        $allowed_elements['img'] = ['src'];
    }
    // Whitelist of allowed class names for <div> tags (from Quill's code blocks)
    $allowed_div_classes = ['ql-code-block-container', 'ql-code-block'];
    // Sanitize code blocks
    if ($editor === 'standard') {
        $html = preg_replace_callback('/<(code)(.*?)>(.*?)<\/\1>/is', function($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];
            $content = $matches[3];
            // decode br tags
            $escaped_content = str_replace('<br class="cs-newline">', PHP_EOL, $content);
            // Escape the raw content to be displayed as literal text
            $escaped_content = htmlspecialchars($escaped_content, ENT_QUOTES, 'UTF-8');
            return "<$tag$attributes>" . $escaped_content . "</$tag>";
        }, $html);
    }
    // Create a DOMDocument from the HTML
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html);
    libxml_clear_errors();
    $xpath = new DOMXPath($dom);
    // This is a critical step to prevent any code from being misinterpreted as HTML.
    if ($editor === 'quill') {
        $code_nodes = $xpath->query('//pre | //code');
        foreach ($code_nodes as $node) {
            $text_content = $node->nodeValue;
            $escaped_node = $dom->createTextNode(htmlspecialchars($text_content, ENT_QUOTES, 'UTF-8'));
            while ($node->firstChild) {
                $node->removeChild($node->firstChild);
            }
            $node->appendChild($escaped_node);
        }
    }
    // Iterate over all elements and sanitize them.
    $elements = $xpath->query('//body//*');
    for ($i = $elements->length - 1; $i >= 0; $i--) {
        $element = $elements->item($i);
        if (!($element instanceof DOMElement)) {
            continue;
        }
        $tag_name = strtolower($element->tagName);
        // If the tag is NOT in our whitelist, remove the tag but keep its content (unwrap it).
        if (!array_key_exists($tag_name, $allowed_elements)) {
            $fragment = $dom->createDocumentFragment();
            while ($element->childNodes->length > 0) {
                $fragment->appendChild($element->childNodes->item(0));
            }
            if ($element->parentNode) {
                $element->parentNode->replaceChild($fragment, $element);
            }
            continue;
        }
        // If the tag is allowed, check and sanitize its attributes.
        if ($element->hasAttributes()) {
            $attributes = iterator_to_array($element->attributes);
            foreach ($attributes as $attr) {
                $attr_name = strtolower($attr->name);
                $allowed_attributes = $allowed_elements[$tag_name];
                if (!in_array($attr_name, $allowed_attributes)) {
                    $element->removeAttribute($attr_name);
                    continue;
                }
                if ($tag_name === 'a' && $attr_name === 'href') {
                    $url = $attr->value;
                    $allowed_protocols = ['http', 'https', 'mailto', 'ftp'];
                    $parsed_url = parse_url($url);
                    if (isset($parsed_url['scheme']) && !in_array(strtolower($parsed_url['scheme']), $allowed_protocols)) {
                        $element->removeAttribute('href');
                    }
                    if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
                        $element->removeAttribute('href');
                    }
                }
                if ($tag_name === 'a') {
                    $element->setAttribute('target', '_blank');
                    $element->setAttribute('rel', 'nofollow noopener noreferrer');
                }
                if (images_enabled && $tag_name === 'img' && $attr_name === 'src') {
                    $url = $attr->value;
                    $allowed_protocols = ['http', 'https', 'data'];
                    $parsed_url = parse_url($url);
                    if (isset($parsed_url['scheme']) && !in_array(strtolower($parsed_url['scheme']), $allowed_protocols)) {
                        $element->removeAttribute('src');
                    }
                }
                if (images_enabled && $tag_name === 'img') {
                    $element->removeAttribute('alt');
                    $element->setAttribute('alt', 'Comment Image');
                    $element->setAttribute('loading', 'lazy');
                }
                if ($tag_name === 'div' && $attr_name === 'class') {
                    if (!in_array($attr->value, $allowed_div_classes)) {
                        $element->removeAttribute('class');
                    }
                }
            }
        }
    }
    // Clean up any empty tags that might have resulted from sanitization.
    $elements = $xpath->query('//body//*');
    for ($i = $elements->length - 1; $i >= 0; $i--) {
        $element = $elements->item($i);
        if ($element instanceof DOMElement) {
            $tag_name = strtolower($element->tagName);
            if (!in_array($tag_name, ['br', 'img']) && !$element->hasChildNodes() && trim($element->nodeValue) == '') {
                 if ($element->parentNode) {
                    $element->parentNode->removeChild($element);
                }
            }
        }
    }
    $body = $dom->getElementsByTagName('body')->item(0);
    $sanitized_html = '';
    if ($body && $body->childNodes) {
        foreach ($body->childNodes as $node) {
            $sanitized_html .= $dom->saveHTML($node);
        }
    }
    return trim($sanitized_html);
}
// Generate schema (for SEO purposes)
function generate_comment_schema($comments) {
    if (empty($comments)) {
        return '';
    }
    $schema_comments = [];
    foreach ($comments as $comment) {
        if (stripos($comment['content'], 'This comment has been deleted.') !== false) {
            continue;
        }
        $display_name = $comment['account_display_name'] ? $comment['account_display_name'] : $comment['display_name'];
        $schema_comments[] = [
            '@type' => 'Comment',
            'dateCreated' => date('c', strtotime($comment['submit_date'])),
            'text' => trim(strip_tags(str_replace('<br>', "\n", $comment['content']))),
            'author' => [
                '@type' => 'Person',
                'name' => htmlspecialchars($display_name, ENT_QUOTES)
            ]
        ];
    }
    $aggregate_schema = [
        '@context' => 'https://schema.org',
        '@graph' => $schema_comments
    ];
    $schema_json = json_encode($aggregate_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    return '<script type="application/ld+json">' . $schema_json . '</script>';
}
// The below function will output a comment
function show_comment($comment, $comments = [], $filters = [], $max_comments = -1, $current_nest = 0) {
    static $count = 0;
    $count++;
    if ($max_comments != -1 && $count > $max_comments) return;
    // Display name
    $display_name = $comment['account_display_name'] ? $comment['account_display_name'] : $comment['display_name'];
    // Sanitize content
    $sanitized_content = $comment['content'];
    // Apply word filters
    if ($filters) {
        $sanitized_content = str_ireplace(array_column($filters, 'word'), array_column($filters, 'replacement'), $sanitized_content);
    }
    // Comment template
    $html = '
    <div class="cs-comment" data-id="' . $comment['id'] . '" id="comment-' . $comment['id'] . '">
        <div class="cs-comment-profile-picture">
            ' . ($comment['profile_photo'] && file_exists($comment['profile_photo']) && profile_photos_enabled ? '<img loading="lazy" src="' . comments_url . htmlspecialchars($comment['profile_photo'], ENT_QUOTES) . '" alt="' . htmlspecialchars($display_name, ENT_QUOTES) . '" width="44" height="44">' : '<span style="background-color:' . color_from_string($display_name) . '">' . htmlspecialchars(strtoupper(substr($display_name, 0, 1)), ENT_QUOTES) . '</span>') . '
        </div>
        <div class="cs-con' . (isset($comment['highlighted']) ? ' cs-highlighted' : '') . '">
            ' . (stripos($comment['content'], 'This comment has been deleted.') == false ? '
            <div class="cs-comment-meta">
                <span class="cs-name' . ($comment['banned'] ? ' cs-banned' : '') . '">' . ($comment['website_url'] && profile_websites_enabled ? '<a href="' . htmlspecialchars($comment['website_url'], ENT_QUOTES) . '" target="_blank" rel="noopener noreferrer nofollow">' . htmlspecialchars($display_name, ENT_QUOTES) . '</a>' : htmlspecialchars($display_name, ENT_QUOTES)) . '</span>
                ' . ($comment['role'] && $comment['role'] != 'Member' ? '<span class="cs-role" title="User Role">' . str_replace(['Admin', 'Moderator'], 'Mod', $comment['role']) . '</span>' : '') . '
                <time datetime="' . date('c', strtotime($comment['submit_date'])) . '" class="cs-date" title="' . date('d-m-Y H:ia', strtotime($comment['submit_date'])) . '">' . time_elapsed_string($comment['submit_date']) . '</time>
                ' . ($comment['edited_date'] > $comment['submit_date'] ? '<span class="cs-edited" title="Edited on ' . date('d-m-Y \a\t H:ia', strtotime($comment['edited_date'])) . '">(edited)</span>' : '') . '
                ' . ($comment['featured'] ? '<span class="cs-featured" title="Featured Comment"><svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/></svg></span>' : '') . '
                <a href="#" class="cs-toggle-comment" title="Toggle Comment"><svg class="cs-toggle-minus" width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,13H5V11H19V13Z" /></svg></a>
                <a href="#" class="cs-toggle-comment-menu" title="Toggle Comment Menu"' . (isset($_SESSION['comment_account_loggedin']) && ($_SESSION['comment_account_id'] == $comment['acc_id'] || $_SESSION['comment_account_role'] == 'Admin') ? ' data-can-delete="true"' : '') . (isset($_SESSION['comment_account_loggedin']) && $_SESSION['comment_account_role'] == 'Admin' ? ' data-is-admin="true"' : '') . ($comment['acc_id'] > 0 ? ' data-account-id="' . $comment['acc_id'] . '"' : '') . ($comment['featured'] ? ' data-is-featured="true"' : '') . ($comment['banned'] ? ' data-is-banned="true"' : '') . '><svg width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M16,12A2,2 0 0,1 18,10A2,2 0 0,1 20,12A2,2 0 0,1 18,14A2,2 0 0,1 16,12M10,12A2,2 0 0,1 12,10A2,2 0 0,1 14,12A2,2 0 0,1 12,14A2,2 0 0,1 10,12M4,12A2,2 0 0,1 6,10A2,2 0 0,1 8,12A2,2 0 0,1 6,14A2,2 0 0,1 4,12Z" /></svg></a>
            </div>' : '') . '
            <div class="cs-comment-content">' . $sanitized_content . '</div>
            ' . ($comment['approved'] ? '' : '<div class="cs-comment-awaiting-approval">(Awaiting approval)</div>') . '
            <div class="cs-comment-footer">
                <span class="cs-num" title="Comment Votes">' . number_format($comment['votes']) . '</span>
                <a href="#" class="cs-vote" title="Vote Up" data-vote="up" data-comment-id="' . $comment['id'] . '">
                    <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7.41,15.41L12,10.83L16.59,15.41L18,14L12,8L6,14L7.41,15.41Z" /></svg>
                </a>
                <a href="#" class="cs-vote" title="Vote Down" data-vote="down" data-comment-id="' . $comment['id'] . '">
                    <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" /></svg>
                </a>
                ' . (!authentication_required || isset($_SESSION['comment_account_loggedin']) || login_enabled ? '<a class="cs-reply-comment-btn" href="#" data-comment-id="' . $comment['id'] . '"' . (!isset($_SESSION['comment_account_loggedin']) && authentication_required && login_enabled ? ' data-login-required="true"' : '') . '>Reply</a>' : '') . '
                ' . (isset($_SESSION['comment_account_loggedin']) && (($_SESSION['comment_account_id'] == $comment['acc_id'] && $comment['submit_date'] > date('Y-m-d H:i:s', strtotime('-' . max_comment_edit_time . ' minutes'))) || $_SESSION['comment_account_role'] == 'Admin') ? '<a class="cs-edit-comment-btn" href="#" data-comment-id="' . $comment['id'] . '">Edit</a>' : '') . '
                <a class="cs-share-comment-btn" title="Share Comment" href="#" data-comment-id="' . $comment['id'] . '"><svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M18 16.08C17.24 16.08 16.56 16.38 16.04 16.85L8.91 12.7C8.96 12.47 9 12.24 9 12S8.96 11.53 8.91 11.3L15.96 7.19C16.5 7.69 17.21 8 18 8C19.66 8 21 6.66 21 5S19.66 2 18 2 15 3.34 15 5C15 5.24 15.04 5.47 15.09 5.7L8.04 9.81C7.5 9.31 6.79 9 6 9C4.34 9 3 10.34 3 12S4.34 15 6 15C6.79 15 7.5 14.69 8.04 14.19L15.16 18.34C15.11 18.55 15.08 18.77 15.08 19C15.08 20.61 16.39 21.91 18 21.91S20.92 20.61 20.92 19C20.92 17.39 19.61 16.08 18 16.08M18 4C18.55 4 19 4.45 19 5S18.55 6 18 6 17 5.55 17 5 17.45 4 18 4M6 13C5.45 13 5 12.55 5 12S5.45 11 6 11 7 11.45 7 12 6.55 13 6 13M18 20C17.45 20 17 19.55 17 19S17.45 18 18 18 19 18.45 19 19 18.55 20 18 20Z" /></svg></a>
            </div>
            <div class="cs-replies">' . ($current_nest < max_nested_replies ? show_comments($comments, $filters, $max_comments, $comment['id'], $current_nest+1) : '') . '</div>
        </div>
    </div>
    ' . ($current_nest >= max_nested_replies ? show_comments($comments, $filters, $max_comments, $comment['id'], $current_nest+1) : '');
    return $html;
}
// Output an array of comments
function show_comments($comments, $filters, $max_comments = -1, $parent_id = -1, $current_nest = 0) {
    $html = '';
    if ($parent_id != -1) {
        array_multisort(array_column($comments, 'submit_date'), SORT_ASC, $comments);
    }
    foreach ($comments as $comment) {
        if ($comment['parent_id'] == $parent_id) {
            $html .= show_comment($comment, $comments, $filters, $max_comments, $current_nest);
        }
    }
    return $html;
}
// Output the write comment form
function show_write_comment_form($editor) {
    // Input for the user's name is authentication isn't required
    $input_html = '';
    if (!authentication_required && !isset($_SESSION['comment_account_loggedin'])) {
        $input_html = '<input type="text" name="name" id="name" placeholder="Your Name">';    
    }
    // Conditionally create the editor area
    $editor_html = '';
    if ($editor === 'quill') {
        $editor_html = '
            <div class="cs-wysiwyg-editor" data-maxlength="' . max_comment_chars . '"></div>
            <textarea name="content" class="cs-hidden-textarea" minlength="' . min_comment_chars . '" maxlength="' . max_comment_chars . '"></textarea>
            <div class="cs-toolbar">
                <button title="Bold" class="ql-bold cs-format-btn" data-command="wrap" data-value="strong"></button>
                <button title="Italic" class="ql-italic cs-format-btn" data-command="wrap" data-value="em"></button>
                <button title="Underline" class="ql-underline cs-format-btn" data-command="wrap" data-value="u"></button>
                <button title="Strikethrough" class="ql-strike cs-format-btn" data-command="wrap" data-value="s"></button>
                <button title="Link" class="ql-link cs-format-btn" data-command="link"></button>
                <button title="Code Block" class="ql-code-block cs-format-btn" data-command="wrap" data-value="pre"></button>
                <button title="Quote" class="ql-blockquote cs-format-btn" data-command="wrap" data-value="blockquote"></button>
                ' . (images_enabled ? '<button title="Image" class="ql-image cs-format-btn" data-command="image"></button>' : '') . '
            </div>
        ';
    } else { 
        // Fallback to standard textarea
        $editor_html = '
            <textarea name="content" class="cs-manual-textarea" placeholder="Add to the discussion..." required minlength="' . min_comment_chars . '" maxlength="' . max_comment_chars . '"></textarea>
            <div class="cs-toolbar cs-toolbar-standard">
                <button title="Bold" class="ql-bold cs-format-btn" data-command="wrap" data-value="strong"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.5,15.5H10V12.5H13.5A1.5,1.5 0 0,1 15,14A1.5,1.5 0 0,1 13.5,15.5M10,6.5H13A1.5,1.5 0 0,1 14.5,8A1.5,1.5 0 0,1 13,9.5H10M15.6,10.79C16.57,10.11 17.25,9 17.25,8C17.25,5.74 15.5,4 13.25,4H7V18H14.04C16.14,18 17.75,16.3 17.75,14.21C17.75,12.69 16.89,11.39 15.6,10.79Z" /></svg></button>
                <button title="Italic" class="ql-italic cs-format-btn" data-command="wrap" data-value="em"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10,4V7H12.21L8.79,15H6V18H14V15H11.79L15.21,7H18V4H10Z" /></svg></button>
                <button title="Underline" class="ql-underline cs-format-btn" data-command="wrap" data-value="u"><svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5,21H19V19H5V21M12,17A6,6 0 0,0 18,11V3H15.5V11A3.5,3.5 0 0,1 12,14.5A3.5,3.5 0 0,1 8.5,11V3H6V11A6,6 0 0,0 12,17Z" /></svg></button>
                <button title="Strikethrough" class="ql-strike cs-format-btn" data-command="wrap" data-value="s"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7.2 9.8C6 7.5 7.7 4.8 10.1 4.3C13.2 3.3 17.7 4.7 17.6 8.5H14.6C14.6 8.2 14.5 7.9 14.5 7.7C14.3 7.1 13.9 6.8 13.3 6.6C12.5 6.3 11.2 6.4 10.5 6.9C9 8.2 10.4 9.5 12 10H7.4C7.3 9.9 7.3 9.8 7.2 9.8M21 13V11H3V13H12.6C12.8 13.1 13 13.1 13.2 13.2C13.8 13.5 14.3 13.7 14.5 14.3C14.6 14.7 14.7 15.2 14.5 15.6C14.3 16.1 13.9 16.3 13.4 16.5C11.6 17 9.4 16.3 9.5 14.1H6.5C6.4 16.7 8.6 18.5 11 18.8C14.8 19.6 19.3 17.2 17.3 12.9L21 13Z" /></svg></button>
                <button title="Link" class="ql-link cs-format-btn" data-command="link" data-value="a"><svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M10.59,13.41C11,13.8 11,14.44 10.59,14.83C10.2,15.22 9.56,15.22 9.17,14.83C7.22,12.88 7.22,9.71 9.17,7.76V7.76L12.71,4.22C14.66,2.27 17.83,2.27 19.78,4.22C21.73,6.17 21.73,9.34 19.78,11.29L18.29,12.78C18.3,11.96 18.17,11.14 17.89,10.36L18.36,9.88C19.54,8.71 19.54,6.81 18.36,5.64C17.19,4.46 15.29,4.46 14.12,5.64L10.59,9.17C9.41,10.34 9.41,12.24 10.59,13.41M13.41,9.17C13.8,8.78 14.44,8.78 14.83,9.17C16.78,11.12 16.78,14.29 14.83,16.24V16.24L11.29,19.78C9.34,21.73 6.17,21.73 4.22,19.78C2.27,17.83 2.27,14.66 4.22,12.71L5.71,11.22C5.7,12.04 5.83,12.86 6.11,13.65L5.64,14.12C4.46,15.29 4.46,17.19 5.64,18.36C6.81,19.54 8.71,19.54 9.88,18.36L13.41,14.83C14.59,13.66 14.59,11.76 13.41,10.59C13,10.2 13,9.56 13.41,9.17Z" /></svg></button>
                <button title="Code Block" class="ql-code-block cs-format-btn" data-command="wrap" data-value="pre"><svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12.89,3L14.85,3.4L11.11,21L9.15,20.6L12.89,3M19.59,12L16,8.41V5.58L22.42,12L16,18.41V15.58L19.59,12M1.58,12L8,5.58V8.41L4.41,12L8,15.58V18.41L1.58,12Z" /></svg></button>
                <button title="Blockquote" class="ql-blockquote cs-format-btn" data-command="wrap" data-value="blockquote"><svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14,17H17L19,13V7H13V13H16M6,17H9L11,13V7H5V13H8L6,17Z" /></svg></button>
                ' . (images_enabled ? '<button title="Image" class="ql-image cs-format-btn" data-command="wrap" data-value="img"><svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,19H5V5H19M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M13.96,12.29L11.21,15.83L9.25,13.47L6.5,17H17.5L13.96,12.29Z" /></svg></button>' : '') . '
            </div>
        ';
    }
    $html = '
    <div class="cs-write-comment cs-hidden" data-comment-id="-1" data-editor="' . $editor . '">
        <form>
            <input name="parent_id" type="hidden" value="-1">
            <input name="comment_id" type="hidden" value="-1">
            ' . $input_html . '
            <div class="cs-content">
            <div class="cs-char-counter">0 / ' . max_comment_chars . '</div>
            ' . $editor_html . '
            </div>
            <p class="cs-msg"></p>
            <div class="cs-group">
                <button type="submit" class="cs-post-button cs-button">Comment</button>
                <button type="button" class="cs-cancel-button cs-button cs-alt">Cancel</button>
                <span class="cs-loader cs-hidden"></span>
            </div>
        </form>
    </div>
    ';
    return $html;
}
// Highlight comment function (when a comment is shared, it will be highlighted)
function highlight_comment($comments, $comment_id, $highlighted = true) {
    foreach ($comments as $i => $comment) {
        if ($comment['id'] == $comment_id) {
            $highlighted_comment = $comment;
            if ($highlighted) {
                $highlighted_comment['highlighted'] = true;
            }
            unset($comments[$i]);
            array_unshift($comments, $highlighted_comment);
            if ($comment['parent_id'] != -1) {
                $comments = highlight_comment($comments, $comment['parent_id'], false);
            }
            break;
        }
    }
    return $comments;
}
// Update featured / pinned comments function
function update_featured_comments($comments) {
    foreach ($comments as $i => $comment) {
        if ($comment['featured']) {
            $featured_comment = $comment;
            $featured_comment['top_parent_id'] = $comment['id'];
            $featured_comment['parent_id'] = -1;
            unset($comments[$i]);
            array_unshift($comments, $featured_comment);
        }
    }
    return $comments;
}
// Send mail function
function send_mail($to, $subject, $body, $from = mail_from, $from_name = mail_name) {
    if (mail_enabled) {
        // Include PHPMailer library
        require_once 'lib/phpmailer/Exception.php';
        require_once 'lib/phpmailer/PHPMailer.php';
        require_once 'lib/phpmailer/SMTP.php';
        // Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
        try {
            // SMTP Server settings
            if (SMTP) {
                $mail->isSMTP();
                $mail->Host = smtp_host;
                $mail->SMTPAuth = empty(smtp_user) && empty(smtp_pass) ? false : true;
                $mail->Username = smtp_user;
                $mail->Password = smtp_pass;
                $mail->SMTPSecure = smtp_secure == 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = smtp_port;
            }
            // Recipients
            $mail->setFrom($from, $from_name);
            $mail->addAddress($to);
            $mail->addReplyTo($from, $from_name);
            // Content
            $mail->isHTML(true);
            // Subject
            $mail->Subject = $subject;
            // Body
            $body = '<!DOCTYPE html><html><head><title>' . $subject . '</title><meta charset="utf-8"><meta name="viewport" content="width=device-width,minimum-scale=1"></head><body style="margin:0;padding:0;">' . $body . '</body></html>';
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);
            // Set charset
            $mail->CharSet = 'UTF-8';
            // Send mail
            $mail->send();
        } catch (Exception $e) {
            // Output error message
            exit('Error: Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
        }
    }
}
// Handle logout
if (isset($_GET['method']) && $_GET['method'] == 'logout') {
    session_destroy();
    exit('success');
}
// Authenticate user
if (isset($_GET['method'], $_POST['email'], $_POST['password']) && $_GET['method'] == 'login') {
    // Check if login is enabled
    if (!login_enabled) {
        exit('Error: Login is disabled!');
    }
    // Retrieve the account
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ?');
    $stmt->execute([ $_POST['email'] ]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC); 
    // Account exists + verify password
    if ($account) {
        // Verify password
        if ($account['banned']) {
            exit('Error: You cannot login right now!');
        } else if (password_verify($_POST['password'], $account['password'])) {
            session_regenerate_id();
            $_SESSION['comment_account_loggedin'] = TRUE;
            $_SESSION['comment_account_id'] = $account['id'];
            $_SESSION['comment_account_display_name'] = $account['display_name'];
            $_SESSION['comment_account_role'] = $account['role']; 
            $_SESSION['comment_account_email'] = $account['email'];
            exit('success');
        } else {
            exit('Error: Incorrect email and/or password!');
        }
    } else {
        exit('Error: Incorrect email and/or password!');
    }
}
// Register user
if (isset($_GET['method'], $_POST['email'], $_POST['password'], $_POST['cpassword'], $_POST['name']) && $_GET['method'] == 'register') {
    // Check if register is enabled
    if (!register_enabled) {
        exit('Error: Register is disabled!');
    }
    // Check if email is already registered
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ?');
    $stmt->execute([ $_POST['email'] ]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC); 
    if ($account) {
        exit('Error: Email is already registered!');
    }
    // Check if password is valid
    if (strlen($_POST['password']) < 6 || strlen($_POST['password']) > 20) {
        exit('Error: Password must be between 6 and 20 characters long!');
    }
    if ($_POST['cpassword'] != $_POST['password']) {
        exit('Error: Passwords do not match!');
    }
    // Check if display name is valid
    if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 20) {
        exit('Error: Display name must be between 3 and 20 characters long!');
    }
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $_POST['name'])) {
        exit('Error: Display name must contain only letters and numbers!');
    }
    // Check if email is valid
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        exit('Error: Email is invalid!');
    }
    // Create account
    $stmt = $pdo->prepare('INSERT INTO accounts (email, `password`, display_name, `role`, registered) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([ $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['name'], 'Member', date('Y-m-d\TH:i:s') ]);
    // Authenticate user
    session_regenerate_id();
    $_SESSION['comment_account_loggedin'] = TRUE;
    $_SESSION['comment_account_id'] = $pdo->lastInsertId();
    $_SESSION['comment_account_display_name'] = $_POST['name'];
    $_SESSION['comment_account_role'] = 'Member';
    $_SESSION['comment_account_email'] = $_POST['email'];
    // Output success
    exit('success');
}
// Edit profile
if (isset($_GET['method'], $_POST['email'], $_POST['password'], $_POST['cpassword'], $_POST['name']) && $_GET['method'] == 'edit_profile') {
    // Check if email is already registered
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE email = ? AND id != ?');
    $stmt->execute([ $_POST['email'], $_SESSION['comment_account_id'] ]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC); 
    if ($account) {
        exit('Error: Email is already registered!');
    }
    // Get password hash
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([ $_SESSION['comment_account_id'] ]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    $password_hash = $account['password'];
    $photo_url = $account['profile_photo'];
    // Check if password is valid
    if (!empty($_POST['password']) && (strlen($_POST['password']) < 6 || strlen($_POST['password']) > 20)) {
        exit('Error: Password must be between 6 and 20 characters long!');
    }
    if (!empty($_POST['password']) && ($_POST['cpassword'] != $_POST['password'])) {
        exit('Error: Passwords do not match!');
    }
    // Check if display name is valid
    if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 20) {
        exit('Error: Display name must be between 3 and 20 characters long!');
    }
    if (!preg_match('/^[a-zA-Z0-9\s]+$/', $_POST['name'])) {
        exit('Error: Display name must contain only letters and numbers!');
    }
    // Check if email is valid
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        exit('Error: Email is invalid!');
    }
    // Make sure website URL is valid if provided
    if (isset($_POST['website']) && !empty($_POST['website']) && !filter_var($_POST['website'], FILTER_VALIDATE_URL) && profile_websites_enabled) {
        exit('Error: Website URL is invalid!');
    }
    // Check if user uploaded photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0 && profile_photos_enabled) {
        // Get file info
        $file_name = $_FILES['photo']['name'];
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_type = $_FILES['photo']['type'];
        $file_size = $_FILES['photo']['size'];
        // Get file extension
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        // Check if file is an image
        if (getimagesize($file_tmp) === false) {
            exit('Error: File is not an image!');
        }
        // Check file size
        if ($file_size > 2097152) { // 2 MB
            exit('Error: File size is too large! (2MB max)');
        }
        // Check file type
        if ($file_type != 'image/jpeg' && $file_type != 'image/png' && $file_type != 'image/gif') {
            exit('Error: Please upload a JPEG, PNG or GIF file!');
        }
        // Generate random file name
        $file_name = uniqid() . '.' . $file_ext;
        // Upload file
        move_uploaded_file($file_tmp, 'uploads/' . $file_name);
        // Delete old photo
        if ($photo_url != '') {
            unlink($photo_url);
        }
        // Update photo URL
        $photo_url = 'uploads/' . $file_name;
    }
    // Only update password if it is not empty
    $password = empty($_POST['password']) ? $password_hash : password_hash($_POST['password'], PASSWORD_DEFAULT);
    // Update account
    $stmt = $pdo->prepare('UPDATE accounts SET email = ?, password = ?, display_name = ?, website_url = ?, profile_photo = ? WHERE id = ?');
    $stmt->execute([ $_POST['email'], $password, $_POST['name'], $_POST['website'], $photo_url, $_SESSION['comment_account_id'] ]);
    // Update session variables
    $_SESSION['comment_account_display_name'] = $_POST['name'];
    $_SESSION['comment_account_email'] = $_POST['email'];
    // Output success
    exit('success');
}
// Delete comment
if (isset($_GET['delete_comment'])) {
    // Retrieve the comment
    $stmt = $pdo->prepare('SELECT acc_id FROM comments WHERE id = ?');
    $stmt->execute([ $_GET['delete_comment'] ]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the user is the owner of the comment or an admin
    if (isset($_SESSION['comment_account_loggedin']) && ($_SESSION['comment_account_id'] == $comment['acc_id'] || $_SESSION['comment_account_role'] == 'Admin')) {
        // Update the comment content
        $stmt = $pdo->prepare('UPDATE comments SET content = ? WHERE id = ?');
        $stmt->execute([ '<p><em>This comment has been deleted.</em></p>', $_GET['delete_comment'] ]);
        // Or you can delete the comment completely but it's not ideal if the comment has replies
        // $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ?');
        // $stmt->execute([ $_GET['delete_comment'] ]);
        exit('success');
    }
    exit('error');
}
// Report comment
if (isset($_POST['report_comment'])) {
    // Validate reason
    if (strlen($_POST['reason']) < 3 || strlen($_POST['reason']) > 150) {
        exit('Error: Reason must be between 3 and 150 characters long!');
    }
    // Retrieve the comment
    $stmt = $pdo->prepare('SELECT acc_id FROM comments WHERE id = ?');
    $stmt->execute([ $_POST['report_comment'] ]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the user is the owner of the comment or an admin
    if ($comment) {
        // Report the comment
        $acc_id = isset($_SESSION['comment_account_id']) ? $_SESSION['comment_account_id'] : NULL;
        $stmt = $pdo->prepare('INSERT INTO comment_reports (comment_id, acc_id, reason) VALUES (?, ?, ?)');
        $stmt->execute([ $_POST['report_comment'], $acc_id, $_POST['reason'] ]);
        exit('success');
    }
    exit('Error: Comment not found!');
}
// Feature comment
if (isset($_GET['feature_comment'], $_SESSION['comment_account_role']) && $_SESSION['comment_account_role'] == 'Admin') {
    // Retrieve the comment
    $stmt = $pdo->prepare('SELECT * FROM comments WHERE id = ?');
    $stmt->execute([ $_GET['feature_comment'] ]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the user is the owner of the comment or an admin
    if ($comment) {
        // Feature the comment
        $stmt = $pdo->prepare('UPDATE comments SET featured = ? WHERE id = ?');
        $stmt->execute([ $comment['featured'] ? 0 : 1, $_GET['feature_comment'] ]);
        exit('success');
    }
    exit('Error: Comment not found!');
}
// Ban user
if (isset($_GET['method'], $_GET['acc_id'], $_GET['delete_all_comments'], $_SESSION['comment_account_loggedin']) && $_GET['method'] == 'ban_user' && $_SESSION['comment_account_role'] == 'Admin') {
    // Get account
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([ $_GET['acc_id'] ]);
    $acc = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($acc) {
         // Ban the user
        $stmt = $pdo->prepare('UPDATE accounts SET banned = ? WHERE id = ?');
        $stmt->execute([ $acc['banned'] ? 0 : 1, $_GET['acc_id'] ]);
        // Delete all comments by the user
        if ($_GET['delete_all_comments']) {
            $stmt = $pdo->prepare('DELETE FROM comments WHERE acc_id = ?');
            $stmt->execute([ $_GET['acc_id'] ]);
        }
        exit('success');   
    }
    exit('Error: User not found!');
}
// IF the user clicks one of the vote buttons
if (isset($_GET['vote'], $_GET['comment_id'])) {
    // Check if the cookie exists for the specified comment
    if (!isset($_COOKIE['vote_' . $_GET['comment_id']])) {
        // Cookie does not exists, update the votes column and increment/decrement the value
        $stmt = $pdo->prepare('UPDATE comments SET votes = votes ' . ($_GET['vote'] == 'up' ? '+' : '-') . ' 1 WHERE id = ?');
        $stmt->execute([ $_GET['comment_id'] ]);
        // Set vote cookie, this will prevent the users from voting multiple times on the same comment, cookie expires in 10 years
        setcookie('vote_' . $_GET['comment_id'], 'true', time() + (10 * 365 * 24 * 60 * 60), '/');
    }
    // Retrieve the number of votes from the comments table
    $stmt = $pdo->prepare('SELECT votes FROM comments WHERE id = ?');
    $stmt->execute([ $_GET['comment_id'] ]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    // Output the votes
    echo $comment['votes'];
    exit;
}
// Retrieve the filters
$filters = $pdo->query('SELECT * FROM comment_filters')->fetchAll(PDO::FETCH_ASSOC);
// Get the editor (standard or quill)
$editor = isset($_GET['editor']) ? $_GET['editor'] : 'standard';
// Get page info
$stmt = $pdo->prepare('SELECT * FROM comment_page_details WHERE page_id = ?');
$stmt->execute([ $_GET['page_id'] ]);
$comment_page_info = $stmt->fetch(PDO::FETCH_ASSOC);
// IF the user submits the write comment form
if (isset($_POST['content'], $_POST['parent_id'], $_POST['comment_id'])) {
    // Check comment cooldown
    if (comment_cooldown_time > 0) {
        if (isset($_SESSION['comment_last_comment_time']) && (time() - $_SESSION['comment_last_comment_time']) < comment_cooldown_time) {
            exit('Error: You must wait ' . comment_cooldown_time . ' seconds before posting another comment!');
        } else {
            $_SESSION['comment_last_comment_time'] = time();
        }
    }
    // If editor is standard, convert textarea new lines to br tags
    if (isset($editor) && $editor == 'standard') {
        $_POST['content'] = str_replace(PHP_EOL, '<br class="cs-newline">', $_POST['content']);
    }
    // Sanitize the comment
    $content = sanitize_comment_html($_POST['content'], $editor);
    // Validation
    if (strlen(strip_tags($content)) > max_comment_chars) {
        exit('Error: comment must be no longer than ' . max_comment_chars . ' characters long!');
    }
    if (strlen(strip_tags($content)) < min_comment_chars) {
        exit('Error: comment must be at least ' . min_comment_chars . ' characters long!');
    }
    // Display name must contain only characters and numbers.
    if (isset($_POST['name']) && !empty($_POST['name']) && !preg_match('/^[a-zA-Z0-9\s]+$/', $_POST['name'])) {
        exit('Error: Display name must contain only letters and numbers!');
    }
    // Name must be between 3 and 20 characters long.
    if (isset($_POST['name']) && !empty($_POST['name']) && (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 20)) {
        exit('Error: Display name must be between 3 and 20 characters long!');
    }
    // Check if authentication required
    if (authentication_required && !isset($_SESSION['comment_account_loggedin'])) {
        exit('Error: Please login to post a comment!');    
    }
    // Check if page is locked
    if (isset($comment_page_info['page_status']) && intval($comment_page_info['page_status']) == 0) {
        exit('Error: Page is locked!');
    }
    // Declare comment variables
    $approved = comments_approval_level == 0 ? 1 : 0;
    $approved = comments_approval_level == 1 && isset($_SESSION['comment_account_loggedin']) ? 1 : $approved;
    $approved = isset($_SESSION['comment_account_loggedin']) && $_SESSION['comment_account_role'] == 'Admin' ? 1 : $approved;
    $acc_id = isset($_SESSION['comment_account_loggedin']) ? $_SESSION['comment_account_id'] : -1; 
    $name = isset($_SESSION['comment_account_display_name']) ? $_SESSION['comment_account_display_name'] : 'Anonymous'; 
    $name = isset($_POST['name']) && !empty($_POST['name']) ? $_POST['name'] : $name;
    // IF the comment ID is not -1, update the comment
    if ($_POST['comment_id'] != -1 && isset($_SESSION['comment_account_loggedin'])) {
        // Update comment
        if ($_SESSION['comment_account_role'] == 'Admin') {
            $stmt = $pdo->prepare('UPDATE comments SET content = ?, edited_date = ? WHERE id = ?');
            $stmt->execute([ $content, date('Y-m-d H:i:s'), $_POST['comment_id'] ]);
        } else {
            $stmt = $pdo->prepare('UPDATE comments SET content = ?, edited_date = ? WHERE id = ? AND acc_id = ? AND submit_date > ?');
            $stmt->execute([ $content, date('Y-m-d H:i:s'), $_POST['comment_id'], $_SESSION['comment_account_id'], date('Y-m-d H:i:s', strtotime('-' . max_comment_edit_time . ' minutes')) ]);
        }
        $id = $_POST['comment_id'];
    } else {
        $parent_id = $_POST['parent_id'];
        $top_parent_id = 0;
        // If this is a reply, find the top_parent_id of its parent
        if ($parent_id != -1) {
            $stmt = $pdo->prepare('SELECT top_parent_id FROM comments WHERE id = ?');
            $stmt->execute([ $parent_id ]);
            $top_parent_id = $stmt->fetchColumn();
        }
        // Insert a new comment
        $stmt = $pdo->prepare('INSERT INTO comments (page_id, parent_id, display_name, content, submit_date, approved, acc_id, top_parent_id) VALUES (?,?,?,?,?,?,?,?)');
        $stmt->execute([ $_GET['page_id'], $parent_id, $name, $content, date('Y-m-d H:i:s'), $approved, $acc_id, $top_parent_id ]);
        $id = $pdo->lastInsertId();
        // If this was a new top-level comment, its top_parent_id is its own id
        if ($parent_id == -1) {
            $stmt = $pdo->prepare('UPDATE comments SET top_parent_id = ? WHERE id = ?');
            $stmt->execute([ $id, $id ]);
        }
    }
    // Retrieve the comment
    $stmt = $pdo->prepare('SELECT c.*, a.role FROM comments c LEFT JOIN accounts a ON a.id = c.acc_id WHERE c.id = ?');
    $stmt->execute([ $id ]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    // Determine the email associated with the comment (if any)
    $email = isset($_SESSION['comment_account_loggedin']) ? $_SESSION['comment_account_email'] : '--';
    // If not approved, add approve button
    $approve_btn_html = '';
    if (!$comment['approved']) {
        $approve_btn_html = '<a href="' . comments_url . 'admin/comments.php?approve=' . $id . '" style="display: inline-block; margin-top: 30px; margin-left: 10px; padding: 10px 20px; background-color: #30c56eff; color: #fff; text-decoration: none; font-size: 14px; font-weight: 500; border-radius: 4px;">Approve</a>';
    }
    // Read the template contents and replace the placeholders with the above variables
    $email_template = str_replace(['%name%','%email%','%date%','%page_id%','%comment%','%url%','%title%','%btn_text%', '%approve_btn%'], [$name, $email, date('Y-m-d H:i:s'), $_GET['page_id'], nl2br(strip_tags($content)), comments_url . 'admin/comment.php?id=' . $id, 'A new comment has been posted!', 'Manage Comment', $approve_btn_html], file_get_contents('notification-email-template.html'));
    // Send notfiication email to the admin
    send_mail(notification_email, 'A new comment has been posted!', $email_template);
    // Send reply email to the parent comment author if this is a reply
    if ($_POST['parent_id'] != -1) {
        // Retrieve the parent comment
        $stmt = $pdo->prepare('SELECT c.*, a.role, a.email, pd.url FROM comments c JOIN accounts a ON a.id = c.acc_id LEFT JOIN comment_page_details pd ON pd.page_id = c.page_id WHERE c.id = ? AND c.acc_id != ?');
        $stmt->execute([ $_POST['parent_id'], $acc_id ]); // No need to send email to the author of the comment itself - adding the $acc_id will prevent that
        $parent_comment = $stmt->fetch(PDO::FETCH_ASSOC);
        // Send email to the parent comment author
        if ($parent_comment) {
            // If no URL is set, use the referrer URL
            $parent_comment['url'] = isset($parent_comment['url']) && !empty($parent_comment['url']) ? $parent_comment['url'] : $_SERVER['HTTP_REFERER'];
            // Read the template contents and replace the placeholders with the above variables
            $email_template = str_replace(['%name%','%email%','%date%','%page_id%','%comment%','%url%','%title%','%btn_text%'], [$parent_comment['display_name'], $parent_comment['email'], date('Y-m-d H:i:s'), $_GET['page_id'], nl2br(strip_tags($content)), $parent_comment['url'] . '#comment-' . $parent_comment['id'], 'Someone replied to your comment!', 'View Comment'], file_get_contents('notification-email-template.html'));
            // Send notification email to the parent comment author
            send_mail($parent_comment['email'], 'Someone replied to your comment!', $email_template);
        }
    }
    // If the comment is not approved, return an error message
    if (!$comment['approved']) {
        exit('Note: Your comment has been submitted for approval and will be visible once approved by an admin.');
    }
    // Output newly added comment ID
    exit('comment-' . $id);
}
// This is the TOTAL number of comments (parents+children) you want to show
$comments_per_page = isset($_GET['comments_to_show']) && is_numeric($_GET['comments_to_show']) ? abs(intval($_GET['comments_to_show'])) : comments_per_page;
// Search comments
if (isset($_GET['method'], $_GET['query']) && $_GET['method'] == 'search' && search_enabled) {
    // Retrieve the comments
    $query = htmlspecialchars($_GET['query'], ENT_QUOTES);
    $stmt = $pdo->prepare('SELECT c.*, a.role, a.display_name AS account_display_name, a.profile_photo, a.banned, a.website_url FROM comments c LEFT JOIN accounts a ON a.id = c.acc_id WHERE c.page_id = ? AND c.approved = 1 AND (c.content LIKE ? OR c.display_name LIKE ?) ORDER BY c.submit_date DESC');
    $stmt->execute([ $_GET['page_id'], '%' . $query . '%', '%' . $query . '%' ]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    exit(show_comments($comments, $filters, $comments_per_page));
}
// By default, order by the submit data (newest)
$sort_by = 'ORDER BY c.votes DESC, c.submit_date DESC';
if (isset($_GET['sort_by'])) {
    // User has changed the sort by, update the sort_by variable
    $sort_by = $_GET['sort_by'] == 'newest' ? 'ORDER BY c.submit_date DESC' : $sort_by;
    $sort_by = $_GET['sort_by'] == 'oldest' ? 'ORDER BY c.submit_date ASC' : $sort_by;
    $sort_by = $_GET['sort_by'] == 'votes' ? 'ORDER BY c.votes DESC, c.submit_date DESC' : $sort_by;
}
// Get a limited number of top-level parent IDs for the first page
$stmt = $pdo->prepare('SELECT id FROM comments WHERE page_id = :page_id AND parent_id = -1 AND approved = 1 ' . str_replace('c.','', $sort_by) . (comments_per_page != -1 ? ' LIMIT :limit' : ''));
$stmt->bindValue(':page_id', $_GET['page_id'], PDO::PARAM_INT);
if (comments_per_page != -1) $stmt->bindValue(':limit', $comments_per_page, PDO::PARAM_INT);
$stmt->execute();
$top_level_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
// Comments array
$comments = [];
// Get child comments based on the top-level parent IDs
if (!empty($top_level_ids)) {
    $stmt = $pdo->prepare('SELECT top_parent_id FROM comments WHERE page_id = ? AND featured = 1 AND approved = 1 ' . str_replace('c.','', $sort_by));
    $stmt->execute([ $_GET['page_id'] ]);
    $featured_comments = $stmt->fetchAll(PDO::FETCH_COLUMN);
    // Prepend the featured comments
    $final_top_level_ids = array_unique(array_merge($featured_comments, $top_level_ids));
    // The IN clause is very fast because top_parent_id is indexed.
    $in_placeholders = implode(',', array_fill(0, count($final_top_level_ids), '?'));
    $stmt = $pdo->prepare('SELECT c.*, a.role, a.display_name AS account_display_name, a.profile_photo, a.banned, a.website_url FROM comments c LEFT JOIN accounts a ON a.id = c.acc_id WHERE c.top_parent_id IN (' . $in_placeholders . ') AND c.approved = 1 ' . $sort_by);
    $stmt->execute(array_values($final_top_level_ids));
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Push featured comments to the top
    $comments = update_featured_comments($comments);
}
// Highlight comment (when comment has been shared, this will handle the share URL)
if (isset($_GET['highlight_comment'])) {
    // Check if comment and page id match in the database
    $stmt = $pdo->prepare('SELECT * FROM comments WHERE id = ? AND page_id = ?');
    $stmt->execute([ $_GET['highlight_comment'], $_GET['page_id'] ]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($comment) {
        // Get all comments with the same top_parent_id
        $stmt = $pdo->prepare('SELECT c.*, a.role, a.display_name AS account_display_name, a.profile_photo, a.banned, a.website_url FROM comments c LEFT JOIN accounts a ON a.id = c.acc_id WHERE c.top_parent_id = ? AND c.page_id = ? AND c.approved = 1');
        $stmt->execute([ $comment['top_parent_id'], $_GET['page_id'] ]);
        $highlighted_comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Combine the comments into one array and remove duplicates
        $comments = array_unique(array_merge($comments, $highlighted_comments), SORT_REGULAR);
        // Highlight the comment
        $comments = highlight_comment($comments, $_GET['highlight_comment']);   
    }
}
// Get the total number of all comments for the page
$stmt = $pdo->prepare('SELECT COUNT(*) AS total_comments FROM comments WHERE page_id = ? AND approved = 1');
$stmt->execute([ $_GET['page_id'] ]);
$comments_info = $stmt->fetch(PDO::FETCH_ASSOC);
// If no page exists, create one
if (!$comment_page_info) {
    // Get the URL
    $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    // no page, exists so create it
    $stmt = $pdo->prepare('INSERT INTO comment_page_details (page_id, title, `description`, `url`) VALUES (?, ?, ?, ?)');
    $stmt->execute([ $_GET['page_id'], 'Page ' . $_GET['page_id'], '', $url ]);
    // Get page info
    $stmt = $pdo->prepare('SELECT * FROM comment_page_details WHERE page_id = ?');
    $stmt->execute([ $_GET['page_id'] ]);
    $comment_page_info = $stmt->fetch(PDO::FETCH_ASSOC);
}
// If logged in, retrieve account details
if (isset($_SESSION['comment_account_loggedin'])) {
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([ $_SESSION['comment_account_id'] ]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$account) {
        // If account does not exist, log out
        session_destroy();
        exit('Error: Account does not exist!');
    }
    if ($account['banned']) {
        // If account is banned, log out
        session_destroy();
        exit('Error: You cannot login right now!');
    }
}
?>
<div class="cs-comment-header" data-max-comments="<?=$comments_per_page?>" data-page-status="<?=$comment_page_info['page_status']?>">
    <span class="cs-total"><?=number_format($comments_info['total_comments'])?> Comment<?=$comments_info['total_comments'] == 1 ? '' : 's'?></span>
    <div class="cs-comment-btns">
        <?php if (!isset($_SESSION['comment_account_loggedin']) && login_enabled): ?>
        <a href="#" class="cs-login-btn">
            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M352 96l64 0c17.7 0 32 14.3 32 32l0 256c0 17.7-14.3 32-32 32l-64 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l64 0c53 0 96-43 96-96l0-256c0-53-43-96-96-96l-64 0c-17.7 0-32 14.3-32 32s14.3 32 32 32zm-9.4 182.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L242.7 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z"/></svg>
            Login
        </a>
        <?php endif; ?>
    </div>
    <div class="cs-sort-by">
        <a href="#">Sort by <span><?=isset($_GET['sort_by']) ? htmlspecialchars(ucwords($_GET['sort_by']), ENT_QUOTES) : 'Votes'?></span><svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/></svg></a>
        <div class="cs-options">
            <a href="#" data-value="votes">Votes</a>
            <a href="#" data-value="newest">Newest</a>
            <a href="#" data-value="oldest">Oldest</a>
        </div>
    </div>
    <?php if (search_enabled): ?>
    <a href="#" title="Search Comments" class="cs-search-btn<?=$comments_per_page == -1 || $comments_per_page > $comments_info['total_comments'] ? ' cs-search-local' : '' ?>">
        <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
    </a>
    <?php endif; ?>
    <?php if (isset($_SESSION['comment_account_loggedin'])): ?>
    <div class="cs-profile-info">
        <?php if ($account['profile_photo'] && file_exists($account['profile_photo']) && profile_photos_enabled): ?>
        <img loading="lazy" class="cs-profile-photo" src="<?=comments_url . $account['profile_photo']?>" width="32" height="32" alt="<?=htmlspecialchars($account['display_name'], ENT_QUOTES)?>">
        <?php else: ?>
        <span class="cs-profile-photo" style="background-color:<?=color_from_string($account['display_name'])?>"><?=htmlspecialchars(strtoupper(substr($account['display_name'], 0, 1)), ENT_QUOTES)?></span>
        <?php endif; ?>
        <div class="cs-options">
            <?php if ($account['role'] == 'Admin'): ?>
            <a href="admin/index.php">Admin Panel</a>
            <?php endif; ?>
            <a href="#" data-action="edit">Edit Profile</a>
            <a href="#" data-action="logout" class="cs-alt">Logout</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="cs-comment-content">
    <?php if (search_enabled): ?>
    <div class="cs-comment-search">
        <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
        <input type="text" placeholder="Search" data-comment-id="-1">
    </div>
    <?php endif; ?>
    <?php if ($comment_page_info['page_status'] && (!authentication_required || isset($_SESSION['comment_account_loggedin']) || login_enabled)): ?>
    <input type="text" placeholder="Add to the discussion..." class="cs-comment-placeholder-content" data-comment-id="-1"<?=(!isset($_SESSION['comment_account_loggedin']) && authentication_required && login_enabled ? ' data-login-required="true"' : '')?>>
    <?php endif; ?>
</div>

<?=show_write_comment_form($editor)?>

<div class="cs-comments-wrapper">
    <?=show_comments($comments, $filters, $comments_per_page)?>
</div>

<?php if (!$comments): ?>
<p class="cs-no-comments">Be the first to comment.</p>
<?php endif; ?>

<?php if ($comments_per_page != -1 && $comments_per_page < $comments_info['total_comments']): ?>
<div class="cs-show-more-comments">
    <a href="#">Show More</a>
</div>
<?php endif; ?>

<!-- Feel free to remove the below if you want to. It's not required -->
<div class="cs-powered-by">
    <a href="https://codeshack.io/package/php/advanced-commenting-system/">Powered by CodeShack Commenting System</a>
</div>

<?php if (login_enabled && !isset($_SESSION['comment_account_loggedin'])): ?>
<dialog class="cs-modal cs-modal-login">
    <form class="cs-comment-auth-form cs-comment-login-form">
        <div class="cs-modal-header">
            <h3>Login</h3>
            <a href="#" class="cs-modal-close" title="Close"><svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg></a>
        </div>
        <div class="cs-modal-content">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <div class="cs-msg"></div>
        </div>
        <div class="cs-modal-footer">
            <div class="cs-modal-btns">
                <button type="submit" class="cs-submit-btn">Login</button>
                <a href="#" class="cs-modal-close cs-modal-btn-alt">Close</a>
            </div>
            <div class="cs-modal-social-btns">
                <?php if (facebook_oauth_enabled): ?>
                <a href="<?=comments_url?>facebook-oauth.php" class="cs-modal-fb-btn"><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z"/></svg>Login with Facebook</a>
                <?php endif; ?>
                <?php if (google_oauth_enabled): ?>
                <a href="<?=comments_url?>google-oauth.php" class="cs-modal-gg-btn"><svg width="16" height="16" viewBox="-3 0 262 262" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid"><path d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622 38.755 30.023 2.685.268c24.659-22.774 38.875-56.282 38.875-96.027" fill="#4285F4"/><path d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055-34.523 0-63.824-22.773-74.269-54.25l-1.531.13-40.298 31.187-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1" fill="#34A853"/><path d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82 0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602l42.356-32.782" fill="#FBBC05"/><path d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0 79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251" fill="#EB4335"/></svg>Login with Google</a>
                <?php endif; ?>
                <?php if (x_oauth_enabled): ?>
                <a href="<?=comments_url?>x-oauth.php" class="cs-modal-x-btn"><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>Login with X</a>
                <?php endif; ?>
            </div>
            <?php if (register_enabled): ?>
            <p class="cs-modal-auth-link">Don't have an account? <a href="#" class="cs-modal-register-link">Register</a></p>
            <?php endif; ?>
        </div>
    </form>
</dialog>
<?php endif; ?>

<?php if (register_enabled && !isset($_SESSION['comment_account_loggedin'])): ?>
<dialog class="cs-modal cs-modal-register">
    <form class="cs-comment-auth-form cs-comment-register-form" autocomplete="off">
        <div class="cs-modal-header">
            <h3>Register</h3>
            <a href="#" class="cs-modal-close" title="Close"><svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg></a>
        </div>
        <div class="cs-modal-content">
            <input type="text" name="name" placeholder="Display Name" minlength="3" maxlength="20" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" autocomplete="new-password" minlength="6" maxlength="20" required>
            <input type="password" name="cpassword" placeholder="Confirm Password" autocomplete="new-password" minlength="6" maxlength="20" required>
            <div class="cs-msg"></div>
        </div>
        <div class="cs-modal-footer">
            <div class="cs-modal-btns">
                <button type="submit" class="cs-submit-btn">Register</button>
                <a href="#" class="cs-modal-close cs-modal-btn-alt">Close</a>
            </div>
            <?php if (login_enabled): ?>
            <p class="cs-modal-auth-link">Already have an account? <a href="#" class="cs-modal-login-link">Login</a></p>
            <?php endif; ?>
        </div>
    </form>
</dialog>
<?php endif; ?>

<dialog class="cs-modal cs-modal-report">
    <form class="cs-comment-auth-form">
        <div class="cs-modal-header">
            <h3>Report Comment</h3>
            <a href="#" class="cs-modal-close" title="Close"><svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg></a>
        </div>
        <div class="cs-modal-content">
            <textarea name="reason" placeholder="Reason for reporting" minlength="3" maxlength="150" required></textarea>
            <div class="cs-msg"></div>
        </div>
        <div class="cs-modal-footer">
            <div class="cs-modal-btns">
                <button type="submit" class="cs-submit-btn">Report</button>
                <a href="#" class="cs-modal-close cs-modal-btn-alt">Close</a>
            </div>
        </div>
    </form>
</dialog>

<?php if (isset($_SESSION['comment_account_loggedin'])): ?>
<dialog class="cs-modal cs-modal-edit-profile">
    <form class="cs-comment-auth-form cs-comment-edit-profile-form" enctype="multipart/form-data">
        <div class="cs-modal-header">
            <h3>Edit Profile</h3>
            <a href="#" class="cs-modal-close" title="Close"><svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg></a>
        </div>
        <div class="cs-modal-content">
            <input type="text" name="name" placeholder="Display Name" minlength="3" maxlength="20" value="<?=htmlspecialchars($account['display_name'])?>" required>
            <input type="email" name="email" placeholder="Email" value="<?=htmlspecialchars($account['email'])?>" required>
            <input type="password" name="password" placeholder="Password" autocomplete="new-password" minlength="6" maxlength="20">
            <input type="password" name="cpassword" placeholder="Confirm Password" autocomplete="new-password" minlength="6" maxlength="20">
            <?php if (profile_websites_enabled): ?>
            <input type="url" name="website" placeholder="Website" value="<?=htmlspecialchars($account['website_url'])?>">
            <?php endif; ?>
            <?php if (profile_photos_enabled): ?>
            <input type="file" name="photo" accept="image/*">
            <?php endif; ?>
            <div class="cs-msg"></div>
        </div>
        <div class="cs-modal-footer">
            <div class="cs-modal-btns">
                <button type="submit" class="cs-submit-btn">Save</button>
                <a href="#" class="cs-modal-close cs-modal-btn-alt">Close</a>
            </div>
        </div>
    </form>
</dialog>
<?php endif; ?>

<?=generate_comment_schema($comments)?>