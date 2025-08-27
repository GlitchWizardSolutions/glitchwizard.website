<?php
include '../assets/includes/main.php';

// Use simple triangle icons for sort direction, matching accounts.php
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>', // ▲
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>' // ▼
];

// Retrieve the GET request parameters (if specified)
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$search = isset($_GET['search_query']) ? $_GET['search_query'] : '';
// Filters parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$media_type = isset($_GET['media_type']) ? $_GET['media_type'] : '';
$is_public = isset($_GET['is_public']) ? $_GET['is_public'] : '';
$acc_id = isset($_GET['acc_id']) ? $_GET['acc_id'] : '';
$collection_id = isset($_GET['collection_id']) ? $_GET['collection_id'] : '';
// Order by column
$order = isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'DESC' : 'ASC';
// Add/remove columns to the whitelist array
$order_by_whitelist = ['id','title','media_type','description_text','uploaded_date','is_approved','account_id','username','email','likes'];
$order_by = isset($_GET['order_by']) && in_array($_GET['order_by'], $order_by_whitelist) ? $_GET['order_by'] : 'id';
// Number of results per pagination page
$results_per_page = 15;
// Declare query param variables
$param1 = ($page - 1) * $results_per_page;
$param2 = $results_per_page;
$param3 = '%' . $search . '%';
// SQL where clause
$where = '';
$where .= $search ? 'WHERE (m.title LIKE :search OR m.filepath = :search) ' : '';
// Status filter
if ($status == 'Approved') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'm.is_approved = 1 ';
}
if ($status == 'Awaiting Approval') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'm.is_approved = 0 ';
}
// Date filter
if ($date_from) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'm.uploaded_date >= :date_from ';
}
if ($date_to) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'm.uploaded_date <= :date_to ';
}
// Media type filter
if ($media_type) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'm.media_type = :media_type ';
}
// Is public filter
if ($is_public !== '') {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'm.is_public = :is_public ';
}
// Account ID filter
if ($acc_id) {
    $where .= ($where ? 'AND ' : 'WHERE ') . 'm.acc_id = :acc_id ';
}
// Collection ID filter
$collection_sql = '';
if ($collection_id) {
    $collection_sql = 'JOIN gallery_media_collections mc ON mc.media_id = m.id AND mc.collection_id = :collection_id ';
}
// Retrieve the total number of media files
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM gallery_media m ' . $collection_sql . $where);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($date_from) $stmt->bindParam('date_from', $date_from, PDO::PARAM_STR);
if ($date_to) $stmt->bindParam('date_to', $date_to, PDO::PARAM_STR);
if ($media_type) $stmt->bindParam('media_type', $media_type, PDO::PARAM_STR);
if ($is_public !== '') $stmt->bindParam('is_public', $is_public, PDO::PARAM_INT);
if ($acc_id) $stmt->bindParam('acc_id', $acc_id, PDO::PARAM_INT);
if ($collection_id) $stmt->bindParam('collection_id', $collection_id, PDO::PARAM_INT);
$stmt->execute();
$total_results = $stmt->fetchColumn();
// Prepare media query
$stmt = $pdo->prepare('SELECT m.*, a.email, a.username, (SELECT COUNT(*) FROM gallery_media_likes ml WHERE ml.media_id = m.id) AS likes FROM gallery_media m LEFT JOIN accounts a ON a.id = m.account_id ' . $collection_sql . $where . ' ORDER BY ' . $order_by . ' ' . $order . ' LIMIT :start_results,:num_results');
$stmt->bindParam('start_results', $param1, PDO::PARAM_INT);
$stmt->bindParam('num_results', $param2, PDO::PARAM_INT);
if ($search) $stmt->bindParam('search', $param3, PDO::PARAM_STR);
if ($date_from) $stmt->bindParam('date_from', $date_from, PDO::PARAM_STR);
if ($date_to) $stmt->bindParam('date_to', $date_to, PDO::PARAM_STR);
if ($media_type) $stmt->bindParam('media_type', $media_type, PDO::PARAM_STR);
if ($is_public !== '') $stmt->bindParam('is_public', $is_public, PDO::PARAM_INT);
if ($acc_id) $stmt->bindParam('acc_id', $acc_id, PDO::PARAM_INT);
if ($collection_id) $stmt->bindParam('collection_id', $collection_id, PDO::PARAM_INT);
$stmt->execute();
// Retrieve query results
$media = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Delete the media
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('SELECT * FROM gallery_media WHERE id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$media) {
        exit('No media found with the specified ID!');
    }
    if ($media['thumbnail'] && file_exists('../' . $media['thumbnail'])) {
        if (!unlink('../' . $media['thumbnail'])) {
            exit('Error deleting thumbnail file: ' . $media['thumbnail'] . '! Please check file permissions.');
        }
    }
    if (file_exists('../' . $media['filepath']) && !unlink('../' . $media['filepath'])) {
        exit('Error deleting media file: ' . $media['filepath'] . '! Please check file permissions.');
    }
    $stmt = $pdo->prepare('DELETE m, ml, mc FROM gallery_media m LEFT JOIN gallery_media_likes ml ON ml.media_id = m.id LEFT JOIN gallery_media_collections mc ON mc.media_id = m.id WHERE m.id = ?');
    $stmt->execute([ $_GET['delete'] ]);
    header('Location: allmedia.php?success_msg=3');
    exit;
}
// Approve the media
if (isset($_GET['approve'])) {
    $stmt = $pdo->prepare('UPDATE gallery_media SET is_approved = 1 WHERE id = ?');
    $stmt->execute([ $_GET['approve'] ]);
    header('Location: allmedia.php?success_msg=5');
    exit;
}
// Handle success messages
if (isset($_GET['success_msg'])) {
    if ($_GET['success_msg'] == 1) {
        $success_msg = 'Media file uploaded successfully!';
    }
    if ($_GET['success_msg'] == 2) {
        $success_msg = 'Media file updated successfully!';
    }
    if ($_GET['success_msg'] == 3) {
        $success_msg = 'Media file deleted successfully!';
    }
    if ($_GET['success_msg'] == 4) {
        $success_msg = 'Media imported successfully! ' . $_GET['imported'] . ' media files were imported.';
    }
    if ($_GET['success_msg'] == 5) {
        $success_msg = 'Media file approved successfully!';
    }
}
// Create URL
$url = 'allmedia.php?search_query=' . $search . '&status=' . $status . '&date_from=' . $date_from . '&date_to=' . $date_to . '&media_type=' . $media_type . '&is_public=' . $is_public . '&acc_id=' . $acc_id . '&collection_id=' . $collection_id;
?>
<?=template_admin_header('Media', 'gallery', 'media_view')?>

<div class="content-title mb-4" id="main-gallery-media" role="banner" aria-label="Gallery Media Management Header">
    <div class="title">
        <div class="icon">
            <i class="bi bi-images" aria-hidden="true"></i>
        </div>
        <div class="txt">
            <h2>Media</h2>
            <p>View, edit, and upload media files.</p>
        </div>
    </div>
</div>

<div class="mb-4">
</div>

<?php if (isset($success_msg)): ?>
<div class="mb-4" role="region" aria-label="Success Message">
    <div class="alert alert-success d-flex align-items-center" role="alert">
    <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>
        <div><?=$success_msg?></div>
    </div>
</div>
<?php endif; ?>

<div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
    
    <a href="media.php" class="btn btn-outline-secondary">
        <i class="bi bi-plus me-1" aria-hidden="true"></i>
        Upload Media
    </a>
</div>
<form method="get" class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Search & Filters</h6>
        <small class="text-muted">Advanced Filtering</small>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="search_query" class="form-label">Search</label>
                <input type="text" class="form-control" id="search_query" name="search_query" placeholder="Search media..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value=""<?=$status==''?' selected':''?>>All</option>
                    <option value="Approved"<?=$status=='Approved'?' selected':''?>>Approved</option>
                    <option value="Awaiting Approval"<?=$status=='Awaiting Approval'?' selected':''?>>Awaiting Approval</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="media_type" class="form-label">Media Type</label>
                <select name="media_type" id="media_type" class="form-control">
                    <option value=""<?=$media_type==''?' selected':''?>>All</option>
                    <option value="image"<?=$media_type=='image'?' selected':''?>>Image</option>
                    <option value="video"<?=$media_type=='video'?' selected':''?>>Video</option>
                    <option value="audio"<?=$media_type=='audio'?' selected':''?>>Audio</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="is_public" class="form-label">Is Public</label>
                <select name="is_public" id="is_public" class="form-control">
                    <option value=""<?=$is_public==''?' selected':''?>>All</option>
                    <option value="1"<?=$is_public==1?' selected':''?>>Yes</option>
                    <option value="0"<?=$is_public==0?' selected':''?>>No</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-funnel me-1" aria-hidden="true"></i>
                    Apply Filters
                </button>
                <?php if ($search || $status || $media_type || $is_public !== '' || $date_from || $date_to): ?>
                <a href="allmedia.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1" aria-hidden="true"></i>
                    Clear
                </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-md-3">
                <label for="date_from" class="form-label">Date From</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="<?=$date_from?>">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">Date To</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="<?=$date_to?>">
            </div>
        </div>
    </div>
</form>
</div>



<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Gallery Media Files</h6>
        <span class="badge bg-secondary"><?= number_format($total_results) ?> Total</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="text-align:left;"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=id'?>" class="sort-header">#<?=$order_by=='id' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th style="text-align:left;" colspan="2"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=title'?>" class="sort-header">Media<?=$order_by=='title' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th class="responsive-hidden" style="text-align:left;"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=username'?>" class="sort-header">Account<?=$order_by=='username' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=likes'?>" class="sort-header">Likes<?=$order_by=='likes' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=media_type'?>" class="sort-header">Type<?=$order_by=='media_type' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=is_approved'?>" class="sort-header">Status<?=$order_by=='is_approved' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th class="responsive-hidden"><a href="<?=$url . '&order=' . ($order=='ASC'?'DESC':'ASC') . '&order_by=uploaded_date'?>" class="sort-header">Date<?=$order_by=='uploaded_date' ? $table_icons[strtolower($order)] : ''?></a></th>
                        <th style="text-align: center;" role="columnheader" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($media)): ?>
                <tr>
                    <td colspan="9" class="text-center py-4 text-muted">
                        <i class="bi bi-images mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                        <br>There are no media files.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($media as $m): ?>
                <tr>
                    <td><span class="badge bg-light text-dark"><?=$m['id']?></span></td>
                    <td style="width: 60px;">
                        <a href="#" class="media-img open-media-modal" data-type="<?=$m['media_type']?>" data-filepath="<?=htmlspecialchars('../' . $m['filepath'], ENT_QUOTES)?>" title="View Media">
                            <?php if ($m['media_type'] == 'image' && file_exists('../' . $m['filepath'])): ?>
                            <img src="../<?=$m['filepath']?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" width="50" height="50" class="rounded">
                            <?php elseif (!empty($m['thumbnail']) && file_exists('../' . $m['thumbnail'])): ?>
                            <img src="../<?=$m['thumbnail']?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" width="50" height="50" class="rounded">
                            <?php elseif ($m['media_type'] == 'video'): ?>
                            <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width: 50px; height: 50px;">
                                <i class="bi bi-play-btn text-primary"></i>
                            </div>
                            <?php elseif ($m['media_type'] == 'audio'): ?>
                            <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width: 50px; height: 50px;">
                                <i class="bi bi-volume-up text-primary"></i>
                            </div>
                            <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-danger rounded" style="width: 50px; height: 50px;" title="File not found">
                                <i class="bi bi-x-lg text-white"></i>
                            </div>
                            <?php endif; ?>
                        </a>
                    </td>
                    <td class="title-caption">
                        <a href="#" class="open-media-modal<?=!file_exists('../' . $m['filepath']) ? ' file-not-exist' : ''?>" data-type="<?=$m['media_type']?>" data-filepath="<?=htmlspecialchars('../' . $m['filepath'], ENT_QUOTES)?>" title="View Media">
                            <?=htmlspecialchars($m['title'], ENT_QUOTES)?>
                            <?php if (!$m['is_public']): ?>
                            <i class="bi bi-lock-fill small text-muted" title="Private Media" aria-hidden="true"></i>
                            <?php endif; ?>
                        </a>
                        <?php if (file_exists('../' . $m['filepath'])): ?>
                        <span><?=mime_content_type('../' . $m['filepath'])?>, <?=convert_filesize(filesize('../' . $m['filepath']))?></span>
                        <?php else: ?>
                        <span>(File not found)</span>
                        <?php endif; ?>
                    </td>
                    <td class="responsive-hidden alt">
                        <?php if ($m['acc_id']): ?>
                        <?=htmlspecialchars($m['display_name'], ENT_QUOTES)?><br>
                        <a class="link1" href="account.php?id=<?=$m['acc_id']?>"><?=htmlspecialchars($m['email'], ENT_QUOTES)?> [<?=htmlspecialchars($m['acc_id'], ENT_QUOTES)?>]</a></td>
                        <?php else: ?>
                        --
                        <?php endif; ?>
                    </td>
                    <td class="responsive-hidden"><a href="likes.php?media_id=<?=$m['id']?>" class="link1"><?=$m['likes'] ? number_format($m['likes']) : 0?></a></td>
                    <td class="responsive-hidden"><span class="grey small"><?=ucfirst($m['media_type'])?></span></td>
                    <td><?=$m['is_approved']?'<span class="green small">Approved</span>':'<span class="orange small">Awaiting Approval</span>'?></td>
                    <td class="responsive-hidden alt"><?=date('F j, Y H:ia', strtotime($m['uploaded_date']))?></td>
                    <td class="actions">
                        <div class="table-dropdown">
                            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                            <div class="table-dropdown-items">
                                <a href="media.php?id=<?=$m['id']?>">
                                    <span class="icon">
                                        <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                    </span>
                                    Edit
                                </a>
                                <?php if (!$m['is_approved']): ?>
                                <a class="green" href="allmedia.php?approve=<?=$m['id']?>" onclick="return confirm('Are you sure you want to approve this media?')">
                                    <span class="icon">
                                        <svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>
                                    </span>    
                                    Approve
                                </a>
                                <?php endif; ?>
                                <?php if (file_exists('../' . $m['filepath'])): ?>
                                <a href="../<?=$m['filepath']?>" target="_blank" download>
                                    <span class="icon">
                                        <i class="bi bi-download" aria-hidden="true"></i>
                                    </span>    
                                    Download
                                </a>
                                <?php endif; ?>
                                <a class="red" href="allmedia.php?delete=<?=$m['id']?>" onclick="return confirm('Are you sure you want to delete this media?')">
                                    <span class="icon">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                    </span>    
                                    Delete
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

    <div class="card-footer bg-light">
        <!-- Pagination: in card footer -->
        <div class="small">
            <span>Page <?= $page ?> of <?= ceil($total_results / $results_per_page) == 0 ? 1 : ceil($total_results / $results_per_page) ?></span>
            <?php if ($page > 1): ?>
                | <a href="<?= $url ?>&page=<?= $page - 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>">Previous</a>
            <?php endif; ?>
            <?php if ($page * $results_per_page < $total_results): ?>
                | <a href="<?= $url ?>&page=<?= $page + 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>

<?=template_admin_footer()?>