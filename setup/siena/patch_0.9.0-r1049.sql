/* r1049 Countries update fix */
UPDATE `cot_users` SET `user_country` = 'tl' WHERE `user_country` = 'tp';
UPDATE `cot_users` SET `user_country` = 'gb' WHERE `user_country` IN ('en', 'sx', 'uk', 'wa');
UPDATE `cot_users` SET `user_country` = '00' WHERE `user_country` IN ('eu', 'yi');
UPDATE `cot_users` SET `user_country` = 'rs' WHERE `user_country` = 'kv';
UPDATE `cot_users` SET `user_country` = 'cd' WHERE `user_country` = 'zr';