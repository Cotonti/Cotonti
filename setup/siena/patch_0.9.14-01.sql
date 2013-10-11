/* 0.9.14-01 update table schema for larger data sets, #981 */
ALTER TABLE `cot_cache` MODIFY `c_value` MEDIUMTEXT collate utf8_unicode_ci;
ALTER TABLE `cot_users` MODIFY `user_auth` MEDIUMTEXT collate utf8_unicode_ci;
