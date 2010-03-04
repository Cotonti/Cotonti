/* r1164  Update settings from search plugin */
DELETE FROM sed_config WHERE config_owner = 'plug' AND config_cat = 'search' AND config_name = 'maxitems_ext';
DELETE FROM sed_config WHERE config_owner = 'plug' AND config_cat = 'search' AND config_name = 'showtext_ext';
DELETE FROM sed_config WHERE config_owner = 'plug' AND config_cat = 'search' AND config_name = 'showtext';

INSERT INTO `sed_config` VALUES ('plug', 'search', '5', 'pagesearch', 3, '1', '1', '', 'Enable pages search');
INSERT INTO `sed_config` VALUES ('plug', 'search', '6', 'forumsearch', 3, '1', '1', '', 'Enable forums search');
