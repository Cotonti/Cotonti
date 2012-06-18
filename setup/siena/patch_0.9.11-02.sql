/* 0.9.11-02 Introduce singular title field for groups */
ALTER TABLE `cot_groups` ADD `grp_name` VARCHAR(64) NOT NULL DEFAULT '';
UPDATE `cot_groups` SET `grp_name` = `grp_title`;
UPDATE `cot_groups` SET `grp_title` = 'Guest' WHERE `grp_title` = 'Guests';
UPDATE `cot_groups` SET `grp_title` = 'Member' WHERE `grp_title` = 'Members';
UPDATE `cot_groups` SET `grp_title` = 'Administrator' WHERE `grp_title` = 'Administrators';
UPDATE `cot_groups` SET `grp_title` = 'Moderator' WHERE `grp_title` = 'Moderators';