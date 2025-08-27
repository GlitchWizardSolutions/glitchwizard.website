<?php
/*
PAGE NAME  : profile.php
LOCATION   : public_html/profile.php
DESCRIPTION: This page allows users to view and edit their profile settings.
FUNCTION   : Users can update their email, username, and avatar. Password changes are also handled.
CHANGE LOG : Initial creation of profile.php for user profile management.
2025-08-24 : Added avatar upload functionality.
2025-08-25 : Improved comment system with user avatars.
2025-08-26 : Enhanced SEO features for blog posts.
*/

include_once "assets/includes/doctype.php";
include_once "assets/includes/header.php";
include_once "assets/includes/blog_load.php";
 
global $pdo;
if (!isset($rowusers) || !$rowusers || !isset($rowusers['role']) || $rowusers['role'] !== 'Blog_User') {
    echo '<script>window.location.href="blog.php";</script>';
    exit;
}
$username = $rowusers['username'];
$email = $rowusers['email'];
$avatar = $rowusers['avatar'];
$account_id = $rowusers['id'];

if (isset($_POST['save'])) {
    $new_email = trim($_POST['email']);
    $new_username = trim($_POST['username']);
    $new_avatar = $avatar;
    $new_password = $_POST['password'];
    $emused = 'No';

    // Check for duplicate email in accounts table
    $stmt = $pdo->prepare("SELECT COUNT(id) FROM accounts WHERE email = ? AND id != ? LIMIT 1");
    $stmt->execute([$new_email, $account_id]);
    $countue = $stmt->fetchColumn();
    if ($countue > 0) {
        $emused = 'Yes';
        echo '<div class="alert alert-warning">This email address is already used by another account.</div>';
    }

    // Handle avatar upload
    if (!empty($_FILES['avafile']['name'])) {
        $target_dir = "accounts_system/assets/uploads/avatars/";
        $imageFileType = strtolower(pathinfo($_FILES["avafile"]["name"], PATHINFO_EXTENSION));
        $filename = $username . '-' . $account_id . '.' . $imageFileType;
        $target_file = $target_dir . $filename;
        $uploadOk = 1;
        $check = getimagesize($_FILES["avafile"]["tmp_name"]);
        if ($check === false) {
            echo '<div class="alert alert-warning">File is not an image.</div>';
            $uploadOk = 0;
        }
        if ($_FILES["avafile"]["size"] > 1000000) {
            echo '<div class="alert alert-warning">Sorry, your file is too large.</div>';
            $uploadOk = 0;
        }
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["avafile"]["tmp_name"], $target_file)) {
                $new_avatar = $target_file;
            } else {
                echo '<div class="alert alert-warning">Failed to upload avatar.</div>';
            }
        }
    }

    // Update account info
    if (filter_var($new_email, FILTER_VALIDATE_EMAIL) && $emused == 'No') {
        if (!empty($new_password)) {
            $hashed_password = hash('sha256', $new_password);
            $stmt = $pdo->prepare("UPDATE accounts SET email = ?, username = ?, avatar = ?, password = ? WHERE id = ?");
            $stmt->execute([$new_email, $new_username, $new_avatar, $hashed_password, $account_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE accounts SET email = ?, username = ?, avatar = ? WHERE id = ?");
            $stmt->execute([$new_email, $new_username, $new_avatar, $account_id]);
        }
        // Newsletter subscription logic
        $newsletter_sub = isset($_POST['newsletter_sub']) ? 1 : 0;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_newsletter WHERE email = ?");
        $stmt->execute([$new_email]);
        $exists = $stmt->fetchColumn();
        if ($newsletter_sub) {
            if ($exists) {
                $stmt = $pdo->prepare("UPDATE blog_newsletter SET date = NOW() WHERE email = ?");
                $stmt->execute([$new_email]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO blog_newsletter (email, date) VALUES (?, NOW())");
                $stmt->execute([$new_email]);
            }
        } else {
            if ($exists) {
                $stmt = $pdo->prepare("DELETE FROM blog_newsletter WHERE email = ?");
                $stmt->execute([$new_email]);
            }
        }
        echo '<meta http-equiv="refresh" content="0;url=profile.php">';
        exit;
    }
}
?>
 
<div class="container">
    <div class="row">
        <div class="col-md-8 mb-3">
            <div class="card">
                <div class="card-header accent-background">Edit Your Profile settings</div>
                <div class="card-body">
                  <form method="post" action="" enctype="multipart/form-data" aria-labelledby="profileFormTitle">
                    <div class="row justify-content-center">
                      <div class="col-12 col-md-10">
                        <div class="mb-3 row align-items-center">
                          <label for="username" class="col-sm-3 col-form-label text-end">Username</label>
                          <div class="col-sm-9">
                            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" class="form-control" required aria-required="true" aria-label="Username" />
                          </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                          <label for="email" class="col-sm-3 col-form-label text-end">Email</label>
                          <div class="col-sm-6">
                            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control" required aria-required="true" aria-label="E-Mail Address" />
                          </div>
                          <div class="col-sm-3 d-flex align-items-center">
                            <?php
                            $is_subscribed = false;
                            if (!empty($email)) {
                              $stmt = $pdo->prepare('SELECT COUNT(*) FROM blog_newsletter WHERE email = ?');
                              $stmt->execute([$email]);
                              $is_subscribed = $stmt->fetchColumn() ? true : false;
                            }
                            ?>
                            <input class="form-check-input newsletter-dark-checkbox-md<?php echo !$is_subscribed ? ' newsletter-grey-checkbox' : ''; ?>" type="checkbox" name="newsletter_sub" id="newsletter_sub" value="1" aria-checked="<?php echo $is_subscribed ? 'true' : 'false'; ?>" <?php if ($is_subscribed) echo 'checked'; ?> />
                            <label class="form-check-label ms-2" for="newsletter_sub">Subscribe to Newsletter</label>
                          </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                          <label for="avatarfile" class="col-sm-3 col-form-label text-end">Avatar</label>
                          <div class="col-sm-9 d-flex align-items-center">
                            <input type="file" class="form-control flex-grow-1" name="avafile" accept="image/*" id="avatarfile" aria-label="Upload new avatar" placeholder="Choose file..." />
                            <img src="accounts_system/assets/uploads/avatars/<?php echo htmlspecialchars($avatar); ?>" width="48" height="48" alt="User avatar" style="border-radius:50%;border:2px solid #593196; object-fit:cover; margin-left:16px; margin-right:0;" />
                          </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                          <label for="password" class="col-sm-3 col-form-label text-end">Password</label>
                          <div class="col-sm-9">
                            <input type="password" name="password" id="password" value="" class="form-control" aria-label="Password" />
                            <small class="form-text text-muted" id="passwordHelp">Fill this field only if you want to change your password.</small>
                          </div>
                        </div>
                        <div class="mb-3 row">
                          <div class="offset-sm-3 col-sm-9">
                            <input type="submit" name="save" class="btn w-100" value="Update" aria-label="Update profile" style="background: var(--brand-secondary, #e94f37); color: #fff; border: none; font-weight:600; font-size:1.1rem;" />
                          </div>
                        </div>
                      </div>
                    </div>
                    <style>
                    .newsletter-dark-checkbox-md {
                      width: 1.7em;
                      height: 1.7em;
                      border: 2px solid #222 !important;
                      background-color: #fff !important;
                      accent-color: #fff;
                    }
                    .newsletter-dark-checkbox-md:checked {
                      background-color: #fff !important;
                      border-color: #222 !important;
                      accent-color: #222;
                    }
                    </style>
                  </form>
                </div>
            </div>
        </div>
        <?php if ($settings['sidebar_position'] == 'Right') { ?>
        <div class="col-md-4 mb-3"><?php sidebar(); ?></div>
        <?php } ?>
    </div>
</div>
<?php
// Use public footer for unified branding 
include 'assets/includes/footer.php';
?>