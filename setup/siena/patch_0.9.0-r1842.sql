/* r1842 login session expiration */
ALTER TABLE `cot_users` MODIFY `user_sid` char(64) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_users` ADD COLUMN `user_sidtime` int NOT NULL default 0;