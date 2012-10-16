DROP TABLE IF EXISTS ezxgis_position;
CREATE TABLE  `ezxgis_position` (
  `contentobject_attribute_id` int(11) NOT NULL default '0',
  `contentobject_attribute_version` int(11) NOT NULL default '0',
  `latitude` float NOT NULL default '0',
  `longitude` float NOT NULL default '0',
  `street` varchar(255) default NULL,
  `zip` varchar(20) default NULL,
  `district` varchar(255) default NULL,
  `city` varchar(255) default NULL,
  `state` varchar(255) default NULL,
  `country` varchar(255) default NULL
)  ENGINE=InnoDB DEFAULT CHARSET=utf8;

ADD PRIMARY KEY (`contentobject_attribute_id`, `contentobject_attribute_version`) ;
