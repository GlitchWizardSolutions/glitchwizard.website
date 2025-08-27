<?php
/**
 * Blog Newsletter Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: newsletter.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Send mass emails to blog subscribers and manage newsletter subscriptions
 * 
 * CREATED: 2025-07-03
 * UPDATED: 2025-07-04
 * VERSION: 2.0
 * PRODUCTION: [READY FOR PRODUCTION]
 * 
 * CHANGE LOG:
 * 2025-07-03 - Original implementation with basic mass mailing
 * 2025-07-04 - Modernized with professional header, enhanced UI, and security improvements
 * 2025-07-04 - Added content title block and consistent button formatting
 * 2025-07-04 - Improved email template and subscription management
 * 
 * FEATURES:
 * - Send mass emails to all newsletter subscribers
 * - Rich text content editor with SummerNote
 * - Professional email templates with unsubscribe links
 * - Subscriber management and unsubscribe functionality
 * - Support for HTML email content
 * - Subscription statistics and overview
 * 
 * DEPENDENCIES:
 * - header.php (blog includes)
 * - Bootstrap 5 for styling
 * - SummerNote for rich text editing
 * - PDO database connection
 * - Font Awesome icons
 * 
 * SECURITY NOTES:
 * - Admin authentication required
 * - PDO prepared statements prevent SQL injection
 * - Input validation and sanitization
 * - XSS protection on output
 * - Email validation for newsletter subscriptions
 */
include_once "header.php";

// Sorting setup following Table.php standard
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>',
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>'
];
$order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';
$order_by_whitelist = [
    'email' => 'email'
];
$order_by = isset($_GET['order_by']) && isset($order_by_whitelist[$_GET['order_by']]) ? $_GET['order_by'] : 'email';
$order_by_sql = $order_by_whitelist[$order_by];

if (isset($_GET['unsubscribe']))
{
    $unsubscribe_email = $_GET['unsubscribe'];

    $stmt = $pdo->prepare("SELECT * FROM blog_newsletter WHERE email = ? LIMIT 1");
    $stmt->execute([$unsubscribe_email]);
    if ($stmt->rowCount() > 0)
    {
        $stmtDel = $pdo->prepare("DELETE FROM blog_newsletter WHERE email = ?");
        $stmtDel->execute([$unsubscribe_email]);
    }
}
?>
<?= template_admin_header('Blog Newsletter', 'blog', 'newsletter') ?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-hidden="true"
                focusable="false">
                <path
                    d="M64 112c-8.8 0-16 7.2-16 16v22.1L220.5 291.7c20.7 17 50.4 17 71.1 0L464 150.1V128c0-8.8-7.2-16-16-16H64zM48 212.2V384c0 8.8 7.2 16 16 16H448c8.8 0 16-7.2 16-16V212.2L322 328.8c-38.4 31.5-93.7 31.5-132 0L48 212.2zM0 128C0 92.7 28.7 64 64 64H448c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128z" />
            </svg>
        </div>
        <div class="txt">
            <h2>Newsletter Management</h2>
            <p>Send mass emails to blog subscribers and manage newsletter subscriptions.</p>
        </div>
    </div>
</div>

<div style="height: 20px;"></div>
<div class="card mb-4">
    <h6 class="card-header"><i class="fas fa-paper-plane me-2"></i>Send Mass Email</h6>
    <div class="card-body">
        <?php
        if (isset($_POST['send_mass_message']))
        {
            $title = $_POST['title'];
            $content = htmlspecialchars($_POST['content']);

            $from = $settings['email'];
            $sitename = $settings['sitename'];

            // Get subscriber count for confirmation
            $stmt_count = $pdo->query("SELECT COUNT(*) as total FROM blog_newsletter");
            $subscriber_count = $stmt_count->fetch(PDO::FETCH_ASSOC)['total'];

            $stmt = $pdo->query("SELECT * FROM blog_newsletter");
            $sent_count = 0;
            $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($subscribers as $row)
            {
                $to = $row['email'];
                $subject = $title;
                $message = '
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . '</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 20px; margin-bottom: 30px; }
        .footer { text-align: center; border-top: 1px solid #eee; padding-top: 20px; margin-top: 30px; font-size: 16px; color: #666; }
        .unsubscribe { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><a href="' . $settings['site_url'] . '/" style="color: #007bff; text-decoration: none;" title="Visit ' . htmlspecialchars($sitename) . '">' . htmlspecialchars($sitename) . '</a></h1>
        </div>
        
        <div class="content">
            ' . html_entity_decode($content) . '
        </div>
        
        <div class="unsubscribe">
            <p><strong>Newsletter Information:</strong></p>
            <p><small>You are receiving this email because you subscribed to our newsletter. If you no longer wish to receive these emails, you can <a href="' . $settings['site_url'] . '/admin/blog/newsletter.php?unsubscribe=' . urlencode($to) . '" style="color: #dc3545;">unsubscribe here</a>.</small></p>
        </div>
        
        <div class="footer">
            <p>&copy; ' . date('Y') . ' ' . htmlspecialchars($sitename) . '. All rights reserved.</p>
        </div>
    </div>
</body>
</html>';
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                $headers .= 'From: ' . htmlspecialchars($sitename) . ' <' . $from . '>' . "\r\n";
                $headers .= 'Reply-To: ' . $from . "\r\n";

                if (@mail($to, $subject, $message, $headers))
                {
                    $sent_count++;
                }
            }

            echo '<div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>
                        <strong>Newsletter sent successfully!</strong><br>
                        Sent to ' . $sent_count . ' out of ' . $subscriber_count . ' subscribers.
                    </div>
                  </div>';
        }
        ?>
        <form action="" method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Email Title</label>
                <input class="form-control" name="title" id="title" type="text" placeholder="Enter newsletter title..."
                    required>
            </div>
            <div class="mb-3">
                <label for="summernote" class="form-label">Email Content</label>
                <textarea class="form-control" id="summernote" name="content"
                    placeholder="Write your newsletter content here..." required></textarea>
            </div>
            <div>
                <button type="submit" name="send_mass_message" class="btn btn-success">
                    <i class="fas fa-paper-plane me-2"></i>Send Newsletter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Search Bar: moved outside the card for consistency with accounts.php -->
<div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
    <form method="get" class="search" style="position: relative; max-width: 280px; width: 100%;">
        <label for="search_email" style="width:100%;margin-bottom:0;">
            <input id="search_email" type="text" name="search" placeholder="Search subscribers..."
                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                class="responsive-width-100" aria-label="Search subscribers"
                style="background:#222;color:#fff;border:1px solid #444;padding:6px 36px 6px 16px;border-radius:20px;width:100%;font-size:16px;transition:none;box-shadow:none;outline:none;height:36px;"
                onfocus="this.style.background='#222';this.style.color='#fff';this.style.borderColor='#3a6ea5';"
                onblur="this.style.background='#222';this.style.color='#fff';this.style.borderColor='#444';"
                autocomplete="off">
            <style>
                #search_email::placeholder {
                    color: #fff !important;
                    opacity: 1;
                }
            </style>
            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); color: #fff; fill: #fff; pointer-events: none;">
                <path
                    d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" />
            </svg>
        </label>
    </form>
</div>

<div class="card">
    <h6 class="card-header"><i class="fas fa-users me-2"></i>Newsletter Subscribers</h6>
    <div class="card-body">
        <?php
        // Get subscriber statistics
        $stmt_stats = $pdo->query("SELECT COUNT(*) as total FROM blog_newsletter");
        $total_subscribers = $stmt_stats->fetch(PDO::FETCH_ASSOC)['total'];

        if ($total_subscribers > 0): ?>
            <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                    <strong>Subscriber Stats:</strong> Currently <?= $total_subscribers ?> active newsletter subscribers.
                </div>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <?php
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            if ($search !== '')
            {
                echo '<div class="alert alert-info d-flex align-items-center mb-2" role="alert" style="justify-content: space-between;">
                        <div><i class="fas fa-filter me-2"></i>Showing results for <strong>' . htmlspecialchars($search) . '</strong></div>
                        <a href="newsletter.php" class="btn btn-sm btn-outline-secondary ms-2">Clear Filter</a>
                    </div>';
            }
            ?>
        <div class="card-body p-0">
            <div class="table" role="table" aria-label="Newsletter Subscribers">
                <table role="grid">
                    <thead role="rowgroup">
                        <tr role="row">
                            <th class="text-left" style="text-align: left;" role="columnheader" scope="col">
                                <?php $q = $_GET; $q['order_by'] = 'email'; $q['order'] = ($order_by == 'email' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
                                <a href="?<?= http_build_query($q) ?>" class="sort-header">Email<?= $order_by == 'email' ? $table_icons[strtolower($order)] : '' ?></a>
                            </th>
                            <th style="text-align: center;" role="columnheader" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody role="rowgroup">
                    <?php
                    if ($search !== '')
                    {
                        $stmt = $pdo->prepare("SELECT * FROM blog_newsletter WHERE email LIKE ? ORDER BY $order_by_sql $order");
                        $stmt->execute(['%' . $search . '%']);
                    } else
                    {
                        $stmt = $pdo->query("SELECT * FROM blog_newsletter ORDER BY $order_by_sql $order");
                    }
                    $newsletter_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($newsletter_rows as $row)
                    {
                        echo '
                    <tr role="row">
                        <td class="text-left" role="gridcell">' . htmlspecialchars($row['email']) . '</td>
                        <td class="actions" style="text-align: center;" role="gridcell">
                            <div class="table-dropdown">
                                <button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for subscriber ' . htmlspecialchars($row['email']) . '">
                                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
                                        <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                    </svg>
                                </button>
                                <div class="table-dropdown-items" role="menu" aria-label="Subscriber Actions">
                                    <div role="menuitem">
                                        <a href="?unsubscribe=' . urlencode($row['email']) . '" class="red" tabindex="-1" onclick="return confirm(\'Are you sure you want to unsubscribe ' . htmlspecialchars($row['email']) . ' from the newsletter?\')" aria-label="Unsubscribe ' . htmlspecialchars($row['email']) . '">
                                            <i class="fas fa-user-slash" aria-hidden="true"></i>
                                            <span>&nbsp;Unsubscribe</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-light">
        <div class="small">
            Total subscribers: <?= count($newsletter_rows) ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#summernote').summernote({ height: 350 });
        var noteBar = $('.note-toolbar');
        noteBar.find('[data-toggle]').each(function () {
            $(this).attr('data-bs-toggle', $(this).attr('data-toggle')).removeAttr('data-toggle');
        });
    });
</script>
<?= template_admin_footer(); ?>