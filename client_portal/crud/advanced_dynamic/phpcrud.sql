CREATE DATABASE IF NOT EXISTS `phpcrud` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `phpcrud`;

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `contacts` (`id`, `first_name`, `last_name`, `email`, `phone`, `title`, `created`) VALUES
(1, 'Sam', 'White', 'samwhite@example.com', '2004550121', 'Manager', '2023-09-12 17:29:00'),
(2, 'Colin', 'Chaplin', 'colinchaplin@example.com', '2022550178', 'Employee', '2023-09-12 17:29:00'),
(3, 'Ricky', 'Waltz', 'rickywaltz@example.com', '7862342390', 'Employee', '2023-09-12 19:16:00'),
(4, 'Arnold', 'Hall', 'arnoldhall@example.com', '5089573579', 'Manager', '2023-09-12 19:17:00'),
(5, 'Donald', 'Smith', 'donald1983@example.com', '7019007916', 'Employee', '2023-09-12 19:20:00'),
(6, 'Nadia', 'Doole', 'nadia.doole0@example.com', '6153353674', 'Employee', '2023-09-12 19:20:00'),
(7, 'Sarah', 'Jones', 'angela1977@example.com', '3094234980', 'Assistant', '2023-09-12 19:21:00'),
(8, 'Robert', 'Junior', 'robertjunior@example.com', '4209875343', 'Assistant', '2023-09-12 23:52:00'),
(9, 'Jakob', 'Biggs', 'jakobbiggs@example.com', '0125345786', 'Manager', '2023-09-11 16:48:00'),
(10, 'John', 'Doe', 'johndoe@example.com', '0675213823', 'Manager', '2023-09-12 20:17:00');