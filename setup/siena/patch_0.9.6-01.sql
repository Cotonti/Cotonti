/* 0.9.6-01 issue #426 rightless groups */
ALTER TABLE `cot_groups` ADD COLUMN `grp_skiprights` tinyint NOT NULL default '0';