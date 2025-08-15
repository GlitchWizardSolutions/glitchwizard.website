<?php
// Contact Form Settings
// Last updated: 2025-08-14 13:50:05

$contact_settings = array (
  'receiving_email' => 'admin@yoursite.com',
  'smtp_enabled' => false,
  'smtp_host' => '',
  'smtp_port' => 587,
  'smtp_username' => '',
  'smtp_password' => '',
  'smtp_encryption' => 'tls',
  'email_from_name' => 'Contact Form',
  'email_subject_prefix' => '[Contact Form]',
  'auto_reply_enabled' => true,
  'auto_reply_subject' => 'Thank you for contacting us',
  'auto_reply_message' => 'We have received your message and will respond as soon as possible.',
  'rate_limit_max' => 3,
  'rate_limit_window' => 3600,
  'min_submit_interval' => 10,
  'blocked_words' => 
  array (
    0 => 'viagra',
    1 => 'cialis',
    2 => 'loan',
    3 => 'casino',
    4 => 'poker',
    5 => 'bitcoin',
    6 => 'crypto',
    7 => 'make money',
    8 => 'work from home',
    9 => 'business opportunity',
    10 => 'free money',
    11 => 'click here',
    12 => 'limited time',
    13 => 'act now',
    14 => 'congratulations',
  ),
  'max_links' => 2,
  'enable_logging' => true,
);
