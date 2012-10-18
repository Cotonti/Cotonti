/* r111 Subforums Enhancment */
ALTER TABLE `sed_forum_sections` ADD COLUMN `fs_allowviewers` tinyint(1) NOT NULL default '1';
ALTER TABLE `sed_users` ADD COLUMN `user_theme` VARCHAR(16) NOT NULL DEFAULT '';