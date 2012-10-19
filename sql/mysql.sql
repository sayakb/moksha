--------------------------------------------------------------------------
-- Moksha DB script
--------------------------------------------------------------------------

-------------------------
-- Central admin tables
-------------------------

-- Replace Central_DB with the name of your Moksha central admin DB
USE Central_DB;

-- Session table
CREATE TABLE `central_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `sessions_last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Sites table
CREATE TABLE `central_sites` (
  `site_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_url` varchar(255) NOT NULL,
  PRIMARY KEY (`site_id`),
  UNIQUE KEY `sites_url_idx` (`site_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Central users table
CREATE TABLE `central_users` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email_address` varchar(225) NOT NULL,
  `founder` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `users_name_idx` (`user_name`),
  KEY `users_auth_idx` (`user_name`,`password`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Insert admin user (username: admin / password: admin)
INSERT INTO `central_users` (
  `user_name`, 
  `password`, 
  `email_address`, 
  `founder`
)
VALUES (
  'admin', 
  '5d32bfc39d0b8dd2d5d48595aa086f58e0f57dcbf6b108ba8f7873b4ae85e85d72beaa9a736cbe17600bad19f26172b0b97a0c1f165c4d06f4974809eda69e27', 
  'admin@webmaster.local', 
  1
);