-- Full normalized content schema (features, services, pages)
-- Created: 2025-08-20
-- Idempotent: safe to run multiple times (uses IF NOT EXISTS)

CREATE TABLE IF NOT EXISTS content_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  area VARCHAR(32) NOT NULL,              -- e.g. hero, feature, service, page, misc
  slug VARCHAR(64) NOT NULL,              -- e.g. feature-1, service-3, about, terms
  title VARCHAR(255) DEFAULT NULL,
  body MEDIUMTEXT DEFAULT NULL,           -- rich HTML or plain text
  position INT NOT NULL DEFAULT 0,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_area_slug (area, slug),
  KEY idx_area_position (area, position, active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pages_metadata (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(64) NOT NULL UNIQUE,       -- about, services, portfolio, contact, privacy, terms
  meta_title VARCHAR(255) DEFAULT NULL,
  meta_description VARCHAR(255) DEFAULT NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed feature / service slots if empty
INSERT IGNORE INTO content_items (area, slug, title, body, position)
SELECT 'feature', CONCAT('feature-', n), '', '', n-1 FROM (
  SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
) t;

INSERT IGNORE INTO content_items (area, slug, title, body, position)
SELECT 'service', CONCAT('service-', n), '', '', n-1 FROM (
  SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6
) t;

-- Seed page placeholders (only create if they do not already exist)
INSERT IGNORE INTO content_items (area, slug, title, body, position)
SELECT 'page', slug, '', '', 0 FROM (
  SELECT 'about' slug UNION ALL SELECT 'services' UNION ALL SELECT 'portfolio' UNION ALL SELECT 'contact' UNION ALL SELECT 'privacy' UNION ALL SELECT 'terms'
) p;

INSERT IGNORE INTO pages_metadata (slug, meta_title, meta_description)
SELECT slug, '', '' FROM (
  SELECT 'about' slug UNION ALL SELECT 'services' UNION ALL SELECT 'portfolio' UNION ALL SELECT 'contact' UNION ALL SELECT 'privacy' UNION ALL SELECT 'terms'
) p;
