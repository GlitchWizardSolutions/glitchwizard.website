# Content Settings Refactor Plan (Next Release)

Objective: Improve usability, testing speed, and maintainability by splitting the monolithic `content_settings.php` into focused, self‑contained modules while preserving today’s stable release.

## Current Pain Points
- Large scrollable form increases cognitive load.
- Mixed concerns (home, sections, media, pages) slow QA.
- Image upload lacks deterministic naming and selection UI.
- Variables written as globals via `content-vars.php` (harder to trace provenance).

## Target End State
Four focused settings pages + shared library:
- `settings_content_home.php`: Hero title, subtitle, hero text, CTA fields, about/mission/vision blocks.
- `settings_content_sections.php`: Services (service1–6), Features (feature1–4) with Summernote editors.
- `settings_content_media.php`: Managed assets (preview, upload, overwrite/version strategy, validation, future alt-text fields).
- `settings_content_pages.php`: Static pages (About, Services, Portfolio, Contact, Privacy, Terms). Either tabs OR dynamic `?page=about` parameter.
- `content_settings_lib.php`: Shared load/save, file path helpers, sanitization, deterministic asset naming, rebuild of `content-vars.php` (or replacement array cache + compatibility shim).

## Navigation Integration
- Content tab already added to Business Configuration card. Buttons will point to individual pages once created.
- Each new page includes a consistent header + Back to Dashboard button.

## Image Handling Strategy
1. Deterministic canonical filenames (e.g. `assets/img/hero/hero_main.jpg`).
2. On upload: validate extension (jpg|jpeg|png|webp|mp4), size limit (e.g. 2MB images, 25MB video), MIME sniff.
3. Optional automatic resize/compress (hook via existing `image_helper.php`).
4. Provide “Replace” and “Select Existing” (directory scan) actions.
5. Future: Alt text + caption fields stored alongside in settings arrays.

## Data Flow Changes
Current: Each category saved into `.../settings/*_content_settings.php` + aggregated into global `content-vars.php`.
Refactor: Library returns associative master array; `content-vars.php` generation retained for transition; later replaced with `include content_settings_cache.php` returning array.

## Incremental Implementation Steps
Phase 1 (Post-release):
  - Extract library (`content_settings_lib.php`): functions loadContentSettings($categories), saveContentCategory($category, $data), rebuildContentVars().
  - Create `settings_content_home.php` using lib; keep old monolith.
  - Point Home button to new page; test parity.

Phase 2:
  - Extract Sections + Media pages (add deterministic folders: `assets/img/sections/`, `assets/img/media/`).
  - Implement upload validation + overwrite policy.

Phase 3:
  - Extract Pages (either multi-tab or dynamic param). Add per-page meta field grouping.
  - Deprecate equivalent portions in monolith (comments + warnings) then remove file.

Phase 4:
  - Replace globals approach: generate a cached array file; create backwards shim that defines legacy globals from the array until codebase fully migrated.
  - Add optional per-page completion tracking for dashboard insights.

## Backward Compatibility & Rollback
- Keep original `content_settings.php` until all new pages validated.
- Simple flag `$USE_NEW_CONTENT_UI` could toggle (default true after Phase 2).
- Rollback = switch links back to original monolith.

## Security & Validation Enhancements
- CSRF token reuse from existing admin pattern.
- Strict allowlist for image/video types.
- Central sanitization per field type (text, textarea, summernote HTML cleaning, email, URL).

## Testing Checklist (Per Extracted Page)
- Load with existing data -> all fields populated identically.
- Save without changes -> idempotent (no diff aside from timestamp).
- Save with modifications -> reflected in `content-vars.php`.
- Upload image -> file at canonical path; old version archived or overwritten.
- Invalid file type -> graceful error alert.

## Acceptance Criteria
- All four new pages functional and accessible from dashboard.
- Old monolith removed (or hard-redirect) after parity verification.
- No missing variables needed by front-end templates.
- Deterministic image naming + validation in place.

## Deferred / Nice-to-Have
- Version history per content field.
- Inline diff viewer before save.
- Drag-and-drop reordering of service/feature blocks.
- Media library modal with search + filters.

Prepared: 2025-08-20
