<?php
/* Temporary Rebuild of Content Settings with Known-Good Tab Skeleton (from polls_table_transfer.php)
 * PURPOSE: Diagnose broken tabs by isolating structure. Once confirmed working, this file can replace content_settings.php.
 */
include_once '../assets/includes/main.php';
// Flash message helper (PRG pattern support)
if(session_status()===PHP_SESSION_NONE){ @session_start(); }

// ==== BEGIN (copied processing logic from content_settings.php) ====
$settings = [ 'home'=>[], 'sections'=>[], 'media'=>[], 'pages'=>[] ];
$messages = [];
$success = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $success = true;
  $is_upload_only = (isset($_POST['action']) && $_POST['action'] === 'upload_hero_image');

  // Generic (non-hero) uploads
  if (!empty($_FILES)) {
    foreach ($_FILES as $file_key => $file) {
      if ($file_key === 'home_hero_image') continue;
      if ($file['error'] === UPLOAD_ERR_OK) {
        $temp_name = $file['tmp_name'];
        $target_path = PROJECT_ROOT . '../../assets/img/about' . basename($file['name']);
        if (move_uploaded_file($temp_name, $target_path)) {
          $messages[] = 'File ' . basename($file['name']) . ' uploaded successfully.';
          $section = explode('_', $file_key)[0];
          $settings[$section][$file_key] = '../../assets/img/about/' . basename($file['name']);
        } else {
          $success = false;
          $messages[] = 'Error uploading ' . basename($file['name']);
        }
      }
    }
  }

  // Existing hero values
  $existingHero = [];
  if ($pdo) {
    try {
      $existingHero = $pdo->query("SELECT hero_headline, hero_subheadline, hero_background_image, hero_button_text, hero_button_link FROM setting_content_homepage LIMIT 1")->fetch(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) { $existingHero = []; }
  }

  $posted_hero = $is_upload_only ? [] : ($_POST['home'] ?? []);
  $hero_headline_p    = $is_upload_only ? ($existingHero['hero_headline'] ?? null)    : (array_key_exists('hero_headline', $posted_hero)    ? trim($posted_hero['hero_headline'])    : ($existingHero['hero_headline']    ?? null));
  $hero_subheadline_p = $is_upload_only ? ($existingHero['hero_subheadline'] ?? null) : (array_key_exists('hero_subheadline', $posted_hero) ? trim($posted_hero['hero_subheadline']) : ($existingHero['hero_subheadline'] ?? null));
  $hero_button_text_p = $is_upload_only ? ($existingHero['hero_button_text'] ?? null) : (array_key_exists('hero_button_text', $posted_hero) ? trim($posted_hero['hero_button_text']) : ($existingHero['hero_button_text'] ?? null));
  $hero_button_link_p = $existingHero['hero_button_link'] ?? null; // deferred
  $hero_image_select_p= $is_upload_only ? ($existingHero['hero_background_image'] ?? null) : (array_key_exists('hero_image', $posted_hero) ? trim($posted_hero['hero_image']) : ($existingHero['hero_background_image'] ?? null));
  if (!$is_upload_only && $hero_image_select_p === 'NONE') { $hero_image_select_p = ''; }

  // Hero image upload & resize
  if (!empty($_FILES['home_hero_image']) && $_FILES['home_hero_image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $upload = $_FILES['home_hero_image'];
    if ($upload['error'] === UPLOAD_ERR_OK) {
      $maxBytes = 2 * 1024 * 1024;
      if ($upload['size'] > $maxBytes) {
        $messages[] = 'Hero image too large.';
        $success = false;
      } else {
        $ext = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
        if (preg_match('/^(jpe?g|png|webp)$/', $ext)) {
          $destDir = PROJECT_ROOT . '/public_html/assets/img/hero-uploads/';
          if (!is_dir($destDir)) {@mkdir($destDir,0755,true);}                
          if (is_dir($destDir) && is_writable($destDir)) {
            $safeName = 'hero_' . date('Ymd_His') . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/','', $upload['name']);
            $destPath = $destDir . $safeName;
            $didProcess = false;
            if (function_exists('imagecreatetruecolor')) {
              $info = @getimagesize($upload['tmp_name']);
              if ($info) {
                $srcW=$info[0]; $srcH=$info[1]; $mime=$info['mime']??'';
                $create=null; $save=null; $alpha=false; $quality=82;
                if ($mime==='image/jpeg') { $create='imagecreatefromjpeg'; $save='imagejpeg'; }
                elseif ($mime==='image/png') { $create='imagecreatefrompng'; $save='imagepng'; $alpha=true; }
                elseif ($mime==='image/webp' && function_exists('imagecreatefromwebp') && function_exists('imagewebp')) { $create='imagecreatefromwebp'; $save='imagewebp'; $alpha=true; }
                if ($create && $save && function_exists($create) && function_exists($save)) {
                  $src=@$create($upload['tmp_name']);
                  if ($src) {
                    $tW=1920; $tH=1080; $scale=max($tW/$srcW,$tH/$srcH); $iW=(int)round($srcW*$scale); $iH=(int)round($srcH*$scale);
                    $inter=imagecreatetruecolor($iW,$iH);
                    if($alpha){imagealphablending($inter,false);imagesavealpha($inter,true);$c=imagecolorallocatealpha($inter,0,0,0,127);imagefilledrectangle($inter,0,0,$iW,$iH,$c);}    
                    imagecopyresampled($inter,$src,0,0,0,0,$iW,$iH,$srcW,$srcH);
                    imagedestroy($src);
                    $dest=imagecreatetruecolor($tW,$tH);
                    if($alpha){imagealphablending($dest,false);imagesavealpha($dest,true);$c2=imagecolorallocatealpha($dest,0,0,0,127);imagefilledrectangle($dest,0,0,$tW,$tH,$c2);}    
                    $cropX=(int)max(0,($iW-$tW)/2); $cropY=(int)max(0,($iH-$tH)/2);
                    imagecopy($dest,$inter,0,0,$cropX,$cropY,$tW,$tH);
                    imagedestroy($inter);
                    $saved=false;
                    if($save==='imagejpeg') $saved=@$save($dest,$destPath,$quality);
                    elseif($save==='imagepng') $saved=@$save($dest,$destPath,6);
                    elseif($save==='imagewebp') $saved=@$save($dest,$destPath,$quality);
                    imagedestroy($dest);
                    if($saved){ $didProcess=true; }
                  }
                }
              }
            }
            if(!$didProcess) {
              move_uploaded_file($upload['tmp_name'],$destPath);
            }
            if($success!==false){
              $hero_image_select_p = 'assets/img/hero-uploads/' . $safeName;
              $messages[] = 'Hero image uploaded & standardized.';
            }
          } else {
            $messages[]='Destination not writable.'; $success=false;
          }
        } else {
          $messages[]='Invalid hero image format.'; $success=false;
        }
      }
    }
    if($pdo && $success!==false) {
      try {
        $stmt = $pdo->prepare("UPDATE setting_content_homepage SET hero_headline=?, hero_subheadline=?, hero_background_image=?, hero_button_text=? LIMIT 1");
        $stmt->execute([$hero_headline_p,$hero_subheadline_p,$hero_image_select_p,$hero_button_text_p]);
      } catch(Exception $e){ /* ignore */ }
    }
  } else {
    // Non-upload hero settings save (text fields / selected image change only)
    if(!$is_upload_only && isset($_POST['action']) && $_POST['action']==='save_hero_settings' && $pdo && $success!==false) {
        try {
          $stmt = $pdo->prepare("UPDATE setting_content_homepage SET hero_headline=?, hero_subheadline=?, hero_background_image=?, hero_button_text=? LIMIT 1");
          $stmt->execute([$hero_headline_p,$hero_subheadline_p,$hero_image_select_p,$hero_button_text_p]);
          $messages[] = 'Hero settings saved.';
        } catch(Exception $e){ $messages[]='Hero save failed.'; $success=false; }
    }
  }
}
// After processing POST, if successful hero action, redirect (PRG) to avoid resubmission dialog & lingering disabled state
if($_SERVER['REQUEST_METHOD']==='POST' && $success!==false && isset($_POST['action']) && in_array($_POST['action'],['upload_hero_image','save_hero_settings'])){
  $_SESSION['flash_messages'] = $messages;
  header('Location: '. $_SERVER['PHP_SELF'] . '#hero');
  exit;
}
// Load current DB values for display
$hero_headline=$hero_subheadline=$hero_button_text=$hero_button_link=$hero_image='';
if($pdo){ $stmt=$pdo->query("SELECT hero_headline, hero_subheadline, hero_background_image, hero_button_text, hero_button_link FROM setting_content_homepage LIMIT 1"); if($row=$stmt->fetch(PDO::FETCH_ASSOC)){ $hero_headline=$row['hero_headline'];$hero_subheadline=$row['hero_subheadline'];$hero_image=$row['hero_background_image'];$hero_button_text=$row['hero_button_text'];$hero_button_link=$row['hero_button_link']; } }
// ==== END processing logic ====

$debugHero = isset($_GET['debug']) && $_GET['debug']==='hero';

?>
<?= template_admin_header('Content Settings (Temp Tabs)','settings','content-temp') ?>
<div class="container mt-3">
<?php
// Pull flash messages after redirect
if(empty($messages) && !empty($_SESSION['flash_messages'])){ $messages = $_SESSION['flash_messages']; unset($_SESSION['flash_messages']); }
foreach($messages as $m): ?><div class="alert alert-<?= $success===false?'danger':'success' ?> py-2 small mb-2"><?= htmlspecialchars($m) ?></div><?php endforeach; ?>
</div>
<!-- Migrated: Content Title Block -->
<div class="content-title mb-3">
  <div class="title d-flex align-items-center gap-3">
    <div class="icon"><i class="bi bi-pencil-square"></i></div>
    <div class="txt">
      <h2 class="mb-0">Content Settings</h2>
      <p class="mb-0 text-muted">Manage website content and media</p>
    </div>
  </div>
</div>
<div class="card">
  <div class="card-header">
    <h6 class="mb-0">Content Settings (Temp)</h6>
    <p class="text-muted mb-0 mt-1">Working tab skeleton cloned from polls system</p>
  </div>
  <div class="card-body">
    <div class="tab-nav" role="tablist" aria-label="Content settings tabs">
      <button type="button" class="tab-btn active" role="tab" aria-selected="true" aria-controls="hero-tab" id="hero-tab-btn" onclick="openTab(event,'hero-tab')">Hero</button>
      <button type="button" class="tab-btn" role="tab" aria-selected="false" aria-controls="services-tab" id="services-tab-btn" onclick="openTab(event,'services-tab')">Services</button>
      <button type="button" class="tab-btn" role="tab" aria-selected="false" aria-controls="clients-tab" id="clients-tab-btn" onclick="openTab(event,'clients-tab')">Clients</button>
      <button type="button" class="tab-btn" role="tab" aria-selected="false" aria-controls="pages-tab" id="pages-tab-btn" onclick="openTab(event,'pages-tab')">Pages</button>
    </div>

    <!-- Hero Tab -->
    <div id="hero-tab" class="tab-content active" role="tabpanel" aria-labelledby="hero-tab-btn">
      <!-- Hero Settings Form (includes upload) -->
      <form action="" method="post" enctype="multipart/form-data" id="heroSettingsForm" class="mt-2">
        <input type="hidden" name="action" value="save_hero_settings">
        <div class="row mb-2">
          <div class="col-md-6 mb-3">
            <label for="home_hero_headline" class="form-label fw-bold">Hero Headline</label>
            <input type="text" name="home[hero_headline]" id="home_hero_headline" class="form-control" value="<?= htmlspecialchars($hero_headline) ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label for="home_hero_subheadline" class="form-label fw-bold">Hero Subheadline</label>
            <input type="text" name="home[hero_subheadline]" id="home_hero_subheadline" class="form-control" value="<?= htmlspecialchars($hero_subheadline) ?>">
          </div>
        </div>
        <div class="border-top mb-3" style="border-top:1px solid #dee2e6 !important;"></div>
        <div class="col-12 mb-4">
          <label class="form-label fw-bold">Hero Background Images</label>
          <div class="border rounded p-2 bg-light" id="heroGalleryBlock">
            <div class="mt-1">
              <div class="input-group input-group-sm mb-2">
                <input type="file" name="home_hero_image" id="heroUploadInput" class="form-control custom-file-input" accept="image/jpeg,image/png,image/webp" aria-label="Upload hero image">
                <button type="button" class="btn btn-success" id="heroUploadBtn"><i class="bi bi-upload me-1"></i>Upload</button>
              </div>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-start" id="heroImageGrid" style="max-height:260px;overflow:auto;">
              <?php
              $hero_dir = PROJECT_ROOT . '/public_html/assets/img/hero-uploads/';
              $noneChecked = ($hero_image === '' || $hero_image === null);
              echo '<label class="hero-thumb none-option'.($noneChecked? ' selected':'').'" data-file=""><input type="radio" name="home[hero_image]" value="" '.($noneChecked? 'checked':'').'><div style="height:80px;display:flex;align-items:center;justify-content:center;border:2px dashed #ced4da;border-radius:6px;font-size:11px;color:#6c757d;background:#fafbfc;margin-bottom:4px;">No Bg Image</div><span class="meta">&nbsp;</span></label>';
              if(is_dir($hero_dir)){
                  $files = array_values(array_filter(scandir($hero_dir), fn($f)=> !is_dir($hero_dir.$f) && preg_match('/\.(jpe?g|png|webp)$/i',$f))); $has=false;
                  foreach($files as $file){ $rel='assets/img/hero-uploads/'.$file; $checked=$hero_image===$rel?'checked':''; $abs=$hero_dir.$file; // metadata suppressed
                    $src='../../'.$rel; echo '<label class="hero-thumb'.($checked?' selected':'').'" data-file="'.htmlspecialchars($rel).'"><input type="radio" name="home[hero_image]" value="'.htmlspecialchars($rel).'" '.$checked.'><img src="'.htmlspecialchars($src).'" alt="'.htmlspecialchars($file).'" style="width:100%;height:80px;object-fit:cover;border-radius:6px;display:block;margin-bottom:4px;"><span class="meta"><a href="#" class="hero-img-del-link text-danger text-decoration-none small" data-file="'.htmlspecialchars($rel).'" style="white-space:nowrap;"><i class="bi bi-trash me-1"></i>Delete</a></span></label>'; $has=true; }
                  if(!$has){ echo '<div class="small text-muted">No images uploaded yet.</div>'; }
              }
              ?>
            </div>
            <div class="mt-2">
              <div class="text-muted small">JPG/PNG/WebP up to 2MB. Standardized to 1920x1080 on upload.</div>
              <div id="heroGalleryStatus" class="small mt-1 text-muted" aria-live="polite"></div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label fw-bold" for="home_hero_button_text">Hero Button Text</label>
            <input type="text" name="home[hero_button_text]" id="home_hero_button_text" class="form-control" value="<?= htmlspecialchars($hero_button_text) ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold" for="home_hero_button_link">Hero Button Link (coming soon)</label>
            <input type="text" id="home_hero_button_link" class="form-control" value="<?= htmlspecialchars($hero_button_link) ?>" placeholder="https://example.com/target" disabled>
            <div class="form-text text-muted">Display only for now; link editing disabled.</div>
          </div>
        </div>
        <div class="d-flex justify-content-start align-items-center gap-2 mt-3">
          <button type="button" class="btn btn-sm btn-outline-secondary" id="heroCancelBtn"><i class="bi bi-arrow-counterclockwise me-1"></i>Cancel</button>
          <button type="submit" class="btn btn-sm btn-success" id="heroSaveBtn"><i class="bi bi-save me-1"></i>Save Hero Settings</button>
          <span class="hero-save-status small text-muted ms-2" aria-live="polite"></span>
        </div>
      </form>
      
    </div>

    <!-- Services Tab (migrated content) -->
    <div id="services-tab" class="tab-content" role="tabpanel" aria-labelledby="services-tab-btn">
      <?php
        // Load services & services section intro (mirrors original content_settings.php)
        $services = [];
        $servicesSectionTitle = $servicesSectionParagraph = '';
        if(isset($pdo) && $pdo){
            try {
                $stmtS = $pdo->query("SELECT id, area, slug, title, body, icon, position, active FROM content_items WHERE area IN('service','section') ORDER BY FIELD(area,'section','service'), position, id");
                while($r = $stmtS->fetch(PDO::FETCH_ASSOC)){
                    if($r['area']==='service') { $services[]=$r; }
                    elseif($r['area']==='section' && $r['slug']==='services') {
                        $servicesSectionTitle = $r['title'] ?? '';
                        $servicesSectionParagraph = $r['body'] ?? '';
                    }
                }
            } catch(Exception $e) { /* suppress in temp */ }
        }
      ?>
      <div class="row" id="servicesCardsRow">
        <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-3">
            <h3 class="mb-0">Services</h3>
            <span class="text-muted small">Stored in content_items (area = service)</span>
          </div>
          <div class="d-flex gap-2">
            <!-- Future: Add Service button -->
          </div>
        </div>
        <!-- Services Intro now first accordion item for consistency -->
        <div class="col-12 mb-3">
          <div class="accordion" id="servicesAccordion">
            <div class="accordion-item">
              <h2 class="accordion-header" id="services-intro-heading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#services-intro" aria-expanded="true" aria-controls="services-intro">
                  Services Intro
                </button>
              </h2>
              <div id="services-intro" class="accordion-collapse collapse show" aria-labelledby="services-intro-heading" data-bs-parent="#servicesAccordion">
                <div class="accordion-body">
                  <div class="d-flex justify-content-end gap-2 mb-3 flex-wrap">
                    <span class="service-intro-status small text-muted" aria-live="polite"></span>
                    <button type="button" class="btn btn-sm btn-success" id="servicesIntroSaveBtn"><i class="bi bi-save me-1"></i> Save Intro</button>
                  </div>
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label fw-bold" for="services_section_title_input">Services Section Title</label>
                      <input type="text" id="services_section_title_input" class="form-control" name="services_section_title" value="<?= htmlspecialchars($servicesSectionTitle) ?>" placeholder="Section heading">
                    </div>
                    <div class="col-md-8">
                      <label class="form-label fw-bold" for="services_section_paragraph_input">Services Section Paragraph</label>
                      <textarea id="services_section_paragraph_input" class="form-control summernote" name="services_section_paragraph" rows="2" placeholder="Intro summary paragraph"><?= htmlspecialchars($servicesSectionParagraph) ?></textarea>
                      <div class="form-text">Rich text supported (stored as HTML).</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Existing service items will append after this intro item -->
    <?php $svcIndex=0; foreach($services as $s): $svcIndex++; $accId = 'svc'.$s['id']; $neg = (isset($s['id']) && (int)$s['id'] < 0); ?>
            <div class="accordion-item service-card-wrapper" data-service-id="<?= (int)$s['id'] ?>">
              <h2 class="accordion-header" id="heading-<?= $accId ?>">
                <button class="accordion-button collapsed d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $accId ?>" aria-expanded="false" aria-controls="collapse-<?= $accId ?>">
      <span class="me-3 text-truncate">Service <?= $svcIndex ?><?php if($neg): ?> <span class="badge bg-warning text-dark ms-2">Neg ID</span><?php endif; ?></span>
                  <span class="badge bg-secondary ms-auto">Edit</span>
                </button>
              </h2>
              <div id="collapse-<?= $accId ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $accId ?>" data-bs-parent="#servicesAccordion">
                <div class="accordion-body service-card" data-service-id="<?= (int)$s['id'] ?>">
      <?php if($neg): ?>
      <div class="alert alert-warning py-2 small mb-3">This service has a negative ID. You can still edit/save it, but consider normalizing IDs in the database (use positive auto-increment) later.</div>
      <?php endif; ?>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Title</label>
                      <input type="text" name="services[<?= (int)$s['id'] ?>][title]" class="form-control service-title" value="<?= htmlspecialchars($s['title'] ?? '') ?>" autocomplete="off">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Icon (HTML or class)</label>
                      <input type="text" name="services[<?= (int)$s['id'] ?>][icon]" class="form-control service-icon" value="<?= htmlspecialchars($s['icon'] ?? '') ?>" placeholder='<i class="bi bi-gear"></i>' autocomplete="off">
                      <div class="form-text">Raw HTML or class name.</div>
                    </div>
                    <?php if(preg_match('/^service-[1-6]$/', $s['slug'])): 
                      // Extract service number from slug
                      $serviceNum = substr($s['slug'], -1);
                      $serviceSlug = $s['slug'];
                      
                      // Prepare service gallery file list
                      $service_files = [];
                      $service_image = ''; // TODO: Get from database or config
                      $service_dir = PROJECT_ROOT.'/public_html/assets/img/services/'.$serviceSlug.'/';
                      $service_rel_dir = 'assets/img/services/'.$serviceSlug.'/';
                      if(is_dir($service_dir)){
                        foreach(scandir($service_dir) as $f){
                          if($f==='.'||$f==='..') continue;
                          if(!preg_match('/\.(jpe?g|png|webp)$/i',$f)) continue;
                          $rel = $service_rel_dir.$f; $full=$service_dir.$f;
                          $service_files[] = ['file'=>$f,'rel'=>$rel,'mtime'=>@filemtime($full) ?: 0];
                        }
                        usort($service_files,function($a,$b){ return $b['mtime'] <=> $a['mtime']; });
                      }
                    ?>
                    <div class="col-12">
                      <label class="form-label fw-bold">Service Images</label>
                      <div class="border rounded p-2 bg-light" id="<?= $serviceSlug ?>GalleryBlock">
                        <div class="mt-1">
                          <div class="input-group input-group-sm mb-2">
                            <input type="file" accept="image/*" id="<?= $serviceSlug ?>GalleryUploadFile" class="form-control custom-file-input" aria-label="Upload <?= $serviceSlug ?> image">
                            <button class="btn btn-success" type="button" id="<?= $serviceSlug ?>GalleryUploadBtn"><i class="bi bi-upload me-1"></i>Upload</button>
                          </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-start" id="<?= $serviceSlug ?>Thumbs" style="max-height:260px;overflow:auto;">
                          <?php
                            $noneSelected = $service_image === '';
                            echo '<label class="hero-thumb none-option'.($noneSelected?' selected':'').'" data-file=""><input type="radio" name="'.$serviceSlug.'_image_choice" value="" '.($noneSelected?'checked':'').'><div style="height:80px;display:flex;align-items:center;justify-content:center;border:2px dashed #ced4da;border-radius:6px;font-size:11px;color:#6c757d;background:#fafbfc;margin-bottom:4px;">No Image</div><span class="meta">&nbsp;</span></label>';
                            foreach($service_files as $sf){
                              $rel = $sf['rel']; $file = $sf['file'];
                              $checked = ($rel === $service_image) ? ' checked' : '';
                              $selClass = $checked ? ' selected' : '';
                              $src='../../'.$rel;
                              echo '<label class="hero-thumb'.$selClass.'" data-file="'.htmlspecialchars($rel).'"><input type="radio" name="'.$serviceSlug.'_image_choice" value="'.htmlspecialchars($rel).'"'.$checked.'><img src="'.htmlspecialchars($src).'" alt="'.htmlspecialchars($file).'" style="width:100%;height:80px;object-fit:cover;border-radius:6px;display:block;margin-bottom:4px;"><span class="meta"><a href="#" class="'.$serviceSlug.'-img-del-link text-danger text-decoration-none small" data-file="'.htmlspecialchars($rel).'" style="white-space:nowrap;"><i class="bi bi-trash"></i> delete</a></span></label>';
                            }
                          ?>
                        </div>
                        <div class="mt-2">
                          <div class="text-muted small">JPG/PNG/WebP up to 2MB. Select one image for Service <?= $serviceNum ?>.</div>
                          <div id="<?= $serviceSlug ?>GalleryStatus" class="small mt-1 text-muted" aria-live="polite"></div>
                        </div>
                      </div>
                    </div>
                    <?php endif; ?>
                    <div class="col-12">
                      <label class="form-label fw-bold">Description</label>
                      <textarea name="services[<?= (int)$s['id'] ?>][body]" class="form-control summernote service-body" rows="4"><?= htmlspecialchars($s['body'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                      <div class="d-flex justify-content-start align-items-center gap-2 mt-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary service-cancel-btn" data-service-id="<?= (int)$s['id'] ?>">
                          <i class="bi bi-arrow-counterclockwise me-1"></i>Cancel
                        </button>
                        <button type="button" class="btn btn-sm btn-success service-save-btn"><i class="bi bi-save me-1" aria-hidden="true"></i> <span class="visually-hidden-focusable">Save changes to service <?= (int)$s['id'] ?></span>Save Service <?= $svcIndex ?></button>
                        <span class="service-save-status small text-muted ms-2" aria-live="polite"></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php if(empty($services)): ?>
          <div class="col-12"><div class="alert alert-warning small">No service records found. (Seed data may be required.)</div></div>
        <?php endif; ?>
  <!-- (Removed previous duplicate accordion rendering now that intro is integrated) -->
      </div>
    </div>

    <!-- Clients Tab (migrated content) -->
    <div id="clients-tab" class="tab-content" role="tabpanel" aria-labelledby="clients-tab-btn">
      <div class="row">
        <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
          <h3 class="mb-0">Clients</h3>
          <span class="text-muted small">Logos & testimonials (upcoming)</span>
        </div>
        <div class="col-12">
          <div class="accordion" id="clientsAccordion">
            <div class="accordion-item">
              <h2 class="accordion-header" id="client-logos-heading">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#client-logos" aria-expanded="true" aria-controls="client-logos">Client Logos</button>
              </h2>
              <div id="client-logos" class="accordion-collapse collapse show" aria-labelledby="client-logos-heading" data-bs-parent="#clientsAccordion">
                <div class="accordion-body">
                  <div class="alert alert-info small mb-3">Future feature: upload, reorder, activate/deactivate logos with per-item save.</div>
                  <ul class="list-unstyled mb-0" id="clientLogoList"><li class="text-muted small">(No logos yet)</li></ul>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="client-testimonials-heading">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#client-testimonials" aria-expanded="false" aria-controls="client-testimonials">Testimonials</button>
              </h2>
              <div id="client-testimonials" class="accordion-collapse collapse" aria-labelledby="client-testimonials-heading" data-bs-parent="#clientsAccordion">
                <div class="accordion-body">
                  <div class="alert alert-info small mb-0">Future feature: testimonial CRUD interface.</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pages Tab (per-page accordions with AJAX save) -->
    <div id="pages-tab" class="tab-content" role="tabpanel" aria-labelledby="pages-tab-btn">
      <?php
        // Static list for now (exclude accessibility per policy)
        $pages_list = [
          'about' => 'About Us',
          'services' => 'Services',
          'contact' => 'Contact',
          'privacy' => 'Privacy Policy',
          'terms' => 'Terms of Service'
        ];
        $page_payloads = [];
        if(isset($pdo)){
          include_once PROJECT_ROOT.'/public_html/assets/includes/content_repository.php';
          foreach(array_keys($pages_list) as $slugTmp){
            $page_payloads[$slugTmp] = pages_get_payload($pdo,$slugTmp);
            $meta = pages_get_metadata($pdo,$slugTmp);
            if($meta){
              $settings['pages'][$slugTmp.'_meta_title'] = $meta['meta_title'] ?? '';
              $settings['pages'][$slugTmp.'_meta_description'] = $meta['meta_description'] ?? '';
            }
          }
        }
      ?>
      <div class="accordion" id="pagesAccordion">
        <div class="mb-3 small text-muted ps-1">Edit standard site pages below. Accessibility page is managed separately.</div>
        <?php foreach($pages_list as $page_key => $page_title):
          $page_content = $settings['pages'][$page_key.'_content'] ?? '';
          $page_meta_title = $settings['pages'][$page_key.'_meta_title'] ?? '';
          $page_meta_desc = $settings['pages'][$page_key.'_meta_description'] ?? '';
          $payload = $page_payloads[$page_key] ?? [];
          // Use stored payload body if available so edits persist after save
          if(isset($payload['body'])){ $page_content = $payload['body']; }
          $about_heading = $payload['heading'] ?? ($settings['pages']['about_heading'] ?? 'About Us');
          $about_intro = $payload['intro'] ?? ($settings['pages']['about_intro'] ?? '');
          $about_image = $payload['image'] ?? ($settings['pages']['about_image'] ?? '');
          // Normalize About image path for display.
          // Stored payload path is like: assets/img/about/about.webp (no leading slash).
          // We are currently in /public_html/admin/settings/ so we must traverse up two levels to reach /public_html/.
          $about_image_display = '';
          if($about_image){
            $clean_about_image = ltrim($about_image,'/'); // strip any accidental leading slash
            // Always construct ../../assets/... so the <img> resolves correctly regardless of leading slash presence.
            $about_image_display = '../../' . $clean_about_image;
          }
          // Prepare About gallery file list
          $about_files = [];
          if($page_key==='about'){
            $about_dir = PROJECT_ROOT.'/public_html/assets/img/about/';
            $about_rel_dir = 'assets/img/about/';
            if(is_dir($about_dir)){
              foreach(scandir($about_dir) as $f){
                if($f==='.'||$f==='..') continue;
                if(!preg_match('/\.(jpe?g|png|webp)$/i',$f)) continue;
                $rel = $about_rel_dir.$f; $full=$about_dir.$f;
                $about_files[] = ['file'=>$f,'rel'=>$rel,'mtime'=>@filemtime($full) ?: 0];
              }
              usort($about_files,function($a,$b){ return $b['mtime'] <=> $a['mtime']; });
            }
          }
          $collapse_id = 'page-'.$page_key.'-body';
          $heading_id = 'page-'.$page_key.'-heading';
        ?>
        <div class="accordion-item page-item" data-page-slug="<?= htmlspecialchars($page_key) ?>">
          <h2 class="accordion-header" id="<?= $heading_id ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapse_id ?>" aria-expanded="false" aria-controls="<?= $collapse_id ?>">
              <?= htmlspecialchars($page_title) ?>
            </button>
          </h2>
          <div id="<?= $collapse_id ?>" class="accordion-collapse collapse" aria-labelledby="<?= $heading_id ?>" data-bs-parent="#pagesAccordion">
            <div class="accordion-body">
              <!-- action buttons moved to bottom -->
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold">Meta Title (SEO)</label>
                  <input type="text" name="pages[<?= $page_key ?>_meta_title]" class="form-control" value="<?= htmlspecialchars($page_meta_title) ?>" maxlength="70">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">Meta Description (SEO)</label>
                  <input type="text" name="pages[<?= $page_key ?>_meta_description]" class="form-control" value="<?= htmlspecialchars($page_meta_desc) ?>" maxlength="160">
                </div>
                <?php if($page_key==='about'): ?>
                  
                  <div class="col-12">
                    <label class="form-label fw-bold">About Page Heading</label>
                    <input type="text" class="form-control" name="pages[about_heading]" value="<?= htmlspecialchars($about_heading) ?>" placeholder="About Us">
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-bold">Intro Text</label>
                    <textarea class="form-control" name="pages[about_intro]" rows="5" placeholder="Short intro paragraph"><?= htmlspecialchars($about_intro) ?></textarea>
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-bold">About Images</label>
                    <div class="border rounded p-2 bg-light" id="aboutGalleryBlock">
                      <div class="mt-1">
                        <div class="input-group input-group-sm mb-2">
                          <input type="file" accept="image/*" id="aboutGalleryUploadFile" class="form-control custom-file-input" aria-label="Upload about image">
                          <button class="btn btn-success" type="button" id="aboutGalleryUploadBtn"><i class="bi bi-upload me-1"></i>Upload</button>
                        </div>
                      </div>
                      <div class="d-flex flex-wrap gap-2 align-items-start" id="aboutThumbs" style="max-height:260px;overflow:auto;">
                        <?php
                          $noneSelected = $about_image === '';
                          echo '<label class="hero-thumb none-option'.($noneSelected?' selected':'').'" data-file=""><input type="radio" name="about_image_choice" value="" '.($noneSelected?'checked':'').'><div style="height:80px;display:flex;align-items:center;justify-content:center;border:2px dashed #ced4da;border-radius:6px;font-size:11px;color:#6c757d;background:#fafbfc;margin-bottom:4px;">No Image</div><span class="meta">&nbsp;</span></label>';
                          foreach($about_files as $af){
                            $rel = $af['rel']; $file = $af['file'];
                            $checked = ($rel === $about_image) ? ' checked' : '';
                            $selClass = $checked ? ' selected' : '';
                            $src='../../'.$rel;
                            echo '<label class="hero-thumb'.$selClass.'" data-file="'.htmlspecialchars($rel).'"><input type="radio" name="about_image_choice" value="'.htmlspecialchars($rel).'"'.$checked.'><img src="'.htmlspecialchars($src).'" alt="'.htmlspecialchars($file).'" style="width:100%;height:80px;object-fit:cover;border-radius:6px;display:block;margin-bottom:4px;"><span class="meta"><a href="#" class="about-img-del-link text-danger text-decoration-none small" data-file="'.htmlspecialchars($rel).'" style="white-space:nowrap;"><i class="bi bi-trash"></i> delete</a></span></label>';
                          }
                        ?>
                      </div>
                      <div class="mt-2">
                        <div class="text-muted small">JPG/PNG/WebP up to 2MB. Select one image for the About page.</div>
                        <div id="aboutGalleryStatus" class="small mt-1 text-muted" aria-live="polite"></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-12">
                    <label class="form-label fw-bold">Main Body Content</label>
                    <textarea name="pages[<?= $page_key ?>_content]" class="form-control summernote" rows="10"><?= htmlspecialchars($page_content) ?></textarea>
                  </div>
                <?php else: ?>
                  <div class="col-12">
                    <label class="form-label fw-bold">Page Content</label>
                    <textarea name="pages[<?= $page_key ?>_content]" class="form-control summernote" rows="10"><?= htmlspecialchars($page_content) ?></textarea>
                  </div>
                <?php endif; ?>
                <div class="col-12">
                  <div class="d-flex justify-content-start align-items-center gap-2 mt-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary page-cancel-btn" data-page-slug="<?= htmlspecialchars($page_key) ?>">
                      <i class="bi bi-arrow-counterclockwise me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-sm btn-success page-save-btn" data-page-slug="<?= htmlspecialchars($page_key) ?>">
                      <i class="bi bi-save me-1"></i>Save Page
                    </button>
                    <span class="page-save-status small text-muted ms-2" data-page-slug="<?= htmlspecialchars($page_key) ?>" aria-live="polite"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if(empty($pages_list)): ?><div class="alert alert-warning small">No pages configured.</div><?php endif; ?>

<?php /* 
======= IMAGE UPLOAD TEMPLATE =======
Based on the About Us and Hero sections above. This template can be copied for other pages/sections.

FEATURES:
- File input with custom blue styling (rgb(74, 121, 180))
- Green upload button (btn-success)
- Thumbnail gallery with radio selection
- Delete functionality for uploaded images
- Database path storage
- Cancel and Save buttons (btn-sm sizing)

HTML STRUCTURE:
<div class="col-12">
  <label class="form-label fw-bold">[SECTION] Images</label>
  <div class="border rounded p-2 bg-light" id="[section]GalleryBlock">
    <div class="mt-1">
      <div class="input-group input-group-sm mb-2">
        <input type="file" accept="image/*" id="[section]GalleryUploadFile" class="form-control custom-file-input" aria-label="Upload [section] image">
        <button class="btn btn-success" type="button" id="[section]GalleryUploadBtn"><i class="bi bi-upload me-1"></i>Upload</button>
      </div>
    </div>
    <div class="d-flex flex-wrap gap-2 align-items-start" id="[section]Thumbs" style="max-height:260px;overflow:auto;">
      <!-- PHP thumbnail loop here -->
    </div>
    <div class="mt-2">
      <div class="text-muted small">JPG/PNG/WebP up to 2MB. Select one image for the [Section] page.</div>
      <div id="[section]GalleryStatus" class="small mt-1 text-muted" aria-live="polite"></div>
    </div>
  </div>
</div>

BUTTON STRUCTURE (consistent across all sections):
<div class="d-flex justify-content-start align-items-center gap-2 mt-3">
  <button type="button" class="btn btn-sm btn-outline-secondary [section]-cancel-btn">
    <i class="bi bi-arrow-counterclockwise me-1"></i>Cancel
  </button>
  <button type="submit" class="btn btn-sm btn-success [section]-save-btn">
    <i class="bi bi-save me-1"></i>Save [Section] [Number]
  </button>
  <span class="[section]-save-status small text-muted ms-2" aria-live="polite"></span>
</div>

SERVICE LAYOUT (service-1 example):
- Title and Icon fields in top row (col-md-6 each)
- Service Images in full-width row (col-12) 
- Description in full-width row (col-12)
- Cancel/Save buttons at bottom (col-12)
- Accordion titles show "Service 1", "Service 2", etc.

CSS STYLES:
.custom-file-input {
  background-color: rgb(74, 121, 180);
  border-color: rgb(74, 121, 180);
  color: white;
}
.custom-file-input:focus {
  background-color: rgb(64, 111, 170);
  border-color: rgb(64, 111, 170);
  box-shadow: 0 0 0 0.2rem rgba(74, 121, 180, 0.25);
}

JAVASCRIPT HANDLERS:
- About: POST to ajax_page_image_upload.php with slug=about, image=file
- Services 1-6: POST to ajax_page_image_upload.php with slug=service-1 through service-6, image=file
- Hero: POST to current page with action=upload_hero_image, home_hero_image=file (special processing)
- Delete: POST to ajax_page_image_delete.php with slug=[section], path=filename
- Response format: {ok: true/false, path: "assets/img/section/file.jpg", error: "message"}

BACKEND FILES NEEDED:
- ajax_page_image_upload.php (handles file upload, supports about, service-1 through service-6, returns JSON)
- ajax_page_image_delete.php (handles file deletion, supports same paths, returns JSON)
- For Hero: built-in processing in main form handler

SUPPORTED UPLOAD DIRECTORIES:
- about → assets/img/about/
- service-1 through service-6 → assets/img/services/service-1/ through assets/img/services/service-6/
- other → assets/img/page-uploads/

SERVICES IMPLEMENTATION:
All services (service-1 through service-6) now have:
- Dynamic image upload UI with blue file input and green upload button
- Individual thumbnail galleries with radio selection
- Delete functionality per service
- Separate directories per service for organized file management

Replace [section] with: about, service-1, service-2, service-3, service-4, service-5, service-6, portfolio, team, etc.
*/ ?>
    </div>
  </div>
</div>

<style>
/* Tab Navigation (copied from polls_table_transfer.php) */
.tab-nav {display:flex;border-bottom:2px solid #dee2e6;margin-bottom:0;position:relative;background-color:transparent;padding:1rem 0 0 0;}
.tab-btn {background:#f8f9fa;border:2px solid #dee2e6;border-bottom:2px solid #dee2e6;padding:12px 24px;cursor:pointer;font-size:14px;font-weight:500;color:#6c757d;transition:all .3s ease;border-radius:8px 8px 0 0;margin-right:4px;position:relative;outline:none;}
.tab-btn:hover {color:#495057;background:#e9ecef;border-color:#adb5bd;border-bottom-color:#adb5bd;}
.tab-btn.active,.tab-btn[aria-selected="true"] {color:#0d6efd;background:#fff;border-color:#dee2e6 #dee2e6 transparent;font-weight:600;z-index:2;border-bottom:2px solid #fff;margin-bottom:-2px;}
.tab-content {display:none;padding:30px;background:#fff;border:2px solid #dee2e6;border-top:none;border-radius:0 8px 8px 8px;margin-top:0;margin-left:0;}
.tab-content.active {display:block;}
.hero-thumb{position:relative;cursor:pointer;flex:0 0 auto;width:140px;text-align:center;font-size:11px;background:#fff;border:2px solid transparent;border-radius:8px;padding:6px;display:flex;flex-direction:column;}
.hero-thumb.selected{border-color:#0d6efd;box-shadow:0 0 0 2px rgba(13,110,253,.35);} 
.hero-thumb input{position:absolute;opacity:0;}
.hero-thumb .meta{display:block;color:#6c757d;font-size:10px;line-height:1.1;}
.custom-file-input {
  background-color: rgb(74, 121, 180);
  border-color: rgb(74, 121, 180);
  color: white;
}
.custom-file-input:focus {
  background-color: rgb(64, 111, 170);
  border-color: rgb(64, 111, 170);
  box-shadow: 0 0 0 0.2rem rgba(74, 121, 180, 0.25);
}
</style>
<script>
// Robust safe selector escaper (fallback if CSS.escape not present)
function _safeSel(id){ return id.replace(/[^a-zA-Z0-9_-]/g, s=> '\\'+s); }
function openTab(evt, tabName){
  if(evt){ evt.preventDefault(); }
  var container = (evt && evt.currentTarget && evt.currentTarget.closest('.card')) || document;
  var panels = container.querySelectorAll('.tab-content');
  panels.forEach(p=>{ p.classList.remove('active'); p.setAttribute('hidden','hidden'); });
  var buttons = container.querySelectorAll('.tab-btn');
  buttons.forEach(b=>{ b.classList.remove('active'); b.setAttribute('aria-selected','false'); });
  var target = container.querySelector('#'+ (window.CSS && CSS.escape? CSS.escape(tabName) : _safeSel(tabName)) );
  if(target){ target.classList.add('active'); target.removeAttribute('hidden'); }
  if(evt && evt.currentTarget){ evt.currentTarget.classList.add('active'); evt.currentTarget.setAttribute('aria-selected','true'); }
  if(history.replaceState && target){ requestAnimationFrame(()=>history.replaceState(null,'','#'+tabName.replace(/-tab$/,''))); }
}
document.addEventListener('DOMContentLoaded',()=>{
  const AJAX_BASE = '../'; // relative from /admin/settings/ to /admin/
  // Attach delegated handler to ensure future buttons also work
  document.querySelectorAll('.tab-nav .tab-btn').forEach(btn=>{
    if(btn.dataset.bound) return; btn.dataset.bound='1';
    btn.addEventListener('click',function(e){ openTab(e, (this.getAttribute('aria-controls') || '').replace(/^(#?)/,'') ); });
    btn.addEventListener('keydown',function(e){ if(e.key==='Enter' || e.key===' '){ openTab(e, (this.getAttribute('aria-controls')||'').replace(/^(#?)/,'') ); } });
  });
  // About image gallery handlers
  (function(){
    const block = document.getElementById('aboutGalleryBlock'); if(!block) return;
    const thumbsWrap = document.getElementById('aboutThumbs');
    const fileInput = document.getElementById('aboutGalleryUploadFile');
    const uploadBtn = document.getElementById('aboutGalleryUploadBtn');
    const statusEl = document.getElementById('aboutGalleryStatus');
    function setStatus(msg,type){ if(statusEl){ statusEl.textContent=msg; statusEl.className='small mt-1 '+(type==='err'?'text-danger':type==='ok'?'text-success':'text-muted'); } }
    function syncSel(){ thumbsWrap.querySelectorAll('label.hero-thumb').forEach(l=>{ const r=l.querySelector('input[type=radio]'); if(r){ l.classList.toggle('selected', r.checked); } }); }
    thumbsWrap.addEventListener('change', e=>{ if(e.target.matches('input[name="about_image_choice"]')) syncSel(); });
    thumbsWrap.addEventListener('click', e=>{
      const del = e.target.closest('.about-img-del-link'); if(!del) return; e.preventDefault();
      const rel = del.getAttribute('data-file'); if(!rel) return; if(!confirm('Delete this image permanently?')) return;
      const orig = del.innerHTML; del.classList.add('disabled','pe-none'); del.innerHTML='<span class="spinner-border spinner-border-sm"></span>';
      const delCandidates=['ajax_page_image_delete.php','../ajax_page_image_delete.php']; let attempt=0;
      function attemptDel(){
        fetch(delCandidates[attempt],{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},body:new URLSearchParams({slug:'about',path:rel})})
          .then(async r=>{ const raw=await r.text(); if(!r.ok) throw new Error('HTTP '+r.status); let d; try{ d=JSON.parse(raw);}catch(e){ throw new Error('Bad JSON'); } return d; })
          .then(d=>{ if(d.ok){ const label=del.closest('label.hero-thumb'); if(label){ const wasSel=label.querySelector('input[type=radio]')?.checked; label.remove(); if(wasSel){ const none=thumbsWrap.querySelector('.hero-thumb.none-option input[type=radio]'); if(none){ none.checked=true; syncSel(); } } } } else { alert('Delete failed: '+(d.error||'Unknown')); del.innerHTML=orig; del.classList.remove('disabled','pe-none'); } })
          .catch(err=>{ if(attempt<delCandidates.length-1){ attempt++; attemptDel(); } else { alert('Delete error: '+(err && err.message? err.message:'Network')); del.innerHTML=orig; del.classList.remove('disabled','pe-none'); } });
      }
      attemptDel();
    });
    if(uploadBtn){
      uploadBtn.addEventListener('click',()=>{
        if(!fileInput.files || !fileInput.files[0]){ setStatus('Choose a file first','err'); return; }
        const f=fileInput.files[0]; if(f.size>2*1024*1024){ setStatus('File too large (>2MB)','err'); return; }
        if(!/\.(jpe?g|png|webp)$/i.test(f.name)){ setStatus('Invalid format','err'); return; }
        setStatus('Uploading...');
        const fd=new FormData(); fd.append('slug','about'); fd.append('image',f);
        const uploadCandidates=['ajax_page_image_upload.php','../ajax_page_image_upload.php']; let upAttempt=0;
        function attemptUp(){
          fetch(uploadCandidates[upAttempt],{method:'POST',body:fd,headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(async r=>{ const raw=await r.text(); if(!r.ok) throw new Error('HTTP '+r.status); let d; try{ d=JSON.parse(raw);}catch(e){ throw new Error('Bad JSON'); } return d; })
            .then(d=>{ if(d.ok){ setStatus('Uploaded','ok'); const rel=d.path; const src='../../'+rel; const label=document.createElement('label'); label.className='hero-thumb selected'; label.setAttribute('data-file',rel); label.innerHTML='<input type="radio" name="about_image_choice" value="'+rel+'" checked><img src="'+src+'?v='+Date.now()+'" alt="about image" style="width:100%;height:80px;object-fit:cover;border-radius:6px;display:block;margin-bottom:4px;"><span class="meta"><a href="#" class="about-img-del-link text-danger text-decoration-none small" data-file="'+rel+'" style="white-space:nowrap;"><i class="bi bi-trash"></i> delete</a></span>'; const none=thumbsWrap.querySelector('.hero-thumb.none-option'); if(none && none.nextSibling){ thumbsWrap.insertBefore(label, none.nextSibling); } else { thumbsWrap.appendChild(label); } thumbsWrap.querySelectorAll('label.hero-thumb').forEach(l=>{ if(l!==label) l.classList.remove('selected'); }); fileInput.value=''; } else { setStatus(d.error||'Upload error','err'); } })
            .catch(err=>{ if(upAttempt<uploadCandidates.length-1){ upAttempt++; attemptUp(); } else { setStatus(err && err.message? err.message:'Network error','err'); } });
        }
        attemptUp();
      });
    }
    syncSel();
  })();

  // Hero image gallery handlers (similar to About)
  (function(){
    const heroGrid = document.getElementById('heroImageGrid');
    const heroUploadBtn = document.getElementById('heroUploadBtn');
    const heroFileInput = document.getElementById('heroUploadInput');
    const heroStatusEl = document.getElementById('heroGalleryStatus');
    if(!heroGrid || !heroUploadBtn || !heroFileInput) return;
    
    function setHeroStatus(msg,type){ if(heroStatusEl){ heroStatusEl.textContent=msg; heroStatusEl.className='small mt-1 '+(type==='err'?'text-danger':type==='ok'?'text-success':'text-muted'); } }
    function syncHeroSel(){ heroGrid.querySelectorAll('label.hero-thumb').forEach(l=>{ const r=l.querySelector('input[type=radio]'); if(r){ l.classList.toggle('selected', r.checked); } }); }
    heroGrid.addEventListener('change', e=>{ if(e.target.matches('input[name="home[hero_image]"]')) syncHeroSel(); });
    
    heroUploadBtn.addEventListener('click',()=>{
      if(!heroFileInput.files || !heroFileInput.files[0]){ setHeroStatus('Choose a file first','err'); return; }
      const f=heroFileInput.files[0]; if(f.size>2*1024*1024){ setHeroStatus('File too large (>2MB)','err'); return; }
      if(!/\.(jpe?g|png|webp)$/i.test(f.name)){ setHeroStatus('Invalid format','err'); return; }
      setHeroStatus('Uploading...');
      const fd=new FormData(); fd.append('action','upload_hero_image'); fd.append('home_hero_image',f);
      // Use the traditional form submission since Hero has special processing
      fetch(window.location.href,{method:'POST',body:fd,headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(async r=>{ const raw=await r.text(); if(!r.ok) throw new Error('HTTP '+r.status); 
          // Check if upload was successful by looking for success message
          if(raw.includes('Hero image uploaded')) { 
            setHeroStatus('Uploaded','ok'); 
            // Refresh the page to show new image 
            setTimeout(()=>location.reload(),1000);
          } else if(raw.includes('error') || raw.includes('Error')) { 
            setHeroStatus('Upload error','err'); 
          } else { 
            setHeroStatus('Upload complete','ok'); 
            setTimeout(()=>location.reload(),1000);
          } 
        })
        .catch(err=>{ setHeroStatus(err && err.message? err.message:'Network error','err'); });
    });
    syncHeroSel();
  })();

  // Service image gallery handlers (dynamic for service-1 through service-6)
  (function(){
    ['service-1', 'service-2', 'service-3', 'service-4', 'service-5', 'service-6'].forEach(serviceSlug => {
      const serviceThumbs = document.getElementById(serviceSlug + 'Thumbs');
      const serviceUploadBtn = document.getElementById(serviceSlug + 'GalleryUploadBtn');
      const serviceFileInput = document.getElementById(serviceSlug + 'GalleryUploadFile');
      const serviceStatusEl = document.getElementById(serviceSlug + 'GalleryStatus');
      if(!serviceThumbs || !serviceUploadBtn || !serviceFileInput) return;
      
      function setServiceStatus(msg,type){ if(serviceStatusEl){ serviceStatusEl.textContent=msg; serviceStatusEl.className='small mt-1 '+(type==='err'?'text-danger':type==='ok'?'text-success':'text-muted'); } }
      function syncServiceSel(){ serviceThumbs.querySelectorAll('label.hero-thumb').forEach(l=>{ const r=l.querySelector('input[type=radio]'); if(r){ l.classList.toggle('selected', r.checked); } }); }
      serviceThumbs.addEventListener('change', e=>{ if(e.target.matches('input[name="' + serviceSlug + '_image_choice"]')) syncServiceSel(); });
      serviceThumbs.addEventListener('click', e=>{
        const del = e.target.closest('.' + serviceSlug + '-img-del-link'); if(!del) return; e.preventDefault();
        const rel = del.getAttribute('data-file'); if(!rel) return; if(!confirm('Delete this image permanently?')) return;
        const orig = del.innerHTML; del.classList.add('disabled','pe-none'); del.innerHTML='<span class="spinner-border spinner-border-sm"></span>';
        const delCandidates=['ajax_page_image_delete.php','../ajax_page_image_delete.php']; let attempt=0;
        function attemptDel(){
          fetch(delCandidates[attempt],{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},body:new URLSearchParams({slug:serviceSlug,path:rel})})
            .then(async r=>{ const raw=await r.text(); if(!r.ok) throw new Error('HTTP '+r.status); let d; try{ d=JSON.parse(raw);}catch(e){ throw new Error('Bad JSON'); } return d; })
            .then(d=>{ if(d.ok){ const label=del.closest('label.hero-thumb'); if(label){ const wasSel=label.querySelector('input[type=radio]')?.checked; label.remove(); if(wasSel){ const none=serviceThumbs.querySelector('.hero-thumb.none-option input[type=radio]'); if(none){ none.checked=true; syncServiceSel(); } } } } else { alert('Delete failed: '+(d.error||'Unknown')); del.innerHTML=orig; del.classList.remove('disabled','pe-none'); } })
            .catch(err=>{ if(attempt<delCandidates.length-1){ attempt++; attemptDel(); } else { alert('Delete error: '+(err && err.message? err.message:'Network')); del.innerHTML=orig; del.classList.remove('disabled','pe-none'); } });
        }
        attemptDel();
      });
      if(serviceUploadBtn){
        serviceUploadBtn.addEventListener('click',()=>{
          if(!serviceFileInput.files || !serviceFileInput.files[0]){ setServiceStatus('Choose a file first','err'); return; }
          const f=serviceFileInput.files[0]; if(f.size>2*1024*1024){ setServiceStatus('File too large (>2MB)','err'); return; }
          if(!/\.(jpe?g|png|webp)$/i.test(f.name)){ setServiceStatus('Invalid format','err'); return; }
          setServiceStatus('Uploading...');
          const fd=new FormData(); fd.append('slug',serviceSlug); fd.append('image',f);
          const uploadCandidates=['ajax_page_image_upload.php','../ajax_page_image_upload.php']; let upAttempt=0;
          function attemptUp(){
            fetch(uploadCandidates[upAttempt],{method:'POST',body:fd,headers:{'X-Requested-With':'XMLHttpRequest'}})
              .then(async r=>{ const raw=await r.text(); if(!r.ok) throw new Error('HTTP '+r.status); let d; try{ d=JSON.parse(raw);}catch(e){ throw new Error('Bad JSON'); } return d; })
              .then(d=>{ if(d.ok){ setServiceStatus('Uploaded','ok'); const rel=d.path; const src='../../'+rel; const label=document.createElement('label'); label.className='hero-thumb selected'; label.setAttribute('data-file',rel); label.innerHTML='<input type="radio" name="' + serviceSlug + '_image_choice" value="'+rel+'" checked><img src="'+src+'?v='+Date.now()+'" alt="' + serviceSlug + ' image" style="width:100%;height:80px;object-fit:cover;border-radius:6px;display:block;margin-bottom:4px;"><span class="meta"><a href="#" class="' + serviceSlug + '-img-del-link text-danger text-decoration-none small" data-file="'+rel+'" style="white-space:nowrap;"><i class="bi bi-trash"></i> delete</a></span>'; const none=serviceThumbs.querySelector('.hero-thumb.none-option'); if(none && none.nextSibling){ serviceThumbs.insertBefore(label, none.nextSibling); } else { serviceThumbs.appendChild(label); } serviceThumbs.querySelectorAll('label.hero-thumb').forEach(l=>{ if(l!==label) l.classList.remove('selected'); }); serviceFileInput.value=''; } else { setServiceStatus(d.error||'Upload error','err'); } })
              .catch(err=>{ if(upAttempt<uploadCandidates.length-1){ upAttempt++; attemptUp(); } else { setServiceStatus(err && err.message? err.message:'Network error','err'); } });
          }
          attemptUp();
        });
      }
      syncServiceSel();
    });
  })();

  var h = location.hash.replace('#','');
  if(h){ var btn = document.getElementById(h+'-tab-btn') || document.getElementById(h+'-tab'); if(btn && btn.tagName==='BUTTON'){ btn.click(); } }
  // Summernote init (ensure jQuery + plugin available globally via main template)
  if(window.jQuery && jQuery().summernote){
    jQuery('.summernote').summernote({
      height: 220,
      toolbar: [
        ['style',['bold','italic','underline','clear']],
        ['para',['ul','ol','paragraph']],
        ['insert',['link']],
        ['view',['codeview']]
      ]
    });
  }
  // Service card handlers (save/delete)
  function attachServiceHandlers(scope){
    (scope||document).querySelectorAll('.service-save-btn').forEach(btn=>{
      if(btn.dataset.bound) return; btn.dataset.bound='1';
      btn.addEventListener('click',function(){
        const card = btn.closest('.service-card');
        if(!card) return;
        const id = card.getAttribute('data-service-id');
  if(!/^-?[0-9]+$/.test(id) || parseInt(id,10) === 0){
          const statusEl = card.querySelector('.service-save-status');
          if(statusEl){ statusEl.textContent='Invalid ID'; statusEl.classList.add('text-danger'); }
          return;
        }
        // For Summernote, ensure textarea value is sync'd
        if(window.jQuery && jQuery.fn.summernote){
          jQuery(card).find('.summernote').each(function(){
            const $el = jQuery(this);
            if($el.next('.note-editor').length){ $el.val($el.summernote('code')); }
          });
        }
        const title = card.querySelector('.service-title')?.value || '';
        const icon  = card.querySelector('.service-icon')?.value || '';
        const body  = card.querySelector('.service-body')?.value || '';
        const statusEl = card.querySelector('.service-save-status');
        if(statusEl){ statusEl.textContent='Saving...'; statusEl.classList.remove('text-success','text-danger'); }
        fetch(AJAX_BASE+'ajax_service_update.php',{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},body:new URLSearchParams({id,title,icon,body})})
          .then(async r=>{ const raw= await r.text(); if(!r.ok){ throw new Error('HTTP '+r.status+' '+raw.slice(0,60)); } let data; try{ data=JSON.parse(raw);}catch(e){ throw new Error('Non-JSON response: '+raw.slice(0,80)); } return data; })
          .then(j=>{ if(j.ok){ if(statusEl){ statusEl.textContent='Saved'; statusEl.classList.add('text-success'); }} else { if(statusEl){ statusEl.textContent=j.error||'Save error'; statusEl.classList.add('text-danger'); }} })
          .catch(err=>{ if(statusEl){ statusEl.textContent= (err && err.message ? err.message : 'Network error'); statusEl.classList.add('text-danger'); } console.warn('Service update error',err); });
      });
    });
  }
  attachServiceHandlers();
  
  // Service Cancel button handlers
  document.addEventListener('click',function(e){
    const btn = e.target.closest('.service-cancel-btn');
    if(!btn) return;
    const card = btn.closest('.service-card');
    if(!card) return;
    // Reset form to original values (could be enhanced to track original state)
    const statusEl = card.querySelector('.service-save-status');
    if(statusEl){ statusEl.textContent=''; statusEl.className='service-save-status small text-muted ms-2'; }
    // Basic refresh - in production might want to store original values
    if(confirm('Reset changes to this service?')){
      location.reload();
    }
  });
  
  // Services Intro save
  const introSaveBtn = document.getElementById('servicesIntroSaveBtn');
  if(introSaveBtn){
    introSaveBtn.addEventListener('click',function(){
      if(window.jQuery && jQuery.fn.summernote){
        jQuery('[name="services_section_paragraph"]').each(function(){
          const $el=jQuery(this); if($el.next('.note-editor').length){ $el.val($el.summernote('code')); }
        });
      }
      const titleInput = document.querySelector('input[name="services_section_title"]');
      const paraInput = document.querySelector('textarea[name="services_section_paragraph"]');
      const status = document.querySelector('.service-intro-status');
      const title = titleInput? titleInput.value.trim(): '';
      const paragraph = paraInput? paraInput.value.trim(): '';
      if(status){ status.textContent='Saving...'; status.classList.remove('text-success','text-danger'); }
      fetch(AJAX_BASE+'ajax_services_intro_update.php',{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},body:new URLSearchParams({title,paragraph})})
        .then(async r=>{ const raw= await r.text(); if(!r.ok){ throw new Error('HTTP '+r.status+' '+raw.slice(0,60)); } let data; try{ data=JSON.parse(raw);}catch(e){ throw new Error('Non-JSON response: '+raw.slice(0,80)); } return data; })
        .then(j=>{ if(j.ok){ if(status){ status.textContent='Saved'; status.classList.add('text-success'); }} else { if(status){ status.textContent=j.error||'Save error'; status.classList.add('text-danger'); }} })
        .catch(err=>{ if(status){ status.textContent= (err && err.message? err.message : 'Network error'); status.classList.add('text-danger'); } console.warn('Services intro save error',err); });
    });
  }
  // Hero Cancel button: reset fields to loaded values & restore selected state
  const heroForm = document.getElementById('heroSettingsForm');
  const heroCancel = document.getElementById('heroCancelBtn');
  function syncHeroThumbSelection(){
    const radios = heroForm ? heroForm.querySelectorAll('input[name="home[hero_image]"]') : [];
    radios.forEach(r=>{ const lab = r.closest('.hero-thumb'); if(lab){ lab.classList.toggle('selected', r.checked); }});
  }
  if(heroCancel && heroForm){
    heroCancel.addEventListener('click', (e)=>{
      e.preventDefault();
      heroForm.reset();
      // Delay to allow native reset to propagate then sync selection
      setTimeout(syncHeroThumbSelection,10);
    });
    heroForm.addEventListener('change', (e)=>{
      if(e.target && e.target.name==='home[hero_image]'){ syncHeroThumbSelection(); }
    });
  }
  syncHeroThumbSelection();
  // Hero image delete (delegated)
  document.getElementById('heroImageGrid')?.addEventListener('click', function(e){
    const link = e.target.closest('.hero-img-del-link');
    if(!link) return;
    e.preventDefault();
    const file = link.getAttribute('data-file');
    if(!file) return;
    if(!confirm('Delete this hero image file? This cannot be undone.')) return;
    const label = link.closest('label.hero-thumb');
    const origHTML = link.innerHTML;
    link.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-label="Deleting"></span>';
    link.classList.add('disabled','pe-none');
    const delUrl = (typeof AJAX_BASE!=='undefined'? AJAX_BASE : '../') + 'ajax_hero_image_delete.php';
    fetch(delUrl,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},body:new URLSearchParams({file})})
      .then(async r=>{ const raw = await r.text(); if(!r.ok){ throw new Error(raw||('HTTP '+r.status)); } let d; try{ d=JSON.parse(raw);}catch(e){ throw new Error('Bad JSON'); } return d; })
      .then(d=>{
        if(d.ok){
          if(label){ label.remove(); }
          // If deleted image was selected, set No Bg Image radio
          const selected = heroForm?.querySelector('input[name="home[hero_image]"]:checked');
          if(!selected){ const noneRadio = heroForm?.querySelector('.hero-thumb.none-option input[type="radio"]'); if(noneRadio){ noneRadio.checked=true; syncHeroThumbSelection(); } }
        } else { alert('Delete failed: '+ (d.error||'Unknown')); link.innerHTML=origHTML; link.classList.remove('disabled','pe-none'); }
      })
      .catch(err=>{ console.warn('Hero image delete network/error', err); alert('Delete error: '+(err && err.message? err.message : 'Network failure')); link.innerHTML=origHTML; link.classList.remove('disabled','pe-none'); });
  });
  // ---- Pages per-page AJAX save ----
  function syncPageSummernote(scope){
    if(window.jQuery && jQuery.fn.summernote){
      jQuery(scope || document).find('.summernote').each(function(){
        const $el=jQuery(this); if($el.next('.note-editor').length){ $el.val($el.summernote('code')); }
      });
    }
  }
  document.querySelectorAll('.page-save-btn').forEach(btn=>{
    if(btn.dataset.bound) return; btn.dataset.bound='1';
    btn.addEventListener('click', function(){
      const slug = btn.getAttribute('data-page-slug'); if(!slug) return;
      const body = btn.closest('.accordion-body') || document;
      syncPageSummernote(body);
      const statusEl = body.querySelector('.page-save-status[data-page-slug="'+slug+'"]');
      if(statusEl){ statusEl.textContent='Saving...'; statusEl.classList.remove('text-success','text-danger'); }
      const metaTitle = body.querySelector('[name="pages['+slug+'_meta_title]"]')?.value || '';
      const metaDesc  = body.querySelector('[name="pages['+slug+'_meta_description]"]')?.value || '';
      if(metaTitle.length>70){ if(statusEl){ statusEl.textContent='Meta title >70'; statusEl.classList.add('text-danger'); } return; }
      if(metaDesc.length>160){ if(statusEl){ statusEl.textContent='Meta desc >160'; statusEl.classList.add('text-danger'); } return; }
      const params = new URLSearchParams();
      params.append('slug', slug);
      params.append('meta_title', metaTitle);
      params.append('meta_description', metaDesc);
      const contentTA = body.querySelector('[name="pages['+slug+'_content]"]');
      const contentVal = contentTA? contentTA.value : '';
      if(slug==='about'){
        const heading = body.querySelector('[name="pages[about_heading]"]')?.value || '';
        if(!heading.trim()){ if(statusEl){ statusEl.textContent='Heading required'; statusEl.classList.add('text-danger'); } return; }
        const intro = body.querySelector('[name="pages[about_intro]"]')?.value || '';
        params.append('part_heading', heading);
        params.append('part_intro', intro);
  const imgRadio = body.querySelector('input[name="about_image_choice"]:checked');
  const imgPath = imgRadio ? imgRadio.value : '';
  if(imgPath){ params.append('part_image', imgPath); }
        params.append('part_body', contentVal);
      } else {
        params.append('part_body', contentVal);
      }
  // ajax_page_update.php lives in the same settings directory (NOT parent admin), so no AJAX_BASE prefix
  // Build flexible endpoint list (handles running from proxy/dev server where relative ../ may break)
  const candidates = [];
  (function(){
        const loc = window.location;
        const path = loc.pathname; // e.g., /gws-universal-hybrid-app/public_html/admin/settings/content_settings_tabs_temp.php
        // Derive project root up to /public_html if present
        const pubIdx = path.indexOf('/public_html/');
        let rootBase = '';
        if(pubIdx !== -1){ rootBase = path.substring(0, pubIdx + '/public_html'.length); } else {
          // fallback: strip everything after /admin/
          const admIdx = path.indexOf('/admin/');
          rootBase = admIdx !== -1 ? path.substring(0, admIdx + '/public_html'.length) : '';
        }
        const add = (u)=>{ if(!candidates.includes(u)) candidates.push(u); };
        if(typeof AJAX_BASE !== 'undefined'){ add(AJAX_BASE+'ajax_page_update.php'); }
        // Root-relative variants
        add('/admin/ajax_page_update.php');
        if(rootBase){ add(rootBase + '/admin/ajax_page_update.php'); }
        // Settings-local + relative parent
        add('ajax_page_update.php');
        add('../ajax_page_update.php');
        // Prefer last known good endpoint if stored
        try {
          const prev = localStorage.getItem('PAGE_SAVE_ENDPOINT');
          if(prev && !candidates.includes(prev)) { candidates.unshift(prev); }
        } catch(e){}
  })();
  // Cancel resets visible inputs back to current DOM-loaded values (simple revert)
  document.querySelectorAll('.page-cancel-btn').forEach(btn=>{
    if(btn.dataset.bound) return; btn.dataset.bound='1';
    btn.addEventListener('click', function(){
      const slug = btn.getAttribute('data-page-slug');
      const body = btn.closest('.accordion-body') || document;
      // Reset text inputs/areas
      body.querySelectorAll('input[type="text"], textarea').forEach(el=>{ el.value = el.defaultValue; });
      // Reset radios in about gallery
      const aboutNone = body.querySelector('.hero-thumb.none-option input[name="about_image_choice"]');
      if(aboutNone){ aboutNone.checked = aboutNone.defaultChecked || aboutNone.checked; }
      // Re-sync UI selections
      body.querySelectorAll('label.hero-thumb').forEach(l=>{ const r=l.querySelector('input[type=radio]'); if(r){ l.classList.toggle('selected', r.checked); } });
      const statusEl = body.querySelector('.page-save-status[data-page-slug="'+slug+'"]'); if(statusEl){ statusEl.textContent='Changes reverted'; statusEl.classList.remove('text-danger'); statusEl.classList.add('text-muted'); }
    });
  });
      let attempt = 0;
      function tryFetch(){
        const url = candidates[attempt];
  const debugParam = window.PAGE_SAVE_DEBUG ? (url.indexOf('?')>-1?'&':'?')+'debug=1' : '';
  if(window.PAGE_SAVE_DEBUG){ console.log('[PageSave] Attempt', attempt+1, 'URL:', url, 'Slug:', slug); }
  fetch(url+debugParam,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},body:params})
          .then(async r=>{ const raw=await r.text(); if(!r.ok){ throw new Error('HTTP '+r.status+' '+raw.slice(0,140)); } let d; try{ d=JSON.parse(raw);}catch(e){ throw new Error('Bad JSON '+raw.slice(0,140)); } return d; })
          .then(j=>{ 
            if(j.ok){ 
              try { localStorage.setItem('PAGE_SAVE_ENDPOINT', url); } catch(e){}
              if(statusEl){ statusEl.textContent='Saved'; statusEl.classList.add('text-success'); }
              if(window.PAGE_SAVE_DEBUG){ console.log('[PageSave] Success for', slug, 'using', url, j); }
            } else { 
              if(statusEl){ statusEl.textContent=j.error||'Error'; statusEl.classList.add('text-danger'); }
              if(window.PAGE_SAVE_DEBUG){ console.warn('[PageSave] JSON error response', j); }
            } 
          })
          .catch(err=>{
            if(attempt < candidates.length-1){ attempt++; tryFetch(); return; }
            if(statusEl){
              statusEl.textContent = (err && err.message? err.message : 'Network error');
              statusEl.classList.add('text-danger');
              const diag = document.createElement('div');
              diag.className='mt-1 small text-muted';
              diag.textContent = 'Tried: '+candidates.join(' | ');
              statusEl.appendChild(diag);
            }
            console.warn('Page save error for slug', slug, err, 'Tried URLs:', candidates);
          });
      }
      tryFetch();
    });
  });
});
</script>
<?= template_admin_footer() ?>
