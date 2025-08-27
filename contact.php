<?php
// Unified includes and layout
include_once 'assets/includes/doctype.php';
include_once 'assets/includes/header.php';
include_once 'assets/includes/blog_load.php';
?>
<div class="container<?php echo ($settings['layout'] == 'Wide') ? '-fluid' : ''; ?> mt-3 mb-5">
    <div class="row">
        <?php if ($settings['sidebar_position'] == 'Left') { echo '<div class="col-md-4 order-1 order-md-1 mb-3">'; sidebar(); echo '</div>'; } ?>
        <div class="col-md-8 order-2 order-md-2 mb-3">
            <div class="card">
                <div class="card-header"><i class="bi bi-envelope-fill" aria-hidden="true"></i> Contact</div>
                <div class="card-body">
                    <h5 class="mb-3">Social Profiles</h5>
                    <div class="list-group">
                        <a class="list-group-item list-group-item-action" href="mailto:<?php echo htmlspecialchars($settings['email']); ?>" target="_blank"><i class="bi bi-envelope" aria-hidden="true"></i><span>&nbsp; E-Mail: <strong><?php echo htmlspecialchars($settings['email']); ?></span></strong></a>
                        <?php if (!empty($settings['facebook'])) { ?>
                            <a class="list-group-item list-group-item-primary list-group-item-action" href="<?php echo htmlspecialchars($settings['facebook']); ?>" target="_blank"><strong><i class="bi bi-facebook" aria-hidden="true"></i>&nbsp; Facebook</strong></a>
                        <?php } ?>
                        <?php if (!empty($settings['instagram'])) { ?>
                            <a class="list-group-item list-group-item-warning list-group-item-action" href="<?php

                        echo htmlspecialchars($settings['instagram']); ?>" target="_blank"><strong><i class="bi bi-instagram" aria-hidden="true"></i>&nbsp; Instagram</strong></a>
                        <?php } ?>
                        <?php if (!empty($settings['twitter'])) { ?>
                            <a class="list-group-item list-group-item-info list-group-item-action" href="<?php echo htmlspecialchars($settings['twitter']); ?>" target="_blank"><strong><i class="bi bi-twitter" aria-hidden="true"></i>&nbsp; Twitter</strong></a>
                        <?php } ?>
                        <?php if (!empty($settings['youtube'])) { ?>
                            <a class="list-group-item list-group-item-danger list-group-item-action" href="<?php echo htmlspecialchars($settings['youtube']); ?>" target="_blank"><strong><i class="bi bi-youtube" aria-hidden="true"></i>&nbsp; YouTube</strong></a>
                        <?php } ?>
                        <?php if (!empty($settings['linkedin'])) { ?>
                            <a class="list-group-item list-group-item-primary list-group-item-action" href="<?php echo htmlspecialchars($settings['linkedin']); ?>" target="_blank"><strong><i class="bi bi-linkedin" aria-hidden="true"></i>&nbsp; LinkedIn</strong></a>
                        <?php } ?>
                    </div>
                    <h5 class="mt-4 mb-2">Leave Your Message</h5>
                    <?php
                    // Contact form logic using PDO
                    if (isset($_POST['send'])) {
                        $name = '';
                        $email = '';
                        if ($logged == 'No') {
                            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
                            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                        } else if (isset($rowusers)) {
                            $name = $rowusers['username'];
                            $email = $rowusers['email'];
                        }
                        $content = isset($_POST['text']) ? trim($_POST['text']) : '';
                        $date = date('Y-m-d');
                        $time = date('H:i:s');
                        $captcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
                        $valid = true;
                        if ($captcha) {
                            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($settings['gcaptcha_secretkey']) . '&response=' . urlencode($captcha);
                            $response = file_get_contents($url);
                            $responseKeys = json_decode($response, true);
                            if (!$responseKeys["success"]) {
                                echo '<div class="alert alert-danger">Captcha validation failed.</div>';
                                $valid = false;
                            }
                        } else {
                            echo '<div class="alert alert-danger">Captcha is required.</div>';
                            $valid = false;
                        }
                        if ($valid) {
                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                echo '<div class="alert alert-danger">The entered E-Mail Address is invalid.</div>';
                            } else {
                                try {
                                    $stmt = $pdo->prepare("INSERT INTO messages (name, email, content, date, time) VALUES (?, ?, ?, ?, ?)");
                                    $stmt->execute([$name, $email, $content, $date, $time]);
                                    echo '<div class="alert alert-success">Your message has been sent successfully.</div>';
                                } catch (Exception $e) {
                                    echo '<div class="alert alert-danger">Error sending message. Please try again later.</div>';
                                }
                            }
                        }
                    }
                    ?>
                    <form method="post" action="">
                        <?php if ($logged == 'No') { ?>
                            <label for="name"><i class="bi bi-person" aria-hidden="true"></i> Name:</label>
                            <input type="text" name="name" id="name" value="" class="form-control" required />
                            <br />
                            <label for="email"><i class="bi bi-envelope" aria-hidden="true"></i> E-Mail Address:</label>
                            <input type="email" name="email" id="email" value="" class="form-control" required />
                            <br />
                        <?php } ?>
                        <label for="input-message"><i class="bi bi-file-text" aria-hidden="true"></i> Message:</label>
                        <textarea name="text" id="input-message" rows="8" class="form-control" required></textarea>
                        <br /><center><div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($settings['gcaptcha_sitekey']); ?>"></div></center>
                        <input type="submit" name="send" class="btn btn-primary col-12" value="Send" />
                    </form>
                </div>
            </div>
        </div>
        <?php if ($settings['sidebar_position'] == 'Right') { echo '<div class="col-md-4 order-3 order-md-3 mb-3">'; sidebar(); echo '</div>'; } ?>
    </div>
</div>
<?php include_once 'assets/includes/footer.php'; ?>