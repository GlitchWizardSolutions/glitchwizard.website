-- Alter content_items to add icon column and seed services section meta
-- Created: 2025-08-20

ALTER TABLE content_items
  ADD COLUMN icon VARCHAR(128) NULL AFTER body;

-- Seed services section meta row (area='section', slug='services') if missing
INSERT IGNORE INTO content_items (area, slug, title, body, position, active)
VALUES ('section','services','Services','',0,1);
