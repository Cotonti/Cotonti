/* 0.9.14-01 update table schema for larger data sets, #981 */
ALTER TABLE `cot_cache` MODIFY `c_value` MEDIUMTEXT collate utf8_unicode_ci;
ALTER TABLE `cot_users` MODIFY `user_auth` MEDIUMTEXT collate utf8_unicode_ci;

ALTER TABLE `cot_users` ADD INDEX `user_name` (`user_name`);
ALTER TABLE `cot_users` ADD INDEX `user_maingrp` (`user_maingrp`);
ALTER TABLE `cot_users` ADD INDEX `user_email` (`user_email`);
ALTER TABLE `cot_users` ADD INDEX `user_sid` (`user_sid`);
ALTER TABLE `cot_users` ADD INDEX `user_lostpass` (`user_lostpass`);