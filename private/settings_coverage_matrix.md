# Settings Coverage Matrix (Phase 0 Draft)

Columns:
- Table: Database settings table name
- Key Fields: Principal columns (excluding surrogate IDs / timestamps)
- SettingsManager Methods: Getter/update methods implemented
- UI/Form(s): Current admin pages invoking or needing mapping
- Gaps: Missing method, mismatch, or unvalidated field

| Table | Key Fields (excerpt) | Methods | UI/Form(s) | Gaps / Notes |
|-------|----------------------|---------|-----------|--------------|
| setting_business_identity | business_name_short, business_name_medium, business_name_long, tagline | updateBusinessIdentity (get via getCompleteBrandingConfig view) | (branding form TBD) | Need dedicated branding form; add validation spec |
| setting_branding_colors | brand_primary_color, brand_secondary_color, brand_accent_color | updateBrandingColors | (branding colors form TBD) | Hex validation missing |
| setting_contact_info | contact_email, contact_phone, contact_address, contact_city/state/zipcode, business_hours(JSON), time_zone | updateContactInfo / getCompleteContactInfo | forms/business_contact_form.php | Form uses different names (primary_* vs contact_*); need mapping layer or rename inputs |
| setting_accounts_config | registration_enabled, email_verification_required, password_* fields, session_lifetime, lockout_duration, default_role | getAccountSettings/updateAccountSettings (added hotfix & mapping) | account_settings.php | Some UI fields not exposed yet (two_factor, remember_me); add toggle controls |
| setting_seo_global | default_title_suffix, default_meta_description, default_meta_keywords, google_analytics_id, robots_txt_content, ... | getSeoSettings/updateSeoSettings (mapping) | seo_settings.php | UI lacks many fields (suffix, canonical, sitemap flags); plan progressive enhancement |
| setting_blog_identity | blog_title, blog_description, author_name, meta_description | getBlogIdentity/updateBlogIdentity | (blog identity form planned) | Need form + validation |
| setting_blog_display | posts_per_page, excerpt_length, layout, theme | getBlogDisplay/updateBlogDisplay | (blog display form planned) | Range validation missing |
| setting_blog_features | feature toggles | getBlogFeatures | (future) | No update method yet |
| setting_blog_comments | moderation, allow_guest, etc. | getBlogComments | (future) | No update method |
| setting_blog_seo | blog-level meta | getBlogSeo | (future) | No update method |
| setting_blog_social | social share settings | getBlogSocial | (future) | No update method |
| setting_system_config | environment, debug_mode, timezone | getSystemConfig | system_settings.php (not reviewed) | Need update method or use generic updateSetting |
| setting_social_media | facebook_url, twitter_url, linkedin_url, instagram_url | (generic via updateSetting) | business_contact_form currently mixing social fields | Consider separate social form or integrate mapping |
| setting_security_config | security toggles | (generic) | (future) | Add explicit methods if complex logic needed |
| setting_performance_config | caching toggles, asset version | (generic) | (future) | Add cache invalidation triggers |
| setting_payment_config | provider keys | (generic) | shop settings (future) | Sensitive fields: mask & audit writes |
| setting_email_config | smtp_host, smtp_user... | (generic) | email settings (future) | Secure storage / encryption maybe |

## Immediate Remediation Targets
1. Contact form field name alignment (primary_* -> contact_* or mapping function before updateContactInfo).
2. Add CSRF + validation to account & SEO forms using SecurityHelper.
3. Introduce a Branding Settings form (missing UI) or stub to avoid dashboard heuristic issues.
4. Replace dashboard completion heuristic with DB presence checks (Phase 0 later step).

## Validation Specs (Draft Examples)
- Account Settings:
  - min_password_length int 6..64
  - max_login_attempts int 3..15
  - lockout_duration minutes 1..240
  - session_timeout minutes 5..1440
- SEO Settings:
  - site_title length <= 60
  - site_description length <= 160
  - site_keywords length <= 500
  - robots_txt_content length <= 5000

## CSRF Integration Plan
Add hidden <input name="csrf_token" value="SecurityHelper::getCsrfToken(form_key)"> and validate early POST.

## Open Questions
- Should we normalize business_contact_form field names to DB columns or maintain mapping? (Recommend mapping array for backwards compatibility, plus optional hidden migration script.)
- Need consistent audit_reason parameterization? (Currently generic.)

(Generated 2025-08-20)
