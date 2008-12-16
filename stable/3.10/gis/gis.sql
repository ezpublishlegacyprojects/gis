DROP TABLE IF EXISTS `ezgis_position`;
CREATE TABLE  `ezgis_position` (
  `contentobject_attribute_id` int(11) NOT NULL default '0',
  `contentobject_attribute_version` int(11) NOT NULL default '0',
  `latitude` float NOT NULL default '0',
  `longitude` float NOT NULL default '0',
  `street` varchar(255) default NULL,
  `zip` varchar(20) default NULL,
  `city` varchar(255) default NULL,
  `state` varchar(255) default NULL,
  `country` varchar(255) default NULL
);