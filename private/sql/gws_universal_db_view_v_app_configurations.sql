
-- --------------------------------------------------------

--
-- Structure for view `v_app_configurations`
--
DROP TABLE IF EXISTS `v_app_configurations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_app_configurations`  AS SELECT `ac`.`id` AS `id`, `ac`.`app_name` AS `app_name`, `ac`.`section` AS `section`, `ac`.`config_key` AS `config_key`, CASE WHEN `ac`.`is_sensitive` = 1 THEN '***SENSITIVE***' ELSE `ac`.`config_value` END AS `display_value`, `ac`.`config_value` AS `config_value`, `ac`.`data_type` AS `data_type`, `ac`.`is_sensitive` AS `is_sensitive`, `ac`.`description` AS `description`, `ac`.`default_value` AS `default_value`, `ac`.`display_group` AS `display_group`, `ac`.`display_order` AS `display_order`, `ac`.`is_active` AS `is_active`, `ac`.`updated_at` AS `updated_at`, `ac`.`updated_by` AS `updated_by` FROM `setting_app_configurations` AS `ac` WHERE `ac`.`is_active` = 1 ORDER BY `ac`.`app_name` ASC, `ac`.`display_group` ASC, `ac`.`display_order` ASC, `ac`.`section` ASC, `ac`.`config_key` ASC ;
