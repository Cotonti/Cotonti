/* r1134 Modify icon paths to match new structure */

UPDATE `sed_forum_sections` SET `fs_icon` = 'system/admin/tpl/img/forums.png' WHERE `fs_icon` = 'images/admin/forums.gif';
