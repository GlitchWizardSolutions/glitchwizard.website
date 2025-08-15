CREATE DATABASE IF NOT EXISTS `phpcomments_advanced` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `phpcomments_advanced`;

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'Member',
  `registered` datetime NOT NULL DEFAULT current_timestamp(),
  `website_url` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `banned` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `accounts` (`id`, `email`, `password`, `display_name`, `role`, `registered`, `website_url`, `profile_photo`, `banned`) VALUES
(1, 'admin@example.com', '$2y$10$ZU7Jq5yZ1U/ifeJoJzvLbenjRyJVkSzmQKQc.X0KDPkfR3qs/iA7O', 'Admin', 'Admin', '2025-06-23 14:05:38', NULL, NULL, 0);

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT -1,
  `display_name` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `submit_date` datetime NOT NULL DEFAULT current_timestamp(),
  `edited_date` datetime NOT NULL DEFAULT current_timestamp(),
  `votes` int(11) NOT NULL DEFAULT 0,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `acc_id` int(11) NOT NULL DEFAULT -1,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `top_parent_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_page_parent_approved` (`page_id`,`parent_id`,`approved`),
  KEY `idx_thread_filtering` (`top_parent_id`,`approved`,`featured`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comment_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(255) NOT NULL,
  `replacement` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comment_page_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `page_status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comment_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `reason` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;