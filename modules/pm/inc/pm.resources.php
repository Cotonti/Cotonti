<?php

/* Private Message Icons */

$R['pm_icon'] = 
	'<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/pm.png"  alt="'.$L['pm_sendnew'].'" />';
$R['pm_icon_new'] = 
	'<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/pm-new.png" alt="'.$L['pm_unread'].'" />';
$R['pm_icon_trashcan'] = 
	'<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/pm-delete.png" alt="'.$L['Delete'].'" />';
$R['pm_icon_edit'] = 
	'<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/pm-edit.png" alt="'.$L['Edit'].'" />';

$R['pm_icon_star'] = '<div class="pm-star"><a href="{$link}" title="'.$L['pm_putinstarred'].'"><img class="icon" src="images/icons/'.$cfg['defaulticons'].'/error.png" /></a></div>';
$R['pm_icon_unstar'] = '<div class="pm-star pm-star-on"><a href="{$link}" title="'.$L['pm_deletefromstarred'].'"><img class="icon" src="images/icons/'.$cfg['defaulticons'].'/error.png" /></a></div>';

$R['pm_link'] = '<a href="{$url}">'.$R['pm_icon'].'</a>';

?>