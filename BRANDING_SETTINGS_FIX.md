# Branding Settings Include Path Fix

## Issue
The `branding_settings.php` file was trying to include non-existent header and footer files:
- `../assets/includes/header.php` 
- `../assets/includes/footer.php`

This caused PHP warnings:
```
Warning: include_once(../assets/includes/header.php): Failed to open stream: No such file or directory
Warning: include_once(../assets/includes/footer.php): Failed to open stream: No such file or directory
```

## Root Cause
The admin system uses template functions instead of separate header/footer include files. Other admin settings files use:
- `template_admin_header()` function for HTML structure
- `template_admin_footer()` function for closing HTML

## Solution Applied

### Before:
```php
include_once '../assets/includes/header.php';
?>
<style>
...
<?php include_once '../assets/includes/footer.php'; ?>
```

### After:
```php
// Call the admin header template
echo template_admin_header('Branding Settings', 'settings');
?>
<style>
...
<?php echo template_admin_footer(); ?>
```

## Files Modified
- `public_html/admin/settings/branding_settings.php`
  - Fixed header include on line 272
  - Fixed footer include on line 850
  - Ensured proper PHP block structure

## Verification
✅ PHP syntax check passed  
✅ No PHP errors or warnings when loading  
✅ Proper integration with admin template system  
✅ Consistent with other admin settings files  

## Result
The branding settings page now loads properly without any include path errors and follows the same template structure as other admin settings pages.
