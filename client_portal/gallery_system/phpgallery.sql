CREATE DATABASE IF NOT EXISTS `phpgallery_advanced` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `phpgallery_advanced`;

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'Member',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `accounts` (`id`, `email`, `password`, `display_name`, `role`) VALUES
(1, 'admin@example.com', '$2y$10$ZU7Jq5yZ1U/ifeJoJzvLbenjRyJVkSzmQKQc.X0KDPkfR3qs/iA7O', 'Admin', 'Admin');

CREATE TABLE IF NOT EXISTS `collections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description_text` varchar(255) NOT NULL,
  `acc_id` int(11) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description_text` mediumtext NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `uploaded_date` datetime NOT NULL DEFAULT current_timestamp(),
  `media_type` varchar(10) NOT NULL,
  `thumbnail` varchar(255) NOT NULL DEFAULT '',
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `acc_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `media_collections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `collection_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id` (`collection_id`,`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `media_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_id` int(11) NOT NULL,
  `acc_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;