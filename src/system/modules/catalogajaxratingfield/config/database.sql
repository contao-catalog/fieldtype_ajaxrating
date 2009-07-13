-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


-- --------------------------------------------------------

--
-- Table `tl_catalog_rating`
-- 

CREATE TABLE `tl_catalog_rating` (
-- id for this vote
  `id` int(10) unsigned NOT NULL auto_increment,
-- id of the catalog
  `cat_id` int(10) unsigned NOT NULL default '0',
-- id of the item in the catalog 
  `item_id` int(10) unsigned NOT NULL default '0',
-- value of this vote
  `value` int(10) unsigned NOT NULL default '0',
-- ip where this vote originated from
  `ip` varchar(255) NOT NULL default '',
-- time when this vote was casted
  `time` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;