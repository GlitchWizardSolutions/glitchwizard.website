<?php
/* 
 * Blog Settings Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: blog_settings.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Manage blog system settings and configuration
 * DETAILED DESCRIPTION:
 * This file provides a comprehensive interface for managing blog settings,
 * including appearance, functionality, content display, comments, and other
 * blog-specific configurations. It offers a centralized location for all
 * blog system customizations and preferences.
 * REQUIRED FILES: 
 * - /public_html/assets/includes/main.php
 * - /public_html/assets/includes/settings/blog_config.php
 * - /public_html/assets/includes/settings/blog_appearance.php
 * CREATED: 2025-08-07
 * UPDATED: 2025-08-07
 * VERSION: 1.0
 * PRODUCTION: [IN DEVELOPMENT]
 * 
 * FEATURES:
 * - Blog appearance settings
 * - Content display options
 * - Comment configuration
 * - Social media integration
*/
include "header.php";

// Only allow updating blog_site_url if user is a developer
$is_developer = (isset($_SESSION['role']) && $_SESSION['role'] === 'developer');

// Check if upload directory is writable and warn if not
$upload_dir_check = "../../blog_system/assets/settings/background_image/";
$message = (!is_writable($upload_dir_check)) ? '<div class="alert alert-danger">Warning: The background image upload directory (<code>' . htmlspecialchars($upload_dir_check) . '</code>) is not writable. Please check folder permissions.</div>' : '';

// List existing background images (store just the filename)
$existing_images = [];
$img_dir = $upload_dir_check;
foreach (glob($img_dir . "*.{jpg,jpeg,png,webp,svg}", GLOB_BRACE) as $img)
{
	$existing_images[] = basename($img);
}

if (isset($_GET['delete_bgrimg']))
{
	if (!empty($settings['background_image']))
	{
		@unlink($img_dir . $settings['background_image']);
	}
	$settings['background_image'] = '';
	file_put_contents('../../blog_system/assets/settings/blog_settings.php', '<?php $settings = ' . var_export($settings, true) . '; ?>');
	echo '<meta http-equiv="refresh" content="0;url=settings.php">';
	exit;
}

if (isset($_POST['save']))
{
	// Feedback message
	$form_message = '';

	// Use selected existing image if chosen
	if (!empty($_POST['existing_background_image']))
	{
		$image = $_POST['existing_background_image'];
		$form_message .= '<div class="alert alert-success">Background image selected successfully.</div>';
	}
	// Or handle upload
	else if (@$_FILES['background_image']['name'] != '')
	{
		// Set correct upload and display paths
		$target_dir = $img_dir;

		// Get original filename and extension
		$original_name = pathinfo($_FILES["background_image"]["name"], PATHINFO_FILENAME);
		$imageFileType = strtolower(pathinfo($_FILES["background_image"]["name"], PATHINFO_EXTENSION));

		// Only allow widely supported and responsive image types
		$allowed_types = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
		if (!in_array($imageFileType, $allowed_types))
		{
			$form_message .= '<div class="alert alert-danger">Only JPG, JPEG, PNG, WEBP, and SVG files are allowed for background images.</div>';
			$image = $settings['background_image'];
		} else
		{
			// Create base filename with date
			$date = date('Y-m-d');
			$base_filename = $original_name . '-' . $date;
			$filename = $base_filename . '.' . $imageFileType;
			$location = $target_dir . $filename;

			// If file exists, add (1), (2), etc.
			$counter = 1;
			while (file_exists($location))
			{
				$filename = $base_filename . "($counter)." . $imageFileType;
				$location = $target_dir . $filename;
				$counter++;
			}

			$uploadOk = 1;

			// Check if image file is a actual image or fake image (skip for SVG)
			if ($imageFileType !== 'svg')
			{
				$check = getimagesize($_FILES["background_image"]["tmp_name"]);
				if ($check !== false)
				{
					$uploadOk = 1;
				} else
				{
					$form_message .= '<div class="alert alert-danger">The file is not a valid image.</div>';
					$uploadOk = 0;
				}
			}

			// Check file size (limit 2MB, skip for SVG)
			if ($imageFileType !== 'svg' && $_FILES["background_image"]["size"] > 2000000)
			{
				$form_message .= '<div class="alert alert-warning">Sorry, the image file size is too large. Limit: 2 MB.</div>';
				$uploadOk = 0;
			}

			if ($uploadOk == 1)
			{
				if (move_uploaded_file($_FILES["background_image"]["tmp_name"], $location))
				{
					$image = $filename;
					$form_message .= '<div class="alert alert-success">Background image uploaded successfully.</div>';
				} else
				{
					$form_message .= '<div class="alert alert-danger">Failed to upload the image. Please check folder permissions.</div>';
					$image = $settings['background_image'];
				}
			} else
			{
				$image = $settings['background_image'];
			}
		}
	} else
	{
		$image = $settings['background_image'];
	}

	// Only update blog_site_url if developer
	if ($is_developer && isset($_POST['blog_site_url']))
	{
		$blog_site_url = trim($_POST['blog_site_url']);
		if (filter_var($blog_site_url, FILTER_VALIDATE_URL))
		{
			$settings['blog_site_url'] = $blog_site_url;
		} else
		{
			$form_message .= '<div class="alert alert-danger">Invalid Blog Site URL. Please enter a valid URL (including http:// or https://).</div>';
		}
	}

	$settings['sitename'] = addslashes($_POST['sitename']);
	$settings['description'] = addslashes($_POST['description']);

	// Email validation
	$email = trim($_POST['email']);
	if (filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		$settings['email'] = $email;
	} else
	{
		$form_message .= '<div class="alert alert-danger">Invalid email address. Please enter a valid email.</div>';
	}

	// Social URLs validation (optional: only validate if not empty)
	$settings['facebook'] = filter_var(trim($_POST['facebook']), FILTER_VALIDATE_URL) || trim($_POST['facebook']) === '' ? trim($_POST['facebook']) : '';
	$settings['instagram'] = filter_var(trim($_POST['instagram']), FILTER_VALIDATE_URL) || trim($_POST['instagram']) === '' ? trim($_POST['instagram']) : '';
	$settings['x'] = filter_var(trim($_POST['twitter']), FILTER_VALIDATE_URL) || trim($_POST['twitter']) === '' ? trim($_POST['twitter']) : '';
	$settings['youtube'] = filter_var(trim($_POST['youtube']), FILTER_VALIDATE_URL) || trim($_POST['youtube']) === '' ? trim($_POST['youtube']) : '';
	$settings['linkedin'] = filter_var(trim($_POST['linkedin']), FILTER_VALIDATE_URL) || trim($_POST['linkedin']) === '' ? trim($_POST['linkedin']) : '';

	$settings['gcaptcha_sitekey'] = addslashes($_POST['gcaptcha-sitekey']);
	$settings['gcaptcha_secretkey'] = addslashes($_POST['gcaptcha-secretkey']);
	$settings['head_customcode'] = base64_encode($_POST['head-customcode']);
	$settings['comments'] = addslashes($_POST['comments']);
	$settings['rtl'] = addslashes($_POST['rtl']);
	$settings['date_format'] = addslashes($_POST['date_format']);
	$settings['layout'] = addslashes($_POST['layout']);
	$settings['latestposts_bar'] = addslashes($_POST['latestposts_bar']);
	$settings['sidebar_position'] = addslashes($_POST['sidebar_position']);
	$settings['posts_per_row'] = addslashes($_POST['posts_per_row']);
	$settings['theme'] = addslashes($_POST['theme']);
	$settings['background_image'] = $image;

	file_put_contents('../../blog_system/assets/settings/blog_settings.php', '<?php $settings = ' . var_export($settings, true) . '; ?>');
	$form_message .= '<div class="alert alert-success">Settings updated successfully.</div>';
	$message .= $form_message;
	// Remove meta refresh so user can see the message
	// echo '<meta http-equiv="refresh" content="1;url=settings.php">';
}
?>
<?= template_admin_header('Blog Settings', 'blog', 'blog_settings') ?>

<div class="content-title mb-4" id="main-blog-settings" role="banner" aria-label="Blog Settings Header">
	<div class="title">
		<div class="icon">
			<svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
				<path d="M96 96c0-35.3 28.7-64 64-64H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H80c-44.2 0-80-35.8-80-80V128c0-17.7 14.3-32 32-32s32 14.3 32 32V400c0 8.8 7.2 16 16 16s16-7.2 16-16V96zm64 24v80c0 13.3 10.7 24 24 24H296c13.3 0 24-10.7 24-24V120c0-13.3-10.7-24-24-24H184c-13.3 0-24 10.7-24 24zm208-8c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16h48c8.8 0 16-7.2 16-16s-7.2-16-16-16H384c-8.8 0-16 7.2-16 16zM160 304c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16zm0 96c0 8.8 7.2 16 16 16H432c8.8 0 16-7.2 16-16s-7.2-16-16-16H176c-8.8 0-16 7.2-16 16z"/>
			</svg>
		</div>
		<div class="txt">
			<h2>Blog Settings</h2>
			<p>Update the appearance of your blog.</p>
		</div>
	</div>
</div>

<div style="height: 20px;"></div>

<div class="d-flex gap-2 pb-3 mb-3" style="justify-content: flex-start;">
	<a href="blog_dash.php" class="btn btn-outline-secondary" aria-label="Go to Blog Dashboard">
		<i class="fas fa-arrow-left me-1"></i>Dashboard
	</a>

</div>

<div class="card">
	<h6 class="card-header">Settings</h6>
	<div class="card-body">
		<?php if (!empty($message))
			echo $message; ?>
		<form action="" method="post" enctype="multipart/form-data">
			<?php if ($is_developer): ?>
				<p>
					<label>Blog Site URL</label>
					<input class="form-control" name="blog_site_url" value="<?php
					echo htmlspecialchars($settings['blog_site_url']);
					?>" type="url" required>
				</p>
			<?php endif; ?>
			<p>
				<label>Site Name</label>
				<input class="form-control" name="sitename" value="<?php
				echo $settings['sitename'];
				?>" type="text" required>
			</p>
			<p>
				<label>Description</label>
				<textarea class="form-control" name="description" required><?php
				echo $settings['description'];
				?></textarea>
			</p>
			<p>
				<label>Website's E-Mail Address</label>
				<input class="form-control" name="email" type="email" value="<?php
				echo $settings['email'];
				?>" required>
			</p>
			<div class="row">
				<div class="col-md-6">
					<p>
						<label>reCAPTCHA v2 Site Key:</label>
						<input class="form-control" name="gcaptcha-sitekey" value="<?php
						echo $settings['gcaptcha_sitekey'];
						?>" type="text" required>
					</p>
				</div>
				<div class="col-md-6">
					<p>
						<label>reCAPTCHA v2 Secret Key:</label>
						<input class="form-control" name="gcaptcha-secretkey" value="<?php
						echo $settings['gcaptcha_secretkey'];
						?>" type="text" required>
					</p>
				</div>
			</div>
			<p>
				<label>Custom code for &lt;head&gt; tag</label>
				<textarea name="head-customcode" class="form-control" rows="4"
					placeholder="For example: Google Analytics tracking code can be placed here"><?php
					echo base64_decode($settings['head_customcode']);
					?></textarea>
			</p>
			<p>
				<label>Facebook Profile</label>
				<input class="form-control" name="facebook" type="url" value="<?php echo $settings['facebook']; ?>">
			</p>
			<p>
				<label>Instagram Profile</label>
				<input class="form-control" name="instagram" type="url" value="<?php echo $settings['instagram']; ?>">
			</p>
			<p>
				<label>Twitter Profile</label>
				<input class="form-control" name="twitter" type="url" value="<?php echo $settings['twitter']; ?>">
			</p>
			<p>
				<label>Youtube Profile</label>
				<input class="form-control" name="youtube" type="url" value="<?php echo $settings['youtube']; ?>">
			</p>
			<p>
				<label>LinkedIn Profile</label>
				<input class="form-control" name="linkedin" type="url" value="<?php echo $settings['linkedin']; ?>">
			</p>
			<div class="row">
				<div class="col-md-6">
					<p>
						<label>RTL Content (Right-To-Left)</label>
						<select name="rtl" class="form-select" required>
							<option value="No" <?php
							if ($settings['rtl'] == "No")
							{
								echo 'selected';
							}
							?>>No</option>
							<option value="Yes" <?php
							if ($settings['rtl'] == "Yes")
							{
								echo 'selected';
							}
							?>>Yes</option>
						</select>
					</p>
				</div>
				<div class="col-md-6">
					<p>
						<label>Comments Section</label>
						<select name="comments" class="form-select" required>
							<option value="guests" <?php
							if ($settings['comments'] == "guests")
							{
								echo 'selected';
							}
							?>>
								Registration not required</option>
							<option value="registered" <?php
							if ($settings['comments'] == "registered")
							{
								echo 'selected';
							}
							?>>Registration required</option>
						</select>
					</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<p>
						<label>Date Format</label><br />
						<select name="date_format" class="form-select" required>
							<option value="d.m.Y" <?php
							if ($settings['date_format'] == "d.m.Y")
							{
								echo 'selected';
							}
							?>>
								<?php echo date("d.m.Y"); ?>
							</option>
							<option value="m.d.Y" <?php
							if ($settings['date_format'] == "m.d.Y")
							{
								echo 'selected';
							}
							?>>
								<?php echo date("m.d.Y"); ?>
							</option>
							<option value="Y.m.d" <?php
							if ($settings['date_format'] == "Y.m.d")
							{
								echo 'selected';
							}
							?>>
								<?php echo date("Y.m.d"); ?>
							</option>
							<option disabled>───────────</option>
							<option value="d F Y" <?php
							if ($settings['date_format'] == "d F Y")
							{
								echo 'selected';
							}
							?>>
								<?php echo date("d F Y"); ?>
							</option>
							<option value="F j, Y" <?php
							if ($settings['date_format'] == "F j, Y")
							{
								echo 'selected';
							}
							?>><?php echo date("F j, Y"); ?></option>
							<option value="Y F j" <?php
							if ($settings['date_format'] == "Y F j")
							{
								echo 'selected';
							}
							?>>
								<?php echo date("Y F j"); ?>
							</option>
							<option disabled>───────────</option>
							<option value="d-m-Y" <?php
							if ($settings['date_format'] == "d-m-Y")
							{
								echo 'selected';
							}
							?>>
								<?php echo date("d-m-Y"); ?>
							</option>
							<option value="m-d-Y" <?php
							if ($settings['date_format'] == "m-d-Y")
							{
								echo 'selected';
							}
							?>>
								<?php echo date("m-d-Y"); ?>
							</option>
							<option value="Y-m-d" <?php
							if ($settings['date_format'] == "Y-m-d")
							{
								echo 'selected';
							}
							?>>
								<?php echo date("Y-m-d"); ?>
							</option>
							<option disabled>───────────</option>
							<option value="d/m/Y" <?php
							if ($settings['date_format'] == "d/m/Y")
							{
								echo 'selected';
							}
							?>>
								<?php echo date("d/m/Y"); ?>
							</option>
							<option value="m/d/Y" <?php
							if ($settings['date_format'] == "m/d/Y")
							{
								echo 'selected';
							}
							?>>
								<?php echo date("m/d/Y"); ?>
							</option>
							<option value="Y/m/d" <?php
							if ($settings['date_format'] == "Y/m/d")
							{
								echo 'selected';
							}
							?>>
								<?php echo date("Y/m/d"); ?>
							</option>
						</select>
					</p>
				</div>
				<div class="col-md-4">
					<p>
						<label>Layout</label>
						<select name="layout" class="form-select" required>
							<option value="Wide" <?php
							if ($settings['layout'] == "Wide")
							{
								echo 'selected';
							}
							?>>Wide
								(Full-Sized)</option>
							<option value="Boxed" <?php
							if ($settings['layout'] == "Boxed")
							{
								echo 'selected';
							}
							?>>Boxed
							</option>
						</select>
					</p>
				</div>
				<div class="col-md-4">
					<p>
						<label>Latest Posts bar</label>
						<select name="latestposts_bar" class="form-select" required>
							<option value="Enabled" <?php
							if ($settings['latestposts_bar'] == "Enabled")
							{
								echo 'selected';
							}
							?>>Enabled</option>
							<option value="Disabled" <?php
							if ($settings['latestposts_bar'] == "Disabled")
							{
								echo 'selected';
							}
							?>>Disabled</option>
						</select>
					</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<p>
						<label>Sidebar Position</label>
						<select name="sidebar_position" class="form-select" required>
							<option value="Left" <?php
							if ($settings['sidebar_position'] == "Left")
							{
								echo 'selected';
							}
							?>>Left</option>
							<option value="Right" <?php
							if ($settings['sidebar_position'] == "Right")
							{
								echo 'selected';
							}
							?>>Right</option>
						</select>
					</p>
				</div>
				<div class="col-md-4">
					<p><label>Homepage posts per row</label><br />
						<select name="posts_per_row" class="form-select" required>
							<option value="2" <?php
							if ($settings['posts_per_row'] == "2")
							{
								echo 'selected';
							}
							?>>2
							</option>
							<option value="3" <?php
							if ($settings['posts_per_row'] == "3")
							{
								echo 'selected';
							}
							?>>3
							</option>
						</select>
					</p>
				</div>
				<div class="col-md-4">
					<p>
						<label>Theme</label>
						<select class="form-select" name="theme" required>
							<?php
							$themes = array("Bootstrap 5", "Cerulean", "Cosmo", "Darkly", "Flatly", "Journal", "Litera", "Lumen", "Lux", "Materia", "Minty", "Morph", "Pulse", "Sandstone", "Simplex", "Sketchy", "Slate", "Solar", "Spacelab", "Superhero", "United", "Vapor", "Yeti", "Zephyr");
							foreach ($themes as $design)
							{
								if ($settings['theme'] == $design)
								{
									$selected = 'selected';
								} else
								{
									$selected = '';
								}
								echo '<option value="' . $design . '" ' . $selected . '>' . $design . '</option>';
							}
							?>
						</select>
					</p>
				</div>
			</div>
			<p>
				<label>Background Image</label>
				<?php
				// Show current background image preview and delete button
				if (!empty($settings['background_image']))
				{
					echo '
	<div class="row d-flex justify-content-center align-items-md-center">
		<img src="' . BACKGROUND_IMAGES_URL . '/' . htmlspecialchars($settings['background_image']) . '" class="col-md-2" width="128px" height="128px" alt="" aria-hidden="true" />
		<a href="?delete_bgrimg" class="btn red btn-sm col-md-2">
			<i class="fas fa-trash"></i> Delete
		</a>
	</div>';
				}
				// Show existing images as selectable options
				if (count($existing_images) > 0)
				{
					echo '<label class="mt-2">Or select an existing background image:</label>
		<div class="row">';
					$first_checked = false;
					foreach ($existing_images as $img)
					{
						// If no background image is set, check the first radio button by default
						$checked = '';
						if ($settings['background_image'] == $img)
						{
							$checked = 'checked';
						} elseif (!$first_checked && empty($settings['background_image']))
						{
							$checked = 'checked';
							$first_checked = true;
						}
						echo '<div class="col-md-2 text-center mb-2">
			<input type="radio" name="existing_background_image" value="' . htmlspecialchars($img) . '" id="img_' . md5($img) . '" ' . $checked . '>
			<label for="img_' . md5($img) . '">
				<img src="' . BACKGROUND_IMAGES_URL . '/' . htmlspecialchars($img) . '" width="64" height="64" alt="" aria-hidden="true" class="img-thumbnail mb-1">
			</label>
		</div>';
					}
					echo '</div>';
				}
				?>
				<input name="background_image" class="form-control mt-2" type="file" id="formFile">
			</p>
			<div class="form-actions">
				<button type="submit" name="save" class="btn btn-success">
					<i class="fa fa-save me-1"></i>Save Blog Settings
				</button>
			</div>
		</form>
	</div>
</div>
<?= template_admin_footer(); ?>