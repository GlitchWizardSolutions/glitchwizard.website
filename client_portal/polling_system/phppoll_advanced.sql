CREATE DATABASE IF NOT EXISTS `phppoll_advanced` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `phppoll_advanced`;

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `role` enum('Admin','Member') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `accounts` (`id`, `email`, `password`, `display_name`, `role`) VALUES
(1, 'admin@example.com', '$2y$10$ZU7Jq5yZ1U/ifeJoJzvLbenjRyJVkSzmQKQc.X0KDPkfR3qs/iA7O', 'Admin', 'Admin');

CREATE TABLE IF NOT EXISTS `polls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT 1,
  `num_choices` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `polls` (`id`, `title`, `description`, `created`, `start_date`, `end_date`, `approved`, `num_choices`) VALUES
(1, 'What\'s your favorite coding language?', '', '2024-01-01 00:00:00', '2024-01-01 00:00:00', NULL, 1, 1),
(2, 'What\'s your favorite gaming console?', '', '2024-01-01 00:00:00', '2024-01-01 00:00:00', '2024-02-01 00:00:00', 1, 1),
(3, 'What\'s your favorite car manufacturer?', 'This is a test description.', '2024-01-01 00:00:00', '2024-01-01 00:00:00', '2024-02-01 00:00:00', 1, 1);

CREATE TABLE IF NOT EXISTS `polls_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `polls_categories` (`id`, `title`, `description`, `created`) VALUES
(1, 'General', 'This is a test description.', '2024-01-01 00:00:00'),
(2, 'Coding', 'This is a test description.', '2024-01-01 00:00:00');

CREATE TABLE IF NOT EXISTS `poll_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT 0,
  `img` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `poll_answers` (`id`, `poll_id`, `title`, `votes`, `img`) VALUES
(1, 1, 'PHP', 47, ''),
(2, 1, 'Python', 39, ''),
(3, 1, 'C#', 24, ''),
(4, 1, 'Java', 17, ''),
(5, 2, 'PlayStation 5', 50, ''),
(6, 2, 'Xbox Series X', 62, ''),
(7, 2, 'Nintendo Switch', 32, ''),
(8, 3, 'BMW', 225, ''),
(9, 3, 'Ford', 194, ''),
(10, 3, 'Tesla', 248, ''),
(11, 3, 'Honda', 129, ''),
(12, 2, 'Steam Deck', 0, '');

CREATE TABLE IF NOT EXISTS `poll_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `poll_id` (`poll_id`,`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `poll_categories` (`id`, `poll_id`, `category_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 3, 1);

CREATE TABLE IF NOT EXISTS `poll_votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;