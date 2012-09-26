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
  KEY `sessions_last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Sites table
CREATE TABLE `sites` (
  `site_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_url` varchar(255) NOT NULL,
  PRIMARY KEY (`site_id`),
  UNIQUE KEY `sites_url_idx` (`site_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Insert default site
-- Change 'localhost/moksha' to whatever applies in your case
INSERT INTO `sites` (
  `site_url`
)
VALUES (
  'localhost/moksha'
);

-- Central users table
CREATE TABLE `users` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) NOT NULL,
  `user_password` varchar(128) NOT NULL,
  `user_email` varchar(225) NOT NULL,
  `user_founder` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `users_name_idx` (`user_name`),
  KEY `users_auth_idx` (`user_name`,`user_password`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Insert admin user (username: admin / password: admin)
INSERT INTO `users` (
  `user_name`, 
  `user_password`, 
  `user_email`, 
  `user_founder`
)
VALUES (
  'admin', 
  '5d32bfc39d0b8dd2d5d48595aa086f58e0f57dcbf6b108ba8f7873b4ae85e85d72beaa9a736cbe17600bad19f26172b0b97a0c1f165c4d06f4974809eda69e27', 
  'admin@webmaster.local', 
  1
);

------------------------
-- Site related tables
------------------------

-- Replace Site_DB with the name of your Moksha site DB name
USE Site_DB;

-- Index table for hubs
CREATE TABLE `hubs_1` (
  `hub_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `hub_name` varchar(100) NOT NULL,
  `hub_driver` char(5) NOT NULL,
  `hub_source` varchar(225),
  PRIMARY KEY (`hub_id`),
  KEY `hubs_siteid_idx` (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;