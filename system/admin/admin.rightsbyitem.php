<?php
/**
 * Administration panel - Rights by item editor
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('users', 'a');
$usr['isadmin'] &= cot_auth('admin', 'a', 'A');
cot_block($usr['isadmin']);

$t = new XTemplate(cot_tplfile('admin.rightsbyitem', 'core'));

$ic = cot_import('ic', 'G', 'ALP');
$io = cot_import('io', 'G', 'ALP');
$advanced = cot_import('advanced', 'G', 'BOL');

$L['adm_code']['admin'] = $L['Administration'];
$L['adm_code']['message'] = $L['Messages'];

/* === Hook === */
foreach (cot_getextplugins('admin.rightsbyitem.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($a == 'update')
{
	$mask = array();
	$auth = cot_import('auth', 'P', 'ARR');

	/* === Hook === */
	foreach (cot_getextplugins('admin.rightsbyitem.update') as $pl)
	{
		include $pl;
	}
	/* ===== */

	$db->update($db_auth, array('auth_rights' => 0), "auth_code='$ic' AND auth_option='$io'");

	foreach ($auth as $i => $j)
	{
		if (is_array($j))
		{
			$mask = 0;
			foreach ($j as $l => $m)
			{
				$mask += cot_auth_getvalue($l);
			}
			$i = (int) $i;
			$db->update($db_auth, array('auth_rights' => $mask),
				"auth_groupid=$i AND auth_code='$ic' AND auth_option='$io'");
		}
	}

	cot_auth_reorder();
	cot_auth_clear('all');

	cot_message('Updated');
}

$sql = $db->query("SELECT a.*, u.user_name, g.grp_name, g.grp_level FROM $db_auth as a
	LEFT JOIN $db_users AS u ON u.user_id=a.auth_setbyuserid
	LEFT JOIN $db_groups AS g ON g.grp_id=a.auth_groupid
	WHERE auth_code='$ic' AND auth_option='$io' AND grp_skiprights = 0 ORDER BY grp_level DESC, grp_id DESC");

cot_die($sql->rowCount() == 0);

if($ic == 'plug')
{
	$title = ' : '.$io;
}
elseif($io != 'a' && !empty($ic))
{
	$title = ' : '.$ic.' '.$structure[$ic][$io]['title']." (".$io.")";
}

/* === Hook for the plugins === */
foreach (cot_getextplugins('admin.rightsbyitem.case') as $pl)
{
	include $pl;
}
/* ===== */
if($ic == 'message' || $ic == 'admin')
{
	$adminpath[] = array(cot_url('admin'), $L['adm_code'][$ic]);
}
else
{
	$adminpath[] = array(cot_url('admin', 'm=extensions'), $L['Extensions']);
	if($ic == 'plug')
	{
		$adminpath[] = array(cot_url('admin', 'm=extensions&a=details&pl='.$io), $cot_plugins_enabled[$io]['title']);
	}
	else
	{
		$adminpath[] = array(cot_url('admin', 'm=extensions&a=details&mod='.$ic), $cot_modules[$ic]['title']);
		if($io != 'a')
		{
			$adminpath[] = array(cot_url('admin', 'm=structure&n='.$ic), $L['Structure']);
			$adminpath[] = array(cot_url('admin', 'm=structure&n='.$ic.'&al='.$io), $structure[$ic][$io]['title']);
		}
	}
}

//m=extensions&a=details&mod=page
$adminpath[] = array(cot_url('admin', 'm=rightsbyitem&ic='.$ic.'&io='.$io), $L['Rights']);
($advanced) && $adminpath[] = array(cot_url('admin', 'm=rightsbyitem&ic='.$ic.'&io='.$io.'&advanced=1'), $L['More']);


$adv_columns = ($advanced) ? 8 : 3;
$adv_columns = (!$advanced && $ic == 'page') ? 4 : $adv_columns;

$l_custom1 = ($ic == 'page') ? $L['Download'] : $L['Custom'].' #1';

while ($row = $sql->fetch())
{
	$link = cot_url('admin', 'm=rights&g='.$row['auth_groupid']);
	$title = htmlspecialchars($row['grp_name']);
	cot_rights_parseline($row, $title, $link);
}
$sql->closeCursor();

$is_adminwarnings = isset($adminwarnings);
$adv_for_url = ($advanced) ? '&advanced=1' : '';

$t->assign(array(
	'ADMIN_RIGHTSBYITEM_FORM_URL' => cot_url('admin', 'm=rightsbyitem&a=update&ic='.$ic.'&io='.$io.$adv_for_url),
	'ADMIN_RIGHTSBYITEM_ADVANCED_URL' => cot_url('admin', 'm=rightsbyitem&ic='.$ic.'&io='.$io.'&advanced=1'),
	'ADMIN_RIGHTSBYITEM_ADV_COLUMNS' => $adv_columns,
	'ADMIN_RIGHTSBYITEM_4ADV_COLUMNS' => 4 + $adv_columns
));

cot_display_messages($t);

/* === Hook === */
foreach (cot_getextplugins('admin.rightsbyitem.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$adminmain = $t->text('MAIN');

$t->parse('RIGHTSBYITEM_HELP');
$adminhelp = $t->text('RIGHTSBYITEM_HELP');

function cot_rights_parseline($row, $title, $link)
{
	global $L, $advanced, $t, $out, $ic;

	$mn['R'] = 1;
	$mn['W'] = 2;

	if ($advanced || $ic == 'page')
	{
		$mn['1'] = 4;
	}
	else
	{
		$rv['1'] = 4;
	}

	if ($advanced)
	{
		$mn['2'] = 8;
		$mn['3'] = 16;
		$mn['4'] = 32;
		$mn['5'] = 64;
	}
	else
	{
		$rv['2'] = 8;
		$rv['3'] = 16;
		$rv['4'] = 32;
		$rv['5'] = 64;
	}
	$mn['A'] = 128;

	foreach ($mn as $code => $value)
	{
		$state[$code] = (($row['auth_rights'] & $value) == $value) ? TRUE : FALSE;
		$locked[$code] = (($row['auth_rights_lock'] & $value) == $value) ? TRUE : FALSE;
		$out['tpl_rights_parseline_locked'] = $locked[$code];
		$out['tpl_rights_parseline_state'] = $state[$code];

		$t->assign(array(
			'ADMIN_RIGHTSBYITEM_ROW_ITEMS_NAME' => 'auth['.$row['auth_groupid'].']['.$code.']',
			'ADMIN_RIGHTSBYITEM_ROW_ITEMS_CHECKED' => ($state[$code]) ? " checked=\"checked\"" : '',
			'ADMIN_RIGHTSBYITEM_ROW_ITEMS_DISABLED' => ($locked[$code]) ? " disabled=\"disabled\"" : ''
		));
		$t->parse('MAIN.RIGHTSBYITEM_ROW.ROW_ITEMS');
	}

	if (!$advanced)
	{
		$preserve = '';
		foreach ($rv as $code => $value)
		{
			if (($row['auth_rights'] & $value) == $value)
			{
				$preserve .= '<input type="hidden" name="auth['.$row['auth_groupid'].']['.$code.']" value="1" />';
			}
		}
		$t->assign('ADMIN_RIGHTSBYITEM_ROW_PRESERVE', $preserve);
	}

	$t->assign(array(
		'ADMIN_RIGHTSBYITEM_ROW_TITLE' => $title,
		'ADMIN_RIGHTSBYITEM_ROW_LINK' => $link,
		'ADMIN_RIGHTSBYITEM_ROW_USER' => cot_build_user($row['auth_setbyuserid'], htmlspecialchars($row['user_name'])),
		'ADMIN_RIGHTSBYITEM_ROW_JUMPTO' => cot_url('users', 'g='.$row['auth_groupid']),
	));
	$t->parse('MAIN.RIGHTSBYITEM_ROW');
}

?>