-- Update-Script für ClubOrganisation 1.7.0
-- Fügt Tabelle für Mitgliedschaftsgebühren hinzu

-- Tabelle für Mitgliedschaftsgebühren
CREATE TABLE IF NOT EXISTS `#__cluborganisation_membershiptype_fees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `membershiptype_id` int(11) NOT NULL COMMENT 'Referenz auf Membership Type',
  `begin` date NOT NULL COMMENT 'Gültig ab diesem Datum',
  `amount` decimal(10,2) NOT NULL COMMENT 'Beitragshöhe',
  `published` tinyint(1) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_membershiptype` (`membershiptype_id`),
  KEY `idx_begin` (`begin`),
  KEY `idx_published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
