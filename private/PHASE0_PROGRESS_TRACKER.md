# Phase 0 Incremental Settings Refactor Progress
Date: 2025-08-20

This file persists the active refactor context so work can resume even if chat/session is lost.

## Completed (Phase 0 scope)
- Removed Font Awesome (replaced with Bootstrap Icons)
- Archived orphan files in /private
- Added SecurityHelper (CSRF + validation utilities)
- Refactored SEO settings form (central validation + CSRF)
- Refactored Account/Security settings form
- Refactored Contact (business) settings form + separated business vs team social concept
- Added business social media dedicated methods in SettingsManager
- Added fallback + JSON safety helpers (safeJsonEncode / safeJsonDecode)
- Created Team Member Social stub form
- Created Social Media Data Model doc
- Branding Colors incremental form exists (needs validation sync)

## In Progress
- Splitting oversized branding/blog forms into small, single-purpose forms
- Field-to-column mapping matrix (pending generation)
- Production Minimal Build (hide deferred modules via feature flags)

## Pending (High Priority)
1. Blog Identity form: author_name, author_bio, default_author_id (move out of branding)
2. Blog Display form: show_author, show_date, show_categories, show_tags, show_excerpt
3. Generate settings_form_field_matrix.md mapping UI -> DB columns; flag unused & duplicates
4. Remove/Hide unused legacy inputs (TBD after matrix)
5. Add diagnostics page listing table/column presence & mismatches
6. Add per-section save success indicators in dashboard
7. Add optional JSON column graceful degrade (skip JSON write if column missing) – partial via safe helpers
8. Ensure navigation & dashboard honor feature flags (shop, landing_pages, invoice, documents, chat, review disabled) – PARTIAL (nav done) – need dashboard adjustments

## Deferred / Optional
- Expose extended social platforms (YouTube, TikTok, etc.) – NOT now (time crunch)
- Team member CRUD full implementation
- Rich editor integration improvements (Summernote already present)
- Accessibility pass on new mini-forms
- Reintegration of deferred modules (shop, invoice, review, chat, documents, landing_pages) post initial release

## Notes About Production JSON Errors
Likely due to CHECK (json_valid(...)) constraints + MySQL version or sql_mode differences. Mitigation path: validate JSON before write (now done) and allow empty fallback. If production still errors, consider ALTER TABLE to drop JSON CHECK temporarily.

## Next Action When Resume
Implement Blog Identity mini-form + update SettingsManager getter/setter naming consistency. Then generate mapping matrix. After that adjust settings dashboard to compute completion only for enabled modules.

-- End of Tracker --
