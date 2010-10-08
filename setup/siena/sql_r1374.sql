/* r1374 Remove trashcan options  and trashcan table*/
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` = 'trash';

DROP TABLE IF EXISTS `cot_trash`;