CREATE TABLE `jobs` (
	  `job_id` varchar(20) collate utf8_unicode_ci NOT NULL,
	  `url_id` int(11) NOT NULL,
	  `start_time` datetime NOT NULL,
	  `end_time` datetime default NULL,
	  PRIMARY KEY  (`job_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `urls` (
	  `url_id` int(11) NOT NULL auto_increment,
	  `url` varchar(500) collate utf8_unicode_ci NOT NULL,
	  PRIMARY KEY  (`url_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

