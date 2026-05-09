-- Update-Script für ClubOrganisation 2.3.0
-- Fügt abhängige Mitgliedschaftstypen hinzu

ALTER TABLE `#__cluborganisation_membershiptypes`
    ADD COLUMN `is_dependent` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Abhängiger Typ (zahlt nicht selbst)' AFTER `ordering`,
    ADD COLUMN `depends_on_type` INT(11) UNSIGNED DEFAULT NULL COMMENT 'ID des übergeordneten Mitgliedschaftstyps' AFTER `is_dependent`;

ALTER TABLE `#__cluborganisation_memberships`
    ADD COLUMN `depends_on_membership_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'ID der übergeordneten Mitgliedschaft (bei abhängigen Typen)' AFTER `catid`,
    ADD KEY `idx_depends_on_membership` (`depends_on_membership_id`);
