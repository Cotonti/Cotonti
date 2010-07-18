/* r1237 Authentication/security improvement */
ALTER TABLE `sed_users` CHANGE COLUMN `user_hashsalt` `user_token` char(16) collate utf8_unicode_ci NOT NULL default '';