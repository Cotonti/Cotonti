<?php
/**
 * Static and dynamic resource (e.g. HTML) strings. Can be overriden by skin files and other code.
 *
 * @package comments
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2009-2012
 * @license BSD
 */

$R['comments_code_admin'] = $L['Ip'].': {$ipsearch}<span class="spaced">'.$cfg['separator'].'</span><a href="{$delete_url}" class="confirmLink">'.$L['Delete'].'</a><span class="spaced">'.$cfg['separator'].'</span>';
$R['comments_code_edit'] = '<a href="{$edit_url}">'.$L['Edit'].'</a> {$allowed_time}';
$R['comments_code_pages_info'] = $L['Total'].': {$totalitems}, '.$L['comm_on_page'].': {$onpage}';
$R['comments_link'] = '<a href="{$url}" class="comments_link" alt="'.$L['Comments'].'">'.$R['icon_comments'].' ({$count})</a>';

$R['icon_comments'] = 
	'<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/comments.png" alt="'.$L['Comments'].'" />';
$R['icon_comments_cnt'] = 
	'<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/comments.png" alt="'.$L['Comments'].'" /> ({$cnt})';

?>
