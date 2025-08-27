<?php
/**
 * Shop Product Management (Individual Product)
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: product.php
 * LOCATION: /public_html/admin/shop_system/
 * PURPOSE: Standalone product edit/create page with admin integration
 * 
 * CREATED: 2025-08-18
 * VERSION: 2.0 (Standalone)
 */

// Include admin authentication and dependencies
include '../assets/includes/main.php';
include '../../shop_system/functions.php';

// Ensure template_editor is defined for TinyMCE editor
if (!defined('template_editor')) {
    define('template_editor', 'tinymce');
}

// Ensure weight_unit is defined for product weight fields
if (!defined('weight_unit')) {
    define('weight_unit', 'lbs');
}
// Default input product values
$product = [
    'title' => '',
    'description' => '',
    'price' => '',
    'rrp' => '',
    'quantity' => '',
    'created' => date('Y-m-d\TH:i'),
    'media' => [],
    'categories' => [],
    'options' => [],
    'downloads' => [],
    'weight' => '',
    'url_slug' => '',
    'product_status' => 1,
    'sku' => '',
    'subscription' => 0,
    'subscription_period' => 1,
    'subscription_period_type' => 'day'
];
// Get all the categories from the database
$stmt = $pdo->query('SELECT * FROM shop_product_categories');
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Add product images to the database
function addProductImages($pdo, $product_id) {
    // Get the total number of media
    if (isset($_POST['media']) && is_array($_POST['media']) && count($_POST['media']) > 0) {
        // Iterate media
        $delete_list = [];
        for ($i = 0; $i < count($_POST['media']); $i++) {
            // If the media doesnt exist in the database
            if (!intval($_POST['media_product_id'][$i])) {
                // Insert new media
                $stmt = $pdo->prepare('INSERT INTO shop_product_media_map (product_id,media_id,position) VALUES (?,?,?)');
                $stmt->execute([ $product_id, $_POST['media'][$i], $_POST['media_position'][$i] ]);
                $delete_list[] = $pdo->lastInsertId();
            } else {
                // Update existing media
                $stmt = $pdo->prepare('UPDATE shop_product_media_map SET position = ? WHERE id = ?');
                $stmt->execute([ $_POST['media_position'][$i], $_POST['media_product_id'][$i] ]);    
                $delete_list[] = $_POST['media_product_id'][$i];          
            }
        }
        // Delete media
        $in  = str_repeat('?,', count($delete_list) - 1) . '?';
        $stmt = $pdo->prepare('DELETE FROM shop_product_media_map WHERE product_id = ? AND id NOT IN (' . $in . ')');
        $stmt->execute(array_merge([ $product_id ], $delete_list));
    } else {
        // No media exists, delete all
        $stmt = $pdo->prepare('DELETE FROM shop_product_media_map WHERE product_id = ?');
        $stmt->execute([ $product_id ]);       
    }
}
// Add product categories to the database
function addProductCategories($pdo, $product_id) {
    if (isset($_POST['categories']) && is_array($_POST['categories']) && count($_POST['categories']) > 0) {
        $in  = str_repeat('?,', count($_POST['categories']) - 1) . '?';
        $stmt = $pdo->prepare('DELETE FROM shop_product_category WHERE product_id = ? AND category_id NOT IN (' . $in . ')');
        $stmt->execute(array_merge([ $product_id ], $_POST['categories']));
        foreach ($_POST['categories'] as $cat) {
            $stmt = $pdo->prepare('INSERT IGNORE INTO shop_product_category (product_id,category_id) VALUES (?,?)');
            $stmt->execute([ $product_id, $cat ]);
        }
    } else {
        $stmt = $pdo->prepare('DELETE FROM shop_product_category WHERE product_id = ?');
        $stmt->execute([ $product_id ]);       
    }
}
// Add product options to the database
function addProductOptions($pdo, $product_id) {
    if (isset($_POST['option_name']) && is_array($_POST['option_name']) && count($_POST['option_name']) > 0) {
        $delete_list = [];
        for ($i = 0; $i < count($_POST['option_name']); $i++) {
            $delete_list[] = $_POST['option_name'][$i] . '__' . $_POST['option_value'][$i];
            $qty = empty($_POST['option_quantity'][$i]) && (int)$_POST['option_quantity'][$i] != 0 ? -1 : $_POST['option_quantity'][$i];
            $stmt = $pdo->prepare('INSERT INTO shop_product_options (option_name,option_value,quantity,price,price_modifier,weight,weight_modifier,option_type,required,position,product_id) VALUES (?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), price = VALUES(price), price_modifier = VALUES(price_modifier), weight = VALUES(weight), weight_modifier = VALUES(weight_modifier), option_type = VALUES(option_type), required = VALUES(required), position = VALUES(position)');
            $stmt->execute([ $_POST['option_name'][$i], $_POST['option_value'][$i], $qty, empty($_POST['option_price'][$i]) ? 0.00 : $_POST['option_price'][$i], $_POST['option_price_modifier'][$i], empty($_POST['option_weight'][$i]) ? 0.00 : $_POST['option_weight'][$i], $_POST['option_weight_modifier'][$i], $_POST['option_type'][$i], $_POST['option_required'][$i], $_POST['option_position'][$i], $product_id ]);           
        }
        $in  = str_repeat('?,', count($delete_list) - 1) . '?';
        $stmt = $pdo->prepare('DELETE FROM shop_product_options WHERE product_id = ? AND CONCAT(option_name, "__", option_value) NOT IN (' . $in . ')');
        $stmt->execute(array_merge([ $product_id ], $delete_list));  
    } else {
        $stmt = $pdo->prepare('DELETE FROM shop_product_options WHERE product_id = ?');
        $stmt->execute([ $product_id ]);       
    }
}
// Add product downloads to the database
function addProductDownloads($pdo, $product_id) {
    if (isset($_POST['download_file_path']) && is_array($_POST['download_file_path']) && count($_POST['download_file_path']) > 0) {
        $delete_list = [];
        for ($i = 0; $i < count($_POST['download_file_path']); $i++) {
            $delete_list[] = $_POST['download_file_path'][$i];
            $stmt = $pdo->prepare('INSERT INTO shop_product_downloads (product_id,file_path,position) VALUES (?,?,?) ON DUPLICATE KEY UPDATE position = VALUES(position)');
            $stmt->execute([ $product_id, $_POST['download_file_path'][$i], $_POST['download_position'][$i] ]);           
        }
        $in  = str_repeat('?,', count($delete_list) - 1) . '?';
        $stmt = $pdo->prepare('DELETE FROM shop_product_downloads WHERE product_id = ? AND file_path NOT IN (' . $in . ')');
        $stmt->execute(array_merge([ $product_id ], $delete_list));  
    } else {
        $stmt = $pdo->prepare('DELETE FROM shop_product_downloads WHERE product_id = ?');
        $stmt->execute([ $product_id ]);       
    }
}
if (isset($_GET['id'])) {
    // ID param exists, edit an existing product
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the product
        $stmt = $pdo->prepare('UPDATE shop_products SET title = ?, description = ?, price = ?, rrp = ?, quantity = ?, created = ?, weight = ?, url_slug = ?, product_status = ?, sku = ?, subscription = ?, subscription_period = ?, subscription_period_type = ? WHERE id = ?');
        $stmt->execute([ $_POST['title'], $_POST['description'], empty($_POST['price']) ? 0.00 : $_POST['price'], empty($_POST['rrp']) ? 0.00 : $_POST['rrp'], $_POST['quantity'], date('Y-m-d H:i:s', strtotime($_POST['date'])), empty($_POST['weight']) ? 0.00 : $_POST['weight'], $_POST['url_slug'], $_POST['status'], $_POST['sku'], $_POST['subscription'], empty($_POST['subscription_period']) ? 0 : $_POST['subscription_period'], $_POST['subscription_period_type'], $_GET['id'] ]);
        addProductImages($pdo, $_GET['id']);
        addProductCategories($pdo, $_GET['id']);
        addProductOptions($pdo, $_GET['id']);
        addProductDownloads($pdo, $_GET['id']);
        // Clear session cart
        if (isset($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
        header('Location: products.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete product
        header('Location: products.php?delete=' . $_GET['id']);
        exit;
    }
    // Get the product and its images from the database
    $stmt = $pdo->prepare('SELECT * FROM shop_products WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // get product media
    $stmt = $pdo->prepare('SELECT m.*, pm.position, pm.id AS product_id FROM shop_product_media m JOIN shop_product_media_map pm ON pm.media_id = m.id JOIN shop_products p ON p.id = pm.product_id WHERE p.id = ? ORDER BY pm.position');
    $stmt->execute([ $_GET['id'] ]);
    $product['media'] = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    // Get the product categories
    $stmt = $pdo->prepare('SELECT c.title, c.id FROM shop_product_category pc JOIN shop_product_categories c ON c.id = pc.category_id WHERE pc.product_id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $product['categories'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Get the product options
    $stmt = $pdo->prepare('SELECT option_name, option_type, GROUP_CONCAT(option_value ORDER BY id) AS list FROM shop_product_options WHERE product_id = ? GROUP BY option_name, option_type, position ORDER BY position');
    $stmt->execute([ $_GET['id'] ]);
    $product['options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Get the product full options
    $stmt = $pdo->prepare('SELECT * FROM shop_product_options WHERE product_id = ? ORDER BY id');
    $stmt->execute([ $_GET['id'] ]);
    $product['options_full'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Get the product downloads
    $stmt = $pdo->prepare('SELECT * FROM shop_product_downloads WHERE product_id = ? ORDER BY position');
    $stmt->execute([ $_GET['id'] ]);
    $product['downloads'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Create a new product
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO shop_products (title,description,price,rrp,quantity,created,weight,url_slug,product_status,sku,subscription,subscription_period,subscription_period_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([ $_POST['title'], $_POST['description'], empty($_POST['price']) ? 0.00 : $_POST['price'], empty($_POST['rrp']) ? 0.00 : $_POST['rrp'], $_POST['quantity'], date('Y-m-d H:i:s', strtotime($_POST['date'])), empty($_POST['weight']) ? 0.00 : $_POST['weight'], $_POST['url_slug'], $_POST['status'], $_POST['sku'], $_POST['subscription'], empty($_POST['subscription_period']) ? 0 : $_POST['subscription_period'], $_POST['subscription_period_type'] ]);
        $id = $pdo->lastInsertId();
        addProductImages($pdo, $id);
        addProductCategories($pdo, $id);
        addProductOptions($pdo, $id);
        addProductDownloads($pdo, $id);
        // Clear session cart
        if (isset($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
        header('Location: products.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Product', 'products', 'manage')?>

<form method="post">

    <div class="content-title">
        <h2 class="responsive-width-100"><?=$page?> Product</h2>
    </div>

    <!-- Tab Navigation -->
    <div class="tab-nav" role="tablist" aria-label="Product management options">
        <button class="tab-btn active" 
            role="tab"
            aria-selected="true"
            aria-controls="general-tab"
            id="general-tab-btn"
            onclick="openTab(event, 'general-tab')">
            General
        </button>
        <button class="tab-btn" 
            role="tab"
            aria-selected="false"
            aria-controls="media-tab"
            id="media-tab-btn"
            onclick="openTab(event, 'media-tab')">
            Media
        </button>
        <button class="tab-btn" 
            role="tab"
            aria-selected="false"
            aria-controls="options-tab"
            id="options-tab-btn"
            onclick="openTab(event, 'options-tab')">
            Options
        </button>
        <button class="tab-btn" 
            role="tab"
            aria-selected="false"
            aria-controls="downloads-tab"
            id="downloads-tab-btn"
            onclick="openTab(event, 'downloads-tab')">
            Downloads
        </button>
        <button class="tab-btn" 
            role="tab"
            aria-selected="false"
            aria-controls="subscription-tab"
            id="subscription-tab-btn"
            onclick="openTab(event, 'subscription-tab')">
            Subscription
        </button>
    </div>

    <!-- General Tab Content -->
    <div id="general-tab" 
        class="tab-content active" 
        role="tabpanel"
        aria-labelledby="general-tab-btn">

        <div class="form responsive-width-100 size-md">

            <div class="group">
                <div class="item">
                    <label for="title"><span class="required">*</span> Title</label>
                    <input id="title" type="text" name="title" placeholder="Title" value="<?=$product['title']?>" required>
                </div>
                <div class="item">
                    <label for="url_slug">URL Slug</label>
                    <input id="url_slug" type="text" name="url_slug" placeholder="your-product-name" value="<?=$product['url_slug']?>" title="If the rewrite URL setting is enabled, the URL slug will appear after the trailing slash as opposed to the product ID.">
                </div>
            </div>

            <label for="description">Description</label>
            <?php if (template_editor == 'tinymce'): ?>
            <div style="width:100%;margin:15px 0 25px;">
                <textarea id="description" name="description" style="width:100%;height:400px;" wrap="off" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"><?=$product['description']?></textarea>
            </div>
            <?php else: ?>
            <textarea id="description" name="description" placeholder="Product Description..."><?=$product['description']?></textarea>
            <?php endif; ?>

            <label for="sku">SKU</label>
            <input id="sku" type="text" name="sku" placeholder="SKU" value="<?=$product['sku']?>">

            <div class="group">
                <div class="item">
                    <label for="price"><span class="required">*</span> Price</label>
                    <input id="price" type="number" name="price" placeholder="Price" min="0" step=".01" value="<?=$product['price']?>" required>
                </div>
                <div class="item">
                    <label for="rrp">RRP</label>
                    <input id="rrp" type="number" name="rrp" placeholder="RRP" min="0" step=".01" value="<?=$product['rrp']?>">
                </div>
            </div>

            <div class="group">
                <div class="item">
                    <label for="quantity"><span class="required">*</span> Quantity</span></label>
                    <input id="quantity" type="number" name="quantity" placeholder="Quantity" min="-1" value="<?=$product['quantity']?>" title="-1 = unlimited" required>
                </div>
                <div class="item pad-top-5">
                    <label for="unlimited" class="switch">
                        <input type="checkbox" id="unlimited" name="unlimited" class="switch" value="1"<?=$product['quantity'] == -1 ? ' checked' : ''?>>
                        <span class="slider round"></span>
                        <span class="txt">Unlimited Stock</span>
                    </label>
                </div>
            </div>

            <label for="category">Categories</label>
            <div class="multiselect" data-name="categories[]">
                <?php foreach ($product['categories'] as $cat): ?>
                <span class="item" data-value="<?=$cat['id']?>">
                    <i class="remove">&times;</i><?=$cat['title']?>
                    <input type="hidden" name="categories[]" value="<?=$cat['id']?>">
                </span>
                <?php endforeach; ?>
                <input type="text" class="search" id="category" placeholder="Categories">
                <div class="list">
                    <?php foreach ($categories as $cat): ?>
                    <span data-value="<?=$cat['id']?>"><?=$cat['title']?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <label for="weight">Weight (<?=weight_unit?>)</span></label>
            <input id="weight" type="number" name="weight" placeholder="Weight (<?=weight_unit?>)" min="0" step=".01" value="<?=$product['weight']?>">

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="1"<?=$product['product_status']==1?' selected':''?>>Enabled</option>
                <option value="0"<?=$product['product_status']==0?' selected':''?>>Disabled</option>
            </select>

            <label for="date"><span class="required">*</span> Date</label>
            <input id="date" type="datetime-local" name="date" placeholder="Date" value="<?=date('Y-m-d\TH:i', strtotime($product['created']))?>" required>

        </div>

    </div>

    <!-- Media Tab Content -->
    <div id="media-tab" 
        class="tab-content"
        role="tabpanel"
        aria-labelledby="media-tab-btn">

        <div class="pad-3 product-media-tab responsive-width-100">

            <h3 class="title1 mar-bot-5">Images</h3>

            <div class="product-media-container">
                <?php if (isset($product['media'])): ?>
                <?php foreach ($product['media'] as $i => $media): ?>
                <div class="product-media">
                    <span class="media-index responsive-hidden"><?=$i+1?></span>
                    <a class="media-img" href="../<?=$media['full_path']?>" target="_blank">
                        <img src="../<?=$media['full_path']?>" alt="<?=basename($media['full_path'])?>">
                    </a>
                    <div class="media-text">
                        <h3 class="responsive-hidden"><?=$media['title']?></h3>
                        <p class="responsive-hidden"><?=$media['caption']?></p>
                    </div>
                    <div class="media-position">
                        <a href="#" class="media-delete" title="Delete">
                            <svg width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                        </a>
                        <a href="#" class="move-up" title="Move Up">
                            <svg width="26" height="26" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7.41,15.41L12,10.83L16.59,15.41L18,14L12,8L6,14L7.41,15.41Z" /></svg>
                        </a>
                        <a href="#" class="move-down" title="Move Down">
                            <svg width="26" height="26" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" /></svg>
                        </a>
                    </div>
                    <input type="hidden" class="input-media-id" name="media[]" value="<?=$media['id']?>">
                    <input type="hidden" class="input-media-product-id" name="media_product_id[]" value="<?=$media['product_id']?>">
                    <input type="hidden" class="input-media-position" name="media_position[]" value="<?=$media['position']?>">
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php if (empty($product['media'])): ?>
                <p class="no-images-msg">There are no images.</p>
                <?php endif; ?>
            </div>

            <a href="#" class="btn open-media-library-modal mar-bot-2 mar-top-4">
                <i class="bi bi-plus-lg me-1"></i> Add Media
            </a>

        </div>

    </div>

    <!-- Options Tab Content -->
    <div id="options-tab" 
        class="tab-content"
        role="tabpanel"
        aria-labelledby="options-tab-btn">

        <div class="pad-3 product-options-tab responsive-width-100">

            <h3 class="title1 mar-bot-5">Options</h3>

            <div class="product-options-container">
                <?php if (isset($product['options'])): ?>
                <?php foreach ($product['options'] as $i => $option): ?>
                <div class="product-option">
                    <span class="option-index responsive-hidden"><?=$i+1?></span>
                    <div class="option-text">
                        <h3><?=$option['option_name']?> (<?=$option['option_type']?>)</h3>
                        <p><?=str_replace(',', ', ', $option['list'])?></p>
                    </div>
                    <div class="option-position">
                        <a href="#" class="option-edit" title="Edit">
                            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" /></svg>
                        </a>
                        <a href="#" class="option-delete" title="Delete">
                            <svg width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                        </a>
                        <a href="#" class="move-up" title="Move Up">
                            <svg width="26" height="26" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7.41,15.41L12,10.83L16.59,15.41L18,14L12,8L6,14L7.41,15.41Z" /></svg>
                        </a>
                        <a href="#" class="move-down" title="Move Down">
                            <svg width="26" height="26" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" /></svg>
                        </a>
                    </div>
                    <?php foreach ($product['options_full'] as $option_full): ?>
                    <?php if ($option['option_name'] != $option_full['option_name']) continue; ?>
                    <div class="input-option">
                        <input type="hidden" class="input-option-name" name="option_name[]" value="<?=$option_full['option_name']?>">
                        <input type="hidden" class="input-option-value" name="option_value[]" value="<?=$option_full['option_value']?>">
                        <input type="hidden" class="input-option-quantity" name="option_quantity[]" value="<?=$option_full['quantity']?>">
                        <input type="hidden" class="input-option-price" name="option_price[]" value="<?=$option_full['price']?>">
                        <input type="hidden" class="input-option-price-modifier" name="option_price_modifier[]" value="<?=$option_full['price_modifier']?>">
                        <input type="hidden" class="input-option-weight" name="option_weight[]" value="<?=$option_full['weight']?>">
                        <input type="hidden" class="input-option-weight-modifier" name="option_weight_modifier[]" value="<?=$option_full['weight_modifier']?>">
                        <input type="hidden" class="input-option-type" name="option_type[]" value="<?=$option_full['option_type']?>">
                        <input type="hidden" class="input-option-required" name="option_required[]" value="<?=$option_full['required']?>">
                        <input type="hidden" class="input-option-position" name="option_position[]" value="<?=$option_full['position']?>">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php if (empty($product['options'])): ?>
                <p class="no-options-msg">There are no options.</p>
                <?php endif; ?>
            </div>

            <a href="#" class="btn open-options-modal mar-bot-2 mar-top-4">
                <i class="bi bi-plus icon-left" aria-hidden="true"></i>
                Add Option
            </a>

        </div>

    </div>

    <!-- Downloads Tab Content -->
    <div id="downloads-tab" 
        class="tab-content"
        role="tabpanel"
        aria-labelledby="downloads-tab-btn">

        <div class="pad-3 product-options-tab responsive-width-100">

            <h3 class="title1 mar-bot-5">Digital Downloads</h3>

            <div class="product-downloads-container">
                <?php if (isset($product['downloads'])): ?>
                <?php foreach ($product['downloads'] as $i => $download): ?>
                <?php if (!file_exists('../' . $download['file_path'])) continue; ?>
                <div class="product-download">
                    <span class="download-index responsive-hidden"><?=$i+1?></span>
                    <div class="download-text">
                        <h3><?=$download['file_path']?></h3>
                        <p><?=mime_content_type('../' . $download['file_path'])?>, <?=format_bytes(filesize('../' . $download['file_path']))?></p>
                    </div>
                    <div class="download-position">
                        <a href="#" class="download-delete" title="Delete">
                            <svg width="22" height="22" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                        </a>
                        <a href="#" class="move-up" title="Move Up">
                            <svg width="26" height="26" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7.41,15.41L12,10.83L16.59,15.41L18,14L12,8L6,14L7.41,15.41Z" /></svg>
                        </a>
                        <a href="#" class="move-down" title="Move Down">
                            <svg width="26" height="26" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z" /></svg>
                        </a>
                    </div>
                    <div class="input-option">
                        <input type="hidden" class="input-download-file-path" name="download_file_path[]" value="<?=$download['file_path']?>">
                        <input type="hidden" class="input-download-position" name="download_position[]" value="<?=$download['position']?>">
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php if (empty($product['downloads'])): ?>
                <p class="no-downloads-msg">There are no digital downloads.</p>
                <?php endif; ?>
            </div>

            <a href="#" class="btn open-downloads-modal mar-bot-2 mar-top-4">
                <i class="bi bi-plus-lg me-1"></i> Add Digital Download
            </a>

        </div>

    </div>

    <!-- Subscription Tab Content -->
    <div id="subscription-tab" 
        class="tab-content"
        role="tabpanel"
        aria-labelledby="subscription-tab-btn">

        <div class="form responsive-width-100">

            <label for="subscription">Subscription</label>
            <select id="subscription" name="subscription">
                <option value="0"<?=$product['subscription']==0?' selected':''?>>No</option>
                <option value="1"<?=$product['subscription']==1?' selected':''?>>Yes</option>
            </select>

            <label for="subscription_period">Subscription Period</label>
            <input id="subscription_period" type="number" name="subscription_period" placeholder="Subscription Period" min="0" value="<?=$product['subscription_period']?>">

            <label for="subscription_period_type">Subscription Period Type</label>
            <select id="subscription_period_type" name="subscription_period_type">
                <option value="day"<?=$product['subscription_period_type']=='day'?' selected':''?>>Day</option>
                <option value="week"<?=$product['subscription_period_type']=='week'?' selected':''?>>Week</option>
                <option value="month"<?=$product['subscription_period_type']=='month'?' selected':''?>>Month</option>
                <option value="year"<?=$product['subscription_period_type']=='year'?' selected':''?>>Year</option>
            </select>

            <div class="subscription-info">
                <div class="d-flex align-items-center gap-2 p-3 bg-light rounded">
                    <i class="bi bi-arrow-repeat text-primary" aria-hidden="true"></i>
                    <div>
                        <strong>Recurring Billing</strong>
                        <p class="mb-0 text-muted">Configure automatic recurring payments for this product. Customers will be charged periodically based on the specified interval.</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Form Actions -->
    <div class="d-flex gap-2 pt-3 border-top mt-4 mx-3" role="region" aria-label="Form Actions">
        <a href="products.php" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
            Cancel
        </a>
        <button type="submit" name="submit" class="btn btn-success">
            <i class="bi bi-save me-1" aria-hidden="true"></i>
            Save Product
        </button>
        <?php if ($page == 'Edit'): ?>
            <button type="submit" name="delete" class="btn btn-danger"
                onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')"
                aria-label="Delete this product permanently">
                <i class="bi bi-trash me-1" aria-hidden="true"></i>
                Delete Product
            </button>
        <?php endif; ?>
    </div>

</form>

<?php if (template_editor == 'tinymce'): ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.3.0/tinymce.min.js" integrity="sha512-RUZ2d69UiTI+LdjfDCxqJh5HfjmOcouct56utQNVRjr90Ea8uHQa+gCxvxDTC9fFvIGP+t4TDDJWNTRV48tBpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
tinymce.init({
    selector: '#description',
    plugins: 'image table lists media link code',
    toolbar: 'undo redo | blocks bold italic forecolor | align outdent indent numlist bullist | image link | fontfamily fontsize backcolor underline strikethrough lineheight table code removeformat',
    menubar: false,
    valid_elements: '*[*]',
    extended_valid_elements: '*[*]',
    valid_children: '+body[style]',
    content_css: false,
    height: 400,
    branding: false,
    promotion: false,
    automatic_uploads: false,
    image_title: true,
    image_description: true,
    license_key: 'gpl'
});
</script>
<?php endif; ?>

<style>
    /* Tab Navigation */
    .tab-nav {
        display: flex;
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 0;
        position: relative;
        background-color: transparent;
        padding: 1rem 0 0 0;
    }

    .tab-btn {
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        border-bottom: 2px solid #dee2e6;
        padding: 12px 24px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.3s ease;
        border-radius: 8px 8px 0 0;
        margin-right: 4px;
        position: relative;
        outline: none;
    }

    .tab-btn:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }

    .tab-btn:hover {
        color: #495057;
        background-color: #e9ecef;
        border-color: #adb5bd;
        border-bottom-color: #adb5bd;
    }

    .tab-btn:focus {
        outline: 2px solid #0d6efd;
        outline-offset: -2px;
        z-index: 1;
    }

    .tab-btn.active {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 transparent;
        position: relative;
        z-index: 2;
        font-weight: 600;
        border-bottom: 2px solid #fff;
        margin-bottom: -2px;
    }

    .tab-btn[aria-selected="true"] {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 transparent;
        border-bottom: 2px solid #fff;
        margin-bottom: -2px;
    }

    /* Tab Content */
    .tab-content {
        display: none;
        padding: 30px;
        background-color: #fff;
        border: 2px solid #dee2e6;
        border-top: none;
        border-radius: 0 8px 8px 8px;
        margin-top: 0;
        margin-left: 0;
    }

    .tab-content.active {
        display: block;
    }

    /* Form Actions Styling */
    .d-flex {
        display: flex !important;
    }

    .gap-2 {
        gap: 0.5rem !important;
    }

    .pt-3 {
        padding-top: 1rem !important;
    }

    .mt-4 {
        margin-top: 1.5rem !important;
    }

    .mx-3 {
        margin-left: 1rem !important;
        margin-right: 1rem !important;
    }

    .border-top {
        border-top: 1px solid #dee2e6 !important;
    }

    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
        background-color: transparent;
    }

    .btn-outline-secondary:hover {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-success {
        color: #fff;
        background-color: #198754;
        border-color: #198754;
    }

    .btn-success:hover {
        color: #fff;
        background-color: #157347;
        border-color: #146c43;
    }

    .btn-danger {
        color: #fff;
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-danger:hover {
        color: #fff;
        background-color: #bb2d3b;
        border-color: #b02a37;
    }

    /* Removed legacy .fas font-family rule */

    .me-1 {
        margin-right: 0.25rem !important;
    }

    .mb-0 {
        margin-bottom: 0 !important;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .text-primary {
        color: #0d6efd !important;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .rounded {
        border-radius: 0.375rem !important;
    }

    .align-items-center {
        align-items: center !important;
    }

    .p-3 {
        padding: 1rem !important;
    }
</style>

<script>

    function openTab(evt, tabName) {
        // Declare variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tab-content" and hide them
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }

        // Get all elements with class="tab-btn" and remove the class "active"
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
            tablinks[i].setAttribute("aria-selected", "false");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
        evt.currentTarget.setAttribute("aria-selected", "true");

        // Set hash so tab stays on reload
        window.location.hash = tabName;
    }

    // On page load, activate tab from hash if present
    document.addEventListener('DOMContentLoaded', function() {
        var hash = window.location.hash.replace('#', '');
        if (hash && document.getElementById(hash)) {
            var tabBtn = document.querySelector('.tab-btn[onclick*="' + hash + '"]');
            if (tabBtn) {
                tabBtn.click();
            }
        }
    });

    // Keyboard navigation for tabs
    document.addEventListener('keydown', function(e) {
        const target = e.target;
        const tabButtons = document.getElementsByClassName("tab-btn");
        
        if (!target.classList.contains('tab-btn')) return;
        
        const currentIndex = Array.from(tabButtons).indexOf(target);
        let nextIndex;
        
        switch(e.key) {
            case 'ArrowLeft':
                nextIndex = currentIndex > 0 ? currentIndex - 1 : tabButtons.length - 1;
                break;
            case 'ArrowRight':
                nextIndex = currentIndex < tabButtons.length - 1 ? currentIndex + 1 : 0;
                break;
            case 'Home':
                nextIndex = 0;
                break;
            case 'End':
                nextIndex = tabButtons.length - 1;
                break;
            default:
                return;
        }
        
        e.preventDefault();
        tabButtons[nextIndex].focus();
        tabButtons[nextIndex].click();
    });
</script>

<?=template_admin_footer('<script>initProduct()</script>')?>