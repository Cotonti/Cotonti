/* Update to v 1.1.8 in release 0.9.23 */
ALTER TABLE `cot_forum_posts` MODIFY `fp_posterip` varchar(64) NOT NULL DEFAULT '';

UPDATE `cot_forum_topics` SET `ft_movedto` = '0' WHERE `ft_movedto` IS NULL OR `ft_movedto` = '';
ALTER TABLE `cot_forum_topics` MODIFY `ft_movedto` mediumint UNSIGNED NOT NULL default 0;