@echo off
echo Final cleanup pass...

REM Move remaining markdown files
for %%f in (*.md) do (
    echo Moving %%f to documentation archive...
    move "%%f" "ARCHIVE\documentation-md-files\" 2>nul
)

REM Move remaining debug/test PHP files  
move "analyze_fonts.php" "ARCHIVE\debug-test-files\" 2>nul
move "check_about_fields.php" "ARCHIVE\debug-test-files\" 2>nul
move "check_branding_table.php" "ARCHIVE\debug-test-files\" 2>nul
move "check_db_structure.php" "ARCHIVE\debug-test-files\" 2>nul
move "check_missing_vars.php" "ARCHIVE\debug-test-files\" 2>nul
move "check_table_structure.php" "ARCHIVE\debug-test-files\" 2>nul
move "check_team_pricing_tables.php" "ARCHIVE\debug-test-files\" 2>nul
move "check_testimonials.php" "ARCHIVE\debug-test-files\" 2>nul
move "database_content_mapping.php" "ARCHIVE\debug-test-files\" 2>nul
move "database_diagnostic.php" "ARCHIVE\debug-test-files\" 2>nul
move "debug_database_variables.php" "ARCHIVE\debug-test-files\" 2>nul
move "debug_footer_values.php" "ARCHIVE\debug-test-files\" 2>nul
move "debug_homepage_content_update.php" "ARCHIVE\debug-test-files\" 2>nul
move "debug_logo_files.php" "ARCHIVE\debug-test-files\" 2>nul
move "debug_logo_path.php" "ARCHIVE\debug-test-files\" 2>nul
move "debug_media.php" "ARCHIVE\debug-test-files\" 2>nul
move "debug_team_data.php" "ARCHIVE\debug-test-files\" 2>nul
move "quick_test.php" "ARCHIVE\debug-test-files\" 2>nul
move "simple_test.php" "ARCHIVE\debug-test-files\" 2>nul
move "template-testing.php" "ARCHIVE\debug-test-files\" 2>nul
move "test_about_alt.php" "ARCHIVE\debug-test-files\" 2>nul
move "test_database_loading.php" "ARCHIVE\debug-test-files\" 2>nul
move "test_pdo_connection.php" "ARCHIVE\debug-test-files\" 2>nul
move "test_testimonials.php" "ARCHIVE\debug-test-files\" 2>nul
move "test_variables.php" "ARCHIVE\debug-test-files\" 2>nul
move "what-is-left-clean.php" "ARCHIVE\debug-test-files\" 2>nul
move "what-is-left.php" "ARCHIVE\debug-test-files\" 2>nul
move "todo.php" "ARCHIVE\debug-test-files\" 2>nul

REM Move remaining empty/corrupt files
move "execute()" "ARCHIVE\empty-files\" 2>nul
move "execute([2])" "ARCHIVE\empty-files\" 2>nul
move "getMessage()" "ARCHIVE\empty-files\" 2>nul

REM Move backup files
move "MASTER_INTEGRATION_CHECKLIST.php.backup" "ARCHIVE\backup-files\" 2>nul

REM Move utility scripts to separate folder
mkdir "ARCHIVE\utility-scripts" 2>nul
move "populate_database.php" "ARCHIVE\utility-scripts\" 2>nul
move "populate_real_content.php" "ARCHIVE\utility-scripts\" 2>nul
move "populate_team.php" "ARCHIVE\utility-scripts\" 2>nul
move "populate_testimonials.php" "ARCHIVE\utility-scripts\" 2>nul
move "update-database.php" "ARCHIVE\utility-scripts\" 2>nul
move "update_database_branding.php" "ARCHIVE\utility-scripts\" 2>nul
move "update_homepage_content.php" "ARCHIVE\utility-scripts\" 2>nul
move "update_logo_system.php" "ARCHIVE\utility-scripts\" 2>nul
move "update_real_data.php" "ARCHIVE\utility-scripts\" 2>nul
move "run-database-update.php" "ARCHIVE\utility-scripts\" 2>nul
move "setup-enhanced-branding-system.php" "ARCHIVE\utility-scripts\" 2>nul
move "phase1_migration_helper.php" "ARCHIVE\utility-scripts\" 2>nul
move "migrate_footer_links.php" "ARCHIVE\utility-scripts\" 2>nul
move "extract_brand_colors.php" "ARCHIVE\utility-scripts\" 2>nul
move "create_brand_colors_css.php" "ARCHIVE\utility-scripts\" 2>nul
move "fix_database_variables.php" "ARCHIVE\utility-scripts\" 2>nul
move "fix_services_and_organize_branding.php" "ARCHIVE\utility-scripts\" 2>nul
move "table_name_updater.php" "ARCHIVE\utility-scripts\" 2>nul
move "download_images.php" "ARCHIVE\utility-scripts\" 2>nul

echo Final cleanup complete!
