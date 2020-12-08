SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `depositEventsSubscribe`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `depositHistory`;
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
  `pubKey` text COLLATE utf8_unicode_ci,
  `nextCheckCollateralization` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `depositContractAddress` (`depositContractAddress`),
  KEY `datetime` (`datetime`),
  KEY `updating` (`updating`),
  KEY `currentState` (`currentState`),
  KEY `isFunded` (`isFunded`),
  KEY `isRedeemed` (`isRedeemed`),
  KEY `nextCheckCollateralization` (`nextCheckCollateralization`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `depositSubscribeQueue`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `grantContract`;
CREATE TABLE `grantContract` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txhash` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `blockNumber` int(11) NOT NULL,
  `address` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `event` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime DEFAULT NULL,
  `grantId` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `operator` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `grantManager` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stakingContract` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `txhash_grantId_event` (`txhash`,`grantId`,`event`),
  KEY `event` (`event`),
  KEY `grantId` (`grantId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `grants`;
CREATE TABLE `grants` (
  `id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `withdrawn` double NOT NULL,
  `staked` double NOT NULL,
  `revokedAmount` double NOT NULL,
  `revokedWithdrawn` double NOT NULL,
  `revokedAt` datetime DEFAULT NULL,
  `revocable` tinyint(1) DEFAULT NULL,
  `grantee` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `grantManager` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `start` date DEFAULT NULL,
  `end` date DEFAULT NULL,
  `cliff` date DEFAULT NULL,
  `policy` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `availableToStake` double DEFAULT NULL,
  `unlockedAmount` double DEFAULT NULL,
  `withdrawable` double DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `KeepBonding`;
CREATE TABLE `KeepBonding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txhash` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `blockNumber` int(11) NOT NULL,
  `address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `event` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `operator` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `holder` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sortitionPool` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `referenceID` text COLLATE utf8_unicode_ci,
  `amount` double DEFAULT NULL,
  `newHolder` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `newReferenceID` text COLLATE utf8_unicode_ci,
  `destination` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `beneficiary` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `txhash_event_operator` (`txhash`,`event`,`operator`),
  KEY `event` (`event`),
  KEY `operator` (`operator`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `KeepRandomBeaconOperator`;
CREATE TABLE `KeepRandomBeaconOperator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txhash` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `blockNumber` int(11) NOT NULL,
  `address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `event` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `memberIndex` int(11) DEFAULT NULL,
  `groupPubKey` text COLLATE utf8_unicode_ci,
  `misbehaved` text COLLATE utf8_unicode_ci,
  `beneficiary` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `operator` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `groupIndex` int(11) DEFAULT NULL,
  `newEntry` int(11) DEFAULT NULL,
  `previousEntry` int(11) DEFAULT NULL,
  `groupPublicKey` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_blockNumber_groupIndex_operator_groupPubKey` (`event`,`blockNumber`,`groupIndex`,`operator`,`groupPubKey`(50)),
  KEY `operator` (`operator`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `KeepToken`;
CREATE TABLE `KeepToken` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txhash` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `blockNumber` int(11) NOT NULL,
  `address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `event` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime DEFAULT NULL,
  `format_date` date DEFAULT NULL,
  `from` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `to` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `value` double NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `from_to_txhash_value` (`from`,`to`,`txhash`,`value`),
  KEY `from` (`from`),
  KEY `to` (`to`),
  KEY `event` (`event`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `operatorEventsSubscribe`;
CREATE TABLE `operatorEventsSubscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `operator` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `events` json DEFAULT NULL,
  `collateralization` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `operator_address` (`operator`,`address`),
  KEY `address` (`address`),
  KEY `operaor` (`operator`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `operators`;
CREATE TABLE `operators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operator` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `updating` tinyint(4) DEFAULT NULL,
  `availBond` double DEFAULT NULL,
  `staked` int(11) DEFAULT NULL,
  `eth_rewards` double DEFAULT NULL,
  `tbtc_rewards` double DEFAULT NULL,
  `saked_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `operator` (`operator`),
  KEY `updating` (`updating`),
  KEY `saked_at` (`saked_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `operatorSubscribeQueue`;
CREATE TABLE `operatorSubscribeQueue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime` datetime DEFAULT NULL,
  `address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `operator` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `contractAddress` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `event` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `collateralization` int(11) DEFAULT NULL,
  `sended` tinyint(4) NOT NULL,
  `params` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `address_operator_contractAddress_event` (`address`,`operator`,`contractAddress`,`event`),
  KEY `sended` (`sended`),
  KEY `collateralization` (`collateralization`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `systemContract`;
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
  UNIQUE KEY `txhash_event__depositContractAddress` (`txhash`,`event`,`_depositContractAddress`),
  KEY `event` (`event`),
  KEY `transactionHash` (`txhash`),
  KEY `depositContractAddress` (`_depositContractAddress`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `TokenContract`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `TokenStaking`;
CREATE TABLE `TokenStaking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txhash` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `blockNumber` int(11) NOT NULL,
  `address` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `event` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `operator` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lockCreator` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `beneficiary` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authorizer` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` double DEFAULT NULL,
  `owner` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `until` datetime DEFAULT NULL,
  `newOwner` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `newAmount` double DEFAULT NULL,
  `topUp` double DEFAULT NULL,
  `undelegatedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `txhash_event_operator` (`txhash`,`event`,`operator`),
  KEY `operator` (`operator`),
  KEY `event` (`event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
