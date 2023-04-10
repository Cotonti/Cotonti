<?php
/* Private Message Icons */

if (!isset(Cot::$L['pm_sendnew'])) {
    global $L;
    include cot_langfile('pm', 'module');
}

$R['pm_icon'] = '<img class="icon" src="images/icons/'.Cot::$cfg['defaulticons'].'/modules/pm/pm.png"  alt="'.Cot::$L['pm_sendnew'].'" />';
$R['pm_icon_new'] = '<img class="icon" src="images/icons/'.Cot::$cfg['defaulticons'].'/modules/pm/pm-new.png" alt="'.Cot::$L['pm_unread'].'" />';
$R['pm_icon_trashcan'] = '<img class="icon" src="images/icons/'.Cot::$cfg['defaulticons'].'/modules/pm/pm-delete.png" alt="'.Cot::$L['Delete'].'" />';
$R['pm_icon_edit'] = '<img class="icon" src="images/icons/'.Cot::$cfg['defaulticons'].'/modules/pm/pm-edit.png" alt="'.Cot::$L['Edit'].'" />';

$R['pm_icon_star'] = '<div class="pm-star"><a href="{$link}" title="'.Cot::$L['pm_putinstarred'].'">'.'<img class="icon" src="images/icons/'.Cot::$cfg['defaulticons'].'/24/error.png" /></a></div>';
$R['pm_icon_unstar'] = '<div class="pm-star pm-star-on"><a href="{$link}" title="'.Cot::$L['pm_deletefromstarred'].'">'.'<img class="icon" src="images/icons/'.Cot::$cfg['defaulticons'].'/24/error.png" /></a></div>';

$R['pm_link'] = '<a href="{$url}">'.$R['pm_icon'].'</a>';
