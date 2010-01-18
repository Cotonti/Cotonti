<?PHP
/* ====================
[BEGIN_SED_EXTPLUGIN]
Code=comedit
Part=loop
File=comedit.loop
Hooks=comments.loop
Tags=comments.tpl:{COMMENTS_ROW_EDIT}
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * Comedit plug
 *
 * @package Cotonti
 * @version 0.6.6
 * @author Asmo (Edited by motor2hg), Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('SED_CODE') or die('Wrong URL');

$time_limit = ($sys['now_offset'] < ($row['com_date'] + $cfg['plugin']['comedit']['time'] * 60)) ? TRUE : FALSE;

$usr['isowner_com'] = $time_limit && ($usr['id'] > 0 && $row['com_authorid'] == $usr['id']
		|| $usr['id'] == 0 && $usr['ip'] == $row['com_authorip']);

$com_gup = $sys['now_offset'] - ($row['com_date'] + $cfg['plugin']['comedit']['time'] * 60);

$allowed_time = ($usr['isowner_com'] && !$usr['isadmin']) ?
	' - ' . sed_build_timegap($sys['now_offset'] + $com_gup, $sys['now_offset']) . $L['plu_comgup'] : '';

$com_edit = ($usr['isadmin_com'] || $usr['isowner_com']) ? '<a href="'.sed_url('plug', 'e=comedit&m=edit&amp;pid='
	. $code . '&amp;cid=' . $row['com_id']).'">'. $L['Edit'] . '</a>' . $allowed_time : '';

$t->assign('COMMENTS_ROW_EDIT', $com_edit);

?>