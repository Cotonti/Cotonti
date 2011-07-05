/* r1681 Remove options altered from config.php */
DELETE FROM `cot_config` WHERE `config_owner` = 'core' AND `config_cat` = 'performance'
  AND `config_name` IN ('cache_index', 'cache_page', 'cache_forums');