<?php
/*
 * AUGUST 13, 2025 - ADMIN TEMPLATE STANDARDIZATION GUIDE
 * 
 * This document contains all template standards and patterns established
 * during the admin interface standardization project.
 * 
 * TEMPLATE SOURCE: public_html/admin/accounts/account.php
 * 
 * =================================================================
 * TEMPLATE STANDARDIZATION ROLLOUT STATUS
 * =================================================================
 * 
 * COMPLETED FILES - COMMENT SYSTEM (August 14, 2025):
 * ✅ comments.php - COMPLETE STANDARDIZATION: Bootstrap 5 table structure, proper search form (row g-3 align-items-end), active filters badges, responsive table headers, professional table styling, proper pagination, actions dropdown with standard icons, status color coding (green/orange classes), avatar display, comment truncation, proper ARIA labels
 * ✅ filters.php - COMPLETE STANDARDIZATION: Bootstrap 5 table structure, professional search form, active filters badges, responsive table headers, code highlighting for word/replacement display, proper pagination, actions dropdown with FontAwesome icons, clean empty state display, proper ARIA labels and accessibility
 * 
 * CSS/JS CLEANUP - COMMENT SYSTEM (August 14, 2025):
 * ✅ admin.scss - DELETED (1,983 lines of unused standalone admin system)
 * ✅ comment-specific.css - DELETED (120 lines of unused filter/dropdown styles)
 * ✅ comment-specific.js - DELETED (unused filter functionality)
 * ✅ main.php.backup - DELETED (old admin system loader)
 * ✅ admin.css.backup - DELETED (48,998 bytes of legacy admin styles)
 * ✅ admin.js.backup - DELETED (4,874 bytes of legacy admin scripts)
 * TOTAL CLEANUP: ~113,000 bytes of dead code removed, system now runs on standardized Bootstrap framework only
 * 
 * COMPLETED FILES - BLOG SYSTEM (August 13, 2025):
 * ✅ posts.php - COMPLETE STANDARDIZATION: Single card structure, Bootstrap search form, proper button colors, table headers
 * ✅ account.php - Gold standard template source
 * ✅ accounts.php - Button colors corrected (btn-success for Apply Filters)
 * ✅ accounts_table_transfer.php - Final tabbed form solution implemented
 * ✅ account_dash.php - Spacing standardized (mb-4), card structure updated
 * ✅ documents.php - Button classes updated (btn-outline-secondary), spacing corrected
 * ✅ email_templates.php - Spacing standardized (mb-4 replacing mar-top-4)
 * ✅ roles.php - Spacing corrected, duplicate footer removed
 * 
 * COMPLETED FILES - POLLING SYSTEM (August 13, 2025):
 * ✅ polls.php - COMPLETE STANDARDIZATION: Duplicate header removed, button structure updated, CSS status classes applied, Actions column converted from Bootstrap dropdown to custom actions-btn
 * ✅ poll.php - COMPLETE STANDARDIZATION: Top and bottom button structure updated, proper button order (Cancel → Save → Delete), FontAwesome icons with me-1 spacing, mb-4 and pt-3 border-top styling
 * ✅ poll_categories.php - COMPLETE STANDARDIZATION: Single card structure, Bootstrap search form, button standardization (btn-outline-secondary for Add Category), proper table headers, responsive design, Bootstrap pagination, Actions column with custom actions-btn
 * ✅ polls_table_transfer.php - COMPLETE STANDARDIZATION: Four-tab structure maintained, col-md-6/col-md-6 layout (form fields / info notes), card header font size corrected, icon moved to content-title, Export buttons moved to bottom of each tab, proper form-control classes, simplified JavaScript
 * 
 * COMPLETED FILES - GALLERY SYSTEM (August 13, 2025):
 * ✅ gallery_table_transfer.php - COMPLETE STANDARDIZATION: Four-tab structure (Export Media, Import Media, Export Collections, Import Collections), col-md-6/col-md-6 layout implementation, card header h6 font size, proper form-control classes, simplified JavaScript, Bootstrap alert integration, comprehensive information sections
 * ✅ allmedia.php - COMPLETE STANDARDIZATION: Single card structure, Bootstrap search form, proper button colors (btn-outline-secondary for navigation), table headers, responsive design, card header h6 font size, proper spacing (mb-4)
 * ✅ media.php - COMPLETE STANDARDIZATION: Top and bottom button structure updated, proper button order (Cancel → Save → Delete), FontAwesome icons with me-1 spacing, mb-4 and pt-3 border-top styling, card header h6 font size, form structure standardized
 * ✅ collections.php - COMPLETE STANDARDIZATION: Single card structure, Bootstrap search form, proper button colors (btn-success for Create), table headers, responsive design, card header h6 font size, proper spacing (mb-4), search/filter form converted to Bootstrap row g-3 align-items-end layout
 * ✅ collection.php - COMPLETE STANDARDIZATION: Button placement fixed (moved above content-title, using form attribute), proper button order (Cancel → Save → Delete), card header h6 font size, duplicate button sections removed, proper spacing structure
 * ✅ likes.php - COMPLETE STANDARDIZATION: Single card structure, Bootstrap search form converted from old filter system, proper button colors (btn-success for Add Like), table headers, responsive design, card header h6 font size, proper spacing (mb-4), modern pagination structure matching template standards
 * 
 * COMPLETED FILES - REVIEW SYSTEM (August 13, 2025):
 * ✅ reviews_table_transfer.php - COMPLETE STANDARDIZATION: Two-tab structure (Export Reviews, Import Reviews), col-md-6/col-md-6 layout implementation, card header h6 font size, main card wrapper structure, proper form-control classes, bottom button placement, Bootstrap alert integration, comprehensive file format support
 * ✅ reviews.php - COMPLETE STANDARDIZATION: Single card structure, Bootstrap search form converted from old filter system, proper button colors (btn-success for Create Review), table headers, responsive design, card header h6 font size, proper spacing (mb-4), Reviews Dashboard button removed, status filter integration with search form, modern pagination structure matching template standards
 * 
 * COMPLETED FILES - TICKET SYSTEM (August 13, 2025):
 * ✅ tickets.php - COMPLETE STANDARDIZATION: Single card structure, spacing, button colors (btn-success, btn-outline-secondary)
 * ✅ ticket.php - COMPLETE STANDARDIZATION: Button structure, spacing, top/bottom buttons, proper icons (btn-success for Save, btn-outline-secondary for Cancel)
 * ✅ ticket_dash.php - Spacing standardized (mb-4)
 * ✅ email-templates.php - Spacing and button structure standardized
 * ✅ categories.php - COMPLETE STANDARDIZATION: Title structure, search form, button colors, card headers, spacing (mb-4)
 * ✅ category.php - Spacing and button structure standardized
 * ✅ settings.php - Spacing and button structure standardized
 * ✅ tickets_table_transfer.php - Complete tabbed form implementation using accounts template copying method
 * ✅ comment.php - Spacing standardized, button structure updated (btn-success for Create/Update, btn-outline-secondary for Cancel)
 * ✅ comments.php - COMPLETE STANDARDIZATION: Single card structure, spacing, search form, button colors (btn-success for Create)
 * 
 * STATUS: All accounts and ticket_system admin files now comply with established template standards.
 * ALL ADMIN PAGES now use the SINGLE CARD STRUCTURE standard for table listings.
 * 
 * SINGLE CARD STRUCTURE ROLLOUT COMPLETE (August 13, 2025):
 * ✅ tickets.php - Converted from dual card to single card structure
 * ✅ categories.php - Converted from dual card to single card structure  
 * ✅ comments.php - Converted from dual card to single card structure
 * ✅ accounts.php - Already using single card structure (gold standard)
 * ✅ Other admin pages verified to use appropriate single card or unique structures
 * 
 * KEY CHANGES APPLIED:
 * - Replaced all <br> tags with mb-4 Bootstrap utility classes
 * - Standardized button colors: btn-success for actions, btn-outline-secondary for navigation
 * - Corrected non-standard button classes (btn alt → btn btn-outline-secondary)  
 * - Fixed mar-top-4 → mb-4 conversions
 * - Maintained proper card structure and spacing
 * - Applied col-md-6/col-md-6 layouts where appropriate
 * - CRITICAL: Added mb-4 spacing divs between content-title and main content areas
 * - Ensured consistent template compliance across all admin interfaces
 * - Extended standardization to ticket_system directory using same patterns
 * - Applied email template tabbed form standards where applicable
 * 
 * TICKET SYSTEM NOTES:
 * - Applied same template standards as accounts directory
 * - Updated button colors to match established pattern (btn-success for actions)
 * - Fixed spacing issues (mb-4 replacing <br> tags and mar-top-4)
 * - tickets_table_transfer.php prepared for enhanced tabbed functionality if needed
 * - All ticket system admin pages now follow consistent template structure
 * 
 * LATEST UPDATE - TABBED FORM COPYING PROCESS (August 13, 2025):
 * ✅ tickets_table_transfer.php - Successfully created using exact copy method from accounts_table_transfer.php
 * 
 * TABBED FORM COPYING PROCESS (PROVEN METHOD):
 * When creating a new tabbed form file (like table transfer pages), follow this exact process:
 * 
 * 1. LOCATE WORKING TEMPLATE:
 *    - Find the working accounts_table_transfer.php file (544 lines)
 *    - This is the gold standard with proper tab initialization and functionality
 * 
 * 2. COPY ENTIRE FILE CONTENT:
 *    - Copy the complete file content exactly as-is
 *    - Do NOT attempt to recreate or modify structure during copying
 * 
 * 3. MAKE TARGETED REPLACEMENTS ONLY:
 *    - Change include path: '../assets/includes/main.php' (adjust path as needed)
 *    - Database operations: 'accounts' → 'tickets' (or target table name)
 *    - Variable names: '$accounts' → '$tickets' (or target variables)
 *    - File names: 'accounts.csv' → 'tickets.csv' (or target files)
 *    - Navigation links: 'accounts.php' → 'tickets.php' (or target pages)
 *    - Display text: 'Account' → 'Ticket' (or target entity)
 *    - XML elements: '<account>' → '<ticket>' (or target XML structure)
 *    - Date fields: Adjust field names as needed ('registered' → 'created', etc.)
 * 
 * 4. PRESERVE EXACT STRUCTURE:
 *    - Keep all JavaScript logic identical (especially tab initialization)
 *    - Keep all CSS styling identical
 *    - Keep all HTML structure and Bootstrap classes identical
 *    - Keep all form handling logic identical
 * 
 * 5. CRITICAL SUCCESS FACTORS:
 *    - The working accounts file has complex tab initialization that MUST be preserved
 *    - Any attempt to "improve" or recreate the JavaScript will break functionality
 *    - The tab-btn.active class and proper event handling are essential
 *    - All accessibility attributes and ARIA labels should be preserved
 * 
 * RESULT: Perfect working tabbed interface with proper tab selection on page load
 * 
 * This process ensures that complex functionality (like tab initialization) is preserved
 * while only changing the specific data and navigation elements needed for the new context.
 * 
 * COMPLETE STANDARDIZATION CHECKLIST (Updated August 13, 2025):
 * 
 * FOR EVERY ADMIN PAGE, VERIFY ALL OF THESE ELEMENTS:
 * 
 * 1. TITLE STRUCTURE (CRITICAL):
 *    ✅ Must have: <div class="content-title mb-4">
 *    ✅ Must have: <div class="title"> wrapper
 *    ✅ Must have: <div class="icon"> with 18x18 SVG
 *    ✅ Must have: <div class="txt"> with h2 and p
 *    ✅ Icon format: Multi-line SVG with proper viewBox and Font Awesome comment
 *    ❌ WRONG: Missing mb-4, missing title wrapper, single-line SVG
 * 
 * 2. SUCCESS MESSAGES:
 *    ✅ Must use: Bootstrap alert structure OR custom msg success
 *    ✅ Must have: 14x14 SVG icons (not bare <i> tags)
 *    ✅ Must have: Multi-line SVG format with Font Awesome comment
 *    ✅ Must have: close-success class (not close-error)
 * 
 * 3. SEARCH FORMS:
 *    ✅ Must use: Bootstrap form structure like accounts.php
 *    ✅ Must have: <div class="row g-3 align-items-end">
 *    ✅ Must have: form-label and form-control classes
 *    ✅ Must have: btn-success for "Apply Filters" button
 *    ✅ Must have: Clear button if search active
 *    ❌ WRONG: Old responsive-flex-column, search div with label
 * 
 * 4. BUTTON STRUCTURE (CRITICAL CORRECTIONS - August 13, 2025):
 *    ✅ Must use: btn-success for actions, btn-outline-secondary for navigation
 *    ✅ Must have: FontAwesome icons with me-1 spacing
 *    ✅ Must have: aria-hidden="true" on icons
 *    ✅ Text: "Add [Entity]" not just entity name
 *    ❌ WRONG: Duplicate button sections in forms (should only have external top + internal bottom)
 *    ❌ WRONG: List pages should NOT have bottom buttons (forms only)
 * 
 * 5. CARD HEADER STANDARDS (CORRECTED - August 13, 2025):
 *    ✅ Form Cards: <h6 class="card-header">Title</h6> (NOT h5 with mb-0)
 *    ✅ List Cards: <div class="card-header d-flex justify-content-between align-items-center">
 *                     <h6 class="mb-0">Title</h6>
 *                     <small class="text-muted">Count/Status</small>
 *                   </div>
 *    ❌ WRONG: h5 in card headers, missing proper structure
 * 
 * 6. CARD STRUCTURE (SINGLE CARD STANDARD - August 13, 2025):
 *    ✅ Must use: SINGLE card approach (like accounts.php and updated tickets.php)
 *    ✅ Must have: One card with header containing title and entity count
 *    ✅ Must have: Card body containing search form, optional separator, and table
 *    ✅ Must show: Entity count in header (e.g., "5 total categories")
 *    ✅ Must use: number_format() for counts
 *    ✅ Structure: Search form → Visual separator (if needed) → Table (all in same card body)
 *    ❌ WRONG: Multiple separate cards for search and table (old dual-card approach)
 * 
 * 6. SPACING:
 *    ✅ Must replace: All <br> tags with mb-4
 *    ✅ Must replace: mar-top-4 with mb-4
 *    ✅ Must replace: responsive-pad-bot-3 with mb-4
 *    ✅ Must add: mb-4 after content-title
 * 
 * 7. ACTIONS COLUMN (CRITICAL - August 13, 2025):
 *    ✅ Must use: Custom actions-btn class (NOT Bootstrap dropdown-toggle)
 *    ✅ Must have: <button class="actions-btn"> with SVG ellipsis icon
 *    ✅ Must use: <div class="table-dropdown-items"> structure for menu
 *    ✅ Must use: role="menuitem" for each dropdown item
 *    ✅ Must use: Color classes (blue, green, black) for action links
 *    ✅ Must have: Proper SVG icons in dropdown items (not FontAwesome <i> tags)
 *    ❌ WRONG: btn-outline-secondary dropdown-toggle (Bootstrap blue button)
 *    ❌ WRONG: dropdown-item class (Bootstrap dropdown structure)
 * 
 * ACTIONS BUTTON STRUCTURE (Gold Standard from accounts.php):
 * <button class="actions-btn" aria-haspopup="true" aria-expanded="false">
 *     <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="fill: #333333;">
 *         <path d="M8 256a56 56 0 1 1 112 0A56 56 0 1 1 8 256zm160 0a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm216-56a56 56 0 1 1 0 112 56 56 0 1 1 0-112z" />
 *     </svg>
 * </button>
 * 
 * USE accounts.php as the GOLD STANDARD for list pages
 * USE account.php as the GOLD STANDARD for edit/create forms
 * 
 * SPACING FIX PATTERN (Applied to roles.php and email_templates.php):
 * 
 * BEFORE (Incorrect):
 * </div>
 * </div>
 * 
 * <div class="card">           <!-- OR <form> or other content -->
 * 
 * AFTER (Correct):
 * </div>
 * </div>
 * 
 * <div class="mb-4">
 * </div>
 * 
 * <div class="card">           <!-- OR <form> or other content -->
 * 
 * All files now follow the account.php gold standard template structure with proper 
 * Bootstrap spacing, button colors, and layout patterns.
 * 
 * =================================================================
 * TEMPLATE STRUCTURE STANDARDS
 * =================================================================
 */

/*
 * 1. PAGE LAYOUT STRUCTURE
 * 
 * Standard order for all admin pages:
 * 
 * <div class="content-title mb-4">           <!-- Always has mb-4 spacing -->
 *     <div class="title">
 *         <div class="icon">                  <!-- 18x18 px icons -->
 *             <svg>...</svg>
 *         </div>
 *         <div class="txt">
 *             <h2>Page Title</h2>
 *             <p>Page description</p>
 *         </div>
 *     </div>
 * </div>
 * 
 * <!-- CRITICAL: Always add mb-4 spacing div after content-title -->
 * <div class="mb-4">
 * </div>
 * 
 * <!-- Success/Error Messages (conditional) -->
 * <?php if (isset($success_msg)): ?>
 *     <div class="mb-4" role="region" aria-label="Success Message">
 *         <!-- Message content -->
 *     </div>
 * <?php endif; ?>
 * 
 * <!-- Top Buttons (always mb-4 spacing) -->
 * <div class="d-flex gap-2 mb-4" role="region" aria-label="Page Actions">
 *     <!-- Buttons here -->
 * </div>
 * 
 * <!-- Main Content (Form or Card) -->
 * <form> or <div class="card">
 *     <!-- Content -->
 * </form> or </div>
 */

/*
 * 2. BUTTON STANDARDS
 * 
 * BUTTON ORDER (left to right):
 * 1. Cancel (always first)
 * 2. Primary Action (Save, Export, Import, etc.)
 * 3. Delete (always last, only in edit mode)
 * 
 * BUTTON STYLES:
 * 
 * Cancel Button:
 * <a href="parent_page.php" class="btn btn-outline-secondary">
 *     <i class="fas fa-arrow-left me-1" aria-hidden="true"></i>
 *     Cancel
 * </a>
 * 
 * Save Button:
 * <button type="submit" name="submit" class="btn btn-success">
 *     <i class="fas fa-save me-1" aria-hidden="true"></i>
 *     Save [Context]
 * </button>
 * 
 * Add/Create Button:
 * <a href="add_page.php" class="btn btn-outline-secondary">
 *     <i class="fas fa-plus me-1" aria-hidden="true"></i>
 *     Add [Item]
 * </a>
 * 
 * Apply Filters Button (same as submit buttons):
 * <button type="submit" class="btn btn-success">
 *     <i class="fas fa-filter me-1" aria-hidden="true"></i>
 *     Apply Filters
 * </button>
 * 
 * Delete Button:
 * <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('...')">
 *     <i class="fas fa-trash me-1" aria-hidden="true"></i>
 *     Delete [Item]
 * </button>
 * 
 * Export Button:
 * <button type="submit" form="export-form" name="export" class="btn btn-success">
 *     <i class="fas fa-download me-1" aria-hidden="true"></i>
 *     Export Data
 * </button>
 * 
 * Import Button:
 * <button type="submit" form="import-form" name="import" class="btn btn-success">
 *     <i class="fas fa-upload me-1" aria-hidden="true"></i>
 *     Import Data
 * </button>
 */

/*
 * 3. BUTTON PLACEMENT
 * 
 * FOR FORM PAGES:
 * - Top buttons: Cancel + Primary Action + Delete (if edit mode)
 * - Bottom buttons: Same as top (inside form, above closing </form>)
 * - Bottom spacing: "d-flex gap-2 pt-3 border-top mt-4"
 * 
 * FOR LIST PAGES:
 * - Top buttons only: Cancel + Add [Item]
 * - No bottom buttons
 * 
 * FOR TABBED FORMS:
 * - Top buttons: Cancel + Dynamic Action Button (changes with tab)
 * - Bottom buttons: Cancel + Tab-specific Action (inside each form)
 * - JavaScript required to update top button
 */

/*
 * 4. CARD STRUCTURE STANDARDS
 * 
 * List Page Cards:
 * <div class="card mb-3">
 *     <div class="card-header d-flex justify-content-between align-items-center">
 *         <h6 class="mb-0">Card Title</h6>
 *         <small class="text-muted">Status/Count info</small>
 *     </div>
 *     <div class="card-body">
 *         <!-- Content -->
 *     </div>
 * </div>
 * 
 * Form Cards:
 * <div class="card mb-3">
 *     <h6 class="card-header">Form Title</h6>
 *     <div class="card-body">
 *         <!-- Form content -->
 *     </div>
 * </div>
 */

/*
 * 5. TABLE STANDARDS (from accounts.php)
 * 
 * <div class="table-responsive">
 *     <table class="table table-hover" role="table" aria-label="Accounts List">
 *         <thead class="table-light">
 *             <tr role="row">
 *                 <th scope="col" class="text-start">Column</th>
 *                 <th scope="col" class="text-center">Actions</th>
 *             </tr>
 *         </thead>
 *         <tbody>
 *             <!-- Table rows -->
 *         </tbody>
 *     </table>
 * </div>
 * 
 * Empty State:
 * <tr role="row">
 *     <td colspan="X" class="text-center text-muted py-4">
 *         <i class="fas fa-icon fa-2x mb-2 d-block"></i>
 *         No items found. <a href="add.php">Create your first item</a>.
 *     </td>
 * </tr>
 */

/*
 * 6. SPACING STANDARDS
 * 
 * REPLACE ALL <br> TAGS WITH:
 * - mb-4: Major section spacing (between title, buttons, cards)
 * - mb-3: Form field spacing
 * - mb-2: Minor spacing within elements
 * - pt-3, mt-4: Bottom button spacing with border-top
 * 
 * CONTENT-TITLE SPACING:
 * - Always add "mb-4" class to content-title div
 * - This creates consistent spacing to buttons/content below
 */

/*
 * 7. FORM INPUT STANDARDS
 * 
 * AVOID INPUT FIELD ISSUES:
 * - Don't use autocomplete attributes that cause blue highlighting
 * - Prefer "family-name" over "given-name" behavior
 * - Use consistent CSS focus styles
 * - Test each input field for unwanted browser behavior
 * 
 * FIELDSET STRUCTURE:
 * <fieldset role="group" aria-labelledby="section-id">
 *     <legend id="section-id">Section Title</legend>
 *     <div class="row">
 *         <div class="col-md-6">
 *             <div class="mb-3">
 *                 <label for="field" class="form-label">Label</label>
 *                 <input type="text" id="field" name="field" class="form-control">
 *             </div>
 *         </div>
 *     </div>
 * </fieldset>
 */

/*
 * 8. TABBED FORM STANDARDS
 * 
 * STRUCTURE:
 * - Top buttons: Cancel + Dynamic button (updates with tab selection)
 * - Tab navigation with proper ARIA attributes
 * - Each tab contains its own form with bottom buttons
 * - JavaScript handles tab switching AND top button updates
 * 
 * JAVASCRIPT REQUIREMENTS:
 * function openTab(evt, tabName) {
 *     // 1. Hide all tab content
 *     // 2. Remove active class from all tabs
 *     // 3. Show selected tab content
 *     // 4. Add active class to selected tab
 *     // 5. UPDATE TOP BUTTON (text, form reference, name attribute)
 * }
 * 
 * TOP BUTTON DYNAMIC UPDATE:
 * - Must change text: "Export Data" vs "Import Data"
 * - Must change form attribute: form="export-form" vs form="import-form"
 * - Must change name attribute: name="export" vs name="import"
 * - Must change icon if different between tabs
 */

/*
 * 9. ICON STANDARDS
 * 
 * Common Icons:
 * - Cancel: fas fa-arrow-left
 * - Save: fas fa-save
 * - Add: fas fa-plus
 * - Delete: fas fa-trash
 * - Export: fas fa-download
 * - Import: fas fa-upload
 * - Edit: fas fa-edit
 * - View: fas fa-eye
 * 
 * Icon Sizing:
 * - Content-title: 18x18px
 * - Buttons: Default with "me-1" spacing
 * - Tables: 12x12px for action icons
 */

/*
 * 10. ACCESSIBILITY STANDARDS
 * 
 * - All buttons have aria-label for screen readers
 * - All form sections have proper fieldset/legend structure
 * - All tables have role="table" and proper headers
 * - All interactive elements have focus styles
 * - All icons have aria-hidden="true"
 * - All forms have proper aria-labelledby and aria-describedby
 */

/*
 * =================================================================
 * TEMPLATE APPLICATION CHECKLIST
 * =================================================================
 * 
 * FOR EACH ADMIN PAGE:
 * 
 * ☐ 1. Add mb-4 to content-title div
 * ☐ 2. Add mb-4 spacing div immediately after content-title closing tag
 * ☐ 3. Remove all <br> tags, replace with Bootstrap spacing
 * ☐ 4. Update button structure (order, styling, icons)
 * ☐ 5. Add top buttons with mb-4 spacing
 * ☐ 6. Add bottom buttons (forms only) with pt-3 border-top mt-4
 * ☐ 7. Update card headers to match accounts.php style
 * ☐ 8. Update table structure with table-responsive, table-hover
 * ☐ 9. Add empty states for tables
 * ☐ 10. Fix any input field autocomplete issues
 * ☐ 11. For tabbed forms: Add JavaScript to update top button
 * ☐ 12. Test all button functionality and form submissions
 * ☐ 13. Verify proper spacing throughout page
 * 
 * CRITICAL SPACING CHECK:
 * - After every content-title block, ensure there's a spacing div:
 *   </div>
 *   </div>
 *   
 *   <div class="mb-4">
 *   </div>
 *   
 *   <!-- Next content starts here -->
 * 
 * =================================================================
 * DIRECTORIES TO UPDATE (in order)
 * =================================================================
 * 
 * 1. ✅ public_html/admin/accounts/ (COMPLETED)
 *    - accounts.php (main list)
 *    - account.php (template source)
 *    - accounts_table_transfer.php (tabbed forms)
 * 
 * 2. public_html/admin/blog/
 * 3. public_html/admin/shop_system/
 * 4. public_html/admin/client_portal/
 * 5. public_html/admin/ (main admin files)
 * 
 * =================================================================
 * SPECIAL CASES TO REMEMBER
 * =================================================================
 * 
 * - Tabbed forms need dynamic top button updates
/*
 * =================================================================
 * TABBED FORM SOLUTION (Final Implementation)
 * =================================================================
 * 
 * PROBLEM SOLVED: Tabbed forms with dynamic functionality
 * FILE EXAMPLE: accounts_table_transfer.php
 * 
 * FINAL SOLUTION STRUCTURE:
 * 
 * 1. TOP BUTTONS - Simplified (no dynamic buttons):
 *    <div class="d-flex gap-2 mb-4">
 *        <a href="parent_page.php" class="btn btn-outline-secondary">Cancel</a>
 *        <!-- NO dynamic top button - use bottom buttons only -->
 *    </div>
 * 
 * 2. CARD WITH TABS:
 *    <div class="card">
 *        <div class="card-header">...</div>
 *        <div class="card-body" style="padding: 0;">
 *            
 *            <!-- Tab Navigation -->
 *            <div class="tab-nav" role="tablist">
 *                <button class="tab-btn active" onclick="openTab(event, 'tab1-id')">Tab 1</button>
 *                <button class="tab-btn" onclick="openTab(event, 'tab2-id')">Tab 2</button>
 *            </div>
 * 
 *            <!-- Tab Content 1 -->
 *            <div id="tab1-id" class="tab-content active" style="padding: 1rem;">
 *                <form action="" method="post" id="form1-id">
 *                    <div class="row">
 *                        <div class="col-md-6"><!-- Form fields --></div>
 *                        <div class="col-md-6"><!-- Information box --></div>
 *                    </div>
 *                    <!-- Bottom buttons -->
 *                    <div class="d-flex gap-2 pt-3 border-top mt-4">
 *                        <a href="parent.php" class="btn btn-outline-secondary">Cancel</a>
 *                        <button type="submit" name="action1" class="btn btn-success">Action Button</button>
 *                    </div>
 *                </form>
 *            </div>
 * 
 *            <!-- Tab Content 2 -->
 *            <div id="tab2-id" class="tab-content" style="padding: 1rem;">
 *                <form action="" method="post" id="form2-id">
 *                    <div class="row">
 *                        <div class="col-md-6"><!-- Form fields --></div>
 *                        <div class="col-md-6"><!-- Information box --></div>
 *                    </div>
 *                    <!-- Bottom buttons -->
 *                    <div class="d-flex gap-2 pt-3 border-top mt-4">
 *                        <a href="parent.php" class="btn btn-outline-secondary">Cancel</a>
 *                        <button type="submit" name="action2" class="btn btn-success">Action Button</button>
 *                    </div>
 *                </form>
 *            </div>
 *        </div>
 *    </div>
 * 
 * 3. REQUIRED CSS:
 *    .tab-nav { display: flex; border-bottom: 2px solid #dee2e6; }
 *    .tab-btn { background: #f8f9fa; border: 2px solid #dee2e6; padding: 12px 20px; cursor: pointer; }
 *    .tab-btn.active { background: white; color: #007bff; border-bottom: 2px solid transparent; }
 *    .tab-content { display: none; }
 *    .tab-content.active { display: block; }
 * 
 * 4. REQUIRED JAVASCRIPT:
 *    function openTab(evt, tabName) {
 *        // Hide all tab content
 *        var tabcontent = document.getElementsByClassName("tab-content");
 *        for (var i = 0; i < tabcontent.length; i++) {
 *            tabcontent[i].style.display = "none";
 *            tabcontent[i].classList.remove("active");
 *        }
 *        // Remove active from all buttons
 *        var tablinks = document.getElementsByClassName("tab-btn");
 *        for (var i = 0; i < tablinks.length; i++) {
 *            tablinks[i].classList.remove("active");
 *        }
 *        // Show selected tab and activate button
 *        document.getElementById(tabName).style.display = "block";
 *        document.getElementById(tabName).classList.add("active");
 *        if (evt && evt.currentTarget) {
 *            evt.currentTarget.classList.add("active");
 *        }
 *    }
 * 
 * KEY POINTS:
 * - NO dynamic top buttons (too complex, scope issues)
 * - Each tab has its own complete form with bottom buttons
 * - Use col-md-6/col-md-6 for balanced layout (form fields / info boxes)
 * - First tab should have "active" class in HTML
 * - Simple JavaScript for tab switching only
 * - Each form is self-contained and works independently
 * 
 */

/*
 * =================================================================
 * ADDITIONAL LEARNINGS AND SOLUTIONS
 * =================================================================
 * 
 * BUTTON COLOR CONSISTENCY:
 * - Apply Filters buttons should use btn-success (same as submit buttons)
 * - DO NOT use btn-secondary for action buttons (not brand colors)
 * - Brand colors: btn-success for actions, btn-outline-secondary for navigation
 * 
 * FORM SCOPE ISSUES SOLVED:
 * - Problem: Top buttons outside cards cannot reference forms inside tabs
 * - Solution: Use bottom buttons only for tabbed forms
 * - Avoid: Dynamic top button form attribute changes (JavaScript scope issues)
 * 
 * COLUMN LAYOUT STANDARDS:
 * - Tabbed forms: col-md-6 / col-md-6 for balanced layout
 * - Form fields: Left column (adequate space for inputs)
 * - Information boxes: Right column (supporting content)
 * 
 * POLLS.PHP ACCESSIBILITY FIXES (August 13, 2025):
 * - PROBLEM: Grey text on grey background (accessibility violation)
 * - SOLUTION: Converted answer options to Bootstrap badges with proper contrast
 * - PROBLEM: Poor column spacing between status and created date
 * - SOLUTION: Applied proper table padding and responsive classes
 * - PROBLEM: Unreadable answer options with vote counts
 * - SOLUTION: Bootstrap badge system with clear vote count display
 * - APPLIED: Single card structure with professional table styling
 * - APPLIED: Bootstrap dropdown actions with proper ARIA labels
 * - APPLIED: Professional status badges (bg-success, bg-warning, bg-secondary, bg-info)
 * - RESULT: Fully accessible, professional polling system admin interface
 * 
 * STATUS AND PRIORITY COLUMN COLORING STANDARDS (August 13, 2025):
 * - USE CSS CLASSES like accounts.php (NOT Bootstrap badges for status/priority)
 * - STATUS COLORS: <span class="green">Active</span>, <span class="orange">Pending</span>, <span class="red">Closed</span>, <span class="grey">Other</span>
 * - PRIORITY COLORS: <span class="red">High</span>, <span class="orange">Medium</span>, <span class="green">Low</span>
 * - ORANGE COLOR: Use "orange" class instead of yellow/warning for medium priority and pending status
 * - APPROVED COLUMN EXAMPLE: <?=$item['approved'] ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'?>
 * - ACTIONS COLUMN: MUST be centered (text-center) for both header and content
 * - ACTIONS DROPDOWN: Use old table-dropdown structure like accounts.php (NOT Bootstrap dropdown)
 * - ACTIONS ICON: Use horizontal ellipsis (fa-ellipsis-h or equivalent SVG), NOT vertical
 * 
 * SEARCH FORM FILTER STANDARDS:
 * - COPY EXACT FILTER STRUCTURE from accounts.php
 * - Multiple filters required: search, role/category, status, priority, date ranges
 * - Each system should have contextually appropriate filters
 * - DO NOT remove existing filters during updates
 * - Maintain all filtering functionality that existed before
 * 
 * CSS/JAVASCRIPT DEBUGGING LESSONS:
 * - Always use !important for critical display rules in complex layouts
 * - Class name mismatches cause silent failures (tab-btn vs tab-button)
 * - Extensive console logging helps identify scope/execution issues
 * - Simplified functions work better than complex dynamic behaviors
 * 
 * TEMPLATE COMPLIANCE VERIFICATION:
 * - mb-4 spacing between content-title and buttons
 * - mb-4 spacing between buttons and main content
 * - Consistent button order: Cancel → Action → Delete
 * - Proper ARIA labels and accessibility attributes
 * 
 * =================================================================
 * CSS/JS REDUNDANCY ANALYSIS PROCESS (August 14, 2025)
 * =================================================================
 * 
 * SYSTEMATIC CLEANUP METHODOLOGY FOR ALL ADMIN SYSTEMS:
 * 
 * STEP 1 - INVENTORY ASSESSMENT:
 * - Use list_dir to catalog all CSS/JS files in each admin system directory
 * - Identify file sizes and creation dates for prioritization
 * - Look for patterns: admin.css, admin.scss, system-specific.css, backup files
 * 
 * STEP 2 - LOADING STRUCTURE ANALYSIS:
 * - Check main.php or template header function for what CSS/JS is actually loaded
 * - Verify Bootstrap 5.3.3 and standardized admin.css are the primary stylesheets
 * - Use grep_search to find CSS/JS references in PHP files
 * - Search for: "link href", "script src", ".css", ".js" patterns
 * 
 * STEP 3 - CONTENT COMPARISON:
 * - Read large CSS/SCSS files to identify custom admin systems vs Bootstrap integration
 * - Look for variables like $font, $header-size, custom layout systems
 * - Check for table styling, filter components, dropdown menus
 * - Compare with standardized admin.css (2,242 lines) for overlaps
 * 
 * STEP 4 - CONFLICT DETECTION:
 * - Use grep_search for Bootstrap conflicts: "bootstrap", "card", "table", "btn-"
 * - Search for custom variables and layout systems that override standards
 * - Identify filter lists, dropdown menus, custom admin layouts
 * - Check for standalone admin systems (typically 1,500+ lines)
 * 
 * STEP 5 - USAGE VERIFICATION:
 * - Search entire system directory for CSS class references
 * - Check if custom JavaScript functions are called anywhere
 * - Verify backup files are truly unused (check .backup, .old extensions)
 * - Confirm main.php.backup vs current main.php differences
 * 
 * STEP 6 - SAFE DELETION CRITERIA:
 * - Files not referenced in any PHP include/require statements
 * - CSS/JS that duplicates Bootstrap 5.3.3 functionality
 * - Backup files (.backup, .old) that are outdated
 * - Standalone admin systems replaced by standardized framework
 * - Custom styling that conflicts with August 13 template standards
 * 
 * COMMENT SYSTEM CLEANUP RESULTS (August 14, 2025):
 * - DELETED: admin.scss (1,983 lines) - Complete unused admin system
 * - DELETED: comment-specific.css (120 lines) - Unused filter/dropdown styles
 * - DELETED: comment-specific.js - Unused filter functionality
 * - DELETED: main.php.backup - Old admin system loader
 * - DELETED: admin.css.backup (48,998 bytes) - Legacy admin styles
 * - DELETED: admin.js.backup (4,874 bytes) - Legacy admin scripts
 * - TOTAL REMOVED: ~113,000 bytes of unused code
 * - RESULT: System functions perfectly with standardized Bootstrap admin framework
 * 
 * REPLICATION INSTRUCTIONS:
 * - Apply this 6-step process to ALL admin system directories
 * - Priority targets: blog_system, gallery_system, polling_system, ticket_system
 * - Look for similar patterns: large SCSS files, *-specific.css, backup files
 * - Always verify current loading structure before deletion
 * - Document removed files and byte savings for project metrics
 * 
 */

/*
 * =================================================================
 * SPECIAL CASES TO REMEMBER (Updated)
 * =================================================================
 * 
 * - Some forms may have "Return to Dashboard" - change to "Cancel"
 * - Export/Import buttons need proper form attribute targeting
 * - Context-specific button text is preferred over generic "Save"
 * - Delete buttons only appear in edit mode
 * - Add buttons only appear on list pages
 * - For tabbed interfaces: Use the FINAL SOLUTION above (no dynamic top buttons)
 * - Apply Filters buttons: Use btn-success (brand color consistency)
 * - Form scope issues: Keep forms and buttons in same container level
 * 
 */
?>
