/* https://github.com/Cotonti/Cotonti/issues/1505 */
UPDATE `cot_users` SET `user_birthdate`=NULL  WHERE `user_birthdate`='0000-00-00';
ALTER TABLE `cot_users` CHANGE `user_birthdate` `user_birthdate` DATE NULL DEFAULT NULL;