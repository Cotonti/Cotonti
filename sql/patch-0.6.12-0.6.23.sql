ALTER TABLE `sed_users` MODIFY `user_sid` char(64) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `sed_users` ADD `user_sidtime` int NOT NULL default 0;