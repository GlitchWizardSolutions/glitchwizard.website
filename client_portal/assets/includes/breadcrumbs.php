
<main id="main" class="main">

    <?php
    // Get current page name
    $current_page = basename($_SERVER['PHP_SELF']);

    // Default breadcrumbs: Home only
    $breadcrumbs = [
        ["label" => "Home", "url" => "./", "active" => true]
    ];
    $page_title = "Home";

    // Template: Add more cases as needed
    switch ($current_page) {
        case "users-profile.php":
            $breadcrumbs = [
                ["label" => "Home", "url" => "./", "active" => false],
                ["label" => "Users", "url" => null, "active" => false],
                ["label" => "Profile", "url" => null, "active" => true]
            ];
            $page_title = "Profile";
            break;
        case "index.php":
            $breadcrumbs = [
                ["label" => "Home", "url" => "./", "active" => true]
            ];
            $page_title = "Home";
            break;
        case "pages-faq.php":
            $breadcrumbs = [
                ["label" => "Home", "url" => "./", "active" => false],
                ["label" => "FAQs", "url" => null, "active" => true]
            ];
            $page_title = "FAQs";
            break;
        case "pages-contact.php":
            $breadcrumbs = [
                ["label" => "Home", "url" => "./", "active" => false],
                ["label" => "Contact", "url" => null, "active" => true]
            ];
            $page_title = "Contact";
            break;
        case "pages-error-404.php":
            $breadcrumbs = [
                ["label" => "Home", "url" => "./", "active" => false],
                ["label" => "Not Found", "url" => null, "active" => true]
            ];
            $page_title = "Not Found";
            break;
        case "pages-blank.php":
            $breadcrumbs = [
                ["label" => "Blank", "url" => null, "active" => true]
            ];
            $page_title = "Blank";
            break;
        // Add more cases for other pages here
    }
    ?>
    <div class="pagetitle">
        <h1><?= htmlspecialchars($page_title) ?></h1>
        <nav>
            <ol class="breadcrumb">
                <?php foreach ($breadcrumbs as $item): ?>
                    <?php if ($item["active"]): ?>
                        <li class="breadcrumb-item active">
                            <?php if ($item["url"]): ?><a href="<?= $item["url"] ?>"><?php endif; ?>
                            <?= htmlspecialchars($item["label"]) ?>
                            <?php if ($item["url"]): ?></a><?php endif; ?>
                        </li>
                    <?php else: ?>
                        <li class="breadcrumb-item">
                            <?php if ($item["url"]): ?><a href="<?= $item["url"] ?>"><?php endif; ?>
                            <?= htmlspecialchars($item["label"]) ?>
                            <?php if ($item["url"]): ?></a><?php endif; ?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div><!-- End Page Title -->
