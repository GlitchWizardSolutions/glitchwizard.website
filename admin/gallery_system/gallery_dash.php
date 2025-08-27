<?php
include '../assets/includes/main.php';
// Current date in MySQL DATETIME format
$date = date('Y-m-d H:i:s');
// New media updated on the current date
$media = $pdo->query('SELECT m.*, a.email, a.username FROM gallery_media m LEFT JOIN accounts a ON a.id = m.account_id WHERE cast(m.uploaded_date as DATE) = cast("' . $date . '" as DATE) ORDER BY m.uploaded_date DESC')->fetchAll(PDO::FETCH_ASSOC);
// Media awaiting approval
$media_awaiting_approval = $pdo->query('SELECT m.*, a.email, a.username FROM gallery_media m LEFT JOIN accounts a ON a.id = m.account_id WHERE m.is_approved = 0 ORDER BY m.uploaded_date DESC')->fetchAll(PDO::FETCH_ASSOC);
// Total media count
$media_total = $pdo->query('SELECT COUNT(*) as total FROM gallery_media')->fetchColumn();
// Approved media count
$media_approved = $pdo->query('SELECT COUNT(*) as total FROM gallery_media WHERE is_approved = 1')->fetchColumn();
// Most liked media
$media_most_liked = $pdo->query('SELECT m.*, a.email, a.username, COUNT(l.id) AS total_likes FROM gallery_media m LEFT JOIN accounts a ON a.id = m.account_id LEFT JOIN gallery_media_likes l ON l.media_id = m.id WHERE m.is_approved = 1 GROUP BY m.id ORDER BY total_likes DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);
// Get sales analytics
if (isset($_GET['date_range'])) {
    $days = (int)$_GET['date_range'];
    $start = new DateTime();
    $start->modify("-{$days} days");
    $end = new DateTime();
} elseif (isset($_GET['date_start'], $_GET['date_end'])) {
    $start = new DateTime($_GET['date_start']);
    $end = new DateTime($_GET['date_end']);
    if ($end <= $start) {
        $end = new DateTime($_GET['date_start']);
    }
} else {
    $start = new DateTime();         
    $start->modify('-6 days'); 
    $end = new DateTime();
}
$start->setTime(0, 0, 0);
$end->setTime(23, 59, 59);
$period = new DatePeriod($start, new DateInterval('P1D'), $end);
$allDates = [];
foreach ($period as $dt) {
    $allDates[$dt->format('Y-m-d')] = 0;
}
$media_analytics_total_uploads = 0;
$media_analytics_total_data = 0;
$media_analytics_total_images = 0;
$media_analytics_total_videos = 0;
$media_analytics_total_audios = 0;
$upload_results = $pdo->query('SELECT DATE(uploaded_date) AS uploaded_date, COUNT(*) AS uploads, COUNT(CASE WHEN media_type = "image" THEN 1 END) AS total_images, COUNT(CASE WHEN media_type = "video" THEN 1 END) AS total_videos, COUNT(CASE WHEN media_type = "audio" THEN 1 END) AS total_audios, GROUP_CONCAT(filepath) AS filepaths FROM gallery_media WHERE uploaded_date BETWEEN "' . $start->format('Y-m-d 00:00:00') . '" AND "' . $end->format('Y-m-d 23:59:59') . '" GROUP BY DATE(uploaded_date)')->fetchAll(PDO::FETCH_ASSOC);
foreach ($upload_results as $row) {
    $allDates[$row['uploaded_date']] = (int)$row['uploads'];
    $media_analytics_total_uploads += (int)$row['uploads'];
    $media_analytics_total_images += (int)$row['total_images'];
    $media_analytics_total_videos += (int)$row['total_videos'];
    $media_analytics_total_audios += (int)$row['total_audios'];
    foreach (explode(',', $row['filepaths']) as $filepath) {
        if (!file_exists('../' . $filepath)) continue;
        $media_analytics_total_data += filesize('../' . $filepath);
    }
}
$stats = [];
foreach ($allDates as $date => $uploads) {
    $stats[] = ['date' => $date, 'uploads' => $uploads];
}
$max_uploads = max(array_column($stats, 'uploads'));
$max_uploads = $max_uploads < 15 ? 15 : $max_uploads;
$num_increments = 5;
$raw_step = $max_uploads / $num_increments;
if ($raw_step <= 5) {
    $step = 5;
} elseif ($raw_step <= 10) {
    $step = 10;
} elseif ($raw_step <= 20) {
    $step = 10 * ceil($raw_step / 10);
} else {
    $step = 10 * ceil($raw_step / 10);
}
$increments = [];
for ($i = 0; $i <= $max_uploads; $i += $step) {
    $increments[] = $i;
}
if (end($increments) < $max_uploads) {
    $increments[] = end($increments) + $step;
}
?>
<?=template_admin_header('Gallery Dashboard', 'gallery', 'dashboard')?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-4 px-4 branding-settings-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <span class="header-icon"><i class="bi bi-images" aria-hidden="true"></i></span>
                    Gallery Dashboard
                </h6>
                <span class="text-white" style="font-size: 0.875rem;">Media Management</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="container-fluid py-3 px-4">
 
<!--GALLERY SYSTEM OVERVIEW-->
<div class="dashboard-apps">
    <!-- Gallery Statistics Card -->
    <div class="app-card" role="region" aria-labelledby="gallery-stats-title">
        <div class="app-header gallery-header" role="banner" aria-labelledby="gallery-stats-title">
            <h3 id="gallery-stats-title">Gallery Statistics</h3>
            <i class="bi bi-images header-icon" aria-hidden="true"></i>
            <span class="badge" aria-label="<?= number_format($media_total) ?> total media"><?= number_format($media_total) ?> total</span>
        </div>
        <div class="app-body">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value"><?= $media ? number_format(count($media)) : 0 ?></div>
                    <div class="stat-label">New Media</div>
                    <div class="stat-sublabel">Media &lt;1 day old</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= $media_awaiting_approval ? number_format(count($media_awaiting_approval)) : 0 ?></div>
                    <div class="stat-label">Awaiting Approval</div>
                    <div class="stat-sublabel">Pending review</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= number_format($media_approved) ?></div>
                    <div class="stat-label">Approved</div>
                    <div class="stat-sublabel">Live media items</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value"><?= convert_filesize(dir_size('../media')) ?></div>
                    <div class="stat-label">Total Size</div>
                    <div class="stat-sublabel">Storage used</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Actions Card -->
    <div class="app-card" role="region" aria-labelledby="gallery-actions-title">
        <div class="app-header blog-header" role="banner" aria-labelledby="gallery-actions-title">
            <h3 id="gallery-actions-title">Quick Actions</h3>
            <i class="bi bi-lightning-charge-fill header-icon" aria-hidden="true"></i>
        </div>
        <div class="app-body">
            <div class="action-items">
                <a href="media.php" class="action-item info">
                    <div class="action-icon">
                        <i class="bi bi-upload" aria-hidden="true"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">Upload Media</div>
                        <div class="action-description">Add new images, videos, or audio</div>
                    </div>
                </a>
                <a href="albums.php" class="action-item success">
                    <div class="action-icon">
                        <i class="bi bi-folder-plus" aria-hidden="true"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">Create Album</div>
                        <div class="action-description">Organize media into collections</div>
                    </div>
                </a>
                <a href="media.php?awaiting_approval=1" class="action-item warning">
                    <div class="action-icon">
                        <i class="bi bi-clock" aria-hidden="true"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">Review Pending</div>
                        <div class="action-description">Approve or reject uploads</div>
                    </div>
                </a>
                <a href="settings.php" class="action-item secondary">
                    <div class="action-icon">
                        <i class="bi bi-gear" aria-hidden="true"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">Gallery Settings</div>
                        <div class="action-description">Configure upload limits and permissions</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Media Uploads Analytics and Most Liked Row -->
<div class="row mt-4">
    <!-- Media Uploads Analytics Card -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100" id="analytics-card" role="region" aria-labelledby="analytics-heading">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="icon me-2" aria-hidden="true">
                        <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M3,22V8H7V22H3M10,22V2H14V22H10M17,22V14H21V22H17Z" /></svg>
                    </div>
                    <h3 id="analytics-heading" class="card-title mb-0">Media Uploads Analytics</h3>
                </div>
                <form action="gallery_dash.php#analytics-card" method="get" class="d-flex align-items-center">
                    <select name="date_range" onchange="this.form.submit()" class="form-select form-select-sm" style="width: auto;">
                        <option value="7" <?=(!isset($_GET['date_range']) || $_GET['date_range'] == '7') ? 'selected' : ''?>>Last week</option>
                        <option value="14" <?=isset($_GET['date_range']) && $_GET['date_range'] == '14' ? 'selected' : ''?>>Last 2 weeks</option>
                        <option value="30" <?=isset($_GET['date_range']) && $_GET['date_range'] == '30' ? 'selected' : ''?>>Last month</option>
                        <option value="90" <?=isset($_GET['date_range']) && $_GET['date_range'] == '90' ? 'selected' : ''?>>Last 3 months</option>
                        <option value="365" <?=isset($_GET['date_range']) && $_GET['date_range'] == '365' ? 'selected' : ''?>>Last year</option>
                    </select>
                </form>
            </div>
            <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col">
                        <h3 class="mb-0 text-primary"><?=$media_analytics_total_uploads ? number_format($media_analytics_total_uploads) : 0?></h3>
                        <small class="text-muted">Uploads</small>
                    </div>
                    <div class="col">
                        <h3 class="mb-0 text-info"><?=convert_filesize($media_analytics_total_data)?></h3>
                        <small class="text-muted">Data</small>
                    </div>
                    <div class="col">
                        <h3 class="mb-0 text-success"><?=$media_analytics_total_images ? number_format($media_analytics_total_images) : 0?></h3>
                        <small class="text-muted">Images</small>
                    </div>
                    <div class="col">
                        <h3 class="mb-0 text-warning"><?=$media_analytics_total_videos ? number_format($media_analytics_total_videos) : 0?></h3>
                        <small class="text-muted">Videos</small>
                    </div>
                    <div class="col">
                        <h3 class="mb-0 text-secondary"><?=$media_analytics_total_audios ? number_format($media_analytics_total_audios) : 0?></h3>
                        <small class="text-muted">Audios</small>
                    </div>
                </div>
                
                <!-- Daily Upload Activity Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-center">Uploads</th>
                                <th class="text-center">Images</th>
                                <th class="text-center">Videos</th>
                                <th class="text-center">Audios</th>
                                <th class="text-end">Activity Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($stats) || array_sum(array_column($stats, 'uploads')) == 0): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    No media uploads during this period
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php 
                            $max_daily = max(array_column($stats, 'uploads'));
                            foreach ($stats as $day): 
                                // Get detailed stats for this day
                                $day_details = null;
                                foreach ($upload_results as $result) {
                                    if ($result['uploaded_date'] == $day['date']) {
                                        $day_details = $result;
                                        break;
                                    }
                                }
                                $images = $day_details ? $day_details['total_images'] : 0;
                                $videos = $day_details ? $day_details['total_videos'] : 0;
                                $audios = $day_details ? $day_details['total_audios'] : 0;
                                
                                // Calculate activity level
                                $activity_percent = $max_daily > 0 ? ($day['uploads'] / $max_daily) * 100 : 0;
                                $activity_class = '';
                                $activity_text = '';
                                if ($activity_percent == 0) {
                                    $activity_class = 'text-muted';
                                    $activity_text = 'No activity';
                                } elseif ($activity_percent <= 25) {
                                    $activity_class = 'text-info';
                                    $activity_text = 'Low';
                                } elseif ($activity_percent <= 75) {
                                    $activity_class = 'text-warning';
                                    $activity_text = 'Medium';
                                } else {
                                    $activity_class = 'text-success';
                                    $activity_text = 'High';
                                }
                            ?>
                            <tr>
                                <td><?=date('M j, Y', strtotime($day['date']))?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?=$day['uploads']?></span>
                                </td>
                                <td class="text-center"><?=$images?></td>
                                <td class="text-center"><?=$videos?></td>
                                <td class="text-center"><?=$audios?></td>
                                <td class="text-end">
                                    <span class="<?=$activity_class?>"><?=$activity_text?></span>
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar <?=str_replace('text-', 'bg-', $activity_class)?>" 
                                             style="width: <?=$activity_percent?>%"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Liked Media Card -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100" role="region" aria-labelledby="most-liked-heading">
            <div class="card-header d-flex align-items-center">
                <div class="icon me-2" aria-hidden="true">
                    <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M16,6L18.29,8.29L13.41,13.17L9.41,9.17L2,16.59L3.41,18L9.41,12L13.41,16L19.71,9.71L22,12V6H16Z" /></svg>
                </div>
                <h3 id="most-liked-heading" class="card-title mb-0">Most Liked</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" role="table" aria-label="Most liked media">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" colspan="2">Media</th>
                                <th scope="col" class="text-end">Likes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($media_most_liked)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <svg width="24" height="24" class="mb-2 opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,21.35L10.55,20.03C5.4,15.36 2,12.27 2,8.5C2,5.41 4.42,3 7.5,3C9.24,3 10.91,3.81 12,5.08C13.09,3.81 14.76,3 16.5,3C19.58,3 22,5.41 22,8.5C22,12.27 18.6,15.36 13.45,20.03L12,21.35Z" /></svg>
                                        <span>There are no liked media.</span>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($media_most_liked as $m): ?>
                            <tr>
                                <td class="align-middle" style="width: 60px;">
                                    <a href="#" class="media-img open-media-modal d-block" data-type="<?=$m['media_type']?>" data-filepath="<?=htmlspecialchars('../' . $m['filepath'], ENT_QUOTES)?>" title="View Media" aria-label="View <?=htmlspecialchars($m['title'], ENT_QUOTES)?>">
                                        <?php if ($m['media_type'] == 'image' && file_exists('../' . $m['filepath'])): ?>
                                        <img src="../<?=$m['filepath']?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" class="rounded" width="40" height="40" style="object-fit: cover;">
                                        <?php elseif (!empty($m['thumbnail']) && file_exists('../' . $m['thumbnail'])): ?>
                                        <img src="../<?=$m['thumbnail']?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" class="rounded" width="40" height="40" style="object-fit: cover;">
                                        <?php elseif ($m['media_type'] == 'video'): ?>
                                        <span class="placeholder d-flex align-items-center justify-content-center bg-light border rounded" style="width: 40px; height: 40px;">
                                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM48 368l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 240l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 112l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16L64 96c-8.8 0-16 7.2-16 16zM416 96c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM160 128l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32L192 96c-17.7 0-32 14.3-32 32zm32 160c-17.7 0-32 14.3-32 32l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32l-128 0z"/></svg>
                                        </span>
                                        <?php elseif ($m['media_type'] == 'audio'): ?>
                                        <span class="placeholder d-flex align-items-center justify-content-center bg-light border rounded" style="width: 40px; height: 40px;">
                                            <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M499.1 6.3c8.1 6 12.9 15.6 12.9 25.7l0 72 0 264c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L448 147 192 223.8 192 432c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L128 200l0-72c0-14.1 9.3-26.6 22.8-30.7l320-96c9.7-2.9 20.2-1.1 28.3 5z"/></svg>
                                        </span>
                                        <?php else: ?>
                                        <span class="placeholder d-flex align-items-center justify-content-center bg-danger text-white rounded" style="width: 40px; height: 40px;" title="File not found">
                                            <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                                        </span>
                                        <?php endif; ?>
                                    </a>
                                </td>
                                <td class="align-middle">
                                    <a href="#" class="open-media-modal text-decoration-none<?=!file_exists('../' . $m['filepath']) ? ' text-danger' : ''?>" data-type="<?=$m['media_type']?>" data-filepath="<?=htmlspecialchars('../' . $m['filepath'], ENT_QUOTES)?>" title="View Media">
                                        <div class="fw-medium"><?=htmlspecialchars($m['title'], ENT_QUOTES)?>
                                            <?php if (!$m['is_public']): ?>
                                            <svg width="12" height="12" class="ms-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><title>Private Media</title><path d="M144 144l0 48 160 0 0-48c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192l0-48C80 64.5 144.5 0 224 0s144 64.5 144 144l0 48 16 0c35.3 0 64 28.7 64 64l0 192c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 256c0-35.3 28.7-64 64-64l16 0z"/></svg>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted">
                                            <?php if (file_exists('../' . $m['filepath'])): ?>
                                            <?=mime_content_type('../' . $m['filepath'])?>, <?=convert_filesize(filesize('../' . $m['filepath']))?>
                                            <?php else: ?>
                                            (File not found)
                                            <?php endif; ?>
                                        </small>
                                    </a>
                                </td>
                                <td class="text-end align-middle">
                                    <span class="badge bg-secondary"><?=$m['total_likes'] ? number_format($m['total_likes']) : 0?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- New Media Section -->
<div class="content-title-block mt-4" role="region" aria-labelledby="new-media-heading">
    <div class="content-title">
        <div class="title">
            <div class="icon alt" aria-hidden="true">
                <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M160 80l352 0c8.8 0 16 7.2 16 16l0 224c0 8.8-7.2 16-16 16l-21.2 0L388.1 178.9c-4.4-6.8-12-10.9-20.1-10.9s-15.7 4.1-20.1 10.9l-52.2 79.8-12.4-16.9c-4.5-6.2-11.7-9.8-19.4-9.8s-14.8 3.6-19.4 9.8L175.6 336 160 336c-8.8 0-16-7.2-16-16l0-224c0-8.8 7.2-16 16-16zM96 96l0 224c0 35.3 28.7 64 64 64l352 0c35.3 0 64-28.7 64-64l0-224c0-35.3-28.7-64-64-64L160 32c-35.3 0-64 28.7-64 64zM48 120c0-13.3-10.7-24-24-24S0 106.7 0 120L0 344c0 75.1 60.9 136 136 136l320 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-320 0c-48.6 0-88-39.4-88-88l0-224zm208 24a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/></svg>
            </div>
            <div class="txt">
                <h2 id="new-media-heading">New Media</h2>
                <p>Media uploaded in the last &lt;1 day.</p>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" role="table" aria-label="New media files">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" colspan="2">Media</th>
                            <th scope="col" class="d-none d-md-table-cell">Account</th>
                            <th scope="col" class="d-none d-md-table-cell">Type</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="d-none d-md-table-cell">Date</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($media)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <svg width="24" height="24" class="mb-2 opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M160 80l352 0c8.8 0 16 7.2 16 16l0 224c0 8.8-7.2 16-16 16l-21.2 0L388.1 178.9c-4.4-6.8-12-10.9-20.1-10.9s-15.7 4.1-20.1 10.9l-52.2 79.8-12.4-16.9c-4.5-6.2-11.7-9.8-19.4-9.8s-14.8 3.6-19.4 9.8L175.6 336 160 336c-8.8 0-16-7.2-16-16l0-224c0-8.8 7.2-16 16-16zM96 96l0 224c0 35.3 28.7 64 64 64l352 0c35.3 0 64-28.7 64-64l0-224c0-35.3-28.7-64-64-64L160 32c-35.3 0-64 28.7-64 64zM48 120c0-13.3-10.7-24-24-24S0 106.7 0 120L0 344c0 75.1 60.9 136 136 136l320 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-320 0c-48.6 0-88-39.4-88-88l0-224z"/></svg>
                                    <span>There are no recent media files.</span>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($media as $m): ?>
                        <tr>
                            <td class="align-middle" style="width: 60px;">
                                <a href="#" class="media-img open-media-modal d-block" data-type="<?=$m['media_type']?>" data-filepath="<?=htmlspecialchars('../' . $m['filepath'], ENT_QUOTES)?>" title="View Media" aria-label="View <?=htmlspecialchars($m['title'], ENT_QUOTES)?>">
                                    <?php if ($m['media_type'] == 'image' && file_exists('../' . $m['filepath'])): ?>
                                    <img src="../<?=$m['filepath']?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" class="rounded" width="40" height="40" style="object-fit: cover;">
                                    <?php elseif (!empty($m['thumbnail']) && file_exists('../' . $m['thumbnail'])): ?>
                                    <img src="../<?=$m['thumbnail']?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" class="rounded" width="40" height="40" style="object-fit: cover;">
                                    <?php elseif ($m['media_type'] == 'video'): ?>
                                    <span class="placeholder d-flex align-items-center justify-content-center bg-light border rounded" style="width: 40px; height: 40px;">
                                        <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM48 368l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 240l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 112l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16L64 96c-8.8 0-16 7.2-16 16zM416 96c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM160 128l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32L192 96c-17.7 0-32 14.3-32 32zm32 160c-17.7 0-32 14.3-32 32l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32l-128 0z"/></svg>
                                    </span>
                                    <?php elseif ($m['media_type'] == 'audio'): ?>
                                    <span class="placeholder d-flex align-items-center justify-content-center bg-light border rounded" style="width: 40px; height: 40px;">
                                        <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M499.1 6.3c8.1 6 12.9 15.6 12.9 25.7l0 72 0 264c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L448 147 192 223.8 192 432c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L128 200l0-72c0-14.1 9.3-26.6 22.8-30.7l320-96c9.7-2.9 20.2-1.1 28.3 5z"/></svg>
                                    </span>
                                    <?php else: ?>
                                    <span class="placeholder d-flex align-items-center justify-content-center bg-danger text-white rounded" style="width: 40px; height: 40px;" title="File not found">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                                    </span>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td class="align-middle">
                                <a href="#" class="open-media-modal text-decoration-none<?=!file_exists('../' . $m['filepath']) ? ' text-danger' : ''?>" data-type="<?=$m['media_type']?>" data-filepath="<?=htmlspecialchars('../' . $m['filepath'], ENT_QUOTES)?>" title="View Media">
                                    <div class="fw-medium"><?=htmlspecialchars($m['title'], ENT_QUOTES)?>
                                        <?php if (!$m['is_public']): ?>
                                        <svg width="12" height="12" class="ms-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><title>Private Media</title><path d="M144 144l0 48 160 0 0-48c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192l0-48C80 64.5 144.5 0 224 0s144 64.5 144 144l0 48 16 0c35.3 0 64 28.7 64 64l0 192c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 256c0-35.3 28.7-64 64-64l16 0z"/></svg>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">
                                        <?php if (file_exists('../' . $m['filepath'])): ?>
                                        <?=mime_content_type('../' . $m['filepath'])?>, <?=convert_filesize(filesize('../' . $m['filepath']))?>
                                        <?php else: ?>
                                        (File not found)
                                        <?php endif; ?>
                                    </small>
                                </a>
                            </td>
                            <td class="d-none d-md-table-cell align-middle">
                                <?php if ($m['acc_id']): ?>
                                <div>
                                    <div class="fw-medium"><?=htmlspecialchars($m['display_name'], ENT_QUOTES)?></div>
                                    <small><a href="account.php?id=<?=$m['acc_id']?>" class="text-decoration-none"><?=htmlspecialchars($m['email'], ENT_QUOTES)?> [<?=htmlspecialchars($m['acc_id'], ENT_QUOTES)?>]</a></small>
                                </div>
                                <?php else: ?>
                                <span class="text-muted">--</span>
                                <?php endif; ?>
                            </td>
                            <td class="d-none d-md-table-cell align-middle">
                                <span class="badge bg-secondary"><?=ucfirst($m['media_type'])?></span>
                            </td>
                            <td class="align-middle">
                                <?=$m['is_approved']?'<span class="badge bg-success">Approved</span>':'<span class="badge bg-warning">Awaiting Approval</span>'?>
                            </td>
                            <td class="d-none d-md-table-cell align-middle">
                                <small class="text-muted"><?=date('M j, Y g:ia', strtotime($m['uploaded_date']))?></small>
                            </td>
                            <td class="align-middle">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/></svg>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="media.php?id=<?=$m['id']?>">
                                            <svg width="12" height="12" class="me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/></svg>
                                            Edit
                                        </a></li>
                                        <?php if (!$m['is_approved']): ?>
                                        <li><a class="dropdown-item text-success" href="allmedia.php?approve=<?=$m['id']?>" onclick="return confirm('Are you sure you want to approve this media?')">
                                            <svg width="16" height="16" class="me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>
                                            Approve
                                        </a></li>
                                        <?php endif; ?>
                                        <?php if (file_exists('../' . $m['filepath'])): ?>
                                        <li><a class="dropdown-item" href="../<?=$m['filepath']?>" target="_blank" download>
                                            <svg width="16" height="16" class="me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M2 12H4V17H20V12H22V17C22 18.11 21.11 19 20 19H4C2.9 19 2 18.11 2 17V12M12 15L17.55 9.54L16.13 8.13L13 11.25V2H11V11.25L7.88 8.13L6.46 9.55L12 15Z" /></svg>
                                            Download
                                        </a></li>
                                        <?php endif; ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="allmedia.php?delete=<?=$m['id']?>" onclick="return confirm('Are you sure you want to delete this media?')">
                                            <svg width="12" height="12" class="me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                                            Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Media Awaiting Approval Section -->
<div class="content-title-block mt-4" role="region" aria-labelledby="awaiting-approval-heading">
    <div class="content-title">
        <div class="title">
            <div class="icon alt" aria-hidden="true">
                <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M75 75L41 41C25.9 25.9 0 36.6 0 57.9L0 168c0 13.3 10.7 24 24 24l110.1 0c21.4 0 32.1-25.9 17-41l-30.8-30.8C155 85.5 203 64 256 64c106 0 192 86 192 192s-86 192-192 192c-40.8 0-78.6-12.7-109.7-34.4c-14.5-10.1-34.4-6.6-44.6 7.9s-6.6 34.4 7.9 44.6C151.2 495 201.7 512 256 512c141.4 0 256-114.6 256-256S397.4 0 256 0C185.3 0 121.3 28.7 75 75zm181 53c-13.3 0-24 10.7-24 24l0 104c0 6.4 2.5 12.5 7 17l72 72c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-65-65 0-94.1c0-13.3-10.7-24-24-24z"/></svg>
            </div>
            <div class="txt">
                <h2 id="awaiting-approval-heading">Media Awaiting Approval</h2>
                <p>Media awaiting admin approval.</p>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" role="table" aria-label="Media awaiting approval">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" colspan="2">Media</th>
                            <th scope="col" class="d-none d-md-table-cell">Account</th>
                            <th scope="col" class="d-none d-md-table-cell">Type</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="d-none d-md-table-cell">Date</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($media_awaiting_approval)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <div class="d-flex flex-column align-items-center">
                                    <svg width="24" height="24" class="mb-2 opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M75 75L41 41C25.9 25.9 0 36.6 0 57.9L0 168c0 13.3 10.7 24 24 24l110.1 0c21.4 0 32.1-25.9 17-41l-30.8-30.8C155 85.5 203 64 256 64c106 0 192 86 192 192s-86 192-192 192c-40.8 0-78.6-12.7-109.7-34.4c-14.5-10.1-34.4-6.6-44.6 7.9s-6.6 34.4 7.9 44.6C151.2 495 201.7 512 256 512c141.4 0 256-114.6 256-256S397.4 0 256 0C185.3 0 121.3 28.7 75 75z"/></svg>
                                    <span>There are no media files awaiting approval.</span>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($media_awaiting_approval as $m): ?>
                        <tr>
                            <td class="align-middle" style="width: 60px;">
                                <a href="#" class="media-img open-media-modal d-block" data-type="<?=$m['media_type']?>" data-filepath="<?=htmlspecialchars('../' . $m['filepath'], ENT_QUOTES)?>" title="View Media" aria-label="View <?=htmlspecialchars($m['title'], ENT_QUOTES)?>">
                                    <?php if ($m['media_type'] == 'image' && file_exists('../' . $m['filepath'])): ?>
                                    <img src="../<?=$m['filepath']?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" class="rounded" width="40" height="40" style="object-fit: cover;">
                                    <?php elseif (!empty($m['thumbnail']) && file_exists('../' . $m['thumbnail'])): ?>
                                    <img src="../<?=$m['thumbnail']?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" class="rounded" width="40" height="40" style="object-fit: cover;">
                                    <?php elseif ($m['media_type'] == 'video'): ?>
                                    <span class="placeholder d-flex align-items-center justify-content-center bg-light border rounded" style="width: 40px; height: 40px;">
                                        <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM48 368l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 240l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 112l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16L64 96c-8.8 0-16 7.2-16 16zM416 96c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM160 128l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32L192 96c-17.7 0-32 14.3-32 32zm32 160c-17.7 0-32 14.3-32 32l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32l-128 0z"/></svg>
                                    </span>
                                    <?php elseif ($m['media_type'] == 'audio'): ?>
                                    <span class="placeholder d-flex align-items-center justify-content-center bg-light border rounded" style="width: 40px; height: 40px;">
                                        <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M499.1 6.3c8.1 6 12.9 15.6 12.9 25.7l0 72 0 264c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L448 147 192 223.8 192 432c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L128 200l0-72c0-14.1 9.3-26.6 22.8-30.7l320-96c9.7-2.9 20.2-1.1 28.3 5z"/></svg>
                                    </span>
                                    <?php else: ?>
                                    <span class="placeholder d-flex align-items-center justify-content-center bg-danger text-white rounded" style="width: 40px; height: 40px;" title="File not found">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>
                                    </span>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td class="align-middle">
                                <a href="#" class="open-media-modal text-decoration-none<?=!file_exists('../' . $m['filepath']) ? ' text-danger' : ''?>" data-type="<?=$m['media_type']?>" data-filepath="<?=htmlspecialchars('../' . $m['filepath'], ENT_QUOTES)?>" title="View Media">
                                    <div class="fw-medium"><?=htmlspecialchars($m['title'], ENT_QUOTES)?>
                                        <?php if (!$m['is_public']): ?>
                                        <svg width="12" height="12" class="ms-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><title>Private Media</title><path d="M144 144l0 48 160 0 0-48c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192l0-48C80 64.5 144.5 0 224 0s144 64.5 144 144l0 48 16 0c35.3 0 64 28.7 64 64l0 192c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 256c0-35.3 28.7-64 64-64l16 0z"/></svg>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">
                                        <?php if (file_exists('../' . $m['filepath'])): ?>
                                        <?=mime_content_type('../' . $m['filepath'])?>, <?=convert_filesize(filesize('../' . $m['filepath']))?>
                                        <?php else: ?>
                                        (File not found)
                                        <?php endif; ?>
                                    </small>
                                </a>
                            </td>
                            <td class="d-none d-md-table-cell align-middle">
                                <?php if ($m['acc_id']): ?>
                                <div>
                                    <div class="fw-medium"><?=htmlspecialchars($m['display_name'], ENT_QUOTES)?></div>
                                    <small><a href="account.php?id=<?=$m['acc_id']?>" class="text-decoration-none"><?=htmlspecialchars($m['email'], ENT_QUOTES)?> [<?=htmlspecialchars($m['acc_id'], ENT_QUOTES)?>]</a></small>
                                </div>
                                <?php else: ?>
                                <span class="text-muted">--</span>
                                <?php endif; ?>
                            </td>
                            <td class="d-none d-md-table-cell align-middle">
                                <span class="badge bg-secondary"><?=ucfirst($m['media_type'])?></span>
                            </td>
                            <td class="align-middle">
                                <?=$m['is_approved']?'<span class="badge bg-success">Approved</span>':'<span class="badge bg-warning">Awaiting Approval</span>'?>
                            </td>
                            <td class="d-none d-md-table-cell align-middle">
                                <small class="text-muted"><?=date('M j, Y g:ia', strtotime($m['uploaded_date']))?></small>
                            </td>
                            <td class="align-middle">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/></svg>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="media.php?id=<?=$m['id']?>">
                                            <svg width="12" height="12" class="me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/></svg>
                                            Edit
                                        </a></li>
                                        <?php if (!$m['is_approved']): ?>
                                        <li><a class="dropdown-item text-success" href="allmedia.php?approve=<?=$m['id']?>" onclick="return confirm('Are you sure you want to approve this media?')">
                                            <svg width="16" height="16" class="me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>
                                            Approve
                                        </a></li>
                                        <?php endif; ?>
                                        <?php if (file_exists('../' . $m['filepath'])): ?>
                                        <li><a class="dropdown-item" href="../<?=$m['filepath']?>" target="_blank" download>
                                            <svg width="16" height="16" class="me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M2 12H4V17H20V12H22V17C22 18.11 21.11 19 20 19H4C2.9 19 2 18.11 2 17V12M12 15L17.55 9.54L16.13 8.13L13 11.25V2H11V11.25L7.88 8.13L6.46 9.55L12 15Z" /></svg>
                                            Download
                                        </a></li>
                                        <?php endif; ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="allmedia.php?delete=<?=$m['id']?>" onclick="return confirm('Are you sure you want to delete this media?')">
                                            <svg width="12" height="12" class="me-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                                            Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>
</div>

<?=template_admin_footer()?>