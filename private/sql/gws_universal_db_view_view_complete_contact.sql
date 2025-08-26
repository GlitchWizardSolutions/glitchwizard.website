
-- --------------------------------------------------------

--
-- Structure for view `view_complete_contact`
--
DROP TABLE IF EXISTS `view_complete_contact`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_complete_contact`  AS SELECT `ci`.`contact_email` AS `contact_email`, `ci`.`contact_phone` AS `contact_phone`, `ci`.`contact_address` AS `contact_address`, `ci`.`contact_city` AS `contact_city`, `ci`.`contact_state` AS `contact_state`, `ci`.`contact_zipcode` AS `contact_zipcode`, `sm`.`facebook_url` AS `facebook_url`, `sm`.`twitter_url` AS `twitter_url`, `sm`.`instagram_url` AS `instagram_url`, `sm`.`linkedin_url` AS `linkedin_url`, `sm`.`website_url` AS `website_url` FROM (`setting_contact_info` `ci` left join `setting_social_media` `sm` on(1 = 1)) ;
