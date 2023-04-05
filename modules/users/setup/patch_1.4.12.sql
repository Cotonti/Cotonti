/* Update to v 1.4.12 in release 0.9.23 */
UPDATE `cot_users` SET `user_timezone` = 'UTC' WHERE `user_timezone` = 'GMT' OR `user_timezone` IS NULL OR `user_timezone` = '0';
UPDATE `cot_users` SET `user_timezone` = 'Europe/Kiev' WHERE `user_timezone` = 'Europe/Kyiv';

ALTER TABLE `cot_users` MODIFY `user_timezone` VARCHAR(32) NOT NULL default 'UTC';