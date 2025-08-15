<?php
include 'functions.php';
// Make sure the GET ID param exists
if (isset($_GET['id'])) {
    // Retrieve the media from the media table using the GET request ID (URL param)
    $stmt = $pdo->prepare('SELECT * FROM media WHERE id = ? AND is_approved = 1');
    $stmt->execute([ $_GET['id'] ]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$media) {
        exit('Media does not exist with this ID!');
    }
    // Check whether the media is public or private
    if (!$media['is_public']) {
        if (!isset($_SESSION['account_id']) || (isset($_SESSION['account_id']) && $media['acc_id'] != $_SESSION['account_id'] && $_SESSION['account_role'] != 'Admin')) {
            exit('Private media!');
        }
    }
} else {
    exit('No ID specified!');
}
?>
<?=template_header(htmlspecialchars($media['title'], ENT_QUOTES))?>

<div class="page-content media-view">

    <div class="page-title">
        <h2><?=htmlspecialchars($media['title'], ENT_QUOTES)?></h2>
	</div>
	
	<p class="media-description"><?=htmlspecialchars($media['description_text'], ENT_QUOTES)?></p>

    <?php if (file_exists($media['filepath'])): ?>
    <?php if ($media['media_type'] == 'image'): ?>
    <img src="<?=$media['filepath']?>" alt="<?=htmlspecialchars($media['title'], ENT_QUOTES)?>" width="<?=getimagesize($media['filepath'])[0]?>" height="<?=getimagesize($media['filepath'])[1]?>">
    <?php elseif ($media['media_type'] == 'video'): ?>
    <video src="<?=$media['filepath']?>" width="852" height="480" controls autoplay></video>
    <?php elseif ($media['media_type'] == 'audio'): ?>
    <audio src="<?=$media['filepath']?>" controls autoplay></audio>
    <?php endif; ?>
    <?php else: ?>
    <p class="error-msg">Media file not found!</p>
    <?php endif; ?>

</div>

<?=template_footer()?>