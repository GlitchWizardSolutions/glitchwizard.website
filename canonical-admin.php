<?php
// IMPORTANT DEVELOPMENT INSTRUCTION:
// When updating admin tables or related files, always use standardized, additive, and non-destructive edits.
// DO NOT replace or overwrite large code blocks unless absolutely necessary.
// Always review the existing structure and make minimal, targeted changes to avoid introducing errors.
// This practice ensures maintainability and reduces the risk of breaking functionality across the LAMP stack.
//DO NOT EDIT
/*
 * GWS UNIVERSAL TABLE STANDARDIZATION REFERENCE - COMPLETE GUIDE
 * 
 * IMPORTANT: This reference shows the exact structure to implement standardized tables 
 * across the admin interface. All required CSS and JavaScript are already included 
 * in the main stylesheet (gws-universal-branding.css) and scripts.
 * 
 * VERSION: Updated August 8, 2025 - Includes card footer pagination and Font Awesome icons
 * 
 * Available CSS Classes (already defined in stylesheet):
 * - .table               - Main table container
 * - .card               - Table wrapper
 * - .card-header        - Table title section
 * - .card-body          - Table content wrapper
 * - .card-footer        - Table footer section (for pagination)
 * - .table-dropdown     - Action menu container
 * - .table-dropdown-items - Dropdown content
 * - .green              - Success actions (edit, approve)
 * - .red                - Destructive actions (delete)
 * - .black              - Neutral actions (reject, cancel)
 * - .avatar-img         - User avatar styling
 * - .text-left          - Left-aligned text columns
 * - .actions            - Actions column styling
 * - .pagination         - Pagination container
 * 
 * Font Awesome Icons (Font Awesome 5 - fas classes):
 * - fa-check            - Approve actions (green)
 * - fa-times            - Reject/remove actions (black)
 * - fa-edit             - Edit actions (green) 
 * - fa-trash            - Delete actions (red)
 * - fa-user             - User/guest indicators
 * 
 * The JavaScript for dropdown functionality is already included in the main scripts.
 * Just use the structure below and it will work automatically.
 * 
 * IMPORTANT: This is the canonical reference for all table implementations in the admin interface.
 * DO NOT MODIFY THIS FILE without updating all related documentation and ensuring backward compatibility.
 * 
 * HOW TO USE THIS REFERENCE:
 * 1. Always wrap tables in a card structure with header, body, and footer
 * 2. Follow the exact HTML structure shown below
 * 3. Use the provided CSS classes without modification
 * 4. Maintain consistent action menu implementation
 * 5. Use Font Awesome icons for actions instead of SVG
 * 6. Place pagination in card footer with bg-light class
 * 
 * TABLE IMPLEMENTATION RULES:
 * 1. DO NOT use inline styles except for the predefined ones shown in the example
 * 2. DO NOT modify the structure of the action menu
 * 3. DO NOT add custom classes unless absolutely necessary
 * 4. ALWAYS maintain the accessibility attributes
 * 5. ALWAYS separate Avatar and Username into different columns
 * 6. ALWAYS use Font Awesome icons for actions (fa-check, fa-times, etc.)
 * 7. ALWAYS place pagination in card footer
 * 
 * COMPLETE TABLE STRUCTURE WITH ALL FEATURES:
 * <div class="card">
 *     <h6 class="card-header">Table Title (e.g., "Accounts", "Recent Comments")</h6>
 *     <div class="card-body">
 *         <div class="table" role="table" aria-label="Descriptive table label">
 *             <table role="grid">
 *                 <thead role="rowgroup">
 *                     <tr role="row">
 *                         <!-- Avatar column: always center-aligned -->
 *                         <th style="text-align: center;" role="columnheader" scope="col">Avatar</th>
 *                         <!-- Username column: always left-aligned with sorting -->
 *                         <th class="text-left" style="text-align: left;" role="columnheader" scope="col">
 *                             <?php $q = $_GET; $q['order_by'] = 'username'; $q['order'] = ($order_by == 'username' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
 *                             <a href="?<?= http_build_query($q) ?>" class="sort-header">Username<?= $order_by == 'username' ? $table_icons[strtolower($order)] : '' ?></a>
 *                         </th>
 *                         <!-- Email column: left-aligned with sorting -->
 *                         <th class="text-left responsive-hidden" style="text-align: left;" role="columnheader" scope="col">
 *                             <?php $q = $_GET; $q['order_by'] = 'email'; $q['order'] = ($order_by == 'email' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
 *                             <a href="?<?= http_build_query($q) ?>" class="sort-header">Email<?= $order_by == 'email' ? $table_icons[strtolower($order)] : '' ?></a>
 *                         </th>
 *                         <!-- Status/Date columns: center-aligned with sorting -->
 *                         <th class="responsive-hidden" style="text-align: center;" role="columnheader" scope="col">
 *                             <?php $q = $_GET; $q['order_by'] = 'status'; $q['order'] = ($order_by == 'status' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
 *                             <a href="?<?= http_build_query($q) ?>" class="sort-header">Status<?= $order_by == 'status' ? $table_icons[strtolower($order)] : '' ?></a>
 *                         </th>
 *                         <!-- Actions column: always last, always center-aligned -->
 *                         <th style="text-align: center;" role="columnheader" scope="col">Actions</th>
 *                     </tr>
 *                 </thead>
 *                 <tbody role="rowgroup">
 *                     <?php foreach ($items as $item): ?>
 *                     <tr role="row">
 *                         <!-- Avatar cell: center-aligned with proper styling -->
 *                         <td style="text-align: center;" role="gridcell">
 *                             <img src="<?= $avatar_path ?>" class="avatar-img" alt="<?= htmlspecialchars($item['username']) ?> avatar" 
 *                                  style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;">
 *                         </td>
 *                         <!-- Username cell: left-aligned -->
 *                         <td class="text-left" role="gridcell"><?= htmlspecialchars($item['username']) ?></td>
 *                         <!-- Email cell: left-aligned -->
 *                         <td class="text-left responsive-hidden" role="gridcell"><?= htmlspecialchars($item['email']) ?></td>
 *                         <!-- Status cell: center-aligned -->
 *                         <td style="text-align: center;" class="responsive-hidden" role="gridcell">
 *                             <span class="<?= $status_class ?>"><?= $status_text ?></span>
 *                         </td>
 *                         <!-- Actions cell: center-aligned with dropdown -->
 *                         <td class="actions" style="text-align: center;" role="gridcell">
 *                             <div class="table-dropdown">
 *                                 <button class="actions-btn" aria-haspopup="true" aria-expanded="false" aria-label="Actions for <?= htmlspecialchars($item['username']) ?>">
 *                                     <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
 *                                         <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
 *                                     </svg>
 *                                 </button>
 *                                 <div class="table-dropdown-items" role="menu" aria-label="Item Actions">
 *                                     <!-- Edit action: green with Font Awesome icon -->
 *                                     <div role="menuitem">
 *                                         <a href="edit.php?id=<?= $item['id'] ?>" class="green" tabindex="-1" aria-label="Edit <?= htmlspecialchars($item['username']) ?>">
 *                                             <i class="fas fa-edit" aria-hidden="true"></i>
 *                                             <span>Edit</span>
 *                                         </a>
 *                                     </div>
 *                                     <!-- Approve action: green with Font Awesome check -->
 *                                     <div role="menuitem">
 *                                         <a href="approve.php?id=<?= $item['id'] ?>" class="green" tabindex="-1" aria-label="Approve <?= htmlspecialchars($item['username']) ?>">
 *                                             <i class="fas fa-check" aria-hidden="true"></i>
 *                                             <span>Approve</span>
 *                                         </a>
 *                                     </div>
 *                                     <!-- Reject action: black with Font Awesome times -->
 *                                     <div role="menuitem">
 *                                         <a href="reject.php?id=<?= $item['id'] ?>" class="black" tabindex="-1" aria-label="Reject <?= htmlspecialchars($item['username']) ?>">
 *                                             <i class="fas fa-times" aria-hidden="true"></i>
 *                                             <span>Reject</span>
 *                                         </a>
 *                                     </div>
 *                                     <!-- Delete action: red with Font Awesome trash -->
 *                                     <div role="menuitem">
 *                                         <a href="delete.php?id=<?= $item['id'] ?>" class="red" onclick="return confirm('Are you sure?')" tabindex="-1" aria-label="Delete <?= htmlspecialchars($item['username']) ?>">
 *                                             <i class="fas fa-trash" aria-hidden="true"></i>
 *                                             <span>Delete</span>
 *                                         </a>
 *                                     </div>
 *                                 </div>
 *                             </div>
 *                         </td>
 *                     </tr>
 *                     <?php endforeach; ?>
 *                 </tbody>
 *             </table>
 *         </div>
 *     </div>
 *     <!-- Card Footer with Pagination: light background for visual separation -->
 *     <div class="card-footer bg-light">
 *         <div class="pagination" style="text-align: left;">
 *             <!-- For paginated lists -->
 *             <span>Page <?= $page ?> of <?= $total_pages == 0 ? 1 : $total_pages ?></span>
 *             <?php if ($page > 1): ?>
 *                 | <a href="?page=<?= $page - 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>">Previous</a>
 *             <?php endif; ?>
 *             <?php if ($page * $results_per_page < $total_count): ?>
 *                 | <a href="?page=<?= $page + 1 ?>&order=<?= $order ?>&order_by=<?= $order_by ?>">Next</a>
 *             <?php endif; ?>
 *             <!-- For dashboard "recent items" lists -->
 *             <!-- <span>Showing <?= count($items) ?> recent item<?= count($items) != 1 ? 's' : '' ?></span> -->
 *         </div>
 *     </div>
 * </div>
 *
 * DETAILED STYLING AND STRUCTURE INSTRUCTIONS:
 * 
 * PHP SORTING SETUP (required at top of PHP file):
 * ```php
 * // 1. Define sorting icons (13px triangle arrows)
 * $table_icons = [
 *     'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>',
 *     'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>'
 * ];
 * 
 * // 2. Get order direction (default ASC)
 * $order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';
 * 
 * // 3. Define allowed sortable columns (whitelist for security)
 * $order_by_whitelist = [
 *     'username' => 'username',        // URL param => DB column
 *     'email' => 'email',
 *     'status' => 'activation_code',   // Example: status maps to activation_code
 *     'date' => 'created_at'
 * ];
 * 
 * // 4. Get and validate order_by parameter
 * $order_by = isset($_GET['order_by']) && isset($order_by_whitelist[$_GET['order_by']]) 
 *             ? $_GET['order_by'] : 'username'; // default column
 * $order_by_sql = $order_by_whitelist[$order_by]; // actual DB column name
 * 
 * // 5. Use in SQL query
 * $sql = "SELECT * FROM table_name ORDER BY $order_by_sql $order";
 * ```
 * 
 * COLUMN ALIGNMENT RULES:
 * - Avatar column: ALWAYS center-aligned (style="text-align: center;")
 * - Username/Text columns: ALWAYS left-aligned (class="text-left" style="text-align: left;")
 * - Email columns: ALWAYS left-aligned (class="text-left" style="text-align: left;")
 * - Date/Status/Numeric columns: ALWAYS center-aligned (style="text-align: center;")
 * - Actions column: ALWAYS center-aligned (class="actions" style="text-align: center;")
 * 
 * FONT AWESOME ICON USAGE:
 * - Approve actions: <i class="fas fa-check" aria-hidden="true"></i> (green)
 * - Reject actions: <i class="fas fa-times" aria-hidden="true"></i> (black)
 * - Edit actions: <i class="fas fa-edit" aria-hidden="true"></i> (green)
 * - Delete actions: <i class="fas fa-trash" aria-hidden="true"></i> (red)
 * - Guest user badge: <i class="fas fa-user"></i> Guest
 * 
 * AVATAR STYLING:
 * - Always use: style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;"
 * - Always include: class="avatar-img" 
 * - Always include: alt="[username] avatar"
 * 
 * PAGINATION IMPLEMENTATION:
 * - For sorting, use clickable table headers with GET parameters (?order_by=column&order=ASC|DESC) and 13px ▲/▼ icons. First, define the sorting setup at the top of your PHP file:
 *   $table_icons = [
 *       'asc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9650;</span>',
 *       'desc' => '<span style="font-size:13px;display:inline-block;vertical-align:middle;">&#9660;</span>'
 *   ];
 *   $order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';
 *   $order_by_whitelist = [
 *       'column1' => 'database_column1',
 *       'column2' => 'database_column2'
 *   ];
 *   $order_by = isset($_GET['order_by']) && isset($order_by_whitelist[$_GET['order_by']]) ? $_GET['order_by'] : 'default_column';
 *   $order_by_sql = $order_by_whitelist[$order_by];
 *   Then use: ORDER BY $order_by_sql $order in your SQL query.
 * - For sortable headers, use this exact pattern:
 *   <th class="text-left" style="text-align: left;">
 *       <?php $q = $_GET; $q['order_by'] = 'column_name'; $q['order'] = ($order_by == 'column_name' && $order == 'ASC') ? 'DESC' : 'ASC'; ?>
 *       <a href="?<?= http_build_query($q) ?>" class="sort-header">Column Title<?= $order_by == 'column_name' ? $table_icons[strtolower($order)] : '' ?></a>
 *   </th>
 * - The pagination must always be left-aligned, plain text, and appear in a card footer with the class "card-footer bg-light". Use this exact structure:
 *   <div class="card-footer bg-light">
 *       <div class="pagination" style="text-align: left;">
 *           <span>Page <?= $page ?> of <?= $total_pages == 0 ? 1 : $total_pages ?></span>
 *       </div>
 *   </div>
 *   For dashboard tables showing recent items, use: <span>Showing <?= count($items) ?> recent item<?= count($items) != 1 ? 's' : '' ?></span>
 * 
 * REQUIRED STRUCTURE ELEMENTS:
 * 1. Avatar and Username must be in separate columns. Use this exact structure:
 *    Avatar column: <th style="text-align: center;">Avatar</th> and <td style="text-align: center;"><img src="..." class="avatar-img" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;" /></td>
 *    Username column: <th class="text-left" style="text-align: left;">Username</th> and <td class="text-left">username_value</td>
 * 2. Use canonical .table-dropdown structure - DO NOT modify the dropdown HTML
 * 3. Actions column must always be last and center-aligned
 * 4. Use Font Awesome icons instead of SVG for actions
 * 5. Card footer must have bg-light class for visual separation
 * 
 * ACCESSIBILITY REQUIREMENTS:
 * - Include role="table", role="grid", role="rowgroup", role="row", role="columnheader", role="gridcell" attributes
 * - Include aria-label attributes for tables and buttons
 * - Include aria-haspopup="true" aria-expanded="false" on dropdown buttons
 * - Include tabindex="-1" on dropdown menu items
 * - Include scope="col" on column headers
 * - Include aria-hidden="true" on decorative icons
 * 
 * ERROR HANDLING BEST PRACTICES:
 * - Always validate $_GET parameters against whitelists
 * - Use prepared statements for database queries
 * - Include fallback values for pagination (avoid division by zero)
 * - Always escape output with htmlspecialchars()
 * - Include confirmation dialogs for destructive actions
 * 
 * RESPONSIVE DESIGN:
 * - Use responsive-hidden class for email and other less critical columns
 * - Ensure dropdown menus work on mobile devices
 * - Test table overflow behavior on small screens
 * 
 * IMPLEMENTATION CHECKLIST:
 * ✓ Card structure with header, body, and footer
 * ✓ Separate Avatar and Username columns  
 * ✓ Canonical dropdown structure
 * ✓ Font Awesome icons for actions
 * ✓ Sorting with GET parameters and triangle icons
 * ✓ Card footer pagination with bg-light
 * ✓ Left-aligned text, center-aligned status/dates/actions
 * ✓ Accessibility attributes
 * ✓ Security (parameter validation)
 * ✓ Error handling and confirmations
 * 1. Avatar and Username must be in separate columns. Use this exact structure:
 *    Avatar column: <th style="text-align: center;">Avatar</th> and <td style="text-align: center;"><img src="..." class="avatar-img" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #dee2e6;" /></td>
 *    Username column: <th class="text-left" style="text-align: left;">Username</th> and <td class="text-left">username_value</td>
 * 2. All other text columns that should be left-aligned must use class="text-left" on both <th> and <td>.
 * 3. Date, Status, and numeric columns are always center-aligned using style="text-align: center;" on both <th> and <td>.
 * 4. The Actions column must be the last column, always center-aligned, and use the .table-dropdown structure. The dropdown button must use class="actions-btn" with proper ARIA attributes.
 * 5. Do not add or modify CSS or JavaScript files. Use only the included stylesheets and scripts. All required classes and dropdown logic are already present.
 * 6. Do not use inline styles except for text-align and width as shown above.
 * 7. Do not add custom classes. Use only .avatar-img, .text-left, .table, .card, .card-header, .card-body, .table-dropdown, .table-dropdown-items, .actions, and the color classes.
 * 8. For accessibility, use proper ARIA attributes and tabindex as in the example.
 * 9. The actions column and dropdown must look and function exactly as in blog_dash.php, including SVG icons, spacing, and color classes.
 * 10. Do not edit any file that contains //DO NOT EDIT at the top.
 * 11. Always include JavaScript to ensure only one Actions dropdown menu is open at a time. If the canonical JS is already included in your admin template (check main.js or unified-utils.js), you don't need to add inline scripts. If not, include the script pattern shown at the bottom of this file. The script must close any open menu when another is opened, and close all menus when clicking outside.
 *
 * FLEXIBLE TABLE PATTERNS FOR DIFFERENT DATA TYPES:
 * - Tables can have different numbers of columns - adjust colspan in error/no-results rows to match total columns
 * - Column types and alignment rules:
 *   * Avatar/Image columns: Center-aligned, .avatar-img class
 *   * Text content (names, titles, descriptions): Left-aligned with .text-left class
 *   * Dates, status, numbers, IDs: Center-aligned with style="text-align: center;"
 *   * Actions: Always last column, center-aligned, .table-dropdown structure
 * - Error and empty state rows:
 *   * No results: <td colspan="[total_columns]" class="no-results">Your message here</td>
 *   * Error messages: <td colspan="[total_columns]" class="error-message-row">Error message here</td>
 * - Sorting: Only add sorting to columns where it makes sense (not for actions, images, or computed values)
 * - Pagination text should match your context: "Page X of Y", "Showing X items", "Showing X recent [type]"
 *
 * STANDARD ICONS FOR COMMENT ACTIONS:
 * - Approve Comment: <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 512A256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/></svg>
 * - Reject Comment: <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M256 512A256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/></svg>
 *
 * This template is the canonical reference for all admin tables. Use it as the basis for all future table implementations.
 * 
 * STANDARD CSS CLASSES:
 * .table        - Main table container
 * .actions      - Action column cells
 * .table-dropdown - Action menu container
 * .table-dropdown-items - Dropdown menu container
 * Color classes for actions:
 * .green       - For edit/approve actions
 * .red         - For delete actions
 * .black       - For reject/cancel actions
 * 
 * REQUIRED CSS (Include in your stylesheet):
 */
?>

<style>
/*
 * COPY THIS EXACT STRUCTURE:
 * Only modify:
 * 1. Card header title
 * 2. Column headers (maintaining alignment rules)
 * 3. Table content
 * 4. Action menu items (maintaining structure)
 */

<!-- Standard Table Structure -->
<div class="card">
    <h6 class="card-header">Table Title</h6>
    <div class="card-body">
        <div class="table">
            <table>
                <thead>
                    <tr>
                        <!-- Text columns: left-aligned -->
                        <th style="text-align: left;">Text Column</th>
                        <!-- Date, Status, Numbers: center-aligned -->
                        <th style="text-align: center;">Date</th>
                        <th style="text-align: center;">Status</th>
                        <!-- Actions: always last, always centered -->
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <!-- Text content: left-aligned -->
                        <td>Sample text</td>
                        <!-- Date: center-aligned -->
                        <td style="text-align: center;">2025-08-07</td>
                        <!-- Status: center-aligned -->
                        <td style="text-align: center;">Active</td>
                        <!-- Actions: centered with dropdown -->
                        <td class="actions" style="text-align: center;">
                            <div class="table-dropdown">
                                <!-- Button wrapper for SVG with ARIA attributes -->
                                <button class="actions-btn" aria-haspopup="true" aria-expanded="false"
                                        aria-label="Actions">
                                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" 
                                         viewBox="0 0 448 512" style="fill: #333333;">
                                        <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z"/>
                                    </svg>
                                </button>
                                <!-- Dropdown menu -->
                                <div class="table-dropdown-items">
                                    <!-- Action items -->
                                    <a href="#" class="green">
                                        <span class="icon">
                                            <!-- SVG icon here -->
                                        </span>
                                        Edit
                                    </a>
                                    <a href="#" class="red">
                                        <span class="icon">
                                            <!-- SVG icon here -->
                                        </span>
                                        Delete
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

/*
 * ALIGNMENT RULES:
 * 1. Text columns are left-aligned (both header and content)
 * 2. Dates, Status, and Numbers are center-aligned
 * 3. Actions column is always center-aligned
 * 
 * ACTION MENU COLORS:
 * - Use class="green" for edit, approve actions
 * - Use class="red" for delete actions
 * - Use class="black" for reject, cancel actions
 * 
 * NOTE: All required CSS and JavaScript functionality is included 
 * in gws-universal-branding.css and assets/js/main.js.
 * Just follow this structure exactly and the functionality will work automatically.
 */

/*
 * EXACT TABLE STRUCTURE TO FOLLOW:
 * 
 * Note: All styles are now in gws-universal-branding.css
 * All JavaScript functionality is in assets/js/main.js
 */
</style>

<?php
//DO NOT EDIT
/*
 * IMPLEMENTATION NOTES:
 * 1. Copy the table structure exactly as shown above
 * 2. Adjust only the following elements:
 *    - Table title in card-header
 *    - Column headers (maintaining alignment rules)
 *    - Data content
 *    - Action menu items (maintaining structure)
 * 3. DO NOT modify the CSS or JavaScript
 * 4. DO NOT add custom styles inline
 * 5. Use the provided color classes for actions
 * 
 * EXAMPLE ACTION MENU ITEMS:
 * Edit:   class="green" with edit icon
 * Delete: class="red" with trash icon
 * Approve/Reject: class="green"/"black" with check/x icon
 * 
 * For any deviations from this standard, consult the development team lead.
 */
?>
// TABLE STRUCTURE AND STYLING GUIDE
// ===========================================
?>

<!-- Basic Accessible Table Structure -->
<div class="table" role="table" aria-label="[Table Description]">
    <table role="grid" aria-label="[More Specific Table Description]">
        <thead role="rowgroup">
            <tr role="row">
                <th role="columnheader" scope="col">Column 1</th>
                <th role="columnheader" scope="col" class="responsive-hidden">Column 2</th>
                <th role="columnheader" scope="col" class="align-center">Actions</th>
            </tr>
        </thead>
        <tbody role="rowgroup">
            <!-- No Results Message -->
            <?php if (empty($items)): ?>
                <tr role="row">
                    <td colspan="[number-of-columns]" class="no-results">No items found</td>
                </tr>
            <?php endif; ?>
            
            <!-- Error Message Example -->
            <?php if (isset($_GET['error'])): ?>
                <tr role="row">
                    <td colspan="[number-of-columns]" class="error-message-row">[Error Message]</td>
                </tr>
            <?php endif; ?>

            <!-- Table Rows -->
            <?php foreach ($items as $item): ?>
                <tr role="row">
                    <!-- Regular Cell -->
                    <td tabindex="0"><?= htmlspecialchars($item['field'], ENT_QUOTES) ?></td>
                    
                    <!-- Responsive Hidden Cell -->
                    <td class="responsive-hidden" tabindex="0">
                        <?= htmlspecialchars($item['field2'], ENT_QUOTES) ?>
                    </td>
                    
                    <!-- Actions Cell -->
                    <td class="actions" style="text-align: center;" tabindex="0">
                        <div class="table-dropdown">
                            <button aria-haspopup="true" 
                                   aria-expanded="false" 
                                   class="dropdown-toggle" 
                                   aria-label="Actions for <?= htmlspecialchars($item['name'], ENT_QUOTES) ?>">
                                <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" 
                                     viewBox="0 0 448 512" aria-hidden="true">
                                    <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
                                </svg>
                                <span class="sr-only">Toggle menu</span>
                            </button>
                            <div class="table-dropdown-items" role="menu" aria-label="[Item Type] Actions">
                                <!-- View Action Example -->
                                <div role="menuitem">
                                    <a href="view.php?id=<?= $item['id'] ?>" 
                                       class="blue" 
                                       tabindex="-1"
                                       aria-label="View details for <?= htmlspecialchars($item['name'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                                                <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/>
                                            </svg>
                                        </span>
                                        <span>View</span>
                                    </a>
                                </div>
                                
                                <!-- Edit Action Example -->
                                <div role="menuitem">
                                    <a href="edit.php?id=<?= $item['id'] ?>" 
                                       class="green" 
                                       tabindex="-1"
                                       aria-label="Edit <?= htmlspecialchars($item['name'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/>
                                            </svg>
                                        </span>
                                        <span>Edit</span>
                                    </a>
                                </div>

                                <!-- Delete Action Example -->
                                <div role="menuitem">
                                    <a href="delete.php?id=<?= $item['id'] ?>" 
                                       class="red" 
                                       onclick="return confirm('Are you sure you want to delete this item?')"
                                       tabindex="-1"
                                       aria-label="Delete <?= htmlspecialchars($item['name'], ENT_QUOTES) ?>">
                                        <span class="icon" aria-hidden="true">
                                            <svg width="12" height="12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                                            </svg>
                                        </span>
                                        <span>Delete</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Required CSS for Tables -->
<style>
/* Table Base Styles */
.table {
    width: 100%;
    margin-bottom: 1rem;
    background-color: transparent;
}

.table table {
    width: 100%;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 0.75rem;
    vertical-align: middle;
    border-top: 1px solid var(--table-border-color, #dee2e6);
}

.table thead th {
    vertical-align: bottom;
    border-bottom: 2px solid var(--table-border-color, #dee2e6);
    background-color: var(--table-header-background, #ffffff);
    color: var(--table-header-text, #000000);
    font-weight: 600;
}

/* Responsive Classes */
.responsive-hidden {
    @media (max-width: 768px) {
        display: none;
    }
}

/* Message Rows */
.no-results,
.error-message-row {
    text-align: center;
    padding: 2rem !important;
}

.error-message-row {
    color: #d32f2f !important;
    font-weight: bold;
    background: #fff3f3 !important;
    border-bottom: 2px solid #d32f2f;
}

/* Accessibility Helper */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0,0,0,0);
    border: 0;
}

/* Action Menu Styles */
.table-dropdown {
    position: relative !important;
    display: inline-block !important;
    cursor: pointer !important;
    padding: 8px !important;
    border-radius: 4px !important;
}

.table-dropdown:hover {
    background-color: #f8f9fa !important;
}

.table-dropdown-items {
    display: none !important;
    position: absolute !important;
    right: 0 !important;
    top: 100% !important;
    background: white !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 6px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    z-index: 9999 !important;
    min-width: 160px !important;
}

.table-dropdown-items a {
    display: flex !important;
    align-items: center !important;
    padding: 10px 15px !important;
    text-decoration: none !important;
    color: #495057 !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    transition: background-color 0.2s ease !important;
}

.table-dropdown-items a:hover {
    background-color: #f8f9fa !important;
}

.table-dropdown-items .icon {
    margin-right: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
}

/* Action Colors */
.blue { color: var(--brand-primary, #3671c9) !important; }
.green { color: var(--brand-secondary, #2fc090) !important; }
.red { color: var(--status-danger, #dc3545) !important; }
.black { color: var(--header-text-color, #4a5361) !important; }

/* Focus Management */
.table-dropdown button:focus,
.table-dropdown a:focus {
    outline: 2px solid #4A90E2;
    outline-offset: 2px;
}

/* High Contrast Support */
@media (forced-colors: active) {
    .table-dropdown button:focus,
    .table-dropdown a:focus {
        outline: 3px solid currentColor;
    }
}
</style>

<!-- Required JavaScript for Action Menus -->
<script>
document.querySelectorAll('.table-dropdown').forEach(dropdown => {
    const button = dropdown.querySelector('button');
    const menu = dropdown.querySelector('.table-dropdown-items');
    const menuItems = menu.querySelectorAll('[role="menuitem"] a');
    
    // Toggle menu on button click
    button.addEventListener('click', () => {
        const isExpanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', !isExpanded);
        menu.style.display = isExpanded ? 'none' : 'block';
        if (!isExpanded) {
            menuItems[0].focus();
        }
    });

    // Handle keyboard navigation
    dropdown.addEventListener('keydown', (e) => {
        const isExpanded = button.getAttribute('aria-expanded') === 'true';
        
        switch (e.key) {
            case 'Escape':
                if (isExpanded) {
                    button.setAttribute('aria-expanded', 'false');
                    menu.style.display = 'none';
                    button.focus();
                }
                break;
            case 'ArrowDown':
                if (!isExpanded) {
                    button.setAttribute('aria-expanded', 'true');
                    menu.style.display = 'block';
                    menuItems[0].focus();
                } else {
                    const activeIndex = Array.from(menuItems).indexOf(document.activeElement);
                    const nextIndex = (activeIndex + 1) % menuItems.length;
                    menuItems[nextIndex].focus();
                }
                e.preventDefault();
                break;
            case 'ArrowUp':
                if (isExpanded) {
                    const activeIndex = Array.from(menuItems).indexOf(document.activeElement);
                    const prevIndex = (activeIndex - 1 + menuItems.length) % menuItems.length;
                    menuItems[prevIndex].focus();
                }
                e.preventDefault();
                break;
        }
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target)) {
            button.setAttribute('aria-expanded', 'false');
            menu.style.display = 'none';
        }
    });
});
</script>

<?php
/*
 * USAGE NOTES:
 * 
 * 1. Table Structure:
 *    - Always use semantic table roles (role="table", role="grid", etc.)
 *    - Include descriptive aria-labels for the table and its sections
 *    - Use proper scope attributes for headers
 *    - Make cells focusable with tabindex="0" for keyboard navigation
 * 
 * 2. Action Menu:
 *    - Copy the entire .table-dropdown structure for action columns
 *    - Customize action items by modifying the menuitem divs
 *    - Available color classes: blue, green, red, black
 *    - Always include aria-labels for accessibility
 *    - Keep the SVG icons aria-hidden="true"
 * 
 * 3. Responsive Design:
 *    - Add class "responsive-hidden" to cells that should hide on mobile
 *    - Table automatically adjusts for smaller screens
 * 
 * 4. Error Handling:
 *    - Use error-message-row class for error messages
 *    - Use no-results class for empty state messages
 * 
 * 5. Accessibility Features:
 *    - Full keyboard navigation support
 *    - Screen reader friendly structure
 *    - High contrast mode support
 *    - Proper focus management
 *    - Clear action descriptions
 * 
 * 6. CSS Dependencies:
 *    - Requires gws-universal-branding.css for brand colors
 *    - All necessary styles are included above
 * 
 * 7. JavaScript:
 *    - Include the provided script for action menu functionality
 *    - Handles keyboard navigation and click events
 *    - Manages focus and ARIA states
 */
?>
