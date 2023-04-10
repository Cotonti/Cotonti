<?php
/**
 * Update to v. 1.1.6
 */
defined('COT_CODE') or die('Wrong URL');

Cot::$db->update(
    Cot::$db->structure,
    ['structure_icon' => 'images/icons/default/modules/forums/forum.png',],
    "structure_area='forums' AND structure_icon='images/icons/default/forums.png'"
);