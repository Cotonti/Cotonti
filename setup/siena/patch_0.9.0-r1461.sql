/* r1461 structure change */
ALTER TABLE `cot_structure` CHANGE COLUMN `structure_pagecount` `structure_count` mediumint NOT NULL default '0';

UPDATE `cot_config` SET `config_cat` = 'main', `config_order`= '99' WHERE `config_owner`= 'core' AND `config_cat`= 'structure' AND `config_name`= 'maxrowsperpage' LIMIT 1 ;
UPDATE `cot_config` SET `config_owner`= 'module', `config_cat` = 'page' WHERE `config_owner`= 'core' AND `config_cat`= 'structure' AND `config_name`= 'maxlistsperpage' LIMIT 1 ;

INSERT INTO `cot_config` (`config_owner`, `config_cat`, `config_order`, `config_name`, `config_type`, `config_value`, `config_default`, `config_variants`, `config_text`) VALUES
('module','page','05','maxrowsperpage',2,'15','15','5,10,15,20,25,30,40,50,60,70,100,200,500','');
