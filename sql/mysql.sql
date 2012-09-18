--------------------------------------------------------------------------
-- Moksha DB script
--------------------------------------------------------------------------

-------------------------
-- Central admin tables
-------------------------

-- Replace Admin_DB with the name of your Moksha admin DB
USE Admin_DB;

-- Sites table
CREATE TABLE `sites` (
  `site_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `site_url` varchar(255) CHARACTER SET latin1 NOT NULL,
  `site_slug` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
