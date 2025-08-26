-- Alter content_items to add icon column and seed service section meta
-- Created: 2025-08-20

ALTER TABLE content_items
  ADD COLUMN icon VARCHAR(64) NULL AFTER body;

-- Seed section meta rows for services title & paragraph if not present
INSERT IGNORE INTO content_items (area, slug, title, body, position, active)
VALUES ('section','services-title','',NULL,0,1),
       ('section','services-paragraph',NULL,'',0,1);
