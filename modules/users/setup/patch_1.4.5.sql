/* https://github.com/Cotonti/Cotonti/issues/1505 */
/* Fix for SQL error (1292): Incorrect date value: '0000-00-00' for column 'user_birthdate' */
UPDATE `cot_users` SET `user_birthdate` = '0000-01-01' WHERE CAST(`user_birthdate` AS CHAR(11)) = '0000-00-00';
ALTER TABLE `cot_users` MODIFY `user_birthdate` DATE NULL DEFAULT NULL;
UPDATE `cot_users` SET `user_birthdate` = NULL WHERE `user_birthdate` = '0000-01-01';