
-- --------------------------------------------------------

--
-- Structure for view `view_complete_branding`
--
DROP TABLE IF EXISTS `view_complete_branding`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_complete_branding`  AS SELECT `bi`.`business_name_short` AS `business_name_short`, `bi`.`business_name_medium` AS `business_name_medium`, `bi`.`business_name_long` AS `business_name_long`, `bi`.`business_tagline_short` AS `business_tagline_short`, `bi`.`business_tagline_medium` AS `business_tagline_medium`, `bi`.`business_tagline_long` AS `business_tagline_long`, `bc`.`brand_primary_color` AS `brand_primary_color`, `bc`.`brand_secondary_color` AS `brand_secondary_color`, `bc`.`brand_accent_color` AS `brand_accent_color`, `bf`.`brand_font_primary` AS `brand_font_primary`, `bf`.`brand_font_headings` AS `brand_font_headings`, `bf`.`brand_font_body` AS `brand_font_body`, `ba`.`business_logo_main` AS `business_logo_main`, `ba`.`favicon_main` AS `favicon_main`, `bt`.`template_key` AS `active_template`, `bt`.`template_name` AS `template_name`, `bt`.`css_class` AS `css_class` FROM ((((`setting_business_identity` `bi` join `setting_branding_colors` `bc`) join `setting_branding_fonts` `bf`) join `setting_branding_assets` `ba`) left join `setting_branding_templates` `bt` on(`bt`.`is_active` = 1)) ;
