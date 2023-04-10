<?php
/**
 * Static and dynamic resource (e.g. HTML) strings. Can be overriden by skin files and other code.
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

$R['comments_code_admin'] = Cot::$L['Ip'] . ': {$ipsearch}<span class="spaced">' . Cot::$cfg['separator'] .
    '</span><a href="{$delete_url}" class="confirmLink">' . Cot::$L['Delete'] . '</a><span class="spaced">' .
    Cot::$cfg['separator'].'</span>';
$R['comments_code_edit'] = '<a href="{$edit_url}">' . Cot::$L['Edit'] . '</a> {$allowed_time}';
$R['comments_code_pages_info'] = Cot::$L['Total'] . ': {$totalitems}, ' . Cot::$L['comm_on_page'] . ': {$onpage}';
$R['comments_link'] = '<a href="{$url}" class="comments_link" title="' . Cot::$L['comments_comments'] . '">' .
    Cot::$R['icon_comments'] . ' ({$count})</a>';

$R['icon_comments'] = '<img class="icon" src="images/icons/' . Cot::$cfg['defaulticons'] . '/24/comments.png" alt="'
    . Cot::$L['comments_comments'] . '" />';
$R['icon_comments_cnt'] = '<img class="icon" src="images/icons/' . Cot::$cfg['defaulticons'] . '/24/comments.png" alt="'
    . Cot::$L['comments_comments'] . '" /> ({$cnt})';
