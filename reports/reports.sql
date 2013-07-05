-- Adminer 3.7.1 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = '+02:00';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `reports`;
CREATE DATABASE `reports` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `reports`;

DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `project_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `manday_cost` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`project_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `record`;
CREATE TABLE `record` (
  `record_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `project_id` int(11) unsigned NOT NULL,
  `from_datetime` datetime NOT NULL,
  `to_datetime` datetime NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `bug_note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`record_id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `record_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `record_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `passwd` char(128) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2013-07-05 19:53:49
