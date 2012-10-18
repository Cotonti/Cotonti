/* r285 24 symbols for user name - not enough */ 
ALTER TABLE `sed_users` CHANGE `user_name` `user_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;