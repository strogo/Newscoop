-- Create table for template cache db handler
CREATE TABLE IF NOT EXISTS `Cache` (
  `language` int(11) default NULL,
  `publication` int(11) default NULL,
  `issue` int(11) default NULL,
  `section` int(11) default NULL,
  `article` int(11) default NULL,
  `params` varchar(128) default NULL,
  `template` varchar(128) NOT NULL,
  `expired` int(11) NOT NULL,
  `content` mediumtext,
  UNIQUE KEY `index` (`language`,`publication`,`issue`,`section`,`article`,`params`,`template`),
  KEY `expired` (`expired`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Add CacheLifetime column for template cache handler
ALTER TABLE `Templates` ADD `CacheLifetime` INT NULL DEFAULT '0';

-- Create tables for authors management
CREATE TABLE IF NOT EXISTS `AuthorAliases` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fk_author_id` int(11) unsigned NOT NULL,
  `alias` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `AuthorAssignedTypes` (
  `fk_author_id` int(11) unsigned NOT NULL DEFAULT '0',
  `fk_type_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fk_author_id`,`fk_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `AuthorBiographies` (
  `fk_author_id` int(11) unsigned NOT NULL DEFAULT '0',
  `fk_language_id` int(11) unsigned NOT NULL DEFAULT '0',
  `biography` text NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`fk_author_id`,`fk_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `AuthorTypes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Add new fields to store some more author data
ALTER TABLE `Authors` ADD `type` INT NULL, ADD `skype` VARCHAR(255) NULL, ADD `jabber` VARCHAR(255) NULL, ADD `aim` VARCHAR(255) NULL, ADD `biography` TEXT NULL, ADD `image` INT NULL;

-- Change fileds to proper data type
ALTER TABLE `ArticleAuthors` CHANGE `fk_article_number` `fk_article_number` INT(10) UNSIGNED NULL, CHANGE `fk_language_id` `fk_language_id` INT(10) UNSIGNED NULL, CHANGE `fk_author_id` `fk_author_id` INT(10) UNSIGNED NULL, ADD `fk_type_id` INT(10) UNSIGNED NULL;

-- Add new column to store the token in password recovering
ALTER TABLE `liveuser_users` ADD COLUMN `password_reset_token` VARCHAR(85) NULL  AFTER `isActive`;
