# Navigation Parameter Reference - Ticket System

## Template Header Parameter Mapping

This document provides the correct `template_admin_header()` parameters for each ticket system page to ensure proper navigation highlighting.

### Navigation Structure (from main.php)

**Main Menu Item:**
- `$selected == 'tickets'` - Highlights main "Tickets" menu item

**Sub-Menu Items:**
- `$selected == 'tickets' && $selected_child == 'view'` - View Tickets
- `$selected == 'tickets' && $selected_child == 'manage'` - View Comments  
- `$selected == 'tickets' && $selected_child == 'transfer'` - Import/Export Tickets
- `$selected == 'tickets' && $selected_child == 'category'` - Ticket Categories

### Correct Parameter Usage

| File | Parameters | Highlights |
|------|------------|------------|
| `index.php` | `('Dashboard', 'tickets')` | Main tickets menu |
| `tickets.php` | `('Tickets', 'tickets', 'view')` | View Tickets sub-menu |
| `ticket.php` | `($page . ' Ticket', 'tickets', 'view')` | View Tickets sub-menu |
| `categories.php` | `('Categories', 'tickets', 'category')` | Ticket Categories sub-menu |
| `category.php` | `($page . ' Category', 'tickets', 'category')` | Ticket Categories sub-menu |
| `comments.php` | `('Comments', 'tickets', 'manage')` | View Comments sub-menu |
| `comment.php` | `($page . ' Comment', 'tickets', 'manage')` | View Comments sub-menu |
| `tickets_import.php` | `('Import Tickets', 'tickets', 'transfer')` | Import/Export sub-menu |
| `tickets_export.php` | `('Export Tickets', 'tickets', 'transfer')` | Import/Export sub-menu |

### Parameter Format Rules

**Two Parameters:** `template_admin_header('Page Title', 'main_section')`
- Use for pages that should highlight only the main menu item
- Example: Dashboard pages, main application entry points

**Three Parameters:** `template_admin_header('Page Title', 'main_section', 'sub_section')`
- Use for pages that should highlight both main and sub-menu items
- Example: List pages, create/edit pages, specific feature pages

### Testing Navigation Highlighting

To verify correct navigation highlighting:

1. **Load each page** - Navigate to every ticket system page
2. **Check highlighting** - Verify the correct menu item is highlighted
3. **Verify breadcrumbs** - Ensure breadcrumb navigation matches
4. **Document issues** - Note any mismatched highlighting for correction

### Integration Checklist for Other Applications

When integrating new applications, ensure:

1. **Menu Structure Added** - Add navigation items to `admin/assets/includes/main.php`
2. **Parameters Defined** - Define `$selected` and `$selected_child` values
3. **Headers Updated** - Use correct parameters in all `template_admin_header()` calls
4. **Testing Completed** - Verify navigation highlighting on all pages

### Common Patterns

- **List/View Pages:** `('Page Name', 'app', 'view')`
- **Create/Edit Pages:** `('Create/Edit Item', 'app', 'manage')` or `('Item', 'app', 'view')`
- **Import/Export Pages:** `('Import/Export', 'app', 'transfer')`
- **Categories/Settings:** `('Categories', 'app', 'category')`
- **Dashboard:** `('Dashboard', 'app')`

This reference ensures consistent navigation behavior across all admin applications.
