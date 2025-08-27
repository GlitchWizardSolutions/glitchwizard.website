# Social Media Data Model Separation

Date: 2025-08-20
Status: Implemented (business form clarified); Team member management stub created.

## Rationale
Business-level social media profiles (brand accounts) serve global site areas (footer, contact page, marketing components). Individual team member profiles relate to personal professional presence and appear only within the Team section or bio cards.

Conflating these creates maintenance risk, inconsistent display, and potential privacy concerns.

## Separation Overview
| Layer | Purpose | Storage Table (proposed/current) | Example Fields |
|-------|---------|----------------------------------|----------------|
| Business (Global) | Official company profiles | `setting_social_media` | facebook_url, twitter_url, linkedin_url, instagram_url, updated_by, updated_at |
| Team Member (Per Row) | Individual professional links | `setting_content_team` | member_facebook, member_x, member_linkedin, member_instagram, member_other |

## Current Implementation
- `business_contact_form.php` now explicitly labels fields as *Official* and includes explanatory text.
- Placeholder admin screen: `team_member_social_form.php` (Phase 1 target) to manage per-member social links.

## Schema Additions (If Not Present)
```sql
ALTER TABLE setting_content_team 
  ADD COLUMN member_facebook VARCHAR(255) NULL AFTER member_image,
  ADD COLUMN member_x VARCHAR(255) NULL AFTER member_facebook,
  ADD COLUMN member_linkedin VARCHAR(255) NULL AFTER member_x,
  ADD COLUMN member_instagram VARCHAR(255) NULL AFTER member_linkedin,
  ADD COLUMN member_other VARCHAR(255) NULL AFTER member_instagram;
```

Business table (`setting_social_media`) should include (if missing):
```sql
CREATE TABLE IF NOT EXISTS setting_social_media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  facebook_url VARCHAR(255) NULL,
  twitter_url VARCHAR(255) NULL,
  linkedin_url VARCHAR(255) NULL,
  instagram_url VARCHAR(255) NULL,
  other_url VARCHAR(255) NULL,
  updated_by VARCHAR(100) NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## SettingsManager Roadmap Additions
- getBusinessSocialMedia()
- updateBusinessSocialMedia($data, $updated_by)
- getTeamMembers() // existing or new
- addTeamMember($data, $updated_by)
- updateTeamMemberSocial($memberId, $links, $updated_by)

## Validation Rules (Draft)
- URLs: max 255 chars, must be valid HTTPS (enforce https:// to protect referral integrity).
- Optional fields: Accept blank -> store NULL.
- Team member social links sanitized individually; no inheritance from business defaults.

## Display Logic
- Footer / global templates: use business table only.
- Team section renderer: for each member, show only links that exist (member_* columns).

## Migration Steps
1. Apply schema alterations above if columns absent.
2. Backfill existing unified social media data (if any) into business table only.
3. Remove any team-related social usage from business context.
4. Deploy per-member CRUD.
5. Update coverage matrix to reflect independent completion statuses.

## Security & Auditing
- All updates routed through SettingsManager with audit entries (reuse `createAuditRecord`).
- CSRF tokens per form key: `contact_settings`, `team_members`.

## Open Questions
- Need `other_url` at business level? (Added for parity; can hold threads, YouTube, etc.)
- Should we support ordering of social icons? (Potential future enhancement; current static order sufficient.)

## Next Actions
- Implement SettingsManager methods (Phase 1).
- Build full team member CRUD with image upload + drag sort.
- Update dashboard completion metrics to include: business social media table presence + at least one team member active.

-- End of Document --
