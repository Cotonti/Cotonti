/**
 * Completely removes page data
 */

DROP TABLE IF EXISTS `sed_pages`;

DELETE FROM `sed_structure` WHERE structure_code IN ('articles', 'links', 'events', 'news');