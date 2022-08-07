<?php
/**
 * Static and dynamic resource (e.g. HTML) strings. Can be overriden by skin files and other code.
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

$R['comments_code_admin'] = cot::$L['Ip'].': {$ipsearch}<span class="spaced">' . cot::$cfg['separator'] .
    '</span><a href="{$delete_url}" class="confirmLink">' . cot::$L['Delete'].'</a><span class="spaced">' .
    cot::$cfg['separator'].'</span>';
$R['comments_code_edit'] = '<a href="{$edit_url}">' . cot::$L['Edit'].'</a> {$allowed_time}';
$R['comments_code_pages_info'] = cot::$L['Total'].': {$totalitems}, '. cot::$L['comm_on_page'].': {$onpage}';
$R['comments_link'] = '<a href="{$url}" class="comments_link" alt="' . cot::$L['Comments'].'">' .
    cot::$R['icon_comments'].' ({$count})</a>';

$R['icon_comments'] =
	'<img class="icon" src="images/icons/' . cot::$cfg['defaulticons'].'/comments.png" alt="' . cot::$L['Comments'] .
    '" />';
$R['icon_comments_cnt'] =
	'<img class="icon" src="images/icons/' . cot::$cfg['defaulticons'].'/comments.png" alt="' . cot::$L['Comments'] .
    '" /> ({$cnt})';
