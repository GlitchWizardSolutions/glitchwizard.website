-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 17, 2025 at 01:25 AM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetBrandingConfigJSON` ()   BEGIN
    SELECT JSON_OBJECT(
        'business_identity', JSON_OBJECT(
            'business_name_short', business_name_short,
            'business_name_medium', business_name_medium,
            'business_name_long', business_name_long,
            'business_tagline_short', business_tagline_short,
            'business_tagline_medium', business_tagline_medium,
            'business_tagline_long', business_tagline_long
        ),
        'brand_colors', JSON_OBJECT(
            'primary', brand_primary_color,
            'secondary', brand_secondary_color,
            'accent', brand_accent_color,
            'background', brand_background_color,
            'text', brand_text_color
        ),
        'brand_fonts', JSON_OBJECT(
            'primary', brand_font_primary,
            'headings', brand_font_headings,
            'body', brand_font_body
        ),
        'brand_assets', JSON_OBJECT(
            'logo_main', business_logo_main,
            'favicon', favicon_main
        ),
        'active_template', JSON_OBJECT(
            'key', active_template,
            'name', template_name,
            'css_class', css_class
        )
    ) as branding_config
    FROM view_complete_branding;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetSettingsByCategory` (IN `category_name` VARCHAR(100))   BEGIN
    SELECT setting_key, default_value, data_type, description
    FROM setting_system_metadata 
    WHERE category = category_name
    ORDER BY subcategory, setting_key;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateSettingWithAudit` (IN `p_setting_key` VARCHAR(255), IN `p_new_value` TEXT, IN `p_changed_by` VARCHAR(100), IN `p_change_reason` VARCHAR(255))   BEGIN
    DECLARE old_value TEXT;
    DECLARE table_name VARCHAR(100);
    
    -- Get current value and table name
    SELECT sm.default_value, sm.table_name 
    INTO old_value, table_name
    FROM setting_system_metadata sm 
    WHERE sm.setting_key = p_setting_key;
    
    -- Insert audit record
    INSERT INTO setting_system_audit (setting_key, old_value, new_value, changed_by, change_reason)
    VALUES (p_setting_key, old_value, p_new_value, p_changed_by, p_change_reason);
    
    -- Update the metadata default value
    UPDATE setting_system_metadata 
    SET default_value = p_new_value, last_updated = NOW(), updated_by = p_changed_by
    WHERE setting_key = p_setting_key;
    
END$$

DELIMITER ;

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
  `website_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `first_name`, `last_name`, `email`, `phone`, `address_street`, `address_city`, `address_state`, `address_zip`, `address_country`, `registered`, `role`, `access_level`, `document_path`, `full_name`, `rememberme`, `activation_code`, `last_seen`, `method`, `social_email`, `reset_code`, `password`, `tfa_code`, `ip`, `approved`, `blog_user`, `avatar`, `banned`, `website_url`) VALUES
(1, 'Dio', 'please', 'update', 'sidewaysy.tasks@gmail.com', '\'\'', '127 Northwood Road', 'Crawfordville', 'FL', '32327', 'USA', '2025-07-02 23:38:00', 'Blog_User', 0, 'Welcome/', 'please update', '$2y$10$esagPd1Lo4sKApSjRvoLVOwX3gaFaRlfVx6QgVKPD21jkPjGobA36', 'activated', '2025-08-13 10:51:02', 'password', 'please update', 'bfee4b3c9490b4ad70e051280a452269570f94f3e403ca66d83f658a61246d19', '$2y$10$Zy64yPZ7YQRI11cEpnFc7u7E5/j/Hcp6151knKsnrZQkQiRhwHM6C', '\'\'', '\'\'', '1', 1, 'default-developer.svg', 0, NULL),
(3, 'GlitchWizard', 'Glitch', 'Wizard', 'webdev@glitchwizardsolutions.com', '(850) 294-4226', '127 Northwood Rd', 'Crawfordville', 'FL', '32327', 'United States', '2024-09-10 10:32:00', 'Developer', 0, 'Barbara_Moore', 'Glitch Wizard', '$2y$10$OPJqs7NOIGg/Pwag7is2C.RJUqSM4VZ4Sbfxld.Z3p4sUSoT/YzGC', 'activated', '2025-08-16 19:01:46', 'password', 'sidewaysy@gmail.com', '\'\'', '$2y$10$Qr0AlGEglzRepKFncvVrKuCzeDWORE4UsQ4ZzmucEnH/l1/ein7a2', 'DC1955', '75.229.47.137', '1', 1, 'default-developer.svg', 0, NULL),
(48, 'Joseph', 'Joseph', 'Gross', 'cherokeejoey@gmail.com', '18502944226', '127 Northwood Road', 'Crawfordville', 'FL', '32327', 'USA', '2025-08-01 01:34:35', 'Member', 0, 'Welcome/', 'Joseph Gross', '\'\'', 'activated', '2025-08-14 19:22:38', 'password', 'please update', '272f5ad258a5d3f8ad4e59848a7d31eacdeecea761b8346a184a54aec75d98c1', '$2y$10$F5g51ASqGAl0KceMVGxyRO0bXOy5sA0X1UVjWZaw55mrdv7nPKqKK', '\'\'', '\'\'', '1', 1, 'default.svg', 0, NULL);

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

--
-- Dumping data for table `blog_albums`
--

INSERT INTO `blog_albums` (`id`, `title`) VALUES
(1, 'General'),
(3, 'Barbara');

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`id`, `category`, `slug`) VALUES
(1, 'Site News', 'site-news'),
(2, 'Resources', 'resource');

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

--
-- Dumping data for table `blog_comments`
--

INSERT INTO `blog_comments` (`id`, `account_id`, `post_id`, `username`, `comment`, `date`, `time`, `approved`, `guest`, `ip`) VALUES
(2, 3, 1, 'GlitchWizard', 'This is a test of the emergency broadcast system.', '2025-08-04 19:34:08', '01:15', 'Yes', 'No', '75.229.47.137'),
(4, 3, 1, 'GlitchWizard', 'This is a test. as well.', '2025-08-01 19:47:22', '16:15', 'Yes', 'No', '75.229.171.240');

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

--
-- Dumping data for table `blog_files`
--

INSERT INTO `blog_files` (`id`, `filename`, `date`, `time`, `path`) VALUES
(3, 'heart.jpg', 'July 25, 2025', '00:26', '../../blog_system/assets/downloadable/heart-07-25-2025.jpg');

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

--
-- Dumping data for table `blog_gallery`
--

INSERT INTO `blog_gallery` (`id`, `category_id`, `album_id`, `title`, `image`, `description`, `active`) VALUES
(1, 1, 1, 'New Gallery Image with Name intact', 'blog_system/assets/uploads/img/gallery/heart.jpg', '&lt;p&gt;&lt;font color=&quot;#000000&quot; style=&quot;background-color: rgb(255, 255, 0);&quot;&gt;&lt;b&gt;Description&lt;/b&gt;&lt;/font&gt;&lt;/p&gt;', 'Yes'),
(2, 2, 1, 'ddd', 'blog_system/assets/uploads/img/gallery/two.jpg', '&lt;p&gt;&lt;font color=&quot;#000000&quot; style=&quot;background-color: rgb(255, 255, 0);&quot;&gt;&lt;b&gt;&lt;u&gt;ggg&lt;/u&gt;&lt;/b&gt;&lt;/font&gt;&lt;/p&gt;', 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `blog_gallery_categories`
--

CREATE TABLE `blog_gallery_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_gallery_categories`
--

INSERT INTO `blog_gallery_categories` (`id`, `name`, `slug`) VALUES
(1, 'Misc', 'misc'),
(2, 'Site', 'site');

-- --------------------------------------------------------

--
-- Table structure for table `blog_gallery_image_tags`
--

CREATE TABLE `blog_gallery_image_tags` (
  `image_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_gallery_image_tags`
--

INSERT INTO `blog_gallery_image_tags` (`image_id`, `tag_id`) VALUES
(1, 1),
(1, 2),
(2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `blog_gallery_tags`
--

CREATE TABLE `blog_gallery_tags` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_gallery_tags`
--

INSERT INTO `blog_gallery_tags` (`id`, `name`, `slug`) VALUES
(1, 'People', 'people'),
(2, 'Places', 'places'),
(3, 'Things', 'things');

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

--
-- Dumping data for table `blog_menu`
--

INSERT INTO `blog_menu` (`id`, `page`, `path`, `fa_icon`) VALUES
(1, 'Posts', 'blog', 'fa-blog'),
(2, 'Gallery', 'gallery', 'fa-images'),
(3, 'Contact', 'contact', 'fa-envelope');

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

--
-- Dumping data for table `blog_messages`
--

INSERT INTO `blog_messages` (`id`, `name`, `email`, `content`, `date`, `viewed`) VALUES
(1, 'Testing Name', 'sidewaysy@gmail.com', 'Contents of the Email', '2025-07-24 14:28:59', 'Replied');

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

--
-- Dumping data for table `blog_newsletter`
--

INSERT INTO `blog_newsletter` (`id`, `email`, `updated`, `ip`) VALUES
(1, 'sidewaysy@gmail.com', '2025-07-05 20:28:04', ''),
(2, 'webdev@glitchwizardsolutions.com', '2025-07-05 20:28:04', ''),
(3, 'sidewaysy.onlineorders@gmail.com', '2025-07-05 20:28:04', '');

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

--
-- Dumping data for table `blog_pages`
--

INSERT INTO `blog_pages` (`id`, `title`, `slug`, `content`, `active`) VALUES
(4, 'Page', 'page', '&lt;p&gt;This is a test page.&lt;img src=&quot;data:image/jpeg;base64,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p6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD3+iiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArK1nxFpegQiTUbpYyw+WMcu30FUPGfihPDGjecgD3kx2W6Hpnux9hXgt5e3Oo3kl1eTvNPIcs7H/OB7Vz1q/s9Fue5lWTvGL2lR2h+LPVLn4v2auRa6VPIvZnkC5/DmpbL4uabLIFvNPuLdT/GpDgfhXkHaiuP6zUvufTf6v4Bxtyv1uz6Z07U7LVrRbqxuEnhb+JD09j6GrdfO3hjxJdeGtVS6hZmt2IE8OeHX/Edq+g7W5hvLSK5gcPDKgdGHcGu6jWVReZ8nmuVywNRWd4vZ/oyaiiitjyQooooAKKKqalfxaZplzfTH93BGXPvgdKG7DjFyait2ZPinxdY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JcsWz4TAUFXxEab2PMby9udRvZby7laW4mbc7nuf8KhpopwrzGffwSSshwp1NFOqWbxFpaSlqTaItFFFSaoK9W+D8Z+zarL2Mkaj8jXlNe1fCm0MPhN5yObi4Zh9Bgf410YVXqI8TiGajgZLu0vxv+h3VFFFeofnwUUUUAFFFFABRRWfe65pWnHF5qFtA3915AD+VJtLcqEJTdoq7NCisWHxd4euHCRaxaMx6DzAP51sRyJKgeN1dD0ZTkGhST2KnRqU/ji16odRRRTMwooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigCK5uIbS2kuLiRY4Y1LO7HAAFeQ+Jfihe3kr2+iE2tsDjzyP3j+4/uj9au/FrXZPOt9DhciPb50+D97+6D/P8q8wrhxFd35Yn12SZTTdNYisrt7LoWZ768unMk93cSuepeQmpbPWNS06USWd/cQsP7shx+VUu1Fcd3e59X7ODjytKx7D4J+IZ1adNM1fYl43EUyjCyn0I7H+deh18uo7RuroxV1IZWB5BHQ19D+Eta/t/w3a3rEedjZNj++OD+fX8a78NWc/dlufFZ/lcMM1Xoq0Xo12f/BNuiiius+bCiiigAooooAKK5fWfH+gaNI0L3RuJ14MduN2D7npXOt8YLIPhdIuSvqZVFZSrU4uzZ6FHKsZW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Wi9EFFFFbHmHIeNfA1v4oiW4hdYNRjXCSEcOP7rf4145qnhTXNGdheadMEH/AC0jXeh/EV9JUVjUoRm7nq4LN62GjyW5onyt0ODwfQ06vpe70LSb9SLrTrWXPUtEM/nXP3nwy8M3eSlrJbMe8MhAH4HIrnlhZdGe3S4ioP8AiRa/H/I8Jpa9Q1P4QMqM+l6luYdI7hcZ/wCBD/CvPtV0bUNEuvs+o2rwP/CTyrfQ9DXNOlOG6PbwmYYbE6UpXfbqUaKKVFaR1RFLOxCqB3J6Vmej0PTfhHpG6a91eReFHkRE+vVj/L869WrI8M6Quh+HrOwA+dEzIfVzyf1rXr1qMOSCR+ZZnivrWKnUW2y9EFFFQXl5b2FpLdXUqxQRLud2PAFanCk27Inqtc6jZWf/AB83cEP/AF0kA/nXjXij4m6hqsr2+kO9lZA43jiSQeuf4R7CuFd3mcvK7SOerOck/ia5Z4lJ2irnv4bIKlSPNVly+W7Pp221XT7xttte28zeiSgmvGPik7N41kVuiwRhfyzXFITG4dGKMOjKcEfjVq91G71KSOW9naeSNBGrvydo6ZPesKtfnjZo9fAZR9Ur+1jK6tYr0opKUVyn0MQooopGhJBM1vcRTocPG4dT7g5r6btZhc2kM46Sxq4/EZr5fPQ19JeHWL+GtMY9Tax/+giu3BvVo+U4pguSnPza/I06KKK7z44KKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiii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HXxAGmGTS9IkDXmMSzjkQ+w/2v5V4+7vJI0kjM7scszHJJ9TXPXxHL7sdz28pyN1kq2I+Hou/r5F3VtZv9cvDdahcNLIfujoqD0A7VRpKWuBtt3Z9jCEYRUYqyQlLSUtIsKKKKCgooopDCiiigAooooAKKKKACiiigAooooA6nwz471Pw6ywsxurHPMEjcr/ALp7fTpXs2heIdO8Q2YubCYNj78bcOh9CK+cKt6bqd5pF6l5YztDMh4I6EehHcV00sRKGj1R4mZZJRxac6fuz/B+v+Z614r+GVprNxJfaZKtnducuhH7uQ+vsa86vvAHibT2O7TXmUfx27Bx/j+leseD/G1r4mg8iXbBqKD54c8P/tL/AIdq6yuv2VOouaJ81HMcbgJewqq9u/8AmfLs9ndWjbbm2mhb0kjK/wA6iFfUkkMUybJY0kU9Q6gisW98GeHb/Jn0m3DH+KNdh/TFZSwj6M9CjxJD/l5Br0d/8j53pRXq2t/CSBkaXRbto3HIhnOVP0bqK8z1DTbzSb17S+t3gmX+Fh1HqD3FctSlKG57+CzDD4tfupa9upVp8UTzzRwxLukkYIoHck4FMrtfhlon9peJPtsi5gsRv5HBc/dH8z+FTCLlJROnFYiOHoSqy6I9g0TTU0jRbPT4+kEQUn1Pc/nmr9FFewlZWR+Wzm5ycpbsKKwde8Y6N4dUi9ugZ8ZEEXzOfw7fjXnOq/F7Up2ZdMs4bWPs8vzt+XSonVhHdnZhstxOIV4R07vQ9kor50ufG/iW6JMmsXCg9oyEH6VWHifXgcjWb7/v81Y/Wo9j048PVmtZr8T6Uor56tfH3ie0IK6rJIB/DMocfyrp9M+L15GypqmnxzL3eA7W/I8VUcTB76GNXIcVBXjaXp/wT16isLQ/F+i+IAFsrsCfHMEvyuPw7/hW7W6aaujyKlKdKXLNWfmFFFFMzCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAryL4qeE2inPiGzjzG+FulUfdPQP8ATsa9dpksUc8LxSorxupVlYZBB7GoqQU42Z1YPFSwtVVI/PzR8rinCvU/EHwjLTPPoVyioxz9mnPA9lb0+tcZeeCPEtgT5ukzso/iiw4/SvPnSnHdH22HzHDVknGa9HozBpRT5YJrdts8MkTekiFT+tMFYs9ODuroWlpKWpN4hXpPwj0vzL++1R1+WJBDGfc8n9APzrzavoPwTo/9i+FbO3dcTSL50v8AvNz/ACxXRhYc079jxeIcT7HCOC3lp8uv9eZ0NFFFemfn4UUVg6r4z0DR9y3WoxGQf8s4jvb8hSbS3Lp0p1HywTb8jeorzDUPjFbLuXTdMkl9HnfaPyGTXL3vxR8S3eRFNBaqe0UQJ/M5rGWIgj06WSYupurerPd6Y80Uf+skRf8AeYCvm268Ta7en/SNWvHHp5pA/IVmvPNIcvNK3+85NZvFLojuhw7J/FU/A+njqNipwby3H1lX/GkGpWJOBe2x+kq/418v4B6jNGAOgpfWn2Nf9XI/8/Pw/wCCfUUgtb63kt3Mc0UilXXIIINcd4N8Ef8ACPa5qN5N8yhvLsyevlnkn69vwNeJJNLGcpNIv+65FaVr4l1yyI+z6teIB280kfkal14yaclsawyavSpTpUqukt7r/gn0pRXhVj8UPElpgTSwXSjtLHg/mMV1Wm/F+zkwupadLAe7wtvH5cGt44imzyK2SYynqlzeh6Uyq6MjgMrDBBHBFeOeL/hjd2tzJe6FEZ7VyWa2X78f+76j9a9N0rxRoutAfYdQhkc/8sydr/kea16ucI1Ec2HxOIwNTRW7pnyxNDLbyGOeJ4pB1WRSp/WkFfT91p9lfLtu7SGcekkYb+dc/efDvwxeZJ01YW9YHKfp0rmlhZdGe/R4ipP+JBr01/yPAaWvXLz4P2D5NlqVxCeyyqHH58VzOqfC/XtPQyW3k3yDkiI4b8j1rCVCouh6tDN8HVdlOz89P+AcVS06WGW3laKaN45FOGR1wR+FNrBnsRd9UFdd8ONI/tPxbDI65hsx5z+mei/r/KuRr234Y6L/AGb4a+2SLie+bzOeoQcL/U/jW2Hhz1EeZnWK+r4OTW8tF8/+AdvXAfEHxv8A2PE2ladJ/p8i/vJB/wAsVP8A7Mf0rc8ZeKI/DOjtKuGvJspbxnuf7x9hXgM88t1cSTzyNJNIxZ3Y8sTXXiK3KuWO581keVqvL29Ve6tl3f8AkiMkkkkkknJJPWiiivOPtgpaSloASlpKWgYUUUUFBRRRSGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUASW881rcRz28jRTRtuR1OCDXuHgfxpH4ktfs10Vj1KJfnXoJB/eH9RXhdT2d5cafeRXdrIY54m3Iw7GtqVV03foebmeW08dS5XpJbP+uh9O0Vg+FPEsHibR1ukwlwnyTxf3W/wPat6vVjJSV0fnNWlOjN05qzQVi+JfDVl4l01re4ULMozDMB80bf4eoraooaUlZhSqzpTU4OzR8y6jp1zpWozWN1GVnhbaR6+hHsa928D6F/YPhqCGRcXM376b/ePb8BgUuteELPWfEGm6rLgNat+9XH+tA5UH6Gujrno0OSTb+R7ma5x9cw9OnHR7y9f61+4QkKCSQAOSTXk/jX4nP5kmm6BJtCnbJeDv6hP8fyqT4oeMnjZvD+nylWI/0uRTyAf4B/WvJ+1TWrO/LE1yrK4uKr1lfsv1Y93eWRpJHZ3Y5ZmOST7mim0tcZ9MhaWkpaCkApaQUtItDlZkdXRmV1OQynBB+tek+EPibNbPHY685lgPypd4+ZP971Hv1rzWiqhUlB3RhicJRxUOSqr/AJr0PqSOVJollidXjcZVlOQRT68W+HvjVtIuk0rUJCdPlbEbsf8AUsf/AGU/pXtPWvSpVFUV0fCY/AzwdXklquj7hRRRWhwhRRVa51Czsxm6u4If+ukgX+dF7DUXJ2SLNFYreLvDyNtOsWmf+ulWbfXtIuziDU7RyewlXNTzx7mrw9aKu4P7maNFICCMg5B7ilqjEKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigCKa2guVKzwRyqezoGH61z+peAvDmpIwbTo4HPSS3+Qj8uP0rpaKlxjLdGtKvVpO9OTXozw7xR8Or/AEKN7uzY3livLEL88Y9x3HuK4yvqMgEEEZB6g14x8RvCKaNdjVLGPbY3DYdAOIn/AMDXDiMPyrmifYZNnUq8lQr/ABdH38n5mL4J0Q654otoGXNvCfOm9No7ficCvoKuI+Geg/2X4f8At0yYub7D8jkJ/CP6/jT/ABV8RtN0AvbWuL2/HHlo3yIf9o/0FbUEqVO8up5Wb1amPxvsqKuo6f5s6+4uIbWBpriVIokGWd2AA/GvPte+LOnWZaHSITeyjjzW+WMf1NeXa34k1XxDceZqN0zqDlYl4RPoKyhUTxLekTrwmRU4+9Xd326G/rHjPXtcLC7vnWE/8sYfkT9Ov41g+9FFczbb1Pdp0oU1ywVl5BS0lLSNAooooGhaKKKBhS0lLSGFFFFAxQSpDKSGHQg4IrqdE+IOvaMVT7T9rtx/yyuDu49m6iuWopqTjqjOrQp1o8tSKaPdvD/xG0bWysMz/Ybs8eXMflY+zdPzrsByMjnNfLFdd4Z+IGq+H2SCVmvLEcGGRvmUf7J7fTpXVTxXSZ87jeH9HPDP5P8AR/5/ee9UVl6H4h07xDZC5sJw+Pvxnh0PoRWpXYmmro+YnCVOTjNWaMDxL4S03xLalbiMR3Kj93cIPmU+/qPavCta0W80HU5LG9TEi8qw+669mFfSlcv458Mp4i0N/LQfbrcF4G7n1X6H+dc+IoKa5lue3k2bSw1RUqj9x/h/wO54v4d0d9d16109Adsj5kI/hQcsfyr6Id7fTrAu5WK2t48k9lUCuE+Fnh82Wmy6vcRlZrr5Igw5WMH+p/lVX4reIjFbxaFbvh5QJLjB6L2X8Tz+FRRSpUud7s68znLMcwjhqb92P9N/oef+KPEE3iTXJr5yRCPkgjP8CDp+J6msekpa4pNt3Z9VSpxpQUIKyQUUUVJqFLSUtACUtJS0DCiiigoKKKKQwooooAKKKKACiiigAooooAKKKKACiiigAooooA3fCfiKXw1rkd2CTbv8lwg/iT1+o619CQzR3EEc0Lh45FDKw6EGvl6vXvhX4gN1p8uizvmW2+eHJ6xnqPwP867MLVs+RnzHEeAU6f1qC1W/p/wD0aiiivQPigrJ8S6ymgeH7vUXwWiT92p/ic8KPzrWryr4yaoQmnaUjcMTPIM+nC/1qKkuWLZ14Gh7fERpvbr6HlU08tzcSXE7l5ZWLux7k9aZSUteYferTYWlpKWkWhaWkpaCkApaQUtItC0UUUi0Fe6fDbxA2s+Hvs1w+66siI2JPLL/AAn+n4V4ZXbfC6+a18Xrb5wl1CyEepHI/ka2oT5ZrzPLznDKvhJPrHVfr+B7hXO+JPGWleGk23EhmuiMrbRHLH6+g+tYHjj4grpJk0zSXV77pLN1WH2Hq38q8ellknmeaaRpJXOWdzkk+5rorYnl92O54eV5G66VWvpHour/AMkdXrXxF17VmZIp/sNuekcBw2Pduv5YrlJJHmcvK7SMeSXOSaYKWuGU5S1bPsaGGo0I8tKKQmB6UoAzkcUUVB0Glp3iDV9KcNY6jcRAfwh8qfwPFd5ofxZlVlh1u2Dr0+0QDBH1X/CvMaK0hVnDZnFisuw2JX7yCv32f3n0zp+pWeq2i3VjcJPC3RkPT2Poat183aJr2oeH70XVhMUP8cZ+5IPQivdPC/iiz8T6f50B8u4TAmgJ5Q/1HvXoUa6qaPc+KzTJqmC9+PvQ79vX/M3aKKK6DxQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKo6xbWF5pNxDqez7GVzKXOAAOc57VPeXlvYWkt1dSrFBEu53Y8AV4P418c3Pie5Nvbl4dLRvkjzgyf7Tf0FZVakYLU9DL8FVxNRODsl17enma/jD4lS3obTdBZreyUbGuBw8g9F/uj9a8670UVwSm5O7PssPhqeHhyU1/wAH1FoooqToFooopDClpKWgYUUUUDQtFFFAwpaSlpDCiiigYtFFFA0JS0lLSGXNM1W90a+S8sJ2hmTuOjD0I7ivc/B/jO08UWuxtsOoRrmWDPX/AGl9R/KvAKns7y40+8iu7SVop4m3I6npW1Kq6b8jzsxy2njIdpLZ/wCfkfUFFc34O8VweKNL38R3sOBPFnof7w9jXSV6UZKSuj4KtRnRm6dRWaK15dQabp811LhIYELtjjAFfN+q6lNrGq3OoTk+ZPIWx6DsPwFeq/FjWfsujwaVE2JLtt0mD/Av+J/lXj1cOKneXKuh9fw7heSi68t5bei/4IlLSUtch9GFFFFIoKWkpaAEpaSloGFFFFBQUUUUhhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAVqeHtWfQ9etNQQnbG+JB6oeGH5Vl0tNNp3RFSEakHCWz0PqGORZY1kQ7kcBlI7g06uT+HWqHU/CFsrtmW1Jgb1wOn6EV1lezCXNFSPyzE0XQrSpPo7BXz58R9Q/tDxve4OUt9sC+2Bz+pNfQTMFUsegGTXy3qM7XWq3lwxy0k7sT/AMCNYYl6JHsZDTvVlPsvz/4YrUtJS1xH1aFpaSlpFIWlpKWgpAKWkFLSLQtFFFItC1Zsb64028ju7SQxzx52uO2QR/Wq1L2o6jaUlZ7CklmLMSzE5JJ5JpKKKRogFLSClpFIKKKKRQUUUUAFaGi6zd6DqkV/Zvh0PzLnh17qaz6KabTuiZwjOLjJXTPpXRtWttc0qDULVsxyryO6nuD7ir9eM/C7X2sNZbSpn/0e85TP8Mg/xH9K9mr1qNT2kLn5tmmBeDxDp9N16BRRRWp5wUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABTJJEhjaSRgkaAszMcAAd6fXkfxR8YmSRvD1hL8i/8fbqep/uf41E5qCuzqweFniaqpx+fkjA8e+NZPEl6bS0dl0uFvkHTzm/vH29BXG0lLXnSk5O7PuaFGFCmqcFogoooqTYWiiigYtFFFIYUtJS0DCiiigaFooooGFLSUtIYUUUUDFooooGhKWkpaQwooooKNPQdbufD+rw6hbHlDh0zxIvdTX0Tpuo2+q6dBfWr7oZkDKfT2PuK+Y67bwZ4yOhaVqljO52NC0lrntLj7v49fwrow9XkdnseFnWWvEwVSmveX4r/gGb451f+2PFt5MrZhhbyIvTC9f1zXO0hJJJY5Y8k+ppawlLmd2e1QpRpU4047JWEpaSlqTUKKKKRQUtJS0AJS0lLQMKKKKCgooopDCiiigAooooAKKKKACiiigAooooAKKKKACiiigApaSloA9I+EWoGPUb/T2b5ZYxKo9wcH9DXrdeA+ALv7H4109s4ErNEf8AgQ/xxXv1enhJXp27HwXEdHkxnMvtJP8AT9Bsi74nUd1Ir5YuUMd5cRtwyysD+BNfVNfOvjzSzpXjLUItuI5n8+P6Nz/PNGJWiYZBUSqTh3X5f8Oc3S0lLXEfVIWlpKWkUhaWkpaCkApaQUtItC0UUUi0LS9qSl7UikFFFFBaAUtIKWkUgooopFBRRRQAUUUUAS21w9pcxXMTFZInDqR2IOa+l7G6W+sLe7T7s0SyD8RmvmPtX0H4GlabwVpTN18nH5EiuzBv3mj5biiknSp1Oqdvv/4Y6GiiivQPjAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACioXu7aNtr3ESt6FwDUiurruRgw9Qc0Daa3HUUUUCCiikJCqSSABySe1AHM+OvE6+GdAeWNh9tnzHbr792/Cvnp3eSRpJGLOxLMxPJJ6mui8ceIm8R+JJp0Ym1gJitx22jqfxPNc3Xn1qnPLyPtsrwf1ajr8T1f+QUtJS1iekFFFFAxaKKKBi0UUUhhS0lLQMKKKKBoWiiigYUtJS0hhRRRQMWiiigaEpaSlpDCiiigoKWkpaAClpKWkNCUtJS0DCiiikUFLSUtACUtJS0DCiiigoKKKKQwooooAKKKKACiiigAooooAKKKKACiiigAooooAKWkpaALmkzm11mxuAceXcI3/AI8K+lwcjNfLoO1gR1BzX03ZyebY28n9+NW/MV3YJ7o+Q4phrSn6r8ievN/i5oJu9Jg1iFMyWh2S47xnv+B/ma9IqK6tory1ltp0DxSoUdT3BrsnHmjY+awtd4etGouh8rUtaniPRJfD2vXOnS5KxtmJj/Gh+6ay68xpp2Z99TnGcVKOzFpaSlqTVC0tJS0FIBS0gpaRaFooopFoWl7UlL2pFIKKKKC0ApaQUtIpBRRRSKCiiigAooooAO1fQ3gqA2/g3So2GD5Ab8yT/WvALO1kvr2C0iGXmkWNR9Tivpi2gW1tYbdPuRIEX6AYrtwcdWz5XiiqlTp0u7b+7/hyWiis/W9Tj0bRbvUZBkQRlgPU9h+eK7m7K7Pj4Rc5KMd2Y/izxtY+GIxFj7RfuMpAp6D1Y9hXkuqeOfEOrSMZL94Iz0it/kUf1P41hXl5cajezXl1IZJ5mLOx9ahrzKteU3psfoGX5RQw0E5Lml1b/QvprOqRvvTUrtWHQiZv8a6zw98TdU06ZItUY31oThmI/eJ7g9/oa4Wiso1JRd0z0K+Cw9eHLUgmfTlle2+oWcV3ayrLBKu5HXuKsV5J8KdfaG+l0OZyYpgZIAT91h1A+o5/CvW69WlU9pHmPzvMcE8HiHSe3T0CiiitDhCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKpatqUWkaTdahP/q4Iy5Hr6D8TQ3ZXKjFykox3Zl+KfF9h4XtQZv313IP3Vup5PufQV45rPjXXdckbzrx4ISeIICUUD37n8ayNT1K51jUZ7+7ctNM2T6KOwHsKq15lWvKb02PvstyijhYqUleff8AyHEknJJJ9Sat2Oq6hpsoksr2eBh/ccgfl0NU6K57tPQ9pxjJWkro9g8F/EX+1J49N1jZHdNxFOBhZD6EdjXolfLgJUgqSGByCOxr33wN4gPiDw5FLM2buA+VP7kdG/Ef1r0MNWcvdlufF59lUMPbEUVaL3Xb/gHTVx3xK106N4Vkiifbc3p8iPHUA/eP5fzrsa8J+KmrnUfFhs0bMNigjA/2zy39B+FbVpcsDysrw/t8Sk9lq/69ThxwMUUUV559sFLSUtIYUUUUDFooooGLRRRSGFLSUtAw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tNwdj7zL8ZHFU1JbrdCCnU0U6sWenEUUtIKWpN4i0UUVJqek/CLTvM1C/wBSZfliQQofc8n9AK9brmPAGknSfCNorriacefJ9W6fpiunr1qEeWmkfmub4j6xjJyWy0XyCiiitjzQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiimSyxwxtJLIsaLyWY4AoBK4+iuZu/iB4Zs3KNqSSMOohUv8AqOKjt/iN4YuHCf2h5RP/AD1jYD88VHtYXtc61gMU48ypyt6M6qioba7t72BZrWeOaJujxsGH6VNVnK007MKKKKBBRRRQAUjEKpYkAAZJPakd1jRndgqKMsxOABXjXjrx9Jq7yaZpUjJp4OJJRwZv/sf51nUqKmrs7sDgKuMqckNur7Gr4z+JRVpNO0GQZGVkvB29k/xry1maR2d2LOxyzMckn3ptLXm1Kkpu7PvcHgqOEp8lNer6sKKKKyO0KWkpaAEpaSloGFFFFBQUUUUhhRRRQAUUUUAFFFFABRRRQBYsb65028ju7OZoZ4zlXX+XuK9y8G+M7fxPa+VJth1CIfvYs8MP7y+38q8FqxZXtzp17Fd2kpinibcjD/PStqNZ035HmZnllPHU9dJLZ/o/I+nKK5/wj4ot/E+lCdcR3UeFnhz90+o9jXQV6sZKSuj87rUZ0ajp1FZoKKKKZkcV8Q/F7eG9NS2s2A1C6B2H/nmvdvr2FeFPI80ryyuzyOcszHJJ9TXUfEi8ku/HN8HJ2wBYkHoAM/zNcqK86tNykfcZVho0MPFreWrHCnCminCsD14m/wCFPE914Y1P7REWe3cYmgzw/ofqKybu6lvb2e6nYtLM5d2Pck1XFOocnaw4UYRqOol7z0b9BRRQKKg60FdH4F1B9O8YWDqxCzP5Mg9Q3H88VzlavhqJpvFGlovU3UZ/JgaqDakmjHFxjOhOMtrP8j6Pooor2T8qCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACvmjxTG8Xi3Vkf7wunP5nNfS9eK/FnQHs9bj1iJP9HuwFkI/hkA/qP5Vz4mN43PayOrGGIcH9pHnop1NFOrgPsYjqWkpaTNoi0tJS1JrEWiiikbIK9R+EFl82pX5H92FT+p/pXl1e9/DzTTpvg6z3DElxmdv+BdP0xXRhY3qX7Hh8Q1/Z4Jx6yaX6/och8aAf+JOe2Zf/Za8or174zx5sdJl/uyyL+YH+FeQ1df+Izjyh3wcPn+bFooorI9MKWkpaQwooooGLRRRQMWiiikMKWkpaBhRRRQNC0UUUDClpKWkMKKKKBi0UUUDQlLSUtIYUUUUFBS0lLQAUtJS0hoSlpKWgYUUUUigpaSloASlpKWgYUUUUFBRRRSGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABS0lLQB6d8ILHMupX5HQLCp/U/0r1WuS+G+n/YfBtszDD3LNO348D9AK62vWoR5aaR+a5vW9tjakl0dvu0CiiitjzQooooAK+eviBb/AGbxzqagYDusn/fSg19C14L8T/8Ake7v/rnH/wCgiubE/Ae5kDaxLXl+qOPpaSlrhPsUApaQUtItC0UUUi0LS9qSl7UikFFFFBaForptB8Ca3ru2RYPstqf+W84IBHsOpr1Pw94A0fQdsxj+13Y/5bTDOD/sjoK1p0Jz8keXjM5w2F0vzS7L9X0PN/Dfw61TWyk92GsbI873X53H+yv9TXr2ieHdN8P2vkWFuEJ+/I3Lv9TWrRXfToRp7bnx2PzbEYx2m7R7Lb/ghRRRWx5gUUUUAFZWq+GtH1sf8TDT4ZnxgSYww/4EOa1aKTSejKhOUHzQdmef3nwj0SbJtbm7tj6bg4H4H/GufvPhBqMeTZajbzDsJFKE/wAxXsFFZSoU30PRpZxjKe07+up886p4K8QaOhkudPdol6yQnzFH5c1gCvqWuI8X/D2z1qKS705EttRAz8owkvsR2PvXPUwtleB7mB4iUpKGJVvNfqjxKtfwxpDa54js7HBMbPulPog5P+H41mTwS21xJBPG0csbFXRhypFes/CjQjb6dPrMyYkuT5cOR0QdT+J/lXNRp880j3MzxiwuElUT1ei9X/Vz0ZVCqFUAKBgAdqWiivXPzQKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAx/EfiKz8NaY15dHcx+WKIH5pG9B/jXhmv+KdU8R3LPeTkQ5+S3Q4RB9O59zVzx7rj614puMOTb2rGCFe3HU/if6VzNebXrOTstj7vJ8shh6aqzV5v8PJC0UUVzH0CNLRde1DQLxbmwnZOfnjJ+Rx6EV714a8Q23iTSEvbcbXztliJ5jb0r5zrrvh1rjaR4migd8W17+6kBPAb+E/nx+NdGHquEuV7M8TO8thiaLqwXvx19V2/yPdqKKK9M/Pwoorz34leLjplp/Y1jJi8uF/fOp5jQ/wBT/KonNQjzM6MLhp4mqqUN2c98RPG51GaTRtMlxZxnE8qn/WsP4R/sj9a88FFFeXObm7s/RMJhaeFpKlT2/PzFNFBoqDqCiiikUFLSUtACUtJS0DCiiigoKKKKQwooooAKKKKACiiigAooooAKKKKANXw/rtz4d1eK/tiSB8sseeJE7ivoXTdQttV06C+tHDwzLuU+nsfcV8y13vw08Uf2ZqX9kXUmLS6b92SeEk/wP866sNV5XyvZnz2fZb9Ype3pr3o/iv8AgHs9FFFekfCHz58SLY23ju/9JQkg/Fa5YV7B8WPDM17bw65aRl3tk2XCKOdnUN+FePivNrRcZs+6yytGrhotdFZ/IcKcKaKcKxPViKKdTRTqlm0RRRQKKRsgrtfhhpZvvFQu2XMVkhcn/aPA/rXFgEkAAkk4AHevfPAvh7/hHvD0ccq4u7j97P7E9F/Af1rfDQ5p37Hj57jFh8I4r4paL9fwOnooor1D88Ciimu6RoXdlVR1ZjgCgB1Fc5qHjvw3prFZtUieQdUh+c/pWBcfF7RIyRBZ3s3vtC/zNQ6sFuzrp4DFVFeMGehUV5l/wuOyz/yCLnH/AF0WrMHxf0dziaxvIvfCt/I1Pt6fc2eU4xK/s3+B6JRXMWPxB8M35CpqSwseizqUP68V0cM8NxGJIJUlQ9GRgw/SrUlLZnHVoVaTtUi16okoooqjIKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACqOr6Va61pc+n3iboZlwfVT2I9xV6ihq44ycWpR3R80+IfD174a1V7K8Q7c5imA+WVexH+FZdfT2paVY6vaG21C2juIT/C46e4PY1wWo/CDTpmZ9Ov57bPRJB5i/nwa4p4aSfun1mDz2lKKVfR9+h5BS13V58Jtft8m2ltLodgHKH9a5+88IeIbDJuNJudo/iRd4/SueVOa3R7VHHYap8E195jUtK6PE2yRGRv7rDB/WkrJnfEWiiikbIu6Rp76rrFpYIDmeUIcdh3P5Zr6UiiSCGOKMYRFCqPQCvJfhNo3n6lc6vIvyW6+VET/ebr+Q/nXrtejhIWhzdz4biTFe0xCoraK/F/0jz74v2/m+FIJwOYbpc/Qgj/AArxKvof4hWf2zwNqSAZaNBKP+AkH+Wa+d6zxC9868jnzYZx7MWiiisD2QpaSlpDCiiigYtFFFAxaKKKQwpaSloGFFFFA0LRRRQMKWkpaQwooooGLRRRQNCUtJS0hhRRRQUFLSUtABS0lLSGhKWkpaBhRRRSKClpKWgBKWkpaBhRRRQUFFFFIYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFT2lq97ewWkYJeaRY1x7nFQV2vwx0r7f4rW5dcxWSGQ/7x4X+p/CrhHmkonPi66w9CVV9F/wx7TaWyWdnDbRjCRIqKPYDFTUUV7J+WNtu7CiiigQUUUUAFfPPj+4Fz451NgchHEf/AHyoFfQc0qwQySucIilmPsBmvmDULpr7Urq7Y5M8rSZ+prlxT0SPoeHqd6s59lb7/wDhivS0lLXEfWIBS0gpaRaFoo7gd63tJ8Ga/rODbafIkR/5azfIv69aFFvRCnVp0o81RpLzMKnxRSTyLFDG8kjdERck/hXq2kfCK3j2yavfNKe8VuNq/wDfR5rvNL0LS9Gj2afZQwerKvzH6nrXRDCzfxaHjYniHD0tKS5n9yPINE+GWt6ntkvAthAf+evLn/gP+NekaH4C0PQ9si2/2m5H/Laf5iPoOgrqKK6oUIQPncXnGKxOjlZdlp/wQooorY8sKKKKACiiigAooooAKKKKACiiigAooooA8+8d+B31vVLK+sE2ySyLFdEdl/v/AIdPyrurO1isbOG0gULDCgRAPQCmahqNppVm93fTpDAg5Zj+g9TXlfiH4q3dwzQaJF9nh6efIMu30HQVhJ06Tcnuz2KFLG5jThRj8MOr2PWbi6t7WPzLieOJP70jBR+tc/d+P/DNoSG1SORh2iUv/IV4ReX13qMxlvbmW4kPJMjlqr1zyxj+yj2qHDFJL97Nv00/zPcD8UvDQbAkuiPUQmrNt8R/DFw2Pt5iJ/56xsv64rwaio+t1Dqlw1g2tHJfNf5H0zZ6nY6im+zu4J1/6ZuDVuvl6GaW2lEsErxSDo0bFT+ldvoPxP1XTmWLUh9vt+hY8SD8e/41tDFxeklY8rFcM1YLmoS5vJ6P/L8j2qisvRPEGm+ILT7Rp9wHx9+M8Oh9CK1K6001dHzdSnOnJwmrNBRRRTICiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiud8VeMdP8LWoM5827kH7q3Q/M3ufQe9eN61471/W5G8y8e2gPSG3JQAe56msalaMND08FlVfFLmWke7/Q+gmnhRsNLGp9CwFLI+IXdSDhSRivlsu7tud2Zj1JOTWrpHiTV9EmElleyqo6xO25GHoVNZLFrqj05cOS5bwqXfmv8AgmdKxeeRmOWZyT+dNoZt8jPgDcxOB2zRXAz6+GwtFFFI2QU5JGikWVDhkYMPqOabRSGfTen3IvNNtbkHPmxK/wCYzVmsTwe5fwfpLHqbZf5Vt17UXeKZ+UV4KFWUF0bMzX9Zg0DRbjUZ+RGvyL/fY9B+dfOd9ez6jfTXt05eeZy7n/Pau4+KfiA3+sppML5gs+ZMdGkP+A/nXn9cGJqc0rLZH2eR4L2FD2svil+XT/MWiiiuY90U0UGigYUUUUigpaSloASlpKWgYUUUUFBRRRSGFFFFABRRRQAUUUUAFFFFABRRRQAUoJBBBIIOQR2pKKAPfvA3iIeIfD8bysDd2/7qcep7N+I/rXTV4F4D146F4lhaR8WtziGb0Gejfga99r1cPU54a7o/Os6wP1TEvl+GWq/VfIQgMCCAQeCD3ryvxp8Mtxk1LQI/mOWlsx390/w/KvVaK0nBTVmcOFxdXCz56b/4J8rFWRyjKVZTgqRgg0or3Pxl4AtfEKPeWYW31MDO7GFl9m9/evErq1nsbuW1uominiba6MOQa8+rScHqfcYDH0sXC8dGt0RCnU0U6sGepEUUUCuv8EeC5fEl2Lm5Vk0yJvnboZSP4V/qacYuTsgr4inh6Tq1HZI1vhr4QN7crrl9H/o0R/0ZGH+sb+99B/OvX6ZDDHbwpDCixxooVVUYAA7U+vVpU1TjZH5xmGOnjazqS26LsgqK4uIbSB57iVIokGWd2wAPrWf4g8Q2HhvTWvL6TA6Rxr96RvQCvB/E3i/UvFF0WuXMdqp/d2yH5V9z6n3pVKqh6mmAy2pi3faPf/I77xD8W4IWe30KATsOPtMoIT8B1P6V5vqviHV9bkLahfzSjPCbsIPoo4rLFFcM6kp7s+twuAw+GXuR179QHSloorM7haKKKRSFq5Yapf6XKJbG8mt2H/PNyB+XSqdFF7A4qStJXR6RofxZvbcrFrNutzH0M0Q2uPqOh/SvTtH17TNetvP066SYD7y9GX6jqK+aqsWV9dabdJdWVxJBOnR0OD/9cV0U8TKPxanjYzIqFZc1L3Zfh93+R9P0V5/4O+I8GrtHYatst748JIOEl/wPtXoFd0Jxmro+RxOFq4afs6qswoooqjnCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooApX+kadqkRivrKGdT/fQZH0PUV5p4o+FrQRvd6CzSKvLWrnLf8AAT3+hr1iis50ozWqO7B5hiMJK9OWnbofLjKyMVZSrKcEEYINCqXYKoLMxwAO5r1j4leD0mgfXrCLE0YzdIo++v8Af+o7+1cz8NtB/tbxELuVM21jiQ56F/4R/X8K82VGSqch91RzWjUwbxXbdefb/I9X8KaMug+HLSxwPNC75T6ueT/h+FbVFFerFJKyPzurUlVm6kt27lbULYXmm3VswyJYmTH1GK+W3QxSNG33kYqfqOK+ra+avFtl/Z/i7VLYDCrcMy/Q8j+dc2JWiZ72QVPenD0ZjUUUVyH0oUtJS0hhRRRQMWiiigYtFFFIYUtJS0DCiiigaFooooGFLSUtIYUUUUDFooooGhKWkpaQwooooKClpKWgApaSlpDQlLSUtAwooopFBS0lLQAlLSUtAwooooKCiiikMKKKKACiiigAooooAKKKKACiiigAooooAKKKKACvbvhhpP8AZ/hf7W64lvX8zn+4OF/qfxrx/R9Nk1jWLTT4h808gUn0Hc/lmvpG3gjtbaK3iXbHEoRR6ADFdmEheTkfL8TYrlpRw63lq/Rf8H8iWiiivQPiwooooAKKKKAOT+I2rDSvB90FbE11+4j9eev6ZrwD0r2Xx74a8QeK9Yt4LOKOOwtk4klkADO3U4HPAwKzrH4ONwdQ1b6rbx/1P+FcdaE6k9EfU5ZisLhMMvaTXM9e/wCR5ZUsFvPdSCO3hkmc9FjQsf0r3fT/AIa+GbDBaza5cfxXDlv06V09tZWtlHstbaGBPSNAv8qUcLLqy6vENKP8OLfrp/meEab8OvEuo4Jshaxn+K4bb+nWux0z4QWse19U1GSY944F2D8zzXptFbRw8FvqeXWzzF1NIvlXkY2l+FND0cA2enQq4/5aMu5vzNbNFFbJJaI8qpUnUfNN3fmFFFFMgKKKKACiiigAooooAKKKwtT8Y6BpBZbvUoRIOscZ3t+QpNpbl06c6jtBNvyN2ivO7r4v6PExFtZXc/uQEB/nVL/hcsGf+QLLj/ruP8Kzdemup3RynGSV1D8j1GivO7X4v6RIwFzY3cAPdcPj+VdPpfjHQNXIW01KLzD0jkOxvyNVGrCWzMquAxNJXnB2N2iiirOMKoaxq9poemTX94+2KMdB1Y9gPc1frwn4heJ213XGtoHzY2bFIwOjt3b+grKtU9nG56OWYB4yvyfZWr/rzMrxJ4mvvE1+Z7pisKn9zAD8sY/qfesWiivKbbd2folKnClBQgrJC0UUUjVBRRRSKCiiigC3pup3mkXyXljO0MyHqOhHoR3Fe6+EPFtv4o08sAIr2IATw56f7Q9jXz/WjoWs3Gg6vBqFsTujPzpnh17qa3o1nTfkeTmuWQxtK6VprZ/o/wCtD6ToqvY3sOo2EF5btuhmQOp9jVivVWp+dSTi7PcKKKKBBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAVm69rEOg6JdalPysKZVf7zdh+JrSrzL4yXrR6Xp1kpws0zSMPXaOP1NRUlyxbOrBUFXrxpvZnlWpaldavqM1/eyGSeZtzHsPQD2FVhTRThXmPU+/hFRVlsOFOpop1SzeIopaQUtSbRFooopGqCjtRTo4zNKkQ6uwUficUh7H0V4UiMPhPSoyMEWyfyqxrepx6Not3qEnSCMsB6nsPzxVqzh+zWUEA/5Zxqn5DFeefF3VDDpdnpiNhriQyOP9len6mvXnLkp37H5lh6X1vGqPSTv8t2eSzzyXNxJPMxaWVy7k9yTk1HRRXln6IlbRC0UUUihTRQaKBhRRRSKClpKWgBKWkpaBhRRRQUFFFFIYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAV794D1z+3PC9vJI2bi3/czeuR0P4jFeA13fws1j7D4iewkbEV6mAD/AH15H5jIrow0+Wdu542fYT6xhHJbx1/z/D8j2miiivUPzwK83+KnhtLjT11y3TE9vhZ8D76HoT9D+hr0iqmqWqX2lXdrIMrNCyEfUVFSCnFo6sFiJYevGoum/p1PmOnUbGD+Xglw23AHJPSvTPBvw0ecx6hr0ZSLho7Q9W939B7V5cKcpuyP0HE4yjhafPVf+b9DG8F+BbjxDKt5eBodMU9ejTey+3vXtltbQWdtHbW0SxQxrtRFGABUiRpFGscaqiKMKqjAAp1elSpRprQ+FzDMquNneWkVsv66hVDWdXtND0ubULx9sUQ6d2PYD3NX68H+JHik67rZsraTNhZsVXB4kfu39BTq1OSNyMvwbxVbl6LcwPEXiG88S6q99dsQOkUQPyxr6D+prKpKWvObbd2fc04RhFRirJCiigUVJohaKKKChaKKKRQtLSUUDQtFFFIpBXrHw+8emcxaLq8uZfu21w5+9/st7+hryelBIIIJBHII7VdOo4O6ObGYOni6Tpz+T7H1NRXFfDzxadf002V4+dQtVAYn/lqnZvr612tepCSkro/PcTh54eq6VTdBRRRVGAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUU13WNGd2CqoySTwBQApIAyTgCuW1f4heH9JkaJro3My8FLcbsH3PT9a888a+PrnWp5LHTZWh01SVLKcNP7n/AGfauHriq4qztA+qy/h5Tip4l2v0X6nrp+L+n78DSror6l1zWtpfxL8P6jIsUsktnI3A89cL/wB9DivDaKxWKqJ6nqz4dwUo2imn6/5n1EjrIiujBlYZDKcg06vEPAnjSbQ72OwvZS+mytt+Y58knuPb1Fe3AggEHIPQiu6lVVRXR8hmOX1MDV5Jap7PuLRRRWp5410WRGR1DKwwwPQisvw/4ftPDtg9paAlXlaRmI5OTwPwGB+Fa1FKyvctVJqDgno9/kFFFFMgK8N+LVj9m8XR3QGFurdWz7rwf6V7lXm/xh07z9Cs9QVctbTbGP8Ast/9cCsa6vBnp5RV9ni4366HjNFFFcB9oFLSUtIYUUUUDFooooGLRRRSGFLSUtAwooooGhaKKKBhS0lLSGFFFFAxaKKKBoSlpKWkMKKKKCgpaSloAKWkpaQ0JS0lLQMKKKKRQUtJS0AJS0lLQMKKKKCgooopDCiiigAooooAKKKKACiiigAooooAKKKKACiipba2lvLqK2gUtNM4RFHcnimDaSuz0r4S6JulutalXhf3EGfX+I/yH516rWfoelRaLotrp8OMQoAx/vN3P51oV69KHJBI/McyxbxeJlV6dPRBRRRWhwhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABXM+KPG2l+GI9kzefeMMpbRnn6sf4RWZ4+8dL4dg+wWDK+pyrnPUQqe59/QV4fNNLczvPPI0s0jbndzksfeuerX5dI7nuZblLrr2tbSPbv/wAA6LXvHWua+zLJcm2tj0ggJUY9z1Nc3SUtcUpOTuz6ujShSjy01ZC0tJS1JsgFLSClpFo6jw9481nQJFTzjd2Y6wTNnA/2T1Fez+HfEun+JbH7RZSYdf8AWwt9+M+/+NfONaGjaxd6FqcV/ZOVkQ/MvZ17qfatqVeUHZ7Hk5hlFLExcqatP8/X/M9y8da0dE8K3M0bbbiYeTF6gt3/AAGa+f67j4h+KIPEMeki0b90ITNImeUc8bT9MVw/eliJ809NjTJMI8PhveVpN6/kFFFFc57QtFFFBSCiiikUFFFFABRRRQB7B8JtVNxo91pkjZa1ffGD/cb/AOuD+deiV4j8Lrs2/jBYc4W4gdCPcfMP5V7dXqYaXNTXkfnmfUFSxsmtpa/5/iFFFFdB4wUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFZeseItK0GIPqN2kRb7qDl2+gHNJtJXZcKc6kuWCu/I1KK89m+LmkJIRDY3ki/3iFX+taOl/Evw/qMixSSyWcjcD7QuF/76HH51mq1Nu1ztnlWNhHmdN2OxopFZXUOjBlIyCDkGlrU88K8t+M1qzWelXYHypI8bH0yAR/I16lWN4o0NPEXh+605iA7ruiY/wuOQaipHmi0deBrqhiI1Hsj5rFOFPubaeyupbW5jMc8TFHQ9QRTBXmM+/i01dDhS0gpalm0RwpaQUtSbRFooopGqCt3wZYHUvF+mwbcqsvmP9F5/pWFXpXwi0zzL++1Nl+WJBCh9zyf0A/OtKMeaaRxZnX9hhKk/L8XoetV4R8TL/wC2+M54wcpaosI+vU/qf0r3boM18z6zdm+1y/uic+bcO36mu3Fv3Uj5PhylzV5VOy/P/hijRRRXAfZC0UUUhimig0UDCiiikUFLSUtACUtJS0DCiiigoKKKKQwooooAKKKKACiiigAooooAKKKKACiiigAqezupLG9gu4jiSCRZFI9Qc1BS0xNJqzPpyyuo76xgu4jlJo1dfxGanri/hjqX23wkkDNl7OQxH/d6j9DXaV7EJc0VI/LMXQdCvOl2YUh5UiloqznOS8NeAtP0Kd72fF1fu7MJGHyx5OcKP611tFFTGKirI2r16lefPUd2FFFFUYnKfEHxAdA8LzNE+26uf3MPqCep/AV8+V23xP1z+1fFLWkT5t7AeUMdC/8AEf6fhXE159efNL0Ptcpw3sMOm95a/wCQtLSUtYnqIUUUCikUhaKKKChaKKKRQtFFFA0LRRRSKQUtJS0FGhomrT6Hq9vqNuTuibLL/eXuPxFfR1jeQ6hYwXlu26GZA6H2NfMXavYvhNqxudFuNMkbLWj7kz/cb/A5rqws7S5e587xDhFOisQt46P0f/B/M9DooorvPjQooooAKKKiNxCvWaMfVhQNJvYlopiyxv8AcdW+hzT6BWsFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUVnXuv6Rpxxealawt/deUA/lSbS3KjCU3aKuaNcR8UNXfTvC/2aJisl7J5RI67By3+H41vW/i3w/dyCODWLN3PQeYBn864L4wsW/sgqQYyJCCDwfu1lWmvZto9PK8M3jacasbddfJXPLxS0lLXln6FEWiiipNUFe7fDnWW1fwtGkzbp7RvIcnqQPun8v5V4TXo/whuymrahaZ+WSESAe6nH9a6MNK1S3c8XP8OquClLrHX/M9dooor1D89CiiigAooooAKzPEWlrrXh++09hzNEQvs3UfritOik1dWKhJwkpLdHyk6PG7RyDa6Eqw9COtJXa/E7QTpHidruJMWt/+9UgcB/4h/X8a4qvMlFxdmfoGHrRrUo1I9QpaSlqTYKKKKBi0UUUDFooopDClpKWgYUUUUDQtFFFAwpaSlpDCiiigYtFFFA0JS0lLSGFFFFBQUtJS0AFLSUtIaEpaSloGFFFFIoKWkpaAEpaSloGFFFFBQUUUUhhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABXovwq0D7VqMuszp+6tvkhz3c9T+A/nXA2dpNf3sNpbrummcIg9zX0Zoekw6Ho1tp8A+WJMM395u5/E11YWnzS5nsjwOIMd7DD+yi/en+XX/I0aKKK9I+CCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACsbxRr8PhvQbjUJMM6jbCn99z0FbNeJfFnWzfeIY9Ljf9zZLlgO8jcn8his6s+SNzuy7C/Wa6g9t36HC3d3PqF5Nd3UhknmYu7nuTUNAorzWfdRSSshaWkpaRaFpaSloKQClpBS0i0L3oo70Ui0LS96Sl70ikFFFFIsWiiigpBRRRSKCiiigAooooA6HwPIYvGulkd5tv5givoOvnrwUhfxppQHacH8hX0LXo4P4H6nxHE9vrMP8P6sKKKK6z5oKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigCpqWp2ekWEl7fTrDBGOWb+Q9TXlWs/F29mlaPRrSOCIHiWcbnPvjoP1rF+JHiSTWvEUllG5+xWTGNFB4Z/4m/pXHCuKrXd7RPq8tyikqaqV1dvp0R1Y+I/irzN/9pD6eSuPyxXT6F8W51lSLW7ZGjJwZ4Bgr7le/wCFeXinVgq0073PWnlmEqx5XBL00PoXxF4rtNH8MnVreRJ/OAFtg8Ox6fgOp+leCXt9c6leSXd5M008hyzsf0HoKR765lsIbJ5ma2hZnjjPRS3X+VQCitWdRl5XlsMFF21be/l0QtFFFc57B3Pw/wDGM+k6hDpl5KX0+dgi7j/qWPQj29q9rr5byRyDgjkV9L6RcNdaLY3DHLSwI5+pUV6GEm2nF9D4viXCQpzjXgrc17+vcu0UUV2Hy5wHxB8CNry/2npiKNQjXEkfTzlH/swrxie3ntJ2guYXhmQ4ZJFwRX1NVHUdG03Vo9l/YwXA7GRASPoetc9WgpO6PbwGcyw8fZ1FzRX3o+ZBTq9wvPhX4cucmFLi1Y9PLlyPyOa5+8+DsoybHVlb0WeLH6iuaWHqI96jnmDnu2vVf5XPMBS11t78NfEtkGZbWO5Ud4ZASfwOK5e4tp7SYw3MMkMq9UkUqf1rCUJR3R61DE0a38OSfoyOiiioOxBXv/gTSP7I8JWcTriaYedJ9W5/livG/CWjnXPEtnZlSYt/mS+yLyf8Pxr6IAAAAGAOABXbg4bzPlOJ8VpDDr1f6fqVdTl8jSbybOPLgds/RTXzHnd83rzX0h4qcx+FdUYdrZ/5V83DoKMW9UieGo/u6kvNC0UUVyH0wtFFFIYpooNFAwooopFBS0lLQAlLSUtAwooooKCiiikMKKKKACiiigAooooAKKKKACiiigAooooAKWkpaAPRPhJqHlaze2DN8s8QdR/tKf8AAmvX6+evBd79g8YabNnCtL5bH2bivoWvSwkrwt2Pg+JKPJi1NfaS/DQKKKK6j58KKKKACsvxHqq6J4fvdQbrDESg9WPCj88VqV5r8YtRMOjWOnq2PtExdx7KP8T+lRUlyxbOrBUfbYiNN9WeOPI8sjSSNukdizE9yetJRRXmn3qFpaSlpFIUUUCikUhaKKKChaKKKRQtFFFA0LRRRSKQUtJS0FB2rtfhbeG38YrBuwtzC6EepHzD+VcV2rpPARI8b6Zj++R/46auk7TRy4+CnhKifZ/kfQVIzBVLMQFAyST0rD8R+LNM8NW+67k3zsP3duhy7f4D3NeN+IvG2reInZJZTb2mflt4jgf8CPevQq1409Op8VgMor4z3l7se7/Tueoa58SdE0lmhgdr64XjbAflB926flmuC1P4o6/elltfJsoz0Ea7m/M/0riBS1wzxFSXWx9dhckwdBax5n3f+Wxfutb1W+YtdaldSk/3pTVIu7HLO5+rGm0Vg23uetCEYq0VYkS4njOY55UP+y5Fa1l4u8QaeR9n1W42j+GRt6/kaxaKak1sxVKNOorTin6o9I0r4t3kRVNVsY517yQHa35Hg/pXoGieLdG18AWV2vnd4ZPlcfgev4V870qsyOHVirKchgcEVvDFTjvqeNi+HsJWV6fuPy2+7/Kx9R0V4x4Z+Jt/prJbatuvbXp5n/LRB9f4vxr1zTdTs9Xsku7GdJoX6Mp6H0I7Gu6nWjUWh8hjssxGCl+8WndbFuiiitTzwooooAKKKKACiiigAooooAKKKKACiiigAooooAKrahqFrpdjLe3kyxW8S7mZv89as14h8UvEr6lrh0iBz9ksjhwDw8vfP06fnWdSfJG52YDCPFVlDp19CHxR8SNT1uV7fT3eysOgCHEkg9WPb6CuKJLMWYksepPJNNFOrzpScndn3WHoU6EeSmrIXAq1JfXU1pFayzyPBExaNGbIQnriqwpag60k9xaWkpak2iLRRRUmqCu3+FZI8YEets+f0riK734TQl/FFxL2jtT+rCtaP8RHn5s0sFVv2PZ6KKK9c/MwooooAKKKKACiiigDA8YeHU8TeH5rPAFwv7y3c/wuOn4HpXznNDJbzyQTIUljYq6kcqR1FfVdeWfFDwaZQ3iDT48uo/0uNR1H98fTvXNiKd1zI93JscqcvYTej29f+CeSUtJS1xH1YUUUUDFooooGLRRRSGFLSUtAwooooGhaKKKBhS0lLSGFFFFAxaKKKBoSlpKWkMKKKKCgpaSloAKWkpaQ0JS0lLQMKKKKRQUtJS0AJS0lLQMKKKKCgooopDCiiigAooooAKKKKACiiigAooooAKKK09A0afX9at9PgyPMbMj/ANxB1NNJt2RNScacXObskd98KvDmWk165TgZjtgR/wB9N/T869TqCytIbCyhtLdAkMKBEUdgKnr16VNQion5lmGMli8RKq9unkgooorQ4gooooAKKKKACiiigAooooAKKp6jqljpNsbi/uo7eId3PX6DvXA6r8XLSJmj0qxe4x0lmOxfy61E6kIfEzsw2AxOK/hRv59PvPSqK8Mu/ib4luSfLngtlPQRRD+ZzWa3jfxKxydYuB9CB/SsHi4dEevDhnFNXlKK+/8AyPoWivAYPiB4ngbI1Rn9pEVh/Kug074uahEVXUrCGdO7wnY35HinHF03voZ1eHMZBXjaXo/87Hr1Fc9ofjXRNf2x210I7g/8sJvlb8PX8K6GuiMlJXR4tWjUoy5KkWn5hRRRTMhCQqljwAMk18vaveNqOs3t45yZp3f8zxX0zqBK6bdMOohcj8jXy13P1rkxL2R9Hw/FXqS9P1FFFAorkPpkLS0lLSKQtLSUtBSAUtIKWkWhe9FHeikWhaXvSUvekUgooopFi0UUUFIKKKKRQUUUUAFFFFAHX/DW2+0eNrZscQxvIfywP1Ne615T8IbAtc6lqBHCqsKn3PJ/kK9Wr08LG1O/c+A4iq8+NcV9lJfr+oUUUV0nhBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUjZ2nHXHFLRQB8sXZZr+5LZ3GZ859dxqMdK1/FunvpfizUrZlKjz2dPdW5B/WsgV5UlZ2P0ajJTgpLZocKdTRTqhnVEUUtIKUVJvEWiiipNRVUu6ooyzEAD1NfTWm2/2TS7S2PWKFEP4ACvDfh/ojaz4pgZlzb2hE8p7ZH3R+f8q97rvwcbJyPjeJ8QpVIUV01fzCiiiu0+VCiiigAooooAKztX0LTddtjBqFqkq4+VsYZfoeorRopNJqzKhOUJKUHZo8E8X+CrrwxMJkZp9PkbCTY5U/3W/x71y1fTl7ZW+o2U1pdRiSCVSrqe4rwm58GXcHjRPD67mWRwUlx1i67vwH6151ehyu8dmfc5PnCxFNwru0oq9+67/I7r4U6H9l0qbV5kxJdnbFntGO/wCJ/lXolQ2ttFZ2kNtAoWKJAiKOwFTV304ckVE+OxuKlisRKs+v5dDD8Y5/4Q7Vsdfs7V86DpX0h4pTzPCuqJ62z/yr5uH3R9K5MX8SPpuG3+5mvP8AQWiiiuQ+kFooopDFNFBooGFFFFIoKWkpaAEpaSloGFFFFBQUUUUhhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABS0lLQA+GU29xFMvWNw4/A5r6bt5RcW0Uw5EiBh+IzXzCelfRHhC5N34R0uYnLG3UH6jj+lduDerR8rxRTvTp1Oza+//AIY26KKK7z40KKKKACvGPjJKW8QafD2S2LfiWP8AhXs9eL/GOIr4h0+XHD2pH4hj/jWOI+A9XJrfW16M85ooorgPsxaWkpaQ0KKKBRSKQtFFFBQtFFFIoWlpKKBoWiiikUgpaSloKDtV3SdTn0bUo7+2CGeINs3jIBIxn8M1S7UUJ2d0EoqcXGWzJ7q6uL26kubqZ5p5Dlnc5JqGiikWkkrIKWkpaRaCiiikUFFFFABRRRQAVreH/EV/4bvxc2Unyk/vYWPyyD0Pv71k0U02ndEVKcKsHCaumfR3h/xBZeI9NW8s2wekkRPzRt6H/GtavnLw54guvDeqpe25JQ/LNFniRfT6+lfQWm6jbatp8N9aSb4Zl3KfT2PuK9ShW9orPc/Ps3yt4KpzR1g9vLyLdFFFbnjhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFADJX8uJ3/uqTXy3dztdX9xcOSWllZyT7k19SuodGQ9GBBr5e1SzfT9XvLOUYeGZ0I/GuTFbI+j4fa5qi66fqVhTqaKdXGfUxHClpBS0jaItLSUtSaxFoooqTVBXqfwftPk1S9I6lIgfzJ/mK8sr3b4bWBsfBts7DD3LNMfoTx+grpwsb1L9jw+Ia3s8E4/zNL9f0Ouooor0z8/CiiigAooooAKKKKACkZQylWAIIwQRwaWigDxD4g+BG0Sd9U02MtpsjZkRR/qGP8A7L/KuBr6qlijnieKVFeNwVZWGQR6V4l47+H8uhvJqWlo0mmk5dByYP8AFfftXFWo296J9VlWaKolRrPXo+//AAfzOCooormPfFooooGLRRRSGFLSUtAwooooGhaKKKBhS0lLSGFFFFAxaKKKBoSlpKWkMKKKKCgpaSloAKWkpaQ0JS0lLQMKKKKRQUtJS0AJS0lLQMKKKKCgooopDCiiigAooooAKKKKACiiigAooooAK9w+HXhj+xNH+2XKYvrwBmBHKJ2X+prhPh14X/tvVvt10mbG0YEgjiR+y/QdTXt9d2FpfbZ8jxHmP/MJTf8Ai/RfqFFFc/rnjTQ/D5KXd2HnH/LCH53/ABHb8a7W0ldnytOlOrLlgrs6CivIdS+MN3IWXTNNjiXs87bj+Q4rm7n4ieKbpsnUzEPSGNVrB4mCPVpZFipq8rR9X/lc+gqK+c/+E08TZz/bd5/32P8ACr1p8RvFFqR/xMBMO4mjDZpfWo9jaXD2ItpJP7/8j36ivKtK+L5yqatp2B3ltm/9lP8AjXoOj+IdK12HzNOvI5SB8ydHX6g81rCrCezPNxOX4nDa1I6d90alFFFaHEFcf4y8dW3huM2tsFuNSYcR5+WMerf4Va8a+KU8M6OXjw17PlLdD692PsK8EnnluZ5J55GkmkYs7sclia5cRX5PdjufQ5NlKxP76t8C2Xf/AIBY1LVL7WLtrq/uHnlbux4X2A7CqlJ2pa89tvVn20IxilGKskFFFFSahRRRQAoJBBBIIOQR2rvPCnxJvNMaO01dnurPoJeskY/9mH61wVFXCcoO8TnxWEo4qHJVV1+XofT1pd299ax3NrMssMg3I6HIIqavCPA/jCXw5qC29w7Nps7YkX/nmf7w/rXuqOsiK6MGVhkEHgivUo1VUjfqfnuZ5dPA1eV6xez/AK6jZ4/OgkiPR1K/mK+WJo2huJYmGGR2Uj6GvquvnLx1p/8AZvjTUoQuEeTzk+jc/wA81liVomd2QVLTnDur/d/w5z4ooFFcZ9ShaWkpaRSFpaSloKQClpBS0i0L3oo70Ui0LS96Sl70ikFFFFIsWiiigpBRRRSKCiiigAo7UVqeHNJfW/EFnYKDtkkBkPog5Y/lTSbdkTUqRpwc5bLU9n+H2lnS/CFqHXEtxmd/+BdP0xXU01EWONUQAKowAOwp1ezGPLFI/K8RWderKrLq7hRRRVGIUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAHG+O/BCeJ7Vbm1Kx6lAuEY8CRf7p/oa8MurO5sLqS1u4XhnjOHRxgivqWue8U+D9P8UWuJ18q7QfurlR8y+x9R7Vz1qHNqtz28szZ4e1KrrH8v+AfO4p1aWu+H9Q8OX5tL+Lbn/VyL9yQeoP8ASs2uCSadmfZUpxnFSi7piilFIKUVB1RFp8cck0qRRIXkdgqqo5JPQUyvXfh14KNiqa3qcWLlxm3iYf6sH+I+5/SqpU3UlZHPj8dTwVF1J79F3Z0ngzw2vhvQkhcA3c37y4Yf3vT6CujqK4uYLOBp7maOGJRlnkYAD8a4DXPizptmWi0qBr6UceY3yxj+pr1LwpxsfnqpYnHVXNK7e/Y9ErL1LxJo2kg/btRt4WH8JfLfkOa8M1fx14h1kss188MJ/wCWVv8AIv8AifxNc4SWO5iSx6knmsJYr+VHsUOHm9a0/kv8z2u++Leh25K2kF1dEdwoQfma566+MV++RaaXbxjsZXLH9MV5rRWLxFR9T1KeTYOH2b+rO1m+KXieXOya2iz2SEH+eapN8Q/FT9dUYf7saj+lcxRUOpPudccDhY7U19yOmX4g+KVORqrn6xqf6Vch+J/iiL711BKPR4V/piuNpaXtJ9xvA4aW9Nfcj0iz+MGoIQLzTLeUdzE5Q/rmug0/4leGL69jubyCWzu1QxrLKm4BSckbh24rxeirWImvM5amTYSfwrlfkz6fstRs9RhE1ldRXEZ7xuDVmvl+0vLqwnE1ncSwSjkNGxU16BoHxXvLYrBrUP2mLp58Qw4+o6H9K6IYqL0loeJiuH61Nc1F8y7bM9V1SLz9JvIsZ3wOv5qa+Y8bfl9OK+mNM1bT9bsxcWFyk8TDB2nlfYjqDXzjqVubTVby3IwYp3TH0JqMVrZo7OHG4OrTkrPT9SrRRRXGfUC0UUUhimig0UDCiiikUFLSUtACUtJS0DCiiigoKKKKQwooooAKKKKACiiigAooooAKKKKACiiigApaSloAK90+Gc5m8E2yk58qR0/8ez/WvC69k+Esu7w1dRf3Lon81FdOEf7w8DiOHNgr9mv8jv6KKK9M+CCiiigAry/4y2W6w0y+A/1crRMf94ZH8jXqFcx8QdNOp+C7+NVzJConQD1Xn+Wazqq8Gjsy+r7LEwk+/wCeh88UUdRRXnH3gtLSUtIaFFFAopFIWiiigoWiiikULRRRQNC0UUUikFLSUtBQdqKO1FIpC0UUUigpaSloKQUUUUigooooAKKKKACiiigArvvhj4lOnap/ZFy/+i3bfusnhJP/AK/88VwNOVmR1dGKupypHYirhNwkpI5sXhYYqjKjPr+D7n1FRWL4U1oa94dtb4n96V2Sj0ccH/H8a2q9iLUldH5hVpypTdOe60CiiimZhRRRQAUUUUAFFFFABRRRQAUUUUAFeS/Fbwq6zf8ACQ2keUICXagdD0D/AND+FetVHNDFcwPDMiyRSKVdWGQQe1RUgpxsdWDxUsNVVSPz9D5XFOrvPFfw0v8AS7mS50iJ7uwY5Ea8yRe2O49xXCOjxOUkRkYHBVhgivNnBxdmfd4bE0q8eam7iilpBS1B2xFpaSlqTWItFFFSaons7WS+vYLSIZkmkWNR9Tivpaztks7KC1jGEhjVF+gGK8Z+F+k/b/E5vHXMVkm//gZ4H9TXttehhIWi5dz4nibE89aNFfZV36v/AIAUUUV2HzIUUUUAFFFFABRRRQAUUUUAFIyq6FWUMpGCCMgilooA8j8b/DRojJqegRbo+Wls16r7p7e1eXYIJBGCDgg9q+ra4Txl8ObXXt99p2y11Hq3GEm+vofeuWrQvrE+iy7OeW1LEPTv/n/meHUVZv8AT7vS7x7S+t3gnQ8o4/Ueo96rCuM+ni1JXQtFFFIoKWkpaBhRRRQNC0UUUDClpKWkMKKKKBi0UUUDQlLSUtIYUUUUFBS0lLQAUtJS0hoSlpKWgYUUUUigpaSloASlpKWgYUUUUFBRRRSGFFFFABRRRQAUUUUAFFFFABV3SdLuda1SDT7RcyzNjPZR3J9hVIDJwBk9hXuHw98J/wBg6Z9su0/4mF0oLAjmNOy/Xua2o0nUlboedmmYRwVBz+09l5/8A6XRtJttE0qDT7VcRxLgnux7k+5q1c3MFnbSXFzKsUMa7ndzgKKkZgqlmICgZJPYV4R8QPGsniG+axs5CulwNgY/5bMP4j7elejOapxPg8JhamOrO782/wCupf8AF3xOu9SeSz0RntrP7pn6SSfT+6P1rz3JLFiSWJySTkmm04VwSm5O7PtMNhqWHhyU1YKWkpag6ULQKKBSLQtTW11cWVylxazPDMhyrxtgioaXvQVZNWZ7J4I+Ii6s8emauVjvTxFN0WX2Po3869BdlRGdiAqjJJ7CvlsEqQykhgcgjqK9Kl8fPefDe4gll/4mmRasc8up/j/IEH3rspYjRqR8vmWSfvIzw6spOzXa/X0OS8Xa8/iLxDPd7j5Cny4F9EH+PWsOkpa4pNt3Z9VRpxpQVOGyDtS0nalqTZBRRRSLCiiigAooooAK9n+F2vNqGiyabO+6ayICEnkxnp+XT8q8Yrq/h1qBsPGVoucJcgwMPXPT9QK2oT5aiPLznCrEYOa6x1Xy/wCAe815B8Y9MKX2n6oi/LIhgc+45H8zXr9YPjDQR4i8NXViuPOx5kJPZx0/Pp+NelVjzQaPhMvxCoYiM3ts/mfOAopzxvFI8cilJEYqykcgjqKbXmn3aFpaSlpFoWlpKWgpAKWkFLSLQveijvRSLQtL3pKXvSKQUUUUixaKKKCkFFFFIoKKKKACvWvhPoRhs59amTDz/uoMj+AdT+J/lXm2h6RNrms22nQZzK3zN/dUdT+VfRlnaQ2NlDaW6BIYUCIo7AV14SneXM+h81xHjfZ0Vh47y39P+CT0UUV6J8QFFZOreJtH0MY1C+iifGRGDuc/gOa50/FXw4H2gXhH94Q8fzqJVIRdmzrpYHE1o81Om2vQ7iisLSPGGha24js75POPSKT5GP0B6/hW7VKSkrowq0alKXLUi0/MKKKKZmFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAGZruh2fiDS5LG8QFWGUfHzRt2YV876rplxo+qXGn3S4lhfaT2YdiPYivpuvJfi/pqx3en6migGVWhkPrjkfzP5Vy4mCcebsfQZBi5Qrewe0vzPMxS/wBafb2811OkFvE8s0hwiIMkmvVNA8I6b4QsRrviaWITp8yRHlYz2wP4mrjhTc3ofVYrHU8LG8tZPZLdjPAngDyzHrGtRAEfPBbOOnozf0Fanij4nafpBe10wLfXg4LA/ukPue/0FcH4t+IeoeIWe1sy9pp3TYp+eQf7R/oK4wVv7RQXLT+88ZYCpi6nt8a/SPRGprHiDVNfn83Ubt5cHKx9EX6L0rMoorBtt3Z7EIRguWKsgpaQUtIsKKKKBi0UUUDClpKWkMKKKKBi0lLSUii9per32i3q3en3Dwyjrjow9CO4pNUv21TVLi+eNY3uH3sq9Ax64/GqdFO7tYlU4c/PbXa4UUUUjQWiiikMU0UGigYUUUUigpaSloASlpKWgYUUUUFBRRRSGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFLSUtABXrPwffOnaon92ZD+YP8AhXk1epfB5vl1ZfeM/wA63w38VHj58r4Cfy/NHqNFFFeqfnYUUUUAFNkjWWJ43GVdSrD1Bp1FAHzDrmmvo+u3unuMeRKVX3XqD+VZ9elfGDSPI1Wz1aNfluE8qQ/7S9P0/lXmteZUjyyaPvsHX9vQjU7r8RaWkpag60KKKBRSKQtFFFBQtFFFIoWiiigaFooopFIKWkpaCg7UUdqKRSFooopFBS0lLQUgooopFBRRRQAUUUUAFFFFABS0lLQB6V8I9UMd7e6U7fLKomjH+0OD+mPyr1mvnvwReGx8ZaZIDgPL5TfRhivoSvSwkr07dj4LiOgqeL519pX+ewUUUV1HgBRRRQAUUUUAFFFFABRSMyryzAfU0AhhkEEeooAWiiigAooooAKp3mk6dqIxeWNvP7yRgn86uUUNXHGTi7xdjkbz4a+GLvJWya3Y94JCtc/efB62bJstVlQ9lmjDD8xivTqKydGm90d1LNMZS+Go/nr+Z4Tqvw18QaYjSRRJexDqbc/N/wB8nn8s1yTK0blHUq6nDKwwQa+o65nxT4K07xLAzlVt74D5LhRyfZvUVz1MKrXge3geI5cyjiVp3X6r/I8Boq3qemXej6jLY3sRjniOCOxHYj1BrR8JaKdf8SWtmQTCG8yY+iDr+fT8a4lFuXL1PrZ16cKTrN+6le/ket/DrRTpHhWF5F23F2fPfPXB+6Py/nXW0iqFUKoAAGAB2pa9iEVGKij8uxNeVetKrLdu4UUUVRiFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAY3iLwxpviWz8i+i+dR+7mXh4z7H+leG+J/B+peF7nFwvm2jHEdyg+U+x9DX0XUNzbQXlu9vcxJLDIMOjjIIrKpRU/U9LAZnVwrtvHt/kfLVFej+MPhlPp/mX+hq89qPme26vH/u+o/WvOPY9q8+cHB2Z9lhsVSxMOem7/AKBS0lLUnQFFFFA0LRRRQMKWkpaQwooooGLRRRQNCUtJS0hhRRRQUFLSUtABS0lLSGhKWkpaBhRRRSKClpKWgBKWkpaBhRRRQUFFFFIYUUUUAFFFFABRRRQAUUVv+EvDM3ifV1txuW1jw1xKOy+g9zVRi5OyM61aFGm6lR2SOk+GvhH+0Lpdavo/9Fgb9wjD/WOO/wBB/OvYaitbaGztYra3jWOGJQqIvQAVLXrUqapxsfmuY46eNrupLbouyPPfip4kbTNITSbZ9tzeg7yDysQ6/n0/OvE66Hxxqp1fxhfz7sxxv5Mf+6vH88mueriqz5pM+ry3DKhh4rq9WLThTacKyPRQUtJS0ikLQKKBSLQtL3pKXvSKQUtJS0FoKWkpaRaDtS0nalpFIKKKKRYUUUUAFFFFABV7Rpjba3p8wOClxGf/AB4VRqezyb62A6+cn/oQprcmok4NM+naKRfuj6Ute2fkp5h8R/Ab3jSa5pMW6fGbmBRy/wDtD39RXkPevq2vP/GXw2t9aaS/0rZbX55dDwkp/ofeuWtQv70T6HLM2UEqNfbo/wDM8TpasX1hd6ZdvaXtu8E6dUcY/EeoqvXGz6mLTV0LS0lLSLQClpBS0i0L3oo70Ui0LS96Sl70ikFFFFIsWiiigpBRRRSKCiiut8BeFz4h1kTTpmwtSGlJ6O3Zf8faqjFyfKjHEV4Yek6tTZHd/DPwz/ZelHVLlMXd4o2AjlI+359fyrvKQAAAAAAcAClr14QUIqKPzLF4meKrSrT3YV598QPHT6Ox0rS3AvWXMsvXyQegH+1/Ku8uJhb20szdI0Ln8Bmvma9vJNQv7i8mYtJPI0jE+5rHE1HCNl1PUyLAwxNVzqK6j08yOSWSeVpZXaSRjlnc5JPuabSUteaz72OgoJVgwJDDkEdRXr/w58Zy6oP7H1KTfdRrmGVjzIo6g+4rx+rWnX0umalbX0JxJBIHHvjqPxFaUqjpyucWY4GGMoODWvR9mfTVFRWtwl1aw3EZykqB1PsRmpa9c/M2mnZhRRRQIKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAK5Lx94evPEmm2VnZBA63IZ3c4CLtOSf0rrawPFfiqz8LaYbibElxJkQQA8uf6AdzUTScXzbHTg5VY14yoq8uhgC28PfDHSPtMv+kajIuFY48yU+i/3VryfxB4j1DxLqBur6T5Qf3UK/cjHoB/Wq2rave65qMl9fzGSZz+CjsAOwqjXBOpf3Y6I+xwmD9m/a1XzVHu/0QtFFFZnoC0UUUhgKWkFLQMKKKKBi0UUUDClpKWkMKKKKBi0lLSUihaKKKBhRRRQMWiiikMU0UGigYUUUUigpaSloASlpKWgYUUUUFBRRRSGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFLSUtABXp/wAHv9Zq30j/AK15hXqHwfHz6sf+uf8AWt8N/FR5Gef7hU+X5o9Tooor1T86CiiigAooooA5jx/pH9s+D72JFzNCvnxfVef5Zr54HIzX1awDKVYAgjBBr5p8T6SdE8S31hghI5SY/dDyP0NcmJjqpH02QV7qVF+q/UyaWkpa5D6NCiigUUikLRRRQULRRRSKFpaSigaFooopFIKWkpaCg7UtJ2opFIWiiikUFLSUtBSCiiikUFFFFABRRRQAUUUUAFLSUtAFvSpDFrFjIOq3EZ/8eFfTFfNOjRGfXNPiAyWuIx/48K+lq78Fsz43im3PT9H+gUUVj+I/Edj4Z0xry8bJPyxRKfmkb0H+NdjaSuz5iEJVJKEVds2Koz6zpdq22fUbSNh2eZQf514Lr3jnW/EErCW5a3tiflt4GKqB7nqfxrnepyefrXLLFL7KPoaHD8pK9WdvJH1FbX9nef8AHrdwT/8AXOQN/KrFfLUMskEiyQyPE6nIZGKkflXonhD4l3drcxWOuSme1chVuW+/H/veo/WnDFJu0tCMVw/Vpxc6Mua3Tqes319babZS3d3KsUES7mdu1eO+IvibqmozPFpbGxtM4DD/AFjj1J7fQVP8U/ELXuqR6PBJm2tgHl2nh3IyPyH868+rLEV3fliejkuUU1TVesrt7J9ETzXt3cOXnup5GPUtITU1nrGpadKJLO/uYWH92Q/yqlRXHd3ufTunBx5WlY9S8LfFFnljs9fCgMdq3aDAH+8P6ivUVZWUMpBUjIIPBFfLlexfCvXpL7S5tKuHLSWeDESefLPb8DXdhq7b5JHyWe5PTpU/rNBWtuunqj0Kiiiu0+TCiiigAooooAKKKKAOM+Inhhdb0Zr23jH26zUspHV07r/UVX+GGgf2boR1KZMXF9hlyOVjHT8+v5V3RAIwRnNUNT1fTdCtPPv7mK2iAwoJ5PsB3/CsnTip+0PRhj68sJ9Sjqm/6X36mhRXk2tfF6RmaLRLIKvQT3HX8FH9TXCal4o1zVmJvNTuHU/wK21fyHFRLEwW2p04fIsRUV5+6vxPoa51rS7PP2nUbWIjs8qg/lms5/G/hmM/NrNr+DE186Hk5PJ96BWTxUuiPSjw9S+1N/h/wT6Nj8ZeHJThNZtD9Xx/OtO31KxvMfZry3mz/wA85Q38q+X8D0pykocoSp9QcULFPqglw7Tfwzf3f8MfVFFfOOneLtf0sj7NqlxtH8Ejb1/I122j/F2VSses2IZe81twfxU/41rHEwe+h51fIcTTV4Wl+Z6xRWbpGv6ZrsHm6ddxzDHzIDhl+o6itKt001dHjThKEuWSswooopkhRRRQAUUUUAFFFFABXC+MPhzaa6HvdO2Wuo4yeMJKff0PvXdUVMoqSszahiKmHnz03Zny9f6fd6VevZ31u8E6dUYfqPUe9Vq+kfEPhnTvEtkbe+i+cD93MvDxn2P9K8O8T+D9S8L3GLhfNtGOI7lB8p9j6GuCrRcNVsfY5fmtPFLllpPt39Dn6KKKxPWQtFFFAwpaSlpDCiiigYtFFFA0JS0lLSGFFFFBQUtJS0AFLSUtIaEpaSloGFFFFIoKWkpaAEpaSloGFFFFBQUUUUhhRRRQAUUUUAFFFOjjeWRY40Lu5CqqjJJPamBZ0zTbrV9RhsbOMvNK2B6AdyfYV9B+HNAtvDmkR2NuAWHzSyY5kfuTWP4F8IJ4c07z7lQ2o3Cgyt/zzH9wf1rrq9LD0eRcz3Pgs8zX61P2NJ+4vxf+XYKgvZvs9jcT/wDPOJn/ACGanqlrALaHqCjqbaQD/vk10PY8KCvJJny+7mSRnY5ZmJJ+tJSDpS15R+ioWnCm04UFoKWkpaRSFoFFApFoWl70lL3pFIKWkpaC0FLSUtItB2paTtS0ikFFFFIsKKKKACiiigArR0C3N14h02ADO+5Qfrms6uu+G1ibzxnbORlLZGmb24wP1NXBc0kjnxlVUsPOb6Jnu1FFFeyflYUUUUAZWueHdM8RWn2fULcPj7kg4dD6g14z4p+H2p+Hi9xCDeaeOfNRfmQf7Q/rXvdIQCCCMg9QayqUoz9T0MFmVbCu0dY9v62Plalr2jxX8MbPVN95pGy0vDy0XSOQ/wDspryHUdNvdJvGtL+3eCZf4WHX3B7iuGpSlDc+wwePo4qPuPXt1KopaQUtZHoIXvRR3opFoWl70lL3pFIKKKKRYtFFFBSCiilVWdlVVLMxwAByTSKLel6Zc6xqUFhZpumlbA9FHcn2FfQug6LbaBpEOn2w+VBl37ux6sawfAPhEeHtO+03SD+0blQZP+ma9lH9ap/Ebxq+gWy6bp7gahcLkuP+WKev1PavRo01Sjzy3PiM0xk8xxCw2H+Ffi+/ouhteIPG+i+HGMVzOZbkDPkQjc349h+NcZN8ZG8z/R9GGzt5k3P6CvK2d5HaSRmd2OWZjkk+pNKKyliJt6aHfh8jwsI/vFzP+ux67D8U9O1SxubO+tZLKSWF0WQNvTJBxnuK8kUYUA9qBS1jUqSn8R6mDwVHC83slZMWlpKWsj0ohRRRSKPoPwNObjwXpbk5Ii2fkSP6V0Ncx8PkKeBtNz3Vj/48a6evZp/AvQ/LccksVUS/mf5hRRRVnKFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFIzBVLMQFAySTwKAForzPxJ8WILSd7XQ4EuXU4NxJ9zP+yB1+tcXN8RvFM8m7+0vL/2Y4lAH6VhLEQi7Hr0MlxVWPM7R9T6AorxDS/ipr1pIovRDexdwy7Gx7Ef1r1PRvFml63o8mowS7EgUtPG/DRYGef8AGqhWhPYxxeV4nDK81dd0al7f2mm2rXN7cRwQr1d2wK4q9+LGiQSFLa3urrH8QUIp/Pn9K808VeJrrxNqjzyMy2qEi3hzwq+p9zWHXLUxUr2gfQ4Hh2lyKWJu2+nRHstn8WtGmkC3NrdWwP8AFgOB+XNdpp+pWWq2q3Njcxzwn+JDnH19K+Zq1NB1++8O6il3ZSEDP7yIn5ZF9CP60qeLkn75pjOG6MoN4d2l2eqZ9IUVT0rUoNX0u3v7Y5inQMB3HqPwqzLIkMTyyuEjRSzMTwAOprvTTVz4uUJRk4taoz9f1y08PaTNqF43yoMIg6u3ZRXztrmt3niDVZdQvXy7nCoPuxr2UVq+N/FcnijWS0bEWEBK26evqx9z/KuZrhrVed2Wx9hlWXrDQ55/G/w8v8wooorA9cWiiigYtFFFIYClpBS0DCiiigYtFFFAwpaSlpDCiiigYtJS0lIoWiiigYUUUUDFooopDFNFBooGFFFFIoKWkpaAEpaSloGFFFFBQUUUUhhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABS0lLQAV6r8Hk/cas/+3GP0NeVV658IUxpGoyf3p1H5L/9et8N/FR42fu2An8vzR6PRRRXqn54FFFFABRRRQAV5H8YtI23FjrEa8ODBKR6jlf6165WB400j+2vCd9aquZVTzYv95eR/h+NZ1Y80Gjty+v7DExm9tn8z5xpaSlrzT7wUUUCikUhaKKKChaKKKRQtFFFA0LRRRSKQUtJS0FB2oo7UUikLRRRSKClpKWgpBRRRSKCiiigAooooAKKKKAClpKWgDpvh/Ym+8aWIxlYCZm9to4/Wvfa8z+EmkGO2vNXkXHmnyYifQcsfzx+VemV6eFjy079z8/4hxCq4xxW0Vb9WFfOvjjX5PEHie4l3k21uxhgXsFB5P4mvoK+Zk0+5dfvLExH1wa+WcknJ6nrSxMnZI1yClFznUe6svvHCnCminCuI+riOFLSClpGsR7MztudizHqScmkooqTaItFFFSaoK7n4VSlPFzxjOJLZ8/gRXDV6F8JLQya/eXWPlht9ufdj/8AWrWh/ER52btLA1G+x7FRRRXrn5oFFFFABRRRQAUUV5h8RfHzWbSaJpEuJ8YubhT/AKv/AGV9/U9qic1BXZ0YXDVMTUVOH/DGj4y+JFtojSWGl7LnUBwz9UhPv6n2rxvUNSvdWu2ur+5kuJm/ic9PYDsKqe559aWuCpUlN6n2uDwNLCxtBa9+otFFFZHeLS0lLQNBRRRSKFpaSloKJbW6uLK5W4tZpIZkOVeNsEV6p4T+KCztHY6+VjkPypdqMK3+8O31rybvRVwqSg9DlxeBo4uPLUWvfqj6mVlZQykMpGQQcgilrxHwP49m0OVNP1J2l01jhWPLQfT/AGfava4pY5oklidXjcBlZTkEHvXo06qqK6PhsfgKuDqcs9U9n3H0UUVocIUUUUAFFFFABRRRQAVFc20F5bvb3MSSwyDDI4yCKlooGm07o8a8YfDOfTvMv9EV57QZZ7fq8Y9vUfrXnVfVVcJ4w+HNprfmXumbLXUOrL0SU+/ofeuSrh+sD6XLs7tanifv/wA/8zxGirF9Y3WmXklpewPBPGcMjj9fcVXrjPp4tNXQUtJS0igooooGLRRRQNCUtJS0hhRRRQUFLSUtABS0lLSGhKWkpaBhRRRSKClpKWgBKWkpaBhRRRQUFFFFIYUUUUAFFFFABXrnw58F/Y0TW9SixcOM20TD/Vj+8fc/pWN8O/BX9oyx6zqUf+iRnMETD/WsP4j/ALI/WvYa7sNQ+3I+Sz7Nt8LRf+J/p/n9wUUUV3HyAUyVBLE8bdHUqfxp9FAHytdQG2vZ7dvvRSMh/AkVFXQ+ObI2HjXVIsYV5fNX6MM/41z1eXJWbR+iUZ89OM+6QtOFNpwqTZBS0lLSKQtAooFItC0vekpe9IpBS0lLQWgpaSlpFoO1LSdqWkUgooopFhRRRQAUUUUAFet/CPSzFp15qjrzO4ijP+yvX9f5V5RBDJczxwQqWllYIgHcngV9IaHpiaNolpp8eMQxhSfVu5/OuvCQvPm7HzvEmK9nhlRW8n+C/pGhRRRXonwoUUUUAFFFFABWbrOg6dr9mbbULdZV/hboyH1B7VpUUmk1ZlQnKElKLs0eD+LPh/f+Hd11blrvTwf9YB80Y/2h/WuPr6nZVdSrAMpGCCOCK8f8feAf7O8zV9IjP2QnM8Cj/Vf7Q/2fbtXFWw/L70T6zK859q1Rr79H39fM8570UUVyH0qFpe9JS96RSCiiikWLRRRQUgr1P4b+DNvl69qUfzHm1iYdP9s/0/Osf4f+CjrNwuqahGRp8TZjQ/8ALZh/7KP1r2gAAAAAAcADtXZhqF/fkfLZ9m3KnhaL1+0/0/z+4WvmjxPqL6r4o1G8ck7p2Vc9lU4A/SvpfqMV8vavbPZa3f20gw8dw6kf8CNa4rZHncPqPtJvrZFQU8UwU8VxM+siOFLSClqWbxFpaSlqTeIUdqKt6VZtqGrWdmoyZ5lT8Cef0oSvoOUlGLk9kfQfhi1Nn4X0yAjBW3Qke5GT/OtamooRFRRhVGBTq9pKysfk9WbqTc31dwooopkBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABXmvxZ8SS2VlDotq5SS6UvOwPPljjH4n+VelV4P8AFYufHEgbO0W8e36c1jXk1DQ9TJ6UamKXN01OLFOpop1ecfcRHVZtr25tI7iOCVkS4j8uUA8Muc4NVqcKk1STVmLS0lLUs6IhRRRSLPaPhPcNL4VmhYkiG5ZV+hAP9azPiv4oNvbroFpJiSYb7kg9E7L+P8queAp4tA+Hlzqt18sZeSb6gYUD8SK8e1LUJ9V1K4v7lt007l29vQfh0rvc3Gio9WfG08JGtmVWq/hi/wASrS0lLXMe8FFFFAxaKKKBi0UUUhgKWkFLQMKKKKBi0UUUDClpKWkMKKKKBi0lLSUihaKKKBhRRRQMWiiikMU0UGigYUUUUigpaSloASlpKWgYUUUUFBRRRSGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFLSUtABXs/wAJ4tnhWaT/AJ6XTfoBXjFe6/DWHyvBFoT/AMtHd/8Ax4j+ldOEX7w8DiSVsFbu1+p11FFFemfBBRRRQAUUUUAFFFFAHzd4x0n+xfFd/ZhcR+Z5kX+43I/w/CsSvVfjHpY/4l+rIOcmCQ4/Ff615VXmVY8s2j73L6/t8NGfXr8hRRQKKzO5C0UUUFC0UUUihaKKKBoWiiikUgpaSloKDtRR2opFIWiiikUFLSUtBSCiiikUFFFFABRRRQAUUUUAFWLGzn1G+gsrZd007hFHuar16t8LfDJjjbXrqPDOClqCOi92/HpWlKm5y5TizDGRweHdV79PNnoOkabFo+k2unwD5IIwufU9z+Jq7RRXrpWVkfmU5OcnKW7GuodGQ9GGDXy7qNq1jqt3aMMGGZ0x9Ca+pK8e+KPhGaC+fxBZxl7eXH2pVHKN03fQ1hiINxuuh7OR4iNOs6cn8X5nmgpwpopwrgPsYjhS0gpaRrEdRRRUm0RaKKKk1QV7j8M9GbTPC63Eq4mvW8056hei/pz+NeW+EPDz+I9fitip+zR/vLhvRR2+p6V9BoixxqiKFVQAoHQAV24Snrzs+U4lxqUFhY7vV/p/mOooorvPjgooooAKKKR3VEZ3IVVGST2FAHJfEDxX/wAI1ouy3Yf2hdZSEf3B3f8AD+deAszOzO7FmY5LE5JNbfi/Xn8ReI7m93HyVPlwL6IOn59fxrDrzq1Tnl5H3GW4NYaik/ier/y+QUtJS1keiLRRRSLFpaSloGgooopFC0tJS0FB3oo70UikFek/DTxg1pcJoV/Jm3lOLZ2P3G/u/Q9vevNqcCVYMpIYHII7GqhNwldHPi8LDFUnSn1/B9z6lorm/BHiD/hIfDkM8jA3UP7qf/eHf8RzXSV60ZKSuj85rUpUajpz3QUUVl614g03QLbztQuVjz91By7/AEFDaSuyYU5VJKMFds1Kguby1so/MuriKBB/FI4UfrXkGu/FTU70tFpUYsoOnmN80h/oK4e6vLm9lMt3cSzyHq0jlj+tcs8XFfDqfRYXhutUXNWly+W7/wAj3a7+IXhm0JU6isrDqIkLVnN8V/DobAW8Yevk/wD168UorB4uoetDhvBpe85P5/8AAPbovip4bkOGa6j92h4/Q1r2fjbw5fELFqsAY/wyEp/OvnqjrTWLn1FU4Zwkl7smvu/yPqGOWOZA8UiSIejKcin18zWOqX+myeZZXk9uw/55uQPy6V2+i/FbUrUrHqsCXkXQyJ8jj+hraGLi/i0PJxPDWIpq9GSl+D/y/E9I8ReF9N8TWfk3sWJVH7udPvofY+ntXh3iXwlqXhi62XSeZbMcRXKD5W9j6H2r3TRPE2leIId9hdKzgZaJvldfqK0Lu0t761ktrqFJoZBhkcZBrSdKNVXRxYPMMRgJ+zqJ26p9PQ+XaWu78bfD2XQg+oaYHm07OXQ8tD/ivv2rhBXBODg7M+xw+Jp4imqlN3QUUUVJ0C0UUUDQlLSUtIYUUUUFBS0lLQAUtJS0hoSlpKWgYUUUUigpaSloASlpKWgYUUUUFBRRRSGFFFFABXY+BfBj+IrsXd2pXTIW+Y9PNb+6Pb1NU/B/hK48T6hg7o7CIjz5R3/2R7n9K94s7SCwtIrW1iWKCJdqIo4Arrw9DnfNLY+dzvN/q8fYUX7738v+CSxxpDEscahEQBVVRgADtTqKK9E+F3CiiigAooooA8g+MOjtHfWWsRr8ki+RKR2Ycr+mfyrzCvpzXtGt9f0a4065HySr8rd0bsw+hr5w1XS7rRdTm0+9j2TQtg+jDsR7GuHEQtLm7n12S4tVKPsnvH8inThTacK5z3EFLSUtIpC0CigUi0LS96Sl70ikFLSUtBaClpKWkWg7UtJ2paRSCiiikWFFFFABRRU9naT397DaWyF5pnCIo9TTBtJXZ3Hwt0D7drD6tMmYLPiPPQyH/AfzFey1meH9Gh0HRbfT4cHy1+dv77HqfzrTr1qNP2cLH5pmmNeMxLqLbZen/B3CiiitTzgooooAKKKKACiiigApGVWUqwDKRggjIIpaKAPCfiB4S/4R3UxdWqH+zrpiUx/yzbuv09K46vpXXtHg17RbjT5wMSr8rf3WHQ/ga+cLu1msbya0uFKzQuUcH1FediKfJK62Z91kuPeJo8k370fxXcipe9JS965j20FFFFIsWuu8EeDJfEl4Lm5DJpkTfO3Qyn+6P6mofBvg648T3nmSbotOiP72UdWP91ff37V7raWlvYWkVraxLFDEoVEUcAV1UKHO+aWx4Gc5wsOnQov33u+3/B/IfBDFbQJDDGscUahURRgACpKKK9E+Hbbd2FeRfFbwq8dx/wAJDaJmNwEulA+6egb6Hoa9dqOeCK5gkgnjWSKRSrowyCD2qKkFONjpweKlhaqqR+fofLApwr0bxH8KL23nkn0JluLdjkW7th09gTwRXEXmiarpzEXmnXUOO7RHH51506co7o+5w2MoV0nTkvTr9xSFLTc84PB9KdWTPQiLS0lLUm8Qrt/hdphvfFX2tlzHZxl8/wC0eB/WuIr2/wCGGkf2d4XF064lvX80/wC4OF/qfxrbDw5qi8jys8xPsMHK28tF89/wO1ooor1T86CiiigAooooAKKKKACiiigAooooAKKKKACiiigArx34x6c8eq2Gohf3csRhZv8AaByP0NexVm67olp4h0mXT7xSY35Vh1RuzD3rOpDnjY7MBiVhq8aj26nzKKdWx4j8Mah4Yvzb3ibomP7qdR8sg/ofascV5sk07M+9pVI1IqcHdMdThTacKhnTEWlpKWpZvEKnsrObUL6Czt1LTTuEUD1NQV6t8MPC5t4j4gvU2s6kWysOi93/AB7e1XSpucrHLmGMjg6Dqvfp5szviZeR6Roml+FbRsIkYkmx3A4X8zk15hWx4r1Y614ov77JKNKUj9kXgfyrHrapK8jzcFRdKilLd6v1e4UtJS1mdYUUUUDFooooGLRRRSGApaQUtAwooooGLRRRQMKWkpaQwooooGLSUtJSKFooooGFFFFAxaKKKQxTRQaKBhRRRSKClpKWgBKWkpaBhRRRQUFFFFIYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUtJS0AFfQ3g2D7P4O0qMjB+zqx/Hn+tfPO0udo6scCvprToRb6Zawj/lnCq/kBXZg17zZ8txRO1KnDu2/uX/AASzRRRXoHxgUUUUAFFFFABRRRQByXxKsvtnge+OMtAVmH4Hn9Ca8Ar6a8Q24uvDmpQEZ320g/8AHTXzIvQVxYle8mfWZBO9GUez/McKKBRXKe+haKKKChaKKKRQtLSUUDQtFFFIpBS0lLQUHalpO1FIpC0UUUigpaSloKQUUUUigooooAKKKKACiitnw14bvPE2pLa2wKxLzNMRxGv+PoKaTk7IirVhSg5zdki94K8KSeJtVHmKVsICDO/97/ZHua96iijgiSKJAkaKFVVHAA7VU0nSbTRdNisbKMJDGPxY9yfUmr1erRpKnHzPzrNcyljq11pFbL9fVhRRRWx5YU10WRGR1DIwwysMginUUAeS+MvhgYjJqHh+MsnLSWfce6f4V5gQVYqwKsDggjBBr6prjPGHw/s/ESvd2m211IDO8D5ZfZh/WuWrh76wPo8uzpwtTxG3f/M8KFLVi+sLrTL6WzvIWiuIjhkP8x6j3qvXCz62DUldbDqKKKk3iLU1tbTXl1FbW0ZkmlYKiKOSTUSqzuqIpZ2OFVRkk+le1+AfBQ0K3Go36A6lKvCnnyVPb6+tXSpOpKyOTMcwp4Kjzy1b2Xf/AIBs+EfDUPhnRltxhrmT57iQfxN6fQVv0UV60YqKsj83rVp1qjqTd2wooopmYUUUUAFch8SdYOk+D7hY22zXZECY68/eP5Zrr68c+MWo+bq9hpyt8sERlYf7THA/QfrWVaXLBnfllH22KjF7LX7jzToKXtSUvavOPuQpaSloGLRRRSLFpaSloGgooopFC0tJS0FB3oo70UikFLSUtAzvfhTqhtPEklgzfu7yM4H+2vI/TNe0183eGbk2fifS5wfu3KZ+hOK9L8f+PDp3maRpMn+lkYnmX/ll7D/a/lXZQqqNN83Q+VzjLqmIxsVSXxLX5dX+Bd8ZfEKDQ99jp2y41Dox6pD9fU+1eOXt9daldPdXs7zzueXc5/D2FVySSSSSSckk8k0nauarVlUep7+Ay6jg4Wgrvq+oUtJS1keigooopFIKKKKACiiigCWC4mtZ0nt5XimQ5V0bBFeqeEPiWty0dhrrKkp+WO66Kx9G9D715NRWlOrKm7o4sbgKGMhy1Vr0fVH1EQsiEEBkYcg8givHfH3gE6Y0mraRETZE7poFHMPuP9n+VS+AfHbWMkekatMWtWO2CZzzEewJ/u/yr10hXQhgGVhgg8givQThXgfFSjicnxNnqn9zX+f5HyzRXefEHwR/Yc7app0Z/s6VvnQf8sWP/sp/SuDrhnBwdmfX4bEU8RTVSm9GLRRRUnQhKWkpaQwooooKClpKWgApaSlpDQlLSUtAwooopFBS0lLQAlLSUtAwooooKCiiikMK3fC3hi78T6mLeEFLdCDPPjhB6D1J9Ki8N+HLzxLqa2tqpWNeZpiOI1/x9BXvmjaNZ6FpsdjZR7Y0HJ7ue5PvXTQoe0d3seHnGbxwcfZ09Zv8PP8AyJdM0y00jT4rKyiEcMYwB3PqT6k1boor00raI+BlJyk5Sd2wooooJCiiigAooooAK5jxl4NtfFViOVhv4gfJnx/463qP5V09FKUVJWZpSqzpTU4OzR8valpl5o9/JZX8DQzoeQehHqD3FVRX0j4i8Mad4msvIvo8SL/qpl+/GfY+ntXhnibwlqXhe72XSeZbOcRXKD5W9j6H2rgq0XDVbH2WX5nTxS5ZaT7d/QwaWkpawPWQtAooFItC0vekpe9IpBS0lLQWgpaSlpFoO1LSdqWkUgooopFhRRRQAV6z8L/Cxt4Dr15HiWUbbZWH3V7t+P8AKuP8D+FX8SasGmUjT7chpm/vHsg+vf2r3lEWNFRFCoowoA4ArtwtK752fLcQ5lyR+q03q9/Tt8/yHUUV5f8AEP4gS2U8mi6PLtmUYuLheqf7K+/qa7JzUFdnymFwtTE1PZw/4Y7PWfF+h6CxS+vkEw/5Yx/O/wCQ6fjXNSfF/RFfCWd86/3tqj+teLFmdmdmLMxyWY5JNLXHLEzex9TRyHDRXvtt/ce72HxQ8N3jhJJprVj3njwPzGa663uIbqFZreVJYmGVdGBB/Gvlytvw74o1Lw1eCWzlLQk/vLdj8jj+h96qGKd/eMsTw9Bxvh3Z9mfRtFZ2h6za6/pUOoWjfu5B8ynqjd1PuK5vxb8QrTw/I1lZot1qAHzLn5Iv94+vtXVKpGMeZvQ+cpYOvVq+xhH3vy9TtaK+fL/xz4k1Byz6nLCp6JB8gH5VVh8Va/A+6PWLwH3lJH61zvGRvse7HhjEON3NX+Z9G0V47onxV1G1kWPV4lu4OhkjAWRf6GvVtM1Sy1ixS8sZ1mhfuOoPoR2NbU60Kmx5ONyzEYN/vVp3Wxcrxn4saOLTXINTjXEd4m18f31/xGPyr2auO+Juni98GzyhcvauswPtnB/Q0q8eamy8oruji4Po9H8/+CeFUvekpa8s/QkFdV4N8GXPia6EsoaLTY2/eS9C5/ur/j2qfwV4Gn8RTLeXYaHTEPLdGm9l9vevbrW1gsraO2tolihjXaiKMACuqhh+b3pbHgZvnKw6dGg7z6vt/wAH8hLKzttPtI7S0iWKCJdqIo4FT0UV6B8U25O73CiiigQUUUUAFIQCMHkGlooAzLzw9o+oDF1plrLnuYxn865zUPhd4eu1b7Mk1nIejRPkD8DXbUVEqcZbo6aWMxFH+HNr5ngniXwFqvh1WuMC6sh1miHK/wC8O316Vy1fUbKrqVZQysMEEZBFeK/ELwcuhXQ1GwQjT52wyD/lk/p9D2rir4flXNHY+uyfO3iJKhX+Lo+//BOW0PS5Na1u00+MH99IAxHZepP5V9IQQpbQRwRLtjjUIoHYDivNvhPoPlwXGtzJ80n7qDI/hH3j+J4/CvTa2wtPlhzPqeRxFjPbYn2Udofn1/yCiiiuo+fCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAqajptnq1lJZ30CTQSDlWHT3Hoa8R8YeAbzw27XVtuudMJ4fHzRezf417zTJI0ljaORVdGGGVhkEVlUpKa1O/A5hVwkrx1j1R8tCnCus8feFB4b1cSWqkafdZaL/Ybuv+FcmK82cXF2Z99hq0K9NVIbMWlpK6/wZ4JuPEs4ubgNDpiH5pOhkP8AdX/GpjFydkbVsRTw9N1KrskSeBPBsniG8F5doV0yFvmz/wAtmH8I9vWvU/F9+ui+D9QniwhSHy4gOACflGPzrZtbWCytYra2iWKGJQqIowAK8/8AjDemHw9Z2YODcXG4+4Uf4kV6KpqjTdtz4erjJ5njYc3w30Xl/meLDgUtFFcR9YFLSUtIYUUUUDFooooGLRRRSGApaQUtAwooooGLRRRQMKWkpaQwooooGLSUtJSKFooooGFFFFAxaKKKQxTRQaKBhRRRSKClpKWgBKWkpaBhRRRQUFFFFIYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUtJS0AXtGt/teuWFuOfMuEX9RX0r0GK8D+H1r9q8bWAxxEWlP4CvfK9DBr3Wz4riepevCHZX+9/8AACiiiuw+YCiiigAooooAKKKKAIL0A2FwD0MTD9K+WhX1HqD+Xpt05/hhc/oa+WwcjPrXHit0fT8PfDU+X6jhRQKK5D6RC0UUUFC0UUUihaKKKBoWiiikUgpaSloKDtRR2opFIWiiikUFLSUtBSCiiikUFFFFABRR7DkntXoHhP4bXOpGO91hXtrTqsPSST6/3R+tXCEpu0TmxWLo4WHtKrsvz9DA8L+Eb/xPd4iBis0P724YcD2Hqa900fRrLQ9OjsrGIJGvU93PqT3NWLS0t7G1jtrWFIYYxhUQYAqevSo0VTXmfBZnm1XHSttBbL9WFFFFbnkhRRRQAUUUUAFFFFAHF/ETwqmuaO17bxj+0LRSykDmRB1U/wAxXhlfU55GDXzl4s01dJ8VajaIMRiUug/2W5H864sVBK0kfW8O4uUlLDy6ar9THp0cbyyLHGjO7nCqoySfQVLY2N1qV5HaWcLzTyHCoo/zgV7X4M8B23h2Nby82z6kw+91WL2X39656dKVR6Hs47MqWCheWsnsv66FPwJ4CXR1TU9URX1AjMcR5EA/+K/lXf0UV6UIKCsj4LFYqriqrq1Xd/l6BRRRVnMFFFFABRRRQAV84eN9Q/tPxlqc4bKLL5SH2Xj+lfQ99cfZdPubg8eVEz/kM18tPIZpHlb7zsWP4nNcuJeiR9DkFO851O2n9fcJS9qSl7Vxn04UtJS0DFooopFi0tJS0DQUUUUihaWkpaCg70Ud6KRSClpKWgY5HaN1dGKupypHUGhmZ3Z3YszHLMTyT602lpFIKO1FHakUFLSUtBSCiiikUgooooAKKKKACiiigAr174aeLjfQDRL6TNxCubd2PLoP4fqP5V5DU1pdzWN5Dd2zlJoXDow7EVrSqOnK6OHMMDDGUHTlv0fZn0zcW8N3byW88ayQyKVdGGQQa8B8aeFJfC+rbUDNYzktbyHt6qfcV7h4f1iLXtEttQiwPMX51/usOo/Ok8QaHbeIdHm0+5HDjKPjlG7MK9GrTVWN0fDZfjJ4DEOE9r2a/X5HzZRVrUtOuNJ1Gewu02zQttb0PoR7Gqtea9D7uMlJJrZiUtJS0igooooKClpKWgApaSlpDQlLSUtAwooopFBS0lLQAlLSUtAwooooKCtbw94fvfEepLZ2i4UcyykfLGvqf8KPD/h698R6ktnZrgDmWUj5Y19T/hXvWg6DZeHtNSyskwBy8h+9I3qa3oUHUd3seLm2bxwcOSGs3+HmxdD0Oy8P6allZR4Ucu5+9I3qa06KK9NJJWR8DOcqknObu2FFFFMgKKKKACiiigAooooAKKKKACoLuzt7+1ktbuFJoJBhkcZBqeigabTujxPxj8N7nRjJfaSHubDktH1eEf1HvXA19Vda858ZfDSHUTJqGiKkN3957fokv09D+lcdXD9YH02XZ1e1PEv5/wCf+Z45QKknt5rW4e3uInimjOHRxgqajFcjPp001dC0vekpe9SWgpaSloLQUtJS0i0HalpO1LSKQUUUUiwrR0TRbvX9VisLRfnflnPRF7saq2dncaheRWlpE0s8rbUQd/8A61e+eEfC1v4Y0sRDD3koBnmx1PoPYVvRouo/I8rNszjgqWms3sv1NHRdHtdC0uGwtFxHGOW7u3dj7mtCiivUSSVkfnU5ynJyk7tmZ4i1P+xvD19qH8UERZR6t0H64r5neR5pXllYtI7FmY9ST1Ne7fFSYxeB51B/1s0afrn+leD1x4l+8kfU5DTSoyn1b/IUU6minVyn0KHClpBS1Jqjo/Dvi678OaZqVrbZ33Sjym7Rt0LflWAzM7s7sWZjlmJySfWmUtDk2kmOnRhCUpxWstxaKKKk6kFdD4Q8Tz+GdXSXczWcpC3EXYj+8PcVz1FOMnF3RnWowrU3TqK6Z9QxSJNEksbBkdQysO4PSqetWovdDv7YjPmQOo+uDisf4e3b3ngqwZzlow0WT/skgfpXTEAqQehGK9iL5437n5fVg8PXcOsX+TPlroMHtxXoHgn4eyasY9S1ZGjsfvRwnhpvr6L/ADrb8J/DZIbx9R1pFYiQmC1PIAzwW9fpWj8QfG3/AAjlsun6eV/tGZc56iFPXHr6VxU6KiueofVYzNZ15/VsFu9329P8/uN7VvEmh+GLdIru5jh2riO3jGWx7KOlcjcfGLTkci30u6lXszOq/pXkM08tzO888ryzOcs7nJY/Wm0SxMntoKhkWHiv3t5P7j2W0+L+kyuBdWF3bg/xAhwK7TSde0zXIPN068jnA+8oOGX6jqK+ZxVmyvbrTrtLqzneCdDlXQ4P/wBcURxUl8Wo6/D9Ccf3L5X96PqCiuU8D+ME8UaeyThY9QgA85B0Yf3h7U/xb41svDEQj2/aL9xlIFPQerHsK7PaR5ea+h8x9Rr+3+rqPvf1+B1FFfPmqeN/EGrSMZdQkhjPSK3OxR+XJrJXVNQR96390G9RM2f51zPGRvoj3afDFaUbzqJP0v8A5H0zRXguk/ELxDpci77r7ZCOsdxzx7N1Fes+GPF+n+J7cmAmK6QZkt3PzL7j1HvWtOvCpotzzcdk2Jwa55ax7r9ToaKKK3PJCqeqabBq+mXFhcjMM6FTjqPQj3FXKKGrqzKjJxkpR0aK1hZQ6dYQWVuu2KBAij6VZoooWgpScm292FFFFAgooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigDlfiJpq6j4NvDtzJbgTofQjr+ma8CFfTOs273eiX1tGm+SW3dFXPUlSBXHeEPhta6QI73Vtl1fDBWPrHEf6n3rkr0XOasfSZTmdPCYaSqProjmvBvw5m1Mx6hrKPDZ/eSA8PL9fQfqa9gggitoEhgjWOJBtVFGABUlFb06UaasjycdmFbGT5qj06Logrxr4yXO/W9OtQeI4C5HuTj+ley14P8AFeUv43dP+edvGPzyajEP3DoyWN8Un2TOJooorhPsQpaSlpDCiiigYtFFFAxaKKKQwFLSCloGFFFFAxaKKKBhS0lLSGFFFFAxaSlpKRQtFFFAwooooGLRRRSGKaKDRQMKKKKRQUtJS0AJS0lLQMKKKKCgooopDCiiigAooooAKKKKACiiigAooooAKKKKAClpKWgD0L4R2nma7e3RHEMAUH3Y/wCANew1558JLPytCvLsjmefaD7KP8Sa9Dr1cMrU0fnWe1faY6flZfcgooorc8gKKKKACiiigAooooAxvFt19j8JarPnBW2cD6kYH86+ax0r3P4r34tfCH2YH57uZUx7D5j/ACFeG1w4l3lY+uyGny4dz7v8hRRQKK5j3ULRRRQULRRRSKFooooGhaKKKRSClpKWgoO1FHaikUhaKKKRQUtJS0FIKKK09I8ParrsuzT7OSVc4MhGEX6seKEm3ZCnUhTjzTdl5mZWvoXhnVfEM4SwtiYwfmmfhF/H+gr0jw/8K7K0Kz6zL9rlHPkpxGPr3NegwwQ20KwwRJFGowqIuAPwrrp4RvWeh83juJKcLwwy5n3e3/BOU8MfD7TNA2XE4F5fD/lq6/Kh/wBkf16119FFd0YRirRPkcRiauInz1ZXYUUUVRgFFFFABRRRQAUUUUAFFFFABXlPjHwjqPiPx8y2cWyEwRmW4cfInUfifavVqKipBTVmdeDxk8JN1Ke9rGH4b8K6d4ZtPKtE3zsP3tw4+d/8B7VuVzHiDx3o3h8tFJMbi6H/ACwh5I+p6CvPNT+Kmt3hZbJIbKM9MDe/5nj9KzlWp01Y7KOWY7HS9q1v1Z7VSZr5wuvEet3pJuNVu3z280gfkKom7uicm6nJ9fMP+NYvGLoj04cLVGveqL7v+Cj6eor5qttc1azYG31O7jI/uymul0z4n+ILFgLlor2MdRKuG/76H9aqOMg91YyrcM4iKvTkpfge4UVyOgfETRtbZYZHNldNwI5jwT7N0rrq6YzjJXizwa+Gq4eXJVi0woooqjAxvFrmPwjqzLwRav8Ayr5pHQV9O+Ibc3fhzUoAMl7aQAf8BNfMS9BXHid0fUZA17Oa8xaXtSUvauU+gClpKWgYtFFFIsWlpKWgaCiiikULS0lLQUHeijvRSKQUtJS0DClpKWkUgo7UUdqRQUtJS0FIKKKKRSCiiigAooooAKKKKACiiigD0j4TayYdQudHkb5J186IHsw6/mP5V65Xzf4dvjpniPT7wHAjnXd/uk4P6GvpDtXpYSd4W7HwnEmHVPFKovtL8V/SPPvif4X/ALR03+2LWPN1aL+9AHLx/wCI6/nXjNfUzKrqVYAqwwQe4r578a+Hj4d8RTW6KRazfvbc/wCye34His8VTs+dHZkGO5ovDTeq29OxzlLSUtch9KFFFFBQUtJS0AFLSUtIaEpaSloGFFFFIoKWkpaAEpaSloGFbHhzw5e+JdRFraLtjXmWYj5Yx/j7VJ4Z8L3vie/8m3BS3Q/vpyOEH9T7V7xo2i2Wg6dHZWMQSNfvN/E59Se5rooUHU1ex4ubZvHBx9nT1m/w9f8AIZoWh2Xh/TUsrKPCjl3P3pG9TWnRRXpJJKyPg5zlUk5zd2wooopkBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQBzXivwZp/ii3LOBDfIuI7hRz9G9RXhOraTeaJqMljfRGOZPyYdiD3FfTdc54w8KW/ijSzHhUvYgTbzY6H0Psa561FTV1ue3lWayw8lTqu8Py/4B89UvepLm2ms7qW2uIzHNExR0PUEVH3rz2fbRaaugpaSlpFoKWkpaRaDtS0nalpFIKkggluZ44II2klkYKiKMljSQwyXEyQwxtJLIwVEUZLE9hXtngfwPH4fhW+vVWTUpF+ohB7D39TWlKk6jsjhzHMaeCpc0tZPZd/8AgE3gjwZF4bs/tFyFk1KZf3jdRGP7q/1NdfRRXqwioqyPzvEYipiKjq1HdsKKKKowON+KFnJeeCLkxgkwSJMQPQHB/nXggr6pnhjubeSCZA8UilXU9CD1r5z8WeG5vDGuS2bgm3c77eQ/xJ/iOhrjxMHfmPp8hxMeV0HvujDFOpop1ch9KhwpaQUtSaoWlpKWkbRFooopGqCiitDRNKl1vWbXT4QczOAx/ur3P5UJNuyJnOMIuctke3fD+1a08FaerDDSKZSP94k101RwQpbW8cEQxHGoRR6ADAqSvZhHlikfleIq+2rSqd239413EcbOxwqgk18ya5qcms67eahIxJmlJX2Xoo/KvovxBN9n8OanMOqWsh/8dNfMa9BXNinsj3uH6a9+p6IcKcKaKcK4z6dCinU0U6kbRNfw3rs3h3WE1CFd5VGRkzgMCO/44NUby8uNQvJbu6lMs8rFnY9zVcUopNu1hxpQU3Utq9L+QoooFFSdKCrOn39zpd/De2chjnibKkfyPsarUUbBKKknGWqZ9IeHtZi1/RLfUYht8xfnT+6w6j861K81+EFy7afqVqTlY5VdR6bhz/KvSq9elPngpH5jmOGWGxU6Udk9PR6hRRRWhxBRRTXdY0Z3YKqjLMTwBQA6q9xfWlp/x8XUMP8A10kC/wA68o8W/Ey5uZpLLQpDDbqdrXIHzyf7voPfrXnksslxIZJ5HlcnJZ2LE/nXJUxUYu0Vc+kwfDtWtFTrS5b9N3/wD6Xh1OwuW2QXttK3okqk/wA6t18tr8jbkJVh0K8Gu48JfEO90i4jtdUme509jtLOcvF7g9x7UoYtN2krGmL4aqU4OdCXNbpaz+R7ZRTI5UmiSWJw8bgMrA8EHvRXYfL2sPooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAr5/wDicc+PLz/rnH/6DX0BXgnxTj2eO7g/34YyPyrnxPwHs5F/vL9H+aOMoooriPrgpaSlpDCiiigYtFFFAxaKKKQwFLSCloGFFFFAxaKKKBhS0lLSGFFFFAxaSlpKRQtFFFAwooooGLRRRSGKaKDRQMKKKKRQUtJS0AJS0lLQMKKKKCgooopDCiiigAooooAKKKKACiiigAooooAKKKKACjtRUttA1zcw26jLSuqAfU4oBtJXZ754EsvsPgzToyMM8fmsPdjmujqK2gW2tYYF+7EgQfgMVLXtRXLFI/KcRVdWrKo+rbCiiiqMQooooAKKKKACiiq9/ew6dp9xezttigjLsfYCgaTbsjxz4uar9r8Q2+nI2Us48sP9tv8A62K8+qzqV/Lqmp3V/Mf3lxIZD7Z6D8BVavLnLmk2foGEo+woRp9l/wAOKKKBRUHUhaKKKChaKKKRQtFFLQNBRRRSKQUtJS0FB2oo7U+KOSZwkSPI5/hRST+lIobRXT6Z8P8AxHqeGWxNtGf47g7P06/pXZ6X8IrWPa+qX8k57xwDYv59f5VrGhUlsjgxGbYOh8U7vstTyZFZ3CIpZj0VRkmuq0f4eeINW2u1t9jgP/LS4+U49l617Npfh3SNGQCwsIYWH8YXLH8TzWpXTDCL7TPCxPE03ph4283/AJf8OcPovww0bTist8Wv5hz+84QH/dH9a7WKKOCJYoY0jjUYCouAPwp9FdMYRgrRR87iMXXxMuatJsKKKKs5wooooAKKiuLiG1gee4lSKJBlndsAV5/rfxa02zZodKga+kHHmsdsf+JqZTjHdnRh8LWxDtSjc9FpCQOpxXgOo/EfxNqDNtvRaoeiW6hcfieawJ9V1G5YtPqF1IT/AHpmP9a53io9EexT4erSXvzS/H/I+nN6Hoy/nTq+W1ubhTkXEwPqJDWhZ+JdcsCDbardpjsZCw/I5pLFLqjWXDk7e7UX3H0pRXjOkfFnVbVlTU7eK8j7snyP/hXpOgeL9H8RJiyuNs4GWgl+Vx+Hf8K2hWhPY8rFZZicMuacbrutUbtFFFanniEhQSSABySe1eS+NviNLPJJpuhylIVO2W6Xq/qF9B71e+J3i1rdP7BsZCJJFzdOp5VT0T8e/tXk9cWIru/JE+syTKIuKxNdX7L9X+gpJJySSScknvRSUtcJ9cgooopFIKKKKACu18JfEK90N47W/Z7rT+nJy8Q9j3HtXFUVcJyg7xMMThqWJh7Oqrr+tj6ds7231C0jurSVZYJV3K6ng1PXhfgLxc/h/UltblydNuGw4J/1THow/rXuYIIBBBB6EV6lGqqkbn53meXTwNbkesXs/wCuoMoZSrDIIwRXzHr+nNpPiC/sWGPJnYL7qTkfoa+na8d+L+imHUrXWY1+S4XyZSOzjofxH8qjERvG/Y6cjr8ld039r80eZ0vakpe1cJ9cFLSUtAxaKKKRYtLSUtA0FFFFIoWlpKWgoO9FHeikUgpaSloGFLRRSKQUdqKO1IoKWkpaCkFFFFIpBRRRQAUUUUAFFFFABRRRQAucHPpX01p0pn0y0mPJkhRvzAr5lPSvpXRlK6Hp6nqLaMH/AL5FduC3Z8pxSlyUn5v9C9XH/EbQP7Z8NPPEmbqyzNHjqV/iH5fyrsKQgMpVgCCMEHvXbKKlFpnymHryoVY1Y7o+WKWtvxdox0LxNeWYBEJbzIf9xuR+XT8KxK8lpp2Z+k0qkasFOOz1CiiikahS0lLQAUtJS0hoSlpKWgYUUUUigpaSloASuk8J+ELzxRefLuhsYz+9nI/RfU1a8G+B7nxLMLm43QaYjfNJ0Mnsv+Ne32VjbadZx2lpCsMEYwqKOBXVQw7n70tjwM2zqOGTpUdZ/l/wSPS9Ls9HsI7KxhEUKDgDqT6k9zVyiivRStoj4iUpTk5Sd2wooooJCiiigAooooAKKKKACiiq13qFlYpuu7uGAf8ATRwtF7DjFydkrlmiucl8eeGIWw2rwt/uAt/IURePPDEzbRq0K/74K/zFR7SHc6fqOKtf2cvuZ0dFV7S/s75N9pdQzr6xuG/lVirvc5pRcXZoKKKKBBRRRQAUUUUAeX/FXwyHhXX7VMOmEugB1Xs34dK8o719Q3drFfWc1rOoaKZCjg9wa+atW06TSdXutPl+9byFM+o7H8q4MTC0uZdT7LIMY6tJ0Zbx29P+AU6Wkpa5T6JBS0lLSLQdqmtrae8uY7a2iaWaRtqIgySak07TrvVr2OzsYWmnkPCjsPU+gr3Hwf4KtfDNt5sm2fUZB+8mxwv+yvoP51rSouo/I87MczpYKGusnsv8/IreCfA0Ph6Fby8Cy6m45PURD0X39TXZ0UV6cIKCsj4DE4mpiajqVXdsKKKKowCiiigArB8W+GrfxPor2kmEnT54Jcfcf/A9DW9RSaTVmXTqSpzU4OzR8tXdncafeTWl1GY54WKOp7Goq9o+Jfg/+1bI6zYx/wCm26fvVA/1sY/qP5V4uK82pTcJWPvcBjI4qkprfqvMcKWkFLWJ6KFpaSlpG0RaKKUAkgAEknAAHWkaIT2Azn0r2v4deEjolgdQvUxfXS8KRzEnYfU9TWX4C8AG3aPV9Zi/fD5oLZh9z0Zvf0Fem13Yahb35Hx2e5uqieGoPTq+/kgooortPlSlrFodQ0W+s1+9PA8Y+pBFfMDI0TtG6lXQlWB7Eda+rK8V+KHhNtO1JtbtI/8ARLpv3wA/1cnr9D/OubEwbXMj3sixMYVHRl9rb1PPRThTRThXCfWoUU6minUjaIopRSClFSzWIoooFFI1QUUU+GGS4njhhQvLIwRFHcnpQNu2rPW/hFaNHo9/dsMCacIvuFH+Jr0asvw5pC6HoFpp64LRJ85HdjyT+dalevSjywSPzDMcQsRip1Vs3p6bIKKKxNT8XaDo7mO91KFJR1jU7m/IVbaW5ywpzqPlgrvyNuuB+KutSWGgw6fA5WS9chyDzsHX8+BWhD8S/C00gT7e8ef4pIWA/PFcV8V7qG9vNJuLWdJrd4H2PG2QeRn+lYVqi9m+Vnr5XgqixkFWg0t9V2R57S0lLXmM++iFFFFI0PY/hXrbXujTaXM+ZLNgY8nny26D8DmivPPCOsPo2pzTI2A8JQ/mDRXoUa6UEpHxWaZPUnipTpLR6/5n0JRRRXYfMBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRSM6oMswUepOKYlxDIcJLGx9FYGgdnuSUUUUCCvFPjDbmPxNZz44ltsZ9wxr2uvMfjLY79M02+A/1UzRsfZhkfyrGurwZ6eUT5MXHzujx+iiiuA+0ClpKWkMKKKKBi0UUUDFooopDAUtIKWgYUUUUDFooooGFLSUtIYUUUUDFpKWkpFC0UUUDCiiigYtFFFIYpooNFAwooopFBS0lLQAlLSUtAwooooKCiiikMKKKKACiiigAooooAKKKKACiiigAooooAK6XwFY/b/GenoRlYmMzf8BGR+uK5qvS/hDYb73UdQI4jRYVPueT/ACFa0Y81RI4M0rexwdSflb79D1miiivXPzIKKKKACiiigAooooAK8w+LniHybOHQoH/eT4luMHog6D8T/KvQtW1S30bSrjULptsMCFj6k9gPcmvmvVtTuNZ1W41G6OZZ33Ef3R2A9gK58RPljyrqe1kuE9rV9rLaP5/8Ap0tJS1wn16FFFFBpFIWikB9OanhtLm4OIbaaU/7EZNA20tyKitm38JeIbrHk6NeEHu0e3+eK17b4Y+J7jBa2hgH/TWYcflmqVOT2RjPGYeHxTS+aOQpa9Itfg9fvg3eqQRjuI4yx/Wt20+EWjRYN1eXdwR2BCA/zq1h6j6HJPOsFD7V/RM8ap8MMtw+yCKSVvRFLH9K+gLPwF4ZssbNKikYfxTEv/Pit6C0trVQtvbxRKO0aBf5VosI+rOKpxJSX8ODfrp/mfP9j4H8SahgxaXKino02EH6109h8IdQlw1/qMEC91iUuf6CvX6K1jhYLfU86rxBi5/BaP8AXmcTp/wu8PWeGuEmvHH/AD1fC/kMV1dlpdhpqBLKzgt1H/PNAKt0VtGEY7I8qti69f8AiTbCiiirOcKKKKACiiigAooooAKxvEniWw8M6cbq9fLNxFCp+aQ+g/xq3q+q2uiaVPqF2+2GFcn1Y9gPc186eINevPEerSX92x54jjB4jXsBWNaryLTc9TLMveKnzS+Ffj5FnxJ4s1TxPdF7yUpbg5jtkOET/E+5rEFNFOFcDbbuz7OlThTioQVkgpaSlqTZC0tJS0ikFPilkhmWWJ2jkQ5V0OCD7GmUtIrc9h8CfEE6m8elaw4F2RiGc8CX2P8Atfzrttb1SLRdGutRm+7ChYD+83YfnXzSrMrKykqynIIPINdn4k8aya94T02wdj9qVybvjhtvCn8c5/CuuniGoNPc+cxmRxliYSpK0W9V2/4f8zk7u6mvrya7uHLzTOXdj3JqGjvRXGz6iKSVkFLSUtI0QUUUUikFFFFABRRRQAV7l8NtcbVvDKwTPuuLJvJYnqV/hP5cfhXhtd58KL42/iaa0J+W5gPH+0vI/TNdGGny1F5nj57hlWwcn1jqv1/A9orH8T6JH4h8PXWnPgO65iY/wuOVP51sUV6bV1Zn57CcoSU47o+VJYpLeaSGZCksbFHU9iODTe1ejfFfw39i1NNbt0xBdnbNgfdk9fxH6ivOa8ycXGVmffYXERxFJVI9QpaSlqTpFooopFi0tJS0DQUUUUihaWkpaCg70Ud6KRSClpKWgYUtJS0ikFHaijtSKClpKWgpBRRRSKQUUUUAFFFFABRRRQAUUUUATW0LXF1DAv3pJFQfUnFfTcMYigjjHRFCj8BXgvgHTjqPjGyUjMcBM7/8B6frivfa9DBx91s+L4nrJ1YUl0V/v/4YKKKK7D5c8z+LukeZYWerxr80LeTIf9lun6/zrySvpLxHpg1fw7f2JGWlhOz/AHhyP1Ar5twQcEYI4Irz8VG079z7Xh/Ee0wzpveL/B/0wooormPfClpKWgApaSlpDQlLSUtAwoooxk4AyT0ApFBXeeCfh/LrRj1HVFaLT85SM8NN/gv861PBPw5L+XqmuxYX70No3f0L/wCH516oAFACgAAYAHau2hh7+9M+WzbPFC9HDPXq/wDL/MZDDFbQJDDGscSDaqKMACpKKK7j5Btt3YUUVkaz4n0fQUzqF7HG+OIgdzn8BzSbS1ZcISnLlgrs16K8w1D4xWqMV07S5ZQOjzuEB/AZNYsnxe1xm/d2dkg9CGP9ayeIprqelTybGTV+W3qz2mivG7f4v6sjDz9PtJV77Syn+tdLpXxZ0e7ZY7+CayY8bj86fmOR+VCr031Jq5PjKavyX9NTv6KgtL22v7dbi0njnhbo8bAip62PNaadmFUtV1ay0axe8v51ihXuepPoB3NSX99b6bYzXl1IEghQs7H0r5/8T+JrvxPqbXMxKW6EiCHPCL/ie5rGtWVNeZ6mV5ZLGz10it3+iN/xD8TtT1F2h0vNja9A45lb8e34VxE00txIZJ5XlkPJZ2LH9ajpa82c5T1kz7zDYSjho8tKNv67hRRRWZ1ktvcT2koltppIZByGjYqf0ruvD3xR1CxZYNYU3tv080DEi/0auAoq4VJQd4s5sTg6GKjy1o3/AD+8+mNM1Wy1iyS7sLhZoW7r1B9COxq5Xzn4d8R3vhvUlurViY2IE0JPyyL/AI+9e/6TqltrOmQ39o+6GVcj1U9wfcV6VGsqi8z4PNcqngZXWsHs/wBGXaKKK3PICiiigArxn4t6aLfxBa6gi4W6i2sf9peP5EV7NXn/AMXLQS+Gba5xzBcjn2YEf4VjiI3ps9TJqrp4yHnp9/8AwTxilpKWvMP0AK1tA8PX/iO/FrYx5A5klb7kY9Sf6Vq+EvA994llWZ91vpyn5pyOX9lHf617dpWk2Wi2KWdhAsUK+nVj6k9zW9HDuer2PFzPOYYVOnS1n+C9f8ih4a8LWHhiy8q1XfO4/ezsPmc/0HtW5RRXopKKsj4mrVnVm51HdsKKKKZmFFFFABRRRQAUUUUAIRngjIr548b6KuheK7u2jXbBIfOhHordvwORX0RXkvxjtlF3pV0B8zI8ZP0wa58TG8L9j2sirOGK5Okl+Wp5iKWkFLXnH26FpaFBZgqgsxOAAMk13Xhv4Z6lquy41LdY2h52kfvHH07fjTjCU3aKJr4qjhoc9WVkchp2m3mq3a2ljbvPM38Kjp7k9hXsfhD4e2uhbL2/2XOoYyOMpF9PU+9dNo+h6doVoLbT7ZYl/ibqzn1J71o130sMoay1Z8fmWe1MSnTo+7D8X/XYKKKK6TwAooooAKr31lb6jZTWd1GJIJlKOp7g1YooGm07o+b/ABV4buPDGtSWcuWgb57eU/xp/iO9Yor6M8WeGoPE+ivaSYW4T57eXH3H/wAD0NfPN1azWN3La3KGOeFyjqexFedWp8j02PuMqx6xVO0viW/+ZEKdTRTqwPYiKKUUgpRUs1iKKKBRSNUFepfDPwiwZdfv48cf6JGw/wDH/wDCs/wN8P5NRePVNXiKWQ+aKBhgy+59F/nXsKqFUKoAUDAAHArsw1B355Hyue5xFReGoPV7v9P8xaQkAZJwB1pa5D4lay+keEZhC5We6YQIQeQD94/kDXdKXKrnydCk61SNOPU4rxz8Rp7y4l0vRZjFaoSktwhw0p7hT2X+dec9SSSSSeSe9MFPrzJzc3dn32Ew1PDwUKa/4I4U7JxtydoOQM8U0UorJndEdS0lLUs3iFFFFI0LNn/rW/3aK1fCemNqupywqudsJY/mB/WitoU5SV0eZi8bSo1OSb1PoaiiivWPzYKKKKACiiigAoorjvG3jq38Lwi2gVZ9SkXKxk/LGP7zf0FTKSirs1o0Z1pqnTV2zrZZ4oIzJNIkaDqzsAKxpvGfhuBykms2m4ddr7v5V4Bqmualrdw0+o3kk7E5Ck4VfovQVRFcssU+iPo6PD0bfvZ6+R9L2PiHR9SO2z1O1mb+6sgz+XWtOvldeCCOCOhHau38J/ETUNGnjttRle708nB3nLxD1B7j2NOGKTdpIzxPD04xcqEr+T3PcGZVUsxAUDJJPAFeVeK/ifL58lloBVUU7Wu2GSx/2B6e5q98TPFKx6Pbadp84P29PNeRD/yy7Y+v9K8iqcRXafLE3yTKITj9Yrq/Zfqy3d6lfX8hku7yedj1MkhNV0kkjYMkjqw6FWINNorhbdz6+MYpWS0Oq0P4ga5o0iq9w15bDrFOcnHs3UV7F4e8SWHiSw+02TkMvEsTfejPof8AGvnOtjwzrs3h7XIL2Nj5eQsydnQ9f8a6KOIlB2lseJmmS0cTBzpK0126+v8AmfRlc3490w6r4M1CFV3SRp5yD3Xn+Wa6KORZYkkQ5RwGU+oNDoroyMMqwwR6ivSaurHwlKbpVFNbpnykOaWtTxHpTaJ4ivtPYYWKU7OOqHlf0NZdeW1Z2P0GElOKlHZhS0lLSLCiiigYtFFFAxaKKKQwFLSCloGFFFFAxaKKKBhS0lLSGFFFFAxaSlpKRQtFFFAwooooGLRRRSGKaKDRQMKKKKRQUtJS0AJS0lLQMKKKKCgooopDCiiigAooooAKKKKACiiigAooooAKKKKACvc/hnp/2LwdDKww907TH6ZwP0FeHwxNcTRwoMvIwRR6knFfTGn2i2GnW1og+WGJYx+AxXZg43k5HzPE9floQpLq7/d/w5Zooor0D4kKKKKACio554baFpp5UijXku7AAVxWr/FPQdPLR2nmX8o/55DCf99H+mamU4x3ZvRw1au7U4tnc0V4rffFzW5yRZ2trar2JBdv14/SsOfx/wCKJzk6tKme0aqv9KxeJgtj1KeQ4qXxNL5/5HrfjLwpe+K47e2TUltbOM72j8vcXbsTz0FctH8GIv8AlprUn/AYR/jXDDxn4mzn+3Lz/vv/AOtVqD4heKbcgjVGk9pEVv6Vk6tKTu0elTy7MKNPkpVEl6f8A7mP4N6Yv+s1S8f6Koq5F8I/Dqf6yW9k/wC2oH9K5ay+LmswkC8s7W5XuVyjf4V1ml/FbQ7wql4k1i57uNyfmP8ACrjKgzkr0s3grttry/4Gpbh+GPhaLrZSSf78zGtCHwP4Zgxs0a24/vAt/M1sWd9a6hAJ7O4iniP8UbBhVit1CHRHjzxeJbtKb+9lCHRNKtseTptpHj+7Co/pV1URBhFVR7DFOoqrJHPKcpbu4UUUUyQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKhu7lbOynuX+7DGzn8BmgaTbsjxz4seIWvdXTRYH/0e0w0uD96Q/4D+ded1LeXcl/fXF5KxaSeRpGJ9zmohXmTlzSbPv8ACUFQoxprp+YU4U2nCoOpBS0lLSLQtLSUtIpBS0lLSLQUtJS0FIXvRR3opFoKWkpaRaCiiikUgooooAKKKKACuj8BymHxtphB+9IUP4qRXOVv+ClLeNNJA/57g/pV0/jRzY1J4aon/K/yPoWiiivZPywz9b0iDXNGudOuB8kyYDf3W7EfQ181X9jPpuoXFlcrtmgco49x3r6lryn4t+G+IvEFsnTEVyAO38Lf0/KufEQuuZdD3Mkxfs6vsZbS29f+CeUUtJS1wn1otFFFIsWlpKWgaCiiikULS0lLQUHeijvRSKQUtJS0DClpKWkUgo7UUdqRQUtJS0FIKKKKRSCiiigAooooAKKKKACiirOnWM2p6jb2NuuZZ3CL7e/4U1qKUlFOT2R6n8JdHMOnXWrSLhrhvKjJ/ur1/X+VekVU0ywh0vTLexgGI4Iwg98d6t169KHJBRPzDH4p4rETrd3p6dAooorQ4wr5y8Xaf/ZnizUrYLhBMXT/AHW5H86+ja8Y+Ldl5PiO1uwOLiDDH3U/4GubFRvC/Y97h6ry4lw/mX5a/wCZ5/RRRXnn2wUtJS0AFLSUtIaEpaSr2laTe61fpZWEBlmb8lHqT2FNJvRBKUYJyk7JFaCCW5njggjaWaQ7URBksa9i8FfD2LSBHqOqqs1/1SPqsP8Ai3vWt4S8FWXhiASnbPqDjEk5HT2X0FdTXdRw6j70tz43Nc7lWvRw+ker6v8A4AUUUV1nzgUjMFUsxAAGST2pa8q+KXjF42bw/p8u0kZu5FPOD0T/ABqJzUI3Z04TCzxNVU4/8MR+M/ifIZZNO8PyBVX5ZLzufZP8a8vklknlaWZ2kkY5Z3OST9ajpRXnznKbuz7jC4SlhoctNfPqx1FHeiszsQtLSUtItGno2vajoF4LjT7hozn5kPKP7EV7n4S8W2vimwLoBFdxYE0BPT3HqDXz1WnoGsz6BrVvqEBP7tsSID99O61rRrOD8jzcyyyGLpuUVaa2f6M7v4seIDJcQ6FA/wAkYEtxg9T/AAr/AF/KvM6t6rfvqurXd/JndPKXwew7D8qqVnVnzybO3L8MsNh40l8/XqFLSUtZnegooopFBRRRQAV6F8Ktda11aTSJX/c3QLxg9pB1/Mfyrz2r2j3jafrVldqcGGdG/DPNaU58k0zkx+GWJw86T6r8eh9LUUgORkdDS17B+XBRRRQAVyfxJiEvga+yPuFH/JhXWVieLtOuNW8LX1haIGnmQKgJwM5HU1FRXg0dODmoYiEnsmvzPnRVLMqqCzMcAAZJNem+D/hm83l3+voUj+8lp3b/AH/T6V1PhLwDYeHVW5n23Wo4/wBaw+WP2Uf1612Fc1LDW1me7mWeud6eG0Xf/IZFFHDEsUSKkaDCqowAPan0UV2HzO4UUUUAFFFFABRRRQAUUUUAFFFFABXk/wAZJwZ9Jt88gPJj8hXrFcfr/gODxL4gW/1G8kFvHGI44Ihg+py3v7VlWi5Rsj0MsrU6GIVWo9Fc8JijkmkWOJGkkY8KgyT+Fdvofww1nU9st9jT7c8/OMyEf7vb8a9c0nw9pOhx7NPsYoT3cDLH6k81p1jDCreR6mJ4hqS92hG3m9/8vzOf0DwZo3h4BrW2ElxjmeX5n/D0/CugoorqUVFWR8/VrVKsueo7sKKKKZmFFFFABRRRQAUUUUAFeO/FzRlttUtdWiXC3Q8uXH99eh/L+VexVxHxUt1m8FySEfNDOjg+nOP61lWjeDPRyms6WLhbrp954aKdTRTq8w/QIiilFIOw7npXZeHPh1q2tFJ7lTY2Z53yD52H+yv9TRGEpO0RVsTSw8OerKyOUtbW4vblLa1heaZzhUQZJr1nwj8NYrFo7/Wwk1yPmS3HKRn39T+lddoPhnS/Dtv5VhbgOR88z8u/1P8AStiu6lhlHWWrPksxz+pXTp4f3Y9+r/yADAwKKKK6j5wK81+MqsdD01gDtFycn0+U16VWD4x0H/hIvDV1Yrjz8eZCT/fHI/Pp+NRUi5QaR14GrGliITlsmfOAp4pJIpIZnilQpIjFWVhyCOopa8w/QIjhSikFKKlm0R1LSUtQzeIUUUUjQ9O+EViGfU751yoCQrn8z/Siur+HemnTfB1ruXElwTO3/Aun6Yor1qEeWmkfm2b1/bY2pJbXt92h1dFFFbHmBRRRQAUUUUAUdY1KPR9Hu9Qm+5bxl8ep7D86+aL+/uNTv5766cvPO5dif5fQV7f8VZWi8DThT/rJo0b6ZrwgVxYmTvY+qyGjFUpVerdvuHClpB0pa5T6JDhS0gpaRqiR5ZJAgd2YIu1dxztHoPam0CipZrEWiiikbIKO1FHakM+ifCE7XHhDSpGOSbdVJ+nH9K26x/Cts1p4U0uFhhlt1JH1Gf61sV7UPhR+U4pp158u13+Z5T8YNCytrrkKfd/cT4H/AHyf5j8q8nr6h1fTINY0m50+4GY50KE+h7H8DXzRqWnz6VqVxYXK4mgco3v6H8RzXJiIWlzdz6bJMV7Sj7J7x/Iq0tJS1zHthRRRQMWiiigYtFFFIYClpBS0DCiiigYtFFFAwpaSlpDCiiigYtJS0lIoWiiigYUUUUDFooopDFNFBooGFFFFIoKWkpaAEpaSloGFFFFBQUUUUhhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAHTeANO/tHxlZKVzHATO3/Aen64r36vL/hDpuItQ1Nl+8RAh9hyf6V6hXp4WNqd+58BxDX9rjHFbRVv1CiijOBk10nhBXD+LPiRp+gs9pYhb2/HBUH5Iz/tH19hXNePPiM8ry6Roc22IZWe6Q8t6qp9PevMO9ctWvbSJ9Fl2Tc6VTEbdv8AM1dZ8Rar4gnMuo3byDPyxA4RfotZfakpe1cbbbuz6eEIwioxVkLRRRSNELRRRQUhaKKKQ0XNP1O+0m4FxYXUtvIO6NgH6joa9O8M/FWOZktdeRYnPAuox8p/3h2+oryairhVlDY5cVgKGKjaote/U+pIpY54llidZI3GVZTkEexp9eAeEvG194ZnWJi1xp7H54Cfu+6+h9q9z0zU7TWLCK9sZhLBIMgjqPYjsa9ClWVReZ8XmGW1cHLXWL2f9dS5RRRWp5oUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFc747nNv4I1Z1OG8gqPxwK6KuY+ISF/Amq47RBvyIqZ/CzfC2deF+6/M+dxThSUoryz9BQU4U2nCgpBS0lLSLQtLSUtIpBS0lLSLQUtJS0FIXvRR3opFoKWkpaRaCiiikUgooooAKKKKACus+G9t9o8bWZxxCryH/vnH9a5OvS/hDYF73UdQYcRosKn3PJ/kK1oxvUSPPzaqqWCqS8rffoes0UUV65+ZhVe+s4dRsZ7O5QPDMhRwfQ1YooGm07o+Y9d0ifQtaudNuAd0L4Vv76nofxFZ9ezfFfw59u0tNat0zPaDbLgctGe/4H+ZrxmvNqw5JWPvMBiliaCn12fqLRRRWR3i0tJS0DQUUUUihaWkpaCg70Ud6KRSClpKWgYUtFFIpBR2oo7UigpaSloKQUUUUikFFFFABRRRQAUUUUAFesfCzw0YYW166TDygpbAjovdvx6VxXg3wvJ4m1hY2DLZQkNcP7f3R7mvfoYo4IUiiQJGihVUDgAdBXZhaV3zs+X4hzFQh9VpvV7+S7fP8vUfRRRXoHxYUUUUAFecfGC036NYXYHMU5Qn/eH/ANavR65H4l2/n+B7tsZMTpIP++gP61nWV6bO/LKnJjKb87ffoeDUUUV5R+ihS0lLQAUtJXb+Dvh9da6Y73UA9vp2cgdHm+noPenCDm7IxxGJpYem6lV2Ri+GfCmoeJ7vZbL5dsh/e3DD5V9h6n2r3PQPDun+HLEW1jFgn/WSt9+Q+pP9KvWVlbadaR2tpCkMEYwqIMAVYr0qVFU9ep8PmWa1cY+VaQ7f5hRRRWx5IUUUUAUNb1OPRtEvNRk+7BEXA9T2H54r5luLmW8upbmdy80zl3Y9yTk17X8XLsweEUgU4+0XCqfoMn+leH1xYmV5WPq8ioqNF1Orf4IWlpKWuY99Du9FHeikWhaWkpaRaFooopFod2oo7UUi0FLSUtI0QUUUUigooooAKUcEH3pKfGpeVEAyWYAfnQDPpmxYvp9s56tEpP5CrFR28flW0Uf9xAv5CpK9tbH5LJpydgooopkhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABXEfFW4WLwY8ZPzSzooH45/pXb1z/AIm8KweKfskV3cyx21u5do4sAucY69qiom4tI6sFUhTxEJ1Nk7nzwiNI4RFZ3Y4CqMk/hXZaH8Ndc1bbJcoLC3PO6YfOR7L/AI16/pHhrR9DQLp9jFE3eQjc5/4Eea1q54YVfaZ7eJ4hm9MPG3m/8v8AhzmdA8CaJoG2SOD7RdD/AJbzjcR9B0FdNTXdIkLyOqKOrMcAVzWo+P8Aw5ppKvfieQdUtxv/AF6frXR7lNdjxLYnGTvZzf3nT0V5rdfF+zUkWulzyejSOF/Tms5/jBe5/d6Rbgf7Up/wrN4mkup2wyLHyV+S3zX+Z63RXkafGC+z8+k25H+zK3+FaFt8YLViBdaVMnq0cgb9KFiaT6jnkOPir8l/mv8AM9MorltO+IfhvUSFF79nc/w3C7P16frXTRSxzxiSKRJEPRkbIP41rGcZbM82thq1B2qxa9UedfEH4fyatM2r6PGpuyP38HTzfcf7X868iuLae0mMNzDJDKvVJFKkfnX1LVa706yv02XlpBcL6Sxhv51jUw6k7rQ9XA51PDwVOouZL7z5gFKK98vPhz4YvMn+z/IY94HK/p0rnrz4P2bZNjqk8R7LKgYfnxXNLDTWx7lHPsJL4rx9V/lc8mpa6zWvh1r2jo0yxLeQLyXt+SB7r1rk++O/pXPOMouzR7eHxFKvHmpSTQVe0fTn1bWbSwjHM8oU+w7n8s1Rr0r4S6L5t7dazKvywjyYSf7x+8fyx+dOlDnmok5hilhcNOr1S09eh6tFEkEEcMY2pGoVR6AcCipKK9g/L276hRRRQAUUUUAFFFFAHJ/Ei1N14F1DAyYgsoH0Ir59FfU97aR39jPaTDMc0Zjb6EYr5o1rR7nQdWn066Qh4m+U9nXsw9iK48THVSPqMgrJwlSe97lIdKWkFLXIfSRHClpBS0jVDhRQKKk2iLRRRSNUFaOg6Y+sa7Z2CAnzZBu9lHLH8qzq9Z+FXh0wW0uuXCYeYeXbgjondvxPH4VpShzzSODMsWsJhpVOuy9f61PSkUIioowqjAHtS0UV65+ZBXmPxX8Lm5tV1+0jzLAAlyAPvJ2b8P5V6dTJI0mieKRA8bgqykcEHqKmcFONmdGFxMsPVVSPT8j5Upa6bxx4Vk8Ma0yRqTYTkvbv6Dup9x/KuZrzJJxdmfeUasasFUhswooopGotFFFAxaKKKQwFLSCloGFFFFAxaKKKBhS0lLSGFFFFAxaSlpKRQtFFFAwooooGLRRRSGKaKDRQMKKKKRQUtJS0AJS0lLQMKKKKCgooopDCiiigAooooAKKKKACiiigAooooAKO1Fafh7TTq3iGxsQMrLKN/wDujk/oKaV3ZE1JqnBzlstT3LwVpn9leErC3ZcSNH5sn+83P+A/Ct+kUBVCqMADAFLXtRXKkkflVaq6tSVSW7dwrzH4n+Mms4zoOnS7Z5F/0mRTyin+Ee57+1dv4m1yLw9oFzqMmCyLiJf7zngD86+bbm5mvLqW5uHMk0zl3Y9yawxFTlXKj18mwSqz9tNaLb1/4BHRRRXCfWi0vakpe1IoWiiigpC0UUUFIWiiikNC0UUUikFdH4Q8V3PhfUw4LSWUpAuIc9R/eHuK5ylpxk4u6Iq0YVoOnUV0z6gtLqC+tIrq2kEkMqh0cdCDU1eRfCzxM1vdtoN0/wC5mJa2JP3W7r+PWvXa9SlUU43Pz3H4OWErum9unmgooorQ4goqrf6jZ6ZbNcX1zHBCP4pGx/8Arrz/AFj4tWsTNHpFm1ww6TTfKv4Dqf0qJ1Iw+JnXhcBiMU/3Ub+fT7z0qkJAGSQPrXgV/wCP/Et+TnUGgQ/w26hP161hTajfXDbpr25kJ/vSsf61zPGR6I9ylwxWa/eTS9Nf8j6Y82POPMT/AL6p9fL/AJ8wORNLn/fNWYNY1O1YGDUbuMj+7M3+NJYxdUay4Wlb3av4f8E+l6K8GsPiN4lsSN16tyg/hnQNn8etdfpXxbtZSqarYvAT1lhO9fy6j9a1jiqct9DzsRw/jaSvFKS8v8j0qiqOm6xp+sQedp93FcJ32NyPqOoq9W6aeqPGlCUHyyVmFFFFMkKzfEFoL7w7qNsRnzLdwPrjitKkIDKQRkEYNJq6sVCTjJSXQ+URkDB6jrThV3WrI6drt/ZsMGG4dfwzxVIV5bVj9EhJSSkuoU4U2nCkaIKWkpaRaFpaSlpFIKWkpaRaClpKWgpC96KO9FItBS0lLSLQUUUUikFFFFABRRRQAV738PtKOleEbUOuJrjM7/8AAun6Yrxvwxo7a74itLEA+Wz7pT6IOT/h+NfRaqqIqKAFUYAHYV24OGrmfKcT4q0Y4dddX+g6iiiu8+OCiiigBksSTwvFKoeN1Ksp6EHqK+cfFegv4c8Q3FgQfJzvgY/xIen5dPwr6Rrh/ib4dGr+HjfQpm7sQZBgcsn8Q/r+FYV4c0b9j1snxfsK/LL4Zaf5HhlFAorzz7YWlpKWgaCiiikULS0lLQUHeijvRSKQUtJS0DClpKWkUgo7UUdqRQUtJS0FIKKKKRSCiiigAooooAK0NF0a717U4rCyTMj8sx6Ivdj7UzStKvNa1COxsYjJM5/BR3JPYV714W8L2nhjThBDiS4fBnnI5c/0A9K3o0XUfkeRmuawwVO0dZvZfq/61LWg6HaeHtKisLReF5dyOZG7k1p0UV6iSSsj89qVJVJOc3dsKKKKZAUUUUAFYnjCH7R4P1aP/p2Zh+Az/StuqWsR+bot9H/et3H/AI6aUldNGtCXLVjLs0fMvaigfdFFeOfp4U+ON5pViiRnkc4VVGST6AVc0jRr/Xb5bTT4GlkP3j/Cg9WPYV7d4T8DWHhqITNtudQI+adhwvso7D9a1pUZVH5Hm4/M6WDjrrLov8+xzng34aLbmPUNeQPKPmjtOqr7t6n2r0wAKAAAAOABS0V6MKcYKyPh8XjKuKnz1X/kgoooqzlCiiigAooooA81+Mqk6HpzDoLk5/75NeN17v8AFW0+0eCpZQObeZJPwzg/zrwiuDEL3z7HJJJ4VLs2LS0lLWB7KHd6KO9FItC0tJS0i0LRRRSLQ7tRR2opFoKWkpaRogooopFBRRRQAVs+FLE6j4q022xlTOrN/uryf5VjV6P8JNKM2pXequvyQJ5MZ/2m5P6fzrSlHmmkcWZYhUMLOp5fi9Eeu0UUV7B+YBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRVPUtUsdIs2u7+5jghX+Jz19gO5ryzxD8W7mdng0KAQR9PtEwy5+i9B+NROpGG52YXA18S/wB2tO/Q9Zubu2s4jLdTxQxjq0jBR+tctf8AxL8M2JKrePcsO1vGWH5nArw291G91OYzX11NcSE9ZHJ//VVWuWWKf2Ue/Q4fppXqyb9ND125+MdopIttInf0MkoX9MGqD/GO8J+TSIAP9qU15lS1m69Tud8cnwS+x+L/AMz06P4x3Wf3ujxEf7MpH9K07T4waZIQLvTrqH1KMH/wrx6gUliKi6hLJcFJfBb5s+iNM8b+HdVKrb6lEsjdI5vkb9a6AEMAQQQehFfLGB3rb0bxZrWhOPsV6/lDrDId6H8D0/Cto4r+ZHnYjh1WvQn8n/n/AMA+jaK4Lw58T9N1RkttTUWN03AYnMbH69vxrvAQyhlIIIyCO9dUZxmrxPnsRha2Hly1Y2FoooqjnCiio554raCSeeRY4o1LO7HAAFA0m3ZDyQASSABySa4HxN8TrLTGe10lVvLocGTP7tD9f4vwrkfGfj+41ySSx013g00HBYcNN9fQe1cP2rhq4rpA+sy3IFZVMV/4D/n/AJGpq3iLVtckL6heySLnIjB2oPoo4rMpKWuNtt3Z9VTpwpx5YKy8goooqTUKKKKACtDTNc1PRpRJp97LAc8qDlT9R0rPopptO6JnCM48s1dHrXh34qwTlLfXIhA54FxGPkP1HUV6NDNFcQrNDIkkbjKuhyCPrXy/XQeGfF+o+GbgeQ5mtGP7y2c/KfcehrrpYprSZ81mHDsJpzwuj7dH6dvy9D6EorL0LX7DxDp63djJkdJIz96M+hFald6aauj42pTlTk4TVmgrgvHHgKDVoJNR0yJY9QQbmRRgTD/4r3rvaKU4KaszXC4qrhaiqUnZ/n6ny/HDLNOlvHGxmdwipjndnGK+i/DmjpoWg2mnpjdGmZG/vOeSfzrFTwRbJ47OvKFEGzzBF6TdM/THP1rr658PRdNts9jOs1jjIwhT2td+vb5BRRRXUfPhRRRQAUUUUAFFFFABXO+LPCFl4qsfLm/dXcY/c3CjlfY+o9q6Kik0mrM0pVZ0pqcHZo+Zta0HUPD1+1nqEJRv4HH3ZB6qazq+mtZ0Sw16waz1CASRn7p6Mh9QexrwzxZ4Kv8AwvcFzmewZsR3Cjp7N6GuCrRcNVsfY5bmsMT7k9J/n6f5HNClpBS1znuRHCigUVJtEWiir2kaTd63qUVjZR7pZDyeyDux9hSSbdkXKcYRcpOyRo+EfDM3ibWUgwVtIsPcSDsvoPc19AwQRW0EcEKBIo1Coo6ADpWb4d0C18OaTHZWwyR80khHMjdya1q9ShS9nHXc/O83zJ42t7vwLb/MKKKK3PJCiiigDK8RaDa+I9Hm0+6GNwzHIBzG/ZhXznqul3Wi6nPp94myaFsH0YdiPY19Q1xfxB8HjxHpv2q0Qf2lbKTH/wBNF7of6VhXpcyutz2Mpx/1efs5v3X+DPBqKUqVYqylWU4IPUGkrgPsULRRRQMWiiikMBS0gpaBhRRRQMWiiigYUtJS0hhRRRQMWkpaSkULRRRQMKKKKBi0UUUhimig0UDCiiikUFLSUtACUtJS0DCiiigoKKKKQwooooAKKKKACiiigAooooAKKKKACvRfhLpfn6vd6m6/Lbx+Wh/2m6/oP1rzqvevh5pf9meELXcuJbnM78c89P0xXRho81S/Y8XP8R7HBuK3lp/mdVRRTXYRozscKoyT7V6h+enjnxe1s3Gq22jxN+7tl82UDu7dPyH8682q/reoNquuX1+xz58zMPpnj9MVQrzKkuaTZ9/g6CoUI0+359RaKKKg6kLS9qSl7UihaKKKCkLRRRQUhaKKKQ0LRRRSKQUtJS0FEtvcSWtzFcQsVlicOjDsRyK+k9G1FNX0a0v48bZ4g5A7HuPzzXzPXtXwovTceFpbZjk207KPYHkf1rpwsrS5e58/xFQUsPGr1i/wZ3lcR4t+IlnoRezsAl3fjg8/JEf9o9z7VieOviGVaXSdElwR8s90h6eqof5mvLO+TyTySa0rYm3uwOPKsi50q2JWnRf5/wCRe1XV7/Wrs3OoXLzyHpk/KvsB0FUqSlrhbb1Z9hCMYJRirJBRRRSLCiiikMKKKKAJ7S8urC4W4s7iSCZejxtg16d4X+KQdktNfAUngXaDj/gQ7fUV5VRWlOrKD9048Zl9DGR5aq179UfUMciTRrJE6ujDKspyCKfXhHg7xvdeG51t7gtPprH5o85Mfuv+Fe4Wl5b39pFdWsqywSruR1PBFenSrRqLTc+BzLLKuBnaWsXs/wCupPRRRWp5p4j8WdFay8RR6mifuL1AGI6CRRg/mMH864CvpbxJoNv4k0SfTp/lLDdHJjlHHQ186anpl3o+ozWF7GY54jgjsR2I9Qa4K8OWV+jPscnxirUVTfxR/IqU4U2nCsD2UFLSUtItC0tJS0ikFLSUtItBS0lLQUhe9FHeikWgpaSlpFoKKKKRSCiiigAoord8JeH5PEevQ2mCLdP3lw47IO31PSqinJ2RnVqwpU3Um7JHo3wt8Pmx0qTVp0xNecR5HSMf4n+leg0yKNIYkijULGihVUdAB0p9evTgoRUUfmGMxUsVXlWl1/LoFFFFWcwUUUUAFNdFkRkcBlYEEHuKdRQB80+I9KOieIr7TyDtikPl57oeV/Q1lV6Z8YNMEWpWGpouBMhhc+68j9DXmdeXUjyyaP0HA1/b4eFR7tfiLS0lLUHYgooopFC0tJS0FB3oo70UikFLSUtAwpaSlpFIKO1FHakUFLSUtBSCiiikUgooooAK1dB8P3/iK/FrYx5x/rJW+7GPUn+la/hPwLf+I3W4lDW2nA8zMOX9lH9a9q0rSLHRbFLOwgWKFfTqx9Se5rpo4dz1lseDmmd08KnTpaz/AAXr/kUvDfhix8M2HkWq7pW5lnYfM5/oPatuiivSSUVZHwtWrOrNzqO7YUUUUzMKKKKACiiigAqK6G6zmX1jYfpUtMlGYZABnKniga3PlsjBI9DXVeFPA2oeJZFmbNtp4PzTsOW9lHf69K6rwp8Md0ov9fT5dxaOzz78b/8ACvUY40ijWONFRFGFVRgAe1cNLDN6zPrsxz2NO9PDavv0Xp3/ACKGjaJYaDYraafAI4xyzdWc+pPc1o0UV2pJKyPkpzlOTlJ3bCiiimSFFFFABRRRQAUUUUAZfiTT/wC1fDeo2QGWlgYKPU4yP1FfMuCDg9Rwa+rq8D+Ivhl9C8QPdRRn7DesZIyBwrdWX+orlxMdFI+hyHEKMpUX11Rx1LSUtcZ9Sh3eijvRSLQtLSUtItC0UUUi0O7UUdqKRaClpKWkaIKKKKRQUUUUAKqs7BVBZmOAB3NfRHhLRRoHhu1siB523zJj6ueT+XT8K8v+Gfhw6rrX9pTpm1sjlcjhpOw/Dr+Ve1134SnZc7PjeJcapTWGg9tX69AooortPlQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACua8W+MrHwrafvMTXsg/dW6nk+59BT/ABh4rt/C2kmdtsl3LlbeHP3j6n2FfPt/f3WqX0t7eStLcStlmP8AIegrCtW5NFuezlmWfWH7Sp8P5/8AALOt69qPiC+N1qM5kb+BBwkY9FHas6kpa4G23dn18IRhFRirJC0d6KO9I0FpaSloGFAooFIpC0tJS0FISuy8IePr3w9Ilrdl7rTScFCctF7r7e1cd3opxk4u6M6+Hp4iDp1VdH09Y31tqVlFd2cyzQSjKup61ZrwbwL4vk8N6kILhydNuGxKv/PM/wB8f1r3hGV0V0YMrDIIPBFelSqqornwWZZfLB1eV6xez/rqGcDmvFviF4zbWbttLsJCNPhbDsp/1zD/ANlFdd8S/FB0nSxplpJi8u1O5geY4+5+p6fnXiw6Vz4mr9hHt5Blqa+tVF/h/wA/8gooorhPrQpaSloKQUUUUigooooAKKKKACiiigDS0PXL3w/qSXtk+GHDxk/LIvoa9/0HXLTxDpUd9aNw3DoTzG3cGvm6ul8FeJn8N62rux+xTkJcL6Ds31H8q6cPW5HZ7Hh51lccXT9pBe+vx8v8j3+ikVldFdSGVhkEdxS16Z+fhRRRQAUUUUAFRzTw26b5pUjQfxOwA/WuU8ceNovC1osMCrNqMy5jjPRB/eb/AA714jqesajrVw0+o3cs7k9GPyj6DoKwqV1B26nr4HKKmKjzyfLH8z6JHiTRC+watZ7s4x5y/wCNaMcscyB4nV0PRlOQa+WAB6CtbRfEOqaBcLNp908YB+aInKMPQislitdUejU4c9391PXzR9JkgAknAHJJrzXxL8U0tZ5LTQ4knZDta5k+5n/ZHf61T8UfEJNT8GW8dkfJu7wmO5QHmIDqPoe3tmvMaVfEPaBplGSRd6mKWzsl6dWdNN4+8TzSbzqrp7RooH8q0tL+KOu2Uii88q9h7hl2t+BFcRRXIqs073Po55dhJx5XTVvSx9GeHvEun+JbMz2Uh3pxJC/Doff29607i3hu7eS3uIllhkXa6OMgivnbw3rU2ga7bX0THaGCyrnh0PUGvoxHWSNXU5VgCD7V6NCr7SOu58RnGW/UaycH7r28vI8E8c+EG8Makr2+5tOuCTCx6oe6H+lcrX0f4n0WPX/D91YOAXZN0R/uuOQa+cWVkdkYYZTgj0NcmIp8ktNmfR5NjniqNp/FHf8ARiiikrV0Hw/f+Ir8WtjFnHMkrfdjHqT/AErBJt2R7MqkacXObskQaXpd5rOoR2NjEZJpD+Cj1J7CvefCnhW08L6d5UeJLqQZnnxyx9B6AelSeGvDFj4ZsfItV3zOP3s7D5nP9B7Vt16FCgoavc+IzfOJYt+ypaQX4/12Ciiiuk8IKKKKACiiigAooooA8o+Jngk5k1/TIs97uJR/4+B/P868pr6sIDAqwBBGCD3rxf4geAW0mWTVtKiLWDHdLCoyYD6j/Z/lXHXo/aifT5RmaaWHqvXo/wBP8jzyiiiuU+jFooopDAUtIKWgYUUUUDFooooGFLSUtIYUUUUDFpKWkpFC0UUUDCiiigYtFFFIYpooNFAwooopFBS0lLQAlLSUtAwooooKCiiikMKKKKACiiigAooooAKKKKACiiigC/ounNq+t2dgo/18oVvZe/6V9JRRrDEkaDCIoVR6AV5H8JdK8/VrrVHX5LZPLjP+03X9P516/XpYSFoc3c+G4kxPtMSqS2ivxf8AwLBWB411H+y/B+p3IbDmExp/vN8o/nW/Xm/xivjDoNlZKcG4n3Eeyj/69b1HaDZ42Bpe1xEIeZ4yOlFFFeYffIWiiigYtL2pKXtSKFooooKQtFFFBSFooopDQtFFFIpBS0lLQUFbOm+I7zSdFv8ATrM+Wb1lLyg8qoGCB9fWsajvQm1sTOnCouWauv8AIWiiipNgNLSGloGgooooKCiiikMKKKKACiiigArsvAXjB/D98LO7kJ02dsNn/lkx/iHt61xtFXCTg7owxOHp4mk6VRaM+o1YMoZSCpGQQeDS1518L/E5vrJtFu5Mz2y7oGJ5aP0/D+Vei161OanHmR+aYzCzwtaVGfT8V3CuZ8YeDrPxVZfNiG+iH7mcDp7N6iumoqpRUlZmNKrOlNTg7NHzBqukX2iag9lqEBimXp6MPUHuKp19La94d07xHYm1v4Q2PuSLw8Z9Qa8M8U+DNR8LXGZQZ7Jj+7uUHH0b0NcFWi4arY+xy/NKeJ9yWk/z9P8AI5ylpKWsD2ELS0lLSKQUtJS0i0FLSUtBSF70Ud6KRaClpKWkWgooopFIKKKKAHRxvLIscaF5HIVVUcknoK9+8FeGU8N6IsbgG8nw9w3v2X6D/GuR+GXhHldfv4/+vRGH/j/+FepV6GFo2XOz4riDM/ay+q03ot/N9vl+foFFFFdh8wFFFFABRRRQAUUUUAcT8U7IXPgySYDLW0ySA+gzg/zrwqvo/wAYwfafB+rR4z/ozMPw5/pXzgOlcOJXvXPr8gnfDyj2YtLSUtcx7yCiiikULS0lLQUHeijvRSKQUtJS0DFooopFIKO1FHakUFLSUtBSCiiuv8N/DzVdcKT3Cmysjz5ki/Ow/wBlf6mnGEpO0TKviaWHhz1ZWRy1ra3F7cpbWsLzTOcKiLkmvVPCvwwityl7ru2WUcraqcov+8e59uldnoXhrS/Dtv5VhbgOR88zcu/1P9K1676WFUdZas+OzHiCpWvTw/ux79X/AJCKqogRFCqowABgAUtFFdZ82FFFFABRRRQAUUUUAFFFUNU1rTtGg87ULuKBewY8t9B1NJtLVlQhKb5Yq7L9FeZar8XYELJpNg0vpLcHaP8Avkc/yrkb74h+Jr4n/T/s6n+GBAuPx61hLFU47ans0OH8ZVV5JR9f8ke90mRnGa+ap9Z1S5JM2pXchPXMzVX+13Oc/ap8/wDXVv8AGsvri7HoR4WnbWr+H/BPp6ivmmHWdUtiDDqV2hHTEzVs2XxB8TWRH/ExM6j+GdA2fx601jI9UZ1OF66XuTT+9f5nvtFeXaX8XQSqarp20d5bc5/8dP8AjXfaR4g0rXYt+n3kcpHVM4ZfqDzXRCrCezPGxWW4rC61Yad90adFFFaHCFFFFABRRRQAVQ1nSLPXdMlsL6PfDIOvdT2I9CKv0UNX0ZUZOLUouzR84+KfCd/4WvjFcKZLVz+5uFHyuPQ+h9qwRX1Hf2FrqdnJaXsCTQSDDI44rxbxj8OrrQTJe6cHudO6sOrw/X1HvXDVoOOsdj63Ls3jWtTraS/B/wDBOH70UlLXMe8haWkpaRaFooopFod2oo7UUi0FLSUtI0QUUUUigq1p2n3Gq6jBY2ibppm2qPT1J9hVWva/h34R/sWw/tG9jxf3K8KRzEnp9T3rWjTdSVjz8zx8cFQc38T2Xn/wDp9C0e30HR7fT7cfLGvzN3dj1J+taVFFeskkrI/NZzlUk5yd2wooqnqOq2Ok2xuL+6jgjHdz1+g7027bhGMpPlirsuUV5jrHxbiQtHo9kZT0E1x8o/BRz/KuJ1Dxx4j1IkS6lJEh/gg+QfpXPLFQjtqe1h+H8XVV52ivPf7j6AknhhH72aNP95gKqPrelRnD6laA+8y/4182yzTTktNNJIT1LuT/ADqPaPQflWLxj6I9SHC8be9V/D/gn0umtaXKcJqNox9pl/xq3HLHKMxyI49VYGvl3avoPyqeC6ubZg0FxNER0KSEULGPqgnwtG3uVfvX/BPp6ivBNM+IXiPTSoN79qjH8FwN369a77QvijpeoMsOpRmwmPG8ndGT9e341tDE05eR5OKyHGUFzJcy8v8ALc72imxyJLGskbq6MMqynII+tOroPFCiiigAooooAKgvbyDT7Ka7uXCQwoXdj2AqevLvi9r5itrfQoHw0376fB/hH3R+J5/ConLljc6cJh3iK0aa6/kec+JdfuPEmtzahOSEJ2wx54jTsP8AGsmkpa81tt3Z97ThGEVGKskFLSUtI0Fo70Ud6RQtLSUtAwoFFApFIWlpKWgpB3oo70UikFex/DfxSkvhu5tb6XDaam/cx6xdvy6flXjlTQ3M1usywyMizJ5cgB+8uc4P5CtKdR05XOPH4KOMo+zl33/ryLmu6vNrus3OozE5lb5FP8KjoPyrPoorNtvVnbThGEVGOyCiiipNApaSloKQUUUUigooooAKKKKACiiigAooooA9u+GeuHVPDn2SZ909ifLyTyU/hP8AT8K7avDvhjqJsvFyW5bEd3G0ZHuOR/I/nXuNerhp81NeR+dZ5hlh8ZLl2lr9+/4hRRRW55AUUUUAfM/iXVZNb8RXt/IxIkkwg9EHCj8qzB0p9zC1vdzQv96Nyp+oNMHSvKk7vU/R6UYxiox2Q4U6minVDOmItLSUtSzeItFFFSaoRvun6V9L6KWOh2Bf732ePP8A3yK+c9NspNR1S1soxl55VQfiea+l4o1hhSJfuooUfQV3YNbs+S4pqK1OHXV/kPr5y8XWq2fi7VYFGFFwzD/gXP8AWvo2uMl+H9nqHi281rU2E0UjqYrYfd4UDLevTpW9em5pJHkZPjaeEqTnU2t97ueb+E/A1/4lkWeQNbacD80xHL+yjv8AXpXtuk6RY6JYpZ2EAiiXr6sfUnuauIixoqRqqoowFUYAFOqqVGNNeZjmGZ1sZK0tI9F/n3CiiitTzQooooAKKKKACiiigAooooAKRlDqVZQykYII4IpaKAPJfGnwydGk1Lw/HuU/NJZjqPdP8Pyry5lZHZHUqynBVhgg19V1y/ifwJpPiUNK6fZr3HFxEOT/ALw71y1cPfWJ9BgM6dNKniNV36/PufPlFdLr/gXW/D7M8tubi1HS4gBYY9x1Fc1XHKLi7M+npVadWPNTd0ApaSlpGoUUUUDFooooGFLSUtIYUUUUDFpKWkpFC0UUUDCiiigYtFFFIYpooNFAwooopFBS0lLQAlLSUtAwooooKCiiikMKKKKACiiigAooooAKKKKACiitfwxpR1rxHY2OCUeQNJ7IOTTSbdkRUqRpwc5bJXPZ/Aekf2R4StI3XE048+T6tyP0xXTUgAUAAAADAApa9mMVFJI/K69aVarKrLdu4V438ZZidZ0yDPyrAz49y2P6V7JXjHxkjI1/TpP4WtiPyas6/wAB35Nb63H5/kecUUUV559ohaKKKBoWl7UlL2pFC0UUUFIWiiigpC0UUUhoWiiikUgpaSloKCjvRR3pDQtFFFIsDS0hpaBoKKKKCgooopDCiiigAooooAKKKKALukanNo+q22oQEh4HDY9R3H4ivpCzuor2zhuoW3RTIHU+xGa+Y69s+FupG98K/ZXbL2cpjH+6eR/UfhXZhJ2k4nzHE2FUqUa63jo/R/8AB/M7eiiivQPigqK4t4bu3e3uIklhkG10cZBFS0UDTad0eNeMvhpNpvmX+iK81oPme36vH9PUfrXnVfVVcB4y+G9vrHmX+khLe/6tH0Sb/A+9clXD9YH0uXZ1a1PEv5/5/wCZ4rS1Ld2lxYXUlrdwvDPGcMjjBFRVxM+oi01dBS0lLSNEFLSUtBSF70Ud6KRaClpKWkWgooopFIK6/wACeEH8R6h9puVI023b94f+erf3R/Ws3wt4ZuvE+qC3iylshBnmxwg9B7mvftP0+20uxhsrOIRwRLtVR/M+9dWHoc75pbHz+d5ssND2NJ++/wAF/n2+8nRFjRURQqKMKoGABTqKK9I+DCiiigAooooAKKKKACiiigDP10bvD+ojjm2k6/7pr5kHSvpfxHIIvDOqSHotrJ/6Ca+aB0rixW6PquHv4c/VC0tJS1yn0aCiiikULS0lLQUHeijvRSKQUtJS0DClpKWkUgo7Uf1rptD8B65rm10t/s1sf+W042gj2HU04xcnZEVq9OjHnqSSXmczXRaD4L1nxAVe3t/Jtj1uJhtX8O5/CvUtA+G+jaOVmuEN9dDnfMPlB9l6fnmuwACgBQABwAO1ddPCdZnzeN4kivdwyv5v9F/n9xyfh34faRoW2aRPtl4OfNmHCn/ZXoK62iiuyMYxVony1fEVcRPnqyuwoooqjEKKKKACiiigAooooAKjnnitoXmnkWOJBlnc4AFQalqVppNhLe3soigjGST39h6mvC/FvjS98T3Jjy0Gnqf3cAPX3b1P8qxq1lTXmenl2WVcbLTSK3f+XmdZ4n+KZ3PaaAo44N26/wDoI/qa8zuru4vrhri7nknmY5LyNk1DQK86dSU3dn3ODwNDCR5aS+fVhS0lLWZ3IKKKKRSCiiigAqSC4mtZ1nt5XilQ5V0bBFR0UwaTVmeo+FPieS0dlr5HPyrdgY/77H9RXqKOsiK6MGRhlWU5BFfLtd14D8cSaNcJpuoyFtOkOEdjnyD/APE/yrsoYl/DM+VzbIYyi62FVn1X+X+R7VRSKwZQykFSMgjvS13nxoUUUUAFFFFABSEAggjIPalooA808ZfDOO88zUdCRYrj70lr0WT3X0Pt0rySaGW3meGeNo5UO10cYKn3FfU1cv4s8EWHieEyEC3v1HyXCjr7MO4rlq4dPWJ9Bl2dSpWp4jWPfqv8z5+FLV/WNGvtB1B7K/hMcq8qR91x6g9xVCuJprRn10JRnFSi7pi0UUVJqh3aijtRSLQUtJS0jRBRRXaeBPBT+ILlb69QrpkTdDx5zDsPb1NVCDm7IyxOJp4ak6tR2SNT4ceDDdyx65qMX7hDm2iYffP94+w7V65TURY41RFCooAVQMAD0p1erSpqnGyPzfH46pjazqT+S7IKKRiFUsxAAGST2ryHxz8QpL55NL0aUpajKy3CnBl9Qvovv3oqVFTV2LBYGrjKnJT+b7G/4t+JVtpTPZaRsurwcNKeY4z/AOzGvJNQ1O91a7a5v7mSeY93PT2A7CqlLXm1KspvU+8wOXUMHG0Fr36hR3oo71keihaKKKCgooopDCiiigDo/DPjLUvDUyrE5nsifntnPH/AfQ17doeu2PiDT1vLGXcvR0P3kPoRXzdWr4f1+88Oaml5aNkdJYiflkX0P+NdNHEODs9jw81yani4upTVp/n6/wCZ9H0VQ0bWLTXdLiv7N90cg5B6oe4PuKv16Saauj4GcJQk4yVmgooopkhXzX4t1Q6z4q1C83ZQylI/9xeB/KvoPXrv7BoGoXWcGK3dgffHFfMIJPJ6nk1y4mWyPosgpK86j9BaWkpa4z6YKWkpaChaO9FHekULS0lLQMKBRQKRSFpaSloKQd6KO9FIpBS0lLQNCmig0UikFFFFIoKWkpaCkFFFFIoKKKKACiiigAooooAKKKKANLQLk2niLTrgHGy5Q/hnmvpKvmC3bbdQsOokU/rX08pyoPqK78G9Gj47imPv0peT/QWiiiu0+UCiiigDwH4jaDLo/ime4EZ+yXrebE4HGT95frn+dckK+nNY0ax13TpLG/hEkT9PVT2IPY14T4s8FX/hacuc3Fgzfu7hR09m9DXBWpOL5lsfYZVmUK0VSm7SX4/8E5sU6minVzM+giLS0lLUs3iLRRWt4c0C58R6vHY24IX70smOI07n/Ckk27IqpUjTg5zdkjs/hT4fM15Lrk6fu4cx2+R1Y/eP4Dj8a9bqtp9hb6ZYQWVqgSGFAqj/AD3qzXr0qfs48p+aZjjXjMQ6r26egUVHNPFbxGWeVIo16u7AAfia5LVfiZ4c00skdw95KP4bdcj8zxVSlGO7Oelh6tZ2pxbOxorx7UPjBfy5XTtOhgXs0zF2/IYFcve+O/E1+T5mqzRqf4YcRj9KxeJgtj1KWRYqesrR/ryPoaSWOJd0kioPVmxWfP4i0a1/12q2afWZa+bZru5uW3T3M0pPXfITUOB6Cs3in0R3Q4dj9up9yPoiTx54Xj4Os2x/3ST/ACqE/EbwqP8AmKKfpG3+FfP1FT9an2N1w/h+sn+H+R9AD4jeFif+Qov4xt/hU6ePfC8nTWbdf97I/pXzzRR9an2G+HsN0k/w/wAj6Th8T6FcECLV7Ns/9NhWjFdW8/8AqZ4pP9xwf5V8tYB6gU+OSSM5jkdP91iP5U1i31RlLhyH2aj+4+p6K+bLTxPrtjj7Pq12gHbzCR+Rrfsvij4ktcCaS3ulH/PWLB/NcVosVHqjjqcPYhfBJP8AA90ory+w+MMLYXUNKdPVoHDD8jiup074geG9SIVNQWGQ/wAE4KH9eK1jWhLZnnVssxdLWUH8tfyOnopkU0c8YkikSRD0ZGBB/Gn1ocOwYzwa5jWvAOga2WkktBbzn/ltb/IfxHQ/lXT0UnFSVmaUq1SlLmptpnjWq/CLU7cs+mXcV2nZJPkf/D+VcZqHh/WNJYi+024hA/iKEr+Y4r6YpCoZSrAEHqCK55YaL20PZoZ9iIaVEpfg/wCvkfKuQaWvpDUPCWgank3WlWzMf41Xa35jFcze/CPQ58m1uLq1PpuDj9ef1rF4aa2PUpZ/hpfGmvx/r7jxaivSLz4PaimTZ6nbygdBKhUn8s1g3fw58UWmT/Z4nA7wSBv0rN0prdHoU8ywlT4ai/L8zlaWrl1pGpWJIutPuocd3iIH51TzWTVjtjJSV4u4UUUUFC0lLSUihaKKKBhRRRQMWiiikMU0UGigYUUUUigpaSloASlpKWgYUUUUFBRRRSGFFFFABRRRQAUUUUAFFFFABXqfwj0j5bzWJF6nyIiR+LH+VeWqjSOqICWYhVA7k19H+HdKXRfD9nYKBuijG8+rHk/rXVhYc079j5/iPFeywvslvP8AJb/oalFFFekfBhXmXxksDJpen36j/UymNj7MOP5V6bWL4t0j+3PDF9YgZkeMtH/vjkfyqKkeaLR1YGt7HEQm9rnzXRQQQSCCCOCD2orzD75C0UUUDQtL2pKXtSKFooooKQtFFFBSFooopDQtFFFIpBS0lLQUFHeijvSGhaKKKRYGlpDS0DQUUUUFBRRRSGFFFFABRRRQAUUUUALXo/whuSuq6ja54khWTHuDj+tecV3PwoJ/4SyUDvbN/MVrQdqiPNziKlgaifY9qooor1z81CiiigAooooA57xT4R0/xPZlZlEd2g/dXCj5lPofUe1eDavpF5oepS2F9HsmToezDsR6ivpqua8Z+FIfE+klFCpfQgtby+/90+xrnrUVNXW57WVZpLDSVOo/cf4f8A+faKfNDLbXEkE8bRyxsVdGHKkdRTK84+3TuroKWkpaC0L3oo70Ui0FLSUtItBWr4e8P3niPU0s7RcDrLKR8sa+p/wpNB0C+8RaitnZJ7ySn7sa+p/wr3vw/oFn4d0xLKzX3kkI+aRvU1vQoOo7vY8fNs2jg4ckNZv8PNkmiaLZ6BpkdjZJhF5Zj9527k+9aNRXFzBaQmW5mjhjHV5GCj8zXOXPxD8L2rlTqiSMOvlKW/pXpXjFW2PhFTr4iTkk5N79TqKK5qz8feGb1wkeqRIx6CUFP510UcqTRrJE6ujDIZTkH8aaknsyalGpS0qRa9UPoorzDxl8SXt7iTTdCZdyErLdYzg9wn+NTUqRgrs2weCq4up7Okv8kelTXMFsu6eaOJfV3C/zqquuaS77F1K0LennL/jXzhdXl1fStLd3Es8jHJaRy1QbV9B+Vcjxjvoj6WHC8eX36mvkv+CfUaurqGRgynoQcinV836T4i1bRJhJYXssYHWMncjfUHivYfB3jq28Sr9lnVbfUVXJjz8sg9V/wraliYzdnozyswyOvhI+0i+aPft6o6+iiiug8Q5n4gXQtfA+ptnl0EY/4EQP5V89V7J8X78RaFZ2Ib5rifeR7KP8TXjdcGJd52Psshp8uF5u7f8AkLS0lLXOe2gooopFC0tJS0FB3oo70AEkADJPQCkUgpa6LSfAviDWNrRWLQQn/lrcfIPy6mu90f4S2Fvtk1W7kunHWOP5E/Pqf0rWNGctkefiM1wuH0lK77LU8mt7a4vJlhtoJJpT0SNSxrttF+Fmr322TUZEsITyVPzSH8Og/GvXtP0qw0qERWFpDbp6RrjP1PerldMMJFfFqeBiuI6s9KEeVd3q/wDL8zm9E8DaFoe14bUTXA/5bT/M34dh+FdJRRXVGKirI8CrWqVpc1STb8wooopmQUUUUAFFFFABRRRQAUUUUAFMllSGJ5ZXCRopZmJ4AHen15r8VfEht7WPQ7Z8STjfcEHonZfx/kKipNQjzM6sHhZYqtGlHr+RxfjXxbL4m1MrEzLp0BIgj/vf7Z9z+lcvQKK8qUnJ3Z+j0KMKFNU6askLQKKBUG4UtJS0FIKKKKRSCiiigAooooAKKKKAPW/hh4qN3AdDvJMzQrutmY8sndfw/lXpNfMdhez6bfwXts22aBw6n6dvxr6P0jU4dY0m21CA/u50DY9D3H4GvSwtXmjyvdHwvEOAVCt7eC92X5/8H/Mu0UUV1HzoUUUUAFFFFABRRRQBieKPDNp4n0trWcBZl+aCYDmNv8PUV8+ajp9zpWoTWN5GUnhbaw9fcexr6erzz4peGxfaYNZt0/0m0GJcDl4//rf41zYilzLmW572SZg6NRUJv3ZbeT/4J43RRRXnn2qHdqKO1FItBS0ldn4K8DT+IplvLxWi0xD16GY+i+3vTjBzdkRXxNPDU3UquyQzwT4Km8SXIuroNFpkbfM3Qyn+6v8AU17hb28Npbx29vGscMahURRgAUW1tDaW0dvbxLFDGu1EUYAFS16lKkqa8z8+zLMqmOqXekVsv66hRRXPeM/EI8OeHprpSPtMn7uBT/fPf8OtaSkoq7OGlSlVqKnDdnGfEvxk299A06XGOLuRT/44P6/lXlopXd5JGkkYs7kszHqSeppK8qpNzldn6NgsJDCUlTh8/Ni96KO9FZnago70Ud6RSFooooKCiiikMKKKKACiiigDq/AnilvDmsCOdz/Z9yQsw/uHs/4d/aveFYMoZSCpGQR3r5cr2j4Y+Izqejtplw+bmyACknlo+35dPyrtwtXXkZ8pxHl6cfrdNarf9H+h3lFFFd58ccz8QZDF4E1Ug8mIL+ZFfOwr6G+IylvAeqY7ID/48K+ea4sT8R9XkP8Au8vX9ELS0lLXMe6FLSUtBQtHeijvSKFpaSloGFAooFIpC0tJS0FIO9FHeikUgpaSloGhTRQaKRSCiiikUFLSUtBSCiiikUFFFFABRRRQAUUUUAFFFFAE9qhkvIEHVpFH619OjgY9K+cPDdsbvxPpkA/iuUz9Acmvo+u/BrRs+N4pl+8px8n+n+QUUUV2nyoUUUUAFR3FvDdW7wXESyxSDayOMgipKKATad0eK+N/h4+irJqelBpLDOZIurQ+/uv8q4KvqV0WSNkdQyMCGUjgg189+NNAHh3xJNaxAi2kHmweynt+B4rgxFJR96Ox9jkmZSr/ALmq/eWz7r/M5+lpK0tF0S+17UFs7CEu55Zj91B6k1y2bdkfRucYRcpOyQzStKvNZ1COxsYjJNIfwUdyT2Fe+eGPDVr4Z0tbaDDzP800xHLt/h6CmeF/C1l4YsPJgHmXDjM05HLn+g9qwvF3xJstDMlnpwS8vxwxz+7iPue59hXdSpRormnufHZjj62Z1Pq+GXuL8fN+XY7DUdTsdJtWub+6jt4R/E5xn6eteZa/8XWJaDQrXjp9puB/Jf8AGvONV1jUNbuzdajdPPIemTwvsB0AqlUTxEnpHQ68JklKn71b3n+H/BL+p61qWszebqF7NcHsGb5R9B0FUaSlrnbb3PbjGMVaKshaSlpKRYUtJS0AFFFFAxaKKKBhRRRSGLRRRQMKWkpaBlqy1S/02QPZXs9u3/TNyP0rsNL+KuuWe1L1Ib6Md2Gx/wAxx+lcLRVRnKOzMK2EoV/4sE/67nuWk/E/QNR2pcvJYyntMMr/AN9CuwguYLqIS280csZ6NGwYH8q+XauafquoaVL5theTW7f9M2wD9R0NdEcU18SPExHDtKWtCVvJ6r/P8z6boryDRvi1e2+2LV7RblOhlh+V/wAuh/SvRNG8W6JrqgWV6nmnrDJ8rj8D1/CumFaE9mfP4rLMThtZx07rVf16m3RRRWpwBRRRQAhAYYIBHvWdd+HtGv8AP2rTLSUnuYhn8+taVFJpPcqM5Qd4uxx158MfDN1kpbS2xP8AzxlI/nmsC7+DsJybLVpF9FmjBH5ivUKKzdGm+h3U80xlPao/nr+Z4hd/CnxDb5MDWtyo/uvtJ/A1g3nhHxDY5M+kXQA/iRN4/MZr6NorN4WD2O+nxDiY/Gk/wPlqSOSFisqOjDqGUim19QXFna3a7bm2hmHpIgb+dYV54C8NXuS+lxIx/iiJQ/pWTwkujPQpcSUn/Eg16a/5Hz5RXsl58ItIlybS9urc9gxDj9awLz4Q6pHk2moW047B1KH+tZPD1F0PQpZ1gp/bt6o87orprz4f+JrPO7THlA7wsH/Qc1z9xaXNlIY7q3lgcdpEK/zrJxlHdHoUsRSq/wAOSfoyI0UUVJuFFFFIoKWkpaAEpaSloGFFFFBQUUUUhhRRRQAUUUUAFFFFABRRRQB1vw60f+1vFcMjrmCzHnPnpn+Efn/Kvd64r4ZaN/ZvhgXci4mvm8056hBwv9T+NdrXq4aHLD1PzvPMX9Yxkkto6L9fxCiiitzxwooooA+f/iNoJ0TxTK8aYtbzM0eOgJ+8Pz/nXI19C+PvDn/CReG5UiXN5bZmg9SR1X8R/SvnrkHBBBHUHtXn1ocsj7XKsV7egk946P8AQWiiisT1ELS9qSl7UihaKKKCkLRRRQUhaKKKQ0LRRRSKQUtJS0FBR3oo70hoWiiikWBpaQ0tA0FFFFBQUUUUhhRRRQAUUUUAFFFFAC16F8I4N+vX0/aO32/iWH+Fee17B8JLAw6JeXzDm4m2KfZR/iTW+GjeqjyM9qqngZ+dl+J6HRRRXqn50FFFFABRRRQAUUUUAeTfFbw2IpI9ftkwrkR3IA7/AMLf0/KvMa+m9W06LVtKurCcZjnjKH2PY/gea+abm2ks7ua2mGJIXKMPcHFefiYcsuZdT7XIcW61B0pbx/IipaSlrmPfQveijvRSLQVs+HPDd94l1AW1ou2NeZZmHyxj/H2q14U8IXvii7+QGGyQ/vbgjj6L6mvdNI0iy0TT47KxhEcSdfVj6k9zXRRoOer2PFzXOYYROnT1n+Xr/kQ6FoNj4e05bOyjwOryH70jepNZfjPxlbeFLFcKJr6YHyYc/wDjze3866WaVIIZJZDhI1LMfQCvmfxBrM2v65dajMxPmP8Au1P8KD7o/KuurP2cbRPmcuwrxtd1Kzulq/Nhq2ualr10bjUbp5mJ4XOEX2A6CqApopwrgbbd2fZ04xglGKsha3PD/irVPDdyslnOzQZ+e3c5Rx9Ox9xWJS0k2ndGk6cKseSaumeyeKvHcEvgeG60yQpPqGYgM/NFj7/4jp+NeOijcdoXJwOQM8CgU6tRzd2Z4DA08HBwh1d/8vuFooorI9FBU1rdT2V3FdW0hjmhYOjDsRUNFA2k1Zn0noWqJrWh2moIMedGGYejdx+daNcT8LJTJ4OCH/lncOo/Q/1rrNRvotN025vZjiOCMyN+A6V7FOXNBSZ+XYyh7LFTow6NpfoeKfFLVBf+LmtkbMdlGIv+BHk/0rianu7qS+vZ7uY5knkaRj7k5qCvNnLmk2fdYWiqNGNNdELS0ma0bDQNX1RgLLTbmYH+IRkD8zxUpN7G0pxgrydkZ9Fd5p3wn126w15Lb2anqC29vyHH6112m/CbRLXDXs1xeOOoJ2L+Q/xrWNCcuh59bOMJS+1d+Wv/AADxdFaRwiKzseiqMmuk0vwF4j1Ta0entBGf+Wlwdg/Lr+le6afommaUgWwsIIMd0QZ/PrV+t44VfaZ5VfiOb0owt6nmOlfCC3Ta+q6g8p7x242j8zzXb6V4X0XRlH2HT4Y3H/LQruc/iea16K3jShHZHi4jMMTiP4k3btsgooorQ4wooooAKKKKACiiigAooooAKKKKACiiigAooooAZLKkMLyyHCIpZj6AV82a5qkmta3d6hISfOkJUei9FH5V7d8QtQOn+C75lbDzAQL/AMCPP6ZrwGuHFy1UT63hvDpQnWfXT/MWkoorjPqULQKKBSKClpKWgpBRRRSKQUUUUAFFFFABRRRQAV6n8JNZJS70aVvu/v4QfTow/ka8sra8KamdI8Uafd7sIJQkn+63B/nWtGfJNM4M0wyxOFnT62uvVH0VRRRXrn5kFFFFABRRRQAUUUUAFMmiSeGSGVQ0cilWB7g8Gn0UAnY+Z9b01tH1y909v+WEpVT6r1B/LFUK734sWQt/FEN0owLm3BPuVOP5Yrgq8mpHlk0fpWCre3w8Kj6r/hx3ailjjeWRY40Z5HOFVRkk+gr1nwX8N1tTHqWuIHnHzR2p5VPdvU+1FOnKo7IMZjqODp89R+i6sxvBPw8k1Qx6lrEbR2Q+aOA8NN7n0X+dewxRRwxLFEipGgwqqMACnDjgUtelTpRpqyPg8dj6uMqc1TbouiCiiitDhCvEPihrJ1DxN9hRsw2K7MD++eW/oK9quJlt7aWZzhY0Ln6AZr5kvLp72+uLuQkvPI0hz7nNcuKlaKj3PouHcOp1pVX9lfi/+AQUtJS1559khe9FHeigtBR3oo70ikLRRRQUFFFFIYUUUUAFFFFABWx4X1ltB8Q2l8CfLVtko9UPB/x/Cseimm07oirTjVg4S2eh9RoyuiupBVhkEdxS1ynw81Y6r4Stw7Zmtf3D568dP0xXV17MJKUVJH5ZiKMqFWVKW6djnvHMfm+CNXXH/LuT+RBr5xFfTniKH7R4a1OLGd1tJ/6Ca+Y1+6PpXLifiR9HkD/dTXn+gtLSUtcp74UtJS0FC0d6KO9IoWlpKWgYUCigUikLS0lLQUg70Ud6KRSClpKWgaFNFBopFIKKKKRQUtJS0FIKKKKRQUUUUAFFFFABRRRQAUUUUAdl8MrL7X4yilIyttE0p+vQfzr3KvN/hHppi0291J15nkESH/ZXr+pr0ivUw0bU/U/Pc/r+1xskvs2X9fNhRRRXQeKFFFFABRRRQAV5h8YrVTaaXeAfOsjRE+xGf6V6fWVrfh+x8QJbR36s8MEvm+WDgOcdD7VnVjzwcUdmX4hYbExqy2X+R4n4V8Gah4mmDqDBYqfnuWHX2UdzXt2jaJp/h3ThbWUaxxqNzyMfmY9yxq/FFHbwrFEixxoMKqjAArx74i+PGv5ZNF0qUi0Q7biZD/rT/dB/u/zrJQhQjd7npVMTic2q+zjpBf1d92SeOfiQ920ul6FKUtx8st0pwX9QvoPevM6KK5Jzc3dn0eGwtPDQ5Ka/4IUtJS1J0hS0lLQAtJS0lIoKWkpaACiiigYtFFFAwooopDFooooGFLSUtAwooopDCiiigYUqkqwZSQw5BBwRSUCgZ12h/EXXdH2xyyi+thx5c5ywHs3WvTdB+IWia3tiab7HdH/llOcAn2boa8ForaGInDzPKxeTYXE625X3X+R9SggjI70tfP2g+ONb0AqkVwbi2H/LCclhj2PUV6n4e+Ieja5thlf7Fdn/AJZTHgn2boa7KeIhPTZny+MyXE4b3kuaPdfqjrqKKK3PICiiigAooooAKKKKACiiigAqG5tLa8jMdzbxTIf4ZEDD9amooGm07o4zVfhl4f1Dc1vE9jKehgPy/wDfJ4rhNY+F2t6fuksjHfxDtH8r/wDfJ/pXt1FYzw8JdD1MNnOLoac3Muz1/wCCfL08E1tM0NxE8Uq9UkUqR+BqOvpXVND0zWofK1CzinHYsPmX6HqK821/4TzRbp9Dn81ev2eY4b8G7/jXJUwso6rU+kwfEGHre7V9x/h9/wDmeZ0tTXdnc2Fy1vdwSQTL1SRcGoa5j3k01dCUtJS0igooooKCiiikMKKKKACiiigAooooAK0dC0t9a1y009Af30gDkdlHLH8qzq9S+Eui/wDH1rUq/wDTCHI/Fj/IfnWtKHPNI4syxX1XDSq9enq9j0+GJIIY4Y1CxxqFUDsBT6QkAZJwBVRtX01H2PqForf3TMoP869bRH5klKT01LlFNSRJUDxurqejKcg06mSFFcX4r+Idl4fmaztYxd3y/eUNhI/94+vsK8+ufiZ4muHJS5hgXsscQ4/PNYTxEIOx6+EyTF4mKmkkn3Pda8H+Jfhv+xfEBvIExZ3xLrgcK/8AEP61b0/4p6/ayD7UILyPuGTY34Ef4V2c9/pPxK8MXNjbt5V8i+YkUn3o3HQj1HbPvWcqkK0bLc7KOCxWV1VUqK8Ho2tUeGUU+WKSCaSGVCkkbFXU9QR1FMrkPpkLS9qSl7UihaKKKCkLRRRQUhaKKKQ0LRRRSKQUtJS0FBR3oo70hoWiiikWBpaQ0tA0FFFFBQUUUUhhRRRQAUUUUAFFFFACgMxCqMsxwB6mvo7w3pg0bw7Y2OMNFEN/+8eT+prx34d6GdY8TxSyJm2s/wB9JkcE/wAI/P8AlXu9d+Dho5nx3E2KUpxw8emr/T+vMKKKK7T5QKKKKACiiigAooooAK8I+JmnCx8ZTSquEuo1mH16H+Ve715X8YrUf8Sq7A5+eIn8iP61z4lXp3PZyGq4YxR/mTX6/oeWUtJTkVndURSzMcBVGSTXnH3YV23g3wBc68yXt+Hg00HI7NN9PQe9bvg34abTHqOvx5PDR2Z7e7/4V6iqhVCqAFAwABwK66OGv70z5rNM9UL0sM9er/y/zIbOzt7C0jtbWFIYIxhUQYAqeiiu4+Rbbd2Yni+RovB+run3havj8q+bBX1BrNt9t0S+tsZ82B0/NTXy+ARwRgjgiuPFbo+n4fa9nNeY4U4U0U4VyH0kR1LSUtI1iLS0lLUs1iLRRRSNUFFFS2ttLeXcNrCpaWZwiAepOKBtpK7Pb/hlbNb+C7dmGDNI8g+mcD+VM+IltrGqaRFpOkWkkxuHzO4ICqo7En1P8q6jTLFNN0u1so/uwRKg/AVbr11D92oH5jUxf+2SxKV9W1f8Dxaw+EWtT4N5d2tqO4BLt+nH6101h8ItGgwb26uro9wCI1/Tn9a9DoqVQguhrVzfF1PtW9P6uYun+EtA0vBtdKtlYfxsm5vzNbIAUAAAAdAKTzEBwXXPpmnVqklsefOpObvNt+oUUUUyAo6DNc54r8YWXhe1HmDzryQfurdTyfc+grxvWvF+t67Ixubx0hJ4ghO1B/j+NYVcRGnpuz18vyavjFz/AAx7v9Ee8T63pVqcT6jaxn0aVc/zog1vSrptsGo2sjeiyrmvmkgE5IyfU0oAByBg+orn+uPse3/qtTt/Ed/Q+pKK+etE8Za3oUi/Z7tpYB1gmO5T/UfhXsPhXxjYeJ4CI/3F4gzJbsefqPUV0UsRGppszxcfkuIwa5/ij3X6o6SiiitzxwooooAKKKKACimu6RoXdlVRyWY4ArntR8eeG9NJWXU4pHH8EPzn9OKTkluaU6NSq7U4t+h0dFed3Hxf0aMkW9jeTehICfzNUm+MkOfl0aXHvMP8Kzdemup2xynGS2h+R6jRXmUXxjsyf3ukXCj1WRTWxY/FHw3dkLLLNasf+e0Zx+YzQq1N9SZ5XjIK7pv5a/kdrRVay1Gy1GHzbK6huE9Y3BxVmtb3OFxcXZnm3xhuSmk6bbA/6ydnI/3R/wDXryKvTfjE5+16UnYI7fqK8yrzMQ71Gfe5JHlwUPO/5i0lFFYHsIWgUUCkUFLSUtBSCiiikUgooooAKKKKACiiigApeRyOo6UlLQB9IeHr7+0vDun3ecmWBSx98YP6itOuN+GFybjwXChPMErx/hnP9a7KvZpy5oJn5bjaXssTUpro2FFFFWcoUUUUAFFFFABRRRQB5d8Y4R5Wkz453yJn8Aa840nR7/XL5bPT4GllPU9FQepPYV7h4y8KyeKl0+ATiGGGYvK+MttxjA9619H0Sw0GyW00+ARxj7zdWc+pPc1yToOdRt7H0mGziGFwMacdZ6+i16mJ4S8C2PhqNZ5NtzqJHzTEcJ7KO31611lFFdMYqKsjwa9epXm6lR3YUUUVRiFFFFAGH4xuDbeD9VlU4P2dlB+vH9a+dB0r3/4hkjwLqeO6qP8Ax8V4BXBi/iR9lw5H/Z5Pz/RBS0lLXIfRIXvRR3ooLQUd6KO9IpC0UUUFBRRRSGFFFFABRRRQAUUUUAejfCTUfJ1e905m+WeISqP9peD+h/SvXq+efBl79g8YaZNnCtMI2+jcf1r6Gr0sJK8Ldj4PiSjyYtTX2l+Wn+QyWNZYnjb7rqVP0NfLl/avY6ldWkgw8MrRkfQ19TV4V8VNFbTvFP25F/cXy789g44YfyNPExvFMjIqyjVlTfVfkcNS0lLXEfVhS0lLQULR3oo70ihaWkpaBhQKKBSKQtLSUtBSDvRR3opFIKWkpaBoU0UGikUgooopFBS0lLQUgooopFBRRRQAUUUUAFFFFABTo0eWRY41LO5CqB3J6U2uz+GmhnVPEi3kiZt7EeYcjgv/AAj+v4VcIuUlFGGKxEcPRlVl0R6/4f0tdG0GzsFAzDGAx9WPJP55rSoor2EklZH5bUnKpNzlu9QooopkBRRRQAUUUUAFFFZHibXofDmhXGoTYLKNsSf33PQUm0ldlQhKclCO7OP+J3jI6bbHRNPkxdzr+/dTzEh7fU/yrxip7y8nv72a8upDJPM5d2PcmoK86pNzlc+7wWEjhaSgt+r8xaKKKzOwKWkpaBhS0lLQAtJS0lIoKWkpaACiiigYtFFFAwooopDFooooGFLSUtAwooopDCiiigYUCigUDFooopDFpKWkoGdV4e8faxoBWIyfa7Qf8sZjnA/2W6ivWfD3jXSPESqkE3k3WObeU4b8PX8K+faVWKsGUlWByCDgit6eInDTdHk43JsPiveS5Zd1+qPqSivFvDfxN1DTNlvqoa+tRwHz+9QfX+L8a9Y0jXNO121Fxp9yky/xL0ZfYjqK7qdaNTY+PxuWYjBv31dd1saNFFFannhRRRQAUUUUAFFFFABRRRQAUUUUAZusaDpuvW3kahbJKP4X6Mv0PavIvFHw41DRQ91Ybr2yHJwP3kY9x3HuK9vorKpRjU33PRwOaYjBv3HePZ7f8A+WqWvafFvw5tNYD3mmBLW+6lcYjlPuOx968dvbK6067ktLyB4Z4zhkcc//AFxXnVKUqb1PucBmNHGxvB2fVdSCiiisj0QooopDCiiigAooooAKKKKAJIIJLm4jghUtLK4RAO5JxXu11qGn/D7wfbRy/M0SBI41PMsnU/ryTXAfC7RPt+vtqMq5hshlc9DIen5DJ/Ksz4mavJqXjCe33HybICFF7A9WP5/yrro/u4OfVnzWZ2xmLjhL+7HWX6L+u5m654w1nxDMzXV26QE/LbxEqij+v1NYeATmminCspNt3Z6VGnCnHlgrI09K13U9FnE2n3ksJByVDZVvqOhr05viYlx4JuLtVWHVlIh8sHjc38Y9sZNeQ0opxqyhojPEZfQxLjKa1T+/yY9mZ3Z3Ys7HLMTyT60lJS1iepEKtabqNxpOow31o5SaFtwI7+oPsaq0UJ21KlFSTjJXTOz+ImlRzCy8UWSYtdSjUygfwSY/r/MVwde7eGdKi174W22nXQ+WWJ1Vu6kMdp/A14jfWU+m39xZXK7ZoHKOPcV1VY7T7nzmX103PDt6wbXyvoQUvakpe1Ynpi0UUUFIWiiigpC0UUUhoWiiikUgpaSloKCjvRR3pDQtFFFIsDS0hpaBoKKKKCgooopDCiiigAooooAKUAsQFBJJwAO9JXoHwz8KnUb8azdx/wCiWzfuQRxJJ6/Qfzq4Qc5cqObF4qGFourPp+L7HfeBvDv/AAj3h6OOVQLuf97OfQnov4Cumoor14xUVZH5jXrTr1JVZ7sKKKKoyCiiigAooooAKKKKACvPfi9Hu8N2cn9y6A/NT/hXoVc/4t8OHxRptvY+f5Ea3CySPjJ2gHge/NZ1YuUGkdmX1Y0cTCpN2SZ4NpWkX2t3y2en27Syt1x0UepPYV7V4R8B2PhxFuZ9tzqJHMpHyp7KP69a3tG0PT9BsVtdPgEadWbqzn1J71o1nSw6hq9zvzLOamJvTp+7D8X6/wCQUUjMqKWdgqjqScAVi3Xi/wAPWTlJ9XtQw6qr7j+lbuSW55FOlUqO0It+iubdFc9D468MzsFXV4AT/fyv8xW5b3MF3GJLeaOVD/FGwYfpSUovZjqUKtL+JFr1ViWvnzx94Zl8PeIJZEQ/YbtzJA4HAJ5K/UGvoOqOraRZa3p0ljfwiWF/zU9iD2NRVp88bHVl+NeEq8z1T3PmEU4V0/i3wRf+F5zL81xp7HCXAH3fZvQ1zArz5RcXZn3FCtCtBTpu6Y6lpKWoOqItLSUtSzWItFFFI1QV6N8K/Dpub59buE/c2+UgyPvP3P4D9TXHeHtCufEWrxWFuCAfmlkxxGnc19C6dp9vpenwWVqmyGFQqj+p966sLS5pcz2R89xBmKo0vq8H70t/Jf8ABLVISAMk4Apa8s+KPjGS3Y+H9PlKuy5u5FPIB6IPr3runNQV2fHYTDTxNVU4f8MXPFPxTttOlks9FjS7uFOGnY/u1Pt/e/lXm2o+Ltf1VybrVLjaf4I22L+QrCFOrgnVlLc+0wuXYfDr3Y3fd7knmyltxlkLepY5rT0/xJrWluGs9TuY8fwlyy/keKyqUVldrY73ThNWkro9b8L/ABTS5lSz11EhdjtW6jGEJ/2h2+td9q+rW+kaPcalMwMUUe8YP3j2A+pxXzPW7ceJ7278K2+hTMWigm3hyeSoHCn6HP6V0QxLUWpHi4rIaVSrGdLRX1Xl5FDVNTutY1Ka/vHLzTNk+ijsB7CqlJS1yN3d2fSwiopRirJBRRRUmqCrFhfXOmX0N5aSGOeJtysP5H2qvRTFKKkrPY+kfD+sRa9odtqMQA81fnX+6w4I/OtOvM/hBes1nqVixyI5FlUem4YP8q9Mr16U+eCkfmOY4ZYbFTpLZPT0eoUUVgeJ/FuneF7PzLpvMuHH7q3Q/M/+A96ttJXZy06c6slCCu2bVzdQWdu9xczJDCgyzu2AK818Q/FqCEvb6FAJ3HH2mYYT8B1P44rz3xF4q1TxNc+ZezbYQcx26HCJ+Hc+5rFFcdTEN6RPqcFkdOC5sRq+3T/gmnqviHVtbkLahfzTDPCbsIPoo4rNFJ3pa5m29We9CEYLlirIKWkpaRohaKKKRSJ7S8urCdZ7O4lglXo8bFTXpHhj4qyIyWuvqHQ8C7jHI/3h/UV5j2oqoVJQehzYnBUMVHlqx+fU9I+LU0V1caPdQSLLDJC5SRDkMMivOKe1xM9vHA0rtDGSUQnhSeuKZSqS55cxeDw/1aiqN72/zCiiioOxC0CigUigpaSloKQUUUUikFFFFABRRRQAUUUUAFLSUtAHrvwhl3aLqEP9y4BH4r/9avRq8w+D7fudVX/bQ/oa9Pr1sP8AwkfnGdxtj6ny/JBRRRWx5QUUUUAFFFFABRRRQAUUUjMqKWdgqjkknAFAC0VyurfETw5pJZDefaZl6x2w3/r0/WuQvvjHKSRp+koo7NPJn9B/jWcq0I7s76OWYqtrGGnnp+Z6zRXhU3xT8TSk+XJawj0WEH+dVx8SvFQOft8f4wLWf1qB2rh/FNbr73/ke+0V4ja/FfxDCw89LS4HcGMr/I11GlfFzTrhlTUrOW0Y9ZEO9f8AEfrVRxFN9TGrkuMpq/Lf0/q50njuMy+CNVUdod35EH+lfPXavo67ubLxD4avhY3MVxFNbuoKNnkqevpXzjjAweo61zYrdM9vh1tUqlN6NP8ANf8AAClpKWuQ+jQveijvRQWgo70Ud6RSFooooKCiiikMKKKKACiiigAooooAlglMFxFKpwUcMD9DX03BKJ7eKYdJEDD8Rmvl9vun6V9H+Gp/tPhnTJc53WyfoMV24N6tHynFEPcpz82vyNWuf8ZeHE8TeHprQYFyn7y3Y9nHb6HpXQUV3NJqzPkadSVOanHdHypLFJBM8MyFJY2KurDlSOopteu/E3wSbpX1/TYszIM3USjlwP4x7jvXkQ6V5tSDg7M+7weKhiaSnH5+TClpKWoOwWjvRR3pFC0tJS0DCgUUCkUhaWkpaCkHeijvRSKQUtJS0DQpooNFIpBRRRSKClpKWgpBRRRSKCiiigAooooAKKKKAFVWZgqglmOAB1Jr6C8GaAPD3h2G2dQLmT97Of8AaPb8Olec/DLw1/aWqnVrlM2to37sEcPJ/wDW6/lXs9d+Ep2XOz43iTH80lhYPRav16IKKKK7T5UKKKKACiiigAooooAK8K+J/iP+1/EH2CB82lgSnB4aT+I/h0/OvWfF+uDw/wCGbu+BAmC7IR6ueB/j+FfNzMzszuxZmOST3NcuJnpyo+gyLC80nXl00X6iUUUVyH1AtFFFIYUtJS0DClpKWgBaSlpKRQUtJS0AFFFFAxaKKKBhRRRSGLRRRQMKWkpaBhRRRSGFFFFAwoFFAoGLRRRSGLSUtJQMWiiigYVZsb+70y6W6sriSCZejIcfn6iq1FCdglFSVmtD13wz8UoLnZa66qwTHgXKD5G/3h/D/KvRo5EljWSN1dGGVZTkEV8u10HhvxlqvhqQLBJ51oT81tIfl/D+6a66WKa0mfNZhw9Cd54bR9uny7fl6H0JRWB4c8X6X4lh/wBFl8u5Ay9vIcOPp6j3Fb9d0ZKSuj5GrRqUZuFRWaCiiimZhRRRQAUUUUAFFFFABRRRQAVheJvCth4ms/LuV2XCD91cKPmQ/wBR7Vu0UpRUlZmlKrOjNTpuzR8265oV94e1FrO+jw3VJB92RfUGs2vpDXtBsvEWmvZ3seR1jkA+aNvUV4J4g8P3vhzUms7xcjrFKB8si+o/wrzK9B03dbH3uU5tDGx5J6TX4+aMqiiiuc9sKKKKACiiigAo57DJ7Ciup+H+h/214ohMiZtrX99L6HH3R+J/lVRi5SUUZYitGhSlVnslc9a8FaH/AGD4Ztrd1xcSDzZv949vwGBXiHjWFoPG2rq3e4LD6HmvpCvFvi1oUtrrkesRoTb3SBHYDhXHHP1H9a9GvC1NJdD4bKcU542Uqj1nf79zzwU4U0U4Vwn10R4pRSClFSzeIopaQUtSbxClwScAZJ4AFJXW/D3w+2t+I45pEzaWZEshI4Lfwr+fP4U4xcpKKM8RXjh6Uqs9kj2Tw7YHS/Dun2RGGihUN/vdT+pNed/Frw3zF4gtk9IrnA/75b+n5V6vVa/soNS0+eyuUDQzoUcexr1p01KHKfm2Gxk6WJ9u+r1+e58uUvar2taTPoes3OnXAO+F8Bv7y9j+Iqj2rzWraH3kJKUVKOzFooopGiFooooKQtFFFIaFooopFIKWkpaCgo70Ud6Q0LRRRSLA0tIaWgaCiiigoKKKKQwooooAKKK09B0G98Q6ktlZJz1kkI+WNfU00m3ZE1KkacXObskWvCvhq58TasttGClsmGuJsfcX0Hue1fQFlZ2+n2UNpaxiOCJQqKOwqnoOhWfh7S47GzXgcu5+9I3cmtOvUoUVTWu5+eZvmcsbVtHSC2/zCiiitzyAooooAKKKKACiivOfFvxQg0yaSw0VUublTteduY0PoP7x/SpnNQV2dGHw1XET5KaueikhQSSAB1Jqm+saZG219RtFb0M6j+tfOmpeIdX1iQvf6hPLn+DfhR9AOKzcAnkZrmeK7I96lw7dfvKmvkj6kgure5GYJ4pR6xuG/lU1fLdvcT2sgkt5pInHRo3KkflXc+HPifqenSJDqxN9a9C5/wBag9c/xfjTjiot2krGWI4eqwjzUZc3lsz2usDxV4rs/C9iJZv3tzJkQwA8sfU+g96vprVhLop1eK4V7IRmXzAew/r7V8+a9rVx4g1ifULgn5ziNOyJ2Aqq9bkjpuznyjLHiqz9rpGO/r2/zLGueKtX8QTM17dMIs/LBGdqL+Hf8axunSkpa82Tbd2ffUaUKUeSCsgq3p2qX2k3AnsLqW3kH9xuD9R0NVKKSdtjSUYyXLJXR7L4O+I0Wryx6fqoSC9biOUcJKfT2Nd/Xy4CQQQSCDkEdq93+H/iJ9f8PgXDbry1IilPdh/C34j+Vehhq7l7stz4rPMohh19YoK0eq7f8A6ieCK5geGeNZInG1kcZBFePeNPhtLpvmajoqNLZ/ektxy0X+76j9RXstFb1KcZqzPFweOq4SfNDbqujPlYU6vTPiX4MS03a7psQWJj/pUSjhSf4x7eteZ15tSDhKzPvMFioYqkqkP+GFpaSlrNndEWrNhYXOp30VnZxGWeU4VR/M+1JY2NzqV5HaWcLSzyHCoo/X2Fe6eDfB1v4Ys977ZdQlH72XHT/ZX2/nWtKi6j8jhzLM6eBp95PZfq/Is+E/DFt4Y0sQJh7mT5p5sfeb0HsK36KK9SMVFWR+d1q0603UqO7ZDd3C2lnPcv92GNnP0AzXy9e3kuo39xezMWknkaRifc19H+K93/AAieq7PvfZXx+VfNC9BXJinqkfRcPwXLOfXRDhTqaKdXIfSodSikpRSNELSikpRSNELS0lLSNUFFFFSaIKKKKAPSvg+D/aOqHt5KZ/M161Xm/wAIrIx6Xf3pH+ulEan1Cj/E122u6zbaBo9xqN0fkiX5V7u3ZR9a9XD+7STZ+eZ03VzGcYavRfgjK8Z+MLbwrp+cLLfTA+RDn/x4+wrwLUNQutUvpb29maa4kOWZv5D0HtUusatd65qk2oXr7pZT07KOyj2FUa5atVzfke/l2AhhYd5Pd/oFOFNpwrI9JB3paTvS0igpaSloKQtFFFIpC9qKO1FIpBS0lLQUFFFFIpC0CigUigpaSloKQUUUUikFFFFABRRRQAUUUUAFLSUtAHqfwfH7vVT23R/yNeoV5t8H48abqcnrOq/kv/169Jr1cN/CR+c5474+p8vyQUUUVueSFFFFABRRRQAUU13WONndgqKMszHAArx/xt8Spb1pNN0KVo7YfLJdLw0nsvoPfvUTqKCuzrwmDq4qfLD5vsdf4o+I2maAWtrfF7fDgxo3yof9pv6CvI9c8W6z4hkJvbthDniCL5UH4d/xrC759aWuCpVlM+wweW0MMrpXl3f9aC0UUVkekLQKKBSKQtLSUtA0XNM1W+0e7FzYXLwyDrtPDD0I6EVWkcyyvIQAXYsQBxyc0yii7tYajFS5ktQpaSlqS0L3oo70UFoKO9FHekUhaKKKCgooopDCiiigAooooAKKKKAFr6A8CSeZ4I0onqIcfkTXz/XvXw8JPgfT8+jf+hGuvB/G/Q+b4mX+yxf979GdTRRRXonw4h6YNeO/ELwA1i8us6RFm1Y7riBB/qz3YD+77dq9jpCAQQQCDwQaipBTVmdWDxdTC1OeHzXc+U6WvTPH/wAPDaGXWNFiJgOWntkH+r9WUenqO1eZ1504ODsz7jC4qniaanTf/AFo70Ud6g6haWkpaBhQKKBSKQtLSUtBSDvRR3opFIKWkpaBoU0UGikUgooopFBS0lLQUgooopFBRRRQAUUUUAFX9G0m51zVYNPtRmSVuW7Ivdj9KoqrOyqqlmY4Cgck17p4C8Jjw7pfn3KD+0LkAyH/AJ5r2Qf1rajSdSVuh5ua5hHBUOb7T2X9djotJ0y30bS4LC1XEUK4B7se5PuTV2iivVSSVkfm85SnJyk7thRRRTJCiiigAooooAKKKa7rHGzucKoJJ9BQB498YNZM+p2mjxt8luvnSj/bbp+Q/nXmlaGu6k2r6/fX7HPnTMy+y5wP0rPrzKkuaTZ99g6HsKEafZfiFFFFSdQtFFFIYUtJS0DClpKWgBaSlpKRQUtJS0AFFFFAxaKKKBhRRRSGLRRRQMKWkpaBhRRRSGFFFFAwoFFAoGLRRRSGLSUtJQMWiiigYUUUUhi0lLSUDJIZpbeZJoZHjlQ5V0OCD9a9O8K/FAjZZ+IPot2o/wDQx/UV5dRWkKkoO8TkxeBoYuHLVXz6o+oYZoriFZoZFkjcZV0OQR9afXz34a8Yan4amAgfzbQn57Zz8p9x6GvafD3inTfElt5lnLtmUfvIH4dPw7j3FehSrxqadT4fMcnrYN83xQ7/AOfY26KKK3PJCiiigAooooAKKKKACiiigArI8ReHrPxJpb2d0uGHMUoHzRt6iteik0mrMunUnSmpwdmj5p1fSbvRNTlsLxNssZ4PZh2YexqjXvvjXwpF4m0s+WFW/gBaCQ9/9k+xrwWWKSCZ4ZkZJI2KurDkEdRXlVqTpy8j9FyrMY42lfaS3X6+jGUUUVieoFFFFABXuvw50L+x/DMc0qYubz98+eoX+Eflz+NeUeD9DOv+JLa1ZSYEPmzn/YHb8elfQoAVQAAABgAV24Snq5s+U4mxloxw0eur/T/MWq1/YWup2UtneQrLBKMMjD/PNWaK7z49Np3R4J4w8A3nhp2urbfc6YTxJj5ovZv8a5AV9UOiSIyOoZGGGVhkEV5L43+G32VZdU0NCYR80toOSvqU9vauKtQt70T6vLM5U7UsRo+j7+p5oKUUgpRXGz6eIopaQVJDDLcTJDDG0ksjbURRksT2qTZOyuyWxsrjUb2GztIzJPK21FH+elfQfhjw/B4b0WKyiw0n3ppMffc9T9PSsfwL4MTw7afa7tVfUpl+Y9REv90f1NdjXo4ejyLmlufDZ5mqxU/Y0n7i/F/5BRRRXUfPnlvxf0UNb2mtRL8yHyJiB2PKn88j8a8m7V9L+I9MXWPDt9YMMmWI7f8AeHI/WvmkgqSrDDA4I964MTG0r9z7HIsR7TD+ze8fyYUUUVznuIWiiigpC0UUUhoWiiikUgpaSloKCjvRR3pDQtFFFIsDS0hpaBoKKKKCgooopDCiiuq8J+CL7xLKszhrfTgfmnI5f2X1+vSqjFydkZV8RTw8HUquyRmeHvDl/wCJL8W1mmEX/WzMPljHv7+1e8eH/D9j4c01bOzT3klb70jepqxpWk2Wi2CWdjCsUKenVj6k9zV2vTo0FTV3ufA5rm9TGy5I6QXTv5sKKKK3PGCiiigAooooAKKKKAPOfih4uk0u0XRrGQpdXK7pnU8pH6D3P8q8YFavibUn1fxNqF6xJDzME9lHAH5CsqvNqzc5XPvMvwscPQUVu9X6jqWkpayPRQ6lpKWpNEalrrt7a6FeaOj/AOi3TKzAn7pB5x9e9ZtJS0NtlQhGLbit9WFLSUtSboKKKKRQV33wmu2h8S3Ftn5J7cnHupB/lmuBrs/hepPjSMjosEhP5VrRdqiODNYqWCqp9me40UUV65+ZEVzbxXdrLbTIHilQo6nuDxXzXrOmvpGtXmnvyYJSoPqOx/KvpmvDvipbCDxl5oGPPt0c/UZH9K5cVG8Uz6Hh2s415UujX4o4qtHRtFvtev1s7CEySH7zH7qD1J7CtTwr4L1DxNKHUG3sVPz3DDr7KO5r2/RdCsPD9itpYQhF6u55Zz6k9656VBz1ex7eY5zTwicKes/wXr/kZ/hXwjZeGLPbHiW8cfvrgjk+w9BXRUUV6MYqKsj4itWqVpupUd2wooopmRXvrcXen3NsRkSxMn5jFfLbxmKR4m6oxU/gcV9WV8+/ELQZNE8VXDhCLW8YzQtjjn7y/ga5cTHRM+gyCslOVJ9dfuOUFOpop1cR9Wh1KKSlFI0QtKKSlFI0QtLSUtI1QUUUVJogpQCxAUZYnAHqaSuy+HHh86x4gW7mTNpZESNkcM/8I/rVwi5yUUYYnERw9GVWeyPWfCuk/wBieGrKxIxIqbpP988mvKPin4jOp67/AGXA+bWxOGweGl7/AJdPzr1zxFqq6J4fvdRbrDESg9W6Afnivmd5HmkeWRi0jsWYnuTya768uWKgj43J6Tr1p4qpvf8AF7jaWkpa4z6ZBThTacKBoO9LSd6WkUFLSUtBSFooopFIXtRR2opFIKWkpaChaSiikUhaBRQKRQUtJS0FIKKKKRSCiiigAooooAKKKKAClpKXtQB7R8J4fL8KSy4/1ty36ACu7rmPh9bfZfBOnAjDSKZD+JNdPXsUVamkfmOZz58ZUl5sKKKK0OEKKKKACkJABJIAHUmlrzb4o+LmsLX+w7GTbc3C5uHU8pGe31P8qmc1FXZ0YXDzxFVU4dTnPiH46bWJ5NJ0yUjT4ziWRT/r2Hb/AHR+tefCiivNnJyd2fdYbDww9NU4LQWlpKWpOlC0UUUihaBRQKRSFpaSloGgooopFoKWkpaQ0L3oo70UFoKO9FHekUhaKKKCgooopDCiiigAooooAKKKKAFr3v4eDHgfT89wx/8AHjXglfQHgNNngjSh6xZ/MmuvB/G/Q+b4mf8AssV/e/RnR0UUV6J8OFFFFACHngivJ/H/AMPDH5usaJD8n3ri1QdPVlH8xXrNFROCmrM6sJi6mFqc8Pmu58qCjvXq/j/4e7vN1jRYfm5a4tUHX1ZR/MV5R3rzpwcHZn3GExdPFU+eHzXYWlpKWoOsKBRQKRSFpaSloKQd6KO9FIpBS0lLQNCmig0UikFFFFIoKWkpaCkFFFFIoKKKKACiiu68AeCjrdwupahGRp0TfIh/5bMP/ZRVwg5y5Uc+KxVPC0nVqPRf1Y2Pht4NI8vXtRj97WJh/wCPn+n516lSBQqhVAAAwAO1LXrU6apxsj83x2NqYys6s/kuyCiiirOMKKKKACiiigAooooAK57xzqJ0zwZqU6th2i8pD7t8v9a6GvOPjFeGLw/Y2gOPPuNxHqFH/wBcVFR2g2deBp+0xMI+Z4uOBgUtFFeafeBRRRQMWiiikMKWkpaBhS0lLQAtJS0lIoKWkpaACiiigYtFFFAwooopDFooooGFLSUtAwooopDCiiigYUCigUDFooopDFpKWkoGLRRRQMKKKKQxaSlpKBi0UUUDCprW7uLG5S5tZnhnjOVkQ4IqGigGk1ZnsHhL4lwagY7HWilvdH5Un6JIff8Aun9K9DBBGQcg9K+XK7Xwl8QrzQilpf77rTxwMnLxfQ9x7V20cV0mfK5nw+nerhP/AAH/AC/yPb6KqadqVpqtml3YzpNC44ZT09j6Grddyd9UfISi4vlkrNBRRRQIKKKKACiiigAooooAK8x+J3hPzIzr9lH86DF0ijqOz/h3r06myIksbRyKGRgVZSOCD2qKlNTjys68DjJ4SsqsPn5rsfLtFdJ418NN4b1xoowfsc+ZLdvQd1+o/wAK5uvIlFxdmfplCtCvTVWDumFFFbHhfRH8QeILaxAPlFt8x9EHX/D8aSTbsiqtSNKDqT2Wp6n8MdB/s3QDqEyYuL47hkcrGPuj8ev5V3NNjRYo1jRQqIAqgdgKdXswgoRUUfl2LxMsTWlWl1CiiiqOcKKKKAPDPiR4bTQ9dF1bJttL3LhQOEcfeH9fxrjBXt/xUtFuPBzTEfNbzI4P1O0/zryPQ9A1DxDei2sIS2PvyNwkY9Sa82vTtUsup97lOMU8GqlV/Do36FO1tZ725jtrWF5p5DhEQZJNe2eCfAsPh2IXl5tm1Jx16rEPRff1NaHhbwdYeGLbMQ868cYluGHJ9h6Cujroo4dR96W54ea51LEJ0aGkOr6v/gBRRRXUfPBRRRQAV84eMLAab4u1O2AwomLoPZuR/Ovo+vD/AIs2wh8XpMB/r7ZWP1BI/wAK5sSrwue5kFTlxDh3X5HC0UUVwn2KFooooKQtFFFIaFooopFIKWkpaCgo70Ud6Q0LRRRSLA0tIaWgaCiiigoKfFFJPKsUMbSSOcKiDJJ9hW/4d8Gat4jdWgi8m0z81zKML+Hqa9i8OeD9L8NxA28fm3RGHuJBlj9PQfStqWHlPXZHk5hnNDBpxXvT7L9f6ucb4T+GPMd7r490swf/AEM/0r0+ONIY1jjRURRhVUYAFPpCQBkkAe9ejTpxpq0T4bGY6vjJ89V+i6IWimLLG5wrq30OafWhxhRTJporeF5ppFjiQbmdjgAV5trfxahhmaHRrQXAU48+YkKfoOtROpGC95nXhcDXxcuWjG/5HplFeJf8LU8R+ZuxZ7f7vkn/ABrpND+LFvcSrDrFr9m3HHnxEsg+o6iso4mm3Y762QY2lHm5b+jPSaKZDNHcQpNDIskbjcrqcgin10HitW0YUyQExOB1KnFPooA+VJQRPKD1Dtn86bWr4nszp/inU7XGAly+36E5H6GsqvKas7H6NSkpQUl1HUtJS1Juh1LSUtSaIWlpKWkzVBS0lLSNEFFFFIoK9I+ENiX1PUL8r8sUQiU+7HP8h+teb9s1738PtGOj+FLcSLtnuT58gxyM9B+WK6MNHmqX7HicQYhUsG49ZafqzqaKKK9Q/PgrmdZ8F2Wv+IbfUtQYyQwQiMW44DnJOWPpz0rpqKUoqSszWlWqUZc1N2YyKKOCJYokVI0GFVRgAfSn0UUzJu4UUUUAFFFFABWT4i8PWXiXSnsbxcfxRyAfNG3Yitaik0mrMqE5QkpRdmj5q8Q+GtR8M35tr2PKE/up1HySD2Pr7Vk19Qalplnq9jJZ30CTQOOVYdPcehrxPxh8Przw6z3dnvutN67gMvF7N7e9cNWg46rY+vy7N4V7U6ukvwZxtKKaKcK5j3kLSikpRSNELS0lLSNUFFFFSaE9lZz6hew2drGZJ5mCIo7mvobw1oUPh3RIbCLBYDdK/wDfc9TXM/Drwd/Y9qNVv48X86/IhH+qQ/1Peu9r0sNR5VzPc+Fz7M1iJ+wpP3Y/i/8AJHm/xh1EwaHZaerYNzNvYf7Kj/Ej8q8Zr0P4w3Jk8S2dvn5YbbOPcsa88rGu7zZ6mU0+TCR89QpaSlrE9NBThTacKBoO9LSd6WkUFLSUtBSFooopFIXtRR2opFIKWkpaCgopaSkUhaBRQKRQUtJS0FIKKKKRSCiiigAooooAKKKKAClCliFHUnApK1vDNl/aPifTbXGQ86lvoOT+gppXdiKk1Tg5vorn0FpVqLLSLO1Ax5UKIfqAKuUUV7SVlY/KJScpOT6hRRRTJCiiigClq+pw6PpN1qFwf3cEZcj1PYfieK+Z9Rv59U1G4v7lt007l2Pp7fQdK9V+MOsGKxstHjbmdvOlH+yvA/X+VeQ1xYid5cvY+syPDKFF1XvL8haKKK5j3ULS0lLQUhaKKKRQtAooFIpC0tJS0DQUUUUi0FLSUtIaF70Ud6KC0FHeijvSKQtFFFBQUUUUhhRRRQAUUUUAFFFFAAfun6V9GeFYvI8K6XH6WyfqM186AbiF9TivprT4vI021h/55wov5ACu3BrVs+V4onalTj5v+vxLNFFFd58aFFFFABRRRQAV5f4/+Hvn+brGiw/veWuLZB9/1ZR6+3evUKKicFNWZ04XFVMNU9pTf/BPlWlr13x98PheCTV9GhAufvT26j/Wf7S/7Xt3ryLBBIIII4IPavOqU3B2Z91g8ZTxVPnh812CgUUCszsQtLSUtBSDvRR3opFIKWkpaBoU0UGikUgooopFBS0lLQUgooopFBRRXW+C/BU/iW5FxcBotMjb55OhkP8AdX+pqoxcnZGOIxFPD03UquyQ7wR4Ll8SXQuboNHpkTfO3Qyn+6v9TXuMEEVtBHBBGscUahVRRgACm2trBZWsdtbRLFDGu1EUYAFTV6tGkqa8z87zLMqmOq8z0itl/XUKKKK1PNCiiigAooooAKKKKACiiigArx34zXG7VtMts/cgaTH1OP6V7FXhvxcfd4wjXP3LVR+ZJrHEP3D1cljfFp9kzg6KKK4D7IKKKKBi0UUUhhS0lLQMKWkpaAFpKWkpFBS0lLQAUUUUDFooooGFFFFIYtFFFAwpaSloGFFFFIYUUUUDCgUUCgYtFFFIYtJS0lAxaKKKBhRRRSGLSUtJQMWiiigYUUUUhhRRRQM1tB8Raj4cvPtFjLhSf3kLcpIPcf1r27wx4u0/xNbZgbyrpB+8t3PzL7j1HvXz3Uttcz2VzHc20zwzRnKOhwQa3pV5U9Oh5OZZRRxq5tp9/wDM+n6K4Dwd8RYNW8uw1ZkgvjwkvRJf8D7V39elCcZq8T4LFYSrhans6qs/z9AoooqzmCiiigAooooAKKKKAMHxf4ej8R6DLa4AuE/eQOezj+h6V89yRvDK8UqFJEYqynqCOor6irx74peHfsWoprNumIbo7ZsD7snr+I/UVx4undc6PqeHMfyTeFm9Ht69vmeeV7P8LtA/s/RG1OZMXF79zPVYx0/Pr+VeYeGNEfxBr9tYAHy2bdMw/hQdf8Pxr6JijSGJIo1CxooVVHQAdKzwlO752dfEuN5Kaw0d3q/Tp97/ACH0UUV6B8WFFFFABRRRQBna5o8OvaTLp1xI6RSldxTrgEHH6VLpml2WkWSWlhbpDCnZR19ye5q5RSsr3NPaz5PZ393e3mFFFFMzCiiigAooooAK8f8AjIgGq6W/cwOD+DD/ABr2CvIfjI3/ABMdKXPSKQ/qKxxH8Nnq5L/vkfn+R5nRRRXnH3CFooooKQtFFFIaFooopFIKWkpaCgo70Ud6Q0LRRRSLA0taOk6DqmuTeXp1nJNzgvjCL9WPFelaD8KLW32z61P9pkHPkREhB9T1P6VrClOeyOHF5lhsIv3ktey3/r1PNdI0HU9duPJ061eXB+Z+iL9T0r1Pw58L7DTylxqzLe3A5EYGIlP0/i/Gu6tbW3srdbe1hjhiUYVEXAFTV208NGOr1Z8njs/xGIvCl7kfx+//ACGoixoqIoVVGAqjAFOorN1/Uxo2g32oEZMERZR6t2/WuhuyPDjFzkordnKeOPiHH4fkOnaaqTajj52blYfr6n2ryPUNf1bVpTJfahcSk/wlyFH0A4rOmnlubiS4ncvNKxd2PUk9aQV51SrKbPusDgKWGikleXVk8NxPA4eGeWNgchkcg/pXc+FviXqOm3Edvq8jXdkTgyN/rI/fPcexrgRTqzjOUXdHbWwtHER5asbnpXxP8Ufa54tGspgbYIsszIeJCRlR9Mc/jXnApMk9Tn60oqak3OXMzXA4WGFoqlDp+IUUdqKzO49H+FniOWHUDodxIWgmBeDJ+4w5IHsRXrtfO3hB2TxfpLL1+0KPzr6Jr0sJJuFn0PhOI6EKeKU4/aV36hRRRXUfPHjvxb8PSQ6jHrsCEwTARzkD7rjoT9Rx+FeaV9TXlpb39pLa3USywSqVdGHBFeGeMvAN34ble7tA9xpZOQ4GWi9m9veuKvSafMj6rJ8xjKCoVHZrbz/4Jx1LSUtcp9Gh1LSUtSaIWlpKWkzVBS0lLSNEFFFPhhluJ44IUaSWRgqIo5JPQUFNpK7Oh8EeHm8Q+IYonUm1gIlnPbA6L+J/rXv4AAAAwB0Arn/B/huPw1oaW5w11J89w47t6fQdK6GvUoUvZx13Z+dZzj/rmI934Y6L/P5hRRRW55AUUySWOJd0siovqxwKijvrOVtsd3A7eiyAmi41FtXSLFFFFAgorJ1/xDYeHLA3V9JjPEca8tIfQCvJdY+J2u6hIy2bLYQZ4EYy/wCLH+lY1K0Ke56WByrEYz3oK0e72PcKK+bn8Ra3I5Z9WvSx7+ca0tN8e+I9NcEag1xGOsdwN4P49RWKxkb6o9SfDGIUbxmm/me/0Vx/hXx/YeIWW1nUWl+RxGzfLJ/un+ldhXVGcZq8TwMRhquHn7OqrMKRlDKVYAqRggjg0tFUYHlvjP4Zq4l1HQECv96SzHQ+6eh9q8qIKsVYEMDggjkGvqevKvif4QRUbxBYRY5/0tFHX0f/ABrjr0FbmifUZPm0nJUK7vfZ/ozy2lFJSiuI+rQtLSUUjVC16d8PPA5kaLW9Vi+QfNbQOOv+2R/IVD4D8AteNHq+sREW4+aC3YcyejMPT27162AAMDgCuzD4f7cj5fO85STw2HevV/ov1FoooruPjzwj4rk/8Ju49LePH61xFd/8Xrcx+K7ebHE1qOfoSK4CvNq/Gz7vL3fC07dgpaSlrM7kFOFNpwoGg70tJ3paRQUtJS0FIWiiikUhe1FHaikUgpaSloKCiiikUhaBRQKRQUtJS0FIKKKKRSCiiigAooooAKKKKACu8+FNh9p8TS3bD5bWA4/3m4/lmuDr2b4Uad9m8NzXrDDXcxIP+yvA/XNb4ePNUR5OeV/Y4GfeWn3/APAud9RRRXqn5yFFFFABRRRQB8+/Ei/N944vRnKW4WBfbA5/UmuTq9rU5ute1C4JyZLl2/8AHjVGvLk7ybP0HDQ9nSjDskLRRRUnQhaWkpaCkLRRRSKFoFFApFIWlpKWgaCiiikWgpaSlpDQveijvRQWgo70Ud6RSFooooKCiiikMKKKKACiiigAooooAu6Tb/a9ZsbcDPmzov5kV9L14B4BtTdeNtOXGRG5lP8AwEE/zxXv9ehg17rZ8VxRUvXhDsr/AHv/AIAUUUV2HzAUUUUAFFFFABRRRQAV5T8TPBYUSa/psWO93Eo/8fH9fzr1amuiyIyOoZWGGBHBFRUgpxszqweLnhaqqQ+fmj5YoFdR468Lt4a1wiFT9hucvAf7vqv4fyrl68yUXF2Z+gUK0K1NVIbMWlpKWpN0HeijvRSKQUtJS0DQpooNFIpBRRRSKClpKWgpBRQa9C8E/DyTUjHqWsRtHZ/ejgPDS+59F/nVQhKbtE58Vi6WFp+0quy/P0KHgrwLN4hlW9vVaLTFPXo03sPb3r2y3t4bS3jt7eJYoY12oijAAp0caQxrHGioijCqowAKfXqUqSprQ/PsxzKrjqnNLSK2Xb/ghRWdda9pNi2251K1iYdVaUZH4UtrrulXzBbXUbWVj0VZRk/hWnMr2ucfsanLzcrt6GhRRRTMgooooAKKKKACiiigAooooAK8J+LP/I6n/r2T+te7V4f8Xoyni6B/79qp/IkVhiPgPXyR/wC1fJnA0UUVwn2AUUUUDFooopDClpKWgYUtJS0ALSUtJSKClpKWgAooooGLRRRQMKKKKQxaKKKBhS0lLQMKKKKQwooooGFAooFAxaKKKQxaSlpKBi0UUUDCiiikMWkpaSgYtFFFAwooopDCiiigYUUUUAFei+DfiPJYeXp+tu0trwsdyeWj9m9R7151RVwqSg7xObF4Oji6fs6quvxXofUMM0c8KzQyLJG4yrqcgin14L4R8bXnhqYQybrjTmPzwk8p7r6fSvbtM1Sz1ixjvLGdZYXHUdQfQjsa9OlWjUXmfn+ZZXVwM9dYvZ/5+ZcooorY8wKKKKACiiigArP1vSodb0e50+cfLMmA391ux/A1oUEgDJOMUmk1ZlQnKElKOjRxHw68LS6DYXFzex7b2dymD/CinA/M8/lXS3+v6RpZxfajbQMP4WkG78uteXeOPiRc3VzLpuhzGG1QlJLlD80h77T2Hv3rzgkuxdyWYnJLHJNcjrRprlh0PpY5XWx03iMTKzl0X9aH0XD418NXEgSPWbUsePmYr/OtyOWOaMSROro3RlOQfxr5Zx7VtaB4n1Pw5dLLZTsYs/PA5yjj6dj7iiOK195FVuHVy3oz18/8z6Lmmit4HmmdY4o1LO7HAAHevJfEfxTu55nt9CAgtwcfaHXLv7gHgCk8e+NotZ0SwtNPcrHdJ5tyueVx0Q/jz+Vec1Neu78sGb5Nk0FH22Jjd9E+nqak3iLW7iQvLq14zH/psavab438Q6ZIDFqUsqD/AJZznep/P+lc9RXIpyTumfSywtCUeWUFb0R7r4R8eWfiTFrMottQAz5RPyye6n+ldfXzBb3E1pcxXFu5jmiYOjjqCK+jdA1QazoNnqAGDNGCw9G6EfnXoYes5q0tz4rPMqhhJKrS+GXTszSooorpPnwooooAKKKKACvFPi7cCTxRbQg/6q1GfxY/4V7XXzz4/vRfeNtScHKxuIR/wEYP65rnxLtCx7eQw5sU5dkzm6KKK4D7JC0UUUFIWiiikNC0UUUikFLSVZsdPvNSmENlazXEh7RoTRuNyUVd7FejvXoGj/CjVbvbJqc8dlGeqL88n+Ar0HRfAmg6JteK0E84/wCW1x85z7dhW8MNOW+h5OJzzC0NIvmfl/n/AMOePaL4M1zXCrW1m0cB/wCW0/yL+Hc/hXpGifCvS7HbLqcjX0w52fdjH4dT+Nd8OBgUV1Qw0I76nzmLz3FV9IvlXlv9/wDwxFBbw2sKw28SRRqMBEUAD8KlooroPGbbd2FFFFAgrmPiHG0ngTVAv8MYY/QMCa6eqeq2K6npN3YtwLiJo8/UYqZK8WjWhNU6sZvo0fLwpwqS7tJ7C8ms7lCk8LlHUjoRUYry2fosGmrocKdTRTqlm8R1KKSlFSzaIdqKO1FSanVfDqwa+8Z2jAZS2DTMfTAwP1Ne9VwXwu0A6dor6lOmJ73BQEciMdPz6/lXe16mGhy09ep+e59ilXxjUdo6f5/iFFFFdB4oUjosiMjqGVhgqRkEUtFAHlXjL4Y/6zUPD6c/eksx/NP8K8sZGjdkdSrqcMrDBBr6orj/ABf4BsvEiNc2+221IDiUD5ZPZh/WuWrh76xPosuzpwtTxGq7/wCZ4PS1Yv8AT7rS76WyvITFcRHDKf5j1FV64XofXQkpK62FpaSlqWbIKWkpaRogr1z4b+DTZxrrmox4uJF/0aNh/q1P8R9z/Ksj4feBzfyR6zqkWLVTughYf60/3j/s/wA69frtw1D7cj5TPs3VnhaL/wAT/T/P7goooruPkBk0scELzTOqRoCzMxwAB3ryLxR8VbmeWS10DEUA4N0y5Z/90HoPerHxa8SyCSPw/bSFVKiS6IPX+6v9fyryoVx16zvyxPpspyuEoKvWV77L9S3dahe30plu7uedz1MkhNQoSpypKn1BxTaUVyM+nhFJWR0uh+ONd0N1Ed21xbjrBOSy49j1H4V7JoPi7Ttd0WTUUfyvIUm4ic8xYGfy96+d6tWt9c2kdxHBKyJcR+VKB/Euc4/StadeUN9jzsdlFHFK8Vyy7/5mj4l1+48R61NfTEiPO2CPsidh9e5rIpKWueTbd2ezRpxpwUIKyQUUUVJuKjMjq6MVdTlWBwQfUV714D8SN4i0ANcNm8tz5cx/vejfiP614JXoXwjuWTX722z8stuGx7g//Xrow03GpbueLn+GjWwcpveOq/U9iooor1D89CoriCK6tpbeZA8UqFHU9wRg1LRQNNp3R8z63pj6Prd5p75/cSlVPqvUH8sVRFd18WLQQeK4bgDH2i3BP1UkVxdpaXF/dR2trC808hwqIMk15NSPLNxR+k4Ov7XDQqy6rX9SIAkgAEknAA716n4H+HRDR6rrkXI+aG0YdPRn/wAPzrX8GfDy30TZf6mEn1Dqq9Uh+nqfeu7rqo4a3vTPnc1zznTo4Z6dX/l/mIOOKWiiuw+XCiiigDzD4yaeX0/TtRUf6qRonPswyP5H868gr6U8WaP/AG54YvrADMjx7o/98cj9RXzWQVJVgQwOCD2NcOIjaV+59dklZTw/J1i/zClpKWuc9tBThTacKBoO9LSd6WkUFLSUtBSFooopFIXtRR2opFIKWkpaChaSiikUhaBRQKRQUtJS0FIKKKKRSCiiigAooooAKKKKAFVGkdUQZZiFUepNfSmiaeulaHZWKj/URKp9zjn9c14f4C0v+1fF9mjLmKA+fJ6fL0/XFe/134OOjkfHcT4i84UF01f6BRRRXafKBRRRQAUh+6celLRQB8q3Gftc+evmNn8zUdXdZtza67qFuwwY7h1/8eNUq8p7n6LB3imhaKKKRohaWkpaCkLRRRSKFoFFApFIWlpKWgaCiiikWgpaSlpDQveijvRQWgo70Ud6RSFooooKCiiikMKKKKACiiigAooooA9B+Etp5viG7uiOILfaD7sf/rV7HXnfwjs/L0S9vCOZ59oPso/xNeiV6uGVqaPzvPavtMdPysvw/wAwooorc8cKKKKACiiigAooooAKKKKAMDxh4fTxH4entMD7Qg8yBvRx0/Pp+NfO7K0bsjqVdSVYHsRX1PXhXxN0QaV4oa5iXEF8vmjHQP0YfyP41yYqGnMj6Xh/F2k8PLrqv1OMpaSlriPrEHeijvRSKQUtJS0DQpooNFIpBRRRSKCnojySLHGjO7HCqoySfarOmaXe6xepZ2EDTTP2HQD1J7CvavCHgSz8OItzPtudRYcykfLH7L/jWtKjKo9Njz8wzOjgoe9rLov62RieC/hwtqY9S1yMPOPmitTyE929T7V6V0oor06dOMFZHwOMxlbF1PaVX/kvQgvLy30+zlu7qQRwRLudz2FeJ+KPiHqOtyvBZSPZ2HQKhw8g9WP9BXQfF3WJF+x6PGxCOPPmA784UfzNeW1x4ms78iPpsiyyn7NYiqrt7eX/AARepJPJPUmgcEFeCOhHBoorjPq1sd74L+IN1pt1FYatM09g5CrK5y0Ppz3WvZgQyhlIIIyCO9fLle4fDTWn1XwyLeZy09k3lEk8leq/px+Fd2FqtvkZ8jxDlsIR+tUlbv8A5naUUUV2nyQUUUUAFFFFABRRRQAV5B8Zrci/0q6xw0bx5+hB/rXr9eefF+y87wxbXQHNvcDP0YY/nisqyvBnoZVPkxcPPT7zxSiiivPPtwooooGLRRRSGFLSUtAwpaSloAWkpaSkUFLSUtABRRRQMWiiigYUUUUhi0UUUDClpKWgYUUUUhhRRRQMKBRQKBi0UUUhi0lLSUDFooooGFFFFIYtJS0lAxaKKKBhRRRSGFFFFAwooooAKKKKACtfw94kv/Dd8LiykzGx/ewsflkHv7+9ZFFNNp3RFSnCrBwmrpn0X4d8S2HiWx+0Wb4kX/WwsfmjPv7e9bNfM+mapeaPfx3tjM0UyHqOjD0I7ivcPCPjSz8T2/lnEGoIMyQE9fdfUfyr0qGIU9JbnwubZLPCN1aWsPxXr5eZ1FFFFdJ4AUUUUAFcn8R9Xk0jwddNCxWa4IgVh1G7qfyzXWV598X4mfwpbyD7sd0u78QRUVG1B2OvARjPEwjLa54mOlOFNFOFeWfoERwp1NFOpM2iLS0nalqTaItFFFSahXunwyLHwTb7uglkA+m6vC+gr6H8Hae2meEtOtnGJPKDuPduT/OuvCL32z5ziaaWFjHq5fkmbtFFFeifDBRRRQAUUUUAV7+7Sw0+4u5CAkMbSHPsM18wXE73VxLcSHLyuXY+5Oa9t+KmrfYfCv2NGxLfOI8f7A5b+g/GvDu1cOKleSR9ZkFDloyqv7T/ACFooFFcx9ChaKTNauneG9a1Uj7FplxIp/jKbV/M0Wb2FKcYK83ZGZRXoem/CPVbja2oXkFovdU/eN/hXY6Z8MPD1htaeKS9kHedvl/75HFbRw835Hm1s6wlLRS5n5f1Y8StbO6vpRFaW8s8h/hiQsf0rr9K+F2v3+17oRWMR7ynLf8AfI/rXtdrZWtlEIrW3igQfwxoFH6VPW8cLFfEzx6/EVWWlGKj66v/ACOG0n4WaHY7XvDLfyj/AJ6Han/fI/rXZWtnbWUIhtYIoYx0WNQo/Sp6K6IwjHZHi18XXru9WTYUUUVRzhRRRQAUUUUAFFFFABRRRQBxnjbwFb+JozeWpWDU0XAc/dlHo39DXiF9YXWmXklpewPDPGcMjD9R6j3r6irC8TeFNO8T2XlXabJ0H7q4QfMh/qPauerQUtY7nt5bm0sPanV1j+K/4B85inVq+IPDl/4a1A2l6gw3MUq/dkHqP8Kyq8+SadmfZ0akakVODumOpRSUoqWdMQ7V1HgjwtJ4k1cGVSLC3Iadv73og+v8qzvD3h688SamtnaLhRzLKR8sa+p/oK9+0bR7TQtMisLNNscY5J6se5Pua3w9Hnd3seNnWarC0/ZU377/AAXf/IuoixoqIoVVGAB0Ap1FFemfAhRRRQAUUUUAFFFFAHG/EDwkniDSmurZB/aNqpaMjrIvdT/T3rwrvzxX1PXgHxB0ddH8W3CxrtguR58YA4Geo/PNcWKp/bR9Vw9jG74aXqv1RzFLSUo64HWuJn1iCvQvAngF9UePVNWjK2IO6KFhgze5/wBn+dXPBHw6aUx6prkRWPhobVhy3oX9vavVgAoAUAADAA7V2UMPf3pnzOb54op0MM9er/y/zBVVFCqoVVGAAMAClooruPjwooooA+bPGNy114y1aVjnFyyD6LwP5Viit7xvbG08batHjG6cyD6Nz/WsEV5c/iZ+hYW3sYW7L8h9KKSlFQdiFpwptOFSzVC0tJS1JqgooopGgV3/AMJIS/iO7lx8sdtyfqwrgK9j+E+lm20K41F1w13JhM/3V4/nn8q3w8b1EeTnlZU8DO/XT+vkeg0UUV6p+chRRRQB5/4/8Lah4m1nS47JAqIjiWd/uxjI/M+1dF4a8J6b4ZtdlqnmXDD95cOPnb/Ae1b1FZqnFScup2Tx1aVCOHvaK/H1CiiitDjCiiigAooooAK8E+JXh06L4ke6hTFpfEyJgcK/8S/1/Gve6w/Fnh6LxLoE9i+BNjfA5/hcdPw7VlVhzxsd+W4v6tXUn8L0Z83UVJPby2tzLbzoUmiYo6nqCOtR15x9ynfVBThTacKCkHelpO9LSKClpKWgpC0UUUikL2oo7UUikFLSUtBQUUUUikLQKKBSKClpKWgpBRRRSKQUUUUAFFFFABRRT4YZLieOCJd0kjBFHqScUwbtqz1r4S6T5Gl3WqOvzXD+XGf9lev6/wAq9Hqjo2nR6To1pYR/dgjCn3Pc/nV6vXpQ5IKJ+X4/E/WcTOr0b09OgUUUVocYUVBd3lvYWr3N1MkMMYyzucAVwOpfFvToJGTT7Ka6A/5aOdin6d6idSMPiZ1YbA4jFO1GN/y+89ForyuD4wv5n+kaONn/AEzm5/UV2vh/xjo/iIbLSfZcAZMEvyv+Hr+FTCtTm7Jm2IyrF4ePNUhp33/I8d+Jenmw8b3bYwlyFnU/UYP6g1yVey/F7RTdaPbatEuXtG2SY/uN/gcfnXjVcdaPLNn1GWVlWw0X1Wn3C0UUVkeihaWkpaCkLRRRSKFoFFApFIWlpKWgaCiiikWgpaSlpDQveijvRQWgo70Ud6RSFooooKCiiikMKKKKACiiigAo7UVYsLVr7ULa0QfNNKsY/E4p7ik1FXZ734Hsf7P8HadERhnj81vq3P8ALFdDTIYlhgjiQYVFCqPYcU+vZiuWKR+U16rq1ZVH1bYUUUVRkFFFFABRRRQAUUUUAFFFFABXEfFLSxfeE2ulXMllIJQf9k8N/P8ASu3qpqdot/pV3aMARNEyc+4qZx5otHRhazo141F0Z8xUtDIY3aNvvISp/CivJP0lB3oo70UikFLSUtA0KaBQaVFZ3VEUszHAVRkk0ihK6Pwv4N1HxNMGiUwWSn57lxx9F9TXU+Evhi8/l32vqUj+8loDy3++e30r1WGCK2hSGCNY4kGFRBgAV1UsM5azPncyz6FK9PDay79F/n+RnaF4e07w7ZC2sIQuf9ZK3LyH1JrULBVLMQABkk9qZPPFa28k87qkUalndjwAOprwjxl49vPEdzJbWkj2+lqcKinDS+7f4V1znGlE+bwuErY+q236tnqmpfEHw1pkhik1ATSA4KwKXx+I4qnbfFHwzcSBGnnhycbpYTj9M14QKcK5Xipn0UOH8Ny2bd/68jtPibcw3nimK5t5kmgktUKOjZBGWrjqaOlOrmnLmk2e7haKoUo0k720FoooqDsQV6H8I7kprt7bZ+WW3DY91P8A9evPK7j4VA/8JexHQWr5/Na1oO1RHn5vFSwNRPse2UUUV6x+aBRRRQAUUUUAFFFFABWH4x046p4R1K1VcuYS6D/aXkfyrcpCAylSMgjBFJq6sXTm4TU101PlEcilrV8S6YdH8S6hYkYWOYlPdTyP0NZVeW1Z2P0KE1OKktmFFFFBYtFFFIYUtJS0DClpKWgBaSlpKRQUtJS0AFFFFAxaKKKBhRRRSGLRRRQMKWkpaBhRRRSGFFFFAwoFFAoGLRRRSGLSUtJQMWiiigYUUUUhi0lLSUDFooooGFFFFIYUUUUDCiiigAooooAKKKKACpbe5ns7mO4tpWimjO5HQ4INRUUwaTVme2+CvHsOvKlhqBSHUgOD0Wb3Hofau3r5cVmR1dGKspyrA4INeueB/iEt95el6zIFuvuxXB4Evs3o38676GJv7sz4zOMjdO9fDL3eq7enl+R6PRRRXYfLBXP+NtIfW/CV9Zwrum2eZEPVl5xXQUUmrqxdOo6c1OO61PlLBBwQQQcEHtThXrnjz4cteSy6vokY89vmntRxvP8AeX39u9eSsrI7I6lWU4ZWGCD6GvNqQcHZn3uCxdPFQ54P1XYBTqaKdWTPRiL2paTtS1JtEWiinxxvNKkUSM8jkKqqMkk9BSNdkbvgzQW1/wASW8BUm3iPmzn/AGQen4nivoMAAYAwB2rmvBPhhfDWiqkgBvZ8POw7Hsv0FdNXqYelyR13Z+d51j1i8R7nwx0X6sKKKK3PHCiiigAoooPIxQB4J8Sdb/tjxZJBC26CyHkpjnLfxH8+PwrFsfDOualj7JpV1Ip/iMZVfzPFfQ1poelWBLWun20TEklljG4k+/Wr9crw/M7yZ9BDO1RpKlRht3/r9TxGw+E2vXODdy21op7Ft7fkK6rT/hDpEGGvry5umHVVwi/416JRWkaEF0OOrnGLqfat6f1cxtO8KaFpWDaaXbow/jZdzfmcmtgAAYAwKWitUktjzp1J1Hebu/MKKKKZAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAGN4n8P2/iPRJrKUASY3QyY5R+x/xr50mhktp5IJlKyRsUdT2I4NfUleCfEiyWy8bXhQYWdVm/Ejn9RXHioKykfT8OYmXPKg9t0crWz4c8NX/iW/FvaJtiU/vZ2Hyxj+p9q1PCPgS+8RutxPuttOB5lI+aT2Uf1r2zTNLs9HsY7OxgWGFBwB1J9Se5rKjh3PWWx6WZ51DCp06Os/wX/B8vvK+haDY+HtOSzso8Dq8h+9I3qTWpRRXoJJKyPialSVSTnN3bCiiimQFFFFABRRRQAUUUUAFeXfGK0Bg0u8A+YM8RPtgEf1r1GuT8e+HbzxLp1lZ2ewMtyGd3PCLg5Pv9KyrRcoNI78rrRo4uE5Oy6/ceFWtrPe3MdtawvNNIcKiDJNex+DPh3BpHl6hqoSe/6pH1SH/E+9b3hnwjpvhi2226eZdMP3lw4+ZvYeg9q6CsqOHUdZbno5lncq96VDSPfq/8kFFFFdR8+FFFFABRRRQB458XdDkh1SDWo0JgnQRSkD7rjpn6j+VeaivqS/sLbU7GazvIllglXa6mvCPGHga98MTtPGGuNNY/JMByns/p9e9cVek0+ZH1eT5hCcFQm7SW3mcrSikpRXKfRoWnCm04VLNULS0lLUmqCiiikaF3SdNn1jVLfT7cEyTOFz/dHc/gK+jtPsodN0+3soFxFAgRR9K4r4beFDpNgdVvI8Xl0v7tWHMcf+Jrvq9PDUuSPM92fBZ/mCxNb2UH7sfxf9aBRRRXSeAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAeTfFbwoQw8Q2cfBwt2qjp6P8A0P4V5XX1TPDFcQSQTRrJFIpV0YZBB6ivAfG/g6fwvqJkiVn02Zv3Mn9z/Yb39PWuLEUrPmR9Vk2PU4/V6j1W3mu3yOUpwpKUVzH0CDvS0nelpFBS0lLQUhaKKKRSF7UUdqKRSClpKWgoKKKKRSFoFFApFBS0lLQUgooopFIKKKKACiiigArtPhlo39peJxdyLmGxXzDn++eF/qfwri+1ejySy+DPhchjzHqOrP8Ae6FFI/ov862oxTld7I8zNqso0PZQ+Kfur57v7jW8XfFCLTLiSw0ZI7m4Q7ZJ25RD6D+8f0rzq78a+JL1y0ur3K5/hibYPyFc8KfWk6spPc48Ll2HoRsopvuzo7Dxx4ksJFaPVZ5QP4Jz5gP516l4N+IFv4jf7FeRrbagBlVB+SUd9vv7V4YKkhmkt5kmhkaORDlXU4INKFaUGVi8qw+Jg1ypS6Nf1qdb4+8Uya/rMltDIf7PtWKRqDw7Dqx/pXJUnfNLWM5OTuz08LQhQpqlBaIKfFLJbzJNDI0csZ3I6nBU+1MoqTqtdWZ7r4T1mLxp4Umt79Q0wUwXI/vZHDfiP1FeI67o8+ga1c6bcA7om+Vv76Ho34iu/wDhBMw1jUYP4GgV8e4bH9a6j4i+EP8AhIdLF5aJnUbVSUA6yp3X69xXfZ1aSl1R8Y6kMvzGdHaErfK54RRQQQSCCCDgg9qK5T6BC0tJS0FIWiiikULQKKBSKQtLSUtA0FFFFItBS0lLSGhe9FHeigtBR3oo70ikLRRRQUFFFFIYUUUUAFFFFABXW/DbT/t3jK3cjKWqNMfr0H6muSr1r4Rad5dhf6ky8yyCJD7Lyf1IrahHmqJHmZxX9jgpy6tW+/Q9KopksscELzSuqRoCzMxwAB3rx7xX8Uru7mktNBY29sDg3OPnk/3fQfrXpzqRgrs+BwmCq4qXLTXz6Hr811b2wzPPFEPV3C/zqKPU7CZtsV9bO3osqk/zr5jnuZ7qUy3E8k0h6tI5Y/rTBwcjiuZ4ryPdjw6mtamvp/wT6por530TxnrmhSL9nvHlgB5gnJZCP5j8K9n8K+LrHxRZl4f3V1GP3tux5X3HqPetqdeM9Op5mOymvhFzvWPdfqdDRSZwM1xuu/ErRtHle3g331wvBWEjaD6Fv8M1pKcYq8mcOHw1bES5aUbs7OivJX+MF5v+TSYAv+1Kc1o6f8XbORwmoadLAD1kiYOB+HBrJYmm+p6E8ix8Y83Jf0a/zPSaKpaZq1hrFqLnT7qOeM9Sp5HsR1FXa2TT1R5Uoyg+WSs0FFFFMk+bPEtsLTxRqkAGAly+PpnIrLrpPH8fl+OdUHq6t+aiubryZq0mj9KwsuahCXdL8g70Ud6Kg6UFLSorO6oilnY4VVGSTXovhb4X3F5su9cLW8B5Fsp+dv8AeP8ACP1qoQlN2ic+JxdHCw56rt+b9Dj9D8Oal4iuvJ0+AsoPzytwifU/0r2fwt4G03w2izEC5viPmncfd9lHb+ddDZWNrp1qlrZwJBCg+VEGBViu+lh4w1erPjcwzmtirwh7sO3V+v8AkFFFFdB4x538XdXez0C306JirXsn7zH9xeSPxOK8WHWvUfjPG/2vSJcfJskXPvlTXlw6159d3mz7XJoRjhItdb/mPFOFNFOFc7PZiKKdTRTqlm8RaKKKRsgr0X4RW5fW7+4xxHAFz9W/+tXnVew/CSx8nQbu9I5uJ9qn2UY/mTW2GV6iPJz2r7PAz87L8T0OiiivVPzoKKKKACiiigAooooAKKKKAPIPjDo/l3llrMa/LKvkSkf3hyp/LI/CvMK+lfFWir4g8OXmnkDzHTdEfRxyP1r5rdHjdo5FKuhKsp7Eda4cRG0r9z7DJcR7XD8j3j+XQSiiisD2BaKKKQwpaSloGFLSUtAC0lLSUigpaSloAKKKKBi0UUUDCiiikMWiiigYUtJS0DCiiikMKKKKBhQKKBQMWiiikMWkpaSgYtFFFAwooopDFpKWkoGLRRRQMKKKKQwooooGFFFFABRRRQAUUUUAFFFFABRRRQB6h4G+IZXytJ1uXjhYLpj+Suf616mDkZHQ18uV6L4F+IDWBj0rWJS1r92G4Y5MXs3+z79q7qGIt7sz5POcj5r18Mteq/Vf5Hr9FIrKyhlIKkZBB4Ipa7j48K4zxj4BtPEaNd2u221IDiQD5ZfZv8a7OiplFSVmbUK9ShNTpuzPmC/0670q9ks76BoZ4zgqw/Ueo96r17/428KQ+JtJbYqrfwAtBJ3P+yfY14CyNG7I6lXUkMD1BFedWpOmz7rLMfHGU77SW6DtS0lOGSQAMk1gevEK9d+Hfgk2CprWpx4umGbeJh/qwf4j7n9Kg8B/D8wmLV9Zi/efegtmH3f9ph6+gr06u3D4e3vyPlM7zlSTw2HenV/ov1Ciiiu0+TCiiigAooooAKKKKACiiigAooooAKK5XxD4/wBE8PSNbvI1zdr1ggwSv1PQVw918YdSdj9k0y2iXt5jFz+mKylWhHRs9ChlmKrrmhHTz0PYqK8ctvi/qqOPtOnWkq99hZD/AFrtvD3xC0bXpFty7Wl23Aimxhj7N0NEa0JOyY6+VYuhHmlG68tTraZLLHDE0srqkajLMxwAKJZUgheWVwkaKWZj0AHU14R4z8Z3PiS9eGF2j0yNsRxg48z/AGm/woq1VTVwy7LqmNqcsdEt2ejan8TvD9g7RwPNeuvB8hfl/wC+j/SskfGCz3c6RcbfXzRn+VeS0VwvFVGz66nw9gYxtJN/P/Kx7tpPxH8P6pIsTTvaTNwFuBgE/wC90rrVYMoZSCCMgg9a+XK7PwX45udBuY7O9kabTHO0hjkw+49vataWLu7TPOzDhxRg6mFe3R/oe40U1HWSNZEYMjAFWHQg06u4+RCiiigAooooAKKKKACiiigArmtS8F6frHiVdX1DMyRxKiW+PlJBPLev0rbutSsbEE3d5bwY/wCekgX+dYV18QvC9oSG1SORh2iUt/SonyfaOrDRxKfNQTu9NEdMirGioihVUYCgYAFLXA3Hxb0CP/Uw3k30jC/zNZ8nxks/+WWkXB/35VFS61NdTaOV4yX/AC7Z6dRXlDfGR8/Loox7z/8A1qZ/wuS4/wCgNF/3/P8AhS+sU+5oslxv8n4r/M9aoryhfjI2fm0UfhP/APWqzH8Y7T/lro84/wB2VTR7en3E8nxq+x+K/wAz06ivP4fi5ob/AOttb2L/AIAG/ka0rf4leF7jGb9oc/8APWJhVKrB9TGWXYuO9N/cddRWVa+JtDvcfZ9VtHz0HmgH9a00dJFDIysp6FTkVaaexyzpzhpJWHUUUUyAooooAKKKKACiiigAooooAKKKKACmTRRzwvFNGskbjayMMgj3p9FAbHj3jT4ayWPmajoUbSW33pLUctH7r6j2rzcV9U15p8QvAcVzby6zpMIS5QF54UHEg7sB6/zrjrUPtRPp8rzltqjiH6P/AD/zPIqcKbThXEz6xC0tJS1Jqgr0D4d+Czqlwmr6jF/oUTZhjYf61h3/AN0frVPwP4Hl8QTrfXyMmmIe/BmPoPb1Ne2xRRwRJFEipGgCqqjAAHauvD0Ob35bHzmd5wqSeHoP3nu+3l6/kP7UUUV6B8UFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABVe9srbUbOS0u4UmgkGGRxkGrFFA02ndHiXiv4Y32lO93o6vd2XUxDmSMf8Asw/WuBwQSpBBHBBHIr6rrndd8E6H4gy9zaiO4P8Ay3h+V/x9fxrlqYa+sT6HB564rlxCv5rf5nzt3pa9B1j4S6taFpNMnjvYuyN8j/4GuKv9K1DS5DHf2U9uw/56IQPz6GuWVOUd0fRUMXQrr93JP8/uKdLSUtQdSFooopFIXtRR2opFIKWkpaChaSiikUhaBRQKRQUtJS0FIKKKKRSCiiigAooooA2vCejHXvElpZEExbvMmPoi8n/D8a7T4zIVg0YKMRK0gAHQcLWp8KtD+x6PLq0qYlvDiPI6Rj/E1P8AFfTJL7wmLmJSzWcokYD+4eD/AENd9Onai33Pj8Zj1UzWEb+7DT5vf8dPkeGin0wU+uU9+I4UopBSipZvEdS0lLUs2iFFFKFZmCqCWJwAO5pGh6f8H7JvM1O/I+XCwqfXuf6V6pWF4P0T+wPDVrZuP35HmTf756/lwPwrdr16MOSCR+Z5piVicXOpHbZfLQ8m+JngnYZNf0yL5TzdxKOn+2B/P868rFfVjKHUqwDKRggjgivEPiB4EfQ7h9T02Mtpshy6KM+QT/7L/KsK9K3vRPYyfMuZLD1Xr0f6f5HB0tIKWuU+jQtFFFIoWgUUCkUhaWkpaBoKKKKRaClpKWkNC96KO9FBaCjvRR3pFIWiiigoKKKKQwooooAKKKKADtX0T4Q03+yfCun2pXDiIPJ/vNyf514Z4Y006v4lsLLGVeUM/wDuryf5V9GgADAGAK7sHHeR8lxRiNIUF6v8l+p5b8XPEUkSQaDbuV81fNucHqv8K/1/CvJRXSfEC5a68c6oxORG4jX2AA/+vXNis6suabOzLaKo4aCXVX+8dThTacKyPSQ6tHQ9Xn0PWLfULdiGib5gP4l7g/hWdQKV2ndGkoRnFxkrpnpfj/x413/xKdIm2wFQbiZDy2RnaD6etebCiloqTc5XZGCwlPC0lTpr/ghRRRWZ3Gho2tX2g6gt5YTFHH3l/hcehHevfvDmv23iPSI763+Un5ZYyeY27ivnGu7+FeqPa+JHsC37m7jPH+2vIP5Zrpw1Vxly9GeDn2Xwr0HWivejr6rqe00UUV6Z8CeAfEb/AJHrUP8AgH/oArl66Lx7J5njjVD6SBfyUVj6dpt7q10LawtpLiY/woOnuT2FeVPWbt3P0bCNQwsHJ2tFfkVK6Dw74Q1XxLKPssPl2wPzXMgwg+nqfpXf+GfhXbWuy61x1uZhyLdD+7X6n+L+VejRRRwxLFEipGowqqMACt6eGb1meRjs/hC8MNq+/T5dznfDXgjSvDaCSKP7ReY+a5lHP/AR2FdLRRXbGKirI+VrVqlafPUd2FFFFMyCiiigDlvH/h1vEXhqSKBc3dufOgH94jqv4ivnzBVirAhgcEHqDX1ZXmHj34dS311Jq+iRhpn5nthxvP8AeX39RXNXpOXvI9/JswjS/c1XZPZnklOFPuLW4s5jFdQSQSA8rIpU/rTBXCz6+DTV0KKdTRTqhm8RaKKKRsg57de1fRnhbTf7J8M6fZkYdIgX/wB48n9TXiXgzSf7Z8VWVsVzEj+bL/urz+pwK+ha7cHHeR8jxRidYUF6v8l+oUUUV3HyQUUUUAFFFFABRRRQAUUUUAFeHfFLw4dL10apAmLW+OWwOFl7/n1/Ovcay/EOiW/iHRLjTrjgSLlH7o46Gs6sOeNjuy/F/Vq6m9no/Q+ZaKs6hYXGl6hPY3aFJ4HKOP6/Q1Wrzj7lNNXQtFFFIoKWkpaBhS0lLQAtJS0lIoKWkpaACiiigYtFFFAwooopDFooooGFLSUtAwooopDCiiigYUCigUDFooopDFpKWkoGLRRRQMKKKKQxaSlpKBi0UUUDCiiikMKKKKBhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAHe+BvHr6O0emapIz6eTiOU8mH/wCx/lXssciSxrJGwZGGVYHIIr5drufA3juTQ5E07UXZ9NY4VzyYD/8AE+3auzD4jl92Wx8xnOSKrevh173Vd/Nef5+p7XRTI5UmiWWJ1eNxlWU5BHrT69A+K2CvBfiRpi6d4yuGjXEd0onA9zw36j9a96rzj4geF9R8SeJNNjsYsKIGEsz8Ig3dz6+1YYiLlDQ9jJMRGjibzdk07/meS21tPd3EdvbRPLNIcIiDJJr2LwX8PItI8vUNVVJr/qkXVIf8W963fDHg/TvDFv8AuF867YYkuXHzH2HoPauiqKOHUfeludOZ55KunSoaR79X/kgoqC7vLewtZLm6mSGCMZZ3OAK8q8SfFaeZnttATyo+hupFyzf7q9vxredSMFqeVhMDWxUrU1p36HqtzeWtlEZLq4igQfxSOFH61z918QvC9qxVtUSRh2iRm/XGK8Gu7671CczXlzLcSHq0jk1BXJLFvoj6Gjw5TS/ezb9NP8z3UfFDwwTj7TOPcwmtC08deGr0hY9WhVj2lyn86+eqKlYqZvLh3Cte7Jr7v8j6kiminjEkMiSIejIwINPr5n07WNR0iUS6fezW7DsjcH6joa9J8N/FVJWS216NY2PAuoh8v/Al7fUVvDFRlo9DycXw/iKK5qT51+P3Hp9FMhmjuIUmhkWSNxlXU5BFPrpPBatowooooAK85+JPjaTSV/sbTZNt5KuZpV6xKegHuf0r0OWQRRPI33UUsfwr5g1XUJNV1a7v5WJeeVn57DPA/LFYYibjGy6nsZNhI16rnPVR/MrZJJJJJPJJ6mlpop1eefZxFpw4IIOCOQRTadSNonZXPjy7vPAx0ad3a7Mgjabu8PXk+vauPpBS0Sk5biw+Hp0E1TVru4tFFFQdaCiiikM9y+GepvqHhKOKVi0lpIYcn+71H6Guyrzf4QZ/srUv7v2hf/Qa9Ir16Dbpps/NM2pxp42pGPf89QooorU84KKjmnit4WmmkSONRlndsAfjXn/iD4sabY7odIi+3TDjzD8sQ/qamU4xWp0YfC1sRLlpRuehkhQSSAB1JrmdX8feHdHLJLfCeYf8srcbz+fQfnXi2s+L9c15mF7fP5R6QxfIg/AdfxrDFcssT/Kj3sPkC3ry+S/zPTdT+MF5LuXS9OjhXtJO25vyHFcjqHjPxFqeRcarOEP8ER8tf0rBorCVWct2e1RwGGo/BBfn+Y52aRi0jM7HqWOaSkpazOwKKKKBi0UUUDCiiikMWiiigYmB6Vat7+8s23W13PCfWOQr/Kq1LQDSaszprL4geJrLG3UmmUfwzqH/AF6/rXSWPxgvEIF/pkUo7tC5U/kc15rSVca01szjq5bhKvxU18tPyPdNP+KHhy8ws0s1m57TJx+YzXU2ep2Goxh7O8guFP8AzzkBr5jp8UskDh4ZHjcdGRiCPxFbRxUlujzK3DlGWtKTX4n1JRXgGmfEHxJpm1RfG5jH8FwN/wCvWuz0v4vWsm1NV0+SE95IDvX8jzW8cTB76Hj18ixdLWK5l5f5HplFZOl+JtG1lR9h1CGRj/AW2sPwPNa1bpp6o8mdOdN8s1Z+YUUUUyAooooAKKKKACiiigDwL4g6CuheJ5BCm21uh50QHRc/eH4H+dcsK9g+L9msmiWN5j5oZ9hPsw/xFeQxRyTSrFEjPI5wqKMkn0Ary68eWbSP0LKcQ62EjOW60fyErvvBPw+l1do9R1VGisBykR4ab/Bf51t+DfhqtuY9R15A8w+aO0PIX3b1PtXpYAAAAwB0FbUcN9qZ5eaZ6knRwr16y/y/z+4bFFHBCkUSKkaDaqqMACn0UV3HyLd9WFFFFABRRRQAUUUUAFFFIzKilnYKoGSScAUALRXLaj8Q/DWmuY3vxPIOCtupf9Rx+tZi/Frw8z4MV6q/3jEP8ah1YLqdcMBipq8ab+47yisTSPF2h62QllqEbSn/AJZP8j/ka26pNPVHPUpzpvlmrPzCo5p4beMyTypEg6s7AAfia5zxj4wt/C9koVRNfTA+TDn/AMeb2rxHVdb1LXLkz6hdSTMTkLnCr9B0FYVcRGm7bs9fLslq4xe0k+WPfv6Hu83jTw3A+19Ytsj+6xb+VW7LxFo2pNstNTtpXPRRIAfyPNfN34UDg5HBHQiudYyV9j25cL0HH3Zu/wAj6korw7wv8Q9S0WVIL53vLDoVc5eMeqnv9DXtNje2+o2UV3aSrLBKu5GHeuulWjUWh83mGWVsDK09U9mixRRRWp5wUyWGOdCksaSIequoINPooC9jltT+HvhvU8s1gLeQ/wAdudn6dP0rj9S+D0y7m0zU1f0juFwfzH+Fes0VlKjCW6O+hmeKo/DN289fzPnbUvBPiLSsm402V4x/y0h/eL+lYJBVtrAqw7EYNfU9Zuo+H9J1ZSL7T7eYn+JkG78xzWEsL/Kz16HETWlaH3f5P/M+au1Fey6l8JNIuNzWFzPaOeik71/Xn9a47Uvhf4hsdzW6w3sY7xNhvyP9KwlQqR6Hs0M3wdbadn56f8A4ulqe8sLzT5DHeWs1u46iVCtQViz1IyUldBRRRSLQtAooFIoKWkpaCkFFFFIpBRRRQAVoaHpUut61a6dFnMz4Y/3V7n8qz69b+FOgeRZTa3On7yf93BkdEHU/if5VrSp880jgzPGLCYaVTrsvX+tT0S2t4rS1itoVCxRIERR2AGKfJGksbRyKGRwVZSMgg9qdRXrn5m227s8Q8bfDy40SWTUNLjabTSdzRjloP8V9+1cIK+qSAQQRkHqDXmPjT4aLP5mo6BGEl5aW0HAb3T0PtXHWofaifT5ZnKdqWIfo/wDP/M8mFKKGRo3ZHUq6nDKwwQfQ0CuJn1MR1LSUtSzeIV6D8M/Ch1C+GtXcf+i27fuFI/1knr9B/OsHwf4TuPE+o7TujsYiDPN/7KPc/pXvdpawWNpFa20axwxKFRFHAFdWGo8z53sfP59mqoweGpP3nv5L/Nk1FFFeifDhTZI0ljaORFdGGGVhkEU6igDyTxd8LZEkkvvD67ozy1mTyv8AuHv9K8zmhltpmhnieKVDhkdcEfhX1PWXq/hzSddj2ajZRTHHD4w4+jDmuaph09Ynv4PPJ00oV1zLv1/4J800V6zqfwehYs+lak0fpHcLuH/fQ5rlL74a+J7IkrZpcqP4oJAf0ODXNKjOPQ96jmeEq7TS9dDkqBV650XVbI4utNu4sf3ojVE/IcMCp9GGKyaaPQhKMleLuLS0lLSLQUUUUi0FLSUtIaF70Ud6KC0FHeijvSKQtFFFBQUUUUhhRRRQAUUUdOaAPSvhHpfmX17qrr8sSiGM/wC0eT+mPzr1quc8C6V/ZHhKzhZcSyr50n1bn+WK6OvXoQ5aaR+a5tifrGMnNbLReiPmvxejx+MdXV/vfanP4HkfpWMK9M+LXhx4L+PXoEJhmAjuMD7rjofxHH4V5mK4akXGTTPrMBWjWw8JR7fkOpwptOFZneh1FFFSzZDqWkpaRrEKKKKk0Cup+HUTSeOLDb/AHY/Taa5avTPhHpLPdXmruvyIvkRn1J5b+la0Y81RI8/NqypYKpJ9Vb79D1iiiivXPzM8vHw2udb8S3+pavL9ntJbh2SKM5d1zxk9F4/GvQtL0ew0a1Ftp9rHBGOu0ct7k9SavUVEacY6o68Rjq2ISjN6Lp0CiiirOQKKKKACiiigAooooAKKKKAILmytbxNl1bQzL6SIGH61gXnw/wDDN7ktpkcTH+KElP5cV01FS4xe6NaderS/hya9GecXnwg02TJstQuYD2EgDj+lcdrnw61zRkaZI1vbdeS8AOQPdete8UVlLDU5baHqYfPcZSfvPmXn/mfLdFev+PfAcV5BLq2lQhLtBumhQcSjuQP73868r0vT5tW1S2sIAfMncIPYdz+Arz6lKUJcrPtMFmFHFUXVi7W38j1P4T6L9n0y41eVcPct5cWf7i9T+J/lXo1V7Cyh06wgs4F2xQIEUewqxXqU4ckVE/PcdinisRKs+u3p0CiiirOQKKKKACiiigAooooAKKKKACiiigDz/wCJXg06zZ/2rYR5v7dfnRRzMg7fUdq8Rr6uryP4j+AzE8uuaRDmM/NdQIPunu6j09R+Nctelf3on0WT5io2w9V+j/T/ACPLqKKK4z6cKWkpaBhS0lLQAtJS0lIoKWkpaACiiigYtFFFAwooopDFooooGFLSUtAwooopDCiiigYUCigUDFooopDFpKWkoGLRRRQMKKKKQxaSlpKBi0UUUDCiiikMKKKKBhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQB23gfxzJoMy2F+7Saa5wCeTAfUe3tXtcUsc8SSxOrxuNyspyCK+Xq7bwN45k0CVbC/Zn0124PUwk9x7eorsw+I5fdlsfM51kvtr16C97qu/wDwfz9T26imRSpNEksTq8bjcrKcgin16B8TsFU9U1S00fT5b69lEcEYySepPYD1JqW9vbfT7Ka7upBHBCpZ3PYV8/8Ai/xbdeKdS3tujsoiRBBnoP7x9zWVWqoLzPSy7L5YyfaK3f6B4s8X3vim93SExWSH9zbg8D3b1Nc7RQK86Tcndn3NGlClBQgrJC0tJS1JsgpaSlpFi9qKO1FIpHUeEfGl54ZuRE5afTnP7yEn7vuvofbvXulhf22p2UV5ZyrLBKMqy/5618x12PgHxa/h/VBa3Lk6dcthwekbHow/rXTh67i+WWx4Gc5RGvF16K99b+f/AAT3SikBBAIIIPQilr0T4gq6ihk0y7RfvNC4H5GvlsccHtX1aQGBB6EYNfMWuWL6br9/ZOuDFOwH0zkfpiuTFLZn0fD81ecPRlEU6minVxn1ERadTadSNoiilpBS1LNYi0UUUjVBRRVrTbCbVdSt7CAZknkCD29T+AoSvoEpKKcpbI9l+F9ibTwgszDDXUrSj6dB/Ku1qvY2cWn2EFnCMRwxhF+gFWK9iEeWKifluLr/AFivOr3YVyvirx3pnhlDCT9pvyPlt4z092PYfrXM+OPiX9laTS9BkDTDKy3Y5Ceyep968kd3lkaSR2d2OWZjkk+9Y1a9tInrZfk7qJVK+i7dWbPiDxXq3iSctfXBEIPyW8fEa/h3+prEooFcbbbuz6inTjTiowVkLS0lLSLQUUUUhhS0lLQMKKKKBi0UUUDCiiikMWiiigYUtJS0AFJS0lIoWiiigYUUUUDFUlWDKSGHQg4IrpNI8eeIdH2rHem4hH/LK4G8Y+vUVzdFOMnF3TM6tGnWjy1IprzPYdI+LWnXO2PVLWS0c8GRPnT/ABFd1YanY6pCJrG7iuIz3jYHH+FfMtTWt3c2M4ntJ5YJR0eNiprohipL4tTw8Tw7Qqa0Xyv71/mfT9FeL6L8VdWstsepRJfRD+IfJJ+fQ16LonjfQtd2pBdiK4P/ACxn+Rvw7H8K64V4T2Z87ispxWG1lG67rU6OiiitTzQooooA5nx1ol34g8PCwswpma4RsscBQOpNJ4W8Ead4ZjEigXF8R89w46eyjsK6eio9nHm5up1LGVlQ+rxdo7+oUUUVZyhRRRQAUUUUAFFFFABRRRQBWv7620yxmvbuQRwQqWdj2FeC+LfHGoeJrl40d7fTgf3dupxuHq3qfaup+MGtv5tpokTYTb58wHfso/ma8srixFRt8qPqsmwEI01Xmrt7eQ4U6m06uU+jQ5SVIZSQwOQQcEV6l4C+IEhP9l63PuCoWhuXPPAztY9+OhryztS04VHB3RlisHSxdP2dRej7Gpr+sTa9rdzqExP7xv3a/wB1B0H5VnUlFZttu7O6lCMIqEdEhaKKKk2CvS/hNrTpe3GjSOTFIpmhB/hYfeA+o5/CvNK6b4fyNH4300qfvMyn6FTWtGTjUTODNaMa2DqRl0TfzWp79RRRXrn5kFFVr6/tNNtWub24jghXq8jYFcRffFvQ7eQpa291d4/iVQin6ZqZTjHdnRQwlev/AAotnoFFeeWnxe0eaQLdWV3bqf4uHA/Ku20zVrDWLUXOn3UdxEepQ8j2I7Uo1Iy2Y6+DxFBXqwaRdqOaeG3TfNKka/3nYAfrXJ+OPGieGbZbe2CyajMuUU8iNf7x/oK8W1DVL/Vrhp7+7lnkP99uB9B0FZVcQoOy1Z6eXZHVxcfaSfLH8WfRSa1pcr7E1K0ZvQTL/jV4EEZByD3r5bwB0ArovD/jLV/D0y+TcNNbZ+a3lbKke3ofpWUcYr+8j0MRwxJRvRnd9mrfie/T20F1EYriGOWM9VdQR+tcTrnwt0fUA0mnFrCc8gL80ZP+72/Cuo0LXLTxBpcd/ZsdjcMh6o3cGtOupxhUWup89Tr4nB1Gotxa3X/APnXXfCWr+HXJvbYmDOFuIvmQ/j2/GsOvqOSNJo2jkRXRhhlYZBFeb+K/hfFOHvNBAil6tak/K3+6ex9ulcdTCtawPp8BxBCo1DE6Pv0+fY8loFSTwTWs7wXETxTRnDo4wVNRiuQ+lTTV0FLSUtItBRRRSKQUUUUAaGiaTNrms22nQ53TPhm/uqOp/Kvo20tYrGzhtYFCxQoERR2ArgfhZ4e+yadJrNwmJrobYcj7sY7/AIn+Vei16eFp8seZ9T4LiDHe3xHsov3Yfn1/yCiiiuk8AKKKKAPL/il4URoD4gs4wsiEC6VR94dA/wBR3rygV9P39pHfafcWkqhkmjZCD7jFfMc0Rgnlhb70blD+BxXn4qCjK66n2nD+KlVoulLeP5MSuk8J+ELzxRefLuhsYz++uCP0X1P8q0/B/wAPbrXWS91EPbadnIHR5vp6D3r2ezs7ewtI7W0hSGCMYVEGAKVHDuXvS2LzTO44dOlQd59+i/4JHpmm2mkWEVlZRCKCMYAHf3Pqat0ySRIY2kkdURRksxwB+NcfrPxN8P6WWjhla+mH8NuMr/30eK73KMFrofH06NbEzfKnJs7OkzgZNeKan8Wdbuyy2MMFlGehxvf8zx+lclfa/rGpsTealdTZ7GQ4/IcVhLExWx61HIK8tajUfx/r7z6Judd0myH+k6lax47NKM/lWTN8QPC0JwdXhYj+4C38hXz2Rk5PJ9TRWbxUuiO+HD1FfFNv7l/me9n4neFgcfbpD9IW/wAKF+JvhZjj7c4+sLf4V4J3pan6zM2/1fwvd/ev8j6Eh8f+F5jgavApP98Ff5itW21zSrwf6PqNrJnssq5/KvmagDByOD6imsVLqjOfDtF/DNr7n/kfVGQw4wQarTabY3IInsreTP8AfiBr5ws9b1XTiDZ6jdQ47LKcflXTaf8AFLxFZlRcNBeIOolTa35itFiYv4kcVTIMRDWlNP8AA9SufA3hm6yZNHtwT3QbT+lZNx8KvDk2fKF1AT/cmz/PNU9L+Lek3JVNRtprNz1cfOn6c/pXb6fqthqsPm2F5DcJ/wBM2yR9R1FaJUp7WOKpLMcJ8bkvnp/kee3Pwdtjk2urzL6CWIN/Iisi5+EWsRjNvfWkw9DuU/yr2Wik8PTfQqGd42H2r+qR4Fc/DnxRbZP9niUesUqtWRc+Htas/wDj40q8jx3MRP8AKvpSioeEj0Z20+JK6+OCf3r/ADPlplZH2upVh1DDBor6XvtH03Uk2Xtjbzg/34wT+dchqvwp0a7DPYSTWUh6AHen5Hn9axlhJLbU9LD8R4eelWLj+K/z/A8Xo711Ot+ANd0UNIbf7Xbr/wAtbf5sD3XqK5bv9K5pRcXZo96hXpV481KSa8haKKKk6AooopDCiiigArX8MaUda8SWNlglGkDSY7IOT/KsivUvhHpH/H5rEi9f3ERI/Fj/ACFa0Yc80jhzPE/VsLOp1tZerPUVAVQAAABgAUtFFeufmJWv7G31Kxms7uMSQTKVdT3FfO3inw5ceGNakspctC3zQS4++n+I719JVgeLvDMHifRXtXwtwnz28uPuN/gehrGtT51pueplePeFqWl8L3/zPnMU4VJdWs9jdy2tzGY54WKOh7EVGK85n3MWmrodRRRUs2Q6lpKWkaxCiipbe3muriO3t4mlmkbaiKMljSNG0ldkunafc6rqMFjaIXmmbao9PUn2FfROh6RBoWj2+nwcrEvzNj7zdz+JrC8D+DY/Ddmbi5CyalMv7xhyIx/dH9TXXV6WHo8iu92fBZ5mixdT2VJ+5H8X3/yCiiiuk8AKq32pWWmQ+bfXUNunrI4Ga5Lx348Tw0gsbILLqUi555WEep9/QV4nfaheapdNc31zJcTNyWkbOPp6VhUrqDstz2cBlE8TH2k3yx/FnvEnxI8LRvt/tMP7pGxH8qu2HjTw7qTiO31W38w9Fc7D+tfOdLjPWsPrUux674ew7Wknf5f5H1QCCAQQQehFLXgHhjxzqnhyZYzI11Y5+a3kbOB/snsf0r3PS9TtdY06G+spPMglGQe49QfQiumlWjU23PAx+WVcG7y1i9mXKKwPEni/TPDMI+1OZLlhlLeP7x9z6D3rzPUPiprtzIfsiW9pH2ATe35n/ClUrwhoysHlGKxa5oKy7vQ9rorwiL4k+KImyb2OQejwrj9BXUaL8WkeRYtZtBEDx58GSB9VPP5VMcVTbtsdNbh7G0o8ySl6P/Ox6fRUNrd297bR3NrMk0Mgyrocgipq6DxGmnZhRRRQIKKKKACuX0jwZaaT4rv9Yj27Zx+5jA/1ZP3/AM/611FFS4ptN9DanXqUoyjB2UlZhRRRVGIUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUEAjBGQaKKAPJ/HXw2OZdV0GLOctNaKPzKf4flXlRBBIIIIOCCORX1bXE+L/h1Y+Id95ZlbTUSMlwPkl/3h6+9ctWhfWJ9Dl2cciVLEbdH/meEUtXtW0XUNDvDa6jbPDIOhPKsPUHoao1xtNbn08ZRkuaLugpaSloKFpKWkpFBS0lLQAUUUUDFooooGFFFFIYtFFFAwpaSloGFFFFIYUUUUDCgUUCgYtFFFIYtJS0lAxaKKKBhRRRSGLSUtJQMWiiigYUUUUhhRRRQMKKKKACiiigAooooAKKKKACiiigAooooAKKKKAClpKcitK22NGdvRQSf0oA7fwL45fQ5k07UXL6a5wrHkwH/wCJr2mORJY1kjYOjDKspyCPWvnS18L69egG30m7ZT/EYyo/WvT/AAPa+JtAtJLfV7dRpiIzozTAvDgZwAO3t2rvw1Sa92S0Pj89wWGk3XpTSn1V1r6ef5+pznxX8SNcX6aFbuRDBh7jB+856D8Bz+NebVZ1G8fUdTur2UkvPK0hz7mq1YVJ80mz18Hh1h6Maa6b+vUWgUUCszsQtLSUtBSClpKWkWL2oo7UUikFFFHekWj3L4ba8dX8Oi2nctc2RETEnlk/hP8AT8K7OvC/hpqZsPF8UBbEd4hhYe/Vf1Fe6V6mHnzQ16H57neFWHxb5dpar57/AIhXnHxK8Eyasv8AbOmx7ryJMTRKOZVHQj3H616PRWs4Kaszz8NiJ4eoqkNz5U5BIIII4IPalr23xn8ObfW/Mv8ASwlvqPVl6JN9fQ+9eMXVpcWF1Ja3cLwzxnDo4wRXnVKbg9T7jA46lio3hv1RFTqbTqyPSiKKWkFLUs1iLRRRSNUFes/C3wyYIW166TEkqlLZSOi92/H+Vcf4I8JyeJdUDzKRp8DAzP8A3z/cH1717zHGkUaxxqFRAFVQMAAdq7MLSu+dny/EOZKEfqtN6vfyXb5/kOryb4i+PmZpdE0ebCj5bm4Q9fVFP8zWx8SvGZ0a0/smwkxf3C/vHU8wof6ntXiVa16tvdiedlGWqVsRVWnRfr/kKOlFFFcZ9MFAooFBQtLSUtAIKKKKQwpaSloGFFFFAxaKKKBhRRRSGLRRRQMKWkpaACkpaSkULRRRQMKKKKBi0UUUgFooooGJS/0pKWgo6bQ/Heu6GVRLn7Tbj/ljcHcMex6ivStC+JejaqViuybC4PGJT8hPs3+NeH0VrCvOB5mLyfC4nVxs+6PqNHWRA6MGUjIKnINOr500TxVrGgOPsN23k55gk+ZD+Hb8K9O0H4o6XqG2HU0+wTnjeTmNj9e341208TCWj0PlsZkWJw/vQ9+Plv8Ad/lc72imRyRzRrJE6ujDKspyD+NProPE2CiiigAooooAKKKKACiiigAooooA8B+J5Y+O7vd2ijA+mK5AV3/xetDD4qt7nHy3FsOfdSR/WuArzaqtNn3uXyUsLTa7IdTqbTqyPQQvalpO1LUs1Q6iilpG0QoooqTQK7D4Z2hufGlvJjK28byH8sD9TXH1678JdIMGmXWqyLhrlvLiz/cXqfxP8q2oR5qiPMzmuqOCm3u1b7/+Aej1Wv76DTbCe9uX2QwIXc+wqzXBfFu7eDwgsKEgXFwqN7gZOP0r1Jy5Ytn57hqPtq0afdnlXiXxPe+KNTa5uWKwqSIYAfljX/H1NY9MFPry5Nt3Z+g0acacVCCskOFavh7Xbvw9q0V7auQAwEseeJF7g1lClFTdp3Ru4RqRcJq6Zpa7q0mt63dajLkec+VUn7q9h+AqhSUtS227s3pQjCKjHZBRRRUmx6F8JtReHXbnTyx8q4i3gf7S9/yJr2KvD/hdG0njNGA4jgdj+g/rXuFenhG/ZnwPEcYxxt11SuFFFFdJ4JzHi7wZZ+J7YuAsGoIP3c4HX2b1H8q8M1HTrrSb+WyvYTFPGcFT39x6ivpquZ8ZeEoPE+nfKFjv4gTBLj/x0+xrmr0FNc0dz38oziWGkqVZ3h+X/APAKWpbm2ms7qW2uI2jmiYq6N1BFRV5rPuotNXQUUUUi0FbHhjQ5PEOvW9goIiJ3zMP4UHX/D8ax69w+HHhz+xdCF3OmLy9Ads9UT+Ff6/jW1Cn7Sduh5mbY5YPDOS+J6L17/I7CGGO3gjhiUJHGoVVHQAVJRRXrH5s227sKKKKACiiigArhNG+HFpBq9zqmq7LmR53kigH3EBYkE+p/Su7rE8ReKdM8M2nnX0uZGH7uBOXf6D096icYvWXQ6sNWrxvToby003NhmSKMs7KiKOSTgAVwHiP4qafpxe30hBfXA4MmcRKfr/F+Fec+J/G2q+JpGSWQ29ln5baM8f8CP8AEa5uuapiHtE97B5HGPv4jV9v8zX1rxNq+vyFtQvHdM5ESnai/QCsqkpa5W23dn0MIRhHlgrISlpKWkWFFFFAw70tJ3paBhRRRSGLRRRQMKmtrm4s5lmtZpIZVOQ8bFSPyqGloCyasz0DQvirqdiVi1WIX0I48wfLIP6GvUNE8TaT4hh36fdK7gZaJuHX6ivm81JBPNazrPbyvFKhyro2CPxreniJR31PHxmR4ev71P3ZeW33f5H1HRXlHhb4pspS08QDK9Fu0HI/3h/UV6nBPDcwJPBKksTjKuhyCK7oVIzV0fJYvA1sJLlqr59GSUUUVZyBXLeIvAej6+rS+ULW8PSeEYyf9odDXU0VMoqSszajXq0J89KVmfO3iHwpqfhqfbeRb4GOEuI+Ub/A+xrEr6eubWC8tnt7mFJYZBhkcZBFeO+NPh9LoofUNLV5tP6vH1aH/Ff5VwVsM4+9HY+zyvPYYhqlX0l36P8AyZwdFAorkPowooooAVVZ3VEGWY4UepNfR3hvSV0Tw/Z2AHzRxjzD6ueT+teO/DrR/wC1vFcMjrmCzHnv6ZH3R+f8q93rvwcNHM+O4mxV5xw66av9P68wooortPlAooooA8u+LXh5Gt4degTEiERXGB1U/db8On415OK+lvEFiupeHtQtHGRJAwH1xkfrXzSMjg9Rwa4MTG0r9z7PIcQ6lB05fZ/IdRRRXKz6BDqWkHJwOSTwBXceGfhtqOrlLnUd1lZnnDD9449h2+ppxhKbtEiviqOGhz1ZWRy2k6Rfa3eraWEDSynqR91B6k9hXtvhHwTZ+GYfNfbcag4w85HC+y+g/nW1pOjWGh2a2un26xRjqR95j6k9zV+u+jh1DV7nxmaZ3Uxd6dP3Yfi/X/IKKKK6TwgqrqN7Hpum3N7L9yCNpD74HSrVcn8SZzB4E1DBx5gWP82FTJ2i2a0KftKsYd2keDX99PqeoXF9csWmncuxPv2qAUgpRXls/RIJJWQ6nU2nVJqha7Dwb4zbwxY6lC4MgkTfbxnp5vT8scn6Vx9L2pxk4u6Cth6eIp+zqK6ZYvLy41C8lu7uVpZ5W3O7d/8A61Q0lLWbOqCSVkFFFFI0Op8E+LpvDepLFK7Np0zYljJ+5/tD+te9I6yIrowZWGQR0Ir5cr3v4eX73/gyyaQ5eHdCSe+08fpiu7CVHfkZ8jxLgoJRxMVZ3s/0Z1NFFFdx8iFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQBT1HS7HV7RrW/to54W/hcdPcHsa8t8RfCSeIvcaFP5qdfs0xww+jd/xr16ionTjPc68Ljq+Gf7t6duh8t3tjd6dcG3vbaW3mHVJFwf8A69QV9QX+mWOqQGC/tIbiM9pFzj6elcHrHwi065LSaVdSWbnpHJ86f4iuSeGkvh1PosNntGelZcr+9f5njlJXV6n8OvEmmZP2L7VGP47Zt36da5iaCW2kMc8TxOOqyKVI/OsHFx3R7VKvSqq9OSZHS0lLUmoUUUUDFooooGFFFFIYtFFFAwpaSloGFFFFIYUUUUDCgUUCgYtFFFIYtJS0lAxaKKKBhRRRSGLSUtJQMWiiigYUUUUhhRRRQMKKKKACiiigAooooAKKKKACiitDStE1LWpvK0+zknPdgMKv1PQU0m3ZEznGEeabsjPpQCzBVBLHoAMk16ho3wk4WXWb33MFv/Vj/QV32l+G9H0ZALGwhjYfxldzH8TzXTDCzlvoeFiuIsLS0p++/uX3niOmeB/EOq4aHT3ijP8Ay0n+QfrzXYad8IWOG1LU8eqW6f1P+Fep0V0xwtNb6ngYjiLGVNIWivL/AIJylj8OvDVjgmxNww/iuHLfp0/SuittPs7NQttaQQgdPLjAqzRW8YRjsjyKuKr1v4k2/VhWJ4wuDa+D9VlBwRbMoP14/rW3XLfEZ9ngTUv9pVX/AMeFE3aLHhY81eC81+Z8+jpS0UV5R+ii0CigUikLS0lLQUgpaSlpFi9qKO1FIpBR3oo70i0XtHuDa63YTqcGO4Q/+PCvpftXzHYoZNRtUUZZpkA/76FfTYGFA9BXdg9mfIcUJc1J+v6C0UUV2nyoVzvinwfp/ii1xMoiu0H7q5UfMvsfUe1dFRScVJWZpSqzpTU4OzR80a3od94f1J7G+j2uOUcfddfUGs+voXxl4Zh8S6JJDtAu4gXt5PRvT6HpXz46PFI0cilXQlWU9QR1FebWpezfkfd5Vj1jKV38S3/zEFLSClrBnrxFrb8M+GbzxNqQtoAUgQ5nnI4Qf4+gp3hjwtfeJ74RQAx2yH99cEcIPQep9q930fRrPQtOjsrGPZGnU93Pck9zW9Cg5u72PHzbOI4SPs6es3+Hr/kSaXpdro2nQ2NlGEhiGAO5Pcn1Jqt4i1yDw9odxqM+D5YxGmfvuegrVrw74peIjqmvDTIHza2Jw2Dw0h6/l0/Ou6pJU4aHyGBw8sZiLT16t/13OKv7641K/nvbpy887l3Y+v8AhVeiivPPuEklZC0UUUhhQKKBQULS0lLQCCiiikMKWkpaBhRRRQMWiiigYUUUUhi0UUUDClpKWgApKWkpFC0UUUDCiiigYtFFFIBaKKKBiUtJS0FBRRRSGFLSUtAGvonijV/D8gNhdssWeYX+ZD+Hb8K9S8PfE/TNT2Qakv2C5PG4nMbH69vxrxWlranWnDY8/GZVhsXrNWl3W/8AwT6iR0kRXRgysMhlOQadXzxoPi7WPDrgWlyXt8828vzIf8Pwr1bw78RNJ1vbBcN9ivDx5cp+Vj/st/jXbTxEJ6PRnyOOyPEYa8o+9Huv1R2NFFFdB4oUUUUAFFFFABRRRQByHxC8Lv4k0HNsub61JkhH98d1/H+leBsrI7I6lWU4ZSMEH0r6rrhPGvw8t9f33+nbLfUsZYdEm+vofeuavR5vejue7lOZxofuavw9H2/4B4fTqlu7O50+7ktbuF4Z4zhkcYIqKuFn18WmroXtS0nalqWbIdRRRSNoi0UUoBZgqglicAAck1JoX9E0ifXNXt9Ptwd0rfM39xe5P0FfRljZw6dYwWduu2GFAiD2Fcr4A8J/8I/pn2q6Qf2hcqC+f+Wa9l/xrsq9PDUuSN3uz4DPcxWKrezpv3I/i+/+QVw3xYtDceDGlUZNvOjn2HQ/zruaqanp8Gq6ZcWFyMwzxlG9s963nHmi0eThqvsa0aj6M+XRT60vEHh+98N6o9leIcZzFKB8si9iKza8tpp2Z+g0pxnFSi7pjhSikFKKlnTEdS0lLUM3iFFFXdI0u41rVbfT7VcyTNjPZR3J9gKEm3ZFSnGEXKTskel/CPSWjtbzVpFx5xEMXuByT+f8q9MqppmnwaVplvYWwxFAgRff3/GrdevShyQUT8xzDFfWsTKt0e3p0CiiitDjCiiigDzr4m+Exe2h1uyj/wBJgX9+qj/WJ6/UfyryCvqNlDKVYAqRgg968A8b+HT4d8QSRRqRaT/vYD6Duv4GuDFUre+j7Lh3MHOP1Wo9Vt6dvkc3RRUkMMlxPHBCheWRgiKOpJ6Vxn1N0ldnT+APDZ1/XleZM2VoRJLkcMf4V/H+Qr3np0rE8K+H4/DmhQ2S4Mx+edx/E56/gOlbderQp+zj5n5zm+P+uYhuPwrRf5/MKKKK2PKCiiigAoorivHvjePw1a/Y7Mq+qTL8o6iJf7x/oKmUlFXZtQoTr1FTprVjvGvjy28MxG1tds+puPljz8sQ9W/wrw6/v7vVL2S8vZ3mnkOWdj+g9B7VFNNLczyTzyNJLIxZ3Y5LH1qOvPqVXNn2uBwFPCQstZdWLRRRWZ3i0tJS0hiUtJS0DCiiigYd6Wk70tAwooopDFooooGFLSUtA0BooNFIYV0XhbxjqHhi4AiYzWTHMlsx4Puvoa52inGTi7oirRhWg4VFdM+lND12w8Qaet5YSh16Oh4ZD6EVp1816Hrt94e1Fb2xk2sOHjP3ZF9CK968NeJbLxNpourU7ZF4mhY/NG3+HvXo0ayno9z4jNMqnhHzw1g/w9Taooorc8cKQgMpUgEEYIPelooA8d+IHgb+y2fV9LjP2JjmaFR/qie4/wBn+Vee19RSRpLG0cihkYFWUjIIPavCfHXhJvDep+bbqTp1wSYj/cPdD/SvPxNDl9+Ox9tkWbOsvq9Z+8tn38vU5OiitPw9pL65r1pp6A7ZXzIR2QcsfyrkSbdkfSVJxpwc5bLU9d+Gei/2Z4ZW6kXE963mn1CdFH9fxrtKZFGkMSRRqFRFCqB2Ap9ezCKjFRR+W4rESxFaVWXVhRRRVHOFFFFAEVyQtrMx6BGJ/Kvl1uZHI/vH+dfSfiKS4Tw7f/ZYXmuGhZI40GSzEY4/OvK9F+E2q3YV9Unjso+6L88h/oK5cRGU2lFH0WSYijhqc51ZWvb8PL5nAV1Wg/D/AFzXCshh+x2p/wCWs4wSPZepr1vRPBOhaEFe3tFlnH/Lef52/DsPwroqmGF6zZtiuIn8OHj83/kcx4e8CaP4f2ypF9pux1nmGSP90dBXT0UV1RioqyPnK1epWlz1JXYUUUVRkFFFFABXH/E62e58C3mwE+UySnHoG5rsKiubaK8tZbadQ0UqFHU9wRg1MlzRaNaFX2VWNTs0z5WFOFa3iXQJ/Detz6fMCUB3QyY4dD0P9DWSK8tpp2Z+hUpxnFTjsx1OptOqTdC0vakpe1I2QtLSUtSaxCiiikWFe6/DOBoPBVszAjzZHkH0zj+leI2dpNf3sFpAu6aZwiD3NfSWmWMemaZa2MX3II1jHvgda7MHH3nI+Y4nrqNGFHq3f5L/AIct0UUV6B8UFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRXNeL/GNp4UslZwJryUfuYAcZ9z6Ck5KKuzSlSnVmoQV2zpCcDJOBUJvrQPsN1AG9PMGa+dtY8W63rsrPeX0gjJ4hiYoi/gOv41jdTnv61yyxS6I+gpcPSavUnZ+SufVAIYZBBB6EUtfN+j+KNZ0OUPZX0oQHmJzuRvwNes2XxFtLvwheaqyCO8tVCvb56ueFx7E1pDERlvocmLyWvQacfeTdvv7mh4p8bad4YXynBuL1hlbdD0Hqx7CvNr34oeIrpyYHgtU7LHHuI/E1yF1dT393Nd3UhknmYu7HuairjqYicno7I+owOSYahBe0ipS6t/ojrrX4l+JreQM91FOvdZIhz+Irv/DHxIsNalS0vkFleNwuWzG59Aex9jXiVFTDEVIve5tislwdeNlHlfdaH1JRXGfDjxFLrehNb3Tl7qzIRmPV1P3Sfft+FdnXpwkpxUkfn+Jw88NWlRnugqvdWFnfJsu7WGdfSRA386sUE4GTwBVGKbTujk774b+GL0k/YDbse8Dlf/rV5Z4w0Tw7odwbXTdRubm7U/PGdrJH7FvX2rpvHfxGd3l0nQ5sIMrPdIeT6qh/rXl3fJ6nrXBWnDaKPr8qw+LSVSvN26L/ADCiiiuc90WiiigYUUUUhi0UUUDClpKWgYUUUUhhRRRQMKBRQKBi0UUUhi0lLSUDFooooGFFFFIYtJS0lAxaKKKBhRRRSGFFFFAwooooAKKKKACiiigAq7pmk3+s3YttPtnnkPXaOF9yegrrvCvw3vNXCXeqb7SyPKpjEkg/9lFeu6bpVjo9otrYWyQRDso5PuT3NdVLDSnrLRHgZjn1HDXp0fel+C/zOE8PfCq0ttlxrcv2mXr5EZxGPqepr0K3toLSBYLaFIYlGFRFAAqWiu+FOMFaKPjcVjq+KlzVpX8un3BRRRVnIFFFFABRRRQAVyHxNbb4FvfdkH/jwrr64v4ptt8ETj+9NGP1qKvwM68Ar4qn6r8zwilpKWvLP0IWgUUCkUhaWkpaCkFLSUtIsXtRR2opFIKO9FHekWdL4D0xtU8YWSbcxwN58n0Xp+uK99kmihXdLIka+rMBXzdpmt6ho6TLp9wbdpgA8iAb8DsD2qtc3l1eOXurmadj1Mjlv5100q6pxslqeFmOUVMfXU5T5YpWXV/ofRMviTRIG2yatZKfQzL/AI0kXiXQ5m2x6tZMfQTL/jXzhgelGB6VX1yXY5/9V6Nv4j+5H1DHLHMu6KRHX1Vsin18yWeo3unyCSzu57dhyDG5H6V3Wg/FS/tGWHWIhdw9POjG2Qfh0NawxcXpLQ8/FcNYimuajLm/B/18z2GvBviTpa6b4xneNdsd2gnA9zw36iva9L1ex1qzW6sLhZoj1x1U+hHY15r8X7dpNS0gxozyyI6KqjJPIwB+dXiEpU7o58jlOjjfZy0umn+f6HmIrrvCHgW88SSLcz7rfTQeZcfNJ7L/AI10nhD4YnMd94gXp8yWef8A0P8Awr1JI0ijWONVVFGFVRgAVjSw19ZnqZlnsaadLDO779F6dyvp2nWmlWUdnZQrDBGMBVH6n1NWqKK7krHyEpOT5pO7Zj+KNZXQPDl5qBI3xpiIHu54X9a+a3d5ZGkkYs7sWZj3J616l8YtXLS2OjxtwB9olA/Jf6mvK64cRK8rdj67JcP7PD+0e8vyCiiisD2RaKKKQBQKKBQULS0lLQCCiiikMKWkpaBhRRRQMWiiigYUUUUhi0UUUDClpKWgApKWkpFC0UUUDCiiigYtFFFIBaKKKBiUtJS0FBRRRSGFLSUtACUtJS0DCiiigZ1Xh3x9q+gbYWf7ZZj/AJYynlR/st2r1vw/4w0nxHGBaz7LjHzW8vDj6eo+lfPVOR3jkWSN2R1OVZTgg+xreniJw03R5GOyTD4q8l7su6/VH1FRXjvhv4o3ljsttZVru3HAnX/WL9f73869V0zVrHWLRbmwuUniPdTyPYjsa76daNTY+LxuW4jBv94tO62LtFFFanAFFFFABRRRQBz/AIo8I6f4otNlwvlXSD91cIPmX2PqPavC9e8Pah4cvzaX8WM8xyr9yQeoP9K+lKzNe0Kz8Q6VJY3iZVhlHA5jbswrCrRU1dbnr5bms8LJQnrD8vT/ACPmrtS1c1bS7nRdUuNPulxLC2M9mHYj2IqnXnNW0Z91TkpRUo7MdRRRUm8Ra9U+HXggxmPXNUiw/wB61gcdP9sj19Kg8BeADKYtX1mLEfDQWzjr6Mw9PQV6v06V2YfD/bkfK53nKs8Nh36v9F+oUUUV3HyIUUUUAZ2taHYeINPaz1CESRnlW6Mh9VPY14X4s8F3/he4LMDPYMf3dyo/RvQ19C1FcW8N3byW9xEksMg2ujjIIrKrSU15no4DMqmElbePb/I+XBSiuy8eeCj4buVvLIM2mzNgZ5MTf3T7elcaK82cXF2Z91ha8K9NVKb0Y6lpKcoLMFUEsTgADkms2dsQVWd1RFLOxwqgZJPpXuXgLwgPDunm6u1B1G4Ub/8ApmvZR/Ws3wD4E/ssJq2qxg3pGYYT/wAsR6n/AGv5V6HXfhqHL78tz47PM3Va+GoP3er7+Xp+YUUUV2HzAUUUUAFFFFABXK+P9A/tzw1KYlzdWuZocdTjqPxH8hXVUduamUVKLTNsPXlQqxqw3TPluvTPhb4Y82Vtfuk+RCUtQR1Pdv6D8aytS8EzzfEN9It1KWszfaN4HCRE8/kcivZrO0hsbOG1t0CQwoERR2Ariw9B87cuh9bnWbR+rRp0XrNX9F/wSeiiiu8+MCiiigAooqK4uIrW3kuJ3CRRqXdj0AHWgEr6IxPF/ieDwvoz3TYe5k+S3iJ+83+A71883l5caheTXl3KZZ5m3O57mtXxb4km8T67LeMSLdfkt4z/AAp/ietYdefWqc702PtsswKwtK8vie/+QUUUViemLRRRQMWlpKWkMSlpKWgYUUUUDDvS0neloGFFFFIYtFFFAwpaSloGgNFBopDCiiigYVp6Frl54f1OO+snwy8OhPyyL3BrMpaabTuhThGcXGSumfSeg65aeINKiv7Rvlbh0PVG7qa06+e/B3iibwxq6yks1lMQtxGPT+8PcV9AQTxXMEc8Lh4pFDIy9CD0NelRq+0j5nwOaZc8HV0+F7f5ElFFFbHlhWfrWkW2uaTPp90uY5V4bujdmHuK0KKTSasyoTlCSlF2aPmfVNNuNH1O4sLpcTQttPow7EexFemfCbRPLtbnWpV+aU+TDn+6PvH8+PwrS+IfhCTXo7a9sI83sbiJwP4oyev/AAHr9M11+mWEOl6ZbWMAxHBGEHvjvXHSw/LVbeyPqMxzlYjARjH4paS8rf5luiiiu0+VCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA5nxr4Vi8UaO0ahVvYcvbyH1/un2NfPs0EttPJBPG0c0bFXRhypHUV9UV5l8T/AAd9qhbX7CP9/Ev+lIo++o/i+o7+1c2IpXXMj38mzD2cvYVHo9vJ/wDBPIqdTRTq4D69C0vakpe1I2QtLSUtSaxCiiu98C+BJNYlj1PU4ymnqcxxsMGc/wDxP86qEHN2RlisVSwtJ1arsvz8kbPww8KNEP7fvY8M67bVGHQHq/49q9OpFVUQKqhVUYAA4Apa9anTUI8qPzbHYyeLrOrP5eSCiiirOQKKKKACiiigAooooAKKKKACiiigAooooAKKKKAGu6xxs7HCqMk+1fNHiPWZdf1+71CViQ7kRj+6g+6Pyr6N1YM2jXqp9427gfXaa+XV6VyYpvRH0fD9ON5z66IeOlOFNHSnCuM+piLTwxAIBIB6jPWmU4VJvEUUtIKWpNohRRRSLPQ/hFIw16/jz8rWwJH0avYa8q+EFk3nalfkfKFWFT69z/SvVa9TDL92j89z+SePlbpb8gryz4leNyhk0HTJcN0u5kPT/YH9fyrqPHnikeGtEPkMPt9zlIB/d9W/D+deAszO7O7FmY5ZieSfWpxFW3uo3yXL1Uf1iotFt69xtLSUtcR9WFFFFAxaKKKBhRRRSGLRRRQMKWkpaBhRRRSGFFFFAwoFFAoGLRRRSGLSUtJQMWiiigYUUUUhi0lLSUDFooooGFFFFIYUUUUDCiiigAooq3pmmXer38dlZQmWeQ8AdAPUnsKaV9EKUlFOUnZIhtbWe9uo7a1ieaaQ4REGSTXsfg/4d22kCO+1VUuL/qqdUi/xPvWv4T8HWfhi1yAJr5x+9nI/RfQV0tehRwyj70tz4jNs9lXbpYd2j36v/JBRRRXWfNhRRRQAUUUUAFFFFABRRRQAVw3xZbHgvH965jH867muA+LrY8JwL63SfyNZ1fgZ25ar4un6nilFFFeYfoAtAooFIpC0tJS0FIKWkpaRYvaijtRSKQUd6KO9ItC0UUUFIKKKKQwooooA0tE1y/0C/W7sJSjfxofuyD0Ir3Dw5rWl+K7eLUo4UF7bgoyOAXhJ649jjrXz7WnoGu3Xh7Vor+1JO04kjzxIvcGt6NZwdnseRmuVxxcHOGlRbPv5M+kaKqaZqNvq2mwX1q+6GZdy+3sfcVbr1U76n55KLjJxlo0FFFZ2v3w0zw/f3pOPJgdgffHH60m7K4Ri5SUV1Pn7xjqf9r+LtRug2Y/NMcf+6vA/lWHRksdzfeJyfrRXmN3dz9CpwVOCgumgUUUUjQWiiikAUCigUFC0tJS0AgooopDClpKWgYUUUUDFooooGFFFFIYtFFFAwpaSloAKSlpKRQtFFFAwooooGLRRRSAWiiigYlLSUtBQUUUUhhS0lLQAlLSUtAwooooGFFFFIYVc0zVb7R7tbrT7l4JR1KnhvYjoRVOimm1qhSjGcXGSumex+GfifZ6hstdYC2lyeBMP9W5/9lP6V6ArK6hlIZSMgg5Br5crpvDfjfVfDjLEj/abLPNvIeB/unt/KuylimtJny+YcORleeF0fbp8ux79RWH4e8V6X4kg3Wc22YDL28nDr+Hce4rcruUlJXR8jVpTpTcKis0FFFFMzCiiigDzD4u6OrW1prMa4dG8iUjuDyv65/OvJ6+gfiBbi58D6kCM7EEg/wCAkGvDNJ0i+1u9W00+BppT1x0UepPYV5+Jh+806n22RYhPB++9It/duVI0eWRY40Z3Y4VVGST7CvW/BPw6FmY9T1uMNcD5orY8iP3b1Pt2rb8I+BLLw2i3E2251EjmYjhPZR2+tddWtHDW96Z52aZ46qdHDaR6vv6eQUUUV1nzQUUUUAFFFFABRRRQBR1jTIdZ0i60+dQUmjK5PY9j+Br5qnge2uZbeUYkico31BxX1HXhereF9Q1zx/q1rp0GVFwWklbhI888n8elcmKhezW59Jw9iVTdSM3aNr/oclb2813cR29vE8s0h2oiDJJr2XwT8P4tECahqarNqBGUTqsP09W962PC3g3T/DFvmIedeMMSXDjn6D0FdJTo4dR96W5OaZ3KunRoaR6vq/8AgBRRRXUfOhRRRQAUUUUAFFFFABRRRQAzyo/N83Yvmbdu/HOPTPpT6KKAuFFFFABRRRQAV5f8WvEhgto9Atnw8wElyQeidl/E8/hXpN7dw2FjPdzsFihQyOfYDNfMusanNrOr3Wozk755C2P7o7D8BXPiJ2jZdT2slwvta3tZbR/MpUtJS1wn1wUUUUDFooooGLS0lLSGJS0lLQMKKKKBh3paTvS0DCiiikMWiiigYUtJS0DQGig0UhhRRRQMKWkpaBhXqfwr8Tk7tAu5OgL2pJ/NP6j8a8sqe0uprG8hu7dys0Lh0I9RV05uErnJjsJHF0HSfy8mfT9FZ+iarFrejWuow/dmQEj+63cfga0K9VO6uj84nCUJOMt0FFFFMkKKKKACiisrV/Eek6HHu1C9jibGRHnLn6Ac0m0ldlwpzqS5YK78jVoryvVvi4xLR6RYYHaW4P8A7KP8a4vUfGPiDVCRcanMEP8ABEdi/kK55YqC21Pbw/DuLq6ztFee/wByPfbrVNPsQftV7bw47SSAH8qxbjx/4Ytzg6rG5HaNS39K8BYl2y5LE92OaSsHjJdEetT4Yor+JNv0sv8AM9xf4o+GUOBNct/uwGmD4qeGz1a7H1gNeI0VP1uodK4bwX977/8AgHu8XxK8MSnBvnj/AN+JhWpa+LNAvCBBq9oxPQGTb/OvnSkwPSmsZPqjKfDGGfwya+5/ofUUcscyb4pFdT3U5FPr5jtr67snD2t1PAw7xyFf5V0unfEjxHYEB7pLuMfwzrk/mOa1jjIv4kedW4YrR1pTT9dP8z3eivOtK+LWnzlU1OzltWPWSM70/wAa7jTtX0/VofNsLyG4XvsbkfUdRXTCrCfws8TE4DE4b+LBrz6feXaKKKs4wooooAKRlDKVYAqRgg96WigD508ZaGPD/ii6s0XFux82H/cbt+ByKwq9R+MdoofSr0D5iHiY+3BH9a8ury6seWbR+hZbXdfDQm9/8tBaXtSUvasj0oi0+ON5ZFjjRnkY4VVGST7Ct7w94M1fxG6tBD5Nrn5riUEL+Hr+Few+GvBel+G4w8Mfn3ZHzXEgy34f3RWtOhKfkjzsdnFDCLl+KXZfr2OR8H/DTBj1DX0GRho7P+r/AOFeoKoVQqgBQMAAcClor0adONNWifEYzHVsZU56r9F0QUUySWOFC8sioo6sxwKof8JBo3mbP7Wst3p56/41TaW5zRpzl8KbNKimRyxzIHikV1PRlORT6ZDVgooooAKKKKACiiigAooooAKKKKACiiigAooooARlDoVPQjBr5h1nT5NK1u9sZVKtDMyge2eD+WK+n64bx94FXxHF9vsNqalEuMHgTL6H39DWFem5x06Hr5RjI4eq4z2keGjpThTp7ea0uJLe5ieKaNtro4wVNNFeez7WLTV0LThTacKk3iKKWkFLUm0QpQCSAoJJOAB3NJXe/DXwsdT1IavdR/6Hat+7BHEkn+A/nVQg5y5UZYvFQwtGVWey/HyPSPBuif2D4ZtbRxidh5s3++3b8OB+FbrusaM7sFVQSxPQCnVxHxQ1w6X4YNpE+Li+byhjqE/iP9Pxr1nanD0PzaKqYzE67yZ5R4u19/EfiG4vMnyFPl26+iDp+fWsKiivMbbd2fe0qcacFCOyClpKWkaBRRRQMWiiigYUUUUhi0UUUDClpKWgYUUUUhhRRRQMKBRQKBi0UUUhi0lLSUDFooooGFFFFIYtJS0lAxaKKKBhRRRSGFFFFAwoop8MMtxPHBCjSSyMFRFGSxPamDaSuyfTtOutVv4rKziMk8pwoHb3PoBXvXhTwraeF9P8qPEl3IMzzkcsfQegFVfBXhGLwzp2+UK+ozAGaT+7/sj2H61c1jxloOhsY7y/Tzh1ij+d/wAh0r0KFFU1zT3Phs2zOpjqn1fDJuK7df8Agf8ADm9RXncvxf0ZXxHY3si/3sKP61f0/wCKPhy9cJLJNaMe86fL+YzW6rQfU8qWWYuK5nTZ2tFRQXEN1Cs1vKksTDKujAg/jUGp6la6Rp019eSBIIlyT3PoB7mrurXONQk5cqWpbZgqlmICjkknpWHeeM/Dtg5SfVrfeOqod5H5ZrxvxN401PxJcMrSNb2IPyW6NgY9WPc1znQVxzxdn7qPqsJw1zR5sRKz7L/M+gIfH3hid9i6tEpP99WUfmRW/b3MF1EJbeaOWM9GRgR+lfMFaGk63qOh3In0+6eFgeVzlG+o6GpjjHf3kbYjhiny3oTd/P8A4B9KUVzHg7xjb+KLRlZRDfxD97Dngj+8vt/KunrtjJSV0fJ16FShUdOorNBRRRVGIV5z8YXx4fsUz965/kpr0avJfjJeA3Gl2QP3VeYj64A/rWVd2ps9LKYuWMh/XQ8vooorzT7sWgUUCkUhaWkpaCkFLSUtIsXtRR2opFIKO9FHekWhaKKKCkFFFFIYUUUUAFFFFAHpfwo14xXc+iTP+7lBlgyejD7w/Ec/hXrNfNOj376XrNnfIcGCZWP0zz+ma+lEdZI1dTlWAIPtXpYSd48r6HwvEmFVLEKrHaf5r+kOri/ildm28D3CA4NxIkX4Zyf5V2leZ/GWcro+m24P37hmP4L/APXraq7QZ5WXQ58VBef5anjlLSUtecfdBRRRQMWiiikAUCigUFC0tJS0AgooopDClpKWgYUUUUDFooooGFFFFIYtFFFAwpaSloAKSlpKRQtFFFAwooooGLRRRSAWiiigYlLSUtBQUUUUhhS0lLQAlLSUtAwooooGFFFFIYUUUUDCiiigCSCeW2nSeCV4pUOVdDgg/WvTvC3xR+5aa+PZbtB/6EP6ivLaK0p1JQd4nHjMBQxkOWqvn1R9QQTxXMKTQSJJE4yrocgipK+efDni3U/DU2bWTzLYn57eQ5Q/T0PuK9m8N+L9M8Sw/wCjyeVdAZe3kPzD6eo9xXo0q8ammzPh8xyavg/eXvQ7/wCZ0FFFFbnjlLV9OTV9JutPkkaNLhCjMvUA+lM0fRNP0KyW10+3WKMfePVnPqT3NaFFKyvc09rPk9nfTewUUUUzMKKKKACiiigAooooAKKKKACmJGkZYoiruO5sDGT6mn013SNC7sqqoyWY4AoDUdRXNXvj7wzYyGOTVIncHBEQL4/KoIPiT4WncL/aBjz3kiZR/Ko9pDudKwWJauqbt6M6yiq1nqFnqMIms7mKeM/xRuGqzVnO04uzCiiigQUUUUAFFFFABRRRQAUUUUAFFFFABRRRQB538W9a+x6DDpcTYkvXy+D/AMs15P5nFeK11nxH1X+1PGd0FbMVqBbp+H3v1z+VcnXnVpc02fcZZQ9jhorq9X8wpaSlrI9AKKKKBi0UUUDFpaSlpDEpaSloGFFFFAw70tJ3paBhRRRSGLRRRQMKWkpaBoDRQaKQwooooGFLSUtAwpaSlpDR6l8I9YP+maNI3A/fwgn8GH8j+dep186+ENSOleLNOud2E80Rv/utwf519FV6OFleFux8PxBh1SxXOtpK/wA+oUUUV0nhBWdq+uadoVqbjULlIl/hXqzewHU1yfi34kWukGSy0rZdXw4Z85jiP9T7V5Df6jeardtdX1w88zdWc9PYegrmq4lR0jqz38uyKpiLVK3ux/F/5HaeIvihqOoF4NJU2VuePMPMrf0WuEklknlaWWRpJGOWdzkn8aZQK4JzlN3kz7LDYSjho8tKNv67i0UUVB0hRRRSGFFFFAwooooAKKKKACpbe5ntJlmtppIZV6PGxUj8qiopg0mrM9A0H4p6jZFYdWjF7COPMX5ZB/Q16foviLS9fg83T7pZCB80Z4dfqK+cKltrqeyuEuLWaSGZDlXjbBFdFPEzjo9UeDjsgw+IvKl7kvLb7v8AI+n6K8w8LfFFZClnr+Ebot2o4P8AvDt9RXpsciSxrJG6ujDKspyCK9CnUjNXifGYzA18JPkqr59GOoooqzkPMPjJKv2PSYs/MZHb8AB/jXkvavb/ABh4KvvFuu2zm6itrC3i27sbnZicnA/KtDRfh7oGjFZBbfarhf8AlrcfNz7DoK46lGU5t9D6nBZph8HhIwbvLsvU8g0PwbrevsrWto0cB6zzfKn4ev4V6h4f+GOk6UUnvz9vuRz84xGp9l7/AI13AAAAAwB0Apa1hh4R1ep52LzvE1/di+WPl/mIqqihUUKoGAAMAUtFFbnjhXI+NPG8HhmEW8CrPqMi5SMnhB/eb/Cuou7hLSzmuZPuRIzt9AM181alqE+rancX9wxaWdy5z2HYfgK58RVcFZbs9vJcuji6rlU+GP4sm1TW9S1q4M2oXckzE5Ck4VfoOgqhgelJS15rbbuz72lCMI8sVZGjpOualolys+n3ckRByUzlG+o6GvcfCHiqDxRpplCiK7iws8Oeh9R7Gvn2t/wbrT6H4mtbjcRDKwhmHqrH+hwa2oVnCVnseVnGWQxVFzivfWz7+R9C0Ug56UteofngUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAcz4s8F2Hii23OBBfIMR3Cjn6N6ivDNZ0O/0C/az1CEo45Vhyrj1U96+may9f0Gy8RaZJZXsYIIzHIB80bdiKwq0VPVbnsZbm08M1CesPy9P8j5rpwq5q+lXOiarcafdjEsLYyOjDsR7GqYrzmrOzPuaclKKlHVMUUtIK2/DXhm98TagLe2BSFCDNOR8sY/qfapScnZGs6sKUHObskP8K+GLnxPqiwR5S2jIM82OFHoPc179Y2NvptjDZ2kYjgiXaiioNF0az0HTY7Gyj2xpyWP3nPck+taFenQoqmvM/P8ANs0ljqllpBbL9WFeDfE3VjqXi+WBWzFZKIV9N3Vv14/Cvcry5Sysri6k+5DG0h+gGa+YLm4e7u5rmQ5eaRpGPuTmoxMtEjqyCjzVZVX0VvvIqKWkriPrApaSloAKKKKBi0UUUDCiiikMWiiigYUtJS0DCiiikMKKKKBhQKKBQMWiiikMWkpaSgYtFFFAwooopDFpKWkoGLRRRQMKKKKQwooooGFeu/DXwh9igXXL+L/SZV/0dGH+rQ/xfU/yrkvh94XGv6x9ouUzY2hDSA9Hbsv9TXqXjbWG0HwleXUJ2zFRFDjszcD8q7MPTSXtJHzGeY+UpLA0XrLf59P8zh/iB8Qphcy6Nos3lqnyXFyh5J7qp7e5ry7JJJJJJOSSeTTeTySSTySe9OFZzm5u7OzCYWnhqahBfPuLTqbTqzO6Jv8AhjxVf+GL5ZIHZ7Vj++tyflYeo9D710HxI8VR61PZ2djLus0jWZsH7zsOAfoP51wNLVe0ko8vQxeBoyxEcRb3l/X4C0tJS1kejEKKKKRRo6Fqsuia3a6hCxBicbx/eQ/eH5V9IxyLLEkiHKuoYH2NfLvavpHw67SeGtMdvvNaxk/98iu7ByesT5LiilG1Or11Rp0UUV3HyAV8+fELVBqnjO9ZGzHb4gTH+z1/XNe0+KtbTw/4dur9iPMVdsQ9XPA/x/Cvm9mZ3Z3JZmJLE9ya5MVLRRPpOH8O+aVd+i/UKKKK4z6kWgUUCkUhaWkpaCkFLSUtIsXtRR2opFIKO9FHekWhaKKKCkFFFFIYUUUUAFFFFAB2r6O8MXBu/C+mTE5LWyAn3Ax/SvnLtX0F4HBHgrSs9fJ/qa7MG/eZ8zxRFfV4Pz/Q6GvJfjRIfN0ePtiVv/Qa9aryL4z/APH5pB/6ZyfzWuqv/DZ83lH++R+f5M8tpaSlrgPtQooooGLRRRSAKBRQKChaWkpaAQUUUUhhS0lLQMKKKKBi0UUUDCiiikMWiiigYUtJS0AFJS0lIoWiiigYUUUUDFooopALRRRQMSlpKWgoKKKKQwpaSloASlpKWgYUUUUDCiiikMKKKKBhRRRQAUUUUAFPhmlt5kmgkeOVDlXQ4IP1plFMGk1ZnqnhT4nhvLstfIU9Fu1HB/3x/UV6bHLHNGskTq8bDKspyCPrXy9XS+GPGmpeGpQkbG4sifmt3PA91PY110cU1pM+YzLh6FS9TC6Pt0fp2/L0Pf6KyNB8Sad4itPPsZgWH+sibh0PuP61r13ppq6PjalOdKThNWaCiiimQFFFFABRRRQAUUUUAFFFFAGfrOsWmhaXNqF6+2KMdB1Y9gPc14L4m8Z6p4nuG86RobMH93bI2FA9/U1vfFnW3vNfj0pHPkWaBmA6GRhn9BgfnXn4rhr1W3yrY+uyfAQp01Wmryf4IUDHSnCkpRXMe+i5p+pXulXS3NhcyW8qn7yHGfqO9e1eB/HMfiSP7HdhYtSjXJA4WUf3l/qK8Lq5pd/NpeqW19AxWSCQOMdx3H4irpVXB+Rx5hl1PF03de90f9dD6Tvb22060kuryZIYIxlnc8CvMNb+LUzSNFotqqxjgT3AyT7he341zXjXxdL4m1HZCzJp0J/dRnjce7H3rl60rYlt2gcWWZDTjBVMSryfTov+CdQ3xE8UM+7+0tvsIkx/KtTTvitrds4F7Fb3cff5djfmOP0rg6KwVaotbntTyzBzjyukvut+R9C+HPF2l+JYv9FkMdwoy9vJw6/4j3Fb9fMNpdXFjdR3VrK0U8R3I6nkGvffB3iVPE2iLckBbqI7J0HZvUexruoV+f3ZbnyGcZN9T/e0tYP8P+AdDRRRXSeAFFFFABRRRQAVU1S9TTdKur2QgLBE0nPsKt1wnxX1P7F4R+yq2JL2UR/8BHJ/kKmcuWLZvhqXtq0afdnh8sz3E8k8hy8jF2J7knNMoorzD9AQUtJS0hhRRRQMWiiigYtLSUtIYlLSUtAwooooGHelpO9LQMKKKKQxaKKKBhS0lLQNAaKDRSGFFFFAwpaSloGFLSUtIaDcUIZfvKcj6ivpnSbkXukWd0DnzYUfP1FfMte/+Dr6GLwDp11cyrHFDb/O7nAAUkV14R2k0fOcSU+alTkt72+//hjpJZY4InlldUjQbmZjgAV5B4z+I0uomTT9FkaKz+7JcDhpfYei/wA6zvG3jmfxHO1nZs0WmIeF6GY+re3oK42iviL+7DYeU5IqSVbEK8ui7evn+QUtJS1xn0olAooFAxaKKKBhRRRSGFFFFAwooooAKKKKACiiigAooooAK6rwn44vvDUqwSFrjTifmhJ5T3X0+lcrRVRk4u6Mq+Hp4iDp1VdM+mNM1Sz1ixjvLGZZYXHBHUH0I7GrlfO3hrxPe+Gb8T2xLwOf30BPyuP6H3r3rR9Ys9c02O+spA8Tjkd1PcH3r06NdVF5n5/muUzwM+Zawez/AEf9al+iiitzyAoork/HHjOHwrYrHEqy6jOD5MZ6KP7ze386UpKKuzWjRnWmqcFds6HUNUsNKg86/u4rePsZGxn6DvXMy/E/wvHIVF3LJj+JISRXh+oale6vdtd39w88zHlmPT2A7Cq4rjlipfZR9PQ4fpcv72Tb8tD3fVPFWj6/4T1aPTL5Hn+ySHyj8r9PQ9fwrwsUgJByDg+opRXPVqOpZs9nL8BDBqUYO6bvqLS0lLWJ6sQo+lFFIo+lNCuje6Bp9yTky26MfrgVoVh+DgR4O0nP/Pstble1B3imflOJio1pxXRv8woooqjEKKKKACiiigAooooAKKKKACiiigAooooAKKKKAPMPi9pKta2Wrovzo/kSEdweV/UGvKBXvXxJh8/wReAKWZXjKgDJzuA4/OuP8I/DKW58u+15THD1S0zhm/3vQe1cNak5VLRPsMqzCnQwPNWezaXd9f1Od8JeC77xNOJCGg09T885H3vZfU17jpWlWWi2EdlYwiKFOw6sfUnuaswwxW0KQwRrHEg2qijAAqSuilRjTXmeFmOaVcbKz0itl/n5hRRRWx5hyvxGvTY+CL8g4aYLCp/3j/hmvn6vY/jFc7NCsLYH/W3G4j/dH/168crgxLvM+yyKny4Xm7t/5C0lLSVzntBS0lLQAUUUUDFooooGFFFFIYtFFFAwpaSloGFFFFIYUUUUDCgUUCgYtFFFIYtJS0lAxaKKKBhRRRSGLSUtJQMWiiigYUUUUhhUkEEt1cR28CF5ZWCIo7k9Kjr0r4V+HPPuZNduU/dxEx24I6t3b8OlaU4OclFHLjsXHCUJVpdNvN9D0PwzocXh7QrewjwXUbpXH8Tnqa5T4wbv+EVtsfd+1ru/75avQq4/4m2Zu/A14VGWgZJvwB5/Q16k42ptI/PcJWlPGxqVHdt6/M8CpwpopwrzWfdoWnU2nVJrEWlpKWkaxFpaSlqTaIUUUUihVRpHVFGWYhQPUmvprTrf7Jplrbf88oVT8gBXhfgDRzrHiu23LmC2Pnyntx0H54r32u/Bx0cj43ifEKVSFFdNX8woorzj4keOF02CTRdNlzeyLieRT/qVPb/eP6V1zmoq7PnMNhp4ioqcDkviV4pGuayLC1k3WVkSMg8SSdz9B0H41xAptOFeZKTk7s+9w9CNCmqcNkLRRRUnSLQKKBSKQtLSUtBSClpKWkWL2oo7UUikFHeijvSLQtFFFBSCiiikMKKKKACiiigA7YFfSeg2hsfD+n2pGGit0Vvrjn9a8K8G6O2t+J7O225hRvNmPoq8/qcCvoau/Bx3kfH8UYhOUKK6av8AT9Qryn40RfutImx0aRf0B/pXq1eefGC283wvbXAH+puhn6MCP8K6KyvBnhZXLlxcH/Wx4nS0lLXnn3AUUUUDFooopAFAooFBQtLSUtAIKKKKQwpaSloGFFFFAxaKKKBhRRRSGLRRRQMKWkpaACkpaSkULRRRQMKKKKBi0UUUgFooooGJS0lLQUFFFFIYUtJS0AJS0lLQMKKKKBhRRRSGFFFFAwooooAKKKKACiiigAooooAs2N/d6bdpdWU7wTofldD+h9RXr/hL4j2ur+XZapstb48K/SOU+3ofavF6K1p1ZU3ocGPy2hjY2qKz6PqfUlFeL+EfiNc6R5dlqpe5sRwsnWSIf+zCvYLK9ttRtI7qznSaCQZV0OQa9KlVjUWh8Fj8tr4Kdqi06PoyxRRRWp54UUUUAFFFFABRRRQB80+K5zc+LtWmJzuunA+gOKyRWn4ntpLPxTqkEgwy3Ln6gnI/nWYK8uW7P0TD29lG21kOpRSUoqDpQ6lFJSipNkLS0lLUmsQooopGgV3Xwqvnt/FL2gP7u5gbI915B/nXC12nwut2m8ZJKB8sMDs348f1rWjf2iscGaqLwVXm7P8A4H4nuFFFFeufmQUUUUAFFFFABXinxe1H7R4jtrBW+W1h3MP9puf5AfnXtdfNHiu//tPxXqd1nKtOwX/dHA/QVz4mVo2PayOlzYhzf2V+ZkUUUVxH1oUtJS0hhRRRQMWiiigYtLSUtIYlLSUtAwooooGHelpO9LQMKKKKQxaKKKBhS0lLQNAaKDRSGFFFFAwpaSloGFLSUtIaErYu/EV5daBZaKD5dnbAllB/1jEk5P0z0rHopptbEypxm05K9tV6i0UUVJoFLSUtAxKBRQKBi0UUUDCiiikMKKKKBhRRRQAUUUUAFFFFABRRRQAUUUUAFdB4T8UXPhjUxMm57SQgTw5+8PUe4rn6KqMnF3RnWowrQdOorpn05ZXtvqNlFd2sgkglUMjDuKsV4p8OvFx0a/GmXkn+gXLYUk8ROe/0Peva69WlVVSNz83zLATwVZweqez8hCQASeg6180+J9Xk1zxJfXzsSrSFYx/dQcAV9JXKlrWZV+8UYD64r5XKsjsjfeUkH61lim7JHpcPwi5Tn1Vhw6U4U0dKcK4mfVxHUopKUVLNoi0tJS1JvEKD0oq9o9m2oa3Y2ijJmnRce2ef0oSu7DnJQi5PZH0NoVv9k0DT7fGDHbop/wC+RWhSKAqhR0AwKWvaSsrH5NOTnJyfUKKKKZIUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAIyhuGAIznkUtFFABRRRQAUUUUAeSfGWb/S9Jh9Ekf9QK8vr0f4xtnXdOX0tm/wDQq84rzq38Rn3WVK2Dh/XVi0lLSViekFLSUtABRRRQMWiiigYUUUUhi0UUUDClpKWgYUUUUhhRRRQMKBRQKBi0UUUhi0lLSUDFooooGFFFFIYtJS0lAxaKKKBhRRRSGXdJ0yfWdVt9Pth+8mfbn+6O5/AV9G6bYQaXptvY2y7YYECL7+/1NcJ8LfDn2PT31q4TE90NsII+7H6/if0Fei16WFp8seZ7s+E4gx/t6/sYP3Yfn1+7b7wqG6tory0mtZ13RSoUceoIxU1FdR4CbTuj5r8TeHLrwxrEllOpMRJaCXHEidvx9ayBX0zrmhWHiHTmsr+EOh5VhwyH1U9jXhXirwZqHha5zIDPYucR3Kjj6N6GuCtRcNVsfZZZmkMQlTnpP8/67HOU6m06uY9yItLSUtI1iLS0lLUm0RaT2xmlrv8A4b+ETqd4us3sf+hwN+5Vh/rXHf6D+dVCDnLlRhi8VTwtF1amy/HyO3+H/hw6BoCvOmLy7xJL6qP4V/Afqa62iuZ8Zab4h1TTvs2h3sFsrAiUNlXf2DdhXrJKEbLofm06ksXiHOpKzk93sjnvHXxHj0xZNM0aRZL0/LJOOVh+nq38q8ad3kkaSRi7uSzMxySfU1rap4V1zRift2mzog/5aKu9T+IrIFcFWcpP3j7HAYahQp2ou9933CnCm04Vmd6FooopFi0CigUikLS0lLQUgpaSlpFi9qKO1FIpBR3oo70i0LRRRQUgooopDCiiigApQCzBVBLE4AA5JpURpHVEVmdjhVUZJPtXrvgTwB/Zxj1bV4wbzrDAeRF7n/a/lWlOlKo7I4sfj6WCpc9Tfou5reAPCx8PaQZrlAL+6w0v+wvZf8feuvoor1oRUY8qPzfE4ieIqyq1N2Fc14/sTf8AgjUo1GXSPzVHupzXS1HcQpc20sEgykiFG+hGKcldWIo1PZ1Iz7O58qClqxf2j2Go3NnIMPBK0ZH0OKr15Z+hJpq6CiiigoWiiikAUCigUFC0tJS0AgooopDClpKWgYUUUUDFooooGFFFFIYtFFFAwpaSloAKSlpKRQtFFFAwooooGLRRRSAWiiigYlLSUtBQUUUUhhS0lLQAlLSUtAwooooGFFFFIYUUUUDCiiigAooooAKKKKACiiigAooooAK2vDvifUfDV35tnJuhY/vIHPyP/gfesWiqTad0RVpQqwcKiumfQ/hvxVp3ia18y1fZOo/eW7n5k/xHvW7XzFaXlxYXUdzaTPDPGcq6HBFew+D/AIi2+sbLHVClvf8ARX6JL/gfavQo4lS0lufE5pkM8PerQ1j26r/NHeUUUV1HzgUUUUAFFFFAHlPxZ8Lu+zxDaoTtUR3QA7fwv/Q/hXlIr6pmhjnheGVFeN1KsrDIIPUV8++N/CcnhfWCsYZrCclrdz29VPuP5VxYinZ8yPqslxynH6vPdbenb5HNUopKUVyn0aHUopKUVJshaWkpak1iFFFFI0CvYfhRozWukT6pKuHu22x5/uL3/E/yrzrwr4cn8S6xHaICtumGuJf7q/4noK+g7e3itLaK3gQJFEoRFHYCuzCU7vnZ8vxHjlGn9Vi9Xq/T/gktFFFegfFhRRRQAUUUUAVdTuPsmlXdxnHlQu/5AmvlssXJc9WJY/jX0h40lMPgvV5B1Fsw/Pivm4dK48S9Uj6fII/u5y8/6/MWiiiuY+gClpKWkMKKKKBi0UUUDFpaSlpDEpaSloGFFFFAw70tJ3paBhRRRSGLRRRQMKWkpaBoDRQaKQwooooGFLSUtAwpaSlpDQlFFFAxaKKKQwpaSloGJQKKBQMWiiigYUUUUhhRRRQMKKKKACiiigAooooAKKKKACiiigAooooAK9s+HHig6zpR0+6kze2igZJ5kj7H6joa8TrR0LV5tC1m31CDOYm+df7ynqPyrajU9nK/Q87NMCsZh3D7S1Xr/wAE+k6+f/iH4ck0LxJLMiEWd4xliYDgE/eX8/517zZ3cN9Zw3du4eGZA6MO4NUfEOg2niPSJbC7GA3McgHMbdmFejVh7SOh8Jl+LeDr3lts/wCvI+aR0pwra17wlrHh24ZLu2d4QfkuI1JRh9e30NYgIPQ150k07M+6o1IVIqUHdD6UUgpRUM6Yi0tJS1JvEK7f4W6b9s8VG6Zcx2cRf/gR4H9a4ivbfhdpP2Dwx9sdcS3r+Z/wAcL/AFP41th4c1ReR5WeYn2GCl3lp9+/4Hb0UUV6p+dBRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAeOfGNca3pretuw/wDHq83r1T4zQfNpNxj/AJ6Jn8jXldedX/iM+5yl3wcPn+bFpKWkrE9MKWkpaACiiigYtFFFAwooopDFooooGFLSUtAwooopDCiiigYUCigUDFooopDFpKWkoGLRRRQMKKKKQxaSlpKBi0UUUDCtvwpoL+ItfgsgD5AO+dh/Cg6/n0rEr3T4eeHP7D0FZ50xeXmJJM9VX+Ff8+ta0KftJ26HmZvjvqeGcl8T0X+fyOtiiSCJIo1CxooVVHQAU+iivWPzhu4UUUUAFRXNtBeW0lvcxJLDINro4yCKlooGm07o8U8Z/DmfRvM1DSVeew6vF1eH/Ff5VwQr6nIBGCMg9RXifxH8IJod8upWMe2xuWwyDpFJ6fQ1w16PKuaJ9bk+bOrJUK2/R9/XzOFpaSlrkPpoi0tJXTeEfB134nu9x3Q2EZ/ez46/7K+p/lSjFydkFWvToU3UqOyQ7wb4Qn8T3+6QNHp8J/fS/wB7/ZX3/lXvFtbQ2dtHbW8axwxKFRFHAAqLT9PtdLsYrOziWKCIYVR/M+9Wq9SjRVNeZ+f5pmc8dVvtFbL9fUKKKK2PLEIBGDyD2rC1TwX4f1fJutMhEh/5aRDY35it6ik0nuXTqTpu8HZ+R5dqXwct23NpepPGe0dwu4fmOf0rkNR+HHibTskWQukH8Vu279OtfQFFYyw8H5HqUc6xVP4nzep8tXFtcWjlLmCWFgekiFf51FX1JcWltdoUuIIplPaRAw/Wucv/AIeeGL8knTlgc/xW7FP06fpWMsK+jPUpcQ03/Eg16a/5Hz/QK9bvfg7atk2GqTR+izIGH5jFc9efCjxDb5Nu9rdKP7r7T+tZSoVF0PSpZtg6m07euhw1LW3deDvEVnnztIucDui7x+lZM1tcW5xPbyxH0dCv86ycWt0ehTq06nwST9GRUtJkUVJuO7UUdqKRSCjvRR3pFoWiijIoKQUVcs9J1HUGC2djcTk/3IyR+fSup034X6/elWuRDZRnr5jbm/IVUacpbI562Mw9BfvZpfP9Diq2dD8L6t4hlC2Ns3lZw07/ACxr+Pf8K9V0b4Y6JppWS7D38w/568IP+Aj+ua7OOKOGNY4kVEUYCqMAV1U8I3rM+fxnEsIrlw0bvu9vu/4Y5fwt4E07w4FnbF1f45nccL7KO31611dFFdsYqKsj5KviKuIm6lV3YUUUVRiFFFFAHhPxU0n7B4tN2i4ivYxJ/wACHDf0P41w9e8fFDRDqvhR7mJd09i3nLjqV6MPy5/CvB68+tHlmfa5TX9thl3jp/XyCiiisj0xaKKKQBQKKBQULS0lLQCCiiikMKWkpaBhRRRQMWiiigYUUUUhi0UUUDClpKWgApKWkpFC0UUUDCiiigYtFFFIBaKKKBiUtJS0FBRRRSGFLSUtACUtJS0DCiiigYUUUUhhRRRQMKKKKACiiigAooooAKKKKACiiigAooooAKKKKAPRPB3xIl0/y7DWnaa1+6lx1aP/AHvUfrXrkE8VzAk0EiyRONyupyCK+X66bwr4zv8AwzOEBM9gxy9ux6e6+hrro4lx92ex81muQxrXq4bSXbo/8n+B79RWfo+tWOu2K3dhMJIz94fxIfQjsa0K9BNNXR8VOEoScZKzQUUUUyQrL1/Q7XxDpE2n3Q+VxlHA5RuzCtSik0mrMqE5QkpRdmj5h1XS7rRtTn0+8TbNC2D6MOxHsaqCvYfi1oS3GlQ6zEn762YRykDqhPH5H+dePCvMqw5JWPv8vxSxVBVOvX1HUopKUVkekhaWkpak1iFaehaDfeIdRWzso8nrJIfuxj1JrU8LeCNR8SyrLg21gD81w4+97KO5/SvbNG0Sw0GwWzsIRGg5ZurOfUnua6KOHc9XseNmmdU8InTpaz/Bev8AkReHvD9n4c0tLK0XJ6ySEfNI3qa1qKje4hjOHljU+jMBXpJKKsj4Sc51ZucndskopiTRSf6uRH/3WBp9Mzaa3CiiigAooooA53x4pbwNrAH/AD7k/qK+cq+mvE1v9r8L6pAB9+2f+VfMo+6PpXHifiR9TkD/AHU15/oLRRRXMe8FLSUtIYUUUUDFooooGLS0lLSGJS0lLQMKKKKBh3paTvS0DCiiikMWiiigYUtJS0DQGig0UhhRRRQMKWkpaBhS0lLSGhKKKKBi0UUUhhS0lLQMSgUUCgYtFFFAwooopDCiiigYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUopKUUAetfCjXTPYz6NM2Xt/3kOf7h6j8D/OvSa+c/C+rHRPEllfZIjWTbJ7oeD/AJ9q+iwQygg5BGQa9PCz5oWfQ+B4hwnscV7SO09fn1/z+YMoZSrAFT1BHWsa98I+H9QJNzpNqzH+JU2n9K2qK6Gk9zxIVJ03eDa9DhLz4UaBPk273Vq3+zJuH5Guc1H4RX0KM+nahFcY6RyrsJ/HkV69RWUqFN9D0KOcY2k9J39df+CfM2o6XfaRdG2v7WSCUdA44PuD3qpX0prGi2Gu2LWl/AJIz91v4kPqD2NeC+J/Dlz4Z1ZrSbLwt80E2OHX/Ed64a1B09VsfXZVnEMZ7klafbv6f5FLSNNk1jV7XT4gd08gUn0Hc/lX0lbW8dpaxW8K7Y4kCKPQAYrzH4TaFk3GuTJxzDBkf99H+n516nXVhYcseZ9TwOI8Z7XEKjHaH5vcKKKK6j50KKKKACiiigAooooAKKKKACiiigAooooAKKKyNf8AEumeG7Tz9Qn2lv8AVxLy7n2H9aTaSuyoQlUkowV2zXorxnVPi7qtw7LptpDaxdmk+d/8KyF+JPioPu+3ofYwrisHiYI9inkOKkruy+f+R77RXkmjfF24SRY9Zs0kjPBmt+GHvtPBr0+x1Wx1HTl1C1uUktSpbzAemOufTFaQqxnscOKwGIwr/eR079B99f2umWj3V7OkECD5nc4rzvVPi7CkjJpWnmYA8SzttB/Ac1xfjLxTP4l1d2DkWMLFbePPGP7x9zXOVyVcVK9oH0+XcP0lBTxKvJ9OiO+Hxa1zdk2lljPTDf41v6P8WrO4kWLVbNrUnjzYjvQfUdRXkVFYrEVE9z1KuSYGpG3JbzR678VRBqXg+01G1lSaKO4Uq6HIIYEV43Wvbavc2+k3mlljJZ3S8xseEcHIZfQ1kVU6iqPmMcJgpYODpN3V9H5C0lLSVmdYUtJS0AFFFFAxaKKKBhRRRSGLRRRQMKWkpaBhRRRSGFFFFAwoFFAoGLRRRSGLSUtJQMWiiigYUUUUhi0lLSUDFoopyRvLIscalndgqqOpJ6CgZ1fw+8Of274gWWZM2doRJLkcM38K/wBfwr3esLwjoCeHdAgtMDz2HmTsO7nr+XSt2vVoU+SHmfnOb4763iG4/CtF/n8wooorY8sKKKKACiiigArM8Q6VHreg3lg65MsZ2ezDkH8606KTV1ZlQm4SU47o+WWVkZkYYZSQR70VreIrNo/FupWkEbO32twiKMk5PAAr0Dwf8MhGY7/X0BcfMlp2Hu/r9K8uNKUpcqP0SvmFHD0VVqPdaLqznvBvgG58QMl5fB7fTQc56NN7L6D3r2q0tLewtY7W1hWKCMbURRgAVMqqihVAVQMAAcAUtehSpRprQ+JzDMauNneekVsv66hRRRWp54UUUUAFFFFABRRRQAUUUUAFFFFABTHijkGJEVh6MM0+igDNuPD2jXX+v0qzf3MK5/lWbN4B8Lz/AHtIhU+qMy/yNdJRUuEXujeGJrw+GbXzZxz/AAx8MP0tZk/3ZjUDfCnw2en2xfpN/wDWruKKn2NPsbrMsYtqj+84b/hVHh3P373/AL/f/WqRPhb4aQ8x3T/70xrtaKPY0+w3mmNf/L1/ecvB8PPC8Bz/AGYrn1eRj/WtW18O6LZ82+l2iEdxECfzNadFUoRWyMJ4vEVPjm382IqqowqgD0ApaKKo5wooooAKKKKACiiigAooooAbJGksbRyKGRwVZT0INfNfinRH8P8AiK709gfLVt8JP8SHkf4fhX0tXAfFPw0dU0VdUtkzdWIJYActH3/Lr+dYV4c0broetk+K9jX5JbS0+fQ8QooorhPshaKKKQBQKKBQULS0lLQCCiiikMKWkpaBhRRRQMWiiigYUUUUhi0UUUDClpKWgApKWkpFC0UUUDCiiigYtFFFIBaKKKBiUtJS0FBRRRSGFLSUtACUtJS0DCiiigYUUUUhhRRRQMKKKKACiiigAooooAKKKKACiiigAooooAKKVVZ2CqpZj0UDJNdVo/w81/VtrtbizhP/AC0uODj2XrVRjKTskY18RSoR5qskl5nKVJDDLcSCOCJ5XPRUUsf0r2PSvhVo9ptfUJZb2QdVPyJ+Q5/WuystMsdNj8uytIYFHH7tAP1rphhJP4nY8HE8S4eGlGLl+C/z/A8h8KeGfGVjfJe2EH2MH74uW2q49CvU17ND5hhQzBRLtG8IeM98e1PorspUlTVkz5bH5hPGzU5xSt2/UKKKK1OAKKKKAMzxFaLfeG9RtmGRJbuB9cZH6180r0FfTurSiDRr6VuiQOx/75NfMK9BXFit0fV8ON8lRdLofSirWnaXfatOILC0luJD2jXIH1PQV6LoPwldts2uXO0dfs8B5/Fv8K5o05T2R7mIx2HwqvVlr26/ced6dpt7q10LawtpJ5T2QdPqe1eqeGfhdb2hS61xluJhyLdT+7U+5/i/lXd6bpVjpFsLewtY4Ih2Qdfqe9SX19b6bZTXl3KIoIl3Ox7Cuynhox1lqfM43Pa+Ifs8OuVP73/l8h7NBZ2xZjHDBEvJOFVQP5CvPPEHxWtrZnt9FhFzIOPPk4jH0HU1xPi3xpe+Jrlo1LQ6cp/dwA/e929T/KuYrKriXtA9DL8ggkqmK1fbp8+5uaj4v1/VWJudTnCn/lnE2xfyFYzO7nLOzE9yxptFckpN7n01KlTpq0IpLyHpLLG26OV1Yd1YitzTfGviHS2XydSlkQf8s5z5i/rzWBRQpOOzHVoUqqtUimvNHs/hr4m2OqSJa6oi2V03Cvn925+vb8a70HIyK+W69N+HXjaRJotD1OUtG/y20znlT2Qn09K7aGJbfLM+TzbIYwg62G6br/L/ACPV6KKK7T5MZLGssTxt911Kn6GvlzULVrLUrq1cYaGZ0I+hr6mrwD4m6adP8a3MgXEd2qzr9Twf1B/OubEr3Uz3shq2qyp91+RyFFFFcZ9SFLSUtIYUUUUDFooooGLS0lLSGJS0lLQMKKKKBh3paTvS0DCiiikMWiiigYUtJS0DQGig0UhhRRRQMKWkpaBhS0lLSGhKKKKBi0UUUhhS0lLQMSgUUCgYtFFFAwooopDCiiigYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUopKUUAB6V9B+CdTOq+EbCdjmRE8p/qvFfPletfCG936bqFkT/qpRIo9mH+IrpwkrVLdzwOI6PPg+frFr8dD0miiivTPggooooAK5/xh4bTxNojWoKpcxtvgkP8J7/gRXQUUpRUlZmtGtOjUVSDs0U9K06HSdLtrC3GI4ECj39T+Jq5RRQlZWIlJyk5S3YUUUUyQooooAKKKKACiiigAooooAKKKKACiiigChrWqwaJo9zqNyf3cCbsd2PYfia+cNY1i813U5dQvZC0sh4GeEXso9hXqnxjvHi0XT7NThZ5yzD12jj+deOCuLEzblyn1mR4aMaPtnu/yHU4U2nCuVn0MRa1dN16+0vT7+xt5CIL2PZIuenuPfHFZVKKV2noXKEZrlkrodS0lLUM6YhRRRSLCoW4c1NWxr2iHT9H0TUQpCXtud5/2wx/mMVcE3c5cVUjHlT3b0+65g0lLSVRiFLSUtABRRRQMWiiigYUUUUhi0UUUDClpKWgYUUUUhhRRRQMKBRQKBi0UUUhi0lLSUDFooooGFFFFIYtJS0lAxa9B+F3hz7fqbaxcJm3tDiLI4aT1/Afqa4axsp9Sv4LK2XdNO4RR9e9fRmi6TBomj22n24+SFME/wB5u5P1NdOGp80uZ7I8LP8AHfV6Hsov3p/l1/yNCiiivSPgwooooAKKKKACiiigAooooAx7Hw1pthq13qiQh725kLtK/JXPZfQVsUhIAyTiqVzrWl2YP2jUbSLHZplB/LNLSJq5VK0tbtl6iuauPiB4XtshtWicjtGpb+QrLm+K3huP/V/a5T/sw4H6mpdWC6m0MBip/DTf3Hc0V5vL8YdNX/VaXdv9WVapyfGP/nlox/4HP/gKh16fc6I5PjZfY/Ff5nqlFeRP8Yr7+DSLcfWVj/SoW+L+rH7unWY/4ExpfWafc1WRY1/ZX3o9jorxg/F3W88WVkPwb/Gj/hbmt/8APnZfk3+NL6zTK/sHG9l957PRXjI+LutDrZWR/Bv8akX4v6qPvabaH/gTCj6zTE8hxvZfej2KivI0+MV7/Ho8B/3Zj/hVmP4xf89NGP8AwGb/AOtT+sU+5DyPHL7H4r/M9TorzaP4w2B/1mlXS/R1NXIvi1oDY8yG9j/7Zg/1qlXpvqZSyjGx3ps72iuQh+JnheXreSR/9dImFaMPjXw3cf6vWLb/AIE23+dUqkHszCWBxMPipv7mb1FU4dX025H7jULWTP8AcmU/1q2GDDKkEexqk0znlGUfiVhaKKKZIUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFIyh1KsAVIwQe4paKAPnrx34Xfw1rrCJD9guSXt27D1X8P5Vy1fS/iXw/beJdGlsLj5WPzRSY5jcdDXznqem3WkajNYXsZjnhbDDsfQj2NcFanyO62Ps8qxyxNPll8S/HzKtFFFYHqhQKKBQULS0lLQCCiiikMKWkpaBhRRRQMWiiigYUUUUhi0UUUDClpKWgApKWkpFC0UUUDCiiigYtFFFIBaKKKBiUtJS0FBRRRSGFLSUtACUtJS0DCiiigYUUUUhhRRRQMKKKKACiiigAooooAKKKKACijv9a7jw18NdR1cJc6iWsbM8gEfvHHsO341cISm7RMMTiqOGhz1pWRxlvbz3c6wW0LzSscKka5JrvtC+FV9dhZtYmFpEefJjw0h+p6D9a9N0bw9pmg2/lafapGSPmkPLt9TWpXbTwiWs9T5HG8SVZ+7hlyru9/8kY2j+FtH0JB9hskWTvK/wAzn8TWzRRXWkkrI+bqVZ1Zc1R3fmFFFFMgKKKKACiiigAooooAyfE1jean4evLCxZFnuE8sNIcBQTyfyzXJ6N8JtKs9smpzyX0g6oPkj/Lqa9CoqJU4yd2dVLG16NN06crJ9t/vK9pZWthAsFnbxwRDosagCrFFFWczbbuwrxv4o+JWvtU/sW3f/RrU5mwfvyen4fzr1rUrxdP0y6vH+7BE0h/AZr5mnnkuriW4lOZJXLsfcnNcuKnaPKup9Fw7hVUqutL7O3qxtFFFeefaIWiiikaIKKKKQwpQSpDKSGByCOxpKKAPoDwR4g/4SHw7FNIwN1D+6n/AN4d/wARzXSV4p8LdVNl4mayZsRXqFQP9teR+ma9rr1qE+eF2fm+cYRYXFyjH4Xqvn/wQrzb4waT9o0a11SNctaybHP+w3/1wK9JqlrGmx6vo93p8v3LiIpn0PY/gcVc480Wjkwlf2FeNTs/w6ny9RUtzbS2d1NbTKVlhcxuD2IOKirzT75O+qClpKWkMKKKKBi0UUUDFpaSlpDEpaSloGFFFFAw70tJ3paBhRRRSGLRRRQMKWkpaBoDRQaKQwooooGFLSUtAwpaSlpDQlFFFAxaKKKQwpaSloGJQKKBQMWiiigYUUUUhhRRRQMKKKKACiiigAooooAKKKKACiiigAooooAKUUlKKACu/wDhLceX4juoM8S2xP4hh/ia4Cux+GL7fGsAz96GQfpmtKLtUR5+ax5sFVXk/wANT3KiiivYPzMKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAPM/jLbF9H025A4inZT/wJf8A61ePCvovxvor694Uu7OEZuFAlhHqy84/EZH4186EFWKsCrA4II5BrhxMbTufX5HVUsNydYv8x1OFNpwrmZ70RaUUlKKk2Q6lpKWpZtEKKKKRZLa20l5dw2sK7pJnEaj3JxXt/jDwyt74DNhAm6axiV4cDklByPxGa4z4WeHzeaq+sTJ+4tfliyPvSHv+A/nXsWMjB7134al7jb6nxuf5g1ioQpv4Nfn/AMN+Z8q9qK6XxzoJ8P8Aie4hRcW0586A9tp6j8DXNVztOLsz3qNWNWCqR2YUtJS1JoFFFFAxaKKKBhRRRSGLRRRQMKWkpaBhRRRSGFFFFAwoFFAoGLRRRSGLSUtJQMWiiigYUUUUhi0lLWl4f0WbX9bt9PhyA5zI/wDcQdTTSbdkTOcacHOTskehfCrw5tSTXrlOWzHbAjt/E39Pzr1CoLS1hsbOG1t0CQwoERR2AqevWpwUI8p+a4/FyxdeVV/L06BRRRWhxhRRVa91Cz06AzXtzFbxj+KRwBQNJt2RZorz7V/i1pFnuj06CW+kHRvuJ+Z5P5Vwuq/ErxHqW5Y7hbKI/wANuuD/AN9HmsZYiEfM9Shk2Kq6tcq8/wDLc9yu9Qs7CMyXl1DAg7yOF/nXK6j8T/DdiSsU8t247QJkfmcCvDJ7ia6kMlxNJNIerSMWP61HWEsVLoj2KPD9GOtWTfpoen3/AMYrhsrp+lRoOzTyZP5CubvPiP4ovMj7eLdT2gjC/rya5SlrF1Zvdnp0stwlP4YL56/mXbrV9SvSTdahdTZ675WI/KqXXrzRRWbbZ2xioq0VYWiiikWFLSUtAwpKWkpDFooooGgooooGLRRRSAWiig0FCUuKSloGA46cVYhvry3OYLueL/ckI/lVeigGk1qblv4w8RW2PK1i6wOztu/nmta3+J/iWDG+a3nH/TSEZ/MVxvelqlUmtmc08DhqnxU0/kj0m2+MF6uBdaVA49YpCp/Wtq1+LmjyEC5sruD/AHQHH9K8bpa0WJqLqcdTIsDP7FvRs9+tPiB4Zu8AamkTHtMpT+YxW3banYXi5tr23mB/uSA18zUqko25SVb1Bwa0WMl1Rw1OGKL/AIc2vWz/AMj6jor5vtPEmt2GPs2q3cYHbzCR+Rrfs/if4jtsCWS3uVHaSPB/MVqsXB7o8+rwziY/w5J/ge40V5fZfGBOBfaSw9Wgkz+h/wAa6Ky+JXhq7wHuntmPaeMgfmMito16ctmebVyjG0vipv5a/kddRVO01bT79Q1pe28wPTZICauVomnsefKMou0lYKKKKZIUUUUAFFFFABXI+OfBcXiiw82DbHqUA/dOeA4/uN7fyrrqKUoqSszWjWnRmqkHZo+Vri3mtLmS2uImimiYq6MMFTUde+eNvAtt4ngNzb7YNTjX5JMcSD+63+PavC76wutMvZLO9geG4jOGRh+o9R7151Sk4M+1wOPp4uF1pJbr+uhXoFFArM9AWlpKWgEFFFFIYUtJS0DCiiigYtFFFAwooopDFooooGFLSUtABSUtJSKFooooGFFFFAxaKKKQC0UUUDEpaSloKCiiikMKWkpaAEpaSloGFFFFAwooopDCiiigYUUUUAFFFFABRRRQAVe0rSL7W71bSwgaWU9f7qj1J7CtPwt4QvvE91iLMNmh/e3DDgew9TXuOi6HYaBYraWEIRerOeWc+pPeumjh3U1ex4maZ1Twf7uHvT/Bev8AkYHhX4fafoIS5ugt3qA58xh8sf8Auj+tdjTXkSJGeR1RFGSzHAFc5e+P/DNi5STVI3YdRCpfH5V6CUKatsfE1J4nG1HOV5P+vuOlorlLb4j+F7l9g1IRk95Y2UfmRXTW91BeQia2mjmjbo8bAg/lTUoy2ZlVw9Wl/Ei16oloqOeeK2gknmdY4o1LO7HgAd68V8W/EO+1meS202V7XTwcAqcPL7k9h7VFWrGmtTpwGXVcbPlholuz12817SbBtt3qVrC3915QD+VQ2/ijQrtwkGrWbueg80A/rXzkeSSeSepNGBXL9clfY+kjwvS5dajv6I+owwZQykEHoQaWvnrQPGGr+Hpl+z3DS2+fmt5SShHt6fhXt/h7xBZ+I9LW9tDj+GSJj80beh/xropV41NOp4WY5RWwXvP3o9/8zWooorc8kKKK53xF400fw2Cl1P5l1jIt4uX/AB9PxpNpK7NKVKdWXJBXZ0VFeI6t8V9bvWZbCOKxi7YG9/zPH5CuXuPEuuXbZn1e8fP/AE1I/lXPLExWx7NLIMRNXm1H8T6Wor5jj1jVI23JqV4p9RO3+Nb+l/ETxHprjdefa4x1juBuz+PWksVHqjSpw7WSvCaf4HrHxAmMPgfUyDgsgT82Ar5+r1HWvHOn+KfA+oWxBtb9VVjA5yHwwztPf6V5dWGJkpSTR7GRUJ0KMoVFZ836IWiiiuY95C0UUUjRBRRRSGFFFFAF3SLxrDWbK7U4MM6P+Gea+lgQwBByDyK+XM4GfTmvpjSZfP0exlJyXt0Y/ioruwb3R8jxTTX7ufqvyLlFFFdx8ieI/FfQv7P8QR6pEmIL4fPjoJB1/MYrz+vo/wAZaCviHwzdWYAM6jzID6OOn59Pxr5xZWVirAqynBB6g1wV4csr9z7LJ8T7bD8r3jp/kJS0lLWB6wUUUUDFooooGLS0lLSGJS0lLQMKKKKBh3paTvS0DCiiikMWiiigYUtJS0DQGig0UhhRRRQMKWkpaBhS0lLSGhKKKKBi0UUUhhS0lLQMSgUUCgYtFFFAwooopDCiiigYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUopKUUAFdf8ADNd3ja2PpFIf/Ha5Cu5+FMJk8WSSY4itmP5kCtKP8RHBmkuXBVX5M9qooor2D8yCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArzjx18ORqkkuq6Mqpen5pYOize49G/nXo9FTOCmrM6MNiamGnz03qfLEsMtvM8M8bxyodrI4wVPuKaK+gfFngnT/FEJkIFvfqMJcKOvsw7ivD9Z0PUPD9+1pqEJjfqrjlXHqp7159Wk4eh9pl+ZUsWrLSXb/Iz6UUUCsD10OpaSlqWbRCr+j6Tc65qsOn2i5klPLdkXux9hVSCCW6uI4II2kmkYKiKMlia938E+Eo/DOm7pdr6hOAZnH8P+yPYfrWtGk6kvI87NcyjgqN/tPZfr6I29I0q30XSrfT7VcRwrjPdj3J9yavUUV6qSSsj85nOU5OUnds5D4h+G/wC3/DzSQJuvbTMsWOrD+JfxH6ivA6+q68N+JPhU6Jqx1G1jxY3jE4A4jk7j6HqPxrlxNP7aPo8ixtv9mn8v1Rw1LSUtcZ9OFFFFAxaKKKBhRRRSGLRRRQMKWkpaBhRRRSGFFFFAwoFFAoGLRRRSGLSUtJQMWiiigYUUUUhi17f8OvDH9h6N9suUxfXgDMCOUTsv9TXEfDnwodZ1Ialdx5sbVsqCOJJB0H0HU17ZXdhaX22fJ8Q5j/zC03/i/wAv8wooqpqOp2Wk2jXV/cxwQr1Zz19h6mu3Y+UjFydluW6ytZ8R6VoEHmajeJEcfLH1dvoo5rzPxJ8WLm5L22gxm3i6G5kHzn/dHb8a85nuJrudp7iV5pnOWeRiSfxrmniUtInvYTI6k/ervlXbr/wD0XXfi3e3O6HRrcWsfTzpQGc/QdB+tef32oXmpTme+upbiQ/xSMTVaiuSdSUt2fR4fB0MOrU42/P7wpaSlqDqCiiigYUtJS0DCiiikMWiiigYUtJS0DCkpaSkMWiiigaCiiigYtFFFIBaDRQaChKWkpaBhRRRSGHelpO9LQAlLSUtAwooooGFFFFIYUUUUDFVmRtyMVb1U4NbFj4t1/TsC21W4CjojtvX8jWNRVJtbEVKVOorTin6q56BY/FnWIMC8tLa6XuVyjfpx+ldPYfFjRbjC3cFzaN6ld6/mP8ACvGKK1jiai6nl1siwNX7FvTT/gH0fYeJdF1MD7HqdtIT/DvAP5HmtWvlvvnv61q6f4l1vS8fY9TuI1H8Bfcv5Hit44z+ZHkV+F+tGp96/Vf5H0fRXjen/FnV7fC31rb3S92XKN/hXWad8U9Bu9q3QnsnPXzF3KPxFbxxFOXU8evkmNo68l15a/8AB/A7iiqVjq2nakgeyvYJwf8Anm4J/LrV2tk09jy5RlF2krMKwvEnhTTfE9p5V5HtmUfurhB86f4j2rdooaTVmOnUnTkpwdmj5z8S+DdV8MTE3MXm2hOEuYxlT9f7p+tc/X1TLFHNE0UsayRsMMrDII9xXnXiP4UWV6XuNFkFnMeTA/MbfTutclTDtaxPpsHnkJe7iNH36HjdLWnq/h3VtBlMeo2UkQzxIBlG+jDiswVytNaM9+E4zXNF3QUUUUiwpaSloGFFFFAxaKKKBhRRRSGLRRRQMKWkpaACkpaSkULRRRQMKKKKBi0UUUgFooooGJS0lLQUFFFFIYUtJS0AJS0lLQMKKKKBhRRRSGFFFFAwooooAKKKKACur8GeC7jxNdedNui02Nv3kg6uf7q/1PaovBvhGfxPqHz7o7CE/vpR3/2R7n9K93tLSCwtIrW1iWKGJdqIo4Arqw9Dn96Wx89nWcfVl7Gi/ff4f8ESysrbTrOO1tIVigjGFRRwKra5rVpoGlTahetiOMYCjq7dlHua0a8R+LOtPeeIk0tG/cWSAlc9ZGGSfwGK7qk+SN0fJYHDPF4jlk/NnP8AiPxfqnia6ZrmZo7UH93bIcIo9/U+5rCFIKcK82Tbd2fdUacKcVCCshRWromv6j4fvFuNPnZOfniJyjj0IrKp1K7TujZwjUi4zV0z0nxt45i1rwvY29i2xrv5rqPPKbf4T7E/oK84pKWipNzd2LB4Snhafs6e12xaKKKzO9BXX/DjWJNM8Vwwbj5F7+5de2eqn8/51yFaOgkr4h00r1F1H/6EKqEnGSaOfGUo1cPOEtmmfSdFFcN8SfFjaDpS2FnJtv7wEBh1jTu317CvXlJRV2fmOHoTr1FThuzL8d/Ec2Ukuk6JIDcL8s1yORGfRff37V5G8jyyNLK7PI5yzsckn3NR/U5z3pwrzp1HN3Z9zg8HTwsOWC9X3FooorM7kLThTacKRaClpKWkaIWiiikWhaKKKRogooopDCiiigAP3T9K+kfDv/ItaZ/16x/+givm7qMV9L6PH5WiWEf923jH/jortwfxM+W4pf7qmvNl2iiiu8+MCvCfif4e/sjxH9uhTFrf5cYHCyfxD+v417tXP+M9AHiLw1cWiqPtCDzYD6OOg/Hp+NZVYc8T0MtxX1aupPZ6M+caWgqysVYFWU4IPY0V5x9wFFFFAxaKKKBi0tJS0hiUtJS0DCiiigYd6Wk70tAwooopDFooooGFLSUtA0BooNFIYUUUUDClpKWgYUtJS0hoSiiigYtFFFIYUtJS0DEoFFAoGLRRRQMKKKKQwooooGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFKKSlFABXqHwftDu1S8I4+SIH8yf6V5fXuXwzsDZeDoZWGHunaY/ToP0FdGFjepfseHxBV9ngnH+Zpfr+h2NFFFeofn4UUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABWZruhWXiDTXsr2Pcp5Rx96NvUGtOik0mrMqE5QkpRdmj5o1zRrnQNXn0+6HzxnKuBw6nowrPFeyfFjRVutDi1WNP31o4VyB1jbj9Dj868cry60OSVj9CyzF/WsOqj32fqLUkMUk8yQwxtJI7bVRRksfQU+zs7nULuO1tIXmnkOFRRya9t8GeBbfw5Et3dbZ9SdeX6rF7L/jRTpSqPTYvH5lSwULy1k9l/XQi8C+B00CEX98qvqUi9OohB7D39TXbUUV6cIKCsj4DE4mpiarq1XdsKKKKo5wqlq2l2utaXPp94m6GZcH1B7Ee4q7RQ1fQcZOLUo7o+afEGg3fh3V5bC6GdpzHIBxInYisuvovxZ4WtfFOlm3lxHcx5aCbHKN6H2Pevn/AFLTbvSL+WxvYjFPEcEHofceoNedWpOD8j7jLMwji6dn8a3/AMypRRRWJ6gtFFFAwooopDFooooGFLSUtAwooopDCiiigYUCigUDFooopDFpKWkoGLRRRQMK1/Dfh+68SavHZW4KoPmmlxxGnr9fSquk6Tea3qMVjYxF5nPXso7k+gr37wz4ctPDWlra24DSt800xHMjf4egrahRdR3ex5ObZnHB0+WPxvby8y/punW2lafDY2kYSGFdqj19z7mrdNd1jRndgqKMszHAAryTxr8TXnMum6BIUi+7JeDq3snoPevRnONNanxOGwtbGVLR+bOp8XfEPT/Doe1tdt3qPTy1Pyx/7x/pXi+sa5qOv3hudRuWlf8AhXoqD0UdqzySSSSSSckk8mkrgqVZT3PsMFl9HCr3dZd/62FooorM7xaKKKQ0FLSUtAwooooGFLSUtAwooopDFooooGFLSUtAwpKWkpDFooooGgooooGLRRRSAWg0UGgoSlpKWgYUUUUhhS0neloASlpKWgYUUUUDCiiikMKKKKBhRRRQAUUUUAFFFFABRRRQA6N3icPG7Iw6MpwRXQ6b478R6ZgR6g80Y/guBvH5nmucoqoylHZmVWhSrK1SKfqj1PTfi8p2rqmmkeslu2f/AB0/412OmeNfD+q7Vg1GNJD/AMs5vkb9a+e6ODW8cVUW+p42I4dwdTWF4vy2+5n1GrBlDKQQehBpa+btO8Q6vpLA2OozxKP4N2V/I8V2WmfFvUYNqalZRXK93iOxvy6V0xxcHvoeHiOG8VT1pNSX3P8Ar5nrksUc8bRyxpJG3BV1BB/CuN1f4YeH9TLSW8b2Mx53QH5f++TxVnS/iL4d1Lapujayn+C4Xbz9eldTFLHNGJIpEkQ9GRgQfxrb3Ki7nlWxeClreD/r5M8W1L4S63a7msZoL1B0GfLb8jx+tcnf+HtY0wkXmmXUQH8RjJH5ivpigjIwaylhovY9Cjn9eOlRKX4f19x8qdDg8H0NLX0zd6BpF8MXWm2sue7RDP51i3Hw38LXGf8AiW+Vn/nlIy/1rJ4WXRnoQ4goP44tfc/8jwCivbZfhJ4ff/Vy3sX0lB/mK4rxl4U8P+F4hFHqV1PfuMpB8vyj1Y44FZyoTirs7cPm2HrzUIXu/I4iiiisj1AooopDFooooGFLSUtABSUtJSKFooooGFFFFAxaKKKQC0UUUDEpaSloKCiiikMKWkpaAEpaSloGFFFFAwooopDCiiigYUUUUAFavh7QbnxFq8djbAgH5pZMcRp3NZsMUk80cMKF5ZGCooHJJ6CvfvB3hiLwzoyxEBrybD3Eg7n0HsK3oUvaS8jys3zJYKj7vxvb/P5GtpWl2ujabDY2cYSGIYHqT3J9zV2iivUSSVkfnUpSnJyk7thXzl46DDxzq+/r536YGP0r6Nrwn4rWLWvjI3G3CXUKuD6kfKf5CsMSvcPYyKaWIafVHEinCminCuA+xiKKdTadSZtEWlpKWpNYi0UUUjZBXQeCLJr/AMY6bEBlUk81vovP+Fc/Xqvwl0QpFda1Kv8ArP3MGfQfeP54H51pRhzTSPPzXErD4Sc3u1ZerPTiQASegr5s8Waw2u+J729LZj3mOIeiLwP8fxr3/wATXZsfDGp3KnDR2zlT74wK+ZR0rsxMtkfLZBSV51X6DqcKbThXIfToWiiikWhacKbThSLQUtJS0jRC0UUUi0LRRRSNEFFFFIYUUUUATWkJuL2CBeskioPxNfTiII41ReigAV8++CLP7d4y02LGVSXzW+ijP9BX0JXoYNaNnxnFFS9WnT7Jv7/+GCiiiuw+WCiiigDwn4n+Hv7I8Rm9hTFrf5cYHCyfxD+v41xFfRvjPQF8ReGri0VR9oQeZAfRx0H49Pxr5zIZWKsCrA4IPY159eHLL1PtMoxXt6Ci946f5CUUUVieqLRRRQMWlpKWkMSlpKWgYUUUUDDvS0neloGFFFFIYtFFFAwpaSloGgNFBopDCiiigYUtJS0DClpKWkNCUUUUDFooopDClpKWgYlAooFAxaKKKBhRRRSGFFFFAwooooAKKKKACiiigAooooAKKKKACiiigApRSUtAEtrbPeXcNrEMyTOI1HuTivpaxtEsbC3tIx8kMaxj8BivGfhjpH9oeKPtjrmGyTfn/bPC/wBT+Fe3V6GDhaLl3PiuJsTz1o0F9lXfq/8AgfmFFFFdh8wFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAZHii2F34W1SAjO62fH1AyP5V4L4f8Oaj4kvBb2MXyjHmTN9yMe5/pX0bcQJc28sEmTHIhRsehGDUOnabaaVZR2djAkMCDAVR+p9TWFWj7SSbPXwGaPB0Jwgryb07IyvDHhLT/AAxabLdfMunH724YfM3sPQe1b9FFbRioqyPMq1Z1pudR3bCiiimZhRRRQAUUUUAFc74s8I2Ximx2SYivIwfJuAOV9j6iuiopNKSszSlVnSmpwdmj5k1fR77Q797LUITFKvQ/wuPVT3FUK+ldd8P6f4isDa38IYdUkHDxn1BrxHxT4I1PwzK0jKbixJ+W4QdPZh2P6VwVaDhqtj7LL82p4lKE9J/n6f5HM0UUVgewFFFFIYtFFFAwpaSloGFFFFIYUUUUDCgUUCgYtFFFIYtJS0qI8sixxozuxwqqMk/hQMStXQfD2oeIr4W1jFkA/vJW+5GPUn+ldX4a+F99qBS51ktZ23XyR/rX/wDif5161p2mWek2aWljbpDCnRVHX3Pqa6aWGctZaI8DMM9pUE4UPel+C/zM/wANeF7HwzYeRbDfM/M07D5nP9B7VrTzxWsEk88ixxRqWd2OABT5JEijaSRgqKCWZjgAeteF+PvHMniK5awsXZNLibqODOR3Pt6Cuyco0o6HzOGw9bMK7cn6v+vwF8c+P5/EMr2Gns0WlqcE9Gn9z7e1cPRRXBKTk7s+yoUKdCCp01ZBRRRUmwtFFFAxaKKKQ0FLSUtAwooooGFLSUtAwooopDFooooGFLSUtAwpKWkpDFooooGgooooGLRRRSAWg0UGgoSlpKWgYUUUUhh3paTvS0AJS0lLQMKKKKBhRRRSGFFFFAwooooAKKKKACiiigAooooAKKKKACiiigAooooAXtVyw1bUdKk32N7Pbn0RyAfw6VTUMzBVBZicAAZJNen+D/hruEeoa8mB96OzP83/AMK0pwlOVonFj8Xh8NS5q+3bv8jX8Ca/4p1sK99bQvYD/l6ddjN/ugcN+ld7TURI0VI1VUUYVVGABTq9WEXGNm7n5zi68K9VzhBRXZBRRVLV9UttG0u41C7bbDCu4+pPYD3Jq27anPGLk1GO7MTxr4uh8L6Z8m2S/nBEER7f7R9hXgd1dT311LdXUrSzytud2PJNWtb1i617Vp9Qu2zJIflXPCL2UfSs+vNq1XN+R93luAjhKevxPd/oLRRRWR6QUUUUhi0UUUDClpKWgApKWkpFC0UUUDCiiigYtFFFIBaKKKBiUtJS0FBRRRSGFLSUtACUtJS0DCiiigYUUUUhhRRRQMKKK1fDuiy+INct9PiyFc5lcfwoOpppNuyJqVI04Oc3ZI7v4W+F9zHX7tOBlLVSPzf+g/GvVKhtbaGytYra3QJDEoRFHYCpq9elTVOPKj8yx+MljK7qy+XkgooorQ4grmPHHhNPFOj+XGVS9gJe3c9Ce6n2NdPRSlFSVmaUqs6U1Ug9UfLN1aXFhdyWt3C8M8Rw8bjBBqMV9EeKfB2neKLbE6+VdoMRXKD5l9j6j2rw3X/Dmo+G777NfxYUn93Mv3JB7H+lefVouHofa5fmVPFLl2l2/wAjKp1NFOrBnsRFpaSlqTWItLSU5EeWRY40Z3chVVRkk+lI1Wxe0TSLjXdXg0+2Hzyt8zdkXuTX0Xp9hBpmnwWVsu2GFAij6d65vwJ4SXw5pnnXCg6hcAGU/wBwdkH9feutr0sPS5I3e7Pgc8zJYur7Om/cj+L7/wCRz/jhS/gnV1UZP2cn8sGvnAdK+ptQtFv9NurR/uzxNGfxGK+Xp4HtbiW3lGJInKMPQg4NRiVqmdWQTXs5w87jKcKbThXKfRoWiiikWhacKbThSLQUtJS0jRC0UUUi0LRRRSNEFFFFIYUUUUAeifCOw83Wb2/YfLBEI1P+0x/wFewVxfwx037D4SSdlxJdyGU/7vQfy/Wu0r1sPHlpo/OM6r+2xs2tlp93/BCiiitjygooooAK8J+J/h/+yPEZvYUxa3+ZBgcLJ/EP6/jXu1c9418Pr4i8NXFqqj7RGPNgP+2O349KyrQ54noZZivq2IUns9GfOdFKQVYqwIYHBB7Gkrzj7kWiiigYtLSUtIYlLSUtAwooooGHelpO9LQMKKKKQxaKKKBhS0lLQNAaKDRSGFFFFAwpaSloGFLSUtIaEooooGLRRRSGFLSUtAxKBRQKBi0UUUDCiiikMKKKKBhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRXQeDdCOv+JLe2ZSbeM+bOf9kdvxPFVGLk7IzrVY0acqk9lqesfDzRDo/heJ5F23F2fPkz1AP3R+X866ykACqABgDgClr2IRUYqKPy3EV5V6sqst2woooqjEKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKa8aSxtHIiujDDKwyCKdRQB5v4m+FVreF7rRHW1mPJt2/wBWx9v7v8q8s1PR9R0a5NvqNpJA+eCw+Vvoehr6bqveWNrqFu1veW8c8TdUkXIrnqYeMtVoe3g87rUfdq+8vxPl6ivYta+ElhclpdIuWtHPPlSfMn59R+tef6r4G8Q6QWM2nvNEP+Wtv84/TkVyTozjuj6XDZnhcR8MrPs9DnqKVgVYqwKsOoIwRSVkegFLSUtAwooopDCiiigYUCtPT/D2saqwFlp1xKD/ABbML+Z4rs9K+Emoz7X1O8itV7pEN7fn0q405y2Ry18dhqH8SaX5/cedVf03RdT1eQJYWM8/+0q/KPx6V7ZpXw78O6WVb7J9qlH8dwd3P06fpXUxxxwoEiRUQdFUYArojhH9pniYjiSC0oQv5v8AyPJNH+El5Ntk1e8S3TvFB8zfn0H616JovhbR9ATFhZosneZ/mc/ia2aK6YUYQ2R4GKzPE4nSpLTstF/XqFFFcr498UDw1oLNCw+3XOY7ceh7t+H88VpKSirs5KNKVaapw3ZxnxQ8ZmWV/D+ny/u1P+lyKfvH+4Pb1ry6lZmdmd2LMxyWJ5JpK82c3N3Z93hcNDDUlTh/w7FoooqDqCiiigBaKKKBi0UUUhoKWkpaBhRRRQMKWkpaBhRRRSGLRRRQMKWkpaBhSUtJSGLRRRQNBRRRQMWiiikAtBooNBQlLSUtAwooopDDvS0neloASlpKWgYUUUUDCiiikMKKKKBhRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABU1raz311HbWsLzTyHaiIMkmn6fp91ql9FZ2ULSzyHCqP5n0Fe6eEfBtp4YtN52zX8g/ez46f7K+g/nW1Gi6j8jzMzzSngYa6yey/V+RR8G+AbfQFS9vgk+pEZB6rD7L7+9drRRXqQgoK0T8+xOJq4mo6lV3YUUUVRzhXjPxV8Rm91RNFt3/cWh3TYP3pPT8B/OvVde1SPRdDvNRkx+4jLKPVugH54r5pmmkubiS4mYtLKxd2Pcnk1y4mdlyo+gyHCqdR15bR29f+AMoooriPrRaKKKBhRRRSGLRRRQMKWkpaACkpaSkULRRRQMKKKKBi0UUUgFooooGJS0lLQUFFFFIYUtJS0AJS0lLQMKKKKBhRRRSGFFFFAwr2f4XaCLDRG1SVf9Ivfu5HKxjp+Z5/KvJ9G0yTWdZtNPjzmeQKT6L3P5Zr6Qt4I7W3it4l2xxIEUegAwK7MJC8uZ9D5jiXGclKOHjvLV+i/wA3+RLRRRXoHxQUUUUAFFFFABVPU9LstYsZLO/gWaBxyrDp7g9jVyihq44ycXeLszwLxn4JuPC9wJomafTpThJSOUP91v8AHvXK19O6jYW+qafNZXcYeCZSrA/z+tfOOt6VNoms3WnTctC+Fb+8vY/iK87EUuR3Wx9tk2YvFRdOp8S/FFGlpKcqs7BVUszHAAGSTXMe/EOpwBknsK9f+H3gc6cqaxqkX+mMMwQsP9UD3P8AtfypvgX4fix8vVdYiBuvvQ27ciL3b/a/lXo9duHw9vfkfJ51nSmnh8O9Or7+S/UKKKK7T5UK8P8Aip4dbTdeGqwp/ot9yxA4WQdfzHP517hWbrujW2v6PcaddD5JV+Vu6N2YfQ1nVhzxsduX4t4Wup9Nn6HzJThVrVNMudH1OfT7xNs0LbT6EdiPY1VFea1Y+7jJSSa2YtFFFI1QtOFNpwpFoKWkpaRohaKKKRaFooopGiCiiikMKns7WS+vYLSIZkmkWNfqTioK7n4W6R9u8StfOuYrJNwP+2eB+mTVwjzyUTnxmIWHoTqvov8AhvxPZLK1jsbGC1iGI4Y1jUewGKnoor2T8sbbd2FFFFAgooooAKKKKAPB/ib4f/sfxIbuFNtrfZkXA4D/AMQ/r+NcVX0X420AeIvDVxbKoNzGPNgP+2O34jIr50IIJBBBBwQe1efXhyy9T7XKMV7fDpPeOn+QtFFFYnqi0tJS0hiUtJS0DCiiigYd6Wk70tAwooopDFooooGFLSUtA0BooNFIYUUUUDClpKWgYUtJS0hoSiiigYtFFFIYUtJS0DEoFFAoGLRRRQMKKKKQwooooGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFe6/Dzw5/YegLNOmLy8xJJnqq/wr+X8688+Hfhg65rQu7hM2NmwZsjh37L/U17lXdhKX22fI8SZhthYPzf6L9fuCiiiu4+RCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAorkvFnj3TvDH+jgfar8jIgQ42+7Ht9K8u1L4j+JdRclbwWkZ6JbqBj8TzWM68YaHp4TKcRiVzLRd2e/UV83R+LPEMb711m93e8pP866jRPirq1nIqaoiXtv0LABZB/Q1CxUHuddXh/ExjeDUj2msjX/EmneHLPz76XDN/q4l5dz7D+tEfiTTJ/D0mtwzh7OOMux6EEfwkdj2rwHW9Zute1Wa/u2JZz8iZ4jXsop1qygtN2Z5VlMsVVftdIx3737HYal8WNXuJGGn28FpF2LDe/wDhWWvxH8UK+7+0Fb2MK4/lXKUVwOtUb3Ps6eV4OEeVU181f8z0/Rfi1IJFi1qzUoTgz2/UfVT/AEr0yxv7XUrSO7s50mgkGVdDXzJXU+B/FEvh7Wo43cmwuWCTITwueAw9xW9HEyTtPY8jM8gpSpuphlaS6dH/AJM97opAcjIPBpa9A+JM++0PStTGL3T7ef3eMZ/PrXN3fwu8NXJJjgntif8AnlKcfkc12lFS4RlujopYuvS/hza+Z5nP8HbJsmDVrhPQPGGqo3wcl/g1lf8AgUP/ANevV6KzeHp9jsjnONj9v8F/keTr8HJs/NrCfhD/APXq3D8HLUczavO3skQH9a9Noo+r0+wPOsc/t/gv8jh7X4VeHIMGUXVwe++XA/TFdDY+FtC04g2ulWyMOjFNx/M81r0VapwWyOWrjcTV+ObfzEACjAAAHYUtNZ1QZdgo9ScVEt7aO21bqFm9BICau5zKLeqJ6KKKBBRRRQAjEKpZiAAMkmvnTxt4hbxH4knuVYm1iPlW47bR3/E81658SdcOjeE5kibbcXh8iPHUA/eP5fzrwHtXJiZ/ZPpciw1k68vRfqFFFFcp9GLRRRSGFFFFAC0UUUDFooopDQUtJS0DCiiigYUtJS0DCiiikMWiiigYUtJS0DCkpaSkMWiiigaCiiigYtFFFIBaDRQaChKWkpaBhRRRSGFLSd6WgBKWkpaBhRRRQMKKKKQwooooGFFFFABRRRQAUUUUAFFFFABRRRQAVYsbG51K9is7SIyzyttVR/npUMcbzSpFEjPI7BVVRkknoK908DeDo/Dlh9ouVV9SnX943/PMf3R/WtqNJ1JW6Hm5nmMMDS5nrJ7L+uha8IeErbwxYYGJb2Ufvpsf+Oj0FdJRRXqRioqyPzqtWqV6jqVHdsKKKKoyCiiigDzL4waqY7Gx0pG5mczSD/ZXgfqf0ryKus+JGofb/Gt2oOUtlWBfwGT+prk682tLmmz73K6PscJBd9fvCiiisj0BaKKKBhRRRSGLRRRQMKWkpaACkpaSkULRRRQMKKKKBi0UUUgFooooGJS0lLQUFFFFIYUtJS0AJS0lLQMKKKKBhRRRSGFFFOjjeWRIo1LO7BVA7k8CmM9N+Eui7prrWpV4X9xCT69WP8hXqtZfh7SU0TQbPT0AzFGN59WPJP51pkgAknAHU169GHJBI/MszxX1rFSqdNl6IWiuY1T4geHNKkaKW+E0q8FLdd5H49P1rHHxd0Avg298F/veWP8AGm6sFuyIYDFTXNGm7eh39Fc5pXjnw9q7rHb6gscrdI5hsJ/Pg10faqUlLY56lGpSfLUi0/MKKKKZmFFFFABXkPxfsFj1PT9QUYM0bROfUryP0NevVx3j7w1eeJ4dNtbPYuyYtJK54Rcdfesq8XKDSPSymvGhi4zk7LW/3HiNnZ3OoXUdraQvNPIcKiDJNe0eDPAFvoAS+v8AZPqRGR3WH6ep962vDXhTTvDNr5dqm+dh+8uHHzP/AID2rdrOjh1H3pbnbmedzxCdKjpD8X/wAooorpPACiiigAooooA4/wAc+CYvFFoJ7crFqUK4jc9JB/db+h7V4ZfWF3pl29pfW7wTpwUcY/L1HvX1HWdq2haZrlv5Oo2kc6j7rEfMv0PUVhVoKeq3PZy/NpYZezqK8fxR8zUV6vq3weQs0mkagUHaK4GR/wB9D/CuP1D4f+JtOyX05p0H8Vuwf9Bz+lccqU47o+moZlha3wzXz0OZpwp0sMtu5SeJ4mHUOpU/rTayPRi76hS0lLSNELRRRSLQtFFFI0QUUUUhhXvHw70b+yPCsLSJtnuz58meoz90fl/OvIfCmjNr3iS0ssExbt8x9EHJ/wAPxr6JVQihVGFAwAO1duEhq5nynE2LtGOGj11f6C0UUV3nxwUUUUAFFFFABRRRQAV4N8S/D39i+JWuYUxa32ZVx0V/4h/X8a95rm/HPh8eIfDM8CKDcw/voD/tDt+I4rKtDniejlmK+r4hN7PRnzvRRyCQQQRwQe1Fecfci0tJS0hiUtJS0DCiiigYd6Wk70tAwooopDFooooGFLSUtA0BooNFIYUUUUDClpKWgYUtJS0hoSiiigYtFFFIYUtJS0DEoFFAoGLRRRQMKKKKQwooooGFFFFABRRRQAUUUUAFFFFABRRRQAVc0vTbnWNSgsLRN00zYHoo7k+wqoAWYKoJYnAAHJNe4+APCI8P6f8Aa7tB/aNyvz5/5ZL2X6+tbUaTqSt0POzPMI4Kjz/aey/rojodC0a30HSINPth8sY+Zu7t3Y/WtGiivVSSVkfm05yqSc5O7YUUUUyQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArn/GfiIeGvDs14uDcOfLgU93Pf8ADr+FdBXkfxmuXNzpNrn5AjyEepyBWdWXLBtHbl9BV8TGEtv8jzKaeW5uJJ55GkmkYs7scliabTRTq81n3sdNBwp1NFOqWbRLkGpXdvp11p8cpFtclWlTsSvSqtIKUUmzSEUrtLcWiiipN0FHaig9D9KRR9IeHLlrzw1ptwxyz26En3xWpWV4at2tfDGmQMMMlsmfyrVr2ofCrn5RiLe2ny7Xf5hRRXOeLvF9p4UsBJIBLdy5EEAOC3ufQCm2oq7JpUp1ZqEFds6GSSOJC8jqiDqzHAFYdz418N2jlJdYtdw6hX3fyrwfWvEureILhpdQu3ZM/LCpxGv0FZYrkliv5UfR0OH01etPXyPo+z8X+Hr+QR2+r2rOeil9p/WtoEEAggg8givlfAPaum8N+NdW8OTqEma4s8/PbStkY/2T2NEcVr7yDEcOtRvQld9n/mfQdeb+MfiT9gnk07RNklwh2y3LDKofRR3PvVzxd42gTwXDd6XL+91EGOI/xRj+PPoR0rxajEV7e7EMlyiNRutiFonZLzW9y7fatqOpSGS9vZ52PXe5x+XSqY4ORwfUUUVwNt7n2cIxirRVkbuj+L9b0SRTbXsjxA8wzEuh/Pp+Few+E/Gdl4ohKAeRfRjMkDHqPVT3FeA1Z0+/uNMv4b21cpNCwZSO/t9DW1KvKD8jysxyihi4NxVp9H/mfTdFUtI1GPVtJtb+L7k8YfHoe4/OrhIUEk4A5Jr1E7q6PzyUXCTjLdHiHxa1U3nieOwVsx2cQBH+23J/TFcDV/XL46nr1/ek586d2H0zx+lUK82cuaTZ97hKXsaEafZBRRRUnSLRRRSGFFFFAC0UUUDFooopDQUtJS0DCiiigYUtJS0DCiiikMWiiigYUtJS0DCkpaSkMWiiigaCiiigYtFFFIBaDRQaChKWkpaBhRRRSGHelpO9LQAlLSUtAwooooGFFFFIYUUUUDCiiigAooooAKKKKACiiigAoorqfAvhg+I9aBmU/YbYh5j/AHj2X8f5VUYuTsjLEV4UKTq1Hojr/hn4Q8mNdev4/wB44/0WNh91f7/1PavTKRVVFCqAFUYAHYUtevTpqEeVH5njcZUxdZ1Z/LyXYKKKKs5AooooAKRiFUsegGTS1R1qf7NoeoT5x5dvIw/BTQ9EVGPNJLufNup3RvdWvLonJmnd/wAyaq0g6Zpa8hn6TFKKSQUUUUFC0UUUDCiiikMWiiigYUtJS0AFJS0lIoWiiigYUUUUDFooopALRRRQMSlpKWgoKKKKQwpaSloASlpKWgYUUUUDCiiikMK7P4aaN/afidbmRcwWS+afQv0Uf1/CuMr3b4daL/ZHhWGSRcXF4fPkz1AP3R+X863w8OefoeRneL+r4SVt5aL9fwOskkSKNpJGCogLMxPAA714Z42+IF1r1xJZadK8GmKduVOGn9z7e1dx8V9ZfTvDKWULbZL6TyyR12Dlvz4FeHiurEVHflR4GSYGDj9Ymr9v8xw6U4U0U4Vxn1ERa7vwX8QbrR54rHU5Xn05jtDsctD757r7VwlLRGbg7omvhqeJpunUV0fUgljMXmh18sru3Z4x659K828S/FNLeV7TQo0mZThrqTlc/wCyO/1NcZdeM72bwdZ6DG7oEys0meXTPyr9PX6VzNdFXEtq0DxcuyCEJOeJ1s9F+r/yN658Z+I7py0mr3K5/hjO0fkKktPHHiWzcMmrTSAfwzYcfrXPUVy+0nvc+i+p4dx5fZq3oj1vw58U4LqRLXW4ltnY4FxH9wn/AGh2+tejqyuoZWDKRkEHIIr5dr0z4Y+LJEuV0C9kLROCbVmP3T/c+npXXQxLb5ZnzWcZHCFN18MrW3X6o9YoooruPkQooooAKKKKACiiigAooooAKKKKAK91YWl8my7tYZ1PGJEDfzrmNQ+Gnhu+y0dq9o5727kD8jxXX0VMoRlujelia1H+HJr5nkuofB+5TLadqccg7JOm0/mP8K5DVvB2vaKpe70+QxDrLF86/p0r6JpCARg8g1hLCwe2h61DiDFU379pL7n+B8tUte7eI/h7pGuK80KCyvDyJYhhWP8AtL0NePa74d1Hw7e/Z7+HAP8Aq5V5SQex/pXHVoyp77H1GAzWhjNIu0uz/TuZdFFFYnrIKKK0tA0eXXdctdOjz+9b52/uoOWP5UJNuyFUnGnFzlolqeo/CrQvsekS6tMmJrw4jyORGP8AE/yFeg1FbwRWttHbwqFiiUIijsBUtexTgoRUT8vxuJliq8q0uv5dAoooqzlCiiigAooooAKKKKACiiigDwT4keH/AOxPEzzwpttb3MqYHCt/EPz5/GuOr6E8e6ANe8L3CIubm3HnQnvkDkfiM189159eHLL1Pt8pxX1jDpPeOj/QWlpKWsD1BKWkpaBhRRRQMO9LSd6WgYUUUUhi0UUUDClpKWgaA0UGikMKKKKBhS0lLQMKWkpaQ0JRRRQMWiiikMKWkpaBiUCigUDFooooGFFFFIYUUUUDCiiigAooooAKKKKACiiigAoor0LwB4GOpyR6vqkRFkhzDEw/1x9T/s/zq4Qc5WRzYvF08LSdWo9Pz8jR+HHgrb5eu6nFhutrCw6f7Z/p+deo0gAAAAwB2FLXrU6apxsj83xuNqYys6tT5LsgoooqzkCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAryj4zWh/4lN4Bxl4j+hH9a9XrmPHmgP4h8LzW8C5uoSJoR6sO34jIrOrHmg0jty6sqOJhOW3+eh88inUhVlcqylWU4IIwQfSlrzD72I4U6minUmbRFFKKQUoqWbRFooopGqCtLw/pj6xr9lYICRLKN/so5J/Ks2vWfhT4eMFtLrlwmHmHl24I6J3b8T/ACrSlDnmkcOZ4tYTDSqddl6v+rnpSqERVUYVRgD0paKK9c/MhksqQQvLIcIilmPoBXzT4i1ubxDrtzqMzEq7YiX+4g6CvffGUrQ+DdXdCQwtnHHuMV82iuTEyeiPpcgpRtOr12HCnCminCuM+nQ6lpKWkaxH+Y5jWMuxRSSqk8DPWkpKWpNYi0UUUjVBRRRSGe4/C+ZpfBcKsc+XNIo+mc/1roPEV19i8N6lc5wY7ZyD74OKw/hnbNb+CrZmH+ud5B9CeP5VN8RZjD4E1THV0Cfmwr1oO1JPyPzfFRU8ylFbOf6nzyM45696WiiuA+zCiiigYtFFFIYUUUUALRRRQMWiiikNBS0lLQMKKKKBhS0lLQMKKKKQxaKKKBhS0lLQMKSlpKQxaKKKBoKKKKBi0UUUgFoNFBoKEpaSloGFFFFIYd6Wk70tACUtJS0DCiiigYUUUUhhRRRQMKKKKACiiigAooooAKKKKAJIIJbm4jghQvLKwRFHcnpX0P4X0GLw7oUFimDLjfM4/ic9T/T8K85+FXh8XWoS61OmYrb5IMjq56n8B/OvX69DCU7LnZ8VxHj+eosNB6R39f8AgBRRRXYfMBRRRQAUUUUAFYnjF/L8H6sw/wCfZh+fFbdc/wCNwT4K1YD/AJ4H+YqZ/CzfDa1oeq/M+dB0paQdKWvKP0UKKKKBi0UUUDCiiikMWiiigYUtJS0AFJS0lIoWiiigYUUUUDFooopALRRRQMSlpKWgoKKKKQwpaSloASlpKWgYUUUUDCiiikM1/C+kNrviOzscExs+6U+iDk19GKqoiqowqjAA7CvNvhLo3lWV1rEq/NMfJiJ/ujqfxP8AKvSq9PCw5YX7nwXEOL9tivZraGnz6/5fI8c+Ms+dX0yDPCwM5+pbH9K81FemfGWzkXUtNvcHyniaIn/aBz/I15mK56/xs9rKrfVIW/rUcKcKaKcKxPViLS0lLUmsR1FFFI1iLRRRUmqCp7K4e0vre5iOJIpVdT7g1BU9nA11e29ugy8sqoB7k01uKduV82x9NROJYUkHR1DD8afTY0EcSRjoqhR+FOr2z8ldr6BRRRQIKKKKACiiigAooooAKKKKACikJA6nFAYHowP0NAC0UUUAFUtV0qz1mwksr6FZYXHfqp9Qexq7RSaTVmVGUoSUouzR88eKfDF14X1M28pMlvJkwTY4ceh9xWFX0d4j0G38RaPNYzgBiN0UmOY37Gvni9s59PvZrO5TZNC5R1PqK8zEUvZvTY/QMmzL65S5Z/HHfz8yCvYvhZ4e+xaXJrFwmJrsbYsj7sY/xP8AIV5t4W0J/EWvwWIBEOd87D+FB1/PpX0RFEkEKRRKEjRQqqOgA6CtMJTu+dnDxJjuSmsNDeWr9P8Agj6KKZNNHBE0s0ixxqMsznAH416B8WlfRD6K4nVvifoWns0dqZL+Uf8APLhP++j/AEzXIXvxZ1mYkWdpa2y9iwLn/CsZYinHqerh8lxtZXULLz0/4J7LRXgUvxC8USnP9pFPZI1H9KiXx34nU5/teY/VV/wrL65DszvXDGKt8Ufx/wAj6Corwq2+Jnia3I33EM6jtJEOfxFdHp3xe5C6npmB3e3f+h/xq44qm/I563D2Npq6Sl6P/Ox6lRWLo/ivRtdAFjeo0veF/lcfgf6VtVummro8epSnSly1E0/MKKKKZmFfOPjLSP7E8V31oq4iL+bF/uNyPy5H4V9HV5V8YtLzHp+rIv3SYJD7Hlf61hiI3hfsexklf2eJ5HtL+keU0tJS1559mJS0lLQMKKKKBh3paTvS0DCiiikMWiiigYUtJS0DQGig0UhhRRRQMKWkpaBhS0lLSGhKKKKBi0UUUhhS0lLQMSgUUCgYtFFFAwooopDCiiigYUUUUAFFFFABRRRQAUUV6R4H+HjXZj1TWoitvw0NswwZPQt7e3erhTlN2Ry4zGUsJT9pVf8Am/Qq+BfAT6u8ep6pGUsFOY4jwZj/APE/zr2REWNFRFCqowqgYAFKqqqhVACgYAA4Apa9WlSVNWR+eZhmFXG1Oee3RdgooorQ4AooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigDz/AMb/AA6i1tpNS0oJDqB5eM8JN/g3vXjVza3FjdSW11C8M8Zw8bjBBr6lrnvFHg/TvFFridfKulH7q5QfMvsfUe1c1Wgpax3Pdy7OJULU62se/Vf8A+dxTq1Nf8Oaj4bvvs1/FhSf3cy/ckHsf6Vl1wyTTsz7ClONSKlB3TFFKKQUoqGdMRaKKvaRpF3rmpRWNlHulkPJ7IO7H2pJNuyLlOMIuUnZI0vCHhqXxNrKwYK2kWGuJB2X0+pr6AghjtoI4IUCRRqFRR0AHSs7w9oFr4d0mOxtRkjmSQjmRu5NatepQpezjrufnmb5k8bW934Ft/mFFFFbnkmb4gtft3h3UbXGTLbOo+u04r5jHSvq0jIweh6186+NfDk3hzxFPEUP2WdjLbv2Kk9PqK5cTF2Uj6LIKyTlSe71X6nPCnCminCuI+qiOpaSlpGsRaWkpak1iLRRRSNUFS21vJd3UVtCpaWZwiAdyTioq9E+Fnh43epPrU6fuLbKQ5H3pD1P4D9TVU4OclFHNjcVHC0JVpdPz6Hqul2KaZpVrYx42wRKnHfArmPiiSPAl3jvJGP/AB4V2Vcf8T03+BL3/ZZG/wDHhXrTVoNH5xhJOWLhKXWS/M8BooorzT7wKKKKBi0UUUhhRRRQAtFFFAxaKKKQ0FLSUtAwooooGFLSUtAwooopDFooooGFLSUtAwpKWkpDFooooGgooooGLRRRSAWg0UGgoSlpKWgYUUUUhhS0neloASlpKWgYUUUUDCiiikMKKKKBhRRRQAUUUUAFFFFABT4YZLieOCJS0kjBFA7k8CmV23wx0f8AtHxP9rkXMNkvmc/3zwv9T+FXCPPJROfF4hYehKtLoj1vw/pMeh6Ha6fGB+6T5yP4mPJP51p0UV7CSSsj8uqTlUk5y3eoUUUUyAooooAKKKKACsbxZH5vhLVUAzm2c/kM1s1W1CH7Tpl1BjPmwun5gilJXVjSlLlqRl2aPl0dBS0EFSVPBBxRXkn6QFFFFAxaKKKBhRRRSGLRRRQMKWkpaACkpaSkULRRRQMKKKKBi0UUUgFooooGJS0lLQUFFFFIYUtJS0AJS0lLQMKKKKBhUtrbS3t3DawLulmcRoB6k4qKu9+Fejfbtfk1KRcxWS/LnoZG6fkMn8qqnDnkonPjMSsNQlWfRfj0/E9b0nT4tJ0m1sIQNkEYT6nufzq5RRXsJWVj8vlJzk5S3Zh+LPD0fiXQJ7BsLN9+Bz/C46fh2/GvnO5tprK6ltbmMxzxMUdG6givqivOPib4O/tG2Ot2EWbuBf36KOZEHf6j+VYV6fMuZHtZNj1Rn7Gfwvbyf/BPGxThTR0pwrgPsULS0lLUmsR1FFFI1iLRRRUmqCu1+GeiNqXiVb10Jt7EbyccFz90f1/CuSsbK51K9hs7SIyTzNtRR/npX0H4Z0CHw3osVjFhpPvTSY++56munDUueV+iPDz3HrDYd04v3pafLqzZooor0z8/CiiigAooooAKKKgvLy20+0kuruZIYIxl3c4AoGk27Inrntd8a6H4fyl3dh7gf8sIfmf8fT8a818WfE+81NpLTRi9pZ9DN0kkH/so/WvP8lmLEkseSSeTXLUxKWkT6DB5FKa5sQ7eS3+Z6Tqnxf1CYsul2MVsnZ5jvb8un865W78a+JL4nztXuFB/hiOwfpWBS1zSqzluz6CjgMNSXuQX5/mWZNRv5m3SX1059Wmb/GmreXSnK3Vwv0lYf1qClrO7OtRjbY1rTxRr1kQbfV7tcdjIWH611GmfFfW7Qhb6KC9jHUkbG/McfpXAilqlUnHZmNXBYesvfgn8j3vQ/iJoWsssTTGzuW4EdxwCfZuhrrAQRkHINfLPaur8MePtU8POsMjtd2PQwyNyo/2T2+nSumniukzwsZw9pzYZ/J/o/wDP7z3yvK/ixoAUwa7AnUiG4wP++W/p+VehaJrth4gsFu7CYOnR1PDIfQjtVy5tYLyBoLmJZYmIJRhkHByP1FdFSCqQseLg8TUwGJU2ttGvI5X4eeG/7C0IXE6YvbwB5MjlF/hX+v412FFcp418YReGLARw7ZNQmH7mM9FH94+386Fy0oeSIk62PxLaV5Sf9fJE/inxlYeGINsh868cZjt0PP1PoK8X17xRqviKcvfXB8rOUgThF/Dv9TWZdXU99dSXV1K0s8rbndjkk1DXn1a8qj8j7fLspo4OKe8+/wDkLRRRWB64UUUUigooooAVWZGDqxVgchgcEV3fhr4mahpjJb6ruvbTpv8A+WiD6/xfjXB0VcJyg7xZz4nCUcTDkrRuv62PpjTdUstXsku7GdJoX7qeh9COxq5Xzl4f8RX/AIbvxc2Unyk/vYWPyyD39/evePD+v2fiLTEvbNvaSM/ejb0NelRrqpo9z4TNcoqYJ80dYPr28matc74603+1PBuowhcukfnJ9V5/lmuipkiLLE8bDKupUj2NbSV1Y8qlUdOpGa6O58r9qWrGoWpsdSurRhgwzNH+RxVevKZ+jJpq6EpaSlpFBRRRQMO9LSd6WgYUUUUhi0UUUDClpKWgaA0UGikMKKKKBhS0lLQMKWkpaQ0JRRRQMWiiikMKWkpaBiUCigUDFooooGFFFFIYUUUUDCiiigAooooAKfFHJNKkUSNJI52qijJY+gFXdI0a/wBdvVtNPgMsh+8eioPUntXtfhPwRY+GohM2LjUGHzTsPu+yjsK2pUZVH5HmZjmtHBRs9ZdF/n2MLwX8OUsTHqWtIsl0Pmjtjysfu3qf5V6NRRXpwpxgrRPgMXjK2Lqe0qv/ACXoFFFFWcoUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFAGfrWjWevaZLY3sYeNxwe6HsR7188a5o1zoGrz6dc8vGflfHDqejCvpevNvi5pCzaXa6si/vIH8qQ+qN0/I/zrmxNNSjzdUe7kWNlSrqjJ+7L8zyIUopBWroHh+/8R34tbGPOOZJW+5GPUn+leek27I+2lUjTi5zdkiDStKvNa1COysYjJM/5KPUnsK958K+FrTwxp3kxYkuZADPORy59B6AelSeGvDFj4ZsPItV3TPzLOw+aQ/0HtW3XoUKChq9z4nNs4li37KnpBfj/XYKKKK6TwgooooAKzNd0Gx8Raa9lfR7kPKOPvRt6g1p0Umk1ZlQnKElKLs0fOnijwhqPha723C+baOf3Vyo+VvY+h9qwBX1HeWdtqFpJa3cKTQSDDI4yDXi3jP4d3OhF77TA9xp3Vl6vD9fUe9cVWg46x2PrstzeNa1OtpL8H/wTh6WminVyn0MRaWkpak1iLRRU9nZ3F/eRWlrE0s8rbURR1NI0ukrst6Folz4g1eGwtRy5y744jXuxr6G0vTbfSNNgsLVNsMK7R6n1J9zWR4P8KweGNM8vh7yXDTy46n0HsK6OvTw9H2au9z4DOs0+uVeSn8EdvN9/wDIK5vx9D5/gbVlA5EO4fgQa6SqGtW32zQ7+2xnzLd1H12nFbSV4tHk0JclWMuzR8v9qWkAxweo4NLXmH6EFFFFAxaKKKQwooooAWiiigYtFFFIaClpKWgYUUUUDClpKWgYUUUUhi0UUUDClpKWgYUlLSUhi0UUUDQUUUUDFooopALQaKDQUJS0lLQMKKKKQw70tJ3paAEpaSloGFFFFAwooopDCiiigYUUUUAFFFFABRRRQAV7j8MtK/s/wmlwy4lvHMp/3ei/oP1rxS1tnvLyC1jGXmkWNfqTivpi0tks7OC2jGEhjVF+gGK7MJG8nI+Y4mxHLRjRX2nf5L/gk1FFFegfFBRRRQAUUUUAFFV7u+tbCAzXdxFBEOrSMFFc5P8AEfwvC+3+0DJ7xxMw/PFTKcY7s3pYWvW/hwb9EdXRWHpvjDQNVkEdrqUJkPRHOxj+BrcpqSeqIqUqlJ8tSLT89D5n8RWZsPEmpWpGPLuHA+hOR+hFZtdx8VdP+yeLvtIGEu4Vf/gQ4P8ASuHry5rlk0foGEq+1oQn3SCiiipOkWiiigYUUUUhi0UUUDClpKWgApKWkpFC0UUUDCiiigYtFFFIBaKKKBiUtJS0FBRRRSGFLSUtACUtJS0DCiiigYV9AeBdF/sXwtaxOuJ5h50v1bt+AxXj3gzRv7c8UWlsy5hRvNm/3V5x+JwK+ha7cHDeZ8nxNi9I4aPq/wBAoooruPkQoPIwelFFAHh3xG8If2FqP9o2UeNPum5UDiJ/T6HtXD19Ma5pUOt6LdafMAVmQgE/wt2P4GvmqaGS3nkglGJInKMPcHFefiKfLK66n2uS414ijyT+KP5dBtLSUtcx7sR1FFFI1iLU9nZ3GoXcdraQtNPIcIijk1o+H/DOpeJLryrKLEQOJJ34RPx7n2Fe2eGfCOn+GbbFuvm3Tj97cOPmb2HoPataVCVTXoebmWb0cFHlWs+3+ZT8F+C4PDNr58+2XUpV/eSdkH91f8e9dZRRXpxioqyPgMRiKmIqOrUd2woooqjEKKKKACiiigCC8vLfT7Oa7upVigiUs7t0ArwDxl4yuvFN8QC0Wnxt+5gz1/2m9/5Vt/FDxY2p6kdFtJP9DtW/fFT/AKyQdvoP5157XFXq3fKtj6zKMvVKKr1F7z28l/mFLSUtcx7yFpaSlpFIKWkpaCkApaQUtIpC0UUUikamga/e+HdTS9s39pIyflkX0NfQei6xa67pUOoWjZjkHKnqjd1PuK+aK7j4Z+IW0rXhp0z/AOiXx24J4WTsfx6V0Yeryy5XszxM6y6Nek60F70fxR6/rer2+h6Rcahcn93EvC92bsB9TXztquqXOs6nPqF2+6aVs47KOwHsK7L4peITf6wukwPm3szmTB4aQ/4D+tcDRianNLlWyKyLAKhR9tJe9L8F/WoUUUVynvi0UUUFBRRRSKCiiigAooooAK2/C/iO48Nawl3ES0DYWeLPDr/iO1YlFUm4u6Iq0oVYOnNXTPp61uob20iurdw8Mqh0YdwamrzP4T68ZYLjRJ3yYv3sGT/CfvD8Dz+NemV61KfPFSPzLH4SWExEqL6benQ+e/iHafZPHGoqBgSlZR/wICuZrv8A4vW/l+KbWbH+utRk/RiK4CuCqrTaPtMBPnwtOXkv8hKWkpazOwKKKKBh3paTvS0DCiiikMWiiigYUtJS0DQGig0UhhRRRQMKWkpaBhS0lLSGhKKKKBi0UUUhhS0lLQMSgUUCgYtFFFAwooopDCiiigYUUVb0/Tb3VbtbWxt3nmb+FR09yewppX2FKSiuaTsipXYeFfAGoeIClzchrTT+vmMPmkH+yP612nhb4Z2mmmO71fZd3Y5WLrHGf/Zj+ld+AAAAMAcACuylhesz5XMuIkr08J/4F/l/mUdJ0aw0OyW00+BYox1Pdj6k9zV+iiu5JJWR8hOcpycpO7YUUUUyQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArnPHkAn8EaopGSsO8fUEGujqK5tobu3e3uI1khkGHRuhFKSumjWhU9nVjPs0zwnwl4Fv/EjrcS7rbTgeZiOX9lHf69K9t0rSLHRbFLOwgWKFfTqx9Se5q4iJGioihUUYCqMACnVnSoxprzO3H5nWxkve0j0X9bhRRRWp5oUUUUAFFFFABRRRQAUhAIIIBB4INLRQB4z8RvBSaTIdY02PbZytiaJRxEx7j2P6V59X0/fWcOoWE9ncIGhmQowPoa+atTsJNL1S6sJfv28pjJ9cdD+Vefiaai7rqfa5FjpV6bpVH70fxRWpaSrul6Ve6zfJZ2EDSzN6dFHqT2Fctm9EfQc0YRcpOyRFaWlxfXUdraxNLPIdqIo5Jr3HwX4Lg8NWvnz7ZdSlX95IOiD+6v9T3qbwj4Ns/DFtvOJr+RcSzkdPZfQV09ehQw/J70tz4vOM6eJvRo6Q6vv/wAAKKKK6j54KPaiigD5i1+yOm+IdRsyMeVcOo+meP0rOruvixpv2PxaLtVwl5CHz/tLwf6VwteZNcsmj7/C1fa0IT7oKKKKk6RaKKKQwooooAWiiigYtFFFIaClpKWgYUUUUDClpKWgYUUUUhi0UUUDClpKWgYUlLSUhi0UUUDQUUUUDFooopALQaKDQUJS0lLQMKKKKQw70tJ3paAEpaSloGFFFFAwooopDCiiigYUUUUAFFFFABRRRQB1Pw7sft3jSzyMpAGmP4Dj9TXvdeT/AAgs915qd6R9xEiB+vP9K9Yr08LG1O/c+B4irc+Ncf5Ul+v6hVDVdZsNEszdajcpBEOm48sfQDual1LUINK024vrltsMCF2P0r5y8Q+Ib3xLqr3t2525xFFn5Y17Af1NXVq8i8zky7L3i5Nt2it/8j0bUPjFAshXTdLeVR0knfbn8BmqcHxivRIPP0mBk77JCD+ua8xFOFcbr1O59RDJ8Elbkv8ANn0B4d8e6P4hkW3R2trs9IJsAt/unoa0fEviC38N6NJfTjc/3Yo88u56CvnFWZWVlYqynIIOCD61ua/4ovfEVvp8d2ebSLYTn/WN/ePvjFaLFPld9zinw9T9vFwfudV/Xcq6xrV/r1611qE7SMT8qZ+VB6Adqz6KK4223dn1VKEYRUYqyQV33gnx/c6bdQ6dqszTWLkKkrnLQntz3X+VcDRThOUHdGeKwlLFU3Tqq6/L0PY/izpf2zw3DqEYy9nICSP7jcH9cV4tXv8A4YYeJPh5BBdnd5tu1vIT7ZAP8q8HvbSXT764s5xiWCQxsPcGuqur2mup89k83BTwst4N/d/w5BRRRXOe0LRRRQMKKKKQxaKKKBhS0lLQAUlLSUihaKKKBhRRRQMWiiikAtFFFAxKWkpaCgooopDClpKWgBKWkpaBhRRVvTNPl1XVLawgH7yeQIPYdz+AotfQJSUYuUtker/CnRfsujzarKuJLttsef7i/wCJz+VehVBZWkVhYwWkC7YoUCKPYCp69inDkion5hjcS8TiJVn1f4dAoooqzlCiiigAr558eWwtfG+qIowrSCT/AL6AJ/U19DV8+fEGcT+OdTI6I6p+SgVzYr4Ee/w9f6xL0/VHNUtT2Vjd6jOILO3luJT0WNSTXoGg/Ce8uCs2tXAto+vkRHc5+p6D9a4o05T+FH1NfG0MMr1ZW/P7jz+2tri8uEt7WGSaZ+FSNck16V4a+FbuUudefavUWsZ5P+839BXomkaBpmhQeTp1okPHzPjLN9T1NaVdlPCpay1PmsbxDVqLkw65V36/8AgtLS3sbZLe1hSGFBhUQYAqeiiuo+dbbd2FFFFAgooooAKKKKACsDxnrv8Awj3hi7vVOJyPLh/324H5dfwrfryH4yamXvdP0tW+WNDO49zwP5Gs6suWDZ25fQVfERg9t38jzAszMWYksTkk9zRSUteafdoKWkpaCkLS0lLSKQUtJS0FIBS0gpaRSFooopFIKfHI8UqSxsVdGDKw7Ecg0yloKHzSyTzSTSuXkkYs7HqSepptHaikUtAooopFC0UUUFBRRRSKCiiigAooooAKKKKANrwnqJ0rxVp91nC+aI3/AN1uD/Ovoqvl0MVYMDgqcg19NWM32jT7ab/npErfmAa78HLRo+O4opJSp1e9193/AA55Z8ZY8XWky+qSL+RB/rXmFesfGZR9n0du/mSj9Fryes6/8Rndk7vg4fP82JS0lLWJ6YUUUUDDvS0neloGFFFFIYtFFFAwpaSloGgNFBopDCiiigYUtJS0DClpKWkNCUUUUDFooopDClpKWgYlAooFAxaKKKBhRRRSGFKASQACSeAB3rovD/grWPELK8MPkWp63Ewwv4Dqa9a8OeBtI8PBZUj+03g63EoyR/ujoK3p0Jz12R5OOznDYT3b80uy/V9Dzzw18NNQ1XZc6nusbQ8hSP3rj6dvxr1rSNF0/Q7QW2n2yQp3I5Zj6k9TWhRXoU6Maex8Xjs0xGMfvu0ey2/4IUUUVqecFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFeFfFC1W38ayyKMCeFJD9cYP8q91rktc8D23iLxNDqV/KTawwiPyF4MhyTyew57VjXg5xsj1MoxcMLXdSo9LM8m8MeENR8T3GIF8q0U4kuXHyj2Hqa9w0Dw7p/hyxFtYxYJ5klbl5D6k/wBK0be2htLdILeJIoUGFRBgAVLRSoxp69R5jmtXGPl2h2/zCiiitjygooooAKKKKAOB+LOkG+8LrfRrmSxkDnH9w8H+leH19T3lrFfWU9pOu6KaMxuPYjFfMeqadLpOq3WnzjElvIUPuOx/EYrjxMbPmPqcixHNTdF7rX5MqUUUVzHvi0UUUhhRRRQAtFFFAxaKKKQ0FLSUtAwooooGFLSUtAwooopDFooooGFLSUtAwpKWkpDFooooGgooooGLRRRSAWg0UGgoSlpKWgYUUUUhh3paSloASlpKWgYUUUUDCiiikMKKKKBhRRRQAUUUUAFFFFAHs/wmtvK8LzTkczXLfkAB/jXe1yvw5jEfgewwMbt7H/vo11VexRVqaPzLM58+Mqvzf4aHA/Fy7eDwjHApIFxcqre4AJ/mBXiAr3L4tWvn+DRMBkwXCOfocr/WvDRXJiPjPosjt9V07scKcKaKcK52e5EdS0lL2qTeI6iiipNohRRT4YZLieOCJS0kjBFA7knAoKbS1Z7p8Nomi8EWZb+NncfTdXEfFnQfsmqQ6zCn7q6Hly47OOh/Efyr1fSLBdL0i0sUxiCJUJHcgcn86g8Q6NFr+h3WnS4Hmr8jf3XHIP516kqV6SifnNLHqnj5YhfDJu/o2fNNFS3VtNZXc1rcIUmhco6nsRUVeefbJp6oWiiigoKKKKQxaKKKBhS0lLQAUlLSUihaKKKBhRRRQMWiiikAtFFFAxKWkpaCgooopDClpKWgBKWkpaBhXp/wn0HdJca5OnC5ht8jv/Ef6fnXnWm6fPqupW9hbLmWdwo9vU/gK+jtL06HSdMt7C3GIoECD39T+JrqwtPmlzPofP8AEON9jQ9hF6y/L/g/5lyiiivRPhgooooAKKKKACuBtvhfZT6lcajrN1JdzTytK0UfyIMnOPU131RXNzBZwNPczJDEo+Z3YACplGMviOihiK1G6pOzfbcisNNstMgEFjaxW8Y/hjXGfr61arh9S+Keg2bFLUT3rDvGu1fzNYUnxhl3/utGUL23T8/yrN16UdLnbDJ8fW97kevfT8z1WivL7b4wRlsXekOq+sUuf0IrrdG8c6DrbLHBdiGc9IpxsY/TsfwpxrU5bMyr5VjKC5p03by1/I6OiiitTzwooooAKKKKACiiigAr59+JN0bnx3f+kQSMfgo/rmvoKvm/xsS3jbWCf+flhXPiX7qPcyGN68n5fqjBpaSlrhPrEFLSUtBSFpaSlpFIKWkpaCkApaQUtIpC0UUUikFLSUtBQvaijtRSKQUUUUihaKKKCgooopFBRRRQAUUUUAFFFFAAelfSmg/8i9pv/XrH/wCgivmyvpjTIvJ0mziP8ECL+Siu3Bbs+V4pf7umvN/oecfGZh9n0de5eU/oteT16f8AGWTN3pMXojt+ZA/pXmFTX/iM3ydWwcPn+bEpaSlrE9MKKKKBh3paTvS0DCiiikMWiiigYUtJS0DQGig0UhhRRRQMKWkpaBhS0lLSGhKKKKBi0UUUhhS0lLQMSgUUdKBi0Vu6H4P1rX2U2lqUgPWeb5U/D1/CvTtB+GOk6Ztmvyb+4HOHGI1Psvf8a1p0Jz2PNxmbYXCaSleXZb/8A8v0Lwpq/iFx9itSIc8zyfKg/Hv+FepeHvhppWklJ77/AE+6HOXH7tT7L3/Gu1REjRUjRURRgKowBTq7qeGhDV6s+Txue4nE3jD3Y+W/zYgAVQqgADgAdqWiiug8QKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKQkAZJxQAtFIHVujA/Q0tABRRXFeLviFaeH5GsrRFu78feXPyRf7x9fapnOMFeRvh8NVxM/Z0ldna0V8/X3jvxJfuWbU5IVPRIBsA/Ln9apw+KvEED701m9z/ALUpYfka5njI32PfjwxiHG7mk/mfRteTfF3w8Q8GvwJwcQ3OP/HW/p+VRaD8VbyCRYdaiW4hJwZ4lw6+5HQ/pXpbjTvE+hSIsiXFldxldyn1/kRWnPCtFpHF9XxOVYiNSotO62aPmSitDW9HuNB1i4025B3xN8rY4dezD6is+uFq2h9bCSlFSjsxaKKKRYUUUUALRRRQMWiiikNBS0lLQMKKKKBhS0lLQMKKKKQxaKKKBhS0lLQMKSlpKQxaKKKBoKKKKBi0UUUgFoNFBoKEpaSloGFFFFIYd6Wk70tACUtJS0DCiiigYUUUUhhRRRQMKKKKACiiigAooooA+gfAS7fBGlj1jJ/8eNdHWD4KXb4M0kf9MAa3q9mn8CPyzGu+JqP+8/zKGs6ZFrOj3WnT8JcRlM/3T2P4Gvm7VtJu9E1OawvYyk0Rxnsw7MPY19QVheJvCun+KLHybtNkyD91Og+ZD/Ue1RWpc6utzsyvMfqsnGfwv8PM+cRThWv4h8M6j4Zvfs97HmNj+6nUfJIPb39qyBXntNOzPtqVSNSKnB3THUvakpe1QdMR1FFFSbRCvQfhd4ca+1NtZuE/0e1OIcj70nr+A/U1yfh7QbrxHq0djbAgHmWTHEadyf6V9CaZp1tpOnQWNogSGFdqj19z7munDUuaXM9keBn+ZKhS+rwfvS38l/wS3RRRXpHwh5R8VvC5DL4gtI+OEugo/wC+X/ofwryyvqW5t4ru2lt50EkUqlHU9CDXzz4u8MzeGNae2IZrWTL28h/iX0+orhxFOz5kfW5JjvaQ+rzeq281/wAD8jBooormPoQooopDFooooGFLSUtABSUtJSKFooooGFFFFAxaKKKQC0UUUDEpaSloKCiiikMKWkpaAEpaSup8D+FX8S6sGmUjT7chp2/vHsg+v8qqMXJ2RnXrwoU3VqOyR2vwu8Mm0tG1y6TE1wu23BHKp3P4/wAq9HpqIscaoihUUYUAcACvPvFnxPtdKlksdIVLu7U4eUn93Gf/AGY16i5aMLM/PKjr5liXOKu3+CPQmZUUsxCqOpJwBWTc+KNCtG2z6taKR1Hmg/yr5/1TxFq+tSF9Qv5pQTwm7ag+ijisvA9KwliuyPWo8PK372f3f1+h9Hw+MfDk7bY9ZtCT6vj+dbENxDcx+ZBLHKh/iRgw/SvlrA9Kt2OpX2mTCWxu5rdx3jciksU+qLqcOQa/dz18z6eoryzwt8U2eVLPXwoDHat2gwB/vD+or08zRCAzmRfKC794PG3Gc59K6oVIzV0eBisFWws+SovTszK8SeI7Pw1pjXd0dzt8sUIPzSN6D/GvCtf8Sal4juzNfTEoD+7hU4RB7D+tTeLfEMviTXZbosfsyEpbpnog7/U9awq8+vWc3ZbH2eUZXDC01Oa99/h5L9RaKKK5z3UFFFFIZ3HhP4iXujSR2mpO91p+cbjy8Q9j3HtXs9tcw3ltHc28qywyqGR1OQRXzBXo/wALfEj298dDuHzBPlrfJ+6/cfQ/zrsw9dp8kj5fPMohKm8TRVmt13Xf1PXaKKK9A+LCiiigAooooAK+c/HieX451Yes278wDX0ZXz98TIfK8eXx7Osbj/vkVz4n4Ue3kL/2iS8v1RyVLSUtcJ9agpaSloKQtLSUtIpBS0lLQUgFLSClpFIWiiikUgpaSloKF7UUdqKRSCiiikULRRRQUFFFFIoKKKKACiiigAooooAu6Tam+1iytQMmWdFx7Z5/SvpYAAADsK8O+GenG+8XxTFcx2kbSk+/Qfzr3KvQwcbRbPiOJq3NXhTX2V+f/DHifxduPN8VW0IP+ptRkfUk1wNdL8Qbv7Z441FgciJliH/AQK5qsKrvNs9vAQ5MLTj5ISlpKWszsCiiigYd6Wk70tAwooopDFooooGFLSUtA0BooNFIYUUUUDClpKWgYUtJS0hoSiiigYtFFWbHTr3U5xDY2stxIe0ak/n6Ubg5KKvJ2RWpQCxCgEsegHU16Jovwnv7nbLq1ytrH1MUXzv+fQfrXomjeEdF0IA2dknmjrNJ8zn8T0/CuiGGnLfQ8XFZ/haOkPfflt9/+VzyLRPh5rus7ZHh+xW5/wCWlwMEj2XrXpWh/DnRNH2yyxm+uRz5k4+UH2Xp/Ouvorrhh4Q8z5rF51isTpflj2X+e4iqFUKoAA4AHalrJ1jxLpGhLnUL2OJyMiMfM5/Ac1zEnxa0JXwlveOv97ywP61pKrCOjZyUcBiq65qcG0d7RXLaV8QfD2qyrCl2beVuAlwuzJ+vSuozkZHSnGUZK6ZjWw9WhLlqxafmLRRRVGIUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABSEhVJJAA5JPalrzn4r+JJNO02LSLWQpNeAtKwPIjHGPxP8jUzkoxuzfDYeWIqqlHqUfFnxUMU0ll4fCMVJVrtxkZ/2R3+przm913VtSkL3mo3MxPYyHH5DiswU+vOnUlJ6n3OFwNDDxtCOvfqWIby6gcPDczxsOhSQg12Ph/4maxpcqR6g5v7Towf/AFij2bv+NcOKdUKcou6Z01cLRrx5akUz3nxJ4ztbPwb/AGtp0yyPdDy7Y9wx65HqOa8Kd3kdpJGLuxLMxOST6043EzWq2zSsYEYusZPAY8E1HTq1XUepGW5fDBRlGOrb38uiFooorE9VBXb/AA18RSaXrqadK5+x3rbdpPCSdiPr0riKmtJWhvbeVDhklVgfoRVQk4yUkYYvDxxFCVKXVHs/xH8Jf2/pX220jzqFopKgDmRO6/XuK8I6HkY9Qa+rEbeit6gGvH/iZ4KNnNJr2mxf6PIc3USj/Vt/fA9D3rvr0r++j43Jsfyv6vUfp/keaUUUVxn04UUUUALRRRQMWiiikNBS0lLQMKKKKBhS0lLQMKKKKQxaKKKBhS0lLQMKSlpKQxaKKKBoKKKKBi0UUUgFoNFBoKEpaSloGFFFFIYd6Wk70tACUtJS0DCiiigYUUUUhhRRRQMKKKKACiiigAoooPSgZ9F+El2eEdJX/p1T+VbNZnhxdvhrTF9LWP8A9BFade1D4UflGJd6035v8woooqjEqalplnq9jJZ30CzQOOVYdPcehrwzxn4KuPC1wssbNPp0rYjlI5Q/3W9/fvXv1VNT0621bTZ7G6QNDMpU+3oR7isqtJTXmejl2Y1MJU7xe6/rqfMdL2q1qmny6Tqt1p8/+sgkKE+o7H8RVXtXltW0P0GElJKS2Y6rulaVd61qMVjZRF5pD+CjuT6CpNE0O+1/UFs7CLc3V3P3Yx6k17t4Y8L2XhjT/Jtxvnfmadhy5/oPataNF1HfocGZ5tTwUOVazey/V/1qO8MeGrXwzpa20GHmf5ppiOXb/D0FbdFFenFKKsj4CrVnVm6k3dsKKKKZmFY3ibw7a+JtIksrjCuPmhlxzG/Y/T1rZopNJqzLp1JU5KcHZo+YdU0y60fUZrC9j8ueI4I7EdiPUGqlfQXjHwhbeKbDjbFfRD9zNj/x0+xrwa/sLrS76SzvYWhnjOGVv5j1FedVpOD8j7rLswhi6faS3X6+hWooorE9IWiiigYUtJS0AFJS0lIoWiiigYUUUUDFooopALRRRQMSlpKWgoKKKKQwpaSt7w14U1DxNdhLdDHbKf3tww+VfYep9qqMXJ2RnVqwpQc6jskV/D3h+88SaotnaLhRzLKR8sa+p/oK+gNG0e00PTIrCzTbHGOT3Y9yfc1HoWg2Ph7Tls7GPao5dz96RvUmptX1FNJ0e71CT7tvEz49SBwPzr0aNFU1d7nwmaZnPH1FTp6Q6Lu+7PPfib40ezDaDpspWd1/0qVTyin+Ee57+1eQipbq6mvrya7uHLzTOXdj3JqKuSpNzldn0uCwscNSUI79fNjqWkpazO5C96WkHWlpM0QtdXZeNLq38F3uhSO7M+Et5P7iH7y/4fWuUpaFJx2Jq0KdZJTV7NP5oWiiipOlC0UUUjRBRRRSGFWLC7ew1C2u4zh4JVkB+hqvQehp7CklJWfU+oYpBLCki9HUMPxp9UNEYvoOns3U28ZP/fIq/XtJ3Vz8nnHlm49gooopkBRRRQAV4r8YLIw+I7S8A+W4t9pPup/wIr2quJ+J+iNq3hVriFN09i3nAAclejD8ufwrKtHmgz0MrrKliot7PT7zwelpKWvOPuEFLSUtBSFpaSlpFIKWkpaCkApaQUtIpC0UUUikFLSUtBQvaijtRSKQUUUUihaKKKCgooopFBRRRQAUUUUAFFFXdJ02bV9WttPgB3zuFz/dHc/gKaV3ZClJQi5S2R618KtI+x6BLqEi4kvH+XI/gXgfmc13UsiwxPI5wqKWJ9hUdnaxWNlDaQLtihQIo9gKwvHmo/2Z4M1CUNh5I/JT6tx/LNevFKnC3Y/Mq9WWNxbl/M/+AvwPAL+6a+1G6u2OTNM0n5nNQUnalrzWfeJJKyEpaSlpFBRRRQMO9LSd6WgYUUUUhi0UUUDClpKWgaA0UGikMKKKKBhS0lFAxaWtrSvCOvayQbTTpRGf+Wso2L+ZrutJ+ESDbJq9+W9YrcYH/fR/wrSFGctkcOIzLC4f4569lqzysAswVQWY9ABkmun0fwB4g1fa4tPssJ/5a3Hy8ew6mvZ9K8M6Noqj7DYRRuP+WhG5z+J5rXrphhF9pnhYniST0oRt5v8AyOA0f4U6TZ7ZNSlkvpB1T7kf5Dk/nXcWlla2EAhtLeKCIdFjUAVPRXTGnGHwo+fxGMr4h3qyb/L7goooqzmCuH8f+NT4fhWwsCDqMy5LHkQr6/U9q7g8DJr5s8Q6g+qeIr+8kJPmTMF9lBwB+QrnxFRwjp1PayPBQxNdyqK8Y/n0KM08tzO888jyyucs7nJJ+tMpBS15jPv4JJWQV6X8N/GU0d1HoWozF4ZOLaRzko39zPoe1eaU5JHikSSNisiMGUjsR0q6c3CV0YY3B08XRdKfy8n3PqKis7QtRGraFZX46zRKzfXv+taNeundXR+Xzg4ScJboKKKKZIUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFeCfFKZpfHVwjZ2xQxqv0xn+te914f8XLMweLYrnHy3Fspz7qSP8ACsMR8B7GRtLFa9mcEKeKYKeK88+ziLTqbTqTNYiilpBS1LNoi0UUUjVBVrTrdrrU7S3QZaWZFH5iqtdn8MtIOo+KVumXMNkvmE/7R4UfzP4VUI80kjDGV1QoTqvov+GPcAAqgDoBikkjSWNo5FDIwwysMgj0p1FeyflZ4V4+8CyeH7htQsEZ9LkbkDkwE9j7ehrh6+qpoYriF4Zo1kicFWRhkEehrxLxz8PZtDeTUdLRpdNJy6DloP8AFfftXFWo296J9XleaqolRrP3uj7/APB/M4KiiiuY94WiiigYtFFFIaClpKWgYUUUUDClpKWgYUUUUhi0UUUDClpKWgYUlLSUhi0UUUDQUUUUDFooopALQaKDQUJS0lLQMKKKKQw70tJS0AJS0lLQMKKKKBhRRRSGFFFFAwooooAKKKKACkPQ0tIehoGfS+irs0OwX0t4x/46KvVW08bdNtV9IUH6CrNe2tj8lqO82/MKKKKZAUUUUAeJ/FizW38VxXCjH2i3Vm+oJH8gKyvCvgvUPE0wdQYLFT89ww6+yjua9e1zwfY+IdatL7UGZ4baMqIBwHOc8n09q6CGKO3hWKGNY40GFRRgAVyfV+ablLY+jWeOjhIUqXxpb9v8yjouh2GgWC2lhCEQcsx5Zz6k960aKK6kklZHz05ynJym7thRRRTJCiiigAooooAK53xV4QsfFNntmHlXcY/c3CjlfY+o9q6Kik4qSszSlVnSmpwdmj5o1vQdQ8PX7WmoQlG/gcfdkHqDWbX03qukWOtWTWmoW6zRN0z1U+oPY1454o+G2o6KXudPDXtiOflH7yMe47j3FcFWg46rY+wy/OaddKFX3ZfgziKKKK5z3ApaSloAKSlpKRQtFFFAwooooGLRRRSAWiiigYlLSVLBBNczLDBE8srHCoikk/hQO6WrI6khgluZlhgieWVzhURck/hXd6D8LNSvts2qyCygPPlj5pD/AEFen6J4a0nw/Fs0+1VHIw0rfM7fU10U8NOWr0PFxmfYeh7tP35eW33/AOR534Y+Fs05S614mKPqLVD8x/3j2+gr1W1tLextktrWFIYUGFRBgCpqK7qdKNNaHyGMzCvjJXqvTt0QVxXxUuGg8Dzqpx500cZ+mc/0rta4n4qwGbwPMwH+qmjc/nj+tOp8DJwNvrNO/dHg9LSUteYffIdS0lLSLQo60tIOtLSZohaWkpaRohaKKKRaFooopGiCiiikMKUKXIVerHApK2fCenHVfFOn2uMr5od/91eT/Kmld2Iq1FTpynLZK/3H0Fp8P2fTbWH/AJ5xKv5AVZoor20fk8m5NthRRRQIKKKKACkZVdSrKGVhggjgilooA+efHXhZ/DOuMI1P2C4Je3b09V+o/lXMV9M+INBtPEWky2F2vytyjgcxt2YV8765ol54f1SSwvUxInKuPuyL2YVwVqXI7rY+yyrHrEQ5Jv3l+Pn/AJmdS0lLWB7CFpaSlpFIKWkpaCkApaQUtIpC0UUUikFLSUtBQvaijtRSKQUUUUihaKKKCgooopFBRRRQAUUUUAFesfCnw75UEuu3CfNKDHbgjovdvx6fhXAeGNAm8R63DYx5EX353H8CDr+J6Cvoa2t4rS2it4ECRRKERR2ArswtK7530PmeIsf7On9Wg9Zb+n/BJa8p+MWqfLp+lI3UmeQfov8AWvVq+cfGerf214svrpW3RK/lRf7q8D+p/GunEStC3c8LJKHtMTzvaJhUtJS1559kJS0lLQMKKKKBh3paTvS0DCiiikMWiiigYUtJU9taXN7II7W3lnc9FjQsf0oC6SuyE0V1+m/DTxHqG1pLdLOM/wAU7YP/AHyOa7DTPhDp8O19Svprlu6RDYv+NaRoVJdDgrZthKO87vy1PIBycAZJ7Ct7TPBniDV9pttOlWM/8tJv3a/rXuemeGtG0gD7Dp0ETD+PblvzPNa1dEcJ/Mzx6/Ej2ow+b/yX+Z5XpXwg+6+raj9YrZf/AGY/4V2+leDtB0fa1rp0XmD/AJayje35n+lbtFdEaUI7I8TEZlisR8c3bstF+AdBiiiitDhCiiigAoorjfiD4wPhnTUgtCDqNyCI88+Wvdv8KmUlFXZrQozr1FThuzR8Q+M9H8NjZdzmS5IyLeL5n/H0/GuFufjHclz9k0iNU7GWUk/oK8xklkuJnmmkaSVzud3OSx9SaSuKeIm3pofX4bJMNCP7xczPW9M+L0M0gi1TTzAjcebC24L9Qea8snKm6mKNuUuxVvUZqGnCsJ1JTXvHp4TA0cNJypK1xRS0gpayPTiFFFFIo9y+GExl8FwqTnyppEH55/rXZVxHwrQr4P3H+K5cj9BXb169H+Gj8yzRJY2rbuwooorU4AooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAK4n4leGJde0NLi0TfeWRLqg6up+8B78A/hXbUVMoqSszahWlQqKpDdHyngg4III6g04V7T41+G8OsNJqOkhIL88vF0SY/0b3rxu5tbixupLa6heGeM4eNxgivOqU5Qep9zgsdSxUbw36ojp1NFOrJnoxFFLSClqWbRFooopGqFALEKoJYnAA6k1794H8Pf8I94ejilUC7n/ezn0J6L+Arhvhp4SN5crrl9H/o8Lf6MjD77/3voP5169XfhaVlzs+N4izFTl9VpvRb+vb5f1sFFFFdh8sFIwDKVYAqRgg96WigDy3xl8MBM0moeH0VZD80lnnAb3T0PtXk8sUtvM8M0bxyocMjjBB9xX1VXPeJPBmk+Jos3UXlXQGEuYhhx9fUexrmqYdPWJ72AzqVNKnX1Xfqv8z50orqPEfgLWfDzNI0X2qzHS4hGcD/AGh1FcuK45RcXZn1FKtTrR5qbuhaKKKk1ClpKWgYUUUUDClpKWgYUUUUhi0UUUDClpKWgYUlLSUhi0UUUDQUUUUDFooopALQaKDQUJS0lLQMKKKKQw70tJ3paAEpaSloGFFFFAwooopDCiiigYUUUUAFFFFABSgZIHqaSnxDM8Y9XA/WmD2Ppy1G20hHpGo/SpaZCMQxj0UU+vbR+SS3YUUVgeKvFdl4W07z7j95cSZEEAPLn+g96TaSuyqdOdWahBXbNi7vLawt2uLueOCFfvPI2AK4LV/i3pdqzR6bbS3rDjzCdif4n8q8s17xHqfiO8NxqE5ZQfkhXhEHsP61l1xzxLfwn1GEyKnFXru77dDvrj4t6/KxMMFnAvYBC38zUUfxW8SIcubSQehix/I1w9LWPtZ9z1VluESt7NHqNh8YZQwGo6UpXu1u+P0P+NdxonjPQ9fIS0uws5/5YzfI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qumwaxpdzp9yuYZ0KN7eh/ConHmi0dODr+wrxqdj5dpavazo91oWrT6ddqRJE3DdnXsw9jVGvMas7M++hJSipR2Y6lpKWkaoUUtIKWkWhaKKKRaAU6minUi0FL3pKXvSLQUUUUFoKKKKQwoorqPAfh46/wCIo/MTNpakSzHscdF/E/yqoxcnZGVevChSlVnsj1fwLox0XwraxSLieYedL6gt0H4DFdLRRXsRiopJH5bXrSrVZVZbt3CiiiqMgooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAMDxX4XtPE+ltBKqpdICYJ8co3p9D3FfPt1az2N3NaXKGOaFyjqexFfUNeOfFvSVttZtdTjXAukKSY7svf8j+lcmJpprnR9HkGNlGp9Xk9Ht6nngpaSu58G/D641xo77UVeDTs5VejzfT0HvXFGEpuyPrK+JpYan7Sq7Io+DvBlz4muxLIGh06Nv3kvd/9lff37V7pZWdvp9nFaWsSxQRLtRFHAFOtraCzto7e2iSKGMbURBgAVLXpUqKprzPg8yzOpjp3ekVsv66hRRRWx5gV418WtJNtrltqaL+7uo9jn/bX/wCtj8q9lrmvHeif254VuoY1zcQjzofqvb8Rmsq0OaDR6GV4n6vioyez0fzPnylpKWvMP0EKSlpKQxaKKKBoKKKKBi0UUUgFoNFBoKEpaSloGFFFFIYUtJ3paAEpaSloGFFFFAwooopDCiiigYUUUUAFFFFAFnT7KTUtStrKIZeeVYx+Jr6WtoEtbWK3jGEiQIv0AxXkfwp0Q3WrzatKv7q1XZGSOrn/AAH869hr0cJC0ebufD8S4pVK8aMdo/m/+AFFFFdZ82FFFFAFPVNPh1XS7mwnXMc8ZQ+3ofwPNfM13ayWV5PaTDEkMhjYe4OK+pa8F+JtgLLxpcOowt0izfj0P8q5cVHRSPoeH67VSVJ7PX7jj6Wkpa4j61C0tJS0i0FKKSlFItC9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VXhTb+R3VFefD4uaNnmzvAP90f41et/ij4anIDy3EHvJCcfpmkq9N9TSWU42Ku6TOzorLsfEejamQLPU7aVj0UOA35HmtStE09jinTnTdpqz8wooopkBRRRQAUUUUAcN8SPCi61pR1G1j/0+0UngcyJ3X6jqK8PFfVPavnzx3oY0LxTcRRrttrj9/D6AHqPwOa4sTT+2j6rIMa5J4efTVfqjm6KKK4z6hAKdTRTqRaCl70lTWtrcXt1HbWsLzTSHCogyTQXdJXZFXpvgn4dGUxaprkRCcNFaMOT6F/8ACtrwb8O4NH2X+qhJ7/qkfVIf8T713tdtHDW96Z8lm2fcydHCvTrL/L/MQAKAAAABgAdqWiiu0+UCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAzdc0Sz8QaXJY3qZRuVcfeRuzD3r5/8Q+H73w3qj2V4uR1ilA+WRfUf4V9JVl69oFj4i017K9jyOqSD70beoNYVqKqK63PXyvNJYOXLLWD/AA80fNlFbPiTwzf+Gb8292u6Jj+5nUfLIP6H2rGrzmmnZn3NKpCpFTg7phRRRSNBaKKKQC0Gig0FCUtJS0DCiiikMKWk70tACUtJS0DCiiigYUUUUhhRRXUeD/Bt14nuxI4aLTo2/ezf3v8AZX3/AJVUYuTsjKvXp0KbqVHZIPBvg+fxPe75N0WnxN+9lH8X+yvv/KvdbOzt7C0itbWJYoIl2oijgCksbK206zitLSJYoIhtVFFcj4p+JGmaC72toBfXy8FEb5EP+0f6CvSpwjRjdnwWNxeIzWty017q2X6s7bOBk1k33ifQ9NJW71W1jYfw+YCfyFeE6z4z13XWb7VfOkJ6QQnYg/Lk/jWB3+tRLFfyo7KHDzavWn93+f8AwD39/iT4VRsf2kW91hc/0qWH4heFpyANVjTP/PRGX+Yr58paz+tTOz/V/DW+J/h/kfTtnq2nagAbO+t58/8APOQGrlfLCM0bh42ZGHRlODXVaL8Qtf0cqrXP2yAf8srj5uPZuorSOKX2kcVfh2aV6M7+T0PfaK5bw1470rxHthVvs16Rzbynr/unv/OuprpjJSV0eBWoVKM+SorMy/EGjtr2jy6b9qe2jmIEjooLFe4GfWufsPhd4as8GWCW7Yf89pDj8hiu0oocIt3aLp4qtThyQk0vIpWWk6dpyhbKxt4Md44wD+dXaKKpKxhKTk7ydwooooEFFFFABRRRQBn66jSeHtSRfvG1kA/75NfMC/dH0r6tkRZY2jYZVgVI9jXy5f2zWWpXVq4w0MzoR9CRXJiVsz6Xh+atOPoVxTqaKdXGfTIWnCm04UjRC0opKUUjRC0tJS0jVBRRRUmiCiinIjSOqIpZ2IVQO5PSgD1r4RWJj0y/v2H+ulEan2Uc/qf0r0isnw1pI0Tw9Z2GPnjjzIfVzyf1rWr2KUeSCR+YZjiFiMVOqtm9PRaIKKKK0OI8z+MrN/Y2mKPum5Yn/vk149X0B8R9Ek1rwlMIE3XFqwnjUdTjqPyzXz+OlcGITU7n2ORzjLDcq3TY4U4U0U4VzntoUUtIKWpNoi0tJS0jWItFFFI1QUUVJBDJc3EcES7pJWCKPUk4FANpK7PoPwbuPg7St2c/Z16/pW7VbTrRbDTbW0X7sESxj8BirNezFWikflFeanVlNdW3+JxXxTsTd+CppVGWtpUl/Dof0NeDV9S39nFqGn3FnMMxzxtG34ivmPULGbTNRuLGcYlt5DG34d65cTHVM+kyGsnTlS6p3+8r0CigVyn0CHCgUCgUikLS0lLQWhaWkpaRSCiiikWhaXvSUvekUgoNFBpFoWiiigtBRRRSGFFFFABRRRQB1fw6ujbeNrIA8TB4j+Iz/MCvea+fPA6lvGulAdps/oa+g69HBv3GfD8TxSxUX/d/VnnvxX8QHTtEj0qB9s99neQeRGOv5nj868TFdH471c6z4vvZg26GFvIi9Nq8fqcmucrGtPmkz1ctw6oYeMer1fzFoFFArI9BC0tJS0ikLRRRQMWiiigtC0UUUhoWiiikUgpaSloKOv8Ah34gbRfEaQSvi0vSIpATwrfwt+fH417vXy0CQcqcMOQfSvo7wvqf9seGrC+Jy8kQD/7w4P6iu3CTunFnyfEeFUZRxEeuj/Q16bJIkUbSSOqIoyzMcACqOs61Y6FYPeX8wjjXhR/E59AO5rxDxT411DxLM0ZY29gD8lup6+7Huf0rarWjTXmeVl2V1sbK60j1f+Xc7vxF8U7OyL2+jRi7mHBmbiMfTu1eaar4l1jW3LX99LIp/wCWanag/AVk0tefUrTnuz7bB5ZhsKvcjr3e/wDwPkFFFFZHpBRRRSGHfPet3SvGGu6My/ZdQkaMf8spjvX9elYVFUpNO6M6tKnVjy1IprzPYdB+KtjeMsGrw/Y5Tx5q/NGfr3FegQzRXEKywyLJG4yrocgj618vVu+HPFmp+GpwbWUyWxOXtnPyN9PQ+4rqp4trSZ83j+HKc054XR9un/APoeisbw54m0/xLZefZvtkUfvYGPzRn/D3rZrvTUldHx9WlOlNwqKzQUUUUzMK85+L2nCbRbPUVX57ebYx/wBlh/iP1r0auZ+IMAuPA+pgjOxBIPwYGs6qvBo7cuqOniqcl3/PQ+fqKKK8o/RUAp1CIzuqIrMzHAVRkk16P4V+F893svNd3QQ9VtlPzt/vHsPbrVQhKbtEwxOMo4WHPVdvzfocl4d8L6l4lufLs4tsKnElw4+RP8T7CvbPDXhLTfDNtttk8y5YfvLhx8zfT0HtWxaWlvY2yW1rCkMKDCogwBU9ehSoRp69T4vMc4rYz3F7sO3f1CiiitzyAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAp6npdnrFjJZ30CzQOOQex9QexrxTxb4BvvDrvc2wa603PEgHzR+zD+te70jKGUqwBUjBBHBrKpSjUWu56GAzKtg5e7rHqj5Zor2HxT8L7e+L3miFba4PLW7f6tz7f3T+leUahpt5pV01tf20kEy/wuOv0PevPqUpQep9tg8woYuN6b17dStRRR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SloGgooooKCiiikMKKKKACiiigAooooAWvZfhPfm48Nz2bHJtZzj2Vuf55rxqvSvg/MRqGqQZ4MSP+Rx/Wt8M7VUeNn1NTwM32s/xPWaKKK9U/PAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArjPibrb6R4UeKBys963kqR1C9WP5cfjXZ15F8ZpybzSbfPCo8mPqQP6VnWdoM78spKrioRe2/wBx5eKcKbThXmn3aHClFIKUVLNYi12knj28Hgi20WB3W6G6OWfuIh90A+p6fQVxYp1Ck43sKrh6dZx9or8rugpaSlqDsiFFFFIo9S+GnjCWSVdB1CUvx/osjHnj+A/0r1KvmKyuXsr63uomKyQyK6kexr6ahkE0Eco6Oob8xXpYWo5R5X0PheIsFChWVWCsp/mv8x9FFFdR86FFFFABRRRQAUUUUAFFFFAHk3xmtG36TegfL88RPvwR/WvKq+hPiFox1nwhdJGu6e3/AH8f1XqPyzXz2Oa4MRG079z7HJaqnhlHrHT9RaKKKwPYQ6iiikWhaWkpaCkLRRRSLQtFFFIpC0UUUi0LR3oo70ikLRRRQWgooopDCiiigAooooA9L+EN4VvdSsSfleNZQPcHB/mK9ZrxX4Ubv+Etlx0+ytn81r2qvUwrvTPz7iGCjjpNdUgoooroPEPH/i/ahNZ0+6Ax5sDIT64P/wBeuI0jRr/XL5bPT4DLIfvHoqD1J7CvaPGnhGXxXd6YonWC3gLmZ+rYOMAD14re0bRNP0GxW00+BY0H3m6s59Se5rjlh3Oo29j6ehnMMNgoQjrPX0Wr3/yMfwl4JsfDMIlOLjUGGHnI6ey+grqaKK6oxUVZHztevUrzdSo7thRRRVGQUUUUAFFFFABRRRQAUUUUAFRR20EMkkkUMaSSHc7KoBY+571LXM+IvHWjeHC0M8pnux/y7w8sPqeg/GlJpK7NaVKpVlyU1ds6aivFtQ+LmszuRY2ttax9iwMjf4fpWavxM8VBsm+jPsYEx/KsHiYI9WGQ4uSu7L5/5HvdFeP6Z8XdQicLqdjDPH3eH5GH4Hg/pXo+m+J9M1fSJtRspw6QoXkQ8MmBnBFaQqwnszkxOW4nDazjp3Wx558UvE7zXn9g2shEMWGuSp++3UL9BXm1TXd1JfXs93KxaSeRpGJ9zmoa8ypNzldn32BwscLQjSj039eotFFFZncgooopDPVfhX4keUSaDdSFtimS2JPbuv8AUfjXp9fN/hy/bTPEen3ikjy51De6k4P86+kOoyK9PCzcoWfQ+D4iwkaOJVSK0nr8+oUUUV0nz55b8YdMBh0/VFX5lYwSH2PI/XP515PXv/xHtBd+BtQ4yYQso/AivAK8/ExtO59pkdVzwvK/str9QpaSlrA9pC0tJS0ikFFFFItC0vekpe9IpBQaKDSLQtFFFBaCnRxvLIscSM8jnCqoySfQCtnw/wCFdU8RzBbOHbADh7iThF/HufYV7J4Y8FaZ4ajEka+fekfNcSDn6KOwralQlU16HlZhnFDBrl+KfZfr2OV8HfDURGPUNeQFx80dp1A939fpXpwAUAAAADAApaK9KnTjBWR8JjMbWxlTnqv0XRegUUUVZyBVDW9Mj1nRbzT5fuzxFAfQ9j+eKv0UNXVioycZKS3R8q3FvLaXMttMpWWJyjg9iDg1HXofxY8Pmx1mPWIExBefLJgcLIP8R/I155XmTi4yaPvsLXVelGouotAooFQdKFpaSlpFIWiiigYtFFFBaFooopDQtFFFIpBS0lLQUKKTvSik70ikLRRRSKClpKWgaCiiigoKKKKQwooooAKKKKACiiigBa9F+EKn+2dSfsLdR/49XnVerfCC1ItNTvCOGkWNT9ASf5it8Or1UeTnk1HAVL9bfmj02iiivVPzkKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAoooo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GNSt0zdWILHA5aPuPw6/nWleHNG66HFk+L9hX5JfDL8+h4hS0gpa88+0QtFFFAxaKKKC0LRRRSGhaKKKRSClpKWgoUUnelFJ3pFIWiiikUFLSUtA0FFFFBQUUUUhhRRRQAUUUUAKis7qiKWZjhVAySfSvd/AnhQeHNJ8y4Uf2hcgNMf7g7J+H86574c+Cjb+Xrmpx4lIzawsPuj++ff0r02vQw1G3vyPi8/zVVX9WovRbvu+3yCiiiuw+XCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAPAPiNKsvjq/wBv8ARD9Qo/xrmYo3mkWOJGkkbgKi5J/AV67/wq9tU1u71PWr44nmaTyLf0J4BY+2OgrtNJ8O6TocezT7GKE45fGXP1Y81w/V5Tk29D61Z1Qw1GNOHvNJLy+88k0H4YaxqZSW/xp9uecOMyEey9vxr1HQfCGj+HlBs7YNPjmeX5nP49vwreorphRhDY8TF5ricVpJ2j2X9ahRRRWp5oUUUUAFFFFABXlfxo3+Ro/wDc3yfngV6pXJ/ETQH17wtKsC7rq2PnxAdWx1H4j+VZ1U3BpHbl1WNLFQlLb/PQ+fqcKbThXmn3qHClFIKUVJrEUU6minUjVBS0lLUm0QooopFCH7p+lfSHhpWTwzpiv94WyZ/KvnvSrCTVNVtbGIZaeVU+gzyfyr6WhiWCGOJBhUUKo9gMV3YNatnyfFNVctOn11Z4T8TdKGneMJpkXEV4gmH+90b9Rn8a42vXvjHZhtO02+A5jlaMn2YZ/mK8hrOtHlmzsyqq6uEg3utPuFpaSlrI9JC0UUUi0LRRRSKQtFFFItC0d6KO9IpC0UUUFoKUDJAAJJ4AHer+kaJqGu3YttPtmlb+JuioPUntXsXhT4f2GgbLq623eodd7D5Yz/sj+ta0qMqm2x52YZrQwUfed5dlv/wDlvBvw2e5Meoa7GUh+9Hangv7t6D2r1lESONUjUKijCqowAKdWVr/AIgsPDmmte30mB0jjH3pG9AK9GFONKOh8Ji8ZiMwqpy17JdC3qGo2mlWUl5ezpDBGMszH9B6mvGPF3xJvdaMlnphe0sDwWBxJKPc9h7Vz/ibxVqHii+867bZAh/c26n5UH9T71h1zVa7lpHY+gy/KIUbVK2svwQtFFFc57YtFFFIYUtJS0DCiiigYUtJS0DCiiikMWiiigYUtJS0DQUd/p0oopDO88KfEq90kpaaqXvLIcB85kjH1/iHtXaat4P8O+N7QanYSrFPIMi5g6MfR19fyNeH1seHvEuoeGr4T2UmY2P72Bj8kg9/Q+9dEK2nLPVHj4vK7y9thXyT/BlnX/A2t+Hy0k1v9otR/wAvEA3Lj3HUVzgr6P8ADviSw8Taf9otGw4GJYG+9GfQ+3vWT4g+HOi63umhj+w3Z58yEYUn3XpWksOmrwZwUM7lTn7LFxs11/zX+R4PS10fiDwRrPh3dLPCJ7Qf8vEPKj6jqK5wVyyi4uzPo6NanWjz03dC0tJS1B0oWiiikaIs6fYXGp6hBY2qbppnCqP6/QV9FaHpEGh6Pb6fbj5Yl+Zv7zdyfqa474Z+FTp1l/bN5Hi6uVxCrDmOP1+p/lXoVejhqXLHme7PheIMx+sVfYU37sfxf/ACiiiuo+eCis/Utc0zR499/fQweis3zH6DrXF6l8W9MgJTT7Oe6YdHf5F/xqJVYR3Z14fAYnEfwoNrv0+89EorxO9+KniC4JFuttaqf7qbj+Z/wrDuPGPiO6z5usXQB7I+wfpisHi4LY9elw1ipfHJL8T6Ior5mk1TUZv9Zf3T/wC9Kx/rUP2q5/5+Zv8Av4aj64ux1LhaXWr+H/BPp+ivmRNRvojmO9uV+krVeg8U6/bf6rWLwY7GUkfrTWMXVEy4Wq/ZqL7v+HPo2ivCbX4leJrbAa7inA7TRA/qMVv2PxfmUgX+lIw7tBJg/kauOKpvfQ4qvDuNh8KUvR/52PV6K5LTfiP4c1EhWu2tZD/DcLt/XpXUwzxXEYkglSWM9GRgQfxFbxnGWzPJrYatQdqsWvVElFFFUYBXPeN9JfWvCN9aRDMwTzIx6svOPx6V0NFJq6sXSqOnNTjutT5S+vFOr0L4k+C20y8fWtPiJsp2zOij/VOe/wDun+deeivMnFxdmfoGExEMRTVSHUcKdTRTqzZ3RHUtJS1DN4hRRRSND1b4Q6dtttQ1Jl++whQ+w5P8xXp1YXg/Sv7H8LWNowxJ5fmSf7zcn+dbtevRjywSPzLM8R9Yxc6i2vp6LQKRlV0KsAysMEHoRS0VqcB86eNNA/4R3xLcWiA/Z5P3sB/2D2/A5Fc/XtHxa0cXegQ6mi/vbN8Mf9huD+RxXi9ebWhyzsfeZZifrGHjJ7rR/IWiiisj0BaKKKC0LRRRSGhaKKKRSClpKWgoUUnelFJ3pFIWiiikUFLSUtA0FFFFBQUUUUhhRRSqCzBVBZicAAZJNACV6X4B8Am4aLWNYiIhGGt7dx9/0Zh6egq14I+HXlGPVNciBkHzQ2rdF939/avTq7qGH+1M+SznPFZ0MM/V/ov8wpaKK7j5AKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAPFPiX4O/sm9Os2Mf8AoVw375FHETnv9D/OvPxX1JeWcF/ZzWl1GJIJlKOp7g188eK/DU/hjWXtJNz27/Pbyn+NP8R3rhr0uV8y2Pr8mzD20fY1H7y281/wDEFKKQUorlPoIiinU2nUjVBS0lLUm0QoorrPBPg6bxLeie4Vk0yJv3j9PMP91f6mnGLk7IzxGIp4em6tR2SOn+Ffhpl3a/dJjcClqCO38Tf0H416jTIYo4IUhiRUjRQqqowAB2p9etTpqEeVH5rj8ZPGV3Vl8vJHE/FWISeCZGxzHPGw/PH9a8Kr3f4pyBPA84/vzRqPzrwiuTE/GfTZDf6q/V/oLS0lLXOe4haKKWkWgooFFIpC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NoYD/wAtrj5F/AdTTjFydkRUrU6MeapJJeZz1XtM0bUdZn8rT7OWdu5UfKPqegr1jRfhVpVltl1OVr6Yc7Puxj8Op/Gu5trW3s4FhtoY4Yl6JGoAFdMMJJ6y0PBxfEdKHu0FzPu9F/n+R5poXwmUbZtcudx6/Z4Dx+Lf4V6Np+mWWlWwt7G1jt4h/Ci4z9fWrdFdcKUYfCj5jFZhiMU/3stO3T7gooorQ4wooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigBAAOgxS0UUAFFFFABRRRQAUUUUAFFFFABRSEgDJIAHc1n3Gv6PasVn1SzjYdQ0yg/zpNpblRhKWkVc0aKoW2t6VeNtttStZWPZJlJ/nV+hNPYJRlF2krBRRVDWdYs9C02S/vZNsSdAOrHsB70NpK7CEJTkoxV2y8Tjk8Vi33i7QNOYpc6rbq46qrbiPyzXjfiTxxqviGZk81rayz8tvG2Mj/aPc/pXNVxzxdvhR9VhOGnKPNiJW8l/me8r8R/C7Pt/tHHuY2x/KtvT9c0vVR/oN/BOf7qON35da+a6dG7xSLJG7I6nIZTgj8ahYyXVHVU4Yw7X7ubT87P/ACPqKivHvCvxMu7KSO01tmuLU8C4/jj+v94frXrsM0dxAk0MiyRSKGVlOQQe9dlOrGoro+Xx2XVsFPlqrR7PoySuI+Kmp/YfBz26tiS8kWH/AID1b+WPxrt68b+Md+ZdX0+wU/LDEZGHuxx/IUq0rQZWV0vaYqCfTX7jzWiiivPPtwpaSlpDCiiigYtFFFAxaKKKQwpaSloGFFFFAwpaSloGFFFFIYtFFFAwpaSloGgooopDCiiigYUCigUDFooopDOzvfGcn/CBafoVrIRMUZLlx/CgY4UfUfpXGUtJVSk5bmNDD06Cagt22/Vi0UUVJ0IKWkpaQxKKKKBi0UUUDDtRR2opDCiiigYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAV1ngPxS3h3WBFO5+wXTBZR2Ruz/4+1cnRVRk4u6McRQhiKTpVFoz6jBDAEHIPIIpa4X4Z+IzqujHTrh83VkAASeXj7H8On5V3VevCanFSR+Y4vDTw1aVGe6CiiirOcKKKKACiiigBrusaM7HCqMk+gr5j1e9Oo6ze3pP+vndx9CeK968d6p/ZPg+/mDYkkTyY/q3H8s188dBXHipapH1PD1G0Z1X10FooorkPoxaKKKC0LRSZrV03w5rOrkfYdNuJVP8AHt2r+Z4oSb2FKcYLmm7IzKK9G0z4R6hPh9TvYrZT1SIb2/Pp/Ou20r4d+HdLKv8AZPtUo/juDu/TpWscPOW+h5dfPMJS0i+Z+X+Z4npuh6prEm2wsZ5/9pV+UfUniu50j4R3k22TVr1Lde8UHzN+fSvXI40iQJGiog6KowBTq6YYWK+LU8TEcQ4ippSSivvf9fIwNH8GaFom1rWxRph/y2m+d/zPT8K36KK6FFRVkeJVq1Ksuao235hRRRTMwooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArjfGPxAs/DWbS3VbrUSP9Vn5Y/dj/SrnjjxOPDGgPPHg3k58u3U/wB7u30H+FfPcsslxM800jSSyMWd2OSxPc1z163LpHc9vKstWI/e1fh/P/gGxq/irW9ckZr2/lKHpFGdiD8B/WsfA9KQUtcLbb1PrqVONOPLBWQo4ORwR3rpNC8b65oMiiG6ae3B5gnJZSPY9RXNinUlJxd0XOjTqx5aiuj6K8MeKLHxRYefbHZMnE0DH5kP9R715J8QfEba54gkgicmysyY4wDwzfxN/T8KwNH1i80O+N3ZSbXMbRsOxBHf6dfwqj1OTyTWtSu5wUTz8DlFPC4mVVarp5dwpaSlrmPeQUUUUigr0/4VeIn82TQbhyUKmS2yen95f6/nXmFa/he6ay8U6XOpxi4VT9CcH+da0puE0zhzLDRxOFnB9rr1R9G188/EW7+1+OtROciIrEP+AgV9DV8w69cfa/EOpT5zvuZD/wCPGu7Ev3Uj4/IYXrSl2X5/8MZ9FFFcZ9SFLSUtIYUUUUDFooooGLRRRSGFLSUtAwooooGFLSUtAwooopDFooooGFLSUtA0FFFFIYUUUUDCgUUCgYtFFFIYtJS0lAxaKKKBoKWkpaQxKKKKBi0UUUDDtRR2opDCiiigYUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAbHhjWX0HxBa34J8tW2yj1Q8H/H8K+ikdZI1dCGVhkEdxXy9Xuvw41c6p4ThjkbM1ofIbPXA+6fy/lXbg56uB8pxNhLwjiY9NH+h11FFFd58cFFFFABRRTXbajMFLYGcDqaAPI/i/rHm31no8bfLCvnSgf3jwo/LJ/GvMq9Eu/h94o8SazdaleLb2n2iQsPNk3FV7DA9BWzYfB21XDahqksh7pCgUfmc1wTpzqSbsfY4fG4TB0I03O7W9tdep5FVi1srq9cJaW007HoI0Lfyr3zT/h94Z0/BTTUmcfxTkuf14roobeG3QJBDHEo7IoA/SqjhX1ZhV4hpr+HBv10/wAzwjTvhr4lv8F7RLRD/FcPg/kOa67Tvg9bJhtT1KSU90gXaPzPNenUVtHDwXmeZWzvF1NIvl9DA0zwX4f0nBttMhMg/wCWko3t+ZreACqFAAA6AUtFbKKWx5lSrUqO822/MKKKKZmFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAeGfFjUWu/Fq2m793aQqAP9puT/SuFFdR8RlK+PNSz3KEfTaK5cV5lV3mz77ARUcNBLshwpaQUtZnchRTqaKdSZogFOpop1SaoKWkpaRqgooopFBV3R0Mmt6ei9Tcxgf8AfQqlXSeA7E3/AIy09MZWJjM30Uf44qoK8kjHE1FToTm+if5HvN7MLewuJz0jiZz+AzXyyzF2Zz1Ykn8a+kvGFx9m8H6tKDgi2dR+Ix/WvmsdK7sS9Uj5DII+5OXmv6/EWiiiuY+gClpKWkMKKKKBi0UUUDFooopDClpKWgYUUUUDClpKWgYUUUUhi0UUUDClpKWgaCiiikMKKKKBhQKKBQMWiiikMWkpaSgYtFFFA0FLSUtIYlFFFAxaKKKBh2oo7UUhhRRRQMKKKKACiiigAooooAKKKKACiiigAooooAKKKKAFr0D4TaiYNeurBm+S5i3qP9pf/rGvP62vCN59h8W6ZPnA88I30bj+taUpcs0zizGj7bCVIeX5ao+iaKKK9g/MAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiig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VRFRFCqowABgAUtejSpRprQ+FzDMauNneekVsv66hRRRWp54UUUUAFch8R9COteFJmiTdc2Z8+PHUgfeH4j+VdfSEAggjIPUGlKKkrM1o1ZUaiqR3R8pDpRXS+OvDx8O+JZ4UUi1nPnQHttPUfga5qvMaadmff0qkasFOOzClpKWpNAooooGLRRRQMWiiikMKWkpaBhRRRQMKWkpaBhRRRSGLRRRQMKWkpaBoKKKKQwooooGFAooFAxaKKKQxaSlpKBi0UUUDQUtJS0hiUUUUDFooooGHaijtRSGFFFFAwooooAKKKKACiiigAooooAKKKKACiiigAooooAWvWfhFp+ywv8AUWX/AFsgiQ+y8n9SK8mr6I8I6Z/ZHhawtCuJBHvk/wB5uT/OunCRvO/Y+f4jxHs8J7Nbyf4LX/I26KKK9M+DCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACszxFZTal4dv7K3UNNPCUQE4GT71p0UmrqxUJuElJdDjvCPgCx8Oql1c7brUccyEfLH7KP612NFFKMVFWRpXxFTETdSq7sKKKKoxCiiigAooooAKKKKAOV8feGf+Ej8POsKg3ttmWA+p7r+I/pXz4QQSGBDA4IPY19W14n8UPCv9l6n/AGxaR4s7tv3oA4jk/wAD/OuXEU/tI+hyTGcr+rz67f5HntLSUtcZ9OFFFFAxaKKKBi0UUUhhS0lLQMKKKKBhS0lLQMKKKKQx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VncIilmY4AAySaFVnZVVSzMcAAZJNexeA/AY0oJquqxhr4jMUR5EI9T/ALX8q1pUnUdkcOPx9LBUuee/RdyTwF4GXRo11PUow2oOv7uM/wDLAH/2b+Vd7RRXqQgoRsj86xWKq4qq6tV6v8PIKKKKs5gooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAr31lb6jYzWd1GJIJlKOp7g186+JvD9x4a1uWxmy0f3oZMffTsfr2NfSVcx448MJ4k0J0RR9ttwZLdu+e6/Q1hXp88brc9XKcd9Wq8svhlv5eZ890tDKVYqwKsDgg9QaK88+3CiiigYUtJS0DCiiikMWiiigYUtJS0DQUUUUhhRRRQMKBRQKBi0UUUhi0lLSUDFooooGgpaSlpDEooooGLRRRQMO1FHaikMKKKKBhRRRQAUUUUAFPiikmlSKJGeRztVFGST6CpLOzudQu47W0heaeQ4VFHJ/+tXtng3wLb+HYlu7rbPqTDl+qxey/41tSoyqPTY87MczpYGF5ayey/roU/A3gJNGVNS1NFk1BhlIzyIP8W9+1d7RRXpwgoKyPz3FYqriqjq1Xd/l6BRRRVnMFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAeH/FDw7/ZWujUYExa3xJOBwsg6/n1/OuFr6O8W6GviDw5dWOB523fCfRx0/w/GvnJlZHZHUqynDA9iK8/EQ5ZXXU+2ybF+3w/LLeOny6CUUUVgeuFLSUtAwooopDFooooGFLSUtA0FFFFIYUUUUDCgUUCgYtFFFIYtJS0lAxaKKKBoKWkpaQxKKKKBi0UUUDDtRR2opDCiiigYUUUUAFaWiaFf+IL9bSwhLt1dz92MepNavhTwVf+JphKQbfT1Pz3DD73so7mvbdH0ax0OxWzsIRHGOp/ic+pPc100cO56vY8LNM6p4ROnT1n+C9f8jO8L+EbHwxabYR5t24/e3DDlvYeg9q6GiivSjFRVkfC1q1StN1Kju2FFFFMyCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigArwb4laJ/ZHiuSaNcW96POXHQN/EPz5/Gvea4f4paR/aHhU3aLmayfzR/unhv8fwrGvDmh6HqZRiPY4pJ7S0/y/E8Nooorzj7kKWkpaBhRRRSGLRRRQMKWkpaBoKKKKQwooooGFAooFAxaKKKQxaSlpKBi0UUUDQUtJS0hiUUUUDFooooGHaijtRSGFFFX9I0W/wBdvVtdPt2lc/eboqD1Y9qaTbshSnGEXKTskUVUswVQWYnAAHJNeleEfhm85jv9eQpF95LTu3+/6D2rqvCngKw8OhbmfbdahjmVh8qf7o7fXrXX13UcLbWZ8fmfEDnelhdF/N1+Xb13GRRRwRJFDGscaDCoowAKfRRXafKt31YUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAU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FABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAUdZ/wCQFqH/AF7Sf+gmvmIdK+mtefy/DupP6Wsh/wDHTXzKOlcWK3R9Vw78FT1QtLSUtcp9IFFFFIYtFFFAwpaSloGgooopDCiiigYUCigUDFooqxZWN1qNwtvZ28k8zdEjXJ/+tQDairsgqazs7rULhbezt5J5m6JGuTXougfCeeXbPrk/lJ1+zwnLH6t2/CvS9L0bT9GtxBp9pHAnfaOT9T1NdFPDSlrLQ8PGZ/Qo+7R95/h9/X5Hmvh/4USy7J9dn8pev2aE/N+Ldvwr0zTdKsNIthb2FrHBGOyDk/U96u0V2wpRhsj5TF5hiMW/3stO3QKKKK0OIKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAMLxlN5Hg7Vn/6d2X8+P6185DpXvfxNuPs/ga8XOPOZI/zbP9K8ErhxT95H1/D8bYeUu7/RBS0lLXMe+FFFFIYtFFFAwpaSloGgooopDCiitfRvDGsa84FhZO8eeZX+VB+J/pTSbdkTOpCnHmm7LzMirum6Tf6vcCDT7SW4fvsXgfU9BXqeh/Caytts2sXBu5Ovkx/LGPqep/SvQLSytdPt1gs7eOCJeiRqAK6YYWT+LQ8HF8Q0afu0FzPv0/zZ5loPwlPyza5c+/2eA/zb/CvR9N0mw0i3EGn2kVvGOoReT9T1NXaK64Uow2R8zisfiMU/3stO3QKKKK0OMKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooA83+MN3s0WwtAeZbguR7KP8A69eO16H8Xr3zvEVpaA8W9vuI92P+ArzyvOru9Rn3WUU+TBw89QpaSlrE9QKKKKQxaKKKBhS1b0/StQ1abyrCzmuH7+WuQPqegrvtF+El5Ptl1i7W3TqYYfmf8T0H61cacp7I5cRjcPhl+9lby6/cebAFmCqCWPQAZJrrNE+HevaxtkeAWVuf+WlxwSPZetew6N4T0XQgDZWSCXvM/wAzn8T/AErarphhV9pngYriKT0w8beb/wAjjND+Gmh6UVluUN/cDndMPlB9l6fnmuxREjRUjVVRRgKowBTqK6owjFWSPn6+Jq15c1WTYUUUVRgFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRVHWb5dM0W9vmOBBCzg++OP1obtqVGLlJRXU8B8aX/APaXjDU5w2UEpjT6L8v9KwaVnaR2duWYlj9TSV5Ld3c/R6UFTgoLorBS0lbWk+FNc1sj7Fp8rRn/AJauNiD8TQk3oip1IU4803ZeZjUqqzuERSzHoAMk16rpHwgUbZNYvy3rDbDA/wC+j/hXe6T4a0fREAsLCGJh/wAtMZc/iea2jhpvfQ8jEZ9h6elP3n9y+88Y0f4d+INX2ubb7HAf+Wlz8px7L1r0HRvhXo1htkv3kv5hzhvlT8h1/Gu8orphh4R8zwcTnOKraJ8q8v8APchtrW3s4VhtoI4Yl6JGoUD8qmoorc8ptt3YUUUUCCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBaKSigBa4L4san9j8LpZq2HvJQp5/hXk/0rvK5HXbaC78a6bHcwxzItuzKsiBgDu6jNZ1fgZ3Zcl9ZjJ9Nfu1PFdM0DVtZcLp9hPOP74XCj8TxXdaT8Ib2ba+rXyW694oBvb8+g/WvXFRY0CooVR0CjAFLWUMNFb6nfiM9xE21TSj+L/r5HO6R4E8PaNtaGxWaYf8tZ/nb9eBXRgAAADAHQCiiuhRUdEeNVrVKr5qkm35i0UlFMzFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAFopKKAP/Z&quot; data-filename=&quot;heart.jpg&quot; style=&quot;width: 707.429px;&quot;&gt;&lt;/p&gt;', 'Yes');
INSERT INTO `blog_pages` (`id`, `title`, `slug`, `content`, `active`) VALUES
(5, 'New Page', 'new-page', 'Hello World!', 'Yes');

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

--
-- Dumping data for table `blog_posts`
--

INSERT INTO `blog_posts` (`id`, `category_id`, `title`, `slug`, `image`, `content`, `author_id`, `date`, `time`, `active`, `featured`, `views`) VALUES
(1, 1, 'Blog Established', 'blog-established-87409', 'purple-logo.png', '&lt;h2 class=&quot;&quot; style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;font-family: &quot;Comic Sans MS&quot;;&quot;&gt;Welcome to this Development Blog - :-)&lt;/span&gt;&lt;/h2&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;font-family: &quot;Comic Sans MS&quot;;&quot;&gt;It&#039;s a work in progress - &lt;/span&gt;&lt;font color=&quot;#000000&quot; style=&quot;background-color: rgb(255, 255, 0);&quot;&gt;&lt;span style=&quot;font-family: &quot;Comic Sans MS&quot;;&quot;&gt;we code here,&lt;/span&gt;&lt;/font&gt;&lt;span style=&quot;font-family: &quot;Comic Sans MS&quot;;&quot;&gt; no shortcuts. &lt;/span&gt;&lt;/p&gt;', 1, '2025-08-04 19:34:26', '06:55', 'Yes', 'No', 260),
(3, 1, 'New Test for image storage and Author ID', 'new-test-for-image-storage-and-author-id-79060', 'ChatGPT Image Jul 29, 2025, 08_10_42 PM.png', '&lt;p&gt;Also what happens if you pick a second featured post?&lt;/p&gt;', 3, '2025-08-04 19:34:33', '20:39', 'Yes', 'Yes', 14),
(4, 1, 'Author ID Post', 'author-id-post-89085', '33c4c89e-0581-47f4-b985-d442505457fb.png', '&lt;p&gt;This is set to feature&lt;font color=&quot;#000000&quot; style=&quot;background-color: rgb(255, 255, 0);&quot;&gt;d, as well.&lt;/font&gt;&lt;/p&gt;', 3, '2025-08-04 19:34:42', '20:45', 'Yes', 'Yes', 1),
(5, 1, 'Testing for errors with no redirect', 'testing-for-errors-with-no-redirect-64467', 'logo-document-code.png', '&lt;p&gt;&lt;b&gt;&lt;u&gt;Test post without a redirect.&lt;/u&gt;&lt;/b&gt;&lt;/p&gt;', 3, '2025-08-01 19:34:49', '20:57', 'Yes', 'No', 18),
(20, 1, 'Testing the image upload.', 'testing-the-image-upload-24096', 'ScreenShot2025-08-02at09.51.13.573PM(2).png', '&lt;p&gt;test&lt;/p&gt;', 3, '2025-08-01 19:34:57', '07:54', 'Yes', 'No', 32),
(24, 1, 'Sunday New Name image Part two', 'sunday-new-name-image-part-two-99129', 'resize.png', '&lt;div style=&quot;color: rgb(204, 204, 204); background-color: rgb(31, 31, 31); font-family: Consolas, &amp;quot;Courier New&amp;quot;, monospace; line-height: 22px; white-space: pre;&quot;&gt;&lt;span style=&quot;color: #9cdcfe;&quot;&gt;$img_path&lt;/span&gt;&lt;/div&gt;', 3, '2025-08-01 19:35:02', '01:58', 'Yes', 'No', 23);

-- --------------------------------------------------------

--
-- Table structure for table `blog_post_tags`
--

CREATE TABLE `blog_post_tags` (
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_post_tags`
--

INSERT INTO `blog_post_tags` (`post_id`, `tag_id`) VALUES
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(8, 1),
(11, 1),
(14, 2),
(22, 2),
(23, 2),
(24, 2);

-- --------------------------------------------------------

--
-- Table structure for table `blog_tags`
--

CREATE TABLE `blog_tags` (
  `id` int(11) NOT NULL,
  `tag` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_tags`
--

INSERT INTO `blog_tags` (`id`, `tag`) VALUES
(1, 'Tag'),
(2, 'Test');

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

--
-- Dumping data for table `blog_users`
--

INSERT INTO `blog_users` (`id`, `email`, `registered`, `username`, `role`, `access_level`, `document_path`, `full_name`, `rememberme`, `activation_code`, `last_seen`, `method`, `social_email`, `reset_code`, `password`, `tfa_code`, `ip`, `approved`, `avatar`, `active`, `added`) VALUES
(1, 'webdev@glitchwizardsolutions.com', '2025-06-25 11:38:41', 'GlitchWizard', 'Admin', 'Blog Only', 'Blog/', 'None Provided', '\'\'', 'activated', '2025-07-03 16:47:33', 'password', 'None Provided', '\'\'', '5c568c14ed8ef1bcc1443eb4a70da2575df73b2a77569d187bd66dcb7c5512a0', '\'\'', '\'\'', 'approved', 'dio.jpg', 1, '2025-07-02 22:10:51');

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

--
-- Dumping data for table `blog_widgets`
--

INSERT INTO `blog_widgets` (`id`, `title`, `content`, `position`) VALUES
(1, 'Some Changes Going On!', '&lt;p&gt;Our next site improvement project &lt;b&gt;is this blog&lt;/b&gt;.&amp;nbsp; The goal is to create a blog system and ever-industrious, work in some newsletter functionality as well.&amp;nbsp; Until the tweaks are tweaked - the development is going on in this production environment.&amp;nbsp; Sometimes that means things won&#039;t go as expected - and that&#039;s okay.&amp;nbsp; It&#039;s part of the development process, it doesn&#039;t mean there is an insurmountable problem.&amp;nbsp; :-)&lt;/p&gt;\r\n', 'Footer');

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

--
-- Dumping data for table `campaigns`
--

INSERT INTO `campaigns` (`id`, `title`, `status`, `groups`, `newsletter_id`, `submit_date`) VALUES
(1, 'Welcome to your website or web app!', 'Paused', '', 4, '2024-03-14 14:52:00');

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

--
-- Dumping data for table `campaign_items`
--

INSERT INTO `campaign_items` (`id`, `campaign_id`, `subscriber_id`, `status`, `fail_text`, `update_date`) VALUES
(1, 1, 1, 'Queued', '', NULL);

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

--
-- Dumping data for table `comment_filters`
--

INSERT INTO `comment_filters` (`id`, `word`, `replacement`) VALUES
(1, 'Damn', 'Darn');

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

--
-- Dumping data for table `contact_form_messages`
--

INSERT INTO `contact_form_messages` (`id`, `email`, `subject`, `msg`, `extra`, `submit_date`, `status`) VALUES
(1, 'johndoe@example.com', 'Responsive Menu Issue', 'Hi Team,\r\n\r\nI\'ve noticed on mobile devices that the responsive menu isn\'t aligned with the layout. \r\n\r\nI thought I would let you guys know!\r\n\r\nRegards,\r\nJohn', '{\"name\":\"John Doe\"}', '2023-08-30 15:06:42', 'Read'),
(2, 'robertjohnson@example.com', 'Advertising Inquiry', 'Hello,\r\n\r\nI\'m contacting you on behalf of our agency, which seeks to provide relevant advertisements based on your niche. \r\n\r\nAre you interested in our services?\r\n\r\nBest Regards,\r\nRobert', '{\"name\":\"Robert Johnson\"}', '2023-08-30 15:10:42', 'Read');

-- --------------------------------------------------------

--
-- Table structure for table `custom_placeholders`
--

CREATE TABLE `custom_placeholders` (
  `id` int(11) NOT NULL,
  `placeholder_text` varchar(255) NOT NULL,
  `placeholder_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `custom_placeholders`
--

INSERT INTO `custom_placeholders` (`id`, `placeholder_text`, `placeholder_value`) VALUES
(1, '%test%', 'This is an example placeholder.\r\n\r\nYou can even include <strong>HTML tags</strong>.');

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
  `title` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `color` varchar(10) NOT NULL,
  `datestart` datetime NOT NULL,
  `dateend` datetime NOT NULL,
  `uid` int(11) NOT NULL,
  `submit_date` datetime NOT NULL DEFAULT current_timestamp(),
  `recurring` enum('never','daily','weekly','monthly','yearly') NOT NULL DEFAULT 'never',
  `photo_url` varchar(255) NOT NULL DEFAULT '',
  `redirect_url` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_page_details`
--

CREATE TABLE `event_page_details` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
-- Table structure for table `gallery_collections`
--

CREATE TABLE `gallery_collections` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description_text` varchar(255) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_collections`
--

INSERT INTO `gallery_collections` (`id`, `title`, `description_text`, `account_id`, `is_public`) VALUES
(1, 'February Test Collections', 'This is a test collection', 1, 1);

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

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `title`, `description`, `submit_date`) VALUES
(1, 'General', 'This is a test description.', '2024-01-01 00:00:00'),
(2, 'Technical', 'This is a test description.', '2024-01-01 00:00:00');

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

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `client_id`, `project_id`, `invoice_number`, `payment_amount`, `payment_status`, `payment_methods`, `due_date`, `created`, `notes`, `viewed`, `tax`, `tax_total`, `invoice_template`, `payment_ref`, `paid_with`, `paid_total`, `recurrence`, `recurrence_period`, `recurrence_period_type`) VALUES
(46, 5, 0, '240110SoCalT', 2500.00, 'Paid', 'PayPal, Cash', '2024-01-31 14:23:00', '2024-01-10 14:23:00', 'Thank you so much for your order!', 0, 'fixed', 0.00, 'GlitchWizardSolutions', '', '', 2500.00, 0, 1, 'day'),
(47, 9, 0, '240110KnickKn', 4000.00, 'Paid', 'PayPal, Cash', '2024-02-29 15:18:00', '2024-02-10 15:18:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'default', '', '', 4000.00, 0, 1, 'day'),
(48, 7, 0, '240110Souther', 1100.00, 'Paid', 'PayPal, Cash', '2024-09-17 15:29:00', '2024-09-02 15:29:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'default', '', '', 1100.00, 0, 1, 'day'),
(49, 10, 0, '240110Wrench', 25.00, 'Unpaid', 'PayPal, Cash', '2025-04-30 15:35:00', '2024-04-10 15:35:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'default', 'TXN1737314667', 'Cash', 0.00, 0, 1, 'day'),
(50, 8, 0, '240601Blamei', 2000.00, 'Paid', 'PayPal, Cash', '2024-06-17 15:37:00', '2024-06-01 15:37:00', 'Thank you for your order!', 1, 'fixed', 0.00, 'default', '', '', 2000.00, 0, 1, 'day'),
(51, 11, 0, '250110Lanier', 350.00, 'Unpaid', 'PayPal, Cash', '2022-01-17 15:39:00', '2022-01-01 15:39:00', 'Thank you for your order!', 1, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'day'),
(52, 12, 0, '250110TheMoo', 0.00, 'Paid', 'PayPal, Cash', '2024-06-17 15:44:00', '2024-06-10 15:44:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'day'),
(53, 1, 0, '250119GlitchW', 1.00, 'Paid', 'PayPal, Cash', '2025-01-26 15:26:00', '2025-01-19 15:26:00', 'Thank you for your order!', 1, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'day'),
(54, 13, 0, '250119Testing', 1.00, 'Pending', 'PayPal, Cash', '2025-01-26 15:31:00', '2025-01-19 15:31:00', 'Thank you for your order!', 1, 'fixed', 0.00, 'default', '', '', 25.00, 0, 1, 'day'),
(55, 1, 0, '250119GlitchW', 1.00, 'Paid', 'PayPal, Cash', '2025-01-26 15:41:00', '2025-01-19 15:41:00', 'Thank you for your order!', 1, 'fixed', 0.00, 'default', '', '', 24.00, 0, 1, 'day'),
(56, 1, 0, '250306GlitchW', 1.00, 'Pending', 'PayPal, Cash', '2025-03-13 16:51:00', '2025-03-06 16:51:00', 'Thank you for your order!', 1, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'day'),
(57, 1, 0, '250311GlitchW', 1.00, 'Cancelled', 'PayPal, Cash', '2025-03-18 19:29:00', '2025-03-11 19:29:00', 'Thank you for your order!', 1, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'day'),
(58, 1, 0, '250612GlitchW', 4500.00, 'Unpaid', 'PayPal, Cash', '2025-06-19 15:46:00', '2025-06-12 15:46:00', 'Thank you for your order!', 1, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'day'),
(59, 1, 0, '250612GlitchW', 9000.00, 'Unpaid', 'PayPal, Cash', '2025-06-19 17:13:00', '2025-06-12 17:13:00', 'Thank you for your order!', 1, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'year'),
(60, 1, 0, '250612GlitchW', 1.00, 'Unpaid', 'PayPal, Cash', '2025-06-19 18:15:00', '2025-06-12 18:15:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'year'),
(62, 1, 0, '25061208:32pmGlitchW', 1.00, 'Unpaid', 'PayPal', '2025-06-19 20:31:00', '2025-06-12 20:31:00', 'Thank you for your order!', 1, 'fixed', 0.00, 'GlitchWizardSolutions', 'TXN1749775030', 'Cash', 0.00, 0, 1, 'year'),
(63, 1, 0, '25061210:15pmGlitchW', 1.00, 'Unpaid', 'PayPal', '2025-06-19 22:14:00', '2025-06-12 22:14:00', 'Thank you for your order!', 1, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'year'),
(64, 13, 0, '25061210:41pmTesting', 1.00, 'Unpaid', 'PayPal', '2025-06-19 22:40:00', '2025-06-12 22:40:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'year'),
(65, 13, 0, '25061501:03pmTesting', 1.00, 'Unpaid', 'PayPal', '2025-06-22 13:01:00', '2025-06-15 13:01:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'GlitchWizardSolutions', '', '', 0.00, 0, 1, 'year'),
(66, 1, 0, '25061501:20pmGlitchW', 1.00, 'Unpaid', '', '2025-06-22 13:19:00', '2025-06-15 13:19:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'minimal', '', '', 0.00, 0, 1, 'year'),
(67, 1, 0, '25061503:30pmGlitchW', 1.00, 'Unpaid', 'PayPal, Cash', '2025-06-22 15:29:00', '2025-06-15 15:29:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'GlitchWizardSolutions', '', '', 0.00, 0, 1, 'year'),
(68, 1, 0, '25061503:40pmGlitchW', 0.00, 'Unpaid', '', '2025-06-22 15:39:00', '2025-06-15 15:39:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'year'),
(69, 13, 0, '25061506:36pmTesting', 1.00, 'Unpaid', 'PayPal, Cash', '2025-06-22 18:33:00', '2025-06-15 18:33:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'year'),
(70, 13, 0, '25061510:38pmTesting', 1.00, 'Unpaid', 'PayPal', '2025-06-22 22:38:00', '2025-06-15 22:38:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'year'),
(71, 17, 0, '25061511:06pmDio\'sH', 1.00, 'Unpaid', 'PayPal', '2025-06-22 22:42:00', '2025-06-15 22:42:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'default', '', '', 0.00, 0, 1, 'year'),
(72, 1, 0, '25061511:32pmGlitchW', 1.00, 'Unpaid', 'PayPal', '2025-06-22 23:32:00', '2025-06-15 23:32:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'minimal', '', '', 0.00, 0, 1, 'year'),
(73, 1, 0, '25061612:18amGlitchW', 1.00, 'Paid', 'PayPal', '2025-06-23 00:17:00', '2025-06-16 00:17:00', 'Thank you for your order!', 0, 'fixed', 0.00, 'default', '8EX54723L1684015L', 'paypal', 6.00, 0, 1, 'year'),
(75, 15, 0, '25061601:40amCustom', 1.00, 'Paid', 'PayPal', '2025-06-23 01:39:00', '2025-06-16 01:39:00', 'Thank you for your order!', 1, 'fixed', 0.00, 'default', '6LR1347392609464G', 'paypal', 1.00, 0, 1, 'year');

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

--
-- Dumping data for table `invoice_clients`
--

INSERT INTO `invoice_clients` (`id`, `account_id`, `project_id`, `business_name`, `description`, `facebook`, `instagram`, `bluesky`, `x`, `linkedin`, `first_name`, `last_name`, `email`, `phone`, `address_street`, `address_city`, `address_state`, `address_zip`, `address_country`, `created`, `total_invoices`, `issue`, `incomplete`) VALUES
(1, 3, 0, 'Dio\'s House of Scritches', 'Dio\'s House of Scritches is based on the long held belief that home is where the scritches are.', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Dio', 'Moore', 'barbara@glitchwizardsolutions.com', '850-491-9028', '127 Northwood Road', 'Crawfordville', 'Fl', '32327', 'United States', '2024-09-10 10:32:00', 0, 'No', 'Yes'),
(2, 29, 0, 'Burden to Blessings Home Solutions LLC', 'Acquisitions Company ', 'facebook.com/#', 'instagram.com/#', 'bluesky.com/#', 'twitter.com/#', 'linkedin.com/#', 'Elizabeth', 'Riggs', 'ehuber1993@gmail.com', '(254) 383-6301', '531 Ell Street', 'Mishawaka', 'IN', '46545', 'United States', '2024-10-09 23:11:00', 0, 'No', 'Yes'),
(5, 36, 0, 'SoCal Thrift', 'This business has closed, permenantly.', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Carl', 'Sullivan', 'sullivancarl853@gmail.com', '(951) 578-5755', '12927 Saticoy Street', 'North Hollywood', 'CA', '91605', 'United States', '2024-12-26 10:58:00', 0, 'No', 'Yes'),
(6, 29, 0, 'Midwest Homes 4 Sale', 'Real Estate', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Elizabeth', 'Riggs', 'elizabeth.riggs@riggszoo.com', '(574) 208-3946', '', '', '', '', 'United States', '2025-01-04 17:06:00', 0, 'No', 'Yes'),
(7, 35, 0, 'Southern Hippy Chic', 'Natural Beauty Products', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Delayne', 'Griffin', 'sustainablebeauty@southernhippychic.com', '(850) 694-4431', '', 'Crawfordville', 'FL', '32327', 'United States', '2025-01-04 18:06:00', 0, 'No', 'Yes'),
(8, 37, 0, 'Blame it on my Roots', 'Flower Shop', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Tylinna', 'Enos', 'Blamemyflowertruck@gmail.com', '(000) 000-0000', '', 'Goshen', 'IN', '', 'United States', '2025-01-04 18:10:00', 0, 'No', 'Yes'),
(9, 38, 0, 'KnickKnackeryByCheri', 'Crochet Sales', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Cheryl', 'Youngman', 'creations@knickknackerybycheri.com', '(574) 903-5500', '', '', 'MI', '', 'United States', '2025-01-04 18:12:00', 0, 'No', 'Yes'),
(10, 39, 0, 'Wrench Blisters Garage', 'Auto Shop', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Joseph', 'Harris', 'wrenchblistersgarage@gmail.com', '(850) 597-4782', '', 'Tallahassee', 'FL', '', 'United States', '2025-01-04 18:14:00', 0, 'No', 'Yes'),
(11, 40, 0, 'Lanier Estates', 'Estate Services', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Michellet', 'Lincoln', 'lanierestates@gmail.com', '(561)  323-8551', '722 Colin Hill Road', 'Lawrenceville', 'GA', '30044', 'United States', '2025-01-04 18:16:00', 0, 'No', 'Yes'),
(12, 33, 0, 'The Moore Gallery Shop', 'Never Started', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Donna', 'Moore', 'dacm@themooregalleryshop.com', '(850) 345-6022', '', 'Tallahassee', 'FL', '', 'United States', '2025-01-04 18:18:00', 0, 'No', 'Yes'),
(13, 41, 0, 'Testing my Application', 'none', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Test', 'Account', 'sidewaysy.onlineorders@gmail.com', '8502944226', '18627 CR 23 ', 'Bristol', 'IN', '46507', 'United States', '2025-01-06 22:47:00', 0, 'No', 'Yes'),
(14, 29, 0, 'Life Libby and the Pursuit of Happiness', 'Pending Project', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Elizabeth', 'Riggs', 'elizabeth.riggs@riggszoo.com', '(254) 383-6301', '531 Ell Street', 'Mishawaka', 'IN', '46545', 'United States', '2025-01-10 15:49:00', 0, 'No', 'Yes'),
(15, 3, 0, 'Dio\'s House of Scritches', 'Dio\'s House of Scritches is based on the long held belief that home is where the scritches are.', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Dio', 'Moore', 'barbara@glitchwizardsolutions.com', '850-491-9028', '127 Northwood Road', 'Crawfordville', 'Fl', '32327', 'United States', '2025-01-28 22:03:46', 0, 'No', 'No'),
(16, 3, 0, 'Dio\'s House of Scritches', 'Dio\'s House of Scritches is based on the long held belief that home is where the scritches are.', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Dio', 'Moore', 'barbara@glitchwizardsolutions.com', '850-491-9028', '127 Northwood Road', 'Crawfordville', 'Fl', '32327', 'United States', '2025-06-15 21:27:00', 0, 'Yes', 'Yes'),
(17, 3, 0, 'Dio\'s House of Scritches', 'Dio\'s House of Scritches is based on the long held belief that home is where the scritches are.', 'https://facebook.com/#', 'https://instagram.com/#', 'https://bluesky.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'Dio', 'Moore', 'barbara@glitchwizardsolutions.com', '850-491-9028', '127 Northwood Road', 'Crawfordville', 'Fl', '32327', 'United States', '2025-06-15 21:31:00', 0, 'Yes', 'No');

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

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_number`, `item_name`, `item_description`, `item_price`, `item_quantity`) VALUES
(49, '240110SoCalT', 'MVP Website & Hosting', 'SoCalThrift.com Brochure Site (No Data Capture)', 2500.00, 1),
(50, '240110KnickKn', 'MVP Website', 'KnickknackeryByCheri.com', 2500.00, 1),
(51, '240110KnickKn', 'Custom App', 'Afghan Shop', 500.00, 1),
(52, '240110KnickKn', 'Hosting', '1 Year + Domain Renwal', 1000.00, 1),
(55, '240110Souther', 'Hosting', 'MVP Site Hosting', 1075.00, 1),
(56, '240110Wrench', 'Domain Renewal', 'WrenchBlistersGarage.com', 25.00, 1),
(57, '240601Blamei', 'MVP Website', 'Brochure Site', 1000.00, 1),
(59, '240601Blamei', 'Hosting 1 Year (incl. Reg)', 'BlameMyRoots.coml', 1000.00, 1),
(60, '250110Lanier', 'Business Web Applications Site', 'Remaining Unpaid Balance', 350.00, 1),
(61, '250110TheMoo', 'MVP Website', 'No data collection', 2500.00, 0),
(62, '250110TheMoo', 'Hosting includes Renewal', 'themooregalleryshop.com', 1000.00, 0),
(63, '250110TheMoo', 'Shop Integration', '3rd Party', 100.00, 0),
(64, '240110Souther', 'Domain Renewal', 'SouthernHippyChic.com', 25.00, 1),
(66, '250119Testing', 'Domain Renewal', 'Testing System', 1.00, 1),
(67, '250119GlitchW', 'Website Owner Manual', 'Helpful resource for your website', 1.00, 1),
(68, '250306GlitchW', 'Hosting', 'MVP Site Hosting', 1.00, 1),
(69, '250311GlitchW', 'Website Owner Manual', 'Helpful resource for your website', 1.00, 1),
(70, '250311GlitchW', 'Hosting', 'MVP Site Hosting', 0.00, 1),
(76, '25061208:32pmGlitchW', 'Business Foundational PHP MySQL Website', 'Complete On-Brand Website .zip file & Website Owner\'s Manual', 1.00, 1),
(77, '25061210:15pmGlitchW', 'MVP Bootstrap v5.3 Website ', 'Complete On-Brand Website .zip file & Website Owner\'s Manual', 1.00, 1),
(78, '25061210:41pmTesting', 'MVP Bootstrap v5.3 Website ', 'Complete On-Brand Website .zip file & Website Owner\'s Manual', 1.00, 1),
(79, '25061501:20pmGlitchW', 'MVP Bootstrap v5.3 Website ', 'Complete On-Brand Website .zip file & Website Owner\'s Manual', 1.00, 1),
(80, '25061503:30pmGlitchW', 'MVP Bootstrap v5.3 Website ', 'Complete On-Brand Website .zip file & Website Owner\'s Manual', 1.00, 1),
(81, '25061506:36pmTesting', 'MVP Bootstrap v5.3 Website ', 'Complete On-Brand Website .zip file & Website Owner\'s Manual', 1.00, 1),
(82, '25061510:38pmTesting', 'MVP Bootstrap v5.3 Website ', 'Complete On-Brand Website .zip file & Website Owner\'s Manual', 1.00, 1),
(83, '25061511:06pmDio\'sH', 'MVP Bootstrap v5.3 Website ', 'Complete On-Brand Website .zip file & Website Owner\'s Manual', 1.00, 1),
(84, '25061511:32pmGlitchW', 'MVP Bootstrap v5.3 Website ', 'Complete On-Brand Website .zip file & Website Owner\'s Manual', 1.00, 1),
(85, '25061612:18amGlitchW', 'Test Record', 'This is a test record.', 1.00, 1),
(87, '25061601:40amCustom', 'Test Record', 'Complete On-Brand Website .zip file & Website Owner\'s Manual', 1.00, 1);

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

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `ip_address`, `attempts_left`, `date`) VALUES
(32, '174.211.233.218', 10, '2024-02-27 21:05:36'),
(34, '174.228.160.79', 10, '2024-03-03 03:36:15'),
(35, '174.239.86.54', 10, '2024-03-04 19:47:10'),
(40, '174.212.44.29', 10, '2024-03-07 16:59:35'),
(44, '174.212.37.222', 10, '2024-03-07 20:28:02'),
(58, '198.7.56.243', 10, '2024-03-20 13:24:09'),
(68, '174.211.227.127', 10, '2024-05-26 11:55:22'),
(77, '174.199.225.115', 10, '2024-09-26 21:29:10'),
(86, '24.131.47.145', 5, '2024-10-11 17:01:38'),
(87, '160.226.243.139', 4, '2024-10-19 03:32:31'),
(89, '216.130.237.61', 4, '2024-10-31 00:20:37'),
(91, '92.62.99.54', 4, '2024-11-06 12:45:15'),
(93, '196.61.35.130', 4, '2024-11-09 00:35:16'),
(95, '129.222.253.212', 5, '2024-11-11 13:34:19'),
(96, '203.95.196.234', 4, '2024-11-15 15:26:42'),
(98, '102.69.145.143', 4, '2024-11-19 05:20:20'),
(136, '37.130.40.166', 5, '2024-11-24 20:41:12'),
(145, '174.212.34.163', 3, '2024-12-03 10:10:02'),
(148, '174.228.165.231', 5, '2024-12-03 10:25:14'),
(152, '204.62.59.218', 5, '2024-12-03 20:05:08'),
(153, '174.211.224.237', 5, '2024-12-08 20:41:49'),
(154, '103.216.50.25', 5, '2024-12-09 05:25:30'),
(156, '176.236.148.221', 5, '2024-12-14 10:52:18'),
(157, '124.248.184.221', 5, '2024-12-23 00:07:11'),
(158, '197.248.247.117', 5, '2024-12-25 19:25:21'),
(159, '109.108.113.125', 5, '2024-12-26 17:16:42'),
(165, '119.15.94.82', 5, '2024-12-31 23:05:41'),
(166, '36.37.174.231', 5, '2025-01-01 16:32:22'),
(169, '37.130.37.91', 5, '2025-01-12 19:46:18'),
(172, '110.34.5.241', 5, '2025-01-21 04:25:07'),
(173, '198.12.32.89', 5, '2025-01-26 01:08:32'),
(174, '103.254.185.247', 5, '2025-01-27 20:54:11'),
(175, '179.40.96.1', 5, '2025-02-01 15:55:02'),
(176, '102.223.228.71', 5, '2025-02-03 12:01:30'),
(177, '200.58.83.225', 5, '2025-02-04 14:51:10'),
(178, '202.141.243.214', 5, '2025-02-05 12:46:28'),
(185, '185.74.83.250', 5, '2025-02-11 00:06:19'),
(186, '109.175.7.205', 5, '2025-02-13 09:33:08'),
(187, '197.210.189.171', 5, '2025-02-14 14:07:13'),
(188, '202.79.47.105', 5, '2025-02-20 01:56:17'),
(189, '202.7.53.165', 5, '2025-02-21 01:09:15'),
(190, '118.91.172.247', 5, '2025-02-23 08:10:33'),
(191, '154.70.147.21', 5, '2025-02-24 04:21:12'),
(192, '185.187.131.150', 5, '2025-02-26 06:13:53'),
(193, '105.27.205.10', 5, '2025-02-27 01:36:07'),
(194, '59.103.244.194', 5, '2025-02-28 19:54:49'),
(195, '41.222.6.154', 5, '2025-03-03 23:30:28'),
(197, '154.72.72.82', 5, '2025-03-04 23:52:50'),
(198, '178.217.115.237', 5, '2025-03-10 15:38:35'),
(203, '152.44.253.62', 5, '2025-03-23 02:30:15');

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

--
-- Dumping data for table `newsletters`
--

INSERT INTO `newsletters` (`id`, `title`, `content`, `attachments`, `last_scheduled`, `submit_date`) VALUES
(1, 'General Example', '<div style=\"background-color:#eeeff1;font-family:-apple-system, BlinkMacSystemFont, \'segoe ui\', roboto, oxygen, ubuntu, cantarell, \'fira sans\', \'droid sans\', \'helvetica neue\', Arial, sans-serif;font-size:16px;padding:30px;\">\n    <!-- TEMPLATE WRAPPER -->\n    <div style=\"background-color:#fff;font-size:16px;max-width:600px;margin: 30px auto;padding-bottom:30px;\">\n        <!-- HEADER -->\n        <div style=\"padding:40px;color:#636468;background-color:#f9fafc;margin:0;\">\n            <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin:0;padding:0;border-collapse:collapse;width:100%;\">\n                <tr>\n                    <!-- TITLE -->\n                    <td style=\"font-size:22px;color:#636468;text-align:left;font-weight:500;\">Newsletter Title</td>\n                    <!-- DATE -->\n                    <td style=\"font-size:16px;color:#909196;text-align:right;\">January, 2025</td>\n                </tr>\n            </table>\n        </div>\n        <!-- SECTION HEADING -->\n        <h2 style=\"padding:20px 40px;margin:0;color:#6e6f74;font-size:20px;font-weight:500;\">Section 1</h2>\n        <!-- SECTION PARAGRAPH -->\n        <p style=\"margin:10px 40px;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam ornare lectus sed lorem placerat condimentum. Nam aliquam viverra libero sed malesuada. Nunc in ligula est. Curabitur eu mattis purus, quis semper lacus. Cras rutrum pellentesque purus et scelerisque. Aliquam feugiat vehicula nulla, sit amet mollis mauris gravida vitae. Sed sit amet erat ac nulla feugiat viverra pretium nec felis.</p>\n        <!-- SECTION PARAGRAPH -->\n        <p style=\"margin:10px 40px;\">Nunc maximus tincidunt magna, eget placerat felis bibendum ut. Curabitur mollis neque eget vestibulum vulputate. Proin fermentum eros arcu, vel efficitur ipsum efficitur id. Nunc fringilla, nulla et faucibus pulvinar, arcu neque bibendum felis, eu sodales elit dolor at lacus. Nullam eget feugiat mauris. Morbi sed nunc nibh. Quisque sit amet justo elit.</p>\n        <!-- SECTION HEADING -->\n        <h2 style=\"padding:20px 40px;margin:0;color:#6e6f74;font-size:20px;font-weight:500;\">Section 2</h2>\n        <!-- PARAGRAPH & IMAGE -->\n        <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin:0;padding:0;border-collapse:collapse;width:100%;\">\n            <tr>\n                <!-- SECTION PARAGRAPH -->\n                <td>\n                    <p style=\"margin:10px 40px;\">Praesent at sapien pretium, placerat magna sed, ornare sapien. Proin pharetra, libero sit amet pharetra congue, libero diam venenatis lacus, id rhoncus eros nulla non velit. Mauris in vehicula tortor, mattis interdum risus. Sed molestie, enim sit amet dignissim volutpat, neque risus facilisis massa, non molestie ex ipsum nec leo. Nam volutpat eros in mollis suscipit.</p>\n                </td>\n                <!-- SECTION IMAGE -->\n                <td>\n                    <img src=\"https://via.placeholder.com/300x280\" width=\"300\" height=\"280\" alt=\"\" style=\"float:right;\">\n                </td>\n            </tr>\n        </table>\n        <!-- READ MORE LINK -->\n        <a href=\"%click_link%http://example.com\" style=\"display:inline-block;background-color:#2b7fc4;border-radius:4px;padding:12px 15px;text-decoration:none;color:#fff;font-weight:500;font-size:14px;margin:20px 40px;\">Read More</a>\n\n        %open_tracking_code%\n    </div>\n    <!-- UNSUBSCRIBE LINK -->\n    <p style=\"font-size:14px;text-align:center;color:#636468;margin:30px 0;\">If you no longer want to hear from us, please <a href=\"%unsubscribe_link%\" style=\"font-size:14px;color:#636468;\">click here to unsubscribe</a>.</p>\n</div>', NULL, NULL, '2024-01-01 00:00:00'),
(2, 'Product Example', '<div style=\"background-color:#eeeff1;font-family:-apple-system, BlinkMacSystemFont, \'segoe ui\', roboto, oxygen, ubuntu, cantarell, \'fira sans\', \'droid sans\', \'helvetica neue\', Arial, sans-serif;font-size:16px;padding:30px;\">\n    <!-- TEMPLATE WRAPPER -->\n    <div style=\"background-color:#fff;font-size:16px;max-width:600px;width:100%;margin: 30px auto;padding-bottom:30px;\">\n        <!-- HEADER -->\n        <div style=\"padding:40px;color:#636468;background-color:#f9fafc;margin:0;\">\n            <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin:0;padding:0;border-collapse:collapse;width:100%;\">\n                <tr>\n                    <!-- TITLE -->\n                    <td style=\"font-size:22px;color:#636468;text-align:left;font-weight:500;\">Newsletter Title</td>\n                    <!-- DATE -->\n                    <td style=\"font-size:16px;color:#909196;text-align:right;\">January, 2022</td>\n                </tr>\n            </table>\n        </div>\n        <!-- SECTION PARAGRAPH -->\n        <p style=\"margin:0;padding:30px 40px 10px 40px;\">We have a new product in store! Check it out now.</p>\n        <!-- PRODUCT -->\n        <div style=\"padding:30px 40px 20px 40px;\">\n            <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin:0;padding:0;border-collapse:collapse;width:100%;\">\n                <tr>\n                    <!-- SECTION IMAGE -->\n                    <td width=\"280\" style=\"width:280px;\">\n                        <img src=\"https://via.placeholder.com/250x250\" width=\"250\" height=\"250\" alt=\"\">\n                    </td>\n                    <!-- SECTION PARAGRAPH -->\n                    <td valign=\"top\" style=\"vertical-align:top\">\n                        <div style=\"margin-right:15px;padding:0 40px 0 0;\">\n                            <!-- PRODUCT NAME -->\n                            <h2 style=\"margin:0;color:#6e6f74;font-size:20px;padding:0 0 20px 0;font-weight:500;\">Product Name</h2>\n                            <!-- PRODUCT PRICE -->\n                            <span style=\"font-size:20px;color:#909197;padding-right:5px;\">$14.99</span>\n                            <!-- PRODUCT RRP PRICE -->\n                            <span style=\"font-size:20px;color:#e03e2d;text-decoration:line-through;\">$19.99</span>\n                        </div>\n                    </td>\n                </tr>\n            </table>\n        </div>\n        <!-- SHOP NOW LINK -->\n        <a href=\"%click_link%http://example.com\" style=\"display:inline-block;background-color:#2b7fc4;border-radius:4px;padding:12px 15px;text-decoration:none;color:#fff;font-weight:500;font-size:14px;margin:20px 40px;\">Shop Now</a>\n\n        %open_tracking_code%\n    </div>\n    <!-- UNSUBSCRIBE LINK -->\n    <p style=\"font-size:14px;text-align:center;color:#636468;margin:30px 0;\">If you no longer want to hear from us, please <a href=\"%unsubscribe_link%\" style=\"font-size:14px;color:#636468;\">click here to unsubscribe</a>.</p>\n</div>', NULL, NULL, '2024-01-01 00:00:00'),
(3, 'Products Example', '<div style=\"background-color:#eeeff1;font-family:-apple-system, BlinkMacSystemFont, \'segoe ui\', roboto, oxygen, ubuntu, cantarell, \'fira sans\', \'droid sans\', \'helvetica neue\', Arial, sans-serif;font-size:16px;padding:30px;\">\r\n    <!-- TEMPLATE WRAPPER -->\r\n    <div style=\"background-color:#fff;font-size:16px;max-width:600px;width:100%;margin:30px auto;padding-bottom:30px;\">\r\n        <!-- HEADER -->\r\n        <div style=\"padding:40px;color:#636468;background-color:#f9fafc;margin:0;\">\r\n            <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin:0;padding:0;border-collapse:collapse;width:100%;\">\r\n                <tr>\r\n                    <!-- TITLE -->\r\n                    <td style=\"font-size:22px;color:#636468;text-align:left;font-weight:500;\">Newsletter Title</td>\r\n                    <!-- DATE -->\r\n                    <td style=\"font-size:16px;color:#909196;text-align:right;\">January, 2025</td>\r\n                </tr>\r\n            </table>\r\n        </div>\r\n        <!-- SECTION PARAGRAPH -->\r\n        <p style=\"font-size:16px;color:#636468;margin:0;padding:30px 40px 10px 40px;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\r\n        <!-- PRODUCTS LIST -->\r\n        <div style=\"margin:0 30px;\">\r\n            <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"margin:0;padding:0;border-collapse:collapse;width:100%;\">\r\n                <tr>\r\n                    <!-- PRODUCT 1 -->\r\n                    <td width=\"33.33%\" valign=\"top\">\r\n                        <div style=\"margin:30px 15px;\">\r\n                            <!-- SECTION IMAGE -->\r\n                            <img src=\"https://via.placeholder.com/150x150\" width=\"150\" height=\"150\" alt=\"\">\r\n                            <!-- PRODUCT NAME -->\r\n                            <h2 style=\"margin:0;color:#6e6f74;font-size:16px;padding:5px 0;font-weight:500;\">Product 1</h2>\r\n                            <!-- PRODUCT PRICE -->\r\n                            <span style=\"font-size:16px;color:#909197;padding-right:2px;\">$14.99</span>\r\n                            <!-- PRODUCT RRP PRICE -->\r\n                            <span style=\"font-size:16px;color:#e03e2d;text-decoration:line-through;\">$19.99</span>\r\n                        </div>\r\n                    </td>\r\n                    <!-- PRODUCT 2 -->\r\n                    <td width=\"33.33%\" valign=\"top\">\r\n                        <div style=\"margin:30px 15px;\">\r\n                            <!-- SECTION IMAGE -->\r\n                            <img src=\"https://via.placeholder.com/150x150\" width=\"150\" height=\"150\" alt=\"\">\r\n                            <!-- PRODUCT NAME -->\r\n                            <h2 style=\"margin:0;color:#6e6f74;font-size:16px;padding:5px 0;font-weight:500;\">Product 2</h2>\r\n                            <!-- PRODUCT PRICE -->\r\n                            <span style=\"font-size:16px;color:#909197;padding-right:2px;\">$7.99</span>\r\n                            <!-- PRODUCT RRP PRICE -->\r\n                            <span style=\"font-size:16px;color:#e03e2d;text-decoration:line-through;\">$12.99</span>\r\n                        </div>\r\n                    </td>\r\n                    <!-- PRODUCT 3 -->\r\n                    <td width=\"33.33%\" valign=\"top\">\r\n                        <div style=\"margin:30px 15px;\">\r\n                            <!-- SECTION IMAGE -->\r\n                            <img src=\"https://via.placeholder.com/150x150\" width=\"150\" height=\"150\" alt=\"\">\r\n                            <!-- PRODUCT NAME -->\r\n                            <h2 style=\"margin:0;color:#6e6f74;font-size:16px;padding:5px 0;font-weight:500;\">Product 3</h2>\r\n                            <!-- PRODUCT PRICE -->\r\n                            <span style=\"font-size:16px;color:#909197;padding-right:2px;\">$39.99</span>\r\n                            <!-- PRODUCT RRP PRICE -->\r\n                            <span style=\"font-size:16px;color:#e03e2d;text-decoration:line-through;\">$59.99</span>\r\n                        </div>\r\n                    </td>\r\n                </tr>\r\n                <tr>\r\n                    <!-- PRODUCT 4 -->\r\n                    <td width=\"33.33%\" valign=\"top\">\r\n                        <div style=\"margin:30px 15px;\">\r\n                            <!-- SECTION IMAGE -->\r\n                            <img src=\"https://via.placeholder.com/150x150\" width=\"150\" height=\"150\" alt=\"\">\r\n                            <!-- PRODUCT NAME -->\r\n                            <h2 style=\"margin:0;color:#6e6f74;font-size:16px;padding:5px 0;font-weight:500;\">Product 4</h2>\r\n                            <!-- PRODUCT PRICE -->\r\n                            <span style=\"font-size:16px;color:#909197;padding-right:2px;\">$44.99</span>\r\n                            <!-- PRODUCT RRP PRICE -->\r\n                            <span style=\"font-size:16px;color:#e03e2d;text-decoration:line-through;\">$49.99</span>\r\n                        </div>\r\n                    </td>\r\n                    <!-- PRODUCT 5 -->\r\n                    <td width=\"33.33%\" valign=\"top\">\r\n                        <div style=\"margin:30px 15px;\">\r\n                            <!-- SECTION IMAGE -->\r\n                            <img src=\"https://via.placeholder.com/150x150\" width=\"150\" height=\"150\" alt=\"\">\r\n                            <!-- PRODUCT NAME -->\r\n                            <h2 style=\"margin:0;color:#6e6f74;font-size:16px;padding:5px 0;font-weight:500;\">Product 5</h2>\r\n                            <!-- PRODUCT PRICE -->\r\n                            <span style=\"font-size:16px;color:#909197;padding-right:2px;\">$67.99</span>\r\n                            <!-- PRODUCT RRP PRICE -->\r\n                            <span style=\"font-size:16px;color:#e03e2d;text-decoration:line-through;\">$79.99</span>\r\n                        </div>\r\n                    </td>\r\n                    <!-- PRODUCT 6 -->\r\n                    <td width=\"33.33%\" valign=\"top\">\r\n                        <div style=\"margin:30px 15px;\">\r\n                            <!-- SECTION IMAGE -->\r\n                            <img src=\"https://via.placeholder.com/150x150\" width=\"150\" height=\"150\" alt=\"\">\r\n                            <!-- PRODUCT NAME -->\r\n                            <h2 style=\"margin:0;color:#6e6f74;font-size:16px;padding:5px 0;font-weight:500;\">Product 6</h2>\r\n                            <!-- PRODUCT PRICE -->\r\n                            <span style=\"font-size:16px;color:#909197;padding-right:2px;\">$39.99</span>\r\n                            <!-- PRODUCT RRP PRICE -->\r\n                            <span style=\"font-size:16px;color:#e03e2d;text-decoration:line-through;\">$59.99</span>\r\n                        </div>\r\n                    </td>\r\n                </tr>\r\n            </table>\r\n        </div>\r\n        <!-- SHOP NOW LINK -->\r\n        <a href=\"%click_link%http://example.com\" style=\"display:inline-block;background-color:#2b7fc4;border-radius:4px;padding:12px 15px;text-decoration:none;color:#fff;font-weight:500;font-size:14px;margin:20px 40px;\">Shop Now</a>\r\n\r\n        %open_tracking_code%\r\n    </div>\r\n    <!-- UNSUBSCRIBE LINK -->\r\n    <p style=\"font-size:14px;text-align:center;color:#636468;margin:30px 0;\">If you no longer want to hear from us, please <a href=\"%unsubscribe_link%\" style=\"font-size:14px;color:#636468;\">click here to unsubscribe</a>.</p>\r\n</div>', NULL, NULL, '2024-01-01 00:00:00');

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

--
-- Dumping data for table `newsletter_subscribers`
--

INSERT INTO `newsletter_subscribers` (`id`, `email`, `date_subbed`, `confirmed`, `status`, `unsub_reason`) VALUES
(1, 'sidewaysy.onlineorders@gmail.com', '2024-02-25 14:56:00', 1, 'Subscribed', NULL);

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

--
-- Dumping data for table `page_completion_status`
--

INSERT INTO `page_completion_status` (`id`, `page_path`, `page_name`, `is_complete`, `completion_notes`, `last_checked`) VALUES
(1, 'index.php', 'Homepage', 0, NULL, '2025-08-14 17:13:15'),
(2, 'about.php', 'About Page', 0, NULL, '2025-08-14 23:13:03'),
(3, 'contact.php', 'Contact Page', 0, NULL, '2025-08-14 17:13:15'),
(4, 'blog.php', 'Blog Listing', 0, NULL, '2025-08-14 17:13:15'),
(5, 'category.php', 'Blog Category', 0, NULL, '2025-08-14 17:13:15'),
(6, 'post.php', 'Blog Post', 0, NULL, '2025-08-14 17:13:15'),
(7, 'products.php', 'Shop Products', 0, NULL, '2025-08-14 17:13:15'),
(8, 'product.php', 'Product Details', 0, NULL, '2025-08-14 17:13:15'),
(9, 'cart.php', 'Shopping Cart', 0, NULL, '2025-08-14 17:13:15'),
(10, 'checkout.php', 'Checkout', 0, NULL, '2025-08-14 17:13:15'),
(11, 'myaccount.php', 'My Account', 0, NULL, '2025-08-14 17:13:15'),
(12, 'auth.php', 'Authentication', 0, NULL, '2025-08-14 17:13:15'),
(13, 'register.php', 'Registration', 0, NULL, '2025-08-14 17:13:15'),
(14, 'profile.php', 'User Profile', 0, NULL, '2025-08-14 17:13:15'),
(15, 'gallery.php', 'Gallery', 0, NULL, '2025-08-14 17:13:15'),
(16, 'search.php', 'Search Results', 0, NULL, '2025-08-14 17:13:15'),
(17, 'faqs.php', 'FAQ Page', 0, NULL, '2025-08-14 17:13:15'),
(18, 'policy-privacy.php', 'Privacy Policy', 0, NULL, '2025-08-14 17:13:15'),
(19, 'policy-terms.php', 'Terms of Service', 0, NULL, '2025-08-14 17:13:15'),
(20, 'policy-accessibility.php', 'Accessibility Policy', 0, NULL, '2025-08-14 17:13:15');

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

--
-- Dumping data for table `polls`
--

INSERT INTO `polls` (`id`, `title`, `description`, `created`, `start_date`, `end_date`, `approved`, `num_choices`) VALUES
(1, 'What\'s your favorite coding language?', '', '2024-01-01 00:00:00', '2024-01-01 00:00:00', NULL, 1, 1),
(2, 'What\'s your favorite gaming console?', '', '2024-01-01 00:00:00', '2024-01-01 00:00:00', '2024-02-01 00:00:00', 1, 1),
(3, 'What\'s your favorite car manufacturer?', 'This is a test description.', '2024-01-01 00:00:00', '2024-01-01 00:00:00', '2024-02-01 00:00:00', 1, 1);

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

--
-- Dumping data for table `polls_categories`
--

INSERT INTO `polls_categories` (`id`, `title`, `description`, `created`) VALUES
(1, 'General', 'This is a test description.', '2024-01-01 00:00:00'),
(2, 'Coding', 'This is a test description.', '2024-01-01 00:00:00'),
(3, 'February Test Collections', '', '2025-08-11 01:13:00');

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

--
-- Dumping data for table `poll_answers`
--

INSERT INTO `poll_answers` (`id`, `poll_id`, `title`, `votes`, `img`) VALUES
(1, 1, 'PHP', 47, ''),
(2, 1, 'Python', 39, ''),
(3, 1, 'C#', 24, ''),
(4, 1, 'Java', 17, ''),
(5, 2, 'PlayStation 5', 50, ''),
(6, 2, 'Xbox Series X', 62, ''),
(7, 2, 'Nintendo Switch', 32, ''),
(8, 3, 'BMW', 225, ''),
(9, 3, 'Ford', 194, ''),
(10, 3, 'Tesla', 248, ''),
(11, 3, 'Honda', 129, ''),
(12, 2, 'Steam Deck', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `poll_categories`
--

CREATE TABLE `poll_categories` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `poll_categories`
--

INSERT INTO `poll_categories` (`id`, `poll_id`, `category_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 3, 1);

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

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `blog_user_id`, `created_at`) VALUES
(1, 'New Application ', 'This is barebones simple blog application for me to screw up on.  (It\'s how I learn.) ', 0, '2025-01-30 21:06:05'),
(2, 'Post Title', 'Post Content', 1, '2025-02-04 18:39:46');

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

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `page_id`, `display_name`, `content`, `rating`, `submit_date`, `approved`, `account_id`, `likes`, `response`) VALUES
(1, 1, 'David Deacon', 'I use this website on a daily basis. The amount of content is brilliant! I\'ve told all my friends and family about the great customer service and how they always answer my questions with a smile!', 5, '2023-02-09 20:43:00', 1, 1, 2, 'Thanks!'),
(2, 1, 'Larry Brown', 'Great website, great content, and great support!', 4, '2023-02-09 21:00:00', 1, 2, 0, ''),
(3, 1, 'Robert Billings', 'Website needs more content. Good website but content is lacking.', 3, '2023-02-09 21:10:00', 1, 3, 0, ''),
(10, 1, 'Barb Administration ', 'Thank you for this page.', 5, '2025-08-12 11:04:00', 1, 1, 3, 'Thank you for your response!');

-- --------------------------------------------------------

--
-- Table structure for table `review_filters`
--

CREATE TABLE `review_filters` (
  `id` int(11) NOT NULL,
  `word` varchar(255) NOT NULL,
  `replacement` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `review_filters`
--

INSERT INTO `review_filters` (`id`, `word`, `replacement`) VALUES
(1, 'Damn', 'Darn');

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

--
-- Dumping data for table `review_page_details`
--

INSERT INTO `review_page_details` (`id`, `page_id`, `title`, `description`, `url`) VALUES
(1, 1, 'Note from the Blog Developer', 'Notes from the blog developer', ' http://localhost:3000/public_html/admin/review_system/');

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

--
-- Dumping data for table `scope`
--

INSERT INTO `scope` (`id`, `title`, `description`, `fee`, `frequency`, `update_date`, `attachment_path`) VALUES
(1, 'Basic Web Maintenance', 'Ongoing updates, plugin checks, and backups.', 150.00, 'monthly', '2025-06-29 12:03:12', NULL),
(2, 'Website Redesign', 'Complete UI/UX redesign with mobile-first layout.', 1200.00, 'one-time', '2025-06-29 12:03:12', NULL);

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

--
-- Dumping data for table `setting_branding_assets`
--

INSERT INTO `setting_branding_assets` (`id`, `business_logo_main`, `business_logo_horizontal`, `business_logo_vertical`, `business_logo_square`, `business_logo_white`, `business_logo_small`, `favicon_main`, `favicon_blog`, `favicon_portal`, `apple_touch_icon`, `social_share_default`, `social_share_facebook`, `social_share_twitter`, `social_share_linkedin`, `social_share_instagram`, `social_share_blog`, `hero_background_image`, `watermark_image`, `loading_animation`, `last_updated`) VALUES
(1, 'assets/img/logo.png', 'assets/branding/logo_horizontal.png', 'assets/branding/logo_vertical.png', 'assets/branding/logo_square.png', 'assets/branding/logo_white.png', 'assets/branding/logo_small.png', 'assets/img/favicon.png', 'assets/branding/favicon_blog.ico', 'assets/branding/favicon_portal.ico', 'assets/img/apple-touch-icon.png', 'assets/branding/social_default.jpg', 'assets/branding/social_facebook.jpg', 'assets/branding/social_twitter.jpg', 'assets/branding/social_linkedin.jpg', 'assets/branding/social_instagram.jpg', 'assets/branding/social_blog.jpg', NULL, NULL, NULL, '2025-08-15 21:00:34');

-- --------------------------------------------------------

--
-- Table structure for table `setting_branding_colors`
--

CREATE TABLE `setting_branding_colors` (
  `id` int(11) NOT NULL,
  `brand_primary_color` varchar(7) NOT NULL DEFAULT '#6c2eb6',
  `brand_secondary_color` varchar(7) NOT NULL DEFAULT '#bf5512',
  `brand_accent_color` varchar(7) DEFAULT '#28a745',
  `brand_warning_color` varchar(7) DEFAULT '#ffc107',
  `brand_danger_color` varchar(7) DEFAULT '#dc3545',
  `brand_info_color` varchar(7) DEFAULT '#17a2b8',
  `brand_background_color` varchar(7) DEFAULT '#ffffff',
  `brand_text_color` varchar(7) DEFAULT '#333333',
  `brand_text_light` varchar(7) DEFAULT '#666666',
  `brand_text_muted` varchar(7) DEFAULT '#999999',
  `brand_success_color` varchar(7) DEFAULT '#28a745',
  `brand_error_color` varchar(7) DEFAULT '#dc3545',
  `custom_color_1` varchar(7) DEFAULT NULL,
  `custom_color_2` varchar(7) DEFAULT NULL,
  `custom_color_3` varchar(7) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_branding_colors`
--

INSERT INTO `setting_branding_colors` (`id`, `brand_primary_color`, `brand_secondary_color`, `brand_accent_color`, `brand_warning_color`, `brand_danger_color`, `brand_info_color`, `brand_background_color`, `brand_text_color`, `brand_text_light`, `brand_text_muted`, `brand_success_color`, `brand_error_color`, `custom_color_1`, `custom_color_2`, `custom_color_3`, `last_updated`) VALUES
(1, '#ed6f45', '#17a2b8', '#19f0c5', '#ffc107', '#dc3545', '#17a2b8', '#ffffff', '#333334', '#666666', '#b0b0b0', '#28a745', '#dc3545', NULL, NULL, NULL, '2025-08-16 18:50:22');

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

--
-- Dumping data for table `setting_branding_fonts`
--

INSERT INTO `setting_branding_fonts` (`id`, `brand_font_primary`, `brand_font_headings`, `brand_font_body`, `brand_font_accent`, `brand_font_monospace`, `brand_font_display`, `brand_font_file_1`, `brand_font_file_2`, `brand_font_file_3`, `brand_font_file_4`, `brand_font_file_5`, `brand_font_file_6`, `font_size_base`, `font_size_small`, `font_size_large`, `font_weight_normal`, `font_weight_bold`, `line_height_base`, `last_updated`) VALUES
(1, 'Roboto, Poppins, Raleway, Arial, sans-serif', 'Poppins, Arial, sans-serif', 'Roboto, Arial, sans-serif', 'Raleway, Arial, sans-serif', 'Consolas, Monaco, &amp;amp;amp;quot;Courier New&amp;amp;amp;quot;, monospace', 'Georgia, &amp;amp;amp;quot;Times New Roman&amp;amp;amp;quot;, serif', NULL, NULL, NULL, NULL, NULL, NULL, '16px', '14px', '18px', '400', '700', '1.5', '2025-08-16 18:50:22');

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
-- Dumping data for table `setting_branding_templates`
--

INSERT INTO `setting_branding_templates` (`id`, `template_key`, `template_name`, `template_description`, `css_class`, `is_active`, `template_config`, `preview_image`, `created_at`, `last_updated`) VALUES
(1, 'template_1', 'Classic', 'Traditional layout with primary color for headers', 'brand-template-classic', 1, NULL, NULL, '2025-08-15 21:00:34', '2025-08-15 21:00:34'),
(2, 'template_2', 'Modern', 'Contemporary layout with secondary color emphasis', 'brand-template-modern', 0, NULL, NULL, '2025-08-15 21:00:34', '2025-08-15 21:00:34'),
(3, 'template_3', 'Bold', 'High contrast layout with accent colors', 'brand-template-bold', 0, NULL, NULL, '2025-08-15 21:00:34', '2025-08-15 21:00:34');

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

--
-- Dumping data for table `setting_business_identity`
--

INSERT INTO `setting_business_identity` (`id`, `business_name_short`, `business_name_medium`, `business_name_long`, `business_tagline_short`, `business_tagline_medium`, `business_tagline_long`, `legal_business_name`, `business_type`, `tax_id`, `registration_number`, `established_date`, `about_business`, `mission_statement`, `vision_statement`, `core_values`, `author`, `footer_business_name_type`, `footer_logo_enabled`, `footer_logo_position`, `footer_logo_file`, `last_updated`) VALUES
(1, 'Burden2Blessings', 'Burden to Blessings', 'Burden to Blessings LLC', 'Sell Your Indiana Home Fast', 'Sell Your Indiana House with Compassion, Speed and a Fair Cash Price.', 'Local experts helping Indiana families sell on their own timeline — always with honesty, fairness, and respect.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'GlitchWizard Solutions LLC', 'medium', 1, 'left', 'favicon', '2025-08-16 18:50:22');

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

--
-- Dumping data for table `setting_contact_info`
--

INSERT INTO `setting_contact_info` (`id`, `contact_email`, `contact_phone`, `contact_address`, `contact_city`, `contact_state`, `contact_zipcode`, `contact_country`, `business_hours`, `time_zone`, `contact_form_email`, `support_email`, `sales_email`, `billing_email`, `emergency_contact`, `mailing_address`, `physical_address`, `gps_coordinates`, `office_locations`, `last_updated`) VALUES
(1, 'barbara@glitchwizardsolutions.com', '+1 850-123-4567', '127 Northwood Road', 'Crawfordville', 'FL', '32327', 'United States', NULL, 'America/New_York', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-16 02:07:29');

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
  `hero_headline` varchar(255) DEFAULT 'Welcome to Our Site',
  `hero_subheadline` text DEFAULT NULL,
  `hero_button_text` varchar(100) DEFAULT 'Get Started',
  `hero_button_link` varchar(255) DEFAULT '#',
  `hero_background_image` varchar(255) DEFAULT NULL,
  `hero_background_video` varchar(255) DEFAULT NULL,
  `hero_overlay_opacity` decimal(3,2) DEFAULT 0.50,
  `services_title` varchar(255) DEFAULT 'Our Services',
  `services_subtitle` text DEFAULT NULL,
  `features_title` varchar(255) DEFAULT 'Features',
  `features_subtitle` text DEFAULT NULL,
  `testimonials_title` varchar(255) DEFAULT 'What Our Clients Say',
  `testimonials_subtitle` text DEFAULT NULL,
  `cta_section_title` varchar(255) DEFAULT 'Ready to Get Started?',
  `cta_section_text` text DEFAULT NULL,
  `cta_button_text` varchar(100) DEFAULT 'Contact Us',
  `cta_button_link` varchar(255) DEFAULT 'contact.php',
  `about_section_title` varchar(255) DEFAULT NULL,
  `about_section_text` text DEFAULT NULL,
  `about_section_image` varchar(255) DEFAULT NULL,
  `stats_enabled` tinyint(1) DEFAULT 1,
  `newsletter_enabled` tinyint(1) DEFAULT 1,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_content_services`
--

CREATE TABLE `setting_content_services` (
  `id` int(11) NOT NULL,
  `service_key` varchar(100) NOT NULL,
  `service_title` varchar(255) NOT NULL,
  `service_description` text DEFAULT NULL,
  `service_short_desc` varchar(500) DEFAULT NULL,
  `service_icon` varchar(255) DEFAULT NULL,
  `service_image` varchar(255) DEFAULT NULL,
  `service_price` varchar(100) DEFAULT NULL,
  `service_duration` varchar(100) DEFAULT NULL,
  `service_features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`service_features`)),
  `service_benefits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`service_benefits`)),
  `service_category` varchar(100) DEFAULT NULL,
  `service_order` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
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
  `client_name` varchar(255) NOT NULL,
  `client_role` varchar(255) DEFAULT NULL,
  `client_company` varchar(255) DEFAULT NULL,
  `client_image` varchar(255) DEFAULT NULL,
  `testimonial_text` text NOT NULL,
  `testimonial_rating` int(11) DEFAULT 5,
  `testimonial_date` date DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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

--
-- Dumping data for table `setting_email_config`
--

INSERT INTO `setting_email_config` (`id`, `mail_enabled`, `mail_from`, `mail_name`, `reply_to`, `smtp_enabled`, `smtp_host`, `smtp_port`, `smtp_username`, `smtp_password`, `smtp_encryption`, `smtp_auth`, `notifications_enabled`, `notification_email`, `auto_reply_enabled`, `email_templates_path`, `email_signature`, `bounce_handling`, `email_tracking`, `unsubscribe_enabled`, `last_updated`) VALUES
(1, 1, 'noreply@glitchwizardsolutions.com', 'GWS Universal', NULL, 0, NULL, 587, NULL, NULL, 'tls', 1, 1, NULL, 1, 'assets/email_templates', NULL, 0, 0, 1, '2025-08-15 21:00:35');

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

--
-- Dumping data for table `setting_system_audit`
--

INSERT INTO `setting_system_audit` (`id`, `setting_key`, `old_value`, `new_value`, `changed_by`, `change_reason`, `changed_at`) VALUES
(1, 'business_identity.author', 'GWS', 'Elizabeth Riggs', 'GlitchWizard', 'Business identity update', '2025-08-16 01:59:33'),
(2, 'business_identity.business_name_short', 'Burden2Blessings', 'Burden2Blessings Short', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:09'),
(3, 'business_identity.business_name_medium', 'Burden to Blessings', 'Burden to Blessings Medium', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:09'),
(4, 'business_identity.business_name_long', 'Burden to Blessings LLC', 'Burden to Blessings LLC Longer Business Name', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:09'),
(5, 'business_identity.business_tagline_short', 'Short Tagline', 'Short Tagline1', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:29'),
(6, 'business_identity.business_tagline_medium', 'Medium tagline for hero sections', 'Medium tagline for hero sections2', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:29'),
(7, 'business_identity.business_tagline_long', 'Longer tagline That spans at least one line and is larger than the medium and the small, of course.', 'Longer tagline That spans at least one line and is larger than the medium and the small, of course.3', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:29'),
(8, 'business_identity.author', 'Elizabeth Riggs', '4', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:29'),
(9, 'business_identity.business_name_short', 'Burden2Blessings Short', 'Burden2Blessings Short2', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:44'),
(10, 'business_identity.business_name_medium', 'Burden to Blessings Medium', 'Burden to Blessings Medium2', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:44'),
(11, 'business_identity.business_name_long', 'Burden to Blessings LLC Longer Business Name', 'Burden to Blessings LLC Longer Business2 Name', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:44'),
(12, 'business_identity.author', '4', '', 'GlitchWizard', 'Business identity update', '2025-08-16 02:00:44'),
(13, 'branding_colors.brand_accent_color', '#28a745', '#19f0c5', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:02:11'),
(14, 'branding_colors.brand_danger_color', '#ff0505', '#dc3545', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:02:11'),
(15, 'branding_colors.brand_info_color', '#17a2b8', '#6dcbd9', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:03:21'),
(16, 'branding_colors.brand_info_color', '#6dcbd9', '#17a2b8', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:03:34'),
(17, 'branding_colors.brand_background_color', '#ffffff', '#7d3636', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:03:34'),
(18, 'branding_colors.brand_danger_color', '#dc3545', '#ff0f27', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:04:08'),
(19, 'branding_colors.brand_background_color', '#7d3636', '#ffffff', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:04:08'),
(20, 'branding_colors.brand_primary_color', '#ed6f45', '#ff5b24', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:04:34'),
(21, 'branding_colors.brand_danger_color', '#ff0f27', '#dc3545', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:04:34'),
(22, 'branding_colors.brand_danger_color', '#dc3545', '#ff0f27', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:05:03'),
(23, 'contact_info.contact_phone', '+1 555-123-4567', '+1 850-123-4567', 'GlitchWizard', 'Contact information update', '2025-08-16 02:07:29'),
(24, 'contact_info.contact_address', '123 Main Street', '127 Northwood Road', 'GlitchWizard', 'Contact information update', '2025-08-16 02:07:29'),
(25, 'branding_colors.brand_primary_color', '#ff5b24', '#ed6f45', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:08:42'),
(26, 'branding_colors.brand_danger_color', '#ff0f27', '#dc3545', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:09:18'),
(27, 'branding_colors.brand_text_muted', '#999999', '#b0b0b0', 'GlitchWizard', 'Brand colors update', '2025-08-16 02:09:18'),
(28, 'business_identity.author', '', 'Test Author 22:15:20', 'debug_test', 'Business identity update', '2025-08-16 02:15:20');

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

--
-- Dumping data for table `shop_discounts`
--

INSERT INTO `shop_discounts` (`id`, `category_ids`, `product_ids`, `discount_code`, `discount_type`, `discount_value`, `start_date`, `end_date`) VALUES
(1, '', '', 'YEAR2025', 'Percentage', 5.00, '2025-01-01 00:00:00', '2025-12-31 00:00:00'),
(2, '', '', '5OFF', 'Fixed', 5.00, '2025-01-01 00:00:00', '2035-01-01 00:00:00');

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

--
-- Dumping data for table `shop_products`
--

INSERT INTO `shop_products` (`id`, `title`, `description`, `sku`, `price`, `rrp`, `quantity`, `created`, `weight`, `url_slug`, `product_status`, `subscription`, `subscription_period`, `subscription_period_type`) VALUES
(1, 'Watch', '<p>Meet our special watch! It\'s made of strong metal and is perfect for anyone who loves watches that can do cool things. It\'s not just a regular watch.</p>\r\n<h3>What makes it great?</h3>\r\n<ul>\r\n<li>It works with Android and has apps already in it.</li>\r\n<li>You can adjust it to fit your wrist just right.</li>\r\n<li>The battery lasts a long time – wear it for 2 days without needing to charge.</li>\r\n<li>It\'s light and comfy to wear all day.</li>\r\n</ul>', 'watch', 29.99, 0.00, -1, '2025-01-01 00:00:00', 0.00, 'smart-watch', 1, 0, 0, 'day'),
(2, 'Wallet', '<p>Discover our sleek black wallet, a must-have accessory that combines simplicity with practicality. It\'s ideal for anyone looking for a reliable yet stylish way to carry their essentials.</p>\r\n<h3>Why you\'ll love it:</h3>\r\n<ul>\r\n<li>Made from durable materials to keep your items safe.</li>\r\n<li>Slim design that fits comfortably in your pocket or purse.</li>\r\n<li>Multiple compartments for cash, cards, and IDs.</li>\r\n<li>Classic black color that matches everything.</li>\r\n</ul>', 'wallet', 14.99, 19.99, -1, '2025-01-01 00:00:00', 0.00, '', 1, 0, 0, 'day'),
(3, 'Headphones', '<p>Experience the freedom of sound with our compact wireless headphones, perfect for those on the move or who love uncluttered simplicity.</p>\r\n<h3>Highlights:</h3>\r\n<ul>\r\n<li>Wireless technology for ultimate mobility and ease.</li>\r\n<li>Compact size for easy storage and portability.</li>\r\n<li>Long-lasting battery for extended listening sessions.</li>\r\n<li>High-quality audio that brings your music to life.</li>\r\n</ul>', 'headphones', 19.99, 0.00, -1, '2025-01-01 00:00:00', 0.00, '', 1, 0, 0, 'day'),
(4, 'Digital Camera', '<p>Discover the world through a lens with our digital camera, designed for both beginners and photography enthusiasts.</p>\r\n<h3>Key Features:</h3>\r\n<ul>\r\n<li>High-resolution imaging for stunning picture quality.</li>\r\n<li>User-friendly interface for easy operation.</li>\r\n<li>Compact and durable design, ready for any adventure.</li>\r\n<li>Powerful zoom to capture distant subjects with clarity.</li>\r\n</ul>', 'digital-camera', 269.99, 0.00, 0, '2025-01-01 00:00:00', 0.00, '', 1, 0, 0, 'day'),
(5, 'Subscription Item 1', '', 'sub-item-1', 15.00, 30.00, -1, '2025-01-01 00:00:00', 0.00, '', 1, 1, 1, 'month');

-- --------------------------------------------------------

--
-- Table structure for table `shop_product_categories`
--

CREATE TABLE `shop_product_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shop_product_categories`
--

INSERT INTO `shop_product_categories` (`id`, `title`, `parent_id`) VALUES
(1, 'Sale', 0),
(2, 'Watches', 0);

-- --------------------------------------------------------

--
-- Table structure for table `shop_product_category`
--

CREATE TABLE `shop_product_category` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shop_product_category`
--

INSERT INTO `shop_product_category` (`id`, `product_id`, `category_id`) VALUES
(1, 1, 2),
(2, 2, 1),
(3, 5, 1);

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

--
-- Dumping data for table `shop_product_media`
--

INSERT INTO `shop_product_media` (`id`, `title`, `caption`, `date_uploaded`, `full_path`) VALUES
(1, 'Watch Front', '', '2025-01-01 00:00:00', 'uploads/watch.jpg'),
(2, 'Watch Side', '', '2025-01-01 00:00:00', 'uploads/watch-2.jpg'),
(3, 'Watch Back', '', '2025-01-01 00:00:00', 'uploads/watch-3.jpg'),
(4, 'Wallet', '', '2025-01-01 00:00:00', 'uploads/wallet.jpg'),
(5, 'Camera', '', '2025-01-01 00:00:00', 'uploads/camera.jpg'),
(6, 'Headphones', '', '2025-01-01 00:00:00', 'uploads/headphones.jpg'),
(7, 'Subscription Placeholder', '', '2025-01-01 00:00:00', 'uploads/subscription.png');

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

--
-- Dumping data for table `shop_product_media_map`
--

INSERT INTO `shop_product_media_map` (`id`, `product_id`, `media_id`, `position`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 2),
(3, 1, 3, 3),
(4, 2, 4, 1),
(5, 3, 6, 1),
(6, 4, 5, 1),
(7, 5, 7, 1);

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

--
-- Dumping data for table `shop_product_options`
--

INSERT INTO `shop_product_options` (`id`, `option_name`, `option_value`, `quantity`, `price`, `price_modifier`, `weight`, `weight_modifier`, `option_type`, `required`, `position`, `product_id`) VALUES
(1, 'Size', 'Small', -1, 9.99, 'add', 9.99, 'add', 'select', 1, 1, 1),
(2, 'Size', 'Large', -1, 8.99, 'add', 8.99, 'add', 'select', 1, 1, 1),
(3, 'Type', 'Standard', -1, 0.00, 'add', 0.00, 'add', 'radio', 1, 2, 1),
(4, 'Type', 'Deluxe', -1, 10.00, 'add', 0.00, 'add', 'radio', 1, 2, 1),
(5, 'Color', 'Red', -1, 1.00, 'add', 10.00, 'add', 'checkbox', 0, 3, 1),
(6, 'Color', 'Yellow', -1, 2.00, 'add', 10.00, 'add', 'checkbox', 0, 3, 1),
(7, 'Color', 'Blue', -1, 3.00, 'add', 10.00, 'add', 'checkbox', 0, 3, 1),
(8, 'Color', 'Purple', 0, 4.00, 'add', 10.00, 'add', 'checkbox', 0, 3, 1),
(9, 'Color', 'Brown', 0, 5.00, 'add', 10.00, 'add', 'checkbox', 0, 3, 1),
(10, 'Color', 'Pink', 0, 6.00, 'add', 10.00, 'add', 'checkbox', 0, 3, 1),
(11, 'Color', 'Orange', -1, 8.00, 'add', 11.00, 'add', 'checkbox', 0, 3, 1),
(12, 'Delivery Date', '', -1, 5.00, 'add', 0.00, 'add', 'datetime', 0, 4, 1),
(13, 'Type', 'Standard', -1, 0.00, 'add', 0.00, 'add', 'radio', 1, 1, 5),
(14, 'Type', 'Premium', -1, 10.00, 'add', 0.00, 'add', 'radio', 1, 1, 5);

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

--
-- Dumping data for table `shop_shipping`
--

INSERT INTO `shop_shipping` (`id`, `title`, `shipping_type`, `countries`, `price_from`, `price_to`, `price`, `weight_from`, `weight_to`) VALUES
(1, 'Standard', 'Entire Order', '', 0.00, 99999.00, 3.99, 0.00, 99999.00),
(2, 'Express', 'Entire Order', '', 0.00, 99999.00, 7.99, 0.00, 99999.00);

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

--
-- Dumping data for table `shop_taxes`
--

INSERT INTO `shop_taxes` (`id`, `country`, `rate`, `rate_type`, `rules`) VALUES
(1, 'United Kingdom', 20.00, 'percentage', NULL);

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

--
-- Dumping data for table `shop_wishlist`
--

INSERT INTO `shop_wishlist` (`id`, `product_id`, `account_id`, `created`) VALUES
(1, 1, 3, '2025-08-12 21:10:10');

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

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `scope_id`, `mine`, `yours`, `file_path`, `status`, `due_date`) VALUES
(1, 1, 'Monitor uptime and updates weekly', 'Report any issues within 24h', NULL, 'pending', '2025-07-15'),
(2, 1, 'Monthly plugin and security updates', 'Provide access to hosting control panel', NULL, 'in-progress', '2025-07-20'),
(3, 2, 'Design new layout concepts', 'Approve mockups before implementation', NULL, 'pending', '2025-08-01');

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

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `title`, `msg`, `full_name`, `email`, `created`, `ticket_status`, `priority`, `category_id`, `private`, `account_id`, `approved`, `last_comment`, `last_update`, `client_ticket`) VALUES
(256, 'Burden to Blessings MVP Development', 'Your website is being staged in your CPanel for MidwestCash4Houses.com in a separate area that is NOT crawled by search engines.  The entire database/website will be moved to a production location when it\'s ready.\r\n\r\nThere are two parts to your website.  This is the front end development link - the final structure depends on the content you provide, so it will be different images and colors, once your branding is submitted.\r\n\r\nhttps://midwestcash4houses.com/burdentoblessings\r\n\r\nYou can make comments here, or upload them to your document folders in this portal. ', 'Elizabeth Riggs', 'work.barbara.moore@gmail.com', '2025-01-01 20:06:00', 'closed', 'high', 1, 1, 0, 1, 'Member', '2025-08-15 20:45:53', 1),
(281, 'Landing Page', 'Hey Barb, I am sending any and all updates for the Main Landing page here. Attached is a picture for that top section of the page that is from Shipshewana, IN, so it has a more relevant feel. ', 'Elizabeth Riggs', 'ehuber1993@gmail.com', '2025-03-13 11:34:27', 'closed', 'medium', 2, 1, 29, 1, 'Member', '2025-03-20 22:07:23', 1),
(282, 'Branding', 'Hey Barb, this is where I will upload all relevant brand documents, including logos, fonts and color codes', 'Elizabeth Riggs', 'ehuber1993@gmail.com', '2025-03-13 12:51:26', 'closed', 'low', 2, 1, 29, 1, 'Admin', '2025-03-20 02:43:09', 1),
(283, 'Services', 'I will upload all relevant information for the services we provide here. If at any point having all of these areas separated becomes stressful or doesn\'t work for you, let me know so we can find a different way to communicate (this is just the way my brain works, separating things into sections that feel more manageable so I am not distracted by the other stuff)', 'Elizabeth Riggs', 'ehuber1993@gmail.com', '2025-03-15 10:19:16', 'closed', 'low', 2, 1, 29, 1, 'Member', '2025-03-20 22:06:42', 1),
(284, 'Hero Section', 'Our monitor is a 27\" monitor (2560x1440)', 'Elizabeth Riggs', 'ehuber1993@gmail.com', '2025-03-16 15:40:29', 'closed', 'medium', 2, 1, 29, 1, 'Member', '2025-03-20 22:06:03', 1),
(285, 'How it works', 'I have looked at several other websites that are somewhat similar to what we do (they are mostly \"sell you house fast for cash\" websites, but that IS how we make our profit, so its not a bad design to borrow ideas from) and they usually have a section that reviews the process, to help ease peoples fears of the unknown and give them a road map of what they can expect when they work with us. I think, because these people are dealing with so much fear and uncertainty this would be a great addition to our website, directly underneath the \"how we Help\" section, but before the contact form. ', 'Elizabeth Riggs', 'ehuber1993@gmail.com', '2025-03-17 15:50:55', 'closed', 'low', 4, 1, 29, 1, 'Member', '2025-03-20 22:06:16', 1),
(286, 'Updated screenshot of hero section', 'heres the updated screenshot you requested. don\'t worry about making it work on our monitor, since ours isn\'t the norm. I can check it on our laptop tomorrow and see how its supposed to look. Tomorrow I plan on tackling our \"about us\" page which should be all we need before we can launch the site fully. Obviously we will continue to tweak and add to it over time, but the basics will be there. I am so excited and grateful for all your hard work on this! Thank you SO much!', 'Elizabeth Riggs', 'ehuber1993@gmail.com', '2025-03-17 16:26:00', 'closed', 'low', 2, 1, 29, 1, 'Member', '2025-03-20 22:04:49', 1),
(287, 'Jd headshot', 'hey, this an updated (but not great) head shot for JD. Hoping to get a new one soon, but this should work for the time being, despite his face looking kinda cranky', 'Elizabeth Riggs', 'ehuber1993@gmail.com', '2025-03-18 15:13:35', 'closed', 'low', 4, 1, 29, 1, 'Admin', '2025-03-21 00:00:28', 1),
(288, 'About Us Page', 'On our  about us page, I think it would be best if we start with Our Mission statement, and then build from there. The mission statement is:\r\nBurden to Blessings empowers Indiana homeowners facing foreclosure by prioritizing their well-being and fostering informed decision-making. Through empathetic guidance and comprehensive resources, we equip individuals with the tools for financial stability, renewed hope, and an empowered mindset. We are committed to a \'people first, house second\' approach, ensuring every action benefits our clients, our community, and our business.', 'Elizabeth Riggs', 'ehuber1993@gmail.com', '2025-03-18 15:24:00', 'closed', 'low', 4, 1, 29, 1, 'Admin', '2025-04-29 19:03:18', 1),
(289, 'Social Media links', 'Barb, here are the links for our social media. they are empty right now, but will begin getting content into them and sharing them shortly.\r\ninstagram:\r\nhttps://www.instagram.com/burden_to_blessings?igsh=cm0zenZoYTV6NjRm\r\n\r\nFacebook:\r\nhttps://www.facebook.com/share/1AXCLnLRmr/', 'Elizabeth Riggs', 'ehuber1993@gmail.com', '2025-03-20 17:10:00', 'closed', 'low', 4, 1, 29, 1, 'Admin', '2025-04-29 19:22:23', 1),
(290, 'Blog Info needed.', '\'gcaptcha_sitekey\'  \r\n\'gcaptcha_secretkey\' \r\n\r\nThese values are generated from your google workspace in the captcha settings area.  Let me know what the values are, and I can integrate them into the blog.  \r\n\r\nThey\'re necessary to secure your blog from hackers and crazies. ', 'Libby Riggs', 'ehuber1993@gmail.com', '2025-04-29 15:06:00', 'closed', 'high', 2, 1, 29, 1, 'Member', '2025-06-06 15:32:27', 1),
(291, 'Blog Decision Needed', 'I coded an application to do comments while I was at it - because when you get further along, you\'ll want the benefits that it gives to SEO.   It\'s disabled currently until we get some questions answered.\r\nFYI - It can remain disabled forever, not a problem.\r\n\r\nLeaving it open to just anyone in the general public is asking for a headache, when all you want is to connect with your potential clients.  I have options though, but depending on your vision, there\'s different ways I can code it.  I\'d like to do it just once, so ...\r\n1. If you will want people to register and log into your site for some reason someday - we can have comments limited to registered and logged in clients.\r\n\r\n2. If you just want people to subscribe to the blog (so they get new posts in an email whenever you post something) and to NOT have to register or log in - I can code comments to only allow people who are subscribed to the blog in their email to come and comment on it. \r\n\r\n3. Screw it, no one can comment.\r\n4. Screw it, everyone can comment and you\'ll just delete the ones that are scammy, spammy, rude or inappropriate.  \r\n5. We COULD make it so someone has to request comment ability, like instead of going to a login form, they just go to your normal webpage at the message box and we can have \"request commenting capabilities\" or something.  That\'s a little odd - but just gives you an idea that this is totally customizable to how you want to approach this.\r\n\r\nMY recommendation is to give it to the people who are already subscribed to your blog.  This gives them repeat visibility to your business, and is an incentive to give you their email address.  I can code it to where you can disable a subscriber from commenting, while leaving them subscribed - in case they are just being annoying.  Think on it, let me know.   ', 'Libby Riggs', 'ehuber1993@gmail.com', '2025-04-29 15:09:00', 'closed', 'high', 2, 1, 29, 1, 'Member', '2025-06-06 15:32:51', 1),
(292, 'A2P 10DLC ', 'I need the \"Texting okay?\" Option to be updated into a compliant option. Below is a link to the minimum requirements\r\nhttps://aws.amazon.com/blogs/messaging-and-targeting/how-to-build-a-compliant-sms-opt-in-process-with-amazon-pinpoint/\r\n\r\nThank you <3', 'Elizabeth Riggs', 'ehuber1993@gmail.com', '2025-05-21 11:02:54', 'closed', 'high', 2, 1, 29, 1, 'Admin', '2025-08-15 20:47:15', 1),
(293, 'Documents Downloads from Website', 'You said you would like a place people can download free documents from your website. Do you want this to be in blog application? You could blog about it, and have a \"download it\" link- or do you want another method for them to get it, like a download page?', 'Libby Riggs', 'ehuber1993@gmail.com', '2025-06-05 07:41:00', 'open', 'low', 2, 1, 29, 1, 'Admin', '2025-06-10 18:28:21', 1),
(294, 'Google Business Profile Resource', 'Google Business Profile is an awesomely powerful tool, that while it doesn\'t replace advertising - it works hand in hand with your website, your sms, your business trustworthiness to search engines and I can\'t stress it enough.  Since you have JD doing all that kind of stuff - there\'s a lot to learn, and it really needs maintained, but the ROI of time you put into it, you will absolutely get back out of it. \r\nThat being said, there is a guy that explains some of the ways it helps with ranking in this video - it\'s only like 16 minutes and absolutely can be put on 1.5 speed to get the gyst of it.  I have had courses in SEO and worked for a google subcontractor - so I can verify this information is basic, but really good place to start in how google is thinking.  I love how he points out that Google\'s aim is to be the best search engine & doesn\'t really care about your business, they just want to put forth the best business for the query - it puts all of the rest of the seo in context.  Would have been nice to start with that, for me, so I\'m passing it on as a starting point for JD.  :-)\r\n ', 'Barbara Moore', 'webdev@glitchwizardsolutions.com', '2025-06-10 13:29:00', 'resolved', 'medium', 1, 1, 29, 1, 'Admin', '2025-08-15 20:49:52', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tickets_categories`
--

CREATE TABLE `tickets_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tickets_categories`
--

INSERT INTO `tickets_categories` (`id`, `title`) VALUES
(1, 'General'),
(2, 'Technical'),
(3, 'Other');

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

--
-- Dumping data for table `tickets_comments`
--

INSERT INTO `tickets_comments` (`id`, `ticket_id`, `msg`, `created`, `account_id`, `new`, `reply`) VALUES
(187, 256, 'I have ordered your burden-to-blessings.com domain.  It\'s included in hosting, so there\'s no separate invoice for it.  ', '2025-01-04 14:08:08', 3, 'Admin', 1),
(189, 256, 'Libby,\r\nAs soon as you decide on your logo for sure,  upload it into your documents folder on this portal.  If you want it only at the footer, or only at the top, or only on documents that people can download, let me know.  :-)', '2025-01-10 13:16:06', 3, 'Admin', 1),
(240, 256, 'Your website is ready for meta tags now, and meta description.  I\'ve been sending/receiving messages in both text and emails, but am having to convert to just using this ticket system I made - because I don\'t want to drop the ball on anything.  I did send an email - but it\'s too much to go copy all the texts and emails and put them in the documents section.  So - I\'ll update here from today forward, and send you a text to remind you to look if it\'s time sensitive.  :-)\r\nPlease pick one of these, or write your own.  Thanks!\r\n1. Navigate the foreclosure process with ease. We provide alternatives like loan modifications, allowing you to focus on your family\'s future without stress.\r\n\r\n2. Simplify your foreclosure journey with our expert assistance. We offer loan modifications and alternatives, helping you prioritize your family\'s next steps.\r\n\r\n3. Let us manage the complexities of foreclosure for you. Explore our loan modification options and focus on what truly matters for your family\'s future.', '2025-03-04 10:53:18', 3, 'Admin', 1),
(241, 256, 'This is what we talked about briefly on the phone, because you are collecting lead data with your form.  \r\nIf you want to see what it looks like before you do it, go to my website glitchwizardsolutions.com and it\'s on the bottom left. \r\n\r\nhttps://app.termageddon.com/?fp_ref=glitchwiz\r\n\r\nI recommend this FAR over a lawyer doing it - because it\'s way more comprehensive and auto-updates.  It will send you a notice each time a law changes, which I usually ignore - except for when it involves extra questions to log on and answer.\r\n\r\nGive yourself TIME to do this because there are a gazillion questions.   \r\n\r\nAt the end, I just need the copy/paste code to integrate into your website.\r\n\r\nIf you have questions on the questions, they are super easy to just click the question mark next to the question and it explains it.  It also tells you what most people select for the answer.  I contacted their help desk before, when I had some questions - and they are right on things, very helpful. \r\n', '2025-03-04 12:01:21', 3, 'Admin', 1),
(242, 256, 'Updated the following on the website\'s contact section.\r\nhelp@burden-to-blessings.com\r\n(574)-633-1736\r\n5776 Grape Rd.\r\nSTE 51, PMB 141\r\nMishawaka, IN 46545', '2025-03-04 17:13:17', 3, 'Admin', 1),
(243, 256, 'I have completed testing on the admin login.  I can make a login anywhere, but currently, the one works at midwestcash4houses.com/login.php and I\'ve integrated that with burden-to-blessings.com so there is only one login.  If there comes a point you want a client portal, we\'ll of course have the login for clients to be at whatever domain you want it to be.  I made a Forgot Password bonus - so if you want to reset your password, or if you forget it, you can reset it without having to wait for me to do it - it\'s all encrypted, so I can\'t see what is on the database to tell you what it is.  \r\nI also completed adding a policy page, disclaimer page and terms page, adding the links to them at the footer of each page.  ', '2025-03-04 21:31:26', 3, 'Admin', 1),
(244, 256, 'I went ahead and created a full on custom application for the contact form to do some behind the scenes gymnastics.  Now, when someone submits the form, they have to put a phone number (for those who have no phone number, due to disability, they can type in all zeros) but they do need to choose yes or no for if texting is approved.  I coded it so that it checks the database to see if the number is already in the text_ok table.  If so, it will update the email address and the yes or no, plus the date it was first added AND the date it was updated.  If it\'s not already in there, it just adds the new record. This way, if you ever have need to prove anything, we can just query the database and print out the list of numbers, the dates, the email addresses that made the change and the date it was changed.  We may never need that - but it fills all the legal requirements for your purposes.  Since you don\'t want to probably look that information up for each new lead, I ALSO have it writing to update/insert the leads table.  You will always have the most current information on whether you can text them, while looking at the leads in your admin.  ALSO, I have included the same information in the messages.  This way, if someone sends you a bunch of messages, the message you are replying to will have the yes or no for texting - in case they change their mind in a particular email - you have a timestamp and the okay or not okay, right where you are in the message you are to reply to.  ', '2025-03-04 21:41:32', 3, 'Admin', 1),
(245, 256, 'Are planning to use GoogleAdSense? I assume you want your Google Analytics integrated into your website & blog. If you have the Google Analytics code set up in your Google Workspace how you want it, it should generate a code for you.', '2025-03-07 20:24:01', 3, 'Admin', 1),
(246, 281, 'Header\r\nReplace: \"Is Foreclosure on your Horizon?\"\r\nwith: \"Indiana Foreclosure Help\"\r\n\r\nSub-Header\r\nReplace: \"We handle the complexities of the forclosure process, so you can focus on what\'s next for your family!\"\r\nWith: \"We provide Hoosier families with free customized foreclosure prevention plans, because no one should have to face these burdens alone.\"\r\n\r\nCall to Action\r\nReplace: \"Let us help\"\r\nwith: \"Get My Free Plan!\"', '2025-03-13 11:09:56', 29, 'Member', 1),
(247, 282, 'I went into canva and tweaked the color codes. The following is our \"brand kit\" of codes from canva:\r\nblack: #373333\r\nCoral: #dd8f75\r\nPeach: #e8baa3\r\nYellow: #ddaa50\r\nTurquoise:  #9bbbb7\r\nLight blue: #b3ced1\r\n', '2025-03-13 11:54:16', 29, 'Member', 1),
(248, 281, 'It is 2:30am.  I\'ve worked on how to make your Hero easier to read, but putting an opaque value on it made it look more like nighttime than evening... and I have fussed with it as much as I could - changing colors, font size and weight, and editing the photo to not have so much \"noise\" where the text is. :-)  G\'night!', '2025-03-14 01:36:05', 3, 'Admin', 1),
(249, 282, 'I used your new black & it washed out the text in most of the site- so I will revisit that (I can make it bigger, or bolder to comply with accessibility, rather than just using a higher contrast black.) ', '2025-03-14 09:47:34', 3, 'Admin', 1),
(250, 281, 'Look at hero. Let me know if you want another image that is more text friendly or overlay friendly, or if you like it as-is, or if you want the image on one side & text on the other or no image, just maybe text & logo (& call to action). I fussed with the image, opacity, font size/weight, etc. and it just isn\'t looking good with that busy image with it\'s mix of contrasts. ', '2025-03-14 09:53:41', 3, 'Admin', 1),
(251, 281, 'Holy yikes that picture was not good on the website! Lets scrap that', '2025-03-15 09:07:36', 29, 'Member', 1),
(252, 281, 'I\'m not sure what to do about the picture :/ I am uploading other pictures I have that I thought might work on the website, but I don\'t know how well any of them will really work. They will be uploaded in the \"Documents\" tab since I can\'t add them to the ticket.', '2025-03-15 09:11:07', 29, 'Member', 1),
(253, 282, 'If we use a different black on the website vs. posting on social media I don\'t think thats a big deal :) Feel free to just use a higher contrast black', '2025-03-15 09:14:28', 29, 'Member', 1),
(254, 281, 'If it\'s possible to make the words \" Indiana Foreclosure Help\" larger and the words beneath it a little bigger as well, or will that throw things off?', '2025-03-15 09:17:13', 29, 'Member', 1),
(255, 283, 'Replace: Loan Modification ( we legally cannot do this as we are not attorneys)\r\nwith: Customized Foreclosure Prevention Plans\r\ndescription: Our Customized Foreclosure Prevention Plans provide clear, actionable steps to help you navigate this challenging time. Whether your goal is to keep your home or sell and start fresh, we\'ll create a plan tailored to your unique situation. Avoid the trauma of a sheriff sale and the long-term impact of foreclosure on your credit. We take a holistic approach, addressing not just your mortgage and housing, but your entire lifestyle. We\'ll equip you with the tools and resources you need to build resilience and achieve lasting financial stability. Let us help you find your path forward!', '2025-03-15 09:34:10', 29, 'Member', 1),
(256, 283, 'Replace: Selling your home\r\nwith: Relocation Assistance\r\nDescription: Moving can be stressful, especially when you\'re looking for safe and affordable housing. We understand the challenges Indiana families face when vetting quality landlords. That\'s why we offer comprehensive Relocation Assistance, even if you\'re not facing foreclosure. We connect you with a network of heavily vetted landlords who are committed to providing high-quality housing in our Indiana communities. Our goal is to ensure you move to a better, safer living situation. We advocate for you, negotiating win-win scenarios that benefit both you and your new landlord. Let us take the stress out of your move and help you find a place you can truly call home.', '2025-03-15 09:41:45', 29, 'Member', 1),
(257, 283, 'Replace: Avoid Homelessness\r\nwith: Asset Management & Advocacy \r\nDescription: Our Asset Management and Advocacy service gives you a powerful voice and a knowledgeable guide to navigate this challenging process. We understand the fear and frustration of dealing with uncooperative lenders. We\'ll step in and advocate on your behalf, using our deep understanding of mortgage companies and the foreclosure process to get you the answers and results you deserve. After understanding your unique needs and goals, we\'ll communicate directly with your lender, asking the right questions and demanding the solutions you need. Let us be your advocate and help you find a clear path forward.', '2025-03-15 09:54:20', 29, 'Member', 1),
(258, 283, 'Making the changes.  Have a thoughts about wording though.  In a nutshell, your target audience is not going to read all the text on this page.  Someone afraid of facing homelessness is not going to click on words that don\'t say \"avoid homelessness\" or some other short statement that says it prettier but not fancier, about not letting the bank take your shelter.  We can do a separate landing page for each service.  (You keep referring to your main website page as landing page, but it is not a landing page, technically.)  A landing page has specific keywords,  and only one service or product on it.  You can say something on your website service section to get them to click on a link to the landing page for that service.  Here\'s a resource.  Look at this website (National literacy institute) and find your target audience FOR THIS website.  I\'m assuming working class or under.  Find the grade level the majority of that target is reading at.  Open up a Microsoft Word document and put all your words in there.  Click on the editor part and adjust the level of evaluation.  It should have a grade level in there.  It is absolutely okay to bask in the glory that you read and write at a higher grade level than your target audience, but then you have to use words they can read and understand.  Once you get that, we can use that.   I\'ll make the changes as you specified, I\'m giving you food for thought here. ', '2025-03-15 15:54:32', 3, 'Admin', 1),
(259, 283, 'The SEO courses I have taken talked about research on keeping folks on track on a website to convert into paying customers, and the homepage should have short options and links to click on.   In a nutshell, they land on the page and see the biggest print - which addresses their pain point.  \"Facing Forclosure?\" or \"Selling your Home?\" or whatever they are looking for.  Okay, so they\'re in the right spot, now what?  Next thing is slightly smaller print saying a little more to pull them in, about how you can help them solve their problem.  Then they want to know how they can get you to solve the problem (click here, see more, contact us, call us, etc. which is the Call to Action button/link or form.  The homepage is NOT where you want all the information beyond what is stated unless it\'s stated in different ways.  Such as facts listed on how great you can help or services in addition to what they are looking for. The website map will let google know the pages are all on the same site.  So, short, to the point on main page, then add a \"click to learn more\" kind of link on each service where all the words come in.  The words on homepage and on landing pages are critical to be on grade level (don\'t forget people who have English as their second language, and people using accessibility tools to \"listen\" to the website, too.)  Once you get into the ideal client mindset, you will break down long paragraphs into bite size information on the actual landing page.  ', '2025-03-15 16:10:00', 3, 'Admin', 1),
(260, 283, 'https://www.thenationalliteracyinstitute.com/2024-2025literacy-statistics  (Here\'s the URL for the literacy info, apparently it didn\'t come through correctly on the previous message.  :-) )', '2025-03-15 16:11:44', 3, 'Admin', 1),
(261, 283, 'At this timestamp, the changes on this ticket have been made as requested.  (There is nothing wrong with editing later, Google LOVES maintained pages, and even refining words updates the updated date to improve what search engines call \"Authority.\"  (When I say Google, I actually mean ALL the web crawler engines.  I will be submitting to all the major ones and several more popular minor ones, and the one for people with disabilities, once I get your site back to readable with the \"black\" you like.  I can\'t submit it there until it is back to being W3C compliant.  ', '2025-03-15 16:18:26', 3, 'Admin', 1),
(262, 282, 'W3C appreciates that.  :-)  The black you like (I\'m partial to Charcoal myself) can be used for larger and bolder print like headings and stuff, because it is easier to read.  We can still use the black, just using it for paragraphs is rough on the eyeballs of even me - and would wash out completely for people with astigmatisms or cataracts or old eyes, or partial blindness.  Disabled people can bring in money, too.  Let your competitors have websites that aren\'t compliant. :-D', '2025-03-15 16:22:30', 3, 'Admin', 1),
(263, 282, 'https://venngage.com/tools/accessible-color-palette-generator if you\'re wanting to play.  ', '2025-03-15 16:25:09', 3, 'Admin', 1),
(264, 281, 'Possible?  Of course.   ', '2025-03-15 20:25:10', 3, 'Admin', 1),
(265, 282, 'At this timestamp, the changes on this ticket have been made as requested. ', '2025-03-15 23:06:38', 3, 'Admin', 1),
(266, 281, 'At this timestamp, the changes on this ticket have been made as requested. ', '2025-03-15 23:07:33', 3, 'Admin', 1),
(267, 281, 'I love the direction you took with this portion of the website. I just checked back and it looks really good. I was wondering if all the wording could be bigger, especially \" Indiana Foreclosure help\" as we want to to really grab their attention immediately. Also, If we could move the verbiage down and to the right some, I think it would help with all the dead space left in the hero section.', '2025-03-16 13:52:39', 29, 'Member', 1),
(268, 283, 'You know, its funny you say this because I was thinking about it yesterday when I sent this information over. I created an ideal client avatar (her name is Brenda) and have found she likely is a 3rd or 4th grade reading level, so I will send updates to the services section. I think I am battling my ego sometimes to \"prove\" we know what the heck we are doing and talking about, so thank you for reminding me that this isn\'t really about me.', '2025-03-16 14:05:22', 29, 'Member', 1),
(269, 283, 'Replace: Customized Foreclosure Prevention plans\"\r\nwith: \"Free Plans to Keep your House\"\r\ndescription: \"We make a plan just for YOU to help you keep your home. We give you the tools you need to help you find your way and hold your hand through it all.\"', '2025-03-16 14:13:00', 29, 'Member', 1),
(270, 281, 'Send me a screenshot & the size of your display (approximate) that you\'re viewing from so I can get what you mean. It hasn\'t gone through the responsiveness stuff yet (to look awesome regardless of screen size) so right now it\'s responsive on my phone and my monitor. I have the tools to adjust it, but until I know what other content changes, that is usually left to later., to avoid re-doing it a bunch of times. :-) ', '2025-03-16 14:15:01', 3, 'Admin', 1),
(271, 283, 'Replace: Relocation assistance\r\nwith \"Help Moving\"\r\ndescription: \"Moving is hard. We can help you find a good and safe new home. We can help with movers, boxes, and a moving truck too! Let us carry that burden for you!\"', '2025-03-16 14:18:10', 29, 'Member', 1),
(272, 283, 'Replace: Asset Management & Advocacy \r\nwith: Help Talking to your Bank\r\ndescription: \"Is your bank hard to talk to? We talk to the bank WITH you. We know how the banks work. We help you ask the right questions to get you answers. Unlike the bank, we are on YOUR side.\"', '2025-03-16 14:21:55', 29, 'Member', 1),
(273, 281, 'Our monitor is a 27\" monitor (2560x1440), will make a new ticket to upload screenshot. on my phone the website looks so different haha', '2025-03-16 14:39:47', 29, 'Member', 1),
(274, 283, 'Replace: Financial assistance\r\nwith: Help with Bills\"\r\nDescription: \"Struggling to keep up with all your bills? We help you find local groups that can help catch up your late bills for water, gas, and electricity.\"', '2025-03-16 14:50:06', 29, 'Member', 1),
(275, 284, 'Yeah, that\'s not what my monitor looks like. :-D That will all get .css styling for different monitors. I\'ll set my tools to your monitor size to do the stuff so you can see it- then will do the responsiveness at the end for all sizes. That is not at all what the finished project will look like. :-D ', '2025-03-16 15:02:16', 3, 'Admin', 1),
(276, 284, 'Interesting! I have no idea how all of this really works, but I trust you :)', '2025-03-17 13:10:50', 29, 'Member', 1),
(277, 283, 'Change: SERVICES\r\nto: How We Help\r\ndescription: We do this for free for Indiana families, because we are better together.\r\n\r\n( idk if that cheesy, I wanted to simplify it to match the other language on the website. feel free to tweak it if something else comes to mind that you feel fits a bit better)', '2025-03-17 13:15:17', 29, 'Member', 1),
(278, 283, 'Change: Encouragement and Motivation\r\nto: Help Sell your House\r\nDescription: We offer different options to sell your house and show you why each option is good and bad, to help you find the best one for you. ', '2025-03-17 13:27:26', 29, 'Member', 1),
(279, 283, 'One last thing for this section. Can we change the background from that teal color to either the peach of coral color and change the yellow that is the services themselves to the teal color? Then this section should be \"done\"', '2025-03-17 14:44:24', 29, 'Member', 1),
(280, 283, 'One last thing, I just realized that the little Icons in each service dont really line up with what the box says. I\'m not sure if you can change these icons, or if it just makes more sense to re-arrange what is in each box to better match the icon in it, but we should probably switch them around :)', '2025-03-17 14:46:10', 29, 'Member', 1),
(281, 284, 'I just went ahead and did all the responsiveness and accessibility - without looking at the changes you must have been making while I did it.  Before I do anything else, I want you to look at your monitor.  If it is the dimensions, you say it is - that\'s not as common of a viewport.  I have been doing responsiveness for the 17 most commonly used viewports, and using the viewport height as 100vh (meaning to make the hero cover the entire monitor for the background image.  I have done a work around for the larger height to make it look better, but cannot \"see\" that size the same as you.  My tool does a simulation, but it\'s not the same thing, so I usually test on the 17 most common, on my phone, on Donna\'s and her dad\'s, and on all my laptops and tablets.  (We will not discuss why I have so many... ha ha!) Can you look and send another screenshot?  My tools say it still stretches, but isn\'t all up in the middle top like your photo.  ', '2025-03-17 15:04:27', 3, 'Admin', 1),
(282, 284, 'Also, why is your resolution so high?  I have not seen that except on a television for HD.  If you rightclick on the desktop and go to display - please check what the recommended resolution is (you don\'t have to change it to that, just tell me the numbers.)  Thanks.  :-)', '2025-03-17 15:11:50', 3, 'Admin', 1),
(283, 285, 'Step 1. Contact us\r\nCall us or use the form on our site. We are ready to help you!\r\n\r\nStep 2. Meet with Us\r\nWe will meet with you in-person or over the phone. We want to hear your story. This helps us know how to help you.\r\n\r\nStep 3. We make your plan\r\nWe make a plan just for you. Your plan tells you what to do and when to do it.\r\n\r\nStep 4. We help you Do your Plan\r\nWe stay in-touch for each step of your plan. We help answer your questions and give you tools when you need them. We help you stay strong when you want to give up.\r\n\r\nStep 5. You get Blessed\r\nWe help you until your plan is done. You get the fresh start you deserve.\r\n', '2025-03-17 15:12:30', 29, 'Member', 1),
(284, 284, 'Fancy pants. ', '2025-03-17 15:14:10', 3, 'Admin', 1),
(285, 284, 'The recommended display is 2560x1440. I did a lot of research before purchasing our monitor to find one with excellent display that would cause less eye strain, and landed on an asus proArt. I dont know anything about computers hahah', '2025-03-17 15:18:19', 29, 'Member', 1),
(286, 286, 'Oh, I forgot, theres this weird little black box on my hero section at the top left corner. Idk if you see it on your screen or not, but I wanted to bring it to your attention', '2025-03-17 15:26:48', 29, 'Member', 1),
(287, 286, 'It should be hidden until it gets focus.  The box should say \"Skip to Content\" or something like that.  It\'s so that the people using something other than a browser can click it and bypass the menu to go straight to the content in their disability tool.  It\'s called a skip link. It should be offscreen until it gets focus.  ', '2025-03-17 19:10:29', 3, 'Admin', 1),
(288, 286, 'PS The box is only showing because of the monitor size, it\'s hidden on other sizes.  You can see the full thing if you don\'t touch anything after your page loads - then press the TAB key.  Then you will see the skip link in all it\'s beautiful accessible glory!  I\'ll see if I can bump it off page a few more pixels to not show on your monitor.  ', '2025-03-17 19:13:15', 3, 'Admin', 1),
(289, 283, 'I just made up some icons as placeholders, in case your brand lady made some for you.  Generally, the content is all in place before icons go on them - but since you indicated we were pretty much done with the section after the last changes, I made some more that are relevant and put them up.  Before you chalk this section off as done, I changed the one that said something like Clarification -  it was still around grade 10-12.  :-)  (Yes, the irony made me smile.)  Check the verbiage & let me know if you want to make further edits.  I just went by the KISS (keep it simple, stupid) rules.  :-)', '2025-03-17 19:39:51', 3, 'Admin', 1),
(290, 281, 'As of the current timestamp, the requested changes have been completed.\r\n(I think I will copy this into a document and have it ready to copy/paste into these ticket things.  In reality, I will think about doing it for probably the rest of my life and never actually do it.  :-) )', '2025-03-17 19:42:14', 3, 'Admin', 1),
(291, 284, 'I had a small business back in the late 90s where my fiancé Jeff did all the network cabling and went to school, while I built custom computers for businesses and the public schools.  To ME, Asus is what made the best processors.  :-) ', '2025-03-17 19:46:25', 3, 'Admin', 1),
(292, 285, 'I have the copy on the page, but have to break up the sections (alternate color/white backgrounds) so nothing is formatted yet. I\'ll be back on this tomorrow afternoon.  ', '2025-03-17 20:58:48', 3, 'Admin', 1),
(293, 286, 'Interesting', '2025-03-18 13:01:04', 29, 'Member', 1),
(294, 285, 'It looks really nice so far, excited to see what magic you work on it!\r\n', '2025-03-18 13:58:50', 29, 'Member', 1),
(295, 283, 'I am scrolling through and I see that one of the service I sent you didn\'t make it on the ticket for some reason. the section that says \"Keep it simple\" should say \" Help with food\" and the description should read:    \"Are you hungry? We help you get food for your family. We help you find a hot meal because no neighbor should go hungry.\"', '2025-03-18 14:05:59', 29, 'Member', 1),
(296, 288, 'After the Mission statement, a section called \"Our Promise to You\" that outlines our core values and commitments would be powerful. This could look like: \r\nPEOPLE FIRST, HOUSE SECOND: We believe helping people is more important than just dealing with houses. We are committed to understanding your needs to help you find the best path forward.\"\r\n\r\nWE LISTEN & HELP: We take the time to listen to your story. We work with you to find the best solutions and support you through tough times. You are not alone!\r\n\r\nDOING WHAT\'S RIGHT: We make decisions that are good for you, our community, and our business. We believe in honesty and fairness in all we do.', '2025-03-18 14:34:31', 29, 'Member', 1),
(297, 288, 'After the \"Our Promise\" section there should be another call to action I was thinking something like \"Your Story matters\" and then ON the button \"Get Personalized Help Now!\"', '2025-03-18 14:37:46', 29, 'Member', 1),
(298, 288, 'Ok, after the Call to action lets have \" MEET THE TEAM\"\r\nELIZABETH RIGGS: Foreclosure specialist (the description on this is already pretty spot-on so we can leave it)\r\n\r\nJON-DAVID RIGGS: Logistics manager\r\nI handle all of the paper-work and behind the scenes stuff. Some may say it\'s boring, but I know without the boring technical stuff, we can\'t help families the way they really deserve.\r\n\r\nOUR FAMILY: Purpose-Coordinators\r\n(the description here is already great)\r\n', '2025-03-18 14:52:43', 29, 'Member', 1),
(299, 288, 'Ok, after \" Meet the team\" Lets do our vision statement, but lets call it \"Our Goal\"\r\n\r\nOur goal is to transform Indiana from a state burdened by high foreclosure rates to a model of financial resilience and community well-being. Within the next decade, we aspire to drive Indiana into the bottom ten states for foreclosures by expanding access to resources, promoting financial literacy, and advocating for healthy lifestyles in underserved communities. We envision vibrant neighborhoods enriched by urban farms, community gardens, and accessible recreational spaces, where every individual has the opportunity to thrive. We aim to create a lasting legacy of hope, empowerment, and prosperity across the state.', '2025-03-18 14:58:37', 29, 'Member', 1),
(300, 288, 'Also, maybe instead of the picture of my next to the top section, we can use 1 of the pictures I uploaded in the documents tab that are local images? I\'m not sure if the quality of any of them will work, but I like the idea of getting some local imagery on our website, not just our faces and logos (although I LOVE having our logo scattered like confetti all through the page to help cement our brand into our clients minds, so please do not take that as criticism. )', '2025-03-18 15:01:36', 29, 'Member', 1),
(301, 288, 'I think that will be a wrap, and once all of this is in place, we should be ready to go live. Later I will want to add a blog section where we can share helpful articles. I will want to also add a \" Testimonials\" section, but I need to reach out to people and get those collected first haha. I will also eventually want to have some free resources that people can download (maybe something they get as a download for submitting their email) and all of that, but we have exactly what we need to get started, now I need to go make a google business profile, a facebook and instagram account and start creating some content. Thank you for all of you hard work I am so proud of the service we are getting to provide and the incredibly important role you play in bringing this to more Indiana families.', '2025-03-18 15:05:03', 29, 'Member', 1),
(302, 286, 'Everything on this ticket is completed, so I am setting it to resolved.  You have the option to reopen it, or close it out.', '2025-03-19 19:55:26', 3, 'Admin', 1),
(303, 285, 'Everything in this ticket is completed, so I am resolving the ticket.  You have the option to reopen it, if you have something further for this section, or close it, if you are done with it.', '2025-03-19 19:56:40', 3, 'Admin', 1),
(304, 283, 'I have completed this, and am resolving the ticket. ', '2025-03-19 20:04:00', 3, 'Admin', 1),
(305, 288, 'I never did go help Mom today, so that will be tomorrow.  I worked on my end of this stuff all day, and started on these tickets.   I have started this ticket, but want to ask you if you REALLY want the photos you sent.  They\'re great photography, but they do not represent your clients.  One, they won\'t be moving to a lake front property, nor will they live in city buildings, etc.  If you really want to go with photos around the city - maybe do some entryways to parks in the lower income areas.  Take the photos from an angle there are no children in the photos (but adults you know, maybe with a cheap bike, looking like they\'re just happening by) or set up a picnic with basic items (sheet, paper plates, kind of thing) at another park, something that they can see and think \"I want that.  I can get that if I call this lady.\" instead of being unrelatable.  Maybe a low income house back yard, where you can see the back door, but not identify the house.  The yard can have a modest fence, maybe a dog.  Things that she (your client persona) can feel drawn to - remember, she\'s about to have NOTHING.  Everything is out of reach to her.  Local parks are not out of reach if you help them find a place near it.  Picnics aren\'t out of reach if you help them find a food pantry.  A back yard is a pretty big reach, but it isn\'t as far away as a lake cottage. :-)  Just my two cents.  I\'ll put them in if you think I\'m off my rocker. I figure they have no family, or they\'d be there.', '2025-03-19 20:18:53', 3, 'Admin', 1),
(306, 288, 'While I\'m helping mom, you hopefully will have time for more social media and google analytics stuff, but really, that is prime stuff to be added piecemeal anyway.  I\'ve somehow lost the scroll to top thing being visible and finding what on Earth I did to lose it has been harrowing.  :-)  I\'m telling myself that it is also an update that Google will like to see... doesn\'t have to be on the page today.  I have to move - I\'ve been here all day (literally got up to pee since I talked to you - but lots of good stuff in the behind-the-pretty-pictures got done.  Learned some things, screwed up and backed up and punted - the usual.  ha ha!  DO check out the thing behind the hero.  The colors.  That is all code, not a photo.  I can change the angle and everything.  (I learned that new.)  :-)', '2025-03-19 20:24:33', 3, 'Admin', 1),
(307, 287, 'Changed the link now.  It\'s burden-to-blessings.com/index-production.php so it\'s pretty close to live.', '2025-03-19 20:29:23', 3, 'Admin', 1),
(308, 287, 'I kinda liked the homeless beard image, but this one does make him look more like a logistics expert.  :-)  Man, it was hard not to use one of him being like 5.  :-D', '2025-03-19 20:33:14', 3, 'Admin', 1),
(309, 287, 'This has been resolved.', '2025-03-19 20:33:42', 3, 'Admin', 1),
(310, 256, 'I am resolving this ticket.  ', '2025-03-19 20:39:43', 3, 'Admin', 1),
(311, 281, 'You can reopen this ticket if you have anything further.', '2025-03-19 20:40:43', 3, 'Admin', 1),
(312, 282, 'Closing this ticket.', '2025-03-19 20:43:09', 3, 'Admin', 1),
(313, 284, 'Resolving this ticket.', '2025-03-19 20:43:45', 3, 'Admin', 1),
(314, 287, 'lol a picture of a 5 year old would be cute and very confusing hahah', '2025-03-20 16:04:11', 29, 'Member', 1),
(315, 286, 'sounds good!', '2025-03-20 16:04:49', 29, 'Member', 1),
(316, 284, 'done', '2025-03-20 16:06:03', 29, 'Member', 1),
(317, 285, 'done', '2025-03-20 16:06:16', 29, 'Member', 1),
(318, 283, 'done', '2025-03-20 16:06:42', 29, 'Member', 1),
(319, 281, 'done', '2025-03-20 16:07:23', 29, 'Member', 1),
(320, 288, 'done', '2025-03-20 16:08:13', 29, 'Member', 1),
(321, 256, 'done', '2025-03-20 16:08:33', 29, 'Member', 1),
(322, 287, 'Yep, but personally, I think would make the website way more awesome.  Unfortunately, your ideal client is likely not on the same page as I am.  ', '2025-03-20 18:00:28', 3, 'Admin', 1),
(323, 289, 'I\'ve put these in.  My business one has a more friendly name for people to see when they hover over the link - go back in your settings and see if you can get burdentoblessings or something like that instead on both of them.  I\'ll change it to the friendly links whenever you want.', '2025-03-20 18:13:40', 3, 'Admin', 1),
(324, 288, 'I reopened this, since I wasn\'t done.  :-)  \r\nI edited JD\'s new photo, since it wasn\'t square and made an egg shape instead of a circle like the others.  I\'m sure he didn\'t MEAN to be the center of attention... :-)  I didn\'t use the Mission statement because it was back to being too fancy - and to be first on the page might bounce them - you have said all the same things further down below, in clearer language.  I broke up the goals paragraph into bites that made sense, replaced my favorite photo of you I\'ve seen with an Indiana State flag - see if you like it and let me know.  There are no other changes I have in mind for this page, let me know what you think and if you don\'t approve of it - just let me know.  It\'s just a matter of copy/paste at this point - so unless you are off the rails with something, it shouldn\'t be a big deal to knock out. ', '2025-03-20 20:22:11', 3, 'Admin', 1),
(325, 289, 'If you would like your instagram feed to be on your website, here\'s how:\r\n1. Signup for a FREE Behold account at https://app.behold.so/\r\n2. Connect your Business Instagram.\r\n3. Add a \"widget feed\"\r\n4. Customize the widget, add mobile breakpoints (I can help with that)\r\n5. Copy your embed code and submit it to me. \r\n\r\nI have not seen the widget, but I am pretty confident that the embed code will have the breakpoints, if you add some, and I can likely tweak it from there without having to access your username or password. \r\n\r\nThis isn\'t just a \"nice to have\" it can increase user engagement to your site (keep them longer) and enhances seo in the process.  It might be a good stop gap between when you have enough content & I write you an application for a blog, or you have me integrate a subscription one - or you can avoid duplication of content and only have to update one social media instead of more.  Totally up to you - but a thought nonetheless.  Always trying to save money, and free is good.  :-)', '2025-03-20 20:41:18', 3, 'Admin', 1),
(326, 288, 'Resolving this ticket, to test an edit to the ticketing system.  If you have any requests, please reopen the ticket.', '2025-03-21 17:14:26', 3, 'Admin', 1),
(327, 289, 'I\'ll just leave them as-is.  :-)', '2025-04-29 13:02:09', 3, 'Admin', 1),
(328, 288, 'Closed.  :-)', '2025-04-29 13:03:18', 3, 'Admin', 1),
(329, 290, 'I\'m working on the blog to get everything except the comments done today.  I will be working on the comments later, so this isn\'t necessary for you to do today.  ', '2025-04-29 14:25:47', 3, 'Admin', 1),
(330, 291, 'If you want to see what it looks like so far:\r\nhttps://burden-to-blessings.com/blog\r\nThe backend part is available to look at in your admin center.  (Don\'t update settings in there until you know what it does.) :-)', '2025-04-29 14:27:59', 3, 'Admin', 1),
(331, 291, 'I added a coming in Fall of this year to the comments area.  Once you have content, I\'ll change it to where if they are subscribed to the newsletter, they can comment on it.  That gives you the ability to unsubscribe people if they comment stupid stuff (because some people do.)  Every email that is sent with the current blog post you create has an unsubscribe feature in the email itself.  This is actually a legal requirement, but not everyone does it.  Just so you know, it brings them straight to your blog and they see a note that they were successfully unsubscribed.  They are free to subscribe again.  I didn\'t code anything to block someone from resubscribing with the same email you deleted previously - but down the road if it becomes an issue, I can certainly do that.\r\nLet me know about a week before you want comments to be enabled, and I\'ll code where it allows subscribers to comment.  It shouldn\'t take the full week, but just in case something comes up - it absolutely can be done in a week.  ', '2025-05-20 19:20:52', 3, 'Admin', 1),
(332, 292, 'I included it on the contact form. \r\nCurrently, this contact form serves multiple functions.  \r\n1. It creates a record in messages table with all the information from the form.\r\n2. It creates or updates a record in text_ok table with the phone number (primary key), email, whether or not texts are approved, when it was first created and what the last update date is.\r\n3. Creates or updates lead table with firstname, last name, phone, email, if texts are approved, the status is set to lead, and sets the updated date.  (There are other columns updatable via Admin area.)\r\n\r\nI included the information from the link you provided, in the form, and made it so you have to \"intentionally\" opt in or out, rather than defaulting to no, and it\'s at the bottom of the required information - so they have to see it to opt-in.\r\n\r\nPlease look over spelling and word usage, and verify it has what you need.  xoxo', '2025-05-22 18:43:12', 3, 'Admin', 1),
(333, 291, 'The subscribe to the blog is working.  Creating blogs in the Admin area is working.  Sidebar widget it working.  Header widget is working.  It\'s all ready to edit or delete the widgets and current posts so you can put your own content there.', '2025-05-22 18:48:44', 3, 'Admin', 1),
(334, 292, 'This was completed two weeks ago, so I am resolving this ticket. Open it back up if you need anything further. ', '2025-06-05 06:21:08', 3, 'Admin', 1),
(335, 290, 'This has been over a month. I am resolving this ticket to declutter my open work. When/If you would like to track things in you google workspace for SEO or other purposes, you can reopen the ticket & submit the code for me to add. I might code a way to add it via blog settings to add yourself at your convenience (if I feel froggy.)  :-)', '2025-06-05 06:24:54', 3, 'Admin', 1),
(336, 291, 'I removed commenting, altogether. The security is an issue if it isn\'t monitored aggressively & there are plenty of more SEO friendly options. I coded dynamic basic seo, so your titles will be added as keywords to blog posts. I\'m resolving this, but will continue to update the code behind & refresh dates. I\'m removing the widgets I added, since they say things like \"Libby, you can change this title, etc.\" When you\'re ready to add content, you can edit it in the Admin center under B2B-Blog. I\'ll delete my temporary blog posts & just leave a nice place holder & sign up so it\'ll be viewable to folks, let them know it\'s coming, and let them sign up- but resolved here. You will be able to do any edits via the Admin area. Reopen this ticket if you see any spelling errors or issues. ', '2025-06-05 06:35:34', 3, 'Admin', 1),
(337, 293, 'Opening ticket. ', '2025-06-05 06:47:07', 3, 'Admin', 1),
(338, 293, 'I think a download link works perfectly for now. At some point it might be good to add in a pop up where they can submit their email for a freebie, but I\'m just not quite there yet :)', '2025-06-06 09:26:09', 29, 'Member', 1),
(339, 292, 'Hey Barb, I looked over the changes made for SMS opt in, and I am pretty confident that the drop down menu will still trigger us to get denied for SMS campaign approval. I am sending you a link to a company I have worked with that has a good opt in and out message on their website with a check box. While I think ours is better and more thorough, I know the carriers are looking for a few key things. Would you check it out and let me know what you think? I want to be really confident that we have this right before we resubmit for A2P compliance, because if we mess it up, we cannot apply again for another 45 days :/ Also, sorry for the very delayed response!', '2025-06-06 09:31:17', 29, 'Member', 1),
(340, 290, 'sounds good!', '2025-06-06 09:32:27', 29, 'Member', 1),
(341, 291, 'Thank you!', '2025-06-06 09:32:51', 29, 'Member', 1),
(342, 292, 'Can you put the link here?  I\'ll look at it today.  :-)', '2025-06-06 11:41:03', 3, 'Admin', 1),
(343, 292, 'This is done.  I redid the backend to accept the input of a checkbox and still get the same values I needed for your Admin area for the messages/leads/text-ok. Made the behind the scenes changes, and will be working on the in-admin\'s-face changes for the next week.  We\'ll need a walk through, likely, but everything is functional and working to this point.', '2025-06-08 12:23:59', 3, 'Admin', 1),
(344, 293, 'Okay, I created an application in the Burden to Blessings Blog area of your Admin.  \r\nI restructured the Admin to have different dashboards for each domain - to set it up for the others whenever you are ready.\r\nOn Burden to Blessings Admin Menu, go to Manage Blog.\r\nHere you will see +Documents and Active Documents.  The Active Documents has a number after it - that\'s how many active documents you have.  If you click it, you\'ll see all of your documents listed, and if they are Active or Inactive.\r\nIf you click +Document you will be able to add documents.  Make sure you name them something the user will understand, not just name it in the form.  It will download to them whatever the name is that the document is actually named, not the name you put in the form.  The name you put in the form is for your reference only.  Suggest you name it the same as the file name, and keep it easy for your clients.\r\nALSO keep in mind your keywords.  If your document is 100 things for the client to do - don\'t name it 100-things-to -do, name it something like checklist-to-avoid-forclosure.pdf and when you upload it, your form name can be something like \'Forclosure Checklist - avoid-100\" to distinguish it from another document that may be Forclosure Checklist what\'s next, for example.  This will set you up nicely for the other feature I added... ', '2025-06-10 12:23:37', 3, 'Admin', 1),
(345, 293, 'Part 2:\r\nWhen you go to add a blog post, ( the +Post  button) you will be able to add your post, but right above where you are designing it, I made a dropdown that shows all of the active document names (the name you put on the form) and when you select the one you want, the link displays.  Just copy and paste it into your post - if you want it to be displayed.  Otherwise, click the link formatting thing and it will let you include the link, but can say \"Click to Download Document\" or just the name of the document - whatever feels right for the post.  It\'s ready when you are.  ', '2025-06-10 12:28:21', 3, 'Admin', 1),
(346, 294, 'I hope it helps!', '2025-06-10 12:40:00', 3, 'Admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tickets_uploads`
--

CREATE TABLE `tickets_uploads` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `filepath` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets_uploads`
--

INSERT INTO `tickets_uploads` (`id`, `ticket_id`, `filepath`) VALUES
(21, 102, 'ticket-uploads/53a5d9b2f280cf452ab3124010b845306f019114.png'),
(22, 103, 'ticket-uploads/31b1592dc01eac3579f91a18a05b48dcd92f2b9a.png'),
(23, 104, 'ticket-uploads/c46f06036c20f494a090ab91034fc5900f48218f.png'),
(39, 143, 'ticket-uploads/530b6136e155119af8009ae505548a075756ddc5.png'),
(40, 144, 'ticket-uploads/d44edcd4bb59b83257aeb8598086e1a425d4f9a9.png'),
(44, 151, 'ticket-uploads/0f5ed2006421a2f9454225507fd0a1bbf356e14f.png'),
(45, 151, 'ticket-uploads/bd0c80c8c2d97a2add156ba0d8b12c389dcb6b48.png'),
(46, 151, 'ticket-uploads/ed6642057ea42dd433ae2919407fa0b21cadc99d.png'),
(47, 151, 'ticket-uploads/c5f360d3a396847990481a6f7b8006c72bb10640.png'),
(48, 153, 'ticket-uploads/727b52a0bf2385a7d9fcd8f2df5c3df7322795ee.png'),
(49, 153, 'ticket-uploads/ae364c065de5b42cd15fc7df4a8caeace97d9fbd.png'),
(50, 153, 'ticket-uploads/a4347db71d3a884edea6b442bfb7f945df102972.png'),
(51, 153, 'ticket-uploads/273463daaaa0e8db7288a0f81de93fcb7b1853bf.png'),
(52, 153, 'ticket-uploads/4c9f3a5ef33a455439ba50e75bd9f66e3f6b767a.png'),
(53, 153, 'ticket-uploads/c016a094d547a06128488477f958c387643e3717.png'),
(54, 154, 'ticket-uploads/fd8d90bb97bd933941eb1a21a54a649f119ae071.png'),
(55, 154, 'ticket-uploads/07287c32ac0d52e9316b434efd0b98888afcd7a6.png'),
(56, 154, 'ticket-uploads/8e54316ab5bda3552af935be84816bb51c6f5847.png'),
(57, 154, 'ticket-uploads/5c2d892ac8b4cf5b4c57dcf5217601b273afac70.png'),
(58, 154, 'ticket-uploads/e2b3fe6192b5caee80cb39e1fa122b4ea9b5f827.png'),
(59, 154, 'ticket-uploads/5eb46b65080e06d3e77a57deff03332da3c3cd1a.png'),
(60, 155, 'ticket-uploads/960d98471ef32a9dcc330e5c58409c7b16f685a8.png'),
(61, 155, 'ticket-uploads/d41d09916aace2ee0c4bd2389a80d9d5072311f4.png'),
(62, 155, 'ticket-uploads/68fdbf66351b7b89c7581baf6d282ad44350e324.png'),
(63, 155, 'ticket-uploads/d3c2f4ee37a37a8aa6adb46ada1d516e7dfd602b.png'),
(64, 155, 'ticket-uploads/faf1d4eb5a1ca2b36ff6e72c2938c3cb2dd7fb9c.png'),
(65, 156, 'ticket-uploads/ce9e92619ea5fe6ed32fb292d018cd109d5d77f7.png'),
(66, 156, 'ticket-uploads/459433e4721891975a63b2f2dfaf2b0662eb4c25.png'),
(67, 156, 'ticket-uploads/3e1de23460cbb6d73a0e9658caf9e35e9bda4996.png'),
(68, 3, '/home/glitchwizarddigi/public_html//client-dashboard//communication/ticket-uploads/d318911dce7178544c1e30594e5c43116c2a4a8c.jpg'),
(69, 29, '/home/glitchwizarddigi/public_html//client-dashboard//communication/ticket-uploads/9ecc430ef7da9da8be797fb071f3b14c29c4b82c.png'),
(70, 29, '/home/glitchwizarddigi/public_html//client-dashboard//communication/ticket-uploads/c01cae8e19e1185cf957bb2ee39f3eba9a764e46.jpg'),
(71, 29, '/home/glitchwizarddigi/public_html//client-dashboard//communication/ticket-uploads/f66714d88e95b77bf18eca67692a534e367936d4.jpg'),
(72, 29, '/home/glitchwizarddigi/public_html//client-dashboard//communication/ticket-uploads/46bbb59729a261c2d1323ad6b4a0aa9f11c76203.png'),
(73, 197, '/home/glitchwizarddigi/public_html//client-dashboard//communication/ticket-uploads/735ffc2935e6a9a35697696727cec1861dac475f.png'),
(74, 197, '/home/glitchwizarddigi/public_html//client-dashboard//communication/ticket-uploads/9f9479426090bdd541827719bc49294ef766b6e3.jpg'),
(75, 198, 'ticket-uploads/0608f7427184597b7997d085fbc5e1675bf14098.png'),
(76, 199, 'ticket-uploads/34245398f8a8a2b8054f831aa964fcc41e182180.png'),
(89, 281, 'ticket-uploads/14db19799fd7123fedd003ec17b7c385a2826d6c.jpg'),
(90, 282, 'ticket-uploads/df440442ed1946ea7332e60e8fadc4d8d2e44757.jpg'),
(91, 282, 'ticket-uploads/9500b22f4fff994d5ad5ab520ee645c18787e122.jpg'),
(92, 282, 'ticket-uploads/46844a1e15610b1785e564509120182127d9a221.png'),
(93, 282, 'ticket-uploads/1484ac9f2e105ef16cddfd445dd6546664ab9b1c.png'),
(94, 282, 'ticket-uploads/afaaf345110d7b6e74fc2d0b9dcad340b5932279.png'),
(95, 284, 'ticket-uploads/6c30029ddb69e72b1355adc4112752b214b39c24.png'),
(96, 286, 'ticket-uploads/4443c622307b7d9a16d15118df3ef6325536907f.png'),
(97, 287, 'ticket-uploads/6e9b041fd3259727525dfb3ca08d782897bfdbf0.jpg');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_active_content`
-- (See below for the actual view)
--
CREATE TABLE `view_active_content` (
`content_type` varchar(7)
,`content_key` varchar(100)
,`title` varchar(255)
,`description` mediumtext
,`icon` varchar(255)
,`display_order` int(11)
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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_page_details`
--
ALTER TABLE `event_page_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_unavailable_dates`
--
ALTER TABLE `event_unavailable_dates`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `setting_blog_config`
--
ALTER TABLE `setting_blog_config`
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
-- Indexes for table `setting_business_identity`
--
ALTER TABLE `setting_business_identity`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_setting_business_identity_names` (`business_name_short`,`business_name_medium`);

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
-- Indexes for table `setting_content_services`
--
ALTER TABLE `setting_content_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `service_key` (`service_key`),
  ADD KEY `idx_category` (`service_category`),
  ADD KEY `idx_order` (`service_order`),
  ADD KEY `idx_setting_content_services_category_order` (`service_category`,`service_order`,`is_active`);

--
-- Indexes for table `setting_content_testimonials`
--
ALTER TABLE `setting_content_testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_order` (`display_order`);

--
-- Indexes for table `setting_email_config`
--
ALTER TABLE `setting_email_config`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blog_comments`
--
ALTER TABLE `blog_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `blog_files`
--
ALTER TABLE `blog_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blog_gallery`
--
ALTER TABLE `blog_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `blog_gallery_categories`
--
ALTER TABLE `blog_gallery_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blog_gallery_tags`
--
ALTER TABLE `blog_gallery_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blog_menu`
--
ALTER TABLE `blog_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10000000;

--
-- AUTO_INCREMENT for table `blog_messages`
--
ALTER TABLE `blog_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blog_newsletter`
--
ALTER TABLE `blog_newsletter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blog_pages`
--
ALTER TABLE `blog_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `blog_tags`
--
ALTER TABLE `blog_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blog_users`
--
ALTER TABLE `blog_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `blog_widgets`
--
ALTER TABLE `blog_widgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `campaign_clicks`
--
ALTER TABLE `campaign_clicks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campaign_items`
--
ALTER TABLE `campaign_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `custom_placeholders`
--
ALTER TABLE `custom_placeholders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `event_page_details`
--
ALTER TABLE `event_page_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_unavailable_dates`
--
ALTER TABLE `event_unavailable_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery_collections`
--
ALTER TABLE `gallery_collections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `group_subscribers`
--
ALTER TABLE `group_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `invoice_clients`
--
ALTER TABLE `invoice_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT for table `newsletters`
--
ALTER TABLE `newsletters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `page_completion_status`
--
ALTER TABLE `page_completion_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2763;

--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `polls_categories`
--
ALTER TABLE `polls_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `poll_answers`
--
ALTER TABLE `poll_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `poll_categories`
--
ALTER TABLE `poll_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `poll_votes`
--
ALTER TABLE `poll_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `review_filters`
--
ALTER TABLE `review_filters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `review_images`
--
ALTER TABLE `review_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review_page_details`
--
ALTER TABLE `review_page_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `scope`
--
ALTER TABLE `scope`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- AUTO_INCREMENT for table `setting_blog_config`
--
ALTER TABLE `setting_blog_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_branding_assets`
--
ALTER TABLE `setting_branding_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_branding_colors`
--
ALTER TABLE `setting_branding_colors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_branding_fonts`
--
ALTER TABLE `setting_branding_fonts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_branding_templates`
--
ALTER TABLE `setting_branding_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `setting_business_identity`
--
ALTER TABLE `setting_business_identity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `setting_contact_config`
--
ALTER TABLE `setting_contact_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting_contact_info`
--
ALTER TABLE `setting_contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `setting_content_services`
--
ALTER TABLE `setting_content_services`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shop_products`
--
ALTER TABLE `shop_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shop_product_categories`
--
ALTER TABLE `shop_product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shop_product_category`
--
ALTER TABLE `shop_product_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `shop_product_downloads`
--
ALTER TABLE `shop_product_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shop_product_media`
--
ALTER TABLE `shop_product_media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `shop_product_media_map`
--
ALTER TABLE `shop_product_media_map`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `shop_product_options`
--
ALTER TABLE `shop_product_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `shop_shipping`
--
ALTER TABLE `shop_shipping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shop_taxes`
--
ALTER TABLE `shop_taxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=295;

--
-- AUTO_INCREMENT for table `tickets_categories`
--
ALTER TABLE `tickets_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tickets_comments`
--
ALTER TABLE `tickets_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=347;

--
-- AUTO_INCREMENT for table `tickets_uploads`
--
ALTER TABLE `tickets_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

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
-- Constraints for table `client_signatures`
--
ALTER TABLE `client_signatures`
  ADD CONSTRAINT `client_signatures_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`scope_id`) REFERENCES `scope` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
