/* 0.9.12-01 Relocate some configuration settings. */
UPDATE `cot_config`
  SET `config_owner` = 'module', `config_order` = '09'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'users'
  AND `config_name` = 'usertextimg';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '01'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'security'
  AND `config_name` = 'cookiedomain';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '02'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'security'
  AND `config_name` = 'cookiepath';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '03'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'security'
  AND `config_name` = 'cookielifetime';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '04'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'users'
  AND `config_name` = 'forcerememberme';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '05'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'users'
  AND `config_name` = 'timedout';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '06'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'redirbkonlogin';

UPDATE `cot_config`
  SET `config_cat` = 'sessions', `config_order` = '07'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'redirbkonlogout';

UPDATE `cot_config`
  SET `config_cat` = 'performance', `config_order` = '05'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'jquery';

UPDATE `cot_config`
  SET `config_cat` = 'performance', `config_order` = '06'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'turnajax';

UPDATE `cot_config`
  SET `config_cat` = 'security', `config_order` = '97'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'devmode';

UPDATE `cot_config`
  SET `config_cat` = 'security', `config_order` = '98'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'maintenance';

UPDATE `cot_config`
  SET `config_cat` = 'security', `config_order` = '99'
WHERE `config_owner` = 'core'
  AND `config_cat` = 'main'
  AND `config_name` = 'maintenancereason';