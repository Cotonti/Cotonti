INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('plug', 'hiddengroups', '01', 'mode', 2, 'Group + Users (maingroup)', 'Group only', 'Group only,Group + Users (maingroup),Group + Users (subgroup)', 'Hiding mode');

UPDATE `cot_updates` SET `upd_value` = '0.9.2' WHERE `upd_param` = 'hiddengroups.ver';