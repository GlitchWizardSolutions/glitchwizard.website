
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
