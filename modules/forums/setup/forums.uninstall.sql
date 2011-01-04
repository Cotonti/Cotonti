/**
 * Completely removes forums data and tables
 */

DROP TABLE IF EXISTS `cot_forum_posts`;
DROP TABLE IF EXISTS `cot_forum_topics`;
DROP TABLE IF EXISTS `cot_forum_stats`;

DELETE FROM `cot_auth` WHERE `auth_code` = 'forums';
DELETE FROM `cot_structure` WHERE `structure_area` = 'forums';