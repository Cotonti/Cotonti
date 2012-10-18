/* r1093 Some changes in PM for economy sql space */

ALTER TABLE `cot_pm` ADD COLUMN `pm_fromstate` tinyint(2) NOT NULL default '0';
ALTER TABLE `cot_pm` ADD COLUMN `pm_tostate` tinyint(2) NOT NULL default '0';

UPDATE `cot_pm` SET `pm_tostate`='1'  WHERE `pm_state` = '1';
UPDATE `cot_pm` SET `pm_tostate`='2'  WHERE `pm_state` = '2';

DELETE FROM `cot_pm` WHERE `pm_state` = '3';

ALTER TABLE `cot_pm` DROP `pm_state`;

