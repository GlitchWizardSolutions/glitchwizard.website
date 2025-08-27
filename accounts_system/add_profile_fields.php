<?php
/*
 * Add profile fields to accounts table
 * This script adds the missing profile fields if they don't exist
 */

require_once '../../private/gws-universal-config.php';

try {
    $pdo = new PDO('mysql:host=' . db_host . ';dbname=' . db_name, db_user, db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Adding profile fields to accounts table...\n";
    
    // Check if profile fields exist, if not add them
    $sql = "
    ALTER TABLE accounts
    ADD COLUMN IF NOT EXISTS phone VARCHAR(20) DEFAULT '',
    ADD COLUMN IF NOT EXISTS first_name VARCHAR(100) DEFAULT '',
    ADD COLUMN IF NOT EXISTS last_name VARCHAR(100) DEFAULT '',
    ADD COLUMN IF NOT EXISTS address_street VARCHAR(255) DEFAULT '',
    ADD COLUMN IF NOT EXISTS address_city VARCHAR(100) DEFAULT '',
    ADD COLUMN IF NOT EXISTS address_state VARCHAR(100) DEFAULT '',
    ADD COLUMN IF NOT EXISTS address_zip VARCHAR(20) DEFAULT '',
    ADD COLUMN IF NOT EXISTS address_country VARCHAR(100) DEFAULT 'United States'
    ";
    
    $pdo->exec($sql);
    echo "✓ Profile fields added successfully to accounts table.\n";
    
    // Update existing full_name data to populate first_name and last_name if possible
    $updateSql = "
    UPDATE accounts 
    SET 
        first_name = TRIM(SUBSTRING_INDEX(full_name, ' ', 1)),
        last_name = CASE 
            WHEN full_name LIKE '% %' THEN TRIM(SUBSTRING(full_name, LOCATE(' ', full_name) + 1))
            ELSE ''
        END
    WHERE (first_name = '' OR first_name IS NULL)
        AND full_name != ''
        AND full_name != 'None Provided'
    ";
    
    $result = $pdo->exec($updateSql);
    echo "✓ Updated {$result} existing records with first/last name data.\n";
    
    echo "\nProfile fields setup complete!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
