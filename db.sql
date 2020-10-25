-- MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `depositEventsSubscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `contractAddress` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `events` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `address_contractAddress` (`address`,`contractAddress`),
  KEY `address` (`address`),
  KEY `contractAddress` (`contractAddress`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `depositHistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `depositContractAddress` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `keepAddress` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `bitcoinAddress` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currentState` int(11) NOT NULL,
  `lotsize` double NOT NULL,
  `bitcoinConfirmations` int(11) DEFAULT '0',
  `requiredConfirmations` int(11) DEFAULT NULL,
  `bitcoinTransaction` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `datetime` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `date` date DEFAULT NULL,
  `_signingGroupPubkeyX` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `_signingGroupPubkeyY` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updating` tinyint(1) NOT NULL DEFAULT '0',
  `isFunded` tinyint(1) DEFAULT NULL,
  `isRedeemed` tinyint(1) DEFAULT NULL,
  `isMinted` tinyint(1) DEFAULT NULL,
  `mintedBy` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `keepMembers` text COLLATE utf8_unicode_ci,
  `keepBond` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `depositContractAddress` (`depositContractAddress`),
  KEY `datetime` (`datetime`),
  KEY `updating` (`updating`),
  KEY `currentState` (`currentState`),
  KEY `isFunded` (`isFunded`),
  KEY `isRedeemed` (`isRedeemed`)
) ENGINE=InnoDB AUTO_INCREMENT=6529 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `depositSubscribeQueue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime DEFAULT NULL,
  `address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `contractAddress` char(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `event` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sended` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_contractAddress_event` (`email`,`contractAddress`,`event`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `systemContract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txhash` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `blockNumber` int(11) NOT NULL,
  `address` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `event` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime DEFAULT NULL,
  `format_date` date DEFAULT NULL,
  `_depositContractAddress` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `_keepAddress` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `_signingGroupPubkeyX` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `_signingGroupPubkeyY` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `_txid` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `_requester` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `_digest` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `_utxoValue` int(11) DEFAULT NULL,
  `_redeemerOutputScript` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `_requestedFee` int(11) DEFAULT NULL,
  `_outpoint` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `_r` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `_s` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `_wasFraud` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `previousOwner` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `newOwner` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `txhash_date_event` (`txhash`,`date`,`event`),
  KEY `event` (`event`),
  KEY `transactionHash` (`txhash`),
  KEY `depositContractAddress` (`_depositContractAddress`)
) ENGINE=InnoDB AUTO_INCREMENT=79579 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `TokenContract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txhash` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `blockNumber` int(11) NOT NULL,
  `address` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `event` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime DEFAULT NULL,
  `format_date` date DEFAULT NULL,
  `from` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `to` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `value` double NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `txhash_value_to` (`txhash`,`value`,`to`),
  KEY `from` (`from`),
  KEY `to` (`to`)
) ENGINE=InnoDB AUTO_INCREMENT=106374 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `values` (
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- 2020-10-25 18:36:49
