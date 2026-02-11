-- Update Script for ClubOrganisation Component v1.1.0
-- Author: Christian Schulz
-- Email: technik@meinetechnikwelt.rocks

-- Add comment field to memberships table
-- Note: If this fails with "Duplicate column" error, the column already exists
-- and you can safely ignore the error or follow the manual installation guide.

ALTER TABLE `#__cluborganisation_memberships` 
ADD COLUMN `comment` TEXT AFTER `end`;
