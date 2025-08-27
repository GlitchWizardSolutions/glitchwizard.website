# Content Schema Inventory

Date: 2025-08-20

## New Normalized Tables

1. `content_items`
   - Purpose: Unified storage of features, services, pages (and future hero/misc) items.
   - Key Columns: area, slug, title, body, position, active
   - Unique Constraint: (area, slug)
   - Seeded Slugs: feature-1..feature-4, service-1..service-6, about, services, portfolio, contact, privacy, terms

2. `pages_metadata`
   - Purpose: Meta title & description per page slug.
   - Relationship: 1:1 with `content_items` rows where area='page'.

## Existing Specialized Table (kept for now)

* `setting_content_homepage` (hero fields + section titles/descriptions). Candidate for partial migration later (hero_* into content_items).

## Legacy File-Based Config (To Deprecate/Remove After Migration)

| File | Status | Replacement |
|------|--------|-------------|
| `assets/includes/settings/sections_content_settings.php` | Legacy (array) | `content_items` (area in ['feature','service']) |
| `assets/includes/settings/pages_content_settings.php` | Legacy (array) | `content_items` + `pages_metadata` |
| `assets/includes/settings/home_content_settings.php` | Empty legacy | `setting_content_homepage` (current) / future `content_items` |
| `assets/includes/settings/media_content_settings.php` | Empty legacy | TBD (possible future media library table) |
| `assets/includes/content-vars.php` | Generated legacy variables | Direct DB queries (deprecate) |

## Migration Steps Remaining

1. Update admin Sections tab to read/write `content_items` (feature/service rows) instead of legacy array file.
2. Update admin Pages tab to read/write `content_items` + `pages_metadata`.
3. Remove generation of `content-vars.php`; adapt frontend includes to query DB.
4. After verification, archive or delete legacy files (retain in git history).
5. (Optional) Migrate hero fields into `content_items` (area='hero').

## Cleanup Candidates After Full Cutover

* Remove legacy `*_content_settings.php` files.
* Remove `content-vars.php` generation logic.
* Consider dropping unused columns in `setting_content_homepage` once hero moves.

## Notes

* Idempotent seed inserts ensure rerunning the schema script is safe.
* Normalized approach supports arbitrary counts and future additions (e.g., feature-5) with simple INSERT.
* Add indexes later for page lookups by slug if access patterns expand.
