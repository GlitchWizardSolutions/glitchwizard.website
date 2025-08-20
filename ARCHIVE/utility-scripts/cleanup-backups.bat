@echo off
echo Moving backup and duplicate files...

REM Create backup files archive directory
mkdir "ARCHIVE\backup-files" 2>nul

REM Move backup files from public_html
cd public_html
move "canonical-plan.php.bak" "..\ARCHIVE\backup-files\"
move "admin\review_system\admin.js.bak" "..\ARCHIVE\backup-files\"
move "admin\review_system\admin.css.bak" "..\ARCHIVE\backup-files\"
move "admin\settings\page_update.php.bak" "..\ARCHIVE\backup-files\"
move "shop_system\sidebar.php.backup" "..\ARCHIVE\backup-files\"
move "admin\settings\branding-templates-enhanced.php.backup" "..\ARCHIVE\backup-files\"
move "admin\settings\settings_dash.php.backup" "..\ARCHIVE\backup-files\"
move "admin\polling_system\admin.js.backup" "..\ARCHIVE\backup-files\"
move "admin\polling_system\admin.css.backup" "..\ARCHIVE\backup-files\"
move "admin\invoice_system\invoice_table_transfer.php.backup" "..\ARCHIVE\backup-files\"
move "admin\invoice_system\admin.js.backup" "..\ARCHIVE\backup-files\"
move "admin\invoice_system\admin.css.backup" "..\ARCHIVE\backup-files\"

REM Move some empty/corrupt files from public_html
move "shop-cart.php" "..\ARCHIVE\empty-files\" 2>nul
move "shop-product.php" "..\ARCHIVE\empty-files\" 2>nul
move "ajax_toggle_newsletter.php" "..\ARCHIVE\empty-files\" 2>nul
move "canonical-plan.php.new" "..\ARCHIVE\empty-files\" 2>nul
move "test_reviews_integration.php" "..\ARCHIVE\debug-test-files\" 2>nul

cd ..

REM Move root backup files  
move "MASTER_INTEGRATION_CHECKLIST.php.backup" "ARCHIVE\backup-files\" 2>nul

echo Backup file cleanup complete!
