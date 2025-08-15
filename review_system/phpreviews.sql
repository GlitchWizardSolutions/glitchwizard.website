CREATE DATABASE IF NOT EXISTS `phpreviews` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `phpreviews`;

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `role` enum('Admin','Member') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `accounts` (`id`, `email`, `password`, `display_name`, `role`) VALUES
(1, 'admin@example.com', '$2y$10$ZU7Jq5yZ1U/ifeJoJzvLbenjRyJVkSzmQKQc.X0KDPkfR3qs/iA7O', 'Admin', 'Admin');

CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `submit_date` datetime NOT NULL DEFAULT current_timestamp(),
  `approved` tinyint(1) NOT NULL,
  `acc_id` int(11) NOT NULL DEFAULT -1,
  `likes` int(11) NOT NULL DEFAULT 0,
  `response` text NOT NULL DEFAULT '', 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

INSERT INTO `reviews` (`id`, `page_id`, `display_name`, `content`, `rating`, `submit_date`, `approved`, `acc_id`, `likes`, `response`) VALUES
(1, 1, 'David Deacon', 'I use this website on a daily basis. The amount of content is brilliant!', 5, '2023-02-09 20:43:00', 1, -1, 0, ''),
(2, 1, 'Larry Brown', 'Great website, great content, and great support!', 4, '2023-02-09 21:00:00', 1, -1, 0, ''),
(3, 1, 'Robert Billings', 'Website needs more content. Good website but content is lacking.', 3, '2023-02-09 21:10:00', 1, -1, 0, ''),
(4, 1, 'Daniel Callaghan', 'Great!', 5, '2023-03-09 23:51:00', 1, -1, 1, ''),
(5, 1, 'Joshua Kennedy', 'Fantasic website! Has everything I need to know.', 5, '2023-03-16 17:34:00', 1, -1, 0, ''),
(6, 1, 'Johannes Hansen', 'Really like this website! Helps me out a lot!', 5, '2023-03-16 17:35:00', 1, -1, 0, ''),
(7, 1, 'Isobel Whitehead', 'Thank you for providing a website that helps us out a lot!', 4, '2023-03-16 17:40:00', 1, -1, 1, ''),
(8, 1, 'John Doe', 'Brilliant! Thank you for providing quality content!', 5, '2023-03-29 18:40:00', 1, -1, 3, 'Thank you for your feedback!'),
(9, 1, 'Oliver Smith', 'An impressive collection of resources for the average internet user.', 4, '2023-03-30 00:40:00', 1, -1, 0, '');

CREATE TABLE IF NOT EXISTS `review_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(255) NOT NULL,
  `replacement` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `review_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `review_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `review_page_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;