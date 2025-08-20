# Gallery System PHP Files Update Guide

## Required Updates for Database Integration

After running the database updates, all gallery PHP files need these changes:

### 1. Table Name Updates
```sql
-- OLD → NEW
media → gallery_media
collections → gallery_collections  
media_collections → gallery_media_collections
media_likes → gallery_media_likes
```

### 2. Column Name Updates
```sql
-- OLD → NEW
acc_id → account_id
display_name → username
```

### 3. Common Query Pattern Updates

**Media queries:**
```php
// OLD:
SELECT m.*, a.email, a.display_name FROM media m LEFT JOIN accounts a ON a.id = m.acc_id

// NEW:
SELECT m.*, a.email, a.username FROM gallery_media m LEFT JOIN accounts a ON a.id = m.account_id
```

**Collections queries:**
```php
// OLD:
SELECT c.*, a.email, a.display_name FROM collections c LEFT JOIN accounts a ON a.id = c.acc_id

// NEW:
SELECT c.*, a.email, a.username FROM gallery_collections c LEFT JOIN accounts a ON a.id = c.account_id
```

**Media likes queries:**
```php
// OLD:
SELECT ml.*, a.display_name FROM media_likes ml LEFT JOIN accounts a ON a.id = ml.acc_id

// NEW:
SELECT ml.*, a.username FROM gallery_media_likes ml LEFT JOIN accounts a ON a.id = ml.account_id
```

**Media collections queries:**
```php
// OLD:
SELECT mc.* FROM media_collections mc

// NEW:
SELECT mc.* FROM gallery_media_collections mc
```

### 4. Files That Need Updates

**Already Updated:**
- ✅ `gallery_dash.php` - Partially updated (main queries done)
- ✅ All admin template headers updated

**Still Need Updates:**
- `allmedia.php` - All queries
- `collections.php` - All queries  
- `media.php` - All queries
- `collection.php` - All queries
- `likes.php` - All queries
- `settings.php` - Any queries
- `media_export.php` - All queries
- `media_import.php` - All queries
- `collections_export.php` - All queries
- `collections_import.php` - All queries
- `like.php` - All queries

### 5. Specific Search/Replace Patterns

**In ALL gallery system PHP files, replace:**

1. `FROM media ` → `FROM gallery_media `
2. `JOIN media ` → `JOIN gallery_media `
3. `INTO media ` → `INTO gallery_media `
4. `UPDATE media ` → `UPDATE gallery_media `
5. `DELETE FROM media ` → `DELETE FROM gallery_media `

6. `FROM collections ` → `FROM gallery_collections `
7. `JOIN collections ` → `JOIN gallery_collections `
8. `INTO collections ` → `INTO gallery_collections `
9. `UPDATE collections ` → `UPDATE gallery_collections `
10. `DELETE FROM collections ` → `DELETE FROM gallery_collections `

11. `FROM media_collections ` → `FROM gallery_media_collections `
12. `JOIN media_collections ` → `JOIN gallery_media_collections `
13. `INTO media_collections ` → `INTO gallery_media_collections `
14. `DELETE FROM media_collections ` → `DELETE FROM gallery_media_collections `

15. `FROM media_likes ` → `FROM gallery_media_likes `
16. `JOIN media_likes ` → `JOIN gallery_media_likes `
17. `INTO media_likes ` → `INTO gallery_media_likes `
18. `DELETE FROM media_likes ` → `DELETE FROM gallery_media_likes `

19. `.acc_id` → `.account_id`
20. `= acc_id` → `= account_id`
21. `acc_id =` → `account_id =`
22. `acc_id,` → `account_id,`
23. `(acc_id` → `(account_id`
24. `, acc_id` → `, account_id`

25. `.display_name` → `.username`
26. `display_name` → `username` (in SELECT clauses)

### 6. PHP Variable Updates

**In PHP code that processes results:**
```php
// OLD:
$row['display_name']

// NEW:  
$row['username']
```

```php
// OLD:
$media['acc_id']

// NEW:
$media['account_id']
```

### 7. Session Variable References

**Check for session usage:**
```php
// These should already be correct if using universal admin:
$_SESSION['account_id'] // Instead of $_SESSION['acc_id']
$_SESSION['username']   // Instead of $_SESSION['display_name']
```

Would you like me to proceed with updating all the remaining gallery system files with these changes?
