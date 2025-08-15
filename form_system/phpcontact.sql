 
 
 
-- Enhanced Contact Form Messages Table for GWS Universal Hybrid App
-- Compatible with existing phpcontact structure but optimized for the integrated system

CREATE TABLE IF NOT EXISTS `contact_form_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(254) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `msg` text NOT NULL,
  `extra` text NOT NULL,
  `submit_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('Unread','Read','Replied') NOT NULL DEFAULT 'Unread',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_submit_date` (`submit_date`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Sample data for testing (can be removed in production)
INSERT INTO `contact_form_messages` (`id`, `email`, `subject`, `msg`, `extra`, `submit_date`, `status`) VALUES
(1, 'johndoe@example.com', 'Responsive Menu Issue', 'Hi Team,\r\n\r\nI\'ve noticed on mobile devices that the responsive menu isn\'t aligned with the layout. \r\n\r\nI thought I would let you guys know!\r\n\r\nRegards,\r\nJohn', '{\"first_name\":\"John\",\"last_name\":\"Doe\",\"category\":\"technical\",\"ip_address\":\"192.168.1.100\",\"user_agent\":\"Mozilla\\/5.0\",\"full_name\":\"John Doe\"}', '2023-08-30 15:06:42', 'Read'),
(2, 'robertjohnson@example.com', 'Advertising Inquiry', 'Hello,\r\n\r\nI\'m contacting you on behalf of our agency, which seeks to provide relevant advertisements based on your niche. \r\n\r\nAre you interested in our services?\r\n\r\nBest Regards,\r\nRobert', '{\"first_name\":\"Robert\",\"last_name\":\"Johnson\",\"category\":\"business\",\"ip_address\":\"192.168.1.101\",\"user_agent\":\"Mozilla\\/5.0\",\"full_name\":\"Robert Johnson\"}', '2023-08-30 15:10:42', 'Read');