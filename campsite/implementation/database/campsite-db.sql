################################################################################
#
# CAMPSITE is a Unicode-enabled multilingual web content
# management system for news publications.
# CAMPFIRE is a Unicode-enabled java-based near WYSIWYG text editor.
# Copyright (C)2000,2001  Media Development Loan Fund
# contact: contact@campware.org - http://www.campware.org
# Campware encourages further development. Please let us know.
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
################################################################################

# MySQL dump 8.14
#
# Host: localhost    Database: campsite
#--------------------------------------------------------
# Server version	3.23.41

#
# Table structure for table 'ArticleIndex'
#

CREATE TABLE ArticleIndex (
  IdPublication int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  IdKeyword int(10) unsigned NOT NULL default '0',
  NrIssue int(10) unsigned NOT NULL default '0',
  NrSection int(10) unsigned NOT NULL default '0',
  NrArticle int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (IdPublication,IdLanguage,IdKeyword,NrIssue,NrSection,NrArticle)
) TYPE=MyISAM;

#
# Dumping data for table 'ArticleIndex'
#


#
# Table structure for table 'ArticleTopics'
#

CREATE TABLE ArticleTopics (
  NrArticle int(10) unsigned NOT NULL default '0',
  TopicId int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (NrArticle,TopicId)
) TYPE=MyISAM;

#
# Dumping data for table 'ArticleTopics'
#


#
# Table structure for table 'Articles'
#

CREATE TABLE Articles (
  IdPublication int(10) unsigned NOT NULL default '0',
  NrIssue int(10) unsigned NOT NULL default '0',
  NrSection int(10) unsigned NOT NULL default '0',
  Number int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Name varchar(140) NOT NULL default '',
  Type varchar(70) NOT NULL default '',
  IdUser int(10) unsigned NOT NULL default '0',
  OnFrontPage enum('N','Y') NOT NULL default 'N',
  OnSection enum('N','Y') NOT NULL default 'N',
  Published enum('N','S','Y') NOT NULL default 'N',
  UploadDate date NOT NULL default '0000-00-00',
  Keywords varchar(255) NOT NULL default '',
  Public enum('N','Y') NOT NULL default 'N',
  IsIndexed enum('N','Y') NOT NULL default 'N',
  LockUser int(10) unsigned NOT NULL default '0',
  LockTime datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (IdPublication,NrIssue,NrSection,Number,IdLanguage),
  UNIQUE KEY IdPublication (IdPublication,NrIssue,NrSection,IdLanguage,Name),
  UNIQUE KEY Number (Number,IdLanguage),
  UNIQUE KEY other_key (IdPublication,NrIssue,NrSection,IdLanguage,Number),
  KEY Type (Type)
) TYPE=MyISAM;

#
# Dumping data for table 'Articles'
#


#
# Table structure for table 'AutoId'
#

CREATE TABLE AutoId (
  DictionaryId int(10) unsigned NOT NULL default '0',
  ClassId int(10) unsigned NOT NULL default '0',
  ArticleId int(10) unsigned NOT NULL default '0',
  KeywordId int(10) unsigned NOT NULL default '0',
  LogTStamp datetime NOT NULL default '0000-00-00 00:00:00',
  TopicId int(10) unsigned NOT NULL default '0'
) TYPE=MyISAM;

#
# Dumping data for table 'AutoId'
#

INSERT INTO AutoId VALUES (0,0,0,0,'0000-00-00 00:00:00',0);

#
# Table structure for table 'Classes'
#

CREATE TABLE Classes (
  Id int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Name varchar(140) NOT NULL default '',
  PRIMARY KEY  (Id,IdLanguage),
  UNIQUE KEY IdLanguage (IdLanguage,Name)
) TYPE=MyISAM;

#
# Dumping data for table 'Classes'
#


#
# Table structure for table 'Countries'
#

CREATE TABLE Countries (
  Code char(2) NOT NULL default '',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Name varchar(140) NOT NULL default '',
  PRIMARY KEY  (Code,IdLanguage),
  UNIQUE KEY IdLanguage (IdLanguage,Name)
) TYPE=MyISAM;

#
# Dumping data for table 'Countries'
#

INSERT INTO Countries VALUES ('CZ',1,'Czech Republic');
INSERT INTO Countries VALUES ('US',1,'United States Of America');
INSERT INTO Countries VALUES ('GB',1,'Great Britain');
INSERT INTO Countries VALUES ('RO',1,'Romania');
INSERT INTO Countries VALUES ('GB',2,'Marea Britanie');
INSERT INTO Countries VALUES ('RO',2,'Rom‚nia');
INSERT INTO Countries VALUES ('CZ',2,'Republica Ceh„');
INSERT INTO Countries VALUES ('UA',1,'Ukraine');
INSERT INTO Countries VALUES ('YU',1,'Yugoslavia');
INSERT INTO Countries VALUES ('YU',4,'Jugoslavija');
INSERT INTO Countries VALUES ('DE',5,'Deutschland');
INSERT INTO Countries VALUES ('DE',1,'Germany');
INSERT INTO Countries VALUES ('AT',1,'Austria');
INSERT INTO Countries VALUES ('AT',6,'÷sterreich');
INSERT INTO Countries VALUES ('IT',1,'Italy');
INSERT INTO Countries VALUES ('IT',14,'Italia');
INSERT INTO Countries VALUES ('FR',1,'France');
INSERT INTO Countries VALUES ('FR',12,'France');
INSERT INTO Countries VALUES ('PT',1,'Portugal');
INSERT INTO Countries VALUES ('PT',9,'Portugal');
INSERT INTO Countries VALUES ('ES',1,'Spain');
INSERT INTO Countries VALUES ('ES',13,'EspaÒa');

#
# Table structure for table 'Dictionary'
#

CREATE TABLE Dictionary (
  Id int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Keyword varchar(140) NOT NULL default '',
  PRIMARY KEY  (IdLanguage,Keyword),
  UNIQUE KEY Id (Id,IdLanguage)
) TYPE=MyISAM;

#
# Dumping data for table 'Dictionary'
#


#
# Table structure for table 'Errors'
#

CREATE TABLE Errors (
  Number int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Message char(255) NOT NULL default '',
  PRIMARY KEY  (Number,IdLanguage)
) TYPE=MyISAM;

#
# Dumping data for table 'Errors'
#

INSERT INTO Errors VALUES (4000,1,'Internal error.');
INSERT INTO Errors VALUES (4001,1,'Username not specified.');
INSERT INTO Errors VALUES (4002,1,'Invalid username.');
INSERT INTO Errors VALUES (4003,1,'Password not specified.');
INSERT INTO Errors VALUES (4004,1,'Invalid password.');
INSERT INTO Errors VALUES (2000,1,'Internal error');
INSERT INTO Errors VALUES (2001,1,'Username is not specified. Please fill out login name field.');
INSERT INTO Errors VALUES (2002,1,'You are not a reader.');
INSERT INTO Errors VALUES (2003,1,'Publication not specified.');
INSERT INTO Errors VALUES (2004,1,'There are other subscriptions not payed.');
INSERT INTO Errors VALUES (2005,1,'Time unit not specified.');
INSERT INTO Errors VALUES (3000,1,'Internal error.');
INSERT INTO Errors VALUES (3001,1,'Username already exists.');
INSERT INTO Errors VALUES (3002,1,'Name is not specified. Please fill out name field.');
INSERT INTO Errors VALUES (3003,1,'Username is not specified. Please fill out login name field.');
INSERT INTO Errors VALUES (3004,1,'Password is not specified. Please fill out password field.');
INSERT INTO Errors VALUES (3005,1,'EMail is not specified. Please fill out EMail field.');
INSERT INTO Errors VALUES (3006,1,'EMail address already exists. Please try to login with your old account.');
INSERT INTO Errors VALUES (3007,1,'Invalid user identifier');
INSERT INTO Errors VALUES (3008,1,'No country specified. Please select a country.');
INSERT INTO Errors VALUES (3009,1,'Password (again) is not specified. Please fill out password (again) field.');
INSERT INTO Errors VALUES (3010,1,'Passwords do not match. Please fill out the same password to both password fields.');
INSERT INTO Errors VALUES (3011,1,'Password is too simple. Please choose a better password (at least 6 characters).');

#
# Table structure for table 'Events'
#

CREATE TABLE Events (
  Id int(10) unsigned NOT NULL default '0',
  Name varchar(140) NOT NULL default '',
  Notify enum('N','Y') NOT NULL default 'N',
  IdLanguage int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (Id,IdLanguage),
  UNIQUE KEY Name (Name)
) TYPE=MyISAM;

#
# Dumping data for table 'Events'
#

INSERT INTO Events VALUES (1,'Add Publication','N',1);
INSERT INTO Events VALUES (2,'Delete Publication','N',1);
INSERT INTO Events VALUES (11,'Add Issue','N',1);
INSERT INTO Events VALUES (12,'Delete Issue','N',1);
INSERT INTO Events VALUES (13,'Change Issue Template','N',1);
INSERT INTO Events VALUES (14,'Change issue status','N',1);
INSERT INTO Events VALUES (15,'Add Issue Translation','N',1);
INSERT INTO Events VALUES (21,'Add Section','N',1);
INSERT INTO Events VALUES (22,'Delete section','N',1);
INSERT INTO Events VALUES (31,'Add Article','Y',1);
INSERT INTO Events VALUES (32,'Delete article','N',1);
INSERT INTO Events VALUES (33,'Change article field','N',1);
INSERT INTO Events VALUES (34,'Change article properties','N',1);
INSERT INTO Events VALUES (35,'Change article status','Y',1);
INSERT INTO Events VALUES (41,'Add Image','Y',1);
INSERT INTO Events VALUES (42,'Delete image','N',1);
INSERT INTO Events VALUES (43,'Change image properties','N',1);
INSERT INTO Events VALUES (51,'Add User','N',1);
INSERT INTO Events VALUES (52,'Delete User','N',1);
INSERT INTO Events VALUES (53,'Changes Own Password','N',1);
INSERT INTO Events VALUES (54,'Change User Password','N',1);
INSERT INTO Events VALUES (55,'Change User Permissions','N',1);
INSERT INTO Events VALUES (56,'Change user information','N',1);
INSERT INTO Events VALUES (61,'Add article type','N',1);
INSERT INTO Events VALUES (62,'Delete article type','N',1);
INSERT INTO Events VALUES (71,'Add article type field','N',1);
INSERT INTO Events VALUES (72,'Delete article type field','N',1);
INSERT INTO Events VALUES (81,'Add dictionary class','N',1);
INSERT INTO Events VALUES (82,'Delete dictionary class','N',1);
INSERT INTO Events VALUES (91,'Add dictionary keyword','N',1);
INSERT INTO Events VALUES (92,'Delete dictionary keyword','N',1);
INSERT INTO Events VALUES (101,'Add language','N',1);
INSERT INTO Events VALUES (102,'Delete language','N',1);
INSERT INTO Events VALUES (103,'Modify language','N',1);
INSERT INTO Events VALUES (112,'Delete templates','N',1);
INSERT INTO Events VALUES (111,'Add templates','N',1);
INSERT INTO Events VALUES (121,'Add user type','N',1);
INSERT INTO Events VALUES (122,'Delete user type','N',1);
INSERT INTO Events VALUES (123,'Change user type','N',1);
INSERT INTO Events VALUES (3,'Change publication information','N',1);
INSERT INTO Events VALUES (36,'Change article template','N',1);
INSERT INTO Events VALUES (57,'Add IP Group','N',1);
INSERT INTO Events VALUES (58,'Delete IP Group','N',1);
INSERT INTO Events VALUES (131,'Add country','N',1);
INSERT INTO Events VALUES (132,'Add country translation','N',1);
INSERT INTO Events VALUES (133,'Change country name','N',1);
INSERT INTO Events VALUES (134,'Delete country','N',1);
INSERT INTO Events VALUES (4,'Add default subscription time','N',1);
INSERT INTO Events VALUES (5,'Delete default subscription time','N',1);
INSERT INTO Events VALUES (6,'Change default subscription time','N',1);
INSERT INTO Events VALUES (113,'Edit template','N',1);
INSERT INTO Events VALUES (114,'Create template','N',1);
INSERT INTO Events VALUES (115,'Duplicate template','N',1);
INSERT INTO Events VALUES (141,'Add topic','N',1);
INSERT INTO Events VALUES (142,'Delete topic','N',1);
INSERT INTO Events VALUES (143,'Update topic','N',1);
INSERT INTO Events VALUES (144,'Add topic to article','N',1);
INSERT INTO Events VALUES (145,'Delete topic from article','N',1);

#
# Table structure for table 'Images'
#

CREATE TABLE Images (
  IdPublication int(10) unsigned NOT NULL default '0',
  NrIssue int(10) unsigned NOT NULL default '0',
  NrSection int(10) unsigned NOT NULL default '0',
  NrArticle int(10) unsigned NOT NULL default '0',
  Number int(10) unsigned NOT NULL default '0',
  Description varchar(255) NOT NULL default '',
  Photographer varchar(140) NOT NULL default '',
  Place varchar(140) NOT NULL default '',
  Date date NOT NULL default '0000-00-00',
  ContentType varchar(64) NOT NULL default '',
  Image mediumblob NOT NULL,
  PRIMARY KEY  (IdPublication,NrIssue,NrSection,NrArticle,Number)
) TYPE=MyISAM;

#
# Dumping data for table 'Images'
#


#
# Table structure for table 'Issues'
#

CREATE TABLE Issues (
  IdPublication int(10) unsigned NOT NULL default '0',
  Number int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Name varchar(140) NOT NULL default '',
  PublicationDate date NOT NULL default '0000-00-00',
  Published enum('N','Y') NOT NULL default 'N',
  FrontPage varchar(128) NOT NULL default '',
  SingleArticle varchar(128) NOT NULL default '',
  PRIMARY KEY  (IdPublication,Number,IdLanguage)
) TYPE=MyISAM;

#
# Dumping data for table 'Issues'
#


#
# Table structure for table 'KeywordClasses'
#

CREATE TABLE KeywordClasses (
  IdDictionary int(10) unsigned NOT NULL default '0',
  IdClasses int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Definition mediumblob NOT NULL,
  PRIMARY KEY  (IdDictionary,IdClasses,IdLanguage),
  KEY IdClasses (IdClasses)
) TYPE=MyISAM;

#
# Dumping data for table 'KeywordClasses'
#


#
# Table structure for table 'KeywordIndex'
#

CREATE TABLE KeywordIndex (
  Keyword varchar(70) NOT NULL default '',
  Id int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (Keyword)
) TYPE=MyISAM;

#
# Dumping data for table 'KeywordIndex'
#


#
# Table structure for table 'Languages'
#

CREATE TABLE Languages (
  Id int(10) unsigned NOT NULL auto_increment,
  Name varchar(140) NOT NULL default '',
  CodePage varchar(140) NOT NULL default '',
  OrigName varchar(140) NOT NULL default '',
  Code varchar(21) NOT NULL default '',
  Month1 varchar(140) NOT NULL default '',
  Month2 varchar(140) NOT NULL default '',
  Month3 varchar(140) NOT NULL default '',
  Month4 varchar(140) NOT NULL default '',
  Month5 varchar(140) NOT NULL default '',
  Month6 varchar(140) NOT NULL default '',
  Month7 varchar(140) NOT NULL default '',
  Month8 varchar(140) NOT NULL default '',
  Month9 varchar(140) NOT NULL default '',
  Month10 varchar(140) NOT NULL default '',
  Month11 varchar(140) NOT NULL default '',
  Month12 varchar(140) NOT NULL default '',
  WDay1 varchar(140) NOT NULL default '',
  WDay2 varchar(140) NOT NULL default '',
  WDay3 varchar(140) NOT NULL default '',
  WDay4 varchar(140) NOT NULL default '',
  WDay5 varchar(140) NOT NULL default '',
  WDay6 varchar(140) NOT NULL default '',
  WDay7 varchar(140) NOT NULL default '',
  PRIMARY KEY  (Id),
  UNIQUE KEY Name (Name)
) TYPE=MyISAM;

#
# Dumping data for table 'Languages'
#

INSERT INTO Languages VALUES (1,'English','ISO-8859-1','English','en','January','February','March','April','May','June','July','August','September','October','November','December','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
INSERT INTO Languages VALUES (2,'Romanian','ISO-8859-2','Rom‚n„','ro','Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie','Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie','Duminic„','Luni','Mar˛i','Miercuri','Joi','Vineri','S‚mb„t„');
INSERT INTO Languages VALUES (3,'Hebrew','ISO-8859-9','¯‡Ë˜¯‡Ë','he','˜¯‡ Ë‚ÎÈÁ‚ÎÚÁ ÎÚÈÁ','ÎÚÈÁ ÎÚÈ','ÁÎÚÈÁÎÚÈÂ456','˜¯‡Ë˜¯‡Ë¯‡','„Î„‚ÎÈ','ÒÚÈ‚ÎÚÈ','‚ÎÚÈ','‚ÎÚÈ„˜¯Ú˘„‚Ú˘„','‚˘„‚˘„/\'¯˜¯È','‚ÎÚÈÎÚÈÁ','ÈˆÈÚ˙ÍÁÏÍÛÁÏ','‚ÎÚÈÁÈÏÚÈÁ','ÂÔÌÙÂÔÌ‡ËÂ','Ô‡ËÂÔÈÁÍ˙Óˆ˙ıÓˆ˙Ó','‰Ó·‰Ó‰·','ÊÒ·‰„˘„‚Î','ÎÚÈÎÚÈÁÚÈÏÍÏ','ÈÁÏÍÈÁÌÂËÔÌË','˜¯‡Ë˜¯‡Ë');
INSERT INTO Languages VALUES (4,'Serbo-Croatian','ISO-8859-2','Srpskohrvatski','sh','Januar','Februar','Mart','April','Maj','Jun','Jul','Avgust','Septembar','Oktobar','Novembar','Decembar','Nedelja','Ponedeljak','Utorak','Sreda','»etvrtak','Petak','Subota');
INSERT INTO Languages VALUES (5,'German','ISO-8859-1','Deutsch','de','Januar','Februar','M‰rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
INSERT INTO Languages VALUES (6,'Austrian','IS0-8859-1','Deutsch (÷sterreich)','at','J‰nner','Februar','M‰rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember','Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag');
INSERT INTO Languages VALUES (7,'Croatian','ISO-8859-2','Hrvatski','hr','SijeËanj','VeljaËa','Oæujak','Travanj','Svibanj','Lipanj','Srpanj','Kolovoz','Rujan','Listopad','Studeni','Prosinac','Nedjelja','Ponedjeljak','Utorak','Srijeda','»etvrtak','Petak','Subota');
INSERT INTO Languages VALUES (8,'Czech','ISO-8859-2','»esk˝','cz','Leden','⁄nor','B¯ezen','Duben','KvÏten','»erven','»ervenec','Srpen','Z·¯Ì','ÿÌjen','Listopad','Prosinec','NedÏle','PondÏlÌ','⁄ter˝','St¯eda','»tvrtek','P·tek','Sobota');
INSERT INTO Languages VALUES (9,'Portuguese','ISO-8859-1','PortuguÍs','pt','Janeiro','Fevereiro','MarÁo','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro','Domingo','Segunda-feira','TerÁa-feira','Quarta-feira','Quinta-feira','Sexta-feira','S·bado');
INSERT INTO Languages VALUES (10,'Sebian (Cyrillic)','ISO-8859-5','¡‡ﬂ·⁄ÿ (´ÿ‡ÿ€ÿÊ–)','sr','¯–›„–‡','‰’—‡„–‡','‹–‡‚','–ﬂ‡ÿ€','‹–¯','¯„›','¯„€','–“”„·‚','·’ﬂ‚’‹—–‡','ﬁ⁄‚ﬁ—–‡','›ﬁ“’‹—–‡','‘’Ê’‹—–‡','Ω’‘’˘–','øﬁ›’‘’˘–⁄','√‚ﬁ‡–⁄','¡‡’‘–','«’‚“‡‚–⁄','ø’‚–⁄','¡„—ﬁ‚–');
INSERT INTO Languages VALUES (11,'Bosnian','ISO-8859-2','Bosanski','sh','Januar','Februar','Mart','April','Maj','Juni','Juli','Avgust','Septembar','Oktobar','Novembar','Decembar','Nedjelja','Ponedeljak','Utorak','Srijeda','»etvrtak','Petak','Subota');
INSERT INTO Languages VALUES (12,'French','ISO-8859-1','FranÁais','fr','Janvier','FÈvrier','Mars','Avril','Peut','Juin','Juli','Ao˚t','Septembre','Octobre','Novembre','DÈcembre','Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
INSERT INTO Languages VALUES (13,'Spanish','ISO-8859-1','EspaÒol','es','Enero','Febrero','Marcha','Abril','Puede','Junio','Juli','Agosto','Septiembre','Octubre','Noviembre','Diciembre','Domingo','Lunes','Martes','MiÈrcoles','Jueves','Viernes','S·bado');
INSERT INTO Languages VALUES (14,'Italian','ISO-8859-1','Italiano','it','Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre','Domenica','LunedÏ','MartedÏ','MercoledÏ','GiovedÏ','VenerdÏ','Sabato');
INSERT INTO Languages VALUES (15,'Russian','ISO-8859-5','¿„··⁄ÿŸ','ru','Ô›“–‡Ï','‰’“‡–€Ï','‹–‡‚','–ﬂ‡’€Ï','‹–Ÿ','ÿÓ›Ï','ÿÓ€Ï','–“”„·‚','·’›‚Ô—‡Ï','ﬁ⁄‚Ô—‡Ï','›ﬁÔ—‡Ï','‘’⁄–—‡Ï','“ﬁ·⁄‡’·’›Ï’','ﬂﬁ›’‘’€Ï›ÿ⁄','“‚ﬁ‡›ÿ⁄','·‡’‘–','Á’‚“’‡”','ﬂÔ‚›ÿÊ–','·„——ﬁ‚–');

#
# Table structure for table 'Log'
#

CREATE TABLE Log (
  TStamp datetime NOT NULL default '0000-00-00 00:00:00',
  IdEvent int(10) unsigned NOT NULL default '0',
  User varchar(70) NOT NULL default '',
  Text varchar(255) NOT NULL default '',
  KEY IdEvent (IdEvent)
) TYPE=MyISAM;

#
# Dumping data for table 'Log'
#


#
# Table structure for table 'Publications'
#

CREATE TABLE Publications (
  Id int(10) unsigned NOT NULL auto_increment,
  Name varchar(255) NOT NULL default '',
  Site varchar(255) NOT NULL default '',
  IdDefaultLanguage int(10) unsigned NOT NULL default '0',
  PayTime int(10) unsigned NOT NULL default '0',
  TimeUnit enum('D','W','M','Y') NOT NULL default 'D',
  UnitCost float(10,2) unsigned NOT NULL default '0.00',
  Currency varchar(140) NOT NULL default '',
  TrialTime int(10) unsigned NOT NULL default '0',
  PaidTime int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (Id),
  UNIQUE KEY Site (Site),
  UNIQUE KEY Name (Name)
) TYPE=MyISAM;

#
# Dumping data for table 'Publications'
#


#
# Table structure for table 'Sections'
#

CREATE TABLE Sections (
  IdPublication int(10) unsigned NOT NULL default '0',
  NrIssue int(10) unsigned NOT NULL default '0',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Number int(10) unsigned NOT NULL default '0',
  Name varchar(255) NOT NULL default '',
  PRIMARY KEY  (IdPublication,NrIssue,IdLanguage,Number),
  UNIQUE KEY IdPublication (IdPublication,NrIssue,IdLanguage,Name)
) TYPE=MyISAM;

#
# Dumping data for table 'Sections'
#


#
# Table structure for table 'SubsByIP'
#

CREATE TABLE SubsByIP (
  IdUser int(10) unsigned NOT NULL default '0',
  StartIP int(10) unsigned NOT NULL default '0',
  Addresses int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (IdUser,StartIP)
) TYPE=MyISAM;

#
# Dumping data for table 'SubsByIP'
#


#
# Table structure for table 'SubsDefTime'
#

CREATE TABLE SubsDefTime (
  CountryCode char(21) NOT NULL default '',
  IdPublication int(10) unsigned NOT NULL default '0',
  TrialTime int(10) unsigned NOT NULL default '0',
  PaidTime int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (CountryCode,IdPublication)
) TYPE=MyISAM;

#
# Dumping data for table 'SubsDefTime'
#


#
# Table structure for table 'SubsSections'
#

CREATE TABLE SubsSections (
  IdSubscription int(10) unsigned NOT NULL default '0',
  SectionNumber int(10) unsigned NOT NULL default '0',
  StartDate date NOT NULL default '0000-00-00',
  Days int(10) unsigned NOT NULL default '0',
  PaidDays int(10) unsigned NOT NULL default '0',
  NoticeSent enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (IdSubscription,SectionNumber)
) TYPE=MyISAM;

#
# Dumping data for table 'SubsSections'
#


#
# Table structure for table 'Subscriptions'
#

CREATE TABLE Subscriptions (
  Id int(10) unsigned NOT NULL auto_increment,
  IdUser int(10) unsigned NOT NULL default '0',
  IdPublication int(10) unsigned NOT NULL default '0',
  Active enum('Y','N') NOT NULL default 'Y',
  ToPay float(10,2) unsigned NOT NULL default '0.00',
  Currency varchar(70) NOT NULL default '',
  Type enum('T','P') NOT NULL default 'T',
  PRIMARY KEY  (Id),
  UNIQUE KEY IdUser (IdUser,IdPublication)
) TYPE=MyISAM;

#
# Dumping data for table 'Subscriptions'
#


#
# Table structure for table 'TimeUnits'
#

CREATE TABLE TimeUnits (
  Unit char(1) NOT NULL default '',
  IdLanguage int(10) unsigned NOT NULL default '0',
  Name varchar(70) NOT NULL default '',
  PRIMARY KEY  (Unit,IdLanguage)
) TYPE=MyISAM;

#
# Dumping data for table 'TimeUnits'
#

INSERT INTO TimeUnits VALUES ('D',1,'days');
INSERT INTO TimeUnits VALUES ('W',1,'weeks');
INSERT INTO TimeUnits VALUES ('M',1,'months');
INSERT INTO TimeUnits VALUES ('Y',1,'years');

#
# Table structure for table 'Topics'
#

CREATE TABLE Topics (
  Id int(10) unsigned NOT NULL default '0',
  LanguageId int(10) unsigned NOT NULL default '0',
  Name varchar(100) NOT NULL default '',
  ParentId int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (Id,LanguageId),
  UNIQUE KEY Name (LanguageId,Name)
) TYPE=MyISAM;

#
# Dumping data for table 'Topics'
#


#
# Table structure for table 'UserPerm'
#

CREATE TABLE UserPerm (
  IdUser int(10) unsigned NOT NULL default '0',
  ManagePub enum('N','Y') NOT NULL default 'N',
  DeletePub enum('N','Y') NOT NULL default 'N',
  ManageIssue enum('N','Y') NOT NULL default 'N',
  DeleteIssue enum('N','Y') NOT NULL default 'N',
  ManageSection enum('N','Y') NOT NULL default 'N',
  DeleteSection enum('N','Y') NOT NULL default 'N',
  AddArticle enum('N','Y') NOT NULL default 'N',
  ChangeArticle enum('N','Y') NOT NULL default 'N',
  DeleteArticle enum('N','Y') NOT NULL default 'N',
  AddImage enum('N','Y') NOT NULL default 'N',
  ChangeImage enum('N','Y') NOT NULL default 'N',
  DeleteImage enum('N','Y') NOT NULL default 'N',
  ManageTempl enum('N','Y') NOT NULL default 'N',
  DeleteTempl enum('N','Y') NOT NULL default 'N',
  ManageUsers enum('N','Y') NOT NULL default 'N',
  ManageSubscriptions enum('N','Y') NOT NULL default 'N',
  DeleteUsers enum('N','Y') NOT NULL default 'N',
  ManageUserTypes enum('N','Y') NOT NULL default 'N',
  ManageArticleTypes enum('N','Y') NOT NULL default 'N',
  DeleteArticleTypes enum('N','Y') NOT NULL default 'N',
  ManageLanguages enum('N','Y') NOT NULL default 'N',
  DeleteLanguages enum('N','Y') NOT NULL default 'N',
  ManageDictionary enum('N','Y') NOT NULL default 'N',
  DeleteDictionary enum('N','Y') NOT NULL default 'N',
  ManageCountries enum('N','Y') NOT NULL default 'N',
  DeleteCountries enum('N','Y') NOT NULL default 'N',
  ManageClasses enum('N','Y') NOT NULL default 'N',
  MailNotify enum('N','Y') NOT NULL default 'N',
  ViewLogs enum('N','Y') NOT NULL default 'N',
  ManageLocalizer enum('N','Y') NOT NULL default 'N',
  ManageIndexer enum('N','Y') NOT NULL default 'N',
  Publish enum('N','Y') NOT NULL default 'N',
  ManageTopics enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (IdUser)
) TYPE=MyISAM;

#
# Dumping data for table 'UserPerm'
#

INSERT INTO UserPerm VALUES (1,'Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y');

#
# Table structure for table 'UserTypes'
#

CREATE TABLE UserTypes (
  Name varchar(140) NOT NULL default '',
  Reader enum('N','Y') NOT NULL default 'N',
  ManagePub enum('N','Y') NOT NULL default 'N',
  DeletePub enum('N','Y') NOT NULL default 'N',
  ManageIssue enum('N','Y') NOT NULL default 'N',
  DeleteIssue enum('N','Y') NOT NULL default 'N',
  ManageSection enum('N','Y') NOT NULL default 'N',
  DeleteSection enum('N','Y') NOT NULL default 'N',
  AddArticle enum('N','Y') NOT NULL default 'N',
  ChangeArticle enum('N','Y') NOT NULL default 'N',
  DeleteArticle enum('N','Y') NOT NULL default 'N',
  AddImage enum('N','Y') NOT NULL default 'N',
  ChangeImage enum('N','Y') NOT NULL default 'N',
  DeleteImage enum('N','Y') NOT NULL default 'N',
  ManageTempl enum('N','Y') NOT NULL default 'N',
  DeleteTempl enum('N','Y') NOT NULL default 'N',
  ManageUsers enum('N','Y') NOT NULL default 'N',
  ManageSubscriptions enum('N','Y') NOT NULL default 'N',
  DeleteUsers enum('N','Y') NOT NULL default 'N',
  ManageUserTypes enum('N','Y') NOT NULL default 'N',
  ManageArticleTypes enum('N','Y') NOT NULL default 'N',
  DeleteArticleTypes enum('N','Y') NOT NULL default 'N',
  ManageLanguages enum('N','Y') NOT NULL default 'N',
  DeleteLanguages enum('N','Y') NOT NULL default 'N',
  ManageDictionary enum('N','Y') NOT NULL default 'N',
  DeleteDictionary enum('N','Y') NOT NULL default 'N',
  ManageCountries enum('N','Y') NOT NULL default 'N',
  DeleteCountries enum('N','Y') NOT NULL default 'N',
  ManageClasses enum('N','Y') NOT NULL default 'N',
  MailNotify enum('N','Y') NOT NULL default 'N',
  ViewLogs enum('N','Y') NOT NULL default 'N',
  ManageLocalizer enum('N','Y') NOT NULL default 'N',
  ManageIndexer enum('N','Y') NOT NULL default 'N',
  Publish enum('N','Y') NOT NULL default 'N',
  ManageTopics enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (Name)
) TYPE=MyISAM;

#
# Dumping data for table 'UserTypes'
#

INSERT INTO UserTypes VALUES ('Reader','Y','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N');
INSERT INTO UserTypes VALUES ('Administrator','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','Y','Y','N','N','Y');
INSERT INTO UserTypes VALUES ('Editor','N','N','N','N','N','N','N','Y','Y','Y','Y','Y','Y','N','N','N','N','N','N','N','N','N','N','N','N','N','N','N','Y','N','N','N','Y','N');
INSERT INTO UserTypes VALUES ('Chief Editor','N','N','N','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','Y','N','N','N','N','Y','Y','N','N','Y','Y','N','N','Y','N','Y','Y','Y','Y','Y');

#
# Table structure for table 'Users'
#

CREATE TABLE Users (
  Id int(10) unsigned NOT NULL auto_increment,
  KeyId int(10) unsigned default NULL,
  Name varchar(255) NOT NULL default '',
  UName varchar(70) NOT NULL default '',
  Password varchar(32) NOT NULL default '',
  EMail varchar(255) NOT NULL default '',
  Reader enum('Y','N') NOT NULL default 'Y',
  City varchar(100) NOT NULL default '',
  StrAddress varchar(255) NOT NULL default '',
  State varchar(32) NOT NULL default '',
  CountryCode varchar(21) NOT NULL default '',
  Phone varchar(20) NOT NULL default '',
  Fax varchar(20) NOT NULL default '',
  Contact varchar(64) NOT NULL default '',
  Phone2 varchar(20) NOT NULL default '',
  Title enum('Mr.','Mrs.','Ms.','Dr.') NOT NULL default 'Mr.',
  Gender enum('M','F') NOT NULL default 'M',
  Age enum('0-17','18-24','25-39','40-49','50-65','65-') NOT NULL default '0-17',
  PostalCode varchar(70) NOT NULL default '',
  Employer varchar(140) NOT NULL default '',
  EmployerType varchar(140) NOT NULL default '',
  Position varchar(70) NOT NULL default '',
  Interests mediumblob NOT NULL,
  How varchar(255) NOT NULL default '',
  Languages varchar(100) NOT NULL default '',
  Improvements mediumblob NOT NULL,
  Pref1 enum('N','Y') NOT NULL default 'N',
  Pref2 enum('N','Y') NOT NULL default 'N',
  Pref3 enum('N','Y') NOT NULL default 'N',
  Pref4 enum('N','Y') NOT NULL default 'N',
  Field1 varchar(150) NOT NULL default '',
  Field2 varchar(150) NOT NULL default '',
  Field3 varchar(150) NOT NULL default '',
  Field4 varchar(150) NOT NULL default '',
  Field5 varchar(150) NOT NULL default '',
  Text1 mediumblob NOT NULL,
  Text2 mediumblob NOT NULL,
  Text3 mediumblob NOT NULL,
  PRIMARY KEY  (Id),
  UNIQUE KEY UName (UName)
) TYPE=MyISAM;

#
# Dumping data for table 'Users'
#

INSERT INTO Users VALUES (1,NULL,'Administrator','admin','2c380f066e0e45d1','','N','','','','','','','','','Mr.','M','0-17','','','','','','','','','N','N','N','N','','','','','','','','');

