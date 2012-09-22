--------------------------------------------------------------------------
-- Moksha DB script
--------------------------------------------------------------------------

-------------------------
-- Central admin tables
-------------------------

-- Replace Central_DB with the name of your Moksha central admin DB
USE Central_DB;

-- Session table
CREATE TABLE `sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Sites table
CREATE TABLE `sites` (
  `site_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_url` varchar(255) NOT NULL,
  `site_slug` varchar(255) NOT NULL,
  PRIMARY KEY (`site_id`),
  UNIQUE KEY `site_url_idx` (`site_url`),
  UNIQUE KEY `site_slug_idx` (`site_slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Central users table
CREATE TABLE `users` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) NOT NULL,
  `user_password` varchar(128) NOT NULL,
  `user_email` varchar(225) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name_idx` (`user_name`),
  KEY `user_auth_idx` (`user_name`,`user_password`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;