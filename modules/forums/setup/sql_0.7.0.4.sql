/* 0.7.0.4 (r1359) Forums icon fix */
UPDATE `cot_forum_sections` SET `fs_icon` = 'images/icons/default/forums.png'
  WHERE `fs_icon` = 'system/admin/tpl/img/forums.png';