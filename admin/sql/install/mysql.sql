-- SQL Installation Script for ClubOrganisation Component
-- Author: Christian Schulz
-- Email: technik@meinetechnikwelt.rocks

-- Tabelle für Anreden (Salutations)
CREATE TABLE IF NOT EXISTS `#__cluborganisation_salutations` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(100) NOT NULL,
    `published` TINYINT(1) NOT NULL DEFAULT 1,
    `ordering` INT(11) NOT NULL DEFAULT 0,
    `checked_out` INT(11) UNSIGNED,
    `checked_out_time` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Tabelle für Mitgliedschaftstypen (Membership Types)
CREATE TABLE IF NOT EXISTS `#__cluborganisation_membershiptypes` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(100) NOT NULL,
    `published` TINYINT(1) NOT NULL DEFAULT 1,
    `ordering` INT(11) NOT NULL DEFAULT 0,
    `checked_out` INT(11) UNSIGNED,
    `checked_out_time` DATETIME,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Tabelle für Personen
CREATE TABLE IF NOT EXISTS `#__cluborganisation_persons` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `salutation` INT(11) UNSIGNED NOT NULL,
    `firstname` VARCHAR(100) NOT NULL,
    `middlename` VARCHAR(100),
    `lastname` VARCHAR(100) NOT NULL,
    `birthname` VARCHAR(100),
    `address` VARCHAR(255) NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `zip` VARCHAR(20) NOT NULL,
    `country` VARCHAR(100) NOT NULL,
    `telephone` VARCHAR(50),
    `mobile` VARCHAR(50) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `birthday` DATE NOT NULL,
    `deceased` DATE,
    `member_no` VARCHAR(50) NOT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `image` VARCHAR(255),
    `user_id` INT(11) UNSIGNED,
    `checked_out` INT(11) UNSIGNED,
    `checked_out_time` DATETIME,
    `created` DATETIME NOT NULL,
    `created_by` INT(11) UNSIGNED NOT NULL,
    `modified` DATETIME,
    `modified_by` INT(11) UNSIGNED,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_member_no` (`member_no`),
    KEY `idx_salutation` (`salutation`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_active` (`active`),
    KEY `idx_lastname` (`lastname`),
    KEY `idx_firstname` (`firstname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Tabelle für Mitgliedschaften
CREATE TABLE IF NOT EXISTS `#__cluborganisation_memberships` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `person_id` INT(11) UNSIGNED NOT NULL,
    `type` INT(11) UNSIGNED NOT NULL,
    `begin` DATE NOT NULL,
    `end` DATE DEFAULT NULL,
    `catid` INT(11) UNSIGNED,
    `checked_out` INT(11) UNSIGNED,
    `checked_out_time` DATETIME,
    `created` DATETIME NOT NULL,
    `created_by` INT(11) UNSIGNED NOT NULL,
    `modified` DATETIME,
    `modified_by` INT(11) UNSIGNED,
    PRIMARY KEY (`id`),
    KEY `idx_person_id` (`person_id`),
    KEY `idx_type` (`type`),
    KEY `idx_begin` (`begin`),
    KEY `idx_end` (`end`),
    KEY `idx_catid` (`catid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Tabelle für Bankverbindungen
CREATE TABLE IF NOT EXISTS `#__cluborganisation_membershipbanks` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `membership_id` INT(11) UNSIGNED NOT NULL,
    `accountname` TEXT NOT NULL,
    `iban` TEXT NOT NULL,
    `bic` TEXT,
    `begin` DATE NOT NULL,
    `checked_out` INT(11) UNSIGNED,
    `checked_out_time` DATETIME,
    `created` DATETIME NOT NULL,
    `created_by` INT(11) UNSIGNED NOT NULL,
    `modified` DATETIME,
    `modified_by` INT(11) UNSIGNED,
    PRIMARY KEY (`id`),
    KEY `idx_membership_id` (`membership_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Standard-Anreden einfügen (nur wenn Tabelle leer ist)
INSERT INTO `#__cluborganisation_salutations` (`title`, `published`, `ordering`)
SELECT * FROM (SELECT 'Herr' as title, 1 as published, 1 as ordering) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM `#__cluborganisation_salutations` WHERE `title` = 'Herr'
) LIMIT 1;

INSERT INTO `#__cluborganisation_salutations` (`title`, `published`, `ordering`)
SELECT * FROM (SELECT 'Frau' as title, 1 as published, 2 as ordering) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM `#__cluborganisation_salutations` WHERE `title` = 'Frau'
) LIMIT 1;

INSERT INTO `#__cluborganisation_salutations` (`title`, `published`, `ordering`)
SELECT * FROM (SELECT 'Divers' as title, 1 as published, 3 as ordering) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM `#__cluborganisation_salutations` WHERE `title` = 'Divers'
) LIMIT 1;

-- Standard-Mitgliedschaftstypen einfügen (nur wenn Tabelle leer ist)
INSERT INTO `#__cluborganisation_membershiptypes` (`title`, `published`, `ordering`)
SELECT * FROM (SELECT 'Einzelmitglied' as title, 1 as published, 1 as ordering) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM `#__cluborganisation_membershiptypes` WHERE `title` = 'Einzelmitglied'
) LIMIT 1;

INSERT INTO `#__cluborganisation_membershiptypes` (`title`, `published`, `ordering`)
SELECT * FROM (SELECT 'Einzelmitglied (reduziert)' as title, 1 as published, 2 as ordering) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM `#__cluborganisation_membershiptypes` WHERE `title` = 'Einzelmitglied (reduziert)'
) LIMIT 1;

INSERT INTO `#__cluborganisation_membershiptypes` (`title`, `published`, `ordering`)
SELECT * FROM (SELECT 'Familienmitglied (zahlend)' as title, 1 as published, 3 as ordering) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM `#__cluborganisation_membershiptypes` WHERE `title` = 'Familienmitglied (zahlend)'
) LIMIT 1;

INSERT INTO `#__cluborganisation_membershiptypes` (`title`, `published`, `ordering`)
SELECT * FROM (SELECT 'Familienmitglied' as title, 1 as published, 4 as ordering) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM `#__cluborganisation_membershiptypes` WHERE `title` = 'Familienmitglied'
) LIMIT 1;
