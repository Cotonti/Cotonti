/* r293 Increase size of user_name and page category title (begins in r285)*/ 
ALTER TABLE `sed_com` CHANGE `com_author` `com_author` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ; 
ALTER TABLE `sed_forum_posts` CHANGE `fp_postername` `fp_postername` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;  
ALTER TABLE `sed_forum_posts` CHANGE `fp_updater` `fp_updater` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL  ;
ALTER TABLE `sed_forum_sections` CHANGE `fs_lt_postername` `fs_lt_postername` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;  
ALTER TABLE `sed_forum_topics` CHANGE `ft_lastpostername` `ft_lastpostername` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ; 
ALTER TABLE `sed_forum_topics` CHANGE `ft_firstpostername` `ft_firstpostername` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;  
ALTER TABLE `sed_logger` CHANGE `log_name` `log_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL  ;
ALTER TABLE `sed_online` CHANGE `online_name` `online_name` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;  
ALTER TABLE `sed_pages` CHANGE `page_author` `page_author` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL  ;
ALTER TABLE `sed_pm` CHANGE `pm_fromuser` `pm_fromuser` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL  ;
ALTER TABLE `sed_structure` CHANGE `structure_title` `structure_title` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;   