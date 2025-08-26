-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2025 at 05:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gws_universal_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL DEFAULT '''''',
  `phone` varchar(255) NOT NULL DEFAULT '''''',
  `address_street` varchar(255) NOT NULL DEFAULT '''''',
  `address_city` varchar(255) NOT NULL DEFAULT '''''',
  `address_state` varchar(255) NOT NULL DEFAULT '''''',
  `address_zip` varchar(255) NOT NULL DEFAULT '''''',
  `address_country` varchar(255) NOT NULL DEFAULT 'USA',
  `registered` datetime NOT NULL DEFAULT current_timestamp(),
  `role` varchar(50) NOT NULL DEFAULT 'Customer' COMMENT '''Admin'', ''Member'', ''Developer'', ''Guest'', ''Subscriber'', ''Editor'', ''Blog_User'', ''Customer''',
  `access_level` tinyint(3) NOT NULL DEFAULT 50,
  `document_path` varchar(200) NOT NULL DEFAULT 'Welcome/',
  `full_name` varchar(200) NOT NULL DEFAULT 'please update',
  `rememberme` varchar(255) NOT NULL DEFAULT '''''',
  `activation_code` varchar(255) NOT NULL DEFAULT 'activated',
  `last_seen` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `method` varchar(50) NOT NULL DEFAULT 'password',
  `social_email` varchar(200) NOT NULL DEFAULT 'please update',
  `reset_code` varchar(255) NOT NULL DEFAULT '''''',
  `password` varchar(255) NOT NULL,
  `tfa_code` varchar(255) NOT NULL DEFAULT '''''',
  `ip` varchar(255) NOT NULL DEFAULT '''''',
  `approved` varchar(50) NOT NULL DEFAULT 'approved',
  `blog_user` int(11) NOT NULL DEFAULT 1,
  `avatar` varchar(255) NOT NULL DEFAULT 'default.svg',
  `banned` tinyint(1) NOT NULL DEFAULT 0,
  `website_url` varchar(255) DEFAULT NULL,
  `chat_operator` tinyint(1) NOT NULL DEFAULT 0,
  `chat_status` enum('offline','available','busy','away') NOT NULL DEFAULT 'offline',
  `chat_auto_accept` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `first_name`, `last_name`, `email`, `phone`, `address_street`, `address_city`, `address_state`, `address_zip`, `address_country`, `registered`, `role`, `access_level`, `document_path`, `full_name`, `rememberme`, `activation_code`, `last_seen`, `method`, `social_email`, `reset_code`, `password`, `tfa_code`, `ip`, `approved`, `blog_user`, `avatar`, `banned`, `website_url`, `chat_operator`, `chat_status`, `chat_auto_accept`) VALUES
(1, 'Dio', 'please', 'update', 'sidewaysy.tasks@gmail.com', '\'\'', '127 Northwood Road', 'Crawfordville', 'FL', '32327', 'USA', '2025-07-02 23:38:00', 'Blog_User', 0, 'Welcome/', 'please update', '$2y$10$esagPd1Lo4sKApSjRvoLVOwX3gaFaRlfVx6QgVKPD21jkPjGobA36', 'activated', '2025-08-13 10:51:02', 'password', 'please update', 'bfee4b3c9490b4ad70e051280a452269570f94f3e403ca66d83f658a61246d19', '$2y$10$Zy64yPZ7YQRI11cEpnFc7u7E5/j/Hcp6151knKsnrZQkQiRhwHM6C', '\'\'', '\'\'', '1', 1, 'default-developer.svg', 0, NULL, 0, 'offline', 1),
(3, 'GlitchWizard', 'Glitch', 'Wizard', 'webdev@glitchwizardsolutions.com', '(850) 294-4226', '127 Northwood Rd', 'Crawfordville', 'FL', '32327', 'United States', '2024-09-10 10:32:00', 'Developer', 0, 'Barbara_Moore', 'Glitch Wizard', '$2y$10$OPJqs7NOIGg/Pwag7is2C.RJUqSM4VZ4Sbfxld.Z3p4sUSoT/YzGC', 'activated', '2025-08-19 22:51:06', 'password', 'sidewaysy@gmail.com', '\'\'', '$2y$10$Qr0AlGEglzRepKFncvVrKuCzeDWORE4UsQ4ZzmucEnH/l1/ein7a2', 'DC1955', '75.229.47.137', '1', 1, 'default-developer.svg', 0, NULL, 0, 'offline', 1),
(48, 'Joseph', 'Joseph', 'Gross', 'cherokeejoey@gmail.com', '(850) 491-9028', '18627 CR 23', 'Bristol', 'Indiana', '46507', 'USA', '2025-08-01 01:34:35', 'Member', 0, '', 'Joseph Gross', '\'\'', 'activated', '2025-08-19 14:06:41', 'password', 'please update', '272f5ad258a5d3f8ad4e59848a7d31eacdeecea761b8346a184a54aec75d98c1', '$2y$10$F5g51ASqGAl0KceMVGxyRO0bXOy5sA0X1UVjWZaw55mrdv7nPKqKK', '\'\'', '\'\'', '1', 1, 'default.svg', 0, NULL, 0, 'offline', 1);

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `document_title` varchar(255) DEFAULT NULL,
  `file_type` enum('pdf','docx','csv','xlsx') DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `output_path` text DEFAULT NULL,
  `version_number` int(11) DEFAULT 1,
  `version_notes` text DEFAULT NULL,
  `version_tags` varchar(255) DEFAULT NULL,
  `signed` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payment_status` enum('pending','paid') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `selector` char(12) NOT NULL,
  `token` char(64) NOT NULL,
  `expires` datetime NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_albums`
--

CREATE TABLE `blog_albums` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments`
--

CREATE TABLE `blog_comments` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL DEFAULT 0,
  `post_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `time` varchar(5) NOT NULL,
  `approved` varchar(3) NOT NULL DEFAULT 'No',
  `guest` varchar(3) NOT NULL DEFAULT 'Yes',
  `ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_files`
--

CREATE TABLE `blog_files` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(5) NOT NULL,
  `path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_gallery`
--

CREATE TABLE `blog_gallery` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `active` varchar(3) NOT NULL DEFAULT 'Yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_gallery_categories`
--

CREATE TABLE `blog_gallery_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_gallery_image_tags`
--

CREATE TABLE `blog_gallery_image_tags` (
  `image_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_gallery_tags`
--

CREATE TABLE `blog_gallery_tags` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_menu`
--

CREATE TABLE `blog_menu` (
  `id` int(11) NOT NULL,
  `page` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `fa_icon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_messages`
--

CREATE TABLE `blog_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `viewed` varchar(7) NOT NULL DEFAULT 'Unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_newsletter`
--

CREATE TABLE `blog_newsletter` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ip` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_pages`
--

CREATE TABLE `blog_pages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `active` enum('Yes','No') DEFAULT 'Yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `author_id` int(11) NOT NULL DEFAULT 1,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `time` varchar(5) NOT NULL,
  `active` varchar(3) NOT NULL DEFAULT 'Yes',
  `featured` varchar(3) NOT NULL DEFAULT 'No',
  `views` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_post_tags`
--

CREATE TABLE `blog_post_tags` (
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_tags`
--

CREATE TABLE `blog_tags` (
  `id` int(11) NOT NULL,
  `tag` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_users`
--

CREATE TABLE `blog_users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `registered` datetime NOT NULL DEFAULT current_timestamp(),
  `username` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'Blog User',
  `access_level` varchar(50) NOT NULL DEFAULT 'Blog Only',
  `document_path` varchar(200) NOT NULL DEFAULT 'Blog/',
  `full_name` varchar(200) NOT NULL DEFAULT 'None Provided',
  `rememberme` varchar(255) NOT NULL DEFAULT '''''',
  `activation_code` varchar(255) NOT NULL DEFAULT 'activated',
  `last_seen` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `method` varchar(50) NOT NULL DEFAULT 'password',
  `social_email` varchar(200) NOT NULL DEFAULT 'None Provided',
  `reset_code` varchar(255) NOT NULL DEFAULT '''''',
  `password` varchar(255) NOT NULL,
  `tfa_code` varchar(255) NOT NULL DEFAULT '''''',
  `ip` varchar(255) NOT NULL DEFAULT '''''',
  `approved` varchar(50) NOT NULL DEFAULT 'approved',
  `avatar` varchar(255) NOT NULL DEFAULT 'assets/img/avatar.png',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_widgets`
--

CREATE TABLE `blog_widgets` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `position` varchar(10) NOT NULL DEFAULT 'Sidebar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE `campaigns` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` enum('Active','Inactive','Paused','Completed','Cancelled') NOT NULL,
  `groups` varchar(255) NOT NULL,
  `newsletter_id` int(11) NOT NULL,
  `submit_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_clicks`
--

CREATE TABLE `campaign_clicks` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `submit_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_items`
--

CREATE TABLE `campaign_items` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `status` enum('Queued','Completed','Cancelled','Failed') NOT NULL,
  `fail_text` varchar(255) NOT NULL DEFAULT '',
  `update_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_opens`
--

CREATE TABLE `campaign_opens` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `submit_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_unsubscribes`
--

CREATE TABLE `campaign_unsubscribes` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `submit_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_banned_ips`
--

CREATE TABLE `chat_banned_ips` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `banned_by` int(11) NOT NULL,
  `banned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_departments`
--

CREATE TABLE `chat_departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `auto_assign` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_files`
--

CREATE TABLE `chat_files` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `upload_by` enum('customer','operator') NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `sender_type` enum('customer','operator','system') NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `sender_name` varchar(100) DEFAULT NULL,
  `message` text NOT NULL,
  `message_type` enum('text','file','image','system') NOT NULL DEFAULT 'text',
  `file_url` varchar(500) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_operator_departments`
--

CREATE TABLE `chat_operator_departments` (
  `id` int(11) NOT NULL,
  `operator_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_quick_responses`
--

CREATE TABLE `chat_quick_responses` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `operator_id` int(11) DEFAULT NULL,
  `is_global` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_sessions`
--

CREATE TABLE `chat_sessions` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_ip` varchar(45) DEFAULT NULL,
  `operator_id` int(11) DEFAULT NULL,
  `status` enum('waiting','active','ended','transferred') NOT NULL DEFAULT 'waiting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ended_at` timestamp NULL DEFAULT NULL,
  `rating` tinyint(1) DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_signatures`
--

CREATE TABLE `client_signatures` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `signature_path` text DEFAULT NULL,
  `initials_path` text DEFAULT NULL,
  `thumbnail_path` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT -1,
  `display_name` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `submit_date` datetime NOT NULL DEFAULT current_timestamp(),
  `edited_date` datetime NOT NULL DEFAULT current_timestamp(),
  `votes` int(11) NOT NULL DEFAULT 0,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `account_id` int(11) NOT NULL DEFAULT -1,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `top_parent_id` int(11) NOT NULL DEFAULT 0,
  `reply` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment_filters`
--

CREATE TABLE `comment_filters` (
  `id` int(11) NOT NULL,
  `word` varchar(255) NOT NULL,
  `replacement` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment_page_details`
--

CREATE TABLE `comment_page_details` (
  `id` int(11) NOT NULL,
  `page_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `page_status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment_reports`
--

CREATE TABLE `comment_reports` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_form_messages`
--

CREATE TABLE `contact_form_messages` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `msg` text NOT NULL,
  `extra` text NOT NULL,
  `submit_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('Unread','Read','Replied') NOT NULL DEFAULT 'Unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_fonts`
--

CREATE TABLE `custom_fonts` (
  `id` int(11) NOT NULL,
  `font_name` varchar(255) NOT NULL,
  `font_family` varchar(255) NOT NULL,
  `font_file_path` varchar(500) NOT NULL,
  `font_format` enum('woff2','woff','ttf','otf') NOT NULL DEFAULT 'woff2',
  `file_size` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `uploaded_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_placeholders`
--

CREATE TABLE `custom_placeholders` (
  `id` int(11) NOT NULL,
  `placeholder_text` varchar(255) NOT NULL,
  `placeholder_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `draft_locks`
--

CREATE TABLE `draft_locks` (
  `id` int(11) NOT NULL,
  `document_title` varchar(255) NOT NULL,
  `client_id` int(11) NOT NULL,
  `locked_until` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `uid` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(500) DEFAULT NULL,
  `datestart` datetime NOT NULL,
  `dateend` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `max_attendees` int(11) DEFAULT NULL,
  `current_attendees` int(11) DEFAULT 0,
  `status` enum('draft','published','cancelled','postponed') NOT NULL DEFAULT 'draft',
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `allow_registration` tinyint(1) NOT NULL DEFAULT 1,
  `registration_deadline` datetime DEFAULT NULL,
  `submit_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `author_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_categories`
--

CREATE TABLE `event_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#3498db',
  `icon` varchar(50) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_comments`
--

CREATE TABLE `event_comments` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `comment` text NOT NULL,
  `rating` tinyint(1) DEFAULT NULL,
  `status` enum('pending','approved','rejected','spam') NOT NULL DEFAULT 'pending',
  `is_review` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_email_templates`
--

CREATE TABLE `event_email_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `template_type` enum('registration_confirmation','reminder','cancellation','update') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_files`
--

CREATE TABLE `event_files` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_page_details`
--

CREATE TABLE `event_page_details` (
  `id` int(11) NOT NULL,
  `page_id` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `template` varchar(50) DEFAULT 'default',
  `custom_css` longtext DEFAULT NULL,
  `custom_js` longtext DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_registrations`
--

CREATE TABLE `event_registrations` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `guest_phone` varchar(20) DEFAULT NULL,
  `registration_type` enum('user','guest') NOT NULL DEFAULT 'guest',
  `status` enum('registered','confirmed','cancelled','attended','no_show') NOT NULL DEFAULT 'registered',
  `payment_status` enum('pending','paid','refunded','failed') DEFAULT NULL,
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `special_requirements` text DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `confirmation_date` timestamp NULL DEFAULT NULL,
  `confirmation_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_unavailable_dates`
--

CREATE TABLE `event_unavailable_dates` (
  `id` int(11) NOT NULL,
  `unavailable_date` date NOT NULL,
  `unavailable_label` varchar(255) NOT NULL,
  `event_uid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE `forms` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `settings` text DEFAULT NULL,
  `status` enum('active','inactive','draft') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `submit_count` int(11) DEFAULT 0,
  `last_submission` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `form_analytics`
--

CREATE TABLE `form_analytics` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `date_tracked` date NOT NULL,
  `views` int(11) DEFAULT 0,
  `submissions` int(11) DEFAULT 0,
  `conversion_rate` decimal(5,2) DEFAULT 0.00,
  `bounce_rate` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `form_email_templates`
--

CREATE TABLE `form_email_templates` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `template_type` enum('admin_notification','user_confirmation','autoresponder') NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `subject_line` varchar(255) NOT NULL,
  `email_body` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `send_to` text DEFAULT NULL,
  `send_from` varchar(255) DEFAULT NULL,
  `send_from_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `form_fields`
--

CREATE TABLE `form_fields` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_type` enum('text','email','number','textarea','select','radio','checkbox','file','date','time','url','tel','password','hidden') NOT NULL,
  `field_options` text DEFAULT NULL,
  `field_placeholder` varchar(255) DEFAULT NULL,
  `field_help_text` text DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `validation_rules` text DEFAULT NULL,
  `conditional_logic` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `form_files`
--

CREATE TABLE `form_files` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_filename` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `form_integrations`
--

CREATE TABLE `form_integrations` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `integration_type` enum('mailchimp','zapier','webhook','slack','google_sheets','custom') NOT NULL,
  `integration_name` varchar(255) NOT NULL,
  `configuration` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_sync` timestamp NULL DEFAULT NULL,
  `sync_status` enum('success','failed','pending') DEFAULT 'pending',
  `error_log` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `form_submissions`
--

CREATE TABLE `form_submissions` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `submission_data` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `referrer` varchar(500) DEFAULT NULL,
  `status` enum('new','read','archived','spam') DEFAULT 'new',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_collections`
--

CREATE TABLE `gallery_collections` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description_text` varchar(255) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_media`
--

CREATE TABLE `gallery_media` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description_text` mediumtext NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `uploaded_date` datetime NOT NULL DEFAULT current_timestamp(),
  `media_type` varchar(10) NOT NULL,
  `thumbnail` varchar(255) NOT NULL DEFAULT '',
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_media_collections`
--

CREATE TABLE `gallery_media_collections` (
  `id` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_media_likes`
--

CREATE TABLE `gallery_media_likes` (
  `id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `submit_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_subscribers`
--

CREATE TABLE `group_subscribers` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `subscriber_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `invoice_number` varchar(255) NOT NULL,
  `payment_amount` decimal(7,2) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `payment_methods` varchar(255) NOT NULL DEFAULT 'Cash, PayPal',
  `due_date` datetime NOT NULL,
  `created` datetime NOT NULL,
  `notes` text NOT NULL,
  `viewed` tinyint(1) NOT NULL DEFAULT 0,
  `tax` varchar(50) NOT NULL DEFAULT 'fixed',
  `tax_total` decimal(7,2) NOT NULL DEFAULT 0.00,
  `invoice_template` varchar(255) NOT NULL DEFAULT 'default',
  `payment_ref` varchar(255) NOT NULL DEFAULT '',
  `paid_with` varchar(50) NOT NULL DEFAULT '',
  `paid_total` decimal(7,2) NOT NULL DEFAULT 0.00,
  `recurrence` tinyint(1) NOT NULL DEFAULT 0,
  `recurrence_period` int(11) NOT NULL DEFAULT 0,
  `recurrence_period_type` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_clients`
--

CREATE TABLE `invoice_clients` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT 0,
  `business_name` varchar(200) NOT NULL,
  `description` varchar(1500) NOT NULL,
  `facebook` varchar(150) NOT NULL DEFAULT 'https://facebook.com/#',
  `instagram` varchar(150) NOT NULL DEFAULT 'https://instagram.com/#',
  `bluesky` varchar(150) NOT NULL DEFAULT 'https://bluesky.com/#',
  `x` varchar(150) NOT NULL DEFAULT 'https://twitter.com/#',
  `linkedin` varchar(150) NOT NULL DEFAULT 'https://linkedin.com/#"',
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address_street` varchar(255) NOT NULL,
  `address_city` varchar(255) NOT NULL,
  `address_state` varchar(255) NOT NULL,
  `address_zip` varchar(255) NOT NULL,
  `address_country` varchar(255) NOT NULL DEFAULT 'USA',
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `total_invoices` int(11) NOT NULL DEFAULT 0,
  `issue` varchar(4) NOT NULL DEFAULT 'No',
  `incomplete` varchar(4) NOT NULL DEFAULT 'Yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `item_price` decimal(7,2) NOT NULL,
  `item_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landing_pages`
--

CREATE TABLE `landing_pages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `page_content` text DEFAULT NULL,
  `seo_settings` text DEFAULT NULL,
  `design_settings` text DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `is_homepage` tinyint(1) DEFAULT 0,
  `custom_css` text DEFAULT NULL,
  `custom_js` text DEFAULT NULL,
  `analytics_code` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `published_at` timestamp NULL DEFAULT NULL,
  `view_count` int(11) DEFAULT 0,
  `conversion_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landing_page_analytics`
--

CREATE TABLE `landing_page_analytics` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `date_tracked` date NOT NULL,
  `page_views` int(11) DEFAULT 0,
  `unique_visitors` int(11) DEFAULT 0,
  `bounce_rate` decimal(5,2) DEFAULT 0.00,
  `avg_time_on_page` int(11) DEFAULT 0,
  `conversions` int(11) DEFAULT 0,
  `conversion_rate` decimal(5,2) DEFAULT 0.00,
  `traffic_sources` text DEFAULT NULL,
  `device_breakdown` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landing_page_forms`
--

CREATE TABLE `landing_page_forms` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `form_id` int(11) DEFAULT NULL,
  `form_type` enum('contact','newsletter','lead_capture','survey','custom') NOT NULL,
  `form_settings` text DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `submission_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landing_page_media`
--

CREATE TABLE `landing_page_media` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `media_type` enum('image','video','audio','document') NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_filename` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `usage_context` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landing_page_sections`
--

CREATE TABLE `landing_page_sections` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `section_type` varchar(100) NOT NULL,
  `section_name` varchar(255) DEFAULT NULL,
  `section_content` text NOT NULL,
  `section_settings` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_visible` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landing_page_templates`
--

CREATE TABLE `landing_page_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `preview_image` varchar(500) DEFAULT NULL,
  `template_structure` text NOT NULL,
  `default_content` text DEFAULT NULL,
  `css_framework` varchar(50) DEFAULT 'bootstrap',
  `is_premium` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `usage_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landing_page_variants`
--

CREATE TABLE `landing_page_variants` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `variant_name` varchar(255) NOT NULL,
  `variant_content` text NOT NULL,
  `traffic_percentage` decimal(5,2) DEFAULT 50.00,
  `is_active` tinyint(1) DEFAULT 1,
  `views` int(11) DEFAULT 0,
  `conversions` int(11) DEFAULT 0,
  `conversion_rate` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `attempts_left` tinyint(1) NOT NULL DEFAULT 5,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletters`
--

CREATE TABLE `newsletters` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `attachments` text DEFAULT NULL,
  `last_scheduled` datetime DEFAULT NULL,
  `submit_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date_subbed` datetime NOT NULL,
  `confirmed` tinyint(1) NOT NULL,
  `status` enum('Subscribed','Unsubscribed') NOT NULL DEFAULT 'Subscribed',
  `unsub_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_completion_status`
--

CREATE TABLE `page_completion_status` (
  `id` int(11) NOT NULL,
  `page_path` varchar(255) NOT NULL,
  `page_name` varchar(100) NOT NULL,
  `is_complete` tinyint(1) DEFAULT 0,
  `completion_notes` text DEFAULT NULL,
  `last_checked` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT 1,
  `num_choices` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `polls_categories`
--

CREATE TABLE `polls_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poll_answers`
--

CREATE TABLE `poll_answers` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT 0,
  `img` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poll_categories`
--

CREATE TABLE `poll_categories` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poll_votes`
--

CREATE TABLE `poll_votes` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `blog_user_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `submit_date` datetime NOT NULL DEFAULT current_timestamp(),
  `approved` tinyint(1) NOT NULL,
  `account_id` int(11) NOT NULL DEFAULT -1,
  `likes` int(11) NOT NULL DEFAULT 0,
  `response` text NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_filters`
--

CREATE TABLE `review_filters` (
  `id` int(11) NOT NULL,
  `word` varchar(255) NOT NULL,
  `replacement` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_images`
--

CREATE TABLE `review_images` (
  `id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review_page_details`
--

CREATE TABLE `review_page_details` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scope`
--

CREATE TABLE `scope` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `fee` decimal(10,2) NOT NULL,
  `frequency` varchar(50) DEFAULT NULL,
  `update_date` datetime NOT NULL DEFAULT current_timestamp(),
  `attachment_path` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `section_templates`
--

CREATE TABLE `section_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `section_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `preview_image` varchar(500) DEFAULT NULL,
  `template_html` text NOT NULL,
  `template_css` text DEFAULT NULL,
  `template_js` text DEFAULT NULL,
  `default_content` text DEFAULT NULL,
  `settings_schema` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `is_premium` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `usage_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings_status`
--

CREATE TABLE `settings_status` (
  `id` int(11) NOT NULL,
  `settings_file` varchar(100) NOT NULL,
  `section_name` varchar(100) DEFAULT NULL,
  `setting_key` varchar(100) DEFAULT NULL,
  `is_configured` tinyint(1) DEFAULT 0,
  `is_complete` tinyint(1) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_accounts_config`
--

CREATE TABLE `setting_accounts_config` (
  `id` int(11) NOT NULL,
  `registration_enabled` tinyint(1) DEFAULT 1,
  `email_verification_required` tinyint(1) DEFAULT 1,
  `admin_approval_required` tinyint(1) DEFAULT 0,
  `username_min_length` int(11) DEFAULT 4,
  `username_max_length` int(11) DEFAULT 50,
  `password_min_length` int(11) DEFAULT 8,
  `password_require_special` tinyint(1) DEFAULT 1,
  `password_require_uppercase` tinyint(1) DEFAULT 1,
  `password_require_lowercase` tinyint(1) DEFAULT 1,
  `password_require_numbers` tinyint(1) DEFAULT 1,
  `max_login_attempts` int(11) DEFAULT 5,
  `lockout_duration` int(11) DEFAULT 900,
  `session_lifetime` int(11) DEFAULT 3600,
  `remember_me_enabled` tinyint(1) DEFAULT 1,
  `remember_duration` int(11) DEFAULT 2592000,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `profile_pictures_enabled` tinyint(1) DEFAULT 1,
  `profile_picture_max_size` int(11) DEFAULT 2097152,
  `allowed_image_types` varchar(255) DEFAULT 'jpg,jpeg,png,gif',
  `default_role` varchar(50) DEFAULT 'Member',
  `welcome_email_enabled` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_analytics_config`
--

CREATE TABLE `setting_analytics_config` (
  `id` int(11) NOT NULL,
  `google_analytics_enabled` tinyint(1) DEFAULT 0,
  `google_analytics_id` varchar(50) DEFAULT NULL,
  `google_tag_manager_enabled` tinyint(1) DEFAULT 0,
  `google_tag_manager_id` varchar(50) DEFAULT NULL,
  `facebook_pixel_enabled` tinyint(1) DEFAULT 0,
  `facebook_pixel_id` varchar(50) DEFAULT NULL,
  `hotjar_enabled` tinyint(1) DEFAULT 0,
  `hotjar_id` varchar(50) DEFAULT NULL,
  `custom_analytics_code` text DEFAULT NULL,
  `internal_analytics_enabled` tinyint(1) DEFAULT 1,
  `page_view_tracking` tinyint(1) DEFAULT 1,
  `event_tracking` tinyint(1) DEFAULT 1,
  `user_behavior_tracking` tinyint(1) DEFAULT 1,
  `conversion_tracking` tinyint(1) DEFAULT 1,
  `bounce_rate_tracking` tinyint(1) DEFAULT 1,
  `session_recording` tinyint(1) DEFAULT 0,
  `heatmap_tracking` tinyint(1) DEFAULT 0,
  `a_b_testing_enabled` tinyint(1) DEFAULT 0,
  `data_retention_days` int(11) DEFAULT 365,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_app_configurations`
--

CREATE TABLE `setting_app_configurations` (
  `id` int(11) NOT NULL,
  `app_name` varchar(50) NOT NULL COMMENT 'Application identifier (blog_system, shop_system, etc.)',
  `section` varchar(100) NOT NULL COMMENT 'Configuration section (identity, display, security, etc.)',
  `config_key` varchar(100) NOT NULL COMMENT 'Configuration key name',
  `config_value` text DEFAULT NULL COMMENT 'Configuration value (JSON for complex data)',
  `data_type` enum('string','integer','boolean','json','array','float') NOT NULL DEFAULT 'string' COMMENT 'Data type for proper casting',
  `is_sensitive` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Whether this is sensitive data (passwords, API keys)',
  `description` text DEFAULT NULL COMMENT 'Human-readable description of the setting',
  `default_value` text DEFAULT NULL COMMENT 'Default value for the setting',
  `validation_rules` text DEFAULT NULL COMMENT 'JSON validation rules',
  `display_group` varchar(50) DEFAULT NULL COMMENT 'Admin UI grouping',
  `display_order` int(11) DEFAULT 0 COMMENT 'Order in admin interface',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether setting is active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(100) DEFAULT NULL COMMENT 'User who last updated this setting'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unified application configuration storage';

-- --------------------------------------------------------

--
-- Table structure for table `setting_app_configurations_audit`
--

CREATE TABLE `setting_app_configurations_audit` (
  `id` int(11) NOT NULL,
  `config_id` int(11) NOT NULL,
  `app_name` varchar(50) NOT NULL,
  `section` varchar(100) NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `change_type` enum('CREATE','UPDATE','DELETE') NOT NULL,
  `changed_by` varchar(100) DEFAULT NULL,
  `change_reason` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit trail for configuration changes';

-- --------------------------------------------------------

--
-- Table structure for table `setting_app_configurations_cache`
--

CREATE TABLE `setting_app_configurations_cache` (
  `cache_key` varchar(255) NOT NULL,
  `app_name` varchar(50) NOT NULL,
  `cached_data` longtext NOT NULL COMMENT 'JSON cached configuration data',
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuration cache for performance optimization';

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_comments`
--

CREATE TABLE `setting_blog_comments` (
  `id` int(11) NOT NULL,
  `comment_system` enum('internal','disqus','facebook','disabled') DEFAULT 'internal',
  `require_approval` tinyint(1) DEFAULT 1,
  `allow_guest_comments` tinyint(1) DEFAULT 1,
  `require_registration` tinyint(1) DEFAULT 0,
  `max_comment_length` int(11) DEFAULT 1000,
  `enable_notifications` tinyint(1) DEFAULT 1,
  `notification_email` varchar(255) DEFAULT 'admin@example.com',
  `enable_threading` tinyint(1) DEFAULT 1,
  `max_thread_depth` int(11) DEFAULT 3,
  `enable_comment_voting` tinyint(1) DEFAULT 0,
  `enable_comment_editing` tinyint(1) DEFAULT 1,
  `comment_edit_time_limit` int(11) DEFAULT 300,
  `enable_comment_deletion` tinyint(1) DEFAULT 1,
  `enable_spam_protection` tinyint(1) DEFAULT 1,
  `disqus_shortname` varchar(255) DEFAULT '',
  `facebook_app_id` varchar(255) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_config`
--

CREATE TABLE `setting_blog_config` (
  `id` int(11) NOT NULL,
  `blog_site_url` varchar(255) DEFAULT NULL,
  `sitename` varchar(255) DEFAULT 'GWS Blog',
  `blog_description` text DEFAULT 'Latest news and insights',
  `blog_email` varchar(255) DEFAULT NULL,
  `posts_per_page` int(11) DEFAULT 10,
  `comments_enabled` varchar(20) DEFAULT 'guests',
  `date_format` varchar(50) DEFAULT 'F j, Y',
  `layout` varchar(50) DEFAULT 'Wide',
  `sidebar_position` varchar(20) DEFAULT 'Right',
  `posts_per_row` int(11) DEFAULT 2,
  `theme` varchar(100) DEFAULT 'Pulse',
  `background_image` varchar(255) DEFAULT NULL,
  `featured_posts_count` int(11) DEFAULT 5,
  `excerpt_length` int(11) DEFAULT 150,
  `read_more_text` varchar(100) DEFAULT 'Read More',
  `author_display` tinyint(1) DEFAULT 1,
  `category_display` tinyint(1) DEFAULT 1,
  `tag_display` tinyint(1) DEFAULT 1,
  `related_posts_count` int(11) DEFAULT 3,
  `rss_enabled` tinyint(1) DEFAULT 1,
  `search_enabled` tinyint(1) DEFAULT 1,
  `archive_enabled` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_display`
--

CREATE TABLE `setting_blog_display` (
  `id` int(11) NOT NULL,
  `posts_per_page` int(11) DEFAULT 10,
  `excerpt_length` int(11) DEFAULT 250,
  `date_format` varchar(50) DEFAULT 'F j, Y',
  `layout` enum('Wide','Boxed','Sidebar') DEFAULT 'Wide',
  `sidebar_position` enum('Left','Right','None') DEFAULT 'Right',
  `posts_per_row` int(11) DEFAULT 2,
  `theme` varchar(100) DEFAULT 'Default',
  `enable_featured_image` tinyint(1) DEFAULT 1,
  `thumbnail_width` int(11) DEFAULT 300,
  `thumbnail_height` int(11) DEFAULT 200,
  `background_image` text DEFAULT '',
  `custom_css` text DEFAULT '',
  `show_author` tinyint(1) DEFAULT 1,
  `show_date` tinyint(1) DEFAULT 1,
  `show_categories` tinyint(1) DEFAULT 1,
  `show_tags` tinyint(1) DEFAULT 1,
  `show_excerpt` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_features`
--

CREATE TABLE `setting_blog_features` (
  `id` int(11) NOT NULL,
  `enable_posts` tinyint(1) DEFAULT 1,
  `enable_pages` tinyint(1) DEFAULT 1,
  `enable_categories` tinyint(1) DEFAULT 1,
  `enable_tags` tinyint(1) DEFAULT 1,
  `enable_comments` tinyint(1) DEFAULT 1,
  `enable_author_bio` tinyint(1) DEFAULT 1,
  `enable_social_sharing` tinyint(1) DEFAULT 1,
  `enable_related_posts` tinyint(1) DEFAULT 1,
  `enable_search` tinyint(1) DEFAULT 1,
  `enable_archives` tinyint(1) DEFAULT 1,
  `enable_rss` tinyint(1) DEFAULT 1,
  `enable_sitemap` tinyint(1) DEFAULT 1,
  `enable_breadcrumbs` tinyint(1) DEFAULT 1,
  `enable_post_navigation` tinyint(1) DEFAULT 1,
  `enable_reading_time` tinyint(1) DEFAULT 1,
  `enable_post_views` tinyint(1) DEFAULT 1,
  `enable_newsletter_signup` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting_blog_features`
--

INSERT INTO `setting_blog_features` (`id`, `enable_posts`, `enable_pages`, `enable_categories`, `enable_tags`, `enable_comments`, `enable_author_bio`, `enable_social_sharing`, `enable_related_posts`, `enable_search`, `enable_archives`, `enable_rss`, `enable_sitemap`, `enable_breadcrumbs`, `enable_post_navigation`, `enable_reading_time`, `enable_post_views`, `enable_newsletter_signup`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '2025-08-17 18:04:54', '2025-08-17 18:04:54'),
(2, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '2025-08-17 18:10:26', '2025-08-17 18:10:26'),
(3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, '2025-08-17 19:16:59', '2025-08-17 19:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_identity`
--

CREATE TABLE `setting_blog_identity` (
  `id` int(11) NOT NULL,
  `blog_title` varchar(255) NOT NULL DEFAULT 'My Blog',
  `blog_description` text DEFAULT 'Welcome to my blog',
  `blog_tagline` varchar(255) DEFAULT 'Sharing thoughts and ideas',
  `author_name` varchar(255) DEFAULT 'Blog Author',
  `author_bio` text DEFAULT 'About the author',
  `default_author_id` int(11) DEFAULT 1,
  `blog_email` varchar(255) DEFAULT '',
  `blog_url` varchar(255) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_seo`
--

CREATE TABLE `setting_blog_seo` (
  `id` int(11) NOT NULL,
  `enable_seo_urls` tinyint(1) DEFAULT 1,
  `post_url_structure` varchar(255) DEFAULT '{year}/{month}/{slug}',
  `enable_meta_tags` tinyint(1) DEFAULT 1,
  `enable_open_graph` tinyint(1) DEFAULT 1,
  `enable_twitter_cards` tinyint(1) DEFAULT 1,
  `default_post_image` text DEFAULT '',
  `robots_txt_additions` text DEFAULT '',
  `sitemap_frequency` varchar(20) DEFAULT 'weekly',
  `sitemap_priority` decimal(2,1) DEFAULT 0.8,
  `enable_canonical_urls` tinyint(1) DEFAULT 1,
  `enable_schema_markup` tinyint(1) DEFAULT 1,
  `google_analytics_id` varchar(255) DEFAULT '',
  `google_site_verification` varchar(255) DEFAULT '',
  `bing_site_verification` varchar(255) DEFAULT '',
  `enable_breadcrumb_schema` tinyint(1) DEFAULT 1,
  `enable_article_schema` tinyint(1) DEFAULT 1,
  `default_meta_description` text DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_blog_social`
--

CREATE TABLE `setting_blog_social` (
  `id` int(11) NOT NULL,
  `facebook_url` varchar(255) DEFAULT '',
  `twitter_url` varchar(255) DEFAULT '',
  `instagram_url` varchar(255) DEFAULT '',
  `linkedin_url` varchar(255) DEFAULT '',
  `youtube_url` varchar(255) DEFAULT '',
  `pinterest_url` varchar(255) DEFAULT '',
  `github_url` varchar(255) DEFAULT '',
  `enable_facebook_sharing` tinyint(1) DEFAULT 1,
  `enable_twitter_sharing` tinyint(1) DEFAULT 1,
  `enable_linkedin_sharing` tinyint(1) DEFAULT 1,
  `enable_pinterest_sharing` tinyint(1) DEFAULT 0,
  `enable_email_sharing` tinyint(1) DEFAULT 1,
  `enable_whatsapp_sharing` tinyint(1) DEFAULT 1,
  `enable_reddit_sharing` tinyint(1) DEFAULT 0,
  `twitter_username` varchar(255) DEFAULT '',
  `facebook_app_id` varchar(255) DEFAULT '',
  `social_sharing_position` enum('top','bottom','both','floating') DEFAULT 'bottom',
  `enable_social_login` tinyint(1) DEFAULT 0,
  `facebook_login_enabled` tinyint(1) DEFAULT 0,
  `google_login_enabled` tinyint(1) DEFAULT 0,
  `twitter_login_enabled` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_branding_assets`
--

CREATE TABLE `setting_branding_assets` (
  `id` int(11) NOT NULL,
  `business_logo_main` varchar(255) DEFAULT 'assets/img/logo.png',
  `business_logo_horizontal` varchar(255) DEFAULT 'assets/branding/logo_horizontal.png',
  `business_logo_vertical` varchar(255) DEFAULT 'assets/branding/logo_vertical.png',
  `business_logo_square` varchar(255) DEFAULT 'assets/branding/logo_square.png',
  `business_logo_white` varchar(255) DEFAULT 'assets/branding/logo_white.png',
  `business_logo_small` varchar(255) DEFAULT 'assets/branding/logo_small.png',
  `favicon_main` varchar(255) DEFAULT 'assets/img/favicon.png',
  `favicon_blog` varchar(255) DEFAULT 'assets/branding/favicon_blog.ico',
  `favicon_portal` varchar(255) DEFAULT 'assets/branding/favicon_portal.ico',
  `apple_touch_icon` varchar(255) DEFAULT 'assets/img/apple-touch-icon.png',
  `social_share_default` varchar(255) DEFAULT 'assets/branding/social_default.jpg',
  `social_share_facebook` varchar(255) DEFAULT 'assets/branding/social_facebook.jpg',
  `social_share_twitter` varchar(255) DEFAULT 'assets/branding/social_twitter.jpg',
  `social_share_linkedin` varchar(255) DEFAULT 'assets/branding/social_linkedin.jpg',
  `social_share_instagram` varchar(255) DEFAULT 'assets/branding/social_instagram.jpg',
  `social_share_blog` varchar(255) DEFAULT 'assets/branding/social_blog.jpg',
  `hero_background_image` varchar(255) DEFAULT NULL,
  `watermark_image` varchar(255) DEFAULT NULL,
  `loading_animation` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_branding_colors`
--

CREATE TABLE `setting_branding_colors` (
  `id` int(11) NOT NULL,
  `brand_primary_color` varchar(7) NOT NULL DEFAULT '#6c2eb6',
  `brand_secondary_color` varchar(7) NOT NULL DEFAULT '#bf5512',
  `brand_tertiary_color` varchar(7) DEFAULT '#8B4513' COMMENT 'Third brand color',
  `brand_quaternary_color` varchar(7) DEFAULT '#2E8B57' COMMENT 'Fourth brand color',
  `brand_accent_color` varchar(7) DEFAULT '#28a745',
  `brand_warning_color` varchar(7) DEFAULT '#ffc107',
  `brand_danger_color` varchar(7) DEFAULT '#dc3545',
  `brand_info_color` varchar(7) DEFAULT '#17a2b8',
  `brand_background_color` varchar(7) DEFAULT '#ffffff',
  `brand_text_color` varchar(7) DEFAULT '#333333',
  `brand_text_light` varchar(7) DEFAULT '#666666',
  `brand_text_muted` varchar(7) DEFAULT '#999999',
  `brand_font_primary` varchar(255) DEFAULT 'Inter, system-ui, sans-serif',
  `brand_font_secondary` varchar(255) DEFAULT 'Roboto, Arial, sans-serif',
  `brand_font_heading` varchar(255) DEFAULT 'Inter, system-ui, sans-serif',
  `brand_font_body` varchar(255) DEFAULT 'Roboto, Arial, sans-serif',
  `brand_font_monospace` varchar(255) DEFAULT 'SF Mono, Monaco, Consolas, monospace',
  `brand_success_color` varchar(7) DEFAULT '#28a745',
  `brand_error_color` varchar(7) DEFAULT '#dc3545',
  `custom_color_1` varchar(7) DEFAULT NULL,
  `custom_color_2` varchar(7) DEFAULT NULL,
  `custom_color_3` varchar(7) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `brand_spinner_style` varchar(50) DEFAULT 'rainbow_ring'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_branding_fonts`
--

CREATE TABLE `setting_branding_fonts` (
  `id` int(11) NOT NULL,
  `brand_font_primary` varchar(255) DEFAULT 'Roboto, Poppins, Raleway, Arial, sans-serif',
  `brand_font_headings` varchar(255) DEFAULT 'Poppins, Arial, sans-serif',
  `brand_font_body` varchar(255) DEFAULT 'Roboto, Arial, sans-serif',
  `brand_font_accent` varchar(255) DEFAULT 'Raleway, Arial, sans-serif',
  `brand_font_monospace` varchar(255) DEFAULT 'Consolas, Monaco, "Courier New", monospace',
  `brand_font_display` varchar(255) DEFAULT 'Georgia, "Times New Roman", serif',
  `brand_font_file_1` varchar(255) DEFAULT NULL,
  `brand_font_file_2` varchar(255) DEFAULT NULL,
  `brand_font_file_3` varchar(255) DEFAULT NULL,
  `brand_font_file_4` varchar(255) DEFAULT NULL,
  `brand_font_file_5` varchar(255) DEFAULT NULL,
  `brand_font_file_6` varchar(255) DEFAULT NULL,
  `font_size_base` varchar(10) DEFAULT '16px',
  `font_size_small` varchar(10) DEFAULT '14px',
  `font_size_large` varchar(10) DEFAULT '18px',
  `font_weight_normal` varchar(10) DEFAULT '400',
  `font_weight_bold` varchar(10) DEFAULT '700',
  `line_height_base` varchar(10) DEFAULT '1.5',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_branding_templates`
--

CREATE TABLE `setting_branding_templates` (
  `id` int(11) NOT NULL,
  `template_key` varchar(50) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `template_description` text DEFAULT NULL,
  `css_class` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `template_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`template_config`)),
  `preview_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `setting_branding_templates`
--
DELIMITER $$
CREATE TRIGGER `template_activation_control` BEFORE UPDATE ON `setting_branding_templates` FOR EACH ROW BEGIN
    -- If setting a template to active, deactivate all others
    IF NEW.is_active = TRUE AND OLD.is_active = FALSE THEN
        UPDATE setting_branding_templates 
        SET is_active = FALSE 
        WHERE template_key != NEW.template_key AND is_active = TRUE;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `template_activation_control_insert` BEFORE INSERT ON `setting_branding_templates` FOR EACH ROW BEGIN
    -- If inserting an active template, deactivate all others
    IF NEW.is_active = TRUE THEN
        UPDATE setting_branding_templates 
        SET is_active = FALSE 
        WHERE is_active = TRUE;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `setting_business_contact`
--

CREATE TABLE `setting_business_contact` (
  `id` int(11) NOT NULL,
  `business_identity_id` int(11) NOT NULL DEFAULT 1,
  `primary_email` varchar(255) DEFAULT NULL,
  `primary_phone` varchar(50) DEFAULT NULL,
  `primary_address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'United States',
  `website_url` varchar(255) DEFAULT NULL,
  `business_hours` text DEFAULT NULL,
  `secondary_phone` varchar(50) DEFAULT NULL,
  `fax_number` varchar(50) DEFAULT NULL,
  `mailing_address` varchar(255) DEFAULT NULL,
  `mailing_city` varchar(100) DEFAULT NULL,
  `mailing_state` varchar(50) DEFAULT NULL,
  `mailing_zipcode` varchar(20) DEFAULT NULL,
  `social_facebook` varchar(255) DEFAULT NULL,
  `social_instagram` varchar(255) DEFAULT NULL,
  `social_twitter` varchar(255) DEFAULT NULL,
  `social_linkedin` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_business_identity`
--

CREATE TABLE `setting_business_identity` (
  `id` int(11) NOT NULL,
  `business_name_short` varchar(50) NOT NULL DEFAULT 'GWS',
  `business_name_medium` varchar(100) NOT NULL DEFAULT 'GWS Universal',
  `business_name_long` varchar(200) NOT NULL DEFAULT 'GWS Universal Hybrid Application',
  `business_tagline_short` varchar(100) DEFAULT 'Innovation Simplified',
  `business_tagline_medium` varchar(200) DEFAULT 'Your complete business solution platform',
  `business_tagline_long` text DEFAULT 'Comprehensive hybrid application platform designed to streamline your business operations',
  `legal_business_name` varchar(200) DEFAULT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `established_date` date DEFAULT NULL,
  `about_business` text DEFAULT NULL,
  `mission_statement` text DEFAULT NULL,
  `vision_statement` text DEFAULT NULL,
  `core_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`core_values`)),
  `author` varchar(100) DEFAULT 'GWS',
  `footer_business_name_type` varchar(20) DEFAULT 'medium',
  `footer_logo_enabled` tinyint(1) DEFAULT 1,
  `footer_logo_position` varchar(20) DEFAULT 'left',
  `footer_logo_file` varchar(50) DEFAULT 'business_logo',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_chat_config`
--

CREATE TABLE `setting_chat_config` (
  `id` int(11) NOT NULL,
  `chat_enabled` tinyint(1) DEFAULT 0,
  `chat_widget_position` varchar(20) DEFAULT 'bottom-right',
  `chat_widget_color` varchar(7) DEFAULT '#3498db',
  `chat_welcome_message` text DEFAULT 'Hello! How can we help you today?',
  `chat_offline_message` text DEFAULT 'We are currently offline. Please leave a message and we will get back to you.',
  `chat_auto_assign` tinyint(1) DEFAULT 1,
  `chat_session_timeout` int(11) DEFAULT 30,
  `chat_require_email` tinyint(1) DEFAULT 0,
  `chat_require_name` tinyint(1) DEFAULT 1,
  `chat_enable_file_upload` tinyint(1) DEFAULT 1,
  `chat_max_file_size` int(11) DEFAULT 5,
  `chat_enable_sound_notifications` tinyint(1) DEFAULT 1,
  `chat_enable_email_notifications` tinyint(1) DEFAULT 1,
  `chat_notification_email` varchar(255) DEFAULT '',
  `chat_business_hours_enabled` tinyint(1) DEFAULT 0,
  `chat_business_hours_start` time DEFAULT '09:00:00',
  `chat_business_hours_end` time DEFAULT '17:00:00',
  `chat_business_days` varchar(20) DEFAULT '1,2,3,4,5',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_contact_config`
--

CREATE TABLE `setting_contact_config` (
  `id` int(11) NOT NULL,
  `receiving_email` varchar(255) NOT NULL,
  `email_subject_prefix` varchar(100) DEFAULT '[Contact Form]',
  `email_from_name` varchar(255) DEFAULT 'Contact Form',
  `auto_reply_enabled` tinyint(1) DEFAULT 1,
  `auto_reply_subject` varchar(255) DEFAULT 'Thank you for contacting us',
  `auto_reply_message` text DEFAULT 'We have received your message and will respond as soon as possible.',
  `rate_limit_enabled` tinyint(1) DEFAULT 1,
  `rate_limit_max` int(11) DEFAULT 3,
  `rate_limit_window` int(11) DEFAULT 3600,
  `min_submit_interval` int(11) DEFAULT 10,
  `spam_protection_enabled` tinyint(1) DEFAULT 1,
  `blocked_words` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`blocked_words`)),
  `max_links_allowed` int(11) DEFAULT 2,
  `captcha_enabled` tinyint(1) DEFAULT 1,
  `captcha_type` varchar(50) DEFAULT 'recaptcha',
  `form_fields_required` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`form_fields_required`)),
  `custom_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`custom_fields`)),
  `enable_logging` tinyint(1) DEFAULT 1,
  `redirect_after_submit` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_contact_info`
--

CREATE TABLE `setting_contact_info` (
  `id` int(11) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `contact_address` varchar(255) DEFAULT NULL,
  `contact_city` varchar(100) DEFAULT NULL,
  `contact_state` varchar(100) DEFAULT NULL,
  `contact_zipcode` varchar(20) DEFAULT NULL,
  `contact_country` varchar(100) DEFAULT 'United States',
  `business_hours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`business_hours`)),
  `time_zone` varchar(50) DEFAULT 'America/New_York',
  `contact_form_email` varchar(255) DEFAULT NULL,
  `support_email` varchar(255) DEFAULT NULL,
  `sales_email` varchar(255) DEFAULT NULL,
  `billing_email` varchar(255) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `mailing_address` text DEFAULT NULL,
  `physical_address` text DEFAULT NULL,
  `gps_coordinates` varchar(100) DEFAULT NULL,
  `office_locations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`office_locations`)),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_clients`
--

CREATE TABLE `setting_content_clients` (
  `id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_logo` varchar(255) NOT NULL,
  `client_website` varchar(255) DEFAULT NULL,
  `client_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_features`
--

CREATE TABLE `setting_content_features` (
  `id` int(11) NOT NULL,
  `feature_key` varchar(100) NOT NULL,
  `feature_title` varchar(255) NOT NULL,
  `feature_description` text DEFAULT NULL,
  `feature_icon` varchar(255) DEFAULT NULL,
  `feature_image` varchar(255) DEFAULT NULL,
  `feature_category` varchar(100) DEFAULT NULL,
  `feature_order` int(11) DEFAULT 0,
  `is_highlighted` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_homepage`
--

CREATE TABLE `setting_content_homepage` (
  `id` int(11) NOT NULL,
  `hero_headline` varchar(255) DEFAULT NULL,
  `hero_subheadline` text DEFAULT NULL,
  `hero_button_text` varchar(100) DEFAULT NULL,
  `hero_button_link` varchar(255) DEFAULT NULL,
  `services_section_title` varchar(255) DEFAULT NULL,
  `services_section_description` text DEFAULT NULL,
  `about_section_title` varchar(255) DEFAULT NULL,
  `about_section_subtitle` text DEFAULT NULL,
  `about_section_description` text DEFAULT NULL,
  `about_section_list` text DEFAULT NULL,
  `testimonials_section_title` varchar(255) DEFAULT NULL,
  `testimonials_section_description` text DEFAULT NULL,
  `team_section_title` varchar(255) DEFAULT NULL,
  `team_section_description` text DEFAULT NULL,
  `contact_section_title` varchar(255) DEFAULT NULL,
  `contact_section_description` text DEFAULT NULL,
  `cta_section_title` varchar(255) DEFAULT NULL,
  `cta_section_description` text DEFAULT NULL,
  `cta_button_text` varchar(100) DEFAULT NULL,
  `cta_button_link` varchar(255) DEFAULT NULL,
  `process_section_title` varchar(255) DEFAULT NULL,
  `process_section_description` text DEFAULT NULL,
  `mission_statement` text DEFAULT NULL,
  `value_proposition` text DEFAULT NULL,
  `service_area` varchar(255) DEFAULT NULL,
  `key_differentiator` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(100) DEFAULT 'system',
  `portfolio_section_title` varchar(255) DEFAULT 'Portfolio',
  `portfolio_section_description` text DEFAULT 'Explore some of our recent projects and creative work. Each item showcases our commitment to quality and innovation.',
  `pricing_section_title` varchar(255) DEFAULT 'Pricing',
  `pricing_section_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_portfolio`
--

CREATE TABLE `setting_content_portfolio` (
  `id` int(11) NOT NULL,
  `project_title` varchar(100) NOT NULL,
  `project_description` text NOT NULL,
  `project_category` varchar(50) NOT NULL DEFAULT 'all',
  `project_image` varchar(255) NOT NULL,
  `project_large_image` varchar(255) DEFAULT NULL,
  `project_url` varchar(255) DEFAULT NULL,
  `portfolio_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_pricing`
--

CREATE TABLE `setting_content_pricing` (
  `id` int(11) NOT NULL,
  `plan_key` varchar(100) NOT NULL,
  `plan_name` varchar(255) NOT NULL,
  `plan_description` text DEFAULT NULL,
  `plan_short_desc` varchar(500) DEFAULT NULL,
  `plan_price` varchar(100) NOT NULL,
  `plan_price_numeric` decimal(10,2) DEFAULT NULL,
  `plan_billing_period` varchar(50) DEFAULT 'monthly',
  `plan_currency` varchar(10) DEFAULT 'USD',
  `plan_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plan_features`)),
  `plan_benefits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plan_benefits`)),
  `plan_limitations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plan_limitations`)),
  `plan_button_text` varchar(100) DEFAULT 'Get Started',
  `plan_button_link` varchar(255) DEFAULT '#',
  `plan_icon` varchar(255) DEFAULT NULL,
  `plan_badge` varchar(100) DEFAULT NULL,
  `plan_color_scheme` varchar(50) DEFAULT 'primary',
  `plan_category` varchar(100) DEFAULT 'standard',
  `plan_order` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_popular` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_services`
--

CREATE TABLE `setting_content_services` (
  `id` int(11) NOT NULL,
  `service_title` varchar(255) NOT NULL,
  `service_description` text DEFAULT NULL,
  `service_icon` varchar(255) DEFAULT NULL,
  `service_link` varchar(255) DEFAULT NULL,
  `service_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `service_category` varchar(100) DEFAULT 'foreclosure_help',
  `service_summary` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(100) DEFAULT 'system'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_stats`
--

CREATE TABLE `setting_content_stats` (
  `id` int(11) NOT NULL,
  `stat_value` varchar(20) NOT NULL,
  `stat_label` varchar(100) NOT NULL,
  `stat_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_team`
--

CREATE TABLE `setting_content_team` (
  `id` int(11) NOT NULL,
  `member_name` varchar(100) NOT NULL,
  `member_role` varchar(100) NOT NULL,
  `member_bio` text DEFAULT NULL,
  `member_image` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_testimonials`
--

CREATE TABLE `setting_content_testimonials` (
  `id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_role` varchar(100) NOT NULL,
  `testimonial_text` text NOT NULL,
  `client_image` varchar(255) DEFAULT NULL,
  `testimonial_order` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_email_config`
--

CREATE TABLE `setting_email_config` (
  `id` int(11) NOT NULL,
  `mail_enabled` tinyint(1) DEFAULT 1,
  `mail_from` varchar(255) DEFAULT 'noreply@example.com',
  `mail_name` varchar(255) DEFAULT 'GWS Universal',
  `reply_to` varchar(255) DEFAULT NULL,
  `smtp_enabled` tinyint(1) DEFAULT 0,
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` int(11) DEFAULT 587,
  `smtp_username` varchar(255) DEFAULT NULL,
  `smtp_password` varchar(255) DEFAULT NULL,
  `smtp_encryption` varchar(10) DEFAULT 'tls',
  `smtp_auth` tinyint(1) DEFAULT 1,
  `notifications_enabled` tinyint(1) DEFAULT 1,
  `notification_email` varchar(255) DEFAULT NULL,
  `auto_reply_enabled` tinyint(1) DEFAULT 1,
  `email_templates_path` varchar(255) DEFAULT 'assets/email_templates',
  `email_signature` text DEFAULT NULL,
  `bounce_handling` tinyint(1) DEFAULT 0,
  `email_tracking` tinyint(1) DEFAULT 0,
  `unsubscribe_enabled` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_events_config`
--

CREATE TABLE `setting_events_config` (
  `id` int(11) NOT NULL,
  `events_enabled` tinyint(1) DEFAULT 1,
  `public_events_enabled` tinyint(1) DEFAULT 1,
  `events_per_page` int(11) DEFAULT 12,
  `allow_public_registration` tinyint(1) DEFAULT 1,
  `require_approval` tinyint(1) DEFAULT 0,
  `send_confirmation_emails` tinyint(1) DEFAULT 1,
  `send_reminder_emails` tinyint(1) DEFAULT 1,
  `reminder_days_before` int(11) DEFAULT 1,
  `max_events_per_user` int(11) DEFAULT 0,
  `event_image_max_size` int(11) DEFAULT 5242880,
  `allowed_file_types` varchar(255) DEFAULT 'jpg,jpeg,png,gif,pdf,doc,docx',
  `default_event_duration` int(11) DEFAULT 60,
  `timezone` varchar(50) DEFAULT 'America/New_York',
  `date_format` varchar(20) DEFAULT 'Y-m-d',
  `time_format` varchar(10) DEFAULT 'H:i',
  `calendar_view_default` varchar(20) DEFAULT 'month',
  `enable_recurring_events` tinyint(1) DEFAULT 0,
  `enable_event_categories` tinyint(1) DEFAULT 1,
  `enable_event_ratings` tinyint(1) DEFAULT 1,
  `enable_event_comments` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_footer_special_links`
--

CREATE TABLE `setting_footer_special_links` (
  `id` int(11) NOT NULL,
  `link_key` varchar(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_footer_useful_links`
--

CREATE TABLE `setting_footer_useful_links` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(50) DEFAULT 'bi-link-45deg',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_forms_config`
--

CREATE TABLE `setting_forms_config` (
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json','array') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_landing_pages_config`
--

CREATE TABLE `setting_landing_pages_config` (
  `setting_name` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json','array') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_payment_config`
--

CREATE TABLE `setting_payment_config` (
  `id` int(11) NOT NULL,
  `pay_on_delivery_enabled` tinyint(1) DEFAULT 1,
  `paypal_enabled` tinyint(1) DEFAULT 1,
  `paypal_email` varchar(255) DEFAULT NULL,
  `paypal_testmode` tinyint(1) DEFAULT 1,
  `paypal_currency` varchar(10) DEFAULT 'USD',
  `paypal_ipn_url` varchar(255) DEFAULT NULL,
  `paypal_cancel_url` varchar(255) DEFAULT NULL,
  `paypal_return_url` varchar(255) DEFAULT NULL,
  `stripe_enabled` tinyint(1) DEFAULT 1,
  `stripe_publish_key` varchar(255) DEFAULT NULL,
  `stripe_secret_key` varchar(255) DEFAULT NULL,
  `stripe_currency` varchar(10) DEFAULT 'USD',
  `stripe_webhook_secret` varchar(255) DEFAULT NULL,
  `coinbase_enabled` tinyint(1) DEFAULT 0,
  `coinbase_api_key` varchar(255) DEFAULT NULL,
  `coinbase_secret` varchar(255) DEFAULT NULL,
  `default_currency` varchar(10) DEFAULT 'USD',
  `accepted_currencies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`accepted_currencies`)),
  `payment_timeout` int(11) DEFAULT 1800,
  `payment_confirmation_page` varchar(255) DEFAULT NULL,
  `failed_payment_redirect` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_performance_config`
--

CREATE TABLE `setting_performance_config` (
  `id` int(11) NOT NULL,
  `performance_monitoring` tinyint(1) DEFAULT 1,
  `slow_query_threshold` decimal(5,3) DEFAULT 1.000,
  `memory_limit_mb` int(11) DEFAULT 256,
  `execution_time_limit` int(11) DEFAULT 30,
  `compression_enabled` tinyint(1) DEFAULT 1,
  `minification_enabled` tinyint(1) DEFAULT 1,
  `css_minification` tinyint(1) DEFAULT 1,
  `js_minification` tinyint(1) DEFAULT 1,
  `image_optimization` tinyint(1) DEFAULT 1,
  `lazy_loading_enabled` tinyint(1) DEFAULT 1,
  `cdn_enabled` tinyint(1) DEFAULT 0,
  `cdn_url` varchar(255) DEFAULT NULL,
  `browser_caching_enabled` tinyint(1) DEFAULT 1,
  `cache_control_headers` tinyint(1) DEFAULT 1,
  `gzip_compression` tinyint(1) DEFAULT 1,
  `resource_bundling` tinyint(1) DEFAULT 1,
  `performance_budget_kb` int(11) DEFAULT 2048,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_portal_config`
--

CREATE TABLE `setting_portal_config` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) DEFAULT 'Client Portal',
  `company_name` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT 'assets/img/logo.png',
  `favicon_path` varchar(255) DEFAULT 'assets/img/favicon.png',
  `tagline` varchar(255) DEFAULT NULL,
  `theme_color` varchar(7) DEFAULT '#4154f1',
  `default_language` varchar(10) DEFAULT 'en',
  `timezone` varchar(50) DEFAULT 'America/New_York',
  `date_format` varchar(50) DEFAULT 'Y-m-d',
  `currency` varchar(10) DEFAULT 'USD',
  `enable_blog` tinyint(1) DEFAULT 1,
  `enable_chat` tinyint(1) DEFAULT 0,
  `enable_events` tinyint(4) DEFAULT 0,
  `maintenance_mode` tinyint(1) DEFAULT 0,
  `upload_dir` varchar(255) DEFAULT '/uploads/',
  `max_upload_size` int(11) DEFAULT 10485760,
  `session_timeout` int(11) DEFAULT 7200,
  `dashboard_widgets` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`dashboard_widgets`)),
  `menu_structure` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`menu_structure`)),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_security_config`
--

CREATE TABLE `setting_security_config` (
  `id` int(11) NOT NULL,
  `csrf_protection` tinyint(1) DEFAULT 1,
  `sql_injection_protection` tinyint(1) DEFAULT 1,
  `xss_protection` tinyint(1) DEFAULT 1,
  `rate_limiting_enabled` tinyint(1) DEFAULT 1,
  `max_requests_per_minute` int(11) DEFAULT 60,
  `ip_whitelist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ip_whitelist`)),
  `ip_blacklist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ip_blacklist`)),
  `password_encryption` varchar(50) DEFAULT 'bcrypt',
  `encryption_key` varchar(255) DEFAULT NULL,
  `api_rate_limit` int(11) DEFAULT 1000,
  `api_rate_window` int(11) DEFAULT 3600,
  `file_upload_scanning` tinyint(1) DEFAULT 1,
  `admin_ip_restriction` tinyint(1) DEFAULT 0,
  `admin_allowed_ips` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`admin_allowed_ips`)),
  `login_attempts_tracking` tinyint(1) DEFAULT 1,
  `suspicious_activity_logging` tinyint(1) DEFAULT 1,
  `two_factor_authentication` tinyint(1) DEFAULT 0,
  `session_security_level` varchar(20) DEFAULT 'high',
  `password_history_length` int(11) DEFAULT 5,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_seo_global`
--

CREATE TABLE `setting_seo_global` (
  `id` int(11) NOT NULL,
  `default_title_suffix` varchar(255) DEFAULT ' | GWS Universal',
  `default_meta_description` text DEFAULT 'Professional business solutions and services',
  `default_meta_keywords` varchar(500) DEFAULT NULL,
  `canonical_domain` varchar(255) DEFAULT NULL,
  `google_analytics_id` varchar(50) DEFAULT NULL,
  `google_tag_manager_id` varchar(50) DEFAULT NULL,
  `facebook_pixel_id` varchar(50) DEFAULT NULL,
  `google_site_verification` varchar(255) DEFAULT NULL,
  `bing_site_verification` varchar(255) DEFAULT NULL,
  `yandex_site_verification` varchar(255) DEFAULT NULL,
  `robots_txt_content` text DEFAULT NULL,
  `sitemap_enabled` tinyint(1) DEFAULT 1,
  `sitemap_priority` decimal(2,1) DEFAULT 0.8,
  `sitemap_changefreq` varchar(20) DEFAULT 'weekly',
  `open_graph_type` varchar(50) DEFAULT 'website',
  `twitter_card_type` varchar(50) DEFAULT 'summary_large_image',
  `schema_org_type` varchar(100) DEFAULT 'Organization',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_seo_pages`
--

CREATE TABLE `setting_seo_pages` (
  `id` int(11) NOT NULL,
  `page_slug` varchar(255) NOT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_description` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `og_type` varchar(50) DEFAULT 'website',
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_description` text DEFAULT NULL,
  `twitter_image` varchar(255) DEFAULT NULL,
  `noindex` tinyint(1) DEFAULT 0,
  `nofollow` tinyint(1) DEFAULT 0,
  `schema_markup` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`schema_markup`)),
  `custom_head_code` text DEFAULT NULL,
  `priority` decimal(2,1) DEFAULT 0.5,
  `changefreq` varchar(20) DEFAULT 'monthly',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_shop_config`
--

CREATE TABLE `setting_shop_config` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) DEFAULT 'Shopping Cart',
  `currency_code` varchar(10) DEFAULT '$',
  `currency_symbol` varchar(5) DEFAULT '$',
  `featured_image` varchar(255) DEFAULT 'uploads/featured-image.jpg',
  `default_payment_status` varchar(50) DEFAULT 'Completed',
  `account_required` tinyint(1) DEFAULT 0,
  `weight_unit` varchar(10) DEFAULT 'lbs',
  `rewrite_url` tinyint(1) DEFAULT 0,
  `template_editor` varchar(50) DEFAULT 'tinymce',
  `products_per_page` int(11) DEFAULT 12,
  `low_stock_threshold` int(11) DEFAULT 5,
  `out_of_stock_action` varchar(50) DEFAULT 'hide',
  `tax_enabled` tinyint(1) DEFAULT 0,
  `tax_rate` decimal(5,4) DEFAULT 0.0000,
  `shipping_enabled` tinyint(1) DEFAULT 1,
  `free_shipping_threshold` decimal(10,2) DEFAULT 0.00,
  `inventory_tracking` tinyint(1) DEFAULT 1,
  `reviews_enabled` tinyint(1) DEFAULT 1,
  `wishlist_enabled` tinyint(1) DEFAULT 1,
  `coupon_system_enabled` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_social_media`
--

CREATE TABLE `setting_social_media` (
  `id` int(11) NOT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `tiktok_url` varchar(255) DEFAULT NULL,
  `pinterest_url` varchar(255) DEFAULT NULL,
  `snapchat_url` varchar(255) DEFAULT NULL,
  `discord_url` varchar(255) DEFAULT NULL,
  `github_url` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `blog_url` varchar(255) DEFAULT NULL,
  `shop_url` varchar(255) DEFAULT NULL,
  `booking_url` varchar(255) DEFAULT NULL,
  `calendar_url` varchar(255) DEFAULT NULL,
  `review_platforms` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`review_platforms`)),
  `social_handles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_handles`)),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_system_audit`
--

CREATE TABLE `setting_system_audit` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `changed_by` varchar(100) DEFAULT NULL,
  `change_reason` varchar(255) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_system_config`
--

CREATE TABLE `setting_system_config` (
  `id` int(11) NOT NULL,
  `environment` varchar(50) DEFAULT 'production',
  `debug_mode` tinyint(1) DEFAULT 0,
  `maintenance_mode` tinyint(1) DEFAULT 0,
  `maintenance_message` text DEFAULT 'Site is currently under maintenance. Please check back later.',
  `timezone` varchar(50) DEFAULT 'America/New_York',
  `default_language` varchar(10) DEFAULT 'en',
  `date_format` varchar(50) DEFAULT 'Y-m-d',
  `time_format` varchar(50) DEFAULT 'H:i:s',
  `pagination_limit` int(11) DEFAULT 25,
  `file_upload_limit` int(11) DEFAULT 10485760,
  `allowed_file_types` varchar(500) DEFAULT 'jpg,jpeg,png,gif,pdf,doc,docx',
  `cache_enabled` tinyint(1) DEFAULT 1,
  `cache_duration` int(11) DEFAULT 3600,
  `logging_enabled` tinyint(1) DEFAULT 1,
  `log_level` varchar(20) DEFAULT 'info',
  `error_reporting_level` int(11) DEFAULT 1,
  `backup_enabled` tinyint(1) DEFAULT 1,
  `backup_frequency` varchar(20) DEFAULT 'daily',
  `backup_retention_days` int(11) DEFAULT 30,
  `auto_updates_enabled` tinyint(1) DEFAULT 0,
  `version` varchar(20) DEFAULT '1.0.0',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_system_config`
--

INSERT INTO `setting_system_config` (`id`, `environment`, `debug_mode`, `maintenance_mode`, `maintenance_message`, `timezone`, `default_language`, `date_format`, `time_format`, `pagination_limit`, `file_upload_limit`, `allowed_file_types`, `cache_enabled`, `cache_duration`, `logging_enabled`, `log_level`, `error_reporting_level`, `backup_enabled`, `backup_frequency`, `backup_retention_days`, `auto_updates_enabled`, `version`, `last_updated`) VALUES
(1, 'production', 0, 0, 'Site is currently under maintenance. Please check back later.', 'America/New_York', 'en', 'Y-m-d', 'H:i:s', 25, 10485760, 'jpg,jpeg,png,gif,pdf,doc,docx', 1, 3600, 1, 'info', 1, 1, 'daily', 30, 0, '1.0.0', '2025-08-15 21:00:35');

-- --------------------------------------------------------

--
-- Table structure for table `setting_system_metadata`
--

CREATE TABLE `setting_system_metadata` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `subcategory` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `data_type` enum('string','text','integer','boolean','json','array','file_path','url','email','color','font') NOT NULL,
  `is_required` tinyint(1) DEFAULT 0,
  `default_value` text DEFAULT NULL,
  `validation_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`validation_rules`)),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_discounts`
--

CREATE TABLE `shop_discounts` (
  `id` int(11) NOT NULL,
  `category_ids` varchar(50) NOT NULL,
  `product_ids` varchar(50) NOT NULL,
  `discount_code` varchar(50) NOT NULL,
  `discount_type` enum('Percentage','Fixed') NOT NULL,
  `discount_value` decimal(7,2) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_products`
--

CREATE TABLE `shop_products` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` mediumtext NOT NULL,
  `sku` varchar(255) NOT NULL DEFAULT '',
  `price` decimal(7,2) NOT NULL,
  `rrp` decimal(7,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `weight` decimal(7,2) NOT NULL DEFAULT 0.00,
  `url_slug` varchar(255) NOT NULL DEFAULT '',
  `product_status` tinyint(1) NOT NULL DEFAULT 1,
  `subscription` tinyint(1) NOT NULL DEFAULT 0,
  `subscription_period` int(11) NOT NULL DEFAULT 0,
  `subscription_period_type` varchar(50) NOT NULL DEFAULT 'day'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_product_categories`
--

CREATE TABLE `shop_product_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_product_category`
--

CREATE TABLE `shop_product_category` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_product_downloads`
--

CREATE TABLE `shop_product_downloads` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_product_media`
--

CREATE TABLE `shop_product_media` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `date_uploaded` datetime NOT NULL DEFAULT current_timestamp(),
  `full_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_product_media_map`
--

CREATE TABLE `shop_product_media_map` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_product_options`
--

CREATE TABLE `shop_product_options` (
  `id` int(11) NOT NULL,
  `option_name` varchar(255) NOT NULL,
  `option_value` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(7,2) NOT NULL,
  `price_modifier` enum('add','subtract') NOT NULL,
  `weight` decimal(7,2) NOT NULL,
  `weight_modifier` enum('add','subtract') NOT NULL,
  `option_type` enum('select','radio','checkbox','text','datetime') NOT NULL,
  `required` tinyint(1) NOT NULL,
  `position` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_settings`
--

CREATE TABLE `shop_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_shipping`
--

CREATE TABLE `shop_shipping` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `shipping_type` enum('Single Product','Entire Order') NOT NULL DEFAULT 'Single Product',
  `countries` varchar(255) NOT NULL DEFAULT '',
  `price_from` decimal(7,2) NOT NULL,
  `price_to` decimal(7,2) NOT NULL,
  `price` decimal(7,2) NOT NULL,
  `weight_from` decimal(7,2) NOT NULL DEFAULT 0.00,
  `weight_to` decimal(7,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_taxes`
--

CREATE TABLE `shop_taxes` (
  `id` int(11) NOT NULL,
  `country` varchar(255) NOT NULL,
  `rate` decimal(5,2) NOT NULL,
  `rate_type` varchar(50) NOT NULL DEFAULT 'percentage',
  `rules` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_transactions`
--

CREATE TABLE `shop_transactions` (
  `id` int(11) NOT NULL,
  `txn_id` varchar(255) NOT NULL,
  `payment_amount` decimal(7,2) NOT NULL,
  `payment_status` varchar(30) NOT NULL,
  `created` datetime NOT NULL,
  `payer_email` varchar(255) NOT NULL DEFAULT '',
  `first_name` varchar(100) NOT NULL DEFAULT '',
  `last_name` varchar(100) NOT NULL DEFAULT '',
  `address_street` varchar(255) NOT NULL DEFAULT '',
  `address_city` varchar(100) NOT NULL DEFAULT '',
  `address_state` varchar(100) NOT NULL DEFAULT '',
  `address_zip` varchar(50) NOT NULL DEFAULT '',
  `address_country` varchar(100) NOT NULL DEFAULT '',
  `account_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'website',
  `shipping_method` varchar(255) NOT NULL DEFAULT '',
  `shipping_amount` decimal(7,2) NOT NULL DEFAULT 0.00,
  `discount_code` varchar(50) NOT NULL DEFAULT '',
  `tax_amount` decimal(7,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_transaction_items`
--

CREATE TABLE `shop_transaction_items` (
  `id` int(11) NOT NULL,
  `txn_id` varchar(255) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_price` decimal(7,2) NOT NULL,
  `item_quantity` int(11) NOT NULL,
  `item_options` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_wishlist`
--

CREATE TABLE `shop_wishlist` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `scope_id` int(11) NOT NULL,
  `mine` text DEFAULT NULL,
  `yours` text DEFAULT NULL,
  `file_path` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `msg` mediumtext NOT NULL,
  `full_name` varchar(50) NOT NULL DEFAULT 'Add Name',
  `email` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `ticket_status` enum('open','closed','resolved') NOT NULL DEFAULT 'open',
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'low',
  `category_id` int(11) NOT NULL DEFAULT 1,
  `private` tinyint(1) NOT NULL DEFAULT 1,
  `account_id` int(11) NOT NULL DEFAULT 0,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `last_comment` varchar(50) NOT NULL DEFAULT 'Member',
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `client_ticket` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets_categories`
--

CREATE TABLE `tickets_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets_comments`
--

CREATE TABLE `tickets_comments` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `msg` mediumtext NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `account_id` int(11) NOT NULL DEFAULT 0,
  `new` enum('Admin','Member') DEFAULT NULL,
  `reply` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets_uploads`
--

CREATE TABLE `tickets_uploads` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `filepath` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_active_content`
-- (See below for the actual view)
--
CREATE TABLE `view_active_content` (
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_complete_branding`
-- (See below for the actual view)
--
CREATE TABLE `view_complete_branding` (
`business_name_short` varchar(50)
,`business_name_medium` varchar(100)
,`business_name_long` varchar(200)
,`business_tagline_short` varchar(100)
,`business_tagline_medium` varchar(200)
,`business_tagline_long` text
,`brand_primary_color` varchar(7)
,`brand_secondary_color` varchar(7)
,`brand_accent_color` varchar(7)
,`brand_font_primary` varchar(255)
,`brand_font_headings` varchar(255)
,`brand_font_body` varchar(255)
,`business_logo_main` varchar(255)
,`favicon_main` varchar(255)
,`active_template` varchar(50)
,`template_name` varchar(100)
,`css_class` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_complete_contact`
-- (See below for the actual view)
--
CREATE TABLE `view_complete_contact` (
`contact_email` varchar(255)
,`contact_phone` varchar(50)
,`contact_address` varchar(255)
,`contact_city` varchar(100)
,`contact_state` varchar(100)
,`contact_zipcode` varchar(20)
,`facebook_url` varchar(255)
,`twitter_url` varchar(255)
,`instagram_url` varchar(255)
,`linkedin_url` varchar(255)
,`website_url` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_app_configurations`
-- (See below for the actual view)
--
CREATE TABLE `v_app_configurations` (
`id` int(11)
,`app_name` varchar(50)
,`section` varchar(100)
,`config_key` varchar(100)
,`display_value` mediumtext
,`config_value` text
,`data_type` enum('string','integer','boolean','json','array','float')
,`is_sensitive` tinyint(1)
,`description` text
,`default_value` text
,`display_group` varchar(50)
,`display_order` int(11)
,`is_active` tinyint(1)
,`updated_at` timestamp
,`updated_by` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_app_config_summary`
-- (See below for the actual view)
--
CREATE TABLE `v_app_config_summary` (
`app_name` varchar(50)
,`total_settings` bigint(21)
,`sensitive_settings` bigint(21)
,`empty_settings` bigint(21)
,`sections_count` bigint(21)
,`groups_count` bigint(21)
,`last_updated` timestamp
);

-- --------------------------------------------------------

--
-- Structure for view `view_active_content`
--
DROP TABLE IF EXISTS `view_active_content`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_active_content`  AS SELECT 'service' AS `content_type`, `setting_content_services`.`service_key` AS `content_key`, `setting_content_services`.`service_title` AS `title`, `setting_content_services`.`service_description` AS `description`, `setting_content_services`.`service_icon` AS `icon`, `setting_content_services`.`service_order` AS `display_order` FROM `setting_content_services` WHERE `setting_content_services`.`is_active` = 1union allselect 'feature' AS `content_type`,`setting_content_features`.`feature_key` AS `content_key`,`setting_content_features`.`feature_title` AS `title`,`setting_content_features`.`feature_description` AS `description`,`setting_content_features`.`feature_icon` AS `icon`,`setting_content_features`.`feature_order` AS `display_order` from `setting_content_features` where `setting_content_features`.`is_active` = 1 order by `display_order`  ;

-- --------------------------------------------------------

--
-- Structure for view `view_complete_branding`
--
DROP TABLE IF EXISTS `view_complete_branding`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_complete_branding`  AS SELECT `bi`.`business_name_short` AS `business_name_short`, `bi`.`business_name_medium` AS `business_name_medium`, `bi`.`business_name_long` AS `business_name_long`, `bi`.`business_tagline_short` AS `business_tagline_short`, `bi`.`business_tagline_medium` AS `business_tagline_medium`, `bi`.`business_tagline_long` AS `business_tagline_long`, `bc`.`brand_primary_color` AS `brand_primary_color`, `bc`.`brand_secondary_color` AS `brand_secondary_color`, `bc`.`brand_accent_color` AS `brand_accent_color`, `bf`.`brand_font_primary` AS `brand_font_primary`, `bf`.`brand_font_headings` AS `brand_font_headings`, `bf`.`brand_font_body` AS `brand_font_body`, `ba`.`business_logo_main` AS `business_logo_main`, `ba`.`favicon_main` AS `favicon_main`, `bt`.`template_key` AS `active_template`, `bt`.`template_name` AS `template_name`, `bt`.`css_class` AS `css_class` FROM ((((`setting_business_identity` `bi` join `setting_branding_colors` `bc`) join `setting_branding_fonts` `bf`) join `setting_branding_assets` `ba`) left join `setting_branding_templates` `bt` on(`bt`.`is_active` = 1)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_complete_contact`
--
DROP TABLE IF EXISTS `view_complete_contact`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_complete_contact`  AS SELECT `ci`.`contact_email` AS `contact_email`, `ci`.`contact_phone` AS `contact_phone`, `ci`.`contact_address` AS `contact_address`, `ci`.`contact_city` AS `contact_city`, `ci`.`contact_state` AS `contact_state`, `ci`.`contact_zipcode` AS `contact_zipcode`, `sm`.`facebook_url` AS `facebook_url`, `sm`.`twitter_url` AS `twitter_url`, `sm`.`instagram_url` AS `instagram_url`, `sm`.`linkedin_url` AS `linkedin_url`, `sm`.`website_url` AS `website_url` FROM (`setting_contact_info` `ci` left join `setting_social_media` `sm` on(1 = 1)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_app_configurations`
--
DROP TABLE IF EXISTS `v_app_configurations`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_app_configurations`  AS SELECT `ac`.`id` AS `id`, `ac`.`app_name` AS `app_name`, `ac`.`section` AS `section`, `ac`.`config_key` AS `config_key`, CASE WHEN `ac`.`is_sensitive` = 1 THEN '***SENSITIVE***' ELSE `ac`.`config_value` END AS `display_value`, `ac`.`config_value` AS `config_value`, `ac`.`data_type` AS `data_type`, `ac`.`is_sensitive` AS `is_sensitive`, `ac`.`description` AS `description`, `ac`.`default_value` AS `default_value`, `ac`.`display_group` AS `display_group`, `ac`.`display_order` AS `display_order`, `ac`.`is_active` AS `is_active`, `ac`.`updated_at` AS `updated_at`, `ac`.`updated_by` AS `updated_by` FROM `setting_app_configurations` AS `ac` WHERE `ac`.`is_active` = 1 ORDER BY `ac`.`app_name` ASC, `ac`.`display_group` ASC, `ac`.`display_order` ASC, `ac`.`section` ASC, `ac`.`config_key` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_app_config_summary`
--
DROP TABLE IF EXISTS `v_app_config_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_app_config_summary`  AS SELECT `setting_app_configurations`.`app_name` AS `app_name`, count(0) AS `total_settings`, count(case when `setting_app_configurations`.`is_sensitive` = 1 then 1 end) AS `sensitive_settings`, count(case when `setting_app_configurations`.`config_value` is null or `setting_app_configurations`.`config_value` = '' then 1 end) AS `empty_settings`, count(distinct `setting_app_configurations`.`section`) AS `sections_count`, count(distinct `setting_app_configurations`.`display_group`) AS `groups_count`, max(`setting_app_configurations`.`updated_at`) AS `last_updated` FROM `setting_app_configurations` WHERE `setting_app_configurations`.`is_active` = 1 GROUP BY `setting_app_configurations`.`app_name` ORDER BY `setting_app_configurations`.`app_name` ASC ;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
