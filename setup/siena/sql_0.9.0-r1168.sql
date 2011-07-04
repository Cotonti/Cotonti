/* r1168 Fix for comments plugin and cache cleanup */
DELETE FROM `cot_plugins` WHERE `pl_code` = 'comedit';

TRUNCATE `cot_cache`;