/* 0.9.8-01 timezone bug fix */
ALTER TABLE `cot_users` MODIFY `user_timezone` decimal(3,1) NOT NULL default '0';