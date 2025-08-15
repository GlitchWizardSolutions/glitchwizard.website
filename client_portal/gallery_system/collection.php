<?php
include 'functions.php';
// Get account ID and role from session (if logged in)
$is_logged_in = isset($_SESSION['account_loggedin']) && $_SESSION['account_loggedin'];
$account_id = $is_logged_in ? $_SESSION['account_id'] : -1;
$account_role = $is_logged_in && isset($_SESSION['account_role']) ? $_SESSION['account_role'] : null;
// Collection ID from GET request (if provided)
$collection_param = isset($_GET['collection']) && is_numeric($_GET['collection']) ? (int)$_GET['collection'] : 'all';
$collection_data = null;
// Determine if the user is viewing a specific collection or all collections
$is_specific_collection_view = false;
// Check if the user is viewing their own collection
$is_owner_viewing_specific = false;
// Handle the case where the user is viewing a specific collection
if ($collection_param !== 'all') {
    $stmt_coll = $pdo->prepare('SELECT c.* FROM collections c WHERE c.id = ?');
    $stmt_coll->execute([ $collection_param ]);
    $collection_data = $stmt_coll->fetch(PDO::FETCH_ASSOC);
    if ($collection_data) {
        $is_specific_collection_view = true;
        if (!$collection_data['is_public']) {
            if (!$is_logged_in || ($collection_data['acc_id'] != $account_id && $account_role != 'Admin')) {
                exit('Private collection! Access Denied.');
            }
        }
        if ($is_logged_in && $collection_data['acc_id'] == $account_id) {
            $is_owner_viewing_specific = true;
        }
    } else {
        $collection_param = 'all';
    }
}
// Check if the user is logged in and retrieve their collections
if ($is_logged_in) {
	$stmt = $pdo->prepare('SELECT title FROM collections WHERE acc_id = ? ORDER BY title');
	$stmt->execute([ $account_id ]);
	$user_collections = implode(',,', $stmt->fetchAll(PDO::FETCH_COLUMN));
} else {
	$user_collections = '';
}
// Set collection variable based on the collection parameter
$collection = $collection_param;
// Set SQL join and where clauses based on the collection parameter
$collection_sql_join = '';
$collection_sql_where = '';
if ($is_specific_collection_view && $collection_data) {
    $collection_sql_join = ' JOIN media_collections mc ON mc.media_id = m.id ';
    $collection_sql_where = ' AND mc.collection_id = :collection ';
} else {
    $collection_sql_where = '';
    $collection = 'all';
}
// Variables to determine if the user is viewing their own likes
$likes = isset($_GET['view']) && $_GET['view'] == 'likes' ? true : false;
$likes_sql = $likes ? ' AND m.id IN (SELECT ml.media_id FROM media_likes ml WHERE ml.acc_id = :acc_id) ' : '';
// Set the sort order based on user selection or default to 'newest'
$sort_by_options = ['newest', 'oldest', 'a_to_z', 'z_to_a'];
$sort_by = isset($_GET['sort_by']) && in_array($_GET['sort_by'], $sort_by_options) ? $_GET['sort_by'] : 'newest';
$sort_by_sql = '';
if ($sort_by == 'oldest') {
    $sort_by_sql = 'm.uploaded_date ASC';
} elseif ($sort_by == 'a_to_z') {
    $sort_by_sql = 'm.title ASC';
} elseif ($sort_by == 'z_to_a') {
    $sort_by_sql = 'm.title DESC';
} else {
    $sort_by_sql = 'm.uploaded_date DESC';
}
// Set the media type filter based on user selection or default to 'all'
$type_options = ['image', 'video', 'audio'];
$type = isset($_GET['type']) && in_array($_GET['type'], $type_options) ? $_GET['type'] : 'all';
$type_sql = '';
if ($type != 'all') {
    $type_sql = ' AND m.media_type = :type ';
}
// Set the account ID for filtering based on likes or collections
$acc_sql = '';
if (!$is_specific_collection_view && !$likes) {
    $acc_sql = ' AND m.acc_id = :acc_id ';
}
// Set the search parameter based on user input
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_sql_param = $search ? '%' . $search . '%' : '';
$search_sql = '';
if ($search_sql_param) {
    $search_sql = ' AND m.title LIKE :search ';
}
// Set the number of media items per page
$media_per_page = media_per_page;
// Get the current page from the GET request or default to 1
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
// Prepare the SQL statement to fetch media items based on the filters and pagination
$stmt = $pdo->prepare('SELECT m.*, a.display_name, (SELECT COUNT(*) FROM media_likes ml WHERE ml.media_id = m.id) AS likes, (SELECT COUNT(*) FROM media_likes ml WHERE ml.media_id = m.id AND ml.acc_id = :acc_id) AS liked FROM media m ' . $collection_sql_join . ' LEFT JOIN accounts a ON a.id = m.acc_id WHERE m.is_approved = 1 ' . $collection_sql_where . $type_sql . $acc_sql . $search_sql . $likes_sql . ' ORDER BY ' . $sort_by_sql . ' LIMIT :page,:media_per_page');
$stmt->bindValue(':acc_id', $account_id, PDO::PARAM_INT);
$stmt->bindValue(':page', (($current_page - 1) * $media_per_page), PDO::PARAM_INT);
$stmt->bindValue(':media_per_page', $media_per_page, PDO::PARAM_INT);
if ($type != 'all') {
    $stmt->bindValue(':type', $type);
}
if ($search_sql_param) {
    $stmt->bindValue(':search', $search_sql_param);
}
if ($collection != 'all') {
    $stmt->bindValue(':collection', $collection, PDO::PARAM_INT);
}
$stmt->execute();
$media = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Prepare the SQL statement to count the total number of media items based on the filters
$stmt = $pdo->prepare('SELECT COUNT(CASE WHEN m.media_type = "image" THEN 1 END) AS total_images, COUNT(CASE WHEN m.media_type = "video" THEN 1 END) AS total_videos, COUNT(CASE WHEN m.media_type = "audio" THEN 1 END) AS total_audios, COUNT(*) AS total_all FROM media m ' . $collection_sql_join . ' WHERE m.is_approved = 1 ' . $acc_sql . $collection_sql_where . $search_sql . $likes_sql);
if ($search_sql_param) {
    $stmt->bindValue(':search', $search_sql_param);
}
if ($collection != 'all') {
    $stmt->bindValue(':collection', $collection, PDO::PARAM_INT);
}
if ($acc_sql || $likes) {
    $stmt->bindValue(':acc_id', $account_id, PDO::PARAM_INT);
}
$stmt->execute();
$total_counts = $stmt->fetch(PDO::FETCH_ASSOC);
// Determine the total number of media items based on the type filter
if ($type == 'image') {
	$total_media = isset($total_counts['total_images']) ? $total_counts['total_images'] : 0;
} elseif ($type == 'video') {
	$total_media = isset($total_counts['total_videos']) ? $total_counts['total_videos'] : 0;
} elseif ($type == 'audio') {
	$total_media = isset($total_counts['total_audios']) ? $total_counts['total_audios'] : 0;
} else {
	$total_media = isset($total_counts['total_all']) ? $total_counts['total_all'] : 0;
}
// Check if the media layout is set to masonry
if (media_layout == 'masonry') {
	// Initialize three empty arrays
	$columns = [[], [], []];
	$sorted_media = [];
	foreach ($media as $index => $item) {
		$sorted_media[] = $item;
	}
	// Distribute media items into the three columns in a round-robin fashion
	foreach ($sorted_media as $index => &$item) {
		$item['index'] = $index;
		$columns[$index % 3][] = $item;
	}
}
?>
<?=template_header($search ? 'Search Results for "' . htmlspecialchars($search, ENT_QUOTES) . '"' : ($is_specific_collection_view && $collection_data ? htmlspecialchars($collection_data['title'], ENT_QUOTES) : 'Your Gallery'))?>

<div class="page-top">
	<div class="container">
		<?php if ($search): ?>
		<h2 class="title">Search Results for "<?=htmlspecialchars($search, ENT_QUOTES)?>"</h2>
        <?php elseif ($is_specific_collection_view && $collection_data): ?>
        <h2 class="title">
			<?=htmlspecialchars($collection_data['title'], ENT_QUOTES)?>
			<?php if (!$collection_data['is_public']): ?>
			<svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><title>Private Collection</title><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M144 144l0 48 160 0 0-48c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192l0-48C80 64.5 144.5 0 224 0s144 64.5 144 144l0 48 16 0c35.3 0 64 28.7 64 64l0 192c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 256c0-35.3 28.7-64 64-64l16 0z"/></svg>
			<?php endif; ?>
		</h2>
        <?php if (!empty($collection_data['description_text'])): ?>
        <p class="description"><?=htmlspecialchars($collection_data['description_text'], ENT_QUOTES)?></p>
        <?php endif; ?>
		<?php elseif ($likes): ?>
		<h2 class="title">Your Liked Media</h2>
		<?php else: ?>
		<h2 class="title">Your Gallery</h2>
		<?php endif; ?>
		<div class="navigation-links">
            <?php $nav_base_params = ['sort_by' => $sort_by, 'collection' => $collection_param, 'search' => $search, 'view' => $likes ? 'likes' : '']; ?>
			<a href="?<?=http_build_query(array_filter($nav_base_params))?>&type=all"<?=$type=='all'?' class="active"':''?>><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M256 0L576 0c35.3 0 64 28.7 64 64l0 224c0 35.3-28.7 64-64 64l-320 0c-35.3 0-64-28.7-64-64l0-224c0-35.3 28.7-64 64-64zM476 106.7C471.5 100 464 96 456 96s-15.5 4-20 10.7l-56 84L362.7 169c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l80 0 48 0 144 0c8.9 0 17-4.9 21.2-12.7s3.7-17.3-1.2-24.6l-96-144zM336 96a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM64 128l96 0 0 256 0 32c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-32 160 0 0 64c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 192c0-35.3 28.7-64 64-64zm8 64c-8.8 0-16 7.2-16 16l0 16c0 8.8 7.2 16 16 16l16 0c8.8 0 16-7.2 16-16l0-16c0-8.8-7.2-16-16-16l-16 0zm0 104c-8.8 0-16 7.2-16 16l0 16c0 8.8 7.2 16 16 16l16 0c8.8 0 16-7.2 16-16l0-16c0-8.8-7.2-16-16-16l-16 0zm0 104c-8.8 0-16 7.2-16 16l0 16c0 8.8 7.2 16 16 16l16 0c8.8 0 16-7.2 16-16l0-16c0-8.8-7.2-16-16-16l-16 0zm336 16l0 16c0 8.8 7.2 16 16 16l16 0c8.8 0 16-7.2 16-16l0-16c0-8.8-7.2-16-16-16l-16 0c-8.8 0-16 7.2-16 16z"/></svg>All Media <?=$total_counts['total_all'] ? number_format($total_counts['total_all']) : 0?></a>
			<a href="?<?=http_build_query(array_filter($nav_base_params))?>&type=image"<?=$type=='image'?' class="active"':''?>><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M448 80c8.8 0 16 7.2 16 16l0 319.8-5-6.5-136-176c-4.5-5.9-11.6-9.3-19-9.3s-14.4 3.4-19 9.3L202 340.7l-30.5-42.7C167 291.7 159.8 288 152 288s-15 3.7-19.5 10.1l-80 112L48 416.3l0-.3L48 96c0-8.8 7.2-16 16-16l384 0zM64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zm80 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/></svg>Images <?=$total_counts['total_images'] ? number_format($total_counts['total_images']) : 0?></a>
			<a href="?<?=http_build_query(array_filter($nav_base_params))?>&type=video"<?=$type=='video'?' class="active"':''?>><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM48 368l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 240l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 112l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16L64 96c-8.8 0-16 7.2-16 16zM416 96c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM160 128l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32L192 96c-17.7 0-32 14.3-32 32zm32 160c-17.7 0-32 14.3-32 32l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32l-128 0z"/></svg>Videos <?=$total_counts['total_videos'] ? number_format($total_counts['total_videos']) : 0?></a>
			<a href="?<?=http_build_query(array_filter($nav_base_params))?>&type=audio"<?=$type=='audio'?' class="active"':''?>><svg width="16" height="16" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M499.1 6.3c8.1 6 12.9 15.6 12.9 25.7l0 72 0 264c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L448 147 192 223.8 192 432c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L128 200l0-72c0-14.1 9.3-26.6 22.8-30.7l320-96c9.7-2.9 20.2-1.1 28.3 5z"/></svg>Audio <?=$total_counts['total_audios'] ? number_format($total_counts['total_audios']) : 0?></a>
		</div>
	</div>
</div>

<div class="page-content">

	<div class="media-list-filters">
        <div class="btns">
            <a href="upload.php?collection=<?=$is_owner_viewing_specific && $collection_data ? htmlspecialchars($collection_data['title'], ENT_QUOTES) : ''?>" class="btn">
			    <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z"/></svg>	
                Upload Media
            </a>
            <?php if ($is_owner_viewing_specific): ?>
            <a href="manage-collection.php?id=<?=$collection_data['id']?>" class="btn">
                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" /></svg>
                Edit Collection
            </a>
            <?php endif; ?>
        </div>
		<form class="media-list-filters-form" action="collection.php" method="get">
			<input type="hidden" name="type" value="<?=htmlspecialchars($type, ENT_QUOTES)?>">
            <input type="hidden" name="collection" value="<?=htmlspecialchars($collection_param, ENT_QUOTES)?>">
            <input type="hidden" name="page" value="1">
			<label for="sort_by">
				<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M151.6 42.4C145.5 35.8 137 32 128 32s-17.5 3.8-23.6 10.4l-88 96c-11.9 13-11.1 33.3 2 45.2s33.3 11.1 45.2-2L96 146.3 96 448c0 17.7 14.3 32 32 32s32-14.3 32-32l0-301.7 32.4 35.4c11.9 13 32.2 13.9 45.2 2s13.9-32.2 2-45.2l-88-96zM320 480l32 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-32 0c-17.7 0-32 14.3-32 32s14.3 32 32 32zm0-128l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0c-17.7 0-32 14.3-32 32s14.3 32 32 32zm0-128l160 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-160 0c-17.7 0-32 14.3-32 32s14.3 32 32 32zm0-128l224 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L320 32c-17.7 0-32 14.3-32 32s14.3 32 32 32z"/></svg>
				Sort By
				<select id="sort_by" name="sort_by" onchange="this.form.submit()">
					<option value="newest"<?=$sort_by=='newest'?' selected':''?>>Newest</option>
					<option value="oldest"<?=$sort_by=='oldest'?' selected':''?>>Oldest</option>
					<option value="a_to_z"<?=$sort_by=='a_to_z'?' selected':''?>>A-Z</option>
					<option value="z_to_a"<?=$sort_by=='z_to_a'?' selected':''?>>Z-A</option>
				</select>
			</label>
			<label for="search">
				<svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
				<input id="search" type="text" name="search" placeholder="Search..." value="<?=htmlspecialchars($search, ENT_QUOTES)?>">
			</label>
		</form>
	</div>

	<div class="media-list layout-<?=media_layout?>">
		<?php if (media_layout == 'masonry'): ?>
		<?php foreach ($columns as $col_index => $column): ?>
		<div class="masonry-column" data-column-index="<?=$col_index?>">
			<?php foreach ($column as $m): ?>
			<a href="<?=media_popup ? '#' : 'view.php?id=' . $m['id']?>" data-src="<?=htmlspecialchars($m['filepath'], ENT_QUOTES)?>" data-id="<?=$m['id']?>" data-index="<?=$m['index']?>" data-autoplay="<?=media_autoplay?>" data-user-collections="<?=htmlspecialchars($user_collections, ENT_QUOTES)?>" data-title="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" data-description="<?=htmlspecialchars($m['description_text'], ENT_QUOTES)?>" data-type="<?=$m['media_type']?>" data-likes="<?=$m['likes']?>" data-liked="<?=$m['liked']?1:0?>" data-uploaded-date="<?=date('F j, Y', strtotime($m['uploaded_date']))?>" data-user="<?=$m['display_name'] ? htmlspecialchars($m['display_name'], ENT_QUOTES) : 'Anonymous'?>" data-original-column="<?=$col_index?>"<?=($is_logged_in && $account_id == $m['acc_id'])?' data-own-media="true"':''?><?=($is_owner_viewing_specific && $collection_data)?' data-collection="' . $collection_data['id'] . '"':''?>>
				<?php if (file_exists($m['filepath']) && $m['media_type'] == 'image' && !file_exists($m['thumbnail'])): ?>
				<img src="<?=htmlspecialchars($m['filepath'], ENT_QUOTES)?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" width="<?=getimagesize($m['filepath'])[0]?>" height="<?=getimagesize($m['filepath'])[1]?>">
				<?php elseif ($m['media_type'] == 'video' && (empty($m['thumbnail']) || !file_exists($m['thumbnail']))): ?>
				<span class="placeholder">
                    <svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM48 368l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 240l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 112l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16L64 96c-8.8 0-16 7.2-16 16zM416 96c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM160 128l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32L192 96c-17.7 0-32 14.3-32 32zm32 160c-17.7 0-32 14.3-32 32l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32l-128 0z"/></svg>
                    <?=htmlspecialchars($m['title'], ENT_QUOTES)?>
				</span>
				<?php elseif ($m['media_type'] == 'audio' && (empty($m['thumbnail']) || !file_exists($m['thumbnail']))): ?>
				<span class="placeholder">
                    <svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M499.1 6.3c8.1 6 12.9 15.6 12.9 25.7l0 72 0 264c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L448 147 192 223.8 192 432c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L128 200l0-72c0-14.1 9.3-26.6 22.8-30.7l320-96c9.7-2.9 20.2-1.1 28.3 5z"/></svg>
                    <?=htmlspecialchars($m['title'], ENT_QUOTES)?>
				</span>
				<?php elseif (!empty($m['thumbnail']) && file_exists($m['thumbnail'])): ?>
				<img src="<?=htmlspecialchars($m['thumbnail'], ENT_QUOTES)?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" width="<?=getimagesize($m['thumbnail'])[0]?>" height="<?=getimagesize($m['thumbnail'])[1]?>">
				<?php else: ?>
				<span class="placeholder">
                    <svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M448 80c8.8 0 16 7.2 16 16l0 319.8-5-6.5-136-176c-4.5-5.9-11.6-9.3-19-9.3s-14.4 3.4-19 9.3L202 340.7l-30.5-42.7C167 291.7 159.8 288 152 288s-15 3.7-19.5 10.1l-80 112L48 416.3l0-.3L48 96c0-8.8 7.2-16 16-16l384 0zM64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zm80 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/></svg>
                    <?=htmlspecialchars($m['title'], ENT_QUOTES)?><?=!file_exists($m['filepath']) ? ' (File Missing)' : '';?>
				</span>
				<?php endif; ?>
				<span class="description">
                    <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l82.7 0L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3l0 82.7c0 17.7 14.3 32 32 32s32-14.3 32-32l0-160c0-17.7-14.3-32-32-32L320 0zM80 32C35.8 32 0 67.8 0 112L0 432c0 44.2 35.8 80 80 80l320 0c44.2 0 80-35.8 80-80l0-112c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 112c0 8.8-7.2 16-16 16L80 448c-8.8 0-16-7.2-16-16l0-320c0-8.8 7.2-16 16-16l112 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L80 32z"/></svg>
                    <?=htmlspecialchars($m['title'], ENT_QUOTES)?>
				</span>
			</a>
			<?php endforeach; ?>
		</div>
		<?php endforeach; ?>
		<?php else: ?>
		<?php foreach ($media as $i => $m): ?>
        <a href="<?=media_popup ? '#' : 'view.php?id=' . $m['id']?>" data-src="<?=htmlspecialchars($m['filepath'], ENT_QUOTES)?>" data-id="<?=$m['id']?>" data-index="<?=$i?>" data-autoplay="<?=media_autoplay?>" data-user-collections="<?=htmlspecialchars($user_collections, ENT_QUOTES)?>" data-title="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" data-description="<?=htmlspecialchars($m['description_text'], ENT_QUOTES)?>" data-type="<?=$m['media_type']?>" data-likes="<?=$m['likes']?>" data-liked="<?=$m['liked']?1:0?>" data-uploaded-date="<?=date('F j, Y', strtotime($m['uploaded_date']))?>" data-user="<?=$m['display_name'] ? htmlspecialchars($m['display_name'], ENT_QUOTES) : 'Anonymous'?>"<?=($is_logged_in && $account_id == $m['acc_id'])?' data-own-media="true"':''?><?=($is_owner_viewing_specific && $collection_data)?' data-collection="' . $collection_data['id'] . '"':''?>>
            <?php if (file_exists($m['filepath']) && $m['media_type'] == 'image' && !file_exists($m['thumbnail'])): ?>
            <img src="<?=htmlspecialchars($m['filepath'], ENT_QUOTES)?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" width="<?=getimagesize($m['filepath'])[0]?>" height="<?=getimagesize($m['filepath'])[1]?>">
            <?php elseif ($m['media_type'] == 'video' && (empty($m['thumbnail']) || !file_exists($m['thumbnail']))): ?>
            <span class="placeholder">
                <svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM48 368l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 240l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0c-8.8 0-16 7.2-16 16zm368-16c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM48 112l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16L64 96c-8.8 0-16 7.2-16 16zM416 96c-8.8 0-16 7.2-16 16l0 32c0 8.8 7.2 16 16 16l32 0c8.8 0 16-7.2 16-16l0-32c0-8.8-7.2-16-16-16l-32 0zM160 128l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32L192 96c-17.7 0-32 14.3-32 32zm32 160c-17.7 0-32 14.3-32 32l0 64c0 17.7 14.3 32 32 32l128 0c17.7 0 32-14.3 32-32l0-64c0-17.7-14.3-32-32-32l-128 0z"/></svg>
                <?=htmlspecialchars($m['title'], ENT_QUOTES)?>
            </span>
            <?php elseif ($m['media_type'] == 'audio' && (empty($m['thumbnail']) || !file_exists($m['thumbnail']))): ?>
            <span class="placeholder">
                <svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M499.1 6.3c8.1 6 12.9 15.6 12.9 25.7l0 72 0 264c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L448 147 192 223.8 192 432c0 44.2-43 80-96 80s-96-35.8-96-80s43-80 96-80c11.2 0 22 1.6 32 4.6L128 200l0-72c0-14.1 9.3-26.6 22.8-30.7l320-96c9.7-2.9 20.2-1.1 28.3 5z"/></svg>
                <?=htmlspecialchars($m['title'], ENT_QUOTES)?>
            </span>
            <?php elseif (!empty($m['thumbnail']) && file_exists($m['thumbnail'])): ?>
            <img src="<?=htmlspecialchars($m['thumbnail'], ENT_QUOTES)?>" alt="<?=htmlspecialchars($m['title'], ENT_QUOTES)?>" width="<?=getimagesize($m['thumbnail'])[0]?>" height="<?=getimagesize($m['thumbnail'])[1]?>">
            <?php else: ?>
            <span class="placeholder">
                <svg width="32" height="32" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M448 80c8.8 0 16 7.2 16 16l0 319.8-5-6.5-136-176c-4.5-5.9-11.6-9.3-19-9.3s-14.4 3.4-19 9.3L202 340.7l-30.5-42.7C167 291.7 159.8 288 152 288s-15 3.7-19.5 10.1l-80 112L48 416.3l0-.3L48 96c0-8.8 7.2-16 16-16l384 0zM64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zm80 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"/></svg>
                <?=htmlspecialchars($m['title'], ENT_QUOTES)?><?=!file_exists($m['filepath']) ? ' (File Missing)' : '';?>
            </span>
            <?php endif; ?>
            <span class="description">
                <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l82.7 0L201.4 265.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3l0 82.7c0 17.7 14.3 32 32 32s32-14.3 32-32l0-160c0-17.7-14.3-32-32-32L320 0zM80 32C35.8 32 0 67.8 0 112L0 432c0 44.2 35.8 80 80 80l320 0c44.2 0 80-35.8 80-80l0-112c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 112c0 8.8-7.2 16-16 16L80 448c-8.8 0-16-7.2-16-16l0-320c0-8.8 7.2-16 16-16l112 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L80 32z"/></svg>
                <?=htmlspecialchars($m['title'], ENT_QUOTES)?>
            </span>
        </a>
		<?php endforeach; ?>
		<?php endif; ?>
		<?php if (empty($media)): ?>
		<p class="no-media">No media found.</p>
		<?php endif; ?>
	</div>

    <div class="pagination">
        <?php
        $query_params = ['sort_by' => $sort_by, 'collection' => $collection_param, 'type' => $type, 'search' => $search, 'view' => $likes ? 'likes' : ''];
        $total_pages = ceil($total_media / $media_per_page);
        ?>
	    <?php if ($current_page > 1): ?>
	    <a href="?<?=http_build_query(array_filter($query_params))?>&page=<?=$current_page-1?>">Prev</a>
	    <?php endif; ?>
	    <div>Page <?=$current_page?> of <?= $total_pages > 0 ? $total_pages : 1 ?></div>
	    <?php if ($current_page < $total_pages): ?>
	    <a href="?<?=http_build_query(array_filter($query_params))?>&page=<?=$current_page+1?>">Next</a>
	    <?php endif; ?>
	</div>

</div>

<?php if (media_popup): ?>
<div class="media-popup"></div>
<?php endif; ?>

<?=template_footer()?>