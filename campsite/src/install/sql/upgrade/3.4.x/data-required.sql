-- template cache
DELETE FROM `SystemPreferences` WHERE `varname` ='TemplateCacheHandler';
INSERT INTO `SystemPreferences` (`varname`,`value`) VALUES ('TemplateCacheHandler', NULL);
UPDATE `SystemPreferences` SET `varname` = 'DBCacheEngine', `value` = NULL WHERE `varname` ='CacheEngine';

-- add new events for the authors management
INSERT INTO `Events` (`Id`,`Name`,`Notify`,`IdLanguage`) VALUES ('172','Add Author','N','1'),('173','Edit Author','N','1'),('174','Delete Author','N','01'),('175','Add author type','N',1),('176','Delete author type','N',1);;

-- add default author types
INSERT INTO `AuthorTypes` (`id`,`type`) VALUES (NULL,'Author'),(NULL,'Writer'),(NULL,'Photographer'),(NULL,'Editor'),(NULL,'Columnist');

-- remove empty authors
CREATE TEMPORARY TABLE `EmptyAuthorsTmp` SELECT DISTINCT `id` FROM `Authors` WHERE `first_name` = '' AND `last_name` = '';
DELETE FROM `Authors` WHERE `id` IN (SELECT `id` FROM `EmptyAuthorsTmp` GROUP BY `id`);
DELETE FROM `ArticleAuthors` WHERE `fk_author_id` IN (SELECT `id` FROM `EmptyAuthorsTmp` GROUP BY `id`);
DROP TEMPORARY TABLE `EmptyAuthorsTmp`;

-- add authors from Articles table to ArticleAuthors
INSERT IGNORE INTO `ArticleAuthors` (`fk_article_number`,`fk_language_id`,`fk_author_id`)
    SELECT `Number`, `IdLanguage`, `fk_default_author_id` FROM Articles;

-- set the default author type to "Author" for all the links and authors
SET @rid := (SELECT `id` FROM `AuthorTypes` WHERE type = 'Author');
UPDATE `ArticleAuthors` SET `fk_type_id` = @rid;
INSERT IGNORE INTO `AuthorAssignedTypes` (`fk_author_id`) SELECT `id` FROM `Authors`;
UPDATE `AuthorAssignedTypes` SET `fk_type_id` = @rid;

-- add system setting for password recovery
INSERT INTO `SystemPreferences` (`varname`,`value`) VALUES ('PasswordRecovery','Y');

-- call additional db upgrade script
system php ./update_rights.php;

