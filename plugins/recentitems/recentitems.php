<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

/**
 * Recent pages, topics in forums, users, comments
 *
 * @package recentitems
 * @version 0.7.0
 * @author esclkm, Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die("Wrong URL.");

$days = cot_import('days', 'G', 'INT');
$d = cot_import('d', 'G', 'INT');
if (empty($d)) $d = '0';
$mode = cot_import('mode', 'G', 'TXT');

if ($days == 0)
{
	if ($usr['id'] > 0)
	{
		$timeback = $usr['lastvisit'];
	}
	else
	{
		$days = 1;
	}
}
if ($days > 0)
{
	$timeminus = $days * 86400;
	$timeback = $sys['now_offset'] - $timeminus;
}

cot_require('users');
cot_require('recentitems', true);
$totalrecent[] = 0;
if ($cfg['plugin']['recentitems']['newpages'] && $cfg['module']['page'] && (empty($mode) || $mode == 'pages'))
{
	cot_require('page');
	$res = cot_build_recentpages('recentitems.pages', $timeback, $cfg['plugin']['recentitems']['itemsperpage'], $d, $pagetitlelimit, $cfg['plugin']['recentitems']['newpagestext'], $cfg['plugin']['recentitems']['rightscan']);
	$t->assign("RECENT_PAGES", $res);
}

if ($cfg['plugin']['recentitems']['newforums'] && $cfg['module']['forums'] && (empty($mode) || $mode == 'forums'))
{
	cot_require('forums');
	$res = cot_build_recentforums('recentitems.forums', $timeback, $cfg['plugin']['recentitems']['itemsperpage'], $d, $forumtitlelimit, $cfg['plugin']['recentitems']['rightscan']);
	$t->assign("RECENT_FORUMS", $res);
}

if ($cfg['plugin']['recentitems']['newadditional'] && ($mode  != 'pages' || $mode != 'forums'))
{
	/* === Hook === */
	foreach (cot_getextplugins('recentitems.tags') as $pl)
	{
		include $pl;
	}
	/* ===== */
}

$totalpages = max($totalrecent);
$days =($days > 0) ? "&days=".$days : "";
$mode=(!empty($mode)) ? "&mode=".$mode : "";
$pagenav = cot_pagenav('plug', 'e=recentitems'.$days.$mode, $d, $totalpages, $cfg['plugin']['recentitems']['itemsperpage']);

$t->assign(array(
	"PAGE_PAGENAV" => $pagenav['main'],
	"PAGE_PAGEPREV" => $pagenav['prev'],
	"PAGE_PAGENEXT" => $pagenav['next']
));

?>