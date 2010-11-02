/* r1486 forums section renamed to cat*/
ALTER TABLE `cot_forum_posts` CHANGE COLUMN `fp_sectionid` `fp_cat` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_forum_topics` CHANGE COLUMN `ft_sectionid` `ft_cat` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_forum_topics` CHANGE COLUMN `ft_movedto` `ft_movedto` varchar(255) collate utf8_unicode_ci NOT NULL default '';
ALTER TABLE `cot_forum_stats` CHANGE COLUMN `fs_code` `fs_cat` varchar(255) collate utf8_unicode_ci NOT NULL default '';

ALTER TABLE `cot_forum_stats` DROP `fs_topiccount_pruned`;
ALTER TABLE `cot_forum_stats` DROP `fs_postcount_pruned`;

ALTER TABLE `cot_forum_posts` ADD KEY (`fp_cat`);
ALTER TABLE `cot_forum_topics` ADD KEY (`ft_cat`);

DELETE FROM `cot_plugins` WHERE `pl_code` = 'forums' AND `pl_hook` = 'admin';
