-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `dokument`;
CREATE TABLE `dokument` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Directory` varchar(255) NOT NULL,
  `CreationDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `Exists` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Freigabe`;
CREATE TABLE `Freigabe` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `UserId` int(11) NOT NULL,
  `DokumentId` int(11) NOT NULL,
  `FreigabeLevel` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `UserId` (`UserId`),
  KEY `DokumentId` (`DokumentId`),
  KEY `FreigabeLevel` (`FreigabeLevel`),
  CONSTRAINT `Freigabe_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `user` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `Freigabe_ibfk_2` FOREIGN KEY (`DokumentId`) REFERENCES `dokument` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `Freigabe_ibfk_3` FOREIGN KEY (`FreigabeLevel`) REFERENCES `freigabeLevel` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `freigabeLevel`;
CREATE TABLE `freigabeLevel` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Freigabe` varchar(20) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `freigabeLevel` (`Id`, `Freigabe`) VALUES
(1,	'Read'),
(2,	'ReadWrite'),
(3,	'Owner');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `UserLevel` int(11) NOT NULL DEFAULT '2',
  `Vorname` varchar(20) NOT NULL,
  `Nachname` varchar(20) NOT NULL,
  `Email` varchar(40) NOT NULL,
  `Password` varchar(60) NOT NULL,
  `Active` bit(1) NOT NULL DEFAULT b'0',
  `ActivationURL` varchar(40) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `UserLevel` (`UserLevel`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`UserLevel`) REFERENCES `userLevel` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `userLevel`;
CREATE TABLE `userLevel` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Level` varchar(15) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `userLevel` (`Id`, `Level`) VALUES
(1,	'admin'),
(2,	'user');

-- 2017-06-11 08:24:34
