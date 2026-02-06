CREATE TABLE IF NOT EXISTS `#__cluborganisation_salutations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `published` TINYINT NOT NULL DEFAULT 1,
    `ordering` INT NOT NULL DEFAULT 0,
    `createdby` INT UNSIGNED NOT NULL DEFAULT 0,
    `createddate` DATETIME NOT NULL,
    `modifiedby` INT UNSIGNED NOT NULL DEFAULT 0,
    `modifieddate` DATETIME NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cluborganisation_membership_types` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `published` TINYINT NOT NULL DEFAULT 1,
    `ordering` INT NOT NULL DEFAULT 0,
    `createdby` INT UNSIGNED NOT NULL DEFAULT 0,
    `createddate` DATETIME NOT NULL,
    `modifiedby` INT UNSIGNED NOT NULL DEFAULT 0,
    `modifieddate` DATETIME NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cluborganisation_persons` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `salutation_id` INT UNSIGNED NOT NULL,
    `firstname` VARCHAR(255) NOT NULL,
    `middlename` VARCHAR(255) NULL,
    `lastname` VARCHAR(255) NOT NULL,
    `birthname` VARCHAR(255) NULL,
    `address` VARCHAR(255) NOT NULL,
    `city` VARCHAR(255) NOT NULL,
    `zip` VARCHAR(20) NOT NULL,
    `country` VARCHAR(255) NOT NULL,
    `telephone` VARCHAR(50) NULL,
    `mobile` VARCHAR(50) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `birthday` DATE NOT NULL,
    `deceased` DATE NULL,
    `member_no` VARCHAR(50) NOT NULL,
    `active` TINYINT NOT NULL DEFAULT 1,
    `image` VARCHAR(1024) NULL,
    `user_id` INT UNSIGNED NULL,
    `createdby` INT UNSIGNED NOT NULL DEFAULT 0,
    `createddate` DATETIME NOT NULL,
    `modifiedby` INT UNSIGNED NOT NULL DEFAULT 0,
    `modifieddate` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_member_no` (`member_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cluborganisation_memberships` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `person_id` INT UNSIGNED NOT NULL,
    `type_id` INT UNSIGNED NOT NULL,
    `begin` DATE NOT NULL,
    `end` DATE NULL,
    `catid` INT UNSIGNED NULL,
    `createdby` INT UNSIGNED NOT NULL DEFAULT 0,
    `createddate` DATETIME NOT NULL,
    `modifiedby` INT UNSIGNED NOT NULL DEFAULT 0,
    `modifieddate` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_person_id` (`person_id`),
    KEY `idx_type_id` (`type_id`),
    KEY `idx_begin_end` (`begin`, `end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__cluborganisation_membership_banks` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `membership_id` INT UNSIGNED NOT NULL,
    `accountname` VARBINARY(512) NOT NULL,
    `iban` VARBINARY(512) NOT NULL,
    `bic` VARBINARY(512) NULL,
    `begin` DATE NULL,
    `createdby` INT UNSIGNED NOT NULL DEFAULT 0,
    `createddate` DATETIME NOT NULL,
    `modifiedby` INT UNSIGNED NOT NULL DEFAULT 0,
    `modifieddate` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_membership_id` (`membership_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
