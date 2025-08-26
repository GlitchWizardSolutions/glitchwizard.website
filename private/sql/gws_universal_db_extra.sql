
--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `selector` (`selector`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `blog_albums`
--
ALTER TABLE `blog_albums`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_files`
--
ALTER TABLE `blog_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_gallery`
--
ALTER TABLE `blog_gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_gallery_categories`
--
ALTER TABLE `blog_gallery_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_gallery_tags`
--
ALTER TABLE `blog_gallery_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_menu`
--
ALTER TABLE `blog_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_messages`
--
ALTER TABLE `blog_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_newsletter`
--
ALTER TABLE `blog_newsletter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_pages`
--
ALTER TABLE `blog_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_post_tags`
--
ALTER TABLE `blog_post_tags`
  ADD PRIMARY KEY (`post_id`,`tag_id`);

--
-- Indexes for table `blog_tags`
--
ALTER TABLE `blog_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`tag`),
  ADD UNIQUE KEY `tag` (`tag`);

--
-- Indexes for table `blog_users`
--
ALTER TABLE `blog_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog_widgets`
--
ALTER TABLE `blog_widgets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `campaign_clicks`
--
ALTER TABLE `campaign_clicks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `campaign_id` (`campaign_id`,`subscriber_id`);

--
-- Indexes for table `campaign_items`
--
ALTER TABLE `campaign_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `campaign_id` (`campaign_id`,`subscriber_id`);

--
-- Indexes for table `campaign_opens`
--
ALTER TABLE `campaign_opens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `campaign_id` (`campaign_id`,`subscriber_id`);

--
-- Indexes for table `campaign_unsubscribes`
--
ALTER TABLE `campaign_unsubscribes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `campaign_id` (`campaign_id`,`subscriber_id`);

--
-- Indexes for table `chat_banned_ips`
--
ALTER TABLE `chat_banned_ips`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`),
  ADD KEY `idx_banned_by` (`banned_by`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `chat_departments`
--
ALTER TABLE `chat_departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `chat_files`
--
ALTER TABLE `chat_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_message` (`message_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_sender_type` (`sender_type`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `chat_operator_departments`
--
ALTER TABLE `chat_operator_departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `operator_department` (`operator_id`,`department_id`),
  ADD KEY `idx_operator` (`operator_id`),
  ADD KEY `idx_department` (`department_id`);

--
-- Indexes for table `chat_quick_responses`
--
ALTER TABLE `chat_quick_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_operator` (`operator_id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_global` (`is_global`);

--
-- Indexes for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_operator` (`operator_id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_last_activity` (`last_activity`);

--
-- Indexes for table `client_signatures`
--
ALTER TABLE `client_signatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_page_parent_approved` (`page_id`,`parent_id`,`approved`),
  ADD KEY `idx_thread_filtering` (`top_parent_id`,`approved`,`featured`);

--
-- Indexes for table `comment_filters`
--
ALTER TABLE `comment_filters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment_page_details`
--
ALTER TABLE `comment_page_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment_reports`
--
ALTER TABLE `comment_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_form_messages`
--
ALTER TABLE `contact_form_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_fonts`
--
ALTER TABLE `custom_fonts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_font_family` (`font_family`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `custom_placeholders`
--
ALTER TABLE `custom_placeholders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `draft_locks`
--
ALTER TABLE `draft_locks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_title` (`document_title`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`),
  ADD KEY `idx_datestart` (`datestart`),
  ADD KEY `idx_dateend` (`dateend`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_featured` (`featured`),
  ADD KEY `idx_author` (`author_id`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `event_categories`
--
ALTER TABLE `event_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `event_comments`
--
ALTER TABLE `event_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event` (`event_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `event_email_templates`
--
ALTER TABLE `event_email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `event_files`
--
ALTER TABLE `event_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event` (`event_id`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`);

--
-- Indexes for table `event_page_details`
--
ALTER TABLE `event_page_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_id` (`page_id`),
  ADD UNIQUE KEY `url` (`url`);

--
-- Indexes for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event` (`event_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payment_status` (`payment_status`);

--
-- Indexes for table `event_unavailable_dates`
--
ALTER TABLE `event_unavailable_dates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_forms_status_created` (`status`,`created_at`);

--
-- Indexes for table `form_analytics`
--
ALTER TABLE `form_analytics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_form_date` (`form_id`,`date_tracked`),
  ADD KEY `idx_form_id` (`form_id`),
  ADD KEY `idx_date_tracked` (`date_tracked`),
  ADD KEY `idx_analytics_date_range` (`date_tracked`,`form_id`);

--
-- Indexes for table `form_email_templates`
--
ALTER TABLE `form_email_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_form_id` (`form_id`),
  ADD KEY `idx_template_type` (`template_type`);

--
-- Indexes for table `form_fields`
--
ALTER TABLE `form_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_form_id` (`form_id`),
  ADD KEY `idx_sort_order` (`sort_order`),
  ADD KEY `idx_fields_form_order` (`form_id`,`sort_order`);

--
-- Indexes for table `form_files`
--
ALTER TABLE `form_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_submission_id` (`submission_id`),
  ADD KEY `idx_field_name` (`field_name`);

--
-- Indexes for table `form_integrations`
--
ALTER TABLE `form_integrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_form_id` (`form_id`),
  ADD KEY `idx_integration_type` (`integration_type`);

--
-- Indexes for table `form_submissions`
--
ALTER TABLE `form_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_form_id` (`form_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_submissions_form_status` (`form_id`,`status`);

--
-- Indexes for table `gallery_collections`
--
ALTER TABLE `gallery_collections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_media`
--
ALTER TABLE `gallery_media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_media_collections`
--
ALTER TABLE `gallery_media_collections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_id` (`collection_id`,`media_id`);

--
-- Indexes for table `gallery_media_likes`
--
ALTER TABLE `gallery_media_likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_subscribers`
--
ALTER TABLE `group_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_id` (`group_id`,`subscriber_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice_clients`
--
ALTER TABLE `invoice_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `landing_pages`
--
ALTER TABLE `landing_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_template_id` (`template_id`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_is_homepage` (`is_homepage`),
  ADD KEY `idx_pages_status_published` (`status`,`published_at`);

--
-- Indexes for table `landing_page_analytics`
--
ALTER TABLE `landing_page_analytics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_page_date` (`page_id`,`date_tracked`),
  ADD KEY `idx_page_id` (`page_id`),
  ADD KEY `idx_date_tracked` (`date_tracked`),
  ADD KEY `idx_analytics_date_range` (`date_tracked`,`page_id`);

--
-- Indexes for table `landing_page_forms`
--
ALTER TABLE `landing_page_forms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_page_id` (`page_id`),
  ADD KEY `idx_form_id` (`form_id`),
  ADD KEY `idx_form_type` (`form_type`);

--
-- Indexes for table `landing_page_media`
--
ALTER TABLE `landing_page_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_page_id` (`page_id`),
  ADD KEY `idx_media_type` (`media_type`),
  ADD KEY `idx_usage_context` (`usage_context`);

--
-- Indexes for table `landing_page_sections`
--
ALTER TABLE `landing_page_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_page_id` (`page_id`),
  ADD KEY `idx_section_type` (`section_type`),
  ADD KEY `idx_sort_order` (`sort_order`),
  ADD KEY `idx_is_visible` (`is_visible`),
  ADD KEY `idx_sections_page_order` (`page_id`,`sort_order`);

--
-- Indexes for table `landing_page_templates`
--
ALTER TABLE `landing_page_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_usage_count` (`usage_count`),
  ADD KEY `idx_templates_category_active` (`category`,`is_active`);

--
-- Indexes for table `landing_page_variants`
--
ALTER TABLE `landing_page_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_page_id` (`page_id`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`);

--
-- Indexes for table `newsletters`
--
ALTER TABLE `newsletters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_completion_status`
--
ALTER TABLE `page_completion_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_path` (`page_path`),
  ADD KEY `is_complete` (`is_complete`);

--
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `polls_categories`
--
ALTER TABLE `polls_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `poll_answers`
--
ALTER TABLE `poll_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `poll_categories`
--
ALTER TABLE `poll_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `poll_id` (`poll_id`,`category_id`);

--
-- Indexes for table `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `review_filters`
--
ALTER TABLE `review_filters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `review_images`
--
ALTER TABLE `review_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `review_page_details`
--
ALTER TABLE `review_page_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scope`
--
ALTER TABLE `scope`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `section_templates`
--
ALTER TABLE `section_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_section_type` (`section_type`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `settings_status`
--
ALTER TABLE `settings_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `settings_file` (`settings_file`),
  ADD KEY `is_configured` (`is_configured`),
  ADD KEY `is_complete` (`is_complete`);

--
-- Indexes for table `setting_accounts_config`
--
ALTER TABLE `setting_accounts_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_analytics_config`
--
ALTER TABLE `setting_analytics_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_app_configurations`
--
ALTER TABLE `setting_app_configurations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_app_config` (`app_name`,`section`,`config_key`),
  ADD KEY `idx_app_section` (`app_name`,`section`),
  ADD KEY `idx_display_group` (`display_group`),
  ADD KEY `idx_sensitive` (`is_sensitive`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_config_lookup` (`app_name`,`section`,`config_key`,`is_active`),
  ADD KEY `idx_admin_display` (`app_name`,`display_group`,`display_order`);

--
-- Indexes for table `setting_app_configurations_audit`
--
ALTER TABLE `setting_app_configurations_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_config_id` (`config_id`),
  ADD KEY `idx_app_name` (`app_name`),
  ADD KEY `idx_change_type` (`change_type`),
  ADD KEY `idx_changed_by` (`changed_by`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `setting_app_configurations_cache`
--
ALTER TABLE `setting_app_configurations_cache`
  ADD PRIMARY KEY (`cache_key`),
  ADD KEY `idx_app_name` (`app_name`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `setting_blog_comments`
--
ALTER TABLE `setting_blog_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_blog_config`
--
ALTER TABLE `setting_blog_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_blog_display`
--
ALTER TABLE `setting_blog_display`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_blog_features`
--
ALTER TABLE `setting_blog_features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_blog_identity`
--
ALTER TABLE `setting_blog_identity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_blog_seo`
--
ALTER TABLE `setting_blog_seo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_blog_social`
--
ALTER TABLE `setting_blog_social`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_branding_assets`
--
ALTER TABLE `setting_branding_assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_branding_colors`
--
ALTER TABLE `setting_branding_colors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_setting_branding_colors_primary` (`brand_primary_color`,`brand_secondary_color`);

--
-- Indexes for table `setting_branding_fonts`
--
ALTER TABLE `setting_branding_fonts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_branding_templates`
--
ALTER TABLE `setting_branding_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_key` (`template_key`);

--
-- Indexes for table `setting_business_contact`
--
ALTER TABLE `setting_business_contact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_identity_id` (`business_identity_id`);

--
-- Indexes for table `setting_business_identity`
--
ALTER TABLE `setting_business_identity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_setting_business_identity_names` (`business_name_short`,`business_name_medium`);

--
-- Indexes for table `setting_chat_config`
--
ALTER TABLE `setting_chat_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_contact_config`
--
ALTER TABLE `setting_contact_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_contact_info`
--
ALTER TABLE `setting_contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_clients`
--
ALTER TABLE `setting_content_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_features`
--
ALTER TABLE `setting_content_features`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `feature_key` (`feature_key`),
  ADD KEY `idx_category` (`feature_category`),
  ADD KEY `idx_order` (`feature_order`),
  ADD KEY `idx_setting_content_features_category_order` (`feature_category`,`feature_order`,`is_active`);

--
-- Indexes for table `setting_content_homepage`
--
ALTER TABLE `setting_content_homepage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_portfolio`
--
ALTER TABLE `setting_content_portfolio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_pricing`
--
ALTER TABLE `setting_content_pricing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plan_key` (`plan_key`),
  ADD KEY `idx_plan_category` (`plan_category`),
  ADD KEY `idx_plan_order` (`plan_order`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_pricing_featured` (`is_featured`,`plan_order`),
  ADD KEY `idx_pricing_popular` (`is_popular`,`plan_order`),
  ADD KEY `idx_pricing_active_order` (`is_active`,`plan_order`);

--
-- Indexes for table `setting_content_services`
--
ALTER TABLE `setting_content_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_stats`
--
ALTER TABLE `setting_content_stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_team`
--
ALTER TABLE `setting_content_team`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_content_testimonials`
--
ALTER TABLE `setting_content_testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_email_config`
--
ALTER TABLE `setting_email_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_events_config`
--
ALTER TABLE `setting_events_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_footer_special_links`
--
ALTER TABLE `setting_footer_special_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `link_key` (`link_key`);

--
-- Indexes for table `setting_footer_useful_links`
--
ALTER TABLE `setting_footer_useful_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_forms_config`
--
ALTER TABLE `setting_forms_config`
  ADD PRIMARY KEY (`setting_name`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `setting_landing_pages_config`
--
ALTER TABLE `setting_landing_pages_config`
  ADD PRIMARY KEY (`setting_name`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `setting_payment_config`
--
ALTER TABLE `setting_payment_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_performance_config`
--
ALTER TABLE `setting_performance_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_portal_config`
--
ALTER TABLE `setting_portal_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_security_config`
--
ALTER TABLE `setting_security_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_seo_global`
--
ALTER TABLE `setting_seo_global`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_seo_pages`
--
ALTER TABLE `setting_seo_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_slug` (`page_slug`),
  ADD KEY `idx_slug` (`page_slug`),
  ADD KEY `idx_setting_seo_pages_slug` (`page_slug`,`noindex`);

--
-- Indexes for table `setting_shop_config`
--
ALTER TABLE `setting_shop_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_social_media`
--
ALTER TABLE `setting_social_media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_system_audit`
--
ALTER TABLE `setting_system_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_setting` (`setting_key`),
  ADD KEY `idx_date` (`changed_at`);

--
-- Indexes for table `setting_system_config`
--
ALTER TABLE `setting_system_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_system_core`
--
ALTER TABLE `setting_system_core`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting_system_metadata`
--
ALTER TABLE `setting_system_metadata`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_table` (`table_name`),
  ADD KEY `idx_key` (`setting_key`);

--
-- Indexes for table `shop_discounts`
--
ALTER TABLE `shop_discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_products`
--
ALTER TABLE `shop_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_product_categories`
--
ALTER TABLE `shop_product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_product_category`
--
ALTER TABLE `shop_product_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`,`category_id`);

--
-- Indexes for table `shop_product_downloads`
--
ALTER TABLE `shop_product_downloads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`,`file_path`);

--
-- Indexes for table `shop_product_media`
--
ALTER TABLE `shop_product_media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_product_media_map`
--
ALTER TABLE `shop_product_media_map`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_product_options`
--
ALTER TABLE `shop_product_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`,`option_name`,`option_value`) USING BTREE;

--
-- Indexes for table `shop_settings`
--
ALTER TABLE `shop_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `shop_shipping`
--
ALTER TABLE `shop_shipping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_taxes`
--
ALTER TABLE `shop_taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_transactions`
--
ALTER TABLE `shop_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `txn_id` (`txn_id`);

--
-- Indexes for table `shop_transaction_items`
--
ALTER TABLE `shop_transaction_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_wishlist`
--
ALTER TABLE `shop_wishlist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scope_id` (`scope_id`);

--
-- Indexes for table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets_categories`
--
ALTER TABLE `tickets_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets_comments`
--
ALTER TABLE `tickets_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets_uploads`
--
ALTER TABLE `tickets_uploads`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_albums`
--
ALTER TABLE `blog_albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_comments`
--
ALTER TABLE `blog_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_files`
--
ALTER TABLE `blog_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_gallery`
--
ALTER TABLE `blog_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_gallery_categories`
--
ALTER TABLE `blog_gallery_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_gallery_tags`
--
ALTER TABLE `blog_gallery_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_menu`
--
ALTER TABLE `blog_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_messages`
--
ALTER TABLE `blog_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_newsletter`
--
ALTER TABLE `blog_newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_pages`
--
ALTER TABLE `blog_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_tags`
--
ALTER TABLE `blog_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_users`
--
ALTER TABLE `blog_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_widgets`
--
ALTER TABLE `blog_widgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campaign_clicks`
--
ALTER TABLE `campaign_clicks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campaign_items`
--
ALTER TABLE `campaign_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campaign_opens`
--
ALTER TABLE `campaign_opens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campaign_unsubscribes`
--
ALTER TABLE `campaign_unsubscribes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_banned_ips`
--
ALTER TABLE `chat_banned_ips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_departments`
--
ALTER TABLE `chat_departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_files`
--
ALTER TABLE `chat_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_operator_departments`
--
ALTER TABLE `chat_operator_departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_quick_responses`
--
ALTER TABLE `chat_quick_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client_signatures`
--
ALTER TABLE `client_signatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comment_filters`
--
ALTER TABLE `comment_filters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comment_page_details`
--
ALTER TABLE `comment_page_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comment_reports`
--
ALTER TABLE `comment_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_form_messages`
--
ALTER TABLE `contact_form_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_fonts`
--
ALTER TABLE `custom_fonts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_placeholders`
--
ALTER TABLE `custom_placeholders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `draft_locks`
--
ALTER TABLE `draft_locks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_categories`
--
ALTER TABLE `event_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_comments`
--
ALTER TABLE `event_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_email_templates`
--
ALTER TABLE `event_email_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_files`
--
ALTER TABLE `event_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_page_details`
--
ALTER TABLE `event_page_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_registrations`
--
ALTER TABLE `event_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_unavailable_dates`
--
ALTER TABLE `event_unavailable_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_analytics`
--
ALTER TABLE `form_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_email_templates`
--
ALTER TABLE `form_email_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_fields`
--
ALTER TABLE `form_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_files`
--
ALTER TABLE `form_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_integrations`
--
ALTER TABLE `form_integrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `form_submissions`
--
ALTER TABLE `form_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery_collections`
--
ALTER TABLE `gallery_collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery_media`
--
ALTER TABLE `gallery_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery_media_collections`
--
ALTER TABLE `gallery_media_collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery_media_likes`
--
ALTER TABLE `gallery_media_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_subscribers`
--
ALTER TABLE `group_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_clients`
--
ALTER TABLE `invoice_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `landing_pages`
--
ALTER TABLE `landing_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `landing_page_analytics`
--
ALTER TABLE `landing_page_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `landing_page_forms`
--
ALTER TABLE `landing_page_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `landing_page_media`
--
ALTER TABLE `landing_page_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `landing_page_sections`
--
ALTER TABLE `landing_page_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `landing_page_templates`
--
ALTER TABLE `landing_page_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `landing_page_variants`
--
ALTER TABLE `landing_page_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletters`
--
ALTER TABLE `newsletters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_completion_status`
--
ALTER TABLE `page_completion_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `polls_categories`
--
ALTER TABLE `polls_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `poll_answers`
--
ALTER TABLE `poll_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `poll_categories`
--
ALTER TABLE `poll_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `poll_votes`
--
ALTER TABLE `poll_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_filters`
--
ALTER TABLE `review_filters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_images`
--
ALTER TABLE `review_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_page_details`
--
ALTER TABLE `review_page_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scope`
--
ALTER TABLE `scope`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `section_templates`
--
ALTER TABLE `section_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings_status`
--
ALTER TABLE `settings_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_accounts_config`
--
ALTER TABLE `setting_accounts_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_analytics_config`
--
ALTER TABLE `setting_analytics_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_app_configurations`
--
ALTER TABLE `setting_app_configurations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_app_configurations_audit`
--
ALTER TABLE `setting_app_configurations_audit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_blog_comments`
--
ALTER TABLE `setting_blog_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_blog_config`
--
ALTER TABLE `setting_blog_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_blog_display`
--
ALTER TABLE `setting_blog_display`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_blog_features`
--
ALTER TABLE `setting_blog_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_blog_identity`
--
ALTER TABLE `setting_blog_identity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_blog_seo`
--
ALTER TABLE `setting_blog_seo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_blog_social`
--
ALTER TABLE `setting_blog_social`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_branding_assets`
--
ALTER TABLE `setting_branding_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_branding_colors`
--
ALTER TABLE `setting_branding_colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_branding_fonts`
--
ALTER TABLE `setting_branding_fonts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_branding_templates`
--
ALTER TABLE `setting_branding_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_business_contact`
--
ALTER TABLE `setting_business_contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_business_identity`
--
ALTER TABLE `setting_business_identity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_chat_config`
--
ALTER TABLE `setting_chat_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_contact_config`
--
ALTER TABLE `setting_contact_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_contact_info`
--
ALTER TABLE `setting_contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_content_clients`
--
ALTER TABLE `setting_content_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_content_features`
--
ALTER TABLE `setting_content_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_content_homepage`
--
ALTER TABLE `setting_content_homepage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_content_portfolio`
--
ALTER TABLE `setting_content_portfolio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_content_pricing`
--
ALTER TABLE `setting_content_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_content_services`
--
ALTER TABLE `setting_content_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_content_stats`
--
ALTER TABLE `setting_content_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_content_team`
--
ALTER TABLE `setting_content_team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_content_testimonials`
--
ALTER TABLE `setting_content_testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_email_config`
--
ALTER TABLE `setting_email_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_events_config`
--
ALTER TABLE `setting_events_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_footer_special_links`
--
ALTER TABLE `setting_footer_special_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_footer_useful_links`
--
ALTER TABLE `setting_footer_useful_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_payment_config`
--
ALTER TABLE `setting_payment_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_performance_config`
--
ALTER TABLE `setting_performance_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_portal_config`
--
ALTER TABLE `setting_portal_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_security_config`
--
ALTER TABLE `setting_security_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_seo_global`
--
ALTER TABLE `setting_seo_global`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_seo_pages`
--
ALTER TABLE `setting_seo_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_shop_config`
--
ALTER TABLE `setting_shop_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_social_media`
--
ALTER TABLE `setting_social_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_system_audit`
--
ALTER TABLE `setting_system_audit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_system_config`
--
ALTER TABLE `setting_system_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_system_core`
--
ALTER TABLE `setting_system_core`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_system_metadata`
--
ALTER TABLE `setting_system_metadata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_discounts`
--
ALTER TABLE `shop_discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_products`
--
ALTER TABLE `shop_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_product_categories`
--
ALTER TABLE `shop_product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_product_category`
--
ALTER TABLE `shop_product_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_product_downloads`
--
ALTER TABLE `shop_product_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_product_media`
--
ALTER TABLE `shop_product_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_product_media_map`
--
ALTER TABLE `shop_product_media_map`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_product_options`
--
ALTER TABLE `shop_product_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_settings`
--
ALTER TABLE `shop_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_shipping`
--
ALTER TABLE `shop_shipping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_taxes`
--
ALTER TABLE `shop_taxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_transactions`
--
ALTER TABLE `shop_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_transaction_items`
--
ALTER TABLE `shop_transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_wishlist`
--
ALTER TABLE `shop_wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets_categories`
--
ALTER TABLE `tickets_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets_comments`
--
ALTER TABLE `tickets_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets_uploads`
--
ALTER TABLE `tickets_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `auth_tokens_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_banned_ips`
--
ALTER TABLE `chat_banned_ips`
  ADD CONSTRAINT `chat_banned_ips_ibfk_1` FOREIGN KEY (`banned_by`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_files`
--
ALTER TABLE `chat_files`
  ADD CONSTRAINT `chat_files_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `chat_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_files_ibfk_2` FOREIGN KEY (`message_id`) REFERENCES `chat_messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `chat_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_operator_departments`
--
ALTER TABLE `chat_operator_departments`
  ADD CONSTRAINT `chat_operator_departments_ibfk_1` FOREIGN KEY (`operator_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_operator_departments_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `chat_departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_quick_responses`
--
ALTER TABLE `chat_quick_responses`
  ADD CONSTRAINT `chat_quick_responses_ibfk_1` FOREIGN KEY (`operator_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_signatures`
--
ALTER TABLE `client_signatures`
  ADD CONSTRAINT `client_signatures_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_events_category` FOREIGN KEY (`category_id`) REFERENCES `event_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `event_comments`
--
ALTER TABLE `event_comments`
  ADD CONSTRAINT `event_comments_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `event_comments_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `event_files`
--
ALTER TABLE `event_files`
  ADD CONSTRAINT `event_files_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_files_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD CONSTRAINT `event_registrations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_registrations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `forms`
--
ALTER TABLE `forms`
  ADD CONSTRAINT `forms_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `form_analytics`
--
ALTER TABLE `form_analytics`
  ADD CONSTRAINT `form_analytics_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `form_email_templates`
--
ALTER TABLE `form_email_templates`
  ADD CONSTRAINT `form_email_templates_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `form_fields`
--
ALTER TABLE `form_fields`
  ADD CONSTRAINT `form_fields_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `form_files`
--
ALTER TABLE `form_files`
  ADD CONSTRAINT `form_files_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `form_submissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `form_integrations`
--
ALTER TABLE `form_integrations`
  ADD CONSTRAINT `form_integrations_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `form_submissions`
--
ALTER TABLE `form_submissions`
  ADD CONSTRAINT `form_submissions_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `form_submissions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `landing_pages`
--
ALTER TABLE `landing_pages`
  ADD CONSTRAINT `landing_pages_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `landing_page_analytics`
--
ALTER TABLE `landing_page_analytics`
  ADD CONSTRAINT `landing_page_analytics_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `landing_pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `landing_page_forms`
--
ALTER TABLE `landing_page_forms`
  ADD CONSTRAINT `landing_page_forms_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `landing_pages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `landing_page_forms_ibfk_2` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `landing_page_media`
--
ALTER TABLE `landing_page_media`
  ADD CONSTRAINT `landing_page_media_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `landing_pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `landing_page_sections`
--
ALTER TABLE `landing_page_sections`
  ADD CONSTRAINT `landing_page_sections_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `landing_pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `landing_page_variants`
--
ALTER TABLE `landing_page_variants`
  ADD CONSTRAINT `landing_page_variants_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `landing_pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `setting_app_configurations_audit`
--
ALTER TABLE `setting_app_configurations_audit`
  ADD CONSTRAINT `fk_config_audit` FOREIGN KEY (`config_id`) REFERENCES `setting_app_configurations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `setting_business_contact`
--
ALTER TABLE `setting_business_contact`
  ADD CONSTRAINT `setting_business_contact_ibfk_1` FOREIGN KEY (`business_identity_id`) REFERENCES `setting_business_identity` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`scope_id`) REFERENCES `scope` (`id`) ON DELETE CASCADE;
