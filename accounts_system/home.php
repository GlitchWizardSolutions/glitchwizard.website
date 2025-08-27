<?php
/*
 * SYSTEM: GWS Universal Hybrid Application
 * LOCATION: public_html/accounts_system/home.php
 * LOG: Default home page for authenticated users with unified template
 * PRODUCTION: [To be updated on deployment]
 */

// Include required files
include 'main.php';
require_once '../../private/gws-universal-functions.php';

// Check if the user is logged in with remember-me support
check_loggedin_full($pdo, '../auth.php?tab=login');

// Template code below
?>
<?= template_header('Dashboard') ?>

<?php

?>

<section class="section dashboard">
	<div class="row">

		<!-- Welcome Card -->
		<div class="col-lg-12">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">Welcome Back!</h5>
					<div class="d-flex align-items-center">
						<div class="ps-3">
							<h6>Hello, <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES) ?>!</h6>
							<span class="text-muted">Welcome to your dashboard. You can customize this page by editing
								the home.php file.</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Quick Stats -->
		<div class="col-xxl-4 col-md-6">
			<div class="card info-card sales-card">
				<div class="card-body">
					<h5 class="card-title">Account <span>| Status</span></h5>
					<div class="d-flex align-items-center">
						<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
							<i class="bi bi-person-check"></i>
						</div>
						<div class="ps-3">
							<h6>Active</h6>
							<span class="text-success small pt-1 fw-bold">Verified Account</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xxl-4 col-md-6">
			<div class="card info-card revenue-card">
				<div class="card-body">
					<h5 class="card-title">Role <span>| Access Level</span></h5>
					<div class="d-flex align-items-center">
						<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
							<i class="bi bi-shield-check"></i>
						</div>
						<div class="ps-3">
							<h6><?= htmlspecialchars($_SESSION['role'] ?? 'Member', ENT_QUOTES) ?></h6>
							<span class="text-muted small pt-1">Current Role</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xxl-4 col-xl-12">
			<div class="card info-card customers-card">
				<div class="card-body">
					<h5 class="card-title">Last Login <span>| Activity</span></h5>
					<div class="d-flex align-items-center">
						<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
							<i class="bi bi-clock"></i>
						</div>
						<div class="ps-3">
							<h6><?= isset($_SESSION['account_last_seen']) ? date('M j, Y', strtotime($_SESSION['account_last_seen'])) : 'Today' ?>
							</h6>
							<span
								class="text-muted small pt-1"><?= isset($_SESSION['account_last_seen']) ? date('g:i A', strtotime($_SESSION['account_last_seen'])) : 'Current Session' ?></span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Quick Actions -->
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">Quick Actions</h5>
					<div class="row">
						<div class="col-md-4 mb-3">
							<a href="profile.php" class="btn btn-primary w-100">
								<i class="bi bi-person me-2"></i>View Profile
							</a>
						</div>
						<div class="col-md-4 mb-3">
							<a href="settings.php" class="btn btn-outline-primary w-100">
								<i class="bi bi-gear me-2"></i>Account Settings
							</a>
						</div>
						<?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'Admin'): ?>
							<div class="col-md-4 mb-3">
								<a href="../admin" class="btn btn-success w-100">
									<i class="bi bi-shield-check me-2"></i>Admin Panel
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

	</div>
</section>

<?= template_footer() ?>