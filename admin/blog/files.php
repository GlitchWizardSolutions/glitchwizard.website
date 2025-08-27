<?php
/**
 * Blog Downloadables/Files Management
 * 
 * SYSTEM: GWS Universal Hybrid App - Admin Panel
 * FILE: files.php
 * LOCATION: /public_html/admin/blog/
 * PURPOSE: Manage downloadable files and documents for the blog system (view, search, upload, and delete)
 * 
 * CREATED: 2025-07-26
 * UPDATED: 2025-07-26
 * VERSION: 1.0
 * PRODUCTION: [READY FOR PRODUCTION]
 * 
 * CHANGE LOG:
 * 2025-07-26 - Initial implementation for blog files management
 * 2025-07-26 - Added file upload, search, and delete logic
 * 2025-07-26 - Improved UI and accessibility for file library
 * 2025-07-26 - Passed Quality Assurance (QA) check: UI, accessibility, error handling, and icon logic verified
 * 
 * FEATURES:
 * - View, search, and filter downloadable files
 * - Upload and delete files securely
 * - Professional admin interface with consistent UI
 * - File type, size, and date display for each file
 * - Responsive table and dropdown actions
 * 
 * DEPENDENCIES:
 * - header.php (blog includes)
 * - Bootstrap 5 for styling
 * - PDO database connection
 * - Font Awesome icons
 * 
 * SECURITY NOTES:
 * - Admin authentication required
 * - PDO prepared statements prevent SQL injection
 * - Input validation and sanitization
 * - XSS protection on output
 */
include "header.php";

// Filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Sorting setup following Table.php standard
$table_icons = [
    'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>',
    'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>'
];
$order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';
$order_by_whitelist = [
    'filename' => 'filename',
    'type' => 'filename',     // Type sorting uses filename since type is computed
    'size' => 'filename',     // Size sorting uses filename since size is computed  
    'date' => 'date'
];
$order_by = isset($_GET['order_by']) && isset($order_by_whitelist[$_GET['order_by']]) ? $_GET['order_by'] : 'date';
$order_by_sql = $order_by_whitelist[$order_by];

if (isset($_GET['delete']))
{
	$id = (int) $_GET["delete"];
	$stmt2 = $pdo->prepare("SELECT * FROM blog_files WHERE id = ?");
	$stmt2->execute([$id]);
	$row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
	if ($row2)
	{
		$file_path = $row2['path'];
		if (!empty($file_path) && !preg_match('/^([a-zA-Z]:\\\\|\\\\|\/)/', $file_path))
		{
			// Not absolute, prepend current script directory
			$abs_path = rtrim(__DIR__, '/\\') . '/' . ltrim($file_path, '/\\');
		} else
		{
			$abs_path = $file_path;
		}
		$resolved_path = !empty($abs_path) ? realpath($abs_path) : false;
		if ($resolved_path && file_exists($resolved_path))
		{
			unlink($resolved_path);
		}
		$stmt = $pdo->prepare("DELETE FROM blog_files WHERE id = ?");
		$stmt->execute([$id]);
	}
}
?>

<?= template_admin_header('Blog Downloadables', 'blog', 'files') ?>

<div class="content-title mb-4">
	<div class="title">
		<div class="icon">
			<svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
				<path d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM112 256H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/>
			</svg>
		</div>
		<div class="txt">
			<h2>Blog Documents & Files</h2>
			<p>Maintenance of documents and files available for download in the blog system.</p>
		</div>
	</div>
</div>

<div style="height: 20px;"></div>

<div class="mb-3">
	<a href="upload_file.php" class="btn btn-outline-secondary">
		<i class="fas fa-upload me-1"></i>Upload File
	</a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Blog Files Management</h6>
        <small class="text-muted" id="file-count">Loading...</small>
    </div>
    <div class="card-body">
        <form method="get" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input id="search" type="text" name="search" class="form-control"
                        placeholder="Search files..." 
                        value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">File Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">All Types</option>
                        <?php
                        // Get distinct file types from database
                        $typeStmt = $pdo->query("SELECT DISTINCT filename FROM blog_files ORDER BY filename");
                        $file_types = [];
                        while ($row = $typeStmt->fetch(PDO::FETCH_ASSOC)) {
                            if (!empty($row['filename'])) {
                                $ext = strtolower(pathinfo($row['filename'], PATHINFO_EXTENSION));
                                if (!empty($ext) && !in_array($ext, $file_types)) {
                                    $file_types[] = $ext;
                                }
                            }
                        }
                        sort($file_types);
                        foreach ($file_types as $ext) {
                            $selected = ($type_filter === $ext) ? ' selected' : '';
                            echo '<option value="' . htmlspecialchars($ext) . '"' . $selected . '>' . strtoupper($ext) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" 
                        value="<?= htmlspecialchars($date_from) ?>">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" 
                        value="<?= htmlspecialchars($date_to) ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-search me-1" aria-hidden="true"></i>
                        Apply Filters
                    </button>
                    <?php if ($search || $type_filter || $date_from || $date_to): ?>
                    <a href="files.php" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times me-1" aria-hidden="true"></i>
                        Clear
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <div class="table-responsive" role="table" aria-label="Downloadable Files">
            <table class="table table-hover mb-0" role="grid">
				<thead role="rowgroup">
					<tr role="row">
						<th class="text-left" style="text-align: left;" role="columnheader" scope="col">
							<?php $q = $_GET; $q['order_by'] = 'filename'; $q['order'] = ($order_by == 'filename' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
							<a href="?<?= http_build_query($q) ?>" class="sort-header">File Name<?= $order_by == 'filename' ? $table_icons[strtolower($order)] : '' ?></a>
						</th>
						<th style="text-align: center;" role="columnheader" scope="col">Thumbnail</th>
						<th style="text-align: center;" role="columnheader" scope="col">
							<?php $q = $_GET; $q['order_by'] = 'type'; $q['order'] = ($order_by == 'type' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
							<a href="?<?= http_build_query($q) ?>" class="sort-header">Type<?= $order_by == 'type' ? $table_icons[strtolower($order)] : '' ?></a>
						</th>
						<th style="text-align: center;" role="columnheader" scope="col">
							<?php $q = $_GET; $q['order_by'] = 'size'; $q['order'] = ($order_by == 'size' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
							<a href="?<?= http_build_query($q) ?>" class="sort-header">Size<?= $order_by == 'size' ? $table_icons[strtolower($order)] : '' ?></a>
						</th>
						<th style="text-align: center;" role="columnheader" scope="col">
							<?php $q = $_GET; $q['order_by'] = 'date'; $q['order'] = ($order_by == 'date' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
							<a href="?<?= http_build_query($q) ?>" class="sort-header">Uploaded<?= $order_by == 'date' ? $table_icons[strtolower($order)] : '' ?></a>
						</th>
						<th style="text-align: center;" role="columnheader" scope="col">Actions</th>
					</tr>
				</thead>
				<tbody role="rowgroup">
			<?php
			// Build WHERE clause for filters
			$where_conditions = [];
			$params = [];
			
			if ($search !== '') {
				$where_conditions[] = "filename LIKE ?";
				$params[] = '%' . $search . '%';
			}
			
			if ($type_filter !== '') {
				$where_conditions[] = "filename LIKE ?";
				$params[] = '%.' . $type_filter;
			}
			
			if ($date_from !== '') {
				$where_conditions[] = "DATE(date) >= ?";
				$params[] = $date_from;
			}
			
			if ($date_to !== '') {
				$where_conditions[] = "DATE(date) <= ?";
				$params[] = $date_to;
			}
			
			$where_clause = '';
			if (!empty($where_conditions)) {
				$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
			}
			
			// Get total count for display with filters applied
			$countSql = "SELECT COUNT(*) FROM blog_files $where_clause";
			if (!empty($params)) {
				$countStmt = $pdo->prepare($countSql);
				$countStmt->execute($params);
				$total_files = $countStmt->fetchColumn();
			} else {
				$countStmt = $pdo->prepare($countSql);
				$countStmt->execute();
				$total_files = $countStmt->fetchColumn();
			}
			
			$sql = "SELECT * FROM blog_files $where_clause ORDER BY $order_by_sql $order";
			
			if (!empty($params)) {
				$stmt = $pdo->prepare($sql);
				$stmt->execute($params);
			} else {
				$stmt = $pdo->query($sql);
			}
			$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			// Update the file count in the header
			echo '<script>document.getElementById("file-count").textContent = "' . number_format($total_files) . ' total files";</script>';
			
			foreach ($files as $row)
			{
				// Build absolute path if needed
				$file_path = $row['path'];
				if (!empty($file_path) && !preg_match('/^([a-zA-Z]:\\|\\|\/)/', $file_path))
				{
					// Not absolute, prepend current script directory
					$abs_path = rtrim(__DIR__, '/\\') . '/' . ltrim($file_path, '/\\');
				} else
				{
					$abs_path = $file_path;
				}
				$resolved_path = !empty($abs_path) ? realpath($abs_path) : false;
				$file_exists = $resolved_path && file_exists($resolved_path);
				$type = ($file_exists) ? pathinfo($resolved_path, PATHINFO_EXTENSION) : (empty($file_path) ? 'N/A' : 'Missing');
				$size = ($file_exists) ? byte_convert(filesize($resolved_path)) : (empty($file_path) ? 'N/A' : '-');
				// For thumbnail and tooltip, use $resolved_path if available
				$thumb_path = $file_exists ? $resolved_path : $abs_path;
				// Determine if file is an image
				$image_exts = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg');
				$is_image = $file_exists && in_array(strtolower(pathinfo($thumb_path, PATHINFO_EXTENSION)), $image_exts);
				$thumb_html = '';
				$tooltip = 'DB path: ' . $row['path'] . '\nResolved: ' . $thumb_path;
				if ($is_image)
				{
					$thumb_html = '<img src="' . htmlspecialchars($row['path']) . '" alt="thumb" style="max-width:48px;max-height:48px;object-fit:contain;border:1px solid #ccc;background:#fff;" title="' . htmlspecialchars($tooltip) . '">';
				} elseif ($file_exists)
				{
					$thumb_html = '<i class="fa fa-file fa-2x" style="color:#888;" title="' . htmlspecialchars($tooltip) . '"></i>';
				} else
				{
					$thumb_html = '<i class="fa fa-exclamation-triangle fa-2x" style="color:#c00;" title="' . htmlspecialchars($tooltip) . '"></i>';
				}
				echo '
	   <tr role="row">
		   <td class="text-left" role="gridcell">' . htmlspecialchars($row['filename']) . '</td>
		   <td style="text-align: center;" role="gridcell">' . $thumb_html . '</td>
		   <td style="text-align: center;" role="gridcell">' . htmlspecialchars($type) . '</td>
		   <td style="text-align: center;" role="gridcell">' . htmlspecialchars($size) . '</td>
		   <td style="text-align: center;" role="gridcell" data-sort="' . strtotime($row['date']) . '">' . date($settings['date_format'], strtotime($row['date'])) . ', ' . strtolower(date('h:i a', strtotime($row['time']))) . '</td>
		   <td class="actions" style="text-align: center;" role="gridcell">
			   <div class="table-dropdown">
				   <button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for file ' . htmlspecialchars($row['filename']) . '">
					   <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
						   <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
					   </svg>
				   </button>
				   <div class="table-dropdown-items" role="menu" aria-label="File Actions">
					   ' . ($file_exists ? '<div role="menuitem"><a class="green" href="' . htmlspecialchars($row['path']) . '" target="_blank" tabindex="-1" aria-label="View file ' . htmlspecialchars($row['filename']) . '"><i class="fas fa-eye" aria-hidden="true"></i><span>&nbsp;View</span></a></div>' : '') . '
					   <div role="menuitem"><a href="?delete=' . $row['id'] . '" class="red" onclick="return confirm(\'Are you sure you want to delete this file?\')" tabindex="-1" aria-label="Delete file ' . htmlspecialchars($row['filename']) . '"><i class="fas fa-trash" aria-hidden="true"></i><span>&nbsp;Delete</span></a></div>
				   </div>
			   </div>
		   </td>
	   </tr>
';
			}
			?>
			</tbody>
        </table>
    </div>
    <div class="card-footer bg-light">
        <div class="small">
            Showing <?= count($files) ?> of <?= $total_files ?> files
        </div>
    </div>
</div>

<!-- DataTables removed: custom search bar is now used above -->
<?= template_admin_footer(); ?>