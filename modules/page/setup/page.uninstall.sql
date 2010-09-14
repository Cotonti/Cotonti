/**
 * Completely removes page data
 */

DROP TABLE IF EXISTS `cot_pages`;

DELETE FROM `cot_structure` WHERE structure_code IN ('articles', 'links', 'events', 'news');