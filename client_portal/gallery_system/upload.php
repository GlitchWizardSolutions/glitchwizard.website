<?php
include 'functions.php';
// Remove the time limit for file uploads
set_time_limit(0);
// Errors array
$errors = [];
// Check if authentication required
if (authentication_required && !isset($_SESSION['account_loggedin'])) {
	header('Location: collections.php');
	exit;
}
// Check if user has uploaded new media file
if (isset($_POST['total_files']) && is_numeric($_POST['total_files']) && (int)$_POST['total_files'] > 0) {
	// Iterate all uploaded files
	for ($i = 0; $i < (int)$_POST['total_files']; $i++) {
		// Make sure the file exists
		if (!isset($_FILES['file_' . $i]) || empty($_FILES['file_' . $i]['tmp_name'])) continue;
		// Assign captured form data
		$title = isset($_POST['title_' . $i]) && !empty($_POST['title_' . $i]) ? $_POST['title_' . $i] : $_FILES['file_' . $i]['name'];
		$description = isset($_POST['description_' . $i]) ? $_POST['description_' . $i] : '';
		$public = isset($_POST['public_' . $i]) ? $_POST['public_' . $i] : '';
		// Title validation
		if (strlen($title) < 3 || strlen($title) > 100) {
			$errors[] = 'Title must be between 3 and 100 characters!';
			continue;
		}
		// Description validation
		if (strlen($description) > 300) {
			$errors[] = 'Description must be less than 300 characters!';
			continue;
		}
		// Get mime type of the uploaded file
		$mime_type = mime_content_type($_FILES['file_' . $i]['tmp_name']);
		// Media file type (image/audio/video)
		$type = '';
		$type = strpos($mime_type, 'image/') === 0 ? 'image' : $type;
		$type = strpos($mime_type, 'audio/') === 0 ? 'audio' : $type;
		$type = strpos($mime_type, 'video/') === 0 ? 'video' : $type;
		// The directory where media files will be stored
		$target_dir = 'media/' . $type . 's/';
		// Unique media ID
		$media_id = md5(uniqid() . $i);
		// Media parts (name, extension)
		$media_parts = explode('.', $_FILES['file_' . $i]['name']);
		// The path of the new uploaded media file
		$media_path = $target_dir . $media_id . '.' . end($media_parts);
		// Set the max upload file size for each media type (measured in bytes):
		$image_max_size = image_max_size;
		$audio_max_size = audio_max_size;
		$video_max_size = video_max_size;
		// Check to make sure the media file is valid
		if (empty($type)) {
			$errors[] = 'Unsupported media format for file: ' . htmlspecialchars($_FILES['file_' . $i]['name'], ENT_QUOTES) . '! Please upload a valid image, audio, or video file.';
			continue;
		}
		// Validate media file size
		if ($_FILES['file_' . $i]['size'] > ${$type . '_max_size'}) {
			$errors[] = htmlspecialchars($_FILES['file_' . $i]['name'], ENT_QUOTES) . ' file size too large! Please choose a file with a size less than ' . convert_filesize(${$type . '_max_size'}) . '.';
			continue;
		}
		// Check thumbnail input
		$thumbnail_path = '';
		if (isset($_FILES['thumbnail_' . $i]) && preg_match('/image\/*/',$_FILES['thumbnail_' . $i]['type']) && !empty($_FILES['thumbnail_' . $i]['tmp_name']) && getimagesize($_FILES['thumbnail_' . $i]['tmp_name'])) {
			if ($_FILES['thumbnail_' . $i]['size'] > $image_max_size) {
				$errors[] = htmlspecialchars($title, ENT_QUOTES) . ' thumbnail size too large! Please choose a file with a size less than ' . convert_filesize($image_max_size) . '.';
				continue;
			} else {
				$thumbnail_parts = explode('.', $_FILES['thumbnail_' . $i]['name']);
				$thumbnail_path = 'media/thumbnails/' . $media_id . '.' . end($thumbnail_parts);
				if (!move_uploaded_file($_FILES['thumbnail_' . $i]['tmp_name'], $thumbnail_path)) {
					$errors[] = 'Error moving thumbnail: ' . htmlspecialchars($_FILES['thumbnail_' . $i]['name'], ENT_QUOTES) . '! Please try again or check permissions.';
					continue;
				} else {
					if (image_quality < 100) {
						compress_image($thumbnail_path, image_quality);
					}
					if (correct_image_orientation) {
						correct_image_orientation($thumbnail_path);
					}
					if (image_max_width != -1 || image_max_height != -1) {
						resize_image($thumbnail_path, image_max_width, image_max_height);
					}
					if (strip_exif_data) {
						strip_exif($thumbnail_path);
					}
				}
			}
		}
		// Everything checks out, so now we can proceed to move the uploaded media file
		if (!move_uploaded_file($_FILES['file_' . $i]['tmp_name'], $media_path)) {
			$errors[] = 'Error moving file: ' . htmlspecialchars($_FILES['file_' . $i]['name'], ENT_QUOTES) . '! Please try again or check file permissions.';
			continue;
		}
		// Check if the media is an image and auto-generate thumbnail	
		if (auto_generate_image_thumbnail && $type == 'image') {
			$thumbnail_path = create_image_thumbnail($media_path, $media_id) ?? '';
		}
		// convert svg to png
		if (convert_svg_to_png && strtolower(end($media_parts)) == 'svg') {
			$media_path = convert_svg_to_png($media_path);
		}
		// Compress image
		if (image_quality < 100) {
			compress_image($media_path, image_quality);
		}
		// Fix image orientation
		if (correct_image_orientation) {
			correct_image_orientation($media_path);
		}
		// Resize image
		if (image_max_width != -1 || image_max_height != -1) {
			resize_image($media_path, image_max_width, image_max_height);
		}
		// Strip EXIF data
		if (strip_exif_data) {
			strip_exif($media_path);
		}
		// Check if approval is required
		$approved = approval_required ? 0 : 1;
		// Approved if user is admin
		$approved = isset($_SESSION['account_loggedin']) && $_SESSION['account_role'] == 'admin' ? 1 : $approved;
		$acc_id = isset($_SESSION['account_id']) ? $_SESSION['account_id'] : NULL;
		$uploaded_date = date('Y-m-d H:i:s');
		// Insert media details into the database
		$stmt = $pdo->prepare('INSERT INTO media (title, description_text, filepath, uploaded_date, media_type, thumbnail, is_approved, is_public, acc_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$stmt->execute([ $title, $description, $media_path, $uploaded_date, $type, $thumbnail_path, $approved, $public, $acc_id ]);
		// If user selected collection, add to collection
		if (isset($_POST['collection'], $_SESSION['account_id'])) {
			// Retrieve the media ID
			$media_id = $pdo->lastInsertId();
			// Ensure the collection exists
			$stmt = $pdo->prepare('SELECT * FROM collections WHERE title = ? AND acc_id = ?');
			$stmt->execute([ $_POST['collection'], $_SESSION['account_id'] ]);
			$collection = $stmt->fetch(PDO::FETCH_ASSOC);
			// If exists, insert into database
			if ($collection) {
				$stmt = $pdo->prepare('INSERT INTO media_collections (collection_id,media_id) VALUES (?, ?)');
				$stmt->execute([ $collection['id'], $media_id ]);
			}
		}
	}
	// Output response
	echo $errors ? implode('<br>', $errors) : 'Upload Complete!' . (approval_required ? ' Your media will be approved by an administrator before being published.' : '');
	exit;
}
// Retrieve the user's collections if they're logged in
$user_collections = '';
if (isset($_SESSION['account_loggedin'])) {
	$stmt = $pdo->prepare('SELECT title FROM collections WHERE acc_id = ? ORDER BY title');
	$stmt->execute([ $_SESSION['account_id'] ]);
	$user_collections_obj = $stmt->fetchAll(PDO::FETCH_COLUMN);
	$user_collections = implode(',,', $user_collections_obj);
}
// Collection title param
$collection_title = isset($_GET['collection'], $user_collections_obj) && in_array($_GET['collection'], $user_collections_obj) ? $_GET['collection'] : '';
?>
<?=template_header('Upload Media')?>

<div class="page-content media-upload">

	<div class="page-title">
		<h2>Upload Media</h2>
	</div>

	<form action="upload.php" method="post" enctype="multipart/form-data" class="gallery-form" data-user-collections="<?=htmlspecialchars($user_collections, ENT_QUOTES)?>" data-image-max-size="<?=image_max_size?>" data-audio-max-size="<?=audio_max_size?>" data-video-max-size="<?=video_max_size?>" data-collection="<?=htmlspecialchars($collection_title, ENT_QUOTES)?>">

		<div id="media-upload-drop-zone">
			<svg width="64" height="64" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M144 480C64.5 480 0 415.5 0 336c0-62.8 40.2-116.2 96.2-135.9c-.1-2.7-.2-5.4-.2-8.1c0-88.4 71.6-160 160-160c59.3 0 111 32.2 138.7 80.2C409.9 102 428.3 96 448 96c53 0 96 43 96 96c0 12.2-2.3 23.8-6.4 34.6C596 238.4 640 290.1 640 352c0 70.7-57.3 128-128 128l-368 0zm79-217c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l39-39L296 392c0 13.3 10.7 24 24 24s24-10.7 24-24l0-134.1 39 39c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-80-80c-9.4-9.4-24.6-9.4-33.9 0l-80 80z"/></svg>
			<p class="drop-zone-txt">Select or drop your media files here!</p>
			<p class="drop-zone-filesize">Max <?=convert_filesize(image_max_size)?> for images, <?=convert_filesize(audio_max_size)?> for audio, <?=convert_filesize(video_max_size)?> for video</p>
		</div>

		<input type="file" name="media[]" multiple accept="audio/*,video/*,image/*" id="media">

		<div class="media-list"></div>

		<div class="btn-wrapper">
			<button type="submit" name="submit" id="submit_btn" class="btn">Upload Media</button>
			<div class="upload-result"></div>
		</div>
		
	</form>

</div>

<?=template_footer()?>