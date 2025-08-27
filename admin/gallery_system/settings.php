<?php
include '../assets/includes/main.php';
// Configuration file
$file = '../config.php';
// Open the configuration file for reading
$contents = file_get_contents($file);
// Format key function
function format_key($key) {
    $key = str_replace(
        ['_', 'url', 'db ', ' pass', ' user', ' id', ' uri', 'smtp', 'paypal', 'ipn', 'pdf', 'ajax', 'svg', 'png', 'exif'], 
        [' ', 'URL', 'Database ', ' Password', ' Username', ' ID', ' URI', 'SMTP', 'PayPal', 'IPN', 'PDF', 'AJAX', 'SVG', 'PNG', 'EXIF'], 
        strtolower($key)
    );
    return ucwords($key);
}
// Format HTML output function
function format_var_html($key, $value, $comment, $list = []) {
    // Add keys to exclude from the form
    $exclude = ['db_user', 'db_pass', 'db_name', 'db_host', 'db_charset'];
    if (in_array($key, $exclude)) {
        return '';
    }
    $html = '';
    $type = 'text';
    $type = strpos($value, '\n') !== false ? 'textarea' : $type;
    $value = $type != 'textarea' ? htmlspecialchars(trim($value, '\''), ENT_QUOTES) : trim($value, '\'');
    $type = strpos($key, 'pass') !== false ? 'password' : $type;
    $type = in_array(strtolower($value), ['true', 'false']) ? 'checkbox' : $type;
    $checked = strtolower($value) == 'true' ? ' checked' : '';
    
    $html .= '<div class="mb-3">';
    $html .= '<label for="' . $key . '" class="form-label">' . format_key($key) . '</label>';
    if (substr($comment, 0, 2) === '//') {
        $html .= '<div class="form-text text-muted small">' . ltrim($comment, '//') . '</div>';
    }
    if ($type == 'checkbox') {
        $html .= '<input type="hidden" name="' . $key . '" value="false">';
    }
    if ($list) {
        $html .= '<select name="' . $key . '" id="' . $key . '" class="form-select">';
        foreach ($list as $item) {
            $item = explode('=', trim($item));
            $selected = strtolower($item[0]) == strtolower($value) ? ' selected' : '';
            $html .= '<option value="' . $item[0] . '"' . $selected . '>' . $item[1] . '</option>';
        }
        $html .= '</select>';
    } else if ($type == 'textarea') {
        $html .= '<textarea name="' . $key . '" id="' . $key . '" class="form-control" placeholder="' . format_key($key) . '" rows="4">' . str_replace('\n', PHP_EOL, $value) . '</textarea>';
    } else if ($type == 'checkbox') {
        $html .= '<div class="form-check form-switch">
                      <input type="' . $type . '" name="' . $key . '" id="' . $key . '" class="form-check-input" value="' . $value . '" placeholder="' . format_key($key) . '"' . $checked . '>
                      <label class="form-check-label" for="' . $key . '">Enable</label>
                  </div>';
    } else {
        $html .= '<input type="' . $type . '" name="' . $key . '" id="' . $key . '" class="form-control" value="' . $value . '" placeholder="' . format_key($key) . '"' . $checked . '>';
    }
    $html .= '</div>';
    return $html;
}
// Format tabs
function format_tabs($contents) {
    $rows = explode("\n", $contents);
    echo '<ul class="nav nav-tabs border-0 mb-4">';
    echo '<li class="nav-item"><a class="nav-link active" href="#" data-tab="general">General</a></li>';
    for ($i = 0; $i < count($rows); $i++) {
        preg_match('/\/\*(.*?)\*\//', $rows[$i], $match);
        if ($match) {
            $tab_id = strtolower(str_replace(' ', '_', $match[1]));
            echo '<li class="nav-item"><a class="nav-link" href="#" data-tab="' . $tab_id . '">' . $match[1] . '</a></li>';
        }
    }
    echo '</ul>';
}
// Format form
function format_form($contents) {
    $rows = explode("\n", $contents);
    echo '<div class="tab-pane show active" id="general">';
    for ($i = 0; $i < count($rows); $i++) {
        preg_match('/\/\*(.*?)\*\//', $rows[$i], $match);
        if ($match) {
            $tab_id = strtolower(str_replace(' ', '_', $match[1]));
            echo '</div><div class="tab-pane" id="' . $tab_id . '">';
        }
        preg_match('/define\(\'(.*?)\', ?(.*?)\)/', $rows[$i], $match);
        if ($match) {
            $list = substr($rows[$i-1], 0, 8) === '// List:' ? explode(',', substr($rows[$i-1], 8)) : [];
            echo format_var_html($match[1], $match[2], $list ? $rows[$i-2] : $rows[$i-1], $list);
        }
    }  
    echo '</div>';
}
if (!empty($_POST)) {
    // Update the configuration file with the new keys and values
    foreach ($_POST as $k => $v) {
        $val = in_array(strtolower($v), ['true', 'false']) ? strtolower($v) : '\'' . $v . '\'';
        $val = is_numeric($v) ? $v : $val;
        $val = str_replace(PHP_EOL, '\n', $val);
        $contents = preg_replace('/define\(\'' . $k . '\'\, ?(.*?)\)/s', 'define(\'' . $k . '\',' . $val . ')', $contents);
    }
    // Save the updated configuration file
    if (file_put_contents($file, $contents) === false) {
        header('Location: settings.php?error_msg=1');
        exit;
    } else {
        header('Location: settings.php?success_msg=1');
        exit;
    }
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Settings updated successfully!';
    }
}
// Handle error messages
if (isset($_GET['error_msg'])) {
    if ($_GET['error_msg'] == 1) {
        $error_msg = 'There was an error updating the settings! Please make sure the config.php file is writable!';
    }
}
?>
<?=template_admin_header('Gallery Settings', 'gallery', 'settings')?>

<div class="content-title" id="main-gallery-settings" role="banner" aria-label="Gallery Settings Header">
    <div class="title">
        <div class="icon">
            <i class="bi bi-gear-fill" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Gallery Settings</h2>
            <p>Configure gallery system preferences and options.</p>
        </div>
    </div>
</div>
<br>

<form method="post" class="needs-validation" novalidate>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-transparent border-0 pb-0">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="card-title mb-0 fw-bold">Gallery Settings</h4>
                    <p class="text-muted mb-0 small">Configure gallery system preferences and options</p>
                </div>
                <div class="col-auto">
                    <button type="submit" name="submit" class="btn btn-success px-4">
                        <i class="bi bi-save me-1"></i>Save Settings
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">

    <?php if (isset($success_msg)): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 text-success" aria-hidden="true"></i>
            <div class="flex-grow-1"><?=$success_msg?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($error_msg)): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-octagon-fill me-2 text-danger" aria-hidden="true"></i>
            <div class="flex-grow-1"><?=$error_msg?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

            <?=format_tabs($contents)?>
            
            <div class="tab-content">
                <?=format_form($contents)?>
            </div>
        </div>
    </div>

    <!-- Bottom Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex gap-2 border-top pt-3">
                <button type="submit" name="submit" class="btn btn-success px-4">
                    <i class="bi bi-save me-1"></i>Save Settings
                </button>
                <a href="allmedia.php" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </a>
            </div>
        </div>
    </div>

</form>

<script>
// Bootstrap tab functionality
document.querySelectorAll('.nav-link').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all tabs and panes
        document.querySelectorAll('.nav-link').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => {
            p.classList.remove('show', 'active');
        });
        
        // Add active class to clicked tab
        this.classList.add('active');
        
        // Show corresponding pane
        const targetTab = this.getAttribute('data-tab');
        const targetPane = document.getElementById(targetTab);
        if (targetPane) {
            targetPane.classList.add('show', 'active');
        }
    });
});

// Checkbox value handling
document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.onclick = () => checkbox.value = checkbox.checked ? 'true' : 'false';
});
</script>

<?=template_admin_footer()?>