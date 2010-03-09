/* r1168 Fix for comments plugin and cache cleanup */
DELETE FROM `sed_plugins` WHERE `pl_code` = 'comedit';

TRUNCATE `sed_cache`;