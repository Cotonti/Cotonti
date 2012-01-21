<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=header.main
Tags=header.tpl:{HEADER_USER_PMS},{HEADER_USER_PMREMINDER}
[END_COT_EXT]
==================== */

/**
 * PM header notices
 *
 * @package pm
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

if ($usr['id'] > 0)
{
	$out['pms'] = cot_rc_link(cot_url('pm'), $L['Private_Messages']);

	require_once cot_incfile('pm', 'module');
	if ($usr['newpm'])
	{
		$usr['messages'] = $db->query("SELECT COUNT(*) FROM $db_pm WHERE pm_touserid='".$usr['id']."' AND pm_tostate=0")->fetchColumn();
	}
	$out['pmreminder'] = cot_rc_link(cot_url('pm'),
		($usr['messages'] > 0) ? cot_declension($usr['messages'], $Ls['Privatemessages']) : $L['hea_noprivatemessages']
	);
	
	$t->assign(array(
		'HEADER_USER_PM_URL' => cot_url('pm'),
		'HEADER_USER_PMS' => $out['pms'],
		'HEADER_USER_PMREMINDER' => $out['pmreminder']
	));
}

if ($cfg['pm']['css'] && $env['ext'] == 'pm')
{
	cot_rc_link_file($cfg['modules_dir'] . '/pm/tpl/pm.css');
}
?>
