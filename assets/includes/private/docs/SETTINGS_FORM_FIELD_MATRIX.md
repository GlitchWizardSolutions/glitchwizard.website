# Settings Form Field → Database Mapping Matrix (Phase 0)

Purpose: Provide a single authoritative reference showing how each current (or planned) settings form field maps to database tables/columns via `SettingsManager` methods. Identifies deprecated / unused UI fields and highlights gaps. Used to drive safe cleanup, dashboard accuracy, and future migrations.

Legend:
- STATUS: Active | Planned | Deprecated | Orphan (has UI but no DB) | Missing (DB column exists, no UI)
- SCOPE: Enabled (module feature flag true) | Disabled (flag false)
- NOTES column clarifies special mappings, transformations, or fallbacks.

## 1. Business / Branding

### 1.1 Branding Colors (Incremental Form `branding_colors_form.php`)
| UI Field | Table.Column | STATUS | NOTES |
|----------|--------------|--------|-------|
| brand_primary_color | setting_branding_colors.brand_primary_color | Active | Fallback to `getFallbackBrandingConfig()` if none |
| brand_secondary_color | setting_branding_colors.brand_secondary_color | Active |  |
| brand_tertiary_color | setting_branding_colors.brand_tertiary_color | Active |  |
| brand_quaternary_color | setting_branding_colors.brand_quaternary_color | Active |  |
| brand_accent_color | setting_branding_colors.brand_accent_color | Active |  |
| brand_warning_color | setting_branding_colors.brand_warning_color | Active |  |
| brand_danger_color | setting_branding_colors.brand_danger_color | Active |  |
| brand_info_color | setting_branding_colors.brand_info_color | Active |  |
| brand_background_color | setting_branding_colors.brand_background_color | Active |  |
| brand_text_color | setting_branding_colors.brand_text_color | Active |  |
| brand_text_light | setting_branding_colors.brand_text_light | Active |  |
| brand_text_muted | setting_branding_colors.brand_text_muted | Active |  |

(Additional branding identity/logo fields not yet broken out into incremental form will be captured in a later section when refactored.)

### 1.2 Business Contact (`forms/business_contact_form.php`)
| UI Field | Table.Column | STATUS | NOTES |
|----------|--------------|--------|-------|
| primary_email | setting_contact_info.contact_email | Active | Required validation |
| primary_phone | setting_contact_info.contact_phone | Active |  |
| primary_address | setting_contact_info.contact_address | Active |  |
| city | setting_contact_info.contact_city | Active |  |
| state | setting_contact_info.contact_state | Active |  |
| zipcode | setting_contact_info.contact_zipcode | Active |  |
| country | setting_contact_info.contact_country | Active |  |
| business_hours | setting_contact_info.business_hours | Active |  |
| timezone | setting_contact_info.time_zone | Active | UI label timezone; DB column time_zone |
| website_url | (none) | Orphan | UI value currently NOT persisted (candidate: add column or remove) |
| facebook_url | setting_social_media.facebook_url | Active | Stored via `updateBusinessSocialMedia()` when provided |
| twitter_url | setting_social_media.twitter_url | Active |  |
| linkedin_url | setting_social_media.linkedin_url | Active |  |
| instagram_url | setting_social_media.instagram_url | Active |  |

Pending separation: extended social platforms, team member social (deferred).

## 2. Accounts (`account_settings.php`)
| UI Field | Table.Column (setting_accounts_config) | STATUS | NOTES |
|----------|----------------------------------------|--------|-------|
| allow_registration | registration_enabled | Active | bool -> int |
| require_email_verification | email_verification_required | Active | bool -> int |
| min_password_length | password_min_length | Active |  |
| require_password_uppercase | password_require_uppercase | Active | bool -> int |
| require_password_numbers | password_require_numbers | Active | bool -> int |
| require_password_symbols | password_require_special | Active | bool -> int |
| default_user_role | default_role | Active |  |
| session_timeout | session_lifetime | Active | minutes -> seconds conversion on save |
| max_login_attempts | max_login_attempts | Active |  |
| lockout_duration | lockout_duration | Active | minutes -> seconds conversion on save |

## 3. SEO (`seo_settings.php`)
| UI Field | Table.Column (setting_seo_global) | STATUS | NOTES |
|----------|----------------------------------|--------|-------|
| site_title | default_title_suffix | Active | UI uses site_title but maps to suffix column |
| site_description | default_meta_description | Active |  |
| site_keywords | default_meta_keywords | Active |  |
| google_analytics_id | google_analytics_id | Active |  |
| google_tag_manager_id | google_tag_manager_id | Active |  |
| facebook_app_id | facebook_pixel_id | Active | UI label mismatch (Pixel vs App) |
| twitter_site | twitter_card_type | Active | Placeholder mapping; refine later |
| robots_txt_content | robots_txt_content | Active |  |

## 4. Blog Identity (`forms/blog_identity_form.php`)
| UI Field | Table.Column (setting_blog_identity) | STATUS | NOTES |
|----------|-------------------------------------|--------|-------|
| blog_title | blog_title | Active |  |
| blog_description | blog_description | Active |  |
| blog_tagline | blog_tagline | Active |  |
| author_name | author_name | Active |  |
| author_bio | author_bio | Active |  |
| default_author_id | default_author_id | Active | int |
| meta_description | meta_description | Active |  |
| meta_keywords | meta_keywords | Active |  |
| blog_email | blog_email | Active | email validation |
| blog_url | blog_url | Active | URL validation |
| copyright_text | copyright_text | Active |  |

Refactored: CSRF + centralized validation applied 2025-08-20. Legacy standalone markup removed.

## 5. Blog Display (`forms/blog_display_form.php`)
| UI Field | Table.Column (setting_blog_display) | STATUS | NOTES |
|----------|-------------------------------------|--------|-------|
| posts_per_page | posts_per_page | Active | int bounds enforced |
| excerpt_length | excerpt_length | Active | int bounds enforced |
| date_format | date_format | Active |  |
| layout | layout | Active |  |
| sidebar_position | sidebar_position | Active |  |
| posts_per_row | posts_per_row | Active |  |
| theme | theme | Active |  |
| enable_featured_image | enable_featured_image | Active | bool -> int |
| thumbnail_width | thumbnail_width | Active |  |
| thumbnail_height | thumbnail_height | Active |  |
| background_image | background_image | Active | URL |
| custom_css | custom_css | Active | Raw string (sanitization intentionally skipped except length) |
| show_author | show_author | Active | bool -> int |
| show_date | show_date | Active | bool -> int |
| show_categories | show_categories | Active | bool -> int |
| show_tags | show_tags | Active | bool -> int |
| show_excerpt | show_excerpt | Active | bool -> int |

## 6. Deferred / Disabled Modules (Feature Flags OFF)
Shop, Landing Pages, Invoices, Documents, Chat, Review System settings forms are hidden; matrix entries will be added when modules re-enabled.

## 7. Orphans / Cleanup Candidates
| UI Field | Reason | Proposed Action |
|----------|--------|-----------------|
| website_url (contact form) | Not persisted | Decide: add column `website_url` to `setting_contact_info` or remove field |
| (none for blog identity/display) |  | Legacy versions replaced | Already refactored |

## 8. Next Steps (Matrix-Driven)
1. Swap in secure CSRF+validation blog identity & display forms (then mark legacy as Deprecated).
2. Implement dashboard completion calculation using mappings only for feature-enabled modules.
3. Add diagnostics page to iterate this matrix and highlight missing columns/tables.
4. Decide on `website_url` persistence (add column or remove UI).
5. Extend matrix when additional branding identity & advanced blog feature forms are modularized.

---
Generated: 2025-08-20
