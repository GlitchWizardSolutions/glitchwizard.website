
-- --------------------------------------------------------

--
-- Structure for view `view_active_content`
--
DROP TABLE IF EXISTS `view_active_content`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_active_content`  AS SELECT 'service' AS `content_type`, `setting_content_services`.`service_key` AS `content_key`, `setting_content_services`.`service_title` AS `title`, `setting_content_services`.`service_description` AS `description`, `setting_content_services`.`service_icon` AS `icon`, `setting_content_services`.`service_order` AS `display_order` FROM `setting_content_services` WHERE `setting_content_services`.`is_active` = 1union allselect 'feature' AS `content_type`,`setting_content_features`.`feature_key` AS `content_key`,`setting_content_features`.`feature_title` AS `title`,`setting_content_features`.`feature_description` AS `description`,`setting_content_features`.`feature_icon` AS `icon`,`setting_content_features`.`feature_order` AS `display_order` from `setting_content_features` where `setting_content_features`.`is_active` = 1 order by `display_order`  ;
