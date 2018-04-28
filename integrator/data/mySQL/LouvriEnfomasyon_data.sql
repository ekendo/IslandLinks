-- phpMyAdmin SQL Dump
-- version 2.8.0.1
-- http://www.phpmyadmin.net
-- 
-- Host: server-mysql
-- Generation Time: May 15, 2016 at 10:48 PM
-- Server version: 5.5.43
-- PHP Version: 4.4.9
-- 
-- Database: `louvrienfomasyon_data`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `Answer`
-- 

DROP TABLE IF EXISTS `Answer`;
CREATE TABLE IF NOT EXISTS `Answer` (
  `Count` int(11) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `language_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `example_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Answers`
-- 

DROP TABLE IF EXISTS `Answers`;
CREATE TABLE IF NOT EXISTS `Answers` (
  `Answer` varchar(250) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `answer_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Comments`
-- 

DROP TABLE IF EXISTS `Comments`;
CREATE TABLE IF NOT EXISTS `Comments` (
  `Comment` text NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `definition_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Definitions`
-- 

DROP TABLE IF EXISTS `Definitions`;
CREATE TABLE IF NOT EXISTS `Definitions` (
  `Definition` varchar(500) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `definition_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Elements`
-- 

DROP TABLE IF EXISTS `Elements`;
CREATE TABLE IF NOT EXISTS `Elements` (
  `Element` varchar(150) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `element_id` int(11) NOT NULL,
  `element_type` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Examples`
-- 

DROP TABLE IF EXISTS `Examples`;
CREATE TABLE IF NOT EXISTS `Examples` (
  `Example` varchar(550) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `example_id` int(11) NOT NULL,
  `element_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Grammer`
-- 

DROP TABLE IF EXISTS `Grammer`;
CREATE TABLE IF NOT EXISTS `Grammer` (
  `Grammer` varchar(150) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `grammer_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Krawler`
-- 

DROP TABLE IF EXISTS `Krawler`;
CREATE TABLE IF NOT EXISTS `Krawler` (
  `id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Krawlers`
-- 

DROP TABLE IF EXISTS `Krawlers`;
CREATE TABLE IF NOT EXISTS `Krawlers` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Languages`
-- 

DROP TABLE IF EXISTS `Languages`;
CREATE TABLE IF NOT EXISTS `Languages` (
  `Langage` varchar(150) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `tag4` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `language_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Links`
-- 

DROP TABLE IF EXISTS `Links`;
CREATE TABLE IF NOT EXISTS `Links` (
  `Link` varchar(250) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `link_id` int(11) NOT NULL,
  `type` varchar(3) NOT NULL,
  `functional` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Locations`
-- 

DROP TABLE IF EXISTS `Locations`;
CREATE TABLE IF NOT EXISTS `Locations` (
  `Location` varchar(150) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `Lat` double NOT NULL,
  `Long` double NOT NULL,
  `CoordSys` varchar(150) NOT NULL,
  `location_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Matches`
-- 

DROP TABLE IF EXISTS `Matches`;
CREATE TABLE IF NOT EXISTS `Matches` (
  `Match` tinyint(4) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `match_id` int(11) NOT NULL,
  `Type` varchar(150) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Phrases`
-- 

DROP TABLE IF EXISTS `Phrases`;
CREATE TABLE IF NOT EXISTS `Phrases` (
  `Phrase` varchar(250) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `phrase_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Problems`
-- 

DROP TABLE IF EXISTS `Problems`;
CREATE TABLE IF NOT EXISTS `Problems` (
  `Problem` varchar(250) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `problem_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Profiles`
-- 

DROP TABLE IF EXISTS `Profiles`;
CREATE TABLE IF NOT EXISTS `Profiles` (
  `Profile` varchar(250) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `AvatarLocation` varchar(150) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Questions`
-- 

DROP TABLE IF EXISTS `Questions`;
CREATE TABLE IF NOT EXISTS `Questions` (
  `Question` varchar(250) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `Language` varchar(150) NOT NULL,
  `match_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Solutions`
-- 

DROP TABLE IF EXISTS `Solutions`;
CREATE TABLE IF NOT EXISTS `Solutions` (
  `Solution` varchar(250) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `solution_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Subjects`
-- 

DROP TABLE IF EXISTS `Subjects`;
CREATE TABLE IF NOT EXISTS `Subjects` (
  `Subject` varchar(100) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `Language` varchar(150) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Tags`
-- 

DROP TABLE IF EXISTS `Tags`;
CREATE TABLE IF NOT EXISTS `Tags` (
  `Tag` varchar(75) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Tracker`
-- 

DROP TABLE IF EXISTS `Tracker`;
CREATE TABLE IF NOT EXISTS `Tracker` (
  `Id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `tracker_type` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Trackers`
-- 

DROP TABLE IF EXISTS `Trackers`;
CREATE TABLE IF NOT EXISTS `Trackers` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `Words`
-- 

DROP TABLE IF EXISTS `Words`;
CREATE TABLE IF NOT EXISTS `Words` (
  `Word` varchar(150) NOT NULL,
  `tag1` varchar(75) NOT NULL,
  `tag2` varchar(75) NOT NULL,
  `tag3` varchar(75) NOT NULL,
  `Language` varchar(150) NOT NULL,
  `word_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_action_fact`
-- 

DROP TABLE IF EXISTS `owa_action_fact`;
CREATE TABLE IF NOT EXISTS `owa_action_fact` (
  `id` bigint(20) NOT NULL,
  `visitor_id` bigint(20) DEFAULT NULL,
  `session_id` bigint(20) DEFAULT NULL,
  `site_id` varchar(255) DEFAULT NULL,
  `referer_id` bigint(20) DEFAULT NULL,
  `ua_id` bigint(20) DEFAULT NULL,
  `host_id` bigint(20) DEFAULT NULL,
  `os_id` bigint(20) DEFAULT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `referring_search_term_id` bigint(20) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `yyyymmdd` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` tinyint(2) DEFAULT NULL,
  `dayofweek` varchar(10) DEFAULT NULL,
  `dayofyear` int(11) DEFAULT NULL,
  `weekofyear` int(11) DEFAULT NULL,
  `last_req` bigint(20) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `is_new_visitor` tinyint(1) DEFAULT NULL,
  `is_repeat_visitor` tinyint(1) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `days_since_prior_session` int(11) DEFAULT NULL,
  `days_since_first_session` int(11) DEFAULT NULL,
  `num_prior_sessions` int(11) DEFAULT NULL,
  `medium` varchar(255) DEFAULT NULL,
  `source_id` bigint(20) DEFAULT NULL,
  `ad_id` bigint(20) DEFAULT NULL,
  `campaign_id` bigint(20) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `cv1_name` varchar(255) DEFAULT NULL,
  `cv1_value` varchar(255) DEFAULT NULL,
  `cv2_name` varchar(255) DEFAULT NULL,
  `cv2_value` varchar(255) DEFAULT NULL,
  `cv3_name` varchar(255) DEFAULT NULL,
  `cv3_value` varchar(255) DEFAULT NULL,
  `cv4_name` varchar(255) DEFAULT NULL,
  `cv4_value` varchar(255) DEFAULT NULL,
  `cv5_name` varchar(255) DEFAULT NULL,
  `cv5_value` varchar(255) DEFAULT NULL,
  `document_id` bigint(20) DEFAULT NULL,
  `action_name` varchar(255) DEFAULT NULL,
  `action_label` varchar(255) DEFAULT NULL,
  `action_group` varchar(255) DEFAULT NULL,
  `numeric_value` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `site_id` (`site_id`),
  KEY `yyyymmdd` (`yyyymmdd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_ad_dim`
-- 

DROP TABLE IF EXISTS `owa_ad_dim`;
CREATE TABLE IF NOT EXISTS `owa_ad_dim` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_campaign_dim`
-- 

DROP TABLE IF EXISTS `owa_campaign_dim`;
CREATE TABLE IF NOT EXISTS `owa_campaign_dim` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_click`
-- 

DROP TABLE IF EXISTS `owa_click`;
CREATE TABLE IF NOT EXISTS `owa_click` (
  `id` bigint(20) NOT NULL,
  `visitor_id` bigint(20) DEFAULT NULL,
  `session_id` bigint(20) DEFAULT NULL,
  `site_id` varchar(255) DEFAULT NULL,
  `referer_id` bigint(20) DEFAULT NULL,
  `ua_id` bigint(20) DEFAULT NULL,
  `host_id` bigint(20) DEFAULT NULL,
  `os_id` bigint(20) DEFAULT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `referring_search_term_id` bigint(20) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `yyyymmdd` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` tinyint(2) DEFAULT NULL,
  `dayofweek` varchar(10) DEFAULT NULL,
  `dayofyear` int(11) DEFAULT NULL,
  `weekofyear` int(11) DEFAULT NULL,
  `last_req` bigint(20) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `is_new_visitor` tinyint(1) DEFAULT NULL,
  `is_repeat_visitor` tinyint(1) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `days_since_prior_session` int(11) DEFAULT NULL,
  `days_since_first_session` int(11) DEFAULT NULL,
  `num_prior_sessions` int(11) DEFAULT NULL,
  `medium` varchar(255) DEFAULT NULL,
  `source_id` bigint(20) DEFAULT NULL,
  `ad_id` bigint(20) DEFAULT NULL,
  `campaign_id` bigint(20) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `cv1_name` varchar(255) DEFAULT NULL,
  `cv1_value` varchar(255) DEFAULT NULL,
  `cv2_name` varchar(255) DEFAULT NULL,
  `cv2_value` varchar(255) DEFAULT NULL,
  `cv3_name` varchar(255) DEFAULT NULL,
  `cv3_value` varchar(255) DEFAULT NULL,
  `cv4_name` varchar(255) DEFAULT NULL,
  `cv4_value` varchar(255) DEFAULT NULL,
  `cv5_name` varchar(255) DEFAULT NULL,
  `cv5_value` varchar(255) DEFAULT NULL,
  `last_impression_id` bigint(20) DEFAULT NULL,
  `document_id` bigint(20) DEFAULT NULL,
  `target_id` bigint(20) DEFAULT NULL,
  `target_url` varchar(255) DEFAULT NULL,
  `hour` tinyint(2) DEFAULT NULL,
  `minute` tinyint(2) DEFAULT NULL,
  `second` int(11) DEFAULT NULL,
  `msec` varchar(255) DEFAULT NULL,
  `click_x` int(11) DEFAULT NULL,
  `click_y` int(11) DEFAULT NULL,
  `page_width` int(11) DEFAULT NULL,
  `page_height` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `approx_position` bigint(20) DEFAULT NULL,
  `dom_element_x` int(11) DEFAULT NULL,
  `dom_element_y` int(11) DEFAULT NULL,
  `dom_element_name` varchar(255) DEFAULT NULL,
  `dom_element_id` varchar(255) DEFAULT NULL,
  `dom_element_value` varchar(255) DEFAULT NULL,
  `dom_element_tag` varchar(255) DEFAULT NULL,
  `dom_element_text` varchar(255) DEFAULT NULL,
  `dom_element_class` varchar(255) DEFAULT NULL,
  `dom_element_parent_id` varchar(255) DEFAULT NULL,
  `tag_id` bigint(20) DEFAULT NULL,
  `placement_id` bigint(20) DEFAULT NULL,
  `ad_group_id` bigint(20) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `site_id` (`site_id`),
  KEY `yyyymmdd` (`yyyymmdd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_commerce_line_item_fact`
-- 

DROP TABLE IF EXISTS `owa_commerce_line_item_fact`;
CREATE TABLE IF NOT EXISTS `owa_commerce_line_item_fact` (
  `id` bigint(20) NOT NULL,
  `visitor_id` bigint(20) DEFAULT NULL,
  `session_id` bigint(20) DEFAULT NULL,
  `site_id` varchar(255) DEFAULT NULL,
  `referer_id` bigint(20) DEFAULT NULL,
  `ua_id` bigint(20) DEFAULT NULL,
  `host_id` bigint(20) DEFAULT NULL,
  `os_id` bigint(20) DEFAULT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `referring_search_term_id` bigint(20) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `yyyymmdd` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` tinyint(2) DEFAULT NULL,
  `dayofweek` varchar(10) DEFAULT NULL,
  `dayofyear` int(11) DEFAULT NULL,
  `weekofyear` int(11) DEFAULT NULL,
  `last_req` bigint(20) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `is_new_visitor` tinyint(1) DEFAULT NULL,
  `is_repeat_visitor` tinyint(1) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `days_since_prior_session` int(11) DEFAULT NULL,
  `days_since_first_session` int(11) DEFAULT NULL,
  `num_prior_sessions` int(11) DEFAULT NULL,
  `medium` varchar(255) DEFAULT NULL,
  `source_id` bigint(20) DEFAULT NULL,
  `ad_id` bigint(20) DEFAULT NULL,
  `campaign_id` bigint(20) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `cv1_name` varchar(255) DEFAULT NULL,
  `cv1_value` varchar(255) DEFAULT NULL,
  `cv2_name` varchar(255) DEFAULT NULL,
  `cv2_value` varchar(255) DEFAULT NULL,
  `cv3_name` varchar(255) DEFAULT NULL,
  `cv3_value` varchar(255) DEFAULT NULL,
  `cv4_name` varchar(255) DEFAULT NULL,
  `cv4_value` varchar(255) DEFAULT NULL,
  `cv5_name` varchar(255) DEFAULT NULL,
  `cv5_value` varchar(255) DEFAULT NULL,
  `document_id` bigint(20) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `unit_price` bigint(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `item_revenue` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `site_id` (`site_id`),
  KEY `yyyymmdd` (`yyyymmdd`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_commerce_transaction_fact`
-- 

DROP TABLE IF EXISTS `owa_commerce_transaction_fact`;
CREATE TABLE IF NOT EXISTS `owa_commerce_transaction_fact` (
  `id` bigint(20) NOT NULL,
  `visitor_id` bigint(20) DEFAULT NULL,
  `session_id` bigint(20) DEFAULT NULL,
  `site_id` varchar(255) DEFAULT NULL,
  `referer_id` bigint(20) DEFAULT NULL,
  `ua_id` bigint(20) DEFAULT NULL,
  `host_id` bigint(20) DEFAULT NULL,
  `os_id` bigint(20) DEFAULT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `referring_search_term_id` bigint(20) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `yyyymmdd` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` tinyint(2) DEFAULT NULL,
  `dayofweek` varchar(10) DEFAULT NULL,
  `dayofyear` int(11) DEFAULT NULL,
  `weekofyear` int(11) DEFAULT NULL,
  `last_req` bigint(20) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `is_new_visitor` tinyint(1) DEFAULT NULL,
  `is_repeat_visitor` tinyint(1) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `days_since_prior_session` int(11) DEFAULT NULL,
  `days_since_first_session` int(11) DEFAULT NULL,
  `num_prior_sessions` int(11) DEFAULT NULL,
  `medium` varchar(255) DEFAULT NULL,
  `source_id` bigint(20) DEFAULT NULL,
  `ad_id` bigint(20) DEFAULT NULL,
  `campaign_id` bigint(20) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `cv1_name` varchar(255) DEFAULT NULL,
  `cv1_value` varchar(255) DEFAULT NULL,
  `cv2_name` varchar(255) DEFAULT NULL,
  `cv2_value` varchar(255) DEFAULT NULL,
  `cv3_name` varchar(255) DEFAULT NULL,
  `cv3_value` varchar(255) DEFAULT NULL,
  `cv4_name` varchar(255) DEFAULT NULL,
  `cv4_value` varchar(255) DEFAULT NULL,
  `cv5_name` varchar(255) DEFAULT NULL,
  `cv5_value` varchar(255) DEFAULT NULL,
  `document_id` bigint(20) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `order_source` varchar(255) DEFAULT NULL,
  `gateway` varchar(255) DEFAULT NULL,
  `total_revenue` bigint(20) DEFAULT NULL,
  `tax_revenue` bigint(20) DEFAULT NULL,
  `shipping_revenue` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `site_id` (`site_id`),
  KEY `yyyymmdd` (`yyyymmdd`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_configuration`
-- 

DROP TABLE IF EXISTS `owa_configuration`;
CREATE TABLE IF NOT EXISTS `owa_configuration` (
  `id` bigint(20) NOT NULL,
  `settings` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_document`
-- 

DROP TABLE IF EXISTS `owa_document`;
CREATE TABLE IF NOT EXISTS `owa_document` (
  `id` bigint(20) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `uri` varchar(255) DEFAULT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `page_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_domstream`
-- 

DROP TABLE IF EXISTS `owa_domstream`;
CREATE TABLE IF NOT EXISTS `owa_domstream` (
  `id` bigint(20) NOT NULL,
  `visitor_id` bigint(20) DEFAULT NULL,
  `session_id` bigint(20) DEFAULT NULL,
  `site_id` varchar(255) DEFAULT NULL,
  `referer_id` bigint(20) DEFAULT NULL,
  `ua_id` bigint(20) DEFAULT NULL,
  `host_id` bigint(20) DEFAULT NULL,
  `os_id` bigint(20) DEFAULT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `referring_search_term_id` bigint(20) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `yyyymmdd` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` tinyint(2) DEFAULT NULL,
  `dayofweek` varchar(10) DEFAULT NULL,
  `dayofyear` int(11) DEFAULT NULL,
  `weekofyear` int(11) DEFAULT NULL,
  `last_req` bigint(20) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `is_new_visitor` tinyint(1) DEFAULT NULL,
  `is_repeat_visitor` tinyint(1) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `days_since_prior_session` int(11) DEFAULT NULL,
  `days_since_first_session` int(11) DEFAULT NULL,
  `num_prior_sessions` int(11) DEFAULT NULL,
  `medium` varchar(255) DEFAULT NULL,
  `source_id` bigint(20) DEFAULT NULL,
  `ad_id` bigint(20) DEFAULT NULL,
  `campaign_id` bigint(20) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `cv1_name` varchar(255) DEFAULT NULL,
  `cv1_value` varchar(255) DEFAULT NULL,
  `cv2_name` varchar(255) DEFAULT NULL,
  `cv2_value` varchar(255) DEFAULT NULL,
  `cv3_name` varchar(255) DEFAULT NULL,
  `cv3_value` varchar(255) DEFAULT NULL,
  `cv4_name` varchar(255) DEFAULT NULL,
  `cv4_value` varchar(255) DEFAULT NULL,
  `cv5_name` varchar(255) DEFAULT NULL,
  `cv5_value` varchar(255) DEFAULT NULL,
  `document_id` bigint(20) DEFAULT NULL,
  `domstream_guid` bigint(20) DEFAULT NULL,
  `events` blob,
  `duration` int(11) DEFAULT NULL,
  `page_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `site_id` (`site_id`),
  KEY `yyyymmdd` (`yyyymmdd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_exit`
-- 

DROP TABLE IF EXISTS `owa_exit`;
CREATE TABLE IF NOT EXISTS `owa_exit` (
  `id` bigint(20) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `anchortext` varchar(255) DEFAULT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_feed_request`
-- 

DROP TABLE IF EXISTS `owa_feed_request`;
CREATE TABLE IF NOT EXISTS `owa_feed_request` (
  `id` bigint(20) NOT NULL,
  `visitor_id` bigint(20) DEFAULT NULL,
  `session_id` bigint(20) DEFAULT NULL,
  `document_id` bigint(20) DEFAULT NULL,
  `site_id` varchar(255) DEFAULT NULL,
  `ua_id` varchar(255) DEFAULT NULL,
  `host_id` bigint(20) DEFAULT NULL,
  `os_id` varchar(255) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  `feed_reader_guid` varchar(255) DEFAULT NULL,
  `subscription_id` bigint(20) DEFAULT NULL,
  `timestamp` bigint(20) DEFAULT NULL,
  `yyyymmdd` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` tinyint(2) DEFAULT NULL,
  `dayofweek` varchar(10) DEFAULT NULL,
  `dayofyear` int(11) DEFAULT NULL,
  `weekofyear` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `hour` tinyint(2) DEFAULT NULL,
  `minute` tinyint(2) DEFAULT NULL,
  `second` tinyint(2) DEFAULT NULL,
  `msec` int(11) DEFAULT NULL,
  `last_req` bigint(20) DEFAULT NULL,
  `feed_format` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `yyyymmdd` (`yyyymmdd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_host`
-- 

DROP TABLE IF EXISTS `owa_host`;
CREATE TABLE IF NOT EXISTS `owa_host` (
  `id` bigint(20) NOT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  `full_host` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_impression`
-- 

DROP TABLE IF EXISTS `owa_impression`;
CREATE TABLE IF NOT EXISTS `owa_impression` (
  `id` bigint(20) NOT NULL,
  `visitor_id` bigint(20) DEFAULT NULL,
  `session_id` bigint(20) DEFAULT NULL,
  `tag_id` bigint(20) DEFAULT NULL,
  `placement_id` bigint(20) DEFAULT NULL,
  `campaign_id` bigint(20) DEFAULT NULL,
  `ad_group_id` bigint(20) DEFAULT NULL,
  `ad_id` bigint(20) DEFAULT NULL,
  `site_id` varchar(255) DEFAULT NULL,
  `last_impression_id` bigint(20) DEFAULT NULL,
  `last_impression_timestamp` bigint(20) DEFAULT NULL,
  `timestamp` bigint(20) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `dayofyear` int(11) DEFAULT NULL,
  `weekofyear` int(11) DEFAULT NULL,
  `hour` tinyint(2) DEFAULT NULL,
  `minute` tinyint(2) DEFAULT NULL,
  `msec` bigint(20) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `ua_id` bigint(20) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `host_id` varchar(255) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_location_dim`
-- 

DROP TABLE IF EXISTS `owa_location_dim`;
CREATE TABLE IF NOT EXISTS `owa_location_dim` (
  `id` bigint(20) NOT NULL,
  `country` varchar(255) DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_os`
-- 

DROP TABLE IF EXISTS `owa_os`;
CREATE TABLE IF NOT EXISTS `owa_os` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_queue_item`
-- 

DROP TABLE IF EXISTS `owa_queue_item`;
CREATE TABLE IF NOT EXISTS `owa_queue_item` (
  `id` bigint(20) NOT NULL,
  `event_type` varchar(255) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `event` blob,
  `insertion_datestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `insertion_timestamp` int(11) DEFAULT NULL,
  `handled_timestamp` int(11) DEFAULT NULL,
  `last_attempt_timestamp` int(11) DEFAULT NULL,
  `not_before_timestamp` int(11) DEFAULT NULL,
  `failed_attempt_count` int(11) DEFAULT NULL,
  `is_assigned` tinyint(1) DEFAULT NULL,
  `last_error_msg` varchar(255) DEFAULT NULL,
  `handled_by` varchar(255) DEFAULT NULL,
  `handler_duration` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_referer`
-- 

DROP TABLE IF EXISTS `owa_referer`;
CREATE TABLE IF NOT EXISTS `owa_referer` (
  `id` bigint(20) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `query_terms` varchar(255) DEFAULT NULL,
  `refering_anchortext` varchar(255) DEFAULT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `snippet` mediumtext,
  `is_searchengine` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_request`
-- 

DROP TABLE IF EXISTS `owa_request`;
CREATE TABLE IF NOT EXISTS `owa_request` (
  `id` bigint(20) NOT NULL,
  `visitor_id` bigint(20) DEFAULT NULL,
  `session_id` bigint(20) DEFAULT NULL,
  `site_id` varchar(255) DEFAULT NULL,
  `referer_id` bigint(20) DEFAULT NULL,
  `ua_id` bigint(20) DEFAULT NULL,
  `host_id` bigint(20) DEFAULT NULL,
  `os_id` bigint(20) DEFAULT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `referring_search_term_id` bigint(20) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `yyyymmdd` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` tinyint(2) DEFAULT NULL,
  `dayofweek` varchar(10) DEFAULT NULL,
  `dayofyear` int(11) DEFAULT NULL,
  `weekofyear` int(11) DEFAULT NULL,
  `last_req` bigint(20) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `is_new_visitor` tinyint(1) DEFAULT NULL,
  `is_repeat_visitor` tinyint(1) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `days_since_prior_session` int(11) DEFAULT NULL,
  `days_since_first_session` int(11) DEFAULT NULL,
  `num_prior_sessions` int(11) DEFAULT NULL,
  `medium` varchar(255) DEFAULT NULL,
  `source_id` bigint(20) DEFAULT NULL,
  `ad_id` bigint(20) DEFAULT NULL,
  `campaign_id` bigint(20) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `cv1_name` varchar(255) DEFAULT NULL,
  `cv1_value` varchar(255) DEFAULT NULL,
  `cv2_name` varchar(255) DEFAULT NULL,
  `cv2_value` varchar(255) DEFAULT NULL,
  `cv3_name` varchar(255) DEFAULT NULL,
  `cv3_value` varchar(255) DEFAULT NULL,
  `cv4_name` varchar(255) DEFAULT NULL,
  `cv4_value` varchar(255) DEFAULT NULL,
  `cv5_name` varchar(255) DEFAULT NULL,
  `cv5_value` varchar(255) DEFAULT NULL,
  `inbound_visitor_id` bigint(20) DEFAULT NULL,
  `inbound_session_id` bigint(20) DEFAULT NULL,
  `feed_subscription_id` bigint(20) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `hour` tinyint(2) DEFAULT NULL,
  `minute` tinyint(2) DEFAULT NULL,
  `second` tinyint(2) DEFAULT NULL,
  `msec` int(11) DEFAULT NULL,
  `document_id` varchar(255) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `prior_document_id` bigint(20) DEFAULT NULL,
  `is_comment` tinyint(1) DEFAULT NULL,
  `is_entry_page` tinyint(1) DEFAULT NULL,
  `is_browser` tinyint(1) DEFAULT NULL,
  `is_robot` tinyint(1) DEFAULT NULL,
  `is_feedreader` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `site_id` (`site_id`),
  KEY `yyyymmdd` (`yyyymmdd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_search_term_dim`
-- 

DROP TABLE IF EXISTS `owa_search_term_dim`;
CREATE TABLE IF NOT EXISTS `owa_search_term_dim` (
  `id` bigint(20) NOT NULL,
  `terms` varchar(255) DEFAULT NULL,
  `term_count` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_session`
-- 

DROP TABLE IF EXISTS `owa_session`;
CREATE TABLE IF NOT EXISTS `owa_session` (
  `id` bigint(20) NOT NULL,
  `visitor_id` bigint(20) DEFAULT NULL,
  `site_id` varchar(255) DEFAULT NULL,
  `referer_id` bigint(20) DEFAULT NULL,
  `ua_id` bigint(20) DEFAULT NULL,
  `host_id` bigint(20) DEFAULT NULL,
  `os_id` bigint(20) DEFAULT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `referring_search_term_id` bigint(20) DEFAULT NULL,
  `timestamp` int(11) DEFAULT NULL,
  `yyyymmdd` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` tinyint(2) DEFAULT NULL,
  `dayofweek` varchar(10) DEFAULT NULL,
  `dayofyear` int(11) DEFAULT NULL,
  `weekofyear` int(11) DEFAULT NULL,
  `last_req` bigint(20) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `is_new_visitor` tinyint(1) DEFAULT NULL,
  `is_repeat_visitor` tinyint(1) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL,
  `days_since_prior_session` int(11) DEFAULT NULL,
  `days_since_first_session` int(11) DEFAULT NULL,
  `num_prior_sessions` int(11) DEFAULT NULL,
  `medium` varchar(255) DEFAULT NULL,
  `source_id` bigint(20) DEFAULT NULL,
  `ad_id` bigint(20) DEFAULT NULL,
  `campaign_id` bigint(20) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `cv1_name` varchar(255) DEFAULT NULL,
  `cv1_value` varchar(255) DEFAULT NULL,
  `cv2_name` varchar(255) DEFAULT NULL,
  `cv2_value` varchar(255) DEFAULT NULL,
  `cv3_name` varchar(255) DEFAULT NULL,
  `cv3_value` varchar(255) DEFAULT NULL,
  `cv4_name` varchar(255) DEFAULT NULL,
  `cv4_value` varchar(255) DEFAULT NULL,
  `cv5_name` varchar(255) DEFAULT NULL,
  `cv5_value` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `hour` tinyint(2) DEFAULT NULL,
  `minute` tinyint(2) DEFAULT NULL,
  `num_pageviews` int(11) DEFAULT NULL,
  `num_comments` int(11) DEFAULT NULL,
  `is_bounce` tinyint(1) DEFAULT NULL,
  `prior_session_lastreq` bigint(20) DEFAULT NULL,
  `prior_session_id` bigint(20) DEFAULT NULL,
  `time_sinse_priorsession` int(11) DEFAULT NULL,
  `prior_session_year` tinyint(4) DEFAULT NULL,
  `prior_session_month` varchar(255) DEFAULT NULL,
  `prior_session_day` tinyint(2) DEFAULT NULL,
  `prior_session_dayofweek` int(11) DEFAULT NULL,
  `prior_session_hour` tinyint(2) DEFAULT NULL,
  `prior_session_minute` tinyint(2) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `first_page_id` bigint(20) DEFAULT NULL,
  `last_page_id` bigint(20) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `is_robot` tinyint(1) DEFAULT NULL,
  `is_browser` tinyint(1) DEFAULT NULL,
  `is_feedreader` tinyint(1) DEFAULT NULL,
  `latest_attributions` blob,
  `goal_1` tinyint(1) DEFAULT NULL,
  `goal_1_start` tinyint(1) DEFAULT NULL,
  `goal_1_value` bigint(20) DEFAULT NULL,
  `goal_2` tinyint(1) DEFAULT NULL,
  `goal_2_start` tinyint(1) DEFAULT NULL,
  `goal_2_value` bigint(20) DEFAULT NULL,
  `goal_3` tinyint(1) DEFAULT NULL,
  `goal_3_start` tinyint(1) DEFAULT NULL,
  `goal_3_value` bigint(20) DEFAULT NULL,
  `goal_4` tinyint(1) DEFAULT NULL,
  `goal_4_start` tinyint(1) DEFAULT NULL,
  `goal_4_value` bigint(20) DEFAULT NULL,
  `goal_5` tinyint(1) DEFAULT NULL,
  `goal_5_start` tinyint(1) DEFAULT NULL,
  `goal_5_value` bigint(20) DEFAULT NULL,
  `goal_6` tinyint(1) DEFAULT NULL,
  `goal_6_start` tinyint(1) DEFAULT NULL,
  `goal_6_value` bigint(20) DEFAULT NULL,
  `goal_7` tinyint(1) DEFAULT NULL,
  `goal_7_start` tinyint(1) DEFAULT NULL,
  `goal_7_value` bigint(20) DEFAULT NULL,
  `goal_8` tinyint(1) DEFAULT NULL,
  `goal_8_start` tinyint(1) DEFAULT NULL,
  `goal_8_value` bigint(20) DEFAULT NULL,
  `goal_9` tinyint(1) DEFAULT NULL,
  `goal_9_start` tinyint(1) DEFAULT NULL,
  `goal_9_value` bigint(20) DEFAULT NULL,
  `goal_10` tinyint(1) DEFAULT NULL,
  `goal_10_start` tinyint(1) DEFAULT NULL,
  `goal_10_value` bigint(20) DEFAULT NULL,
  `goal_11` tinyint(1) DEFAULT NULL,
  `goal_11_start` tinyint(1) DEFAULT NULL,
  `goal_11_value` bigint(20) DEFAULT NULL,
  `goal_12` tinyint(1) DEFAULT NULL,
  `goal_12_start` tinyint(1) DEFAULT NULL,
  `goal_12_value` bigint(20) DEFAULT NULL,
  `goal_13` tinyint(1) DEFAULT NULL,
  `goal_13_start` tinyint(1) DEFAULT NULL,
  `goal_13_value` bigint(20) DEFAULT NULL,
  `goal_14` tinyint(1) DEFAULT NULL,
  `goal_14_start` tinyint(1) DEFAULT NULL,
  `goal_14_value` bigint(20) DEFAULT NULL,
  `goal_15` tinyint(1) DEFAULT NULL,
  `goal_15_start` tinyint(1) DEFAULT NULL,
  `goal_15_value` bigint(20) DEFAULT NULL,
  `num_goals` tinyint(1) DEFAULT NULL,
  `num_goal_starts` tinyint(1) DEFAULT NULL,
  `goals_value` bigint(20) DEFAULT NULL,
  `commerce_trans_count` int(11) DEFAULT NULL,
  `commerce_trans_revenue` bigint(20) DEFAULT NULL,
  `commerce_items_revenue` bigint(20) DEFAULT NULL,
  `commerce_items_count` int(11) DEFAULT NULL,
  `commerce_items_quantity` int(11) DEFAULT NULL,
  `commerce_shipping_revenue` bigint(20) DEFAULT NULL,
  `commerce_tax_revenue` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_id` (`site_id`),
  KEY `yyyymmdd` (`yyyymmdd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_site`
-- 

DROP TABLE IF EXISTS `owa_site`;
CREATE TABLE IF NOT EXISTS `owa_site` (
  `id` bigint(20) NOT NULL,
  `site_id` varchar(255) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` mediumtext,
  `site_family` varchar(255) DEFAULT NULL,
  `settings` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_site_user`
-- 

DROP TABLE IF EXISTS `owa_site_user`;
CREATE TABLE IF NOT EXISTS `owa_site_user` (
  `site_id` bigint(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_source_dim`
-- 

DROP TABLE IF EXISTS `owa_source_dim`;
CREATE TABLE IF NOT EXISTS `owa_source_dim` (
  `id` bigint(20) NOT NULL,
  `source_domain` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_ua`
-- 

DROP TABLE IF EXISTS `owa_ua`;
CREATE TABLE IF NOT EXISTS `owa_ua` (
  `id` bigint(20) NOT NULL,
  `ua` varchar(255) DEFAULT NULL,
  `browser_type` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_user`
-- 

DROP TABLE IF EXISTS `owa_user`;
CREATE TABLE IF NOT EXISTS `owa_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `real_name` varchar(255) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `temp_passkey` varchar(255) DEFAULT NULL,
  `creation_date` bigint(20) DEFAULT NULL,
  `last_update_date` bigint(20) DEFAULT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `owa_visitor`
-- 

DROP TABLE IF EXISTS `owa_visitor`;
CREATE TABLE IF NOT EXISTS `owa_visitor` (
  `id` bigint(20) NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `first_session_id` bigint(20) DEFAULT NULL,
  `first_session_year` int(11) DEFAULT NULL,
  `first_session_month` varchar(255) DEFAULT NULL,
  `first_session_day` int(11) DEFAULT NULL,
  `first_session_dayofyear` int(11) DEFAULT NULL,
  `first_session_timestamp` bigint(20) DEFAULT NULL,
  `first_session_yyyymmdd` bigint(20) DEFAULT NULL,
  `last_session_id` bigint(20) DEFAULT NULL,
  `last_session_year` int(11) DEFAULT NULL,
  `last_session_month` varchar(255) DEFAULT NULL,
  `last_session_day` int(11) DEFAULT NULL,
  `last_session_dayofyear` int(11) DEFAULT NULL,
  `num_prior_sessions` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
