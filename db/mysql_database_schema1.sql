-- run as mysql query after creating database
-- mysql_database_schema.sql
-- Schema for the 140dev Twitter framework MySQL database server
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `cache_tweets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` int(11) NOT NULL,
  `keyword` varchar(100) CHARACTER SET utf8 NOT NULL,
  `tweet` varchar(500) NOT NULL,
  `screen_name` varchar(100) NOT NULL,
  `image` varchar(500) CHARACTER SET utf8 NOT NULL,
  `tweet_id` bigint(20) NOT NULL,
  `retweet_count` int(11) NOT NULL,
  `like_count` int(11) NOT NULL,
  `description` varchar(500) NOT NULL,
  `location` varchar(500) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `created_at` varchar(100) CHARACTER SET latin1 NOT NULL,
  `cache_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Cache Table for showtweets';

CREATE TABLE `cache_tweets1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` int(11) NOT NULL,
  `tweet` varchar(500) NOT NULL,
  `screen_name` varchar(100) NOT NULL,
  `image` varchar(500) CHARACTER SET utf8 NOT NULL,
  `tweet_id` bigint(20) NOT NULL,
  `retweet_count` int(11) NOT NULL,
  `like_count` int(11) NOT NULL,
  `description` varchar(500) NOT NULL,
  `location` varchar(500) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `created_at` varchar(100) CHARACTER SET latin1 NOT NULL,
  `cache_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Cache Table for tweetlist';

CREATE TABLE `collection_words` (
  `id` int(5) NOT NULL,
  `words` varchar(60) NOT NULL,
  `type` enum('words','phrase') NOT NULL DEFAULT 'words',
  `time` datetime NOT NULL,
  `is_active` tinyint(2) NOT NULL DEFAULT '1',
  `out_words` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `exclusion_words` (
  `words` varchar(60) NOT NULL,
  `type` enum('partial','exact') NOT NULL DEFAULT 'partial',
  KEY `words` (`words`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8

CREATE TABLE IF NOT EXISTS `json_cache` (
  `tweet_id` bigint(20) unsigned NOT NULL,
  `cache_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cache_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `raw_tweet` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cache_id`),
  KEY `tweet_id` (`tweet_id`),
  KEY `cache_date` (`cache_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tweets` (
  `tweet_id` bigint(20) unsigned NOT NULL,
  `tweet_text` varchar(160) CHARACTER SET utf8mb4 NOT NULL,
  `created_at` datetime NOT NULL,
  `geo_lat` decimal(10,5) DEFAULT NULL,
  `geo_long` decimal(10,5) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `screen_name` char(20) NOT NULL,
  `name` varchar(20) DEFAULT NULL,
  `profile_image_url` varchar(200) DEFAULT NULL,
  `is_rt` tinyint(1) NOT NULL,
  PRIMARY KEY (`tweet_id`),
  KEY `created_at` (`created_at`),
  KEY `user_id` (`user_id`),
  KEY `screen_name` (`screen_name`),
  KEY `name` (`name`),
  FULLTEXT KEY `tweet_text` (`tweet_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tweet_mentions` (
  `tweet_id` bigint(20) unsigned NOT NULL,
  `source_user_id` bigint(20) unsigned NOT NULL,
  `target_user_id` bigint(20) unsigned NOT NULL,
  KEY `tweet_id` (`tweet_id`),
  KEY `source` (`source_user_id`),
  KEY `target` (`target_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `tweets_removed` (
  `tweet_id` bigint(20) UNSIGNED NOT NULL,
  `tweet_text` varchar(255) CHARACTER SET utf8 NOT NULL,
  `created_at` datetime NOT NULL,
  `geo_lat` decimal(10,5) DEFAULT NULL,
  `geo_long` decimal(10,5) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `screen_name` char(20) CHARACTER SET utf8 NOT NULL,
  `name` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `profile_image_url` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `is_rt` tinyint(1) NOT NULL,
  `tag` varchar(100) NOT NULL,
  `url` varchar(140) NOT NULL,
  `source_user_id` bigint(20) NOT NULL,
  `target_user_id` bigint(20) NOT NULL,
  KEY (`tweet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tweet_tags` (
  `tweet_id` bigint(20) unsigned NOT NULL,
  `tag` varchar(100) NOT NULL,
  KEY `tweet_id` (`tweet_id`),
  KEY `tag` (`tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tweet_urls` (
  `tweet_id` bigint(20) unsigned NOT NULL,
  `url` varchar(140) NOT NULL,
  KEY `tweet_id` (`tweet_id`),
  KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tweet_words` (
  `tweet_id` bigint(20) unsigned NOT NULL,
  `words` varchar(60) NOT NULL,
  KEY `tweet_id` (`tweet_id`),
  KEY `words` (`words`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` bigint(20) unsigned NOT NULL,
  `screen_name` varchar(20) NOT NULL,
  `name` varchar(20) DEFAULT NULL,
  `profile_image_url` varchar(200) DEFAULT NULL,
  `location` varchar(30) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `followers_count` int(10) unsigned DEFAULT NULL,
  `friends_count` int(10) unsigned DEFAULT NULL,
  `statuses_count` int(10) unsigned DEFAULT NULL,
  `time_zone` varchar(40) DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP 
     ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`name`),
  KEY `last_update` (`last_update`),
  KEY `screen_name` (`screen_name`),
  FULLTEXT KEY `description` (`description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;


