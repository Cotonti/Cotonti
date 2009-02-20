/* Incorrect DB scheme fixes */
ALTER TABLE `sed_forum_posts` MODIFY `fp_updater` varchar(100) collate utf8_unicode_ci NOT NULL DEFAULT '';
ALTER TABLE `sed_users` MODIFY `user_timezone` decimal(2,1) NOT NULL default '0';