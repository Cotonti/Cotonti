/* Moving users from core to module */
UPDATE `cot_core` SET `ct_lock` = 0 WHERE `ct_code` = 'users';

UPDATE `cot_config` SET `config_owner` = 'module' WHERE `config_cat` = 'users' AND `config_name` NOT IN('disablewhosonline', 'usertextimg', 'forcerememberme', 'timedout');

INSERT INTO `cot_plugins` (pl_hook, pl_code, pl_part, pl_title, pl_file, pl_module) VALUES ('module', 'users', 'main', 'Users', 'users/users.php', 1);