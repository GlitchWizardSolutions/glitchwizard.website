
-- --------------------------------------------------------

--
-- Structure for view `v_app_config_summary`
--
DROP TABLE IF EXISTS `v_app_config_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_app_config_summary`  AS SELECT `setting_app_configurations`.`app_name` AS `app_name`, count(0) AS `total_settings`, count(case when `setting_app_configurations`.`is_sensitive` = 1 then 1 end) AS `sensitive_settings`, count(case when `setting_app_configurations`.`config_value` is null or `setting_app_configurations`.`config_value` = '' then 1 end) AS `empty_settings`, count(distinct `setting_app_configurations`.`section`) AS `sections_count`, count(distinct `setting_app_configurations`.`display_group`) AS `groups_count`, max(`setting_app_configurations`.`updated_at`) AS `last_updated` FROM `setting_app_configurations` WHERE `setting_app_configurations`.`is_active` = 1 GROUP BY `setting_app_configurations`.`app_name` ORDER BY `setting_app_configurations`.`app_name` ASC ;
