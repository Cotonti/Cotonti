/* r1049 Countries update fix */
UPDATE `sed_users` SET `user_country` = 'tl' WHERE `user_country` = 'tp';
UPDATE `sed_users` SET `user_country` = 'gb' WHERE `user_country` IN ('en', 'sx', 'uk', 'wa');
UPDATE `sed_users` SET `user_country` = '00' WHERE `user_country` IN ('eu', 'yi');
UPDATE `sed_users` SET `user_country` = 'rs' WHERE `user_country` = 'kv';
UPDATE `sed_users` SET `user_country` = 'cd' WHERE `user_country` = 'zr';