-- Create team table with corrected field names
-- This matches the field names that were used in the UPDATE statements

CREATE TABLE IF NOT EXISTS `setting_content_team` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `member_name` varchar(100) NOT NULL,
    `member_role` varchar(100) NOT NULL,
    `member_bio` text DEFAULT NULL,
    `member_image` varchar(255) DEFAULT NULL,
    `display_order` int(11) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert the Burden to Blessings team data with ACTUAL website content
INSERT INTO `setting_content_team` (`member_name`, `member_role`, `member_bio`, `member_image`, `display_order`, `is_active`) VALUES
('Elizabeth Riggs', 'CEO', 'I''m the person whose voice you hear on the phone, the hand you shake and who personally oversees your experience with us.', 'assets/img/team/elizabeth-riggs.jpg', 1, 1),
('Jon-David Riggs', 'Logistics Manager', 'I handle all of the paper-work and behind the scenes stuff. Some may say it''s boring, but I know without the boring technical stuff, we can''t help families the way they really deserve.', 'assets/img/team/jon-david-riggs.jpg', 2, 1),
('Our Family', 'Purpose Coordinators', 'Appreciation of the blessings we have with our family, is the drive behind our purpose to positively impact other Indiana families, like ours.', 'assets/img/team/riggs-family.jpg', 3, 1);
